<?php
/**
 * WP-Background Processing
 *
 * @package WP-Background-Processing
 */

if ( ! class_exists( 'WP_Async_Request' ) ) {
    require_once BETTERDOCS_PRO_ROOT_DIR_PATH . 'includes/wp-background-processing/wp-async-request.php';
}

if ( ! class_exists( 'WP_Background_Process' ) ) {
    require_once BETTERDOCS_PRO_ROOT_DIR_PATH . 'includes/wp-background-processing/wp-background-process.php';
}

class BetterDocs_Migration_Process extends WP_Background_Process {

    /**
     * @var string
     */
    protected $action = 'betterdocs_migration_analytics';

    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task( $item ) {
        global $wpdb;

        if( empty($item) ) {
            return false;
        }

        if ( $item['page_now']  == $item['total_page'] ) {
            update_option( "betterdocs_analytics_migration", '1.0' );
        }

        self::feedback_migration($item['offset'], $item['per_page']);
        
        return false;
    }

    public function feedback_migration($offset, $per_page) {
        global $wpdb;
        $paging = "LIMIT ${offset}, ${per_page}";
        $meta_impression = $wpdb->get_results(
            "SELECT post_id, meta_value
            FROM {$wpdb->prefix}postmeta
            WHERE meta_key = '_betterdocs_meta_impression_per_day'
            {$paging}"
        );

        if ($meta_impression == null) return;

        foreach ($meta_impression as $meta_key=>$meta) {
            $post_id = $meta->post_id;
            $check_migration = get_post_meta( $post_id, '_betterdocs_migration', true );

            if ($check_migration == false) {
                $meta_value = unserialize($meta->meta_value);
                foreach ($meta_value as $key=>$value) {
                    if (array_key_exists('impressions', $value)) {
                        $sad = (array_key_exists('sad', $value)) ? $value['sad'] : 0;
                        $normal = (array_key_exists('normal', $value)) ? $value['normal'] : 0;
                        $happy = (array_key_exists('happy', $value)) ? $value['happy'] : 0;

                        $result = $wpdb->get_results(
                            $wpdb->prepare(
                                "SELECT * FROM {$wpdb->prefix}betterdocs_analytics WHERE post_id = %d AND created_at = %s",
                                array(
                                    $post_id,
                                    date("Y-m-d", strtotime($key))
                                )
                            )
                        );

                        if (!empty($result)) {
                            $impressions = (int) $result[0]->impressions + $value['impressions'];
                            $happy = (int) $result[0]->happy + $happy;
                            $sad = (int) $result[0]->sad + $sad;
                            $normal = (int) $result[0]->normal + $normal;
                            $wpdb->query(
                                $wpdb->prepare(
                                    "UPDATE {$wpdb->prefix}betterdocs_analytics 
                                    SET impressions = ". $impressions .", unique_visit = ". $impressions .", happy = ". $happy .", sad = ". $sad .", normal = ". $normal ."
                                    WHERE created_at = %s AND post_id = %d",
                                    array(
                                        $result[0]->created_at,
                                        $result[0]->post_id
                                    )
                                )
                            );
                        } else {
                            $wpdb->query(
                                $wpdb->prepare(
                                    "INSERT INTO {$wpdb->prefix}betterdocs_analytics
                                    ( post_id, impressions, unique_visit, happy, sad, normal, created_at )
                                    VALUES ( %d, %d, %d, %d, %d, %d, %s )",
                                    array(
                                        $post_id,
                                        $value['impressions'],
                                        $value['impressions'],
                                        $happy,
                                        $sad,
                                        $normal,
                                        date("Y-m-d", strtotime($key))
                                    )
                                )
                            );
                        }
                    }
                }
                update_post_meta( $post_id, '_betterdocs_migration', 1 );
            }
        }
    }

    /**
     * Complete
     *
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
    protected function complete() {
        parent::complete();
        
        // Show notice to user or perform some other arbitrary task...
    }

}

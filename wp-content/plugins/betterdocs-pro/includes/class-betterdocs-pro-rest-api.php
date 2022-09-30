<?php

class BetterDocs_Pro_Rest_Api
{
    /**
     * BetterDocs_Pro_Rest_Api instance
     * @var BetterDocs_Pro_Rest_Api
     */
    private static $_instance = null;

    /**
     * REST API namespace
     * @var string
     */
    private $namespace = 'betterdocs';

    /**
     * Singleton Instance of BetterDocs_Pro_Rest_Api
     * @return BetterDocs_Pro_Rest_Api
     */
    public static function instance()
    {
        return self::$_instance === null ? self::$_instance = new self() : self::$_instance;
    }

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_api_endpoint'));
    }

    public function register_api_endpoint()
    {
        register_rest_route(
            $this->namespace,
            'order_docs',
            array(
                'methods' => \WP_REST_Server::READABLE,
                'args' => array(

                    'orderby' => [], "order" => [], "per_page" => [], "doc_category" => []
                ),
                'callback' => array($this, 'betterdocs_pro_betterdocs_order_docs'),
                'permission_callback' => '__return_true'
            )
        );
    }

    /**
     * BetterDocs Order API Callback
     */
    public function betterdocs_pro_betterdocs_order_docs($attr)
    {
        $query_args = array(
            'post_status' => 'publish',
            'post_type' => 'docs',
            'posts_per_page' => isset($attr['per_page']) ? $attr['per_page'] : 10,
            'orderby' => isset($attr['orderby']) && $attr['orderby'] === 'betterdocs_order' ? 'post__in' : 'menu_order',
        );

        $term_id = isset($attr['doc_category']) ? $attr['doc_category'] : 0;
        $docs_order = get_term_meta($term_id, '_docs_order', true);

        $term_object = get_term($term_id);


        $query_args['tax_query'][] =
            array(
                'taxonomy' => 'doc_category',
                'field'     => 'slug',
                'terms'    => $term_object->slug,
                'operator' => 'AND',
                'include_children' => false
            );

        $query_args['orderby'] =  !empty($docs_order) ? $query_args['orderby'] : 'menu_order';

        $new_ids = [];
        global $wpdb;

        if (!empty($docs_order)) {

            $docs_order = explode(',', $docs_order);

            $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id = $term_id");

            if (!is_null($results) && !empty($results) && is_array($results)) {

                $object_ids = array_filter($results, function ($value) use ($docs_order) {
                    return !in_array($value->object_id, $docs_order);
                });

                if (!empty($object_ids)) {

                    array_walk($object_ids, function ($value) use (&$new_ids) {
                        $new_ids[] = $value->object_id;
                    });
                }
            }
        } else {
            $docs_order = [];
        }



        $query_args['post__in'] = array_merge($new_ids, $docs_order);

        $data = [];
        $loop = new \WP_Query($query_args);

        if ($loop->have_posts()) {
            while ($loop->have_posts()) {
                $loop->the_post();
                $docs                       = array();
                $docs['id']                 = get_the_ID();
                $docs['title']['rendered']  =  wp_kses(get_the_title(get_the_ID()), BETTERDOCS_PRO_KSES_ALLOWED_HTML);
                $docs['permalink']          = get_permalink();
                $data[] = $docs;
            }
            wp_reset_postdata();
        }
        return $data;
    }
}

if (class_exists('BetterDocs')) {
    BetterDocs_Pro_Rest_Api::instance();
}

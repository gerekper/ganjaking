<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements features of PREMIUM version of YWAR plugin
 *
 * @class   YITH_WooCommerce_Advanced_Reviews_Privacy
 * @package Yithemes
 * @since   1.5.3
 * @author  Your Inspiration Themes
 */
if ( ! class_exists( 'YITH_WooCommerce_Advanced_Reviews_Privacy' ) ) {

    class YITH_WooCommerce_Advanced_Reviews_Privacy extends YITH_Privacy_Plugin_Abstract
    {

        /**
         * Init - hook into events.
         */
        public function __construct()
        {

            /**
             * GDRP privacy policy content
             */
            parent::__construct( _x( 'YITH Advanced Reviews for WooCommerce premium', 'Privacy Policy Content', 'yith-woocommerce-advanced-reviews' ) );

            /**
             * WordPress hook to add exporters
             */
            add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'yith_wc_register_advanced_reviews_customer_data_exporter' ) );

            /**
             * WordPress hook to add erasers
             */
            add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'yith_wc_register_advanced_reviews_customer_data_eraser' ) );

        }

        /**
         * Add privacy policy content for the privacy policy page.
         *
         * @since 1.5.3
         */
        public function get_privacy_message( $section ) {

            $privacy_content_path = YITH_YWAR_VIEWS_PATH . '/privacy/html-policy-content-' . $section . '.php';

            if ( file_exists( $privacy_content_path ) ) {

                ob_start();

                include $privacy_content_path;

                return ob_get_clean();

            }

            return '';

        }

        /**
         * Registers the personal data exporter for yith advanced reviews.
         *
         * @since 1.5.3
         *
         * @param array $exporters An array of personal data exporters.
         * @return array $exporters An array of personal data exporters.
         */
        function yith_wc_register_advanced_reviews_customer_data_exporter( $exporters )
        {

            $exporters['yith-advanced-reviews'] = array(
                'exporter_friendly_name' => _x( 'Yith Advanced Reviews', 'GDPR privacy', 'yith-woocommerce-advanced-reviews' ),
                'callback' => array( $this, 'yith_wc_advanced_reviews_customer_data_exporter' ),
            );

            return $exporters;
        }

        /**
         * Finds and exports personal data associated with an email address from the posts table.
         *
         * @since 1.5.3
         *
         * @param string $email_address The comment author email address.
         * @param int $page Comment page.
         * @return array $return An array of personal data.
         */
        function yith_wc_advanced_reviews_customer_data_exporter( $email_address, $page = 1 )
        {

            $user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

            $customer      = new WC_Customer( $user->ID );

            if ( ! $customer ) {
                return array();
            }

            $posts_per_page = 500;
            $page = (int) $page;

            $args = array(
                'post_type' => YITH_YWAR_POST_TYPE,
                'paged' => $page,
                'posts_per_page' => $posts_per_page,
                'meta_query' => array(
                    array(
                        'key' => YITH_YWAR_META_REVIEW_USER_ID,
                        'value' => $user->ID,
                        'compare' => '=',
                    ),
                ),
            );

            $reviews = get_posts( $args );

            $data_to_export = array();

            foreach ( $reviews as $review ) {

                $comment_data_to_export = array();

                $comment_data_to_export[] = array(
                    'name' => 'Review Author',
                    'value' => get_post_meta( $review->ID, YITH_YWAR_META_REVIEW_AUTHOR, true ),
                );

                $comment_data_to_export[] = array(
                    'name' => 'Review Author Email',
                    'value' => get_post_meta( $review->ID, YITH_YWAR_META_REVIEW_AUTHOR_EMAIL, true ),
                );

                $comment_data_to_export[] = array(
                    'name' => 'Review Author IP',
                    'value' => get_post_meta( $review->ID, YITH_YWAR_META_REVIEW_AUTHOR_IP, true ),
                );

                $comment_data_to_export[] = array(
                    'name' => 'Review Date',
                    'value' => $review->post_date,
                );

                $comment_data_to_export[] = array(
                    'name' => 'Review Title',
                    'value' => $review->post_title,
                );

                $comment_data_to_export[] = array(
                    'name' => 'Review Content',
                    'value' => $review->post_content,
                );


                $comment_data_to_export[] = array(
                    'name' => 'Review Author URL',
                    'value' => $review->guid,
                );

                $data_to_export[] = array(
                    'group_id' => 'yith_wc_advanced_reviews',
                    'group_label' => _x( 'Reviews', 'GDPR privacy', 'yith-woocommerce-advanced-reviews' ),
                    'item_id' => "yith_reviews-" . $review->ID,
                    'data' => $comment_data_to_export,
                );

            }

            $done = count( $reviews ) < $posts_per_page;

            return array(
                'data' => $data_to_export,
                'done' => $done,
            );

        }

        /**
         * Registers the personal data eraser for yith advanced reviews.
         *
         * @since 1.5.3
         *
         * @param  array $erasers An array of personal data erasers.
         * @return array $erasers An array of personal data erasers.
         */
        function yith_wc_register_advanced_reviews_customer_data_eraser( $erasers )
        {

            $erasers[ 'yith-advanced-reviews' ] = array(
                'eraser_friendly_name' => _x( 'Yith Advanced Reviews', 'GDPR privacy', 'yith-woocommerce-advanced-reviews' ),
                'callback' => array( $this, 'yith_wc_advanced_reviews_customer_data_eraser' ),
            );

            return $erasers;
        }

        /**
         * Erases personal data associated with an email address from the posts table.
         *
         * @since 1.5.3
         *
         * @param  string $email_address The comment author email address.
         * @param  int $page Comment page.
         * @return array
         */
        function yith_wc_advanced_reviews_customer_data_eraser( $email_address, $page = 1 )
        {

            $user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

            $customer      = new WC_Customer( $user->ID );

            if ( ! $customer ) {
                return array();
            }

            $posts_per_page = 500;
            $page = (int) $page;
            $items_removed  = false;
            $items_retained = false;

            $args = array(
                'post_type' => YITH_YWAR_POST_TYPE,
                'paged' => $page,
                'posts_per_page' => $posts_per_page,
                'meta_query' => array(
                    array(
                        'key' => YITH_YWAR_META_REVIEW_USER_ID,
                        'value' => $user->ID,
                        'compare' => '=',
                    ),
                ),
            );

            $reviews = get_posts( $args );

            $anon_author = _x( 'Anonymous', 'GDPR privacy', 'yith-woocommerce-advanced-reviews' );
            $messages    = array();

            global $wpdb;

            foreach ( $reviews as $review ) {

                /**
                 * Filters whether to anonymize the review.
                 *
                 * @since 1.5.3
                 *
                 * @param bool|string                    Whether to apply the reviwe anonymization (bool).
                 *                                       Custom prevention message (string). Default true.
                 * @param YITH_WooCommerce_Advanced_Reviews_Premium $review             YITH_WooCommerce_Advanced_Reviews_Premium object.
                 */
                $anon_message = apply_filters( 'yith_wc_anonymize_advanced_review', true, $review );

                if ( true !== $anon_message ) {
                    if ( $anon_message && is_string( $anon_message ) ) {
                        $messages[] = esc_html( $anon_message );
                    } else {
                        /* translators: %d: Comment ID */
                        $messages[] = sprintf( __( 'Review %d contains personal data but could not be anonymized.' ), $review->ID );
                    }

                    $items_retained = true;

                    continue;
                }

                $guid = wp_privacy_anonymize_data( 'url', $review->guid );

                // Update post
                $result = $wpdb->query( $wpdb->prepare(
                    "
                    UPDATE $wpdb->posts 
                    SET guid = '$guid', post_author = '0'
                    WHERE ID = %d
                    ", $review->ID ) );

                if ( $result ) {

                    update_post_meta( $review->ID, YITH_YWAR_META_REVIEW_USER_ID, 0 );

                    update_post_meta( $review->ID, YITH_YWAR_META_REVIEW_AUTHOR, $anon_author );

                    update_post_meta( $review->ID, YITH_YWAR_META_REVIEW_AUTHOR_EMAIL, wp_privacy_anonymize_data( 'email', get_post_meta( $review->ID, YITH_YWAR_META_REVIEW_AUTHOR_EMAIL, true ) ) );

                    update_post_meta( $review->ID, YITH_YWAR_META_REVIEW_AUTHOR_IP, wp_privacy_anonymize_data( 'ip', get_post_meta( $review->ID, YITH_YWAR_META_REVIEW_AUTHOR_IP, true ) ) );

                    update_post_meta( $review->ID, YITH_YWAR_META_REVIEW_AUTHOR_URL, wp_privacy_anonymize_data( 'url', get_post_meta( $review->ID, YITH_YWAR_META_REVIEW_AUTHOR_URL, true ) ) );

                    $items_removed = true;

                } else {

                    $items_retained = true;
                }

            }

            $done = count( $reviews ) < $posts_per_page;

            return array(
                'items_removed'  => $items_removed,
                'items_retained' => $items_retained,
                'messages'       => $messages,
                'done'           => $done,
            );

        }

    }

}

new YITH_WooCommerce_Advanced_Reviews_Privacy();
<?php
/**
 * Notes class
 *
 * @author  Yithemes
 * @package YITH Deals for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCDLS_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

if ( !class_exists( 'YITH_WCDLS_Ajax_Premium' ) ) {
    /**
     * YITH_WCDLS_Ajax_Premium
     *
     * @since 1.0.0
     */
    class YITH_WCDLS_Ajax_Premium extends YITH_WCDLS_Ajax
    {

        /**
         * Constructor
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function __construct()
        {
            parent::__construct();

            add_action('wp_ajax_yith_wcdls_add_condition_row',array($this,'add_conditions_row'));
            add_action('wp_ajax_yith_wcdls_category_search', array($this,'category_search'));
            add_action('wp_ajax_yith_wcdls_tag_search', array($this,'tag_search'));

            add_action('wp_ajax_yith_wcdls_accept_offer',array($this,'accept_offer'));
            add_action('wp_ajax_yith_wcdls_decline_offer',array($this,'decline_offer'));
            add_action('wp_ajax_yith_wcdls_show_offer',array($this,'show_offer'));
            add_action('wp_ajax_nopriv_yith_wcdls_accept_offer',array($this,'accept_offer'));
            add_action('wp_ajax_nopriv_yith_wcdls_decline_offer',array($this,'decline_offer'));
            add_action('wp_ajax_nopriv_yith_wcdls_show_offer',array($this,'show_offer'));

        }

        /**
         * Show Offer
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function show_offer() {

            $offer_id = $_POST['id'];
            $offer_accepted  = get_post_meta( $offer_id, 'yith_wcdls_offer', true );
            $type_layout = apply_filters('yith_wcdls_show_layout',$offer_accepted['type_layout'],$offer_id);
            $layout = array(
                'offer_id' => $offer_id,
                'layout_type' => $type_layout,
            );

            wp_send_json($layout);

        }
        /**
         * Decline offer action
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function decline_offer() {

            do_action('yith_wcdls_before_decline_offer_action',$_POST['id']);

            $offer_id = $_POST['id'];
            $function = YITH_Deals()->functions;
            $show_another_offer_id = $function->decline_offer($offer_id);
            if ( $show_another_offer_id ) {
                $deals_offer = get_post_meta($show_another_offer_id,'yith_wcdls_offer',true);
                $offer = get_post($show_another_offer_id);
                $args = apply_filters( 'yith_wcdls_popup_template_args', array(
                    'animation' => $deals_offer['type_layout'],
                    'content'   => do_shortcode($offer->post_content),
                    'offer_id'     => $show_another_offer_id,
                ) );
                ob_start();
                wc_get_template( 'yith-deals-popup.php', $args, '', YITH_WCDLS_TEMPLATE_PATH . 'frontend/' );
                $templates['offer'] = ob_get_clean();
                wp_send_json($templates);
            }

            do_action('yith_wcdls_after_decline_offer_action',$_POST['id']);


            die();
        }
        /**
         * Accept offer action
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function accept_offer() {

            do_action('yith_wcdls_before_accept_offer_action',$_POST['id']);

            $offer_id = $_POST['id'];
            $cart = WC()->cart;
            $function = YITH_Deals()->functions;
            $function->accept_offer($offer_id,$cart);

            do_action('yith_wcdls_after_accept_offer_action',$_POST['id']);

            die();
        }

        /**
         * Add new row in offer condition list
         *
         * @since  1.0.0
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */

        public function add_conditions_row() {

            $args = array(
                'i' => $_POST['index'],
            );
            wc_get_template( 'wcdls-conditions-row.php',$args, '', YITH_WCDLS_TEMPLATE_PATH . 'admin/metabox/' );
            die();
        }

        /**
         * function category_search
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function category_search() {
            check_ajax_referer( 'search-categories', 'security' );

            ob_start();

            if ( version_compare( WC()->version, '2.7', '<' ) ) {
                $term = (string) wc_clean( stripslashes( $_GET['term'] ) );
            } else {
                $term = (string) wc_clean( stripslashes( $_GET['term']['term'] ) );
            }

            if ( empty( $term ) ) {
                die();
            }
            global $wpdb;
            $terms = $wpdb->get_results( 'SELECT name, slug, wpt.term_id FROM ' . $wpdb->prefix . 'terms wpt, ' . $wpdb->prefix . 'term_taxonomy wptt WHERE wpt.term_id = wptt.term_id AND wptt.taxonomy = "product_cat" and wpt.name LIKE "%'.$term.'%" ORDER BY name ASC;' );

            $found_categories = array();

            if ( $terms ) {
                foreach ( $terms as $cat ) {
                    $found_categories[$cat->term_id] = ( $cat->name ) ? $cat->name : 'ID: ' . $cat->slug;
                }
            }

            $found_categories = apply_filters( 'yith_wcdls_json_search_categories', $found_categories );
            wp_send_json( $found_categories );
        }
        /**
         * function tag search
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function tag_search() {
            check_ajax_referer( 'search-tags', 'security' );

            ob_start();

            if ( version_compare( WC()->version, '2.7', '<' ) ) {
                $term = (string) wc_clean( stripslashes( $_GET['term'] ) );
            } else {
                $term = (string) wc_clean( stripslashes( $_GET['term']['term'] ) );
            }

            if ( empty( $term ) ) {
                die();
            }
            global $wpdb;
            $terms = $wpdb->get_results( 'SELECT name, slug, wpt.term_id FROM ' . $wpdb->prefix . 'terms wpt, ' . $wpdb->prefix . 'term_taxonomy wptt WHERE wpt.term_id = wptt.term_id AND wptt.taxonomy = "product_tag" and wpt.name LIKE "%'.$term.'%" ORDER BY name ASC;' );

            $found_tags = array();

            if ( $terms ) {
                foreach ( $terms as $tag ) {
                    $found_tags[$tag->term_id] = ( $tag->name ) ? $tag->name : 'ID: ' . $tag->slug;
                }
            }

            $found_tags = apply_filters( 'yith_wcdls_json_search_tags', $found_tags );
            wp_send_json( $found_tags );
        }
    }
}
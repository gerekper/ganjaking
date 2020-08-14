<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCGPF_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 * @class      YITH_WCGPF_Google_Product_Feed_Ajax
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_WCGPF_Google_Product_Feed_Ajax' ) ) {

    class YITH_WCGPF_Google_Product_Feed_Ajax {

        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Google_Product_Feed_Ajax
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main plugin Instance
         *
         * @return
         * @var YITH_WCGPF_Google_Product_Feed_Ajax instance
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public static function get_instance()
        {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }

        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function __construct()
        {
            add_action('wp_ajax_yith_wcgpf_category_search', array($this,'category_search'));
            add_action('wp_ajax_yith_wcgpf_tag_search', array($this,'tag_search'));
            add_action('wp_ajax_yith_wcgpf_save_custom_fields', array($this,'save_custom_fields'));
            add_action('wp_ajax_yith_wcgpf_load_merchant_options', array($this,'load_merchant_template'));

        }


        /**
         * Load merchant template
         * var ID template
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function load_merchant_template() {
            $template_id = isset($_POST['template_id']) ? $_POST['template_id'] :'';
            $merchant = isset($_POST['merchant']) ? $_POST['merchant'] : 'google';
            $data_post = $_POST['data_post'];
            $show_templates = isset($_POST['show_templates']) ? $_POST['show_templates'] : '';
            $feed_edit = get_post_meta($data_post,'yith_wcgpf_save_feed',true);
            include(YITH_WCGPF_TEMPLATE_PATH.'merchant/'.$merchant.'.php');

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

            $found_categories = apply_filters( 'yith_wcgpf_json_search_categories', $found_categories );
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

            $found_tags = apply_filters( 'yith_wcgpf_json_search_tags', $found_tags );
            wp_send_json( $found_tags );
        }

        public function save_custom_fields() {

            $custom_fields = $_POST['custom_fields'];
            update_option('yith_wcgpf_custom_fields',$custom_fields);
            die();
        }
    }

}
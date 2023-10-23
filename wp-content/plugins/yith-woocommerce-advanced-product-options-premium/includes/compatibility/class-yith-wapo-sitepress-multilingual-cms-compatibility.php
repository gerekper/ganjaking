<?php
/**
 * WPML compatibility.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddons
 */

defined( 'ICL_SITEPRESS_VERSION' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_WPML_Compatibility' ) ) {
    /**
     * Compatibility Class
     *
     * @class   YITH_WAPO_WPML_Compatibility
     * @since   4.0.0
     */
    class YITH_WAPO_WPML_Compatibility {

        /**
         * Single instance of the class
         *
         * @var YITH_WAPO_WPML_Compatibility
         */
        protected static $instance;

        /**
         * The default WPML language.
         *
         * @var string
         */
        public $wpml_default_lang;


        /**
         * Returns single instance of the class
         *
         * @return YITH_WAPO_WPML_Compatibility
         */
        public static function get_instance() {
            return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
        }

        /**
         * YITH_WAPO_WPML_Compatibility constructor
         */
        private function __construct() {
            $this->wpml_default_lang = apply_filters( 'wpml_default_language', NULL );

            add_filter( 'yith_wapo_get_original_product_id', array( $this, 'get_parent_id' ) );
            add_filter( 'yith_wapo_get_original_category_ids', array( $this, 'get_original_category_ids' ), 10, 3 );
            add_filter( 'yith_wapo_addon_product_id', array( $this, 'get_translated_product_id' ) );
            add_filter( 'yith_wapo_conditional_rule_variation', array( $this, 'display_variation_based_current_language' ) );
            add_filter( 'yith_wapo_frontend_localize_args', array( $this, 'set_current_wpml_language' ) );

        }

        /**
         * Retrieve the WPML parent product id
         *
         * @param int $id ID.
         *
         * @return int
         */
        public function get_parent_id( $id ) {
            /**
             * WPML Post Translations
             *
             * @var WPML_Post_Translation $wpml_post_translations
             */
            global $wpml_post_translations;

            $parent_id = ! ! $wpml_post_translations ? $wpml_post_translations->get_original_element( $id ) : false;

            if ( $parent_id ) {
                $id = $parent_id;
            }

            return $id;
        }

        /**
         * Retrieve the WPML parent category ids
         *
         * @param Array      $categories Categories.
         * @param WC_Product $product Product.
         * @param int        $product_id Parent product id.
         * @return array
         */
        public function get_original_category_ids( $categories, $product, $product_id ) {

            if ( $product_id !== $product->get_id() ) {
                $original_categories = array();
                $default_language    = apply_filters( 'wpml_default_language', null );
                foreach ( $categories as $id ) {
                    $original_categories[] = apply_filters( 'wpml_object_id', $id, 'product_cat', true, $default_language );
                }
                if ( ! empty( $original_categories ) ) {
                    $categories = $original_categories;
                }
            }
            return $categories;
        }

        /**
         * Retrieve product id in current language
         *
         * @param int $product_id Product id.
         * @return int
         */
        public function get_translated_product_id( $product_id ) {

            if ( $product_id ) {
                $my_current_lang = apply_filters( 'wpml_current_language', null );
                $my_default_lang = apply_filters( 'wpml_default_language', null );
                if ( $my_current_lang !== $my_default_lang ) {
                    $product_id = apply_filters( 'wpml_object_id', $product_id, 'post' );
                }
            }
            return $product_id;
        }

        /**
         * Filter the current WPML Language
         *
         * @param array $args The args passed to the JS
         * @return array mixed
         */
        public function set_current_wpml_language( $args ) {

            global $sitepress;

            $args['currentLanguage'] = isset( $sitepress ) ? $sitepress->get_current_language() : $args['currentLanguage'];

            return $args;
        }

        /**
         * Retrieve current variation translation product
         *
         * @param Array $conditional_rule_addon conditional rules.
         * @return array
         */
        public function display_variation_based_current_language( $conditional_rule_addon ) {

            $my_current_lang = apply_filters( 'wpml_current_language', null );
            $my_default_lang = apply_filters( 'wpml_default_language', null );

            if ( $my_current_lang !== $my_default_lang ) {
                if ( ! empty( $conditional_rule_addon ) ) {
                    $new_conditional_rule_array = array();
                    foreach ( $conditional_rule_addon as $variation_id ) {
                        $variation_id                 = apply_filters( 'wpml_object_id', $variation_id, 'post' );
                        $new_conditional_rule_array[] = $variation_id;
                    }
                    $conditional_rule_addon = $new_conditional_rule_array;
                }
            }
            return $conditional_rule_addon;
        }


    }
}

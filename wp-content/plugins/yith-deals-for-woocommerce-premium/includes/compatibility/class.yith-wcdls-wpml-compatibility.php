<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * WPML Deals Class
 *
 * @class   YITH_WCDLS_WPML_Compatibility
 * @package Yithemes
 * @since   1.0.3
 * @author  Yithemes
 *
 */
if(!class_exists('YITH_WCDLS_WPML_Compatibility')) {
    class YITH_WCDLS_WPML_Compatibility
    {
        /**
         * Single instance of the class
         *
         * @var \YITH_WCDLS_WPML_Compatibility
         */
        protected static $instance;

        /**
         * @var YITH_WCDLS_WPML_Compatibility
         */
        public $wpml_integration;

        /**
         * Returns single instance of the class
         *
         * @param YITH_WCDLS_WPML_Compatibility $wpml_integration
         *
         * @return YITH_WCDLS_WPML_Compatibility
         */
        public static function get_instance($wpml_integration)
        {
            if (is_null(static::$instance)) {
                static::$instance = new static($wpml_integration);
            }

            return static::$instance;
        }

        /**
         * Constructor
         *
         * @access public
         *
         * @param YITH_WCDLS_WPML_Compatibility
         */
        public function __construct()
        {
            if ($this->is_active()) {
                $this->_init_wpml_vars();
                add_filter('yith_wcdls_get_offer', array($this, 'get_translation_offer'), 10, 2);
                add_filter('yith_wcdls_product_add_to_cart',array($this,'get_translation_product'),10);
                add_filter('yith_wcdls_remove_some_products',array($this,'get_translation_remove_product'));

            }
        }

        /**
         * init the WPML vars
         */
        protected function _init_wpml_vars()
        {
            if ($this->is_active()) {
                global $sitepress;
                $this->sitepress = $sitepress;
                $this->current_language = $this->sitepress->get_current_language();
                $this->default_language = $this->sitepress->get_default_language();
            }
        }

        /**
         * return true if WPML is active
         *
         * @return bool
         */
        public function is_active()
        {
            global $sitepress;

            return !empty($sitepress);
        }

        /**
         * Get Translation Offer
         *
         * @access public
         *
         * @param $offer $user
         * @return $offer
         */

        public function get_translation_offer($offer, $user)
        {

            $offer_id = yit_wpml_object_id($offer->ID, 'yith_wcdls_offer', true);

            if ($offer->ID != $offer_id) {
                $offer = get_post($offer_id);
            }

            return $offer;

        }

        public function get_translation_product($product_id) {

            $id_translate_product = yit_wpml_object_id($product_id,'product',true);
            if( $product_id != $id_translate_product ) {
                $product_id = $id_translate_product;
            }

            return $product_id;

        }

        public function get_translation_remove_product($products_to_remove) {

            if ( is_array( $products_to_remove ) ) {

                $products_to_remove_translated = array();

                foreach ( $products_to_remove as $id ) {

                    $id = yit_wpml_object_id($id,'product',true);

                    $products_to_remove_translated[] = $id;

                }

                return $products_to_remove_translated;

            }

            return $products_to_remove;
        }
    }

    return new YITH_WCDLS_WPML_Compatibility();
}



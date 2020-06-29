<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * WPML Compatibility Class
 *
 * @class   YITH_WCPB_Wpml_Compatibility
 * @package Yithemes
 * @since   1.0.11
 * @author  Yithemes
 *
 */
class YITH_WCPB_Wpml_Compatibility_Premium extends YITH_WCPB_Wpml_Compatibility {

    /** @var YITH_WCPB_Wpml_Compatibility_Premium */
    protected static $_instance;

    public $bundle_meta_to_copy = array( '_yith_wcpb_bundle_data', '_yith_wcpb_per_item_pricing', '_yith_wcpb_non_bundled_shipping', '_yith_wcpb_bundle_advanced_options' );


    /**
     * Constructor
     *
     * @access protected
     */
    protected function __construct() {
        parent::__construct();

        global $sitepress;

        if ( $sitepress ) {
            add_filter( 'wcml_exception_duplicate_products_in_cart', array( $this, 'duplicate_exception_in_cart' ), 10, 2 );

            add_action( 'woocommerce_before_calculate_totals', array( $this, 'save_temp_bundle_products' ), 101 );
            add_action( 'woocommerce_before_calculate_totals', array( $this, 'restore_temp_bundle_products' ), 999 );

            add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'sync_bundled_item_keys' ), 999 );

            /**
             * Multi Currency
             *
             * @since 1.1.8
             */
            add_action( 'wp_ajax_yith_wcpb_get_bundle_total_price', array( $this, 'set_currency_and_add_price_filters_when_retrieve_ajax_bundle_price' ), 9 );
            add_action( 'wp_ajax_nopriv_yith_wcpb_get_bundle_total_price', array( $this, 'set_currency_and_add_price_filters_when_retrieve_ajax_bundle_price' ), 9 );
            add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_currency_hidden_input_in_bundles' ) );
        }
    }

    /**
     * set the currency and add price filters in AJAX get bundle price
     * [Multi Currency]
     *
     * @since 1.1.8
     */
    public function set_currency_and_add_price_filters_when_retrieve_ajax_bundle_price() {
        global $woocommerce_wpml;
        if ( !$woocommerce_wpml
             ||
             !isset( $woocommerce_wpml->multi_currency )
             ||
             !isset( $woocommerce_wpml->multi_currency->prices )
             ||
             !is_callable( array( $woocommerce_wpml->multi_currency, 'set_client_currency' ) )
             ||
             empty( $_REQUEST[ 'yith_wcpb_wpml_client_currency' ] )
        )
            return;


        /* set currency */
        $currency = $_REQUEST[ 'yith_wcpb_wpml_client_currency' ];
        $woocommerce_wpml->multi_currency->set_client_currency( $currency );


        /* Add needed price filters */
        $multi_currency_prices = $woocommerce_wpml->multi_currency->prices;
        if ( is_callable( array( $multi_currency_prices, 'currency_filter' ) ) )
            add_filter( 'woocommerce_currency', array( $multi_currency_prices, 'currency_filter' ) );

        if ( is_callable( array( $multi_currency_prices, 'product_price_filter' ) ) )
            add_filter( 'get_post_metadata', array( $multi_currency_prices, 'product_price_filter' ), 10, 4 );

        if ( is_callable( array( $multi_currency_prices, 'variation_prices_filter' ) ) )
            add_filter( 'get_post_metadata', array( $multi_currency_prices, 'variation_prices_filter' ), 12, 4 );

        if ( is_callable( array( $multi_currency_prices, 'price_currency_filter' ) ) )
            add_filter( 'wcml_price_currency', array( $multi_currency_prices, 'price_currency_filter' ) );

        if ( is_callable( array( $multi_currency_prices, 'raw_price_filter' ) ) )
            add_filter( 'wcml_raw_price_amount', array( $multi_currency_prices, 'raw_price_filter' ), 10, 2 );

        if ( is_callable( array( $multi_currency_prices, 'get_product_price_in_currency' ) ) )
            add_filter( 'wcml_product_price_by_currency', array( $multi_currency_prices, 'get_product_price_in_currency' ), 10, 2 );

        if ( is_callable( array( $multi_currency_prices, 'filter_price_filter_results' ) ) )
            add_filter( 'woocommerce_price_filter_results', array( $multi_currency_prices, 'filter_price_filter_results' ), 10, 3 );

        if ( is_callable( array( $multi_currency_prices, 'add_currency_to_variation_prices_hash' ) ) )
            add_filter( 'woocommerce_get_variation_prices_hash', array( $multi_currency_prices, 'add_currency_to_variation_prices_hash' ) );
    }

    /**
     * Add an hidden input in bundles for currency
     * [Multi Currency]
     *
     * @since 1.1.8
     */
    public function add_currency_hidden_input_in_bundles() {
        global $product, $woocommerce_wpml;
        if ( !$woocommerce_wpml
             ||
             !isset( $woocommerce_wpml->multi_currency )
             ||
             !is_callable( array( $woocommerce_wpml->multi_currency, 'get_client_currency' ) )
             ||
             !$product || !$product->is_type( 'yith_bundle' )
        )
            return;

        $client_currency = $woocommerce_wpml->multi_currency->get_client_currency();
        echo "<input type='hidden' name='yith_wcpb_wpml_client_currency' value='$client_currency' />";
    }

    /**
     * search for bundled items and synchronize the bundle product info
     *
     * @param WC_Cart $cart
     */
    public function sync_bundled_item_keys( $cart ) {
        foreach ( $cart->cart_contents as $key => $cart_item ) {
            if ( isset( $cart_item[ 'cartstamp' ] ) && isset( $cart_item[ 'bundled_items' ] ) ) {
                $cart->cart_contents[ $key ][ 'bundled_items' ] = array();
            }
        }
        foreach ( $cart->cart_contents as $key => $cart_item ) {
            if ( isset( $cart_item[ 'bundled_by' ] ) && isset( $cart->cart_contents[ $cart_item[ 'bundled_by' ] ][ 'bundled_items' ] ) ) {
                $cart->cart_contents[ $cart_item[ 'bundled_by' ] ][ 'bundled_items' ][] = $key;
                $cart->cart_contents[ $cart_item[ 'bundled_by' ] ][ 'bundled_items' ]   = array_unique( $cart->cart_contents[ $cart_item[ 'bundled_by' ] ][ 'bundled_items' ] );
            }
        }
    }

    public function duplicate_exception_in_cart( $exclude, $cart_item ) {
        if ( isset( $cart_item[ 'bundled_items' ] ) || isset( $cart_item[ 'bundled_by' ] ) ) {
            $exclude = true;
        }

        return $exclude;
    }

    /**
     * @param WC_Cart $cart
     */
    public function save_temp_bundle_products( $cart ) {
        global $woocommerce;
        $temp_bundle_products = array();

        if ( !empty( $cart->temp_bundle_products ) ) {
            return;
        }

        foreach ( $cart->cart_contents as $key => $item ) {
            if ( isset( $item[ 'bundled_by' ] ) || isset( $item[ 'cartstamp' ] ) ) {
                $temp_bundle_products[ $key ] = $item;
                unset( $cart->cart_contents[ $key ] );
            }
        }

        $cart->temp_bundle_products = $temp_bundle_products;

        $woocommerce->session->cart = $cart;
    }

    /**
     * @param WC_Cart $cart
     */
    public function restore_temp_bundle_products( $cart ) {
        global $woocommerce;

        if ( empty( $cart->temp_bundle_products ) ) {
            return;
        }

        $temp_bundle_products = $cart->temp_bundle_products;
        unset( $cart->temp_bundle_products );

        $cart->cart_contents = array_merge( $cart->cart_contents, $temp_bundle_products );

        $woocommerce->session->cart = $cart;
    }


    public function get_wpml_product_id_current_language( $id ) {
        return $this->wpml_object_id( $id, 'product', true );
    }

    public function get_wpml_term_slug_current_language( $slug, $attribute ) {
        global $woocommerce_wpml;

        if ( isset( $woocommerce_wpml ) ) {
            if ( $woocommerce_wpml->attributes->is_translatable_attribute( $attribute ) ) {
                $default_term_id = $woocommerce_wpml->terms->wcml_get_term_id_by_slug( $attribute, $slug );
                $tr_id           = apply_filters( 'translate_object_id', $default_term_id, $attribute, false );

                if ( $tr_id ) {
                    $translated_term = $woocommerce_wpml->terms->wcml_get_term_by_id( $tr_id, $attribute );

                    return $translated_term->slug;
                }
            }
        }

        return $slug;
    }

}

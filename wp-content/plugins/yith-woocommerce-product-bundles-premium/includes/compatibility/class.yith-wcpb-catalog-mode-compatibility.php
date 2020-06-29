<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * YITH WooCommerce Catalog Mode Compatibility Class
 *
 * @class   YITH_WCPB_Catalog_Mode_Compatibility
 * @package Yithemes
 * @since   1.1.3
 * @author  Yithemes
 *
 */
class YITH_WCPB_Catalog_Mode_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCPB_Catalog_Mode_Compatibility
     */
    protected static $instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCPB_Catalog_Mode_Compatibility
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Constructor
     *
     * @access private
     */
    private function __construct() {
        if ( !function_exists( 'YITH_WCTM' ) )
            return;

        if ( $this->is_applied() ) {
            $catalog_mode = YITH_WCTM();
            add_filter( 'yith_wcpb_woocommerce_get_price_html', array( $catalog_mode, 'show_product_price' ), 10, 2 );
            add_filter( 'yith_wcpb_show_bundled_items_prices', array( $this, 'check_show_bundled_items_prices' ), 10, 3 );
            add_filter( 'yith_wcpb_ajax_update_price_enabled', array( $this, 'check_ajax_update_price_enabled' ), 10, 2 );
        }
    }

    /**
     * @param bool                   $value
     * @param YITH_WC_Bundled_Item   $bundled_item
     * @param WC_Product_Yith_Bundle $product
     *
     * @since   1.1.4
     * @return bool
     */
    public function check_show_bundled_items_prices( $value, $bundled_item, $product ) {
        $catalog_mode = YITH_WCTM();

        return !$catalog_mode->check_price_hidden( $value, $product->get_id() );
    }

    /**
     * @param bool                   $value
     * @param WC_Product_Yith_Bundle $product
     *
     * @since   1.1.4
     * @return bool
     */
    public function check_ajax_update_price_enabled( $value, $product ) {
        $catalog_mode = YITH_WCTM();

        return $value && !$catalog_mode->check_price_hidden( $value, $product->get_id() );
    }

    /**
     * return true if is applied catalog mode
     * (plugin active, check admins,etc...)
     *
     * @return bool
     */
    public function is_applied() {
        if ( !function_exists( 'YITH_WCTM' ) )
            return false;

        $catalog_mode = YITH_WCTM();
        if ( get_option( 'ywctm_enable_plugin' ) == 'yes' && $catalog_mode->check_user_admin_enable() ) {
            if ( !is_admin() || $catalog_mode->is_quick_view() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
                return true;
            }
        }

        return false;
    }
}
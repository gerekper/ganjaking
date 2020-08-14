<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Multivendor Compatibility Class
 *
 * @class   YITH_WCBEP_Multivendor_Compatibility
 * @package Yithemes
 * @since   1.1.23
 * @author  Yithemes
 *
 */
class YITH_WCBEP_Multivendor_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCBEP_Multivendor_Compatibility
     */
    protected static $_instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCBEP_Multivendor_Compatibility
     */
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public $plugin_page = 'admin.php?page=yith_wcbep_panel';

    /**
     * Constructor
     */
    protected function __construct() {
        add_filter( 'yith_wcbep_product_list_query_args', array( $this, 'add_vendor_tax_in_query_args' ) );
        add_filter( 'yith_wcbep_settings_admin_tabs', array( $this, 'remove_tabs_for_vendors' ), 11 );

        add_action( 'admin_menu', array( $this, 'add_page_to_menu' ), 5 );
        add_action( 'admin_init', array( $this, 'check_vendor_cant_manage_bulk' ), 15 );

        add_filter( 'yith_wpv_vendor_menu_items', array( $this, 'add_menu_page_to_vendors' ) );

        add_filter( 'yith_wcbep_get_custom_taxonomies', array( $this, 'remove_vendor_in_custom_taxonomies' ) );

    }

    public function add_menu_page_to_vendors( $pages ) {
        $pages[] = $this->plugin_page;

        return $pages;
    }

    public function add_page_to_menu() {
        if ( 'yes' == get_option( 'yith_wpv_vendors_option_bulk_product_editing_options_management', 'no' ) && $this->is_vendor() ) {
            add_menu_page( 'yith_wc_bulk_product_editing', 'Bulk Product Editing', YITH_Vendors()->admin->get_special_cap(), $this->plugin_page, null, 'dashicons-forms' );
        }
    }

    public function check_vendor_cant_manage_bulk() {
        global $pagenow;

        if ( $this->is_vendor() ) {
            $vendor_cant_manage_bulk_product_editing = 'no' == get_option( 'yith_wpv_vendors_option_bulk_product_editing_options_management', 'no' );
            $is_bulk_editing_page                    = 'admin.php' == $pagenow && !empty( $_GET[ 'page' ] ) && 'yith_wcbep_panel' == $_GET[ 'page' ];
            if ( $vendor_cant_manage_bulk_product_editing && $is_bulk_editing_page ) {
                wp_die( __( 'Permission denied!', 'yith-woocommerce-bulk-product-editing' ) );
            }
        }
    }

    /**
     * Return true if the current user is vendor
     *
     * @return bool
     */
    public function is_vendor() {
        $vendor = yith_get_vendor( 'current', 'user' );

        return $vendor->is_valid() && $vendor->has_limited_access();
    }

    /**
     * Add vendor tax in query args
     * Vendors can view only their products
     *
     * @param $query_args
     *
     * @return mixed
     */
    public function add_vendor_tax_in_query_args( $query_args ) {
        if ( function_exists( 'yith_get_vendor' ) && class_exists( 'YITH_Vendor' ) ) {
            $vendor = yith_get_vendor( 'current', 'user' );
            if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
                $query_args[ 'tax_query' ][] = array(
                    'taxonomy' => YITH_Vendor::$taxonomy,
                    'field'    => 'id',
                    'terms'    => $vendor->id,
                );
            }
        }

        return $query_args;
    }

    public function remove_tabs_for_vendors( $tabs ) {
        if ( $this->is_vendor() ) {
            $tabs = array( 'bulk-edit' => $tabs[ 'bulk-edit' ] );
        }

        return $tabs;
    }

    public function remove_vendor_in_custom_taxonomies( $taxonomies ) {
        if ( $this->is_vendor() ) {
            $taxonomies = array_diff( $taxonomies, array( YITH_Vendor::$taxonomy ) );
        }

        return $taxonomies;
    }

}

/**
 * Unique access to instance of YITH_WCBEP_Multivendor_Compatibility class
 *
 * @return YITH_WCBEP_Multivendor_Compatibility
 * @since 1.0.23
 */
function YITH_WCBEP_Multivendor_Compatibility() {
    return YITH_WCBEP_Multivendor_Compatibility::get_instance();
}
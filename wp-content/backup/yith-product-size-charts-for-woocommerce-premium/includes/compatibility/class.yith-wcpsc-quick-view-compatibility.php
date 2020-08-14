<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Quick View Compatibility Class
 *
 * @class   YITH_WCPSC_Quick_View_Compatibility
 * @package Yithemes
 * @since   1.1.1
 * @author  Yithemes
 *
 */
class YITH_WCPSC_Quick_View_Compatibility {

    /** @var YITH_WCPSC_Quick_View_Compatibility */
    private static $_instance;

    /** @return YITH_WCPSC_Quick_View_Compatibility */
    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
    }

    /**
     * Constructor
     *
     * @access public
     * @since  1.0.0
     */
    private function __construct() {
        if ( !YITH_WCPSC_Compatibility::has_plugin( 'quick-view' ) ) return;

        $available_positions        = array(
            'before_summary'     => array( 'action' => 'yith_wcqv_before_product_summary', 'priority' => 25 ),
            'before_description' => array( 'action' => 'yith_wcqv_product_summary', 'priority' => 15 ),
            'after_description'  => array( 'action' => 'yith_wcqv_product_summary', 'priority' => 25 ),
            'after_add_to_cart'  => array( 'action' => 'yith_wcqv_product_summary', 'priority' => 35 ),
            'after_summary'      => array( 'action' => 'yith_wcqv_after_product_summary', 'priority' => 9 ),
        );
        $quick_view_button_position = get_option( 'yith-wcpsc-popup-button-quick-view-position', 'none' );

        if ( isset( $available_positions[ $quick_view_button_position ] ) ) {
            $action   = $available_positions[ $quick_view_button_position ][ 'action' ];
            $priority = $available_positions[ $quick_view_button_position ][ 'priority' ];
            add_action( $action, array( $this, 'print_buttons' ), $priority );
        }

    }

    /**
     * Print the size chart buttons
     */
    public function print_buttons() {
        do_action( 'yith_wcpsc_size_chart_buttons' );
    }
}

YITH_WCPSC_Quick_View_Compatibility::get_instance();
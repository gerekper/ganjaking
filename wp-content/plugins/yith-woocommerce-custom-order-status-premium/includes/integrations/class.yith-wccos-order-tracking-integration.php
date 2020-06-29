<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

/**
 * YITH Order Traking Compatibility Class
 *
 * @class   YITH_WCCOS_Order_Tracking_Integration
 * @since   1.1.14
 */
class YITH_WCCOS_Order_Tracking_Integration {

    /** @var \YITH_WCCOS_Order_Tracking_Integration */
    private static $_instance;

    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
    }

    private function __construct() {
        if ( $this->is_active() ) {
            add_filter( 'yith_wccos_email_placeholders', array( $this, 'add_order_tracking_placeholders' ), 10, 2 );
        }
    }

    /**
     * @param array    $placeholders
     * @param WC_Order $order
     * @return array
     */
    public function add_order_tracking_placeholders( $placeholders, $order ) {
        /** @var YITH_WooCommerce_Order_Tracking_Premium $YWOT_Instance */
        global $YWOT_Instance;
        if ( class_exists( 'YITH_Tracking_Data' ) && is_callable( 'YITH_Tracking_Data::get' ) &&
             class_exists( 'Carriers' ) && is_callable( 'Carriers::get_instance' ) &&
             is_callable( array( Carriers::get_instance(), 'get_carrier_list' ) ) &&
             $YWOT_Instance && is_callable( array( $YWOT_Instance, 'get_track_url' ) )
        ) {
            $data     = YITH_Tracking_Data::get( $order );
            $carriers = Carriers::get_instance()->get_carrier_list();

            $order_carrier_id = $data->get_carrier_id();
            $carrier_name     = $order_carrier_id ? $carriers[ $order_carrier_id ][ 'name' ] : '';
            $placeholders[ '{tracking_number}' ] = $data->get_tracking_code();
            $placeholders[ '{shipping_date}' ]   = date(get_option('date_format'), strtotime($data->get_pickup_date()));
            $placeholders[ '{tracking_url}' ]    = $YWOT_Instance->get_track_url( $order );
            $placeholders[ '{carrier_name}' ]    = $carrier_name;
        }
        return $placeholders;
    }

    public function is_active() {
        $min_version = apply_filters( 'yith_wccos_order_tracking_integration_min_version', '1.5.7' );
        return defined( 'YITH_YWOT_PREMIUM' ) && defined( 'YITH_YWOT_VERSION' ) && version_compare( YITH_YWOT_VERSION, $min_version, '>=' );
    }
}

return YITH_WCCOS_Order_Tracking_Integration::get_instance();
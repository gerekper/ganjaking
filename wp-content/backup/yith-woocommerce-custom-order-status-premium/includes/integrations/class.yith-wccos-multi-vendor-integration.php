<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


class YITH_WCCOS_Multi_Vendor_Integration {

    /** @var \YITH_WCCOS_Multi_Vendor_Integration */
    private static $_instance;

    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
    }

    private function __construct() {
        if ( $this->is_enabled() ) {
            add_filter( 'yith_wccos_custom_email_recipients', array( $this, 'custom_email_recipients' ), 10, 6 );
            add_filter( 'yith_wccos_get_allowed_recipients', array( $this, 'filter_recipients' ), 10, 6 );
            add_filter( 'yith_wccos_email_recipients', array( $this, 'remove_admin_and_customer_recipients_for_suborder_emails' ), 10, 3 );
        }
    }

    public function is_enabled() {
        return defined( 'YITH_WPV_PREMIUM' ) && defined( 'YITH_WPV_VERSION' ) && version_compare( YITH_WPV_VERSION, '1.12.0', '>=' );
    }

    public function filter_recipients( $recipients ) {
        $recipients[ 'vendor' ] = __( 'Vendor', 'yith-woocommerce-custom-order-status' );
        return $recipients;
    }

    public function remove_admin_and_customer_recipients_for_suborder_emails( $recipients, $status_id, $order_id ) {
        if ( apply_filters( 'yith_wccos_wcmv_remove_admin_and_customer_recipients_in_emails', wp_get_post_parent_id( $order_id ), $order_id, $recipients, $status_id ) ) {
            $to_remove = array( get_option( 'admin_email' ) );

            if ( $order = wc_get_order( $order_id ) ) {
                $to_remove[] = $order->get_billing_email();
            }

            foreach ( $to_remove as $email ) {
                if ( isset( $recipients[ $email ] ) ) {
                    unset( $recipients[ $email ] );
                }
            }
        }

        return $recipients;
    }

    public function custom_email_recipients( $email_recipients, $recipients, $status_id, $order_id, $old_status, $new_status ) {
        if ( in_array( 'vendor', $recipients ) && wp_get_post_parent_id( $order_id ) ) {
            $vendor_id = get_post_field( 'post_author', $order_id );
            $vendor    = yith_get_vendor( $vendor_id, 'user' );

            if ( $vendor->is_valid() ) {
                $vendor_email = $vendor->store_email;
                if ( empty( $vendor_email ) ) {
                    $vendor_owner = get_user_by( 'id', absint( $vendor->get_owner() ) );
                    $vendor_email = $vendor_owner instanceof WP_User ? $vendor_owner->user_email : false;
                }

                if ( $vendor_email ) {
                    $email_recipients = array( $vendor_email => true );
                }
            }
        }

        return $email_recipients;
    }
}

return YITH_WCCOS_Multi_Vendor_Integration::get_instance();
<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Orders Class
 *
 * @class   YITH_WCMBS_Orders
 * @package Yithemes
 * @since   1.2.6
 * @author  Yithemes
 *
 */
class YITH_WCMBS_Orders {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCMBS_Orders
     */
    protected static $_instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCMBS_Manager
     */
    public static function get_instance() {
        $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

        return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
    }

    /**
     * Constructor
     *
     * @access public
     */
    protected function __construct() {
        add_action( 'woocommerce_order_status_completed', array( $this, 'set_user_membership' ) );
        add_action( 'woocommerce_order_status_processing', array( $this, 'set_user_membership' ) );

        // Action to manage Paypal and Stripe disputes
        add_action( 'woocommerce_order_status_changed', array( $this, 'deactivate_memberships' ), 10, 3 );
    }

    /**
     * in case of Paypal and Stripe disputes
     * the memberships in the order will be deactivated
     *
     * @param int    $order_id
     * @param string $old_status
     * @param string $new_status
     *
     * @since 1.1.2
     */
    public function deactivate_memberships( $order_id, $old_status, $new_status ) {
        if ( $new_status == 'on-hold' && in_array( $old_status, array( 'processing', 'completed' ) ) ) {
            $memberships     = YITH_WCMBS_Membership_Helper()->get_memberships_by_order( $order_id );
            $additional_note = sprintf( __( 'Reason: Order #%s status changed from %s to %s', 'yith-woocommerce-membership' ), $order_id, $old_status, $new_status );

            if ( !empty( $memberships ) ) {
                foreach ( $memberships as $membership ) {
                    if ( $membership instanceof YITH_WCMBS_Membership ) {
                        $membership->update_status( 'not_active', 'change_status', $additional_note );
                    }
                }
            }
        }
    }

    /**
     * set user membership when order is completed
     *
     * @param int $order_id id of order
     *
     * @access public
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    public function set_user_membership( $order_id ) {
        $memberships = YITH_WCMBS_Membership_Helper()->get_memberships_by_order( $order_id );

        if ( !empty( $memberships ) ) {
            foreach ( $memberships as $membership ) {
                if ( $membership instanceof YITH_WCMBS_Membership && $membership->status == 'not_active' ) {
                    $membership->update_status( 'resumed' );
                }
            }
        } else {
            $member_product_id = get_option( 'yith-wcmbs-membership-product', false );
            if ( $member_product_id ) {
                $order   = wc_get_order( $order_id );
                $user_id = $order->get_user_id();

                foreach ( $order->get_items() as $item ) {
                    $id = !empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ];
                    if ( $id == $member_product_id ) {

                        $membership_meta_data = array(
                            'plan_id'    => 0,
                            'title'      => get_option( 'yith-wcmbs-membership-name', _x( 'Membership', 'Default value for Membership Plan Name', 'yith-woocommerce-membership' ) ),
                            'start_date' => time(),
                            'end_date'   => 'unlimited',
                            'order_id'   => $order_id,
                            'user_id'    => $user_id,
                            'status'     => 'active',
                        );
                        /* create the Membership */
                        $membership = new YITH_WCMBS_Membership( 0, $membership_meta_data );
                    }
                }
            }
        }
    }
}

/**
 * Unique access to instance of YITH_WCMBS_Admin class
 *
 * @return \YITH_WCMBS_Orders
 * @since 1.0.0
 */
function YITH_WCMBS_Orders() {
    return YITH_WCMBS_Orders::get_instance();
}
<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Orders Class
 *
 * @class   YITH_WCMBS_Orders_Premium
 * @package Yithemes
 * @since   1.2.6
 * @author  Yithemes
 *
 */
class YITH_WCMBS_Orders_Premium extends YITH_WCMBS_Orders {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCMBS_Orders
     */
    protected static $_instance;

    /**
     * Constructor
     *
     * @access public
     */
    protected function __construct() {
        parent::__construct();
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
        $memberships           = YITH_WCMBS_Membership_Helper()->get_memberships_by_order( $order_id );
        $just_actived_plan_ids = array();

        if ( !empty( $memberships ) ) {
            foreach ( $memberships as $membership ) {
                if ( $membership instanceof YITH_WCMBS_Membership && $membership->status == 'not_active' )
                    $membership->update_status( 'resumed' );
            }
        } else {

            $plan_product_ids    = array();
            $plans_product_array = array();
            $plans               = YITH_WCMBS_Manager()->plans;

            if ( !empty( $plans ) ) {
                foreach ( $plans as $plan ) {
                    $member_product_ids = YITH_WCMBS_Manager()->get_membership_product_ids_by_plan( $plan->ID );
                    if ( !!$member_product_ids ) {
                        $plan_product_ids = array_unique( array_merge( $plan_product_ids, $member_product_ids ) );
                        foreach ( $member_product_ids as $member_product_id )
                            $plans_product_array[ $member_product_id ][] = $plan;
                    }
                }
            }

            $order   = wc_get_order( $order_id );
            $user_id = $order->get_user_id();

            $member = YITH_WCMBS_Members()->get_member( $user_id );

            foreach ( $order->get_items() as $order_item_id => $item ) {
                $product_id = $item[ 'product_id' ];
                $id         = !empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $product_id;
                /*
                 * if this product is subscription, no actions!
                 * Subscription plugin will manage the membership activation
                 */
                if ( YITH_WCMBS_Compatibility::has_plugin( 'subscription' ) ) {
                    if ( YITH_WC_Subscription()->is_subscription( $id ) ) {
                        continue;
                    }
                }

                if ( !apply_filters( 'yith_wcmbs_create_membership', true, $id, $order, $plan_product_ids ) ) {
                    continue;
                }

                $plans_to_activate = array();
                if ( in_array( $id, $plan_product_ids ) ) {
                    $plans_to_activate = $plans_product_array[ $id ];
                }

                if ( $product_id !== $id && in_array( $product_id, $plan_product_ids ) ) {
                    $plans_to_activate = array_merge( $plans_to_activate, $plans_product_array[ $product_id ] );
                }

                if ( $plans_to_activate ) {
                    foreach ( $plans_to_activate as $_plan ) {
                        $member->create_membership( $_plan->ID, $order_id, $order_item_id );
                    }
                }
            }
        }

    }
}
<?php

// Update YITH WooCommerce Membership to 1.0.1
if ( !function_exists( 'yith_wcmbs_update_to_1_0_1' ) ) {
    function yith_wcmbs_update_to_1_0_1() {
        $free = get_option( 'yith-wcmbs-membership-product', false ) || get_option( 'yith-wcmbs-membership-name', false );
        if ( !$free ) {
            $membership_option = get_option( 'yith_wcmbs_membership_version', '1.0.0' );
            if ( $membership_option && version_compare( $membership_option, '1.0.1', '<' ) ) {
                update_option( 'yith_wcmbs_membership_version', '1.0.1' );
            }

            return;
        }

        $membership_option = get_option( 'yith_wcmbs_membership_version', '1.0.0' );
        if ( $membership_option && version_compare( $membership_option, '1.0.1', '<' ) ) {
            $users = get_users( array(
                'meta_key'     => 'yith_wcmbs_membership_plans',
                'meta_value'   => ' ',
                'meta_compare' => '!=',
                'fields'       => 'ids'
            ) );
            if ( !empty( $users ) ) {
                foreach ( $users as $user_id ) {
                    $plans = get_user_meta( $user_id, 'yith_wcmbs_membership_plans', true );
                    if ( !empty( $plans ) ) {
                        foreach ( $plans as $plan ) {
                            $start_date = isset( $plan->start_date ) ? strtotime( $plan->start_date ) : time();
                            $order_id   = isset( $plan->order_id ) ? $plan->order_id : 0;

                            $membership_meta_data = array(
                                'plan_id'    => 0,
                                'title'      => get_option( 'yith-wcmbs-membership-name', _x( 'Membership', 'Default value for Membership Plan Name', 'yith-woocommerce-membership' ) ),
                                'start_date' => $start_date,
                                'end_date'   => 'unlimited',
                                'order_id'   => $order_id,
                                'user_id'    => $user_id,
                                'status'     => 'active',
                            );
                            // create the user membership
                            $membership = new YITH_WCMBS_Membership( 0, $membership_meta_data );
                        }
                        delete_user_meta( $user_id, 'yith_wcmbs_membership_plans' );
                    }
                }
            }
            update_option( 'yith_wcmbs_membership_version', '1.0.1' );
        }
    }
}

register_activation_hook( YITH_WCMBS_FILE, 'yith_wcmbs_update_to_1_0_1' );
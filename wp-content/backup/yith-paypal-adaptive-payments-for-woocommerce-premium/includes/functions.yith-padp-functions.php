<?php

if( !function_exists( 'yith_paypal_adaptive_payments_receivers_get_endpoint' ) ) {
    function yith_paypal_adaptive_payments_receivers_get_endpoint()
    {
        $endpoint = get_option( 'ywpadp_receiver_endpoint' );

        return apply_filters( 'yith_paypal_adaptive_payments_receiver_endpoint', $endpoint );
    }
}

if( !function_exists( 'yith_paypal_adaptive_payments_get_account_commission_columns' ) ) {

    function yith_paypal_adaptive_payments_get_account_commission_columns()
    {
        $columns = array(
            'order_id' => __( 'Order', 'yith-paypal-adaptive-payments-for-woocommerce' ),
            'transaction_value' => __( 'Commission', 'yith-paypal-adaptive-payments-for-woocommerce' ),
            'transaction_status' => __( 'Commission Status', 'yith-paypal-adaptive-payments-for-woocommerce' ),
            'transaction_date' => __( 'Date', 'yith-paypal-adaptive-payments-for-woocommerce' )
        );

        return apply_filters( 'paypal_adaptive_payments_commission_columns', $columns );
    }

}

if( !function_exists( 'yith_get_user_by_meta' ) ) {

    /**
     * @param string $meta_key
     * @param $meta_value
     * @return WP_User|bool
     */
    function yith_get_user_by_meta( $meta_key, $meta_value ){

        $user_query = new WP_User_Query(
            array(
                'meta_key' => $meta_key,
                'meta_value' => $meta_value
            )
        );
        
        $user = $user_query->get_results();

        
        return !empty( $user ) ? $user[0] : false;
    }
}

/**
 * Database Table Check
 */

//Check if Commission tables are created
function yith_paypal_adaptive_payments_check_commissions_table() {
    $create_commissions_table = get_option( 'yith_product_vendors_commissions_table_created' );
    if ( ! $create_commissions_table ) {
        /**
         * Create new Commissions DB table
         */
        YITH_PADP_Receiver_Commission::install();
    }
}

add_action( 'admin_init', 'yith_paypal_adaptive_payments_check_commissions_table' );
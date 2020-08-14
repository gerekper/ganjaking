<?php
if( !defined('ABSPATH') ){
    exit;
}

$settings = array(
    'receiver-endpoint-settings' => array(
        'receiver_endpoint_section_start' => array(
            'name' => __('Endpoint Settings', 'yith-paypal-adaptive-payments-for-woocommerce'),
            'type' => 'title',

        ),
       'receiver_endpoint'  => array(
           'name' => __('Receiver Endpoint', 'yith-paypal-adaptive-payments' ),
           'type' => 'text',
           'id' => 'ywpadp_receiver_endpoint',
           'default' => 'receiver-commissions',
           'desc'   => __('It will display all your receivers\' commissions', 'yith-paypal-adaptive-payments-for-woocommerce' )
       ),
        'receiver_endpoint_end' => array(
            'type'=> 'sectionend'
        )
    )
);

return apply_filters( 'yith_padp_receiver_endpoint_settings', $settings );
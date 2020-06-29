<?php

if( !defined( 'ABSPATH' ) ) {
    exit;
}


$form_fields = array(
    'gateway-settings' => array(
        'gateway_fields' => array(
            'type' => 'custom_tab',
            'action' => 'yith_paypal_adaptive_payments_gateway_settings_tab'
    )
    )

);

return $form_fields;


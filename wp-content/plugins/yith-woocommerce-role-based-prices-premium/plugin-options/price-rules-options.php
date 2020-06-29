<?php
if( !defined( 'ABSPATH' ) )
    exit;

$settings = array(
    'price-rules' => array(
        'price_rules' => array(
            'type'   => 'custom_tab',
            'action' => 'ywcrbp_price_rules'
        )
    )
);

return apply_filters( 'yith_wcrbp_price_rule_option', $settings );
<?php
if( !defined( 'ABSPATH' ) )
    exit;

$settings = array(
    'pending-survey-order-recovered' => array(
        'pending_survey_order_recovered' => array(
            'type'   => 'custom_tab',
            'action' => 'yith_ywcpos_pending_survey_order_recovered'
        )
    )
);

return apply_filters( 'yith_wc_pending_order_survey_list_pending_order_recovered', $settings );
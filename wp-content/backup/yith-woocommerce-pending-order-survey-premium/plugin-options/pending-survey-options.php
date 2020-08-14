<?php
if( !defined( 'ABSPATH' ) )
    exit;

$settings = array(
    'pending-survey' => array(
        'pending_survey' => array(
            'type'   => 'custom_tab',
            'action' => 'yith_ywcpos_pending_survey'
        )
    )
);

return apply_filters( 'yith_wc_pending_order_survey_list_survey', $settings );
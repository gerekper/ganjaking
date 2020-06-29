<?php
if( !defined( 'ABSPATH' ) )
    exit;

$settings = array(
    'pending-survey-order' => array(
        'pending_survey_order' => array(
            'type'   => 'custom_tab',
            'action' => 'yith_ywcpos_pending_survey_order'
        )
    )
);

return apply_filters( 'yith_wc_pending_order_survey_list_pending_order', $settings );
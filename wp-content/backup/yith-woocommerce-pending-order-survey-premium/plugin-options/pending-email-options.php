<?php
if( !defined( 'ABSPATH' ) )
    exit;

$settings = array(
    'pending-email' => array(
        'pending_email' => array(
            'type'   => 'custom_tab',
            'action' => 'yith_ywcpos_pending_survey_email'
        )
    )
);

return apply_filters( 'yith_wc_pending_order_survey_list_email', $settings );
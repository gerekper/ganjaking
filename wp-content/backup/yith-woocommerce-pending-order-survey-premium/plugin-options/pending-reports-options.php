<?php
if( !defined( 'ABSPATH' ) )
    exit;

$settings = array(
    'pending-reports' => array(
        'pending_reports' => array(
            'type'   => 'custom_tab',
            'action' => 'yith_ywcpos_pending_survey_report'
        )
    )
);

return apply_filters( 'yith_wc_pending_order_survey_report', $settings );
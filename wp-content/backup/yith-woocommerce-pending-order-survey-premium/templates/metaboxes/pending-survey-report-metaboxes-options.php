<?php
if( !defined( 'ABSPATH' ) )
    exit;

$args   =   array(

    'label'    =>   __( 'Pending Order Survey Reports', 'yith-woocommerce-pending-order-survey' ),
    'pages'    =>   array( 'ywcpos_survey' ),
    'context'  =>   'normal',
    'priority' =>   'default',
    'class' => yith_set_wrapper_class(),
    'tabs'     =>   array(

        'settings_report'  =>  array(

            'label'     =>  __( 'Reports', 'yith-woocommerce-pending-order-survey' ),
            'fields'    =>  array(
                'yith_pending_survey_report' => array(
                    'label' => __( 'Survey Questions', 'yith-woocommerce-pending-order-survey' ),
                    'desc'  => '',
                    'type'  => 'pending_survey_report'
                ),
            )
        )
    )
);

return apply_filters( 'yith_wc_pending_survey_report_metaboxes', $args );

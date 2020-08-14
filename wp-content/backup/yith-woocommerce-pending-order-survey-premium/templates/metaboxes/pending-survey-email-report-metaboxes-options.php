<?php
if( !defined( 'ABSPATH' ) )
    exit;

$args   =   array(

    'label'    =>   __( 'Email report', 'yith-woocommerce-pending-order-survey' ),
    'pages'    =>    'ywcpos_survey_email' ,
    'context'  =>   'normal',
    'priority' =>   'default',
    'class' => yith_set_wrapper_class(),
    'tabs'     =>   array(

        'settings'  =>  array(

            'label'     =>  __( 'Reports', 'yith-woocommerce-pending-order-survey' ),
            'fields'    =>  array(
                'yith_pending_survey_email_report' => array(
                    'label' => '',
                    'desc'  => '',
                    'type'  => 'pending_survey_email_report'
                ),
            )
        )
    )
);

return apply_filters( 'yith_wc_pending_survey_email_report_metaboxes', $args );

<?php
if( !defined( 'ABSPATH' ) )
    exit;

$args   =   array(

    'label'    =>   __( 'Pending Order Survey Options', 'yith-woocommerce-pending-order-survey' ),
    'pages'    =>   array( 'ywcpos_survey' ),
    'context'  =>   'normal',
    'priority' =>   'default',
    'class' => yith_set_wrapper_class(),
    'tabs'     =>   array(

        'settings'  =>  array(

            'label'     =>  __( 'Settings', 'yith-woocommerce-pending-order-survey' ),
            'fields'    =>  array(
                'yith_pending_survey_question' => array(
                    'label' => __( 'Survey Questions', 'yith-woocommerce-pending-order-survey' ),
                    'desc'  => '',
                    'type'  => 'pending_survey_type'
                ),
            )
        )
    )
);

return apply_filters( 'yith_wc_pending_survey_metaboxes', $args );

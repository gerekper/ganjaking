<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


$args = array(
    'label'    => __( 'Email settings', 'yith-woocommerce-pending-order-survey' ),
    'pages'    => 'ywcpos_survey_email', //or array( 'post-type1', 'post-type2')
    'context'  => 'normal', //('normal', 'advanced', or 'side')
    'priority' => 'default',
    'class' => yith_set_wrapper_class(),
    'tabs'     => array(
        'emails' => array(
            'label'  => __( 'Settings', 'yith-woocommerce-pending-order-survey' ),
            'fields' => apply_filters( 'ywcpos_email_metabox_settings', array(
                    'ywcpos_email_subject' => array(
                       'label'  => __('Subject', 'yith-woocommerce-pending-order-survey' ),
                        'type' => 'text',
                        'desc'  => __('Subject email', 'yith-woocommerce-pending-order-survey' ),
                        'std'   => ''
                    ) ,
                    'ywcpos_enable_email' => array(
                        'label' => __( 'Enable/disable', 'yith-woocommerce-pending-order-survey' ),
                        'desc'  => '',
                        'type'  => 'checkbox',
                        'std'   => 1 ),

                    'ywcpos_send_after' => array(
                        'label' => __( 'Send after', 'yith-woocommerce-pending-order-survey' ),
                        'desc'  => __('minutes', 'yith-woocommerce-pending-order-survey' ),
                        'type'  => 'text',
                        'std'   => '15' ),
                    )
            )
        )
    )
);


return $args;
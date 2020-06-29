<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


$args = array(
    'label'    => __( 'Coupon settings', 'yith-woocommerce-pending-order-survey' ),
    'pages'    => 'ywcpos_survey_email', //or array( 'post-type1', 'post-type2')
    'context'  => 'normal', //('normal', 'advanced', or 'side')
    'priority' => 'default',
    'class' => yith_set_wrapper_class(),
    'tabs'     => array(
        'coupons' => array(
            'label'  => __( 'Coupons', 'yith-woocommerce-pending-order-survey' ),
            'fields' => apply_filters( 'ywcpos_email_metabox_coupons', array(
                    'ywcpos_coupon_value' => array(
                        'label' => __( 'Coupon value', 'yith-woocommerce-pending-order-survey' ),
                        'desc'  => '',
                        'type'  => 'text',
                        'std'   => '' ),

                    'ywcpos_coupon_type' => array(
                        'label' => __( 'Coupon type', 'yith-woocommerce-pending-order-survey' ),
                        'desc'  => '',
                        'type'  => 'select',
                        'options' => array(
                            'percent' => __( 'Percentage', 'yith-woocommerce-pending-order-survey' ),
                            'fixed_cart'   => __( 'Amount', 'yith-woocommerce-pending-order-survey' ),
                        ),
                        'std'   => 'percent' ),

                    'ywcpos_coupon_validity' => array(
                        'label' => __( 'Validity in days', 'yith-woocommerce-pending-order-survey' ),
                        'desc'  => '',
                        'type'  => 'text',
                        'std'   => '7' ),

                )

            )
        )
    )
);


return $args;
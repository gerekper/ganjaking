<?php
// Exit if accessed directly
!defined( 'YITH_WCMBS' ) && exit();

$tab = array(
    'settings' => array(
        'plan-options'       => array(
            'title' => __( 'Plan Options', 'yith-woocommerce-membership' ),
            'type'  => 'title',
            'desc'  => '',
            'id'    => 'yith-wcmbs-plan-options'
        ),
        'membership-name'    => array(
            'name'              => __( 'Membership Name', 'yith-woocommerce-membership' ),
            'type'              => 'text',
            'desc'              => __( 'Write the name of the membership plan.', 'yith-woocommerce-membership' ),
            'id'                => 'yith-wcmbs-membership-name',
            'default'           => _x( 'Membership', 'Default value for Membership Plan Name', 'yith-woocommerce-membership' ),
            'custom_attributes' => array(
                'min' => 0
            )
        ),
        'membership-product' => array(
            'id'       => 'yith-wcmbs-membership-product',
            'name'     => __( 'Select Product', 'yith-woocommerce-membership' ),
            'type'     => 'yith-wcmbs-ajax-products',
            'class'    => 'wc-product-search',
            'desc'     => __( 'Select the product that users have to purchase to get a membership access', 'yith-woocommerce-membership' ),
            'multiple' => false,
            'default'  => '',
        ),
        'plan-options-end'   => array(
            'type' => 'sectionend',
            'id'   => 'yith-wcmbs-plan-options'
        )
    )
);

return apply_filters( 'yith_wcmbs_panel_settings_options', $tab );
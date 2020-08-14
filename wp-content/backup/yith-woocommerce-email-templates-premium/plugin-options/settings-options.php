<?php
$is_premium           = defined( 'YITH_WCET_PREMIUM' ) && YITH_WCET_PREMIUM;
$require_premium_text = '<i>' . __( 'This feature is available in the Premium version of YITH WooCommerce Email Templates', 'yith-woocommerce-email-templates' ) . '</i>';
$require_premium_text = $is_premium ? '' : $require_premium_text;
$free_disabled        = $is_premium ? array() : array( 'disabled' => 'disabled' );

$settings = array(

    'settings' => array(

        'general-options' => array(
            'title' => __( 'General Options', 'yith-woocommerce-email-templates' ),
            'type'  => 'title',
            'desc'  => '',
            'id'    => 'yith-wcet-general-options'
        ),

        'custom-default-header-logo' => array(
            'id'   => 'yith-wcet-custom-default-header-logo',
            'name' => __( 'Default Logo', 'yith-woocommerce-email-templates' ),
            'type' => 'yith_wcet_upload',
            'desc' => __( 'Upload your custom default logo', 'yith-woocommerce-email-templates' ),
        ),

        'use-mini-social-icons' => array(
            'id'                => 'yith-wcet-use-mini-social-icons',
            'name'              => __( 'Use social mini-icons', 'yith-woocommerce-email-templates' ),
            'type'              => 'checkbox',
            'desc'              => __( 'Use always social mini-icons (30x30 px) in all email templates.', 'yith-woocommerce-email-templates' ) . $require_premium_text,
            'default'           => 'no',
            'custom_attributes' => $free_disabled
        ),

        'general-options-end' => array(
            'type' => 'sectionend',
            'id'   => 'yith-wcet-general-options'
        )

    )
);

return apply_filters( 'yith_wcet_panel_settings_options', $settings );
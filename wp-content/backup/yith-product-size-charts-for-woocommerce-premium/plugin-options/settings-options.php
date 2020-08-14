<?php
// Exit if accessed directly

$custom_attributes = defined( 'YITH_WCPSC_PREMIUM' ) ? '' : array( 'disabled' => 'disabled' );
!defined( 'YITH_WCPSC' ) && exit();

$settings = array(
    'settings' => array(
        'popup-options'                    => array(
            'title' => __( 'Popup Options', 'yith-product-size-charts-for-woocommerce' ),
            'type'  => 'title',
            'desc'  => '',
            'id'    => 'yith-wcpsc-popup-options'
        ),
        'popup-style'                      => array(
            'name'              => __( 'Style', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'select',
            'desc'              => __( 'Select the style you want to apply to all popups', 'yith-product-size-charts-for-woocommerce' ),
            'id'                => 'yith-wcpsc-popup-style',
            'options'           => array(
                'default'  => __( 'Default', 'yith-product-size-charts-for-woocommerce' ),
                'informal' => __( 'Informal', 'yith-product-size-charts-for-woocommerce' ),
                'elegant'  => __( 'Elegant', 'yith-product-size-charts-for-woocommerce' ),
                'casual'   => __( 'Casual', 'yith-product-size-charts-for-woocommerce' ),
            ),
            'custom_attributes' => $custom_attributes,
            'default'           => 'default'
        ),
        'popup-base-color'                 => array(
            'name'              => __( 'Main Color', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'yith-field',
            'yith-type'         => 'colorpicker',
            'desc'              => __( 'Select the main color for popups', 'yith-product-size-charts-for-woocommerce' ),
            'id'                => 'yith-wcpsc-popup-base-color',
            'custom_attributes' => $custom_attributes,
            'default'           => '#ffffff'
        ),
        'popup-position'                   => array(
            'name'              => __( 'Position', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'select',
            'desc'              => __( 'Select the position you want to apply to all popups', 'yith-product-size-charts-for-woocommerce' ),
            'id'                => 'yith-wcpsc-popup-position',
            'options'           => array(
                'center'        => __( 'Center', 'yith-product-size-charts-for-woocommerce' ),
                'top-left'      => __( 'Top Left', 'yith-product-size-charts-for-woocommerce' ),
                'top-rigth'     => __( 'Top Right', 'yith-product-size-charts-for-woocommerce' ),
                'bottom-left'   => __( 'Bottom Left', 'yith-product-size-charts-for-woocommerce' ),
                'bottom-right'  => __( 'Bottom Right', 'yith-product-size-charts-for-woocommerce' ),
                'top-center'    => __( 'Top Center', 'yith-product-size-charts-for-woocommerce' ),
                'bottom-center' => __( 'Bottom Center', 'yith-product-size-charts-for-woocommerce' ),
            ),
            'custom_attributes' => $custom_attributes,
            'default'           => 'center'
        ),
        'popup-effect'                     => array(
            'name'              => __( 'Effect', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'select',
            'desc'              => __( 'Select the effect you want to apply to all popups', 'yith-product-size-charts-for-woocommerce' ),
            'id'                => 'yith-wcpsc-popup-effect',
            'options'           => array(
                'fade'    => __( 'Fade', 'yith-product-size-charts-for-woocommerce' ),
                'slide'   => __( 'Slide', 'yith-product-size-charts-for-woocommerce' ),
                'zoomIn'  => __( 'Zoom In', 'yith-product-size-charts-for-woocommerce' ),
                'zoomOut' => __( 'Zoom Out', 'yith-product-size-charts-for-woocommerce' ),
            ),
            'custom_attributes' => $custom_attributes,
            'default'           => 'fade'
        ),
        'popup-overlay-color'              => array(
            'name'              => __( 'Overlay Color', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'yith-field',
            'yith-type'         => 'colorpicker',
            'desc'              => __( 'Select the color you want to apply to popup overlay', 'yith-product-size-charts-for-woocommerce' ),
            'id'                => 'yith-wcpsc-popup-overlay-color',
            'custom_attributes' => $custom_attributes,
            'default'           => '#000000'
        ),
        'popup-overlay-opacity'            => array(
            'name'              => __( 'Overlay Opacity', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'number',
            'desc'              => __( 'Select the opacity you want to set for popup overlay', 'yith-product-size-charts-for-woocommerce' ),
            'id'                => 'yith-wcpsc-popup-overlay-opacity',
            'custom_attributes' => array(
                'min'  => 0,
                'max'  => 1,
                'step' => 0.1
            ),
            'default'           => 0.8
        ),
        'popup-options-end'                => array(
            'type' => 'sectionend',
            'id'   => 'yith-wcpsc-popup-options'
        ),
        'popup-button-options'             => array(
            'title' => __( 'Popup Button Options', 'yith-product-size-charts-for-woocommerce' ),
            'type'  => 'title',
            'desc'  => '',
            'id'    => 'yith-wcpsc-popup-button-options'
        ),
        'popup-button-position'            => array(
            'name'              => __( 'Button Position', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'select',
            'desc'              => __( 'Select the position you want to apply to buttons in all popups.', 'yith-product-size-charts-for-woocommerce' ),
            'id'                => 'yith-wcpsc-popup-button-position',
            'options'           => array(
                'before_summary'     => __( 'Before summary', 'yith-product-size-charts-for-woocommerce' ),
                'before_description' => __( 'Before description', 'yith-product-size-charts-for-woocommerce' ),
                'after_description'  => __( 'After description', 'yith-product-size-charts-for-woocommerce' ),
                'after_add_to_cart'  => __( 'After "Add to Cart" Button', 'yith-product-size-charts-for-woocommerce' ),
                'after_summary'      => __( 'After summary', 'yith-product-size-charts-for-woocommerce' ),
            ),
            'custom_attributes' => $custom_attributes,
            'default'           => 'after_add_to_cart'
        ),
        'popup-button-quick-view-position' => array(
            'name'              => __( 'Quick View Button Position', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'select',
            'desc'              => __( 'Select the position you want to apply to size chart buttons in quick view.', 'yith-product-size-charts-for-woocommerce' ) . __( '<b>This feature requires YITH WooCommerce Quick View Premium 1.1.5 or greater</b>', 'yith-product-size-charts-for-woocommerce' ),
            'id'                => 'yith-wcpsc-popup-button-quick-view-position',
            'options'           => array(
                'none'               => __( 'None', 'yith-product-size-charts-for-woocommerce' ),
                'before_summary'     => __( 'Before summary', 'yith-product-size-charts-for-woocommerce' ),
                'before_description' => __( 'Before description', 'yith-product-size-charts-for-woocommerce' ),
                'after_description'  => __( 'After description', 'yith-product-size-charts-for-woocommerce' ),
                'after_add_to_cart'  => __( 'After "Add to Cart" Button', 'yith-product-size-charts-for-woocommerce' ),
                'after_summary'      => __( 'After summary', 'yith-product-size-charts-for-woocommerce' ),
            ),
            'custom_attributes' => YITH_WCPSC_Compatibility::has_plugin( 'quick-view' ) ? $custom_attributes : array( 'disabled' => 'disabled' ),
            'default'           => 'none'
        ),
        'popup-button-color'               => array(
            'name'              => __( 'Button Color', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'yith-field',
            'yith-type'         => 'colorpicker',
            'desc'              => __( 'Select the color you want to apply to the popup button', 'yith-product-size-charts-for-woocommerce' ),
            'id'                => 'yith-wcpsc-popup-button-color',
            'custom_attributes' => $custom_attributes,
            'default'           => '#b369a5'
        ),
        'popup-button-text-color'          => array(
            'name'              => __( 'Button Text Color', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'yith-field',
            'yith-type'         => 'colorpicker',
            'desc'              => __( 'Select the color you want to apply to popup button text', 'yith-product-size-charts-for-woocommerce' ),
            'id'                => 'yith-wcpsc-popup-button-text-color',
            'custom_attributes' => $custom_attributes,
            'default'           => '#ffffff'
        ),
        'popup-button-border-radius'       => array(
            'name'              => __( 'Border Radius', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'number',
            'desc'              => __( 'Select the border radius for popup', 'yith-product-size-charts-for-woocommerce' ),
            'id'                => 'yith-wcpsc-popup-button-border-radius',
            'custom_attributes' => array(
                'min' => 0,
            ),
            'default'           => 3
        ),
        'popup-button-padding'             => array(
            'name'              => __( 'Padding', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'multiinput',
            'id'                => 'yith-wcpsc-popup-button-padding',
            'custom_attributes' => array(
                'min' => 0,
            ),
            'options'           => array(
                'input_type' => 'number',
                'fields'     => array(
                    __( 'Top', 'yith-product-size-charts-for-woocommerce' ),
                    __( 'Right', 'yith-product-size-charts-for-woocommerce' ),
                    __( 'Bottom', 'yith-product-size-charts-for-woocommerce' ),
                    __( 'Left', 'yith-product-size-charts-for-woocommerce' )
                )
            ),
            'default'           => array( 10, 20, 10, 20 ),
        ),
        'popup-button-shadow-color'        => array(
            'name'              => __( 'Button Shadow Color', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'yith-field',
            'yith-type'         => 'colorpicker',
            'desc'              => __( 'Select the color you want to apply to popup button shadow', 'yith-product-size-charts-for-woocommerce' ),
            'id'                => 'yith-wcpsc-popup-button-shadow-color',
            'custom_attributes' => $custom_attributes,
            'default'           => '#dddddd'
        ),
        'popup-button-options-end'         => array(
            'type' => 'sectionend',
            'id'   => 'yith-wcpsc-popup-button-options'
        ),
        'table-options'                    => array(
            'title' => __( 'Table Options', 'yith-product-size-charts-for-woocommerce' ),
            'type'  => 'title',
            'desc'  => '',
            'id'    => 'yith-wcpsc-table-options'
        ),
        'table-style'                      => array(
            'name'              => __( 'Style', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'select',
            'desc'              => __( 'Select the style you want to apply to all tables', 'yith-product-size-charts-for-woocommerce' ),
            'id'                => 'yith-wcpsc-table-style',
            'options'           => array(
                'default'  => __( 'Default', 'yith-product-size-charts-for-woocommerce' ),
                'informal' => __( 'Informal', 'yith-product-size-charts-for-woocommerce' ),
                'elegant'  => __( 'Elegant', 'yith-product-size-charts-for-woocommerce' ),
                'casual'   => __( 'Casual', 'yith-product-size-charts-for-woocommerce' ),
            ),
            'custom_attributes' => $custom_attributes,
            'default'           => 'default'
        ),
        'table-base-color'                 => array(
            'name'              => __( 'Main Color', 'yith-product-size-charts-for-woocommerce' ),
            'type'              => 'yith-field',
            'yith-type'         => 'colorpicker',
            'desc'              => __( 'Select the main color for tables', 'yith-product-size-charts-for-woocommerce' ),
            'id'                => 'yith-wcpsc-table-base-color',
            'custom_attributes' => $custom_attributes,
            'default'           => '#f9f9f9'
        ),
        'table-style-end'                  => array(
            'type' => 'sectionend',
            'id'   => 'yith-wcpsc-table-style'
        ),
    )
);

return apply_filters( 'yith_wcpsc_panel_settings_options', $settings );
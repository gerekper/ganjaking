<?php
/**
 *  Style Tab
 *  Last update: Version 4.0.0
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

$general_style = array(

    // Block options.

	'block-section'               => array(
		'id'    => 'yith_wapo_style_block',
        // translators: [ADMIN] Style tab option
		'title' => __( 'Block style', 'yith-woocommerce-product-add-ons' ),
		'type'  => 'title',
		'desc'  => '',
	),

	'style-addon-titles'          => array(
		'id'      => 'yith_wapo_style_addon_titles',
        // translators: [ADMIN] Style tab option
        'name'    => __( 'Block titles', 'yith-woocommerce-product-add-ons' ),
        // translators: [ADMIN] Style tab option (description)
        'desc'    => __( 'Choose which heading to use for the titles in the block of options.', 'yith-woocommerce-product-add-ons' ),
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'default' => 'h3',
		'options' => array(
			'h1' => 'H1',
			'h2' => 'H2',
			'h3' => 'H3',
			'h4' => 'H4',
			'h5' => 'H5',
			'h6' => 'H6',
		),
	),

	'style-addon-background'      => array(
		'id'           => 'yith_wapo_style_addon_background',
        // translators: [ADMIN] Style tab option
        'name'         => __( 'Block background', 'yith-woocommerce-product-add-ons' ),
        // translators: [ADMIN] Style tab option
        'desc'         => __( 'Set the background color for all block options.', 'yith-woocommerce-product-add-ons' ),
		'type'         => 'yith-field',
		'yith-type'    => 'multi-colorpicker',
		'colorpickers' => array(
			array(
				'name'          => '',
				'id'            => 'color',
				'default'       => '#ffffff',
				'alpha_enabled' => false,
			),
		),
	),

	'style-addon-padding'         => array(
		'id'        => 'yith_wapo_style_addon_padding',
        // translators: [ADMIN] Style tab option
        'name'      => __( 'Block padding', 'yith-woocommerce-product-add-ons' ) . ' (px)',
        // translators: [ADMIN] Style tab option
        'desc'      => __( 'Set the padding for the content in all block options.', 'yith-woocommerce-product-add-ons' ),
		'type'      => 'yith-field',
		'yith-type' => 'dimensions',
		'default'   => array(
			'dimensions' => array(
				'top'    => 0,
				'right'  => 0,
				'bottom' => 0,
				'left'   => 0,
			),
			'unit'       => 'px',
			'linked'     => 'no',
		),
		'units'     => array(),
	),

    'style-section-end'           => array(
        'id'   => 'yith_wapo_style_block_end',
        'type' => 'sectionend',
    ),

    // Form options.

    'form-section'            => array(
        'id'    => 'yith_wapo_style_form_options',
        // translators: [ADMIN] Style tab option
        'title' => __( 'Form style', 'yith-woocommerce-product-add-ons' ),
        'type'  => 'title',
        'desc'  => '',
    ),

	'style-form-style'            => array(
		'id'        => 'yith_wapo_style_form_style',
        // translators: [ADMIN] Style tab option
        'name'      => __( 'Form style', 'yith-woocommerce-product-add-ons' ),
        // translators: [ADMIN] Style tab option
        'desc'      => __( 'Choose the general style for form: checkbox, radio, select, input field, textarea, etc.', 'yith-woocommerce-product-add-ons' ),
		'type'      => 'yith-field',
		'yith-type' => 'radio',
		'default'   => 'theme',
		'options'   => array(
            // translators: [ADMIN] Style tab option
            'theme'  => __( 'Theme style', 'yith-woocommerce-product-add-ons' ),
            // translators: [ADMIN] Style tab option
            'custom' => __( 'Custom style', 'yith-woocommerce-product-add-ons' ),
		),
	),

	'style-checkbox-style'        => array(
		'id'        => 'yith_wapo_style_checkbox_style',
        // translators: [ADMIN] Style tab option
        'name'      => __( 'Checkbox style', 'yith-woocommerce-product-add-ons' ),
        // translators: [ADMIN] Style tab option
        'desc'      => __( 'Choose the style for the checkbox.', 'yith-woocommerce-product-add-ons' ),
		'type'      => 'yith-field',
		'yith-type' => 'radio',
		'default'   => 'rounded',
		'options'   => array(
            // translators: [ADMIN] Style tab option
            'rounded' => __( 'Rounded', 'yith-woocommerce-product-add-ons' ),
            // translators: [ADMIN] Style tab option
            'square'  => __( 'Square', 'yith-woocommerce-product-add-ons' ),
		),
		'deps'      => array(
			'id'    => 'yith_wapo_style_form_style',
			'value' => 'custom',
			'type'  => 'fade',
		),
	),

	'style-accent-color'          => array(
		'id'           => 'yith_wapo_style_accent_color',
        // translators: [ADMIN] Style tab option
        'name'         => __( 'Accent color', 'yith-woocommerce-product-add-ons' ),
        // translators: [ADMIN] Style tab option
        'desc'         => __( 'Set the accent color to use for the selected options.', 'yith-woocommerce-product-add-ons' ),
		'type'         => 'yith-field',
		'yith-type'    => 'multi-colorpicker',
		'colorpickers' => array(
			array(
				'name'          => '',
				'id'            => 'color',
				'default'       => '#03bfac',
				'alpha_enabled' => false,
			),
		),
		'deps'         => array(
			'id'    => 'yith_wapo_style_form_style',
			'value' => 'custom',
			'type'  => 'fade',
		),
	),

	'style-borders-color'         => array(
		'id'           => 'yith_wapo_style_borders_color',
        // translators: [ADMIN] Style tab option
        'name'         => __( 'Form border-color', 'yith-woocommerce-product-add-ons' ),
        // translators: [ADMIN] Style tab option
        'desc'         => __( 'Set the color of the form borders.', 'yith-woocommerce-product-add-ons' ),
		'type'         => 'yith-field',
		'yith-type'    => 'multi-colorpicker',
		'colorpickers' => array(
			array(
				'name'          => '',
				'id'            => 'color',
				'default'       => '#7a7a7a',
				'alpha_enabled' => false,
			),
		),
		'deps'         => array(
			'id'    => 'yith_wapo_style_form_style',
			'value' => 'custom',
			'type'  => 'fade',
		),
	),

	'style-label-font-size'       => array(
		'id'        => 'yith_wapo_style_label_font_size',
        // translators: [ADMIN] Style tab option
        'name'      => __( 'Label font size', 'yith-woocommerce-product-add-ons' ) . ' (px)',
        // translators: [ADMIN] Style tab option
        'desc'      => __( 'Set the label font size in pixel.', 'yith-woocommerce-product-add-ons' ),
		'type'      => 'yith-field',
		'yith-type' => 'number',
		'default'   => '16',
		'deps'      => array(
			'id'    => 'yith_wapo_style_form_style',
			'value' => 'custom',
			'type'  => 'fade',
		),
	),

	'style-description-font-size' => array(
		'id'        => 'yith_wapo_style_description_font_size',
        // translators: [ADMIN] Style tab option
        'name'      => __( 'Description font size', 'yith-woocommerce-product-add-ons' ) . ' (px)',
        // translators: [ADMIN] Style tab option
        'desc'      => __( 'Set the description font size in pixel.', 'yith-woocommerce-product-add-ons' ),
		'type'      => 'yith-field',
		'yith-type' => 'number',
		'default'   => '12',
		'deps'      => array(
			'id'    => 'yith_wapo_style_form_style',
			'value' => 'custom',
			'type'  => 'fade',
		),
	),

    'form-section-end'        => array(
        'id'   => 'yith_wapo_style_form_options',
        'type' => 'sectionend',
    ),
);

$style = array( 'style' => $general_style );

return apply_filters( 'yith_wapo_panel_style_options', $style );

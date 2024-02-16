<?php
/**
 * GENERAL ARRAY OPTIONS
 *
 * @since 1.0.0
 * @author Francesco Licandro
 * @package YITH WooCommerce Color and Label Variations Premium
 */

defined( 'YITH_WCCL' ) || exit; // Exit if accessed directly.

$general = array(

	'general' => array(

		array(
			'title' => __( 'General Options', 'yith-woocommerce-color-label-variations' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wccl-general-options',
		),

		array(
			'title'     => __( 'Attribute behavior', 'yith-woocommerce-color-label-variations' ),
			'desc'      => __( 'Choose attribute style after selection.', 'yith-woocommerce-color-label-variations' ),
			'id'        => 'yith-wccl-attributes-style',
			'default'   => 'hide',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'hide' => __( 'Hide attributes', 'yith-woocommerce-color-label-variations' ),
				'grey' => __( 'Blur attributes', 'yith-woocommerce-color-label-variations' ),
			),
		),

		array(
			'id'        => 'yith-wccl-enable-tooltip',
			'title'     => __( 'Enable Tooltip', 'yith-woocommerce-color-label-variations' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable tooltip for attributes', 'yith-woocommerce-color-label-variations' ),
			'default'   => 'yes',
		),

		array(
			'id'        => 'yith-wccl-tooltip-position',
			'title'     => __( 'Tooltip position', 'yith-woocommerce-color-label-variations' ),
			'desc'      => __( 'Select tooltip position', 'yith-woocommerce-color-label-variations' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'top'    => __( 'Top', 'yith-woocommerce-color-label-variations' ),
				'bottom' => __( 'Bottom', 'yith-woocommerce-color-label-variations' ),
			),
			'default'   => 'top',
		),

		array(
			'id'        => 'yith-wccl-tooltip-animation',
			'title'     => __( 'Tooltip animation', 'yith-woocommerce-color-label-variations' ),
			'desc'      => __( 'Select tooltip animation', 'yith-woocommerce-color-label-variations' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'fade'  => __( 'Fade in', 'yith-woocommerce-color-label-variations' ),
				'slide' => __( 'Slide in', 'yith-woocommerce-color-label-variations' ),
			),
			'default'   => 'fade',
		),

		array(
			'id'        => 'yith-wccl-tooltip-background',
			'title'     => __( 'Tooltip background', 'yith-woocommerce-color-label-variations' ),
			'desc'      => __( 'Select tooltip background', 'yith-woocommerce-color-label-variations' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#222222',
		),

		array(
			'id'        => 'yith-wccl-tooltip-text-color',
			'title'     => __( 'Tooltip text color', 'yith-woocommerce-color-label-variations' ),
			'desc'      => __( 'Select tooltip text color', 'yith-woocommerce-color-label-variations' ),
			'type'      => 'yith-field',
			'yith-type' => 'colorpicker',
			'default'   => '#ffffff',
		),

		array(
			'id'        => 'yith-wccl-enable-description',
			'title'     => __( 'Show Attribute Description', 'yith-woocommerce-color-label-variations' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Choose to show description below each attribute in single product page', 'yith-woocommerce-color-label-variations' ),
			'default'   => 'yes',
		),

		array(
			'id'        => 'yith-wccl-enable-in-loop',
			'title'     => __( 'Enable plugin in archive pages', 'yith-woocommerce-color-label-variations' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Choose to show attribute selection in archive shop pages.', 'yith-woocommerce-color-label-variations' ),
			'default'   => 'yes',
		),

		array(
			'id'        => 'yith-wccl-position-in-loop',
			'title'     => __( 'Form Position', 'yith-woocommerce-color-label-variations' ),
			'desc'      => __( 'Choose the form position in archive shop page', 'yith-woocommerce-color-label-variations' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'before' => __( 'Before add to cart button', 'yith-woocommerce-color-label-variations' ),
				'after'  => __( 'After add to cart button', 'yith-woocommerce-color-label-variations' ),
			),
			'default'   => 'after',
		),

		array(
			'id'        => 'yith-wccl-ajax-in-loop',
			'title'     => __( 'Enable AJAX form in archive pages', 'yith-woocommerce-color-label-variations' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable AJAX handle for variations form in archive shop pages.', 'yith-woocommerce-color-label-variations' ),
			'default'   => 'no',
		),

		array(
			'id'        => 'yith-wccl-add-to-cart-label',
			'title'     => __( 'Label for \'Add to cart\' button', 'yith-woocommerce-color-label-variations' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => __( 'Label for \'Add to cart\' button when a variation is selected from archive page', 'yith-woocommerce-color-label-variations' ),
			'default'   => __( 'Add to cart', 'yith-woocommerce-color-label-variations' ),
		),

		array(
			'id'        => 'yith-wccl-change-image-hover',
			'title'     => __( 'Change product image on hover', 'yith-woocommerce-color-label-variations' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Change the product image when the mouse hovers the concerned attribute. PLEASE, NOTE: It works only for products that have only one attribute per variation.', 'yith-woocommerce-color-label-variations' ),
			'default'   => 'no',
		),

		array(
			'id'        => 'yith-wccl-show-custom-on-tab',
			'title'     => __( 'Show custom attributes on "Additional Information" Tab', 'yith-woocommerce-color-label-variations' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Show custom attributes style on "Additional Information" Tab instead of simple text.', 'yith-woocommerce-color-label-variations' ),
			'default'   => 'no',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-wccl-general-options',
		),
	),
);

return apply_filters( 'yith_wccl_panel_general_options', $general );

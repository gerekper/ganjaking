<?php

/**
 * GENERAL ARRAY OPTIONS
 */

$variations = array(

	'variations'  => array(

		array(
			'title' => __( 'Manage variations', 'yith-woocommerce-product-add-ons' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith-wccl-general-options'
		),

		array(
			'title'    => __( 'Attribute behavior', 'yith-wccl' ),
			'desc'     => __( 'Choose attribute style after selection.', 'yith-wccl' ),
			'id'       => 'yith-wccl-attributes-style',
			'default'  => 'hide',
			'type'     => 'radio',
			'options'  => array(
				'hide'  => __( 'Hide attributes', 'yith-wccl' ),
				'grey'  => __( 'Blur attributes', 'yith-wccl' )
			),
			'desc_tip' =>  true
		),

		array(
			'id'        => 'yith-wccl-enable-tooltip',
			'title'     => __( 'Enable tooltip', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'checkbox',
			'desc'      => __( 'Enable tooltip on attributes', 'yith-woocommerce-product-add-ons' ),
			'default'   => 'yes'
		),

		array(
			'id'        => 'yith-wccl-tooltip-position',
			'title'     => __( 'Tooltip position', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Select tooltip position', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'select',
			'options'   => array(
				'top'       => __( 'Top', 'yith-woocommerce-product-add-ons' ),
				'bottom'    => __( 'Bottom', 'yith-woocommerce-product-add-ons' )
			),
			'default'   => 'top'
		),

		array(
			'id'        => 'yith-wccl-tooltip-animation',
			'title'     => __( 'Tooltip animation', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Select tooltip animation', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'select',
			'options'   => array(
				'fade'     => __( 'Fade in', 'yith-woocommerce-product-add-ons' ),
				'slide'    => __( 'Slide in', 'yith-woocommerce-product-add-ons' )
			),
			'default'   => 'fade'
		),

		array(
			'id'        => 'yith-wccl-tooltip-background',
			'title'     => __( 'Tooltip background', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Pick a color', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'color',
			'default'   => '#222222'
		),

		array(
			'id'        => 'yith-wccl-tooltip-text-color',
			'title'     => __( 'Tooltip text color', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Pick a color', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'color',
			'default'   => '#ffffff'
		),

		array(
			'id'        => 'yith-wccl-enable-description',
			'title'     => __( 'Show attribute description', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'checkbox',
			'desc'      => __( 'Show description below attributes in single product page', 'yith-woocommerce-product-add-ons' ),
			'default'   => 'yes'
		),

		array(
			'id'        => 'yith-wccl-enable-in-loop',
			'title'     => __( 'Enable plugin in archive page', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'checkbox',
			'desc'      => __( 'Allow attribute selection in archive shop page', 'yith-woocommerce-product-add-ons' ),
			'default'   => 'yes'
		),

		array(
			'id'        => 'yith-wccl-position-in-loop',
			'title'     => __( 'Show add-ons', 'yith-woocommerce-product-add-ons' ),
			'desc'      => __( 'Show add-ons in archive shop page', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'select',
			'options'   => array(
				'before'    => __( 'Before \'Add to cart\' button', 'yith-woocommerce-product-add-ons' ),
				'after'    => __( 'After \'Add to cart\' button', 'yith-woocommerce-product-add-ons' )
			),
			'default'   => 'after'
		),

		array(
			'id'        => 'yith-wccl-add-to-cart-label',
			'title'     => __( '\'Add to cart\' button label', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'text',
			'desc'      => __( '\'Add to cart\' button label in archive page (for variable products only)', 'yith-woocommerce-product-add-ons' ),
			'default'   => __( 'Add to cart', 'yith-woocommerce-product-add-ons' )
		),

		array(
			'id'        => 'yith-wccl-change-image-hover',
			'title'     => __( 'Change product image on hover', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'checkbox',
			'desc'      => __( 'Change the product image when the mouse hovers the concerned attribute. PLEASE, NOTE: It works only for products that have only one attribute per variation.', 'yith-woocommerce-product-add-ons' ),
			'default'   => 'no'
		),

		array(
			'id'        => 'yith-wccl-show-custom-on-tab',
			'title'     => __( 'Show custom attributes on "Additional Information" Tab', 'yith-woocommerce-product-add-ons' ),
			'type'      => 'checkbox',
			'desc'      => __( 'Show custom attributes style on "Additional Information" Tab instead of simple text.', 'yith-woocommerce-product-add-ons' ),
			'default'   => 'no'
		),

		array(
			'type'      => 'sectionend',
			'id'        => 'yith-wccl-general-options'
		)
	)
);

return apply_filters( 'yith_wapo_panel_variations_options', $variations );

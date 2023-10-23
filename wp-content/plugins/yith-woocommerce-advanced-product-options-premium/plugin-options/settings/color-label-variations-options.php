<?php
/**
 * Color and Label Variations module options array
 *
 * @package YITH\ProductAddOns
 * @version 4.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

return apply_filters(
	'yith_wapo_color_label_variations_options',
	array(

		'settings-color-label-variations' => array(

			array(
				'title' => __( 'Manage variations', 'yith-woocommerce-product-add-ons' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'yith-wccl-general-options',
			),

			array(
				'title'     => __( 'Attribute behavior', 'yith-woocommerce-product-add-ons' ),
				'desc'      => __( 'Choose attribute style after selection.', 'yith-woocommerce-product-add-ons' ),
				'id'        => 'yith-wccl-attributes-style',
				'type'      => 'yith-field',
				'yith-type' => 'radio',
				'options'   => array(
					'hide' => __( 'Hide attributes', 'yith-woocommerce-product-add-ons' ),
					'grey' => __( 'Blur attributes', 'yith-woocommerce-product-add-ons' ),
				),
				'default'   => 'hide',
			),

			array(
				'id'        => 'yith-wccl-enable-tooltip',
				'title'     => __( 'Enable tooltip', 'yith-woocommerce-product-add-ons' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => __( 'Enable tooltip on attributes.', 'yith-woocommerce-product-add-ons' ),
				'default'   => 'yes',
			),

			array(
				'id'        => 'yith-wccl-tooltip-position',
				'title'     => __( 'Tooltip position', 'yith-woocommerce-product-add-ons' ),
				'desc'      => __( 'Select tooltip position.', 'yith-woocommerce-product-add-ons' ),
				'type'      => 'yith-field',
				'yith-type' => 'radio',
				'options'   => array(
					'top'    => __( 'Top', 'yith-woocommerce-product-add-ons' ),
					'bottom' => __( 'Bottom', 'yith-woocommerce-product-add-ons' ),
				),
				'default'   => 'top',
			),

			array(
				'id'        => 'yith-wccl-tooltip-animation',
				'title'     => __( 'Tooltip animation', 'yith-woocommerce-product-add-ons' ),
				'desc'      => __( 'Select tooltip animation.', 'yith-woocommerce-product-add-ons' ),
				'type'      => 'yith-field',
				'yith-type' => 'radio',
				'options'   => array(
					'fade'  => __( 'Fade in', 'yith-woocommerce-product-add-ons' ),
					'slide' => __( 'Slide in', 'yith-woocommerce-product-add-ons' ),
				),
				'default'   => 'fade',
			),

			array(
				'id'        => 'yith-wccl-enable-description',
				'title'     => __( 'Show attribute description', 'yith-woocommerce-product-add-ons' ),
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
				'desc'      => __( 'Show description below attributes in the single product page.', 'yith-woocommerce-product-add-ons' ),
				'default'   => 'yes',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'yith-wccl-general-options',
			),
		),
	)
);

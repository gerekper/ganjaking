<?php
/**
 * FAQ options tab
 *
 * @package YITH\FAQPluginForWordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	'customization-faq' => array(
		'home'     => array(
			array(
				'name' => esc_html__( 'FAQ style', 'yith-faq-plugin-for-wordpress' ),
				'type' => 'title',
			),
			array(
				'type' => 'close',
			),
		),
		'settings' => array(
			array(
				'name'    => esc_html__( 'FAQ layout', 'yith-faq-plugin-for-wordpress' ),
				'desc'    => esc_html__( 'Choose the FAQ design.', 'yith-faq-plugin-for-wordpress' ),
				'id'      => 'faq-layout',
				'type'    => 'select',
				'options' => array(
					'minimal' => esc_html__( 'Minimal', 'yith-faq-plugin-for-wordpress' ),
					'pill'    => esc_html__( 'Pill', 'yith-faq-plugin-for-wordpress' ),
				),
				'class'   => 'yfwp-select',
				'std'     => yfwp_get_default( 'faq-layout' ),
			),
			array(
				'name'         => esc_html__( 'FAQ colors', 'yith-faq-plugin-for-wordpress' ),
				'desc'         => '',
				'id'           => 'faq-colors',
				'type'         => 'multi-colorpicker',
				'colorpickers' => array(
					array(
						'id'      => 'background',
						'name'    => esc_html__( 'Background', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-colors', 'background' ),
					),
					array(
						'id'      => 'background-hover',
						'name'    => esc_html__( 'Background hover', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-colors', 'background-hover' ),
					),
					array(
						'id'      => 'background-active',
						'name'    => esc_html__( 'Background active', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-colors', 'background-active' ),
					),
					array(
						'id'      => 'border',
						'name'    => esc_html__( 'Border', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-colors', 'border' ),
					),
					array(
						'id'      => 'border-hover',
						'name'    => esc_html__( 'Border hover', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-colors', 'border-hover' ),
					),
					array(
						'id'      => 'border-active',
						'name'    => esc_html__( 'Border active', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-colors', 'border-active' ),
					),
					array(
						'id'      => 'text',
						'name'    => esc_html__( 'Text', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-colors', 'text' ),
					),
					array(
						'id'      => 'text-hover',
						'name'    => esc_html__( 'Text hover', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-colors', 'text-hover' ),
					),
					array(
						'id'      => 'text-active',
						'name'    => esc_html__( 'Text active', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-colors', 'text-active' ),
					),
					array(
						'id'      => 'content',
						'name'    => esc_html__( 'Content', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-colors', 'content' ),
					),
					array(
						'id'      => 'content-hover',
						'name'    => esc_html__( 'Content hover', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-colors', 'content-hover' ),
					),
					array(
						'id'      => 'content-active',
						'name'    => esc_html__( 'Content active', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-colors', 'content-active' ),
					),
				),
			),
			array(
				'id'    => 'faq-border',
				'name'  => esc_html__( 'Border radius', 'yith-faq-plugin-for-wordpress' ),
				'type'  => 'dimensions',
				'desc'  => esc_html__( 'Set the FAQ border radius.', 'yith-faq-plugin-for-wordpress' ),
				'units' => array( 'px' => 'px' ),
				'std'   => yfwp_get_default( 'faq-border' ),
				'deps'  => array(
					'ids'    => 'faq-layout',
					'values' => 'pill',
				),
			),
			array(
				'name' => esc_html__( 'Show "Copy FAQ" link', 'yith-faq-plugin-for-wordpress' ),
				'desc' => esc_html__( 'Enable to show a button to copy the FAQ link in each FAQ.', 'yith-faq-plugin-for-wordpress' ),
				'id'   => 'faq-copy-button',
				'type' => 'on-off',
				'std'  => yfwp_get_default( 'faq-copy-button' ),
			),
			array(
				'name'         => esc_html__( '"Copy FAQ" link colors', 'yith-faq-plugin-for-wordpress' ),
				'desc'         => '',
				'id'           => 'faq-copy-button-color',
				'type'         => 'multi-colorpicker',
				'colorpickers' => array(
					array(
						'id'      => 'background',
						'name'    => esc_html__( 'Background', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-copy-button-color', 'background' ),
					),
					array(
						'id'      => 'background-hover',
						'name'    => esc_html__( 'Background hover', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-copy-button-color', 'background-hover' ),
					),
					array(
						'id'      => 'icon',
						'name'    => esc_html__( 'Icon & Text', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-copy-button-color', 'icon' ),
					),
					array(
						'id'      => 'icon-hover',
						'name'    => esc_html__( 'Icon & Text hover', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-copy-button-color', 'icon-hover' ),
					),
					array(
						'id'      => 'border',
						'name'    => esc_html__( 'Border', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-copy-button-color', 'border' ),
					),
					array(
						'id'      => 'border-hover',
						'name'    => esc_html__( 'Border hover', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'faq-copy-button-color', 'border-hover' ),
					),
				),
				'deps'         => array(
					'ids'    => 'faq-copy-button',
					'values' => 'yes',
				),
			),
			array(
				'id'    => 'faq-copy-button-border',
				'name'  => esc_html__( 'Border radius', 'yith-faq-plugin-for-wordpress' ),
				'type'  => 'dimensions',
				'desc'  => esc_html__( 'Set the "Copy FAQ" button border radius.', 'yith-faq-plugin-for-wordpress' ),
				'units' => array( 'px' => 'px' ),
				'std'   => yfwp_get_default( 'faq-copy-button-border' ),
				'deps'  => array(
					'ids'    => 'faq-copy-button',
					'values' => 'yes',
				),
			),
			array(
				'name'    => esc_html__( 'AJAX loader', 'yith-faq-plugin-for-wordpress' ),
				'desc'    => esc_html__( 'Choose the AJAX loader to use.', 'yith-faq-plugin-for-wordpress' ),
				'id'      => 'faq-loader-type',
				'type'    => 'radio',
				'options' => array(
					'default' => esc_html__( 'Use default loader', 'yith-faq-plugin-for-wordpress' ),
					'custom'  => esc_html__( 'Upload custom loader', 'yith-faq-plugin-for-wordpress' ),
				),
				'std'     => yfwp_get_default( 'faq-loader-type' ),
			),
			array(
				'name'          => esc_html__( 'Loader color', 'yith-faq-plugin-for-wordpress' ),
				'desc'          => esc_html__( 'Choose the color for the AJAX loader.', 'yith-faq-plugin-for-wordpress' ),
				'id'            => 'faq-loader-color',
				'type'          => 'colorpicker',
				'alpha_enabled' => false,
				'std'           => yfwp_get_default( 'faq-loader-color' ),
				'deps'          => array(
					'ids'    => 'faq-loader-type',
					'values' => 'default',
				),
			),
			array(
				'name' => esc_html__( 'Upload a custom loader', 'yith-faq-plugin-for-wordpress' ),
				'desc' => esc_html__( 'Upload a custom loader.', 'yith-faq-plugin-for-wordpress' ),
				'id'   => 'faq-loader-custom',
				'type' => 'upload',
				'std'  => yfwp_get_default( 'faq-loader-custom' ),
				'deps' => array(
					'ids'    => 'faq-loader-type',
					'values' => 'custom',
				),
			),

		),
	),
);

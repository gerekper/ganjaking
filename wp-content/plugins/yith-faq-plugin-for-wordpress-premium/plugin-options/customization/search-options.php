<?php
/**
 * Search options tab
 *
 * @package YITH\FAQPluginForWordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	'customization-search' => array(
		'home'     => array(
			array(
				'name' => esc_html__( 'Search style', 'yith-faq-plugin-for-wordpress' ),
				'type' => 'title',
			),
			array(
				'type' => 'close',
			),
		),
		'settings' => array(
			array(
				'name'         => esc_html__( 'Search field', 'yith-faq-plugin-for-wordpress' ),
				'desc'         => '',
				'id'           => 'search-field',
				'type'         => 'multi-colorpicker',
				'colorpickers' => array(
					array(
						'id'      => 'background',
						'name'    => esc_html__( 'Background', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'search-field', 'background' ),
					),
					array(
						'id'      => 'background-active',
						'name'    => esc_html__( 'Background active', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'search-field', 'background-active' ),
					),
					array(
						'id'      => 'border',
						'name'    => esc_html__( 'Border', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'search-field', 'border' ),
					),
					array(
						'id'      => 'border-active',
						'name'    => esc_html__( 'Border active', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'search-field', 'border-active' ),
					),
					array(
						'id'      => 'placeholder-text',
						'name'    => esc_html__( 'Placeholder text', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'search-field', 'placeholder-text' ),
					),
					array(
						'id'      => 'active-text',
						'name'    => esc_html__( 'Active text', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'search-field', 'active-text' ),
					),
				),
			),
			array(
				'name'         => esc_html__( 'Search button', 'yith-faq-plugin-for-wordpress' ),
				'desc'         => '',
				'id'           => 'search-button',
				'type'         => 'multi-colorpicker',
				'colorpickers' => array(
					array(
						'id'      => 'background',
						'name'    => esc_html__( 'Background', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'search-button', 'background' ),
					),
					array(
						'id'      => 'background-hover',
						'name'    => esc_html__( 'Background hover', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'search-button', 'background-hover' ),
					),
					array(
						'id'      => 'icon',
						'name'    => esc_html__( 'Icon', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'search-button', 'icon' ),
					),
					array(
						'id'      => 'icon-hover',
						'name'    => esc_html__( 'Icon hover', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'search-button', 'icon-hover' ),
					),
				),
			),
		),
	),
);

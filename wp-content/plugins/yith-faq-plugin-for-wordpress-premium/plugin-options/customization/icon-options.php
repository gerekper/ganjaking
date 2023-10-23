<?php
/**
 * Icon options tab
 *
 * @package YITH\FAQPluginForWordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	'customization-icon' => array(
		'home'     => array(
			array(
				'name' => esc_html__( 'Icons style', 'yith-faq-plugin-for-wordpress' ),
				'type' => 'title',
			),
			array(
				'type' => 'close',
			),
		),
		'settings' => array(
			array(
				'name'         => esc_html__( 'Colors for FAQ icons', 'yith-faq-plugin-for-wordpress' ),
				'desc'         => '',
				'id'           => 'icon-colors',
				'type'         => 'multi-colorpicker',
				'colorpickers' => array(
					array(
						'id'      => 'background',
						'name'    => esc_html__( 'Background', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'icon-colors', 'background' ),
					),
					array(
						'id'      => 'background-hover',
						'name'    => esc_html__( 'Background hover', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'icon-colors', 'background-hover' ),
					),
					array(
						'id'      => 'background-active',
						'name'    => esc_html__( 'Background active', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'icon-colors', 'background-active' ),
					),
					array(
						'id'      => 'icon',
						'name'    => esc_html__( 'Icon', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'icon-colors', 'icon' ),
					),
					array(
						'id'      => 'icon-hover',
						'name'    => esc_html__( 'Icon hover', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'icon-colors', 'icon-hover' ),
					),
					array(
						'id'      => 'icon-active',
						'name'    => esc_html__( 'Icon active', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'icon-colors', 'icon-active' ),
					),
				),
			),
			array(
				'id'    => 'icon-border',
				'name'  => esc_html__( 'Border radius', 'yith-faq-plugin-for-wordpress' ),
				'type'  => 'dimensions',
				'desc'  => esc_html__( 'Set the icon border radius.', 'yith-faq-plugin-for-wordpress' ),
				'units' => array( 'px' => 'px' ),
				'std'   => yfwp_get_default( 'icon-border' ),
			),
		),
	),
);

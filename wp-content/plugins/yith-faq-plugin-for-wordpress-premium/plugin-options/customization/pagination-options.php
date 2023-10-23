<?php
/**
 * Pagination options tab
 *
 * @package YITH\FAQPluginForWordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	'customization-pagination' => array(
		'home'     => array(
			array(
				'name' => esc_html__( 'Pagination style', 'yith-faq-plugin-for-wordpress' ),
				'type' => 'title',
			),
			array(
				'type' => 'close',
			),
		),
		'settings' => array(
			array(
				'name'    => esc_html__( 'Pagination style', 'yith-faq-plugin-for-wordpress' ),
				'desc'    => esc_html__( 'Choose the pagination design.', 'yith-faq-plugin-for-wordpress' ),
				'id'      => 'pagination-layout',
				'type'    => 'select',
				'options' => array(
					'minimal' => esc_html__( 'Minimal', 'yith-faq-plugin-for-wordpress' ),
					'squared' => esc_html__( 'Squared', 'yith-faq-plugin-for-wordpress' ),
				),
				'class'   => 'yfwp-select',
				'std'     => yfwp_get_default( 'pagination-layout' ),
			),
			array(
				'name'         => esc_html__( 'Pagination colors', 'yith-faq-plugin-for-wordpress' ),
				'desc'         => '',
				'id'           => 'pagination-colors',
				'type'         => 'multi-colorpicker',
				'colorpickers' => array(
					array(
						'id'      => 'background',
						'name'    => esc_html__( 'Background', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'pagination-colors', 'background' ),
					),
					array(
						'id'      => 'background-hover',
						'name'    => esc_html__( 'Background hover', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'pagination-colors', 'background-hover' ),
					),
					array(
						'id'      => 'background-active',
						'name'    => esc_html__( 'Background active', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'pagination-colors', 'background-active' ),
					),
					array(
						'id'      => 'border',
						'name'    => esc_html__( 'Border', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'pagination-colors', 'border' ),
					),
					array(
						'id'      => 'border-hover',
						'name'    => esc_html__( 'Border hover', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'pagination-colors', 'border-hover' ),
					),
					array(
						'id'      => 'border-active',
						'name'    => esc_html__( 'Border active', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'pagination-colors', 'border-active' ),
					),
					array(
						'id'      => 'text',
						'name'    => esc_html__( 'Text', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'pagination-colors', 'text' ),
					),
					array(
						'id'      => 'text-hover',
						'name'    => esc_html__( 'Text hover', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'pagination-colors', 'text-hover' ),
					),
					array(
						'id'      => 'text-active',
						'name'    => esc_html__( 'Text active', 'yith-faq-plugin-for-wordpress' ),
						'default' => yfwp_get_default( 'pagination-colors', 'text-active' ),
					),
				),
			),
			array(
				'id'    => 'pagination-border',
				'name'  => esc_html__( 'Border radius', 'yith-faq-plugin-for-wordpress' ),
				'type'  => 'dimensions',
				'desc'  => esc_html__( 'Set the pagination border radius.', 'yith-faq-plugin-for-wordpress' ),
				'units' => array( 'px' => 'px' ),
				'std'   => yfwp_get_default( 'pagination-border' ),
				'deps'  => array(
					'ids'    => 'pagination-layout',
					'values' => 'squared',
				),
			),
		),
	),
);

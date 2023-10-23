<?php
/**
 * Customization options tab
 *
 * @package YITH\FAQPluginForWordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	'customization' => array(
		'customization-options' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => array(
				'customization-search'     => array(
					'title' => esc_html__( 'Search', 'yith-faq-plugin-for-wordpress' ),
				),
				'customization-filters'    => array(
					'title' => esc_html__( 'Filters', 'yith-faq-plugin-for-wordpress' ),
				),
				'customization-icon'       => array(
					'title' => esc_html__( 'Icon', 'yith-faq-plugin-for-wordpress' ),
				),
				'customization-faq'        => array(
					'title' => esc_html__( 'FAQ', 'yith-faq-plugin-for-wordpress' ),
				),
				'customization-pagination' => array(
					'title' => esc_html__( 'Pagination', 'yith-faq-plugin-for-wordpress' ),
				),
			),
		),
	),
);

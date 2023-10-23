<?php
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\CategoryAccordion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings = array(

	'settings' => array(

		'section_general_settings' => array(
			'name' => __( 'Plugin options', 'yith-woocommerce-category-accordion' ),
			'type' => 'title',
			'id'   => 'ywcca_section_general',
			'desc' => '',
		),

		'hide_empty_cat' => array(
			'id'        => 'ywcca_hide_empty_cat',
			'name'      => __( 'Hide empty categories', 'yith-woocommerce-category-accordion' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'desc'      => __( 'Enable to automatically hide empty categories in accordions', 'yith-woocommerce-category-accordion' ),
			'std'       => '',
		),

		'event_type_start_acc' => array(
			'name'      => __( 'Open accordion', 'yith-woocommerce-category-accordion' ),
			'desc'      => __( 'Select the event that will open the accordion menu', 'yith-woocommerce-category-accordion' ),
			'id'        => 'ywcca_event_type_start_acc',
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'click' => __( 'on click', 'yith-woocommerce-category-accordion' ),
				'hover' => __( 'on hover', 'yith-woocommerce-category-accordion' ),
			),
			'default'   => 'click',
			'std'       => 'click',
		),

		'accordion_speed' => array(
			'id'        => 'ywcca_accordion_speed',
			'name'      => __( 'Accordion speed', 'yith-woocommerce-category-accordion' ),
			'desc'      => __( 'Set the accordion speed in milliseconds. <strong> Default: </strong>400', 'yith-woocommerce-category-accordion' ),
			'type'      => 'yith-field',
			'yith-type' => 'inline-fields',
			'fields'    => array(
				'number_speed' => array(
					'type'    => 'number',
					'min'     => 0,
					'step'    => 1,
					'default' => 400,
					'std'     => 400,

				),
				'html_ms'      => array(
					'type' => 'html',
					'html' => esc_html( 'ms' ),
				),

			),
		),

		'accordion_macro_cat_close' => array(
			'id'        => 'ywcca_accordion_macro_cat_close',
			'name'      => __( 'Show accordions closed', 'yith-woocommerce-category-accordion' ),
			'desc'      => __( 'Enable to show accordions closed by default', 'yith-woocommerce-category-accordion' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'std'       => 'yes',
		),

		'open_sub_cat_parent_visit' => array(
			'id'        => 'ywcca_open_sub_cat_parent_visit',
			'name'      => __( 'Open subcategories', 'yith-woocommerce-category-accordion' ),
			'desc'      => __( 'Enable to open subcategories when visiting the parent ones', 'yith-woocommerce-category-accordion' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => 'yes',
			'std'       => 'yes',
		),

		'level_depth_acc' => array(
			'id'        => 'ywcca_level_depth_acc',
			'name'      => __( 'Category levels', 'yith-woocommerce-category-accordion' ),
			'desc'      => __( 'Choose if you want to show all category levels or if you want to set a max depth for subcategories', 'yith-woocommerce-category-accordion' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'all'   => __( 'show all levels', 'yith-woocommerce-category-accordion' ),
				'level' => __( 'set a max depth level for subcategories', 'yith-woocommerce-category-accordion' ),
			),
			'std'       => 'level',
			'default'   => 'level',

		),

		'max_depth_level' => array(
			'id'        => 'ywcca_max_level_depth',
			'name'      => __( 'Max depth level', 'yith-woocommerce-category-accordion' ),
			'desc'      => __( 'Set the max depth to show', 'yith-woocommerce-category-accordion' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'min'       => 1,
			'step'      => 1,
			'default'   => 2,
			'std'       => 2,
			'deps'      => array(
				'id'    => 'ywcca_level_depth_acc',
				'value' => 'level',
				'type'  => 'fadeInOut',
			),

		),

		'show_cat_acc' => array(
			'id'        => 'ywcca_show_cat_acc',
			'name'      => __( 'Categories to show', 'yith-woocommerce-category-accordion' ),
			'desc'      => __( 'Choose if you want to show all categories or set a max number to show', 'yith-woocommerce-category-accordion' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'all'    => __( 'show all categories', 'yith-woocommerce-category-accordion' ),
				'amount' => __( 'set a max number of categories to show', 'yith-woocommerce-category-accordion' ),
			),
			'std'       => 'amount',
			'default'   => 'amount',

		),

		'amount_max_cat_acc' => array(
			'id'        => 'ywcca_amount_max_acc',
			'name'      => __( 'Show a max of', 'yith-woocommerce-category-accordion' ),
			'desc'      => __( 'Set the maximum number of categories to show', 'yith-woocommerce-category-accordion' ),
			'type'      => 'yith-field',
			'yith-type' => 'inline-fields',
			'fields'    => array(
				'number_categories' => array(
					'type'    => 'number',
					'min'     => 1,
					'step'    => 1,
					'default' => 10,
					'std'     => 10,
				),
				'html_ms'           => array(
					'type' => 'html',
					'html' => __( 'categories', 'yith-woocommerce-category-accordion'),
				),
			),
			'deps'      => array(
				'id'    => 'ywcca_show_cat_acc',
				'value' => 'amount',
				'type'  => 'fadeInOut',
			),

		),

		'section_general_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywtm_section_general_end',
		),
	),
);

return apply_filters( 'yith_wc_category_accordion_options', $settings );

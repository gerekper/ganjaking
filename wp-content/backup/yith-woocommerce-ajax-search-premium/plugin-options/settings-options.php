<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Ajax Search Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.


return array(

	'settings' => array(

		'section_general_settings'         => array(
			'name' => __( 'General settings', 'yith-woocommerce-ajax-search' ),
			'type' => 'title',
			'id'   => 'yith_wcas_general',
		),

		'search_input_label'               => array(
			'name'    => __( 'Search input label', 'yith-woocommerce-ajax-search' ),
			'type'    => 'text',
			'desc'    => __( 'Label for Search input field.', 'yith-woocommerce-ajax-search' ),
			'id'      => 'yith_wcas_search_input_label',
			'default' => __( 'Search for products', 'yith-woocommerce-ajax-search' ),
		),

		'search_submit_label'              => array(
			'name'    => __( 'Search submit label', 'yith-woocommerce-ajax-search' ),
			'type'    => 'text',
			'desc'    => __( 'Label for Search input field.', 'yith-woocommerce-ajax-search' ),
			'id'      => 'yith_wcas_search_submit_label',
			'default' => __( 'Search', 'yith-woocommerce-ajax-search' ),
		),

		'trigger_min_chars'                => array(
			'name'              => __( 'Minimum number of characters', 'yith-woocommerce-ajax-search' ),
			'desc'              => __( 'Minimum number of characters required to trigger autosuggest.', 'yith-woocommerce-ajax-search' ),
			'id'                => 'yith_wcas_min_chars',
			'default'           => '3',
			'css'               => 'width:50px;',
			'type'              => 'number',
			'custom_attributes' => array(
				'min'  => 1,
				'max'  => 100,
				'step' => 1,
			),
		),

		'trigger_max_result_num'           => array(
			'name'              => __( 'Maximum number of results', 'yith-woocommerce-ajax-search' ),
			'desc'              => __( 'Maximum number of results showed in autosuggest box.', 'yith-woocommerce-ajax-search' ),
			'id'                => 'yith_wcas_posts_per_page',
			'default'           => '3',
			'css'               => 'width:50px;',
			'type'              => 'number',
			'custom_attributes' => array(
				'min'  => 1,
				'max'  => 15,
				'step' => 1,
			),
		),


		'section_ajax_search_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcas_general_end',
		),

		'section_additional_features'      => array(
			'name' => __( 'Additional Features', 'yith-woocommerce-ajax-search' ),
			'desc' => __( 'If these options are not showed, your theme may not support these features. Please, contact the developer of theme to implement them.', 'yith-woocommerce-ajax-search' ),
			'type' => 'title',
			'id'   => 'yith_wcas_additional_features',
		),

		'show_search_list'                 => array(
			'name'      => __( 'Show filter for search fields', 'yith-woocommerce-ajax-search' ),
			'desc'      => __( 'Show filter for search fields (it allows searching the Whole site or only among products)', 'yith-woocommerce-ajax-search' ),
			'id'        => 'yith_wcas_show_search_list',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),

		'show_category_list'               => array(
			'name'      => __( 'Show the category list', 'yith-woocommerce-ajax-search' ),
			'desc'      => __( 'This option lets you decide to show the categories dropdown', 'yith-woocommerce-ajax-search' ),
			'id'        => 'yith_wcas_show_category_list',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',

		),

		'show_category_list_all'           => array(
			'title'     => __( 'Categories to show', 'yith-woocommerce-ajax-search' ),
			'desc'      => __( 'This option lets you decide to show all the categories or only the main ones', 'yith-woocommerce-ajax-search' ),
			'id'        => 'yith_wcas_show_category_list_all',
			'class'     => 'wc-enhanced-select',
			'default'   => 'main',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'options'   => array(
				'main' => __( 'Main Categories', 'yith-woocommerce-ajax-search' ),
				'all'  => __( 'All Categories', 'yith-woocommerce-ajax-search' ),
			),
			'deps'      => array(
				'id'    => 'yith_wcas_show_category_list',
				'value' => 'yes',
			),
		),

		'section_additional_features_end'  => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcas_additional_features_end',
		),


	),
);

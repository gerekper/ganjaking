<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

global $yith_wcas;
return array(

	'settings' => array(

		'section_general_settings'         => array(
			'name'              => __( 'General settings', 'yith-woocommerce-ajax-search' ),
			'type'              => 'title',
			'custom_attributes' => array(
				'disabled' => 'disabled',
			),
			'id'                => 'yith_wcas_general',
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
				'max'  => 10,
				'step' => 1,
			),
		),

		'trigger_max_result_num'           => array(
			'name'              => __( 'Maximum number of results', 'yith-woocommerce-ajax-search' ),
			'desc'              => __( 'Maximum number of results showed within the autosuggest box.', 'yith-woocommerce-ajax-search' ),
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

		'enable_transient'                 => array(
			'name'      => __( 'Enable transients to cache autocomplete results', 'yith-woocommerce-ajax-search' ),
			'desc'      => __( 'Save the results of a query in a transient', 'yith-woocommerce-ajax-search' ),
			'id'        => 'yith_wcas_enable_transient',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),


		'transient_duration'               => array(
			'name'      => __( 'Set the duration of transient', 'yith-woocommerce-ajax-search' ),
			'desc'      => __( '(hours)', 'yith-woocommerce-ajax-search' ),
			'id'        => 'yith_wcas_transient_duration',
			'default'   => 12,
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'deps'      => array(
				'id'    => 'yith_wcas_enable_transient',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),


		'section_ajax_search_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcas_general_end',
		),
	),
);

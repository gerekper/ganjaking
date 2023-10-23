<?php
/**
 * Filter preset options
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Options
 * @version 4.0.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

return apply_filters(
	'yith_wcan_panel_filter_preset_options',
	array(
		'filter-preset' => YITH_WCAN()->admin->is_preset_detail_page() ? array(
			'presets' => array(
				'type'         => 'custom_tab',
				'action'       => 'yith_wcan_preset_details',
				'hide_sidebar' => true,
			),
		) : array(
			'filter_preset_section_start' => array(
				'type' => 'title',
				'desc' => '',
				'id'   => 'yith_wcan_filter_preset_settings',
			),

			'presets'                     => array_merge(
				array(
					'name'                 => _x( 'Filter Presets', '[Admin] Filter Presets tab', 'yith-woocommerce-ajax-navigation' ),
					'type'                 => 'yith-field',
					'yith-type'            => 'list-table',

					'list_table_class'     => 'YITH_WCAN_Filter_Presets_Table',
					'list_table_class_dir' => YITH_WCAN_INC . 'tables/class-yith-wcan-filter-presets-table.php',
					'title'                => _x( 'Filter Presets', '[Admin] Filter Presets table title', 'yith-woocommerce-ajax-navigation' ),
					'id'                   => 'filter_presets_table',
					'class'                => '',
					'post_type'            => YITH_WCAN_Presets()->get_post_type(),
				),
				YITH_WCAN_Preset_Factory::count_presets() ? array(
					'add_new_button' => _x( 'Add preset', '[Admin] Add Preset button', 'yith-woocommerce-ajax-navigation' ),
				) : array()
			),

			'lists_section_end'           => array(
				'type' => 'sectionend',
				'id'   => 'yith_wcan_filter_preset_settings',
			),
		),
	)
);

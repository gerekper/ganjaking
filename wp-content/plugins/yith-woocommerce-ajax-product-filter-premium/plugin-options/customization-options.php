<?php
/**
 * Customization options
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Options
 * @version 4.0.0
 */

$default_accent_color = apply_filters( 'yith_wcan_default_accent_color', '#A7144C' );

return apply_filters(
	'yith_wcan_panel_customization_options',
	array(
		'customization' => array(
			'global_section_start' => array(
				'name' => _x( 'Global options', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'yith_wcan_global_settings',
			),

			'filters_colors'              => array(
				'name'         => _x( 'Filters area colors', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
				'id'           => 'yith_wcan_filters_colors',
				'type'         => 'yith-field',
				'yith-type'    => 'multi-colorpicker',
				'colorpickers' => array(
					array(
						'name'    => _x( 'Titles', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
						'id'      => 'titles',
						'default' => '#333333',
					),
					array(
						'name'    => _x( 'Background', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
						'id'      => 'background',
						'default' => '#FFFFFF',
					),
					array(
						'name'    => _x( 'Accent color', '[ADMIN] Customization settings page', 'yith-woocommerce-ajax-navigation' ),
						'id'      => 'accent',
						'default' => $default_accent_color,
					),
				),
			),

			'global_section_end'   => array(
				'type' => 'sectionend',
				'id'   => 'yith_wcan_global_settings',
			),
		),
	)
);

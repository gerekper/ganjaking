<?php
/**
 * Preset options
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Options
 * @version 4.0.0
 */

return apply_filters(
	'yith_wcan_panel_preset_options',
	array(
		'preset_title' => array(
			'label' => _x( 'Preset name', '[Admin] Label in new preset page', 'yith-woocommerce-ajax-navigation' ),
			'type'  => 'text',
			'desc'  => _x( 'Enter a name to identify this filter preset', '[Admin] Label in new preset page', 'yith-woocommerce-ajax-navigation' ),
		),
	)
);

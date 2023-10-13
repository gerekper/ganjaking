<?php
/**
 * Price options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

$tab_options = array(
	'configuration-price-rules' => array(
		'price-rules-tab' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_wcbk_print_global_price_rules_tab',
		),
	),
);

return apply_filters( 'yith_wcbk_panel_costs_options', $tab_options );

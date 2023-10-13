<?php
/**
 * Dashboard options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

$sub_tabs = array(
	'configuration-availability-rules' => array(
		'title'       => _x( 'Availability Rules', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
		'description' => implode(
			'<br />',
			array(
				esc_html__( 'Create advanced rules to manage availability on specific dates.', 'yith-booking-for-woocommerce' ),
				esc_html__( 'These rules are global and applied to all bookable products by default. You can override them with specific rules on the product editing page.', 'yith-booking-for-woocommerce' ),
			)
		),
	),
	'configuration-price-rules'        => array(
		'title'       => _x( 'Price Rules', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
		'description' => implode(
			'<br />',
			array(
				esc_html__( 'Create advanced rules to set different prices for specific conditions (dates, months, duration).', 'yith-booking-for-woocommerce' ),
				esc_html__( 'These rules are global and applied to all bookable products by default. You can create product-specific rules on the product edit page.', 'yith-booking-for-woocommerce' ),
			)
		),
	),
);

$options = array(
	'configuration' => array(
		'configuration-tabs' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => apply_filters( 'yith_wcbk_panel_configuration_sub_tabs', $sub_tabs ),
		),
	),
);

return apply_filters( 'yith_wcbk_panel_configuration_options', $options );

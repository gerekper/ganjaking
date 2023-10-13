<?php
/**
 * Tools options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit();

$tab_options = array(
	'tools' => array(
		'tools-tabs' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => array(
				'tools-tools' => array(
					'title'       => _x( 'Tools', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
					'description' => __( 'A set of specific tools to debug, and perform tests and special actions in your site.', 'yith-booking-for-woocommerce' ),
				),
				'tools-logs'  => array(
					'title'       => _x( 'Logs', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
					'description' => __( 'Here you can see the list of logs related to your bookings.', 'yith-booking-for-woocommerce' ),

				),
			),
		),
	),
);

return apply_filters( 'yith_wcbk_panel_tools_options', $tab_options );

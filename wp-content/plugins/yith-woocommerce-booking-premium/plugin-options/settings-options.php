<?php
/**
 * Settings options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit();

$sub_tabs = array(
	'settings-booking-forms' => array(
		'title'              => _x( 'Booking Forms', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
		'yith-wcbk-priority' => 20,
		'description'        => __( 'Configure the layout of the elements in the booking forms.', 'yith-booking-for-woocommerce' ),
	),
	'settings-calendars'     => array(
		'title'              => _x( 'Calendars', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
		'description'        => __( 'Set the options related to the calendars handled by the plugin.', 'yith-booking-for-woocommerce' ),
		'yith-wcbk-priority' => 30,
	),
	'settings-customization' => array(
		'title'              => _x( 'Customizations', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
		'description'        => __( 'Customize the design of the elements rendered in your site, the format of the date and time shown, and a special behavior for your site.', 'yith-booking-for-woocommerce' ),
		'yith-wcbk-priority' => 100,
	),
);

$sub_tabs = apply_filters( 'yith_wcbk_panel_settings_sub_tabs', $sub_tabs );
$sub_tabs = yith_wcbk_filter_options(
	$sub_tabs,
	array(
		'sort'             => true,
		'default_priority' => 10,
	)
);

$options = array(
	'settings' => array(
		'settings-tabs' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => $sub_tabs,
		),
	),
);

return apply_filters( 'yith_wcbk_panel_settings_options', $options );

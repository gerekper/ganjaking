<?php
/**
 * Modules list.
 *
 * @package YITH\Booking\Modules
 */

defined( 'YITH_WCBK' ) || exit;

return array(
	'premium'         => array(
		// Premium needs to be the FIRST module loaded.
		'name'          => 'Premium',
		'hidden'        => true,
		'always_active' => true,
		'requires'      => 'premium',
	),
	'people'          => array(
		'name'         => _x( 'People', 'Module name', 'yith-booking-for-woocommerce' ),
		'description'  => __( 'Create unlimited "people types" and set specific rules or prices (e.g.: Adults, Under 18, Children, Students, Over 60, Military Veterans, Members, etc.).', 'yith-booking-for-woocommerce' ),
		'needs_reload' => true,
	),
	'services'        => array(
		'name'         => _x( 'Services', 'Module name', 'yith-booking-for-woocommerce' ),
		'description'  => __( 'Create free or paid services to associate with your bookable products (e.g.: parking, breakfast, daily cleaning, chauffeur, insurance, etc.).', 'yith-booking-for-woocommerce' ),
		'needs_reload' => true,
		'requires'     => 'premium',
	),
	'resources'       => array(
		'name'         => _x( 'Resources', 'Module name', 'yith-booking-for-woocommerce' ),
		'description'  => __( 'Create unlimited "resources" (e.g.: equipment, staff, technology, software, etc.) to associate with your bookable products.', 'yith-booking-for-woocommerce' ),
		'needs_reload' => true,
	),
	'costs'           => array(
		'name'         => _x( 'Extra costs and discounts', 'Module name', 'yith-booking-for-woocommerce' ),
		'description'  => __( 'Create unlimited costs to apply to your bookings (e.g.: insurance, visitor\'s tax, cleaning service, etc.) and set discounts for weekly or monthly bookings.', 'yith-booking-for-woocommerce' ),
		'needs_reload' => true,
		'requires'     => 'premium',
	),
	'search-forms'    => array(
		'name'         => _x( 'Search forms', 'Module name', 'yith-booking-for-woocommerce' ),
		'description'  => __( 'Allow your users to search for specific bookable products. Choose which filters to enable for each form (dates, people, services, etc.).', 'yith-booking-for-woocommerce' ),
		'needs_reload' => true,
		'requires'     => 'premium',
	),
	'google-maps'     => array(
		'name'        => _x( 'Google Maps', 'Module name', 'yith-booking-for-woocommerce' ),
		'description' => __( 'Set the location for your bookable products, show a Google map on the product page and allow your users to search for products by location.', 'yith-booking-for-woocommerce' ),
		'requires'    => 'premium',
	),
	'google-calendar' => array(
		'name'        => _x( 'Google Calendar', 'Module name', 'yith-booking-for-woocommerce' ),
		'description' => __( 'Synchronize all bookings placed on your site by automatically adding them to your Google Calendar.', 'yith-booking-for-woocommerce' ),
	),
	'external-sync'   => array(
		'name'        => _x( 'External synchronization', 'Module name', 'yith-booking-for-woocommerce' ),
		'description' => __( 'Auto-sync the availability of your bookable products with external platforms like Booking, Airbnb, and HomeAway.', 'yith-booking-for-woocommerce' ),
		'requires'    => 'premium',
	),
);

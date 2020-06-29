<?php
/**
 * Compatibility class
 *
 * This class is responsible for loading the
 * compatibility classes for the supported plugins.
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_COMPATIBILITY_base {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->add_compatibility();

		do_action( 'wc_epo_add_compatibility' );

	}

	/**
	 * Add compatibility classes
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {

		// Currency Switchers
		THEMECOMPLETE_EPO_CP_currency::instance();
		// WPML Multilingual CMS
		THEMECOMPLETE_EPO_CP_WPML::instance();
		// WooCommerce Subscriptions
		THEMECOMPLETE_EPO_CP_subscriptions::instance();
		// WooCommerce Composite Products
		THEMECOMPLETE_EPO_CP_composite::instance();
		// WooCommerce Bookings
		THEMECOMPLETE_EPO_CP_bookings::instance();
		// Measurement Price Calculator
		THEMECOMPLETE_EPO_CP_measurement::instance();
		// WooCommerce Easy Booking
		THEMECOMPLETE_EPO_CP_easy_bookings::instance();
		// Booking & Appointment Plugin for WooCommerce
		THEMECOMPLETE_EPO_CP_BAP::instance();
		// WooCommerce Dynamic Pricing & Discounts
		THEMECOMPLETE_EPO_CP_DPD::instance();
		// Elex Dynamic Pricing & Discounts
		THEMECOMPLETE_EPO_CP_ELEX_DPD::instance();
		// Store Exporter Deluxe for WooCommerce
		THEMECOMPLETE_EPO_CP_store_exporter::instance();
		// WooTour - WooCommerce Travel Tour Booking
		THEMECOMPLETE_EPO_CP_wootours::instance();
		// ElasticPress
		THEMECOMPLETE_EPO_CP_elasticpress::instance();
		// qTranslate X
		THEMECOMPLETE_EPO_CP_qtranslatex::instance();
		// Quick view plugins
		THEMECOMPLETE_EPO_CP_quickview::instance();
		// The SEO Framework
		THEMECOMPLETE_EPO_CP_theseoframework::instance();
		// Woocommerce Add to cart Ajax for variable products
		THEMECOMPLETE_EPO_CP_WATCAFVP::instance();
		// WooDeposits - Woocommerce partial payments and deposits plugin
		THEMECOMPLETE_EPO_CP_woodeposits::instance();
		// Name Your Price 
		THEMECOMPLETE_EPO_CP_nyp::instance();
		// Fancy Product Designer 
		THEMECOMPLETE_EPO_CP_fpd::instance();
		// Food Online Premium for WooCommerce
		THEMECOMPLETE_EPO_CP_fop::instance();

	}

}



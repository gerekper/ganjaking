<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for loading the
 * compatibility classes for the supported plugins.
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_COMPATIBILITY_Base {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_COMPATIBILITY_Base|null
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_COMPATIBILITY_Base
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
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
	 * @return void
	 * @since 1.0
	 */
	public function add_compatibility() {
		// Aelia Currency Switcher.
		THEMECOMPLETE_EPO_CP_Aelia_Currency_Switcher::instance();
		// WPML Currency.
		THEMECOMPLETE_EPO_CP_WPML_Currency::instance();
		// WooCommerce Currency Switcher (WOOCS).
		THEMECOMPLETE_EPO_CP_WOOCS::instance();
		// WooCommerce Currency 3rd Party plugins.
		THEMECOMPLETE_EPO_CP_Currency_3rd_Party::instance();
		// WooCommerce Subscriptions.
		THEMECOMPLETE_EPO_CP_Subscriptions::instance();
		// WPML Multilingual CMS.
		THEMECOMPLETE_EPO_CP_WPML::instance();
		// WooCommerce Composite Products.
		THEMECOMPLETE_EPO_CP_Composite::instance();
		// WooCommerce Bookings.
		THEMECOMPLETE_EPO_CP_Bookings::instance();
		// Measurement Price Calculator.
		THEMECOMPLETE_EPO_CP_Measurement::instance();
		// WooCommerce Easy Booking.
		THEMECOMPLETE_EPO_CP_Easy_Bookings::instance();
		// Booking & Appointment Plugin for WooCommerce.
		THEMECOMPLETE_EPO_CP_BAP::instance();
		// WooCommerce Dynamic Pricing & Discounts.
		THEMECOMPLETE_EPO_CP_DPD::instance();
		// Advanced Dynamic Pricing for WooCommerce Pro (AlogPlus).
		THEMECOMPLETE_EPO_CP_APD_PRO::instance();
		// Store Exporter Deluxe for WooCommerce.
		THEMECOMPLETE_EPO_CP_Store_Exporter::instance();
		// WooTour - WooCommerce Travel Tour Booking.
		THEMECOMPLETE_EPO_CP_Wootours::instance();
		// ElasticPress.
		THEMECOMPLETE_EPO_CP_Elasticpress::instance();
		// qTranslate X.
		THEMECOMPLETE_EPO_CP_Qtranslatex::instance();
		// Quick view plugins.
		THEMECOMPLETE_EPO_CP_Quickview::instance();
		// The SEO Framework.
		THEMECOMPLETE_EPO_CP_TheSeoFramework::instance();
		// Woocommerce Add to cart Ajax for variable products.
		THEMECOMPLETE_EPO_CP_WATCAFVP::instance();
		// Name Your Price.
		THEMECOMPLETE_EPO_CP_NYP::instance();
		// Fancy Product Designer.
		THEMECOMPLETE_EPO_CP_FPD::instance();
		// Food Online Premium for WooCommerce.
		THEMECOMPLETE_EPO_CP_FOP::instance();
		// ATUM Inventory Management for WooCommerce.
		THEMECOMPLETE_EPO_CP_Atum::instance();
		// Account funds.
		THEMECOMPLETE_EPO_CP_Account_Funds::instance();
		// Booster.
		THEMECOMPLETE_EPO_CP_Booster::instance();
		// Themes.
		THEMECOMPLETE_EPO_CP_Themes::instance();
		// Plugins (various).
		THEMECOMPLETE_EPO_CP_Plugins::instance();
		// WooCommerce B2B by Code4Life.
		THEMECOMPLETE_EPO_CP_B2B_Code4Life::instance();
	}
}

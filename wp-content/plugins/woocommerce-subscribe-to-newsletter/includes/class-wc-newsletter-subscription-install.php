<?php
/**
 * Installation related functions and actions
 *
 * @package WC_Newsletter_Subscription
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Newsletter_Subscription_Install.
 */
class WC_Newsletter_Subscription_Install {

	/**
	 * Init installation.
	 *
	 * @since 4.0.0
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'init', array( __CLASS__, 'add_endpoints' ) );
	}

	/**
	 * Check the plugin version and run the updater is necessary.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 *
	 * @since 4.0.0
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'wc_newsletter_subscription_version' ), WC_NEWSLETTER_SUBSCRIPTION_VERSION, '<' ) ) {
			self::install();
		}
	}

	/**
	 * Init installation.
	 *
	 * @since 4.0.0
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running the installation process.
		if ( 'yes' === get_transient( 'wc_newsletter_subscription_installing' ) ) {
			return;
		}

		// Add a transient to indicate that we are running the installation process.
		set_transient( 'wc_newsletter_subscription_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		self::add_endpoints();
		self::update_version();

		// Installation finished.
		delete_transient( 'wc_newsletter_subscription_installing' );

		flush_rewrite_rules();

		/**
		 * Fires when the plugin installation finished.
		 *
		 * @since 4.0.0
		 */
		do_action( 'wc_newsletter_subscription_installed' );
	}

	/**
	 * Update the plugin version to current.
	 *
	 * @since 4.0.0
	 */
	private static function update_version() {
		update_option( 'wc_newsletter_subscription_version', WC_NEWSLETTER_SUBSCRIPTION_VERSION );
	}

	/**
	 * Registers custom endpoints.
	 *
	 * @since 4.0.0
	 */
	public static function add_endpoints() {
		$mask = ( function_exists( 'WC' ) && ! is_null( WC()->query ) ? WC()->query->get_endpoints_mask() : EP_PAGES );

		add_rewrite_endpoint( 'newsletter', $mask );
	}
}

WC_Newsletter_Subscription_Install::init();

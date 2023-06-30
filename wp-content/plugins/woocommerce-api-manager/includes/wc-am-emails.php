<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager Subscriptions Email Class
 *
 * Modifies the base WooCommerce email class and extends it to send subscription emails.
 *
 * @since       3.0
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Subscriptions Email
 */
class WC_AM_Emails {

	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * @static
	 * @return \WC_AM_Emails
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function __construct() {
		add_filter( 'woocommerce_email_classes', array( $this, 'register_email__classes' ), 10, 1 );
	}

	/**
	 * Register email classes.
	 *
	 * @since 3.0
	 */
	public function register_email__classes( $email_classes ) {
		$email_classes[ 'WCAM_Email_Expiring_Subscription' ] = require_once( 'emails/wcam-email-expiring-subscription.php' );

		return $email_classes;
	}

	/**
	 * Prepare and send the customer the expiring subscription email on-demand.
	 *
	 * @since 3.0
	 *
	 * @param int $api_resource_id
	 */
	public function send_subscription_expiration_notification( $api_resource_id ) {
		if ( ! empty( $api_resource_id ) ) {
			/**
			 * @var WCAM_Email_Expiring_Subscription $mailer
			 */
			$mailer = WC()->mailer()->get_emails()[ 'WCAM_Email_Expiring_Subscription' ];
			$mailer->trigger( $api_resource_id );
		}
	}
}

/**
 * Returns the WC_AM_Emails class object
 *
 * @since 3.0
 *
 * @return \WC_AM_Emails
 */
function WC_AM_EMAILS() {
	return WC_AM_Emails::instance();
}

WC_AM_EMAILS();
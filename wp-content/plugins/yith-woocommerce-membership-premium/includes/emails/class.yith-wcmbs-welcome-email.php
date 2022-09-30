<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Welcome_Mail' ) ) {
	/**
	 * Membership Welcome Email
	 */
	class YITH_WCMBS_Welcome_Mail extends YITH_WCMBS_Email {
		/**
		 * Constructor
		 */
		function __construct() {
			$this->id             = 'membership_welcome';
			$this->customer_email = true;
			$this->title          = __( 'Membership - Welcome', 'yith-woocommerce-membership' );
			$this->description    = __( 'The welcome email is sent when a customer becomes member.', 'yith-woocommerce-membership' );

			$this->template_base  = YITH_WCMBS_TEMPLATE_PATH . '/';
			$this->template_html  = 'emails/membership-welcome.php';
			$this->template_plain = 'emails/plain/membership-welcome.php';

			$this->subject = __( 'Welcome to membership {membership_name}', 'yith-woocommerce-membership' );
			$this->heading = __( 'Welcome to membership {membership_name}', 'yith-woocommerce-membership' );

			// Set default custom message
			$this->custom_message = __( "Dear {firstname} {lastname},\n\nwelcome to your membership to <b>{membership_name}.</b>\n\nWe are glad to have you with us! :)\n\nRegards,\n\nStaff of {site_title}", 'yith-woocommerce-membership' );

			// Triggers
			add_action( 'yith_wcmbs_new_member_notification', array( $this, 'trigger' ) );

			parent::__construct();
		}
	}
}

return new YITH_WCMBS_Welcome_Mail();
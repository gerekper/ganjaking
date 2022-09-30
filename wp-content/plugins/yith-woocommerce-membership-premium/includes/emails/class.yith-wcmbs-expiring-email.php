<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Expiring_Mail' ) ) {
	/**
	 * Membership Expiring Email
	 */
	class YITH_WCMBS_Expiring_Mail extends YITH_WCMBS_Email {
		/**
		 * Constructor
		 */
		function __construct() {
			$this->id             = 'membership_expiring';
			$this->customer_email = true;
			$this->title          = __( 'Expiring Membership', 'yith-woocommerce-membership' );
			$this->description    = __( 'Expiring Membership email is sent when a membership is expiring.', 'yith-woocommerce-membership' );

			$this->template_base  = YITH_WCMBS_TEMPLATE_PATH . '/';
			$this->template_html  = 'emails/membership-expiring.php';
			$this->template_plain = 'emails/plain/membership-expiring.php';

			$this->subject = __( 'Membership {membership_name} is expiring', 'yith-woocommerce-membership' );
			$this->heading = __( 'Membership {membership_name} is expiring', 'yith-woocommerce-membership' );

			// Set default custom message
			$this->custom_message = __( "Dear {firstname} {lastname},\n\nyour membership to <b>{membership_name}</b> will expire on {membership_expire_date}.\n\nRegards,\n\nStaff of {site_title}", 'yith-woocommerce-membership' );

			// Triggers
			add_action( 'yith_wcmbs_membership_expiring_notification', array( $this, 'trigger' ) );

			parent::__construct();
		}
	}
}

return new YITH_WCMBS_Expiring_Mail();
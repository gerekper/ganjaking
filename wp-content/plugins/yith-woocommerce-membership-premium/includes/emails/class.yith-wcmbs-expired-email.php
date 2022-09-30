<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Expired_Mail' ) ) {
	/**
	 * Membership Expired Email
	 */
	class YITH_WCMBS_Expired_Mail extends YITH_WCMBS_Email {
		/**
		 * Constructor
		 */
		function __construct() {
			$this->id             = 'membership_expired';
			$this->customer_email = true;
			$this->title          = __( 'Expired Membership', 'yith-woocommerce-membership' );
			$this->description    = __( 'Expired Membership email is sent when a membership is expired.', 'yith-woocommerce-membership' );

			$this->template_base  = YITH_WCMBS_TEMPLATE_PATH . '/';
			$this->template_html  = 'emails/membership-expired.php';
			$this->template_plain = 'emails/plain/membership-expired.php';

			$this->subject = __( 'Membership {membership_name} is expired', 'yith-woocommerce-membership' );
			$this->heading = __( 'Membership {membership_name} is expired', 'yith-woocommerce-membership' );

			// Set default custom message
			$this->custom_message = __( "Dear {firstname} {lastname},\n\nyour membership to <b>{membership_name}</b> has expired.\n\nRegards,\n\nStaff of {site_title}", 'yith-woocommerce-membership' );

			// Triggers
			add_action( 'yith_wcmbs_membership_expired_notification', array( $this, 'trigger' ) );

			parent::__construct();
		}
	}
}

return new YITH_WCMBS_Expired_Mail();
<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Cancelled_Mail' ) ) {
	/**
	 * Membership Cancelled Email
	 */
	class YITH_WCMBS_Cancelled_Mail extends YITH_WCMBS_Email {
		/**
		 * Constructor
		 */
		function __construct() {
			$this->id             = 'membership_cancelled';
			$this->customer_email = true;
			$this->title          = __( 'Cancelled Membership', 'yith-woocommerce-membership' );
			$this->description    = __( 'Cancelled Membership email is sent when a customer membership is cancelled.', 'yith-woocommerce-membership' );

			$this->template_base  = YITH_WCMBS_TEMPLATE_PATH . '/';
			$this->template_html  = 'emails/membership-cancelled.php';
			$this->template_plain = 'emails/plain/membership-cancelled.php';

			$this->subject = __( 'Membership {membership_name} cancelled', 'yith-woocommerce-membership' );
			$this->heading = __( 'Membership {membership_name} cancelled', 'yith-woocommerce-membership' );

			// Set default custom message
			$this->custom_message = __( "Dear {firstname} {lastname},\n\nyour membership to <b>{membership_name}</b> has been cancelled.\n\nRegards,\n\nStaff of {site_title}", 'yith-woocommerce-membership' );

			// Triggers
			add_action( 'yith_wcmbs_membership_cancelled_notification', array( $this, 'trigger' ) );

			parent::__construct();
		}
	}
}

return new YITH_WCMBS_Cancelled_Mail();
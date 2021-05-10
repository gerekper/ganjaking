<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Frontend_Shortcode' ) ) {
	/**
	 * Handles the user account for displaying their personal waitlist items
	 *
	 * @package  WooCommerce Waitlist
	 */
	class Pie_WCWL_Frontend_Shortcode extends Pie_WCWL_Frontend_User_Waitlist {

		/**
		 * Pie_WCWL_Frontend_Shortcode constructor.
		 */
		public function __construct() {
			$this->init();
			$this->load_shortcode();
		}

		/**
		 * Add hooks for loading the shortcode
		 */
		protected function load_shortcode() {
			add_shortcode( 'woocommerce_my_waitlist', array( $this, 'display_users_waitlists' ) );
		}
	}
}

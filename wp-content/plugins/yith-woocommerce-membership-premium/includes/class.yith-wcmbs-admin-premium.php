<?php
/**
 * Admin class
 *
 * @author  YITH
 * @package YITH WooCommerce Membership
 */

! defined( 'YITH_WCMBS' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Admin_Premium' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @deprecated 1.4.0 | use YITH_WCMBS_Admin instead
	 */
	class YITH_WCMBS_Admin_Premium extends YITH_WCMBS_Admin {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCMBS_Admin_Premium
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		protected function __construct() {
			parent::__construct();
		}
	}
}
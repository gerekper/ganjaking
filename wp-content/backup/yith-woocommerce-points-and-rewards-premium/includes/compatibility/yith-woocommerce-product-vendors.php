<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}


/**
 * YWPAR_Multivendor class to add compatibility with YITH WooCommerce Multivendor
 *
 * @class   YWPAR_Multivendor
 * @package YITH WooCommerce Points and Rewards
 * @since   1.1.3
 * @author  YITH
 */
if ( ! class_exists( 'YWPAR_Multivendor' ) ) {

	/**
	 * Class YWPAR_Multivendor
	 */
	class YWPAR_Multivendor {

		/**
		 * Single instance of the class
		 *
		 * @var \YWPAR_Multivendor
		 */
		protected static $instance;


		/**
		 * @var string
		 */
		protected $current_order = '';


		/**
		 * Returns single instance of the class
		 *
		 * @return \YWPAR_Multivendor
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize class and registers actions and filters to be used
		 *
		 * @since  1.3.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			add_filter( 'ywpar_enable_product_meta', array( $this, 'disable_vendor_admin' ) );
			add_filter( 'ywpar_add_order_points', array( $this, 'check_if_is_suborder' ), 10, 2 );
			add_filter( 'ywpar_save_points_earned_from_cart', array( $this, 'save_points_earned_from_cart' ) );

		}

		/**
		 * Get points only if the main order is completed.
		 *
		 * @param $order_id
		 *
		 * @return bool
		 */
		public function save_points_earned_from_cart( $order_id ) {
			if ( $this->check_if_is_suborder( $order_id ) ) {
				return true;
			}
		}

		/**
		 * Check if the order is a sub order
		 *
		 * @param $order_id
		 *
		 * @return bool
		 * @internal param $result
		 */
		public function check_if_is_suborder( $check, $order_id ) {
			return wp_get_post_parent_id( $order_id ) ? true : $check;
		}


		/**
		 * Disable points and rewards product editor options
		 *
		 * @param $enable
		 *
		 * @return bool
		 */
		public function disable_vendor_admin( $enable ) {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				return false;
			}

			return $enable;
		}


	}

}

/**
 * Unique access to instance of YWPAR_Multivendor class
 *
 * @return \YWPAR_Multivendor
 */
function YWPAR_Multivendor() {
	return YWPAR_Multivendor::get_instance();
}

YWPAR_Multivendor();

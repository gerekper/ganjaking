<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}


/**
 * YWSBS_Multivendor class to add compatibility with YITH WooCommerce Multivendor
 *
 * @class   YWSBS_Membership
 * @package YITH WooCommerce Subscription
 * @since   1.1.0
 * @author  YITH
 */
if ( ! class_exists( 'YWSBS_Membership' ) && function_exists( 'YITH_WCMBS_Membership_Helper' ) ) {

	class YWSBS_Membership {

		/**
		 * Single instance of the class
		 *
		 * @var \YWSBS_Multivendor
		 */
		protected static $instance;



		/**
		 * Returns single instance of the class
		 *
		 * @return \YWSBS_Multivendor
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
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {
			add_filter( 'ywsbs_subscription_table_list_columns', array( $this, 'subscription_table_list_columns' ) );
			add_filter( 'ywsbs_column_default', array( $this, 'subscription_column_default' ), 10, 3 );
			add_action( 'init', array( $this, 'init' ), 20 );
		}

		/**
		 *
		 */
		public function init() {

		}

		/**
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function subscription_table_list_columns( $columns ) {
			$columns['membership'] = __( 'Membership Status', 'yith-woocommerce-subscription' );

			return $columns;

		}


		/**
		 * @param $result
		 * @param $item
		 * @param $column_name
		 *
		 * @return mixed
		 */
		public function subscription_column_default( $result, $item, $column_name ) {
			if ( $column_name == 'membership' ) {
				$memberships = YITH_WCMBS_Membership_Helper()->get_memberships_by_subscription( $item->ID );
				if ( $memberships ) {
					$result = $memberships[0]->get_status_text();
				}

				return $result;
			}
		}

	}

}

/**
 * Unique access to instance of YWSBS_Membership class
 *
 * @return YWSBS_Multivendor
 */
function YWSBS_Membership() {
	return YWSBS_Membership::get_instance();
}

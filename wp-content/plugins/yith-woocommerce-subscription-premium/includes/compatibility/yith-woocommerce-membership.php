<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Membership class to add compatibility with YITH WooCommerce Multivendor
 *
 * @class   YWSBS_Membership
 * @package YITH WooCommerce Subscription
 * @since   1.1.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWSBS_Membership' ) && function_exists( 'YITH_WCMBS_Membership_Helper' ) ) {
	/**
	 * Class YWSBS_Membership
	 */
	class YWSBS_Membership {

		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Membership
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Membership
		 * @since 1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize class and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {
			add_filter( 'ywsbs_subscription_table_list_columns', array( $this, 'subscription_table_list_columns' ) );
			add_filter( 'ywsbs_column_default', array( $this, 'subscription_column_default' ), 10, 3 );
		}


		/**
		 * Add a column inside subscription list table.
		 *
		 * @param array $columns Columns list.
		 *
		 * @return array
		 */
		public function subscription_table_list_columns( $columns ) {
			$columns['membership'] = __( 'Membership Status', 'yith-woocommerce-subscription' );

			return $columns;
		}


		/**
		 * Fill the new column with the Membership status.
		 *
		 * @param string  $result Value to fill.
		 * @param WP_Post $item Current item.
		 * @param string  $column_name Column name.
		 *
		 * @return string
		 */
		public function subscription_column_default( $result, $item, $column_name ) {
			if ( 'membership' === $column_name ) {
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
 * @return YWSBS_Membership
 */
function YWSBS_Membership() { // phpcs:ignore
	return YWSBS_Membership::get_instance();
}

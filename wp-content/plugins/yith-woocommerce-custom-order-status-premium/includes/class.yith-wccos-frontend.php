<?php
/**
 * Frontend class
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCCOS_Frontend' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the Frontend behaviors.
	 *
	 * @since    1.0.0
	 * @author   YITH <plugins@yithemes.com>
	 */
	class YITH_WCCOS_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCCOS_Frontend
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCCOS_Frontend
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 */
		protected function __construct() {
			add_filter( 'woocommerce_valid_order_statuses_for_cancel', array( $this, 'add_statuses_for_cancel' ) );

			add_filter( 'woocommerce_valid_order_statuses_for_payment', array( $this, 'add_statuses_for_pay' ) );

			add_filter( 'woocommerce_valid_order_statuses_for_payment_complete', array( $this, 'add_statuses_for_pay' ) );

			add_filter( 'woocommerce_order_is_download_permitted', array( $this, 'woocommerce_order_is_download_permitted' ), 10, 2 );

			add_filter( 'woocommerce_order_is_paid_statuses', array( $this, 'add_paid_statuses' ) );
		}

		/**
		 * Add paid statuses
		 * it doesn't override the WooCommerce builtin ones to prevent issues
		 *
		 * @param array $statuses The statuses.
		 *
		 * @return array
		 */
		public function add_paid_statuses( $statuses ) {
			$status_ids = get_posts(
				array(
					'posts_per_page' => - 1,
					'post_type'      => 'yith-wccos-ostatus',
					'post_status'    => 'publish',
					'fields'         => 'ids',
				)
			);

			foreach ( $status_ids as $status_id ) {
				$is_paid = yith_wccos_is_true( get_post_meta( $status_id, 'is-paid', true ) );
				if ( $is_paid ) {
					$statuses[] = get_post_meta( $status_id, 'slug', true );
				}
			}

			return array_unique( $statuses );
		}

		/**
		 * Order is download permitted
		 *
		 * Check if the order status has downloads permitted checked
		 *
		 * @param bool     $download_permitted True if download is allowed.
		 * @param WC_Order $order              The order.
		 *
		 * @return bool
		 * @access public
		 * @since  1.0.0
		 */
		public function woocommerce_order_is_download_permitted( $download_permitted, $order ) {
			// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			$order_status = $order->get_status();
			$status_ids   = get_posts(
				array(
					'posts_per_page' => - 1,
					'post_type'      => 'yith-wccos-ostatus',
					'post_status'    => 'publish',
					'fields'         => 'ids',
					'meta_query'     => array(
						array(
							'key'   => 'slug',
							'value' => $order_status,
						),
					),
				)
			);

			// phpcs:enable

			if ( ! ! $status_ids ) {
				foreach ( $status_ids as $status_id ) {
					$download_permitted = yith_wccos_is_true( get_post_meta( $status_id, 'downloads-permitted', true ) );
					break;
				}
			}

			return $download_permitted;
		}

		/**
		 * Add Statuses for cancel
		 * Add the statuses in which the order can be cancelled by user
		 *
		 * @param array $statuses The statuses.
		 *
		 * @return array
		 */
		public function add_statuses_for_cancel( $statuses ) {
			$status_ids = yith_wccos_get_statuses();

			$new_statuses            = array();
			$cancel_default_statuses = array();

			foreach ( (array) $statuses as $status ) {
				$cancel_default_statuses[ $status ] = 1;
			}

			foreach ( $status_ids as $status_id ) {
				$can_cancel = yith_wccos_is_true( get_post_meta( $status_id, 'can-cancel', true ) );
				$slug       = get_post_meta( $status_id, 'slug', true );
				if ( $can_cancel ) {
					if ( ! in_array( $slug, (array) $statuses, true ) ) {
						$new_statuses[] = $slug;
					}
				} else {
					if ( in_array( $slug, (array) $statuses, true ) ) {
						$cancel_default_statuses[ $slug ] = 0;
					}
				}
			}
			foreach ( $cancel_default_statuses as $key => $value ) {
				if ( $value ) {
					$new_statuses[] = $key;
				}
			}

			return $new_statuses;
		}


		/**
		 * Add Statuses for pay
		 * Add the statuses in which the order can be payed by user
		 *
		 * @param array $statuses The statuses.
		 *
		 * @return array
		 */
		public function add_statuses_for_pay( $statuses ) {
			$status_ids = yith_wccos_get_statuses();

			$all_statuses        = $statuses;
			$statuses_to_disable = array();

			foreach ( $status_ids as $status_id ) {
				$can_pay = yith_wccos_is_true( get_post_meta( $status_id, 'can-pay', true ) );
				$slug    = get_post_meta( $status_id, 'slug', true );

				$all_statuses[] = $slug;

				if ( ! $can_pay ) {
					$statuses_to_disable[] = $slug;
				}
			}

			$all_statuses = array_unique( $all_statuses );
			$new_statuses = array_diff( $all_statuses, $statuses_to_disable );

			return array_unique( $new_statuses );
		}
	}
}
/**
 * Unique access to instance of YITH_WCCOS_Frontend class
 *
 * @return YITH_WCCOS_Frontend
 */
function yith_wccos_frontend() {
	return YITH_WCCOS_Frontend::get_instance();
}

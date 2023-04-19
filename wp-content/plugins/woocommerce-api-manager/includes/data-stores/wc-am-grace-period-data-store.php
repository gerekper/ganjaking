<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager Grace Period Data Store Class
 *
 * @since       2.6
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Grace Period Data Store
 */
class WC_AM_Grace_Period_Data_Store implements WCAM_Grace_Period_Data_Store_Interface {

	private $grace_period_table = '';

	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * @static
	 * @return null|\WC_AM_Grace_Period_Data_Store
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function __construct() {
		$this->grace_period_table = WC_AM_USER()->get_grace_period_table_table_name();
	}

	/**
	 * Adds a Grace Period expiration time for an API Resource.
	 *
	 * @since 2.6
	 *
	 * @param int $api_resource_id
	 * @param int $expires
	 *
	 * @return bool
	 */
	public function insert( $api_resource_id, $expires ) {
		global $wpdb;

		$data = array(
			'api_resource_id' => (int) $api_resource_id,
			'expires'         => (int) $expires
		);

		$format = array(
			'%d',
			'%d'
		);

		$result = $wpdb->insert( $wpdb->prefix . $this->grace_period_table, $data, $format );

		return ! WC_AM_FORMAT()->empty( $result );
	}

	/**
	 * Updates a Grace Period expiration time for an API Resource if it exists, or adds the expiration if it does not exist.
	 *
	 * @since 2.6
	 *
	 * @param int $api_resource_id
	 * @param int $expires
	 *
	 * @return bool
	 */
	public function update( $api_resource_id, $expires ) {
		global $wpdb;

		$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}$this->grace_period_table (`api_resource_id`, `expires`) VALUES (%d, %d)
 					ON DUPLICATE KEY UPDATE `api_resource_id` = VALUES(`api_resource_id`), `expires` = VALUES(`expires`)", $api_resource_id, $expires ) );

		return ! WC_AM_FORMAT()->empty( $result );
	}

	/**
	 * Deletes a Grace Period expiration time for an API Resource.
	 *
	 * @since 2.6
	 *
	 * @param int $api_resource_id
	 *
	 * @return bool
	 */
	public function delete( $api_resource_id ) {
		global $wpdb;

		$where = array(
			'api_resource_id' => (int) $api_resource_id
		);

		$where_format = array(
			'%d'
		);

		$result = $wpdb->delete( $wpdb->prefix . $this->grace_period_table, $where, $where_format );

		return ! WC_AM_FORMAT()->empty( $result );
	}

	/**
	 * Returns a Grace Period expiration time for an API Resource.
	 *
	 * @since  2.6
	 *
	 * @param int $api_resource_id
	 *
	 * @return int
	 */
	public function get_expiration( $api_resource_id ) {
		global $wpdb;

		$expiration = $wpdb->get_var( $wpdb->prepare( "
				SELECT 		expires
				FROM {$wpdb->prefix}" . $this->grace_period_table . "
				WHERE 		api_resource_id = %d
			", (int) $api_resource_id ) );

		return ! WC_AM_FORMAT()->empty( $expiration ) ? (int) $expiration : 0;
	}

	/**
	 * Returns true if the $api_resource_id exists in the table.
	 *
	 * @since 2.6
	 *
	 * @param int $api_resource_id
	 *
	 * @return bool
	 */
	public function exists( $api_resource_id ) {
		global $wpdb;

		$id = $wpdb->get_var( $wpdb->prepare( "
			SELECT 		api_resource_id
			FROM {$wpdb->prefix}" . $this->grace_period_table . "
			WHERE 		api_resource_id = %d
			LIMIT 1
			", (int) $api_resource_id ) );

		return ! WC_AM_FORMAT()->empty( $id );
	}

	/**
	 * Returns true if the Grace Period is not greater than current time (now).
	 *
	 * @since   2.6
	 * @updated 2.6.7 Do not delete expired Grace Period here.
	 *
	 * @param int $api_resource_id
	 *
	 * @return bool
	 */
	public function is_expired( $api_resource_id ) {
		$expired = false;

		if ( $this->exists( $api_resource_id ) ) {
			$expired = WC_AM_ORDER_DATA_STORE()->is_time_expired( $this->get_expiration( $api_resource_id ) );
		}

		return $expired;
	}

	/**
	 * Adds the WooCommerce Subscription Grace Period expiration.
	 *
	 * @since 2.6
	 *
	 * @param int $api_resource_id
	 *
	 * @return bool
	 */
	public function add_wc_subscription_expiration_by_api_resource_id( $api_resource_id ) {
		return $this->add_wc_subscription_expiration( $api_resource_id );
	}

	/**
	 * Adds the WooCommerce Subscription Grace Period expiration.
	 *
	 * @since 2.6
	 *
	 * @param int|object $order
	 *
	 * @return bool
	 */
	public function add_wc_subscription_expiration_by_order( $order ) {
		$api_resource_ids = WC_AM_API_RESOURCE_DATA_STORE()->get_api_resource_ids_by_order( $order );

		return $this->add_wc_subscription_expiration( $api_resource_ids );
	}

	/**
	 * Adds the WooCommerce Subscription Grace Period expiration.
	 *
	 * @since 2.6
	 *
	 * @param int|object $subscription
	 *
	 * @return bool
	 */
	public function add_wc_subscription_expiration_by_subscription( $subscription ) {
		$api_resource_ids = WC_AM_API_RESOURCE_DATA_STORE()->get_api_resource_ids_by_subscription( $subscription );

		return $this->add_wc_subscription_expiration( $api_resource_ids );
	}

	/**
	 * Adds the WooCommerce Subscription Grace Period expiration.
	 *
	 * @since 2.6
	 *
	 * @param object $api_resource_ids
	 *
	 * @return bool
	 */
	private function add_wc_subscription_expiration( $api_resource_ids ) {
		$result           = false;
		$expiration_dates = array();

		if ( ! empty( $api_resource_ids ) && ( is_array( $api_resource_ids ) || is_object( $api_resource_ids ) ) ) {
			foreach ( $api_resource_ids as $resource ) {
				$sub_id = WC_AM_API_RESOURCE_DATA_STORE()->get_sub_id_by_api_resource_id( $resource->api_resource_id );

				if ( ! empty( $sub_id ) ) {
					if ( WC_AM_SUBSCRIPTION()->has_end_date_by_sub( $sub_id ) ) {
						$end_date = WC_AM_SUBSCRIPTION()->get_subscription_time_by_sub_id( $sub_id );

						if ( is_numeric( $end_date ) && $end_date > 0 ) {
							$expiration_dates[ $resource->api_resource_id ] = absint( $end_date + $this->calculate_grace_period() );
						}
					} else {
						$expiration_dates[ $resource->api_resource_id ] = absint( WC_AM_ORDER_DATA_STORE()->get_current_time_stamp() + $this->calculate_grace_period() );
					}
				}
			}

			if ( ! empty( $expiration_dates ) ) {
				foreach ( $expiration_dates as $resource->api_resource_id => $expiration_date ) {
					$result = $this->insert( $resource->api_resource_id, $expiration_date );
				}
			}
		} else {
			$sub_id = WC_AM_API_RESOURCE_DATA_STORE()->get_sub_id_by_api_resource_id( $api_resource_ids );

			if ( ! empty( $sub_id ) ) {
				if ( WC_AM_SUBSCRIPTION()->has_end_date_by_sub( $sub_id ) ) {
					$end_date = WC_AM_SUBSCRIPTION()->get_subscription_time_by_sub_id( $sub_id );

					if ( is_numeric( $end_date ) && $end_date > 0 ) {
						$result = $this->insert( $api_resource_ids, absint( $end_date + $this->calculate_grace_period() ) );
					}
				} else {
					$result = $this->insert( $api_resource_ids, absint( WC_AM_ORDER_DATA_STORE()->get_current_time_stamp() + $this->calculate_grace_period() ) );
				}
			}
		}

		return $result;
	}

	/**
	 * Adds the Non WooCommerce Subscription Grace Period expiration.
	 *
	 * @since 2.6
	 *
	 * @param int $api_resource_id
	 *
	 * @return bool
	 */
	public function add_non_wc_subscription_expiration_by_api_resource_id( $api_resource_id ) {
		return $this->add_non_wc_subscription_expiration( $api_resource_id );
	}

	/**
	 * Adds the Non WooCommerce Subscription Grace Period expiration.
	 *
	 * @since 2.6
	 *
	 * @param int|object $order
	 *
	 * @return bool
	 */
	public function add_non_wc_subscription_expiration_by_order( $order ) {
		$api_resource_ids = WC_AM_API_RESOURCE_DATA_STORE()->get_api_resource_ids_by_order( $order );

		return $this->add_non_wc_subscription_expiration( $api_resource_ids );
	}

	/**
	 * Adds the Non WooCommerce Subscription Grace Period expiration.
	 *
	 * @since 2.6
	 *
	 * @param object|int $api_resource_id
	 *
	 * @return bool
	 */
	private function add_non_wc_subscription_expiration( $api_resource_ids ) {
		$result           = false;
		$expiration_dates = array();

		if ( ! empty( $api_resource_ids ) && ( is_array( $api_resource_ids ) || is_object( $api_resource_ids ) ) ) {
			foreach ( $api_resource_ids as $resource ) {
				$access_expires = WC_AM_API_RESOURCE_DATA_STORE()->get_access_expires_by_api_resource_id( $resource->api_resource_id );

				if ( is_numeric( $access_expires ) && $access_expires > 0 ) {
					$expiration_dates[ $resource->api_resource_id ] = absint( $access_expires + $this->calculate_grace_period() );
				} else {
					$expiration_dates[ $resource->api_resource_id ] = absint( WC_AM_ORDER_DATA_STORE()->get_current_time_stamp() + $this->calculate_grace_period() );
				}
			}

			if ( ! empty( $expiration_dates ) ) {
				foreach ( $expiration_dates as $resource->api_resource_id => $expiration_date ) {
					$result = $this->insert( $resource->api_resource_id, $expiration_date );
				}
			}
		} else {
			$access_expires = WC_AM_API_RESOURCE_DATA_STORE()->get_access_expires_by_api_resource_id( $api_resource_ids );

			if ( is_numeric( $access_expires ) && $access_expires > 0 ) {
				$result = $this->insert( $api_resource_ids, absint( $access_expires + $this->calculate_grace_period() ) );
			} else {
				$this->insert( $api_resource_ids, absint( WC_AM_ORDER_DATA_STORE()->get_current_time_stamp() + $this->calculate_grace_period() ) );
			}
		}

		return $result;
	}

	/**
	 * Delete the Non  WooCommerce Subscription and WooCommerce Subscription Grace Period expiration.
	 *
	 * @since 2.6
	 *
	 * @param int $api_resource_id
	 *
	 * @return bool
	 */
	public function delete_expiration_by_api_resource_id( $api_resource_id ) {
		return $this->delete_expiration( $api_resource_id );
	}

	/**
	 * Delete the Non  WooCommerce Subscription and WooCommerce Subscription Grace Period expiration.
	 *
	 * @since 2.6
	 *
	 * @param int|object $order
	 *
	 * @return bool
	 */
	public function delete_expiration_by_order( $order ) {
		$api_resource_ids = WC_AM_API_RESOURCE_DATA_STORE()->get_api_resource_ids_by_order( $order );

		return $this->delete_expiration( $api_resource_ids );
	}

	/**
	 * Delete the WooCommerce Subscription Grace Period expiration.
	 *
	 * @since 2.6
	 *
	 * @param int|object $subscription
	 *
	 * @return bool
	 */
	public function delete_wc_subscription_expiration_by_subscription( $subscription ) {
		$api_resource_ids = WC_AM_API_RESOURCE_DATA_STORE()->get_api_resource_ids_by_subscription( $subscription );

		return $this->delete_expiration( $api_resource_ids );
	}

	/**
	 * Delete the WooCommerce Subscription Grace Period expiration.
	 *
	 * @since 2.6
	 *
	 * @param object|int $api_resource_ids
	 *
	 * @return bool
	 */
	private function delete_expiration( $api_resource_ids ) {
		$result = false;

		if ( ! WC_AM_FORMAT()->empty( $api_resource_ids ) ) {
			if ( is_array( $api_resource_ids ) || is_object( $api_resource_ids ) ) {
				foreach ( $api_resource_ids as $resource ) {
					$result = $this->delete( (int) $resource->api_resource_id );
				}
			} else {
				$result = $this->delete( (int) $api_resource_ids );
			}
		}

		return $result;
	}

	/**
	 * Calculates the Expired Grace Period in seconds.
	 *
	 * @since 2.6
	 *
	 * @return int
	 */
	public function calculate_grace_period() {
		$array    = get_option( 'woocommerce_api_manager_grace_period' );
		$interval = 0;

		if ( ! empty( $array ) && is_array( $array ) ) {
			$number = $array[ 'number' ];
			$unit   = $array[ 'unit' ];

			if ( ! empty( $number ) ) {
				if ( $unit == 'days' ) {
					$interval = $number * DAY_IN_SECONDS;
				} elseif ( $unit == 'weeks' ) {
					$interval = $number * WEEK_IN_SECONDS;
				} elseif ( $unit == 'months' ) {
					$interval = $number * MONTH_IN_SECONDS;
				} elseif ( $unit == 'years' ) {
					$interval = $number * YEAR_IN_SECONDS;
				}
			}
		}

		return absint( $interval );
	}

	/**
	 * Returns all api_resource_ids.
	 *
	 * @since 2.6
	 *
	 * @return array
	 */
	public function get_all_api_resource_ids() {
		global $wpdb;

		$api_resource_ids = $wpdb->get_col( "
			SELECT DISTINCT api_resource_id
			FROM {$wpdb->prefix}" . $this->grace_period_table . "
		" );

		return ! WC_AM_FORMAT()->empty( $api_resource_ids ) ? $api_resource_ids : array();
	}

	/**
	 * Return total number of graces periods.
	 * COUNT(expr) only counts non-null values, whereas COUNT(*) also counts null values.
	 *
	 * @since 2.6.2
	 *
	 * @return int
	 */
	public function count() {
		global $wpdb;

		$count = $wpdb->get_var( "
			SELECT COUNT(api_resource_id)
			FROM {$wpdb->prefix}" . $this->grace_period_table . "
		" );

		return ! empty( $count ) ? (int) $count : 0;
	}

}
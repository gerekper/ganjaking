<?php
/**
 * List of order waiting to be registered as Abandoned
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAC_Carts_Waiting_Queue' ) ) {
	/**
	 * WooCommerce Active Campaign Queue of Waiting Orders
	 *
	 * Stores carts ready to be processed as Abandoned
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAC_Carts_Waiting_Queue {

		/**
		 * Insert a new record in waiting order table
		 *
		 * @param array $args Record to add.
		 *
		 * @return int Last entered ID
		 */
		public function create( $args ) {
			global $wpdb;

			$defaults = array(
				'email'    => '',
				'currency' => get_woocommerce_currency(),
				'cart'     => '',
				'ts'       => gmdate( 'Y-m-d H:i:s' ),
			);

			$args = $this->_process_args( wp_parse_args( $args, $defaults ) );

			if ( empty( $args['cart'] ) || empty( $args['email'] ) ) {
				return false;
			}

			$wpdb->insert( $wpdb->yith_wcac_waiting_orders, $args );

			return $wpdb->insert_id;
		}

		/**
		 * Get records from register table
		 *
		 * @param array $args array Array used to filter result set.
		 *
		 * @return array|object|null Result set
		 */
		public function read( $args = array() ) {
			global $wpdb;

			$defaults = array(
				'email'    => '',
				'currency' => '',
			);

			$args = array_filter( wp_parse_args( $args, $defaults ) );

			$query_args = array();
			$query      = "SELECT ID, email, currency, cart, ts FROM {$wpdb->yith_wcac_waiting_orders} WHERE 1=1";

			if ( ! empty( $args ) ) {
				foreach ( $args as $arg_name => $arg_value ) {
					$arg_type     = $this->_get_arg_type( $arg_name );
					$query        .= " AND {$arg_name} = {$arg_type}";
					$query_args[] = $arg_value;
				}
			}

			$results = $wpdb->get_results( $wpdb->prepare( $query, $query_args ), ARRAY_A ); // phpcs:ignore
			$results = $this->_process_results( $results );

			return $results;
		}

		/**
		 * Update a set of records into register table
		 *
		 * @param array $args  Array of data to update.
		 * @param array $where Array used to filter set of records to update.
		 *
		 * @return false|int Number of records updated, or false on failure
		 */
		public function update( $args, $where ) {
			global $wpdb;

			$defaults = array(
				'ID'       => 0,
				'email'    => '',
				'currency' => '',
				'cart'     => '',
				'ts'       => '',
			);

			$args        = $this->_process_args( array_filter( wp_parse_args( $args, $defaults ) ) );
			$args_format = array();

			if ( ! empty( $args ) ) {
				foreach ( $args as $arg_name => $arg_value ) {
					$arg_type      = $this->_get_arg_type( $arg_name );
					$args_format[] = $arg_type;
				}
			}

			$where        = array_filter( wp_parse_args( $where, $defaults ) );
			$where_format = array();

			if ( ! empty( $where ) ) {
				foreach ( $where as $where_name => $where_value ) {
					$where_type     = $this->_get_arg_type( $where_name );
					$where_format[] = $where_type;
				}
			}

			return $wpdb->update( $wpdb->yith_wcac_waiting_orders, $args, $where, $args_format, $where_format );
		}

		/**
		 * Delete records from register table
		 *
		 * @param array $where Array used to filter records to delete.
		 *
		 * @return false|int Number of deleted records, or false on failure
		 */
		public function delete( $where ) {
			global $wpdb;

			$defaults = array(
				'ID'       => 0,
				'email'    => '',
				'currency' => '',
			);

			$where        = array_filter( wp_parse_args( $where, $defaults ) );
			$where_format = array();

			if ( ! empty( $where ) ) {
				foreach ( $where as $where_name => $where_value ) {
					$where_type     = $this->_get_arg_type( $where_name );
					$where_format[] = $where_type;
				}
			}

			return $wpdb->delete( $wpdb->yith_wcac_waiting_orders, $where, $where_format );
		}

		/* === UTILITY === */

		/**
		 * Check if a specific cart is already waiting in the queue
		 *
		 * @param string $email Customer email to check.
		 *
		 * @return array|bool Items details, if item was processed; false otherwise
		 */
		public function is_cart_waiting( $email ) {
			$res = $this->read(
				array(
					'email' => $email,
				)
			);

			return ! empty( $res ) ? array_shift( $res ) : false;
		}

		/**
		 * Register a new item as waiting
		 *
		 * @param string $email     Customer email.
		 * @param string $cart      Cart to register.
		 * @param string $currency  Current currency.
		 *
		 * @return int Last entered ID
		 */
		public function add_item( $email, $cart, $currency ) {
			return $this->create(
				array(
					'cart'     => $cart,
					'email'    => $email,
					'currency' => $currency,
				)
			);
		}

		/**
		 * Register a new item as waiting, if it wasn't registered before, otherwise update it
		 *
		 * @param string $email     Customer email.
		 * @param string $cart      Cart to register.
		 * @param string $currency  Current currency.
		 *
		 * @return false|int Last entered id if new item; number of updated rows or false on failure if existing item
		 */
		public function maybe_add_item( $email, $cart, $currency ) {
			if ( $this->is_cart_waiting( $email ) ) {
				return $this->update(
					array(
						'cart'     => $cart,
						'currency' => $currency,
						'ts'       => gmdate( 'Y-m-d H:i:s' ),
					),
					array(
						'email' => $email,
					)
				);
			} else {
				return $this->add_item( $email, $cart, $currency );
			}
		}

		/**
		 * Update "ts" record of an item
		 *
		 * @param string   $email Customer email.
		 * @param bool|int $time  Timestamp to register; if false, use current timestamp.
		 *
		 * @return false|int Status of the update process
		 */
		public function last_updated( $email, $time = false ) {
			$time = gmdate( 'Y-m-d H:i:s', $time );

			return $this->update(
				array(
					'ts' => $time,
				),
				array(
					'email' => $email,
				)
			);
		}

		/**
		 * Remove an item from the table, and returns removed item
		 *
		 * @param string $email Customer email.
		 *
		 * @return false|array Item just removed, or false.
		 */
		public function remove_item( $email ) {
			$item = $this->is_cart_waiting( $email );

			if ( $item ) {
				$this->delete(
					array(
						'email' => $email,
					)
				);
			}

			return $item;
		}

		/**
		 * Retrieves all abandoned cart, and removes them from the queue
		 *
		 * @param int $threshold Threshold timestamp.
		 * @return array Array of retrieved elements
		 */
		public function pop_expired_items( $threshold ) {
			global $wpdb;

			$items = $wpdb->get_results( $wpdb->prepare( "SELECT ID, email, currency, cart, ts FROM {$wpdb->yith_wcac_waiting_orders} WHERE ts < %s", gmdate( 'Y-m-d H:i:s', $threshold ) ), ARRAY_A );

			if ( empty( $items ) ) {
				return array();
			}

			// remove expired items from the queue.
			$item_ids = wp_list_pluck( $items, 'ID' );
			$item_ids = implode( ',', $item_ids );

			$wpdb->query( esc_sql( "DELETE FROM {$wpdb->yith_wcac_waiting_orders} WHERE ID IN ({$item_ids})" ) );

			// process results.
			$items = $this->_process_results( $items );

			return $items;
		}

		/**
		 * Checks whether queue is currently empty
		 *
		 * @return bool Whether queue is empty or not
		 */
		public function is_empty() {
			global $wpdb;

			$count = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->yith_wcac_waiting_orders}" );

			return ! $count;
		}

		/**
		 * Truncate register table
		 *
		 * @return void
		 */
		public function truncate() {
			global $wpdb;

			$wpdb->query( "TRUNCATE {$wpdb->yith_wcac_waiting_orders}" );
		}

		/**
		 * Returns type of any supported param for register table
		 *
		 * @param string $param Param.
		 *
		 * @return string Param type
		 */
		protected function _get_arg_type( $param ) {
			switch ( $param ) {
				case 'ID':
					return '%d';
				case 'cart':
				case 'currency':
				case 'email':
				case 'ts':
				default:
					return '%s';
			}
		}

		/**
		 * Process results set, applying required post process
		 *
		 * @param array $results Results to process.
		 * @return array Processed results
		 */
		protected function _process_results( $results ) {
			if ( ! empty( $results ) ) {
				foreach ( $results as & $item ) {
					$item = array_map( 'maybe_unserialize', $item );
				}
			}

			return $results;
		}

		/**
		 * Process input values, applying required pre process
		 *
		 * @param array $args Args to process.
		 * @return array Processed results
		 */
		protected function _process_args( $args ) {
			return array_map( 'maybe_serialize', $args );
		}
	}
}
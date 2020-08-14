<?php
/**
 * Store register class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMC_Store' ) ) {
	/**
	 * WooCommerce MailChimp Store Register
	 *
	 * Stores elements already processed by the system, and MC IDs
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMC_Store_Register {

		/**
		 * Insert a new record in register table
		 *
		 * @param $args array Record to add
		 *
		 * @return int Last entered ID
		 */
		public function create( $args ) {
			global $wpdb;

			$defaults = array(
				'item_type'    => 'order',
				'item_id'      => 0,
				'last_updated' => date( 'Y-m-d H:i:s' )
			);

			$args          = wp_parse_args( $args, $defaults );
			$args['mc_id'] = YITH_WCMC_Store()->get_object_uniqid( $args['item_type'], $args['item_id'] );

			$wpdb->insert( $wpdb->yith_wcmc_register, $args );

			return $wpdb->insert_id;
		}

		/**
		 * Get records from register table
		 *
		 * @param array $args array Array used to filter result set
		 *
		 * @return array|object|null Result set
		 */
		public function read( $args = array() ) {
			global $wpdb;

			$defaults = array(
				'item_type' => '',
				'item_id'   => 0,
				'mc_id'     => ''
			);

			$args = array_filter( wp_parse_args( $args, $defaults ) );

			$query_args = array();
			$query      = "SELECT ID, item_type, item_id, mc_id, last_updated FROM {$wpdb->yith_wcmc_register} WHERE 1=1";

			if ( ! empty( $args ) ) {
				foreach ( $args as $arg_name => $arg_value ) {
					$arg_type     = $this->_get_arg_type( $arg_name );
					$query        .= " AND {$arg_name} = {$arg_type}";
					$query_args[] = $arg_value;
				}
			}

			return $wpdb->get_results( $wpdb->prepare( $query, $query_args ), ARRAY_A );
		}

		/**
		 * Update a set of records into register table
		 *
		 * @param $args  array Array of data to update
		 * @param $where array Array used to filter set of records to update
		 *
		 * @return false|int Number of records updated, or false on failure
		 */
		public function update( $args, $where ) {
			global $wpdb;

			$defaults = array(
				'ID'           => 0,
				'item_type'    => '',
				'item_id'      => 0,
				'mc_id'        => '',
				'last_updated' => ''
			);

			$args        = array_filter( wp_parse_args( $args, $defaults ) );
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

			return $wpdb->update( $wpdb->yith_wcmc_register, $args, $where, $args_format, $where_format );
		}

		/**
		 * Delete records from register table
		 *
		 * @param $where array Array used to filter records to delete
		 *
		 * @return false|int Number of deleted records, or false on failure
		 */
		public function delete( $where ) {
			global $wpdb;

			$defaults = array(
				'ID'        => 0,
				'item_type' => '',
				'item_id'   => 0,
				'mc_id'     => ''
			);

			$where        = array_filter( wp_parse_args( $where, $defaults ) );
			$where_format = array();

			if ( ! empty( $where ) ) {
				foreach ( $where as $where_name => $where_value ) {
					$where_type     = $this->_get_arg_type( $where_name );
					$where_format[] = $where_type;
				}
			}

			return $wpdb->delete( $wpdb->yith_wcmc_register, $where, $where_format );
		}

		/* === UTILITY === */

		/**
		 * Check if a specific item was already processed
		 *
		 * @param $item_id   int Item id
		 * @param $item_type string Item type (product/order/etc...)
		 *
		 * @return bool Whether item was processed or not
		 */
		public function is_item_processed( $item_id, $item_type ) {
			$res = $this->read( array(
				'item_type' => $item_type,
				'item_id'   => $item_id
			) );

			return ! empty( $res );
		}

		/**
		 * Register a new item as processed
		 *
		 * @param $item_id   int Item id
		 * @param $item_type string Item type (product/order/etc...)
		 *
		 * @return int Last entered ID
		 */
		public function add_item( $item_id, $item_type ) {
			return $this->create( array(
				'item_id'   => $item_id,
				'item_type' => $item_type
			) );
		}

		/**
		 * Register a new item as processed, if it wasn't registered before, otherwise update it
		 *
		 * @param $item_id   int Item id
		 * @param $item_type string Item type (product/order/etc...)
		 *
		 * @return false|int Last entered id if new item; number of updated rows or false on failure if existing item
		 */
		public function maybe_add_item( $item_id, $item_type ) {
			if ( $this->is_item_processed( $item_id, $item_type ) ) {
				return $this->update(
					array(
						'last_updated' => date( 'Y-m-d H:i:s' )
					),
					array(
						'item_id'   => $item_id,
						'item_type' => $item_type
					)
				);
			} else {
				return $this->create( array(
					'item_id'   => $item_id,
					'item_type' => $item_type
				) );

			}
		}

		/**
		 * Returns MailChimp's id of a registered item
		 *
		 * @param $item_id   int Item id
		 * @param $item_type string Item type (product/order/etc...)
		 *
		 * @return bool|string False if no item was found; MailChimp ID otherwise
		 */
		public function get_item_mc_id( $item_id, $item_type ) {
			$res = $this->read( array(
				'item_type' => $item_type,
				'itemd_id'  => $item_id
			) );

			if ( ! empty( $res ) && isset( $res['mc_id'] ) ) {
				return $res['mc_id'];
			}

			return false;
		}

		/**
		 * Update "last_updated" record of an item
		 *
		 * @param $item_id   int Item id
		 * @param $item_type string Item type (product/order/etc...)
		 * @param $time      bool|int Timestamp to register; if false, use current timestamp
		 *
		 * @return false|int Status of the update process
		 */
		public function last_updated( $item_id, $item_type, $time = false ) {
			if ( ! $time ) {
				$time = time();
			}

			return $this->update(
				array(
					'last_updated' => date( 'Y-m-d H:i:s', $time )
				),
				array(
					'item_id'   => $item_id,
					'item_type' => $item_type
				)
			);
		}

		/**
		 * Remove an item from the table
		 *
		 * @param $item_id   int Item id
		 * @param $item_type string Item type (product/order/etc...)
		 *
		 * @return false|int Status of the delete process
		 */
		public function remove_item( $item_id, $item_type ) {
			return $this->delete( array(
				'item_id'   => $item_id,
				'item_type' => $item_type
			) );
		}

		/**
		 * Truncate register table
		 *
		 * @return void
		 */
		public function truncate() {
			global $wpdb;

			$wpdb->query( "TRUNCATE {$wpdb->yith_wcmc_register}" );
		}

		/**
		 * Returns type of any supported param for register table
		 *
		 * @param $param string Param
		 *
		 * @return string Param type
		 */
		protected function _get_arg_type( $param ) {
			switch ( $param ) {
				case 'ID':
					return '%d';
				case 'item_type':
					return '%s';
				case 'item_id':
					return '%d';
				case 'mc_id':
					return '%s';
				case 'last_updated':
					return '%s';
				default:
					return '%s';
			}
		}
	}
}
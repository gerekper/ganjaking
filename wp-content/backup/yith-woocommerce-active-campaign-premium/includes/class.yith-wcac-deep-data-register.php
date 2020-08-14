<?php
/**
 * Store register class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAC_Deep_Data_Register' ) ) {
	/**
	 * WooCommerce Active Campaign Store Register
	 *
	 * Stores elements already processed by the system, and MC IDs
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAC_Deep_Data_Register {

		/**
		 * Insert a new record in register table
		 *
		 * @param array $args Record to add.
		 *
		 * @return int Last entered ID
		 */
		public function create( $args ) {
			global $wpdb;

			$defaults = array(
				'item_type'    => 'order',
				'item'         => '',
				'last_updated' => gmdate( 'Y-m-d H:i:s' ),
				'ac_id'        => '',
			);

			$args = wp_parse_args( $args, $defaults );

			if ( empty( $args['ac_id'] ) ) {
				return false;
			}

			$wpdb->insert( $wpdb->yith_wcac_register, $args );

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
				'item_type' => '',
				'item'      => '',
				'ac_id'     => '',
			);

			$args = array_filter( wp_parse_args( $args, $defaults ) );

			$query_args = array();
			$query      = "SELECT ID, item_type, item, ac_id, last_updated FROM {$wpdb->yith_wcac_register} WHERE 1=1";

			if ( ! empty( $args ) ) {
				foreach ( $args as $arg_name => $arg_value ) {
					$arg_type     = $this->_get_arg_type( $arg_name );
					$query        .= " AND {$arg_name} = {$arg_type}";
					$query_args[] = $arg_value;
				}
			}

			return $wpdb->get_results( $wpdb->prepare( $query, $query_args ), ARRAY_A ); // phpcs:ignore
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
				'ID'           => 0,
				'item_type'    => '',
				'item'         => '',
				'ac_id'        => '',
				'last_updated' => '',
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

			return $wpdb->update( $wpdb->yith_wcac_register, $args, $where, $args_format, $where_format );
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
				'ID'        => 0,
				'item_type' => '',
				'item'      => 0,
				'ac_id'     => '',
			);

			$where        = array_filter( wp_parse_args( $where, $defaults ) );
			$where_format = array();

			if ( ! empty( $where ) ) {
				foreach ( $where as $where_name => $where_value ) {
					$where_type     = $this->_get_arg_type( $where_name );
					$where_format[] = $where_type;
				}
			}

			return $wpdb->delete( $wpdb->yith_wcac_register, $where, $where_format );
		}

		/* === UTILITY === */

		/**
		 * Check if a specific item was already processed
		 *
		 * @param string $item      Item.
		 * @param string $item_type Item type (product/order/etc...).
		 *
		 * @return array|bool Items details, if item was processed; false otherwise
		 */
		public function is_item_processed( $item, $item_type ) {
			$res = $this->read(
				array(
					'item_type' => $item_type,
					'item'      => $item,
				)
			);

			return ! empty( $res ) ? array_shift( $res ) : false;
		}

		/**
		 * Register a new item as processed
		 *
		 * @param string $item      Item.
		 * @param string $item_type Item type (product/order/etc...).
		 * @param string $ac_id     Id on Active Campaign db.
		 *
		 * @return int Last entered ID
		 */
		public function add_item( $item, $item_type, $ac_id ) {
			return $this->create(
				array(
					'item'      => $item,
					'item_type' => $item_type,
					'ac_id'     => $ac_id,
				)
			);
		}

		/**
		 * Register a new item as processed, if it wasn't registered before, otherwise update it
		 *
		 * @param string $item      Item.
		 * @param string $item_type Item type (product/order/etc...).
		 * @param string $ac_id     Id on Active Campaign database.
		 *
		 * @return false|int Last entered id if new item; number of updated rows or false on failure if existing item
		 */
		public function maybe_add_item( $item, $item_type, $ac_id ) {
			if ( $this->is_item_processed( $item, $item_type ) ) {
				return $this->update(
					array(
						'ac_id'        => $ac_id,
						'last_updated' => gmdate( 'Y-m-d H:i:s' ),
					),
					array(
						'item'      => $item,
						'item_type' => $item_type,
					)
				);
			} else {
				return $this->create(
					array(
						'item'      => $item,
						'item_type' => $item_type,
						'ac_id'     => $ac_id,
					)
				);

			}
		}

		/**
		 * Returns MailChimp's id of a registered item
		 *
		 * @param string $item      Item.
		 * @param string $item_type Item type (product/order/etc...).
		 *
		 * @return bool|string False if no item was found; MailChimp ID otherwise
		 */
		public function get_item_ac_id( $item, $item_type ) {
			$res = $this->read(
				array(
					'item_type' => $item_type,
					'item'  => $item,
				)
			);

			if ( ! empty( $res ) && isset( $res['ac_id'] ) ) {
				return $res['ac_id'];
			}

			return false;
		}

		/**
		 * Update "last_updated" record of an item
		 *
		 * @param string   $item      Item.
		 * @param string   $item_type Item type (product/order/etc...).
		 * @param bool|int $time      Timestamp to register; if false, use current timestamp.
		 *
		 * @return false|int Status of the update process
		 */
		public function last_updated( $item, $item_type, $time = false ) {
			if ( ! $time ) {
				$time = time();
			}

			return $this->update(
				array(
					'last_updated' => gmdate( 'Y-m-d H:i:s', $time ),
				),
				array(
					'item'      => $item,
					'item_type' => $item_type,
				)
			);
		}

		/**
		 * Remove an item from the table
		 *
		 * @param int    $item      Item id.
		 * @param string $item_type Item type (product/order/etc...).
		 *
		 * @return false|int Status of the delete process
		 */
		public function remove_item( $item, $item_type ) {
			return $this->delete(
				array(
					'item'      => $item,
					'item_type' => $item_type,
				)
			);
		}

		/**
		 * Truncate register table
		 *
		 * @return void
		 */
		public function truncate() {
			global $wpdb;

			$wpdb->query( "TRUNCATE {$wpdb->yith_wcac_register}" );
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
				case 'item_type':
					return '%s';
				case 'item':
					return '%s';
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
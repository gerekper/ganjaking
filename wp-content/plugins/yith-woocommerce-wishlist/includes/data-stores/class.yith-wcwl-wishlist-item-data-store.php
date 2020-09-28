<?php
/**
 * Wishlist data store
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Wishlist_Item_Data_Store' ) ) {
	/**
	 * This class implements CRUD methods for wishlists' items
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Wishlist_Item_Data_Store{

		/**
		 * Create a new wishlist item in the database.
		 *
		 * @since 3.0.0
		 * @param \YITH_WCWL_Wishlist_Item $item Wishlist item object.
		 */
		public function create( &$item ) {
			global $wpdb;

			if( ! ( $product_id = $item->get_original_product_id() ) || ! ( $wishlist_id = $item->get_wishlist_id() ) ){
				return;
			}

			if( $item_id = YITH_WCWL_Wishlist_Factory::get_wishlist_item_by_product_id( $wishlist_id, $product_id ) ){
				$item->set_id( $item_id );

				$this->update( $item );
				return;
			}

			$columns = array(
				'prod_id' => '%d',
				'quantity' => '%d',
				'wishlist_id' => '%d',
				'position' => '%d',
				'original_price' => '%d',
				'original_currency' => '%s',
				'on_sale' => '%s'
			);
			$values = array(
				apply_filters( 'yith_wcwl_adding_to_wishlist_product_id', $product_id ),
				apply_filters( 'yith_wcwl_adding_to_wishlist_quantity', $item->get_quantity() ),
				apply_filters( 'yith_wcwl_adding_to_wishlist_wishlist_id', $wishlist_id ),
				apply_filters( 'yith_wcwl_adding_to_wishlist_position', $item->get_position() ),
				apply_filters( 'yith_wcwl_adding_to_wishlist_original_price', $item->get_product_price() ),
				apply_filters( 'yith_wcwl_adding_to_wishlist_original_currency', $item->get_original_currency() ),
				apply_filters( 'yith_wcwl_adding_to_wishlist_on_sale', $item->is_on_sale() ),
			);

			if ( $user_id = $item->get_user_id() ) {
				$columns['user_id'] = '%d';
				$values[] = apply_filters( 'yith_wcwl_adding_to_wishlist_user_id', $user_id );
			}

			if( $date_added = $item->get_date_added( 'edit' ) ){
				$columns['dateadded'] = 'FROM_UNIXTIME( %d )';
				$values[] = apply_filters( 'yith_wcwl_adding_to_wishlist_date_added', $date_added->getTimestamp() );
			}

			$query_columns = implode( ', ', array_map( 'esc_sql', array_keys( $columns ) ) );
			$query_values = implode( ', ', array_values( $columns ) );
			$query = "INSERT INTO {$wpdb->yith_wcwl_items} ( {$query_columns} ) VALUES ( {$query_values} ) ";

			$res = $wpdb->query( $wpdb->prepare( $query, $values ) );

			if( $res ) {
				$item->set_id( $wpdb->insert_id );
				$item->apply_changes();
				$this->clear_cache( $item );

				do_action( 'yith_wcwl_new_wishlist_item', $item->get_id(), $item, $item->get_wishlist_id() );
			}
		}

		/**
		 * Read/populate data properties specific to this order item.
		 *
		 * @param WC_Order_Item_Product $item Product order item object.
		 *
		 * @throws Exception When wishlist item is not found
		 * @since 3.0.0
		 */
		public function read( &$item ) {
			global $wpdb;

			$item->set_defaults();

			// Get from cache if available.
			$data = wp_cache_get( 'item-' . $item->get_id(), 'wishlist-items' );

			if ( false === $data ) {
				$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->yith_wcwl_items} WHERE ID = %d LIMIT 1;", $item->get_id() ) );
				wp_cache_set( 'item-' . $item->get_id(), $data, 'wishlist-items' );
			}

			if ( ! $data ) {
				throw new Exception( __( 'Invalid wishlist item.', 'yith-woocommerce-wishlist' ) );
			}

			$item->set_props(
				array(
					'wishlist_id' => $data->wishlist_id,
					'product_id' => $data->prod_id,
					'user_id' => $data->user_id,
					'quantity' => $data->quantity,
					'date_added' => $data->dateadded,
					'position' => $data->position,
					'original_price' => $data->original_price,
					'original_currency' => $data->original_currency,
					'on_sale' => $data->on_sale
				)
			);
			$item->set_object_read( true );
		}

		/**
		 * Update a wishlist item in the database.
		 *
		 * @since 3.0.0
		 * @param YITH_WCWL_Wishlist_Item $item Wishlist item object.
		 */
		public function update( &$item ) {
			global $wpdb;

			if( ! $item->get_id() ){
				return;
			}

			$changes = $item->get_changes();

			if ( array_intersect( array( 'quantity', 'wishlist_id', 'product_id', 'user_id', 'position', 'on_sale', 'date_added' ), array_keys( $changes ) ) ) {
				$columns = array(
					'quantity' => '%d',
					'wishlist_id' => '%d',
					'prod_id' => '%d',
					'position' => '%d',
					'on_sale' => '%d',
					'dateadded' => 'FROM_UNIXTIME( %d )',
					'user_id' => $item->get_user_id() ? '%d' : 'NULL',
				);
				$values = array(
					$item->get_quantity(),
					$item->get_wishlist_id(),
					$item->get_original_product_id(),
					$item->get_position(),
					$item->is_on_sale(),
					$item->get_date_added( 'edit' ) ? $item->get_date_added( 'edit' )->getTimestamp() : time(),
				);

				if ( $user_id = $item->get_user_id() ) {
					$values[] = $user_id;
				}

				$this->update_raw( $columns, $values, array( 'ID' => '%d' ), array( $item->get_id() ) );
			}

			$item->apply_changes();
			$this->clear_cache( $item );

			do_action( 'yith_wcwl_update_wishlist_item', $item->get_id(), $item, $item->get_wishlist_id() );
		}

		/**
		 * Remove a wishlist item from the database.
		 *
		 * @since 3.0.0
		 * @param \YITH_WCWL_Wishlist_Item $item Wishlist item object.
		 */
		public function delete( &$item ) {
			global $wpdb;

			$id = $item->get_id();

			if( ! $id ){
				return;
			}

			do_action( 'yith_wcwl_before_delete_wishlist_item', $item->get_id() );

			$wpdb->delete( $wpdb->yith_wcwl_items, array( 'ID' => $item->get_id() ) );

			do_action( 'yith_wcwl_delete_wishlist_item', $item->get_id() );

			$item->set_id( 0 );
			$this->clear_cache( $item );
		}

		/**
		 * Retrieves wishlist items that match a set of conditions
		 *
		 * @param $args mixed Arguments array; it may contains any of the following:<br/>
		 * [<br/>
		 *     'user_id'             // Owner of the wishlist; default to current user logged in (if any), or false for cookie wishlist<br/>
		 *     'product_id'          // Product to search in the wishlist<br/>
		 *     'wishlist_id'         // wishlist_id for a specific wishlist, false for default, or all for any wishlist<br/>
		 *     'wishlist_token'      // wishlist token, or false as default<br/>
		 *     'wishlist_visibility' // all, visible, public, shared, private<br/>
		 *     'is_default' =>       // whether searched wishlist should be default one <br/>
		 *     'id' => false,        // only for table select<br/>
		 *     'limit' => false,     // pagination param; number of items per page. 0 to get all items<br/>
		 *     'offset' => 0         // pagination param; offset for the current set. 0 to start from the first item<br/>
		 * ]
		 *
		 * @return YITH_WCWL_Wishlist_Item[]
		 */
		public function query( $args = array() ) {
			global $wpdb;

			$default = array(
				'user_id' => ( is_user_logged_in() ) ? get_current_user_id(): false,
				'session_id' => ( ! is_user_logged_in() ) ? YITH_WCWL_Session()->get_session_id() : false,
				'product_id' => false,
				'wishlist_id' => false, //wishlist_id for a specific wishlist, false for default, or all for any wishlist
				'wishlist_token' => false,
				'wishlist_visibility' => apply_filters( 'yith_wcwl_wishlist_visibility_string_value', 'all'), // all, visible, public, shared, private
				'is_default' => false,
				'on_sale' => false,
				'id' => false, // only for table select
				'limit' => false,
				'offset' => 0,
				'orderby' => '',
				'order' => 'DESC',
			);

			$args = wp_parse_args( $args, $default );
			extract( $args );

			$sql = "SELECT SQL_CALC_FOUND_ROWS i.*
                    FROM `{$wpdb->yith_wcwl_items}` AS i
                    LEFT JOIN {$wpdb->yith_wcwl_wishlists} AS l ON l.`ID` = i.`wishlist_id`
                    INNER JOIN {$wpdb->posts} AS p ON p.ID = i.prod_id 
                    WHERE 1 AND p.post_type IN ( %s, %s ) AND p.post_status = %s";

			// remove hidden products from result
			$hidden_products = yith_wcwl_get_hidden_products();

			if( ! empty( $hidden_products ) && apply_filters( 'yith_wcwl_remove_hidden_products_via_query', true ) ) {
				$sql .= " AND p.ID NOT IN ( " . implode( ', ', array_filter( $hidden_products, 'esc_sql' ) ) . " )";
			}

			$sql_args = array(
				'product',
				'product_variation',
				'publish'
			);

			if( ! empty( $user_id ) ){
				$sql .= " AND i.`user_id` = %d";
				$sql_args[] = $user_id;
			}

			if( ! empty( $session_id ) ){
				$sql .= " AND l.`session_id` = %s AND l.`expiration` > NOW()";
				$sql_args[] = $session_id;
			}

			if( ! empty( $product_id ) ){
				$product_id = yith_wcwl_object_id( $product_id, 'product', true, 'default' );

				$sql .= " AND i.`prod_id` = %d";
				$sql_args[] = $product_id;
			}

			if( ! empty( $wishlist_id ) && $wishlist_id != 'all' ){
				$sql .= " AND i.`wishlist_id` = %d";
				$sql_args[] = $wishlist_id;
			}
			elseif( ( empty( $wishlist_id ) ) && empty( $wishlist_token ) && empty( $is_default ) ){
				$sql .= " AND i.`wishlist_id` IS NULL";
			}

			if( ! empty( $wishlist_token ) ){
				$sql .= " AND l.`wishlist_token` = %s";
				$sql_args[] = $wishlist_token;
			}

			if( ! empty( $wishlist_visibility ) && $wishlist_visibility != 'all' ){
				switch( $wishlist_visibility ){
					case 'visible':
						$sql .= " AND ( l.`wishlist_privacy` = %d OR l.`wishlist_privacy` = %d )";
						$sql_args[] = 0;
						$sql_args[] = 1;
						break;
					case 'public':
						$sql .= " AND l.`wishlist_privacy` = %d";
						$sql_args[] = 0;
						break;
					case 'shared':
						$sql .= " AND l.`wishlist_privacy` = %d";
						$sql_args[] = 1;
						break;
					case 'private':
						$sql .= " AND l.`wishlist_privacy` = %d";
						$sql_args[] = 2;
						break;
					default:
						$sql .= " AND l.`wishlist_privacy` = %d";
						$sql_args[] = 0;
						break;
				}
			}

			if( ! empty( $is_default ) ){
				YITH_WCWL_Wishlist_Factory::generate_default_wishlist();

				$sql .= " AND l.`is_default` = %d";
				$sql_args[] = $is_default;
			}

			if( isset( $on_sale ) && $on_sale !== false ){
				$sql .= " AND i.`on_sale` = %d";
				$sql_args[] = $on_sale;
			}

			if( ! empty( $id ) ){
				$sql .= " AND `i.ID` = %d";
				$sql_args[] = $id;
			}

			$sql .= " GROUP BY i.prod_id, l.ID";

			$sql .= " ORDER BY ";

			if( ! empty( $orderby ) ){
				$order = ! empty( $order ) ? $order : 'DESC';
				$sql .= "i." .  esc_sql( $orderby ) . " " . esc_sql( $order ) . ", ";
			}

			$sql .= "  i.position ASC";

			if( ! empty( $limit ) && isset( $offset ) ){
				$sql .= " LIMIT %d, %d";
				$sql_args[] = $offset;
				$sql_args[] = $limit;
			}

			$items = $wpdb->get_results( $wpdb->prepare( $sql, $sql_args ) );

			/**
			 * This filter was added to allow developer remove hidden products using a foreach loop, instead of the query
			 * It is required when the store contains a huge number of hidden products, and the resulting query would fail
			 * to be submitted to DBMS because of its size
			 *
			 * This code requires reasonable amount of products in the wishlist
			 * A great number of products retrieved from the main query could easily degrade performance of the overall system
			 *
			 * @since 3.0.7
			 */
			if( ! empty( $hidden_products ) && ! empty( $items ) && ! apply_filters( 'yith_wcwl_remove_hidden_products_via_query', true ) ){
				foreach( $items as $item_id => $item ){
					if( ! in_array( $item->prod_id, $hidden_products ) ){
						continue;
					}

					unset( $items[ $item_id ] );
				}
			}

			if( ! empty( $items ) ){
				$items = array_map( array( 'YITH_WCWL_Wishlist_Factory', 'get_wishlist_item' ), $items );
			} else {
				$items = array();
			}

			return apply_filters( 'yith_wcwl_get_products', $items, $args );
		}

		/**
		 * Counts items that matches
		 *
		 * @param $args array Same parameters allowed for {@see query} method
		 * @return int Count of items
		 */
		public function count( $args = array() ) {
			return count( $this->query( $args ) );
		}

		/**
		 * Query items table to retrieve distinct products added to wishlist, with count of occurrences
		 *
		 * @param $args mixed Arguments array; it may contains any of the following:<br/>
		 * [<br/>
		 *     'search' => '',       // search string; will be matched against product name<br/>
		 *     'orderby' => 'ID',    // order param; a valid column in the result set<br/>
		 *     'order' => 'desc',    // order param; asc or desc<br/>
 		 *     'limit' => false,     // pagination param; number of items per page. 0 to get all items<br/>
		 *     'offset' => 0         // pagination param; offset for the current set. 0 to start from the first item<br/>
		 * ]
		 * @return mixed Result set
		 */
		public function query_products( $args ) {
			global $wpdb;

			$default = array(
				'search' => '',
				'limit' => false,
				'offset' => 0,
				'orderby' => 'ID',
				'order' => 'DESC',
			);

			$args = wp_parse_args( $args, $default );
			extract( $args );

			$sql = "SELECT
		            DISTINCT i.prod_id AS id,
		            p.post_title AS post_title,
		            i2.wishlist_count AS wishlist_count
		            FROM {$wpdb->yith_wcwl_items} AS i
		            INNER JOIN {$wpdb->posts} AS p ON p.ID = i.prod_id
		            LEFT JOIN ( 
		                SELECT 
		                COUNT( DISTINCT ID ) AS wishlist_count, 
                        prod_id 
		                FROM {$wpdb->yith_wcwl_items} 
		                GROUP BY prod_id 
	                ) AS i2 ON p.ID = i2.prod_id
		            WHERE 1=1 AND p.post_status = %s";

			$sql_args = array( 'publish' );

			if ( ! empty( $product_id ) ) {
				$sql       .= ' AND i.prod_id = %d';
				$sql_args[] = $product_id;
			}

			if ( ! empty( $search ) ) {
				$sql .= ' AND p.post_title LIKE %s';
				$sql_args[] = '%' . $search . '%';
			}

			if ( ! empty( $args['interval'] ) && is_array( $args['interval'] ) && ( isset( $args['interval']['start_date'] ) || isset( $args['interval']['end_date'] ) ) ) {
				if ( ! empty( $args['interval']['start_date'] ) ) {
					$sql       .= ' AND i.dateadded >= %s';
					$sql_args[] = $args['interval']['start_date'];
				}

				if ( ! empty( $args['interval']['end_date'] ) ) {
					$sql       .= ' AND i.dateadded <= %s';
					$sql_args[] = $args['interval']['end_date'];
				}
			}

			if ( ! empty( $orderby ) ) {
				$order = ! empty( $order ) ? $order : 'DESC';
				$sql .= ' ORDER BY ' . esc_sql( $orderby ) . ' ' . esc_sql( $order );
			}

			if ( ! empty( $limit ) && isset( $offset ) ) {
				$sql .= ' LIMIT %d, %d';
				$sql_args[] = $offset;
				$sql_args[] = $limit;
			}

			$items = $wpdb->get_results( $wpdb->prepare( $sql, $sql_args ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			return $items;
		}

		/**
		 * Counts total number of distinct products added to wishlist
		 *
		 * @param $args array Same parameters allowed for {@see query_products} method
		 * @return int Count of items
		 */
		public function count_products( $args ) {
			return count( $this->query_products( $args ) );
		}

		/**
		 * Counts how many distinct users added a product in wishlist
		 *
		 * @param $product_id int Product id
		 * @return int Count of times product was added to wishlist
		 */
		public function count_times_added( $product_id, $user = false ) {
			global $wpdb;

			$query_args = array();
			$user_condition = '';

			if( $user ){
				if( 'current' == $user ){
					if( is_user_logged_in() ){
						$user_condition = " AND l.`user_id` = %d";
						$query_args[] = get_current_user_id();
					}
					else{
						$user_condition = " AND l.`session_id` = %s";
						$query_args[] = YITH_WCWL_Session()->get_session_id();
					}
				}
				elseif( is_int( $user ) ){
					$user_condition = " AND l.`user_id` = %d";
					$query_args[] = get_current_user_id();
				}
				elseif( is_string( $user ) ){
					$user_condition = " AND l.`session_id` = %s";
					$query_args[] = YITH_WCWL_Session()->get_session_id();
				}
			}

			$query = "SELECT 
       				      COUNT( DISTINCT( v.`u_id` ) ) 
					  FROM ( 
					      SELECT 
					          ( CASE WHEN l.`user_id` IS NULL THEN l.`session_id` ELSE l.`user_id` END) AS u_id, 
					          l.`ID` as wishlist_id 
					      FROM {$wpdb->yith_wcwl_wishlists} AS l 
					      WHERE ( l.`expiration` > NOW() OR l.`expiration` IS NULL ) {$user_condition}
				      ) as v 
				      LEFT JOIN {$wpdb->yith_wcwl_items} AS i USING( wishlist_id ) 
					  WHERE i.`prod_id` = %d";

			$query_args[] = $product_id;

			$res = $wpdb->get_var( $wpdb->prepare( $query, $query_args ) );

			return (int) $res;
		}

		/**
		 * Raw update method; useful when it is needed to update a bunch of items
		 *
		 * @param $columns array Array of columns to update, in the following format: 'column_id' => 'column_type'
		 * @param $column_values array Array of values to apply to the query; must have same number of elements of columns, and they must respect defined tpe
		 * @param $conditions array Array of where conditions, in the following format: 'column_id' => 'columns_type'
		 * @param $conditions_values array Array of values to apply to where condition; must have same number of elements of columns, and they must respect defined tpe
		 * @pram $clear_caches bool Whether system should clear caches (this is optional since other methods may want to run more optimized clear)
		 *
		 * @return void
		 */
		public function update_raw( $columns, $column_values, $conditions = array(), $conditions_values = array(), $clear_caches = false ) {
			global $wpdb;

			// calculate where statement
			$query_where = '';

			if( ! empty( $conditions ) ){
				$query_where = array();

				foreach( $conditions as $column => $value ){
					$query_where[] = $column . '=' . $value;
				}

				$query_where = " WHERE " . implode( ' AND ', $query_where );
			}

			// retrieves wishlists that will be affected by the changes
			if( $clear_caches ){
				$query = "SELECT ID FROM {$wpdb->yith_wcwl_items} {$query_where}";
				$query = $conditions ? $wpdb->prepare( $query, $conditions_values ) : $query;
				$ids = $wpdb->get_col( $query );
			}

			// calculate set statement
			$query_columns = array();

			foreach( $columns as $column => $value ){
				$query_columns[] = $column . '=' . $value;
			}

			$query_columns = implode( ', ', $query_columns );

			// build query, and execute it
			$query = "UPDATE {$wpdb->yith_wcwl_items} SET {$query_columns} {$query_where}";
			$values = $conditions ? array_merge( $column_values, $conditions_values ) : $column_values;

			$wpdb->query( $wpdb->prepare( $query, $values ) );

			// clear cache for updated items
			if( $clear_caches && $ids ){
				foreach( $ids as $id ){
					$this->clear_cache( $id );
				}
			}
		}

		/**
		 * Clear meta cache.
		 *
		 * @param YITH_WCWL_Wishlist_Item|int $item Wishlist item object, or id of the item.
		 */
		public function clear_cache( &$item ) {
			if( ! $item instanceof YITH_WCWL_Wishlist_Item ){
				$item = YITH_WCWL_Wishlist_Factory::get_wishlist_item( $item );
			}

			wp_cache_delete( 'item-' . $item->get_id(), 'wishlist-items' );
			wp_cache_delete( 'wishlist-items-' . $item->get_wishlist_id(), 'wishlists' );
			wp_cache_delete( 'wishlist-items-' . $item->get_origin_wishlist_id(), 'wishlists' );
		}

		/* === MISC === */

		/**
		 * Here we collected all methods related to db implementation of the items
		 * They can be used without creating an instance of the Data Store, and are
		 * listed here just for
		 */

		/**
		 * Alter join section of the query, for ordering purpose
		 *
		 * @param $join string
		 * @return string
		 * @since 2.0.0
		 */
		public static function filter_join_for_wishlist_count( $join ) {
			global $wpdb;
			$join .= " LEFT JOIN ( SELECT COUNT(*) AS wishlist_count, prod_id FROM {$wpdb->yith_wcwl_items} GROUP BY prod_id ) AS i ON ID = i.prod_id";
			return $join;
		}

		/**
		 * Alter orderby section of the query, for ordering purpose
		 *
		 * @param $orderby string
		 * @return string
		 * @since 2.0.0
		 */
		public static function filter_orderby_for_wishlist_count( $orderby ) {
			$orderby = "i.wishlist_count " . ( isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'ASC' );
			return $orderby;
		}
	}
}
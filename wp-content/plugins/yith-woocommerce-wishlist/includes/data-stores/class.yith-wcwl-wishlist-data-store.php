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

if ( ! class_exists( 'YITH_WCWL_Wishlist_Data_Store' ) ) {
	/**
	 * This class implements CRUD methods for wishlists
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Wishlist_Data_Store{

		/**
		 * Create a new wishlist and stores it on DB
		 *
		 * @param $wishlist \YITH_WCWL_Wishlist Wishlist to create
		 */
		public function create( &$wishlist ){
			global $wpdb;

			// set token if missing
			if( ! $wishlist->get_token() ){
				$wishlist->set_token( $this->generate_token() );
			}

			// set slug if missing
			if( ! $wishlist_slug = $wishlist->get_slug() ){
				$wishlist_slug = sanitize_title_with_dashes( $wishlist->get_name() );
				$wishlist->set_slug( $wishlist_slug );
			}

			// set date added if missing
			if( ! $wishlist->get_date_added() ) {
				$wishlist->set_date_added( date( 'Y-m-d H:i:s' ) );
			}

			// set default, if needed
			if( $this->should_be_default() ){
				$wishlist->set_is_default( 1 );
			}

			// set always at least an owner
			if( ! $session_id = $wishlist->get_session_id() && ! $user_id = $wishlist->get_user_id() ){
				if( is_user_logged_in() ){
					$user_id = get_current_user_id();
					$wishlist->set_user_id( apply_filters( 'yith_wcwl_add_wishlist_user_id', $user_id ) );
				}
				else{
					$session_id = YITH_WCWL_Session()->get_session_id();
					$wishlist->set_session_id( apply_filters( 'yith_wcwl_add_wishlist_session_id', $session_id ) );
				}
			}

			// avoid slug duplicate, adding -n to the end of the string
			$wishlist->set_slug( $this->generate_slug( $wishlist_slug ) );

			$columns = array(
				'wishlist_privacy' => '%d',
				'wishlist_name' => '%s',
				'wishlist_slug' => '%s',
				'wishlist_token' => '%s',
				'is_default' => '%d'
			);
			$values = array(
				apply_filters( 'yith_wcwl_add_wishlist_privacy', $wishlist->get_privacy() ),
				apply_filters( 'yith_wcwl_add_wishlist_name', $wishlist->get_name() ),
				apply_filters( 'yith_wcwl_add_wishlist_slug', $wishlist->get_slug() ),
				apply_filters( 'yith_wcwl_add_wishlist_token', $wishlist->get_token() ),
				apply_filters( 'yith_wcwl_add_wishlist_is_default', $wishlist->get_is_default() )
			);

			if( $session_id = $wishlist->get_session_id() ){
				$columns['session_id'] = '%s';
				$values[] = apply_filters( 'yith_wcwl_add_wishlist_session_id', $session_id );
			}

			if( $user_id = $wishlist->get_user_id() ){
				$columns['user_id'] = '%d';
				$values[] = apply_filters( 'yith_wcwl_add_wishlist_user_id', $user_id );
			}

			if( $date_added = $wishlist->get_date_added( 'edit' ) ){
				$columns['dateadded'] = 'FROM_UNIXTIME( %d )';
				$values[] = apply_filters( 'yith_wcwl_add_wishlist_date_added', $date_added->getTimestamp() );
			}

			if( $expiration = $wishlist->get_expiration( 'edit' ) ){
				$columns['expiration'] = 'FROM_UNIXTIME( %d )';
				$values[] = apply_filters( 'yith_wcwl_add_wishlist_expiration', $expiration->getTimestamp() );
			}

			// if session wishlist, set always an expiration
			if( isset( $columns['session_id'] ) && ! $expiration && $expiration = YITH_WCWL_Session()->get_session_expiration() ){
				$columns['expiration'] = 'FROM_UNIXTIME( %d )';
				$values[] = apply_filters( 'yith_wcwl_add_wishlist_expiration', $expiration );
			}

			$query_columns = implode( ', ', array_map( 'esc_sql', array_keys( $columns ) ) );
			$query_values = implode( ', ', array_values( $columns ) );
			$query = "INSERT INTO {$wpdb->yith_wcwl_wishlists} ( {$query_columns} ) VALUES ( {$query_values} ) ";

			$res = $wpdb->query( $wpdb->prepare( $query, $values ) );

			if ( $res ) {
				$id = apply_filters( 'yith_wcwl_wishlist_correctly_created', intval( $wpdb->insert_id ) );

				$wishlist->set_id( $id );
				$wishlist->apply_changes();
				$this->clear_caches( $wishlist );

				do_action( 'yith_wcwl_new_wishlist', $wishlist->get_id(), $wishlist );
			}
		}

		/**
		 * Read data from DB for a specific wishlist
		 *
		 * @param $wishlist \YITH_WCWL_Wishlist
		 * @throws Exception When cannot retrieve specified wishlist
		 */
		public function read( &$wishlist ){
			global $wpdb;

			$wishlist->set_defaults();

			if ( ! ( $id = $wishlist->get_id() ) && ! ( $token = $wishlist->get_token() ) ) {
				throw new Exception( __( 'Invalid wishlist.', 'yith-woocommerce-wishlist' ) );
			}

			$wishlist_data = $wishlist->get_id() ? wp_cache_get( 'wishlist-id-' . $wishlist->get_id(), 'wishlists' ) : wp_cache_get( 'wishlist-token-' . $wishlist->get_token(), 'wishlists' );

			if( ! $wishlist_data ) {
				// format query to retrieve wishlist
				$query = false;
				if ( $id ) {
					$query = $wpdb->prepare( "SELECT * FROM {$wpdb->yith_wcwl_wishlists} WHERE ID = %d", $id );
				} elseif ( $token ) {
					$query = $wpdb->prepare( "SELECT * FROM {$wpdb->yith_wcwl_wishlists} WHERE wishlist_token = %s", $token );
				}

				// retrieve wishlist data
				$wishlist_data = $wpdb->get_row( $query );

				wp_cache_set( 'wishlist-id-' . $wishlist->get_id(), $wishlist_data, 'wishlists' );
				wp_cache_set( 'wishlist-token-' . $wishlist->get_token(), $wishlist_data, 'wishlists' );
			}

			if( ! $wishlist_data ){
				throw new Exception( __( 'Invalid wishlist.', 'yith-woocommerce-wishlist' ) );
			}

			// set wishlist props
			$wishlist->set_props(
				array(
					'id'         => $wishlist_data->ID,
					'privacy'    => $wishlist_data->wishlist_privacy,
					'user_id'    => $wishlist_data->user_id,
					'session_id' => isset( $wishlist_data->session_id ) ? $wishlist_data->session_id : '',
					'name'       => wc_clean( stripslashes( $wishlist_data->wishlist_name ) ),
					'slug'       => $wishlist_data->wishlist_slug,
					'token'      => $wishlist_data->wishlist_token,
					'is_default' => $wishlist_data->is_default,
					'date_added' => $wishlist_data->dateadded,
					'expiration' => isset( $wishlist_data->expiration ) ? $wishlist_data->expiration : ''
				)
			);
			$wishlist->set_object_read( true );
		}

		/**
		 * Update wishlist data on DB
		 *
		 * @param $wishlist \YITH_WCWL_Wishlist Wishlist to save on db, with $changes property
		 */
		public function update( &$wishlist ){
			global $wpdb;

			if( ! $wishlist->get_id() ){
				return;
			}

			$data = $wishlist->get_data();
			$changes = $wishlist->get_changes();

			if ( array_intersect( array( 'user_id', 'session_id', 'slug', 'name', 'token', 'privacy', 'expiration', 'date_added', 'is_default' ), array_keys( $changes ) ) ) {
				$columns = array(
					'wishlist_privacy' => '%d',
					'wishlist_name' => '%s',
					'wishlist_token' => '%s',
					'is_default' => '%d',
					'dateadded' => 'FROM_UNIXTIME( %d )'
				);
				$values = array(
					$wishlist->get_privacy(),
					$wishlist->get_name(),
					$wishlist->get_token(),
					$wishlist->get_is_default(),
					$wishlist->get_date_added( 'edit' ) ? $wishlist->get_date_added( 'edit' )->getTimestamp() : time()
				);

				if( $session_id = $wishlist->get_session_id() ){
					$columns['session_id'] = '%s';
					$values[] = apply_filters( 'yith_wcwl_update_wishlist_session_id', $session_id );
				}
				else{
					$columns['session_id'] = 'NULL';
				}

				if( $user_id = $wishlist->get_user_id() ){
					$columns['user_id'] = '%d';
					$values[] = apply_filters( 'yith_wcwl_update_wishlist_user_id', $user_id );
				}
				else{
					$columns['user_id'] = 'NULL';
				}

				if( $expiration = $wishlist->get_expiration( 'edit' ) ){
					$columns['expiration'] = 'FROM_UNIXTIME( %d )';
					$values[] = apply_filters( 'yith_wcwl_update_wishlist_expiration', $expiration->getTimestamp() );
				}
				else{
					$columns['expiration'] = 'NULL';
				}

				if( isset( $changes['slug'] ) && ( $wishlist_slug = $wishlist->get_slug() ) != $data['slug'] ){
					$columns['wishlist_slug'] = '%s';
					$values[] = $this->generate_slug( $wishlist_slug );
				}

				$this->update_raw( $columns, $values, array( 'ID' => '%d' ), array( $wishlist->get_id() ) );
			}

			$wishlist->apply_changes();
			$this->clear_caches( $wishlist );

			do_action( 'yith_wcwl_update_wishlist', $wishlist->get_id(), $wishlist );
		}

		/**
		 * Delete a wishlist from DB
		 *
		 * @param \YITH_WCWL_Wishlist $wishlist Wishlist to delete.
		 */
		public function delete( &$wishlist ) {
			global $wpdb;

			$id         = $wishlist->get_id();
			$is_default = $wishlist->is_default();
			$user_id    = $wishlist->get_user_id();
			$session_id = $wishlist->get_session_id();

			if ( ! $id ) {
				return;
			}

			do_action( 'yith_wcwl_before_delete_wishlist', $wishlist->get_id() );

			$this->clear_caches( $wishlist );

			// delete wishlist and all its items.
			$wpdb->delete( $wpdb->yith_wcwl_items, array( 'wishlist_id' => $id ) );
			$wpdb->delete( $wpdb->yith_wcwl_wishlists, array( 'ID' => $id ) );

			do_action( 'yith_wcwl_delete_wishlist', $wishlist->get_id() );

			$wishlist->set_id( 0 );

			do_action( 'yith_wcwl_deleted_wishlist', $id );

			if ( $is_default && ( $user_id || $session_id ) ) {
				// retrieve other lists for the same user.
				$other_lists = $this->query(
					array_merge(
						array(
							'orderby' => 'dateadded',
							'order'   => 'asc',
						),
						$user_id ? array( 'user_id' => $user_id ) : array(),
						$session_id ? array( 'session_id' => $session_id ) : array()
					)
				);

				if ( ! empty( $other_lists ) ) {
					$new_default = $other_lists[0];

					$new_default->set_is_default( 1 );
					$new_default->save();
				}
			}
		}

		/**
		 * Delete expired session wishlist from DB
		 *
		 * @return void
		 */
		public function delete_expired() {
			global $wpdb;

			$wpdb->query( "DELETE FROM {$wpdb->yith_wcwl_items} WHERE wishlist_id IN ( SELECT ID FROM {$wpdb->yith_wcwl_wishlists} WHERE expiration < NOW() and user_id IS NULL )" );
			$wpdb->query( "DELETE FROM {$wpdb->yith_wcwl_wishlists} WHERE expiration < NOW() and user_id IS NULL" );
		}

		/**
		 * Query database to search
		 *
		 * @return \YITH_WCWL_Wishlist[] Array of matched wishlists.
		 */
		public function query( $args = array() ) {
			global $wpdb;

			$default = array(
				'id' => false,
				'user_id' => ( is_user_logged_in() ) ? get_current_user_id() : false,
				'session_id' => ( ! is_user_logged_in() ) ? YITH_WCWL_Session()->get_session_id() : false,
				'wishlist_slug' => false,
				'wishlist_name' => false,
				'wishlist_token' => false,
				'wishlist_visibility' => apply_filters( 'yith_wcwl_wishlist_visibility_string_value', 'all'), // all, visible, public, shared, private
				'user_search' => false,
				's' => false,
				'is_default' => false,
				'orderby' => '',
				'order' => 'DESC',
				'limit' =>  false,
				'offset' => 0,
				'show_empty' => true
			);

			$args = wp_parse_args( $args, $default );
			extract( $args );

			$sql = "SELECT SQL_CALC_FOUND_ROWS l.ID";

			$sql .= " FROM `{$wpdb->yith_wcwl_wishlists}` AS l";

			if( ! empty( $user_search ) || ! empty( $s ) || ( ! empty($orderby ) && $orderby == 'user_login' ) ) {
				$sql .= " LEFT JOIN `{$wpdb->users}` AS u ON l.`user_id` = u.ID";
			}

			if( ! empty( $user_search ) || ! empty( $s ) ){
				$sql .= " LEFT JOIN `{$wpdb->usermeta}` AS umn ON umn.`user_id` = u.`ID`";
				$sql .= " LEFT JOIN `{$wpdb->usermeta}` AS ums ON ums.`user_id` = u.`ID`";
			}

			$sql .= " WHERE 1";
			$sql_args = array();

			if( ! empty( $user_id ) ){
				$sql .= " AND l.`user_id` = %d";

				$sql_args[] = $user_id;
			}

			if( ! empty( $session_id ) ){
				$sql .= " AND l.`session_id` = %s AND l.`expiration` > NOW()";

				$sql_args[] = $session_id;
			}

			if( ! empty( $user_search ) && empty( $s ) ){
				$sql .= " AND ( umn.`meta_key` LIKE %s AND ums.`meta_key` LIKE %s AND ( u.`user_email` LIKE %s OR umn.`meta_value` LIKE %s OR ums.`meta_value` LIKE %s ) )";

				$search_value = '%' . esc_sql( $user_search ) . '%';

				$sql_args[] = 'first_name';
				$sql_args[] = 'last_name';
				$sql_args[] = $search_value;
				$sql_args[] = $search_value;
				$sql_args[] = $search_value;
			}

			if( ! empty( $s ) ) {
				$sql .= " AND ( ( umn.`meta_key` LIKE %s AND ums.`meta_key` LIKE %s AND ( u.`user_email` LIKE %s OR umn.`meta_value` LIKE %s OR ums.`meta_value` LIKE %s ) ) OR l.wishlist_name LIKE %s OR l.wishlist_slug LIKE %s OR l.wishlist_token LIKE %s )";

				$search_value = '%' . esc_sql( $s ) . '%';

				$sql_args[] = 'first_name';
				$sql_args[] = 'last_name';
				$sql_args[] = $search_value;
				$sql_args[] = $search_value;
				$sql_args[] = $search_value;
				$sql_args[] = $search_value;
				$sql_args[] = $search_value;
				$sql_args[] = $search_value;
			}

			if( ! empty( $is_default ) ){
				$sql .= " AND l.`is_default` = %d";
				$sql_args[] = $is_default;
			}

			if( ! empty( $id ) ){
				$sql .= " AND l.`ID` = %d";
				$sql_args[] = $id;
			}

			if( isset( $wishlist_slug ) && $wishlist_slug !== false ){
				$sql .= " AND l.`wishlist_slug` = %s";
				$sql_args[] = sanitize_title_with_dashes( $wishlist_slug );
			}

			if( ! empty( $wishlist_token ) ){
				$sql .= " AND l.`wishlist_token` = %s";
				$sql_args[] = $wishlist_token;
			}

			if( ! empty( $wishlist_name ) ){
				$sql .= " AND l.`wishlist_name` LIKE %s";
				$sql_args[] = "%" . esc_sql( $wishlist_name ) . "%";
			}

			if( ! empty( $wishlist_visibility ) && $wishlist_visibility != 'all' ){
				switch( $wishlist_visibility ){
					case 'visible':
						$sql .= " AND ( l.`wishlist_privacy` = %d OR l.`is_public` = %d )";
						$sql_args[] = 0;
						$sql_args[] = 1;
						break;
					default:
						$sql .= " AND l.`wishlist_privacy` = %d";
						$sql_args[] = yith_wcwl_get_privacy_value( $wishlist_visibility );
						break;
				}
			}

			if( empty( $show_empty ) ){
				$sql .= " AND l.`ID` IN ( SELECT wishlist_id FROM {$wpdb->yith_wcwl_items} )";
			}

			$sql .= " GROUP BY l.ID";

			$sql .= " ORDER BY";

			if( ! empty( $orderby ) && isset( $order ) ) {
				$sql .= " " . esc_sql( $orderby ) . " " . esc_sql( $order ) . ", ";
			}

			$sql .= " is_default DESC";

			if( ! empty( $limit ) && isset( $offset ) ){
				$sql .= " LIMIT %d, %d";
				$sql_args[] = $offset;
				$sql_args[] = $limit;
			}

			if( ! empty( $sql_args ) ){
				$sql = $wpdb->prepare( $sql, $sql_args );
			}

			$lists = $wpdb->get_col( $sql );

			if( ! empty( $lists ) ){
				$lists = array_map( array( 'YITH_WCWL_Wishlist_Factory', 'get_wishlist' ), $lists );
			} else {
				$lists = array();
			}

			return apply_filters( 'yith_wcwl_get_wishlists', $lists, $args );
		}

		/**
		 * Counts items that matches
		 *
		 * @param $args array Same parameters allowed for {@see query} method
		 * @return int Count of items
		 */
		public function count( $args = array() ) {
			// retrieve number of items found
			return count( $this->query( $args ) );
		}

		/**
		 * Search user ids whose wishlists match passed parameters
		 * NOTE: this will only retrieve wishlists for a logged in user, while guests wishlist will be ignored
		 *
		 * @param $args mixed Array of valid arguments<br/>
		 * [<br/>
		 *     'search' // String to match against first name / last name / user login or user email of wishlist owner<br/>
		 *     'limit'  // Pagination param: number of items to show in one page. 0 to show all items<br/>
		 *     'offset' // Pagination param: offset for the current set. 0 to start from the first item<br/>
		 * ]
		 * @return int[] Array of user ids
		 */
		public function search_users( $args = array() ) {
			global $wpdb;

			$default = array(
				'search' => false,
				'limit' => false,
				'offset' => 0
			);

			$args = wp_parse_args( $args, $default );
			extract( $args );

			$sql = "SELECT DISTINCT i.user_id
                    FROM {$wpdb->yith_wcwl_items} AS i
                    LEFT JOIN {$wpdb->yith_wcwl_wishlists} AS l ON i.wishlist_id = l.ID";

			if( ! empty( $search ) ){
				$sql .= " LEFT JOIN `{$wpdb->users}` AS u ON l.`user_id` = u.ID";
				$sql .= " LEFT JOIN `{$wpdb->usermeta}` AS umn ON umn.`user_id` = u.`ID`";
				$sql .= " LEFT JOIN `{$wpdb->usermeta}` AS ums ON ums.`user_id` = u.`ID`";
			}

			$sql .= " WHERE l.wishlist_privacy = %d";
			$sql_args = array( 0 );

			if( ! empty( $search ) ){
				$sql .= " AND ( umn.`meta_key` LIKE %s AND ums.`meta_key` LIKE %s AND ( u.`user_email` LIKE %s OR u.`user_login` LIKE %s OR umn.`meta_value` LIKE %s OR ums.`meta_value` LIKE %s ) )";
				$sql_args[] = 'first_name';
				$sql_args[] = 'last_name';
				$sql_args[] = "%" . esc_sql( $search ) . "%";
				$sql_args[] = "%" . esc_sql( $search ) . "%";
				$sql_args[] = "%" . esc_sql( $search ) . "%";
				$sql_args[] = "%" . esc_sql( $search ) . "%";
			}

			if( ! empty( $limit ) && isset( $offset ) ){
				$sql .= " LIMIT " . $offset . ", " . $limit;
			}

			$res = $wpdb->get_col( $wpdb->prepare( $sql, $sql_args ) );
			return $res;
		}

		/**
		 * Raw update method; useful when it is needed to update a bunch of wishlists
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
				$query = "SELECT ID FROM {$wpdb->yith_wcwl_wishlists} {$query_where}";
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
			$query = "UPDATE {$wpdb->yith_wcwl_wishlists} SET {$query_columns} {$query_where}";
			$values = $conditions ? array_merge( $column_values, $conditions_values ) : $column_values;

			$wpdb->query( $wpdb->prepare( $query, $values ) );

			// clear cache for updated items
			if( $clear_caches && $ids ){
				foreach( $ids as $id ){
					$this->clear_caches( $id );
				}
			}
		}

		/**
		 * Retrieve all items for the wishlist
		 *
		 * @param $wishlist \YITH_WCWL_Wishlist
		 * @return \YITH_WCWL_Wishlist_Item[] Array or Wishlist items for the wishlist
		 */
		public function read_items( $wishlist ) {
			global $wpdb;

			// Get from cache if available.
			$items = 0 < $wishlist->get_id() ? wp_cache_get( 'wishlist-items-' . $wishlist->get_id(), 'wishlists' ) : false;

			if ( false === $items ) {
				$query = "SELECT i.* FROM {$wpdb->yith_wcwl_items} as i INNER JOIN {$wpdb->posts} as p on i.prod_id = p.ID WHERE wishlist_id = %d AND p.post_type IN ( %s, %s ) AND p.post_status = %s";

				// remove hidden products from result
				$hidden_products = yith_wcwl_get_hidden_products();

				if( ! empty( $hidden_products ) && apply_filters( 'yith_wcwl_remove_hidden_products_via_query', true ) ){
					$query .= " AND prod_id NOT IN ( " . implode( ', ', array_filter( $hidden_products, 'esc_sql' ) ) . " )";
				}

				// order by statement
				$query .= " ORDER BY position ASC, ID ASC;";

				$items = $wpdb->get_results( $wpdb->prepare( $query, array(
					$wishlist->get_id(),
					'product',
					'product_variation',
					'publish'
				) ) );

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

				foreach ( $items as $item ) {
					wp_cache_set( 'item-' . $item->ID, $item, 'wishlist-items' );
				}

				if ( 0 < $wishlist->get_id() ) {
					wp_cache_set( 'wishlist-items-' . $wishlist->get_id(), $items, 'wishlists' );
				}
			}

			if ( ! empty( $items ) ) {
				$items = array_map( array( 'YITH_WCWL_Wishlist_Factory', 'get_wishlist_item' ), array_combine( wp_list_pluck( $items, 'prod_id' ), $items ) );
			} else {
				$items = array();
			}

			return apply_filters( 'yith_wcwl_get_products', $items, array( 'wishlist_id' => $wishlist->get_id() ) );
		}

		/**
		 * Delete all items from the wishist
		 *
		 * @param $wishlist \YITH_WCWL_Wishlist
		 * @return void
		 */
		public function delete_items( $wishlist ) {
			global $wpdb;

			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->yith_wcwl_items} WHERE wishlist_id = %d", $wishlist->get_id() ) );

			$this->clear_caches( $wishlist );
		}

		/**
		 * Generate default token for the wishlist
		 *
		 * @return string Wishlist token
		 */
		public function generate_token() {
			global $wpdb;

			$sql = "SELECT COUNT(*) FROM `{$wpdb->yith_wcwl_wishlists}` WHERE `wishlist_token` = %s";

			do {
				$dictionary = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				$nchars = 12;
				$token = "";

				for( $i = 0; $i <= $nchars - 1; $i++ ){
					$token .= $dictionary[ mt_rand( 0, strlen( $dictionary ) - 1 ) ];
				}

				$count = $wpdb->get_var( $wpdb->prepare( $sql, $token ) );
			}
			while( $count != 0 );

			return $token;
		}

		/**
		 * When a session is finalized, all session wishlists will be converted to user wishlists
		 * This method takes also care of allowing just one default per time after finalization
		 *
		 * @param $session_id string Session id
		 * @param $user_id int User id
		 *
		 * @return void
		 */
		public function assign_to_user( $session_id, $user_id ) {
			global $wpdb;

			// update any item that is assigned to the list
			$items = $wpdb->get_col( $wpdb->prepare( "SELECT i.ID FROM {$wpdb->yith_wcwl_items} AS i LEFT JOIN {$wpdb->yith_wcwl_wishlists} AS l ON l.ID = i.wishlist_id WHERE l.session_id = %s", $session_id ) );

			if( ! empty( $items ) ) {
				$items_string = implode( ',', array_map( 'esc_sql', $items ) );
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->yith_wcwl_items} SET user_id = %d WHERE ID IN ({$items_string})", $user_id ) );
			}

			// set user id for any session wishlist, and remove session data
			$this->update_raw(
				array( 'session_id' => 'NULL', 'expiration' => 'NULL', 'user_id' => '%d' ),
				array( $user_id ),
				array( 'session_id' => '%s' ),
				array( $session_id )
			);

			// retrieves default wishlist ids
			$default_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->yith_wcwl_wishlists} WHERE is_default = %d AND user_id = %d ORDER BY dateadded ASC", 1, $user_id ) );

			// if we find more than one default list, fix data in db
			if( count( $default_ids ) > 1 ){

				// search for master default wishlist
				$master_default_wishlist = array_shift( $default_ids );
				$where_statement = implode( ', ', array_map( 'esc_sql', $default_ids ) );

				try{
					// by default we merge all default wishlists into oldest one (master default wishlist)
					if( apply_filters( 'yith_wcwl_merge_default_wishlists', true ) ){
						// change wishlist id to master default id
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->yith_wcwl_items} SET wishlist_id = %d WHERE wishlist_id IN ({$where_statement})", $master_default_wishlist ) );

						// delete slave default wishlists
						$wpdb->query( "DELETE FROM {$wpdb->yith_wcwl_wishlists} WHERE ID IN ({$where_statement})" );
					}

					// otherwise, we just leave all the wishlists as they are, but we remove default flag from latest
					else {
						// remove default flag
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->yith_wcwl_wishlists} SET is_default = %d WHERE ID IN ({$where_statement})", 0 ) );

						// set name where it is missing
						$default_title = apply_filters( 'yith_wcwl_default_wishlist_formatted_title', get_option( 'yith_wcwl_wishlist_title' ) );
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->yith_wcwl_wishlists} SET wishlist_name = %s WHERE ID IN ({$where_statement}) AND wishlist_name = ''", $default_title ) );
					}
				}
				catch( Exception $e ){
					return;
				}
			}
		}

		/**
		 * Retrieve default wishlist for current user/session; if none is found, generate it
		 *
		 * @param $id string|int|bool Pass this param when you want to retrieve a wishlist for a specific user/session
		 * @param $context string Context; when on edit context, wishlist will be created, if not exists
		 * @return \YITH_WCWL_Wishlist|bool Default wishlist for current user/session, or false on failure
		 */
		public function get_default_wishlist( $id = false, $context = 'read' ) {
			global $wpdb;

			if( ! empty( $id ) && is_int( $id ) ){
				$cache_key = 'wishlist-default-' . $id;
				$wishlist_id = wp_cache_get( $cache_key, 'wishlists' );
				$wishlist_id = $wishlist_id !== false ? $wishlist_id : $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->yith_wcwl_wishlists} WHERE user_id = %d AND is_default = 1", $id ) );
			}
			elseif( ! empty( $id ) && is_string( $id ) ){
				$cache_key = 'wishlist-default-' . $id;
				$wishlist_id = wp_cache_get( $cache_key, 'wishlists' );
				$wishlist_id = $wishlist_id !== false ? $wishlist_id : $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->yith_wcwl_wishlists} WHERE session_id = %s AND expiration > NOW() AND is_default = 1", $id ) );
			}
			elseif( $user_id = get_current_user_id() ){
				$cache_key = 'wishlist-default-' . $user_id;
				$wishlist_id = wp_cache_get( $cache_key, 'wishlists' );
				$wishlist_id = $wishlist_id !== false ? $wishlist_id : $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->yith_wcwl_wishlists} WHERE user_id = %d AND is_default = 1", $user_id ) );
			}
			elseif( $session_id = YITH_WCWL_Session()->get_session_id() ){
				$cache_key = 'wishlist-default-' . $session_id;
				$wishlist_id = wp_cache_get( $cache_key, 'wishlists' );
				$wishlist_id = $wishlist_id !== false ? $wishlist_id : $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->yith_wcwl_wishlists} WHERE session_id = %s AND expiration > NOW() AND is_default = 1", $session_id ) );
			}

			if( $wishlist_id ){
				if( $cache_key ) {
					wp_cache_set( $cache_key, $wishlist_id, 'wishlists' );
				}

				return YITH_WCWL_Wishlist_Factory::get_wishlist( $wishlist_id );
			}
			elseif( 'edit' == $context ){
				$wishlist = $this->generate_default_wishlist( $id );

				if( $cache_key ) {
					wp_cache_set( $cache_key, $wishlist->get_id(), 'wishlists' );
				}

				return $wishlist;
			}
			else{
				/**
				 * If no default wishlist was found, register null as cache value
				 * This will be used until someone tries to edit the list (entering previous elseif),
				 * causing a new default wishlist to be automatically generated and stored in cache, replacing null
				 *
				 * @since 3.0.6
				 */
				if( $cache_key ) {
					wp_cache_set( $cache_key, null, 'wishlists' );
				}

				return false;
			}
		}

		/**
		 * Generate a new default wishlist
		 *
		 * @param string|int|bool Pass this param when you want to create a wishlist for a specific user/session
		 * @return YITH_WCWL_Wishlist|bool Brand new default wishlist, or false on failure
		 */
		public function generate_default_wishlist( $id ) {
			try {
				$default_wishlist = new YITH_WCWL_Wishlist();

				if ( ! empty( $id ) && is_int( $id ) ) {
					$default_wishlist->set_user_id( $id );
				} elseif ( ! empty( $id ) && is_string( $id ) ) {
					$default_wishlist->set_session_id( $id );
				}

				$default_wishlist->save();

				/**
				 * Let developers perform processing when default wishlist is created
				 *
				 * @since 3.0.10
				 */
				do_action( 'yith_wcwl_generated_default_wishlist', $default_wishlist, $id );
			}
			catch( Exception $e ){
				return false;
			}

			return $default_wishlist;
		}

		/**
		 * Generate unique slug for the wishlisst
		 *
		 * @param string Original slug assigned to the wishlist (it cuold be custom assigned, or generated from the title)
		 * @return string Unique slug, derived from original one adding ordinal number when necessary
		 */
		public function generate_slug( $slug ) {
			if( empty( $slug ) ){
				return '';
			}

			while( $this->slug_exists( $slug ) ){
				$match = array();

				if ( ! preg_match( '/([a-z-]+)-([0-9]+)/', $slug, $match ) ) {
					$i = 2;
				} else {
					$i = intval( $match[2] ) + 1;
					$slug = $match[1];
				}

				$slug = $slug . '-' . $i;
			}

			return $slug;
		}

		/**
		 * Checks if a slug already exists
		 *
		 * @param $slug string Slug to check on db
		 *
		 * @return bool Whether slug already exists for current session or not
		 */
		public function slug_exists( $slug ) {
			global $wpdb;

			$res = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM {$wpdb->yith_wcwl_wishlists} WHERE wishlist_slug = %s", $slug ) );

			return (bool) $res;
		}

		/**
		 * Check if we're registering first wishlist for the user/session
		 *
		 * @return bool Whether current wishlist should be default
		 */
		protected function should_be_default() {
			global $wpdb;

			if( $user_id = get_current_user_id() ){
				$wishlists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( ID ) FROM {$wpdb->yith_wcwl_wishlists} WHERE user_id = %d AND is_default = %d", $user_id, 1 ) );

				return ! (bool) $wishlists;
			}

			if( $customer_id = YITH_WCWL_Session()->get_session_id() ){
				$wishlists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( ID ) FROM {$wpdb->yith_wcwl_wishlists} WHERE session_id = %s AND expiration > NOW() AND is_default = %d", $customer_id, 1 ) );

				return ! (bool) $wishlists;
			}

			return true;
		}

		/**
		 * Clear wishlist related caches
		 *
		 * @param $wishlist \YITH_WCWL_Wishlist|int|string
		 * @return void
		 */
		protected function clear_caches( &$wishlist ) {
			if( $wishlist instanceof YITH_WCWL_Wishlist ){
				$id = $wishlist->get_id();
				$token = $wishlist->get_token();
			}
			elseif( intval( $wishlist ) ){
				$id = $wishlist;
				$wishlist = yith_wcwl_get_wishlist( $wishlist );
				$token = $wishlist ? $wishlist->get_token() : false;
			}
			else{
				$token = $wishlist;
				$wishlist = yith_wcwl_get_wishlist( $wishlist );
				$id = $wishlist ? $wishlist->get_id() : false;
			}

			$user_id = $wishlist ? $wishlist->get_user_id() : false;
			$session_id = $wishlist ? $wishlist->get_session_id() : false;

			wp_cache_delete( 'wishlist-items-' . $id, 'wishlists' );
			wp_cache_delete( 'wishlist-id-' . $id, 'wishlists' );
			wp_cache_delete( 'wishlist-token-' . $token, 'wishlists' );

			if ( $user_id ) {
				wp_cache_delete( 'user-wishlists-' . $user_id, 'wishlists' );
			}

			if ( $session_id ) {
				wp_cache_delete( 'user-wishlists-' . $session_id, 'wishlists' );
			}
		}
	}
}

<?php
/**
 * Filter Session data store
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\DataStore
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Session_Data_Store' ) ) {
	/**
	 * This class implements CRUD methods for filter preset
	 *
	 * @since 4.0.0
	 */
	class YITH_WCAN_Session_Data_Store {
		/**
		 * Method to create a new record of a WC_Data based object.
		 *
		 * @param YITH_WCAN_Session $session Data object.
		 * @throws Exception When session cannot be created with current information.
		 */
		public function create( &$session ) {
			global $wpdb;

			if ( ! $session->get_token() || ! $session->get_hash() ) {
				throw new Exception( _x( 'Unable to create session. Missing required params.', '[DEV] Debug message triggered when unable to create filter session.', 'yith-woocommerce-ajax-navigation' ) );
			}

			if ( ! $session->get_expiration( 'edit' ) ) {
				$default_expiration = time() + (int) $this->get_session_duration();
				$session->set_expiration( date_i18n( 'Y-m-d H:i:s', $default_expiration ) );
			}

			$columns = array(
				'token'      => '%s',
				'hash'       => '%s',
				'origin_url' => '%s',
				'query_vars' => '%s',
				'expiration' => 'FROM_UNIXTIME( %d )',
			);
			$values  = array(
				$session->get_token(),
				$session->get_hash(),
				$session->get_origin_url(),
				maybe_serialize( $session->get_query_vars() ),
				$session->get_expiration( 'edit' )->getTimestamp(),
			);

			$query_columns = implode( ', ', array_map( 'esc_sql', array_keys( $columns ) ) );
			$query_values  = implode( ', ', array_values( $columns ) );
			$query         = "INSERT INTO {$wpdb->filter_sessions} ( {$query_columns} ) VALUES ( {$query_values} ) ";

			$res = $wpdb->query( $wpdb->prepare( $query, $values ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery

			if ( $res ) {
				$id = apply_filters( 'yith_wcan_session_correctly_created', intval( $wpdb->insert_id ) );

				$session->set_id( $id );
				$session->apply_changes();

				$this->clear_cache( $session );

				do_action( 'yith_wcan_new_session', $session->get_id(), $session );
			}
		}

		/**
		 * Method to read a record. Creates a new WC_Data based object.
		 *
		 * @param YITH_WCAN_Session $session Data object.
		 * @throws Exception When session cannot be created with current information.
		 */
		public function read( &$session ) {
			global $wpdb;

			$session->set_defaults();

			$id    = $session->get_id();
			$hash  = $session->get_hash();
			$token = $session->get_token();

			if ( ! $id && ! $hash && ! $token ) {
				throw new Exception( _x( 'Invalid session.', '[DEV] Debug message triggered when unable to find filter session.', 'yith-woocommerce-ajax-navigation' ) );
			}

			$session_data = $hash ? wp_cache_get( 'filter-session-' . $hash, 'filter_sessions' ) : false;

			if ( ! $session_data ) {
				// format query to retrieve session.
				$query = false;

				if ( $id ) {
					$query = $wpdb->prepare( "SELECT * FROM {$wpdb->filter_sessions} WHERE ID = %d", $id );
				} elseif ( $hash ) {
					$query = $wpdb->prepare( "SELECT * FROM {$wpdb->filter_sessions} WHERE hash = %s", $hash );
				} elseif ( $token ) {
					$query = $wpdb->prepare( "SELECT * FROM {$wpdb->filter_sessions} WHERE token = %s", $token );
				}

				// retrieve session data.
				$session_data = $wpdb->get_row( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery

				if ( $session_data ) {
					wp_cache_set( 'filter-session-' . $session_data->hash, $session_data, 'filter_sessions' );
				}
			}

			if ( ! $session_data ) {
				throw new Exception( _x( 'Invalid session.', '[DEV] Debug message triggered when unable to find filter session.', 'yith-woocommerce-ajax-navigation' ) );
			}

			// set wishlist props.
			$session->set_props(
				array(
					'id'         => $session_data->ID,
					'token'      => $session_data->token,
					'hash'       => $session_data->hash,
					'origin_url' => $session_data->origin_url,
					'query_vars' => maybe_unserialize( $session_data->query_vars ),
					'expiration' => $session_data->expiration,
				)
			);
			$session->set_object_read( true );
		}

		/**
		 * Updates a record in the database.
		 *
		 * @param YITH_WCAN_Session $session Data object.
		 */
		public function update( &$session ) {
			if ( ! $session->get_id() ) {
				return;
			}

			$changes = $session->get_changes();

			if ( array_intersect( array( 'hash', 'token', 'origin_url', 'query_vars', 'expiration' ), array_keys( $changes ) ) ) {
				$columns = array(
					'hash'       => '%s',
					'token'      => '%s',
					'origin_url' => '%s',
					'query_vars' => '%s',
					'expiration' => 'FROM_UNIXTIME( %d )',
				);
				$values  = array(
					$session->get_hash(),
					$session->get_token(),
					$session->get_origin_url(),
					maybe_serialize( $session->get_query_vars() ),
					$session->get_expiration( 'edit' ) ? $session->get_expiration( 'edit' )->getTimestamp() : time() + $this->get_session_duration(),
				);

				$this->update_raw( $columns, $values, array( 'ID' => '%d' ), array( $session->get_id() ) );
			}

			$session->apply_changes();
			$this->clear_cache( $session );

			do_action( 'yith_wcan_update_session', $session->get_id(), $session );
		}

		/**
		 * Deletes a record from the database.
		 *
		 * @param  YITH_WCAN_Session $session Data object.
		 *
		 * @return bool result
		 */
		public function delete( &$session ) {
			global $wpdb;

			$id = $session->get_id();

			if ( ! $id ) {
				return false;
			}

			do_action( 'yith_wcan_before_delete_session', $id, $session );

			$this->clear_cache( $session );

			// delete session.
			$res = $wpdb->delete( $wpdb->filter_sessions, array( 'ID' => $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

			if ( $res ) {
				do_action( 'yith_wcan_delete_session', $id, $session );

				$session->set_id( 0 );

				do_action( 'yith_wcan_deleted_session', $id, $session );
			}

			return $res;
		}

		/**
		 * Raw update method; useful when it is needed to update a bunch of wishlists
		 *
		 * @param array $columns           Array of columns to update, in the following format: 'column_id' => 'column_type'.
		 * @param array $column_values     Array of values to apply to the query; must have same number of elements of columns, and they must respect defined types.
		 * @param array $conditions        Array of where conditions, in the following format: 'column_id' => 'columns_type'.
		 * @param array $conditions_values Array of values to apply to where condition; must have same number of elements of columns, and they must respect defined types.
		 * @pram $clear_caches bool Whether system should clear caches (this is optional since other methods may want to run more optimized clear)
		 *
		 * @return void
		 */
		public function update_raw( $columns, $column_values, $conditions = array(), $conditions_values = array() ) {
			global $wpdb;

			// calculate where statement.
			$query_where = '';

			if ( ! empty( $conditions ) ) {
				$query_where = array();

				foreach ( $conditions as $column => $value ) {
					$query_where[] = $column . '=' . $value;
				}

				$query_where = ' WHERE ' . implode( ' AND ', $query_where );
			}

			// calculate set statement.
			$query_columns = array();

			foreach ( $columns as $column => $value ) {
				$query_columns[] = $column . '=' . $value;
			}

			$query_columns = implode( ', ', $query_columns );

			// build query, and execute it.
			$query = "UPDATE {$wpdb->filter_sessions} SET {$query_columns} {$query_where}";

			$values = $conditions ? array_merge( $column_values, $conditions_values ) : $column_values;

			$wpdb->query( $wpdb->prepare( $query, $values ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
		}

		/**
		 * Deletes all available sessions
		 *
		 * @return bool result
		 */
		public function delete_all() {
			global $wpdb;

			return $wpdb->query( "TRUNCATE TABLE {$wpdb->filter_sessions}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}

		/**
		 * Searches for sessions that have equivalent parameters (hash) to those passed as parameter
		 *
		 * If any session matches, set identifying parameters in the object passed as parameter
		 *
		 * @param YITH_WCAN_Session $session Session to search for.
		 * @return bool Whether a matching session was found.
		 */
		public function get_equivalent_session( &$session ) {
			global $wpdb;

			$hash = $session->get_hash();

			if ( ! $hash ) {
				return false;
			}

			$res = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->filter_sessions} WHERE hash = %s", $hash ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

			if ( empty( $res ) ) {
				return false;
			}

			$session->set_id( $res->ID );
			$session->set_token( $res->token );
			$session->set_expiration( $res->expiration );

			return true;
		}

		/**
		 * Returns default session duration
		 *
		 * @return int
		 */
		public function get_session_duration() {
			return apply_filters( 'yith_wcan_session_duration', 7 * DAY_IN_SECONDS );
		}

		/**
		 * Retrieves session token from token hash
		 *
		 * @param string $hash Session hash.
		 * @return string|bool Session token; false on failure.
		 */
		public function get_token_by_hash( $hash ) {
			global $wpdb;

			$token = $wpdb->get_var( $wpdb->prepare( "SELECT token FROM {$wpdb->filter_sessions} WHERE hash = %s", $hash ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

			if ( ! ! $token ) {
				return $token;
			}

			return false;
		}

		/**
		 * Generate token for the session
		 *
		 * Token is an handy string that identifies a filtering session; it is 10 character long, and this may cause collisions
		 * since the real parameter used to search for matching session is the hash (md5 of identifying parameters, 32 characters long)
		 * Anyway, because of the high number of combinations possible, and the relatively small duration of the sessions, it is
		 * just more convenient to allow for an easy share of the token.
		 *
		 * @param string $hash Optionally pass hash for which we're generating a token; system will first search if another session with
		 *                     same hash exists, and eventually it will return token for the retrieved entry.
		 * @return string Generated token.
		 */
		public function generate_token( $hash = '' ) {
			global $wpdb;

			if ( ! ! $hash ) {
				$token = $wpdb->get_var( $wpdb->prepare( "SELECT token FROM {$wpdb->filter_sessions} WHERE hash = %s", $hash ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

				if ( ! ! $token ) {
					return $token;
				}
			}

			$sql = "SELECT COUNT(*) FROM {$wpdb->filter_sessions} WHERE token = %s";

			do {
				$dictionary = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
				$nchars     = 10;
				$token      = '';

				for ( $i = 0; $i <= $nchars - 1; $i++ ) {
					$token .= $dictionary[ wp_rand( 0, strlen( $dictionary ) - 1 ) ];
				}

				$count = $wpdb->get_var( $wpdb->prepare( $sql, $token ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
			} while ( ! ! $count );

			return $token;
		}

		/**
		 * Delete expired sessions
		 *
		 * @return void
		 */
		public function delete_expired() {
			global $wpdb;

			$wpdb->query( "DELETE FROM {$wpdb->filter_sessions} WHERE expiration < NOW()" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}

		/**
		 * Clear session related caches
		 *
		 * @param \YITH_WCAN_Session|int|string $session Filtering session, in the form of Session object, Session id or session token.
		 * @return void
		 */
		protected function clear_cache( &$session ) {
			if ( $session instanceof YITH_WCAN_Session ) {
				$hash = $session->get_hash();
			} elseif ( intval( $session ) ) {
				$session = YITH_WCAN_Session_Factory::get_session_by_id( $session );
				$hash    = $session ? $session->get_hash() : false;
			} else {
				$session = YITH_WCAN_Session_Factory::get_session_by_token( $session );
				$hash    = $session ? $session->get_hash() : false;
			}

			if ( ! $hash ) {
				return;
			}

			wp_cache_delete( 'filter-session-' . $hash, 'filter_sessions' );
		}
	}
}

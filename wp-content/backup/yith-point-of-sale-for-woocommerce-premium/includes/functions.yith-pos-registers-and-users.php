<?php
if ( ! function_exists( 'yith_pos_get_allowed_store_registers_by_user' ) ) {

	/**
	 * return the array containing the allowed Store and Registers the user can manage
	 * Note: Managers can open all Registers of the Store (including the guest ones)
	 *      Cashiers can open the Registers of the Store they are allowed to
	 *
	 * @param int|bool $user_id
	 *
	 * @return array
	 */
	function yith_pos_get_allowed_store_registers_by_user( $user_id = false ) {
		$user_id            = ! ! $user_id ? $user_id : get_current_user_id();
		$enabled_meta_query = array( 'relation' => 'OR', array( 'key' => '_enabled', 'value' => 'yes' ), array( 'key' => '_enabled', 'compare' => 'NOT EXISTS' ) );

		$stores = array();
		if ( yith_pos_is_admin_and_can_use_pos( $user_id ) ) {
			$enabled_stores = yith_pos_get_stores( array( 'meta_query' => array( $enabled_meta_query ) ) );

			foreach ( $enabled_stores as $store_id ) {
				$register_ids   = array_filter( yith_pos_get_registers_by_store( $store_id, array( 'fields' => 'ids', 'meta_query' => $enabled_meta_query ) ) );
				$register_names = array_map( 'yith_pos_get_register_name', $register_ids );
				$registers      = array_combine( $register_ids, $register_names );
				asort( $registers );

				$stores[ $store_id ] = array(
					'id'        => $store_id,
					'name'      => yith_pos_get_store_name( $store_id ),
					'registers' => $registers
				);
			}

		} else {
			$len            = strlen( $user_id );
			$search         = "s:{$len}:\"{$user_id}\";";
			$manager_stores = yith_pos_get_stores( array( 'meta_query' => array( array( 'key' => '_managers', 'value' => $search, 'compare' => 'LIKE' ), $enabled_meta_query ) ) );

			$cashier_stores = yith_pos_get_stores( array(
				                                       'meta_query'   => array( array( 'key' => '_cashiers', 'value' => $search, 'compare' => 'LIKE' ), $enabled_meta_query ),
				                                       'post__not_in' => $manager_stores
			                                       ) );


			foreach ( $manager_stores as $store_id ) {
				$register_ids   = array_filter( yith_pos_get_registers_by_store( $store_id, array( 'fields' => 'ids', 'meta_query' => $enabled_meta_query ) ) );
				$register_names = array_map( 'yith_pos_get_register_name', $register_ids );
				$registers      = array_combine( $register_ids, $register_names );
				asort( $registers );

				$stores[ $store_id ] = array(
					'id'        => $store_id,
					'name'      => yith_pos_get_store_name( $store_id ),
					'registers' => $registers
				);
			}

			foreach ( $cashier_stores as $store_id ) {

				/** @var YITH_POS_Register[] $_registers */
				$_register_ids = yith_pos_get_registers_by_store( $store_id, array( 'fields' => 'ids', 'meta_query' => $enabled_meta_query ) );
				$registers     = array();

				foreach ( $_register_ids as $_register_id ) {

					if ( yith_pos_user_can_use_register( $_register_id, $user_id ) ) {
						$registers[ $_register_id ] = yith_pos_get_register_name( $_register_id );
					}
				}
				if ( $registers ) {
					asort( $registers );
					$stores[ $store_id ] = array(
						'id'        => $store_id,
						'name'      => yith_pos_get_store_name( $store_id ),
						'registers' => $registers
					);
				}
			}
		}

		foreach ( $stores as $key => $store ) {
			$_registers = $store[ 'registers' ];
			$registers  = array();
			foreach ( $_registers as $register_id => $register_name ) {
				$register = array(
					'id'   => $register_id,
					'name' => $register_name,
				);

				$registers[] = $register;
			}
			$stores[ $key ][ 'registers' ] = $registers;
		}

		return $stores;
	}
}

if ( ! function_exists( 'yith_pos_register_login' ) ) {

	/**
	 * Login to a Register
	 *
	 * @param $register_id
	 */
	function yith_pos_register_login( $register_id ) {
		$expire = apply_filters( 'yith_pos_register_login_expiration', 24 * HOUR_IN_SECONDS );
		$secure = is_ssl();
		setcookie( YITH_POS_REGISTER_COOKIE, $register_id, time() + $expire, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
	}
}

if ( ! function_exists( 'yith_pos_register_logout' ) ) {

	/**
	 * Logout from a Register
	 */
	function yith_pos_register_logout() {
		setcookie( YITH_POS_REGISTER_COOKIE, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
	}
}

if ( ! function_exists( 'yith_pos_register_logged_in' ) ) {

	/**
	 * Am I logged-in for a Register?
	 */
	function yith_pos_register_logged_in() {
		$register_id = isset( $_COOKIE[ YITH_POS_REGISTER_COOKIE ] ) ? absint( $_COOKIE[ YITH_POS_REGISTER_COOKIE ] ) : false;

		return is_user_logged_in() && $register_id && yith_pos_user_can_use_register( $register_id ) ? $register_id : false;
	}
}

if ( ! function_exists( 'yith_pos_user_can_use_register' ) ) {

	/**
	 * Am I logged-in for a Register?
	 *
	 * @param int $register_id
	 * @param bool!int $user_id
	 *
	 * @return bool
	 */
	function yith_pos_user_can_use_register( $register_id, $user_id = false ) {
		$user_id  = ! ! $user_id ? $user_id : get_current_user_id();
		$register = yith_pos_get_register( $register_id );
		$can      = false;

		if ( $register && $register->is_enabled() ) {
			$store = $register->get_store();
			if ( $store && $store->is_enabled() ) {
				if ( yith_pos_is_admin_and_can_use_pos() ) {
					$can = true;
				} elseif ( in_array( $user_id, $store->get_managers() ) ) {
					$can = true;
				} elseif ( in_array( $user_id, $store->get_cashiers() ) ) {
					if ( ! $register->is_guest_enabled() ) {
						if ( 'all' === $register->get_visibility() ) {
							$can = true;
						} else {
							$visibility_cashiers = $register->get_visibility_cashiers();
							$type                = isset( $visibility_cashiers[ 'type' ] ) ? $visibility_cashiers[ 'type' ] : 'exclude';
							$cashiers            = isset( $visibility_cashiers[ 'cashiers' ] ) ? $visibility_cashiers[ 'cashiers' ] : array();

							$can = ( 'show' === $type && in_array( $user_id, $cashiers ) ) || ( 'hide' === $type && ! in_array( $user_id, $cashiers ) );
						}
					} else {
						$can = true;
					}
				}
			}
		}

		return $can;
	}
}


if ( ! function_exists( 'yith_pos_set_register_lock' ) ) {

	/**
	 * Mark the register as currently being used by the current user
	 *
	 * @param int $register_id ID of the register being used.
	 *
	 * @return array|false Array of the lock time and user ID. False if the post does not exist, or
	 *                         there is no current user.
	 */
	function yith_pos_set_register_lock( $register_id ) {
		if ( ! $post = get_post( $register_id ) ) {
			return false;
		}

		if ( ! $user_id = get_current_user_id() ) {
			return false;
		}

		$register_id = $post->ID;

		$now  = time();
		$lock = "$now:$user_id";

		update_post_meta( $register_id, '_yith_pos_register_lock', $lock );
		update_post_meta( $register_id, '_yith_pos_register_used_last', $user_id );

		return array( $now, $user_id );
	}
}

if ( ! function_exists( 'yith_pos_unset_register_lock' ) ) {

	/**
	 * Mark the register as not currently being used by the current user
	 *
	 * @param int $register_id ID of the register being used.
	 *
	 * @return bool true if it's unset correctly
	 */
	function yith_pos_unset_register_lock( $register_id ) {
		if ( ! $user = yith_pos_get_register_lock( $register_id ) ) {
			return false;
		}

		if ( $user === get_current_user_id() ) {
			delete_post_meta( $register_id, '_yith_pos_register_lock' );

			return true;
		}

		return false;
	}
}


if ( ! function_exists( 'yith_pos_get_register_lock' ) ) {

	/**
	 * Get the register lock
	 *
	 * @param int $register_id ID of the register being used.
	 *
	 * @return int|false Array of the lock time and user ID. False if the post does not exist, or
	 *                         there is no current user.
	 */
	function yith_pos_get_register_lock( $register_id ) {
		if ( ! $post = get_post( $register_id ) ) {
			return false;
		}

		$register_id = $post->ID;

		if ( ! $lock = get_post_meta( $register_id, '_yith_pos_register_lock', true ) ) {
			return false;
		}

		$lock = explode( ':', $lock );
		$time = $lock[ 0 ];
		$user = isset( $lock[ 1 ] ) ? $lock[ 1 ] : get_post_meta( $register_id, '_yith_pos_register_used_last', true );
		$user = absint( $user );

		if ( ! get_userdata( $user ) ) {
			return false;
		}

		$time_window = apply_filters( 'yith_pos_register_lock_window', 150 );

		if ( $time && $time > time() - $time_window ) {
			return $user;
		}

		return false;
	}
}

if ( ! function_exists( 'yith_pos_check_register_lock' ) ) {

	/**
	 * Check to see if the register is currently being used by another user.
	 *
	 * @param int $register_id ID of the register being used.
	 *
	 * @return int|false Array of the lock time and user ID. False if the post does not exist, or
	 *                         there is no current user.
	 */
	function yith_pos_check_register_lock( $register_id ) {
		if ( $user = yith_pos_get_register_lock( $register_id ) ) {
			if ( $user && $user != get_current_user_id() ) {
				return $user;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'yith_pos_maybe_open_register' ) ) {
	/**
	 * Open a register
	 *
	 * @param int $register_id ID of the register being used.
	 */
	function yith_pos_maybe_open_register( $register_id ) {
		$register = yith_pos_get_register( $register_id );

		if ( $register ) {
			if ( ! $register->has_status( 'opened' ) ) {
				$register->set_status( 'opened' );
				$session_id = YITH_POS_Register_Session::add_session( $register_id );
				$register->set_current_session( $session_id );
				$register->save();
			}

			$current_session = $register->get_current_session();

			//check if the current session is set
			if ( ! $current_session ) {
				$session_id = YITH_POS_Register_Session::add_session( $register_id );
				$register->set_current_session( $session_id );
				$register->save();
			} else {
				//todo:update the cashier
				YITH_POS_Register_Session::update_cashiers( $current_session );
			}
		}

	}
}

if ( ! function_exists( 'yith_pos_close_register' ) ) {
	/**
	 * Close a register
	 *
	 * @param int $register_id ID of the register being used.
	 */
	function yith_pos_close_register( $register_id ) {
		$register   = yith_pos_get_register( $register_id );
		$session_id = $register->get_current_session();
		if ( $register && ! $register->has_status( 'closed' ) ) {
			YITH_POS_Register_Session::close_session( $session_id );
			$register->set_status( 'closed' );
			$register->set_current_session( '' );
			$register->save();
		}
	}
}

if ( ! function_exists( 'yith_pos_is_viewing_register' ) ) {
	/**
	 * Can the current user view the register?
	 */
	function yith_pos_can_view_register() {
		$register_id = yith_pos_register_logged_in();
		$register    = yith_pos_get_register( $register_id );

		return $register && $register->has_status( 'opened' ) && ! yith_pos_check_register_lock( $register_id );
	}
}


if ( ! function_exists( 'yith_pos_get_manager_stores' ) ) {
	/**
	 * get stores of a specified manager
	 *
	 * @param int $user_id
	 *
	 * @return mixed|void
	 */
	function yith_pos_get_manager_stores( $user_id = 0 ) {
		return yith_pos_get_stores( array( 'meta_query' => yith_pos_get_manager_stores_meta_query( $user_id ) ) );
	}
}

if ( ! function_exists( 'yith_pos_get_manager_stores_meta_query' ) ) {
	/**
	 * get the meta query for stores of a specified manager
	 *
	 * @param int $user_id
	 *
	 * @return mixed|void
	 */
	function yith_pos_get_manager_stores_meta_query( $user_id = 0 ) {
		$user_id = ! ! $user_id ? $user_id : get_current_user_id();
		$len     = strlen( $user_id );
		$search  = "s:{$len}:\"{$user_id}\";";

		return array( array( 'key' => '_managers', 'value' => $search, 'compare' => 'LIKE' ) );
	}
}


if ( ! function_exists( 'yith_pos_admin_can_use_pos' ) ) {
	function yith_pos_admin_can_use_pos() {
		return apply_filters( 'yith_pos_admin_can_use_pos', true );
	}
}

if ( ! function_exists( 'yith_pos_is_admin_and_can_use_pos' ) ) {
	/**
	 * @param int $user_id
	 *
	 * @return mixed|void
	 */
	function yith_pos_is_admin_and_can_use_pos( $user_id = 0 ) {
		$user_id = ! ! $user_id ? $user_id : get_current_user_id();

		return yith_pos_admin_can_use_pos() && user_can( $user_id, 'manage_options' );

	}
}
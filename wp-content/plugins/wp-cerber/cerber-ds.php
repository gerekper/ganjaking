<?php
/*
	Copyright (C) 2015-20 CERBER TECH INC., https://cerber.tech
	Copyright (C) 2015-20 CERBER TECH INC., https://wpcerber.com

    Licenced under the GNU GPL.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'WPINC' ) ) { exit; }

final class CRB_DS {
	private static $setting = '_crb_ds_shadowing';
	private static $config = null;
	private static $the_user = null;
	private static $update_user = null;
	private static $acc_owner = false;
	private static $user_blocked = false;
	private static $user_fields = array( 'user_login' => 'lgn', 'user_pass' => 'pwd', 'user_email' => 'eml' );
	private static $opt_cache = array();
	//private static $user_metas = array( 'capabilities' );

	static function enable_shadowing( $type ) {

		if ( self::get_config( $type ) || ! lab_lab() ) {
			return;
		}

		$conf = self::get_config();

		if ( ! $conf ) {
			$conf = array();
		}

		$data    = array();
		$data[0] = time();
		$data[1] = 0;
		$data[2] = get_current_user_id();
		$data[3] = get_wp_cerber()->getRequestID();

		switch ( $type ) {
			case 1: // Users data

				if ( defined( 'CRB_USHD_KEY' ) && is_string( CRB_USHD_KEY ) ) {
					$data[5] = CRB_USHD_KEY; // If users' tables are shared among mutiple websites, define it in the wp-config.php on all websites before (!) activation shadowing
				}
				else {
					$data[5] = substr( str_shuffle( 'abcdefghijklmnopqrstuvwxyz' ), 0, rand( 14, 16 ) );
				}

				$conf[ $type ] = $data;
				self::save_config( $conf );

				self::update_user_shadow( get_current_user_id(), null, null, self::is_meta_preserve() );

				cerber_bg_task_add( '_crb_ds_background', array( 'exec_until' => 'done' ) );

				break;

			case 2: // Roles
			case 3: // Settings

				$data[1] = time(); // Should be set after all shadows has created
				$data[5] = substr( str_shuffle( 'abcdefghijklmnopqrstuvwxyz' ), 0, rand( 10, 12 ) );
				$data[6] = substr( str_shuffle( '0123456789abcdefghijklmnopqrstuvwxyz' ), 0, rand( 8, 14 ) );

				$conf[ $type ] = $data;
				self::save_config( $conf );

				foreach ( self::get_protected_settings()[ $type ] as $item ) {
					self::update_setting_shadow( $item, get_option( $item ) );
				}

				break;
		}
	}

	static function disable_shadowing( $type ) {
		global $wpdb;

		if ( ! $type_conf = self::get_config( $type ) ) {
			return;
		}

		if ( ! is_super_admin() && ! nexus_is_valid_request() ) {
			return;
		}

		$conf = self::get_config();
		unset( $conf[ $type ] );
		self::save_config( $conf );

		switch ( $type ) {
			case 1:
				cerber_db_query( 'DELETE FROM ' . $wpdb->usermeta . ' WHERE meta_key ="' . $type_conf[5] . '"' );
				break;
			case 2:
			case 3:
				cerber_db_query( 'DELETE FROM ' . cerber_get_db_prefix() . CERBER_SETS_TABLE . ' WHERE the_key LIKE "' . $type_conf[5] . '%"' );
				break;
		}

	}

	static function is_ready( $type ) {

		if ( lab_lab() && ( $conf = self::get_config( $type ) ) && $conf[1] ) {
			return true;
		}

		return false;
	}

	private static function save_config( $conf ) {
		self::$config = $conf;

		// Encoding

		reset( $conf );
		$lenght = count( $conf );

		$b = rand( 1, 10 );
		//$config           = array_fill( $b, rand( 12, 14 ), 0 );
		$config = array_fill( $b, rand( $lenght + 8, $lenght + 10 ), 0 );
		$s = rand( 3, 5 );
		$config[ $b + 1 ] = $s; // crucial for extracting
		$config[ $b + $s ] = $conf;
		$config[ $b + $s + 1 ] = array( array( $b ) ); // is not used
		$config[] = $b + 3; // crucial for verification

		update_site_option( self::$setting, $config );
	}

	private static function get_config( $type = null ) {

		if ( ! isset( self::$config ) ) {

			$s = get_site_option( self::$setting );

			if ( ! $s || ! is_array( $s ) ) {
				return false;
			}

			reset( $s );
			if ( key( $s ) != ( end( $s ) - 3 ) ) {
				return false;
			}

			$s = array_values( $s );

			self::$config = $s[ $s[1] ];
		}

		if ( $type ) {
			if ( isset( self::$config[ $type ] ) ) {
				return self::$config[ $type ];
			}

			return false;
		}

		return self::$config;
	}

	/**
	 * Creating shadow in bulk
	 *
	 * @return bool true if not completed, false = completed or nothing to do
	 */
	static function iterate_users() {
		global $wpdb;

		if ( ( ! $type_conf = self::get_config( 1 ) ) || self::is_ready( 1 ) ) {
			return false;
		}

		$to_do = cerber_db_get_col( 'SELECT DISTINCT ID FROM ' . $wpdb->users . ' WHERE ID NOT IN (SELECT DISTINCT user_id FROM ' . $wpdb->usermeta . ' WHERE meta_key = "' . $type_conf[5] . '")' );

		if ( ! $to_do ) { // Creating shadow completed
			$conf       = self::get_config();
			$conf[1][1] = time();
			self::save_config( $conf );
			//cerber_diag_log( 'Create shadow completed' );

			return false;
		}

		foreach ( $to_do as $user_id ) {
			self::update_user_shadow( $user_id, null, null, self::is_meta_preserve() );
			//wp_cache_delete( $user_id, 'user_meta' );
		}

		return true;
	}

	private static function get_user_shadow( $user_id ) {
		if ( ! $user_id || ! $conf = self::get_config( 1 ) ) {
			return false;
		}

		$um = get_user_meta( $user_id, $conf[5], true );

		if ( ! $um ) {
			return array();
		}

		$ret = @unserialize( self::decode( $um ) );

		if ( ! $ret || ! is_array( $ret ) ) {
			$ret = array();
		}

		return $ret;
	}

	private static function update_user_shadow( $user_id, $fields = array(), $meta_data = array(), $meta_keys = array() ) {

		if ( ! $user_id || ! $conf = self::get_config( 1 ) ) {
			return false;
		}

		if ( ! $data = get_userdata( $user_id ) ) {
			return false; // Not a valid user
		}

		$sh = self::get_user_shadow( $user_id );

		$sh[0] = $user_id;

		if ( empty( $sh[1] ) ) {
			$sh[1] = array();
		}

		if ( $fields ) {
			$list = array_intersect_key( self::$user_fields, array_flip( $fields ) );
		}
		else {
			$list = self::$user_fields;
		}

		foreach ( $list as $user_field => $key ) {
			$sh[1][ $key ] = $data->data->$user_field;
		}

		if ( empty( $sh[2] ) ) {
			$sh[2] = array();
		}

		/*if ( ! $fields ) { // We use $fields only for updating password
			if ( empty( $sh[2] ) ) { // New user
				foreach ( self::is_meta_preserve() as $key ) {
					$sh[2][ $key ] = get_user_meta( $user_id, $key, true );
				}
			}
		}*/

		if ( ! empty( $meta_keys ) ) {
			foreach ( $meta_keys as $key ) {
				$sh[2][ $key ] = get_user_meta( $user_id, $key, true );
			}
		}

		if ( $meta_data ) {
			$sh[2] = array_merge( $sh[2], $meta_data );
		}

		return update_user_meta( $user_id, $conf[5], self::encode( serialize( $sh ) ) );
	}

	static function is_user_valid( $user_id = null ) {
		if ( ! self::is_ready( 1 ) ) {
			return true;
		}

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( ( $sh = self::get_user_shadow( $user_id ) )
		     && $sh[0] == $user_id ) {
			return true;
		}

		return false;
	}

	/**
	 * Return the password hash of a given user.
	 * Make sure that CRB_DS::is_ready() returns true before using result
	 *
	 * @param null $user_id
	 *
	 * @return string
	 */
	static function get_user_pass( $user_id ) {
		if ( ! $user_id ) {
			return '';
		}

		if ( ! ( $sh = self::get_user_shadow( $user_id ) )
		     || $sh[0] != $user_id
		     || ! ( $ret = crb_array_get( $sh[1], self::$user_fields['user_pass'] ) ) ) {
			return '';
		}

		return $ret;
	}

	/**
	 * @param $mode
	 * @param $user_id
	 * @param null $data User data from 'wp_pre_insert_user_data' filter
	 */
	static function acc_processor( $mode, $user_id, $data = null ) {

		if ( $mode == 'pass' ) {
			self::update_user_shadow( $user_id, array( 'user_pass' ) );

			return;
		}

		$update_shadow = false;

		if ( crb_get_settings( 'ds_4acc_acl' ) && crb_acl_is_white() ) {
			$update_shadow = true;
		}

		switch ( $mode ) {
			case 'new':
				self::$update_user = null;
				if ( ! $update_shadow ) {
					$update_shadow = self::acc_new( $user_id );
				}
				if ( $update_shadow ) {
					self::update_user_shadow( $user_id );
				}
				break;
			case 'update':
				self::$update_user = $user_id;
				self::acc_update( $user_id, $data );
				// Must be deferred till user's data is saved to DB
				add_action( 'profile_update', function ( $user_id ) {
					CRB_DS::update_helper( $user_id );
				} );
				break;
		}

	}

	static function update_helper( $user_id ) {
		if ( ( self::$update_user != $user_id )
		     || ( self::$user_blocked && ! self::$acc_owner ) ) {
			return;
		}

		self::update_user_shadow( $user_id );
	}

	/**
	 * Protect DB from an unauthorized user creation
	 *
	 * @param $user_id
	 *
	 * @return bool true if this operation is permitted
	 */
	private static function acc_new( $user_id ) {
		global $cerber_status;

		$set                = crb_get_settings();
		self::$user_blocked = false;

		// Due to lack of a hook in the wp_insert_user() we are forced to check permissions and use wp_delete_user() after the user was created
		if ( ! is_user_logged_in() ) {
			if ( ! crb_user_has_role_strict( $set['ds_regs_roles'], $user_id ) ) {
				$cerber_status      = 32;
				self::$user_blocked = true;
			}
		}
		else {
			if ( ! cerber_user_has_role( $set['ds_add_acc'] ) ) {
				$cerber_status      = 33;
				self::$user_blocked = true;
			}
		}

		if ( self::$user_blocked ) {
			require_once( ABSPATH . 'wp-admin/includes/user.php' );
			wp_delete_user( $user_id );
			cerber_log( 72 );
			remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
			remove_action( 'edit_user_created_user', 'wp_send_new_user_notifications', 10 );
		}
		elseif ( ! has_filter( 'register_new_user', 'wp_send_new_user_notifications' ) ) {
			// this is needed in the case of a bulk user import
			add_action( 'register_new_user', 'wp_send_new_user_notifications' );
			add_action( 'edit_user_created_user', 'wp_send_new_user_notifications', 10 );
		}

		return ( ! self::$user_blocked );
	}

	/**
	 * Protect user's data from authorized modification
	 *
	 * @param int $user_id
	 * @param array $data User data from 'wp_pre_insert_user_data' filter
	 *
	 * @return bool true if this operation is permitted
	 */
	private static function acc_update( $user_id, $data ) {
		global $cerber_status, $wpdb;

		$cid = get_current_user_id();

		self::$acc_owner = ( $user_id == $cid ) ? true : false;
		self::$user_blocked = false;

		if ( ! cerber_user_has_role( crb_get_settings( 'ds_edit_acc' ) ) ) {

			// An exception: password reset requested (since WP 5.3)
			if ( ! empty( $data['user_activation_key'] ) && cerber_is_http_post() ) {
				// These fields cannot be changed during a normal password reset process
				$protected = array('user_pass','user_nicename','user_email','user_url','user_registered','display_name');
				$ok = true;
				if ( $row = cerber_db_get_row( 'SELECT * FROM ' . $wpdb->users . ' WHERE ID = ' . $user_id ) ) {
					foreach ( $protected as $field ) {
						if ( $row[ $field ] != $data[ $field ] ) {
							$ok = false;
							break;
						}
					}
				}

				if ( $ok ) {
					return true;
				}
			}

			self::$user_blocked = true;

			if ( ! self::$acc_owner ) {
				// Protect the user's row in the users table
				add_filter( 'query', 'crb_empty_query', PHP_INT_MAX );
				add_filter( 'pre_get_col_charset', 'crb_return_wp_error', PHP_INT_MAX );
				$cerber_status = ( ! $cid ) ? 34 : 33;
				cerber_log( 73 );
			}

			if ( ! has_filter( 'insert_user_meta', array( 'CRB_DS', 'user_meta' ) ) ) {
				add_filter( 'insert_user_meta', array( 'CRB_DS', 'user_meta' ), 0, 3 );
			}

		}

		return ( ! self::$user_blocked );
	}

	static function user_meta( $meta, $user, $update ) {
		self::$the_user = $user;

		if ( self::$user_blocked == true ) {
			if ( ! self::$acc_owner ) {
				// Must be removed after a single use for a given user
				remove_filter( 'query', 'crb_empty_query', PHP_INT_MAX );
				remove_filter( 'pre_get_col_charset', 'crb_return_wp_error', PHP_INT_MAX );
			}

			//return array(); // No user meta to update
		}

		return $meta;
	}

	/**
	 * Protect/process users metas and roles from being updated
	 *
	 * @param $var
	 * @param $user_id
	 * @param $meta_key
	 * @param $meta_value
	 * @param $prev_value
	 *
	 * @return bool|null
	 */
	static function protect_user_meta( $var, $user_id, $meta_key, $meta_value, $prev_value ) {
		global $cerber_status;

		// A user is not permitted to be created or updated?
		if ( self::$user_blocked ) {
			if ( self::is_meta_protected( $meta_key ) ) { // User roles are here
				$cerber_status = ( ! is_user_logged_in() ) ? 34 : 33;
				cerber_log( 76 );

				return false;
			}
		}

		if ( true === self::is_meta_preserve( $meta_key ) ) {
			$ok = false;

			//if ( ( self::$update_user == $user_id )
			if ( cerber_user_has_role( crb_get_settings( 'ds_edit_acc' ) ) ) {
				$ok = true;
			}
			// Makes sense for user's role meta ONLY
			elseif ( is_array( $meta_value ) && ! array_diff_key( $meta_value, array_flip( crb_get_settings( 'ds_regs_roles' ) ) ) ) {
				$ok = true;
			}

			if ( $ok ) {
				self::update_user_shadow( $user_id, null, array( $meta_key => $meta_value ) );
			}
		}

		return $var;
	}

	private static function is_meta_preserve( $meta_key = null ) {
		global $wpdb;

		// TODO: add support for multisite via $wpdb->get_blog_prefix()

		$list = array( $wpdb->base_prefix . 'capabilities', $wpdb->base_prefix . 'user_level' );

		if ( $meta_key && in_array( $meta_key, $list ) ) {
			return true;
		}

		return $list;
	}

	private static function is_meta_protected( $meta_key ) {
		global $wpdb;

		if ( ( isset( self::$the_user ) && ( $meta_key == self::$the_user->cap_key ) ) // User roles are here
		     || $meta_key == $wpdb->get_blog_prefix() . 'user_level' ) {
			return true;
		}

		/*
		$metas = array();
		if ( in_array( $meta_key, $metas ) ) {
			return true;
		}*/

		return false;
	}

	static function get_user_meta( $user_id, $meta_key, $single ) {

		if ( ( $conf = self::get_config( 1 ) )
		     && $conf[5] == $meta_key ) {
			return false; // Skip use shadow meta (infinite loop protection)
		}

		$sh = self::get_user_shadow( $user_id );
		if ( isset( $sh[2][ $meta_key ] ) ) {
			return array( $sh[2][ $meta_key ] );
		}

		return false;
	}

	/**
	 * Process settings updates. Updates shadow if permitted.
	 *
	 * @param $value
	 * @param $option
	 * @param $old_value
	 *
	 * @return mixed The old value if update is not permitted
	 */
	static function setting_processor( &$value, $option, &$old_value ) {
		global $cerber_status;

		if ( empty( self::get_protected_settings()[3][ $option ] ) ) {
			return $value;
		}

		if ( $value == $old_value
		     || ( is_array( $value ) && is_array( $old_value )
		          && ( serialize( $value ) === serialize( $old_value ) ) ) ) {
			return $value;
		}

		if ( crb_get_settings( 'ds_4opts_acl' ) && crb_acl_is_white() ) {
			self::update_setting_shadow( $option, $value );

			return $value;
		}

		if ( ! cerber_is_ip_allowed() ) {
			cerber_log( 75 );

			return $old_value;
		}

		$roles = crb_get_settings( 'ds_4opts_roles' );

		if ( ! $roles || ! cerber_user_has_role( $roles ) ) {
			$cerber_status = ( is_user_logged_in() ) ? 33 : 34;
			cerber_log( 75 );

			return $old_value;
		}

		self::update_setting_shadow( $option, $value );

		return $value;
	}

	static function role_processor( &$value, $option, &$old_value ) {
		global $cerber_status;

		if ( ! is_array( $value )
		     || ( substr( $option, - 11 ) != '_user_roles' ) ) {
			return $value;
		}

		if ( serialize( $value ) === serialize( $old_value ) ) {
			return $value;
		}

		$cerber_status = 0;

		if ( ! self::role_update_permitted( $value, $old_value ) ) {
			if ( ! $cerber_status ) {
				$cerber_status = ( is_user_logged_in() ) ? 33 : 34;
			}
			cerber_log( 74 );

			return $old_value;
		}

		self::update_setting_shadow( $option, $value );

		return $value;
	}

	/**
	 * Check if role data can be updated
	 *
	 * @param $value
	 * @param $old_value
	 *
	 * @return bool True if update is permitted
	 */
	static function role_update_permitted( &$value, &$old_value ) {

		if ( crb_get_settings( 'ds_4roles_acl' ) && crb_acl_is_white() ) {
			return true;
		}

		if ( ! cerber_is_ip_allowed() ) {
			return false;
		}

		$add  = crb_get_settings( 'ds_add_role' );
		$edit = crb_get_settings( 'ds_edit_role' );

		if ( ! $add && ! $edit ) {
			return false;
		}

		// Are there new or deleted roles?

		if ( crb_array_diff_keys( $value, $old_value ) ) {
			if ( ! $add || ! cerber_user_has_role( $add ) ) {
				return false;
			}

			return true;
		}

		// There are some changes in capabilities or names

		if ( ! $edit || ! cerber_user_has_role( $edit ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Install hooks for retrieving data of protected settings
	 *
	 */
	static function settings_hooks( $type ) {

		if ( ! self::is_ready( $type ) ) {
			return;
		}

		$list = self::get_protected_settings()[ $type ];

		foreach ( $list as $option ) {

			add_filter( "pre_option_{$option}", function ( $var, $option, $default ) {

				$value = CRB_DS::get_setting_shadow( $option );

				if ( $value ) {
					return $value;
				}

				/*add_filter( "option_{$option}", function ( $value, $option ) {
					CRB_DS::update_setting_shadow( $option, $value );

					return $value;
				}, 10, 2 );*/

				return $var;

			}, PHP_INT_MAX, 3 );

		}

	}

	static function get_setting_shadow( $option ) {
		// TODO: implement WP caching via wp_cache_add() / wp_cache_set()?
		if ( ! isset( self::$opt_cache[ $option ] ) ) {
			self::$opt_cache[ $option ] = cerber_get_set( self::get_setting_key( $option ) );
		}

		return self::$opt_cache[ $option ];
	}

	private static function update_setting_shadow( $option, $value ) {
		self::$opt_cache[ $option ] = $value;

		return cerber_update_set( self::get_setting_key( $option ), $value );
	}

	private static function get_setting_key( $option ) {
		global $wpdb;
		if ( $conf = self::get_config( 2 ) ) {
			return $conf[5] . sha1( $option . $conf[6] . $wpdb->get_blog_prefix() );
		}

		return '';
	}

	/**
	 * @return array
	 */
	private static function get_protected_settings() {
		global $wpdb;

		$list = array();

		$list[2] = array( $wpdb->get_blog_prefix() . 'user_roles' );

		if ( $set = crb_get_settings( 'ds_4opts_list' ) ) {
			$list[3] = $set;
		}
		else {
			$list[3] = array();
		}

		return $list;

	}

	/**
	 * A list of settings to protect
	 *
	 * @param bool $ui_labels
	 *
	 * @return array
	 */
	static function get_settings_list( $ui_labels = true ) {
		$set = array(
			'admin_email'        => __( 'Administration Email Address' ),
			'default_role'       => __( 'New User Default Role' ),
			'home'               => __( 'Site Address (URL)' ),
			'siteurl'            => __( 'WordPress Address (URL)' ),
			'users_can_register' => __( 'Anyone can register' ),
			'active_plugins'     => __( 'Active Plugins' ),
			'template'           => __( 'Active Theme' ),
		);

		if ( $ui_labels ) {
			return $set;
		}

		array_walk( $set, function ( &$e ) {
			$e = 1; // Default value is ON
		} );

		return $set;
	}

	private static function encode( $str ) {
		$str = base64_encode( $str );
		$s   = strlen( $str );
		$str = rtrim( $str, '=' );
		$num = $s - strlen( $str );
		$ret = $num . $str;

		return $ret;
	}

	private static function decode( $str ) {
		static $equs = array( '', '=', '==' );
		$num = substr( $str, 0, 1 );
		$str = ltrim( $str, (string) $num );

		return base64_decode( $str . $equs[ $num ] );
	}

	static function get_status() {
		$ret = array();

		if ( crb_get_settings( 'ds_4acc' ) ) {
			self::get_type_status( 1, $msg );
			$ret [] = 'Enabled for user accounts. ' . $msg;
		}
		if ( crb_get_settings( 'ds_4roles' ) ) {
			self::get_type_status( 2, $msg );
			$ret [] = 'Enabled for user roles. ' . $msg;
		}
		if ( crb_get_settings( 'ds_4opts' ) ) {
			self::get_type_status( 3, $msg );
			$ret [] = 'Enabled for site settings. ' . $msg;
		}

		return $ret;
	}

	private static function get_type_status( $n, &$msg = '' ) {
		if ( $conf = self::get_config( $n ) ) {
			if ( $conf[1] ) {
				$msg = 'Active since ' . cerber_date( $conf[1] ) . '.';
				return true;
			}
			else {
				$msg = 'Creating shadow data in progress. ';
				return true;
			}
		}

		$msg = 'Configuration has been corrupted. Please re-enable protection in the Data Shield settings.';
		return false;
	}

	// TODO: implement error notification
	static function check_errors( &$msg ) {
		$msg = '...';
		return false;
	}
}

if ( crb_get_settings( 'ds_4acc' ) && CRB_DS::is_ready( 1 ) ) {

	add_action( 'user_register', function ( $user_id ) {

		CRB_DS::acc_processor( 'new', $user_id );

	}, 0 );

	add_filter( 'wp_pre_insert_user_data', function ( $data, $update, $user_id ) {

		if ( $update ) {
			CRB_DS::acc_processor( 'update', $user_id, $data );
		}

		return $data;
	}, PHP_INT_MAX, 3 );

	add_filter( 'update_user_metadata', function ( $var, $object_id, $meta_key, $meta_value, $prev_value ) {
		// apply_filters( "update_{$meta_type}_metadata", null, $object_id, $meta_key, $meta_value, $prev_value );

		return CRB_DS::protect_user_meta( $var, $object_id, $meta_key, $meta_value, $prev_value );

	}, PHP_INT_MAX, 5 );

	add_filter( 'get_user_metadata', function ( $var, $object_id, $meta_key, $single ) {
		//$check = apply_filters( "get_{$meta_type}_metadata", null, $object_id, $meta_key, $single );

		if ( $meta = CRB_DS::get_user_meta( $object_id, $meta_key, $single ) ) {
			return $meta;
		}

		return $var;

	}, PHP_INT_MAX, 4 );

	add_action( 'crb_after_reset', function ( $null, $user_id ) {

		CRB_DS::acc_processor( 'pass', $user_id );

	}, 10, 2 );
}

if ( crb_get_settings( 'ds_4roles' ) && CRB_DS::is_ready( 2 ) ) {

	CRB_DS::settings_hooks( 2 );

	add_filter( 'pre_update_option', function ( $value, $option, $old_value ) {

		return CRB_DS::role_processor( $value, $option, $old_value );

	}, PHP_INT_MAX, 3 );

}

if ( crb_get_settings( 'ds_4opts' ) && CRB_DS::is_ready( 3 ) ) {

	CRB_DS::settings_hooks( 3 );

	add_filter( 'pre_update_option', function ( $value, $option, $old_value ) {

		return CRB_DS::setting_processor( $value, $option, $old_value );

	}, PHP_INT_MAX, 3 );

}

/**
 * A special SQL clause that produces empty result
 *
 * @return string
 */
function crb_empty_query() {
	global $wpdb;

	return 'SELECT 0 FROM ' . $wpdb->users;
}

/**
 * This helps to get rid of PHP warnings
 *
 * @return WP_Error
 */
function crb_return_wp_error() {
	return new WP_Error();
}

function _crb_ds_background() {
	if ( ! CRB_DS::iterate_users() ) {
		return 'done';
	}

	return 1;
}
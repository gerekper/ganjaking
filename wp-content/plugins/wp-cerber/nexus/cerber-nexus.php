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

/*

*========================================================================*
|                                                                        |
|	       ATTENTION!  Do not change or edit this file!                  |
|                                                                        |
*========================================================================*

*/

if ( ! defined( 'WPINC' ) ) { exit; }

function nexus_init() {

	if ( nexus_is_slave() ) {
		require_once( dirname( __FILE__ ) . '/cerber-nexus-slave.php' );
		if ( nexus_is_valid_request() ) {
			nexus_slave_process();
		}
	}
	elseif ( defined( 'WP_ADMIN' )
	     || defined( 'WP_NETWORK_ADMIN' )
	     || cerber_is_wp_cron() ) {
		if ( nexus_is_master() ) {
			require_once( dirname( __FILE__ ) . '/cerber-nexus-master.php' );
		}
	}

}

// Admin functions

function nexus_admin_page() {

	//cerber_show_admin_notice();

	if ( ! $role = nexus_get_role_data() ) {

		$roles = array(
			'slave' => array(
				__( 'Enable slave mode', 'wp-cerber' ),
				__( 'This website can be managed from a master website', 'wp-cerber' )
			),
			'master' => array(
				__( 'Enable master mode', 'wp-cerber' ),
				__( 'Configure this website as a master to manage other website', 'wp-cerber' )
			)
		);

		echo '<div class="" style="text-align: center; padding-top: 100px; padding-right: 100px;">';
		echo '<h2 style="margin-bottom: 2em;">' . __( 'To proceed, please select the mode for this website', 'wp-cerber' ) . '</h2>';

		foreach ( $roles as $r => $d ) {
			echo '<div style="padding-bottom: 1em;"><p><a href="' . wp_nonce_url( add_query_arg( array(
					'cerber_admin_do' => 'nexus_set_role',
					'nexus_set_role'  => $r,
				) ), 'control', 'cerber_nonce' ) . '" class="button button-primary cerber-button">' . $d[0] . '</a></p>';
			echo '<p>' . $d[1] . '</p></div>';
		}


		echo '<p style="margin-top: 3rem">Know more: <a href="https://wpcerber.com/manage-multiple-websites/" target="_blank">Managing multiple WP Cerber instances from one dashboard</a></p></div>';

		return;
	}

	if ( nexus_is_master() ) {
		$tabs = array(
			'nexus_sites'  => array( 'bxs-world', __( 'My Websites', 'wp-cerber' ) ),
			'nexus_master' => array( 'bx-cog', __( 'Settings', 'wp-cerber' ) ),
		);
	}
	else {
		$tabs = array(
			'nexus_slave' => array( 'bx-cog', __( 'Slave Settings', 'wp-cerber' ) ),
		);
	}

	$t = ( nexus_is_slave() ) ? __( 'Slave Settings', 'wp-cerber' ) : __( 'My Websites', 'wp-cerber' );

	cerber_show_admin_page( $t, $tabs, null, function ( $tab ) {

		if ( nexus_get_context() ) {
			echo 'You are currently managing the slave website. <a href="' . nexus_get_back_link() . '">Switch to the master</a>.';

			return;
		}

		switch ( $tab ) {
			case 'nexus_master':
				cerber_show_settings_form( $tab );
				break;
			default:
				nexus_site_manager();
		}

	} );

}

function nexus_site_manager() {
	if ( nexus_is_master() ) {
		if ( $site_id = absint( cerber_get_get( 'site_id' ) ) ) {
			nexus_show_slave_form( $site_id );

			return;
		}
		nexus_show_slaves();
	}
	else {

		$token = nexus_the_token();

		//print_r(nexus_the_token($token));

		$no_slave = wp_nonce_url( add_query_arg( array(
			'cerber_admin_do' => 'nexus_set_role',
			'nexus_set_role'  => 'none',
		) ), 'control', 'cerber_nonce' );

		echo '<div class="crb-admin-form" style="padding-bottom: 1em; font-size: 2em;"><p style="font-weight: bold;">' . __( 'Secret Access Token', 'wp-cerber' ) . '</p>';

		echo '<p>' . __( 'The token is unique to this website. Keep it secret. Install the token on a master website to grant access to this website.', 'wp-cerber' ) . ' </p>';
		echo '<p class="crb-monospace" style="padding:1em; background-color: #fff; border: solid 1px #d6d6d6; word-break: break-all;">' . $token . '</p>';
		$confirm = ' onclick="return confirm(\'' . __( 'Are you sure? This permanently invalidates the token.', 'wp-cerber' ) . '\');"';
		echo '<p>' . __( 'To revoke the token and disable remote management, click here:', 'wp-cerber' ) . ' <a href="' . $no_slave . '" ' . $confirm . '>' . __( 'Disable slave mode', 'wp-cerber' ) . '</a>.</p>';

		echo '</div>';

		cerber_show_settings_form( 'nexus-slave' );
	}
}

/**
 * Return slave token if no token specified, otherwise decode and return it
 *
 * @param string $token Token to decode
 *
 * @return array|string
 */
function nexus_the_token( $token = '' ) {
	if ( ! is_super_admin() ) {
		return false;
	}
	if ( $token ) {
		// Decode
		if ( substr( $token, 0, 3 ) != 'X01' ) {
			return false;
		}
		$crc  = substr( $token, 3, 32 );
		$body = substr( $token, 35 );
		if ( $crc != md5( $body ) ) {
			return false;
		}

		$ret = @json_decode( str_rot13( urldecode( str_replace( '&', '%', $body ) ) ), true );
		if ( JSON_ERROR_NONE != json_last_error() ) {
			return false;
		}
		if ( empty( $ret['cerber-slave'] ) || 6 > count( $ret['cerber-slave'] ) ) {
			return false;
		}

		return $ret['cerber-slave'];
	}

	$role = nexus_get_role_data();
	if ( ! $role || empty( $role['slave'] ) ) {
		return '';
	}

	// Encode
	$token = str_replace( '%', '&', urlencode( str_rot13( '' . json_encode( array(
			'cerber-slave' => array(
				$role['slave']['nx_pass'],
				$role['slave']['nx_echo'],
				$role['slave']['x_field'],
				$role['slave']['x_num'],
				CERBER_VER,
				get_site_url(),
				get_bloginfo( 'name' )
			)
		), JSON_UNESCAPED_UNICODE ) ) ) );

	return 'X01' . md5( $token ) . '' . $token;
}

function nexus_enable_role() {
	if ( ! is_admin() || ! is_super_admin() ) {
		return;
	}
	if ( ! $role = cerber_get_get( 'nexus_set_role', 'master|slave|none' ) ) {
		return;
	}
	if ( $role == 'none' ) {
		cerber_delete_set( '_nexus_mode' );
		return;
	}
	if ( nexus_is_master() && ( $role == 'master' ) ) {
		return;
	}
	if ( nexus_is_slave() && ( $role == 'slave' ) ) {
		return;
	}

	$data = array();
	switch ( $role ) {
		case 'slave':
			$all_ascii       = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~';
			$num             = rand( 20, 50 );
			$data['nx_pass'] = substr( str_shuffle( $all_ascii ), 0, $num );
			$data['nx_echo'] = substr( str_shuffle( $all_ascii ), 0, $num );

			$num             = rand( 8, 10 );
			$data['x_field'] = substr( str_shuffle( 'abcdefghijklmnopqrstuvwxyz' ), 0, $num );
			$data['x_num']   = rand( 1, $num - 2 ); // see loop in nexus_get_fields()
			break;
		case 'master':
			require_once( dirname( __FILE__ ) . '/cerber-nexus-master.php' );
			if ( ! nexus_create_db( $role ) ) {
				cerber_admin_notice( 'Unable to create master DB tables' );

				return;
			}
			break;
	}

	$data['ip']   = cerber_get_remote_ip();
	$data['time'] = time();
	$data['user'] = get_current_user_id();

	cerber_update_set( '_nexus_mode', array(
		$role => $data
	) );

	nexus_get_role_data( true );

	//cerber_admin_message( sprintf( __( 'This website is set as %s.', 'wp-cerber' ), $role ) );
	$msg = array();
	if ( nexus_is_master() ) {
		$msg[] = __( 'This website is set as master.', 'wp-cerber' );
		$msg[] = __( 'Add slave websites by using access tokens.', 'wp-cerber' ) . ' <a href="https://wpcerber.com/manage-multiple-websites/" target="_blank">Read more</a>.';
	}
	else {
		$msg[] = __( 'This website is set as slave.', 'wp-cerber' );
		$msg[] = __( 'Install the access token on the master website.', 'wp-cerber' );
	}

	cerber_admin_message( $msg );
}

// Common functions

function nexus_is_valid_request() {
	static $ret;

	if ( isset( $ret ) ) {
		return $ret;
	}

	if ( ! empty( $_COOKIE )
	     || crb_array_get( $_SERVER, 'REQUEST_METHOD' ) != 'POST'
	     || count( $_POST ) < 2
	     || ! nexus_is_slave() ) {

		$ret = false;
		return false;
	}

	if ( $ip = crb_get_settings( 'slave_ips' ) ) {
		if ( $ip != cerber_get_remote_ip() ) {

			$ret = false;
			return false;
		}
	}
	else {
		if ( ! cerber_is_ip_allowed( null, CRB_CNTX_NEXUS ) || lab_is_blocked() ) {

			$ret = false;
			return false;
		}
	}

	$field_names = nexus_get_fields();
	$xn          = array_shift( $field_names );

	if ( ( ! $auth = cerber_get_post( $field_names[ $xn ] ) )
	     || ( ! $payload = cerber_get_post( $field_names[0] ) )
	     || ( array_diff_key( array_keys( $_POST ), $field_names ) ) ) {

		$ret = false;
		return false;
	}

	nexus_diag_log( 'Check for valid master request...' );

	// It seems this is a request from the master
	// Check master credentials and payload checksum

	$role = nexus_get_role_data();
	//$payload = stripslashes( $payload );

	if ( hash_equals( $auth, hash( 'sha512', $role['slave']['nx_pass'] . sha1( $payload ) ) ) ) {
		nexus_diag_log( 'Master credentials are valid' );
		$ret = true;
	}
	else {
		cerber_log( 300 );
		nexus_diag_log( 'ERROR: invalid master credentials or payload checksum mismatch' );
		$ret = false;
	}

	return $ret;
}

function nexus_get_context() {
	static $slave, $slave_id;

	if ( ! is_admin()
	     || ! nexus_is_master() ) {
		return false;
	}

	if ( ! function_exists( 'wp_get_current_user' ) // No information about a user is available
	     || ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	$id = null;

	if ( ! $id = absint( cerber_get_cookie( 'cerber_nexus_id', 0 ) ) ) {
		return false;
	}

	if ( $id === $slave_id && isset( $slave ) ) {
		return $slave;
	}

	$slave_id = $id;
	if ( ! $slave = nexus_get_slave_data( $slave_id ) ) {
		$slave_id = null;
		$slave = false;
	}

	return $slave;
}

function nexus_get_role_data( $flush = false ) {
	static $data;
	if ( $flush || null === $data ) {
		$data = cerber_get_set( '_nexus_mode' );
	}

	return $data;
}

function nexus_is_master() {
	$role = nexus_get_role_data();
	if ( ! empty( $role['master'] ) ) {
		return true;
	}

	return false;
}

function nexus_is_slave() {
	$role = nexus_get_role_data();
	if ( ! empty( $role['slave'] ) ) {
		return true;
	}

	return false;
}

function nexus_diag_log( $msg ) {

	if ( ( nexus_is_slave() && crb_get_settings( 'slave_diag' ) )
	     || ( nexus_is_master() && crb_get_settings( 'master_diag' ) ) ) {
		$m = 'NONE';
		if ( nexus_is_slave() ) {
			$m = 'Slave';
		}
		elseif ( nexus_is_master() ) {
			$m = 'Master';
		}

		cerber_diag_log( cerber_db_get_errors(), 'NXS ' . $m );

		if ( is_array( $msg ) ) {
			foreach ( $msg as $k => $v ) {
				if ( is_array( $v ) ) {
					$v = print_r( $v, 1 );
				}
				cerber_diag_log( ' | ' . $k . ' = ' . $v, 'NXS ' . $m );
			}
		}
		else {
			cerber_diag_log( $msg, 'NXS ' . $m );
		}
	}
}

function nexus_get_fields( $slave = null ) {
	if ( nexus_is_slave() ) {
		$role = nexus_get_role_data();
		$xf   = $role['slave']['x_field'];
		$xn   = $role['slave']['x_num'];
	}
	elseif ( nexus_is_master() ) {
		if ( ! $slave ) {
			$slave = nexus_get_context();
		}
		$xf    = $slave->x_field;
		$xn    = $slave->x_num;
	}
	else {
		return false;
	}
	if ( ! $xn || ! $xf ) {
		return false;
	}
	// Generate a set of field names
	$ret   = array( $xn, $xf );

	$chars = str_split( $xf );
	for ( $i = count( $chars ) - 2; $i > 0; $i -- ) {
		$tmp       = $chars;
		$tmp[ $i ] = '_';
		$ret[]     = implode( '', $tmp );
	}

	/*
	for ( $i = strlen( $xf ) - 2; $i > 0; $i -- ) {
		$tmp = $xf;
		while ( $tmp == $xf ) {
			$tmp = str_shuffle( $xf );
		}
		$ret[] = $tmp;
	}*/

	return $ret;
}

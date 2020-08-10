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

// If this file is called directly, abort executing.
if ( ! defined( 'WPINC' ) ) {
	exit;
}

/*
 *
 *  Replacement for WordPress pluggable functions without hooks
 *
 *
 */

if( ! function_exists( 'wp_set_password' ) ) {
	function wp_set_password( $password, $user_id ) {
		global $wpdb;

		$hash = wp_hash_password( $password );
		$wpdb->update( $wpdb->users, array( 'user_pass'           => $hash,
		                                    'user_activation_key' => ''
		), array( 'ID' => $user_id ) );

		wp_cache_delete( $user_id, 'users' );

		do_action( 'crb_after_reset', null, $user_id );
	}
}
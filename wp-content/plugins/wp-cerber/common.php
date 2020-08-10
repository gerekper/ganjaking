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

if ( ! defined( 'WPINC' ) ) {
	define( 'WPINC', 'wp-includes' );
}

define( 'MYSQL_FETCH_OBJECT', 5 );
define( 'MYSQL_FETCH_OBJECT_K', 6 );
define( 'CRB_IP_NET_RANGE', '/[^a-f\d\-\.\:\*\/]+/i' );
define( 'CRB_SANITIZE_ID', '[a-z\d\_\-\.\:\*\/]+' );
define( 'CRB_SANITIZE_KEY', '/[^a-z_\-\d.:\/]/i' );


/**
 * Known WP scripts
 * @since 6.0
 *
 */
function cerber_get_wp_scripts() {
	return array_map( function ( $e ) {
		return '/' . $e;
	}, array( WP_LOGIN_SCRIPT, WP_REG_URI, WP_XMLRPC_SCRIPT, WP_TRACKBACK_SCRIPT, WP_PING_SCRIPT, WP_SIGNUP_SCRIPT, WP_COMMENT_SCRIPT ) );
}

/**
 * Return a link (full URL) to a Cerber admin page.
 * Add a particular tab and GET parameters if they are specified
 *
 * @param string $tab   Tab on the page
 * @param array $args   GET arguments to add to the URL
 * @param bool $add_nonce If true, adds the nonce
 *
 * @return string   Full URL
 */
function cerber_admin_link( $tab = '', $args = array(), $add_nonce = false ) {
	static $link_base;

	$page = 'cerber-security';

	if ( empty( $args['page'] ) ) {
		if ( in_array( $tab, array( 'antispam', 'captcha' ) ) ) {
			$page = 'cerber-recaptcha';
		}
		elseif ( in_array( $tab, array( 'imex', 'diagnostic', 'license', 'diag-log', 'change-log' ) ) ) {
			$page = 'cerber-tools';
		}
		elseif ( in_array( $tab, array( 'traffic', 'ti_settings' ) ) ) {
			$page = 'cerber-traffic';
		}
		elseif ( in_array( $tab, array( 'user_shield', 'opt_shield' ) ) ) {
			$page = 'cerber-shield';
		}
		elseif ( in_array( $tab, array( 'geo' ) ) ) {
			$page = 'cerber-rules';
		}
		elseif ( in_array( $tab, array( 'role_policies', 'global_policies' ) ) ) {
			$page = 'cerber-users';
		}
		else {
			if ( list( $prefix ) = explode( '_', $tab, 2 ) ) {
				if ( $prefix == 'scan' ) {
					$page = 'cerber-integrity';
				}
				elseif ( $prefix == 'nexus' ) {
					$page = 'cerber-nexus';
				}
			}
		}

		// TODO: look up the page in tabs config
		//$config = cerber_get_admin_page_config();
	}
	else {
		$page = $args['page'];
		unset( $args['page'] );
	}

	if ( ! isset( $link_base ) ) {
		if ( nexus_is_valid_request() ) {
			$base = nexus_request_data()->base;
		}
		else {
			$base = ( ! is_multisite() ) ? admin_url() : network_admin_url();
		}

		$link_base = rtrim( $base, '/' ) . '/admin.php?page=';
	}

	$link = $link_base . $page;

	if ( $tab ) {
		$link .= '&amp;tab=' . preg_replace( '/[^\w\-]+/', '', $tab );
	}

	if ( $args ) {
		foreach ( $args as $arg => $value ) {
			$link .= '&amp;' . $arg . '=' . urlencode( $value );
		}
	}

	if ( $add_nonce ) {
		$nonce = wp_create_nonce( 'control' );
		$link  .= '&amp;cerber_nonce=' . $nonce;
	}

	return $link;
}

/**
 * Return modified link to the currently displaying page
 *
 * @param array $args Arguments to add to the link to the currently displayed page
 * @param bool $preserve Save GET paramaters of the current request
 * @param bool $add_nonce Add Cerber's nonce
 *
 * @return string
 */
function cerber_admin_link_add( $args = array(), $preserve = false, $add_nonce = true ) {

	$link = cerber_admin_link( crb_admin_get_tab(), array( 'page' => crb_admin_get_page() ), $add_nonce );

	if ( $preserve ) {
		$get = crb_get_query_params();
		unset( $get['page'], $get['tab'] );
	}
	else {
		$get = array();
	}

	if ( $args ) {
		$get = array_merge( $get, $args );
	}

	if ( $get ) {
		foreach ( $get as $arg => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $key => $val ) {
					$link .= '&amp;' . $arg . '[' . $key . ']=' . urlencode( $val );
				}
			}
			else {
				$link .= '&amp;' . $arg . '=' . urlencode( $value );
			}
		}
	}

	return esc_url( $link );
}

/**
 * @param array $set
 *
 * @return string
 */
function cerber_activity_link( $set = array() ) {
	static $link;

	if ( ! $link ) {
		$link = cerber_admin_link( 'activity' );
	}

	$filter = '';

	if ( 1 == count( $set ) ) {
		$filter .= '&filter_activity=' . absint( array_shift( $set ) );
	}
	else {
		foreach ( $set as $key => $item ) {
			$filter .= '&filter_activity[' . $key . ']=' . absint( $item );
		}
	}

	return $link . $filter;
}

function cerber_traffic_link( $set = array(), $button = true ) {
	$ret = cerber_admin_link( 'traffic', $set );
	if ( $button ) {
		$ret = ' <a class="crb-button-tiny" href="' . $ret . '">' . __( 'Check for requests', 'wp-cerber' ) . '</a>';
	}

	return $ret;
}

function cerber_get_login_url(){
	$ret = '';

	if ($path = crb_get_settings( 'loginpath' )) {
		$ret = cerber_get_home_url() . '/' . $path . '/';
	}

	return $ret;
}

/**
 * Always includes the path to the current WP installation
 *
 * @since 7.9.4
 *
 * @return string
 */
function cerber_get_site_url() {
	static $url;

	if ( isset( $url ) ) {
		return $url;
	}

	$url = trim( get_site_url(), '/' );

	return $url;
}
/**
 * Might NOT include the path to the current WP installation in some cases
 * See: https://wordpress.org/support/article/giving-wordpress-its-own-directory/
 *
 * @since 7.9.4
 *
 * @return string
 */
function cerber_get_home_url() {
	static $url;

	if ( ! isset( $url ) ) {
		$url = trim( get_home_url(), '/' );
	}

	return $url;
}

function cerber_calculate_kpi($period = 1){
	global $wpdb;

	$period = absint( $period );
	if ( ! $period ) {
		$period = 1;
	}

	// TODO: Add spam performance as percentage Denied / Allowed comments

	$stamp = time() - $period * 24 * 3600;
	$in = crb_get_activity_set( 'malicious', true );
	$unique_ip = cerber_db_get_var( 'SELECT COUNT(DISTINCT ip) FROM ' . CERBER_LOG_TABLE . ' WHERE activity IN (' . $in . ') AND stamp > ' . $stamp );

	$kpi_list = array(
		//array( __('Incidents detected','wp-cerber').'</a>', cerber_count_log( array( 16, 40, 50, 51, 52, 53, 54 ) ) ),
		array(
			__( 'Malicious activities mitigated', 'wp-cerber' ) . '</a>',
			cerber_count_log( crb_get_activity_set( 'malicious' ), $period )
		),
		array( __( 'Spam comments denied', 'wp-cerber' ), cerber_count_log( array( 16 ), $period ) ),
		array( __( 'Spam form submissions denied', 'wp-cerber' ), cerber_count_log( array( 17 ), $period ) ),
		array( __( 'Malicious IP addresses detected', 'wp-cerber' ), $unique_ip ),
		array( __( 'Lockouts occurred', 'wp-cerber' ), cerber_count_log( array( 10, 11 ), $period ) ),
		//array( __('Locked out IP now','wp-cerber'), $kpi_locknum ),
	);

	return $kpi_list;
}


function cerber_pb_get_devices($token = ''){

	$ret = array();

	if ( ! $token ) {
		if ( ! $token = crb_get_settings( 'pbtoken' ) ) {
			return false;
		}
	}

	$curl = @curl_init();
	if (!$curl) return false;

	$headers = array(
		'Authorization: Bearer ' . $token
	);

	curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.pushbullet.com/v2/devices',
		CURLOPT_HTTPHEADER => $headers,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CONNECTTIMEOUT => 2,
		CURLOPT_TIMEOUT => 4, // including CURLOPT_CONNECTTIMEOUT
		CURLOPT_DNS_CACHE_TIMEOUT => 4 * 3600,
	));

	$result = @curl_exec($curl);
	$curl_error = curl_error($curl);
	curl_close($curl);

	$response = json_decode( $result, true );

	if ( JSON_ERROR_NONE == json_last_error() && isset( $response['devices'] ) ) {
		foreach ( $response['devices'] as $device ) {
			$ret[ $device['iden'] ] = $device['nickname'];
		}
	}
	else {
		if ($response['error']){
			$e = 'Pushbullet ' . $response['error']['message'];
		}
		elseif ($curl_error){
			$e = $curl_error;
		}
		else $e = 'Unknown cURL error';

		cerber_admin_notice( __( 'ERROR:', 'wp-cerber' ) .' '. $e);
	}

	return $ret;
}

/**
 * Send push message via Pushbullet
 *
 * @param $title
 * @param $body
 *
 * @return bool
 */
function cerber_pb_send($title, $body){

	if (!$body) return false;
	if ( ! $token = crb_get_settings( 'pbtoken' ) ) {
		return false;
	}

	$params = array('type' => 'note', 'title' => $title, 'body' => $body, 'sender_name' => 'WP Cerber');

	if ($device = crb_get_settings('pbdevice')) {
		if ($device && $device != 'all' && $device != 'N') $params['device_iden'] = $device;
	}

	$headers = array('Access-Token: '.$token,'Content-Type: application/json');

	$curl = @curl_init();
	if (!$curl) return false;

	curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.pushbullet.com/v2/pushes',
		CURLOPT_POST => true,
		CURLOPT_HTTPHEADER => $headers,
		CURLOPT_POSTFIELDS => json_encode($params),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CONNECTTIMEOUT => 2,
		CURLOPT_TIMEOUT => 4, // including CURLOPT_CONNECTTIMEOUT
		CURLOPT_DNS_CACHE_TIMEOUT => 4 * 3600,
	));

	$result = @curl_exec($curl);
	$curl_error = curl_error($curl);
	curl_close($curl);

	return $curl_error;
}
/**
 * Alert admin if something wrong with the website or settings
 */
function cerber_check_environment(){
	static $done;

	if ( $done ) {
		return;
	}
	$done = true;

	if ( cerber_get_set( '_check_env', 0, false ) ) {
		return;
	}
	cerber_update_set( '_check_env', 1, 0, false, 300 );

	if ( ! crb_get_settings( 'tienabled' ) ) {
		cerber_admin_notice('Warning: Traffic Inspector is disabled');
	}

	if ( cerber_is_admin_page( false, array( 'page' => 'cerber-shield' ) ) ) {
		if ( CRB_DS::check_errors( $msg ) ) {
			cerber_admin_notice( $msg );
		}
	}

	$ex_list = get_loaded_extensions();

	if ( ! in_array( 'curl', $ex_list ) ) {
		cerber_admin_notice( __( 'ERROR:', 'wp-cerber' ) . ' cURL PHP library is not enabled on this website.' );
	}
	else {
		$curl = @curl_init();
		if ( ! $curl && ( $err_msg = curl_error( $curl ) ) ) {
			cerber_admin_notice( __( 'ERROR:', 'wp-cerber' ) . ' ' . $err_msg );
		}
		curl_close( $curl );
	}

	if ( ! in_array( 'mbstring', $ex_list ) || ! function_exists( 'mb_convert_encoding' ) ) {
		cerber_admin_notice( __( 'ERROR:', 'wp-cerber' ) . ' A PHP extension <b>mbstring</b> is not enabled on this website. Some plugin features will not work properly. 
			You need to enable the PHP mbstring extension (multibyte string support) in your hosting control panel.' );
	}

	if ( cerber_get_mode() != crb_get_settings( 'boot-mode' ) ) {
		cerber_admin_notice( __( 'ERROR:', 'wp-cerber' ) . ' ' . 'The plugin is initialized in a different mode that does not match the settings. Check the "Load security engine" setting.' );
	}
}

/**
 * Register an issue to be used in the "trouble solving" functionality
 *
 * @param string $code
 * @param string $text
 * @param string $related_setting
 */
function cerber_add_issue( $code, $text, $related_setting = '' ) {
	// TODO: implement this reporting feature
	static $issues = array();
	if ( ! isset( $issues[ $code ] ) ) {
		$issues[ $code ] = array( $text, $related_setting );
		if ( is_admin() ) {
			// There will be a separate list of issues that is displayed separately.
			// cerber_admin_notice( __( 'Warning:', 'wp-cerber' ) . ' ' . $text );
		}
	}
}

/**
 * Health check-up and self-repairing for vital parts
 *
 */
function cerber_watchdog( $full = false ) {
	if ( $full ) {
		cerber_create_db( false );
		cerber_upgrade_db();

		return;
	}
	if ( ! cerber_is_table( CERBER_LOG_TABLE )
	     || ! cerber_is_table( CERBER_BLOCKS_TABLE )
	     || ! cerber_is_table( CERBER_LAB_IP_TABLE )
	) {
		cerber_create_db( false );
		cerber_upgrade_db();
	}
}

/**
 * Detect and return remote client IP address
 *
 * @since 6.0
 * @return string Valid IP address
 */
function cerber_get_remote_ip() {
	static $remote_ip;

	if ( isset( $remote_ip ) ) {
		return $remote_ip;
	}

	//$options = crb_get_settings();

	if ( defined( 'CERBER_IP_KEY' ) ) {
		$remote_ip = filter_var( $_SERVER[ CERBER_IP_KEY ], FILTER_VALIDATE_IP );
	}
	elseif ( crb_get_settings( 'proxy' ) && isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$list = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
		foreach ( $list as $maybe_ip ) {
			$remote_ip = filter_var( trim( $maybe_ip ), FILTER_VALIDATE_IP );
			if ( $remote_ip ) {
				break;
			}
		}
		if ( ! $remote_ip && isset( $_SERVER['HTTP_X_REAL_IP'] ) ) {
			$remote_ip = filter_var( $_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP );
		}
	}
	else {
		if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$remote_ip = $_SERVER['REMOTE_ADDR'];
		}
		elseif ( ! empty( $_SERVER['HTTP_X_REAL_IP'] ) ) {
			$remote_ip = $_SERVER['HTTP_X_REAL_IP'];
		}
		elseif ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$remote_ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		$remote_ip = filter_var( $remote_ip, FILTER_VALIDATE_IP );
	}

	if ( ! $remote_ip ) { // including WP-CLI, other way is: if defined('WP_CLI')
		$remote_ip = CERBER_NO_REMOTE_IP;
	}

	if ( cerber_is_ipv6( $remote_ip ) ) {
		$remote_ip = cerber_ipv6_short( $remote_ip );
	}

	return $remote_ip;
}


/**
 * Get ip_id for IP.
 * The ip_id can be safely used for array indexes and in any HTML code
 * @since 2.2
 *
 * @param $ip string IP address
 * @return string ID for given IP
 */
function cerber_get_id_ip( $ip ) {
	$ip_id = str_replace( '.', '-', $ip, $count );
	$ip_id = str_replace( ':', '_', $ip_id );

	return $ip_id;
}
/**
 * Get IP from ip_id
 * @since 2.2
 *
 * @param $ip_id string ID for an IP
 *
 * @return string IP address for given ID
 */
function cerber_get_ip_id( $ip_id ) {
	$ip = str_replace( '-', '.', $ip_id, $count );
	$ip = str_replace( '_', ':', $ip );

	return $ip;
}
/**
 * Check if given IP address is a valid single IP v4 address
 * 
 * @param $ip
 *
 * @return bool
 */
function cerber_is_ipv4( $ip ) {
	return ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) ? true : false;
}

function cerber_is_ipv6( $ip ) {
	return ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) ? true : false;
}

/**
 * Check if a given IP address belongs to a private network (private IP).
 * Universal: support IPv6 and IPv4.
 *
 * @param $ip string An IP address to check
 *
 * @return bool True if IP is in the private range, false otherwise
 */
function is_ip_private($ip) {

	if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE ) ) {
		return true;
	}
	elseif ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE ) ) {
		return true;
	}

	return false;
}

function cerber_is_ip( $ip ) {
	return filter_var( $ip, FILTER_VALIDATE_IP );
}

/**
 * Expands shortened IPv6 to full IPv6 address
 *
 * @param $ip string IPv6 address
 *
 * @return string Full IPv6 address
 */
function cerber_ipv6_expand( $ip ) {
	$full_hex = (string) bin2hex( inet_pton( $ip ) );

	return implode( ':', str_split( $full_hex, 4 ) );
}

/**
 * Compress full IPv6 to shortened
 *
 * @param $ip string IPv6 address
 *
 * @return string Full IPv6 address
 */
function cerber_ipv6_short( $ip ) {
	if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
		return $ip;
	}

	return inet_ntop( inet_pton( $ip ) );
}

/**
 * Convert multilevel object or array of objects to associative array recursively
 *
 * @param $var object|array
 *
 * @return array result of conversion
 * @since 3.0
 */
function obj_to_arr_deep( $var ) {
	if ( is_object( $var ) ) {
		$var = get_object_vars( $var );
	}
	if ( is_array( $var ) ) {
		return array_map( __FUNCTION__, $var );
	}

	return $var;
}

/**
 * Search for a key in the given multidimensional array
 *
 * @param $array
 * @param $needle
 *
 * @return bool
 */
function recursive_search_key($array, $needle){
	foreach($array as $key => $value){
		if ((string)$key == (string)$needle){
			return true;
		}
		if(is_array($value)){
			$ret = recursive_search_key($value, $needle);
			if ($ret == true) return true;
		}
	}
	return false;
}

/**
 * array_column() implementation for PHP < 5.5
 *
 * @param array $arr Multidimensional array
 * @param string $column Column key
 *
 * @return array
 */
function crb_array_column( $arr = array(), $column = '' ) {
	global $x_column;
	$x_column = $column;

	$ret = array_map( function ( $element ) {
		global $x_column;

		return $element[ $x_column ];
	}, $arr );

	$ret = array_values( $ret );

	return $ret;
}

/**
 * @param $arr array
 * @param $key string|integer|array
 * @param $default mixed
 * @param $pattern string REGEX pattern for value validation, UTF is not supported
 *
 * @return mixed
 */
function crb_array_get( &$arr, $key, $default = false, $pattern = '' ) {
	if ( is_array( $key ) ) {
		$ret = crb_array_get_deep( $arr, $key );
		if ( $ret === null ) {
			$ret = $default;
		}
	}
	else {
		$ret = ( isset( $arr[ $key ] ) ) ? $arr[ $key ] : $default;
	}

	if ( ! $pattern ) {
		return $ret;
	}

	if ( ! is_array( $ret ) ) {
		if ( @preg_match( '/^' . $pattern . '$/i', $ret ) ) {
			return $ret;
		}

		return $default;
	}

	global $cerber_temp;
	$cerber_temp = $pattern;

	array_walk( $ret, function ( &$item ) {
		global $cerber_temp;
		if ( ! @preg_match( '/^' . $cerber_temp . '$/i', $item ) ) {
			$item = '';
		}
	} );

	return array_filter( $ret );
}

/**
 * Retrieve element from multi-dimensional array
 *
 * @param array $arr
 * @param array $keys Keys (dimensions)
 *
 * @return mixed|null Value of the element if it's defined, null otherwise
 */
function crb_array_get_deep( &$arr, $keys ) {
	$key = array_shift( $keys );
	if ( isset( $arr[ $key ] ) ) {
		if ( empty( $keys ) ) {
			return $arr[ $key ];
		}

		return crb_array_get_deep( $arr[ $key ], $keys );
	}

	return null;
}

/**
 * Compare two arrays by using keys: check if two arrays have different set of keys
 *
 * @param $arr1 array
 * @param $arr2 array
 *
 * @return bool true if arrays have different set of keys
 */
function crb_array_diff_keys( &$arr1, &$arr2 ) {
	if ( count( $arr1 ) != count( $arr2 ) ) {
		return true;
	}
	if ( array_diff_key( $arr1, $arr2 ) ) {
		return true;
	}
	if ( array_diff_key( $arr2, $arr1 ) ) {
		return true;
	}

	return false;
}


/**
 * @param string|array $val
 *
 * for objects see map_deep();
 */
function crb_trim_deep( &$val ) {
	if ( ! is_array( $val ) ) {
		$val = trim( $val );
	}

	array_walk_recursive( $val, function ( &$v ) {
		$v = trim( $v );
	} );
}

/**
 * @param string|array $val
 *
 * Note: _sanitize_text_fields removes HTML tags
 *
 */
function crb_sanitize_deep( &$val ) {
	if ( ! is_array( $val ) ) {
		$val = _sanitize_text_fields( $val, true );
	}

	array_walk_recursive( $val, function ( &$v ) {
		$v = _sanitize_text_fields( $v, true );
	} );
}

/**
 * Return true if a REST API URL has been requested
 *
 * @return bool
 * @since 3.0
 */
function cerber_is_rest_url(){
	global $wp_rewrite;
	static $ret = null;

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return true;
	}

	if ( isset( $_REQUEST['rest_route'] ) ) {
		return true;
	}

	if ( isset( $ret ) ) {
		return $ret;
	}

	if ( ! $wp_rewrite ) { // see get_rest_url() in the multisite mode
		return false;
	}

	$ret = false;

	// @since 8.1

	$path = CRB_Request::get_request_path();

	if ( 0 === strpos( $path, '/' . rest_get_url_prefix() . '/' ) ) {
		if ( 0 === strpos( cerber_get_home_url() . $path , crb_get_rest_url() ) ) {
			$ret = true;
		}
	}

	return $ret;
}

/**
 * Check if the current query is HTTP and GET method is being
 *
 * @return bool true if request method is GET
 */
function cerber_is_http_get() {
	if ( nexus_is_valid_request() ) {
		return ! nexus_request_data()->is_post;
	}
	if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == 'GET' ) {
		return true;
	}

	return false;
}

/**
 * Check if the current query is HTTP and POST method is being
 *
 * @return bool true if request method is GET
 */
function cerber_is_http_post() {
	if ( nexus_is_valid_request() ) {
		return nexus_request_data()->is_post;
	}

	if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		return true;
	}

	return false;
}

/**
 * Checks if it's a wp cron request
 *
 * @return bool
 */
function cerber_is_wp_cron() {
	if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
		return true;
	}
	if ( CRB_Request::is_script( '/wp-cron.php' ) ) {
		return true;
	}

	return false;
}

function cerber_is_wp_ajax( $use_filter = false ) {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return true;
	}

	// @since 8.1.3
	if ( $use_filter && function_exists( 'wp_doing_ajax' ) ) {
		return wp_doing_ajax();
	}

	return false;
}

/**
 *
 * @param $key string
 * @param $pattern string
 *
 * @return bool|array|string
 */
function cerber_get_get( $key, $pattern = '' ) {
	return crb_array_get( $_GET, $key, false, $pattern );
}

/**
 *
 * @param $key string
 * @param $pattern string
 *
 * @return bool|array|string
 */
function cerber_get_post( $key, $pattern = '' ) {
	return crb_array_get( $_POST, $key, false, $pattern );
}

/**
 * Admin page query params
 *
 * @param string $key
 * @param string $pattern
 *
 * @return mixed
 */
function crb_get_query_params( $key = null, $pattern = '' ) {
	if ( nexus_is_valid_request() ) {
		if ( $key ) {
			return crb_array_get( nexus_request_data()->get_params, $key, false, $pattern );
		}

		return nexus_request_data()->get_params;
	}

	// Local context

	if ( $key ) {
		return cerber_get_get( $key, $pattern );
	}

	return $_GET;
}

function crb_get_post_fields( $key = null, $default = false, $pattern = '' ) {
	if ( nexus_is_valid_request() ) {
		if ( nexus_request_data()->is_post ) {
			return nexus_request_data()->get_post_fields( $key, $default, $pattern );
		}

		return array();
	}

	if ( $key ) {
		return crb_array_get( $_POST, $key, $default, $pattern );
	}

	return $_POST;
}

function crb_get_request_fields() {
	if ( nexus_is_valid_request() ) {
		$ret = nexus_request_data()->get_params;
		if ( nexus_request_data()->is_post ) {
			$ret = array_merge( $ret, nexus_request_data()->get_post_fields() );
		}

		return $ret;
	}

	return $_REQUEST;
}

/**
 * Is requested REST API namespace whitelisted
 *
 * @return bool
 */
function cerber_is_route_allowed() {

	$list = crb_get_settings( 'restwhite' );

	if ( ! is_array( $list ) || empty( $list ) ) {
		return false;
	}

	$rest_path = crb_get_rest_path();

	$namespace = substr( $rest_path, 0, strpos( $rest_path, '/' ) );

	foreach ( $list as $exception ) {
		if ($exception == $namespace) {
			return true;
		}
	}

	return false;
}
/**
 * Is requested REST API route blocked (not allowed)
 *
 * @return bool
 */
/*
function cerber_is_route_blocked() {
	if ( crb_get_settings( 'norestuser' ) ) {
		$path = explode( '/', crb_get_rest_path() );
		if ( $path && count( $path ) > 2 && $path[0] == 'wp' && $path[2] == 'users' ) {
			return true;
		}
	}
	return false;
}*/

function cerber_is_rest_permitted() {
	global $cerber_req_status;

	$opt = crb_get_settings();

	if ( ! empty( $opt['norestuser'] ) ) {
		$path = explode( '/', crb_get_rest_path() );
		if ( $path && count( $path ) > 2 && $path[0] == 'wp' && $path[2] == 'users' ) {
			if ( is_super_admin() ) {
				return true; // @since 8.3.4
			}

			return false;
		}

	}

	if ( empty( $opt['norest'] ) ) {
		return true;
	}

	if ( $opt['restauth'] && is_user_logged_in() ) {
		return true;
	}

	if ( ! empty( $opt['restwhite'] ) || is_array( $opt['restwhite'] ) ) {
		$rest_path = crb_get_rest_path();
		$namespace = substr( $rest_path, 0, strpos( $rest_path, '/' ) );
		foreach ( $opt['restwhite'] as $exception ) {
			if ( $exception == $namespace ) {
				$cerber_req_status = 503;
				return true;
			}
		}

	}

	if ( ! empty( $opt['restroles'] ) || is_array( $opt['restroles'] ) ) {
		if ( cerber_user_has_role( $opt['restroles'] ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Check if a user has at least one role from the list
 *
 * @param array $roles
 * @param null $user_id
 *
 * @return bool
 */
function cerber_user_has_role( $roles = array(), $user_id = null ) {
	if ( ! $user_id ) {
		$user = wp_get_current_user();
	}
	else {
		$user = get_userdata( $user_id );
	}

	if ( ! $user || empty( $user->roles ) ) {
		return false;
	}

	if ( ! is_array( $roles ) ) {
		$roles = array( $roles );
	}

	if ( array_intersect( $user->roles, $roles ) ) {
		return true;
	}

	return false;
}

/**
 * Check if all user roles are in the list
 *
 * @param array|string $roles
 * @param int $user_id
 *
 * @return bool false if the user has role(s) other than listed in $roles
 */
function crb_user_has_role_strict( $roles, $user_id ) {
	if ( ! $user_id || ! $user = get_userdata( $user_id ) ) {
		return false;
	}

	if ( ! is_array( $roles ) ) {
		$roles = array( $roles );
	}

	$user_roles = ( is_array( $user->roles ) ) ? $user->roles : array();

	return ( ! array_diff( $user_roles, $roles ) );
}

function crb_get_rest_path() {
	static $ret;
	if ( isset( $ret ) ) {
		return $ret;
	}

	if ( isset( $_REQUEST['rest_route'] ) ) {
		$ret = ltrim( $_REQUEST['rest_route'], '/' );
	}
	elseif ( cerber_is_permalink_enabled() ) {
		$path = CRB_Request::get_request_path();
		$pos = strlen( crb_get_rest_url() );
		$ret = substr( cerber_get_home_url() . $path, $pos ); // @since 8.1
		$ret = trim( $ret, '/' );
	}

	return $ret;
}

function crb_get_rest_url() {
	static $ret;

	if ( ! isset( $ret ) ) {
		$ret = get_rest_url();
	}

	return $ret;
}

function crb_is_user_blocked( $uid ) {
	if ( $uid
	     && ( $m = get_user_meta( $uid, CERBER_BUKEY, 1 ) )
	     && ! empty( $m['blocked'] )
	     && $m[ 'u' . $uid ] == $uid ) {
		return $m;
	}

	return false;
}

/**
 * Return the last element in the path of the requested URI.
 *
 * @return bool|string
 */
function cerber_last_uri() {
	static $ret;

	if ( isset( $ret ) ) {
		return $ret;
	}

	$ret = strtolower( $_SERVER['REQUEST_URI'] );

	if ( $pos = strpos( $ret, '?' ) ) {
		$ret = substr( $ret, 0, $pos );
	}

	if ( $pos = strpos( $ret, '#' ) ) {
		$ret = substr( $ret, 0, $pos ); // @since 8.1 - malformed request URI
	}

	$ret = rtrim( $ret, '/' );
	$ret = substr( strrchr( $ret, '/' ), 1 );

	return $ret;
}

/**
 * Return the name of an executable script in the requested URI if it's present
 *
 * @return bool|string The script name or false if executable script is not detected
 */
function cerber_get_uri_script() {
	static $ret;

	if ( isset( $ret ) ) {
		return $ret;
	}

	$last = cerber_last_uri();
	if ( cerber_detect_exec_extension( $last ) ) {
		$ret = $last;
	}
	else {
		$ret = false;
	}

	return $ret;
}

/**
 * Detects an executable extension in a filename.
 * Supports double and N fake extensions.
 *
 * @param $line string Filename
 * @param array $extra A list of additional extensions to detect
 *
 * @return bool|string An extension if it's found, false otherwise
 */
function cerber_detect_exec_extension( $line, $extra = array() ) {
	static $executable = array( 'php', 'phtm', 'phtml', 'phps', 'shtm', 'shtml', 'jsp', 'asp', 'aspx', 'axd', 'exe', 'com', 'cgi', 'pl', 'py', 'pyc', 'pyo' );
	static $not_exec = array( 'jpg', 'png', 'svg', 'css', 'txt' );

	if ( empty( $line ) || ! strrpos( $line, '.' ) ) {
		return false;
	}

	if ( $extra ) {
		$ex_list = array_merge( $executable, $extra );
	}
	else {
		$ex_list = $executable;
	}

	$line = trim( $line );
	$line = trim( $line, '/' );

	$parts = explode( '.', $line );
	array_shift( $parts );

	// First and last are critical for most server environments
	$first_ext = array_shift( $parts );
	$last_ext  = array_pop( $parts );

	if ( $first_ext ) {
		$first_ext = strtolower( $first_ext );
		if ( ! in_array( $first_ext, $not_exec ) ) {
			if ( in_array( $first_ext, $ex_list ) ) {
				return $first_ext;
			}
			if ( preg_match( '/php\d+/', $first_ext ) ) {
				return $first_ext;
			}
		}
	}

	if ( $last_ext ) {
		$last_ext = strtolower( $last_ext );
		if ( in_array( $last_ext, $ex_list ) ) {
			return $last_ext;
		}
		if ( preg_match( '/php\d+/', $last_ext ) ) {
			return $last_ext;
		}
	}

	return false;
}

/**
 * Remove extra slashes \ / from a script file name
 *
 * @return string|bool
 */
function cerber_script_filename() {
	return preg_replace('/[\/\\\\]+/','/',$_SERVER['SCRIPT_FILENAME']); // Windows server
}

function cerber_script_exists( $uri ) {
	$script_filename = cerber_script_filename();
	if ( is_multisite() && ! is_subdomain_install() ) {
		$path = explode( '/', $uri );
		if ( count( $path ) > 1 ) {
			$last = array_pop( $path );
			$virtual_sub_folder = array_pop( $path );
			$uri = implode( '/', $path ) . '/' . $last;
		}
	}
	if ( false === strrpos( $script_filename, $uri ) ) {
		return false;
	}

	return true;
}

/**
 * Activity labels and statues
 *
 * @param string $type
 *
 * @return mixed
 */
function cerber_get_labels( $type = 'activity' ) {

	if ( ! $labels = cerber_cache_get( 'labels' ) ) {

		// Initialize it

		$labels = array(
			'activity' => array(),
			'status'   => array(),
		);

		$act = &$labels['activity'];

		// User actions
		$act[1] = __( 'User created', 'wp-cerber' );
		$act[2] = __( 'User registered', 'wp-cerber' );
		$act[3] = __( 'User deleted', 'wp-cerber' );
		$act[5] = __( 'Logged in', 'wp-cerber' );
		$act[6] = __( 'Logged out', 'wp-cerber' );
		$act[7] = __( 'Login failed', 'wp-cerber' );

		// Cerber actions - IP specific - lockouts
		$act[10] = __( 'IP blocked', 'wp-cerber' );
		$act[11] = __( 'IP subnet blocked', 'wp-cerber' );

		// Cerber actions - common
		$act[12] = __( 'Citadel activated!', 'wp-cerber' );
		$act[16] = __( 'Spam comment denied', 'wp-cerber' );
		$act[17] = __( 'Spam form submission denied', 'wp-cerber' );
		$act[18] = __( 'Form submission denied', 'wp-cerber' );
		$act[19] = __( 'Comment denied', 'wp-cerber' );

		// Cerber status now
		//$act[13]=__('Locked out','wp-cerber');
		//$act[14]=__('IP blacklisted','wp-cerber');
		//$act[15]=__('Malicious activity detected','wp-cerber');
		// --------------------------------------------------------------

		// Other actions
		$act[20] = __( 'Password changed', 'wp-cerber' );
		$act[21] = __( 'Password reset requested', 'wp-cerber' );

		$act[40] = __( 'reCAPTCHA verification failed', 'wp-cerber' );
		$act[41] = __( 'reCAPTCHA settings are incorrect', 'wp-cerber' );
		$act[42] = __( 'Request to the Google reCAPTCHA service failed', 'wp-cerber' );

		$act[50] = __( 'Attempt to access prohibited URL', 'wp-cerber' );
		$act[51] = __( 'Attempt to log in with non-existing username', 'wp-cerber' );
		$act[52] = __( 'Attempt to log in with prohibited username', 'wp-cerber' );
		// @since 4.9 // TODO 53 & 54 should be a cerber action?
		$act[53] = __( 'Attempt to log in denied', 'wp-cerber' );
		$act[54] = __( 'Attempt to register denied', 'wp-cerber' );
		$act[55] = __( 'Probing for vulnerable code', 'wp-cerber' );
		$act[56] = __( 'Attempt to upload malicious file denied', 'wp-cerber' );
		$act[57] = __( 'File upload denied', 'wp-cerber' );

		$act[70] = __( 'Request to REST API denied', 'wp-cerber' );
		$act[71] = __( 'XML-RPC request denied', 'wp-cerber' );
		$act[72] = __( 'User creation denied', 'wp-cerber' );
		$act[73] = __( 'User row update denied', 'wp-cerber' );
		$act[74] = __( 'Role update denied', 'wp-cerber' );
		$act[75] = __( 'Setting update denied', 'wp-cerber' );
		$act[76] = __( 'User metadata update denied', 'wp-cerber' );

		$act[100] = __( 'Malicious request denied', 'wp-cerber' );

		// BuddyPress
		$act[200] = __( 'User activated', 'wp-cerber' );

		// Nexus slave
		$act[300] = __( 'Invalid master credentials', 'wp-cerber' );

		$act[400] = 'Two-factor authentication enforced';

		// Statuses

		$sts = &$labels['status'];

		$sts[11] = __( 'Bot detected', 'wp-cerber' );
		$sts[12] = __( 'Citadel mode is active', 'wp-cerber' );
		$sts[13] = __( 'Locked out', 'wp-cerber' );
		$sts[13] = __( 'IP address is locked out', 'wp-cerber' );
		$sts[14] = __( 'IP blacklisted', 'wp-cerber' );
		$sts[15] = __( 'Malicious activity detected', 'wp-cerber' );
		$sts[16] = __( 'Blocked by country rule', 'wp-cerber' );
		$sts[17] = __( 'Limit reached', 'wp-cerber' );
		$sts[18] = __( 'Multiple suspicious activities', 'wp-cerber' );
		$sts[19] = __( 'Denied', 'wp-cerber' ); // @since 6.7.5

		$sts[20] = __( 'Suspicious number of fields', 'wp-cerber' );
		$sts[21] = __( 'Suspicious number of nested values', 'wp-cerber' );
		$sts[22] = __( 'Malicious code detected', 'wp-cerber' );
		$sts[23] = __( 'Suspicious SQL code detected', 'wp-cerber' );
		$sts[24] = __( 'Suspicious JavaScript code detected', 'wp-cerber' );
		$sts[25] = __( 'Blocked by administrator', 'wp-cerber' );
		$sts[26] = __( 'Site policy enforcement', 'wp-cerber' );
		$sts[27] = __( '2FA code verified', 'wp-cerber' );
		$sts[28] = __( 'Initiated by the user', 'wp-cerber' );

		$sts[30] = 'Username is prohibited';
		$sts[31] = __( 'Email address is prohibited', 'wp-cerber' );
		$sts[32] = 'User role is prohibited';
		$sts[33] = __( 'Permission denied', 'wp-cerber' );
		$sts[34] = 'Unauthorized access denied';
		$sts[35] = __( 'Invalid user', 'wp-cerber' );
		$sts[36] = __( 'Incorrect password', 'wp-cerber' );

		// @since 8.6.4
		$sts[500] = __( 'IP whitelisted', 'wp-cerber' );
		$sts[501] = 'URL whitelisted';
		$sts[502] = 'Query whitelisted';
		$sts[503] = 'Namespace whitelisted';

		cerber_cache_set( 'labels', $labels );
	}

	return $labels[ $type ];
}

function crb_get_activity_set( $slice = 'malicious', $implode = false ) {

	$ret = array();

	switch ( $slice ) {
		case 'malicious':
			$ret = array( 16, 17, 40, 50, 51, 52, 53, 54, 55, 56, 100 );
			break;
		case 'black': // Like 'malicious' but will cause an IP lockout when hit the limit
			$ret = array( 16, 17, 40, 50, 51, 52, 55, 56, 100, 300 );
			break;
		case 'suspicious': // Uses when an admin inspects logs
			$ret = array( 10, 11, 16, 17, 20, 40, 50, 51, 52, 53, 54, 55, 56, 100, 70, 71, 72, 73, 74, 75, 76, 300 );
			break;
		case 'dashboard': // Important events for the plugin dashboard
			$ret = array( 1, 2, 5, 10, 11, 12, 16, 17, 18, 19, 40, 41, 42, 50, 51, 52, 53, 54, 55, 56, 72, 73, 74, 75, 76, 100, 300 );
			break;
		case 'blocked': // IP or subnet was blocked
			$ret = array( 10, 11 );
	}

	if ( $implode ) {
		return implode( ',', $ret );
	}

	return $ret;
}


function cerber_get_reason( $reason_id = null ) {

	if ( ! $labels = cerber_cache_get( 'reasons' ) ) {

		$labels      = array();
		$labels[701] = __( 'Limit on login attempts is reached', 'wp-cerber' );
		$labels[702] = __( 'Attempt to access', 'wp-cerber' );
		$labels[702] = __( 'Attempt to access prohibited URL', 'wp-cerber' );
		$labels[703] = __( 'Attempt to log in with non-existing username', 'wp-cerber' );
		$labels[704] = __( 'Attempt to log in with prohibited username', 'wp-cerber' );
		$labels[705] = __( 'Limit on failed reCAPTCHA verifications is reached', 'wp-cerber' );
		$labels[706] = __( 'Bot activity is detected', 'wp-cerber' );
		$labels[707] = __( 'Multiple suspicious activities were detected', 'wp-cerber' );
		$labels[708] = __( 'Probing for vulnerable code', 'wp-cerber' );
		$labels[709] = __( 'Malicious code detected', 'wp-cerber' );
		$labels[710] = __( 'Attempt to upload a file with malicious code', 'wp-cerber' );

		$labels[711] = __( 'Multiple erroneous requests', 'wp-cerber' );
		$labels[712] = __( 'Multiple suspicious requests', 'wp-cerber' );
		$labels[721] = 'Limit on 2FA verifications is reached';

		cerber_cache_set( 'reasons', $labels );

	}

	if ( $reason_id ) {
		if ( isset( $labels[ $reason_id ] ) ) {
			return $labels[ $reason_id ];
		}

		return __( 'Unknown', 'wp-cerber' );
	}

	return $labels;

}

function cerber_db_error_log( $msg = null ) {
	global $wpdb;
	if ( ! $msg ) {
		$msg = array( $wpdb->last_error, $wpdb->last_query, date( 'Y-m-d H:i:s' ) );
	}
	$old = get_site_option( '_cerber_db_errors' );
	if ( ! $old ) {
		$old = array();
	}
	update_site_option( '_cerber_db_errors', array_merge( $old, $msg ) );
}

/**
 *
 * @param string|array $msg
 */
function cerber_admin_notice( $msg ) {
	crb_admin_add_msg( $msg, 'admin_notice' );
}
/**
 *
 * @param string|array $msg
 */
function cerber_admin_message( $msg ) {
	crb_admin_add_msg( $msg );
}

function crb_admin_add_msg( $msg, $type = 'admin_message' ) {
	global $cerber_doing_upgrade;

	if ( ! $msg || $cerber_doing_upgrade ) {
		return;
	}

	if ( ! is_array( $msg ) ) {
		$msg = array( $msg );
	}

	$set = cerber_get_set( $type );

	if ( ! $set || ! is_array( $set ) ) {
		$set = array();
	}

	cerber_update_set( $type, array_merge( $set, $msg ) );
}

function crb_clear_admin_msg(){
	cerber_update_set( 'admin_notice', array() );
	cerber_update_set( 'admin_message', array() );
	cerber_update_set( 'cerber_admin_wide', '' );
}

/*
	Check if currently displayed page is a Cerber admin dashboard page with optional checking a set of GET params
*/
function cerber_is_admin_page( $force = false, $params = array() ) {

	if ( ! is_admin()
	     && ! nexus_is_valid_request() ) {
		return false;
	}

	$get = crb_get_query_params();
	$ret = false;

	if ( isset( $get['page'] ) && false !== strpos( $get['page'], 'cerber-' ) ) {
		$ret = true;
		if ( $params ) {
			foreach ( $params as $param => $value ) {
				if ( ! isset( $get[ $param ] ) ) {
					$ret = false;
					break;
				}
				if ( ! is_array( $value ) ) {
					if ( $get[ $param ] != $value ) {
						$ret = false;
						break;
					}
				}
				elseif ( ! in_array( $get[ $param ], $value ) ) {
					$ret = false;
					break;
				}
			}
		}
	}
	if ( $ret || ! $force ) {
		return $ret;
	}

	if ( ! function_exists( 'get_current_screen' ) || ! $screen = get_current_screen() ) {
		return false;
	}

	if ( $screen->base == 'plugins' ) {
		return true;
	}

	/*
	if ($screen->parent_base == 'options-general') return true;
	if ($screen->parent_base == 'settings') return true;
	*/

	return false;
}

/**
 * Return human readable "ago" time
 * 
 * @param $time integer Unix timestamp - time of an event
 *
 * @return string
 */
function cerber_ago_time( $time ) {
	$diff = (int) abs( time() - $time );
	if ( $diff < MINUTE_IN_SECONDS ) {
		$secs = ( $diff <= 1 ) ? 1 : $diff;
		/* translators: Time difference between two dates, in seconds (sec=second). 1: Number of seconds */
		$dt = sprintf( _n( '%s sec', '%s secs', $secs ), $secs );
	}
	else {
		$dt = human_time_diff( $time );
	}

	// _x( 'at', 'preposition of time',
	return ( $time <= time() ) ? sprintf( __( '%s ago' ), $dt ) : sprintf( _x( 'in %s', 'preposition of a period of time like: in 6 hours', 'wp-cerber' ), $dt );
}

function cerber_auto_date( $time ) {
	if ( ! $time ) {
		return __( 'Never', 'wp-cerber' );
	}
	return $time < ( time() - DAY_IN_SECONDS ) ? cerber_date( $time ) : cerber_ago_time( $time );
}

/**
 * Format date according to user settings and timezone
 *
 * @param $timestamp int Unix timestamp
 * @param $purify boolean If true adds html to have a better look on a web page
 *
 * @return string
 */
function cerber_date( $timestamp, $purify = true ) {
	static $gmt_offset;

	if ( $gmt_offset === null ) {
		$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	}

	$timestamp = $gmt_offset + absint( $timestamp );

	// @since 8.6.4: snippet is taken from new date_i18n()
	if ( function_exists( 'wp_date' ) ) { // wp_date() introduced in WP 5.3
		$local_time = gmdate( 'Y-m-d H:i:s', $timestamp );
		$timezone = wp_timezone();
		$datetime = date_create( $local_time, $timezone );
		$date = wp_date( cerber_get_dt_format(), $datetime->getTimestamp(), $timezone );
	}
	else { // Older WP
		$date = date_i18n( cerber_get_dt_format(), $timestamp );
	}

	if ( $purify ) {
		$date = str_replace( array( ',', ' am', ' pm', ' AM', ' PM' ), array( ',<wbr>', '&nbsp;am', '&nbsp;pm', '&nbsp;AM', '&nbsp;PM' ), $date );
	}

	return $date;
}

function cerber_get_dt_format() {
	static $ret;

	if ( $ret !== null) {
		return $ret;
	}

	if ( $ret = crb_get_settings( 'dateformat' ) ) {
		return $ret;
	}

	$ret = crb_get_default_dt_format();
	return $ret;

}

function crb_get_default_dt_format() {
	$tf = get_option( 'time_format' );
	$df = get_option( 'date_format' );

	return $df . ', ' . $tf;
}

function cerber_is_ampm() {
	if ( 'a' == strtolower( substr( trim( get_option( 'time_format' ) ), - 1 ) ) ) {
		return true;
	}

	return false;
}

function cerber_sec_from_time( $time ) {
	list( $h, $m ) = explode( ':', trim( $time ) );
	$h   = absint( $h );
	$m   = absint( $m );
	$ret = $h * 3600 + $m * 60;

	if ( strpos( strtolower( $time ), 'pm' ) ) {
		$ret += 12 * 3600;
	}

	return $ret;
}

function cerber_percent($one,$two){
	if ($one == 0) {
		if ($two > 0) $ret = '100';
		else $ret = '0';
	}
	else {
		$ret = round (((($two - $one)/$one)) * 100);
	}
	$style='';
	if ($ret < 0) $style='color:#008000';
	elseif ($ret > 0) $style='color:#FF0000';
	if ($ret > 0)	$ret = '+'.$ret;
	return '<span style="'.$style.'">'.$ret.' %</span>';
}

function crb_size_format( $fsize ) {
	$fsize = absint( $fsize );

	return ( $fsize < 1024 ) ? $fsize . '&nbsp;' . __( 'Bytes', 'wp-cerber' ) : size_format( $fsize );
}

/**
 * Return a user by login or email with automatic detection
 *
 * @param $login_email string login or email
 *
 * @return false|WP_User
 */
function cerber_get_user( $login_email ) {
	if ( is_email( $login_email ) ) {
		return get_user_by( 'email', $login_email );
	}

	return get_user_by( 'login', $login_email );
}

/**
 * Check if a DB table exists
 *
 * @param $table
 *
 * @return bool true if table exists in the DB
 */
function cerber_is_table( $table ) {
	global $wpdb;
	if ( ! $wpdb->get_row( "SHOW TABLES LIKE '" . $table . "'" ) ) {
		return false;
	}

	return true;
}

/**
 * Check if a column is defined in a table
 *
 * @param $table string DB table name
 * @param $column string Field name
 *
 * @return bool true if field exists in a table
 */
function cerber_is_column( $table, $column ) {

	$table  = preg_replace( '/[^\w\-]/', '', $table );
	$column = preg_replace( '/[^\w\-]/', '', $column );

	if ( cerber_db_get_results( 'SHOW FIELDS FROM ' . $table . ' WHERE FIELD = "' . $column . '"' ) ) {
		return true;
	}

	return false;
}

/**
 * Check if a table has an index
 *
 * @param $table string DB table name
 * @param $key string Index name
 *
 * @return bool true if an index defined for a table
 */
function cerber_is_index( $table, $key ) {

	$table = preg_replace( '/[^\w\-]/', '', $table );
	$key   = preg_replace( '/[^\w\-]/', '', $key );

	if ( cerber_db_get_results( 'SHOW INDEX FROM ' . $table . ' WHERE KEY_NAME = "' . $key . '"' ) ) {
		return true;
	}

	return false;
}

/**
 * Return reCAPTCHA language code for reCAPTCHA widget
 *
 * @return string
 */
function cerber_recaptcha_lang() {
	static $lang = '';
	if (!$lang) {
		$lang = crb_get_bloginfo( 'language' );
		//$trans = array('en-US' => 'en', 'de-DE' => 'de');
		//if (isset($trans[$lang])) $lang = $trans[$lang];
		$lang = substr( $lang, 0, 2 );
	}

	return $lang;
}

/*
	Checks for a new version of WP Cerber and creates messages if needed
*/
function cerber_check_for_newer() {
	if ( ! $updates = get_site_transient( 'update_plugins' ) ) {
		return false;
	}

	$ret = false;
	$key = CERBER_PLUGIN_ID;

	if ( isset( $updates->checked[ $key ] ) && isset( $updates->response[ $key ] ) ) {
		$old = $updates->checked[ $key ];
		$new = $updates->response[ $key ]->new_version;
		if ( 1 === version_compare( $new, $old ) ) { // current version is lower than latest
			$msg = sprintf( __( 'A new version of %s is available. Please install it.', 'wp-cerber' ), 'WP Cerber Security' );
			$ret = array( 'msg' => $msg, 'ver' => $new );
		}
	}

	return $ret;
}

/**
 * Detects known browsers/crawlers and platform in User Agent string
 *
 * @param $ua
 *
 * @return string Sanitized browser name and platform on success
 * @since 6.0
 */
function cerber_detect_browser( $ua ) {
	$ua  = trim( $ua );

	if ( empty( $ua ) ) {
		return __( 'Not specified', 'wp-cerber' );
	}

	if ( preg_match( '/\(compatible\;(.+)\)/i', $ua, $matches ) ) {
		$bot_info = explode( ';', $matches[1] );
		foreach ( $bot_info as $item ) {
			if ( stripos( $item, 'bot' )
			     || stripos( $item, 'crawler' )
			     || stripos( $item, 'spider' )
			     || stripos( $item, 'Yandex' )
			     || stripos( $item, 'Yahoo! Slurp' )
			) {
				if ( strpos( $ua, 'Android' ) ) {
					$item .= ' Mobile';
				}
				return htmlentities( $item );
			}
		}
	}
	elseif ( strpos( $ua, 'google.com' ) ) {
		// Various Google bots

		$ret = '';

		if ( false !== strpos( $ua, 'Googlebot' ) ) {
			if ( strpos( $ua, 'Android' ) ) {
				$ret = 'Googlebot Mobile';
			}
			elseif ( false !== strpos( $ua, 'Mozilla' ) ) {
				$ret = 'Googlebot Desktop';
			}
		}
		elseif ( preg_match( '/AdsBot-Google-Mobile|AdsBot-Google|APIs-Google/', $ua, $matches ) ) {
			$ret = $matches[0];
		}

		if ( $ret ) {
			return htmlentities( $ret );
		}
		else {
			return __( 'Unknown', 'wp-cerber' );
		}
	}
	elseif ( 0 === strpos( $ua, 'Googlebot' ) ) {
		if ( preg_match( '/Googlebot-\w+/', $ua, $matches ) ) {
			return $matches[0];
		}
	}
	elseif ( 0 === strpos( $ua, 'WordPress/' ) ) {
		list( $ret ) = explode( ';', $ua, 2 );
		return htmlentities( $ret );
	}
	elseif ( 0 === strpos( $ua, 'PayPal IPN' ) ) {
		return 'PayPal Payment Notification';
	}
	elseif (0 === strpos( $ua, 'Wget/' )){
		return htmlentities( $ua );
	}
	elseif (0 === strpos( $ua, 'Mediapartners-Google' )){
		return 'AdSense Crawler';
	}


	$browsers = array(
		'Firefox'   => 'Firefox',
		'OPR'       => 'Opera',
		'Opera'     => 'Opera',
		'YaBrowser' => 'Yandex Browser',
		'Trident'   => 'Internet Explorer',
		'IE'        => 'Internet Explorer',
		'Edge'      => 'Microsoft Edge',
		'Chrome'    => 'Chrome',
		'Safari'    => 'Safari',
		'Lynx'      => 'Lynx',
	);

	$systems  = array( 'Android' , 'Linux', 'Windows', 'iPhone', 'iPad', 'Macintosh', 'OpenBSD', 'Unix' );

	$b = '';
	foreach ( $browsers as $browser_id => $browser ) {
		if ( false !== strpos( $ua, $browser_id ) ) {
			$b = $browser;
			break;
		}
	}

	$s = '';
	foreach ( $systems as $system ) {
		if ( false !== strpos( $ua, $system ) ) {
			$s = $system;
			break;
		}
	}

	if ($b == 'Lynx' && !$s) {
		$s = 'Linux';
	}

	if ( $b && $s ) {
		$ret = $b . ' on ' . $s;
	}
	elseif ( 0 === strpos( $ua, 'python-requests' ) ) {
		$ret = 'Python Script';
	}
	elseif ( 0 === strpos( $ua, 'ApacheBench' ) ) {
		$ret = $ua;
	}
	else {
		$ret = __( 'Unknown', 'wp-cerber' );
	}

	return htmlentities( $ret );
}

/**
 * Is user agent string indicates bot (crawler)
 *
 * @param $ua
 *
 * @return integer 1 if ua string contains a bot definition, 0 otherwise
 * @since 6.0
 */
function cerber_is_crawler( $ua ) {
	if ( ! $ua ) {
		return 0;
	}
	$ua = strtolower( $ua );
	if ( preg_match( '/\(compatible\;(.+)\)/', $ua, $matches ) ) {
		$bot_info = explode( ';', $matches[1] );
		foreach ( $bot_info as $item ) {
			if ( strpos( $item, 'bot' )
			     || strpos( $item, 'crawler' )
			     || strpos( $item, 'spider' )
			     || strpos( $item, 'Yahoo! Slurp' )
			) {
				return 1;
			}
		}
	}
	elseif (0 === strpos( $ua, 'Wget/' )){
		return 1;
	}

	return 0;
}

/**
 * Natively escape a string for use in an SQL statement
 * The reason: https://make.wordpress.org/core/2017/10/31/changed-behaviour-of-esc_sql-in-wordpress-4-8-3/
 *
 * @param string $str
 *
 * @return string
 * @since 6.0
 */
function cerber_real_escape( $str ) {

	$str = (string) $str;

	if ( empty( $str ) ) {
		if ( $str === '0' ) {
			return '0';
		}

		return '';
	}

	if ( $db = cerber_get_db() ) {
		return mysqli_real_escape_string( $db->dbh, $str );
	}

	return  '';
}

function cerber_db_get_errors( $erase = false ) {
	global $cerber_db_errors;

	if ( ! isset( $cerber_db_errors ) ) {
		$cerber_db_errors = array();
	}

	if ( ! $erase ) {
		return $cerber_db_errors;
	}

	$ret = $cerber_db_errors;
	$cerber_db_errors = array();

	return $ret;
}

/**
 * Execute generic direct SQL query on the site DB
 *
 * The reason: https://make.wordpress.org/core/2017/10/31/changed-behaviour-of-esc_sql-in-wordpress-4-8-3/
 *
 * @param $query string An SQL query
 *
 * @return bool|mysqli_result|resource
 * @since 6.0
 */
function cerber_db_query( $query ) {
	global $cerber_db_errors;

	$db = cerber_get_db();

	if ( ! $db
	     || empty( $db->dbh )
	     || ! ( $db->dbh instanceof MySQLi ) ) {

		$cerber_db_errors[] = 'No active DB handler. Query: ' . $query;

		return false;
	}

	//$ret = mysqli_query( $db->dbh, $query, MYSQLI_USE_RESULT );
	if ( ! $ret = mysqli_query( $db->dbh, $query ) ) {
		$err = mysqli_error( $db->dbh ) . '. Query: ' . $query;
		$cerber_db_errors[] = $err;
	}

	return $ret;
}

function cerber_db_get_results( $query, $type = MYSQLI_ASSOC ) {

	if ( ! $result = cerber_db_query( $query ) ) {
		return array();
	}

	$ret = array();

	switch ( $type ) {
		case MYSQLI_ASSOC:
			while ( $row = mysqli_fetch_assoc( $result ) ) {
				$ret[] = $row;
			}
			//$ret = mysqli_fetch_all( $result, $type ); // Requires mysqlnd driver
			break;
		case MYSQL_FETCH_OBJECT:
			while ( $row = mysqli_fetch_object( $result ) ) {
				$ret[] = $row;
			}
			break;
		case MYSQL_FETCH_OBJECT_K:
			while ( $row = mysqli_fetch_object( $result ) ) {
				$vars = get_object_vars( $row );
				$key = array_shift( $vars );
				$ret[ $key ] = $row;
			}
			break;
		default:
			while ( $row = mysqli_fetch_row( $result ) ) {
				$ret[] = $row;
			}
	}

	mysqli_free_result( $result );

	return $ret;
}

function cerber_db_get_row( $query, $type = MYSQLI_ASSOC ) {

	if ( ! $result = cerber_db_query( $query ) ) {
		return false;
	}

	if ( $type == MYSQL_FETCH_OBJECT ) {
		$ret = $result->fetch_object();
	}
	else {
		$ret = $result->fetch_array( $type );
	}

	$result->free();

	return $ret;
}

function cerber_db_get_col( $query ) {

	if ( ! $result = cerber_db_query( $query ) ) {
		return array();
	}

	$ret = array();

	while ( $row = $result->fetch_row() ) {
		$ret[] = $row[0];
	}

	$result->free();

	return $ret;
}

function cerber_db_get_var( $query ) {

	if ( ! $result = cerber_db_query( $query ) ) {
		return false;
	}

	//$r = $result->fetch_row();
	$r = mysqli_fetch_row( $result );
	$result->free();

	if ( $r ) {
		return $r[0];
	}

	return false;
}

function cerber_db_insert( $table, $values ) {
	return cerber_db_query( 'INSERT INTO ' . $table . ' (' . implode( ',', array_keys( $values ) ) . ') VALUES (' . implode( ',', $values ) . ')' );
}

/**
 * @return bool|wpdb
 */
function cerber_get_db() {
	global $wpdb, $cerber_db_errors;
	static $db;

	if ( ! isset( $cerber_db_errors ) ) {
		$cerber_db_errors = array();
	}

	if ( empty( $db )
	     || empty( $db->dbh )
	     || ! ( $db->dbh instanceof MySQLi ) ) {

		if ( ! is_object( $wpdb )
		     || empty( $wpdb->dbh )
		     || ! ( $wpdb->dbh instanceof MySQLi ) ) {
			if ( ! $db = cerber_db_connect() ) {
				$cerber_db_errors[] = 'Unable to connect to the DB';

				return false;
			}
		}
		else {
			$db = $wpdb;
		}
	}

	return $db;
}

function cerber_get_db_prefix() {
	global $wpdb;
	static $prefix = null;
	if ( $prefix === null ) {
		$prefix = $wpdb->base_prefix;
	}

	return $prefix;
}

/**
 * Create a WP DB handler
 *
 * @return bool|wpdb
 */
function cerber_db_connect() {
	if ( ! defined( 'CRB_ABSPATH' ) ) {
		define( 'CRB_ABSPATH', cerber_dirname( __FILE__, 4 ) );
	}

	$db_class  = CRB_ABSPATH . '/' . WPINC . '/wp-db.php';

	$wp_config = CRB_ABSPATH . '/wp-config.php';
	if ( ! file_exists( $wp_config ) ) {
		$wp_config = dirname( CRB_ABSPATH ) . '/wp-config.php';
	}

	if ( file_exists( $db_class ) && $config = file_get_contents( $wp_config ) ) {
		$config = str_replace( '<?php', '', $config );
		$config = str_replace( '?>', '', $config );
		ob_start();
		@eval( $config ); // This eval is OK. Getting site DB connection parameters.
		ob_end_clean();
		if ( defined( 'DB_USER' ) && defined( 'DB_PASSWORD' ) && defined( 'DB_NAME' ) && defined( 'DB_HOST' ) ) {
			require_once( $db_class );

			return new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
		}
	}

	return false;
}

function crb_get_mysql_var( $var ) {
	static $cache;
	if ( ! isset( $cache[ $var ] ) ) {
		if ( $v = cerber_db_get_row( 'SHOW VARIABLES LIKE "' . $var . '"' ) ) {
			$cache[ $var ] = $v['Value'];
		}
		else {
			$cache[ $var ] = false;
		}
	}

	return $cache[ $var ];
}

/**
 * Retrieve a value from the key-value storage
 *
 * @param string $key
 * @param integer $id
 * @param bool $unserialize
 * @param bool $use_cache
 *
 * @return bool|array
 */
function cerber_get_set( $key, $id = null, $unserialize = true, $use_cache = null ) {
	if ( ! $key ) {
		return false;
	}

	$key = preg_replace( CRB_SANITIZE_KEY, '', $key );
	$cache_key = 'crb#' . $key . '#';

	$id = ( $id !== null ) ? absint( $id ) : 0;
	$cache_key .= $id;

	$ret = false;

	$use_cache = ( isset( $use_cache ) ) ? $use_cache : cerber_cache_is_enabled();

	if ( $use_cache ) {
		$cache_value = cerber_cache_get( $cache_key, null );
		if ( $cache_value !== null ) {
			return $cache_value;
		}
	}

	if ( $row = cerber_db_get_row( 'SELECT * FROM ' . cerber_get_db_prefix() . CERBER_SETS_TABLE . ' WHERE the_key = "' . $key . '" AND the_id = ' . $id ) ) {
		if ( $row['expires'] > 0 && $row['expires'] < time() ) {
			cerber_delete_set( $key, $id );
			if ( $use_cache ) {
				cerber_cache_delete( $cache_key );
			}
			return false;
		}
		if ( $unserialize ) {
			if ( ! empty( $row['the_value'] ) ) {
				$ret = unserialize( $row['the_value'] );
			}
			else {
				$ret = array();
			}
		}
		else {
			$ret = $row['the_value'];
		}
	}

	if ( $use_cache ) {
		cerber_cache_set( $cache_key, $ret );
	}

	return $ret;
}

/**
 * Update/insert value to the key-value storage
 *
 * @param string $key A unique key for the data set
 * @param $value
 * @param integer $id An additional numerical key
 * @param bool $serialize
 * @param integer $expires Unix timestamp (UTC) when this element will be deleted
 * @param bool $use_cache
 *
 * @return bool
 */
function cerber_update_set( $key, $value, $id = null, $serialize = true, $expires = null, $use_cache = null ) {

	if ( ! $key ) {
		return false;
	}

	$key = preg_replace( CRB_SANITIZE_KEY, '', $key );
	$cache_key = 'crb#' . $key . '#';

	$expires = ( $expires !== null ) ? absint( $expires ) : 0;

	$id = ( $id !== null ) ? absint( $id ) : 0;
	$cache_key .= $id;

	$use_cache = ( isset( $use_cache ) ) ? $use_cache : cerber_cache_is_enabled();

	if ( $use_cache ) {
		cerber_cache_set( $cache_key, $value, $expires - time() );
	}

	if ( $serialize && ! is_string( $value ) ) {
		$value = serialize( $value );
	}

	$value = cerber_real_escape( $value );

	if ( false !== cerber_get_set( $key, $id, false, false ) ) {
		$sql = 'UPDATE ' . cerber_get_db_prefix() . CERBER_SETS_TABLE . ' SET the_value = "' . $value . '", expires = ' . $expires . ' WHERE the_key = "' . $key . '" AND the_id = ' . $id;
	}
	else {
		$sql = 'INSERT INTO ' . cerber_get_db_prefix() . CERBER_SETS_TABLE . ' (the_key, the_id, the_value, expires) VALUES ("' . $key . '",' . $id . ',"' . $value . '",' . $expires . ')';
	}

	unset( $value );

	if ( cerber_db_query( $sql ) ) {
		return true;
	}
	else {
		return false;
	}
}

/**
 * Delete value from the storage
 *
 * @param string $key
 * @param integer $id
 *
 * @return bool
 */
function cerber_delete_set( $key, $id = null) {

	$key = preg_replace( CRB_SANITIZE_KEY, '', $key );
	$cache_key = 'crb#' . $key . '#';

	$id = ( $id !== null ) ? absint( $id ) : 0;
	$cache_key .= $id;

	cerber_cache_delete( $cache_key );

	if ( cerber_db_query( 'DELETE FROM ' . cerber_get_db_prefix() . CERBER_SETS_TABLE . ' WHERE the_key = "' . $key . '" AND the_id = ' . $id ) ) {
		return true;
	}

	return false;
}

/**
 * Clean up all expired sets. Usually by cron.
 * @param bool $all if true, deletes all sets that has expiration
 *
 * @return bool
 */
function cerber_delete_expired_set( $all = false ) {
	if ( ! $all ) {
		$where = 'expires > 0 AND expires < ' . time();
	}
	else {
		$where = 'expires > 0';
	}
	if ( cerber_db_query( 'DELETE FROM ' . cerber_get_db_prefix() . CERBER_SETS_TABLE . ' WHERE ' . $where ) ) {
		return true;
	}
	else {
		return false;
	}
}

/**
 * Remove comments from a given piece of code
 *
 * @param string $string
 *
 * @return mixed
 */
function cerber_remove_comments( $string = '' ) {
	return preg_replace( '/#.*/', '', preg_replace( '#//.*#', '', preg_replace( '#/\*(?:[^*]*(?:\*(?!/))*)*\*/#', '', $string ) ) );
}

/**
 * Set Cerber Groove to logged in user browser
 *
 * @param $expire
 */
function cerber_set_groove( $expire ) {
	if ( ! headers_sent() ) {
		cerber_set_cookie( 'cerber_groove', md5( cerber_get_groove() ), $expire + 1 );

		$groove_x = cerber_get_groove_x();
		cerber_set_cookie( 'cerber_groove_x_' . $groove_x[0], $groove_x[1], $expire + 1 );
	}
}

/*
	Get the special Cerber Sign for using with cookies
*/
function cerber_get_groove() {
	$groove = cerber_get_site_option( 'cerber-groove', false );

	if ( empty( $groove ) ) {
		//$groove = wp_generate_password( 16, false );
		$groove = substr( str_shuffle( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' ), 0, 16 );
		update_site_option( 'cerber-groove', $groove );
	}

	return $groove;
}

/*
	Check if the special Cerber Sign valid
*/
function cerber_check_groove( $hash = '' ) {
	if ( ! $hash ) {
		if ( ! $hash = cerber_get_cookie( 'cerber_groove' ) ) {
			return false;
		}
	}
	$groove = cerber_get_groove();
	if ( $hash == md5( $groove ) ) {
		return true;
	}

	return false;
}

/**
 * @since 7.0
 */
function cerber_check_groove_x() {
	$groove_x = cerber_get_groove_x();
	if ( cerber_get_cookie( 'cerber_groove_x_' . $groove_x[0] ) == $groove_x[1] ) {
		return true;
	}

	return false;
}

/*
	Get the special public Cerber Sign for using with cookies
*/
function cerber_get_groove_x( $regenerate = false ) {
	$groove_x = array();

	if ( ! $regenerate ) {
		$groove_x = cerber_get_site_option( 'cerber-groove-x' );
	}

	if ( $regenerate || empty( $groove_x ) ) {
		$groove_0 = substr( str_shuffle( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' ), 0, rand( 24, 32 ) );
		$groove_1 = substr( str_shuffle( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' ), 0, rand( 24, 32 ) );
		$groove_x = array( $groove_0, $groove_1 );
		update_site_option( 'cerber-groove-x', $groove_x );
		crb_update_cookie_dependent();
	}

	return $groove_x;
}

function cerber_get_cookie_path(){
	if ( defined( 'COOKIEPATH' ) ) {
		return COOKIEPATH;
	}

	return '/';
}

function cerber_set_cookie( $name, $value, $expire = 0, $path = "", $domain = "" ) {
	if ( cerber_is_wp_cron() ) {
		return;
	}
	if ( ! $path ) {
		$path = cerber_get_cookie_path();
	}

	$expire = absint( $expire );
	$expire = ( $expire > 43009401600 ) ? 43009401600 : $expire;

	setcookie( cerber_get_cookie_prefix() . $name, $value, $expire, $path, $domain, is_ssl(), false );
	// No rush here: PHP 7.3 only
	/*setcookie( cerber_get_cookie_prefix() . $name, $value, array(
		'expires ' => $expire,
		'path'     => $path,
		'domain'   => $domain,
		'secure'   => is_ssl(),
		'httponly' => false,
		'samesite' => 'Strict',
	) );*/
}

/**
 * @param $name
 * @param bool $default
 *
 * @return string|boolean value of the cookie, false if the cookie is not set
 */
function cerber_get_cookie( $name, $default = false ) {
	return crb_array_get( $_COOKIE, cerber_get_cookie_prefix() . $name, $default );
}

function cerber_get_cookie_prefix() {
	/*
	if ( defined( 'CERBER_COOKIE_PREFIX' )
	     && is_string( CERBER_COOKIE_PREFIX )
	     && preg_match( '/^\w+$/', CERBER_COOKIE_PREFIX ) ) {
		return CERBER_COOKIE_PREFIX;
	}*/
	if ( $p = crb_get_settings( 'cookiepref' ) ) {
		return $p;
	}

	return '';
}

function crb_update_cookie_dependent() {
	static $done = false;

	if ( $done ) {
		return;
	}

	//add_action( 'init', function () {
	register_shutdown_function( function () {
		cerber_htaccess_sync( 'main' ); // keep the .htaccess rule is up to date
	} );

	$done = true;
}

/**
 * Synchronize plugin settings with rules in the .htaccess file
 *
 * @param $file string
 * @param $settings array
 *
 * @return bool|string|WP_Error
 */
function cerber_htaccess_sync( $file, $settings = array() ) {

	if ( ! $settings ) {
		$settings = crb_get_settings();
	}

	if ( 'main' == $file ) {
		$rules    = array();
		if ( ! empty( $settings['adminphp'] ) && ( ! defined( 'CONCATENATE_SCRIPTS' ) || ! CONCATENATE_SCRIPTS ) ) {
			// https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2018-6389
			if ( ! apache_mod_loaded( 'mod_rewrite', true ) ) {
				cerber_add_issue( 'no_mod', 'The Apache mod_rewrite module is not enabled on your web server. Ask your server administrator for assistance.', 'adminphp' );
				return new WP_Error( 'no_mod', 'The Apache mod_rewrite module is not enabled on your web server. Ask your server administrator for assistance.' );
			}
			$groove_x = cerber_get_groove_x();
			$cookie   = cerber_get_cookie_prefix() . 'cerber_groove_x_' . $groove_x[0];
			$rules [] = '# Protection of admin scripts is enabled (CVE-2018-6389)';
			$rules [] = '<IfModule mod_rewrite.c>';
			$rules [] = 'RewriteEngine On';
			$rules [] = 'RewriteBase /';
			$rules [] = 'RewriteCond %{REQUEST_URI} ^(.*)wp-admin/+load-scripts\.php$ [OR,NC]'; // @updated 8.1
			$rules [] = 'RewriteCond %{REQUEST_URI} ^(.*)wp-admin/+load-styles\.php$ [NC]'; // @updated 8.1
			$rules [] = 'RewriteCond %{HTTP_COOKIE} !' . $cookie . '=' . $groove_x[1];
			$rules [] = 'RewriteRule (.*) - [R=403,L]';
			$rules [] = '</IfModule>';
		}

		return cerber_update_htaccess( $file, $rules );
	}

	if ( 'media' == $file ) {
		/*if ( ! crb_is_php_mod() ) {
			return 'ERROR: The Apache PHP module mod_php is not active.';
		}*/
		$rules = array();
		if ( ! empty( $settings['phpnoupl'] ) ) {

			$rules [] = '<Files *>';
			$rules [] = 'SetHandler none';
			$rules [] = 'SetHandler default-handler';
			$rules [] = 'Options -ExecCGI';
			$rules [] = 'RemoveHandler .cgi .php .php3 .php4 .php5 .php7 .phtml .pl .py .pyc .pyo';
			$rules [] = '</Files>';

			$rules [] = '<IfModule mod_php7.c>';
			$rules [] = 'php_flag engine off';
			$rules [] = '</IfModule>';
			$rules [] = '<IfModule mod_php5.c>';
			$rules [] = 'php_flag engine off';
			$rules [] = '</IfModule>';
		}

		return cerber_update_htaccess( $file, $rules );
	}

	return false;
}

/**
 * Remove Cerber rules from the .htaccess file
 *
 */
function cerber_htaccess_clean_up() {
	cerber_update_htaccess( 'main', array() );
	cerber_update_htaccess( 'media', array() );
}

/**
 * Update the .htaccess file
 *
 * @param $file
 * @param array $rules A set of rules (array of strings) for the section. If empty, the section will be cleaned.
 *
 * @return bool|string|WP_Error  True on success, string with error message on failure
 */
function cerber_update_htaccess( $file, $rules = array() ) {
	if ( $file == 'main' ) {
		$htaccess = cerber_get_htaccess_file();
		$marker = CERBER_MARKER1;
	}
	elseif ( $file == 'media' ) {
		$htaccess = cerber_get_upload_dir() . '/.htaccess';
		$marker = CERBER_MARKER2;
	}
	else {
		return '???';
	}

	if ( ! is_file( $htaccess ) ) {
		if ( ! touch( $htaccess ) ) {
			return new WP_Error( 'htaccess-io', 'ERROR: Unable to create the file ' . $htaccess);
		}
	}
	elseif ( ! is_writable( $htaccess ) ) {
		return new WP_Error( 'htaccess-io', 'ERROR: Unable to get access to the file ' . $htaccess);
	}

	$result = crb_insert_with_markers( $htaccess, $marker, $rules );

	if ( $result || $result === 0 ) {
		$result = 'The ' . $htaccess . ' file has been updated';
	}
	else {
		$result = new WP_Error( 'htaccess-io', 'ERROR: Unable to modify the file ' . $htaccess);
	}

	return $result;
}

/**
 * Return .htaccess filename with full path
 *
 * @return bool|string full filename if the file can be written, false otherwise
 */
function cerber_get_htaccess_file() {
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	$home_path = get_home_path();
	return $home_path . '.htaccess';
}

/**
 * Check if the remote domain match mask
 *
 * @param $domain_mask array|string Mask(s) to check remote domain against
 *
 * @return bool True if hostname match at least one domain from the list
 */
function cerber_check_remote_domain( $domain_mask ) {

	$hostname = @gethostbyaddr( cerber_get_remote_ip() );

	if ( ! $hostname || filter_var( $hostname, FILTER_VALIDATE_IP ) ) {
		return false;
	}

	if ( ! is_array( $domain_mask ) ) {
		$domain_mask = array( $domain_mask );
	}

	foreach ( $domain_mask as $mask ) {

		if ( substr_count( $mask, '.' ) != substr_count( $hostname, '.' ) ) {
			continue;
		}

		$parts = array_reverse( explode( '.', $hostname ) );

		$ok = true;

		foreach ( array_reverse( explode( '.', $mask ) ) as $i => $item ) {
			if ( $item != '*' && $item != $parts[ $i ] ) {
				$ok = false;
				break;
			}
		}

		if ( $ok == true ) {
			return true;
		}

	}

	return false;
}

/**
 * Prepare files (install/deinstall) for different boot modes
 *
 * @param $mode int A plugin boot mode
 * @param $old_mode int
 *
 * @return bool|WP_Error
 * @since 6.3
 */
function cerber_set_boot_mode( $mode = null, $old_mode = null ) {
	if ( $mode === null ) {
		$mode = crb_get_settings( 'boot-mode' );
	}
	$source = dirname( cerber_plugin_file() ) . '/modules/aaa-wp-cerber.php';
	$target = WPMU_PLUGIN_DIR . '/aaa-wp-cerber.php';
	if ( $mode == 1 ) {
		if ( file_exists( $target ) ) {
			if ( sha1_file( $source, true ) == sha1_file( $target, true ) ) {
				return true;
			}
		}
		if ( ! is_dir( WPMU_PLUGIN_DIR ) ) {
			if ( ! mkdir( WPMU_PLUGIN_DIR, 0755, true ) ) {
				// TODO: try to set permissions for the parent folder
				return new WP_Error( 'cerber-boot', __( 'Unable to create the directory', 'wp-cerber' ) . ' ' . WPMU_PLUGIN_DIR );
			}
		}
		if ( ! copy( $source, $target ) ) {
			if ( ! wp_is_writable( WPMU_PLUGIN_DIR ) ) {
				return new WP_Error( 'cerber-boot', __( 'Destination folder access denied', 'wp-cerber' ) . ' ' . WPMU_PLUGIN_DIR );
			}
			elseif ( ! file_exists( $source ) ) {
				return new WP_Error( 'cerber-boot', __( 'File not found', 'wp-cerber' ) . ' ' . $source );
			}

			return new WP_Error( 'cerber-boot', __( 'Unable to copy the file', 'wp-cerber' ) . ' ' . $source . ' to the folder ' . WPMU_PLUGIN_DIR );
		}
	}
	else {
		if ( file_exists( $target ) ) {
			if ( ! unlink( $target ) ) {
				return new WP_Error( 'cerber-boot', __( 'Unable to delete the file', 'wp-cerber' ) . ' ' . $target );
			}
		}

		return true;
	}

	return true;
}

/**
 * How the plugin was loaded (initialized)
 *
 * @return int
 * @since 6.3
 */
function cerber_get_mode() {
	if ( function_exists( 'cerber_mode' ) && defined( 'CERBER_MODE' ) ) {
		return cerber_mode();
	}

	return 0;
}

function cerber_is_permalink_enabled() {
	static $ret;

	if ( isset( $ret ) ) {
		return $ret;
	}

	$ret = ( get_option( 'permalink_structure' ) ) ? true : false;

	return $ret;
}

/**
 * Given the path of a file or directory, this function
 * will return the parent directory's path that is given levels up
 *
 * @param string $path
 * @param integer $levels
 *
 * @return string Parent directory's path
 */
function cerber_dirname( $path, $levels = 1 ) {

	if ( PHP_VERSION_ID >= 70000 || $levels == 1 ) {
		return dirname( $path, $levels );
	}

	$ret = '.';

	$path = explode( DIRECTORY_SEPARATOR, str_replace( array( '/', '\\' ), DIRECTORY_SEPARATOR, $path ) );
	if ( 0 < ( count( $path ) - $levels ) ) {
		$path = array_slice( $path, 0, count( $path ) - $levels );
		$ret  = implode( DIRECTORY_SEPARATOR, $path );
	}

	return $ret;

}

// Return an unmodified $wp_version variable
function cerber_get_wp_version() {
	static $v;
	if ( ! $v ) {
		global $wp_version;
		include_once( ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'version.php' );
		$v = $wp_version;
	}

	return $v;
}

function crb_get_themes() {

	static $theme_headers = array(
		'Name'        => 'Theme Name',
		'ThemeURI'    => 'Theme URI',
		'Description' => 'Description',
		'Author'      => 'Author',
		'AuthorURI'   => 'Author URI',
		'Version'     => 'Version',
		'Template'    => 'Template',
		'Status'      => 'Status',
		'Tags'        => 'Tags',
		'TextDomain'  => 'Text Domain',
		'DomainPath'  => 'Domain Path',
	);

	$themes = array();

	if ( $list = search_theme_directories() ) {
		foreach ( $list as $key => $info ) {
			$css = $info['theme_root'] . '/' . $info['theme_file'];
			if ( is_readable( $css ) ) {
				$themes[ $key ]               = get_file_data( $info['theme_root'] . '/' . $info['theme_file'], $theme_headers, 'theme' );
				$themes[ $key ]['theme_root'] = $info['theme_root'];
				$themes[ $key ]['theme_file'] = $info['theme_file'];
			}
		}
	}

	return $themes;
}

function cerber_is_base64_encoded( $val ) {
	$val = trim( $val );
	if ( empty( $val ) || is_numeric( $val ) || strlen( $val ) < 8 || preg_match( '/[^A-Z0-9\+\/=]/i', $val ) ) {
		return false;
	}
	if ( $val = @base64_decode( $val ) ) {
		if ( ! preg_match( '/[\x00-\x08\x0B-\x0C\x0E-\x1F]/', $val ) ) { // ASCII control characters must not be
			if ( preg_match( '/[A-Z]/i', $val ) ) { // Latin chars must be
				return $val;
			}
		}
	}


	return false;
}

function crb_is_alphanumeric( $str ) {
	return ! preg_match( '/[^\w\-]/', $str );
}

/**
 * @param array $arr
 * @param array $fields
 *
 * @return bool
 */
function crb_arrays_similar( &$arr, $fields ) {
	if ( crb_array_diff_keys( $arr, $fields ) ) {
		return false;
	}

	foreach ( $fields as $field => $pattern ) {
		if ( is_callable( $pattern ) ) {
			if ( ! call_user_func( $pattern, $arr[ $field ] ) ) {
				return false;
			}
		}
		else {
			if ( ! preg_match( $pattern, $arr[ $field ] ) ) {
				return false;
			}
		}
	}

	return true;
}

function cerber_get_html_label( $iid ) {
	$css['scan-ilabel'] = '
	color: #fff;
    margin-left: 6px;
    display: inline-block;
    line-height: 1em;
    padding: 3px 5px;
    font-size: 92%;
	';

	if ( $iid == 1 ) {
		$c = '#33be84;';
	}
	else {
		$c = '#dc2f34;';
	}

	return '<span style="background-color:' . $c . $css['scan-ilabel'] . '">' . cerber_get_issue_label( $iid ) . '</span>';

}

function crb_getallheaders() {

	if ( function_exists( 'getallheaders' ) ) {
		return getallheaders();
	}

	// @since v. 7.7 for PHP-FPM

	$headers = array();
	foreach ( $_SERVER as $name => $value ) {
		if ( substr( $name, 0, 5 ) == 'HTTP_' ) {
			$headers[ str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $name, 5 ) ) ) ) ) ] = $value;
		}
	}

	return $headers;
}

function cerber_error_log( $msg, $source = '' ) {
	//if ( crb_get_settings( 'log_errors' ) ) {
		cerber_diag_log( $msg, $source, true );
	//}
}

/**
 * Write message to the diagnostic log
 *
 * @param string|array $msg
 * @param string $source
 * @param bool $error
 *
 * @return bool|int
 */
function cerber_diag_log( $msg, $source = '', $error = false ) {
	if ( ! $msg || ! $log = @fopen( cerber_get_diag_log(), 'a' ) ) {
		return false;
	}
	if ( $source ) {
		$source = '[' . $source . ']';
	}
	if ( $error ) {
		$source .= ' ERROR: ';
	}
	if ( ! is_array( $msg ) ) {
		$msg = array( $msg );
	}

	foreach ( $msg as $line ) {
		//$ret = @fwrite( $log, '[' .cerber_get_remote_ip(). '][' . cerber_date( time() ) . ']' . $source . ' ' . $line . PHP_EOL );
		$ret = @fwrite( $log, '[' . cerber_date( time(), false ) . ']' . $source . ' ' . $line . PHP_EOL );
	}

	@fclose( $log );

	return $ret;
}

function cerber_get_diag_log() {

	//$dir = ( defined( 'CERBER_DIAG_DIR' ) && is_dir( CERBER_DIAG_DIR ) ) ? CERBER_DIAG_DIR . '/' : cerber_get_the_folder();
	if ( defined( 'CERBER_DIAG_DIR' ) && is_dir( CERBER_DIAG_DIR ) ) {
		$dir = CERBER_DIAG_DIR;
	}
	else {
		if ( ! $dir = cerber_get_the_folder() ) {
			return false;
		}
	}

	return rtrim( $dir, '/' ) . '/cerber-debug.log';
}

function cerber_truncate_log( $bytes = 10000000 ) {
	$file = cerber_get_diag_log();
	if ( ! is_file( $file ) || filesize( $file ) <= $bytes ) {
		return;
	}
	if ( $bytes == 0 ) {
		$log = @fopen( $file, 'w' );
		@fclose( $log );
		return;
	}
	if ( $text = file_get_contents( $file ) ) {
		$text = substr( $text, 0 - $bytes );
		if ( ! $log = @fopen( $file, 'w' ) ) {
			return;
		}
		@fwrite( $log, $text );
		@fclose( $log );
	}
}

function crb_get_bloginfo( $what ) {
	static $info = array();
	if ( ! isset( $info[ $what ] ) ) {
		$info[ $what ] = get_bloginfo( $what );
	}

	return $info[ $what ];
}

function crb_is_php_mod() {
	require_once( ABSPATH . 'wp-admin/includes/misc.php' );
	if ( apache_mod_loaded( 'mod_php7' ) ) {
		return true;
	}
	if ( apache_mod_loaded( 'mod_php5' ) ) {
		return true;
	}

	return false;
}

/**
 * PHP implementation of fromCharCode
 *
 * @param $str
 *
 * @return string
 */
function cerber_fromcharcode( $str ) {
	$vals = explode( ',', $str );
	$vals = array_map( function ( $v ) {
		$v = trim( $v );
		if ( $v[0] == '0' ) {
			$v = ( $v[1] == 'x' || $v[1] == 'X' ) ? hexdec( $v ) : octdec( $v );
		}
		else {
			$v = intval( $v );
		}

		return '&#' . $v . ';';
	}, $vals );
	$ret  = mb_convert_encoding( implode( '', $vals ), 'UTF-8', 'HTML-ENTITIES' );

	return $ret;
}

/**
 * @param $dir string Directory to empty with a trailing directory separator
 *
 * @return int|WP_Error
 */
function cerber_empty_dir( $dir ) {
	//$trd = rtrim( $dir, '/\\' );
	if ( ! @is_dir( $dir )
	     || 0 === strpos( $dir, ABSPATH ) ) { // Workaround for a non-legitimate use of this function
		return new WP_Error( 'no-dir', 'This directory cannot be emptied' );
	}

	$files = @scandir( $dir );
	if ( ! is_array( $files ) || empty( $files ) ) {
		return true;
	}

	$fs = cerber_init_wp_filesystem();
	if ( is_wp_error( $fs ) ) {
		return $fs;
	}

	$ret = true;

	foreach ( $files as $file ) {
		$full = $dir . $file;
		if ( @is_file( $full ) ) {
			if ( ! @unlink( $full ) ) {
				$ret = false;
			}
		}
		elseif ( ! in_array( $file, array( '..', '.' ) ) && is_dir( $full ) ) {
			if ( ! $fs->rmdir( $full, true ) ) {
				$ret = false;
			}
		}
	}

	if ( ! $ret ) {
		return new WP_Error( 'file-deletion-error', 'Some files or subdirectories in this directory cannot be deleted: ' . $dir );
	}

	return $ret;
}

/**
 * Tries to raise PHP limits
 *
 */
function crb_raise_limits( $mem = null ) {

	@ini_set( 'max_execution_time', 180 );

	if ( function_exists( 'set_time_limit' )
	     && false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) ) {
		@set_time_limit( 0 );
	}

	if ( $mem ) {
		@ini_set( 'memory_limit', $mem );
	}
}

function cerber_mask_email( $email ) {
	list( $box, $host ) = explode( '@', $email );
	$box  = str_pad( $box[0], strlen( $box ), '*' );
	$host = str_pad( substr( $host, strrpos( $host, '.' ) ), strlen( $host ), '*', STR_PAD_LEFT );

	return str_replace( '*', '&#8727;', $box . '@' . $host );
}

/**
 * A modified clone of insert_with_markers() from wp-admin/includes/misc.php
 * Removed switch_to_locale() and related stuff that were introduced in WP 5.3. and cause problem if calling ite before 'init' hook.
 *
 * Inserts an array of strings into a file (.htaccess ), placing it between
 * BEGIN and END markers.
 *
 * Replaces existing marked info. Retains surrounding
 * data. Creates file if none exists.
 *
 * @since 8.5.1
 *
 * @param string       $filename  Filename to alter.
 * @param string       $marker    The marker to alter.
 * @param array|string $insertion The new content to insert.
 * @return bool True on write success, false on failure.
 */
function crb_insert_with_markers( $filename, $marker, $insertion ) {
	if ( ! file_exists( $filename ) ) {
		if ( ! is_writable( dirname( $filename ) ) ) {
			return false;
		}
		if ( ! touch( $filename ) ) {
			return false;
		}
	}
	elseif ( ! is_writeable( $filename ) ) {
		return false;
	}

	if ( ! is_array( $insertion ) ) {
		$insertion = explode( "\n", $insertion );
	}

	$start_marker = "# BEGIN {$marker}";
	$end_marker   = "# END {$marker}";

	$fp = fopen( $filename, 'r+' );
	if ( ! $fp ) {
		return false;
	}

	// Attempt to get a lock. If the filesystem supports locking, this will block until the lock is acquired.
	flock( $fp, LOCK_EX );

	$lines = array();
	while ( ! feof( $fp ) ) {
		$lines[] = rtrim( fgets( $fp ), "\r\n" );
	}

	// Split out the existing file into the preceding lines, and those that appear after the marker
	$pre_lines        = array();
	$post_lines       = array();
	$existing_lines   = array();
	$found_marker     = false;
	$found_end_marker = false;
	foreach ( $lines as $line ) {
		if ( ! $found_marker && false !== strpos( $line, $start_marker ) ) {
			$found_marker = true;
			continue;
		}
		elseif ( ! $found_end_marker && false !== strpos( $line, $end_marker ) ) {
			$found_end_marker = true;
			continue;
		}
		if ( ! $found_marker ) {
			$pre_lines[] = $line;
		}
		elseif ( $found_marker && $found_end_marker ) {
			$post_lines[] = $line;
		}
		else {
			$existing_lines[] = $line;
		}
	}

	// Check to see if there was a change
	if ( $existing_lines === $insertion ) {
		flock( $fp, LOCK_UN );
		fclose( $fp );

		return true;
	}

	// Generate the new file data
	$new_file_data = implode(
		"\n",
		array_merge(
			$pre_lines,
			array( $start_marker ),
			$insertion,
			array( $end_marker ),
			$post_lines
		)
	);

	// Write to the start of the file, and truncate it to that length
	fseek( $fp, 0 );
	$bytes = fwrite( $fp, $new_file_data );
	if ( $bytes ) {
		ftruncate( $fp, ftell( $fp ) );
	}
	fflush( $fp );
	flock( $fp, LOCK_UN );
	fclose( $fp );

	return (bool) $bytes;
}

/**
 * @return WP_Error|WP_Filesystem_Direct
 */
function cerber_init_wp_filesystem() {
	global $wp_filesystem;

	if ( $wp_filesystem instanceof WP_Filesystem_Direct ) { // @since 8.1.5
		return $wp_filesystem;
	}

	require_once( ABSPATH . 'wp-admin/includes/file.php' );

	add_filter( 'filesystem_method', '__ret_direct' );
	if ( ! WP_Filesystem() ) {
		return new WP_Error( 'cerber-file', 'Unable to init WP_Filesystem' );
	}
	remove_filter( 'filesystem_method', '__ret_direct' );

	return $wp_filesystem;
}

function __ret_direct() {
	return 'direct';
}

function crb_file_headers( $fname ) {
	$fname = rawurlencode( $fname ); // encode non-ASCII symbols
	@ob_clean(); // This trick is crucial for some servers/environments (e.g. some IIS)
	header( "Content-Type: application/octet-stream" );
	header( "Content-Disposition: attachment; filename*=UTF-8''{$fname}" );
}

// The key-value cache

final class CRB_Cache {
	private static $cache = array();
	private static $stat = array();
	private static $wp_cache_group = 'wp_cerber';
	private static $wp_key_list = 'wp_cerber_list';

	static function set( $key, $value, $expire = 0 ) {
		$exp = 0;

		if ( $expire > 0 ) {
			$exp = time() + (int) $expire;
			if ( $exp < time() ) {
				return false;
			}
		}

		$element = array( $value, $exp );
		self::$cache[ $key ] = $element;

		if ( self::checker() ) {
			wp_cache_set( $key, $element, self::$wp_cache_group );

			$entries = wp_cache_get( self::$wp_key_list, self::$wp_key_list );
			if ( ! $entries ) {
				$entries = array();
			}
			$entries[ $key ] = $expire;
			wp_cache_set( self::$wp_key_list, $entries, self::$wp_key_list );
		}

		if ( ! isset( self::$stat[ $key ] ) ) {
			self::$stat[ $key ] = array( 0, 0 );
		}

		self::$stat[ $key ][0] ++;

		return true;
	}

	static function get( $key, $default = null ) {

		$element = crb_array_get( self::$cache, $key );

		if ( ! is_array( $element ) ) {
			if ( self::checker() ) {
				$element = wp_cache_get( $key, self::$wp_cache_group );
			}
		}

		if ( ! is_array( $element ) ) {
			return $default;
		}

		if ( ! empty( $element[1] ) && $element[1] < time() ) {
			self::delete( $key );

			return $default;
		}

		if ( ! isset( self::$stat[ $key ] ) ) {
			self::$stat[ $key ] = array( 0, 0 );
		}

		self::$stat[ $key ][1] ++;

		return $element[0];
	}

	static function delete( $key ) {
		if ( isset( self::$cache[ $key ] ) ) {
			unset( self::$cache[ $key ] );
		}
		if ( self::checker() ) {
			wp_cache_delete( $key, self::$wp_cache_group );
		}
	}

	static function reset() {
		self::$cache = array();

		if ( $entries = wp_cache_get( self::$wp_key_list, self::$wp_key_list ) ) {
			foreach ( $entries as $entry => $exp ) {
				wp_cache_delete( $entry, self::$wp_cache_group );
			}

			wp_cache_delete( self::$wp_key_list, self::$wp_key_list );
		}
	}

	static function get_stat( $recheck = false ) {
		$entries = wp_cache_get( self::$wp_key_list, self::$wp_key_list );

		if ( $recheck && $entries ) { // Make sure that our list of existing key doesn't contain wrong entries
			foreach ( $entries as $key => $exp ) {
				if ( ! $element = wp_cache_get( $key, self::$wp_cache_group ) ) {
					unset( $entries[ $key ] );
				}
			}

			wp_cache_set( self::$wp_key_list, $entries, self::$wp_key_list );
		}

		return array( self::$stat, $entries );
	}

	static function checker() {

		$sid = get_wp_cerber()->getRequestID();
		$check = wp_cache_get( '__checker__', self::$wp_cache_group );

		if ( ! $check || ! isset( $check['t'] ) || ! isset( $check['s'] ) ) {
			wp_cache_set( '__checker__', array(
				't' => time(),
				's' => $sid
			), self::$wp_cache_group );

			return 0;
		}

		if ( $check['s'] == $sid ) {
			return 0;
		}

		return $check['t'];
	}
}

/**
 * @param $key string
 * @param $value mixed
 * @param $expire integer Element will expire in X seconds, 0 = never expires
 *
 * @return bool
 */
function cerber_cache_set( $key, $value, $expire = 0 ) {
	return CRB_Cache::set( $key, $value, $expire );
}

/**
 * @param $key string
 * @param $default mixed
 *
 * @return mixed|null
 */
function cerber_cache_get( $key, $default = null ) {
	return CRB_Cache::get( $key, $default );
}

function cerber_cache_delete( $key ) {
	CRB_Cache::delete( $key );
}

function cerber_cache_enable() {
	global $cerber_use_cache;
	$cerber_use_cache = true;
}

function cerber_cache_disable() {
	global $cerber_use_cache;
	$cerber_use_cache = false;
}

function cerber_cache_is_enabled() {
	global $cerber_use_cache;

	return ! empty( $cerber_use_cache );
}
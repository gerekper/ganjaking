<?php
/*
	Cerber Laboratory (cerberlab.net) specific routines.

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
if ( ! defined( 'WPINC' ) ) { exit; }

define( 'LAB_NODE_MAX', 9 ); // Maximum node ID
define( 'LAB_DELAY_MAX', 2000 ); // milliseconds, reasonable maximum of processing time while connecting to a node
define( 'LAB_RECHECK', 15 * 60 ); // seconds, allowed interval for rechecking nodes
define( 'LAB_INTERVAL', 180 ); // seconds, push interval
define( 'LAB_DNS_TTL', 3 * 24 * 3600 ); // seconds, interval of updating DNS cache for nodes IPs
define( 'LAB_IP_OK', 100 ); // an ideal, the best possible reputation
define( 'LAB_KEY_LENGTH', 32 );
define( 'LAB_LICENSE_GRACE', 3600 * 24 * 3 );

/**
 * Is IP blocked globally in Cerber Lab?
 *
 * @param string $ip IP address to check against global black list
 * @param bool $ask If true, send request to Cerber Lab if no IP in the local cache found
 *
 * @return bool true if IP is blocked
 */
function lab_is_blocked( $ip = '', $ask = true ) {

	if ( ! $ip ) {
		$ip = cerber_get_remote_ip();
	}

	if ( is_ip_private( $ip ) ) {
		return false;
	}

	$tag = cerber_acl_check( $ip );
	if ( $tag == 'W' ) {
		return false;
	}
	if ( $tag == 'B' ) {
		return true;
	}

	if ( !lab_lab() ) {
		return false;
	}

	$rep = lab_get_reputation( $ip, $ask );

	if ( is_numeric( $rep ) && $rep < LAB_IP_OK ) {
		return true;
	}

	return false;
}

/**
 * Return reputation for a given IP
 *
 * @param string $ip
 * @param bool $ask If true, send request to Cerber Lab (if no IP in the local cache found)
 *
 * @return int  Reputation for a given IP
 */
function lab_get_reputation( $ip, $ask = true ) {

	if ( ! $ip = filter_var( $ip, FILTER_VALIDATE_IP ) ) {
		return LAB_IP_OK;
	}
	if ( is_ip_private( $ip ) ) {
		return LAB_IP_OK;
	}

	$reputation = cerber_db_get_var( 'SELECT reputation FROM ' . CERBER_LAB_IP_TABLE . ' WHERE ip = "' . $ip . '"' );
	if ( is_numeric( $reputation ) ) {
		return $reputation;
	}
	elseif (!$ask){
		return LAB_IP_OK;
	}

	$ip_id    = cerber_get_id_ip( $ip );
	$lab_data = lab_api_send_request( array( 'ask_cerberlab' => array( $ip_id => $ip ) ) );

	if ( ! $lab_data || empty( $lab_data['response']['payload'][ $ip_id ]['reputation'] ) ) {
		$reputation = LAB_IP_OK;
		$ip_data = array();
		$ip_data['reputation']['value'] = $reputation;
		$ip_data['reputation']['ttl'] = 600;
	}
	else {
		$reputation = absint( $lab_data['response']['payload'][ $ip_id ]['reputation']['value'] );
		$ip_data = $lab_data['response']['payload'][ $ip_id ];
	}

	lab_reputation_update($ip , $ip_data);

	if ( ! empty( $lab_data['response']['payload'][ $ip_id ]['network']['geo'] ) ) {
		lab_geo_update($ip, $lab_data['response']['payload'][ $ip_id ]);
	}

	return $reputation;
}

function lab_reputation_update( $ip, $ip_data ) {

	if ( empty( $ip_data['reputation'] ) ) {
		return;
	}

	if ( ! $ip = filter_var( $ip, FILTER_VALIDATE_IP ) ) {
		return;
	}

	$reputation = absint( $ip_data['reputation']['value'] );
	$expires    = time() + absint( $ip_data['reputation']['ttl'] );

	if ( cerber_db_get_var( 'SELECT COUNT(ip) FROM ' . CERBER_LAB_IP_TABLE . ' WHERE ip = "' . $ip . '"' ) ) {
		cerber_db_query( 'UPDATE ' . CERBER_LAB_IP_TABLE . ' SET reputation = ' . $reputation . ', expires = ' . $expires . ' WHERE ip = "' . $ip . '"' );
	}
	else {
		cerber_db_query( 'INSERT INTO ' . CERBER_LAB_IP_TABLE . ' (ip, reputation, expires) VALUES ("' . $ip . '",' . $reputation . ',' . $expires . ')' );
	}
}

/**
 * Send request to a Cerber Lab node.
 *
 * @param array $workload Workload
 * @param string|int Return this element from the payload if it exists
 *
 * @return array|bool
 */
function lab_api_send_request( $workload = array(), $payload_key = null ) {
	global $node_delay;

	$push = lab_get_push();

	if ( ! $workload && ! $push ) {
		return false;
	}

	$key = lab_get_key();

	if ( $workload && empty( $key[2] ) && ! $push ) {
		return false;
	}

	$request = array(
		'key'      => $key,
		'workload' => $workload,
		'push'     => $push,
		'lang'     => crb_get_bloginfo( 'language' ),
		'multi'    => is_multisite(),
		'version'  => CERBER_VER,
		'PHP'      => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
		'sapi'     => PHP_SAPI,
		'time'     => time(),
	);

	$ret = lab_send_request( $request );

	// If something goes wrong, take the next closest node
	if ( ! $ret ) {
		$ret = lab_send_request( $request );
	}
	elseif ( ( $node_delay * 1000 ) > LAB_DELAY_MAX ) {
		lab_check_nodes(); // Recheck nodes for further requests
	}

	if ( $ret ) {
		lab_trunc_push();
	}

	if ( $payload_key ) {
		return crb_array_get( $ret, array( 'response', 'payload', $payload_key ) );
	}

	return $ret;
}

/**
 * Send an HTTP request to a node.
 *
 * @param $request array
 * @param null $node_id Node ID if not set, will use the last closest and active node
 * @param string $scheme http|https
 *
 * @return array|bool The response of a node on the success request otherwise false on any error
 */
function lab_send_request( $request, $node_id = null, $scheme = null ) {
	global $node_delay;

	$node = lab_get_node( $node_id );
	if ( ! $scheme ) {
		if ( crb_get_settings( 'cerberproto' ) ) {
			$scheme = 'https';
		}
		else {
			$scheme = 'http';
		}
	}
	elseif ( $scheme != 'http' || $scheme != 'https' ) {
		$scheme = 'https';
	}

	$body = array();
	$body['container'] = $request;
	$body['nodes'] = lab_get_nodes();

	$request_body = json_encode( $body );
	if ( JSON_ERROR_NONE != json_last_error() ) {
		//'Unable to encode request: '.json_last_error_msg(), array(__FUNCTION__,__LINE__));
		return false;
	}

	$headers = array(
		'Host:' . $node[2],
		'Content-Type: application/json',
		'Accept: application/json',
		'Cerber: ' . CERBER_VER,
		/*	'Authorization: Bearer ' . $fields['key']*/
	);

	$curl = @curl_init(); // @since 4.32
	if ( ! $curl ) {
		return false;
	}

	curl_setopt_array( $curl, array(
		CURLOPT_URL               => $scheme . '://' . $node[2] . '/engine/v1/',
		CURLOPT_POST              => true,
		CURLOPT_HTTPHEADER        => $headers,
		CURLOPT_POSTFIELDS        => $request_body,
		CURLOPT_RETURNTRANSFER    => true,
		CURLOPT_USERAGENT         => 'Cerber Security Plugin ' . CERBER_VER,
		CURLOPT_CONNECTTIMEOUT    => 2,
		CURLOPT_TIMEOUT           => 4, // including CURLOPT_CONNECTTIMEOUT
		CURLOPT_DNS_CACHE_TIMEOUT => 4 * 3600,
		CURLOPT_SSL_VERIFYHOST    => 2,
		CURLOPT_SSL_VERIFYPEER    => true,
		CURLOPT_CAINFO            => ABSPATH . WPINC . '/certificates/ca-bundle.crt',
	) );

	$start = microtime( true );
	$data = @curl_exec( $curl );
	$stop = microtime( true );

	$node_delay = $stop - $start;

	if ( $data ) {
		$response = lab_parse_response( $data );
	}
	else {
		$response['status'] = 0;
		$code = intval( curl_getinfo( $curl, CURLINFO_HTTP_CODE ) );
		$response['error'] = 'No connection (' . $code . ')';
		//if (!$data) // curl_error($curl) . curl_errno($curl) );
	}

	curl_close( $curl );

	lab_update_node_last( $node[0], array(
		$node_delay,
		$response['status'],
		$response['error'],
		time(),
		$scheme,
		$node[1]
	) );

	if ( $response['error'] ) {
		return false;
	}

	return $response;
}

/**
 * Parse node response and detect possible errors
 *
 * @param $response
 *
 * @return array
 */
function lab_parse_response( $response ) {
	$ret = array( 'status' => 1, 'error' => false );

	if ( ! empty( $response ) ) {
		$ret = json_decode( $response, true );
		if ( JSON_ERROR_NONE != json_last_error() ) {
			$ret['status'] = 0;
			$ret['error'] = 'JSON ERROR: ' . json_last_error_msg();
		}
		// Is everything is OK?
		if ( empty( $ret['key'] ) || ! empty( $ret['error'] ) ) {
			$ret['status'] = 0; // Not OK
		}
	}
	else {
		$ret['status'] = 0;
		$ret['error'] = 'No node answer';
	}

	if ( ! isset( $ret['error'] ) ) {
		$ret['error'] = false;
	}

	return $ret;
}

/**
 * Return "the best" (closest) node if $node_id is not specified
 *
 * @param $node_id integer node ID
 *
 * @return array first element is ID of closest node, second is an IP address
 */
function lab_get_node( $node_id = null ) {

	$node_id = absint( $node_id );

	if ( $node_id ) {
		$best_id = $node_id;
	}
	else {
		$best_id = null;
	}

	$nodes = lab_get_nodes();

	if ( ! $best_id ) {
		if ( $nodes && ! empty( $nodes['best'] ) ) {
			$best_id = $nodes['best'];
			if ( ! $nodes['nodes'][ $best_id ]['last'][1] ) { // this node was not active at the last request
				unset( $nodes['nodes'][ $best_id ] );
				$best_id = lab_best_node( $nodes['nodes'] );
			}
		}
	}

	if ( ! $best_id || $best_id > LAB_NODE_MAX ) {
		$best_id = rand( 1, LAB_NODE_MAX );
	}

	$name = 'node' . $best_id . '.cerberlab.net';

	$host = null;
	if ( ! empty( $nodes['nodes'][ $best_id ]['last'] ) ) {
		$node = $nodes['nodes'][ $best_id ]['last'];
		if ( $node[5] && ( time() - $node[3] ) < LAB_DNS_TTL ) {
			$host = $node[5];
		}
	}
	if ( ! $host ) {
		$host = @gethostbyname( $name );
	}

	return array( $best_id, $host, $name );
}

/**
 * Check all nodes and find the closest and active one.
 * 
 * @param bool $force If true performs checking nodes without checking allowed interval LAB_RECHECK
 * @param bool $kick_dns If true preload DNS cache to eliminate DNS resolving delay
 *
 * @return bool|int
 */
function lab_check_nodes( $force = false, $kick_dns = false ) {

	$nodes = lab_get_nodes();
	if ( ! $force && isset( $nodes['last_check'] ) && ( time() - $nodes['last_check'] ) < LAB_RECHECK ) {
		return false;
	}

	$nodes['nodes'] = array(); // clean up before testing

	cerber_update_set( '_cerberlab_', $nodes );

	for ( $i = 1; $i <= LAB_NODE_MAX; $i ++ ) {
		if ( $kick_dns ) {
			@gethostbyname( 'node' . $i . '.cerberlab.net' );
		}

		lab_send_request( array( 'test' => 'test', 'key' => 1 ), $i );
	}

	$nodes = lab_get_nodes();
	$nodes['best'] = lab_best_node( $nodes['nodes'] );
	$nodes['last_check'] = time();

	cerber_update_set( '_cerberlab_', $nodes );

	return $nodes['best'];
}

/**
 * Find the best (closest) and active node in the list of nodes
 *
 * @param array $nodes
 *
 * @return int the ID of a node, 0 if no node available
 */
function lab_best_node( $nodes = array() ) {
	if ( empty( $nodes ) ) {
		return 0;
	}

	$active_nodes = array();
	foreach ( $nodes as $id => $data ) {
		if ( $data['last']['1'] ) {  // only active nodes must be in the list
			$active_nodes[ $id ] = $data['last']['0'];
		}
	}

	if ( $active_nodes ) {
		asort( $active_nodes );
		reset( $active_nodes );
		$best_id = key( $active_nodes );
	}
	else {
		$best_id = 0;  // no active nodes found :-(
	}

	return $best_id;
}
/**
 * Update node status
 *
 * @param $node_id
 * @param array $last
 *
 * @return bool
 */
function lab_update_node_last($node_id, $last = array()) {
	$nodes = lab_get_nodes();
	$nodes['nodes'][$node_id]['last'] = $last;
	return cerber_update_set('_cerberlab_', $nodes);
}

function lab_get_nodes() {
	$nodes = cerber_get_set( '_cerberlab_' );
	if ( ! $nodes || ! is_array( $nodes ) ) {
		$nodes = array();
	}

	return $nodes;
}

/**
 * Small diagnostic report about nodes for admin
 *
 * @return string Report to show in Dashboard
 */
function lab_status() {

	$ret = '';

	if ( ! crb_get_settings( 'cerberlab' ) && ! lab_lab() ) {
		$ret .= '<p style = "color:red;"><b>Cerber Lab connection is disabled</b></p>';
	}

	$nodes = lab_get_nodes();
	if ( empty( $nodes['nodes'] ) ) {
		return $ret . '<p>No information. No request has been made yet.</p>';
	}

	$tb = array();
	ksort( $nodes['nodes'] );

	foreach ( $nodes['nodes'] as $id => $node ) {
		$delay  = round( 1000 * $node['last'][0] ) . ' ms';
		$ago    = cerber_ago_time( $node['last'][3] );
		$status = $node['last'][1];
		if ( $status ) {
			$status = '<span style = "color:green;">' . $status . '</span>';
		}
		else {
			$status = 'Down';
			$delay  = 'Unknown';
		}
		if ( $country = lab_get_country( $node['last'][5], false ) ) {
			$country = cerber_country_name( $country );
		}
		else {
			$country = '';
		}
		$tb[] = array(
			$id,
			$delay,
			$status,
			$node['last'][2],
			$node['last'][5],
			$country,
			$ago,
			$node['last'][4],
		);
	}

	$ret .= cerber_make_plain_table( $tb, array(
		'Node',
		'Processing time',
		'Operational status',
		'Info',
		'IP address',
		'Location',
		'Last request',
		'Protocol'
	), false, true );

	if ( ! empty( $nodes['best'] ) ) {
		$ret .= '<p>Closest (fastest) node: ' . $nodes['best'] . '</p>';
	}
	if ( ! empty( $nodes['last_check'] ) ) {
		$ret .= '<p>Last check for all nodes: ' . cerber_ago_time( $nodes['last_check'] ) . '</p>';
	}
	$key = lab_get_key();
	$ret .= '<p>Site ID: ' . $key[0] . '</p>';

	return $ret;
}

/**
 * Check if the Cerber Cloud alive
 *
 * @return bool|int The number of active nodes, false otherwise
 */
function lab_is_cloud_ok(){
	$nodes = lab_get_nodes();
	if ( ! $nodes ) {
		return false;
	}
	$n = 0;
	foreach ( $nodes['nodes'] as $id => $node ) {
		if ($node['last'][1]){
			$n++;
		}
	}
	if ($n > 0){
		return $n;
	}

	return false;
}

/**
 * Save data for lab
 *
 * @param $ip string IP address
 * @param $reason_id integer Why IP is malicious
 * @param $details
 */
function lab_save_push( $ip, $reason_id, $details = null ) {
	global $cerber_status;
	$ip = filter_var( $ip, FILTER_VALIDATE_IP );
	if ( ! $ip || is_ip_private( $ip ) || crb_acl_is_white( $ip ) || ! ( crb_get_settings( 'cerberlab' ) || lab_lab() ) ) {
		return;
	}

	$reason_id = absint( $reason_id );
	if ( $reason_id == 8 || $reason_id == 9 ) {
		$details = array( 'uri' => $_SERVER['REQUEST_URI'] );
	}
	elseif ( $reason_id == 100 ) {
		$details = absint( $cerber_status );
	}

	if ( is_array( $details ) ) {
		$details = serialize( $details );
	}
	$details = cerber_real_escape( $details );

	cerber_db_query( 'INSERT INTO ' . CERBER_LAB_TABLE . ' (ip, reason_id, details, stamp) VALUES ("' . $ip . '",' . $reason_id . ',"' . $details . '",' . time() . ')' );
}
/**
 * Get data for lab
 *
 * @return array|bool
 */
function lab_get_push() {
	//$result = $wpdb->get_results( 'SELECT * FROM ' . CERBER_LAB_TABLE, ARRAY_A );
	$result = cerber_db_get_results( 'SELECT * FROM ' . CERBER_LAB_TABLE, MYSQLI_ASSOC );
	if ( $result ) {
		return array( 'type_1' => $result );
	}

	return false;
}

function lab_trunc_push(){
	cerber_db_query( 'TRUNCATE TABLE ' . CERBER_LAB_TABLE );
	cerber_db_query( 'DELETE FROM ' . CERBER_LAB_TABLE ); // TRUNCATE might not work on a weird hosting
}

function cerber_push_lab() {
	if ( ! crb_get_settings( 'cerberlab' ) ) {
		return;
	}
	if ( cerber_get_set( '_cerberpush_', null, false ) ) {
		return;
	}
	lab_api_send_request();
	cerber_update_set( '_cerberpush_', 1, null, false, time() + LAB_INTERVAL );
}

function lab_gen_site_id() {
	if ( is_multisite() ) {
		$home = network_home_url();
	}
	else {
		$home = cerber_get_site_url();
	}

	$home = rtrim( trim( $home ), '/' );
	$id   = substr( $home, strpos( $home, '//' ) + 2 );

	$site_id = md5( $id );

	return $site_id;
}

/**
 * @since 8.5.6
 * @param $site_id string
 *
 * @return bool
 */
function lab_check_site_id( $site_id ) {
	if ( ! $site_id
	     || $site_id != substr( preg_replace( '/[^A-Z0-9]/i', '', $site_id ), 0, 32 ) ) {
		return false;
	}

	return true;
}

function lab_get_key( $refresh = false, $nocache = false) {
	static $key = null;

	if ( ! isset( $key ) || $nocache ) {
		$key = cerber_get_set( '_cerberkey_' );
	}

	if ( $refresh
	     || ! $key
	     || ! is_array( $key ) ) {

		if ( empty( $key ) || ! is_array( $key ) ) {
			$key = array( '' );
		}

		if ( ! lab_check_site_id( $key[0] ) ) {
			$key[0] = lab_gen_site_id();
		}
		else {
			// Fix: WP is installed in a subdirectory, rewrite old, domain-based site ID
			if ( 2 < substr_count( cerber_get_site_url(), '/' ) ) {
				$key[0] = lab_gen_site_id();
			}
		}

		$key[1] = time();

		if ( empty( $key[4] ) ) {
			$key[4] = 'SK//' . str_shuffle( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
		}

		cerber_update_set( '_cerberkey_', $key );
	}

	return $key;
}

function lab_update_key( $lic, $expires = 0 ) {
	$key    = lab_get_key();
	$key[2] = strtoupper( $lic );
	$key[3] = absint( $expires );
	delete_site_option( '_cerberkey_' ); // old
	cerber_update_set( '_cerberkey_', $key );
	lab_get_key( false, true ); // reload the static cache
}

function lab_validate_lic( $lic = '', &$msg = '' ) {

	$msg = '';
	$key = lab_get_key();

	if ( ! $lic ) {
		if ( empty( $key[2] ) ) {
			$msg = '(1)';
			return false;
		}
		$lic = $key[2];
	}

	$request = array( 'key' => $key, 'validate' => $lic );
	$i       = LAB_NODE_MAX;
	while ( ! ( $ret = lab_send_request( $request ) ) && $i > 0 ) {
		$i --;
	}

	if ( ! $ret || ! isset( $ret['response']['expires_gmt'] ) ) {
		cerber_admin_notice( 'A network error occurred while verifying the license key. Please try again in a couple of minutes.' );
		$msg = '(2)';
		$expires = 0;
	}
	else {
		$msg = '(3)';
		$expires = absint( $ret['response']['expires_gmt'] );
	}

	lab_update_key( $lic, $expires );

	if ( ! $expires ) {
		$msg = '(4)';

		return false;
	}

	if ( time() > ( $expires + LAB_LICENSE_GRACE ) ) {
		$msg = '(5)';

		return false;
	}

	$df         = get_option( 'date_format', false );
	$gmt_offset = get_option( 'gmt_offset', false ) * 3600;

	$msg = date_i18n( $df, $gmt_offset + $expires );
	return true;
}

function lab_lab( $with_date = 0 ) {
	return true;
	if ( $slave = nexus_get_context() ) {
		if ( ! $slave->site_key ) {
			return false;
		}
		$exp = $slave->site_key;
	}
	else {

		$key = lab_get_key();

		if ( empty( $key[2] ) || empty( $key[3] ) ) {
			return false;
		}

		$exp = $key[3];
	}

	if ( time() > ( $exp + LAB_LICENSE_GRACE ) ) {
		return false;
	}
	if ( ! $with_date ) {
		return true;
	}
	if ( $with_date == 2 ) {
		return $exp;
	}

	$df         = get_option( 'date_format', false );
	$gmt_offset = get_option( 'gmt_offset', false ) * 3600;

	return date_i18n( $df, $gmt_offset + $exp );
}

function lab_indicator(){
	if ( lab_is_cloud_ok() && lab_lab() ) {
		$key = lab_get_key();
		$sid = 'Site ID: '.$key[0];
		return '<div title="'.$sid.'" style="margin-left:10px; float: right; font-weight: normal; font-size: 80%; padding: 0.35em 0.6em 0.35em 0.6em; color: #fff; background-color: #00ae65cc;"><i style="font-size:1.5em; vertical-align: top; line-height: 1;" class="crb-icon crb-icon-bxs-shield"></i></div>';
		//return '<div title="'.$sid.'" style="font-size: 80%; padding: 0.35em 0.6em 0.35em 0.6em; color: #fff; background-color: #00ae65cc;"><i style="font-size:1.5em; vertical-align: top; line-height: 1;" class="crb-icon crb-icon-bxs-shield"></i></div>';
		//return '<div title="'.$sid.'" style="float: right; font-weight: normal; font-size: 80%; padding: 0.35em 0.6em 0.35em 0.6em; color: #fff; background-color: #51AE43;"><span style="vertical-align: top; line-height: 1;" class="dashicons dashicons-yes"></span> Cerber Security Cloud Protection is active</div>';
	}

	return '';
}

/**
 * Opt in for the connection to Cerber Lab
 *
 */
function lab_opt_in(){

	if ( lab_lab() || crb_get_settings( 'cerberlab' ) ) {
		return;
	}

	if ( $o = get_site_option( '_lab_o' . 'pt_in_' ) ) {
		//if ( $o[0] == 'NO' && ( $o[1] + 3600 * 24 * 30 ) > time() ) {
		if ( ( $o[1] + 3600 * 24 * 30 ) > time() ) {
			return;
		}
	}

	//if ( $c = get_site_option( '_cerber_activated' ) ) {
	//	$c = maybe_unserialize( $c );
	if ( $c = cerber_get_set( '_activated' ) ) {
		if ( ! empty( $c['time'] ) && ( $c['time'] + 3600 * 24 * 7 ) > time() ) {
			return;
		}
	}

	$h = __('Want to make WP Cerber even more powerful?','wp-cerber');
	$text = __('Allow WP Cerber to send locked out malicious IP addresses to Cerber Lab. This helps the plugin team to develop new algorithms for WP Cerber that will defend WordPress against new threats and botnets that are appearing  everyday. You can disable the sending in the plugin settings at any time.','wp-cerber');
	$ok = __('OK, nail them all','wp-cerber');
	$no = __('NO, maybe later','wp-cerber');
	$more = '<a href="https://wpcerber.com/cerber-laboratory/" target="_blank">' . __( 'Know more', 'wp-cerber' ) . '</a>';

	$notice =
		'<div style="width: 70%; min-height: 200px;"><h2>' . $h . '</h2><p>' . $text . '</p>' .
		'<p style="float:left;">' . $more . '</p>
		<p style="text-align:right; margin-top: 2em;">
		<input type="button" id = "lab_ok" class="button button-primary cerber-dismiss" value=" &nbsp; ' . $ok . ' &nbsp; "/>
		<input type="button" id = "lab_no" class="button button-primary cerber-dismiss" value=" &nbsp; ' . $no . ' &nbsp; "/>
		</p></div>';

	crb_show_admin_announcement( $notice, false );
}

/**
 * Save a user choice
 *
 * @param string $button
 */
function lab_user_opt_in( $button = '' ) {
	$a = null;
	if ( $button == 'lab_ok' ) {
		$a     = array( 'YES', time() );
		$o     = get_site_option( CERBER_OPT );
		$o['cerberlab'] = 1;
		update_site_option( CERBER_OPT, $o );
	}
	if ( $button == 'lab_no' ) {
		$a = array( 'NO', time() );
	}
	if ( $a ) {
		update_site_option( '_lab_o' . 'pt_in_', $a );
	}
}


/**
 * Return country ISO code
 *
 * @param $ip array|string  IP address(es)
 * @param bool $cache_only  Use local cache. If false and an IP is not in the cache, sends a request to the Cerber Lab GEO service.
 *
 * @return array|string|false    A list of country codes if a list of IPs provided, otherwise a string with the country code.
 */
function lab_get_country( $ip, $cache_only = true ) {
	global $remote_country;

	if ( ! lab_lab() ) {
		return false;
	}

	if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
		$ip_id = cerber_get_id_ip( $ip );
		if ( isset( $remote_country[ $ip_id ] ) ) {
			return $remote_country[ $ip_id ];
		}
	}

	if ( ! is_array( $ip ) ) {
		$ip_list = array( $ip );
	}
	else {
		$ip_list = $ip;
	}

	$ret = array();
	$ask = array();

	foreach ( $ip_list as $item ) {
		if ( ! filter_var( $item, FILTER_VALIDATE_IP ) ) {
			continue;
		}

		$ip_id = cerber_get_id_ip( $item );
		$ret[ $ip_id ] = null;

		if ( is_ip_private( $item ) ) {
			continue;
		}

		if ( cerber_is_ipv4( $item ) ) {
			$ip_long = ip2long($item);
			$where = ' WHERE ip_long_begin <= ' . $ip_long . ' AND ' . $ip_long . ' <= ip_long_end';
		}
		else {
			$where = ' WHERE ip = "' . $item . '"';
		}

		$country = cerber_db_get_var( 'SELECT country FROM ' . CERBER_LAB_NET_TABLE . $where );

		if ( $country ) {
			$ret[ $ip_id ] = $country;
		}
		elseif (!$cache_only) {
			$ask[ $ip_id ] = $item;
		}
	}

	if ( !$cache_only && $ask ) {
		$lab_data = lab_api_send_request( array( 'ask_cerberlab' => $ask ) );

		if ( ! empty( $lab_data['response']['payload'] ) ) {
			foreach ( $lab_data['response']['payload'] as $ip_id => $ip_data ) {
			//foreach ( $ask as $ip_id => $ip_ask ) {
				//if ( ! empty( $lab_data['response']['payload'][ $ip_id ] ) ) {
					//$ip_data = $lab_data['response']['payload'][ $ip_id ];
					lab_geo_update( $ip_data['ip'], $ip_data );
					lab_reputation_update($ip_data['ip'] , $ip_data);
					$ret[ $ip_id ] = $ip_data['network']['geo']['country_iso'];
				//}
			}
		}

	}

	if ( ! is_array( $ip ) && ! empty( $ret ) ) {
		return current($ret);
	}

	return $ret;

}

/**
 * Update local GEO cache with a given network data
 *
 * @param string $ip IP address which country we asked for
 * @param array $data IP and its network data
 */
function lab_geo_update( $ip = '', $data = array() ) {
	global $remote_country;
	if ( empty( $data['network']['geo'] ) ) {
		return;
	}

	$code = substr( $data['network']['geo']['country_iso'], 0, 3 );
	$remote_country[ cerber_get_id_ip( $ip ) ] = $code;
	$expires = time() + absint($data['network']['geo']['country_expires']);
	$begin = intval($data['network']['begin']);
	$end   = intval($data['network']['end']);

	if ( cerber_is_ipv4( $ip ) ) {
		$where = ' WHERE ip_long_begin = ' . $begin . ' AND ip_long_end = ' . $end;
		//$ip = '';
	}
	else {
		$where = ' WHERE ip = "' . $ip . '"';
	}

	$exists = cerber_db_get_var( 'SELECT ip FROM ' . CERBER_LAB_NET_TABLE . $where );

	if ( $exists ) {
		cerber_db_query( 'UPDATE ' . CERBER_LAB_NET_TABLE . " SET expires = $expires, country = '$code' $where" );
	}
	else {
		cerber_db_query( 'INSERT INTO ' . CERBER_LAB_NET_TABLE . " (ip, ip_long_begin, ip_long_end, country, expires) VALUES ('{$ip}',{$begin},{$end},'{$code}',{$expires})" );
	}

	// The list of names of the countries

	if ( ! empty( $data['network']['geo']['country'] ) ) {
		foreach ( $data['network']['geo']['country'] as $locale => $name ) {
			$where  = ' WHERE country = "' . $code . '" AND locale = "' . $locale . '"';
			$exists = cerber_db_get_var( 'SELECT country FROM ' . CERBER_GEO_TABLE . $where );

			if ( ! $exists ) {
				cerber_db_query( 'INSERT INTO ' . CERBER_GEO_TABLE . ' (country, locale, country_name) VALUES ("' . $code . '","' . $locale . '","' . $name . '")' );
			}
			else {
				//$wpdb->query( 'UPDATE ' . CERBER_GEO_TABLE . ' SET country_name = "' . $name . '"' . $where );
			}
		}
	}
}

function lab_cleanup_cache() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	cerber_db_query( 'TRUNCATE TABLE ' . CERBER_LAB_NET_TABLE );
	cerber_db_query( 'TRUNCATE TABLE ' . CERBER_LAB_IP_TABLE );
}

/**
 * Return node ID for the current request if it is originated from the Cerber Cloud
 *
 * @return bool|int Node ID if the current request comes from a valid node or false otherwise
 */
function lab_get_real_node_id() {
	static $ret;

	if ( $ret !== null ) {
		return $ret;
	}

	$hostname = @gethostbyaddr( cerber_get_remote_ip() );
	if ( ! $hostname || filter_var( $hostname, FILTER_VALIDATE_IP ) ) {
		$ret = false;

		return $ret;
	}
	$domain = array_slice( explode( '.', $hostname ), - 3, 3 );
	if ( ! $domain || count( $domain ) != 3 ) {
		$ret = false;

		return $ret;
	}
	if ( $domain[1] . '.' . $domain[2] !== 'cerberlab.net' ) {
		$ret = false;

		return $ret;
	}

	$ret = absint( substr( $domain[0], 4, 2 ) ); // 0-99

	return $ret;
}
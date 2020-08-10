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
if ( ! defined( 'WPINC' ) ) { exit; }

/**
 * RIPE REST API
 *
 * RIPE Database Acceptable Use Policy
 * https://www.ripe.net/manage-ips-and-asns/db/support/documentation/ripe-database-acceptable-use-policy
 * https://www.ripe.net/manage-ips-and-asns/db/faq/faq-db
 *
 */

define('RIPE_ERR_EXPIRE',300);
define('RIPE_OK_EXPIRE',24 * 3600);
define('RIPE_HOST','http://rest.db.ripe.net/');
/*
 * Search for information about IP by using RIPE REST API, method 'search'
 * @since 2.7
 *
 */
function ripe_search($ip = ''){
	//if ( !cerber_is_ip_or_net($ip) || !is_user_logged_in() || !is_admin()) return false;
	if ( !cerber_is_ip_or_net($ip)) return false;
	$key = 'ripe-'.cerber_get_id_ip($ip);
	$ripe = get_transient($key);
	if (false === $ripe) {
	//if (1==1) {
		$args = array();
		$args['headers']['Accept'] = 'application/json';
		$args['headers']['User-Agent'] = 'Cerber Security Plugin for WP';
		$ripe_response = wp_remote_get( RIPE_HOST.'search?query-string=' . $ip, $args );
		if ( is_wp_error( $ripe_response ) ) {
			$error = 'WHOIS: '.$ripe_response->get_error_message();
			return $error;
		}
		if (absint($ripe_response['response']['code']) != 200) {
			$error = 'WHOIS: '.$ripe_response['response']['message'].' / '.$ripe_response['response']['code'];
			set_transient( $key, $error , RIPE_ERR_EXPIRE );
			//return $error;
			return ''; // don't blow user mind
		}
		$ripe = $ripe_response;
		$ripe['body'] = json_decode($ripe_response['body']);
		if (JSON_ERROR_NONE != json_last_error()) {
			$error = 'WHOIS: '.json_last_error_msg();
			set_transient( $key, $error , RIPE_ERR_EXPIRE );
			return $error;
		}
		$ripe['abuse-email'] = ripe_find_abuse_contact($ripe['body'],$ip);
		set_transient( $key, serialize( $ripe ), RIPE_OK_EXPIRE );
	}
	else {
		$ripe = unserialize($ripe);
	}
	//$ripe['abuse-email'] = ripe_get_abuse_contact($ripe['body'],$ip);
	return $ripe;
}
/*
 * Retrieve abuse email from response, rollback to direct request to the API
 * @since 2.7
 *
 */
function ripe_find_abuse_contact($ripe_body, $ip){
	//http://rest.db.ripe.net/abuse-contact
	$email = '';
	foreach ($ripe_body->objects->object as $object) {
		foreach ($object->attributes->attribute as $att){
			if ($att->name == 'abuse-mailbox' && is_email($att->value)){
				$email = $att->value;
				break;
			}
		}
	}
	if (!$email) { // make an API request
		$args                      = array();
		$args['headers']['Accept'] = 'application/json';
		$ripe_response = wp_remote_get( RIPE_HOST.'abuse-contact/' . $ip, $args );
		if ( is_wp_error( $ripe_response ) ) {
			return $ripe_response->get_error_message();
		}
		$abuse = json_decode($ripe_response['body']);
		$abuse = get_object_vars($abuse);
		if (is_email($abuse['abuse-contacts']->email)) $email = $abuse['abuse-contacts']->email;
	}
	return $email;
}
/*
 * Get and parse RIPE response to human readable view
 * @since 2.7
 *
 */
function ripe_readable_info($ip){
	$ripe = ripe_search($ip);
	if (!is_array($ripe)) {
		if (!$ripe) return array('error' => 'RIPE error');
		return array('whois' => $ripe);
	}
	$ret = array();

	$body = $ripe['body'];
	if ($body->service->name != 'search') return $ret; // only for RIPE search requests & responses

	$info = '';
	foreach ($body->objects->object as $object) {
		$info.='<table class="whois-object otype-'.$object->type.'"><tr><td colspan="2"><b>'.strtoupper($object->type).'</b></td></tr>';
		foreach ($object->attributes->attribute as $att){
			$ret['data'][$att->name] = $att->value;
			if (is_email($att->value)) $value = '<a href="mailto:'.$att->value.'">'.$att->value.'</a>';
			elseif (strtolower($att->name) == 'country') {
				$value = cerber_get_flag_html($att->value) . '<b>' . cerber_country_name($att->value) . ' (' . $att->value . ')</b>';
				$ret['country'] = $value;
			}
			else $value = $att->value;
			$info.='<tr><td>'.$att->name.'</td><td>'.$value.'</td></tr>';
		}
		$info.='</table>';
	}

	if (!empty($ripe['abuse-email']) && is_email($ripe['abuse-email'])) {
		$ret['data']['abuse-mailbox'] = $ripe['abuse-email'];
	}

	// Network
	if (!empty($ret['data']['inetnum'])) {
		$ret['data']['network'] = $ret['data']['inetnum'];
	}
	
	$ret['whois'] = $info;

	return $ret;
}


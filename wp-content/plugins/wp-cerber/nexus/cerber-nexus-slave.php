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

require_once( dirname( cerber_plugin_file() ) . '/cerber-maintenance.php' );

add_action( 'plugins_loaded', function () {
	if ( ! $key = cerber_get_get( 'cerber_magic_key', '[\d\w\-_]+' ) ) {
		return;
	}
	if ( ! $data = cerber_get_set( '_the_key_' . $key ) ) {
		cerber_404_page();
	}

	@ini_set( 'display_errors', 0 );
	cerber_delete_set( '_the_key_' . $key );
	cerber_load_admin_code();
	crb_admin_download_file( $data['query']['type'], $data['query'] );
	cerber_404_page();
}, 0 );

class CRB_Master {
	public $page;
	public $tab;
	public $base;
	public $is_post;
	public $seal;
	public $type;
	public $get_params = array(); // Unsanitized, as is
	public $payload;
	public $action;
	public $screen;
	public $at_site;
	public $locale;
	public $error;

	final function __construct() {
		global $crb_assets_url, $crb_ajax_loader;

		$fields = nexus_get_fields();
		if ( ! $payload = cerber_get_post( $fields[1] ) ) {
			$this->error = new WP_Error( 'nexus_format_error', 'Invalid request: master request malformed' );
			return;
		}
		$request = json_decode( stripslashes( $payload ), true );
		if ( JSON_ERROR_NONE != json_last_error() ) {
			$this->error = new WP_Error( 'json_error', 'Unable to parse JSON: ' . json_last_error_msg() );
			return;
		}

		array_walk_recursive( $request, function ( &$e ) {
			$e = str_replace( array( '<br/>' ), "\n", $e ); // restore new lines after json_decode
		} );

		$this->seal       = $request['seal'];
		$this->base       = rtrim( $request['base'], '/' ) . '/';
		$this->get_params = $request['params'];
		$this->payload    = $request['payload'];
		$this->type       = $request['payload']['type'];
		$this->page       = preg_replace( '/[^\w\-]/i', '', crb_array_get( $request, 'page' ) );
		$this->tab        = preg_replace( '/[^\w\-]/i', '', crb_array_get( $request, 'tab' ) );
		$this->at_site    = crb_array_get( $request, 'at_site' );
		$this->screen     = crb_array_get( $request, 'screen' );
		$this->is_post    = ( ! empty( $request['is_post'] ) ) ? true : false;

		if ( ! $this->locale = crb_array_get( $request, 'master_locale' ) ) {
			if ( ! $this->locale = get_site_option( 'WPLANG' ) ) {
				$this->locale = 'en_US';
			}
		}

		$crb_assets_url   = $request['assets'];
		$crb_ajax_loader = $crb_assets_url . 'ajax-loader.gif';

		if ( $this->type == 'ajax' ) {
			if ( ! $this->action = crb_array_get( $request['params'], 'action' ) ) {
				$this->action = $this->get_post_fields( 'action' );
			}
			$this->action = preg_replace( '/[^\w\-]/i', '', (string) $this->action );
		}
	}

	/**
	 * @param integer|string $key
	 * @param mixed $default
	 * @param $pattern string REGEX pattern for value validation, UTF is not supported
	 *
	 * @return mixed
	 */
	final function get_post_fields( $key = null, $default = false, $pattern = ''  ) {
		if ( ! empty( $this->payload['data']['post'] ) ) {
			if ( $key ) {
				return crb_array_get( $this->payload['data']['post'], $key, $default, $pattern );
			}

			return $this->payload['data']['post'];
		}
		if ( $key ) {
			return false;
		}

		return array();
	}
}

/**
 * @return CRB_Master
 */
function nexus_request_data() {
	static $crb_master;
	if ( ! is_object( $crb_master ) ) {
		$crb_master = new CRB_Master();
	}

	return $crb_master;
}

function nexus_slave_process() {

	if ( ! nexus_is_valid_request() ) {
		return;
	}

	@ini_set( 'display_errors', 0 );
	@ignore_user_abort( true );
	crb_raise_limits();

	cerber_update_set( 'processing_master_request', 1, 0, false, time() + 120 );

	nexus_diag_log( 'Parsing request...' );

	$crb_master = nexus_request_data();

	if ( is_wp_error( $crb_master->error ) ) {
		nexus_diag_log( 'ERROR: ' . $crb_master->error->get_error_message() );

		exit;
	}

	nexus_diag_log( 'Request is parsed, generating response...' );

	add_filter( 'plugin_locale', function () {
		return nexus_request_data()->locale;
	}, 9999 );

	$use_eng = false;
	if ( nexus_request_data()->locale == 'en_US' ) {
		$use_eng = true;
		// We do not load any translation files
		add_filter( 'override_load_textdomain', function ( $val, $domain, $mofile ) {
			return true;
		}, 9999, 3 );
	}

	if ( ! $use_eng ) {
		$r = load_plugin_textdomain( 'wp-cerber', false, 'wp-cerber/languages' );

		/*if ( ! $r ) {
			nexus_diag_log( 'Unable to load plugin localization files ' . (string) nexus_request_data()->locale );
		}*/
	}

	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	require_once( ABSPATH . 'wp-admin/includes/template.php' );
	require_once( ABSPATH . WPINC . '/pluggable.php' );
	require_once( ABSPATH . WPINC . '/vars.php' );

	cerber_load_admin_code();

	$response = nexus_prepare_responce();

	if ( is_wp_error( $response ) ) {
		$m = __( 'ERROR:', 'wp-cerber' ) . ' ' . $response->get_error_message();
		nexus_diag_log( $m );
		$error    = array( $response->get_error_code(), $response->get_error_message() );
		$response = array( 'error' => $error );
	}

	nexus_diag_log( 'Now sending response to the master...' );

	$result = nexus_net_send_responce( $response );

	if ( is_wp_error( $result ) ) {
		nexus_diag_log( __( 'ERROR:', 'wp-cerber' ) . ' ' . $result->get_error_message() );
	}

	cerber_delete_set( 'processing_master_request' );
	nexus_diag_log( '=== SLAVE HAS FINISHED ===' );
	exit;

}

/**
 * Avoid simultaneous requests from the master(s)
 *
 * @return bool
 *
 */
function nexus_is_processing() {
	return ( cerber_get_set( 'processing_master_request' ) ) ? true : false;
}

/*
function nexus_render_admin_page_1(){
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	require_once( ABSPATH . '/wp-admin/includes/template.php' );
	require_once( ABSPATH . '/wp-includes/pluggable.php' );
	cerber_load_admin_code();
	$page_id = 'cerber-recaptcha';
	//$tab  = 'captcha';
	if ( empty( $tab ) ) {
		$tab = $page_id;
	}
	cerber_wp_settings_setup( cerber_get_setting_id( $tab ) );
	// Render inner html
	$page = cerber_get_admin_page_config( $page_id );
	call_user_func( $page['callback'], '' );
	exit;
}*/

function nexus_render_admin_page( $page, $tab ) {
	$id = ( empty( $tab ) ) ? $page : $tab;
	cerber_wp_settings_setup( cerber_get_setting_id( $id ) );
	cerber_admin_init(); // TODO: remove, old way
	ob_start();
	cerber_render_admin_page( $page, $tab );    // Render whole html

	return ob_get_clean();
}

function nexus_parse_request() {
	$fields = nexus_get_fields();
	if ( ! $payload = cerber_get_post( $fields[1] ) ) {
		return new WP_Error( 'nexus_format_error', 'Invalid request: master request malformed' );
	}
	$request = json_decode( stripslashes( $payload ), true );
	if ( JSON_ERROR_NONE != json_last_error() ) {
		return new WP_Error( 'json_error', 'Unable to parse JSON: ' . json_last_error_msg() );
	}

	return $request;
}

function nexus_prepare_responce() {

	$master = nexus_request_data();

	nexus_diag_log( 'Type: ' . $master->type );

	if ( ! nexus_is_granted() ) {
		return new WP_Error( 'not_allowed', 'Operation is not allowed in this context' );
	}

	$result = '';

	switch ( $master->type ) {
		case 'get_page':
			CRB_Addons::load_active();

			return array(
				'html' => nexus_render_admin_page( $master->page, $master->tab ),
				//'o'    => get_option( 'gmt_offset' ),
				//'z'    => get_option( 'timezone_string' ),
			);
			break;
		case 'submit':
			CRB_Addons::load_active();

			if ( $master->get_post_fields( 'option_page' ) ) { // True WP setting page
				return nexus_process_wp_settings_form( $master->get_post_fields() );
			}
			else {
				return cerber_admin_request( $master->is_post );
			}
			// A new way: processing + follow up rendering in a single request
			//return nexus_render_admin_page( $master->page, $master->tab );
			break;
		case 'manage':
			return cerber_admin_request( $master->is_post );
			break;
		case 'hello':
			//case 'checkup':
			return array( 'numbers' => nexus_get_numbers() );
			break;
		case 'ping':
			return array( 'pong' );
			break;
		case 'sw_upgrade':
			$result             = nexus_sw_upgrade();
			$result ['numbers'] = nexus_get_numbers();
			if ( ! $result['updates'] ) {
				//nexus_diag_log( 'No updates are available' );
			}
			break;
		case 'ajax':
			if ( ! crb_admin_allowed_ajax( $master->action ) ) {
				return new WP_Error( 'unknown_ajax', 'The action ' . $master->action . ' is not supported' );
			}
			global $nexus_doing_ajax;
			$nexus_doing_ajax = true;
			ob_start();
			do_action( 'wp_ajax_' . $master->action );
			$nexus_doing_ajax = false;
			nexus_diag_log( 'AJAX ' . $master->action . ' completed' );

			return ob_get_clean();
			break;
		default:
			return new WP_Error( 'unknown_request', 'This type of request is not supported' );
			break;
	}

	return $result;
}

function nexus_sw_upgrade() {
	$ret = array( 'updates' => 0, 'completed' => 1, 'results' => array() );

	if ( nexus_is_processing() ) {
		$ret['completed'] = 0;
		$ret['wait']      = cerber_get_remote_ip();

		return $ret;
	}

	$list = crb_array_get( nexus_request_data()->payload, 'list' );
	switch ( nexus_request_data()->payload['sw_type'] ) {
		case 'plugins':
			if ( empty( $list ) ) {
				// Upgrade all
				$to_update = array();
				$active  = get_option( 'active_plugins' );

				if ( ! $plugins = get_site_transient( 'update_plugins' ) ) {
					wp_update_plugins();
					$plugins = get_site_transient( 'update_plugins' );
				}

				if ( isset( $plugins->response ) ) {
					$to_update = array_intersect( $active, array_keys( $plugins->response ) );
				}

				nexus_diag_log( 'Total active plugins to update: ' . count( $to_update ) );

				if ( $done = cerber_get_set( 'plugins_done' ) ) { // Upgraded in the current bulk operation
					$to_do = array_diff( $to_update, $done );
				}
				else {
					$done  = array();
					$to_do = $to_update;
				}

				if ( ! empty( $to_do ) ) {
					$run_now = array_shift( $to_do );
					$done[]  = $run_now;
					$list    = array( $run_now );
				}

				if ( ! empty( $to_do ) ) {
					$ret['completed'] = 0; // Something left
					cerber_update_set( 'plugins_done', $done, 0, true, time() + 300 * count( $to_do ) );
				}
				else {
					cerber_delete_set( 'plugins_done' );
				}
			}

			if ( ! empty( $list ) && is_array( $list ) ) {
				foreach ( $list as $obj ) {
					$ret['results'][ $obj ] = cerber_update_plugin( $obj );
				}
			}

			break;
	}

	nexus_diag_log( cerber_flat_results( $ret['results'] ) );

	return $ret;
}

/**
 * Process forms generated by WP Settings API
 *
 * @param $form array WP Settings form fields
 *
 * @return string|WP_Error
 */
function nexus_process_wp_settings_form( $form ) {
	if ( ! $page = crb_array_get( $form, 'option_page' ) ) {
		return new WP_Error( 'unknown_option', 'Unable to identify settings page' );
	}
	if ( ! wp_verify_nonce( crb_array_get( $form, '_wpnonce' ), $page . '-options' ) ) {
		return new WP_Error( 'nonce_failed', 'Nonce verification failed' );
	}

	$wp_option = 'cerber-' . cerber_get_wp_option_id( $page );
	if ( ! isset( $form[ $wp_option ] ) ) {
		return new WP_Error( 'no_value', 'Setting fields are not set' );
	}

	$new_values = crb_array_get( $form, $wp_option );
	nexus_diag_log( 'Updating ' . $wp_option . ' option' );
	cerber_update_site_option( $wp_option, $new_values );
	cerber_admin_message( __( 'Settings updated', 'wp-cerber' ) );

	return '';
}

/**
 * @param string|array $payload
 *
 * @return bool|WP_Error
 */
function nexus_net_send_responce( $payload ) {
	$ret  = true;
	$role = nexus_get_role_data();
	if ( is_array( $payload ) ) {
		$p = json_encode( $payload, JSON_UNESCAPED_UNICODE ); // 8.0.5
	}
	elseif ( is_scalar( $payload ) ) {
		$p = (string) $payload;
	}
	else {
		$p   = '';
		$ret = new WP_Error( 'wrong_type', 'Unsupported slave data type' );
	}

	$processing = microtime( true ) - cerber_request_time();

	$hash     = hash( 'sha512', $role['slave']['nx_echo'] . sha1( $p ) );
	$response = json_encode( array(
		'payload'  => $payload,
		'extra'    => array(
			'versions' => array( CERBER_VER, cerber_get_wp_version(), PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION, PHP_OS, lab_lab( 2 ) )
		),
		'echo'     => $hash,
		'p_time'   => $processing,
		'scheme' => 2 // 8.0.5
	), JSON_UNESCAPED_UNICODE );

	if ( JSON_ERROR_NONE != json_last_error() ) {
		$response = 'Unable to encode payload. JSON error.';
		$ret      = new WP_Error( 'json_error', 'Unable to encode JSON: ' . json_last_error_msg() );
	}

	echo $response; // To master

	return $ret;
}

function nexus_is_granted( $type = null ) {

	$acs = crb_get_settings( 'slave_access' );
	$lab = lab_lab();

	if ( $acs >= 8 ) {
		return false;
	}

	if ( $acs == 2 ) {
		if ( $lab ) {
			return true;
		}
	}

	// RO mode

	if ( ! $type ) {
		$type = nexus_request_data()->type;
	}

	if ( in_array( $type, array( 'get_page', 'hello', 'ping', 'sw_upgrade' ) ) ) {
		return true;
	}

	if ( $type == 'submit' ) {
		if ( crb_get_post_fields( 'cerber_license' ) ) {
			return true;
		}
		return false;
	}

	if ( $type == 'ajax' ) {
		$action = nexus_request_data()->action;
		if ( ! crb_admin_allowed_ajax( $action ) ) {
			return false;
		}
		if ( in_array( $action, array( 'cerber_scan_control', 'cerber_view_file' ) ) ) {
			return true;
		}
		if ( $action == 'cerber_ajax' ) {
			$fields = crb_get_request_fields();
			if ( empty( $fields['acl_delete'] ) ) {
				return true;
			}
		}
	}

	return false;
}

function nexus_get_numbers() {
	$numbers = array();
	$active_plugins = get_option( 'active_plugins' );

	// see wp_get_update_data();
	$updates  = array( 'plugins' => 0, 'themes' => 0, 'wp' => 0, 'translations' => 0 );

	$pl_updates = get_site_transient( 'update_plugins' );
	if ( ! $pl_updates || ( $pl_updates->last_checked < ( time() - 7200 ) ) ) {
		delete_site_transient( 'update_plugins' );
		wp_update_plugins();
		$pl_updates = get_site_transient( 'update_plugins' );
	}

	if ( ! empty( $pl_updates->response ) ) {
		$updates['plugins'] = count( array_intersect( $active_plugins, array_keys( $pl_updates->response ) ) );
	}

	include_once( ABSPATH . 'wp-admin/includes/update.php' );
	if ( function_exists( 'get_core_updates' ) ) {
		$wp = get_core_updates( array( 'dismissed' => false ) );
		if ( ! empty( $wp ) ) {
			$updates['wp'] = 1;
		}
	}

	$scan = array();
	if ( ( $last_scan = cerber_get_scan() ) && $last_scan['finished'] ) {
		$scan['finished'] = $last_scan['finished'];
		$scan['numbers']  = $last_scan['numbers'];
	}

	$numbers['updates'] = $updates;
	$numbers['scan']    = $scan;

	// New

	$list = array();
	if ( ! empty( $pl_updates->response ) ) {
		$list = array_map( function ( $e ) {
			//$ret = (array) $e;
			$ret = array_map( function ( $e ) {
				return ( is_object( $e ) ) ? (array) $e : $e;
			}, (array) $e );

			return $ret;
		}, $pl_updates->response );
	}

	$numbers['pl_updates'] = $list;
	$numbers['active']     = $active_plugins;
	$numbers['plugins']    = get_plugins();
	$numbers['themes']     = crb_get_themes();
	$numbers['gmt']        = get_option( 'gmt_offset' );
	$numbers['tz']         = get_option( 'timezone_string' );

	return $numbers;
}

// We have to use our own "user id"
add_filter( 'nonce_user_logged_out', function ( $uid, $action ) {
	if ( ! nexus_is_valid_request() ) {
		return $uid;
	}

	return PHP_INT_MAX;

}, 10, 2 );

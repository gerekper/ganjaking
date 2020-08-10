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
require_once( dirname( __FILE__ ) . '/cerber-slave-list.php' );

define( 'CRB_ADD_SLAVE_LNK', '#TB_inline?width=450&height=350&inlineId=crb-popup-add-slave' );
define( 'CRB_NX_SLAVE', 'slave-edit-form' ); // Special form for editing slave data

function nexus_show_slaves() {
	//load_nexus_test_slaves();
	cerber_cache_enable();
	echo '<form id="crb-nexus-sites" method="get" action="">';
	wp_nonce_field( 'control', 'cerber_nonce' );
	echo '<input type="hidden" name="page" value="' . crb_admin_get_page() . '">';
	echo '<input type="hidden" name="cerber_admin_do" value="nexus_site_table">';
	$slaves = new CRB_Slave_Table();
	$slaves->prepare_items();
	$slaves->search_box( 'Search', 'search_id' );
	$slaves->display();
	echo '</form>';
}

function nexus_master_screen() {
	// Standard WP options
	crb_admin_screen_options();

	// Add our own fields
	add_filter( 'screen_settings', function ( $form, $screen ) {
		$set     = get_site_option( '_cerber_slist_screen', array() );
		$checked = ( crb_array_get( $set, 'url_name' ) ) ? 'checked="checked"' : '';
		$form    .= '<legend>Layout</legend>';
		$form    .= '<label for="crbuina">' . __( 'Show homepage in the Website column', 'wp-cerber' ) . '</label><input id="crbuina" type="checkbox" name="crb_url_in_name" value="1" ' . $checked . '/>';
		$checked = ( crb_array_get( $set, 'srv_ip' ) ) ? 'checked="checked"' : '';
		$form    .= '<br/><label for="crbsrip">' . __( 'Hide server IP address', 'wp-cerber' ) . '</label><input id="crbsrip" type="checkbox" name="crb_srv_ip" value="1" ' . $checked . '/>';
		$form    .= '<input type="hidden" name="nexus_slave_list_screen" value="1" />';

		return $form;
	}, 10, 2 );
}

add_filter( 'set-screen-option', function ( $status ) {
	// Save our own fields
	if ( isset( $_POST['nexus_slave_list_screen'] ) ) {
		$set             = get_site_option( '_cerber_slist_screen', array() );
		$set['url_name'] = crb_array_get( $_POST, 'crb_url_in_name', 0, '\d' );
		$set['srv_ip']   = crb_array_get( $_POST, 'crb_srv_ip', 0, '\d' );
		update_site_option( '_cerber_slist_screen', $set );
	}

	return $status;
} );

function nexus_show_slave_form( $site_id ) {
	$site = nexus_get_slave_data( $site_id );
	if ( ! $site ) {
		echo 'Website not found. <a href="' . cerber_admin_link( 'nexus_sites' ) . '">Return to the list</a>.';

		return;
	}

	$p = __( 'Select an existing group or enter a new one to add it', 'wp-cerber' );

	// We utilize WP settings API routines just to render the edit form

	$edit_fields = nexus_slave_form_fields(); // TODO implement this

	$edit_fields = array(
		'main' => array(
			'name'   => __( 'Website Properties', 'wp-cerber' ),
			//'info'   => __( 'User related settings', 'wp-cerber' ),
			'fields' => array(
				'site_id'    => array(
					'value'    => $site->id,
					'db_field' => 'id',
					'type'     => 'hidden',
					'title'    => '',
				),
				'site_url'   => array(
					'title'    => __( 'Website URL', 'wp-cerber' ),
					'value'    => $site->site_url,
					'disabled' => true,
				),
				'site_name'  => array(
					'title'     => __( 'Display as', 'wp-cerber' ),
					'label'     => 'Original website name: ' . $site->site_name_remote,
					'value'     => $site->site_name,
					'required'  => true,
					'maxlength' => 200
				),
				'site_group' => array(
					'class'       => 'crb-wide crb-select2-tags',
					'title'       => __( 'Group', 'wp-cerber' ),
					'set'         => nexus_get_groups( true ),
					'value'       => $site->group_id,
					'db_field'    => 'group_id',
					'type'        => 'select',
					'label'       => $p,
					'placeholder' => $p
				),
				/*'site_server' => array(
					'title'     => __( 'Server Name', 'wp-cerber' ),
					'value'     => $site->site_server,
					'maxlength' => 1000
				),*/
				'site_notes' => array(
					'title'     => __( 'Notes', 'wp-cerber' ),
					'value'     => $site->site_notes,
					'type'      => 'textarea',
					'maxlength' => 1000
				),
			)
		),
		'owner' => array(
			'name'   => __( 'Website Owner', 'wp-cerber' ),
			//'info'   => __( 'User related settings', 'wp-cerber' ),
			'fields' => array(
				'first_name'    => array(
					'title'     => __( 'First Name' ),
					'maxlength' => 100
				),
				'last_name'     => array(
					'title'     => __( 'Last Name' ),
					'maxlength' => 100
				),
				'owner_email'   => array(
					'title'     => __( 'Email' ),
					'maxlength' => 100
				),
				'owner_phone'   => array(
					'title'     => __( 'Phone', 'wp-cerber' ),
					'maxlength' => 100,
				),
				'owner_biz'   => array(
					'title'     => __( 'Company', 'wp-cerber' ),
					'maxlength' => 200,
				),
				'owner_address' => array(
					'title'     => __( 'Address', 'wp-cerber' ),
					'maxlength' => 200
				),
			)
		),
	);

	foreach ( $edit_fields['owner']['fields'] as $key => &$f ) {
		$f['value'] = crb_array_get( $site->details, $key );
	}

	// TODO: replace WP settings API with a new form-processing engine
	cerber_wp_settings_setup( CRB_NX_SLAVE, $edit_fields );
	cerber_show_settings_form( CRB_NX_SLAVE );
}

function nexus_slave_form_fields() { // TODO implement this
	return array( 'first_name', 'last_name', 'owner_email', 'owner_phone', 'owner_biz', 'owner_address' );
}

function nexus_get_groups( $sort = false ) {
	if ( ! $groups = cerber_get_set( 'nexus_groups' ) ) {
		$groups = array( 'Default' );
	}

	if ( $sort ) {
		asort( $groups );
	}

	return $groups;
}

add_action( 'admin_init', function () {

	if ( is_admin() && nexus_is_master() ) { // @since 8.6.3.3
		nexus_set_context();
		if ( nexus_get_context() ) {
			nexus_send_admin_request();
		}
	}

	if ( nexus_is_master() && function_exists( 'nexus_schedule_refresh' ) ) {
		nexus_schedule_refresh();
	}

	if ( ! is_super_admin() ) {
		return;
	}

	if ( cerber_is_wp_ajax() ) {
		nexus_ajax_router();
	}

	if ( nexus_get_context() ) {
		add_action( 'admin_notices', 'cerber_show_admin_notice', 999 );
		add_action( 'network_admin_notices', 'cerber_show_admin_notice', 999 );
	}

	// Some tricks to obtain form data via WP settings API
	register_setting( 'cerberus-' . CRB_NX_SLAVE, 'cerber-' . CRB_NX_SLAVE );
	add_filter( 'pre_update_option_cerber-' . CRB_NX_SLAVE, function ( $fields, $old_value, $option ) {

		cerber_cache_enable();

		$site_id = absint( $fields['site_id'] );

		$group_id = 0;
		if ( ! empty( $fields['site_group'] ) ) {
			$groups = nexus_get_groups();
			if ( is_numeric( $fields['site_group'] ) ) {
				$group_id = (int) $fields['site_group'];
			}
			if ( ! $group_id || ! isset( $groups[ $group_id ] ) ) {
				// Add new group
				$new = strip_tags( $fields['site_group'] );
				if ( $new ) {
					$groups   = nexus_get_groups();
					$groups[] = $new;
					end( $groups );
					$group_id = key( $groups );
					cerber_update_set( 'nexus_groups', $groups );
				}
			}
		}

		$new_details = array_intersect_key( $fields, array_flip( nexus_slave_form_fields() ) );
		$new_details = array_map( 'strip_tags', $new_details );

		$fields = array_replace( $fields, $new_details );

		$fields['group_id'] = $group_id;
		nexus_update_slave( $site_id, $fields );
		nexus_delete_unused( 'nexus_groups', 'group_id' );

		return '';
	}, 10, 3 );

}, 0 );

function nexus_add_slave( $token ) {
	if ( ! is_super_admin() || ! nexus_is_master() ) {
		return;
	}
	$token = trim( $token );
	if ( ( ! $t = nexus_the_token( $token ) )
	     || empty( $t[0] )
	     || empty( $t[1] )
	     || empty( $t[2] )
	     || empty( $t[3] )
	     || empty( $t[4] )
	     || empty( $t[5] )
	     || empty( $t[6] )
	) {
		cerber_admin_notice( __( 'Security access token is invalid', 'wp-cerber' ) );

		return;
	}

	// Subdir install? Add slash to avoid 301 redirection
	if ( strpos( substr( $t[5], strpos( $t[5], '.' ) ), '/' ) ) {
		$url = rtrim( $t[5], '/' ) . '/';
	}
	else {
		$url = $t[5];
    }

	$no_https = ( 'https://' !== substr( $url, 0, 8 ) ) ? true : false;

	$data                     = array();
	$data['site_pass']        = $t[0];
	$data['site_echo']        = $t[1];
	$data['x_field']          = $t[2];
	$data['x_num']            = $t[3];
	// These are shown in the dashboard, make them safe
	$data['site_url']         = substr( esc_url( $url ), 0, 250 );
	$data['site_name']        = mb_substr( htmlspecialchars( htmlspecialchars_decode( $t[6] ) ), 0, 250 );
	$data['site_name_remote'] = $data['site_name'];

	$data = array_map( function ( $e ) {
		return '"' . cerber_real_escape( $e ) . '"';
	}, $data );

	if ( cerber_db_get_var( 'SELECT id FROM ' . cerber_get_db_prefix() . CERBER_MS_TABLE . ' WHERE site_url = ' . $data['site_url'] ) ) {
		cerber_admin_notice( __( 'The website you are trying to add is already in the list', 'wp-cerber' ) );
	}

	if ( ! cerber_db_insert( cerber_get_db_prefix() . CERBER_MS_TABLE, $data ) ) {
		cerber_admin_notice( 'Unable to add website' );
	}
	else {
		$site_id = cerber_db_get_var( ' SELECT LAST_INSERT_ID()' );
		$edit = cerber_admin_link( 'nexus_sites', array( 'site_id' => $site_id ) );
		cerber_admin_message( __( 'The website has been added successfully', 'wp-cerber' )
                              . '&nbsp; [ <a href="' . $edit . '">' . __( 'Click to edit', 'wp-cerber' ) . '</a> | '
		                      . ' <a href="' . wp_nonce_url( cerber_admin_link() . '&amp;cerber_admin_do=nexus_switch&nexus_site_id=' . $site_id, 'control', 'cerber_nonce' ) . '">' . __( 'Switch to the Dashboard', 'wp-cerber' ) . '</a> ]' );
		if ( $no_https ) {
			//cerber_admin_notice( __( 'Note: No SSL encryption is enabled on the website this can lead to data leakage.', 'wp-cerber' ) );
			cerber_admin_notice( __( 'Keep in mind: You have added the website that does not support SSL encryption. This may lead to data leakage.', 'wp-cerber' ) );
		}

		//cerber_bg_task_add( array( 'func' => 'nexus_send', 'args' => array( array( 'type' => 'hello' ), $site_id ) ), true );
		nexus_add_bg_refresh( $site_id );
	}

}

/**
 * @param $id int Slave ID
 * @param $data array Sanitized slave data
 *
 * @return bool|mysqli_result|resource
 */
function nexus_update_slave( $id, $data ) {

	$id = absint( $id );
	if ( ! $id ) {
		return false;
	}

	$old = nexus_get_slave_data( $id );

	// Details
	$old_details = ( is_array( $old->details ) ) ? $old->details : array();
	$details_fields = nexus_slave_form_fields();
	if ( $new_details = array_intersect_key( $data, array_flip( $details_fields ) ) ) {
		$data['details'] = serialize( array_replace( $old_details, $new_details ) );
	}

	// Name is always stored in escaped form!
	if ( isset( $data['site_name'] ) ) {
		$data['site_name'] = htmlspecialchars( $data['site_name'] );
	}

	// 1. Numbers
	$int_columns = array( 'group_id', 'site_status', 'updates', 'refreshed', 'last_scan', 'last_http', 'site_key' );
	$update      = array_map( function ( $e ) {
		return absint( $e );
	}, array_intersect_key( $data, array_flip( $int_columns ) ) );

	// 2. Escaping strings
	$str_columns = array( 'site_name', 'details', 'site_notes', 'plugin_v', 'wp_v', 'server_id', 'server_country' );
	$update      = array_merge( $update,
		array_map( function ( $e ) {
			return cerber_real_escape( $e );
		}, array_intersect_key( $data, array_flip( $str_columns ) ) ) );

	// SQL clause
	$fields = array();
	foreach ( $update as $field => $value ) {
		$fields[] = $field . '="' . $value . '"';
	}
	$sql_fields = implode( ',', $fields );

	if ( cerber_db_query( 'UPDATE ' . cerber_get_db_prefix() . CERBER_MS_TABLE . ' SET ' . $sql_fields . ' WHERE id = ' . $id ) ) {
		return true;
	}

	return false;
}

/**
 * @param $site_id
 *
 * @return bool|object
 */
function nexus_get_slave_data( $site_id ) {
	$site_id = absint( $site_id );
	$site = cerber_db_get_row( 'SELECT * FROM ' . cerber_get_db_prefix() . CERBER_MS_TABLE . ' WHERE id = ' . $site_id, MYSQL_FETCH_OBJECT );
	if ( ! $site ) {
		return false;
	}
	if ( ! empty( $site->details ) ) {
		$site->details = unserialize( $site->details );
	}

	return $site;
}

function nexus_get_slaves( $args ) {
	$order = '';
	if ( isset( $args['orderby'] ) ) {
		$order = ' ORDER BY ' . $args['orderby'] . ' ';
	}
	if ( isset( $args['order'] ) ) {
		$order .= $args['order'];
	}

	return cerber_db_get_results( 'SELECT * FROM ' . cerber_get_db_prefix() . CERBER_MS_TABLE . $order, MYSQL_FETCH_OBJECT );
}

/**
 * @param $ids integer|array
 *
 * @return bool
 */
function nexus_delete_slave( $ids ) {
	if ( ! is_super_admin() ) {
		return false;
	}
	/*$field = ( ! $bulk ) ? 'site_id' : 'ids';
	if ( ! $ids = cerber_get_get( $field ) ) {
		return false;
	}*/

	if ( ! is_array( $ids ) ) {
		$ids = array( $ids );
	}

	$ids = array_map( function ( $e ) {
		return absint( $e );
	}, $ids );

	$ret = cerber_db_query( 'DELETE FROM ' . cerber_get_db_prefix() . CERBER_MS_TABLE . ' WHERE id IN (' . implode( ',', $ids ) . ')' );

	if ( $ret ) {
		$num = cerber_db_get_var( 'SELECT ROW_COUNT()' );
		cerber_admin_message( sprintf( _n( 'Website has been deleted', '%s websites have been deleted', $num, 'wp-cerber' ), $num ) );

		foreach ( $ids as $id ) {
			nexus_delete_list( $id );
		}

		nexus_delete_unused( 'nexus_servers', 'server_id' );
		nexus_delete_unused( 'nexus_countries', 'server_country' );
		nexus_delete_unused( 'nexus_groups', 'group_id' );

		return true;
	}
	else {
		cerber_admin_notice( 'Unable to delete website' );
		return false;
	}
}

function nexus_get_back_link() {
	return cerber_admin_link_add( array( 'cerber_admin_do' => 'nexus_switch', 'nexus_site_id' => 0 ) );
}

// ======================================================================================

function nexus_show_remote_page() {

    /* This code for new settings mechanism

	$slave = nexus_get_context();
	if ( cerber_is_http_post()
		 && ( $m = cerber_get_post( 'cerber_nexus_seal' ) )
		 && sha1( $slave->id . '|' . get_current_user_id() ) === $m ) {
		$response = nexus_send( array(
			'type' => 'submit',
			'data' => array(
				'post' => $_POST,
			)
		) );
	}
	else {
		$response = nexus_send( array(
			'type' => 'get_page',
			'data' => array(
			)
		) );
	}*/

    // An old, two steps version
    // TODO: remove the second step after upgrading Cerber's settings mechanism to a new version

	if ( cerber_is_http_post()
	     && ( nexus_seal( crb_array_get( $_POST, 'cerber_nexus_seal', 'none' ) ) ) ) {
		$response = nexus_send( array(
			'type' => 'submit',
			'data' => array(
				'post' => $_POST,
			)
		) );
	}

	// A separate request to render the page cause settings cache is updated now
	$response = nexus_send( array(
		'type' => 'get_page'
	) );

	if ( is_array( $response ) ) {
		echo $response['html'];
	}
	else {
		echo $response;
	}
}

function nexus_send_admin_request() {
	if ( empty( $_GET['cerber_admin_do'] )
	     || ( empty( $_GET['cerber_nonce'] )
	          && empty( $_POST['cerber_nonce'] ) ) ) {
		return;
	}
	$response = nexus_send( array(
		'type' => 'manage'
	) );
}

function nexus_ajax_router() {
    //return;

    if ( empty( $_REQUEST['action'] )
	     || empty( $_REQUEST['ajax_nonce'] ) ) {
		return;
	}

	if ( ! nexus_is_master()
	     || ! nexus_get_context()
	     || ! crb_admin_allowed_ajax( $_REQUEST['action'] )
	     || ! is_user_logged_in()
    ) {
		return;
	}

	check_ajax_referer( 'crb-ajax-admin', 'ajax_nonce' );

	$response = nexus_send( array(
		'type'  => 'ajax',
		//'cache' => false,
		'data'  => array(
			'post' => $_POST,
		)
	) );

	if ( is_wp_error( $response ) ) {
		nexus_diag_log( 'NETWORK ERROR: ' . $response->get_error_message() );
	}
	else {
		echo $response;
	}

	exit;
}

/**
 * @param $request array
 * @param $slave_id int
 *
 * @return bool|mixed
 */
function nexus_send( $request, $slave_id = null ) {
    global $nexus_last_http, $nexus_last_curl, $nexus_slave_name, $nexus_slave_id;

	nexus_diag_log( '/\/ Initiating ' . $request['type'] . ' request to the slave' . ( ( $slave_id ) ? ' #' . $slave_id : ' from context' ) );

	$slave  = ( $slave_id ) ? nexus_get_slave_data( $slave_id ) : nexus_get_context();

	if ( ! $slave ) {
		return false;
	}

	$network = nexus_net_send_request( $request, $slave );

	nexus_update_slave( $slave->id, array( 'last_http' => $nexus_last_http ) );
	$nexus_slave_id = $slave->id;
	$nexus_slave_name = $slave->site_name;

	if ( ! is_wp_error( $network ) ) {

		nexus_process_extra( $network, $slave );

		if ( ! empty( $network['payload']['error'] ) ) { // A critical error on the slave
			$m = 'An error occurred on ' . $slave->site_name . ': ' . htmlspecialchars( $network['payload']['error'][1] );
			cerber_admin_notice( $m );
			nexus_diag_log( $m );
			return '';
		}

		$response = $network['payload'];

		if ( cerber_is_wp_ajax() ) {
			return $response;
		}

		if ( isset( $response['redirect'] ) ) {
			if ( $redirect_to = crb_array_get( $response, 'redirect_url' ) ) {
				nexus_diag_log( '> > > Redirecting to ' . $redirect_to );
				// TODO should we use wp_safe_redirect()???
				header( 'Location: ' . $redirect_to, true, 302 );
				exit;
			}
			else {
				cerber_safe_redirect( crb_array_get( $response, 'remove_args' ) );
			}
		}

		return $response;
	}
	else {

		$codes = array(
			301 => 'Unexpected HTTP redirection on the slave. Check if WP Cerber is installed and active on the slave website.',
			302 => 'Unexpected HTTP redirection on the slave. Check if WP Cerber is installed and active on the slave website.',
			403 => 'Access to the slave website is denied.',
			500 => 'Remote website (web server) is unable to proceed due to a fatal software (PHP) error. Check the server error log on the slave website.'
		);

		$causes = array(
			'A security plugin on the slave website is interfering with the WP Cerber plugin',
			'A directive in the .htaccess file on the slave website is blocking incoming requests',
			'A firewall or a proxy service (like Cloudflare) is blocking (filtering out) incoming requests to the slave website',
			'The IP address of this master is locked out or in the Black Access List on the slave website',

			'The slave mode on the remote website has been re-enabled making the security token saved on this master invalid',
			'The slave mode has been disabled on the remote website',
			'The IP address of this master website does not match the one set in the slave settings on the remote website',
			'The WP Cerber plugin has been deactivated on the slave website',

			'The remote server is redirecting incoming requests to another website',
			'The domain name of the slave website has been changed',

			'The remote server is down or not responding',
			'The SSL certificate of the slave website is expired or invalid',
            'There is no network connectivity between this master server and the server on which the slave website is running',
		);

		$kb = array(
			// 200
			'json_error'     => array( 4, 5, 1, 6, 7 ),
			'checksum_error' => array( 4 ),

			// Not 200
			0                => array( 8, 9, 10, 11, 12 ),
			301              => array( 8, 9, 11 ),
			302              => array( 8, 9, 11 ),
			403              => array( 0, 1, 2, 3 ),
		);

		if ( $network->get_error_code() == 'http_error' ) {
			if ( ! $error = crb_array_get( $codes, $nexus_last_http ) ) {
				$desc  = $nexus_last_http . ' ' . get_status_header_desc( $nexus_last_http );
				$error = '<a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/' . $nexus_last_http . '" target="_blank">' . $desc . '</a>';
			}
			else {
				$error = $nexus_last_http . ' ' . $error;
			}
			$error = 'HTTP ERROR ' . $error;
		}
		else {
			$error = $network->get_error_message();
		}

		nexus_diag_log( 'NETWORK ERROR: ' . $error );

		$parse  = parse_url( $slave->site_url );
		$domain = $parse['host'];
		$ip     = gethostbyname( $domain );
		if ( ! cerber_is_ip( $ip ) ) {
			$ip = 'Unknown. Unable to resolve the IP address. Possibly the domain ' . $domain . ' is not delegated.';
			$hostname = 'Unknown';
		}
		else {
			$hostname = gethostbyaddr( $ip );
		}

		?>
        <div style="padding: 4em;">

            <h3><?php _e( 'Invalid response from the slave website', 'wp-cerber' ); ?></h3>
            <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
            Website: <?php echo $slave->site_name; ?><br/>
            Website URL: <?php echo $slave->site_url; ?><br/>
            <p style="font-weight: bold;">This may be caused by a number of reasons</p>
            <ul style="list-style: disc;">
				<?php

				if ( ! $kb_filter = crb_array_get( $kb, $network->get_error_code() ) ) {
					if ( ! $kb_filter = crb_array_get( $kb, $nexus_last_http ) ) {
						$kb_filter = null;
					}
				}

				$show = array();
				if ( $kb_filter ) {
					foreach ( $kb_filter as $key ) {
						$show[] = $causes[ $key ];
					}
					//$show = array_intersect_key( $causes, array_flip( $kb_filter ) );
				}

				if ( ! $show ) {
					$show = $causes;
				}

				echo '<li>' . implode( '</li><li>', $show ) . '</li>';

				?>
            </ul>

            <?php
			$slave = nexus_get_context();
			if ( $slave ) {
				echo '<p><a href="' . nexus_get_back_link() . '">Switch back to the master website</a></p>';
				echo '<p><a href="' . $slave->site_url . '/wp-admin/admin.php?page=cerber-nexus" target="_blank">Check the slave settings on ' . $slave->site_name . '</a></p>';
			}
            ?>

            <p style="margin-top: 2em;">Diagnostic information</p>
            <p class="crb-monospace" style="font-size: 90%">
                HTTP code: <?php echo $nexus_last_http; ?><br/>
                Response size: <?php echo $nexus_last_curl['size_download']; ?><br/>
                IP address: <?php echo $ip; ?><br/>
                Hostname: <?php echo $hostname; ?><br/>
		        <?php

		        foreach ( $nexus_last_curl as $key => $val ) {
			        if ( is_scalar( $val ) ) {
				        echo $key . ': ' . htmlspecialchars( $val );
			        }
			        else {
				        echo $key . ': ';
				        print_r( $val );
			        }
			        echo '<br/>';
		        }
		        ?>
                </p>
        </div>
		<?php
	}

	return false;
}

function nexus_process_extra( $data, $slave ) {

	$update = array();

	$v = $data['extra']['versions'];

	if ( $slave->plugin_v != $v[0] ) {
		$update['plugin_v'] = $v[0];
	}
	if ( $slave->wp_v != $v[1] ) {
		$update['wp_v'] = $v[1];
	}

	if ( isset( $v[6] ) && $slave->site_key != $v[6] ) {
		$update['site_key'] = $v[6];
	}

	if ( $nums = crb_array_get( $data['payload'], 'numbers' ) ) {

		$update['refreshed'] = time();

		$u = $nums['updates']['plugins'] + $nums['updates']['wp'];
		if ( $slave->updates != $u ) {
			$update['updates'] = $u;
		}

		if ( ! empty( $nums['scan'] ) ) {
			$update['last_scan'] = $nums['scan']['finished'];

			if ( $nb = crb_array_get( $nums['scan'], 'numbers' ) ) {
				// TODO move to separate meta table?
				cerber_update_set( '_nexus_tmp_' . $slave->id, $nb );
			}
		}

		// Plugins

        if ( $plugins = crb_array_get( $nums, 'plugins' ) ) {
			$installed = array();

			$active = array_flip( crb_array_get( $nums, 'active', array() ) );

	        foreach ( $plugins as $plugin => $data ) {

	            $key = 'nexus_p_' . sha1( $plugin . $data['Version'] );

		        $installed[] = array(
			        $key,
			        $data['Version'],
			        (string) ( isset( $active[ $plugin ] ) ) ? '1' : '0'
		        );

		        // Plugin data for common usage

		        if ( ! cerber_get_set( $key, null, false ) ) {
			        $data['plugin_slug'] = $plugin;
			        $data['plugin_key']  = sha1( $plugin );
			        cerber_update_set( $key, $data );
		        }
	        }

			nexus_update_list( $slave->id, 'plugins', $installed );
		}

		// Updates

		if ( $pl_updates = crb_array_get( $nums, 'pl_updates' ) ) {
			nexus_update_updates( $pl_updates );
		}

	}

	if ( $update ) {
		nexus_update_slave( $slave->id, $update );
		if ( ! $nums ) {
			nexus_add_bg_refresh( $slave->id );
		}
	}

}

function nexus_update_updates( $pl_updates ) {
	foreach ( $pl_updates as $plugin => $data ) {

		$key = 'nexus_upd_' . sha1( $plugin );
		$go  = true;

		if ( ( $pup = cerber_get_set( $key ) )
		     && ( $data['new_version'] == $pup['new_version'] ) ) {
			$go = false;
		}
		if ( $go ) {
			cerber_update_set( $key, $data );
		}
	}
}

function nexus_get_update( $plugin, $version = null ) {

	$update = cerber_get_set( 'nexus_upd_' . sha1( $plugin ) );

	if ( $version
         && version_compare( $version, $update['new_version'], '>=' ) ) {
		return false;
	}

	return $update;
}

/**
 * @param $payload array
 * @param $slave object
 *
 * @return bool|array|string|WP_Error
 */
function nexus_net_send_request( $payload, $slave ) {
	global $crb_assets_url, $nexus_last_http, $nexus_last_curl;

	if ( ! is_super_admin()
	     && ! ( defined( 'CRB_DOING_BG_TASK' ) && CRB_DOING_BG_TASK ) ) {
		return new WP_Error( 'no_context', 'Not permitted in this context' );
	}

	if ( empty( $slave->site_pass )
	     || empty( $slave->site_url )
	     || empty( $slave->x_field )
	     || empty( $slave->x_num )
	     || ( ! $field_names = nexus_get_fields( $slave ) ) ) {
		return new WP_Error( 'invalid_slave', 'Slave configuration is corrupted for the slave ' . $slave->id );
	}

	array_walk_recursive( $payload, function ( &$e ) {
		$e = str_replace( array( "\r\n", "\n", "\r" ), '<br/>', $e ); // preserve new lines before json_encode
	} );

	$data            = array();
	$data['seal']    = nexus_seal();
	$data['params']  = $_GET;
	$data['base']    = ( ! is_multisite() ) ? admin_url() : network_admin_url();
	$data['assets']  = $crb_assets_url;
	$data['is_post'] = cerber_is_http_post();
	$data['payload'] = $payload;
	$data[ rand() ]  = rand(); // random checksum for identical requests

	if ( crb_get_settings( 'master_locale' ) ) {
		$data['master_locale'] = ( crb_get_settings( 'admin_lang' ) ) ? 'en_US' : get_user_locale();
	}

	if ( ! cerber_is_wp_ajax()
	     && ! cerber_is_wp_cron() ) {
		$data['page']    = crb_admin_get_page();
		$data['tab']     = crb_admin_get_tab();
		$data['at_site'] = ( ! crb_get_settings( 'master_at_site' ) ) ? '' : ' @ ' . $slave->site_name;
		$data['screen']  = array( 'per_page' => crb_admin_get_per_page() );
	}

	$x_num = array_shift( $field_names );

	$fields = array();

	$fields[ $field_names[0] ] = json_encode( $data, JSON_UNESCAPED_UNICODE );
	if ( JSON_ERROR_NONE != json_last_error() ) {
		return new WP_Error( 'json_error', 'Unable to encode request: ' . json_last_error_msg() );
	}

	$auth = hash( 'sha512', $slave->site_pass . sha1( $fields[ $field_names[0] ] ) );

	foreach ( $field_names as $i => $name ) {
		if ( isset( $fields[ $name ] ) ) {
			continue;
		}
		if ( $x_num == $i ) {
			$fields[ $name ] = $auth;
		}
		else {
			$fields[ $name ] = str_shuffle( $auth );
		}
	}

	$curl = @curl_init();
	if ( ! $curl ) {
		return new WP_Error( 'no_curl', 'Unable to init cURL library. Enable PHP cURL extension in your hosting control panel.' );
	}

	nexus_diag_log( 'Sending HTTP request to ' . $slave->site_url );

	curl_setopt_array( $curl, array(
		CURLOPT_URL               => $slave->site_url,
		CURLOPT_FOLLOWLOCATION    => 0,
		CURLOPT_POST              => true,
		CURLOPT_POSTFIELDS        => http_build_query( $fields ),
		CURLOPT_RETURNTRANSFER    => true,
		CURLOPT_CONNECTTIMEOUT    => 10, // including domain resolving
		CURLOPT_TIMEOUT           => 15, // including CURLOPT_CONNECTTIMEOUT
		CURLOPT_DNS_CACHE_TIMEOUT => 1 * 3600,
		CURLOPT_SSL_VERIFYHOST    => 2,
		CURLOPT_SSL_VERIFYPEER    => true,
		//CURLOPT_CERTINFO          => 1, doesn't work
		//CURLOPT_VERBOSE          => 1,
		CURLOPT_CAINFO            => ABSPATH . WPINC . '/certificates/ca-bundle.crt',
		CURLOPT_ENCODING          => '' // allows built-in compressions
	) );

	$response = @curl_exec( $curl );
	$curl_info = curl_getinfo( $curl );
	$code = intval( curl_getinfo( $curl, CURLINFO_HTTP_CODE ) );
	$nexus_last_http = $code;
	$nexus_last_curl = $curl_info;

	curl_close( $curl );

	if ( $code == 200 ) {
		nexus_diag_log( 'HTTP 200 OK' );
		nexus_diag_log( 'Slave data size ' . $curl_info['size_download'] . ', receiving took ' . $curl_info['total_time'] . ' seconds' );
	}

	nexus_diag_log( 'Domain name lookup took ' . $curl_info['namelookup_time'] . ' seconds' );

	if ( $code != 200 ) {
		nexus_diag_log( '=== NETWORK SUBSYSTEM ===' );
		nexus_diag_log( $curl_info );
		/*if ( $code != 403 ) {
			cerber_update_set( 'bad_response_' . $slave->id, array( time(), $code ) );
		}*/
		return new WP_Error( 'http_error', $code );
	}

	$ret = json_decode( $response, true );
	if ( JSON_ERROR_NONE != json_last_error() ) {
		return new WP_Error( 'json_error', 'Unable to decode the response: ' . json_last_error_msg() );
	}

	if ( is_array( $ret['payload'] ) ) {
		if ( isset( $ret['scheme'] ) ) { // @since 8.0.5
			$sha = sha1( json_encode( $ret['payload'], JSON_UNESCAPED_UNICODE ) );
		}
		else {
			$sha = sha1( serialize( $ret['payload'] ) ); // 8.0
		}
	}
	else {
		$sha = sha1( $ret['payload'] );
	}

	if ( ! hash_equals( $ret['echo'], hash( 'sha512', $slave->site_echo . $sha ) ) ) {
		return new WP_Error( 'checksum_error', 'Checksum mismatch: the slave response has been altered or security tokens mismatch.' );
	}

	nexus_diag_log( 'Slave ' . $slave->site_name . ' has generated response for ' . $ret['p_time'] );

	nexus_diag_log( '=== NETWORK HAS FINISHED OK ===' );

    return $ret;
}

function nexus_set_context() {
	if ( 'nexus_switch' != cerber_get_get( 'cerber_admin_do' )
	     || ! wp_verify_nonce( cerber_get_get( 'cerber_nonce' ), 'control' )
	     || ! is_super_admin() ) {
		return;
	}

	$id = absint( cerber_get_get( 'nexus_site_id' ) );

	if ( $slave = nexus_get_slave_data( $id ) ) {
		if ( crb_get_settings( 'master_swshow' ) ) {
			cerber_admin_message( sprintf( __( 'You have switched to %s', 'wp-cerber' ), $slave->site_name ) . '. ' . 'To switch back to the master, click the X icon on the toolbar.' );
		}

		$expire = time() + apply_filters( 'auth_cookie_expiration', 14 * DAY_IN_SECONDS, get_current_user_id(), true );

		if ( $back = cerber_get_get( 'back' ) ) {
			update_user_meta( get_current_user_id(), 'nexus_back_to_url', $back );
		}
	}
	else {
		cerber_admin_message( __( 'You have switched back to the master website', 'wp-cerber' ) );
		$expire = time();
		$id = 0;
	}

	cerber_set_cookie( 'cerber_nexus_id', $id, $expire, '/' );
	$remove = array(
		'cerber_admin_do',
		'cerber_nonce',
		'nexus_site_id',
        'back'
	);

	if ( $id ) {
		if ( crb_admin_get_page() == 'cerber-nexus' ) {
			$url = cerber_admin_link();
		}
		else {
			$url = remove_query_arg( $remove );
		}
	}
	else {
		if ( crb_get_settings( 'master_tolist' ) ) {
			if ( ! $url = get_user_meta( get_current_user_id(), 'nexus_back_to_url', true ) ) {
				$url = cerber_admin_link( 'nexus_sites' );
			}
			else {
				update_user_meta( get_current_user_id(), 'nexus_back_to_url', '' );
			}
		}
		else {
			$url = remove_query_arg( $remove );
		}
	}

	if ( ! $url ) {
		$url = cerber_admin_link();
	}

	wp_safe_redirect( $url );

	exit();
}

/**
 * A light version of nonce (pre-nonce)
 *
 * @param null $check_it
 *
 * @return bool|string
 */
function nexus_seal( $check_it = null ) {
	$slave = nexus_get_context();
	if ( ! $slave || ! $uid = get_current_user_id() ) {
		return false;
	}
	$seal = sha1( $slave->id . '|' . $uid . '|' . PHP_VERSION . '|' . PHP_SAPI );
	if ( $check_it === null ) {
		return $seal;
	}

	return ( $seal === $check_it );
}

function nexus_do_bulk() {
	if ( ! $ids = cerber_get_get( 'ids', '\d+' ) ) {
		cerber_admin_notice( 'No items selected' );
		return;
	}
	switch ( cerber_get_bulk_action() ) {
		case 'nexus_delete_slave':
			nexus_delete_slave( $ids );
			break;
		case 'nexus_upgrade_plugins':
			nexus_bg_upgrade( $ids, array() );
			//nexus_do_upgrade( $ids[0], array( CERBER_PLUGIN_ID ) );
			break;
		case 'nexus_upgrade_cerber':
			nexus_bg_upgrade( $ids, array( CERBER_PLUGIN_ID ) );
			//nexus_do_upgrade( $ids[0], array( CERBER_PLUGIN_ID ) );
			break;
	}
}

function nexus_bg_upgrade( $ids, $plugins ) {
	foreach ( $ids as $id ) {
		cerber_bg_task_add( 'nexus_do_upgrade', array(
			//'func'  => 'nexus_do_upgrade',
			'args'       => array( $id, $plugins, false ),
			'exec_until' => 'stop', // may not be boolean
		) );
	}
	cerber_admin_message( 'A background upgrade task has been launched' );
}

function nexus_do_upgrade( $slave_id, $plugins, $display_errors = false ) {
	$response = nexus_send( array(
		'type'    => 'sw_upgrade',
		'sw_type' => 'plugins',
		'list'    => $plugins
	), $slave_id );

	if ( ! empty( $response['results'] ) ) {
		nexus_diag_log( cerber_flat_results( $response['results'], $display_errors ) );
	}

	if ( ! empty( $response['wait'] ) ) {
		nexus_diag_log( 'Waiting for request from ' . $response['wait'] . ' is completed' );
		sleep( 10 );
	}

	if ( empty( $response ) || ! empty( $response['completed'] ) ) {
		nexus_add_bg_refresh( $slave_id );

		return 'stop';
	}

	sleep( 3 );

	return 0;
}

function nexus_schedule_refresh() {
	// ORDER BY id DESC ?
	$t = time() - ( is_super_admin() ? 1800 : 3600 );
	if ( $sites = cerber_db_get_col( 'SELECT id FROM ' . cerber_get_db_prefix() . CERBER_MS_TABLE . ' WHERE refreshed < ' . $t . ' AND last_http <= 200 LIMIT 50' ) ) {

		foreach ( $sites as $id ) {

			// Protective interval
			$key  = 'nexus_schedule';
			$last = cerber_get_set( $key, $id, false );
			if ( $last && ( $last > ( time() - 600 ) ) ) {
				continue;
			}
			
			cerber_update_set( $key, time(), $id, false, time() + 3600 );

			/*
			$key = 'nexus_refresh';
			$log = array();
			if ( $log = cerber_get_set( $key, $id ) ) {
				if ( ! is_array( $log ) ) {
					$log = array();
				}
				elseif ( ! empty( $log ) ) {
					$delay = 300 * count( $log );
					if ( reset( $log ) > ( time() - $delay ) ) {
						continue;
					}
				}
			}

			array_shift( $log );
			$log[] = time();
			cerber_update_set( $key, $log, $id, false, time() + 60 );
			*/

			//cerber_bg_task_add( array( 'func' => 'nexus_send', 'args' => array( array( 'type' => 'hello' ), $id ) ) );
			nexus_add_bg_refresh( $id );
		}
	}
}

function nexus_add_bg_refresh( $slave_id ) {
	cerber_bg_task_add( 'nexus_send', array( 'args' => array( array( 'type' => 'hello' ), $slave_id ) ) );
	cerber_bg_task_add( 'nexus_refresh_slave_srv', array( 'args' => array( $slave_id ) ) );
}

add_action( 'wp_before_admin_bar_render', function () {
	global $wp_admin_bar;

	if ( ! cerber_is_admin_page()
	     || ! nexus_is_master() ) {
		return;
	}

	if ( ! is_admin_bar_showing()
	     || ! $wp_admin_bar instanceof WP_Admin_Bar ) {
		return;
	}

	?>
    <div id="crb-popup-add-slave" style="display:none;">
        <form id="crb-add-slave" method="post" action="<?php echo cerber_admin_link( 'nexus_sites' ); ?>"
              style="padding-top:15px; height: 100%;">
			<?php wp_nonce_field( 'control', 'cerber_nonce' ); ?>
            <input type="hidden" name="page" value="cerber-nexus">
            <textarea name="new_slave_token" style="width: 100%; height: 80%; font-family: monospace;"
                      placeholder="Copy and paste Secret Access Token here"></textarea>
	        <?php
            //echo cerber_select( 'new_slave_group', nexus_get_groups(), 0, 'crb-wide crb-select2-tags' )
            ?>
            <input type="hidden" name="cerber_admin_do" value="add_slave">
            <p style="text-align: center;"><input type="submit" class="button button-primary" value="Add Website"></p>
        </form>
    </div>
	<?php

	//$wp_admin_bar->remove_node( 'new-content' );
	//$wp_admin_bar->remove_node( 'my-account' );
	$exclude = array();
	$exclude = array( 'query-monitor' );
	//$exclude = array( 'top-secondary' );
	foreach ( $wp_admin_bar->get_nodes() as $node => $data ) {
		if ( in_array( $data->id, $exclude ) ) {
			continue;
		}
		if ( empty( $data->parent ) ) {
			$wp_admin_bar->remove_node( $data->id );
		}
	}

	$current_id = null;
	if ( $current = nexus_get_context() ) {
		$current_id = $current->id;
		$title = __( 'You are here:', 'wp-cerber' ) . ' ' . $current->site_name;
	}
	else {
		$title = __( 'My Websites', 'wp-cerber' );
    }

	$wp_admin_bar->add_node( array(
		'id'    => 'crb_site_switch',
		'title' => '<span class="ab-icon"></span>' . $title,
		'href'  => cerber_admin_link( 'nexus_sites' ),
		'meta'  => array( 'class' => 'cerber-site-select' )
	) );

	$this_page = cerber_admin_link( crb_admin_get_tab(), array( 'page' => crb_admin_get_page() ), true );

	if ( $current ) {
		$wp_admin_bar->add_node( array(
			'id'    => 'crb_slave_site_menu',
			'title' => '<span class="ab-icon"></span>' . __( 'Visit Site' ),
			'href'  => $current->site_url,
		) );
		$wp_admin_bar->add_node( array(
			'id'     => 'crb_slave_admin',
			'parent' => 'crb_slave_site_menu',
			'title'  => 'Dashboard',
			'href'   => trim( $current->site_url, '/' ) . '/wp-admin/',
		) );


		$wp_admin_bar->add_node( array(
			'id'    => 'crb_to_master',
			//'parent' => 'top-secondary',
			'title' => '<span class="ab-icon"></span>',
			'meta'  => array( 'class' => 'ab-top-secondary', 'title' => 'Switch to the master' ),
			'href'  => nexus_get_back_link(),
		) );
	}

	if ( $slaves = nexus_get_slaves( array( 'orderby' => 'id', 'order' => 'DESC' ) ) ) {
		foreach ( $slaves as $slave ) {
			if ( $current_id === $slave->id ) {
				continue;
			}
			$wp_admin_bar->add_node( array(
				'parent' => 'crb_site_switch',
				'id'     => 'site' . $slave->id,
				'title'  => $slave->site_name,
				'href'   => $this_page . '&amp;cerber_admin_do=nexus_switch&nexus_site_id=' . $slave->id,
			) );
		}
	}
	else {
		$wp_admin_bar->add_node( array(
			'parent' => 'crb_site_switch',
			'id'     => 'none',
			'title'  => 'Click here to add a slave website',
			'href'   => cerber_admin_link( 'nexus_sites' ),
		) );
	}

}, 9999 );

add_filter( 'admin_body_class', function ( $var ) {
	if ( cerber_is_admin_page() && nexus_get_context() ) {
		$var .= ' crb-remote';
	}

	return $var;
} );

add_action( 'admin_head', function () {
	if ( ! cerber_is_admin_page()
         || ! nexus_is_master() ) {
		return;
	}
	?>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {

            $("#wp-admin-bar-crb_slave_site_menu a").attr('target','_blank');

            $("#crb-nexus-sites .bulkactions .button").click(function (e) {
                if ('nexus_delete_slave' === $(this).prev('select').find(':selected').val()) {
                    if (!confirm('<?php _e( 'Are you sure you want to delete selected websites?', 'wp-cerber' ) ?>')) {
                        e.preventDefault();
                    }
                }
            });
        });
    </script>

	<?php
});

add_action( 'wp_ajax_cerber_master_ajax', function () {
	global $crb_assets_url;
	check_ajax_referer( 'crb-ajax-admin', 'ajax_nonce' );
	if ( ! is_super_admin() ) {
		wp_die( 'Oops! Access denied.' );
	}

	$response = array();

	switch ( crb_array_get( $_REQUEST, 'crb_ajax_do' ) ) {
		case 'nexus_view_updates':
			$slave_id = absint( $_REQUEST['slave_id'] );
			//$slave = nexus_get_slave_data( $slave_id );
			if ( $list = nexus_get_slave_plugins( $slave_id ) ) {
				$tbody = '';

				foreach ( $list as $item ) {
					$data = $item['data'];

					$data = array_map( function ( $e ) {
						return strip_tags( $e );
					}, $data );

					$c   = '';
					$upd = '';

					if ( $update = crb_array_get( $item, 'update' ) ) {
						$c = 'crb-slave-update';
						array_walk_recursive( $update, function ( &$e ) {
							$e = strip_tags( $e );
						} );

						//	dashicons-info
						$upd = '<span style="color: green;"><i class="dashicons dashicons-info"></i> ' . __( 'A newer version is available', 'wp-cerber' ) . ' (' . $update['new_version'] . ')</span>';
                        // $upd = '<span style="color: red;"><i class="dashicons dashicons-warning"></i> There is a newer version available' . ' (' . $update['new_version'] . ')</span>';
						//$upd = '<span style="color: red;"><i style="font-size: 100%;" class="crb-icon crb-icon-bxs-bell"></i> There is a newer version available' . ' (' . $update['new_version'] . ')</span>';
					}

					$tbody .= '<tr class="' . $c . '" data-plugin-slug="' . $data['plugin_slug'] . '"><td><p style="font-size: 1.09em;"><b>' . $data['Name'] . ' ' . $data['Version'] . '</b> ' . $upd . '</p><p>'.$data['Description'].'</p></td></tr>';

				}

				/*$heading = array(
					__( 'Plugin', 'wp-cerber' ),
				);
				$titles = '<tr><th>' . implode( '</th><th>', $heading ) . '</th></tr>';
				$html = '<table id="" class="widefat crb-table cerber-margin"><thead>' . $titles . '</thead><tfoot>' . $titles . '</tfoot><tbody>' . $tbody . '</tbody></table>';
				*/

                $html = '<table id="" class="widefat crb-table cerber-margin"><tbody>' . $tbody . '</tbody></table>';

				//$html .= '<pre style=" white-space: pre-wrap;">';
				//$html .= print_r( $list,1 );
				//$html .= '</pre>';

				$response = array( 'html' => $html, 'header' => __( 'Active plugins and updates on', 'wp-cerber' ) );
			}
			else {
				// TODO remove in production
				//cerber_bg_task_add( array( 'func' => 'nexus_send', 'args' => array( array( 'type' => 'hello' ), $slave_id ) ) );

				$slave = nexus_get_slave_data( $slave_id );
				$response = array( 'html'   => 'No information available. Refreshed: ' . cerber_auto_date( $slave->refreshed ),
				                   'header' => __( 'Active plugins and updates on', 'wp-cerber' )
				);
            }
			break;
	}

	echo json_encode( $response );
	exit;
} );


function nexus_get_slave_plugins( $slave_id, $sort = true, $active_only = true, $inc_updates = true ) {

    $slave_id = absint( $slave_id );
	$ac = ( $active_only ) ? ' AND status = "1"' : '';
	$plugins = cerber_db_get_results( 'SELECT * FROM ' . cerber_get_db_prefix() . CERBER_MS_LIST_TABLE . ' lst JOIN ' . cerber_get_db_prefix() . CERBER_SETS_TABLE . ' sts ON (lst.list_item = sts.the_key) WHERE lst.list_key = "plugins" AND lst.site_id = ' . $slave_id . ' ' . $ac );

	$ret = array();

	if ( $plugins ) {
		foreach ( $plugins as $plugin ) {
			$p           = array();
			$data        = unserialize( $plugin['the_value'] );
			$p['extra']  = $plugin['extra'];
			$p['status'] = $plugin['status'];
			$p['Name']   = $data['Name'];
			$p['data']   = $data;
			if ( $inc_updates ) {
				//$p['update'] = cerber_get_set( 'nexus_upd_' . $data['plugin_key'] );
				$p['update'] = nexus_get_update( $data['plugin_slug'], $data['Version'] );
			}
			$ret[] = $p;
		}

		if ( $sort ) {
			uasort( $ret, function ( $a, $b ) {
				return strnatcasecmp( $a['Name'], $b['Name'] );
			} );
		}
	}

	return $ret;
}

function nexus_refresh_slave_srv( $slave_id ) {

	if ( ! $slave = nexus_get_slave_data( $slave_id ) ) {
		return;
	}

	$server_host = parse_url( $slave->site_url, PHP_URL_HOST );
	$srv_ip      = @gethostbyname( $server_host );

	if ( ! cerber_is_ip( $srv_ip ) ) {
		return;
	}

	$srv_country = lab_get_country( $srv_ip, false );

	if ( $srv_ip != $slave->server_id || $srv_country != $slave->server_country ) {
		nexus_update_slave( $slave_id, array( 'server_id' => $srv_ip, 'server_country' => $srv_country ) );
	}

	// Updating servers

	if ( ! $servers = cerber_get_set( 'nexus_servers' ) ) {
		$servers = array();
	}

	$srv = crb_array_get( $servers, $srv_ip, array() );

	if ( ! $srv || ( $srv[0] < ( time() - 300 ) ) ) {
		$srv[0]             = time();
		$srv[1]             = @gethostbyaddr( $srv_ip );
		$srv[2]             = $srv_country;
		$servers[ $srv_ip ] = $srv;
		cerber_update_set( 'nexus_servers', $servers );
	}

	// Updating list of server countries

	if ( ! $list = cerber_get_set( 'nexus_countries' ) ) {
		$list = array();
	}

	if ( $srv_country && ! isset( $list[ $srv_country ] ) ) {
		$list[ $srv_country ] = cerber_country_name( $srv_country );
		cerber_update_set( 'nexus_countries', $list );
	}

}

/**
 * Cleanup dependable lists
 *
 * @param $key string List key
 * @param $field string DB field
 */
function nexus_delete_unused( $key, $field ) {
	if ( ! $list = cerber_get_set( $key ) ) {
		return;
	}

	$field = preg_replace( '/[^\w]/', '', $field );

	$used = cerber_db_get_col( 'SELECT DISTINCT ' . $field . ' FROM ' . cerber_get_db_prefix() . CERBER_MS_TABLE );

	if ( $used ) {
		$filtered = array_intersect_key( $list, array_flip( array_intersect( array_keys( $list ), $used ) ) );
		if ( count( $list ) != count( $filtered ) ) {
			cerber_update_set( $key, $filtered );
		}
	}
	else {
		cerber_update_set( $key, array() );
	}
}

function nexus_get_srv_info( $server_ip ) {
	if ( ! $servers = cerber_get_set( 'nexus_servers' ) ) {
		return false;
	}

	return crb_array_get( $servers, $server_ip, array() );
}

function nexus_update_list( $site_id, $key, $items = array() ) {

	list( $site_id, $key ) = nexus_sanitize( $site_id, $key );

	if ( empty( $site_id ) || empty( $key )  ) {
		return false;
	}

	if ( empty( $items ) ) {
		return nexus_delete_list( $site_id, $key );
	}

	// Reduce DB overhead if no changes in the list
    // Note: works only if values in $items are all strings
    // Doesn't work of the DB returns $old with not the same order as in $items

	if ( $old = nexus_get_list( $site_id, $key, array( 'list_item', 'extra', 'status' ), 0 ) ) {
		if ( count( $old ) === count( $items ) ) {
			if ( sha1( serialize( $old ) ) === sha1( serialize( $items ) ) ) {
				return true;
			}
		}
	}

	// Insert a new list

	$values = array();
	foreach ( $items as $item ) {
		if ( is_array( $item ) ) {
			$list_item = cerber_real_escape( $item[0] );
			$extra     = cerber_real_escape( crb_array_get( $item, 1, '' ) );
			$status    = substr( crb_array_get( $item, 2, '' ), 0, 1 );
		}
		else {
			$list_item = cerber_real_escape( $item );
			$extra     = '';
			$status    = '0';
		}
		$values[] = '(' . $site_id . ',"' . $key . '","' . $list_item . '","' . $extra . '", ' . $status . ')';
	}

	nexus_delete_list( $site_id, $key );

	$query = 'INSERT INTO ' . cerber_get_db_prefix() . CERBER_MS_LIST_TABLE . ' (site_id, list_key, list_item, extra, status) VALUES ' . implode( ',', $values );

	$ret = cerber_db_query( $query );

	if ( $e = cerber_db_get_errors() ) {
		nexus_diag_log( $e );
	}

	return $ret;
}

function nexus_get_list( $site_id, $key = '', $fields = array(), $type = MYSQLI_ASSOC ) {

	list( $site_id, $key, $table_fields ) = nexus_sanitize( $site_id, $key, $fields );

	if ( empty( $site_id ) || empty( $key ) ) {
		return false;
	}

	$where = nexus_make_where( $site_id, $key );

	$sql_fields = ( $table_fields ) ? implode( ',', $fields ) : '*';

	return cerber_db_get_results( 'SELECT ' . $sql_fields . ' FROM ' . cerber_get_db_prefix() . CERBER_MS_LIST_TABLE . ' WHERE ' . $where, $type );
}

function nexus_delete_list( $site_id, $key = '' ) {

	list( $site_id, $key ) = nexus_sanitize( $site_id, $key );

	if ( empty( $site_id ) ) {
		return false;
	}

	$where = nexus_make_where( $site_id, $key );

	return cerber_db_query( 'DELETE FROM ' . cerber_get_db_prefix() . CERBER_MS_LIST_TABLE . ' WHERE ' . $where );
}

function nexus_make_where( $site_id, $key = '' ) {
	$where = ' site_id = ' . $site_id;
	if ( $key ) {
		$where .= ' AND list_key = "' . $key . '" ';
	}

	return $where;
}

function nexus_sanitize( $site_id, $key = '', $fields = array() ) {
	$ret = array( 0, '', array() );

	if ( ! $ret[0] = absint( $site_id ) ) {
		return $ret;
	}

	if ( $key ) {
		if ( ! preg_match( '/^[\w\-]+$/', $key ) ) {
			return $ret;
		}
		$ret[1] = $key;
	}

	if ( $fields ) {
		$ret[2] = array_filter( $fields, function ( $val ) {
			if ( preg_match( '/^[\w]+$/', $val ) ) {
				return true;
			}

			return false;
		} );
	}

	return $ret;
}

function nexus_create_db( $role ) {
	global $wpdb;

	if ( 'utf8mb4' === $wpdb->charset || ( ! $wpdb->charset && $wpdb->has_cap( 'utf8mb4' ) ) ) {
		$charset = 'utf8mb4';
		$collate = 'utf8mb4_unicode_ci';
	}
	else {
		$charset = 'utf8';
		$collate = 'utf8_general_ci';
	}

	$sql = array();
	if ( ! cerber_is_table( cerber_get_db_prefix() . CERBER_MS_TABLE ) ) {
		$sql[] = '
			CREATE TABLE IF NOT EXISTS ' . cerber_get_db_prefix() . CERBER_MS_TABLE . ' (
			id int(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			site_name varchar(250) NOT NULL,
			site_name_remote varchar(250) NOT NULL,
			site_url varchar(250) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
			group_id int(10) UNSIGNED NOT NULL DEFAULT 0,
			plugin_v varchar(250) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT "",
			wp_v varchar(250) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT "",
			last_scan int(10) UNSIGNED NOT NULL DEFAULT 0,
			updates int(10) UNSIGNED NOT NULL DEFAULT 0,
			last_http int(10) UNSIGNED NOT NULL DEFAULT 0,
			refreshed int(10) UNSIGNED NOT NULL DEFAULT 0,
			x_field varchar(250) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
			x_num int(10) UNSIGNED NOT NULL,
			site_echo varchar(250) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
			site_pass varchar(250) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
			details text NOT NULL,
			site_notes text NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY site_url (site_url),
			KEY group_id (group_id),
			KEY refreshed (refreshed)			
			) DEFAULT CHARSET='.$charset.' COLLATE='.$collate.' COMMENT="Keep it secret";
        ';
	}

	if ( ! cerber_is_table( cerber_get_db_prefix() . CERBER_MS_LIST_TABLE ) ) {
		$sql[] = '
			CREATE TABLE IF NOT EXISTS ' . cerber_get_db_prefix() . CERBER_MS_LIST_TABLE . ' (
            site_id bigint(20) UNSIGNED NOT NULL,
            list_key varchar(250) CHARACTER SET ascii NOT NULL,
            list_item varchar(250) NOT NULL,
            extra varchar(250) NOT NULL DEFAULT "",
            status char(1) CHARACTER SET ascii NOT NULL DEFAULT "",
            updated int(10) UNSIGNED NOT NULL DEFAULT 0,
    		KEY site_id (site_id) USING HASH,
			KEY the_key (list_key) USING BTREE
			) DEFAULT CHARSET='.$charset.' COLLATE='.$collate.' COMMENT="";
        ';
	}

	foreach ( $sql as $query ) {
		cerber_db_query( $query );
	}

	if ( $e = cerber_db_get_errors( true ) ) {
		cerber_admin_notice( $e );

		return false;
	}

	return nexus_upgrade_db();
}

function nexus_upgrade_db( $force = false ) {

	$sql = array();

	if ( $force || ! cerber_is_column( cerber_get_db_prefix() . CERBER_MS_TABLE, 'server_id' ) ) {
		$sql[] = 'ALTER TABLE ' . cerber_get_db_prefix() . CERBER_MS_TABLE . '
		 ADD server_id VARCHAR(64) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT "" AFTER group_id, 
		 ADD INDEX server (server_id),
         ADD server_country CHAR(3) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT "" AFTER server_id, 
         ADD INDEX country (server_country);
		 ';
	}

	if ( $force || ! cerber_is_column( cerber_get_db_prefix() . CERBER_MS_TABLE, 'site_key' ) ) {
		$sql[] = 'ALTER TABLE ' . cerber_get_db_prefix() . CERBER_MS_TABLE . '
		 ADD site_key INT(11) UNSIGNED NOT NULL DEFAULT "0" AFTER plugin_v;
		 ';
	}

	foreach ( $sql as $query ) {
		cerber_db_query( $query );
	}

	if ( $e = cerber_db_get_errors( true ) ) {
		cerber_admin_notice( $e );

		return false;
	}

	return true;
}
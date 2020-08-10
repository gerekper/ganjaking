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

if ( ! class_exists( 'Plugin_Upgrader' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
}
/**
 * Makes array of human readable update report
 *
 * @param array $data
 * @param bool $display_errors
 *
 * @return array String to log or to display
 */
function cerber_flat_results( $data = array(), $display_errors = false ) {
	$ret = array();

	if ( ! is_array( $data ) || empty( $data ) ) {
		return $ret;
	}

	foreach ( $data as $key => $item ) {
		if ( $item['info'] ) {

		}
		if ( $item['status'] ) {
			$ret = array_merge( $ret, $item['status'] );
		}
		if ( $item['errors'] ) {
			foreach ( $item['errors'] as $err ) {
				$e    = $err[1];
				$more = crb_array_get( $err, 2 );
				if ( $more ) {
					$e .= '(' . $more . ')';
				}
				$ret[] = 'An error occurred while updating ' . $item['info']['Name'] . ': ' . $e;
				if ( $err[0] == 'no_update' ) {
					continue;
				}
				if ( $display_errors ) {
					cerber_admin_notice( 'An error occurred while updating ' . $item['info']['Name'] . ': ' . $e );
				}
			}
		}
	}

	return $ret;
}

function cerber_update_plugin( $plugin_id = '' ) {
	static $silent_skin, $upgrader;

	$ret = array( 'status' => array(), 'errors' => array() );

	$errors = array();

	crb_is_task_permitted( true );

	ob_start();

	$fs = cerber_init_wp_filesystem();
	if ( is_wp_error( $fs ) ) {
		$code = $fs->get_error_code();

		$ret['errors'] = array(
			array(
				$code,
				$fs->get_error_message( $code ),
				$fs->get_error_data( $code )
			)
		);

		$junk = ob_get_clean();
		return $ret;
	}

	$plugins = get_plugins();
	$ret['status'][] = 'Upgrading ' . $plugins[ $plugin_id ]['Name'] . ' ' . $plugins[ $plugin_id ]['Version'];

	if ( ! is_object( $silent_skin ) ) {
		$silent_skin = new CRB_Upgrader_Skin();
	}
	if ( ! is_object( $upgrader ) ) {
		$upgrader = new CRB_Plugin_Upgrader( $silent_skin );
	}

	$result = $upgrader->upgrade( $plugin_id );

	$junk = ob_get_clean(); // should be empty

	if ( ! $result ) {
		$errors [] = array( 'upgrade-unknown', 'Unknown file error' );
	}
	elseif ( is_wp_error( $result ) ) {
		foreach ( $result->get_error_codes() as $code ) {
			$errors [] = array(
				$code,
				$result->get_error_message( $code ),
				$result->get_error_data( $code )
			);
		}
	}

	$ret['errors'] = $errors;
	$ret['status'] = array_merge( $ret['status'], $silent_skin->the_status );

	$plugins = get_plugins();
	$ret['info'] = $plugins[ $plugin_id ];

	return $ret;
}

/**
 * Class CRB_Plugin_Upgrader
 * Installs the latest version of the plugin, remove other actions, provides more info in case of error
 *
 */
class CRB_Plugin_Upgrader extends Plugin_Upgrader {

	private $flushed = false;

	public function upgrade( $plugin, $args = array() ) {

		$this->download_update_info(); // Use fresh data from wordpress.org

		$defaults = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->upgrade_strings();

		$wp_org = get_site_transient( 'update_plugins' );
		if ( isset( $wp_org->response[ $plugin ] ) ) {
			$url = $wp_org->response[ $plugin ]->package;
		}
		else {
			return new WP_Error( 'no_update', 'No newer version found' );
		}

		// No other default updates are allowed!
		remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );
		remove_action( 'upgrader_process_complete', 'wp_version_check' );
		remove_action( 'upgrader_process_complete', 'wp_update_plugins' );
		remove_action( 'upgrader_process_complete', 'wp_update_themes' );

		// In the background update it will deactivate the plugin
		// add_filter('upgrader_pre_install', array($this, 'deactivate_plugin_before_upgrade'), 10, 2);

		add_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ), 10, 4 );
		//'source_selection' => array($this, 'source_selection'), //there's a trac ticket to move up the directory for zip's which are made a bit differently, useful for non-.org plugins.
		if ( $parsed_args['clear_update_cache'] ) {
			// Clear cache so wp_update_plugins() knows about the new plugin.
			add_action( 'upgrader_process_complete', 'wp_clean_plugins_cache', 9, 0 );
		}

		$run_result = $this->run( array(
			'package'           => $url,
			'destination'       => WP_PLUGIN_DIR,
			'clear_destination' => true,
			'clear_working'     => true,
			'hook_extra'        => array(
				'plugin' => $plugin,
				'type'   => 'plugin',
				'action' => 'update',
			),
		) );

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_action( 'upgrader_process_complete', 'wp_clean_plugins_cache', 9 );
		remove_filter( 'upgrader_pre_install', array( $this, 'deactivate_plugin_before_upgrade' ) );
		remove_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ) );

		if ( is_wp_error( $run_result ) ) { // Typically filesystem errors

			return $run_result; // not the same as $this->result  (who knows why)
		}

		if ( ! $this->result || is_wp_error( $this->result ) ) {

			return $this->result;
		}

		// Force refresh of plugin update information
		wp_clean_plugins_cache( $parsed_args['clear_update_cache'] );

		return true;
	}

	private function download_update_info() {
		if ( ! $this->flushed ) {
			delete_site_transient( 'update_plugins' );
			wp_update_plugins();
		}
		$this->flushed = true; // Update once in case of bulk update
	}

}

class CRB_Upgrader_Skin extends WP_Upgrader_Skin {
	public $the_status = array();

	public function __construct() { // simply avoid parsing unnecessary parameters
		$defaults      = array( 'url' => '', 'nonce' => '', 'title' => '', 'context' => false );
		$this->options = $defaults;
	}

	/*
     * Saves results for further usage instead of flushing it to a user browser
	 *
	 */
	public function feedback( $string, ...$args ) { // Variadic functions requires PHP 5.6
		if ( isset( $this->upgrader->strings[ $string ] ) ) {
			$string = $this->upgrader->strings[ $string ];
		}

		if ( strpos( $string, '%' ) !== false ) {
			if ( $args ) {
				$args   = array_map( 'strip_tags', $args );
				$args   = array_map( 'esc_html', $args );
				$string = vsprintf( $string, $args );
			}
		}
		if ( empty( $string ) ) {
			return;
		}
		//show_message($string); No flush!
		$this->the_status[] = $string;
	}

	public function header() {
		// No output
	}

	public function footer() {
		// No output
	}
}
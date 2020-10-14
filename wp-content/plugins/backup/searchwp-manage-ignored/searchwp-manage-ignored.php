<?php
/*
Plugin Name: SearchWP Manage Ignored
Plugin URI: https://searchwp.com/
Description: Un-ignore ignored queries for SearchWP stats
Version: 1.0.0
Author: SearchWP, LLC
Author URI: https://searchwp.com/

Copyright 2015-2017 Jonathan Christopher

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SEARCHWP_MANAGE_IGNORED_VERSION' ) ) {
	define( 'SEARCHWP_MANAGE_IGNORED_VERSION', '1.0.0' );
}

/**
 * Implement updater
 *
 * @return bool|SWP_Manage_Ignored_Updater
 */
function searchwp_manage_ignored_update_check() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return false;
	}

	// environment check
	if ( ! defined( 'SEARCHWP_PREFIX' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_EDD_STORE_URL' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_MANAGE_IGNORED_VERSION' ) ) {
		return false;
	}

	if ( ! class_exists( 'SWP_Manage_Ignored_Updater' ) ) {
		// load our custom updater
		include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
	}

	// retrieve stored license key
	$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
	$license_key = sanitize_text_field( $license_key );

	// instantiate the updater to prep the environment
	$searchwp_manage_ignored_updater = new SWP_Manage_Ignored_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 90557,
			'version'   => SEARCHWP_MANAGE_IGNORED_VERSION,
			'license'   => $license_key,
			'item_name' => 'Manage Ignored',
			'author'    => 'Jonathan Christopher',
			'url'       => site_url(),
		)
	);

	return $searchwp_manage_ignored_updater;
}

add_action( 'admin_init', 'searchwp_manage_ignored_update_check' );

class SearchWPManageIgnored {

	// required for all SearchWP extensions
	public $public                = true;
	public $slug                  = 'manageignored';
	public $name                  = 'Manage Ignored';
	public $min_searchwp_version  = '2.5';

	// unique to this extension
	private $url;
	private $prefix     = 'swp_manageignored_';

	function __construct() {
		$this->url = plugins_url( 'searchwp-manage-ignored' );

		// SearchWP Hooks
		add_filter( 'searchwp_extensions', array( $this, 'register' ), 10 );
	}

	function register( $extensions ) {
		$extensions['ManageIgnored'] = __FILE__;

		return $extensions;
	}

	function maybe_unignore_queries() {

		if ( ! defined( 'SEARCHWP_PREFIX' ) ) {
			wp_die( 'Invalid request' );
		}

		if ( isset( $_REQUEST['searchwp_manageignored_action'] ) && 'unignore' == $_REQUEST['searchwp_manageignored_action']
		    && isset( $_REQUEST['searchwp_manageignored_nonce'] ) && wp_verify_nonce( $_REQUEST['searchwp_manageignored_nonce'], 'searchwp_manageignored' ) ) {
			if ( isset( $_REQUEST['swp_unignore'] ) && ! empty( $_REQUEST['swp_unignore'] ) && is_array( $_REQUEST['swp_unignore'] ) ) {
				$queries_to_unignore = $_REQUEST['swp_unignore'];
				$queries_to_unignore = array_map( 'sanitize_text_field', $queries_to_unignore );

				// grab the user's ignored queries
				$ignored_queries = get_user_meta( get_current_user_id(), SEARCHWP_PREFIX . 'ignored_queries', true );

				// remove unignored queries
				$updated = false;
				foreach ( $queries_to_unignore as $query_to_unignore ) {
					if ( isset( $ignored_queries[ $query_to_unignore ] ) ) {
						$updated = true;
						unset( $ignored_queries[ $query_to_unignore ] );
					}
				}

				// store the update
				if ( $updated ) {
					update_user_meta( get_current_user_id(), SEARCHWP_PREFIX . 'ignored_queries', $ignored_queries );
				}
			}
		}
	}

	function view() {

		global $wpdb;

		if ( ! defined( 'SEARCHWP_PREFIX' ) ) {
			wp_die( 'Invalid request' );
		}

		$prefix = $wpdb->prefix;

		$this->maybe_unignore_queries();

		$nonce = wp_create_nonce( 'searchwp_manageignored' );
		$ignored_queries = get_user_meta( get_current_user_id(), SEARCHWP_PREFIX . 'ignored_queries', true );
		?>
		<h3><?php _e( 'Your ignored queries', 'searchwp' ); ?></h3>
		<p><?php _e( 'These ignored queries are unique to your User account', 'searchwp' ); ?></p>
		<form action="options-general.php" method="get" id="swp-manage-ignored">
			<input type="hidden" name="page" value="searchwp" />
			<input type="hidden" name="tab" value="extensions" />
			<input type="hidden" name="extension" value="manageignored" />
			<input type="hidden" name="nonce" value="<?php echo isset( $_GET['nonce'] ) ? esc_attr( $_GET['nonce'] ) : ''; ?>" />
			<input type="hidden" name="searchwp_manageignored_action" value="unignore" />
			<input type="hidden" name="searchwp_manageignored_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
			<table class="form-table">
				<tbody>
				<tr valign="top">
					<th scope="row">
						<label><?php _e( 'Ignored Queries', 'searchwp_manageignored' ); ?></label>
					</th>
					<td>
						<?php if ( ! is_array( $ignored_queries ) || empty( $ignored_queries ) ) : ?>
							<p><?php _e( 'No ignored queries', 'searchwp' ); ?></p>
						<?php else : ?>
							<?php foreach ( $ignored_queries as $ignored_query ) : ?>
								<?php
									// ignored queries are stored as md5 hashes so we need to reverse lookup
									$query_hash = sanitize_text_field( $ignored_query );
									$ignore_sql = $wpdb->prepare( "SELECT {$prefix}swp_log.query, md5( {$prefix}swp_log.query ) FROM {$prefix}swp_log WHERE md5( {$prefix}swp_log.query ) = %s", $query_hash );
									$query_to_ignore = $wpdb->get_var( $ignore_sql );
								?>
								<p>
									<input type="checkbox" value="<?php echo esc_attr( $query_hash ); ?>" name="swp_unignore[]" id="swp<?php echo esc_attr( $query_hash ); ?>" />
									<label for="swp<?php echo esc_attr( $query_hash ); ?>">
										<?php if ( ! empty( $query_to_ignore ) ) : ?>
											<?php echo esc_html( $query_to_ignore ); ?>
										<?php else : ?>
											<em>Ignored query not found</em>
										<?php endif; ?>
									</label>
								</p>
							<?php endforeach; ?>
						<?php endif; ?>
					</td>
				</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button" value="<?php _e( 'Unignore Checked Queries', 'searchwp' ); ?>">
			</p>
		</form>
		<?php
	}

}

new SearchWPManageIgnored();

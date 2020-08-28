<?php
/*
Plugin Name: SearchWP Give Integration
Plugin URI: https://searchwp.com/extensions/give-integration/
Description: Integrates SearchWP with Give
Version: 1.1.0
Author: SearchWP
Author URI: https://searchwp.com/

Copyright 2018-2020 SearchWP

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

if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
	add_action( 'admin_notices', 'searchwp_give_below_php_version_notice' );
	return;
}

/**
 * Show an error to sites running PHP < 5.4
 *
 * @since 1.0.1
 */
function searchwp_give_below_php_version_notice() {
	// Translators: this message outputs a minimum PHP requirement.
	echo '<div class="error"><p>' . esc_html( sprintf( __( 'Your version of PHP (%s) is below the minimum version of PHP required by SearchWP Give Integration (5.6). Please contact your host and request that your version be upgraded to 5.6 or later.', 'searchwp' ), PHP_VERSION ) ) . '</p></div>';
}

if ( ! defined( 'SEARCHWP_GIVE_VERSION' ) ) {
	define( 'SEARCHWP_GIVE_VERSION', '1.1.0' );
}

/**
 * Instantiate the updater
 */
if ( ! class_exists( 'SWP_Give_Updater' ) ) {
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

// Set up the updater
function searchwp_give_update_check() {

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

	// SearchWP 4 compat.
	if ( class_exists( '\\SearchWP\\License' ) ) {
		$license_key = \SearchWP\License::get_key();
	} else {
		$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
		$license_key = sanitize_text_field( $license_key );
	}

	// Instantiate the updater to prep the environment
	$searchwp_give_updater = new SWP_Give_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 152587,
			'version'   => SEARCHWP_GIVE_VERSION,
			'license'   => $license_key,
			'item_name' => 'Give Integration',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_give_updater;
}

add_action( 'admin_init', 'searchwp_give_update_check' );

class SearchWP_Give_Integration {

	private $post_type = 'give_forms';

	function __construct() {
		add_action( 'searchwp_indexer_pre',       array( $this, 'prevent_give_metadata_hijack' ) );
		add_filter( 'searchwp_custom_field_keys', array( $this, 'retrieve_give_formmeta_keys' ) );
		add_filter( 'searchwp_get_custom_fields', array( $this, 'retrieve_form_metadata' ), 10, 2 );

		add_action( 'save_post', array( $this, 'purge_form_from_index' ), 999999 );
	}

	/*
	 * Forcefully purge the post from the index when it's safe e.g. once Give has finished
	 * hijacking meta save requests, we purge the form from the index as well
	 *
	 * @since 1.0
	 */
	function purge_form_from_index( $post_id ) {
		if ( function_exists( 'SWP' ) && $this->post_type === get_post_type( $post_id ) ) {
			add_action( 'shutdown', function( $post_id ) {
				$this->prevent_give_metadata_hijack();
				SWP()->purge_post( $post_id, true );
				SWP()->trigger_index();
			} );
		}
	}

	/**
	 * Give uses custom database tables and as part of the implementation intercepts
	 * all interaction with postmeta for Give Form IDs, but we don't want that to happen
	 * so during indexing we're going to remove all of those hooks
	 *
	 * @since 1.0
	 */
	function prevent_give_metadata_hijack() {
		remove_all_filters( 'add_post_metadata' );
		remove_all_filters( 'update_post_metadata' );
		remove_all_filters( 'get_post_metadata' );
		remove_all_filters( 'delete_post_metadata' );
	}

	/**
	 * SearchWP's indexer is built on the premise that all Custom Fields are retrieved and then
	 * indexed a la carte. In the case of Give there is no metadata associated because of its
	 * abstraction into a custom database table, so this function retrieves applicable metadata.
	 *
	 * @since 1.0
	 */
	function retrieve_form_metadata( $metadata, $post_id ) {
		if ( $this->post_type !== get_post_type( $post_id ) ) {
			return $metadata;
		}

		$meta_keys = array();

		foreach ( SWP()->settings['engines'] as $engine => $engine_config ) {
			if ( ! array_key_exists( $this->post_type, $engine_config ) ) {
				continue;
			}

			foreach ( $engine_config as $post_type => $post_type_config ) {
				$meta_keys = array_merge(
					$meta_keys,
					array_values( wp_list_pluck(
						$engine_config[ $this->post_type ]['weights']['cf'],
						'metakey'
					) )
				);
			}
		}

		if ( ! empty( $meta_keys ) ) {
			$give_meta = new Give_DB_Form_Meta();
			foreach ( $meta_keys as $meta_key ) {
				$metadata[ $meta_key ] = $give_meta->get_meta( $post_id, $meta_key, false );
			}
		}

		return $metadata;
	}

	/**
	 * Retrieves all unqiue formmeta keys as per Give's custom database table
	 *
	 * @since 1.0
	 */
	function retrieve_give_formmeta_keys( $keys ) {
		global $wpdb;

		$meta_keys = $wpdb->get_col( $wpdb->prepare( "
			SELECT meta_key
			FROM $wpdb->formmeta
			WHERE meta_key != %s
			AND meta_key != %s
			AND meta_key != %s
			AND meta_key != %s
			AND meta_key NOT LIKE %s
			GROUP BY meta_key
		",
			'_' . SEARCHWP_PREFIX . 'indexed',
			'_' . SEARCHWP_PREFIX . 'content',
			'_' . SEARCHWP_PREFIX . 'needs_remote',
			'_' . SEARCHWP_PREFIX . 'skip',
			'_oembed_%'
		) );

		return array_merge( array_unique( $meta_keys ), $keys );
	}
}

new SearchWP_Give_Integration();

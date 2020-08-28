<?php
/*
Plugin Name: SearchWP Enable Media Replace
Plugin URI: https://searchwp.com/extensions/enable-media-replace
Description: Adds support for Enable Media Replace
Version: 1.1.0
Author: SearchWP
Author URI: https://searchwp.com/

Copyright 2017-2020 SearchWP

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

if ( ! defined( 'SEARCHWP_EMR_VERSION' ) ) {
	define( 'SEARCHWP_EMR_VERSION', '1.1.0' );
}

/**
 * Instantiate the updater
 */
if ( ! class_exists( 'SWP_EMR_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}


/**
 * @return bool|SWP_EMR_Updater
 */
function searchwp_emr_update_check() {

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

	// instantiate the updater to prep the environment
	$searchwp_emr_updater = new SWP_EMR_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 88188,
			'version'   => SEARCHWP_EMR_VERSION,
			'license'   => $license_key,
			'item_name' => 'Enable Media Replace',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_emr_updater;
}

add_action( 'admin_init', 'searchwp_emr_update_check' );

/**
 * Class SearchWP_EMR_Integration
 */
class SearchWP_EMR_Integration {

	/**
	 * SearchWP_EMR_Integration constructor.
	 */
	function __construct() {
		// EMR: Just replace the file
		// This is a hack because this setting generates no metadata changes, so the update_post_meta action never fires
		add_filter( 'update_post_metadata', array( $this, 'purge_post_via_update_post_metadata' ), 999, 5 );

		// EMR: Replace the file, use file name and update all links
		add_action( 'update_post_meta', array( $this, 'purge_post_via_edit_meta' ), 999, 4 );
	}

	/**
	 * There are certain meta keys we don't want to consider, else we'd be constantly purging posts
	 *
	 * @param $meta_key
	 *
	 * @return bool
	 */
	function maybe_skip_meta_key( $meta_key ) {
		return in_array( $meta_key, array(
			'_edit_lock',
			'_edit_last',
			'_wp_old_slug',
			'_searchwp_attempts',
			'_searchwp_skip',
			'_searchwp_review',
			'_searchwp_last_index',
			'searchwp_content',
			'searchwp_pdf_metadata',
		), true );
	}

	/**
	 * Callback for update_post_metadata filter
	 *
	 * @param $return
	 * @param $object_id
	 * @param $meta_key
	 * @param $meta_value
	 * @param $prev_value
	 *
	 * @return mixed
	 * @internal param $meta_id
	 * @internal param $_meta_value
	 *
	 */
	function purge_post_via_update_post_metadata( $return, $object_id, $meta_key, $meta_value, $prev_value ) {

		if ( $this->maybe_skip_meta_key( $meta_key ) ) {
			return $return;
		}

		remove_filter( 'update_post_metadata', array( $this, 'purge_post_via_update_post_metadata' ), 999, 5 );

		$this->purge_post_via_edit_meta( 0, $object_id, $meta_key, $meta_value );

		return $return;
	}

	/**
	 * Purge a post from the index when its metadata is edited
	 *
	 * @param $meta_id
	 * @param $object_id
	 * @param $meta_key
	 * @param $_meta_value
	 */
	function purge_post_via_edit_meta( $meta_id, $object_id, $meta_key, $_meta_value ) {

		if ( $this->maybe_skip_meta_key( $meta_key ) ) {
			return;
		}

		// Prevent redundancy; this hook is fired for each meta record for a post
		remove_action( 'update_post_meta', array( $this, 'purge_post_via_edit_meta' ), 999, 4 );

		delete_post_meta( $object_id, SEARCHWP_PREFIX . 'content' );
		delete_post_meta( $object_id, SEARCHWP_PREFIX . 'pdf_metadata' );

		// We need to manually force the purge of this post because EMR does wp_redirect() after upload
		if ( function_exists( 'SWP' ) && ! isset( SWP()->purgeQueue[ $object_id ] ) ) {
			SWP()->purgeQueue[ $object_id ] = $object_id;
			do_action( 'searchwp_log', 'purge_post_via_edit_meta() ' . $object_id );
			SWP()->setup_purge_queue();
		}

		if ( class_exists( '\\SearchWP\\Index\\Controller' ) ) {
			$post = get_post( $object_id );
			$index = new SearchWP\Index\Controller();
			$source = $index->get_source_by_name( 'post' . SEARCHWP_SEPARATOR . $post->post_type );

			if ( ! is_wp_error( $source ) ) {
				$index->drop( $source, $post->ID, true );
			}
		}
	}

}

new SearchWP_EMR_Integration();

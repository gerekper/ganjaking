<?php
/*
Plugin Name: SearchWP PrivateContent Integration
Plugin URI: https://searchwp.com/extensions/privatecontent-integration/
Description: Integrate SearchWP and PrivateContent
Version: 1.3.0
Author: SearchWP
Author URI: https://searchwp.com/

Copyright 2013-2020 SearchWP

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

if ( ! defined( 'SEARCHWP_PRIVATECONTENT_VERSION' ) ) {
	define( 'SEARCHWP_PRIVATECONTENT_VERSION', '1.3.0' );
}

/**
 * instantiate the updater
 */
if ( ! class_exists( 'SWP_PrivateContent_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

// set up the updater
function searchwp_privatecontent_update_check() {

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
	$searchwp_privatecontent_updater = new SWP_PrivateContent_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 33250,
			'version'   => SEARCHWP_PRIVATECONTENT_VERSION,
			'license'   => $license_key,
			'item_name' => 'PrivateContent Integration',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_privatecontent_updater;
}

add_action( 'admin_init', 'searchwp_privatecontent_update_check' );

class SearchWP_PrivateContent {

	function __construct() {
		add_action( 'searchwp_indexer_pre', array( $this, 'indexer_pre' ) );
		add_filter( 'searchwp_include', array( $this, 'limit_results_to_privatecontent_access' ), 10, 3 );
		add_filter( 'searchwp_live_search_query_args', array( $this, 'searchwp_live_ajax_search_args' ) );

		// SearchWP 4.0 compat.
		add_filter( 'searchwp\query\mods', array( $this, 'add_mods' ), 10, 2 );
	}

	function add_mods( $mods, $query ) {
		// We need to loop through all post types and add a Mod for each.
		foreach ( $query->get_engine()->get_sources() as $source ) {
			$flag = 'post' . SEARCHWP_SEPARATOR;

			if ( 0 !== strpos( $source->get_name(), $flag ) ) {
				continue;
			}

			$mod = new \SearchWP\Mod( $source );
			$mod->raw_where_sql( function( $runtime_mod, $args ) {
				$ids = $this->limit_results_to_privatecontent_access( array(), null, null );
				$ids = array_map( 'absint', (array) $ids );

				return "{$runtime_mod->get_foreign_alias()}.id IN (" . implode( ',', $ids ) . ')';
			} );

			$mods[] = $mod;
		}

		return $mods;
	}

	function searchwp_live_ajax_search_args( $args ) {
		if ( empty( $args['post__in'] ) ) {
			$args['post__in'] = array();
		}

		$engine = isset( $_REQUEST['swpengine'] ) ? sanitize_text_field( $_REQUEST['swpengine'] ) : 'default';
		$query = isset( $_REQUEST['swpquery'] ) ? sanitize_text_field( $_REQUEST['swpquery'] ) : $args['s'];
		$args['post__in'] = $this->limit_results_to_privatecontent_access( $args['post__in'], $engine, $query );

		return $args;
	}

	function indexer_pre() {
		remove_filter( 'pre_get_posts', 'pg_query_filter', 999 );
	}

	function limit_results_to_privatecontent_access( $ids, $engine, $terms ) {

		if ( ! empty( $engine ) ) {
			$engine = null;
		}

		if ( ! empty( $terms ) ) {
			$terms = null;
		}

		$privatecontent_taxonomy = 'pg_user_categories';

		// retrieve the user record of the logged in user
		$privatecontent_user_obj = function_exists( 'pg_user_logged' ) ? pg_user_logged() : false;

		// determine which categories the current user DOES have access to
		$access_categories = isset( $privatecontent_user_obj->categories ) ? maybe_unserialize( $privatecontent_user_obj->categories ) : array( 0 );
		$access_categories = array_map( 'absint', $access_categories );

		// determine which categories the current user does NOT have access to
		$privatecontent_categories = get_terms( $privatecontent_taxonomy,
			array(
				'fields' => 'ids',
				'hide_empty' => false,
			)
		);
		$no_access_categories = array_diff( $privatecontent_categories, $access_categories );
		$no_access_categories = array_map( 'absint', $no_access_categories );

		// Retrieve post IDs that the user DOES have access to
		if ( count( $no_access_categories ) ) {
			$args  = array(
				'post_type' => 'any',
				'fields'    => 'ids',
				'nopaging'  => true,
				'tax_query' => array(
					array(
						'taxonomy' => sanitize_text_field( $privatecontent_taxonomy ),  // the PrivateContent taxonomy
						'field'    => 'id',
						'terms'    => $no_access_categories,
						'operator' => 'NOT IN',
					),
				),
			);
			$query = new WP_Query( $args );
			$ids   = array_merge( (array) $ids, $query->posts );
			$ids   = array_map( 'absint', $ids );
			$ids   = array_unique( $ids );
		}

		// Because Media has a different post_status we need to repeat, and we don't want to open up 'any' post_type to 'inherit' so
		// while this is mega redundant it is okay it's just source code no one will mind
		if ( count( $no_access_categories ) ) {
			$args  = array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
				'fields'      => 'ids',
				'nopaging'    => true,
				'tax_query'   => array(
					array(
						'taxonomy' => sanitize_text_field( $privatecontent_taxonomy ),  // the PrivateContent taxonomy
						'field'    => 'id',
						'terms'    => $no_access_categories,
						'operator' => 'NOT IN',
					),
				),
			);
			$query = new WP_Query( $args );
			$ids   = array_merge( (array) $ids, $query->posts );
			$ids   = array_map( 'absint', $ids );
			$ids   = array_unique( $ids );
		}

		return $ids;
	}

}

new SearchWP_PrivateContent();

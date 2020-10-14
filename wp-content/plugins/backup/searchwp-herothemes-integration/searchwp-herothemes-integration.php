<?php
/*
Plugin Name: SearchWP HeroThemes Integration
Plugin URI: https://searchwp.com/
Description: SearchWP compatibility with Herothemes
Version: 1.2.0
Author: SearchWP
Author URI: https://searchwp.com/

Copyright 2016-2020 SearchWP

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

if ( ! defined( 'SEARCHWP_HEROTHEMES_VERSION' ) ) {
	define( 'SEARCHWP_HEROTHEMES_VERSION', '1.2.0' );
}

/**
 * Instantiate the updater
 */
if ( ! class_exists( 'SWP_Herothemes_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

/**
 * Set up the updater
 *
 * @return bool|SWP_Herothemes_Updater
 */
function searchwp_herothemes_update_check(){

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
	$searchwp_herothemes_updater = new SWP_Herothemes_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 63360,
			'version'   => SEARCHWP_HEROTHEMES_VERSION,
			'license'   => $license_key,
			'item_name' => 'HeroThemes Integration',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_herothemes_updater;
}

add_action( 'admin_init', 'searchwp_herothemes_update_check' );

/**
 * Class SearchWP_Herothemes_Integration
 */
class SearchWP_Herothemes_Integration {

	/**
	 * SearchWP_Herothemes_Integration constructor.
	 */
	function __construct() {}

	function init() {
		add_filter( 'pre_get_posts', array( $this, 'knowall_compat' ), 5 );

		add_filter( 'searchwp_exclude', array( $this, 'tax_restriction' ), 10, 3 );
	}

	function tax_restriction( $ids, $engine, $terms ) {

		if ( is_user_logged_in() || ! function_exists( 'hkb_get_category_restrict_access_level' ) ) {
			return $ids;
		}

		$hkb_master_tax_terms = get_terms( array(
			'taxonomy'   => 'ht_kb_category',
			'orderby'    => 'term_order',
			'depth'      => 0,
			'child_of'   => 0,
			'hide_empty' => true,
		) );

		$excluded_categories = array();
		foreach ( $hkb_master_tax_terms as $term ){
			if ( 'loggedin' == hkb_get_category_restrict_access_level( $term ) ) {
				$excluded_categories[] = $term->term_id;
			}
		}

		if ( empty( $excluded_categories ) ) {
			return $ids;
		}

		// Retrieve the IDs of all the posts in this category.
		$excluded = get_posts( array(
			'post_type' => 'any',
			'tax_query' => array(
				array(
					'taxonomy' => 'ht_kb_category',
					'field'    => 'term_id',
					'terms'    => $excluded_categories
				)
			),
			'fields'   => 'ids',
			'nopaging' => true,
		) );

		if ( ! empty( $excluded ) ) {
			$ids = array_unique( array_merge( $ids, $excluded ) );
		}

		return $ids;
	}

	/**
	 * Allow SearchWP's filters to run uninterrupted
	 *
	 * @param $query
	 *
	 * @return mixed
	 */
	function knowall_compat( $query ) {

		// This conditional is in /knowall/functions.php@ht_knowall_pre_get_posts_filter()
		if (
			class_exists( 'SearchWP' )
			&& ! is_preview()
			&& ! is_singular()
			&& ! is_admin()
			&& (
				function_exists( 'ht_kb_is_ht_kb_search' )
				&& ht_kb_is_ht_kb_search()
			)
		) {

			// This filter is part of KnowAll and limits search results
			remove_filter( 'pre_get_posts', 'ht_knowall_pre_get_posts_filter', 20 );
		}

		return $query;
	}

}

$searchwp_herothemes = new SearchWP_Herothemes_Integration();
$searchwp_herothemes->init();

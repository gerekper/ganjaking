<?php
/*
Plugin Name: SearchWP Boolean Query
Plugin URI: https://searchwp.com/
Description: Implements support for boolean operators in searches (e.g. terms preceeded with a direct hyphen (no white space) or <code>NOT</code>)
Version: 1.4.1
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

if ( ! defined( 'SEARCHWP_BOOLEAN_VERSION' ) ) {
	define( 'SEARCHWP_BOOLEAN_VERSION', '1.4.1' );
}

/**
 * instantiate the updater
 */
if ( ! class_exists( 'SWP_Boolean_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

// set up the updater
function searchwp_boolean_update_check() {

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
	$searchwp_boolean_updater = new SWP_Boolean_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 33684,
			'version'   => SEARCHWP_BOOLEAN_VERSION,
			'license'   => $license_key,
			'item_name' => 'Boolean Search',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_boolean_updater;
}

add_action( 'admin_init', 'searchwp_boolean_update_check' );

class SearchWPBooleanQuery {

	public $excludeTerms = array();

	function __construct() {
		add_filter( 'searchwp_terms', array( $this, 'parse_search_query' ), 10, 1 );
		add_filter( 'searchwp_exclude', array( $this, 'exclude_excluded' ), 10, 1 );

		// SearchWP 4.0 compat.
		add_action( 'searchwp\query\mods', array( $this, 'exclude' ), 10, 2 );
	}


	function exclude( $mods, $query ) {
		global $wpdb;

		$this->parse_search_query( $query->get_keywords() );

		if ( ! is_array( $this->excludeTerms ) || empty( $this->excludeTerms ) ) {
			return $mods;
		}

		$tokens = new \SearchWP\Tokens( $this->excludeTerms );
		$token_ids = $tokens->map_index_ids();

		$index        = new \SearchWP\Index\Controller();
		$index_tables = $index->get_tables();
		$index_table  = $index_tables['index'];
		$index_table  = $index_table->table_name;

		$flag = 'post' . SEARCHWP_SEPARATOR;

		$mod = new \SearchWP\Mod();
		$mod->raw_where_sql( 'NOT EXISTS (' . $wpdb->prepare( "
			SELECT DISTINCT id
			FROM {$index_table}
			WHERE token IN (" . implode( ',', array_fill( 0, count( $token_ids ), '%d' ) ) . ")
			AND {$mod->get_foreign_alias()}.id = id
			AND SUBSTRING(source, 1, " . strlen( $flag ) . ") = %s
			AND site = %d",
			array_merge( array_keys( $token_ids ), array( $flag ), array( get_current_blog_id() ) )
		) . ')' );

		$mods[] = $mod;

		return $mods;
	}

	function exclude_excluded( $excluded ) {
		global $wpdb;

		$prefix = $wpdb->prefix . SEARCHWP_DBPREFIX;

		if ( is_array( $excluded ) && is_array( $this->excludeTerms ) && ! empty( $this->excludeTerms ) ) {

			$excludeTerms = implode( ',', $this->excludeTerms ); // prepared in parse_search_query()

			$excludeIDs = $wpdb->get_col(
				"SELECT {$prefix}index.post_id
				FROM {$prefix}index
				LEFT JOIN {$prefix}terms ON {$prefix}index.term = {$prefix}terms.id
				LEFT JOIN {$prefix}cf ON {$prefix}cf.post_id = {$prefix}index.post_id
				LEFT JOIN {$prefix}tax ON {$prefix}tax.post_id = {$prefix}index.post_id
				WHERE
				{$prefix}terms.term IN ({$excludeTerms})
				GROUP BY {$prefix}index.post_id" );

			$excluded = array_values( array_unique( array_merge( $excluded, $excludeIDs ) ) );
		}

		return $excluded;
	}

	function parse_search_query( $query ) {
		global $wpdb;

		// pluck out terms that are prefixed with a hyphen
		$terms = ( strpos( $query, ' ' ) !== false ) ? explode( ' ', $query ) : array( $query );
		$terms = array_map( 'trim', $terms );

		foreach ( $terms as $key => $term ) {
			$term = strtolower( trim( $term ) );
			if ( strlen( trim( $term ) ) > 1 && substr( $term, 0, 1 ) == '-' ) {
				$this->excludeTerms[] = $wpdb->prepare( '%s', str_replace( '-', '', $term ) );
				unset( $terms[ $key ] );
			}
		}

		// pluck out terms that come after 'not'
		$flag = false;
		foreach ( $terms as $key => $term ) {

			if ( $flag ) {
				// the preceeding term was NOT, so store this one
				$this->excludeTerms[] = $wpdb->prepare( '%s', str_replace( '-', '', $term ) );
				unset( $terms[ $key ] );

				// reset the flag
				$flag = false;
			}

			// SearchWP <2.6.1 forced lowercase early
			if ( 'not' == strtolower( $term ) ) {
				unset( $terms[ $key ] );
				$flag = true;
			}
		}

		$query = implode( ' ', $terms );

		return $query;
	}
}

new SearchWPBooleanQuery();

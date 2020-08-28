<?php
/*
Plugin Name: SearchWP Co-Authors Plus Integration
Plugin URI: https://searchwp.com/
Description: Integrate Co-Authors Plus author information with SearchWP
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

if ( ! defined( 'SEARCHWP_COAUTHORSPLUS_VERSION' ) ) {
	define( 'SEARCHWP_COAUTHORSPLUS_VERSION', '1.2.0' );
}

/**
 * instantiate the updater
 */
if ( ! class_exists( 'SWP_CoAuthorsPlus_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

// set up the updater
function searchwp_coauthorsplus_update_check() {

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
	$searchwp_boolean_updater = new SWP_CoAuthorsPlus_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 54834,
			'version'   => SEARCHWP_COAUTHORSPLUS_VERSION,
			'license'   => $license_key,
			'item_name' => 'Co-Authors Plus Integration',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_boolean_updater;
}

add_action( 'admin_init', 'searchwp_coauthorsplus_update_check' );

class SearchWPCoAuthorsPlus {

	private $fields     = array();
	private $extra_meta = array();

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		// Define which Author fields to index
		$this->fields = apply_filters( 'searchwp_coauthorsplus_author_fields', array(
			'user_nicename',
			'display_name',
			'nickname',
			'first_name',
			'last_name',
			'description',
		) );

		$this->fields = array_map( 'sanitize_key', $this->fields );

		add_filter( 'searchwp_extra_metadata', array( $this, 'retrieve_coauthor_metadata' ), 10, 2 );
		add_filter( 'searchwp_custom_field_keys', array( $this, 'searchwp_custom_field_keys' ), 10, 2 );

		add_filter( 'searchwp\entry\data', array( $this, 'set_coauthor_metadata' ), 10, 2 );
		add_filter( 'searchwp\source\attribute\options', array( $this, 'special_meta_keys' ), 10, 2 );
	}

	public function special_meta_keys( $keys, $args ) {
		if ( $args['attribute'] !== 'meta' ) {
			return $keys;
		}

		$these_keys = array();
		foreach ( $this->get_fields() as $field ) {
			$these_keys[] = 'coauthorsplus_' . $field;
		}

		foreach ( $these_keys as $this_key ) {
			// Add this field if it's not added already.
			if ( ! in_array(
					$this_key,
					array_map( function( $option ) { return $option->get_value(); }, $keys )
				) ) {

				$keys[] = new \SearchWP\Option( $this_key, 'Co-Authors Plus: ' . $this_key );
			}
		}

		return $keys;
	}

	function set_coauthor_metadata( $data, $entry ) {
		$meta = $this->retrieve_coauthor_metadata( [], $entry->native() );

		if ( ! empty( $meta ) ) {
			foreach ( $meta as $key => $val ) {
				$data['meta'][ $key ] = new \SearchWP\Tokens( $val );
			}
		}

		return $data;
	}

	function get_fields() {
		// we always want to support 'any'
		$fields = array_merge( array( 'any' ), $this->fields );

		return array_unique( $fields );
	}

	function retrieve_coauthor_metadata( $extra_meta, $post_being_indexed ) {
		if ( ! function_exists( 'get_coauthors' ) ) {
			return $extra_meta;
		}

		// retrieve a list of author IDs
		$coauthors = get_coauthors( $post_being_indexed->ID );

		if ( empty( $coauthors ) ) {
			$coauthors = array( $post_being_indexed->post_author );
		}

		$this->extra_meta = array();
		$this->extra_meta[ 'coauthorsplus_any' ] = array();

		// $coauthors is an array of WP_User objects or standard objects for guest authors
		$users = array();
		$guest_authors = array();

		foreach ( $coauthors as $coauthor ) {
			if ( is_a( $coauthor, 'WP_User' ) ) {
				$users[] = $coauthor;
			} else {
				$guest_authors[] = $coauthor;
			}
		}

		$this->add_user_extra_meta( $users );
		$this->add_guest_extra_meta( $guest_authors );

		$extra_meta = is_array( $extra_meta ) ? array_merge( $extra_meta, $this->extra_meta ) : $this->extra_meta;

		return $extra_meta;
	}

	function add_user_extra_meta( $coauthors ) {
		$coauthors = wp_list_pluck( $coauthors, 'ID' );

		$coauthors = array_map( 'absint', $coauthors );

		foreach ( $coauthors as $coauthor ) {

			// break out meta per field
			foreach ( $this->get_fields() as $field ) {

				if ( ! isset( $this->extra_meta[ 'coauthorsplus_' . $field ] ) || ! is_array( $this->extra_meta[ 'coauthorsplus_' . $field ] ) ) {
					$this->extra_meta[ 'coauthorsplus_' . $field ] = array();
				}

				$this->extra_meta[ 'coauthorsplus_' . $field ][] = get_the_author_meta( $field, $coauthor );

				// the 'any' field is internal
				if ( 'any' !== $field ) {
					$this->extra_meta[ 'coauthorsplus_any' ][] = get_the_author_meta( $field, $coauthor );
				}
			}

		}
	}

	function add_guest_extra_meta( $coauthors ) {
		foreach ( $coauthors as $coauthor ) {
			if ( ! is_object( $coauthor ) ) {
				continue;
			}

			// break out meta per field
			foreach ( $this->get_fields() as $field ) {

				if ( ! isset( $this->extra_meta[ 'coauthorsplus_' . $field ] ) || ! is_array( $this->extra_meta[ 'coauthorsplus_' . $field ] ) ) {
					$this->extra_meta[ 'coauthorsplus_' . $field ] = array();
				}

				// the 'any' field is internal and there is no nickname field
				if ( 'any' !== $field && 'nickname'  !== $field) {
					$this->extra_meta[ 'coauthorsplus_' . $field ][] = $coauthor->$field;
					$this->extra_meta[ 'coauthorsplus_any' ][] = $coauthor->$field;
				}
			}

		}
	}

	function searchwp_custom_field_keys( $keys ) {

		foreach ( $this->get_fields() as $field ) {
			$keys[] = 'coauthorsplus_' . $field;
		}

		return array_unique( $keys );

	}

}

new SearchWPCoAuthorsPlus();

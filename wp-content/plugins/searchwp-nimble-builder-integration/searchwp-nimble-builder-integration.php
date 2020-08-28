<?php
/*
Plugin Name: SearchWP Nimble Builder Integration
Plugin URI: https://searchwp.com/extensions/nimble-builder-integration/
Description: Integrate SearchWP with Nimble Builder
Version: 1.0
Author: SearchWP, LLC
Author URI: https://searchwp.com/

Copyright 2019 Jonathan Christopher

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

if ( ! defined( 'SEARCHWP_NIMBLE_BUILDER_VERSION' ) ) {
	define( 'SEARCHWP_NIMBLE_BUILDER_VERSION', '1.0' );
}

/**
 * Instantiate the updater.
 */
if ( ! class_exists( 'SWP_Nimble_Builder_Updater' ) ) {
	include_once dirname( __FILE__ ) . '/vendor/updater.php';
}

function searchwp_nimble_builder_update_check() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_PREFIX' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_EDD_STORE_URL' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_EDD_VERSION' ) ) {
		return false;
	}

	$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
	$license_key = sanitize_text_field( $license_key );

	$searchwp_nimble_builder_updater = new SWP_Nimble_Builder_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 193387,
			'version'   => SEARCHWP_EDD_VERSION,
			'license'   => $license_key,
			'item_name' => 'Nimble Builder Integration',
			'author'    => 'SearchWP, LLC',
			'url'       => site_url(),
		)
	);

	return $searchwp_nimble_builder_updater;
}

add_action( 'admin_init', 'searchwp_nimble_builder_update_check' );

/**
 * SearchWP Nimble Builder Integration
 *
 * Class SearchWP_Nimble_Builder_Integration
 */
class SearchWP_Nimble_Builder_Integration {

	function __construct() {
		add_filter( 'searchwp_set_post', array( $this, 'set_post' ), 110 );
		add_action( 'customize_save', array( $this, 'trigger_index' ), 110 );
	}

	public function trigger_index( $manager ) {
		$skope_id = apply_filters( 'skp_get_skope_id', 0, 'local' );

		if ( empty( $skope_id ) ) {
			return;
		}

		// $skope_id has the post ID we want as a suffix.
		preg_match( '/(\d+)$/', $skope_id, $post_id );

		if ( $post_id && isset( $post_id[1] ) && is_numeric( $post_id[1] ) ) {
			SWP()->purge_post( $post_id[1] );
			SWP()->trigger_index();
		}
	}

	/**
	 * Callback to SearchWP's set_post hook. When editing a post using Nimble Builder, Nimble Builder does not
	 * store anything in post_content. Instead it creates foreign shadow entries in a Custom Post Type that
	 * stores a serialized array of Nimble Builder modules in _that_ post_content. What we need to do is replace
	 * this post content with the (parsed) post_content from the Nimble Builder shadow entry in its CPT.
	 *
	 * @param WP_Post $the_post The incoming entry being indexed.
	 *
	 * @return WP_Post The updated post.
	 *
	 * @since 1.0
	 */
	public function set_post( $the_post ) {
		/**
		 * After reviewing how Nimble Builder works, we are not able to utilize
		 * \Nimble\sek_get_seks_post() to retrieve the foreign post we need.
		 * As a result we are going to utilize Nimble's naming convention.
		 */
		if ( ! defined( 'NIMBLE_OPT_PREFIX_FOR_SEKTION_COLLECTION' ) ) {
			return $the_post;
		}

		$foreign_key = $this->get_foreign_key( $the_post->ID );

		if ( empty( $foreign_key ) ) {
			return $the_post;
		}

		$nimble_post = get_post( absint( $foreign_key ) );

		$the_post = $this->update_post_content_with_nimble_content( $the_post, $nimble_post );

		return $the_post;
	}

	/**
	 * Nimble Builder stores post content in its own post type in the post_content field as a serialized
	 * array that has a number of properties which allow Nimble to operate internally. We only want the content
	 * of each Nimble Builder module though, so we're going to extract it from the serialized array.
	 *
	 * @param WP_Post $the_post The original post.
	 * @param WP_Post $nimble_post The Nimble Builder foreign post.
	 *
	 * @return WP_Post The updated post.
	 *
	 * @since 1.0
	 */
	private function update_post_content_with_nimble_content( $the_post, $nimble_post ) {
		if ( ! $nimble_post instanceof WP_Post ) {
			return $the_post;
		}

		$nimble_model = maybe_unserialize( $nimble_post->post_content );

		$values = is_array( $nimble_model ) ? $this->recursive_find( $nimble_model, 'value' ) : false;

		// Now that we have our values, we can turn that into a string that contains only the module content we're after.
		$the_post->post_content = $values ? implode( ' ', $this->recursive_values( $values ) ) : $the_post->post_content;

		return $the_post;
	}

	/**
	 * Nimble Builder uses a Custom Post Type to store all of its content, and there is a foreign
	 * key relationship that's maintained in the wp_options table. After sifting through the code
	 * of Nimble Builder it proved to be too difficult to use Nimble Builder internal methods
	 * to retrieve this relationship, so we are assuming things based on naming structure and
	 * post ID to retrieve the foreign post ID we need to actually work with, which is the post
	 * ID of the Nimble Builder CPT entry whose post_content contains the Nimble Builder post_content.
	 *
	 * @param int $post_id The source post ID for which we want the Nimble Builder post ID.
	 *
	 * @param int The Nimble Builder post ID that contains the actual post content we want.
	 *
	 * @since 1.0
	 */
	private function get_foreign_key( $post_id ) {
		global $wpdb;

		$prefix      = preg_replace( '/[^a-zA-Z_]*/m', '', NIMBLE_OPT_PREFIX_FOR_SEKTION_COLLECTION );
		$option_key  = $prefix . 'skp_%_' . absint( $post_id );

		return $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name LIKE '" . $option_key . "'" );
	}

	/**
	 * Recursively retrieve array values by key.
	 *
	 * @param array $array The source array.
	 * @param string $needle The key to retrieve.
	 *
	 * @return array The filtered array.
	 *
	 * @since 1.0
	 */
	public function recursive_find( array $array, $needle ) {
		$iterator  = new RecursiveArrayIterator( $array );
		$recursive = new RecursiveIteratorIterator( $iterator, RecursiveIteratorIterator::SELF_FIRST );
		$aHitList  = array();

		foreach ( $recursive as $key => $value ) {
			if ( $key === $needle ) {
				array_push( $aHitList, $value );
			}
		}

		return $aHitList;
	}

	/**
	 * Recursively extracts array values.
	 *
	 * @param array $array The source array.
	 *
	 * @return array The values.
	 *
	 * @since 1.0
	 */
	public function recursive_values( array $array ) {
		$flat = array();

		foreach( $array as $value ) {
			if ( is_array( $value ) ) {
				$flat = array_merge( $flat, $this->recursive_values( $value ) );
			} else {
				$flat[] = $value;
			}
		}

		return $flat;
	}
}

new SearchWP_Nimble_Builder_Integration();

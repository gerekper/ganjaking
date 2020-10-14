<?php
/*
Plugin Name: SearchWP WP Document Revisions Integration
Plugin URI: https://searchwp.com/
Description: Integrates SearchWP and WP Document Revisions
Version: 1.0.1
Author: SearchWP, LLC
Author URI: https://searchwp.com/

Copyright 2015-2019 Jonathan Christopher

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

if ( ! defined( 'SEARCHWP_WPDRI_VERSION' ) ) {
	define( 'SEARCHWP_WPDRI_VERSION', '1.0.1' );
}

/**
 * instantiate the updater
 */
if ( ! class_exists( 'SWP_Wpdri_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

// set up the updater
function searchwp_wpdri_update_check() {

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

	if ( ! defined( 'SEARCHWP_BBPRESS_VERSION' ) ) {
		return false;
	}

	// retrieve stored license key
	$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
	$license_key = sanitize_text_field( $license_key );

	// instantiate the updater to prep the environment
	$searchwp_wpdri_updater = new SWP_Wpdri_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 38053,
			'version'   => SEARCHWP_WPDRI_VERSION,
			'license'   => $license_key,
			'item_name' => 'WP Document Revisions Integration',
			'author'    => 'SearchWP, LLC',
			'url'       => site_url(),
		)
	);

	return $searchwp_wpdri_updater;
}

add_action( 'admin_init', 'searchwp_wpdri_update_check' );

class SearchWP_WPDRI {

	function __construct() {
		add_filter( 'searchwp_extra_metadata', array( $this, 'index_wp_document_revisions' ), 10, 2 );
		add_filter( 'searchwp_custom_field_keys', array( $this, 'custom_field_keys' ), 10, 1 );
	}

	function custom_field_keys( $keys ) {
		$keys[] = 'swp_wp_document_revision';

		return $keys;
	}

	/**
	 * Tap in to the SearchWP indexing process and check if a WP Document Revisions post is being indexed. If it is, we're
	 * going to retrieve the most recent revision and extract the PDF content of that file. We'll store that data as a
	 * pseudo Custom Field called wp_document_revision, allowing SearchWP to search for that content
	 *
	 * @param $post_metadata
	 * @param $post_to_index
	 *
	 * @return bool
	 */
	function index_wp_document_revisions( $post_metadata, $post_to_index ) {

		// make sure it's a WP Document Revisions Document
		if ( 'document' !== $post_to_index->post_type ) {
			return $post_metadata;
		}

		if ( ! class_exists( 'SearchWPIndexer' ) ) {
			return $post_metadata;
		}

		// get the latest Revision
		$rev_id   = $this->mywpdr_get_latest_revision( $post_to_index );
		$rev_post = get_post( $rev_id );
		$revision = get_post( $rev_post->post_content );

		// grab the PDF content from Xpdf
		$indexer = new SearchWPIndexer();
		$indexer->set_post( $revision );
		$pdf_content = $indexer->extract_pdf_text( absint( $revision->ID ) );

		// add it to the pseudo-metadata array
		$post_metadata['swp_wp_document_revision'] = $pdf_content;

		return $post_metadata;
	}

	/**
	 * This was taken directly from WPDR's main class because we can't instantiate a new object
	 * Given a post ID, returns the latest revision attachment
	 *
	 * @param object $post
	 *
	 * @return object latest revision object
	 */
	function mywpdr_get_latest_revision( $post ) {

		if ( is_object( $post ) && isset ( $post->ID ) ) {
			$post = $post->ID;
		}

		$revisions = $this->mywpdr_get_revisions( $post );

		if ( ! $revisions ) {
			return false;
		}

		//verify that there's an upload ID in the content field
		//if there's no upload ID for some reason, default to latest attached upload
		if ( ! is_numeric( $revisions[0]->post_content ) ) {
			$attachments = (array) $this->mywpdr_get_attachments( $post );

			if ( empty( $attachments ) ) {
				return false;
			}

			$latest_attachment = reset( $attachments );
			$revisions[0]->post_content = $latest_attachment->ID;
		}

		return $revisions[0];

	}

	/**
	 * This was taken directly from WPDR's main class because we can't instantiate a new object
	 * Retrieves all revisions for a given post (including the current post)
	 * Workaround for #16215 to ensure revision author is accurate
	 * http://core.trac.wordpress.org/ticket/16215
	 * @since 1.0
	 * @param int $postID the post ID
	 * @return array array of post objects
	 */
	function mywpdr_get_revisions( $postID ) {

		// Revision authors are actually shifted by one
		// This moves each revision author up one, and then uses the post_author as the initial revision

		//get the actual post
		$post = get_post( $postID );

		if ( ! $post ) {
			return false;
		}

		if ( $cache = wp_cache_get( $postID, 'document_revisions' ) ) {
			return $cache;
		}

		//correct the modified date
		$post->post_date = date( 'Y-m-d H:i:s', (int) get_post_modified_time( 'U', null, $postID ) );

		//grab the post author
		$post_author = $post->post_author;

		//fix for Quotes in the most recent post because it comes from get_post
		$post->post_excerpt = html_entity_decode( $post->post_excerpt );

		//get revisions, and prepend the post
		$revs = wp_get_post_revisions( $postID, array( 'order' => 'DESC' ) );
		array_unshift( $revs, $post );

		//loop through revisions
		foreach ( $revs as $ID => &$rev ) {

			//if this is anything other than the first revision, shift author 1
			if ( $ID < sizeof( $revs ) - 1 ) {
				$rev->post_author = $revs[ $ID + 1 ]->post_author;
			} else {
				// if last revision, get the post author
				$rev->post_author = $post_author;
			}
		}

		wp_cache_set( $postID, $revs, 'document_revisions' );

		return $revs;

	}

	/**
	 * This was taken directly from WPDR's main class because we can't instantiate a new object
	 * Given a post object, returns all attached uploads
	 *
	 * @since 0.5
	 *
	 * @param object|string $post (optional) post object
	 *
	 * @return object all attached uploads
	 */
	function mywpdr_get_attachments( $post = '' ) {

		if ( '' == $post ) {
			global $post;
		}

		//verify that it's an object
		if ( ! is_object( $post ) ) {
			$post = get_post( $post );
		}

		//check for revisions
		if ( $parent = wp_is_post_revision( $post ) ) {
			$post = get_post( $parent );
		}

		//check for attachments
		if ( 'attachment' == $post->post_type ) {
			$post = get_post( $post->post_parent );
		}

		if ( ! isset( $post->ID ) ) {
			return array();
		}

		$args = array(
			'post_parent' => $post->ID,
			'post_status' => 'inherit',
			'post_type'   => 'attachment',
			'order'       => 'DESC',
			'orderby'     => 'post_date',
		);

		$args = apply_filters( 'document_revision_query', $args );

		return get_children( $args );
	}

	function plugin_row() {
		if ( ! class_exists( 'SearchWP' ) || ! function_exists( 'SWP' ) ) {
			return;
		}

		$searchwp = SWP();
		if ( version_compare( $searchwp->version, '2.5', '<' ) ) { ?>
			<tr class="plugin-update-tr searchwp">
				<td colspan="3" class="plugin-update">
					<div class="update-message">
						<?php esc_html_e( 'SearchWP WP Document Revisions Integration requires SearchWP 2.5 or greater', 'searchwp' ); ?>
					</div>
				</td>
			</tr>
		<?php }
	}
}

new SearchWP_WPDRI();

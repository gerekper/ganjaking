<?php
/**
 * Yoast SEO Compatibility class
 *
 * @since 6.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_WPSEO_Compatibility {
	/**
	 * Constructor
	 */
	public function __construct() {
		if ( ! defined( 'WPSEO_VERSION' ) ) {
			return;
		}

		add_filter( 'wpseo_accessible_post_types', array( $this, 'remove_metabox_from_archive_pages' ) );
	}

	/**
	 * remove wpseo meta boxes from archive pages to set options in wpseo settings
	 */
	public function remove_metabox_from_archive_pages( $post_types ) {
		global $post;
		if ( ! $post || 'page' != $post->post_type ) {
			return $post_types;
		}
		return $post_types;
	}
}

new Porto_WPSEO_Compatibility();

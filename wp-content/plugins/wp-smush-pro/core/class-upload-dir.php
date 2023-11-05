<?php

namespace Smush\Core;

class Upload_Dir {
	private $wp_upload_dir;

	private $root_path;

	private $upload_path;

	private $upload_rel_path;

	private $upload_url;

	/**
	 * @return array
	 */
	private function get_wp_upload_dir() {
		if ( is_null( $this->wp_upload_dir ) ) {
			$this->wp_upload_dir = $this->prepare_wp_upload_dir();
		}

		return $this->wp_upload_dir;
	}

	/**
	 * @return mixed
	 */
	private function get_root_path() {
		if ( is_null( $this->root_path ) ) {
			$this->root_path = $this->prepare_root_path();
		}

		return $this->root_path;
	}

	/**
	 * @return mixed
	 */
	public function get_upload_path() {
		if ( is_null( $this->upload_path ) ) {
			$this->upload_path = $this->prepare_upload_path();
		}

		return $this->upload_path;
	}

	/**
	 * @return string
	 */
	public function get_upload_rel_path() {
		if ( is_null( $this->upload_rel_path ) ) {
			$this->upload_rel_path = $this->prepare_upload_rel_path();
		}

		return $this->upload_rel_path;
	}

	/**
	 * @return string
	 */
	public function get_upload_url() {
		if ( is_null( $this->upload_url ) ) {
			$this->upload_url = $this->prepare_upload_url();
		}

		return $this->upload_url;
	}

	private function prepare_upload_path() {
		$upload = $this->get_wp_upload_dir();

		return untrailingslashit( $upload['basedir'] );
	}

	private function prepare_upload_rel_path() {
		$root_path = $this->get_root_path();

		return str_replace( $root_path, '', $this->get_upload_path() );
	}

	private function prepare_upload_url() {
		$upload = $this->get_wp_upload_dir();

		return untrailingslashit( $upload['baseurl'] );
	}

	private function prepare_wp_upload_dir() {
		if ( ! is_multisite() || is_main_site() ) {
			$upload = wp_upload_dir();
		} else {
			// Use the main site's upload directory for all subsite's webp converted images.
			// This makes it easier to have a single rule on the server configs for serving webp in mu.
			$blog_id = get_main_site_id();
			switch_to_blog( $blog_id );
			$upload = wp_upload_dir();
			restore_current_blog();
		}

		return $upload;
	}

	protected function prepare_root_path() {
		// Is it possible that none of the following conditions are met?
		$root_path = '';

		// Get the Document root path. There must be a better way to do this.
		// For example, /srv/www/site/public_html for /srv/www/site/public_html/wp-content/uploads.
		if ( 0 === strpos( $this->get_upload_path(), ABSPATH ) ) {
			// Environments like Flywheel have an ABSPATH that's not used in the paths.
			$root_path = ABSPATH;
		} elseif ( ! empty( $_SERVER['DOCUMENT_ROOT'] ) && 0 === strpos( $this->get_upload_path(), wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) ) ) {
			/**
			 * This gets called when scanning for uncompressed images.
			 * When ran from certain contexts, $_SERVER['DOCUMENT_ROOT'] might not be set.
			 *
			 * We are removing this part from the path later on.
			 */
			$root_path = realpath( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		} elseif ( 0 === strpos( $this->get_upload_path(), dirname( WP_CONTENT_DIR ) ) ) {
			// We're assuming WP_CONTENT_DIR is only one level deep into the document root.
			// This might not be true in customized sites. A bit edgy.
			$root_path = dirname( WP_CONTENT_DIR );
		}

		$root_path = untrailingslashit( $root_path );

		/**
		 * Filters the Document root path used to get relative paths for webp rules.
		 * Hopefully of help for debugging and SLS.
		 *
		 * @since 3.9.0
		 */
		return apply_filters( 'smush_webp_rules_root_path_base', $root_path );
	}

	public function get_human_readable_path( $full_path ) {
		return str_replace( WP_CONTENT_DIR, '', $full_path );
	}
}
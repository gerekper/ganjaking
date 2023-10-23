<?php

class LocalFonts {
	protected static $shortcodesLoaded = false;

	public static $downloadAllFonts;

	public function __construct() {
		add_action( 'init', [
			$this,
			'init',
		] );
	}

	public function init() {
		$checked = get_option( 'wpb_js_local_google_fonts' );
		if ( empty( $checked ) || apply_filters( 'wpb_disable_local_fonts', false ) ) {
			return;
		}
		if ( is_null( self::$downloadAllFonts ) ) {
			self::$downloadAllFonts = apply_filters( 'wpb_download_all_google_fonts', false );
		}
		add_filter( 'style_loader_src', [
			$this,
			'getStyleLoaderSrc',
		], 110, 2 );

		// add action on save post
		add_action( 'save_post', [
			$this,
			'downloadFontsOnSave',
		], 10, 3 );
	}

	protected function checkAccess( $post_id, $post ) {
		// check if this is an auto save routine.
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return false;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return false;
			}
		}
		// if content doesn't have vc_row shortcode - return
		if ( ! preg_match( '/vc_row/', $post->post_content ) ) {
			return false;
		}
		// if editor is not enabled for this post type - return
		if ( ! in_array( $post->post_type, vc_editor_post_types() ) ) {
			return false;
		}
		// finally check the nonce
        // phpcs:ignore
		if ( ! isset( $_POST['wpb_js_google_fonts_save_nonce'] ) || ! wp_verify_nonce( $_POST['wpb_js_google_fonts_save_nonce'], 'wpb_js_google_fonts_save' ) ) {
			return false;
		}

		return true;
	}

	public function downloadFontsOnSave( $post_id, $post, $update ) {
		if ( ! $this->checkAccess( $post_id, $post ) ) {
			return;
		}

		if ( ! self::$shortcodesLoaded ) {
			self::$shortcodesLoaded = true;
			WPBMap::addAllMappedShortcodes();
		}
		// render shortcodes to simulate wp_enqueue_styles
		ob_start();
		do_shortcode( $post->post_content ); // this will call the filter getStyleLoaderSrc callback
		ob_end_clean(); // avoid unnecessary output
		// get global $wp_styles object
		global $wp_styles;
		// get queued styles
		$styles = $wp_styles->queue;
		foreach ( $styles as $style ) {
			// get style src
			$src = $wp_styles->registered[ $style ]->src;
			// handle should be vc_google_fonts
			$handle = $wp_styles->registered[ $style ]->handle;

			// check if src is a Google font
			if ( self::$downloadAllFonts && strpos( $src, 'fonts.googleapis.com/css' ) !== false ) {
				// download font
				$this->downloadFontFamily( $src );
			} else if ( strpos( $handle, 'vc_google_fonts_' ) !== false ) {
				// download font
				$this->downloadFontFamily( $src );
			}
		}
	}

	/**
	 * @return string
	 */
	public function getStyleLoaderSrc( $src, $handle = '' ) {
		if ( self::$downloadAllFonts && strpos( $src, 'fonts.googleapis.com/css' ) !== false ) {
			// download font
			return $this->downloadFontFamily( $src );
		} else if ( strpos( $handle, 'vc_google_fonts_' ) !== false ) {
			// download font
			return $this->downloadFontFamily( $src );
		}

		return $src;
	}

	/**
	 * @return WP_Filesystem_Base|\WP_Filesystem_Direct
	 */
	protected function getFileSystem() {
		global $wp_filesystem;

		if ( ! $wp_filesystem ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}

	protected function downloadFontFamily( $src ) {
		// get the font name
		$src = urldecode( $src );
		preg_match( '/family=([^&:]+)/', $src, $matches );
		// slugify the font name
		$fontSlug = strtolower( sanitize_title( $matches[1] ) );

		// include wp-includes/functions.php
		require_once ABSPATH . 'wp-includes/functions.php';
		$wpUploadDir = wp_upload_dir();
		$slugOfBaseUrl = str_replace( [
			'http://',
			'https://',
		], '', $wpUploadDir['baseurl'] );
		// replace wp-content/uploads with ''
		$slugOfBaseUrl = str_replace( 'wp-content/uploads', '', $slugOfBaseUrl );
		$slugOfBaseUrl = sanitize_title( $slugOfBaseUrl );
		// remove &ver from src
		$src = preg_replace( '/&ver=[^&]+/', '', $src );
		$srcChecksum = $fontSlug . '-' . md5( $src );
		// check if file exists in uploads/fonts
		$fontFile = $wpUploadDir['basedir'] . '/wpb-fonts/' . $slugOfBaseUrl . '/' . $srcChecksum . '.css';
		// use wp filesystem to check if file exists
		// include wp-admin/includes/.php
		/** @var WP_Filesystem_Base $filesystem */
		$filesystem = $this->getFileSystem();
		if ( ! is_object( $filesystem ) ) {
			return $src;
		}
		// create folder if not exists
		if ( ! $filesystem->is_dir( $wpUploadDir['basedir'] . '/wpb-fonts' ) ) {
			$filesystem->mkdir( $wpUploadDir['basedir'] . '/wpb-fonts' );
		}
		if ( ! $filesystem->is_dir( $wpUploadDir['basedir'] . '/wpb-fonts/' . $slugOfBaseUrl ) ) {
			$filesystem->mkdir( $wpUploadDir['basedir'] . '/wpb-fonts/' . $slugOfBaseUrl );
		}
		if ( ! $filesystem->exists( $fontFile ) ) {
			// if file doesn't exist, download it
			$fontContents = wp_remote_get( $src, [
				'timeout' => 30,
			] );
			if ( is_wp_error( $fontContents ) ) {
				// try again
				$fontContents = wp_remote_get( $src, [
					'timeout' => 30,
				] );
				if ( is_wp_error( $fontContents ) ) {
					// error_log the error
					//file_put_contents( ABSPATH . 'wp-content/uploads/error.log', $fontContents->get_error_message(), FILE_APPEND );

					return $src;
				}
			}
			$filesystem->put_contents( $fontFile, $this->downloadFontFiles( $slugOfBaseUrl, $fontSlug, $fontContents['body'] ) );
			// save the file to uploads/fonts
		}

		// return the local file
		return $wpUploadDir['baseurl'] . '/wpb-fonts/' . $slugOfBaseUrl . '/' . $srcChecksum . '.css';
	}

	protected function downloadFontFiles( $slugOfBaseUrl, $fontFamily, $body ) {
		// parse the body for all resources from css url() and download them locally
		// save the files to uploads/fonts/$fontFamily/
		// return the updated body with replaced url()
		$body = preg_replace_callback( '/url\((.*?)\)/', function ( $matches ) use ( $slugOfBaseUrl, $fontFamily ) {
			// $matches[1] is the url
			// download the file
			// save the file to uploads/fonts/$fontFamily/
			// return the new url
			$url = $matches[1];
			// get file name
			$fileName = basename( $url );
			// get file contents
			$fileContents = wp_remote_get( $url, [
				'sslverify' => false,
				'timeout' => 30,
			] );
			if ( is_wp_error( $fileContents ) ) {
				// try again
				$fileContents = wp_remote_get( $url, [
					'timeout' => 30,
				] );
				if ( is_wp_error( $fileContents ) ) {
					//file_put_contents( ABSPATH . 'wp-content/uploads/error.log', $fileContents->get_error_message(), FILE_APPEND );

					return $matches[0];
				}
			}
			// save the file to uploads/fonts/$fontFamily/
			$wpUploadDir = wp_upload_dir();
			$fontFile = $wpUploadDir['basedir'] . '/wpb-fonts/' . $slugOfBaseUrl . '/' . $fontFamily . '/' . $fileName;
			// use wp filesystem to check if file exists
			// include wp-admin/includes/.php
			/** @var WP_Filesystem_Base $filesystem */
			$filesystem = $this->getFileSystem();
			if ( ! is_object( $filesystem ) ) {
				return $url;
			}
			// create folder if not exists
			if ( ! $filesystem->is_dir( $wpUploadDir['basedir'] . '/wpb-fonts/' . $slugOfBaseUrl ) ) {
				$filesystem->mkdir( $wpUploadDir['basedir'] . '/wpb-fonts/' . $slugOfBaseUrl );
			}
			// create folder if not exists
			if ( ! $filesystem->is_dir( $wpUploadDir['basedir'] . '/wpb-fonts/' . $slugOfBaseUrl . '/' . $fontFamily ) ) {
				$filesystem->mkdir( $wpUploadDir['basedir'] . '/wpb-fonts/' . $slugOfBaseUrl . '/' . $fontFamily );
			}
			$filesystem->put_contents( $fontFile, $fileContents['body'] );

			// generate new url, relative-url
			return 'url(' . $fontFamily . '/' . $fileName . ')';
		}, $body );

		return $body;
	}
}

new LocalFonts();

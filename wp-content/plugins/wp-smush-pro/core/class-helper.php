<?php
/**
 * Helpers class.
 *
 * @package Smush\Core
 * @version 1.0
 *
 * @author Umesh Kumar <umesh@incsub.com>
 *
 * @copyright (c) 2017, Incsub (http://incsub.com)
 */

namespace Smush\Core;

use finfo;
use WP_Smush;
use WDEV_Logger;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Helper
 */
class Helper {
	/**
	 * Temporary cache.
	 *
	 * We use this instead of WP_Object_Cache to avoid save data to memory cache (persistent caching).
	 *
	 * And to avoid it take memory space, we also reset the group cache each we get a new key,
	 * it means one group only has one key.
	 * It's useful when we want to save result a function.
	 *
	 * Leave group is null to set and get the value by unique key.
	 *
	 * It's useful to avoid checking something multiple times.
	 *
	 * @since 3.9.6
	 *
	 * @var array
	 */
	private static $temp_cache = array();

	/**
	 * WPMUDEV Logger lib.
	 *
	 * @access private
	 *
	 * @var null|WDEV_Logger
	 */
	private static $logger;

	/**
	 * Get WDEV_Logger instance.
	 *
	 * @return WDEV_Logger
	 */
	public static function logger() {
		if ( null === self::$logger ) {
			$swiched_blog = false;
			// On MU site, move all log files into the log folder [wp-content/uploads/smush] on the main site.
			if ( is_multisite() && ! is_main_site() ) {
				switch_to_blog( get_main_site_id() );
				$swiched_blog = true;
			}
			$upload_dir = wp_get_upload_dir();

			$log_dir = 'smush';
			if ( false !== strpos( $upload_dir['basedir'], WP_CONTENT_DIR ) ) {
				$log_dir = str_replace( trailingslashit( WP_CONTENT_DIR ), '', $upload_dir['basedir'] ) . '/smush';
			}

			if ( $swiched_blog ) {
				restore_current_blog();
			}

			self::$logger = WDEV_Logger::create(
				array(
					'log_dir'    => $log_dir,
					'is_private' => true,
					'modules'    => array(
						'smush'        => array(
							'is_global_module' => true,
						),
						'cdn'          => array(),
						'lazy'         => array(),
						'webp'         => array(),
						'png2jpg'      => array(),
						'resize'       => array(),
						'dir'          => array(),
						'backup'       => array(),
						'api'          => array(),
						'integrations' => array(),
					),
				)
			);
		}

		return self::$logger;
	}

	/**
	 * Clean file path.
	 *
	 * @param string $file File path.
	 * @return string
	 */
	public static function clean_file_path( $file ) {
		return str_replace( WP_CONTENT_DIR, '', $file );
	}

	/**
	 * Get value from temporary cache.
	 *
	 * @param string      $key Key name.
	 * @param string|null $group Group name.
	 * @param mixed       $default Default value, default is NULL.
	 *
	 *      Uses:
	 *      if( null !== Helper::cache_get( 'your_key', 'your_group' ) ){
	 *           // Do your something with temporary cache value.
	 *      }
	 *      // Maybe setting it with Helper::cache_set.
	 *
	 * @since 3.9.6
	 *
	 * @return mixed The cached result.
	 */
	public static function cache_get( $key, $group = null, $default = null ) {
		// Add current blog id to support MU site.
		$current_blog_id = get_current_blog_id();

		// Get cache for current blog.
		$temp_cache = array();
		if ( isset( self::$temp_cache[ $current_blog_id ] ) ) {
			$temp_cache = self::$temp_cache[ $current_blog_id ];
		}

		/**
		 * Add a filter to force cache.
		 * It might be helpful when we debug.
		 */
		if ( apply_filters( 'wp_smush_force_cache', false, $key, $group, $temp_cache ) ) {
			$locked_groups = array(
				// Required for cache png2jpg()->can_be_converted() before resizing.
				'png2jpg_can_be_converted',
				// Required for cache unique file name of png2jpg()->convert_to_jpg().
				'convert_to_jpg',
			);

			if ( ! in_array( $group, $locked_groups, true ) ) {
				return null;
			}
		}

		$value = $default;
		if ( isset( $group ) ) {
			if ( isset( $temp_cache[ $group ][ $key ] ) ) {
				$value = $temp_cache[ $group ][ $key ];
			} elseif ( isset( $temp_cache[ $group ] ) ) {
				// Get a new key, reset group.
				unset( $temp_cache[ $group ] );
			}
		} elseif ( isset( $temp_cache[ $key ] ) ) {
			// Get the value by key.
			$value = $temp_cache[ $key ];
		}

		return $value;
	}

	/**
	 * Save value to temporary cache.
	 *
	 * @since 3.9.6
	 *
	 * @param string      $key Key name.
	 * @param mixed       $value Data to cache.
	 * @param string|null $group Group name.
	 *
	 * Note, we return the provided value to use it inside some methods.
	 * @return mixed Returns the provided value.
	 */
	public static function cache_set( $key, $value, $group = null ) {
		// Add current blog id to support MU site.
		$current_blog_id = get_current_blog_id();
		if ( isset( $group ) ) {
			// Reset group and set the value.
			self::$temp_cache[ $current_blog_id ][ $group ] = array( $key => $value );
		} else {
			// Save value by unique key.
			self::$temp_cache[ $current_blog_id ][ $key ] = $value;
		}
		return $value;
	}

	/**
	 * Clear cache by group or key.
	 *
	 * @since 3.9.6
	 *
	 * @param string $cache_key Group name or unique key name.
	 */
	public static function cache_delete( $cache_key ) {
		// Add current blog id to support MU site.
		$current_blog_id = get_current_blog_id();

		// Delete temp cache by cache key.
		if ( isset( $cache_key, self::$temp_cache[ $current_blog_id ][ $cache_key ] ) ) {
			unset( self::$temp_cache[ $current_blog_id ][ $cache_key ] );
		}

		return true;
	}

	/**
	 * Get mime type for file.
	 *
	 * @since 3.1.0  Moved here as a helper function.
	 *
	 * @param string $path  Image path.
	 *
	 * @return bool|string
	 */
	public static function get_mime_type( $path ) {
		// These mime functions only work on local files/streams.
		if ( ! stream_is_local( $path ) ) {
			return false;
		}

		// Get the File mime.
		if ( class_exists( 'finfo' ) ) {
			$file_info = new finfo( FILEINFO_MIME_TYPE );
		} else {
			$file_info = false;
		}

		if ( $file_info ) {
			$mime = file_exists( $path ) ? $file_info->file( $path ) : '';
		} elseif ( function_exists( 'mime_content_type' ) ) {
			$mime = mime_content_type( $path );
		} else {
			$mime = false;
		}

		return $mime;
	}

	/**
	 * Filter the Posts object as per mime type.
	 *
	 * @param array $posts Object of Posts.
	 *
	 * @return array  Array of post IDs.
	 */
	public static function filter_by_mime( $posts ) {
		if ( empty( $posts ) ) {
			return $posts;
		}

		foreach ( $posts as $post_k => $post ) {
			if ( ! isset( $post->post_mime_type ) || ! in_array( $post->post_mime_type, Core::$mime_types, true ) ) {
				unset( $posts[ $post_k ] );
			} else {
				$posts[ $post_k ] = $post->ID;
			}
		}

		return $posts;
	}

	/**
	 * Iterate over PNG->JPG Savings to return cummulative savings for an image
	 *
	 * @param string $attachment_id  Attachment ID.
	 *
	 * @return array
	 */
	public static function get_pngjpg_savings( $attachment_id = '' ) {
		// Initialize empty array.
		$savings = array(
			'bytes'       => 0,
			'size_before' => 0,
			'size_after'  => 0,
		);

		// Return empty array if attachment id not provided.
		if ( empty( $attachment_id ) ) {
			return $savings;
		}

		$pngjpg_savings = get_post_meta( $attachment_id, 'wp-smush-pngjpg_savings', true );
		if ( empty( $pngjpg_savings ) || ! is_array( $pngjpg_savings ) ) {
			return $savings;
		}

		foreach ( $pngjpg_savings as $s_savings ) {
			if ( empty( $s_savings ) ) {
				continue;
			}
			$savings['size_before'] += $s_savings['size_before'];
			$savings['size_after']  += $s_savings['size_after'];
		}
		$savings['bytes'] = $savings['size_before'] - $savings['size_after'];

		return $savings;
	}

	/**
	 * Get the link to the media library page for the image.
	 *
	 * @since 2.9.0
	 *
	 * @param int    $id    Image ID.
	 * @param string $name  Image file name.
	 * @param bool   $src   Return only src. Default - return link.
	 *
	 * @return string
	 */
	public static function get_image_media_link( $id, $name, $src = false ) {
		$mode = get_user_option( 'media_library_mode' );
		if ( 'grid' === $mode ) {
			$link = admin_url( "upload.php?item=$id" );
		} else {
			$link = admin_url( "post.php?post=$id&action=edit" );
		}

		if ( ! $src ) {
			return "<a href='$link'>$name</a>";
		}

		return $link;
	}

	/**
	 * Returns current user name to be displayed
	 *
	 * @return string
	 */
	public static function get_user_name() {
		$current_user = wp_get_current_user();
		return ! empty( $current_user->first_name ) ? $current_user->first_name : $current_user->display_name;
	}

	/**
	 * Allows to filter the error message sent to the user
	 *
	 * @param string $error          Error message.
	 * @param string $attachment_id  Attachment ID.
	 *
	 * @return mixed|null|string
	 */
	public static function filter_error( $error = '', $attachment_id = '' ) {
		if ( empty( $error ) ) {
			return null;
		}

		/**
		 * Replace the 500 server error with a more appropriate error message.
		 */
		if ( false !== strpos( $error, '500 Internal Server Error' ) ) {
			$error = esc_html__( "Couldn't process image due to bad headers. Try re-saving the image in an image editor, then upload it again.", 'wp-smushit' );
		} elseif ( strpos( $error, 'timed out' ) ) {
			$error = esc_html__( "Timeout error. You can increase the request timeout to make sure Smush has enough time to process larger files. `define('WP_SMUSH_TIMEOUT', 150);`", 'wp-smushit' );
		}

		/**
		 * Used internally to modify the error message
		 */
		return apply_filters( 'wp_smush_error', $error, $attachment_id );
	}

	/**
	 * Format metadata from $_POST request.
	 *
	 * Post request in WordPress will convert all values
	 * to string. Make sure image height and width are int.
	 * This is required only when Async requests are used.
	 * See - https://wordpress.org/support/topic/smushit-overwrites-image-meta-crop-sizes-as-string-instead-of-int/
	 *
	 * @since 2.8.0
	 *
	 * @param array $meta Metadata of attachment.
	 *
	 * @return array
	 */
	public static function format_meta_from_post( $meta = array() ) {
		// Do not continue in case meta is empty.
		if ( empty( $meta ) ) {
			return $meta;
		}

		// If metadata is array proceed.
		if ( is_array( $meta ) ) {

			// Walk through each items and format.
			array_walk_recursive( $meta, array( 'self', 'format_attachment_meta_item' ) );
		}

		return $meta;
	}

	/**
	 * If current item is width or height, make sure it is int.
	 *
	 * @since 2.8.0
	 *
	 * @param mixed  $value Meta item value.
	 * @param string $key Meta item key.
	 */
	public static function format_attachment_meta_item( &$value, $key ) {
		if ( 'height' === $key || 'width' === $key ) {
			$value = (int) $value;
		}

		/**
		 * Allows to format single item in meta.
		 *
		 * This filter will be used only for Async, post requests.
		 *
		 * @param mixed $value Meta item value.
		 * @param string $key Meta item key.
		 */
		$value = apply_filters( 'wp_smush_format_attachment_meta_item', $value, $key );
	}

	/**
	 * Check to see if file is animated.
	 *
	 * @since 3.0    Moved from class-resize.php
	 * @since 3.9.6  Add a new param $mime_type.
	 *
	 * @param string       $file_path  Image file path.
	 * @param int          $id         Attachment ID.
	 * @param false|string $mime_type  Mime type.
	 *
	 * @return bool|int
	 */
	public static function check_animated_status( $file_path, $id, $mime_type = false ) {
		// Only do this for GIFs.
		$mime_type = $mime_type ? $mime_type : get_post_mime_type( $id );
		if ( 'image/gif' !== $mime_type || ! isset( $file_path ) ) {
			return false;
		}

		// Try to check from saved meta.
		$is_animated = get_post_meta( $id, 'wp-smush-animated', true );
		if ( $is_animated ) {
			/**
			 * Support old version.
			 *
			 * @since 3.9.10
			 * @since 3.12.0 Flag as a failed item with animated error keycode.
			 */
			Error_Handler::set_flag_failed_item( $id, 'animated' );
			// Clean the old metadata.
			delete_post_meta( $id, 'wp-smush-animated' );
			return true;
		}

		$enabled_backup = WP_Smush::get_instance()->core()->mod->backup->is_active();
		// If enabling backup, it's safe for us to check exists result from the meta value.
		if ( $enabled_backup && '0' === $is_animated ) {
			// If it's not an animated image, returns.
			return false;
		}

		// Check animated status from error meta value.
		$is_animated = Error_Handler::is_animated_file( $id );
		if ( $is_animated ) {
			return true;
		}

		$filecontents = file_get_contents( $file_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$str_loc = 0;
		$count   = 0;

		// There is no point in continuing after we find a 2nd frame.
		while ( $count < 2 ) {
			$where1 = strpos( $filecontents, "\x00\x21\xF9\x04", $str_loc );
			if ( false === $where1 ) {
				break;
			} else {
				$str_loc = $where1 + 1;
				$where2  = strpos( $filecontents, "\x00\x2C", $str_loc );
				if ( false === $where2 ) {
					break;
				} else {
					if ( $where2 === $where1 + 8 ) {
						$count++;
					}
					$str_loc = $where2 + 1;
				}
			}
		}

		$is_animated = $count > 1;
		if ( ! $is_animated && $enabled_backup ) {
			// Cache non-animated status if user enabled the backup mode. We cached animated status via Failed_Processing.
			update_post_meta( $id, 'wp-smush-animated', $is_animated );
		}

		return $is_animated;
	}

	/**
	 * Verify the file size limit.
	 *
	 * @param int $attachment_id Attachment ID.
	 *
	 * Note: We only use this method to verify an image before smushing it,
	 * we still need to verify the file size of every thumbnail files while smushing them.
	 *
	 * @return bool|int Return the file size if the size limit is exceeded, otherwise return FALSE.
	 */
	public static function size_limit_exceeded( $attachment_id ) {
		$original_file_path = self::get_attached_file( $attachment_id, 'original' );
		if ( ! file_exists( $original_file_path ) ) {
			$original_file_path = self::get_attached_file( $attachment_id );
		}

		if ( ! file_exists( $original_file_path ) ) {
			return false;
		}
		$max_file_size = WP_Smush::is_pro() ? WP_SMUSH_PREMIUM_MAX_BYTES : WP_SMUSH_MAX_BYTES;
		$file_size     = filesize( $original_file_path );

		return $file_size > $max_file_size ? $file_size : false;
	}

	/**
	 * Original File path
	 *
	 * @param string $original_file  Original file.
	 *
	 * @return string File Path
	 */
	public static function original_file( $original_file = '' ) {
		$uploads     = wp_get_upload_dir();
		$upload_path = $uploads['basedir'];

		return path_join( $upload_path, $original_file );
	}

	/**
	 * Gets the WPMU DEV API key.
	 *
	 * @since 3.8.6
	 *
	 * @return string|false
	 */
	public static function get_wpmudev_apikey() {
		// If API key defined manually, get that.
		if ( defined( 'WPMUDEV_APIKEY' ) && WPMUDEV_APIKEY ) {
			return WPMUDEV_APIKEY;
		}

		// If dashboard plugin is active, get API key from db.
		if ( class_exists( 'WPMUDEV_Dashboard' ) ) {
			return get_site_option( 'wpmudev_apikey' );
		}

		return false;
	}

	/**
	 * Get upsell URL.
	 *
	 * @since 3.9.1
	 *
	 * @param string $utm_campaign  Campaing string.
	 *
	 * @return string
	 */
	public static function get_url( $utm_campaign = '' ) {
		$upgrade_url = 'https://wpmudev.com/project/wp-smush-pro/';

		return add_query_arg(
			array(
				'utm_source'   => 'smush',
				'utm_medium'   => 'plugin',
				'utm_campaign' => $utm_campaign,
			),
			$upgrade_url
		);
	}

	/**
	 * Get Smush page URL.
	 *
	 * @param string $page Page URL.
	 *
	 * @return string
	 */
	public static function get_page_url( $page = 'smush-bulk' ) {
		if ( is_multisite() && is_network_admin() ) {
			return network_admin_url( 'admin.php?page=' . $page );
		}

		return admin_url( 'admin.php?page=' . $page );
	}

	/**
	 * Get the extension of a file.
	 *
	 * @param string $file File path or file name.
	 * @param string $expected_ext The expected extension.
	 *
	 * @return bool|string Returns extension of the file, or false if it's not the same as the expected extension.
	 */
	public static function get_file_ext( $file, $expected_ext = '' ) {
		$ext = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
		if ( ! empty( $expected_ext ) ) {
			return $expected_ext === $ext ? $ext : false;
		} else {
			return $ext;
		}
	}

	/**
	 * Returns TRUE if the current request is REST API but is not media endpoint.
	 *
	 * @since 3.9.7
	 */
	public static function is_non_rest_media() {
		static $is_not_rest_media;
		if ( null === $is_not_rest_media ) {
			$is_not_rest_media = false;
			// We need to check if this call originated from Gutenberg and allow only media.
			if ( ! empty( $GLOBALS['wp']->query_vars['rest_route'] ) ) {
				$route = untrailingslashit( $GLOBALS['wp']->query_vars['rest_route'] );

				// Only allow media routes.
				if ( empty( $route ) || '/wp/v2/media' !== $route ) {
					// If not - return image metadata.
					$is_not_rest_media = true;
				}
			}
		}
		return $is_not_rest_media;
	}

	/**
	 * Checks if user is allowed to perform the ajax actions.
	 * As previous we allowed for logged in user, so add a hook filter to allow
	 * user can custom the capability. It might also helpful when user custom admin menu via Branda.
	 *
	 * @since 3.13.0
	 *
	 * @param string $capability Capability default is manage_options.
	 * @return boolean
	 */
	public static function is_user_allowed( $capability = 'manage_options' ) {
		$capability = empty( $capability ) ? 'manage_options' : $capability;
		return current_user_can( apply_filters( 'wp_smush_admin_cap', $capability ) );
	}

	/*------ S3 Compatible Methods ------*/

	/**
	 * Return unfiltered path for Smush or restore.
	 *
	 * @since 3.9.6
	 *
	 * @param int    $attachment_id  Attachment ID.
	 * @param string $type           false|original|scaled|smush|backup|resize|check-resize.
	 * @param bool   $unfiltered     Whether to get unfiltered path or not.
	 *
	 * $type = original|backup => Try to get the original image file path.
	 * $type = false|smush     => Get the file path base on the setting "compress original".
	 * $type = scaled|resize   => Get the full file path, for large jpg it's scaled file not the original file.
	 *
	 * @return bool|string
	 */
	public static function get_raw_attached_file( $attachment_id, $type = 'smush', $unfiltered = false ) {
		if ( function_exists( 'wp_get_original_image_path' ) ) {
			if ( 'backup' === $type ) {
				$type = 'original';
			} elseif ( 'resize' === $type || 'check-resize' === $type ) {
				$type = 'scaled';
			}
			// We will get the original file if we are doing for backup or restore, or smush original file.
			if ( 'original' === $type || 'scaled' !== $type && Settings::get_instance()->get( 'original' ) ) {
				$file_path = wp_get_original_image_path( $attachment_id, $unfiltered );
			} else {
				$file_path = get_attached_file( $attachment_id, $unfiltered );
			}
		} else {
			$file_path = get_attached_file( $attachment_id, $unfiltered );
		}

		return $file_path;
	}

	/**
	 * Return file path for Smush, restore or checking resize.
	 *
	 * Add a hook for third party download the file,
	 * if it's not available on the server.
	 *
	 * @param int    $attachment_id  Attachment ID.
	 * @param string $type           false|original|smush|backup|resize
	 * $type = smush|backup  => Get the file path and download the attached file if it doesn't exist.
	 * $type = check-resize  => Get the file path ( if it exists ), or filtered file path if it doesn't exist.
	 * $type = original      => Only get the original file path (not scaled file).
	 * $type = scaled|resize => Get the full file path, for large jpg it's scaled file not the original file.
	 * $type = false         => Get the file path base on the setting "compress original".
	 *
	 * @since 3.9.6 Moved S3 to S3 integration.
	 * Add a hook filter to allow 3rd party to custom the result.
	 *
	 * @return bool|string
	 */
	public static function get_attached_file( $attachment_id, $type = 'smush' ) {
		if ( empty( $attachment_id ) ) {
			return false;
		}

		/**
		 * Add a hook to allow 3rd party to custom the result.
		 *
		 * @param null|string $file_path        File path or file url(checking resize).
		 * @param int         $attachment_id    Attachment ID.
		 * @param bool        $should_download  Should download the file if it doesn't exist.
		 * @param bool        $should_real_path Expecting a real file path instead an URL.
		 * @param string      $type             false|original|smush|backup|resize|scaled|check-resize.
		 *
		 * @usedby Smush\Core\Integrations\S3::get_attached_file
		 */
		// If the site is using S3, we only need to download the file when doing smush, backup or resizing.
		$should_download = in_array( $type, array( 'smush', 'backup', 'resize' ), true );
		// But when restoring/smushing we are expecting a real file path.
		$should_real_path = 'check-resize' !== $type;
		$file_path        = apply_filters( 'wp_smush_get_attached_file', null, $attachment_id, $should_download, $should_real_path, $type );

		if ( is_null( $file_path ) ) {
			$file_path = self::get_raw_attached_file( $attachment_id, $type );
		}

		return $file_path;
	}

	/**
	 * Custom for function wp_update_attachment_metadata
	 * We use this method to reset our S3 config before updating the metadata.
	 *
	 * @param int   $attachment_id Attachment ID.
	 * @param array $meta Metadata.
	 * @return bool
	 */
	public static function wp_update_attachment_metadata( $attachment_id, $meta ) {
		/**
		 * Fire before calling wp_update_attachment_metadata.
		 *
		 * @param int   $attachment_id Attachment ID.
		 * @param array $meta Metadata.
		 *
		 * @hooked Smush\Core\Integrations\S3::release_smush_mode()
		 * This will help we to upload the attachments, and remove them if it's required.
		 */
		do_action( 'wp_smush_before_update_attachment_metadata', $attachment_id, $meta );
		return wp_update_attachment_metadata( $attachment_id, $meta );
	}

	/**
	 * Check if the file exists on the server or cloud (S3).
	 *
	 * @since 3.9.6
	 *
	 * @param string|int $file  File path or File ID.
	 * @param int|null   $attachment_id File ID.
	 * @param bool       $should_download Whether to download the file or not.
	 * @param bool       $force_cache Whether check for result from the cache for full image or not.
	 *
	 * @return bool
	 */
	public static function file_exists( $file, $attachment_id = null, $should_download = false, $force_cache = false ) {
		// If file is an attachment id we will reset the arguments.
		// Use is_numeric for common case.
		if ( $file && is_numeric( $file ) ) {
			$attachment_id = $file;
			$file          = null;
		}

		// If the file path is not empty we will try to check file_exists first.
		if ( empty( $file ) ) {
			$file_exists = null;
		} else {
			$file_exists = file_exists( $file );
			if ( $file_exists ) {
				return true;
			}
		}

		// Only continue if provided Attachment ID.
		if ( $attachment_id < 1 ) {
			return false;
		}

		/**
		 * Check if there is a cached for full image.
		 */
		if ( null === $file && ! $force_cache ) {
			// Use different key for the download case.
			$cache_key = 'helper_file_exists' . intval( $should_download );

			$cached_file_exists = self::cache_get( $attachment_id, $cache_key );
			if ( null !== $cached_file_exists ) {
				return $cached_file_exists;
			}
		}

		/**
		 * Add a hook to allow 3rd party to custom the result.
		 *
		 * @param bool|null   $file_exists Current status.
		 * @param string|null $file Full file path.
		 * @param int         $attachment_id Attachment ID.
		 * @param bool        $should_download Whether to download the file if it's missing on the server or not.
		 *
		 * @usedby Smush\Core\Integrations\S3::file_exists_on_s3
		 */
		$file_exists = apply_filters( 'wp_smush_file_exists', $file_exists, $file, $attachment_id, $should_download );

		// If it doesn't check and file is null, we will try to get the attached file from $attachment_id to check.
		if ( is_null( $file_exists ) && ! $file ) {
			$file = get_attached_file( $attachment_id );
			if ( $file ) {
				$file_exists = file_exists( $file );
			}
		}

		/**
		 * Cache the result for full image,
		 * It also avoid we download again the not found image when enabling S3.
		 */
		if ( isset( $cache_key ) ) {
			return self::cache_set( $attachment_id, $file_exists, $cache_key );
		}

		return $file_exists;
	}

	/**
	 * Check if the file exists, will try to download if it is not on the server (e.g s3).
	 *
	 * @since 3.9.6
	 *
	 * @param string|int $file          File path or File ID.
	 * @param int|null   $attachment_id File ID.
	 *
	 * @return bool Returns TRUE if file exists on the server.
	 */
	public static function exists_or_downloaded( $file, $attachment_id = null ) {
		return self::file_exists( $file, $attachment_id, true );
	}

	/**
	 * Check if the file is an image, is supported in Smush and exists, and then cache the result.
	 *
	 * @since 3.9.6
	 *
	 * @param int|null $attachment_id File ID.
	 *
	 * @return bool|0 Returns TRUE if file is smushable, FALSE If the image does not exist, and 0 is not an image or is not supported
	 */
	public static function is_smushable( $attachment_id ) {
		if ( empty( $attachment_id ) ) {
			return null;// Nothing to check.
		}

		$is_smushable = self::cache_get( $attachment_id, 'is_smushable' );
		if ( ! is_null( $is_smushable ) ) {
			return $is_smushable;
		}
		// Set is_smushable is 0 (not false) to detect is not an image or image not found.
		$is_smushable = 0;
		$mime         = get_post_mime_type( $attachment_id );
		if (
			apply_filters( 'wp_smush_resmush_mime_supported', in_array( $mime, Core::$mime_types, true ), $mime )
			&& wp_attachment_is_image( $attachment_id )
		) {
			$is_smushable = self::file_exists( $attachment_id );
		}

		/**
		 * Cache and returns the result.
		 * Also added a hook for third-party.
		 *
		 * @param bool  $is_smushable   0 if is not an image or mime type not supported | TRUE if image exists and otherwise is FALSE.
		 * @param int   $attachment_id  Attachment ID.
		 * @param array $mime_types     List supported mime types.
		 */
		return apply_filters( 'wp_smush_is_smushable', self::cache_set( $attachment_id, $is_smushable, 'is_smushable' ), $attachment_id, Core::$mime_types );
	}

	/**
	 * Delete a file path from server and cloud (e.g s3).
	 *
	 * @since 3.9.6
	 *
	 * @param string|array $file_paths File path or list of file paths to remove.
	 * @param int          $attachment_id Attachment ID.
	 * @param bool         $only_exists_file Whether to call the action wp_smush_after_remove_file even the file doesn't exits or not.
	 *
	 * Current we only use this method to delete the file when after converting PNG to JPG or after restore, or when delete the files.
	 */
	public static function delete_permanently( $file_paths, $attachment_id, $only_exists_file = true ) {
		if ( empty( $file_paths ) ) {
			return;
		}
		$file_paths = (array) $file_paths;

		$removed = true;
		foreach ( $file_paths as $file_path ) {
			if ( file_exists( $file_path ) ) {
				if ( ! unlink( $file_path ) ) {
					$removed = false;
					// Log the error.
					self::logger()->error( sprintf( 'Cannot delete file [%s(%d)].', self::clean_file_path( $file_path ), $attachment_id ) );
				}
			}
		}

		if ( $removed || ! $only_exists_file ) {
			/**
			 * Fires after removing a file on server.
			 *
			 * @param int          $attachment_id Attachment ID.
			 * @param string|array $file_paths File path or list of file paths.
			 * @param bool         $removed Unlink status.
			 */
			do_action( 'wp_smush_after_remove_file', $attachment_id, $file_paths, $removed );
		}
	}

	/*------ End S3 Compatible Methods ------*/

}

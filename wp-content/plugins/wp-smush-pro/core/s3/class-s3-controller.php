<?php

namespace Smush\Core\S3;

use DeliciousBrains\WP_Offload_Media\Integrations\Media_Library;
use DeliciousBrains\WP_Offload_Media\Items\Media_Library_Item;
use Smush\Core\Controller;
use Smush\Core\File_System;
use Smush\Core\Helper;
use Smush\Core\Media\Media_Item;
use Smush\Core\Media\Media_Item_Cache;
use Smush\Core\Settings;
use WDEV_Logger;

class S3_Controller extends Controller {
	const AS3CF_GET_ATTACHED_FILE_PRIORITY = - 10;
	private $media_item_cache;
	/**
	 * @var WP_Offload_Media_Api
	 */
	private $wp_offload_media;
	/**
	 * @var WDEV_Logger
	 */
	private $logger;
	/**
	 * @var Settings
	 */
	private $settings;
	/**
	 * @var File_System
	 */
	private $fs;

	public function __construct() {
		$this->media_item_cache = Media_Item_Cache::get_instance();
		$this->wp_offload_media = new WP_Offload_Media_Api();
		$this->logger           = Helper::logger()->integrations();
		$this->settings         = Settings::get_instance();
		$this->fs               = new File_System();

		$this->register_action( 'init', array( $this, 'maybe_initialize' ), - 10 );
	}

	public function maybe_initialize() {
		$wp_offload_media_active = $this->wp_offload_media_active();
		if ( ! $wp_offload_media_active || ! $this->settings->is_s3_active() ) {
			return;
		}

		// TODO: PNG2Jpg file names should not exist on the server?
		// TODO: check whether we need to check is_plugin_setup, remove-local-file and copy-to-s3 settings from wp-offload

		$this->support_s3_image_optimization();

		$this->support_s3_backup_and_restore();

		add_filter( 'wp_smush_media_item_size', array( $this, 'initialize_s3_size' ), 10, 4 );
	}

	public function before_restore( $callback, $priority ) {
		add_action( 'wp_smush_before_restore_backup', $callback, $priority, 2 );
	}

	public function before_restore_attempt( $callback, $priority ) {
		add_action( 'wp_smush_before_restore_backup_attempt', $callback, $priority, 1 );
	}

	public function after_restore( $callback, $priority ) {
		add_action( 'wp_smush_after_restore_backup', $callback, $priority, 3 );
	}

	public function disable_s3_auto_download() {
		add_filter( 'as3cf_get_attached_file_copy_back_to_local', array( $this, 'return_false' ) );
	}

	public function enable_back_s3_auto_download() {
		add_filter( 'as3cf_get_attached_file_copy_back_to_local', array( $this, 'return_false' ) );
	}

	public function download_all_sizes( $attachment_id ) {
		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( ! $this->is_media_item_valid( $media_item ) ) {
			return;
		}

		foreach ( $media_item->get_sizes() as $size ) {
			if ( ! is_a( $size, '\Smush\Core\S3\S3_Media_Item_Size' ) ) {
				$this->log_error( 'Something went wrong while trying to download the images for Smush.' );
				continue;
			}

			if ( ! $this->fs->file_exists( $size->get_local_path() ) ) {
				$this->download_remote_file( $attachment_id, $size->get_local_path() );
			}
		}
	}

	public function download_backup_file( $file_path, $attachment_id ) {
		if ( ! $this->fs->file_exists( $file_path ) ) {
			$this->download_remote_file( $attachment_id, $file_path );
		}
	}

	private function download_remote_file( $attachment_id, $file_path ) {
		$s3_library_item = $this->get_s3_media_item( $attachment_id );
		if ( $s3_library_item ) {
			$this->wp_offload_media->copy_provider_file_to_server( $s3_library_item, $file_path );
		}

		if ( ! $this->fs->file_exists( $file_path ) ) {
			$this->log_error( "Failed to download remote file $attachment_id." );
		}
	}

	public function disable_s3_update_attachment( $data ) {
		add_action( 'as3cf_pre_update_attachment_metadata', array( $this, 'return_true' ) );

		return $data;
	}

	public function enable_back_s3_update_attachment() {
		remove_action( 'as3cf_pre_update_attachment_metadata', array( $this, 'return_true' ) );
	}

	public function return_true() {
		return true;
	}

	public function return_false() {
		return false;
	}

	/**
	 * @return bool
	 */
	private function wp_offload_media_active() {
		return function_exists( 'as3cf_init' ) || function_exists( 'as3cf_pro_init' );
	}

	/**
	 * @param Media_Item $media_item
	 *
	 * @return bool
	 */
	private function is_media_item_valid( $media_item ) {
		$invalid = ! $media_item || empty( $media_item->get_wp_metadata() );
		if ( $invalid ) {
			$this->log_error( 'Media item is not valid.' );
		}

		return ! $invalid;
	}

	public function disable_s3_get_attached_file_filters() {
		// Make sure smush always gets local paths
		$this->disable_stream_wrapper_file();
		// S3 auto downloads an image when get_attached_file is called, we want to disable this, because we will explicitly download all media item sizes.
		$this->disable_s3_auto_download();
		// Reset media items, so they have to fetch the new values
		$this->media_item_cache->reset_all();
	}

	public function enable_back_s3_get_attached_file_filters() {
		$this->enable_back_stream_wrapper_file();
		$this->enable_back_s3_auto_download();
		$this->media_item_cache->reset_all();
	}

	private function disable_stream_wrapper_file() {
		add_filter(
			'as3cf_get_attached_file',
			array( $this, 'return_local_file_path' ),
			self::AS3CF_GET_ATTACHED_FILE_PRIORITY, // Our callback needs to run before the s3 callback get_stream_wrapper_file
			2
		);
	}

	private function enable_back_stream_wrapper_file() {
		remove_filter( 'as3cf_get_attached_file', array(
			$this,
			'return_local_file_path',
		), self::AS3CF_GET_ATTACHED_FILE_PRIORITY );
	}

	public function return_local_file_path( $url, $file_path ) {
		return $file_path;
	}

	/**
	 * @param callable $callback
	 *
	 * @return void
	 */
	private function before_smush( $callback, $priority ) {
		add_action( 'wp_smush_before_smush_file', $callback, $priority );
	}

	private function before_smush_attempt( $callback, $priority ) {
		add_action( 'wp_smush_before_smush_attempt', $callback, $priority );
	}

	/**
	 * @param callable $callback
	 *
	 * @return void
	 */
	private function after_smush( $callback, $priority ) {
		add_action( 'wp_smush_after_smush_file', $callback, $priority );
	}

	/**
	 * @param $attachment_id
	 *
	 * @return void
	 */
	public function trigger_update_attachment_metadata( $attachment_id ) {
		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( ! $this->is_media_item_valid( $media_item ) ) {
			return;
		}
		wp_update_attachment_metadata( $attachment_id, $media_item->get_wp_metadata() );
	}

	public function delete_files_from_remote( $attachment_id ) {
		if ( ! $this->local_files_available( $attachment_id ) ) {
			$this->log_error( "Did not find expected local files for attachment $attachment_id, so files from remote are not being deleted." );
			return;
		}

		$s3_media_item  = $this->get_s3_media_item( $attachment_id );
		$remove_handler = $this->wp_offload_media->get_item_handler( 'remove-provider' );
		if ( $s3_media_item && $remove_handler && method_exists( $remove_handler, 'handle' ) ) {
			$remove_handler->handle( $s3_media_item, array( 'verify_exists_on_local' => false ) );
		}
	}

	private function local_files_available( $attachment_id ) {
		$local_media_item = $this->media_item_cache->get( $attachment_id );
		foreach ( $local_media_item->get_sizes() as $size ) {
			if ( is_a( $size, '\Smush\Core\S3\S3_Media_Item_Size' ) ) {
				if ( ! $this->fs->file_exists( $size->get_local_path() ) ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * @param $size
	 * @param $key
	 * @param $metadata
	 * @param $media_item Media_Item
	 *
	 * @return S3_Media_Item_Size
	 */
	public function initialize_s3_size( $size, $key, $metadata, $media_item ) {
		return new S3_Media_Item_Size(
			$key,
			$media_item->get_id(),
			$media_item->get_dir(),
			$media_item->get_base_url(),
			$metadata
		);
	}

	/**
	 * @param $attachment_id
	 *
	 * @return Media_Library_Item|null
	 */
	private function get_s3_media_item( $attachment_id ) {
		return $this->wp_offload_media->is_attachment_served_by_provider( $attachment_id, true );
	}

	/**
	 * @return void
	 */
	private function support_s3_image_optimization() {
		/**
		 * Prevent frequent offloading attempts
		 */
		// During the optimization we might call wp_update_attachment_metadata multiple times. Prevent any offload attempts while smushing is in progress.
		$this->before_smush( array( $this, 'disable_s3_update_attachment' ), 10 );

		/**
		 * Ensure smush has access to local files during optimization
		 */
		// Download any of the sizes that don't exist locally
		$this->before_smush( array( $this, 'download_all_sizes' ), 20 );

		/**
		 * Delete remote version before uploading optimized
		 */
		// When all optimizations are completed, the new files will be uploaded.
		// Note that this is especially important for Png2Jpg optimization for getting rid of the old files from the servers. The new files are nothing like the old ones.
		$this->after_smush( array( $this, 'delete_files_from_remote' ), 10 );

		/**
		 * Trigger offloading after smush is done
		 */
		// Turn offloading back on
		$this->after_smush( array( $this, 'enable_back_s3_update_attachment' ), 20 );
		// Trigger offloading
		$this->after_smush( array( $this, 'trigger_update_attachment_metadata' ), 30 );

		/**
		 * Delay offloading on new media upload when auto smush is on
		 */
		$auto_smush_on = $this->settings->get( 'auto' );
		if ( $auto_smush_on ) {
			/**
			 * We need to prevent {@see Media_Library::wp_update_attachment_metadata()} from getting called
			 */

			// New media upload triggers wp_update_attachment_metadata which triggers offloading. Make sure offloading is postponed until smush is done.
			add_filter( 'add_attachment', array( $this, 'disable_s3_update_attachment' ) );

			$this->before_smush_attempt(
				function ( $attachment_id ) {
					$media_item = $this->media_item_cache->get( $attachment_id );
					if ( ! $media_item->is_valid() || $media_item->has_errors() || $media_item->is_skipped() ) {
						// If there is an error we want the image to be offloaded explicitly
						$this->enable_back_s3_update_attachment();
						$this->trigger_update_attachment_metadata( $attachment_id );
					}
					// We have already added hooks for enabling back and triggering offloading after successful smush.
				},
				100 // This has to be higher than other methods attached to this hook because $media_item->is_skipped() depends on those other methods
			);
		}
	}

	/**
	 * @return void
	 */
	private function support_s3_backup_and_restore() {
		/**
		 * Disable remote file filters during the restore process
		 */
		$this->before_restore_attempt( array( $this, 'disable_s3_get_attached_file_filters' ), 10 );

		/**
		 * Ensure smush has access to local files during restoration
		 */
		$this->before_restore( array( $this, 'download_backup_file' ), 10 );
		/**
		 * Disable offloading
		 */
		$this->before_restore( array( $this, 'disable_s3_update_attachment' ), 20 );

		/**
		 * Delete remote version before uploading restored
		 */
		// When the restoration is completed, the new files will be uploaded. Again, this is especially important for Png2Jpg
		$this->after_restore( function ( $restored, $backup_file_path, $attachment_id ) {
			if ( $restored ) {
				$this->delete_files_from_remote( $attachment_id );
			}
		}, 10 );

		/**
		 * Trigger offloading after restore is done
		 */
		$this->after_restore( array( $this, 'enable_back_s3_update_attachment' ), 20 );

		$this->after_restore( array( $this, 'enable_back_s3_get_attached_file_filters' ), 30 );

		$this->after_restore( function ( $restored, $backup_file_path, $attachment_id ) {
			if ( $restored ) {
				$this->trigger_update_attachment_metadata( $attachment_id );
			}
		}, 40 );
	}

	private function log_error( $error ) {
		$this->logger->error( "Smush S3 Integration: $error" );
	}
}
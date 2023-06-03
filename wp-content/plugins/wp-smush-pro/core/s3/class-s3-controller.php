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

	private $files_to_remove = array();
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

		$auto_smush_on = $this->settings->get( 'auto' );

		// TODO: check whether we need to check is_plugin_setup, remove-local-file and copy-to-s3 settings from wp-offload

		/**
		 * Ensure smush has access to local files during optimization
		 */
		// Download any of the sizes that don't exist locally
		$this->before_smush( array( $this, 'download_all_sizes' ), 20 );
		// Delete the media item from wp-offload and any remote servers. When all optimizations are completed, the new files will be uploaded.
		// Note that this is especially important for Png2Jpg optimization for getting rid of the old files from the servers. The new files are nothing like the old ones.
		$this->before_smush( array( $this, 'delete_files_from_remote' ), 30 );

		/**
		 * Ensure smush has access to local files during restoration
		 */
		$this->before_restore( array( $this, 'download_backup_file' ) );
		// Delete the media item from wp-offload and any remote servers. When the restoration is completed, the new files will be uploaded.
		// Again, this is especially important for Png2Jpg
		$this->before_restore( function ( $backup_file_path, $attachment_id ) {
			$this->delete_files_from_remote( $attachment_id );
		}, 20 );
		$this->before_restore( array( $this, 'disable_s3_update_attachment' ), 30 );
		$this->after_restore( array( $this, 'enable_back_s3_update_attachment' ) );
		$this->after_restore( function ( $restored, $backup_file_path, $attachment_id ) {
			if ( $restored ) {
				$this->trigger_update_attachment_metadata( $attachment_id );
			}
		}, 20 );

		/**
		 * Prevent frequent offloading attempts
		 */
		// During the optimization we might call wp_update_attachment_metadata multiple times. Prevent any offload attempts while smushing is in progress.
		$this->before_smush( array( $this, 'disable_s3_update_attachment' ) );
		// After all the optimizations are complete, turn on offloading.
		$this->after_smush( array( $this, 'enable_back_s3_update_attachment' ) );

		/**
		 * Trigger offloading after smush is done
		 */
		$this->after_smush( array( $this, 'trigger_update_attachment_metadata' ), 20 );

		/**
		 * Delay offloading on new media upload when auto smush is on
		 */
		if ( $auto_smush_on ) {
			/**
			 * We need to prevent {@see Media_Library::wp_update_attachment_metadata()} from getting called so the priority needs to be lower
			 * @var $priority
			 */
			$priority = 10;

			// New media upload triggers wp_update_attachment_metadata which triggers offloading. Make sure offloading is postponed until smush is done.
			add_filter( 'add_attachment', array( $this, 'disable_s3_update_attachment' ), $priority );
			// We have already added hooks for enabling back and triggering offloading after smush.
		}

		// TODO: PNG2Jpg file names should not exist on the server?

		/**
		 * Cleanup
		 */
		// If the user has chosen to remove local files then delete any leftover files
		// TODO: make sure local files are deleted in all cases
		add_filter( 'shutdown', array( $this, 'maybe_remove_downloaded_files' ), 10, 2 );

		add_filter( 'wp_smush_media_item_size', array( $this, 'initialize_s3_size' ), 10, 4 );
	}

	public function before_restore( $callback, $priority = 10 ) {
		add_action( 'wp_smush_before_restore_backup', $callback, $priority, 2 );
	}

	public function after_restore( $callback, $priority = 10 ) {
		add_action( 'wp_smush_after_restore_backup', $callback, $priority, 3 );
	}

	public function disable_s3_auto_download() {
		add_filter( 'as3cf_get_attached_file_copy_back_to_local', array( $this, 'return_false' ) );
	}

	public function enable_back_s3_auto_download() {
		add_filter( 'as3cf_get_attached_file_copy_back_to_local', array( $this, 'return_false' ) );
	}

	public function download_all_sizes( $attachment_id ) {
		$this->disable_s3_get_attached_file_filters();

		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( ! $this->is_media_item_valid( $media_item ) ) {
			return;
		}

		foreach ( $media_item->get_sizes() as $size_key => $size ) {
			if ( ! $this->fs->file_exists( $size->get_file_path() ) ) {
				$this->download_remote_file( $attachment_id, $size->get_file_path() );
				$this->files_to_remove[ $size_key ] = $size->get_file_path();
			}
		}

		$this->enable_back_s3_get_attached_file_filters();
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
			$this->logger->info( 'Media item is not valid.' );
		}

		return ! $invalid;
	}

	public function disable_s3_get_attached_file_filters() {
		// Make sure smush always gets local paths
		$this->disable_stream_wrapper_file();
		// S3 auto downloads an image when get_attached_file is called, we want to disable this, because we will explicitly download all media item sizes.
		$this->disable_s3_auto_download();
	}

	private function disable_stream_wrapper_file() {
		$priority = - 10; // Our callback needs to run before the s3 callback get_stream_wrapper_file
		add_filter( 'as3cf_get_attached_file', array( $this, 'return_local_file_path' ), $priority, 2 );
	}

	public function enable_back_s3_get_attached_file_filters() {
		$this->enable_back_stream_wrapper_file();
		$this->enable_back_s3_auto_download();
	}

	private function enable_back_stream_wrapper_file() {
		remove_filter( 'as3cf_get_attached_file', array( $this, 'return_local_file_path' ) );
	}

	public function return_local_file_path( $url, $file_path ) {
		return $file_path;
	}

	public function maybe_remove_downloaded_files() {
		if ( ! $this->wp_offload_media->get_setting( 'remove-local-file' ) ) {
			return;
		}

		foreach ( $this->files_to_remove as $file_path ) {
			if ( $this->fs->file_exists( $file_path ) ) {
				$this->fs->unlink( $file_path );
			}
		}
	}

	/**
	 * @param callable $callback
	 *
	 * @return void
	 */
	private function before_smush( $callback, $priority = 10 ) {
		add_action( 'wp_smush_before_smush_file', $callback, $priority );
	}

	/**
	 * @param callable $callback
	 *
	 * @return void
	 */
	private function after_smush( $callback, $priority = 10 ) {
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
		$s3_media_item  = $this->get_s3_media_item( $attachment_id );
		$remove_handler = $this->wp_offload_media->get_item_handler( 'remove-provider' );
		if ( $s3_media_item && $remove_handler && method_exists( $remove_handler, 'handle' ) ) {
			$remove_handler->handle( $s3_media_item, array( 'verify_exists_on_local' => false ) );
		}
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
}
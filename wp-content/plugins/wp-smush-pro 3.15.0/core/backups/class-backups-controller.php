<?php

namespace Smush\Core\Backups;

use Smush\Core\Controller;
use Smush\Core\Core;
use Smush\Core\File_System;
use Smush\Core\Helper;
use Smush\Core\Media\Media_Item_Cache;
use Smush\Core\Media\Media_Item_Optimizer;
use WP_Smush;

class Backups_Controller extends Controller {
	/**
	 * @var Media_Item_Cache
	 */
	private $media_item_cache;
	/**
	 * @var \WDEV_Logger|null
	 */
	private $logger;
	/**
	 * @var File_System
	 */
	private $fs;

	public function __construct() {
		$this->media_item_cache = Media_Item_Cache::get_instance();
		$this->logger           = Helper::logger();
		$this->fs               = new File_System();

		$this->register_action( 'wp_ajax_smush_restore_image', array( $this, 'handle_restore_ajax' ) );
		$this->register_action( 'delete_attachment', array( $this, 'delete_backup_file' ) );
	}

	public function handle_restore_ajax() {
		if ( empty( $_POST['attachment_id'] ) || empty( $_POST['_nonce'] ) ) {
			wp_send_json_error( array(
				'error_msg' => esc_html__( 'Error in processing restore action, fields empty.', 'wp-smushit' ),
			) );
		}

		$nonce_value   = filter_input( INPUT_POST, '_nonce', FILTER_SANITIZE_SPECIAL_CHARS );
		$attachment_id = filter_input( INPUT_POST, 'attachment_id', FILTER_SANITIZE_NUMBER_INT );

		if ( ! wp_verify_nonce( $nonce_value, "wp-smush-restore-$attachment_id" ) ) {
			wp_send_json_error( array(
				'error_msg' => esc_html__( 'Image not restored, nonce verification failed.', 'wp-smushit' ),
			) );
		}

		// Check capability.
		// TODO: change Helper::is_user_allowed to a non static method
		if ( ! Helper::is_user_allowed( 'upload_files' ) ) {
			wp_send_json_error( array(
				'error_msg' => esc_html__( "You don't have permission to work with uploaded files.", 'wp-smushit' ),
			) );
		}

		$attachment_id = (int) $attachment_id;
		$media_item    = Media_Item_Cache::get_instance()->get( $attachment_id );
		if ( ! $media_item->is_mime_type_supported() ) {
			wp_send_json_error( array(
				'error_msg' => $media_item->get_errors()->get_error_message(),
			) );
		}

		$optimizer = new Media_Item_Optimizer( $media_item );
		$restored  = $optimizer->restore();

		if ( ! $restored ) {
			wp_send_json_error( array(
				'error_msg' => esc_html__( 'Unable to restore image', 'wp-smushit' ),
			) );
		}

		$button_html = WP_Smush::get_instance()->library()->generate_markup( $attachment_id );
		$file_path   = $media_item->get_main_size()->get_file_path();
		$size        = $this->fs->file_exists( $file_path )
			? $this->fs->filesize( $file_path )
			: 0;
		if ( $size > 0 ) {
			$update_size = size_format( $size );
		}

		wp_send_json_success( array(
			'stats'    => $button_html,
			'new_size' => isset( $update_size ) ? $update_size : 0,
		) );
	}

	public function delete_backup_file( $attachment_id ) {
		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( $media_item->is_valid() && $media_item->get_default_backup_size() ) {
			// Delete the file
			$default_backup_path = $media_item->get_default_backup_size()->get_file_path();
			if ( $this->fs->file_exists( $default_backup_path ) ) {
				$this->fs->unlink( $default_backup_path );
			}

			// Delete the meta
			$media_item->remove_default_backup_size();
			$media_item->save();
		} else {
			$this->logger->error( sprintf( 'Count not delete webp versions of the media item [%d]', $attachment_id ) );
		}
	}
}
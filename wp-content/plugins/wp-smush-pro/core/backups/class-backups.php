<?php

namespace Smush\Core\Backups;

use Smush\Core\File_System;
use Smush\Core\Helper;
use Smush\Core\Media\Media_Item;
use Smush\Core\Media\Media_Item_Optimizer;
use Smush\Core\Settings;
use WDEV_Logger;

class Backups {
	/**
	 * @var WDEV_Logger
	 */
	private $logger;
	/**
	 * @var Settings|null
	 */
	private $settings;
	/**
	 * @var File_System
	 */
	private $fs;

	public function __construct() {
		$this->logger   = Helper::logger()->backup();
		$this->settings = Settings::get_instance();
		$this->fs       = new File_System();
	}

	public function create_backup_file( $source_image_path ) {
		$bak_file_path = $this->generate_unique_bak_file_path( $source_image_path );
		$copied        = $this->fs->copy( $source_image_path, $bak_file_path );
		if ( $copied ) {
			return basename( $bak_file_path );
		}

		return false;
	}

	private function generate_unique_bak_file_path( $source_image_path ) {
		// TODO: why not use the wp_unique_filename method for this?
		$path_info        = pathinfo( $source_image_path );
		$ext              = $path_info['extension'];
		$bak_ext          = ".bak.$ext";
		$file_without_ext = trailingslashit( $path_info['dirname'] ) . $path_info['filename'];
		$bak_file_path    = $file_without_ext . $bak_ext;
		if ( ! $this->fs->file_exists( $bak_file_path ) ) {
			return $bak_file_path;
		}

		$count         = 1;
		$bak_file_path = $file_without_ext . '-' . $count . $bak_ext;
		while ( $this->fs->file_exists( $bak_file_path ) ) {
			$count ++;
			$bak_file_path = $file_without_ext . '-' . $count . $bak_ext;
		}

		return $bak_file_path;
	}

	/**
	 * @param $media_item Media_Item
	 * @param $optimizer Media_Item_Optimizer
	 *
	 * @return bool
	 */
	public function maybe_create_backup( $media_item, $optimizer ) {
		if ( ! $this->settings->is_backup_active() ) {
			return false;
		}

		// Maybe it already exists?
		$backup_size = $media_item->get_default_backup_size();
		if ( $backup_size && $backup_size->get_file_path() && $this->fs->file_exists( $backup_size->get_file_path() ) ) {
			// We have a perfectly viable backup available already
			$this->logger->info( sprintf( 'Found an existing backed up file [%s] for attachment [%d].', $backup_size->get_file(), $media_item->get_id() ) );

			return false;
		}

		$size_to_backup = $media_item->get_full_or_scaled_size();
		if ( ! $size_to_backup || ! $size_to_backup->file_exists() ) {
			$this->logger->warning( sprintf( 'File not found, could not backup up for attachment [%d].', $media_item->get_id() ) );

			return false;
		}

		$create_copy = $optimizer->should_optimize_size( $size_to_backup );

		// Create the backup
		$file_name = $size_to_backup->get_file_name();
		$width     = $size_to_backup->get_width();
		$height    = $size_to_backup->get_height();
		if ( $create_copy ) {
			$file_name = $this->create_backup_file( $size_to_backup->get_file_path() );
			if ( ! $file_name ) {
				$this->logger->info( sprintf( 'File operation failed when trying to create backup file [%s] for attachment [%d].', $size_to_backup->get_file_path(), $media_item->get_id() ) );

				return false;
			}
		}
		$media_item->add_backup_size( $file_name, $width, $height );
		$media_item->save();

		return true;
	}

	/**
	 * @param $media_item Media_Item
	 *
	 * @return bool
	 */
	public function restore_backup( $media_item ) {
		do_action( 'wp_smush_before_restore_backup_attempt', $media_item->get_id() );

		return $this->restore_backup_to_file_path(
			$media_item,
			$media_item->get_original_image_path() // Directly using the path here because the size object is not available when the file doesn't exist on the disk
		);
	}

	/**
	 * @param $media_item Media_Item
	 * @param $file_path
	 *
	 * @return bool
	 */
	public function restore_backup_to_file_path( $media_item, $file_path ) {
		$restored         = false;
		$backup_file_path = '';
		$attachment_id    = $media_item->get_id();

		do {
			$backup_size = $media_item->get_default_backup_size();
			if ( ! $backup_size ) {
				$this->logger->warning( sprintf( 'A restore was attempted for attachment [%d] but we did not find a backup file.', $media_item->get_id() ) );

				break;
			}

			$backup_file_path = $backup_size->get_file_path();

			do_action( 'wp_smush_before_restore_backup', $backup_file_path, $attachment_id, $file_path );

			if ( ! $this->fs->file_exists( $backup_file_path ) ) {
				// Clean up
				$media_item->remove_default_backup_size();
				$media_item->save();

				$this->logger->warning( sprintf( 'A restore was attempted for attachment [%d] but the backup file does not exist.', $media_item->get_id() ) );

				break;
			}

			$is_separate_backup_file = $backup_file_path !== $file_path;
			if ( $is_separate_backup_file ) {
				$copied = $this->fs->copy( $backup_file_path, $file_path );
				if ( $copied ) {
					$this->fs->unlink( $backup_file_path );
				} else {
					$this->logger->warning( sprintf( 'Error copying from [%s] to [%s].', $backup_file_path, $file_path ) );

					break;
				}
			}

			wp_generate_attachment_metadata( $attachment_id, $file_path );
			/*
			 * TODO: we might want to follow media_handle_upload which makes an extra update attachment call like this:
			 * wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file ) );
			 */

			// The metadata has changed because we called wp_generate_attachment_metadata. We need to reset before saving.
			$media_item->reset();

			$media_item->remove_default_backup_size();
			$media_item->save();

			$restored = true;
		} while ( 0 );

		do_action( 'wp_smush_after_restore_backup', $restored, $backup_file_path, $attachment_id, $file_path );

		return $restored;
	}

	/**
	 * TODO: merge somehow with \Smush\Core\Modules\Backup::get_attachments_with_backups
	 * @return int
	 */
	private function count_attachments_with_backups() {
		global $wpdb;
		$wild            = '%';
		$backup_key_like = $wild . $wpdb->esc_like( Media_Item::DEFAULT_BACKUP_KEY ) . $wild;
		$no_backup_files = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT count(*)
				FROM {$wpdb->postmeta}
				WHERE meta_key=%s AND `meta_value` LIKE %s",
				Media_Item::BACKUP_SIZES_META_KEY,
				$backup_key_like
			)
		);

		return (int) $no_backup_files;
	}

	public function items_with_backup_exist() {
		return $this->count_attachments_with_backups() > 0;
	}
}
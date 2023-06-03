<?php

namespace Smush\Core\Backups;

use Smush\Core\Controller;
use Smush\Core\File_System;
use Smush\Core\Media\Media_Item;
use Smush\Core\Media\Media_Item_Cache;

class Backups_Backward_Compatibility extends Controller {
	const ORIGINAL_FILE_META_KEY = 'wp-smush-original_file';
	const PNG_PATH_INDEX = 'smush_png_path';

	private $media_item_cache;
	/**
	 * @var File_System
	 */
	private $fs;

	public function __construct() {
		$this->media_item_cache = Media_Item_Cache::get_instance();
		$this->fs               = new File_System();

		// TODO: deprecate the png2jpg backup hook in favor of the fallback size hook
	}

	public function init() {
		parent::init();

		add_action( 'delete_attachment', array( $this, 'delete_old_backup_files' ) );

		$this->add_backup_sizes_filter();
	}

	public function stop() {
		$this->remove_backup_sizes_filter();

		remove_action( 'delete_attachment', array( $this, 'delete_old_backup_files' ) );

		parent::stop();
	}

	public function maybe_use_deprecated_backup_sizes_meta( $original, $attachment_id, $meta_key ) {
		if ( $meta_key !== Media_Item::BACKUP_SIZES_META_KEY ) {
			return $original;
		}

		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( ! $media_item->is_image() ) {
			return $original;
		}

		$actual_backup_sizes_meta = $this->get_unfiltered_backup_sizes_meta( $attachment_id );
		if ( ! empty( $actual_backup_sizes_meta[ Media_Item::DEFAULT_BACKUP_KEY ] ) ) {
			/**
			 * If {@see Media_Item::DEFAULT_BACKUP_KEY} is already set then we don't want to overwrite it with an older, potentially inaccurate value
			 */
			return $original;
		}

		$new_meta = $this->maybe_use_original_file_meta( $media_item, $actual_backup_sizes_meta );
		if ( $new_meta ) {
			return array( $new_meta );
		}

		$new_meta = $this->maybe_use_smush_png_path( $media_item, $actual_backup_sizes_meta );
		if ( $new_meta ) {
			return array( $new_meta );
		}

		return $original;
	}

	private function maybe_use_original_file_meta( $media_item, $backup_sizes_meta ) {
		$attachment_id      = $media_item->get_id();
		$meta_key           = self::ORIGINAL_FILE_META_KEY;
		$original_file_meta = get_post_meta( $attachment_id, $meta_key, true );
		if ( empty( $original_file_meta ) ) {
			return false;
		}

		delete_post_meta( $attachment_id, $meta_key );

		$original_file_meta_path = $this->get_original_file_path( $original_file_meta );
		if ( ! $this->fs->file_exists( $original_file_meta_path ) ) {
			return false;
		}

		$new_backup_meta = $this->make_new_backup_meta(
			$backup_sizes_meta,
			$original_file_meta_path
		);
		$this->update_backup_sizes_meta( $attachment_id, $new_backup_meta );

		return $new_backup_meta;
	}

	private function maybe_use_smush_png_path( $media_item, $backup_sizes_meta ) {
		$attachment_id = $media_item->get_id();
		$png_path_key  = self::PNG_PATH_INDEX;
		if ( empty( $backup_sizes_meta[ $png_path_key ]['file'] ) ) {
			return false;
		}
		$smush_png_file = $backup_sizes_meta[ $png_path_key ]['file'];
		$smush_png_path = $this->file_name_to_path( $media_item, $smush_png_file );

		unset( $backup_sizes_meta[ $png_path_key ] );
		$backup_sizes_meta = $this->make_new_backup_meta( $backup_sizes_meta, $smush_png_path );
		$this->update_backup_sizes_meta( $attachment_id, $backup_sizes_meta );

		if ( $this->fs->file_exists( $smush_png_path ) ) {
			return $backup_sizes_meta;
		} else {
			return false;
		}
	}

	private function make_new_backup_meta( $existing_backup_meta, $file_path ) {
		list( $width, $height ) = $this->fs->getimagesize( $file_path );

		$existing_backup_meta[ Media_Item::DEFAULT_BACKUP_KEY ] = array(
			'file'   => basename( $file_path ),
			'width'  => $width,
			'height' => $height,
		);

		return $existing_backup_meta;
	}

	/**
	 * @param $attachment_id
	 *
	 * @return mixed
	 */
	private function get_unfiltered_backup_sizes_meta( $attachment_id ) {
		$this->remove_backup_sizes_filter();
		$post_meta = get_post_meta( $attachment_id, Media_Item::BACKUP_SIZES_META_KEY, true );
		$this->add_backup_sizes_filter();

		return empty( $post_meta ) ? array() : $post_meta;
	}

	private function update_backup_sizes_meta( $attachment_id, $meta_value ) {
		$this->remove_backup_sizes_filter();

		if ( empty( $meta_value ) ) {
			delete_post_meta( $attachment_id, Media_Item::BACKUP_SIZES_META_KEY );
		} else {
			update_post_meta( $attachment_id, Media_Item::BACKUP_SIZES_META_KEY, $meta_value );
		}

		$this->add_backup_sizes_filter();
	}

	/**
	 * @return void
	 */
	private function add_backup_sizes_filter() {
		add_action( 'get_post_metadata', array( $this, 'maybe_use_deprecated_backup_sizes_meta' ), 10, 3 );
	}

	/**
	 * @return void
	 */
	private function remove_backup_sizes_filter() {
		remove_filter( 'get_post_metadata', array( $this, 'maybe_use_deprecated_backup_sizes_meta' ) );
	}

	public function delete_old_backup_files( $attachment_id ) {
		$this->remove_backup_sizes_filter();
		$this->_delete_old_backup_files( $attachment_id );
		$this->add_backup_sizes_filter();
	}

	private function _delete_old_backup_files( $attachment_id ) {
		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( $media_item->is_valid() ) {
			$this->delete_original_file( $attachment_id, $media_item );
			$this->delete_png_meta_value_and_file( $media_item );
			$this->delete_backup_files_for_sizes( $media_item );
		}
	}

	/**
	 * @param $attachment_id
	 * @param Media_Item $media_item
	 *
	 * @return void
	 */
	private function delete_original_file( $attachment_id, $media_item ) {
		$meta_key           = self::ORIGINAL_FILE_META_KEY;
		$original_file_meta = get_post_meta( $attachment_id, $meta_key, true );
		if ( ! empty( $original_file_meta ) ) {
			$original_file_meta_path = $this->get_original_file_path( $original_file_meta );
			if ( $this->fs->file_exists( $original_file_meta_path ) ) {
				$this->fs->unlink( $original_file_meta_path );
			}
		}
	}

	private function get_original_file_path( $original_file_meta ) {
		$upload_dir = wp_upload_dir();
		$basedir    = trailingslashit( $upload_dir['basedir'] );
		return path_join( $basedir, $original_file_meta );
	}

	private function delete_png_meta_value_and_file( $media_item ) {
		$attachment_id     = $media_item->get_id();
		$png_path_key      = self::PNG_PATH_INDEX;
		$backup_sizes_meta = $this->get_unfiltered_backup_sizes_meta( $attachment_id );
		if ( empty( $backup_sizes_meta[ $png_path_key ]['file'] ) ) {
			return;
		}
		$smush_png_file = $backup_sizes_meta[ $png_path_key ]['file'];
		$smush_png_path = $this->file_name_to_path( $media_item, $smush_png_file );

		unset( $backup_sizes_meta[ $png_path_key ] );
		$backup_sizes_meta = $this->make_new_backup_meta( $backup_sizes_meta, $smush_png_path );
		$this->update_backup_sizes_meta( $attachment_id, $backup_sizes_meta );

		if ( $this->fs->file_exists( $smush_png_path ) ) {
			$this->fs->unlink( $smush_png_path );
		}
	}

	/**
	 * @param Media_Item $media_item
	 * @param $original_file_meta
	 *
	 * @return string
	 */
	private function file_name_to_path( $media_item, $original_file_meta ) {
		return path_join( $media_item->get_dir(), $original_file_meta );
	}

	/**
	 * @param $media_item Media_Item
	 *
	 * @return void
	 */
	private function delete_backup_files_for_sizes( $media_item ) {
		foreach ( $media_item->get_sizes() as $size ) {
			$backup_file_path = $size->get_dir() . $size->get_file_name_without_extension() . '.bak.' . $size->get_extension();
			if ( $this->fs->file_exists( $backup_file_path ) ) {
				$this->fs->unlink( $backup_file_path );
			}
		}
	}
}
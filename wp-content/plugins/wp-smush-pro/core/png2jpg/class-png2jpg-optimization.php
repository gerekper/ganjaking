<?php

namespace Smush\Core\Png2Jpg;

use Smush\Core\Backups\Backups;
use Smush\Core\File_System;
use Smush\Core\Helper;
use Smush\Core\Media\Media_Item;
use Smush\Core\Media\Media_Item_Optimization;
use Smush\Core\Media\Media_Item_Size;
use Smush\Core\Media\Media_Item_Stats;
use Smush\Core\Settings;
use Smush\Core\Upload_Dir;
use WP_Error;

class Png2Jpg_Optimization extends Media_Item_Optimization {
	const KEY = 'png2jpg_optimization';
	const PNG2JPG_SAVINGS_KEY = 'wp-smush-pngjpg_savings';
	const CONVERTED_PNG_FILES_META = 'converted_png_files';
	/**
	 * @var Media_Item
	 */
	private $media_item;
	/**
	 * @var array
	 */
	private $meta;
	/**
	 * @var Media_Item_Stats[]
	 */
	private $size_stats = array();

	private $converted_png_files;

	private $reset_properties = array(
		'meta',
		'size_stats',
		'converted_png_files',
	);

	private $logger;
	private $backups;
	/**
	 * @var WP_Error
	 */
	private $errors;
	/**
	 * @var Settings
	 */
	private $settings;
	/**
	 * @var Png2Jpg_Helper
	 */
	private $helper;
	/**
	 * @var File_System
	 */
	private $fs;
	/**
	 * @var Upload_Dir
	 */
	private $upload_dir;

	public function __construct( $media_item ) {
		$this->media_item = $media_item;
		$this->logger     = Helper::logger()->png2jpg();
		$this->backups    = new Backups();
		$this->settings   = Settings::get_instance();
		$this->errors     = new WP_Error();
		$this->helper     = new Png2Jpg_Helper();
		$this->fs         = new File_System();
		$this->upload_dir = new Upload_Dir();
	}

	public function get_key() {
		return self::KEY;
	}

	public function get_stats() {
		$stats       = new Media_Item_Stats();
		$size_before = 0;
		$size_after  = 0;
		foreach ( $this->get_sizes_to_convert( $this->media_item ) as $size_key => $size ) {
			$size_stats  = $this->get_size_stats( $size_key );
			$size_before += $size_stats->get_size_before();
			$size_after  += $size_stats->get_size_after();
		}

		if ( empty( $size_before ) || empty( $size_after ) ) {
			return $stats;
		}

		$stats->set_size_before( $size_before );
		$stats->set_size_after( $size_after );

		return $stats;
	}

	public function get_size_stats( $size_key ) {
		if ( empty( $this->size_stats[ $size_key ] ) ) {
			$this->size_stats[ $size_key ] = $this->prepare_size_stats( $size_key );
		}

		return $this->size_stats[ $size_key ];
	}

	public function save() {
		$meta = $this->make_meta();
		if ( ! empty( $meta ) ) {
			update_post_meta( $this->media_item->get_id(), self::PNG2JPG_SAVINGS_KEY, $meta );
			$this->reset();
		}
	}

	/**
	 * @return array
	 */
	private function make_meta() {
		$meta = array();
		foreach ( $this->get_sizes_to_convert( $this->media_item ) as $size_key => $size ) {
			$size_stats = $this->get_size_stats( $size_key );
			if ( ! $size_stats->is_empty() ) {
				$meta[ $size_key ] = $size_stats->to_array();
			}
		}

		if ( ! empty( $this->get_converted_png_files() ) ) {
			$meta[ self::CONVERTED_PNG_FILES_META ] = $this->get_converted_png_files();
		}

		return $meta;
	}

	public function is_optimized() {
		return ! $this->get_stats()->is_empty();
	}

	public function should_optimize() {
		if (
			$this->media_item->is_skipped()
			|| $this->media_item->has_errors()
			|| ! $this->settings->is_png2jpg_module_active()
		) {
			return false;
		}

		return $this->can_be_converted( $this->media_item );
	}

	public function should_reoptimize() {
		// PNG 2 JPG conversion happens only once so this is the same as should_optimize
		return $this->should_optimize();
	}

	public function optimize() {
		if ( ! $this->should_optimize() ) {
			return false;
		}

		return $this->convert_media_item( $this->media_item );
	}

	private function get_meta() {
		if ( is_null( $this->meta ) ) {
			$this->meta = $this->fetch_meta();
		}

		return $this->meta;
	}

	private function fetch_meta() {
		$post_meta = get_post_meta( $this->media_item->get_id(), self::PNG2JPG_SAVINGS_KEY, true );

		return empty( $post_meta ) || ! is_array( $post_meta )
			? array()
			: $post_meta;
	}

	private function get_size_meta( $size_key ) {
		$meta = $this->get_meta();
		$size = empty( $meta[ $size_key ] )
			? array()
			: $meta[ $size_key ];

		return empty( $size ) || ! is_array( $size )
			? array()
			: $size;
	}

	private function prepare_size_stats( $size_key ) {
		$stats = new Media_Item_Stats();
		$stats->from_array( $this->get_size_meta( $size_key ) );

		return $stats;
	}

	/**
	 * @param $media_item Media_Item
	 *
	 * @return boolean
	 */
	private function can_be_converted( $media_item ) {
		$id   = $media_item->get_id();
		$file = $media_item->get_full_or_scaled_size()->get_file_path();
		if ( ! $media_item->is_png() ) {
			$this->logger->info( sprintf( 'File [%s(%d)] does not have the PNG mime-type.', $file, $id ) );

			return false;
		}

		if ( ! $this->helper->supports_imagick() && ! $this->helper->supports_gd() ) {
			$this->logger->warning( 'The site does not support Imagick or GD.' );

			return false;
		}

		$is_optimized = $this->is_optimized();
		if ( $is_optimized ) {
			$this->logger->info( sprintf( 'File [%s(%d)] already tried the conversion.', $file, $id ) );

			return false;
		}

		/**
		 * Filter whether to convert the PNG to JPG or not
		 *
		 * @param bool $should_convert Current choice for image conversion
		 * @param int $id Attachment id
		 * @param string $file File path for the image
		 * @param string $size Image size being converted
		 *
		 * @since 2.4
		 */
		return apply_filters( 'wp_smush_convert_to_jpg', ! $media_item->is_transparent(), $id, $file, 'full' );
	}

	/**
	 * @param $media_item Media_Item
	 *
	 * @return boolean
	 */
	private function convert_media_item( $media_item ) {
		$old_urls               = $media_item->get_size_urls();
		$converted_source_files = array();
		$converted_sizes        = array();
		$sizes                  = $this->get_sizes_to_convert( $media_item ); // We must convert all sizes, regardless of which sizes the user has selected for smushing
		$old_main_file_name     = $media_item->get_full_or_scaled_size()->get_file_name();
		$new_main_file_name     = $this->get_main_jpg_file_name();

		foreach ( $sizes as $size_key => $size ) {
			$size_file_path         = $size->get_file_path();
			$file_already_converted = ! empty( $converted_source_files[ $size_file_path ] );
			$new_size_file_name     = $this->get_size_jpg_file_name( $size, $old_main_file_name, $new_main_file_name );
			if ( $file_already_converted ) {
				// The file for the current size was already converted under a different size, just copy the stats.
				$this->copy_size(
					$media_item,
					$converted_source_files[ $size_file_path ],
					$size_key
				);
				$converted_sizes[] = $size_key;
			} else {
				// Convert the file for the current size.
				$converted_source_files[ $size_file_path ] = $size_key;
				$size_converted                            = $this->convert_size( $media_item, $size, $new_size_file_name );
				if ( $size_converted ) {
					$converted_sizes[] = $size_key;
				}
			}
		}

		if ( count( $converted_sizes ) === count( $sizes ) ) {
			$png_file_paths = array_flip( $converted_source_files );

			$this->delete_files( $png_file_paths );

			// All sizes successful, save media item.
			$media_item->set_mime_type( 'image/jpeg' );
			$media_item->save();

			// Save optimization data.
			$this->set_converted_png_files( $this->relative_paths( $png_file_paths ) );
			$this->save();

			$media_item_stats = $this->get_stats();
			do_action(
				'wp_smush_png_jpg_converted',
				$media_item->get_id(),
				$media_item->get_wp_metadata(),
				$media_item_stats ? $media_item_stats->to_array() : array(),
				$png_file_paths
			);

			$this->replace_urls_in_content( $old_urls, $media_item->get_size_urls() );

			return true;
		} else {
			// We can't have some sizes as pngs and others as jpgs, if this is the case then we must delete the successfully converted files
			$converted_files_to_delete = array_map( function ( $converted_size ) use ( $media_item ) {
				return $media_item->get_size( $converted_size )->get_file_path();
			}, $converted_sizes );
			$this->delete_files( $converted_files_to_delete );

			// Reset all the changes made so far.
			$media_item->reset();
			$this->reset();

			return false;
		}
	}

	/**
	 * @param $media_item Media_Item
	 * @param $media_item_size Media_Item_Size
	 * @param $new_file_name
	 *
	 * @return boolean
	 */
	private function convert_size( $media_item, $media_item_size, $new_file_name ) {
		$new_file_path = $media_item->get_dir() . $new_file_name;

		$result = $this->write_file_for_size( $media_item, $media_item_size, $new_file_path );
		if ( ! $result || empty( $result['filesize'] ) ) {
			return false;
		}

		$size_before = $media_item_size->get_filesize();
		$size_after  = $result['filesize'];
		if ( $size_after > $size_before ) {
			$this->fs->unlink( $new_file_path );
			$this->add_error(
				$media_item_size->get_key(),
				'converted_image_larger',
				__( 'Skipped: Smushed file is larger than the original file.', 'wp-smushit' )
			);

			$this->logger->error(
				sprintf(
					/* translators: 1: Converted path, 2: Converted file size, 3: Original path, 4: Original file size */
					__( 'The new file [%1$s](%2$s) is larger than the original file [%3$s](%4$s).', 'wp-smushit' ),
					$this->upload_dir->get_human_readable_path( $new_file_path ),
					size_format( $size_after ),
					$this->upload_dir->get_human_readable_path( $media_item_size->get_file_path() ),
					size_format( $size_before )
				)
			);

			return false;
		}

		$media_item_size->set_file_name( $new_file_name );
		$media_item_size->set_mime_type( 'image/jpeg' );
		$media_item_size->set_filesize( $size_after );

		$size_stats = $this->get_size_stats( $media_item_size->get_key() );
		$size_stats->set_size_before( $size_before );
		$size_stats->set_size_after( $size_after );

		$this->logger->info( sprintf( 'Image [%s] converted from PNG with size [%d] to JPG with size [%d].', $new_file_name, $size_before, $size_after ) );

		do_action(
			'wp_smush_png_jpg_size_converted',
			$media_item->get_id(),
			$media_item_size->get_key(),
			$size_stats->to_array()
		);

		return true;
	}

	/**
	 * @param $media_item Media_Item
	 * @param $media_item_size Media_Item_Size
	 * @param $new_file_path string
	 *
	 * @return array|false
	 */
	private function write_file_for_size( $media_item, $media_item_size, $new_file_path ) {
		$editor = wp_get_image_editor( $media_item_size->get_file_path() );

		$this->logger->info( sprintf( 'Image editor [%s] selected for PNG 2 JPG conversion.', get_class( $editor ) ) );

		if ( is_wp_error( $editor ) ) {
			$this->add_error(
				$media_item_size->get_key(),
				'image_load_error',
				sprintf(
				/* translators: 1: Image path, 2: Image id, 3: Error message. */
					__( 'Image Editor cannot load file [%1$s(%2$d)]: %3$s.', 'wp-smushit' ),
					$this->upload_dir->get_human_readable_path( $media_item_size->get_file_path() ),
					$media_item->get_id(),
					$editor->get_error_message()
				)
			);

			return false;
		}

		$new_image_data = $editor->save( $new_file_path, 'image/jpeg' );
		if ( is_wp_error( $new_image_data ) ) {
			$this->add_error(
				$media_item_size->get_key(),
				'image_save_error',
				/* translators: %s: Error message. */
				sprintf( __( 'The image editor was unable to save the image: %s', 'wp-smushit' ), $new_image_data->get_error_message() )
			);

			return false;
		}

		return $new_image_data;
	}

	private function delete_files( $files ) {
		foreach ( $files as $file ) {
			if ( $this->fs->file_exists( $file ) ) {
				// TODO: create an S3 compatible method for this
				$this->fs->unlink( $file );
			}
		}
	}

	/**
	 * TODO: add the action wp_smush_image_url_updated
	 *
	 * @param $old_url
	 * @param $new_url
	 *
	 * @return bool
	 */
	private function replace_url_in_content( $old_url, $new_url ) {
		global $wpdb;
		$wild         = '%';
		$old_url_like = $wild . $wpdb->esc_like( $old_url ) . $wild;
		$query        = $wpdb->prepare( "SELECT ID, post_content FROM $wpdb->posts WHERE post_content LIKE %s", $old_url_like );
		$rows         = $wpdb->get_results( $query );
		if ( empty( $rows ) || ! is_array( $rows ) ) {
			return true;
		}

		$update_count = 0;
		foreach ( $rows as $row ) {
			// Replace old URLs with new URLs.
			$post_content = $row->post_content;
			$post_content = str_replace( $old_url, $new_url, $post_content );
			// Update Post content.
			$updated = $wpdb->update(
				$wpdb->posts,
				array( 'post_content' => $post_content ),
				array( 'ID' => $row->ID )
			);
			if ( $updated ) {
				$update_count ++;
			}
			clean_post_cache( $row->ID );
		}

		// TODO: do something with this return value, see what we were doing in the legacy code
		return $update_count === count( $rows );
	}

	public function reset() {
		foreach ( $this->reset_properties as $property ) {
			$this->$property = null;
		}
	}

	private function copy_size( $media_item, $source_size_key, $destination_size_key ) {
		$source_size  = $media_item->get_size( $source_size_key );
		$source_stats = $this->get_size_stats( $source_size_key );

		$destination_size  = $media_item->get_size( $destination_size_key );
		$destination_stats = $this->get_size_stats( $destination_size_key );

		$destination_size->set_file_name( $source_size->get_file_name() );
		$destination_size->set_mime_type( $source_size->get_mime_type() );
		$destination_size->set_filesize( $source_stats->get_size_after() );

		$destination_stats->from_array( $source_stats->to_array() );
	}

	/**
	 * @param array $old_urls
	 * @param array $new_urls
	 *
	 * @return void
	 */
	private function replace_urls_in_content( $old_urls, $new_urls ) {
		foreach ( $old_urls as $size_key => $old_size_url ) {
			if ( empty( $new_urls[ $size_key ] ) ) {
				continue;
			}
			$new_size_url = $new_urls[ $size_key ];
			$this->replace_url_in_content( $old_size_url, $new_size_url );
		}
	}

	public function delete_data() {
		delete_post_meta( $this->media_item->get_id(), self::PNG2JPG_SAVINGS_KEY );

		$this->reset();
	}

	public function should_optimize_size( $size ) {
		if ( ! $this->should_optimize() ) {
			return false;
		}

		return array_key_exists(
			$size->get_key(),
			$this->get_sizes_to_convert( $this->media_item )
		);
	}

	/**
	 * @param Media_Item $media_item
	 *
	 * @return array|Media_Item_Size[]
	 */
	private function get_sizes_to_convert( $media_item ) {
		return $media_item->get_sizes();
	}

	public function get_converted_png_files() {
		if ( is_null( $this->converted_png_files ) ) {
			$this->converted_png_files = $this->prepare_converted_png_files();
		}

		return $this->converted_png_files;
	}

	private function prepare_converted_png_files() {
		$meta = $this->get_meta();

		return empty( $meta[ self::CONVERTED_PNG_FILES_META ] )
			? array()
			: $meta[ self::CONVERTED_PNG_FILES_META ];
	}

	public function set_converted_png_files( $converted_png_files ) {
		$this->converted_png_files = $converted_png_files;
	}

	public function can_restore() {
		// If it is optimized then we can restore it
		return $this->is_optimized();
	}

	public function restore() {
		$media_item        = $this->media_item;
		$jpg_urls          = $media_item->get_size_urls();
		$jpg_paths         = $media_item->get_size_paths();
		$restore_file_path = $this->get_restore_file_path();
		$after_restore     = function ( $restored ) use ( $media_item, $jpg_urls, $jpg_paths, $restore_file_path ) {
			// We are doing these changes in a callback so that the other callbacks hooked to wp_smush_after_restore_backup will receive the most up-to-date state of the media item
			if ( $restored ) {
				// Undo all the changes we did during the conversion
				$media_item->get_main_size()->set_file_name( basename( $restore_file_path ) );
				$media_item->set_mime_type( 'image/png' );
				$media_item->save();

				$this->replace_urls_in_content( $jpg_urls, $media_item->get_size_urls() );
				$this->delete_files( $jpg_paths );

				/**
				 * The DB data will be deleted by {@see delete_data}
				 */
			}
		};

		add_action( 'wp_smush_after_restore_backup', $after_restore, - 10 );
		$restored = $this->backups->restore_backup_to_file_path( $media_item, $restore_file_path );
		remove_action( 'wp_smush_after_restore_backup', $after_restore );

		return $restored;
	}

	private function get_restore_file_path() {
		$media_item          = $this->media_item;
		$converted_png_files = $this->get_converted_png_files();
		$default_backup_size = $media_item->get_default_backup_size();
		$check_file_exists   = true;

		if ( ! empty( $converted_png_files['full'] ) ) {
			// First try to get the original file name from converted file meta
			$file_name = basename( $converted_png_files['full'] );
		} elseif ( $default_backup_size ) {
			// If converted meta didn't work out then
			$backup_file_path = $default_backup_size->get_file_path();
			if ( strpos( $backup_file_path, '.bak' ) ) {
				$backup_file_path = str_replace( '.bak', '', $backup_file_path );
			} else {
				// If the default backup path does not have .bak extension then we don't need to do wp_unique_filename because we know that the file at the restore path is our file
				$check_file_exists = false;
			}
			$file_name = basename( $backup_file_path );
		} else {
			$full_size = $media_item->get_full_size();
			$file_name = $full_size->get_file_name_without_extension() . '.png';
		}

		$restore_file_path = path_join( $media_item->get_dir(), $file_name );
		if ( $check_file_exists && $this->fs->file_exists( $restore_file_path ) ) {
			$unique_filename = wp_unique_filename( $media_item->get_dir(), $file_name );

			return path_join( $media_item->get_dir(), $unique_filename );
		} else {
			return $restore_file_path;
		}
	}

	private function add_error( $size_key, $code, $message ) {
		// Log the error
		$this->logger->error( $message );
		// Add the error

		if ( $size_key ) {
			$message = "[$size_key] $message";
		}
		$this->errors->add( $code, $message );
	}

	public function get_errors() {
		return $this->errors;
	}

	private function relative_paths( $absolute_paths ) {
		$relative_paths = array();
		foreach ( $absolute_paths as $key => $absolute_path ) {
			$dir                    = $this->media_item->get_relative_file_dir();
			$file                   = wp_basename( $absolute_path );
			$relative_paths[ $key ] = "$dir/$file";
		}

		return $relative_paths;
	}

	private function get_main_jpg_file_name() {
		$media_item     = $this->media_item;
		$full_size      = $media_item->get_full_or_scaled_size();
		$png_extension  = '.' . pathinfo( $full_size->get_file_path(), PATHINFO_EXTENSION );
		$full_file_name = str_replace( $png_extension, '.jpg', $full_size->get_file_name() );

		return $this->fs->file_exists( $media_item->get_dir() . $full_file_name )
			? wp_unique_filename( $media_item->get_dir(), $full_file_name )
			: $full_file_name;
	}

	/**
	 * @param $size Media_Item_Size
	 * @param $main_png_file_name string
	 * @param $main_jpg_file_name string
	 *
	 * @return string
	 */
	private function get_size_jpg_file_name( $size, $main_png_file_name, $main_jpg_file_name ) {
		$png_extension   = '.' . pathinfo( $main_png_file_name, PATHINFO_EXTENSION );
		$png_without_ext = str_replace( $png_extension, '', $main_png_file_name );
		$jpg_without_ext = str_replace( '.jpg', '', $main_jpg_file_name );
		$size_file_name  = str_replace( $png_without_ext, $jpg_without_ext, $size->get_file_name() );
		$size_file_name  = str_replace( $png_extension, '.jpg', $size_file_name );

		return $this->fs->file_exists( $size->get_dir() . $size_file_name )
			? wp_unique_filename( $size->get_dir(), $size_file_name )
			: $size_file_name;
	}

	public function get_optimized_sizes_count() {
		$count = 0;
		$sizes = $this->get_sizes_to_convert( $this->media_item );
		foreach ( $sizes as $size_key => $size ) {
			$size_stats = $this->get_size_stats( $size_key );
			if ( $size_stats && ! $size_stats->is_empty() ) {
				$count ++;
			}
		}

		return $count;
	}
}
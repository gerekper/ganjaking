<?php

namespace Smush\Core\Resize;

use Smush\Core\File_System;
use Smush\Core\Helper;
use Smush\Core\Media\Media_Item;
use Smush\Core\Media\Media_Item_Optimization;
use Smush\Core\Media\Media_Item_Size;
use Smush\Core\Media\Media_Item_Stats;
use Smush\Core\Settings;
use Smush\Core\Upload_Dir;
use WDEV_Logger;
use WP_Error;
use WP_Image_Editor;

class Resize_Optimization extends Media_Item_Optimization {
	const KEY = 'resize_optimization';
	const META_KEY = 'wp-smush-resize_savings';
	/**
	 * @var Media_Item
	 */
	private $media_item;
	/**
	 * @var Settings
	 */
	private $settings;
	/**
	 * @var \stdClass
	 */
	private $resize_dimensions;
	/**
	 * @var WDEV_Logger
	 */
	private $logger;
	/**
	 * @var WP_Image_Editor[]
	 */
	private $implementations;
	/**
	 * @var Media_Item_Stats
	 */
	private $stats;
	private $savings_meta;
	private $size_stats;
	/**
	 * @var WP_Error
	 */
	private $errors;
	private $reset_properties = array(
		'stats',
		'savings_meta',
		'size_stats',
	);
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
		$this->settings   = Settings::get_instance();
		$this->logger     = Helper::logger()->resize();
		$this->errors     = new WP_Error();
		$this->fs         = new File_System();
		$this->upload_dir = new Upload_Dir();
	}

	public function get_key() {
		return self::KEY;
	}

	public function get_stats() {
		if ( is_null( $this->stats ) ) {
			$this->stats = $this->prepare_stats();
		}

		return $this->stats;
	}

	public function get_size_stats( $size_key ) {
		if ( is_null( $this->size_stats ) ) {
			$this->size_stats = new Media_Item_Stats();
		}

		return $this->size_stats;
	}

	public function save() {
		$meta = $this->make_meta();
		if ( ! empty( $meta ) ) {
			update_post_meta( $this->media_item->get_id(), self::META_KEY, $meta );
			$this->reset();
		}
	}

	private function make_meta() {
		$stats = $this->get_stats();

		return $stats->is_empty() ? array() : $stats->to_array();
	}

	public function is_optimized() {
		return ! $this->get_stats()->is_empty();
	}

	public function should_optimize() {
		if (
			$this->media_item->is_skipped()
			|| $this->media_item->has_errors()
			|| ! $this->settings->is_resize_module_active()
		) {
			return false;
		}

		return apply_filters(
			'wp_smush_resize_uploaded_image',
			$this->_should_optimize(),
			$this->media_item->get_id(),
			$this->get_savings_meta()
		);
	}

	private function _should_optimize() {
		$size          = $this->get_size_to_resize();
		$dimensions    = $this->get_resize_dimensions();
		$target_width  = (int) $dimensions->width;
		$target_height = (int) $dimensions->height;

		if ( strpos( $size->get_file_path(), 'noresize' ) !== false ) {
			return false;
		}

		$width_resizable  = $target_width > 0
		                    && $size->get_width() > $target_width
		                    && ! wp_fuzzy_number_match( $target_width, $size->get_width() );
		$height_resizable = $target_height > 0
		                    && $size->get_height() > $target_height
		                    && ! wp_fuzzy_number_match( $target_height, $size->get_height() );
		if ( $width_resizable || $height_resizable ) {
			return true;
		}

		return false;
	}

	public function should_reoptimize() {
		return $this->should_optimize();
	}

	public function optimize() {
		if ( ! $this->should_optimize() ) {
			return false;
		}

		return $this->resize_image();
	}

	/**
	 * @return Media_Item_Size
	 */
	private function get_size_to_resize() {
		return $this->media_item->get_main_size();
	}

	/**
	 * TODO: maybe it should only resize the full image and not the scaled image
	 *
	 * @return bool
	 */
	private function resize_image() {
		$media_item     = $this->media_item;
		$id             = $media_item->get_id();
		$size_to_resize = $this->get_size_to_resize();
		if ( ! $size_to_resize ) {
			/* translators: %d: Image id. */
			$this->add_error( 'no_size', sprintf( __( 'Could not find a suitable source image for resizing media item [%d].', 'wp-smushit' ), $id ) );

			return false;
		}

		$this->include_implementations();
		foreach ( $this->get_implementations() as $implementation ) {
			$data = $this->try_with_implementation( $implementation );
			if ( ! empty( $data['file'] ) ) {
				break;
			}
		}

		$original_path = $size_to_resize->get_file_path();
		if ( empty( $data['file'] ) ) {
			/* translators: 1: Original path, 2: Image id. */
			$this->add_error( 'resize_failed', sprintf( __( 'Cannot resize image [%1$s(%2$d)].', 'wp-smushit' ), $this->upload_dir->get_human_readable_path( $original_path ), $id ) );

			return false;
		}

		$new_path = path_join( dirname( $original_path ), $data['file'] );
		if ( ! $this->fs->file_exists( $new_path ) ) {
			/* translators: %s: Resized path */
			$this->add_error( 'resized_image_not_found', sprintf( __( 'The resized image [%s] does not exist.', 'wp-smushit' ), $this->upload_dir->get_human_readable_path( $new_path ) ) );

			return false;
		}

		$original_filesize = $size_to_resize->get_filesize();
		$new_filesize      = ! empty( $data['filesize'] )
			? $data['filesize']
			: $this->fs->filesize( $new_path );
		if ( $new_filesize > $original_filesize ) {
			$this->delete_file( $new_path );
			$this->add_error(
				'no_savings',
				__( 'Skipped: Smushed file is larger than the original file.', 'wp-smushit' )
			);

			$this->logger->error(
				sprintf(
					/* translators: 1: Resized path, 2: Resized file size, 3: Original path, 4: Image id, 5: Original file size */
					__( 'The resized image [%1$s](%2$s) is larger than the original image [%3$s(%4$d)](%5$s).', 'wp-smushit' ),
					$this->upload_dir->get_human_readable_path( $new_path ),
					size_format( $new_filesize ),
					$this->upload_dir->get_human_readable_path( $original_path ),
					$id,
					size_format( $original_filesize )
				)
			);

			return false;
		}

		$copied = $this->fs->copy( $new_path, $original_path );
		if ( ! $copied ) {
			$this->add_error(
				'copy_failed',
				sprintf(
				/* translators: 1: Resized path, 2: Original path. */
					__( 'Failed to copy from [%1$s] to [%2$s]', 'wp-smushit' ),
					$this->upload_dir->get_human_readable_path( $new_path ),
					$this->upload_dir->get_human_readable_path( $original_path )
				)
			);

			return false;
		}

		// Delete intermediate file.
		$this->maybe_delete_file( $new_path );

		// Update media item.
		$size_to_resize->set_filesize( $new_filesize );
		$size_to_resize->set_width( $data['width'] );
		$size_to_resize->set_height( $data['height'] );
		$this->media_item->save();

		// Update the stats.
		$stats = $this->get_stats();
		$stats->set_size_before( $original_filesize );
		$stats->set_size_after( $new_filesize );

		// Save resize meta.
		$this->save();

		do_action( 'wp_smush_image_resized', $id, $stats->to_array() );

		return true;
	}

	private function maybe_delete_file( $file_path ) {
		$should_delete_file = true;
		foreach ( $this->media_item->get_sizes() as $size ) {
			if ( $size->get_file_path() === $file_path ) {
				$should_delete_file = false;
				break;
			}
		}

		if ( $should_delete_file ) {
			$this->delete_file( $file_path );
		}
	}

	private function delete_file( $file_path ) {
		if ( $this->fs->file_exists( $file_path ) ) {
			$this->fs->unlink( $file_path );
		}
	}

	public function get_errors() {
		return $this->errors;
	}

	private function add_error( $code, $message ) {
		$size     = $this->get_size_to_resize();
		$size_key = $size ? $size->get_key() : 'full';

		// Log the error
		$this->logger->error( $message );
		// Add the error
		$this->errors->add( $code, "[$size_key] $message" );
	}

	private function get_implementations() {
		if ( is_null( $this->implementations ) ) {
			$this->implementations = $this->prepare_implementations();
		}

		return $this->implementations;
	}

	private function prepare_implementations() {
		$implementations = array(
			'WP_Image_Editor_Imagick',
			'WP_Image_Editor_GD',
		);
		$supported       = array();
		foreach ( $implementations as $implementation ) {
			if ( class_exists( $implementation ) && call_user_func( array( $implementation, 'test' ) ) ) {
				$supported[] = $implementation;
			}
		}

		return $supported;
	}

	/**
	 * @return \stdClass
	 */
	private function get_resize_dimensions() {
		if ( is_null( $this->resize_dimensions ) ) {
			$this->resize_dimensions = $this->prepare_resize_dimensions();
		}

		return $this->resize_dimensions;
	}

	private function prepare_resize_dimensions() {
		$dimensions = $this->settings->get_setting( 'wp-smush-resize_sizes', array() );
		$dimensions = apply_filters(
			'wp_smush_resize_sizes',
			array(
				'width'  => empty( $dimensions['width'] ) ? 0 : (int) $dimensions['width'],
				'height' => empty( $dimensions['height'] ) ? 0 : (int) $dimensions['height'],
			),
			$this->get_size_to_resize()->get_file_path(),
			$this->media_item->get_id()
		);

		return (object) $dimensions;
	}

	private function try_with_implementation( $implementation ) {
		$editors_callback = function () use ( $implementation ) {
			return array( $implementation );
		};
		add_filter( 'wp_image_editors', $editors_callback );
		$file_path  = $this->get_size_to_resize()->get_file_path();
		$dimensions = $this->get_resize_dimensions();
		$return     = image_make_intermediate_size( $file_path, $dimensions->width, $dimensions->height );
		remove_filter( 'wp_image_editors', $editors_callback );

		return $return;
	}

	private function prepare_stats() {
		$stats = new Media_Item_Stats();
		$stats->from_array( $this->get_savings_meta() );

		return $stats;
	}

	private function get_savings_meta() {
		if ( is_null( $this->savings_meta ) ) {
			$this->savings_meta = $this->prepare_savings_meta();
		}

		return $this->savings_meta;
	}

	private function prepare_savings_meta() {
		$meta = get_post_meta( $this->media_item->get_id(), self::META_KEY, true );

		return empty( $meta )
			? array()
			: $meta;
	}

	/**
	 * @param Settings $settings
	 */
	public function set_settings( $settings ) {
		$this->settings = $settings;
	}

	public function delete_data() {
		delete_post_meta( $this->media_item->get_id(), self::META_KEY );

		$this->reset();
	}

	public function should_optimize_size( $size ) {
		if ( ! $this->should_optimize() ) {
			return false;
		}

		return $this->get_size_to_resize()->get_key() === $size->get_key();
	}

	private function reset() {
		foreach ( $this->reset_properties as $property ) {
			$this->$property = null;
		}
	}

	/**
	 * @return void
	 */
	private function include_implementations() {
		// Calling this method includes the necessary files
		_wp_image_editor_choose();
	}

	public function get_optimized_sizes_count() {
		// We always resize the largest available size only
		return $this->is_optimized() ? 1 : 0;
	}
}
<?php

namespace Smush\Core\Media;

use Smush\Core\Animated_Status_Controller;
use Smush\Core\Array_Utils;
use Smush\Core\Backup_Size;
use Smush\Core\File_System;
use Smush\Core\Helper;
use Smush\Core\Settings;
use Smush\Core\Smush_File;
use WP_Error;
use WP_Smush;

class Media_Item extends Smush_File {
	const ANIMATED_META_KEY = 'wp-smush-animated';
	const TRANSPARENT_META_KEY = 'wp-smush-transparent';
	const IGNORED_META_KEY = 'wp-smush-ignore-bulk';

	const SIZE_KEY_SCALED = 'wp_scaled';
	const SIZE_KEY_FULL = 'full';
	const BACKUP_SIZES_META_KEY = '_wp_attachment_backup_sizes';
	const DEFAULT_BACKUP_KEY = 'smush-full';

	private $id;
	/**
	 * @var array|false
	 */
	private $metadata;
	/**
	 * @var string
	 */
	private $edit_link;
	/**
	 * @var string
	 */
	private $file;
	/**
	 * @var Media_Item_Size[]
	 */
	private $sizes;
	/**
	 * @var Settings
	 */
	private $plugin_settings;
	/**
	 * @var \WP_Post
	 */
	private $post;
	/**
	 * @var string[]
	 */
	private $animated_mime_types = array( 'image/gif' );
	/**
	 * @var bool
	 */
	private $animated;
	/**
	 * @var bool
	 */
	private $transparent;
	/**
	 * @var int
	 */
	private $ignored;
	/**
	 * @var int
	 */
	private $size_limit;
	/**
	 * @var WP_Error
	 */
	private $errors;
	/**
	 * @var array
	 */
	private $smushable_sizes;
	/**
	 * @var bool
	 */
	private $is_image;
	/**
	 * @var bool
	 */
	private $mime_type_supported;
	/**
	 * @var array[]
	 */
	private $missing_sizes;

	private $reset_properties = array(
		'metadata',
		'file',
		'sizes',
		'post',
		'animated',
		'ignored',
		'errors',
		'smushable_sizes',
		'is_image',
		'mime_type_supported',
		'missing_sizes',
		'backup_sizes',
		'mime_type',
		'attached_file',
		'original_image_path',
		'outdated_meta_values',
	);
	/**
	 * @var Backup_Size[]
	 */
	private $backup_sizes;
	/**
	 * @var string
	 */
	private $mime_type;
	/**
	 * @var string
	 */
	private $attached_file;
	/**
	 * @var false|string
	 */
	private $original_image_path;
	/**
	 * @var Array_Utils
	 */
	private $array_utils;
	private $registered_wp_sizes;
	private $outdated_meta_values = array();
	/**
	 * @var File_System
	 */
	private $fs;

	public function __construct( $id ) {
		$this->id = $id;

		$this->set_settings( Settings::get_instance() );
		$size_limit = WP_Smush::is_pro()
			? WP_SMUSH_PREMIUM_MAX_BYTES
			: WP_SMUSH_MAX_BYTES;
		$this->set_size_limit( $size_limit );
		$this->array_utils = new Array_Utils();
		$this->fs          = new File_System();
	}

	public function size_limit_exceeded() {
		foreach ( $this->get_smushable_sizes() as $size ) {
			if ( $size->exceeds_size_limit() ) {
				return true;
			}
		}

		return false;
	}

	private function get_file_name_exceeding_limit() {
		foreach ( $this->get_smushable_sizes() as $size ) {
			if ( $size->exceeds_size_limit() ) {
				return $size->get_file_name();
			}
		}

		return '';
	}

	public function set_size_limit( $size_limit ) {
		$this->size_limit = $size_limit;
	}

	public function get_size_limit() {
		return $this->size_limit;
	}

	public function get_human_size_limit() {
		return size_format( $this->get_size_limit() );
	}

	public function get_id() {
		return $this->id;
	}

	/**
	 * Checks whether important metadata exists for the media item.
	 *
	 * Missing metadata for a size does not mean there is a problem, for example data
	 * is not generated if the image is too small to generate a 'large' version.
	 * So we don't check metadata for sizes.
	 *
	 * @return bool
	 */
	public function has_wp_metadata() {
		$metadata = $this->get_wp_metadata();

		return is_array( $metadata )
		       && ! empty( $metadata )
		       && ! empty( $metadata['file'] );
	}

	private function get_missing_sizes() {
		if ( is_null( $this->missing_sizes ) ) {
			$this->missing_sizes = wp_get_missing_image_subsizes( $this->get_id() );
		}

		return $this->missing_sizes;
	}

	/**
	 * TODO: maybe add an error for this
	 * @return bool
	 */
	public function has_missing_sizes() {
		return ! empty( $this->get_missing_sizes() );
	}

	public function get_wp_metadata() {
		if ( empty( $this->metadata ) ) {
			$this->metadata = $this->fetch_wp_metadata();
		}

		return $this->metadata;
	}

	private function fetch_wp_metadata() {
		$attachment_metadata = wp_get_attachment_metadata( $this->get_id() );

		return $this->array_utils->ensure_array( $attachment_metadata );
	}

	/**
	 * @return void
	 */
	private function update_wp_metadata() {
		$updated_attachment_meta = $this->make_attachment_meta();
		if ( ! $this->arrays_same( $this->get_wp_metadata(), $updated_attachment_meta ) ) {
			wp_update_attachment_metadata( $this->get_id(), $updated_attachment_meta );
		}
	}

	private function file_name_from_path( $file_path ) {
		return wp_basename( $file_path );
	}

	/**
	 * TODO: use this instead of Helper::get_image_media_link
	 * @return string
	 */
	public function get_edit_link() {
		if ( is_null( $this->edit_link ) ) {
			$this->edit_link = $this->prepare_edit_link();
		}

		return $this->edit_link;
	}

	private function prepare_edit_link() {
		// TODO: copy implementation from Helper::get_image_media_link
		return '';
	}

	public function get_relative_file_dir() {
		$relative_file_dir = dirname( $this->get_relative_file_path() );
		if ( '.' === $relative_file_dir ) {
			return '';
		}
		return untrailingslashit( $relative_file_dir );
	}

	/**
	 * The relative file path e.g. 2023/05/image.png
	 *
	 * @return string
	 */
	public function get_relative_file_path() {
		if ( is_null( $this->file ) ) {
			$this->file = $this->prepare_relative_file_path();
		}

		return $this->file;
	}

	private function prepare_relative_file_path() {
		$file = (string) $this->get_array_value( $this->get_wp_metadata(), 'file' );

		if ( empty( $file ) ) {
			/**
			 * If metadata is missing we still want some of our functions to work, e.g. backup and restore
			 *
			 * Using _wp_attached_file meta because:
			 * 1. get_attached_file returns the full path but the _wp_attached_file meta has the relative path we need
			 * 2. get_attached_file is filtered which can interfere with our code e.g. the S3 module changes attached file, but we don't want that
			 */
			$file = $this->get_post_meta( '_wp_attached_file' );
		}

		return $file;
	}

	private function get_post_meta( $key ) {
		return get_post_meta( $this->get_id(), $key, true );
	}

	public function has_size( $key ) {
		$sizes = $this->get_sizes();

		return ! empty( $sizes[ $key ] );
	}

	public function has_scaled_size() {
		return $this->has_size( self::SIZE_KEY_SCALED );
	}

	public function has_full_size() {
		return $this->has_size( self::SIZE_KEY_FULL );
	}

	/**
	 * @param $key
	 *
	 * @return Media_Item_Size
	 */
	public function get_size( $key ) {
		return $this->get_array_value( $this->get_sizes(), $key );
	}

	public function get_scaled_size() {
		return $this->get_size( self::SIZE_KEY_SCALED );
	}

	public function get_full_size() {
		return $this->get_size( self::SIZE_KEY_FULL );
	}

	public function get_sizes() {
		if ( is_null( $this->sizes ) ) {
			$this->sizes = $this->prepare_sizes();
		}

		return $this->sizes;
	}

	/**
	 * The 'main' size is the size has get_attached_file as the file path
	 *
	 * @return Media_Item_Size
	 */
	public function get_main_size() {
		return $this->get_scaled_or_full_size();
	}

	private function prepare_sizes() {
		$media_item_sizes = array();

		$metadata_sizes = $this->get_wp_metadata_sizes();
		foreach ( $metadata_sizes as $size_key => $metadata_size ) {
			$registered_size = $this->array_utils->ensure_array( $this->get_registered_wp_size( $size_key ) );
			$metadata_size   = $this->array_utils->ensure_array( $metadata_size );
			$size            = $this->initialize_size( $size_key, array_merge( $registered_size, $metadata_size ) );

			if ( $size ) {
				$media_item_sizes[ $size_key ] = $size;
			}
		}

		$scaled_size = $this->prepare_scaled_size();
		if ( $scaled_size ) {
			$media_item_sizes[ self::SIZE_KEY_SCALED ] = $scaled_size;
		}

		$full_size = $this->prepare_full_size();
		if ( $full_size ) {
			$media_item_sizes[ self::SIZE_KEY_FULL ] = $full_size;
		}

		return $media_item_sizes;
	}

	public function prepare_scaled_size() {
		$file = $this->get_attached_file();
		if ( $file && $this->file_path_has_scaled_postfix( $file ) ) {
			$wp_size_metadata = $this->attachment_metadata_as_size_metadata( $file );

			return $this->initialize_size( self::SIZE_KEY_SCALED, $wp_size_metadata );
		}

		return null;
	}

	private function original_image_exists() {
		$original_image = $this->get_original_image_path();
		$main_file      = $this->get_attached_file();

		return $original_image !== $main_file
		       && $this->fs->file_exists( $original_image );
	}

	public function prepare_full_size() {
		$original_image_exists = $this->original_image_exists();

		if ( $original_image_exists ) {
			$original_image_file = $this->get_original_image_path();
			$image_size          = $this->fs->getimagesize( $original_image_file );
			if ( ! $image_size ) {
				return null;
			}

			return $this->initialize_size( self::SIZE_KEY_FULL, array(
				'file'      => $this->file_name_from_path( $original_image_file ),
				'width'     => $image_size[0],
				'height'    => $image_size[1],
				'mime-type' => $this->get_mime_type(),
				'filesize'  => $this->fs->filesize( $original_image_file ),
			) );
		} else {
			$main_file = $this->get_attached_file();
			if ( $this->file_path_has_scaled_postfix( $main_file ) ) {
				// No luck, the main file is the scaled file
				return null;
			}

			$wp_size_metadata = $this->attachment_metadata_as_size_metadata( $main_file );

			return $this->initialize_size( self::SIZE_KEY_FULL, $wp_size_metadata );
		}
	}

	public function has_smushable_sizes() {
		return ! empty( $this->get_smushable_sizes() );
	}

	/**
	 * @return Media_Item_Size[]
	 */
	public function get_smushable_sizes() {
		if ( is_null( $this->smushable_sizes ) ) {
			$this->smushable_sizes = $this->prepare_smushable_sizes();
		}

		return $this->smushable_sizes;
	}

	private function prepare_smushable_sizes() {
		$sizes = array();
		foreach ( $this->get_sizes() as $size_key => $size ) {
			if ( $size->is_smushable() ) {
				$sizes[ $size_key ] = $size;
			}
		}

		return $sizes;
	}

	private function get_array_value( $array, $key ) {
		return $array && isset( $array[ $key ] )
			? $array[ $key ]
			: null;
	}

	/**
	 * @return array|mixed
	 */
	private function get_wp_metadata_sizes() {
		// TODO: media items created before a certain WP version might not have the scaled size so that needs to be normalized for all wp versions
		$metadata = $this->get_wp_metadata();

		return empty( $metadata['sizes'] )
			? array()
			: $metadata['sizes'];
	}

	private function get_wp_metadata_size( $size_key ) {
		$metadata = $this->get_wp_metadata_sizes();

		return empty( $metadata[ $size_key ] )
			? array()
			: $metadata[ $size_key ];
	}

	public function is_skipped() {
		return $this->is_ignored() ||
		       $this->is_animated();
	}

	public function is_mime_type_supported() {
		if ( is_null( $this->mime_type_supported ) ) {
			$this->mime_type_supported = $this->check_is_mime_type_supported();
		}

		return $this->mime_type_supported;
	}

	private function check_is_mime_type_supported() {
		$mime_type = $this->get_mime_type();
		$supported = in_array( $mime_type, $this->get_supported_mime_types(), true );

		return apply_filters( 'wp_smush_resmush_mime_supported', $supported, $mime_type );
	}

	public function is_image() {
		if ( is_null( $this->is_image ) ) {
			$this->is_image = $this->check_is_image();
		}

		return $this->is_image;
	}

	private function check_is_image() {
		return wp_attachment_is_image( $this->get_id() );
	}

	private function is_smushable_filter() {
		return apply_filters( 'wp_smush_is_smushable', true, $this->get_id(), $this->get_supported_mime_types() );
	}

	public function is_ignored() {
		if ( is_null( $this->ignored ) ) {
			$this->ignored = $this->prepare_ignored();
		}

		return $this->ignored;
	}

	private function prepare_ignored() {
		return (boolean) $this->get_post_meta( self::IGNORED_META_KEY );
	}

	public function set_ignored( $ignored ) {
		$this->ignored = $ignored;

		$this->set_outdated( self::IGNORED_META_KEY );
	}

	/**
	 * @return void
	 */
	private function update_ignored_meta() {
		if ( ! $this->is_outdated( self::IGNORED_META_KEY ) ) {
			return;
		}

		if ( $this->is_ignored() ) {
			update_post_meta( $this->get_id(), self::IGNORED_META_KEY, true );
		} else {
			delete_post_meta( $this->get_id(), self::IGNORED_META_KEY );
		}
	}

	private function smush_image_filter() {
		return apply_filters( 'wp_smush_image', true, $this->get_id() );
	}

	/**
	 * Checking if a file is really animated is an expensive operation because we look at file frames, so here we check only a meta value and do the actual checking right before bulk smush.
	 *
	 * @return bool
	 * @see Animated_Status_Controller
	 */
	public function is_animated() {
		if ( ! $this->has_animated_mime_type() ) {
			return false;
		}

		if ( is_null( $this->animated ) ) {
			$this->animated = (bool) $this->get_post_meta( self::ANIMATED_META_KEY );
		}

		return $this->animated;
	}

	/**
	 * @param $animated
	 *
	 * @return bool
	 */
	public function set_animated( $animated ) {
		if ( ! $this->has_animated_mime_type() ) {
			return false;
		}

		$this->animated = (bool) $animated;
		$this->set_outdated( self::ANIMATED_META_KEY );

		return true;
	}

	/**
	 * @return void
	 */
	private function update_animated_meta() {
		if ( $this->is_outdated( self::ANIMATED_META_KEY ) ) {
			update_post_meta( $this->get_id(), self::ANIMATED_META_KEY, $this->is_animated() ? 1 : 0 );
		}
	}

	public function animated_meta_exists() {
		$animated_meta_value = $this->get_post_meta( self::ANIMATED_META_KEY );

		// Post meta default is empty string so a bool means there is a row in the meta table
		return is_numeric( $animated_meta_value );
	}

	/**
	 * Checking if a file is really transparent is an expensive operation because we look at file contents, so here we check only a meta value and do the actual checking elsewhere.
	 */
	public function is_transparent() {
		if ( ! $this->is_png() ) {
			return false;
		}

		if ( is_null( $this->transparent ) ) {
			$this->transparent = (bool) $this->get_post_meta( self::TRANSPARENT_META_KEY );
		}

		return $this->transparent;
	}

	public function set_transparent( $transparent ) {
		if ( ! $this->is_png() ) {
			return false;
		}

		$this->transparent = (bool) $transparent;
		$this->set_outdated( self::TRANSPARENT_META_KEY );

		return true;
	}

	/**
	 * @return void
	 */
	private function update_transparent_meta() {
		if ( ! $this->is_png() ) {
			// Maybe the mime type has changed, and we should delete the transparent meta value added when the mime type was PNG
			if ( $this->transparent_meta_exists() ) {
				delete_post_meta( $this->get_id(), self::TRANSPARENT_META_KEY );
			}
		} else {
			if ( $this->is_outdated( self::TRANSPARENT_META_KEY ) ) {
				// Unlike most other meta values we will not delete the meta because even a false value is useful: it tells us we have checked transparency before.
				update_post_meta( $this->get_id(), self::TRANSPARENT_META_KEY, $this->is_transparent() ? 1 : 0 );
			}
		}
	}

	public function transparent_meta_exists() {
		$transparent_meta_value = $this->get_post_meta( self::TRANSPARENT_META_KEY );

		// Post meta default is empty string so a bool means there is a row in the meta table
		return is_numeric( $transparent_meta_value );
	}

	public function is_valid() {
		return ! empty( $this->get_wp_metadata() );
	}

	/**
	 * @return bool
	 */
	public function has_animated_mime_type() {
		return in_array( $this->get_mime_type(), $this->animated_mime_types, true );
	}

	private function get_missing_file_name() {
		foreach ( $this->get_smushable_sizes() as $size ) {
			if ( ! $size->file_exists() ) {
				return $size->get_file_name();
			}
		}

		return '';
	}

	private function files_exist() {
		foreach ( $this->get_smushable_sizes() as $size ) {
			if ( ! $size->file_exists() ) {
				return false;
			}
		}

		return true;
	}

	public function set_settings( $settings ) {
		$this->plugin_settings = $settings;
	}

	private function get_post() {
		if ( is_null( $this->post ) ) {
			$this->post = get_post( $this->get_id() );
		}

		return $this->post;
	}

	public function get_mime_type() {
		if ( is_null( $this->mime_type ) ) {
			$this->mime_type = $this->fetch_post_mime_type();
		}

		return $this->mime_type;
	}

	private function fetch_post_mime_type() {
		return $this->get_post()->post_mime_type;
	}

	public function set_mime_type( $mime_type ) {
		$this->mime_type = $mime_type;
	}

	/**
	 * @return void
	 */
	private function update_post_mime_type() {
		if ( $this->get_mime_type() !== $this->fetch_post_mime_type() ) {
			wp_update_post(
				array(
					'ID'             => $this->get_id(),
					'post_mime_type' => $this->get_mime_type(),
				)
			);
		}
	}

	public function save() {
		$this->update_ignored_meta();

		if ( $this->is_valid() ) {
			// We don't want to touch the rest of the stuff if the item is not valid.
			// For example if we don't have metadata to begin with then don't try to update it now.

			$this->update_animated_meta();

			$this->update_transparent_meta();

			$this->update_attached_file();

			$this->update_post_mime_type();

			$this->update_wp_metadata();

			$this->update_backup_sizes();
		}

		// Force everything to be reloaded from DB
		$this->reset();
	}

	public function prepare_errors() {
		$errors = new WP_Error();

		if ( ! $this->is_image() ) {
			$errors->add(
				'not_an_image',
				esc_html__( "Attachment is not an image so it can't be smushed.", 'wp-smushit' )
			);
		}

		if ( ! $this->is_mime_type_supported() ) {
			$errors->add(
				'unsupported_mime_type',
				/* translators: %s: Image mime type */
				sprintf( esc_html__( 'The mime type %s is not supported by Smush.', 'wp-smushit' ), $this->get_mime_type() )
			);
		}

		if ( ! $this->has_wp_metadata() ) {
			$errors->add( 'no_file_meta', esc_html__( 'No file data found in image meta', 'wp-smushit' ) );
		}

		if ( ! $this->files_exist() ) {
			$errors->add(
				'file_not_found',
				/* translators: %s: The missing file name */
				sprintf( esc_html__( 'Skipped (%s), File not found.', 'wp-smushit' ), $this->get_missing_file_name() )
			);
		}

		if ( $this->size_limit_exceeded() ) {
			$errors->add(
				'size_limit',
				/* translators: 1: Exceeded size limit file name, 2: Image size limit */
				sprintf( esc_html__( 'Skipped (%1$s), file size limit of %2$s exceeded', 'wp-smushit' ), $this->get_file_name_exceeding_limit(), $this->get_human_size_limit() )
			);
		}

		if ( ! $this->smush_image_filter() || ! $this->is_smushable_filter() ) {
			$errors->add(
				'skipped_filter',
				/* translators: %s: Smush image filter */
				sprintf( esc_html__( 'Skipped with %s filter.', 'wp-smushit' ), ! $this->smush_image_filter() ? 'wp_smush_image' : 'wp_smush_is_smushable' )
			);
		}

		return $errors;
	}

	/**
	 * @return WP_Error
	 */
	public function get_errors() {
		if ( is_null( $this->errors ) ) {
			$this->errors = $this->prepare_errors();
		}

		return $this->errors;
	}

	public function has_errors() {
		return $this->get_errors()->has_errors();
	}

	/**
	 * @param $file
	 *
	 * @return bool
	 */
	private function file_path_has_scaled_postfix( $file ) {
		return false !== strpos( $file, '-scaled.' );
	}

	public function get_dir() {
		$upload_dir = wp_upload_dir();
		$basedir    = untrailingslashit( $upload_dir['basedir'] );
		$file_dir   = $this->get_relative_file_dir();

		return "$basedir/$file_dir/";
	}

	public function get_base_url() {
		$upload_dir     = wp_upload_dir();
		$upload_dir_url = untrailingslashit( $upload_dir['baseurl'] );
		$file_dir       = $this->get_relative_file_dir();

		return "$upload_dir_url/$file_dir/";
	}

	/**
	 * Transform the metadata for a size, so it can be used to initialize a size object
	 * @return array|false
	 */
	private function attachment_metadata_as_size_metadata( $file_path ) {
		$size_metadata = array(
			// Size data is expected to have just the file name instead of path.
			'file'      => $this->file_name_from_path( $file_path ),
			// Size data is expected to have 'mime-type'.
			'mime-type' => $this->get_mime_type(),
		);
		if ( $this->fs->file_exists( $file_path ) ) {
			// Some older WP versions don't have filesize in wp_metadata.
			$size_metadata['filesize'] = $this->fs->filesize( $file_path );
		}
		return array_merge(
			$this->get_wp_metadata(),
			$size_metadata
		);
	}

	private function initialize_size( $key, $metadata ) {
		$size = new Media_Item_Size(
			$key,
			$this->get_id(),
			$this->get_dir(),
			$this->get_base_url(),
			$metadata
		);

		return apply_filters( 'wp_smush_media_item_size', $size, $key, $metadata, $this );
	}

	/**
	 * @return array
	 */
	private function get_registered_wp_sizes() {
		if ( is_null( $this->registered_wp_sizes ) ) {
			$this->registered_wp_sizes = Helper::get_image_sizes();
		}

		return $this->registered_wp_sizes;
	}

	private function get_registered_wp_size( $size_key ) {
		return $this->array_utils->get_array_value( $this->get_registered_wp_sizes(), $size_key );
	}

	public function set_registered_wp_sizes( $registered_wp_sizes ) {
		$this->registered_wp_sizes = $registered_wp_sizes;
	}

	public function reset() {
		foreach ( $this->reset_properties as $property ) {
			$this->$property = null;
		}
	}

	/**
	 * @return false|string
	 */
	private function get_attached_file() {
		if ( is_null( $this->attached_file ) ) {
			$this->attached_file = get_attached_file( $this->get_id() );
		}

		return $this->attached_file;
	}

	/**
	 * @return void
	 */
	private function update_attached_file() {
		$main_size             = $this->get_main_size();
		$updated_attached_file = $main_size->get_file_path();
		if ( $updated_attached_file !== $this->get_attached_file() ) {
			update_attached_file( $this->get_id(), $updated_attached_file );
		}
	}

	private function make_attachment_meta() {
		$sizes = array();
		foreach ( $this->get_sizes() as $size_key => $size ) {
			if ( $size_key === self::SIZE_KEY_FULL || $size_key === self::SIZE_KEY_SCALED ) {
				continue;
			}

			$sizes[ $size_key ] = array(
				'file'      => $size->get_file_name(),
				'width'     => $size->get_width(),
				'height'    => $size->get_height(),
				'mime-type' => $size->get_mime_type(),
				'filesize'  => $size->get_filesize(),
			);
		}

		$short_dir = $this->get_relative_file_dir();
		$main_size = $this->get_main_size();
		$new_meta  = array(
			'file'     => "$short_dir/{$main_size->get_file_name()}",
			'width'    => $main_size->get_width(),
			'height'   => $main_size->get_height(),
			'filesize' => $main_size->get_filesize(),
			'sizes'    => $sizes,
		);
		if ( $this->original_image_exists() && $this->has_full_size() ) {
			// If the original image exists then we must have used it when preparing the full size,
			// use it now to update the original_image value in the meta
			$new_meta['original_image'] = $this->get_full_size()->get_file_name();
		}

		return array_merge( $this->get_wp_metadata(), $new_meta );
	}

	private function arrays_same( $array1, $array2 ) {
		if (
			! is_array( $array1 )
			|| ! is_array( $array2 )
			|| count( $array1 ) !== count( $array2 )
		) {
			return false;
		}

		return $this->array_utils->array_hash( $array1 ) === $this->array_utils->array_hash( $array2 );
	}

	/**
	 * @return false|string
	 */
	public function get_original_image_path() {
		if ( is_null( $this->original_image_path ) ) {
			$this->original_image_path = wp_get_original_image_path( $this->get_id() );
		}

		return $this->original_image_path;
	}

	public function get_backup_sizes() {
		if ( is_null( $this->backup_sizes ) ) {
			$this->backup_sizes = $this->prepare_backup_sizes();
		}

		return $this->backup_sizes;
	}

	/**
	 * @param $backup_sizes Backup_Size[]
	 *
	 * @return void
	 */
	private function set_backup_sizes( $backup_sizes ) {
		$this->backup_sizes = $backup_sizes;

		$this->set_outdated( self::BACKUP_SIZES_META_KEY );
	}

	/**
	 * @return void
	 */
	private function update_backup_sizes() {
		if ( ! $this->is_outdated( self::BACKUP_SIZES_META_KEY ) ) {
			return;
		}

		$updated_backup_sizes_meta = $this->make_backup_sizes_meta();
		if ( ! $this->arrays_same( $this->get_backup_sizes_meta(), $updated_backup_sizes_meta ) ) {
			update_post_meta( $this->get_id(), self::BACKUP_SIZES_META_KEY, $updated_backup_sizes_meta );
		}
	}

	private function prepare_backup_sizes() {
		$backup_sizes      = array();
		$backup_sizes_meta = $this->get_backup_sizes_meta();

		foreach ( $backup_sizes_meta as $backup_size_key => $backup_size_meta ) {
			$backup_size = new Backup_Size( $this->get_dir() );
			$backup_size->from_array( $backup_size_meta );
			$backup_sizes[ $backup_size_key ] = $backup_size;
		}

		return $backup_sizes;
	}

	/**
	 * @return Backup_Size|null
	 */
	public function get_default_backup_size() {
		return $this->get_backup_size( self::DEFAULT_BACKUP_KEY );
	}

	/**
	 * @param $file_name
	 * @param $width
	 * @param $height
	 * @param $key
	 *
	 * @return void
	 */
	public function add_backup_size( $file_name, $width, $height, $key = self::DEFAULT_BACKUP_KEY ) {
		$backup_sizes         = $this->get_backup_sizes();
		$dir                  = $this->get_dir();
		$backup_size          = ( new Backup_Size( $dir ) )->set_file( $file_name )
		                                                   ->set_width( $width )
		                                                   ->set_height( $height );
		$backup_sizes[ $key ] = $backup_size;
		$this->set_backup_sizes( $backup_sizes );
	}

	public function make_backup_sizes_meta() {
		return array_map( function ( $backup_size ) {
			return $backup_size->to_array();
		}, $this->get_backup_sizes() );
	}

	/**
	 * @return array|mixed
	 */
	private function get_backup_sizes_meta() {
		$backup_sizes_meta = $this->get_post_meta( self::BACKUP_SIZES_META_KEY );

		return empty( $backup_sizes_meta ) ? array() : $backup_sizes_meta;
	}

	/**
	 * @param $key
	 *
	 * @return Backup_Size|null
	 */
	public function get_backup_size( $key ) {
		return $this->get_array_value( $this->get_backup_sizes(), $key );
	}

	public function remove_default_backup_size() {
		$this->remove_backup_size( self::DEFAULT_BACKUP_KEY );
	}

	public function remove_backup_size( $key ) {
		$backup_sizes = $this->get_backup_sizes();
		if ( isset( $backup_sizes[ $key ] ) ) {
			unset( $backup_sizes[ $key ] );
		}
		$this->set_backup_sizes( $backup_sizes );

		$this->set_outdated( self::BACKUP_SIZES_META_KEY );
	}

	public function get_scaled_or_full_size() {
		return $this->has_scaled_size()
			? $this->get_scaled_size()
			: $this->get_full_size();
	}

	public function get_full_or_scaled_size() {
		return $this->has_full_size()
			? $this->get_full_size()
			: $this->get_scaled_size();
	}

	public function get_size_urls() {
		return array_map( function ( $size ) {
			return $size->get_file_url();
		}, $this->get_sizes() );
	}

	public function get_size_paths() {
		return array_map( function ( $size ) {
			return $size->get_file_path();
		}, $this->get_sizes() );
	}

	private function is_outdated( $key ) {
		$outdated_values = empty( $this->outdated_meta_values ) ? array() : $this->outdated_meta_values;

		return ! empty( $outdated_values[ $key ] );
	}

	private function set_outdated( $key ) {
		if ( empty( $this->outdated_meta_values ) ) {
			$this->outdated_meta_values = array();
		}

		$this->outdated_meta_values[ $key ] = true;
	}

	public function is_png() {
		$mime = $this->get_mime_type();

		return 'image/png' === $mime || 'image/x-png' === $mime;
	}

	public function can_be_restored() {
		if ( ! $this->plugin_settings->is_backup_active() ) {
			return false;
		}

		// Note that we don't check if file exists because the file might be on a remote server e.g. s3
		return ! empty( $this->get_default_backup_size() );
	}

	public function is_large() {
		$file_size = $this->get_full_or_scaled_size()->get_filesize();
		$cut_off   = $this->plugin_settings->get_large_file_cutoff();

		return $file_size > $cut_off;
	}
}
<?php

namespace Smush\Core\Media;

use Smush\Core\File_System;
use Smush\Core\Settings;
use WP_Smush;

class Media_Item_Size {
	/**
	 * @var string
	 */
	private $key;
	/**
	 * @var string
	 */
	private $file_name;
	/**
	 * @var int
	 */
	private $width;
	/**
	 * @var int
	 */
	private $height;
	/**
	 * @var string
	 */
	private $mime_type;
	/**
	 * @var int
	 */
	private $filesize;
	/**
	 * @var int
	 */
	private $attachment_id;
	/**
	 * @var Settings
	 */
	private $settings;
	/**
	 * @var array
	 */
	private $wp_metadata;
	/**
	 * @var int
	 */
	private $size_limit;
	/**
	 * @var string
	 */
	private $dir;
	/**
	 * @var string
	 */
	private $base_url;
	/**
	 * @var string
	 */
	private $extension;
	/**
	 * @var File_System
	 */
	private $fs;

	/**
	 * @param $key string
	 * @param $attachment_id int
	 * @param $wp_size_metadata array
	 */
	public function __construct( $key, $attachment_id, $dir, $base_url, $wp_size_metadata ) {
		$this->key           = $key;
		$this->attachment_id = $attachment_id;
		$this->dir           = $dir;
		$this->base_url      = $base_url;
		$this->wp_metadata   = $wp_size_metadata;
		$this->fs            = new File_System();

		$this->size_limit = WP_Smush::is_pro()
			? WP_SMUSH_PREMIUM_MAX_BYTES
			: WP_SMUSH_MAX_BYTES;
		$this->settings   = Settings::get_instance();
		$this->from_array( $wp_size_metadata );
	}

	/**
	 * @param $size_data array Typically an item from 'sizes' array returned by wp_get_attachment_metadata
	 *
	 * @return void
	 */
	private function from_array( $size_data ) {
		$this->set_file_name( (string) $this->get_array_value( $size_data, 'file' ) );
		$this->set_width( (int) $this->get_array_value( $size_data, 'width' ) );
		$this->set_height( (int) $this->get_array_value( $size_data, 'height' ) );
		$this->set_mime_type( (string) $this->get_array_value( $size_data, 'mime-type' ) );
		$this->set_filesize( (int) $this->get_array_value( $size_data, 'filesize' ) );
	}

	private function get_array_value( $array, $key ) {
		return isset( $array[ $key ] ) ? $array[ $key ] : null;
	}

	public function get_file_name_without_extension() {
		return mb_substr( $this->get_file_name(), 0, mb_strlen( $this->get_file_name() ) - mb_strlen( '.' . $this->get_extension() ) );
	}

	public function get_file_name() {
		return $this->file_name;
	}

	public function set_file_name( $file_name ) {
		$this->file_name = $file_name;
	}

	/**
	 * @return string
	 */
	public function get_file_path() {
		return path_join( $this->dir, $this->get_file_name() );
	}

	public function get_file_url() {
		$base_url  = $this->base_url;
		$file_name = $this->get_file_name();

		return "$base_url$file_name";
	}

	/**
	 * @return int
	 */
	public function get_width() {
		return $this->width;
	}

	/**
	 * @param int $width
	 */
	public function set_width( $width ) {
		$this->width = $width;
	}

	/**
	 * @return int
	 */
	public function get_height() {
		return $this->height;
	}

	/**
	 * @param int $height
	 */
	public function set_height( $height ) {
		$this->height = $height;
	}

	/**
	 * @return string
	 */
	public function get_mime_type() {
		return $this->mime_type;
	}

	/**
	 * @param string $mime_type
	 */
	public function set_mime_type( $mime_type ) {
		$this->mime_type = $mime_type;
	}

	/**
	 * @return int
	 */
	public function get_filesize() {
		return $this->filesize;
	}

	/**
	 * @param int $filesize
	 */
	public function set_filesize( $filesize ) {
		$this->filesize = $filesize;
	}

	/**
	 * @return string
	 */
	public function get_key() {
		return $this->key;
	}

	public function has_wp_metadata() {
		return ! empty( $this->wp_metadata );
	}

	public function is_smushable() {
		return $this->is_size_selected_in_settings() &&
		       $this->media_image_filter();
	}

	public function exceeds_size_limit() {
		return $this->get_filesize() > $this->size_limit;
	}

	private function media_image_filter() {
		return apply_filters( 'wp_smush_media_image', true, $this->get_key(), $this->get_file_path(), $this->get_attachment_id() );
	}

	public function file_exists() {
		return $this->fs->file_exists( $this->get_file_path() );
	}

	private function is_size_selected_in_settings() {
		if ( $this->get_key() === 'full' ) {
			return $this->settings->get( 'original' );
		}

		$selected = $this->settings->get_setting( 'wp-smush-image_sizes' );
		if ( empty( $selected ) || ! is_array( $selected ) ) {
			return true;
		}

		return in_array( $this->get_key(), $selected );
	}

	/**
	 * @return int
	 */
	public function get_size_limit() {
		return $this->size_limit;
	}

	/**
	 * @param int $size_limit
	 */
	public function set_size_limit( $size_limit ) {
		$this->size_limit = $size_limit;
	}

	public function get_dir() {
		return $this->dir;
	}

	public function get_extension() {
		if ( is_null( $this->extension ) ) {
			$this->extension = $this->prepare_extension();
		}

		return $this->extension;
	}

	public function prepare_extension() {
		return pathinfo( $this->get_file_path(), PATHINFO_EXTENSION );
	}

	/**
	 * @return int
	 */
	public function get_attachment_id() {
		return $this->attachment_id;
	}
}
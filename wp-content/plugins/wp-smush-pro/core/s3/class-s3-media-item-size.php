<?php

namespace Smush\Core\S3;

use Smush\Core\File_System;
use Smush\Core\Media\Media_Item_Size;

class S3_Media_Item_Size extends Media_Item_Size {
	private $file_size;
	/**
	 * @var File_System
	 */
	private $fs;

	public function __construct( $key, $attachment_id, $dir, $base_url, $wp_size_metadata ) {
		parent::__construct( $key, $attachment_id, $dir, $base_url, $wp_size_metadata );

		$this->fs = new File_System();
	}

	/**
	 * The media item always uses local paths, which is fine for most cases because we download the files for our operations.
	 * But to cover any edge cases, this method returns the remote path (filtered by WP offload media). The remote path supports read operations like filesize() and file_exists()
	 */
	public function get_file_path() {
		$local_file_path = parent::get_file_path();
		if ( $this->fs->file_exists( $local_file_path ) ) {
			return $local_file_path;
		}

		$size_file_name     = $this->get_file_name();
		$attached_file      = get_attached_file( $this->get_attachment_id() );
		$attached_file_name = wp_basename( $attached_file );
		$maybe_remote_path  = str_replace( $attached_file_name, $size_file_name, $attached_file );

		return $this->fs->file_exists( $maybe_remote_path )
			? $maybe_remote_path
			: $local_file_path;
	}

	public function get_local_path() {
		return parent::get_file_path();
	}

	/**
	 * Because for some reason WP Offload media removes the size {@see \DeliciousBrains\WP_Offload_Media\Items\Media_Library_Item::update_filesize_after_download_local}
	 *
	 * @return int
	 */
	public function get_filesize() {
		$file_path = $this->get_file_path();
		if ( empty( $this->file_size ) && $this->fs->file_exists( $file_path ) ) {
			$this->file_size = $this->fs->filesize( $file_path );
		}

		return $this->file_size;
	}
}
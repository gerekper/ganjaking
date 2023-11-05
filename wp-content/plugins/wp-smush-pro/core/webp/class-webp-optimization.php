<?php

namespace Smush\Core\Webp;

use Smush\Core\File_System;
use Smush\Core\Media\Media_Item;
use Smush\Core\Media\Media_Item_Optimization;
use Smush\Core\Media\Media_Item_Stats;
use Smush\Core\Settings;

/**
 * TODO: the response from the API has webp: false and mime_content_type of the written file is not webp, investigate
 */
class Webp_Optimization extends Media_Item_Optimization {
	const OPTIMIZATION_KEY = 'webp_optimization';
	private $webp_dir;
	/**
	 * @var Media_Item
	 */
	private $media_item;
	/**
	 * @var Webp_Helper
	 */
	private $webp_helper;
	/**
	 * @var Settings|null
	 */
	private $settings;
	/**
	 * @var Webp_Converter
	 */
	private $converter;
	/**
	 * @var File_System
	 */
	private $fs;

	public function __construct( $media_item ) {
		$this->webp_dir    = new Webp_Dir();
		$this->webp_helper = new Webp_Helper();
		$this->media_item  = $media_item;
		$this->settings    = Settings::get_instance();
		$this->converter   = new Webp_Converter();
		$this->fs          = new File_System();
	}

	public function get_key() {
		return self::OPTIMIZATION_KEY;
	}

	public function is_optimized() {
		$attachment_id = $this->media_item->get_id();
		$webp_flag     = $this->webp_helper->get_webp_flag( $attachment_id );
		if ( empty( $webp_flag ) ) {
			return false;
		}

		$webp_file_path = trailingslashit( $this->webp_dir->get_webp_path() ) . ltrim( $webp_flag, '/' );

		return $this->fs->file_exists( $webp_file_path );
	}

	public function should_optimize() {
		if (
			$this->media_item->is_skipped()
			|| $this->media_item->has_errors()
			|| ! $this->settings->is_webp_module_active()
		) {
			return false;
		}

		return in_array(
			$this->media_item->get_mime_type(),
			$this->webp_helper->supported_mime_types(),
			true
		);
	}

	public function should_reoptimize() {
		if ( ! $this->should_optimize() ) {
			return false;
		}

		$smushable_sizes = $this->media_item->get_smushable_sizes();
		foreach ( $smushable_sizes as $size ) {
			$webp_file_path = $this->webp_helper->get_webp_file_path( $size->get_file_path() );
			if ( ! $this->fs->file_exists( $webp_file_path ) ) {
				return true;
			}
		}

		return false;
	}

	public function save() {
		$webp_file_path = $this->webp_helper->get_webp_file_path( $this->media_item->get_main_size()->get_file_path() );
		if ( $this->fs->file_exists( $webp_file_path ) ) {
			$relative_path = substr( $webp_file_path, strlen( $this->webp_dir->get_webp_path() . '/' ) );
			$this->webp_helper->update_webp_flag( $this->media_item->get_id(), $relative_path );
		}
	}

	public function get_stats() {
		// Empty stats for now since we don't store webp savings
		return new Media_Item_Stats();
	}

	public function get_size_stats( $size_key ) {
		// Empty stats for now since we don't store webp savings
		return new Media_Item_Stats();
	}

	public function should_optimize_size( $size ) {
		if ( ! $this->should_optimize() ) {
			return false;
		}

		return array_key_exists(
			$size->get_key(),
			$this->media_item->get_smushable_sizes()
		);
	}

	public function delete_data() {
		$this->webp_helper->unset_webp_flag( $this->media_item->get_id() );
	}

	public function optimize() {
		$media_item        = $this->media_item;
		$file_paths        = array_map( function ( $size ) {
			return $size->get_file_path();
		}, $media_item->get_smushable_sizes() );
		$responses         = $this->converter->smush( $file_paths );
		$success_responses = array_filter( $responses );
		if ( count( $success_responses ) !== count( $responses ) ) {
			return false;
		}
		$this->save();

		return true;
	}

	public function get_errors() {
		return $this->converter->get_errors();
	}

	public function get_optimized_sizes_count() {
		// We don't keep per-size stats
		return 0;
	}
}
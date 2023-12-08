<?php

namespace Smush\Core\Png2Jpg;

use Smush\Core\Controller;
use Smush\Core\File_System;
use Smush\Core\Helper;
use Smush\Core\Media\Media_Item;
use Smush\Core\Media\Media_Item_Cache;
use Smush\Core\Settings;
use Smush\Core\Stats\Global_Stats;
use Smush\Core\Stats\Media_Item_Optimization_Global_Stats_Persistable;
use Smush\Core\Upload_Dir;
use WDEV_Logger;
use WP;

class Png2Jpg_Controller extends Controller {
	const GLOBAL_STATS_OPTION_ID = 'wp-smush-png2jpg-global-stats';
	const REWRITE_RULES_FLUSHED_OPTION = 'wp-smush-png2jpg-rewrite-rules-flushed';
	const PNG2JPG_OPTIMIZATION_ORDER = 0;
	/**
	 * @var WDEV_Logger
	 */
	private $logger;
	/**
	 * @var Global_Stats
	 */
	private $global_stats;
	/**
	 * @var Media_Item_Cache
	 */
	private $media_item_cache;
	/**
	 * @var Png2Jpg_Helper
	 */
	private $helper;

	/**
	 * Static instance
	 *
	 * @var self
	 */
	private static $instance;
	/**
	 * @var File_System
	 */
	private $fs;

	/**
	 * @var Settings
	 */
	private $settings;

	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->logger           = Helper::logger()->png2jpg();
		$this->global_stats     = Global_Stats::get();
		$this->media_item_cache = Media_Item_Cache::get_instance();
		$this->helper           = new Png2Jpg_Helper();
		$this->fs               = new File_System();
		$this->settings         = Settings::get_instance();

		$this->register_filter( 'wp_smush_optimizations', array(
			$this,
			'add_png2jpg_optimization',
		), self::PNG2JPG_OPTIMIZATION_ORDER, 2 );
		$this->register_filter( 'wp_smush_global_optimization_stats', array( $this, 'add_png2jpg_global_stats' ) );

		$this->register_action( 'wp_smush_settings_updated', array(
			$this,
			'maybe_mark_global_stats_as_outdated',
		), 10, 2 );

		$this->register_filter( 'wp_smush_scan_library_slice_handle_attachment', array(
			$this,
			'maybe_update_transparent_status_during_scan',
		), 10, 2 );
		$this->register_action( 'wp_smush_after_attachment_upload', array(
			$this,
			'maybe_update_transparent_status_on_upload',
		) );
		$this->register_action( 'wp_smush_before_smush_attempt', array(
			$this,
			'maybe_update_transparent_status_before_optimization',
		) );

		if ( ! $this->settings->is_png2jpg_module_active() ) {
			return;
		}
		$this->register_action( 'init', array( $this, 'add_fallback_png_rewrite_rules' ) );
		$this->register_action( 'wp', array( $this, 'serve_fallback_png' ) );
	}

	public function add_fallback_png_rewrite_rules() {
		/**
		 * @var $wp WP
		 */
		global $wp;
		$wp->add_query_var( 'smush_load_fallback_png' );

		$upload_dir      = new Upload_Dir();
		$upload_rel_path = ltrim( $upload_dir->get_upload_rel_path(), '/' );

		$this->logger->info( "Added rewrite rule [$upload_rel_path/(.*\.(?:png))$] so fallback PNGs can be served" );
		add_rewrite_rule( "$upload_rel_path/(.*\.(?:png))$", 'index.php?smush_load_fallback_png=$matches[1]', 'top' );
		$this->maybe_flush_rewrite_rules();
	}

	public function serve_fallback_png() {
		$png_relative_path = get_query_var( 'smush_load_fallback_png' );
		if ( ! $png_relative_path ) {
			return;
		}

		$this->logger->info( 'Attempting to serve a fallback PNG.' );
		if ( headers_sent() ) {
			$this->logger->info( 'Attempted to serve a fallback PNG but headers have already been sent.' );

			return;
		}

		$fallback_jpg_path = $this->get_fallback_jpg_path( $png_relative_path );
		if ( ! $fallback_jpg_path ) {
			$this->logger->info( 'Attempted to serve a fallback PNG but no JPG was found for fallback.' );

			return;
		}

		$extension = pathinfo( $fallback_jpg_path, PATHINFO_EXTENSION );
		$mime_type = $extension === 'png' ? 'image/png' : 'image/jpeg';
		status_header( 200 );
		header( "Content-Type: $mime_type" );
		readfile( $fallback_jpg_path );
		exit;
	}

	public function get_fallback_jpg_path( $png_relative_path ) {
		global $wpdb;
		$wild              = '%';
		$png_relative_path = ltrim( $png_relative_path, '/' );
		$path_like         = $wild . $wpdb->esc_like( $png_relative_path ) . $wild;
		$row               = $wpdb->get_row( $wpdb->prepare( "SELECT post_id, meta_key FROM {$wpdb->postmeta} WHERE meta_value LIKE %s LIMIT 1", $path_like ) );
		if ( empty( $row ) ) {
			return false;
		}

		$media_item = Media_Item_Cache::get_instance()->get( $row->post_id );
		if ( ! $media_item->is_image() ) {
			return false;
		}
		$optimization        = new Png2Jpg_Optimization( $media_item );
		$converted_png_files = $optimization->get_converted_png_files();
		$size_key            = array_search( $png_relative_path, $converted_png_files );
		if ( ! empty( $size_key ) && $media_item->has_size( $size_key ) ) {
			$file_path = $media_item->get_size( $size_key )->get_file_path();
		} else {
			$file_path = $media_item->get_main_size()->get_file_path();
		}

		if ( ! $this->fs->file_exists( $file_path ) ) {
			return false;
		}

		return $file_path;
	}

	/**
	 * @param $optimizations array
	 * @param $media_item Media_Item
	 *
	 * @return array
	 */
	public function add_png2jpg_optimization( $optimizations, $media_item ) {
		$optimization                              = new Png2Jpg_Optimization( $media_item );
		$optimizations[ $optimization->get_key() ] = $optimization;

		return $optimizations;
	}

	private function maybe_flush_rewrite_rules() {
		$flushed = get_option( self::REWRITE_RULES_FLUSHED_OPTION, false );
		if ( WP_SMUSH_VERSION !== $flushed ) {
			$this->logger->info( "Flushing rewrite rules so fallback PNGs can be served" );
			flush_rewrite_rules();
			update_option( self::REWRITE_RULES_FLUSHED_OPTION, WP_SMUSH_VERSION );
		}
	}

	public function add_png2jpg_global_stats( $stats ) {
		$stats[ Png2Jpg_Optimization::KEY ] = new Media_Item_Optimization_Global_Stats_Persistable( self::GLOBAL_STATS_OPTION_ID );

		return $stats;
	}

	public function maybe_mark_global_stats_as_outdated( $old_settings, $settings ) {
		$png_to_jpg_old = ! empty( $old_settings['png_to_jpg'] );
		$png_to_jpg_new = ! empty( $settings['png_to_jpg'] );
		if ( $png_to_jpg_old !== $png_to_jpg_new ) {
			$this->global_stats->mark_as_outdated();
		}
	}

	public function maybe_update_transparent_status_during_scan( $slice_data, $attachment_id ) {
		$this->maybe_update_transparent_status( $attachment_id );

		return $slice_data;
	}

	public function maybe_update_transparent_status_on_upload( $attachment_id ) {
		$this->maybe_update_transparent_status( $attachment_id );
	}

	/**
	 * TODO: add test
	 *
	 * @param $attachment_id
	 *
	 * @return void
	 */
	public function maybe_update_transparent_status_before_optimization( $attachment_id ) {
		$this->maybe_update_transparent_status( $attachment_id );
	}

	private function maybe_update_transparent_status( $attachment_id ) {
		// We are checking the status of the resize module here because the resize module destroys transparency information.
		$is_resize_module_active  = $this->settings->is_resize_module_active();
		$is_png2jpg_module_active = $this->settings->is_png2jpg_module_active();

		$transparency_check_required = $is_png2jpg_module_active || $is_resize_module_active;
		if ( ! $transparency_check_required ) {
			return;
		}

		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( ! $media_item->is_valid() ) {
			$this->logger->error( 'Tried to check transparent value but encountered a problem with the media item' );

			return;
		}

		if ( apply_filters( 'wp_smush_skip_image_transparency_check', false, $attachment_id ) ) {
			// The image is explicitly excluded from the transparency check
			return;
		}

		if ( ! $media_item->is_png() ) {
			// The media item is not even a png so no need to check.
			return;
		}

		if ( $media_item->transparent_meta_exists() ) {
			// Already checked, no need to check again.
			return;
		}

		$this->logger->log( 'Setting transparent meta value' );

		$full_size       = $media_item->get_full_or_scaled_size();
		$is_transparent  = $this->helper->is_transparent( $full_size->get_file_path(), $full_size->get_width(), $full_size->get_height() );
		$set_transparent = $media_item->set_transparent( $is_transparent );
		if ( $set_transparent ) {
			$media_item->save();
		}
	}
}
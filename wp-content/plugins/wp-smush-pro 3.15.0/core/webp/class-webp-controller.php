<?php

namespace Smush\Core\Webp;

use Smush\Core\Controller;
use Smush\Core\File_System;
use Smush\Core\Helper;
use Smush\Core\Media\Media_Item_Cache;
use Smush\Core\Stats\Global_Stats;

class Webp_Controller extends Controller {
	const WEBP_OPTIMIZATION_ORDER = 20;
	/**
	 * @var Webp_Helper
	 */
	private $helper;
	/**
	 * @var Global_Stats
	 */
	private $global_stats;
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
		$this->helper           = new Webp_Helper();
		$this->global_stats     = Global_Stats::get();
		$this->media_item_cache = Media_Item_Cache::get_instance();
		$this->logger           = Helper::logger();
		$this->fs               = new File_System();

		$this->register_action( 'wp_smush_png_jpg_converted', array( $this, 'delete_webp_versions_of_pngs' ), 10, 4 );
		$this->register_action( 'delete_attachment', array( $this, 'delete_webp_versions_before_delete' ) );
		$this->register_filter( 'wp_smush_optimizations', array(
			$this,
			'add_webp_optimization',
		), self::WEBP_OPTIMIZATION_ORDER, 2 );
		$this->register_filter( 'wp_smush_global_optimization_stats', array( $this, 'add_webp_global_stats' ) );
		$this->register_action( 'wp_smush_before_restore_backup', array(
			$this,
			'delete_webp_versions_on_restore',
		), 10, 2 );
		$this->register_action( 'wp_smush_settings_updated', array(
			$this,
			'maybe_mark_global_stats_as_outdated',
		), 10, 2 );
	}

	/**
	 * @param $backup_full_path
	 * @param $attachment_id
	 *
	 * @return bool
	 */
	public function delete_webp_versions_on_restore( $backup_full_path, $attachment_id ) {
		$media_item = Media_Item_Cache::get_instance()->get( $attachment_id );
		if ( ! $media_item->is_valid() ) {
			return false;
		}

		$this->helper->delete_media_item_webp_versions( $media_item );

		return true;
	}

	public function delete_webp_versions_before_delete( $attachment_id ) {
		$media_item = $this->media_item_cache->get( $attachment_id );
		if ( $media_item->is_valid() ) {
			foreach ( $media_item->get_size_paths() as $size_path ) {
				$this->delete_webp_version( $size_path );
			}
		} else {
			$this->logger->error( sprintf( 'Count not delete webp versions of the media item [%d]', $attachment_id ) );
		}
	}

	public function delete_webp_versions_of_pngs( $attachment_id, $meta, $stats, $png_paths ) {
		foreach ( $png_paths as $png_path ) {
			$this->delete_webp_version( $png_path );
		}

		$this->helper->unset_webp_flag( $attachment_id );
	}

	public function delete_webp_version( $original_path ) {
		$webp_file_path = $this->helper->get_webp_file_path( $original_path );
		if ( $this->fs->file_exists( $webp_file_path ) ) {
			$this->fs->unlink( $webp_file_path );
		}
	}

	public function add_webp_optimization( $optimizations, $media_item ) {
		$optimization                              = new Webp_Optimization( $media_item );
		$optimizations[ $optimization->get_key() ] = $optimization;

		return $optimizations;
	}

	public function add_webp_global_stats( $stats ) {
		$stats[ Webp_Optimization::OPTIMIZATION_KEY ] = new Webp_Optimization_Global_Stats_Persistable();

		return $stats;
	}

	public function maybe_mark_global_stats_as_outdated( $old_settings, $settings ) {
		$old_webp_status = ! empty( $old_settings['webp_mod'] );
		$new_webp_status = ! empty( $settings['webp_mod'] );
		if ( $old_webp_status !== $new_webp_status ) {
			$this->global_stats->mark_as_outdated();
		}
	}
}
<?php
/**
 * Handles all the stats related functions
 *
 * @package Smush\Core\Integrations\NextGen
 * @version 1.0
 *
 * @author Umesh Kumar <umesh@incsub.com>
 *
 * @copyright (c) 2016, Incsub (http://incsub.com)
 */

namespace Smush\Core\Integrations\NextGen;

use C_Component_Registry;
use C_Gallery_Storage;
use C_NextGen_Serializable;
use Exception;
use Ngg_Serializable;
use Smush\Core\Attachment_Id_List;
use Smush\Core\Integrations\NextGen;
use WP_Smush;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class
 *
 * TODO refactor stats by using the new core stats to clean the code.
 */
class Stats extends NextGen {

	const REOPTIMIZE_LIST_OPTION_ID = 'wp-smush-nextgen-reoptimize-list';

	const SUPPER_SMUSHED_LIST_OPTION_ID = 'wp-smush-nextgen-super-smushed-list';

	const SMUSH_STATS_OPTION_ID = 'wp_smush_stats_nextgen';

	/**
	 * Contains the total Stats, for displaying it on bulk page
	 *
	 * @var array
	 */
	public $stats = array();

	/**
	 * PRO user status.
	 *
	 * @var bool
	 */
	private $is_pro_user;

	/**
	 * @var Attachment_Id_List
	 */
	private $reoptimize_list;

	/**
	 * @var Attachment_Id_List
	 */
	private $supper_smushed_list;

	/**
	 * @var null|array
	 */
	private $global_stats;

	/**
	 * @var null|array
	 */
	private $unsmushed_images;

	/**
	 * @var null|int.
	 */
	private $remaining_count;

	/**
	 * @var int
	 */
	private $percent_optimized = 0;

	/**
	 * Stats constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->is_pro_user         = WP_Smush::is_pro();
		$this->reoptimize_list     = new Attachment_Id_List( self::REOPTIMIZE_LIST_OPTION_ID );
		$this->supper_smushed_list = new Attachment_Id_List( self::SUPPER_SMUSHED_LIST_OPTION_ID );


		// Clear stats cache when an image is restored.
		add_action( 'wp_smush_image_nextgen_restored', array( $this, 'clear_cache' ) );

		// Add the resizing stats to Global stats.
		add_action( 'wp_smush_image_nextgen_resized', array( $this, 'update_stats' ), '', 2 );

		// Get the stats for single image, update the global stats.
		add_action( 'wp_smush_nextgen_image_stats', array( $this, 'update_stats' ), '', 2 );
	}

	/**
	 * Get the images id for nextgen gallery
	 *
	 * @param bool $force_refresh Optional. Whether to force the cache to be refreshed.
	 * Default false.
	 *
	 * @param bool $return_ids Whether to return the ids array, set to false by default.
	 *
	 * @return int|mixed Returns the images ids or the count
	 */
	public static function total_count( $force_refresh = false, $return_ids = false ) {
		// Check for the  wp_smush_images in the 'nextgen' group.
		$attachment_ids = wp_cache_get( 'wp_smush_images', 'nextgen' );

		// If nothing is found, build the object.
		if ( true === $force_refresh || false === $attachment_ids ) {
			// Get the nextgen image IDs.
			$attachment_ids = self::get_nextgen_attachments();

			if ( ! is_wp_error( $attachment_ids ) ) {
				// In this case we don't need a timed cache expiration.
				wp_cache_set( 'wp_smush_images', $attachment_ids, 'nextgen' );
			}
		}

		return $return_ids ? $attachment_ids : count( $attachment_ids );
	}

	/**
	 * Returns the ngg images list(id and meta ) or count
	 *
	 * @param string     $type          Whether to return smushed images or unsmushed images.
	 * @param bool|false $count         Return count only.
	 * @param bool|false $force_update  True/false to update the cache or not.
	 *
	 * @return bool|mixed Returns assoc array of image ids and meta or Image count
	 *
	 * @throws Exception  Exception.
	 */
	public function get_ngg_images( $type = 'smushed', $count = false, $force_update = false ) {
		global $wpdb;

		$limit  = apply_filters( 'wp_smush_nextgen_query_limit', 1000 );
		$offset = 0;

		// Check type of images being queried.
		if ( ! in_array( $type, array( 'smushed', 'unsmushed' ), true ) ) {
			return false;
		}

		// Check for the  wp_smush_images_smushed in the 'nextgen' group.
		$images = wp_cache_get( 'wp_smush_images_' . $type, 'nextgen' );

		// If nothing is found, build the object.
		if ( ! $images || $force_update ) {
			// Query Attachments for meta key.
			$attachments = $wpdb->get_results( $wpdb->prepare( "SELECT pid, meta_data FROM {$wpdb->nggpictures} LIMIT %d, %d", $offset, $limit ) ); // Db call ok.
			while ( ! empty( $attachments ) ) {
				foreach ( $attachments as $attachment ) {
					// Check if it has `wp_smush` key.
					if ( class_exists( 'Ngg_Serializable' ) ) {
						$meta = ( new Ngg_Serializable() )->unserialize( $attachment->meta_data );
					} elseif ( class_exists( 'C_NextGen_Serializable' ) && method_exists( 'C_NextGen_Serializable', 'unserialize' ) ) {
						$meta = C_NextGen_Serializable::unserialize( $attachment->meta_data );
					} else {
						// If you can't parse it without NextGen - don't parse at all.
						continue;
					}

					// Store pid in image meta.
					if ( is_array( $meta ) && empty( $meta['pid'] ) ) {
						$meta['pid'] = $attachment->pid;
					} elseif ( is_object( $meta ) && empty( $meta->pid ) ) {
						$meta->pid = $attachment->pid;
					}

					// Check meta for wp_smush.
					if ( ! is_array( $meta ) || empty( $meta['wp_smush'] ) ) {
						$unsmushed_images[ $attachment->pid ] = $meta;
						continue;
					}
					$smushed_images[ $attachment->pid ] = $meta;
				}
				// Set the offset.
				$offset += $limit;

				$attachments = $wpdb->get_results( $wpdb->prepare( "SELECT pid, meta_data FROM {$wpdb->nggpictures} LIMIT %d, %d", $offset, $limit ) ); // Db call ok.
			}
			if ( ! empty( $smushed_images ) ) {
				wp_cache_set( 'wp_smush_images_smushed', $smushed_images, 'nextgen', 300 );
			}
			if ( ! empty( $unsmushed_images ) ) {
				wp_cache_set( 'wp_smush_images_unsmushed', $unsmushed_images, 'nextgen', 300 );
			}
		}

		if ( 'smushed' === $type ) {
			$smushed_images = ! empty( $smushed_images ) ? $smushed_images : $images;
			if ( ! $smushed_images ) {
				return 0;
			}
			return $count ? count( $smushed_images ) : $smushed_images;
		}

		$unsmushed_images = ! empty( $unsmushed_images ) ? $unsmushed_images : $images;
		if ( ! $unsmushed_images ) {
			return 0;
		}
		return $count ? count( $unsmushed_images ) : $unsmushed_images;
	}

	/**
	 * Updated the global smush stats for NextGen gallery
	 *
	 * @param int   $image_id  Image ID.
	 * @param array $stats     Compression stats fo respective image.
	 */
	public function update_stats( $image_id, $stats ) {
		$stats = ! empty( $stats['stats'] ) ? $stats['stats'] : '';

		$smush_stats = $this->get_cache_smush_stats();

		if ( ! empty( $stats ) ) {
			// Human Readable.
			$smush_stats['human'] = ! empty( $smush_stats['bytes'] ) ? size_format( $smush_stats['bytes'], 1 ) : '';

			// Size of images before the compression.
			$smush_stats['size_before'] = ! empty( $smush_stats['size_before'] ) ? ( $smush_stats['size_before'] + $stats['size_before'] ) : $stats['size_before'];

			// Size of image after compression.
			$smush_stats['size_after'] = ! empty( $smush_stats['size_after'] ) ? ( $smush_stats['size_after'] + $stats['size_after'] ) : $stats['size_after'];

			$smush_stats['bytes'] = ! empty( $smush_stats['size_before'] ) && ! empty( $smush_stats['size_after'] ) ? ( $smush_stats['size_before'] - $smush_stats['size_after'] ) : 0;

			// Compression Percentage.
			$smush_stats['percent'] = ! empty( $smush_stats['size_before'] ) && ! empty( $smush_stats['size_after'] ) && $smush_stats['size_before'] > 0 ? ( $smush_stats['bytes'] / $smush_stats['size_before'] ) * 100 : $stats['percent'];
		}

		update_option( self::SMUSH_STATS_OPTION_ID, $smush_stats, false );
		$this->clear_cache();
	}

	/**
	 * Clears the object cache for NextGen stats.
	 *
	 * @since 3.7.0
	 */
	public function clear_cache() {
		wp_cache_delete( 'wp_smush_images_smushed', 'nextgen' );
		wp_cache_delete( 'wp_smush_images_unsmushed', 'nextgen' );
		wp_cache_delete( 'wp_smush_images', 'nextgen' );
	}

	/**
	 * Get the attachment stats for a image
	 *
	 * @param object|array|int $id  Attachment ID.
	 *
	 * @return array
	 */
	private function get_attachment_stats( $image ) {
		// We'll get the image object in $image itself, else fetch it using Gallery Storage.
		if ( is_numeric( $image ) ) {
			// Registry Object for NextGen Gallery.
			$registry = C_Component_Registry::get_instance();

			// Gallery Storage Object.
			$storage = $registry->get_utility( 'I_Gallery_Storage' );

			// get an image object.
			$image = $storage->object->_image_mapper->find( $image );
		}

		$smush_savings  = $this->get_image_smush_savings( $image );
		$resize_savings = $this->get_image_resize_savings( $image );

		return $this->recalculate_stats( 'add', $smush_savings, $resize_savings );
	}

	/**
	 * Get the Nextgen Smush stats
	 *
	 * @return bool|mixed|void
	 */
	public function get_smush_stats() {
		$smushed_stats = array(
			'bytes'       => 0,
			'size_before' => 0,
			'size_after'  => 0,
			'percent'     => 0,
		);

		// Clear up the stats.
		if ( 0 == $this->total_count() || $this->get_smushed_count() < 1 ) {
			delete_option( self::SMUSH_STATS_OPTION_ID );
		}

		// Check for the  wp_smush_images in the 'nextgen' group.
		$stats = $this->get_cache_smush_stats();

		$size_before = (int) $this->get_array_value( $stats, 'size_before' );
		if ( empty( $size_before ) ) {
			return $smushed_stats;
		}
		$size_after       = (int) $this->get_array_value( $stats, 'size_after' );
		$stats['bytes']   = $size_before - $size_after;
		$stats['bytes']   = $stats['bytes'] > 0 ? $stats['bytes'] : 0;
		$stats['percent'] = ( $stats['bytes'] / $stats['size_before'] ) * 100;
		// Round off precentage.
		$stats['percent'] = ! empty( $stats['percent'] ) ? round( $stats['percent'], 1 ) : 0;
		$stats['human']   = size_format( $stats['bytes'], $stats['bytes'] >= 1024 ? 1 : 0 );

		$smushed_stats = array_merge( $smushed_stats, $stats );

		// Gotta remove the stats for re-smush ids.
		if ( $this->get_reoptimize_list()->get_count() ) {
			$resmush_stats = $this->get_stats_for_ids( $this->get_reoptimize_list()->get_ids() );
			// Recalculate stats, Remove stats for resmush ids.
			$smushed_stats = $this->recalculate_stats( 'sub', $smushed_stats, $resmush_stats );
		}

		return $smushed_stats;
	}

	/**
	 * Get the combined stats for given Ids
	 *
	 * @param array $ids  Image IDs.
	 *
	 * @return array|bool Array of Stats for the given ids
	 */
	public function get_stats_for_ids( $ids = array() ) {
		// Return if we don't have an array or no ids.
		if ( ! is_array( $ids ) || empty( $ids ) ) {
			return false;
		}

		// Initialize the Stats array.
		$stats = array(
			'size_before' => 0,
			'size_after'  => 0,
		);
		// Calculate the stats, Expensive Operation.
		foreach ( $ids as $id ) {
			$image_stats = $this->get_attachment_stats( $id );
			$stats       = $this->recalculate_stats( 'add', $stats, $image_stats );
		}

		return $stats;
	}

	/**
	 * Add/Subtract the values from 2nd array to First array
	 * This function is very specific to current requirement of stats re-calculation
	 *
	 * @param string $op 'add', 'sub' Add or Subtract the values.
	 * @param array  $a1  First array.
	 * @param array  $a2  Second array.
	 *
	 * @return array Return $a1
	 */
	private function recalculate_stats( $op = 'add', $a1 = array(), $a2 = array() ) {
		// If the first array itself is not set, return.
		if ( empty( $a1 ) ) {
			return $a1;
		}

		// Iterate over keys in first array, and add/subtract the values.
		foreach ( $a1 as $k => $v ) {
			// If the key is not set in 2nd array, skip.
			if ( empty( $a2[ $k ] ) || ! in_array( $k, array( 'size_before', 'size_after' ) ) ) {
				continue;
			}
			// Else perform the operation, Considers the value to be integer, doesn't performs a check.
			if ( 'sub' === $op ) {
				// Subtract the value.
				$a1[ $k ] -= $a2[ $k ];
			} elseif ( 'add' === $op ) {
				// Add the value.
				$a1[ $k ] += $a2[ $k ];
			}
		}

		// Recalculate percentage and human savings.
		$a1['bytes']   = $a1['size_before'] - $a1['size_after'];
		$a1['percent'] = $a1['bytes'] > 0 ? round( ( $a1['bytes'] / $a1['size_before'] ) * 100, 1 ) : 0;
		$a1['human']   = $a1['bytes'] > 0 ? size_format( $a1['bytes'], 1 ) : 0;

		return $a1;
	}

	/**
	 * Get Super smushed images from the given images array
	 *
	 * @param array $images Array of images containing metadata.
	 *
	 * @return array Array containing ids of supersmushed images
	 */
	private function get_super_smushed_images( $images = array() ) {
		if ( empty( $images ) ) {
			return array();
		}
		$super_smushed = array();
		// Iterate Over all the images.
		foreach ( $images as $image_k => $image ) {
			if ( empty( $image ) || ! is_array( $image ) || ! isset( $image['wp_smush'] ) ) {
				continue;
			}
			// Check for lossy compression.
			if ( ! empty( $image['wp_smush']['stats'] ) && ! empty( $image['wp_smush']['stats']['lossy'] ) ) {
				$super_smushed[] = $image_k;
			}
		}
		return $super_smushed;
	}

	/**
	 * Recalculate stats for the given smushed ids and update the cache
	 * Update Super smushed image ids
	 *
	 * @throws Exception  Exception.
	 */
	public function update_stats_cache() {
		// Get the Image ids.
		$smushed_images = $this->get_ngg_images( 'smushed' );
		$super_smushed  = array(
			'ids'       => array(),
			'timestamp' => '',
		);

		$stats = $this->get_stats_for_ids( $smushed_images );
		$lossy = $this->get_super_smushed_images( $smushed_images );

		if ( empty( $stats['bytes'] ) && ! empty( $stats['size_before'] ) ) {
			$stats['bytes'] = $stats['size_before'] - $stats['size_after'];
		}
		$stats['human'] = size_format( ! empty( $stats['bytes'] ) ? $stats['bytes'] : 0 );
		if ( ! empty( $stats['size_before'] ) ) {
			$stats['percent'] = ( $stats['bytes'] / $stats['size_before'] ) * 100;
			$stats['percent'] = round( $stats['percent'], 2 );
		}

		// Update Re-smush list.
		if ( is_array( WP_Smush::get_instance()->core()->nextgen->ng_admin->resmush_ids ) && is_array( $smushed_images ) ) {
			$resmush_ids = array_intersect( WP_Smush::get_instance()->core()->nextgen->ng_admin->resmush_ids, array_keys( $smushed_images ) );
		}

		// If we have resmush ids, add it to db.
		if ( ! empty( $resmush_ids ) ) {
			// Update re-smush images to db.
			$this->get_reoptimize_list()->update_ids( $resmush_ids );
		}

		// Update Super smushed images in db.
		$this->get_supper_smushed_list()->update_ids( $lossy );

		// Update Stats Cache.
		update_option( self::SMUSH_STATS_OPTION_ID, $stats, false );

	}

	public function get_reoptimize_list() {
		return $this->reoptimize_list;
	}

	public function get_supper_smushed_list() {
		return $this->supper_smushed_list;
	}

	public function get_supper_smushed_count() {
		return count( $this->get_supper_smushed() );
	}

	private function get_supper_smushed() {
		$super_smushed = $this->get_supper_smushed_list()->get_ids();

		// If we have images to be resmushed, Update supersmush list.
		$resmush_ids = $this->get_reoptimize_list()->get_ids();
		if ( ! empty( $resmush_ids ) && ! empty( $super_smushed ) ) {
			$super_smushed = array_diff( $super_smushed, $resmush_ids );
		}

		// If supersmushed images are more than total, clean it up.
		if ( count( $super_smushed ) > self::total_count() ) {
			$super_smushed = $this->cleanup_super_smush_data();
		}

		return (array) $super_smushed;
	}

	/**
	 * Cleanup Super-smush images array against the all ids in gallery
	 *
	 * @return array|mixed|void
	 */
	private function cleanup_super_smush_data() {
		$supper_smushed_list = $this->get_supper_smushed_list();
		$super_smushed       = $supper_smushed_list->get_ids();
		$ids                 = self::total_count( false, true );

		if ( ! empty( $super_smushed ) && is_array( $ids ) ) {
			$super_smushed = array_intersect( $super_smushed, $ids );
		}

		$supper_smushed_list->update_ids( $super_smushed );
	}

	public function get_global_stats() {
		if ( $this->global_stats ) {
			return $this->global_stats;
		}

		$stats = $this->get_smush_stats();
		$human_bytes = $this->get_array_value( $stats, 'human' );
		if ( empty( $human_bytes ) ) {
			$human_bytes = '0 B';
		}

		$this->global_stats = array(
			'count_supersmushed'   => $this->get_supper_smushed_count(),
			'count_smushed'        => $this->get_smushed_count(),
			'count_total'          => $this->total_count(),
			'count_images'         => $this->get_smushed_image_count(),
			'count_resize'         => 0,
			'count_skipped'        => 0,
			'unsmushed'            => $this->get_unsmushed_images(),
			'count_unsmushed'      => count( $this->get_unsmushed_images() ),
			'resmush'              => $this->get_reoptimize_list()->get_ids(),
			'count_resmush'        => $this->get_reoptimize_list()->get_count(),
			'size_before'          => $this->get_array_value( $stats, 'size_before' ),
			'size_after'           => $this->get_array_value( $stats, 'size_after' ),
			'savings_bytes'        => $this->get_array_value( $stats, 'bytes' ),
			'human_bytes'          => $human_bytes,
			'savings_resize'       => 0,
			'savings_resize_human' => 0,
			'savings_conversion'   => 0,
			'savings_dir_smush'    => 0,
			'savings_percent'      => $this->get_array_value( $stats, 'percent' ),
			'percent_grade'        => $this->get_grade_class(),
			'percent_metric'       => $this->get_percent_metric(),
			'percent_optimized'    => $this->get_percent_optimized(),
			'remaining_count'      => $this->get_remaining_count(),
		);

		return $this->global_stats;
	}

	public function get_smushed_image_count() {
		$ng_smushed_images = $this->get_ngg_images( 'smushed' );
		if ( empty( $ng_smushed_images ) ) {
			return 0;
		}

		$image_count = 0;
		// $image in here is expected to be metadata array
		foreach ( $ng_smushed_images as $pid => $image ) {
			// If there are no smush stats, skip.
			if ( empty( $image['wp_smush'] ) || $this->get_reoptimize_list()->has_id( $pid ) ) {
				continue;
			}

			// Get the image count.
			if ( ! empty( $image['wp_smush']['sizes'] ) ) {
				$image_count += count( $image['wp_smush']['sizes'] );
			}
		}

		return $image_count;
	}

	public function get_smushed_count() {
		return $this->total_count() - $this->get_remaining_count();
	}

	public function get_unsmushed_images() {
		if ( null !== $this->unsmushed_images ) {
			return $this->unsmushed_images;
		}
		$ng_unsmushed_images = $this->get_ngg_images( 'unsmushed' );
		if ( ! $ng_unsmushed_images ) {
			return array();
		}
		$this->unsmushed_images = array_keys( $ng_unsmushed_images );

		return $this->unsmushed_images;
	}

	public function get_remaining_count() {
		if ( null === $this->remaining_count ) {
			$unsmushed_images      = $this->get_unsmushed_images();
			$resmush_ids           = $this->get_reoptimize_list()->get_ids();
			$remaining_images      = array_unique( array_merge( $resmush_ids, $unsmushed_images ) );
			$this->remaining_count = count( $remaining_images );
		}

		return $this->remaining_count;
	}

	private function get_percent_optimized() {
		$smushed_count = $this->get_smushed_count();
		if ( $smushed_count < 1 ) {
			return $this->percent_optimized;
		}

		$total_optimizable_count = $this->total_count();
		$remaining_count         = $this->get_remaining_count();
		$this->percent_optimized = floor( ( $total_optimizable_count - $remaining_count ) * 100 / $total_optimizable_count );
		if ( $this->percent_optimized > 100 ) {
			$this->percent_optimized = 100;
		} elseif ( $this->percent_optimized < 0 ) {
			$this->percent_optimized = 0;
		}

		return $this->percent_optimized;
	}

	private function get_percent_metric() {
		$percent_optimized = $this->get_percent_optimized();
		return 0.0 === (float) $percent_optimized ? 100 : $percent_optimized;
	}

	private function get_grade_class() {
		$percent_optimized = $this->get_percent_optimized();
		if ( 0 === $percent_optimized ) {
			return 'sui-grade-dismissed';
		}

		$grade = 'sui-grade-f';
		if ( $percent_optimized >= 60 && $percent_optimized < 90 ) {
			$grade = 'sui-grade-c';
		} elseif ( $percent_optimized >= 90 ) {
			$grade = 'sui-grade-a';
		}

		return $grade;
	}

	public function get_array_value( $array, $key ) {
		return isset( $array[ $key ] ) ? $array[ $key ] : null;
	}

	public function subtract_image_stats( $image ) {
		$stats = $this->get_cache_smush_stats();
		$stats = $this->recalculate_stats( 'sub', $stats, $this->get_attachment_stats( $image ) );
		$this->update_smush_stats( $stats );
	}

	private function get_image_smush_savings( $image ) {
		$image = (array) $image;
		if ( ! empty( $image['meta_data']['wp_smush']['stats'] ) ) {
			return $image['meta_data']['wp_smush']['stats'];
		}

		if ( ! empty( $image['wp_smush']['stats'] ) ) {
			return $image['wp_smush']['stats'];
		}

		return array();
	}

	private function get_image_resize_savings( $image ) {
		$image = (array) $image;

		if ( ! empty( $image['meta_data']['wp_smush_resize_savings'] ) ) {
			return $image['meta_data']['wp_smush_resize_savings'];
		}

		if ( ! empty( $image['wp_smush_resize_savings'] ) ) {
			return $image['wp_smush_resize_savings'];
		}

		return array();
	}

	private function update_smush_stats( $stats ) {
		return update_option( self::SMUSH_STATS_OPTION_ID, $stats );
	}

	private function get_cache_smush_stats() {
		return get_option( self::SMUSH_STATS_OPTION_ID, array() );
	}
}
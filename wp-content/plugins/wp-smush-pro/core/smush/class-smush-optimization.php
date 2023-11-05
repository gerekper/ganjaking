<?php

namespace Smush\Core\Smush;

use Smush\Core\Media\Media_Item;
use Smush\Core\Media\Media_Item_Optimization;
use Smush\Core\Media\Media_Item_Size;
use Smush\Core\Media\Media_Item_Stats;
use Smush\Core\Settings;
use WP_Error;

/**
 * Smushes a media item and updates the stats.
 */
class Smush_Optimization extends Media_Item_Optimization {
	const KEY = 'smush_optimization';
	const SMUSH_META_KEY = 'wp-smpro-smush-data';
	const LOSSY_META_KEY = 'wp-smush-lossy';

	/**
	 * @var Media_Item_Stats
	 */
	private $stats;
	/**
	 * @var Media_Item_Stats[]
	 */
	private $size_stats = array();
	/**
	 * @var Media_Item
	 */
	private $media_item;
	/**
	 * @var array
	 */
	private $smush_meta;
	/**
	 * @var int
	 */
	private $keep_exif;
	/**
	 * @var bool
	 */
	private $lossy_level;
	/**
	 * @var string
	 */
	private $api_version;
	/**
	 * @var Settings
	 */
	private $settings;

	private $reset_properties = array(
		'stats',
		'size_stats',
		'smush_meta',
		'keep_exif',
		'lossy_level',
		'api_version',
	);
	/**
	 * @var Smusher
	 */
	private $smusher;

	public function __construct( $media_item ) {
		$this->media_item = $media_item;
		$this->settings   = Settings::get_instance();
		$this->smusher    = new Smusher();
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

	public function set_stats( $stats ) {
		$this->stats = $stats;
	}

	private function get_meta_sizes() {
		$smush_meta = $this->get_smush_meta();

		return empty( $smush_meta['sizes'] )
			? array()
			: $smush_meta['sizes'];
	}

	private function get_size_meta( $size_key ) {
		$sizes = $this->get_meta_sizes();
		$size  = empty( $sizes[ $size_key ] )
			? array()
			: (array) $sizes[ $size_key ];

		return empty( $size ) ? array() : $size;
	}

	private function size_meta_exists( $size_key ) {
		return ! empty( $this->get_size_meta( $size_key ) );
	}

	public function get_size_stats( $size_key ) {
		if ( empty( $this->size_stats[ $size_key ] ) ) {
			$this->size_stats[ $size_key ] = $this->prepare_size_stats( $size_key );
		}

		return $this->size_stats[ $size_key ];
	}

	private function prepare_size_stats( $size_key ) {
		$stats = new Media_Item_Stats();
		$stats->from_array( $this->get_size_meta( $size_key ) );

		return $stats;
	}

	public function save() {
		$meta = $this->make_smush_meta();
		if ( ! empty( $meta ) ) {
			update_post_meta( $this->media_item->get_id(), self::SMUSH_META_KEY, $meta );
			// TODO: the separate lossy meta is only necessary for the backup global stats, if enough time has passed and enough people have moved to the new stats then we can remove it
			if ( $this->get_lossy_level() ) {
				update_post_meta( $this->media_item->get_id(), self::LOSSY_META_KEY, 1 );
			} else {
				delete_post_meta( $this->media_item->get_id(), self::LOSSY_META_KEY );
			}
			$this->reset();
		}
	}

	public function is_optimized() {
		return ! $this->get_stats()->is_empty();
	}

	public function should_optimize() {
		if ( $this->media_item->is_skipped() || $this->media_item->has_errors() ) {
			return false;
		}

		return ! empty( $this->get_sizes_to_smush() );
	}

	public function should_reoptimize() {
		return $this->should_resmush();
	}

	public function optimize() {
		if ( ! $this->should_optimize() ) {
			return false;
		}

		$media_item        = $this->media_item;
		$file_paths        = array_map( function ( $size ) {
			return $size->get_file_path();
		}, $this->get_sizes_to_smush() );
		$responses         = $this->smusher->smush( $file_paths );
		$success_responses = array_filter( $responses );
		if ( count( $success_responses ) !== count( $responses ) ) {
			return false;
		}

		$media_item_stats = $this->create_media_item_stats_instance();
		foreach ( $responses as $size_key => $data ) {
			$this->update_from_response( $size_key, $data, $media_item_stats );
		}
		$this->set_stats( $media_item_stats );

		if ( $media_item_stats->get_bytes() >= 0 ) {
			do_action( 'wp_smush_image_optimised',
				$this->media_item->get_id(),
				$this->make_smush_meta(),
				$this->media_item->get_wp_metadata()
			);
		}

		// Update media item
		$media_item->save();

		// Update smush meta
		$this->save();

		return true;
	}

	private function prepare_stats() {
		$smush_meta = $this->get_smush_meta();
		$stats      = $this->create_media_item_stats_instance();
		$stats_data = empty( $smush_meta['stats'] )
			? array()
			: $smush_meta['stats'];
		$stats->from_array( $stats_data );
		$stats->set_lossy( (bool) $this->get_lossy_level() );

		return $stats;
	}

	private function get_smush_meta() {
		if ( is_null( $this->smush_meta ) ) {
			$this->smush_meta = $this->fetch_smush_meta();
		}

		return $this->smush_meta;
	}

	private function fetch_smush_meta() {
		$post_meta = get_post_meta( $this->media_item->get_id(), self::SMUSH_META_KEY, true );

		return empty( $post_meta ) || ! is_array( $post_meta )
			? array()
			: $post_meta;
	}

	public function keep_exif() {
		if ( is_null( $this->keep_exif ) ) {
			$this->keep_exif = $this->prepare_keep_exif();
		}

		return $this->keep_exif;
	}

	private function prepare_keep_exif() {
		$smush_meta = $this->get_smush_meta();

		return isset( $smush_meta['stats']['keep_exif'] )
			? (int) $smush_meta['stats']['keep_exif']
			: 0;
	}

	public function set_keep_exif( $keep_exif ) {
		$this->keep_exif = (int) $keep_exif;
	}

	public function get_lossy_level() {
		if ( is_null( $this->lossy_level ) ) {
			$this->lossy_level = $this->prepare_lossy_level();
		}

		return $this->lossy_level;
	}

	private function prepare_lossy_level() {
		$smush_meta = $this->get_smush_meta();

		return empty( $smush_meta['stats']['lossy'] )
			? 0
			: (int) $smush_meta['stats']['lossy'];
	}

	public function set_lossy_level( $lossy ) {
		$this->lossy_level = (int) $lossy;
	}

	public function get_api_version() {
		if ( is_null( $this->api_version ) ) {
			$this->api_version = $this->prepare_api_version();
		}

		return $this->api_version;
	}

	private function prepare_api_version() {
		$smush_meta = $this->get_smush_meta();

		return empty( $smush_meta['stats']['api_version'] )
			? ''
			: $smush_meta['stats']['api_version'];
	}

	public function set_api_version( $api_version ) {
		$this->api_version = $api_version;
	}

	private function make_smush_meta() {
		$smush_meta = $this->get_smush_meta();

		// Stats
		$media_item_stats = $this->get_stats();
		if ( ! $media_item_stats->is_empty() ) {
			$smush_meta['stats'] = array_merge(
				empty( $smush_meta['stats'] ) ? array() : $smush_meta['stats'],
				$media_item_stats->to_array(),
				array(
					'keep_exif'   => $this->keep_exif(),
					'lossy'       => $this->get_lossy_level(),
					'api_version' => $this->get_api_version(),
				)
			);
		}

		// Sizes
		foreach ( $this->size_stats as $size_key => $size_stats ) {
			if ( ! $size_stats->is_empty() ) {
				$smush_meta['sizes'][ $size_key ] = (object) $size_stats->to_array();
			}
		}

		return $smush_meta;
	}

	private function should_resmush() {
		if ( ! $this->should_optimize() ) {
			return false;
		}

		if ( $this->is_next_level_available() ) {
			return true;
		}

		if ( $this->settings->get( 'strip_exif' ) && $this->keep_exif() ) {
			return true;
		}

		foreach ( $this->get_sizes_to_smush() as $size_key => $size ) {
			$is_smushed = $this->size_meta_exists( $size_key ) || $this->is_file_smushed( $size->get_file_path() );
			if ( ! $is_smushed ) {
				return true;
			}
		}

		return false;
	}

	public function is_next_level_available() {
		$current_lossy_level  = $this->get_lossy_level();
		$required_lossy_level = $this->settings->get_lossy_level_setting();

		return $current_lossy_level < $required_lossy_level;
	}

	private function is_file_smushed( $file_path ) {
		foreach ( $this->media_item->get_sizes() as $size_key => $size ) {
			if ( $size->get_file_path() === $file_path && $this->size_meta_exists( $size_key ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param $size_key
	 * @param object $data
	 * @param $media_item_stats Smush_Media_Item_Stats
	 */
	private function update_from_response( $size_key, $data, $media_item_stats ) {
		$size_stats = $this->get_size_stats( $size_key );

		$this->set_api_version( $data->api_version );
		$this->set_lossy_level( (int) $data->lossy );
		$this->set_keep_exif( empty( $data->keep_exif ) ? 0 : $data->keep_exif );

		// Update the size stats
		$size_stats->from_array( $this->size_stats_from_response( $size_stats, $data ) );

		// Add the size stats to the media item stats
		$media_item_stats->add( $size_stats );
		// TODO: maybe remove the lossy count from smush stats
		$media_item_stats->set_lossy( (bool) $this->get_lossy_level() );
	}

	/**
	 * @param $existing_stats Media_Item_Stats
	 * @param $data
	 *
	 * @return array
	 */
	private function size_stats_from_response( $existing_stats, $data ) {
		$size_before = max( $existing_stats->get_size_before(), $data->before_size ); // We want to use the oldest before size

		return array(
			'size_before' => $size_before,
			'size_after'  => $data->after_size,
			'time'        => $data->time,
		);
	}

	/**
	 * @return WP_Error
	 */
	public function get_errors() {
		return $this->get_smusher()->get_errors();
	}

	protected function reset() {
		foreach ( $this->reset_properties as $property ) {
			$this->$property = null;
		}
	}

	public function delete_data() {
		delete_post_meta( $this->media_item->get_id(), self::SMUSH_META_KEY );

		$this->reset();
	}

	/**
	 * @param $size Media_Item_Size
	 *
	 * @return bool
	 */
	public function should_optimize_size( $size ) {
		if ( ! $this->should_optimize() ) {
			return false;
		}

		return array_key_exists(
			$size->get_key(),
			$this->get_sizes_to_smush()
		);
	}

	/**
	 * @return Media_Item_Size[]
	 */
	private function get_sizes_to_smush() {
		return $this->media_item->get_smushable_sizes();
	}

	/**
	 * @return Smusher
	 */
	public function get_smusher() {
		return $this->smusher;
	}

	/**
	 * @return Smush_Media_Item_Stats
	 */
	private function create_media_item_stats_instance() {
		return new Smush_Media_Item_Stats();
	}

	public function get_optimized_sizes_count() {
		$count = 0;
		$sizes = $this->get_sizes_to_smush();
		foreach ( $sizes as $size_key => $size ) {
			$size_stats = $this->get_size_stats( $size_key );
			if ( $size_stats && ! $size_stats->is_empty() ) {
				$count ++;
			}
		}

		return $count;
	}
}
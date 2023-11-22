<?php
/**
 * class-engine.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Processing engine.
 */
class Engine {

	const CACHE_GROUP = 'ixwps_engine';

	/**
	 * Fixed cache lifetime
	 *
	 * @var string
	 */
	const LIFETIME_ALGO_FIXED = 'fixed';

	/**
	 * Adaptive cache lifetime
	 *
	 * @var string
	 */
	const LIFETIME_ALGO_ADAPTIVE = 'adaptive';

	/**
	 * @var Engine_Stage[]
	 */
	private $stages = null;

	/**
	 * @var Matrix
	 */
	private $matrix = null;

	/**
	 * @var Engine_Timer
	 */
	private $timer = null;

	/**
	 * @var Engine_Timer
	 */
	private $process_timer = null;

	/**
	 * @var boolean
	 */
	private $caching = true;

	/**
	 * @var boolean
	 */
	private $is_cache_hit = false;

	/**
	 * @var boolean
	 */
	private $is_cache_write = false;

	/**
	 * @var integer
	 */
	private $count = 0;

	/**
	 * Engine cache lifetime
	 *
	 * @var int
	 */
	private $lifetime = Cache::HOUR;

	/**
	 * Engine cache lifetime algorithm
	 *
	 * @var string
	 */
	private $lifetime_algo = self::LIFETIME_ALGO_ADAPTIVE;

	/**
	 * Create an engine instance.
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		$this->stages = array();
		$this->matrix = new Matrix();

		$this->process_timer = new Engine_Timer( $this, 'processing' );
		$this->timer = new Engine_Timer( $this );

		$settings = Engine_Settings::get_instance();
		$engine_settings = $settings->get();
		if ( !is_array( $engine_settings ) ) {
			$engine_settings = array();
		}

		if (
			array_key_exists( 'caching', $engine_settings ) &&
			is_bool( $engine_settings['caching'] )
		) {
			$this->caching = boolval( $engine_settings['caching'] );
		}

		if (
			array_key_exists( 'lifetime', $engine_settings ) &&
			is_numeric( $engine_settings['lifetime'] )
		) {
			$this->lifetime = max( 0, intval( $engine_settings['lifetime'] ) );
		}

		if (
			array_key_exists( 'lifetime_algo', $engine_settings ) &&
			is_string( $engine_settings['lifetime_algo'] )
		) {
			$lifetime_algo = trim( strtolower( $engine_settings['lifetime_algo'] ) );
			switch ( $lifetime_algo ) {
				case self::LIFETIME_ALGO_FIXED:
					break;
				case self::LIFETIME_ALGO_ADAPTIVE:
					break;
				default:
					$lifetime_algo = self::LIFETIME_ALGO_ADAPTIVE;
			}
			$this->lifetime_algo = $lifetime_algo;
		}
	}

	/**
	 * Attach a stage to the engine.
	 *
	 * @param Engine_Stage $stage
	 */
	public function attach_stage( $stage ) {
		if ( $stage instanceof Engine_Stage ) {
			$this->stages[] = $stage;
			$stage->set_engine( $this );
		}
	}

	/**
	 * Count attached stages.
	 *
	 * @return int
	 */
	public function get_stage_count() {
		$count = 0;
		if ( $this->stages !== null ) {
			$count = count( $this->stages );
		}
		return $count;
	}

	/**
	 * Provide all parameters.
	 *
	 * @return array[]
	 */
	public function get_parameters() {
		$parameters = array();
		usort( $this->stages, array( $this, 'sort_stages' ) );
		foreach ( $this->stages as $stage ) {

			$parameters[] = array(
				'stage_id' => $stage->get_stage_id(),
				'parameters' => $stage->get_parameters()
			);
		}
		return $parameters;
	}

	/**
	 * Process stages.
	 */
	public function process() {

		do_action( 'woocommerce_product_search_engine_process_start', $this );

		$this->process_timer->start();

		usort( $this->stages, array( $this, 'sort_stages' ) );

		$ids = null;
		$counts = array();

		foreach ( $this->stages as $stage ) {

			$stage->get_matching_ids( $ids );

			$this->matrix->inc_stage();
			foreach ( $ids as $id ) {
				$this->matrix->inc( $id );
			}

			if ( count( $ids ) === 0 ) {

				$counts[] = 0;
				$this->matrix->purge();
				break;
			} else {
				$this->matrix->evaluate();
			}

			if ( $stage->is_sorting() ) {
				$this->matrix->arrange( $ids );
			}

			$counts[] = count( $ids );

			unset( $ids );
		}

		$this->count = min( $counts );

		$this->process_timer->stop();
		$this->process_timer->log( 'verbose' );

		do_action( 'woocommerce_product_search_engine_process_end', $this );
	}

	/**
	 * Provide found IDs.
	 *
	 * @return int[]
	 */
	public function get_ids() {

		$this->timer->start();

		if ( $this->caching ) {
			$cache_context = array(
				'parameters' => json_encode( $this->get_parameters() )
			);
			$cache_key = $this->get_cache_key( $cache_context );
			$cache = Cache::get_instance();
			$ids = $cache->get( $cache_key, self::CACHE_GROUP );
			if ( is_array( $ids ) ) {
				$this->count = count( $ids );
				$this->is_cache_hit = true;
				$this->timer->stop();
				$this->timer->log();

				$all_parameters = $this->get_parameters();
				foreach ( $all_parameters as $parameters ) {
					if (
						isset( $parameters['stage_id'] ) &&
						$parameters['stage_id'] === 'words' &&
						!empty( $parameters['parameters']['q'] )
					) {
						$search_query = $parameters['parameters']['q'];
						$search_query = apply_filters( 'woocommerce_product_search_request_search_query', $search_query );
						$record_search_query = \WooCommerce_Product_Search_Indexer::equalize( $search_query );
						\WooCommerce_Product_Search_Service::maybe_record_hit( $record_search_query, $this->count );
					}
				}
				return $ids;
			}
		}

		$this->process();

		$ids = $this->matrix->get_ids();
		$this->count = count( $ids );

		if ( $this->caching ) {
			$this->is_cache_write = $cache->set( $cache_key, $ids, self::CACHE_GROUP, $this->get_cache_lifetime() );
		}

		$this->timer->stop();
		$this->timer->log();

		return $ids;
	}

	/**
	 * Provide the matrix.
	 *
	 * @return Matrix
	 */
	public function get_matrix() {
		return $this->matrix;
	}

	/**
	 * Priority-based sorting callback.
	 *
	 * @param Engine_Stage $s1
	 * @param Engine_Stage $s2
	 *
	 * @return int
	 */
	public function sort_stages( $s1, $s2 ) {
		$result = 0;
		if ( $s1 instanceof Engine_Stage && $s2 instanceof Engine_Stage ) {
			$result = $s1->get_priority() - $s2->get_priority();
		}
		return $result;
	}

	/**
	 * Computes a cache key based on the parameters provided.
	 *
	 * @param array $parameters
	 *
	 * @return string
	 */
	protected function get_cache_key( $parameters ) {

		return sha1( json_encode ( $parameters ) );
	}

	/**
	 * Returns the cache lifetime for stored results in seconds.
	 *
	 * @return int
	 */
	protected function get_cache_lifetime() {
		$lifetime = $this->lifetime;
		switch ( $this->lifetime_algo ) {
			case self::LIFETIME_ALGO_ADAPTIVE:
				if ( $this->lifetime > 1 ) {
					$cache = Cache::get_instance();
					if ( $cache->get_use_slot() ) {
						$slot = new Slot( null, $cache->get_slot_path() );
						$war = $slot->get_current_war();
						$f = 1 - min( 1, $war * $war );
						$lifetime = max( 1, intval( round( $this->lifetime * $f ) ) );
						if ( $this->lifetime !== $lifetime ) {
							if ( WPS_CACHE_DEBUG ) {
								wps_log_verbose( sprintf( 'Engine cache lifetime adapted: %d -> %d', $this->lifetime, $lifetime ) );
							}
						}
					}
				}
				break;

		}
		return $lifetime;
	}

	/**
	 * Data origin is cache?
	 *
	 * @return boolean
	 */
	public function is_cache_hit() {
		return $this->is_cache_hit;
	}

	/**
	 * Data written to cache?
	 *
	 * @return boolean
	 */
	public function is_cache_write() {
		return $this->is_cache_write;
	}

	/**
	 * Engine result count.
	 *
	 * @return int
	 */
	public function get_count() {
		return $this->count;
	}
}

<?php
/**
 * class-engine-stage.php
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
 * Base stage abstract class.
 */
abstract class Engine_Stage {

	/**
	 * Common default cache lifetime is never expire.
	 *
	 * @var int
	 */
	const CACHE_LIFETIME = PHP_INT_MAX;

	/**
	 * Stage priority
	 *
	 * @var int
	 */
	const PRIORITY = 0;

	/**
	 * @var string
	 */
	protected $stage_id = null;

	/**
	 * @var boolean
	 */
	protected $enabled = true;

	/**
	 * @var Engine
	 */
	protected $engine = null;

	/**
	 * @var int
	 */
	protected $priority = 0;

	/**
	 * @var string
	 */
	private $cache_group = null;

	/**
	 * @var boolean
	 */
	protected $caching = true;

	/**
	 * @var boolean
	 */
	protected $is_cache_hit = false;

	/**
	 * @var boolean
	 */
	protected $is_cache_write = false;

	/**
	 * @var integer
	 */
	protected $count = 0;

	/**
	 * @var Engine_Timer
	 */
	protected $timer = null;

	/**
	 * @var boolean
	 */
	protected $variations = false;

	/**
	 * Limit the results to so many entries
	 *
	 * @var int
	 */
	protected $limit = null;

	/**
	 * Instance cache lifetime
	 *
	 * @var int
	 */
	protected $lifetime = null;

	/**
	 * Enable logging
	 *
	 * @var boolean
	 */
	protected $log = null;

	/**
	 * Base constructor.
	 */
	public function __construct( $args = array() ) {

		$this->timer = new Engine_Timer( $this );

		$this->lifetime = static::CACHE_LIFETIME;

		$this->priority = static::PRIORITY;

		$settings = Engine_Stage_Settings::get_instance();
		$stages = $settings->get();
		if ( !is_array( $stages ) ) {
			$stages = array();
		}

		if (
			array_key_exists( $this->stage_id, $stages ) &&
			is_array( $stages[$this->stage_id] )
		) {

			if (
				array_key_exists( 'enabled', $stages[$this->stage_id] ) &&
				is_bool( $stages[$this->stage_id]['enabled'] )
			) {
				$this->enabled = boolval( $stages[$this->stage_id]['enabled'] );
			}

			if (
				array_key_exists( 'priority', $stages[$this->stage_id] ) &&
				is_numeric( $stages[$this->stage_id]['priority'] )
			) {
				$this->priority = intval( $stages[$this->stage_id]['lifetime'] );
			}

			if (
				array_key_exists( 'lifetime', $stages[$this->stage_id] ) &&
				is_numeric( $stages[$this->stage_id]['lifetime'] )
			) {
				$this->lifetime = max( 0, intval( $stages[$this->stage_id]['lifetime'] ) );
			}

			if (
				array_key_exists( 'caching', $stages[$this->stage_id] ) &&
				is_bool( $stages[$this->stage_id]['caching'] )
			) {
				$this->caching = boolval( $stages[$this->stage_id]['caching'] );
			}

			if (
				array_key_exists( 'log', $stages[$this->stage_id] ) &&
				is_bool( $stages[$this->stage_id]['log'] )
			) {
				$this->timer->set_log( $stages[$this->stage_id]['log'] );
			}
		}

		if ( is_array( $args ) && count( $args ) > 0 ) {
			$params = array();
			foreach ( $args as $key => $value ) {
				$set_param = true;
				switch ( $key ) {
					case 'variations':
						$value = boolval( $value );
						break;
					case 'limit':
						if ( is_numeric( $value ) ) {
							$value = intval( $value );
							if ( $value <= 0 ) {
								$value = null;
							}
						} else {
							$value = null;
						}
						break;
					case 'log':
						if ( is_bool( $value ) ) {
							$value = boolval( $value );
						} else {
							$value = null;
						}
						break;
					default:
						$set_param = false;
				}
				if ( $set_param ) {
					$params[$key] = $value;
				}
			}
			foreach ( $params as $key => $value ) {
				$this->$key = $value;
			}
		}

		if ( $this->limit === null ) {
			if ( apply_filters( 'woocommerce_product_search_engine_stage_apply_object_limit', true, $this ) ) {
				$limit = \WooCommerce_Product_Search_Controller::get_object_limit();
				if ( $limit > 0 ) {
					$this->limit = $limit;
				}
			}
		}
	}

	/**
	 * Provides the stage identifier.
	 *
	 * @return string
	 */
	public function get_stage_id() {
		return $this->stage_id;
	}

	/**
	 * Stage parameters.
	 *
	 * @return array
	 */
	public function get_parameters() {
		return array(
			'limit' => $this->limit,
			'variations' => $this->variations
		);
	}

	/**
	 * Cache context.
	 *
	 * @return array
	 */
	protected function get_cache_context() {
		$cache_context = $this->get_parameters();
		$cache_context = apply_filters( 'woocommerce_product_search_engine_stage_cache_context', $cache_context, $this );
		return $cache_context;
	}

	/**
	 * Set the engine for this stage.
	 *
	 * @param Engine $engine
	 */
	public function set_engine( $engine ) {
		$this->engine = $engine;
	}

	/**
	 * Provide the stage's priority.
	 *
	 * @return int
	 */
	public function get_priority() {
		return $this->priority;
	}

	/**
	 * Computes a cache key based on the parameters provided.
	 *
	 * @param array $parameters set of parameters for which to compute the key
	 *
	 * @return string
	 */
	protected function get_cache_key( $parameters ) {

		return md5( json_encode( $parameters ) );
	}

	/**
	 * Returns the cache lifetime for stored results in seconds.
	 *
	 * @return int
	 */
	protected function get_cache_lifetime() {
		return $this->lifetime;
	}

	/**
	 * Retrieve matching IDs for the stage.
	 *
	 * @param int[] $ids provides matching IDs retrieved
	 */
	abstract public function get_matching_ids( &$ids );

	/**
	 * Is this a sorting stage?
	 *
	 * @return boolean
	 */
	public function is_sorting() {
		return false;
	}

	/**
	 * Stage uses caching?
	 *
	 * @return boolean
	 */
	public function is_caching() {
		return $this->caching;
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
	 * Stage result count.
	 *
	 * @return int
	 */
	public function get_count() {
		return $this->count;
	}
}

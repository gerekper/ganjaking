<?php
/**
 * class-transitory-cache.php
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
 * Transitory cache.
 *
 * Provides a volatile object cache.
 */
class Transitory_Cache extends Cache_Base {

	protected $id = 'transitory';

	/**
	 * @var array
	 */
	private $cache = array();

	/**
	 * Number of entries.
	 *
	 * @var int
	 */
	private $count = 0;

	/**
	 * Maximum number of entries.
	 *
	 * @var int
	 */
	private $max_count = null;

	/**
	 * Maximum magnitude per entry.
	 *
	 * @var int
	 */
	private $max_magnitude = 1048576;

	/**
	 * Total size in bytes.
	 *
	 * @var int
	 */
	private $size = 0;

	/**
	 * Extent.
	 *
	 * @var int
	 */
	private $extent = 0;

	/**
	 * Maximum extent of magnitudes cached.
	 *
	 * @var int
	 */
	private $max_extent = 16777216;

	/**
	 * Minimum free memory, bytes.
	 *
	 * @var int
	 */
	private $min_memory = 67108864;

	/**
	 * Minimum free memory unit.
	 *
	 * @var string
	 */
	private $min_memory_unit = 'bytes';

	/**
	 * Memory limit, bytes.
	 *
	 * @var int
	 */
	private $memory_limit = null;

	/**
	 * @var boolean
	 */
	private $multisite = false;

	/**
	 * @var boolean
	 */
	protected $ui = false;

	/**
	 * Create an instance.
	 *
	 * @param array $params instance parameters
	 * @param int $params['priority']
	 */
	public function __construct( $params = null ) {
		parent::__construct( $params );
		$this->active = true;
		$this->volatile = true;
		$this->multisite = is_multisite();
		$this->memory_limit = \WooCommerce_Product_Search_System::get_memory_limit();

		if ( isset( $params['max_count'] ) && is_numeric( $params['max_count'] ) ) {
			$max_count = max( 0, intval( $params['max_count'] ) );
			if ( $max_count === 0 ) {
				$max_count = null;
			}
			$this->max_count = $max_count;
		}

		if ( isset( $params['max_extent'] ) && is_numeric( $params['max_extent'] ) ) {
			$max_extent = max( 0, intval( $params['max_extent'] ) );
			if ( $max_extent === 0 ) {
				$max_extent = null;
			}
			$this->max_extent = $max_extent;
		}

		if ( isset( $params['max_magnitude'] ) && is_numeric( $params['max_magnitude'] ) ) {
			$max_magnitude = max( 0, intval( $params['max_magnitude'] ) );
			if ( $max_magnitude === 0 ) {
				$max_magnitude = null;
			}
			$this->max_magnitude = $max_magnitude;
		}

		if ( isset( $params['min_memory'] ) ) {
			if ( is_numeric( $params['min_memory'] ) ) {
				$min_memory = max( 0, intval( $params['min_memory'] ) );
				if ( $min_memory === 0 ) {
					$min_memory = null;
				}
				$this->min_memory = $min_memory;
			} else if ( is_string( $params['min_memory'] ) ) {
				$mark = strpos( $params['min_memory'], '%' );
				if ( $mark !== false && $mark > 0 ) {
					$min_memory = floatval( trim( substr( $params['min_memory'], 0, $mark ) ) );
					if ( $min_memory < 0.0 ) {
						$this->min_memory = null;
					} else if ( $min_memory >= 100.0 ) {
						$this->min_memory = 100.0;
						$this->min_memory_unit = 'percent';
						$this->active = false;
					} else {
						$this->min_memory = sprintf( '%.2f', $min_memory );
						$this->min_memory_unit = 'percent';
					}
				} else {
					$parsed = File_Cache::parse_storage_bytes( $params['min_memory'] );
					$this->min_memory = max( 0, intval( $parsed['bytes'] ) );
				}
			}
		}
		if (
			$this->min_memory !== null &&
			$this->memory_limit !== null &&
			$this->min_memory_unit === 'bytes' &&
			$this->min_memory >= $this->memory_limit
		) {
			$this->active = false;
		}
	}

	/**
	 * Flush the cache.
	 *
	 * @param string $group to flush a particular group only
	 *
	 * @return boolean
	 */
	public function flush( $group = null ) {
		$blog_id = get_current_blog_id();
		if ( $group === null ) {
			if ( isset( $this->cache[$blog_id] ) ) {
				foreach ( $this->cache[$blog_id] as $group ) {
					foreach ( $group as $key ) {
						$this->count = max( 0, $this->count - 1 );
						if ( $this->max_extent !== null ) {
							if ( $this->cache[$blog_id][$group][$key] instanceof Cache_Object ) {
								$this->extent = max( 0, $this->extent - $this->cache[$blog_id][$group][$key]->get_magnitude() );
							}
						}
					}
				}
				unset( $this->cache[$blog_id] );
			}
		} else {
			if ( isset( $this->cache[$blog_id][$group] ) ) {
				foreach ( $this->cache[$blog_id][$group] as $key ) {
					$this->count = max( 0, $this->count - 1 );
					if ( $this->max_extent !== null ) {
						if ( $this->cache[$blog_id][$group][$key] instanceof Cache_Object ) {
							$this->extent = max( 0, $this->extent - $this->cache[$blog_id][$group][$key]->get_magnitude() );
						}
					}
				}
				unset( $this->cache[$blog_id][$group] );
			}
		}
		return true;
	}

	public function gc( $group = null ) {
		return true;
	}

	/**
	 * Get from cache.
	 *
	 * @param string $key
	 * @param string $group
	 *
	 * @return mixed|null
	 */
	public function get( $key, $group = '' ) {
		$value = null;
		$blog_id = get_current_blog_id();
		$group = $this->get_group( $group );
		$object = null;
		if (
			isset( $this->cache[$blog_id] ) &&
			isset( $this->cache[$blog_id][$group] ) &&
			isset( $this->cache[$blog_id][$group][$key] )
		) {
			$object = $this->cache[$blog_id][$group][$key];
		}
		if ( !( $object instanceof Cache_Object ) ) {
			$object = null;
		} else {
			if ( $object->has_expired() ) {
				$this->delete( $key, $group );
				$object = null;
			}
		}
		if ( $object !== null ) {
			$value = $object->get_value();
		}
		return $value;
	}

	/**
	 * Store in cache.
	 *
	 * @param string $key
	 * @param mixed $data
	 * @param string $group
	 * @param int $expire
	 *
	 * @return boolean
	 */
	public function set( $key, $data, $group = '', $expire = 0 ) {
		$object = new Cache_Object( $key, $data, $expire );

		if ( $this->max_count !== null && $this->count + 1 > $this->max_count ) {
			return false;
		}

		if ( $this->min_memory !== null && $this->memory_limit !== null ) {
			$memory = memory_get_usage();
			switch ( $this->min_memory_unit ) {
				case 'percent':
					if ( $this->memory_limit > 0 ) {
						$unused = 100.0 * ( $this->memory_limit - $memory ) / $this->memory_limit;
						if ( $unused < $this->min_memory ) {
							return false;
						}
					}
					break;
				default:
					if ( $this->memory_limit - $memory < $this->min_memory ) {
						return false;
					}
			}
		}

		if ( $this->max_magnitude !== null || $this->max_extent !== null ) {
			$magnitude = $object->get_magnitude();
			if ( $this->max_magnitude !== null && $magnitude > $this->max_magnitude ) {
				return false;
			}
			if ( $this->max_extent !== null ) {
				if ( $this->extent + $magnitude > $this->max_extent ) {
					return false;
				}
				$this->extent += $magnitude;
			}
		}

		$blog_id = get_current_blog_id();
		$group = $this->get_group( $group );
		if ( !isset( $this->cache[$blog_id] ) ) {
			$this->cache[$blog_id] = array();
		}
		if ( !isset( $this->cache[$blog_id][$group] ) ) {
			$this->cache[$blog_id][$group] = array();
		}
		$this->count++;
		$this->cache[$blog_id][$group][$key] = $object;
		return true;
	}

	/**
	 * Delete from cache.
	 *
	 * @param string $key
	 * @param string $group
	 *
	 * @return boolean
	 */
	public function delete( $key, $group = '' ) {
		$blog_id = get_current_blog_id();
		$deleted = false;
		$group = $this->get_group( $group );
		if (
			isset( $this->cache[$blog_id] ) &&
			isset( $this->cache[$blog_id][$group] ) &&
			isset( $this->cache[$blog_id][$group][$key] )
		) {
			$this->count = max( 0, $this->count - 1 );
			if ( $this->max_extent !== null ) {
				if ( $this->cache[$blog_id][$group][$key] instanceof Cache_Object ) {
					$this->extent = max( 0, $this->extent - $this->cache[$blog_id][$group][$key]->get_magnitude() );
				}
			}
			unset( $this->cache[$blog_id][$group][$key] );
			$deleted = true;
		}
		return $deleted;
	}

}

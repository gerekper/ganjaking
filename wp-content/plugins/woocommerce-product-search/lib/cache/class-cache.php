<?php
/**
 * class-cache.php
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
 * Cache handler.
 *
 * Role and group-based caching, lifespan management.
 *
 * For caching to be effective, a persistent caching solution must be used which supports a persistent Object Cache:
 *
 * - the WordPress Object Cache supported by a suitable caching plugin
 * - a Memcached instance
 * - a Redis instance
 * - the File Cache
 *
 * See https://developer.wordpress.org/reference/classes/wp_object_cache/#persistent-cache-plugins for a list of suggested solutions.
 */
class Cache extends Cache_Base {

	/**
	 * Default instance ID
	 *
	 * @var string
	 */
	const DEFAULT_ID = 'default';

	/**
	 * Save to one
	 *
	 * @var string
	 */
	const STRATEGY_ONE = 'one';

	/**
	 * Save to all
	 *
	 * @var string
	 */
	const STRATEGY_ALL = 'all';

	/**
	 * Cache registry
	 *
	 * @var Cache[string]
	 */
	private static $registry = array();

	/**
	 * Cache instance ID
	 *
	 * @var string
	 */
	protected $id = self::DEFAULT_ID;

	/**
	 * Instance parameters
	 *
	 * @var array
	 */
	private $parameters = array();

	/**
	 * Instance hash
	 *
	 * @var string
	 */
	private $hash = null;

	/**
	 * Attached caches
	 *
	 * @var Cache_Base[]
	 */
	private $caches = array();

	/**
	 * Cache strategy
	 *
	 * @var string
	 */
	private $strategy = self::STRATEGY_ONE;

	/**
	 * Slot file path
	 *
	 * @var string
	 */
	private $slot_path = null;

	/**
	 * Use slot
	 *
	 * @var boolean
	 */
	private $use_slot = true;

	/**
	 * Parameter sorting.
	 *
	 * @param array $s1
	 * @param array $s2
	 *
	 * @return int
	 */
	public static function parameter_sort( $s1, $s2 ) {

		$p1 = is_array( $s1 ) && isset( $s1['priority'] ) ? intval( $s1['priority'] ) : 0;
		$p2 = is_array( $s2 ) && isset( $s2['priority'] ) ? intval( $s2['priority'] ) : 0;
		return $p2 - $p1;
	}

	/**
	 * Create a cache instance.
	 *
	 * If a specific cache configuration is not provided via the $caches parameter,
	 * the cache configuration is determined by the WPS_CACHES constant, or by the
	 * stored or default cache settings (in that order).
	 *
	 * @param array|null $caches
	 *
	 * @return Cache
	 */
	public static function create_instance( $caches = null ) {
		$instance = null;

		if ( $caches === null ) {
			$cache_settings = Cache_Settings::get_instance();
			$caches = $cache_settings->get();
		}
		$caches = self::process( $caches );
		$id = isset( $caches['id'] ) ? $caches['id'] : self::DEFAULT_ID;

		if ( key_exists( $id, self::$registry ) ) {

			$hash = self::get_parameter_hash( $caches );
			$instance = self::$registry[$id];
			if ( $instance->get_hash() !== $hash ) {
				$instance = null;
			}
		}
		if ( $instance === null ) {
			$instance = new Cache( $caches );
			self::$registry[$id] = $instance;
		}
		return $instance;
	}

	public static function delete_instance( $id = null ) {
		if ( $id === null ) {
			$id = self::DEFAULT_ID;
		}
		if ( key_exists( $id, self::$registry ) ) {
			unset( self::$registry[$id] );
		}
	}

	/**
	 * Provide a cache instance.
	 *
	 * @param string $id
	 *
	 * @return Cache|null
	 */
	public static function get_instance( $id = null ) {
		if ( $id === null ) {
			$id = self::DEFAULT_ID;
		}
		$instance = null;
		if ( isset( self::$registry[$id] ) ) {
			$instance = self::$registry[$id];
		} else {

			$instance = self::create_instance();
		}
		return $instance;
	}

	/**
	 * Provide the hash for the parameters.
	 *
	 * @param array $caches
	 *
	 * @return string
	 */
	private static function get_parameter_hash( $caches ) {
		return md5( json_encode( $caches ) );
	}

	/**
	 * Process instance parameters
	 *
	 * @param array $caches
	 *
	 * @return array
	 */
	private static function process( $caches ) {
		$result = array();
		if ( is_array( $caches ) ) {
			foreach ( $caches as $key => $args ) {

				if ( isset( $args['enabled'] ) && !$args['enabled'] ) {
					continue;
				}
				$host = null;
				if ( isset( $args['host'] ) && is_string( $args['host'] ) ) {
					$host = $args['host'];
				}
				$port = null;
				if ( isset( $args['port'] ) && ( is_numeric( $args['port'] ) || is_string( $args['port'] ) ) ) {
					if ( is_numeric( $args['port'] ) ) {
						$port = intval( $args['port'] );
					} else {
						$port = $args['port'];
					}
				}
				$weight = null;
				if ( isset( $args['weight'] ) && ( is_numeric( $args['weight'] ) || is_string( $args['weight'] ) ) ) {
					if ( is_numeric( $args['weight'] ) ) {
						$weight = intval( $args['weight'] );
					} else {
						$weight = $args['weight'];
					}
				}
				$username = null;
				if ( isset( $args['username'] ) && is_string( $args['username'] ) ) {
					$username = $args['username'];
				}
				$password = null;
				if ( isset( $args['password'] ) && is_string( $args['password'] ) ) {
					$password = $args['password'];
				}
				$max_files = null;
				if ( isset( $args['max_files'] ) && is_numeric( $args['max_files'] ) ) {
					$max_files = intval( $args['max_files'] );
				}
				$max_size = null;
				if ( isset( $args['max_size'] ) && ( is_numeric( $args['max_size'] ) || is_string( $args['max_size'] ) ) ) {
					$max_size = is_numeric( $args['max_size'] ) ? intval( $args['max_size'] ) : trim( $args['max_size'] );
				}
				$min_free_disk_space = null;
				if ( isset( $args['min_free_disk_space'] ) && ( is_numeric( $args['min_free_disk_space'] ) || is_string( $args['min_free_disk_space'] ) ) ) {
					$min_free_disk_space = is_numeric( $args['min_free_disk_space'] ) ? intval( $args['min_free_disk_space'] ) : trim( $args['min_free_disk_space'] );
				}
				$gc_interval = null;
				if ( isset( $args['gc_interval'] ) && is_numeric( $args['gc_interval'] ) ) {
					$gc_interval = intval( $args['gc_interval'] );
				}
				$gc_time_limit = null;
				if ( isset( $args['gc_time_limit'] ) && is_numeric( $args['gc_time_limit'] ) ) {
					$gc_time_limit = intval( $args['gc_time_limit'] );
				}
				$purge = null;
				if ( isset( $args['purge'] ) && is_bool( $args['purge'] ) ) {
					$purge = boolval( $args['purge'] );
				}
				$priority = null;
				if ( isset( $args['priority'] ) && is_numeric( $args['priority'] ) ) {
					$priority = intval( $args['priority'] );
				}
				$max_count = null;
				if ( isset( $args['max_count'] ) && is_numeric( $args['max_count'] ) ) {
					$max_count = intval( $args['max_count'] );
				}
				$max_extent = null;
				if ( isset( $args['max_extent'] ) && is_numeric( $args['max_extent'] ) ) {
					$max_extent = intval( $args['max_extent'] );
				}
				$max_magnitude = null;
				if ( isset( $args['max_magnitude'] ) && is_numeric( $args['max_magnitude'] ) ) {
					$max_magnitude = intval( $args['max_magnitude'] );
				}
				$min_memory = null;
				if ( isset( $args['min_memory'] ) ) {
					if ( is_numeric( $args['min_memory'] ) ) {
						$min_memory = intval( $args['min_memory'] );
					} else if ( is_string( $args['min_memory'] ) ) {
						$min_memory = trim( $args['min_memory'] );
					}
				}
				$ui = null;
				if ( isset( $args['ui'] ) && is_bool( $args['ui'] ) ) {
					$ui = boolval( $args['ui'] );
				}
				switch ( $key ) {
					case 'id':
						if ( is_string( $args ) ) {
							$result['id'] = $args;
						}
						break;
					case 'strategy':
						if ( is_string( $args ) ) {
							$result['strategy'] = $args;
						}
						break;
					case 'use_slot':
						if ( is_bool( $args ) ) {
							$result['use_slot'] = boolval( $args );
						}
						break;
					case 'object_cache':
						$result[$key] = array(
							'key' => $key,
							'priority' => $priority
						);
						break;
					case 'memcached':
						$result[$key] = array(
							'key' => $key,
							'host' => $host,
							'port' => $port,
							'weight' => $weight,
							'username' => $username,
							'password' => $password,
							'priority' => $priority
						);
						break;
					case 'redis':
						$result[$key] = array(
							'key' => $key,
							'host' => $host,
							'port' => $port,
							'username' => $username,
							'password' => $password,
							'priority' => $priority
						);
						break;
					case 'file_cache':
						$result[$key] = array(
							'key' => $key,
							'max_files' => $max_files,
							'max_size' => $max_size,
							'min_free_disk_space' => $min_free_disk_space,
							'gc_interval' => $gc_interval,
							'gc_time_limit' => $gc_time_limit,
							'purge' => $purge,
							'priority' => $priority
						);
						break;
					case 'transitory':
						$result[$key] = array(
							'key' => $key,
							'priority' => $priority,
							'max_count' => $max_count,
							'max_extent' => $max_extent,
							'max_magnitude' => $max_magnitude,
							'min_memory' => $min_memory,
							'ui' => $ui
						);
						break;
				}
			}
		}
		uasort( $result, array( __CLASS__, 'parameter_sort' ) );
		return $result;
	}

	/**
	 * Create an instance with attached caches.
	 *
	 * @param array $caches
	 */
	private function __construct( $caches ) {

		foreach ( $caches as $key => $args ) {
			switch ( $key ) {
				case 'id':

					if ( is_string( $args ) ) {
						$this->id = $args;
					}
					break;
				case 'strategy':

					if ( is_string( $args ) ) {
						switch ( $args ) {
							case self::STRATEGY_ALL:
							case self::STRATEGY_ONE:
								$this->strategy = $args;
								break;
						}
					}
					break;
				case 'use_slot':
					if ( is_bool( $args ) ) {
						$this->use_slot = boolval( $args );
					}
					break;
				default:
					$cache = null;

					$enabled = isset( $args['enabled'] ) ? boolval( $args['enabled'] ) : true;
					if ( $enabled ) {
						switch ( $key ) {
							case 'transitory':
								$cache = new Transitory_Cache( $args );
								break;
							case 'object_cache':
								$cache = new Object_Cache( $args );
								break;
							case 'memcached':
								$cache = new Memcached_Cache( $args );
								break;
							case 'redis':
								if (
									class_exists( '\Redis' ) &&
									( !defined( 'WPS_REDIS_CACHE_API' ) || WPS_REDIS_CACHE_API !== true )
								) {
									$cache = new PhpRedis_Cache( $args );
								} else {
									$cache = new Redis_Cache( $args );
								}
								break;
							case 'file_cache':
								$cache = new File_Cache( $args );
								break;
							default:

								$instance = apply_filters( 'woocommerce_product_search_get_cache_instance', null, $key, $args );
								if ( $instance !== null && $instance instanceof Cache_Base ) {
									$cache = $instance;
								}
						}
						if ( $key !== 'id' ) {
							if ( $cache !== null && $cache->is_active() ) {
								$this->caches[] = $cache;
							} else {
								wps_log_warning( sprintf( 'Requested cache is not active: %s', esc_html( $key ) ) );
							}
						}
					}
			}
		}
		$this->parameters = $caches;
		$this->hash = self::get_parameter_hash( $caches );
	}

	/**
	 * Provide the instance ID
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Provide the instance hash
	 *
	 * @return string
	 */
	public function get_hash() {
		return $this->hash;
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

		/**
		 * @var Cache_Base $cache
		 */
		foreach ( $this->caches as $cache ) {
			$cache->set_unigroup( $this->unigroup );
			$value = $cache->get( $key, $group );
			if ( $value !== null ) {
				break;
			}
		}
		return $value;
	}

	/**
	 * Provide the slot path.
	 *
	 * @return string
	 */
	public function get_slot_path() {
		if ( $this->slot_path === null ) {
			$slot_path = untrailingslashit( WP_CONTENT_DIR ) . DIRECTORY_SEPARATOR . '.wps-slots';
			if ( is_multisite() ) {
				$blog_id = get_current_blog_id();
				if ( intval( $blog_id ) !== 1 ) {
					$slot_path .= '-' . intval( $blog_id );
				}
			}
			$this->slot_path = apply_filters( 'woocommerce_product_search_cache_slot_path', $slot_path );
		}
		return $this->slot_path;
	}

	/**
	 * Whether the instance uses slot.
	 *
	 * @return boolean
	 */
	public function get_use_slot() {
		return $this->use_slot && count( $this->caches ) > 0 ;
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

		$result = false;

		/**
		 * @var Cache_Base $cache
		 */
		foreach ( $this->caches as $cache ) {
			$cache->set_unigroup( $this->unigroup );

			$cached = $cache->set( $key, $data, $group, $expire );
			$result = $cached || $result;

			if (
				$result &&
				$this->strategy === self::STRATEGY_ONE &&
				!$cache->is_volatile()
			) {

				break;

			}
		}

		if ( count( $this->caches ) > 0 ) {
			if ( $this->use_slot ) {
				$slot = new Slot( null, $this->get_slot_path() );
				$slot->push( $result );
			}
		}

		return $result;

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

		$result = false;
		foreach ( $this->caches as $cache ) {
			$unigroup = $cache->get_unigroup();
			$cache->set_unigroup( $this->unigroup );

			$deleted = $cache->delete( $key, $group );
			$cache->set_unigroup( $unigroup );
			$result = $deleted || $result;
		}
		return $result;
	}

	/**
	 * Flush the cache.
	 *
	 * @param string $group to flush a particular group only (if supported)
	 *
	 * @return boolean
	 */
	public function flush( $group = null ) {

		$flushes = array();
		foreach ( $this->caches as $cache ) {
			$flushes[] = $cache->flush( $group );
		}
		$flushed = count( $flushes ) > 0 && !in_array( false, $flushes );
		return $flushed;
	}

	/**
	 * Flush a particular cache.
	 *
	 * @param string $cache_id the cache identifier
	 * @param string $group to flush a particular group only (if supported)
	 *
	 * @return boolean
	 */
	public function flush_cache( $cache_id, $group = null ) {
		$flushes = array();
		foreach ( $this->caches as $cache ) {
			if ( $cache->get_id() === $cache_id ) {
				$flushes[] = $cache->flush( $group );
			}
		}
		$flushed = count( $flushes ) > 0 && !in_array( false, $flushes );
		return $flushed;
	}

	/**
	 * Whether the instance has a specific cache.
	 *
	 * @param string $cache_id the cache identifier
	 *
	 * @return boolean
	 */
	public function has_cache( $cache_id ) {
		$has = false;
		foreach ( $this->caches as $cache ) {
			if ( $cache->get_id() === $cache_id ) {
				$has = true;
				break;
			}
		}
		return $has;
	}

	/**
	 * Provide the associated cache object if the instance has it.
	 *
	 * @param string $cache_id the cache identifier
	 *
	 * @return \com\itthinx\woocommerce\search\engine\Cache_Base | null
	 */
	public function get_cache( $cache_id ) {
		$object = null;
		foreach ( $this->caches as $cache ) {
			if ( $cache->get_id() === $cache_id ) {
				$object = $cache;
				break;
			}
		}
		return $object;
	}

	/**
	 * GC the cache.
	 *
	 * @param string $group to do a particular group only (if supported)
	 */
	public function gc( $group = null ) {
		$gcs = array();
		foreach ( $this->caches as $cache ) {
			$gcs[] = $cache->gc( $group );
		}
		$gc = count( $gcs ) > 0 && !in_array( false, $gcs );
		return $gc;
	}

	/**
	 * GC a particular cache.
	 *
	 * @param string $cache_id the cache identifier
	 * @param string $group to GC a particular group only (if supported)
	 *
	 * @return boolean
	 */
	public function gc_cache( $cache_id, $group = null ) {
		$gcs = array();
		foreach ( $this->caches as $cache ) {
			if ( $cache->get_id() === $cache_id ) {
				$gcs[] = $cache->gc( $group );
			}
		}
		$gc = count( $gcs ) > 0 && !in_array( false, $gcs );
		return $gc;
	}
}

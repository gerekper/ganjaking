<?php
/**
 * class-file-cache.php
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
 * File-based cache.
 *
 * Provides an effective persistent Object Cache.
 *
 * Role and group-based caching, lifespan management.
 */
class File_Cache extends Cache_Base {

	/**
	 * Maximum cache files, configuration key
	 *
	 * @var string
	 */
	const MAX_FILES = 'max_files';

	/**
	 * Maximum number of cache files, default
	 *
	 * @var int
	 */
	const MAX_FILES_DEFAULT = 0;

	/**
	 * Maximum cache size, configuration key
	 *
	 * @var string
	 */
	const MAX_SIZE = 'max_size';

	/**
	 * Maximum size of cache files, default
	 *
	 * @var int
	 */
	const MAX_SIZE_DEFAULT = 0;

	/**
	 * Minimum free disk space, configuration key
	 *
	 * @var string
	 */
	const MIN_FREE_DISK_SPACE = 'min_free_disk_space';

	/**
	 * Minimum free disk space, of total, default
	 *
	 * @var string
	 */
	const MIN_FREE_DISK_SPACE_DEFAULT = '5%';

	/**
	 * File prelude
	 *
	 * @var string
	 */
	const PRELUDE = '<?php exit; ?>';

	/**
	 * GC interval, configuration key
	 *
	 * @var string
	 */
	const GC_INTERVAL = 'gc_interval';

	/**
	 * GC interval in seconds
	 *
	 * @var int
	 */
	const GC_INTERVAL_DEFAULT = 1800;

	/**
	 * GC time limit, configuration key
	 *
	 * @var string
	 */
	const GC_TIME_LIMIT = 'gc_time_limit';

	/**
	 * GC time limit in seconds
	 *
	 * @var int
	 */
	const GC_TIME_LIMIT_DEFAULT = 30;

	/**
	 * Minimum GC time limit in seconds, 0 means unlimited
	 *
	 * @var int
	 */
	const GC_TIME_LIMIT_MIN = 0;

	/**
	 * Purge, configuration key
	 *
	 * @var string
	 */
	const PURGE = 'purge';

	/**
	 * Purge default value
	 *
	 * @var string
	 */
	const PURGE_DEFAULT = false;

	/**
	 * Schedule gap, in seconds
	 *
	 * @var int
	 */
	const SCHEDULE_GAP = 10;

	/**
	 * Token expiration in seconds
	 *
	 * @var int
	 */
	const TOKEN_EXPIRATION = 60;

	protected $id = 'file_cache';

	/**
	 * Key prefix length.
	 *
	 * @var integer
	 */
	private $key_prefix_length = 2;

	/**
	 * Root cache directory.
	 *
	 * @var string
	 */
	private $cache_dir = null;

	/**
	 * Maximum files
	 *
	 * @var int
	 */
	private $max_files = self::MAX_FILES_DEFAULT;

	/**
	 * Maximum size
	 *
	 * @var int|string
	 */
	private $max_size = self::MAX_SIZE_DEFAULT;

	/**
	 * Minimum free disk space
	 *
	 * @var float
	 */
	private $min_free_disk_space = self::MIN_FREE_DISK_SPACE_DEFAULT;

	/**
	 * GC interval
	 *
	 * @var int
	 */
	private $gc_interval = self::GC_INTERVAL_DEFAULT;

	/**
	 * GC time limit
	 *
	 * @var int
	 */
	private $gc_time_limit = self::GC_TIME_LIMIT_DEFAULT;

	/**
	 * @var Lock
	 */
	private $lock = null;

	/**
	 * @var \WooCommerce_Product_Search_Guardian
	 */
	private $guardian = null;

	/**
	 * @var array
	 */
	private $set_settings = null;

	/**
	 * @var boolean
	 */
	private $purge = self::PURGE_DEFAULT;

	/**
	 * Create a file cache instance.
	 *
	 * @param array $params instance parameters
	 * @param int $params['max_files'] maximum number of cache files
	 * @param int|string $params['max_size'] maximum cache size
	 * @param int|string $params['min_free_disk_space'] minimum free disk space
	 */
	public function __construct( $params = null ) {

		parent::__construct( $params );

		$max_files = isset( $params['max_files'] ) ? intval( $params['max_files'] ) : null;
		$max_size = isset( $params['max_size'] ) ? $params['max_size'] : null;
		$min_free_disk_space = isset( $params['min_free_disk_space'] ) ? $params['min_free_disk_space'] : null;
		$gc_interval = isset( $params['gc_interval'] ) ? intval( $params['gc_interval'] ) : null;
		$gc_time_limit = isset( $params['gc_time_limit'] ) ? intval( $params['gc_time_limit'] ) : null;
		$purge = isset( $params['purge'] ) ? boolval( $params['purge'] ) : null;

		$this->set_max_files( $max_files );
		$this->set_max_size( $max_size );
		$this->set_min_free_disk_space( $min_free_disk_space );
		$this->set_gc_interval( $gc_interval );
		$this->set_gc_time_limit( $gc_time_limit );
		$this->set_purge( $purge );

		$this->active = $this->get_cache_dir() !== null;

		$this->guardian = new \WooCommerce_Product_Search_Guardian();
		$this->guardian->start();

		$this->control_gc();
	}

	public function scheduled_gc() {

		if ( $this->gc_interval === 0 ) {
			wps_log_verbose( 'The File Cache has discarded a scheduled GC cycle' );
			return;
		}

		wps_log_verbose( 'The File Cache is processing a scheduled GC cycle' );

		$time = time();
		$token = md5( sprintf( '%d%d', rand(), $time ) );
		set_transient( 'wps_scheduled_gc_token', $token, self::TOKEN_EXPIRATION );

		$gc_url = add_query_arg(
			array(
				'cache_id' => 'file_cache',
				'action' => 'gc',
				'token' => $token

			),
			rest_url( 'wps/v1/search/cache/gcs' )
		);

		$posted = wp_remote_request(
			$gc_url,
			array(
				'method' => 'DELETE',
				'blocking' => false
			)
		);

	}

	/**
	 * Control GC cycles.
	 */
	private function control_gc() {

		add_action( 'woocommerce_product_search_file_cache_gc', array( $this, 'scheduled_gc' ), 10, 0 );

		add_action( 'woocommerce_product_search_deactivate', array( $this, 'deactivate' ) );

		if ( $this->gc_interval >= 0 ) {
			$lock = null;
			$cache_dir = $this->get_cache_dir();
			if ( $cache_dir !== null ) {
				$lock_path = $cache_dir . DIRECTORY_SEPARATOR . '.schedulegclock';
				try {

					$lock = new Lock( $lock_path, false );
				} catch ( Lock_Exception $le ) {
				}

				if ( $lock === null || $lock->writer() ) {

					if ( $this->gc_interval > 0 ) {

						$last_scheduled = intval( get_option( 'woocommerce_product_search_file_cache_gc_scheduled', 0 ) );
						if ( ( time() - $last_scheduled ) > self::SCHEDULE_GAP ) {
							update_option( 'woocommerce_product_search_file_cache_gc_scheduled', time(), false );
							$this->schedule_gc();
						}
					} else if ( $this->gc_interval === 0 ) {

						$this->unschedule_gc();
					}
				}
			}
			if ( $lock !== null ) {
				$lock->release();
			}
		}

	}

	/**
	 * Schedule a GC cycle.
	 */
	private function schedule_gc() {
		if ( $this->gc_interval > 0 ) {

			$event = wp_get_scheduled_event( 'woocommerce_product_search_file_cache_gc' );
			if ( $event === false ) {
				$next = time() + $this->gc_interval;
				$scheduled = wp_schedule_single_event( $next, 'woocommerce_product_search_file_cache_gc', array(), true );
				if ( WPS_CACHE_DEBUG ) {
					if ( $scheduled === true ) {
						wps_log_info( sprintf(
							'The File Cache has scheduled the next garbage-collection cycle @ %s',
							date( 'Y-m-d H:i:s', $next )
						) );
					} else {

						$error = null;
						if ( $scheduled instanceof \WP_Error ) {
							$error = $scheduled->get_error_message();
						}
						if ( $error === null ) {
							wps_log_warning( 'The File Cache could not schedule the next garbage-collection at this time.' );
						} else {
							wps_log_warning( sprintf( 'The File Cache could not schedule the next garbage-collection cycle at this time: %s', $error ) );
						}
					}
				}
			}
		}
	}

	/**
	 * Unschedule any GC cycles.
	 */
	private function unschedule_gc() {
		$unscheduled = wp_unschedule_hook( 'woocommerce_product_search_file_cache_gc', true );
		if ( WPS_CACHE_DEBUG ) {
			if ( $unscheduled !== false && is_numeric( $unscheduled ) ) {
				$unscheduled = intval( $unscheduled );
				if ( $unscheduled > 0 ) {
					wps_log_verbose( sprintf( 'The File Cache has removed the scheduled garbage-collection (%d).', $unscheduled ) );
				} else {
					wps_log_verbose( 'The File Cache encountered no scheduled garbage-collection to remove.' );
				}
			} else {
				if ( $unscheduled instanceof \WP_Error ) {
					wps_log_warning( sprintf( 'The File Cache encountered an error while trying to remove scheduled garbage-collection: %s', $unscheduled->get_error_message() ) );
				} else {
					wps_log_warning( 'The File Cache encountered an error while trying to remove scheduled garbage-collection.' );
				}
			}
		}
	}

	/**
	 * Reschedule the GC cycle.
	 */
	public function reschedule_gc() {
		$this->unschedule_gc();
		$this->schedule_gc();
	}

	/**
	 * Clean up the GC schedule.
	 */
	public function deactivate() {

		$this->unschedule_gc();
		delete_option( 'woocommerce_product_search_file_cache_gc_scheduled' );
	}

	/**
	 * Set maximum files.
	 *
	 * @param int $max_files
	 *
	 * @return int property value
	 */
	private function set_max_files( $max_files ) {
		if ( $max_files === null ) {
			$max_files = self::MAX_FILES_DEFAULT;
		}
		if ( $max_files !== null ) {

			if ( is_numeric( $max_files ) ) {
				$max_files = max( 0, intval( $max_files ) );
				$this->max_files = $max_files;
			}
		}
		return $this->max_files;
	}

	/**
	 * Set max size.
	 *
	 * @param int|string $max_size
	 *
	 * @return int|string property value
	 */
	private function set_max_size( $max_size ) {
		if ( $max_size === null ) {
			$max_size = self::MAX_SIZE_DEFAULT;
		}
		if ( $max_size !== null ) {

			if ( is_numeric( $max_size ) || is_string( $max_size ) ) {
				$max_size_parsed = self::parse_storage_bytes( $max_size );
				$this->max_size = $max_size_parsed['bytes'];
			}
		}
		return $this->max_size;
	}

	/**
	 * Set minimum free disk space.
	 *
	 * @param int|string $min_free_disk_space
	 *
	 * @return int|string property value
	 */
	private function set_min_free_disk_space( $min_free_disk_space ) {
		if ( $min_free_disk_space === null ) {
			$min_free_disk_space = self::MIN_FREE_DISK_SPACE_DEFAULT;
		}
		if ( $min_free_disk_space !== null ) {

			if ( is_numeric( $min_free_disk_space ) || is_string( $min_free_disk_space ) ) {
				$parsed = self::parse_storage_measure( $min_free_disk_space );
				$this->min_free_disk_space = $parsed['string'];
			}
		}
		return $this->min_free_disk_space;
	}

	/**
	 * Set GC interval.
	 *
	 * @param int $gc_interval
	 *
	 * @return int property value
	 */
	private function set_gc_interval( $gc_interval ) {
		if ( $gc_interval === null ) {
			$gc_interval = self::GC_INTERVAL_DEFAULT;
		}
		if ( $gc_interval !== null ) {
			if ( is_numeric( $gc_interval ) ) {

				$gc_interval = max( -1, intval( $gc_interval ) );
				$this->gc_interval = $gc_interval;
			}
		}
		return $this->gc_interval;
	}

	/**
	 * Set GC time limit.
	 *
	 * @param int $gc_time_limit
	 *
	 * @return int property value
	 */
	private function set_gc_time_limit( $gc_time_limit ) {
		if ( $gc_time_limit === null ) {
			$gc_time_limit = self::GC_TIME_LIMIT_DEFAULT;
		}
		if ( $gc_time_limit !== null ) {
			if ( is_numeric( $gc_time_limit ) ) {
				$gc_time_limit = max( self::GC_TIME_LIMIT_MIN, intval( $gc_time_limit ) );
				$this->gc_time_limit = $gc_time_limit;
			}
		}
		return $this->gc_time_limit;
	}

	/**
	 * Set the purge property.
	 *
	 * @param boolean $purge
	 */
	private function set_purge( $purge ) {
		$this->purge = boolval( $purge );
	}

	/**
	 * Provide a valid key for the suggested key.
	 *
	 * The key is returned as is if it represents a hexadecimal number.
	 * Otherwise the md5 hash of the string provided is returned.
	 *
	 * @param string $key
	 *
	 * @return string|null
	 */
	private function get_key( $key ) {
		if ( is_string( $key ) ) {
			if ( !ctype_xdigit( $key ) ) {
				$key = md5( $key );
			}
		} else {
			$key = null;
		}
		return $key;
	}

	/**
	 * Provide the prefix based on the given key.
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	private function get_key_prefix( $key ) {
		$prefix = '';
		if ( is_string( $key ) && strlen( $key ) > 0 ) {
			$prefix = substr( $key, 0, $this->key_prefix_length );
		}
		return $prefix;
	}

	/**
	 * Provide the trailing name of the path.
	 *
	 * @param string $path
	 *
	 * @return mixed
	 */
	public function basename( $path ) {

		$path = str_replace( "\\", "/", $path );

		if ( function_exists( 'mb_strrpos' ) ) {
			$k = mb_strrpos( $path, "/" );
			if ( $k !== false ) {
				$path = mb_substr( $path, $k + 1 );
			}
		} else {
			$k = strrpos( $path, "/" );
			if ( $k !== false ) {
				$path = substr( $path, $k + 1 );
			}
		}
		return $path;
	}

	/**
	 * Create a directory.
	 *
	 * @param string $directory
	 * @param int $permissions
	 * @param bool $recursive
	 * @param resource|null $context
	 *
	 * @return bool
	 */
	private function mkdir( $directory, $permissions = 0777, $recursive = false, $context = null ) {

		$created = @mkdir( $directory, $permissions, $recursive, $context );
		return $created;
	}

	/**
	 * Provide the root cache directory.
	 *
	 * @return string|null
	 */
	public function get_cache_dir() {
		if ( $this->cache_dir === null ) {
			$this->cache_dir = untrailingslashit( WP_CONTENT_DIR ) . DIRECTORY_SEPARATOR . 'wps-cache';
			if ( is_multisite() ) {
				$blog_id = get_current_blog_id();
				if ( intval( $blog_id ) !== 1 ) {
					$this->cache_dir .= '_' . intval( $blog_id );
				}
			}
			$cache_dir = apply_filters( 'woocommerce_product_search_cache_dir', $this->cache_dir );
			if ( is_string( $cache_dir ) ) {
				$this->cache_dir = $cache_dir;
			} else {
				wps_log_warning( sprintf( 'The woocommerce_product_search_cache_dir filter must provide a string value, not modified.' ) );
			}
			if ( !is_dir( $this->cache_dir ) ) {
				$created = $this->mkdir( $this->cache_dir, 0755, true );
				if ( !$created && !is_dir( $this->cache_dir ) ) {
					wps_log_error( sprintf( 'Could not create the cache directory %s', $this->cache_dir ) );
					$this->cache_dir = null;
				} else {
					if ( WPS_CACHE_DEBUG ) {
						wps_log_verbose( sprintf( 'Created the cache directory %s', $this->cache_dir ) );
					}
				}
			}

			if ( $this->cache_dir !== null && is_dir( $this->cache_dir ) ) {

				$filename = $this->cache_dir . DIRECTORY_SEPARATOR . 'index.php';
				if ( !file_exists( $filename ) ) {
					$bytes = file_put_contents(
						$filename,
						self::PRELUDE . "\n",
						LOCK_EX
					);
					if ( $bytes === false ) {
						wps_log_error( sprintf( 'Failed to create the cache index %s', $filename ) );
					} else {
						if ( WPS_CACHE_DEBUG ) {
							wps_log_verbose( sprintf( 'Created the cache index %s', $filename ) );
						}
					}
				}
			}
		}
		return $this->cache_dir;
	}

	/**
	 * Basic cache functionality test.
	 *
	 * @return array
	 */
	public function test() {
		$result = array( 'success' => true, 'errors' => array() );
		$cache_dir = $this->get_cache_dir();
		if ( $cache_dir !== null ) {
			$file = $cache_dir . DIRECTORY_SEPARATOR . 'test';
			if ( @file_put_contents( $file, '1' ) !== false ) {
				$lock = new Lock( $file );
				if ( $lock->writer() ) {
					$lock->release();
				} else {
					$result['errors'][] = __( 'Locking in the cache directory fails.', 'woocommerce-product-search' );
				}
				if ( !@unlink( $file ) ) {
					$result['errors'][] = __( 'Deleting from the cache directory is not possible.', 'woocommerce-product-search' );
				}
			} else {
				$result['errors'][] = __( 'Writing to the cache directory is not possible.', 'woocommerce-product-search' );
			}
		} else {
			$result['errors'][] = __( 'The cache directory is not available.', 'woocommerce-product-search' );
		}
		if ( count( $result['errors'] ) > 0 ) {
			$result['success'] = false;
		}
		return $result;
	}

	public function get_status() {

		$key = $this->get_key( 'status' );
		$group = 'status';

		$infos = array();
		$status = array(

			'use_master' => true,
			'max_files' => $this->max_files,
			'max_size' => $this->max_size,
			'count' => -1,
			'size' => -1,

			'unit' => '',
			'free_disk_space' => 0,
			'min_free_disk_space' => 0,
			'parsed_min_free_disk_space' => '',
			'free' => 0,
			'total' => 0,

			'infos' => $infos
		);

		$use_master = true;
		if ( $this->max_files === 0 && $this->max_size === 0 ) {
			$use_master = false;
			$status['use_master'] = false;
		}

		$mlock = null;
		$lock = null;
		if ( $use_master ) {
			$master_lock = $this->get_master_lock_path();
			if ( $master_lock !== null ) {
				$lock = new Lock( $master_lock, false );
			}
			if ( $lock !== null ) {
				if ( $lock->writer() ) {
					$master = $this->get_master();
					$status['count'] = $master['count'];
					$status['size'] = $master['size'];
				} else {
					$infos[] = sprintf( __( 'Could not obtain a master write lock on %s', 'woocommerce-product-search' ), esc_html( $master_lock ) );
				}
			} else {
				$infos[] = sprintf( __( 'Could not establish a master lock', 'woocommerce-product-search' ), esc_html( $master_lock ) );
			}
		} else {
			$master_lock = $this->get_master_lock_path();
			if ( $master_lock !== null ) {
				$mlock = new Lock( $master_lock, false );
				if ( !$mlock->reader() ) {
					$infos[] = sprintf( __( 'Could not obtain a master read lock on %s', 'woocommerce-product-search' ), esc_html( $master_lock ) );
				}
			} else {
				$infos[] = sprintf( __( 'Could not establish a master lock', 'woocommerce-product-search' ), esc_html( $master_lock ) );
			}

			$lock_path = $this->get_lock_path( $key, $group );
			if ( $lock_path !== null ) {
				$lock = new Lock( $lock_path, false );
			}
			if ( $lock !== null ) {
				if ( !$lock->writer() ) {
					$infos[] = sprintf( __( 'Could not obtain a path lock on %s', 'woocommerce-product-search' ), esc_html( $lock_path ) );
				}
			} else {
				$infos[] = sprintf( __( 'Could not establish a path lock', 'woocommerce-product-search' ), esc_html( $master_lock ) );
			}
		}

		$filtered_min_free_disk_space = apply_filters( 'woocommerce_product_search_file_cache_min_free_disk_space', $this->min_free_disk_space );
		$parsed_min_free_disk_space = self::parse_storage_measure( $filtered_min_free_disk_space );

		if ( $parsed_min_free_disk_space['value'] > 0.0 ) {
			$status['unit'] = $parsed_min_free_disk_space['unit'];
			$status['parsed_min_free_disk_space'] = $parsed_min_free_disk_space['string'];
			$cache_dir = $this->get_cache_dir();
			switch ( $parsed_min_free_disk_space['unit'] ) {
				case 'percent':
					$min_free_disk_space = floatval( $parsed_min_free_disk_space['value'] );
					$min_free_disk_space = min( 100.0, max( 0.0, $parsed_min_free_disk_space['value'] ) );
					$status['min_free_disk_space'] = $min_free_disk_space;
					if ( $min_free_disk_space > 0.0 ) {
						$free = function_exists( 'disk_free_space' ) ? disk_free_space( $cache_dir ) : null;
						$total = function_exists( 'disk_total_space' ) ? disk_total_space( $cache_dir ) : null;
						$status['free'] = $free;
						$status['total'] = $total;
						if ( $free !== null && $total !== null ) {
							if ( $total > 0 ) {
								$free_disk_space = floatval( $free ) / floatval( $total ) * 100.0;
								$status['free_disk_space'] = $free_disk_space;
							}
						} else {
							$status['free_disk_space'] = null;
						}
					}
					break;
				default:
					$min_free_disk_space = max( 0, $parsed_min_free_disk_space['value'] );
					$status['min_free_disk_space'] = $min_free_disk_space;
					if ( $min_free_disk_space > 0 ) {
						$free = function_exists( 'disk_free_space' ) ? disk_free_space( $cache_dir ) : null;
						$total = function_exists( 'disk_total_space' ) ? disk_total_space( $cache_dir ) : null;
						$status['free'] = $free;
						$status['total'] = $total;
						if ( $free !== null && $total !== null ) {
							if ( $total > 0 ) {
								$free_disk_space = floatval( $free ) / floatval( $total ) * 100.0;
								$status['free_disk_space'] = $free_disk_space;
							}
						} else {
							$status['free_disk_space'] = null;
						}
					}
			}
		}

		if ( !function_exists( 'disk_free_space' ) ) {
			$infos[] = sprintf( __( 'The PHP function %s is not available', 'woocommerce-product-search' ), esc_html( 'disk_free_space()' ) );
		}

		if ( !function_exists( 'disk_total_space' ) ) {
			$infos[] = sprintf( __( 'The PHP function %s is not available', 'woocommerce-product-search' ), esc_html( 'disk_total_space()' ) );
		}

		$status['infos'] = $infos;

		return $status;
	}

	/**
	 * Provide the full path of the master.
	 *
	 * @return string|null
	 */
	private function get_master_path() {
		$path = null;
		$cache_dir = $this->get_cache_dir();
		if ( $cache_dir !== null ) {
			$path = $cache_dir . DIRECTORY_SEPARATOR . 'master.php';
		}
		return $path;
	}

	/**
	 * Provide the base master set.
	 *
	 * @return array
	 */
	private function get_master_set() {
		return array(
			'count' => 0,
			'size'  => 0
		);
	}

	/**
	 * Get master data.
	 *
	 * @return array
	 */
	private function get_master() {
		$master = $this->get_master_set();
		$master_path = $this->get_master_path();
		if ( $master_path !== null ) {
			$master_content = @file_get_contents( $master_path );
			if ( is_string( $master_content ) && strlen( $master_content ) > 0 ) {
				$master_content = trim( str_replace( self::PRELUDE, '', $master_content ) );
				if ( strlen( $master_content ) > 0 ) {
					$master_data = json_decode( $master_content, true );
					if ( is_array( $master_data ) ) {
						$master['count'] = isset( $master_data['count'] ) && is_numeric( $master_data['count'] ) ? max( 0, intval( $master_data['count'] ) ) : 0;
						$master['size'] = isset( $master_data['size'] ) && is_numeric( $master_data['size'] ) ? max( 0, intval( $master_data['size'] ) ) : 0;
					}
				}
			}
		}
		return $master;
	}

	/**
	 * Set master data.
	 *
	 * @param array $master
	 *
	 * @return int
	 */
	private function set_master( $master ) {
		$bytes = 0;
		$store_master = $this->get_master_set();
		if ( is_array( $master ) ) {
			$store_master['count'] = isset( $master['count'] ) && is_numeric( $master['count'] ) ? max( 0, intval( $master['count'] ) ) : 0;
			$store_master['size'] = isset( $master['size'] ) && is_numeric( $master['size'] ) ? max( 0, intval( $master['size'] ) ) : 0;
		}

		$master_path = $this->get_master_path();
		if ( $master_path !== null ) {
			$master_content = self::PRELUDE . "\n";
			$master_content .= json_encode( $store_master );
			$bytes = @file_put_contents( $master_path, $master_content );
			if ( $bytes === false ) {
				$error = error_get_last();
				$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
				wps_log_error( sprintf( 'Failed to write the master file: %s [%s]', $master_path, $msg ) );
				$this->nuke();
			}
		}
		return $bytes;
	}

	/**
	 * Whether the cache directory exists.
	 *
	 * @return boolean
	 */
	public function cache_dir_exists() {
		$exists = false;
		if ( $this->cache_dir !== null ) {
			$exists = is_dir( $this->cache_dir );
		}
		return $exists;
	}

	/**
	 * Void file and size limits, remove master.
	 */
	private function nuke() {

		$settings = Cache_Settings::get_instance();
		$cache_settings = $settings->get();
		$cache_settings['file_cache'][self::MAX_FILES] = 0;
		$cache_settings['file_cache'][self::MAX_SIZE] = 0;
		$settings->set( $cache_settings );
		$settings->save();

		$master_path = $this->get_master_path();
		if ( $master_path !== null ) {
			@unlink( $master_path );
		}

		if ( WPS_CACHE_DEBUG ) {
			wps_log_warning( 'Voided the cache file and size limits, minimum storage space requirements apply' );
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

		@set_time_limit( 0 );
		$flushed = false;
		if ( $group === null || $group === '' ) {
			$flushed = $this->flush_dir();
		} else {
			$all_groups = $this->get_all_groups( $group );
			foreach ( $all_groups as $the_group ) {

				$flushed = $this->flush_dir( md5( $the_group ) );
			}
		}
		return $flushed;
	}

	/**
	 * Flush the cache, apply and save the specified settings.
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	public function settings_flush( $settings = null ) {
		$this->set_settings = $settings;
		return $this->flush();
	}

	/**
	 * Flush the cache directory.
	 *
	 * @param string $directory to flush a particular cache directory only
	 *
	 * @return boolean
	 */
	private function flush_dir( $directory = null, &$lock = null, &$master = null ) {

		$limits_exceeded = false;

		$release = true;
		if ( $lock === null ) {
			$master_lock = $this->get_master_lock_path();
			if ( $master_lock !== null ) {
				$lock = new Lock( $master_lock );
			}
			if ( $lock !== null ) {
				if ( !$lock->writer() ) {

					return false;
				}
			} else {

				return false;
			}
		} else {
			$release = false;
		}

		$set_settings = null;
		if ( $this->set_settings !== null && is_array( $this->set_settings ) ) {
			$set_settings = $this->set_settings;
			$this->set_settings = null;
		}

		$use_master = true;
		if ( $this->max_files === 0 && $this->max_size === 0 ) {
			$use_master = false;
		}
		if ( $master === null && $use_master ) {
			$master = $this->get_master();
		}

		$nuke = false;

		$cache_dir = $this->get_cache_dir();
		if ( $directory === null ) {
			$directory = $cache_dir;
		} else {

			if ( strpos( $directory, $cache_dir ) !== 0 ) {
				$directory = $this->get_cache_dir() . DIRECTORY_SEPARATOR . $directory;
			}
		}
		if ( is_dir( $directory ) ) {

			$subdirs = glob( $directory . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR | GLOB_NOSORT );
			foreach ( $subdirs as $subdir ) {

				$subdir = untrailingslashit( $subdir );

				if ( strpos( $subdir, $directory . DIRECTORY_SEPARATOR ) === false ) {
					$subdir = $directory . DIRECTORY_SEPARATOR . $subdir;
				}
				if ( is_dir( $subdir ) ) {

					if ( $subdir !== '.' && $subdir !== '..' ) {
						$this->flush_dir( $subdir, $lock, $master );
					}
				}
				if ( !$this->guardian->is_ok() ) {
					$limits_exceeded = true;
					break;
				}
			}

			if ( !$limits_exceeded ) {

				if ( WPS_CACHE_DEBUG ) {
					if ( $directory === $cache_dir ) {
						wps_log_info( sprintf( 'Flushing the cache directory %s', $directory ) );
					} else {
						wps_log_verbose( sprintf( 'Flushing the cache directory %s', $directory ) );
					}
				}

				$path = $directory . DIRECTORY_SEPARATOR . 'index.php';
				if ( file_exists( $path ) ) {

					$entries = array();
					$index_data = @file_get_contents( $path );
					if ( $index_data !== false ) {
						if ( strpos( $index_data, self::PRELUDE ) === 0 ) {
							$index_data = trim( substr( $index_data, strlen( self::PRELUDE ) ) );
							if ( strlen( $index_data ) > 0 ) {
								$entries = json_decode( $index_data, true );
								if ( !is_array( $entries ) ) {
									$entries = array();
									wps_log_error( sprintf( 'Invalid index file detected, corrupted: %s', $path ) );
									$nuke = true;
								}
							}
						} else {
							wps_log_error( sprintf( 'Invalid index file detected, missing header: %s', $path ) );
							$nuke = true;
						}
					} else {
						$error = error_get_last();
						$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
						wps_log_error( sprintf( 'Failed to read index file: %s [%s]', $path, $msg ) );
						$nuke = true;
					}

					$files = 0;
					$size = 0;
					foreach ( $entries as $key => $entry ) {
						if ( isset( $entry['file'] ) ) {
							$files++;
							if ( isset( $entry['size'] ) && is_numeric( $entry['size'] ) ) {
								$size += max( 0, intval( $entry['size'] ) );
							}
							$file_path = $directory . DIRECTORY_SEPARATOR . $entry['file'];
							if ( file_exists( $file_path ) ) {
								$deleted = @unlink( $file_path );
								if ( $deleted ) {
									unset( $entries[$key] );
									if ( WPS_CACHE_DEBUG ) {
										wps_log_verbose( sprintf( 'Deleted cache file %s', $file_path ) );
									}
								} else {
									$error = error_get_last();
									$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
									wps_log_error( sprintf( 'Failed to delete cache file %s referenced in index %s [%s]', $file_path, $path, $msg ) );
									$nuke = true;
								}
							} else {
								if ( WPS_CACHE_DEBUG ) {
									wps_log_warning( 'Cache file %s referenced in index %s does not exist', $file_path, $path );
								}
							}
						}
						if ( !$this->guardian->is_ok() ) {
							$limits_exceeded = true;
							break;
						}
					}

					if ( $files > 0 || $size > 0 || $nuke ) {
						$bytes = 0;
						if ( count( $entries ) > 0 && !$nuke ) {
							$bytes = @file_put_contents( $path, self::PRELUDE . "\n" . json_encode( $entries ) );
						} else {

							$bytes = @file_put_contents( $path, self::PRELUDE . "\n" );
						}
						if ( $bytes !== false ) {
							if ( WPS_CACHE_DEBUG ) {
								if ( count( $entries ) > 0 ) {
									wps_log_verbose( sprintf( 'Flushed the cache index %s partially', $path ) );
								} else {
									wps_log_verbose( sprintf( 'Flushed the cache index %s', $path ) );
								}
							}
						} else {
							$error = error_get_last();
							$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
							wps_log_error( sprintf( 'Failed to flush the cache index %s [%s]', $path, $msg ) );
							$nuke = true;
						}
					}

					if ( $nuke ) {
						$this->nuke();
					}

					if ( $use_master && !$nuke ) {
						if ( $files > 0 || $size > 0 ) {
							$master['count'] -= $files;
							$master['count'] = max( 0, $master['count'] );
							$master['size'] -= $size;
							$master['size'] = max( 0, $master['size'] );
							$this->set_master( $master );

						}
					}
				}

				if ( !$limits_exceeded ) {
					$nuked = $nuke;

					$files = glob( $directory . DIRECTORY_SEPARATOR . '*.php', GLOB_NOSORT );
					foreach ( $files as $file ) {

						$file = untrailingslashit( $file );

						if ( strpos( $file, $directory . DIRECTORY_SEPARATOR ) === false ) {
							$file = $directory . DIRECTORY_SEPARATOR . $file;
						}
						if ( !is_dir( $file ) ) {
							$basename = basename( $file );
							if (
								$basename !== '.lock' &&
								$basename !== '.mlock' &&
								$basename !== 'index.php' &&
								$basename !== 'master.php'
							) {

								$nuke = true;
								$deleted = @unlink( $file );
								if ( $deleted ) {
									if ( WPS_CACHE_DEBUG ) {
										wps_log_warning( sprintf( 'Deleted unindexed file %s', $file ) );
									}
								} else {
									$error = error_get_last();
									$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
									wps_log_error( sprintf( 'Failed to delete unindexed file %s [%s]', $file, $msg ) );
								}
							}
						}
						if ( !$this->guardian->is_ok() ) {
							$limits_exceeded = true;
							break;
						}
					}
					if ( !$nuked && $nuke ) {
						$this->nuke();
					}
				}

				if ( $this->purge && !$limits_exceeded ) {

					$count = 0;

					$all_files = glob( $directory . DIRECTORY_SEPARATOR . '{.*,*}', GLOB_NOSORT | GLOB_BRACE );
					if ( is_array( $all_files ) ) {
						foreach ( $all_files as $file ) {
							$basename = basename( $file );
							switch ( $basename ) {
								case '.':
								case '..':
									break;
								default:
									$count++;
							}
						}
					}

					if ( $count <= 2 ) {
						$files = glob( $directory . DIRECTORY_SEPARATOR . '{*.php,.lock}', GLOB_NOSORT | GLOB_BRACE );
						if ( is_array( $files ) && count( $files ) <= 2 ) {
							foreach ( $files as $file ) {
								if ( !is_dir( $file ) ) {
									$basename = basename( $file );
									if (
										$basename === 'index.php' ||
										$basename === '.lock'
									) {
										if ( @unlink( $file ) ) {
											wps_log_verbose( sprintf( 'Removed the cache file %s', $file ) );
										} else {
											$error = error_get_last();
											$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
											wps_log_error( sprintf( 'Failed to remove the cache file %s [%s]', $file, $msg ) );
										}
									}
								}

							}
							if ( @rmdir( $directory ) ) {
								wps_log_verbose( sprintf( 'Removed the cache directory %s', $directory ) );
							} else {
								$error = error_get_last();
								$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
								wps_log_error( sprintf( 'Failed to remove the cache directory %s [%s]', $directory, $msg ) );
							}
						}
					} else {
						wps_log_warning( sprintf( 'Could not remove the cache directory because it contains remnant files: %s', $directory ) );
					}
				}
			}

		}

		if ( $set_settings !== null ) {
			$settings = Cache_Settings::get_instance();
			$cache_settings = $settings->get();
			foreach ( $set_settings as $key => $value ) {
				$cache_settings['file_cache'][$key] = $value;
			}
			$settings->set( $cache_settings );
			$settings->save();

			if ( array_key_exists( 'max_files', $cache_settings['file_cache'] ) ) {
				$this->set_max_files( $cache_settings['file_cache']['max_files'] );
			}
			if ( array_key_exists( 'max_size', $cache_settings['file_cache'] ) ) {
				$this->set_max_size( $cache_settings['file_cache']['max_size'] );
			}
			if ( array_key_exists( 'min_free_disk_space', $cache_settings['file_cache'] ) ) {
				$this->set_min_free_disk_space( $cache_settings['file_cache']['min_free_disk_space'] );
			}
			if ( array_key_exists( 'gc_interval', $cache_settings['file_cache'] ) ) {
				$this->set_gc_interval( $cache_settings['file_cache']['gc_interval'] );
			}
			if ( array_key_exists( 'gc_time_limit', $cache_settings['file_cache'] ) ) {
				$this->set_gc_time_limit( $cache_settings['file_cache']['gc_time_limit'] );
			}

			if ( $this->max_files === 0 && $this->max_size === 0 ) {
				$master_path = $this->get_master_path();
				if ( $master_path !== null ) {
					@unlink( $master_path );
				}
			}
		}

		if ( $release ) {
			$lock->release();
		}

		return true;
	}

	/**
	 * GC the cache.
	 *
	 * @param string $group to do GC for a particular group only
	 */
	public function gc( $group = null ) {

		$result = false;

		@set_time_limit( $this->gc_time_limit );

		if ( $group === null || $group === '' ) {

			$result = $this->gc_dir_frac();
		} else {

			$all_groups = $this->get_all_groups( $group );
			foreach ( $all_groups as $the_group ) {

				$result = $this->gc_dir_frac( md5( $the_group ) );
			}
		}
		return $result;
	}

	/**
	 * GC the cache directory.
	 *
	 * @param string $directory to GC a specific cache directory hierarchy
	 *
	 * @return boolean
	 */
	private function gc_dir( $directory = null, &$lock = null, &$master = null ) {

		$limits_exceeded = false;

		$release = true;
		if ( $lock === null ) {
			$master_lock = $this->get_master_lock_path();
			if ( $master_lock !== null ) {
				$lock = new Lock( $master_lock );
			}
			if ( $lock !== null ) {
				if ( !$lock->writer() ) {

					return false;
				}
			} else {

				return false;
			}
		} else {
			$release = false;
		}

		$use_master = true;
		if ( $this->max_files === 0 && $this->max_size === 0 ) {
			$use_master = false;
		}
		if ( $master === null && $use_master ) {
			$master = $this->get_master();
		}

		$nuke = false;

		$cache_dir = $this->get_cache_dir();
		if ( $directory === null ) {
			$directory = $cache_dir;
		} else {

			if ( strpos( $directory, $cache_dir ) !== 0 ) {
				$directory = $this->get_cache_dir() . DIRECTORY_SEPARATOR . $directory;
			}
		}
		if ( is_dir( $directory ) ) {

			if ( WPS_CACHE_DEBUG ) {
				if ( $directory === $cache_dir ) {
					wps_log_info( sprintf( 'GC on the cache directory %s', $directory ) );
				} else {
					wps_log_verbose( sprintf( 'GC on the cache directory %s', $directory ) );
				}
			}

			$entries = array();
			$path = $directory . DIRECTORY_SEPARATOR . 'index.php';
			if ( file_exists( $path ) ) {

				$index_data = @file_get_contents( $path );
				if ( $index_data !== false ) {
					if ( strpos( $index_data, self::PRELUDE ) === 0 ) {
						$index_data = trim( substr( $index_data, strlen( self::PRELUDE ) ) );
						if ( strlen( $index_data ) > 0 ) {
							$entries = json_decode( $index_data, true );
							if ( !is_array( $entries ) ) {
								$nuke = true;
								$entries = array();
								wps_log_error( sprintf( 'Invalid index file detected, corrupted: %s', $path ) );
							}
						}
					} else {
						$nuke = true;
						wps_log_error( sprintf( 'Invalid index file detected, missing header: %s', $path ) );
					}
				} else {
					$nuke = true;
					$error = error_get_last();
					$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
					wps_log_error( sprintf( 'Failed to read index file: %s [%s]', $path, $msg ) );
				}

				$size = 0;
				$files = 0;
				foreach ( $entries as $key => $entry ) {

					if ( !$this->guardian->is_ok() ) {
						$limits_exceeded = true;
						break;
					}

					$expired = false;

					if ( !empty( $entry['expires'] ) ) {
						if ( is_numeric( $entry['expires'] ) ) {
							$expires = intval( $entry['expires'] );
							if ( $expires > 0 ) {
								if ( $expires < time() ) {
									$expired = true;
								}
							}
						}
					}

					if ( !$expired ) {
						if ( isset( $entry['created'] ) ) {
							if ( is_numeric( $entry['created'] ) ) {
								$created = intval( $entry['created'] );
								$expired = Cache_Control::has_timestamp_expired( $created );
							}
						}
					}

					if ( $expired ) {

						$files++;
						if ( isset( $entry['size'] ) && is_numeric( $entry['size'] ) ) {
							$size += max( 0, intval( $entry['size'] ) );
						}

						unset( $entries[$key] );

						if ( isset( $entry['file'] ) ) {
							$file_path = $directory . DIRECTORY_SEPARATOR . $entry['file'];
							if ( file_exists( $file_path ) ) {
								$deleted = @unlink( $file_path );
								if ( $deleted ) {
									if ( WPS_CACHE_DEBUG ) {
										wps_log_verbose( sprintf( 'Deleted cache file %s', $file_path ) );
									}
								} else {
									$nuke = true;
									$error = error_get_last();
									$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
									wps_log_error( sprintf( 'Failed to delete cache file %s referenced in index %s [%s]', $file_path, $path, $msg ) );
								}
							} else {
								if ( WPS_CACHE_DEBUG ) {
									wps_log_warning( 'Cache file %s referenced in index %s does not exist', $file_path, $path );
								}
							}
						}
					}
				}

				if ( $files > 0 || $size > 0 ) {
					$bytes = @file_put_contents( $path, self::PRELUDE . "\n" . json_encode( $entries ) );
					if ( $bytes !== false ) {
						if ( WPS_CACHE_DEBUG ) {
							wps_log_verbose( sprintf( 'GC has rewritten the cache index %s', $path ) );
						}
					} else {
						$nuke = true;
						$error = error_get_last();
						$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
						wps_log_error( sprintf( 'GC failed to rewrite the cache index %s [%s]', $path, $msg ) );
					}
				}

				if ( $nuke ) {
					$this->nuke();
				}

				if ( $use_master && !$nuke ) {
					if ( ( $files > 0 || $size > 0 ) ) {
						$master['count'] -= $files;
						$master['count'] = max( 0, $master['count'] );
						$master['size'] -= $size;
						$master['size'] = max( 0, $master['size'] );
						$this->set_master( $master );

					}
				}
			}

			if ( !$limits_exceeded ) {

				$nuked = $nuke;
				$files = glob( $directory . DIRECTORY_SEPARATOR . '*.php', GLOB_NOSORT );
				foreach ( $files as $file ) {

					$file = untrailingslashit( $file );

					if ( strpos( $file, $directory . DIRECTORY_SEPARATOR ) === false ) {
						$file = $directory . DIRECTORY_SEPARATOR . $file;
					}
					if ( !is_dir( $file ) ) {
						$basename = basename( $file );
						if (
							$basename !== '.lock' &&
							$basename !== '.mlock' &&
							$basename !== 'index.php' &&
							$basename !== 'master.php'
						) {
							$file_key = $this->get_key_from_path( $file );
							if ( $file_key !== null ) {
								if ( !key_exists( $file_key, $entries ) ) {

									$nuke = true;
									$deleted = @unlink( $file );
									if ( $deleted ) {
										if ( WPS_CACHE_DEBUG ) {
											wps_log_warning( sprintf( 'Deleted unindexed file %s', $file ) );
										}
									} else {
										$error = error_get_last();
										$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
										wps_log_error( sprintf( 'Failed to delete unindexed file %s [%s]', $file, $msg ) );
									}
								}
							}
						}
					}
					if ( !$this->guardian->is_ok() ) {
						$limits_exceeded = true;
						break;
					}
				}
				if ( !$nuked && $nuke ) {
					$this->nuke();
				}
			}

			if ( !$limits_exceeded ) {
				$subdirs = glob( $directory . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR | GLOB_NOSORT );

				while ( count( $subdirs ) > 0 ) {

					if ( !$this->guardian->is_ok() ) {
						$limits_exceeded = true;
						break;
					}

					$k = rand( 0, count( $subdirs ) - 1 );
					$slice = array_splice( $subdirs, $k, 1 );
					if ( count( $slice ) > 0 ) {
						$subdir = array_shift( $slice );

						$subdir = untrailingslashit( $subdir );

						if ( strpos( $subdir, $directory . DIRECTORY_SEPARATOR ) === false ) {
							$subdir = $directory . DIRECTORY_SEPARATOR . $subdir;
						}
						if ( is_dir( $subdir ) ) {

							if ( $subdir !== '.' && $subdir !== '..' ) {
								$this->gc_dir( $subdir, $lock, $master );
							}
						}
					} else {
						break;
					}
				}
			}

		}

		if ( $release ) {
			$lock->release();
		}

		return true;
	}

	/**
	 * GC the cache directory.
	 *
	 * @param string $directory to GC a specific cache directory hierarchy
	 *
	 * @param boolean
	 */
	private function gc_dir_frac( $directory = null ) {

		$limits_exceeded = false;

		$master_lock = $this->get_master_lock_path();
		if ( $master_lock !== null ) {
			$lock = new Lock( $master_lock );
		}
		if ( $lock !== null ) {
			if ( !$lock->writer() ) {

				return false;
			}
		} else {

			return false;
		}

		$use_master = true;
		if ( $this->max_files === 0 && $this->max_size === 0 ) {
			$use_master = false;
		}
		if ( $use_master ) {
			$master = $this->get_master();
		}

		$nuke = false;

		$cache_dir = $this->get_cache_dir();
		if ( $directory === null ) {
			$directory = $cache_dir;
		} else {

			if ( strpos( $directory, $cache_dir ) !== 0 ) {
				$directory = $this->get_cache_dir() . DIRECTORY_SEPARATOR . $directory;
			}
		}
		if ( is_dir( $directory ) ) {

			if ( WPS_CACHE_DEBUG ) {
				if ( $directory === $cache_dir ) {
					wps_log_info( sprintf( 'GC on the cache directory %s', $directory ) );
				} else {
					wps_log_verbose( sprintf( 'GC on the cache directory %s', $directory ) );
				}
			}

			$entries = array();
			$path = $directory . DIRECTORY_SEPARATOR . 'index.php';
			if ( file_exists( $path ) ) {

				$index_data = @file_get_contents( $path );
				if ( $index_data !== false ) {
					if ( strpos( $index_data, self::PRELUDE ) === 0 ) {
						$index_data = trim( substr( $index_data, strlen( self::PRELUDE ) ) );
						if ( strlen( $index_data ) > 0 ) {
							$entries = json_decode( $index_data, true );
							if ( !is_array( $entries ) ) {
								$nuke = true;
								$entries = array();
								wps_log_error( sprintf( 'Invalid index file detected, corrupted: %s', $path ) );
							}
						}
					} else {
						$nuke = true;
						wps_log_error( sprintf( 'Invalid index file detected, missing header: %s', $path ) );
					}
				} else {
					$nuke = true;
					$error = error_get_last();
					$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
					wps_log_error( sprintf( 'Failed to read index file: %s [%s]', $path, $msg ) );
				}

				$size = 0;
				$files = 0;
				foreach ( $entries as $key => $entry ) {

					if ( !$this->guardian->is_ok() ) {
						$limits_exceeded = true;
						break;
					}

					$expired = false;

					if ( !empty( $entry['expires'] ) ) {
						if ( is_numeric( $entry['expires'] ) ) {
							$expires = intval( $entry['expires'] );
							if ( $expires > 0 ) {
								if ( $expires <= time() ) {
									$expired = true;
									if ( WPS_CACHE_DEBUG ) {
										wps_log_verbose( sprintf( 'Cache entry %s expired, cache file %s', $key, isset( $entry['file'] ) ? $entry['file'] : '?' ) );
									}
								}
							}
						}
					}

					if ( !$expired ) {
						if ( isset( $entry['created'] ) ) {
							if ( is_numeric( $entry['created'] ) ) {
								$created = intval( $entry['created'] );
								$expired = Cache_Control::has_timestamp_expired( $created );
								if ( $expired ) {
									if ( WPS_CACHE_DEBUG ) {
										wps_log_verbose( sprintf( 'Cache entry creation %s expired, cache file %s', $key, isset( $entry['file'] ) ? $entry['file'] : '?' ) );
									}
								}
							}
						}
					}

					if ( $expired ) {

						$files++;
						if ( isset( $entry['size'] ) && is_numeric( $entry['size'] ) ) {
							$size += max( 0, intval( $entry['size'] ) );
						}

						unset( $entries[$key] );

						if ( isset( $entry['file'] ) ) {
							$file_path = $directory . DIRECTORY_SEPARATOR . $entry['file'];
							if ( file_exists( $file_path ) ) {
								$deleted = @unlink( $file_path );
								if ( $deleted ) {
									if ( WPS_CACHE_DEBUG ) {
										wps_log_verbose( sprintf( 'Deleted cache file %s', $file_path ) );
									}
								} else {
									$nuke = true;
									$error = error_get_last();
									$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
									wps_log_error( sprintf( 'Failed to delete cache file %s referenced in index %s [%s]', $file_path, $path, $msg ) );
								}
							} else {
								if ( WPS_CACHE_DEBUG ) {
									wps_log_warning( 'Cache file %s referenced in index %s does not exist', $file_path, $path );
								}
							}
						}
					}
				}

				if ( $files > 0 || $size > 0 ) {
					$bytes = @file_put_contents( $path, self::PRELUDE . "\n" . json_encode( $entries ) );
					if ( $bytes !== false ) {
						if ( WPS_CACHE_DEBUG ) {
							wps_log_verbose( sprintf( 'GC has rewritten the cache index %s', $path ) );
						}
					} else {
						$nuke = true;
						$error = error_get_last();
						$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
						wps_log_error( sprintf( 'GC failed to rewrite the cache index %s [%s]', $path, $msg ) );
					}
				}

				if ( $nuke ) {
					$this->nuke();
				}

				if ( $use_master && !$nuke ) {
					if ( $files > 0 || $size > 0 ) {
						$master['count'] -= $files;
						$master['count'] = max( 0, $master['count'] );
						$master['size'] -= $size;
						$master['size'] = max( 0, $master['size'] );
						$this->set_master( $master );

					}
				}
			}

			if ( !$limits_exceeded ) {

				$nuked = $nuke;
				$files = glob( $directory . DIRECTORY_SEPARATOR . '*.php', GLOB_NOSORT );
				foreach ( $files as $file ) {

					$file = untrailingslashit( $file );

					if ( strpos( $file, $directory . DIRECTORY_SEPARATOR ) === false ) {
						$file = $directory . DIRECTORY_SEPARATOR . $file;
					}
					if ( !is_dir( $file ) ) {
						$basename = basename( $file );
						if (
							$basename !== '.lock' &&
							$basename !== '.mlock' &&
							$basename !== 'index.php' &&
							$basename !== 'master.php'
						) {
							$file_key = $this->get_key_from_path( $file );
							if ( $file_key !== null ) {
								if ( !key_exists( $file_key, $entries ) ) {

									$nuke = true;
									$deleted = @unlink( $file );
									if ( $deleted ) {
										if ( WPS_CACHE_DEBUG ) {
											wps_log_warning( sprintf( 'Deleted unindexed file %s', $file ) );
										}
									} else {
										$error = error_get_last();
										$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
										wps_log_error( sprintf( 'Failed to delete unindexed file %s [%s]', $file, $msg ) );
									}
								}
							}
						}
					}
					if ( !$this->guardian->is_ok() ) {
						$limits_exceeded = true;
						break;
					}
				}
				if ( !$nuked && $nuke ) {
					$this->nuke();
				}
			}
		}

		$lock->release();

		if ( !$limits_exceeded ) {
			if ( is_dir( $directory ) ) {

				$subdirs = glob( $directory . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR | GLOB_NOSORT );

				while ( count( $subdirs ) > 0 ) {

					if ( !$this->guardian->is_ok() ) {
						break;
					}

					$k = rand( 0, count( $subdirs ) - 1 );
					$slice = array_splice( $subdirs, $k, 1 );
					if ( count( $slice ) > 0 ) {
						$subdir = array_shift( $slice );

						$subdir = untrailingslashit( $subdir );

						if ( strpos( $subdir, $directory . DIRECTORY_SEPARATOR ) === false ) {
							$subdir = $directory . DIRECTORY_SEPARATOR . $subdir;
						}
						if ( is_dir( $subdir ) ) {

							if ( $subdir !== '.' && $subdir !== '..' ) {
								$this->gc_dir_frac( $subdir );
							}
						}
					} else {
						break;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Provide the file path for the key and group.
	 *
	 * @param string $key
	 * @param string $group
	 *
	 * @return string|null
	 */
	private function get_file_path( $key, $group = '' ) {
		$path = null;
		if ( is_string( $key ) && strlen( $key ) > 0 ) {
			$cache_dir = $this->get_cache_dir();
			if ( $cache_dir !== null ) {

				$key = preg_replace( '/[^a-zA-Z0-9_-]/', '-', $key );

				$bits = array( $cache_dir );

				if ( strlen( $group ) > 0 ) {
					$group = $this->get_group_cache_directory( $group );
					$subdir = $cache_dir . DIRECTORY_SEPARATOR . $group;
					if ( !is_dir( $subdir ) ) {
						$created = $this->mkdir( $subdir, 0755, true );
						if ( !$created && !is_dir( $subdir ) ) {
							wps_log_error( sprintf( 'Could not create the cache directory %s', $subdir ) );
							return null;
						} else {
							if ( WPS_CACHE_DEBUG ) {
								wps_log_verbose( sprintf( 'Created the cache directory %s', $subdir ) );
							}
						}
					}
					$bits[] = $group;

					$prefix = $this->get_key_prefix( $key );
					if ( strlen( $prefix ) > 0 ) {
						$subsubdir = $cache_dir . DIRECTORY_SEPARATOR . $group . DIRECTORY_SEPARATOR . $prefix;
						if ( !is_dir( $subsubdir ) ) {
							$created = $this->mkdir( $subsubdir, 0755, true );
							if ( !$created && !is_dir( $subsubdir ) ) {
								wps_log_error( sprintf( 'Could not create the cache directory %s', $subsubdir ) );
								return null;
							} else {
								if ( WPS_CACHE_DEBUG ) {
									wps_log_verbose( sprintf( 'Created the cache directory %s', $subsubdir ) );
								}
							}
						}
						$bits[] = $prefix;
					}
				}

				$bits[] = $key . '.php';
				$path = implode( DIRECTORY_SEPARATOR, $bits );
			}
		}
		return $path;
	}

	/**
	 * Provide the key part for the given path to a cache file.
	 *
	 * @param string $path
	 *
	 * @return string|null
	 */
	private function get_key_from_path( $path ) {
		$key = null;
		$basename = basename( $path );
		$ext_pos = strrpos( $basename, '.php' );
		if ( $ext_pos !== false ) {
			$key = substr( $basename, 0, $ext_pos );
			if ( strlen( $key ) === 0 ) {
				$key = null;
			}
		}
		return $key;
	}

	/**
	 * Provide the name of the cache directory.
	 *
	 * @param string $group
	 *
	 * @return string
	 */
	private function get_group_cache_directory( $group = '' ) {

		return md5( $this->get_group( $group ) );
	}

	/**
	 * Provide the path to the master lock file.
	 *
	 * @return string|null
	 */
	private function get_master_lock_path() {

		$path = null;
		$cache_dir = $this->get_cache_dir();
		if ( $cache_dir !== null ) {
			$path = $cache_dir . DIRECTORY_SEPARATOR . '.mlock';
		}
		return $path;
	}

	/**
	 * Provide the path to the lock file.
	 *
	 * @param string $key
	 * @param string $group
	 *
	 * @return string|null
	 */
	private function get_lock_path( $key, $group = '' ) {
		$lock_path = null;
		$index_path = $this->get_index_path( $key, $group );
		if ( $index_path !== null ) {
			$directory = dirname( $index_path );
			if ( is_dir( $directory ) ) {
				$lock_path = $directory . DIRECTORY_SEPARATOR . '.lock';
			}
		}
		return $lock_path;
	}

	/**
	 * Provide the index path for the key and group.
	 *
	 * Must only be used in locked context.
	 *
	 * @param string $key
	 * @param string $group
	 *
	 * @return string|null
	 */
	private function get_index_path( $key, $group = '' ) {

		$path = null;
		if ( is_string( $key ) && strlen( $key ) > 0 ) {
			$cache_dir = $this->get_cache_dir();
			if ( $cache_dir !== null ) {

				$bits = array( $cache_dir );

				if ( strlen( $group ) > 0 ) {
					$group = $this->get_group_cache_directory( $group );
					$subdir = $cache_dir . DIRECTORY_SEPARATOR . $group;
					if ( !is_dir( $subdir ) ) {
						$created = $this->mkdir( $subdir, 0755, true );
						if ( !$created && !is_dir( $subdir ) ) {
							$error = error_get_last();
							$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
							wps_log_error( sprintf( 'Could not create the cache directory %s [%s]', $subdir, $msg ) );
							return null;
						} else {
							if ( WPS_CACHE_DEBUG ) {
								wps_log_verbose( sprintf( 'Created the cache directory %s', $subdir ) );
							}
						}
					}
					$bits[] = $group;

					$group_bits = $bits;
					$group_bits[] = 'index.php';
					$path = implode( DIRECTORY_SEPARATOR, $group_bits );

					if ( !file_exists( $path ) ) {
						if ( @file_put_contents( $path, self::PRELUDE . "\n" ) === false ) {
							$error = error_get_last();
							$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
							wps_log_error( sprintf( 'Could not create the index %s [%s]', $path, $msg ) );
							return null;
						} else {
							if ( WPS_CACHE_DEBUG ) {
								wps_log_verbose( sprintf( 'Wrote the empty index %s', $path ) );
							}
						}
					}

					$prefix = $this->get_key_prefix( $key );
					if ( strlen( $prefix ) > 0 ) {
						$subsubdir = $cache_dir . DIRECTORY_SEPARATOR . $group . DIRECTORY_SEPARATOR . $prefix;
						if ( !is_dir( $subsubdir ) ) {
							$created = $this->mkdir( $subsubdir, 0755, true );
							if ( !$created && !is_dir( $subsubdir ) ) {
								$error = error_get_last();
								$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
								wps_log_error( sprintf( 'Could not create the cache directory %s [%s]', $subsubdir, $msg ) );
								return null;
							} else {
								if ( WPS_CACHE_DEBUG ) {
									wps_log_verbose( sprintf( 'Created the cache directory %s', $subsubdir ) );
								}
							}
						}
						$bits[] = $prefix;
					}
				}

				$bits[] = 'index.php';
				$path = implode( DIRECTORY_SEPARATOR, $bits );

				if ( !file_exists( $path ) ) {

					if ( @file_put_contents( $path, self::PRELUDE . "\n" ) === false ) {
						$error = error_get_last();
						$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
						wps_log_error( sprintf( 'Could not create the index %s [%s]', $path, $msg ) );
						return null;
					} else {
						if ( WPS_CACHE_DEBUG ) {
							wps_log_verbose( sprintf( 'Wrote the empty index %s', $path ) );
						}
					}
				}
			}
		}
		return $path;
	}

	/**
	 * Whether a cache file exists for the given key and group.
	 *
	 * @param string $key
	 * @param string $group
	 *
	 * @return boolean
	 */
	private function exists( $key, $group = '' ) {
		$exists = false;
		$path = $this->get_file_path( $key, $group );
		if ( $path !== null ) {
			$exists = file_exists( $path );
		}
		return $exists;
	}

	/**
	 * Read cache file for given key and group.
	 *
	 * @param string $key
	 * @param string $group
	 *
	 * @return string|null
	 */
	private function read( $key, $group = '' ) {
		$contents = null;
		$path = $this->get_file_path( $key, $group );
		if ( $path !== null ) {
			$contents = @file_get_contents( $path );
			if ( $contents === false ) {

				$contents = null;
			} else {
				if ( strpos( $contents, self::PRELUDE ) === 0 ) {
					$contents = trim( substr( $contents, strlen( self::PRELUDE ) ) );
				} else {
					$contents = null;
					wps_log_error( sprintf( 'Invalid cache file detected, missing header: %s', $path ) );
				}
			}
		}
		return $contents;
	}

	/**
	 * Write cache file for given key and group.
	 *
	 * @param string $key
	 * @param string $data
	 * @param string $group
	 *
	 * @return int|null bytes written
	 */
	private function write( $key, $data, $group = '' ) {

		$bytes = null;
		$path = $this->get_file_path( $key, $group );
		if ( $path !== null ) {
			$dirname = dirname( $path );
			if ( is_writable( $dirname ) ) {
				$bytes = @file_put_contents( $path, self::PRELUDE . "\n" . $data );
				if ( $bytes === false ) {
					$error = error_get_last();
					$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
					wps_log_error( sprintf( 'Data could not be written to cache file %s', $path, $msg ) );
					$bytes = null;
				} else {
					if ( WPS_CACHE_DEBUG ) {
						wps_log_verbose( sprintf( 'Data written to cache file %s', $path ) );
					}
				}
			} else {
				wps_log_error( sprintf( 'Data can not be written to cache file %s because %s is not writable', $path, $dirname ) );
			}
		}
		return $bytes;
	}

	/**
	 * Sort by ascending expiration.
	 *
	 * @param array $i1
	 * @param array $i2
	 *
	 * @return int
	 */
	public function sort_expires( $i1, $i2 ) {

		return
			( is_array( $i1 ) && !empty( $i1['expires'] ) ? intval( $i1['expires'] ) : PHP_INT_MAX )
			-
			( is_array( $i2 ) && !empty( $i2['expires'] ) ? intval( $i2['expires'] ) : PHP_INT_MAX );
	}

	/**
	 * Sort by ascending size.
	 *
	 * @param array $i1
	 * @param array $i2
	 *
	 * @return int
	 */
	public function sort_size( $i1, $i2 ) {
		return
			( is_array( $i1 ) && isset( $i1['size'] ) ? intval( $i1['size'] ) : 0 )
			-
			( is_array( $i2 ) && isset( $i2['size'] ) ? intval( $i2['size'] ) : 0 );
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

		$key = $this->get_key( $key );
		if ( $key === null ) {
			return null;
		}

		$value = null;
		$object = null;
		$contents = $this->read( $key, $group );
		if ( $contents !== null ) {
			if ( is_serialized( $contents ) ) {

				$object = @unserialize( $contents );
				if ( $object === false ) {
					$error = error_get_last();
					$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
					$path = $this->get_file_path( $key, $group );
					wps_log_error( sprintf( 'Invalid cache file data: %s [%s]', $path !== null ? $path : '?', $msg ) );

					$this->delete( $key, $group, false );
				}
			} else {
				$path = $this->get_file_path( $key, $group );
				wps_log_error( sprintf( 'Invalid cache file data: %s [non-serialized]', $path !== null ? $path : '?' ) );

				$this->delete( $key, $group, false );
			}
		}
		if ( !( $object instanceof Cache_Object ) ) {
			$object = null;
		} else {
			if ( $object->has_expired() ) {

				$this->delete( $key, $group, false );
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
	 * @return int bytes written
	 */
	public function set( $key, $data, $group = '', $expire = 0 ) {

		$key = $this->get_key( $key );
		if ( $key === null ) {
			return 0;
		}

		$skip = false;

		$bytes = 0;

		$use_master = true;
		if ( $this->max_files === 0 && $this->max_size === 0 ) {
			$use_master = false;
		}

		$mlock = null;
		$lock = null;
		if ( $use_master ) {

			$master_lock = $this->get_master_lock_path();
			if ( $master_lock !== null ) {

				$lock = new Lock( $master_lock, false );
			}
			if ( $lock !== null ) {

				if ( $lock->writer() ) {
					$master = $this->get_master();
				} else {

					return 0;
				}
			} else {

				return 0;
			}

			if ( $this->max_files > 0 ) {
				if ( $master['count'] >= $this->max_files ) {
					$skip = true;
					if ( WPS_CACHE_DEBUG ) {
						wps_log_warning(
							sprintf(
								'Could not add a cache file entry because the maximum number of cache files (%d/%d) has been reached.',
								$master['count'],
								$this->max_files
							)
						);
					}
				}
			}

			if ( !$skip ) {
				if ( $this->max_size > 0 ) {
					if ( $master['size'] >= $this->max_size ) {
						$skip = true;
						if ( WPS_CACHE_DEBUG ) {
							wps_log_warning(
								sprintf(
									'Could not add a cache file entry because the maximum size of cache files (%d/%d) has been reached.',
									$master['size'],
									$this->max_size
								)
							);
						}
					}
				}
			}
		} else {

			$master_lock = $this->get_master_lock_path();
			if ( $master_lock !== null ) {
				$mlock = new Lock( $master_lock, false );
				if ( !$mlock->reader() ) {

					return 0;
				}
			} else {

				return 0;
			}

			$lock_path = $this->get_lock_path( $key, $group );
			if ( $lock_path !== null ) {

				$lock = new Lock( $lock_path, false );
			}
			if ( $lock !== null ) {
				if ( !$lock->writer() ) {

					return 0;
				}
			} else {

				return 0;
			}
		}

		if ( !$skip ) {
			$filtered_min_free_disk_space = apply_filters( 'woocommerce_product_search_file_cache_min_free_disk_space', $this->min_free_disk_space );
			$parsed_min_free_disk_space = self::parse_storage_measure( $filtered_min_free_disk_space );

			if ( $parsed_min_free_disk_space['value'] > 0.0 ) {
				$cache_dir = $this->get_cache_dir();
				switch ( $parsed_min_free_disk_space['unit'] ) {
					case 'percent':
						$min_free_disk_space = floatval( $parsed_min_free_disk_space['value'] );
						$min_free_disk_space = min( 100.0, max( 0.0, $parsed_min_free_disk_space['value'] ) );
						if ( $min_free_disk_space > 0.0 ) {
							$free = function_exists( 'disk_free_space' ) ? disk_free_space( $cache_dir ) : null;
							$total = function_exists( 'disk_total_space' ) ? disk_total_space( $cache_dir ) : null;
							if ( $free !== null && $total !== null && $total > 0 ) {
								$free_disk_space = floatval( $free ) / floatval( $total ) * 100.0;
								if ( $free_disk_space < $min_free_disk_space ) {
									$skip = true;
									if ( WPS_CACHE_DEBUG ) {
										wps_log_warning(
											sprintf(
												'Could not add a cache file entry because the minimum free storage space (%.2f%%) has been exceeded. Free: %.2f%%, %d bytes free of total %d bytes [%s]',
												$min_free_disk_space,
												$free_disk_space,
												$free,
												$total,
												!empty( $cache_dir ) ? $cache_dir : ''
											)
										);
									}
								}
							}
						}
						break;
					default:
						$min_free_disk_space = max( 0, $parsed_min_free_disk_space['value'] );
						if ( $min_free_disk_space > 0 ) {
							$free = function_exists( 'disk_free_space' ) ? disk_free_space( $cache_dir ) : null;
							$total = function_exists( 'disk_total_space' ) ? disk_total_space( $cache_dir ) : null;
							if ( $free !== null && $total !== null && $free < $min_free_disk_space ) {
								$skip = true;
								if ( WPS_CACHE_DEBUG ) {
									wps_log_warning(
										sprintf(
											'Could not add a cache file entry because the minimum free storage space (%s) has been exceeded. Free: %d bytes free of total %d bytes [%s]',
											$parsed_min_free_disk_space['string'],
											$free,
											$total,
											!empty( $cache_dir ) ? $cache_dir : ''
										)
									);
								}
							}
						}
				}
			}
		}

		$nuke = false;

		if ( !$skip ) {

			$object = new Cache_Object( $key, $data, $expire );

			$exists = false;
			$write = true;
			$path = $this->get_file_path( $key, $group );
			if ( $path !== null ) {
				$exists = file_exists( $path );
				if ( $exists ) {
					$contents = $this->read( $key, $group );
					if ( $contents !== null ) {
						if ( is_serialized( $contents ) ) {

							$stored_object = @unserialize( $contents );
							if ( $stored_object instanceof Cache_Object ) {

								if (
									$stored_object->get_created() >= $object->get_created()
									||
									$object->get_lifespan() === $stored_object->get_lifespan() && $object->get_hash() === $stored_object->get_hash()
								) {
									$write = false;
								}
							} else {
								$msg = 'not a cache object';
								if ( $stored_object === false ) {
									$error = error_get_last();
									$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
								}
								wps_log_error( sprintf( 'Invalid cache file data: %s [%s]', $path, $msg ) );
							}
						}
					}
				}
				if ( !$exists || $write ) {

					$bytes = $this->write( $key, serialize( $object ), $group );
				}
			}

			if (
				$path !== null &&
				$bytes !== null &&
				$bytes > 0 &&
				file_exists( $path )
			) {

				$filename = $this->basename( $path );

				$index_path = $this->get_index_path( $key, $group );
				$entries = array();
				$index_data = @file_get_contents( $index_path );
				if ( $index_data !== false ) {
					if ( strpos( $index_data, self::PRELUDE ) === 0 ) {
						$index_data = trim( substr( $index_data, strlen( self::PRELUDE ) ) );
						if ( strlen( $index_data ) > 0 ) {
							$entries = json_decode( $index_data, true );
							if ( !is_array( $entries ) ) {
								$entries = array();
								wps_log_error( sprintf( 'Invalid index file detected, corrupted: %s', $index_path ) );
								$nuke = true;
							}
						}
					} else {
						wps_log_error( sprintf( 'Invalid index file detected, missing header: %s', $index_path ) );
						$nuke = true;
					}
				} else {
					$error = error_get_last();
					$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
					wps_log_error( sprintf( 'Failed to read index file: %s [%s]', $index_path, $msg ) );
					$nuke = true;
				}

				$expires = '';
				$lifespan = $object->get_lifespan();
				if ( $lifespan !== null && $lifespan > 0 ) {
					$expires = $object->get_created() + $lifespan;
				}
				$entry = array(
					'expires'  => $expires,
					'size'     => $bytes,
					'file'     => $filename,
					'created'  => $object->get_created(),
					'lifespan' => $object->get_lifespan(),
					'hash'     => $object->get_hash()
				);
				if ( WPS_CACHE_DEBUG ) {
					$entry['group'] = $group;
					if ( function_exists( 'getmypid' ) ) {
						$entry['pid'] = getmypid();
					}
				}

				$entries[$key] = $entry;

				$index_data = self::PRELUDE . "\n" . json_encode( $entries );
				$index_bytes = @file_put_contents( $index_path, $index_data );
				if ( $index_bytes === false ) {
					$error = error_get_last();
					$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
					wps_log_error( sprintf( 'Failed to write index file: %s [%s]', $index_path, $msg ) );
					$nuke = true;
				} else {
					if ( WPS_CACHE_DEBUG ) {
						wps_log_verbose( sprintf( 'Wrote key %s entry to index file: %s', $key, $index_path ) );
					}
				}
			}

			if ( $nuke ) {
				$bytes = 0;
				$this->nuke();
			}
		}

		if ( $use_master && !$nuke ) {

			if ( $bytes > 0 ) {
				$master['count']++;
				$master['size'] += $bytes;
				$this->set_master( $master );
			}
		}

		$lock->release();

		if ( $mlock !== null ) {
			$mlock->release();
		}

		return $bytes;
	}

	/**
	 * Delete from cache.
	 *
	 * @param string $key
	 * @param string $group
	 * @param boolean $blocking
	 *
	 * @return boolean
	 */
	public function delete( $key, $group = '', $blocking = true ) {

		$deleted = false;

		$use_master = true;
		if ( $this->max_files === 0 && $this->max_size === 0 ) {
			$use_master = false;
		}

		$mlock = null;
		$lock = null;
		if ( $use_master ) {
			$master_lock = $this->get_master_lock_path();
			if ( $master_lock !== null ) {
				$lock = new Lock( $master_lock, $blocking );
			}
			if ( $lock !== null ) {
				if ( $lock->writer() ) {
					$master = $this->get_master();
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {

			$master_lock = $this->get_master_lock_path();
			if ( $master_lock !== null ) {
				$mlock = new Lock( $master_lock, $blocking );
				if ( !$mlock->reader() ) {

					return false;
				}
			} else {

				return false;
			}

			$lock_path = $this->get_lock_path( $key, $group );
			if ( $lock_path !== null ) {
				$lock = new Lock( $lock_path, $blocking );
			}
			if ( $lock !== null ) {
				if ( !$lock->writer() ) {

					return false;
				}
			} else {

				return false;
			}
		}

		$nuke = false;
		$bytes = 0;

		$path = $this->get_file_path( $key, $group );
		if ( $path !== null ) {
			if ( file_exists( $path ) ) {

				clearstatcache( true, $path );
				$cache_filesize = @filesize( $path );
				if ( $cache_filesize === false ) {
					$cache_filesize = 0;
					$error = error_get_last();
					$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
					wps_log_error( sprintf( 'Failed to obtain cache file size: %s [%s]', $path, $msg ) );
				}

				$deleted = @unlink( $path );
				if ( $deleted ) {
					if ( WPS_CACHE_DEBUG ) {
						wps_log_verbose( sprintf( 'Deleted cache file: %s', $path ) );
					}

					$index_path = $this->get_index_path( $key, $group );
					$entries = array();
					$index_data = @file_get_contents( $index_path );
					if ( $index_data !== false ) {
						if ( strpos( $index_data, self::PRELUDE ) === 0 ) {
							$index_data = trim( substr( $index_data, strlen( self::PRELUDE ) ) );
							if ( strlen( $index_data ) > 0 ) {
								$entries = json_decode( $index_data, true );
								if ( !is_array( $entries ) ) {
									$entries = array();
									wps_log_error( sprintf( 'Invalid index file detected, corrupted: %s', $index_path ) );
									$nuke = true;
								}
							}
						} else {
							wps_log_error( sprintf( 'Invalid index file detected, missing header: %s', $index_path ) );
							$nuke = true;
						}
					} else {
						$error = error_get_last();
						$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
						wps_log_error( sprintf( 'Failed to read index file: %s [%s]', $index_path, $msg ) );
						$nuke = true;
					}
					if ( isset( $entries[$key] ) ) {
						$entry = $entries[$key];
						if ( isset( $entry['size'] ) ) {
							$bytes = max( 0, intval( $entry['size'] ) );
						}
						unset( $entries[$key] );
						$index_data = self::PRELUDE . "\n" . json_encode( $entries );
						$index_bytes = @file_put_contents( $index_path, $index_data );
						if ( $index_bytes === false ) {
							$error = error_get_last();
							$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
							wps_log_error( sprintf( 'Failed to write index file: %s [%s]', $index_path, $msg ) );
							$nuke = true;
						} else {
							if ( WPS_CACHE_DEBUG ) {
								wps_log_verbose( sprintf( 'Removed key %s entry from index file: %s', $key, $index_path ) );
							}
						}
					} else {
						wps_log_error( sprintf( 'Missing key %s in index file %s for deleted cache file: %s', $key, $index_path, $path ) );
						$nuke = true;
					}

					if ( $bytes === 0 ) {
						$bytes = $cache_filesize;
					}
				} else {
					$error = error_get_last();
					$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
					wps_log_error( sprintf( 'Failed to delete cache file: %s [%s]', $path, $msg ) );
					$nuke = true;
				}
			}
		}

		if ( $nuke ) {
			$this->nuke();
		}

		if ( $use_master && !$nuke ) {
			if ( $deleted ) {
				$master['count']--;
				$master['size'] -= $bytes;
				$this->set_master( $master );
			}
		}
		$lock->release();

		if ( $mlock !== null ) {
			$mlock->release();
		}

		return $deleted;
	}

	/**
	 * Get size in bytes with possible abbreviations K, M, G, T or P to number of bytes.
	 * KB, MB, ... translate to K, M, ...
	 *
	 * @param string $bytes
	 *
	 * @return array provides int 'bytes' and string 'string' a string representation
	 */
	public static function parse_storage_bytes( $bytes ) {
		$result = array( 'string' => '0', 'bytes' => 0 );
		if ( is_string( $bytes ) || is_numeric( $bytes ) ) {
			$bytes = (string) $bytes;
			$bytes = strtoupper( $bytes );
			$bytes = preg_replace( '/[^0-9.BKMGTP]/', '', $bytes );
			$bytes = preg_replace( '/([KMGTP])B/', '$1', $bytes );
			$matches = null;
			if ( preg_match( '/([0-9]*)\.?([0-9]*)([KMGTP]?)/', $bytes, $matches ) ) {
				$integer = 0;
				$decimal = 0;
				$unit = '';
				if ( !empty( $matches[1] ) ) {
					$integer = intval( $matches[1] );
				}
				if ( !empty( $matches[2] ) ) {
					$decimal = intval( $matches[2] );
				}
				if ( !empty( $matches[3] ) ) {
					$unit = $matches[3];
				}
				$mul = 1;
				$units = array( '', 'K', 'M', 'G', 'T', 'P' );
				for ( $i = 0; $i < count( $units ); $i++ ) {
					if ( $unit === $units[$i] ) {
						$mul = pow( 1024, $i );
						break;
					}
				}
				$result['bytes'] = intval( floatval( $integer . '.' . $decimal ) * $mul );
				$result['string'] = sprintf(
					'%d%s%s%s',
					$integer,
					$unit !== '' && $decimal > 0 ? '.' : '',
					$unit !== '' && $decimal > 0 ? $decimal : '',
					$unit
				);
			}
		}
		return $result;
	}

	/**
	 * Get the storage measure indicated in bytes or as a percentage.
	 *
	 * Storage measure examples: 1024, 1K, 10M, 1G, 5%, 7.5% ...
	 *
	 * @param string|number $what
	 *
	 * @return array provides int|float 'value', string 'string' a string representation and string 'unit' the UOM
	 */
	public static function parse_storage_measure( $what ) {
		$result = array( 'value' => 0, 'unit' => 'bytes', 'string' => '0' );
		if ( is_string( $what ) && strpos( $what, '%' ) !== false ) {
			$mark = strpos( $what, '%' );
			if ( $mark > 0 ) {
				$value = floatval( trim( substr( $what, 0, $mark ) ) );
				if ( $value < 0.0 ) {
					$value = 0;
				} else if ( $value > 100.0 ) {
					$value = 100.0;
				} else {
					$value = sprintf( '%.2f', $value );
				}
				if ( $value == intval( $value ) ) {
					$value = intval( $value );
				}
				$result['value'] = $value;
				$result['string'] = sprintf( '%s%%', $value );
				$result['unit'] = 'percent';
			}
		} else {
			$parsed = self::parse_storage_bytes( $what );
			$result['value'] = $parsed['bytes'];
			$result['string'] = $parsed['string'];
			$result['unit'] = 'bytes';
		}
		return $result;
	}
}

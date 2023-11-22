<?php
/**
 * class-phpredis-cache.php
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
 * Redis-based cache, uses the PhpRedis PHP extension.
 */
class PhpRedis_Cache extends Redis_Cache_Base {

	/**
	 * @var \Redis
	 */
	private $redis = null;

	/**
	 * @var string
	 */
	private $redis_version = null;

	/**
	 * Connect to a Redis instance.
	 *
	 * @param array $params instance parameters
	 * @param string $params['host'] hostname or socket; the prefix "unix://" will be removed automatically, assuming that a socket is provided if the remainder starts with "/"
	 * @param int $params['port']
	 * @param string $params['username']
	 * @param string $params['password']
	 * @param int $params['priority']
	 */
	public function __construct( $params = null ) {

		parent::__construct( $params );
		$host = isset( $params['host'] ) ? $params['host'] : null;
		$port = isset( $params['port'] ) ? $params['port'] : null;
		$username = isset( $params['username'] ) ? $params['username'] : null;
		$password = isset( $params['password'] ) ? $params['password'] : null;

		if ( $host !== null && is_string( $host ) ) {
			if ( strpos( $host, 'unix://' ) === 0 ) {
				$host = substr( $host, strlen( 'unix://' ) );
			}
			$this->host = $host;
		}
		if ( strpos( $this->host, '/' ) === 0 ) {
			$port = null;
			$this->port = -1;
		}
		if ( $port !== null && is_numeric( $port ) ) {
			$this->port = intval( $port );
		}
		if ( $username !== null && is_string( $username ) ) {
			$this->username = $username;
		}
		if ( $password !== null && is_string( $password ) ) {
			$this->password = $password;
		}

		if ( class_exists( '\Redis' ) ) {
			$this->redis = new \Redis();
			try {
				$connected = $this->redis->connect(
					$this->host,
					$this->port,
					$this->connection_timeout !== null ? $this->connection_timeout : 0,
					null,
					0,
					$this->timeout !== null ? $this->timeout : 0,
					$this->username !== null && $this->password !== null ? array( 'auth' => array( $this->username, $this->password ) ) : array()
				);
				if ( !$connected ) {
					$this->redis = null;
					wps_log_error( sprintf(
						'Failed to connect with the Redis server %1$s at port %2$s',
						esc_html( $this->host ),
						esc_html( $this->port )
					) );
				}
			} catch ( \Exception $ex ) {
				wps_log_error( $ex->getMessage() );
				$this->redis = null;
			}
		}
		$this->active = $this->redis !== null;
	}

	/**
	 * Close connection.
	 */
	public function close() {
		if ( $this->redis !== null ) {
			try {
				$this->redis->close();
			} catch ( \Exception $ex ) {
				wps_log_error( $ex->getMessage() );
				$this->redis = null;
			}
		}
	}

	/**
	 * Close up.
	 */
	public function __destruct() {
		$this->close();
	}

	/**
	 * Provide the Redis server's version.
	 *
	 * @return string
	 */
	public function get_redis_version() {
		if ( $this->redis_version === null ) {
			if ( $this->redis !== null ) {
				try {
					$info = $this->redis->info( 'server' );
					if ( is_array( $info ) && isset( $info['redis_version'] ) ) {
						$this->redis_version = $info['redis_version'];
					} else {
						$this->redis_version = '0.0.0';
					}
				} catch ( \Exception $ex ) {
					wps_log_error( $ex->getMessage() );
					$this->redis = null;
				}
			}
		}
		return $this->redis_version;
	}

	/**
	 * Flush the whole cache or a specific group.
	 *
	 * @param string $group to flush a particular group only
	 *
	 * @return boolean
	 */
	public function flush( $group = null ) {
		$flushed = false;
		if ( $this->redis !== null ) {
			if ( $group === null || $group === '' ) {
				$flushed = $this->_flush( $group );
			} else {

				$flushed = true;
				$all_groups = $this->get_all_groups( $group );
				foreach ( $all_groups as $the_group ) {
					$flushed = $this->_flush( $the_group ) && $flushed;
				}
			}
		}
		return $flushed;
	}

	/**
	 * Flush the cache.
	 *
	 * @param string $group to flush a particular group only
	 *
	 * @return boolean
	 */
	private function _flush( $group = null ) {

		$flushed = false;
		if ( $this->redis !== null ) {
			try {
				if ( !is_multisite() && ( $group === null || $group === '' ) ) {
					$flushed = $this->redis->flushDb();
				} else {

					$blog_id = get_current_blog_id();

					$unlink = false;
					$redis_version = $this->get_redis_version();
					if ( $redis_version !== null && version_compare( $redis_version , '4.0.0' ) >= 0 ) {
						$unlink = true;
					}

					$it = null;
					while( $it !== 0 ) {

						if ( $group === null || $group === '' ) {
							$keys = $this->redis->scan( $it, sprintf( '%d_*', intval( $blog_id ) ), self::FLUSH_SCAN_COUNT );
						} else {
							$keys = $this->redis->scan( $it, sprintf( '%d_%s_*', intval( $blog_id ), md5( $group ) ), self::FLUSH_SCAN_COUNT );
						}
						if ( is_array( $keys ) && count( $keys ) > 0 ) {
							if ( $unlink ) {
								$this->redis->unlink( $keys );
							} else {
								$this->redis->del( $keys );
							}
						}
					}
					$flushed = true;
				}
			} catch ( \Exception $ex ) {
				wps_log_error( $ex->getMessage() );
				$this->redis = null;
			}
		}
		return $flushed;
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
		$object = null;
		if ( $this->redis !== null ) {
			try {
				$key = $this->get_cache_key( $key, $group );
				$stored = $this->redis->get( $key );
				if ( $stored !== null ) {

					if ( is_serialized( $stored ) ) {
						$object = @unserialize( $stored );
					}
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
			} catch ( \Exception $ex ) {
				wps_log_error( $ex->getMessage() );
				$this->redis = null;
			}
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
		$success = false;
		if ( $this->redis !== null ) {
			try {
				$object = new Cache_Object( $key, $data, $expire );
				$key = $this->get_cache_key( $key, $group );
				if ( $expire > 0 ) {
					$success = $this->redis->set( $key, serialize( $object ), $expire );
				} else {
					$success = $this->redis->set( $key, serialize( $object ) );
				}
			} catch ( \Exception $ex ) {
				wps_log_error( $ex->getMessage() );
				$this->redis = null;
			}
		}
		return $success;
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
		if ( $this->redis !== null ) {
			try {
				$key = $this->get_cache_key( $key, $group );
				$redis_version = $this->get_redis_version();
				if ( $redis_version !== null && version_compare( $redis_version , '4.0.0' ) >= 0 ) {

					$deleted = $this->redis->unlink( $key );
				} else {
					$deleted = $this->redis->del( $key );
				}
				$result = $deleted > 0;
			} catch ( \Exception $ex ) {
				wps_log_error( $ex->getMessage() );
				$this->redis = null;
			}
		}
		return $result;
	}

}

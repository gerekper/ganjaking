<?php
/**
 * class-memcached-cache.php
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
 * Memcached-based cache.
 *
 * @see https://www.php.net/manual/en/memcache.examples-overview.php
 * @see https://developer.wordpress.org/reference/classes/wp_object_cache/
 * @see https://github.com/memcached/memcached/wiki/
 */
class Memcached_Cache extends Cache_Base {

	/**
	 * Default host.
	 *
	 * @var string
	 */
	public const HOST_DEFAULT = 'localhost';

	/**
	 * Default port.
	 *
	 * @var int
	 */
	public const PORT_DEFAULT = 11211;

	/**
	 * Default weight.
	 *
	 * @var int
	 */
	public const WEIGHT_DEFAULT = 0;

	/**
	 * Maximum expiration indicated in seconds, otherwise UNIX timestamp required
	 *
	 * @var int
	 */
	public const EXPIRATION_MAX_SECONDS = 2592000;

	protected $id = 'memcached';

	/**
	 * The maximum key length as determined by KEY_MAX_LENGTH
	 *
	 * Defined in https://github.com/memcached/memcached/blob/1.6.19/memcached.h#L68
	 *
	 * @var int
	 */
	private const KEY_MAX_LENGTH = 250;

	/**
	 * @var \Memcached
	 */
	private $memcached = null;

	/**
	 * @var array
	 */
	private $servers = null;

	/**
	 * @var string
	 */
	private $host = self::HOST_DEFAULT;

	/**
	 * @var int
	 */
	private $port = self::PORT_DEFAULT;

	/**
	 * @var int
	 */
	private $weight = self::WEIGHT_DEFAULT;

	/**
	 * @var string
	 */
	private $username = null;

	/**
	 * @var string
	 */
	private $password = null;

	/**
	 * @var string
	 */
	private $persistent_id = 'woocommerce-product-search';

	/**
	 * Connect to a Memcached instance.
	 *
	 * @param array $params instance parameters
	 * @param string $params['host'] hostname or socket; the prefix "unix://" will be removed automatically
	 * @param int $params['port']
	 * @param string $params['username']
	 * @param string $params['password']
	 * @param int $params['priority']
	 */
	public function __construct( $params = null ) {

		parent::__construct( $params );
		$host = isset( $params['host'] ) ? $params['host'] : null;
		$port = isset( $params['port'] ) ? $params['port'] : null;
		$weight = isset( $params['weight'] ) ? $params['weight'] : null;
		$username = isset( $params['username'] ) ? $params['username'] : null;
		$password = isset( $params['password'] ) ? $params['password'] : null;

		$servers = array(
			'host' => array(),
			'port' => array(),
			'weight' => array()
		);
		if ( $host !== null ) {
			$hosts = array_map( 'trim', explode( ',', $host ) );
			foreach ( $hosts as $server_host ) {
				if ( strlen( $server_host ) > 0 ) {

					if ( strpos( $host, 'unix://' ) === 0 ) {
						$server_host = substr( $server_host, strlen( 'unix://' ) );
					}
					$servers['host'][] = $server_host;
				}
			}
		}
		if ( $port !== null ) {
			$ports = array_map( 'trim', explode( ',', $port ) );
			foreach ( $ports as $server_port ) {
				if ( strlen( $server_port ) > 0 && is_numeric( $server_port ) ) {
					$server_port = intval( $server_port );
					if ( $server_port >= 0 ) {
						$servers['port'][] = $server_port;
					}
				}
			}
		}
		if ( $weight !== null ) {
			$weights = array_map( 'trim', explode( ',', $weight ) );
			foreach ( $weights as $server_weight ) {
				if ( strlen( $server_weight ) > 0 && is_numeric( $server_weight ) ) {
					$server_weight = intval( $server_weight );
					$servers['weight'][] = $server_weight;
				}
			}
		}

		if ( count( $servers['host'] ) === 0 ) {
			$servers['host'] = array( $this->host );
		}
		if ( count( $servers['port'] ) === 0 ) {
			$servers['port'] = array( $this->port );
		}
		if ( count( $servers['weight'] ) === 0 ) {
			$servers['weight'] = array( $this->weight );
		}

		$this->host = implode( ',', $servers['host'] );
		$this->port = implode( ',', $servers['port'] );
		$this->weight = implode( ',', $servers['weight'] );

		$this->servers = array();
		for ( $i = 0; $i < count( $servers['host'] ); $i++ ) {
			$host = $servers['host'][$i];
			$port = 0;
			if ( strpos( $host, '/' ) !== 0 ) {
				$port = isset( $servers['port'][$i] ) ? $servers['port'][$i] : self::PORT_DEFAULT;
			}
			$weight = isset( $servers['weight'][$i] ) ? $servers['weight'][$i] : self::WEIGHT_DEFAULT;
			$server = array( $host, $port, $weight );
			$this->servers[] = $server;
		}

		if ( $username !== null && is_string( $username ) ) {
			$this->username = $username;
		}
		if ( $password !== null && is_string( $password ) ) {
			$this->password = $password;
		}

		if ( class_exists( '\Memcached' ) ) {

			$connected = false;

			$this->memcached = new \Memcached();
			$this->memcached->setOption( \Memcached::OPT_TCP_NODELAY, true );

			if ( !$connected ) {

				if ( $this->memcached !== null ) {
					if ( $this->username !== null && $this->password !== null ) {
						if ( method_exists( $this->memcached, 'setSaslAuthData' ) ) {
							$this->memcached->setOption( \Memcached::OPT_BINARY_PROTOCOL, true );
							$this->memcached->setSaslAuthData( $this->username, $this->password );
							$code = $this->memcached->getResultCode();
							if ( $code !== \Memcached::RES_SUCCESS ) {
								$msg = $this->memcached->getResultMessage();
								wps_log_error( sprintf(
									'Failed to set authentication credentials with Memcached at %1$s%2$s [%3$s]',
									esc_html( $this->host ),
									$this->port !== null ? ':' . esc_html( $this->port ) : '',
									esc_html( $msg )
								) );
								$this->memcached = null;
							}
						} else {
							wps_log_error( 'The Memcached extension does not support SASL authentication.' );
						}
					}
				}

				if ( $this->memcached !== null ) {
					if ( $this->connection_timeout !== null ) {

						$this->memcached->setOption( \Memcached::OPT_CONNECT_TIMEOUT, intval( $this->connection_timeout * 1000 ) );
					}
					if ( $this->timeout !== null ) {
						$this->memcached->setOption( \Memcached::OPT_SEND_TIMEOUT, intval( $this->timeout * 1000000 ) );
						$this->memcached->setOption( \Memcached::OPT_RECV_TIMEOUT, intval( $this->timeout * 1000000 ) );
					}
				}

				if ( $this->memcached !== null ) {
					$added = $this->memcached->addServers( $this->servers );
					$code = $this->memcached->getResultCode();
					if ( !$added || $code !== \Memcached::RES_SUCCESS ) {
						$msg = $this->memcached->getResultMessage();
						wps_log_error( sprintf(
							'Failed to connect with Memcached at %1$s%2$s [%3$s]',
							esc_html( $this->host ),
							$this->port !== null ? ':' . esc_html( $this->port ) : '',
							esc_html( $msg )
						) );
						$this->memcached = null;
					}
				}

			}
		} else {
			wps_log_error( 'Memcached is not available' );
		}

		$this->active = $this->memcached !== null;
	}

	/**
	 * Clean up.
	 */
	public function __destruct() {
		if ( $this->memcached !== null ) {
			$this->memcached->quit();
		}
	}

	/**
	 * Flush the whole cache or a specific group.
	 *
	 * - flushing all keys for the given group is not guaranteed
	 * - flushing only keys for the current site is not guaranteed
	 * - if the connection requires authentication, the whole cache will be flushed, for all groups, whether a group is given or not, and also for all sites in a multisite setup
	 * - if flushing groups is desired or a multisite setup is used, a better alternative like our File Cache or Redis should be used
	 *
	 * @param string $group to flush a particular group only
	 *
	 * @return boolean
	 */
	public function flush( $group = null ) {
		$flushed = false;
		if ( $this->memcached !== null ) {

			if ( $this->username !== null && $this->password !== null ) {
				$flushed = $this->memcached->flush();
			} else {
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
		if ( $this->memcached !== null ) {
			if ( !is_multisite() ) {
				if ( $group === null || $group === '' ) {

					$flushed = $this->memcached->flush();
				} else {

					$blog_id = get_current_blog_id();
					$keys = $this->get_all_keys();
					if ( is_array( $keys ) ) {
						foreach ( $keys as $key ) {
							if ( strpos( $key, sprintf( '%d_%s_', intval( $blog_id ), md5( $group ) ) ) === 0 ) {
								$this->memcached->delete( $key );
							}
						}
					}
					$flushed = true;
				}
			} else {

				$blog_id = get_current_blog_id();
				$keys = $this->get_all_keys();
				if ( is_array( $keys ) ) {
					foreach ( $keys as $key ) {
						if ( $group === null || $group === '' ) {

							if ( strpos( $key, sprintf( '%d_', intval( $blog_id ) ) ) === 0 ) {
								$this->memcached->delete( $key );
							}
						} else {

							if ( strpos( $key, sprintf( '%d_%s_', intval( $blog_id ), md5( $group ) ) ) === 0 ) {
								$this->memcached->delete( $key );
							}
						}
					}
				}
				$flushed = true;
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

		if ( $this->memcached !== null ) {
			$memcached_key = $this->get_memcached_key( $key, $group );
			$object = $this->memcached->get( $memcached_key );
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

		if ( $this->memcached !== null ) {
			$object = new Cache_Object( $key, $data, $expire );
			$memcached_key = $this->get_memcached_key( $key, $group );

			if ( $expire > self::EXPIRATION_MAX_SECONDS ) {
				$expire += time();
			}
			$success = $this->memcached->set( $memcached_key, $object, $expire );
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
		$deleted = false;
		if ( $this->memcached !== null ) {
			$memcached_key = $this->get_memcached_key( $key, $group );
			$deleted = $this->memcached->delete( $memcached_key );
		}
		return $deleted;
	}

	/**
	 * Build the key for the given key and group.
	 *
	 * @param string $key
	 * @param string $group
	 *
	 * @return string
	 */
	private function get_memcached_key( $key, $group = '' ) {
		$group = $this->get_group( $group );
		$blog_id = get_current_blog_id();
		$key = sprintf( '%d_%s_%s', intval( $blog_id ), md5( $group ), $key );
		if ( strlen( $key ) > self::KEY_MAX_LENGTH ) {

			$key = sprintf( '%d_%s_%s', intval( $blog_id ), md5( $group ), sha1( $key ) );
		}
		return $key;
	}

	/**
	 * Retrieve all keys from Memcached.
	 *
	 * @return array
	 */
	private function get_all_keys() {

		$keys = array();
		if ( $this->memcached !== null ) {

			$keys = $this->memcached->getAllKeys();
			if ( !is_array( $keys ) ) {
				$keys = array();
			}

		}
		return $keys;
	}

}

<?php
/**
 * class-redis-cache.php
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
 * Redis-based cache.
 */
class Redis_Cache extends Redis_Cache_Base {

	/**
	 * @var resource
	 */
	private $resource = null;

	/**
	 * @var string
	 */
	private $redis_version = null;

	/**
	 * Connect to a Redis instance.
	 *
	 * @param array $params instance parameters
	 * @param string $params['host'] hostname or socket; the prefix "unix://" will automatically be added if it starts with "/", assuming that a socket is provided instead of a hostname
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

			if ( strpos( $host, '/' ) === 0 ) {
				$host = 'unix://' . $host;
			}
			$this->host = $host;
		}
		if ( strpos( $this->host, 'unix://' ) === 0 ) {
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
		$this->connect();
	}

	/**
	 * Connect or reconnect to the Redis server.
	 */
	private function connect() {

		if ( $this->resource !== null ) {
			$this->close();
		}

		$errno = null;
		$errstr = null;
		if ( function_exists( 'fsockopen' ) ) {
			if ( $this->connection_timeout !== null ) {
				$resource = @fsockopen( $this->host, $this->port, $errno, $errstr, $this->connection_timeout );
			} else {
				$resource = @fsockopen( $this->host, $this->port, $errno, $errstr );
			}
			if ( $resource !== false && is_resource( $resource ) ) {
				$this->resource = $resource;
				stream_set_blocking( $this->resource, true );

				if ( $this->timeout !== null ) {
					@stream_set_timeout( $this->resource, 0, $this->timeout * 1000000 );
				}
				if ( $this->username !== null && $this->password !== null ) {
					try {
						$authenticated = $this->auth( $this->username, $this->password );
						if ( !$authenticated ) {
							throw new \Exception();
						}
					} catch ( \Exception $ex ) {
						wps_log_error(
							sprintf(
								'Failed to authenticate with the Redis server %1$s at port %2$s',
								esc_html( $this->host ),
								esc_html( $this->port )
							)
						);
						$this->close();
					}
				}
			} else {
				wps_log_error(
					sprintf(
						'Failed to connect to the Redis server %1$s at port %2$s: [%3$s] %4$s',
						esc_html( $this->host ),
						esc_html( $this->port ),
						$errno !== null ? esc_html( $errno ) : '?',
						$errstr !== null ? esc_html( $errstr ) : '?'
					)
				);
			}
		} else {
			wps_log_error(
				sprintf(
					'Cannot connect to the Redis server %1$s at port %2$s because this server does not have fsockopen enabled.',
					esc_html( $this->host ),
					esc_html( $this->port )
				)
			);
		}

		$this->active = $this->resource !== null;
	}

	/**
	 * Close connection.
	 */
	public function close() {
		if ( $this->resource !== null ) {
			if ( @fclose( $this->resource ) ) {
				$this->resource = null;
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
	 * Authenticate with the Redis server.
	 *
	 * @param string $username
	 * @param string $password
	 *
	 * @return boolean
	 */
	private function auth( $username, $password ) {
		$result = array();
		if ( $this->resource !== null ) {
			$args = array( 'AUTH', $username, $password );
			$response = $this->command( $args );
			$result = $response === 'OK';
		}
		return $result;
	}

	/**
	 * Write the given data to the server socket.
	 *
	 * @throws \Exception
	 *
	 * @param string $data
	 *
	 * @return int
	 */
	private function write( $data ) {
		$total = 0;
		if ( $this->resource !== null ) {
			$length = strlen( $data );
			for ( $total = 0, $bytes = 0; $total < $length; $total += $bytes ) {
				$bytes = @fwrite( $this->resource, substr( $data, $total ) );
				if ( $bytes === false ) {
					break;
				}
			}
			if ( $total < $length ) {
				$msg = sprintf(
					'Failed to complete a write to the Redis server %1$s at port %2$s, wrote %3$d of %4$d bytes',
					esc_html( $this->host ),
					esc_html( $this->port ),
					$total,
					$length
				);
				throw new \Exception( $msg );
			}
		}
		return $total;
	}

	/**
	 * Read a reply from the server socket.
	 *
	 * @throws \Exception
	 *
	 * @return mixed|null
	 */
	private function read() {
		$result = null;

		$line = false;
		if ( $this->resource !== null ) {
			if ( !@feof( $this->resource ) ) {
				$line = @fgets( $this->resource );
			}
		}
		if ( $line === false ) {
			$msg = sprintf(
				'Failed to read from the Redis server %1$s at port %2$s',
				esc_html( $this->host ),
				esc_html( $this->port )
			);
			$metas = stream_get_meta_data( $this->resource );
			if ( is_array( $metas ) ) {
				if ( isset( $metas['timed_out'] ) && $metas['timed_out'] ) {
					$msg = sprintf(
						'Timeout while trying to read from the Redis server %1$s at port %2$s',
						esc_html( $this->host ),
						esc_html( $this->port )
					);
				}
			}
			throw new \Exception( $msg );
		}

		$line = trim( $line );
		$type = substr( $line, 0, 1 );
		switch ( $type ) {
			case '-':

				$msg = sprintf(
					'Received an error reply from the Redis server %1$s at port %2$s: %3$s',
					esc_html( $this->host ),
					esc_html( $this->port ),
					esc_html( trim( substr( $line, 4 ) ) )
				);
				throw new \Exception( $msg );
				break;
			case '+':

				$result = substr( $line, 1 );
				break;
			case '$':

				if ( $line !== '$-1' ) {

					$size = intval( substr( $line, 1 ) );

					$result = stream_get_contents( $this->resource, $size + 2 );
					if ( $result !== false ) {
						$result = substr( $result, 0, $size );
					}
				}

				break;
			case '*':

				$count = intval( substr( $line, 1 ) );
				if ( $count > 0 ) {
					$result = array();
					for ( $i = 0; $i < $count; $i++ ) {
						$result[] = $this->read();
					}
				}
				break;
			case ':':

				$result = intval( substr( $line, 1 ) );
				break;
			default:
				$msg = sprintf(
					'Received an unknown reply from the Redis server %1$s at port %2$s: %s',
					esc_html( $this->host ),
					esc_html( $this->port ),
					esc_html( $line )
				);
				throw new \Exception( $msg );
		}
		return $result;
	}

	/**
	 * Reset the connection.
	 */
	private function reset() {

		if ( $this->resource !== null ) {
			$this->close();

			if ( $this->active ) {
				$this->connect();
			}
		}
	}

	/**
	 * Send the command and obtain a result.
	 *
	 * @throws \Exception
	 *
	 * @param array $args
	 *
	 * @return mixed|null result
	 */
	private function command( $args ) {
		$result = null;
		if ( is_array( $args ) && count( $args ) > 0 ) {
			$lines = array( sprintf( '*%d', count( $args ) ) );
			foreach ( $args as $arg ) {
				$lines[] = sprintf( '$%d', strlen( $arg ) );
				$lines[] = $arg;
			}
			$command = implode( "\r\n", $lines ) . "\r\n";
			try {
				$bytes = $this->write( $command );
				$result = $this->read();
			} catch ( \Exception $ex ) {
				wps_log_error( $ex->getMessage() );
				$this->reset();
			}
		}
		return $result;
	}

	/**
	 * Get Redis info.
	 *
	 * @param string $section
	 *
	 * @return array
	 */
	public function info( $section = null ) {
		$info = array();
		if ( $this->resource !== null ) {
			$args = array( 'INFO' );
			if ( $section !== null && is_string( $section ) && strlen( $section ) > 0 ) {
				$args[] = $section;
			}
			$response = $this->command( $args );
			if ( is_string( $response ) ) {
				$section = '';
				$lines = explode( "\r\n", $response );
				foreach ( $lines as $line ) {
					$line = trim( $line );
					if ( strlen( $line ) > 0 ) {
						if ( strpos( $line, '#' ) === 0 ) {
							$section = strtolower( trim( substr( $line, 1 ) ) );
						} else {
							list( $key, $value )= explode( ':', trim( $line ) );
							$info[$section][$key] = $value;
						}
					}
				}
			}
			if ( $section !== null && key_exists( $section, $info ) ) {
				$info = $info[$section];
			}
		}
		return $info;
	}

	/**
	 * Provide the Redis server's version.
	 *
	 * @return string
	 */
	public function get_redis_version() {
		if ( $this->redis_version === null ) {
			$info = $this->info( 'server' );
			if ( is_array( $info ) && isset( $info['redis_version'] ) ) {
				$this->redis_version = $info['redis_version'];
			} else {
				$this->redis_version = '0.0.0';
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
		if ( $this->resource !== null ) {
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
	 * Flushes the cache or a cache group.
	 *
	 * @param string $group to flush a particular group only
	 *
	 * @return boolean
	 */
	private function _flush( $group = null ) {

		$flushed = false;
		if ( $this->resource !== null ) {
			if ( !is_multisite() && ( $group === null || $group === '' ) ) {
				$flushed = $this->flushdb();
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
						$keys = $this->scan( $it, sprintf( '%d_*', intval( $blog_id ) ), self::FLUSH_SCAN_COUNT );
					} else {
						$keys = $this->scan( $it, sprintf( '%d_%s_*', intval( $blog_id ), md5( $group ) ), self::FLUSH_SCAN_COUNT );
					}
					if ( is_array( $keys ) ) {
						if ( count( $keys ) > 0 ) {

							if ( $unlink ) {
								array_unshift( $keys, 'UNLINK' );
							} else {
								array_unshift( $keys, 'DEL' );
							}
							$this->command( $keys );
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
		if ( $this->resource !== null ) {
			$key = $this->get_cache_key( $key, $group );
			$args = array(
				'GET',
				$key
			);
			$response = $this->command( $args );
			if ( $response !== null ) {

				if ( is_serialized( $response ) ) {
					$object = @unserialize( $response );
					if ( $object === false ) {
						$this->reset();
					}
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
		if ( $this->resource !== null ) {
			$object = new Cache_Object( $key, $data, $expire );
			$key = $this->get_cache_key( $key, $group );
			$args = array(
				'SET',
				$key,
				serialize( $object )
			);
			if ( is_numeric( $expire ) ) {
				$expire = intval( $expire );
				if ( $expire > 0 ) {
					$args[] = 'EX';
					$args[] = $expire;
				}
			}
			$response = $this->command( $args );
			$success = $response === 'OK';
		}
		return $success;
	}

	/**
	 * Iterator.
	 *
	 * @param int $cursor
	 * @param string $match
	 * @param int $count
	 *
	 * @return string[]
	 */
	public function scan( &$cursor = 0, $match = null, $count = null ) {
		$result = array();
		if ( $this->resource !== null ) {
			if ( is_numeric( $cursor ) ) {
				$cursor = max( 0, intval( $cursor ) );
			} else {
				$cursor = 0;
			}
			$args = array(
				'SCAN',
				$cursor
			);
			if ( is_string( $match ) ) {
				$args[] = 'MATCH';
				$args[] = $match;
			}
			if ( is_numeric( $count ) ) {
				$count = max( 1, intval( $count ) );
				$args[] = 'COUNT';
				$args[] = $count;
			}
			$response = $this->command( $args );
			if ( is_array( $response ) ) {
				if ( count( $response ) > 1 ) {
					$cursor = is_numeric( $response[0] ) ? intval( $response[0] ) : 0;
					$result = is_array( $response[1] ) ? $response[1] : array();
				}
			}
		}
		return $result;
	}

	/**
	 * Select the logical database.
	 *
	 * Note that new connections always use the database 0.
	 *
	 * @param int $database
	 *
	 * @return boolean
	 */
	public function select( $database = 0 ) {
		$result = false;
		if ( $this->resource !== null ) {
			if ( is_numeric( $database ) ) {
				$database = max( 0, intval( $database ) );
				$args = array(
					'SELECT',
					$database
				);
				$response = $this->command( $args );
				$result = $response === 'OK';
			}
		}
		return $result;
	}

	/**
	 * Flush the currently selected database.
	 *
	 * @param boolean $async flush asynchronously, default: true
	 *
	 * @return boolean
	 */
	public function flushdb( $async = true ) {
		$result = false;
		if ( $this->resource !== null ) {
			$async = boolval( $async );
			$args = array(
				'FLUSHDB',
				$async ? 'ASYNC' : 'SYNC'
			);
			$response = $this->command( $args );
			$result = $response === 'OK';
		}
		return $result;
	}

	/**
	 * Flush all databases.
	 *
	 * @param boolean $async flush asynchronously, default: true
	 *
	 * @return boolean
	 */
	public function flushall( $async = true ) {
		$result = false;
		if ( $this->resource !== null ) {
			$async = boolval( $async );
			$args = array(
				'FLUSHALL',
				$async ? 'ASYNC' : 'SYNC'
			);
			$response = $this->command( $args );
			$result = $response === 'OK';
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
		if ( $this->resource !== null ) {
			$key = $this->get_cache_key( $key, $group );
			$redis_version = $this->get_redis_version();
			if ( $redis_version !== null && version_compare( $redis_version , '4.0.0' ) >= 0 ) {
				$args = array( 'UNLINK', $key );
			} else {
				$args = array( 'DEL', $key );
			}
			$response = $this->command( $args );
			$result = $response > 0;
		}
		return $result;
	}

}

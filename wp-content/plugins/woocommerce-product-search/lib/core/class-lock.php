<?php
/**
 * class-lock.php
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

require_once 'class-lock-exception.php';

/**
 * Lock file.
 */
class Lock {

	/**
	 * Timeout slices
	 *
	 * @var int
	 */
	const TIMEOUT_SLICES = 10;

	/**
	 * Microseconds in a second
	 *
	 * @var int
	 */
	const SECOND = 1000000;

	/**
	 * Full path of the lockfile
	 *
	 * @var string
	 */
	private $path = null;

	/**
	 * File pointer resource
	 *
	 * @var resource
	 */
	private $h = null;

	/**
	 * Blocking lock
	 *
	 * @var boolean
	 */
	private $blocking = true;

	/**
	 * Lock timeout, microseconds
	 *
	 * @var integer
	 */
	private $timeout = 100000;

	/**
	 * Create a lock file instance using the file given as path.
	 *
	 * Blocks by default, $blocking set false allows to use $timeout.
	 *
	 * When lock is non-blocking, $timeout determines period during which write or read will be attempted repeatedly.
	 * A $timeout of 0 will fail on the first attempt if read of write lock is not obtained.
	 *
	 * @throws Lock_Exception
	 *
	 * @param string $path lockfile
	 * @param boolean $blocking default true
	 * @param int $timeout microseconds
	 */
	public function __construct( $path, $blocking = null, $timeout = null ) {
		if ( $blocking !== null && is_bool( $blocking ) ) {
			$this->blocking = boolval( $blocking );
		}
		if ( $timeout !== null && is_numeric( $timeout ) ) {
			$this->timeout = max( 0, intval( $timeout ) );
		}
		if ( is_string( $path ) ) {
			$h = @fopen( $path, 'c' );
			if ( $h !== false ) {
				$this->path = $path;
				@fclose( $h );
			} else {
				$error = error_get_last();
				$msg = $error !== null && !empty( $error['message'] ) ? $error['message'] : '';
				if ( !file_exists( $path ) ) {
					$error_msg = sprintf( 'Could not create lock file %s [%s]', $path, $msg );
				} else {
					$error_msg = sprintf( 'Could not access lock file %s [%s]', $path, $msg );
				}
				wps_log_error( $error_msg );
				throw new Lock_Exception( $error_msg );
			}
		}
		register_shutdown_function( array( $this, 'release' ) );
	}

	/**
	 * Lock file path
	 *
	 * @return string
	 */
	public function get_path() {
		return $this->path;
	}

	/**
	 * Lock file pointer resource
	 *
	 * @return resource
	 */
	public function get_resource() {
		return $this->h;
	}

	/**
	 * Tells whether the lock is usable
	 *
	 * @return boolean
	 */
	public function is_usable() {
		return $this->path !== null && is_readable( $this->path );
	}

	/**
	 * Acquire a write lock
	 *
	 * @return boolean
	 */
	public function writer() {
		if ( $this->path !== null ) {
			if ( $this->h === null ) {
				$h = @fopen( $this->path, 'r+' );
				if ( $h !== false ) {
					if ( $this->blocking ) {
						if ( @flock( $h, LOCK_EX ) ) {
							$this->h = $h;
						} else {

							wps_log_warning( sprintf( 'Could not acquire write lock on file %s', $this->path ) );
						}
					} else {
						$slice = $this->timeout / self::TIMEOUT_SLICES;
						$sleep = floor( $slice / self::SECOND );
						$usleep = (int) $slice % self::SECOND;
						$i = 0;
						while ( $this->h === null && $i < self::TIMEOUT_SLICES ) {
							$i++;
							if ( @flock( $h, LOCK_EX | LOCK_NB ) ) {
								$this->h = $h;
								break;
							} else {
								if ( $this->timeout > 0 ) {
									if ( $sleep > 0 ) {
										sleep( $sleep );
									}
									if ( $usleep > 0 ) {
										usleep( $usleep );
									}
								} else {
									break;
								}
							}
						}

					}
				} else {
					wps_log_error( sprintf( 'Could not access lock file %s to acquire write lock', $this->path ) );
				}
			}
		}
		return $this->h !== null;
	}

	/**
	 * Acquire a read lock
	 *
	 * @return boolean
	 */
	public function reader() {
		if ( $this->path !== null ) {
			if ( $this->h === null ) {
				$h = @fopen( $this->path, 'r' );
				if ( $h !== false ) {
					if ( $this->blocking ) {
						if ( @flock( $h, LOCK_SH ) ) {
							$this->h = $h;
						} else {
							wps_log_warning( sprintf( 'Could not acquire read lock on file %s', $this->path ) );
						}
					} else {
						$slice = $this->timeout / self::TIMEOUT_SLICES;
						$sleep = floor( $slice / self::SECOND );
						$usleep = (int) $slice % self::SECOND;
						$i = 0;
						while ( $this->h === null && $i < self::TIMEOUT_SLICES ) {
							$i++;
							if ( @flock( $h, LOCK_SH | LOCK_NB ) ) {
								$this->h = $h;
								break;
							} else {
								if ( $this->timeout > 0 ) {
									if ( $sleep > 0 ) {
										sleep( $sleep );
									}
									if ( $usleep > 0 ) {
										usleep( $usleep );
									}
								} else {
									break;
								}
							}
						}
						if ( $this->h === null ) {
							wps_log_warning( sprintf( 'Could not acquire non-blocking read lock on file %s', $this->path ) );
						}
					}
				} else {
					wps_log_error( sprintf( 'Could not access lock file %s to acquire read lock', $this->path ) );
				}
			}
		}
		return $this->h !== null;
	}

	/**
	 * Release the lock
	 *
	 * @return boolean
	 */
	public function release() {
		$released = false;
		if ( $this->h !== null ) {
			$released = @flock( $this->h, LOCK_UN );
			@fclose( $this->h );
			$this->h = null;
		}
		return $released;
	}
}

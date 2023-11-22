<?php
/**
 * class-woocommerce-product-search-lock.php
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
 * @since 4.1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lock.
 *
 * @deprecated since 5.0.0
 */
class WooCommerce_Product_Search_Lock {

	/**
	 * @var string Filename for the lockfile.
	 */
	private $lock = '.wps_lock';

	/**
	 * @var int File pointer for the lockfile.
	 */
	private $h = null;

	/**
	 * @var int
	 */
	private $verbosity = WooCommerce_Product_Search_Log::WARNING;

	/**
	 * Create a lock instance with optional lock filename specified as $lock.
	 *
	 * @param string $lock
	 */
	public function __construct( $lock = null ) {
		if ( $lock !== null && is_string( $lock ) ) {
			$this->lock = $lock;
		}
	}

	/**
	 * @param int $verbosity
	 */
	public function set_verbosity( $verbosity ) {
		switch ( $verbosity ) {
			case WooCommerce_Product_Search_Log::INFO:
			case WooCommerce_Product_Search_Log::WARNING:
			case WooCommerce_Product_Search_Log::ERROR:
				$this->verbosity = $verbosity;
				break;
		}
	}

	/**
	 * @return int
	 */
	public function get_verbosity() {
		return $this->verbosity;
	}

	/**
	 * Provides the lock filename with path.
	 *
	 * @return string
	 */
	private function get_lockfile() {
		return WOO_PS_CORE_LIB . '/' . $this->lock;
	}

	/**
	 * Checks and returns true if the lockfile exists.
	 *
	 * @return boolean true if the lockfile exists
	 */
	public function check_lock() {
		$exists = false;
		$lockfile = $this->get_lockfile();
		if ( !file_exists( $lockfile ) ) {
			if ( $h = @fopen( $lockfile, 'w' ) ) {
				@fclose( $h );
				$exists = true;
			} else {
				if ( $this->verbosity <= WooCommerce_Product_Search_Log::WARNING ) {
					wps_log_warning( sprintf( 'Could not create the lockfile (%s).', $lockfile ) );
				}
			}
		} else {
			$exists = true;
		}
		return $exists;
	}

	/**
	 * Acquire the lock; also acquires if the lockfile is not available.
	 *
	 * @return boolean true if the lock was succesfully acquired or the lockfile is not available
	 */
	public function acquire() {
		$acquired = true;
		$lockfile = $this->get_lockfile();
		if ( $this->check_lock() ) {
			if ( $this->h = @fopen( $lockfile, 'r+' ) ) {
				if ( !flock( $this->h, LOCK_EX | LOCK_NB ) ) {
					$acquired = false;
				}
			} else {
				if ( $this->verbosity <= WooCommerce_Product_Search_Log::WARNING ) {
					wps_log_warning( sprintf( 'Could not open the lockfile (%s).', $lockfile ) );
				}
			}
		}
		if ( $acquired ) {
			if ( $this->verbosity <= WooCommerce_Product_Search_Log::INFO ) {
				wps_log_info( sprintf( 'Acquired the lock (%s).', $lockfile ) );
			}
		}
		return $acquired;
	}

	/**
	 * Release the lock.
	 *
	 * @return boolean true if the lock could be released, false on failure
	 */
	public function release() {
		$released = false;
		$lockfile = $this->get_lockfile();
		if ( $this->h !== null ) {
			if ( $this->check_lock() ) {
				if ( flock( $this->h, LOCK_UN ) ) {
					$released = true;
				}
				@fclose( $this->h );
				$this->h = null;
			}
		}
		if ( $released ) {
			if ( $this->verbosity <= WooCommerce_Product_Search_Log::INFO ) {
				wps_log_info( sprintf( 'Released the lock (%s).', $lockfile ) );
			}
		}
		return $released;
	}
}

<?php
/**
 * class-woocommerce-product-search-guardian.php
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
 * @since 3.3.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Processing guardian.
 */
class WooCommerce_Product_Search_Guardian {

	/**
	 * Base factor.
	 *
	 * @var int
	 */
	const BASE_DELTA = 1048576;

	/**
	 * Resilience factor.
	 *
	 * @var float
	 */
	const DELTA_F    = 1.2;

	/**
	 * Status OK.
	 *
	 * @var int
	 */
	const OK = 0x00;

	/**
	 * Status memory limit exhausted.
	 *
	 * @var int
	 */
	const MEMORY_LIMIT = 0x01;

	/**
	 * Status time limit exhausted.
	 *
	 * @var int
	 */
	const TIME_LIMIT = 0x02;

	/**
	 * Check the memory limit.
	 *
	 * @var boolean
	 */
	private $check_memory_limit = true;

	/**
	 * Check the execution limit.
	 *
	 * @var boolean
	 */
	private $check_execution_limit = true;

	/**
	 * Initial time.
	 *
	 * @var int
	 */
	private $initial_execution_time = null;

	/**
	 * Maximum execution time.
	 *
	 * @var int
	 */
	private $max_execution_time = null;

	/**
	 * Memory limit
	 *
	 * @var int
	 */
	private $memory_limit = null;

	/**
	 * Bytes
	 *
	 * @var int
	 */
	private $bytes = null;

	/**
	 * Status
	 *
	 * @var int
	 */
	private $status = self::OK;

	/**
	 * Constructor. Accepts check_memory_limit and check_execution_limit options, both enabled by default.
	 *
	 * @param array $params
	 */
	public function __construct( $params = array() ) {
		if ( isset( $params['check_memory_limit'] ) ) {
			$this->check_memory_limit = boolval( $params['check_memory_limit'] );
		}
		if ( isset( $params['check_execution_limit'] ) ) {
			$this->check_execution_limit = boolval( $params['check_execution_limit'] );
		}
	}

	/**
	 * Start the guardian's watch.
	 */
	public function start() {
		$this->status = self::OK;

		if ( $this->check_memory_limit ) {
			$this->bytes = memory_get_peak_usage( true );
			$this->memory_limit = ini_get( 'memory_limit' );
			$matches = null;
			preg_match( '/([0-9]+)(.)/', $this->memory_limit, $matches );
			if ( isset( $matches[2] ) ) {
				$exp = array( 'K' => 1, 'M' => 2, 'G' => 3, 'T' => 4, 'P' => 5, 'E' => 6 );
				if ( key_exists( $matches[2], $exp ) ) {
					$this->memory_limit = intval( preg_replace( '/[^0-9]/', '', $this->memory_limit ) ) * pow( 1024, $exp[$matches[2]] );
				}
			}
		}

		if ( $this->check_execution_limit ) {
			$this->max_execution_time = intval( ini_get( 'max_execution_time' ) );

			if ( $this->max_execution_time === 0 ) {
				$this->max_execution_time = PHP_INT_MAX;
			}
			$max_input_time = ini_get( 'max_input_time' );
			if ( $max_input_time !== false ) {
				$max_input_time = intval( $max_input_time );
				switch ( $max_input_time ) {
					case -1 :

						break;
					case 0 :

						$this->max_execution_time = min( $this->max_execution_time, PHP_INT_MAX );
						break;
					default :

						$this->max_execution_time = min( $this->max_execution_time, $max_input_time );
				}
			}

			if ( function_exists( 'getrusage' ) ) {
				$resource_usage = getrusage();
				if ( isset( $resource_usage['ru_utime.tv_sec'] ) ) {
					$this->initial_execution_time = $resource_usage['ru_stime.tv_sec'] + $resource_usage['ru_utime.tv_sec'] + 2;
				}
			}
		}
	}

	/**
	 * Have the guardian check limits.
	 *
	 * @return int
	 */
	public function check() {
		$status = self::OK;

		if ( $this->check_memory_limit ) {
			if ( is_numeric( $this->memory_limit ) ) {
				$old_bytes = $this->bytes;
				$this->bytes     = memory_get_peak_usage( true );
				$remaining = $this->memory_limit - $this->bytes;
				$delta = self::BASE_DELTA;
				if ( $this->bytes > $old_bytes ) {
					$delta += intval( ( $this->bytes - $old_bytes ) * self::DELTA_F );
				}
				if ( $remaining < $delta ) {
					$status = $status | self::MEMORY_LIMIT;
				}
			}
		}

		if ( $this->check_execution_limit ) {
			if ( function_exists( 'getrusage' ) ) {
				$resource_usage = getrusage();
				if ( isset( $resource_usage['ru_utime.tv_sec'] ) ) {
					$execution_time = $resource_usage['ru_stime.tv_sec'] + $resource_usage['ru_utime.tv_sec'] + 2;
					$d = ceil( $execution_time - $this->initial_execution_time );
					if ( intval( $d * self::DELTA_F ) > ( $this->max_execution_time - $d ) ) {
						$status = $status | self::TIME_LIMIT;
					}
				}
			}
		}
		$this->status = $this->status | $status;
		return $status;
	}

	/**
	 * Status report.
	 *
	 * @return int
	 */
	public function get_status() {
		$this->check();
		return $this->status;
	}

	/**
	 * Check whether status is ok.
	 *
	 * @return boolean
	 */
	public function is_ok() {
		$this->check();
		return $this->status === self::OK;
	}

	/**
	 * Check whether memory limit is exhausted.
	 *
	 * @return boolean
	 */
	public function is_memory_limit() {
		$this->check();
		return $this->status & self::MEMORY_LIMIT === self::MEMORY_LIMIT;
	}

	/**
	 * Check whether time limit is exhausted.
	 *
	 * @return boolean
	 */
	public function is_time_limit() {
		$this->check();
		return $this->status & self::TIME_LIMIT === self::TIME_LIMIT;
	}
}

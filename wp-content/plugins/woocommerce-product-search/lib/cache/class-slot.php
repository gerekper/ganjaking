<?php
/**
 * class-cache-base.php
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
 * Data slots
 */
class Slot {

	const PATH = '.slotlock';

	const HIT = 'hit';

	const MISS = 'miss';

	const RATIO = 'ratio';

	const WAR = 'war';

	const MIN_SLOTS = 2;

	private $slots = 60;

	private $data = array();

	private $lock = null;

	/**
	 * Create a new slot instance
	 *
	 * @param int $slots number of slots
	 * @param string $path path to file to use as storage
	 */
	public function __construct( $slots = null, $path = null ) {

		if ( !is_string( $path ) ) {
			$path = null;
		}
		if ( $path === null ) {
			$path = self::PATH;
		}

		if ( $slots !== null && is_numeric( $slots ) ) {
			$slots = max( self::MIN_SLOTS, intval( $slots ) );
		}

		$this->init_data();

		try {

			$this->lock = new Lock( $path, false );
		} catch ( Lock_Exception $e ) {
			$this->lock = null;
		}

	}

	/**
	 * Initialize slot data.
	 */
	private function init_data() {
		for ( $i = 0; $i < $this->slots; $i++ ) {
			$this->data[$i] = $this->get_slot_init();
		}
	}

	/**
	 * Get initial data for a slot.
	 *
	 * @return array
	 */
	private function get_slot_init() {
		return array( self::HIT => 0, self::MISS => 0, self::RATIO => 0.0, self::WAR => 0.0 );
	}

	/**
	 * Read slot data from storage.
	 */
	public function read_data() {
		if ( $this->lock !== null ) {
			if ( $this->lock->reader() ) {
				$contents = file_get_contents( $this->lock->get_path() );
				$data = json_decode( $contents, true );
				$this->data = $data;
				if ( !is_array( $this->data ) ) {
					$this->init_data();
				}
			}
			$this->lock->release();
		}
	}

	/**
	 * Provide current slot data.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Provide the current WAR.
	 *
	 * @return float
	 */
	public function get_current_war() {
		$war = 0.0;
		if ( $this->lock !== null ) {
			if ( $this->lock->reader() ) {
				$contents = file_get_contents( $this->lock->get_path() );
				$data = json_decode( $contents, true );
				$this->data = $data;
				if ( !is_array( $this->data ) ) {
					$this->init_data();
				}
				$time = time();
				$slot = $time % $this->slots;
				if (
					array_key_exists( $slot, $this->data ) &&
					is_array( $this->data[$slot] ) &&
					array_key_exists( self::HIT, $this->data[$slot] ) &&
					array_key_exists( self::MISS, $this->data[$slot] ) &&
					array_key_exists( self::RATIO, $this->data[$slot] )
				) {
					$war = $this->data[$slot][self::WAR];
				}
			}
			$this->lock->release();
		}
		return $war;
	}

	/**
	 * Push to slot data.
	 *
	 * @param boolean $success
	 */
	public function push( $success ) {

		$success = boolval( $success );
		if ( $this->lock !== null ) {
			if ( $this->lock->writer() ) {
				$contents = file_get_contents( $this->lock->get_path() );
				$data = json_decode( $contents, true );
				$this->data = $data;
				if ( !is_array( $this->data ) ) {
					$this->init_data();
				}

				$time = time();
				$slot = $time % $this->slots;

				if ( !(
					array_key_exists( $slot, $this->data ) &&
					is_array( $this->data[$slot] ) &&
					array_key_exists( self::HIT, $this->data[$slot] ) &&
					array_key_exists( self::MISS, $this->data[$slot] ) &&
					array_key_exists( self::RATIO, $this->data[$slot] )
				) ) {
					$this->data[$slot] = $this->get_slot_init();
				}

				if ( $success ) {
					$this->data[$slot][self::HIT]++;
				} else {
					$this->data[$slot][self::MISS]++;
				}
				$total = $this->data[$slot][self::HIT] + $this->data[$slot][self::MISS];
				$ratio = 1.0;
				if ( $total > 0 ) {
					$ratio = $this->data[$slot][self::MISS] / $total;
				}
				$this->data[$slot][self::RATIO] = round( $ratio, 6 );

				$divisor = 0;
				$war = 0;
				for ( $i = 0; $i < $this->slots; $i++ ) {
					$k_slot = ( $time - $i ) % $this->slots;
					if ( array_key_exists( $k_slot, $this->data ) ) {
						$values = $this->data[$k_slot];
						if ( $values[self::HIT] > 0 || $values[self::MISS] > 0 ) {
							$f = 1 / ( $i + 1 );
							$divisor += $f;
							$war += $values[self::RATIO] * $f;
						}
					}
				}
				if ( $divisor > 0 ) {
					$this->data[$slot][self::WAR] = round( $war / $divisor, 6 );
				} else {
					$this->data[$slot][self::WAR] = 0.0;
				}

				$next_slot = ( $time + 1 ) % $this->slots;
				$this->data[$next_slot] = $this->get_slot_init();

				$contents = json_encode( $this->data );
				file_put_contents( $this->lock->get_path(), $contents );
			}
			$this->lock->release();
		}
	}

}

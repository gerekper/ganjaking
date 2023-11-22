<?php
/**
 * class-matrix.php
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
 * Processing matrix.
 *
 */
class Matrix {

	/**
	 * Stage counter
	 *
	 * @var int
	 */
	private $stage = 0;

	/**
	 * Matrix data
	 *
	 * @var array
	 */
	private $matrix = array();

	/**
	 * Matrix constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ) {

	}

	/**
	 * Increment stage counter.
	 */
	public function inc_stage() {

		$this->stage++;
	}

	/**
	 * Current stage.
	 *
	 * @return int
	 */
	public function get_stage() {
		return $this->stage;
	}

	/**
	 * Increment counter.
	 *
	 * @param int $id
	 */
	public function inc( $id ) {

		if ( $this->stage <= 1 ) {

			$this->matrix[$id] = 1;
		} else {
			if ( isset( $this->matrix[$id] ) ) {

				if ( $this->matrix[$id] < $this->stage ) {
					$this->matrix[$id]++;
				}
			}
		}
	}

	/**
	 * Evaluate the matrix.
	 */
	public function evaluate() {
		if ( $this->stage > 1 ) {
			foreach ( $this->matrix as $id => $count ) {
				if ( $count < $this->stage ) {
					unset( $this->matrix[$id] );
				}
			}
		}
	}

	/**
	 * Purge the matrix.
	 */
	public function purge() {
		$this->matrix = array();
	}

	/**
	 * Provide the matrix.
	 *
	 * @return array|mixed[]
	 */
	public function get() {
		return $this->matrix;
	}

	/**
	 * Provide IDs in the matrix.
	 *
	 * @return int[]
	 */
	public function get_ids() {
		return array_keys( $this->matrix );
	}

	/**
	 * Arrange the matrix.
	 *
	 * @param int[] $ids
	 */
	public function arrange( &$ids ) {

		$matrix = array();
		foreach ( $ids as $id ) {
			if ( isset( $this->matrix[$id] ) ) {
				$matrix[$id] = $this->matrix[$id];
			}
		}
		$this->matrix = $matrix;
	}
}

<?php

/**
 * WC_Boxpack_Item class.
 */
class WC_Boxpack_Item {

	public $weight;
	public $height;
	public $width;
	public $length;
	public $volume;
	public $value;
	public $meta;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct( $length, $width, $height, $weight, $value = '', $meta = array() ) {
		$dimensions = array( $length, $width, $height );

		sort( $dimensions );

		$this->length = floatval( $dimensions[2] );
		$this->width  = floatval( $dimensions[1] );
		$this->height = floatval( $dimensions[0] );

		$this->volume = floatval( $width * $height * $length );
		$this->weight = floatval( $weight );
		$this->value  = $value;
		$this->meta   = $meta;
	}

	/**
	 * get_volume function.
	 *
	 * @access public
	 * @return void
	 */
	function get_volume() {
		return $this->volume;
	}

	/**
	 * get_height function.
	 *
	 * @access public
	 * @return void
	 */
	function get_height() {
		return $this->height;
	}

	/**
	 * get_width function.
	 *
	 * @access public
	 * @return void
	 */
	function get_width() {
		return $this->width;
	}

	/**
	 * get_width function.
	 *
	 * @access public
	 * @return void
	 */
	function get_length() {
		return $this->length;
	}

	/**
	 * get_width function.
	 *
	 * @access public
	 * @return void
	 */
	function get_weight() {
		return $this->weight;
	}

	/**
	 * get_value function.
	 *
	 * @access public
	 * @return void
	 */
	function get_value() {
		return $this->value;
	}

	/**
	 * get_meta function.
	 *
	 * @access public
	 * @return void
	 */
	function get_meta( $key = '' ) {
		if ( $key ) {
			if ( isset( $this->meta[ $key ] ) ) {
				return $this->meta[ $key ];
			} else {
				return null;
			}
		} else {
			return array_filter( (array) $this->meta );
		}
	}
}

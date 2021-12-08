<?php

class WC_Swatches_Attribute_Configuration_Object {

	public $_attribute_name;
	public $_swatch_options;
	public $_product;

	/**
	 *
	 * @param WC_Product $product
	 * @param string     $attribute The name of the attribute.
	 */
	public function __construct( $product, $attribute ) {
		$this->_attribute_name = $attribute;
		$this->_product        = $product;

		$swatch_options = maybe_unserialize( $product->get_meta( '_swatch_type_options', true ) );

		if ( ! empty( $swatch_options ) ) {

			$st_name     = sanitize_title( $attribute );
			$hashed_name = md5( $st_name );
			$lookup_name = '';

			//Normalize the key we use, this is for backwards compatibility.
			if ( isset( $swatch_options[ $hashed_name ] ) ) {
				$lookup_name = $hashed_name;
			} elseif ( isset( $swatch_options[ $st_name ] ) ) {
				$lookup_name = $st_name;
			}


			$size = 'swatches_image_size';
			//If the post has a default size configured for it.
			//This was done for CSV import suite, there is no UI for selecting this on the post directly.
			$product_configured_size = $product->get_meta( '_swatch_size', true );
			if ( $product_configured_size ) {
				$size = 'swatches_image_size';
			}

			if ( isset( $swatch_options[ $lookup_name ] ) ) {
				$this->_swatch_options = $swatch_options[ $lookup_name ];
			} else {
				$this->_swatch_options = array();
			}
		}
	}

	public function get_attribute_name() {
		return $this->_attribute_name;
	}

	/**
	 * Returns the type of input to display.
	 */
	public function get_type() {
		$type = apply_filters( 'woocommerce_swatches_type_for_product', $this->sg( 'type', 'default' ), $this->_product->get_id(), $this->_attribute_name );

		return $type;
	}

	public function get_size() {
		$size = apply_filters( 'woocommerce_swatches_size_for_product', $this->sg( 'size' ), $this->_product->get_id(), $this->_attribute_name );

		return $size;
	}

	public function get_label_layout() {
		return apply_filters( 'wc_swatches_and_photos_label_get_layout', $this->sg( 'layout', 'default' ), $this->get_attribute_name(), $this->_swatch_options, $this );
	}

	public function get_options() {
		return $this->_swatch_options;
	}

	/**
	 * Safely get a configuration value from the swatch options.
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	private function sg( $key, $default = null ) {
		return isset( $this->_swatch_options[ $key ] ) ? $this->_swatch_options[ $key ] : $default;
	}

}

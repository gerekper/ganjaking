<?php
/**
 * Abstract data class which includes shipping methods.
 *
 * @package WC_OD/Abstracts
 * @since   1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_OD_Shipping_Methods_Data class.
 */
class WC_OD_Shipping_Methods_Data extends WC_OD_Data {

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param array $data The object data.
	 */
	public function __construct( array $data = array() ) {
		if ( empty( $data['shipping_methods_option'] ) ) {
			$data['shipping_methods_option'] = ( ! empty( $data['shipping_methods'] ) ? 'specific' : '' );
		}

		$this->data = array_merge( $this->data, $this->get_default_shipping_method_data() );

		parent::__construct( $data );
	}

	/**
	 * Gets the default shipping methods data.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	protected function get_default_shipping_method_data() {
		return array(
			'shipping_methods_option' => '',
			'shipping_methods'        => array(),
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Gets the shipping methods option.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public function get_shipping_methods_option() {
		return $this->get_prop( 'shipping_methods_option' );
	}

	/**
	 * Gets the shipping methods.
	 *
	 * @since 1.6.0
	 *
	 * @param string $return Optional. The way for returning the shipping methods. Default empty. Accepts 'extended'.
	 * @return array
	 */
	public function get_shipping_methods( $return = '' ) {
		$shipping_methods = $this->get_prop( 'shipping_methods' );

		if ( 'expanded' === $return ) {
			$shipping_methods = wc_od_expand_shipping_methods( $shipping_methods );
		}

		return $shipping_methods;
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Sets the shipping methods option.
	 *
	 * Allowed values: [
	 *    '',            // All the shipping methods.
	 *    'all_except',  // All the shipping methods, except...
	 *    'specific'     // Only the specified shipping methods.
	 * ]
	 *
	 * @since 1.6.0
	 *
	 * @param string $option The shipping methods option.
	 */
	public function set_shipping_methods_option( $option ) {
		if ( ! in_array( $option, array( '', 'all_except', 'specific' ), true ) ) {
			$option = '';
		}

		$this->set_prop( 'shipping_methods_option', $option );
	}

	/**
	 * Sets the shipping methods.
	 *
	 * @since 1.6.0
	 *
	 * @param array $shipping_methods The shipping methods.
	 */
	public function set_shipping_methods( array $shipping_methods ) {
		$this->set_prop( 'shipping_methods', $shipping_methods );

		$shipping_methods = $this->get_shipping_methods();

		// Synchronize the shipping methods option.
		if ( empty( $shipping_methods ) ) {
			$this->set_shipping_methods_option( '' );
		} elseif ( ! $this->get_shipping_methods_option() ) {
			$this->set_shipping_methods_option( 'specific' );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Other Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Gets if there are shipping methods defined or not.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public function has_shipping_methods() {
		$shipping_methods = $this->get_shipping_methods();

		return ! empty( $shipping_methods );
	}

	/**
	 * Gets if a shipping method is valid or not.
	 *
	 * @since 1.6.0
	 *
	 * @param string $shipping_method The shipping method to validate.
	 * @return bool
	 */
	public function validate_shipping_method( $shipping_method ) {
		$valid = true;

		$shipping_methods_option = $this->get_shipping_methods_option();

		if ( $shipping_methods_option ) {
			$shipping_methods = $this->get_shipping_methods( 'expanded' );
			$in_array         = in_array( $shipping_method, $shipping_methods, true );

			if (
				( 'specific' === $shipping_methods_option && ! $in_array ) ||
				( 'all_except' === $shipping_methods_option && $in_array )
			) {
				$valid = false;
			}
		}

		return $valid;
	}
}

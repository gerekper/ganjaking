<?php
/**
 * Data: Shipping Methods.
 *
 * @package WC_OD/Traits
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trait for extending the class WC_Data with shipping methods' properties.
 */
trait WC_OD_Data_Shipping_Methods {

	/**
	 * Gets the default shipping methods data properties.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_default_shipping_methods_data() {
		return array(
			'shipping_methods_option' => '',
			'shipping_methods'        => array(),
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Methods for getting the shipping methods data.
	|
	*/

	/**
	 * Gets the shipping methods option.
	 *
	 * @since 1.6.0
	 * @since 2.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_shipping_methods_option( $context = 'view' ) {
		return $this->get_prop( 'shipping_methods_option', $context );
	}

	/**
	 * Gets the shipping methods.
	 *
	 * @since 1.6.0
	 * @since 2.0.0 Replaced parameter `$return` by `$context`. Deprecated option 'expanded'.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return array
	 */
	public function get_shipping_methods( $context = 'view' ) {
		$shipping_methods = $this->get_prop( 'shipping_methods', $context );

		if ( 'expanded' === $context ) {
			wc_doing_it_wrong( __FUNCTION__, "The option 'expanded' is deprecated. Use wc_od_expand_shipping_methods() instead.", '2.0.0' );
			$shipping_methods = wc_od_expand_shipping_methods( $shipping_methods );
		}

		return $shipping_methods;
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Methods for setting the shipping methods data. These should not update
	| anything in the database itself and should only change what is stored in
	| the class object.
	|
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

		// Synchronize the 'shipping_methods' property.
		if ( ! $option ) {
			$shipping_methods = $this->get_shipping_methods();

			if ( ! empty( $shipping_methods ) ) {
				$this->set_shipping_methods( array() );
			}
		}
	}

	/**
	 * Sets the shipping methods.
	 *
	 * @since 1.6.0
	 *
	 * @param array $shipping_methods The shipping methods.
	 */
	public function set_shipping_methods( $shipping_methods ) {
		if ( ! is_array( $shipping_methods ) ) {
			$shipping_methods = array();
		}

		$this->set_prop( 'shipping_methods', $shipping_methods );

		// Synchronize the 'shipping_methods_option' property.
		$option = $this->get_shipping_methods_option();

		if ( empty( $shipping_methods ) && $option ) {
			$this->set_shipping_methods_option( '' );
		} elseif ( ! empty( $shipping_methods ) && ! $option ) {
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
		$shipping_methods_option = $this->get_shipping_methods_option();

		// No shipping methods restrictions.
		if ( ! $shipping_methods_option ) {
			return true;
		}

		// Check if the shipping method is listed.
		$shipping_methods = $this->get_shipping_methods();
		$in_array         = in_array( $shipping_method, $shipping_methods, true );

		if ( ! $in_array ) {
			$parts = explode( ':', $shipping_method );

			// Check if the shipping method without the rate ID is listed.
			if ( 2 < count( $parts ) ) {
				$in_array = in_array( "$parts[0]:$parts[1]", $shipping_methods, true );
			}

			// Check if the shipping zone is listed.
			if ( ! $in_array ) {
				$shipping_zone = WC_Shipping_Zones::get_zone_by( 'instance_id', $parts[1] );

				if ( $shipping_zone ) {
					$in_array = in_array( 'zone:' . $shipping_zone->get_id(), $shipping_methods, true );
				}
			}
		}

		return (
			( $in_array && 'specific' === $shipping_methods_option ) ||
			( ! $in_array && 'all_except' === $shipping_methods_option )
		);
	}
}

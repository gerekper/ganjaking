<?php
/**
 * Data: Fee
 *
 * @package WC_OD/Traits
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trait for extending the class WC_Data with fee properties.
 */
trait WC_OD_Data_Fee {

	/**
	 * Gets the default fee data properties.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_default_fee_data() {
		return array(
			'fee_amount'     => '',
			'fee_label'      => '',
			'fee_tax_status' => 'none',
			'fee_tax_class'  => '',
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Methods for getting the fee data.
	|
	*/

	/**
	 * Gets the fee amount.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_fee_amount( $context = 'view' ) {
		return $this->get_prop( 'fee_amount', $context );
	}

	/**
	 * Gets the fee label.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_fee_label( $context = 'view' ) {
		return $this->get_prop( 'fee_label', $context );
	}

	/**
	 * Gets the fee tax status.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_fee_tax_status( $context = 'view' ) {
		return $this->get_prop( 'fee_tax_status', $context );
	}

	/**
	 * Gets the fee tax class.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_fee_tax_class( $context = 'view' ) {
		return $this->get_prop( 'fee_tax_class', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Methods for setting the fee data. These should not update
	| anything in the database itself and should only change what is stored in
	| the class object.
	|
	*/

	/**
	 * Sets the fee amount.
	 *
	 * @since 2.0.0
	 *
	 * @param string $amount Fee amount.
	 */
	public function set_fee_amount( $amount ) {
		$this->set_prop( 'fee_amount', wc_format_decimal( $amount ) );
	}

	/**
	 * Sets the fee label.
	 *
	 * @since 2.0.0
	 *
	 * @param string $label Fee label.
	 */
	public function set_fee_label( $label ) {
		$this->set_prop( 'fee_label', $label );
	}

	/**
	 * Sets the fee tax status.
	 *
	 * @since 2.0.0
	 *
	 * @param string $status Tax status.
	 */
	public function set_fee_tax_status( $status ) {
		$this->set_prop( 'fee_tax_status', $status );
	}

	/**
	 * Sets the fee tax class.
	 *
	 * @since 2.0.0
	 *
	 * @param string $class Tax class.
	 */
	public function set_fee_tax_class( $class ) {
		$class = sanitize_title( $class );
		$class = 'standard' === $class ? '' : $class;

		if ( ! in_array( $class, $this->get_valid_fee_tax_classes(), true ) ) {
			$class = '';
		}

		$this->set_prop( 'fee_tax_class', $class );
	}

	/**
	 * Gets an array of valid tax classes.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_valid_fee_tax_classes() {
		return WC_Tax::get_tax_class_slugs();
	}
}

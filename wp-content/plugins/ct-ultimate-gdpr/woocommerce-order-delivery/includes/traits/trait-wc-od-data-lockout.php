<?php
/**
 * Data: Lockout.
 *
 * @package WC_OD/Traits
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trait for extending the class WC_Data with lockout properties.
 */
trait WC_OD_Data_Lockout {

	/**
	 * Gets the default lockout data properties.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_default_lockout_data() {
		return array(
			'number_of_orders' => 0,
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Methods for getting the lockout data.
	|
	*/

	/**
	 * Gets the number of orders allowed before locking out the delivery.
	 *
	 * @since 1.8.0
	 * @since 2.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return int
	 */
	public function get_number_of_orders( $context = 'view' ) {
		return $this->get_prop( 'number_of_orders', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Methods for setting the lockout data. These should not update
	| anything in the database itself and should only change what is stored in
	| the class object.
	|
	*/

	/**
	 * Sets the number of orders allowed before locking out the delivery.
	 *
	 * @since 1.8.0
	 *
	 * @param int $number Number of orders. 0 means no limit.
	 */
	public function set_number_of_orders( $number ) {
		$this->set_prop( 'number_of_orders', absint( $number ) );
	}
}

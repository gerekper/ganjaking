<?php

namespace ACA\WC\Settings\User;

use AC;
use AC\View;

/**
 * @since 3.1
 */
class Country extends AC\Settings\Column
	implements AC\Settings\FormatValue {

	/**
	 * @var string
	 */
	private $address_type;

	public function format( $country_code, $original_value ) {
		$countries = WC()->countries->get_countries();

		if ( ! isset( $countries[ $country_code ] ) ) {
			return false;
		}

		return $countries[ $country_code ];
	}

	protected function define_options() {
		return [
			'address_type' => 'billing',
		];
	}

	protected function get_display_options() {
		$options = [
			'shipping' => __( 'Shipping', 'codepress-admin-columns' ),
			'billing'  => __( 'Billing', 'codepress-admin-columns' ),
		];

		return $options;
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_attribute( 'data-refresh', 'column' )
		               ->set_options( $this->get_display_options() );

		return new View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $select,
		] );
	}

	/**
	 * @return string
	 */
	public function get_address_type() {
		return $this->address_type;
	}

	/**
	 * @param string $address_type
	 */
	public function set_address_type( $address_type ) {
		$this->address_type = $address_type;
	}

}
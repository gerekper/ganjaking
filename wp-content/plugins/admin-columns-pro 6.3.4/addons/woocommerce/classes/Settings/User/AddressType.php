<?php

namespace ACA\WC\Settings\User;

use AC;
use AC\View;
use ACA\WC\Settings;

/**
 * @since 3.0.4
 */
class AddressType extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $address_type;

	protected function set_name() {
		$this->name = 'address_type';
	}

	protected function define_options() {
		return [
			'address_type' => 'billing',
		];
	}

	public function get_dependent_settings() {
		$settings = [];

		switch ( $this->get_address_type() ) {
			case 'shipping':
				$settings[] = new Settings\Address( $this->column );
				break;
			case 'billing':
				$settings[] = new Settings\Address\Billing( $this->column );
				break;

		}

		return $settings;
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

	protected function get_display_options() {
		return [
			'shipping' => __( 'Shipping', 'codepress-admin-columns' ),
			'billing'  => __( 'Billing', 'codepress-admin-columns' ),
		];
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
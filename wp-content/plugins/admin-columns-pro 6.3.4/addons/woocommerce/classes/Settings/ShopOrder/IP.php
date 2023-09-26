<?php

namespace ACA\WC\Settings\ShopOrder;

use AC;

/**
 * @since 3.0
 */
class IP extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $ip_property;

	protected function set_name() {
		$this->name = 'ip_property';
	}

	protected function define_options() {
		return [
			'ip_property' => 'ip',
		];
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_attribute( 'data-refresh', 'column' )
		               ->set_options( $this->get_display_options() );

		return new AC\View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $select,
		] );
	}

	protected function get_display_options() {
		return [
			'ip'      => __( 'IP Address', 'codepress-admin-columns' ),
			'country' => __( 'IP Country Code', 'codepress-admin-columns' ),
		];
	}

	/**
	 * @return string
	 */
	public function get_ip_property() {
		return $this->ip_property;
	}

	/**
	 * @param string $ip_property
	 */
	public function set_ip_property( $ip_property ) {
		$this->ip_property = $ip_property;
	}

}
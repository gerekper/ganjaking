<?php

namespace ACA\WC\Settings\ShopOrder;

use AC;

class ShippingMethodType extends AC\Settings\Column {

	const NAME = 'shipping_method_type';
	const METHOD_TITLE = 'method_title';
	const METHOD_ID = 'method_id';

	/**
	 * @var string
	 */
	private $shipping_method_type;

	protected function set_name() {
		$this->name = self::NAME;
	}

	protected function define_options() {
		return [
			self::NAME => self::METHOD_TITLE,
		];
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_options( [
			               self::METHOD_TITLE => __( 'Method Label', 'codepress-admin-columns' ),
			               self::METHOD_ID    => __( 'Method Type', 'codepress-admin-columns' ),
		               ] );

		return new AC\View( [
			'label'   => __( 'Shipping Method Type', 'codepress-admin-columns' ),
			'setting' => $select,
		] );
	}

	/**
	 * @return string
	 */
	public function get_shipping_method_type() {
		return $this->shipping_method_type;
	}

	/**
	 * @param string $shipping_method_type
	 */
	public function set_shipping_method_type( $shipping_method_type ) {
		$this->shipping_method_type = $shipping_method_type;
	}

}
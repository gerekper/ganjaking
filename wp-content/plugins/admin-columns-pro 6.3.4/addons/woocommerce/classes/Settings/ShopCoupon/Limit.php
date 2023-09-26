<?php

namespace ACA\WC\Settings\ShopCoupon;

use AC;
use AC\View;

/**
 * @since 3.0.3
 */
class Limit extends AC\Settings\Column {

	/**
	 * @var string
	 */
	private $coupon_limit;

	protected function set_name() {
		$this->name = 'coupon_limit';
	}

	protected function define_options() {
		return [
			'coupon_limit' => 'usage_limit',
		];
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
		$options = [
			'usage_limit'          => __( 'Usage limit per coupon', 'woocommerce' ),
			'usage_limit_per_user' => __( 'Usage limit per user', 'woocommerce' ),
		];

		return $options;
	}

	/**
	 * @return string
	 */
	public function get_coupon_limit() {
		return $this->coupon_limit;
	}

	/**
	 * @param string $coupon_limit
	 */
	public function set_coupon_limit( $coupon_limit ) {
		$this->coupon_limit = $coupon_limit;
	}

}
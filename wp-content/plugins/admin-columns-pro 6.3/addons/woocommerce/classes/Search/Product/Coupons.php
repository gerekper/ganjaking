<?php

namespace ACA\WC\Search\Product;

use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class Coupons extends Comparison {

	public function __construct() {
		$operators = new Operators( [
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;
		$ids = implode( ',', $this->get_products_with_coupon_applied() );

		if ( Operators::NOT_IS_EMPTY === $operator ) {
			$where = $wpdb->posts . '.ID IN( ' . $ids . ' )';
		} else {
			$where = $wpdb->posts . '.ID NOT IN( ' . $ids . ' )';
		}

		$bindings = new Bindings();
		$bindings->where( $where );

		return $bindings;
	}

	/**
	 * @return array
	 */
	private function get_products_with_coupon_applied() {
		$coupons = get_posts( [
			'post_type'  => 'shop_coupon',
			'fields'     => 'ids',
			'meta_query' => [
				[
					'key'     => 'product_ids',
					'value'   => '',
					'compare' => '!=',
				],
			],
		] );

		if ( ! $coupons ) {
			return [];
		}

		$products = [];

		foreach ( $coupons as $coupon_id ) {
			$product_ids = explode( ',', get_post_meta( $coupon_id, 'product_ids', true ) );

			foreach ( $product_ids as $_id ) {
				$products[] = $_id;
			}
		}

		return array_unique( $products );
	}

}
<?php

namespace ACA\WC\Filtering\Product;

use ACP;

/**
 * @since 3.0
 */
class Coupons extends ACP\Filtering\Model {

	public function get_filtering_vars( $vars ) {
		$product_ids = $this->get_products_with_coupon_applied();

		if ( ! empty( $product_ids ) ) {

			switch ( $this->get_filter_value() ) {
				case 'cpac_nonempty':
					$vars['post__in'] = $product_ids;

					break;
				case 'cpac_empty':

					$vars['post__not_in'] = $product_ids;
					break;
			}
		}

		return $vars;
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

	public function get_filtering_data() {
		return [
			'empty_option' => $this->get_empty_labels( __( 'Coupons Applied', 'woocommerce' ) ),
		];
	}

}
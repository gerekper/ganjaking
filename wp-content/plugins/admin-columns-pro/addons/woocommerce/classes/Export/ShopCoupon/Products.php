<?php

namespace ACA\WC\Export\ShopCoupon;

use ACP;
use WC_Coupon;

/**
 * WooCommerce coupon product IDs (default column) exportability model
 * @since 2.2.1
 */
class Products extends ACP\Export\Model {

	public function get_value( $id ) {
		$coupon = new WC_Coupon( $id );
		$product_ids = $coupon->get_product_ids();

		if ( count( $product_ids ) < 1 ) {
			return '';
		}

		return implode( ', ', $product_ids );
	}

}

<?php
declare( strict_types=1 );

namespace ACA\WC\Editing\Storage\Coupon;

use ACP\Editing\Storage;
use WC_Coupon;

class ExcludedProductCategories implements Storage {

	public function get( int $id ) {
		return ( new WC_Coupon( $id ) )->get_excluded_product_categories();
	}

	public function update( int $id, $data ): bool {
		$term_ids = $data && is_array( $data )
			? array_filter( $data, 'is_numeric' )
			: [];

		$coupon = new WC_Coupon( $id );
		$coupon->set_excluded_product_categories( $term_ids );

		return $coupon->save() > 0;
	}

}
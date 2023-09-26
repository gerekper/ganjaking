<?php
declare( strict_types=1 );

namespace ACA\WC\Editing\Storage\Coupon;

use ACP\Editing\Storage;
use WC_Coupon;

class IncludedProducts implements Storage {

	public function get( int $id ) {
		return ( new WC_Coupon( $id ) )->get_product_ids();
	}

	public function update( int $id, $data ): bool {
		$ids = $data && is_array( $data )
			? array_filter( $data, 'is_numeric' )
			: [];

		$coupon = new WC_Coupon( $id );
		$coupon->set_product_ids( $ids );

		return $coupon->save() > 0;
	}

}
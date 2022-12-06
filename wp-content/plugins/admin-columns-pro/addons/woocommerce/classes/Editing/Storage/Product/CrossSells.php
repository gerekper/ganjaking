<?php
declare( strict_types=1 );

namespace ACA\WC\Editing\Storage\Product;

use ACP\Editing\Storage;

class CrossSells implements Storage {

	public function get( int $id ) {
		return wc_get_product( $id )->get_cross_sell_ids();
	}

	public function update( int $id, $data ): bool {
		$ids = $data && is_array( $data )
			? array_filter( $data, 'is_numeric' )
			: [];

		$product = wc_get_product( $id );
		$product->set_cross_sell_ids( $ids );

		return $product->save() > 0;
	}

}
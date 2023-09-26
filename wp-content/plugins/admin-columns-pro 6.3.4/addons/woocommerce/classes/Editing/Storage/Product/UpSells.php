<?php
declare( strict_types=1 );

namespace ACA\WC\Editing\Storage\Product;

use ACP\Editing\Storage;

class UpSells implements Storage {

	public function get( int $id ) {
		return wc_get_product( $id )->get_upsell_ids();
	}

	public function update( int $id, $data ): bool {
		$ids = $data && is_array( $data )
			? array_filter( $data, 'is_numeric' )
			: [];

		$product = wc_get_product( $id );
		$product->set_upsell_ids( $ids );

		return $product->save() > 0;
	}

}
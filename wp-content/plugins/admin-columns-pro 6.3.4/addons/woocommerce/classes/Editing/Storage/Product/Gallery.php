<?php

namespace ACA\WC\Editing\Storage\Product;

use ACP\Editing\Storage;

class Gallery implements Storage {

	public function get( int $id ) {
		$product = wc_get_product( $id );

		return $product
			? $product->get_gallery_image_ids()
			: false;
	}

	public function update( int $id, $data ): bool {
		$product = wc_get_product( $id );
		$product->set_gallery_image_ids( $data );

		return $product->save() > 0;
	}

}
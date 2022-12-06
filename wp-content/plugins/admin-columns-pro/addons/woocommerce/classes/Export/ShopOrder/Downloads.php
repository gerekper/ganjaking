<?php

namespace ACA\WC\Export\ShopOrder;

use ACP;

/**
 * @since 3.0
 */
class Downloads extends ACP\Export\Model {

	public function get_value( $id ) {
		$values = [];

		foreach ( $this->column->get_raw_value( $id ) as $download ) {
			$product = wc_get_product( $download['product_id'] );

			$values[] = $product->get_file_download_path( $download['download_id'] );
		}

		if ( empty( $values ) ) {
			return false;
		}

		return implode( ', ', $values );
	}

}
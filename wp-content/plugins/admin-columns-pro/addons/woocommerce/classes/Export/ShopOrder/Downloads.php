<?php

namespace ACA\WC\Export\ShopOrder;

use AC\Column;
use ACP;

class Downloads implements ACP\Export\Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$values = [];

		foreach ( $this->column->get_raw_value( $id ) as $download ) {
			$product = wc_get_product( $download['product_id'] );

			if ( ! $product ) {
				continue;
			}

			$values[] = $product->get_file_download_path( $download['download_id'] );
		}

		if ( empty( $values ) ) {
			return false;
		}

		return implode( ', ', $values );
	}

}
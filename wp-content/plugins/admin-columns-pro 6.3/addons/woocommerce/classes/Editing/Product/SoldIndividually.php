<?php

namespace ACA\WC\Editing\Product;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACA\WC\Editing\Storage;
use ACP;
use ACP\Editing\View;

class SoldIndividually implements ACP\Editing\Service {

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\Toggle(
			new ToggleOptions(
				new Option( 'yes' ), new Option( 'no' )
			)
		);
	}

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		return $product->get_sold_individually() ? 'yes' : 'no';
	}

	public function update( int $id, $data ): void {
		$product = wc_get_product( $id );
		$product->set_sold_individually( $data );
		$product->save();
	}

}
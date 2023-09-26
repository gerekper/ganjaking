<?php

namespace ACA\WC\Editing\ProductVariation;

use ACP;
use ACP\Editing\View;

class Weight implements ACP\Editing\Service {

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		return $product ? $product->get_weight() : false;
	}

	public function get_view( string $context ): ?View {
		$view = new ACP\Editing\View\Number();

		return $view->set_step( 'any' )->set_min( 0 );
	}

	public function update( int $id, $data ): void {
		$product = wc_get_product( $id );
		$product->set_weight( $data );
		$product->save();
	}

}
<?php

namespace ACA\WC\Editing\Product;

use ACP;
use ACP\Editing\View;
use WC_Tax;

class TaxClass implements ACP\Editing\Service {

	public function get_view( string $context ): ?View {
		$options = [ '' => __( 'Standard', 'codepress-admin-columns' ) ];

		foreach ( WC_Tax::get_tax_classes() as $tax_class ) {
			$options[ WC_Tax::format_tax_rate_class( $tax_class ) ] = $tax_class;
		}

		return new ACP\Editing\View\Select( $options );
	}

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		return $product ? $product->get_tax_class() : false;
	}

	public function update( int $id, $data ): void {
		$product = wc_get_product( $id );
		$product->set_tax_class( $data );
		$product->save();
	}

}
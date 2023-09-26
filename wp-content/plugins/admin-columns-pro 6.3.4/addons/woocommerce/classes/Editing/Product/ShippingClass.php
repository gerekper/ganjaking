<?php

namespace ACA\WC\Editing\Product;

use ACP;
use ACP\Editing\Service;
use ACP\Editing\Service\Editability;
use ACP\Editing\View;

class ShippingClass implements Service, Editability {

	public function is_editable( int $id ): bool {
		return $this->needs_shipping( $id );
	}

	public function get_not_editable_reason( int $id ): string {
		return __( 'Product does not have a shipping class.', 'codepress-admin-columns' );
	}

	public function get_view( string $context ): ?View {
		$options = [ '' => __( 'No shipping class', 'codepress-admin-columns' ) ];
		$shipping_classes = get_terms( [ 'taxonomy' => 'product_shipping_class', 'hide_empty' => false ] );

		foreach ( $shipping_classes as $term ) {
			$options[ $term->term_id ] = $term->name;
		}

		return new ACP\Editing\View\Select( $options );
	}

	private function needs_shipping( $id ): bool {
		$product = wc_get_product( $id );

		return $product && $product->needs_shipping();
	}

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		return $this->needs_shipping( $id )
			? $product->get_shipping_class_id()
			: null;
	}

	public function update( int $id, $data ): void {
		$product = wc_get_product( $id );
		$product->set_shipping_class_id( $data );
		$product->save();
	}

}
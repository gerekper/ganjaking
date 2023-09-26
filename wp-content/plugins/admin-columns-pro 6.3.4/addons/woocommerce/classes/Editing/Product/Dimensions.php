<?php

namespace ACA\WC\Editing\Product;

use ACA\WC\Editing;
use ACP\Editing\Service;
use ACP\Editing\Service\Editability;
use ACP\Editing\View;

class Dimensions implements Service, Editability {

	public function is_editable( int $id ): bool {
		$product = wc_get_product( $id );

		if ( ! $product ) {
			return false;
		}

		return ! $product->is_virtual();
	}

	public function get_not_editable_reason( int $id ): string {
		$product = wc_get_product( $id );

		return sprintf( __( '%s can not be edited.', 'codepress-admin-columns' ), sprintf( '%s "%s"', $this->get_product_type_label( $product->get_type() ), $product->get_name() ) );
	}

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		if ( $product->is_virtual() ) {
			return null;
		}

		return (object) [
			'length' => $product->get_length(),
			'width'  => $product->get_width(),
			'height' => $product->get_height(),
		];
	}

	public function update( int $id, $data ): void {
		if ( ! is_array( $data ) || ( ! isset( $data['length'], $data['width'], $data['height'] ) ) ) {
			return;
		}

		$product = wc_get_product( $id );

		if ( $product->is_virtual() ) {
			return;
		}

		$product->set_length( $data['length'] );
		$product->set_width( $data['width'] );
		$product->set_height( $data['height'] );
		$product->save();
	}

	private function get_product_type_label( $type ) {
		$types = wc_get_product_types();

		return $types[ $type ] ?? $type;
	}

	public function get_view( string $context ): ?View {
		return new Editing\View\Dimensions();
	}

}
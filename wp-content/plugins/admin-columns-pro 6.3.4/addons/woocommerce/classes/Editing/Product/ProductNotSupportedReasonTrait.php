<?php

namespace ACA\WC\Editing\Product;

trait ProductNotSupportedReasonTrait {

	public function get_not_editable_reason( int $id ): string {
		$product = wc_get_product( $id );
		$types = wc_get_product_types();

		$type = $product->get_type();
		$label = $types[ $type ] ?? $type;

		return sprintf(
			__( '%s can not be edited.', 'codepress-admin-columns' ),
			sprintf( '%s "%s"', $label, $product->get_name() )
		);
	}

}
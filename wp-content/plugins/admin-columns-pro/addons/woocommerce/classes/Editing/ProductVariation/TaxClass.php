<?php

namespace ACA\WC\Editing\ProductVariation;

use ACA\WC\Editing\Storage;
use ACP;
use ACP\Editing\View;

class TaxClass implements ACP\Editing\Service {

	/**
	 * @var array
	 */
	private $tax_classes;

	public function __construct( $tax_classes ) {
		$this->tax_classes = $tax_classes;
	}

	public function get_view( string $context ): ?View {
		$options = [ 'parent' => __( 'Use Product Tax Class', 'codepress-admin-columns' ) ];
		$options = array_merge( $options, $this->tax_classes );

		return new ACP\Editing\View\Select( $options );
	}

	public function get_value( $id ) {
		return get_post_meta( $id, '_tax_class', true );
	}

	public function update( int $id, $data ): void {
		$product = wc_get_product( $id );
		$product->set_tax_class( $data );
		$product->save();
	}

}
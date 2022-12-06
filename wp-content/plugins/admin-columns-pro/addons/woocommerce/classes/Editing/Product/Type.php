<?php

namespace ACA\WC\Editing\Product;

use ACA\WC\Editing\Storage;
use ACA\WC\Editing\View;
use ACP\Editing\Service;
use WC_Cache_Helper;

class Type implements Service, Service\Editability {

	use ProductNotSupportedReasonTrait;

	/**
	 * @var array
	 */
	private $simple_product_types;

	public function __construct( array $simple_product_types ) {
		$this->simple_product_types = $simple_product_types;
	}

	public function get_view( string $context ): ?\ACP\Editing\View {
		return new View\Type( $this->simple_product_types );
	}

	public function is_editable( int $id ): bool {
		$product = wc_get_product( $id );

		return ! in_array( $product->get_type(), [ 'subscription', 'variable_subscription' ] );
	}

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		return [
			'type'         => $product->get_type(),
			'virtual'      => $product->is_virtual(),
			'downloadable' => $product->is_downloadable(),
		];
	}

	public function update( int $id, $data ): void {
		if ( isset( $data['type'] ) ) {
			wp_set_object_terms( $id, $data['type'], 'product_type' );
		}

		$cache_key = WC_Cache_Helper::get_cache_prefix( 'product_' . $id ) . '_type_' . $id;

		wp_cache_delete( $cache_key, 'products' );

		$product = wc_get_product( $id );

		if ( isset( $data['downloadable'] ) ) {
			$product->set_downloadable( $data['downloadable'] );
		}

		if ( isset( $data['virtual'] ) ) {
			$product->set_virtual( $data['virtual'] );
		}

		$product->save();
	}

}
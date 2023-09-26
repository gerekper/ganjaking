<?php

namespace ACA\WC\Editing\Product;

use ACA\WC\Editing;
use ACP\Editing\Service;
use ACP\Editing\View;
use WC_Product;

class Stock implements Service, Service\Editability {

	use ProductNotSupportedReasonTrait;

	public function get_view( string $context ): ?View {
		return ( new Editing\View\Stock() )->set_manage_stock( $this->is_manage_stock_enabled() )->set_revisioning( false );
	}

	protected function is_manage_stock_enabled(): bool {
		return 'yes' === get_option( 'woocommerce_manage_stock' );
	}

	public function is_editable( int $id ): bool {
		$product = wc_get_product( $id );

		return $product && $product->is_type( 'simple' );
	}

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		return (object) [
			'type'     => $product->get_manage_stock() && $this->is_manage_stock_enabled() ? 'manage_stock' : $product->get_stock_status(),
			'quantity' => $product->get_stock_quantity(),
		];
	}

	public function update( int $id, $data ): void {
		$product = wc_get_product( $id );

		if ( ! $product ) {
			return;
		}

		$type = $data['type'];
		$manage_stock = ( 'manage_stock' === $type );

		$product->set_stock_status( $manage_stock ? '' : $type );

		if ( $this->is_manage_stock_enabled() ) {
			$product->set_manage_stock( $manage_stock );
			$this->set_stock_quantity( $product, $data['replace_type'], $data['quantity'] );
		}

		$product->save();
	}

	private function set_stock_quantity( WC_Product $product, $type, $stock ): void {
		$original_quantity = $product->get_stock_quantity();

		switch ( $type ) {
			case 'increase':
				$stock = $original_quantity + $stock;
				break;
			case 'decrease':
				$stock = $original_quantity - $stock;
				break;
		}

		if ( $stock < 0 ) {
			$stock = 0;
		}

		$product->set_stock_quantity( $stock );
	}

}
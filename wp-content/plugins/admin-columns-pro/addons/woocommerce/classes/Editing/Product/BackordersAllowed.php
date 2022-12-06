<?php

namespace ACA\WC\Editing\Product;

use ACA\WC\Editing\Storage;
use ACP;
use ACP\Editing\View;

class BackordersAllowed implements ACP\Editing\Service, ACP\Editing\Service\Editability {

	public function is_editable( int $id ): bool {
		$product = wc_get_product( $id );

		return $product && $product->managing_stock();
	}

	public function get_not_editable_reason( int $id ): string {
		return sprintf( '%s %s', __( 'Backorder value could not be changed.', 'codepress-admin-columns' ), sprintf( __( 'Only product that have "%s" can have back orders', 'codepress-admin-columns' ), __( 'manage stock enabled', 'codepress-admin-columns' ) ) );
	}

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\Select( $this->get_backorder_options() );
	}

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		return $product
			? $product->get_backorders()
			: false;
	}

	public function update( int $id, $data ): void {
		if ( ! array_key_exists( $data, $this->get_backorder_options() ) ) {
			return;
		}

		$product = wc_get_product( $id );
		$product->set_backorders( $data );
		$product->save();
	}

	private function get_backorder_options(): array {
		return [
			'no'     => __( 'Do not allow', 'woocommerce' ),
			'notify' => __( 'Allow, but notify customer', 'woocommerce' ),
			'yes'    => __( 'Allow', 'woocommerce' ),
		];
	}

}
<?php

namespace ACA\WC\Editing\Product;

use ACP;
use ACP\Editing\View;
use RuntimeException;
use WC_Data_Exception;

class TaxStatus implements ACP\Editing\Service {

	/**
	 * @var array
	 */
	private $statuses;

	public function __construct( $statuses ) {
		$this->statuses = $statuses;
	}

	public function get_view( string $context ): ?View {
		return new View\Select( $this->statuses );
	}

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		return $product ? $product->get_tax_status() : false;
	}

	public function update( int $id, $data ): void {
		$product = wc_get_product( $id );

		try {
			$product->set_tax_status( $data );
		} catch ( WC_Data_Exception $e ) {
			throw new RuntimeException( $e->getMessage() );
		}

		$product->save();
	}

}
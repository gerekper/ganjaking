<?php

namespace ACA\WC\Editing\Product;

use ACA\WC\Editing\EditValue;
use ACA\WC\Editing\Storage;
use ACA\WC\Editing\StorageModel;
use ACA\WC\Editing\View;
use ACP\Editing\Service;
use ACP\Editing\Service\Editability;
use LogicException;
use RuntimeException;
use WP_Error;

class Price implements Service, Editability {

	use ProductNotSupportedReasonTrait;

	const TYPE_SALE = 'sale';
	const TYPE_REGULAR = 'regular';

	private $default_type;

	public function __construct( $default_type = 'regular' ) {
		if ( 'regular' !== $default_type ) {
			$default_type = 'sale';
		}

		$this->default_type = $default_type;
	}

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		$from_date = $product->get_date_on_sale_from();
		$to_date = $product->get_date_on_sale_to();

		return [
			self::TYPE_REGULAR => [
				'price' => $product->get_regular_price(),
			],
			self::TYPE_SALE    => [
				'price'         => $product->get_sale_price(),
				'schedule_from' => $from_date ? $from_date->format( 'Y-m-d' ) : '',
				'schedule_to'   => $to_date ? $to_date->format( 'Y-m-d' ) : '',
			],
		];
	}

	public function update( int $id, $data ): void {
		$product = wc_get_product( $id );

		switch ( $data['type'] ) {
			case self::TYPE_REGULAR:
				$model = new StorageModel\Product\Price( $product, new EditValue\Product\Price( $data ) );

				break;
			case self::TYPE_SALE:
				$model = new StorageModel\Product\SalePrice( $product, new EditValue\Product\SalePrice( $data ) );

				break;
			default:
				throw new LogicException( 'Invalid type.' );
		}

		$result = $model->save();

		if ( $result instanceof WP_Error ) {
			throw new RuntimeException( $result->get_error_message() );
		}
	}

	public function is_editable( int $id ): bool {
		$product = wc_get_product( $id );

		return ! $product->is_type( [ 'variable', 'grouped' ] );
	}

	public function get_view( string $context ): ?\ACP\Editing\View {
		return new View\Price( $this->default_type );
	}

}
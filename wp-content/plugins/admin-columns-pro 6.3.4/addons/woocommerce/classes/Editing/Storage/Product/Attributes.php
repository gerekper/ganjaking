<?php

namespace ACA\WC\Editing\Storage\Product;

use ACA\WC\Editing\EditValue;
use ACA\WC\Editing\StorageModel;
use ACP\Editing\Storage;
use WC_Product_Attribute;

abstract class Attributes implements Storage {

	/**
	 * @var string
	 */
	protected $attribute;

	public function __construct( string $attribute ) {
		$this->attribute = $attribute;
	}

	/**
	 * @return false|WC_Product_Attribute
	 */
	abstract protected function create_attribute();

	public function get( int $id ) {
		$attribute = $this->get_attribute_object( $id );

		return $attribute ? array_values( $attribute->get_options() ) : [];
	}

	public function update( int $id, $data ): bool {
		$attribute = $this->get_attribute_object( $id );

		if ( ! $attribute ) {
			$attribute = $this->create_attribute();
		}

		if ( ! $attribute ) {
			throw new \RuntimeException( __( 'Non existing attribute.', 'codepress-admin-columns' ) );
		}

		$attribute->set_options( $data );

		$product = wc_get_product( $id );

		$attributes = $product->get_attributes();
		$attributes[] = $attribute;

		$product->set_attributes( $attributes );

		return $product->save() > 0;
	}

	/**
	 * @param int $id
	 *
	 * @return false|WC_Product_Attribute
	 */
	protected function get_attribute_object( $id ) {
		$product = wc_get_product( $id );
		$attributes = $product->get_attributes();

		return $attributes[ $this->attribute ] ?? false;
	}
}
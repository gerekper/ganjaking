<?php

namespace ACA\WC\Column\ProductVariation;

use AC;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACA\WC\Type;
use ACP\ConditionalFormat\ConditionalFormatTrait;
use ACP\ConditionalFormat\Formattable;
use ACP\Search\Searchable;

class Attribute extends AC\Column implements Searchable, Formattable {

	use ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-variation_attribute' )
		     ->set_label( __( 'Attribute', 'woocommerce' ) )
		     ->set_group( 'woocommerce' );
	}

	/**
	 * @return Type\ProductAttribute|null
	 */
	private function get_attribute() {
		/** @var Settings\ProductVariation\Attribute $setting */
		$setting = $this->get_setting( Settings\ProductVariation\Attribute::NAME );

		return $setting->get_product_attribute();
	}

	private function get_attrribute_label( $id, Type\ProductAttribute $attribute ) {
		foreach ( wc_get_product( $id )->get_attributes() as $name => $label ) {
			if ( $attribute->get_name() !== $name ) {
				continue;
			}

			return $attribute->is_taxonomy()
				? wc_get_product( $id )->get_attribute( $attribute->get_name() )
				: $label;
		}

		return null;
	}

	public function get_value( $id ) {
		$attribute = $this->get_attribute();

		if ( ! $attribute ) {
			return null;
		}

		$label = $this->get_attrribute_label( $id, $attribute );

		return $label ?: $this->get_empty_char();
	}

	public function register_settings() {
		$this->add_setting( new Settings\ProductVariation\Attribute( $this ) );
	}

	public function search() {
		$attribute = $this->get_attribute();

		if ( ! $attribute ) {
			return false;
		}

		if ( $attribute->is_taxonomy() ) {
			return new Search\ProductVariation\AttributeTaxonomy( $attribute->get_name() );
		}

		return new Search\ProductVariation\Attribute( 'attribute_' . $attribute->get_name() );
	}

}
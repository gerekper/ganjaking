<?php

if ( ! class_exists( 'GFForms' ) || ! class_exists( 'GF_Field_Calculation' ) ) {
	die();
}

class GF_Field_Tax extends GF_Field_Subtotal {

	public $type = 'tax';

	// # FORM EDITOR

	public function get_form_editor_field_settings() {
		return array_merge( parent::get_form_editor_field_settings(), array( 'ecommerce-amount-setting', 'visibility_setting' ) );
	}



	// # BACKEND

	public static function add_taxes( $order, $form, $entry ) {

		$tax_fields = array();

		foreach( $form['fields'] as $field ) {
			if( $field->type == 'tax' && ! GFFormsModel::is_field_hidden( $form, $field, array(), $entry ) ) {
				$tax_fields[] = $field;
			}
		}

		if( empty( $tax_fields ) ) {
			return $order;
		}

		$total = gp_ecommerce_fields()->get_total( $order );

		foreach( $tax_fields as $tax_field ) {

			$tax = gp_ecommerce_fields()->get_field_total( $order, $entry, $total, array(
				'amount'       => $tax_field->taxAmount,
				'amountType'   => $tax_field->taxAmountType,
				'products'     => $tax_field->taxProducts,
				'productsType' => $tax_field->taxProductsType,
				'includeDiscounts' => true
			), $form );

			$order['products'][ $tax_field->id ] = array(
				'name'     => $tax_field->get_field_label( true, false ),
				'price'    => $tax,
				'quantity' => 1,
				'isTax'    => true
			);

		}

		return $order;
	}

	public static function add_order_summary_tax_items( $order_summary, $form, $entry, $order ) {

		foreach( $order['products'] as $product ) {

			if( ! rgar( $product, 'isTax' ) ) {
				continue;
			}

			$order_summary['taxes'][] = array(
				'name'  => $product['name'],
				'price' => $product['price']
			);

		}

		return $order_summary;
	}

}

GF_Fields::register( new GF_Field_Tax() );
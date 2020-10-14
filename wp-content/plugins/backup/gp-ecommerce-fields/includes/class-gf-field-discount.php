<?php

if ( ! class_exists( 'GFForms' ) || ! class_exists( 'GF_Field_Calculation' ) ) {
	die();
}

class GF_Field_Discount extends GF_Field_Subtotal {

	public $type = 'discount';

	// # FORM EDITOR

	public function get_form_editor_field_settings() {
		return array_merge( parent::get_form_editor_field_settings(), array( 'ecommerce-amount-setting', 'visibility_setting' ) );
	}



	// # FRONTEND

	public function get_inline_price_styles() {
		return '';
	}


	// # BACKEND

	public static function add_discounts( $order, $form, $entry ) {

		$discount_fields = array();

		foreach( $form['fields'] as $field ) {
			if( $field->type == 'discount' && ! GFFormsModel::is_field_hidden( $form, $field, array(), $entry ) ) {
				$discount_fields[] = $field;
			}
		}

		if( empty( $discount_fields ) ) {
			return $order;
		}

		$total = gp_ecommerce_fields()->get_total( $order );

		foreach( $discount_fields as $discount_field ) {

			$discount = gp_ecommerce_fields()->get_field_total( $order, $entry, $total, array(
				'amount'       => $discount_field->discountAmount,
				'amountType'   => $discount_field->discountAmountType,
				'products'     => $discount_field->discountProducts,
				'productsType' => $discount_field->discountProductsType,
			) );

			$discount *= -1;

			/**
			 * Modify the calculated discount for a Discount field.
			 *
			 * @since 1.0.23
			 *
			 * @param float    $discount       The total for the current Discount field.
			 * @param GF_Field $discount_field The current field object.
			 * @param array    $form           The current form object.
			 * @param array    $entry          The current entry object.
			 */
			$discount = gf_apply_filters( array( 'gpecf_discount_total', $form['id'], $discount_field->id ), $discount, $discount_field, $form, $entry );

			$order['products'][ $discount_field->id ] = array(
				'name'       => $discount_field->get_field_label( true, false ),
				'price'      => $discount,
				'quantity'   => 1,
				'isDiscount' => true
			);

		}

		return $order;
	}

	/**
	 * Get all discounts for this specific product. Includes Discount fields and coupons.
	 *
	 * @param $order
	 * @param $form
	 * @param $entry
	 * @param $product_id
	 * @param $product_total
	 *
	 * @return bool|float|int
	 */
	public static function get_product_discount( $order, $form, $entry, $product_id, $product_total ) {

		$coupon_fields = GFFormsModel::get_fields_by_type( $form, 'coupon' );
		$discount_fields = array();

		foreach( $form['fields'] as $field ) {
			if( $field->type == 'discount'
			    && ! GFFormsModel::is_field_hidden( $form, $field, array(), $entry )
				&& ( ! $field->discountProducts || ( $field->discountProductsType == 'include' && in_array( $product_id, $field->discountProducts ) ) )
				) {
				$discount_fields[] = $field;
			}
		}

		$total = 0;

		if( ! empty( $discount_fields ) ) {

			foreach( $discount_fields as $discount_field ) {

				$discount = gp_ecommerce_fields()->get_field_total( $order, $entry, $product_total, array(
					'amount'           => $discount_field->discountAmount,
					'amountType'       => $discount_field->discountAmountType,
					'products'         => $discount_field->discountProducts,
					'productsType'     => $discount_field->discountProductsType,
					'includeDiscounts' => false,
					'calculateByProduct' => true
				) );

				$total += $discount;

			}

		}

		if( ! empty( $coupon_fields ) && is_callable( 'gf_coupons' ) ) {
			$coupon_codes = gf_coupons()->get_submitted_coupon_codes( $form, $entry );
			$coupons      = gf_coupons()->get_coupons_by_codes( $coupon_codes, $form );
			$coupons      = gf_coupons()->get_discounts( $coupons, $product_total, $total );
		}

		return $total;
	}

	public static function add_order_summary_discount_items( $order_summary, $form, $entry, $order ) {

		foreach( $order['products'] as $product ) {

			if( ! rgar( $product, 'isDiscount' ) ) {
				continue;
			}

			$order_summary['discounts'][] = array(
				'name'      => $product['name'],
				'price'     => $product['price'],
				'cellStyle' => 'color:#008800',
				'class'     => 'discount'
			);

		}

		return $order_summary;
	}

}

GF_Fields::register( new GF_Field_Discount() );
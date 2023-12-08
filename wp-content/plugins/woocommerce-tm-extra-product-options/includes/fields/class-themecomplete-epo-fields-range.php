<?php
/**
 * Range Picker Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 * phpcs:disable PEAR.NamingConventions.ValidClassName
 */

defined( 'ABSPATH' ) || exit;

/**
 * Range Picker Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 */
class THEMECOMPLETE_EPO_FIELDS_range extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @param array<mixed> $element The element array.
	 * @param array<mixed> $args Array of arguments.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function display_field( $element = [], $args = [] ) {

		$default_value = $this->get_value( $element, 'default_value', '' );
		$min           = $this->get_value( $element, 'min', '' );
		if ( '' !== $min && '' === $default_value ) {
			$default_value = $min;
		}

		$display = [
			'default_value'     => $default_value,
			'get_default_value' => $this->get_default_value( $element, $args, 'notempty', $min ),
			'textbeforeprice'   => $this->get_value( $element, 'text_before_price', '' ),
			'textafterprice'    => $this->get_value( $element, 'text_after_price', '' ),
			'hide_amount'       => $this->get_value( $element, 'hide_amount', '' ),
			'min'               => $min,
			'max'               => $this->get_value( $element, 'max', '' ),
			'step'              => $this->get_value( $element, 'step', '' ),
			'pips'              => $this->get_value( $element, 'pips', '' ),
			'noofpips'          => $this->get_value( $element, 'noofpips', '' ),
			'show_picker_value' => $this->get_value( $element, 'show_picker_value', '' ),
			'quantity'          => $this->get_value( $element, 'quantity', '' ),
		];

		return apply_filters( 'wc_epo_display_field_range', $display, $this, $element, $args );
	}

	/**
	 * Field validation
	 *
	 * @since 1.0
	 * @return array<mixed>
	 */
	public function validate() {

		$passed  = true;
		$message = [];

		$quantity_once = false;
		$min_quantity  = isset( $this->element['quantity_min'] ) ? (int) $this->element['quantity_min'] : 0;
		if ( apply_filters( 'wc_epo_field_min_quantity_greater_than_zero', true ) && $min_quantity < 0 ) {
			$min_quantity = 0;
		}
		foreach ( $this->field_names as $attribute ) {
			$attribute_quantity = $attribute . '_quantity';
			if ( ! $quantity_once && isset( $this->epo_post_fields[ $attribute ] ) && '' !== $this->epo_post_fields[ $attribute ] && isset( $this->epo_post_fields[ $attribute_quantity ] ) && ! ( (int) array_sum( (array) $this->epo_post_fields[ $attribute_quantity ] ) >= $min_quantity ) ) {
				$passed        = false;
				$quantity_once = true;
				/* translators: %1 element label %2 quantity value. */
				$message[] = sprintf( esc_html__( 'The quantity for "%1$s" must be greater than %2$s', 'woocommerce-tm-extra-product-options' ), $this->element['label'], $min_quantity );
			}
			if ( $this->element['required'] ) {
				if ( ! isset( $this->epo_post_fields[ $attribute ] ) || '' === $this->epo_post_fields[ $attribute ] ) {
					$passed    = false;
					$message[] = 'required';
					break;
				}
			}
		}

		return [
			'passed'  => $passed,
			'message' => $message,
		];
	}

	/**
	 * Add field data to cart (single type fields)
	 *
	 * @return false|array<mixed>
	 * @since 1.0
	 */
	public function add_cart_item_data_single() {
		if ( ! $this->is_setup() ) {
			return false;
		}
		if ( ! empty( $this->key ) ) {

			$attribute_quantity = isset( $this->post_data[ $this->attribute_quantity ] ) ? $this->post_data[ $this->attribute_quantity ] : 1;
			if ( is_array( $attribute_quantity ) && isset( $attribute_quantity[ $this->key_id ] ) ) {
				$attribute_quantity = $attribute_quantity[ $this->key_id ];
				if ( is_array( $attribute_quantity ) && isset( $attribute_quantity[ $this->keyvalue_id ] ) ) {
					$attribute_quantity = $attribute_quantity[ $this->keyvalue_id ];
				}
			}
			$_price = THEMECOMPLETE_EPO()->calculate_price( $this->post_data, $this->element, $this->key, $this->attribute, $attribute_quantity, $this->key_id, $this->keyvalue_id, $this->per_product_pricing, $this->cpf_product_price, $this->variation_id );
			return apply_filters(
				'wc_epo_add_cart_item_data_single',
				[
					'mode'                             => 'builder',
					'cssclass'                         => $this->element['class'],
					'hidelabelincart'                  => $this->element['hide_element_label_in_cart'],
					'hidevalueincart'                  => $this->element['hide_element_value_in_cart'],
					'hidelabelinorder'                 => $this->element['hide_element_label_in_order'],
					'hidevalueinorder'                 => $this->element['hide_element_value_in_order'],
					'shippingmethodsenable'            => $this->element['shipping_methods_enable'],
					'shippingmethodsenablelogicrules'  => $this->element['shipping_methods_enable_logicrules'],
					'shippingmethodsdisable'           => $this->element['shipping_methods_disable'],
					'shippingmethodsdisablelogicrules' => $this->element['shipping_methods_disable_logicrules'],
					'element'                          => $this->order_saved_element,
					'name'                             => $this->element['label'],
					'value'                            => $this->key,
					'post_name'                        => $this->attribute,
					'price'                            => $_price,
					'section'                          => $this->element['uniqid'],
					'section_label'                    => $this->element['label'],
					'currencies'                       => isset( $this->element['currencies'] ) ? $this->element['currencies'] : [],
					'price_per_currency'               => $this->fill_currencies( $attribute_quantity ),
					'percentcurrenttotal'              => isset( $this->post_data[ $this->attribute . '_hidden' ] ) ? 1 : 0,
					'fixedcurrenttotal'                => isset( $this->post_data[ $this->attribute . '_hiddenfixed' ] ) ? 1 : 0,
					'quantity'                         => $attribute_quantity,
					'quantity_selector'                => isset( $this->element['quantity'] ) ? $this->element['quantity'] : '',
				],
				$this
			);

		}

		return false;
	}
}

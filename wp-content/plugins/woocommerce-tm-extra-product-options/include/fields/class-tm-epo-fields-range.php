<?php
/**
 * Range Picker Field class
 *
 * @package Extra Product Options/Fields
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_FIELDS_range extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @since 1.0
	 */
	public function display_field( $element = array(), $args = array() ) {

		$default_value = isset( $element['default_value'] ) ? $element['default_value'] : "";
		$min           = isset( $element['min'] ) ? $element['min'] : "";
		if ( $min !== '' && $default_value == '' ) {
			$default_value = $min;
		}

		$get_default_value = "";
		if ( THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "no" && isset( $_POST[ $args['name'] ] ) ) {
			$get_default_value = stripslashes( $_POST[ $args['name'] ] );
		} elseif ( isset( $_GET[ $args['name'] ] ) ) {
			$get_default_value = stripslashes( $_GET[ $args['name'] ] );
		} elseif ( isset( $default_value ) ) {
			$get_default_value = $default_value;
		} else {
			$get_default_value = $min;
		}
		$get_default_value = apply_filters( 'wc_epo_default_value', $get_default_value, $element );

		return array(
			'textbeforeprice'   => isset( $element['text_before_price'] ) ? $element['text_before_price'] : "",
			'textafterprice'    => isset( $element['text_after_price'] ) ? $element['text_after_price'] : "",
			'hide_amount'       => isset( $element['hide_amount'] ) ? " " . $element['hide_amount'] : "",
			'min'               => $min,
			'max'               => isset( $element['max'] ) ? $element['max'] : "",
			'step'              => isset( $element['step'] ) ? $element['step'] : "",
			'pips'              => isset( $element['pips'] ) ? $element['pips'] : "",
			'noofpips'          => isset( $element['pips'] ) ? $element['noofpips'] : "",
			'show_picker_value' => isset( $element['show_picker_value'] ) ? $element['show_picker_value'] : "",
			'quantity'          => isset( $element['quantity'] ) ? $element['quantity'] : "",
			'default_value'     => $default_value,
			'get_default_value' => $get_default_value,
		);
	}

	/**
	 * Field validation
	 *
	 * @since 1.0
	 */
	public function validate() {

		$passed  = TRUE;
		$message = array();

		$quantity_once = FALSE;
		$min_quantity  = isset( $this->element['quantity_min'] ) ? intval( $this->element['quantity_min'] ) : 0;
		if ( $min_quantity < 0 ) {
			$min_quantity = 0;
		}
		foreach ( $this->field_names as $attribute ) {
			if ( ! $quantity_once && isset( $this->epo_post_fields[ $attribute ] ) && $this->epo_post_fields[ $attribute ] !== "" && isset( $this->epo_post_fields[ $attribute . '_quantity' ] ) && ! ( intval( $this->epo_post_fields[ $attribute . '_quantity' ] ) >= $min_quantity ) ) {
				$passed        = FALSE;
				$quantity_once = TRUE;
				$message[]     = sprintf( esc_html__( 'The quantity for "%s" must be greater than %s', 'woocommerce-tm-extra-product-options' ), $this->element['label'], $min_quantity );
			}
			if ( $this->element['required'] ) {
				if ( ! isset( $this->epo_post_fields[ $attribute ] ) || $this->epo_post_fields[ $attribute ] == "" ) {
					$passed    = FALSE;
					$message[] = 'required';
					break;
				}
			}
		}

		return array( 'passed' => $passed, 'message' => $message );
	}

	/**
	 * Add field data to cart (single type fields)
	 *
	 * @since 1.0
	 */
	public function add_cart_item_data_single() {
		if ( ! $this->is_setup() ) {
			return FALSE;
		}
		if ( ! empty( $this->key ) ) {

			$_price = THEMECOMPLETE_EPO()->calculate_price( $this->post_data, $this->element, $this->key, $this->attribute, $this->per_product_pricing, $this->cpf_product_price, $this->variation_id );

			return apply_filters( 'wc_epo_add_cart_item_data_single', array(
				'mode' => 'builder',

				'cssclass'         => $this->element['class'],
				'hidelabelincart'  => $this->element['hide_element_label_in_cart'],
				'hidevalueincart'  => $this->element['hide_element_value_in_cart'],
				'hidelabelinorder' => $this->element['hide_element_label_in_order'],
				'hidevalueinorder' => $this->element['hide_element_value_in_order'],

				'element' => $this->order_saved_element,

				'name'                => $this->element['label'],
				'value'               => $this->key,
				'price'               => $_price,
				'section'             => $this->element['uniqid'],
				'section_label'       => $this->element['label'],
				'currencies'          => isset( $this->element['currencies'] ) ? $this->element['currencies'] : array(),
				'price_per_currency'  => $this->fill_currencies(),
				'percentcurrenttotal' => isset( $this->post_data[ $this->attribute . '_hidden' ] ) ? 1 : 0,
				'fixedcurrenttotal'   => isset( $this->post_data[ $this->attribute . '_hiddenfixed' ] ) ? 1 : 0,
				'quantity'            => isset( $this->post_data[ $this->attribute . '_quantity' ] ) ? $this->post_data[ $this->attribute . '_quantity' ] : 1,
			), $this );

		}

		return FALSE;
	}
}

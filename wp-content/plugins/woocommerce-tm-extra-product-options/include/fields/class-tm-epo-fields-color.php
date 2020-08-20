<?php
/**
 * Color Field class
 *
 * @package Extra Product Options/Fields
 * @version 4.8
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_FIELDS_color extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @since 1.0
	 */
	public function display_field( $element = array(), $args = array() ) {

		$default_value     = isset( $element['default_value'] ) ? esc_attr( $element['default_value'] ) : '';
		$get_default_value = "";
		if ( THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "no" && isset( $_POST[ $args['name'] ] ) ) {
			$get_default_value = esc_attr( stripslashes( $_POST[ $args['name'] ] ) );
		} elseif ( isset( $_GET[ $args['name'] ] ) ) {
			$get_default_value = esc_attr( stripslashes( $_GET[ $args['name'] ] ) );
		} else {
			$get_default_value = $default_value;
		}
		$get_default_value = apply_filters( 'wc_epo_default_value', $get_default_value, $element );

		return array(
			'textbeforeprice'   => isset( $element['text_before_price'] ) ? $element['text_before_price'] : "",
			'textafterprice'    => isset( $element['text_after_price'] ) ? $element['text_after_price'] : "",
			'hide_amount'       => isset( $element['hide_amount'] ) ? " " . $element['hide_amount'] : "",
			'default_value'     => $default_value,
			'get_default_value' => $get_default_value,
			'quantity'          => isset( $element['quantity'] ) ? $element['quantity'] : "",
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

}

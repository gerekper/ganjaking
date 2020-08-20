<?php
/**
 * Textfield Field class
 *
 * @package Extra Product Options/Fields
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_FIELDS_textfield extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @since 1.0
	 */
	public function display_field( $element = array(), $args = array() ) {

		$default_value     = isset( $element['default_value'] ) ? esc_attr( $element['default_value'] ) : '';
		$get_default_value = "";
		if ( THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "no" && isset( $_POST[ $args['name'] ] ) ) {
			$get_default_value = stripslashes( $_POST[ $args['name'] ] );
		} elseif ( isset( $_GET[ $args['name'] ] ) ) {
			$get_default_value = stripslashes( $_GET[ $args['name'] ] );
		} else {
			$get_default_value = $default_value;
		}
		$get_default_value = apply_filters( 'wc_epo_default_value', $get_default_value, $element );

		return array(
			'textbeforeprice'   => isset( $element['text_before_price'] ) ? $element['text_before_price'] : "",
			'textafterprice'    => isset( $element['text_after_price'] ) ? $element['text_after_price'] : "",
			'hide_amount'       => isset( $element['hide_amount'] ) ? " " . $element['hide_amount'] : "",
			'placeholder'       => isset( $element['placeholder'] ) ? esc_attr( $element['placeholder'] ) : '',
			'min_chars'         => isset( $element['min_chars'] ) && $element['min_chars'] !== '' ? absint( $element['min_chars'] ) : '',
			'max_chars'         => isset( $element['max_chars'] ) && $element['max_chars'] !== '' ? absint( $element['max_chars'] ) : '',
			'min'               => isset( $element['min'] ) ? $element['min'] : '',
			'max'               => isset( $element['max'] ) ? $element['max'] : '',
			'default_value'     => $default_value,
			'get_default_value' => $get_default_value,
			'quantity'          => isset( $element['quantity'] ) ? $element['quantity'] : "",
			'freechars'         => isset( $element['freechars'] ) ? $element['freechars'] : "",
			'input_type'        => isset( $element['price_rules_type'] ) && isset( $element['price_rules_type'][0] ) && isset( $element['price_rules_type'][0][0] )
				? (
				in_array( $element['price_rules_type'][0][0], array( 'step', 'currentstep' ) )
				|| ( isset( $element['validation1'] ) && ( $element['validation1'] == "number" || $element['validation1'] == "digits" ) )
					? 'number' : 'text'
				)
				: 'text',
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
			if ( $this->element['min_chars'] ) {
				$val = FALSE;
				if ( isset( $this->epo_post_fields[ $attribute ] ) ) {
					$val = $this->epo_post_fields[ $attribute ];
					$val = preg_replace( "/\r\n/", "\n", $val );
				}
				if ( $val !== '' && ( $val === FALSE || strlen( $val ) < intval( $this->element['min_chars'] ) ) ) {
					$passed    = FALSE;
					$message[] = sprintf( esc_html__( 'You must enter at least %s characters for "%s".', 'woocommerce-tm-extra-product-options' ), intval( $this->element['min_chars'] ), $this->element['label'] );
					break;
				}
			}
			if ( $this->element['max_chars'] ) {
				$val = FALSE;
				if ( isset( $this->epo_post_fields[ $attribute ] ) ) {
					$val = $this->epo_post_fields[ $attribute ];
					$val = preg_replace( "/\r\n/", "\n", $val );
				}
				if ( $val !== '' && ( $val !== FALSE && strlen( utf8_decode( $val ) ) > intval( $this->element['max_chars'] ) ) ) {
					$passed    = FALSE;
					$message[] = sprintf( esc_html__( 'You cannot enter more than %s characters for "%s".', 'woocommerce-tm-extra-product-options' ), intval( $this->element['max_chars'] ), $this->element['label'] );
					break;
				}
			}
		}

		return array( 'passed' => $passed, 'message' => $message );
	}

}

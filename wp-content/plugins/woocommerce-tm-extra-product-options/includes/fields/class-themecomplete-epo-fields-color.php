<?php
/**
 * Color Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 * phpcs:disable PEAR.NamingConventions.ValidClassName
 */

defined( 'ABSPATH' ) || exit;

/**
 * Color Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 */
class THEMECOMPLETE_EPO_FIELDS_color extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @param array<mixed> $element The element array.
	 * @param array<mixed> $args Array of arguments.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function display_field( $element = [], $args = [] ) {
		$class_label = '';
		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_select_fullwidth' ) ) {
			$class_label = ' fullwidth';
		}
		$display = [
			'default_value'     => $this->get_value( $element, 'default_value', '' ),
			'get_default_value' => $this->get_default_value( $element, $args ),
			'textbeforeprice'   => $this->get_value( $element, 'text_before_price', '' ),
			'textafterprice'    => $this->get_value( $element, 'text_after_price', '' ),
			'hide_amount'       => $this->get_value( $element, 'hide_amount', '' ),
			'quantity'          => $this->get_value( $element, 'quantity', '' ),
			'class_label'       => $class_label,
		];

		return apply_filters( 'wc_epo_display_field_color', $display, $this, $element, $args );
	}

	/**
	 * Field validation
	 *
	 * @return array<mixed>
	 * @since 1.0
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
}

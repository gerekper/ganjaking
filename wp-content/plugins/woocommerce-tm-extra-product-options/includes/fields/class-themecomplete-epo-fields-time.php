<?php
/**
 * Time Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 * phpcs:disable PEAR.NamingConventions.ValidClassName
 */

defined( 'ABSPATH' ) || exit;

/**
 * Time Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 */
class THEMECOMPLETE_EPO_FIELDS_time extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @param array<mixed> $element The element array.
	 * @param array<mixed> $args Array of arguments.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function display_field( $element = [], $args = [] ) {
		$tm_epo_global_datepicker_theme    = ! empty( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_datepicker_theme' ) ) ? THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_datepicker_theme' ) : $this->get_value( $element, 'theme', 'epo' );
		$tm_epo_global_datepicker_size     = ! empty( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_datepicker_size' ) ) ? THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_datepicker_size' ) : $this->get_value( $element, 'theme_size', 'medium' );
		$tm_epo_global_datepicker_position = ! empty( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_datepicker_position' ) ) ? THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_datepicker_position' ) : $this->get_value( $element, 'theme_position', 'normal' );

		$button_type = ! empty( $element['button_type'] ) ? $element['button_type'] : 'system';

		$custom_time_format = $this->get_value_no_empty( $element, 'custom_time_format', '' );
		$time_format        = $this->get_value_no_empty( $element, 'time_format', 'HH:mm' );
		if ( ! is_string( $time_format ) ) {
			$time_format = 'HH:mm';
		}
		$time_placeholder = $time_format;
		$time_mask        = $time_format;
		if ( '' !== $custom_time_format ) {
			$time_mask = $custom_time_format;
		}
		$time_mask = str_replace( 'H', '0', $time_mask );
		$time_mask = str_replace( 'h', '0', $time_mask );
		$time_mask = str_replace( 'm', '0', $time_mask );
		$time_mask = str_replace( 'M', '0', $time_mask );
		$time_mask = str_replace( 's', '0', $time_mask );
		$time_mask = str_replace( 'S', '0', $time_mask );
		$time_mask = str_replace( 't', 'S', $time_mask );
		$time_mask = str_replace( 'T', 'S', $time_mask );

		if ( ! is_string( $time_mask ) ) {
			$time_mask = '00:00';
		}

		if ( apply_filters( 'wc_epo_display_rtl', is_rtl() ) ) {
			$time_mask = strrev( $time_mask );
		}

		$input_type   = 'text';
		$button_style = ' tm-epo-timepicker';
		if ( 'system' === $button_type ) {
			$input_type   = 'time';
			$button_style = ' tm-epo-system-timepicker';
		}

		$class_label = '';
		if ( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_select_fullwidth' ) === 'yes' ) {
			$class_label = ' fullwidth';
		}

		$display = [
			'get_default_value'   => $this->get_default_value( $element, $args, false ),
			'textbeforeprice'     => $this->get_value( $element, 'text_before_price', '' ),
			'textafterprice'      => $this->get_value( $element, 'text_after_price', '' ),
			'hide_amount'         => $this->get_value( $element, 'hide_amount', '' ),
			'min_time'            => $this->get_value( $element, 'min_time', '' ),
			'max_time'            => $this->get_value( $element, 'max_time', '' ),
			'translation_hour'    => $this->get_value_no_empty( $element, 'translation_hour', '' ),
			'translation_minute'  => $this->get_value_no_empty( $element, 'translation_minute', '' ),
			'translation_second'  => $this->get_value_no_empty( $element, 'translation_second', '' ),
			'quantity'            => $this->get_value( $element, 'quantity', '' ),
			'time_format'         => $time_format,
			'custom_time_format'  => $custom_time_format,
			'time_theme'          => $tm_epo_global_datepicker_theme,
			'time_theme_size'     => $tm_epo_global_datepicker_size,
			'time_theme_position' => $tm_epo_global_datepicker_position,
			'time_placeholder'    => $time_placeholder,
			'time_mask'           => $time_mask,
			'input_type'          => $input_type,
			'button_style'        => $button_style,
			'class_label'         => $class_label,
		];

		return apply_filters( 'wc_epo_display_field_time', $display, $this, $element, $args );
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
		$min_quantity  = isset( $this->element['quantity_min'] ) ? intval( $this->element['quantity_min'] ) : 0;
		if ( apply_filters( 'wc_epo_field_min_quantity_greater_than_zero', true ) && $min_quantity < 0 ) {
			$min_quantity = 0;
		}
		foreach ( $this->field_names as $attribute ) {
			$attribute_quantity = $attribute . '_quantity';
			if ( ! $quantity_once && isset( $this->epo_post_fields[ $attribute ] ) && '' !== $this->epo_post_fields[ $attribute ] && isset( $this->epo_post_fields[ $attribute_quantity ] ) && ! ( intval( array_sum( (array) $this->epo_post_fields[ $attribute_quantity ] ) ) >= $min_quantity ) ) {
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

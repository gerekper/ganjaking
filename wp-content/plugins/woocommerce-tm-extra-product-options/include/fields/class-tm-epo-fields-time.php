<?php
/**
 * Time Field class
 *
 * @package Extra Product Options/Fields
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_FIELDS_time extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @since 1.0
	 */
	public function display_field( $element = array(), $args = array() ) {
		$tm_epo_global_datepicker_theme    = ! empty( THEMECOMPLETE_EPO()->tm_epo_global_datepicker_theme ) ? THEMECOMPLETE_EPO()->tm_epo_global_datepicker_theme : ( isset( $element['theme'] ) ? $element['theme'] : "epo" );
		$tm_epo_global_datepicker_size     = ! empty( THEMECOMPLETE_EPO()->tm_epo_global_datepicker_size ) ? THEMECOMPLETE_EPO()->tm_epo_global_datepicker_size : ( isset( $element['theme_size'] ) ? $element['theme_size'] : "medium" );
		$tm_epo_global_datepicker_position = ! empty( THEMECOMPLETE_EPO()->tm_epo_global_datepicker_position ) ? THEMECOMPLETE_EPO()->tm_epo_global_datepicker_position : ( isset( $element['theme_position'] ) ? $element['theme_position'] : "normal" );

		$button_type = ! empty( $element['button_type'] ) ? $element['button_type'] : "system";

		$custom_time_format = ! empty( $element['custom_time_format'] ) ? $element['custom_time_format'] : "";
		$time_format        = ! empty( $element['time_format'] ) ? $element['time_format'] : "HH:mm";
		$time_placeholder   = $time_format;
		$time_mask          = $time_format;
		if ( $custom_time_format !== '' ) {
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

		if ( apply_filters( 'wc_epo_display_rtl', is_rtl() ) ) {
			$time_mask = strrev( $time_mask );
		}

		$input_type = "text";
		$button_style = " tm-epo-timepicker";
		if ($button_type === "system"){
			$input_type = "time";
			$button_style = " tm-epo-system-timepicker";
		}

		$get_default_value = "";
		if ( THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "no" && isset( $_POST[ $args['name'] ] ) ) {
			$get_default_value = stripslashes( $_POST[ $args['name'] ] );
		} elseif ( isset( $_GET[ $args['name'] ] ) ) {
			$get_default_value = stripslashes( $_GET[ $args['name'] ] );
		}
		$get_default_value = apply_filters( 'wc_epo_default_value', $get_default_value, $element );

		return array(
			'textbeforeprice'     => isset( $element['text_before_price'] ) ? $element['text_before_price'] : "",
			'textafterprice'      => isset( $element['text_after_price'] ) ? $element['text_after_price'] : "",
			'hide_amount'         => isset( $element['hide_amount'] ) ? " " . $element['hide_amount'] : "",
			'time_format'         => $time_format,
			'custom_time_format'  => $custom_time_format,
			'min_time'            => isset( $element['min_time'] ) ? $element['min_time'] : "",
			'max_time'            => isset( $element['max_time'] ) ? $element['max_time'] : "",
			'tranlation_hour'     => ! empty( $element['tranlation_hour'] ) ? $element['tranlation_hour'] : "",
			'tranlation_minute'   => ! empty( $element['tranlation_minute'] ) ? $element['tranlation_minute'] : "",
			'tranlation_second'   => ! empty( $element['tranlation_second'] ) ? $element['tranlation_second'] : "",
			'quantity'            => isset( $element['quantity'] ) ? $element['quantity'] : "",
			'time_theme'          => $tm_epo_global_datepicker_theme,
			'time_theme_size'     => $tm_epo_global_datepicker_size,
			'time_theme_position' => $tm_epo_global_datepicker_position,

			'time_placeholder'  => $time_placeholder,
			'time_mask'         => $time_mask,
			'input_type'        => $input_type,
			'get_default_value' => $get_default_value,
			'button_style'      => $button_style,
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
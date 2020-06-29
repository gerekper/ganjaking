<?php

/**
 * Helper class to render input field types. 
 */
class WC_Conditional_Content_Input_Builder {

	/**
	 * Gets the input object and renders the field. 
	 * @param array $field_args Arguments to render the field with. 
	 * @param mixed $value The value, if any, to apply to the field. 
	 */
	public static function create_input_field( $field_args, $value = null ) {

		$field_args = apply_filters( 'wc_conditional_content_get_input_defaults', self::get_input_field_defaults( $field_args ) );
		$field_value = apply_filters( 'wc_conditional_content_get_input_value', $value, $field_args );
		$input_object = woocommerce_conditional_content_get_input_object( $field_args['input'] );
		$input_object->render( $field_args, $field_value );
	}

	/**
	 * Helper function to get field defaults. 
	 * @param array $field_args Arguments to merge with the defaults. 
	 * @return array The merged arguments
	 */
	public static function get_input_field_defaults( $field_args ) {

		// defaults
		$defaults = array(
		    'key' => '',
		    'label' => '',
		    'name' => '',
		    'input' => 'text',
		    'order_no' => 1,
		    'instructions' => '',
		    'required' => 0,
		    'id' => '',
		    'class' => '',
		);

		$field_args = array_merge( $defaults, $field_args );
		if ( !isset( $field_args['id'] ) ) {
			$field_args['id'] = sanitize_title( $field_args['name'] );
		}

		return $field_args;
	}

}
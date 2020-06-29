<?php
class MFN_Validation_color extends MFN_Options{

	/**
	 * Constructor
	 */
	function __construct( $field, $value, $current ){

		$this->field = $field;
		$this->field['msg'] = isset( $this->field['msg'] ) ? $this->field['msg'] : __( 'This field must be a valid color value.', 'mfn-opts' );

		// set value + sanitize
		$this->value = str_replace( ' ', '', $value );

		$this->current = $current;

		$this->validate();
	}

	/**
	 * Validate
	 */
	function validate(){

		// single color

		if( ! is_array( $this->value ) ){

			// allowed values: #f2f2f2, #fff, rgba(0,0,0,0.5), rgba(0,0,0,.5)
			// regex101.com/r/A2IjNO/19

			$pattern = '/^(\#[\da-f]{3}|\#[\da-f]{6}|rgba\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)(,\s*((0)*\.\d+|0|1))\)|rgb\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)\))$/i';

			if( ! preg_match( $pattern, $this->value ) ){
				$this->value = ( isset( $this->current ) ) ? $this->current : '';
				$this->error = $this->field;
				return false;
			}

		}

		// gradient
		// only HEX validation

		if( is_array( $this->value ) ){

			foreach( $this->value as $k => $value ){

				if( isset( $this->error ) ){
					continue;
				}

				if($value[0] != '#'){
					$this->value[$k] = ( isset( $this->current[$k] ) ) ? $this->current[$k] : '';
					$this->error = $this->field;
					continue;
				}

				if(strlen($value) != 7){
					$this->value[$k] = ( isset( $this->current[$k] ) ) ? $this->current[$k] : '';
					$this->error = $this->field;
				}

			}

		}

	}

}

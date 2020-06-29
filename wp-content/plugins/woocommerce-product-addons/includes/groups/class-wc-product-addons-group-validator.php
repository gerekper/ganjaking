<?php

class WC_Product_Addons_Group_Validator {

	/**
	 * Validates that the passed update is valid to apply to a global add-ons group
	 *
	 * @since 2.9.0
	 *
	 * @param array $update
	 * @throws Exception If the passed update is invalid for any reason
	 * @return true
	 */
	public static function is_valid_global_addons_group_update( $update ) {
		$schema = array(
			'name' => array(
				'required' => false,
				'validator' => 'is_non_empty_string'
			),
			'priority' => array(
				'required' => false,
				'validator' => 'is_positive_integer'
			),
			'restrict_to_categories' => array(
				'required' => false,
				'validator' => 'is_array_of_product_category_ids'
			),
			'fields' => array(
				'required' => false,
				'validator' => 'is_array_of_fields'
			)
		);

		return self::validate( $update, $schema );
	}

	/**
	 * Validates that the passed update is valid to apply to a product's add-ons
	 *
	 * @since 2.9.0
	 *
	 * @param array $update
	 * @throws Exception If the passed update is invalid for any reason
	 * @return true
	 */
	public static function is_valid_product_addons_update( $update ) {
		$schema = array(
			'exclude_global_add_ons' => array(
				'required' => false,
				'validator' => 'is_zero_or_one'
			),
			'fields' => array(
				'required' => false,
				'validator' => 'is_array_of_fields'
			)
		);

		return self::validate( $update, $schema );
	}

	///////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Validates the passed data against the passed schema.  Note: Used recursively.
	 *
	 * @since 2.9.0
	 *
	 * @param array $data The data to validate
	 * @param array $schema The schema against which to validate it
	 * @throws Exception If the passed data fails validation
	 * @return true
	 */
	protected static function validate( $data, $schema ) {
		// First, make sure data is an array
		if ( ! is_array( $data ) ) {
			$type = gettype( $data );
			throw new Exception( "Data must be provided as an array. ({$type} received.)" );
		}

		// Then, make sure each required key is present
		foreach ( $schema as $key => $key_schema ) {
			if ( $key_schema['required'] && ! array_key_exists( $key, $data ) ) {
				throw new Exception( "Required key '{$key}' was not provided." );
			}
		}

		// Make sure each key present is expected
		$data_keys = array_keys( $data );
		foreach ( $data_keys as $data_key ) {
			if ( ! in_array( $data_key, array_keys( $schema ) ) ) {
				throw new Exception( "Unexpected key '{$data_key}' was provided." );
			}
		}

		// Lastly, for each key present, run its validator
		foreach ( $data_keys as $data_key ) {
			try {
				call_user_func( array( __CLASS__, $schema[ $data_key ][ 'validator' ] ), $data[ $data_key ] );
			}
			catch ( Exception $e ) {
				throw new Exception( "Invalid value given for '{$data_key}': " . $e->getMessage() );
			}
		}

		// No exceptions? Good to go!
		return true;
	}

	/**
	 * Validates that the passed argument is a string. Used in validation schemas.
	 *
	 * @since 2.9.0
	 *
	 * @param string $arg The data to validate
	 * @throws Exception If the passed data is not a string
	 * @return true
	 */
	protected static function is_string( $arg ) {
		if ( ! is_string( $arg ) ) {
			throw new Exception( 'String expected.' );
		}
		return true;
	}

	/**
	 * Validates that the passed argument is a NON EMPTY string. Used in validation schemas.
	 *
	 * @since 2.9.0
	 *
	 * @param string $arg The data to validate
	 * @throws Exception If the passed data is not a string or is empty
	 * @return true
	 */
	protected static function is_non_empty_string( $arg ) {
		if ( ! is_string( $arg ) ) {
			throw new Exception( 'String expected.' );
		}
		if ( empty( $arg ) ) {
			throw new Exception( 'Non-empty string expected.' );
		}
		return true;
	}

	/**
	 * Validates that the passed argument is a 0 or a 1. Used in validation schemas.
	 * Note that "false" in JSON will arrive as an empty string, so we also allow that.
	 * Also note that "true" in JSON will arrive as a "1".
	 *
	 * @since 2.9.0
	 *
	 * @param string|integer|boolean $arg The data to validate
	 * @throws Exception If the passed data is not a 0 or 1
	 * @return true
	 */
	protected static function is_zero_or_one( $arg ) {
		if ( empty( $arg ) ) {
			return true;
		}
		if ( is_numeric( $arg ) ) {
			$intval = intval( $arg );
			if ( 0 === $intval || 1 === $intval ) {
				return true;
			}
		}
		throw new Exception( 'A 0 or 1 was expected.' );
	}

	/**
	 * Validates that the passed argument is an array. The array can be empty. Used in validation schemas.
	 *
	 * @since 2.9.0
	 *
	 * @param array $arg The data to validate
	 * @throws Exception If the passed data is not an array
	 * @return true
	 */
	protected static function is_array( $arg ) {
		if ( ! is_array( $arg ) ) {
			throw new Exception( 'An array was expected.' );
		}
		return true;
	}

	/**
	 * Validates that the passed argument is a positive integer (zero is also acceptable). Floating point
	 * numbers are NOT acceptable. Used in validation schemas.
	 *
	 * @since 2.9.0
	 *
	 * @param integer $arg The data to validate
	 * @throws Exception If the passed data is not a positive integer
	 * @return true
	 */
	protected static function is_positive_integer( $arg ) {
		if ( ! is_numeric( $arg ) ) {
			throw new Exception( 'Number expected.' );
		}
		if ( is_float( $arg ) ) {
			throw new Exception( 'Floating point number was provided. Integer expected.' );
		}
		if ( 0 > intval( $arg ) ) {
			throw new Exception( 'Negative integer was provided. Positive integer expected.' );
		}
		return true;
	}

	/**
	 * Validates that the passed argument is empty or numeric. Integer AND floating point
	 * numbers ARE acceptable. Used in validation schemas.
	 *
	 * @since 2.9.0
	 *
	 * @param integer|float|string $arg The data to validate
	 * @throws Exception If the passed data is not empty or numeric
	 * @return true
	 */
	protected static function is_empty_or_numeric( $arg ) {
		if ( ! empty( $arg ) && ! is_numeric( $arg ) ) {
			throw new Exception( 'Number (or empty string) expected.' );
		}
		return true;
	}

	/**
	 * Validates that the passed argument is empty or an integer. Floating point
	 * numbers are NOT acceptable. Used in validation schemas.
	 *
	 * @since 2.9.0
	 *
	 * @param integer|string $arg The data to validate
	 * @throws Exception If the passed data is not empty or an integer
	 * @return true
	 */
	protected static function is_empty_or_integer( $arg ) {
		if ( ! empty( $arg ) && ! is_numeric( $arg ) ) {
			throw new Exception( 'Integer (or empty string) expected.' );
		}
		if ( is_float( $arg ) ) {
			throw new Exception( 'Floating point number was provided. Integer expected.' );
		}
		return true;
	}

	/**
	 * Validates that the passed argument is an array of valid product category IDs. An empty array
	 * IS acceptable. Used in validation schemas.
	 *
	 * @since 2.9.0
	 *
	 * @param array $arg The data to validate
	 * @throws Exception If the passed data is not an array of valid product category IDs
	 * @return true
	 */
	protected static function is_array_of_product_category_ids( $arg ) {
		if ( ! is_array( $arg ) ) {
			throw new Exception( 'Array expected.' );
		}

		$terms = get_terms( 'product_cat', array( 'hide_empty' => 0 ) );
		$term_ids = array();
		foreach ( $terms as $term ) {
			$term_ids[] = $term->term_id;
		}

		foreach ( $arg as $item ) {
			if ( ! is_numeric( $item ) ) {
				throw new Exception( "Invalid (non numeric) product category ID ({$item}) provided." );
			}

			if ( ! in_array( $item, $term_ids ) ) {
				throw new Exception( "ID provided ({$item}) is not a valid product category ID." );
			}
		}

		return true;
	}

	/**
	 * Validates that the passed argument is an array of add-on fields. An empty array
	 * IS acceptable. This also validates each option in the field's options array against
	 * the field type. Used in validation schemas.
	 *
	 * @since 2.9.0
	 *
	 * @param array $arg The data to validate
	 * @throws Exception If the passed data is not an array of add-on fields
	 * @return true
	 */
	protected static function is_array_of_fields( $arg ) {
		if ( ! is_array( $arg ) ) {
			throw new Exception( 'Array expected for fields.' );
		}

		// Note: Since fields (and the options within) have no IDs, we require ALL fields to be present for an update
		// (since we are going to replace the fields and options completely)

		$schema = array(
			'name' => array(
				'required' => true,
				'validator' => 'is_non_empty_string'
			),
			'description' => array(
				'required' => true,
				'validator' => 'is_string'
			),
			'type' => array(
				'required' => true,
				'validator' => 'is_field_type'
			),
			'position' => array(
				'required' => true,
				'validator' => 'is_positive_integer'
			),
			'options' => array(
				'required' => true,
				'validator' => 'is_array_of_basic_options'
			),
			'required' => array(
				'required' => true,
				'validator' => 'is_zero_or_one'
			)
		);

		foreach ( $arg as $field ) {
			// If a type was given (as it should have been), we can do a better test than just is_array for options
			// (If not, this is going to blow up anyways and we'll catch that.)
			$options_validator = 'is_array_of_basic_options';
			if ( array_key_exists( 'type', $field ) ) {
				switch( $field['type'] ) {
					case 'custom':
					case 'custom_textarea':
					case 'custom_letters_only':
					case 'custom_digits_only':
					case 'custom_letters_or_digits':
					$options_validator = 'is_array_of_options_with_optional_integer_limits';
						break;
					case 'custom_price':
					case 'input_multiplier':
					$options_validator = 'is_array_of_options_with_optional_float_limits';
						break;
				}
			}

			$schema['options']['validator'] = $options_validator;

			// Run the validator
			self::validate( $field, $schema );
		}

		return true;
	}

	/**
	 * Validates that the passed argument is valid field type. Used in validation schemas.
	 *
	 * @since 2.9.0
	 *
	 * @param string $arg The data to validate
	 * @throws Exception If the passed data is not a valid field type
	 * @return true
	 */
	protected static function is_field_type( $arg ) {
		$supported_types = array(
			'checkbox', 'multiple_choice', 'custom', 'custom_textarea', 'custom_price', 'custom_letters_only',
			'custom_digits_only', 'custom_letters_or_digits', 'custom_email', 'input_multiplier', 'select',
			'file_upload'
		);

		if ( ! in_array( $arg, $supported_types ) ) {
			throw new Exception( "Invalid type {($arg}} provided for field." );
		}

		return true;
	}

	/**
	 * Validates that the passed argument is an array of basic options (i.e. options that only have
	 * a label and optional price.) An empty array IS acceptable. Used in validation schemas.
	 *
	 * @since 2.9.0
	 *
	 * @param array $arg The data to validate
	 * @throws Exception If the passed data is not an array of basic options
	 * @return true
	 */
	protected static function is_array_of_basic_options( $arg ) {
		if ( ! is_array( $arg ) ) {
			throw new Exception( 'Array expected for options.' );
		}

		$schema = array(
			'label' => array(
				'required' => true,
				'validator' => 'is_non_empty_string'
			),
			'price' => array(
				'required' => false,
				'validator' => 'is_empty_or_numeric'
			)
		);

		foreach ( $arg as $option ) {
			self::validate( $option, $schema );
		}

		return true;
	}

	/**
	 * Validates that the passed argument is an array of options with optional integer limits.
	 * An empty array IS acceptable. Used in validation schemas.
	 *
	 * @since 2.9.0
	 *
	 * @param array $arg The data to validate
	 * @throws Exception If the passed data is not an array of options with integer limits
	 * @return true
	 */
	protected static function is_array_of_options_with_optional_integer_limits( $arg ) {
		if ( ! is_array( $arg ) ) {
			throw new Exception( 'Array expected for options.' );
		}

		$schema = array(
			'label' => array(
				'required' => true,
				'validator' => 'is_non_empty_string'
			),
			'price' => array(
				'required' => false,
				'validator' => 'is_empty_or_numeric'
			),
			'min' => array(
				'required' => false,
				'validator' => 'is_empty_or_integer'
			),
			'max' => array(
				'required' => false,
				'validator' => 'is_empty_or_integer'
			)
		);

		foreach ( $arg as $option ) {
			self::validate( $option, $schema );
		}

		return true;
	}

	/**
	 * Validates that the passed argument is an array of options with optional float limits.
	 * An empty array IS acceptable. Used in validation schemas.
	 *
	 * @since 2.9.0
	 *
	 * @param array $arg The data to validate
	 * @throws Exception If the passed data is not an array of options with float limits
	 * @return true
	 */
	protected static function is_array_of_options_with_optional_float_limits( $arg ) {
		if ( ! is_array( $arg ) ) {
			throw new Exception( 'Array expected for options.' );
		}

		$schema = array(
			'label' => array(
				'required' => true,
				'validator' => 'is_non_empty_string'
			),
			'price' => array(
				'required' => false,
				'validator' => 'is_empty_or_numeric'
			),
			'min' => array(
				'required' => false,
				'validator' => 'is_empty_or_numeric'
			),
			'max' => array(
				'required' => false,
				'validator' => 'is_empty_or_numeric'
			)
		);

		foreach ( $arg as $option ) {
			self::validate( $option, $schema );
		}

		return true;
	}
}

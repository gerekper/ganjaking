<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the settings conversion from pre 3.0.
 *
 * @since 3.0.0
 */
class WC_Product_Addons_3_0_Conversion_Helper {
	/**
	 * Performs the conversion.
	 *
	 * @since 3.0.0
	 * @param array $addon_fields
	 * @return array $updated_addon_fields Updated add-on fields.
	 */
	public static function do_conversion( $addon_fields ) {
		$special_field_types = array(
			'custom',
			'custom_email',
			'custom_letters_only',
			'custom_digits_only',
			'custom_letters_or_digits',
			'file_upload',
			'custom_textarea',
			'input_multiplier',
			'custom_price',
		);

		$updated_addon_fields = array();
		$create_heading       = false;

		foreach ( $addon_fields as $field ) {
			/*
			 * We need to create separate headings for these types.
			 */
			if ( in_array( $field['type'], $special_field_types ) ) {
				$updated_addon_fields[] = self::generate_heading( $field );
			}

			/*
			 * Check if there are more than one option and
			 * if there is, we need to break those out into
			 * its own field set. For specific types only.
			 */
			if ( in_array( $field['type'], $special_field_types ) && isset( $field['options'] ) ) {
				foreach ( $field['options'] as $option ) {
					// If restrictions is enabled and minimum is greater than 0, make required.
					if ( ! empty( $option['min'] ) && 0 < $option['min'] ) {
						$field['required'] = 1;
					}

					$name = ! empty( $option['label'] ) ? $option['label'] : '';

					$updated_addon_fields[] = array(
						'name'               => ! empty( $name ) ? $name : $field['name'],
						'title_format'       => empty( $option['label'] ) ? 'hide' : 'label',
						'type'               => self::convert_type( $field['type'] ),
						'restrictions_type'  => self::convert_restrictions( $field['type'] ),
						'description'        => '',
						'description_enable' => 0,
						'required'           => ! empty( $field['required'] ) ? $field['required'] : 0,
						'min'                => ! empty( $option['min'] ) ? $option['min'] : '',
						'max'                => ! empty( $option['max'] ) ? $option['max'] : '',
						'restrictions'       => ( ! empty( $option['min'] ) || ! empty( $option['max'] ) ) ? 1 : 0,
						'price'              => ! empty( $option['price'] ) ? $option['price'] : '',
						'price_type'         => 'quantity_based',
						'adjust_price'       => ! empty( $option['price'] ) ? 1 : 0,
					);
				}
			} else {
				$updated_addon_fields[] = self::convert( $field );
			}
		}

		return $updated_addon_fields;
	}

	/**
	 * Converts the field type to restrictions.
	 *
	 * @since 3.0.0
	 * @param string $field_type
	 * @return string $restrictions_type
	 */
	public static function convert_restrictions( $field_type ) {
		$restrictions_type = '';

		switch ( $field_type ) {
			case 'custom':
				$restrictions_type = 'any_text';
				break;
			case 'custom_email':
				$restrictions_type = 'email';
				break;
			case 'custom_letters_only':
				$restrictions_type = 'only_letters';
				break;
			case 'custom_digits_only':
				$restrictions_type = 'only_numbers';
				break;
			case 'custom_letters_or_digits':
				$restrictions_type = 'only_letters_numbers';
				break;
		}

		return $restrictions_type;
	}

	/**
	 * Converts the field type.
	 *
	 * @since 3.0.0
	 * @param string $previous_type
	 * @return string $new_type
	 */
	public static function convert_type( $previous_type ) {
		switch ( $previous_type ) {
			case 'custom':
			case 'custom_email':
			case 'custom_letters_only':
			case 'custom_digits_only':
			case 'custom_letters_or_digits':
				$new_type = 'custom_text';
				break;
			case 'file_upload':
				$new_type = 'file_upload';
				break;
			case 'custom_textarea':
				$new_type = 'custom_textarea';
				break;
			case 'input_multiplier':
				$new_type = 'input_multiplier';
				break;
			case 'custom_price':
				$new_type = 'custom_price';
				break;
			case 'checkbox':
				$new_type = 'checkbox';
				break;
			case 'select':
				$new_type = 'select';
				break;
			case 'radiobutton':
				$new_type = 'radiobutton';
				break;
		}

		return $new_type;
	}

	/**
	 * Adds a heading field.
	 *
	 * @since 3.0.0
	 * @param array $field
	 * @return array $heading
	 */
	public static function generate_heading( $field ) {
		$heading = array(
			'name'        => $field['name'],
			'type'        => 'heading',
			'description' => $field['description'],
		);

		if ( ! empty( $field['description'] ) ) {
			$heading['description_enable'] = 1;
		}

		return $heading;
	}

	/**
	 * Converts the field to 3.0 compatible.
	 *
	 * @since 3.0.0
	 * @param array $field Field setting.
	 * @return array Converted field setting data.
	 */
	public static function convert( $field = array() ) {
		$field['title_format'] = 'heading';

		if ( ! empty( $field['description'] ) ) {
			$field['description_enable'] = 1;
		}

		if ( ! empty( $field['required'] ) ) {
			$field['required'] = 1;
		} else {
			$field['required'] = 0;
		}

		switch ( $field['type'] ) {
			case 'checkbox':
				// Loop through each options for this field type.
				foreach ( $field['options'] as $key => $option ) {
					$field['options'][ $key ]['price_type'] = 'quantity_based';
					unset( $field['options'][ $key ]['min'] );
					unset( $field['options'][ $key ]['max'] );
				}

				break;
			case 'select':
			case 'radiobutton':
				$field['display'] = $field['type'];
				$field['type']    = 'multiple_choice';

				// Loop through each options for this field type.
				foreach ( $field['options'] as $key => $option ) {
					$field['options'][ $key ]['price_type'] = 'quantity_based';
					unset( $field['options'][ $key ]['min'] );
					unset( $field['options'][ $key ]['max'] );
				}

				break;
			case 'custom_textarea':
			case 'input_multiplier':
				if ( isset( $field['options'] ) ) {
					// Loop through each options for this field type.
					foreach ( $field['options'] as $key => $option ) {
						if ( ! empty( $field['min'] ) ) {
							$field['min']          = $option['min'];
							$field['restrictions'] = 1;
						}

						if ( ! empty( $field['max'] ) ) {
							$field['max']          = $option['max'];
							$field['restrictions'] = 1;
						}

						if ( ! empty( $option['price'] ) ) {
							$field['adjust_price'] = 1;
						}

						$field['price_type'] = 'quantity_based';
						$field['price']      = $option['price'];

						unset( $field['options'][ $key ]['min'] );
						unset( $field['options'][ $key ]['max'] );
					}
				}
				
				break;
			case 'custom_price':
				if ( isset( $field['options'] ) ) {
					// Loop through each options for this field type.
					foreach ( $field['options'] as $key => $option ) {
						if ( ! empty( $field['min'] ) ) {
							$field['min']          = $option['min'];
							$field['restrictions'] = 1;
						}

						if ( ! empty( $field['max'] ) ) {
							$field['max']          = $option['max'];
							$field['restrictions'] = 1;
						}

						unset( $field['options'][ $key ]['min'] );
						unset( $field['options'][ $key ]['max'] );
					}
				}
				
				break;
		}

		return $field;
	}
}

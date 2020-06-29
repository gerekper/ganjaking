<?php
/**
 * Custom fields (text)
 */
class Product_Addon_Field_Custom extends Product_Addon_Field {

	/**
	 * Validate an addon
	 * @return bool pass, or WP_Error
	 */
	public function validate() {
		foreach ( $this->addon['options'] as $key => $option ) {
			$option_key = empty( $option['label'] ) ? $key : sanitize_title( $option['label'] );
			$posted     = isset( $this->value[ $option_key ] ) ? $this->value[ $option_key ] : '';
			$posted     = apply_filters( 'woocommerce_product_addons_validate_value', $posted, $this );

			// Required addon checks
			if ( ! empty( $this->addon['required'] ) && empty( $posted ) ) {
				return new WP_Error( 'error', sprintf( __( '"%s" is a required field.', 'woocommerce-product-addons' ), $this->addon['name'] ) );
			}

			// Min, max checks

			switch ( $this->addon['type'] ) {
				case "custom" :
				case "custom_textarea" :
				case "custom_letters_only" :
				case "custom_digits_only" :
				case "custom_letters_or_digits" :
					if ( ! empty( $option['min'] ) && ! empty( $posted ) && mb_strlen( $posted, 'UTF-8' ) < $option['min'] ) {
						return new WP_Error( 'error', sprintf( __( 'The minimum allowed length for "%s - %s" is %s.', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'], $option['min'] ) );
					}

					if ( ! empty( $option['max'] ) && ! empty( $posted ) && mb_strlen( $posted, 'UTF-8' ) > $option['max'] ) {
						return new WP_Error( 'error', sprintf( __( 'The maximum allowed length for "%s - %s" is %s.', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'], $option['max'] ) );
					}
				break;
				case "custom_price" :
				case "input_multiplier" :
					if ( ! empty( $option['min'] ) && ! empty( $posted ) && $posted < $option['min'] || ( isset( $option['min'] ) && $posted < $option['min'] ) ) {
						if ( ! empty( $this->addon['required'] ) ) {
							return new WP_Error( 'error', sprintf( __( 'The minimum allowed amount for "%s - %s" is %s.', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'], $option['min'] ) );
						}
					}

					if ( ! empty( $option['max'] ) && ! empty( $posted ) && $posted > $option['max'] ) {
						if ( ! empty( $this->addon['required'] ) ) {
							return new WP_Error( 'error', sprintf( __( 'The maximum allowed amount for "%s - %s" is %s.', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'], $option['max'] ) );
						}
					}
				break;
			}

			// Other option specific checks

			switch ( $this->addon['type'] ) {
				case "input_multiplier" :
					$posted = absint( $posted );
					if ( $posted < 0 ) {
						return new WP_Error( 'error', sprintf( __( 'Please enter a value greater than 0 for "%s - %s".', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'] ) );
					}
				break;
				case "custom_letters_only" :
					if ( 1 !== preg_match( '/^[A-Z ]*$/i', $posted ) ) {
						return new WP_Error( 'error', sprintf( __( 'Only letters are allowed for "%s - %s".', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'] ) );
					}
				break;
				case "custom_digits_only" :
					if ( 1 !== preg_match( '/^[0-9]*$/', $posted ) ) {
						return new WP_Error( 'error', sprintf( __( 'Only digits are allowed for "%s - %s".', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'] ) );
					}
				break;
				case "custom_letters_or_digits" :
					if ( 1 !== preg_match( '/^[A-Z0-9 ]*$/i', $posted ) ) {
						return new WP_Error( 'error', sprintf( __( 'Only letters and digits are allowed for "%s - %s".', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'] ) );
					}
				break;
				case "custom_email" :
					if ( ! empty( $posted ) && ! is_email( $posted ) ) {
						return new WP_Error( 'error', sprintf( __( 'A valid email address is required for "%s - %s".', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'] ) );
					}
				break;
			}

		}
		return true;
	}

	/**
	 * Process this field after being posted
	 * @return array on success, WP_ERROR on failure
	 */
	public function get_cart_item_data() {
		$cart_item_data           = array();

		foreach ( $this->addon['options'] as $key => $option ) {
			$option_key = empty( $option['label'] ) ? $key : sanitize_title( $option['label'] );
			$posted     = isset( $this->value[ $option_key ] ) ? $this->value[ $option_key ] : '';

			if ( '' === $posted ) {
				continue;
			}

			$label = $this->get_option_label( $option );
			$price = $this->get_option_price( $option );

			switch ( $this->addon['type'] ) {
				case 'custom_price' :
					$price = floatval( sanitize_text_field( $posted ) );

					if ( $price >= 0 ) {
						$cart_item_data[] = array(
							'name'     => $label,
							'value'    => $price,
							'price'    => $price,
							'display'  => strip_tags( wc_price( $price ) ),
						);
					}
				break;
				case 'input_multiplier' :
					$posted = absint( $posted );

					if ( 0 < $posted ) {
						$cart_item_data[] = array(
							'name'   => $label,
							'value'  => $posted,
							'price'  => $posted * $price,
						);
					}
				break;
				default :
					$cart_item_data[] = array(
						'name'   => $label,
						'value'  => wp_kses_post( $posted ),
						'price'  => $price,
					);
				break;
			}
		}

		return $cart_item_data;
	}

}

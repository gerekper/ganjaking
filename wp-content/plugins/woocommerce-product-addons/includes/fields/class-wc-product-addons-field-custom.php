<?php
/**
 * Custom fields (text)
 */
class WC_Product_Addons_Field_Custom extends WC_Product_Addons_Field {
	/**
	 * Validate an addon
	 * @return bool pass, or WP_Error
	 */
	public function validate() {
		$posted = isset( $this->value ) ? $this->value : '';
		$posted = apply_filters( 'woocommerce_product_addons_validate_value', $posted, $this );

		// Required addon checks
		if ( ! empty( $this->addon['required'] ) && '' === $posted ) {
			/* translators: %s Name of the addon */
			return new WP_Error( 'error', sprintf( __( '"%s" is a required field.', 'woocommerce-product-addons' ), $this->addon['name'] ) );
		}

		if ( '1' == $this->addon['restrictions'] ) {
			// Min, max checks
			switch ( $this->addon['type'] ) {
				case 'custom_text':
				case 'custom_textarea':
					if ( ! empty( $this->addon['min'] ) && '' !== $posted && mb_strlen( $posted, 'UTF-8' ) < $this->addon['min'] ) {
						/* translators: 1 Addon name 2 Minimum amount */
						return new WP_Error( 'error', sprintf( __( 'The minimum characters required for "%1$s" is %2$s.', 'woocommerce-product-addons' ), $this->addon['name'], $this->addon['min'] ) );
					}

					if ( ! empty( $this->addon['max'] ) && '' !== $posted && mb_strlen( $posted, 'UTF-8' ) > $this->addon['max'] ) {
						/* translators: 1 Addon name 2 Maximum amount */
						return new WP_Error( 'error', sprintf( __( 'The maximum allowed characters for "%1$s" is %2$s.', 'woocommerce-product-addons' ), $this->addon['name'], $this->addon['max'] ) );
					}
					break;
				case 'custom_price':
				case 'input_multiplier':
					if ( ! empty( $this->addon['min'] ) && '' !== $posted && $posted < $this->addon['min'] || ( isset( $this->addon['min'] ) && $posted < $this->addon['min'] ) ) {
						if ( ! empty( $this->addon['required'] ) ) {
							/* translators: 1 Addon name 2 minimum amount */
							return new WP_Error( 'error', sprintf( __( 'The minimum amount required for "%1$s" is %2$s.', 'woocommerce-product-addons' ), $this->addon['name'], $this->addon['min'] ) );
						}
					}

					if ( ! empty( $this->addon['max'] ) && '' !== $posted && $posted > $this->addon['max'] ) {
						if ( ! empty( $this->addon['required'] ) ) {
							/* translators: 1 Addon name 2 Maximum amount */
							return new WP_Error( 'error', sprintf( __( 'The maximum allowed amount for "%1$s" is %2$s.', 'woocommerce-product-addons' ), $this->addon['name'], $this->addon['max'] ) );
						}
					}
					break;
			}
		}

		// Other option specific checks

		switch ( $this->addon['type'] ) {
			case 'input_multiplier':
				$posted = absint( $posted );
				if ( $posted < 0 ) {
					/* translators: %s Addon name */
					return new WP_Error( 'error', sprintf( __( 'Please enter a value greater than 0 for "%s".', 'woocommerce-product-addons' ), $this->addon['name'] ) );
				}
				break;
		}

		return true;
	}

	/**
	 * Process this field after being posted
	 * @return array on success, WP_ERROR on failure
	 */
	public function get_cart_item_data() {
		$cart_item_data = array();
		$posted         = isset( $this->value ) ? $this->value : '';

		if ( '' === $posted ) {
			return $cart_item_data;
		}

		$label        = sanitize_text_field( $this->addon['name'] );
		$price        = floatval( sanitize_text_field( $this->addon['price'] ) );
		$adjust_price = $this->addon['adjust_price'];

		switch ( $this->addon['type'] ) {
			case 'custom_price':
				$price = floatval( sanitize_text_field( $posted ) );

				if ( 0 <= $price ) {
					$cart_item_data[] = array(
						'name'     => $label,
						'value'    => '',
						'price'    => floatval( sanitize_text_field( $price ) ),
						'display'  => strip_tags( wc_price( $price ) ),
						'field_name' => $this->addon['field_name'],
						'field_type' => $this->addon['type'],
						'price_type' => $this->addon['price_type'],
					);
				}
				break;
			case 'input_multiplier':
				$posted = absint( $posted );

				if ( 0 < $posted ) {
					$cart_item_data[] = array(
						'name'   => $label,
						'value'  => $posted,
						'price'  => '1' != $adjust_price ? 0 : floatval( sanitize_text_field( $price * $posted ) ),
						'field_name' => $this->addon['field_name'],
						'field_type' => $this->addon['type'],
						'price_type' => $this->addon['price_type'],
					);
				}
				break;
			default:
				$cart_item_data[] = array(
					'name'   => $label,
					'value'  => wp_kses_post( $posted ),
					'price'  => '1' != $adjust_price ? 0 : floatval( sanitize_text_field( $price ) ),
					'field_name' => $this->addon['field_name'],
					'field_type' => $this->addon['type'],
					'price_type' => $this->addon['price_type'],
				);
				break;
		}

		return $cart_item_data;
	}
}

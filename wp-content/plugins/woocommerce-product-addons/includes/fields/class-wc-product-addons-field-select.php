<?php
/**
 * Select field
 */
class WC_Product_Addons_Field_Select extends WC_Product_Addons_Field {

	/**
	 * Validate an addon
	 * @return bool pass or fail, or WP_Error
	 */
	public function validate() {
		if ( ! empty( $this->addon['required'] ) ) {
			if ( empty( $this->value ) ) {
				return new WP_Error( 'error', sprintf( __( '"%s" is a required field.', 'woocommerce-product-addons' ), $this->addon['name'] ) );
			}
		}
		return true;
	}

	/**
	 * Process this field after being posted
	 * @return array on success, WP_ERROR on failure
	 */
	public function get_cart_item_data() {
		$cart_item_data = array();

		if ( empty( $this->value ) ) {
			return false;
		}

		$chosen_option = '';
		$loop          = 0;

		foreach ( $this->addon['options'] as $option ) {
			$loop++;
			if ( sanitize_title( $option['label'] . '-' . $loop ) == $this->value ) {
				$chosen_option = $option;
				break;
			}
		}

		if ( ! $chosen_option ) {
			return false;
		}

		$cart_item_data[] = array(
			'name'  => sanitize_text_field( $this->addon['name'] ),
			'value' => $chosen_option['label'],
			'price' => floatval( sanitize_text_field( $this->get_option_price( $chosen_option ) ) ),
			'field_name' => $this->addon['field_name'],
			'field_type' => $this->addon['type'],
			'price_type' => $chosen_option['price_type'],
		);

		return $cart_item_data;
	}
}
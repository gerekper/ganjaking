<?php
/**
 * Select field
 */
class Product_Addon_Field_Select extends Product_Addon_Field {

	/**
	 * Validate an addon
	 * @return bool pass or fail, or WP_Error
	 */
	public function validate() {
		if ( ! empty( $this->addon['required'] ) ) {
			if ( ! $this->value || sizeof( $this->value ) == 0 ) {
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
			'name'  => $this->addon['name'],
			'value' => $chosen_option['label'],
			'price' => $this->get_option_price( $chosen_option )
		);

		return $cart_item_data;
	}
}
<?php

class WC_GFPA_Cart_Validation {
	private static WC_GFPA_Cart_Validation $instance;

	public static function get_instance(): WC_GFPA_Cart_Validation {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WC_GFPA_Cart_Validation();
		}

		return self::$instance;
	}

	public static function register() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WC_GFPA_Cart_Validation();
		}
	}

	protected function __construct() {
		add_action( 'woocommerce_check_cart_items', [ $this, 'check_cart_items' ] );
	}

	/**
	 * Check the cart items to see if any of the forms that are attached to the products have changed.
	 * If they have, revalidate the entry.
	 *
	 * Uses the _gravity_form_hash to determine if the form has changed.
	 *
	 * @throws Exception
	 */
	public function check_cart_items() {

		if ( ! apply_filters( 'woocommerce_gforms_check_cart_items', true ) ) {
			return;
		}

		$cart = WC()->cart->get_cart();

		//Find items in the cart that have a hashed value for a form.
		$cart_items_with_gf = array_filter( $cart, function ( $cart_item ) {
			return isset( $cart_item['_gravity_form_hash'] );
		} );

		$cart_updated = false;
		$notices      = [];
		//Loop through each item in the cart that has a gravity form attached to it.
		foreach ( $cart_items_with_gf as $cart_item_key => $cart_item ) {

			$form             = GFAPI::get_form( intval( $cart_item['_gravity_form_data']['id'] ?? 0 ) );
			$form_fields_hash = wc_gfpa()->get_form_field_hash( $form['id'] );

			// The _gravity_form_hash is a hash of the form fields. If the form fields have changed, we need to revalidate the entry.
			// The _gravity_form_hash is set in the WC_GFPA_Product_Addons::add_cart_item_data() method.
			$cart_item_form_field_hash = $cart_item['_gravity_form_hash'];

			if ( $form_fields_hash !== $cart_item_form_field_hash ) {
				GFCommon::log_debug( __METHOD__ . '(): Form fields have changed for product ' . $cart_item['product_id'] . '. Revalidating.' );

				// Use the submission helper to revalidate the data.
				$validation_result = WC_GFPA_Submission_Helpers::revalidate_entry( $form['id'], $cart_item['_gravity_form_lead'] );
				$is_valid          = $validation_result['is_valid'] ?? false;

				if ( is_wp_error( $validation_result ) || ! $is_valid ) {
					GFCommon::log_debug( __METHOD__ . '(): Form fields have changed for product ' . $cart_item['product_id'] . '. Revalidation failed.' );

					// Add a notice that the item is no longer valid.
					$product_name = $cart_item['data']->get_name();
					$notices[]    = sprintf( __( 'Sorry, %s needs to be reconfigured. Please remove it from your cart and try again.', 'wc_gf_addons' ), $product_name );
				} else {
					GFCommon::log_debug( __METHOD__ . '(): Form fields have changed for product ' . $cart_item['product_id'] . '. Revalidation passed.' );

					// Generate a new hash and update the WC cart we don't have to revalidate it again, unless the form changes again.
					WC()->cart->cart_contents[ $cart_item_key ]['_gravity_form_hash'] = $form_fields_hash;
					$cart_updated                                                     = true;
				}
			}
		}

		if ( ! empty( $notices ) ) {
			foreach ( $notices as $notice ) {
				wc_add_notice( $notice, 'error' );
			}
		}

		if ( $cart_updated ) {
			GFCommon::log_debug( __METHOD__ . "(): [woocommerce-gravityforms-product-addons] Cart Updated with new hashes for modified forms." );
		}
	}
}

<?php
/**
 * Add custom fields to the Store Credit product.
 *
 * @package WC_Store_Credit
 * @since   3.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Store Credit product add-ons class.
 */
class WC_Store_Credit_Product_Addons {

	/**
	 * Init.
	 *
	 * @since 3.2.0
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'product_content' ) );
		add_filter( 'woocommerce_add_to_cart_validation', array( __CLASS__, 'validate_add_cart_item' ), 20, 2 );
		add_filter( 'woocommerce_add_cart_item_data', array( __CLASS__, 'add_cart_item_data' ), 10, 2 );
		add_filter( 'woocommerce_get_item_data', array( __CLASS__, 'get_item_data' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'order_line_item' ), 10, 3 );
	}

	/**
	 * Gets if the specified Store Credit product allows sending the credit to a different person.
	 *
	 * @since 3.2.0
	 *
	 * @param mixed $the_product Post object or post ID of the product.
	 * @return bool
	 */
	public static function allow_different_receiver( $the_product ) {
		$product = wc_store_credit_get_product( $the_product );

		if ( ! $product || ! $product->is_type( 'store_credit' ) ) {
			return false;
		}

		$data = $product->get_meta( '_store_credit_data' );

		return ( is_array( $data ) && ( empty( $data['allow_different_receiver'] ) || wc_string_to_bool( $data['allow_different_receiver'] ) ) );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 3.2.0
	 *
	 * @global WP_Post $post Current post.
	 */
	public static function enqueue_scripts() {
		global $post;

		if ( is_product() && self::allow_different_receiver( $post->ID ) ) {
			$suffix = wc_store_credit_get_scripts_suffix();

			wp_enqueue_style( 'wc-store-credit-single-product', WC_STORE_CREDIT_URL . 'assets/css/single-product.css', array(), WC_STORE_CREDIT_VERSION );
			wp_enqueue_script( 'wc-store-credit-single-product', WC_STORE_CREDIT_URL . "assets/js/frontend/single-product{$suffix}.js", array( 'jquery' ), WC_STORE_CREDIT_VERSION, true );
		}
	}

	/**
	 * Gets the store credit product receiver fields.
	 *
	 * @since 3.2.0
	 *
	 * @return array
	 */
	public static function get_receiver_fields() {
		$fields = array(
			'store_credit_receiver_email' => array(
				'type'        => 'email',
				'label'       => _x( 'Send to', 'product field label', 'woocommerce-store-credit' ),
				'placeholder' => _x( 'The receiver email', 'product field placeholder', 'woocommerce-store-credit' ),
				'required'    => true,
			),
			'store_credit_receiver_note'  => array(
				'type'        => 'textarea',
				'label'       => _x( 'Message', 'product field label', 'woocommerce-store-credit' ),
				'placeholder' => _x( 'Add a message', 'product field placeholder', 'woocommerce-store-credit' ),
			),
		);

		/**
		 * Filters the store credit product receiver fields.
		 *
		 * @since 3.2.0
		 */
		return apply_filters( 'wc_store_credit_product_receiver_fields', $fields );
	}

	/**
	 * Outputs the product content.
	 *
	 * @since 3.2.0
	 *
	 * @global WC_Product $product Product object.
	 */
	public static function product_content() {
		global $product;

		if ( ! self::allow_different_receiver( $product ) ) {
			return;
		}

		wc_store_credit_get_template( 'single-product/store-credit.php' );
	}

	/**
	 * Validates the custom data before adding the item to the cart.
	 *
	 * @since 3.2.0
	 *
	 * @param bool $passed     If passed validation.
	 * @param int  $product_id Product ID.
	 * @return bool
	 */
	public static function validate_add_cart_item( $passed, $product_id ) {
		// phpcs:disable WordPress.Security.NonceVerification
		if ( empty( $_POST['send-to-different-customer'] ) || ! self::allow_different_receiver( $product_id ) ) {
			return $passed;
		}

		$fields = self::get_receiver_fields();

		foreach ( $fields as $id => $field ) {
			$value = ( isset( $_POST[ $id ] ) ? wc_clean( wp_unslash( $_POST[ $id ] ) ) : '' );

			if ( ! empty( $field['required'] ) && ! $value ) {
				/* translators: %s: field label */
				wc_add_notice( sprintf( __( '"%s" is a required field.', 'woocommerce-store-credit' ), $field['label'] ), 'error' );
				return false;
			}
		}

		return $passed;
		// phpcs:enable WordPress.Security.NonceVerification
	}

	/**
	 * Adds custom data to the cart item.
	 *
	 * @since 3.2.0
	 *
	 * @param array $cart_item_data An array with the cart item data.
	 * @param int   $product_id     Product ID.
	 * @return array
	 */
	public static function add_cart_item_data( $cart_item_data, $product_id ) {
		// phpcs:disable WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( empty( $_POST['send-to-different-customer'] ) || ! self::allow_different_receiver( $product_id ) ) {
			return $cart_item_data;
		}

		$fields = self::get_receiver_fields();
		$values = array();

		foreach ( $fields as $id => $field ) {
			$key   = str_replace( 'store_credit_receiver_', '', $id );
			$value = ( isset( $_POST[ $id ] ) ? wp_unslash( $_POST[ $id ] ) : '' );

			switch ( $field['type'] ) {
				case 'textarea':
					$value = sanitize_textarea_field( $value );
					break;
				default:
					$value = sanitize_text_field( $value );
					break;
			}

			$values[ $key ] = $value;
		}

		$values = array_filter( $values );

		if ( ! empty( $values ) ) {
			$cart_item_data['store_credit_receiver'] = $values;
		}

		return $cart_item_data;
		// phpcs:enable WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	/**
	 * Gets the custom cart item data.
	 *
	 * @since 3.2.0
	 *
	 * @param array $item_data An array with the cart item data.
	 * @param array $cart_item Cart item.
	 * @return array
	 */
	public static function get_item_data( $item_data, $cart_item ) {
		if ( empty( $cart_item['store_credit_receiver'] ) ) {
			return $item_data;
		}

		$fields = self::get_receiver_fields();

		foreach ( $cart_item['store_credit_receiver'] as $key => $value ) {
			$id = "store_credit_receiver_{$key}";

			$item_data[ $id ] = array(
				'name'  => ( isset( $fields[ $id ]['label'] ) ? $fields[ $id ]['label'] : $id ),
				'value' => $value,
			);
		}

		return $item_data;
	}

	/**
	 * Saves the custom data in the order line item.
	 *
	 * @since 3.2.0
	 *
	 * @param WC_Order_Item_Product $item          Order item product.
	 * @param string                $cart_item_key Cart item key.
	 * @param array                 $values        An array with the cart item values.
	 */
	public static function order_line_item( $item, $cart_item_key, $values ) {
		if ( ! empty( $values['store_credit_receiver'] ) ) {
			$item->add_meta_data( '_store_credit_receiver', $values['store_credit_receiver'] );
		}
	}
}

WC_Store_Credit_Product_Addons::init();

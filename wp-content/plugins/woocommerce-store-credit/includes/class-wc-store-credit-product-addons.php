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
	 * Stores if there are errors with the product validation.
	 *
	 * @var bool
	 */
	private static $has_errors = false;

	/**
	 * Init.
	 *
	 * @since 3.2.0
	 */
	public static function init() {
		// Template hooks.
		add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'product_content' ) );
		add_action( 'wc_store_credit_single_product_content', array( __CLASS__, 'preset_amounts_content' ) );
		add_action( 'wc_store_credit_single_product_content', array( __CLASS__, 'custom_amount_content' ), 20 );
		add_action( 'wc_store_credit_single_product_content', array( __CLASS__, 'different_receiver_content' ), 30 );
		add_action( 'wp', array( __CLASS__, 'check_validation_errors' ) );

		add_filter( 'woocommerce_add_to_cart_validation', array( __CLASS__, 'validate_add_cart_item' ), 20, 2 );
		add_filter( 'woocommerce_add_cart_item_data', array( __CLASS__, 'add_cart_item_data' ), 10, 2 );
		add_filter( 'woocommerce_cart_item_price', array( __CLASS__, 'cart_item_price' ), 10, 2 );
		add_filter( 'woocommerce_get_item_data', array( __CLASS__, 'get_item_data' ), 10, 2 );
		add_action( 'woocommerce_before_calculate_totals', array( __CLASS__, 'before_calculate_totals' ) );
		add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'order_line_item' ), 10, 3 );
	}

	/**
	 * Checks if there are errors with the product validation.
	 *
	 * @since 4.0.3
	 */
	public static function check_validation_errors() {
		if ( is_product() && 0 < wc_notice_count( 'error' ) ) {
			self::$has_errors = true;
		}
	}

	/**
	 * Gets the Store Credit product.
	 *
	 * If the parameter isn't provided, it uses the current product.
	 *
	 * @since 4.0.0
	 *
	 * @global WC_Product $product Product object.
	 *
	 * @param mixed $the_product Optional. Post object or post ID of the product. Default false.
	 * @return WC_Store_Credit_Product|false
	 */
	protected static function get_store_credit_product( $the_product = false ) {
		global $product;

		if ( $the_product ) {
			$product = wc_store_credit_get_product( $the_product );
		}

		return ( $product instanceof WC_Store_Credit_Product ? $product : false );
	}

	/**
	 * Outputs the product content.
	 *
	 * @since 3.2.0
	 */
	public static function product_content() {
		if ( ! self::get_store_credit_product() ) {
			return;
		}

		wc_store_credit_get_template( 'single-product/store-credit.php' );
	}

	/**
	 * Outputs the preset amounts content.
	 *
	 * @since 4.5.0
	 */
	public static function preset_amounts_content() {
		$product = self::get_store_credit_product();

		if ( ! $product ) {
			return;
		}

		$preset_amounts = $product->get_preset_amounts();

		if ( ! $preset_amounts ) {
			return;
		}

		$args = array(
			'preset_amounts' => $preset_amounts,
			'allow_custom'   => $product->allow_custom_amount(),
		);

		wc_store_credit_get_template( 'single-product/store-credit/preset-amounts.php', $args );
	}

	/**
	 * Outputs the custom amount content.
	 *
	 * @since 4.0.0
	 */
	public static function custom_amount_content() {
		$product = self::get_store_credit_product();

		if ( ! $product || ( ! $product->allow_custom_amount() && ! $product->get_preset_amounts() ) ) {
			return;
		}

		$custom_attributes = array();
		$description       = '';

		if ( $product->allow_custom_amount() ) {
			$min_amount         = $product->get_min_custom_amount();
			$max_amount         = $product->get_max_custom_amount();
			$custom_amount_step = $product->get_custom_amount_step();

			if ( $min_amount > 0 ) {
				$custom_attributes['min'] = $min_amount;
			}

			if ( $max_amount > 0 ) {
				$custom_attributes['max'] = $max_amount;
			}

			if ( $custom_amount_step ) {
				$custom_attributes['step'] = $custom_amount_step;
			}

			if ( $min_amount > 0 || $max_amount > 0 ) {
				$min_amount_text = ( $min_amount > 0 ? wc_price( $min_amount ) : __( 'zero', 'woocommerce-store-credit' ) );
				$max_amount_text = ( $max_amount > 0 ? wc_price( $max_amount ) : __( 'unlimited', 'woocommerce-store-credit' ) );

				if ( $custom_amount_step ) {
					$description = sprintf(
						/* translators: 1: minimum amount, 2: maximum amount, 3: step amount */
						__( 'Enter an amount between %1$s and %2$s with increments of %3$s.', 'woocommerce-store-credit' ),
						$min_amount_text,
						$max_amount_text,
						wc_price( $custom_amount_step )
					);
				} else {
					$description = sprintf(
						/* translators: 1: minimum amount 2: maximum amount */
						__( 'Enter an amount between %1$s and %2$s.', 'woocommerce-store-credit' ),
						$min_amount_text,
						$max_amount_text
					);
				}
			}
		}

		$data = array(
			'fields' => array(
				'store_credit_custom_amount' => array(
					/* translators: %s: Currency symbol */
					'label'             => sprintf( _x( 'Credit amount (%s)', 'product field label', 'woocommerce-store-credit' ), get_woocommerce_currency_symbol() ),
					'type'              => 'number',
					'description'       => $description,
					'custom_attributes' => $custom_attributes,
				),
			),
		);

		wc_store_credit_get_template( 'single-product/store-credit/custom-amount.php', $data );
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
	 * Outputs different receiver content.
	 *
	 * @since 4.0.0
	 */
	public static function different_receiver_content() {
		$product = self::get_store_credit_product();

		if ( ! $product || ! $product->allow_different_receiver() ) {
			return;
		}

		$data = wp_parse_args(
			$product->get_meta( '_store_credit_data' ),
			array(
				'receiver_fields_title' => __( 'Send credit to someone?', 'woocommerce-store-credit' ),
			)
		);

		if ( self::$has_errors && isset( $_POST['send-to-different-customer'] ) && '1' === $_POST['send-to-different-customer'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			$data['display_receiver_fields'] = 'expanded';
		}

		$args = array(
			'data'   => $data,
			'fields' => self::get_receiver_fields(),
		);

		wc_store_credit_get_template( 'single-product/store-credit/custom-receiver.php', $args );
	}

	/**
	 * Gets the field value by key.
	 *
	 * @since 4.0.3
	 *
	 * @param string $key Field key.
	 * @return mixed
	 */
	public static function get_value( $key ) {
		return ( self::$has_errors ? wc_get_post_data_by_key( $key ) : '' );
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
		$product = self::get_store_credit_product( $product_id );

		if ( ! $passed || ! $product ) {
			return $passed;
		}

		// phpcs:disable WordPress.Security.NonceVerification
		if ( ! empty( $_POST['send-to-different-customer'] ) && $product->allow_different_receiver() ) {
			$fields = self::get_receiver_fields();

			foreach ( $fields as $id => $field ) {
				$value = ( isset( $_POST[ $id ] ) ? wc_clean( wp_unslash( $_POST[ $id ] ) ) : '' );

				if ( ! empty( $field['required'] ) && ! $value ) {
					/* translators: %s: field label */
					wc_add_notice( sprintf( __( '"%s" is a required field.', 'woocommerce-store-credit' ), $field['label'] ), 'error' );
					return false;
				}
			}
		}

		$preset_amounts = $product->get_preset_amounts();
		$custom_amount  = ( ! empty( $_POST['store_credit_custom_amount'] ) ? wc_clean( wp_unslash( $_POST['store_credit_custom_amount'] ) ) : 0 );

		// Custom amount is a preset amount.
		if ( $preset_amounts && $custom_amount && in_array( $custom_amount, $preset_amounts, true ) ) {
			return true;
		}

		$allow_custom_amount = $product->allow_custom_amount();

		// Custom amount not allowed.
		if ( ! $allow_custom_amount && $custom_amount ) {
			wc_add_notice( __( 'The credit amount is not valid.', 'woocommerce-store-credit' ), 'error' );
			return false;
		}

		if ( $allow_custom_amount ) {
			// Default credit amount not defined, a custom amount is required.
			if ( ! $custom_amount && 0 >= $product->get_credit_amount() ) {
				wc_add_notice( __( 'Please, enter a credit amount.', 'woocommerce-store-credit' ), 'error' );
				return false;
			}

			// Use the default credit amount if empty.
			if ( ! empty( $custom_amount ) ) {
				$amount = (float) wc_format_decimal( $custom_amount );

				if ( $amount <= 0 ) {
					wc_add_notice( __( 'The credit amount is not valid.', 'woocommerce-store-credit' ), 'error' );
					return false;
				}

				$min_amount = (float) $product->get_min_custom_amount();

				if ( $min_amount > 0 && $min_amount > $amount ) {
					/* translators: %s: minimum amount */
					wc_add_notice( sprintf( __( 'The minimum credit amount is %s.', 'woocommerce-store-credit' ), wc_price( $min_amount ) ), 'error' );
					return false;
				}

				$max_amount = (float) $product->get_max_custom_amount();

				if ( $max_amount > 0 && $max_amount < $amount ) {
					/* translators: %s: maximum amount */
					wc_add_notice( sprintf( __( 'The maximum credit amount is %s.', 'woocommerce-store-credit' ), wc_price( $max_amount ) ), 'error' );
					return false;
				}

				$amount_step = (float) $product->get_custom_amount_step();

				// Add number precision to avoid a 'division by zero' error.
				if ( $amount_step > 0 && 0 !== ( wc_add_number_precision( $amount ) % wc_add_number_precision( $amount_step ) ) ) {
					/* translators: %s: amount step */
					wc_add_notice( sprintf( __( 'The credit amount has an interval of %s.', 'woocommerce-store-credit' ), wc_price( $amount_step ) ), 'error' );
					return false;
				}
			}
		}

		return true;
		// phpcs:enable WordPress.Security.NonceVerification
	}

	/**
	 * Adds custom data to the cart item.
	 *
	 * @since 3.2.0
	 *
	 * @param array $cart_item  An array with the cart item data.
	 * @param int   $product_id Product ID.
	 *
	 * @return array
	 */
	public static function add_cart_item_data( $cart_item, $product_id ) {
		$product = self::get_store_credit_product( $product_id );

		if ( ! $product ) {
			return $cart_item;
		}

		// phpcs:disable WordPress.Security.NonceVerification
		if ( ! empty( $_POST['store_credit_custom_amount'] ) && ( $product->allow_custom_amount() || $product->get_preset_amounts() ) ) {
			// The custom amount has been validated into the method validate_add_cart_item().
			$amount = wc_format_decimal( wp_unslash( $_POST['store_credit_custom_amount'] ) );

			if ( $amount > 0 ) {
				$cart_item['store_credit_custom_amount'] = (float) $amount;
			}
		}

		if ( ! empty( $_POST['send-to-different-customer'] ) && $product->allow_different_receiver() ) {
			$fields = self::get_receiver_fields();
			$values = array();

			foreach ( $fields as $id => $field ) {
				$key   = str_replace( 'store_credit_receiver_', '', $id );
				$value = ( isset( $_POST[ $id ] ) ? wp_unslash( $_POST[ $id ] ) : '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

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
				$cart_item['store_credit_receiver'] = $values;
			}
		}

		return $cart_item;
		// phpcs:enable WordPress.Security.NonceVerification
	}

	/**
	 * Set store credit custom amount.
	 *
	 * @since 4.0.0
	 *
	 * @param WC_Cart $cart Cart object.
	 */
	public static function before_calculate_totals( $cart ) {
		foreach ( $cart->cart_contents as $cart_item ) {
			if ( isset( $cart_item['store_credit_custom_amount'] ) && isset( $cart_item['data'] ) && $cart_item['data'] instanceof WC_Store_Credit_Product ) {
				$cart_item['data']->set_price( $cart_item['store_credit_custom_amount'] );
				$cart_item['data']->set_sale_price( $cart_item['store_credit_custom_amount'] );
				$cart_item['data']->set_regular_price( $cart_item['store_credit_custom_amount'] );
			}
		}
	}

	/**
	 * Gets the custom cart item price.
	 *
	 * @since 4.0.0
	 *
	 * @param string $price_html Formatted price.
	 * @param mixed  $cart_item  Cart item.
	 * @return string
	 */
	public static function cart_item_price( $price_html, $cart_item ) {
		if ( empty( $cart_item['store_credit_custom_amount'] ) ) {
			return $price_html;
		}

		return wc_price( $cart_item['store_credit_custom_amount'] );
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
		$product = self::get_store_credit_product( $cart_item['data'] );

		if ( ! $product ) {
			return $item_data;
		}

		$amount = ( ! empty( $cart_item['store_credit_custom_amount'] ) ? $cart_item['store_credit_custom_amount'] : $product->get_credit_amount() );

		$item_data['store_credit_amount'] = array(
			'name'  => __( 'Credit amount', 'woocommerce-store-credit' ),
			'value' => wc_price( $amount ),
		);

		if ( ! empty( $cart_item['store_credit_receiver'] ) ) {
			$fields = self::get_receiver_fields();

			foreach ( $cart_item['store_credit_receiver'] as $key => $value ) {
				$id = "store_credit_receiver_{$key}";

				$item_data[ $id ] = array(
					'name'  => ( isset( $fields[ $id ]['label'] ) ? $fields[ $id ]['label'] : $id ),
					'value' => $value,
				);
			}
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
		if ( ! empty( $values['store_credit_custom_amount'] ) ) {
			$item->add_meta_data( '_store_credit_custom_amount', $values['store_credit_custom_amount'] );
		}

		if ( ! empty( $values['store_credit_receiver'] ) ) {
			$item->add_meta_data( '_store_credit_receiver', $values['store_credit_receiver'] );
		}
	}
}

WC_Store_Credit_Product_Addons::init();

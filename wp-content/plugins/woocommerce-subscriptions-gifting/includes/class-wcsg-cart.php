<?php
/**
 * Cart integration.
 *
 * @package WooCommerce Subscriptions Gifting
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for cart integration.
 */
class WCSG_Cart {

	/**
	 * Setup hooks & filters, when the class is initialised.
	 */
	public static function init() {
		if ( wcsg_is_woocommerce_pre( '3.4' ) ) {
			add_filter( 'woocommerce_cart_item_name', __CLASS__ . '::add_gifting_option_cart', 21, 3 );
		} elseif ( wcsg_is_woocommerce_pre( '3.4.1' ) ) {
			// On version 3.4.0 of WC display flat/un-editable elements - similar to the mini-cart.
			add_filter( 'woocommerce_cart_item_name', __CLASS__ . '::add_gifting_option_minicart', 21, 3 );
		} else {
			add_filter( 'woocommerce_after_cart_item_name', __CLASS__ . '::print_gifting_option_cart', 10, 2 );
		}

		add_filter( 'woocommerce_widget_cart_item_quantity', __CLASS__ . '::add_gifting_option_minicart', 1, 3 );

		add_filter( 'woocommerce_update_cart_action_cart_updated', __CLASS__ . '::cart_update', 1, 1 );

		add_filter( 'woocommerce_add_to_cart_validation', __CLASS__ . '::prevent_products_in_gifted_renewal_orders', 10 );

		add_filter( 'woocommerce_order_again_cart_item_data', __CLASS__ . '::add_recipient_to_resubscribe_initial_payment_item', 10, 3 );

		add_filter( 'woocommerce_order_again_cart_item_data', __CLASS__ . '::remove_recipient_from_order_again_cart_item_meta', 10, 1 );
	}

	/**
	 * Adds gifting ui elements to subscription cart items.
	 *
	 * @param string $title         The product title displayed in the cart table.
	 * @param array  $cart_item     Details of an item in WC_Cart.
	 * @param string $cart_item_key The key of the cart item being displayed in the cart table.
	 */
	public static function add_gifting_option_cart( $title, $cart_item, $cart_item_key ) {

		$is_mini_cart = did_action( 'woocommerce_before_mini_cart' ) && ! did_action( 'woocommerce_after_mini_cart' );

		if ( is_cart() && ! $is_mini_cart ) {
			$title .= self::maybe_display_gifting_information( $cart_item, $cart_item_key );
		}

		return $title;
	}

	/**
	 * Adds gifting ui elements to subscription items in the mini cart.
	 *
	 * @param int    $quantity      The quantity of the cart item.
	 * @param array  $cart_item     Details of an item in WC_Cart.
	 * @param string $cart_item_key Key of the cart item being displayed in the mini cart.
	 */
	public static function add_gifting_option_minicart( $quantity, $cart_item, $cart_item_key ) {
		$recipient_email = '';
		$html_string     = '';

		if ( self::contains_gifted_renewal() ) {
			$recipient_user_id = self::get_recipient_from_cart_item( wcs_cart_contains_renewal() );
			$recipient_user    = get_userdata( $recipient_user_id );

			if ( $recipient_user ) {
				$recipient_email = $recipient_user->user_email;
			}
		} elseif ( ! empty( $cart_item['wcsg_gift_recipients_email'] ) ) {
			$recipient_email = $cart_item['wcsg_gift_recipients_email'];
		}

		if ( '' !== $recipient_email ) {
			ob_start();
			wc_get_template( 'html-flat-gifting-recipient-details.php', array( 'email' => $recipient_email ), '', plugin_dir_path( WCS_Gifting::$plugin_file ) . 'templates/' );
			$html_string = ob_get_clean();
		}

		return $quantity . $html_string;
	}

	/**
	 * Updates the cart items for changes made to recipient infomation on the cart page.
	 *
	 * @param bool $cart_updated whether the cart has been updated.
	 */
	public static function cart_update( $cart_updated ) {
		if ( ! empty( $_POST['recipient_email'] ) ) {
			if ( ! empty( $_POST['_wcsgnonce'] ) && wp_verify_nonce( $_POST['_wcsgnonce'], 'wcsg_add_recipient' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$recipients = $_POST['recipient_email']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				WCS_Gifting::validate_recipient_emails( $recipients );
				foreach ( WC()->cart->cart_contents as $key => $item ) {
					if ( isset( $_POST['recipient_email'][ $key ] ) ) {
						WCS_Gifting::update_cart_item_key( $item, $key, $_POST['recipient_email'][ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
					}
				}
			} else {
				wc_add_notice( __( 'There was an error with your request. Please try again.', 'woocommerce-subscriptions-gifting' ), 'error' );
			}
		}

		return $cart_updated;
	}

	/**
	 * Prevent products being added to the cart if the cart contains a gifted subscription renewal.
	 *
	 * @param bool $passed Whether adding to cart is valid.
	 */
	public static function prevent_products_in_gifted_renewal_orders( $passed ) {
		if ( $passed ) {
			foreach ( WC()->cart->cart_contents as $key => $item ) {
				if ( isset( $item['subscription_renewal'] ) ) {
					$subscription = wcs_get_subscription( $item['subscription_renewal']['subscription_id'] );
					if ( WCS_Gifting::is_gifted_subscription( $subscription ) ) {
						$passed = false;
						wc_add_notice( __( 'You cannot add additional products to the cart. Please pay for the subscription renewal first.', 'woocommerce-subscriptions-gifting' ), 'error' );
						break;
					}
				}
			}
		}
		return $passed;
	}

	/**
	 * Determines if a cart item is able to be gifted.
	 * Only subscriptions that are not a renewal or switch subscription are giftable.
	 *
	 * @param array $cart_item Cart item.
	 * @return bool Whether the cart item is giftable.
	 */
	public static function is_giftable_item( $cart_item ) {
		return WCSG_Product::is_giftable( $cart_item['data'] ) && ! isset( $cart_item['subscription_renewal'] ) && ! isset( $cart_item['subscription_switch'] );
	}

	/**
	 * Returns the relevant html (static/flat, interactive or none at all) depending on
	 * whether the cart item is a giftable cart item or is a gifted renewal item.
	 *
	 * @param array  $cart_item       The cart item.
	 * @param string $cart_item_key   The cart item key.
	 * @param string $print_or_return Wether to print or return the HTML content. Optional. Default behaviour is to return the string. Pass 'print' to print the HTML content directly.
	 * @return string Returns the HTML string if $print_or_return is set to 'return', otherwise prints the HTML and nothing is returned.
	 */
	public static function maybe_display_gifting_information( $cart_item, $cart_item_key, $print_or_return = 'return' ) {
		$output = '';

		if ( self::is_giftable_item( $cart_item ) ) {
			$email  = ( empty( $cart_item['wcsg_gift_recipients_email'] ) ) ? '' : $cart_item['wcsg_gift_recipients_email'];
			$output = WCS_Gifting::render_add_recipient_fields( $email, $cart_item_key, 'return' );
		} elseif ( self::contains_gifted_renewal() ) {
			$recipient_user_id = self::get_recipient_from_cart_item( wcs_cart_contains_renewal() );
			$recipient_user    = get_userdata( $recipient_user_id );

			if ( $recipient_user ) {
				$output = wc_get_template_html(
					'html-flat-gifting-recipient-details.php',
					array( 'email' => $recipient_user->user_email ),
					'',
					plugin_dir_path( WCS_Gifting::$plugin_file ) . 'templates/'
				);
			}
		}

		if ( 'return' === $print_or_return ) {
			return $output;
		} else {
			echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped.
		}
	}

	/**
	 * When setting up the cart for resubscribes or initial subscription payment carts, ensure the existing subscription recipient email is added to the cart item.
	 *
	 * @param array  $cart_item_data Cart item data.
	 * @param array  $line_item      Line item.
	 * @param object $subscription   Subscription object.
	 * @return array Updated cart item data.
	 */
	public static function add_recipient_to_resubscribe_initial_payment_item( $cart_item_data, $line_item, $subscription ) {
		$recipient_user_id = 0;

		if ( $subscription instanceof WC_Order && isset( $line_item['wcsg_recipient'] ) ) {
			$recipient_user_id = substr( $line_item['wcsg_recipient'], strlen( 'wcsg_recipient_id_' ) );

		} elseif ( ! array_key_exists( 'subscription_renewal', $cart_item_data ) && WCS_Gifting::is_gifted_subscription( $subscription ) ) {
			$recipient_user_id = WCS_Gifting::get_recipient_user( $subscription );
		}

		if ( ! empty( $recipient_user_id ) ) {
			$recipient_user = get_userdata( $recipient_user_id );

			if ( $recipient_user ) {
				$cart_item_data['wcsg_gift_recipients_email'] = $recipient_user->user_email;
			}
		}

		return $cart_item_data;
	}

	/**
	 * Checks the cart to see if it contains a gifted subscription renewal.
	 *
	 * @return bool
	 * @since 1.0
	 */
	public static function contains_gifted_renewal() {
		$cart_contains_gifted_renewal = false;

		$item = wcs_cart_contains_renewal();

		if ( $item ) {
			$cart_contains_gifted_renewal = WCS_Gifting::is_gifted_subscription( $item['subscription_renewal']['subscription_id'] );
		}

		return $cart_contains_gifted_renewal;
	}

	/**
	 * Retrieve a recipient user's ID from a cart item. This function will also create a new user
	 * if the recipient user's email doesn't already exist.
	 *
	 * @param array $cart_item Cart item.
	 * @return string the recipient id. If the cart item doesn't belong to a recipient an empty string is returned
	 * @since 1.0.1
	 */
	public static function get_recipient_from_cart_item( $cart_item ) {
		$recipient_email   = '';
		$recipient_user_id = '';

		if ( isset( $cart_item['subscription_renewal'] ) && WCS_Gifting::is_gifted_subscription( $cart_item['subscription_renewal']['subscription_id'] ) ) {
			$recipient_id    = WCS_Gifting::get_recipient_user( wcs_get_subscription( $cart_item['subscription_renewal']['subscription_id'] ) );
			$recipient       = get_user_by( 'id', $recipient_id );
			$recipient_email = $recipient->user_email;
		} elseif ( isset( $cart_item['wcsg_gift_recipients_email'] ) ) {
			$recipient_email = $cart_item['wcsg_gift_recipients_email'];
		}

		if ( ! empty( $recipient_email ) ) {
			$recipient_user_id = email_exists( $recipient_email );

			// Create a new user if the recipient's email doesn't already exist.
			if ( ! $recipient_user_id ) {
				$recipient_user_id = WCS_Gifting::create_recipient_user( $recipient_email );
			}
		}

		return $recipient_user_id;
	}

	/**
	 * Remove recipient line item meta from order again cart item meta. This meta is re-added to the line item after
	 * checkout and so doesn't need to copied through the cart in this way.
	 *
	 * @param array $cart_item_data Cart item data.
	 * @return array Updated cart item data.
	 * @since 1.0.1
	 */
	public static function remove_recipient_from_order_again_cart_item_meta( $cart_item_data ) {

		foreach ( array( 'subscription_renewal', 'subscription_resubscribe', 'subscription_initial_payment' ) as $subscription_order_again_key ) {
			if ( isset( $cart_item_data[ $subscription_order_again_key ]['custom_line_item_meta']['wcsg_recipient'] ) ) {
				unset( $cart_item_data[ $subscription_order_again_key ]['custom_line_item_meta']['wcsg_recipient'] );
			}
		}

		return $cart_item_data;
	}

	/**
	 * Maybe print gifting HTML elements.
	 *
	 * @param array  $cart_item     The cart item array data.
	 * @param string $cart_item_key The cart item key.
	 * @since 2.0.1
	 */
	public static function print_gifting_option_cart( $cart_item, $cart_item_key ) {
		self::maybe_display_gifting_information( $cart_item, $cart_item_key, 'print' );
	}

	/** Deprecated **/

	/**
	 * Returns gifting ui html elements displaying the email of the recipient.
	 *
	 * @param string $cart_item_key The key of the cart item being displayed in the mini cart.
	 * @param string $email The email of the gift recipient.
	 * @deprecated 2.0.1
	 */
	public static function generate_static_gifting_html( $cart_item_key, $email ) {
		wcs_deprecated_function( __METHOD__, '2.0.1', "the 'html-flat-gifting-recipient-details.php' template. For example usage, see " . __METHOD__ );

		ob_start();
		wc_get_template( 'html-flat-gifting-recipient-details.php', array( 'email' => $email ), '', plugin_dir_path( WCS_Gifting::$plugin_file ) . 'templates/' );
		return ob_get_clean();
	}
}
WCSG_Cart::init();

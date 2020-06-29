<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Box_Office_Cart {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Ticket fields form. Set it to render after add to cart.
		add_action( 'woocommerce_after_add_to_cart_quantity', array( $this, 'render_ticket_fields' ), 20 );

		// Add ticket meta to cart item data.
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );

		// Change the add to cart button related stuff.
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'change_add_to_cart_text' ), 10, 2 );
		add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'change_add_to_cart_url' ), 10, 2 );
		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'loop_add_to_cart_buttom_remove_add_to_cart_class'), 10, 2 );
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'change_add_to_cart_text_single' ), 10, 2 );

		// Cart page.
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'make_qty_in_cart_immutable' ), 10, 3 );

		// Cart message when ticket is added.
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
			add_filter( 'wc_add_to_cart_message_html', array( $this, 'filter_cart_message' ), 10, 2 );
		} else {
			add_filter( 'wc_add_to_cart_message', array( $this, 'filter_cart_message' ), 10, 2 );
		}

		add_filter( 'woocommerce_add_to_cart_sold_individually_found_in_cart', array( $this, 'filter_sold_individually_found_in_cart' ), 10, 5 );
	}

	/**
	 * Render ticket fields in single product summary.
	 *
	 * @return void
	 */
	public function render_ticket_fields() {
		$product = wc_get_product( get_the_ID() );
		if ( wc_box_office_is_product_ticket( $product ) ) {
			// When in single product page, renders the posted ticket after adding
			// it to cart. However when in non product page, e.g. via [product_page]
			// shortcode don't attempt to render posted ticket. There's no context
			// in which posted values should be rendered into.
			//
			// See https://github.com/woocommerce/woocommerce-box-office/pull/177#issuecomment-281755382.
			$posted_fields = ( ! empty( $_POST['ticket_fields'] ) && is_product() )
				? $_POST['ticket_fields']
				: null;

			// Prepare form.
			$ticket_form = new WC_Box_Office_Ticket_Form(
				$product,
				$posted_fields
			);

			// No need to load template if no ticket fields set in the product.
			if ( empty( $ticket_form->fields ) ) {
				return;
			}

			wc_get_template( 'single-product/add-to-cart/ticket-form.php', array( 'ticket_form' => $ticket_form ), 'woocommerce-box-office', WCBO()->dir . 'templates/' );
		}
	}

	/**
	 * Add posted data to the cart item.
	 *
	 * @param array $cart_item_meta Cart item meta
	 * @param int   $product_id     Product ID
	 *
	 * @return array Cart item meta
	 */
	public function add_cart_item_data( $cart_item_meta, $product_id ) {
		if ( ! wc_box_office_is_product_ticket( $product_id ) ) {
			return $cart_item_meta;
		}

		if ( empty( $_POST['ticket_fields'] ) && ! empty( $_GET['force-ticket-creation'] ) ) {
			$ticket_fields = array();

			$fields = get_post_meta( $product_id, '_ticket_fields', true );

			foreach ( $fields as $hash => $field_data ) {
				$ticket_fields[0][ $field_data['type'] ] = '';
			}

			$_POST['ticket_fields'] = apply_filters( 'woocommerce_cart_item_data_ticket_fields', $ticket_fields, $fields );
		}

		if( empty( $_POST ) ){

			return $cart_item_meta;

		}

		// Validate posted ticket form.
		$product = wc_get_product( $product_id );
		$ticket_form = new WC_Box_Office_Ticket_Form( $product );
		$ticket_form->validate( $_POST );

		// Add meta in cart item.
		$cart_item_meta['ticket'] = array_merge(
			$this->_ticket_meta( $product, $_POST ),
			array(
				'fields' => $ticket_form->get_clean_data()
			)
		);

		return $cart_item_meta;
	}

	/**
	 * Returns ticket meta to be stored in cart item meta.
	 *
	 * @param WC_Product $product     Product
	 * @param array      $posted_data Posted data
	 *
	 * @return array Ticket meta
	 */
	private function _ticket_meta( $product, $posted_data ) {
		$variation_id = ! empty( $posted_data['variation_id'] ) ? $posted_data['variation_id'] : '';
		$variations   = array();

		if ( $variation_id ) {
			$variation            = wc_get_product( $variation_id );
			$attributes           = $product->get_attributes();
			$variation_attributes = $variation->get_variation_attributes();

			foreach ( $attributes as $attribute ) {
				if ( ! $attribute['is_variation'] ) {
					continue;
				}

				$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );
				if ( isset( $posted_data[ $taxonomy ] ) ) {
					// Get value from post data.
					if ( $attribute['is_taxonomy'] ) {
						// Don't use wc_clean as it destroys sanitized characters.
						$value = sanitize_title( stripslashes( $posted_data[ $taxonomy ] ) );
					} else {
						$value = wc_clean( stripslashes( $posted_data[ $taxonomy ] ) );
					}

					// Get valid value from variation.
					$valid_value = isset( $variation_attributes[ $taxonomy ] ) ? $variation_attributes[ $taxonomy ] : '';

					// Allow if valid.
					if ( '' === $valid_value || $valid_value === $value ) {
						$variations[ $taxonomy ] = $value;
						continue;
					}
				}
			}
		}

		return array(
			'key'          => uniqid(),
			'product_id'   => $product->get_id(),
			'variation_id' => $variation_id,
			'variations'   => $variations,
		);
	}

	/*
	 * Change the add to cart text for ticket enabled products.
	 *
	 * @param string     $text
	 * @param WC_Product $product
	 *
	 * @return String
	 */
	public function change_add_to_cart_text( $text, $product ) {
		if ( wc_box_office_is_product_ticket( $product ) ) {
			$text = get_option( 'box_office_add_to_cart_text', '' );
			if ( empty( $text ) ) {
				$text = __( 'Ticket Detail', 'woocommerce-box-office' );
			}

			$text = apply_filters( 'woocommerce_box_office_add_to_cart_text', $text );
		}
		return $text;
	}

	/**
	 * Change the add to cart URL for ticket enabled products.
	 *
	 * @param String $url
	 * @param WC_Product $product
	 *
	 * @return String
	 */
	public function change_add_to_cart_url( $url, $product ) {
		if ( wc_box_office_is_product_ticket( $product ) ) {
			$url = get_permalink( $product->get_id() );
		}
		return $url;
	}

	/**
	 * Remove the add to cart class from ticket enabled products.
	 *
	 * @param String $link
	 * @param WC_Product $product
	 *
	 * @return String
	 */
	public function loop_add_to_cart_buttom_remove_add_to_cart_class( $link, $product ) {
		if ( wc_box_office_is_product_ticket( $product ) ) {
			$link = str_ireplace( 'add_to_cart_button', '', $link );
		}
		return $link;
	}

	/*
	 * Change the add to cart text for ticket enabled product in single page.
	 *
	 * @param string     $text
	 * @param WC_Product $product
	 *
	 * @return String
	 */
	public function change_add_to_cart_text_single( $text, $product ) {
		if ( wc_box_office_is_product_ticket( $product ) ) {
			$text = apply_filters( 'woocommerce_box_office_add_to_cart_text_single', __( 'Buy Ticket Now', 'woocommerce-box-office' ) );
		}
		return $text;
	}

	/**
	 * Make quantity input in cart page immutable.
	 *
	 * @param string $product_quantity Product quantity markup
	 * @param string $cart_item_key    Cart item key
	 * @param array  $cart_item        Cart item
	 *
	 * @return string Immutable quantity display
	 */
	public function make_qty_in_cart_immutable( $product_quantity, $cart_item_key, $cart_item ) {
		if ( ! empty( $cart_item['ticket'] ) ) {
			$product_quantity = sprintf( '<span class="qty">%s</span>', esc_html( $cart_item['quantity'] ) );
		}

		return $product_quantity;
	}

	/**
	 * Alter cart message when ticket product is added.
	 *
	 * @param string $message    Cart message
	 * @param int    $product_id Product ID
	 *
	 * @return string Cart message
	 */
	public function filter_cart_message( $message, $product_id ) {
		if ( ! wc_box_office_is_product_ticket( $product_id ) || is_array( $product_id ) ) {
			return $message;
		}

		$qty = ! empty( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 1;
		if ( ! $qty ) {
			$qty = 1;
		}

		$added_text = sprintf(
			_n( '%s"%s" has been added to your cart.', '%s"%s" have been added to your cart.', $qty, 'woocommerce-box-office' ),
			$qty > 1 ? $qty . '&times; ' : '',
			get_the_title( $product_id )
		);

		// Output success messages
		if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
			$return_to = apply_filters( 'woocommerce_continue_shopping_redirect', wp_get_referer() ? wp_get_referer() : home_url() );
			$message   = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', esc_url( $return_to ), esc_html__( 'Continue Shopping', 'woocommerce-box-office' ), esc_html( $added_text ) );
		} else {
			$message   = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', esc_url( wc_get_page_permalink( 'cart' ) ), esc_html__( 'View Cart', 'woocommerce-box-office' ), esc_html( $added_text ) );
		}

		return $message;
	}

	/**
	 * Filter sold individually product found in cart.
	 *
	 * The ticket product will always generate different cart item key (because
	 * of ticket field hash) with the same ticket product.
	 *
	 * One of each variation can be added to the cart when sold individually is enabled.
	 *
	 * @see https://github.com/woocommerce/woocommerce-box-office/issues/208
	 *
	 * @since 1.1.6
	 * @version 1.1.9
	 *
	 * @param bool   $found_in_cart  Whether an item found in the cart.
	 * @param int    $product_id     The ID of the product being added to the cart.
	 * @param int    $variation_id   The variation ID of the product being added to the cart.
	 * @param array  $cart_item_data Extra cart item data being passed into the item.
	 * @param string $cart_id        Cart ID being evaluated.
	 *
	 * @return bool Whether an iten found in the cart.
	 */
	public function filter_sold_individually_found_in_cart( $found_in_cart, $product_id, $variation_id, $cart_item_data, $cart_id ) {
		if ( ! wc_box_office_is_product_ticket( $product_id ) ) {
			return $found_in_cart;
		}

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( $cart_id === $cart_item_key ) {
				continue;
			}

			$current_product_id = $variation_id ? $variation_id : $product_id;
			if ( wc_box_office_is_product_ticket( $cart_item['data'] ) && $current_product_id === $cart_item['data']->get_ID() && $cart_item['quantity'] > 0 ) {
				$found_in_cart = true;
				break;
			}
		}

		return $found_in_cart;
	}
}

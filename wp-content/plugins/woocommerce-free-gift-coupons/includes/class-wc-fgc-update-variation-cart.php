<?php
/**
 * Update Variations in cart.
 *
 * Handles all Variation Update related functionalities.
 *
 * @since 3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main WC_FGC_Update_Variation_Cart Class
 *
 * @version 3.0.0
 */
class WC_FGC_Update_Variation_Cart {

	/**
	 * Coupon code flushing.
	 *
	 * This helps prevent an FGC coupon from being removed
	 * even if there's no more gift item in the cart.
	 *
	 * @var bool
	 * @since 3.1.0
	 */
	private static $prevent_coupon_flushing = false;

	/**
	 * WC Update Variation Cart constructor
	 *
	 * @since 3.0.0
	 */
	public static function init() {

		// Enqueue required js and css.
		add_filter( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_cart_script' ) );

		// Add edit option on the cart page.
		add_action( 'woocommerce_after_cart_item_name', array( __CLASS__, 'variation_update_data' ), 1, 2 );

		// Handle the ajax request of the update cart.
		add_action( 'wc_ajax_fgc_get_product', array( __CLASS__, 'get_variation_html' ) );

		// Update the cart as per the user choice.
		add_action( 'wc_ajax_fgc_update_variation_in_cart', array( __CLASS__, 'update_variation_in_cart' ) );

		// Show error message on the checkout page.
		add_action( 'woocommerce_before_checkout_process', array( __CLASS__, 'check_is_valid_product' ) );

	}

	/**
	 * Prevent Coupon Flushing.
	 *
	 * @return bool Returns field $prevent_coupon_flushing
	 * @since 3.1.0
	 */
	public static function prevent_coupon_flushing() {
		return self::$prevent_coupon_flushing;
	}

	/**
	 * Enqueue needed js and css.
	 *
	 * @param string $page   Name of the page.
	 * @since 3.0.0
	 */
	public static function enqueue_cart_script( $page ) {
		// Check is cart page or not.
		if ( is_cart() ) {

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			// Enqueue needed css for it.
			wp_enqueue_style( 'wc_fgc_style', plugins_url( 'assets/css/frontend/frontend-variation-cart' . $suffix . '.css' , __DIR__ ), array(), WC_Free_Gift_Coupons::$version );
			wp_style_add_data( 'wc_fgc_style', 'rtl', 'replace' );

			// Change the variable add to cart error pop up notice text.
			add_filter( 'woocommerce_get_script_data', array( __CLASS__, 'change_variation_select_alert' ), 10, 2 );

			// Enqueue default js for woocommerce.
			wp_enqueue_script( 'wc-add-to-cart-variation' );

			// Enqueue js.
			wp_enqueue_script( 'wc_fgc_js', plugins_url( 'assets/js/frontend/frontend-variation-cart' . $suffix . '.js' , __DIR__ ), array( 'jquery', 'flexslider', 'wc-single-product' ), WC_Free_Gift_Coupons::$version, true );

			$wc_fgc_trans_array = array(
				'ajax_url'           => admin_url( 'admin-ajax.php' ), // ajax url.
				'wc_fgc_nonce'       => wp_create_nonce( 'wc-fgc-verify-nonce' ),
				'flexslider'         => apply_filters(
					'woocommerce_single_product_carousel_options',
					array(
						'rtl'            => is_rtl(),
						'animation'      => 'slide',
						'smoothHeight'   => false,
						'directionNav'   => false,
						'controlNav'     => false,
						'slideshow'      => false,
						'animationSpeed' => 500,
						'animationLoop'  => false, // Breaks photoswipe pagination if true.
					)
				),
				'zoom_enabled'                => get_theme_support( 'wc-product-gallery-zoom' ),
				'photoswipe_enabled'          => get_theme_support( 'wc-product-gallery-lightbox' ),
				'flexslider_enabled'          => get_theme_support( 'wc-product-gallery-slider' ),
				'server_error'                => __( 'Sorry, an error occured, please try again later.', 'wc_free_gift_coupons' ),
				'variation_update_error'  => __( 'Sorry, the gift item could not be updated, please try again later.', 'wc_free_gift_coupons' ),
			);

			// Localize array here.
			wp_localize_script( 'wc_fgc_js', 'wc_fgc_var_cart_params', $wc_fgc_trans_array );
		}

	}

	/**
	 * Change alert text for Variation.
	 * 
	 * Adjust selection alert text for variation popup.
	 * 
	 * @hook woocommerce_get_script_data
	 * @param array $params
	 * @param string $handle
	 * @return $array
	 */
	public static function change_variation_select_alert( $params, $handle ) {
		if ( 'wc-add-to-cart-variation' === $handle ) {
			$params['i18n_make_a_selection_text'] = __( ' Please select some product options.', 'wc_free_gift_coupons' );
		}
		return $params;
	}

	/**
	 * Add data on the product page.
	 *
	 * @since 3.0.0
	 * @param array        $cart_item  Cart item array.
	 * @param array string $cart_item_key Cart item key.
	 */
	public static function variation_update_data( $cart_item, $cart_item_key ) {

		$_product = $cart_item['data'];

		$product_id   = isset( $cart_item['product_id'] ) ? intval( $cart_item['product_id'] ) : 0; // Get the variation id.
		$variation_id = isset( $cart_item['variation_id'] ) ? intval( $cart_item['variation_id'] ) : 0; // Get the variation id.
	
		$get_gift_cart_meta = isset( $cart_item['free_gift'] ) ? $cart_item['free_gift'] : '';

		// Check if product is varaible and has free gift item meta in it.
		if ( ( $_product->is_type( 'variable' ) || $variation_id > 0 ) && ! empty( $get_gift_cart_meta )
		&& 'variable' === $cart_item['fgc_type'] && ! self::is_cart_widget() ) {

			// Check if no variation selected, so as to do something cool :)
			if ( $variation_id > 0 ) {
				$edit_in_cart_text      = _x( 'Change options', 'edit in cart link text', 'wc_free_gift_coupons' );
				$var_edit_trigger_class = '';
			} else {
				$edit_in_cart_text      = _x( 'Choose options', 'edit in cart link text', 'wc_free_gift_coupons' );
				$var_edit_trigger_class = ' wc-fgc-show-edit';
			}

			// Get Link incase js fails.
			$edit_in_cart_link = add_query_arg(
				array(
					'update-gift'  => $cart_item_key
				),
				$_product->get_permalink( $cart_item )
			);

			// Translators: %1$s text for edit price link.
			$edit_in_cart_link_content = sprintf( _x( '<small>%1$s<span class="dashicons dashicons-after dashicons-edit"></span></small>', 'edit in cart text', 'wc_free_gift_coupons' ), $edit_in_cart_text );

			$variation_html =
			'<div class="actions wc-fgc-cart-update' . esc_attr( $var_edit_trigger_class ) . '">
				<a href="' . esc_url( get_permalink( $_product->get_id() ) ) . '" class="wc-fgc-edit-var-link button wc_fgc_updatenow" data-item_key="' . esc_attr( $cart_item_key ) . '" data-product_id="' . esc_attr( $product_id ) . '" data-variation_id="' . esc_attr( $variation_id ) . '">'
				. $edit_in_cart_link_content .
				'</a>
			</div>';
			echo $variation_html;
		}
	}

	/**
	 * Ajax Handler for update cart.
	 *
	 * @since 3.0.0
	 */
	public static function get_variation_html() {
		global $post;

		// Get the product id from the ajax request.
		$product_id    = isset( $_GET['product_id'] ) ? intval( wp_unslash( $_GET['product_id'] ) ) : 0; // phpcs:ignore
		$variation_id  = isset( $_GET['variation_id'] ) ? intval( wp_unslash( $_GET['variation_id'] ) ) : 0; // phpcs:ignore
		$cart_item_key = isset( $_GET['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_GET['cart_item_key'] ) ) : ''; // phpcs:ignore

		$cart_item = WC()->cart->get_cart_item( $cart_item_key );

		// Is it really a product, is it really still in the cart?
		if ( empty( $cart_item ) || ! isset( $cart_item['data'] ) || false === $cart_item['data'] ) {
			wp_send_json_error( esc_html__( 'This gift product no longer appears to be valid. Could you try reloading the page?', 'wc_free_gift_coupons' ), 400 );
		}

		// Add atribbutes to global $_REQUEST so the option data can capture.
		foreach ( $cart_item['variation'] as $key => $value ) {
			$_REQUEST[ $key ] = $value;
		}

		// Get product and post from the id.
		$post = get_post( $product_id ); // phpcs:ignore

		setup_postdata( $post );
	
		/* Setting global things on our own for getting things in woocommerce manner ::end */
		do_action( 'wc_fgc_before_single_cart_product' );

		ob_start();

		// Call the template.
		wc_get_template(
			'cart/content-product.php',
			array(
				'variation_id' => $variation_id,
				'cart_item_key' => $cart_item_key,
			),
			'',
			WC_Free_Gift_Coupons::plugin_path() . '/templates/'
		);

		$html = ob_get_contents();
		ob_end_clean();
		
		wp_send_json_success( $html );
	}


	/**
	 * Handle ajax as per the updation in the cart.
	 *
	 * @since 3.0.0
	 */
	public static function update_variation_in_cart() {
		do_action( 'wc_fgc_before_updating_product_in_cart' );

		// phpcs:ignore
		$_product_id = ! empty( $_GET['product_id'] ) ? intval( wp_unslash( $_GET['product_id'] ) ) : 0;
		$variation_id  = isset( $_GET['variation_id'] ) ? intval( wp_unslash( absint( $_GET['variation_id'] ) ) ) : 0; // phpcs:ignore
		$cart_item_key = isset( $_GET['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_GET['cart_item_key'] ) ) : 0; // phpcs:ignore
		$variation     = isset( $_GET['variation'] ) ? map_deep( wp_unslash( $_GET['variation'] ), 'sanitize_text_field' ) : array(); // phpcs:ignore

		$cart_item = WC()->cart->get_cart_item( $cart_item_key );

		if ( ! empty( $cart_item ) ) {

			// Helps when it's just a single variation gift item in the cart, so we prevent removal trigger.
			self::$prevent_coupon_flushing = true;

			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $_product_id, $cart_item['quantity'], $variation_id, $variation, $cart_item );

			if ( $passed_validation && false !== WC()->cart->add_to_cart( $_product_id, $cart_item['quantity'], $variation_id, $variation, $cart_item ) ) {

				// Remove the existing product from cart.
				WC()->cart->remove_cart_item( $cart_item_key );

				do_action( 'woocommerce_ajax_added_to_cart', $_product_id );

				wp_send_json_success();

			} else {
				wp_send_json_error( esc_html__( 'Product selections are not valid, so this selection cannot be confirmed.', 'wc_free_gift_coupons' ) );
			}

		} else {

			wp_send_json_error( esc_html__( 'Product no longer exists in cart, so cannot be edited.', 'wc_free_gift_coupons' ) );

		}
	}

	/**
	 * Check is valid product in the cart.
	 *
	 * @since 3.0.0
	 * @since 3.1.0 - added link to error note.
	 */
	public static function check_is_valid_product() {
		$cart = WC()->cart->get_cart();

		if ( ! empty( $cart ) ) {
			$_is_not_valid_product = false;
			foreach ( $cart as $cart_item_key => $cart_item ) {

				$_cart_remove_pro = wc_get_product( $cart_item['product_id'] );

				if ( $_cart_remove_pro->is_type( 'variable' ) && isset( $cart_item['free_gift'] ) ) {

					if ( 0 === $cart_item['variation_id'] ) {

						$_is_not_valid_product = true;

						$_product_name = $_cart_remove_pro->get_name();
						break;
					}
				}
			}

			if ( $_is_not_valid_product ) {
				$cart_link_text = __( 'Click here', 'wc_free_gift_coupons' );
				$cart_link      = '<a href="' . esc_url( self::edit_in_cart_redirect() ) . '">' . $cart_link_text . '</a>';

				// Translators: 1: Product name. 2: Cart link.
				$message = sprintf( __( 'We were unable to process your order, please try again by choosing attributes for "%1$s". %2$s', 'wc_free_gift_coupons' ), $_product_name, $cart_link );
				throw new Exception( $message );
			}
		}
	}

	/**
	 * Rendering cart widget?
	 *
	 * @return bool
	 */
	public static function is_cart_widget() {
		return did_action( 'woocommerce_before_mini_cart' ) > did_action( 'woocommerce_after_mini_cart' );
	}

	/**
	 * Customize product display of ajax-loaded template.
	 *
	 * @since 3.0.2
	 * @deprecated 3.1.0
	 */
	public static function custom_hooks_and_filters() {
		wc_deprecated_function( 'WC_FGC_Update_Variation_Cart::custom_hooks_and_filters()', '3.1.0', 'wc_fgc_customize_product_template_in_cart()' );	
	}

	/**
	 * Change add to cart text
	 *
	 * @since 3.0.2
	 * @deprecated 3.1.0
	 */
	public static function add_to_cart_text( $text ) {
		wc_deprecated_function( 'WC_FGC_Update_Variation_Cart::custom_hooks_and_filters()', '3.1.0', 'wc_fgc_add_to_cart_text()' );			
	}


	/**
	 * Hide the quantity input when updating.
	 *
	 * @param array $args
	 * @param object $product
	 * @return string
	 * @since 3.1.0
	 */
	public static function hide_quantity_input( $args, $product ) {

		// phpcs:disable WordPress.Security.NonceVerification
		if ( isset( $_GET['update-gift'] ) ) {
			$updating_cart_key = wc_clean( $_GET['update-gift'] );

			if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {
				$args['min_value'] = WC()->cart->cart_contents[ $updating_cart_key ]['quantity'];
				$args['max_value'] = WC()->cart->cart_contents[ $updating_cart_key ]['quantity'];
			}
		}

		return $args;

	}

	/**
	 * If Updating a gift change single item's add to cart button text.
	 *
	 * @param string $text
	 * @param object $product
	 * @return string
	 * @since 3.1.0
	 */
	public static function single_add_to_cart_text( $text, $product ) {

		if ( isset( $_GET['update-gift'] ) ) {
			$updating_cart_key = wc_clean( $_GET['update-gift'] );

			if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {
				$text = apply_filters( 'wc_fgc_single_update_cart_text', __( 'Update Gift', 'wc_free_gift_coupons' ), $product );
			}
		}

		return $text;

	}



	/**
	 * Add a hidden input to facilitate changing the variation from cart, and enqueue script.
	 * 
	 * @since 3.1.0
	 */
	public static function display_hidden_update_input() {
		if ( isset( $_GET['update-gift'] ) ) {
			$updating_cart_key = wc_clean( $_GET['update-gift'] );

			if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {
				echo '<input type="hidden" name="update-gift" value="' . esc_attr( $updating_cart_key ) . '" />';
			}

		}
	}


	/*-----------------------------------------------------------------------------------*/
	/* Cart                                                                              */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Redirect to the cart when editing a price "in-cart".
	 *
	 * @param  string $url
	 * @return string
	 * @since   3.1.0
	 */
	public static function edit_in_cart_redirect() {
		return wc_get_cart_url();
	}

	/**
	 * Filter the displayed notice after redirecting to the cart when editing a price "in-cart".
	 *
	 * @param  string $url
	 * @return string
	 * @since   3.1.0
	 */
	public static function edit_in_cart_redirect_message( $message ) {
		return __( 'Cart updated.', 'wc_free_gift_coupons' );
	}

	/**
	 * Add cart session data.
	 *
	 * @param array $cart_item_data extra cart item data we want to pass into the item.
	 * @param int   $product_id contains the id of the product to add to the cart.
	 * @param int   $variation_id ID of the variation being added to the cart.
	 * @return array
	 * @since 3.1.0
	 */
	public static function add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {

		// Updating container in cart?
		if ( isset( $_POST['update-gift'] ) ) {

			$product_type = WC_Product_Factory::get_product_type( $product_id );

			// Is this a variable product?
			if ( in_array( $product_type, array( 'variable', 'variable-subscription' ), true ) ) {

				$updating_cart_key = wc_clean( $_POST['update-gift'] );

				if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {

					$cart_item = WC()->cart->cart_contents[ $updating_cart_key ];

					// Pass free gift coupon code from existing product to new product.
					if ( isset( $cart_item['free_gift'] ) ) {
						$cart_item_data['free_gift'] = $cart_item['free_gift'];
					}

					// Pass free gift quantity from existing product to new product.
					if ( isset( $cart_item['fgc_quantity'] ) ) {
						$cart_item_data['fgc_quantity'] = $cart_item['fgc_quantity'];
					}

					// Pass free gift "type" from existing product to new product.
					if ( isset( $cart_item['fgc_type'] ) ) {
						$cart_item_data['fgc_type'] = $cart_item['fgc_type'];
					}

					// Remove.
					WC()->cart->remove_cart_item( $updating_cart_key );

					// Redirect to cart.
					if ( ! wp_doing_ajax() ) {
						add_filter( 'woocommerce_add_to_cart_redirect', array( __CLASS__, 'edit_in_cart_redirect' ) );
					}

					// Edit notice.
					add_filter( 'wc_add_to_cart_message_html', array( __CLASS__, 'edit_in_cart_redirect_message' ) );
				}
			}

		}

		return $cart_item_data;
	}

}

WC_FGC_Update_Variation_Cart::init();

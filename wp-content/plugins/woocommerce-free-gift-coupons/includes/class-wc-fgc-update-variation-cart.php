<?php
/**
 * Update Variations in cart.
 *
 * Handles all Variation Update related functionalities.
 *
 * @since 3.0.0
 * @package  WooCommerce Free Gift Coupons/Edit in Cart
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main WC_FGC_Update_Variation_Cart Class
 *
 * @version 3.1.0
 */
class WC_FGC_Update_Variation_Cart {

	/**
	 * Coupon code flushing.
	 *
	 * This helps prevent an FGC coupon from being removed
	 * even if there's no more gift item in the cart.
	 *
	 * @since 3.1.0
	 * @var bool
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

		// Show notice if gift item variations haven't been selected.
		add_action( 'woocommerce_check_cart_items', array( __CLASS__, 'display_unselected_variation_notice' ) );

		// For JS Degrade compatibility.

		// Hide quantity input when updating.
		add_filter( 'woocommerce_quantity_input_args', array( __CLASS__, 'hide_quantity_input' ), 99, 2 );

		// Change add to cart link.
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( __CLASS__, 'single_add_to_cart_text' ), 99, 2 );

		// Add hidden input to add to cart form.
		add_action( 'woocommerce_after_single_variation', array( __CLASS__, 'display_hidden_update_input' ) );	

		// Hide "From: X".
		add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'hide_from_statement' ), 99, 2 );

		// Update cart.
		add_filter( 'woocommerce_add_cart_item_data', array( __CLASS__, 'add_cart_item_data' ), 5, 3 );

	}

	/**
	 * Prevent Coupon Flushing.
	 *
	 * @since 3.1.0
	 *
	 * @return bool Returns field $prevent_coupon_flushing
	 */
	public static function prevent_coupon_flushing() {
		return self::$prevent_coupon_flushing;
	}

	/**
	 * Enqueue needed js and css.
	 *
	 * @since 3.0.0
	 * 
	 * @param string $page   Name of the page.
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
				'i18n_server_error'           => __( 'Sorry, an error occured, please try again later.', 'wc_free_gift_coupons' ),
				'i18n_variation_update_error' => __( 'Sorry, the gift item could not be updated, please try again later.', 'wc_free_gift_coupons' ),
				// translators: %button_text% the single_add_to_cart_text() for the product %product_title% is the product title.
				'i18_update_button_label'     => _x( '%button_text% options for %product_title%', 'add to cart button label in cart edit context', 'wc_free_gift_coupons' ),
			);

			// Localize array here.
			wp_localize_script( 'wc_fgc_js', 'wc_fgc_var_cart_params', $wc_fgc_trans_array );

			// Make sure the script template is loaded.
			add_action( 'wp_print_footer_scripts', array( __CLASS__, 'print_row_template' ) );

		}

	}

	/**
	 * Change alert text for Variation.
	 * 
	 * Adjust selection alert text for variation popup.
	 *
	 * @since 3.0.0
	 * 
	 * @hooked woocommerce_get_script_data
	 * @param array $params
	 * @param string $handle
	 * @return $array
	 */
	public static function change_variation_select_alert( $params, $handle ) {
		if ( 'wc-add-to-cart-variation' === $handle ) {
			$params['i18n_make_a_selection_text'] = __( 'Please select some product options.', 'wc_free_gift_coupons' );
		}
		return $params;
	}

	/**
	 * Add data on the product page.
	 *
	 * @since 3.0.0
	 * 
	 * @param array        $cart_item  Cart item array.
	 * @param array string $cart_item_key Cart item key.
	 */
	public static function variation_update_data( $cart_item, $cart_item_key ) {

		$_product = isset( $cart_item['data'] ) && $cart_item['data'] instanceof WC_Product ? $cart_item['data'] : false;

		// Check if product is varaible and has free gift item meta in it.
		if ( $_product && ! self::is_cart_widget() && ! empty( $cart_item[ 'fgc_edit_in_cart' ] ) ) {

			$product_id   = isset( $cart_item['product_id'] ) ? intval( $cart_item['product_id'] ) : 0; // Get the variation id.
			$variation_id = isset( $cart_item['variation_id'] ) ? intval( $cart_item['variation_id'] ) : 0; // Get the variation id.
			$variation    = isset( $cart_item['variation'] ) ? $cart_item['variation'] : array(); // Array of chosen attributes in the cart.

			$pre_selected_attributes = ! empty( $cart_item['fgc_pre_selected_attributes'] ) ? $cart_item['fgc_pre_selected_attributes'] : array();

			// Get Link incase js fails.
			$edit_in_cart_link = add_query_arg(
				array(
					'update-gift'  => $cart_item_key
				),
				$_product->get_permalink( $cart_item )
			);

			// Check if no variation selected, or if its "any", so as to do something cool :)
			if ( $variation_id > 0 && ! self::has_any_variation( $variation ) ) {
				// translators: %1$s Screen reader text opening <span> %2$s Product title %3$s Closing </span>
				$edit_in_cart_text = sprintf( esc_html_x( 'Edit options %1$sfor %2$s%3$s', 'edit in cart link text', 'wc_free_gift_coupons' ),
					'<span class="screen-reader-text">',
					$_product->get_title(),
					'</span>'
				);

				$var_edit_trigger_class = '';
			} else {
				// translators: %1$s Screen reader text opening <span> %2$s Product title %3$s Closing </span>
				$edit_in_cart_text      = sprintf( esc_html_x( 'Choose options %1$sfor %2$s%3$s', 'edit in cart link text', 'wc_free_gift_coupons' ),
					'<span class="screen-reader-text">',
					$_product->get_title(),
					'</span>'
				);
				$var_edit_trigger_class = 'wc-fgc-auto-open-edit';

			}

			?>

			<div class="actions wc-fgc-cart-update" >
				<button
					formaction="<?php echo esc_attr( $edit_in_cart_link );?>"
					class="wc-fgc-edit-in-cart wc_fgc_updatenow wc-fgc-edit-var-link button <?php echo esc_attr( $var_edit_trigger_class ); ?>"
					data-cart_item_key="<?php echo esc_attr( $cart_item_key );?>"
					data-product_id="<?php echo esc_attr( $product_id );?>"
					data-variation_id="<?php echo esc_attr( $variation_id );?>"
					data-pre_selected_attributes="<?php echo htmlspecialchars( wp_json_encode( $pre_selected_attributes ) );?>"
					href="<?php esc_url( $edit_in_cart_link ); ?>">
					<?php echo $edit_in_cart_text; ?>
				</button>
			</div>
			<?php
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
		/**
		 * Before single cart product template is loaded.
		 *
		 * @param array $cart_item
		 * @since 3.0.0
		 * @since 3.1.0 Added $cart_item param.
		 */
		do_action( 'wc_fgc_before_single_product_cart_template', $cart_item );

		ob_start();

		// Call the template.
		wc_get_template(
			'cart/content-product.php',
			array(
				'variation_id'  => $variation_id,
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

		$_product_id = ! empty( $_REQUEST['product_id'] ) ? intval( wp_unslash( $_REQUEST['product_id'] ) ) : 0; // phpcs:ignore
		$variation_id  = isset( $_REQUEST['variation_id'] ) ? intval( wp_unslash( absint( $_REQUEST['variation_id'] ) ) ) : 0; // phpcs:ignore
		$cart_item_key = isset( $_REQUEST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['cart_item_key'] ) ) : 0; // phpcs:ignore
		$variation     = isset( $_REQUEST['variation'] ) ? map_deep( wp_unslash( $_REQUEST['variation'] ), 'sanitize_text_field' ) : array(); // phpcs:ignore

		$cart_item = WC()->cart->get_cart_item( $cart_item_key );

		if ( ! empty( $cart_item ) ) {

			/**
			 * Run before updating our product in cart.
			 *
			 * @param array $cart_item
			 * @since 3.0.0
			 * @since 3.1.0 Added $cart_item key param.
			 */
			do_action( 'wc_fgc_before_updating_product_in_cart', $cart_item );
		
			/**
			 * Edit the cart_item.
			 *
			 * @param array $cart_item
			 * @since 3.1.0
			 */
			$cart_item = apply_filters( 'wc_fgc_edit_cart_item', $cart_item );

			// Helps when it's just a single variation gift item in the cart, so we prevent removal trigger.
			self::$prevent_coupon_flushing = true;

			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $_product_id, $cart_item['quantity'], $variation_id, $variation, $cart_item );

			if ( $passed_validation && false !== WC()->cart->add_to_cart( $_product_id, $cart_item['quantity'], $variation_id, $variation, $cart_item ) ) {
				// Clear notices, so old errors don't show up.
				wc_clear_notices();

				// Remove the existing product from cart.
				WC()->cart->remove_cart_item( $cart_item_key );

				do_action( 'woocommerce_ajax_added_to_cart', $_product_id );

				wp_send_json_success();

			} else {
				$error_notices  = wc_get_notices( 'error' );
				$default_notice = esc_html__( 'Product selections are not valid, so this selection cannot be confirmed.', 'wc_free_gift_coupons' );

				// Use the last error notice, or our default notice.
				$msg = ( 0 < count( $error_notices ) ? end( $error_notices )['notice'] : $default_notice );
	
				wp_send_json_error( $msg );
			}

		} else {

			wp_send_json_error( esc_html__( 'Product no longer exists in cart, so cannot be edited.', 'wc_free_gift_coupons' ) );

		}
	}

	/**
	 * Scan cart contents for unconfigured variable gift product.
	 *
	 * @since 3.2.0
	 *
	 * @return array - The unconfigured cart items.
	 */
	private static function check_cart_for_gift_errors() {

		$cart = WC()->cart->get_cart();

		$errors = array();

		if ( ! empty( $cart ) ) {
			
			$_is_not_valid_product = false;
			
			foreach ( $cart as $cart_item_key => $cart_item ) {

				if ( $cart_item['data']->is_type( 'variable' ) && isset( $cart_item['free_gift'] ) ) {

					if ( 0 === $cart_item['variation_id'] ) {
						$errors[$cart_item_key] = $cart_item;
					}
				}
			}
		}

		return $errors;
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
	 * Hide the quantity input when updating.
	 *
	 * @since 3.1.0
	 *
	 * @param array $args
	 * @param object $product
	 * @return string
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
	 * @since 3.1.0
	 *
	 * @param string $text
	 * @param object $product
	 * @return string
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

	/**
	 * Hide "From:X" statement.
	 *
	 * @since 3.1.0
	 *
	 * @param int $price
	 * @param WC_Product $product
	 * @return mixed
	 */
	public static function hide_from_statement( $price, $product ) {
		if ( isset( $_GET['update-gift'] ) && $product->is_type( 'variable' ) ) {
			return '';
		}
		return $price;
	}

	/**
	 * Has "any" variation?
	 *
	 * Tests if any of the attributes are empty.
	 *
	 * @param array $variation
	 * @return bool
	 * @since 3.1.0
	 */
	public static function has_any_variation( $variations ) {
		return count( $variations ) !== count( array_filter( $variations ) );
	}

	/**
	 * Display notice for unselected variations.
	 *
	 * @since 3.1.0
	 */
	public static function display_unselected_variation_notice() {

		if ( is_cart() || is_checkout() ) {

			$unselected_variations = self::check_cart_for_gift_errors();

			if ( ! empty( $unselected_variations ) ) {

				$type = is_checkout() ? 'error' : 'notice';

				// Construct phases, also for easy translation.
				$comma  = _x( ', ', 'comma for product name separation in unselected variation notice sentence.', 'wc_free_gift_coupons' );
				$and    = _x( ' and ', '"and" phrase to separate last product in unselected variation notice sentence.', 'wc_free_gift_coupons' );
				$phrase = '';

				$count          = 0;
				$products_count = count( $unselected_variations );

				foreach ( $unselected_variations as $cart_item_key => $cart_item ) {
					$word_seperator = '';

					// Is it second to the last item?
					if ( ( $products_count - $count ) === 2 ) {
						$word_seperator = $and;
					} elseif ( ( $products_count - $count ) !== 1 ) { // Use comma for other items, except last.
						$word_seperator = $comma;
					}

					$pre_selected_attributes = ! empty( $cart_item['fgc_pre_selected_attributes'] ) ? $cart_item['fgc_pre_selected_attributes'] : array();

					$edit_in_cart_link = add_query_arg(
						array(
							'update-gift'  => $cart_item_key
						),
						$cart_item['data']->get_permalink( $cart_item )
					);

					$phrase .= '<a class="wc-fgc-edit-in-cart wc-fgc-edit-var-link" href="' . esc_url( $edit_in_cart_link ) . '" data-cart_item_key="' . esc_attr( $cart_item_key ) . '" data-product_id="' . esc_attr( $cart_item['product_id'] ) . '" data-variation_id="' . esc_attr( $cart_item['variation_id'] ) . '" data-pre_selected_attributes="'. htmlspecialchars( wp_json_encode( $pre_selected_attributes ) ) .'">' . esc_html( $cart_item['data']->get_name() ) . '</a>' . $word_seperator;

					++$count;
				}

				// Translators: %s is product link list.
				$message = sprintf( __( 'You have not selected options for the following gift items: <strong>"%s"</strong>', 'wc_free_gift_coupons' ), $phrase );

				wc_add_notice( $message, $type );

			}

		}

	}

	/*-----------------------------------------------------------------------------------*/
	/* Cart                                                                              */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Filter the displayed notice after redirecting to the cart when editing a price "in-cart".
	 *
	 * @since 3.1.0
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function edit_in_cart_redirect_message( $message ) {
		return __( 'Cart updated.', 'wc_free_gift_coupons' );
	}

	/**
	 * Add cart session data.
	 *
	 * @since 3.1.0
	 *
	 * @param array $cart_item_data extra cart item data we want to pass into the item.
	 * @param int   $product_id contains the id of the product to add to the cart.
	 * @param int   $variation_id ID of the variation being added to the cart.
	 * @return array
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

					// Pass edit in cart ish to new product.
					if ( isset( $cart_item['fgc_edit_in_cart' ] ) ) {
						$cart_item_data['fgc_edit_in_cart'] = $cart_item['fgc_edit_in_cart'];
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

	/**
	 * Print the new row script template.
	 *
	 * @since 3.1.0
	 */
	public static function print_row_template() { ?>
		<script type="text/html" id="tmpl-wc-fgc-edit">
			<tr class="wc-fgc-decoy-row"></tr>
			<tr class="wc-fgc-new-row" id="wc-fgc-new-row_{{{data.cart_item_key}}}">
				   <td colspan="{{{data.colsBeforeProductName}}}"></td>
				   <td colspan="{{{data.colSpan}}}" class="product-name wc-fgc-product-content">{{{data.content}}}</td>
		   </tr>
		</script>
		<?php
	}


	/*
	 * -----------------------------------------------------------------------------------
	 *  Deprecated Functions                                                                 
	 *------------------------------------------------------------------------------------
	 */

	/**
	 * Check is valid product in the cart.
	 *
	 * @since 3.0.0
	 * @since 3.1.0 - Added link to error note.
	 * @deprecated 3.2.0
	 */
	public static function check_is_valid_product() {
		$cart = WC()->cart->get_cart();

		if ( ! empty( $cart ) ) {
			$_is_not_valid_product = false;
			foreach ( $cart as $cart_item_key => $cart_item ) {

				if ( $cart_item['data']->is_type( 'variable' ) && isset( $cart_item['free_gift'] ) ) {

					if ( 0 === $cart_item['variation_id'] ) {

						$_is_not_valid_product = true;

						$_product_name = $cart_item['data']->get_name();
						break;
					}
				}
			}

			if ( $_is_not_valid_product ) {
				$cart_link_text = __( 'Click here', 'wc_free_gift_coupons' );
				$cart_link      = '<a href="' . esc_url( wc_get_cart_url() ) . '">' . $cart_link_text . '</a>';

				// Translators: 1: Product name. 2: Cart link.
				$message = sprintf( __( 'We were unable to process your order, please try again by choosing attributes for "%1$s". %2$s', 'wc_free_gift_coupons' ), $_product_name, $cart_link );
				throw new Exception( $message );
			}
		}
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
	 * Redirect to the cart when editing a price "in-cart".
	 *
	 * @since 3.1.0
	 * @deprecated 3.2.0
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function edit_in_cart_redirect() {
		return wc_get_cart_url();
	}

}
WC_FGC_Update_Variation_Cart::init();

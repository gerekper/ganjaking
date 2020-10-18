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
		add_action( 'wp_ajax_wc_fgc_get_product_html', array( __CLASS__, 'get_variation_html' ) );
		add_action( 'wp_ajax_nopriv_wc_fgc_get_product_html', array( __CLASS__, 'get_variation_html' ) );

		// Update the cart as per the user choice.
		add_action( 'wp_ajax_wc_fgc_update_variation_in_cart', array( __CLASS__, 'update_variation_in_cart' ) );
		add_action( 'wp_ajax_nopriv_wc_fgc_update_variation_in_cart', array( __CLASS__, 'update_variation_in_cart' ) );

		// Show error message on the checkout page.
		add_action( 'woocommerce_before_checkout_process', array( __CLASS__, 'check_is_valid_product' ) );
	}

	/**
	 * Enqueue needed js and css
	 *
	 * @name enqueue_cart_script
	 * @param string $page   Name of the page.
	 * @since 3.0.0
	 */
	public static function enqueue_cart_script( $page ) {
		// Check is cart page or not.
		if ( is_cart() ) {

			// Enqueue needed css for it.
			wp_enqueue_style( 'wc_fgc_style', plugins_url( 'assets/css/frontend-variation-cart' . $suffix . '.css' , __DIR__ ), array(), WC_Free_Gift_Coupons::$version );
			wp_style_add_data( 'wc_fgc_style', 'rtl', 'replace' );


			add_filter( 'woocommerce_get_script_data', array( __CLASS__, 'change_variation_select_alert' ), 10, 2 );

			$suffix = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : 'min' );

			// Enqueue default js for woocommerce.
			wp_enqueue_script( 'wc-add-to-cart-variation' );

			// Enqueue js.
			wp_enqueue_script( 'wc_fgc_js', plugins_url( 'assets/js/frontend-variation-cart' . $suffix . '.js' , __DIR__ ), array( 'jquery', 'flexslider', 'wc-single-product' ), WC_Free_Gift_Coupons::$version, true );

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
	 * @name variation_update_data
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
		if ( ( $_product->is_type( 'variable' ) || $variation_id > 0 ) && ! empty( $get_gift_cart_meta ) ) {

			// Check if no variation selected, so as to do something cool :)
			if ( $variation_id > 0 ) {
				$edit_in_cart_text      = _x( 'Change options', 'edit in cart link text', 'wc_free_gift_coupons' );
				$var_edit_trigger_class = '';
			} else {
				$edit_in_cart_text      = _x( 'Choose options', 'edit in cart link text', 'wc_free_gift_coupons' );
				$var_edit_trigger_class = ' wc-fgc-show-edit';
			}

			// Translators: %1$s text for edit price link.
			$edit_in_cart_link_content = sprintf( _x( '<small>%1$s<span class="dashicons dashicons-after dashicons-edit"></span></small>', 'edit in cart text', 'wc_free_gift_coupons' ), $edit_in_cart_text );

			$variation_html =
			'<div id="wc-fgc-item_' . esc_attr( $cart_item_key ) . '" class="actions wc-fgc-cart-update' . esc_attr( $var_edit_trigger_class ) . '">
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
	 * @name get_variation_html
	 * @since 3.0.0
	 */
	public static function get_variation_html() {
		check_ajax_referer( 'wc-fgc-verify-nonce', 'nonce' );

		global $product;
		// Verify the ajax request.

		// get the product id from the ajax request.
		$product_id = isset( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';

		// Get the variation id from the ajax.
		$variation_id       = isset( $_POST['variation_id'] ) ? sanitize_text_field( wp_unslash( $_POST['variation_id'] ) ) : '';
		$cart_item_key_ajax = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
		// Get total cart.
		$wc_cart = WC()->cart->get_cart();

		if ( ! empty( $wc_cart ) ) {
			foreach ( $wc_cart as $cart_item_key => $cart_item ) {

				// Check that product is exists in the cart.
				if ( $cart_item_key_ajax === $cart_item_key ) {
					$_cart_item_key = $cart_item_key;
					foreach ( $cart_item['variation'] as $key => $value ) {
						$_REQUEST[ $key ] = $value;
					}
				}
			}
		}

		// Get product and post from the id.
		$product = wc_get_product( $product_id );

		/* setting global things on our own for getting things in woocommerce manner ::end */
		do_action( 'wc_fgc_before_product_html' );
		?>
		<div class="wc_fgc_cart" data-title="<?php echo esc_attr( $product->add_to_cart_text() ); ?>" id="wc_fgc_<?php echo $_cart_item_key; ?>">
			<section class="wc-fgc-close-section">
				<a href="javascript:void(0)" class="wc-fgc-close-btn">
					<span class="dashicons dashicons-after dashicons-no-alt"></span>
				</a>
			</section>
			<div class="wc-fgc-stock-error" style="display: none;"></div>

			<input type="hidden" id="wc_fgc_prevproid" value="<?php echo $variation_id; ?>">
			<input type="hidden" id="wc_fgc_cart_item_key" value="<?php echo $_cart_item_key; ?>">
			<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php echo $product->get_id(); ?>" <?php wc_product_class( '', $product ); ?>>
			<?php
			/*
			 * woocommerce_before_single_product_summary hook.
			 * @hooked woocommerce_show_product_sale_flash - 10 , @hooked woocommerce_show_product_images - 20
			 */
			do_action( 'woocommerce_before_single_product_summary' );
			?>
			 <div class="summary entry-summary">
				<?php
				// Get single product name.
				wc_get_template( 'single-product/title.php' );

				// Get Available variations?
				$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

				// Load the template.
				wc_get_template(
					'single-product/add-to-cart/variable.php',
					array(
						'available_variations' => $get_variations ? $product->get_available_variations() : false,
						'attributes'           => $product->get_variation_attributes(),
					)
				);
				?>
				 </div>
				 <meta itemprop="url" content="<?php the_permalink(); ?>" />
			 </div>
			</div>
			<?php

			do_action( 'wc_fgc_after_product_html' );

			wp_die();
	}

	/**
	 * Handle ajax as per the updation in the cart.
	 *
	 * @name wc_fgc_update_variation_in_cart
	 * @since 3.0.0
	 */
	public static function update_variation_in_cart() {
		// Verify the ajax request.
		check_ajax_referer( 'wc-fgc-verify-nonce', 'nonce' );

		do_action( 'wc_fgc_before_updating_product_in_cart' );

		$_product_id = ! empty( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : 0;

		/* addition setup for variable product ::start */
		$variation_id       = isset( $_POST['variation_id'] ) ? sanitize_text_field( wp_unslash( absint( $_POST['variation_id'] ) ) ) : 0;
		$cart_item_key_ajax = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : 0;
		$variation          = isset( $_POST['variation'] ) ? map_deep( wp_unslash( $_POST['variation'] ), 'sanitize_text_field' ) : array();
		/* addition setup for variable product ::end */
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $_product_id, 1 );

		if ( $passed_validation ) {

			if ( isset( $_POST['PrevProId'] ) ) {

				$product_to_remove = sanitize_text_field( wp_unslash( $_POST['PrevProId'] ) );

				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

					$_cart_remove_pro = wc_get_product( $cart_item['product_id'] );
					if ( $_cart_remove_pro->is_type( 'variable' ) && isset( $cart_item['free_gift'] ) ) {

						if ( $cart_item_key_ajax === $cart_item_key ) {
							// remove single product.
							$wc_cart_item = $cart_item;
							WC()->cart->remove_cart_item( $cart_item_key );
						}
					}
				}
			}
			ob_start();

			$cart_item_data['free_gift'] = $wc_cart_item['free_gift'];

			$cart_item_data['fgc_quantity'] = $wc_cart_item['fgc_quantity'];

			$product_status = get_post_status( $_product_id );
			if ( $passed_validation && WC()->cart->add_to_cart( $_product_id, $cart_item_data['fgc_quantity'], $variation_id, $variation, $cart_item_data ) && 'publish' === $product_status ) {

				do_action( 'woocommerce_ajax_added_to_cart', $_product_id );

				$success = true;

			} else {

				$success = false;
			}

			wp_die( esc_html( $success ) );

		} else {

			$success = false;
			wp_die( esc_html( $success ) );
		}
	}

	/**
	 * Check is valid product in the cart.
	 *
	 * @name check_is_valid_product
	 * @since 3.0.0
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
				// Translators: %s is Product name.
				$message = sprintf( __( 'We were unable to process your order, please try again by choosing proper attributes of the %s. ', 'wc_free_gift_coupons' ), $_product_name );
				throw new Exception( $message );
			}
		}
	}

	/**
	 * Rendering cart widget?
	 *
	 * @return boolean
	 */
	public static function is_cart_widget() {
		return did_action( 'woocommerce_before_mini_cart' ) > did_action( 'woocommerce_after_mini_cart' );
	}
}

WC_FGC_Update_Variation_Cart::init();

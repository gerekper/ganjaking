<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'WC_Free_Gift_Coupons' ) ) {
	return; // Exit if class exists.
}

/**
 * Main WC_Free_Gift_Coupons Class
 *
 * @package Class
 * @version	3.3.2
 */
class WC_Free_Gift_Coupons extends WC_Free_Gift_Coupons_Legacy {

	/**
	 * The plugin version
	 *
	 * @var string
	 */
	public static $version = '3.3.2';

	/**
	 * The required WooCommerce version
	 *
	 * @var string
	 */
	public static $required_woo = '3.1.0';

	/**
	 * Coupon code to remove.
	 *
	 * @since 3.1.0
	 * @var string
	 * 
	 */
	private static $coupon_to_remove = '';

	/**
	 * Coupon removed directly.
	 *
	 * @since 3.1.0
	 * @var bool
	 */
	private static $coupon_removed_directly = false;

	/**
	 * Array of deprecated hook handlers.
	 *
	 * @since 3.0.0
	 * @var array of WC_FGC_Deprecated_Hooks
	 */
	public static $deprecated_hook_handlers = array();

	/**
	 * Free Gift Coupons pseudo constructor
	 */
	public static function init() {

		self::includes();

		// Make translation-ready.
		add_action( 'init', array( __CLASS__, 'load_textdomain_files' ) );

		// Include theme-level hooks and actions files.
		add_action( 'after_setup_theme', array( __CLASS__, 'theme_includes' ) );

		// Prepare handling of deprecated filters/actions.
		self::$deprecated_hook_handlers['actions'] = new WC_FGC_Deprecated_Action_Hooks();
		self::$deprecated_hook_handlers['filters'] = new WC_FGC_Deprecated_Filter_Hooks();

		// Add the free_gift coupon type.
		add_filter( 'woocommerce_coupon_discount_types', array( __CLASS__, 'discount_types' ) );

		// Add the gift item when coupon is applied.
		add_action( 'woocommerce_applied_coupon', array( __CLASS__, 'apply_coupon' ) );

		// Change the price to ZERO/Free on gift item.
		add_filter( 'woocommerce_add_cart_item', array( __CLASS__, 'add_cart_item' ), 15 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( __CLASS__, 'get_cart_item_from_session' ), 15, 2 );

		add_filter('woocommerce_product_get_price', array( __CLASS__, 'filter_gift_price' ), 999, 2 );
		add_filter('woocommerce_product_variation_get_price', array( __CLASS__, 'filter_gift_price' ), 999, 2 );

		// Sync gift item quantity with actual product.
		add_filter( 'woocommerce_add_cart_item', array( __CLASS__, 'sync_add_cart_item' ), 20 );
		add_filter( 'woocommerce_cart_loaded_from_session', array( __CLASS__, 'sync_free_gifts_from_session' ) );

		// Validate quantity on update_cart in case sneaky folks mess with the markup.
		add_filter( 'woocommerce_update_cart_validation', array( __CLASS__, 'update_cart_validation' ), 10, 4 );

		// Disable multiple quantities of free item.
		add_filter( 'woocommerce_cart_item_quantity', array( __CLASS__, 'cart_item_quantity' ), 5, 3 );

		// Remove gift item when coupon code is removed.
		add_action( 'woocommerce_removed_coupon', array( __CLASS__, 'remove_free_gift_from_cart' ) );

		// Remove gift item if coupon code conditions are no longer valid.
		add_action( 'woocommerce_check_cart_items', array( __CLASS__, 'check_cart_items' ) );

		// Remove gift items, check if last folk standing, to prepare removal of coupon.
		add_action( 'woocommerce_remove_cart_item', array( __CLASS__, 'check_remaining_product' ), 10, 2 );

		/* 
		 * The coupon removal must be delayed so that you don't end up in a loop
		 * since FGC checks cart items when coupons are removed.
		*/
		add_action( 'woocommerce_cart_item_removed', array( __CLASS__, 'delayed_coupon_removal' ), 10, 2 );

		// Free Gifts should not count one way or the other towards product validations.
		add_action( 'woocommerce_coupon_get_items_to_validate', array( __CLASS__, 'exclude_free_gifts_from_coupon_validation' ), 10, 2 );

		// Display as Free! in cart and in orders.
		add_filter( 'woocommerce_cart_item_price', array( __CLASS__, 'cart_item_price' ), 20, 2 );
		add_filter( 'woocommerce_cart_item_subtotal', array( __CLASS__, 'cart_item_price' ), 20, 2 );
		add_filter( 'woocommerce_order_formatted_line_subtotal', array( __CLASS__, 'cart_item_price' ), 20, 2 );

		// Remove free gifts from shipping calcs & enable free shipping if required.
		add_filter( 'woocommerce_cart_shipping_packages', array( __CLASS__, 'remove_free_shipping_items' ) );
		add_filter( 'woocommerce_shipping_free_shipping_is_available', array( __CLASS__, 'enable_free_shipping'), 20, 2 );
		add_filter( 'woocommerce_shipping_legacy_free_shipping_is_available', array( __CLASS__, 'enable_free_shipping'), 20, 2 );

		// Add custom class in cart and in orders.
		add_filter( 'woocommerce_cart_item_class', array( __CLASS__, 'cart_item_class' ), 10, 3 );
		add_filter( 'woocommerce_mini_cart_item_class', array( __CLASS__, 'cart_item_class' ), 10, 3 );
		add_filter( 'woocommerce_order_item_class', array( __CLASS__, 'order_item_class' ), 10, 3 );

		// Add order item meta.
		add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'add_order_item_meta' ), 10, 3 );

	}

	/**
	 * Includes.
	 *
	 * @since 2.0.0
	 */
	public static function includes() {

		// Install.
		include_once  'updates/class-wc-free-gift-coupons-install.php' ;

		// Compatibility.
		include_once  'compatibility/class-wc-fgc-compatibility.php' ;

		// Support deprecated filter hooks and actions.
		include_once  'compatibility/backcompatibility/class-wc-fgc-deprecated-action-hooks.php' ;
		include_once  'compatibility/backcompatibility/class-wc-fgc-deprecated-filter-hooks.php' ;

		if ( is_admin() ) {
			// Admin includes.
			self::admin_includes();
		} 
			
		// Variation editing feature in cart.
		include_once  'class-wc-fgc-update-variation-cart.php';

	}

	/**
	 * Admin & AJAX functions and hooks.
	 */
	public static function admin_includes() {

		// Admin notices handling.
		include_once  'admin/class-wc-free-gift-coupons-admin-notices.php';

		// Admin functions and hooks.
		include_once  'admin/class-wc-free-gift-coupons-admin.php';
	}

	/**
	 * Load localisation files
	 *
	 * Preferred language file location is: /wp-content/languages/plugins/wc_free_gift_coupons-$locale.mo
	 *
	 * @return void
	 * @since 1.0
	 */
	public static function load_textdomain_files() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'wc_free_gift_coupons' );

		load_textdomain( 'wc_free_gift_coupons', WP_LANG_DIR . '/wc_free_gift_coupons/wc_free_gift_coupons-' . $locale . '.mo' );
		load_plugin_textdomain( 'wc_free_gift_coupons', false, 'woocommerce-free-gift-coupons/languages' );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 * @since 3.1.0
	 */
	public static function plugin_path() {
		return untrailingslashit( plugin_dir_path( WC_FGC_PLUGIN_FILE ) );
	}

	/**
	 * Displays a warning message if version check fails.
	 *
	 * @return string
	 */
	public static function admin_notice() {
		wc_deprecated_function( 'WC_Free_Gift_Coupons::admin_notice()', '1.6.0', 'Function is no longer used.' );
		/* translators: %s: Required version of WooCommerce */
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Free Gift Coupons requires at least WooCommerce %s in order to function. Please upgrade WooCommerce.', 'wc_free_gift_coupons' ), self::$required_woo ) . '</p></div>';
	}


	/**
	 * Include template functions and hooks.
	 * 
	 * @since 3.1.0
	 */
	public static function theme_includes() {
		include_once 'wc-fgc-template-functions.php';
		include_once 'wc-fgc-template-hooks.php';
	}


	/**
	 * Add a new coupon type
	 *
	 * @param array $types - available coupon types
	 * @return array
	 */
	public static function discount_types( $types ) {
		$types['free_gift'] = __( 'Free Gift', 'wc_free_gift_coupons' );
		return $types;
	}


	/**
	 * Add the gift item to the cart when coupon is applied
	 * 
	 * @param string $coupon_code
	 * @return void
	 */
	public static function apply_coupon( $coupon_code ) { 

		// Get the Gift IDs.
		$gift_data = self::get_gift_data( $coupon_code );

		if ( ! empty ( $gift_data ) ) {

			foreach ( $gift_data as $gift_id => $data ) {

				/**
				 * Wc_fgc_apply_coupon_data
				 * 
				 * @since 3.1.0 Added custom hook for 3rd party to add data to fgc cart item.
				 * @param  array $data
				 * @param  string $coupon_code
				 */
				$data = apply_filters( 'wc_fgc_apply_coupon_data', $data, $coupon_code );

				if ( $data['product_id'] > 0 && isset( $data['data'] ) && $data['data'] instanceof WC_Product && $data['data']->is_purchasable() ) {

					/**
					 * FGC cart item data.
					 *
					 * @param array items.
					 * @param string $coupon_code
					 * @since 3.1.0
					 */
					$cart_item_data = apply_filters( 'wc_fgc_cart_item_data', 
						array(
							'free_gift'                   => $coupon_code,
							'fgc_quantity'                => isset( $data['quantity'] ) && $data['quantity'] > 0 ? intval( $data['quantity'] ) : 1,
							'fgc_type'                    => $data['data']->is_type( 'variable' ) ? 'variable' : 'not-variable', // Deprecated 3.1.0.
							'fgc_edit_in_cart'            => self::supports_edit_in_cart( $data['data'] ),
							'fgc_pre_selected_attributes' => self::get_pre_selected_attributes( $data['data'] ),
						),
						$coupon_code
					);

					$key = self::add_gift_item_to_cart( $data, $cart_item_data );

				} elseif ( current_user_can( 'manage_woocommerce' ) ) {

					if ( isset( $data['data'] ) && $data['data'] instanceof WC_Product ) {
						// translators: %1$s is the product name. 
						$message = sprintf( wp_kses_post( __( '"%1$s" is not purchasable and could not be added to the cart as a gift. Please verify it is published, in stock, and has a price. Note: This message is visible to store managers only.', 'wc_free_gift_coupons' ) ), $data['data']->get_title() );

						$notice = sprintf( '<a href="%s" tabindex="1" class="button wc-forward">%s</a> %s', esc_url( get_edit_post_link( $data['data']->get_id() ) ), esc_html__( 'Edit gift', 'wc_free_gift_coupons' ), esc_html( $message ) );

					} else {
						$notice = esc_html__( 'There are problems with the Gift Products associated with this coupon. Please try editing and re-saving your coupon. Note: This message is visible to store managers only.', 'wc_free_gift_coupons' );
					}
					
					wc_add_notice( $notice, 'error' );
				}
			}

			do_action( 'wc_fgc_applied', $coupon_code );

		}

	}


	/**
	 * Supports edit in cart
	 *
	 * True for variable-ish product type or variation with an "Any" attribute.
	 *
	 * @since 3.1.0
	 *
	 * @param WC_Product $product
	 * @return  bool 
	 */
	private static function supports_edit_in_cart( $product ) {
		$supports_edit_in_cart = false;

		if ( $product->is_type( array( 'variable', 'variable-subscription' ) )
			|| ( $product->get_parent_id() > 0 && WC_FGC_Update_Variation_Cart::has_any_variation( $product->get_variation_attributes() ) ) ) {

			$supports_edit_in_cart = true;

		}

		return $supports_edit_in_cart;
	}


	/**
	 * Get pre-selected attributes of "any" variation.
	 *
	 * Ex: A gift is a Black t-shirt of "Any" size. Size needs to be editable, but not color attribute.
	 * This will return an array of attributes that have already been defined, so they can be hidden.
	 *
	 * @since 3.1.0
	 *
	 * @param WC_Product $product
	 * @return  array 
	 */
	private static function get_pre_selected_attributes( $product ) {
		$pre_selected_attributes = array();

		if ( $product->get_parent_id() > 0 ) {
			// Remove attributes that are empty.
			$pre_selected_attributes = array_keys( array_filter( $product->get_variation_attributes() ) );
		}

		return $pre_selected_attributes;
	}


	/**
	 * Add gift item to cart.
	 *
	 * Bypass WC()-cart->add_to_cart() since "any" variations can't be added to cart.
	 *
	 * @since 3.1.0
	 *
	 * @param array        $product_data
	 * @param array        $cart_item_data
	 * @return string|false
	 */
	private static function add_gift_item_to_cart( $product_data, $cart_item_data ) {
		$product_id   = $product_data['product_id'];
		$quantity     = $product_data['quantity'];
		$variation_id = $product_data['variation_id'];
		$variation    = $product_data['variation'];
		$product      = $product_data['data'];

		/**
		 * Load cart item data for child items.
		 *
		 * @param array $cart_item_data Child item's cart data.
		 * @param int $product_id Child item's product ID.
		 * @param int $variation_id Child item's variation ID.
		 * @param int $quantity Child item's quantity.
		 */
		$cart_item_data = ( array ) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity );

		// Generate a ID based on product ID, variation ID, variation data, and other cart item data.
		$cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

		// See if this product and its options is already in the cart.
		$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );

		// If cart_item_key is set, the item is already in the cart.
		if ( ! $cart_item_key ) {

			$cart_item_key = $cart_id;

			/**
			 * Add item after merging with $cart_item_data
			 *
			 * Allow plugins and add_cart_item_filter() to modify cart item.
			 *
			 * @param array $cart_item_data Child item's cart data.
			 * @param str $cart_item_key Key in the WooCommerce cart array.
			 */
			WC()->cart->cart_contents[ $cart_item_key ] = apply_filters(
				'woocommerce_add_cart_item',
				array_merge(
					$cart_item_data,
					array(
						'key'          => $cart_item_key,
						'product_id'   => absint( $product_id ),
						'variation_id' => absint( $variation_id ),
						'variation'    => $variation,
						'quantity'     => $quantity,
						'data'         => $product,
					)
				),
				$cart_item_key
			);

		}
		return $cart_item_key;
	}


	/**
	 * Prevent Subscriptions validating free gift coupons
	 * 
	 * @since 1.0.7
	 * @deprecated 1.2.1 - Moved to separate compatibility class.
	 * 
	 * @param bool $validate
	 * @param obj $coupon
	 * @return bool
	 */
	public static function ignore_free_gift( $validate, $coupon ) {
		wc_deprecated_function( 'WC_Free_Gift_Coupons::ignore_free_gift', '2.1.2', 'WC_FGC_Subscriptions_Compatibility::ignore_free_gift' );
		return WC_FGC_Subscriptions_Compatibility::ignore_free_gift( $validate, $coupon );
	}


	/**
	 * Change the price on the gift item to be zero
	 *
	 * @param array $cart_item
	 * @return array
	 */
	public static function add_cart_item( $cart_item ) {

		// Adjust price in cart if bonus item.
		if ( ! empty ( $cart_item['free_gift'] ) ) {
			$cart_item['data']->free_gift = $cart_item['free_gift'];
		}

		// Strictly enforce original quantity.
		if ( ! empty ( $cart_item['fgc_quantity'] ) ) {
			$cart_item['quantity'] = $cart_item['fgc_quantity'];
		}

		return $cart_item;
	}

	/**
	 * Adjust session values on any gift items
	 *
	 * @since  3.3.0
	 *
	 * @param array $cart_item
	 * @param array $values
	 * @return array
	 */
	public static function sync_free_gifts_from_session( $cart ) {

		$cart_contents = $cart->get_cart_contents();

		if ( empty( $cart_contents ) ) {
			return;
		}

		foreach ( $cart_contents as $cart_item_key => $cart_item_data ) {

			if ( empty( $cart_item_data[ 'fgc_synced_original_qty' ] ) ) {
				continue;
			}

			// Modify the current gift quantity.
			$cart_item                                  = $cart_contents[$cart_item_key];
			WC()->cart->cart_contents[ $cart_item_key ] = self::sync_add_cart_item( $cart_item );

		}

	}


	/**
	 * Adjust session values on the gift item
	 *
	 * @since  3.0.0
	 * @deprecated 3.3.0 - Cannot sync when loading individual cart item as the entire cart isn't present yet.
	 *
	 * @param array $cart_item
	 * @param array $values
	 * @return array
	 */
	public static function get_cart_item_from_session( $cart_item, $values ) {

		if ( ! empty( $values['free_gift'] ) ) {
			$cart_item['free_gift'] = $values['free_gift'];

			$cart_item['data']->free_gift = $cart_item['free_gift'];

			if ( ! empty ( $values['fgc_quantity'] ) ) {
				$cart_item['fgc_quantity'] = $values['fgc_quantity'];
			}

			if ( ! empty ( $values['fgc_type'] ) ) {
				$cart_item['fgc_type'] = $values['fgc_type'];
			}

			if ( ! empty ( $values['fgc_edit_in_cart'] ) ) {
				$cart_item['fgc_edit_in_cart'] = $values['fgc_edit_in_cart'];
			}

			if ( ! empty( $values['fgc_synced_original_qty'] ) ) {
				$cart_item['fgc_synced_original_qty'] = $values['fgc_synced_original_qty'];
			}

		}

		return $cart_item;

	}


	/**
	 * Adjust price of the gift item
	 *
	 * @since 3.0.0
	 *
	 * @param string $price
	 * @param WC_Product $product
	 * @return string
	 */
	public static function filter_gift_price( $price, $product ) {

		if ( property_exists( $product, 'free_gift' ) ) {
			$price = 0;
		}

		return $price;

	}

	/**
	 * Update cart validation.
	 * Malicious users can change the quantity input in the source markup.
	 *
	 * @since 2.4.0
	 * 
	 * @param bool $passed_validation Whether or not this product is valid.
	 * @param string $cart_item_key The unique key in the cart array.
	 * @param array $values The cart item data values.
	 * @param int $quantity the cart quantity.
	 * @return bool
	 */
	public static function update_cart_validation( $passed_validation, $cart_item_key, $values, $quantity ) {

		if ( ! empty( $values['free_gift'] ) ) {
			
			// Has an initial FGC quantity.
			if ( ! empty ( $values['fgc_quantity'] ) && $quantity !== $values['fgc_quantity'] ) {

				/* Translators: %s Product title. */
				wc_add_notice( sprintf( __( 'You are not allowed to modify the quantity of your %s gift.', 'wc_free_gift_coupons' ), $values['data']->get_name() ), 'error' );
				$passed_validation = false;
			}

		}

		return $passed_validation;

	}			

	/**
	 * Disable quantity inputs in cart
	 *
	 * @param string $product_quantity
	 * @param string $cart_item_key
	 * @param array $cart_item
	 * @return string
	 */
	public static function cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {

		if ( ! empty ( $cart_item['free_gift'] ) ) {
			$product_quantity = sprintf( '%1$s <input type="hidden" name="cart[%2$s][qty]" value="%1$s" />', $cart_item['quantity'], $cart_item_key );
		}

		return $product_quantity;
	}


	/**
	 * Removes gift item from cart when coupon is removed
	 *
	 * @since 1.2.0
	 * 
	 * @param string $coupon
	 * @return void
	 */
	public static function remove_free_gift_from_cart( $coupon ) {
		// Removed directly.
		self::$coupon_removed_directly = true;

		// If the coupon still applies to the initial cart, the free product can remain. WC Subscriptions removes coupons from recurring cart objects. This condition bypasses that.
		if ( WC()->cart->has_discount( $coupon ) ) {
			return;
		}

		$clear_error_notice = false;

		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

			if ( isset( $values['free_gift'] ) && $values['free_gift'] === $coupon ) {

				WC()->cart->set_quantity( $cart_item_key, 0 );
				$clear_error_notice = true;

			}
		}

		// Clear notices, so old errors of gift items don't show up in the cart notice section.
		if ( $clear_error_notice ) {
			wc_clear_notices();
		}

	}


	/**
	 * Removes gift item from cart if coupon is invalidated
	 * 
	 * @return void
	 */
	public static function check_cart_items() {

		$cart_coupons = (array) WC()->cart->get_applied_coupons();

		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

			if ( isset( $values['free_gift'] ) && ! in_array( $values['free_gift'], $cart_coupons, true ) ) {

				WC()->cart->set_quantity( $cart_item_key, 0 );

				wc_add_notice( __( 'A gift item which is no longer available was removed from your cart.', 'wc_free_gift_coupons' ), 'error' );

			}
		}

	}

	/**
	 * Removes gift item from cart if coupon is invalidated
	 *
	 * @since 2.0.0
	 * 
	 * @param  object[] $items | $item properties:  key, object (cart item or order item), product, quantity, price
	 * @return object[]
	 */
	public static function exclude_free_gifts_from_coupon_validation( $items, $discount ) {
		return array_filter( $items, array( __CLASS__, 'exclude_free_gifts' ) );
	}


	/**
	 * Array_filter callback
	 *
	 * @since 2.0.0
	 * 
	 * @param  object $item | properties:  key, object (cart item or order item), product, quantity, price
	 * @return object[]
	 */
	public static function exclude_free_gifts( $item ) {
		return ! ( is_array( $item->object ) && isset( $item->object[ 'free_gift' ] ) ) || ( is_a( $item->object, 'WC_Order_Item' ) && $item->object->get_meta( '_free_gift' ) );
	}


	/**
	 * Instead of $0, show Free! in the cart/order summary
	 * 
	 * @param string $price
	 * @param mixed array|WC_Order_Item $cart_item
	 * @return string
	 */
	public static function cart_item_price( $price, $cart_item ) {

		// WC 2.7 passes a $cart_item object to order item subtotal.
		if ( ( is_array( $cart_item ) && isset( $cart_item['free_gift' ] ) ) || ( is_object( $cart_item ) && $cart_item->get_meta( '_free_gift' ) ) ) {
			$price = __( 'Free!', 'wc_free_gift_coupons' );
		}

		return $price;
	}


	/**
	 * Unset the free items from the packages needing shipping calculations
	 *
	 * @since 1.0.7
	 * 
	 * @param array $packages
	 * @return array
	 */
	public static function remove_free_shipping_items( $packages ) {

		if ( $packages ) {
			foreach ( $packages as $i => $package ) { 

				$free_shipping_count = 0;
				$remove_items        = array();
				$total_count         = count( $package['contents'] );

				foreach ( $package['contents'] as $key => $item ) {
				
					// If the item is a free gift item get free shipping status.
					if ( isset( $item['free_gift'] ) ) {

						if ( self::has_free_shipping( $item['free_gift'] ) ) {
							$remove_items[$key] = $item;
							$free_shipping_count++;
						} 

					} 

					// If the free gift with free shipping is the only item then switch 
					// shipping to free shipping. otherwise delete free gift from package calcs.
					if ( $total_count === $free_shipping_count ) {
						$packages[$i]['ship_via'] = array( 'free_shipping' );					
					} else {
						$remaining_packages       = array_diff_key( $packages[$i]['contents'], $remove_items );
						$packages[$i]['contents'] = $remaining_packages;
					}

				}

			}
		}

		return $packages;
	}


	/**
	 * If the free gift w/ free shipping is the only item in the cart, enable free shipping
	 *
	 * @since 1.0.7
	 *
	 * @param array $packages
	 * @return array
	 */
	public static function enable_free_shipping( $is_available, $package ) { 

		if ( count( $package['contents'] ) === 1 && self::check_for_free_gift_with_free_shipping( $package ) ) {
			$is_available = true;
		}
	 
		return $is_available;
	}


	/**
	 * Check shipping package for a free gift with free shipping
	 *
	 * @since 1.1.0
	 *
	 * @param array $package
	 * @return boolean
	 */
	public static function check_for_free_gift_with_free_shipping( $package ) { 

		$has_free_gift_with_free_shipping = false;

		// Loop through the items looking for one in the eligible array.
		foreach ( $package['contents'] as $item ) {

			// if the item is a free gift item get free shipping status
			if ( isset( $item['free_gift'] ) ) { 

				if ( self::has_free_shipping( $item['free_gift'] ) ) {
					$has_free_gift_with_free_shipping = true;
					break;
				} 

			} 

		}
	 
		return $has_free_gift_with_free_shipping;
	}

	/**
	 * When a new order is inserted, add item meta noting this item was a free gift
	 *
	 * @since 1.1.1
	 * 
	 * @param WC_Order_Item $item
	 * @param str $cart_item_key
	 * @param array $values
	 * @return void
	 */
	public static function add_order_item_meta( $item, $cart_item_key, $values ) {
		if ( isset( $values['free_gift'] ) ) { 	
			$item->add_meta_data( '_free_gift', $values['free_gift'], true );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Check the installed version of WooCommerce is greater than $version argument
	 *
	 * @since   1.1.0
	 * 
	 * @param   $version
	 * @return	boolean
	 */
	public static function wc_is_version( $version = '2.6' ) {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, $version ) >= 0 ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get Free Gift Data from a coupon's ID.
	 *
	 * @since   2.0.0
	 *
	 * @param   mixed $code int coupon ID  | str coupon code
	 * @param   bool $add_titles add product titles - Deprecated 2.5.0
	 * @return	array[] => array( 
	 * 				'product_id'   => int,
	 *				'quantity'     => int,
	 *				'variation_id' => int,
	 *				'variation'    => array(),
	 *				'data'         => WC_Product
	 *				'title'        => string
	 *			);
	 */
	public static function get_gift_data( $code, $add_titles = false ) {

		$gift_data = array();

		// Sanitize coupon code.
		$code = wc_format_coupon_code( $code );

		// Get the coupon object.
		$coupon = new WC_Coupon( $code );
		
		if ( ! is_wp_error( $coupon ) && self::is_supported_gift_coupon_type( $coupon->get_discount_type() ) ) {

			$coupon_meta = $coupon->get_meta( '_wc_free_gift_coupon_data' );

			// Only return meta if it is an array, since coupon meta can be null, which results in an empty model in the JS collection.
			$gift_data = is_array( $coupon_meta ) ? $coupon_meta : array();

			foreach ( $gift_data as $gift_id => $gift ) {

				$gift_product = wc_get_product( $gift_id );

				$defaults = array (
					'product_id'   => 0,
					'quantity'     => 1,
					'variation_id' => 0,
					'variation'    => array(),
					'data'         => $gift_product, // The product object is always passed now.
					'title'        => '',
				);

				$gift_data[$gift_id] = wp_parse_args( $gift, $defaults );
		

				if ( $gift_product instanceof WC_Product ) {

					// Add variation attributes.
					if ( $gift_product->get_parent_id() > 0 && is_callable( array( $gift_product, 'get_variation_attributes' ) ) ) { 
						$gift_data[$gift_id]['variation'] = $gift_product->get_variation_attributes();
					}

					// Get the title of each product.
					$gift_data[$gift_id]['title'] = $gift_product->get_formatted_name();

				}
			}
		}

		return apply_filters( 'wc_fgc_data', $gift_data, $code );

	}

	/**
	 * Supported coupon types.
	 *
	 * @since   3.0.0
	 * 
	 * @return	array
	 */
	public static function get_gift_coupon_types() {
		return apply_filters( 'wc_fgc_types', array( 'free_gift', 'percent', 'fixed_cart', 'fixed_product' ) );
	}

	/**
	 * Is Supported coupon types.
	 *
	 * @since   3.0.0
	 *
	 * @param   string
	 * @return	bool
	 */
	public static function is_supported_gift_coupon_type( $type ) {
		return in_array( $type, self::get_gift_coupon_types(), true );
	}

	/**
	 * Is free shipping enabled for free gift?
	 *
	 * @since   1.1.1
	 *
	 * @param   mixed $code int coupon ID  | str coupon code
	 * @return	bool
	 */
	public static function has_free_shipping( $code ) {

		$has_free_shipping = false;

		// Sanitize coupon code.
		$code = wc_format_coupon_code( $code );

		// Get the coupon object.
		$coupon = new WC_Coupon( $code );

		$gift_ids = array();

		if ( ! is_wp_error( $coupon ) && self::is_supported_gift_coupon_type( $coupon->get_discount_type() ) ) {
			$has_free_shipping = wc_string_to_bool( $coupon->get_meta( '_wc_free_gift_coupon_free_shipping' ) );
		}

		return $has_free_shipping;

	}


	/**
	 * Adjusts cart item class.
	 *
	 * @since 3.0.0
	 *
	 * @param  string $class The class name(s).
	 * @param  array  $cart_item Cart item.
	 * @param  string $cart_item_key The key.
	 * @return string
	 */
	public static function cart_item_class( $class, $cart_item, $cart_item_key ) {
		// Add class if it's our very own :).
		if ( ! empty ( $cart_item['free_gift'] ) ) {
			$class .= ' wc-fgc-cart-item';
		}
		return $class;
	}

	/**
	 * Adjusts order item class.
	 *
	 * @since 3.0.0
	 *
	 * @param  string   $class The class name(s).
	 * @param  Object   $item Cart item.
	 * @param  WC_order $order The key.
	 * @return string
	 */
	public static function order_item_class( $class, $item, $order ) {
		// Add class if it's our very own :).
		if ( ! empty ( $item->get_meta( '_free_gift' ) ) ) {
			$class .= ' wc-fgc-order-item';
		}
		return $class;

	}


	/**
	 * Change the gift item qty to synced product qty.
	 *
	 * @since 3.0.0
	 *
	 * @param array $cart_item
	 * @return array
	 * 
	 * @hooked woocommerce_add_cart_item
	 * @priority 20
	 */
	public static function sync_add_cart_item( $cart_item ) {

		// Adjust quantity in cart if bonus item.
		if ( ! empty ( $cart_item['free_gift'] ) ) {

			$current_key = $cart_item['key'];

			$coupon = new WC_Coupon( $cart_item['free_gift'] );

			if ( $coupon instanceof WC_Coupon && $coupon->get_object_read() ) {

				$cart_contents    = WC()->cart->get_cart_contents();
				$sync_to_products = $coupon->get_meta( '_wc_fgc_product_sync_ids', true, 'edit' );
				$multiply_factor  = 0;

				// Is there anything to sync?
				if ( empty( $sync_to_products ) ) {
					return $cart_item;
				}

				foreach ( $cart_contents as $per_cart_item ) { 
					if ( in_array( $per_cart_item['variation_id'], $sync_to_products, true ) || in_array( $per_cart_item['product_id'], $sync_to_products, true ) ) {
						// Do not count the quantity of the gift itself.
						if ( $current_key === $per_cart_item['key'] ) {
							continue;
						}
						$multiply_factor += $per_cart_item['quantity'];
					}
				}

				// Default to 1 if nothing was found.
				$multiply_factor = $multiply_factor > 0 ? $multiply_factor : 1;

				// Stash the original quantity.
				if ( ! isset( $cart_item['fgc_synced_original_qty'] ) ) {
					$cart_item['fgc_synced_original_qty'] = $cart_item['quantity'];
				}

				$cart_item['quantity'] = $cart_item['fgc_synced_original_qty'] * $multiply_factor;

			}

		}

		return $cart_item;

	}

	/**
	 * Adjust session values on the gift item.
	 *
	 * @since 3.0.0
	 * @deprecated 3.3.0 - Cannot calculate sync quantities before entire cart is loaded.
	 *
	 * @param array $cart_item
	 * @param array $values
	 * @return array
	 * @hooked woocommerce_get_cart_item_from_session
	 * @priority 20
	 */
	public static function sync_get_cart_item_from_session( $cart_item, $values ) {

		if ( ! empty( $values['fgc_synced_original_qty'] ) ) {
			$cart_item['fgc_synced_original_qty'] = $values['fgc_synced_original_qty'];
			$cart_item                            = self::sync_add_cart_item( $cart_item );
		}

		return $cart_item;

	}

	/**
	 * Validates coupon for synced products.
	 *
	 * Checks if a gift coupon has a synced product and makes sure it can only be added if the product is in the cart.
	 *
	 * @since  3.0.0
	 * @deprecated 3.3.0
	 * 
	 * @param  bool      $is_valid To mark valid or not.
	 * @param  WC_Coupon $coupon Coupon object.
	 * @param  WC_Discounts $discounts the Discounts class object.
	 * @return bool
	 */
	public static function coupon_sync_validation( $is_valid, $coupon, $discounts ) {

		$sync_to_products = $coupon->get_meta( '_wc_fgc_product_sync_ids', true, 'edit' );

		// Is there anything to sync? 
		if ( count( $sync_to_products ) > 0 ) {

			$valid = false;

			foreach ( $discounts->get_items_to_validate() as $item ) {
				if ( $item->product && in_array( $item->product->get_id(), $sync_to_products, true ) || in_array( $item->product->get_parent_id(), $sync_to_products, true ) ) {
					$valid = true;
					break;
				}
			}

			if ( ! $valid ) {
				throw new Exception( __( 'Sorry, this coupon is not applicable to selected products.', 'wc_free_gift_coupons' ), 201 );
			}

		}

		return $is_valid;

	}

	/**
	 * Remove the coupon code if no free gifts left in cart.
	 *
	 * @since 3.1.0
	 * 
	 * @see woocommerce_remove_cart_item
	 * @param string $cart_item_key The cart item key.
	 * @param  WC_Cart $cart The cart object.
	 */
	public static function check_remaining_product( $cart_item_key, $cart ) { 
	
		$cart_contents = $cart->get_cart_contents();

		$cart_item = isset( $cart_contents[$cart_item_key] ) ?  $cart_contents[$cart_item_key] : array();

		// Removed a free gift.
		if ( isset( $cart_item['free_gift'] ) ) {

			unset( $cart_contents[$cart_item_key] );

			$coupon_code = $cart_item['free_gift'];
			$has_gifts   = false;

			foreach ( $cart_contents as $cart_item_key => $values ) {
				if ( isset( $values['free_gift'] ) && $values['free_gift'] === $coupon_code ) {
					$has_gifts = true;
					break;
				}
			}

			// If no matching gifts left, and no need to prevent removal, remove code.
			if ( ! $has_gifts && ! WC_FGC_Update_Variation_Cart::prevent_coupon_flushing() ) {
				self::$coupon_to_remove = $coupon_code;				
			}
		}

	}

	/**
	 * Remove the coupon code if no free gifts left in cart.
	 *
	 * @since 3.1.0
	 * 
	 * @see woocommerce_cart_item_removed
	 * @param string $cart_item_key Cart item key.
	 * @param  WC_Cart $cart Cart object.
	 */
	public static function delayed_coupon_removal( $cart_item_key, $cart ) { 
		$coupon = new WC_Coupon( self::$coupon_to_remove );

		if ( ! empty( self::$coupon_to_remove ) ) {
			// Check if it's an only "free gift" coupon.
			$coupon = new WC_Coupon( self::$coupon_to_remove );

			// Is it our very own free gift?
			if ( $coupon->is_type( 'free_gift' ) ) {

				// Don't keep the user confused if the coupon was not removed directly by user.
				if ( ! self::$coupon_removed_directly ) {
					// Translators: %s is the coupon code/name.
					wc_add_notice( sprintf( __( 'Coupon "%s" has been removed.', 'wc_free_gift_coupons' ), self::$coupon_to_remove ) );

					// Remove "item removed notice" for last item, when coupon is automatically removed.
					add_filter( 'woocommerce_cart_item_removed_notice_type', '__return_null' );
				}

				$cart->remove_coupon( self::$coupon_to_remove );
				self::$coupon_to_remove = '';
			}
		}

	}

} // End class.

<?php
/**
 * Front-End Display
 *
 * @package  WooCommerce Mix and Match Products/Display
 * @since    1.0.0
 * @version  2.4.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Mix_and_Match_Display Class.
 *
 * Mix and Match front-end functions and filters.
 */
class WC_Mix_and_Match_Display {

	/**
	 * The single instance of the class.
	 * @var WC_Mix_and_Match_Display
	 *
	 * @since 1.9.2
	 */
	protected static $_instance = null;

	/**
	 * Main class instance. Ensures only one instance of class is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_Mix_and_Match_Display
	 * @since  1.9.2
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Flag used to insert some JS into table for reliable indentation.
	 *
	 * @var bool
	 */
	private $enqueued_table_item_js = false;

	/**
	 * __construct function.
	 */
	public function __construct() {

		// Front end scripts- validation + price updates.
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

		/**
		 * Cart.
		 */

		// Change the tr class attributes when displaying child items in templates.
		add_filter( 'woocommerce_cart_item_class', array( $this, 'cart_item_class' ), 10, 3 );

		// Control modification of packed items' quantity.
		add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'cart_item_remove_link' ), 10, 2 );

		 // Add wrapper info to child products.
		add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'cart_item_wrap' ), 10, 2 );

		// Add wrapper arrows to child products.
		add_filter( 'woocommerce_cart_item_name', array( $this, 'in_cart_item_title' ), 10, 3 );

		// Add edit button to cart container.
		add_filter( 'woocommerce_after_cart_item_name', array( $this, 'edit_selections_button' ), 10, 2 );

		// Disable Cart permalink for child items.
		add_filter( 'woocommerce_cart_item_permalink', array( $this, 'cart_item_permalink' ), 10, 2 );

		// Change packed item quantity output.
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'cart_item_quantity' ), 10, 3 );

		// Hide packed item price.
		add_filter( 'woocommerce_cart_item_price', array( $this, 'cart_item_price' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'cart_item_subtotal' ), 10, 3 );
		add_filter( 'woocommerce_checkout_item_subtotal', array( $this, 'cart_item_subtotal' ), 10, 3 );

		// Filter cart item count.
		add_filter( 'woocommerce_cart_contents_count', array( $this, 'cart_contents_count' ) );

		// Add "Configuration" cart item data to container items.
		add_filter( 'woocommerce_get_item_data', array( $this, 'cart_item_data' ), 10, 2 );

		// Filter cart widget items.
		add_action( 'woocommerce_before_mini_cart', array( $this, 'add_cart_widget_filters' ) );
		add_action( 'woocommerce_after_mini_cart', array( $this, 'remove_cart_widget_filters' ) );

		/**
		 * Orders.
		 */

		// Add wrapper info to child products.
		add_filter( 'woocommerce_order_item_name', array( $this, 'order_table_item_title' ), 10, 2 );

		// Hide Container size meta in my-account.
		add_filter( 'woocommerce_order_items_meta_get_formatted', array( $this, 'order_item_meta' ), 10, 2 );

		// Change the tr class attributes when displaying child items in templates.
		add_filter( 'woocommerce_order_item_class', array( $this, 'order_item_class' ), 10, 3 );

		// Stop displaying the "Part of" meta key in Order Tables, ex: My Account and Emails.
		add_filter( 'woocommerce_order_item_get_formatted_meta_data', array( $this, 'remove_part_of_meta_key_from_display' ), 10, 2 );

		// Display label for mnm_container_size order item meta.
		add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'order_item_meta_label' ), 10, 3 );

		/**
		 * Emails.
		 */

		// Indent items in emails.
		add_action( 'woocommerce_email_styles', array( $this, 'email_styles' ) );

	}

	/*-----------------------------------------------------------------------------------*/
	/*  Scripts and Styles                                                               */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Load scripts.
	 */
	public function frontend_scripts() {

		$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$style_path  = 'assets/css/frontend/mnm-frontend' . $suffix . '.css';
		$script_path = 'assets/js/frontend/add-to-cart-mnm' . $suffix . '.js';

		wp_enqueue_style( 'wc-mnm-frontend', WC_Mix_and_Match()->plugin_url() . '/' . $style_path, array(), WC_Mix_and_Match()->get_file_version( WC_MNM_ABSPATH . $style_path ) );
		wp_style_add_data( 'wc-mnm-frontend', 'rtl', 'replace' );

		if ( $suffix ) {
			wp_style_add_data( 'wc-mnm-frontend', 'suffix', '.min' );
		}

		wp_register_script( 'wc-add-to-cart-mnm', WC_Mix_and_Match()->plugin_url() . '/' . $script_path, array( 'jquery', 'jquery-blockui' ), WC_Mix_and_Match()->get_file_version( WC_MNM_ABSPATH . $script_path ), true );
		
		/**
		 * Javascript strings.
		 *
		 * @param  array $params
		 */
		$params = $this->get_add_to_cart_parameters();

		wp_localize_script( 'wc-add-to-cart-mnm', 'wc_mnm_params', $params );

	}

	/**
	 * Returns Add to Cart Parameters.
	 *
	 * @param  bool $trim_zeros - Deprecated 1.10.5.
	 * @return array
	 */
	public static function get_add_to_cart_parameters( $deprecated = false ) {

		if ( $deprecated ) {
			wc_deprecated_argument( 'args', '1.10.5', 'Passing args to the get_add_to_cart_parameters() method is deprecated.' );
		}

		return apply_filters(
            'wc_mnm_add_to_cart_script_parameters',
            array(
			'addons_three_support'                      => defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0', '>=' ) ? 'yes' : 'no',
			'i18n_total'                                => _x( 'Total:', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			'i18n_subtotal'                             => _x( 'Subtotal:', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			'i18n_addon_total'                          => _x( 'Options total:', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			'i18n_addons_total'                         => _x( 'Grand total:', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: Placeholders "Total/Subtotal" string followed by price followed by price suffix. %1$d is %t placeholder for subtotal. %2$d is %p placeholder for price. %3$d is %s placeholder for %s price suffix.
			'i18n_price_format'                         => sprintf( _x( '%1$s %2$s %3$s', '[Frontend]"Total/Subtotal" string followed by price followed by price suffix', 'woocommerce-mix-and-match-products' ), '%t', '%p', '%s' ),
			// translators: %s is number of items
			'i18n_quantity_format'                      => _x( '%s items', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %s is number of items
			'i18n_quantity_format_single'               => _x( '%s item', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %s is number of items. %max is the container maximum.
			'i18n_quantity_format_counter'              => _x( '%s/%max items', '[Frontend]Status counter format. Ex: 1/10 items', 'woocommerce-mix-and-match-products' ),
			// translators: %s is number of items. %max is the container maximum.
			'i18n_quantity_format_counter_single'       => _x( '%s/%max item', '[Frontend]Status counter format. Ex: 1/10 items', 'woocommerce-mix-and-match-products' ),
			// translators: %v complete price string. %s is the current selected quantity.
			'i18n_status_format'                        => _x( '%v <span class="mnm_counter">(%s)</span>', '[Frontend]"Total price string followed by formatted quantity count, ex: $99 (9 items)', 'woocommerce-mix-and-match-products' ),
			// translators: %1$s is original price. %2$s is the discounted price.
			'i18n_strikeout_price_string'               => sprintf( _x( '<del>%1$s</del> <ins>%2$s</ins>', '[Frontend]Sale/discount price format', 'woocommerce-mix-and-match-products' ), '%f', '%t' ),
			'i18n_free'                                 => _x( 'Free!', 'woocommerce-mix-and-match-products' ),
			// translators: %s is current selected quantity.
			'i18n_qty_message'                          => _x( 'You have selected %s items.', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %s is current selected quantity.
			'i18n_qty_message_single'                   => _x( 'You have selected %s item.', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message.
			'i18n_valid_fixed_message'                  => _x( '%v Add to cart to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message.
			'i18n_valid_min_message'                    => _x( '%v You can select more or add to cart to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message. %max is the container maximum.
			'i18n_valid_max_message'                    => _x( '%v You can select up to %max or add to cart to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message. %min is the container minimum. %max is the container maximum.
			'i18n_valid_range_message'                  => _x( '%v You may select between %min and %max items or add to cart to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message. %s is quantity left to be selected.
			'i18n_qty_error'                            => _x( '%v Please select %s items to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message. %s is quantity left to be selected.
			'i18n_qty_error_single'                     => _x( '%v Please select %s item to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			'i18n_empty_error'                          => _x( 'Please select at least 1 item to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message. %min is the script placeholder for min quantity. %max is script placeholder for max quantity.
			'i18n_min_max_qty_error'                    => _x( '%v Please select between %min and %max items to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message. %min is the script placeholder for min quantity. %max is script placeholder for max quantity.
			'i18n_min_qty_error_singular'               => _x( '%v Please select at least %min item to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message. %min is the script placeholder for min quantity. %max is script placeholder for max quantity.
			'i18n_min_qty_error'                        => _x( '%v Please select at least %min items to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message. %min is the script placeholder for min quantity. %max is script placeholder for max quantity.
			'i18n_max_qty_error_singular'               => _x( '%v Please select fewer than %max item to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message. %min is the script placeholder for min quantity. %max is script placeholder for max quantity.
			'i18n_max_qty_error'                        => _x( '%v Please select fewer than %max items to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message. %min is the script placeholder for min quantity. %max is script placeholder for max quantity.
			'i18n_validation_alert'                     => _x( 'Please resolve all pending configuration issues before adding this product to your cart.', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %d is the min quantity required for an individual child item.
			'i18n_child_item_min_qty_message'           => _x( 'Minimum %d required', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %d is the max quantity allowed for an individual child item.
			'i18n_child_item_max_qty_message'           => _x( 'Maximum %d allowed', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %d is the step quantity allowed for an individual child item.
			'i18n_child_item_step_qty_message'           => _x( 'Must be a multiple of %d', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %d is the max quantity allowed for an individual child item.
			'i18n_child_item_max_container_qty_message' => _x( 'Container limited to %d', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message.
			'i18n_edit_valid_fixed_message'             => _x( '%v Update to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message.
			'i18n_edit_valid_min_message'               => _x( '%v You can select more or update to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message. %max is the container maximum.
			'i18n_edit_valid_max_message'               => _x( '%v You can select up to %max or update to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message. %min is the container minimum. %max is the container maximum.
			'i18n_edit_valid_range_message'             => _x( '%v You may select between %min and %max items or update to continue&hellip;', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: Warning before container configuration is cleared.
			'i18n_confirm_reset'                        => _x( 'Are you sure you want to clear all your selections?', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the current quantity message.
			'i18n_change_config_prompt'                 => esc_html__( '%v Please make some changes to your configuration to update.', '[Frontend]', 'woocommerce-mix-and-match-products' ),
			'currency_symbol'                           => get_woocommerce_currency_symbol(),
			'currency_position'                         => esc_attr( stripslashes( get_option( 'woocommerce_currency_pos' ) ) ),
			'currency_format_num_decimals'              => absint( wc_get_price_decimals() ),
			'currency_format_precision_decimals'        => absint( wc_get_rounding_precision() ),
			'currency_format_decimal_sep'               => esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
			'currency_format_thousand_sep'              => esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
			'currency_format_trim_zeros'                => false === apply_filters( 'woocommerce_price_trim_zeros', false ) ? 'no' : 'yes',
			'price_display_suffix'                      => esc_attr( get_option( 'woocommerce_price_display_suffix' ) ),
			'prices_include_tax'                        => esc_attr( get_option( 'woocommerce_prices_include_tax' ) ),
			'tax_display_shop'                          => esc_attr( get_option( 'woocommerce_tax_display_shop' ) ),
			'calc_taxes'                                => esc_attr( get_option( 'woocommerce_calc_taxes' ) ),
			'photoswipe_enabled'                        => current_theme_supports( 'wc-product-gallery-lightbox' ) ? 'yes' : 'no',
            ) 
        );
	}


	/*-----------------------------------------------------------------------------------*/
	/*  Cart Display                                                                     */
	/*-----------------------------------------------------------------------------------*/


	/**
	 * Changes the tr class of MNM content items to allow their styling.
	 *
	 * @param  string  $class
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_class( $class, $cart_item, $cart_item_key ) {

		// Parent item.
		if ( wc_mnm_is_container_cart_item( $cart_item ) ) {
			$class .= ' mnm_table_container';

			if ( $cart_item['data']->is_priced_per_product() ) {
				$class .= ' mnm_is_priced_per_product_container';
			} else {
				$class .= ' mnm_is_static_priced_container';
			}
			// Child Item.
		} else if ( $container = wc_mnm_get_cart_item_container( $cart_item ) ) {
			$class .= ' mnm_table_item';

			if ( $container['data']->is_priced_per_product() ) {
				$class .= ' mnm_part_of_priced_per_product_container';
			} else {
				$class .= ' mnm_part_of_static_priced_container';
			}
		}

		return $class;
	}


	/**
	 * MnM items can't be removed individually from the cart.
	 * This filter doesn't pass the $cart_item array for some reason.
	 *
	 * @since 2.0.0
	 *
	 * @param  string  $link
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_remove_link( $link, $cart_item_key ) {

		if ( wc_mnm_is_child_cart_item( WC()->cart->cart_contents[ $cart_item_key ] ) ) {
			$link = '';
		}

		return $link;
	}


	/**
	 * Adds style wrapper to child cart items.
	 *
	 * @since 2.0.0
	 *
	 * @param  string   $content
	 * @param  array    $cart_item
	 * @return string
	 */
	public function cart_item_wrap( $content, $cart_item ) {

		if ( wc_mnm_maybe_is_child_cart_item( $cart_item ) ) {
			$content = '<span class="mnm_child_item_arrow_wrap">' . $content . '</span>';
		}

		return $content;

	}


	/**
	 * Adds arrows to child products.
	 *
	 * @param  string   $content
	 * @param  array    $cart_item
	 * @param  string   $cart_item_key
	 * @return string
	 */
	public function in_cart_item_title( $content, $cart_item, $cart_item_key ) {

		if ( wc_mnm_maybe_is_child_cart_item( $cart_item ) && '' !== $content ) {
			$content = '<small class="mnm_child_item_arrow_wrap">' . $content . '</small>';
		}

		return $content;

	}


	/**
	 * Adds edit button to container cart items.
	 * 
	 * @since 2.4.8
	 *
	 * @param  array    $cart_item
	 * @param  string   $cart_item_key
	 */
	public function edit_selections_button( $cart_item, $cart_item_key ) {

		if ( wc_mnm_is_container_cart_item( $cart_item ) ) {

			$container = $cart_item['data'];

			if ( function_exists( 'is_cart' ) && is_cart() && ! $this->is_cart_widget() && apply_filters( 'wc_mnm_show_edit_it_cart_link', true, $cart_item, $cart_item_key ) ) {

				$edit_in_cart_link = esc_url( $container->get_cart_edit_link( $cart_item ) );
				$edit_in_cart_text = esc_html_x( 'Edit selections', 'edit in cart link text', 'woocommerce-mix-and-match-products' );
				$button_class      = esc_attr( WC_MNM_Core_Compatibility::wp_theme_get_element_class_name( 'button' ) );
				printf( '<div class="actions"><a class="button edit_container_in_cart_text edit_in_cart_text %1$s" href="%2$s">%3$s</a>', $button_class, $edit_in_cart_link, $edit_in_cart_text );

			}
		}

	}


	/**
	 * Disables the cart.php permalink for items in the container.
	 *
	 * @since  2.0.0
	 *
	 * @param  string  $permalink
	 * @param  array   $cart_item
	 * @return string
	 */
	public function cart_item_permalink( $permalink, $cart_item ) {

		if ( wc_mnm_maybe_is_child_cart_item( $cart_item ) ) {
			$permalink = '';
		}

		return $permalink;
	}


	/**
	 * Modifies the cart.php formatted quantity for items in the container.
	 *
	 * @since 2.0.0
	 *
	 * @param  string  $quantity
	 * @param  string  $cart_item_key
	 * @param  array   $cart_item
	 * @return string
	 */
	public function cart_item_quantity( $quantity, $cart_item_key, $cart_item ) {

		if ( wc_mnm_maybe_is_child_cart_item( $cart_item ) ) {
			$quantity = $cart_item['quantity'];
		}

		return $quantity;
	}


	/**
	 * Modifies the cart.php formatted html prices visibility for items in the container.
	 *
	 * @since 2.0.0
	 *
	 * @param  string  $price
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_price( $price, $cart_item, $cart_item_key ) {

		// Child items.
		if ( $container_cart_item = wc_mnm_get_cart_item_container( $cart_item ) ) {

			if ( $container_cart_item['data']->is_priced_per_product() ) {
				$is_mini_cart = WC_Mix_and_Match()->display->is_cart_widget() ? 'mini_cart' : 'table';
				$price = '<span class="mnm_child_item_arrow_wrap mnm_child_' . $is_mini_cart . '_item_price">' . $price . '</span>';
			} else {
				$price = '&nbsp;';
			}

			// Parent container.
		} else if ( wc_mnm_is_container_cart_item( $cart_item ) ) {

			if ( $cart_item['data']->is_priced_per_product() ) {

				$price = wc_price( $this->get_container_cart_item_price_amount( $cart_item, 'price' ) );
			}
		}

		return $price;
	}


	/**
	 * Aggregates parent + child cart item prices.
	 *
	 * @since  2.1.0
	 *
	 * @param  array   $cart_item
	 * @param  strong  $type Values: 'price' | 'regular_price' | 'sale_price'
	 * @return string
	 */
	public function get_container_cart_item_price_amount( $cart_item, $type = 'price' ) {

		$calc_type = WC_MNM_Helpers::cache_get( 'display_cart_prices_including_tax' );
		$price_fn  = 'get_' . $type;

		if ( null === $calc_type ) {
			$calc_type = ! WC()->cart->display_prices_including_tax() ? 'excl_tax' : 'incl_tax';
			WC_MNM_Helpers::cache_set( 'display_cart_prices_including_tax', $calc_type );
		}

		$base_price        = (double) WC_MNM_Product_Prices::get_product_price( $cart_item[ 'data' ], array( 'price' => $cart_item[ 'data' ]->$price_fn(), 'calc' => $calc_type ) );
		$child_items       = wc_mnm_get_child_cart_items( $cart_item );
		$child_items_price = 0.0;

		foreach ( $child_items as $child_item_key => $child_item ) {
			$child_item_qty     = $child_item[ 'data' ]->is_sold_individually() ? 1 : $child_item[ 'quantity' ] / $cart_item[ 'quantity' ];
			$child_item_price  =  WC_MNM_Product_Prices::get_product_price( $child_item[ 'data' ], array( 'price' => $child_item[ 'data' ]->$price_fn(), 'calc' => $calc_type, 'qty' => $child_item_qty ) );
			$child_items_price += wc_format_decimal( (double) $child_item_price );
		}

		return $base_price + $child_items_price;

	}


	/**
	 * Modifies line item subtotals in the 'cart.php' & 'review-order.php' templates.
	 *
	 * @since 2.1.0
	 *
	 * @param  string  $subtotal
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {

		// Child items.
		if ( $container_cart_item = wc_mnm_get_cart_item_container( $cart_item ) ) {

			$subtotal = '&nbsp;';

		// Parent container.
		} else if ( wc_mnm_is_container_cart_item( $cart_item ) ) {

			if ( $cart_item['data']->is_priced_per_product() ) {

				$mnm_items_price     = 0;
				$mnm_container_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $cart_item['data'], array( 'qty' => $cart_item['quantity'] ) ) : wc_get_price_including_tax( $cart_item['data'], array( 'qty' => $cart_item['quantity'] ) );

				foreach ( wc_mnm_get_child_cart_items( $cart_item ) as $mnm_item_key => $mnm_item ) {

					$child_item_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $mnm_item['data'], array( 'qty' => $mnm_item['quantity'] ) ) : wc_get_price_including_tax( $mnm_item['data'], array( 'qty' => $mnm_item['quantity'] ) );
					$mnm_items_price    += (double) $child_item_price;

				}

				$cumulative_subtotal = (double) $mnm_container_price + $mnm_items_price;

				$subtotal = $this->format_product_subtotal( $cart_item['data'], $cumulative_subtotal );
			}
		}

		return $subtotal;
	}

	/**
	 * Aggregates cart item totals.
	 *
	 * @since 2.1.0
	 *
	 * @param  array   $cart_item
	 * @param  string  $type Values: 'total' | 'tax' | 'subtotal' | 'subtotal_tax'
	 * @return float
	 */
	public static function get_container_cart_item_subtotal_amount( $cart_item, $type = 'total' ) {

		$base_price        = wc_format_decimal( (double) $cart_item[ 'line_' . $type ] ); // extra decimals?
		$child_items       = wc_mnm_get_child_cart_items( $cart_item );
		$child_items_price = 0.0;

		foreach ( $child_items as $child_cart_item ) {
			$child_item_price    = $child_cart_item[ 'line_' . $type ];
			$child_items_price  += wc_format_decimal( (double) $child_item_price ); // Do we need extra decimals?
		}

		return $base_price + $child_items_price;
	}


	/**
	 * Outputs a formatted subtotal ( @see cart_item_subtotal() ). how necessary is this one?
	 *
	 * @since 2.1.0
	 *
	 * @param  obj     $product   The WC_Product.
	 * @param  string  $subtotal  Formatted subtotal.
	 * @return string             Modified formatted subtotal.
	 */
	public static function format_product_subtotal( $product, $subtotal ) {

		$cart = WC()->cart;
		$taxable = $product->is_taxable();
		$product_subtotal = wc_price( $subtotal );

		// Taxable.
		if ( $taxable ) {

			$tax_subtotal = WC_MNM_Core_Compatibility::is_wc_version_gte( '3.2' ) ? $cart->get_subtotal_tax() : $cart->tax_total;

			$cart_display_prices_including_tax = WC_MNM_Core_Compatibility::is_wc_version_gte( '3.3' ) ? $cart->display_prices_including_tax() : $cart->tax_display_cart === 'incl';

			if ( $cart_display_prices_including_tax ) {
				if ( ! wc_prices_include_tax() && $tax_subtotal > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
				}
		} else {
				if ( wc_prices_include_tax() && $tax_subtotal > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
				}
			}
		}

		return $product_subtotal;
	}


	/**
	 * Rendering cart widget?
	 *
	 * @since  1.4.0
	 * @return boolean
	 */
	public function is_cart_widget() {
		return did_action( 'woocommerce_before_mini_cart' ) > did_action( 'woocommerce_after_mini_cart' );
	}

	/**
	 * Add cart widget filters.
	 *
	 * @since  2.0.5
	 */
	public function add_cart_widget_filters() {
		add_filter( 'woocommerce_mini_cart_item_class', array( $this, 'mini_cart_item_class' ), 10, 2 );
		add_filter( 'woocommerce_widget_cart_item_visible', array( $this, 'cart_widget_item_visible' ), 10, 3 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'cart_widget_container_item_data' ), 10, 2 );
	}

	/**
	 * Remove cart widget filters.
	 *
	 * @since  2.0.5
	 */
	public function remove_cart_widget_filters() {
		remove_filter( 'woocommerce_mini_cart_item_class', array( $this, 'mini_cart_item_class' ), 10, 2 );
		remove_filter( 'woocommerce_widget_cart_item_visible', array( $this, 'cart_widget_item_visible' ), 10, 3 );
		remove_filter( 'woocommerce_get_item_data', array( $this, 'cart_widget_container_item_data' ), 10, 2 );
	}

	/**
	 * Change the li class of composite parent/child items in mini-cart templates to allow their styling.
	 *
	 * @since  2.0.5
	 *
	 * @param  string  $classname
	 * @param  array   $cart_item
	 * @return string
	 */
	public function mini_cart_item_class( $classname, $cart_item ) {

		if ( wc_mnm_maybe_is_child_cart_item( $cart_item ) ) {
			$classname .= ' mnm_child_mini_cart_item';
		} elseif ( wc_mnm_is_container_cart_item( $cart_item ) ) {
			$classname .= ' mnm_container_mini_cart_item';
		}

		return $classname;
	}

	/**
	 * Do not show mix and matched items in cart widget.
	 *
	 * @since  1.0.0
	 * @since  2.0.5 - Renamed from cart_widget_filter.
	 *
	 * @param  bool     $show
	 * @param  array    $cart_item
	 * @param  string   $cart_item_key
	 * @return bool
	 */
	public function cart_widget_item_visible( $show, $cart_item, $cart_item_key ) {

		if ( wc_mnm_maybe_is_child_cart_item( $cart_item ) ) {
			$show = false;
		}

		return $show;
	}

	/**
	 * Adds content data as parent item meta (by default in the mini-cart only).
	 *
	 * @since  2.0.5
	 *
	 * @param  array  $data
	 * @param  array  $cart_item
	 * @return array
	 */
	public function cart_widget_container_item_data( $data, $cart_item ) {

		if ( wc_mnm_is_container_cart_item( $cart_item ) ) {
			$data = array_merge( $data, $this->get_container_config_cart_item_data( $cart_item ) );
		}

		return $data;
	}


	/**
	 * Get container configuration data.
	 *
	 * @since  2.0.5
	 *
	 * @param  array  $cart_item
	 * @param  array  $args
	 *
	 * @return array
	 */
	public function get_container_config_cart_item_data( $cart_item, $args = array() ) {

		$args = wp_parse_args(
            $args,
            array(
				'aggregated' => true,
            ) 
        );

		$data             = array();
		$child_cart_items = wc_mnm_get_child_cart_items( $cart_item );

		if ( ! empty( $child_cart_items ) ) {

			$config_data = array();

			foreach ( $child_cart_items as $child_cart_item_key => $child_cart_item ) {

				$child_config_qty      = $child_cart_item[ 'quantity' ] / $cart_item[ 'quantity' ];
				$child_item_description = WC_MNM_Helpers::format_product_title( $child_cart_item[ 'data' ]->get_name(), $child_config_qty, array( 'title_first' => false ) );

				if ( $args[ 'aggregated' ] ) {

					$config_data[] = $child_item_description;

				} else {

					$data[] = array(
						'key'   => esc_html_x( 'Selections', '[Frontend]', 'woocommerce-mix-and-match-products' ),
						'value' => $child_item_description,
						'className' => 'wc-block-components-product-details__selections',
					);
				}

			}

			if ( ! empty( $config_data ) ) {
				$data[] = array(
					'key'   => esc_html_x( 'Selections', '[Frontend]', 'woocommerce-mix-and-match-products' ),
					'display' => ( string ) implode( "<br/>", $config_data ),
					'className' => 'wc-block-components-product-details__selections',
				);
			}
		}

		return $data;
	}

	/**
	 * Filters the reported number of cart items.
	 * Counts only MnM containers.
	 *
	 * @param  int  $count
	 * @return int
	 */
	public function cart_contents_count( $count ) {

		$cart_items = WC()->cart->get_cart();
		$subtract   = 0;

		foreach ( $cart_items as $key => $cart_item ) {

			if ( wc_mnm_maybe_is_child_cart_item( $cart_item ) ) {
				$subtract += $cart_item['quantity'];
			}
		}

		return $count - $subtract;
	}

	/**
	 * Add "Configuration" cart item data to container items.
	 *
	 * @param  array  $data
	 * @param  array  $cart_item
	 * @return array
	 */
	public function cart_item_data( $data, $cart_item ) {

		// When serving a Store API request...
		if ( WC_MNM_Core_Compatibility::is_store_api_request() && wc_mnm_is_container_cart_item( $cart_item ) ) {

			if ( ! wc_mnm_is_product_container_type( $cart_item[ 'data' ] ) ) {
				return $data;
			}

			// Add child item config as metadata.
			$data = array_merge( $data, $this->get_container_config_cart_item_data( $cart_item, array( 'aggregated' => false ) ) );

		}

		return $data;
	}

	/*-----------------------------------------------------------------------------------*/
	/*  Order Display                                                                    */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Adds child item title preambles to order-details template.
	 *
	 * @param  string   $content
	 * @param  object   $order_item
	 * @return string
	 */
	public function order_table_item_title( $content, $order_item ) {


		if ( wc_mnm_is_child_order_item( $order_item ) ) {
			if ( did_action( 'wc_ajax_mnm_update_container_order_item' ) || did_action( 'woocommerce_view_order' ) || did_action( 'woocommerce_thankyou' ) || did_action( 'before_woocommerce_pay' ) || did_action( 'woocommerce_account_view-subscription_endpoint' ) ) {
				$content = '<span class="mnm_child_item_arrow_wrap">' . $content . '</span>';
			} else {
				// E-mails.
				$content = '<small>' . $content . '</small>';
			}
		}

		return $content;
	}


	/**
	 * Hide the "Container size" meta in the my-account area.
	 *
	 * @param  array  $formatted_meta
	 * @param  obj    $order
	 * @return array
	 */
	public function order_item_meta( $formatted_meta, $order ) {
		foreach ( $formatted_meta as $id => $meta ) {
			if ( $meta['key'] ===  __( 'Container size', 'woocommerce-mix-and-match-products' ) ) {
				unset( $formatted_meta[ $id ] );
			}
		}
		return $formatted_meta;
	}

	/**
	 * Changes the tr class of MNM content items to allow their styling in orders.
	 *
	 * @param  string    $class
	 * @param  array     $order_item
	 * @param  WC_Order  $order
	 * @return string
	 */
	public function order_item_class( $class, $order_item, $order ) {

		// Parent item.
		if ( wc_mnm_is_container_order_item( $order_item ) ) {
			$class .= ' mnm_table_container';
			// Child item.
		} else if ( $container = wc_mnm_get_order_item_container( $order_item, $order ) ) {
			$class .= ' mnm_table_item';

			// Find if it's the first/last one add a suitable CSS class.
			$first_child = '';
			$last_child  = '';

			foreach ( wc_mnm_get_child_order_items( $container, $order ) as $child_item ) {

				if ( wc_mnm_maybe_is_child_order_item( $child_item ) ) {
					if ( $first_child === '' ) {
						$first_child = $child_item;
					}
					$last_child = $child_item;
				}
			}

			if ( $order_item == $first_child ) {
				$class .= ' mnm_table_item_first';
			}

			if ( $order_item == $last_child ) {
				$class .= ' mnm_table_item_last';
			}
		}

		return $class;
	}

	/**
	 * Stop displaying the "Part of" meta key for MNM children.
	 *
	 * @param  obj[] $formatted_meta an array of objects indexed by meta key
	 * @param  WC_Order_Item $order_item
	 * @return  array
	 */
	public function remove_part_of_meta_key_from_display( $formatted_meta, $order_item ) {
		/*
		 * Version 1.5.0 stops saving this string by default, set filter to true to continue saving/displaying it.
		*/
		if ( ! apply_filters( 'wc_mnm_order_item_legacy_part_of_meta', false, $order_item ) ) {
			$formatted_meta = wp_list_filter( $formatted_meta, array( 'key' => __( 'Purchased with', 'woocommerce-mix-and-match-products' ) ), 'NOT' );
			$formatted_meta = wp_list_filter( $formatted_meta, array( 'key' => __( 'Part of', 'woocommerce-mix-and-match-products' ) ), 'NOT' );
		}
		return $formatted_meta;
	}

	/**
	 * Display mnm_container_size meta as Container size.
	 *
	 * @param  string $display_key The front-end label for the meta key.
	 * @param  obj $meta Meta object with key and value properties.
	 * @param  WC_Order_Item $order_item
	 * @return  array
	 */
	public function order_item_meta_label( $display_key, $meta, $order_item ) {

		switch ( $meta->key ) {
			case '_mnm_container_size':
				$display_key = _x( 'Container size', '[Frontend]', 'woocommerce-mix-and-match-products' );
				break;
			case '_mnm_part_of':
				$display_key = _x( 'Part of', '[Frontend]', 'woocommerce-mix-and-match-products' );
				break;
			case '_mnm_purchased_with':
				$display_key = _x( 'Purchased with', '[Frontend]', 'woocommerce-mix-and-match-products' );
				break;
		}
		return $display_key;
	}


	/*-----------------------------------------------------------------------------------*/
	/* Emails */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Indent child items in emails.
	 *
	 * @param  string  $css
	 * @return string
	 */
	public function email_styles( $css ) {
		$css = $css . ".mnm_table_item td:nth-child(1) { padding-left: 2.5em !important; } .mnm_table_container td { border-bottom: none; } .mnm_table_item td { border-top: none; border-bottom: none; font-size: 0.875em; } #body_content table tr.mnm_table_item td ul.wc-item-meta { font-size: inherit; }";
		return $css;
	}


	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|
	--------------------------------------------------------------------------
	*/

	/**
	 * Add-to-cart template for Mix and Match products.
	 *
	 * @deprecated 1.3.0
	 */
	public function add_to_cart_template() {
		wc_deprecated_function( 'WC_Mix_and_Match_Display::add_to_cart_template()', '1.3.0', 'wc_mnm_template_add_to_cart' );
		return wc_mnm_template_add_to_cart();
	}

	/**
	 * QuickView scripts init.
	 *
	 * @deprecated 2.0.0
	 */
	public function quickview_support() {

		wc_deprecated_function( 'WC_Mix_and_Match_Display::quickview_support()', '2.0.0', 'Quick view compatibility has own WC_MNM_QV_Compatibility class.' );

		if ( class_exists( 'WC_MNM_QV_Compatibility' ) ) {
			return WC_MNM_QV_Compatibility::init();
		}
	}

	/**
	 * Enqeue js that wraps child table items in a div in order to apply indentation reliably.
	 *
	 * @since 1.0.2
	 * @deprecated 2.0.0
	 */
	private function enqueue_table_item_js() {

		wc_deprecated_function( 'WC_Mix_and_Match_Display::enqueue_table_item_js()', '2.0.0', 'Styles no longer added via JQuery.' );

		if ( ! $this->enqueued_table_item_js ) {
			wc_enqueue_js(
				"
				var wc_mnm_wrap_mnm_table_item = function() {
					jQuery( '.mnm_table_item > td' ).wrapInner( function() {
						return 0 === $(this).find( '.mnm_child_item_arrow_wrap' ).length ? '<div class=\"mnm_child_item_arrow_wrap\"></div>' : '';
					} );
				}

				jQuery( 'body' ).on( 'updated_checkout updated_cart_totals', function() {
					wc_mnm_wrap_mnm_table_item();
				} );

				wc_mnm_wrap_mnm_table_item();
			"
			);

			$this->enqueued_table_item_js = true;
		}
	}

	/**
	 * Do not show mix and matched items in cart widget.
	 *
	 * @deprecated 2.0.5 - Soft deprecated.
	 *
	 * @param  bool     $show
	 * @param  array    $cart_item
	 * @param  string   $cart_item_key
	 * @return bool
	 */
	public function cart_widget_filter( $show, $cart_item, $cart_item_key ) {
		return $this->cart_widget_item_visible( $show, $cart_item, $cart_item_key );
	}

} // End class.

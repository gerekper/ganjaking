<?php
/**
 * WC_PB_Display class
 *
 * @package  WooCommerce Product Bundles
 * @since    4.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Bundle display functions and filters.
 *
 * @class    WC_PB_Display
 * @version  6.18.0
 */
class WC_PB_Display {

	/**
	 * Indicates whether the bundled table item indent JS has already been enqueued.
	 * @var boolean
	 */
	private $enqueued_bundled_table_item_js = false;

	/**
	 * Workaround for $order arg missing from 'woocommerce_order_item_name' filter - set within the 'woocommerce_order_item_class' filter - @see 'order_item_class()'.
	 * @var boolean|WC_Order
	 */
	private $order_item_order = false;

	/**
	 * Active element position/column when rendering a grid of bundled items, applicable when the "Grid" layout is active.
	 * @var integer
	 */
	private $grid_layout_pos = 1;

	/**
	 * Runtime cache.
	 * @var bool
	 */
	private $display_cart_prices_incl_tax;

	/**
	 * The single instance of the class.
	 * @var WC_PB_Display
	 *
	 * @since 5.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main WC_PB_Display instance. Ensures only one instance of WC_PB_Display is loaded or can be loaded.
	 *
	 * @since  5.0.0
	 *
	 * @return WC_PB_Display
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 5.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-product-bundles' ), '5.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 5.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-product-bundles' ), '5.0.0' );
	}

	/**
	 * Setup hooks and functions.
	 */
	protected function __construct() {

		// Single product template functions and hooks.
		require_once( WC_PB_ABSPATH . 'includes/wc-pb-template-functions.php' );
		require_once( WC_PB_ABSPATH . 'includes/wc-pb-template-hooks.php' );

		// Front end bundle add-to-cart script.
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ), 100 );

		/*
		 * Single-product.
		 */

		// Display info notice when editing a bundle from the cart. Notices are rendered at priority 10.
		add_action( 'woocommerce_before_single_product', array( $this, 'add_edit_in_cart_notice' ), 0 );

		// Modify structured data.
		add_filter( 'woocommerce_structured_data_product_offer', array( $this, 'structured_product_data' ), 10, 2 );

		// Replace 'in_stock' post class with 'insufficient_stock' and 'out_of_stock' post class.
		add_filter( 'woocommerce_post_class', array( $this, 'post_classes' ), 10, 2 );

		/*
		 * Cart.
		 */

		// Filter cart item price.
		add_filter( 'woocommerce_cart_item_price', array( $this, 'cart_item_price' ), 10, 3 );

		// Filter cart item subtotals.
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'cart_item_subtotal' ), 10, 3 );
		add_filter( 'woocommerce_checkout_item_subtotal', array( $this, 'cart_item_subtotal' ), 10, 3 );

		// Keep quantities in sync.
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'cart_item_quantity' ), 10, 2 );
		add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'cart_item_remove_link' ), 10, 2 );

		// Visibility.
		add_filter( 'woocommerce_cart_item_visible', array( $this, 'cart_item_visible' ), 10, 3 );
		add_filter( 'woocommerce_widget_cart_item_visible', array( $this, 'cart_item_visible' ), 10, 3 );
		add_filter( 'woocommerce_checkout_cart_item_visible', array( $this, 'cart_item_visible' ), 10, 3 );

		// Modify titles.
		add_filter( 'woocommerce_cart_item_name', array( $this, 'cart_item_title' ), 10, 3 );

		// Add table item classes.
		add_filter( 'woocommerce_cart_item_class', array( $this, 'cart_item_class' ), 10, 3 );

		// Filter cart item count.
		add_filter( 'woocommerce_cart_contents_count',  array( $this, 'cart_contents_count' ) );

		// Item data.
		add_filter( 'woocommerce_get_item_data', array( $this, 'cart_item_data' ), 10, 2 );

		// Hide thumbnail in cart when 'Hide thumbnail' option is selected.
		add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'cart_item_thumbnail' ), 10, 3);

		// Filter cart widget items.
		add_action( 'woocommerce_before_mini_cart', array( $this, 'add_cart_widget_filters' ) );
		add_action( 'woocommerce_after_mini_cart', array( $this, 'remove_cart_widget_filters' ) );

		/*
		 * Orders.
		 */

		// Filter order item subtotals.
		add_filter( 'woocommerce_order_formatted_line_subtotal', array( $this, 'order_item_subtotal' ), 10, 3 );

		// Visibility.
		add_filter( 'woocommerce_order_item_visible', array( $this, 'order_item_visible' ), 10, 2 );

		// Modify titles.
		add_filter( 'woocommerce_order_item_name', array( $this, 'order_item_title' ), 10, 2 );

		// Add table item classes.
		add_filter( 'woocommerce_order_item_class', array( $this, 'order_item_class' ), 10, 3 );

		// Filter order item count.
		add_filter( 'woocommerce_get_item_count', array( $this, 'order_item_count' ), 10, 3 );

		// Indentation of bundled items in emails.
		add_action( 'woocommerce_email_styles', array( $this, 'email_styles' ) );

		/*
		 * Archives.
		 */

		// Allow ajax add-to-cart to work in WC 2.3/2.4.
		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'loop_add_to_cart_link' ), 10, 2 );
	}

	/**
	 * Frontend scripts.
	 *
	 * @return void
	 */
	public function frontend_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$dependencies = array( 'jquery', 'wc-add-to-cart-variation' );

		/**
		 * Filter to allow adding custom script dependencies here.
		 *
		 * @param  array  $dependencies
		 */
		$dependencies = apply_filters( 'woocommerce_pb_script_dependencies', $dependencies );

		wp_register_script( 'wc-add-to-cart-bundle', WC_PB()->plugin_url() . '/assets/js/frontend/add-to-cart-bundle' . $suffix . '.js', $dependencies, WC_PB()->version, true );

		wp_register_style( 'wc-bundle-css', WC_PB()->plugin_url() . '/assets/css/frontend/single-product.css', false, WC_PB()->version );
		wp_style_add_data( 'wc-bundle-css', 'rtl', 'replace' );

		wp_register_style( 'wc-bundle-style', WC_PB()->plugin_url() . '/assets/css/frontend/woocommerce.css', false, WC_PB()->version );
		wp_style_add_data( 'wc-bundle-style', 'rtl', 'replace' );

		wp_enqueue_style( 'wc-bundle-style' );

		$on_backorder_string       = __( 'Available on backorder', 'woocommerce' );
		$insufficient_stock_string = __( 'Insufficient stock', 'woocommerce-product-bundles' );

		/**
		 * 'woocommerce_bundle_front_end_params' filter.
		 *
		 * @param  array
		 */
		$params = apply_filters( 'woocommerce_bundle_front_end_params', array(
			'i18n_free'                      => __( 'Free!', 'woocommerce' ),
			'i18n_total'                     => __( 'Total: ', 'woocommerce-product-bundles' ),
			'i18n_subtotal'                  => __( 'Subtotal: ', 'woocommerce-product-bundles' ),
			/* translators: %1$s: "Total/Subtotal" string, %2$s: Price, %2$s: Price suffix */
			'i18n_price_format'              => sprintf( _x( '%1$s%2$s%3$s', '"Total/Subtotal" string followed by price followed by price suffix', 'woocommerce-product-bundles' ), '%t', '%p', '%s' ),
			/* translators: %1$s: Regular price, %2$s: Discounted price */
			'i18n_strikeout_price_string'    => sprintf( _x( '<del>%1$s</del> <ins>%2$s</ins>', 'Sale/strikeout price', 'woocommerce-product-bundles' ), '%f', '%t' ),
			/* translators: %1$s: Stock status, %2$s: List of bundled products */
			'i18n_insufficient_stock_list'   => sprintf( _x( '<p class="stock out-of-stock insufficient-stock">%1$s &rarr; %2$s</p>', 'insufficiently stocked items template', 'woocommerce-product-bundles' ), $insufficient_stock_string, '%s' ),
			/* translators: %1$s: Backorder status, %2$s: List of bundled products */
			'i18n_on_backorder_list'         => sprintf( _x( '<p class="stock available-on-backorder">%1$s &rarr; %2$s</p>', 'backordered items template', 'woocommerce-product-bundles' ), $on_backorder_string, '%s' ),
			/* translators: stock status */
			'i18n_insufficient_stock_status' => sprintf( _x( '<p class="stock out-of-stock insufficient-stock">%s</p>', 'insufficiently stocked item exists template', 'woocommerce-product-bundles' ), $insufficient_stock_string ),
			/* translators: backorder stock status */
			'i18n_on_backorder_status'       => sprintf( _x( '<p class="stock available-on-backorder">%s</p>', 'backordered item exists template', 'woocommerce-product-bundles' ), $on_backorder_string ),
			'i18n_select_options'            => __( 'Please choose product options.', 'woocommerce-product-bundles' ),
			/* translators: Bundled product */
			'i18n_select_options_for'        => __( 'Please choose %s options.', 'woocommerce-product-bundles' ),
			'i18n_review_product_addons'     => __( 'Please review product options.', 'woocommerce-product-bundles' ),
			'i18n_enter_valid_price'         => __( 'Please enter valid amounts.', 'woocommerce-product-bundles' ),
			/* translators: Bundled product */
			'i18n_enter_valid_price_for'     => __( 'Please enter a valid %s amount.', 'woocommerce-product-bundles' ),
			/* translators: Item name */
			'i18n_string_list_item'          => _x( '&quot;%s&quot;', 'string list item', 'woocommerce-product-bundles' ),
			/* translators: %1$s: Item before comma, %2$s: Item after comma */
			'i18n_string_list_sep'           => sprintf( _x( '%1$s, %2$s', 'string list item separator', 'woocommerce-product-bundles' ), '%s', '%v' ),
			/* translators: %1$s: Item before "and", %2$s: Item after "and" */
			'i18n_string_list_last_sep'      => sprintf( _x( '%1$s and %2$s', 'string list item last separator', 'woocommerce-product-bundles' ), '%s', '%v' ),
			/* translators: Quantity */
			'i18n_qty_string'                => _x( ' &times; %s', 'qty string', 'woocommerce-product-bundles' ),
			/* translators: Optional item suffix */
			'i18n_optional_string'           => _x( ' &mdash; %s', 'suffix', 'woocommerce-product-bundles' ),
			'i18n_optional'                  => __( 'optional', 'woocommerce-product-bundles' ),
			'i18n_contents'                  => __( 'Includes', 'woocommerce-product-bundles' ),
			/* translators: %1$s: Product title, %2$s: Product meta */
			'i18n_title_meta_string'         => sprintf( _x( '%1$s &ndash; %2$s', 'title followed by meta', 'woocommerce-product-bundles' ), '%t', '%m' ),
			/* translators: %1$s: Product title, %2$s: Product quantity, %3$s: Product price, %4$s: Product suffix */
			'i18n_title_string'              => sprintf( _x( '%1$s%2$s%3$s%4$s', 'title, quantity, price, suffix', 'woocommerce-product-bundles' ), '<span class="item_title">%t</span>', '<span class="item_qty">%q</span>', '', '<span class="item_suffix">%o</span>' ),
			'i18n_unavailable_text'          => __( 'This product is currently unavailable.', 'woocommerce-product-bundles' ),
			/* translators: %1$s: Product titles, %2$s: Resolution message */
			'i18n_validation_issues_for'     => sprintf( __( '<span class="msg-source">%1$s</span> &rarr; <span class="msg-content">%2$s</span>', 'woocommerce-product-bundles' ), '%c', '%e' ),
			'i18n_validation_alert'          => __( 'Please resolve all pending issues before adding this product to your cart.', 'woocommerce-product-bundles' ),
			'i18n_zero_qty_error'            => __( 'Please choose at least 1 item.', 'woocommerce-product-bundles' ),
			/* translators: %1$s: Recurring price part before comma, %2$s: Recurring price part after comma */
			'i18n_recurring_price_join'      => sprintf( _x( '%1$s,</br>%2$s', 'subscription price html', 'woocommerce-product-bundles' ), '%r', '%c' ),
			/* translators: %1$s: Recurring price part before end, %2$s: Recurring price part at end */
			'i18n_recurring_price_join_last' => sprintf( _x( '%1$s, and</br>%2$s', 'subscription price html', 'woocommerce-product-bundles' ), '%r', '%c' ),
			'discounted_price_decimals'      => WC_PB_Product_Prices::get_discounted_price_precision(),
			'currency_symbol'                => get_woocommerce_currency_symbol(),
			'currency_position'              => esc_attr( stripslashes( get_option( 'woocommerce_currency_pos' ) ) ),
			'currency_format_num_decimals'   => wc_pb_price_num_decimals(),
			'currency_format_decimal_sep'    => esc_attr( wc_get_price_decimal_separator() ),
			'currency_format_thousand_sep'   => esc_attr( wc_get_price_thousand_separator() ),
			'currency_format_trim_zeros'     => false === apply_filters( 'woocommerce_price_trim_zeros', false ) ? 'no' : 'yes',
			'is_pao_installed'               => class_exists( 'WC_Product_Addons' ) && defined( 'WC_PRODUCT_ADDONS_VERSION' ) ? 'yes' : 'no',
			'price_display_suffix'           => esc_attr( get_option( 'woocommerce_price_display_suffix' ) ),
			'prices_include_tax'             => esc_attr( get_option( 'woocommerce_prices_include_tax' ) ),
			'tax_display_shop'               => esc_attr( get_option( 'woocommerce_tax_display_shop' ) ),
			'calc_taxes'                     => esc_attr( get_option( 'woocommerce_calc_taxes' ) ),
			'photoswipe_enabled'             => current_theme_supports( 'wc-product-gallery-lightbox' ) ? 'yes' : 'no',
			'responsive_breakpoint'          => 380,
			'zoom_enabled'                   => 'no',
			'force_min_max_qty_input'        => 'yes'
		) );

		wp_localize_script( 'wc-add-to-cart-bundle', 'wc_bundle_params', $params );
	}

	/**
	 * Enqueue js that wraps bundled table items in a div in order to apply indentation reliably.
	 * This obviously sucks but if you can find a CSS-only way to do it better that works reliably with any theme out there, drop us a line, will you?
	 *
	 * @return void
	 */
	private function enqueue_bundled_table_item_js() {

		/**
		 * 'woocommerce_bundled_table_item_js_enqueued' filter.
		 *
		 * Use this filter to get rid of this ugly hack:
		 * Return 'false' and add your own CSS to indent '.bundled_table_item' elements.
		 *
		 * @since  5.5.0
		 *
		 * @param  boolean  $is_enqueued
		 */
		$is_enqueued = apply_filters( 'woocommerce_bundled_table_item_js_enqueued', $this->enqueued_bundled_table_item_js );

		if ( ! $is_enqueued ) {

			wc_enqueue_js( "
				var wc_pb_wrap_bundled_table_item = function() {
					jQuery( '.bundled_table_item td.product-name' ).each( function() {
						var el = jQuery( this );
						if ( el.find( '.bundled-product-name' ).length === 0 ) {
							el.wrapInner( '<div class=\"bundled-product-name bundled_table_item_indent\"></div>' );
						}
					} );
				};

				jQuery( 'body' ).on( 'updated_checkout updated_cart_totals', function() {
					wc_pb_wrap_bundled_table_item();
				} );

				wc_pb_wrap_bundled_table_item();
			" );

			$this->enqueued_bundled_table_item_js = true;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Single-product.
	|--------------------------------------------------------------------------
	*/

	/**
	 * The number of bundled item columns when the "Grid" layout is active.
	 *
	 * @since  5.8.0
	 *
	 * @param  WC_Product_Bundle  $bundle
	 * @return int
	 */
	public function get_grid_layout_columns( $bundle ) {

		/**
		 * 'woocommerce_bundled_items_grid_columns' filter.
		 *
		 * @since  5.8.0
		 *
		 * @param  int                $count
		 * @param  WC_Product_Bundle  $bundle
		 */
		return apply_filters( 'woocommerce_bundled_items_grid_layout_columns', 3, $bundle );
	}

	/**
	 * Class associated with the position of a bundled item in the grid when the "Grid" layout is active.
	 *
	 * @since  5.8.0
	 *
	 * @param  WC_Bundled_Item  $bundled_item
	 * @return int
	 */
	public function get_grid_layout_class( $bundled_item ) {

		$class = '';

		if ( $this->grid_layout_pos === 1 ) {
			$class = 'first';
		} elseif ( $this->grid_layout_pos === $this->get_grid_layout_columns( $bundled_item->get_bundle() ) ) {
			$class = 'last';
		}

		return $class;
	}

	/**
	 * Increments the position of a bundled item in the grid when the "Grid" layout is active.
	 *
	 * @since  5.8.0
	 *
	 * @param  WC_Bundled_Item  $bundled_item
	 * @return void
	 */
	public function incr_grid_layout_pos( $bundled_item ) {

		if ( $this->grid_layout_pos === $this->get_grid_layout_columns( $bundled_item->get_bundle() ) ) {
			$this->grid_layout_pos = 1;
		} else {
			$this->grid_layout_pos++;
		}
	}

	/**
	 * Resets the position of a bundled item in the grid when the "Grid" layout is active.
	 *
	 * @since  5.8.0
	 *
	 * @return void
	 */
	public function reset_grid_layout_pos() {
		$this->grid_layout_pos = 1;
	}

	/**
	 * Display info notice when editing a bundle from the cart.
	 */
	public function add_edit_in_cart_notice() {

		global $product;

		if ( isset( $_GET[ 'update-bundle' ] ) ) {

			$current_product   = $product;
			$updating_cart_key = wc_clean( $_GET[ 'update-bundle' ] );

			if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {
				if ( ! is_a( $current_product, 'WC_Product' ) ) {
					$current_product = WC()->cart->cart_contents[ $updating_cart_key ][ 'data' ];
				}

				if ( is_a( $current_product, 'WC_Product' ) && $current_product->is_type( 'bundle' ) ) {
					/* translators: %1$s: Bundle title */
					$notice = sprintf( __( 'You are currently editing &quot;%1$s&quot;. When finished, click the <strong>Update Cart</strong> button.', 'woocommerce-product-bundles' ), $current_product->get_title() );
				} else {
					$notice = __( 'You are currently editing this bundle. When finished, click the <strong>Update Cart</strong> button.', 'woocommerce-product-bundles' );
				}
				wc_add_notice( $notice, 'notice' );
			}
		}
	}

	/**
	 * Modify structured data for bundle-type products.
	 *
	 * @param  array       $data
	 * @param  WC_Product  $product
	 * @return array
	 */
	public function structured_product_data( $data, $product ) {

		if ( is_object( $product ) && $product->is_type( 'bundle' ) ) {

			$bundle_price = $product->get_bundle_price();

			if ( isset( $data[ 'price' ] ) ) {
				$data[ 'price' ] = $bundle_price;
			}

			if ( isset( $data[ 'priceSpecification' ][ 'price' ] ) ) {
				$data[ 'priceSpecification' ][ 'price' ] = $bundle_price;
			}
		}

		return $data;
	}

	/**
	 * Replace 'in_stock' post class with 'insufficient_stock' and 'out_of_stock' post class.
	 *
	 * @since  5.11.2
	 *
	 * @param  array       $classes
	 * @param  WC_Product  $product
	 * @return array
	 */
	public function post_classes( $classes, $product ) {

		if ( ! $product->is_type( 'bundle' ) ) {
			return $classes;
		}

		if ( in_array( 'instock', $classes ) && 'outofstock' === $product->get_bundled_items_stock_status() ) {
			$classes = array_diff( $classes, array( 'instock' ) );
			$classes = array_merge( $classes, array( 'outofstock', 'insufficientstock' ) );
		}

		return $classes;
	}

	/*
	|--------------------------------------------------------------------------
	| Cart.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Back-compat wrapper for 'WC_Cart::display_price_including_tax'.
	 *
	 * @since  6.3.2
	 *
	 * @return string
	 */
	public function display_cart_prices_including_tax() {

		if ( is_null( $this->display_cart_prices_incl_tax ) ) {
			$this->display_cart_prices_incl_tax = WC()->cart->display_prices_including_tax();
		}

		return $this->display_cart_prices_incl_tax;
	}

	/**
	 * Outputs a formatted subtotal.
	 *
	 * @param  WC_Product  $product
	 * @param  string      $subtotal
	 * @return string
	 */
	public function format_subtotal( $product, $subtotal ) {

		$cart               = WC()->cart;
		$taxable            = $product->is_taxable();
		$formatted_subtotal = wc_price( $subtotal );

		if ( $taxable ) {

			$tax_subtotal = $cart->get_subtotal_tax();

			if ( ! $this->display_cart_prices_including_tax() ) {

				if ( wc_prices_include_tax() && $tax_subtotal > 0 ) {
					$formatted_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
				}

			} else {

				if ( ! wc_prices_include_tax() && $tax_subtotal > 0 ) {
					$formatted_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
				}
			}
		}

		return $formatted_subtotal;
	}

	/**
	 * Modify the front-end price of bundled items and container items depending on their pricing setup.
	 *
	 * @param  double  $price
	 * @param  array   $values
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_price( $price, $cart_item, $cart_item_key ) {

		if ( $bundle_container_item = wc_pb_get_bundled_cart_item_container( $cart_item ) ) {
			$price = $this->get_child_cart_item_price( $price, $cart_item, $cart_item_key, $bundle_container_item );
		} elseif ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {
			$price = $this->get_container_cart_item_price( $price, $cart_item, $cart_item_key );
		}

		return $price;
	}

	/**
	 * Modifies child cart item prices.
	 *
	 * @since  5.8.0
	 *
	 * @param  string  $price
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function get_child_cart_item_price( $price, $cart_item, $cart_item_key, $bundle_container_item = false ) {

		if ( false === $bundle_container_item ) {
			$bundle_container_item = wc_pb_get_bundled_cart_item_container( $cart_item );
		}

		if ( $bundle_container_item ) {

			$bundled_item_id = $cart_item[ 'bundled_item_id' ];

			if ( $bundled_item = $bundle_container_item[ 'data' ]->get_bundled_item( $bundled_item_id ) ) {

				if ( empty( $cart_item[ 'line_subtotal' ] ) && false === $bundled_item->is_priced_individually() ) {

					$price = '';

				} elseif ( false === $bundled_item->is_price_visible( 'cart' ) ) {

					$price = '';

				} elseif ( WC_Product_Bundle::group_mode_has( $bundle_container_item[ 'data' ]->get_group_mode(), 'aggregated_prices' ) ) {

					if ( WC_PB()->compatibility->is_composited_cart_item( $bundle_container_item ) ) {
						$price = '';
					} elseif ( $price ) {
						$price = '<span class="bundled_' . ( $this->is_cart_widget() ? 'mini_cart' : 'table' ) . '_item_price">' . $price . '</span>';
					}

				} elseif ( $price && function_exists( 'wc_cp_get_composited_cart_item_container' ) && ( $composite_container_item_key = wc_cp_get_composited_cart_item_container( $bundle_container_item, WC()->cart->cart_contents, true ) ) ) {

					$composite_container_item = WC()->cart->cart_contents[ $composite_container_item_key ];

					if ( apply_filters( 'woocommerce_add_composited_cart_item_prices', true, $composite_container_item, $composite_container_item_key ) ) {

						$show_price = true;

						if ( empty( $cart_item[ 'line_subtotal' ] ) && false === $bundled_item->is_priced_individually() ) {

							$component_id             = $bundle_container_item[ 'composite_item' ];
							$composite_container_item = wc_cp_get_composited_cart_item_container( $bundle_container_item );

							if ( $composite_container_item ) {
								$component  = $composite_container_item[ 'data' ]->get_component( $component_id );
								$show_price = $component && $component->is_priced_individually();
							}
						}

						if ( $show_price ) {
							$price = '<span class="bundled_' . ( $this->is_cart_widget() ? 'mini_cart' : 'table' ) . '_item_price">' . $price . '</span>';
						} else {
							$price = '';
						}
					}
				}
			}
		}

		return $price;
	}

	/**
	 * Aggregates parent + child cart item prices.
	 *
	 * @param  string  $price
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	private function get_container_cart_item_price( $price, $cart_item, $cart_item_key ) {

		if ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {

			$aggregate_prices = WC_Product_Bundle::group_mode_has( $cart_item[ 'data' ]->get_group_mode(), 'aggregated_prices' );

			if ( $aggregate_prices ) {
				$price = wc_price( self::get_container_cart_item_price_amount( $cart_item, 'price' ) );
			} elseif ( empty( $cart_item[ 'line_subtotal' ] ) ) {
				$hide_container_zero_price = WC_Product_Bundle::group_mode_has( $cart_item[ 'data' ]->get_group_mode(), 'component_multiselect' );
				$price                     = $hide_container_zero_price ? '' : $price;
			}
		}

		return $price;
	}

	/**
	 * Aggregates parent + child cart item prices.
	 *
	 * @since  6.15.0
	 *
	 * @param  array   $cart_item
	 * @return string
	 */
	public function get_container_cart_item_price_amount( $cart_item, $type ) {

		$calc_type           = ! WC_PB()->display->display_cart_prices_including_tax() ? 'excl_tax' : 'incl_tax';
		$price_fn            = 'get_' . $type;
		$bundle_price        = (double) wc_format_decimal( WC_PB_Product_Prices::get_product_price( $cart_item[ 'data' ], array( 'price' => $cart_item[ 'data' ]->$price_fn(), 'calc' => $calc_type ) ), wc_pb_price_num_decimals() );
		$bundled_cart_items  = wc_pb_get_bundled_cart_items( $cart_item, WC()->cart->cart_contents );
		$bundled_items_price = 0.0;

		foreach ( $bundled_cart_items as $bundled_cart_item ) {

			$bundled_item_id        = $bundled_cart_item[ 'bundled_item_id' ];
			$bundled_item_raw_price = $bundled_cart_item[ 'data' ]->$price_fn();

			if ( WC_PB()->compatibility->is_subscription( $bundled_cart_item[ 'data' ] ) && ! WC_PB()->compatibility->is_subscription( $cart_item[ 'data' ] ) ) {

				$bundled_item = $cart_item[ 'data' ]->get_bundled_item( $bundled_item_id );

				if ( $bundled_item ) {
					$bundled_item_raw_recurring_fee = $bundled_cart_item[ 'data' ]->$price_fn();
					$bundled_item_raw_sign_up_fee   = (double) WC_Subscriptions_Product::get_sign_up_fee( $bundled_cart_item[ 'data' ] );
					$bundled_item_raw_price         = $bundled_item->get_up_front_subscription_price( $bundled_item_raw_recurring_fee, $bundled_item_raw_sign_up_fee, $bundled_cart_item[ 'data' ] );
				}
			}

			$bundled_item_qty     = $bundled_cart_item[ 'data' ]->is_sold_individually() ? 1 : $bundled_cart_item[ 'quantity' ] / $cart_item[ 'quantity' ];
			$bundled_item_price   = WC_PB_Product_Prices::get_product_price( $bundled_cart_item[ 'data' ], array( 'price' => $bundled_item_raw_price, 'calc' => $calc_type, 'qty' => $bundled_item_qty ) );
			$bundled_items_price += wc_format_decimal( (double) $bundled_item_price, wc_pb_price_num_decimals() );
		}

		return $bundle_price + $bundled_items_price;
	}

	/**
	 * Modifies child cart item subtotals.
	 *
	 * @since  5.8.0
	 *
	 * @param  string  $price
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function get_child_cart_item_subtotal( $subtotal, $cart_item, $cart_item_key, $bundle_container_item = false ) {

		if ( false === $bundle_container_item ) {
			$bundle_container_item = wc_pb_get_bundled_cart_item_container( $cart_item );
		}

		if ( $bundle_container_item ) {

			$bundled_item_id = $cart_item[ 'bundled_item_id' ];

			if ( $bundled_item = $bundle_container_item[ 'data' ]->get_bundled_item( $bundled_item_id ) ) {

				if ( empty( $cart_item[ 'line_subtotal' ] ) && false === $bundled_item->is_priced_individually() ) {

					$subtotal = '';

				} elseif ( false === $bundled_item->is_price_visible( 'cart' ) ) {

					$subtotal = '';

				} elseif ( WC_Product_Bundle::group_mode_has( $bundle_container_item[ 'data' ]->get_group_mode(), 'aggregated_subtotals' ) ) {

					if ( WC_PB()->compatibility->is_composited_cart_item( $bundle_container_item ) ) {
						$subtotal = '';
					} elseif ( $subtotal ) {
						$subtotal = '<span class="bundled_' . ( $this->is_cart_widget() ? 'mini_cart' : 'table' ) . '_item_subtotal">' . $subtotal . '</span>';
					}

				} elseif ( $subtotal && function_exists( 'wc_cp_get_composited_cart_item_container' ) && ( $composite_container_item_key = wc_cp_get_composited_cart_item_container( $bundle_container_item, WC()->cart->cart_contents, true ) ) ) {

					$composite_container_item = WC()->cart->cart_contents[ $composite_container_item_key ];

					if ( apply_filters( 'woocommerce_add_composited_cart_item_subtotals', true, $composite_container_item, $composite_container_item_key ) ) {

						$show_subtotal = true;

						if ( empty( $cart_item[ 'line_subtotal' ] ) && false === $bundled_item->is_priced_individually() ) {

							$component_id             = $bundle_container_item[ 'composite_item' ];
							$composite_container_item = wc_cp_get_composited_cart_item_container( $bundle_container_item );

							if ( $composite_container_item ) {
								$component     = $composite_container_item[ 'data' ]->get_component( $component_id );
								$show_subtotal = $component && $component->is_priced_individually();
							}
						}

						if ( $show_subtotal ) {
							$subtotal = '<span class="bundled_' . ( $this->is_cart_widget() ? 'mini_cart' : 'table' ) . '_item_subtotal">' . $subtotal . '</span>';
						} else {
							$subtotal = '';
						}
					}
				}
			}
		}

		return $subtotal;
	}

	/**
	 * Aggregates parent + child cart item subtotals.
	 *
	 * @param  string  $subtotal
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	private function get_container_cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {

		if ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {

			$aggregate_subtotals = WC_Product_Bundle::group_mode_has( $cart_item[ 'data' ]->get_group_mode(), 'aggregated_subtotals' );

			if ( $aggregate_subtotals ) {

				$calc_type           = ! $this->display_cart_prices_including_tax() ? 'excl_tax' : 'incl_tax';
				$bundle_price        = WC_PB_Product_Prices::get_product_price( $cart_item[ 'data' ], array( 'price' => $cart_item[ 'data' ]->get_price(), 'calc' => $calc_type, 'qty' => $cart_item[ 'quantity' ] ) );
				$bundled_cart_items  = wc_pb_get_bundled_cart_items( $cart_item, WC()->cart->cart_contents );
				$bundled_items_price = 0.0;

				foreach ( $bundled_cart_items as $bundled_cart_item ) {

					$bundled_item_id        = $bundled_cart_item[ 'bundled_item_id' ];
					$bundled_item_raw_price = $bundled_cart_item[ 'data' ]->get_price();

					if ( WC_PB()->compatibility->is_subscription( $bundled_cart_item[ 'data' ] ) && ! WC_PB()->compatibility->is_subscription( $cart_item[ 'data' ] ) ) {

						$bundled_item = $cart_item[ 'data' ]->get_bundled_item( $bundled_item_id );

						if ( $bundled_item ) {
							$bundled_item_raw_recurring_fee = $bundled_cart_item[ 'data' ]->get_price();
							$bundled_item_raw_sign_up_fee   = (double) WC_Subscriptions_Product::get_sign_up_fee( $bundled_cart_item[ 'data' ] );
							$bundled_item_raw_price         = $bundled_item->get_up_front_subscription_price( $bundled_item_raw_recurring_fee, $bundled_item_raw_sign_up_fee, $bundled_cart_item[ 'data' ] );
						}
					}

					$bundled_item_price  = WC_PB_Product_Prices::get_product_price( $bundled_cart_item[ 'data' ], array( 'price' => $bundled_item_raw_price, 'calc' => $calc_type, 'qty' => $bundled_cart_item[ 'quantity' ] ) );
					$bundled_items_price += wc_format_decimal( (double) $bundled_item_price, wc_pb_price_num_decimals() );
				}

				$subtotal = $this->format_subtotal( $cart_item[ 'data' ], (double) $bundle_price + $bundled_items_price );

			} elseif ( empty( $cart_item[ 'line_subtotal' ] ) ) {

				$hide_container_zero_subtotal = WC_Product_Bundle::group_mode_has( $cart_item[ 'data' ]->get_group_mode(), 'component_multiselect' );
				$subtotal                     = $hide_container_zero_subtotal ? '' : $subtotal;
			}
		}

		return $subtotal;
	}

	/**
	 * Aggregates cart item totals.
	 *
	 * @param  array   $cart_item
	 * @param  string  $type
	 * @return float
	 */
	public static function get_container_cart_item_subtotal_amount( $cart_item, $type ) {

		$bundle_price        = wc_format_decimal( (double) $cart_item[ 'line_' . $type ], wc_pb_price_num_decimals() );
		$bundled_cart_items  = wc_pb_get_bundled_cart_items( $cart_item, WC()->cart->cart_contents );
		$bundled_items_price = 0.0;

		foreach ( $bundled_cart_items as $bundled_cart_item ) {
			$bundled_item_price    = $bundled_cart_item[ 'line_' . $type ];
			$bundled_items_price  += wc_format_decimal( (double) $bundled_item_price, wc_pb_price_num_decimals() );
		}

		return $bundle_price + $bundled_items_price;
	}

	/**
	 * Modifies line item subtotals in the 'cart.php' & 'review-order.php' templates.
	 *
	 * @param  string  $subtotal
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {

		if ( wc_pb_is_bundled_cart_item( $cart_item ) ) {
			$subtotal = $this->get_child_cart_item_subtotal( $subtotal, $cart_item, $cart_item_key );
		} elseif ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {
			$subtotal = $this->get_container_cart_item_subtotal( $subtotal, $cart_item, $cart_item_key );
		}

		return $subtotal;
	}

	/**
	 * Bundled item quantities can't be changed individually. When adjusting quantity for the container item, the bundled products must follow.
	 *
	 * @param  int     $quantity
	 * @param  string  $cart_item_key
	 * @return int
	 */
	public function cart_item_quantity( $quantity, $cart_item_key ) {

		$cart_item = WC()->cart->cart_contents[ $cart_item_key ];

		if ( $container_item = wc_pb_get_bundled_cart_item_container( $cart_item ) ) {

			$bundled_item_id = $cart_item[ 'bundled_item_id' ];
			$bundled_item    = $container_item[ 'data' ]->get_bundled_item( $bundled_item_id );

			$min_quantity = $bundled_item->get_quantity( 'min' );
			$max_quantity = $bundled_item->get_quantity( 'max' );

			if ( $min_quantity === $max_quantity ) {

				$quantity = $cart_item[ 'quantity' ];

			} else {

				$parent_quantity = $container_item[ 'quantity' ];

				$min_qty = $parent_quantity * $min_quantity;
				$max_qty = '' !== $max_quantity ? $parent_quantity * $max_quantity : '';

				if ( ( $max_qty > $min_qty || '' === $max_qty ) && ! $cart_item[ 'data' ]->is_sold_individually() ) {

					$quantity = woocommerce_quantity_input( array(
						'input_name'  => "cart[{$cart_item_key}][qty]",
						'input_value' => $cart_item[ 'quantity' ],
						'min_value'   => $min_qty,
						'max_value'   => $max_qty,
						'step'        => $parent_quantity
					), $cart_item[ 'data' ], false );

				} else {
					$quantity = $cart_item[ 'quantity' ];
				}
			}
		}

		return $quantity;
	}

	/**
	 * Bundled items can't be removed individually from the cart - this hides the remove buttons.
	 *
	 * @param  string  $link
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_remove_link( $link, $cart_item_key ) {

		$cart_item = WC()->cart->cart_contents[ $cart_item_key ];

		if ( $bundle_container_item_key = wc_pb_get_bundled_cart_item_container( $cart_item, false, true ) ) {

			$bundle_container_item = WC()->cart->cart_contents[ $bundle_container_item_key ];
			$bundle                = $bundle_container_item[ 'data' ];
			$bundled_item          = $bundle->get_bundled_item( $cart_item[ 'bundled_item_id' ] );

			if ( ! is_a( $bundled_item, 'WC_Bundled_Item' ) ) {
				return '';
			}

			if ( false === WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'parent_item' ) ) {

				// Remove the entire bundle if this is the last visible item, or if it's a mandatory item.
				$bundled_cart_items = wc_pb_get_bundled_cart_items( $bundle_container_item );
				$is_mandatory       = $bundled_item->get_quantity( 'min', array( 'check_optional' => true ) ) > 0;
				$visible_items      = 0;

				if ( ! empty( $bundled_cart_items ) ) {
					foreach ( $bundled_cart_items as $bundled_cart_item ) {

						$maybe_visible_bundled_item = $bundle->get_bundled_item( $bundled_cart_item[ 'bundled_item_id' ] );

						if ( ! is_a( $maybe_visible_bundled_item, 'WC_Bundled_Item' ) ) {
							continue;
						}

						if ( $maybe_visible_bundled_item->is_visible( 'cart' ) ) {
							$visible_items++;
						}
					}

					if ( $is_mandatory || $visible_items === 1 ) {

						$remove_text = __( 'Remove this bundle', 'woocommerce-product-bundles' );
						$link        = sprintf(
							'<a href="%s" class="remove remove_bundle" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
							esc_url( wc_get_cart_remove_url( $bundle_container_item_key ) ),
							$remove_text,
							esc_attr( $bundle->get_id() ),
							esc_attr( $bundle->get_sku() )
						);

						// Bail out early.
						return $link;
					}
				}
			}

			if ( 0 !== $bundled_item->get_quantity( 'min', array( 'check_optional' => true ) ) ) {
				return '';
			}
		}

		return $link;
	}

	/**
	 * Visibility of bundled item in cart.
	 *
	 * @param  boolean  $visible
	 * @param  array    $cart_item
	 * @param  string   $cart_item_key
	 * @return boolean
	 */
	public function cart_item_visible( $visible, $cart_item, $cart_item_key ) {

		if ( $bundle_container_item = wc_pb_get_bundled_cart_item_container( $cart_item ) ) {

			$bundle          = $bundle_container_item[ 'data' ];
			$bundled_item_id = $cart_item[ 'bundled_item_id' ];

			if ( $bundled_item = $bundle->get_bundled_item( $bundled_item_id ) ) {
				if ( false === $bundled_item->is_visible( 'cart' ) ) {
					$visible = false;
				}
			}

		} elseif ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {

			$bundle = $cart_item[ 'data' ];

			if ( false === WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'parent_item' ) ) {
				$visible = false;
			}
		}

		return $visible;
	}

	/**
	 * Override bundled item title in cart/checkout templates.
	 *
	 * @param  string  $content
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_title( $content, $cart_item, $cart_item_key ) {

		if ( $bundle_container_item_key = wc_pb_get_bundled_cart_item_container( $cart_item, false, true ) ) {

			$bundle_container_item = WC()->cart->cart_contents[ $bundle_container_item_key ];
			$bundle                = $bundle_container_item[ 'data' ];

			if ( WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'child_item_indent' ) ) {
				$this->enqueue_bundled_table_item_js();
			}

			if ( WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'faked_parent_item' ) ) {

				$bundled_cart_item_keys = wc_pb_get_bundled_cart_items( $bundle_container_item, false, true );

				if ( ! empty( $bundled_cart_item_keys ) && current( $bundled_cart_item_keys ) === $cart_item_key ) {

					if ( function_exists( 'is_cart' ) && is_cart() && ! $this->is_cart_widget() ) {

						if ( $bundle->is_editable_in_cart( $bundle_container_item ) ) {

							$edit_in_cart_link = esc_url( add_query_arg( array( 'update-bundle' => $bundle_container_item_key ), $bundle->get_permalink( $bundle_container_item ) ) );
							$edit_in_cart_text = _x( 'Edit', 'edit in cart link text', 'woocommerce-product-bundles' );
							/* translators: %1$s: Product title, %2$s: Edit in cart URL, %3$s: Edit in cart text */
							$content           = sprintf( _x( '%1$s<br/><a class="edit_bundle_in_cart_text edit_in_cart_text" rel="no-follow" href="%2$s"><small>%3$s</small></a>', 'edit in cart text', 'woocommerce-product-bundles' ), $content, $edit_in_cart_link, $edit_in_cart_text );
						}
					}
				}
			}

		} elseif ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {

			$bundle = $cart_item[ 'data' ];

			if ( function_exists( 'is_cart' ) && is_cart() && ! $this->is_cart_widget() ) {

				if ( $bundle->is_editable_in_cart( $cart_item ) ) {

					$edit_in_cart_link = esc_url( add_query_arg( array( 'update-bundle' => $cart_item_key, 'quantity' => $cart_item[ 'quantity' ] ), $bundle->get_permalink( $cart_item ) ) );
					$edit_in_cart_text = _x( 'Edit', 'edit in cart link text', 'woocommerce-product-bundles' );
					/* translators: %1$s: Product title, %2$s: Edit in cart URL, %3$s: Edit in cart text */
					$content           = sprintf( _x( '%1$s<br/><a class="edit_bundle_in_cart_text edit_in_cart_text" href="%2$s"><small>%3$s</small></a>', 'edit in cart text', 'woocommerce-product-bundles' ), $content, $edit_in_cart_link, $edit_in_cart_text );
				}

				if ( WC_Product_Bundle::group_mode_has( $cart_item[ 'data' ]->get_group_mode(), 'parent_cart_item_meta' ) ) {
					$content .= $this->get_bundle_container_cart_item_data( $cart_item, array( 'html' => true ) );
				}
			}
		}

		return $content;
	}

	/**
	 * Change the tr class of bundled items in cart templates to allow their styling.
	 *
	 * @param  string  $classname
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_class( $classname, $cart_item, $cart_item_key ) {

		if ( $bundle_container_item = wc_pb_get_bundled_cart_item_container( $cart_item ) ) {

			$bundle = $bundle_container_item[ 'data' ];

			if ( WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'child_item_indent' ) ) {

				if ( WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'faked_parent_item' ) ) {

					// Ensure this isn't the first child (shamelessly assuming that the first one is visible).
					$bundled_cart_item_keys = wc_pb_get_bundled_cart_items( $bundle_container_item, false, true );

					if ( empty( $bundled_cart_item_keys ) || current( $bundled_cart_item_keys ) !== $cart_item_key ) {
						$classname .= ' bundled_table_item';
					}

				} else {
					$classname .= ' bundled_table_item';
				}
			}

		} elseif ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {
			$classname .= ' bundle_table_item';
		}

		return $classname;
	}

	/**
	 * Filters the reported number of cart items. Omit:
	 *
	 * - Hidden parent items.
	 * - Hidden or indented child items.
	 *
	 * @param  int  $count
	 * @return int
	 */
	public function cart_contents_count( $count ) {

		$cart     = WC()->cart->get_cart();
		$subtract = 0;

		foreach ( $cart as $cart_item_key => $cart_item ) {
			if ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {

				$parent_item_visible = $this->cart_item_visible( true, $cart_item, $cart_item_key );

				if ( ! $parent_item_visible ) {
					$subtract += $cart_item[ 'quantity' ];
				}

				$bundled_cart_items = wc_pb_get_bundled_cart_items( $cart_item );

				foreach ( $bundled_cart_items as $bundled_item_key => $bundled_cart_item ) {

					$is_bundled_item_indented = $cart_item[ 'data' ]->is_type( 'bundle' ) && WC_Product_Bundle::group_mode_has( $cart_item[ 'data' ]->get_group_mode(), 'child_item_indent' );
					$is_bundled_item_visible  = false === $this->cart_item_visible( true, $bundled_cart_item, $bundled_item_key );

					if ( $is_bundled_item_indented || $is_bundled_item_visible ) {
						$subtract += $bundled_cart_item[ 'quantity' ];
					}
				}
			}
		}

		return $count - $subtract;
	}

	/**
	 * Add "Part of" cart item data to bundled items.
	 *
	 * @param  array  $data
	 * @param  array  $cart_item
	 * @return array
	 */
	public function cart_item_data( $data, $cart_item ) {

		// When serving a Store API request...
		if ( WC_PB_Core_Compatibility::is_store_api_request() && wc_pb_is_bundle_container_cart_item( $cart_item ) ) {

			if ( ! $cart_item[ 'data' ]->is_type( 'bundle' ) ) {
				return $data;
			}

			$bundle = $cart_item[ 'data' ];

			// Add bundled items as metadata.
			$data = array_merge( $data, $this->get_bundle_container_cart_item_data( $cart_item, array( 'aggregated' => false ) ) );
		}

		if ( $container = wc_pb_get_bundled_cart_item_container( $cart_item ) ) {

			$bundle      = $container[ 'data' ];
			$part_of_key = __( 'Part of', 'woocommerce-product-bundles' );
			$exists      = in_array( $part_of_key, array_keys( $data ) );

			if ( ! $exists && WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'child_item_meta' ) ) {
				$data[] = array(
					'key'   => $part_of_key,
					'value' => $bundle->get_title()
				);
			}
		}

		return $data;
	}

	/**
	 * Hide thumbnail in cart when 'Hide thumbnail' option is selected.
	 *
	 * @param  string  $image
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */

	public function cart_item_thumbnail( $image, $cart_item, $cart_item_key ) {

		if ( $bundle_container_item = wc_pb_get_bundled_cart_item_container( $cart_item ) ) {

			$bundled_item_id = $cart_item[ 'bundled_item_id' ];

			if ( $bundled_item = $bundle_container_item[ 'data' ]->get_bundled_item( $bundled_item_id) ) {

				if ( false === $bundled_item->is_thumbnail_visible() ) {

					$is_faked_parent_item = false;

					if ( WC_Product_Bundle::group_mode_has( $bundle_container_item[ 'data' ]->get_group_mode(), 'faked_parent_item' ) ) {

						$bundled_cart_item_keys = wc_pb_get_bundled_cart_items( $bundle_container_item, false, true );

						if ( ! empty( $bundled_cart_item_keys ) && current( $bundled_cart_item_keys ) === $cart_item_key ) {
							$is_faked_parent_item = true;
						}
					}

					if ( ! $is_faked_parent_item ) {
						$image = '';
					}
				}
			}
		}

		return $image;
	}

	/**
	 * Rendering cart widget?
	 *
	 * @since  5.8.0
	 * @return boolean
	 */
	protected function is_cart_widget() {
		return did_action( 'woocommerce_before_mini_cart' ) > did_action( 'woocommerce_after_mini_cart' );
	}

	/**
	 * Add cart widget filters.
	 *
	 * @return void
	 */
	public function add_cart_widget_filters() {
		add_filter( 'woocommerce_mini_cart_item_class', array( $this, 'mini_cart_item_class' ), 10, 2 );
		add_filter( 'woocommerce_widget_cart_item_visible', array( $this, 'cart_widget_item_visible' ), 10, 3 );
		add_filter( 'woocommerce_widget_cart_item_quantity', array( $this, 'cart_widget_item_qty' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_name', array( $this, 'cart_widget_container_item_name' ), 10, 3 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'cart_widget_container_item_data' ), 10, 2 );
	}

	/**
	 * Remove cart widget filters.
	 *
	 * @return void
	 */
	public function remove_cart_widget_filters() {
		remove_filter( 'woocommerce_mini_cart_item_class', array( $this, 'mini_cart_item_class' ), 10, 2 );
		remove_filter( 'woocommerce_widget_cart_item_visible', array( $this, 'cart_widget_item_visible' ), 10, 3 );
		remove_filter( 'woocommerce_widget_cart_item_quantity', array( $this, 'cart_widget_item_qty' ), 10, 3 );
		remove_filter( 'woocommerce_cart_item_name', array( $this, 'cart_widget_container_item_name' ), 10, 3 );
		remove_filter( 'woocommerce_get_item_data', array( $this, 'cart_widget_container_item_data' ), 10, 2 );
	}

	/**
	 * Change the li class of composite parent/child items in mini-cart templates to allow their styling.
	 *
	 * @since  5.8.0
	 *
	 * @param  string  $classname
	 * @param  array   $cart_item
	 * @return string
	 */
	public function mini_cart_item_class( $classname, $cart_item ) {

		if ( wc_pb_is_bundled_cart_item( $cart_item ) ) {
			$classname .= ' bundled_mini_cart_item';
		} elseif ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {
			$classname .= ' bundle_container_mini_cart_item';
		}

		return $classname;
	}


	/**
	 * Conditionally hide bundled items in the mini cart.
	 *
	 * @param  boolean  $show
	 * @param  array    $cart_item
	 * @param  string   $cart_item_key
	 * @return boolean
	 */
	public function cart_widget_item_visible( $show, $cart_item, $cart_item_key ) {

		if ( $container = wc_pb_get_bundled_cart_item_container( $cart_item ) ) {

			$bundle = $container[ 'data' ];

			if ( WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'parent_item' ) && WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'parent_cart_widget_item_meta' ) ) {
				$show = false;
			} elseif ( WC_Product_Bundle::group_mode_has( $bundle->get_group_mode(), 'component_multiselect' ) ) {
				$show = false;
			}
		}

		return $show;
	}

	/**
	 * Tweak bundle container qty.
	 *
	 * @param  bool    $qty
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return bool
	 */
	public function cart_widget_item_qty( $qty, $cart_item, $cart_item_key ) {

		if ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {

			if ( WC_Product_Bundle::group_mode_has( $cart_item[ 'data' ]->get_group_mode(), 'aggregated_subtotals' ) ) {

				if ( WC_PB()->cart->container_cart_item_contains( $cart_item, 'sold_individually' ) ) {
					$qty = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $cart_item[ 'data' ], $cart_item[ 'quantity' ] ), $cart_item, $cart_item_key );
				}

			} elseif ( empty( $cart_item[ 'line_subtotal' ] ) && $cart_item[ 'data' ]->contains( 'priced_individually' ) ) {

				$bundled_item_keys = wc_pb_get_bundled_cart_items( $cart_item, WC()->cart->cart_contents, true );

				if ( ! empty( $bundled_item_keys ) ) {
					$qty = '';
				}
			}

		} elseif ( $bundle_container_item = wc_pb_get_bundled_cart_item_container( $cart_item ) ) {

			if ( ! empty( $cart_item[ 'line_subtotal' ] ) ) {
				return $qty;
			}

			$bundled_item_id = $cart_item[ 'bundled_item_id' ];
			$bundled_item    = $bundle_container_item[ 'data' ]->get_bundled_item( $cart_item[ 'bundled_item_id' ] );

			if ( ! $bundled_item ) {
				return $qty;
			}

			if ( ! $bundled_item->is_priced_individually() && ! WC_Product_Bundle::group_mode_has( $bundle_container_item[ 'data' ]->get_group_mode(), 'parent_cart_widget_item_meta' ) ) {
				$qty = '';
			}
		}

		return $qty;
	}

	/**
	 * Tweak bundle container name.
	 *
	 * @param  bool    $show
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return bool
	 */
	public function cart_widget_container_item_name( $name, $cart_item, $cart_item_key ) {

		if ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {

			if ( WC_Product_Bundle::group_mode_has( $cart_item[ 'data' ]->get_group_mode(), 'aggregated_subtotals' ) ) {

				if ( WC_PB()->cart->container_cart_item_contains( $cart_item, 'sold_individually' ) && ! WC_PB()->compatibility->is_composited_cart_item( $cart_item ) ) {
					$name = WC_PB_Helpers::format_product_shop_title( $name, $cart_item[ 'quantity' ] );
				}

			} elseif ( empty( $cart_item[ 'line_subtotal' ] ) && $cart_item[ 'data' ]->contains( 'priced_individually' ) ) {

				$bundled_item_keys = wc_pb_get_bundled_cart_items( $cart_item, WC()->cart->cart_contents, true );

				if ( ! empty( $bundled_item_keys ) ) {
					$name = WC_PB_Helpers::format_product_shop_title( $name, $cart_item[ 'quantity' ] );
				}
			}

		} elseif ( $bundle_container_item = wc_pb_get_bundled_cart_item_container( $cart_item ) ) {

			if ( ! empty( $cart_item[ 'line_subtotal' ] ) ) {
				return $name;
			}

			$bundled_item_id = $cart_item[ 'bundled_item_id' ];
			$bundled_item    = $bundle_container_item[ 'data' ]->get_bundled_item( $cart_item[ 'bundled_item_id' ] );

			if ( ! $bundled_item ) {
				return $name;
			}

			if ( ! $bundled_item->is_priced_individually() && ! WC_Product_Bundle::group_mode_has( $bundle_container_item[ 'data' ]->get_group_mode(), 'parent_cart_widget_item_meta' ) ) {
				$name = WC_PB_Helpers::format_product_shop_title( $name, $cart_item[ 'quantity' ] );
			}
		}

		return $name;
	}

	/**
	 * Gets bundled content data.
	 *
	 * @since  5.8.0
	 *
	 * @param  array  $cart_item
	 * @param  mixed  $arg
	 * @return array
	 */
	public function get_bundle_container_cart_item_data( $cart_item, $arg = array() ) {

		if ( is_array( $arg ) ) {

			$args = wp_parse_args( $arg, array(
				'html'       => false,
				'aggregated' => true,
			) );

		} else {

			$args = array(
				'html'       => ( bool ) $arg,
				'aggregated' => true,
			);
		}

		$data               = array();
		$bundled_cart_items = wc_pb_get_bundled_cart_items( $cart_item );

		if ( ! empty( $bundled_cart_items ) ) {

			$woocommerce_bundle_container_cart_item_data = array();

			foreach ( $bundled_cart_items as $bundled_cart_item_key => $bundled_cart_item ) {

				$bundled_item_id = $bundled_cart_item[ 'bundled_item_id' ];

				if ( $bundled_item = $cart_item[ 'data' ]->get_bundled_item( $bundled_item_id ) ) {

					if ( $bundled_item->is_visible( 'cart' ) ) {

						$bundled_item_description = WC_PB_Helpers::format_product_shop_title( $bundled_cart_item[ 'data' ]->get_name(), $bundled_cart_item[ 'quantity' ] );

						if ( $args[ 'aggregated' ] ) {

							$woocommerce_bundle_container_cart_item_data[] = $bundled_item_description;

						} else {

							$data[] = array(
								'key'   => __( 'Includes', 'woocommerce-product-bundles' ),
								'value' => $bundled_item_description
							);
						}
					}
				}
			}

			if ( ! empty( $woocommerce_bundle_container_cart_item_data ) ) {

				$data[] = array(
					'key'   => __( 'Includes', 'woocommerce-product-bundles' ),
					'value' => ( string ) implode( '<br/>', $woocommerce_bundle_container_cart_item_data )
				);
			}
		}

		if ( $args[ 'html' ] ) {

			$formatted_data = '';

			if ( ! empty( $data ) ) {

				ob_start();

				wc_get_template( 'cart/bundle-container-item-data.php', array(
					'data' => $data
				), false, WC_PB()->plugin_path() . '/templates/' );

				$formatted_data = ob_get_clean();
			}

			$data = $formatted_data;
		}

		/**
		 * 'woocommerce_bundle_container_cart_item_data' filter.
		 *
		 * @since  6.15.0
		 *
		 * @param  string  $bundled_item_description
		 * @param  array   $bundled_cart_item
		 * @param  string  $bundled_cart_item_key
		 */
		return apply_filters( 'woocommerce_bundle_container_cart_item_data', $data, $cart_item, $args );
	}

	/**
	 * Adds content data as parent item meta (by default in the mini-cart only).
	 *
	 * @param  array  $data
	 * @param  array  $cart_item
	 * @return array
	 */
	public function cart_widget_container_item_data( $data, $cart_item ) {

		if ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {
			if ( WC_Product_Bundle::group_mode_has( $cart_item[ 'data' ]->get_group_mode(), 'parent_cart_widget_item_meta' ) ) {
				$data = array_merge( $data, $this->get_bundle_container_cart_item_data( $cart_item ) );
			}
		}

		return $data;
	}

	/*
	|--------------------------------------------------------------------------
	| Orders.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Modify the subtotal of order items depending on their pricing setup.
	 *
	 * @param  string         $subtotal
	 * @param  WC_Order_Item  $item
	 * @param  WC_Order       $order
	 * @return string
	 */
	public function order_item_subtotal( $subtotal, $item, $order ) {

		// If it's a bundled item...
		if ( $bundle_container_item = wc_pb_get_bundled_order_item_container( $item, $order ) ) {

			$bundled_item_priced_individually = $item->get_meta( '_bundled_item_priced_individually', true );
			$bundled_item_price_hidden        = $item->get_meta( '_bundled_item_price_hidden', true );

			// Back-compat.
			if ( ! in_array( $bundled_item_priced_individually, array( 'yes', 'no' ) ) ) {
				$bundled_item_priced_individually = isset( $bundle_container_item[ 'per_product_pricing' ] ) ? $bundle_container_item[ 'per_product_pricing' ] : get_post_meta( $bundle_container_item[ 'product_id' ], '_wc_pb_v4_per_product_pricing', true );
			}

			$is_pip = WC_PB()->compatibility->is_pip( 'invoice' );

			if ( 'no' === $bundled_item_priced_individually && $item->get_subtotal( 'edit' ) == 0 ) {

				$subtotal = '';

			} elseif ( ! $is_pip && 'yes' === $bundled_item_price_hidden ) {

				$subtotal = '';

			} elseif ( ! $is_pip ) {

				$group_mode = $bundle_container_item->get_meta( '_bundle_group_mode', true );
				$group_mode = $group_mode ? $group_mode : 'parent';

				if ( WC_Product_Bundle::group_mode_has( $group_mode, 'aggregated_subtotals' ) ) {

					if ( WC_PB()->compatibility->is_composited_order_item( $bundle_container_item, $order ) ) {
						$subtotal = '';
					} elseif ( $subtotal ) {
						$subtotal = '<span class="bundled_table_item_subtotal">' . $subtotal . '</span>';
					}

				} elseif ( $subtotal && function_exists( 'wc_cp_get_composited_order_item_container' ) && ( $composite_container_item = wc_cp_get_composited_order_item_container( $bundle_container_item, $order ) ) ) {

					if ( apply_filters( 'woocommerce_add_composited_order_item_subtotals', true, $composite_container_item, $order ) ) {

						$show_subtotal = true;

						if ( $item->get_subtotal( 'edit' ) == 0 && 'yes' === $bundled_item_priced_individually ) {
							if ( $component_priced_individually = $bundle_container_item->get_meta( '_component_priced_individually', true ) ) {
								$show_subtotal = 'yes' === $component_priced_individually;
							}
						}

						if ( $show_subtotal ) {
							$subtotal = '<span class="bundled_table_item_subtotal">' . $subtotal . '</span>';
						} else {
							$subtotal = '';
						}
					}
				}
			}

		// If it's a bundle (parent item)...
		} elseif ( wc_pb_is_bundle_container_order_item( $item ) ) {

			if ( ! isset( $item->child_subtotals_added ) ) {

				$group_mode = $item->get_meta( '_bundle_group_mode', true );
				$group_mode = $group_mode ? $group_mode : 'parent';

				$children            = wc_pb_get_bundled_order_items( $item, $order );
				$aggregate_subtotals = WC_Product_Bundle::group_mode_has( $group_mode, 'aggregated_subtotals' ) && false === WC_PB()->compatibility->is_pip( 'invoice' );

				// Aggregate subtotals if required the bundle's group mode. Important: Don't aggregate when rendering PIP invoices!
				if ( $aggregate_subtotals ) {

					if ( ! empty( $children ) ) {

						// Create a clone to ensure the original item will not be modified.
						$cloned_item = clone $item;

						foreach ( $children as $child ) {
							$cloned_item->set_subtotal( $cloned_item->get_subtotal( 'edit' ) + round( $child->get_subtotal( 'edit' ), wc_pb_price_num_decimals() ) );
							$cloned_item->set_subtotal_tax( $cloned_item->get_subtotal_tax( 'edit' ) + round( $child->get_subtotal_tax( 'edit' ), wc_pb_price_num_decimals() ) );
						}

						$cloned_item->child_subtotals_added = 'yes';

						$subtotal = $order->get_formatted_line_subtotal( $cloned_item );
					}
				}
			}
		}

		return $subtotal;
	}

	/**
	 * Visibility of bundled item in orders.
	 *
	 * @param  boolean  $visible
	 * @param  array    order_item
	 * @return boolean
	 */
	public function order_item_visible( $visible, $order_item ) {

		if ( wc_pb_is_bundled_order_item( $order_item ) ) {

			$bundled_item_hidden = $order_item->get_meta( '_bundled_item_hidden' );

			if ( ! empty( $bundled_item_hidden ) ) {
				$visible = false;
			}

		} elseif ( wc_pb_is_bundle_container_order_item( $order_item ) ) {

			$group_mode = $order_item->get_meta( '_bundle_group_mode', true );
			$group_mode = $group_mode ? $group_mode : 'parent';

			if ( false === WC_Product_Bundle::group_mode_has( $group_mode, 'parent_item' ) ) {
				$visible = false;
			}
		}

		return $visible;
	}

	/**
	 * Override bundled item title in order-details template.
	 *
	 * @param  string  $content
	 * @param  array   $order_item
	 * @return string
	 */
	public function order_item_title( $content, $order_item ) {

		if ( false !== $this->order_item_order && wc_pb_is_bundled_order_item( $order_item, $this->order_item_order ) ) {

			$this->order_item_order = false;

			$group_mode = $order_item->get_meta( '_bundle_group_mode', true );
			$group_mode = $group_mode ? $group_mode : 'parent';

			if ( WC_Product_Bundle::group_mode_has( $group_mode, 'child_item_indent' ) ) {
				if ( did_action( 'woocommerce_view_order' ) || did_action( 'woocommerce_thankyou' ) || did_action( 'before_woocommerce_pay' ) || did_action( 'woocommerce_account_view-subscription_endpoint' ) ) {
					$this->enqueue_bundled_table_item_js();
				}
			}
		}

		return $content;
	}

	/**
	 * Add class to bundled items in order templates.
	 *
	 * @param  string  $classname
	 * @param  array   $order_item
	 * @return string
	 */
	public function order_item_class( $classname, $order_item, $order ) {

		if ( $bundle_container_order_item = wc_pb_get_bundled_order_item_container( $order_item, $order ) ) {

			$group_mode = $bundle_container_order_item->get_meta( '_bundle_group_mode', true );
			$group_mode = $group_mode ? $group_mode : 'parent';

			if ( WC_Product_Bundle::group_mode_has( $group_mode, 'child_item_indent' ) ) {

				if ( WC_Product_Bundle::group_mode_has( $group_mode, 'faked_parent_item' ) ) {

					// Ensure this isn't the first child.
					$bundled_order_item_ids = wc_pb_get_bundled_order_items( $bundle_container_order_item, $order, true );

					if ( empty( $bundled_order_item_ids ) || current( $bundled_order_item_ids ) !== $order_item->get_id() ) {
						$classname .= ' bundled_table_item';
					}

				} else {
					$classname .= ' bundled_table_item';
				}
			}

			$this->order_item_order = $order;

		} elseif ( wc_pb_is_bundle_container_order_item( $order_item ) ) {
			$classname .= ' bundle_table_item';
		}

		return $classname;
	}

	/**
	 * Filters the reported number of order items.
	 *
	 * @param  int       $count
	 * @param  string    $type
	 * @param  WC_Order  $order
	 * @return int
	 */
	public function order_item_count( $count, $type, $order ) {

		$subtract = 0;

		if ( function_exists( 'is_account_page' ) && is_account_page() ) {

			foreach ( $order->get_items() as $item ) {
				if ( wc_pb_is_bundle_container_order_item( $item, $order ) ) {

					$parent_item_visible = $this->order_item_visible( true, $item );

					if ( ! $parent_item_visible ) {
						$subtract += $item->get_quantity();
					}


					$bundled_order_items = wc_pb_get_bundled_order_items( $item, $order );

					foreach ( $bundled_order_items as $bundled_item_key => $bundled_order_item ) {
						if ( ! $parent_item_visible ) {
							if ( ! $this->order_item_visible( true, $bundled_order_item ) ) {
								$subtract += $bundled_order_item->get_quantity();
							}
						} else {
							$subtract += $bundled_order_item->get_quantity();
						}
					}
				}
			}
		}

		return $count - $subtract;
	}

	/**
	 * Indent bundled items in emails.
	 *
	 * @param  string  $css
	 * @return string
	 */
	public function email_styles( $css ) {

		if ( is_rtl() ) {
			$css .= ' .bundled_table_item td:first-of-type { padding-right: 2.5em !important; } .bundled_table_item td { border-top: none; font-size: 0.875em; } #body_content table tr.bundled_table_item td ul.wc-item-meta { font-size: inherit; } .bundled_table_item_subtotal { white-space: nowrap; } .bundled_table_item_subtotal:after { display: inline-block; width: 1em; height: 1em; background: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzODQgNTEyIj48cGF0aCBkPSJNMzM2LjEgMTY4LjFjLTkuMzc1IDkuMzc1LTI0LjU2IDkuMzc1LTMzLjk0IDBMMjE2IDgxLjk0VjQ4OGMwIDEzLjI1LTEwLjc1IDI0LTI0IDI0SDI0QzEwLjc1IDUxMiAwIDUwMS4zIDAgNDg4czEwLjc1LTI0IDI0LTI0aDE0NFY4MS45NEw4MC45NyAxNjguMWMtOS4zNzUgOS4zNzUtMjQuNTYgOS4zNzUtMzMuOTQgMHMtOS4zNzUtMjQuNTYgMC0zMy45NGwxMjgtMTI4QzE3OS43IDIuMzQ0IDE4NS44IDAgMTkyIDBzMTIuMjggMi4zNDQgMTYuOTcgNy4wMzFsMTI4IDEyOEMzNDYuMyAxNDQuNCAzNDYuMyAxNTkuNiAzMzYuMSAxNjguMXoiLz48L3N2Zz4="); background-repeat: no-repeat; background-position: right; background-size: contain; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; -webkit-transform: rotate(90deg); -ms-transform:rotate(90deg); transform: rotate(90deg); content:""; margin:0 8px 0 2px; opacity:.25 } ';
		} else {
			$css .= ' .bundled_table_item td:first-of-type { padding-left: 2.5em !important; } .bundled_table_item td { border-top: none; font-size: 0.875em; } #body_content table tr.bundled_table_item td ul.wc-item-meta { font-size: inherit; } .bundled_table_item_subtotal { white-space: nowrap; } .bundled_table_item_subtotal:after { display: inline-block; width: 1em; height: 1em; background: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzODQgNTEyIj48cGF0aCBkPSJNMzM2LjEgMzc2LjFsLTEyOCAxMjhDMjA0LjMgNTA5LjcgMTk4LjIgNTEyIDE5MS4xIDUxMnMtMTIuMjgtMi4zNDQtMTYuOTctNy4wMzFsLTEyOC0xMjhjLTkuMzc1LTkuMzc1LTkuMzc1LTI0LjU2IDAtMzMuOTRzMjQuNTYtOS4zNzUgMzMuOTQgMEwxNjggNDMwLjFWNDhoLTE0NEMxMC43NSA0OCAwIDM3LjI1IDAgMjRTMTAuNzUgMCAyNCAwSDE5MmMxMy4yNSAwIDI0IDEwLjc1IDI0IDI0djQwNi4xbDg3LjAzLTg3LjAzYzkuMzc1LTkuMzc1IDI0LjU2LTkuMzc1IDMzLjk0IDBTMzQ2LjMgMzY3LjYgMzM2LjEgMzc2LjF6Ii8+PC9zdmc+"); background-repeat: no-repeat; background-position: right; background-size: contain; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; transform: rotate(90deg); content: ""; margin: 0 2px 0 8px; opacity: .25; } ';
		}
		return $css;
	}

	/*
	|--------------------------------------------------------------------------
	| Archives.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Used to fix QuickView support when:
	 * - ajax add-to-cart is active and
	 * - QuickView operates without a separate button.
	 * Since WC 2.5+ this is (almost) a relic.
	 *
	 * @param  string      $link
	 * @param  WC_Product  $product
	 * @return string
	 */
	public function loop_add_to_cart_link( $link, $product ) {

		if ( $product->is_type( 'bundle' ) ) {

			if ( ! $product->is_in_stock() || $product->has_options() ) {
				$link = str_replace( array( 'product_type_bundle', 'ajax_add_to_cart' ), array( 'product_type_bundle product_type_bundle_input_required', '' ), $link );
			}
		}

		return $link;
	}

	/*
	|--------------------------------------------------------------------------
	| Other.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Enhance price filter widget meta query to include results based on max '_wc_sw_max_price' meta.
	 *
	 * @param  array     $meta_query
	 * @param  WC_Query  $wc_query
	 * @return array
	 */
	public function price_filter_query_params( $meta_query, $wc_query ) {

		if ( isset( $meta_query[ 'price_filter' ] ) && isset( $meta_query[ 'price_filter' ][ 'price_filter' ] ) && ! isset( $meta_query[ 'price_filter' ][ 'sw_price_filter' ] ) ) {

			$min = isset( $_GET[ 'min_price' ] ) ? floatval( $_GET[ 'min_price' ] ) : 0;
			$max = isset( $_GET[ 'max_price' ] ) ? floatval( $_GET[ 'max_price' ] ) : 9999999999;

			$price_meta_query = $meta_query[ 'price_filter' ];
			$price_meta_query = array(
				'sw_price_filter' => true,
				'price_filter'    => true,
				'relation'        => 'OR',
				$price_meta_query,
				array(
					'relation' => 'AND',
					array(
						'key'     => '_price',
						'compare' => '<=',
						'type'    => 'DECIMAL',
						'value'   => $max
					),
					array(
						'key'     => '_wc_sw_max_price',
						'compare' => '>=',
						'type'    => 'DECIMAL',
						'value'   => $min
					)
				)
			);

			$meta_query[ 'price_filter' ] = $price_meta_query;
		}

		return $meta_query;
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated.
	|--------------------------------------------------------------------------
	*/

	public function order_table_item_title( $content, $order_item ) {
		_deprecated_function( __METHOD__ . '()', '5.5.0', __CLASS__ . '::order_item_title()' );
		return $this->order_item_title( $content, $order_item );
	}
	public function woo_bundles_loop_add_to_cart_link( $link, $product ) {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::loop_add_to_cart_link()' );
		return $this->loop_add_to_cart_link( $link, $product );
	}
	public function woo_bundles_in_cart_item_title( $content, $cart_item_values, $cart_item_key ) {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::cart_item_title()' );
		return $this->cart_item_title( $content, $cart_item_values, $cart_item_key );
	}
	public function woo_bundles_order_table_item_title( $content, $order_item ) {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::order_item_title()' );
		return $this->order_item_title( $content, $order_item );
	}
	public function woo_bundles_table_item_class( $classname, $values ) {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::table_item_class()' );
		return false !== strpos( $classname, 'cart_item' ) ? $this->cart_item_class( $classname, $values, false ) : $this->order_item_class( $classname, $values, false );
	}
	public function woo_bundles_frontend_scripts() {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::frontend_scripts()' );
		return $this->frontend_scripts();
	}
	public function woo_bundles_cart_contents_count( $count ) {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::cart_contents_count()' );
		return $this->cart_contents_count( $count );
	}
	public function woo_bundles_add_cart_widget_filters() {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::add_cart_widget_filters()' );
		return $this->add_cart_widget_filters();
	}
	public function woo_bundles_remove_cart_widget_filters() {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::remove_cart_widget_filters()' );
		return $this->remove_cart_widget_filters();
	}
	public function woo_bundles_order_item_visible( $visible, $order_item ) {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::order_item_visible()' );
		return $this->order_item_visible( $visible, $order_item );
	}
	public function woo_bundles_cart_item_visible( $visible, $cart_item, $cart_item_key ) {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::cart_item_visible()' );
		return $this->cart_item_visible( $visible, $cart_item, $cart_item_key );
	}
	public function woo_bundles_email_styles( $css ) {
		_deprecated_function( __METHOD__ . '()', '5.0.0', __CLASS__ . '::email_styles()' );
		return $this->email_styles( $css );
	}
}

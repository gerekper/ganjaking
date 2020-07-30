<?php
/**
 * Front-End Display
 *
 * @author   Kathy Darling
 * @category Classes
 * @package  WooCommerce Mix and Match Products/Display
 * @since    1.0.0
 * @version  1.9.3
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

		// Add preamble info to child products.
		add_filter( 'woocommerce_cart_item_name', array( $this, 'in_cart_item_title' ), 10, 3 );
		add_filter( 'woocommerce_order_item_name', array( $this, 'order_table_item_title' ), 10, 2 );

		// Hide Container size meta in my-account.
		add_filter( 'woocommerce_order_items_meta_get_formatted', array( $this, 'order_item_meta' ), 10, 2 );

		// Change the tr class attributes when displaying child items in templates.
		add_filter( 'woocommerce_cart_item_class', array( $this, 'cart_item_class' ), 10, 3 );
		add_filter( 'woocommerce_order_item_class', array( $this, 'order_item_class' ), 10, 3 );

		// Front end scripts- validation + price updates.
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

		// QuickView support.
		add_action( 'wc_quick_view_enqueue_scripts', array( $this, 'quickview_support' ) );

		// Indent items in emails.
		add_action( 'woocommerce_email_styles', array( $this, 'email_styles' ) );

		// Stop displaying the "Part of" meta key in Order Tables, ex: My Account and Emails
		add_filter( 'woocommerce_order_item_get_formatted_meta_data', array( $this, 'remove_part_of_meta_key_from_display' ), 10, 2 );

		// Display label for mnm_container_size order item meta.
		add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'order_item_meta_label' ), 10, 3 );

	}

	/*-----------------------------------------------------------------------------------*/
	/*  Single Product Display                                                           */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Add-to-cart template for Mix and Match products.
	 * @return void
	 */
	public function add_to_cart_template() {

		wc_deprecated_function( 'WC_Mix_and_Match_Display::add_to_cart_template()', '1.3.0', 'wc_mnm_template_add_to_cart' );

		global $product;

		// Enqueue scripts and styles - then, initialize js variables.
		wp_enqueue_script( 'wc-add-to-cart-mnm' );
		wp_enqueue_style( 'wc-mnm-frontend' );

		// Load the add to cart template.
		wc_get_template(
			'single-product/add-to-cart/mnm.php',
			array(
				'container'	      => $product,
				'min_container_size'  => $product->get_min_container_size(),
				'max_container_size'  => $product->get_max_container_size(),
				'mnm_products'    => $product->get_available_children(),
			),
			'',
			WC_Mix_and_Match()->plugin_path() . '/templates/'
		);

	}


	/*-----------------------------------------------------------------------------------*/
	/*  Cart and Order Display                                                           */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Adds title preambles to cart items.
	 *
	 * @param  string   $content
	 * @param  array    $cart_item
	 * @param  string   $cart_item_key
	 * @return string
	 */
	public function in_cart_item_title( $content, $cart_item, $cart_item_key ) {

		if ( wc_mnm_maybe_is_child_order_item( $cart_item ) ) {
			$this->enqueue_table_item_js();
		}

		if( wc_mnm_is_container_cart_item( $cart_item ) ) {

			$container = $cart_item[ 'data' ];

			if ( function_exists( 'is_cart' ) && is_cart() && ! $this->is_cart_widget() ) {

				$edit_in_cart_link = esc_url( add_query_arg( array( 'update-container' => $cart_item_key ), $container->get_permalink( $cart_item ) ) );
				$edit_in_cart_text = _x( 'Edit', 'edit in cart link text', 'woocommerce-mix-and-match-products' );
				// translators: %1$d is the original product name. %2$s is the edit link url. %3$s is the 'Edit' string.
				$content           = sprintf( _x( '%1$s<br/><a class="edit_container_in_cart_text edit_in_cart_text" href="%2$s"><small>%3$s</small></a>', 'edit in cart text', 'woocommerce-mix-and-match-products' ), $content, $edit_in_cart_link, $edit_in_cart_text );
			
			}

		}

		return $content;

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
	 * Adds child item title preambles to order-details template.
	 *
	 * @param  string 	$content
	 * @param  object 	$order_item
	 * @return string
	 */
	public function order_table_item_title( $content, $order_item ) {

		if ( ! empty( $order_item[ 'mnm_container' ] ) ) {
			if ( did_action( 'woocommerce_view_order' ) || did_action( 'woocommerce_thankyou' ) || did_action( 'before_woocommerce_pay' ) || did_action( 'woocommerce_account_view-subscription_endpoint' ) ) {
				$this->enqueue_table_item_js();
			} else {
				// E-mails.
				return '<small>' . $content . '</small>';
			}
		}

		return $content;
	}


	/**
	 * Enqeue js that wraps child table items in a div in order to apply indentation reliably.
	 *
	 * @since 1.0.2
	 */
	private function enqueue_table_item_js() {

		if ( ! $this->enqueued_table_item_js ) {
			wc_enqueue_js( "
				var wc_mnm_wrap_mnm_table_item = function() {
					jQuery( '.mnm_table_item td.product-name' ).wrapInner( '<div class=\"mnm_table_item_indent\"></div>' );
				}

				jQuery( 'body' ).on( 'updated_checkout updated_wc_div', function() {
					wc_mnm_wrap_mnm_table_item();
				} );

				wc_mnm_wrap_mnm_table_item();
			" );

			$this->enqueued_table_item_js = true;
		}
	}

	/**
	 * Hide the "Container size" meta in the my-account area.
	 *
	 * @param  array  $formatted_meta
	 * @param  obj    $order
	 * @return array
	 */
	public function order_item_meta( $formatted_meta, $order ){
		foreach( $formatted_meta as $id => $meta ){
			if ( $meta['key'] ==  __( 'Container size', 'woocommerce-mix-and-match-products' ) ){
				unset( $formatted_meta[$id] );
			}
		}
		return $formatted_meta;
	}


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

			if( $cart_item['data']->is_priced_per_product() ) {
				$class .= ' mnm_is_priced_per_product_container';
			} else {
				$class .= ' mnm_is_static_priced_container';
			}
			// Child Item.
		} else if ( $container = wc_mnm_get_cart_item_container( $cart_item ) ) {
			$class .= ' mnm_table_item';

			if( $container['data']->is_priced_per_product() ) {
				$class .= ' mnm_part_of_priced_per_product_container';
			} else {
				$class .= ' mnm_part_of_static_priced_container';
			}
		}

		return $class;
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

				if( wc_mnm_maybe_is_child_order_item( $child_item ) ) {
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

	/*-----------------------------------------------------------------------------------*/
	/*  Scripts and Styles                                                               */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Load scripts.
	 */
	public function frontend_scripts() {

		wp_register_style( 'wc-mnm-frontend', WC_Mix_and_Match()->plugin_url() . '/assets/css/frontend/mnm-frontend.css', array(), WC_Mix_and_Match()->version );
		wp_enqueue_style( 'wc-mnm-frontend' );

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_register_script( 'wc-add-to-cart-mnm', WC_Mix_and_Match()->plugin_url() . '/assets/js/frontend/add-to-cart-mnm' . $suffix . '.js', array( 'jquery', 'jquery-blockui' ), WC_Mix_and_Match()->version, true );

		/**
		 * Trim Zeros setting.
		 *
		 * @param  array $params
		 */	
		$trim_zeros = apply_filters( 'woocommerce_price_trim_zeros', false );

		/**
		 * Javascript strings.
		 *
		 * @param  array $params
		 */
		$params = $this->get_add_to_cart_parameters( $trim_zeros );

		wp_localize_script( 'wc-add-to-cart-mnm', 'wc_mnm_params', $params );

	}

	/**
	 * Returns Add to Cart Parameters.
	 *
	 * @access public
	 * @static
	 * @param  bool $trim_zeros
	 * @return array
	 */
	public static function get_add_to_cart_parameters( $trim_zeros ) {
		return apply_filters( 'woocommerce_mnm_add_to_cart_parameters', array(
			'addons_three_support'               => defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0', '>=' ) ? 'yes' : 'no',
			'i18n_total'                         => __( 'Total: ', 'woocommerce-mix-and-match-products' ),
			'i18n_subtotal'                      => __( 'Subtotal: ', 'woocommerce-mix-and-match-products' ),
			'i18n_addon_total'                   => __( 'Options total:', 'woocommerce-mix-and-match-products' ),
			'i18n_addons_total'                  => __( 'Grand total: ', 'woocommerce-mix-and-match-products' ),
			// translators: Placeholders "Total/Subtotal" string followed by price followed by price suffix. %1$d is %t placeholder for subtotal. %2$d is %p placeholder for price. %3$d is %s placeholder for %s price suffix. 
			'i18n_price_format'                  => sprintf( _x( '%1$s%2$s%3$s', '"Total/Subtotal" string followed by price followed by price suffix', 'woocommerce-mix-and-match-products' ), '%t', '%p', '%s' ),
			// translators: %1$d is 
			'i18n_strikeout_price_string'        => sprintf( _x( '<del>%1$s</del> <ins>%2$s</ins>', 'Sale/strikeout price', 'woocommerce-mix-and-match-products' ), '%f', '%t' ),
			'i18n_free'                          => __( 'Free!', 'woocommerce-mix-and-match-products' ),
			// translators: %s is current selected quantity 
			'i18n_qty_message'                   => __( 'You have selected %s items. ', 'woocommerce-mix-and-match-products' ),
			// translators: %s is current selected quantity 
			'i18n_qty_message_single'            => __( 'You have selected %s item. ', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the error message. %s is quantity left to be selected.  
			'i18n_qty_error'                     => __( '%vPlease select %s items to continue&hellip;', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the error message. %s is quantity left to be selected. 
			'i18n_qty_error_single'              => __( '%vPlease select %s item to continue&hellip;', 'woocommerce-mix-and-match-products' ),
			'i18n_empty_error'   		         => __( 'Please select at least 1 item to continue&hellip;', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the error message. %min is the script placeholder for min quantity. %max is script placeholder for max quantity.  
			'i18n_min_max_qty_error'             => __( '%vPlease choose between %min and %max items to continue&hellip;', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the error message. %min is the script placeholder for min quantity. %max is script placeholder for max quantity.  
			'i18n_min_qty_error_singular'        => __( '%vPlease choose at least %min item to continue&hellip;', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the error message. %min is the script placeholder for min quantity. %max is script placeholder for max quantity.  
			'i18n_min_qty_error'                 => __( '%vPlease choose at least %min items to continue&hellip;', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the error message. %min is the script placeholder for min quantity. %max is script placeholder for max quantity.  
			'i18n_max_qty_error_singular'        => __( '%vPlease choose fewer than %max item to continue&hellip;', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the error message. %min is the script placeholder for min quantity. %max is script placeholder for max quantity.  
			'i18n_max_qty_error'                 => __( '%vPlease choose fewer than %max items to continue&hellip;', 'woocommerce-mix-and-match-products' ),
			// translators: %v is the error message. %min is the script placeholder for min quantity. %max is script placeholder for max quantity.  
			'i18n_validation_alert'              => __( 'Please resolve all pending configuration issues before adding this product to your cart.', 'woocommerce-mix-and-match-products' ),
			'currency_symbol'                    => get_woocommerce_currency_symbol(),
			'currency_position'                  => esc_attr( stripslashes( get_option( 'woocommerce_currency_pos' ) ) ),
			'currency_format_num_decimals'       => absint( wc_get_price_decimals() ),
			'currency_format_precision_decimals' => absint( wc_get_rounding_precision() ),
			'currency_format_decimal_sep'        => esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
			'currency_format_thousand_sep'       => esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
			'currency_format_trim_zeros'         => false == $trim_zeros ? 'no' : 'yes',
			'price_display_suffix'               => esc_attr( get_option( 'woocommerce_price_display_suffix' ) ),
			'prices_include_tax'                 => esc_attr( get_option( 'woocommerce_prices_include_tax' ) ),
			'tax_display_shop'                   => esc_attr( get_option( 'woocommerce_tax_display_shop' ) ),
			'calc_taxes'                         => esc_attr( get_option( 'woocommerce_calc_taxes' ) ),
			'photoswipe_enabled'                 => current_theme_supports( 'wc-product-gallery-lightbox' ) ? 'yes' : 'no',
		) );
	}

	/**
	 * QuickView scripts init.
	 */
	public function quickview_support() {

		if ( ! is_product() ) {
			$this->frontend_scripts();
			wp_enqueue_script( 'wc-add-to-cart-mnm' );
			wp_enqueue_style( 'wc-mnm-styles' );
		}
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
		$css = $css . ".mnm_table_item td:nth-child(1) { padding-left: 2.5em !important; } .mnm_table_item td { border-top: none; font-size: 0.875em; } #body_content table tr.mnm_table_item td ul.wc-item-meta { font-size: inherit; }";
		return $css;
	}


	/*-----------------------------------------------------------------------------------*/
	/* Order tables */
	/*-----------------------------------------------------------------------------------*/

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
		if( ! apply_filters( 'woocommerce_mnm_order_item_legacy_part_of_meta', false, $order_item ) ) {
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
			case 'mnm_container_size':
				$display_key = __( 'Container size', 'woocommerce-mix-and-match-products' );
				break;
			case 'mnm_part_of':
				$display_key = __( 'Part of', 'woocommerce-mix-and-match-products' );
				break;
			case 'mnm_purchased_with':
				$display_key = __( 'Purchased with', 'woocommerce-mix-and-match-products' );
				break;
		}
		return $display_key;
	}

} // End class.

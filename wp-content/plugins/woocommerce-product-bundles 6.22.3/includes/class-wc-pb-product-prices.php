<?php
/**
 * WC_PB_Product_Prices class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Price functions and hooks.
 *
 * @class    WC_PB_Product_Prices
 * @version  6.17.4
 */
class WC_PB_Product_Prices {

	/**
	 * Flag indicating whether 'filter_get_price_cart' is applied on a variable product price.
	 *
	 * @var boolean
	 */
	private static $filtering_variable_price_html = false;

	/**
	 * Flag indicating whether 'get_extended_price_precision' is being used to filter WC decimals.
	 *
	 * @var boolean
	 */
	private static $filtering_price_decimals = false;

	/**
	 * Bundled items whose prices are currently being filtered -- all states.
	 *
	 * @var WC_Bundled_Item
	 */
	private static $bundled_item_pre;

	/**
	 * Bundled item whose prices are currently being filtered.
	 *
	 * @var WC_Bundled_Item
	 */
	public static $bundled_item;

	/**
	 * Initialize.
	 */
	public static function init() {

		// Always-on price filters used in cart context.
		if ( 'filters' === self::get_bundled_cart_item_discount_method() ) {

			add_filter( 'woocommerce_product_get_price', array( __CLASS__, 'filter_get_price_cart' ), 98, 2 );
			add_filter( 'woocommerce_product_get_sale_price', array( __CLASS__, 'filter_get_sale_price_cart' ), 98, 2 );
			add_filter( 'woocommerce_product_get_regular_price', array( __CLASS__, 'filter_get_regular_price_cart' ), 98, 2 );

			add_filter( 'woocommerce_product_variation_get_price', array( __CLASS__, 'filter_get_price_cart' ), 98, 2 );
			add_filter( 'woocommerce_product_variation_get_sale_price', array( __CLASS__, 'filter_get_sale_price_cart' ), 98, 2 );
			add_filter( 'woocommerce_product_variation_get_regular_price', array( __CLASS__, 'filter_get_regular_price_cart' ), 98, 2 );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Class methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * A non-strict way to tell if a product's prices are being altered due to the presence of a parent "bundle".
	 *
	 * @since  6.0.0
	 *
	 * @param  WC_Product  $product
	 * @param  string      $context
	 * @return boolean
	 */
	public static function is_bundled_pricing_context( $product, $context = 'any' ) {

		$is_bundled_pricing_context = false;

		if ( in_array( $context, array( 'any', 'catalog' ) ) ) {
			$is_bundled_pricing_context =  self::$bundled_item && in_array( self::$bundled_item->get_product_id(), array( $product->get_id(), $product->get_parent_id() ) );
		}

		if ( 'cart' === $context || ( ! $is_bundled_pricing_context && 'any' === $context) ){
			$is_bundled_pricing_context =  isset( $product->bundled_cart_item );
		}

		return $is_bundled_pricing_context;
	}

	/**
	 * A non-strict way to get the bundled_item, depending on if a product's prices are being altered due to the presence of a parent "bundle".
	 *
	 * @since  6.12.0
	 *
	 * @param  WC_Product  $product
	 * @param  string      $context
	 * @return WC_Bundled_Item|boolean
	 */
	public static function get_filtered_bundled_item( $product, $context = 'any' ) {

		$bundled_item = false;

		if ( in_array( $context, array( 'any', 'catalog' ), true ) ) {
			if ( self::$bundled_item && in_array( self::$bundled_item->get_product_id(), array( $product->get_id(), $product->get_parent_id() ) ) ) {
				$bundled_item = self::$bundled_item;
			}
		}

		if ( 'cart' === $context || ( ! $bundled_item && 'any' === $context ) ) {
			if ( isset( $product->bundled_cart_item ) ) {
				$bundled_item = $product->bundled_cart_item;
			}
		}

		return $bundled_item;
	}

	/**
	 * Method to use for calculating cart item discounts. Values: 'filters' | 'props'
	 *
	 * @since  6.0.0
	 *
	 * @return string  $method
	 */
	public static function get_bundled_cart_item_discount_method() {
		/**
		 * 'woocommerce_bundled_cart_item_discount_method' filter.
		 *
		 * @since  6.0.0
		 *
		 * @param  string  $method  Method to use for calculating cart item discounts. Values: 'filters' | 'props'.
		 */
		$discount_method = apply_filters( 'woocommerce_bundled_cart_item_discount_method', 'filters' );
		return in_array( $discount_method, array( 'filters', 'props' ) ) ? $discount_method : 'filters';
	}

	/**
	 * Returns the incl/excl tax coefficients for calculating prices incl/excl tax on the client side.
	 *
	 * @since  5.7.6
	 *
	 * @param  WC_Product  $product
	 * @return array
	 */
	public static function get_tax_ratios( $product ) {

		WC_PB_Product_Prices::extend_price_display_precision();

		$ref_price      = 1000.0;
		$ref_price_incl = wc_get_price_including_tax( $product, array( 'qty' => 1, 'price' => $ref_price ) );
		$ref_price_excl = wc_get_price_excluding_tax( $product, array( 'qty' => 1, 'price' => $ref_price ) );

		WC_PB_Product_Prices::reset_price_display_precision();

		return array(
			'incl' => $ref_price_incl / $ref_price,
			'excl' => $ref_price_excl / $ref_price
		);
	}

	/**
	 * Filters the 'woocommerce_price_num_decimals' option to use the internal WC rounding precision.
	 */
	public static function extend_price_display_precision() {
		add_filter( 'wc_get_price_decimals', array( __CLASS__, 'get_extended_price_precision' ) );
	}

	/**
	 * Reset applied filters to the 'woocommerce_price_num_decimals' option.
	 */
	public static function reset_price_display_precision() {
		remove_filter( 'wc_get_price_decimals', array( __CLASS__, 'get_extended_price_precision' ) );
	}

	/**
	 * Calculates product prices.
	 *
	 * @since  5.5.0
	 *
	 * @param  WC_Product  $product
	 * @param  array       $args
	 * @return mixed
	 */
	public static function get_product_price( $product, $args ) {

		$defaults = array(
			'price' => '',
			'qty'   => 1,
			'calc'  => ''
		);

		$args  = wp_parse_args( $args, $defaults );
		$price = $args[ 'price' ];
		$qty   = $args[ 'qty' ];
		$calc  = $args[ 'calc' ];

		if ( $price ) {

			if ( 'display' === $calc ) {
				$calc = 'excl' === get_option( 'woocommerce_tax_display_shop' ) ? 'excl_tax' : 'incl_tax';
			}

			if ( 'incl_tax' === $calc ) {
				$price = wc_get_price_including_tax( $product, array( 'qty' => $qty, 'price' => $price ) );
			} elseif ( 'excl_tax' === $calc ) {
				$price = wc_get_price_excluding_tax( $product, array( 'qty' => $qty, 'price' => $price ) );
			} else {
				$price = $price * $qty;
			}
		}

		return $price;
	}

	/**
	 * Get extended rounding precision.
	 *
	 * @since  6.7.7
	 *
	 * @param  int  $decimals
	 * @return int
	 */
	public static function get_extended_price_precision( $decimals = null ) {

		// Prevent infinite loops through 'wc_pb_price_num_decimals'.
		if ( ! is_null( $decimals ) && self::$filtering_price_decimals ) {
			return $decimals;
		}

		self::$filtering_price_decimals = true;
		$decimals = wc_pb_price_num_decimals( 'extended' );
		self::$filtering_price_decimals = false;

		return $decimals;
	}

	/**
	 * Discounted bundled item price precision. Defaults to the price display precision, a.k.a. wc_get_price_decimals.
	 *
	 * @since  5.7.8
	 *
	 * @return int
	 */
	public static function get_discounted_price_precision() {
		return wc_pb_price_num_decimals( 'extended' );
	}

	/**
	 * Discounted price getter.
	 *
	 * @param  mixed  $price
	 * @param  mixed  $discount
	 * @return mixed
	 */
	public static function get_discounted_price( $price, $discount ) {

		$discounted_price = $price;

		if ( ! empty( $price ) && ! empty( $discount ) ) {
			$discounted_price = round( ( double ) $price * ( 100 - $discount ) / 100, self::get_discounted_price_precision() );
		}

		return $discounted_price;
	}

	/**
	 * Returns the recurring price component of a subscription product.
	 *
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function get_recurring_price_html_component( $product ) {

		$sync_date = $product->get_meta( '_subscription_payment_sync_date', true );

		$product->update_meta_data( '_subscription_payment_sync_date', 0 );

		$sub_price_html = WC_Subscriptions_Product::get_price_string( $product, array( 'price' => '%s', 'sign_up_fee' => false ) );

		$product->update_meta_data( '_subscription_payment_sync_date', $sync_date );

		return $sub_price_html;
	}

	/**
	 * Add price filters to modify child product prices depending on the bundled item pricing setup.
	 *
	 * @param  WC_Bundled_Item  $bundled_item
	 */
	public static function add_price_filters( $bundled_item ) {

		$add_filters = false;

		if ( empty( self::$bundled_item_pre ) ) {
			self::$bundled_item_pre = array();
			$add_filters            = true;
		}

		self::$bundled_item_pre[] = $bundled_item;
		self::$bundled_item       = $bundled_item;

		if ( $add_filters ) {

			add_filter( 'woocommerce_product_get_price', array( __CLASS__, 'filter_get_price' ), 15, 2 );
			add_filter( 'woocommerce_product_get_sale_price', array( __CLASS__, 'filter_get_sale_price' ), 15, 2 );
			add_filter( 'woocommerce_product_get_regular_price', array( __CLASS__, 'filter_get_regular_price' ), 15, 2 );
			add_filter( 'woocommerce_product_variation_get_price', array( __CLASS__, 'filter_get_price' ), 15, 2 );
			add_filter( 'woocommerce_product_variation_get_sale_price', array( __CLASS__, 'filter_get_sale_price' ), 15, 2 );
			add_filter( 'woocommerce_product_variation_get_regular_price', array( __CLASS__, 'filter_get_regular_price' ), 15, 2 );

			add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'filter_get_price_html' ), 10, 2 );
			add_filter( 'woocommerce_get_children', array( __CLASS__, 'filter_children' ), 10, 2 );
			add_filter( 'woocommerce_variable_price_html', array( __CLASS__, 'filter_variable_price_html' ), 10, 2 );
			add_filter( 'woocommerce_variation_prices', array( __CLASS__, 'filter_get_variation_prices' ), 15, 2 );
			add_filter( 'woocommerce_show_variation_price', array( __CLASS__, 'filter_show_variation_price' ), 10, 3 );
			add_filter( 'woocommerce_get_variation_prices_hash', array( __CLASS__, 'filter_variation_prices_hash' ), 10, 2 );

			add_filter( 'woocommerce_product_is_on_sale', array( __CLASS__, 'filter_is_on_sale' ), 99, 2 );

			/**
			 * 'woocommerce_bundled_product_price_filters_added' hook.
			 *
			 * @param  WC_Bundled_Item  $bundled_item
			 */
			do_action( 'woocommerce_bundled_product_price_filters_added', $bundled_item );
		}
	}

	/**
	 * Remove price filters after modifying child product prices depending on the bundled item pricing setup.
	 */
	public static function remove_price_filters() {

		$bundled_item = self::$bundled_item;

		array_pop( self::$bundled_item_pre );

		self::$bundled_item = ! empty( self::$bundled_item_pre ) && is_array( self::$bundled_item_pre ) ? end( self::$bundled_item_pre ) : null;

		if ( $bundled_item && empty( self::$bundled_item ) ) {

			remove_filter( 'woocommerce_product_get_price', array( __CLASS__, 'filter_get_price' ), 15, 2 );
			remove_filter( 'woocommerce_product_get_sale_price', array( __CLASS__, 'filter_get_sale_price' ), 15, 2 );
			remove_filter( 'woocommerce_product_get_regular_price', array( __CLASS__, 'filter_get_regular_price' ), 15, 2 );
			remove_filter( 'woocommerce_product_variation_get_price', array( __CLASS__, 'filter_get_price' ), 15, 2 );
			remove_filter( 'woocommerce_product_variation_get_sale_price', array( __CLASS__, 'filter_get_sale_price' ), 15, 2 );
			remove_filter( 'woocommerce_product_variation_get_regular_price', array( __CLASS__, 'filter_get_regular_price' ), 15, 2 );

			remove_filter( 'woocommerce_get_price_html', array( __CLASS__, 'filter_get_price_html' ), 10, 2 );
			remove_filter( 'woocommerce_get_children', array( __CLASS__, 'filter_children' ), 10, 2 );
			remove_filter( 'woocommerce_variable_price_html', array( __CLASS__, 'filter_variable_price_html' ), 10, 2 );
			remove_filter( 'woocommerce_variation_prices', array( __CLASS__, 'filter_get_variation_prices' ), 15, 2 );
			remove_filter( 'woocommerce_show_variation_price', array( __CLASS__, 'filter_show_variation_price' ), 10, 3 );
			remove_filter( 'woocommerce_get_variation_prices_hash', array( __CLASS__, 'filter_variation_prices_hash' ), 10, 2 );

			remove_filter( 'woocommerce_product_is_on_sale', array( __CLASS__, 'filter_is_on_sale' ), 99, 2 );

			/**
			 * 'woocommerce_bundled_product_price_filters_removed' hook.
			 *
			 * @param  WC_Bundled_Item  $bundled_item
			 */
			do_action( 'woocommerce_bundled_product_price_filters_removed', $bundled_item );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Callbacks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Filter variation prices hash to load different prices for variable products with variation filters and/or discounts.
	 *
	 * @param  array                $hash
	 * @param  WC_Product_Variable  $product
	 * @return array
	 */
	public static function filter_variation_prices_hash( $hash, $product ) {

		$bundled_item = self::get_filtered_bundled_item( $product );

		if ( $bundled_item ) {

			$discount                = $bundled_item->get_discount();
			$has_filtered_variations = $product->is_type( 'variable' ) && $bundled_item->has_filtered_variations();

			if ( $has_filtered_variations || ! empty( $discount ) ) {
				$hash[] = $bundled_item->data->get_id();
			}
		}

		return $hash;
	}

	/**
	 * Filter variable product children to exclude filtered out variations.
	 *
	 * @param  array                $children
	 * @param  WC_Product_Variable  $product
	 * @return array
	 */
	public static function filter_children( $children, $product ) {

		$bundled_item = self::get_filtered_bundled_item( $product );

		if ( $bundled_item ) {

			if ( $bundled_item->has_filtered_variations() ) {

				$filtered_children = array();

				foreach ( $children as $variation_id ) {
					// Remove if filtered.
					if ( in_array( $variation_id, $bundled_item->get_filtered_variations() ) ) {
						$filtered_children[] = $variation_id;
					}
				}

				$children = $filtered_children;
			}
		}

		return $children;
	}

	/**
	 * Filter get_variation_prices() calls for bundled products to include discounts.
	 *
	 * @param  array                $prices_array
	 * @param  WC_Product_Variable  $product
	 * @return array
	 */
	public static function filter_get_variation_prices( $prices_array, $product ) {

		$bundled_item = self::get_filtered_bundled_item( $product );

		if ( $bundled_item ) {

			$prices         = array();
			$regular_prices = array();
			$sale_prices    = array();

			$discount           = $bundled_item->get_discount();
			$priced_per_product = $bundled_item->is_priced_individually();

			// Filter regular prices.
			foreach ( $prices_array[ 'regular_price' ] as $variation_id => $regular_price ) {
				if ( $priced_per_product ) {
					$regular_prices[ $variation_id ] = $regular_price === '' ? $prices_array[ 'price' ][ $variation_id ] : $regular_price;
				} else {
					$regular_prices[ $variation_id ] = 0;
				}
			}

			// Filter prices.
			foreach ( $prices_array[ 'price' ] as $variation_id => $price ) {
				if ( $priced_per_product ) {
					if ( false === $bundled_item->is_discount_allowed_on_sale_price() ) {
						$regular_price = $regular_prices[ $variation_id ];
					} else {
						$regular_price = $price;
					}
					$price                   = empty( $discount ) ? $price : round( ( double ) $regular_price * ( 100 - $discount ) / 100, self::get_discounted_price_precision() );
					$prices[ $variation_id ] = apply_filters( 'woocommerce_bundled_variation_price', $price, $variation_id, $discount, $bundled_item );
				} else {
					$prices[ $variation_id ] = 0;
				}
			}

			// Filter sale prices.
			foreach ( $prices_array[ 'sale_price' ] as $variation_id => $sale_price ) {
				if ( $priced_per_product ) {
					$sale_prices[ $variation_id ] = empty( $discount ) ? $sale_price : $prices[ $variation_id ];
				} else {
					$sale_prices[ $variation_id ] = 0;
				}
			}

			if ( $priced_per_product && ! empty( $discount ) && false === $bundled_item->is_discount_allowed_on_sale_price() ) {
				asort( $regular_prices );
				asort( $prices );
				asort( $sale_prices );
			}

			$prices_array = array(
				'price'         => $prices,
				'regular_price' => $regular_prices,
				'sale_price'    => $sale_prices
			);
		}

		return $prices_array;
	}

	/**
	 * Filter condition that allows WC to calculate variation price_html.
	 *
	 * @param  boolean               $show
	 * @param  WC_Product_Variable   $product
	 * @param  WC_Product_Variation  $variation
	 * @return boolean
	 */
	public static function filter_show_variation_price( $show, $product, $variation ) {

		$bundled_item = self::get_filtered_bundled_item( $variation );

		if ( $bundled_item ) {

			$prices_equal = ! $show;
			$show         = false;

			if ( $bundled_item->is_priced_individually() && $bundled_item->is_price_visible( 'product' ) ) {
				$show = true;
				// If the product is optional and all prices are equal, then the prices is already displayed in "Add for $XXX".
				if ( $bundled_item->is_optional() && $prices_equal ) {
					$show = false;
				}
			}
		}

		return $show;
	}

	/**
	 * Filter get_price() calls for bundled products to include discounts.
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @param  string      $context
	 * @return double
	 */
	public static function filter_get_price( $price, $product, $context = 'any' ) {

		$bundled_item = self::get_filtered_bundled_item( $product, $context );

		if ( $bundled_item && ( $bundled_item instanceof WC_Bundled_Item ) ) {

			if ( $price === '' ) {
				return $price;
			}

			if ( ! $bundled_item->is_priced_individually() ) {
				return 0;
			}

			if ( $discount = $bundled_item->get_discount( $context ) ) {

				$offset_price     = ! empty( $product->bundled_price_offset ) ? $product->bundled_price_offset : false;
				$offset_price_pct = ! empty( $product->bundled_price_offset_pct ) && is_array( $product->bundled_price_offset_pct ) ? $product->bundled_price_offset_pct : false;

				if ( false === $bundled_item->is_discount_allowed_on_sale_price() ) {
					do_action( 'woocommerce_bundled_item_get_unfiltered_regular_price_start' );
					$regular_price = $product->get_regular_price();
					do_action( 'woocommerce_bundled_item_get_unfiltered_regular_price_end' );
				} else {
					$regular_price = $price;
				}

				$price = self::get_discounted_price( $regular_price, $discount );

				// Add-on % prices.
				if ( $offset_price_pct ) {

					if ( ! $offset_price ) {
						$offset_price = 0.0;
					}

					foreach ( $offset_price_pct as $price_pct ) {
						$offset_price += $price * $price_pct / 100;
					}
				}

				// Add-on prices.
				if ( $offset_price ) {
					$price += $offset_price;
				}
			}

			$product->bundled_item_price = $price;

			/** Documented in 'WC_Bundled_Item::get_raw_price()'. */
			$price = apply_filters( 'woocommerce_bundled_item_price', $price, $product, $discount, $bundled_item );
		}

		return $price;
	}

	/**
	 * Filter get_regular_price() calls for bundled products to include discounts.
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @param  string      $context
	 * @return double
	 */
	public static function filter_get_regular_price( $regular_price, $product, $context = 'any' ) {

		$bundled_item = self::get_filtered_bundled_item( $product, $context );

		if ( $bundled_item && ( $bundled_item instanceof WC_Bundled_Item ) ) {

			if ( ! $bundled_item->is_priced_individually() ) {
				return 0;
			}
		}

		return $regular_price;
	}

	/**
	 * Filter get_sale_price() calls for bundled products to include discounts.
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @param  string      $context
	 * @return double
	 */
	public static function filter_get_sale_price( $sale_price, $product, $context = 'any' ) {

		$bundled_item = self::get_filtered_bundled_item( $product, $context );

		if ( $bundled_item && ( $bundled_item instanceof WC_Bundled_Item ) ) {

			if ( ! $bundled_item->is_priced_individually() ) {
				return 0;
			}

			if ( $discount = $bundled_item->get_discount( $context ) ) {

				$offset_price     = ! empty( $product->bundled_price_offset ) ? $product->bundled_price_offset : false;
				$offset_price_pct = ! empty( $product->bundled_price_offset_pct ) && is_array( $product->bundled_price_offset_pct ) ? $product->bundled_price_offset_pct : false;

				if ( '' === $sale_price || false === $bundled_item->is_discount_allowed_on_sale_price() ) {
					$regular_price = $product->get_regular_price();
				} else {
					$regular_price = $sale_price;
				}

				$sale_price = self::get_discounted_price( $regular_price, $discount );

				// Add-on % prices.
				if ( $offset_price_pct ) {

					if ( ! $offset_price ) {
						$offset_price = 0.0;
					}

					foreach ( $offset_price_pct as $price_pct ) {
						$offset_price += $sale_price * $price_pct / 100;
					}
				}

				// Add-on prices.
				if ( $offset_price ) {
					$sale_price += $offset_price;
				}
			}

			/** Documented in 'WC_Bundled_Item::get_raw_price()'. */
			$sale_price = apply_filters( 'woocommerce_bundled_item_price', $sale_price, $product, $discount, $bundled_item );
		}

		return $sale_price;
	}

	/**
	 * Filter get_price() calls for bundled cart items to include discounts.
	 *
	 * @since  6.0.0
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @return double
	 */
	public static function filter_get_price_cart( $price, $product ) {
		return self::is_bundled_pricing_context( $product, 'cart' ) ? self::filter_get_price( $price, $product, 'cart' ) : $price;
	}

	/**
	 * Filter get_sale_price() calls for bundled cart items to include discounts.
	 *
	 * @since  6.0.0
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @return double
	 */
	public static function filter_get_sale_price_cart( $price, $product ) {
		return self::is_bundled_pricing_context( $product, 'cart' ) ? self::filter_get_sale_price( $price, $product, 'cart' ) : $price;
	}

	/**
	 * Filter get_regular_price() calls for bundled cart items.
	 *
	 * @since  6.1.4
	 *
	 * @param  double      $price
	 * @param  WC_Product  $product
	 * @return double
	 */
	public static function filter_get_regular_price_cart( $price, $product ) {
		return self::is_bundled_pricing_context( $product, 'cart' ) ? self::filter_get_regular_price( $price, $product, 'cart' ) : $price;
	}

	/**
	 * Wrapper of 'filter_get_price_html' for variable products.
	 *
	 * @param  string      $price_html
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function filter_variable_price_html( $price_html, $product ) {

		self::$filtering_variable_price_html = true;
		$price_html = self::filter_get_price_html( $price_html, $product );
		self::$filtering_variable_price_html = false;

		return $price_html;
	}

	/**
	 * Filter the html price string of bundled items to show the correct price with discount and tax - needs to be hidden when the bundled item is priced individually.
	 *
	 * @param  string      $price_html
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function filter_get_price_html( $price_html, $product ) {

		if ( $product->is_type( 'variable' ) && false === self::$filtering_variable_price_html ) {
			return $price_html;
		}

		$bundled_item = self::get_filtered_bundled_item( $product );

		if ( $bundled_item ) {

			if ( ! $bundled_item->is_priced_individually() ) {
				return '';
			}

			if ( ! $bundled_item->is_price_visible( 'product' ) ) {
				return '';
			}

			$quantity = $bundled_item->get_quantity( 'max' );

			/**
			 * 'woocommerce_bundled_item_price_html' filter.
			 *
			 * @param  string           $price_html
			 * @param  WC_Bundled_Item  $bundled_item
			 */

			/* translators: %1$s: Product price, %2$s: Product quantity */
			$price_html = apply_filters( 'woocommerce_bundled_item_price_html', '' === $quantity || $quantity > 1 ? sprintf( __( '%1$s <span class="bundled_item_price_quantity">each</span>', 'woocommerce-product-bundles' ), $price_html, $quantity ) : $price_html, $price_html, $bundled_item );
		}

		return $price_html;
	}

	/**
	 * Filter WC_Product::is_on_sale() calls.
	 *
	 * @since  6.12.0
	 *
	 * @param  bool        $is_on_sale
	 * @param  WC_Product  $product
	 * @return bool
	 */
	public static function filter_is_on_sale( $is_on_sale, $product ) {

		$bundled_item = self::get_filtered_bundled_item( $product );

		if ( $bundled_item ) {

			if ( ! $bundled_item->is_priced_individually() ) {
				return $is_on_sale;
			}

			if ( ! empty( $bundled_item->get_discount() ) ) {
				$is_on_sale = true;
			}
		}
		return $is_on_sale;
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Calculates bundled product prices incl. or excl. tax depending on the 'woocommerce_tax_display_shop' setting.
	 *
	 * @deprecated  5.5.0
	 */
	public static function get_product_display_price( $product, $price, $qty = 1 ) {
		_deprecated_function( __METHOD__ . '()', '5.5.0', 'WC_PB_Product_Prices::get_product_price()' );
		return self::get_product_price( $product, array(
			'price' => $price,
			'qty'   => $qty,
			'calc'  => 'display'
		) );
	}
}

WC_PB_Product_Prices::init();

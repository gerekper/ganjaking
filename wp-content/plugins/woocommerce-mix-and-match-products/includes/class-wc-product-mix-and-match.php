<?php
/**
 * Product Class
 *
 * @author   Kathy Darling
 * @category Classes
 * @package  WooCommerce Mix and Match Products/Classes/Products
 * @since    1.0.0
 * @version  1.10.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Product_Mix_and_Match Class.
 *
 * The custom product type for WooCommerce.
 *
 * @uses  WC_Product
 */
class WC_Product_Mix_and_Match extends WC_Product {

	/**
	 * Price-specific data, used to calculate min/max product prices for display and min/max prices incl/excl tax.
	 * @var array
	 */
	private $pricing_data;

	/**
	 * Array of container price data for consumption by the front-end script.
	 * @var array
	 */
	private $container_price_data = array();

	/**
	 * Child products/variations.
	 * @var array
	 */
	private $children;

	/**
	 * Array of child keys that are available.
	 * @var array
	 */
	private $available_children;

	/**
	 * In per-product pricing mode, the sale status of the product is defined by the children.
	 * @var bool
	 */
	private $on_sale;

	/**
	 * True if children stock can fill all slots.
	 * @var bool
	 */
	private $has_enough_children_in_stock;

	/**
	 * True if children must be backordered to fill all slots.
	 * @var bool
	 */
	private $backorders_required;

	/**
	 * True if product data is in sync with children.
	 * @var bool
	 */
	private $is_synced = false;

	/**
	 * Runtime cache for calculated prices.
	 * @var array
	 */
	private $mnm_price_cache = array();

	/**
	 * Layout options data.
	 * @see 'WC_Product_Mix_and_Match::get_layout_options'.
	 * @var array
	 */
	private static $layout_options_data = null;

	/**
	 *  Define type-specific properties.
	 * @var array
	 */
	protected $extra_data = array(
		'min_raw_price'         => '',
		'min_raw_regular_price' => '',
		'max_raw_price'         => '',
		'max_raw_regular_price' => '',
		'layout'                     => 'tabular',
		'add_to_cart_form_location'  => 'default',
		'min_container_size'    => 0,
		'max_container_size'    => null,
		'contents'              => array(),
		'priced_per_product'    => false,
		'discount'				=> 0,
		'shipped_per_product'   => false
	);

	/**
	 * __construct function.
	 *
	 * @param  mixed $product
	 */
	public function __construct( $product = 0 ) {

		// Back-compat.
		$this->product_type = 'mix-and-match';

		parent::__construct( $product );
	}


	/*
	|--------------------------------------------------------------------------
	| CRUD Getters.
	|--------------------------------------------------------------------------
	*/


	/**
	 * Returns the base active price of the MnM bundle.
	 *
	 * @since  1.2.0
	 *
	 * @param  string $context
	 * @return mixed
	 */
	public function get_price( $context = 'view' ) {
		$value = $this->get_prop( 'price', $context );
		return in_array( $context, array( 'view', 'sync' ) ) && $this->is_priced_per_product() ? (double) $value : $value;
	}


	/**
	 * Returns the base regular price of the MnM bundle.
	 *
	 * @since  1.2.0
	 *
	 * @param  string $context
	 * @return mixed
	 */
	public function get_regular_price( $context = 'view' ) {
		$value = $this->get_prop( 'regular_price', $context );
		return in_array( $context, array( 'view', 'sync' ) ) && $this->is_priced_per_product() ? (double) $value : $value;
	}


	/**
	 * Returns the base sale price of the MnM bundle.
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $context
	 * @return mixed
	 */
	public function get_sale_price( $context = 'view' ) {
		$value = $this->get_prop( 'sale_price', $context );
		return in_array( $context, array( 'view', 'sync' ) ) && $this->is_priced_per_product() && '' !== $value ? (double) $value : $value;
	}

	/**
	 * "Form Location" getter.
	 *
	 * @since  1.3.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_add_to_cart_form_location( $context = 'view' ) {
		return $this->get_prop( 'add_to_cart_form_location', $context );
	}


	/**
	 * "Layout" getter.
	 *
	 * @since  1.3.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_layout( $context = 'any' ) {
		return $this->get_prop( 'layout', $context );
	}


	/**
	 * Minimum raw MnM bundle price getter.
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_min_raw_price( $context = 'view' ) {
		$this->sync();
		$value = $this->get_prop( 'min_raw_price', $context );
		return in_array( $context, array( 'view', 'sync' ) ) && $this->is_priced_per_product() && '' !== $value ? (double) $value : $value;
	}


	/**
	 * Minimum raw regular MnM bundle price getter.
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_min_raw_regular_price( $context = 'view' ) {
		$this->sync();
		$value = $this->get_prop( 'min_raw_regular_price', $context );
		return in_array( $context, array( 'view', 'sync' ) ) && $this->is_priced_per_product() && '' !== $value ? (double) $value : $value;
	}


	/**
	 * Minimum raw MnM bundle price getter.
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_max_raw_price( $context = 'view' ) {
		$this->sync();
		$value = $this->get_prop( 'max_raw_price', $context );
		$value = 'edit' !== $context && $this->get_max_container_size() && $this->is_priced_per_product() && '' !== $value ? (double) $value : $value;
		$value = 'edit' === $context && '' === $value ? 9999999999.0 : $value;
		return $value;
	}


	/**
	 * Minimum raw regular MnM bundle price getter.
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_max_raw_regular_price( $context = 'view' ) {
		$this->sync();
		$value = $this->get_prop( 'max_raw_regular_price', $context );
		$value = 'edit' !== $context && $this->get_max_container_size() && $this->is_priced_per_product() && '' !== $value ? (double) $value : $value;
		$value = 'edit' === $context && '' === $value ? 9999999999.0 : $value;
		return $value;
	}


	/**
	 * Per-Item Pricing getter.
	 *
	 * @since  1.2.0
	 *
	 * @param  string $context
	 * @return bool
	 */
	public function get_priced_per_product( $context = 'any' ) {
		return $this->get_prop( 'priced_per_product', $context );
	}


	/**
	 * Per-Item Discount getter.
	 *
	 * @since  1.4.0
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_discount( $context = 'any' ) {
		$value = $this->get_prop( 'discount', $context );
			
		if ( 'edit' !== $context ) {
			$value = floatval( $this->is_priced_per_product() ? $value : 0 );
		}
		return $value;
	}


	/**
	 * Per-Item Shipping getter.
	 *
	 * @since  1.2.0
	 *
	 * @param  string $context
	 * @return bool
	 */
	public function get_shipped_per_product( $context = 'any' ) {
		return $this->get_prop( 'shipped_per_product', $context );
	}


	/**
	 * Return the product's minimum size limit.
	 *
	 * @param  string $context
	 * @return int
	 */
	public function get_min_container_size( $context = 'view' ) {
		$value = $this->get_prop( 'min_container_size', 'edit' );

		/**
		 * Container minimum size.
		 *
		 * @param  str 				  $size
		 * @param  obj WC_Product 	  $product
	 	*/
		return 'view' === $context ? apply_filters( 'woocommerce_mnm_min_container_size', $value, $this ) : $value;
	}

	/**
	 * Return the product's maximum size limit.
	 * @param  string $context
	 * @return mixed | string or int
	 */
	public function get_max_container_size( $context = 'view' ) {
		$value = $this->get_prop( 'max_container_size', 'edit' );

		/**
		 * Container maximum size.
		 *
		 * @param  mixed			  $size
		 * @param  obj WC_Product 	  $product
	 	*/
		return 'view' === $context ? apply_filters( 'woocommerce_mnm_max_container_size', $value, $this ) : $value;
	}

	/**
	 * Contained product IDs getter.
	 *
	 * @since  1.2.0
	 *
	 * @param  string $context
	 * @return array
	 */
	public function get_contents( $context = 'view' ) {
		return $this->get_prop( 'contents', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Setters.
	|--------------------------------------------------------------------------
	*/

	/**
	 * "Form Location" setter.
	 *
	 * @since  1.3.0
	 *
	 * @param  string  $value
	 */
	public function	set_add_to_cart_form_location( $value ) {
		$value = in_array( $value, array_keys( self::get_add_to_cart_form_location_options() ) ) ? $value : 'default';
		return $this->set_prop( 'add_to_cart_form_location', $value );
	}

	/**
	 * "Layout" setter.
	 *
	 * @since  1.3.0
	 *
	 * @param  string  $layout
	 */
	public function set_layout( $layout ) {
		$layout = array_key_exists( $layout, self::get_layout_options() ) ? $layout : 'tabular';
		$this->set_prop( 'layout', $layout );
	}

	/**
	 * Minimum raw price setter.
	 *
	 * @since  1.2.0
	 *
	 * @param  mixed  $value
	 */
	public function set_min_raw_price( $value ) {
		$value = wc_format_decimal( $value );
		$this->set_prop( 'min_raw_price', $value );
	}


	/**
	 * Minimum raw regular bundle price setter.
	 *
	 * @since  1.2.0
	 *
	 * @param  mixed  $value
	 */
	public function set_min_raw_regular_price( $value ) {
		$value = wc_format_decimal( $value );
		$this->set_prop( 'min_raw_regular_price', $value );
	}


	/**
	 * Maximum raw price setter.
	 *
	 * @since  1.2.0
	 *
	 * @param  mixed  $value
	 */
	public function set_max_raw_price( $value ) {
		$value = wc_format_decimal( min( $value, 9999999999 ) );
		$this->set_prop( 'max_raw_price', $value );
	}


	/**
	 * Maximum raw regular bundle price setter.
	 *
	 * @since  1.2.0
	 *
	 * @param  mixed  $value
	 */
	public function set_max_raw_regular_price( $value ) {
		$value = wc_format_decimal( min( $value, 9999999999 ) );
		$this->set_prop( 'max_raw_regular_price', $value );
	}


	/**
	 * Per-Item Pricing setter.
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $value
	 */
	public function set_priced_per_product( $value ) {
		$this->set_prop( 'priced_per_product', wc_string_to_bool( $value ) );
	}


	/**
	 * Per-Item Pricing Discount setter.
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $value
	 */
	public function set_discount( $value ) {
		$this->set_prop( 'discount', wc_format_decimal( $value ) );
	}


	/**
	 * Per-Item Shipping setter.
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $value
	 */
	public function set_shipped_per_product( $value ) {
		$this->set_prop( 'shipped_per_product', wc_string_to_bool( $value ) );
	}


	/**
	 * Set the product's minimum size limit.
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $value
	 */
	public function set_min_container_size( $value ) {
		$this->set_prop( 'min_container_size', '' !== $value ? absint( $value ) : 0 );
	}


	/**
	 * Set the product's maximum size limit.
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $value
	 */
	public function set_max_container_size( $value ) {
		$this->set_prop( 'max_container_size', '' !== $value ? absint( $value ) : '' );
	}


	/**
	 * Contained product IDs setter.
	 *
	 * @since  1.2.0
	 *
	 * @param  array  $value
	 */
	public function set_contents( $value ) {

		$new_contents = array();

		if ( is_array( $value ) ) {

			foreach( $value as $id => $data ) {

				$child_item   = array();
				$product_id   = $variation_id = 0;

				$product_id   = isset( $data['product_id'] ) ? intval( $data['product_id'] ) : 0;
				$variation_id = isset( $data['variation_id'] ) ? intval( $data['variation_id'] ) : 0;
				$child_id     = $variation_id > 0 ? $variation_id : $product_id;

				// Maintain compatibility while waiting for upgrade routine. 
				if ( 0 === $child_id && $id > 0 ) {
					$child_id = $id;
				}

				if ( $child_id > 0 ) {
					$new_contents[$child_id]['child_id']     = $child_id;
					$new_contents[$child_id]['product_id']   = $product_id;
					$new_contents[$child_id]['variation_id'] = $variation_id;
				}

			}

		}

		$this->set_prop( 'contents', $new_contents );
	}

	/*
	|--------------------------------------------------------------------------
	| Other methods.
	|--------------------------------------------------------------------------
	*/


	/**
	 * Wrapper for get_permalink that adds bundle configuration data to the URL.
	 *
	 * @return string
	 */
	public function get_permalink() {

		$permalink     = get_permalink( $this->get_id() );
		$fn_args_count = func_num_args();

		if ( 1 === $fn_args_count ) {

			$cart_item = func_get_arg( 0 );

			if ( is_array( $cart_item ) && isset( $cart_item[ 'mnm_config' ] ) && is_array( $cart_item[ 'mnm_config' ] ) ) {

				$container_quantity = isset( $cart_item['quantity'] ) ? intval( $cart_item['quantity'] ) : 0;

				$qty_args = WC_Mix_and_Match()->cart->rebuild_posted_container_form_data( $cart_item[ 'mnm_config' ], $this );

				if ( ! empty( $qty_args ) ) {
					$args = array_merge( $qty_args, array( 'quantity' => $container_quantity ) );
					$permalink = add_query_arg( $args, $permalink );
				}
			}
		}

		return $permalink;
	}


	/**
	 * Get internal type.
	 * @return string
	 */
	public function get_type() {
		return 'mix-and-match';
	}


	/**
	 * Is this a NYP product?
	 * @return bool
	 */
	public function is_nyp() {
		if ( ! isset( $this->is_nyp ) ) {
			$this->is_nyp = WC_Mix_and_Match()->compatibility->is_nyp( $this );
		}
		return $this->is_nyp;
	}


	/**
	 * Mimics the return of the product's children posts.
	 * these are the items that are allowed to be in the container (but aren't actually child posts)
	 *
	 * @return array
	 */
	public function get_children() {

		$this->children = WC_Mix_and_Match_Helpers::cache_get( 'child_products_' . $this->get_id() );

		if ( null === $this->children ) {

			$this->children = array();

			if ( $contents = $this->get_contents() ) {

				/*
				 * Currently data is stored as array( ID => 1 )
				 * And so we rely on the array keys of the stored array
				 */
				foreach ( $contents as $mnm_item_id => $mnm_item_data ) {

					$product = wc_get_product( $mnm_item_id );

					if ( $product ) {
						$this->maybe_apply_discount_to_child( $product );
						$this->children[ $mnm_item_id ] = $product;
					}
				}
			}

			/**
			 * Container's children.
			 *
			 * @param  array			  $children
			 * @param  obj WC_Product 	  $this
		 	*/
			$this->children = apply_filters( 'woocommerce_mnm_get_children', $this->children, $this );

			WC_Mix_and_Match_Helpers::cache_set( 'child_products_' . $this->get_id(), $this->children );

		}

		return $this->children;

	}

	/**
	 * Get the product object of one of the child items.
	 *
	 * @param  int 		$child_id
	 * @return object 	WC_Product or WC_Product_Variation
	 */
	public function get_child( $child_id ) {

		if ( is_array( $this->children ) && isset( $this->children[ $child_id ] ) ) {
			$child = $this->children[ $child_id ];
		} else {
			$child = wc_get_product( $child_id );
		}

		/**
		 * Individual child product.
		 *
		 * @param  obj WC_Product or WC_Product_Variation  $child The child product or variation.
		 * @param  obj WC_Product 	  					   $this
	 	*/
		return apply_filters( 'woocommerce_mnm_get_child', $child, $this );
	}


	/**
	 * Returns whether or not the product has any child product.
	 *
	 * @return bool
	 */
	public function has_children() {
		return sizeof( $this->get_children() ) ? true : false;
	}


	/**
	 * Get an array of available children for the current product.
	 *
	 * @return array
	 */
	public function get_available_children() {

		$this->sync();

		$available_children = array();

		foreach ( $this->get_children() as $child_id => $child ) {

			if ( $this->is_child_available( $child_id ) && apply_filters( 'woocommerce_mnm_is_child_available', true, $child, $this ) ) {
				$available_children[ $child_id ] = $child;
			}
		}

		return $available_children;
    }


    /**
     * Is child item available for inclusion in container.
     *
     * @param  int 	$child_id
     * @return bool
     */
	public function is_child_available( $child_id ) {

		$this->sync();

		if ( ! empty( $this->available_children ) && in_array( $child_id, $this->available_children ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Stock of container is synced to allowed child items.
	 *
	 * @return bool
	 */
	public function is_synced() {
		return $this->is_synced;
	}


    /**
     * Sync child data such as price, availability, etc.
     */
	public function sync() {

		if ( $this->is_synced() ) {
			return false;
		}

		/**
		 * wc_mnm_before_sync hook.
		 *
		 * @since  1.9.1
		 * @param  obj $product WC_Product_Mix_and_Match
		 */
		do_action( 'wc_mnm_before_sync', $this );

		/*-----------------------------------------------------------------------------------*/
		/*	Sync Availability Data.
		/*-----------------------------------------------------------------------------------*/

		$this->available_children           = array();

		$min_raw_price                      = $this->get_price( 'sync' );
		$max_raw_price                      = $this->get_price( 'sync' );
		$min_raw_regular_price              = $this->get_regular_price( 'sync' );
		$max_raw_regular_price              = $this->get_regular_price( 'sync' );

		$this->has_enough_children_in_stock = false;
		$this->backorders_required          = false;

		$items_in_stock                     = 0;
		$backorders_allowed                 = false;
		$unlimited_stock_available          = false;

		$children                           = $this->get_children();
		$min_container_size                 = $this->get_min_container_size();
		$max_container_size                 = $this->get_max_container_size();

		if ( empty( $children ) ) {
			$this->is_synced = true;
			return;
		}

		foreach ( $children as $child_id => $child ) {

			// Skip any item that isn't in stock/purchasable.
			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && ! $child->is_in_stock() ) {
				continue;
			}

			// Skip any item that isn't purchasable.
			if ( ! $child->is_purchasable() ) {
				continue;
			}

			// Store available child id.
			$this->available_children[] = $child_id;

			$unlimited_child_stock_available = false;
			$child_stock_available           = 0;
			$child_backorders_allowed        = false;
			$child_sold_individually         = $child->is_sold_individually();

			// Calculate how many slots this child can fill with backordered / non-backordered items.
			if ( $child->managing_stock() ) {

				$child_stock = $child->get_stock_quantity();

				if ( $child_stock > 0 ) {

					$child_stock_available = $child_stock;

					if ( $child->backorders_allowed() ) {
						$backorders_allowed = $child_backorders_allowed = true;
					}

				} elseif ( $child->backorders_allowed() ) {
					$backorders_allowed = $child_backorders_allowed = true;
				}

			} elseif ( $child->is_in_stock() ) {
				$unlimited_stock_available = $unlimited_child_stock_available = true;
			}

			// Set max number of slots according to stock status and max container size.
			if ( $child_sold_individually ) {
				$this->sold_individually = true;
				$this->pricing_data[ $child_id ][ 'slots' ] = 1;
			} else if ( $max_container_size > 0 ) {
				if ( $unlimited_child_stock_available || $child_backorders_allowed ) {
					$this->pricing_data[ $child_id ][ 'slots' ] = $max_container_size;
				} else {
					$this->pricing_data[ $child_id ][ 'slots' ] = $child_stock_available > $max_container_size ? $max_container_size : $child_stock_available;
				}
				// If max_container_size = 0, then unlimited so only limit by stock.
			} else if ( $unlimited_child_stock_available || $child_backorders_allowed ){
				$this->pricing_data[ $child_id ][ 'slots' ] = '';
			} else {
				$this->pricing_data[ $child_id ][ 'slots' ] = $child_stock_available;
			}

			// Store price and slots for the min/max price calculation.
			if ( $this->is_priced_per_product() ) {

				$this->maybe_apply_discount_to_child( $child );

				$this->pricing_data[ $child_id ][ 'price_raw' ]         = (double) $child->get_price( 'edit' );
				$this->pricing_data[ $child_id ][ 'price' ]             = (double) $child->get_price();
				$this->pricing_data[ $child_id ][ 'regular_price_raw' ] = (double) $child->get_regular_price( 'edit' );
				$this->pricing_data[ $child_id ][ 'regular_price' ]     = (double) $child->get_regular_price();

				// Amount used up in "cheapest" config.
				$this->pricing_data[ $child_id ][ 'slots_filled_min' ] = 0;
				// Amount used up in "most expensive" config.
				$this->pricing_data[ $child_id ][ 'slots_filled_max' ] = 0;

				// Save sale status for parent.
				if ( $child->is_on_sale( 'edit' ) ) {
					$this->on_sale = true;
				}
			}

			$items_in_stock += $child_stock_available;
		}

		// Update data for container availability.
		if ( $unlimited_stock_available || $backorders_allowed || $items_in_stock >= $min_container_size ) {
			$this->has_enough_children_in_stock = true;
		}

		if ( ! $unlimited_stock_available && $backorders_allowed && $items_in_stock < $min_container_size ) {
			$this->backorders_required = true;
		}

		/*-----------------------------------------------------------------------------------*/
		/*	Per Product Pricing Min/Max Prices.
		/*-----------------------------------------------------------------------------------*/

		if ( $this->is_priced_per_product() && ! empty( $this->available_children ) ) {

			/*-----------------------------------------------------------------------------------*/
			/*	Min Price.
			/*-----------------------------------------------------------------------------------*/

			// Slots filled so far.
			$filled_slots = 0;

			// Sort by cheapest.
			uasort( $this->pricing_data, array( $this, 'sort_by_price' ) );

			if ( $this->has_enough_children_in_stock ) {

				// Fill slots and calculate min price.
				foreach ( $this->pricing_data as $child_id => $data ) {

					$slots_to_fill = $min_container_size - $filled_slots;

					$items_to_use = $this->pricing_data[ $child_id ][ 'slots_filled_min' ] = $this->pricing_data[ $child_id ][ 'slots' ] !== '' ? min( $this->pricing_data[ $child_id ][ 'slots' ], $slots_to_fill ) : $slots_to_fill;

					$filled_slots += $items_to_use;

					$min_raw_price         += $items_to_use * $this->pricing_data[ $child_id ][ 'price_raw' ];
					$min_raw_regular_price += $items_to_use * $this->pricing_data[ $child_id ][ 'regular_price_raw' ];

					if ( $filled_slots >= $min_container_size ) {
						break;
					}
				}

			} else {

				// In the unlikely even that stock is insufficient, just calculate the min price from the cheapest child
				foreach ( $this->pricing_data as $child_id => $data ) {
					$this->pricing_data[ $child_id ][ 'slots_filled_min' ] = 0;
				}

				$cheapest_child_id   = current( array_keys( $this->pricing_data ) );
				$cheapest_child_data = current( array_values( $this->pricing_data ) );

				$this->pricing_data[ $cheapest_child_id ][ 'slots_filled_min' ] = $min_container_size;

				$min_raw_price         += $cheapest_child_data[ 'price_raw' ] * $min_container_size;
				$min_raw_regular_price += $cheapest_child_data[ 'regular_price_raw' ] * $min_container_size;
			}

			/*-----------------------------------------------------------------------------------*/
			/*	Max Price.
			/*-----------------------------------------------------------------------------------*/

			// Slots filled so far.
			$filled_slots = 0;

			// Sort by most expensive.
			arsort( $this->pricing_data );

			if ( $this->has_enough_children_in_stock && $max_container_size !== '' && ! $this->is_nyp() ) {

				// Fill slots and calculate max price.
				foreach ( $this->pricing_data as $child_id => $data ) {

					$slots_to_fill = $max_container_size - $filled_slots;

					$items_to_use = $this->pricing_data[ $child_id ][ 'slots_filled_max' ] = $this->pricing_data[ $child_id ][ 'slots' ] !== '' ? min( $this->pricing_data[ $child_id ][ 'slots' ], $slots_to_fill ) : $slots_to_fill;

					$filled_slots += $items_to_use;

					$max_raw_price         += $items_to_use * $this->pricing_data[ $child_id ][ 'price_raw' ];
					$max_raw_regular_price += $items_to_use * $this->pricing_data[ $child_id ][ 'regular_price_raw' ];

					if ( $filled_slots >= $max_container_size ) {
						break;
					}
				}

			} else {

				// In the unlikely even that stock is insufficient, just calculate the max price from the most expensive child.
				foreach ( $this->pricing_data as $child_id => $data ) {
					$this->pricing_data[ $child_id ][ 'slots_filled_max' ] = 0;
				}

				if ( $max_container_size !== '' && ! $this->is_nyp() ) {

					$priciest_child_id   = current( array_keys( $this->pricing_data ) );
					$priciest_child_data = current( array_values( $this->pricing_data ) );

					$this->pricing_data[ $priciest_child_id ][ 'slots_filled_max' ] = $max_container_size;

					$max_raw_price         += $priciest_child_data[ 'price_raw' ] * $max_container_size;
					$max_raw_regular_price += $priciest_child_data[ 'regular_price_raw' ] * $max_container_size;

				}
			}
		}

		if ( $this->is_nyp() || ( $this->is_priced_per_product() && $max_container_size === '' ) ) {
			$max_raw_price = $max_raw_regular_price = '';
		}

		$this->is_synced = true;

		/*
		 * Set min/max raw (regular) prices.
		 */

		$raw_price_meta_changed = false;

		if ( $this->get_min_raw_price( 'sync' ) !== $min_raw_price || $this->get_min_raw_regular_price( 'sync' ) !== $min_raw_regular_price || $this->get_max_raw_price( 'sync' ) !== $max_raw_price || $this->get_max_raw_regular_price( 'sync' ) !== $max_raw_regular_price ) {
			$raw_price_meta_changed = true;
		}

		$this->set_min_raw_price( $min_raw_price );
		$this->set_min_raw_regular_price( $min_raw_regular_price );
		$this->set_max_raw_price( $max_raw_price );
		$this->set_max_raw_regular_price( $max_raw_regular_price );

		if ( $raw_price_meta_changed ) {
			$this->data_store->update_raw_prices( $this );
		}

		/**
		 * woocommerce_mnm_synced hook.
		 *
		 * @param  obj $product WC_Product
		 */
		do_action( 'woocommerce_mnm_synced', $this );
    }


	/**
	 * Sort array data by price.
	 *
	 * @param  array $a
	 * @param  array $b
	 * @return -1|0|1
	 */
    private function sort_by_price( $a, $b ) {

	    if ( $a[ 'price' ] == $b[ 'price' ] ) {
	        return 0;
	    }

	    return ( $a[ 'price' ] < $b[ 'price' ] ) ? -1 : 1;
	}


	/**
	 * Get min/max mnm price.
	 *
	 * @param  string $min_or_max
	 * @return mixed
	 */
	public function get_mnm_price( $min_or_max = 'min', $display = false ) {

		if ( $this->is_priced_per_product() ) {

			$this->sync();

			$cache_key = md5( json_encode( apply_filters( 'woocommerce_mnm_prices_hash', array(
				'type'       => 'price',
				'display'    => $display,
				'min_or_max' => $min_or_max
			), $this ) ) );

			if ( isset( $this->mnm_price_cache[ $cache_key ] ) ) {
				$price = $this->mnm_price_cache[ $cache_key ];
			} else {

				$raw_price_fn_name = 'get_' . $min_or_max . '_raw_price';

				if ( $this->$raw_price_fn_name() === '' ) {
					$price = '';
				} else {
					$price = $display ? wc_get_price_to_display( $this, array( 'price' => $this->get_price() ) ) : $this->get_price();

					if ( ! empty( $this->pricing_data ) ) {
						foreach ( $this->pricing_data as $child_id => $data ) {
							$qty = $data[ 'slots_filled_' . $min_or_max ];
							if ( $qty ) {
								$child = $this->get_child( $child_id );
								if ( $display ) {
									$price += wc_get_price_to_display( $child, array( 'qty' => $qty, 'price' => $data[ 'price' ] ) );
								} else {
									$price += $qty * $data[ 'price' ];
								}
							}
						}
					}
				}

				$this->mnm_price_cache[ $cache_key ] = $price;
			}

		} else {

			$price = $this->get_price();

			if ( $display ) {
				$price = wc_get_price_to_display( $this, array( 'price' => $price ) );
			}
		}

		return $price;
	}


	/**
	 * Get min/max MnM regular price.
	 *
	 * @param  string $min_or_max
	 * @return mixed
	 */
	public function get_mnm_regular_price( $min_or_max = 'min', $display = false ) {

		if ( $this->is_priced_per_product() ) {

			$this->sync();

			$cache_key = md5( json_encode( apply_filters( 'woocommerce_mnm_prices_hash', array(
				'type'       => 'regular_price',
				'display'    => $display,
				'min_or_max' => $min_or_max
			), $this ) ) );

			if ( isset( $this->mnm_price_cache[ $cache_key ] ) ) {
				$price = $this->mnm_price_cache[ $cache_key ];
			} else {

				$raw_price_fn_name = 'get_' . $min_or_max . '_raw_regular_price';

				if ( $this->$raw_price_fn_name() === '' ) {
					$price = '';
				} else {
					$price = $display ? wc_get_price_to_display( $this, array( 'price' => $this->get_regular_price() ) ) : $this->get_regular_price();
					if ( ! empty( $this->pricing_data ) ) {
						foreach ( $this->pricing_data as $child_id => $data ) {
							$qty = $data[ 'slots_filled_' . $min_or_max ];
							if ( $qty ) {
								$child = $this->get_child( $child_id );
								if ( $display ) {
									$price += wc_get_price_to_display( $child, array( 'qty' => $qty, 'price' => $data[ 'regular_price' ] ) );
								} else {
									$price += $qty * $data[ 'regular_price' ];
								}
							}
						}
					}
				}

				$this->mnm_price_cache[ $cache_key ] = $price;
			}

		} else {

			$price = $this->get_regular_price();

			if ( $display ) {
				$price = wc_get_price_to_display( $this, array( 'price' => $price ) );
			}
		}

		return $price;
	}


	/**
	 * MnM price including tax.
	 *
	 * @return mixed
	 */
	public function get_mnm_price_including_tax( $min_or_max = 'min', $qty = 1 ) {

		if ( $this->is_priced_per_product() ) {

			$this->sync();

			$cache_key = md5( json_encode( apply_filters( 'woocommerce_mnm_prices_hash', array(
				'type'       => 'price_incl_tax',
				'qty'        => $qty,
				'min_or_max' => $min_or_max
			), $this ) ) );

			if ( isset( $this->mnm_price_cache[ $cache_key ] ) ) {
				$price = $this->mnm_price_cache[ $cache_key ];
			} else {

				$price = wc_get_price_including_tax( $this, array( 'qty' => $qty, 'price' => $this->get_price() ) );

				if ( ! empty( $this->pricing_data ) ) {
					foreach ( $this->pricing_data as $child_id => $data ) {
						$item_qty = $qty * $data[ 'slots_filled_' . $min_or_max ];
						if ( $item_qty ) {
							$child = $this->get_child( $child_id );
							$price += wc_get_price_including_tax( $child, array( 'qty' => $item_qty, 'price' => $data[ 'price' ] ) );
						}
					}
				}

				$this->mnm_price_cache[ $cache_key ] = $price;
			}

		} else {
			$price = wc_get_price_including_tax( $this, array( 'qty' => $qty, 'price' => $this->get_price() ) );
		}

		return $price;
	}


	/**
	 * Min/max MnM price excl tax.
	 *
	 * @return mixed
	 */
	public function get_mnm_price_excluding_tax( $min_or_max = 'min', $qty = 1 ) {

		if ( $this->is_priced_per_product() ) {

			$this->sync();

			$cache_key = md5( json_encode( apply_filters( 'woocommerce_mnm_prices_hash', array(
				'type'       => 'price_excl_tax',
				'qty'        => $qty,
				'min_or_max' => $min_or_max
			), $this ) ) );

			if ( isset( $this->mnm_price_cache[ $cache_key ] ) ) {
				$price = $this->mnm_price_cache[ $cache_key ];
			} else {

				$price = wc_get_price_excluding_tax( $this, array( 'qty' => $qty, 'price' => $this->get_price() ) );

				if ( ! empty( $this->pricing_data ) ) {
					foreach ( $this->pricing_data as $child_id => $data ) {
						$item_qty = $qty * $data[ 'slots_filled_' . $min_or_max ];
						if ( $item_qty ) {
							$child = $this->get_child( $child_id );
							$price += wc_get_price_excluding_tax( $child, array( 'qty' => $item_qty, 'price' => $data[ 'price' ] ) );
						}
					}
				}

				$this->mnm_price_cache[ $cache_key ] = $price;
			}

		} else {
			$price = wc_get_price_excluding_tax( $this, array( 'qty' => $qty, 'price' => $this->get_price() ) );
		}

		return $price;
	}


	/**
	 * Returns range style html price string without min and max.
	 *
	 * @param  mixed    $price    default price
	 * @return string             overridden html price string (old style)
	 */
	public function get_price_html( $price = '' ) {

		if ( $this->is_priced_per_product() ) {

			$this->sync();

			// Get the price string.
			if ( $this->get_mnm_price( 'min' ) === '' ) {

				/**
				 * Empty price html.
				 *
				 * @param  str $empty_price
				 * @param  obj WC_Product_Mix_and_Match $this
			 	 */
				$price = apply_filters( 'woocommerce_mnm_empty_price_html', '', $this );

			} else {

				$price = wc_price( $this->get_mnm_price( 'min', true ) );

				if ( $this->is_on_sale() || ( $this->has_discount() && $this->get_mnm_regular_price( 'min' ) !== $this->get_mnm_price( 'min' ) ) ) {

					$regular_price = wc_price( $this->get_mnm_regular_price( 'min', true ) );

					if ( $this->get_mnm_price( 'min' ) !== $this->get_mnm_price( 'max' ) ) {

						$from_price = $price != $regular_price ? wc_format_sale_price( $regular_price, $price ) : $price;
						// translators: %1$s "From string: %2$s min container price with price suffix.
						$price = sprintf( _x( '%1$s%2$s', 'Price range: from', 'woocommerce-mix-and-match-products' ), wc_get_price_html_from_text(), $from_price . $this->get_price_suffix() );
					} else {
						$price = wc_format_sale_price( $regular_price, $price ) . $this->get_price_suffix();
					}

					/**
					 * Sale price html.
					 *
					 * @param  str $sale_price
					 * @param  obj WC_Product_Mix_and_Match $this
				 	 */
					$price = apply_filters( 'woocommerce_mnm_sale_price_html', $price, $this );

				} elseif ( $this->get_max_container_size() && $this->get_mnm_price( 'min' ) == 0 && $this->get_mnm_price( 'max' ) == 0 ) {

					/**
					 * Free string.
					 *
					 * @param  str $free_string
					 * @param  obj WC_Product_Mix_and_Match $this
				 	 */
					$free_string = apply_filters( 'woocommerce_mnm_show_free_string', false, $this ) ? __( 'Free!', 'woocommerce-mix-and-match-products' ) : $price;

					/**
					 * Free price html.
					 *
					 * @param  str $free_price
					 * @param  obj WC_Product_Mix_and_Match $this
				 	 */
			 		$price       = apply_filters( 'woocommerce_mnm_free_price_html', $free_string, $this );

				} else {

					if ( $this->get_mnm_price( 'min' ) !== $this->get_mnm_price( 'max' ) ) {
						// translators: %1$s "From string: %2$s min container price with price suffix.
						$price = sprintf( _x( '%1$s%2$s', 'Price range: from', 'woocommerce-mix-and-match-products' ), wc_get_price_html_from_text(), $price . $this->get_price_suffix() );
					} else {
						$price = $price . $this->get_price_suffix();
					}

					/**
					 * Price html.
					 *
					 * @param  str $price
					 * @param  obj WC_Product_Mix_and_Match $this
				 	 */
					$price = apply_filters( 'woocommerce_mnm_price_html', $price, $this );
				}
			}

			/**
			 * Mix and Match specific price html.
			 *
			 * @param  str $price
			 * @param  obj WC_Product_Mix_and_Match $this
		 	 */
			$price = apply_filters( 'woocommerce_get_mnm_price_html', $price, $this );

			/**
			 * WooCommerce price html.
			 *
			 * @param  str $price
			 * @param  obj WC_Product_Mix_and_Match $this
		 	 */
			return apply_filters( 'woocommerce_get_price_html', $price, $this );

		} else {

			return parent::get_price_html();
		}
	}


	/**
	 * Prices incl. or excl. tax are calculated based on the child products prices, so get_price_suffix() must be overridden to return the correct field in per-product pricing mode.
	 *
	 * @param  mixed    $price  price string
	 * @param  mixed    $qty  item quantity
	 * @return string    modified price html suffix
	 */
	public function get_price_suffix( $price = '', $qty = 1 ) {

		if ( $this->is_priced_per_product() ) {

			$price_suffix  = get_option( 'woocommerce_price_display_suffix' );

			if ( $price_suffix ) {
				$price_suffix = ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

				if ( false !== strpos( $price_suffix, '{price_including_tax}' ) ) {
					$price_suffix = str_replace( '{price_including_tax}', wc_price( $this->get_mnm_price_including_tax() * $qty ), $price_suffix );
				}

				if ( false !== strpos( $price_suffix, '{price_excluding_tax}' ) ) {
					$price_suffix = str_replace( '{price_excluding_tax}', wc_price( $this->get_mnm_price_excluding_tax() * $qty ), $price_suffix );
				}
			}

			/**
			 * WooCommerce price suffix.
			 *
			 * @param  str $price_suffix
			 * @param  obj WC_Product_Mix_and_Match $this
			 * @param  mixed              $price
+			 * @param  int                $qty
		 	 */
			return apply_filters( 'woocommerce_get_price_suffix', $price_suffix, $this, $price, $qty );

		} else {

			return parent::get_price_suffix();
		}
	}


	/**
	 * A MnM product must contain children and have a price in static mode only.
	 *
	 * @return bool
	 */
	public function is_purchasable() {

		$is_purchasable = true;
		
		// Products must exist of course
		if ( ! $this->exists() ) {
			$is_purchasable = false;

			// When priced statically a price needs to be set
		} elseif ( $this->is_priced_per_product() == false && $this->get_price() === '' ) {

			$is_purchasable = false;

			// Check the product is published
		} elseif ( $this->get_status() !== 'publish' && ! current_user_can( 'edit_post', $this->get_id() ) ) {

			$is_purchasable = false;

		} elseif ( false === $this->has_available_children() ) {

			$is_purchasable = false;

		}

		/**
		 * WooCommerce product is purchasable.
		 *
		 * @param  str $is_purchasable
		 * @param  obj WC_Product_Mix_and_Match $this
	 	 */
		return apply_filters( 'woocommerce_is_purchasable', $is_purchasable, $this );
	}


    /**
	 * Returns whether or not the product container has any available child items.
	 *
	 * @return bool
	 */
	public function has_available_children() {
		return sizeof( $this->get_available_children() ) ? true : false;
	}


    /**
	 * Returns whether or not the product container's price is based on the included items.
	 *
	 * @return bool
	 */
	public function is_priced_per_product() {
		/**
		 * @param  bool $is_purchasable
		 * @param  obj WC_Product_Mix_and_Match $this
	 	 */
		return apply_filters( 'woocommerce_mnm_priced_per_product', $this->get_priced_per_product(), $this );
	}


    /**
	 * Returns whether or not the product container's price is based on the included items.
	 *
	 * @since  1.4.0
	 * @return bool
	 */
	public function has_discount() {
		/**
		 * @param  bool $has_discount
		 * @param  obj WC_Product_Mix_and_Match $this
	 	 */
		return apply_filters( 'woocommerce_mnm_has_discount', $this->get_priced_per_product() && $this->get_discount() > 0, $this );
	}


    /**
	 * Returns whether or not the product container's shipping cost is based on the included items.
	 *
	 * @return bool
	 */
	public function is_shipped_per_product() {
		/**
		 * @param  str $is_shipped_per_product
		 * @param  obj WC_Product_Mix_and_Match $this
	 	 */
		return apply_filters( 'woocommerce_mnm_shipped_per_product', $this->get_shipped_per_product(), $this );
	}


    /**
	 * Get availability of container.
	 *
	 * @return array
	 */
	public function get_availability() {

		$backend_availability_data = parent::get_availability();

		if ( ! parent::is_in_stock() || $this->is_on_backorder() ) {
			return $backend_availability_data;
		}

		if ( ! is_admin() ) {

			$this->sync();

			$availability = $class = '';

			if ( ! $this->has_enough_children_in_stock ) {
				$availability = __( 'Insufficient stock', 'woocommerce-mix-and-match-products' );
				$class        = 'out-of-stock';
			}

			if ( $this->backorders_required ) {
				$availability = __( 'Available on backorder', 'woocommerce-mix-and-match-products' );
				$class        = 'available-on-backorder';
			}

			if ( $class == 'out-of-stock' || $class == 'available-on-backorder' ) {
				return array( 'availability' => $availability, 'class' => $class );
			}
		}

		return $backend_availability_data;
	}


    /**
	 * Returns whether container is in stock
	 *
	 * @return bool
	 */
	public function is_in_stock() {

		$backend_stock_status = parent::is_in_stock();

		if ( ! is_admin() ) {

			$this->sync();

			if ( $backend_stock_status === true && ! $this->has_enough_children_in_stock ) {

				return false;
			}
		}

		return $backend_stock_status;
	}


	/**
	 * Override on_sale status of mnm product. In per-product-pricing mode, true if a one of the child products is on sale, or if there is a base sale price defined.
	 *
	 * @param  string  $context
	 * @return bool
	 */
	public function is_on_sale( $context = 'view' ) {

		$is_on_sale = false;

		if ( 'update-price' !== $context && $this->is_priced_per_product() ) {

			$this->sync();

			$is_on_sale = parent::is_on_sale( $context ) || ( $this->on_sale && $this->get_min_raw_regular_price( $context ) > 0 );

		} else {
			$is_on_sale = parent::is_on_sale( $context );
		}
		/**
		 * Only filter Sale Status in "view" context.
		 *
		 * @param  str $is_on_sale
		 * @param  obj WC_Product_Mix_and_Match $this
	 	 */
		return 'view' === $context ? apply_filters( 'woocommerce_mnm_is_on_sale', $is_on_sale, $this ) : $is_on_sale;
	}


	/**
	 * Get the add to cart button text
	 *
	 * @return string
	 */
	public function add_to_cart_text() {

		$text = __( 'Read More', 'woocommerce-mix-and-match-products' );

		if ( $this->is_purchasable() && $this->is_in_stock() ) {
			$text =  __( 'Select options', 'woocommerce-mix-and-match-products' );
		}

		/**
		 * Add to cart text.
		 *
		 * @param  str $text
		 * @param  obj WC_Product_Mix_and_Match $this
	 	 */
		$text = apply_filters( 'mnm_add_to_cart_text', $text, $this );

		/**
		 * WC core filter.
		 *
		 * @param  str $text
		 * @param  obj WC_Product_Mix_and_Match $this
	 	 */
		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
	}


	/**
	 * Get the add to cart button text for the single page.
	 *
	 * @return string
	 */
	public function single_add_to_cart_text() {

		$text = __( 'Add to cart', 'woocommerce-mix-and-match-products' );

		if ( isset( $_GET[ 'update-container' ] ) ) {

			$updating_cart_key = wc_clean( $_GET[ 'update-container' ] );

			if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {
				$text = __( 'Update Cart', 'woocommerce-mix-and-match-products' );
			}
		}

		/** WC core filter. */
		return apply_filters( 'woocommerce_product_single_add_to_cart_text', $text, $this );
	}

	/**
	 * Gets price data array. Contains localized strings and price data passed to JS.
	 *
	 * @since  1.4.0
	 * @return array
	 */
	public function get_container_price_data() {

		$this->sync();

		if ( empty( $this->container_price_data ) ) {

			$container_price_data = array();

			$container_price_data[ 'per_product_pricing' ] = $this->is_priced_per_product() ? 'yes' : 'no';

			$container_price_data[ 'raw_container_min_price' ] = wc_get_price_to_display( $this, array( 'price' => $this->get_min_raw_price() ) );
			$container_price_data[ 'raw_container_regular_price' ] =  wc_get_price_to_display( $this, array( 'price' => $this->get_min_raw_regular_price() ) );
			$container_price_data[ 'raw_container_price' ] = wc_get_price_to_display( $this, array( 'price' => $this->get_max_raw_price() ) );
			$container_price_data[ 'raw_container_regular_price' ] =  wc_get_price_to_display( $this, array( 'price' => $this->get_max_raw_regular_price() ) );

			$container_price_data[ 'price_string' ] = '%s';
			$container_price_data[ 'is_purchasable' ] = $this->is_purchasable() ? 'yes' : 'no';

			$container_price_data[ 'show_free_string' ] =  ( $this->is_priced_per_product() ? apply_filters( 'wc_mnm_show_free_string', false, $this ) : true ) ? 'yes' : 'no';

			$container_price_data[ 'prices' ] = array();
			$container_price_data[ 'regular_prices' ] = array();

			$container_price_data[ 'prices_tax' ] = array();

			$container_price_data[ 'quantities' ] = array();

			$container_price_data[ 'product_ids' ] = array();

			$container_price_data[ 'is_sold_individually' ] = array();

			$container_price_data[ 'base_price' ] = wc_get_price_to_display( $this, array( 'price' => $this->get_price() ) );
			$container_price_data[ 'base_regular_price' ] =  wc_get_price_to_display( $this, array( 'price' => $this->get_regular_price() ) );
			$container_price_data[ 'base_price_tax' ]     = wc_mnm_get_tax_ratios( $this );

			$container_price_data[ 'price' ] 	= $container_price_data[ 'base_price' ];
			$container_price_data[ 'regular_price' ] = $container_price_data[ 'base_regular_price' ];
			$container_price_data[ 'price_tax' ] 	= $container_price_data[ 'base_price_tax' ];

			$totals = new stdClass;

			$totals->price          = 0.0;
			$totals->regular_price  = 0.0;
			$totals->price_incl_tax = 0.0;
			$totals->price_excl_tax = 0.0;

			$container_price_data[ 'base_price_subtotals' ] = $totals;
			$container_price_data[ 'base_price_totals' ]    = $totals;

			$container_price_data[ 'subtotals' ] = $totals;
			$container_price_data[ 'totals' ]    = $totals;

			$children                           = $this->get_children();

			if ( empty( $children ) ) {
				return;
			}

			foreach ( $children as $child_id => $child ) {

				if ( ! $child->is_purchasable() ) {
					continue;
				}

				$container_price_data[ 'is_sold_individually' ][ $child->get_id() ]   = $child->is_sold_individually() ? 'yes' : 'no';
				$container_price_data[ 'product_ids' ][ $child->get_id() ] = $child->get_parent_id() > 0 ? $child->get_parent_id() : $child->get_id();
				$container_price_data[ 'prices' ][ $child->get_id() ]         = $child->get_price();
				$container_price_data[ 'regular_prices' ][ $child->get_id() ] = $child->get_regular_price();

				$container_price_data[ 'prices_tax' ][ $child->get_id() ] = wc_mnm_get_tax_ratios( $child );

				$container_price_data[ 'quantities' ][ $child->get_id() ] = 0;

				$container_price_data[ 'child_item_subtotals' ][ $child->get_id() ] = $totals;
				$container_price_data[ 'child_item_totals' ][ $child->get_id() ] = $totals;

			}

			$this->container_price_data = apply_filters( 'woocommerce_mnm_container_price_data', $container_price_data, $this );

		}

		return $this->container_price_data;

	}

	/**
	 * Get the data attributes
	 *
	 * @return string
	 */
	public function get_data_attributes() {
		$attributes = array(
			'per_product_pricing' => $this->is_priced_per_product() ? 'true' : 'false',
			'container_id'        => $this->get_id(),
			'min_container_size'      => $this->get_min_container_size(),
			'max_container_size'      => $this->get_max_container_size(),
			'base_price'          => wc_get_price_to_display( $this, array( 'price' => $this->get_price() ) ),
			'base_regular_price'  => wc_get_price_to_display( $this, array( 'price' => $this->get_regular_price() ) ),
			'price_data' => json_encode( $this->get_container_price_data() ),
			'input_name' => wc_mnm_get_child_input_name( $this->get_id() ),
		);

		/**
		 * Data attribues.
		 *
		 * @param  array $attributes
		 * @param  obj WC_Product_Mix_and_Match $this
	 	 */
		$attributes = (array) apply_filters( 'woocommerce_mix_and_match_data_attributes', $attributes, $this );

		$data = '';

		foreach ( $attributes as $a => $att ){
			$data .= sprintf( 'data-%s="%s" ', esc_attr( $a ), esc_attr( $att ) );
		}

		return $data;
	}


	/**
	 * Get the min/max/step quantity of a child.
	 *
	 * @param  string $value
	 * @param  string $child_id
	 * @return int
	 */
	public function get_child_quantity( $value, $child_id ) {

		$this->sync();

		$qty = '';

		if ( $mnm_product = $this->get_child( $child_id ) ) {

			if ( $value === 'min' ) {
				$qty = 0;
			} elseif ( 'step' === $value ) {
				$qty = 1;
			} else {
				if ( isset( $this->pricing_data[ $child_id ][ 'slots' ] ) ) {
					$qty = $this->pricing_data[ $child_id ][ 'slots' ];
				}
			}

			/**
			 * Min/Max/Step quantity.
			 *
			 * @param  int $qty Quantity.
			 * @param  obj WC_Product $product
			 * @param  obj WC_Product_Mix_and_Match $this
		 	 */
			$qty = apply_filters( 'woocommerce_mnm_quantity_input_' . $value, $qty, $mnm_product, $this );
		}

		return $qty;
	}


	/**
	 * Get the availability message of a child, taking its purchasable status into account.
	 *
	 * @param  string $child_id
	 * @return string
	 */
	public function get_child_availability_html( $child_id ) {

		$availability_html = '';

		if ( $mnm_product = $this->get_child( $child_id ) ) {

			// If not purchasable, the stock status is of no interest.
			if ( ! $this->is_in_stock() || ! $mnm_product->is_purchasable() ) {
				$availability_html = '<p class="unavailable">' . __( 'Temporarily unavailable', 'woocommerce-mix-and-match-products' ) . '</p>';
			} else {

				$availability      = $mnm_product->get_availability();
				$availability_html = empty( $availability[ 'availability' ] ) ? '' : '<p class="stock ' . esc_attr( $availability[ 'class' ] ) . '">' . esc_html( $availability[ 'availability' ] ) . '</p>';
				$availability_html = apply_filters( 'woocommerce_stock_html', $availability_html, $availability[ 'availability' ], $mnm_product );
			}
		}

		return $availability_html;
	}


	/**
	 * Runtime application of discount to products in an MNM container.
	 *
	 * @since  1.4.0
	 *
	 * @param WC_Product $child
	 */
	public function maybe_apply_discount_to_child( $child ) {

		if( $child && $this->has_discount() ) {
			// Apply discount to regular price and not sale price.
			$price = apply_filters( 'woocommerce_mnm_item_discount_from_regular', true, $this ) ? $child->get_regular_price() : $child->get_price();
			$discounted_price = round( (double) $price * ( 100 - $this->get_discount() ) / 100, wc_get_rounding_precision() );
			$child->set_price( $discounted_price );
			$child->set_sale_price( $discounted_price );
		}

	}

	/*
	|--------------------------------------------------------------------------
	| Static methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Supported "Form Location" options.
	 *
	 * @since  1.3.0
	 *
	 * @return array
	 */
	public static function get_add_to_cart_form_location_options() {

		$options = array(
			'default'      => array(
				'title'       => __( 'Default', 'woocommerce-mix-and-match-products' ),
				'description' => __( 'The add-to-cart form is displayed inside the single-product summary.', 'woocommerce-mix-and-match-products' )
			),
			'after_summary' => array(
				'title'       => __( 'After summary', 'woocommerce-mix-and-match-products' ),
				'description' => __( 'The add-to-cart form is displayed after the single-product summary. Usually allocates the entire page width for displaying form content. Note that some themes may not support this option.', 'woocommerce-mix-and-match-products' )
			)
		);

		return apply_filters( 'woocommerce_mnm_add_to_cart_form_location_options', $options );
	}

	/**
	 * Supported layouts.
	 *
	 * @since  1.3.0
	 *
	 * @return array
	 */
	public static function get_layout_options() {
		if ( is_null( self::$layout_options_data ) ) {
			self::$layout_options_data = apply_filters( 'woocommerce_mnm_supported_layouts', array(
				'tabular' => __( 'Tabular', 'woocommerce-mix-and-match-products' ),
				'grid' => __( 'Grid', 'woocommerce-mix-and-match-products' )
			) );
		}
		return self::$layout_options_data;
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|
	--------------------------------------------------------------------------
	*/

	public function get_base_price() {
		wc_deprecated_function( __METHOD__ . '()', '1.2.0', __CLASS__ . '::get_price()' );
		return $this->get_price( 'edit' );
	}
	public function get_base_regular_price() {
		wc_deprecated_function( __METHOD__ . '()', '1.2.0', __CLASS__ . '::get_regular_price()' );
		return $this->get_regular_price( 'edit' );
	}
	public function get_base_sale_price() {
		wc_deprecated_function( __METHOD__ . '()', '1.2.0', __CLASS__ . '::get_sale_price()' );
		return $this->get_sale_price( 'edit' );
	}
	public function get_mnm_data() {
		wc_deprecated_function( __METHOD__ . '()', '1.2.0', __CLASS__ . '::get_contents()' );
		return $this->get_contents();
	}
	public function get_container_size( $context = 'view' ) {
		wc_deprecated_function( __METHOD__ . '()', '1.2.0', __CLASS__ . '::get_min_container_size()' );
		return $this->get_min_container_size();
	}
	public function maybe_sync() {
		wc_deprecated_function( __METHOD__ . '()', '1.10.0', __CLASS__ . '::sync()' );
		return $this->sync();
	}

}

<?php
/**
 * Product Class
 *
 * @package  WooCommerce Mix and Match Products/Classes/Products
 * @since    1.0.0
 * @version  2.4.10
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
class WC_Product_Mix_and_Match extends WC_Product_Mix_and_Match_Legacy {

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
	 * Array of child item objects.
	 * @var null|WC_MNM_Child_Item[]
	 */
	private $child_items = null;

	/**
	 * Child items that need deleting are stored here.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	protected $child_items_to_delete = array();

	/**
	 * Indicates whether child items need saving.
	 * @var array
	 */
	private $child_items_changed = false;

	/**
	 * In per-product pricing mode, the sale status of the product is defined by the children.
	 * @var bool
	 */
	private $on_sale;

	/**
	 * True if product is NYP enabled.
	 * @var null|bool
	 */
	private $is_nyp = null;

	/**
	 * True if product data is in sync with children.
	 * @var bool
	 */
	private $is_synced = false;

	/**
	 * Runtime cache for calculated prices.
	 * @var array
	 */
	private $container_price_cache = array();

	/**
	 * Layout options data.
	 * @see 'WC_Product_Mix_and_Match::get_layout_options()'.
	 * @var array
	 */
	private static $layout_options_data = null;

	/**
	 * Layout locations data.
	 * @see 'WC_Product_Mix_and_Match::get_add_to_cart_form_location_options()'.
	 * @var array
	 */
	private static $layout_locations_data = null;

	/**
	 *  Define type-specific properties.
	 * @var array
	 */
	protected $extended_data = array(
		'min_raw_price'             => '',
		'min_raw_regular_price'     => '',
		'max_raw_price'             => '',
		'max_raw_regular_price'     => '',
		'layout_override'           => false,
		'layout'                    => 'tabular',
		'add_to_cart_form_location' => 'default',
		'min_container_size'        => 0,
		'max_container_size'        => null,
		'priced_per_product'        => false,
		'discount'                  => 0,
		'packing_mode'              => 'together',
		'weight_cumulative'         => false,
		'content_source'            => 'products',
		'child_category_ids'        => array(),
		'child_items_stock_status'  => 'outofstock', // 'instock' | 'onbackorder' | 'outofstock' - This prop is not saved as meta.
	);

	/**
	 * __construct function.
	 *
	 * @param  mixed $product
	 */
	public function __construct( $product = 0 ) {

		// Back-compat.
		$this->product_type = 'mix-and-match';

		// Merge in our extended data. Renaming to prevent Woo from saving duplicate meta keys. We will handle all own meta saving.
		$this->data = array_merge( $this->data, $this->extended_data );

		parent::__construct( $product );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get internal type.
	 * @return string
	 */
	public function get_type() {
		return 'mix-and-match';
	}

	/**
	 * Checks if a product is virtual (has no shipping).
	 *
	 * @return bool
	 */
	public function is_virtual() {
		return apply_filters( 'woocommerce_is_virtual', in_array( $this->get_packing_mode(), array( 'virtual', 'separate' ) ), $this );
	}

	/**
	 * Load property and runtime cache defaults to trigger a re-sync.
	 *
	 * @since 2.0.0
	 */
	public function load_defaults( $reset_child_items = false ) {

		$this->is_synced          = false;
		$this->container_price_data   = array();
		$this->container_price_cache = array();

		if ( $reset_child_items ) {
			$this->child_items = null;
		}
	}

	/**
	 * Get the add to cart button text for the single page.
	 *
	 * @return string
	 */
	public function single_add_to_cart_text() {

		$text = _x( 'Add to cart', '[Frontend]', 'woocommerce-mix-and-match-products' );

		if ( isset( $_GET['update-container'] ) ) {

			$updating_cart_key = wc_clean( $_GET['update-container'] );

			if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {
				$text = _x( 'Update Cart', '[Frontend]', 'woocommerce-mix-and-match-products' );
			}
		}

		/** WC core filter. */
		return apply_filters( 'woocommerce_product_single_add_to_cart_text', $text, $this );
	}


	/**
	 * Get the add to cart button text
	 *
	 * @return string
	 */
	public function add_to_cart_text() {

		$text = _x( 'Read More', '[Frontend]', 'woocommerce-mix-and-match-products' );

		if ( $this->is_purchasable() && $this->is_in_stock() ) {
			$text =  _x( 'Select options', '[Frontend]', 'woocommerce-mix-and-match-products' );
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
	 * Get the add to cart button text description - used in aria tags.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function add_to_cart_description() {
		/* translators: %s: Product title */
		return apply_filters( 'woocommerce_product_add_to_cart_description', sprintf( _x( 'Select options for &ldquo;%s&rdquo;', '[Frontend]', 'woocommerce-mix-and-match-products' ), $this->get_name() ), $this );
	}


	/**
	 * Returns the base active price of the MnM container.
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
	 * Returns the base regular price of the MnM container.
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
	 * Returns the base sale price of the MnM container.
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

		if ( ! wc_mnm_has_legacy_product_template( $this ) ) {
			return 'default';
		}

		return $this->get_prop( 'add_to_cart_form_location', $context );
	}

	/**
	 * "Override template" getter.
	 *
	 * @since  2.0.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_layout_override( $context = 'view' ) {
		return $this->get_prop( 'layout_override', $context );
	}

	/**
	 * "Layout" getter.
	 *
	 * @since  1.3.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function get_layout( $context = 'view' ) {
		return $this->get_prop( 'layout', $context );
	}


	/**
	 * Minimum raw MnM container price getter.
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
	 * Minimum raw regular MnM container price getter.
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
	 * Minimum raw MnM container price getter.
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
	 * Minimum raw regular MnM container price getter.
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
	public function get_priced_per_product( $context = 'view' ) {
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
	public function get_discount( $context = 'view' ) {
		$value = $this->get_prop( 'discount', $context );

		if ( 'edit' !== $context ) {
			$value = floatval( $this->is_priced_per_product() ? $value : 0 );
		}
		return $value;
	}

	/**
	 * Packing Mode getter.
	 *
	 * @since  2.0.0
	 *
	 * @param  string $context
	 * @return bool
	 */
	public function get_packing_mode( $context = 'view' ) {
		return $this->get_prop( 'packing_mode', $context );
	}

	/**
	 * Shipping weight cumulative getter.
	 *
	 * @since  2.0.0
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_weight_cumulative( $context = 'view' ) {
		return $this->get_prop( 'weight_cumulative', $context );
	}

	/**
	 * Child items content source getter.
	 *
	 * @since  2.0.0
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_content_source( $context = 'view' ) {
		return $this->get_prop( 'content_source', $context );
	}

	/**
	 * Category contents getter.
	 *
	 * @since  2.0.0
	 *
	 * @param  string $context
	 * @return array
	 */
	public function get_child_category_ids( $context = 'view' ) {
		return $this->get_prop( 'child_category_ids', $context );
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
		 * @param  str                $size
		 * @param  obj WC_Product     $product
		*/
		return 'view' === $context ? apply_filters( 'wc_mnm_container_min_size', $value, $this ) : $value;
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
		 * @param  mixed              $size
		 * @param  obj WC_Product     $product
		*/
		return 'view' === $context ? apply_filters( 'wc_mnm_container_max_size', $value, $this ) : $value;
	}


	/**
	 * Get child items stock status.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_child_items_stock_status( $context = 'view' ) {

		if ( ! is_admin() ) {
			$this->sync();
		}

		return $this->get_prop( 'child_items_stock_status' , $context );
	}


	/*
	|--------------------------------------------------------------------------
	| Setters.
	|--------------------------------------------------------------------------
	*/

	/**
	 * "Override template" setter.
	 *
	 * @since  2.0.0
	 *
	 * @param  string  $value
	 */
	public function set_layout_override( $value ) {
		$this->set_prop( 'layout_override', wc_string_to_bool( $value ) );
	}

	/**
	 * "Form Location" setter.
	 *
	 * @since  1.3.0
	 *
	 * @param  string  $location
	 */
	public function set_add_to_cart_form_location( $location ) {
		$location = $location && array_key_exists( $location, self::get_add_to_cart_form_location_options() ) ? $location : 'default';
		$this->set_prop( 'add_to_cart_form_location', $location );
	}


	/**
	 * "Layout" setter.
	 *
	 * @since  1.3.0
	 *
	 * @param  string  $layout
	 */
	public function set_layout( $layout ) {
		$layout = $layout && array_key_exists( $layout, self::get_layout_options() ) ? $layout : 'tabular';
		$this->set_prop( 'layout', $layout );
	}


	/**
	 * Minimum raw price setter.
	 *
	 * @since  1.2.0
	 *
	 * @param string $price Min Raw Price.
	 */
	public function set_min_raw_price( $price ) {
		$this->set_prop( 'min_raw_price', wc_format_decimal( $price ) );
	}


	/**
	 * Minimum raw regular price setter.
	 *
	 * @since  1.2.0
	 *
	 * @param string $price Min Raw Regular Price.
	 */
	public function set_min_raw_regular_price( $price ) {
		$this->set_prop( 'min_raw_regular_price', wc_format_decimal( $price ) );
	}


	/**
	 * Maximum raw price setter.
	 *
	 * @since  1.2.0
	 *
	 * @param string $price Max Raw Price.
	 */
	public function set_max_raw_price( $price ) {
		$this->set_prop( 'max_raw_price', wc_format_decimal( min( $price, 9999999999 ) ) );
	}


	/**
	 * Maximum raw regular price setter.
	 *
	 * @since  1.2.0
	 *
	 * @param string $price Max Raw Regular Price.
	 */
	public function set_max_raw_regular_price( $price ) {
		$this->set_prop( 'max_raw_regular_price', wc_format_decimal( min( $price, 9999999999 ) ) );
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
	 * Packing Mode setter.
	 *
	 * @since  2.0.0
	 *
	 * @param  string  $value 'virtual' | 'together' | 'separate' | 'separate_plus'
	 *    'virtual'       - Everything is virtual.
	 *    'together'      - Packed as a single unit.
	 *    'separate'      - Packed separately, no physical container.
	 *    'separate_plus' - Packed separately, with physical container.
	 */
	public function set_packing_mode( $value ) {
		$value = $value && in_array( $value, array( 'virtual', 'together', 'separate', 'separate_plus' ) ) ? $value : 'together';
		$this->set_prop( 'packing_mode', $value );
	}


	/**
	 * Shipping weight calculation setter.
	 *
	 * @since  2.0.0
	 *
	 * @param  string $value
	 */
	public function set_weight_cumulative( $value ) {
		$this->set_prop( 'weight_cumulative', wc_string_to_bool( $value ) );
	}


	/**
	 * Child items content source setter.
	 *
	 * @since  2.0.0
	 *
	 * @param  string $value - 'products' | 'categories'
	 */
	public function set_content_source( $value ) {
		return $this->set_prop( 'content_source', in_array( $value, array( 'products', 'categories' ) ) ? $value : 'products' );
	}


	/**
	 * Category contents setter.
	 *
	 * @since  2.0.0
	 *
	 * @param  int[] $value
	 */
	public function set_child_category_ids( $value ) {
		$this->set_prop( 'child_category_ids', is_array( $value ) ? array_filter( array_unique( array_map( 'intval', $value ) ), 'term_exists' ) : array() );
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
	 * Child items/product IDs setter.
	 *
	 * @since  2.0.0
	 *
	 * @param  mixed WC_MNM_Child_Item[] | array[]  $data {
	 *     @type  int  $product_id     Child product id.
	 *	   @type  int  $variation_id   Child variation id.
	 * }
	 */
	public function set_child_items( array $data ) {

		// Reindex the existing items by product|variation ID, for easier comparison.
		$current_items = array();
		foreach( $this->get_child_items( 'edit' ) as $child_item ) {
			$current_items[ $child_item->get_variation_id() ? $child_item->get_variation_id() : $child_item->get_product_id() ] = $child_item;
		}

		$incoming_ids = array();
		$new_items    = array();

		// Step 1 - Set all new/updated child items.
		foreach( $data as $data_item ) {
			if ( $data_item instanceof WC_MNM_Child_Item ) {
				$new_item = $data_item;
				$new_item->set_container_id( $this->get_id() );
				$incoming_id = $data_item->get_variation_id() ? $data_item->get_variation_id() : $data_item->get_product_id();
			} else {
				$props = wp_parse_args(
                    (array) $data_item,
                    array(
					'product_id'   => 0,
					'variation_id' => 0,
                    ) 
                );
				$props['container_id'] = $this->get_id();
				$new_item = new WC_MNM_Child_Item( $props, $this );
				$incoming_id = $props[ 'variation_id' ] ? $props[ 'variation_id' ] : $props[ 'product_id' ];
			}

			$incoming_ids[] = $incoming_id; // Store for later comparison.

			// An existing item.
			if ( isset( $current_items[ $incoming_id ] ) ) {
				$new_items[] = $current_items[ $incoming_id ];
			} else {
				$new_items[] = $new_item;
			}

		}

		$this->child_items         = $new_items;
		$this->child_items_changed = true;
		$this->load_defaults();

		// Step 2 - Queue any items to delete.
		foreach( array_diff( array_keys( $current_items ), $incoming_ids ) as $product_id_to_delete ) {
			$this->child_items_to_delete[] = $current_items[ $product_id_to_delete ];
		}

	}


	/**
	 * Set child items stock status.
	 *
	 * @since 2.0.0
	 *
	 * @param string  $status - 'instock' | 'onbackorder' | 'outofstock'
	 * 	  'instock'     - Child items stock can fill all slots.
	 *    'onbackorder' - Child items stock must be backordered to fill all slots.
	 *    'outofstock'  - Child items do not have enough stock to fill all slots.
	 */
	public function set_child_items_stock_status( $status = '' ) {
		$status = in_array( $status, array( 'instock', 'outofstock', 'onbackorder' ) ) ? $status : 'instock';
		$this->set_prop( 'child_items_stock_status', $status );
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	*/

	/**
	 * Equivalent of 'get_changes', but boolean and for child items only.
	 *
	 * @since  2.0.0
	 *
	 * @return boolean
	 */
	public function has_child_item_changes() {
		return $this->child_items_changed;
	}

	/**
	 * Returns whether or not the product has additional options that need
	 * selecting before adding to cart.
	 *
	 * @since  1.10.2
	 * @return boolean
	 */
	public function has_options() {
		return apply_filters( 'woocommerce_product_has_options', true, $this );
	}


	/**
	 * Is this a NYP product?
	 * @return bool
	 */
	public function is_nyp() {
		if ( is_null ( $this->is_nyp ) ) {
			$this->is_nyp = WC_Mix_and_Match()->compatibility->is_nyp( $this );
		}
		return $this->is_nyp;
	}


	/**
	 * Returns whether or not the product container has any visible child items.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context
	 * @return bool
	 */
	public function has_child_items( $context = 'view' ) {
		return sizeof( $this->get_child_items( $context ) );
	}

	/**
	 * A MnM product must contain children and have a price in static mode only.
	 *
	 * @return bool
	 */
	public function is_purchasable() {

		$is_purchasable = true;

		// Not purchasable while updating DB.
		if ( defined( 'WC_MNM_UPDATING' ) ) {
			$is_purchasable = false;

			// Products must exist of course.
		} elseif ( ! $this->exists() ) {
			$is_purchasable = false;

			// When priced statically a price needs to be set.
		} elseif ( false === $this->is_priced_per_product() && '' === $this->get_price() ) {

			$is_purchasable = false;

			// Check the product is published.
		} elseif ( $this->get_status() !== 'publish' && ! current_user_can( 'edit_post', $this->get_id() ) ) {

			$is_purchasable = false;

		} elseif ( ! $this->has_child_items() ) {

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
	 * Returns whether or not the product container's price is based on the included items.
	 *
	 * @param string $context
	 * @return bool
	 */
	public function is_priced_per_product( $context = 'view' ) {

		$is_priced_per_product = $this->get_priced_per_product();

		/**
		 * `wc_mnm_container_is_priced_per_product` filter
		 *
		 * @param  bool $is_purchasable
		 * @param  obj WC_Product_Mix_and_Match $this
		 */
		return 'view' === $context ? apply_filters( 'wc_mnm_container_is_priced_per_product', $is_priced_per_product, $this ) : $is_priced_per_product;
	}


	/**
	 * Returns whether or not the product container's price is based on the included items.
	 *
	 * @since  1.4.0
	 *
	 * @param string $context
	 * @return bool
	 */
	public function has_discount( $context = 'view' ) {

		$has_discount = $this->get_priced_per_product() && $this->get_discount() > 0;

		/**
		 * `wc_mnm_container_has_discount` filter
		 *
		 * @param  bool $has_discount
		 * @param  obj WC_Product_Mix_and_Match $this
		 */
		return 'view' === $context ? apply_filters( 'wc_mnm_container_has_discount', $has_discount, $this ) : $has_discount;
	}


	/**
	 * Returns whether or not the child products are shipped as a single unit.
	 *
	 * @since 2.0.0
	 *
	 * @param  string  $context
	 * @return bool
	 */
	public function is_packed_together( $context = 'view' ) {

		$packed_together = in_array( $this->get_packing_mode( $context ), array( 'virtual', 'together' ) );

		if ( 'view' === $context && has_filter( 'woocommerce_mnm_shipped_per_product' ) ) {

			wc_deprecated_function( 'woocommerce_mnm_shipped_per_product', '2.0.0', 'wc_mnm_container_is_packed_together (NB: packed_together is the opposite of shipped_per_product)' );

			/**
			 * @param  bool $is_shipped_per_product
			 * @param  obj WC_Product_Mix_and_Match $this
			 */
			$packed_together = ! apply_filters( 'woocommerce_mnm_shipped_per_product', ! $packed_together, $this );
		}

		/**
		 * 'wc_mnm_container_is_packed_together' filter.
		 *
		 * @param  bool $is_packed_together
		 * @param  obj WC_Product_Mix_and_Match $this
		 */
		return 'view' === $context ? apply_filters( 'wc_mnm_container_is_packed_together', $packed_together, $this ) : $packed_together;
	}


	/**
	 * Returns whether or not the product container's shipping weight is cumulative.
	 *
	 * @since  2.0.0
	 *
	 * @param  string  $context
	 * @return bool
	 */
	public function is_weight_cumulative( $context = 'view' ) {

		$is_weight_cumulative = $this->needs_shipping() && $this->is_packed_together() && $this->get_weight_cumulative();
		/**
		 * 'wc_mnm_container_is_weight_cumulative' filter.
		 *
		 * @param  bool $is_weight_cumulative
		 * @param  obj WC_Product_Mix_and_Match $this
		 */
		return 'view' === $context ? apply_filters( 'wc_mnm_container_is_weight_cumulative', $is_weight_cumulative, $this ) : $is_weight_cumulative;
	}


	/**
	 * Returns whether container is in stock
	 *
	 * NB: Child items stock is only checked for the child items on the frontend.
	 *
	 * @return bool
	 */
	public function is_in_stock() {

		$is_in_stock = parent::is_in_stock();

		if ( ! is_admin() ) {

			$this->sync();

			if ( $is_in_stock && 'outofstock' === $this->get_child_items_stock_status() ) {
				$is_in_stock = false;
			}
		}

		return apply_filters( 'wc_mnm_container_is_in_stock', $is_in_stock, $this );
	}


	/**
	 * Override on_sale status of mnm product. In per-product-pricing mode, true if has discount or if there is a base sale price defined.
	 *
	 * @param  string  $context
	 * @return bool
	 */
	public function is_on_sale( $context = 'view' ) {

		$is_on_sale = false;

		if ( 'update-price' !== $context && $this->is_priced_per_product() ) {
			$is_on_sale = parent::is_on_sale( $context ) || ( $this->has_discount( $context ) && $this->get_min_raw_regular_price( $context ) > 0 );
		} else {
			$is_on_sale = parent::is_on_sale( $context );
		}

		/**
		 * `wc_mnm_container_is_on_sale` filter
		 *
		 * @param  str $is_on_sale
		 * @param  obj WC_Product_Mix_and_Match $this
		 */
		return 'view' === $context ? apply_filters( 'wc_mnm_container_is_on_sale', $is_on_sale, $this ) : $is_on_sale;
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
	 *
	 * Does this product have a layout override
	 *
	 * @param  string  $context
	 *
	 * @return bool
	 */
	public function has_layout_override( $context = 'view' ) {
		return $this->get_layout_override( $context );
	}


	/**
	 *
	 * Is this product ID in the allowed contents.
	 *
	 * @param  mixed WC_Product|int  $id | product or variation ID
	 *
	 * @return bool
	 */
	public function is_allowed_child_product( $id ) {
		$id = $id instanceof WC_Product ? $id->get_id() : intval( $id );
		return false !== $this->get_child_item_by_product_id( $id );
	}


	/*
	|--------------------------------------------------------------------------
	| Non-CRUD Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return array of allowed child product IDs
	 *
	 * @since  2.0.0
	 *
	 * @return array[] array of child item ID => product|variation ID
	 */
	public function get_child_product_ids( $context = 'view' ) {

		$child_product_ids = WC_MNM_Helpers::cache_get( $this->get_id(), 'child_product_ids' );

		if ( null === $child_product_ids ) {

			$child_product_ids = array();

			foreach ( $this->get_child_items( $context ) as $item_key => $child_item ) {
				$child_product_ids[ $child_item->get_child_item_id() ] = $child_item->get_variation_id() ? $child_item->get_variation_id() : $child_item->get_product_id();
			}

			WC_Mix_and_Match_Helpers::cache_set( $this->get_id(), $child_product_ids, 'child_product_ids' );

		}

		/**
		 * 'wc_mnm_child_product_ids' filter.
		 *
		 * @param  array                     $child_product_ids
		 * @param  WC_Product_Mix_and_Match  $this
		 */
		return 'view' === $context ? apply_filters( 'wc_mnm_child_product_ids', $child_product_ids, $this ) : $child_product_ids;

	}

	/**
	 * Return all child items
	 * these are the items that are allowed to be in the container
	 *
	 * @since  2.0.0
	 *
	 * @return WC_MNM_Child_Item[]
	 */
	public function get_child_items( $context = 'view' ) {

		if ( $this->get_id() && ! $this->has_child_item_changes() ) {
			$this->child_items = WC_MNM_Helpers::cache_get( $this->get_id(), 'child_items' );
		}

		if ( null === $this->child_items ) {

			$this->child_items = array();

			$child_items = $this->data_store->read_child_items( $this );

			// Sanity check that the products do exist.
			foreach ( $child_items as $item_key => $child_item ) {

				if ( $child_item && $child_item->exists() ) {

					if ( ! $child_item->is_visible() ) {
						continue;
					}

					$this->child_items[ $item_key ] = $child_item;

				}

			}

			WC_Mix_and_Match_Helpers::cache_set( $this->get_id(), $this->child_items, 'child_items' );

		}

		/**
		 * 'wc_mnm_child_items' filter.
		 *
		 * @param  WC_MNM_Child_Item[]       $child_items
		 * @param  WC_Product_Mix_and_Match  $this
		 */
		return 'view' === $context ? apply_filters( 'wc_mnm_child_items', $this->child_items, $this ) : $this->child_items;

	}


	/**
	 * Gets a specific child item.
	 *
	 * @since  2.0.0
	 *
	 * @param  int  $child_item_id
	 * @param  string $context
	 * @return false|WC_MNM_Child_Item
	 */
	public function get_child_item( $child_item_id, $context = 'view' ) {
		$child_items = $this->get_child_items( $context );
		return ! empty( $child_items ) && array_key_exists( $child_item_id, $child_items ) ? $child_items[ $child_item_id ] : false;
	}

	/**
	 * Return a specific child item by product|variation ID.
	 *
	 * @since  2.0.0
	 *
	 * @return WC_MNM_Child_Item|false
	 */
	public function get_child_item_by_product_id( $child_product_id, $context = 'view' ) {

		$child_items_by_product = WC_MNM_Helpers::cache_get( $this->get_id(), 'child_items_by_product' );

		if ( null === $child_items_by_product ) {

			$child_items_by_product = array();

			foreach ( $this->get_child_items( $context ) as $child_item ) {
				$child_items_by_product[ $child_item->get_variation_id() ? $child_item->get_variation_id() : $child_item->get_product_id() ] = $child_item;
			}

			WC_Mix_and_Match_Helpers::cache_set( $this->get_id(), $child_items_by_product, 'child_items_by_product' );

		}

		return ! empty( $child_items_by_product ) && array_key_exists( $child_product_id, $child_items_by_product ) ?  $child_items_by_product[ $child_product_id ] : false;

	}

	/**
	 * Adds container configuration data to the URL.
	 *
	 * @since 2.0.0
	 *
	 * @param  array|null $item_object item array If a cart or order item is passed, we can get a link containing the exact attributes selected for the variation, rather than the default attributes.
	 * @return string
	 */
	public function get_cart_edit_link( $item_object = null ) {

		$edit_link = get_permalink( $this->get_id() );

		if ( is_array( $item_object ) && isset( $item_object['mnm_config'] ) && is_array( $item_object['mnm_config'] ) ) {

			$qty_args = WC_Mix_and_Match()->cart->rebuild_posted_container_form_data( $item_object['mnm_config'], $this );

			if ( ! empty( $qty_args ) ) {
				$args = array_merge(
                    $qty_args,
					array(
						'quantity' => isset( $item_object['quantity'] ) ? intval( $item_object['quantity'] ) : 0,
						'update-container' => isset( $item_object['key'] ) ? $item_object['key'] : '',
						)
				);
				$edit_link = add_query_arg( $args, $edit_link );
			}
		}

		return $edit_link;
	}

	/**
	 * Returns range style html price string without min and max.
	 *
	 * @param  mixed    $price    default price
	 * @return string             overridden html price string (old style)
	 */
	public function get_price_html( $price = '' ) {

		if ( ! $this->is_purchasable() ) {
			/**
			 * Empty price html.
			 *
			 * @param  str $empty_price
			 * @param  obj WC_Product_Mix_and_Match $this
			 */
			return apply_filters( 'wc_mnm_container_empty_price_html', '', $this );
		}

		if ( $this->is_priced_per_product() ) {

			$this->sync();

			// Get the price string.
			if ( $this->get_container_price( 'min' ) === '' ) {
				$price = apply_filters( 'wc_mnm_container_empty_price_html', '', $this );
			} elseif ( $this->get_max_container_size() && 0 === $this->get_container_price( 'min' ) && 0 === $this->get_container_price( 'max' ) ) {

				/**
				 * Free string.
				 *
				 * @param  str $free_string
				 * @param  obj WC_Product_Mix_and_Match $this
				 */
				$free_string = apply_filters( 'wc_mnm_container_show_free_string', false, $this ) ? _x( 'Free!', '[Frontend]', 'woocommerce-mix-and-match-products' ) : $price;

				/**
				 * Free price html.
				 *
				 * @param  str $free_price
				 * @param  obj WC_Product_Mix_and_Match $this
				 */
				$price       = apply_filters( 'wc_mnm_container_free_price_html', $free_string, $this );

			} elseif ( $this->is_on_sale() || $this->has_discount() ) {

				if ( $this->get_container_price( 'min' ) === $this->get_container_price( 'max' ) ) {
					$price = wc_format_sale_price( $this->get_container_regular_price( 'min' ), $this->get_container_price( 'min' ) );
				} elseif ( $this->get_max_container_size() ) {

					$show_discounted_ranges = apply_filters( 'wc_mnm_container_show_discounted_range_price', ! is_admin(), $this );

					if ( $show_discounted_ranges ) {
						$price = '<del aria-hidden="true">' . wc_format_price_range( $this->get_container_regular_price( 'min' ), $this->get_container_regular_price( 'max' ) ) . '</del>';
						$price .= ' <ins>' . wc_format_price_range( $this->get_container_price( 'min' ), $this->get_container_price( 'max' ) ) . '</ins>' ;
					} else {
						$price = wc_format_price_range( $this->get_container_price( 'min' ), $this->get_container_price( 'max' ) );
					}
				} else {
					$price = sprintf(
                        _x( 'Starting at %s', '[Frontend]Price range, ex:  Starting at $99', 'woocommerce-mix-and-match-products' ),
						wc_format_sale_price( $this->get_container_regular_price( 'min' ), $this->get_container_price( 'min' ) )
					);
				}

				$price .= $this->get_price_suffix();

				/**
				 * Sale price html.
				 *
				 * @param  str $sale_price
				 * @param  obj WC_Product_Mix_and_Match $this
				 */
				$price = apply_filters( 'wc_mnm_sale_price_html', $price, $this );

			} elseif ( $this->get_container_price( 'min' ) === $this->get_container_price( 'max' ) ) {

				$price = wc_price( $this->get_container_price( 'min' ) ) . $this->get_price_suffix();

			} else {

				// A range price.
				if ( $this->get_max_container_size() ) {
					$price = wc_format_price_range( $this->get_container_price( 'min' ), $this->get_container_price( 'max' ) );
				} else {
					$price = sprintf(
                        _x( 'Starting at %s', '[Frontend]Price range, ex:  Starting at $99', 'woocommerce-mix-and-match-products' ),
						wc_price( $this->get_container_price( 'min' ) )
					);
				}

				$price .= $this->get_price_suffix();

			}

			/**
			 * Mix and Match specific price html.
			 *
			 * @param  str $price
			 * @param  obj WC_Product_Mix_and_Match $this
			 */
			$price = apply_filters( 'wc_mnm_container_get_price_html', $price, $this );

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
					$price_suffix = str_replace( '{price_including_tax}', wc_price( $this->get_container_price_including_tax() * $qty ), $price_suffix );
				}

				if ( false !== strpos( $price_suffix, '{price_excluding_tax}' ) ) {
					$price_suffix = str_replace( '{price_excluding_tax}', wc_price( $this->get_container_price_excluding_tax() * $qty ), $price_suffix );
				}
			}

			/**
			 * WooCommerce price suffix.
			 *
			 * @param  str $price_suffix
			 * @param  obj WC_Product_Mix_and_Match $this
			 * @param  mixed              $price
			 * @param  int                $qty
			 */
			return apply_filters( 'woocommerce_get_price_suffix', $price_suffix, $this, $price, $qty );

		} else {

			return parent::get_price_suffix();
		}
	}


	/**
	 * Get availability of container.
	 *
	 * @return array
	 */
	public function get_availability() {

		$availability = parent::get_availability();

		if ( ! is_admin() && parent::is_in_stock() ) {

			$get_child_items_stock_status = $this->get_child_items_stock_status();

			// If a child does not have enough stock, let people know.
			if ( 'outofstock' === $get_child_items_stock_status ) {

				$availability[ 'availability' ] = _x( 'Insufficient stock', '[Frontend]', 'woocommerce-mix-and-match-products' );
				$availability[ 'class' ]        = 'out-of-stock';

			// If a child is on backorder, the parent should appear to be on backorder, too.
			} elseif ( parent::is_in_stock() && 'onbackorder' === $get_child_items_stock_status ) {

				$availability[ 'availability' ] = _x( 'Available on backorder', '[Frontend]', 'woocommerce-mix-and-match-products' );
				$availability[ 'class' ]        = 'available-on-backorder';

			}
		}

		/**
		 * 'wc_mnm_container_get_availability' filter.
		 *
		 * @param  array                     $availability
		 * @param  WC_Product_Mix_and_Match  $this
		 */
		return apply_filters( 'wc_mnm_container_get_availability', $availability, $this );

	}


	/**
	 * Get min/max container price.
	 *
	 * @since  2.0.0
	 *
	 * @param  string $min_or_max
	 * @return mixed
	 */
	public function get_container_price( $min_or_max = 'min', $display = false ) {
		return $this->calculate_price(
			array(
			'min_or_max' => $min_or_max,
			'calc'       => $display ? 'display' : '',
			'prop'       => 'price'
			)
		);
	}


	/**
	 * Get min/max container regular price.
	 *
	 * @since  2.0.0
	 *
	 * @param  string $min_or_max
	 * @return mixed
	 */
	public function get_container_regular_price( $min_or_max = 'min', $display = false ) {
		return $this->calculate_price(
			array(
			'min_or_max' => $min_or_max,
			'calc'       => $display ? 'display' : '',
			'prop'       => 'regular_price',
			'strict'     => true
			)
		);
	}


	/**
	 * Get min/max container price excl tax.
	 *
	 * @since  2.0.0
	 *
	 * @return mixed
	 */
	public function get_container_price_including_tax( $min_or_max = 'min', $qty = 1 ) {
		return $this->calculate_price(
			array(
			'min_or_max' => $min_or_max,
			'qty'        => $qty,
			'calc'       => 'incl_tax',
			'prop'       => 'price'
			)
		);
	}


	/**
	 * Get min/max container price excl tax.
	 *
	 * @since  2.0.0
	 *
	 * @return mixed
	 */
	public function get_container_price_excluding_tax( $min_or_max = 'min', $qty = 1 ) {
		return $this->calculate_price(
			array(
			'min_or_max' => $min_or_max,
			'qty'        => $qty,
			'calc'       => 'excl_tax',
			'prop'       => 'price'
			)
		);
	}


	/**
	 * Calculates container prices.
	 *
	 * @since  2.0.0
	 *
	 * @param  array  $args
	 * @return mixed
	 */
	public function calculate_price( $args ) {

		$min_or_max = isset( $args[ 'min_or_max' ] ) && in_array( $args[ 'min_or_max' ] , array( 'min', 'max' ) ) ? $args[ 'min_or_max' ] : 'min';
		$qty        = isset( $args[ 'qty' ] ) ? absint( $args[ 'qty' ] ) : 1;
		$price_prop = isset( $args[ 'prop' ] ) && in_array( $args[ 'prop' ] , array( 'price', 'regular_price' ) ) ? $args[ 'prop' ] : 'price';
		$price_calc = isset( $args[ 'calc' ] ) && in_array( $args[ 'calc' ] , array( 'incl_tax', 'excl_tax', 'display', '' ) ) ? $args[ 'calc' ] : '';

		if ( $this->is_priced_per_product() ) {

			$this->sync();

			$cache_key = md5(
				json_encode(
					apply_filters(
						'wc_mnm_container_prices_hash',
						array(
							'prop'       => $price_prop,
							'min_or_max' => $min_or_max,
							'calc'       => $price_calc,
							'qty'        => $qty,
						),
						$this
					)
				)
			);

			if ( isset( $this->container_price_cache[ $cache_key ] ) ) {
				$price = $this->container_price_cache[ $cache_key ];
			} else {

				$raw_price_fn = 'get_' . $min_or_max . '_raw_' . $price_prop;

				if ( '' === $this->$raw_price_fn() || INF === $this->$raw_price_fn() ) {
					$price = '';
				} else {

					$price_fn = 'get_' . $price_prop;

					$price    = wc_format_decimal(
						WC_MNM_Product_Prices::get_product_price(
							$this,
							array(
								'price' => $this->$price_fn(),
								'qty'   => $qty,
								'calc'  => $price_calc,
							)
						),
						wc_get_price_decimals()
					);

					if ( ! empty( $this->pricing_data ) ) {
						foreach ( $this->pricing_data as $child_item_id => $data ) {

							$item_qty = $qty * $data[ 'slots_filled_' . $min_or_max ];

							if ( $item_qty ) {
								$child_item = $this->get_child_item( $child_item_id );
								if ( $child_item ) {

									$price += wc_format_decimal(
										WC_MNM_Product_Prices::get_product_price(
											$child_item->get_product(),
											array(
												'price' => $data[$price_prop],
												'qty'   => $item_qty,
												'calc'  => $price_calc,
											)
										),
										wc_get_price_decimals()
									);
								}
							}
						}
					}

				}

				$this->container_price_cache[ $cache_key ] = $price;
			}
		} else {

			$price_fn = 'get_' . $price_prop;
			$price    = WC_MNM_Product_Prices::get_product_price(
				$this,
				array(
				'price' => $this->$price_fn(),
				'qty'   => $qty,
				'calc'  => $price_calc,
				)
			);
		}

		return $price;

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

			$raw_container_price_min         = $this->get_container_price( 'min', true );
			$raw_container_price_max         = $this->get_container_price( 'max', true );
			$raw_container_regular_price_min = $this->get_container_regular_price( 'min', true );
			$raw_container_regular_price_max = $this->get_container_regular_price( 'max', true );

			$container_price_data['per_product_pricing']         = $this->is_priced_per_product() ? 'yes' : 'no';

			$container_price_data['raw_container_price_min']         = (double) $raw_container_price_min;
			$container_price_data['raw_container_price_max']         = '' === $raw_container_price_max ? '' : (double) $raw_container_price_max;

			// Deprecated data keys.
			$container_price_data['raw_container_min_price']         = $container_price_data['raw_container_price_min'];
			$container_price_data['raw_container_price']             = $container_price_data['raw_container_price_max'];
			$container_price_data['raw_container_min_regular_price'] = (double) $raw_container_regular_price_min;
			$container_price_data['raw_container_regular_price']     = '' === $raw_container_regular_price_max ? '' : (double) $raw_container_regular_price_max;
			
			$container_price_data['price_string']                = '%s';
			$container_price_data['is_purchasable']              = $this->is_purchasable() ? 'yes' : 'no';
			$container_price_data['is_in_stock']                 = $this->is_in_stock() ? 'yes' : 'no';

			$container_price_data['show_free_string']            =  ( $this->is_priced_per_product() ? apply_filters( 'wc_mnm_show_free_string', false, $this ) : true ) ? 'yes' : 'no';

			$container_price_data['prices']                      = array();
			$container_price_data['regular_prices']              = array();

			$container_price_data['prices_tax']                  = array();

			$container_price_data['quantities']                  = array();

			$container_price_data['product_ids']                 = array();

			$container_price_data['is_sold_individually']        = array();

			$container_price_data['base_price']                  = $this->get_price();
			$container_price_data['base_regular_price']          = $this->get_regular_price();
			$container_price_data['base_price_tax']              = WC_MNM_Product_Prices::get_tax_ratios( $this );

			$container_price_data['price']                       = $container_price_data['base_price'];
			$container_price_data['regular_price']               = $container_price_data['base_regular_price'];
			$container_price_data['price_tax']                   = $container_price_data['base_price_tax'];

			$totals = new stdClass;

			$totals->price          = 0.0;
			$totals->regular_price  = 0.0;
			$totals->price_incl_tax = 0.0;
			$totals->price_excl_tax = 0.0;

			$container_price_data['base_price_subtotals']       = $totals;
			$container_price_data['base_price_totals']          = $totals;

			$container_price_data['addons_totals']              = $totals;

			$container_price_data['subtotals']                  = $totals;
			$container_price_data['totals']                     = $totals;

			$child_items                           = $this->get_child_items();

			if ( empty( $child_items ) ) {
				return;
			}

			foreach ( $child_items as $child_item_id => $child_item ) {

				$child_product    = $child_item->get_product();
				$child_product_id = $child_product->get_id();

				// Skip any product that isn't purchasable.
				if ( ! $child_product->is_purchasable() ) {
					continue;
				}

				$container_price_data['is_sold_individually'][ $child_product_id ] = $child_product->is_sold_individually() ? 'yes' : 'no';
				$container_price_data['product_ids'][ $child_product_id ]          = $child_product_id;
				$container_price_data['prices'][ $child_product_id ]               = $child_product->get_price();
				$container_price_data['regular_prices'][ $child_product_id ]       = $child_product->get_regular_price();
				$container_price_data['prices_tax'][ $child_product_id ]           = WC_MNM_Product_Prices::get_tax_ratios( $child_product );
				$container_price_data['quantities'][ $child_product_id ]           = 0;
				$container_price_data['child_item_subtotals'][ $child_product_id ] = $totals;
				$container_price_data['child_item_totals'][ $child_product_id ]    = $totals;

			}

			$this->container_price_data = apply_filters( 'wc_mnm_container_price_data', $container_price_data, $this );

		}

		return $this->container_price_data;

	}

	/**
	 * Get the data attributes
	 * 
	 * @param array $args
	 * @return string
	 */
	public function get_data_attributes( $args = array() ) {

		$attributes = wp_parse_args(
			$args,
			array(
				'per_product_pricing' => $this->is_priced_per_product() ? 'true' :  'false',
				'container_id'        => $this->get_id(),
				'min_container_size'  => $this->get_min_container_size(),
				'max_container_size'  => $this->get_max_container_size(),
				'base_price'          => wc_get_price_to_display( $this, array( 'price' => $this->get_price() ) ),
				'base_regular_price'  => wc_get_price_to_display( $this, array( 'price' => $this->get_regular_price() ) ),
				'price_data'          => json_encode( $this->get_container_price_data() ),
				'input_name'          => wc_mnm_get_child_input_name( $this->get_id() ),
			)
		);

		/**
		 * `wc_mnm_container_data_attributes` Data attribues filter.
		 *
		 * @param  array $attributes
		 * @param  obj WC_Product_Mix_and_Match $this
		 */
		$attributes = (array) apply_filters( 'wc_mnm_container_data_attributes', wp_parse_args( $args, $attributes ), $this );

		return wc_mnm_prefix_data_attribute_keys( $attributes );

	}


	/**
	 * Get the min/max/step quantity of a child.
	 *
	 * @param  string $value options: 'min' | 'max' | 'step'
	 * @param  int $child_id
	 * @return int
	 */
	public function get_child_quantity( $value, $child_id ) {

		wc_deprecated_function( __METHOD__ . '()', '2.0.0', 'Handled at the item level. See: WC_MNM_Child_Item::get_quantity()' );

		$qty = '';

		$child_item = $this->get_child_item_by_product_id( $child_id );

		if ( $child_item ) {
			$qty = $child_item->get_quantity( $value );
		}

		return $qty;
	}


	/**
	 * Get the availability message of a child, taking its purchasable status into account.
	 *
	 * @param  int $child_id
	 * @return string
	 */
	public function get_child_availability_html( $child_id ) {

		wc_deprecated_function( __METHOD__ . '()', '2.0.0', 'Handled at the item level. See: WC_MNM_Child_Item::get_availability_html()' );

		$availability_html = '';

		$child_item = $this->get_child_item_by_product_id( $child_id );

		if ( $child_item ) {
			$availability_html = $child_item->get_availability_html();
		}

		return $availability_html;
	}


	/*
	|--------------------------------------------------------------------------
	| Sync with children.
	|--------------------------------------------------------------------------
	*/


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

		$min_raw_price                      = $this->get_price( 'sync' );
		$max_raw_price                      = $this->get_price( 'sync' );
		$min_raw_regular_price              = $this->get_regular_price( 'sync' );
		$max_raw_regular_price              = $this->get_regular_price( 'sync' );

		$child_items_stock_status           = 'outofstock';

		$items_in_stock                     = 0;
		$backorders_allowed                 = false;
		$unlimited_stock_available          = false;

		$child_items                        = $this->get_child_items();
		$min_container_size                 = $this->get_min_container_size();
		$max_container_size                 = $this->get_max_container_size();

		if ( empty( $child_items ) ) {
			$this->is_synced = true;
			return;
		}

		foreach ( $child_items as $child_item ) {

			$child_product    = $child_item->get_product();
			$child_item_id    = $child_item->get_child_item_id();

			// Skip any product that isn't purchasable.
			if ( ! $child_product->is_purchasable() ) {
				continue;
			}

			$unlimited_child_stock_available = false;
			$child_stock_available           = 0;
			$child_backorders_allowed        = false;

			// If a child is sold-individually, let's force the container to be sold-individually.
			// @todo - Ideally, the container should only be sold individually IF a sold-individually child is selected.
			if ( $child_product->is_sold_individually() ) {
				$this->set_sold_individually( true );
			}

			// Calculate how many slots this child can fill with backordered / non-backordered items.
			if ( $child_product->managing_stock() ) {

				$child_stock = $child_product->get_stock_quantity();

				if ( $child_stock > 0 ) {

					$child_stock_available = $child_stock;

					if ( $child_product->backorders_allowed() ) {
						$backorders_allowed = $child_backorders_allowed = true;
					}
				} elseif ( $child_product->backorders_allowed() ) {
					$backorders_allowed = $child_backorders_allowed = true;
				}
			} elseif ( $child_product->is_in_stock() ) {
				$unlimited_stock_available = $unlimited_child_stock_available = true;
			}

			// Set max number of slots according to stock status and max container size.
			$this->pricing_data[ $child_item_id ]['slots'] = $child_item->get_quantity( 'max' );

			// Store price and slots for the min/max price calculation.
			if ( $this->is_priced_per_product() ) {

				$this->pricing_data[ $child_item_id ]['price_raw']         = (double) $child_item->get_raw_price();
				$this->pricing_data[ $child_item_id ]['price']             = (double) $child_product->get_price();
				$this->pricing_data[ $child_item_id ]['regular_price_raw'] = (double) $child_item->get_raw_regular_price();
				$this->pricing_data[ $child_item_id ]['regular_price']     = (double) $child_product->get_regular_price();

				// Amount used up in "cheapest" config.
				$this->pricing_data[ $child_item_id ]['slots_filled_min'] = 0;
				// Amount used up in "most expensive" config.
				$this->pricing_data[ $child_item_id ]['slots_filled_max'] = 0;

				// Save sale status for parent.
				if ( $child_product->is_on_sale( 'edit' ) ) {
					$this->on_sale = true;
				}
			}

			$items_in_stock += $child_stock_available;
		}

		// Update data for container availability.
		if ( $unlimited_stock_available || $backorders_allowed || $items_in_stock >= $min_container_size ) {
			$child_items_stock_status = 'instock';
		}

		if ( ! $unlimited_stock_available && $backorders_allowed && $items_in_stock < $min_container_size ) {
			$child_items_stock_status = 'onbackorder';
		}

		$this->set_child_items_stock_status( $child_items_stock_status );

		/*-----------------------------------------------------------------------------------*/
		/*	Per Product Pricing Min/Max Prices.
		/*-----------------------------------------------------------------------------------*/

		if ( $this->is_priced_per_product() && ! empty( $this->pricing_data ) ) {

			/*-----------------------------------------------------------------------------------*/
			/*	Min Price.
			/*-----------------------------------------------------------------------------------*/

			// Slots filled so far.
			$filled_slots = 0;

			// Sort by cheapest.
			$this->pricing_data = wp_list_sort( $this->pricing_data, 'price', 'ASC', true );

			if ( 'instock' === $child_items_stock_status ) {

				// Fill slots and calculate min price.
				foreach ( $this->pricing_data as $child_item_id => $data ) {

					$slots_to_fill = $min_container_size - $filled_slots;

					$items_to_use = $this->pricing_data[ $child_item_id ]['slots_filled_min'] = $this->pricing_data[ $child_item_id ]['slots'] !== '' ? min( $this->pricing_data[ $child_item_id ]['slots'], $slots_to_fill ) : $slots_to_fill;

					$filled_slots += $items_to_use;

					$min_raw_price         += $items_to_use * $this->pricing_data[ $child_item_id ]['price_raw'];
					$min_raw_regular_price += $items_to_use * $this->pricing_data[ $child_item_id ]['regular_price_raw'];

					if ( $filled_slots >= $min_container_size ) {
						break;
					}
				}
			} else {

				// In the unlikely even that stock is insufficient, just calculate the min price from the cheapest child.
				foreach ( $this->pricing_data as $child_item_id => $data ) {
					$this->pricing_data[ $child_item_id ]['slots_filled_min'] = 0;
				}

				$cheapest_child_id   = current( array_keys( $this->pricing_data ) );
				$cheapest_child_data = current( array_values( $this->pricing_data ) );

				$this->pricing_data[ $cheapest_child_id ]['slots_filled_min'] = $min_container_size;

				$min_raw_price         += $cheapest_child_data['price_raw'] * $min_container_size;
				$min_raw_regular_price += $cheapest_child_data['regular_price_raw'] * $min_container_size;
			}

			/*-----------------------------------------------------------------------------------*/
			/*	Max Price.
			/*-----------------------------------------------------------------------------------*/

			// Slots filled so far.
			$filled_slots = 0;

			// Sort by most expensive.
			$this->pricing_data = wp_list_sort( $this->pricing_data, 'price', 'DESC', true );

			if ( 'instock' === $child_items_stock_status && $max_container_size !== '' && ! $this->is_nyp() ) {

				// Fill slots and calculate max price.
				foreach ( $this->pricing_data as $child_item_id => $data ) {

					$slots_to_fill = $max_container_size - $filled_slots;

					$items_to_use = $this->pricing_data[ $child_item_id ]['slots_filled_max'] = $this->pricing_data[ $child_item_id ]['slots'] !== '' ? min( $this->pricing_data[ $child_item_id ]['slots'], $slots_to_fill ) : $slots_to_fill;

					$filled_slots += $items_to_use;

					$max_raw_price         += $items_to_use * $this->pricing_data[ $child_item_id ]['price_raw'];
					$max_raw_regular_price += $items_to_use * $this->pricing_data[ $child_item_id ]['regular_price_raw'];

					if ( $filled_slots >= $max_container_size ) {
						break;
					}
				}
			} else {

				// In the unlikely even that stock is insufficient, just calculate the max price from the most expensive child.
				foreach ( $this->pricing_data as $child_item_id => $data ) {
					$this->pricing_data[ $child_item_id ]['slots_filled_max'] = 0;
				}

				if ( $max_container_size !== '' && ! $this->is_nyp() ) {

					$priciest_child_id   = current( array_keys( $this->pricing_data ) );
					$priciest_child_data = current( array_values( $this->pricing_data ) );

					$this->pricing_data[ $priciest_child_id ]['slots_filled_max'] = $max_container_size;

					$max_raw_price         += $priciest_child_data['price_raw'] * $max_container_size;
					$max_raw_regular_price += $priciest_child_data['regular_price_raw'] * $max_container_size;

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
		 * `wc_mnm_synced` hook.
		 *
		 * @param  obj $product WC_Product
		 */
		do_action( 'wc_mnm_synced', $this );
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
	 * @changed 2.0.0
	 *
	 * @return array {
	 *     @type string       $label        The translatable label for the icon.
	 *     @type string       $description  Text to display a longer decsription of the icon. Optional.
	 *     @type string       $image        URL to option icon.
	 * }
	 */
	public static function get_add_to_cart_form_location_options() {

		if ( is_null( self::$layout_locations_data ) ) {

			self::$layout_locations_data = array(
				'default'      => array(
					'label'       => __( 'Inline', 'woocommerce-mix-and-match-products' ),
					'description' => __( 'The add-to-cart form is displayed inside the single-product summary.', 'woocommerce-mix-and-match-products' ),
					'image'       => WC_Mix_and_Match()->plugin_url() . '/assets/images/location-inline.svg',
				),
				'after_summary' => array(
					'label'       => __( 'Full-width', 'woocommerce-mix-and-match-products' ),
					'description' => __( 'The add-to-cart form is displayed after the single-product summary. Usually allocates the entire page width for displaying form content. Note that some themes may not support this option.', 'woocommerce-mix-and-match-products' ),
					'image'       => WC_Mix_and_Match()->plugin_url() . '/assets/images/location-full.svg',
				)
			);

			self::$layout_locations_data = apply_filters( 'wc_mnm_add_to_cart_form_location_options', self::$layout_locations_data );

		}

		return self::$layout_locations_data;
	}

	/**
	 * Supported layouts.
	 *
	 * @since  1.3.0
	 * @changed 2.0.0
	 *
	 * @return array {
	 *     @type string       $label        The translatable label for the icon.
	 *     @type string       $description  Text to display a longer decsription of the icon. Optional.
	 *     @type string       $image        URL to option icon.
	 * }
	 */
	public static function get_layout_options() {

		if ( is_null( self::$layout_options_data ) ) {

			self::$layout_options_data = array(
				'tabular' => array(
					'label'       => esc_html__( 'List', 'woocommerce-mix-and-match-products' ),
					'description' => esc_html__( 'The allowed contents are displayed as a list.', 'woocommerce-mix-and-match-products' ),
					'image'       => WC_Mix_and_Match()->plugin_url() . '/assets/images/layout-list.svg',
					'mb_display'  => false, // In the product metabox, this icon is in the admin font. Set to true to print the svg directly.
				),
				'grid' => array(
					'label'       => esc_html__( 'Grid', 'woocommerce-mix-and-match-products' ),
					'description' => esc_html__( 'The allowed contents are displayed as a grid.', 'woocommerce-mix-and-match-products' ),
					'image'       => WC_Mix_and_Match()->plugin_url() . '/assets/images/layout-grid.svg',
					'mb_display'  => false,
				)
			);

			self::$layout_options_data = apply_filters( 'wc_mnm_supported_layouts', self::$layout_options_data );

		}
		return self::$layout_options_data;
	}

	/*
	|--------------------------------------------------------------------------
	| Save child items.
	--------------------------------------------------------------------------
	*/

	/**
	 * Do any extra processing needed after the actual product save
	 * (but before triggering the 'woocommerce_after_..._object_save' action)
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $state The state object that was returned by before_data_store_save_or_update.
	 */
	protected function after_data_store_save_or_update( $state ) {
		parent::after_data_store_save_or_update( $state );

		if ( $this->has_child_item_changes() ) {
			$this->save_child_items();
		}

	}

	/**
	 * Save all child items which are part of this product.
	 *
	 * @since 2.0.0
	 */
	protected function save_child_items() {

		wc_transaction_query();

		try {

			// Delete items in the delete queue.
			foreach ( $this->child_items_to_delete as $child_item ) {
				$child_item->delete();
			}
			$this->child_items_to_delete = array();

			// Add/save items.
			if ( is_array( $this->child_items ) ) {
				$menu_order = 0;
				$child_items = array_filter( $this->child_items );
				foreach ( $child_items as $item_key => $child_item ) {

					$child_item->set_container_id( $this->get_id() );
					$child_item->set_menu_order( $menu_order );

					$child_item_id = $child_item->save();

					// If ID changed (new item saved to DB)...
					if ( $child_item_id !== $child_item_id ) {
						$this->child_items[ $child_item_id ] = $child_item;
						unset( $this->child_items[ $item_key ] );
					}

					$menu_order++;
				}
			}

			// Commit all the changes
			wc_transaction_query( 'commit' );

			$this->load_defaults();

			WC_MNM_Helpers::cache_delete( $this->get_id(), 'child_items' );

		} catch ( Exception $e ) {
			wc_get_logger()->error(
				esc_html__( 'Error saving Mix and Match product child items.', 'woocommerce-mix-and-match-products' ),
				array(
					'source' => 'wc-mix-and-match-product-save',
					'product' => $this,
					'error' => $e,
				)
			);
			wc_transaction_query( 'rollback' );
		}

	}

}

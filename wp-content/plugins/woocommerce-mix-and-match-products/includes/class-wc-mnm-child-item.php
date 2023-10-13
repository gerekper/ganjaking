<?php
/**
 * Child Item class.
 *
 * @package  WooCommerce Mix and Match Products/Classes/Products
 *
 * @since   2.0.0
 * @version 2.4.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Child Item class.
 *
 * @class    WC_MNM_Child_Item
 */
class WC_MNM_Child_Item extends WC_Data {

	/**
	 * Data array, with defaults.
	 *
	 * @var array
	 */
	protected $data = array(
		'product_id'          => 0,
		'variation_id'        => 0,
		'container_id'        => 0,
		'menu_order'          => 0,
		'priced_individually' => null,
		'discount'            => null,
	);

	/**
	 * Product instance of the associated child product.
	 *
	 * @var WC_Product
	 */
	private $product;

	/**
	 * Product instance of the parent container.
	 *
	 * @var WC_Product_Mix_and_Match
	 */
	private $container;

	/**
	 * Stores meta in cache for future reads.
	 * A group must be set to to enable caching.
	 *
	 * @var string
	 */
	protected $cache_group = 'wc-mnm-child-items';

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'wc_mnm_child_item';

	/**
	 * __construct method.
	 *
	 * @param  mixed  int|array $child_item
	 * @param  WC_Product_Mix_and_Match  $container
	 */
	public function __construct( $child_item = 0, $container = false ) {

		parent::__construct( $child_item );

		if ( $child_item instanceof WC_MNM_Child_Item ) {
			$this->set_id( $child_item->get_id() );
		} elseif ( is_numeric( $child_item ) && $child_item > 0 ) {
			$this->set_id( $child_item );
		} else {
			if ( is_array( $child_item ) ) {
				$this->set_props( $child_item );
			}
			$this->set_object_read( true );
		}

		$this->data_store = WC_Data_Store::load( 'wc-mnm-child-item' );

		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}

		if ( is_object( $container ) && $this->get_container_id() === $container->get_id() ) {
			$this->container = $container;
		}
	}

	/**
	 * __call method.
	 * magically relay method to WC_Product class if possible.
	 */
	public function __call( $name, $args ) {
		if ( ! method_exists( $this, $name ) && is_callable( array( $this->get_product(), $name ) ) ) {
			return call_user_func_array( array( $this->get_product(), $name ), $args );
		}
	}

	/**
	 * Get child item ID.
	 * Returns the DB item ID if it exists, or a temporary ID based on the unique product ID.
	 *
	 * @return mixed int|"product-ID", ex: "product-99"
	 */
	public function get_child_item_id() {
		return $this->get_id() ? $this->get_id() : 'product-' . ( $this->get_variation_id() ? $this->get_variation_id() : $this->get_product_id() );
	}

	/**
	 * Get the ID of the associated product object.
	 *
	 * @param string $context
	 * @return int
	 */
	public function get_product_id( $context = 'view' ) {
		return $this->get_prop( 'product_id' );
	}

	/**
	 * Get the ID of the associated variation object.
	 *
	 * @param string $context
	 * @return int
	 */
	public function get_variation_id( $context = 'view' ) {
		return $this->get_prop( 'variation_id' );
	}

	/**
	 * Get the ID of the parent container.
	 *
	 * @param string $context
	 * @return int
	 */
	public function get_container_id( $context = 'view' ) {
		return $this->get_prop( 'container_id' );
	}

	/**
	 * Get the menu order.
	 *
	 * @param string $context
	 * @return int
	 */
	public function get_menu_order( $context = 'view' ) {
		return $this->get_prop( 'menu_order' );
	}

	/**
	 * Get the discount - Currently inherited from the parent container.
	 *
	 * @param string $context
	 * @return string|float
	 */
	public function get_discount( $context = 'view' ) {

		if ( null === $this->get_prop( 'discount' ) ) {
			$this->set_prop( 'discount', is_callable( array( $this->get_container(), 'get_discount' ) ) ? $this->get_container()->get_discount( $context ) : 0.0 );
		}

		return $this->get_prop( 'discount' );
	}

	/**
	 * Get priced individually - Currently inherited from the parent container.
	 *
	 * @param string $context
	 * @return string|float
	 */
	public function get_priced_individually( $context = 'view' ) {

		if ( null === $this->get_prop( 'priced_individually' ) ) {
			$this->set_prop( 'priced_individually', is_callable( array( $this->get_container(), 'is_priced_per_product' ) ) ? $this->get_container()->is_priced_per_product( $context ) : false );
		}

		return $this->get_prop( 'priced_individually' );
	}


	/**
	 * Returns the child item's product.
	 *
	 * @return WC_Product|false - Can return false if this product does not exist.
	 */
	public function get_product() {

		if ( is_null( $this->product ) ) {

			$this->product = wc_get_product( $this->get_variation_id() ? $this->get_variation_id() : $this->get_product_id() );

			if ( $this->product ) {

				// Store the item as a property on the product object.
				$this->product->mnm_child_item = $this;

				// Maybe apply discounts.
				if ( 'props' === WC_MNM_Product_Prices::get_discount_method() ) {
					$this->set_price_props();
				}
			}
		}

		// Backcompatibility.
		if ( has_filter( 'woocommerce_mnm_get_child' ) ) {
			wc_deprecated_function( 'woocommerce_mnm_get_child', '2.0.0', 'wc_mnm_child_item_product, nb: 2nd parameter will now be WC_MNM_Child_Item object.' );
			/**
			 * Individual child product.
			 *
			 * @param  obj WC_Product $child_product The child product or variation.
			 * @param  obj WC_Product_Mix_and_Match                          $container
			*/
			$this->product = apply_filters( 'woocommerce_mnm_get_child', $this->product, $container );
		}

		/**
		 * Individual child product.
		 *
		 * @param  false|WC_Product $child_product The child product or variation.
		 * @param  obj WC_MNM_Child_Item  $this
		*/
		return apply_filters( 'wc_mnm_child_item_product', $this->product, $this );
	}

	/**
	 * Get child product price after discount, price filters excluded.
	 *
	 * @param  mixed  $product
	 * @return mixed
	 */
	public function get_raw_price( $product = false ) {

		if ( ! $product ) {
			$product = $this->get_product();
		}

		$price = $product->get_price( 'edit' );

		if ( '' === $price ) {
			return $price;
		}

		if ( ! $this->is_priced_individually() ) {
			return 0;
		}

		$price = WC_MNM_Product_Prices::get_discounted_price( $this->is_discounted_from_regular_price() ? $product->get_regular_price() : $price, $this->get_discount() );

		/**
		 * 'wc_mnm_child_item_raw_price' raw price filter.
		 *
		 * @param  mixed          $price
		 * @param  WC_Child_Item  $this
		 */
		return apply_filters( 'wc_mnm_child_item_raw_price', $price, $this );
	}

	/**
	 * Get child product regular price before discounts, price filters excluded.
	 *
	 * @param  mixed  $product
	 * @return mixed
	 */
	public function get_raw_regular_price( $product = false ) {

		if ( ! $product ) {
			$product = $this->get_product();
		}

		$regular_price = $product->get_regular_price( 'edit' );

		if ( ! $this->is_priced_individually() ) {
			return 0;
		}

		return empty( $regular_price ) ? $product->get_price( 'edit' ) : $regular_price;
	}


	/**
	 * Returns the parent.
	 *
	 * @return WC_Product_Mix_and_Match|false
	 */
	public function get_container() {
		if ( is_null( $this->container ) ) {
			$this->container = wc_get_product( $this->get_container_id() );
		}
		return $this->container;
	}


	/**
	 * Returns item's data attributes.
	 *
	 * @return array
	 */
	public function get_data_attributes() {
		$is_priced_per_product = $this->get_container()->is_priced_per_product();

		$product = $this->get_product();

		$atts = array(
			'child_item_id'  => $this->get_id(),
			'mnm_item_id'    => $product->get_id(), // Deprecated.
			'child_id'       => $product->get_id(),
			'regular_price'  => $is_priced_per_product ? wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ) : 0,
			'price'          => $is_priced_per_product ? wc_get_price_to_display( $product, array( 'price' => $product->get_price() ) ) : 0,
			'price_incl_tax' => $is_priced_per_product ? wc_get_price_including_tax( $product, array( 'price' => $product->get_price() ) ) : 0,
			'price_excl_tax' => $is_priced_per_product ? wc_get_price_excluding_tax( $product, array( 'price' => $product->get_price() ) ) : 0,
			'max_stock'      => $product->get_max_purchase_quantity(),
		);

		/**
		 * Data attributes.
		 *
		 * @param array - The attributes that will print in the opening template.
		 * @param obj $child_item WC_MNM_Child_Item the child item class.
		 * @since  2.0.0
		 */
		$attributes = (array) apply_filters( 'wc_mnm_child_item_data_attributes', $atts, $this );

		return wc_mnm_prefix_data_attribute_keys( $attributes );
	}

	/**
	 * Item min/max/step quantity.
	 *
	 * @param  string  $type
	 * @return mixed null|int
	 */
	public function get_quantity( $type = 'value' ) {

		if ( 'min' === $type ) {
			$qty = 0;
		} elseif ( 'step' === $type ) {
			$qty = 1;
		} elseif ( 'max' === $type ) {
			$child_max     = $this->get_product()->get_max_purchase_quantity();
			$container_max = $this->get_container()->get_max_container_size();

			if ( $child_max > 0 ) {
				$qty = $container_max ? min( $child_max, $container_max ) : $child_max;
			} else {
				$qty = $container_max;
			}
		} else {
			$child_id   = $this->get_product()->get_id();
			$input_name = $this->get_input_name( false );

			if ( $this->get_quantity( 'min' ) === $this->get_quantity( 'max' ) ) {
				$qty = $this->get_quantity( 'min' );
			} elseif ( isset( $_REQUEST[ $input_name ] ) && ! empty( $_REQUEST[ $input_name ][ $child_id ] ) ) {
				$qty = intval( $_REQUEST[ $input_name ][ $child_id ] );
			} else {
				$qty = $this->get_quantity( 'min' ) ? $this->get_quantity( 'min' ) : '';
				$qty = apply_filters( 'wc_mnm_child_item_quantity_input_default_value', $qty, $this, $this->get_container() );

				if ( has_filter( 'woocommerce_mnm_quantity_input' ) ) {
					wc_deprecated_hook( 'woocommerce_mnm_quantity_input', '2.0.0', 'wc_mnm_child_item_quantity_input_$type: note that the 2nd parameter will be a WC_MNM_Child_Item instance.' );
					$qty = apply_filters( 'woocommerce_mnm_quantity_input', $qty, $this->get_product(), $this->get_container() );
				}
			}
		}

		if ( has_filter( 'woocommerce_mnm_quantity_input_' . $type ) ) {
			wc_deprecated_hook( 'woocommerce_mnm_quantity_input_' . $type, '2.0.0', 'wc_mnm_child_item_quantity_input_$type: note that the 2nd parameter will be a WC_MNM_Child_Item instance.' );
			$qty = apply_filters( 'woocommerce_mnm_quantity_input_' . $type, $qty, $this->get_product(), $this->get_container() );
		}

		/**
		 * Min|Max|Step quantity filter.
		 *
		 * @param  int $qty Quantity.
		 * @param  obj WC_MNM_Child_Item $this
		 * @param  obj WC_Product_Mix_and_Match $container
		 */
		$qty = apply_filters( 'wc_mnm_child_item_quantity_input_' . $type, $qty, $this, $this->get_container() );

		return '' !== $qty ? intval( $qty ) : '';
	}

	/**
	 * Get a name prefix for quantity input.
	 *
	 * @param  int $child_id - Product ID of child product.
	 * @return string
	 */
	function get_input_name( $add_child = true ) {

		$name = apply_filters( 'wc_mnm_child_item_quantity_name_prefix', '', $this ) . 'mnm_quantity';

		if ( has_filter( 'woocommerce_mnm_quantity_name_prefix' ) ) {
			wc_deprecated_hook( 'woocommerce_mnm_quantity_name_prefix', '2.0.0', 'wc_mnm_child_item_quantity_name_prefix: note that the 2nd parameter will be a WC_MNM_Child_Item instance.' );
			$name = apply_filters( 'woocommerce_mnm_quantity_name_prefix', $name, $this->get_container_id() );
		}

		if ( $add_child ) {
			$name .= sprintf( '[%d]', $this->get_product()->get_id() );
		}
		return $name;
	}

	/**
	 * Get the availability message of a child, taking its purchasable status into account.
	 *
	 * @param  string $child_id
	 * @return string
	 */
	public function get_availability_html() {

		$html = '';

		if ( $this->exists() ) {

			// If not purchasable, the stock status is of no interest.
			if ( ! $this->get_product()->is_purchasable() ) {
				$html = '<p class="unavailable">' . _x( 'Temporarily unavailable', '[Frontend]', 'woocommerce-mix-and-match-products' ) . '</p>';
			} else {
				$html = wc_get_stock_html( $this->get_product() );
			}

			if ( has_filter( 'woocommerce_mnm_availability_html' ) ) {
				wc_deprecated_hook( 'woocommerce_mnm_availability_html', '2.0.0', 'wc_mnm_child_item_quantity_name_prefix: note that the 2nd parameter will be a WC_MNM_Child_Item instance.' );
				$html = apply_filters( 'woocommerce_mnm_availability_html', $html, $this->get_product() );
			}

			/**
			 * Child item availability message.
			 *
			 * @param str $html
			 * @param obj WC_MNM_Child_Item $this
			 */
			$html = apply_filters( 'wc_mnm_child_item_availability_html', $html, $this );

		}

		return $html;
	}

	/**
	 * Item title.
	 *
	 * @since  2.2.0
	 *
	 * @return string
	 */
	public function get_title() {
		/**
		 * 'wc_mnm_child_item_title' filter.
		 *
		 * @param  string             $title
		 * @param  WC_MNM_Child_Item  $this
		 */
		return apply_filters( 'wc_mnm_child_item_title', $this->get_product()->get_title(), $this );
	}

	/**
	 * Item permalink.
	 *
	 * @since  2.2.0
	 *
	 * @return string
	 */
	public function get_permalink() {
		/**
		 * 'wc_mnm_child_item_permalink' filter.
		 *
		 * @param  string             $permalink
		 * @param  WC_MNM_Child_Item  $this
		 */
		return apply_filters( 'wc_mnm_child_item_permalink', $this->is_visible() && $this->get_product()->is_visible() ? $this->get_product()->get_permalink() : '', $this );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set child item ID.
	 *
	 * @param  int  $value
	 */
	public function set_child_item_id( $value ) {
		$this->set_id( absint( $value ) );
	}

	/**
	 * Set child product ID.
	 *
	 * @param  int  $value
	 */
	public function set_product_id( $value ) {
		$this->set_prop( 'product_id', absint( $value ) );
	}

	/**
	 * Set child variation ID.
	 *
	 * @param  int  $value
	 */
	public function set_variation_id( $value ) {
		$this->set_prop( 'variation_id', absint( $value ) );
	}

	/**
	 * Set product bundle is.
	 *
	 * @param  int  $value
	 */
	public function set_container_id( $value ) {
		$this->set_prop( 'container_id', absint( $value ) );
	}

	/**
	 * Set child item menu order.
	 *
	 * @param  int  $value
	 */
	public function set_menu_order( $value ) {
		$this->set_prop( 'menu_order', absint( $value ) );
	}

	/**
	 * Runtime application of prices to product via props.
	 */
	private function set_price_props() {

		if ( ! $this->is_priced_individually() ) {

			$this->product->set_price( 0 );
			$this->product->set_regular_price( 0 );
			$this->product->set_sale_price( '' );

		} elseif ( $this->has_discount() ) {

			$price            = $this->is_discounted_from_regular_price() ? $this->product->get_regular_price() : $this->product->get_price();
			$discounted_price = WC_MNM_Product_Prices::get_discounted_price( $price, $this->get_discount() );

			$this->product->set_price( $discounted_price );
			$this->product->set_sale_price( $discounted_price );

		}
	}

	/*
	|--------------------------------------------------------------------------
	| Conditional Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Child item exists status.
	 *
	 * @return boolean
	 */
	public function exists() {

		$exists = true;

		if ( ! $this->get_product() ) {
			$exists = false;
		}

		if ( $exists && ! is_object( $this->get_product() ) ) {
			$exists = false;
		}

		if ( $exists && ( ! is_object( $this->get_container() ) || ! ( $this->get_container() instanceof WC_Product ) ) ) {
			$exists = false;
		}

		if ( $exists ) {
			if ( 'trash' === $this->get_product()->get_status() ) {
				$exists = false;
			} elseif ( ! $this->get_product()->is_type( WC_MNM_Helpers::get_supported_product_types() ) ) {
				$exists = false;
			}
		}

		return $exists;
	}

	/**
	 * Child item visibility.
	 *
	 * @param string $context
	 * @return boolean
	 */
	public function is_visible( $context = 'view' ) {

		$visible = true;

		if ( 'view' === $context ) {
			$visible = 'publish' === $this->get_product()->get_status() || current_user_can( 'edit_product', $this->product->get_id() );

			if ( $visible && apply_filters( 'wc_mnm_hide_out_of_stock_items', 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ), $this ) && ! $this->get_product()->is_in_stock() ) {
				$visible = false;
			}

			// Add backcompatibility for `woocommerce_mnm_is_child_available` filter.
			if ( has_filter( 'woocommerce_mnm_is_child_available' ) ) {
				wc_deprecated_function( 'woocommerce_mnm_is_child_available', '2.0.0', 'wc_mnm_child_item_is_visible, nb: 2nd parameter will now be WC_MNM_Child_Item object.' );
				$visible = apply_filters( 'woocommerce_mnm_is_child_available', $visible, $this->get_product(), $this->get_container() );
			}
		}

		return 'view' === $context ? apply_filters( 'wc_mnm_child_item_is_visible', $visible, $this ) : $visible;
	}

	/**
	 * Returns whether or not the item's product price is counted towards the total.
	 *
	 * @param string $context
	 * @return bool
	 */
	public function is_priced_individually( $context = 'view' ) {
		return $this->get_priced_individually( $context );
	}

	/**
	 * Returns whether or not the item's product price is discounted.
	 *
	 * @param string $context
	 * @return cool
	 */
	public function has_discount( $context = 'view' ) {
		return $this->is_priced_individually( $context ) && $this->get_discount( $context ) > 0;
	}

	/**
	 * Returns whether or not the item's product price is discounted from regular price or sale price.
	 *
	 * @return bool
	 */
	public function is_discounted_from_regular_price() {

		$discount_from_regular = true;

		// Apply discount to regular price and not sale price.
		if ( has_filter( 'woocommerce_mnm_item_discount_from_regular' ) ) {
			wc_deprecated_function( 'woocommerce_mnm_item_discount_from_regular', '2.0.0', 'wc_mnm_child_item_discount_from_regular, nb: 2nd parameter will now be WC_MNM_Child_Item object.' );
			$discount_from_regular = apply_filters( 'woocommerce_mnm_item_discount_from_regular', $discount_from_regular, $this->get_container() );
		}

		return (bool) apply_filters( 'wc_mnm_child_item_discount_from_regular', $discount_from_regular, $this );
	}
}

<?php
/**
 * Wishlist Item class
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes\Wishlists
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Wishlist_Item' ) ) {
	/**
	 * This class describes Wishlist Item object, and it is meant to be created by YITH_WCWL_Wishlist class, via
	 * get_items method
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Wishlist_Item extends WC_Data implements ArrayAccess {

		/**
		 * Item Data array
		 *
		 * @since 3.0.0
		 * @var array
		 */
		protected $data = array(
			'wishlist_id'       => 0,
			'product_id'        => 0,
			'quantity'          => 1,
			'user_id'           => 0,
			'date_added'        => '',
			'position'          => 0,
			'original_price'    => 0,
			'original_currency' => '',
			'on_sale'           => 0,
		);

		/**
		 * Register product to avoid retrieving it more than once
		 *
		 * @var \WC_Product
		 */
		protected $product = null;

		/**
		 * Register origin wishlist ID;
		 * if item is moved to another wishlist, we can then clear origin wishlist cache
		 *
		 * @var int
		 */
		protected $origin_wishlist_id = 0;

		/**
		 * Stores meta in cache for future reads.
		 * A group must be set to to enable caching.
		 *
		 * @var string
		 */
		protected $cache_group = 'wishlist-items';

		/**
		 * Constructor.
		 *
		 * @param int|object|array $item ID to load from the DB, or YITH_WCWL_Wishlist_Item object.
		 * @throws Exception When cannot loading correct Data Store object.
		 */
		public function __construct( $item = 0 ) {
			parent::__construct( $item );

			if ( $item instanceof YITH_WCWL_Wishlist_Item ) {
				$this->set_id( $item->get_id() );
			} elseif ( is_numeric( $item ) && $item > 0 ) {
				$this->set_id( $item );
			} else {
				$this->set_object_read( true );
			}

			$this->data_store = WC_Data_Store::load( 'wishlist-item' );

			if ( $this->get_id() > 0 ) {
				$this->data_store->read( $this );
			}

			if ( $this->get_object_read() ) {
				$this->origin_wishlist_id = $this->get_wishlist_id();
			}
		}

		/* === GETTERS === */

		/**
		 * Get wishlist ID for current item
		 *
		 * @param string $context Context.
		 * @return int Wishlist ID
		 */
		public function get_wishlist_id( $context = 'view' ) {
			return (int) $this->get_prop( 'wishlist_id', $context );
		}

		/**
		 * Get origin wishlist ID for current item
		 *
		 * @return int Wishlist ID
		 */
		public function get_origin_wishlist_id() {
			return (int) $this->origin_wishlist_id;
		}

		/**
		 * Get origin product ID for current item (no WPML filtering)
		 *
		 * @param string $context Context.
		 *
		 * @return int Wishlist ID
		 */
		public function get_original_product_id( $context = 'view' ) {
			return (int) $this->get_prop( 'product_id', $context );
		}

		/**
		 * Get product ID for current item
		 *
		 * @param string $context Context.
		 * @return int Product ID
		 */
		public function get_product_id( $context = 'view' ) {
			return yit_wpml_object_id( $this->get_original_product_id( $context ), 'product', true );
		}

		/**
		 * Return product object related to current item
		 *
		 * @param string $context Context.
		 * @return \WC_Product Product
		 */
		public function get_product( $context = 'view' ) {
			if ( empty( $this->product ) ) {
				$product = wc_get_product( $this->get_product_id( $context ) );

				if ( $product ) {
					$this->product = $product;
				}
			}

			return $this->product;
		}

		/**
		 * Return price of the produce related to current item
		 *
		 * @param string $context Context.
		 * @return float
		 */
		public function get_product_price( $context = 'view' ) {
			$product = $this->get_product( $context );

			if ( ! $product ) {
				return 0;
			}

			switch ( $product->get_type() ) {
				case 'variable':
					/**
					 * Product used is a variation; we can then retrieve minimum variation price
					 *
					 * @var $product \WC_Product_Variable
					 */
					return (float) $product->get_variation_price( 'min' );
				default:
					$sale_price = $product->get_sale_price();
					return $sale_price ? (float) $sale_price : (float) $product->get_price();
			}
		}

		/**
		 * Retrieve formatted price for current item
		 *
		 * @param string $context Context.
		 * @return string Formatter price
		 */
		public function get_formatted_product_price( $context = 'view' ) {
			$product = $this->get_product( $context );

			$base_price = $product->is_type( 'variable' ) ? $product->get_variation_regular_price( 'max' ) : $product->get_price();

			if ( '' === $base_price ) {
				$formatted_price = '';
			} elseif ( ! $base_price ) {
				/**
				 * APPLY_FILTERS: yith_free_text
				 *
				 * Filter the text shown when a product in the wishlist has no price.
				 *
				 * @param string     $text    Text
				 * @param WC_Product $product Product object
				 *
				 * @return string
				 */
				$formatted_price = apply_filters( 'yith_free_text', __( 'Free!', 'yith-woocommerce-wishlist' ), $product );
			} else {
				$formatted_price = $product->get_price_html();
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_item_formatted_price
			 *
			 * Filter the formatted price for a wishlist item.
			 *
			 * @param string     $formatted_price Formatted price
			 * @param string     $base_price      Base price
			 * @param WC_Product $product         Product object
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcwl_item_formatted_price', $formatted_price, $base_price, $product );
		}

		/**
		 * Return formatted product name
		 *
		 * @param string $context Context.
		 * @return string Formatted name; empty string on failure
		 */
		public function get_formatted_product_name( $context = 'view' ) {
			$product = $this->get_product( $context );

			if ( ! $product ) {
				return '';
			}

			return $product->get_formatted_name();
		}

		/**
		 * Get quantity for current item
		 *
		 * @param string $context Context.
		 * @return int Quantity
		 */
		public function get_quantity( $context = 'view' ) {
			return max( 1, (int) $this->get_prop( 'quantity', $context ) );
		}

		/**
		 * Get user ID for current item
		 *
		 * @param string $context Context.
		 * @return int User ID
		 */
		public function get_user_id( $context = 'view' ) {
			return (int) $this->get_prop( 'user_id', $context );
		}

		/**
		 * Get user for current item
		 *
		 * @param string $context Context.
		 * @return \WP_User|bool User
		 */
		public function get_user( $context = 'view' ) {
			$user_id = (int) $this->get_prop( 'user_id', $context );

			if ( ! $user_id ) {
				return false;
			}

			return get_user_by( 'id', $user_id );
		}

		/**
		 * Get wishlist date added
		 *
		 * @param string $context Context.
		 * @return \WC_DateTime|string Wishlist date of creation
		 */
		public function get_date_added( $context = 'view' ) {
			$date_added = $this->get_prop( 'date_added', $context );

			if ( $date_added && 'view' === $context ) {
				return $date_added->date_i18n( 'Y-m-d H:i:s' );
			} else {
				return $date_added;
			}
		}

		/**
		 * Get formatted wishlist date added
		 *
		 * @param string $format Date format (if empty, WP date format will be applied).
		 * @return string Wishlist date of creation
		 */
		public function get_date_added_formatted( $format = '' ) {
			$date_added = $this->get_date_added( 'edit' );

			if ( $date_added ) {
				$format = $format ? $format : get_option( 'date_format' );
				return $date_added->date_i18n( $format );
			}

			return '';
		}

		/**
		 * Get related wishlist
		 *
		 * @return \YITH_WCWL_Wishlist|bool Wishlist object, or false on failure
		 */
		public function get_wishlist() {
			$wishlist_id = $this->get_wishlist_id();

			if ( ! $wishlist_id ) {
				return false;
			}

			return YITH_WCWL_Wishlist_Factory::get_wishlist( $wishlist_id );
		}

		/**
		 * Get related wishlist slug
		 *
		 * @return string|bool Wishlist slug, or false on failure
		 */
		public function get_wishlist_slug() {
			$wishlist = $this->get_wishlist();

			if ( ! $wishlist ) {
				return false;
			}

			return $wishlist->get_slug();
		}

		/**
		 * Get related wishlist name
		 *
		 * @return string|bool Wishlist name, or false on failure
		 */
		public function get_wishlist_name() {
			$wishlist = $this->get_wishlist();

			if ( ! $wishlist ) {
				return false;
			}

			return $wishlist->get_name();
		}

		/**
		 * Get related wishlist token
		 *
		 * @return string|bool Wishlist token, or false on failure
		 */
		public function get_wishlist_token() {
			$wishlist = $this->get_wishlist();

			if ( ! $wishlist ) {
				return false;
			}

			return $wishlist->get_token();
		}

		/**
		 * Return item position inside the list
		 *
		 * @param string $context Context.
		 * @return int Position
		 */
		public function get_position( $context = 'view' ) {
			return (int) $this->get_prop( 'position', $context );
		}

		/**
		 * Return original price
		 *
		 * @param string $context Context.
		 * @return string Original price
		 */
		public function get_original_price( $context = 'view' ) {
			$price = $this->get_prop( 'original_price', 'edit' );

			if ( 'view' === $context ) {
				return wc_price(
					$price,
					array(
						'currency' => $this->get_original_currency(),
					)
				);
			}

			return $price;
		}

		/**
		 * Return original currency
		 *
		 * @param string $context Context.
		 * @return string Original price
		 */
		public function get_original_currency( $context = 'view' ) {
			$currency = $this->get_prop( 'original_currency', 'edit' );

			if ( 'view' === $context && ! $currency ) {
				$currency = get_woocommerce_currency();
			}

			return $currency;
		}

		/**
		 * Returns a formatted HTML template for the "Price variation" label
		 *
		 * @return string HTML for the template, or empty string if price variation is not applicable to current item
		 */
		public function get_price_variation() {
			$original_currency = $this->get_original_currency( 'edit' );

			// if currency changed, makes no sense to make comparisons.
			if ( get_woocommerce_currency() !== $original_currency ) {
				return '';
			}

			$original_price = $this->get_original_price( 'edit' );

			// original price wasn't stored in the wishlist.
			if ( ! $original_price ) {
				return '';
			}

			$product       = $this->get_product();
			$current_price = $this->get_product_price();

			if ( ! $current_price || ! is_numeric( $current_price ) ) {
				return '';
			}

			$difference = $original_price - $current_price;

			/**
			 * APPLY_FILTERS: yith_wcwl_hide_price_increase
			 *
			 * Filter whether to show the price difference in the wishlist.
			 *
			 * @param bool       $show_difference   Whether to show price difference or not
			 * @param WC_Product $product           Product object
			 * @param string     $original_price    Original price
			 * @param string     $original_currency Original currency
			 * @param float      $difference        Price difference
			 *
			 * @return bool
			 */
			if ( $difference <= 0 && apply_filters( 'yith_wcwl_hide_price_increase', true, $product, $original_price, $original_currency, $difference ) ) {
				return '';
			}

			$percentage_difference = -1 * round( $difference / $original_price * 100, 2 );
			$class                 = $percentage_difference > 0 ? 'increase' : 'decrease';

			/**
			 * APPLY_FILTERS: yith_wcwl_price_variation_template
			 *
			 * Filter the HTML string to show the price difference in the wishlist.
			 *
			 * @param string $html HTML string
			 *
			 * @return string
			 */
			$template = apply_filters(
				'yith_wcwl_price_variation_template',
				sprintf(
					'<small class="price-variation %s"><span class="variation-rate">%s</span><span class="old-price">%s</span></small>',
					$class,
					// translators: 1. % of reduction/increase in price.
					_x( 'Price is %1$s%%', 'Part of the template that shows price variation since addition to list; placeholder will be replaced with a percentage', 'yith-woocommerce-wishlist' ),
					// translators: 2: original product price.
					_x( '(Was %2$s when added  in list)', 'Part of the template that shows price variation since addition to list; placeholder will be replaced with a price', 'yith-woocommerce-wishlist' )
				),
				$class,
				$percentage_difference,
				$original_price,
				$original_currency,
				$this
			);
			$template = sprintf( $template, $percentage_difference, wc_price( $original_price, array( 'currency' => $original_currency ) ) );

			return $template;
		}

		/**
		 * Return state of on_sale flag
		 * Important: this flag is used for email campaigns, and doesn't necessarily represent
		 * current on_sale status for the product
		 * Plugins checks every day to find on_sale products, and to schedule email sending
		 *
		 * @param string $context Context.
		 * @return bool Whether product was on sale during last check that plugin performed
		 */
		public function is_on_sale( $context = 'view' ) {
			return (bool) $this->get_prop( 'on_sale', $context );
		}

		/**
		 * Returns url to remove item from wishlist
		 *
		 * @return string Remove url.
		 */
		public function get_remove_url() {
			$base_url = $this->get_wishlist()->get_url();

			/**
			 * APPLY_FILTERS: yith_wcwl_wishlist_item_remove_url
			 *
			 * Filter the URL to remove an item from the wishlist.
			 *
			 * @param string $url URL to remove item from the wishlist
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcwl_wishlist_item_remove_url', wp_nonce_url( add_query_arg( 'remove_from_wishlist', $this->get_product_id(), $base_url ), 'remove_from_wishlist' ), $this );
		}

		/**
		 * Get product availability class
		 *
		 * @param string $context Context of the operation.
		 * @return string Availability class.
		 */
		public function get_stock_status( $context = 'view' ) {
			$product = $this->get_product( $context );

			if ( ! $product ) {
				return false;
			}

			$availability = $product->get_availability();
			$stock_status = isset( $availability['class'] ) ? $availability['class'] : false;

			return $stock_status;
		}

		/**
		 * Checks whether product is purchasable or not
		 *
		 * @param string $context Context of the operation.
		 * @return bool Whether product is purchasable or not
		 */
		public function is_purchasable( $context = 'view' ) {
			$product = $this->get_product( $context );

			if ( ! $product ) {
				return false;
			}

			return $product->is_purchasable();
		}

		/* === SETTERS === */

		/**
		 * Set wishlist ID for current item
		 *
		 * @param int $wishlist_id Wishlist ID.
		 */
		public function set_wishlist_id( $wishlist_id ) {
			$this->set_prop( 'wishlist_id', $wishlist_id );

			$wishlist = yith_wcwl_get_wishlist( $wishlist_id );

			if ( $wishlist && $this->get_user_id() !== $wishlist->get_user_id() ) {
				$this->set_user_id( $wishlist->get_user_id() );
			}
		}

		/**
		 * Set product ID for current item
		 *
		 * @param int $product_id Product ID.
		 */
		public function set_product_id( $product_id ) {
			$product_id = yith_wcwl_object_id( $product_id, 'product', true, 'default' );

			if ( ! empty( $this->product ) ) {
				$this->product = null;
			}

			$this->set_prop( 'product_id', $product_id );
		}

		/**
		 * Set quantity for current item
		 *
		 * @param int $quantity Quantity.
		 */
		public function set_quantity( $quantity ) {
			$this->set_prop( 'quantity', $quantity );
		}

		/**
		 * Set user ID for current item
		 *
		 * @param int $user_id User ID.
		 */
		public function set_user_id( $user_id ) {
			$this->set_prop( 'user_id', $user_id );
		}

		/**
		 * Set date added for current item
		 *
		 * @param int $date_added Date added.
		 */
		public function set_date_added( $date_added ) {
			$this->set_date_prop( 'date_added', $date_added );
		}

		/**
		 * Set position in wishlist for current item
		 *
		 * @param int $position Position.
		 */
		public function set_position( $position ) {
			$this->set_prop( 'position', (int) $position );
		}

		/**
		 * Set original price
		 *
		 * @param double $original_price Price.
		 */
		public function set_original_price( $original_price ) {
			$this->set_prop( 'original_price', $original_price );
		}

		/**
		 * Set original currency
		 *
		 * @param string $original_currency Currency.
		 */
		public function set_original_currency( $original_currency ) {
			$this->set_prop( 'original_currency', $original_currency );
		}

		/**
		 * Set on sale value
		 *
		 * @param bool $on_sale Whether product was found as on sale.
		 * @return void
		 */
		public function set_on_sale( $on_sale ) {
			if ( $this->get_object_read() && $on_sale && $this->is_on_sale() !== $on_sale ) {
				/**
				 * DO_ACTION: yith_wcwl_item_is_on_sale
				 *
				 * Allows to fire some action when a wishlist item is set on sale.
				 *
				 * @param YITH_WCWL_Wishlist_Item $wishlist_item Wishlist item object
				 */
				do_action( 'yith_wcwl_item_is_on_sale', $this );
			}

			$this->set_prop( 'on_sale', $on_sale );
		}

		/* === ARRAY ACCESS METHODS === */

		/**
		 * OffsetSet for ArrayAccess.
		 *
		 * @param string $offset Offset.
		 * @param mixed  $value  Value.
		 */
		#[\ReturnTypeWillChange]
		public function offsetSet( $offset, $value ) {
			$offset = $this->map_legacy_offsets( $offset );

			if ( array_key_exists( $offset, $this->data ) ) {
				$setter = "set_$offset";
				if ( is_callable( array( $this, $setter ) ) ) {
					$this->$setter( $value );
				}
			}
		}

		/**
		 * OffsetUnset for ArrayAccess.
		 *
		 * @param string $offset Offset.
		 */
		#[\ReturnTypeWillChange]
		public function offsetUnset( $offset ) {
			$offset = $this->map_legacy_offsets( $offset );

			if ( array_key_exists( $offset, $this->data ) ) {
				unset( $this->data[ $offset ] );
			}

			if ( array_key_exists( $offset, $this->changes ) ) {
				unset( $this->changes[ $offset ] );
			}
		}

		/**
		 * OffsetExists for ArrayAccess.
		 *
		 * @param string $offset Offset.
		 * @return bool
		 */
		#[\ReturnTypeWillChange]
		public function offsetExists( $offset ) {
			$offset = $this->map_legacy_offsets( $offset );

			$getter = "get_$offset";
			if ( is_callable( array( $this, $getter ) ) ) {
				return true;
			}

			return false;
		}

		/**
		 * OffsetGet for ArrayAccess.
		 *
		 * @param string $offset Offset.
		 * @return mixed
		 */
		#[\ReturnTypeWillChange]
		public function offsetGet( $offset ) {
			$offset = $this->map_legacy_offsets( $offset );

			$getter = "get_$offset";
			if ( is_callable( array( $this, $getter ) ) ) {
				return $this->$getter();
			}

			return null;
		}

		/**
		 * Map legacy indexes to new properties, for ArrayAccess
		 *
		 * @param string $offset Offset to search.
		 * @return string Mapped offset
		 */
		protected function map_legacy_offsets( $offset ) {
			$legacy_offset = $offset;

			if ( 'prod_id' === $offset ) {
				$offset = 'product_id';
			} elseif ( 'dateadded' === $offset ) {
				$offset = 'date_added';
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_wishlist_item_map_legacy_offsets
			 *
			 * Filter the wishlist item legacy offsets to index to new properties.
			 *
			 * @param string $offset        Offset to search
			 * @param string $legacy_offset Legacy offset
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcwl_wishlist_item_map_legacy_offsets', $offset, $legacy_offset );
		}
	}
}

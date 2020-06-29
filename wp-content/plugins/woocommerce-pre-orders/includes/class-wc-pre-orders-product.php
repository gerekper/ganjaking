<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package   WC_Pre_Orders/Product
 * @author    WooThemes
 * @copyright Copyright (c) 2013, WooThemes
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) )  {
	exit; // Exit if accessed directly
}

/**
 * Pre-Orders Product class
 *
 * Customizes the functionality and display of simple/variable products to support Pre-Orders
 *
 * @since 1.0
 */
class WC_Pre_Orders_Product {

	/**
	 * Adds needed hooks / filters
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// Add an optional pre-order product message after single product price on the single product page
		add_action( 'woocommerce_single_product_summary', array( $this, 'add_pre_order_product_message' ), 11 );

		// Add an optional pre-order product message before the 'add to cart' button on the product shop loop page
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'add_pre_order_product_message' ), 11 );

		// WooCommerce Blocks does not use `woocommerce_after_shop_loop_item_title` so we need to add the message with the blocks filter.
		add_filter( 'woocommerce_blocks_product_grid_item_html', array( $this, 'update_blocks_product_grid' ), 10, 3 );

		// Change the product availability text.
		add_filter( 'woocommerce_get_availability', array( $this, 'modify_availability_text' ), 10, 2 );

		// 2.1 Filters.
		add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'modify_add_to_cart_button_text' ), 10 , 2 );
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'modify_add_to_cart_button_text' ), 10 , 2 );

		// Automatically cancel a pre-order when product is trashed.
		add_action( 'wp_trash_post', array( $this, 'maybe_cancel_pre_order_product_trashed' ) );
	}

	/**
	 * Add 'Available XX/XX/XXXX' message to product lists in woocommerce blocks.
	 *
	 * @param string $html Upstream html.
	 * @param stdClass $data Data to render.
	 * @param WC_Product $product Current Product.
	 *
	 * @return string
	 */
	public function update_blocks_product_grid( $html, $data, $product ) {
		$pre_order_message = $this->get_pre_order_product_message( $product, true );

		return "<li class=\"wc-block-grid__product\">
				<a href=\"{$data->permalink}\" class=\"wc-block-grid__product-link\">
					{$data->image}
					{$data->title}
				</a>
				{$data->badge}
				{$data->price}
				{$data->rating}
				{$pre_order_message}
				{$data->button}
			</li>";
	}

	/**
	 * Add a customizable message to product's on the shop loop page and / or on the single product page immediately
	 * after the price
	 *
	 * @since 1.0
	 */
	public function add_pre_order_product_message() {
		global $product;

		$product_grid = 'woocommerce_after_shop_loop_item_title' === current_filter();

		$message = $this->get_pre_order_product_message( $product, $product_grid );

		echo wp_kses_post( $message );
	}

	/**
	 * Get message of when product will be available.
	 *
	 * @param WC_Product $product Current product.
	 * @param bool $product_grid We are currently displaying a grid of products.
	 *
	 * @return string
	 */
	private function get_pre_order_product_message( $product, $product_grid = false ) {
		// Only modify products with pre-orders enabled
		if ( ! self::product_can_be_pre_ordered( $product ) ) {
			return '';
		}

		// Get custom message
		if ( $product_grid ) {
			$message = get_option( 'wc_pre_orders_shop_loop_product_message' );
		} else {
			$message = get_option( 'wc_pre_orders_single_product_message' );
		}

		// Bail if none available
		if ( ! $message ) {
			return '';
		}

		// Add localized availability date if needed
		$message = str_replace( '{availability_date}', $this->get_localized_availability_date( $product ), $message );

		// Add localized availability time if needed
		$message = str_replace( '{availability_time}', $this->get_localized_availability_time( $product ), $message );

		$message = apply_filters( 'wc_pre_orders_product_message', $message, $product );

		$message =  '<span class="availability_date">' . $message . '</span>';

		return $message;
	}

	/**
	 * Modifies the add to cart button text on product loop page & single product page
	 *
	 * @since 1.0
	 * @param string $default_text default add to cart button text
	 * @param WC_Product $product
	 * @return string
	 */
	public function modify_add_to_cart_button_text( $default_text, $product ) {
		// Only modify products with pre-orders enabled
		if ( ! self::product_can_be_pre_ordered( $product ) ) {
			return $default_text;
		}

		// Get custom text if set
		$text = get_option( 'wc_pre_orders_add_to_cart_button_text' );

		if ( $text ) {
			return $text;
		} else {
			return $default_text;
		}
	}

	/**
	 * Modify availability text
	 *
	 * @param  array      $data
	 * @param  WC_Product $product
	 *
	 * @return array
	 */
	public function modify_availability_text( $data, $product ) {
		if ( self::product_can_be_pre_ordered( $product ) ) {
			$availability = $class = '';

			if ( $product->managing_stock() ) {
				if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
					$product_total_stock = $product->get_total_stock();
				} else {
					if ( sizeof( $product->get_children() ) > 0 ) {
						$product_total_stock = max( 0, $product->get_stock_quantity() );

						foreach ( $product->get_children() as $child_id ) {
							if ( 'yes' === get_post_meta( $child_id, '_manage_stock', true ) ) {
								$stock = get_post_meta( $child_id, '_stock', true );
								$product_total_stock += max( 0, wc_stock_amount( $stock ) );
							}
						}
					} else {
						$product_total_stock = $product->get_stock_quantity();
					}

					$product_total_stock = wc_stock_amount( $product_total_stock );
				}

				if ( $product->is_in_stock() && $product_total_stock > get_option( 'woocommerce_notify_no_stock_amount' ) ) {
					switch ( get_option( 'woocommerce_stock_format' ) ) {
						case 'no_amount' :
							$availability = __( 'Available for pre-ordering', 'wc-pre-orders' );
						break;
						case 'low_amount' :
							if ( $product_total_stock <= get_option( 'woocommerce_notify_low_stock_amount' ) ) {
								/* translators: 1: product total stock */
								$availability = sprintf( __( 'Only %s left available for pre-ordering', 'wc-pre-orders' ), $product_total_stock );

								if ( $product->backorders_allowed() && $product->backorders_require_notification() ) {
									$availability .= ' ' . __( '(can be backordered)', 'wc-pre-orders' );
								}
							} else {
								$availability = __( 'Available for pre-ordering', 'wc-pre-orders' );
							}
						break;

						default :
							/* translators: 1: product total stock */
							$availability = sprintf( __( '%s available for pre-ordering', 'wc-pre-orders' ), $product_total_stock );

							if ( $product->backorders_allowed() && $product->backorders_require_notification() ) {
								$availability .= ' ' . __( '(can be backordered)', 'wc-pre-orders' );
							}
						break;
					}

					$class = 'in-stock';
				} elseif ( $product->backorders_allowed() && $product->backorders_require_notification() ) {
					$availability = __( 'Available on backorder', 'wc-pre-orders' );
					$class        = 'available-on-backorder';
				} elseif ( $product->backorders_allowed() ) {
					$availability = __( 'Available for pre-ordering', 'wc-pre-orders' );
					$class        = 'in-stock';
				} else {
					$availability = __( 'No longer available for pre-ordering', 'wc-pre-orders' );
					$class        = 'out-of-stock';
				}
			} elseif ( ! $product->is_in_stock() ) {
				$availability = __( 'No longer available for pre-ordering', 'wc-pre-orders' );
				$class        = 'out-of-stock';
			}

			$data = array( 'availability' => $availability, 'class' => $class );
		}

		return $data;
	}

	/**
	 * Checks if a given product can be pre-ordered by verifying pre-orders are enabled for it
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return bool true if product can be pre-ordered, false otherwise
	 */
	public static function product_can_be_pre_ordered( $product ) {
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$product_id = $product->is_type( 'variation' ) && version_compare( WC_VERSION, '3.0', '>=' ) ? $product->get_parent_id() : $product->get_id();

		return is_object( $product ) && 'yes' === get_post_meta( $product_id, '_wc_pre_orders_enabled', true );
	}

	/**
	 * Checks if a given product has active pre-orders
	 *
	 * @since 1.0.0
	 * @version 1.5.1
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return bool true if product can be pre-ordered, false otherwise
	 */
	public static function product_has_active_pre_orders( $product ) {
		global $wpdb;

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$order_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT ID
			FROM {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS items ON posts.ID = items.order_id
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS item_meta ON items.order_item_id = item_meta.order_item_id
			LEFT JOIN {$wpdb->postmeta} AS post_meta ON items.order_id = post_meta.post_id
			WHERE
				items.order_item_type = 'line_item' AND
				item_meta.meta_key = '_product_id' AND
				item_meta.meta_value = '%s' AND
				post_meta.meta_key = '_wc_pre_orders_status' AND
				post_meta.meta_value = 'active'
			", $product->get_id()
			)
		);

		return ( ! empty( $order_ids ) );
	}

	/**
	 * Checks if a given pre-order-enabled product is charged upon release
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return bool true if pre-order is charged upon release, false otherwise
	 */
	public static function product_is_charged_upon_release( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		return 'upon_release' === get_post_meta( $product->is_type( 'variation' ) && version_compare( WC_VERSION, '3.0', '>=' ) ? $product->get_parent_id() : $product->get_id(), '_wc_pre_orders_when_to_charge', true );
	}

	/**
	 * Checks if a given pre-order-enabled product is charged upfront
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return bool true if pre-order is charged upfront, false otherwise
	 */
	public static function product_is_charged_upfront( $product ) {
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		return 'upfront' === get_post_meta( $product->is_type( 'variation' ) && version_compare( WC_VERSION, '3.0', '>=' ) ? $product->get_parent_id() : $product->get_id(), '_wc_pre_orders_when_to_charge', true );
	}

	/**
	 * Gets the pre-order fee for a given product
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return string the pre-order fee amount
	 */
	public static function get_pre_order_fee( $product ) {
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		return get_post_meta( $product->get_id(), '_wc_pre_orders_fee', true );
	}

	/**
	 * Gets the tax status of a pre-order fee by checking the tax status of the product
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return bool true if the pre-order fee is taxable, false otherwise
	 */
	public static function get_pre_order_fee_tax_status( $product ) {
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		return 'taxable' === $product->get_tax_status();
	}

	/**
	 * Gets the availability date of the product localized to the site's date format
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @param string $none_text optional text to return if there is no availability datetime set
	 * @return string the formatted availability date
	 */
	public static function get_localized_availability_date( $product, $none_text = '' ) {
		if ( '' === $none_text ) {
			$none_text = __( 'at a future date', 'wc-pre-orders' );
		}

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$timestamp = self::get_localized_availability_datetime_timestamp( $product );

		if ( ! $timestamp ) {
			return $none_text;
		}

		return apply_filters( 'wc_pre_orders_localized_availability_date', date_i18n( wc_date_format(), $timestamp ), $product, $none_text );
	}

	/**
	 * Gets the availability time of the product formatted according to the site's time format and timezone
	 *
	 * @since 1.0
	 * @param object|int $product preferably the product object, or product ID if object is inconvenient to provide
	 * @return string the formatted availability time
	 */
	public static function get_localized_availability_time( $product ) {
		$timestamp = self::get_localized_availability_datetime_timestamp( $product );

		$localized_time = date( get_option( 'time_format' ), $timestamp );

		return apply_filters( 'wc_pre_orders_localized_availability_time', $localized_time, $timestamp );
	}

	/**
	 * Gets the availability timestamp of the product localized to the configured
	 * timezone
	 *
	 * @param WC_Product|int $product the product object or post identifier
	 * @return int the timestamp, localized to the current timezone
	 */
	public static function get_localized_availability_datetime_timestamp( $product ) {
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product || ! $timestamp = get_post_meta( $product->is_type( 'variation' ) && version_compare( WC_VERSION, '3.0', '>=' ) ? $product->get_parent_id() : $product->get_id(), '_wc_pre_orders_availability_datetime', true ) ) {
			return 0;
		}

		try {
			// Get datetime object from unix timestamp
			$datetime = new DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );

			// Set the timezone to the site timezone
			$datetime->setTimezone( new DateTimeZone( self::get_wp_timezone_string() ) );

			// Return the unix timestamp adjusted to reflect the site's timezone
			return $timestamp + $datetime->getOffset();

		} catch ( Exception $e ) {
			global $wc_pre_orders;

			// Log error
			$wc_pre_orders->log( $e->getMessage() );
			return 0;
		}
	}

	/**
	 * Returns the timezone string for a site, even if it's set to a UTC offset
	 *
	 * Adapted from http://www.php.net/manual/en/function.timezone-name-from-abbr.php#89155
	 *
	 * @since 1.0
	 * @return string valid PHP timezone string
	 */
	public static function get_wp_timezone_string() {

		// If site timezone string exists, return it
		if ( $timezone = get_option( 'timezone_string' ) ) {
			return $timezone;
		}

		// Get UTC offset, if it isn't set then return UTC
		if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) ) {
			return 'UTC';
		}

		// Adjust UTC offset from hours to seconds
		$utc_offset *= 3600;

		// Attempt to guess the timezone string from the UTC offset
		$timezone = timezone_name_from_abbr( '', $utc_offset );

		// Last try, guess timezone string manually
		if ( false === $timezone ) {

			$is_dst = date( 'I' );

			foreach ( timezone_abbreviations_list() as $abbr ) {
				foreach ( $abbr as $city ) {
					if ( $city['dst'] == $is_dst && $city['offset'] == $utc_offset && ! empty( $city['timezone_id'] ) ) {
						return $city['timezone_id'];
					}
				}
			}
		}

		// Fallback to UTC offset
		return $utc_offset / 3600;
	}

	/**
	 * Maybe cancel pre order when product is trashed
	 *
	 * @param int $product_id Product ID
	 * @return void
	 */
	public function maybe_cancel_pre_order_product_trashed( $product_id ) {
		global $wpdb;

		$orders = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT order_items.order_id
				FROM {$wpdb->prefix}woocommerce_order_items AS order_items
					LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_itemmeta
					ON order_itemmeta.order_item_id = order_items.order_item_id
				WHERE order_itemmeta.meta_key = '_product_id'
				AND order_itemmeta.meta_value = %d
			", $product_id )
		);

		foreach ( $orders as $order_data ) {
			$order = new WC_Order( $order_data->order_id );

			if ( WC_Pre_Orders_Order::order_contains_pre_order( $order ) && WC_Pre_Orders_Manager::can_pre_order_be_changed_to( 'cancelled', $order ) ) {
				WC_Pre_Orders_Order::update_pre_order_status( $order, 'cancelled' );
			}
		}
	}
}

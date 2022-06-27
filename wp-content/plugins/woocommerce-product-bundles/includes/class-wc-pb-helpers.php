<?php
/**
 * WC_PB_Helpers class
 *
 * @package  WooCommerce Product Bundles
 * @since    4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Bundle Helper Functions.
 *
 * @class    WC_PB_Helpers
 * @version  6.15.2
 */
class WC_PB_Helpers {

	/**
	 * Runtime cache for simple storage.
	 *
	 * @var array
	 */
	public static $cache = array();

	/**
	 * Simple runtime cache getter.
	 *
	 * @param  string  $key
	 * @param  string  $group_key
	 * @return mixed
	 */
	public static function cache_get( $key, $group_key = '' ) {

		$value = null;

		if ( defined( 'WC_PB_DEBUG_RUNTIME_CACHE' ) ) {
			return $value;
		}

		if ( $group_key ) {

			if ( $group_id = self::cache_get( $group_key . '_id' ) ) {
				$value = self::cache_get( $group_key . '_' . $group_id . '_' . $key );
			}

		} elseif ( isset( self::$cache[ $key ] ) ) {
			$value = self::$cache[ $key ];
		}

		return $value;
	}

	/**
	 * Simple runtime cache setter.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  string  $group_key
	 * @return void
	 */
	public static function cache_set( $key, $value, $group_key = '' ) {

		if ( $group_key ) {

			if ( null === ( $group_id = self::cache_get( $group_key . '_id' ) ) ) {
				$group_id = md5( $group_key );
				self::cache_set( $group_key . '_id', $group_id );
			}

			self::$cache[ $group_key . '_' . $group_id . '_' . $key ] = $value;

		} else {
			self::$cache[ $key ] = $value;
		}
	}

	/**
	 * Simple runtime cache unsetter.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  string  $group_key
	 * @return void
	 */
	public static function cache_delete( $key, $group_key = '' ) {

		if ( $group_key ) {

			if ( $group_id = self::cache_get( $group_key . '_id' ) ) {
				self::cache_delete( $group_key . '_' . $group_id . '_' . $key );
			}

		} elseif ( isset( self::$cache[ $key ] ) ) {
			unset( self::$cache[ $key ] );
		}
	}

	/**
	 * Simple runtime group cache invalidator.
	 *
	 * @since  5.7.4
	 *
	 * @param  string  $key
	 * @param  string  $group_key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function cache_invalidate( $group_key = '' ) {

		if ( '' === $group_key ) {
			self::$cache = array();
		} elseif ( $group_id = self::cache_get( $group_key . '_id' ) ) {
			$group_id = md5( $group_key . '_' . $group_id );
			self::cache_set( $group_key . '_id', $group_id );
		}
	}

	/**
	 * Runtime object prop getter.
	 *
	 * @since  6.2.4
	 *
	 * @param  object  $object
	 * @param  string  $prop
	 * @return mixed
	 */
	public static function get_runtime_prop( $object, $prop ) {
		return isset( $object->$prop ) ? $object->$prop : null;
	}

	/**
	 * Runtime object prop checker.
	 *
	 * @since  6.2.4
	 *
	 * @param  object  $object
	 * @param  string  $prop
	 * @return mixed
	 */
	public static function has_runtime_prop( $object, $prop ) {
		return isset( $object->$prop );
	}

	/**
	 * True when processing a FE request.
	 *
	 * @return boolean
	 */
	public static function is_front_end() {
		$is_fe = ( ! is_admin() ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		return $is_fe;
	}

	/**
	 * Loads variation IDs for a given variable product.
	 *
	 * @param  WC_Product_Variable|int  $product
	 * @return array
	 */
	public static function get_product_variations( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product ) {
			return false;
		}

		return $product->get_children();
	}

	/**
	 * Return a formatted product title based on id.
	 *
	 * @param  mixed  $product_id
	 * @return string
	 */
	public static function get_product_title( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product ) {
			return false;
		}

		$title = $product->get_title();
		$sku   = $product->get_sku();
		$id    = $product->get_id();

		if ( $sku ) {
			$identifier = $sku;
		} else {
			$identifier = '#' . $id;
		}

		return self::format_product_title( $title, $identifier );
	}

	/**
	 * Return a formatted product title based on variation id.
	 *
	 * @param  int     $item_id
	 * @param  string  $format
	 * @return string
	 */
	public static function get_product_variation_title( $variation, $format = 'flat' ) {

		if ( ! is_object( $variation ) ) {
			$variation = wc_get_product( $variation );
		}

		if ( ! $variation ) {
			return false;
		}

		if ( 'core' === $format || true === $format ) {

			$title = $variation->get_formatted_name();

		} else {

			$description = wc_get_formatted_variation( $variation, true );

			$title = $variation->get_title();
			$sku   = $variation->get_sku();
			$id    = $variation->get_id();

			if ( $sku ) {
				$identifier = $sku;
			} else {
				$identifier = '#' . $id;
			}

			$title = self::format_product_title( $title, $identifier, $description, true );
		}

		return $title;
	}

	/**
	 * Format a product title.
	 *
	 * @param  string   $title
	 * @param  string   $sku
	 * @param  string   $meta
	 * @param  boolean  $paren
	 * @return string
	 */
	public static function format_product_title( $title, $sku = '', $meta = '', $paren = false ) {

		if ( $sku && $meta ) {
			if ( $paren ) {
				/* translators: %1$s: Product title, %2$s: Product meta, %3$s: Product SKU */
				$title = sprintf( _x( '%1$s &ndash; %2$s (%3$s)', 'product title followed by meta and sku in parenthesis', 'woocommerce-product-bundles' ), $title, $meta, $sku );
			} else {
				/* translators: %1$s: Product sku, %2$s: Product title, %3$s: Product meta */
				$title = sprintf( _x( '%1$s &ndash; %2$s &ndash; %3$s', 'sku followed by product title and meta', 'woocommerce-product-bundles' ), $sku, $title, $meta );
			}
		} elseif ( $sku ) {
			if ( $paren ) {
				/* translators: %1$s: Product title, %2$s: Product SKU */
				$title = sprintf( _x( '%1$s (%2$s)', 'product title followed by sku in parenthesis', 'woocommerce-product-bundles' ), $title, $sku );
			} else {
				/* translators: %1$s: Product sku, %2$s: Product title */
				$title = sprintf( _x( '%1$s &ndash; %2$s', 'sku followed by product title', 'woocommerce-product-bundles' ), $sku, $title );
			}
		} elseif ( $meta ) {
			if ( $paren ) {
				/* translators: %1$s: Product title, %2$s: Product meta */
				$title = sprintf( _x( '%1$s (%2$s)', 'product title followed by meta in parenthesis', 'woocommerce-product-bundles' ), $title, $meta );
			} else {
				/* translators: %1$s: Product title, %2$s: Product meta */
				$title = sprintf( _x( '%1$s &ndash; %2$s', 'product title followed by meta', 'woocommerce-product-bundles' ), $title, $meta );
			}
		}

		return $title;
	}

	/**
	 * Format a product title incl qty, price and suffix.
	 *
	 * @param  string  $title
	 * @param  string  $qty
	 * @param  string  $price
	 * @param  string  $suffix
	 * @return string
	 */
	public static function format_product_shop_title( $title, $qty = '', $price = '', $suffix = '' ) {

		$quantity_string = '';
		$price_string    = '';
		$suffix_string   = '';

		if ( $qty ) {
			/* translators: Quantity */
			$quantity_string = sprintf( _x( ' &times; %s', 'qty string', 'woocommerce-product-bundles' ), $qty );
		}

		if ( $price ) {
			/* translators: Price */
			$price_string = sprintf( _x( ' &ndash; %s', 'price suffix', 'woocommerce-product-bundles' ), $price );
		}

		if ( $suffix ) {
			/* translators: Suffix */
			$suffix_string = sprintf( _x( ' &ndash; %s', 'suffix', 'woocommerce-product-bundles' ), $suffix );
		}

		/* translators: %1$s: Product title, %2$s: Product quantity, %3$s: Product price, %4$s: Product suffix */
		$title_string = sprintf( _x( '%1$s%2$s%3$s%4$s', 'title, quantity, price, suffix', 'woocommerce-product-bundles' ), $title, $quantity_string, $price_string, $suffix_string );

		return $title_string;
	}

	/**
	 * Comma separated list of item names, with final comma replaced by 'and'.
	 *
	 * @since  5.5.0
	 *
	 * @param  array  $items
	 * @return string
	 */
	public static function format_list_of_items( $items ) {

		$item_string = '';
		$count       = count( $items );
		$loop        = 1;

		foreach ( $items as $key => $item ) {
			if ( $count === 1 || $loop === 1 ) {
				$item_string = $item;
			} elseif ( $loop === $count ) {
				$item_string = sprintf( _x( '%1$s and %2$s', 'string list item last separator', 'woocommerce-product-bundles' ), $item_string, $item );
			} else {
				$item_string = sprintf( _x( '%1$s, %2$s', 'string list item separator', 'woocommerce-product-bundles' ), $item_string, $item );
			}
			$loop++;
		}

		return $item_string;
	}

	/**
	 * Array of allowed HTML tags per case.
	 *
	 * @since  6.1.5
	 *
	 * @param  string  $case
	 * @return array
	 */
	public static function get_allowed_html( $case ) {

		$allowed_html = array();

		switch ( $case ) {
			case 'inline':
				$allowed_html = array(

					// Formatting.
					'strong' => array(),
					'em'     => array(),
					'b'      => array(),
					'i'      => array(),
					'span'   => array(
						'class' => array()
					),

					// Links.
					'a'      => array(
						'href'   => array(),
						'target' => array()
					)
				);
				break;

			default:
				break;
		}

		return $allowed_html;
	}

	/**
	 * Get a new product instance, preserving runtime meta from another one.
	 *
	 * @since  6.3.5
	 *
	 * @param  WC_Product  $product
	 * @return WC_Product
	 */
	public static function get_product_preserving_meta( $product ) {

		$clone = wc_get_product( $product->get_id() );

		$meta_data_to_set = array();

		foreach ( $product->get_meta_data() as $meta ) {
			if ( ! isset( $meta->id ) ) {
				$meta_data_to_set[] = array(
					'key'   => $meta->key,
					'value' => $meta->value
				);
			}
		}

		foreach ( $meta_data_to_set as $meta ) {
			$clone->add_meta_data( $meta[ 'key' ], $meta[ 'value' ], true );
		}

		return $clone;
	}
}

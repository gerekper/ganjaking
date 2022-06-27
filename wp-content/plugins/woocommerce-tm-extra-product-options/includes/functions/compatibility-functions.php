<?php
/**
 * Compatibility functions
 *
 * These functions provide an interface in order to
 * get correct results for functions that are missing
 * or work differently accross various WooCommerce version.
 *
 * @package Extra Product Options/Functions
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'mb_basename' ) ) {
	/**
	 * Creates mb_basename functionality if missing.
	 *
	 * @param mixed $path The path to check.
	 * @return string
	 */
	function mb_basename( $path ) {
		$separator = ' qq ';
		$path      = preg_replace( '/[^ ]/u', $separator . '$0' . $separator, $path );
		$base      = basename( $path );
		$base      = str_replace( $separator, '', $base );

		return $base;
	}
}

if ( ! function_exists( 'themecomplete_get_product_att' ) ) {

	/**
	 * Get product get_$att method
	 *
	 * @param WC_Product $product Product object.
	 * @param string     $att The product attribute.
	 * @return mixed
	 */
	function themecomplete_get_product_att( $product, $att = '' ) {
		if ( is_callable( [ $product, 'get_' . $att ] ) ) {
			return $product->{'get_' . $att}();
		} else {
			return $product->$att;
		}
	}
}

if ( ! function_exists( 'themecomplete_get_price_excluding_tax' ) ) {

	/**
	 * Get product price excluding tax
	 * For a given product, and optionally price/qty, work out the price with tax excluded, based on store settings.
	 *
	 * @param  WC_Product $product WC_Product object.
	 * @param  array      $args    Optional arguments to pass product quantity and price.
	 *
	 * @return float
	 */
	function themecomplete_get_price_excluding_tax( $product, $args = [] ) {
		if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
			if ( empty( $args['price'] ) ) {
				$args = wp_parse_args(
					$args,
					[
						'qty'   => '',
						'price' => '',
					]
				);
				$qty  = (int) $args['qty'] ? $args['qty'] : 1;

				return apply_filters( 'woocommerce_get_price_excluding_tax', 0, $qty, $product );
			}
			if ( (float) $args['price'] < 0 ) {
				$args['price'] = - $args['price'];

				return - wc_get_price_excluding_tax( $product, $args );
			}

			return wc_get_price_excluding_tax( $product, $args );
		}
		$args  = wp_parse_args(
			$args,
			[
				'qty'   => '',
				'price' => '',
			]
		);
		$price = $args['price'] ? $args['price'] : 0;
		$qty   = $args['qty'] ? $args['qty'] : 1;

		return $product->get_price_excluding_tax( $qty, $price );
	}
}

if ( ! function_exists( 'themecomplete_get_price_including_tax' ) ) {

	/**
	 * Get product price including tax
	 * For a given product, and optionally price/qty, work out the price with tax included, based on store settings.
	 *
	 * @param  WC_Product $product WC_Product object.
	 * @param  array      $args    Optional arguments to pass product quantity and price.
	 *
	 * @return float
	 */
	function themecomplete_get_price_including_tax( $product, $args = [] ) {
		if ( function_exists( 'wc_get_price_including_tax' ) ) {
			if ( empty( $args['price'] ) ) {
				$args = wp_parse_args(
					$args,
					[
						'qty'   => '',
						'price' => '',
					]
				);
				$qty  = (int) $args['qty'] ? $args['qty'] : 1;

				return apply_filters( 'woocommerce_get_price_including_tax', 0, $qty, $product );
			}
			if ( (float) $args['price'] < 0 ) {
				$args['price'] = - $args['price'];

				return - wc_get_price_including_tax( $product, $args );
			}

			return wc_get_price_including_tax( $product, $args );
		}
		$args  = wp_parse_args(
			$args,
			[
				'qty'   => '',
				'price' => '',
			]
		);
		$price = $args['price'] ? $args['price'] : 0;
		$qty   = $args['qty'] ? $args['qty'] : 1;

		return $product->get_price_including_tax( $qty, $price );
	}
}

if ( ! function_exists( 'themecomplete_get_product_type' ) ) {

	/**
	 * Get product type
	 *
	 * @param WC_Product|null $product Product object.
	 * @return bool
	 */
	function themecomplete_get_product_type( $product = null ) {
		if ( is_object( $product ) ) {
			if ( is_callable( [ $product, 'get_type' ] ) ) {
				return $product->get_type();
			} else {
				return $product->product_type;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'themecomplete_get_id' ) ) {

	/**
	 * Get product ID
	 *
	 * @param WC_Product $product Product object.
	 * @return int
	 */
	function themecomplete_get_id( $product ) {
		if ( is_callable( [ $product, 'get_id' ] ) && is_callable( [ $product, 'get_parent_id' ] ) ) {
			if ( 'variation' === themecomplete_get_product_type( $product ) ) {
				return $product->get_parent_id();
			}

			return $product->get_id();
		}
		if ( is_object( $product ) ) {
			return $product->id;
		}

		return 0;
	}
}

if ( ! function_exists( 'themecomplete_get_variation_id' ) ) {

	/**
	 * Get variation ID
	 *
	 * @param WC_Product $product Product object.
	 * @return int
	 */
	function themecomplete_get_variation_id( $product ) {
		if ( is_callable( [ $product, 'get_id' ] ) ) {
			return $product->get_id();
		}

		return $product->variation_id;
	}
}

if ( ! function_exists( 'themecomplete_get_tax_class' ) ) {

	/**
	 * Get product tax class
	 *
	 * @param WC_Product $product Product object.
	 * @param string     $context view, edit, or unfiltered.
	 * @return string
	 */
	function themecomplete_get_tax_class( $product, $context = false ) {
		if ( is_callable( [ $product, 'get_tax_class' ] ) ) {

			if ( false !== $context ) {
				return $product->get_tax_class( $context );
			} else {
				return $product->get_tax_class();
			}
		}

		return $product->tax_class;
	}
}

if ( ! function_exists( 'themecomplete_get_woocommerce_currency' ) ) {

	/**
	 * Get WooCommerce currency
	 *
	 * @return string
	 */
	function themecomplete_get_woocommerce_currency() {
		$currency = get_woocommerce_currency();
		if ( class_exists( 'WooCommerce_All_in_One_Currency_Converter_Main' ) ) {
			global $woocommerce_all_in_one_currency_converter;
			$currency = $woocommerce_all_in_one_currency_converter->settings->session_currency;
		}

		return $currency;

	}
}

if ( ! function_exists( 'themecomplete_get_post_meta' ) ) {

	/**
	 * Get post meta
	 *
	 * @param integer $post_id The id of the post to search.
	 * @param string  $meta_key The meta key to retrieve.
	 * @param boolean $single Whether to return a single value.
	 *
	 * @return mixed
	 */
	function themecomplete_get_post_meta( $post_id, $meta_key = '', $single = false ) {
		$meta = false;

		if ( $post_id instanceof WC_PRODUCT ) {
			return $post_id->get_meta( $meta_key, $single );
		}

		if ( $post_id instanceof WP_Post || ( is_object( $post_id ) && isset( $post_id->ID ) && isset( $post_id->post_type ) && 'product' !== $post_id->post_type ) ) {
			return get_post_meta( $post_id->ID, $meta_key, $single );
		}

		if ( function_exists( 'wc_get_product' ) && is_numeric( $post_id ) ) {
			$product = wc_get_product( $post_id );
			if ( is_object( $product ) ) {
				$meta = $product->get_meta( $meta_key, $single );
			} else {
				$meta = get_post_meta( $post_id, $meta_key, $single );
			}
		} else {
			$meta = get_post_meta( $post_id, $meta_key, $single );
		}
		// needed in some rare occassions where WordPress doesn't unserialize the data.
		if ( $single ) {
			$meta = maybe_unserialize( $meta );
		}

		return $meta;
	}
}

if ( ! function_exists( 'themecomplete_update_post_meta' ) ) {

	/**
	 * Updates a post meta field based on the given post ID.
	 *
	 * @param integer $post_id    Post ID.
	 * @param string  $meta_key   Metadata key.
	 * @param mixed   $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param mixed   $prev_value Optional. Previous value to check before updating.
	 *                            If specified, only update existing metadata entries with
	 *                            this value. Otherwise, update all entries. Default empty.
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure or if the value passed to the function
	 *                  is the same as the one that is already in the database.
	 */
	function themecomplete_update_post_meta( $post_id, $meta_key, $meta_value, $prev_value = '' ) {

		if ( is_numeric( $post_id ) ) {
			$product = wc_get_product( $post_id );
			if ( is_object( $product ) ) {
				$product->update_meta_data( $meta_key, $meta_value );
				$product->save_meta_data();

				return true;
			} else {
				return update_post_meta( $post_id, $meta_key, $meta_value );
			}
		}

		return false;

	}
}

if ( ! function_exists( 'themecomplete_delete_post_meta' ) ) {

	/**
	 * Deletes a post meta field for the given post ID.
	 *
	 * @param integer $post_id    Post ID.
	 * @param string  $meta_key   Metadata name.
	 * @param mixed   $meta_value Optional. Metadata value. If provided,
	 *                            rows will only be removed that match the value.
	 *                            Must be serializable if non-scalar. Default empty.
	 * @return bool True on success, false on failure.
	 */
	function themecomplete_delete_post_meta( $post_id, $meta_key, $meta_value = '' ) {

		if ( is_numeric( $post_id ) ) {
			$product = wc_get_product( $post_id );
			if ( is_object( $product ) ) {
				$product->delete_meta_data( $meta_key );
				$product->save_meta_data();

				return true;
			} else {
				return delete_post_meta( $post_id, $meta_key, $meta_value );
			}
		}

		return false;

	}
}

if ( ! function_exists( 'themecomplete_add_post_meta' ) ) {

	/**
	 * Adds a meta field to the given post.
	 *
	 * @param integer $post_id    Post ID.
	 * @param string  $meta_key   Metadata name.
	 * @param mixed   $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param boolean $unique     Optional. Whether the same key should not be added.
	 *                            Default false.
	 * @return int|false Meta ID on success, false on failure.
	 */
	function themecomplete_add_post_meta( $post_id, $meta_key, $meta_value, $unique = false ) {

		if ( is_numeric( $post_id ) ) {
			$product = wc_get_product( $post_id );
			if ( is_object( $product ) ) {
				$product->add_meta_data( $meta_key, $meta_value, $unique );
				$product->save_meta_data();

				return true;
			} else {
				return add_post_meta( $post_id, $meta_key, $meta_value, $unique );
			}
		}

		return false;

	}
}

if ( ! function_exists( 'themecomplete_get_attributes' ) ) {

	/**
	 * Get product attributes
	 *
	 * @param int $post_id Post ID.
	 */
	function themecomplete_get_attributes( $post_id ) {

		$product = false;
		if ( is_numeric( $post_id ) ) {
			$product = wc_get_product( $post_id );
		} elseif ( is_object( $post_id ) ) {
			$product = $post_id;
		}

		if ( is_object( $product ) && is_callable( [ $product, 'get_attributes' ] ) ) {
			$attributes = $product->get_attributes();
		} else {
			$attributes = maybe_unserialize( themecomplete_get_post_meta( $post_id, '_product_attributes', true ) );
		}

		return $attributes;

	}
}

if ( ! function_exists( 'themecomplete_order_get_att' ) ) {

	/**
	 * Get order get_$att method
	 *
	 * @param WC_Order $order Order object.
	 * @param string   $att Method name.
	 *
	 * @return mixed
	 */
	function themecomplete_order_get_att( $order, $att ) {

		$ret        = null;
		$att_method = 'get_' . $att;

		if ( is_object( $order ) && is_callable( [ $order, $att_method ] ) ) {
			$ret = $order->$att_method();
		} else {
			$ret = $order->$att;
		}

		return $ret;

	}
}

if ( ! function_exists( 'themecomplete_get_product_from_item' ) ) {

	/**
	 * Get product from item
	 *
	 * @param WC_Order_Item $item Order Item.
	 * @param WC_Order      $order Order object.
	 *
	 * @return WC_Product
	 */
	function themecomplete_get_product_from_item( $item, $order = false ) {

		$product = false;
		if ( is_object( $item ) && is_callable( [ $item, 'get_product' ] ) ) {
			$product = $item->get_product();
		} elseif ( $order && is_object( $order ) && is_callable( [ $order, 'get_product_from_item' ] ) ) {
			$product = $order->get_product_from_item( $item );
		}

		return $product;

	}
}

if ( ! function_exists( 'themecomplete_order_get_price_excluding_tax' ) ) {

	/**
	 * Get product price in order with tax excluded
	 *
	 * @param WC_Order $order Order object.
	 * @param integer  $item_id Item ID.
	 * @param array    $args Array of args to pass.
	 */
	function themecomplete_order_get_price_excluding_tax( $order, $item_id, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'qty'   => '',
				'price' => '',
			]
		);

		$price = (float) $args['price'];
		$qty   = (float) $args['qty'];

		$prices_include_tax = themecomplete_order_get_att( $order, 'prices_include_tax' );
		$order_items        = $order->get_items();
		$order_taxes        = $order->get_taxes();

		if ( ! isset( $order_items[ $item_id ] ) ) {
			return $price;
		}

		$product = themecomplete_get_product_from_item( $order_items[ $item_id ], $order );

		if ( ! $product ) {
			return $price;
		}

		$tax_data  = empty( $legacy_order ) && wc_tax_enabled() ? maybe_unserialize( isset( $order_items[ $item_id ]['line_tax_data'] ) ? $order_items[ $item_id ]['line_tax_data'] : '' ) : false;
		$tax_price = 0;
		if ( ! empty( $tax_data ) && $prices_include_tax ) {
			$tax_based_on = get_option( 'woocommerce_tax_based_on' );
			if ( 'billing' === $tax_based_on ) {
				$country  = $order->get_billing_country();
				$state    = $order->get_billing_state();
				$postcode = $order->get_billing_postcode();
				$city     = $order->get_billing_city();
			} elseif ( 'shipping' === $tax_based_on ) {
				$country  = $order->get_shipping_country();
				$state    = $order->get_shipping_state();
				$postcode = $order->get_shipping_postcode();
				$city     = $order->get_shipping_city();
			}
			// Default to base.
			if ( 'base' === $tax_based_on || empty( $country ) ) {
				$default  = wc_get_base_location();
				$country  = $default['country'];
				$state    = $default['state'];
				$postcode = '';
				$city     = '';
			}
			$tax_class      = $order_items[ $item_id ]['tax_class'];
			$tax_rates      = WC_Tax::find_rates(
				[
					'country'   => $country,
					'state'     => $state,
					'postcode'  => $postcode,
					'city'      => $city,
					'tax_class' => $tax_class,
				]
			);
			$epo_line_taxes = WC_Tax::calc_tax( $price, $tax_rates, $prices_include_tax );

			foreach ( $order_taxes as $tax_item ) {
				$tax_item_id = $tax_item['rate_id'];
				if ( is_callable( [ $tax_item, 'get_rate_id' ] ) ) {
					$tax_item_id = $tax_item->get_rate_id();
				}
				if ( isset( $epo_line_taxes[ $tax_item_id ] ) ) {
					$tax_price = $tax_price + $epo_line_taxes[ $tax_item_id ];
				}
			}
		}

		if ( $product->is_taxable() && $prices_include_tax ) {
			$price = ( $price - $tax_price ) * $qty;
		} else {
			$price = $price * $qty;
		}

		return apply_filters( 'wc_epo_order_get_price_excluding_tax', $price, $order, $item_id, $args );
	}
}

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

if ( ! function_exists( "mb_basename" ) ) {
	function mb_basename( $path ) {
		$separator = " qq ";
		$path      = preg_replace( "/[^ ]/u", $separator . "\$0" . $separator, $path );
		$base      = basename( $path );
		$base      = str_replace( $separator, "", $base );

		return $base;
	}
}

if ( ! function_exists( 'themecomplete_attribute_orderby' ) ) {

	/**
	 * Get a product attributes orderby setting
	 *
	 * @param mixed $name
	 *
	 * @return string
	 */
	function themecomplete_attribute_orderby( $name ) {

		if ( function_exists( 'wc_attribute_orderby' ) ) {
			return wc_attribute_orderby( $name );
		}

		global $wc_product_attributes, $wpdb;

		$name = str_replace( 'pa_', '', $name );

		if ( isset( $wc_product_attributes[ 'pa_' . $name ] ) ) {
			$orderby = $wc_product_attributes[ 'pa_' . $name ]->attribute_orderby;
		} else {
			$orderby = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_orderby FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = %s;", $name ) );
		}

		return apply_filters( 'woocommerce_attribute_orderby', $orderby, $name );

	}

}

if ( ! function_exists( 'themecomplete_get_product_att' ) ) {

	/**
	 * Get product get_$att method
	 *
	 * @param $product
	 *
	 * @return mixed
	 */
	function themecomplete_get_product_att( $product, $att = "" ) {
		if ( is_callable( array( $product, 'get_' . $att ) ) ) {
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
	function themecomplete_get_price_excluding_tax( $product, $args = array() ) {
		if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
			if ( empty( $args['price'] ) ) {
				$args = wp_parse_args( $args, array(
					'qty'   => '',
					'price' => '',
				) );
				$qty  = (int) $args['qty'] ? $args['qty'] : 1;

				return apply_filters( 'woocommerce_get_price_excluding_tax', 0, $qty, $product );
			}
			if ( (float) $args['price'] < 0 ) {
				$args['price'] = - $args['price'];

				return - wc_get_price_excluding_tax( $product, $args );
			}

			return wc_get_price_excluding_tax( $product, $args );
		}
		$args  = wp_parse_args( $args, array(
			'qty'   => '',
			'price' => '',
		) );
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
	function themecomplete_get_price_including_tax( $product, $args = array() ) {
		if ( function_exists( 'wc_get_price_including_tax' ) ) {
			if ( empty( $args['price'] ) ) {
				$args = wp_parse_args( $args, array(
					'qty'   => '',
					'price' => '',
				) );
				$qty  = (int) $args['qty'] ? $args['qty'] : 1;

				return apply_filters( 'woocommerce_get_price_including_tax', 0, $qty, $product );
			}
			if ( (float) $args['price'] < 0 ) {
				$args['price'] = - $args['price'];

				return - wc_get_price_including_tax( $product, $args );
			}

			return wc_get_price_including_tax( $product, $args );
		}
		$args  = wp_parse_args( $args, array(
			'qty'   => '',
			'price' => '',
		) );
		$price = $args['price'] ? $args['price'] : 0;
		$qty   = $args['qty'] ? $args['qty'] : 1;

		return $product->get_price_including_tax( $qty, $price );
	}

}

if ( ! function_exists( 'themecomplete_get_product_type' ) ) {

	/**
	 * Get product type
	 *
	 * @param null $product
	 *
	 * @return bool
	 */
	function themecomplete_get_product_type( $product = NULL ) {
		if ( is_object( $product ) ) {
			if ( is_callable( array( $product, 'get_type' ) ) ) {
				return $product->get_type();
			} else {
				return $product->product_type;
			}
		}

		return FALSE;
	}

}

if ( ! function_exists( 'themecomplete_get_id' ) ) {

	/**
	 * Get product ID
	 *
	 * @param $product
	 *
	 * @return mixed
	 */
	function themecomplete_get_id( $product ) {
		if ( is_callable( array( $product, 'get_id' ) ) && is_callable( array( $product, 'get_parent_id' ) ) ) {
			if ( themecomplete_get_product_type( $product ) == "variation" ) {
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
	 * @param $product
	 *
	 * @return mixed
	 */
	function themecomplete_get_variation_id( $product ) {
		if ( is_callable( array( $product, 'get_id' ) ) ) {
			return $product->get_id();
		}

		return $product->variation_id;
	}

}

if ( ! function_exists( 'themecomplete_get_tax_class' ) ) {

	/**
	 * Get product tax class
	 *
	 * @param $product
	 *
	 * @return mixed
	 */
	function themecomplete_get_tax_class( $product, $context = FALSE ) {
		if ( is_callable( array( $product, 'get_tax_class' ) ) ) {

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7', '<' ) ) {
				return $product->get_tax_class();
			} else {
				if ( $context !== FALSE ) {
					return $product->get_tax_class( $context );
				} else {
					return $product->get_tax_class();
				}
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
	 * @param        $post_id
	 * @param string $meta_key
	 * @param bool   $single
	 *
	 * @return bool|mixed
	 */
	function themecomplete_get_post_meta( $post_id, $meta_key = '', $single = FALSE ) {
		$meta = FALSE;
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7', '<' ) ) {
			$meta = get_post_meta( $post_id, $meta_key, $single );
		} else {
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
		}
		//needed in some rare occassions where WordPress doesn't unserialize the data
		if ( $single ) {
			$meta = maybe_unserialize( $meta );
		}

		return $meta;
	}

}

if ( ! function_exists( 'themecomplete_update_post_meta' ) ) {

	/**
	 * Update post meta
	 *
	 * @param        $post_id
	 * @param        $meta_key
	 * @param        $meta_value
	 * @param string $prev_value
	 *
	 * @return bool|int
	 */
	function themecomplete_update_post_meta( $post_id, $meta_key, $meta_value, $prev_value = '' ) {

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7', '<' ) ) {
			return update_post_meta( $post_id, $meta_key, $meta_value, $prev_value );
		} else {
			if ( is_numeric( $post_id ) ) {
				$product = wc_get_product( $post_id );
				if ( is_object( $product ) ) {
					$product->update_meta_data( $meta_key, $meta_value );
					$product->save_meta_data();

					return TRUE;
				} else {
					return update_post_meta( $post_id, $meta_key, $meta_value );
				}
			}
		}

		return FALSE;

	}

}

if ( ! function_exists( 'themecomplete_delete_post_meta' ) ) {

	/**
	 * Delete post meta
	 *
	 * @param        $post_id
	 * @param        $meta_key
	 * @param string $meta_value
	 *
	 * @return bool
	 */
	function themecomplete_delete_post_meta( $post_id, $meta_key, $meta_value = '' ) {

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7', '<' ) ) {
			return delete_post_meta( $post_id, $meta_key, $meta_value );
		} else {
			if ( is_numeric( $post_id ) ) {
				$product = wc_get_product( $post_id );
				if ( is_object( $product ) ) {
					$product->delete_meta_data( $meta_key );
					$product->save_meta_data();

					return TRUE;
				} else {
					return delete_post_meta( $post_id, $meta_key, $meta_value );
				}
			}
		}

		return FALSE;

	}

}

if ( ! function_exists( 'themecomplete_add_post_meta' ) ) {

	/**
	 * Add post meta
	 *
	 * @param      $post_id
	 * @param      $meta_key
	 * @param      $meta_value
	 * @param bool $unique
	 *
	 * @return bool|false|int
	 */
	function themecomplete_add_post_meta( $post_id, $meta_key, $meta_value, $unique = FALSE ) {

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7', '<' ) ) {
			return add_post_meta( $post_id, $meta_key, $meta_value, $unique );
		} else {
			if ( is_numeric( $post_id ) ) {
				$product = wc_get_product( $post_id );
				if ( is_object( $product ) ) {
					$product->add_meta_data( $meta_key, $meta_value, $unique );
					$product->save_meta_data();

					return TRUE;
				} else {
					return add_post_meta( $post_id, $meta_key, $meta_value, $unique );
				}
			}
		}

		return FALSE;

	}

}

if ( ! function_exists( 'themecomplete_get_attributes' ) ) {

	/**
	 * Get product attributes
	 *
	 * @param $post_id
	 *
	 */
	function themecomplete_get_attributes( $post_id ) {

		if ( is_numeric( $post_id ) ) {
			$product = wc_get_product( $post_id );
		} elseif ( is_object( $post_id ) ) {
			$product = $post_id;
		}

		if ( is_object( $product ) && is_callable( array( $product, 'get_attributes' ) ) ) {
			$attributes = $product->get_attributes();
		} else {
			$attributes = maybe_unserialize( themecomplete_get_post_meta( $post_id, '_product_attributes', TRUE ) );
		}

		return $attributes;

	}

}

if ( ! function_exists( 'themecomplete_order_get_att' ) ) {

	/**
	 * Get order get_$att method
	 *
	 * @param $order
	 * @param $att
	 *
	 * @return mixed
	 */
	function themecomplete_order_get_att( $order, $att ) {

		$ret        = NULL;
		$att_method = 'get_' . $att;

		if ( is_object( $order ) && is_callable( array( $order, $att_method ) ) ) {
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
	 * @param $item
	 *
	 */
	function themecomplete_get_product_from_item( $item, $order = FALSE ) {

		$product = FALSE;
		if ( is_object( $item ) && is_callable( array( $item, 'get_product' ) ) ) {
			$product = $item->get_product();
		} elseif ( $order && is_object( $order ) && is_callable( array( $order, 'get_product_from_item' ) ) ) {
			$product = $order->get_product_from_item( $item );
		}

		return $product;

	}

}

if ( ! function_exists( 'themecomplete_order_get_price_excluding_tax' ) ) {

	/**
	 * Get product price in order with tax excluded
	 *
	 * @param $item
	 *
	 */
	function themecomplete_order_get_price_excluding_tax( $order, $item_id, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'qty'   => '',
			'price' => '',
		) );

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

		$tax_data  = empty( $legacy_order ) && wc_tax_enabled() ? maybe_unserialize( isset( $order_items[ $item_id ]['line_tax_data'] ) ? $order_items[ $item_id ]['line_tax_data'] : '' ) : FALSE;
		$tax_price = 0;
		if ( ! empty( $tax_data ) && $prices_include_tax ) {
			$tax_based_on = get_option( 'woocommerce_tax_based_on' );
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7.0', '<' ) ) {
				if ( 'billing' === $tax_based_on ) {
					$country  = $order->billing_country;
					$state    = $order->billing_state;
					$postcode = $order->billing_postcode;
					$city     = $order->billing_city;
				} elseif ( 'shipping' === $tax_based_on ) {
					$country  = $order->shipping_country;
					$state    = $order->shipping_state;
					$postcode = $order->shipping_postcode;
					$city     = $order->shipping_city;
				}
			} else {
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
			}
			// Default to base
			if ( 'base' === $tax_based_on || empty( $country ) ) {
				$default  = wc_get_base_location();
				$country  = $default['country'];
				$state    = $default['state'];
				$postcode = '';
				$city     = '';
			}
			$tax_class      = $order_items[ $item_id ]['tax_class'];
			$tax_rates      = WC_Tax::find_rates( array(
				'country'   => $country,
				'state'     => $state,
				'postcode'  => $postcode,
				'city'      => $city,
				'tax_class' => $tax_class,
			) );
			$epo_line_taxes = WC_Tax::calc_tax( $price, $tax_rates, $prices_include_tax );

			foreach ( $order_taxes as $tax_item ) {
				$tax_item_id = $tax_item['rate_id'];
				if ( is_callable( array( $tax_item, 'get_rate_id' ) ) ) {
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

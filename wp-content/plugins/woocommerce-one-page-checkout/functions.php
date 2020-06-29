<?php
/**
 * WooCommerce One Page Checkout functions
 *
 * Functions mainly to take advantage of APIs added to newer versions of WooCommerce while maintaining backward compatibility.
 *
 * @author  Automattic
 * @version 1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Set the property for a product in a version independent way.
 *
 * @since 1.4.0
 */
function wcopc_set_products_prop( $product, $prop, $value ) {
	if ( is_callable( array( $product, 'update_meta_data' ) ) ) { // WC 3.0+
		$product->update_meta_data( $prop, $value );
	} else {
		$product->{$prop} = $value;
	}
}

/**
 * Returns the ID of a parent product of a variation product or false otherwise.
 *
 * @since 1.5.5
 *
 * @param WC_Product|int $product WC_Product object or an ID
 *
 * @return int|false
 */
function wcopc_get_variation_parent_id( $product ) {
	$product = $product instanceof WC_Product ? $product : wc_get_product( $product );

	if ( ! $product->is_type( 'variation' ) ) {
		$parent = false;
	} else if ( is_callable( array( $product, 'get_parent_id' ) ) ) {
		$parent = $product->get_parent_id();
	} else if ( ! empty( $product->parent ) && $product->parent instanceof WC_Product_Variable ) {
		$parent = $product->parent->get_id();
	} else {
		$parent = wp_get_post_parent_id( $product->get_id() );
	}

	return $parent;
}

/**
 * Get the property for a product in a version independent way.
 *
 * @since 1.4.0
 */
function wcopc_get_products_prop( $product, $prop, $meta_key_prefix = '' ) {
	if ( is_callable( array( $product, 'get_meta' ) ) ) { // WC 3.0+
		$value = $product->get_meta( $meta_key_prefix . $prop );
	} else {
		$value = $product->{$prop};
	}

	return $value;
}

/**
 * Get the name for a product in a version independent way.
 *
 * @since 1.5.4
 */
function wcopc_get_products_name( $product ) {

	if ( is_callable( array( $product, 'get_name' ) ) ) { // WC 3.0+
		$name = $product->get_name();
	} else {
		$name = $product->get_title();
	}

	return $name;
}

/**
 * Get the type of a certain product
 *
 * @since 1.4.0
 */
function wcopc_get_product_type( $product ) {

	if ( $product->is_type( 'variable' ) ) {
		$product_type = 'variable';
	} elseif ( $product->get_type() ) {
		$product_type = $product->get_type();
	} else {
		$product_type = 'simple';
	}

	return $product_type;
}

/**
 * Get the url to remove a cart item from the cart.
 *
 * @since 1.5.4
 */
function wcopc_get_cart_remove_url( $cart_item_key ) {

	if ( is_callable( 'wc_get_cart_remove_url' ) ) {
		$url = wc_get_cart_remove_url( $cart_item_key );
	} else {
		$url = WC()->cart->get_remove_url( $cart_item_key );
	}

	return $url;
}

/**
 * Gets the cart item formatted data in a WC version compatible way.
 *
 * @since 1.5.4
 */
function wcopc_get_formatted_cart_item_data( $cart_item, $flat = false ) {

	if ( is_callable( 'wc_get_formatted_cart_item_data' ) ) {
		$item_data = wc_get_formatted_cart_item_data( $cart_item, $flat );
	} else {
		$item_data = WC()->cart->get_item_data( $cart_item );
	}

	return $item_data;
}

/**
 * Get all child products with ancestry to a given product.
 * Unlike WC's get_visible_children() or get_children(), this function traverses down grouped products to find all leaf children.
 *
 * @param  int|WC_Product $product The product or product ID.
 * @return array The products child product IDs.
 */
function wcopc_get_all_child_products( $product ) {
	$product  = is_a( $product, 'WC_Product' ) ? $product : wc_get_product( $product );
	$children = array();

	if ( ! is_a( $product, 'WC_Product' ) || ! $product->has_child() ) {
		return $children;
	}

	$visible_children = wcopc_get_visible_children( $product );

	// Variable products are only 1 layer deep, we only need to return their children.
	if ( $product->is_type( 'variable' ) ) {
		$children = $visible_children;
	} elseif ( $product->is_type( 'grouped' ) ) {
		// Find all grouped child product's children
		foreach ( $visible_children as $child_product_id ) {
			$grand_children = wcopc_get_all_child_products( $child_product_id );

			// When there aren't any grand children, this child is the leaf, so add it.
			if ( empty( $grand_children ) ) {
				$children[] = $child_product_id;
			} else {
				$children = array_merge( $children, $grand_children );
			}
		}
	}

	return $children;
}

/**
 * Determines if the current request is for the frontend.
 *
 * The logic in this function is based off WooCommerce::is_request( 'frontend' )
 *
 * @since 1.7.0
 *
 * @return bool
 */
function wcopc_is_frontend_request() {
	return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! wcopc_is_rest_api_request();
}

/**
 * Returns true if the request is a non-legacy REST API request.
 *
 * This function is a compatibility wrapper for WC()->is_rest_api_request() which was introduced in WC 3.6.
 *
 * @since 1.7.0
 *
 * @return bool
 */
function wcopc_is_rest_api_request() {

	if ( is_callable( array( WC(), 'is_rest_api_request' ) ) ) {
		return WC()->is_rest_api_request();
	}


	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return false;
	}

	$rest_prefix         = trailingslashit( rest_get_url_prefix() );
	$is_rest_api_request = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	return apply_filters( 'woocommerce_is_rest_api_request', $is_rest_api_request );
}

/**
 * This function gets the visible children with pre WC 3.0 compatibility.
 * WC 3.0 provides a get_visible_children() method instead of using the $is_visible parameter on get_children()
 *
 * @param WC_Product $product The product
 *
 * @return array
 * @since 1.7.0
 */
function wcopc_get_visible_children( $product ) {
	if ( ! is_a( $product, 'WC_Product' ) ) {
		return array();
	}

	return is_callable( array( $product, 'get_visible_children', ) ) ? $product->get_visible_children() : $product->get_children( true );
}

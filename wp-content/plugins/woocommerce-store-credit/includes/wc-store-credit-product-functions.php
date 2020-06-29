<?php
/**
 * Product Functions
 *
 * @package WC_Store_Credit/Functions
 * @since   3.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the product instance.
 *
 * @since 3.2.0
 *
 * @param mixed $the_product Post object or post ID of the product.
 * @return WC_Product|false The product object. False on failure.
 */
function wc_store_credit_get_product( $the_product ) {
	return ( $the_product instanceof WC_Product ? $the_product : wc_get_product( $the_product ) );
}

/**
 * Gets if it's a 'store_credit' product or not.
 *
 * @since 3.2.0
 *
 * @param mixed $the_product Post object or post ID of the product.
 * @return bool
 */
function wc_is_store_credit_product( $the_product ) {
	$product = wc_store_credit_get_product( $the_product );

	return ( $product && $product->is_type( 'store_credit' ) );
}

/**
 * Gets the product label to use it in a select field.
 *
 * @since 3.2.0
 *
 * @param mixed $the_product Post object or post ID of the product.
 * @param bool  $identifier  Optional. Include the product identifier or not.
 * @return string
 */
function wc_store_credit_get_product_choice_label( $the_product, $identifier = false ) {
	$product = wc_store_credit_get_product( $the_product );

	if ( ! $product ) {
		return '';
	}

	if ( $identifier ) {
		$title = $product->get_formatted_name();
	} else {
		$title = $product->get_title();

		if ( $product instanceof WC_Product_Variation ) {
			$formatted_attributes = wc_get_formatted_variation( $product, true );

			$title = "{$title} &ndash; {$formatted_attributes}";
		}
	}

	return $title;
}

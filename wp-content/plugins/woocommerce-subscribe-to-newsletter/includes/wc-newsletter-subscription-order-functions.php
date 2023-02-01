<?php
/**
 * Order functions.
 *
 * @package WC_Newsletter_Subscription/Functions
 * @since   3.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the tags to add to the subscriber based on the order info.
 *
 * @since 3.6.0
 *
 * @param WC_Order $order Order object.
 * @return array
 */
function wc_newsletter_subscription_get_tags_for_order( $order ) {
	$tags = wc_newsletter_subscription_get_product_tags_for_order( $order );

	/**
	 * Filters the tags to add to the subscriber based on the order info.
	 *
	 * @since 3.6.0
	 *
	 * @param array    $tags  An array of tags.
	 * @param WC_Order $order Order object.
	 */
	return apply_filters( 'wc_newsletter_subscription_order_tags', $tags, $order );
}

/**
 * Gets the tags to add to the subscriber based on the order items.
 *
 * @since 3.6.0
 *
 * @param WC_Order $order Order object.
 * @return array
 */
function wc_newsletter_subscription_get_product_tags_for_order( $order ) {
	// Don't include the product tags.
	if ( 'yes' !== get_option( 'woocommerce_newsletter_product_tags', 'no' ) ) {
		return array();
	}

	$tags  = array();
	$items = $order->get_items();

	$tag_format = get_option( 'woocommerce_newsletter_product_tag_format' );

	if ( ! $tag_format ) {
		$tag_format = '{product-slug}';
	}

	foreach ( $items as $item ) {
		if ( ! $item instanceof WC_Order_Item_Product ) {
			continue;
		}

		$product = $item->get_product();

		if ( $product ) {
			$tags[] = str_replace( '{product-slug}', $product->get_slug(), $tag_format );
		}
	}

	return $tags;
}

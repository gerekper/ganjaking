<?php
/**
 * Template file for the email variable "{item_prices_image}".
 *
 * To edit this template, copy this file over to your wp-content/[current_theme]/follow-up-emails/email-variables
 * then edit the new file. A single variable named $lists is passed along to this template.
 *
 * $lists = array('items' => array(
 *      array(
 *          id:     Product ID
 *          sku:    Product's SKU
 *          link:   Absolute URL to the product
 *          name:   Product's name
 *          price:  Price of the product - unformatted
 *          qty:    Quantity bought
 *          categories: Array of product categories
 *      )
 * ))
 */
?>
<ul>
	<?php

	if ( ! function_exists( 'get_thumbnail_data' ) ) {
		/**
		 * Returns the main product image. This function is similar to
		 * WC_Product::get_image with the exception that it does not return
		 * relative URLs, since those cannot be viewed in emails.
		 *
		 * @param string $size (default: 'woocommerce_thumbnail').
		 * @param array  $attr Image attributes.
		 * @param bool   $placeholder True to return $placeholder if no image is found, or false to return an empty string.
		 * @return string
		 */
		function get_thumbnail_data( $product, $size = 'woocommerce_thumbnail', $attr = array(), $placeholder = true ) {
			if ( has_post_thumbnail( $product->get_id() ) ) {
				$image = get_the_post_thumbnail( $product->get_id(), $size, $attr );
			} elseif ( ( $parent_id = wp_get_post_parent_id( $product->get_id() ) ) && has_post_thumbnail( $parent_id ) ) {
				$image = get_the_post_thumbnail( $parent_id, $size, $attr );
			} elseif ( $placeholder ) {
				$image = wc_placeholder_img( $size );
			} else {
				$image = '';
			}

			if ( version_compare( WC_VERSION, '3.3.2', '<' ) ) {
				return apply_filters( 'woocommerce_product_get_image', $image, $product, $size, $attr, $placeholder );
			} else {
				return apply_filters( 'woocommerce_product_get_image', $image, $product, $size, $attr, $placeholder, $image );
			}
		}
	}

	foreach ( $lists['items'] as $item ) {
		$_product = WC_FUE_Compatibility::wc_get_product( $item['id'] );

		if ( ! $_product ) {
			continue;
		}

		$thumbnail = get_thumbnail_data( $_product, 'woocommerce_thumbnail', array( 'title' => '' ) );

		$thumbnail_html = sprintf( '<a href="%s">%s</a>', esc_url( $item['link'] ), $thumbnail );

		echo wp_kses_post( sprintf(
			'<li>%s <a href="%s">%s X %d &ndash; %s</a></li>',
			$thumbnail_html,
			$item['link'],
			$item['name'],
			$item['qty'],
			wc_price( $item['price'] ) )
		);
	} ?>
</ul>

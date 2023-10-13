<?php
/**
 * Mix and Match Item Thumbnail
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm/mnm-product-thumbnail.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce Mix and Match/Templates
 * @since   1.9.4
 * @version 2.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="mnm_child_product_images mnm_image">
<?php

	global $product;

if ( $thumbnail_id ) {

	$image_title = esc_attr( get_the_title( $thumbnail_id ) );
	$image_data  = wp_get_attachment_image_src( $thumbnail_id, 'full' );
	$image_link  = $image_data[0];

	$image = wp_get_attachment_image(
		$thumbnail_id,
		$image_size,
		false,
		array(
			'title'                   => $image_title,
			'data-caption'            => get_post_field( 'post_excerpt', $thumbnail_id ),
			'data-large_image'        => $image_link,
			'data-large_image_width'  => $image_data[1],
			'data-large_image_height' => $image_data[2],
		)
	);

	$html  = '<figure class="mnm_child_product_image woocommerce-product-gallery__image">';
	$html .= sprintf( '<a href="%1$s" class="image zoom" title="%2$s" data-rel="%3$s">%4$s</a>', $image_link, $image_title, $image_rel, $image );
	$html .= '</figure>';

} else {

	$html  = '<figure class="mnm_child_product_image woocommerce-product-gallery__image--placeholder">';
	$html .= sprintf(
		'<img class="wp-post-image" src="%1$s" alt="%2$s"/>',
		wc_placeholder_img_src(),
		_x( 'Child product placeholder image', '[Frontend]', 'woocommerce-mix-and-match-products' )
	);
	$html .= '</figure>';
}

// Backcompatibility filter.
if ( has_filter( 'wc_mnm_child_product_image_html' ) ) {
	wc_deprecated_hook( 'wc_mnm_child_product_image_html', '2.0.0', 'wc_mnm_child_item_image_html' );
	$html = apply_filters( 'wc_mnm_child_product_image_html', $html, $child_item->get_product()->get_id(), $mnm_item );
}

/**
 * Child item image html
 *
 * @since 2.0.0
 *
 * @param string $html
 * @param  obj WC_MNM_Child_Item $child_item
 * @param  obj WC_Product_Mix_and_Match $product
 */
echo wp_kses_post( apply_filters( 'wc_mnm_child_item_image_html', $html, $child_item, $child_item->get_container() ) );
?>
</div>

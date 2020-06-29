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
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Kathy Darling
 * @package WooCommerce Mix and Match/Templates
 * @since   1.9.4
 * @version 1.9.7
 */
if ( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}
?>
<div class="mnm_child_product_images mnm_image"><?php

	global $product;

	$thumbmail_id = has_post_thumbnail( $mnm_item->get_id() ) ? get_post_thumbnail_id( $mnm_item->get_id() ) : get_post_thumbnail_id( $mnm_item->get_parent_id() );

if ( $thumbmail_id ) {

	$image_title   = esc_attr( get_the_title( $thumbmail_id ) );
	$image_data    = wp_get_attachment_image_src( $thumbmail_id, 'full' );
	$image_link    = $image_data[ 0 ];

	/**
	 * Child item thumbnail size.
	 *
	 * @param string $size
	 * @param  obj WC_Product $mnm_item
	 * @param  obj WC_Product_Mix_and_Match $product
	 */
	$image_size    = apply_filters( 'woocommerce_mnm_product_thumbnail_size', WC_MNM_Core_Compatibility::is_wc_version_gte( '3.3' ) ? 'woocommerce_thumbnail' : 'shop_thumbnail', $mnm_item, $product );

	/**
	 * Child item link_classes. 
	 * Some themes use different lightbox triggers.
	 *
	 * @param array $link_classes
	 * @param  obj WC_Product $mnm_item
	 * @param  obj WC_Product_Mix_and_Match $product
	 * 
	 */
	$link_classes    = apply_filters( 'wc_mnm_product_thumbnail_link_classes', array( 'image', 'zoom' ), $mnm_item, $product );

	$image         = wp_get_attachment_image( $thumbmail_id, $image_size, false, array(
		'title'                   => $image_title,
		'data-caption'            => get_post_field( 'post_excerpt', $thumbmail_id ),
		'data-large_image'        => $image_link,
		'data-large_image_width'  => $image_data[ 1 ],
		'data-large_image_height' => $image_data[ 2 ],
	) );

	$html  = '<figure class="mnm_child_product_images woocommerce-product-gallery__image">';
		
	if ( current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
		$html .= sprintf( '<a href="%1$s" class="%2$s" title="%3$s" data-rel="photoSwipe">%4$s</a>',
				$image_link,
				join( ' ', $link_classes ),
				$image_title,
				$image
			);
	} else {
		$html .= $image;
	}
	$html .= '</figure>';

} else {

	$html  = '<figure class="mnm_child_product_images woocommerce-product-gallery__image--placeholder">';
	$html .= sprintf( '<img class="wp-post-image" src="%1$s" alt="%2$s"/>',
		wc_placeholder_img_src(),
		__( 'Child product placeholder image', 'woocommerce-mix-and-match-products' )
	);
	$html .= '</figure>';
}

	echo apply_filters( 'wc_mnm_child_product_image_html', $html, $mnm_item->get_id(), $mnm_item );

?></div>
<?php
/**
 * Bundled Product Image template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/bundled-item-image.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 6.21.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div class="<?php echo esc_attr( implode( ' ', $gallery_classes ) ); ?>"><?php

	if ( has_post_thumbnail( $product_id ) ) {

		$image_post_id = get_post_thumbnail_id( $product_id );
		$image_title   = esc_attr( get_the_title( $image_post_id ) );
		$image_data    = wp_get_attachment_image_src( $image_post_id, 'full' );
		$image_link    = $image_data[ 0 ];
		$image         = get_the_post_thumbnail( $product_id, $image_size, array(
			'title'                   => $image_title,
			'data-caption'            => get_post_field( 'post_excerpt', $image_post_id ),
			'data-large_image'        => $image_link,
			'data-large_image_width'  => $image_data[ 1 ],
			'data-large_image_height' => $image_data[ 2 ],
		) );

		$html  = '<figure class="bundled_product_image woocommerce-product-gallery__image">';
		$html .= sprintf( '<a href="%1$s" class="image zoom" title="%2$s" data-rel="%3$s">%4$s</a>', $image_link, $image_title, $image_rel, $image );
		$html .= '</figure>';

	} else {

		$html  = '<figure class="bundled_product_image woocommerce-product-gallery__image--placeholder">';
		$html .= sprintf( '<a href="%1$s" class="placeholder_image zoom" data-rel="%3$s"><img class="wp-post-image" src="%1$s" alt="%2$s"/></a>', wc_placeholder_img_src(), __( 'Bundled product placeholder image', 'woocommerce-product-bundles' ), $image_rel );
		$html .= '</figure>';
	}

	echo apply_filters( 'woocommerce_bundled_product_image_html', $html, $product_id, $bundled_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

?></div>

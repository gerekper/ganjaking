<?php
/**
 * Photography loop add to cart button.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $post;

?>
<div class="photography-image">
	<?php
		if ( has_post_thumbnail() ) {

			$image_title = esc_attr( get_the_excerpt() );
			$image_thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), 'photography_lightbox' );
			$image_link  = isset( $image_thumb[0] ) ? $image_thumb[0] : '';
			$image       = get_the_post_thumbnail( $post->ID, apply_filters( 'wc_photography_shop_loop_thumbnail_size', 'photography_thumbnail' ), array( 'title' => $image_title ) );

			echo apply_filters( 'wc_photography_shop_loop_image_html', sprintf( '<a href="%s" class="zoom" title="%s" data-rel="prettyPhoto[product-gallery]">%s</a>', $image_link, $image_title, $image ), $post->ID );

		} else {

			echo apply_filters( 'wc_photography_shop_loop_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce-photography' ) ), $post->ID );

		}
	?>
</div>

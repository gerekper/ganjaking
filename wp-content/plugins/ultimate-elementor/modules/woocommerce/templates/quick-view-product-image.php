<?php
/**
 * WooCommerce - Product Images
 *
 * @package UAEL
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product, $woocommerce;

?>
<div class="uael-qv-image-slider flexslider images">
	<div class="uael-qv-slides slides">
	<?php
	if ( has_post_thumbnail() ) {
		$attachment_ids = $product->get_gallery_image_ids();
		$props          = wc_get_product_attachment_props( get_post_thumbnail_id(), $post );
		$image          = get_the_post_thumbnail(
			$post->ID,
			'shop_single',
			array(
				'title' => $props['title'],
				'alt'   => $props['alt'],
			)
		);
		echo sprintf(
			'<li class="woocommerce-product-gallery__image">%s</li>',
			$image // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);

		if ( $attachment_ids ) {
			$loop = 0;

			foreach ( $attachment_ids as $attachment_id ) {

				$props = wc_get_product_attachment_props( $attachment_id, $post );

				if ( ! $props['url'] ) {
					continue;
				}

				echo sprintf(
					'<li>%s</li>',
					wp_get_attachment_image( $attachment_id, 'shop_single', 0, $props )
				);

				$loop++;
			}
		}
	} else {
		echo sprintf( '<li><img src="%s" alt="%s" /></li>', wp_kses_post( wc_placeholder_img_src() ), esc_attr__( 'Placeholder', 'uael' ) );
	}
	?>
	</div>
</div>

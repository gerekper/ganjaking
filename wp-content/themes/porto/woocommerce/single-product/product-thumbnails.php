<?php
/**
 * Single Product Thumbnails
 *
 * @version     3.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $woocommerce, $product, $porto_product_layout, $porto_settings;

if ( 'extended' == $porto_product_layout || 'sticky_info' == $porto_product_layout || 'sticky_both_info' == $porto_product_layout || 'grid' == $porto_product_layout ) {
	return;
}

$attachment_ids     = $product->get_gallery_image_ids();
$thumbnails_classes = '';
if ( 'full_width' === $porto_product_layout || 'centered_vertical_zoom' === $porto_product_layout ) {
	$thumbnails_classes = 'product-thumbnails-inner';
} elseif ( 'transparent' === $porto_product_layout ) {
	$thumbnails_classes = 'product-thumbs-vertical-slider';
} else {
	$thumbnails_classes = 'product-thumbs-slider owl-carousel';
}

?>
<div class="product-thumbnails thumbnails">
	<?php
	$html = '<div class="' . esc_attr( $thumbnails_classes ) . ( 'product-thumbs-slider owl-carousel' == $thumbnails_classes ? ' has-ccols ccols-' . intval( $porto_settings['product-thumbs-count'] ) : '' ) . '">';

	if ( $product->get_image_id() ) {

		$attachment_id = get_post_thumbnail_id();
		$image_title   = get_the_title( $attachment_id );
		if ( ! $image_title ) {
			$image_title = '';
		}
		$image_thumb_link = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );

		$html .= '<div class="img-thumbnail">';
		$html .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', '<img class="woocommerce-main-thumb img-responsive" alt="' . esc_attr( $image_title ) . '" src="' . esc_url( $image_thumb_link[0] ) . '" />', $attachment_id, $post->ID, '' ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
		$html .= '</div>';

	} else {

		$image_thumb_link = wc_placeholder_img_src();
		$html            .= '<div class="img-thumbnail"><div class="inner">';
		$html            .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', '<img class="woocommerce-main-thumb img-responsive" alt="placeholder" src="' . esc_url( $image_thumb_link ) . '" />', false, $post->ID, '' ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
		$html            .= '</div></div>';
	}

	if ( $attachment_ids ) {
		foreach ( $attachment_ids as $attachment_id ) {

			$image_link = wp_get_attachment_image_url( $attachment_id, 'full' );

			if ( ! $image_link ) {
				continue;
			}

			$image            = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
			$image_thumb_link = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
			$image_title      = get_the_title( $attachment_id );
			if ( ! $image_title ) {
				$image_title = '';
			}

			$html .= '<div class="img-thumbnail">';
			$html .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', '<img class="img-responsive" alt="' . esc_attr( $image_title ) . '" src="' . esc_url( $image_thumb_link[0] ) . '" />', $attachment_id, $post->ID, '' ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			$html .= '</div>';

		}
	}
	$html .= apply_filters( 'porto_single_product_after_thumbnails', '' );

	$html .= '</div>';

	echo porto_filter_output( $html );

	?>
</div>

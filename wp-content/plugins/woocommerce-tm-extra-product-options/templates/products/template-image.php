<?php
/**
 * The template for displaying the product element image
 *
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

$image_data = FALSE;
if ( has_post_thumbnail( $product_id ) ) {
	$image_post_id = get_post_thumbnail_id( $product_id );
	$image_title   = get_the_title( $image_post_id );
	$image_data    = wp_get_attachment_image_src( $image_post_id, 'full' );
	$image_link    = "";
	if ( ! $image_data ) {
		$image_data = FALSE;
	} else {
		$image_link = $image_data[0];
	}
}
if ( $image_data !== FALSE ) {
	?>
    <figure class="tc-product-image"><?php
	echo get_the_post_thumbnail( $product_id, 'shop_catalog', array(
		'title'                   => $image_title,
		'data-caption'            => get_post_field( 'post_excerpt', $image_post_id ),
		'data-large_image'        => $image_link,
		'data-large_image_width'  => $image_data[1],
		'data-large_image_height' => $image_data[2],
	) );
	?></figure><?php
} else {
	?>
    <figure class="tc-product-image woocommerce-product-gallery__image--placeholder">
    <img class="wp-post-image" src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" alt="<?php esc_attr_e( 'Awaiting product image', 'woocommerce-tm-extra-product-options' ); ?>"/>
    </figure><?php
}

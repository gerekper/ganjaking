<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php

global $wpdb, $woocommerce, $woocommerce_loop;

$products = get_posts( $query_args );
$i        = 0;
$cols     = '';

$priorities = array(
	'hide_cart'  => - 1,
	'hide_price' => - 1
);


if ( $hide_add_to_cart ) {
	$priorities['hide_cart'] = has_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

	if ( $priorities['hide_cart'] != false ) {
		remove_action( 'woocommerce_template_loop_add_to_cart', $priorities['hide_cart'] );
		add_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string', 10 );
	}

}

if ( $hide_price ) {

	$priorities['hide_price'] = has_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price' );

	if ( $priorities['hide_price'] != false ) {
		remove_action( 'woocommerce_template_loop_price', $priorities['hide_price'] );
		add_filter( 'woocommerce_get_price_html', '__return_empty_string', 10 );

	}
}

$extra_class = isset( $woocommerce_loop['products_layout'] ) ? array( $woocommerce_loop['products_layout'] ) : array();

$extra_class = apply_filters( 'ywcps_add_classes_in_slider', $extra_class );

$extra_class = implode( ' ', $extra_class );
$z_index     = empty( $z_index ) ? '' : 'style="z-index: ' . $z_index . ';"';

ob_start();

if ( count( $products ) > 0 ) :
	echo '<div class="woocommerce ywcps-product-slider">';
	if ( $show_title ) {
		echo '<h3>' . get_the_title( $id ) . '</h3>';
	}
	echo '<div class="ywcps-wrapper" data-columns="%columns%" data-en_responsive="' . $en_responsive . '" data-n_item_desk_small="' . $n_item_desk_small . '" data-n_item_tablet="' . $n_item_tablet . '" data-n_item_mobile="' . $n_item_mobile . '" ';
	echo 'data-n_items="' . $n_items . '" data-is_loop="' . $is_loop . '" data-pag_speed="' . $page_speed . '" data-auto_play="' . $auto_play . '" data-stop_hov="' . $stop_hov . '" data-show_nav="' . $show_nav . '" ';
	echo 'data-en_rtl="' . $is_rtl . '" data-anim_in="' . $anim_in . '" data-anim_out="' . $anim_out . '" data-anim_speed="' . $anim_speed . '" data-show_dot_nav="' . $show_dot_nav . '" data-slide_by="' . $slideBy . '" >';
	echo '<div class="ywcps-slider ' . $extra_class . '" style="visibility:hidden;">';
	echo '<ul class="ywcps-products products ywcps_products_slider owl-carousel" ' . $z_index . '>';
	foreach ( $products as $slider_product ) :
		global $product, $post;
		$post    = $slider_product;
		$product = wc_get_product( $slider_product->ID );
		do_action( 'ywcps_before_slider_loop_item' );
		wc_get_template( 'content-product.php' );
		do_action( 'ywcps_after_slider_loop_item' );
		$i ++;
		$cols = ( isset( $woocommerce_loop['columns'] ) ) ? $woocommerce_loop['columns'] : 6; //fix $woocommerce_loop['columns'] empty
	endforeach; // end of the loop.
	echo '</ul></div>';
	echo '<div class="ywcps-nav">';
	echo '<div id="nav_prev_def_' . $id . '" class="ywcps-nav-prev"><span id="default_prev"></span></div>';
	echo '<div id="nav_next_def_' . $id . '" class="ywcps-nav-next"><span id="default_next"></span></div>';
	echo '</div></div><div class="es-carousel-clear"></div>';
	echo '</div>';
else:
	_e( 'There is no product to show', 'yith-woocommerce-product-slider-carousel' );
endif;

if ( $hide_add_to_cart && $priorities['hide_cart'] != false ) {
	add_action( 'woocommerce_template_loop_add_to_cart', $priorities['hide_cart'] );
	remove_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string', 10 );
}

if ( $hide_price && $priorities['hide_price'] != false ) {
	add_action( 'woocommerce_template_loop_price', $priorities['hide_price'] );
	remove_filter( 'woocommerce_get_price_html', '__return_empty_string', 10 );
}

$content = ob_get_clean();

echo str_replace( '%columns%', $cols, $content );

wp_reset_postdata();

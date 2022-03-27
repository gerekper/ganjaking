<?php // phpcs:ignore WordPress.NamingConventions
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\WooCommerceProductSliderCarousel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php

global $wpdb, $woocommerce, $woocommerce_loop;

$products = get_posts( $query_args );
$i        = 0;

// @codingStandardsIgnoreStart
$cols = ( isset( $woocommerce_loop['columns'] ) ) ? $woocommerce_loop['columns'] : 6; // Fix $woocommerce_loop['columns'] empty!
// @codingStandardsIgnoreEnd

$priorities = array(
	'hide_cart'  => - 1,
	'hide_price' => - 1,
);


if ( $hide_add_to_cart ) {
	$priorities['hide_cart'] = has_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

	if ( false !== $priorities['hide_cart'] ) {
		remove_action( 'woocommerce_template_loop_add_to_cart', $priorities['hide_cart'] );
		add_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string', 10 );
	}
}

if ( $hide_price ) {

	$priorities['hide_price'] = has_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price' );

	if ( false !== $priorities['hide_price'] ) {
		remove_action( 'woocommerce_template_loop_price', $priorities['hide_price'] );
		add_filter( 'woocommerce_get_price_html', '__return_empty_string', 10 );

	}
}

$extra_class = isset( $woocommerce_loop['products_layout'] ) ? array( $woocommerce_loop['products_layout'] ) : array();

$extra_class = apply_filters( 'ywcps_add_classes_in_slider', $extra_class );

$extra_class = implode( ' ', $extra_class );
$z_index     = empty( $z_index ) ? '' : 'style="z-index: ' . $z_index . ';"';
if ( count( $products ) > 0 ) :
	$data_attributes = array(
		'n_items'           => $n_items,
		'is_loop'           => $is_loop,
		'pag_speed'         => $page_speed,
		'auto_play'         => $auto_play,
		'stop_hov'          => $stop_hov,
		'show_nav'          => $show_nav,
		'is_rtl'            => $is_rtl,
		'anim_in'           => $anim_in,
		'anim_out'          => $anim_out,
		'anim_speed'        => $anim_speed,
		'show_dot_nav'      => $show_dot_nav,
		'columns'           => $cols,
		'slide_by'          => $slideBy, // phpcs:ignore WordPress.NamingConventions
		'en_responsive'     => $en_responsive,
		'n_item_desk_small' => $n_item_desk_small,
		'n_item_tablet'     => $n_item_tablet,
		'n_item_mobile'     => $n_item_mobile,
	);
	$data_attributes = yith_plugin_fw_html_data_to_string( $data_attributes );
	?>
<div class="woocommerce ywcps-product-slider">
	<?php
	if ( $show_title ) {
		echo '<h3>' . esc_html( $title ) . '</h3>';
	}
	?>
	<div class="ywcps-wrapper" <?php echo $data_attributes;//phpcs:ignore WordPress.Security.EscapeOutput ?> >
		<div class="ywcps-slider <?php echo esc_attr( $extra_class ); ?>"style="visibility:hidden;">
			<ul class="ywcps-products products ywcps_products_slider owl-carousel" <?php echo esc_attr( $z_index ); ?>>
				<?php
				foreach ( $products as $slider_product ) :
					global $product, $post;
					$post    = $slider_product; //phpcs:ignore WordPress.WP.GlobalVariablesOverride
					$product = wc_get_product( $slider_product->ID );
					do_action( 'ywcps_before_slider_loop_item' );
					wc_get_template( 'content-product.php' );
					do_action( 'ywcps_after_slider_loop_item' );
					$i ++;
					endforeach; // end of the loop.
				?>
			</ul>
		</div>
		<div class="ywcps-nav">
			<div id="nav_prev_def_<?php echo esc_attr( $id ); ?>" class="ywcps-nav-prev"><span id="default_prev"></span></div>
			<div id="nav_next_def_<?php echo esc_attr( $id ); ?>" class="ywcps-nav-next"><span id="default_next"></span></div>
		</div>
	</div>
	<div class="es-carousel-clear"></div>
</div>
	<?php
	else :
		esc_html_e( 'There is no product to show', 'yith-woocommerce-product-slider-carousel' );
endif;

	if ( $hide_add_to_cart && false !== $priorities['hide_cart'] ) {
		add_action( 'woocommerce_template_loop_add_to_cart', $priorities['hide_cart'] );
		remove_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string', 10 );
	}

	if ( $hide_price && false !== $priorities['hide_price'] ) {
		add_action( 'woocommerce_template_loop_price', $priorities['hide_price'] );
		remove_filter( 'woocommerce_get_price_html', '__return_empty_string', 10 );
	}
	wp_reset_postdata();

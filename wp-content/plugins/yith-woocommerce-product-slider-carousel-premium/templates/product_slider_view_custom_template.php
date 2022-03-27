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
$products = get_posts( $query_args );

if ( count( $products ) > 0 ) {
	$show_wishlist_btn = get_post_meta( $id, '_ywcps_show_wishlist', true );

	$sizes = wc_get_image_size( 'shop_single' );
	add_image_size( 'ywcps_image_size', $sizes['width'], $sizes['height'], true );
	$n_items = apply_filters( 'ywcps_items_to_show', $n_items, $products );
	$z_index = empty( $z_index ) ? '' : 'style="z-index: ' . $z_index . ';"';

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
		'en_responsive'     => $en_responsive,
		'n_item_desk_small' => $n_item_desk_small,
		'n_item_tablet'     => $n_item_tablet,
		'n_item_mobile'     => $n_item_mobile,
		'slide_by'          => $slideBy, // phpcs:ignore WordPress.NamingConventions
	);

	$data_attributes = yith_plugin_fw_html_data_to_string( $data_attributes );

	if ( 'tmp1' === $layouts ) {
		$layout = 'ywcps_layout1';
	} elseif ( 'tmp2' === $layouts ) {
		$layout = 'ywcps_layout2';
	} else {
		$layout = 'ywcps_layout3';
	}
	?>
<div id="<?php echo esc_attr( $layout ); ?>" class="general_container woocommerce" >
	<?php
	if ( $show_title ) {
		echo '<h3>' . esc_html( $title ) . '</h3>';
	}
	?>
	<div class="ywcps-wrapper" <?php echo $data_attributes; //phpcs:ignore WordPress.Security.EscapeOutput ?> >
		<div class="ywcps-slider" style="visibility: hidden;">
		<ul class="ywcps-products products owl-carousel <?php echo esc_attr( $layout ); ?>" <?php echo esc_attr( $z_index ); ?> >
		<?php
		foreach ( $products as $prod ) {
			global $product;
			$product = wc_get_product( $prod->ID );
			?>
			<li class="single_product product">
				<div class="single_product_container product-wrapper">
					<?php
					if ( defined( 'YITH_WCWL_PREMIUM' ) && yith_plugin_fw_is_true( $show_wishlist_btn ) ) {
						echo do_shortcode( '[yith_wcwl_add_to_wishlist label="" already_in_wishslist_text="" browse_wishlist_text=""] ' );
					}
						$img = '';
					if ( has_post_thumbnail( $prod->ID ) ) {
						$img = apply_filters( 'ywcps_product_image', get_the_post_thumbnail( $prod->ID, apply_filters( 'ywps_thumbnail_size', 'ywcps_image_size' ) ), $prod->ID );
					} else {
						$img = wc_placeholder_img( 'shop_catalog' );
					}
					?>
					<div class="product_img">
						<?php do_action( 'yith_wcbsl_bestseller_product_badge', $product ); ?>
						<a href="<?php echo esc_url( get_permalink( $prod->ID ) ); ?>" ><?php echo $img; //phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					</div>
					<div class="product_other_info">
						<div class="product_name">
						<?php do_action( 'yith_ywcps_before_title', $product ); ?>
							<a href="<?php echo esc_url( get_permalink( $prod->ID ) ); ?>" ><?php echo esc_html( $product->get_title() ); ?></a>
							<?php do_action( 'yith_ywcps_after_title', $product ); ?>
							<?php
							if ( ! $hide_price ) {
								?>
								<div class="product_price"><?php echo $product->get_price_html(); //phpcs:ignore WordPress.Security.EscapeOutput ?></div>
								<?php

								do_action( 'yith_ywcps_after_price', $product );
							}
							if ( function_exists( 'YITH_WCBR_Premium' ) ) {
								echo do_shortcode( '[yith_wcbr_product_brand content_to_show="logo"]' );
							}
							?>
						</div>
						<?php
						woocommerce_template_loop_rating();
						if ( ! $hide_add_to_cart ) {

							$add_to_cart_text  = $product->add_to_cart_text();
							$add_to_cart_url   = $product->add_to_cart_url();
							$add_to_cart_class = implode(
								' ',
								array_filter(
									array(
										'button',
										'product_type_' . $product->get_type(),
										$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
										$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
										'product_add_to_cart',
									)
								)
							);

							$sku = is_a( $product, 'WC_Data' ) ? $product->get_sku() : $product->sku;

							$add_to_cart_button = '';

							$add_to_cart_button = sprintf(
								'<a rel="%s" data-product_id="%s" data-product_sku="%s" class="%s" href="%s">',
								'nofollow',
								yit_get_base_product_id( $product ),
								$sku,
								$add_to_cart_class,
								$add_to_cart_url
							);

							if ( 'tmp1' === $layouts ) {
								$add_to_cart_button .= '<span class="icon_add_to_cart">&nbsp;</span>';
							} elseif ( 'tmp3' === $layouts ) {
								$add_to_cart_button .= '+ ';
							}

							$add_to_cart_button .= $add_to_cart_text . '</a>';

							$add_to_cart_button = apply_filters( 'woocommerce_loop_add_to_cart_link', $add_to_cart_button, $product, $args );
							echo $add_to_cart_button; //phpcs:ignore WordPress.Security.EscapeOutput
						}
						?>
					</div>
				</div>
			</li>
							<?php
		}
		?>
		</ul>
		</div>
		<div class="ywcps-nav" id="nav_<?php echo esc_attr( $layouts ); ?>">
			<div id="nav_prev_<?php echo esc_attr( $id ); ?>" class="ywcps-nav-prev">
				<span id="prev_<?php echo esc_attr( $layouts ); ?>" class="fa fa-chevron-left"></span>
			</div>
			<div id="nav_next_<?php echo esc_attr( $id ); ?>" class="ywcps-nav-next">
			<span id="next_<?php echo esc_attr( $layouts ); ?>" class="fa fa-chevron-right"></span>
		</div>
		</div>
	</div>
</div>
			<?php
} else {
	esc_html_e( 'There is no product to show', 'yith-woocommerce-product-slider-carousel' );
}

wp_reset_postdata();

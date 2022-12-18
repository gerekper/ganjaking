<?php

/**
 * The template for displaying product content within loops
 *
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $woocommerce_loop, $product, $porto_settings, $porto_woocommerce_loop, $porto_layout;

$porto_woo_version = porto_get_woo_version_number();

// Ensure visibility.
if ( ! $product || ! $product->is_visible() ) {
	return;
}

// Extra post classes
$classes = array( 'product-col' );
if ( ! empty( $product_classes ) ) {
	$classes[] = trim( $product_classes );
}

if ( ( function_exists( 'wc_get_loop_prop' ) && ! wc_get_loop_prop( 'is_paginated' ) ) || isset( $porto_woocommerce_loop['view'] ) || ! isset( $_COOKIE['gridcookie'] ) || 'list' != $_COOKIE['gridcookie'] ) {
	if ( ! isset( $porto_woocommerce_loop['view'] ) || 'list' != $porto_woocommerce_loop['view'] ) {
		if ( isset( $woocommerce_loop['addlinks_pos'] ) && 'quantity' == $woocommerce_loop['addlinks_pos'] ) {
			$classes[] = 'product-wq_onimage';
		} elseif ( isset( $woocommerce_loop['addlinks_pos'] ) ) {
			if ( 'outimage_aq_onimage2' == $woocommerce_loop['addlinks_pos'] ) {
				$classes[] = 'product-outimage_aq_onimage with-padding';
			} elseif ( 'onhover' == $woocommerce_loop['addlinks_pos'] ) {
				$classes[] = 'product-default show-links-hover';
			} else {
				$classes[] = 'product-' . esc_attr( $woocommerce_loop['addlinks_pos'] );
			}
		}
	}
}

if ( isset( $porto_woocommerce_loop['view'] ) && 'creative' == $porto_woocommerce_loop['view'] && isset( $porto_woocommerce_loop['grid_layout'] ) && isset( $porto_woocommerce_loop['grid_layout'][ $woocommerce_loop['product_loop'] % count( $porto_woocommerce_loop['grid_layout'] ) ] ) ) {
	$grid_layout = $porto_woocommerce_loop['grid_layout'][ $woocommerce_loop['product_loop'] % count( $porto_woocommerce_loop['grid_layout'] ) ];
	$classes[]   = 'grid-col-' . $grid_layout['width'] . ' grid-col-md-' . $grid_layout['width_md'] . ( isset( $grid_layout['width_lg'] ) ? ' grid-col-lg-' . $grid_layout['width_lg'] : '' ) . ' grid-height-' . $grid_layout['height'];

	$porto_woocommerce_loop['image_size'] = $grid_layout['size'];
}

$woocommerce_loop['product_loop']++;

$more_link   = apply_filters( 'the_permalink', get_permalink() );
$more_target = '';
if ( isset( $porto_settings['catalog-enable'] ) && $porto_settings['catalog-enable'] ) {
	if ( $porto_settings['catalog-admin'] || ( ! $porto_settings['catalog-admin'] && ! ( current_user_can( 'administrator' ) && is_user_logged_in() ) ) ) {
		if ( ! $porto_settings['catalog-cart'] ) {
			if ( $porto_settings['catalog-readmore'] && 'all' === $porto_settings['catalog-readmore-archive'] ) {
				$link = get_post_meta( get_the_id(), 'product_more_link', true );
				if ( $link ) {
					$more_link = $link;
				}
				$more_target = $porto_settings['catalog-readmore-target'] ? 'target="' . esc_attr( $porto_settings['catalog-readmore-target'] ) . '"' : '';
			}
		}
	}
}

?>

<li <?php wc_product_class( $classes, $product ); ?>>
<div class="product-inner">
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );
	?>

	<div class="product-image">

		<a <?php echo porto_filter_output( $more_target ); ?> href="<?php echo esc_url( $more_link ); ?>" aria-label="product">
			<?php

				/**
				 * Hook: woocommerce_before_shop_loop_item_title.
				 *
				 * @hooked woocommerce_show_product_loop_sale_flash - 10
				 * @hooked woocommerce_template_loop_product_thumbnail - 10
				 */
				do_action( 'woocommerce_before_shop_loop_item_title' );
			?>
		</a>
	<?php
	$legacy_mode = apply_filters( 'porto_legacy_mode', true );
	$legacy_mode = ( $legacy_mode && ! empty( $porto_settings['product-wishlist'] ) ) || ! $legacy_mode;

	if ( ( ! isset( $porto_woocommerce_loop['widget'] ) || ! $porto_woocommerce_loop['widget'] ) && ( ! isset( $porto_woocommerce_loop['use_simple_layout'] ) || ! $porto_woocommerce_loop['use_simple_layout'] ) && isset( $woocommerce_loop['addlinks_pos'] ) && ! empty( $woocommerce_loop['addlinks_pos'] ) && ( ! in_array( $woocommerce_loop['addlinks_pos'], array( 'default', 'onhover', 'outimage' ) ) || ( class_exists( 'YITH_WCWL' ) && $legacy_mode && 'onimage' == $woocommerce_loop['addlinks_pos'] ) ) && ( ! isset( $porto_woocommerce_loop['view'] ) || 'list' != $porto_woocommerce_loop['view'] ) ) :
		?>
		<div class="links-on-image">
			<?php do_action( 'porto_woocommerce_loop_links_on_image' ); ?>
		</div>
	<?php endif; ?>
	<?php
	if ( isset( $woocommerce_loop['addlinks_pos'] ) && ( 'default' == $woocommerce_loop['addlinks_pos'] || 'outimage' == $woocommerce_loop['addlinks_pos'] || 'onhover' == $woocommerce_loop['addlinks_pos'] || 'onimage' == $woocommerce_loop['addlinks_pos'] ) ) {
		do_action( 'porto_template_compare', ' on-image' );
	}
	?>
	</div>

	<div class="product-content">
		<?php do_action( 'porto_woocommerce_before_shop_loop_item_title' ); ?>

		<?php
			/**
			 * Hook: woocommerce_shop_loop_item_title.
			 *
			 * @hooked woocommerce_template_loop_product_title - 10
			 */
			do_action( 'woocommerce_shop_loop_item_title' );
		?>

		<?php
			/**
			 * Hook: woocommerce_after_shop_loop_item_title.
			 *
			 * @hooked woocommerce_template_loop_rating - 5
			 * @hooked woocommerce_template_loop_price - 10
			 */
			do_action( 'woocommerce_after_shop_loop_item_title' );
		?>

		<?php
			/**
			* Hook: woocommerce_after_shop_loop_item.
			*
			* @hooked woocommerce_template_loop_product_link_close - 5 : removed
			* @hooked woocommerce_template_loop_add_to_cart - 10
			*/
			do_action( 'woocommerce_after_shop_loop_item' );
		?>
	</div>
</div>
</li>

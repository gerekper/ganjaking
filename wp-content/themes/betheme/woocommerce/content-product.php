<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// ensure visibility

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

// extra post classes

$classes = array( 'isotope-item' );

// product type - buttons

if( $product->is_in_stock() && (! mfn_opts_get('shop-catalogue')) && (! in_array($product->get_type(), array('external', 'grouped', 'variable'))) ){
	$image_frame = 'double';
} else {
	$image_frame = false;
}

?>
<li <?php wc_product_class( $classes, $product ); ?>>

	<?php
		/**
		 * woocommerce_before_shop_loop_item hook.
		 *
		 * @hooked woocommerce_template_loop_product_link_open - 10
		 */
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
		do_action( 'woocommerce_before_shop_loop_item' );

		$shop_images = mfn_opts_get( 'shop-images' );

		if( $shop_images == 'plugin' ){
			// Disable Image Frames if use external plugin for Featured Images

			echo '<a href="'. apply_filters( 'the_permalink', get_permalink() ) .'">';

				/**
				 * woocommerce_before_shop_loop_item_title hook.
				 *
				 * @hooked woocommerce_show_product_loop_sale_flash - 10
				 * @hooked woocommerce_template_loop_product_thumbnail - 10
				 */
				remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
				do_action( 'woocommerce_before_shop_loop_item_title' );

				if( $product->is_on_sale() ) echo '<span class="onsale"><i class="icon-star"></i></span>';

			echo '</a>';

		} elseif( $shop_images == 'secondary') {
			// Show secondary image on hover

			echo '<div class="hover_box hover_box_product" ontouchstart="this.classList.toggle(\'hover\');" >';
				echo '<a href="'. apply_filters( 'the_permalink', get_permalink() ) .'">';
					echo '<div class="hover_box_wrapper">';

						if( has_post_thumbnail() ){
							the_post_thumbnail( 'shop_catalog', array( 'class' => 'visible_photo scale-with-grid' ) );
						} elseif ( wc_placeholder_img_src() ) {
							echo wc_placeholder_img( 'shop_catalog' );
						}

						if( $attachment_ids = $product->get_gallery_image_ids() ) {
							if( isset( $attachment_ids['0'] ) ){
								$secondary_image_id = $attachment_ids['0'];
								echo wp_get_attachment_image( $secondary_image_id, 'shop_catalog', '', $attr = array( 'class' => 'hidden_photo scale-with-grid' ) );
							}
						}

					echo '</div>';
				echo '</a>';

				if( $product->is_on_sale() ){
					echo '<span class="onsale"><i class="icon-star"></i></span>';
				}

				if( ! $product->is_in_stock() && $soldout = mfn_opts_get( 'shop-soldout' ) ){
					echo '<span class="soldout"><h4>'. $soldout .'</h4></span>';
				}

			echo '</div>';

		} else {

			echo '<div class="image_frame scale-with-grid product-loop-thumb" ontouchstart="this.classList.toggle(\'hover\');">';

				echo '<div class="image_wrapper">';

				echo '<a href="'. apply_filters( 'the_permalink', get_permalink() ) .'">';
					echo '<div class="mask"></div>';

					if( has_post_thumbnail() ){
						the_post_thumbnail( 'shop_catalog', array( 'class' => 'scale-with-grid' ) );
					} elseif ( wc_placeholder_img_src() ) {
						echo wc_placeholder_img( 'shop_catalog' );
					}

				echo '</a>';

				echo '<div class="image_links '. esc_attr($image_frame) .'">';
					if( $product->is_in_stock() && (! mfn_opts_get('shop-catalogue')) && (! in_array($product->get_type(), array('external', 'grouped', 'variable'))) ){
						if( $product->supports( 'ajax_add_to_cart' ) ){
							echo '<a rel="nofollow" href="'. apply_filters('add_to_cart_url', esc_url($product->add_to_cart_url())) .'" data-quantity="1" data-product_id="'. esc_attr($product->get_id()) .'" class="add_to_cart_button ajax_add_to_cart product_type_simple"><i class="icon-basket"></i></a>';
						} else {
							echo'<a rel="nofollow" href="'. apply_filters('add_to_cart_url', esc_url($product->add_to_cart_url())) .'" data-quantity="1" data-product_id="'. esc_attr($product->get_id()) .'" class="add_to_cart_button product_type_simple"><i class="icon-basket"></i></a>';
						}
					}
					echo '<a class="link" href="'. apply_filters('the_permalink', get_permalink()) .'"><i class="icon-link"></i></a>';
				echo '</div>';

				echo '</div>';

				if( $product->is_on_sale() ){
					echo '<span class="onsale"><i class="icon-star"></i></span>';
				}

				if( ! $product->is_in_stock() && $soldout = mfn_opts_get( 'shop-soldout' ) ){
					echo '<span class="soldout"><h4>'. $soldout .'</h4></span>';
				}

				echo '<a href="'. apply_filters( 'the_permalink', get_permalink() ) .'"><span class="product-loading-icon added-cart"></span></a>';

			echo '</div>';
		}

	?>

	<div class="desc">

		<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>

		<?php
			/**
			 * woocommerce_after_shop_loop_item_title hook.
			 *
			 * @hooked woocommerce_template_loop_rating - 5
			 * @hooked woocommerce_template_loop_price - 10
			 */
			do_action( 'woocommerce_after_shop_loop_item_title' );

			// excerpt
			if( $_GET && key_exists('mfn-shop', $_GET) ){
				$shop_layout = esc_html( $_GET['mfn-shop'] ); // demo
			} else {
				$shop_layout = mfn_opts_get( 'shop-layout', 'grid' );
			}

			if( $_GET && key_exists('mfn-shop-ex', $_GET) ){
				echo '<div class="excerpt">'. apply_filters( 'woocommerce_short_description', $post->post_excerpt ) .'</div>'; // demo
			} elseif( mfn_opts_get('shop-excerpt') ){
				echo '<div class="excerpt">'. apply_filters( 'woocommerce_short_description', $post->post_excerpt ) .'</div>';
			}

		?>

	</div>

	<?php

		/**
		 * woocommerce_after_shop_loop_item hook.
		 *
		 * @hooked woocommerce_template_loop_product_link_close - 5
		 * @hooked woocommerce_template_loop_add_to_cart - 10
		 */
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
		if( ! mfn_opts_get('shop-button') ){
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
		}
		do_action( 'woocommerce_after_shop_loop_item' );
	?>

</li>

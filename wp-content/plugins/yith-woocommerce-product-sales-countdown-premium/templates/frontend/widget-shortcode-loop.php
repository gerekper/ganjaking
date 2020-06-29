<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>
	<div class="woocommerce ywpc-<?php echo $type; ?> <?php echo apply_filters( 'ywpc_' . $type . '_div_loop_class', '' ); ?>">
		<ul class="products ywpc-<?php echo $type; ?>-products <?php echo apply_filters( 'ywpc_' . $type . '_ul_loop_class', '' ); ?>">
			<?php

			while ( $products->have_posts() ) : $products->the_post(); ?>
				<?php

				global $product;

				$args = ywpc_get_product_args(  yit_get_product_id( $product ), $type );

				if ( $options['show_title'] != 'yes' ) {
					add_filter( 'the_title', array( YITH_WPC(), 'ywpc_loop_hide_filter' ), 999 );
				}

				if ( $options['show_rating'] != 'yes' ) {
					add_filter( 'woocommerce_product_get_rating_html', array( YITH_WPC(), 'ywpc_loop_hide_filter' ), 999 );
				}

				if ( $options['show_price'] != 'yes' ) {
					add_filter( 'woocommerce_get_price_html', array( YITH_WPC(), 'ywpc_loop_hide_filter' ), 999 );
				}

				if ( $options['show_image'] != 'yes' ) {
					add_filter( 'post_thumbnail_html', array( YITH_WPC(), 'ywpc_loop_hide_filter' ), 999 );
					add_filter( 'woocommerce_placeholder_img', array( YITH_WPC(), 'ywpc_loop_hide_filter' ), 999 );
				}

				if ( $options['show_addtocart'] != 'yes' ) {
					add_filter( 'woocommerce_loop_add_to_cart_link', array( YITH_WPC(), 'ywpc_loop_hide_filter' ), 999 );
				}

				$has_ywpc = yit_get_prop( $product, '_ywpc_enabled', true );

				if ( $has_ywpc != 'yes' ) {
					continue;
				}

				if ( isset ( $args['active_var'] ) && $args['active_var'] == 0 ) {
					continue;
				}

				if ( YITH_WPC()->check_ywpc_expiration( ( isset ( $args['active_var'] ) && $args['active_var'] != 0 ) ? $args['active_var'] : yit_get_product_id( $product ) ) ) {
					continue;
				};

				?>

				<?php wc_get_template( 'content-product.php' ); ?>

			<?php endwhile; ?>

		</ul>
	</div>
<?php

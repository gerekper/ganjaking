<?php
/**
 * Cross-sells
 *
 * @version     3.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $porto_settings;

if ( $cross_sells && $porto_settings['product-crosssell'] ) : ?>

	<div class="cross-sells">

		<h2 class="slider-title"><span class="inline-title"><?php esc_html_e( 'You may be interested in&hellip;', 'woocommerce' ); ?></span><span class="line"></span></h2>

		<div class="slider-wrapper">

			<?php
			global $porto_woocommerce_loop, $porto_layout;
			$porto_woocommerce_loop['view'] = 'products-slider';
			if ( in_array( $porto_layout, porto_options_sidebars() ) ) {
				$porto_woocommerce_loop['columns'] = 3;
			} else {
				$porto_woocommerce_loop['columns'] = 4;
			}
			$porto_woocommerce_loop['widget'] = true;

			woocommerce_product_loop_start();
			?>

				<?php foreach ( $cross_sells as $index => $cross_sell ) : ?>

					<?php

					if ( isset( $porto_settings['product-crosssell-count'] ) && $index >= (int) $porto_settings['product-crosssell-count'] ) {
						break;
					}
						$post_object = get_post( $cross_sell->get_id() );

						setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited, Squiz.PHP.DisallowMultipleAssignments.Found

						wc_get_template_part( 'content', 'product' );
					?>

				<?php endforeach; ?>

			<?php
			woocommerce_product_loop_end();
			?>

		</div>

	</div>
	<?php
endif;

wp_reset_postdata();

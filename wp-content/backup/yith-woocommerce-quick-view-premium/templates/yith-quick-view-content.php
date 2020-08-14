<?php
/**
 * Quick view content.
 *
 * @author  YITH
 * @package YITH WooCommerce Quick View
 * @version 1.0.0
 */
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

while ( have_posts() ) :
	the_post();
	?>
	<div class="product">
		<?php if ( ! post_password_required() ) { ?>

			<div id="product-<?php the_ID(); ?>" <?php post_class( 'product' ); ?>>
				<input type="hidden" id="yith_wcqv_product_id" value="<?php the_ID(); ?>"/>
				<?php do_action( 'yith_wcqv_product_image' ); ?>

				<?php do_action( 'yith_wcqv_before_product_summary' ); ?>

				<div class="summary entry-summary">
					<div class="summary-content">
						<?php do_action( 'yith_wcqv_product_summary' ); ?>
					</div>
				</div>

				<?php do_action( 'yith_wcqv_after_product_summary' ); ?>

			</div>

			<?php

		} else {
			echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
	</div>
<?php
endwhile; // end of the loop.

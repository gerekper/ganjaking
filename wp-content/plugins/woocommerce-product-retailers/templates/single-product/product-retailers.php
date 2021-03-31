<?php
/**
 * WooCommerce Product Retailers
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Retailers to newer
 * versions in the future. If you wish to customize WooCommerce Product Retailers for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-retailers/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Product page product retailers dropdown/button.
 *
 * @type \WC_Product_Retailers_Retailer[] $retailers Array of Product Retailers
 * @type bool $open_in_new_tab Whether to open a retailer in a new tab or not
 *
 * @version 1.7.2
 * @since 1.0.0
 */

global $product;

// hide the retailers selection for variable products
$style = $product->is_type( 'variable' ) ? 'style="display: none;"' : '';

// open links in new tab
$open_in_new_tab = 'yes' === get_option( 'wc_product_retailers_enable_new_tab' ) ? 'target="_blank"' : '';

?>
<div class="wc-product-retailers-wrap">

	<?php if ( count( $retailers ) > 1 ) : ?>

		<?php if ( ! \WC_Product_Retailers_Product::use_buttons( $product ) ) : ?>

			<select name="wc-product-retailers" class="wc-product-retailers" <?php echo $style; ?>>
				<option value=""><?php echo esc_html( \WC_Product_Retailers_Product::get_product_button_text( $product ) ); ?></option>
				<?php foreach ( $retailers as $retailer ) : ?>
						<option value="<?php echo esc_attr( $retailer->get_url() ); ?>" class="<?php echo esc_attr( $retailer->get_class() ); ?>"><?php echo esc_html( $retailer->get_label( $product ) ); ?></option>
				<?php endforeach; ?>
			</select>

		<?php else : ?>

			<?php if ( $button_text = trim( \WC_Product_Retailers_Product::get_product_button_text( $product ) ) ) : ?>

				<p><?php echo esc_html( $button_text ); ?></p>

			<?php endif; ?>

			<ul class="wc-product-retailers" <?php echo $style; ?>>
				<?php foreach ( $retailers as $retailer ) : ?>
					<li>
						<a href="<?php echo esc_url( $retailer->get_url() ); ?>" rel="nofollow" <?php echo $open_in_new_tab; ?> class="wc-product-retailers button alt <?php echo esc_attr( $retailer->get_class() ); ?>"><?php echo esc_html( $retailer->get_label( $product ) ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>

		<?php endif; ?>

	<?php elseif ( $retailer = current( $retailers ) ) : ?>

		<?php if ( is_object( $retailer ) && method_exists( current( $retailers ), 'get_label' ) ) : ?>

			<span class="wc-product-retailers" <?php echo $style; ?>>
				<a href="<?php echo esc_url( $retailer->get_url() ); ?>" rel="nofollow" <?php echo $open_in_new_tab; ?> class="wc-product-retailers button alt <?php echo esc_attr( $retailer->get_class() ); ?>"><?php echo esc_html( $retailer->get_label( $product, true ) ); ?></a>
			</span>

		<?php endif; ?>

	<?php endif; ?>

</div>

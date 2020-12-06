<?php
/**
 * WooCommerce Free Gift Coupons Cart Product Variation select template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce Free Gift Coupons will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce Free Gift Coupons/Templates
 * @since   3.1.0
 * @version 3.1.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'wc_fgc_before_single_cart_product' );

?>

<div class="wc_fgc_cart" id="wc_fgc_<?php echo $cart_item_key; ?>">

	<div class="wc-fgc-close-section">
		<button class="wc-fgc-close-btn">
			<span class="dashicons dashicons-after dashicons-no-alt"></span>
		</button>
	</div>

	<div class="wc-fgc-stock-error" style="display: none;"></div>

	<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

		<?php
		/**
		 * Hook: wc_fgc_before_single_product_summary.
		 *
		 * @hooked wc_fgc_template_display_product_image - 10
		 */
		 do_action( 'wc_fgc_before_single_product_summary' );
		?>

		<div class="summary entry-summary">
			<?php
			/**
			 * Hook: woocommerce_single_product_summary.
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 */
			 do_action( 'wc_fgc_single_product_summary' ); 
			?>
		</div>

	</div>
</div>

<?php 
do_action( 'wc_fgc_after_single_cart_product' );

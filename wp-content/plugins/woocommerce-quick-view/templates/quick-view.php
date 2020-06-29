<?php
/**
 * Quick view template
 *
 * Uses same hooks as single product template so more plugins will work with
 * quick view.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

// Change form action to avoid redirect to product page.
add_filter( 'woocommerce_add_to_cart_form_action', '__return_empty_string' );

do_action( 'wc_quick_view_before_single_product' );
?>
<div class="woocommerce quick-view single-product">

	<div id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>

		<?php
		/**
		 * Hook: woocommerce_before_single_product_summary.
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );
		?>

		<div class="summary entry-summary">
			<?php
			/**
			 * Hook: woocommerce_single_product_summary.
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 * @hooked woocommerce_template_single_meta - 40
			 * @hooked woocommerce_template_single_sharing - 50
			 * @hooked WC_Structured_Data::generate_product_data() - 60
			 */
			do_action( 'woocommerce_single_product_summary' );
			?>
		</div>
		<a class="quick-view-detail-button button alt images" href="<?php echo esc_attr( get_permalink( $product->get_id() ) ); ?>"><?php esc_attr_e( 'View Full Details', 'wc_quick_view' ); ?></a>
	</div>
</div>

<?php
remove_filter( 'woocommerce_add_to_cart_form_action', '__return_empty_string' );
?>

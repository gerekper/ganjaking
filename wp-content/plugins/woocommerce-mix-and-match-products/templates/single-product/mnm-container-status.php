<?php
/**
 * Mix and Match Product Add to Cart status template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm-container-status.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce Mix and Match/Templates
 * @since   2.2.0
 * @version 2.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="mnm_cart mnm_data mnm_status" data-for_container="<?php echo esc_attr( $product->get_id() ); ?>" <?php echo wc_implode_html_attributes( $product->get_data_attributes() ); // phpcs:ignore WordPress.Security.EscapeOutput ?> >

<?php

if ( $product->is_purchasable() ) {
	/**
	 * Hook: wc_mnm_before_container_status.
	 *
	 * @param  WC_Mix_and_Match  $product
	 */
	do_action( 'wc_mnm_before_container_status', $product );
	?>

		<p class="mnm_price" style="display:none;"></p>

		<div aria-live="polite" role="status" class="mnm_message woocommerce-message" style="display:none;">		
			<ul class="msg mnm_message_content">
				<li><?php echo esc_html( wc_mnm_get_quantity_message( $product ) ); ?></li>
			</ul>
		</div>

		<div class="mnm_availability">

		<?php echo wp_kses_post( wc_get_stock_html( $product ) ); ?>
			
		</div>

		<?php
		/**
		 * Hook: wc_mnm_after_container_status.
		 *
		 * @param  WC_Mix_and_Match  $product
		 */
		do_action( 'wc_mnm_after_container_status', $product );

		?>

<?php } else { ?>

	<p class="mnm_container_unavailable stock out-of-stock"><?php echo wp_kses_post( $purchasable_notice ); ?></p>

<?php } ?>

</div>

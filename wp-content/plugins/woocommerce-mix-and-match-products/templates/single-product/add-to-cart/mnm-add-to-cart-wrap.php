<?php
/**
 * Mix and Match Product Add to Cart button wrapper template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/mnm-add-to-cart-wrap.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Kathy Darling
 * @package WooCommerce Mix and Match/Templates
 * @since   1.3.0
 * @version 1.9.5
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit;
}
?>

<?php 

if ( $product->is_purchasable() && $product->is_in_stock() ) { ?>

	<div class="mnm_cart mnm_data cart" <?php echo $product->get_data_attributes(); ?>>

	<?php

		/**
		 * wc_mnm_before_add_to_cart_button_wrap hook.
		 */
		do_action( 'wc_mnm_before_add_to_cart_button_wrap' );
	?>

		<div class="mnm_button_wrap" style="display:block">

			<div class="mnm_price"></div>

			<div class="mnm_message woocommerce-info">
				<ul class="msg mnm_message_content">
					<li><?php echo wc_mnm_get_quantity_message( $product ); ?></li>
				</ul>
			</div>

			<?php

			// MnM Availability.
			?>
			<div class="mnm_availability">

				<?php

				// Availability html.
				echo wc_get_stock_html( $product );

				?>
				
			</div>
			<?php

			/**
			 * woocommerce_before_add_to_cart_button hook.
			 */
			do_action( 'woocommerce_before_add_to_cart_button' ); 

			/**
			 * @since 1.4.0.
			 */
			do_action( 'woocommerce_before_add_to_cart_quantity' );

	 		woocommerce_quantity_input( array(
	 			'min_value' => $product->is_sold_individually() ? 1 : apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
	 			'max_value' => $product->is_sold_individually() ? 1 : apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ),
	 			'input_value' => isset( $_REQUEST['quantity'] ) ? wc_stock_amount( wp_unslash( $_REQUEST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
	 			) );

			/**
			 * @since 1.4.0.
			 */
	 		do_action( 'woocommerce_after_add_to_cart_quantity' );
	 		?>

	 		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />

			<button type="submit" class="single_add_to_cart_button mnm_add_to_cart_button button alt" value="<?php echo esc_attr( $product->get_id() ); ?>" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"><?php echo $product->single_add_to_cart_text(); ?></button>

			<?php 
			/**
			 * woocommerce_after_add_to_cart_button hook.
			 */
			do_action( 'woocommerce_after_add_to_cart_button' ); 
			?>

		</div>
	

		<?php 
		/**
		 * wc_mnm_after_add_to_cart_button_wrap hook.
		 */
		do_action( 'wc_mnm_after_add_to_cart_button_wrap' );

		?>

	</div>

<?php } else { ?>

	<p class="mnm_container_unavailable stock out-of-stock"><?php esc_html_e( 'This product is currently unavailable.', 'woocommerce-mix-and-match-products' ); ?></p>

<?php } ?>



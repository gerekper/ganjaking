<?php
/**
 * Mix and Match Product Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/mnm.php.
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
 * @since   1.0.0
 * @version 1.8.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit;
}
 
global $product;

/**
 * woocommerce_before_add_to_cart_form hook.
 */
do_action( 'woocommerce_before_add_to_cart_form' ); 
?>

<form method="post" enctype="multipart/form-data" class="mnm_form cart cart_group <?php echo esc_attr( $classes ); ?>">

	<?php

	/**
	 * 'woocommerce_mnm_content_loop' action.
	 *
	 * @param  WC_Mix_and_Match  $product
	 * @since  1.8.0
	 *
	 * @hooked woocommerce_mnm_content_loop - 10
	 */
	do_action( 'woocommerce_mnm_content_loop', $product );

	/**
	 * 'woocommerce_mnm_add_to_cart_wrap' action.
	 *
	 * @param  WC_Mix_and_Match  $product
	 * @since  1.3.0
	 *
	 * @hooked wc_mnm_template_reset_link 		- 10
	 * @hooked wc_mnm_template_add_to_cart_wrap - 20
	 */
	do_action( 'woocommerce_mnm_add_to_cart_wrap', $product );

	?>

</form>

<?php 
/**
 * woocommerce_after_add_to_cart_form hook.
 */
do_action( 'woocommerce_after_add_to_cart_form' ); 
?>
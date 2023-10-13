<?php
/**
 * Mix and Match Item Product Title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm/mnm-product-title.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce Mix and Match/Templates
 * @since   1.0.0
 * @version 2.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h4 class="<?php echo esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ); ?>"><?php echo $permalink ? '<a href="' . esc_url( $permalink ) . '" target="_blank">' . wp_kses_post( WC_Mix_and_Match_Helpers::format_product_title( $title, $quantity ) ) . '</a>' : wp_kses_post( WC_Mix_and_Match_Helpers::format_product_title( $title, $quantity ) ); ?> </h4>

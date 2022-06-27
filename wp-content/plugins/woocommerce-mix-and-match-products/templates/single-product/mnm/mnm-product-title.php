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
 * @version 2.0.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h4 class="woocommerce-loop-product__title product_title entry-title"><?php echo $child_item->get_product()->is_visible() ? '<a href="' . $child_item->get_product()->get_permalink() . '" target="_blank">' . WC_Mix_and_Match_Helpers::format_product_title( $title, $quantity ) . '</a>' : WC_Mix_and_Match_Helpers::format_product_title( $title, $quantity ); ?> </h4>

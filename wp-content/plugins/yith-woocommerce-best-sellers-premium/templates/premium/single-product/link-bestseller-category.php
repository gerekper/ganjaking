<?php
/**
 * Template of link best seller category
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */

$bestsellers_page_id        = get_option( 'yith-wcbsl-bestsellers-page-id' );
$bestsellers_page_permalink = $bestsellers_page_id ? get_permalink( $bestsellers_page_id ) : '#';
$bestsellers_page_permalink .= !empty( $cat_id ) ? '?bs_cat=' . $cat_id : '';

$text_top_100 = sprintf( __( 'show %1$s Top %2$s', 'yith-woocommerce-best-sellers' ), $cat_name, YITH_WCBSL()->get_limit() );

echo '<div class="yith-wcbsl-bestseller-positioning-in-product-wrapper">';
echo "<a href='{$bestsellers_page_permalink}'>{$text_top_100}</a>";
echo '</div>';

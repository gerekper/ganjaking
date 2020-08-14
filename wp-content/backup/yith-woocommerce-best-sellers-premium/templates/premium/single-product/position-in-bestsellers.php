<?php
/**
 * Template of position in Best Sellers
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */

$bestsellers_page_id        = get_option( 'yith-wcbsl-bestsellers-page-id' );
$bestsellers_page_permalink = $bestsellers_page_id ? get_permalink( $bestsellers_page_id ) : '#';


if ( !empty( $bestseller_in ) ) {
    echo '<div class="yith-wcbsl-bestseller-positioning-in-product-wrapper">';
    echo '<h4>' . __( 'Best Seller position', 'yith-woocommerce-best-sellers' ) . '</h4>';
    foreach ( $bestseller_in as $bs ) {
        echo '<p>';
        $position = '<strong>' . $bs[ 'position' ] . '</strong>';
        if ( $bs[ 'title' ] == 'yith_wcbsl_all' ) {
            $text_top_100 = sprintf(__( 'show Top %s', 'yith-woocommerce-best-sellers' ), YITH_WCBSL()->get_limit());
            $link_top_100 = "<a href='{$bestsellers_page_permalink}'>{$text_top_100}</a>";
            echo sprintf( __( 'No. %1$s in %2$s', 'yith-woocommerce-best-sellers' ), $position, $link_top_100 );
        } else {
            $text_top_100 = $bs[ 'title' ];
            $cat_id       = $bs[ 'cat_id' ];
            $link_top_100 = "<a href='{$bestsellers_page_permalink}?bs_cat={$cat_id}'>{$text_top_100}</a>";
            echo sprintf( __( 'No. %1$s in %2$s', 'yith-woocommerce-best-sellers' ), $position, $link_top_100 );
        }
        echo '</p>';
    }
    echo "</div>";
}
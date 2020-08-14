<?php
/**
 * Template of Best Sellers
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */

$product_args = array(
    'post_per_page' => -1,
    'post_type'     => 'product',
    'post_status'   => 'publish'
);

$reports      = new YITH_WCBSL_Reports();
$best_sellers = $reports->get_best_sellers();
$best_sellers_per_page = 25;

if ( !empty( $best_sellers ) ) {

    $current_page       = max( 1, get_query_var( 'paged' ) );
    $best_sellers_pages = array_chunk( $best_sellers, $best_sellers_per_page );
    $total_pages        = count( $best_sellers_pages );

    $best_sellers_current_page = isset( $best_sellers_pages[ $current_page - 1 ] ) ? $best_sellers_pages[ $current_page - 1 ] : array();

    if ( !empty( $best_sellers_current_page ) ) {
        echo '<div class="yith-wcbsl-bestsellers-wrapper">';
        $loop = ( $current_page - 1 ) * $best_sellers_per_page;
        foreach ( $best_sellers_current_page as $best_seller ) {
            $loop++;
            $bs_id  = absint( $best_seller->product_id );
            $bs_qty = $best_seller->order_item_qty;
            $args   = array(
                'id'   => $bs_id,
                'qty'  => $bs_qty,
                'loop' => $loop,
            );

            wc_get_template( '/bestseller.php', $args, YITH_WCBSL_TEMPLATE_PATH, YITH_WCBSL_TEMPLATE_PATH );
        }

        $args = array(
            'current' => $current_page,
            'total'   => $total_pages
        );

        wc_get_template( '/pagination.php', $args, YITH_WCBSL_TEMPLATE_PATH, YITH_WCBSL_TEMPLATE_PATH );

        echo '</div>';
    }
}

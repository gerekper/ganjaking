<?php
/**
 * Template of Best Sellers
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */

$reports                 = new YITH_WCBSL_Reports_Premium();
$best_sellers            = array();
$last_best_sellers_array = array();
$is_cat                  = isset( $_GET[ 'bs_cat' ] ) ? true : false;
$cat_id                  = 0;
$selected_categories     = !empty( $cats ) ? explode( ',', $cats ) : array();
$show_newest_bestsellers = isset( $_GET[ 'newest' ] ) ? true : false;

$range      = get_option( 'yith-wcpsc-update-time', '7day' );
$range_args = array();
$limit      = YITH_WCBSL()->get_limit();

if ( false && current_user_can( 'manage_options' ) ) {
    $limit = -1;
}

// Check category exists
if ( $is_cat ) {
    $cat_id = absint( $_GET[ 'bs_cat' ] );
    $cat    = get_term( $cat_id, 'product_cat' );
    if ( !$cat ) {
        $cat_id = 0;
        $is_cat = false;
    }
}

if ( $show_newest_bestsellers ) {
    $best_sellers = $reports->get_newest_bestsellers( $range, $range_args, $limit );
} elseif ( !empty( $selected_categories ) ) {
    $best_sellers = $reports->get_best_sellers_in_category( $selected_categories, $range, $range_args, array( 'limit' => $limit ) );
} elseif ( $is_cat ) {
    $cat_id       = absint( $_GET[ 'bs_cat' ] );
    $best_sellers = $reports->get_best_sellers_in_category( $cat_id, $range, $range_args, array( 'limit' => $limit ) );
} else {
    $best_sellers = $reports->get_best_sellers( $range, array( 'range_args' => $range_args, 'limit' => $limit ) );
}
$best_sellers_per_page = 25;


/* LAST BEST SELLERS */
if ( $range != 'ever' ) {
    $last_range        = 'last_' . $range;
    $last_best_sellers = array();
    if ( $show_newest_bestsellers ) {
        $last_best_sellers = $reports->get_newest_bestsellers( $last_range, $range_args, $limit );
    } elseif ( !empty( $selected_categories ) ) {
        $last_best_sellers = $reports->get_best_sellers_in_category( $selected_categories, $last_range, $range_args, array( 'limit' => $limit ) );
    } elseif ( $is_cat ) {
        $cat_id            = absint( $_GET[ 'bs_cat' ] );
        $last_best_sellers = $reports->get_best_sellers_in_category( $cat_id, $last_range, $range_args, array( 'limit' => $limit ) );
    } else {
        $last_best_sellers = $reports->get_best_sellers( $last_range, array( 'range_args' => $range_args, 'limit' => $limit ) );
    }
    if ( !empty( $last_best_sellers ) ) {
        foreach ( $last_best_sellers as $l_bs ) {
            $last_best_sellers_array[ absint( $l_bs->product_id ) ] = $l_bs->order_item_qty;
        }
    }
}
/* ----------------- */

echo '<div class="yith-wcbsl-subtitle-and-feed-container">';
echo '<div class="yith-wcbsl-subtitle-container">';
if ( $show_newest_bestsellers ) {
    echo '<h5 class="yith-wcbsl-categories-subtitle">' . __( 'Newest Products in Top 100', 'yith-woocommerce-best-sellers' ) . '</h5>';
} elseif ( $is_cat ) {
    $cat = get_term( $cat_id, 'product_cat' );
    echo '<h5 class="yith-wcbsl-categories-subtitle">' . sprintf( __( 'Best Sellers in category %s', 'yith-woocommerce-best-sellers' ), '<strong>' . $cat->name . '</strong>' ) . '</h5>';
}
echo '</div>';
do_action( 'yith_wcbsl_rss_link', $cat_id, $show_newest_bestsellers );

echo '</div>';

if ( !empty( $best_sellers ) ) {

    /**
     * WPML
     */
    global $sitepress;
    $wpml_best_sellers = array();
    if ( !empty( $sitepress ) ) {
        foreach ( $best_sellers as $best_seller ) {
            $product_id = YITH_WCBSL_WPML_Integration()->get_current_language_id( $best_seller->product_id );
            if ( !isset( $wpml_best_sellers[ $product_id ] ) ) {
                $wpml_best_sellers[ $product_id ] = $best_seller;
            } else {
                $wpml_best_sellers[ $product_id ]->order_item_qty += $best_seller->order_item_qty;
            }
        }
        $best_sellers = $wpml_best_sellers;
    }
    /**
     * WPML - END
     */


    $current_page       = max( 1, get_query_var( 'paged' ) );
    $best_sellers_pages = array_chunk( $best_sellers, $best_sellers_per_page );
    $total_pages        = count( $best_sellers_pages );

    $best_sellers_current_page = isset( $best_sellers_pages[ $current_page - 1 ] ) ? $best_sellers_pages[ $current_page - 1 ] : array();

    if ( !empty( $best_sellers_current_page ) ) {
        echo '<div class="yith-wcbsl-bestsellers-wrapper">';
        $loop = ( $current_page - 1 ) * $best_sellers_per_page;
        foreach ( $best_sellers_current_page as $bestseller ) {
            $loop++;
            $bs_id  = absint( $bestseller->product_id );
            $bs_qty = $bestseller->order_item_qty;
            $args   = array(
                'id'                => $bs_id,
                'qty'               => $bs_qty,
                'loop'              => $loop,
                'last_best_sellers' => $last_best_sellers_array
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
} else {
    echo '<p>';
    _e( 'No Best Sellers found', 'yith-woocommerce-best-sellers' );
    echo '</p>';
}
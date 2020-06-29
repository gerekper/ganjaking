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


$description         = __( 'Best Sellers', 'yith-woocommerce-best-sellers' );
$bestsellers_page_id = get_option( 'yith-wcbsl-bestsellers-page-id' );
$link                = get_permalink( $bestsellers_page_id ) . '?feed=rss2';

if ( $is_cat ) {
    $cat         = get_term( $cat_id, 'product_cat' );
    $description = sprintf( __( 'Best Sellers in category %s', 'yith-woocommerce-best-sellers' ), $cat->name);
    $link .= '&bs_cat=' . $cat_id;
}

header( 'Content-Type: application/rss+xml; charset=' . get_option( 'blog_charset' ), true );
echo '<?xml version="1.0" encoding="' . get_bloginfo( 'charset' ) . "\" ?>\n";
?>
    <rss version="2.0">
        <channel>
            <title><?php bloginfo_rss( 'name' ); ?></title>
            <link><?php bloginfo_rss( 'url' ); ?></link>
            <description><?php echo $description ?></description>
            <pubDate><?php echo date( 'D, d M Y H:i:s +0000' ); ?></pubDate>
            <language><?php bloginfo_rss( 'language' ); ?></language>
            <?php
            if ( !empty( $best_sellers ) ) {
                $loop = 0;
                foreach ( $best_sellers as $bestseller ) {
                    $loop++;
                    $bs_id  = absint( $bestseller->product_id );
                    $bs_qty = $bestseller->order_item_qty;

                    $post = get_post( $bs_id );

                    $admin_desc = '';
                    if ( current_user_can( 'manage_options' ) ) {
                        $admin_desc = ' [' . number_format_i18n( $bs_qty ) . ' ' . _n( 'sale', 'sales', $bs_qty, 'yith-woocommerce-best-sellers' ). ']';
                    }

                    ?>
                    <item>
                        <title><?php echo '<![CDATA[' . $loop . '. ' . get_the_title( $post->ID ) . $admin_desc . ']]>'; ?></title>
                        <link><?php echo get_permalink( $post->ID ); ?></link>
                        <description><?php echo '<![CDATA[' . yith_wcbsl_rss_text_limit( $post->post_content, 500 ) . '<br/>' . __( 'show ', 'yith-woocommerce-best-sellers' ) . ' <a href="' . get_permalink( $post->ID ) . '">' . get_the_title( $post->ID ) . '</a>' . ']]>'; ?></description>
                        <pubDate><?php echo date( 'D, d M Y H:i:s +0000', strtotime( $post->post_date_gmt ) ); ?></pubDate>
                        <guid><?php echo get_permalink( $post->ID ); ?></guid>
                    </item>
                    <?php
                }
            }
            ?>
        </channel>
    </rss>
<?php

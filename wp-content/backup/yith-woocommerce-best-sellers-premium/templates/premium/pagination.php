<?php
/**
 * Pagination - Show numbered pagination for Best Sellers page.
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers
 * @version 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<nav class="yith-wcbsl-pagination" >
    <?php
    echo paginate_links( apply_filters( 'yith_wcbsl_pagination_args', array(
        'base'      => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
        'format'    => '',
        'add_args'  => '',
        'current'   => max( 1, $current ),
        'total'     => $total,
        'prev_text' => '&larr;',
        'next_text' => '&rarr;',
        'type'      => 'list',
        'show_all' => true
    ) ) );
    ?>
</nav>
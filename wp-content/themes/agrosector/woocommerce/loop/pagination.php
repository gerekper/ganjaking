<?php
/**
 * Pagination - Show numbered pagination for catalog pages
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/pagination.php.
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$total   = isset( $total ) ? $total : wc_get_loop_prop( 'total_pages' );
$current = isset( $current ) ? $current : wc_get_loop_prop( 'current_page' );
$base    = isset( $base ) ? $base : esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
$format  = isset( $format ) ? $format : '';

if ( $total <= 1 ) {
	return;
}

?>
<nav class="woocommerce-pagination">
	<?php $page_links = paginate_links( apply_filters( 'woocommerce_pagination_args', array(
		'base'               => $base,
		'format'             => $format,
		'add_args'           => false,
		'current'            => max( 1, $current ),
		'total'              => $total,
		'prev_text'          => '<span>'.esc_html__( 'Prev', 'agrosector' ).'</span><i class="fa fa-angle-left"></i><span class="gt3_pagination_delimiter"></span>',
		'next_text'          => '<span class="gt3_pagination_delimiter"></span><i class="fa fa-angle-right"></i><span>'.esc_html__( 'Next', 'agrosector' ).'</span>',
		'type'               => 'array',
		'end_size'           => 2,
		'mid_size'           => 1,
		'before_page_number' => '<span class="gt3_pagination_last_text">'.esc_html__( 'View the Last Page', 'agrosector' ).'</span><span class="gt3_pagination_text">'.esc_html__( 'View Page ', 'agrosector' ).'</span><span class="gt3_pagination_current">',
		'after_page_number'  => '</span><span class="gt3_pagination_text"> '.esc_html__( 'of ', 'agrosector' ).'</span>',
	) ) );

	?>

    <ul class='page-numbers'>
	    <?php foreach ( $page_links as $key => $page_link ) {
		    $class = strpos($page_link,'page-numbers current') !== false ? 'gt3_current ' : '';

		    end($page_links);
		    $class .= $key === key($page_links) ? 'gt3_last ' : '';

		    $class .= strpos($page_link,'next page-numbers') !== false || strpos($page_link,'prev page-numbers') !== false ? 'gt3_prev_next ' : 'gt3_page-numbers ';
		    echo "<li class='".esc_attr($class)."'>$page_link</li>"; ?>
	    <?php } ?>
        <li class="gt3_show_all_li <?php if( ! empty( $_GET['post_type'] ) && $_GET['post_type'] === 'product' && '' === get_option( 'permalink_structure' ) && get_post_type_archive_link( 'product' ) || is_search() ) echo 'hidden'; ?>">
            <span>
                <span class="gt3_pagination_delimiter"></span>
                <a class="gt3_show_all<?php echo ($current === 1 ? ' first-page' : ''); ?>" href="<?php echo ($current === 1 ? '#' : get_pagenum_link(1)); ?>" title="<?php echo esc_attr('Show all products', 'agrosector'); ?>"><?php esc_html_e('View All', 'agrosector'); ?></a>

            </span>
        </li>
    </ul>
</nav>

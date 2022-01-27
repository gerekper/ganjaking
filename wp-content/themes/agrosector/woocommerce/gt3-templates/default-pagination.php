<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $products;
if ( $products->max_num_pages <= 1 ) {
	return;
}
?>
    <nav class="woocommerce-pagination">
		<?php
		$page_links = paginate_links( array(
			'base'               => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
			'format'             => '',
			'add_args'           => false,
			'current'            => max( 1, get_query_var( 'paged' ) ),
			'total'              => $products->max_num_pages,
			'prev_text'          => '<i class="fa fa-angle-left"></i><span>'.esc_html__( 'Prev', 'agrosector' ).'</span>',
			'next_text'          => '<span>'.esc_html__( 'Next', 'agrosector' ).'</span><i class="fa fa-angle-right"></i>',
			'type'               => 'list',
			'end_size'           => 2,
			'mid_size'           => 1,
			'before_page_number' => '',
			'after_page_number'  => '',
		) );

		echo $page_links;
		?>
    </nav>
<?php

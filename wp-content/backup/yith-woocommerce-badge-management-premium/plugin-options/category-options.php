<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$custom_attributes = defined( 'YITH_WCBM_PREMIUM' ) ? '' : array( 'disabled' => 'disabled' );

$pagination               = new StdClass();
$pagination->totals       = apply_filters( 'yith_wcbm_category_pagination_totals', wp_count_terms( 'product_cat', array( 'hide_empty' => false ) ) );
$pagination->per_page     = apply_filters( 'yith_wcbm_settings_categories_per_page', 50 );
$pagination->current_page = max( 1, absint( isset( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : 1 ) );
$pagination->pages        = ceil( $pagination->totals / $pagination->per_page );
$pagination->current_page = min( $pagination->current_page, $pagination->pages );

$pagination_html = "<div class='alignright actions'>";
$pagination_html .= "<form method='post'>";
$pagination_html .= "<span class='displaying-num'>" . sprintf( _n( '%s category', '%s categories', 'yith-woocommerce-badges-management' ), $pagination->totals ) . "</span>";
$pagination_html .= "<span class='pagination'>";
if ( $pagination->pages > 1 ) {
	$first = "<span class='navspan first' aria-hidden='true'>«</span>";
	$prev  = "<span class='navspan prev' aria-hidden='true'>‹</span>";
	$next  = "<span class='navspan next' aria-hidden='true'>›</span>";
	$last  = "<span class='navspan last' aria-hidden='true'>»</span>";
	if ( $pagination->current_page > 1 ) {
		//PREV
		$prev_url = add_query_arg( array( 'paged' => $pagination->current_page - 1 ) );
		$prev     = "<a href='$prev_url'>$prev</a>";

		$first_url = add_query_arg( array( 'paged' => 1 ) );
		$first     = "<a href='$first_url'>$first</a>";
	}

	if ( $pagination->current_page < $pagination->pages ) {
		//NEXT
		$prev_url = add_query_arg( array( 'paged' => $pagination->current_page + 1 ) );
		$next     = "<a href='$prev_url'>$next</a>";

		$last_url = add_query_arg( array( 'paged' => $pagination->pages ) );
		$last     = "<a href='$last_url'>$last</a>";
	}

	$current = "<span class='current-page'>";
	$current .= "<input type='text' name='paged' value='{$pagination->current_page}' size='3' />";
	$current .= "<span class='paging-text'> of {$pagination->pages}</span>";
	$current .= "</span>";

	$pagination_html .= $first . $prev . $current . $next . $last;
}

$pagination_html .= "</span>";
$pagination_html .= "</form>";
$pagination_html .= "</div>";

//get categories of products and create an array of catagories
$cat_args = array(
	'taxonomy'     => 'product_cat',
	'orderby'      => 'name',
	'order'        => 'ASC',
	'hide_empty'   => false,
	'hierarchical' => true,
	'number'       => apply_filters( 'yith_wcbm_number_cat_to_show', $pagination->per_page ),
	'offset'       => ( $pagination->current_page - 1 ) * $pagination->per_page,
);


$list_category_opt = array(
	'category-badge-options'    => array(
		'title' => __( 'Category Badges', 'yith-woocommerce-badges-management' ),
		'type'  => 'title',
		'desc'  => '',
		'id'    => 'yith-wcbm-category-badge-options',
	),
	'category-badge-pagination' => array(
		'type'             => 'yith-field',
		'yith-type'        => 'html',
		'html'             => $pagination_html,
		'yith-display-row' => true,
	),
);

$categories = get_categories( $cat_args );
foreach ( $categories as $cat ) {
	$id   = $cat->term_id;
	$name = $cat->name;

	$list_category_opt[ 'category-badge-' . $id ] = array(
		'name'              => $name,
		'type'              => 'yith-field',
		'yith-type'         => 'custom',
		'action'            => 'yith_wcbm_print_badges_select',
		'desc'              => sprintf( __( 'Select the Badge for all products of category %s', 'yith-woocommerce-badges-management' ), $name ),
		'id'                => 'yith-wcbm-category-badge-' . $id,
		'custom_attributes' => $custom_attributes,
		'default'           => 'none',
	);
}

$list_category_opt['category-badge-pagination-bottom'] = array(
	'type'             => 'yith-field',
	'yith-type'        => 'html',
	'html'             => $pagination_html,
	'yith-display-row' => true,
);

$list_category_opt['category-badge-options-end'] = array(
	'type' => 'sectionend',
	'id'   => 'yith-wcbm-category-badge-options',
);

$settings = array(
	'category' => $list_category_opt,
);

return $settings;
<?php

extract(
	shortcode_atts(
		array(
			'category_orderby' => 'name',
			'category_order'   => '',
			'hide_empty'       => 'yes',
			'show_products'    => 'yes',
			'infinite_scroll'  => '',
			'view'             => 'products-slider',
			'count'            => '',
			'columns'          => 4,
			'columns_mobile'   => '',
			'column_width'     => '',
			'product_orderby'  => '',
			'product_order'    => '',
			'addlinks_pos'     => '',
			'image_size'       => '',
			'navigation'       => 1,
			'nav_pos'          => '',
			'nav_pos2'         => '',
			'nav_type'         => '',
			'show_nav_hover'   => false,
			'pagination'       => 0,
			'dots_pos'         => '',
			'dots_style'       => '',
			'autoplay'         => 'yes',
			'autoplay_timeout' => 5000,
			'el_class'         => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

$wrapper_classes = 'porto-onepage-category';
if ( $show_products ) {
	$wrapper_classes .= ' show-products';
}
if ( $el_class ) {
	$wrapper_classes .= ' ' . $el_class;
}
if ( $infinite_scroll ) {
	$wrapper_classes .= ' ajax-load';
}

$column_class = '';
switch ( $columns ) {
	case 1:
		$cols_md = 1;
		$cols_xs = 1;
		$cols_ls = 1;
		break;
	case 2:
		$cols_md = 2;
		$cols_xs = 2;
		$cols_ls = 1;
		break;
	case 3:
		$cols_md = 3;
		$cols_xs = 2;
		$cols_ls = 1;
		break;
	case 4:
		$cols_md = 3;
		$cols_xs = 2;
		$cols_ls = 1;
		break;
	case 5:
		$cols_md = 3;
		$cols_xs = 2;
		$cols_ls = 1;
		break;
	case 6:
		$cols_md = 5;
		$cols_xs = 3;
		$cols_ls = 2;
		break;
	case 7:
		$cols_md = 6;
		$cols_xs = 3;
		$cols_ls = 2;
		break;
	case 8:
		$cols_md = 6;
		$cols_xs = 3;
		$cols_ls = 2;
		break;
	default:
		$columns = 4;
		$cols_md = 3;
		$cols_xs = 2;
		$cols_ls = 1;
}
$subcategory_class = 'sub-category products pcols-lg-' . $columns . ' pcols-md-' . $cols_md . ' pcols-xs-' . $cols_xs . ' pcols-ls-' . $cols_ls;

$output          = '';
$output         .= '<div class="' . esc_attr( $wrapper_classes ) . '">';
$terms           = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'parent'     => 0,
		'hide_empty' => ( 'yes' == $hide_empty ? true : false ),
		'orderby'    => $category_orderby,
		'order'      => $category_order,
	)
);
	$output     .= '<nav class="category-list">';
		$output .= '<ul class="product-cats" data-plugin-sticky data-plugin-options="' . esc_attr( '{"autoInit": true, "minWidth": 767, "containerSelector": "' . ( $show_products ? '.porto-onepage-category' : '#main' ) . '","autoFit":true, "paddingOffsetTop": 1}' ) . '">';
foreach ( $terms as $term_cat ) {
	if ( 'Uncategorized' == $term_cat->name ) {
		continue;
	}
	$id      = $term_cat->term_id;
	$name    = $term_cat->name;
	$slug    = $term_cat->slug;
	$output .= '<li><a class="nav-link ' . esc_attr( $slug ) . '" href="' . ( $show_products ? '#category-' . esc_attr( $term_cat->slug ) : esc_url( get_term_link( $id, 'product_cat' ) ) ) . '" data-cat_id="' . esc_attr( $slug ) . '">';
	$icon    = get_metadata( 'product_cat', $term_cat->term_id, 'category_icon', true );
	if ( $icon ) {
		$output .= '<span class="category-icon"><i class="' . esc_attr( $icon ) . '"></i></span>';
	} else {
		$thumbnail_id = get_term_meta( $term_cat->term_id, 'thumbnail_id', true );
		$image        = wp_get_attachment_image_src( $thumbnail_id );
		if ( $thumbnail_id && $image ) {
			$output .= '<span class="category-icon"><img src="' . esc_url( $image[0] ) . '" alt="' . esc_html( $name ) . '" width="' . esc_attr( $image[1] ) . '" height="' . $image[2] . '" /></span>';
		}
	}
	$output .= '<span class="category-title">' . esc_html( $name ) . '</span></a></li>';
}
		$output .= '</ul>';
	$output     .= '</nav>';

if ( $show_products && ! empty( $terms ) ) {
	$output         .= '<div class="category-details">';
		$output     .= '<form class="ajax-form d-none">';
			$output .= '<input type="hidden" name="count" value="' . esc_attr( $count ) . '" >';
			$output .= '<input type="hidden" name="orderby" value="' . esc_attr( $product_orderby ) . '" >';
			$output .= '<input type="hidden" name="order" value="' . esc_attr( $product_order ) . '" >';
			$output .= '<input type="hidden" name="columns" value="' . esc_attr( $columns ) . '" >';
			$output .= '<input type="hidden" name="view" value="' . esc_attr( $view ) . '" >';
			$output .= '<input type="hidden" name="navigation" value="' . esc_attr( $navigation ) . '" >';
	if ( $addlinks_pos ) {
		$output .= '<input type="hidden" name="addlinks_pos" value="' . esc_attr( $addlinks_pos ) . '" >';
	}
	if ( $nav_pos ) {
		$output .= '<input type="hidden" name="nav_pos" value="' . esc_attr( $nav_pos ) . '" >';
	}
	if ( $nav_pos2 ) {
		$output .= '<input type="hidden" name="nav_pos2" value="' . esc_attr( $nav_pos2 ) . '" >';
	}
	if ( $nav_type ) {
		$output .= '<input type="hidden" name="nav_type" value="' . esc_attr( $nav_type ) . '" >';
	}
	if ( $show_nav_hover ) {
		$output .= '<input type="hidden" name="show_nav_hover" value="' . esc_attr( $show_nav_hover ) . '" >';
	}
			$output .= '<input type="hidden" name="pagination" value="' . esc_attr( $pagination ) . '" >';
	if ( $dots_pos ) {
		$output .= '<input type="hidden" name="dots_pos" value="' . esc_attr( $dots_pos ) . '" >';
	}
	if ( $dots_style ) {
		$output .= '<input type="hidden" name="dots_style" value="' . esc_attr( $dots_style ) . '" >';
	}
	if ( $image_size ) {
		$output .= '<input type="hidden" name="image_size" value="' . esc_attr( $image_size ) . '" >';
	}
	if ( 'products-slider' == $view ) {
		if ( ! $autoplay ) {
			$output .= '<input type="hidden" name="autoplay" value="" >';
		}
		if ( 5000 !== intval( $autoplay_timeout ) ) {
			$output .= '<input type="hidden" name="autoplay_timeout" value="' . esc_attr( $autoplay_timeout ) . '" >';
		}
	}
		$output .= '</form>';

		$is_first = true;
	foreach ( $terms as $term_cat ) {
		if ( 'Uncategorized' == $term_cat->name ) {
			continue;
		}
		$output                      .= '<section id="category-' . esc_attr( $term_cat->slug ) . '" class="category-section' . ( $infinite_scroll && $is_first ? ' ajax-loaded' : '' ) . '">';
			$output                  .= '<div class="category-title">';
				$output              .= '<div class="dropdown">';
					$child_categories = wp_list_categories(
						array(
							'child_of'            => $term_cat->term_id,
							'echo'                => false,
							'taxonomy'            => 'product_cat',
							'hide_title_if_empty' => true,
							'title_li'            => '',
							'show_option_none'    => '',
							'orderby'             => $category_orderby,
							'order'               => $category_order,
						)
					);
					$output          .= '<h4 class="cat-title dropdown-toggle' . ( $child_categories ? ' has-sub-cat' : '' ) . '" data-display="static" data-bs-toggle="dropdown" aria-expanded="false"><span>' . esc_html( $term_cat->name ) . '</span></h4>';
		if ( $child_categories ) {
			$output .= '<ul class="dropdown-menu ' . $subcategory_class . '">' . $child_categories . '</ul>';
		}
				$output .= '</div>';
				$output .= '<div class="category-link"><a href="' . esc_url( get_term_link( $term_cat->term_id, 'product_cat' ) ) . '" class="btn btn-modern btn-dark">' . esc_html__( 'View All', 'porto-functionality' ) . '</a></div>';
			$output     .= '</div>';

		if ( $infinite_scroll && $is_first ) {
			$attrs_escaped = 'per_page="' . intval( $count ) . '" columns="' . intval( $columns ) . '" orderby="' . esc_attr( $product_orderby ) . '" order="' . esc_attr( $product_order ) . '" category="' . esc_attr( $term_cat->slug ) . '"';
			if ( $view ) {
				$attrs_escaped .= ' view="' . esc_attr( $view ) . '"';
			}
			if ( $addlinks_pos ) {
				$attrs_escaped .= ' addlinks_pos="' . esc_attr( $addlinks_pos ) . '"';
			}
			if ( $columns_mobile ) {
				$attrs_escaped .= ' columns_mobile="' . esc_attr( $columns_mobile ) . '"';
			}
			if ( $column_width ) {
				$attrs_escaped .= ' column_width="' . esc_attr( $column_width ) . '"';
			}
			if ( $image_size ) {
				$attrs_escaped .= ' image_size="' . esc_attr( $image_size ) . '"';
			}
			if ( $navigation ) {
				$attrs_escaped .= ' navigation="' . esc_attr( $navigation ) . '"';
			}
			if ( $nav_pos ) {
				$attrs_escaped .= ' nav_pos="' . esc_attr( $nav_pos ) . '"';
			}
			if ( $nav_type ) {
				$attrs_escaped .= ' nav_type="' . esc_attr( $nav_type ) . '"';
			}
			if ( $nav_pos2 ) {
				$attrs_escaped .= ' nav_pos2="' . esc_attr( $nav_pos2 ) . '"';
			}
			if ( $show_nav_hover ) {
				$attrs_escaped .= ' show_nav_hover="' . esc_attr( $show_nav_hover ) . '"';
			}
			if ( $pagination ) {
				$attrs_escaped .= ' pagination="' . esc_attr( $pagination ) . '"';
			}
			if ( $dots_pos ) {
				$attrs_escaped .= ' dots_pos="' . esc_attr( $dots_pos ) . '"';
			}
			if ( $dots_style ) {
				$attrs_escaped .= ' dots_style="' . esc_attr( $dots_style ) . '"';
			}
			if ( $autoplay ) {
				$attrs_escaped .= ' autoplay="' . esc_attr( $autoplay ) . '"';
			}
			if ( 5000 !== intval( $autoplay_timeout ) ) {
				$attrs_escaped .= ' autoplay_timeout="' . intval( $autoplay_timeout ) . '"';
			}
			$output .= do_shortcode( '[porto_product_category ' . $attrs_escaped . ']' );

			if ( $term_cat->description ) {
				$output .= '<div class="category-description">';
				$output .= do_shortcode( $term_cat->description );
				$output .= '</div>';
			}
		}
			$output .= '</section>';

			$is_first = false;
	}
	$output .= '</div>';
}

$output .= '</div>';

echo porto_filter_output( $output );

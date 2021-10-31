<?php

$output = $title = $view = $per_page = $columns = $column_width = $addlinks_pos = $orderby = $order = $category = $pagination = $navigation = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'shortcode'          => 'products',
			'title'              => '',
			'title_border_style' => '',
			'title_align'        => '',
			'view'               => 'grid',
			'grid_layout'        => '1',
			'grid_height'        => 600,
			'spacing'            => '',

			'per_page'           => '',
			'columns'            => 4,
			'columns_mobile'     => '',
			'column_width'       => '',

			'count'              => '',
			'pagination_style'   => '',
			'show_sort'          => '',
			'show_new_title'     => '',
			'show_sales_title'   => '',
			'show_rating_title'  => '',
			'show_onsale_title'  => '',
			'category_filter'    => '',
			'filter_style'       => '',

			'orderby'            => '',
			'order_date'         => 'DESC',
			'order_id'           => 'DESC',
			'order_title'        => 'DESC',
			'order_rand'         => 'DESC',
			'order_menu_order'   => 'DESC',
			'order_price'        => 'DESC',
			'order_popularity'   => 'DESC',
			'order_rating'       => 'DESC',
			'order'              => '',
			'category'           => '',
			'ids'                => '',
			'attribute'          => '',
			'filter'             => '',

			'addlinks_pos'       => '',
			'use_simple'         => false,
			'overlay_bg_opacity' => '30',
			'image_size'         => '',
			'navigation'         => 1,
			'nav_pos'            => '',
			'nav_pos2'           => '',
			'nav_type'           => '',
			'show_nav_hover'     => false,
			'pagination'         => 0,
			'dots_pos'           => '',
			'dots_style'         => '',
			'autoplay'           => '',
			'autoplay_timeout'   => 5000,
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
			'className'          => '',
			'status'             => '',
		),
		$atts
	)
);

if ( 'list' == $view ) {
	$columns = 1;
}
$orders = '';

if ( ! is_array( $orderby ) && false !== strpos( $orderby, ',' ) ) {
	$orderby = explode( ',', $orderby );
}

if ( is_array( $orderby ) && 1 === count( $orderby ) ) {
	$orderby = $orderby[0];
	$order   = ${'order_' . strtolower( $orderby )};
}

if ( ! empty( $orderby ) && is_array( $orderby ) ) {
	if ( ! is_array( $orderby ) ) {
		$orderby = explode( ',', $orderby );
	}
	$orders = '{';
	foreach ( $orderby as &$value ) {
		$value = trim( $value );
		if ( 'total_sales' == $value ) {
			$value = 'popularity';
		}
		if ( 'onsale' == $value ) {
			$status = 'on_sale';
			$value  = '';
			$order  = '';
		}
		if ( 'date' == $value ) {
			$order = $order_date;
		} elseif ( 'id' == $value ) {
			$order = $order_id;
		} elseif ( 'title' == $value ) {
			$order = $order_title;
		} elseif ( 'rand' == $value ) {
			$order = $order_rand;
		} elseif ( 'menu_order' == $value ) {
			$order = $order_menu_order;
		} elseif ( 'price' == $value ) {
			$order = $order_price;
		} elseif ( 'popularity' == $value ) {
			$order = $order_popularity;
		} elseif ( 'rating' == $value ) {
			$order = $order_rating;
		}
		$orders .= '"' . $value . '":"' . ( empty( $order ) ? 'DESC' : $order ) . '",';
	}
	$orders = rtrim( $orders, ',' ) . '}';
} else {
	if ( 'total_sales' == $orderby ) {
		$orderby = 'popularity';
	}
	if ( 'price' == $orderby && 'desc' == strtolower( $order ) ) {
		$orderby = 'price-desc';
		$order   = '';
	}
	if ( 'onsale' == $orderby ) {
		$status  = 'on_sale';
		$orderby = '';
		$order   = '';
	}
}

if ( 'viewed' == $status ) {
	$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) : array(); // @codingStandardsIgnoreLine
	$viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );
	if ( empty( $viewed_products ) ) {
		return;
	}
	if ( is_array( $viewed_products ) ) {
		$ids     = implode( ',', $viewed_products );
		$orderby = 'post__in';
	}
}


global $porto_settings;

$el_class = porto_shortcode_extract_class( $el_class );

if ( $className ) {
	if ( $el_class ) {
		$el_class = ' ' . $className;
	} else {
		$el_class = $className;
	}
}

if ( is_array( $count ) && isset( $count['size'] ) ) {
	$count = $count['size'];
}
if ( is_array( $spacing ) && isset( $spacing['size'] ) ) {
	$spacing = $spacing['size'];
}
if ( is_array( $overlay_bg_opacity ) && isset( $overlay_bg_opacity['size'] ) ) {
	$overlay_bg_opacity = $overlay_bg_opacity['size'];
}

$wrapper_id = 'porto-products-' . rand( 1000, 9999 );

$output = '<div id="' . $wrapper_id . '" class="porto-products wpb_content_element' . ( ! empty( $show_sort ) || $category_filter ? ' show-category filter-' . ( $filter_style ? esc_attr( $filter_style ) : 'vertical' ) : '' ) . ( $pagination_style ? ' archive-products' : '' ) . ( $title_border_style ? ' title-' . esc_attr( $title_border_style ) : '' ) . ' ' . esc_attr( trim( $el_class ) ) . '"';
if ( $animation_type ) {
	$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
$output .= '>';

if ( ! empty( $show_sort ) || $category_filter || $pagination_style ) {
	$output .= '<form class="pagination-form d-none">';
	if ( 'products' != $shortcode ) {
		$output .= '<input type="hidden" name="shortcode" value="' . esc_attr( $shortcode ) . '" >';
	}
	if ( $status ) {
		$output .= '<input type="hidden" name="status" value="' . esc_attr( $status ) . '" >';
	}
	$output .= '<input type="hidden" name="count" value="' . esc_attr( $count ) . '" >';
	if ( $per_page ) {
		$output .= '<input type="hidden" name="per_page" value="' . esc_attr( $per_page ) . '" >';
	}
	$output .= '<input type="hidden" name="original_orderby" value="' . esc_attr( is_array( $orderby ) ? implode( ',', $orderby ) : $orderby ) . '" >';
	$output .= '<input type="hidden" name="orderby" value="' . esc_attr( is_array( $orderby ) ? implode( ',', $orderby ) : $orderby ) . '" >';
	$output .= '<input type="hidden" name="order" value="' . esc_attr( $order ) . '" >';
	$output .= '<input type="hidden" name="category" value="' . esc_attr( $category ) . '" >';
	$output .= '<input type="hidden" name="ids" value="' . esc_attr( $ids ) . '" >';
	$output .= '<input type="hidden" name="columns" value="' . esc_attr( $columns ) . '" >';
	$output .= '<input type="hidden" name="view" value="' . esc_attr( $view ) . '" >';
	if ( 'creative' == $view ) {
		$output .= '<input type="hidden" name="grid_layout" value="' . esc_attr( $grid_layout ) . '" >';
		$output .= '<input type="hidden" name="grid_height" value="' . esc_attr( $grid_height ) . '" >';
		$output .= '<input type="hidden" name="spacing" value="' . esc_attr( $spacing ) . '" >';
	}
	if ( $use_simple ) {
		$output .= '<input type="hidden" name="use_simple" value="' . esc_attr( $use_simple ) . '" >';
	}
	$output .= '<input type="hidden" name="pagination_style" value="' . esc_attr( $pagination_style ) . '" >';
	if ( $addlinks_pos ) {
		$output .= '<input type="hidden" name="addlinks_pos" value="' . esc_attr( $addlinks_pos ) . '" >';
	}
	$output .= '</form>';
}

if ( $title ) {
	$output .= '<h2 class="section-title' . ( $title_align ? ' text-' . esc_attr( $title_align ) : '' ) . ( 'products-slider' == $view ? ' slider-title' : '' ) . '"><span class="inline-title">' . esc_html( $title ) . '</span><span class="line"></span></h2>';
}

if ( ! empty( $show_sort ) || $category_filter ) {
	$term_args = array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
	);
	if ( $category ) {
		$categories = explode( ',', sanitize_text_field( $category ) );
		if ( 1 === count( $categories ) ) {
			$term_exists           = term_exists( $categories[0], 'product_cat' );
			$term_id               = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
			$term_args['child_of'] = $term_id;
		} else {
			$term_args['include'] = $categories;
		}
	} else {
		$term_args['parent'] = 0;
	}
	$category_html  = '<h4 class="section-title">' . esc_html__( 'Sort By', 'porto-functionality' ) . '</h4>';
	$category_html .= '<ul class="product-categories">';

	if ( ! empty( $show_sort ) ) {
		if ( ! is_array( $show_sort ) ) {
			$show_sort = explode( ',', trim( $show_sort ) );
		}
		if ( in_array( 'all', $show_sort ) ) {
			$category_html .= '<li class="current"><a href="javascript:void(0)" data-cat_id="">' . esc_html__( 'All', 'porto-functionality' ) . '</a></li>';
		}
		if ( in_array( 'popular', $show_sort ) ) {
			$filter_title   = $show_sales_title ? $show_sales_title : __( 'Best Seller', 'porto-functionality' );
			$category_html .= '<li><a href="javascript:void(0)" data-sort_id="popularity"' . ( $category ? ' data-cat_id="' . esc_attr( $category ) . '"' : '' ) . '>' . esc_html( $filter_title ) . '</a></li>';
		}
		if ( in_array( 'date', $show_sort ) ) {
			$filter_title   = $show_new_title ? $show_new_title : __( 'New Arrivals', 'porto-functionality' );
			$category_html .= '<li><a href="javascript:void(0)" data-sort_id="date"' . ( $category ? ' data-cat_id="' . esc_attr( $category ) . '"' : '' ) . '>' . esc_html( $filter_title ) . '</a></li>';
		}
		if ( in_array( 'rating', $show_sort ) ) {
			$filter_title   = $show_rating_title ? $show_rating_title : __( 'Best Rating', 'porto-functionality' );
			$category_html .= '<li><a href="javascript:void(0)" data-sort_id="rating"' . ( $category ? ' data-cat_id="' . esc_attr( $category ) . '"' : '' ) . '>' . esc_html( $filter_title ) . '</a></li>';
		}
		if ( in_array( 'onsale', $show_sort ) ) {
			$filter_title   = $show_onsale_title ? $show_onsale_title : __( 'On Sale', 'porto-functionality' );
			$category_html .= '<li><a href="javascript:void(0)" data-sort_id="onsale"' . ( $category ? ' data-cat_id="' . esc_attr( $category ) . '"' : '' ) . '>' . esc_html( $filter_title ) . '</a></li>';
		}
	}

	if ( $category_filter ) {
		$terms = get_terms( $term_args );
		foreach ( $terms as $term_cat ) {
			if ( 'Uncategorized' == $term_cat->name ) {
				continue;
			}
			$id             = $term_cat->term_id;
			$name           = $term_cat->name;
			$slug           = $term_cat->slug;
			$category_html .= '<li><a href="' . esc_url( get_term_link( $id, 'product_cat' ) ) . '" data-cat_id="' . esc_attr( $slug ) . '">' . esc_html( $name ) . '</a></li>';
		}
	}
	$category_html .= '</ul>';
	$output        .= '<div class="products-filter">';
	if ( apply_filters( 'porto_wooocommerce_products_shortcode_sticky_filter', false ) ) {
		$output .= '<div data-plugin-sticky data-plugin-options="{&quot;autoInit&quot;: true, &quot;minWidth&quot;: 991, &quot;containerSelector&quot;: &quot;.porto-products&quot;, &quot;autoFit&quot;:true, &quot;paddingOffsetBottom&quot;: 10}">';
	}
				$output .= apply_filters( 'porto_wooocommerce_products_shortcode_categories_html', $category_html );
	if ( apply_filters( 'porto_wooocommerce_products_shortcode_sticky_filter', false ) ) {
		$output .= '</div>';
	}
	$output .= '</div>';
}

$wrapper_class = '';
if ( 'products-slider' == $view ) {
	$output .= '<div class="slider-wrapper">';
} elseif ( 'divider' == $view ) {
	$wrapper_class .= 'divider-line';
	$view           = 'grid';
} elseif ( 'creative' == $view && ! in_array( $addlinks_pos, array( 'onimage', 'onimage2', 'onimage3' ) ) ) {
	$addlinks_pos = 'onimage';
}

global $porto_woocommerce_loop;

if ( ! empty( $porto_woocommerce_loop ) ) {
	$porto_woocommerce_loop_backup = $porto_woocommerce_loop;
}

$porto_woocommerce_loop['view']    = $view;
$porto_woocommerce_loop['columns'] = $columns;
if ( $columns_mobile ) {
	$porto_woocommerce_loop['columns_mobile'] = $columns_mobile;
}
$porto_woocommerce_loop['column_width'] = $column_width;
$porto_woocommerce_loop['addlinks_pos'] = $addlinks_pos;

if ( 'products-slider' == $view ) {
	$porto_woocommerce_loop['pagination'] = $pagination;
	$porto_woocommerce_loop['navigation'] = $navigation;
	if ( $autoplay ) {
		$porto_woocommerce_loop['autoplay'] = ( 'yes' == $autoplay ? true : false );
		if ( 5000 !== intval( $autoplay_timeout ) ) {
			$porto_woocommerce_loop['autoplay_timeout'] = $autoplay_timeout;
		}
	}
}

if ( $use_simple ) {
	if ( ! isset( $porto_settings['product-review'] ) || $porto_settings['product-review'] ) {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	}
	$porto_woocommerce_loop['use_simple_layout'] = true;
}

$extra_atts = '';
if ( $category ) {
	$extra_atts .= ' category="' . esc_attr( $category ) . '"';
}
if ( $per_page ) {
	$extra_atts .= ' per_page="' . esc_attr( $per_page ) . '"';
}
if ( $ids ) {
	$extra_atts .= ' ids="' . esc_attr( $ids ) . '"';
	if ( empty( $atts['orderby'] ) ) {
		$orderby = 'post__in';
		$order   = 'ASC';
	}
}
if ( $category ) {
	$extra_atts .= ' category="' . esc_attr( $category ) . '"';
}
if ( $attribute ) {
	$extra_atts .= ' attribute="' . esc_attr( $attribute ) . '"';
	if ( isset( $atts[ 'filter_' . $attribute ] ) ) {
		$filter = $atts[ 'filter_' . $attribute ];
		if ( is_array( $filter ) ) {
			$filter = implode( ',', $filter );
		}
	}
}
if ( $filter ) {
	$extra_atts .= ' filter="' . esc_attr( $filter ) . '"';
}
if ( ! empty( $orderby ) ) {

	$extra_atts .= ' orderby="' . esc_attr( is_array( $orderby ) ? $orders : $orderby ) . '"';
}
if ( $order && ! is_array( $orderby ) ) {
	$extra_atts .= ' order="' . esc_attr( $order ) . '"';
}

if ( $pagination_style ) {
	$extra_atts                        .= ' paginate="true"';
	$porto_settings_backup              = $porto_settings['product-infinite'];
	$porto_settings['product-infinite'] = $pagination_style;

	$shop_action1 = false;
	$shop_action2 = false;
	$shop_action3 = false;
	if ( has_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_open_before_clearfix_div' ) ) {
		$shop_action1 = true;
		remove_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_open_before_clearfix_div', 11 );
	}
	if ( has_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_close_before_clearfix_div' ) ) {
		$shop_action2 = true;
		remove_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_close_before_clearfix_div', 80 );
	}
	if ( has_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering' ) ) {
		$shop_action3 = true;
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	}
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 50 );
}

if ( 'featured' == $status ) {
	$extra_atts .= ' visibility="featured"';
} elseif ( 'on_sale' == $status ) {
	$extra_atts .= ' on_sale="1"';
} elseif ( 'pre_order' == $status ) {
	$extra_atts .= ' visibility="pre_order"';
}

if ( $navigation ) {
	if ( $nav_pos ) {
		$wrapper_class .= ' ' . $nav_pos;
	}
	if ( ( empty( $nav_pos ) || 'nav-center-images-only' == $nav_pos ) && $nav_pos2 ) {
		$wrapper_class .= ' ' . $nav_pos2;
	}
	if ( $nav_type ) {
		$wrapper_class .= ' ' . $nav_type;
	} else {
		$wrapper_class .= ' show-nav-middle';
	}
	if ( $show_nav_hover ) {
		$wrapper_class .= ' show-nav-hover';
	}
}

if ( $pagination ) {
	if ( $dots_pos ) {
		$wrapper_class .= ' ' . $dots_pos;
	}
	if ( $dots_style ) {
		$wrapper_class .= ' ' . $dots_style;
	}
}

if ( $wrapper_class ) {
	$porto_woocommerce_loop['el_class'] = $wrapper_class;
}

if ( $image_size ) {
	$porto_woocommerce_loop['image_size'] = $image_size;
}

if ( 'creative' == $view || ( 'grid' == $view && '' !== $spacing ) || ( '0' == $overlay_bg_opacity || ( '30' != $overlay_bg_opacity && $overlay_bg_opacity ) ) ) {
	echo '<style scope="scope">';

	if ( 'grid' == $view && '' !== $spacing ) {
		echo '#' . $wrapper_id . ' ul.products { margin-left: ' . ( (int) $spacing / 2 * -1 ) . 'px; margin-right: ' . ( (int) $spacing / 2 * -1 ) . 'px; }';
		echo '#' . $wrapper_id . ' li.product { padding-left: ' . ( (int) $spacing / 2 ) . 'px; padding-right: ' . ( (int) $spacing / 2 ) . 'px; margin-bottom: ' . ( (int) $spacing ) . 'px; }';
		if ( 0 === (int) $spacing && 'onimage2' != $addlinks_pos && 'onimage3' != $addlinks_pos ) {
			echo '#' . $wrapper_id . ' li.product:nth-child(even) .product-image .inner:after { content: ""; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: rgba(33, 37, 41, .01); }';
			if ( 'outimage' == $addlinks_pos || 'outimage_aq_onimage' == $addlinks_pos ) {
				echo '#' . $wrapper_id . ' .product-content { padding-left: 10px; padding-right: 10px; }';
			}
		}
	} elseif ( 'creative' == $view ) {
		$porto_woocommerce_loop['grid_layout'] = porto_creative_grid_layout( $grid_layout );

		if ( ! $count ) {
			$count = count( $porto_woocommerce_loop['grid_layout'] );
		}

		wp_enqueue_script( 'isotope' );

		$grid_height_number = trim( preg_replace( '/[^0-9]/', '', $grid_height ) );
		$unit               = trim( str_replace( $grid_height_number, '', $grid_height ) );
		porto_creative_grid_style( $porto_woocommerce_loop['grid_layout'], $grid_height_number, $wrapper_id, $spacing, false, $unit, '.product-col', $grid_layout );
	}

	if ( ( 'onimage2' == $addlinks_pos || 'onimage3' == $addlinks_pos ) && ( '0' == $overlay_bg_opacity || ( '30' != $overlay_bg_opacity && $overlay_bg_opacity ) ) ) {
		echo '#' . $wrapper_id . ' li.product .product-image .inner:after { background-color: rgba(27, 27, 23, ' . ( (int) $overlay_bg_opacity / 100 ) . '); }';
		if ( 'onimage3' == $addlinks_pos ) {
			echo '#' . $wrapper_id . ' li.product:hover .product-image .inner:after { background-color: rgba(27, 27, 23, ' . ( ( $overlay_bg_opacity > 45 ? (int) $overlay_bg_opacity - 15 : (int) $overlay_bg_opacity + 15 ) / 100 ) . '); }';
		}
	}

	echo '</style>';
} elseif ( 'products-slider' == $view && '' !== $spacing ) {
	echo '<style scope="scope">';
	echo '#' . $wrapper_id . ' .slider-wrapper { margin-left: ' . ( (int) $spacing / 2 * -1 ) . 'px; margin-right: ' . ( (int) $spacing / 2 * -1 ) . 'px; }';
	echo '#' . $wrapper_id . ' li.product { padding-left: ' . ( (int) $spacing / 2 ) . 'px; padding-right: ' . ( (int) $spacing / 2 ) . 'px; margin-bottom: ' . ( (int) $spacing ) . 'px; }';
	echo '</style>';
}

if ( $count ) {
	$extra_atts .= ' limit="' . intval( $count ) . '"';
}

$output .= do_shortcode( '[' . esc_html( $shortcode ) . ' columns="' . $columns . '"' . $extra_atts . ']' );

if ( 'products-slider' == $view ) {
	$output .= '</div>';
}

$output .= '</div>';

if ( isset( $porto_woocommerce_loop_backup ) && ! empty( $porto_woocommerce_loop_backup ) ) {
	global $porto_woocommerce_loop;
	$porto_woocommerce_loop = $porto_woocommerce_loop_backup;
} else {
	unset( $GLOBALS['porto_woocommerce_loop'] );
}

if ( $pagination_style ) {
	if ( isset( $porto_settings_backup ) ) {
		$porto_settings['product-infinite'] = $porto_settings_backup;
	}
	if ( $shop_action1 ) {
		add_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_open_before_clearfix_div', 11 );
	}
	if ( $shop_action2 ) {
		add_action( 'woocommerce_before_shop_loop', 'porto_woocommerce_close_before_clearfix_div', 80 );
	}
	if ( $shop_action3 ) {
		add_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	}
	add_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 50 );
}

if ( $use_simple && ( ! isset( $porto_settings['product-review'] ) || $porto_settings['product-review'] ) ) {
	add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
}

echo porto_filter_output( $output );

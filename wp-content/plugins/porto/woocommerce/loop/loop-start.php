<?php
/**
 * Product Loop Start
 *
 * @version     3.3.0
 */

global $porto_settings, $porto_layout, $woocommerce_loop, $porto_woocommerce_loop;
$cols         = isset( $porto_settings['product-cols'] ) ? $porto_settings['product-cols'] : 3;
$addlinks_pos = isset( $porto_settings['category-addlinks-pos'] ) ? $porto_settings['category-addlinks-pos'] : 'default';

$attrs = '';

if ( isset( $porto_woocommerce_loop['columns'] ) && $porto_woocommerce_loop['columns'] ) {
	$cols = $porto_woocommerce_loop['columns'];
} elseif ( isset( $woocommerce_loop['columns'] ) && $woocommerce_loop['columns'] ) {
	$cols = $woocommerce_loop['columns'];
}

$woocommerce_loop['product_loop'] = 0;
$woocommerce_loop['cat_loop']     = 0;

if ( in_array( $porto_layout, porto_options_sidebars() ) ) {
	if ( 8 == $cols || 7 == $cols ) {
		$cols = 6;
	}
}

$item_width = $cols;
if ( isset( $porto_woocommerce_loop['column_width'] ) && $porto_woocommerce_loop['column_width'] ) {
	$item_width = $porto_woocommerce_loop['column_width'];
} elseif ( isset( $woocommerce_loop['column_width'] ) && $woocommerce_loop['column_width'] ) {
	$item_width = $woocommerce_loop['column_width'];
}

$cols_arr = porto_generate_shop_columns( $cols, $porto_layout );
if ( is_array( $cols_arr ) ) {
	$cols_ls = $cols_arr[0];
	$cols_xs = $cols_arr[1];
	$cols_md = $cols_arr[2];
	$cols    = $cols_arr[3];

	if ( count( $cols_arr ) > 4 ) {
		$cols_xl = $cols_arr[4];
	}
}

switch ( $item_width ) {
	case 1:
		$item_width_md = 1;
		$item_width_xs = 1;
		$item_width_ls = 1;
		break;
	case 2:
		$item_width_md = 2;
		$item_width_xs = 1;
		$item_width_ls = 1;
		break;
	case 3:
		$item_width_md = 3;
		$item_width_xs = 2;
		$item_width_ls = 1;
		break;
	case 4:
		$item_width_md = 3;
		$item_width_xs = 2;
		$item_width_ls = 1;
		break;
	case 5:
		$item_width_md = 4;
		if ( porto_is_wide_layout( $porto_layout ) ) {
			$item_width_xs = 3;
			$item_width_ls = 2;
		} else {
			$item_width_xs = 2;
			$item_width_ls = 1;
		}
		break;
	case 6:
		$item_width_md = 5;
		$item_width_xs = 3;
		$item_width_ls = 2;
		break;
	case 7:
		$item_width_md = 6;
		$item_width_xs = 3;
		$item_width_ls = 2;
		break;
	case 8:
		$item_width_md = 6;
		$item_width_xs = 3;
		$item_width_ls = 2;
		break;
	default:
		$item_width    = 4;
		$item_width_md = 3;
		$item_width_xs = 2;
		$item_width_ls = 1;
}

global $porto_shop_filter_layout;
if ( porto_is_ajax() && isset( $porto_shop_filter_layout ) && 'horizontal' === $porto_shop_filter_layout && isset( $_COOKIE['porto_horizontal_filter'] ) && 'opened' == $_COOKIE['porto_horizontal_filter'] ) {
	if ( $cols >= 2 ) {
		$cols--;
	}
	if ( $cols_md >= 2 ) {
		$cols_md--;
	}
	if ( $item_width >= 2 ) {
		$item_width--;
	}
	if ( $item_width_md >= 2 ) {
		$item_width_md--;
	}
}

if ( ! empty( $porto_woocommerce_loop['columns_mobile'] ) ) {
	$cols_ls = $porto_woocommerce_loop['columns_mobile'];
} elseif ( ! empty( $woocommerce_loop['columns_mobile'] ) ) {
	$cols_ls = $woocommerce_loop['columns_mobile'];
} elseif ( isset( $porto_settings['shop-product-cols-mobile'] ) && $porto_settings['shop-product-cols-mobile'] ) {
	$cols_ls = $porto_settings['shop-product-cols-mobile'];
}
if ( 1 === (int) $cols ) {
	$cols_ls = 1;
}
if ( 1 == (int) $cols_ls && (int) $cols_xs >= 3 ) {
	$cols_xs--;
}

if ( ! isset( $woocommerce_loop['addlinks_pos'] ) || ! $woocommerce_loop['addlinks_pos'] ) {
	if ( isset( $porto_woocommerce_loop['addlinks_pos'] ) && $porto_woocommerce_loop['addlinks_pos'] ) {
		$woocommerce_loop['addlinks_pos'] = $porto_woocommerce_loop['addlinks_pos'];
	} else {
		$woocommerce_loop['addlinks_pos'] = $addlinks_pos;
	}
}

global $porto_products_cols_lg, $porto_products_cols_md, $porto_products_cols_xs, $porto_products_cols_ls;
$porto_products_cols_lg = $cols;
$porto_products_cols_md = $cols_md;
$porto_products_cols_xs = $cols_xs;
$porto_products_cols_ls = $cols_ls;

$classes = array( 'products', 'products-container' );
if ( isset( $porto_woocommerce_loop['widget'] ) && $porto_woocommerce_loop['widget'] ) {
	$classes[] = 'product_list_widget';
}

if ( isset( $porto_woocommerce_loop['view'] ) && $porto_woocommerce_loop['view'] ) {

	$classes[] = 'creative' == $porto_woocommerce_loop['view'] ? 'grid-creative' : $porto_woocommerce_loop['view'];
	if ( 'products-slider' === $porto_woocommerce_loop['view'] ) {
		$classes[] = 'owl-carousel';
		if ( empty( $porto_woocommerce_loop['el_class'] ) && ( ! isset( $porto_woocommerce_loop['navigation'] ) || $porto_woocommerce_loop['navigation'] ) ) {
			$classes[] = 'show-nav-title';
		}
	}
}

if ( isset( $porto_woocommerce_loop['category-view'] ) && $porto_woocommerce_loop['category-view'] ) {
	$classes[] = $porto_woocommerce_loop['category-view'];
}
if ( isset( $porto_woocommerce_loop['el_class'] ) && $porto_woocommerce_loop['el_class'] ) {
	$classes[] = trim( $porto_woocommerce_loop['el_class'] );
}

$view_mode = '';
if ( isset( $woocommerce_loop['category-view'] ) && $woocommerce_loop['category-view'] ) {
	$view_mode = $woocommerce_loop['category-view'];
}
if ( ( ! function_exists( 'wc_get_loop_prop' ) || wc_get_loop_prop( 'is_paginated' ) ) && ! isset( $porto_woocommerce_loop['view'] ) && isset( $_COOKIE['gridcookie'] ) ) {
	$view_mode = $_COOKIE['gridcookie'];
}
if ( $view_mode ) {
	$classes[] = $view_mode;
	if ( 'list' == $view_mode ) {
		$woocommerce_loop['addlinks_pos'] = '';
	}
} elseif ( isset( $porto_woocommerce_loop['view'] ) && $porto_woocommerce_loop['view'] ) {
	$view_mode = $porto_woocommerce_loop['view'];
}

if ( ! $view_mode ) {
	$classes[] = 'grid';
	$view_mode = 'grid';
}

if ( 'grid' == $view_mode && (int) $cols >= 7 ) {
	$classes[] = 'gap-narrow';
}

if ( ! isset( $porto_woocommerce_loop['view'] ) || 'creative' != $porto_woocommerce_loop['view'] ) {
	if ( isset( $cols_xl ) ) {
		$classes[] = 'pcols-xl-' . $cols_xl;
	}
	$classes[] = 'pcols-lg-' . $cols;
	$classes[] = 'pcols-md-' . $cols_md;
	$classes[] = 'pcols-xs-' . $cols_xs;
	$classes[] = 'pcols-ls-' . $cols_ls;
	$classes[] = 'pwidth-lg-' . $item_width;
	$classes[] = 'pwidth-md-' . $item_width_md;
	$classes[] = 'pwidth-xs-' . $item_width_xs;
	$classes[] = 'pwidth-ls-' . $item_width_ls;
} elseif ( ! isset( $porto_woocommerce_loop['creative_grid'] ) ) {
	$attrs = ' data-plugin-masonry data-plugin-options="' . esc_attr(
		json_encode(
			array(
				'itemSelector' => '.product-col',
				'masonry'      => array( 'columnWidth' => '.grid-col-sizer' ),
			)
		)
	) . '"';
}

$options                = array();
$options['themeConfig'] = true;
if ( isset( $porto_woocommerce_loop['view'] ) && 'products-slider' == $porto_woocommerce_loop['view'] ) {
	if ( isset( $cols_xl ) ) {
		$options['xl'] = (int) $cols_xl;
	}
	$options['lg'] = (int) $cols;
	$options['md'] = (int) $cols_md;
	$options['xs'] = (int) $cols_xs;
	$options['ls'] = (int) $cols_ls;
	if ( ! isset( $porto_woocommerce_loop['navigation'] ) || $porto_woocommerce_loop['navigation'] ) {
		$options['nav'] = true;
	}
	if ( isset( $porto_woocommerce_loop['pagination'] ) && $porto_woocommerce_loop['pagination'] ) {
		$options['dots'] = true;
	}
	if ( isset( $porto_woocommerce_loop['autoplay'] ) ) {
		$options['autoplay'] = $porto_woocommerce_loop['autoplay'];
	}
	if ( isset( $porto_woocommerce_loop['autoplay_timeout'] ) ) {
		$options['autoplayTimeout'] = (int) $porto_woocommerce_loop['autoplay_timeout'];
	}
	if ( isset( $porto_woocommerce_loop['stage_padding'] ) && $porto_woocommerce_loop['stage_padding'] ) {
		$options['stagePadding'] = intval( $porto_woocommerce_loop['stage_padding'] );
	}
}
$options = json_encode( $options );

if ( wc_get_loop_prop( 'is_shortcode' ) && isset( $porto_settings['product-infinite'] ) && 'load_more' == $porto_settings['product-infinite'] ) {
	$cur_page = absint( empty( $_GET['product-page'] ) ? 1 : $_GET['product-page'] );
	//$page_path = esc_url_raw( add_query_arg( 'product-page', '', false ) ) . '=';
	$attrs .= ' data-cur_page="' . $cur_page . '" data-max_page="' . absint( wc_get_loop_prop( 'total_pages' ) ) . '"';
} elseif ( porto_is_ajax() && isset( $porto_settings['product-infinite'] ) && $porto_settings['product-infinite'] ) {
	global $wp_query;
	$page_num     = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	$page_link    = get_pagenum_link( 999999999 );
	$page_max_num = $wp_query->max_num_pages;
	$page_path    = str_replace( '999999999', '%cur_page%', esc_url( add_query_arg( 'load_posts_only', '1', $page_link ) ) );
	$page_path    = str_replace( '&#038;', '&amp;', $page_path );
	$page_path    = str_replace( '#038;', '&amp;', $page_path );

	$attrs .= ' data-cur_page="' . intval( $page_num ) . '" data-max_page="' . esc_attr( $page_max_num ) . '" data-page_path="' . $page_path . '"';
}

if ( wc_get_loop_prop( 'is_shortcode' ) ) {
	$classes[] = 'is-shortcode';
}

if ( $porto_settings['add-to-cart-notification'] && ! has_action( 'porto_after_wrapper', 'porto_woocommerce_add_to_cart_notification_html' ) ) {
	add_action( 'porto_after_wrapper', 'porto_woocommerce_add_to_cart_notification_html' );
}
if ( 'list' == $view_mode || ( isset( $porto_settings['product-desc'] ) && $porto_settings['product-desc'] ) ) {
	if ( ! has_action( 'woocommerce_after_shop_loop_item_title', 'porto_woocommerce_single_excerpt' ) ) {
		add_action( 'woocommerce_after_shop_loop_item_title', 'porto_woocommerce_single_excerpt', 9 );
	}
} elseif ( has_action( 'woocommerce_after_shop_loop_item_title', 'porto_woocommerce_single_excerpt' ) ) {
	remove_action( 'woocommerce_after_shop_loop_item_title', 'porto_woocommerce_single_excerpt', 9 );
}

if ( ! empty( $porto_settings['show-skeleton-screen'] ) && in_array( 'shop', $porto_settings['show-skeleton-screen'] ) && ( function_exists( 'wc_get_loop_prop' ) && ! wc_get_loop_prop( 'is_paginated' ) ) || isset( $porto_woocommerce_loop['view'] ) || ! isset( $_COOKIE['gridcookie'] ) || 'list' != $_COOKIE['gridcookie'] ) {
	if ( isset( $woocommerce_loop['addlinks_pos'] ) && 'quantity' == $woocommerce_loop['addlinks_pos'] ) {
		$attrs .= ' data-product_layout="product-wq_onimage"';
	} elseif ( isset( $woocommerce_loop['addlinks_pos'] ) ) {
		if ( 'outimage_aq_onimage2' == $woocommerce_loop['addlinks_pos'] ) {
			$attrs .= ' data-product_layout="product-outimage_aq_onimage with-padding"';
		} elseif ( 'onhover' == $woocommerce_loop['addlinks_pos'] ) {
			$attrs .= ' data-product_layout="product-default show-links-hover"';
		} else {
			$attrs .= ' data-product_layout="product-' . esc_attr( $woocommerce_loop['addlinks_pos'] ) . '"';
		}
	}
}

$legacy_mode = apply_filters( 'porto_legacy_mode', true );
$legacy_mode = ( $legacy_mode && ! empty( $porto_settings['product-quickview'] ) ) || ! $legacy_mode;
if ( $legacy_mode || ! empty( $porto_settings['show_swatch'] ) ) {
	// load wc variation script
	wp_enqueue_script( 'wc-add-to-cart-variation' );
}
?>
<ul class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
	<?php if ( isset( $porto_woocommerce_loop['view'] ) && 'products-slider' == $porto_woocommerce_loop['view'] ) : ?>
	data-plugin-options="<?php echo esc_attr( $options ); ?>"<?php endif; ?><?php echo porto_filter_output( $attrs ); ?>>
<?php
	do_action( 'porto_woocommerce_shop_loop_start' );

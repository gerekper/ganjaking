<?php
extract(
	shortcode_atts(
		array(
			'view'               => 'grid',
			'grid_layout'        => '1',
			'grid_height'        => 600,
			'spacing'            => '',
			'columns'            => 4,
			'columns_mobile'     => '',
			'pagination_style'   => '',
			'addlinks_pos'       => '',
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

			'el_class'           => '',
		),
		$atts
	)
);

if ( ( ! isset( $atts['title_use_theme_fonts'] ) || 'yes' !== $atts['title_use_theme_fonts'] ) && ! empty( $atts['title_google_font'] ) ) {
	$google_fonts_data = porto_sc_parse_google_font( $atts['title_google_font'] );
	if ( $google_fonts_data ) {
		porto_sc_enqueue_google_fonts( array( $google_fonts_data ) );
	}
}

if ( ! empty( $_COOKIE['gridcookie'] ) ) {
	$view = esc_html( $_COOKIE['gridcookie'] );
}

global $porto_settings;

$el_class = porto_shortcode_extract_class( $el_class );

if ( is_array( $spacing ) && isset( $spacing['size'] ) ) {
	$spacing = $spacing['size'];
}

$wrapper_id = 'porto-products-' . rand( 1000, 9999 );
echo '<div id="' . esc_attr( $wrapper_id ) . '" class="archive-products">';
if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
	echo '<style>';
	include 'style-products.php';
	echo '</style>';
}

$wrapper_class = '';
if ( 'products-slider' == $view ) {
	echo '<div class="slider-wrapper">';
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

if ( $el_class ) {
	$wrapper_class .= ' ' . $el_class;
}

if ( $wrapper_class ) {
	$porto_woocommerce_loop['el_class'] = trim( $wrapper_class );
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

$skeleton_lazyload = apply_filters( 'porto_skeleton_lazyload', ! empty( $porto_settings['show-skeleton-screen'] ) && in_array( 'shop', $porto_settings['show-skeleton-screen'] ) && ! porto_is_ajax(), 'archive-product' );

if ( $skeleton_lazyload ) {
	global $porto_woocommerce_loop;
	if ( ! $porto_woocommerce_loop ) {
		$porto_woocommerce_loop = array();
	}
	if ( ! isset( $porto_woocommerce_loop['el_class'] ) || empty( $porto_woocommerce_loop['el_class'] ) ) {
		$porto_woocommerce_loop['el_class'] = 'skeleton-loading';
	} else {
		$porto_woocommerce_loop['el_class'] .= ' skeleton-loading';
	}
	$porto_settings['skeleton_lazyload'] = true;

	remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );
}

woocommerce_product_loop_start();
if ( $skeleton_lazyload ) {
	$porto_woocommerce_loop['el_class'] = str_replace( 'skeleton-loading', 'skeleton-body', $porto_woocommerce_loop['el_class'] );
	$skeleton_body_start                = woocommerce_product_loop_start( false );

	$sp_class = 'product product-col';
	if ( ( function_exists( 'wc_get_loop_prop' ) && ! wc_get_loop_prop( 'is_paginated' ) ) || isset( $porto_woocommerce_loop['view'] ) || ! isset( $_COOKIE['gridcookie'] ) || 'list' != $_COOKIE['gridcookie'] ) {
		if ( isset( $woocommerce_loop['addlinks_pos'] ) && 'quantity' == $woocommerce_loop['addlinks_pos'] ) {
			$sp_class .= ' product-wq_onimage';
		} elseif ( isset( $woocommerce_loop['addlinks_pos'] ) ) {
			if ( 'outimage_aq_onimage2' == $woocommerce_loop['addlinks_pos'] ) {
				$sp_class .= ' product-outimage_aq_onimage with-padding';
			} elseif ( 'onhover' == $woocommerce_loop['addlinks_pos'] ) {
				$sp_class .= ' product-default show-links-hover';
			} else {
				$sp_class .= ' product-' . $woocommerce_loop['addlinks_pos'];
			}
		}
	}

	ob_start();
	echo woocommerce_maybe_show_product_subcategories();
	$products_count = 0;
}
if ( ! function_exists( 'wc_get_loop_prop' ) || wc_get_loop_prop( 'total' ) ) {
	while ( have_posts() ) :
		the_post();
		/**
		 * Hook: woocommerce_shop_loop.
		 */
		do_action( 'woocommerce_shop_loop' );
		wc_get_template_part( 'content', 'product' );
		if ( $skeleton_lazyload ) {
			$products_count++;
		}
	endwhile;
}

if ( $skeleton_lazyload ) {
	$archive_content = ob_get_clean();
	echo '<script type="text/template">' . json_encode( $archive_content ) . '</script>';
}

woocommerce_product_loop_end();

if ( $skeleton_lazyload ) {
	if ( $products_count < 1 ) {
		global $porto_products_cols_lg;
		$products_count = $porto_products_cols_lg;
	}
	echo porto_filter_output( $skeleton_body_start );
	for ( $i = 0; $i < $products_count; $i++ ) {
		echo '<li class="' . esc_attr( $sp_class ) . '"></li>';
	}
	woocommerce_product_loop_end();

	add_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );
}

if ( 'products-slider' == $view ) {
	echo '</div>';
}

echo '</div>';

if ( isset( $porto_woocommerce_loop_backup ) && ! empty( $porto_woocommerce_loop_backup ) ) {
	global $porto_woocommerce_loop;
	$porto_woocommerce_loop = $porto_woocommerce_loop_backup;
} else {
	unset( $GLOBALS['porto_woocommerce_loop'] );
}

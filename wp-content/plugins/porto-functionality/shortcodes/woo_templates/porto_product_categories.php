<?php

$output = $title = $view = $number = $columns = $column_width = $hide_empty = $orderby = $order = $parent = $ids = $addlinks_pos = $hide_count = $pagination = $navigation = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'title'              => '',
			'view'               => 'grid',
			'number'             => 12,
			'columns'            => 4,
			'columns_mobile'     => '',
			'column_width'       => '',
			'grid_layout'        => '1',
			'grid_height'        => '600',
			'spacing'            => '',
			'text_position'      => 'middle-center',
			'overlay_bg_opacity' => '15',
			'text_color'         => 'light',

			'orderby'            => 'name',
			'order'              => 'ASC',
			'hide_empty'         => '',
			'parent'             => '',
			'ids'                => '',
			'addlinks_pos'       => '',
			'media_type'         => '',
			'show_sub_cats'      => '',
			'show_featured'      => '',
			'hide_count'         => '',
			'hover_effect'       => '',
			'image_size'         => '',

			'navigation'         => 1,
			'nav_pos'            => '',
			'nav_pos2'           => '',
			'nav_type'           => '',
			'show_nav_hover'     => false,
			'pagination'         => 0,
			'dots_pos'           => '',
			'dots_style'         => '',
			'stage_padding'      => '',
			'autoplay'           => '',
			'autoplay_timeout'   => 5000,

			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

if ( $hide_count ) {
	$el_class .= ' hide-count';
}
if ( $hover_effect ) {
	$el_class .= ' show-count-on-hover';
}

if ( is_array( $number ) && isset( $number['size'] ) ) {
	$number = $number['size'];
}
if ( is_array( $spacing ) && isset( $spacing['size'] ) ) {
	$spacing = $spacing['size'];
}
if ( is_array( $overlay_bg_opacity ) && isset( $overlay_bg_opacity['size'] ) ) {
	$overlay_bg_opacity = $overlay_bg_opacity['size'];
}

$hide_empty = $hide_empty ? 1 : 0;

$wrapper_id = 'porto-product-categories-' . rand( 1000, 9999 );

$output = '<div id="' . $wrapper_id . '" class="porto-products wpb_content_element' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '"';
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

if ( $title ) {
	if ( 'products-slider' == $view ) {
		$output .= '<h2 class="slider-title"><span class="inline-title">' . esc_html( $title ) . '</span><span class="line"></span></h2>';
	} else {
		$output .= '<h2 class="section-title">' . esc_html( $title ) . '</h2>';
	}
}

if ( 'products-slider' == $view ) {
	$output .= '<div class="slider-wrapper">';
}

global $porto_woocommerce_loop, $woocommerce_loop;

$porto_woocommerce_loop['view']    = $view;
$porto_woocommerce_loop['columns'] = $columns;
if ( $columns_mobile ) {
	$porto_woocommerce_loop['columns_mobile'] = $columns_mobile;
}
$porto_woocommerce_loop['column_width'] = $column_width;
$porto_woocommerce_loop['addlinks_pos'] = $addlinks_pos;

$wrapper_class = '';
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
if ( $media_type ) {
	$porto_woocommerce_loop['product_categories_media_type'] = $media_type;
}
if ( $show_sub_cats ) {
	$porto_woocommerce_loop['product_categories_show_sub_cats'] = true;
	$porto_woocommerce_loop['product_categories_hide_empty']    = $hide_empty;
}
if ( $show_featured ) {
	$porto_woocommerce_loop['product_categories_show_featured'] = true;
}

if ( $image_size ) {
	$porto_woocommerce_loop['image_size'] = $image_size;
}

if ( 'products-slider' == $view ) {
	if ( $stage_padding ) {
		$porto_woocommerce_loop['stage_padding'] = $stage_padding;
	}
	if ( $autoplay ) {
		$porto_woocommerce_loop['autoplay'] = ( 'yes' == $autoplay ? true : false );
		if ( 5000 !== intval( $autoplay_timeout ) ) {
			$porto_woocommerce_loop['autoplay_timeout'] = $autoplay_timeout;
		}
	}
	$porto_woocommerce_loop['navigation'] = $navigation;
	$porto_woocommerce_loop['pagination'] = $pagination;
}

$porto_woocommerce_loop['category-view'] = 'category-pos-' . explode( '-', $text_position )[0] . ( isset( explode( '-', $text_position )[1] ) ? ' category-text-' . explode( '-', $text_position )[1] : '' ) . ( 'light' != $text_color ? ' category-color-' . $text_color : '' );

if ( 'creative' == $view ) {
	$porto_woocommerce_loop['grid_layout'] = porto_creative_grid_layout( $grid_layout );

	if ( '4' == $grid_layout ) {
		$porto_woocommerce_loop['creative_grid'] = 'true';
	} else {
		wp_enqueue_script( 'isotope' );
	}

	$grid_height_number = trim( preg_replace( '/[^0-9]/', '', $grid_height ) );
	$unit               = trim( str_replace( $grid_height_number, '', $grid_height ) );
	porto_creative_grid_style( $porto_woocommerce_loop['grid_layout'], $grid_height_number, $wrapper_id, $spacing, true, $unit );
}

if ( '0' == $overlay_bg_opacity || ( '15' != $overlay_bg_opacity && $overlay_bg_opacity ) ) {
	echo '<style>';
		echo '#' . $wrapper_id . ' li.product-category .thumb-info-wrapper:after { background-color: rgba(27, 27, 23, ' . ( (int) $overlay_bg_opacity / 100 ) . '); }';
		echo '#' . $wrapper_id . ' li.product-category:hover .thumb-info-wrapper:after { background-color: rgba(27, 27, 23, ' . ( ( $overlay_bg_opacity > 45 ? (int) $overlay_bg_opacity - 15 : (int) $overlay_bg_opacity + 15 ) / 100 ) . '); }';
	echo '</style>';
}

if ( ! empty( $ids ) ) {
	$orderby = 'include';
	$order   = 'ASC';
}
$output .= do_shortcode( '[product_categories number="' . $number . '" columns="' . $columns . '" orderby="' . $orderby . '" order="' . $order . '" hide_empty="' . $hide_empty . '" parent="' . $parent . '" ids="' . $ids . '"]' );

if ( 'products-slider' == $view ) {
	$output .= '</div>';
}

$output .= '</div>';

	unset( $GLOBALS['porto_woocommerce_loop'] );

echo porto_filter_output( $output );

<?php

extract(
	shortcode_atts(
		array(
			'images'             => '',
			'image_size'         => '',
			'view'               => 'slider',
			'grid_layout'        => '1',
			'grid_height'        => 600,
			'spacing'            => '',
			'columns'            => '{"xl":"4"}',
			'v_align'            => '',
			'click_action'       => '',
			'hover_effect'       => '',

			'navigation'         => true,
			'nav_pos'            => '',
			'nav_pos2'           => '',
			'nav_type'           => '',
			'show_nav_hover'     => false,
			'pagination'         => false,
			'dots_pos'           => '',
			'dots_style'         => '',
			'autoplay'           => '',
			'autoplay_timeout'   => 5000,
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'icon_cl'            => '',
			'icon_type'          => 'fontawesome',
			'icon_simpleline'    => '',
			'icon_porto'         => '',
			'el_class'           => '',
		),
		$atts
	)
);
switch ( $icon_type ) {
	case 'simpleline':
		$icon_cl = $icon_simpleline;
		break;
	case 'porto':
		$icon_cl = $icon_porto;
		break;
}

if ( ! is_array( $images ) ) {
	$images = explode( ',', $images );
	foreach ( $images as $key => $val ) {
		$images[ $key ] = array( 'id' => (int) $val );
	}
}

if ( ! empty( $columns ) ) {
	if ( ! is_array( $columns ) ) {
		$columns_arr = json_decode( str_replace( '``', '"', $columns ), true );
	} else {
		$columns_arr = $columns;
	}
	$columns    = empty( $columns_arr['xl'] ) ? 4 : (int) $columns_arr['xl'];
	$columns_lg = empty( $columns_arr['lg'] ) ? min( 4, $columns ) : (int) $columns_arr['lg'];
	$columns_md = empty( $columns_arr['md'] ) ? min( 3, $columns_lg ) : (int) $columns_arr['md'];
	$columns_sm = empty( $columns_arr['sm'] ) ? min( 2, $columns_md ) : (int) $columns_arr['sm'];
	$columns_xs = empty( $columns_arr['xs'] ) ? min( 1, $columns_sm ) : (int) $columns_arr['xs'];
	if ( isset( $columns_arr['min'] ) ) {
		$columns_xs = empty( $columns_arr['min'] ) ? min( 1, $columns_sm ) : (int) $columns_arr['min'];
	}
} else {
	$columns    = 1;
	$columns_lg = 1;
	$columns_md = 1;
	$columns_sm = 1;
	$columns_xs = 1;
}

$wrapper_cls   = 'porto-gallery has-ccols ccols-' . $columns_xs;
$wrapper_attrs = '';

if ( ! empty( $spacing ) ) {
	$wrapper_cls .= ' has-ccols-spacing';
}

if ( $columns_sm != $columns_xs ) {
	$wrapper_cls .= ' ccols-sm-' . $columns_sm;
}
if ( $columns_md != $columns_sm ) {
	$wrapper_cls .= ' ccols-md-' . $columns_md;
}
if ( $columns_lg != $columns_md ) {
	$wrapper_cls .= ' ccols-lg-' . $columns_lg;
}
if ( $columns != $columns_lg ) {
	$wrapper_cls .= ' ccols-xl-' . $columns;
}

if ( ! empty( $shortcode_class ) ) {
	$wrapper_cls .= $shortcode_class;
} elseif ( 'creative' == $view ) {
	$shortcode_class = 'porto-gallery-' . porto_generate_rand( 4 );
	$wrapper_cls    .= ' ' . $shortcode_class;
}

if ( 'grid' == $view ) {
	$wrapper_cls .= ' porto-gallery-grid';
} elseif ( 'slider' == $view ) {
	$wrapper_cls .= ' porto-carousel owl-carousel';

	if ( $navigation ) {
		if ( $nav_pos ) {
			$wrapper_cls .= ' ' . $nav_pos;
		}
		if ( ( empty( $nav_pos ) || 'nav-center-images-only' == $nav_pos ) && $nav_pos2 ) {
			$wrapper_cls .= ' ' . $nav_pos2;
		}
		if ( $nav_type ) {
			$wrapper_cls .= ' ' . $nav_type;
		}
		if ( $show_nav_hover ) {
			$wrapper_cls .= ' show-nav-hover';
		}
	}

	if ( $pagination ) {
		if ( $dots_pos ) {
			$wrapper_cls .= ' ' . $dots_pos;
		}
		if ( $dots_style ) {
			$wrapper_cls .= ' ' . $dots_style;
		}
	}

	$options = array();
	if ( $autoplay ) {
		$options['autoplay'] = ( 'yes' == $autoplay ? true : false );
	}
	$options['autoplayTimeout']    = (int) $autoplay_timeout;
	$options['autoplayHoverPause'] = true;
	$options['items']              = (int) $columns;
	$options['lg']                 = (int) $columns_lg;
	$options['md']                 = (int) $columns_md;
	$options['sm']                 = (int) $columns_sm;
	$options['xs']                 = (int) $columns_xs;
	$options['nav']                = $navigation;
	$options['dots']               = $pagination;
	if ( ! empty( $spacing ) ) {
		$options['margin'] = (int) $spacing;
	}

	$wrapper_attrs .= ' data-plugin-options="' . esc_attr( json_encode( $options ) ) . '"';
}
if ( 'grid' == $view || 'slider' == $view ) {
	if ( $v_align ) {
		$wrapper_cls .= ' align-items-' . $v_align;
	} else {
		$wrapper_cls .= ' align-items-start';
	}
}

if ( 'creative' == $view || 'masonry' == $view ) {
	wp_enqueue_script( 'isotope' );
	$wrapper_cls   .= ' porto-gallery-' . $view;
	$wrapper_attrs .= ' data-plugin-masonry';

	$iso_options                 = array();
	$iso_options['layoutMode']   = 'masonry';
	$iso_options['itemSelector'] = empty( $click_action ) ? 'figure' : 'a';
	$extra_attrs                 = '';
	$grid_sizer                  = '';
	if ( 'creative' == $view ) {
		$porto_grid_layout  = porto_creative_grid_layout( $grid_layout );
		$grid_height_number = trim( preg_replace( '/[^0-9]/', '', $grid_height ) );
		$unit               = trim( str_replace( $grid_height_number, '', $grid_height ) );
		porto_creative_grid_style( $porto_grid_layout, $grid_height_number, '.' . trim( $shortcode_class ), false, true, $unit, empty( $click_action ) ? 'figure' : 'a', $grid_layout );
		$wrapper_cls           .= ' porto-preset-layout';
		$iso_options['masonry'] = array( 'columnWidth' => '.grid-col-sizer' );
	} else {
		$iso_options['masonry'] = array( 'columnWidth' => empty( $click_action ) ? 'figure' : 'a' );
	}
	$iso_options['animationEngine'] = 'best-available';
	$iso_options['resizable']       = false;
	$wrapper_attrs                 .= ' data-plugin-options="' . esc_attr( json_encode( $iso_options ) ) . '"';
}
if ( $animation_type ) {
	$wrapper_attrs .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$wrapper_attrs .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$wrapper_attrs .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}

if ( 'lightbox' == $click_action ) {
	$wrapper_cls   .= ' lightbox';
	$wrapper_attrs .= ' data-lightbox-options="' . esc_attr(
		json_encode(
			array(
				'delegate'  => 'a',
				'mainClass' => 'mfp-with-zoom',
				'zoom'      => array(
					'enabled'  => true,
					'duration' => 300,
				),
				'gallery'   => array(
					'enabled' => true,
				),
				'type'      => 'image',
			)
		)
	) . '"';
}

$item_cls = '';
if ( $hover_effect ) {
	if ( 'hoverdir' == $hover_effect ) {
		wp_enqueue_script( 'modernizr' );
		wp_enqueue_script( 'jquery-hoverdir', PORTO_SHORTCODES_URL . 'assets/js/jquery.hoverdir.min.js', array( 'jquery-core', 'modernizr' ), PORTO_SHORTCODES_VERSION, true );
		$item_cls .= ' hover-effect-dir';
	} else {
		$wrapper_cls .= ' porto-ig-' . $hover_effect;
		if ( 'overlay-icon' == $hover_effect ) {
			$item_cls .= ' hover-overlay';
		}
	}
}

if ( $el_class ) {
	$wrapper_cls .= ' ' . trim( $el_class );
}
echo '<div class="' . esc_attr( $wrapper_cls ) . '"' . $wrapper_attrs . '>';
foreach ( $images as $index => $img_id ) {
	$col_cls = trim( $item_cls );
	if ( 'creative' == $view && ! empty( $porto_grid_layout[ $index ] ) && isset( $porto_grid_layout[ $index % count( $porto_grid_layout ) ] ) ) {
		$grid_layout = $porto_grid_layout[ $index % count( $porto_grid_layout ) ];
		$col_cls    .= ' grid-col-' . $grid_layout['width'] . ' grid-col-md-' . $grid_layout['width_md'] . ( isset( $grid_layout['width_lg'] ) ? ' grid-col-lg-' . $grid_layout['width_lg'] : '' ) . ' grid-height-' . $grid_layout['height'];
		$image_size  = $grid_layout['size'];
	}

	$full_src = wp_get_attachment_image_src( $img_id['id'], 'full' );
	if ( ! $full_src ) {
		continue;
	}
	if ( ! empty( $click_action ) ) {
		echo '<a' . ( $col_cls ? ' class="' . esc_attr( $col_cls ) . '"' : '' ) . ' href="' . esc_url( $full_src[0] ) . '">';
	}
	echo '<figure' . ( $col_cls && ! $click_action ? ' class="' . esc_attr( $col_cls ) . '"' : '' ) . '>';

	if ( empty( $click_action ) && ( 'slider' != $view ) && ( 'zoom' == $hover_effect || 'hoverdir' == $hover_effect || 'overlay-icon' == $hover_effect ) ) {
		echo '<div class="image-wrapper">';
	}

	echo wp_get_attachment_image( $img_id['id'], $image_size ? $image_size : 'full' );
	if ( 'hoverdir' == $hover_effect || 'overlay-icon' == $hover_effect ) {
		echo '<div class="fill">';
		if ( ! empty( $click_action ) ) {
			echo '<i class="centered-icon ' . ( $icon_cl ? esc_attr( $icon_cl ) : 'fa fa-plus' ) . '"></i>';
		}
		echo '</div>';
	}

	if ( empty( $click_action ) && ( 'slider' != $view ) && ( 'zoom' == $hover_effect || 'hoverdir' == $hover_effect || 'overlay-icon' == $hover_effect ) ) {
		echo '</div>';
	}

	echo '</figure>';
	if ( ! empty( $click_action ) ) {
		echo '</a>';
	}
}
if ( 'creative' == $view ) {
	echo '<figure class="grid-col-sizer"></figure>';
}
echo '</div>';

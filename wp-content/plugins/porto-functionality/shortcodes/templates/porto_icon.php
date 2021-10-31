<?php

$icon_type      = $icon_img = $img_width = $icon = $icon_color = $icon_color_bg = $icon_size = $icon_style = $icon_border_style = $icon_border_radius = $icon_color_border = $icon_border_size = $icon_border_spacing = $icon_link = $el_class = $animation_type = $icon_align = '';
$icon_animation = '';
extract(
	shortcode_atts(
		array(
			'icon_type'           => 'fontawesome',
			'icon'                => '',
			'icon_simpleline'     => '',
			'icon_porto'          => '',
			'icon_img'            => '',
			'img_width'           => '48',
			'icon_size'           => '32',
			'icon_color'          => '',
			'icon_style'          => 'none',
			'icon_color_bg'       => '#ffffff',
			'icon_color_border'   => '#333333',
			'icon_border_style'   => '',
			'icon_border_size'    => '1',
			'icon_border_radius'  => '500',
			'icon_border_spacing' => '50',
			'icon_link'           => '',
			'animation_type'      => '',
			'icon_animation'      => '',
			'el_class'            => '',
			'icon_align'          => '',
			'css_porto_icon'      => '',
			'icon_margin_bottom'  => '',
			'icon_margin_left'    => '',
			'icon_margin_right'   => '',
		),
		$atts
	)
);

switch ( $icon_type ) {
	case 'simpleline':
		if ( $icon_simpleline ) {
			$icon = $icon_simpleline;
		}
		break;
	case 'porto':
		if ( $icon_porto ) {
			$icon = $icon_porto;
		}
		break;
}

if ( empty( $animation_type ) && ! empty( $icon_animation ) ) {
	$animation_type = $icon_animation;
}

if ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
	$css_porto_icon = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css_porto_icon, ' ' ), 'porto_icon', $atts );
}

$output = $style = $link_sufix = $link_prefix = $target = $href = $icon_align_style = $css_trans = $target = $link_title  = $rel = '';

if ( $animation_type ) {
	$css_trans = ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
}

$uniqid = uniqid();
if ( $icon_link && function_exists( 'vc_build_link' ) ) {
	$href         = vc_build_link( $icon_link );
	$url          = ( isset( $href['url'] ) && $href['url'] ) ? $href['url'] : '';
	$target       = ( isset( $href['target'] ) && $href['target'] ) ? "target='" . esc_attr( trim( $href['target'] ) ) . "'" : '';
	$link_title   = ( isset( $href['title'] ) && $href['title'] ) ? "title='" . esc_attr( $href['title'] ) . "'" : '';
	$rel          = ( isset( $href['rel'] ) && $href['rel'] ) ? "rel='" . esc_attr( $href['rel'] ) . "'" : '';
	$link_prefix .= '<a class="porto-tooltip ' . esc_attr( $uniqid ) . '" href = "' . esc_url( $url ) . '" ' . $target . ' ' . $rel . ' ' . $link_title . '>';
	$link_sufix  .= '</a>';
}

$elx_class = '';

if ( 'none' != $icon_style ) {
	//$icon_align = 'center';
}
/*if ( 'right' == $icon_align ) {
	$icon_align_style .= 'text-align:right;';
} elseif ( 'left' == $icon_align ) {
	$icon_align_style .= 'text-align:left;';
}*/
if ( $icon_align ) {
	$el_class = trim( 'porto-icon-pos-' . $icon_align . ' ' . $el_class );
}

if ( $icon_margin_bottom ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $icon_margin_bottom ) );
	if ( ! $unit ) {
		$icon_margin_bottom .= 'px';
	}
	$icon_align_style .= 'margin-bottom:' . esc_attr( $icon_margin_bottom ) . ';';
}

if ( 'custom' == $icon_type ) {
	$img = '';
	$alt = '';
	if ( $icon_img ) {
		if ( is_numeric( $icon_img ) ) {
			$attachment = wp_get_attachment_image_src( (int) $icon_img, 'full' );
		}
		if ( isset( $attachment ) && is_array( $attachment ) ) {
			$img = $attachment[0];
		} else {
			$img        = $icon_img;
			$attachment = array( $img, $img_width, $img_width );
		}
	}

	if ( 'none' !== $icon_style && $icon_color_bg ) {
		$style .= 'background:' . esc_attr( $icon_color_bg ) . ';';
	}
	if ( 'circle' == $icon_style ) {
		$elx_class .= ' porto-u-circle';
	}
	if ( 'circle_img' == $icon_style ) {
		$elx_class .= ' porto-u-circle-img';
		if ( isset( $attachment ) && $attachment[2] > $attachment[1] ) {
			$elx_class .= ' porto-u-img-tall';
		}
	}
	if ( 'square' == $icon_style ) {
		$elx_class .= ' porto-u-square';
	}
	if ( 'advanced' == $icon_style || 'circle_img' == $icon_style ) {
		if ( $icon_border_style ) {
			$style .= 'border-style:' . esc_attr( $icon_border_style ) . ';';
			if ( $icon_color_border ) {
				$style .= 'border-color:' . esc_attr( $icon_color_border ) . ';';
			}
			if ( $icon_border_size ) {
				$style .= 'border-width:' . esc_attr( $icon_border_size ) . 'px;';
			}
		}
		if ( $icon_border_spacing ) {
			$style .= 'padding:' . esc_attr( $icon_border_spacing ) . 'px;';
		}
		if ( $icon_border_radius ) {
			$style .= 'border-radius:' . esc_attr( $icon_border_radius ) . 'px;';
		}
	}

	if ( ! empty( $img ) ) {
		/*if ( '' == $icon_link || 'center' == $icon_align ) {
			$style .= 'display:inline-block;';
		}*/
		if ( $img_width ) {
			$style .= 'font-size: ' . esc_attr( $img_width ) . 'px;';
		}
		if ( $icon_margin_left ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $icon_margin_left ) );
			if ( ! $unit ) {
				$icon_margin_left .= 'px';
			}
			$style .= 'margin-' . ( is_rtl() ? 'right' : 'left' ) . ':' . esc_attr( $icon_margin_left ) . ';';
		}
		if ( $icon_margin_right ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $icon_margin_right ) );
			if ( ! $unit ) {
				$icon_margin_right .= 'px';
			}
			$style .= 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ':' . esc_attr( $icon_margin_right ) . ';';
		}


		$uniqid         = uniqid( rand() );
		$internal_style = '';
		if ( 'circle_img' == $icon_style && $icon_border_spacing ) {
			$internal_style         .= '<style>';
				$internal_style     .= '#porto-icon-' . esc_html( $uniqid ) . ' .porto-sicon-img.porto-u-circle-img:before {';
					$internal_style .= 'border-width: ' . ( esc_html( $icon_border_spacing ) + 1 ) . 'px;';
			if ( $icon_color_bg ) {
				$internal_style .= 'border-color: ' . esc_html( $icon_color_bg );
			}
				$internal_style .= '}';
			$internal_style     .= '</style>';
		}

		if ( $icon_align_style ) {
			$style .= $icon_align_style;
		}

		$output .= $internal_style . $link_prefix . '<div id="porto-icon-' . esc_attr( $uniqid ) . '" class="porto-just-icon-wrapper porto-sicon-img' . ( ( $el_class . $elx_class ) ? ' ' . esc_attr( $el_class . $elx_class ) : '' ) . ( $css_porto_icon ? ' ' . esc_attr( $css_porto_icon ) : '' ) . '" style="' . esc_attr( $style ) . '"' . $css_trans . '>';
		$output .= '<img class="img-icon" alt="' . esc_attr( $alt ) . '" src="' . esc_url( $img ) . '" width="' . esc_attr( $attachment[1] ) . '" height="' . esc_attr( $attachment[2] ) . '" />';
		$output .= '</div>' . $link_sufix;
	}
} else {
	if ( $icon_color ) {
		$style .= 'color:' . $icon_color . ';';
	}
	if ( 'none' !== $icon_style ) {
		if ( $icon_color_bg ) {
			$style .= 'background:' . $icon_color_bg . ';';
		}
	}
	if ( 'advanced' == $icon_style ) {
		if ( $icon_border_style ) {
			$style .= 'border-style:' . $icon_border_style . ';';
			if ( $icon_color_border ) {
				$style .= 'border-color:' . $icon_color_border . ';';
			}
			if ( $icon_border_size ) {
				$style .= 'border-width:' . $icon_border_size . 'px;';
			}
		}
		if ( $icon_border_spacing ) {
			$style .= 'width:' . $icon_border_spacing . 'px;';
			$style .= 'height:' . $icon_border_spacing . 'px;';
			$style .= 'line-height:' . $icon_border_spacing . 'px;';
		}
		if ( $icon_border_radius ) {
			$style .= 'border-radius:' . $icon_border_radius . 'px;';
		}
	}
	if ( $icon_size ) {
		$style .= 'font-size:' . $icon_size . 'px;';
	}
	if ( $icon ) {
		if ( $icon_margin_left ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $icon_margin_left ) );
			if ( ! $unit ) {
				$icon_margin_left .= 'px';
			}
			$style .= 'margin-' . ( is_rtl() ? 'right' : 'left' ) . ':' . esc_attr( $icon_margin_left ) . ';';
		}
		if ( $icon_margin_right ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $icon_margin_right ) );
			if ( ! $unit ) {
				$icon_margin_right .= 'px';
			}
			$style .= 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ':' . esc_attr( $icon_margin_right ) . ';';
		}

		if ( $icon_align_style ) {
			$style .= $icon_align_style;
		}

		$output .= $link_prefix . '<div class="porto-just-icon-wrapper porto-icon ' . esc_attr( $icon_style ) . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . ( $css_porto_icon ? ' ' . esc_attr( $css_porto_icon ) : '' ) . '"' . $css_trans . ' style="' . esc_attr( $style ) . '">';
		if ( defined( 'ELEMENTOR_VERSION' ) && 'svg' === $icon_type ) {
			ob_start();
			\ELEMENTOR\Icons_Manager::render_icon(
				array(
					'library' => 'svg',
					'value'   => array( 'id' => absint( $icon ) ),
				),
				array( 'aria-hidden' => 'true' )
			);
			$output .= ob_get_clean();
		} else {
			$output .= '<i class="' . esc_attr( $icon ) . '"></i>';
		}
		$output .= '</div>' . $link_sufix;
	}
}

echo porto_filter_output( $output );

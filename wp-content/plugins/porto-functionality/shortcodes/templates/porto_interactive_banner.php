<?php

$banner_title              = $banner_desc = $banner_image = $banner_link = $banner_style = $el_class = '';
$banner_title_font_size    = '';
$banner_title_style_inline = $banner_desc_style_inline = $banner_color_bg = $banner_color_title = $banner_color_desc = $banner_title_bg = '';

$animation_type     = '';
$animation_delay    = '';
$animation_duration = '';

$image_opacity = $image_opacity_on_hover = $target = $link_title  = $rel = '';
extract(
	shortcode_atts(
		array(
			'banner_title'           => '',
			'banner_desc'            => '',
			'banner_image'           => '',
			'banner_video'           => '',
			'lazyload'               => '',
			'image_opacity'          => '1',
			'image_opacity_on_hover' => '1',
			'banner_style'           => '',
			'banner_title_font_size' => '',
			'banner_color_bg'        => '',
			'banner_color_title'     => '',
			'banner_color_desc'      => '',
			'banner_title_bg'        => '',
			'banner_link'            => '',
			'min_height'             => '',
			'add_container'          => '',
			'parallax'               => '',
			'overlay_color'          => '',
			'overlay_opacity'        => 0.08,
			'box_shadow'             => '',
			'el_class'               => '',
			'css_ibanner'            => '',
			'className'              => '',
			'animation_type'         => '',
			'animation_duration'     => 1000,
			'animation_delay'        => 0,
			'align'                  => '',
		),
		$atts
	)
);

if ( $className ) {
	if ( $el_class ) {
		$el_class .= ' ' . $className;
	} else {
		$el_class = $className;
	}
}
if ( ( ! isset( $content ) || empty( $content ) ) && isset( $atts['content'] ) && ! empty( $atts['content'] ) ) {
	$content = $atts['content'];
}

$css_ib_styles = '';
if ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
	$css_ib_styles = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css_ibanner, ' ' ), 'porto_interactive_banner', $atts );
}
global $porto_settings_optimize;

$output = $target = $link = $banner_style_inline = $title_bg = $img_style = $target = '';

if ( $banner_title_bg && 'style2' == $banner_style ) {
	$title_bg .= 'background:' . esc_attr( $banner_title_bg ) . ';';
}

$img             = '';
$internal_styles = '';
if ( $image_opacity && '1' != $image_opacity ) {
	$img_style .= 'opacity:' . esc_attr( $image_opacity ) . ';';
}
if ( $banner_image ) {
	global $porto_carousel_lazyload;
	$img_attr = array();
	if ( $lazyload ) {
		if ( isset( $porto_carousel_lazyload ) && true === $porto_carousel_lazyload ) {
			$img_attr['class'] = 'porto-ibanner-img owl-lazy';
		} else {
			wp_enqueue_script( 'jquery-lazyload' );

			$img_attr['class'] = 'porto-ibanner-img porto-lazyload';
		}
	} else {
		$img_attr['class'] = 'porto-ibanner-img';
	}
	if ( $img_style ) {
		$img_attr['style'] = $img_style;
	}
	if ( is_numeric( $banner_image ) ) {
		$img_data = wp_get_attachment_image_src( $banner_image, 'full' );
		if ( is_array( $img_data ) ) {
			if ( $lazyload ) {
				$placeholder          = porto_generate_placeholder( $img_data[1] . 'x' . $img_data[2] );
				$img_attr['src']      = esc_url( $placeholder[0] );
				$img_attr['data-src'] = esc_url( $img_data[0] );
			}

			// Generate 'srcset' and 'sizes'
			$image_meta = wp_get_attachment_metadata( $banner_image );
			if ( $min_height ) {
				$unit = trim( preg_replace( '/[0-9.]/', '', $min_height ) );
				if ( ! $unit || 'px' == $unit ) {
					if ( is_array( $image_meta ) && is_array( $image_meta['sizes'] ) ) {
						$ratio = $image_meta['height'] / $image_meta['width'];
						foreach ( $image_meta['sizes'] as $key => $size ) {
							if ( $size['width'] * (float) $ratio < (int) $min_height ) {
								unset( $image_meta['sizes'][ $key ] );
							}
						}
					}
				}
			}
			$srcset = wp_get_attachment_image_srcset( $banner_image, 'full', $image_meta );
			$sizes  = wp_get_attachment_image_sizes( $banner_image, 'full', $image_meta );
			if ( $srcset && $sizes ) {
				$img_attr['srcset'] = $srcset;
				$img_attr['sizes']  = $sizes;
			}

			$attr_str_escaped = '';
			foreach ( $img_attr as $key => $val ) {
				$attr_str_escaped .= ' ' . esc_html( $key ) . '="' . esc_attr( $val ) . '"';
			}
			$img = '<img src="' . esc_url( $img_data[0] ) . '" alt="' . esc_attr( trim( get_post_meta( $banner_image, '_wp_attachment_image_alt', true ) ) ) . '" width="' . esc_attr( $img_data[1] ) . '" height="' . esc_attr( $img_data[2] ) . '"' . $attr_str_escaped . '>';
		}
	} else {
		if ( $lazyload ) {
			$placeholder          = porto_generate_placeholder( '1x1' );
			$img_attr['src']      = esc_url( $placeholder[0] );
			$img_attr['data-src'] = esc_url( $banner_image );
		} else {
			$img_attr['src'] = esc_url( $banner_image );
		}
		$img_attr_html = '';
		foreach ( $img_attr as $name => $value ) {
			$img_attr_html .= " $name=" . '"' . $value . '"';
		}
		$img = '<img alt=""' . $img_attr_html . ' />';
	}
}

if ( $banner_link ) {
	if ( function_exists( 'vc_build_link' ) ) {
		$href = vc_build_link( $banner_link );
		if ( ! empty( $href['url'] ) ) {
			$link       = ( isset( $href['url'] ) && $href['url'] ) ? $href['url'] : '';
			$target     = ( isset( $href['target'] ) && $href['target'] ) ? "target='" . esc_attr( trim( $href['target'] ) ) . "'" : '';
			$link_title = ( isset( $href['title'] ) && $href['title'] ) ? "title='" . esc_attr( $href['title'] ) . "'" : '';
			$rel        = ( isset( $href['rel'] ) && $href['rel'] ) ? "rel='" . esc_attr( $href['rel'] ) . "'" : '';
		} else {
			$link = $banner_link;
		}
	} else {
		$link = $banner_link;
	}
} else {
	$link = '#';
}

if ( ! is_numeric( $banner_title_font_size ) ) {
	$banner_title_font_size = preg_replace( '/[^0-9]/', '', $banner_title_font_size );
}
if ( $banner_title_font_size ) {
	$banner_title_style_inline .= 'font-size: ' . esc_attr( $banner_title_font_size ) . 'px;';
}

$interactive_banner_id = 'interactive-banner-wrap-' . rand( 1000, 9999 );
$classes               = 'porto-ibanner';

if ( $banner_color_bg ) {
	$banner_style_inline .= 'background:' . esc_attr( $banner_color_bg ) . ';';
}
if ( $min_height ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $min_height ) );
	if ( ! $unit ) {
		$min_height .= 'px';
	}
	$banner_style_inline .= 'min-height:' . esc_attr( $min_height ) . ';';
}

if ( $banner_color_title ) {
	$banner_title_style_inline .= 'color:' . esc_attr( $banner_color_title ) . ';';
}

if ( $banner_color_desc ) {
	$banner_desc_style_inline .= 'color:' . esc_attr( $banner_color_desc ) . ';';
}

if ( '#' !== $link ) {
	$href = 'href="' . esc_url( $link ) . '"';
} else {
	$href = '';
}

$heading_tag = 'h2';

$opacity_attr = '';


if ( $image_opacity != $image_opacity_on_hover ) {
	$internal_styles .= '#' . $interactive_banner_id . ' .porto-ibanner-img {';
	$internal_styles .= 'opacity:' . esc_attr( $image_opacity ) . ';';
	$internal_styles .= '}';
}

if ( $image_opacity != $image_opacity_on_hover ) {
	$internal_styles .= '#' . $interactive_banner_id . ':hover .porto-ibanner-img {';
	$internal_styles .= 'opacity:' . esc_attr( $image_opacity_on_hover ) . ';';
	$internal_styles .= '}';
}
if ( 'boxshadow' == $banner_style && $box_shadow ) {
	$data = porto_get_box_shadow( $box_shadow, 'data' );
	if ( $data ) {
		if ( strpos( $data, 'none' ) !== false || strpos( $data, ':;' ) !== false ) {
			$data = 'none';
		}
		if ( strpos( $data, 'inherit' ) !== false ) {
			$data = 'inherit';
		}
		$internal_styles .= '#' . $interactive_banner_id . ':hover{box-shadow:' . esc_attr( $data ) . ';}';
	}
}


if ( $animation_type ) {
	$opacity_attr .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$opacity_attr .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$opacity_attr .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}

if ( $banner_style ) {
	$classes .= ' porto-ibe-' . $banner_style;
	if ( 'overlay' == $banner_style && $overlay_color && $overlay_opacity ) {
		$internal_styles .= '#' . esc_html( $interactive_banner_id ) . ':hover:before { background-color: ' . esc_html( $overlay_color ) . '; opacity: ' . esc_html( $overlay_opacity ) . ' }';
	}
}
if ( trim( $css_ib_styles ) ) {
	$classes .= ' ' . trim( $css_ib_styles );
}
if ( trim( $el_class ) ) {
	$classes .= ' ' . trim( $el_class );
}
if ( $align ) {
	$classes .= ' align' . $align;
}

// lazy load background image
if ( isset( $porto_settings_optimize['lazyload'] ) && $porto_settings_optimize['lazyload'] ) {
	preg_match( '/\.vc_custom_[^}]*(background-image:[^(]*([^)]*)|background:\s#[A-Fa-f0-9]{3,6}\s*url\(([^)]*))/', $css_ibanner, $matches );
	if ( ! empty( $matches[2] ) || ! empty( $matches[3] ) ) {
		$image_url     = ! empty( $matches[2] ) ? $matches[2] : $matches[3];
		$opacity_attr .= ' data-original="' . esc_url( trim( str_replace( array( '(', ')' ), '', $image_url ) ) ) . '"';
		$classes      .= ' porto-lazyload';
	}
}

// parallax
if ( $parallax && $banner_image ) {
	wp_enqueue_script( 'skrollr' );
	if ( is_numeric( $banner_image ) ) {
		$image_url = wp_get_attachment_image_url( $banner_image, 'full' );
	} else {
		$image_url = $banner_image;
	}
	$opacity_attr .= ' data-plugin-parallax data-plugin-options="' . esc_attr( json_encode( array( 'speed' => $parallax ) ) ) . '"';
	$opacity_attr .= ' data-image-src="' . esc_url( $image_url ) . '"';
	$classes      .= ' has-parallax-bg';
}

// video banner
if ( empty( $banner_image ) && $banner_video && strrpos( $banner_video, '.mp4' ) !== false ) {
	wp_enqueue_script( 'jquery-vide' );
	$classes      .= ' section-video';
	$opacity_attr .= ' data-video-path="' . esc_url( str_replace( '.mp4', '', $banner_video ) ) . '"';
	$opacity_attr .= ' data-plugin-video-background';
	$opacity_attr .= ' data-plugin-options="{\'posterType\': \'jpg\', \'position\': \'50% 50%\', \'overlay\': true}"';
}

$output .= '<div id="' . esc_attr( $interactive_banner_id ) . '" class="' . esc_attr( $classes ) . '" style="' . esc_attr( $banner_style_inline ) . '"' . $opacity_attr . '>';
if ( $internal_styles ) {
	$output .= '<style scope="scope">';
	$output .= $internal_styles;
	$output .= '</style>';
}
if ( $img ) {
	$output .= $img;
}
if ( $banner_title || $banner_desc || $content ) {
	$output .= '<div class="porto-ibanner-desc' . ( $content && ( false !== strpos( $content, '[porto_interactive_banner_layer ' ) || false !== strpos( $content, 'class="porto-ibanner-layer' ) ) ? ' no-padding d-flex' : '' ) . '"' . ( $title_bg ? ' style="' . esc_attr( $title_bg ) . '"' : '' ) . '>';
	if ( $banner_title ) {
		$output .= '<' . $heading_tag . ' class="porto-ibanner-title" style="' . esc_attr( $banner_title_style_inline ) . '">' . do_shortcode( $banner_title ) . '</' . $heading_tag . '>';
	}
	if ( $content && ( false !== strpos( $content, '[porto_interactive_banner_layer ' ) || false !== strpos( $content, 'class="porto-ibanner-layer' ) ) ) {
		if ( $add_container ) {
			$output .= '<div class="container"><div class="porto-ibanner-container">';
		}
		$output .= do_shortcode( $content );
		if ( $add_container ) {
			$output .= '</div></div>';
		}
	} else {
		$output .= '<div class="porto-ibanner-content" style="' . esc_attr( $banner_desc_style_inline ) . '">';
		$output .= do_shortcode( $banner_desc ? $banner_desc : $content );
		$output .= '</div>';
	}
	$output .= '</div>';
}
if ( $href ) {
	$output .= '<a class="porto-ibanner-link" ' . $href . ' ' . $target . ' ' . $link_title . ' ' . $rel . '></a>';
}
$output .= '</div>';

echo porto_filter_output( $output );

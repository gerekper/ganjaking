<?php

$icon_type = $icon_img = $img_width = $icon = $icon_color = $icon_color_bg = $icon_size = $icon_style = $icon_border_style = $icon_border_radius = $icon_color_border = $icon_border_size = $icon_border_spacing = $icon_link = $el_class = $animation_type = $icon_margin = $target = $link_title  = $rel = $css_trans = '';
extract(
	shortcode_atts(
		array(
			'icon_type'           => 'fontawesome',
			'icon'                => '',
			'icon_simpleline'     => '',
			'icon_porto'          => '',
			'icon_size'           => '',
			'icon_color'          => '',
			'icon_style'          => '',
			'icon_color_bg'       => '',
			'icon_color_border'   => '',
			'icon_border_style'   => '',
			'icon_border_size'    => '',
			'icon_border_radius'  => '',
			'icon_border_spacing' => '',
			'icon_link'           => '',
			'icon_margin'         => '',
			'animation_type'      => '',
			'el_class'            => '',
			'className'           => '',
		),
		$atts
	)
);

switch ( $icon_type ) {
	case 'simpleline':
		$icon = $icon_simpleline;
		break;
	case 'porto':
		$icon = $icon_porto;
		break;
}

if ( $className ) {
	if ( $el_class ) {
		$el_class .= ' ' . $className;
	} else {
		$el_class = $className;
	}
}

if ( 'none' !== $animation_type && $animation_type ) {
	$css_trans = 'data-appear-animation="' . esc_attr( $animation_type ) . '"';
}
$output = $style = $link_sufix = $link_prefix = $target = $href = $icon_align_style = '';
if ( $icon_link ) {
	if ( is_array( $icon_link ) && isset( $icon_link['url'] ) ) {
		$url          = $icon_link['url'];
		$target       = isset( $icon_link['is_external'] ) && 'on' == $icon_link['is_external'] ? ' target="_blank"' : '';
		$rel          = isset( $icon_link['nofollow'] ) && 'on' == $icon_link['nofollow'] ? ' rel="nofollow' . ( $target ? ' noopener noreferrer' : '' ) . '"' : ( $target ? ' rel="noopener noreferrer"' : '' );
		$link_prefix .= '<a href = "' . esc_url( $url ) . '"' . $target . $rel . '>';
		$link_sufix  .= '</a>';
	} elseif ( function_exists( 'vc_build_link' ) ) {
		$href         = vc_build_link( $icon_link );
		$url          = ( isset( $href['url'] ) && $href['url'] ) ? $href['url'] : '';
		$target       = ( isset( $href['target'] ) && $href['target'] ) ? " target='" . esc_attr( trim( $href['target'] ) ) . "'" : '';
		$link_title   = ( isset( $href['title'] ) && $href['title'] ) ? " title='" . esc_attr( $href['title'] ) . "'" : '';
		$rel          = ( isset( $href['rel'] ) && $href['rel'] ) ? " rel='" . esc_attr( $href['rel'] ) . "'" : '';
		$link_prefix .= '<a href="' . esc_url( $url ) . '"' . $target . $link_title . $rel . '>';
		$link_sufix  .= '</a>';
	}
}

if ( $icon_color ) {
	$style .= 'color:' . esc_attr( $icon_color ) . ';';
}
if ( 'none' !== $icon_style ) {
	if ( $icon_color_bg ) {
		$style .= 'background:' . esc_attr( $icon_color_bg ) . ';';
	}
}
if ( 'advanced' == $icon_style ) {
	if ( $icon_border_style ) {
		$style .= 'border-style:' . esc_attr( $icon_border_style ) . ';';
	}
	if ( $icon_color_border ) {
		$style .= 'border-color:' . esc_attr( $icon_color_border ) . ';';
	}
	if ( $icon_border_size ) {
		$style .= 'border-width:' . esc_attr( $icon_border_size ) . 'px;';
	}
	$style .= 'width:' . esc_attr( $icon_border_spacing ) . 'px;';
	$style .= 'height:' . esc_attr( $icon_border_spacing ) . 'px;';
	$style .= 'line-height:' . esc_attr( $icon_border_spacing ) . 'px;';
	$style .= 'border-radius:' . esc_attr( $icon_border_radius ) . 'px;';
}
if ( $icon_size ) {
	$style .= 'font-size:' . $icon_size . 'px;';
}

if ( $icon_margin ) {
	$style .= 'margin-right:' . $icon_margin . 'px;';
}

if ( $icon ) {
	$output .= "\n" . $link_prefix . '<div class="porto-icon ' . esc_attr( $icon_style ) . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '" ' . $css_trans . ' style="' . esc_attr( $style ) . '">';
	$output .= "\n\t" . '<i class="' . esc_attr( $icon ) . '"></i>';
	$output .= "\n" . '</div>' . $link_sufix;
}
echo porto_filter_output( $output );

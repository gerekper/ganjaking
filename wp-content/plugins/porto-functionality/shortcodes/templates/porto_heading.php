<?php
$atts = shortcode_atts(
	array(
		'title'                => '',
		'font_family'          => '',
		'font_size'            => '',
		'font_weight'          => '',
		'text_transform'       => '',
		'line_height'          => '',
		'letter_spacing'       => '',
		'color'                => '',
		'tag'                  => 'h2',
		'link'                 => '',
		'alignment'            => '',
		'show_border'          => '',
		'border_width'         => '',
		'border_color'         => '',
		'enable_typewriter'    => false,
		'typewriter_animation' => 'fadeIn',
		'typewriter_delay'     => 0,
		'typewriter_width'     => 0,
		'animation_type'       => '',
		'animation_duration'   => 1000,
		'animation_delay'      => 0,
		'className'            => '',
	),
	$atts
);

$style_inline = '';
if ( ! empty( $atts['font_family'] ) ) {
	$style_inline .= 'font-family:' . $atts['font_family'] . ';';
}
if ( $atts['font_size'] ) {
	$unit = preg_replace( '/[0-9.]/', '', $atts['font_size'] );
	if ( ! $unit ) {
		$atts['font_size'] .= 'px';
	}
	$style_inline .= 'font-size:' . $atts['font_size'] . ';';
}
if ( $atts['font_weight'] ) {
	$style_inline .= 'font-weight:' . intval( $atts['font_weight'] ) . ';';
}
if ( ! empty( $atts['text_transform'] ) ) {
	$style_inline .= 'text-transform:' . $atts['text_transform'] . ';';
}
if ( $atts['line_height'] ) {
	$unit = preg_replace( '/[0-9.]/', '', $atts['line_height'] );
	if ( ! $unit && $atts['line_height'] > 3 ) {
		$atts['line_height'] .= 'px';
	}
	$style_inline .= 'line-height:' . $atts['line_height'] . ';';
}
if ( $atts['letter_spacing'] || '0' === $atts['letter_spacing'] ) {
	$style_inline .= 'letter-spacing:' . $atts['letter_spacing'] . ';';
}
if ( $atts['color'] ) {
	$style_inline .= 'color:' . $atts['color'] . ';';
}
if ( $atts['show_border'] && $atts['border_width'] ) {
	$unit = preg_replace( '/[0-9.]/', '', $atts['border_width'] );
	if ( ! $unit ) {
		$atts['border_width'] .= 'px';
	}
	$style_inline .= 'border-width:' . $atts['border_width'] . ';';
	if ( $atts['border_color'] ) {
		$style_inline .= 'border-color:' . $atts['border_color'] . ';';
	}
	if ( 'middle' == $atts['show_border'] ) {
		$atts['alignment'] = 'center';
	} elseif ( 'middle-left' == $atts['show_border'] ) {
		$atts['alignment'] = 'left';
	} elseif ( 'middle-right' == $atts['show_border'] ) {
		$atts['alignment'] = 'right';
	}
}
if ( $atts['alignment'] ) {
	$style_inline .= 'text-align:' . $atts['alignment'] . ';';
}

$animation_attrs = '';
if ( $atts['animation_type'] ) {
	$animation_attrs .= ' data-appear-animation="' . esc_attr( $atts['animation_type'] ) . '"';
	if ( $atts['animation_delay'] ) {
		$animation_attrs .= ' data-appear-animation-delay="' . esc_attr( $atts['animation_delay'] ) . '"';
	}
	if ( $atts['animation_duration'] && 1000 != $atts['animation_duration'] ) {
		$animation_attrs .= ' data-appear-animation-duration="' . esc_attr( $atts['animation_duration'] ) . '"';
	}
}

$type_plugin = '';
if ( ! empty( $atts['enable_typewriter'] ) ) {
	$typewriter_options = array(
		'startDelay'     => 0,
		'minWindowWidth' => 0,
	);
	if ( ! empty( $atts['typewriter_delay'] ) ) {
		$typewriter_options['startDelay'] = (int) $atts['typewriter_delay'];
	}
	if ( ! empty( $atts['typewriter_width'] ) ) {
		$typewriter_options['minWindowWidth'] = (int) $atts['typewriter_width'];
	}
	if ( ! empty( $atts['typewriter_animation'] ) ) {
		$typewriter_options['animationName'] = $atts['typewriter_animation'];
	}
	$type_plugin .= ' data-plugin-animated-letters data-plugin-options="' . esc_attr( json_encode( $typewriter_options ) ) . '"';
}

$result = '';

$result .= '<' . esc_html( $atts['tag'] ) . ' class="porto-heading' . ( $atts['show_border'] ? ' has-border border-' . esc_attr( $atts['show_border'] ) : '' ) . ( $atts['className'] ? ' ' . esc_attr( trim( $atts['className'] ) ) : '' ) . '" style="' . esc_attr( $style_inline ) . '"' . $animation_attrs . $type_plugin . '>';
if ( $atts['link'] ) {
	$result .= '<a href="' . esc_url( $atts['link'] ) . '">';
}
$result .= wp_kses_post( $atts['title'] );
if ( $atts['link'] ) {
	$result .= '</a>';
}
$result .= '</' . esc_html( $atts['tag'] ) . '>';

echo porto_filter_output( $result );

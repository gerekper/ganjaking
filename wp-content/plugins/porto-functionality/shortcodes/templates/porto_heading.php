<?php
$settings     = shortcode_atts(
	array(
		'text_source'          => '',
		'title'                => '',
		'dynamic_content'      => '',
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
if ( ! empty( $settings['font_family'] ) ) {
	$style_inline .= 'font-family:' . $settings['font_family'] . ';';
}
if ( $settings['font_size'] ) {
	$unit = preg_replace( '/[0-9.]/', '', $settings['font_size'] );
	if ( ! $unit ) {
		$settings['font_size'] .= 'px';
	}
	$style_inline .= 'font-size:' . $settings['font_size'] . ';';
}
if ( $settings['font_weight'] ) {
	$style_inline .= 'font-weight:' . intval( $settings['font_weight'] ) . ';';
}
if ( ! empty( $settings['text_transform'] ) ) {
	$style_inline .= 'text-transform:' . $settings['text_transform'] . ';';
}
if ( $settings['line_height'] ) {
	$unit = preg_replace( '/[0-9.]/', '', $settings['line_height'] );
	if ( ! $unit && $settings['line_height'] > 3 ) {
		$settings['line_height'] .= 'px';
	}
	$style_inline .= 'line-height:' . $settings['line_height'] . ';';
}
if ( $settings['letter_spacing'] || '0' === $settings['letter_spacing'] ) {
	$style_inline .= 'letter-spacing:' . $settings['letter_spacing'] . ';';
}
if ( $settings['color'] ) {
	$style_inline .= 'color:' . $settings['color'] . ';';
}
if ( $settings['show_border'] && $settings['border_width'] ) {
	$unit = preg_replace( '/[0-9.]/', '', $settings['border_width'] );
	if ( ! $unit ) {
		$settings['border_width'] .= 'px';
	}
	$style_inline .= 'border-width:' . $settings['border_width'] . ';';
	if ( $settings['border_color'] ) {
		$style_inline .= 'border-color:' . $settings['border_color'] . ';';
	}
	if ( 'middle' == $settings['show_border'] ) {
		$settings['alignment'] = 'center';
	} elseif ( 'middle-left' == $settings['show_border'] ) {
		$settings['alignment'] = 'left';
	} elseif ( 'middle-right' == $settings['show_border'] ) {
		$settings['alignment'] = 'right';
	}
}
if ( $settings['alignment'] ) {
	$style_inline .= 'text-align:' . $settings['alignment'] . ';';
}

$animation_attrs = '';
if ( $settings['animation_type'] ) {
	$animation_attrs .= ' data-appear-animation="' . esc_attr( $settings['animation_type'] ) . '"';
	if ( $settings['animation_delay'] ) {
		$animation_attrs .= ' data-appear-animation-delay="' . esc_attr( $settings['animation_delay'] ) . '"';
	}
	if ( $settings['animation_duration'] && 1000 != $settings['animation_duration'] ) {
		$animation_attrs .= ' data-appear-animation-duration="' . esc_attr( $settings['animation_duration'] ) . '"';
	}
}

$type_plugin = '';
if ( ! empty( $settings['enable_typewriter'] ) ) {
	$typewriter_options = array(
		'startDelay'     => 0,
		'minWindowWidth' => 0,
	);
	if ( ! empty( $settings['typewriter_delay'] ) ) {
		$typewriter_options['startDelay'] = (int) $settings['typewriter_delay'];
	}
	if ( ! empty( $settings['typewriter_width'] ) ) {
		$typewriter_options['minWindowWidth'] = (int) $settings['typewriter_width'];
	}
	if ( ! empty( $settings['typewriter_animation'] ) ) {
		$typewriter_options['animationName'] = $settings['typewriter_animation'];
	}
	$type_plugin .= ' data-plugin-animated-letters data-plugin-options="' . esc_attr( json_encode( $typewriter_options ) ) . '"';
}

$result = '';

$result .= '<' . esc_html( $settings['tag'] ) . ' class="' . esc_attr( apply_filters( 'porto_elements_wrap_css_class', 'porto-heading' . ( $settings['show_border'] ? ' has-border border-' . esc_attr( $settings['show_border'] ) : '' ) . ( $settings['className'] ? ' ' . trim( $settings['className'] ) : '' ), $atts, 'heading' ) ) . '" style="' . esc_attr( $style_inline ) . '"' . $animation_attrs . $type_plugin . '>';

$show_link = false;
if ( $settings['link'] ) {
	$show_link = true;
	$result   .= '<a href="' . esc_url( $settings['link'] ) . '">';
} elseif ( isset( $settings['dynamic_content']['source'] ) && 'post' == $settings['dynamic_content']['source'] && isset( $settings['dynamic_content']['post_info'] ) && 'title' == $settings['dynamic_content']['post_info'] ) {
	$show_link = true;
	$result   .= '<a href="' . esc_url( get_permalink() ) . '">';
} elseif ( isset( $settings['dynamic_content']['source'] ) && 'tax' == $settings['dynamic_content']['source'] && isset( $settings['dynamic_content']['tax'] ) && 'title' == $settings['dynamic_content']['tax'] && get_queried_object() && isset( get_queried_object()->term_id ) ) {
	$show_link = true;
	$result   .= '<a href="' . esc_url( get_term_link( get_queried_object() ) ) . '">';
}

if ( empty( $settings['text_source'] ) ) {
	$result .= wp_kses_post( $settings['title'] );
} elseif ( $settings['dynamic_content'] && $settings['dynamic_content']['source'] ) {
	$field_name = '';
	if ( 'post' == $settings['dynamic_content']['source'] ) {
		if ( isset( $settings['dynamic_content']['post_info'] ) ) {
			$field_name = $settings['dynamic_content']['post_info'];
		}
	} else {
		if ( isset( $settings['dynamic_content'][ $settings['dynamic_content']['source'] ] ) ) {
			$field_name = $settings['dynamic_content'][ $settings['dynamic_content']['source'] ];
		}
	}
	$value = '';
	if ( $field_name ) {
		$value = apply_filters( 'porto_dynamic_tags_content', '', null, $settings['dynamic_content']['source'], $field_name );
	}
	if ( ! $value && ! empty( $settings['dynamic_content']['fallback'] ) ) {
		$value = porto_strip_script_tags( $settings['dynamic_content']['fallback'] );
	}
	if ( ! empty( $settings['dynamic_content']['before'] ) ) {
		$value = porto_strip_script_tags( $settings['dynamic_content']['before'] ) . $value;
	}
	if ( ! empty( $settings['dynamic_content']['after'] ) ) {
		$value .= porto_strip_script_tags( $settings['dynamic_content']['after'] );
	}
	$result .= $value;
}

if ( $show_link ) {
	$result .= '</a>';
}
$result .= '</' . esc_html( $settings['tag'] ) . '>';

echo porto_filter_output( $result );

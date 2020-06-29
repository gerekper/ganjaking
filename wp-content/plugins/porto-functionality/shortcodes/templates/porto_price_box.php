<?php
$output = $title = $desc = $is_popular = $popular_label = $price = $skin = $show_btn = $btn_label = $btn_action = $popup_iframe = $popup_block = $popup_size = $popup_animation = $btn_link = $btn_size = $btn_pos = $btn_skin = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'title'              => '',
			'desc'               => '',
			'is_popular'         => false,
			'popular_label'      => '',
			'price'              => '',
			'price_unit'         => '',
			'price_label'        => '',
			'skin'               => 'custom',
			'show_btn'           => false,
			'btn_label'          => '',
			'btn_action'         => 'open_link',
			'btn_link'           => '',
			'popup_iframe'       => '',
			'popup_block'        => '',
			'popup_size'         => 'md',
			'popup_animation'    => 'mfp-fade',
			'btn_style'          => '',
			'btn_size'           => '',
			'btn_pos'            => '',
			'btn_skin'           => 'custom',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

if ( $is_popular ) {
	$el_class .= ' most-popular';
}

if ( $skin ) {
	$el_class .= ' plan-' . esc_attr( $skin );
}

$btn_class = 'btn btn-modern';
if ( $btn_style ) {
	$btn_class .= ' btn-' . esc_attr( $btn_style );
}
$btn_html = '';
if ( $btn_size ) {
	$btn_class .= ' btn-' . esc_attr( $btn_size );
}
if ( 'custom' !== $btn_skin ) {
	$btn_class .= ' btn-' . esc_attr( $btn_skin );
} else {
	$btn_class .= ' btn-default';
}
if ( 'bottom' !== $btn_pos ) {
	$btn_class .= ' btn-top';
} else {
	$btn_class .= ' btn-bottom';
}

if ( 'open_link' === $btn_action ) {
	$link = ( '||' === $btn_link ) ? '' : $btn_link;
	if ( function_exists( 'vc_build_link' ) ) {
		$link     = vc_build_link( $link );
		$use_link = false;
		if ( strlen( $link['url'] ) > 0 ) {
			$use_link = true;
			$a_href   = $link['url'];
			$a_title  = $link['title'];
			$a_target = strlen( $link['target'] ) > 0 ? $link['target'] : '_self';
		} else {
			$link = 'url:' . urlencode( $btn_link ) . '||';
			$link = vc_build_link( $link );
			if ( strlen( $link['url'] ) > 0 ) {
				$use_link = true;
				$a_href   = $link['url'];
				$a_title  = $link['title'];
				$a_target = strlen( $link['target'] ) > 0 ? $link['target'] : '_self';
			}
		}
	} elseif ( $link ) {
		$use_link = true;
		if ( is_array( $link ) && isset( $link['url'] ) ) {
			$a_href   = $link['url'];
			$a_target = isset( $link['is_external'] ) && 'on' == $link['is_external'] ? ' target="_blank"' : '';
			$a_title  = '';
		} else {
			$a_href   = $link;
			$a_title  = '';
			$a_target = '';
		}
	}

	$attributes = array();
	if ( $use_link ) {
		$attributes[] = 'href="' . esc_url( trim( $a_href ) ) . '"';
		if ( $a_title ) {
			$attributes[] = 'title="' . esc_attr( trim( $a_title ) ) . '"';
		}
		if ( $a_target ) {
			$attributes[] = 'target="' . esc_attr( trim( $a_target ) ) . '"';
		}
	}

	$attributes = implode( ' ', $attributes );

	if ( $use_link ) {
		$btn_html .= '<a ' . $attributes . ' class="' . $btn_class . '">' . esc_html( $btn_label ) . '</a>';
	} else {
		$btn_html .= '<span class="' . $btn_class . '">' . esc_html( $btn_label ) . '</span>';
	}
} elseif ( 'popup_iframe' === $btn_action ) {
	if ( $popup_iframe ) {
		$btn_html .= '<a class="' . $btn_class . ' porto-popup-iframe" href="' . esc_url( $popup_iframe ) . '">' . esc_html( $btn_label ) . '</a>';
	}
} else {
	if ( $popup_block ) {
		$id        = 'popup' . rand();
		$btn_html .= '<a class="' . $btn_class . ' porto-popup-content" href="#' . esc_attr( $id ) . '" data-animation="' . esc_attr( $popup_animation ) . '">' . $btn_label . '</a>';
		$btn_html .= '<div id="' . $id . '" class="dialog dialog-' . esc_attr( $popup_size ) . ' zoom-anim-dialog mfp-hide">' . do_shortcode( '[porto_block name="' . $popup_block . '"]' ) . '</div>';
	}
}

if ( $btn_html ) {
	if ( 'bottom' === $btn_pos ) {
		$el_class .= ' plan-btn-bottom';
	} else {
		$el_class .= ' plan-btn-top';
	}
}

global $porto_price_boxes_count_md, $porto_price_boxes_count_sm;

if ( false === $porto_price_boxes_count_md ) {
	$porto_price_boxes_count_md = 4;
}

if ( false === $porto_price_boxes_count_sm ) {
	$porto_price_boxes_count_sm = 2;
}

if ( ! empty( $porto_price_boxes_count_md ) && ! empty( $porto_price_boxes_count_sm ) ) {
	$css_class  = ' col-lg-' . ( 12 / $porto_price_boxes_count_md );
	$css_class .= ' col-md-' . ( 12 / $porto_price_boxes_count_sm );
	$output     = '<div class="' . esc_attr( $css_class ) . '">';
}

$output .= '<div class="porto-price-box plan ' . esc_attr( $el_class ) . '"';
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

if ( $is_popular && $popular_label ) {
	$output .= '<div class="plan-ribbon-wrapper"><div class="plan-ribbon">' . porto_strip_script_tags( $popular_label ) . '</div></div>';
}

if ( $title || $price || $desc ) {
	$output .= '<h3>';
	if ( $title ) {
		$output .= '<strong' . ( isset( $title_attrs_escaped ) ? $title_attrs_escaped : '' ) . '>' . esc_html( $title ) . '</strong>';
	}
	if ( $desc ) {
		if ( ! isset( $desc_attrs_escaped ) ) {
			$desc_attrs_escaped = ' class="desc"';
		}
		$output .= '<em' . $desc_attrs_escaped . '>' . porto_strip_script_tags( $desc ) . '</em>';
	}
	if ( $price ) {
		$output .= '<span class="plan-price">';
		$output .= '<span class="price">';
		if ( $price_unit ) {
			$output .= '<span class="price-unit">' . esc_html( $price_unit ) . '</span>';
		}
		$output .= esc_html( $price );
		$output .= '</span>';
		if ( $price_label ) {
			$output .= '<label class="price-label">' . esc_html( $price_label ) . '</label>';
		}
		$output .= '</span>';
	}
	$output .= '</h3>';
}

if ( $show_btn && 'bottom' !== $btn_pos ) {
	$output .= $btn_html;
}

$output .= function_exists( 'wpb_js_remove_wpautop' ) ? wpb_js_remove_wpautop( $content, true ) : do_shortcode( $content );

if ( $show_btn && 'bottom' === $btn_pos ) {
	$output .= $btn_html;
}

$output .= '</div>';

if ( ! empty( $porto_price_boxes_count_md ) && ! empty( $porto_price_boxes_count_sm ) ) {
	$output .= '</div>';
}

echo porto_filter_output( $output );

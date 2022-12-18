<?php

$wrap_cls = 'tb-content';
if ( ! empty( $atts['el_class'] ) && wp_is_json_request() ) {
	$wrap_cls .= ' ' . trim( $atts['el_class'] );
}
if ( ! empty( $atts['className'] ) ) {
	$wrap_cls .= ' ' . trim( $atts['className'] );
}

echo '<div class="' . esc_attr( apply_filters( 'porto_elements_wrap_css_class', $wrap_cls, $atts, 'porto-tb/porto-content' ) ) . '">';
global $current_screen;
if ( ( $current_object = get_queried_object() ) && $current_object->term_id ) {
	if ( $current_object->description ) {
		echo do_shortcode( $current_object->description );
	}
} else {
	if ( empty( $atts['content_display'] ) || 'content' != $atts['content_display'] ) {
		echo porto_get_excerpt( isset( $atts['excerpt_length'] ) ? (int) $atts['excerpt_length'] : 50, false, false, $current_screen && $current_screen->is_block_editor() ? false : true );
	} else {
		if ( $current_screen && $current_screen->is_block_editor() ) {
			echo do_shortcode( get_the_content() );
		} else {
			the_content();
		}
	}
}
echo '</div>';

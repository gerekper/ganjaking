<?php
$output = '';
extract(
	shortcode_atts(
		array(
			'type'            => '',
			'position'        => '',
			'position1'       => '',
			'icon_cls'        => '',
			'icon_type'       => 'fontawesome',
			'icon_simpleline' => '',
			'icon_porto'      => '',
			'el_class'        => '',
		),
		$atts
	)
);

wp_enqueue_script( 'porto-scroll-progress', PORTO_SHORTCODES_URL . 'assets/js/porto-scroll-progress.min.js', array( 'jquery-core' ), PORTO_SHORTCODES_VERSION, true );

switch ( $icon_type ) {
	case 'simpleline':
		$icon_cls = $icon_simpleline;
		break;
	case 'porto':
		$icon_cls = $icon_porto;
		break;
}

$el_class = porto_shortcode_extract_class( $el_class );

if ( ! empty( $shortcode_class ) ) {
	if ( empty( $el_class ) ) {
		$el_class = $shortcode_class;
	} else {
		$el_class .= ' ' . $shortcode_class;
	}
}

if ( 'circle' == $type ) {
	$cls = 'porto-scroll-progress porto-scroll-progress-circle';
	if ( $position1 ) {
		$cls .= ' pos-' . $position1;
	}
	if ( $el_class ) {
		$cls .= ' ' . trim( $el_class );
	}

	$output         .= '&nbsp;<a class="' . esc_attr( $cls ) . '" href="#" role="button">';
		$output     .= '<i class="' . ( $icon_cls ? esc_attr( $icon_cls ) : 'fas fa-chevron-up' ) . '"></i>';
		$output     .= '<svg  version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 70 70">';
			$output .= '<circle id="progress-indicator" fill="transparent" stroke="#000000" stroke-miterlimit="10" cx="35" cy="35" r="34"/>';
		$output     .= '</svg>';
	$output         .= '</a><style>#topcontrol{display:none}</style>';
} else {
	$cls = 'porto-scroll-progress';
	if ( $position ) {
		$cls .= ' fixed-' . $position;
		if ( 'under-header' == $position ) {
			$cls .= ' fixed-top';
		}
	}
	if ( $el_class ) {
		$cls .= ' ' . trim( $el_class );
	}
	$output .= '&nbsp;<progress class="' . esc_attr( $cls ) . '" max="100">';
	$output .= '</progress>';
}

echo porto_filter_output( $output );

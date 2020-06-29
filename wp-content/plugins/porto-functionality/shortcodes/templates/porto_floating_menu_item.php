<?php
$output = $icon_simpleline = $title = $link = $el_class = '';

extract(
	shortcode_atts(
		array(
			'icon_simpleline' => '',
			'title'           => '',
			'link'            => '',
			'el_class'        => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

$output         .= '<li class="menu-item">';
	$output     .= '<a href="' . esc_url( $link ) . '" class="text-color-dark background-color-primary">';
		$output .= '<i class="' . esc_attr( $icon_simpleline ) . '"></i>';

if ( $title ) {
	$output .= '<span class="font-weight-bold">' . esc_attr( $title ) . '</span>';
}

	$output .= '</a>';
$output     .= '</li>';

echo porto_filter_output( $output );

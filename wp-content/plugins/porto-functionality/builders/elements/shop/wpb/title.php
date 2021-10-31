<?php

extract(
	shortcode_atts(
		array(
			'font_size'   => '',
			'font_weight' => '',
			'text_transform' => '',
			'line_height' => '',
			'ls'          => '',
			'color'       => '',
			'el_class'    => '',
		),
		$atts
	)
);

$inline_style = '';
if ( $font_size ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $font_size ) );
	if ( ! $unit ) {
		$font_size .= 'px';
	}
	$inline_style .= 'font-size:' . esc_html( $font_size ) . ';';
}
if ( ! empty( $font_weight ) ) {
	$inline_style .= 'font-weight:' . esc_html( $font_weight ) . ';';
}
if ( ! empty( $text_transform ) ) {
	$inline_style .= 'text-transform:' . esc_html( $text_transform ) . ';';
}
if ( $line_height ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $line_height ) );
	if ( ! $unit && (int) $line_height > 3 ) {
		$line_height .= 'px';
	}
	$inline_style .= 'line-height:' . esc_attr( $line_height ) . ';';
}
if ( $ls ) {
	$unit = trim( preg_replace( '/[0-9.-]/', '', $ls ) );
	if ( ! $unit ) {
		$ls .= 'px';
	}
	$inline_style .= 'letter-spacing:' . esc_html( $ls ) . ';';
}
if ( ! empty( $color ) ) {
	$inline_style .= 'color:' . esc_html( $color );
}
if ( $inline_style ) {
	$inline_style = ' style="' . $inline_style . '"';
}
echo '<h2 class="entry-title' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '"' . $inline_style . '>';
echo porto_page_title();
echo '</h2>';

<?php

if ( empty( $atts ) ) {
	return;
}

echo '#header .share-links a {';
if ( ! empty( $atts['icon_size'] ) ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $atts['icon_size'] ) );
	if ( ! $unit ) {
		$atts['icon_size'] .= 'px';
	}
	echo 'font-size:' . esc_html( $atts['icon_size'] ) . ';';
}
if ( ! empty( $atts['icon_border_style'] ) ) {
	echo 'border-style:' . esc_html( $atts['icon_border_style'] ) . ';';
}
if ( ! empty( $atts['icon_color_border'] ) ) {
	echo 'border-color:' . esc_html( $atts['icon_color_border'] ) . ';';
}
if ( ! empty( $atts['icon_border_size'] ) ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $atts['icon_border_size'] ) );
	if ( ! $unit ) {
		$atts['icon_border_size'] .= 'px';
	}
	echo 'border-width:' . esc_html( $atts['icon_border_size'] ) . ';';
}
if ( ! empty( $atts['icon_border_radius'] ) ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $atts['icon_border_radius'] ) );
	if ( ! $unit ) {
		$atts['icon_border_radius'] .= 'px';
	}
	echo 'border-radius:' . esc_html( $atts['icon_border_radius'] ) . ';';
}
if ( ! empty( $atts['icon_border_spacing'] ) ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $atts['icon_border_spacing'] ) );
	if ( ! $unit ) {
		$atts['icon_border_spacing'] .= 'px';
	}
	echo 'width:' . esc_html( $atts['icon_border_spacing'] ) . ';height:' . esc_html( $atts['icon_border_spacing'] ) . ';';
}
if ( ! empty( $atts['spacing'] ) ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $atts['spacing'] ) );
	if ( ! $unit ) {
		$atts['spacing'] = (float) $atts['spacing'] / 2 . 'px';
	} else {
		$atts['spacing'] = (float) str_replace( $unit, '', $atts['spacing'] ) / 2 . $unit;
	}
	echo 'margin-left:' . esc_html( $atts['spacing'] ) . ';margin-right:' . esc_html( $atts['spacing'] ) . ';';
}
if ( ! empty( $atts['box_shadow'] ) ) {
	$data = porto_get_box_shadow( $atts['box_shadow'], 'css' );
	if ( strpos( $data, 'none' ) !== false || strpos( $data, ':;' ) !== false ) {
		echo 'box-shadow: none;';
	} else {
		echo esc_html( $data );
	}
}
echo '}';

if ( ! empty( $atts['icon_color'] ) || ! empty( $atts['icon_color_bg'] ) ) {
	echo '#header .share-links a:not(:hover){';
	if ( ! empty( $atts['icon_color'] ) ) {
		echo 'color:' . esc_html( $atts['icon_color'] ) . ';';
	}
	if ( ! empty( $atts['icon_color_bg'] ) ) {
		echo 'background-color:' . esc_html( $atts['icon_color_bg'] );
	}
	echo '}';
}

if ( ! empty( $atts['icon_hover_color'] ) || ! empty( $atts['icon_hover_color_bg'] ) ) {
	echo '#header .share-links a:hover {';
	if ( ! empty( $atts['icon_hover_color'] ) ) {
		echo 'color:' . esc_html( $atts['icon_hover_color'] ) . ';';
	}
	if ( ! empty( $atts['icon_hover_color_bg'] ) ) {
		echo 'background-color:' . esc_html( $atts['icon_hover_color_bg'] ) . ';';
	}
	echo '}';
}

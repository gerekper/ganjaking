<?php

if ( empty( $atts ) ) {
	return;
}

if ( ! empty( $atts['icon_size'] ) ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $atts['icon_size'] ) );
	if ( ! $unit ) {
		$atts['icon_size'] .= 'px';
	}
	echo '#mini-cart .minicart-icon{font-size:' . esc_html( $atts['icon_size'] ) . '}';
}
if ( ! empty( $atts['icon_color'] ) ) {
	echo '#mini-cart .cart-subtotal, #mini-cart .minicart-icon{color:' . esc_html( $atts['icon_color'] ) . '}';
}
if ( ! empty( $atts['icon_mr'] ) || ! empty( $atts['icon_ml'] ) ) {
	echo '#mini-cart .cart-icon{';
	if ( ! empty( $atts['icon_mr'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $atts['icon_mr'] ) );
		if ( ! $unit ) {
			$atts['icon_mr'] .= 'px';
		}
		echo 'margin-right:' . esc_html( $atts['icon_mr'] ) . ';';
	}
	if ( ! empty( $atts['icon_ml'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $atts['icon_ml'] ) );
		if ( ! $unit ) {
			$atts['icon_ml'] .= 'px';
		}
		echo 'margin-left:' . esc_html( $atts['icon_ml'] );
	}
	echo '}';
}

if ( isset( $atts['type'] ) && ( 'minicart-inline' == $atts['type'] || 'minicart-text' == $atts['type'] ) ) {
	$text_style_escaped = '';
	if ( ! empty( $atts['text_font_size'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $atts['text_font_size'] ) );
		if ( ! $unit ) {
			$atts['text_font_size'] .= 'px';
		}
		$text_style_escaped .= 'font-size:' . esc_html( $atts['text_font_size'] ) . ';';
	}
	if ( ! empty( $atts['text_font_weight'] ) ) {
		$text_style_escaped .= 'font-weight:' . esc_html( $atts['text_font_weight'] ) . ';';
	}
	if ( ! empty( $atts['text_transform'] ) ) {
		$text_style_escaped .= 'text-transform:' . esc_html( $atts['text_transform'] ) . ';';
	}
	if ( ! empty( $atts['text_line_height'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $atts['text_line_height'] ) );
		if ( ! $unit && (int) $atts['text_line_height'] > 3 ) {
			$atts['text_line_height'] .= 'px';
		}
		$text_style_escaped .= 'line-height:' . esc_attr( $atts['text_line_height'] ) . ';';
	}
	if ( isset( $atts['text_ls'] ) && '' != $atts['text_ls'] ) {
		$unit = trim( preg_replace( '/[0-9.-]/', '', $atts['text_ls'] ) );
		if ( ! $unit ) {
			$atts['text_ls'] .= 'px';
		}
		$text_style_escaped .= 'letter-spacing:' . esc_html( $atts['text_ls'] ) . ';';
	}
	if ( ! empty( $atts['text_color'] ) ) {
		$text_style_escaped .= 'color:' . esc_html( $atts['text_color'] );
	}
	if ( $text_style_escaped ) {
		echo '#mini-cart .cart-subtotal {' . $text_style_escaped . '}';
	}

	$price_style_escaped = '';
	if ( ! empty( $atts['price_font_size'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $atts['price_font_size'] ) );
		if ( ! $unit ) {
			$atts['price_font_size'] .= 'px';
		}
		$price_style_escaped .= 'font-size:' . esc_html( $atts['price_font_size'] ) . ';';
	}
	if ( ! empty( $atts['price_font_weight'] ) ) {
		$price_style_escaped .= 'font-weight:' . esc_html( $atts['price_font_weight'] ) . ';';
	}
	if ( ! empty( $atts['price_line_height'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $atts['price_line_height'] ) );
		if ( ! $unit && (int) $atts['price_line_height'] > 3 ) {
			$atts['price_line_height'] .= 'px';
		}
		$price_style_escaped .= 'line-height:' . esc_attr( $atts['price_line_height'] ) . ';';
	}
	if ( isset( $atts['price_ls'] ) && '' != $atts['price_ls'] ) {
		$unit = trim( preg_replace( '/[0-9.-]/', '', $atts['price_ls'] ) );
		if ( ! $unit ) {
			$atts['price_ls'] .= 'px';
		}
		$price_style_escaped .= 'letter-spacing:' . esc_html( $atts['price_ls'] ) . ';';
	}
	if ( ! empty( $atts['price_color'] ) ) {
		$price_style_escaped .= 'color:' . esc_html( $atts['price_color'] );
	}
	if ( $price_style_escaped ) {
		echo '#mini-cart .cart-price {' . $price_style_escaped . '}';
	}
}

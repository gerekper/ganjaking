<?php

if ( empty( $atts ) || empty( $atts['location'] ) ) {
	return;
}

if ( 'nav-top' == $atts['location'] ) {
	if ( ! empty( $atts['font_size'] ) || ! empty( $atts['font_weight'] ) || ! empty( $atts['text_transform'] ) || ! empty( $atts['line_height'] ) || ! empty( $atts['letter_spacing'] ) || ! empty( $atts['padding'] ) || ! empty( $atts['color'] ) ) {
		echo '#header .top-links > li.menu-item > a {';
		if ( ! empty( $atts['font_size'] ) ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $atts['font_size'] ) );
			if ( ! $unit ) {
				$atts['font_size'] .= 'px';
			}
			echo 'font-size:' . esc_html( $atts['font_size'] ) . ';';
		}
		if ( ! empty( $atts['font_weight'] ) ) {
			echo 'font-weight:' . esc_html( $atts['font_weight'] ) . ';';
		}
		if ( ! empty( $atts['text_transform'] ) ) {
			echo 'text-transform:' . esc_html( $atts['text_transform'] ) . ';';
		}
		if ( ! empty( $atts['line_height'] ) ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $atts['line_height'] ) );
			if ( ! $unit && (int) $atts['line_height'] > 3 ) {
				$atts['line_height'] .= 'px';
			}
			echo 'line-height:' . esc_attr( $atts['line_height'] ) . ';';
		}
		if ( ! empty( $atts['letter_spacing'] ) ) {
			$unit = trim( preg_replace( '/[0-9.-]/', '', $atts['letter_spacing'] ) );
			if ( ! $unit ) {
				$atts['letter_spacing'] .= 'px';
			}
			echo 'letter-spacing:' . esc_html( $atts['letter_spacing'] ) . ';';
		}
		if ( ! empty( $atts['padding'] ) ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $atts['padding'] ) );
			if ( ! $unit ) {
				$atts['padding'] .= 'px';
			}
			echo 'padding-left:' . esc_html( $atts['padding'] ) . ';';
			echo 'padding-right:' . esc_html( $atts['padding'] ) . ';';
		}
		if ( ! empty( $atts['color'] ) ) {
			echo 'color:' . esc_html( $atts['color'] );
		}
		echo '}';
	}

	if ( ! empty( $atts['hover_color'] ) ) {
		echo '#header .top-links > li.menu-item:hover > a {color:' . esc_html( $atts['hover_color'] ) . '}';
	}
} elseif ( 'main-toggle-menu' == $atts['location'] ) {
	if ( ! empty( $atts['font_size'] ) || ! empty( $atts['font_weight'] ) || ! empty( $atts['text_transform'] ) || ! empty( $atts['line_height'] ) || ! empty( $atts['letter_spacing'] ) || ! empty( $atts['padding'] ) || ! empty( $atts['color'] ) || ! empty( $atts['bgcolor'] ) ) {
		echo '#main-toggle-menu .menu-title {';
		if ( ! empty( $atts['font_size'] ) ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $atts['font_size'] ) );
			if ( ! $unit ) {
				$atts['font_size'] .= 'px';
			}
			echo 'font-size:' . esc_html( $atts['font_size'] ) . ';';
		}
		if ( ! empty( $atts['font_weight'] ) ) {
			echo 'font-weight:' . esc_html( $atts['font_weight'] ) . ';';
		}
		if ( ! empty( $atts['text_transform'] ) ) {
			echo 'text-transform:' . esc_html( $atts['text_transform'] ) . ';';
		}
		if ( ! empty( $atts['line_height'] ) ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $atts['line_height'] ) );
			if ( ! $unit && (int) $atts['line_height'] > 3 ) {
				$atts['line_height'] .= 'px';
			}
			echo 'line-height:' . esc_attr( $atts['line_height'] ) . ';';
		}
		if ( ! empty( $atts['letter_spacing'] ) ) {
			$unit = trim( preg_replace( '/[0-9.-]/', '', $atts['letter_spacing'] ) );
			if ( ! $unit ) {
				$atts['letter_spacing'] .= 'px';
			}
			echo 'letter-spacing:' . esc_html( $atts['letter_spacing'] ) . ';';
		}
		if ( ! empty( $atts['padding'] ) || 0 === (int) $atts['padding'] ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $atts['padding'] ) );
			if ( ! $unit ) {
				$atts['padding'] .= 'px';
			}
			echo 'padding-left:' . esc_html( $atts['padding'] ) . ';';
			echo 'padding-right:' . esc_html( $atts['padding'] ) . ';';
		}
		if ( ! empty( $atts['color'] ) ) {
			echo 'color:' . esc_html( $atts['color'] ) . ';';
		}
		if ( ! empty( $atts['bgcolor'] ) ) {
			echo 'background-color:' . esc_html( $atts['bgcolor'] );
		}
		echo '}';
	}

	if ( ! empty( $atts['popup_width'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $atts['popup_width'] ) );
		if ( ! $unit ) {
			$atts['popup_width'] .= 'px';
		}
		echo '#main-toggle-menu .toggle-menu-wrap {width:' . esc_html( $atts['popup_width'] ) . '}';
	}

	if ( ! empty( $atts['hover_color'] ) || ! empty( $atts['hover_bgcolor'] ) ) {
		echo '#main-toggle-menu .menu-title:hover{';
		if ( ! empty( $atts['hover_color'] ) ) {
			echo 'color:' . esc_html( $atts['hover_color'] ) . ';';
		}
		if ( ! empty( $atts['hover_bgcolor'] ) ) {
			echo 'background-color:' . esc_html( $atts['hover_bgcolor'] ) . ';';
		}
		echo '}';
	}
}

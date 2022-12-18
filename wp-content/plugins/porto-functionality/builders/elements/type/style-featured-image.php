<?php

if ( ! empty( $atts['hover_bgcolor'] ) || ! empty( $atts['hover_padding'] ) || ! empty( $atts['hover_halign'] ) || ! empty( $atts['hover_valign'] ) ) {
	echo porto_filter_output( $atts['selector'] ) . ' .tb-hover-content{';
	if ( ! empty( $atts['hover_halign'] ) ) {
		echo 'align-items:' . sanitize_text_field( $atts['hover_halign'] ) . ';';
	}
	if ( ! empty( $atts['hover_valign'] ) ) {
		echo 'justify-content:' . sanitize_text_field( $atts['hover_valign'] ) . ';';
	}
	if ( ! empty( $atts['hover_bgcolor'] ) ) {
		echo 'background-color:' . sanitize_text_field( $atts['hover_bgcolor'] ) . ';';
	}
	if ( ! empty( $atts['hover_padding'] ) ) {
		if ( ! empty( $atts['hover_padding']['top'] ) && ! empty( $atts['hover_padding']['right'] ) && ! empty( $atts['hover_padding']['bottom'] ) && ! empty( $atts['hover_padding']['left'] ) ) {
			echo 'padding:' . sanitize_text_field( $atts['hover_padding']['top'] . ' ' . $atts['hover_padding']['right'] . ' ' . $atts['hover_padding']['bottom'] . ' ' . $atts['hover_padding']['left'] ) . ';';
		} else {
			if ( ! empty( $atts['hover_padding']['top'] ) ) {
				echo 'padding-top:' . sanitize_text_field( $atts['hover_padding']['top'] ) . ';';
			}
			if ( ! empty( $atts['hover_padding']['right'] ) ) {
				echo 'padding-right:' . sanitize_text_field( $atts['hover_padding']['right'] ) . ';';
			}
			if ( ! empty( $atts['hover_padding']['bottom'] ) ) {
				echo 'padding-bottom:' . sanitize_text_field( $atts['hover_padding']['bottom'] ) . ';';
			}
			if ( ! empty( $atts['hover_padding']['left'] ) ) {
				echo 'padding-left:' . sanitize_text_field( $atts['hover_padding']['left'] ) . ';';
			}
		}
	}
	echo '}';
}

if ( ! empty( $atts['zoom_size'] ) || ! empty( $atts['zoom_fs'] ) || ! empty( $atts['zoom_bgc'] ) || ! empty( $atts['zoom_clr'] ) || ! empty( $atts['zoom_bs'] ) || ! empty( $atts['zoom_bw'] ) || ! empty( $atts['zoom_bc'] ) ) {
	echo porto_filter_output( $atts['selector'] ) . ' .zoom{';
	if ( ! empty( $atts['zoom_size'] ) ) {
		echo 'width:' . esc_html( $atts['zoom_size'] ) . ';height:' . esc_html( $atts['zoom_size'] ) . ';';
		if ( empty( $atts['zoom_bs'] ) || empty( $atts['zoom_bw'] ) ) {
			echo 'line-height:' . esc_html( $atts['zoom_size'] ) . ';';
		}
	}
	if ( ! empty( $atts['zoom_fs'] ) ) {
		echo 'font-size:' . esc_html( $atts['zoom_fs'] ) . ';';
	}
	if ( ! empty( $atts['zoom_bgc'] ) ) {
		echo 'background-color:' . esc_html( $atts['zoom_bgc'] ) . ';';
	}
	if ( ! empty( $atts['zoom_clr'] ) ) {
		echo 'color:' . esc_html( $atts['zoom_clr'] ) . ';';
	}
	if ( ! empty( $atts['zoom_bs'] ) ) {
		echo 'border-style:' . esc_html( $atts['zoom_bs'] ) . ';';
		if ( ! empty( $atts['zoom_bw'] ) ) {
			echo 'border-width:' . esc_html( $atts['zoom_bw'] ) . 'px;';
			echo 'line-height:calc(' . ( empty( $atts['zoom_size'] ) ? '30' : esc_html( $atts['zoom_size'] ) ) . ' - ' . ( (int) $atts['zoom_bw'] * 2 ) . 'px);';
		}
		if ( ! empty( $atts['zoom_bc'] ) ) {
			echo 'border-color:' . esc_html( $atts['zoom_bc'] ) . ';';
		}
	}
	echo '}';
}

if ( ! empty( $atts['zoom_bgc_hover'] ) || ! empty( $atts['zoom_clr_hover'] ) || ! empty( $atts['zoom_bc_hover'] ) ) {
	echo porto_filter_output( $atts['selector'] ) . ' .zoom:hover{';
	if ( ! empty( $atts['zoom_bgc_hover'] ) ) {
		echo 'background-color:' . esc_html( $atts['zoom_bgc_hover'] ) . ';';
	}
	if ( ! empty( $atts['zoom_clr_hover'] ) ) {
		echo 'color:' . esc_html( $atts['zoom_clr_hover'] ) . ';';
	}
	if ( ! empty( $atts['zoom_bs'] ) && ! empty( $atts['zoom_bc_hover'] ) ) {
		echo 'border-color:' . esc_html( $atts['zoom_bc_hover'] ) . ';';
	}
	echo '}';
}

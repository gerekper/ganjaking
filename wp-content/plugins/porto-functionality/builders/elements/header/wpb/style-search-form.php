<?php

if ( empty( $atts ) ) {
	return;
}

if ( ! empty( $atts['popup_pos'] ) ) {
	global $porto_settings;
	$popup_style = '';
	if ( 'left' == $atts['popup_pos'] ) {
		$popup_style .= 'left: auto; right: -1.5rem';
	} elseif ( 'center' == $atts['popup_pos'] ) {
		$popup_style .= 'left: 50%; right: auto; transform: translateX(-50%)';
	} elseif ( 'right' == $atts['popup_pos'] ) {
		$popup_style .= 'left: -1.5rem; right: auto';
	}
	if ( 'simple' == $porto_settings['search-layout'] || 'large' == $porto_settings['search-layout'] ) {
		echo '#header .search-popup .searchform {';
		echo porto_filter_output( $popup_style );
		echo '}';
	} elseif ( 'advanced' == $porto_settings['search-layout'] ) {
		echo '@media (max-width: 991px) {';
		echo '#header .searchform {';
		echo porto_filter_output( $popup_style );
		echo '}';
		echo '}';
	}
}

if ( isset( $atts['page_builder'] ) && 'gutenberg' == $atts['page_builder'] ) {
	if ( ! empty( $atts['toggle_size'] ) || ! empty( $atts['toggle_color'] ) ) {
		echo '#header .searchform button, #header .searchform-popup .search-toggle {';
		if ( ! empty( $atts['toggle_size'] ) ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $atts['toggle_size'] ) );
			if ( ! $unit ) {
				$atts['toggle_size'] .= 'px';
			}
			echo 'font-size:' . esc_html( $atts['toggle_size'] ) . ';';
		}
		if ( ! empty( $atts['toggle_color'] ) ) {
			echo 'color:' . esc_html( $atts['toggle_color'] );
		}
		echo '}';
	}

	if ( ! empty( $atts['input_size'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $atts['input_size'] ) );
		if ( ! $unit ) {
			$atts['input_size'] .= 'px';
		}
		echo '#header .searchform input, #header .searchform.searchform-cats input{width:' . esc_html( $atts['input_size'] ) . '}';
	}

	if ( ! empty( $atts['height'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $atts['height'] ) );
		if ( ! $unit ) {
			$atts['height'] .= 'px';
		}
		echo '#header .searchform input, #header .searchform select, #header .searchform .selectric .label, #header .searchform button{height:' . esc_html( $atts['height'] ) . '; line-height:' . esc_html( $atts['height'] ) . '}';
	}

	if ( ! empty( $atts['border_width'] ) || ! empty( $atts['border_color'] ) ) {
		echo '#header .searchform {';
		if ( ! empty( $atts['border_width'] ) ) {
			$unit = trim( preg_replace( '/[0-9.]/', '', $atts['border_width'] ) );
			if ( ! $unit ) {
				$atts['border_width'] .= 'px';
			}
			echo 'border-width:' . esc_html( $atts['border_width'] ) . ';';
		}
		if ( ! empty( $atts['border_color'] ) ) {
			echo 'border-color:' . esc_html( $atts['border_color'] );
		}
		echo '}';
		if ( ! empty( $atts['border_color'] ) ) {
			echo '#header .searchform-popup .search-toggle:after{border-bottom-color:' . esc_html( $atts['border_color'] ) . '}';
		}
	}

	if ( isset( $atts['border_radius'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $atts['border_radius'] ) );
		if ( ! $unit ) {
			$atts['border_radius'] .= 'px';
		}
		$border_radius_selectors = '#header .searchform { border-radius: %s }';
		if ( is_rtl() ) {
			$border_radius_selectors .= '#header .searchform input { border-radius: 0 %s %s 0 }';
			$border_radius_selectors .= '#header .searchform button { border-radius: %s 0 0 %s }';
		} else {
			$border_radius_selectors .= '#header .searchform input { border-radius: %s 0 0 %s }';
			$border_radius_selectors .= '#header .searchform button { border-radius: 0 %s %s 0 }';
		}
		echo esc_html( str_replace( '%s', $atts['border_radius'], $border_radius_selectors ) );
	}

	if ( ! empty( $atts['divider_color'] ) ) {
		echo '#header .searchform input, #header .searchform select, #header .searchform .selectric, #header .searchform .selectric-hover .selectric, #header .searchform .selectric-open .selectric, #header .searchform .autocomplete-suggestions, #header .searchform .selectric-items{border-color:' . esc_html( $atts['divider_color'] ) . '}';
	}
}

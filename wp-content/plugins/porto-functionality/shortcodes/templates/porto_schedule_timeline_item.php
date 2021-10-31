<?php

$output = $subtitle = $image_url = $image_id = $heading = $shadow = $heading_color = $subtitle_color = $animation_type = $animation_duration = $animation_delay = $el_class = '';

extract(
	shortcode_atts(
		array(
			'item_type'          => 'schedule',
			'subtitle'           => '',
			'icon_type'          => 'custom',
			'icon'               => '',
			'icon_simpleline'    => '',
			'icon_porto'         => '',
			'image_url'          => '',
			'image_id'           => '',
			'heading'            => '',
			'shadow'             => '',
			'heading_color'      => '',
			'subtitle_color'     => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

switch ( $icon_type ) {
	case 'custom':
		if ( ! $image_url && $image_id ) {
			$image_url = wp_get_attachment_url( $image_id );
		}

		$image_url = str_replace( array( 'http:', 'https:' ), '', $image_url );
		break;
	case 'simpleline':
		$icon = $icon_simpleline;
		break;
	case 'porto':
		$icon = $icon_porto;
		break;
}

global $porto_schedule_timeline_count, $porto_schedule_step_count;

if ( 'history' == $item_type || isset( $porto_schedule_timeline_count ) ) {
	$porto_schedule_timeline_count++;

	if ( $subtitle ) {
		$output .= '<div class="timeline-date"><h3' . ( $subtitle_color ? ' style="color:' . esc_attr( $subtitle_color ) . '"' : '' ) . ' class="time-text step-item-subtitle font-weight-bold font-size-sm">' . esc_html( $subtitle ) . '</h3></div>';
	}
	$attrs = '';
	if ( $animation_type ) {
		$attrs .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
		if ( $animation_delay ) {
			$attrs .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
		}
		if ( $animation_duration && 1000 != $animation_duration ) {
			$attrs .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
		}
	}

	if ( 1 == $porto_schedule_timeline_count % 2 ) {
		$position_class = ' left';
	} else {
		$position_class = ' right';
	}
	$output         .= '<article class="timeline-box' . $position_class . '"' . $attrs . '>';
		$output     .= '<div>';
		if ( 'custom' == $icon_type && $image_url ) {
			$output .= '<img src="' . esc_url( $image_url ) . '" class="img-responsive" alt="' . esc_attr( $heading ) . '">';
		} elseif ( 'custom' != $icon_type && $icon ) {
			$output .= '<i class="' . esc_attr( $icon ) . '"></i>';
		}
			$output .= '<h4' . ( $heading_color ? ' style="color:' . esc_attr( $heading_color ) . ' !important"' : '' ) . ' class="timeline-item-title step-item-title">' . esc_html( $heading ) . '</h4>';
			$output .= '<div class="timeline-item-content">' . do_shortcode( $content ) . '</div>';
		$output     .= '</div>';
	$output         .= '</article>';
} elseif ( 'step' == $item_type || isset( $porto_schedule_step_count ) ) {
	$porto_schedule_step_count++;

	$attrs = '';
	if ( $animation_type ) {
		$attrs .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
		if ( $animation_delay ) {
			$attrs .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
		}
		if ( $animation_duration && 1000 != $animation_duration ) {
			$attrs .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
		}
	}

	$output .= '<div class="process-step' . ( $el_class ? ' ' . esc_attr( trim( $el_class ) ) : '' ) . '"' . $attrs . '>';
	$output .= '<div class="process-step-circle">';
	$output .= '<strong class="process-step-circle-content">';
	if ( 'custom' == $icon_type && $image_url ) {
		$output .= '<img src="' . esc_url( $image_url ) . '" class="img-responsive img-circle" alt="' . esc_attr( $heading ) . '">';
	} elseif ( 'custom' != $icon_type && $icon ) {
		$output .= '<i class="' . esc_attr( $icon ) . '"></i>';
	} elseif ( $subtitle ) {
		$output .= esc_html( $subtitle );
	} else {
		$output .= ( (int) $porto_schedule_step_count );
	}
	$output .= '</strong>';
	$output .= '</div>';
	$output .= '<div class="process-step-content">';
	$output .= '<h4 class="step-item-title"' . ( $heading_color ? ' style="color:' . esc_attr( $heading_color ) . '"' : '' ) . '>' . esc_html( $heading ) . '</h4>';
	$output .= '<div class="process-step-desc">' . do_shortcode( $content ) . '</div>';
	$output .= '</div>';
	$output .= '</div>';
} else {
	$output         .= '<div class="timeline-balloon p-b-lg m-b-sm ' . esc_attr( $el_class ) . '">';
		$output     .= '<div class="balloon-cell balloon-time">';
			$output .= '<span' . ( $subtitle_color ? ' style="color:' . esc_attr( $subtitle_color ) . ' !important"' : '' ) . ' class="time-text step-item-subtitle text-color-dark font-weight-bold font-size-sm">' . esc_html( $subtitle ) . '</span>';
			$output .= '<div class="time-dot background-color-light"></div>';
		$output     .= '</div>';
		$output     .= '<div class="balloon-cell"';
	if ( $animation_type ) {
		$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
		if ( $animation_delay ) {
			$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
		}
		if ( $animation_duration && 1000 != $animation_duration ) {
			$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
		}
	}
		$output     .= '>';
			$output .= '<div class="balloon-content ';
	if ( $shadow ) {
		$output .= ' balloon-shadow ';
	}
			$output     .= 'background-color-light">';
				$output .= '<span class="balloon-arrow background-color-light"></span>';
	if ( 'custom' == $icon_type && $image_url ) {
		$output     .= '<div class="balloon-photo">';
			$output .= '<img src="' . esc_url( $image_url ) . '" class="img-responsive img-circle" alt="' . esc_attr( $heading ) . '">';
		$output     .= '</div>';
	} elseif ( 'custom' != $icon_type && $icon ) {
		$output .= '<div class="balloon-photo"><i class="' . esc_attr( $icon ) . '"></i></div>';
	}
				$output .= '<div class="balloon-description">';
	if ( $heading ) {
		$output .= '<h5' . ( $heading_color ? ' style="color:' . esc_attr( $heading_color ) . ' !important"' : '' ) . ' class="step-item-title text-color-dark font-weight-bold p-t-xs m-none">' . esc_html( $heading ) . '</h5>';
	}

	if ( $content ) {
		$output .= '<p class="font-weight-normal m-t-sm m-b-xs">' . do_shortcode( $content ) . '</p>';
	}
				$output .= '</div>';
			$output     .= '</div>';
		$output         .= '</div>';
	$output             .= '</div>';
}

echo porto_filter_output( $output );

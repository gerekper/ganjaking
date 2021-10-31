<?php

$output = $subtitle = $image_url = $image_id = $heading = $color = $heading_color = $company_color = $animation_type = $animation_duration = $animation_delay = $el_class = '';

extract(
	shortcode_atts(
		array(
			'from'          => '',
			'to'            => '',
			'duration'      => '',
			'company'       => '',
			'location'      => '',
			'heading'       => '',
			'color'         => '',
			'heading_color' => '',
			'company_color' => '',
			'el_class'      => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

$output             .= '<article class="timeline-box right ' . esc_attr( $el_class ) . '">';
	$output         .= '<div class="experience-info col-lg-3 col-sm-5 match-height background-color-primary">';
		$output     .= '<span class="from text-color-dark text-uppercase"' . ( $color ? ' style="color:' . esc_attr( $color ) . ' !important"' : '' ) . '>';
			$output .= __( 'From', 'porto-functionality' ) . '<span class="font-weight-semibold">' . porto_strip_script_tags( $from ) . '</span>';
		$output     .= '</span>';
		$output     .= '<span class="to text-color-dark text-uppercase"' . ( $color ? ' style="color:' . esc_attr( $color ) . ' !important"' : '' ) . '>';
			$output .= __( 'To', 'porto-functionality' ) . '<span class="font-weight-semibold">' . porto_strip_script_tags( $to ) . '</span>';
		$output     .= '</span>';
		$output     .= '<p' . ( $color ? ' style="color:' . esc_attr( $color ) . ' !important"' : '' ) . ' class="text-color-dark">' . porto_strip_script_tags( $duration ) . '</p>';
		$output     .= '<span' . ( $company_color ? ' style="color:' . esc_attr( $company_color ) . ' !important"' : '' ) . ' class="company text-color-dark font-weight-semibold">';
			$output .= $company;
			$output .= '<span' . ( $color ? ' style="color:' . esc_attr( $color ) . ' !important"' : '' ) . ' class="company-location text-color-dark font-weight-normal text-uppercase">' . porto_strip_script_tags( $location ) . '</span>';
		$output     .= '</span>';
	$output         .= '</div>';
	$output         .= '<div class="experience-description col-lg-9 col-sm-7 match-height background-color-light">';
		$output     .= '<h4' . ( $heading_color ? ' style="color:' . esc_attr( $heading_color ) . ' !important"' : '' ) . ' class="text-color-dark font-weight-semibold">' . porto_strip_script_tags( $heading ) . '</h4>';
		$output     .= '<p class="custom-text-color-2">' . porto_strip_script_tags( $content ) . '</p>';
	$output         .= '</div>';
$output             .= '</article>';

echo porto_filter_output( $output );

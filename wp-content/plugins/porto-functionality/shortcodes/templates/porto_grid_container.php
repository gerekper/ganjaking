<?php
if ( ! empty( $atts['className'] ) ) {
	$atts['el_class'] = $atts['className'];
}

$output = $grid_size = $gutter_size = $max_width = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'layout'             => '',
			'grid_layout'        => '1',
			'grid_height'        => 600,
			'grid_size'          => '0',
			'gutter_size'        => '2%',
			'max_width'          => '767px',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

wp_enqueue_script( 'isotope' );

if ( ! $gutter_size ) {
	$gutter_size = '0%';
}
$valid_characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
$rand_escaped     = '';
$length           = 32;
for ( $n = 1; $n < $length; $n++ ) {
	$whichcharacter = rand( 0, strlen( $valid_characters ) - 1 );
	$rand_escaped  .= substr( $valid_characters, $whichcharacter, 1 );
}

$el_class = porto_shortcode_extract_class( $el_class );

$output = '<div class="porto-grid-container"';
if ( $animation_type ) {
	$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
$output .= '>';

$iso_options               = array();
$iso_options['layoutMode'] = 'masonry';
$iso_options['masonry']    = array( 'columnWidth' => '.grid-col-sizer' );
if ( ! ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
	$iso_options['itemSelector'] = '.porto-grid-item';
} else {
	$iso_options['itemSelector'] = '.vc_porto_grid_item';
}

$extra_attrs = '';
$grid_sizer  = '';
if ( 'preset' == $layout ) {
	global $porto_grid_layout, $porto_item_count;
	$porto_grid_layout  = porto_creative_grid_layout( $grid_layout );
	$grid_height_number = trim( preg_replace( '/[^0-9]/', '', $grid_height ) );
	$unit               = trim( str_replace( $grid_height_number, '', $grid_height ) );
	$porto_item_count   = 0;

	$el_class .= ( $el_class ? ' ' : '' ) . 'porto-preset-layout';

	if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
		$extra_attrs = array();
		foreach ( $porto_grid_layout as $pl ) {
			$extra_attrs[] = 'grid-col-' . $pl['width'] . ' grid-col-md-' . $pl['width_md'] . ( isset( $pl['width_lg'] ) ? ' grid-col-lg-' . $pl['width_lg'] : '' ) . ( isset( $pl['height'] ) ? ' grid-height-' . $pl['height'] : '' );
		}
		$extra_attrs = ' data-item-grid="' . esc_attr( implode( ',', $extra_attrs ) ) . '"';
	}
} else {
	preg_match_all( '/\[porto_grid_item\s[^]]*width="([^]"]*)"[^]]*\]/', $content, $matches );
	if ( isset( $matches[1] ) && is_array( $matches[1] ) ) {
		$fractions    = array();
		$denominators = array();
		$numerators   = array();
		$unit         = '';
		foreach ( $matches[1] as $index => $item ) {
			if ( ! $unit ) {
				$unit = preg_replace( '/[.0-9]/', '', $item );
			}
			$w = (float) $item;
			if ( ! $w ) {
				continue;
			}
			if ( (float) ( (int) $w ) === $w ) { // integer
				$arr = array( $w, 1 );
			} else {
				for ( $index = 2; $index <= 100; $index++ ) {
					$r_w = round( $w * $index, 1 );
					if ( (float)( (int) $r_w ) === $r_w ) { //integer
						$gcd = porto_gcd( $r_w, $index );
						$arr = array( $r_w / $gcd, $index / $gcd );
					}
				}
				if ( ! isset( $arr ) ) {
					$w   = floor( $w * 10 );
					$gcd = porto_gcd( $w, 10 );
					$arr = array( $w / $gcd, 10 / $gcd );
				}
			}
			if ( isset( $arr ) && ! in_array( $arr, $fractions ) ) {
				$fractions[]    = $arr;
				$numerators[]   = $arr[0];
				$denominators[] = $arr[1];
			}
		}
		if ( count( $fractions ) > 1 ) {
			$deno_lcm = porto_lcm( $denominators );
			$num_gcd  = porto_gcd( $numerators );
			$unit_num = round( $num_gcd / $deno_lcm, 4 );
			if ( $unit_num >= 0.1 ) {
				$unit_num  .= esc_attr( $unit );
				$grid_sizer = ' style="width:' . $unit_num . '; flex: 0 0 ' . $unit_num . '"';
			}
		}
	}
}
$iso_options['animationEngine'] = 'best-available';
$iso_options['resizable']       = false;

$output .= '<div id="grid-' . $rand_escaped . '" class="' . esc_attr( $el_class ) . ' wpb_content_element clearfix" data-plugin-masonry data-plugin-options=\'' . json_encode( $iso_options ) . '\'' . $extra_attrs . '>';
$output .= do_shortcode( $content );
$output .= '<div class="grid-col-sizer"' . $grid_sizer . '></div>';
if ( 'preset' == $layout ) {
	unset( $GLOBALS['porto_grid_layout'], $GLOBALS['porto_item_count'] );
}
$output .= '</div>';

$gutter_size_number  = preg_replace( '/[^.0-9]/', '', $gutter_size );
$gutter_size         = str_replace( $gutter_size_number, (float) ( $gutter_size_number / 2 ), $gutter_size );
$gutter_size_escaped = esc_html( $gutter_size );

$output .= '<style scope="scope">';
$output .= '#grid-' . $rand_escaped . ' .porto-grid-item { padding: ' . $gutter_size_escaped . '; }';
$output .= '#grid-' . $rand_escaped . ' { margin: -' . $gutter_size_escaped . ' -' . $gutter_size_escaped . ' ' . $gutter_size_escaped . '; }';
if ( 'preset' == $layout ) {
	ob_start();
	porto_creative_grid_style( $porto_grid_layout, $grid_height_number, 'grid-' . $rand_escaped, false, false, $unit, $iso_options['itemSelector'] );
	$output .= ob_get_clean();
} elseif ( ! empty( $max_width ) ) {
	$output .= '@media (max-width:' . esc_html( $max_width ) . ') {';
	$output .= '#grid-' . $rand_escaped . ' { height: auto !important }';
	$output .= '#grid-' . $rand_escaped . ' .porto-grid-item:first-child { margin-top: 0 }';
	$output .= '#grid-' . $rand_escaped . ' .porto-grid-item { width: 100% !important; position: static !important; float: none }';
	$output .= '}';
}
if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
	$output .= '.porto-grid-container .porto-grid-item { float: none; } .porto-grid-container .vc_porto_grid_item { float: left; }';
	$output .= '.porto-grid-container .porto-grid-item .wpb_single_image { margin-bottom: 0; }';
}
$output .= '</style>';

$output .= '</div>';

echo porto_filter_output( $output );

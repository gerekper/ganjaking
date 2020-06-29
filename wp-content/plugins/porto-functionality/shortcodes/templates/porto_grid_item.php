<?php
$output = $width = $el_class = '';
extract(
	shortcode_atts(
		array(
			'width'        => '',
			'el_class'     => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

global $porto_grid_layout, $porto_item_count;
if ( ! empty( $porto_grid_layout ) ) {
	$grid_layout = $porto_grid_layout[ $porto_item_count % count( $porto_grid_layout ) ];
	$el_class   .= ( $el_class ? ' ' : '' ) . 'grid-col-' . $grid_layout['width'] . ' grid-col-md-' . $grid_layout['width_md'] . ( isset( $grid_layout['width_lg'] ) ? ' grid-col-lg-' . $grid_layout['width_lg'] : '' ) . ( isset( $grid_layout['height'] ) ? ' grid-height-' . $grid_layout['height'] : '' );
	$porto_item_count++;
}

if ( false !== strpos( $width, '.' ) ) {
	$unit  = preg_replace( '/[.0-9]/', '', $width );
	$width = preg_replace( '/[^.0-9]/', '', $width );
	$width = floor( $width * 10 ) / 10 . $unit;
}
$output  = '<div class="porto-grid-item' . ( $el_class ? ' ' . $el_class : '' ) . ( empty( $porto_grid_layout ) && $width ? '" style="width:' . esc_attr( $width ) : '' ) . '">';
$output .= do_shortcode( $content );
$output .= '</div>';

echo porto_filter_output( $output );

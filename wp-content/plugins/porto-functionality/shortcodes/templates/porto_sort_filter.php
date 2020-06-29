<?php
$output = $label = $sort_by = $filter_by = $active = $el_class = '';
extract(
	shortcode_atts(
		array(
			'label'     => '',
			'sort_by'   => 'popular',
			'filter_by' => '',
			'active'    => false,
			'el_class'  => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

if ( $label ) {
	$output = '<li data-sort-by="' . esc_attr( $sort_by ) . '" data-filter-by="' . esc_attr( $filter_by ? $filter_by : '*' ) . '"' .
		( ( 'yes' == $active ) ? ' data-active="true" class="active"' : '' ) .
		( $el_class ? ' class="' . esc_attr( $el_class ) . '"' : '' ) . '><a href="#">' . esc_html( $label ) . '</a></li>';
}

echo porto_filter_output( $output );

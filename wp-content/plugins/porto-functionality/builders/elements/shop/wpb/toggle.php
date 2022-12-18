<?php
extract( // @codingStandardsIgnoreLine
	shortcode_atts(
		array(
			'icon_grid_type'       => '',
			'icon_grid'            => '',
			'icon_grid_porto'      => '',
			'icon_grid_simpleline' => '',
			'icon_list_type'       => '',
			'icon_list'            => '',
			'icon_list_porto'      => '',
			'icon_list_simpleline' => '',
			'el_class'             => '',
		),
		$atts
	)
);

switch ( $icon_grid_type ) {
	case 'simpleline':
		if ( $icon_grid_simpleline ) {
			$icon_grid = $icon_grid_simpleline;
		}
		break;
	case 'porto':
		if ( $icon_grid_porto ) {
			$icon_grid = $icon_grid_porto;
		}
		break;
}

switch ( $icon_list_type ) {
	case 'simpleline':
		if ( $icon_list_simpleline ) {
			$icon_list = $icon_list_simpleline;
		}
		break;
	case 'porto':
		if ( $icon_list_porto ) {
			$icon_list = $icon_list_porto;
		}
		break;
}

if ( ! empty( $shortcode_class ) ) {
	$el_class = trim( $shortcode_class . ' ' . $el_class );
}

porto_grid_list_toggle( $el_class, $icon_grid, $icon_list );

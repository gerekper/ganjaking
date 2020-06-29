<?php

$output   = '';
$title    = '';
$nav_menu = '';
$el_class = '';

extract(
	shortcode_atts(
		array(
			'title'    => '',
			'nav_menu' => '',
			'el_class' => '',
		),
		$atts
	)
);

if ( ! class_exists( 'porto_sidebar_navwalker' ) ) {
	return;
}

global $porto_settings;

$output .= '<div class="widget_sidebar_menu main-sidebar-menu' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '">';
if ( $title ) {
	$output .= '<div class="widget-title">';

		$output .= esc_html( $title );
	if ( $porto_settings['menu-sidebar-toggle'] ) {
		$output .= '<div class="toggle"></div>';
	}
	$output .= '</div>';
}
	$output .= '<div class="sidebar-menu-wrap">';

	ob_start();
if ( $nav_menu ) {
	$args = array(
		'container'   => '',
		'menu_class'  => 'sidebar-menu',
		'before'      => '',
		'after'       => '',
		'link_before' => '',
		'link_after'  => '',
		'fallback_cb' => false,
		'walker'      => new porto_sidebar_navwalker,
		'menu'        => $nav_menu,
	);
	wp_nav_menu( $args );
}

	$output .= str_replace( '&nbsp;', '', ob_get_clean() );

	$output .= '</div>';

$output .= '</div>';

echo porto_filter_output( $output );

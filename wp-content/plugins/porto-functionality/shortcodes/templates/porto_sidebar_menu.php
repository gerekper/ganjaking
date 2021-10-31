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

$nav_menu_html_escaped = '';
if ( $nav_menu ) {
	global $porto_settings_optimize;
	if ( ! empty( $porto_settings_optimize['lazyload_menu'] ) ) {
		$optimize_backup = $porto_settings_optimize['lazyload_menu'];
		$porto_settings_optimize['lazyload_menu'] = '';
	}
	$args                  = array(
		'container'   => '',
		'menu_class'  => 'sidebar-menu',
		'before'      => '',
		'after'       => '',
		'link_before' => '',
		'link_after'  => '',
		'fallback_cb' => false,
		'walker'      => new porto_sidebar_navwalker,
		'menu'        => $nav_menu,
		'echo'        => false,
	);
	$nav_menu_html_escaped = wp_nav_menu( $args );

	if ( isset( $optimize_backup ) ) {
		$porto_settings_optimize['lazyload_menu'] = $optimize_backup;
	}
}
if ( ! $nav_menu_html_escaped ) {
	$nav_menu_html_escaped = esc_html__( 'Please select a valid menu to display.', 'porto-functionality' );
}
$output .= $nav_menu_html_escaped;

	$output .= '</div>';

$output .= '</div>';

echo porto_filter_output( $output );

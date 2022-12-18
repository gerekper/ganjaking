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

if ( ! empty( $shortcode_class ) ) {
	$el_class .= ' ' . $shortcode_class;
}

global $porto_settings, $porto_settings_optimize;

$output .= '<div class="widget_sidebar_menu main-sidebar-menu' . ( $el_class ? ' ' . esc_attr( trim( $el_class ) ) : '' ) . '"' . ( $nav_menu && ! empty( $porto_settings_optimize['lazyload_menu'] ) ? ' data-menu="' . esc_attr( $nav_menu ) . '"' : '' ) . '>';
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
		'echo'        => false,
	);
	if ( ! empty( $porto_settings_optimize['lazyload_menu'] ) ) {
		//$optimize_backup = $porto_settings_optimize['lazyload_menu'];
		//$porto_settings_optimize['lazyload_menu'] = '';
		$args['depth'] = 2;

		add_filter( 'porto_lazymenu_depth', '__return_true' );
	}

	$nav_menu_html_escaped = wp_nav_menu( $args );

	/*if ( isset( $optimize_backup ) ) {
		$porto_settings_optimize['lazyload_menu'] = $optimize_backup;
	}*/

	if ( ! empty( $porto_settings_optimize['lazyload_menu'] ) ) {
		remove_filter( 'porto_lazymenu_depth', '__return_true' );
	}
}
if ( ! $nav_menu_html_escaped ) {
	$nav_menu_html_escaped = esc_html__( 'Please select a valid menu to display.', 'porto-functionality' );
}
$output .= $nav_menu_html_escaped;

	$output .= '</div>';

$output .= '</div>';

echo porto_filter_output( $output );

<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( 'vc_edit_form' === vc_post_param( 'action' ) && vc_verify_admin_nonce() ) {
	$link_category = array( esc_html__( 'All Links', 'js_composer' ) => '' );
	$link_cats = get_terms( 'link_category' );
	if ( is_array( $link_cats ) && ! empty( $link_cats ) ) {
		foreach ( $link_cats as $link_cat ) {
			if ( is_object( $link_cat ) && isset( $link_cat->name, $link_cat->term_id ) ) {
				$link_category[ $link_cat->name ] = $link_cat->term_id;
			}
		}
	}
} else {
	$link_category = array();
}

return array(
	'name' => 'WP ' . esc_html__( 'Links' ),
	'base' => 'vc_wp_links',
	'icon' => 'icon-wpb-wp',
	'category' => esc_html__( 'WordPress Widgets', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'content_element' => (bool) get_option( 'link_manager_enabled' ),
	'weight' => - 50,
	'description' => esc_html__( 'Your blogroll', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Link Category', 'js_composer' ),
			'param_name' => 'category',
			'value' => $link_category,
			'admin_label' => true,
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => array(
				esc_html__( 'Link title', 'js_composer' ) => 'name',
				esc_html__( 'Link rating', 'js_composer' ) => 'rating',
				esc_html__( 'Link ID', 'js_composer' ) => 'id',
				esc_html__( 'Random', 'js_composer' ) => 'rand',
			),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Options', 'js_composer' ),
			'param_name' => 'options',
			'value' => array(
				esc_html__( 'Show Link Image', 'js_composer' ) => 'images',
				esc_html__( 'Show Link Name', 'js_composer' ) => 'name',
				esc_html__( 'Show Link Description', 'js_composer' ) => 'description',
				esc_html__( 'Show Link Rating', 'js_composer' ) => 'rating',
			),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Number of links to show', 'js_composer' ),
			'param_name' => 'limit',
			'value' => - 1,
		),
		array(
			'type' => 'el_id',
			'heading' => esc_html__( 'Element ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %1$sw3c specification%2$s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
	),
);

<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$tag_taxonomies = array();
if ( 'vc_edit_form' === vc_post_param( 'action' ) && vc_verify_admin_nonce() ) {
	$taxonomies = get_taxonomies();
	if ( is_array( $taxonomies ) && ! empty( $taxonomies ) ) {
		foreach ( $taxonomies as $taxonomy ) {
			$tax = get_taxonomy( $taxonomy );
			if ( ( is_object( $tax ) && ( ! $tax->show_tagcloud || empty( $tax->labels->name ) ) ) || ! is_object( $tax ) ) {
				continue;
			}
			$tag_taxonomies[ $tax->labels->name ] = esc_attr( $taxonomy );
		}
	}
}
return array(
	'name' => 'WP ' . esc_html__( 'Tag Cloud' ),
	'base' => 'vc_wp_tagcloud',
	'icon' => 'icon-wpb-wp',
	'category' => esc_html__( 'WordPress Widgets', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => esc_html__( 'Your most used tags in cloud format', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Widget title', 'js_composer' ),
			'param_name' => 'title',
			'value' => esc_html__( 'Tags', 'js_composer' ),
			'description' => esc_html__( 'What text use as a widget title. Leave blank to use default widget title.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Taxonomy', 'js_composer' ),
			'param_name' => 'taxonomy',
			'value' => $tag_taxonomies,
			'description' => esc_html__( 'Select source for tag cloud.', 'js_composer' ),
			'admin_label' => true,
			'save_always' => true,
		),
		array(
			'type' => 'el_id',
			'heading' => esc_html__( 'Element ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %sw3c specification%s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
	),
);

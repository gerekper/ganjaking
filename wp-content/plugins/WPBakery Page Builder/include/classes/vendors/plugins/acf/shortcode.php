<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$groups = function_exists( 'acf_get_field_groups' ) ? acf_get_field_groups() : apply_filters( 'acf/get_field_groups', array() );
$groups_param_values = $fields_params = array();
foreach ( (array) $groups as $group ) {
	$id = isset( $group['id'] ) ? 'id' : ( isset( $group['ID'] ) ? 'ID' : 'id' );
	$groups_param_values[ $group['title'] ] = $group[ $id ];
	$fields = function_exists( 'acf_get_fields' ) ? acf_get_fields( $group[ $id ] ) : apply_filters( 'acf/field_group/get_fields', array(), $group[ $id ] );
	$fields_param_value = array();
	foreach ( (array) $fields as $field ) {
		$fields_param_value[ $field['label'] ] = (string) $field['key'];
	}
	$fields_params[] = array(
		'type' => 'dropdown',
		'heading' => esc_html__( 'Field name', 'js_composer' ),
		'param_name' => 'field_from_' . $group[ $id ],
		'value' => $fields_param_value,
		'save_always' => true,
		'description' => esc_html__( 'Choose field from group.', 'js_composer' ),
		'dependency' => array(
			'element' => 'field_group',
			'value' => array( (string) $group[ $id ] ),
		),
	);
}

return array(
	'name' => esc_html__( 'Advanced Custom Field', 'js_composer' ),
	'base' => 'vc_acf',
	'icon' => 'vc_icon-acf',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Advanced Custom Field', 'js_composer' ),
	'params' => array_merge( array(
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Field group', 'js_composer' ),
			'param_name' => 'field_group',
			'value' => $groups_param_values,
			'save_always' => true,
			'description' => esc_html__( 'Select field group.', 'js_composer' ),
		),
	), $fields_params, array(
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Show label', 'js_composer' ),
			'param_name' => 'show_label',
			'value' => array( esc_html__( 'Yes', 'js_composer' ) => 'yes' ),
			'description' => esc_html__( 'Enter label to display before key value.', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Align', 'js_composer' ),
			'param_name' => 'align',
			'value' => array(
				esc_attr__( 'left', 'js_composer' ) => 'left',
				esc_attr__( 'right', 'js_composer' ) => 'right',
				esc_attr__( 'center', 'js_composer' ) => 'center',
				esc_attr__( 'justify', 'js_composer' ) => 'justify',
			),
			'description' => esc_html__( 'Select alignment.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
	) ),
);

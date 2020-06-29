<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
vc_include_template( 'pages/partials/vc-roles-parts/_part.tpl.php', array(
	'part' => $part,
	'role' => $role,
	'params_prefix' => 'vc_roles[' . $role . '][' . $part . ']',
	'controller' => vc_role_access()->who( $role )->part( $part ),
	'options' => array(
		array( true, esc_html__( 'All', 'js_composer' ) ),
		array( 'add', esc_html__( 'Apply templates only', 'js_composer' ) ),
		array( false, esc_html__( 'Disabled', 'js_composer' ) ),
	),
	'main_label' => esc_html__( 'Templates', 'js_composer' ),
	'description' => esc_html__( 'Control access rights to templates and predefined templates. Note: "Apply templates only" restricts users from saving new templates and deleting existing.', 'js_composer' ),
) );

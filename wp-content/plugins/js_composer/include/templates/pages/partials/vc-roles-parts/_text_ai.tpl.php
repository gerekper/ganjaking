<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/** @var string $part */
vc_include_template( 'pages/partials/vc-roles-parts/_part.tpl.php', array(
	'part' => $part,
	'role' => $role,
	'params_prefix' => 'vc_roles[' . $role . '][' . $part . ']',
	'controller' => vc_role_access()->who( $role )->part( $part ),
	'options' => array(
		array( true, esc_html__( 'Enabled', 'js_composer' ) ),
		array( false, esc_html__( 'Disabled', 'js_composer' ) ),
	),
	'main_label' => esc_html__( 'Text AI access', 'js_composer' ),
	'description' => esc_html__( 'Control access to WPBakery Page Builder AI assistant for text fields', 'js_composer' ),
) );

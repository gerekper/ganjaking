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
	'capabilities' => array(
		array(
			'disabled_ce_editor',
			esc_html__( 'Disable Classic editor', 'js_composer' ),
		),
	),
	'options' => array(
		array(
			true,
			esc_html__( 'Enabled', 'js_composer' ),
		),
		array(
			'default',
			esc_html__( 'Enabled and default', 'js_composer' ),
		),
		array(
			false,
			esc_html__( 'Disabled', 'js_composer' ),
		),
	),
	'main_label' => esc_html__( 'Backend editor', 'js_composer' ),
	'custom_label' => esc_html__( 'Backend editor', 'js_composer' ),
) );

<?php
/*----------------------------------------------------------------------------*\
	PARAMS
\*----------------------------------------------------------------------------*/

$mpc_params = array(
	'mpc_align',
	'mpc_animation',
	'mpc_colorpicker',
	'mpc_content',
	'mpc_css',
	'mpc_datetime',
	'mpc_divider',
	'mpc_gradient',
	'mpc_icon',
	'mpc_layout_select',
	'mpc_list',
	'mpc_preset',
	'mpc_shadow',
	'mpc_slider',
	'mpc_split',
	'mpc_text',
	'mpc_typography',
);

foreach( $mpc_params as $param ) {
	require_once( mpc_get_plugin_path( __FILE__, 'dir' ) . '/params/' . $param . '/' . $param . '.php' );
}

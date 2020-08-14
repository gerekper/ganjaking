<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


$settings = array(
	'tools' => array(
		'tools-action'  => array(
			'type' => 'custom_tab',
			'action' => 'ywson_tools_tab'
		)
	)
);

return $settings;
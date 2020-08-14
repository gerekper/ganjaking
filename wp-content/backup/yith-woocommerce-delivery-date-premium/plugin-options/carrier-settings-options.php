<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings = array(
	'carrier-settings' => array(
		'carrier_tab' => array(
			'type'   => 'custom_tab',
			'action' => 'ywcdd_show_carrier_tab'
		)
	)

);

return $settings;
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings = array(
	'user-log-settings' => array(
		'user_log' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_account_funds_user_log'
		)
	)

);

return $settings;

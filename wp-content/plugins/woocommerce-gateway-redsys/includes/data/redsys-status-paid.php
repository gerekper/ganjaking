<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function redsys_return_status_paid() {
	
	$status = array();
	$status = array(
		'pending',
		'redsys-pbankt',
	);
	return $status;
}

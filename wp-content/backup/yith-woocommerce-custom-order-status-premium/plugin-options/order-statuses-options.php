<?php
// Exit if accessed directly
! defined( 'YITH_WCCOS' ) && exit();

return array(
	'order-statuses' => array(
		'order-statuses_list' => array(
			'type'      => 'post_type',
			'post_type' => 'yith-wccos-ostatus',
		),
	),
);
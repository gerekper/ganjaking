<?php
/**
 * Order statuses.
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.

return array(
	'order-statuses' => array(
		'order-statuses_list' => array(
			'type'      => 'post_type',
			'post_type' => 'yith-wccos-ostatus',
		),
	),
);

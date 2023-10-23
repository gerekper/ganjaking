<?php
/**
 * Bulk operations tab
 *
 * @package YITH\MinimumMaximumQuantity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	'bulk' => array(
		'ywmmq_bulk_operations' => array(
			'type'   => 'custom_tab',
			'action' => 'ywmmq_bulk_operations',
		),
	),
);

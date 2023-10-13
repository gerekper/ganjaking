<?php
/**
 * Resources List options
 *
 * @package YITH\Booking\Modules\Resources
 */

defined( 'YITH_WCBK' ) || exit();

return array(
	'configuration-resources' => array(
		'configuration-resources-list' => array(
			'type'          => 'post_type',
			'post_type'     => YITH_WCBK_Post_Types::RESOURCE,
			'wp-list-style' => 'boxed',
		),
	),
);


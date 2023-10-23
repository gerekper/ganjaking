<?php
/**
 * Badge Post Type Tab in
 *
 * @package YITH\BadgeManagement\Compatibility\MultiVendor
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

$badges = array(
	'badges' => array(
		'type'          => 'post_type',
		'post_type'     => YITH_WCBM_Post_Types::$badge,
		'wp-list-style' => 'classic',
	),
);

return compact( 'badges' );

<?php
/**
 * Badge Post Type Tab
 *
 * @package YITH\BadgeManagement\PluginOptions
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

$badges = array(
	'badges' => array(
		'type'          => 'post_type',
		'post_type'     => YITH_WCBM_Post_Types::$badge,
		'wp-list-style' => 'classic',
	),
);

return apply_filters( 'yith_wcbm_badges_tab_options', compact( 'badges' ) );

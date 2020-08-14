<?php
/**
 * Stats settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

return apply_filters(
	'yith_wcaf_stats_settings',
	array(
		'stats' => array(
			'stat_panel' => array(
				'type' => 'custom_tab',
				'action' => 'yith_wcaf_stat_panel',
				'hide_sidebar' => true
			)
		)
	)
);
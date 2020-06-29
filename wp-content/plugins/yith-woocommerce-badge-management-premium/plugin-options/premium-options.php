<?php
/**
 * Premium Tab
 *
 * @package YITH WooCommerce Badge Management
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

return array(
	'premium' => array(
		'landing' => array(
			'type'         => 'custom_tab',
			'action'       => 'yith_wcbm_premium_tab',
			'hide_sidebar' => true,
		),
	),
);

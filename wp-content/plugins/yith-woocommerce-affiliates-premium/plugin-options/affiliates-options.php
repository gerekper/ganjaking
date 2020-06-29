<?php
/**
 * Affiliates report page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

return apply_filters(
	'yith_wcaf_affiliates_settings',
	array(
		'affiliates' => array(
			'affiliate_panel' => array(
				'type' => 'custom_tab',
				'action' => 'yith_wcaf_affiliate_panel',
				'hide_sidebar' => true
			)
		)
	)
);
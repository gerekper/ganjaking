<?php
/**
 * General settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

return apply_filters(
	'yith_wcaf_commissions_settings',
	array(
		'commissions' => array(

			'commission_panel' => array(
				'type' => 'custom_tab',
				'action' => 'yith_wcaf_commission_panel',
				'hide_sidebar' => true
			)
		)
	)
);
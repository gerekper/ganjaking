<?php
/**
 * Pricing rules settings
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

$pricing = array(
	'pricing' => array(
		'pricing_list' => array(
			'type'      => 'post_type',
			'post_type' => 'ywdpd_discount',
		),
	),
);

return apply_filters( 'ywdpd_panel_price_rules_tab', $pricing );

<?php
/**
 * Integration settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

return apply_filters( 'yith_wcac_integration_options', array(
	'integration' => array(
		'active-campaign-options' => array(
			'title' => __( 'Active Campaign Options', 'yith-woocommerce-active-campaign' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith_wcac_active_campaign_options'
		),

		'active-campaign-api-url' => array(
			'title'   => __( 'Active Campaign API URL', 'yith-woocommerce-active-campaign' ),
			'type'    => 'text',
			'id'      => 'yith_wcac_active_campaign_api_url',
			'desc'    => __( 'Active Campaign API URL; you can get one at <b>//&lt;your_company&gt;.activehosted.com/admin/main.php?action=settings#tab_api</b>', 'yith-woocommerce-active-campaign' ),
			'default' => '',
			'css'     => 'min-width:300px;'
		),

		'active-campaign-api-key' => array(
			'title'   => __( 'Active Campaign API Key', 'yith-woocommerce-active-campaign' ),
			'type'    => 'text',
			'id'      => 'yith_wcac_active_campaign_api_key',
			'desc'    => __( 'Active Campaign API key; you can get one at <b>//&lt;your_company&gt;.activehosted.com/admin/main.php?action=settings#tab_api</b>', 'yith-woocommerce-active-campaign' ),
			'default' => '',
			'css'     => 'min-width:300px;'
		),

		'active-campaign-status' => array(
			'title' => __( 'Integration status', 'yith-woocommerce-active-campaign' ),
			'type'  => 'yith_wcac_integration_status',
			'id'    => 'yith_wcac_integration_status'
		),

		'active-campaign-options-end' => array(
			'type' => 'sectionend',
			'id'   => 'yith_wcac_active_campaign_options'
		),
	)
) );
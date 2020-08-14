<?php
/**
 * GENERAL ARRAY OPTIONS
 */
if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

$general = array(

	'endpoints' => array(

		array(
			'title' => __( 'Endpoint Options', 'yith-woocommerce-customize-myaccount-page' ),
			'type'  => 'title',
			'desc'  => '',
			'id'    => 'yith-wcmap-endpoints-options',
		),

		array(
			'name'    => __( 'Manage Endpoints', 'yith-woocommerce-customize-myaccount-page' ),
			'desc'    => '',
			'id'      => 'yith_wcmap_endpoint',
			'default' => '',
			'type'    => 'wcmap_endpoints',
		),

		array(
			'type' => 'sectionend',
			'id'   => 'yith-wcmap-end-endpoints-options',
		),
	),
);

return apply_filters( 'yith_wcmap_panel_endpoints_options', $general );
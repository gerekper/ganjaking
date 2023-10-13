<?php
/**
 * General settings page
 *
 * @author YITH
 * @package YITH\Wishlist\Options
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

$yith_wfbt_installed = ( defined( 'YITH_WFBT' ) && YITH_WFBT );

/**
 * APPLY_FILTERS: yith_wcwl_settings_options
 *
 * Filter the options available in the 'General settings' tab.
 *
 * @param array $options Array of options
 *
 * @return array
 */

return apply_filters(
	'yith_wcwl_settings_options',
	array(
		'settings-general' => array(
			/**
			 * General settings
			 */
			'general_section_start' => array(
				'name' => __( 'General Settings', 'yith-woocommerce-wishlist' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'yith_wcwl_general_settings',
			),
			'enable_ajax_loading'   => array(
				'name'      => __( 'Enable AJAX loading', 'yith-woocommerce-wishlist' ),
				'desc'      => __( 'Load any cacheable wishlist item via AJAX', 'yith-woocommerce-wishlist' ),
				'id'        => 'yith_wcwl_ajax_enable',
				'default'   => 'no',
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
			),

			'general_section_end'   => array(
				'type' => 'sectionend',
				'id'   => 'yith_wcwl_general_settings',
			),
		),
		$yith_wfbt_installed ? array(
			'yith_wfbt_start'              => array(
				'name' => __( 'YITH WooCommerce Frequently Bought Together Integration', 'yith-woocommerce-wishlist' ),
				'type' => 'title',
				'id'   => 'yith_wcwl_yith_wfbt',
			),

			'yith_wfbt_enable_integration' => array(
				'name'      => __( 'Enable slider in wishlist', 'yith-woocommerce-wishlist' ),
				'desc'      => __( 'Enable the slider with linked products on the Wishlist page.', 'yith-woocommerce-wishlist' ),
				'id'        => 'yith_wfbt_enable_integration',
				'default'   => 'yes',
				'type'      => 'yith-field',
				'yith-type' => 'onoff',
			),

			'yith_wfbt_end'                => array(
				'type' => 'sectionend',
				'id'   => 'yith_wcwl_yith_wfbt',
			),
		) : array(),
	)
);

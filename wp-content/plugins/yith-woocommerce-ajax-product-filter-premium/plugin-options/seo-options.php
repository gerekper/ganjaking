<?php
/**
 * SEO options
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Options
 * @version 4.0.0
 */

return apply_filters(
	'yith_wcan_panel_seo_options',
	array(
		'seo' => array_merge(
			array(
				'seo_section_start' => array(
					'name' => _x( 'SEO settings', '[ADMIN] Seo settings page', 'yith-woocommerce-ajax-navigation' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'yith_wcan_seo_settings',
				),

				'enable_seo'        => array(
					'name'      => _x( 'Enable SEO option', '[ADMIN] Seo settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Add "robots" meta tag in head tag of HTML page if filters have been activated.', '[ADMIN] Seo settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_enable_seo',
					'type'      => 'yith-field',
					'yith-type' => 'onoff',
				),

				'meta_tag'          => array(
					'name'      => _x( 'Meta tag', '[ADMIN] Seo settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Select which tag to use on filtered pages', '[ADMIN] Seo settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_seo_value',
					'type'      => 'yith-field',
					'yith-type' => 'select',
					'class'     => 'wc-enhanced-select',
					'options'   => array(
						'disabled'         => _x( 'Disabled', '[ADMIN] Seo settings page', 'yith-woocommerce-ajax-navigation' ),
						'noindex-nofollow' => 'noindex, nofollow',
						'noindex-follow'   => 'noindex, follow',
						'index-nofollow'   => 'index, nofollow',
						'index-follow'     => 'index, follow',
					),
					'deps'      => array(
						'ids'    => 'yith_wcan_enable_seo',
						'values' => 'yes',
					),
				),

				'nofollow'          => array(
					'name'      => _x( 'Add "nofollow" to filter anchors', '[ADMIN] Seo settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'When enabled, adds re="nofollow" attribute to all the filter anchors across the plugin', '[ADMIN] Seo settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_seo_rel_nofollow',
					'type'      => 'yith-field',
					'yith-type' => 'onoff',
					'deps'      => array(
						'ids'    => 'yith_wcan_enable_seo',
						'values' => 'yes',
					),
				),
			),
			'yes' === get_option( 'yith_wcan_ajax_filters', 'yes' ) ? array(
				'change_url' => array(
					'name'      => _x( 'URL permalinks', '[ADMIN] Seo settings page', 'yith-woocommerce-ajax-navigation' ),
					'desc'      => _x( 'Choose how to manage browser URL during filtering', '[ADMIN] Seo settings page', 'yith-woocommerce-ajax-navigation' ),
					'id'        => 'yith_wcan_change_browser_url',
					'type'      => 'yith-field',
					'yith-type' => 'radio',
					'default'   => 'yes',
					'options'   => array(
						'yes' => _x( 'Add filters parameters to default URL', '[ADMIN] Seo settings page', 'yith-woocommerce-ajax-navigation' ),
						'no'  => _x( 'Don\'t change URL', '[ADMIN] Seo settings page', 'yith-woocommerce-ajax-navigation' ),
					),
				),
			) : array(),
			array(
				'seo_section_end' => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcan_seo_settings',
				),
			)
		),
	)
);

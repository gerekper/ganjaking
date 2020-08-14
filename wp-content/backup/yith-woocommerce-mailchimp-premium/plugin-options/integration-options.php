<?php
/**
 * Integration settings page
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

return apply_filters( 'yith_wcmc_integration_options', array(
	'integration' => array(
		'mailchimp-video-box' =>  array(
			'name'    => __( 'Upgrade to the PREMIUM VERSION', 'yith-woocommerce-mailchimp' ),
			'type'    => 'videobox',
			'default' => array(
				'plugin_name'               => __( 'YITH WooCommerce Mailchimp', 'yith-woocommerce-mailchimp' ),
				'title_first_column'        => __( 'Discover the Advanced Features', 'yith-woocommerce-mailchimp' ),
				'description_first_column'  => __( 'Upgrade to the PREMIUM VERSION of YITH WOOCOMMERCE MAILCHIMP to benefit from all features!', 'yith-woocommerce-mailchimp' ),
				'video'                     => array(
					'video_id'          => '125238913',
					'video_image_url'   => YITH_WCMC_URL . '/assets/images/video-thumb.jpg',
					'video_description' => '',
				),
				'title_second_column'       => __( 'Get Support and Pro Features', 'yit' ),
				'description_second_column' => __( 'By purchasing the premium version of the plugin, you will take advantage of the advanced features of the product and you will get one year of free updates and support through our platform available 24h/24.', 'yith-woocommerce-mailchimp' ),
				'button'                    => array(
					'href'  => function_exists( 'YITH_WCMC_Admin_Premium' ) ? YITH_WCMC_Admin_Premium()->get_premium_landing_uri() : YITH_WCMC_Admin()->get_premium_landing_uri(),
					'title' => 'Get Support and Pro Features'
				)
			),
			'id'      => 'yith_wcmc_general_videobox'
		),

		'mailchimp-options' => array(
			'title' => __( 'MailChimp Options', 'yith-woocommerce-mailchimp' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith_wcmc_mailchimp_options'
		),

		'mailchimp-api-key' => array(
			'title' => __( 'MailChimp API Key', 'yith-woocommerce-mailchimp' ),
			'type' => 'text',
			'id' => 'yith_wcmc_mailchimp_api_key',
			'desc' => __( 'API key used to access MailChimp account; you can get one <a href="//admin.mailchimp.com/account/api/">here</a>', 'yith-woocommerce-mailchimp' ),
			'default' => '',
			'css'     => 'min-width:300px;'
		),

		'mailchimp-status' => array(
			'title' => __( 'Integration status', 'yith-woocommerce-mailchimp' ),
			'type' => 'yith_wcmc_integration_status',
			'id' => 'yith_wcmc_integration_status'
		),

		'mailchimp-options-end' => array(
			'type'  => 'sectionend',
			'id'    => 'yith_wcmc_mailchimp_options'
		),
	)
) );
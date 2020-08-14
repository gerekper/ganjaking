<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$general_options = array(

	'general' => array(

		'section_general_settings'          => array(
			'name' => esc_html__( 'General settings', 'yith-woocommerce-advanced-reviews' ),
			'type' => 'title',
			'id'   => 'ywar_section_general',
		),
		'review_settings_enable_title'      => array(
			'name'    => esc_html__( 'Show title', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
			'desc'    => esc_html__( 'Add a title field in the reviews.', 'yith-woocommerce-advanced-reviews' ),
			'id'      => 'ywar_enable_review_title',
			'default' => 'yes',
		),
		'review_settings_enable_attachment' => array(
			'name'    => esc_html__( 'Show attachments', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
			'desc'    => esc_html__( 'Add an attachment section in the reviews.', 'yith-woocommerce-advanced-reviews' ),
			'id'      => 'ywar_enable_attachments',
			'default' => 'yes',
		),
		'ywar_attachment_type'              => array(
			'name'    => esc_html__( 'Attachment type', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
			'desc'    => esc_html__( 'Set the file type allowed as the review attachment', 'yith-woocommerce-advanced-reviews' ),
			'id'      => 'ywar_attachment_type',
			'default' => 'jpg,png',
		),

		'review_settings_attachment_limit' => array(
			'name'    => esc_html__( 'Multiple attachment limit', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'number',
			'desc'    => esc_html__( 'Set the maximum number of attachments that can be selected (0 = no limit).', 'yith-woocommerce-advanced-reviews' ),
			'id'      => 'ywar_max_attachments',
            'min'     => 0,
            'step'    => 1,
            'default' => 0
		),
		'ywar_attachment_max_size'         => array(
			'name'             => esc_html__( 'Attachment max size', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'number',
			'desc'             => esc_html__( 'Set the file max size allowed in MB. Set to 0 for no size limit.', 'yith-woocommerce-advanced-reviews' ),
			'id'               => 'ywar_attachment_max_size',
            'min'     => 0,
            'step'    => 1,
            'default' => 0
		),

		'ywar_tab_selector'            => array(
			'name'    => esc_html__( 'Review content selector', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
			'desc'    => esc_html__( 'Set the id or CSS class that matches the review tab content (default: #tab-reviews).', 'yith-woocommerce-advanced-reviews' ),
			'id'      => 'ywar_tab_selector',
			'default' => '#tab-reviews',
		),

		'ywar_enable_recaptcha'             => array (
			'name'    => esc_html__( 'reCaptcha', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
			'desc'    => esc_html__( 'Enable reCaptcha on review submitting', 'yith-woocommerce-advanced-reviews' ),
			'id'      => 'ywar_enable_recaptcha',
			'default' => 'no',
		),
		'ywar_recaptcha_site_key'           => array (
			'name' => esc_html__( 'reCaptcha site key', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
			'desc' => esc_html__( 'Insert your reCaptcha site key', 'yith-woocommerce-advanced-reviews' ),
			'id'   => 'ywar_recaptcha_site_key',
			'css'  => 'min-width:50%;',

		),
		'ywar_recaptcha_secret_key'         => array (
			'name' => esc_html__( 'reCaptcha secret key', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
			'desc' => esc_html__( 'Insert your reCaptcha secret key', 'yith-woocommerce-advanced-reviews' ),
			'id'   => 'ywar_recaptcha_secret_key',
			'css'  => 'min-width:50%;',
		),
        'ywar_recaptcha_message_error'         => array (
            'name' => esc_html__( 'reCaptcha message error', 'yith-woocommerce-advanced-reviews' ),
            'type'    => 'yith-field',
            'yith-type' => 'textarea',
            'desc' => esc_html__( 'Insert your message error when the review is submitted without recaptcha', 'yith-woocommerce-advanced-reviews' ),
            'id'   => 'ywar_recaptcha_message_error',
            'css'  => 'min-width:50%;',
            'default'   =>  esc_html__('You have entered an incorrect reCAPTCHA value. Click the BACK button on your browser and try again.', 'yith-woocommerce-advanced-reviews' ),
        ),
		'review_settings_import'       => array(
			'name'    => esc_html__( 'Previous reviews', 'yith-woocommerce-advanced-reviews' ),
			'type'    => 'ywar_import_previous_reviews',
			'id'      => 'ywar_import_review',
			'default' => 'yes',
		),
		'section_general_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywar_section_general_end',
		),
	),
);

return $general_options;

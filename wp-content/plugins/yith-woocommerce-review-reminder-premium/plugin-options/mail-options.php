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


$settings_start     = array(
	'review_reminder_mail_section_title' => array(
		'name' => esc_html__( 'Email Settings', 'yith-woocommerce-review-reminder' ),
		'type' => 'title',
		'desc' => '',
	)
);
$settings_mail_free = ywrr_mail_options();
$settings_mail_pro  = apply_filters( 'ywrr_premium_options', array() );
$settings_end       = array(
	'review_reminder_mail_test'        => array(
		'name'      => esc_html__( 'Test email', 'yith-woocommerce-review-reminder' ),
		'desc'      => esc_html__( 'Type an email address to send a test email', 'yith-woocommerce-review-reminder' ),
		'type'      => 'yith-field',
		'yith-type' => 'text-button',
		'buttons'   => array(
			array(
				'name'  => esc_html__( 'Send Test Email', 'yith-woocommerce-review-reminder' ),
				'class' => 'ywrr-send-test-email',
			)
		),
		'default'   => get_option( 'admin_email' ),
		'id'        => 'ywrr_email_test',
	),
	'review_reminder_mail_section_end' => array(
		'type' => 'sectionend',
	)
);

return array(
	'mail' => array_merge( $settings_start, $settings_mail_free, $settings_mail_pro, $settings_end ),
);

<?php
/**
 * Settings options tab
 *
 * @package YITH\ReviewReminder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	'settings' => array(
		'review_reminder_request_section_title'  => array(
			'name' => esc_html__( 'Request Settings', 'yith-woocommerce-review-reminder' ),
			'type' => 'title',
			'desc' => '',
			'id'   => 'ywrr_request_settings_title',
		),
		'review_reminder_refuse_requests'        => array(
			'name'      => esc_html__( 'Don\'t email me checkbox', 'yith-woocommerce-review-reminder' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html__( 'When they are in checkout page, users can refuse to receive review requests via email', 'yith-woocommerce-review-reminder' ),
			'id'        => 'ywrr_refuse_requests',
			'default'   => 'no',
		),
		'review_reminder_refuse_label'           => array(
			'name'              => esc_html__( 'Checkbox Label', 'yith-woocommerce-review-reminder' ),
			'type'              => 'yith-field',
			'yith-type'         => 'text',
			'desc'              => esc_html__( 'The label of the checkbox that will be showed in checkout page', 'yith-woocommerce-review-reminder' ),
			'default'           => esc_html__( 'I accept to receive review requests via email', 'yith-woocommerce-review-reminder' ),
			'id'                => 'ywrr_refuse_requests_label',
			'custom_attributes' => 'required',
			'deps'              => array(
				'id'    => 'ywrr_refuse_requests',
				'value' => 'yes',
			),
		),
		'review_reminder_schedule_day'           => array(
			'name'              => esc_html__( 'Days to elapse', 'yith-woocommerce-review-reminder' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'desc'              => esc_html__( 'Type here the number of days that have to pass after the order has been set as "completed" before sending an email for reminding users to review the item(s)purchased. Defaults to 7 <br/> Note: Changing this WILL NOT re-schedule currently scheduled emails. If you would like to reschedule emails to this new date, make sure you check the \'Reschedule emails\' checkboxes below.', 'yith-woocommerce-review-reminder' ),
			'default'           => 7,
			'id'                => 'ywrr_mail_schedule_day',
			'custom_attributes' => 'min="1" required',
		),
		'review_reminder_request_type'           => array(
			'name'      => esc_html__( 'Request a review for', 'yith-woocommerce-review-reminder' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'desc'      => esc_html__( 'Select the products you want to ask for a review', 'yith-woocommerce-review-reminder' ),
			'options'   => array(
				'all'       => esc_html__( 'All products in order', 'yith-woocommerce-review-reminder' ),
				'selection' => esc_html__( 'Specific products', 'yith-woocommerce-review-reminder' ),
			),
			'default'   => 'all',
			'id'        => 'ywrr_request_type',
		),
		'review_reminder_request_number'         => array(
			'name'              => esc_html__( 'Number of products for review request', 'yith-woocommerce-review-reminder' ),
			'type'              => 'yith-field',
			'yith-type'         => 'number',
			'desc'              => esc_html__( 'Set the number of products from the order to include in the review reminder email. Default: 1', 'yith-woocommerce-review-reminder' ),
			'default'           => 1,
			'id'                => 'ywrr_request_number',
			'custom_attributes' => 'min="1" required',
			'deps'              => array(
				'id'    => 'ywrr_request_type',
				'value' => 'selection',
			),
		),
		'review_reminder_request_criteria'       => array(
			'name'      => esc_html__( 'Send review reminder for', 'yith-woocommerce-review-reminder' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'desc'      => '',
			'options'   => array(
				'first'               => esc_html__( 'First products(s) bought', 'yith-woocommerce-review-reminder' ),
				'last'                => esc_html__( 'Last products(s) bought', 'yith-woocommerce-review-reminder' ),
				'highest_quantity'    => esc_html__( 'Products with highest number of items bought', 'yith-woocommerce-review-reminder' ),
				'lowest_quantity'     => esc_html__( 'Products with lowest number of items bought', 'yith-woocommerce-review-reminder' ),
				'most_reviewed'       => esc_html__( 'Products with highest number of reviews', 'yith-woocommerce-review-reminder' ),
				'least_reviewed'      => esc_html__( 'Products with lowest number of reviews', 'yith-woocommerce-review-reminder' ),
				'highest_priced'      => esc_html__( 'Products with highest price', 'yith-woocommerce-review-reminder' ),
				'lowest_priced'       => esc_html__( 'Products with lowest price', 'yith-woocommerce-review-reminder' ),
				'highest_total_value' => esc_html__( 'Products with highest total value', 'yith-woocommerce-review-reminder' ),
				'lowest_total_value'  => esc_html__( 'Products with lowest total value', 'yith-woocommerce-review-reminder' ),
				'random'              => esc_html__( 'Random', 'yith-woocommerce-review-reminder' ),
			),
			'class'     => 'wc-enhanced-select',
			'default'   => 'first',
			'id'        => 'ywrr_request_criteria',
			'deps'      => array(
				'id'    => 'ywrr_request_type',
				'value' => 'selection',
			),
		),
		'review_reminder_reschedule'             => array(
			'name'      => esc_html__( 'Reschedule emails', 'yith-woocommerce-review-reminder' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html__( 'Reschedule all currently scheduled emails to the new date defined above', 'yith-woocommerce-review-reminder' ),
			'id'        => 'ywrr_mail_reschedule',
			'default'   => 'no',
		),
		'review_reminder_send_rescheduled'       => array(
			'name'      => esc_html__( 'Send emails immediately', 'yith-woocommerce-review-reminder' ),
			'type'      => 'yith-field',
			'yith-type' => 'checkbox',
			'desc'      => esc_html__( 'Send emails if rescheduled date has already passed', 'yith-woocommerce-review-reminder' ),
			'id'        => 'ywrr_mail_send_rescheduled',
			'default'   => 'no',
			'deps'      => array(
				'id'    => 'ywrr_mail_reschedule',
				'value' => 'yes',
			),
		),
		'review_reminder_request_section_end'    => array(
			'type' => 'sectionend',
			'id'   => 'ywrr_request_settings_end',
		),
		'review_reminder_advanced_section_title' => array(
			'name' => esc_html__( 'Advanced Tools', 'yith-woocommerce-review-reminder' ),
			'type' => 'title',
		),
		'review_reminder_show_column'            => array(
			'name'      => esc_html__( 'Show in Orders page', 'yith-woocommerce-review-reminder' ),
			'type'      => 'yith-field',
			'yith-type' => 'checkbox',
			'desc'      => esc_html__( 'Show Review Reminder Column in Orders page', 'yith-woocommerce-review-reminder' ),
			'id'        => 'ywrr_schedule_order_column',
			'default'   => 'yes',
		),
		'review_reminder_mass_schedule'          => array(
			'id'        => 'ywrr_mass_schedule',
			'type'      => 'yith-field',
			'yith-type' => 'buttons',
			'name'      => esc_html__( 'Bulk Schedule', 'yith-woocommerce-review-reminder' ),
			'buttons'   => array(
				array(
					'name'  => esc_html__( 'Schedule Orders', 'yith-woocommerce-review-reminder' ),
					'class' => 'ywrr-bulk-actions',
					'data'  => array(
						'action' => 'ywrr_mass_schedule',
					),
				),
			),
			'desc'      => esc_html__( 'Use this option to schedule all the orders that have never been scheduled. This option is useful if you use external tool to manage your e-commerce, such as Linnworks, that could bypass some WooCommerce functionalities.', 'yith-woocommerce-review-reminder' ),
		),
		'review_reminder_mass_unschedule'        => array(
			'id'        => 'ywrr_mass_unschedule',
			'type'      => 'yith-field',
			'yith-type' => 'buttons',
			'name'      => esc_html__( 'Bulk Unschedule', 'yith-woocommerce-review-reminder' ),
			'buttons'   => array(
				array(
					'name'  => esc_html__( 'Clear', 'yith-woocommerce-review-reminder' ),
					'class' => 'ywrr-bulk-actions',
					'data'  => array(
						'action' => 'ywrr_mass_unschedule',
					),
				),
			),
			'desc'      => esc_html__( 'Use this option to unschedule all pending emails from Schedule List.', 'yith-woocommerce-review-reminder' ),
		),
		'review_reminder_clear_sent'             => array(
			'id'        => 'ywrr_clear_sent',
			'type'      => 'yith-field',
			'yith-type' => 'buttons',
			'name'      => esc_html__( 'Clear sent emails from Schedule List', 'yith-woocommerce-review-reminder' ),
			'buttons'   => array(
				array(
					'name'  => esc_html__( 'Clear', 'yith-woocommerce-review-reminder' ),
					'class' => 'ywrr-bulk-actions',
					'data'  => array(
						'action' => 'ywrr_clear_sent',
					),
				),
			),
			'desc'      => esc_html__( 'Use this option to clear all sent emails from Schedule List. This option is useful if you want to reduce the weight of your database.', 'yith-woocommerce-review-reminder' ),
		),
		'review_reminder_clear_cancelled'        => array(
			'id'        => 'ywrr_clear_cancelled',
			'type'      => 'yith-field',
			'yith-type' => 'buttons',
			'name'      => esc_html__( 'Clear cancelled emails from Schedule List', 'yith-woocommerce-review-reminder' ),
			'buttons'   => array(
				array(
					'name'  => esc_html__( 'Clear', 'yith-woocommerce-review-reminder' ),
					'class' => 'ywrr-bulk-actions',
					'data'  => array(
						'action' => 'ywrr_clear_cancelled',
					),
				),
			),
			'desc'      => esc_html__( 'Use this option to clear all cancelled emails from Schedule List. This option is useful if you want to reduce the weight of your database.', 'yith-woocommerce-review-reminder' ),
		),
		'review_reminder_advanced_section_end'   => array(
			'type' => 'sectionend',
		),
	),
);

<?php
/**
 * Created by PhpStorm.
 * User: CreateIT
 * Date: 5/14/2018
 * Time: 5:04 PM
 */
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_ct-gdpr-fields',
		'title' => 'ct-gdpr-fields',
		'fields' => array (
			array (
				'key' => 'field_5b0e762dfcf3e',
				'label' => esc_html__( 'Service name', 'ct-ultimate-gdpr' ),
				'name' => 'service_name',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_5b0e763cfcf3f',
				'label' => esc_html__( 'Script names', 'ct-ultimate-gdpr' ),
				'name' => 'script_name',
				'type' => 'text',
				'instructions' => esc_html__( 'Please input comma separated names of cookie creating scripts', 'ct-ultimate-gdpr' ),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_5b0e7655fcf40',
				'label' => esc_html__( 'Cookie names', 'ct-ultimate-gdpr' ),
				'name' => 'cookie_name',
				'type' => 'text',
				'instructions' => esc_html__( 'Please input comma separated cookie names', 'ct-ultimate-gdpr' ),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_5b0e7661fcf41',
				'label' => esc_html__( 'Type of cookie', 'ct-ultimate-gdpr' ),
				'name' => 'type_of_cookie',
				'type' => 'select',
				'choices' => CT_Ultimate_GDPR_Model_Group::get_all_labels(),
				'default_value' => '',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5b0e7684fcf42',
				'label' => esc_html__( 'First or Third party?', 'ct-ultimate-gdpr' ),
				'name' => 'first_or_third_party',
				'type' => 'select',
				'choices' => array (
					'default' => '',
					'first_party' => esc_html__( 'First party', 'ct-ultimate-gdpr' ),
					'third_party' => esc_html__( 'Third party', 'ct-ultimate-gdpr' ),
				),
				'default_value' => '',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5b0e76d4fcf43',
				'label' => esc_html__( 'Can be blocked?', 'ct-ultimate-gdpr' ),
				'name' => 'can_be_blocked',
				'type' => 'true_false',
				'message' => '',
				'default_value' => 0,
			),
			array (
				'key' => 'field_5b0e76ebfcf44',
				'label' => esc_html__( 'Session or Persistent?', 'ct-ultimate-gdpr' ),
				'name' => 'session_or_persistent',
				'type' => 'select',
				'instructions' => '',
				'choices' => array (
					'default' => '',
					'session' => esc_html__( 'Session', 'ct-ultimate-gdpr' ),
					'persistent' => esc_html__( 'Persistent', 'ct-ultimate-gdpr' )
				),
				'default_value' => '',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5b0e7725fcf45',
				'label' => esc_html__( 'Expiry Time', 'ct-ultimate-gdpr' ),
				'name' => 'expiry_time',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
			),
			array (
				'key' => 'field_5b0e7730fcf46',
				'label' => esc_html__( 'Purpose', 'ct-ultimate-gdpr' ),
				'name' => 'purpose',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_5b0e7733fcf47',
				'label' => esc_html__( 'Do you want to activate this service?', 'ct-ultimate-gdpr' ),
				'name' => 'is_active',
				'type' => 'true_false',
				'message' => '',
				'default_value' => 0,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'ct_ugdpr_service',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
				0 => 'custom_fields',
				1 => 'the_content',
			),
		),
		'menu_order' => 0,
	));
}

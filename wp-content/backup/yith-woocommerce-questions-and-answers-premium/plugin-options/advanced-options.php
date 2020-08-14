<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$advanced_options = array(
	
	'advanced' => array(
		'section_advanced_settings' => array(
			'name' => esc_html__( 'Advanced settings', 'yith-woocommerce-questions-and-answers' ),
			'type' => 'title',
			'id'   => 'ywqa_section_advanced'
		),
		
		'ywqa_enable_question_vote'                 => array(
			'name'    => esc_html__( 'Vote question', 'yith-woocommerce-questions-and-answers' ),
			'type'    => 'checkbox',
			'desc'    => esc_html__( 'Allow users to vote product questions.', 'yith-woocommerce-questions-and-answers' ),
			'id'      => 'ywqa_enable_question_vote',
			'default' => 'yes'
		),
		'ywqa_notify_new_question'                  => array(
			'name'    => esc_html__( 'New question notification', 'yith-woocommerce-questions-and-answers' ),
			'desc'    => esc_html__( 'Send a notification email to the administrator when new questions are available.', 'yith-woocommerce-questions-and-answers' ),
			'type'    => 'select',
            'class'   => 'wc-enhanced-select',
			'options' => array(
				'disabled' => esc_html__( 'Do not send notifications', 'yith-woocommerce-questions-and-answers' ),
				//'plain'    => esc_html__( 'Notification in text email', 'yith-woocommerce-questions-and-answers' ),
				'html'     => esc_html__( 'Notification in HTML email', 'yith-woocommerce-questions-and-answers' ),
			),
			'default' => 'disabled',
			'std'     => 'disabled',
			'id'      => 'ywqa_notify_new_question',
		),
		'ywqa_notify_new_answer'                    => array(
			'name'    => esc_html__( 'New answer notification', 'yith-woocommerce-questions-and-answers' ),
			'desc'    => esc_html__( 'Send a notification email to the administrator when new answers are available.', 'yith-woocommerce-questions-and-answers' ),
			'type'    => 'select',
            'class'   => 'wc-enhanced-select',
			'options' => array(
				'disabled' => esc_html__( 'Do not send notifications', 'yith-woocommerce-questions-and-answers' ),
				//'plain'    => esc_html__( 'Notification in text email', 'yith-woocommerce-questions-and-answers' ),
				'html'     => esc_html__( 'Notification in HTML email', 'yith-woocommerce-questions-and-answers' ),
			),
			'default' => 'disabled',
			'std'     => 'disabled',
			'id'      => 'ywqa_notify_new_answer',
		),
		'ywqa_notify_answers_to_user'               => array(
			'name'    => esc_html__( 'User notification', 'yith-woocommerce-questions-and-answers' ),
			'desc'    => esc_html__( 'Allow users to receive a notification when their questions receive an answer.', 'yith-woocommerce-questions-and-answers' ),
			'type'    => 'checkbox',
			'default' => 'yes',
			'std'     => 'yes',
			'id'      => 'ywqa_notify_answers_to_user',
		),
		'ywqa_enable_answer_vote'                   => array(
			'name'    => esc_html__( 'Vote answers', 'yith-woocommerce-questions-and-answers' ),
			'type'    => 'checkbox',
			'desc'    => esc_html__( 'Allow users to vote answers.', 'yith-woocommerce-questions-and-answers' ),
			'id'      => 'ywqa_enable_answer_vote',
			'default' => 'yes'
		),
		'ywqa_enable_answer_abuse_reporting'        => array(
			'name'    => esc_html__( 'Inappropriate content', 'yith-woocommerce-questions-and-answers' ),
			'type'    => 'select',
            'class'   => 'wc-enhanced-select',
			'desc'    => esc_html__( 'Let users report an answer as inappropriate content.', 'yith-woocommerce-questions-and-answers' ),
			'id'      => 'ywqa_enable_answer_abuse_reporting',
			'options' => array(
				'disabled'   => esc_html__( 'Not enabled', 'yith-woocommerce-questions-and-answers' ),
				'registered' => esc_html__( 'Only registered users can report an inappropriate content', 'yith-woocommerce-questions-and-answers' ),
				'everyone'   => esc_html__( 'Everyone can report an inappropriate content', 'yith-woocommerce-questions-and-answers' ),
			),
			'default' => '2'
		),
		'ywqa_hide_inappropriate_content_threshold' => array(
			'name'              => esc_html__( 'Hiding threshold', 'yith-woocommerce-questions-and-answers' ),
			'type'              => 'number',
			'desc'              => esc_html__( 'Hide temporarily an answer when a specific number of users has flagged it as inappropriate. Set this value to 0 to never hide automatically the reviews.', 'yith-woocommerce-questions-and-answers' ),
			'id'                => 'ywqa_hide_inappropriate_content_threshold',
			'custom_attributes' => array(
				'min'      => 0,
				'step'     => 1,
				'required' => 'required'
			),
			'default'           => '0'
		),
		'ywqa_enable_answer_excerpt'                => array(
			'name'              => esc_html__( 'Answer excerpt', 'yith-woocommerce-questions-and-answers' ),
			'type'              => 'number',
			'desc'              => esc_html__( 'Set max length for answers and show a "Read more" text for showing all content.', 'yith-woocommerce-questions-and-answers' ),
			'id'                => 'ywqa_enable_answer_excerpt',
			'custom_attributes' => array(
				'min'      => 0,
				'step'     => 1,
				'required' => 'required'
			),
			'default'           => '0'
		),
		'ywqa_anonymise_user'                       => array(
			'name'    => esc_html__( 'Anonymous mode', 'yith-woocommerce-questions-and-answers' ),
			'type'    => 'checkbox',
			'desc'    => esc_html__( "Do not show the name of the users that have added a question or an answer.", 'yith-woocommerce-questions-and-answers' ),
			'id'      => 'ywqa_anonymise_user',
			'default' => '1'
		),
        'ywqa_anonymise_date'                       => array(
            'name'    => esc_html__( 'Anonymous date mode', 'yith-woocommerce-questions-and-answers' ),
            'type'    => 'checkbox',
            'desc'    => esc_html__( "Do not show the date when users added a question or an answer.", 'yith-woocommerce-questions-and-answers' ),
            'id'      => 'ywqa_anonymise_date',
            'default' => '1'
        ),
		'ywqa_ask_customers'                        => array(
			'name'    => esc_html__( 'Ask customers for an answer', 'yith-woocommerce-questions-and-answers' ),
			'desc'    => esc_html__( 'Send an email to whoever purchased a product when a new question is available.', 'yith-woocommerce-questions-and-answers' ),
			'type'    => 'select',
            'class'   => 'wc-enhanced-select',
			'options' => array(
				'disabled' => esc_html__( 'Do not send requests', 'yith-woocommerce-questions-and-answers' ),
				'all'      => esc_html__( 'Send an email to all customers', 'yith-woocommerce-questions-and-answers' ),
				'custom'   => esc_html__( 'Send an email to a sample of customers', 'yith-woocommerce-questions-and-answers' ),
			),
			'default' => 'disabled',
			'std'     => 'disabled',
			'id'      => 'ywqa_ask_customers',
		),
		'ywqa_ask_customers_percent'                => array(
			'name'              => esc_html__( 'Survey sample size', 'yith-woocommerce-questions-and-answers' ),
			'type'              => 'number',
			'desc'              => esc_html__( '(%) Set the percentage of customers that have bought the product that you want to contact to ask for an answer.', 'yith-woocommerce-questions-and-answers' ),
			'id'                => 'ywqa_ask_customers_percent',
			'custom_attributes' => array(
				'min'      => 1,
				'max'      => 100,
				'step'     => 1,
				'required' => 'required'
			),
			'default'           => '50'
		),
		'ywqa_enable_recaptcha'                     => array(
			'name'    => esc_html__( 'reCaptcha', 'yith-woocommerce-questions-and-answers' ),
			'type'    => 'checkbox',
			'desc'    => esc_html__( 'Enable reCaptcha on plugin forms.', 'yith-woocommerce-questions-and-answers' ),
			'id'      => 'ywqa_enable_recaptcha',
			'default' => 'not'
		),

        'ywqa_recaptcha_version'                     => array(
            'name'    => esc_html__( 'reCaptcha version', 'yith-woocommerce-questions-and-answers' ),
            'type'    => 'select',
            'class'   => 'wc-enhanced-select',
            'options' => array(
                'v2' => esc_html__( 'reCaptcha v2', 'yith-woocommerce-questions-and-answers' ),
                'v3' => esc_html__( 'reCaptcha v3', 'yith-woocommerce-questions-and-answers' ),
            ),
            'desc'    => esc_html__( 'Choose the reCaptcha version.', 'yith-woocommerce-questions-and-answers' ),
            'id'      => 'ywqa_recaptcha_version',
            'default' => 'v2'
        ),
		'ywqa_recaptcha_site_key'                   => array(
			'name' => esc_html__( 'reCaptcha site key', 'yith-woocommerce-questions-and-answers' ),
			'type' => 'text',
			'desc' => esc_html__( 'Insert your reCaptcha site key.', 'yith-woocommerce-questions-and-answers' ),
			'id'   => 'ywqa_recaptcha_site_key',
			'css'  => 'min-width:50%;',
		
		),
		'ywqa_recaptcha_secret_key'                 => array(
			'name' => esc_html__( 'reCaptcha secret key', 'yith-woocommerce-questions-and-answers' ),
			'type' => 'text',
			'desc' => esc_html__( 'Insert your reCaptcha secret key.', 'yith-woocommerce-questions-and-answers' ),
			'id'   => 'ywqa_recaptcha_secret_key',
			'css'  => 'min-width:50%;',
		),
		'section_advanced_settings_end'             => array(
			'type' => 'sectionend',
			'id'   => 'ywqa_section_advanced_end'
		)
	)
);


return apply_filters ( 'ywqa_advanced_options', $advanced_options );


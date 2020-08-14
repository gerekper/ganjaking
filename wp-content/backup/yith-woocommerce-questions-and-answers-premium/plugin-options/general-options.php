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
		'section_general_settings'     => array(
			'name' => esc_html__( 'General settings', 'yith-woocommerce-questions-and-answers' ),
			'type' => 'title',
			'id'   => 'ywqa_section_general'
		),
		'ywqa_shop_name' => array(
			'name' => esc_html__('Shop name', 'yith-woocommerce-questions-and-answers'),
			'desc' => esc_html__('The reference name for email notifications.', 'yith-woocommerce-questions-and-answers'),
			'type' => 'text',
			'id' => 'ywqa_shop_name',
		),
		'ywqa_questions_to_show'       => array(
			'name'              => esc_html__( 'Question paging', 'yith-woocommerce-questions-and-answers' ),
			'type'              => 'number',
			'desc'              => esc_html__( 'Set how many questions you want to show for each product (set 0 to display all).', 'yith-woocommerce-questions-and-answers' ),
			'id'                => 'ywqa_questions_to_show',
			'default'           => '0',
			'custom_attributes' => array(
				'min'      => 0,
				'step'     => 1,
				'required' => 'required'
			)
		),
		'ywqa_answers_to_show'       => array(
			'name'              => esc_html__( 'Answer paging', 'yith-woocommerce-questions-and-answers' ),
			'type'              => 'number',
			'desc'              => esc_html__( 'Set how many answers you want to show for each question (set 0 to display all).', 'yith-woocommerce-questions-and-answers' ),
			'id'                => 'ywqa_answers_to_show',
			'default'           => '0',
			'custom_attributes' => array(
				'min'      => 0,
				'step'     => 1,
				'required' => 'required'
			)
		),
		'ywqa_question_manual_approval' => array(
			'name' => esc_html__('Question approval', 'yith-woocommerce-questions-and-answers'),
			'type' => 'checkbox',
			'desc' => esc_html__('The entered question has to be approved before it may be shown.', 'yith-woocommerce-questions-and-answers'),
			'id' => 'ywqa_question_manual_approval',
			'default' => 'no'
		),
		'ywqa_answer_manual_approval' => array(
			'name' => esc_html__('Answer approval', 'yith-woocommerce-questions-and-answers'),
			'type' => 'checkbox',
			'desc' => esc_html__('The entered answer has to be approved before it may be shown.', 'yith-woocommerce-questions-and-answers'),
			'id' => 'ywqa_answer_manual_approval',
			'default' => 'no'
		),
		'ywqa_allow_guest' => array(
			'name' => esc_html__('Allow guest users', 'yith-woocommerce-questions-and-answers'),
			'type' => 'checkbox',
			'desc' => esc_html__('Let guest user to enter questions or answers', 'yith-woocommerce-questions-and-answers'),
			'id' => 'ywqa_allow_guest',
			'default' => 'yes'
		),
		'ywqa_only_admin_answers' => array(
			'name' => esc_html__('Only admins can reply', 'yith-woocommerce-questions-and-answers'),
			'type' => 'checkbox',
			'desc' => esc_html__('If enabled, only the admins can reply to the questions.', 'yith-woocommerce-questions-and-answers'),
			'id' => 'ywqa_only_admin_answers',
			'default' => 'no'
		),
		'ywqa_mandatory_guest_data' => array(
			'name' => esc_html__('Mandatory data for guest users', 'yith-woocommerce-questions-and-answers'),
			'type' => 'checkbox',
			'desc' => esc_html__('Guest user that want to submit a question or answers must fill his name and email', 'yith-woocommerce-questions-and-answers'),
			'id' => 'ywqa_mandatory_guest_data',
			'default' => 'yes'
		),
		'ywqa_faq_mode'                => array(
			'name'    => esc_html__( 'FAQ mode', 'yith-woocommerce-questions-and-answers' ),
			'type'    => 'checkbox',
			'desc'    => esc_html__( 'Don\'t allow users to add questions and answers, but let them read them in FAQ mode.', 'yith-woocommerce-questions-and-answers' ),
			'id'      => 'ywqa_faq_mode',
			'default' => 'no',
		),
		'ywqa_attach_to_tabs'                => array(
			'name'    => esc_html__( 'Show on product tabs', 'yith-woocommerce-questions-and-answers' ),
			'type'    => 'checkbox',
			'desc'    => esc_html__( 'Choose if the plugin output should be shown as a tab in the product tabs. Uncheck it if you want to use the shortcode [ywqa_questions] in a custom position', 'yith-woocommerce-questions-and-answers' ),
			'id'      => 'ywqa_attach_to_tabs',
			'default' => 'yes',
		),
		'ywqa_tab_label'                => array(
			'name'    => esc_html__( 'Tab label', 'yith-woocommerce-questions-and-answers' ),
			'type'    => 'text',
			'desc'    => esc_html__( 'Set the text for the label showed in single product page', 'yith-woocommerce-questions-and-answers' ),
			'id'      => 'ywqa_tab_label',
			'default' => esc_html__( 'Questions & Answers', 'yith-woocommerce-questions-and-answers' ),
		),
		'ywqa_question_section_title'                => array(
			'name'    => esc_html__( 'Question section title', 'yith-woocommerce-questions-and-answers' ),
			'type'    => 'textarea',
			'desc'    => esc_html__( 'Set the title for the section "Questions & Answers"', 'yith-woocommerce-questions-and-answers' ),
			'id'      => 'ywqa_question_section_title',
			'default' => esc_html__( 'Questions and answers of the customers', 'yith-woocommerce-questions-and-answers' ),
		),
		'section_general_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywqa_section_general_end'
		)
	)
);

return apply_filters( 'ywqa_general_options', $general_options );


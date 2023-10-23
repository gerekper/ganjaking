<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package       YITH\yit-woocommerce-advanced-reviews\plugin-options
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$review_settings = array(
	'voting' => array(
		'section_vote_system_settings'     => array(
			'name' => esc_html__( 'Voting system', 'yith-woocommerce-advanced-reviews' ),
			'type' => 'title',
			'id'   => 'ywar_section_general',
		),
		'vote_system_enable'               => array(
			'name'      => esc_html__( 'Show vote section', 'yith-woocommerce-advanced-reviews' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html__( 'Allow user to upvote or downvote a review.', 'yith-woocommerce-advanced-reviews' ),
			'id'        => 'ywar_enable_vote_system',
			'default'   => 'yes',
		),
		'vote_system_show_peoples_choice'  => array(
			'name'      => esc_html__( 'Show review votes', 'yith-woocommerce-advanced-reviews' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html__( 'Add a string stating how many people found the review useful.', 'yith-woocommerce-advanced-reviews' ),
			'id'        => 'ywar_show_peoples_vote',
			'default'   => 'yes',
			'deps'      => array(
				'id'    => 'ywar_enable_vote_system',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),
		'enable_visitors_vote'             => array(
			'name'      => esc_html__( 'Enable vote for all', 'yith-woocommerce-advanced-reviews' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html__( 'Allow unregistered users to vote the reviews.', 'yith-woocommerce-advanced-reviews' ),
			'id'        => 'ywar_enable_visitors_vote',
			'default'   => 'no',
			'deps'      => array(
				'id'    => 'ywar_enable_vote_system',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),
		'section_vote_system_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywar_section_general_end',
		),
		'section_reviews_settings'         => array(
			'name' => esc_html__( 'Review settings', 'yith-woocommerce-advanced-reviews' ),
			'type' => 'title',
			'id'   => 'ywar_section_reviews',
		),
		'review_moderation'                => array(
			'name'      => esc_html__( 'Manual review approval', 'yith-woocommerce-advanced-reviews' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html__( 'When a user submits a review, it should be manually approved to be showed', 'yith-woocommerce-advanced-reviews' ),
			'id'        => 'ywar_review_moderation',
			'default'   => 'no',
		),
		'ywar_limit_multiple_review'       => array(
			'name'      => esc_html__( 'Limit multiple reviews', 'yith-woocommerce-advanced-reviews' ),
			'desc'      => esc_html__( 'Only allow one review for a product. Require "Only allow reviews from verified owners" to be set to work properly', 'yith-woocommerce-advanced-reviews' ),
			'id'        => 'ywar_limit_multiple_review',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),
		'ywar_edit_reviews'                => array(
			'name'      => esc_html__( 'Edit reviews', 'yith-woocommerce-advanced-reviews' ),
			'desc'      => esc_html__( 'Allow the users to edit their review', 'yith-woocommerce-advanced-reviews' ),
			'id'        => 'ywar_edit_reviews',
			'default'   => 'no',
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
		),
		'show_how_many_reviews'            => array(
			'name'      => esc_html__( 'Number of reviews to display', 'yith-woocommerce-advanced-reviews' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'desc'      => esc_html__( 'Limit the maximum number of reviews to display, 0 for unlimited', 'yith-woocommerce-advanced-reviews' ),
			'id'        => 'ywar_review_per_page',
			'required'  => 'required',
			'min'       => 0,
			'step'      => 1,
			'default'   => 0,
		),
		'show_load_more'                   => array(
			'name'      => esc_html__( '"Load more"', 'yith-woocommerce-advanced-reviews' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'desc'      => esc_html__( 'Choose if reviews should be shown grouped and which link style should be applied.', 'yith-woocommerce-advanced-reviews' ),
			'id'        => 'ywar_show_load_more',
			'options'   => array(
				'1' => esc_html__( 'Don\'t group reviews', 'yith-woocommerce-advanced-reviews' ),
				'2' => esc_html__( 'Group reviews and show a textual link on bottom', 'yith-woocommerce-advanced-reviews' ),
				'3' => esc_html__( 'Group reviews and show a button link on bottom', 'yith-woocommerce-advanced-reviews' ),
			),
			'class'     => 'wc-enhanced-select',
			'default'   => '1',
		),
		'show_reviews_dialog'              => array(
			'name'      => esc_html__( 'Modal window', 'yith-woocommerce-advanced-reviews' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html__( 'Display reviews filtered by rate in a modal window.', 'yith-woocommerce-advanced-reviews' ),
			'id'        => 'ywar_reviews_dialog',
			'default'   => 'no',
		),
		'reply_to_review'                  => array(
			'name'      => esc_html__( 'Reply to review', 'yith-woocommerce-advanced-reviews' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'desc'      => esc_html__( 'Choose who can reply to a review.', 'yith-woocommerce-advanced-reviews' ),
			'id'        => 'ywar_reply_to_review',
			'options'   => array(
				'1' => esc_html__( 'No one can reply', 'yith-woocommerce-advanced-reviews' ),
				'2' => esc_html__( 'Only administrators can reply', 'yith-woocommerce-advanced-reviews' ),
				'3' => esc_html__( 'Everyone can reply', 'yith-woocommerce-advanced-reviews' ),
			),
			'class'     => 'wc-enhanced-select',
			'default'   => '2',
		),
		'report_inappropriate_review'      => array(
			'name'      => esc_html__( 'Inappropriate reviews', 'yith-woocommerce-advanced-reviews' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'desc'      => esc_html__( 'Let users report a review as inappropriate content.', 'yith-woocommerce-advanced-reviews' ),
			'id'        => 'ywar_report_inappropriate_review',
			'options'   => array(
				'0' => esc_html__( 'Not enabled', 'yith-woocommerce-advanced-reviews' ),
				'1' => esc_html__( 'Only registered users can report an inappropriate content', 'yith-woocommerce-advanced-reviews' ),
				'2' => esc_html__( 'Everyone can report an inappropriate content', 'yith-woocommerce-advanced-reviews' ),
			),
			'class'     => 'wc-enhanced-select',
			'default'   => '2',
		),
		'hide_inappropriate_review'        => array(
			'name'      => esc_html__( 'Hiding threshold', 'yith-woocommerce-advanced-reviews' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'desc'      => esc_html__( 'Hide temporarily a review when a specific number of users has flagged it as inappropriate. Set this value to 0 to never hide automatically the reviews.', 'yith-woocommerce-advanced-reviews' ),
			'id'        => 'ywar_hide_inappropriate_review',
			'required'  => 'required',
			'min'       => 0,
			'step'      => 1,
			'default'   => 0,
		),
		'show_featured_review'             => array(
			'name'     => esc_html__( 'Featured reviews', 'yith-woocommerce-advanced-reviews' ),
			'type'     => 'number',
			'desc'     => esc_html__( 'Number of reviews to show as featured items. Set this value to 0 to never show featured reviews.', 'yith-woocommerce-advanced-reviews' ),
			'id'       => 'ywar_featured_review',
			'required' => 'required',
			'min'      => 0,
			'step'     => 1,
			'default'  => 0,
		),
		'ywar_featured_review_tab_first'   => array(
			'name'      => esc_html__( 'Enable Most Helpful Reviews tab by default', 'yith-woocommerce-advanced-reviews' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => esc_html__( 'Show Most Helpful Reviews tab selected by default.', 'yith-woocommerce-advanced-reviews' ),
			'id'        => 'ywar_featured_review_tab_first',
			'default'   => 'no',
		),
		'load_more_text'                   => array(
			'name'      => esc_html__( '"Load more" text', 'yith-woocommerce-advanced-reviews' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'desc'      => esc_html__( 'Text to show in the textual link or button.', 'yith-woocommerce-advanced-reviews' ),
			'id'        => 'ywar_load_more_text',
			'default'   => esc_html__( 'Load more', 'yith-woocommerce-advanced-reviews' ),
		),
		'section_reviews_settings_end'     => array(
			'type' => 'sectionend',
			'id'   => 'ywar_section_reviews_end',
		),
	),
);
/** APPLY_FILTERS: ywar_review_voting_settings
*
* Filter the default plugin options.
*
* @param array $review_settings Default plugin options.
*/
return apply_filters( 'ywar_review_voting_settings', $review_settings );

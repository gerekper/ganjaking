<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return array(
	'name' => esc_html__( 'Tweetmeme Button', 'js_composer' ),
	'base' => 'vc_tweetmeme',
	'icon' => 'icon-wpb-tweetme',
	'category' => esc_html__( 'Social', 'js_composer' ),
	'description' => esc_html__( 'Tweet button', 'js_composer' ),
	'params' => array(
		array(
			'type' => 'dropdown',
			'param_name' => 'type',
			'heading' => esc_html__( 'Choose a button', 'js_composer' ),
			'value' => array(
				esc_html__( 'Share a link', 'js_composer' ) => 'share',
				esc_html__( 'Follow', 'js_composer' ) => 'follow',
				esc_html__( 'Hashtag', 'js_composer' ) => 'hashtag',
				esc_html__( 'Mention', 'js_composer' ) => 'mention',
			),
			'description' => esc_html__( 'Select type of Twitter button.', 'js_composer' ),
		),
		// share type
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Share url: page URL', 'js_composer' ),
			'param_name' => 'share_use_page_url',
			'value' => array(
				esc_html__( 'Yes', 'js_composer' ) => 'page_url',
			),
			'std' => 'page_url',
			'dependency' => array(
				'element' => 'type',
				'value' => 'share',
			),
			'description' => esc_html__( 'Use the current page url to share?', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Share url: custom URL', 'js_composer' ),
			'param_name' => 'share_use_custom_url',
			'value' => '',
			'dependency' => array(
				'element' => 'share_use_page_url',
				'value_not_equal_to' => 'page_url',
			),
			'description' => esc_html__( 'Enter custom page url which you like to share on twitter?', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Tweet text: page title', 'js_composer' ),
			'param_name' => 'share_text_page_title',
			'value' => array(
				esc_html__( 'Yes', 'js_composer' ) => 'page_title',
			),
			'std' => 'page_title',
			'dependency' => array(
				'element' => 'type',
				'value' => 'share',
			),
			'description' => esc_html__( 'Use the current page title as tweet text?', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Tweet text: custom text', 'js_composer' ),
			'param_name' => 'share_text_custom_text',
			'value' => '',
			'dependency' => array(
				'element' => 'share_text_page_title',
				'value_not_equal_to' => 'page_title',
			),
			'description' => esc_html__( 'Enter the text to be used as a tweet?', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Via @', 'js_composer' ),
			'param_name' => 'share_via',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'share',
			),
			'description' => esc_html__( 'Enter your Twitter username.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Recommend @', 'js_composer' ),
			'param_name' => 'share_recommend',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'share',
			),
			'description' => esc_html__( 'Enter the Twitter username to be recommended.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Hashtag #', 'js_composer' ),
			'param_name' => 'share_hashtag',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'share',
			),
			'description' => esc_html__( 'Add a comma-separated list of hashtags to a Tweet using the hashtags parameter.', 'js_composer' ),
		),
		// follow type
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'User @', 'js_composer' ),
			'param_name' => 'follow_user',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'follow',
			),
			'description' => esc_html__( 'Enter username to follow.', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Show username', 'js_composer' ),
			'param_name' => 'follow_show_username',
			'value' => array(
				esc_html__( 'Yes', 'js_composer' ) => 'yes',
			),
			'std' => 'yes',
			'dependency' => array(
				'element' => 'type',
				'value' => 'follow',
			),
			'description' => esc_html__( 'Do you want to show username in button?', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Show followers count', 'js_composer' ),
			'param_name' => 'show_followers_count',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'follow',
			),
			'description' => esc_html__( 'Do you want to displat the follower count in button?', 'js_composer' ),
		),
		// hashtag type
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Hashtag #', 'js_composer' ),
			'param_name' => 'hashtag_hash',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'hashtag',
			),
			'description' => esc_html__( 'Add hashtag to a Tweet using the hashtags parameter', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Tweet text: No default text', 'js_composer' ),
			'param_name' => 'hashtag_no_default',
			'value' => array(
				esc_html__( 'Yes', 'js_composer' ) => 'yes',
			),
			'std' => 'yes',
			'dependency' => array(
				'element' => 'type',
				'value' => 'hashtag',
			),
			'description' => esc_html__( 'Set no default text for tweet?', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Tweet text: custom', 'js_composer' ),
			'param_name' => 'hashtag_custom_tweet_text',
			'value' => '',
			'dependency' => array(
				'element' => 'hashtag_no_default',
				'value_not_equal_to' => 'yes',
			),
			'description' => esc_html__( 'Set custom text for tweet.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Recommend @', 'js_composer' ),
			'param_name' => 'hashtag_recommend_1',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'hashtag',
			),
			'description' => esc_html__( 'Enter username to be recommended.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Recommend @', 'js_composer' ),
			'param_name' => 'hashtag_recommend_2',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'hashtag',
			),
			'description' => esc_html__( 'Enter username to be recommended.', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Tweet url: No URL', 'js_composer' ),
			'param_name' => 'hashtag_no_url',
			'value' => array(
				esc_html__( 'Yes', 'js_composer' ) => 'yes',
			),
			'std' => 'yes',
			'dependency' => array(
				'element' => 'type',
				'value' => 'hashtag',
			),
			'description' => esc_html__( 'Do you want to set no url to be tweeted?', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Tweet url: custom', 'js_composer' ),
			'param_name' => 'hashtag_custom_tweet_url',
			'value' => '',
			'dependency' => array(
				'element' => 'hashtag_no_url',
				'value_not_equal_to' => 'yes',
			),
			'description' => esc_html__( 'Enter custom url to be used in the tweet.', 'js_composer' ),
		),
		// mention type
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Tweet to @', 'js_composer' ),
			'param_name' => 'mention_tweet_to',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'mention',
			),
			'description' => esc_html__( 'Enter username where you want to send your tweet.', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Tweet text: No default text', 'js_composer' ),
			'param_name' => 'mention_no_default',
			'value' => array(
				esc_html__( 'Yes', 'js_composer' ) => 'yes',
			),
			'std' => 'yes',
			'dependency' => array(
				'element' => 'type',
				'value' => 'mention',
			),
			'description' => esc_html__( 'Set no default text of the tweet?', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Tweet text: custom', 'js_composer' ),
			'param_name' => 'mention_custom_tweet_text',
			'value' => '',
			'dependency' => array(
				'element' => 'mention_no_default',
				'value_not_equal_to' => 'yes',
			),
			'description' => esc_html__( 'Enter custom text for the tweet.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Recommend @', 'js_composer' ),
			'param_name' => 'mention_recommend_1',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'mention',
			),
			'description' => esc_html__( 'Enter username to recommend.', 'js_composer' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Recommend @', 'js_composer' ),
			'param_name' => 'mention_recommend_2',
			'value' => '',
			'dependency' => array(
				'element' => 'type',
				'value' => 'mention',
			),
			'description' => esc_html__( 'Enter username to recommend.', 'js_composer' ),
		),
		// general
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Use large button', 'js_composer' ),
			'param_name' => 'large_button',
			'value' => '',
			'description' => esc_html__( 'Do you like to display a larger Tweet button?', 'js_composer' ),
		),
		array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Opt-out of tailoring Twitter', 'js_composer' ),
			'param_name' => 'disable_tailoring',
			'value' => '',
			'description' => esc_html__( 'Tailored suggestions make building a great timeline. Would you like to disable this feature?', 'js_composer' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Language', 'js_composer' ),
			'param_name' => 'lang',
			'value' => array(
				'Automatic' => '',
				'French - français' => 'fr',
				'English' => 'en',
				'Arabic - العربية' => 'ar',
				'Japanese - 日本語' => 'ja',
				'Spanish - Español' => 'es',
				'German - Deutsch' => 'de',
				'Italian - Italiano' => 'it',
				'Indonesian - Bahasa Indonesia' => 'id',
				'Portuguese - Português' => 'pt',
				'Korean - 한국어' => 'ko',
				'Turkish - Türkçe' => 'tr',
				'Russian - Русский' => 'ru',
				'Dutch - Nederlands' => 'nl',
				'Filipino - Filipino' => 'fil',
				'Malay - Bahasa Melayu' => 'msa',
				'Traditional Chinese - 繁體中文' => 'zh-tw',
				'Simplified Chinese - 简体中文' => 'zh-cn',
				'Hindi - हिन्दी' => 'hi',
				'Norwegian - Norsk' => 'no',
				'Swedish - Svenska' => 'sv',
				'Finnish - Suomi' => 'fi',
				'Danish - Dansk' => 'da',
				'Polish - Polski' => 'pl',
				'Hungarian - Magyar' => 'hu',
				'Farsi - فارسی' => 'fa',
				'Hebrew - עִבְרִית' => 'he',
				'Urdu - اردو' => 'ur',
				'Thai - ภาษาไทย' => 'th',
			),
			'description' => esc_html__( 'Select button display language or allow it to be automatically defined by user preferences.', 'js_composer' ),
		),
		vc_map_add_css_animation(),
		array(
			'type' => 'el_id',
			'heading' => esc_html__( 'Element ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %1$sw3c specification%2$s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
		),
		array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
		array(
			'type' => 'css_editor',
			'heading' => esc_html__( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => esc_html__( 'Design Options', 'js_composer' ),
		),
	),
);

<?php
namespace TheplusAddons;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

Class WPML {
	
	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;
	
    public function plus_translate_widgets($widgets)
    {
		 $widgets['tp-adv-text-block'] = [
            'conditions' => ['widgetType' => 'tp-adv-text-block'],
            'fields' => [
                [
                    'field'       => 'content_description',
                    'type'        => esc_html__('Advanced Text Block Description', 'theplus'),
                    'editor_type' => 'VISUAL',
                ]
            ],
        ];
		$widgets['tp-advanced-buttons'] = [
            'conditions' => ['widgetType' => 'tp-advanced-buttons'],
            'fields' => [
                [
                    'field'       => 'common_button_text',
                    'type'        => esc_html__('Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'dbt_button_text_2',
                    'type'        => esc_html__('Loading text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'dbt_button_text_3',
                    'type'        => esc_html__('Success text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'common_button_text_2',
                    'type'        => esc_html__('Extra Text 1', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'db_common_button_text_2',
                    'type'        => esc_html__('Extra Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'common_button_text_3',
                    'type'        => esc_html__('Extra Text 2', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'download_file_name',
                    'type'        => esc_html__('Download File Name', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp_advertisement_banner'] = [
            'conditions' => ['widgetType' => 'tp_advertisement_banner'],
            'fields' => [
                [
                    'field'       => 'title',
                    'type'        => esc_html__('Advertisement Banner Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'subtitle',
                    'type'        => esc_html__('Advertisement Banner SubTitle', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'button_text',
                    'type'        => esc_html__('Advertisement Banner Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'button_hover_text',
                    'type'        => esc_html__('Advertisement Banner Button Hover Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-before-after'] = [
            'conditions' => ['widgetType' => 'tp-before-after'],
            'fields' => [
                [
                    'field'       => 'before_label',
                    'type'        => esc_html__('Label for Before', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'after_label',
                    'type'        => esc_html__('Label for After', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-blockquote'] = [
            'conditions' => ['widgetType' => 'tp-blockquote'],
            'fields' => [
                [
                    'field'       => 'content_description',
                    'type'        => esc_html__('Quote Description', 'theplus'),
                    'editor_type' => 'VISUAL',
                ],
				[
                    'field'       => 'quote_author',
                    'type'        => esc_html__('Quote Author', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-blog-listout'] = [
            'conditions' => ['widgetType' => 'tp-blog-listout'],
            'fields' => [
                [
                    'field'       => 'button_text',
                    'type'        => esc_html__('Blog Listout Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'all_filter_category',
                    'type'        => esc_html__('Blog Listout All Filter Category Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'load_more_btn_text',
                    'type'        => esc_html__('Blog Listout Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'tp_loading_text',
                    'type'        => esc_html__('Dynamic Listing Loading Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'loaded_posts_text',
                    'type'        => esc_html__('Blog Listout All Posts Loaded Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'pagination_next',
                    'type'        => esc_html__('Pagination Next Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'pagination_prev',
                    'type'        => esc_html__('Pagination Previous Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-breadcrumbs-bar'] = [
            'conditions' => ['widgetType' => 'tp-breadcrumbs-bar'],
            'fields' => [
                [
                    'field'       => 'home_title',
                    'type'        => esc_html__('Breadcrumbs Bar Home Title', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-button'] = [
            'conditions' => ['widgetType' => 'tp-button'],
            'fields' => [
                [
                    'field'       => 'button_text',
                    'type'        => esc_html__('Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'button_24_text',
                    'type'        => esc_html__('Button Tag Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'button_hover_text',
                    'type'        => esc_html__('Button Hover Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				 [
                    'field'       => 'plus_tooltip_content_desc',
                    'type'        => esc_html__('Button Tooltip Content', 'theplus'),
                    'editor_type' => 'AREA',
                ],
				[
                    'field'       => 'plus_tooltip_content_wysiwyg',
                    'type'        => esc_html__('Button Tooltip Content', 'theplus'),
                    'editor_type' => 'VISUAL',
                ]
            ],
        ];
		$widgets['tp-carousel-remote'] = [
            'conditions' => ['widgetType' => 'tp-carousel-remote'],
            'fields' => [
                [
                    'field'       => 'nav_next_slide',
                    'type'        => esc_html__('Carousel Remote Next Slide Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'nav_prev_slide',
                    'type'        => esc_html__('Carousel Remote PREV Slide Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-clients-listout'] = [
            'conditions' => ['widgetType' => 'tp-clients-listout'],
            'fields' => [
                [
                    'field'       => 'all_filter_category',
                    'type'        => esc_html__('Clients Listout All Filter Category Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'load_more_btn_text',
                    'type'        => esc_html__('Clients Listout Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'tp_loading_text',
                    'type'        => esc_html__('Dynamic Listing Loading Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'loaded_posts_text',
                    'type'        => esc_html__('Clients Listout All Posts Loaded Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-countdown'] = [
            'conditions' => ['widgetType' => 'tp-countdown'],
            'fields' => [
                [
                    'field'       => 'text_days',
                    'type'        => esc_html__('Countdown Days Section Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'text_hours',
                    'type'        => esc_html__('Countdown Hours Section Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'text_minutes',
                    'type'        => esc_html__('Countdown Minutes Section Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'text_seconds',
                    'type'        => esc_html__('Countdown Seconds Section Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-dynamic-listing'] = [
            'conditions' => ['widgetType' => 'tp-dynamic-listing'],
            'fields' => [
                [
                    'field'       => 'button_text',
                    'type'        => esc_html__('Dynamic Listing Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'all_filter_category',
                    'type'        => esc_html__('Dynamic Listing All Filter Category Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'load_more_btn_text',
                    'type'        => esc_html__('Dynamic Listing Load More Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'tp_loading_text',
                    'type'        => esc_html__('Dynamic Listing Loading Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'loaded_posts_text',
                    'type'        => esc_html__('Dynamic Listing All Posts Loaded Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
				
            ],
        ];
		$widgets['tp-dynamic-smart-showcase'] = [
            'conditions' => ['widgetType' => 'tp-dynamic-smart-showcase'],
            'fields' => [
                [
                    'field'       => 'left_side_filter_text',
                    'type'        => esc_html__('Dynamic Smart Showcase Heading', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'news_label',
                    'type'        => esc_html__('Dynamic Smart Showcase Label', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-header-extras'] = [
            'conditions' => ['widgetType' => 'tp-header-extras'],
            'fields' => [
                [
                    'field'       => 'search_placeholder_text',
                    'type'        => esc_html__('Header Extras Search Placeholder Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'cart_offer_text',
                    'type'        => esc_html__('Header Extras Mini Cart Offer Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'button_1_text',
                    'type'        => esc_html__('Header Extras Button Text 1', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'button_2_text',
                    'type'        => esc_html__('Header Extras Button Text 2', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-heading-animation'] = [
            'conditions' => ['widgetType' => 'tp-heading-animation'],
            'fields' => [
                [
                    'field'       => 'prefix',
                    'type'        => esc_html__('Heading Animation Prefix Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'ani_title',
                    'type'        => esc_html__('Heading Animation Animated Text', 'theplus'),
                    'editor_type' => 'AREA',
                ],
				[
                    'field'       => 'postfix',
                    'type'        => esc_html__('Heading Animation Postfix Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-heading-title'] = [
            'conditions' => ['widgetType' => 'tp-heading-title'],
            'fields' => [
                [
                    'field'       => 'title',
                    'type'        => esc_html__('Heading Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'sub_title',
                    'type'        => esc_html__('Heading Sub Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'title_s',
                    'type'        => esc_html__('Heading Extra Title', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-image-factory'] = [
            'conditions' => ['widgetType' => 'tp-image-factory'],
            'fields' => [
                [
                    'field'       => 'plus_tooltip_content_desc',
                    'type'        => esc_html__('Image Factory Tooltip Content', 'theplus'),
                    'editor_type' => 'AREA',
                ],
				[
                    'field'       => 'plus_tooltip_content_wysiwyg',
                    'type'        => esc_html__('Image Factory Tooltip Content', 'theplus'),
                    'editor_type' => 'VISUAL',
                ]
            ],
        ];
		$widgets['tp-mailchimp-subscribe'] = [
            'conditions' => ['widgetType' => 'tp-mailchimp-subscribe'],
            'fields' => [
                [
                    'field'       => 'email_field_placeholder',
                    'type'        => esc_html__('Mailchimp Subscribe Email Field Placeholder', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'button_text',
                    'type'        => esc_html__('Mailchimp Subscribe Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'loading_suscribe_msg',
                    'type'        => esc_html__('Mailchimp Loading Subscribe Message', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'incorrect_msg',
                    'type'        => esc_html__('Mailchimp Subscribe Incorrect Email Id', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'correct_msg',
                    'type'        => esc_html__('Mailchimp Subscribe Success Message', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		
		$widgets['tp-navigation-menu'] = [
            'conditions' => ['widgetType' => 'tp-navigation-menu'],
            'fields' => [
                [
                    'field'       => 'vertical_side_title_text',
                    'type'        => esc_html__('Navigation Menu Title', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-number-counter'] = [
            'conditions' => ['widgetType' => 'tp-number-counter'],
            'fields' => [
                [
                    'field'       => 'title',
                    'type'        => esc_html__('Number Counter Title', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-off-canvas'] = [
            'conditions' => ['widgetType' => 'tp-off-canvas'],
            'fields' => [
                [
                    'field'       => 'button_text',
                    'type'        => esc_html__('Off Canvas Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-page-scroll'] = [
            'conditions' => ['widgetType' => 'tp-page-scroll'],
            'fields' => [
                [
                    'field'       => 'nav_dots_tooltips',
                    'type'        => esc_html__('Page Scroll Dots Tooltips Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'dots_tooltips',
                    'type'        => esc_html__('Page Scroll Dots Tooltips Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'nxt_txt',
                    'type'        => esc_html__('Page Scroll Next Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'prev_txt',
                    'type'        => esc_html__('Page Scroll Previous Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-post-search'] = [
            'conditions' => ['widgetType' => 'tp-post-search'],
            'fields' => [
                [
                    'field'       => 'search_field_placeholder',
                    'type'        => esc_html__('Post Search Search Field Placeholder', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'button_text',
                    'type'        => esc_html__('Post Search Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-pricing-list'] = [
            'conditions' => ['widgetType' => 'tp-pricing-list'],
            'fields' => [
                [
                    'field'       => 'title',
                    'type'        => esc_html__('Pricing List Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'title_tag',
                    'type'        => esc_html__('Pricing List Tag', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'price',
                    'type'        => esc_html__('Pricing List Price', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'content',
                    'type'        => esc_html__('Pricing List Description', 'theplus'),
                    'editor_type' => 'VISUAL',
                ]
            ],
        ];
		$widgets['tp-product-listout'] = [
            'conditions' => ['widgetType' => 'tp-product-listout'],
            'fields' => [
                [
                    'field'       => 'all_filter_category',
                    'type'        => esc_html__('Product Listout All Filter Category Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'load_more_btn_text',
                    'type'        => esc_html__('Product Listout Load More Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'tp_loading_text',
                    'type'        => esc_html__('Dynamic Listing Loading Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'loaded_posts_text',
                    'type'        => esc_html__('Product Listout All Posts Loaded Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'dcb_single_product',
                    'type'        => esc_html__('Product Listout Add to Cart Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-progress-bar'] = [
            'conditions' => ['widgetType' => 'tp-progress-bar'],
            'fields' => [
                [
                    'field'       => 'title',
                    'type'        => esc_html__('Progress Bar Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'sub_title',
                    'type'        => esc_html__('Progress Bar Sub Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'number',
                    'type'        => esc_html__('Progress Bar Number', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'symbol',
                    'type'        => esc_html__('Progress Bar Prefix/Postfix Symbol', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-protected-content'] = [
            'conditions' => ['widgetType' => 'tp-protected-content'],
            'fields' => [
                [
                    'field'       => 'form_input_text',
                    'type'        => esc_html__('Protected Content Input text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'form_button_text',
                    'type'        => esc_html__('Protected Content Button text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'error_message_text',
                    'type'        => esc_html__('Protected Content Error Message', 'theplus'),
                    'editor_type' => 'AREA',
                ],
				[
                    'field'       => 'protected_content_field',
                    'type'        => esc_html__('Protected Content', 'theplus'),
                    'editor_type' => 'VISUAL',
                ],
				[
                    'field'       => 'pc_message_text',
                    'type'        => esc_html__('Protected Content Text', 'theplus'),
                    'editor_type' => 'VISUAL',
                ]
            ],
        ];
		$widgets['tp-switcher'] = [
            'conditions' => ['widgetType' => 'tp-switcher'],
            'fields' => [
                [
                    'field'       => 'switch_a_title',
                    'type'        => esc_html__('Switcher Switch A Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'switch_b_title',
                    'type'        => esc_html__('Switcher Switch B Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'content_template_id',
                    'type'        => esc_html__('Switcher Switch A Template Id', 'theplus'),
                    'editor_type' => 'AREA',
                ],
				[
                    'field'       => 'content_b_template_id',
                    'type'        => esc_html__('Switcher Switch B Template Id', 'theplus'),
                    'editor_type' => 'AREA',
                ],
				[
                    'field'       => 'content_a_desc',
                    'type'        => esc_html__('Switcher Switch A Content', 'theplus'),
                    'editor_type' => 'VISUAL',
                ],
				[
                    'field'       => 'content_b_desc',
                    'type'        => esc_html__('Switcher Switch B Content', 'theplus'),
                    'editor_type' => 'VISUAL',
                ]
            ],
        ];
		$widgets['tp-team-member-listout'] = [
            'conditions' => ['widgetType' => 'tp-team-member-listout'],
            'fields' => [
                [
                    'field'       => 'all_filter_category',
                    'type'        => esc_html__('Team Member All Filter Category Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-video-player'] = [
            'conditions' => ['widgetType' => 'tp-video-player'],
            'fields' => [
                [
                    'field'       => 'video_title',
                    'type'        => esc_html__('Title of Video', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-unfold'] = [
            'conditions' => ['widgetType' => 'tp-unfold'],
            'fields' => [
                [
                    'field'       => 'content_title',
                    'type'        => esc_html__('Unfold : Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'content_description',
					'type'        => esc_html__('Unfold : Description', 'theplus'),
                    'editor_type' => 'VISUAL',
                ],
				[
                    'field'       => 'content_readmore',
                    'type'        => esc_html__('Unfold : Expand Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'content_readless',
                    'type'        => esc_html__('Unfold : Collapse Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'eb_text',
                    'type'        => esc_html__('Unfold : Extra Button Title', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		$widgets['tp-dynamic-categories'] = [
            'conditions' => ['widgetType' => 'tp-dynamic-categories'],
            'fields' => [
                [
                    'field'       => 'count_extra_text',
                    'type'        => esc_html__('Woo Categories Product Count After Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
            ],
        ];
		
		/*repeater & normal start*/
		$widgets['tp-accordion'] = [
            'conditions' => ['widgetType' => 'tp-accordion'],
			'fields'     => [],
            'integration-class' => '\TheplusAddons\WPML\Tp_Accordion',
        ];
		
		$widgets['tp-advanced-typography'] = [
            'conditions' => ['widgetType' => 'tp-advanced-typography'],
			'fields'     => [
				[
                    'field'       => 'advanced_typography_text',
                    'type'        => esc_html__('Advanced Typography Text', 'theplus'),
                    'editor_type' => 'AREA',
                ]
			],
            'integration-class' => '\TheplusAddons\WPML\Tp_Advanced_Typography',
        ];
		
		$widgets['tp-animated-service-boxes'] = [
            'conditions' => ['widgetType' => 'tp-animated-service-boxes'],
			'fields'     => [
				[
                    'field'       => 'port_mobile_text',
                    'type'        => esc_html__('Animated Service Boxes Title On Click Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
			],
            'integration-class' => '\TheplusAddons\WPML\Tp_Animated_Service_Boxes',
        ];
		
		$widgets['tp-audio-player'] = [
            'conditions' => ['widgetType' => 'tp-audio-player'],
			'fields'     => [],
            'integration-class' => '\TheplusAddons\WPML\Tp_Audio_Player',
        ];
		$widgets['tp-carousel-anything'] = [
            'conditions' => ['widgetType' => 'tp-carousel-anything'],
			'fields'     => [],
            'integration-class' => '\TheplusAddons\WPML\Tp_Carousel_Anything',
        ];
		$widgets['tp-cascading-image'] = [
            'conditions' => ['widgetType' => 'tp-cascading-image'],
			'fields'     => [],
            'integration-class' => '\TheplusAddons\WPML\Tp_Cascading_Image',
        ];
		
		$widgets['tp-circle-menu'] = [
            'conditions' => ['widgetType' => 'tp-circle-menu'],
			'fields'     => [],
            'integration-class' => '\TheplusAddons\WPML\Tp_Circle_Menu',
        ];
		
		$widgets['tp-flip-box'] = [
            'conditions' => ['widgetType' => 'tp-flip-box'],
			'fields'     => [
				[
                    'field'       => 'title',
                    'type'        => esc_html__('Flip Box : Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'content_desc',
                    'type'        => esc_html__('Flip Box : Description', 'theplus'),
                    'editor_type' => 'VISUAL',
                ],	
				[
                    'field'       => 'button_text',
                    'type'        => esc_html__('Flip Box : Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
			],
            'integration-class' => '\TheplusAddons\WPML\Tp_Flip_Box',
        ];
		
		$widgets['tp-gallery-listout'] = [
            'conditions' => ['widgetType' => 'tp-gallery-listout'],
			'fields'     => [				
				[
                    'field'       => 'style_4_button_text',
                    'type'        => esc_html__('Gallery Listout : Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'all_filter_category',
                    'type'        => esc_html__('Gallery Listout : All Filter Category Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'load_more_btn_text',
                    'type'        => esc_html__('Gallery Listout : Load More Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'loaded_posts_text',
                    'type'        => esc_html__('Gallery Listout : All Posts Loaded Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
			],
            'integration-class' => '\TheplusAddons\WPML\Tp_Gallery_Listout',
        ];
		
		$widgets['tp-google-map'] = [
            'conditions' => ['widgetType' => 'tp-google-map'],
			'fields'     => [
				
				[
                    'field'       => 'title_text',
                    'type'        => esc_html__('Google Map : Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				
				[
                    'field'       => 'overlay_content',
                    'type'        => esc_html__('Google Map : Description', 'theplus'),
                    'editor_type' => 'LINE',
                ]
			],
            'integration-class' => '\TheplusAddons\WPML\Tp_Google_Map',
        ];
		
		$widgets['tp-hotspot'] = [
            'conditions' => ['widgetType' => 'tp-hotspot'],
			'fields'     => [],
            'integration-class' => '\TheplusAddons\WPML\Tp_Hotspot',
        ];
		
		$widgets['tp-info-box'] = [
            'conditions' => ['widgetType' => 'tp-info-box'],
			'fields'     => [
				[
                    'field'       => 'title',
                    'type'        => esc_html__('Info Box : Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'button_text',
                    'type'        => esc_html__('Info Box : Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'pin_text_title',
                    'type'        => esc_html__('Info Box : Pin Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'content_desc',
                    'type'        => esc_html__('Info Box : Description', 'theplus'),
                    'editor_type' => 'VISUAL',
                ],				
				'url_link'       => [
					'field'       => 'url',
					'type'        => __( 'Info Box : Link', 'theplus' ),
					'editor_type' => 'LINK',
				],				
			],
            'integration-class' => '\TheplusAddons\WPML\Tp_Info_Box',
        ];
		
		$widgets['tp-pricing-table'] = [
            'conditions' => ['widgetType' => 'tp-pricing-table'],
			'fields'     => [
				[
                    'field'       => 'pricing_title',
                    'type'        => esc_html__('Pricing Table : Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'pricing_subtitle',
                    'type'        => esc_html__('Pricing Table : Sub Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'price_prefix',
                    'type'        => esc_html__('Pricing Table : Prefix Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'price_postfix',
                    'type'        => esc_html__('Pricing Table : Postfix Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'previous_price_prefix',
                    'type'        => esc_html__('Pricing Table : Prefix Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'previous_price_postfix',
                    'type'        => esc_html__('Pricing Table : Postfix Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'list_style_show_option',
                    'type'        => esc_html__('Pricing Table : Expand Section Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'list_style_less_option',
                    'type'        => esc_html__('Pricing Table : Shrink Section Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'button_text',
                    'type'        => esc_html__('Pricing Table : Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'content_wysiwyg',
                    'type'        => esc_html__('Pricing Table : Content', 'theplus'),
                    'editor_type' => 'VISUAL',
                ],
				[
                    'field'       => 'ribbon_pin_text',
                    'type'        => esc_html__('Pricing Table : Ribbon/Pin Text', 'theplus'),
                    'editor_type' => 'VISUAL',
                ]
			],
            'integration-class' => '\TheplusAddons\WPML\Tp_Pricing_Table',
        ];
		
		$widgets['tp-process-steps'] = [
            'conditions' => ['widgetType' => 'tp-process-steps'],
			'fields'     => [],
            'integration-class' => '\TheplusAddons\WPML\Tp_Process_Steps',
        ];
		
		$widgets['tp-scroll-navigation'] = [
            'conditions' => ['widgetType' => 'tp-scroll-navigation'],
			'fields'     => [],
            'integration-class' => '\TheplusAddons\WPML\Tp_Scroll_Navigation',
        ];
		
		$widgets['tp-social-icon'] = [
            'conditions' => ['widgetType' => 'tp-social-icon'],
			'fields'     => [],
            'integration-class' => '\TheplusAddons\WPML\Tp_Social_Icon',
        ];
		
		$widgets['tp-style-list'] = [
            'conditions' => ['widgetType' => 'tp-style-list'],
			'fields'     => [
				[
                    'field'       => 'read_show_option',
                    'type'        => esc_html__('Style List : Expand Section Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'read_less_option',
                    'type'        => esc_html__('Style List : Shrink Section Title', 'theplus'),
                    'editor_type' => 'LINE',
                ]
			],
            'integration-class' => '\TheplusAddons\WPML\Tp_Style_List',
        ];
		
		$widgets['tp-table'] = [
            'conditions' => ['widgetType' => 'tp-table'],
			'fields'     => [
				[
                    'field'       => 'searchable_label',
                    'type'        => esc_html__('Table : Search Field Label', 'theplus'),
                    'editor_type' => 'LINE',
                ]
			],
            'integration-class' => '\TheplusAddons\WPML\Tp_Table',
        ];
		
		$widgets['tp-tabs-tours'] = [
            'conditions' => ['widgetType' => 'tp-tabs-tours'],
			'fields'     => [],
            'integration-class' => '\TheplusAddons\WPML\Tp_Tabs_Tours',
        ];
		
		$widgets['tp-timeline'] = [
            'conditions' => ['widgetType' => 'tp-timeline'],
			'fields'     => [
				[
                    'field'       => 'start_pin_title',
                    'type'        => esc_html__('Timeline : Start Pin Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'end_pin_title',
                    'type'        => esc_html__('Timeline : End Pin Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'button_text',
                    'type'        => esc_html__('Timeline : Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]				
			],
            'integration-class' => '\TheplusAddons\WPML\Tp_Timeline',
        ];
		
		$widgets['tp-wp-login-register'] = [
            'conditions' => ['widgetType' => 'tp-wp-login-register'],
			'fields'     => [
				[
                    'field'       => 'dropdown_button_text',
                    'type'        => esc_html__('Login Register : Common Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'button_text',
                    'type'        => esc_html__('Login Register : Login Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'tab_com_login',
                    'type'        => esc_html__('Login Register : Login Tab Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'user_label',
                    'type'        => esc_html__('Login Register : Username Label', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'user_placeholder',
                    'type'        => esc_html__('Login Register : Username Placeholder', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'password_label',
                    'type'        => esc_html__('Login Register : Password Label', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'password_placeholder',
                    'type'        => esc_html__('Login Register : Password Placeholder', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'bottom_lost_pass_text',
                    'type'        => esc_html__('Login Register : Lost password Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'bottom_register_text',
                    'type'        => esc_html__('Login Register : Register Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'remember_me_text',
                    'type'        => esc_html__('Login Register : Remember Me Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'button_text_reg',
                    'type'        => esc_html__('Login Register : Register Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'tab_com_signup',
                    'type'        => esc_html__('Login Register : Register Tab Title', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'first_name_label',
                    'type'        => esc_html__('Login Register : First Name Label', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'first_name_placeholder',
                    'type'        => esc_html__('Login Register : First Name Placeholder', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'last_name_label',
                    'type'        => esc_html__('Login Register : Last Name Label', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'last_name_placeholder',
                    'type'        => esc_html__('Login Register : Last Name Placeholder', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'email_label',
                    'type'        => esc_html__('Login Register : Email Label', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'email_placeholder',
                    'type'        => esc_html__('Login Register : Email Placeholder', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'bottom_login_text',
                    'type'        => esc_html__('Login Register : Login Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'login_before_text',
                    'type'        => esc_html__('Login Register : Login Before Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'additional_message',
                    'type'        => esc_html__('Login Register : Additional Message', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'lost_pass_placeholder',
                    'type'        => esc_html__('Login Register : Lost Password Placeholder', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'forgot_pass_btn',
                    'type'        => esc_html__('Login Register : Lost Password Button Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'edit_profile_text',
                    'type'        => esc_html__('Login Register : Edit Pofile Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'button_text_logout',
                    'type'        => esc_html__('Login Register : Logout Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'login_msg_loading_txt',
                    'type'        => esc_html__('Login Register : Loading text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'login_msg_success',
                    'type'        => esc_html__('Login Register : Success text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'login_msg_validation',
                    'type'        => esc_html__('Login Register : Validation text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'login_msg_error',
                    'type'        => esc_html__('Login Register : Error text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'reg_msg_loading',
                    'type'        => esc_html__('Login Register : Loading text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'reg_msg_success',
                    'type'        => esc_html__('Login Register : Success text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'reg_msg_email_duplication',
                    'type'        => esc_html__('Login Register : Email Validate', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'reg_msg_error',
                    'type'        => esc_html__('Login Register : Error Text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'fp_msg_loading',
                    'type'        => esc_html__('Login Register : Loading text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'fp_msg_success',
                    'type'        => esc_html__('Login Register : Success text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'fp_msg_error',
                    'type'        => esc_html__('Login Register : Error text', 'theplus'),
                    'editor_type' => 'LINE',
                ],
				[
                    'field'       => 'modal_header_description_log',
                    'type'        => esc_html__('Login Register : Login Heading', 'theplus'),
                    'editor_type' => 'VISUAL',
                ],
				[
                    'field'       => 'modal_header_description_reg',
                    'type'        => esc_html__('Login Register : Registration Heading', 'theplus'),
                    'editor_type' => 'VISUAL',
                ],
				[
                    'field'       => 'lost_password_heading_desc',
                    'type'        => esc_html__('Login Register : Lost Password Heading', 'theplus'),
                    'editor_type' => 'VISUAL',
                ]
			],
            'integration-class' => '\TheplusAddons\WPML\Tp_Wp_Login_Register',
        ];
		
		$widgets['tp-mobile-menu'] = [
            'conditions' => ['widgetType' => 'tp-mobile-menu'],
			'fields'     => [
				[
                    'field'       => 'mm_extra_toggle_text',
                    'type'        => esc_html__('Mobile Menu : Extra Toggle Text', 'theplus'),
                    'editor_type' => 'LINE',
                ]
			],
            'integration-class' => '\TheplusAddons\WPML\Tp_Mobile_Menu',
        ];

		/*repeater & normal end*/
		
		return $widgets;
    }
	public function __construct() {
		if ( class_exists( 'WPML_Elementor_Module_With_Items' ) ) {
			$this->includes();
			add_filter('wpml_elementor_widgets_to_translate', [$this, 'plus_translate_widgets']);
		}
	}
	
	public function includes() {
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Accordion.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Advanced_Typography.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Animated_Service_Boxes.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Audio_Player.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Carousel_Anything.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Cascading_Image.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Circle_Menu.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Flip_Box.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Gallery_Listout.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Google_Map.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Hotspot.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Info_Box.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Pricing_Table.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Process_Steps.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Scroll_Navigation.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Social_Icon.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Style_List.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Table.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Tabs_Tours.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Timeline.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Wp_Login_Register.php';
		require_once THEPLUS_PATH.'modules/enqueue/WPML/Tp_Mobile_Menu.php';
	}
	
	/**
	 * Returns the instance.
	 * @since  1.0.0
	 */
	public static function get_instance( $shortcodes = array() ) {

		if ( null == self::$instance ) {
			self::$instance = new self( $shortcodes );
		}
		return self::$instance;
	}
}

/**
 * Returns instance of WPML
 */
function theplus_wpml_translate() {
	return WPML::get_instance();
}
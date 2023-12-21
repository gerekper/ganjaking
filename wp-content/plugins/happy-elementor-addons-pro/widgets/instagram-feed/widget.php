<?php
/**
 * Instagram Feed
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Utils;

defined('ABSPATH') || die();

class Instagram_Feed extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __('Instagram Feed', 'happy-addons-pro');
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-instagram';
	}

	public function get_keywords() {
		return ['instagram-feed', 'instagram', 'feed', 'social'];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__insta_feed_content_controls();
		$this->__insta_settings_content_controls();
		$this->__general_settings_content_controls();
	}

	// Instagram Feed content
	protected function __insta_feed_content_controls() {

		$this->start_controls_section(
			'_section_instagram',
			[
				'label' => __('Instagram Feed', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
            'credentials',
            [
                'label' => __('Credentials from', 'happy-addons-pro'),
                'type' => Controls_Manager::SELECT,
                'default' => 'custom',
                'options' =>  [
                    'global' => __('Global', 'happy-addons-pro'),
                    'custom' => __('Custom', 'happy-addons-pro'),
                ],
            ]
        );

        $this->add_control(
            'credentials_set_notice',
            [
                'raw' => '<strong>' . esc_html__('Note!', 'happy-addons-pro') . '</strong> ' . esc_html__('Please set credentials in Happy Addons Dashboard - ', 'happy-addons-pro') . '<a style="border-bottom-color: inherit;" href="'. esc_url(admin_url('admin.php?page=happy-addons#credentials')) . '" target="_blank" >'. esc_html__('Credentials', 'happy-addons-pro') .'</a>',
                'type' => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                'render_type' => 'ui',
                'condition' => [
                    'credentials' => 'global',
                ],
            ]
        );

		$this->add_control(
			'access_token',
			[
				'label' => __('Access Token', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => '',
                'condition' => [
                    'credentials' => 'custom',
                ],
			]
		);

		$this->add_control(
			'important_note',
			[
				'label' => __( 'Important Note', 'happy-addons-pro' ),
				'show_label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<a href="https://developers.facebook.com/docs/instagram-basic-display-api/getting-started" target="_blank">Get Access Token</a>',
				'content_classes' => 'your-class',
                'condition' => [
                    'credentials' => 'custom',
                ],
			]
		);

		$this->add_control(
			'cache_headding',
			[
				'label' => __( 'Data Cache Settings', 'happy-addons-pro' ),
				'label_block' => true,
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cache_time',
			[
				'label' => __('Data Cache Time', 'happy-addons-pro'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'default' => 1,
				'placeholder' => '1',
			]
		);

		$this->add_control(
			'cache_note',
			[
				'label' => __( 'Cache Related Nonte', 'happy-addons-pro'),
				'show_label' => false,
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( "Default value is 1. Set 0 to remove cache immediately.", 'happy-addons-pro' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
			]
		);

		$this->add_control(
			'cache_time_type',
			[
				'label' => __('Data Cache Time Type', 'happy-addons-pro'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'minute' => __('Minute', 'happy-addons-pro'),
					'hour' => __('Hour', 'happy-addons-pro'),
					'day' => __('Day', 'happy-addons-pro'),
					'week' => __('Week', 'happy-addons-pro'),
					'month' => __('Month', 'happy-addons-pro'),
					'year' => __('Year', 'happy-addons-pro'),
				],
				'default' => 'day',
			]
		);

		$this->end_controls_section();
	}

	// Instagram Settings content
	protected function __insta_settings_content_controls() {

		$this->start_controls_section(
			'_section_instagram_settings',
			[
				'label' => __('Instagram Settings', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'sort_by',
			[
				'label' => __('Sort By', 'happy-addons-pro'),
				'type' => Controls_Manager::SELECT,
				'default' => 'recent-posts',
				'options' => [
					'recent-posts' => __('Recent Posts', 'happy-addons-pro'),
					'old-posts' => __('Old Posts', 'happy-addons-pro'),
				],
			]
		);

		$this->add_control(
			'instagram_item',
			[
				'label' => __('Image Items', 'happy-addons-pro'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 12,
                'style_transfer' => true,
			]
		);

		$this->end_controls_section();

	}

	// General Settings content
	protected function __general_settings_content_controls() {

		$this->start_controls_section(
			'_section_instagram_general_settings',
			[
				'label' => __('General Settings', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'view_style',
			[
				'label' => __('View Style', 'happy-addons-pro'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'ha-hover-info',
				'options' => [
					'ha-hover-info' => __('Hover Info', 'happy-addons-pro'),
					'ha-hover-push' => __('Hover Push', 'happy-addons-pro'),
					'ha-feed-view' => __('Feed View', 'happy-addons-pro'),
				],
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'grid',
			[
				'label' => __('Grid Number', 'happy-addons-pro'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'ha-grid-4',
				'options' => [
					'ha-grid-3' => __('Grid 3', 'happy-addons-pro'),
					'ha-grid-4' => __('Grid 4', 'happy-addons-pro'),
					'ha-grid-5' => __('Grid 5', 'happy-addons-pro'),
					'ha-grid-6' => __('Grid 6', 'happy-addons-pro'),
					'ha-grid-7' => __('Grid 7', 'happy-addons-pro'),
					'ha-grid-8' => __('Grid 8', 'happy-addons-pro'),
				],
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_caption',
			[
				'label' => __('Show Caption?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_link',
			[
				'label' => __('Enable Image Link?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'view_style!' => 'ha-feed-view',
				],
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'link_target',
			[
				'label' => __('Open in new tab?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => '_blank',
				'default' => '_blank',
				'condition' => [
					'view_style!' => 'ha-feed-view',
					'show_link' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_btn',
			[
				'label' => __('Title Bar?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'title_btn_text',
			[
				'label' => __('Title Bar Text', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Instagram', 'happy-addons-pro'),
				'condition' => [
					'title_btn' => 'yes',
				],
			]
		);

		$this->add_control(
			'load_more',
			[
				'label' => __('Load More Button?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'load_more_text',
			[
				'label' => __('Load More Text', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'default' => 'Load More',
				'condition' => [
					'load_more' => 'yes',
				],
			]
		);

		$this->add_control(
			'_heading_user_info',
			[
				'label' => __( 'User Info', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'view_style' => 'ha-feed-view',
				],
			]
		);

		$this->add_control(
			'show_user_picture',
			[
				'label' => __('Show Profile Picture?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'view_style' => 'ha-feed-view',
				],
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'profile_image',
			[
				'label' => __( 'Profile Image', 'happy-addons-pro' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'view_style' => 'ha-feed-view',
					'show_user_picture' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_username',
			[
				'label' => __('Show Username?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'view_style' => 'ha-feed-view',
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'user_name',
			[
				'label' => __('Display Name', 'happy-addons-pro'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Happy Addons', 'happy-addons-pro'),
				'condition' => [
					'view_style' => 'ha-feed-view',
					'show_username' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_user_postdate',
			[
				'label' => __('Show Post Date?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'view_style' => 'ha-feed-view',
				],
                'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_user_insta_icon',
			[
				'label' => __('Show Insta Icon?', 'happy-addons-pro'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'view_style' => 'ha-feed-view',
				],
                'style_transfer' => true,
			]
		);

		$this->end_controls_section();
	}


	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__instagram_style_controls();
		$this->__feed_view_style_controls();
		$this->__title_bar_style_controls();
		$this->__load_more_button_style_controls();
	}

	// Instagram styles
	protected function __instagram_style_controls() {

		$this->start_controls_section(
			'_section_instagram_style',
			[
				'label' => __('Instagram', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'space_between_item',
			[
				'label' => __( 'Space Between Item', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'selectors' => [
					'(desktop){{WRAPPER}} .ha-insta-item' => 'margin: {{SIZE}}{{UNIT}};',
					'(tablet){{WRAPPER}} .ha-insta-item' => 'margin: {{space_between_item_tablet.SIZE || 5}}{{UNIT}};',
					'(mobile){{WRAPPER}} .ha-insta-item' => 'margin: {{space_between_item_mobile.SIZE || 5}}{{UNIT}};',
					//Desktop
					'(desktop){{WRAPPER}} .ha-insta-default.ha-grid-3 .ha-insta-item' => 'max-width: calc(33.33% - ({{SIZE}}{{UNIT}}*2));flex: 0 0 calc(33.33% - ({{SIZE}}{{UNIT}}*2));',
					'(desktop){{WRAPPER}} .ha-insta-default.ha-grid-4 .ha-insta-item' => 'max-width: calc(25% - ({{SIZE}}{{UNIT}}*2));flex: 0 0 calc(25% - ({{SIZE}}{{UNIT}}*2));',
					'(desktop){{WRAPPER}} .ha-insta-default.ha-grid-5 .ha-insta-item' => 'max-width: calc(20% - ({{SIZE}}{{UNIT}}*2));flex: 0 0 calc(20% - ({{SIZE}}{{UNIT}}*2));',
					'(desktop){{WRAPPER}} .ha-insta-default.ha-grid-6 .ha-insta-item' => 'max-width: calc(16.66% - ({{SIZE}}{{UNIT}}*2));flex: 0 0 calc(16.66% - ({{SIZE}}{{UNIT}}*2));',
					'(desktop){{WRAPPER}} .ha-insta-default.ha-grid-7 .ha-insta-item' => 'max-width: calc(14.2857% - ({{SIZE}}{{UNIT}}*2));flex: 0 0 calc(14.2857% - ({{SIZE}}{{UNIT}}*2));',
					'(desktop){{WRAPPER}} .ha-insta-default.ha-grid-8 .ha-insta-item' => 'max-width: calc(12.5% - ({{SIZE}}{{UNIT}}*2));flex: 0 0 calc(12.5% - ({{SIZE}}{{UNIT}}*2));',
					//Tablet
					'(tablet){{WRAPPER}} .ha-insta-default.ha-grid-3 .ha-insta-item' => 'max-width: calc(33.33% - ({{space_between_item_tablet.SIZE || 5}}{{UNIT}}*2));flex: 0 0 calc(33.33% - ({{space_between_item_tablet.SIZE || 5}}{{UNIT}}*2));',
					'(tablet){{WRAPPER}} .ha-insta-default.ha-grid-4 .ha-insta-item' => 'max-width: calc(33.33% - ({{space_between_item_tablet.SIZE || 5}}{{UNIT}}*2));flex: 0 0 calc(33.33% - ({{space_between_item_tablet.SIZE || 5}}{{UNIT}}*2));',
					'(tablet){{WRAPPER}} .ha-insta-default.ha-grid-5 .ha-insta-item' => 'max-width: calc(33.33% - ({{space_between_item_tablet.SIZE || 5}}{{UNIT}}*2));flex: 0 0 calc(33.33% - ({{space_between_item_tablet.SIZE || 5}}{{UNIT}}*2));',
					'(tablet){{WRAPPER}} .ha-insta-default.ha-grid-6 .ha-insta-item' => 'max-width: calc(33.33% - ({{space_between_item_tablet.SIZE || 5}}{{UNIT}}*2));flex: 0 0 calc(33.33% - ({{space_between_item_tablet.SIZE || 5}}{{UNIT}}*2));',
					'(tablet){{WRAPPER}} .ha-insta-default.ha-grid-7 .ha-insta-item' => 'max-width: calc(33.33% - ({{space_between_item_tablet.SIZE || 5}}{{UNIT}}*2));flex: 0 0 calc(33.33% - ({{space_between_item_tablet.SIZE || 5}}{{UNIT}}*2));',
					'(tablet){{WRAPPER}} .ha-insta-default.ha-grid-8 .ha-insta-item' => 'max-width: calc(33.33% - ({{space_between_item_tablet.SIZE || 5}}{{UNIT}}*2));flex: 0 0 calc(33.33% - ({{space_between_item_tablet.SIZE || 5}}{{UNIT}}*2));',
					//Mobile
					'(mobile){{WRAPPER}} .ha-insta-default.ha-grid-3 .ha-insta-item' => 'max-width: calc(100% - ({{space_between_item_mobile.SIZE || 5}}{{UNIT}}*2));flex: 0 0 calc(100% - ({{space_between_item_mobile.SIZE || 5}}{{UNIT}}*2));',
					'(mobile){{WRAPPER}} .ha-insta-default.ha-grid-4 .ha-insta-item' => 'max-width: calc(100% - ({{space_between_item_mobile.SIZE || 5}}{{UNIT}}*2));flex: 0 0 calc(100% - ({{space_between_item_mobile.SIZE || 5}}{{UNIT}}*2));',
					'(mobile){{WRAPPER}} .ha-insta-default.ha-grid-5 .ha-insta-item' => 'max-width: calc(100% - ({{space_between_item_mobile.SIZE || 5}}{{UNIT}}*2));flex: 0 0 calc(100% - ({{space_between_item_mobile.SIZE || 5}}{{UNIT}}*2));',
					'(mobile){{WRAPPER}} .ha-insta-default.ha-grid-6 .ha-insta-item' => 'max-width: calc(100% - ({{space_between_item_mobile.SIZE || 5}}{{UNIT}}*2));flex: 0 0 calc(100% - ({{space_between_item_mobile.SIZE || 5}}{{UNIT}}*2));',
					'(mobile){{WRAPPER}} .ha-insta-default.ha-grid-7 .ha-insta-item' => 'max-width: calc(100% - ({{space_between_item_mobile.SIZE || 5}}{{UNIT}}*2));flex: 0 0 calc(100% - ({{space_between_item_mobile.SIZE || 5}}{{UNIT}}*2));',
					'(mobile){{WRAPPER}} .ha-insta-default.ha-grid-8 .ha-insta-item' => 'max-width: calc(100% - ({{space_between_item_mobile.SIZE || 5}}{{UNIT}}*2));flex: 0 0 calc(100% - ({{space_between_item_mobile.SIZE || 5}}{{UNIT}}*2));',
				],
			]
		);

		$this->add_control(
			'item_box_bg',
			[
				'label' => __('Background', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-insta-item' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_box_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-insta-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'item_box_border',
				'label' => __('Border', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-insta-item',
			]
		);

		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'caption_color',
			[
				'label' => esc_html__('Caption Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-insta-caption p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_caption' => 'yes',
				],
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label' => esc_html__('Hover Overlay Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-hover-info .ha-insta-item:before' => 'background: {{VALUE}};',
				],
				'condition' => [
					'view_style' => 'ha-hover-info',
				],
			]
		);

		$this->add_control(
			'comment_box_bg',
			[
				'label' => esc_html__('Caption Box Background', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-hover-push .ha-insta-item .ha-insta-likes-comments' => 'background: {{VALUE}};',
				],
				'condition' => [
					'view_style' => 'ha-hover-push',
				],
			]
		);

		$this->end_controls_section();
	}

	// Feed View styles
	protected function __feed_view_style_controls() {

		$this->start_controls_section(
			'_section_instagram_feed_view_style',
			[
				'label' => __('Feed View', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'view_style' => 'ha-feed-view',
				],
			]
		);

		$this->add_control(
			'user_name_color',
			[
				'label' => esc_html__('User Name Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-feed-view .ha-insta-user-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'post_date_color',
			[
				'label' => esc_html__('Post Date Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-feed-view .ha-insta-postdate' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'instagram_icon_color',
			[
				'label' => esc_html__('Instagram Icon Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-feed-view .ha-insta-user-info .ha-insta-feed-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'instagram_icon_hover_color',
			[
				'label' => esc_html__('Instagram Icon Hover Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-feed-view .ha-insta-user-info .ha-insta-feed-icon:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	// Title Bar styles
	protected function __title_bar_style_controls() {

		//Title Bar Style Section Start
		$this->start_controls_section(
			'_section_instagram_title_button_style',
			[
				'label' => __('Title Bar', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'title_btn' => 'yes',
					'title_btn_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_button_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .ha-insta-title',
			]
		);

		$this->add_control(
			'title_button_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-insta-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'title_button_bg',
			[
				'label' => __('Background', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-insta-title' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'title_button_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-insta-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	// Load More Button styles
	protected function __load_more_button_style_controls() {

		$this->start_controls_section(
			'_section_instagram_load_more_button_style',
			[
				'label' => __('Load More Button', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'load_more' => 'yes',
					'load_more_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .ha-insta-load-more',
			]
		);

		$this->start_controls_tabs(
			'button_tabs'
		);
		$this->start_controls_tab(
			'button_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);
		$this->add_control(
			'button_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-insta-load-more' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-insta-load-more',
			]
		);
		$this->end_controls_tab();//Button Normal Tab END

		$this->start_controls_tab(
			'button_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);
		$this->add_control(
			'button_hover_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-insta-load-more:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_hover_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-insta-load-more:hover',
			]
		);
		$this->end_controls_tab(); //Button Hover Tab END
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-insta-load-more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-insta-load-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label' => __( 'Margin', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-insta-load-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}


	protected function instagram_get_b64_icon() {
		return 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOC4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB3aWR0aD0iMzJweCIgaGVpZ2h0PSIzMnB4IiB2aWV3Qm94PSIwIDAgMzIgMzIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDMyIDMyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8c3R5bGUgdHlwZT0idGV4dC9jc3MiPg0KCS5zdDB7ZGlzcGxheTpub25lO2ZpbGw6bm9uZTtzdHJva2U6IzAwMDAwMDtzdHJva2Utd2lkdGg6MjtzdHJva2UtbGluZWpvaW46cm91bmQ7c3Ryb2tlLW1pdGVybGltaXQ6MTA7fQ0KCS5zdDF7ZGlzcGxheTpub25lO30NCjwvc3R5bGU+DQo8cGF0aCBkPSJNMTYsMzJDNy4yLDMyLDAsMjQuOCwwLDE2UzcuMiwwLDE2LDBzMTYsNy4yLDE2LDE2YzAsMi40LTAuNSw0LjgtMS42LDdsMS42LDYuN2MwLjEsMC4zLDAsMC43LTAuMywwLjkNCgljLTAuMiwwLjItMC42LDAuMy0wLjksMC4zbC02LjMtMS40QzIyLDMxLjIsMTksMzIsMTYsMzJ6IE0xNiwyQzguMywyLDIsOC4zLDIsMTZzNi4zLDE0LDE0LDE0YzIuOCwwLDUuNS0wLjgsNy44LTIuNA0KCWMwLjItMC4yLDAuNS0wLjIsMC44LTAuMWw1LjEsMS4xbC0xLjMtNS41Yy0wLjEtMC4yLDAtMC41LDAuMS0wLjdjMS0yLDEuNS00LjEsMS41LTYuNEMzMCw4LjMsMjMuNywyLDE2LDJ6Ii8+DQo8cGF0aCBjbGFzcz0ic3QwIiBkPSJNLTY3LDkwLjdjMy40LTMuNSwzLjQtOS4yLTAuMS0xMi43bDAsMGMtMy41LTMuNS05LjItMy41LTEyLjctMC4xYy0zLjUtMy40LTkuMi0zLjQtMTIuNywwLjFsMCwwDQoJYy0zLjUsMy41LTMuNSw5LjItMC4xLDEyLjdsMCwwbDAuMSwwLjFsMTEuMywxMS4zYzAuOCwwLjgsMiwwLjgsMi44LDBsMTEuMy0xMS4zTC02Nyw5MC43TC02Nyw5MC43eiIvPg0KPHBhdGggY2xhc3M9InN0MSIgZD0iTTIzLDMySDljLTUsMC05LTQtOS05VjljMC01LDQtOSw5LTloMTRjNSwwLDksNCw5LDl2MTRDMzIsMjgsMjgsMzIsMjMsMzJ6IE05LDJDNS4xLDIsMiw1LjEsMiw5djE0DQoJYzAsMy45LDMuMSw3LDcsN2gxNGMzLjksMCw3LTMuMSw3LTdWOWMwLTMuOS0zLjEtNy03LTdIOXoiLz4NCjxwYXRoIGNsYXNzPSJzdDEiIGQ9Ik0xNiwyNC4yYy00LjUsMC04LjItMy43LTguMi04LjJjMC00LjUsMy43LTguMiw4LjItOC4yYzQuNSwwLDguMiwzLjcsOC4yLDguMkMyNC4yLDIwLjUsMjAuNSwyNC4yLDE2LDI0LjJ6DQoJIE0xNiw5LjhjLTMuNCwwLTYuMiwyLjgtNi4yLDYuMnMyLjgsNi4yLDYuMiw2LjJzNi4yLTIuOCw2LjItNi4yUzE5LjQsOS44LDE2LDkuOHoiLz4NCjxjaXJjbGUgY2xhc3M9InN0MSIgY3g9IjE2IiBjeT0iMTYiIHI9IjEuOSIvPg0KPC9zdmc+DQo=';
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('wrapper', 'class', 'ha-instagram-wrapper');
		?>
		<div <?php $this->get_render_attribute_string('wrapper');?>>
			<?php $this->instafeed_render(); ?>
		</div>
		<?php
	}

	protected function instafeed_render() {

		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('ha-grid', 'class', ['ha-insta-default',$settings['grid'],$settings['view_style']]);
		$this->add_render_attribute('instagram-item', 'class', ['ha-insta-item']);
		//old key = cash_time
		//new key = cache_time
		$cache_time = 0 === $settings['cache_time'] || !empty($settings['cache_time']) ? $settings['cache_time'] : 1;
		//old key = cash_time_type
		//new key = cache_time_type
		$cache_time_type = $this->get_cacheing_time_type($settings['cache_time_type']);
		// var_dump($cache_time);
		$item_tag = 'div';
		if('yes' == $settings['show_link']){
			$item_tag = 'a';
			$this->add_render_attribute('instagram-item', 'target', $settings['link_target']);
		}
		$widget_id = $this->get_id();
		// $user_id = $settings['user_id'];
		if( 'global' == $settings['credentials'] && is_array( ha_get_credentials('instagram') ) ){
			$credentials = ha_get_credentials('instagram');
			$access_token = $credentials['access_token'];
		}else {
			$access_token = $settings['access_token'];
		}
		$transient_key = 'happy_insta_feed_data' . $widget_id . str_replace('.', '_', $access_token);
		// $transient_key = 'happy_insta_feed_data' . str_replace('.', '_', $user_id).str_replace('.', '_', $access_token);
		$transient_key = substr($transient_key,0,170); //Transient Key Must be 172 characters or fewer in length.
		if ( '0' == $cache_time ) {
			// echo 'Cache Delete';
			delete_transient($transient_key);
			$cache_time = 1;
			$cache_time_type = MINUTE_IN_SECONDS;
		}

		$messages = array();
		$instagram_data = get_transient($transient_key);
		if (false === $instagram_data && $access_token ) {
			$url = 'https://graph.instagram.com/me/media/?fields=caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username&limit=100&access_token='.esc_html($access_token);
			// $url = 'https://graph.instagram.com/'.esc_html($user_id).'/media?fields=caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username&limit=100&access_token='.esc_html($access_token);
			$instagram_data = wp_remote_retrieve_body(wp_remote_get($url));
			// echo 'Data cache';
			set_transient($transient_key, $instagram_data, $cache_time * $cache_time_type); //HOUR_IN_SECONDS
		}
		$instagram_data = apply_filters( 'happy_instagram_data', $instagram_data, $transient_key, $access_token, $widget_id );
		$instagram_data = json_decode($instagram_data, true);
		// code 100 = ID dose not exist.
		// code 190 = invalid access token or session expire
		if ( empty($access_token) ) {
			$messages['invalid_token_id'] = __('Please Input Access token', 'happy-addons-pro');
		}elseif ( empty($access_token) || empty($instagram_data) ) {
			$messages['invalid_token_id'] = __('Please Input valid Access token', 'happy-addons-pro');
		} elseif (array_key_exists("error", $instagram_data)) {
			$messages['invalid_token'] = $instagram_data['error']['message'];
		} elseif (empty($instagram_data['data'])) {
			$messages['data_empty'] = __('Whoops! It seems like this account has not created any post yet. Please, make some post on Instagram.', 'happy-addons-pro');
		} elseif (empty($settings['instagram_item'])) {
			$messages['item_empty'] = __('Must set how many items want to show.', 'happy-addons-pro');
		} elseif ($settings['instagram_item'] > count($instagram_data['data'])) {
			$messages['item_empty'] = __('The image number is more than the total post\'s number of instagram. Please set it less or equal to total post\'s number.', 'happy-addons-pro');
		}
		if (!empty($messages)) {
			foreach ($messages as $key => $message) {
				printf('<span class="ha-insta-error-message">%1$s</span>', esc_html($message));
			}
			return;
		}
		$total_data = count($instagram_data['data']);
		$query_settings = [
			'access_token' => $access_token,
			// 'user_id' => $settings['user_id'],
			'widget_id' => $widget_id,
			'view_style' => $settings['view_style'],
			'instagram_item' => $settings['instagram_item'],
			'sort_by' => $settings['sort_by'],
			'show_caption' => $settings['show_caption'],
			'show_link' => $settings['show_link'],
			'link_target' => $settings['link_target'],
			'show_user_picture' => $settings['show_user_picture'],
			'profile_image' => isset($settings['profile_image']['url'])? $settings['profile_image']['url']: '',
			'show_username' => $settings['show_username'],
			'user_name' => $settings['user_name'],
			'show_user_postdate' => $settings['show_user_postdate'],
			'show_user_insta_icon' => $settings['show_user_insta_icon'],
		];
		$query_settings = json_encode($query_settings, true);
		switch ($settings['sort_by']) {
			case 'old-posts':
				usort($instagram_data['data'], function ($a,$b){
					if ( strtotime($a['timestamp']) == strtotime($b['timestamp']) ) return 0;
					return ( strtotime($a['timestamp']) < strtotime($b['timestamp']) )? -1 : 1 ;
				});
				break;
			default:
				$instagram_data['data'];
		}

		$instagram_data = array_splice($instagram_data['data'], 0 , $settings['instagram_item']);

		?>
		<div <?php $this->print_render_attribute_string('ha-grid'); ?>>
			<?php if('yes' == $settings['title_btn'] && $settings['title_btn_text']):?>
				<div class="ha-insta-title"><i class="fa fa-instagram"></i><?php echo esc_html($settings['title_btn_text']);?></div>
			<?php endif;?>
		<!-- Hover Info Start-->
			<?php if ('ha-hover-info' == $settings['view_style']): ?>
				<?php foreach ($instagram_data as $key => $single): ?>
					<?php if('yes' == $settings['show_link']){$this->set_render_attribute('instagram-item', 'href', esc_url($single['permalink']));} ?>
					<<?php echo ha_escape_tags($item_tag).' '.$this->get_render_attribute_string('instagram-item');?>>
						<?php $image_src = ($single['media_type'] == 'VIDEO') ? $single['thumbnail_url'] : $single['media_url']; ?>
						<img src="<?php echo esc_url($image_src); ?>" alt="">
						<div class="ha-insta-content">
							<?php if( 'yes' == $settings['show_caption'] && !empty($single['caption']) ):?>
							<div class="ha-insta-caption">
								<p><?php echo esc_html($single['caption']);?></p>
							</div>
							<?php endif;?>
						</div>
					</<?php echo ha_escape_tags($item_tag);?>><!-- Item wrap End-->
				<?php endforeach; ?>
			<?php endif; ?>
		<!-- Hover Push Start-->
			<?php if ('ha-hover-push' == $settings['view_style']): ?>
				<?php foreach ($instagram_data as $key => $single): ?>
					<?php if('yes' == $settings['show_link']){$this->set_render_attribute('instagram-item', 'href', esc_url($single['permalink']));} ?>
					<<?php echo ha_escape_tags($item_tag).' '.$this->get_render_attribute_string('instagram-item');?>>
						<?php $image_src = ($single['media_type'] == 'VIDEO') ? $single['thumbnail_url'] : $single['media_url']; ?>
						<img src="<?php echo esc_url($image_src); ?>" alt="">
						<div class="ha-insta-likes-comments">
							<?php if( 'yes' == $settings['show_caption'] && !empty($single['caption']) ):?>
								<div class="ha-insta-caption">
									<p><?php echo esc_html($single['caption']);?></p>
								</div>
							<?php endif;?>
						</div>
					</<?php echo ha_escape_tags($item_tag);?>>
				<?php endforeach; ?>
			<?php endif; ?>
		<!-- Feed View Start-->
			<?php if ('ha-feed-view' == $settings['view_style']): ?>
				<?php foreach ($instagram_data as $key => $single): ?>
					<div <?php echo $this->get_render_attribute_string('instagram-item');?>>
						<?php if('yes' == $settings['show_user_picture'] || 'yes' == $settings['show_username'] || 'yes' == $settings['show_user_postdate'] || 'yes' == $settings['show_user_insta_icon']):?>
							<div class="ha-insta-user-info">
							<?php if('yes' == $settings['show_user_picture'] || 'yes' == $settings['show_username'] || 'yes' == $settings['show_user_postdate']):?>
							<a class="ha-insta-user" href="<?php echo esc_url('https://www.instagram.com/'.$single['username']);?>" target="_blank">
								<?php if('yes' == $settings['show_user_picture'] && !empty($settings['profile_image']['url'])):?>
									<div class="ha-insta-user-profile-picture">
										<img src="<?php echo esc_url($settings['profile_image']['url']);?>" alt="">
									</div>
								<?php endif;?>
								<div class="ha-insta-username-and-postdate">
									<?php if('yes' == $settings['show_username'] && !empty($settings['user_name']) ):?>
										<span class="ha-insta-user-name"><?php echo esc_html($settings['user_name'])?></span>
									<?php endif;?>
									<?php if('yes' == $settings['show_user_postdate']):?>
										<span class="ha-insta-postdate"><?php echo esc_html(date("M d Y", strtotime($single['timestamp'])));?></span>
									<?php endif;?>
								</div>
							</a>
							<?php endif;?>
							<?php if('yes' == $settings['show_user_insta_icon']):?>
								<a class="ha-insta-feed-icon" href="<?php echo esc_url($single['permalink']);?>" target="_blank">
									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="Layer_1" x="0px" y="0px" width="32px" height="32px" viewBox="0 0 32 32" style="enable-background:new 0 0 32 32;" xml:space="preserve"><path d="M23,32H9c-5,0-9-4-9-9V9c0-5,4-9,9-9h14c5,0,9,4,9,9v14C32,28,28,32,23,32z M9,2C5.1,2,2,5.1,2,9v14c0,3.9,3.1,7,7,7h14  c3.9,0,7-3.1,7-7V9c0-3.9-3.1-7-7-7H9z"></path><path d="M16,24.2c-4.5,0-8.2-3.7-8.2-8.2c0-4.5,3.7-8.2,8.2-8.2c4.5,0,8.2,3.7,8.2,8.2C24.2,20.5,20.5,24.2,16,24.2z M16,9.8  c-3.4,0-6.2,2.8-6.2,6.2s2.8,6.2,6.2,6.2s6.2-2.8,6.2-6.2S19.4,9.8,16,9.8z"></path><circle cx="16" cy="16" r="1.9"></circle></svg>
								</a>
							<?php endif;?>
						</div>
						<?php endif;?>
						<a class="ha-insta-image" href="<?php echo esc_url($single['permalink']);?>" target="_blank">
							<?php $image_src = ($single['media_type'] == 'VIDEO') ? $single['thumbnail_url'] : $single['media_url']; ?>
							<img src="<?php echo esc_url($image_src); ?>" alt="">
						</a>
						<?php if( ( 'yes' == $settings['show_caption']) && !empty($single['caption']) ):?>
							<div class="ha-insta-content">
									<div class="ha-insta-caption">
										<p><?php echo esc_html($single['caption']);?></p>
									</div>
							</div>
						<?php endif;?>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<?php if($total_data > $settings['instagram_item'] && 'yes' == $settings['load_more'] && $settings['load_more_text']):?>
			<div class="ha-insta-load-more-wrap">
				<button class="ha-insta-load-more" data-settings="<?php echo esc_attr($query_settings);?>" data-total="<?php echo esc_attr($total_data);?>"><?php echo esc_html($settings['load_more_text']);?></button>
			</div>
		<?php endif;?>
		<?php

	}

	protected function get_cacheing_time_type($type){
		switch ( $type ) {
			case 'minute':
				$cache_time_type = MINUTE_IN_SECONDS;
				break;
			case 'hour':
				$cache_time_type = HOUR_IN_SECONDS;
				break;
			case 'day':
				$cache_time_type = DAY_IN_SECONDS;
				break;
			case 'week':
				$cache_time_type = WEEK_IN_SECONDS;
				break;
			case 'month':
				$cache_time_type = MONTH_IN_SECONDS;
				break;
			case 'year':
				$cache_time_type = YEAR_IN_SECONDS;
				break;
			default:
				$cache_time_type = DAY_IN_SECONDS;
		}
		return $cache_time_type;
	}

}

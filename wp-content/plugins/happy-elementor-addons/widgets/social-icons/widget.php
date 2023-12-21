<?php
/**
 * Social Icons widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined('ABSPATH') || die();

class Social_Icons extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __('Social Icons', 'happy-elementor-addons');
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
		return 'hm hm-bond2';
	}

	public function get_keywords() {
		return ['social', 'icons', 'media', 'facebook', 'fb', 'twitter', 'linkedin'];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {

		$this->start_controls_section(
			'_section_ha_social_icons_contents',
			[
				'label' => __('Icon', 'happy-elementor-addons'),
				'tab'   => Controls_Manager::TAB_CONTENT
			]
		);
		$repeater = new Repeater();

		$repeater->add_control(
			'ha_social_icon',
			[
				'label'       => __('Icon', 'happy-elementor-addons'),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'default'     => [
					'value'   => 'fab fa-wordpress',
					'library' => 'fa-brands',
				],
				'skin' => 'inline',
				'exclude_inline_options' => ['svg'],
				'recommended' => [
					'fa-brands' => [
						'android',
						'apple',
						'behance',
						'bitbucket',
						'codepen',
						'delicious',
						'deviantart',
						'digg',
						'dribbble',
						'happy-elementor-addons',
						'facebook',
						'flickr',
						'foursquare',
						'free-code-camp',
						'github',
						'gitlab',
						'globe',
						'google-plus',
						'houzz',
						'instagram',
						'jsfiddle',
						'linkedin',
						'medium',
						'meetup',
						'mixcloud',
						'odnoklassniki',
						'pinterest',
						'product-hunt',
						'reddit',
						'shopping-cart',
						'skype',
						'slideshare',
						'snapchat',
						'soundcloud',
						'spotify',
						'stack-overflow',
						'steam',
						'stumbleupon',
						'telegram',
						'thumb-tack',
						'tripadvisor',
						'tumblr',
						'twitch',
						'twitter',
						'viber',
						'vimeo',
						'vk',
						'weibo',
						'weixin',
						'whatsapp',
						'wordpress',
						'xing',
						'yelp',
						'youtube',
						'500px',
					],
				],
			]
		);

		$repeater->add_control(
			'ha_social_link',
			[
				'label'       => __('Link', 'happy-elementor-addons'),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'default' => [
					'url' => '#'
				],
				'placeholder' => __('https://your-social-link.com', 'happy-elementor-addons'),
			]
		);


		// repeater icon text field
		$repeater->add_control(
			'ha_enable_text',
			[
				'label'          => __('Enable Text', 'happy-elementor-addons'),
				'type'           => Controls_Manager::SWITCHER,
				'label_on'       => __('Yes', 'happy-elementor-addons'),
				'label_off'      => __('No', 'happy-elementor-addons'),
				'return_value'   => 'yes',
				'style_transfer' => true,
				'separator'      => 'before'
			]
		);

		$repeater->add_control(
			'ha_social_icon_title',
			[
				'label'     => __('Social Name', 'happy-elementor-addons'),
				'type'      => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'condition' => [
					'ha_enable_text' => 'yes'
				],
			]
		);

		$repeater->add_control(
			'customize',
			[
				'label'          => __('Want To Customize?', 'happy-elementor-addons'),
				'type'           => Controls_Manager::SWITCHER,
				'label_on'       => __('Yes', 'happy-elementor-addons'),
				'label_off'      => __('No', 'happy-elementor-addons'),
				'return_value'   => 'yes',
				'style_transfer' => true,
				'separator'      => 'before'
			]
		);

		$repeater->start_controls_tabs(
			'_tab_social_icon_colors',
			[
				'condition' => ['customize' => 'yes']
			]
		);
		$repeater->start_controls_tab(
			'_tab_ha_social_icon_normal',
			[
				'label' => __('Normal', 'happy-elementor-addons'),
			]
		);

		$repeater->add_control(
			'ha_social_icon_color',
			[
				'label' => __('Color', 'happy-elementor-addons'),
				'type'  => Controls_Manager::COLOR,

				'selectors'      => [
					'{{WRAPPER}} .ha-social-icons-wrapper > {{CURRENT_ITEM}}.ha-social-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-social-icons-wrapper > {{CURRENT_ITEM}}.ha-social-icon svg' => 'fill: {{VALUE}};',
				],
				'condition'      => ['customize' => 'yes'],
				'style_transfer' => true,
			]
		);
		$repeater->add_control(
			'ha_social_icon_bg_color',
			[
				'label' => __('Background Color', 'happy-elementor-addons'),
				'type'  => Controls_Manager::COLOR,

				'selectors'      => [
					'{{WRAPPER}} .ha-social-icons-wrapper > {{CURRENT_ITEM}}.ha-social-icon' => 'background-color: {{VALUE}};',
				],
				'condition'      => ['customize' => 'yes'],
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'social_icon_border_color',
			[
				'label'          => __('Border Color', 'happy-elementor-addons'),
				'type'           => Controls_Manager::COLOR,
				'condition'      => ['customize' => 'yes'],
				'style_transfer' => true,
				'selectors'      => [
					'{{WRAPPER}} .ha-social-icons-wrapper {{CURRENT_ITEM}}' => 'border-color: {{VALUE}};',
				]
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'_tab_social_icon_hover',
			[
				'label' => __('Hover', 'happy-elementor-addons'),
			]
		);

		$repeater->add_control(
			'ha_social_icon_hover_color',
			[
				'label'          => __('Color', 'happy-elementor-addons'),
				'type'           => Controls_Manager::COLOR,
				'selectors'      => [
					'{{WRAPPER}} .ha-social-icons-wrapper > {{CURRENT_ITEM}}.ha-social-icon:hover'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-social-icons-wrapper > {{CURRENT_ITEM}}.ha-social-icon:hover svg'     => 'fill: {{VALUE}};',
				],
				'condition'      => ['customize' => 'yes'],
				'style_transfer' => true,
			]
		);
		$repeater->add_control(
			'ha_social_icon_hover_bg_color',
			[
				'label'          => __('Background Color', 'happy-elementor-addons'),
				'type'           => Controls_Manager::COLOR,
				'selectors'      => [
					'{{WRAPPER}} .ha-social-icons-wrapper > {{CURRENT_ITEM}}.ha-social-icon:hover' => 'background-color: {{VALUE}};',
				],
				'condition'      => ['customize' => 'yes'],
				'style_transfer' => true,
			]
		);
		$repeater->add_control(
			'social_icon_hover_border_color',
			[
				'label'          => __('Border Color', 'happy-elementor-addons'),
				'type'           => Controls_Manager::COLOR,
				'condition'      => ['customize' => 'yes'],
				'style_transfer' => true,
				'selectors'      => [
					'{{WRAPPER}} .ha-social-icons-wrapper {{CURRENT_ITEM}}.ha-social-icon:hover' => 'border-color: {{VALUE}};',
				]
			]
		);

		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();

		$this->add_control(
			'ha_social_icon_list',
			[
				'label'       => __('Social Icons', 'happy-elementor-addons'),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'ha_social_icon' => [
							'value'   => 'fab fa-facebook',
							'library' => 'fa-brands',
						],
						'ha_social_link' => [
							'url' => '#'
						],
					],
					[
						'ha_social_icon' => [
							'value'   => 'fab fa-twitter',
							'library' => 'fa-brands',
						],
						'ha_social_link' => [
							'url' => '#'
						],
					],
					[
						'ha_social_icon' => [
							'value'   => 'fab fa-linkedin',
							'library' => 'fa-brands',
						],
						'ha_social_link' => [
							'url' => '#'
						],
					],
				],
				'title_field' => '<# print(elementor.helpers.getSocialNetworkNameFromIcon( ha_social_icon ) || ha_social_icon_title); #>',
			]
		);

		$this->add_control(
			'social_media_separator',
			[
				'label'        => __('Show Separator', 'happy-elementor-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'separator_type',
			[
				'label'        => __('Type', 'happy-elementor-addons'),
				'type'         => Controls_Manager::SELECT,
				'options'      => [
					'stroke' => __('Stroke', 'happy-elementor-addons'),
					'custom' => __('Custom', 'happy-elementor-addons'),
				],
				'default'      => 'stroke',
				'condition'    => [
					'social_media_separator' => 'yes'
				],
				'prefix_class' => 'ha-separator--',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'default_separator',
			[
				'label'       => __('Stroke Size', 'happy-elementor-addons'),
				'type'        => Controls_Manager::SLIDER,
				'condition'   => [
					'social_media_separator' => 'yes',
					'separator_type'         => 'stroke'
				],
				'size_units'  => ['px', 'em'],
				'selectors'   => [
					'{{WRAPPER}}.ha-separator--stroke .ha-social-icon-separator' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label'     => __('Color', 'happy-elementor-addons'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'social_media_separator' => 'yes',
					'separator_type'         => 'stroke'
				],
				'selectors' => [
					'{{WRAPPER}}.ha-separator--stroke .ha-social-icon-separator' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'custom_separator',
			[
				'label'       => __('Custom Character', 'happy-elementor-addons'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'social_media_separator' => 'yes',
					'separator_type'         => 'custom'
				],
				'render_type' => 'template'
			]
		);


		$this->add_responsive_control(
			'ha_social_icon_align',
			[
				'label'       => __('Alignment', 'happy-elementor-addons'),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'left'   => [
						'title' => __('Left', 'happy-elementor-addons'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'happy-elementor-addons'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __('Right', 'happy-elementor-addons'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'     => 'center',
				'selectors'   => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
				'separator'   => 'before',
				'render_type' => 'ui'
			]
		);

		$this->add_control(
			'sticky_options',
			[
				'label'        => __('Enable Sticky', 'happy-elementor-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator'    => 'before',
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__common_style_controls();
		$this->__icon_style_controls();
		$this->__social_name_style_controls();
		$this->__separator_style_controls();
	}

	protected function __common_style_controls() {

		$this->start_controls_section(
			'_section_common_style',
			[
				'label' => __('Common', 'happy-elementor-addons'),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->start_controls_tabs('_tab_social_icons_colors');

		$this->start_controls_tab(
			'_tab_normal_social_color',
			[
				'label' => __('Normal', 'happy-elementor-addons'),
			]
		);

		$this->add_control(
			'social_icons_color',
			[
				'label' => __('Color', 'happy-elementor-addons'),
				'type'  => Controls_Manager::COLOR,

				'selectors'      => [
					'{{WRAPPER}} .ha-social-icons-wrapper > .ha-social-icon'       => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-social-icons-wrapper > .ha-social-icon svg'   => 'fill: {{VALUE}};',
					'{{WRAPPER}}.ha-separator--stroke .ha-social-icon-separator'   => 'background: {{VALUE}};',
					'{{WRAPPER}}.ha-separator--custom .ha-social-icon-separator'   => 'color: {{VALUE}};',
				],
				'style_transfer' => true,
			]
		);
		$this->add_control(
			'social_icons_bg_color',
			[
				'label' => __('Background Color', 'happy-elementor-addons'),
				'type'  => Controls_Manager::COLOR,

				'selectors'      => [
					'{{WRAPPER}} .ha-social-icons-wrapper .ha-social-icon' => 'background-color: {{VALUE}};',
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'social_icon_common_border_color',
			[
				'label'     => __('Border Color', 'happy-elementor-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-social-icons-wrapper .ha-social-icon' => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'_tab_social_icons_hover',
			[
				'label' => __('Hover', 'happy-elementor-addons'),
			]
		);

		$this->add_control(
			'social_icons_hover_color',
			[
				'label'          => __('Color', 'happy-elementor-addons'),
				'type'           => Controls_Manager::COLOR,
				'selectors'      => [
					'{{WRAPPER}} .ha-social-icons-wrapper > .ha-social-icon:hover'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-social-icons-wrapper > .ha-social-icon:hover svg'   => 'fill: {{VALUE}};',
					'{{WRAPPER}}.ha-separator--stroke .ha-social-icon-separator'       => 'background: {{VALUE}};',
					'{{WRAPPER}}.ha-separator--custom .ha-social-icon-separator'       => 'color: {{VALUE}};',
				],
				'style_transfer' => true,
			]
		);
		$this->add_control(
			'social_icons_hover_bg_color',
			[
				'label'          => __('Background Color', 'happy-elementor-addons'),
				'type'           => Controls_Manager::COLOR,
				'selectors'      => [
					'{{WRAPPER}} .ha-social-icon:hover' => 'background-color: {{VALUE}};',
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'social_icon_common_hover_border_color',
			[
				'label'     => __('Border Color', 'happy-elementor-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-social-icons-wrapper .ha-social-icon:hover' => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ha_social_icon_global_padding',
			[
				'label'          => __('Padding', 'happy-elementor-addons'),
				'type'           => Controls_Manager::DIMENSIONS,
				'selectors'      => [
					'{{WRAPPER}} .ha-social-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'size_units'     => ['px', 'em'],
				'default'        => [
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'range'          => [
					'px' => [
						'min' => 20,
						'max' => 300
					],
					'em' => [
						'min' => 0,
						'max' => 5,
					],
				],
			]
		);

		$icon_spacing = is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};';

		$this->add_responsive_control(
			'ha_social_icon_spacing',
			[
				'label'     => __('Social Spacing', 'happy-elementor-addons'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-social-icon:not(:last-child)' => $icon_spacing,
					'{{WRAPPER}} .ha-social-icon-separator'        => $icon_spacing,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'image_border',
				'selector'  => '{{WRAPPER}} .ha-social-icon',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ha_social_icon_border_radius',
			[
				'label'      => __('Border Radius', 'happy-elementor-addons'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-social-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icons_box_shadow',
				//'selector'      => '{{WRAPPER}} .ha-social-icon, {{WRAPPER}} .ha-social-icon-separator',
				'selector' => '{{WRAPPER}} .ha-social-icon',
			]
		);

		$this->end_controls_section();
	}

	protected function __icon_style_controls() {

		$this->start_controls_section(
			'_section_style_ha_icon',
			[
				'label' => __('Icon', 'happy-elementor-addons'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'ha_social_icon_size',
			[
				'label'     => __('Size', 'happy-elementor-addons'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 20,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-social-icon.ha-social-icon--network i'   => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ha_social_icon_padding',
			[
				'label'          => __('Padding', 'happy-elementor-addons'),
				'type'           => Controls_Manager::SLIDER,
				'selectors'      => [
					'{{WRAPPER}} .ha-social-icon.ha-social-icon--network' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'default'        => [
					'unit' => 'em',
				],
				'tablet_default' => [
					'unit' => 'em',
				],
				'mobile_default' => [
					'unit' => 'em',
				],
				'range'          => [
					'em' => [
						'min' => 0,
						'max' => 5,
					],
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label'   => __('Hover Animation', 'happy-elementor-addons'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'none'                   => __('None', 'happy-elementor-addons'),
					'2d-transition'          => __('2D Animation', 'happy-elementor-addons'),
					'background-transition'  => __('Background Animation', 'happy-elementor-addons'),
					'shadow-glow-transition' => __('Shadow and Glow Animation', 'happy-elementor-addons'),
				]
			]
		);

		$this->add_control(
			'hover_2d_css_animation',
			[
				'label'     => __('Animation', 'happy-elementor-addons'),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'hvr-grow'                   => __('Grow', 'happy-elementor-addons'),
					'hvr-shrink'                 => __('Shrink', 'happy-elementor-addons'),
					'hvr-pulse'                  => __('Pulse', 'happy-elementor-addons'),
					'hvr-pulse-grow'             => __('Pulse Grow', 'happy-elementor-addons'),
					'hvr-pulse-shrink'           => __('Pulse Shrink', 'happy-elementor-addons'),
					'hvr-push'                   => __('Push', 'happy-elementor-addons'),
					'hvr-pop'                    => __('Pop', 'happy-elementor-addons'),
					'hvr-bounce-in'              => __('Bounce In', 'happy-elementor-addons'),
					'hvr-bounce-out'             => __('Bounce Out', 'happy-elementor-addons'),
					'hvr-rotate'                 => __('Rotate', 'happy-elementor-addons'),
					'hvr-grow-rotate'            => __('Grow Rotate', 'happy-elementor-addons'),
					'hvr-float'                  => __('Float', 'happy-elementor-addons'),
					'hvr-sink'                   => __('Sink', 'happy-elementor-addons'),
					'hvr-bob'                    => __('Bob', 'happy-elementor-addons'),
					'hvr-hang'                   => __('Hang', 'happy-elementor-addons'),
					'hvr-wobble-vertical'        => __('Wobble Vertical', 'happy-elementor-addons'),
					'hvr-wobble-horizontal'      => __('Wobble Horizontal', 'happy-elementor-addons'),
					'hvr-wobble-to-bottom-right' => __('Wobble To Bottom Right', 'happy-elementor-addons'),
					'hvr-wobble-to-top-right'    => __('Wobble To Top Right', 'happy-elementor-addons'),
					'hvr-buzz'                   => __('Buzz', 'happy-elementor-addons'),
					'hvr-buzz-out'               => __('Buzz Out', 'happy-elementor-addons'),
				],
				'condition' => [
					'hover_animation' => '2d-transition'
				]
			]
		);

		$this->add_control(
			'hover_background_css_animation',
			[
				'label'     => __('Animation', 'happy-elementor-addons'),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'hvr-fade'                   => __('Fade', 'happy-elementor-addons'),
					'hvr-back-pulse'             => __('Back Pulse', 'happy-elementor-addons'),
					'hvr-sweep-to-right'         => __('Sweep To Right', 'happy-elementor-addons'),
					'hvr-sweep-to-left'          => __('Sweep To Left', 'happy-elementor-addons'),
					'hvr-sweep-to-bottom'        => __('Sweep To Bottom', 'happy-elementor-addons'),
					'hvr-sweep-to-top'           => __('Sweep To Top', 'happy-elementor-addons'),
					'hvr-bounce-to-right'        => __('Bounce To Right', 'happy-elementor-addons'),
					'hvr-bounce-to-left'         => __('Bounce To Left', 'happy-elementor-addons'),
					'hvr-bounce-to-bottom'       => __('Bounce To Bottom', 'happy-elementor-addons'),
					'hvr-bounce-to-top'          => __('Bounce To Top', 'happy-elementor-addons'),
					'hvr-radial-out'             => __('Radial Out', 'happy-elementor-addons'),
					'hvr-radial-in'              => __('Radial In', 'happy-elementor-addons'),
					'hvr-rectangle-in'           => __('Rectangle In', 'happy-elementor-addons'),
					'hvr-rectangle-out'          => __('Rectangle Out', 'happy-elementor-addons'),
					'hvr-shutter-in-horizontal'  => __('Shutter In Horizontal', 'happy-elementor-addons'),
					'hvr-shutter-out-horizontal' => __('Shutter Out Horizontal', 'happy-elementor-addons'),
					'hvr-shutter-in-vertical'    => __('Shutter In Vertical', 'happy-elementor-addons'),
					'hvr-shutter-out-vertical'   => __('Shutter Out Vertical', 'happy-elementor-addons'),
				],
				'condition' => [
					'hover_animation' => 'background-transition'
				]
			]

		);

		$this->add_control(
			'hover_shadow_glow_css_animation',
			[
				'label'     => __('Animation', 'happy-elementor-addons'),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'hvr-glow'              => __('Glow', 'happy-elementor-addons'),
					'hvr-shadow'            => __('Shadow', 'happy-elementor-addons'),
					'hvr-grow-shadow'       => __('Grow Shadow', 'happy-elementor-addons'),
					'hvr-box-shadow-outset' => __('Box Shadow Outset', 'happy-elementor-addons'),
					'hvr-box-shadow-inset'  => __('Box Shadow Inset', 'happy-elementor-addons'),
					'hvr-float-shadow'      => __('Float Shadow', 'happy-elementor-addons'),
					'hvr-shadow-radial'     => __('Shadow Radial', 'happy-elementor-addons'),
				],
				'condition' => [
					'hover_animation' => 'shadow-glow-transition'
				]
			]

		);

		$this->end_controls_section();
	}

	protected function __social_name_style_controls() {

		$this->start_controls_section(
			'_section_style_custom_label',
			[
				'label' => __('Social Name', 'happy-elementor-addons'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'custom_label_typography',
				'label'    => __('Typography', 'happy-elementor-addons'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-social-icon-label'
			]

		);

		$this->add_control(
			'social_name_spacing',
			[
				'label'     => __('Spacing', 'happy-elementor-addons'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-social-icon:not(.elementor-social-icon-label) .ha-social-icon-label' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __separator_style_controls() {

		$this->start_controls_section(
			'_section_social_icon_separator',
			[
				'label' => __('Separator', 'happy-elementor-addons'),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'custom_separator_color',
			[
				'label'     => __('Color', 'happy-elementor-addons'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'social_media_separator' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.ha-separator--stroke .ha-social-icon-separator' => 'background: {{VALUE}} !important;',
					'{{WRAPPER}}.ha-separator--custom .ha-social-icon-separator' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ha_icon_separator_typography',
				'label'    => __('Typography', 'happy-elementor-addons'),
				'selector' => '{{WRAPPER}} .ha-social-icon-separator',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$social_list   = $settings['ha_social_icon_list'];
		$sticky_option = $settings['sticky_options'];
		$sticky_class  = '';
		if ('yes' === $sticky_option) {
			$sticky_class = 'ha-social-icon-sticky';
		}

		$enable_separator  = $settings['social_media_separator'];
		$separator_type    = $settings['separator_type'];
		$custom_separators = $settings['custom_separator'];
		$separators        = $custom_separators ? $custom_separators : '';

		$hover_css_animation = '';

		if (!empty($settings['hover_animation'])) {

			if ($settings['hover_2d_css_animation']) {
				$hover_css_animation = $settings['hover_2d_css_animation'];
			}

			if ($settings['hover_background_css_animation']) {
				$hover_css_animation = $settings['hover_background_css_animation'];
			}

			if ($settings['hover_shadow_glow_css_animation']) {
				$hover_css_animation = $settings['hover_shadow_glow_css_animation'];
			}
		}
		?>
		<div class="ha-social-icons-wrapper <?php echo $sticky_class; ?>">
			<?php
			foreach ($social_list as $key => $icons) {
				$icon         = $icons['ha_social_icon']['value'];
				$social_title = esc_html($icons['ha_social_icon_title']);
				$link_attr    = 'link_' . $key;

				if (!empty($icons['ha_social_icon'])) {
					$social_name = str_replace(['fa fa-', 'fab fa-', 'far fa-'], '', $icon);
				}

				$this->add_link_attributes( $link_attr, $icons['ha_social_link'] );

				$this->add_render_attribute($link_attr, 'class', [
					'ha-social-icon',
					'elementor-repeater-item-' . $icons['_id'] . ' ' . $hover_css_animation,
					'elementor-social-icon-' . ($icon ? $social_name : 'label'),
				]);

				if (!empty($icon)) {
					$this->add_render_attribute($link_attr, 'class', 'ha-social-icon--network');
				} else {
					$this->add_render_attribute($link_attr, 'class', 'ha-social-icon--custom-label');
				}
				?>
				<a <?php echo $this->get_render_attribute_string($link_attr); ?>>
					<?php
					Icons_Manager::render_icon($icons['ha_social_icon']);
					// Icons_Manager::render_icon( $icons['new_social_icons'], [ 'aria-hidden' => 'true' ] );
					if (!empty($social_title) && '' != $social_title) {
						echo "<span class='ha-social-icon-label'>" . $social_title . "</span>";
					}

					?>
				</a>
				<?php
				if ('yes' === $enable_separator) {
					echo "<span class='ha-social-icon-separator'> " . $separators . " </span>";
				}
			}
			?>
		</div>
		<?php
	}
}

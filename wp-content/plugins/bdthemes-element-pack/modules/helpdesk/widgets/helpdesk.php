<?php
namespace ElementPack\Modules\Helpdesk\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Helpdesk extends Module_Base {
	public function get_name() {
		return 'bdt-helpdesk';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Help Desk', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-helpdesk';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'help', 'desk', 'livechat', 'messanger', 'telegram', 'email', 'whatsapp' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-font', 'ep-helpdesk', 'tippy' ];
        }
    }

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['popper', 'tippyjs', 'ep-scripts'];
        } else {
			return [ 'popper', 'tippyjs', 'ep-helpdesk'];
        }
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/bO__skhy4yk';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_helpdesk_layout',
			[
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'helpdesk_position',
			[
				'label'   => __( 'Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'left'  => __( 'Left', 'bdthemes-element-pack' ),
					'right' => __( 'Right', 'bdthemes-element-pack' ),
				],
				'default'   => 'right',
				'selectors' => [
					'{{WRAPPER}} .bdt-helpdesk-icons' => '{{VALUE}} : 30px;',
				],
			]
		);

		$this->add_control(
			'helpdesk_select_icon',
			[
				'label'       => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'helpdesk_icon',
				'default' => [
					'value' => 'far fa-life-ring',
					'library' => 'fa-regular',
				],
			]
		);

		$this->add_responsive_control(
			'helpdesk_size',
			[
				'label'   => esc_html__( 'Icon Size', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 35,
						'max' => 100,
						'step' => 5,
					],
				],
				'default' => [
					'size' => 50,
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons-item, {{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons-open-button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'helpdesk_space',
			[
				'label'   => esc_html__( 'Icon Space', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-helpdesk-icons-open:checked ~ .bdt-helpdesk-icons-item:nth-child(3)' => 'transform: translate3d(0, calc(-{{SIZE}}{{UNIT}} - {{helpdesk_size.SIZE}}{{UNIT}}), 0);',
					'{{WRAPPER}} .bdt-helpdesk-icons-open:checked ~ .bdt-helpdesk-icons-item:nth-child(4)' => 'transform: translate3d(0, calc(-{{SIZE}}{{UNIT}} * 2 - {{helpdesk_size.SIZE}}{{UNIT}} * 2), 0);',
					'{{WRAPPER}} .bdt-helpdesk-icons-open:checked ~ .bdt-helpdesk-icons-item:nth-child(5)' => 'transform: translate3d(0, calc(-{{SIZE}}{{UNIT}} * 3 - {{helpdesk_size.SIZE}}{{UNIT}} * 3), 0);',
					'{{WRAPPER}} .bdt-helpdesk-icons-open:checked ~ .bdt-helpdesk-icons-item:nth-child(6)' => 'transform: translate3d(0, calc(-{{SIZE}}{{UNIT}} * 4 - {{helpdesk_size.SIZE}}{{UNIT}} * 4), 0);',
					'{{WRAPPER}} .bdt-helpdesk-icons-open:checked ~ .bdt-helpdesk-icons-item:nth-child(7)' => 'transform: translate3d(0, calc(-{{SIZE}}{{UNIT}} * 5 - {{helpdesk_size.SIZE}}{{UNIT}} * 5), 0);',
					'{{WRAPPER}} .bdt-helpdesk-icons-open:checked ~ .bdt-helpdesk-icons-item:nth-child(8)' => 'transform: translate3d(0, calc(-{{SIZE}}{{UNIT}} * 6 - {{helpdesk_size.SIZE}}{{UNIT}} * 6), 0);',
					'{{WRAPPER}} .bdt-helpdesk-icons-open:checked ~ .bdt-helpdesk-icons-item:nth-child(9)' => 'transform: translate3d(0, calc(-{{SIZE}}{{UNIT}} * 7 - {{helpdesk_size.SIZE}}{{UNIT}} * 7), 0);',
				],
			]
		);

		$this->add_control(
			'helpdesk_title',
			[
				'label'       => esc_html__( 'Main Icon Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'render_type' => 'template',
				'default'     => 'Need help? Contact us with your favorite way.',
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'messenger_show',
			[
				'label'   => esc_html__( 'Messenger', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'skype_show',
			[
				'label'   => esc_html__( 'Skype', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'viber_show',
			[
				'label'   => esc_html__( 'Viber', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'whatsapp_show',
			[
				'label'   => esc_html__( 'WhatsApp', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'telegram_show',
			[
				'label'   => esc_html__( 'Telegram', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);


		$this->add_control(
			'custom_show',
			[
				'label'   => esc_html__( 'Custom', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'mailto_show',
			[
				'label'   => esc_html__( 'Email', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_helpdesk_messenger',
			[
				'label' => esc_html__( 'Messenger', 'bdthemes-element-pack' ),
				'condition' => [
					'messenger_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'messenger_title',
			[
				'label'       => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'render_type' => 'template',
				'default'     => 'Chat on messenger',
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'messenger_link',
			[
				'label'       => esc_html__( 'Link/ID', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'alias', 'bdthemes-element-pack' ),
				'default'     => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'messenger_onclick',
			[
				'label'   => esc_html__( 'OnClick', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'messenger_onclick_event',
			[
				'label'       => esc_html__( 'OnClick Event', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'myFunction()',
				'description' => sprintf( __('For details please look <a href="%s" target="_blank">here</a>'), 'https://www.w3schools.com/jsref/event_onclick.asp' ),
				'condition' => [
					'messenger_onclick' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_helpdesk_skype',
			[
				'label' => esc_html__( 'Skype', 'bdthemes-element-pack' ),
				'condition' => [
					'skype_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'skype_title',
			[
				'label'       => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'render_type' => 'template',
				'default'     => 'Chat on skype',
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'skype_link',
			[
				'label'       => esc_html__( 'Link/ID', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'alias', 'bdthemes-element-pack' ),
				'default'     => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'skype_onclick',
			[
				'label'   => esc_html__( 'OnClick', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'skype_onclick_event',
			[
				'label'       => esc_html__( 'OnClick Event', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'myFunction()',
				'description' => sprintf( __('For details please look <a href="%s" target="_blank">here</a>'), 'https://www.w3schools.com/jsref/event_onclick.asp' ),
				'condition' => [
					'skype_onclick' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_helpdesk_viber',
			[
				'label' => esc_html__( 'Viber', 'bdthemes-element-pack' ),
				'condition' => [
					'viber_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'viber_title',
			[
				'label'       => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'render_type' => 'template',
				'default'     => 'Chat on viber',
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'viber_link',
			[
				'label'       => esc_html__( 'Link/ID', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'alias', 'bdthemes-element-pack' ),
				'default'     => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'viber_onclick',
			[
				'label'   => esc_html__( 'OnClick', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'viber_onclick_event',
			[
				'label'       => esc_html__( 'OnClick Event', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'myFunction()',
				'description' => sprintf( __('For details please look <a href="%s" target="_blank">here</a>'), 'https://www.w3schools.com/jsref/event_onclick.asp' ),
				'condition' => [
					'viber_onclick' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_helpdesk_whatsapp',
			[
				'label' => esc_html__( 'WhatsApp', 'bdthemes-element-pack' ),
				'condition' => [
					'whatsapp_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'whatsapp_title',
			[
				'label'       => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'render_type' => 'template',
				'default'     => 'Call via whatsapp',
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'whatsapp_link',
			[
				'label'       => esc_html__( 'Number', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => '+00 000 000 00',
				'default'     => [
					'url' => '+1234567890',
				],
			]
		);

		$this->add_control(
			'whatsapp_onclick',
			[
				'label'   => esc_html__( 'OnClick', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'whatsapp_onclick_event',
			[
				'label'       => esc_html__( 'OnClick Event', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'myFunction()',
				'description' => sprintf( __('For details please look <a href="%s" target="_blank">here</a>'), 'https://www.w3schools.com/jsref/event_onclick.asp' ),
				'condition' => [
					'whatsapp_onclick' => 'yes'
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_helpdesk_telegram',
			[
				'label' => esc_html__( 'Telegram', 'bdthemes-element-pack' ),
				'condition' => [
					'telegram_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'telegram_title',
			[
				'label'       => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'render_type' => 'template',
				'default'     => 'Chat on telegram',
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'telegram_link',
			[
				'label'       => esc_html__( 'User ID', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'alias', 'bdthemes-element-pack' ),
				'default'     => [
					'url' => 'bdthemes',
				],
			]
		);

		$this->add_control(
			'telegram_onclick',
			[
				'label'   => esc_html__( 'OnClick', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'telegram_onclick_event',
			[
				'label'       => esc_html__( 'OnClick Event', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'myFunction()',
				'description' => sprintf( __('For details please look <a href="%s" target="_blank">here</a>'), 'https://www.w3schools.com/jsref/event_onclick.asp' ),
				'condition' => [
					'telegram_onclick' => 'yes'
				]
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_helpdesk_custom',
			[
				'label' => esc_html__( 'Custom', 'bdthemes-element-pack' ),
				'condition' => [
					'custom_show' => 'yes',
				],

			]
		);

		$this->add_control(
			'custom_title',
			[
				'label'       => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'render_type' => 'template',
				'default'     => 'Chat with us',
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'custom_link',
			[
				'label'       => esc_html__( 'Link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => esc_html__( 'https://sample.com', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'custom_onclick',
			[
				'label'   => esc_html__( 'OnClick', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'custom_onclick_event',
			[
				'label'       => esc_html__( 'OnClick Event', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'myFunction()',
				'default' => 'Intercom("show");',
				'description' => sprintf( __('For details please look <a href="%s" target="_blank">here</a>'), 'https://www.w3schools.com/jsref/event_onclick.asp' ),
				'condition' => [
					'custom_onclick' => 'yes'
				]
			]
		);

		$this->add_control(
			'helpdesk_custom_icon',
			[
				'label'       => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'custom_icon',
				'default' => [
					'value' => 'fas fa-comment',
					'library' => 'fa-solid',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_helpdesk_mailto',
			[
				'label' => esc_html__( 'Email', 'bdthemes-element-pack' ),
				'condition' => [
					'mailto_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'mailto_title',
			[
				'label'       => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'render_type' => 'template',
				'default'     => 'Email Us',
				'dynamic'     => [ 'active' => true ],
			]
		);


		$default_email = get_bloginfo( 'admin_email' );

		$this->add_control(
			'mailto_link',
			[
				'label'       => esc_html__( 'Email Address', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => $default_email,
				'default'     => [
					'url' => $default_email,
				],
			]
		);

		$this->add_control(
			'mailto_subject',
			[
				'label'   => esc_html__( 'Subject', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Support related issue', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'mailto_body',
			[
				'label'   => esc_html__( 'Body Text', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Hello, I am contact with you because ', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'mailto_onclick',
			[
				'label'   => esc_html__( 'OnClick', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);


		$this->add_control(
			'mailto_onclick_event',
			[
				'label'       => esc_html__( 'OnClick Event', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'myFunction()',
				'description' => sprintf( __('For details please look <a href="%s" target="_blank">here</a>'), 'https://www.w3schools.com/jsref/event_onclick.asp' ),
				'condition' => [
					'mailto_onclick' => 'yes'
				]
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_helpdesk_additional',
			[
				'label' => esc_html__( 'Additional', 'bdthemes-element-pack' ),
			]
		);


		$this->add_control(
			'helpdesk_horizontal_offset',
			[
				'label'   => esc_html__( 'Horizontal Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 5,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons-item, {{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons-open-button' => '{{helpdesk_position.VALUE}}: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'helpdesk_vertical_offset',
			[
				'label'   => esc_html__( 'Vertical Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 5,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons' => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'helpdesk_tooltip',
			[
				'label'   => esc_html__( 'Title as Tooltip', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'helpdesk_tooltip_placement',
			[
				'label'   => esc_html__( 'Placement', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'top'          => esc_html__( 'Top', 'bdthemes-element-pack' ),
					'bottom'       => esc_html__( 'Bottom', 'bdthemes-element-pack' ),
					'left'         => esc_html__( 'Left', 'bdthemes-element-pack' ),
					'right'        => esc_html__( 'Right', 'bdthemes-element-pack' ),
				],
				'render_type'  => 'template',
				'condition' => [
					'helpdesk_tooltip' => 'yes'
				]
			]
		);

		$this->add_control(
			'helpdesk_tooltip_animation',
			[
				'label'   => esc_html__( 'Animation', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'shift-toward',
				'options' => [
					'shift-away'   => esc_html__( 'Shift-Away', 'bdthemes-element-pack' ),
					'shift-toward' => esc_html__( 'Shift-Toward', 'bdthemes-element-pack' ),
					'fade'         => esc_html__( 'Fade', 'bdthemes-element-pack' ),
					'scale'        => esc_html__( 'Scale', 'bdthemes-element-pack' ),
					'perspective'  => esc_html__( 'Perspective', 'bdthemes-element-pack' ),
				],
				'render_type'  => 'template',
				'condition' => [
					'helpdesk_tooltip' => 'yes'
				]
			]
		);

		$this->add_control(
			'helpdesk_tooltip_x_offset',
			[
				'label'   => esc_html__( 'Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => -1000,
				'max'     => 1000,
				'step'    => 1,
				'condition' => [
					'helpdesk_tooltip' => 'yes'
				]
			]
		);

		$this->add_control(
			'helpdesk_tooltip_y_offset',
			[
				'label'   => esc_html__( 'Distance', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => -1000,
				'max'     => 1000,
				'step'    => 1,
				'condition' => [
					'helpdesk_tooltip' => 'yes'
				]
			]
		);

		$this->add_control(
			'helpdesk_tooltip_arrow',
			[
				'label'        => esc_html__( 'Arrow', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'condition' => [
					'helpdesk_tooltip' => 'yes'
				]
			]
		);

		$this->add_control(
			'helpdesk_tooltip_trigger',
			[
				'label'       => __( 'Trigger on Click', 'bdthemes-element-pack' ),
				'description' => __( 'Don\'t set yes when you set lightbox image with marker.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SWITCHER,
				'condition' => [
					'helpdesk_tooltip' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_helpdesk_main_icon',
			[
				'label'     => esc_html__( 'Main Icons', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_style_helpdesk');

		$this->start_controls_tab(
			'tab_style_helpdesk_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'helpdesk_main_icon_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons-open-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons-open-button svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'helpdesk_main_icon_background',
				'selector' => '{{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons-open-button'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_style_helpdesk_main_icon_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'helpdesk_main_icon_hover_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons-open-button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons-open-button:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'helpdesk_main_icon_hover_background',
				'selector' => '{{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons-open-button:hover'
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_helpdesk_icons',
			[
				'label'     => esc_html__( 'Icons Style', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_style_helpdesk_icons');

		$this->start_controls_tab(
			'tab_style_helpdesk_icons_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'helpdesk_icons_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons-item' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'helpdesk_icons_background',
				'selector' => '{{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons-item'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_style_helpdesk_icons_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'helpdesk_icons_hover_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons-item:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'helpdesk_icons_hover_background',
				'selector' => '{{WRAPPER}} .bdt-helpdesk .bdt-helpdesk-icons-item:hover'
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_tooltip',
			[
				'label' => esc_html__( 'Tooltip', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'helpdesk_tooltip_width',
			[
				'label'      => esc_html__( 'Width', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [
					'px', 'em',
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'width: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'helpdesk_tooltip_typography',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_control(
			'helpdesk_tooltip_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'helpdesk_tooltip_text_align',
			[
				'label'   => esc_html__( 'Text Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'helpdesk_tooltip_background',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"], .tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-backdrop',
			]
		);

		$this->add_control(
			'helpdesk_tooltip_arrow_color',
			[
				'label'     => esc_html__( 'Arrow Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'helpdesk_tooltip_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'render_type'  => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'helpdesk_tooltip_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_responsive_control(
			'helpdesk_tooltip_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'helpdesk_tooltip_box_shadow',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_control(
			'tooltip_size',
			[
				'label'   => esc_html__( 'Tooltip Size', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''      => esc_html__( 'Default', 'bdthemes-element-pack' ),
					'large' => esc_html__( 'Large', 'bdthemes-element-pack' ),
					'small' => esc_html__( 'small', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->end_controls_section();

	}



	protected function render() {
		$settings = $this->get_settings_for_display();
		$id       = 'bdt-helpdesk-icons-' . $this->get_id();

		if ( ! isset( $settings['helpdesk_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['helpdesk_icon'] = 'fas fa-life-ring';
		}

		$migrated  = isset( $settings['__fa4_migrated']['helpdesk_select_icon'] );
		$is_new    = empty( $settings['helpdesk_icon'] ) && Icons_Manager::is_migration_allowed();

		?>

		<div class="bdt-helpdesk">
			<nav class="bdt-helpdesk-icons">
				<input type="checkbox" class="bdt-helpdesk-icons-open" name="bdt-helpdesk-icons-open" id="<?php echo esc_attr($id); ?>"/>
				<label class="bdt-helpdesk-icons-open-button" for="<?php echo esc_attr($id); ?>" title="<?php echo esc_html($settings['helpdesk_title']); ?>">

					<?php if ( $is_new || $migrated ) :
						Icons_Manager::render_icon( $settings['helpdesk_select_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] );
					else : ?>
						<i class="<?php echo esc_attr( $settings['helpdesk_icon'] ); ?>" aria-hidden="true"></i>
					<?php endif; ?>

				</label>
				<?php $this->messenger(); ?>
				<?php $this->skype(); ?>
				<?php $this->viber(); ?>
				<?php $this->whatsapp(); ?>
				<?php $this->mailto(); ?>
				<?php $this->telegram(); ?>
				<?php $this->custom(); ?>
			</nav>
		</div>

		<?php

	}

	protected function messenger() {
		$settings = $this->get_settings_for_display();

		if ('yes' != $settings['messenger_show']) {
			return;
		}

		$this->add_render_attribute( 'messenger', 'class', ['bdt-helpdesk-icons-item', 'bdt-hdi-messenger'] );

		if ( ! empty( $settings['messenger_link']['url'] ) ) {

			$final_link = 'https://m.me/' . $settings['messenger_link']['url'];

			$this->add_render_attribute( 'messenger', 'href', $final_link );

			if ( $settings['messenger_link']['is_external'] ) {
				$this->add_render_attribute( 'messenger', 'target', '_blank' );
			}

			if ( $settings['messenger_link']['nofollow'] ) {
				$this->add_render_attribute( 'messenger', 'rel', 'nofollow' );
			}

		}

		if ( $settings['messenger_link']['nofollow'] ) {
			$this->add_render_attribute( 'messenger', 'rel', 'nofollow' );
		}

		if ($settings['messenger_onclick']) {
			$this->add_render_attribute( 'messenger', 'href', '#', true );
			$this->add_render_attribute( 'messenger', 'onclick', $settings['messenger_onclick_event'] );
		}

		$this->add_render_attribute( 'messenger', 'data-tippy-content', $settings['messenger_title'] );

		$this->tooltip('messenger');

		?>


		<a <?php echo $this->get_render_attribute_string( 'messenger' ); ?>>
			<i class="ep-icon-messenger" aria-hidden="true"></i>
		</a>

		<?php

	}

	protected function skype() {
		$settings = $this->get_settings_for_display();

		if ('yes' != $settings['skype_show']) {
			return;
		}

		$this->add_render_attribute( 'skype', 'class', ['bdt-helpdesk-icons-item', 'bdt-hdi-skype'] );

		if ( ! empty( $settings['skype_link']['url'] ) ) {

			$final_link = 'https://www.skype.com/' . $settings['skype_link']['url'];

			$this->add_render_attribute( 'skype', 'href', $final_link );

			if ( $settings['skype_link']['is_external'] ) {
				$this->add_render_attribute( 'skype', 'target', '_blank' );
			}

			if ( $settings['skype_link']['nofollow'] ) {
				$this->add_render_attribute( 'skype', 'rel', 'nofollow' );
			}

		}

		if ( $settings['skype_link']['nofollow'] ) {
			$this->add_render_attribute( 'skype', 'rel', 'nofollow' );
		}

		if ($settings['skype_onclick']) {
			$this->add_render_attribute( 'skype', 'href', '#', true );
			$this->add_render_attribute( 'skype', 'onclick', $settings['skype_onclick_event'] );
		}

		$this->add_render_attribute( 'skype', 'data-tippy-content', $settings['skype_title'] );

		$this->tooltip('skype');

		?>


		<a <?php echo $this->get_render_attribute_string( 'skype' ); ?>>
			<i class="ep-icon-skype" aria-hidden="true"></i>
		</a>

		<?php

	}

	protected function viber() {
		$settings = $this->get_settings_for_display();

		if ('yes' != $settings['viber_show']) {
			return;
		}

		$this->add_render_attribute( 'viber', 'class', ['bdt-helpdesk-icons-item', 'bdt-hdi-viber'] );

		if ( ! empty( $settings['viber_link']['url'] ) ) {

			$final_link = 'https://www.viber.com/' . $settings['viber_link']['url'];

			$this->add_render_attribute( 'viber', 'href', $final_link );

			if ( $settings['viber_link']['is_external'] ) {
				$this->add_render_attribute( 'viber', 'target', '_blank' );
			}

			if ( $settings['viber_link']['nofollow'] ) {
				$this->add_render_attribute( 'viber', 'rel', 'nofollow' );
			}

		}

		if ( $settings['viber_link']['nofollow'] ) {
			$this->add_render_attribute( 'viber', 'rel', 'nofollow' );
		}

		if ($settings['viber_onclick']) {
			$this->add_render_attribute( 'viber', 'href', '#', true );
			$this->add_render_attribute( 'viber', 'onclick', $settings['viber_onclick_event'] );
		}

		$this->add_render_attribute( 'viber', 'data-tippy-content', $settings['viber_title'] );

		$this->tooltip('viber');

		?>


		<a <?php echo $this->get_render_attribute_string( 'viber' ); ?>>
			<i class="ep-icon-viber" aria-hidden="true"></i>
		</a>

		<?php

	}

	protected function whatsapp() {
		$settings = $this->get_settings_for_display();

		if ('yes' != $settings['whatsapp_show']) {
			return;
		}

		$this->add_render_attribute( 'whatsapp', 'class', ['bdt-helpdesk-icons-item', 'bdt-hdi-whatsapp'] );

		if ( ! empty( $settings['whatsapp_link']['url'] ) ) {

			$final_link = 'https://wa.me/' . $settings['whatsapp_link']['url'];

			$this->add_render_attribute( 'whatsapp', 'href', $final_link );

			if ( $settings['whatsapp_link']['is_external'] ) {
				$this->add_render_attribute( 'whatsapp', 'target', '_blank' );
			}

			if ( $settings['whatsapp_link']['nofollow'] ) {
				$this->add_render_attribute( 'whatsapp', 'rel', 'nofollow' );
			}

		}

		if ( $settings['whatsapp_link']['nofollow'] ) {
			$this->add_render_attribute( 'whatsapp', 'rel', 'nofollow' );
		}

		if ($settings['whatsapp_onclick']) {
			$this->add_render_attribute( 'whatsapp', 'onclick', $settings['whatsapp_onclick_event'] );
		}

		$this->add_render_attribute( 'whatsapp', 'data-tippy-content', $settings['whatsapp_title'] );

		$this->tooltip('whatsapp');

		?>


		<a <?php echo $this->get_render_attribute_string( 'whatsapp' ); ?>>
			<i class="ep-icon-whatsapp" aria-hidden="true"></i>
		</a>


		<?php

	}


	protected function telegram() {
		$settings = $this->get_settings_for_display();

		if ('yes' != $settings['telegram_show']) {
			return;
		}

		$this->add_render_attribute( 'telegram', 'class', ['bdt-helpdesk-icons-item', 'bdt-hdi-telegram'] );

		if ( ! empty( $settings['telegram_link']['url'] ) ) {

			$final_link = 'https://telegram.me/' . $settings['telegram_link']['url'];

			$this->add_render_attribute( 'telegram', 'href', esc_url($final_link) );

			if ( $settings['telegram_link']['is_external'] ) {
				$this->add_render_attribute( 'telegram', 'target', '_blank' );
			}

			if ( $settings['telegram_link']['nofollow'] ) {
				$this->add_render_attribute( 'telegram', 'rel', 'nofollow' );
			}

		}

		if ( $settings['telegram_link']['nofollow'] ) {
			$this->add_render_attribute( 'telegram', 'rel', 'nofollow' );
		}

		if ($settings['telegram_onclick']) {
			$this->add_render_attribute( 'telegram', 'onclick', $settings['telegram_onclick_event'] );
		}

		$this->add_render_attribute( 'telegram', 'data-tippy-content', $settings['telegram_title'] );

		$this->tooltip('telegram');

		?>


		<a <?php echo $this->get_render_attribute_string( 'telegram' ); ?>>
			<i class="ep-icon-telegram" aria-hidden="true"></i>
		</a>


		<?php

	}

	protected function custom() {
		$settings = $this->get_settings_for_display();

		if ('yes' != $settings['custom_show']) {
			return;
		}

		$this->add_render_attribute( 'custom', 'class', ['bdt-helpdesk-icons-item', 'bdt-hdi-custom'] );

		if ( ! empty( $settings['custom_link']['url'] ) ) {

			$final_link = $settings['custom_link']['url'];

			$this->add_render_attribute( 'custom', 'href', esc_url($final_link) );

			if ( $settings['custom_link']['is_external'] ) {
				$this->add_render_attribute( 'custom', 'target', '_blank' );
			}

			if ( $settings['custom_link']['nofollow'] ) {
				$this->add_render_attribute( 'custom', 'rel', 'nofollow' );
			}

			$this->tooltip('custom');

		}

		if ( $settings['custom_link']['nofollow'] ) {
			$this->add_render_attribute( 'custom', 'rel', 'nofollow' );
		}

		if ($settings['custom_onclick']) {
			$this->add_render_attribute( 'custom', 'onclick', $settings['custom_onclick_event'] );
		}

		$this->add_render_attribute( 'custom', 'data-tippy-content', $settings['custom_title'] );

		$this->tooltip('custom');

		if ( ! isset( $settings['custom_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['custom_icon'] = 'fas fa-envelope';
		}

		$migrated  = isset( $settings['__fa4_migrated']['helpdesk_custom_icon'] );
		$is_new    = empty( $settings['custom_icon'] ) && Icons_Manager::is_migration_allowed();

		?>


		<a <?php echo $this->get_render_attribute_string( 'custom' ); ?>>

			<?php if ( $is_new || $migrated ) :
				Icons_Manager::render_icon( $settings['helpdesk_custom_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] );
			else : ?>
				<i class="<?php echo esc_attr( $settings['custom_icon'] ); ?>" aria-hidden="true"></i>
			<?php endif; ?>

		</a>


		<?php

	}

	protected function mailto() {
		$settings = $this->get_settings_for_display();

		if ('yes' != $settings['mailto_show']) {
			return;
		}

		$this->add_render_attribute( 'mailto', 'class', ['bdt-helpdesk-icons-item', 'bdt-hdi-mailto'] );

		if ( ! empty( $settings['mailto_link']['url'] ) ) {

			$final_link = 'mailto:';
			$final_link .= $settings['mailto_link']['url'];

			if ($settings['mailto_subject']) {

				$final_link .= '?subject=' . $settings['mailto_subject'];

				if ($settings['mailto_body']) {
					$final_link .= '&amp;body=' . $settings['mailto_body'];
				}
			}

			$this->add_render_attribute( 'mailto', 'href', esc_url($final_link) );

			if ( $settings['mailto_link']['is_external'] ) {
				$this->add_render_attribute( 'mailto', 'target', '_blank' );
			}

			if ( $settings['mailto_link']['nofollow'] ) {
				$this->add_render_attribute( 'mailto', 'rel', 'nofollow' );
			}

		}

		if ( $settings['mailto_link']['nofollow'] ) {
			$this->add_render_attribute( 'mailto', 'rel', 'nofollow' );
		}

		if ($settings['mailto_onclick']) {
			$this->add_render_attribute( 'mailto', 'onclick', $settings['mailto_onclick_event'] );
		}

		$this->add_render_attribute( 'mailto', 'data-tippy-content', $settings['mailto_title'] );

		$this->tooltip('mailto');

		?>


		<a <?php echo $this->get_render_attribute_string( 'mailto' ); ?>>
			<i class="ep-icon-envelope" aria-hidden="true"></i>
		</a>


		<?php

	}


	public function tooltip($icon) {
		$settings = $this->get_settings_for_display();

		if ('yes' != $settings['helpdesk_tooltip']) {
			return;
		}

		// Tooltip settings
		$this->add_render_attribute( $icon, 'class', 'bdt-tippy-tooltip' );
		$this->add_render_attribute( $icon, 'data-tippy', '', true );

		if ($settings['helpdesk_tooltip_placement']) {
			$this->add_render_attribute( $icon, 'data-tippy-placement', $settings['helpdesk_tooltip_placement'], true );
		}

		if ($settings['helpdesk_tooltip_animation']) {
			$this->add_render_attribute( $icon, 'data-tippy-animation', $settings['helpdesk_tooltip_animation'], true );
		}

		if (isset($settings['helpdesk_tooltip_x_offset']['size']) or isset($settings['helpdesk_tooltip_y_offset']['size'])) {
			$this->add_render_attribute( $icon, 'data-tippy-offset', '[' . $settings['helpdesk_tooltip_x_offset']['size'] .','. $settings['helpdesk_tooltip_y_offset']['size'] . ']', true );
		}

		if ('yes' == $settings['helpdesk_tooltip_arrow']) {
			$this->add_render_attribute( $icon, 'data-tippy-arrow', 'true', true );
		}else{
			$this->add_render_attribute( $icon, 'data-tippy-arrow', 'false', true );
		}

		if ('yes' == $settings['helpdesk_tooltip_trigger']) {
			$this->add_render_attribute( $icon, 'data-tippy-trigger', 'click', true );
		}
	}


}

<?php

/**
 * Content Switcher widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Repeater;

defined( 'ABSPATH' ) || die();

class Content_Switcher extends Base {

	/**
	 * Get widget title.
	 *
	 * @since 2.24.2
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Content Switcher', 'happy-elementor-addons' );
	}

	public function get_custom_help_url() {
		// return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/';
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-switcher';
	}

	public function get_keywords() {
		return ['content', 'switcher', 'toggle'];
	}

	public function select_elementor_page( $type ) {
		$args  = [
			'tax_query'      => [
				[
					'taxonomy' => 'elementor_library_type',
					'field'    => 'slug',
					'terms'    => $type,
				],
			],
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
		];
		$query = new \WP_Query( $args );

		$posts = $query->posts;
		foreach ( $posts as $post ) {
			$items[ $post->ID ] = $post->post_title;
		}

		if ( empty( $items ) ) {
			$items = [];
		}

		return $items;
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls() {
		$this->__switcher_content_controls();
		$this->__settings_content_controls();
	}

	protected function __switcher_content_controls() {

		$this->start_controls_section(
			'_section_content',
			[
				'label' => __( 'Content Switcher', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'select_design',
			[
				'label'   => __( 'Choose Design', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'round'    => __( 'Round', 'happy-elementor-addons' ),
					'round-2'  => __( 'Round 2', 'happy-elementor-addons' ),
					'square'   => __( 'Square', 'happy-elementor-addons' ),
					'square-2' => __( 'Square 2', 'happy-elementor-addons' ),
					'button'   => __( 'Button', 'happy-elementor-addons' ),
				],
				'default' => 'round',
			]
		);

		$this->add_control(
			'design_warning_message',
			[
				'raw'             => '<strong>' . esc_html__( 'Please note!', 'happy-elementor-addons' ) . '</strong> ' . esc_html__( 'This design requires only two items. Only the first two items will be used.', 'happy-elementor-addons' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type'     => 'ui',
				'condition'       => [
					'select_design' => ['round', 'round-2', 'square', 'square-2'],
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'label'   => __( 'Title', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Content', 'happy-elementor-addons' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'content_type',
			[
				'label'   => __( 'Type', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'plain_content' => __( 'Plain/ HTML Text', 'happy-elementor-addons' ),
					'saved_section' => __( 'Saved Section', 'happy-elementor-addons' ),
					'saved_page'    => __( 'Saved Page', 'happy-elementor-addons' ),
				],
				'default' => 'plain_content',
			]
		);
		$repeater->add_control(
			'plain_content',
			[
				'label'       => __( 'Plain/ HTML Text', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => ha_get_allowed_html_desc( 'intermediate' ),
				'rows'        => 20,
				'condition'   => [
					'content_type' => 'plain_content',
				],
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Add some content here.', 'happy-elementor-addons' ),
			]
		);

		$saved_sections = ['0' => __( '--- Select Section ---', 'happy-elementor-addons' )];
		$saved_sections = $saved_sections + $this->select_elementor_page( 'section' );

		$repeater->add_control(
			'saved_section',
			[
				'label'     => __( 'Sections', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $saved_sections,
				'default'   => '0',
				'condition' => [
					'content_type' => 'saved_section',
				],
			]
		);

		$saved_page = ['0' => __( '--- Select Page ---', 'happy-elementor-addons' )];
		$saved_page = $saved_page + $this->select_elementor_page( 'page' );

		$repeater->add_control(
			'saved_pages',
			[
				'label'     => __( 'Pages', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $saved_page,
				'default'   => '0',
				'condition' => [
					'content_type' => 'saved_page',
				],
			]
		);

		$repeater->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'happy-elementor-addons' ),
				'type'  => Controls_Manager::ICONS,
			]
		);

		$repeater->add_control(
			'icon_align',
			[
				'label'   => __( 'Icon Position', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => __( 'Left', 'happy-elementor-addons' ),
					'right' => __( 'Right', 'happy-elementor-addons' ),
				],
			]
		);

		$repeater->add_control(
			'active',
			[
				'label'        => __( 'Active', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'Active on Load', 'happy-elementor-addons' ),
				'label_on'     => __( 'Yes', 'happy-elementor-addons' ),
				'label_off'    => __( 'No', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'content_list',
			[
				'label'         => __( 'Contents', 'happy-elementor-addons' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'prevent_empty' => true,
				'default'       => [
					[
						'title'         => __( 'Primary', 'happy-elementor-addons' ),
						'content_type'  => 'plain_content',
						'plain_content' => __( 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.' ),
						'active'        => 'yes',
					],
					[
						'title'         => __( 'Secondary', 'happy-elementor-addons' ),
						'content_type'  => 'plain_content',
						'plain_content' => __( 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).', 'happy-elementor-addons' ),
					],
					[
						'title'         => __( 'Others', 'happy-elementor-addons' ),
						'content_type'  => 'plain_content',
						'plain_content' => __( 'Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).', 'happy-elementor-addons' ),
					],
				],
				'title_field'   => '{{{ title }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function __settings_content_controls() {

		$this->start_controls_section(
			'_section_display_settings',
			[
				'label' => __( 'Display Settings', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'switch_direction',
			[
				'label'   => __( 'Switch Direction', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'horizontal' => [
						'title' => __( 'Horizontal', 'happy-elementor-addons' ),
						'icon'  => 'eicon-navigation-horizontal',
					],
					'vertical'   => [
						'title' => __( 'Vertical', 'happy-elementor-addons' ),
						'icon'  => 'eicon-navigation-vertical',
					],
					// 'row' => [
					// 	'title' => __('Horizontal', 'happy-elementor-addons'),
					// 	'icon'  => 'eicon-navigation-horizontal',
					// ],
					// 'column' => [
					// 	'title' => __('Vertical', 'happy-elementor-addons'),
					// 	'icon'  => 'eicon-navigation-vertical',
					// ],
				],
				// 'selectors' => [
				// 	'{{WRAPPER}} .ha-cs-switch-container .ha-cs-switch-wrapper' => 'flex-direction : {{VALUE}}',
				// ],
				'default' => 'horizontal',
				'toggle'  => false,
			]
		);

		// $this->add_responsive_control(
		// 	'switch_direction_class_1',
		// 	[
		// 		'type' => \Elementor\Controls_Manager::HIDDEN,
		// 		'default' => 'horizontal',
		// 		'condition' => [
		// 			'switch_direction' => 'row'
		// 		],
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'switch_direction_class_2',
		// 	[
		// 		'type' => \Elementor\Controls_Manager::HIDDEN,
		// 		'default' => 'vertical',
		// 		'condition' => [
		// 			'switch_direction' => 'column'
		// 		],
		// 	]
		// );

		$this->add_responsive_control(
			'switch_align',
			[
				'label'     => __( 'Switch Alignment', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'     => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'   => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-cs-switch-container' => 'justify-content : {{VALUE}}',
				],
				'default'   => 'center',
				'toggle'    => true,
			]
		);

		$this->add_responsive_control(
			'space_between',
			[
				'label'       => __( 'Space', 'happy-elementor-addons' ),
				'description' => __( 'Set Space between switcher and content section', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => ['px', '%'],
				'range'       => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'     => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors'   => [
					'{{WRAPPER}} .ha-cs-switch-container' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'anim_duration',
			[
				'label'       => __( 'Animation Speed', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set Animation Duration in Millisecond', 'happy-elementor-addons' ),
				'min'         => 100,
				'max'         => 3000,
				'step'        => 100,
				'default'     => 400,
				'selectors'   => [
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-slider:before' => 'transition-duration: {{VALUE}}ms',
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button' => 'transition-duration: {{VALUE}}ms',
					'{{WRAPPER}} .ha-cs-content-wrapper .ha-cs-content-section' => 'transition: transform calc( {{VALUE}}ms / 2 ) ease-in;',
					'{{WRAPPER}} .ha-cs-content-wrapper .ha-cs-content-section' => 'transition: transform calc( {{VALUE}}ms / 2 ) ease-out;',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {
		$this->__switch_style_controls();
		$this->__switcher_control_style_controls();
		$this->__switch_bar_style_controls();
		$this->__content_style_controls();
	}

	protected function __switch_style_controls() {

		$this->start_controls_section(
			'_section_style_switch',
			[
				'label' => __( 'Switch', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'typography',
				'fields_options' => [
					'typography'  => ['default' => 'yes'],
					'font_family' => [
						'default' => 'Nunito',
					],
					'font_weight' => [
						'default' => 'Bold',
					],
				],
				'selector'       => '{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button span, {{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-switch',

			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label'      => __( 'Icon Spacing', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button.ha-cs-icon-left i' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button.ha-cs-icon-right i' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-switch.ha-cs-icon-left i' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-switch.ha-cs-icon-right i' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'label_spacing',
			[
				'label'      => __( 'Title Spacing', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-cs-switch-wrapper.horizontal .ha-cs-switch.primary' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: unset;',
					'body[data-elementor-device-mode="widescreen"] {{WRAPPER}} .ha-cs-switch-wrapper.widescreen-horizontal .ha-cs-switch.primary' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: unset;',
					'body[data-elementor-device-mode="desktop"] {{WRAPPER}} .ha-cs-switch-wrapper.desktop-horizontal .ha-cs-switch.primary' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: unset;',
					'body[data-elementor-device-mode="laptop"] {{WRAPPER}} .ha-cs-switch-wrapper.laptop-horizontal .ha-cs-switch.primary' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: unset;',
					'body[data-elementor-device-mode="tablet_extra"] {{WRAPPER}} .ha-cs-switch-wrapper.tablet-extra-horizontal .ha-cs-switch.primary' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: unset;',
					'body[data-elementor-device-mode="tablet"] {{WRAPPER}} .ha-cs-switch-wrapper.tablet-horizontal .ha-cs-switch.primary' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: unset;',
					'body[data-elementor-device-mode="mobile_extra"] {{WRAPPER}} .ha-cs-switch-wrapper.mobile-extra-horizontal .ha-cs-switch.primary' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: unset;',
					'body[data-elementor-device-mode="mobile"] {{WRAPPER}} .ha-cs-switch-wrapper.mobile-horizontal .ha-cs-switch.primary' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: unset;',

					'{{WRAPPER}} .ha-cs-switch-wrapper.horizontal .ha-cs-switch.secondary' => 'margin-left: {{SIZE}}{{UNIT}}; margin-top: unset;',
					'body[data-elementor-device-mode="widescreen"] {{WRAPPER}} .ha-cs-switch-wrapper.widescreen-horizontal .ha-cs-switch.secondary' => 'margin-left: {{SIZE}}{{UNIT}}; margin-top: unset;',
					'body[data-elementor-device-mode="desktop"] {{WRAPPER}} .ha-cs-switch-wrapper.desktop-horizontal .ha-cs-switch.secondary' => 'margin-left: {{SIZE}}{{UNIT}}; margin-top: unset;',
					'body[data-elementor-device-mode="laptop"] {{WRAPPER}} .ha-cs-switch-wrapper.laptop-horizontal .ha-cs-switch.secondary' => 'margin-left: {{SIZE}}{{UNIT}}; margin-top: unset;',
					'body[data-elementor-device-mode="tablet_extra"] {{WRAPPER}} .ha-cs-switch-wrapper.tablet-extra-horizontal .ha-cs-switch.secondary' => 'margin-left: {{SIZE}}{{UNIT}}; margin-top: unset;',
					'body[data-elementor-device-mode="tablet"] {{WRAPPER}} .ha-cs-switch-wrapper.tablet-horizontal .ha-cs-switch.secondary' => 'margin-left: {{SIZE}}{{UNIT}}; margin-top: unset;',
					'body[data-elementor-device-mode="mobile_extra"] {{WRAPPER}} .ha-cs-switch-wrapper.mobile-extra-horizontal .ha-cs-switch.secondary' => 'margin-left: {{SIZE}}{{UNIT}}; margin-top: unset;',
					'body[data-elementor-device-mode="mobile"] {{WRAPPER}} .ha-cs-switch-wrapper.mobile-horizontal .ha-cs-switch.secondary' => 'margin-left: {{SIZE}}{{UNIT}}; margin-top: unset;',

					'{{WRAPPER}} .ha-cs-switch-wrapper.horizontal .ha-cs-switch.primary' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-right: unset;',
					'body[data-elementor-device-mode="widescreen"] {{WRAPPER}} .ha-cs-switch-wrapper.widescreen-vertical .ha-cs-switch.primary' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-right: unset;',
					'body[data-elementor-device-mode="desktop"] {{WRAPPER}} .ha-cs-switch-wrapper.desktop-vertical .ha-cs-switch.primary' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-right: unset;',
					'body[data-elementor-device-mode="laptop"] {{WRAPPER}} .ha-cs-switch-wrapper.laptop-vertical .ha-cs-switch.primary' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-right: unset;',
					'body[data-elementor-device-mode="tablet_extra"] {{WRAPPER}} .ha-cs-switch-wrapper.tablet-extra-vertical .ha-cs-switch.primary' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-right: unset;',
					'body[data-elementor-device-mode="tablet"] {{WRAPPER}} .ha-cs-switch-wrapper.tablet-vertical .ha-cs-switch.primary' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-right: unset;',
					'body[data-elementor-device-mode="mobile_extra"] {{WRAPPER}} .ha-cs-switch-wrapper.mobile-extra-vertical .ha-cs-switch.primary' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-right: unset;',
					'body[data-elementor-device-mode="mobile"] {{WRAPPER}} .ha-cs-switch-wrapper.mobile-vertical .ha-cs-switch.primary' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-right: unset;',

					'{{WRAPPER}} .ha-cs-switch-wrapper.vertical .ha-cs-switch.secondary' => 'margin-top: {{SIZE}}{{UNIT}}; margin-left: unset;',
					'body[data-elementor-device-mode="widescreen"] {{WRAPPER}} .ha-cs-switch-wrapper.widescreen-vertical .ha-cs-switch.secondary' => 'margin-top: {{SIZE}}{{UNIT}}; margin-left: unset;',
					'body[data-elementor-device-mode="desktop"] {{WRAPPER}} .ha-cs-switch-wrapper.desktop-vertical .ha-cs-switch.secondary' => 'margin-top: {{SIZE}}{{UNIT}}; margin-left: unset;',
					'body[data-elementor-device-mode="laptop"] {{WRAPPER}} .ha-cs-switch-wrapper.laptop-vertical .ha-cs-switch.secondary' => 'margin-top: {{SIZE}}{{UNIT}}; margin-left: unset;',
					'body[data-elementor-device-mode="tablet_extra"] {{WRAPPER}} .ha-cs-switch-wrapper.tablet-extra-vertical .ha-cs-switch.secondary' => 'margin-top: {{SIZE}}{{UNIT}}; margin-left: unset;',
					'body[data-elementor-device-mode="tablet"] {{WRAPPER}} .ha-cs-switch-wrapper.tablet-vertical .ha-cs-switch.secondary' => 'margin-top: {{SIZE}}{{UNIT}}; margin-left: unset;',
					'body[data-elementor-device-mode="mobile_extra"] {{WRAPPER}} .ha-cs-switch-wrapper.mobile-extra-vertical .ha-cs-switch.secondary' => 'margin-top: {{SIZE}}{{UNIT}}; margin-left: unset;',
					'body[data-elementor-device-mode="mobile"] {{WRAPPER}} .ha-cs-switch-wrapper.mobile-vertical .ha-cs-switch.secondary' => 'margin-top: {{SIZE}}{{UNIT}}; margin-left: unset;',
				],
				'condition'  => [
					'select_design' => ['round', 'round-2', 'square', 'square-2'],
				],
			]
		);

		$this->start_controls_tabs(
			'label_tabs'
		);

		$this->start_controls_tab(
			'label_style_normal',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Title Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button span' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-switch span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Icon Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button div > i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-switch div > i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button div > svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-switch div > svg' => 'fill: {{VALUE}}',

				],
			]
		);

		$this->add_control(
			'title_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'select_design' => ['button'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'title_border_normal',
				'label'     => __( 'Border', 'happy-elementor-addons' ),
				'selector'  => '{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button',
				'condition' => [
					'select_design' => ['button'],
				],
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'default'    => [
					'top'      => '30',
					'right'    => '30',
					'bottom'   => '30',
					'left'     => '30',
					'unit'     => 'px',
					'isLinked' => 'true',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'select_design' => ['button'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'title_box_shadow',
				'label'     => __( 'Box Shadow', 'happy-elementor-addons' ),
				'selector'  => '{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button',
				'condition' => [
					'select_design' => ['button'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'label_style_active',
			[
				'label' => __( 'Active', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'title_color_active',
			[
				'label'     => __( 'Title Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button.active span' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-switch.active span' => 'color: {{VALUE}}',

				],
			]
		);

		$this->add_control(
			'icon_color_active',
			[
				'label'     => __( 'Icon Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button.active i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-switch.active i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button.active svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-switch.active svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'title_bg_color_active',
			[
				'label'     => __( 'Background Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button.active' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'select_design' => ['button'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'title_border_active',
				'label'     => __( 'Border', 'happy-elementor-addons' ),
				'selector'  => '{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button.active',
				'condition' => [
					'select_design' => ['button'],
				],
			]
		);

		$this->add_control(
			'button_border_radius_active',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'default'    => [
					'top'      => '30',
					'right'    => '30',
					'bottom'   => '30',
					'left'     => '30',
					'unit'     => 'px',
					'isLinked' => 'true',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button.active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'select_design' => ['button'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'title_box_shadow_active',
				'label'     => __( 'Box Shadow', 'happy-elementor-addons' ),
				'selector'  => '{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button.active',
				'condition' => [
					'select_design' => ['button'],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => __( 'Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'default'    => [
					'top'      => '10',
					'right'    => '20',
					'bottom'   => '10',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => '',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'select_design' => ['button'],
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label'      => __( 'Margin', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'default'    => [
					'top'      => '5',
					'right'    => '5',
					'bottom'   => '5',
					'left'     => '5',
					'unit'     => 'px',
					'isLinked' => 'true',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-cs-switch-wrapper .ha-cs-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'select_design' => ['button'],
				],
			]
		);

		$this->add_control(
			'box_style',
			[
				'label'     => __( 'Box Style', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'select_design' => ['button'],
				],
			]
		);

		$this->add_control(
			'title_box_bg_color',
			[
				'label'     => __( 'Box Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFF',
				'selectors' => [
					'{{WRAPPER}} .ha-cs-switch-wrapper' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'select_design' => ['button'],
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label'      => __( 'Box Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'default'    => [
					'top'      => '5',
					'right'    => '5',
					'bottom'   => '5',
					'left'     => '5',
					'unit'     => 'px',
					'isLinked' => 'true',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-cs-switch-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'select_design' => ['button'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'box_border',
				'label'     => __( 'Border', 'happy-elementor-addons' ),
				'selector'  => '{{WRAPPER}} .ha-cs-switch-wrapper',
				'condition' => [
					'select_design' => ['button'],
				],
			]
		);

		$this->add_control(
			'box_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'default'    => [
					'top'      => '50',
					'right'    => '50',
					'bottom'   => '50',
					'left'     => '50',
					'unit'     => 'px',
					'isLinked' => 'true',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-cs-switch-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'select_design' => ['button'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_box_shadow',
				'label'    => __( 'Box Shadow', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-cs-switch-wrapper',
			]
		);

		$this->end_controls_section();
	}

	protected function __switcher_control_style_controls() {

		$this->start_controls_section(
			'_section_style_switcher_control',
			[
				'label'     => __( 'Switcher Control', 'happy-elementor-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'select_design' => ['round', 'round-2', 'square', 'square-2'],
				],
			]
		);

		$this->add_control(
			'switch_style_size',
			[
				'label'      => __( 'Switch Size (px)', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 200,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-cs-switch-container .ha-cs-switch.ha-input-label' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'switch_style_tabs'
		);

		$this->start_controls_tab(
			'switch_style_normal_tab',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'switch_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-input-label .ha-cs-slider:before' => 'background-color : {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'switch_background_color',
			[
				'label'     => __( 'Background Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-input-label .ha-cs-slider' => 'background-color : {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'switch_style_active_tab',
			[
				'label' => __( 'Active', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'switch_color_active',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-input-label input:checked+.ha-cs-slider:before' => 'background-color : {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'switch_background_color_active',
			[
				'label'     => __( 'Background Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-input-label input:checked+.ha-cs-slider' => 'background-color : {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __switch_bar_style_controls() {

		$this->start_controls_section(
			'_section_style_switch_bar',
			[
				'label' => __( 'Switch Bar', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'title_section_padding',
			[
				'label'      => __( 'Section Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-content-switcher-wrapper .ha-cs-switch-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'title_section_bg_color',
				'label'    => __( 'Background', 'happy-elementor-addons' ),
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ha-content-switcher-wrapper .ha-cs-switch-container',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'title_section_border',
				'label'    => __( 'Border', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-content-switcher-wrapper .ha-cs-switch-container',
			]
		);

		$this->add_control(
			'title_section_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-content-switcher-wrapper .ha-cs-switch-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'title_section_shadow',
				'label'    => __( 'Box Shadow', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-content-switcher-wrapper .ha-cs-switch-container',
			]
		);

		$this->end_controls_section();
	}

	protected function __content_style_controls() {

		$this->start_controls_section(
			'_section_style_content',
			[
				'label' => __( 'Content', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __( 'Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-content-switcher-wrapper .ha-cs-content-container .ha-cs-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'content_typography',
				'fields_options' => [
					'typography'  => [
						'default' => 'yes',
					],
					'font_family' => [
						'default' => 'Nunito',
					],
					'font_weight' => [
						'default' => '600',
					],
				],
				'selector'       => '{{WRAPPER}} .ha-content-switcher-wrapper .ha-cs-content-section',
			]
		);

		$this->add_control(
			'content_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-cs-content-container .ha-cs-content-wrapper .ha-cs-content-section' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'content_box_bg_color',
				'label'    => __( 'Background', 'happy-elementor-addons' ),
				'types'    => ['classic', 'gradient', 'video'],
				'selector' => '{{WRAPPER}} .ha-content-switcher-wrapper .ha-cs-content-container .ha-cs-content-wrapper',
			]
		);

		$this->add_control(
			'contetn_box_alignment',
			[
				'label'     => __( 'Alignment', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .ha-content-switcher-wrapper' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'content_box',
				'label'    => __( 'Border', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-content-switcher-wrapper .ha-cs-content-container .ha-cs-content-wrapper',

			]
		);
		$this->add_control(
			'content_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-content-switcher-wrapper .ha-cs-content-container .ha-cs-content-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_box_shadow',
				'label'    => __( 'Box Shadow', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-content-switcher-wrapper .ha-cs-content-container .ha-cs-content-wrapper',

			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$primary   = ( isset( $settings['content_list'][0] ) ? $settings['content_list'][0] : '' );
		$secondary = ( isset( $settings['content_list'][1] ) ? $settings['content_list'][1] : '' );

		$class_for_direction  = (!empty($settings['switch_direction_widescreen']))? 'widescreen-' . $settings['switch_direction_widescreen']: '';
		$class_for_direction .= (!empty($settings['switch_direction']))? ' desktop-' . $settings['switch_direction']: '';
		$class_for_direction .= (!empty($settings['switch_direction']))? ' ' . $settings['switch_direction']: '';
		$class_for_direction .= (!empty($settings['switch_direction_laptop']))? ' laptop-' . $settings['switch_direction_laptop']: '';
		$class_for_direction .= (!empty($settings['switch_direction_tablet_extra']))? ' tablet-extra-' . $settings['switch_direction_tablet_extra']: '';
		$class_for_direction .= (!empty($settings['switch_direction_tablet']))? ' tablet-' . $settings['switch_direction_tablet']: '';
		$class_for_direction .= (!empty($settings['switch_direction_mobile_extra']))? ' mobile-extra-' . $settings['switch_direction_mobile_extra']: '';
		$class_for_direction .= (!empty($settings['switch_direction_mobile']))? ' mobile-' . $settings['switch_direction_mobile']: '';

		?>
		<div class="ha-content-switcher-wrapper ha-cs-design-<?php echo esc_attr( $settings['select_design'] ); ?>" data-design-type="<?php echo esc_attr( $settings['select_design'] ); ?>">
			<div class="ha-cs-switch-container">
				<div class="ha-cs-switch-wrapper <?php echo esc_attr($class_for_direction); ?>">
					<?php if ( $settings['select_design'] == 'button' ) : ?>
						<?php foreach ( $settings['content_list'] as $i => $item ) : ?>
							<button class="ha-cs-button <?php echo esc_attr( ( $item['active'] == 'yes' ) ? 'active' : '' ); ?> ha-cs-icon-<?php echo esc_attr( $item['icon_align'] ); ?>" data-content-id="<?php echo esc_attr( $item['_id'] ); ?>">
								<?php if ( ! empty( $item['icon']['value'] ) ) : ?>
									<div class="ha-cs-icon-wrapper"><?php ha_render_icon( $item, null, 'icon' ); ?></div>
								<?php endif; ?>
								<span><?php echo esc_html( $item['title'] ); ?></span>
							</button>
						<?php endforeach; ?>
						<?php
					else :
						?>
						<div class="ha-cs-switch primary <?php echo esc_attr( ( $primary['active'] == 'yes' ) ? 'active' : '' ); ?> ha-cs-icon-<?php echo esc_attr( $primary['icon_align'] ); ?>" data-content-id="<?php echo esc_attr( $primary['_id'] ); ?>">
							<?php if ( ! empty( $primary['icon']['value'] ) ) : ?>
								<div class="ha-cs-icon-wrapper"><?php ha_render_icon( $primary, null, 'icon' ); ?></div>
							<?php endif; ?>
							<span><?php echo esc_html( $primary['title'] ); ?></span>
						</div>

						<label class="ha-cs-switch ha-input-label">
							<input class="ha-cs-toggle-switch" type="checkbox" <?php echo esc_attr( ( $secondary['active'] == 'yes' ) ? 'checked' : '' ); ?>>
							<span class="ha-cs-slider ha-cs-<?php echo esc_attr( $settings['select_design'] ); ?>"></span>
						</label>

						<div class="ha-cs-switch secondary <?php echo esc_attr( ( $secondary['active'] == 'yes' ) ? 'active' : '' ); ?> ha-cs-icon-<?php echo esc_attr( $secondary['icon_align'] ); ?>" data-content-id="<?php echo esc_attr( $secondary['_id'] ); ?>">
							<?php if ( ! empty( $secondary['icon']['value'] ) ) : ?>
								<div class="ha-cs-icon-wrapper"><?php ha_render_icon( $secondary, null, 'icon' ); ?></div>
							<?php endif; ?>
							<span><?php echo esc_html( $secondary['title'] ); ?></span>
						</div>

					<?php endif; ?>
				</div>
			</div>
			<div class="ha-cs-content-container">
				<div class="ha-cs-content-wrapper <?php echo esc_attr( $class_for_direction ); ?>">
					<?php if ( $settings['select_design'] == 'button' ) : ?>
						<?php foreach ( $settings['content_list'] as $i => $item ) : ?>
							<div id="<?php echo esc_attr( $item['_id'] ); ?>" class="ha-cs-content-section <?php echo esc_attr( ( $item['active'] == 'yes' ) ? 'active' : '' ); ?>">
								<?php
								if ( $item['content_type'] == 'plain_content' ) {
									echo ha_kses_intermediate( $item['plain_content'] );
								} elseif ( $item['content_type'] == 'saved_section' ) {
									$item['saved_section'] = apply_filters('wpml_object_id', $item['saved_section'], 'elementor_library');
									echo ha_elementor()->frontend->get_builder_content_for_display( $item['saved_section'] );
								} elseif ( $item['content_type'] == 'saved_page' ) {
									$item['saved_pages'] = apply_filters('wpml_object_id', $item['saved_pages'], 'elementor_library');
									echo ha_elementor()->frontend->get_builder_content_for_display( $item['saved_pages'] );
								}
								?>
							</div>
						<?php endforeach; ?>
						<?php
					else :
						?>
						<div id="<?php echo esc_attr( $primary['_id'] ); ?>" class="ha-cs-content-section primary <?php echo esc_attr( ( $primary['active'] == 'yes' ) ? 'active' : '' ); ?>">
							<?php
							if ( $primary['content_type'] == 'plain_content' ) {
								echo ha_kses_intermediate( $primary['plain_content'] );
							} elseif ( $primary['content_type'] == 'saved_section' ) {
								$primary['saved_section'] = apply_filters('wpml_object_id', $primary['saved_section'], 'elementor_library');
								echo ha_elementor()->frontend->get_builder_content_for_display( $primary['saved_section'] );
							} elseif ( $primary['content_type'] == 'saved_page' ) {
								$primary['saved_pages'] = apply_filters('wpml_object_id', $primary['saved_pages'], 'elementor_library');
								echo ha_elementor()->frontend->get_builder_content_for_display( $primary['saved_pages'] );
							}
							?>
						</div>

						<div id="<?php echo esc_attr( $secondary['_id'] ); ?>" class="ha-cs-content-section secondary <?php echo esc_attr( ( $secondary['active'] == 'yes' ) ? 'active' : '' ); ?>">
							<?php
							if ( $secondary['content_type'] == 'plain_content' ) {
								echo ha_kses_intermediate( $secondary['plain_content'] );
							} elseif ( $secondary['content_type'] == 'saved_section' ) {
								$secondary['saved_section'] = apply_filters('wpml_object_id', $secondary['saved_section'], 'elementor_library');
								echo ha_elementor()->frontend->get_builder_content_for_display( $secondary['saved_section'] );
							} elseif ( $secondary['content_type'] == 'saved_page' ) {
								$secondary['saved_pages'] = apply_filters('wpml_object_id', $secondary['saved_pages'], 'elementor_library');
								echo ha_elementor()->frontend->get_builder_content_for_display( $secondary['saved_pages'] );
							}
							?>
						</div>

					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}

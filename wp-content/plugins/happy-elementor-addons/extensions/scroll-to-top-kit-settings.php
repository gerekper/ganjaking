<?php
namespace Happy_Addons\Elementor\Extension;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Tab_Base;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

class Scroll_To_Top_Kit_Setings extends Tab_Base {

	public function get_id() {
		return 'ha-scroll-to-top-kit-settings';
	}

	public function get_title() {
		return __( 'Scroll to Top', 'happy-elementor-addons' ) . '<span style="margin: 0 15px 0 0;display: inline-block;float: right;">' . ha_get_section_icon() . '</spna>';
	}

	public function get_icon() {
		return 'hm hm-scroll-top';
	}

	public function get_help_url() {
		return '';
	}

	public function get_group() {
		return 'settings';
	}

	protected function register_tab_controls() {
		$this->start_controls_section(
			'ha_scroll_to_top_kit_section',
			[
				'tab'   => 'ha-scroll-to-top-kit-settings',
				'label' => __( 'Scroll to Top', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'ha_scroll_to_top_global',
			[
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Enable Scroll To Top', 'happy-elementor-addons' ),
				'default'   => '',
				'label_on'  => __( 'Show', 'happy-elementor-addons' ),
				'label_off' => __( 'Hide', 'happy-elementor-addons' ),
			]
		);

		// TODO: For Pro 3.6.0, convert this to the breakpoints utility method introduced in core 3.5.0.
		$breakpoints    = ha_elementor()->breakpoints->get_active_breakpoints();
		$device_default = [];
		foreach ( $breakpoints as $breakpoint_key => $breakpoint ) {
			$device_default[ $breakpoint_key . '_default' ] = 'yes';
		}
		$device_default['desktop_default'] = 'yes';
		$this->add_responsive_control(
			'ha_scroll_to_top_responsive_visibility',
			array_merge(
				[
					'type'                 => Controls_Manager::SWITCHER,
					'label'                => __( 'Responsive Visibility', 'happy-elementor-addons' ),
					'default'              => 'yes',
					'return_value'         => 'yes',
					'label_on'             => __( 'Show', 'happy-elementor-addons' ),
					'label_off'            => __( 'Hide', 'happy-elementor-addons' ),
					'selectors_dictionary' => [
						''    => 'visibility: hidden; opacity: 0;',
						'yes' => 'visibility: visible; opacity: 1;',
					],
					'selectors'            => [
						'body[data-elementor-device-mode="widescreen"] .ha-scroll-to-top-wrap,
						body[data-elementor-device-mode="widescreen"] .ha-scroll-to-top-wrap.edit-mode,
						body[data-elementor-device-mode="widescreen"] .ha-scroll-to-top-wrap.single-page-off' => '{{VALUE}}',

						'body[data-elementor-device-mode="desktop"] .ha-scroll-to-top-wrap,
						body[data-elementor-device-mode="desktop"] .ha-scroll-to-top-wrap.edit-mode,
						body[data-elementor-device-mode="desktop"] .ha-scroll-to-top-wrap.single-page-off' => '{{VALUE}}',

						'body[data-elementor-device-mode="laptop"] .ha-scroll-to-top-wrap,
						body[data-elementor-device-mode="laptop"] .ha-scroll-to-top-wrap.edit-mode,
						body[data-elementor-device-mode="laptop"] .ha-scroll-to-top-wrap.single-page-off' => '{{VALUE}}',

						'body[data-elementor-device-mode="tablet_extra"] .ha-scroll-to-top-wrap,
						body[data-elementor-device-mode="tablet_extra"] .ha-scroll-to-top-wrap.edit-mode,
						body[data-elementor-device-mode="tablet_extra"] .ha-scroll-to-top-wrap.single-page-off' => '{{VALUE}}',

						'body[data-elementor-device-mode="tablet"] .ha-scroll-to-top-wrap,
						body[data-elementor-device-mode="tablet"] .ha-scroll-to-top-wrap.edit-mode,
						body[data-elementor-device-mode="tablet"] .ha-scroll-to-top-wrap.single-page-off' => '{{VALUE}}',

						'body[data-elementor-device-mode="mobile_extra"] .ha-scroll-to-top-wrap,
						body[data-elementor-device-mode="mobile_extra"] .ha-scroll-to-top-wrap.edit-mode,
						body[data-elementor-device-mode="mobile_extra"] .ha-scroll-to-top-wrap.single-page-off' => '{{VALUE}}',

						'body[data-elementor-device-mode="mobile"] .ha-scroll-to-top-wrap,
						body[data-elementor-device-mode="mobile"] .ha-scroll-to-top-wrap.edit-mode,
						body[data-elementor-device-mode="mobile"] .ha-scroll-to-top-wrap.single-page-off' => '{{VALUE}}',
					],
					'condition'            => [
						'ha_scroll_to_top_global' => 'yes',
					],
				],
				$device_default
			)
		);

		$this->add_control(
			'ha_scroll_to_top_position_text',
			[
				'label'       => esc_html__( 'Position', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'bottom-right',
				'label_block' => false,
				'options'     => [
					'bottom-left'  => esc_html__( 'Bottom Left', 'happy-elementor-addons' ),
					'bottom-right' => esc_html__( 'Bottom Right', 'happy-elementor-addons' ),
				],
				'separator'   => 'before',
				'condition'   => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_position_bottom',
			[
				'label'      => __( 'Bottom', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'em' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button' => 'bottom: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_position_left',
			[
				'label'      => __( 'Left', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'em' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'.ha-scroll-to-top-button' => 'left: 15px',
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button' => 'left: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'ha_scroll_to_top_global'        => 'yes',
					'ha_scroll_to_top_position_text' => 'bottom-left',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_position_right',
			[
				'label'      => __( 'Right', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'em' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button' => 'right: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'ha_scroll_to_top_global'        => 'yes',
					'ha_scroll_to_top_position_text' => 'bottom-right',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_button_width',
			[
				'label'      => __( 'Width', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button' => 'width: {{SIZE}}{{UNIT}};',
				],
				'separator'  => 'before',
				'condition'  => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_button_height',
			[
				'label'      => __( 'Height', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_z_index',
			[
				'label'      => __( 'Z Index', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 9999,
						'step' => 10,
					],
				],
				'selectors'  => [
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button' => 'z-index: {{SIZE}}',
				],
				'condition'  => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_button_opacity',
			[
				'label'     => __( 'Opacity', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_media_type',
			[
				'label'          => __( 'Media Type', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::CHOOSE,
				'label_block'    => false,
				'options'        => [
					'icon'  => [
						'title' => __( 'Icon', 'happy-elementor-addons' ),
						'icon'  => 'eicon-star',
					],
					'image' => [
						'title' => __( 'Image', 'happy-elementor-addons' ),
						'icon'  => 'eicon-image',
					],
					'text'  => [
						'title' => __( 'Text', 'happy-elementor-addons' ),
						'icon'  => 'eicon-animation-text',
					],
				],
				'default'        => 'icon',
				'separator'      => 'before',
				'toggle'         => false,
				'style_transfer' => true,
				'condition'      => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_button_icon',
			[
				'label'      => esc_html__( 'Icon', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::ICONS,
				'show_label' => false,
				'default'    => [
					'value'   => 'fas fa-chevron-up',
					'library' => 'fa-solid',
				],
				'condition'  => [
					'ha_scroll_to_top_global'     => 'yes',
					'ha_scroll_to_top_media_type' => 'icon',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_button_image',
			[
				'label'      => __( 'Image', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::MEDIA,
				'show_label' => false,
				'dynamic'    => [
					'active' => true,
				],
				'condition'  => [
					'ha_scroll_to_top_global'     => 'yes',
					'ha_scroll_to_top_media_type' => 'image',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_button_text',
			[
				'label'       => __( 'Text', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::TEXT,
				'show_label'  => false,
				'label_block' => true,
				'default'     => 'Top',
				'condition'   => [
					'ha_scroll_to_top_global'     => 'yes',
					'ha_scroll_to_top_media_type' => 'text',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_button_icon_size',
			[
				'label'      => __( 'Size', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button i' => 'font-size: {{SIZE}}{{UNIT}};',
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button img' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'ha_scroll_to_top_global'      => 'yes',
					'ha_scroll_to_top_media_type!' => 'text',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'ha_scroll_to_top_button_text_typo',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector'  => '.ha-scroll-to-top-wrap .ha-scroll-to-top-button span',
				'condition' => [
					'ha_scroll_to_top_global'     => 'yes',
					'ha_scroll_to_top_media_type' => 'text',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'ha_scroll_to_top_button_border',
				'exclude'   => ['color'], //remove border color
				'selector'  => '{{WRAPPER}} .ha-scroll-to-top-wrap .ha-scroll-to-top-button',
				'condition' => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->start_controls_tabs(
			'ha_scroll_to_top_tabs',
			[
				'separator' => 'before',
				'condition' => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->start_controls_tab(
			'ha_scroll_to_top_tab_normal',
			[
				'label'     => __( 'Normal', 'happy-elementor-addons' ),
				'condition' => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_button_icon_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button i' => 'color: {{VALUE}}',
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button span' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ha_scroll_to_top_global'      => 'yes',
					'ha_scroll_to_top_media_type!' => 'image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ha_scroll_to_top_button_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'exclude'   => [ 'image' ],
				'selector'  => '.ha-scroll-to-top-wrap .ha-scroll-to-top-button',
				'condition' => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_button_border_color',
			[
				'label'     => __( 'Border Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'ha_scroll_to_top_global' => 'yes',
					'ha_scroll_to_top_button_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ha_scroll_to_top_tab_hover',
			[
				'label'     => __( 'Hover', 'happy-elementor-addons' ),
				'condition' => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_button_icon_hvr_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button:hover i' => 'color: {{VALUE}}',
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button:hover span' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ha_scroll_to_top_global'      => 'yes',
					'ha_scroll_to_top_media_type!' => 'image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ha_scroll_to_top_button_bg_hvr_color',
				'types'     => [ 'classic', 'gradient' ],
				'exclude'   => [ 'image' ],
				'selector'  => '.ha-scroll-to-top-wrap .ha-scroll-to-top-button:hover',
				'condition' => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->add_control(
			'ha_scroll_to_top_button_hvr_border_color',
			[
				'label'     => __( 'Border Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button:hover' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'ha_scroll_to_top_global' => 'yes',
					'ha_scroll_to_top_button_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'ha_scroll_to_top_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'.ha-scroll-to-top-wrap .ha-scroll-to-top-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
				'condition'  => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'ha_scroll_to_top_button_box_shadow',
				'exclude'   => [
					'box_shadow_position',
				],
				'selector'  => '.ha-scroll-to-top-wrap .ha-scroll-to-top-button',
				'condition' => [
					'ha_scroll_to_top_global' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}
}

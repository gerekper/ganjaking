<?php

namespace ElementPack\Modules\SvgImage\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use ElementPack\Base\Module_Base;
use ElementPack\Element_Pack_Loader;

if (!defined('ABSPATH')) {
	exit;
}
// Exit if accessed directly

class Svg_Image extends Module_Base {

	public function get_name() {
		return 'bdt-svg-image';
	}

	public function get_title() {
		return BDTEP . esc_html__('SVG', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-svg-image';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['image', 'svg image', 'svg'];
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			if (true == is_ep_pro()) {
				return ['draw-svg-plugin-js', 'scroll-trigger-js', 'magic-scroll-js', 'magic-scroll-animation-js', 'ep-scripts'];
			} else {
				return ['ep-scripts'];
			}
		} else {
			if (true == is_ep_pro()) {
				return ['gsap', 'draw-svg-plugin-js', 'magic-scroll-js', 'magic-scroll-animation-js', 'scroll-trigger-js', 'ep-svg-image'];
			} else {
				return ['ep-svg-image'];
			}
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/XRbjpcp5dJ0';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_image',
			[
				'label' => esc_html__('SVG', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'image',
			[
				'label'   => esc_html__('Choose SVG', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => BDTEP_ASSETS_URL . 'images/crane.svg',
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'     => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-svg-image' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'caption_source',
			[
				'label'   => esc_html__('Caption', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'none'       => esc_html__('None', 'bdthemes-element-pack'),
					'attachment' => esc_html__('Attachment Caption', 'bdthemes-element-pack'),
					'custom'     => esc_html__('Custom Caption', 'bdthemes-element-pack'),
				],
				'default' => 'none',
			]
		);

		$this->add_control(
			'caption',
			[
				'label'       => esc_html__('Custom Caption', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__('Enter your image caption', 'bdthemes-element-pack'),
				'condition'   => [
					'caption_source' => 'custom',
				],
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'link_to',
			[
				'label'   => esc_html__('Link', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'   => esc_html__('None', 'bdthemes-element-pack'),
					'file'   => esc_html__('Media File', 'bdthemes-element-pack'),
					'custom' => esc_html__('Custom URL', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label'       => esc_html__('Link', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => esc_html__('https://your-link.com', 'bdthemes-element-pack'),
				'condition'   => [
					'link_to' => 'custom',
				],
				'show_label'  => false,
			]
		);

		$this->add_control(
			'open_lightbox',
			[
				'label'     => esc_html__('Lightbox', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'yes',
				'options'   => [
					'yes' => esc_html__('Yes', 'bdthemes-element-pack'),
					'no'  => esc_html__('No', 'bdthemes-element-pack'),
				],
				'condition' => [
					'link_to' => 'file',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label'   => esc_html__('View', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();

		
		$this->start_controls_section(
			'section_svg_additionl',
			[
				'label' => esc_html__('SVG Animation', 'bdthemes-element-pack'),
			]
		);

		if(true === is_ep_pro()) {

		$this->add_control(
			'svg_image_draw',
			[
				'label'              => esc_html__('Draw SVG', 'bdthemes-element-pack') . BDTEP_NC,
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__('Yes', 'bdthemes-element-pack'),
				'label_off'          => esc_html__('No', 'bdthemes-element-pack'),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'render_type'        => 'template',
				'separator'          => 'before'
			]
		);

		$this->add_control(
			'svg_image_drawer_type',
			[
				'label'              => esc_html__('Drawer Type', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SELECT,
				'options'            => [
					'hover'     => esc_html__(' On Hover', 'bdthemes-element-pack'),
					'viewport'  => esc_html__('On Scroll', 'bdthemes-element-pack'),
					'automatic' => esc_html__('Automatic', 'bdthemes-element-pack'),
				],
				'default'            => 'hover',
				'frontend_available' => true,
				'render_type'        => 'template',
				'condition' => [
					'svg_image_draw' => 'yes'
				]

			]
		);
		$this->add_control(
			'svg_image_animate_trigger',
			[
				'label'              => esc_html__('When the draw should start?', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SELECT,
				'options'            => [
					'top'    => esc_html__('Top of Viewport Hits The Widget', 'bdthemes-element-pack'),
					'center' => esc_html__('Center of Viewport Hits The Widget', 'bdthemes-element-pack'),
					'custom' => esc_html__('Custom Offset', 'bdthemes-element-pack'),
				],
				'separator' 		 => 'before',
				'default'            => 'center',
				'label_block'        => true,
				'condition'          => [
					'svg_image_drawer_type' => 'automatic',
					'svg_image_draw' => 'yes'
				],
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'svg_image_anim_rev',
			[
				'label'              => esc_html__('Reset Animation on Scroll Up', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'render_type'        => 'template',
				'default'            => 'yes',
				'condition'          => [
					'svg_image_drawer_type' => 'automatic',
					'svg_image_draw' => 'yes'
				],
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'svg_image_animate_offset',
			[
				'label'              => esc_html__('Offset (%)', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'size' => 50,
					'unit' => '%',
				],
				'frontend_available' => true,
				'condition' => [
					'svg_image_draw' => 'yes',
					'svg_image_drawer_type!' => 'hover'
				]
			]
		);

		$this->add_control(
			'svg_image_repeat',
			[
				'label'         => esc_html__('Repeat', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SWITCHER,
				'label_on'      => esc_html__('Yes', 'bdthemes-element-pack'),
				'label_off'     => esc_html__('No', 'bdthemes-element-pack'),
				'separator'     => 'before',
				'default'       => 'yes',
				'frontend_available' => true,
				'condition'          => [
					'svg_image_drawer_type!' => 'viewport',
					'svg_image_draw' => 'yes'
				],
			]
		);
		$this->add_control(
			'svg_image_yoyo',
			[
				'label'              => esc_html__('Yoyo Effect', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => [
					'svg_image_drawer_type!' => 'viewport',
					'svg_image_draw' => 'yes'
				],
				'default'       => 'yes',
				'return_value'   => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'svg_image_animation_duration',
			[
				'label'              => esc_html__('Duration', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'description'        => esc_html__('Larger value means longer drawing duration.', 'bdthemes-element-pack'),
				'range'         => [
					'px'        => [
						'min'   => 0,
						'max'   => 500,
						'step'  => 1,
					]
				],
				'default'            => [
					'unit' => 'px',
					'size' => 100,
				],
				'condition' => [
					'svg_image_drawer_type!' => 'viewport',
					'svg_image_draw' => 'yes'
				],
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'svg_image_animation_start_point',
			[
				'label'              => esc_html__('Start Point (%)', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'description'        => esc_html__('Set the point that the SVG should start from.', 'bdthemes-element-pack'),
				'default'            => [
					'unit' => '%',
					'size' => 0,
				],
				'condition' => [
					'svg_image_draw' => 'yes'
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'svg_image_animation_end_point',
			[
				'label'              => esc_html__('End Point (%)', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'description'        => esc_html__('Set the point that the SVG should end at.', 'bdthemes-element-pack'),
				'default'            => [
					'unit' => '%',
					'size' => 100,
				],
				'condition' => [
					'svg_image_draw' => 'yes'
				],
				'frontend_available' => true,
				'separator' 		 => 'after'
			]
		);
		}

		if(true !== is_ep_pro()) {
		$this->add_control(
			'on_hover_animation',
			[
				'label' => esc_html__('On Hover Animation', 'bdthemes-element-pack'),
				'description' => esc_html__('Make sure you select a stroke based svg image, otherwise hover animation will not work.', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'separator'     => 'before',
				// 'condition' => [
				// 	'svg_image_draw!' => 'yes'
				// ]
			]
		);

		$this->add_control(
			'on_hover_reverse_animation',
			[
				'label' => esc_html__('Reverse Animation', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'on_hover_animation' => 'yes',
				],
				'separator'	=> 'after',
			]
		);
		$this->add_control(
			'svg_parallax_effects_show',
			[
				'label'       => esc_html__('Stroke Parallax Animation', 'bdthemes-element-pack'),
				'description' => esc_html__('Make sure you select a stroke based svg image, otherwise parallax stroke animation will not work.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'separator' => 'before',
				// 'condition' => [
				// 	'svg_image_draw!' => 'yes'
				// ]
			]
		);

		$this->add_control(
			'parallax_effects_stroke_value',
			[
				'label'       => esc_html__('Stroke Start Point', 'bdthemes-element-pack'),
				'description' => esc_html__('Set your stroke start value where from you start the stroke parallax.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'%' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'default'     => [
					'unit' => '%',
					'size' => 0,
				],
				'condition'   => [
					'svg_parallax_effects_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_effects_viewport_value',
			[
				'label'     => esc_html__('Viewport', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0.1,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 0.7,
				],
				'condition' => [
					'svg_parallax_effects_show' => 'yes',
				],
			]
		);
		}

		$this->end_controls_section();
	

		//Style
		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__('SVG', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'svg_color_preserved',
			[
				'label' => esc_html__('Preserved Original Color', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'svg_fill_color',
			[
				'label'     => esc_html__('Fill Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-image svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'svg_stroke_color',
			[
				'label'     => esc_html__('Stroke Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-image svg *' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label'          => esc_html__('Width', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units'     => ['%', 'px', 'vw'],
				'range'          => [
					'%'  => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'      => [
					'{{WRAPPER}} .bdt-svg-image svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'space',
			[
				'label'          => esc_html__('Max Width', 'bdthemes-element-pack') . ' (%)',
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units'     => ['%'],
				'range'          => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'      => [
					'{{WRAPPER}} .bdt-svg-image svg' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'separator_panel_style',
			[
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->start_controls_tabs('image_effects');

		$this->start_controls_tab(
			'normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'opacity',
			[
				'label'     => esc_html__('Opacity', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-svg-image svg' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .bdt-svg-image svg',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'opacity_hover',
			[
				'label'     => esc_html__('Opacity', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-svg-image:hover svg' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .bdt-svg-image:hover svg',
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label'     => esc_html__('Transition Duration', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max'  => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-svg-image svg' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'image_border',
				'selector'  => '{{WRAPPER}} .bdt-svg-image svg',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-svg-image svg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_box_shadow',
				'exclude'  => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .bdt-svg-image svg',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_caption',
			[
				'label'     => esc_html__('Caption', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'caption_source!' => 'none',
				],
			]
		);

		$this->add_control(
			'caption_align',
			[
				'label'     => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'    => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .widget-image-caption' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .widget-image-caption' => 'color: {{VALUE}};',
				],

				// 'scheme' => [

				//     'type' => Schemes\Color::get_type(),

				//     'value' => Schemes\Color::COLOR_3,
				// ],
			]
		);

		$this->add_control(
			'caption_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .widget-image-caption' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'caption_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .widget-image-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'caption_typography',
				'selector' => '{{WRAPPER}} .widget-image-caption',
				//'scheme' => Schemes\Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'caption_text_shadow',
				'selector' => '{{WRAPPER}} .widget-image-caption',
			]
		);

		$this->add_responsive_control(
			'caption_space',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .widget-image-caption' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function has_caption($settings) {
		return (!empty($settings['caption_source']) && 'none' !== $settings['caption_source']);
	}

	private function get_caption($settings) {
		$caption = '';

		if (!empty($settings['caption_source'])) {

			switch ($settings['caption_source']) {
				case 'attachment':
					$caption = wp_get_attachment_caption($settings['image']['id']);
					break;
				case 'custom':
					$caption = !Utils::is_empty($settings['caption']) ? $settings['caption'] : '';
			}
		}

		return $caption;
	}

	public function render_svg() {
		$settings = $this->get_settings_for_display();
		$svg_file = file_get_contents($settings['image']['url'], true);
		echo $svg_file;
	}

	public function render_image() {
		$settings = $this->get_settings_for_display();

		if (true !== is_ep_pro()) {
			if ($settings['on_hover_animation']) {
				$this->add_render_attribute('svg-image', 'class', 'bdt-animation-stroke');
				$this->add_render_attribute('svg-image', 'data-bdt-svg', 'stroke-animation: true');
			}

			if ($settings['on_hover_reverse_animation']) {
				$this->add_render_attribute('svg-image', 'class', 'bdt-animation-reverse');
			}
		}

		if ($settings['svg_color_preserved']) {
			$this->add_render_attribute('svg-image', 'class', 'bdt-preserve');
		}

		$this->add_render_attribute('svg-image', 'data-bdt-svg', '');

		if ($settings['image']['id']) {
			$image = wp_get_attachment_image_src($settings['image']['id'], 'full');
			printf('<img %3$s src="%1$s" alt="%2$s">', esc_url($image[0]), esc_html(get_the_title()), $this->get_render_attribute_string('svg-image'));
		} else {
			printf('<img %3$s src="%1$s" alt="%2$s">', BDTEP_ASSETS_URL . 'images/crane.svg', esc_html(get_the_title()), $this->get_render_attribute_string('svg-image'));
		}
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if (empty($settings['image']['url'])) {
			return;
		}
		$has_caption = $this->has_caption($settings);
		$this->add_render_attribute('wrapper', 'class', 'elementor-image bdt-svg-image bdt-animation-toggle');

		if (true !== is_ep_pro()) {
			$parallax_stroke   = 100 - (isset($settings['parallax_effects_stroke_value']['size']) ? $settings['parallax_effects_stroke_value']['size'] : 0);
			$parallax_viewport = (isset($settings['parallax_effects_viewport_value']['size']) ? $settings['parallax_effects_viewport_value']['size'] : 0.7);
			if ($settings['svg_parallax_effects_show']) {
				$this->add_render_attribute('wrapper', 'bdt-parallax', 'stroke: ' . $parallax_stroke . '%;');
				$this->add_render_attribute('wrapper', 'bdt-parallax', 'viewport: ' . $parallax_viewport . ';');
			}
		}

		if (!empty($settings['shape'])) {
			$this->add_render_attribute('wrapper', 'class', 'elementor-image-shape-' . $settings['shape']);
		}
		$link = $this->get_link_url($settings);
		if ($link) {

			$this->add_render_attribute('link', 'data-elementor-open-lightbox', 'no');

			if ('yes' == $settings['open_lightbox']) {
				$this->add_render_attribute('wrapper', 'bdt-lightbox', '');
			}

			if (Element_Pack_Loader::elementor()->editor->is_edit_mode()) {
				$this->add_render_attribute('link', [
					'class' => 'elementor-clickable',
				]);
			}

			$this->add_link_attributes('link', $link);
		}
		?>
		<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
			<?php if ($has_caption) : ?>
				<figure class="wp-caption">
				<?php endif; ?>
				<?php if ($link) : ?>
					<a <?php echo $this->get_render_attribute_string('link'); ?>>
					<?php endif; ?>
					<?php if (isset($settings['svg_image_draw']) && 'yes' === $settings['svg_image_draw']) {
						$this->render_svg();
					} else {
						$this->render_image();
					} ?>
					<?php if ($link) : ?>
					</a>
				<?php endif; ?>
				<?php
				if ($has_caption) : ?>
					<figcaption class="widget-image-caption wp-caption-text">
						<?php echo $this->get_caption($settings); ?>
					</figcaption>
				<?php endif; ?>
				<?php
				if ($has_caption) : ?>
				</figure>
			<?php endif; ?>
		</div>
<?php
	}

	private function get_link_url($settings) {

		if ('none' === $settings['link_to']) {
			return false;
		}

		if ('custom' === $settings['link_to']) {

			if (empty($settings['link']['url'])) {
				return false;
			}

			return $settings['link'];
		}

		return [
			'url' => $settings['image']['url'],
		];
	}
}

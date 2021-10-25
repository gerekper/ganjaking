<?php

namespace MasterAddons\Addons;

use \Elementor\Widget_Base;
use \Elementor\Utils;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Box_Shadow;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Tooltip extends Widget_Base
{

	public function get_name()
	{
		return 'ma-tooltip';
	}

	public function get_title()
	{
		return esc_html__('Tooltip', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-tools';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_keywords()
	{
		return ['tooltip', 'tooltips', 'image tooltips', 'icon tooltip', 'icons', 'hover content', 'content'];
	}

	public function get_style_depends()
	{
		return [
			'jltma-bootstrap',
			'font-awesome-5-all',
			'font-awesome-4-shim'
		];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/tooltip/';
	}


	protected function _register_controls()
	{

		$this->start_controls_section(
			'tooltip_button_content',
			[
				'label' => __('Content Settings', MELA_TD),
			]
		);


		$this->add_control(
			'ma_el_tooltip_type',
			[
				'label' => esc_html__('Content Type', MELA_TD),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options' => [
					'icon' => [
						'title' => esc_html__('Icon', MELA_TD),
						'icon' => 'fa fa-info',
					],
					'text' => [
						'title' => esc_html__('Text', MELA_TD),
						'icon' => 'fa fa-text-width',
					],
					'image' => [
						'title' => esc_html__('Image', MELA_TD),
						'icon' => 'fa fa-image',
					],
				],
				'default' => 'icon',
			]
		);

		$this->add_control(
			'ma_el_tooltip_content',
			[
				'label' => esc_html__('Content', MELA_TD),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'default' => esc_html__('Hover Me!', MELA_TD),
				'condition' => [
					'ma_el_tooltip_type' => ['text']
				]
			]
		);

		$this->add_control(
			'ma_el_tooltip_icon_content',
			[
				'label'         	=> esc_html__('Icon', MELA_TD),
				'description' 		=> esc_html__('Please choose an icon from the list.', MELA_TD),
				'type'          	=> Controls_Manager::ICONS,
				'fa4compatibility' 	=> 'icon',
				'default'       	=> [
					'value'     => 'fab fa-linux',
					'library'   => 'brand',
				],
				'render_type'      => 'template',
				'condition' => [
					'ma_el_tooltip_type' => ['icon']
				]
			]
		);

		$this->add_control(
			'ma_el_tooltip_img_content',
			[
				'label' => esc_html__('Image', MELA_TD),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'ma_el_tooltip_type' => ['image']
				]
			]
		);

		$this->add_control(
			'tooltip_button_img',
			[
				'label' => __('Image', MELA_TD),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'tooltip_type' => ['image']
				]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'tooltip_button_imgsize',
				'default' => 'large',
				'separator' => 'none',
				'condition' => [
					'tooltip_type' => ['image']
				]
			]
		);

		$this->add_control(
			'tooltip_style_section_align',
			[
				'label' => __('Alignment', MELA_TD),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', MELA_TD),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __('Center', MELA_TD),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __('Right', MELA_TD),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'prefix_class' => 'ma-el-tooltip-align-',
			]
		);

		$this->add_control(
			'ma_el_tooltip_enable_link',
			[
				'label' => __('Show Link', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'ma_el_tooltip_link',
			[
				'label' => __('Link', MELA_TD),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', MELA_TD),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
				'condition' => [
					'ma_el_tooltip_enable_link' => 'yes',
				]
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'tooltip_options',
			[
				'label' => __('Tooltip Options', MELA_TD),
			]
		);
		$this->add_control(
			'ma_el_tooltip_text',
			[
				'label' => esc_html__('Tooltip Text', MELA_TD),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'default' => esc_html__('These are some dummy tooltip contents.', MELA_TD),
				'dynamic' => ['active' => true]
			]
		);

		$this->add_control(
			'ma_el_tooltip_direction',
			[
				'label'         => esc_html__('Direction', MELA_TD),
				'type'          => Controls_Manager::SELECT,
				'default'       => 'tooltip-right',
				'label_block'   => false,
				'options'       => [
					'tooltip-left'      => esc_html__('Left', MELA_TD),
					'tooltip-right'     => esc_html__('Right', MELA_TD),
					'tooltip-top'       => esc_html__('Top', MELA_TD),
					'tooltip-bottom'    => esc_html__('Bottom', MELA_TD),
				],
			]
		);


		$this->add_control(
			'ma_el_tooltip_visible_hover',
			[
				'label' 		=> __('Visible on Hover', MELA_TD),
				'type' 			=> Controls_Manager::SWITCHER,
				'label_on' 		=> __('Yes', MELA_TD),
				'label_off' 	=> __('No', MELA_TD),
				'return_value' 	=> 'yes',
				'default' 		=> 'no',
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item:hover .ma-el-tooltip-text' => 'visibility: visible;opacity: 1; display:block;',
				]

			]
		);

		$this->end_controls_section();





		/**
		 * Content Tab: Docs Links
		 */
		$this->start_controls_section(
			'jltma_section_help_docs',
			[
				'label' => esc_html__('Help Docs', MELA_TD),
			]
		);


		$this->add_control(
			'help_doc_1',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/tooltip/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/adding-tooltip-in-elementor-editor/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=Av3eTae9vaE" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();



		





		// Style tab section
		$this->start_controls_section(
			'tooltip_style_section',
			[
				'label' => __('General Styles', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'ma_el_tooltip_content_width',
			[
				'label' => __('Content Width', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => ['px', '%'],
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-content' => 'width: {{SIZE}}{{UNIT}};',
				]
			]
		);
		$this->add_control(
			'ma_el_tooltip_content_padding',
			[
				'label' => esc_html__('Padding', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'default' => [
					'top' => 20,
					'right' => 20,
					'bottom' => 20,
					'left' => 20,
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->start_controls_tabs('ma_el_tooltip_content_style_tabs');
		// Normal State Tab
		$this->start_controls_tab('ma_el_tooltip_content_normal', ['label' => esc_html__('Normal', MELA_TD)]);
		$this->add_control(
			'ma_el_tooltip_content_bg_color',
			[
				'label' => esc_html__('Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#f9f9f9',
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-content' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ma_el_tooltip_content_color',
			[
				'label' => esc_html__('Text Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#826EFF',
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-content, {{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-content a'
					=> 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ma_el_tooltip_content_shadow',
				'selector' => '{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-content',
			]
		);

		$this->end_controls_tab();

		// Hover State Tab
		$this->start_controls_tab('ma_el_tooltip_content_hover', ['label' => esc_html__('Hover', MELA_TD)]);
		$this->add_control(
			'ma_el_tooltip_content_hover_bg_color',
			[
				'label' => esc_html__('Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#f9f9f9',
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-content:hover' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ma_el_tooltip_content_hover_color',
			[
				'label' => esc_html__('Text Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#212121',
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-content:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-content a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ma_el_tooltip_hover_shadow',
				'selector' => '{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-content:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();



		$this->add_control(
			'ma_el_shadow-separator',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_tooltip_content_typography',
				'selector' => '{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-content',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ma_el_tooltip_hover_border',
				'label' => esc_html__('Border', MELA_TD),
				'selector' => '{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-content',
			]
		);


		$this->add_control(
			'ma_el_tooltip_content_radius',
			[
				'label' => esc_html__('Border Radius', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'default' => [
					'top' => 4,
					'right' => 4,
					'bottom' => 4,
					'left' => 4,
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


		// Tooltip Style tab section
		$this->start_controls_section(
			'ma_el_tooltip_style_section',
			[
				'label' => __('Tooltip Styles', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_tooltip_text_width',
			[
				'label' => __('Tooltip Width', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => ['px', '%'],
				'default' => [
					'unit' => 'px',
					'size' => 200,
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-text' => 'width: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'ma_el_tooltip_bg_color',
			[
				'label' => __('Tooltip Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#826EFF',
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_tooltip_style_color',
			[
				'label' => __('Text Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item .ma-el-tooltip-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'hover_tooltip_content_background',
				'label' => __('Background', MELA_TD),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-text',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'hover_tooltip_content_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-text',
			]
		);

		$this->add_control(
			'ma_el_tooltip_text_padding',
			[
				'label' => __('Padding', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'default' => [
					'top' => 10,
					'right' => 10,
					'bottom' => 10,
					'left' => 10,
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ma_el_tooltip_content_border_radius',
			[
				'label' => esc_html__('Border Radius', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'default' => [
					'top' => 4,
					'right' => 4,
					'bottom' => 4,
					'left' => 4,
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-text' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px !important;',
				],
			]
		);

		// Arrow Tab Start
		$this->add_control(
			'ma_el_tooltip_arrow_color',
			[
				'label' => __('Arrow Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#826EFF',
				'selectors' => [
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item.tooltip-top .ma-el-tooltip-text:after' => 'border-color: {{VALUE}} transparent transparent transparent;',
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item.tooltip-left .ma-el-tooltip-text:after' => 'border-color: transparent transparent transparent {{VALUE}};',
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item.tooltip-bottom .ma-el-tooltip-text:after' => 'border-color: transparent transparent {{VALUE}} transparent;',
					'{{WRAPPER}} .ma-el-tooltip .ma-el-tooltip-item.tooltip-right .ma-el-tooltip-text:after' => 'border-color: transparent {{VALUE}} transparent transparent;',
				],
			]
		);

		$this->end_controls_section();


		
	}


	protected function render()
	{

		$settings = $this->get_settings_for_display();
		$this->add_render_attribute(
			'ma_el_tooltip_wrapper',
			[
				'class' => ['ma-el-tooltip'],
			]
		); ?>

		<div <?php echo $this->get_render_attribute_string('ma_el_tooltip_wrapper'); ?>>
			<div class="ma-el-tooltip-item <?php echo esc_attr($settings['ma_el_tooltip_direction']); ?>">
				<div class="ma-el-tooltip-content">
					<?php if ($settings['ma_el_tooltip_type'] === 'text') : ?>
						<?php if ($settings['ma_el_tooltip_enable_link'] === 'yes') : ?>
							<a href="<?php echo esc_url($settings['ma_el_tooltip_link']['url']); ?>">
							<?php endif; ?>
							<?php echo esc_html($settings['ma_el_tooltip_content']); ?>
							<?php if ($settings['ma_el_tooltip_enable_link'] === 'yes') : ?>
							</a>
						<?php endif; ?>
					<?php elseif ($settings['ma_el_tooltip_type'] === 'icon') : ?>
						<?php if ($settings['ma_el_tooltip_enable_link'] === 'yes') : ?>
							<a href="<?php echo esc_url($settings['ma_el_tooltip_link']['url']); ?>">
							<?php endif; ?>
							<?php Master_Addons_Helper::jltma_fa_icon_picker('fab fa-linux', 'icon', $settings['ma_el_tooltip_icon_content'], 'ma_el_tooltip_icon_content'); ?>
							<?php if ($settings['ma_el_tooltip_enable_link'] === 'yes') : ?>
							</a>
						<?php endif; ?>
					<?php elseif ($settings['ma_el_tooltip_type'] === 'image') : ?>
						<?php if ($settings['ma_el_tooltip_enable_link'] === 'yes') : ?>
							<a href="<?php echo esc_url($settings['ma_el_tooltip_link']['url']); ?>">
							<?php endif; ?>
							<img src="<?php echo esc_url($settings['ma_el_tooltip_img_content']['url']); ?>">
							<?php if ($settings['ma_el_tooltip_enable_link'] === 'yes') : ?>
							</a>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<div class="ma-el-tooltip-text"><?php echo esc_html($settings['ma_el_tooltip_text']); ?></div>
			</div>
		</div>

<?php

	}
}

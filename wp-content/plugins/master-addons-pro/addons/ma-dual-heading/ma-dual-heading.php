<?php

namespace MasterAddons\Addons;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;
use \Elementor\Group_Control_Border;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Dual_Heading extends Widget_Base
{

	public function get_name()
	{
		return 'ma-dual-heading';
	}

	public function get_title()
	{
		return esc_html__('Dual Heading', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-heading';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_keywords()
	{
		return ['heading', 'headlines', 'dual headline', 'gradient', 'gradient heading', 'gradient headlines'];
	}

	public function get_style_depends()
	{
		return [
			'font-awesome-5-all',
			'font-awesome-4-shim'
		];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/dual-heading/';
	}


	protected function _register_controls()
	{

		/**
		 * Master Addons: Dual Heading Content Section
		 */
		$this->start_controls_section(
			'ma_el_dual_heading_content',
			[
				'label' => esc_html__('Content', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_dual_heading_styles_preset',
			[
				'label' => esc_html__('Style Preset', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'default' => '-style2',
				'options' => [
					'-style1' => esc_html__('Style 1', MELA_TD),
					'-style2' => esc_html__('Style 2', MELA_TD)
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_dual_heading_alignment',
			[
				'label' => esc_html__('Alignment', MELA_TD),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', MELA_TD),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', MELA_TD),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', MELA_TD),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper,
						{{WRAPPER}} .ma-el-sec-head-style' => 'text-align: {{VALUE}};',
				],
			]
		);



		$this->add_control(
			'ma_el_dual_first_heading',
			[
				'label' => esc_html__('First Heading', MELA_TD),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('First', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_dual_second_heading',
			[
				'label' 		=> esc_html__('Second Heading', MELA_TD),
				'type' 			=> Controls_Manager::TEXT,
				'label_block' 	=> true,
				'default' 		=> esc_html__('Second', MELA_TD),
			]
		);




		$this->add_control(
			'ma_el_dual_heading_title_link',
			[
				'label' 		=> esc_html__('Heading URL', MELA_TD),
				'type' 			=> Controls_Manager::URL,
				'placeholder' 	=> esc_html__('https://master-addons.com', MELA_TD),
				'label_block' 	=> true,
				'condition' 	=> [
					'ma_el_dual_heading_styles_preset' => '-style2',
				],
			]
		);

		$this->add_control(
			'ma_el_dual_heading_description',
			[
				'label'       => esc_html__('Sub Heading', MELA_TD),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'dynamic'     => ['active' => true],
				'default'     => __('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Architecto modi vel repudiandae reiciendis, cupiditate quod voluptatibus, placeat ad assumenda molestiae alias quisquam', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_dual_heading_icon_show',
			[
				'label' => esc_html__('Enable Icon', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'return_value' => 'yes',
				'condition' => [
					'ma_el_dual_heading_styles_preset' => '-style2',
				],
			]
		);

		$this->add_control(
			'ma_el_dual_heading_icon',
			[
				'label'         	=> esc_html__('Icon', MELA_TD),
				'description' 		=> esc_html__('Please choose an icon from the list.', MELA_TD),
				'type'          	=> Controls_Manager::ICONS,
				'fa4compatibility' 	=> 'icon',
				'default'       	=> [
					'value'     => 'fab fa-elementor',
					'library'   => 'brand',
				],
				'render_type'      => 'template',
				'condition' => [
					'ma_el_dual_heading_icon_show' => 'yes',
					'ma_el_dual_heading_styles_preset' => '-style2',
				]
			]
		);


		$this->add_control(
			'title_html_tag',
			[
				'label'   => __('Heading Tag', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'options' => Master_Addons_Helper::ma_el_title_tags(),
				'default' => 'h1',
			]
		);

		$this->end_controls_section();


		/*
			* Master Addons: Dual Heading Styling Section
			*/
		$this->start_controls_section(
			'ma_el_dual_heading_styles_general',
			[
				'label' => esc_html__('Icon Style', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ma_el_dual_heading_icon_show' => 'yes'
				],
			]
		);


		$this->add_control(
			'ma_el_dual_heading_icon_size',
			[
				'label'   => __('Size', MELA_TD),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => 40,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper .ma-el-dual-heading-icon' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'ma_el_dual_heading_icon_color',
			[
				'label'		=> esc_html__('Icon Color', MELA_TD),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#132C47',
				'selectors'	=> [
					'{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper .ma-el-dual-heading-icon' => 'color: {{VALUE}};',
				],
				'condition' => [
					'ma_el_dual_heading_icon_show' => 'yes'
				],
			]
		);

		$this->end_controls_section();



		/*
			* Master Addons: Dual Heading First Part Styling Section
			*/
		$this->start_controls_section(
			'ma_el_dual_first_heading_styles',
			[
				'label' => esc_html__('First Heading', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_dual_heading_first_text_color',
			[
				'label'		=> esc_html__('Text Color', MELA_TD),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#1fb5ac',
				'selectors'	=> [
					'{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper .ma-el-dual-heading-title .first-heading, {{WRAPPER}} .ma-el-section-title span'
					=> 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_dual_heading_first_bg_color',
			[
				'label' => __('Background', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#704aff',
				'selectors' => [
					'{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper .ma-el-dual-heading-title .first-heading, {{WRAPPER}} .ma-el-sec-head-container .ma-el-sec-head-style:after'
					=> 'background-color: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'ma_el_dual_first_heading_alignment',
			[
				'label' => esc_html__('Alignment', MELA_TD),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', MELA_TD),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', MELA_TD),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', MELA_TD),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ma-el-dual-heading-title' => 'text-align: {{VALUE}};',
				],
				'condition' 	=> [
					'ma_el_dual_heading_styles_preset' => '-style2',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_dual_first_heading_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper .ma-el-dual-heading-title  .first-heading,{{WRAPPER}} .ma-el-section-title span',
			]
		);

		$this->add_responsive_control(
			'ma_el_dual_first_heading_padding',
			[
				'label'         => __('Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper .ma-el-dual-heading-title  .first-heading,{{WRAPPER}} .ma-el-section-title span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_dual_first_heading_margin',
			[
				'label'         => __('Margin', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper .ma-el-dual-heading-title  .first-heading,{{WRAPPER}} .ma-el-section-title span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->end_controls_section();

		/*
			* Master Addons: Dual Heading Second Part Styling Section
			*/
		$this->start_controls_section(
			'ma_el_dual_second_heading_styles',
			[
				'label' => esc_html__('Second Heading', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'ma_el_dual_heading_second_text_color',
			[
				'label'		=> esc_html__('Text Color', MELA_TD),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#132C47',
				'selectors'	=> [
					'{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper .ma-el-dual-heading-title .second-heading,
						{{WRAPPER}} .ma-el-section-title' =>
					'color: {{VALUE}};',
				],

			]
		);

		$this->add_control(
			'ma_el_dual_heading_second_bg_color',
			[
				'label' => __('Background', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper .ma-el-dual-heading-title .second-heading' =>
					'background-color: {{VALUE}};',
				],

				'condition' => [
					'ma_el_dual_heading_styles_preset' => '-style2',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_dual_second_heading_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' =>
				'{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper .ma-el-dual-heading-title .second-heading,                          {{WRAPPER}} .ma-el-section-title',
			]
		);


		$this->add_responsive_control(
			'ma_el_dual_second_heading_padding',
			[
				'label'         => __('Padding', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper .ma-el-dual-heading-description,
						{{WRAPPER}} .ma-el-section-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'ma_el_dual_second_heading_margin',
			[
				'label'         => __('Margin', MELA_TD),
				'type'          => Controls_Manager::DIMENSIONS,
				'size_units'    => ['px', 'em', '%'],
				'selectors'     => [
					'{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper .ma-el-dual-heading-description,
						{{WRAPPER}} .ma-el-section-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);
		$this->end_controls_section();

		/*
				* Master Addons: Dual Heading description Styling Section
			*/
		$this->start_controls_section(
			'ma_el_dual_heading_description_styles',
			[
				'label' => esc_html__('Description', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'ma_el_dual_desc_heading_alignment',
			[
				'label' => esc_html__('Alignment', MELA_TD),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', MELA_TD),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', MELA_TD),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', MELA_TD),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ma-el-section-description' => 'text-align: {{VALUE}};',
				],
				'condition' 	=> [
					'ma_el_dual_heading_styles_preset' => '-style2',
				],
			]
		);
		$this->add_control(
			'ma_el_dual_heading_description_text_color',
			[
				'label'		=> esc_html__('Text Color', MELA_TD),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#989B9E',
				'selectors'	=> [
					'{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper .ma-el-dual-heading-description,
						{{WRAPPER}} .ma-el-section-description' =>
					'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_dual_heading_description_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .ma-el-dual-heading .ma-el-dual-heading-wrapper .ma-el-dual-heading-description,
					{{WRAPPER}} .ma-el-section-description',
			]
		);

		$this->end_controls_section();




		/*
			 *  Master Addons: Icon Styling
			 */
		$this->start_controls_section(
			'ma_el_dual_heading_icon_style',
			[
				'label' => esc_html__('Icon Style', MELA_TD),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->start_controls_tabs('ma_el_dual_heading_icon_style_tabs');

		$this->start_controls_tab('normal', ['label' => esc_html__('Normal', MELA_TD)]);

		$this->add_control(
			'ma_el_dual_heading_icon_style_color',
			[
				'label'     => esc_html__('Text Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8c8c8c',
				'selectors' => [
					'{{WRAPPER}} .ma-el-dual-heading-icon i'                                      => 'color: {{VALUE}};'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ma_el_dual_heading_icon_hover_color',
			[
				'label' => esc_html__('Hover', MELA_TD)
			]
		);

		$this->add_control(
			'ma_el_dual_heading_icon_style_hover_text_color',
			[
				'label'     => esc_html__('Text Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8c8c8c',
				'selectors' => [
					'{{WRAPPER}} .ma-el-dual-heading-icon i:hover'                               => 'color: {{VALUE}};'
				],
			]
		);
		$this->end_controls_tab();


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
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/dual-heading/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/dual-heading/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=kXyvNe6l0Sg" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();




		
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

?>


		<?php if ($settings['ma_el_dual_heading_styles_preset'] == '-style1') { ?>

			<div class="ma-el-sec-head-container">
				<div class="ma-el-sec-head-style">
					<<?php echo $settings['title_html_tag']; ?> class="ma-el-section-title">
						<span>
							<?php echo esc_html($settings['ma_el_dual_first_heading']); ?>
						</span><br>

						<?php echo esc_html($settings['ma_el_dual_second_heading']); ?>

					</<?php echo $settings['title_html_tag']; ?>><!-- /.section-title -->

					<div class="ma-el-section-description">
						<?php echo esc_html($settings['ma_el_dual_heading_description']); ?>
					</div><!-- /.section-description -->
				</div><!-- /.sec-head-style -->
			</div><!-- /.sec-head-container -->

		<?php } elseif ($settings['ma_el_dual_heading_styles_preset'] == '-style2') { ?>

			<div id="ma-el-heading-<?php echo esc_attr($this->get_id()); ?>" class="ma-el-dual-heading">
				<div class="ma-el-dual-heading-wrapper">
					<?php if ($settings['ma_el_dual_heading_icon_show'] == 'yes') : ?>
						<span class="ma-el-dual-heading-icon">
							<?php Master_Addons_Helper::jltma_fa_icon_picker('fab fa-elementor', 'icon', $settings['ma_el_dual_heading_icon'], 'ma_el_dual_heading_icon'); ?>
						</span>
					<?php endif; ?>
					<<?php echo $settings['title_html_tag']; ?> class="ma-el-dual-heading-title">

						<?php if (isset($settings['ma_el_dual_heading_title_link']['url']) && $settings['ma_el_dual_heading_title_link']['url'] != "") { ?>
							<a href="<?php echo esc_url($settings['ma_el_dual_heading_title_link']['url']); ?>">
							<?php } ?>

							<span class="first-heading">
								<?php echo esc_html($settings['ma_el_dual_first_heading']); ?>
							</span>

							<span class="second-heading">
								<?php echo esc_html($settings['ma_el_dual_second_heading']); ?>
							</span>

							<?php if (isset($settings['ma_el_dual_heading_title_link']['url']) && $settings['ma_el_dual_heading_title_link']['url'] != "") { ?>
							</a>
						<?php } ?>

					</<?php echo $settings['title_html_tag']; ?>>
					<?php if ($settings['ma_el_dual_heading_description'] != "") : ?>
						<p class="ma-el-dual-heading-description"><?php echo esc_html($settings['ma_el_dual_heading_description']); ?></p>
					<?php endif; ?>
				</div>
			</div>

		<?php } ?>


	<?php
	}

	protected function _content_template()
	{ ?>

		<# if ( '-style1'==settings.ma_el_dual_heading_styles_preset ) { #>

			<div class="ma-el-sec-head-container">
				<div class="ma-el-sec-head-style">
					<h2 class="ma-el-section-title">
						<span>{{{ settings.ma_el_dual_first_heading }}}</span> {{{ settings.ma_el_dual_second_heading }}}
					</h2><!-- /.section-title -->

					<div class="ma-el-section-description">
						{{{ settings.ma_el_dual_heading_description }}}
					</div><!-- /.section-description -->
				</div><!-- /.sec-head-style -->
			</div><!-- /.sec-head-container -->

			<# } else{ #>

				<div id="ma-el-heading" class="ma-el-dual-heading">
					<div class="ma-el-dual-heading-wrapper">
						<# if ( settings.ma_el_dual_heading_icon_show=='yes' ) { #>
							<span class="ma-el-dual-heading-icon"><i class="{{ settings.ma_el_dual_heading_icon.value }}"></i></span>
							<# } #>
								<h1 class="ma-el-dual-heading-title">
									<a href="{{{ settings.ma_el_dual_heading_title_link }}}">
										<span class="first-heading">{{{ settings.ma_el_dual_first_heading }}}</span><span class="second-heading">{{{ settings.ma_el_dual_second_heading }}}</span>
									</a>
								</h1>
								<# if ( settings.ma_el_dual_heading_description !="" ) { #>
									<p class="ma-el-dual-heading-description">{{{ settings.ma_el_dual_heading_description }}}</p>
									<# } #>
					</div>
				</div>
				<# } #>

			<?php
		}
	}

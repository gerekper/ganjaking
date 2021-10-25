<?php

namespace MasterAddons\Addons;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager as Controls_Manager;
use \Elementor\Group_Control_Border as Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow as Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography as Group_Control_Typography;
use \Elementor\Scheme_Typography as Scheme_Typography;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 6/27/19
 */


if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Creative_Links extends Widget_Base
{

	public function get_name()
	{
		return 'ma-creative-links';
	}

	public function get_title()
	{
		return esc_html__('Creative Links', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-editor-external-link';
	}

	public function get_categories()
	{
		return ['master-addons'];
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
		return 'https://master-addons.com/demos/creative-link/';
	}

	protected function _register_controls()
	{

		// Style Presets
		$this->start_controls_section(
			'ma_el_creative_link_content_style_presets',
			[
				'label' => esc_html__('Style Presets', MELA_TD)
			]
		);


		// Premium Version Codes
		

			$this->add_control(
				'creative_link_effect',
				[
					'label'       => esc_html__('Effects', MELA_TD),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'cl-effect-1',
					'options'     => [
						'cl-effect-1' 	=> esc_html__('Brackets', 	                                MELA_TD),
						'cl-effect-2' 	=> esc_html__('3D Effect', 	                            MELA_TD),
						'cl-effect-3' 	=> esc_html__('Bottom Line Slide', 	                    MELA_TD),
						'cl-effect-4' 	=> esc_html__('Bottom Border Enlarge', 	                MELA_TD),
						'cl-effect-5' 	=> esc_html__('Slide In', 	                                MELA_TD),
						'cl-effect-6' 	=> esc_html__('Border Slide Down', 	                    MELA_TD),
						'cl-effect-7' 	=> esc_html__('2nd Border Slide Up', 	                    MELA_TD),
						'cl-effect-8' 	=> esc_html__('Border Translate', 	                        MELA_TD),
						'cl-effect-9' 	=> esc_html__('2nd Text and Border', 	                    MELA_TD),
						'cl-effect-10' 	=> esc_html__('Reveal Push Out', 	                        MELA_TD),
						'cl-effect-11' 	=> esc_html__('Text Fill', 	                            MELA_TD), //problem
						'cl-effect-12' 	=> esc_html__('Circle ', 	                                MELA_TD),
						'cl-effect-13' 	=> esc_html__('Three Dots', 	                            MELA_TD),
						'cl-effect-14' 	=> esc_html__('Border Switch', 	                        MELA_TD),
						'cl-effect-15' 	=> esc_html__('Scale Down', 	                            MELA_TD),
						'cl-effect-16' 	=> esc_html__('Fall Down', 	                            MELA_TD),
						'cl-effect-17' 	=> esc_html__('Move Up', 	                                MELA_TD),
						'cl-effect-18' 	=> esc_html__('Cross', 	                                MELA_TD),
						'cl-effect-19' 	=> esc_html__('3D Slide', 	                                MELA_TD),
						'cl-effect-20' 	=> esc_html__('3D Slide Down', 	                        MELA_TD),
						'cl-effect-21' 	=> esc_html__('Effect 21', 	                            MELA_TD),
						// 'cl-effect-22' 	=> esc_html__( 'Effect 22', 	                            MELA_TD ),
					],

				]
			);


			//Free Version Codes
		




		$this->add_responsive_control(
			'ma_el_creative_link_alignment',
			[
				'label'       => esc_html__('Link Alignment', MELA_TD),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left' => [
						'title' => esc_html__('Left', MELA_TD),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', MELA_TD),
						'icon'  => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', MELA_TD),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .ma-el-creative-links.cl-effect-1 .ma-el-creative-link .ma-el-creative-link-item,
						{{WRAPPER}} .ma-el-creative-links.cl-effect-2 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-2 .ma-el-creative-link .ma-el-creative-link-item span,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-3 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-4 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-5 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-6 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-7 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-8 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-9 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-10 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-11 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-12 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-13 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-14 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-17 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-18 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-19 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-20 .ma-el-creative-link .ma-el-creative-link-item,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-20 .ma-el-creative-link span,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-21 .ma-el-creative-link .ma-el-creative-link-item' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();



		/*
			 * Master Addons: Creative Link Contents
			 */
		$this->start_controls_section(
			'ma_el_creative_link_content',
			[
				'label' => esc_html__('Contents', MELA_TD)
			]
		);


		$this->add_control(
			'creative_link_text',
			[
				'label'       => esc_html__('Link Text', MELA_TD),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'Click Me!',
				'placeholder' => esc_html__('Enter Link text', MELA_TD),
				'title'       => esc_html__('Enter Link text here', MELA_TD),
			]
		);

		$this->add_control(
			'creative_alternative_link_text',
			[
				'label'       => esc_html__('Alternative Link Text', MELA_TD),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'Go!',
				'placeholder' => esc_html__('Enter Alternative Link text', MELA_TD),
				'title'       => esc_html__('Enter Alternative Link text here', MELA_TD),
			]
		);


		$this->add_control(
			'creative_link_url',
			[
				'label'       => esc_html__('Link URL', MELA_TD),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'default'     => [
					'url'         => '#',
					'is_external' => '',
				],
				'show_external' => true,
			]
		);

		$this->add_control(
			'ma_el_creative_link_icon',
			[
				'label'         	=> esc_html__('Icon', MELA_TD),
				'description' 		=> esc_html__('Please choose an icon from the list.', MELA_TD),
				'type'          	=> Controls_Manager::ICONS,
				'fa4compatibility' 	=> 'icon',
				'default'       	=> [
					'value'     => 'fas fa-external-link-alt',
					'library'   => 'solid',
				],
				'render_type'      => 'template'
			]
		);


		$this->add_control(
			'ma_el_creative_link_icon_alignment',
			[
				'label'   => esc_html__('Icon Position', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => esc_html__('Before', MELA_TD),
					'right' => esc_html__('After', MELA_TD),
				],
				'condition' => [
					'ma_el_creative_link_icon!' => '',
				],
			]
		);


		$this->add_control(
			'ma_el_creative_link_icon_indent',
			[
				'label' => esc_html__('Icon Spacing', MELA_TD),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 60,
					],
				],
				'condition' => [
					'ma_el_creative_link_icon!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-creative-link-icon-right' => 'margin-left: {{SIZE}}px;',
					'{{WRAPPER}} .ma-el-creative-link-icon-left'  => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .ma-el-creative-link i' => 'left: -{{SIZE}}px;',
				],
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
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/creative-link/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/how-to-add-creative-links/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=o6SmdwMJPyA" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();



		



		/*
			 *  Master Addons: Style Controls
			 */
		$this->start_controls_section(
			'ma_el_creative_link_settings',
			[
				'label' => esc_html__('Link Styles', MELA_TD),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'ma_el_creative_link_width',
			[
				'label'      => esc_html__('Width', MELA_TD),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-creative-link' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ma_el_creative_link_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .ma-el-creative-links .ma-el-creative-link .ma-el-creative-link-item',
			]
		);

		$this->add_responsive_control(
			'ma_el_creative_link_padding',
			[
				'label'      => esc_html__('Link Padding', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ma-el-creative-link'                                      => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ma-el-creative-link.ma-el-creative-link--winona::after'  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ma-el-creative-link.ma-el-creative-link--winona > span'  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ma-el-creative-link.ma-el-creative-link--tamaya::before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ma-el-creative-link.ma-el-creative-link--rayen::before'  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ma-el-creative-link.ma-el-creative-link--rayen > span'   => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->start_controls_tabs('ma_el_creative_link_tabs');

		$this->start_controls_tab('normal', ['label' => esc_html__('Normal', MELA_TD)]);

		$this->add_control(
			'ma_el_creative_link_text_color',
			[
				'label'     => esc_html__('Text Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .ma-el-creative-links .ma-el-creative-link a'
					=> 'color: {{VALUE}};',

					// Bar Colors
					'{{WRAPPER}} .ma-el-creative-links.cl-effect-4 a::after,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-3 a::after,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-6 a::before,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-6 a::after,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-7 a::before,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-7 a::after
                        '
					=>  'background: {{VALUE}};',


					'{{WRAPPER}} .ma-el-creative-links.cl-effect-20 a span' => 'box-shadow: inset 0 3px {{VALUE}};',


					'{{WRAPPER}} .ma-el-creative-links.cl-effect-8 a::before,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-8 a::after' => 'border: 3px solid {{VALUE}};',




					//						'{{WRAPPER}} .ma-el-creative-link.ma-el-creative-link--tamaya::before' => 'color: {{VALUE}};',
					//						'{{WRAPPER}} .ma-el-creative-link.ma-el-creative-link--tamaya::after'  => 'color: {{VALUE}};',
				],
			]
		);



		$this->add_control(
			'ma_el_creative_link_background_color',
			[
				'label'     => esc_html__('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [

					'{{WRAPPER}} .ma-el-creative-links .ma-el-creative-link' => 'background-color: {{VALUE}};',

					'{{WRAPPER}} .ma-el-creative-links.cl-effect-2 a span,
						{{WRAPPER}} .ma-el-creative-links.cl-effect-20 a span' => 'background: {{VALUE}};',

					'{{WRAPPER}} .ma-el-creative-links.cl-effect-19 .ma-el-creative-link a span,
						{{WRAPPER}} .csstransforms3d .ma-el-creative-links.cl-effect-19 a span::before' => 'background: {{VALUE}};',

					//						'{{WRAPPER}} .ma-el-creative-link'                                      => 'background-color: {{VALUE}};',
					//						'{{WRAPPER}} .ma-el-creative-link:hover'   => 'background-color: {{VALUE}};',

					//						'{{WRAPPER}} .ma-el-creative-link a:hover'    => 'background-color: {{VALUE}};',
					//						'{{WRAPPER}} .ma-el-creative-link a::before' => 'background-color: {{VALUE}};',
					//						'{{WRAPPER}} .ma-el-creative-link a::after'  => 'background-color: {{VALUE}};',
					//						'{{WRAPPER}} .ma-el-creative-link a:hover'    => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'ma_el_creative_link_border',
				'selector' => '{{WRAPPER}} .ma-el-creative-link',
			]
		);

		$this->add_control(
			'ma_el_creative_link_border_radius',
			[
				'label' => esc_html__('Border Radius', MELA_TD),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ma-el-creative-link'         => 'border-radius: {{SIZE}}px;',
					'{{WRAPPER}} .ma-el-creative-link::before' => 'border-radius: {{SIZE}}px;',
					'{{WRAPPER}} .ma-el-creative-link::after'  => 'border-radius: {{SIZE}}px;',
				],
			]
		);



		$this->end_controls_tab();

		$this->start_controls_tab('ma_el_creative_link_hover', [
			'label' => esc_html__('Hover', MELA_TD)
		]);

		$this->add_control(
			'ma_el_creative_link_hover_text_color',
			[
				'label'     => esc_html__('Text Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .ma-el-creative-links .ma-el-creative-link a:hover,
						{{WRAPPER}} .ma-el-creative-links.cl-effect-9 a span:last-child,
						{{WRAPPER}} .ma-el-creative-links.cl-effect-20 a span::before' => 'color: {{VALUE}};',

					'{{WRAPPER}} .ma-el-creative-links.cl-effect-8 .cl-effect-8 a::after' => 'border-color: {{VALUE}};',


					'{{WRAPPER}} .ma-el-creative-links.cl-effect-13 a:hover::before,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-13 a:focus::before' => 'color: {{VALUE}}; text-shadow: 10px 0 {{VALUE}}, -10px 0 {{VALUE}};',


					'{{WRAPPER}} .ma-el-creative-links.cl-effect-14 a::before,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-14 a::after,
						{{WRAPPER}} .ma-el-creative-links.cl-effect-17 a::after,
						{{WRAPPER}} .ma-el-creative-links.cl-effect-18 a::before,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-18 a::after' => 'background: {{VALUE}};',


				],
			]
		);

		$this->add_control(
			'ma_el_creative_link_hover_background_color',
			[
				'label'     => esc_html__('Background Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					//						'{{WRAPPER}} .ma-el-creative-link:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .csstransforms3d .cl-effect-2 a span::before,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-20 a span::before' => 'background: {{VALUE}};',

				]
			]
		);

		$this->add_control(
			'ma_el_creative_link_hover_border_color',
			[
				'label'     => esc_html__('Border Color', MELA_TD),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [

					'{{WRAPPER}} .ma-el-creative-links.cl-effect-12 a::before,
                        {{WRAPPER}} .ma-el-creative-links.cl-effect-12 a::after'
					=> 'border: 2px solid {{VALUE}};',

					//						'{{WRAPPER}} .ma-el-creative-link:hover'                                 => 'border-color: {{VALUE}};',
					//						'{{WRAPPER}} .ma-el-creative-link.ma-el-creative-link--wapasha::before' => 'border-color: {{VALUE}};',
					//						'{{WRAPPER}} .ma-el-creative-link.ma-el-creative-link--antiman::before' => 'border-color: {{VALUE}};',
					//						'{{WRAPPER}} .ma-el-creative-link.ma-el-creative-link--pipaluk::before' => 'border-color: {{VALUE}};',
					//						'{{WRAPPER}} .ma-el-creative-link.ma-el-creative-link--quidel::before'  => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'link_box_shadow',
				'selector' => '{{WRAPPER}} .ma-el-creative-link',
			]
		);


		$this->end_controls_section();



		
	}

	protected function render()
	{
		$settings = $this->get_settings();
		$link_id		= $this->get_id();


		$this->add_render_attribute('ma_el_creative_links_wrapper', [
			'class'	=> [
				'ma-el-creative-links',
				esc_attr($settings['creative_link_effect'])
			],
			'id' => 'ma-creative-link-' . esc_attr($link_id)
		]);

		$this->add_render_attribute('ma_el_creative_link', [
			'class'	=>  'ma-el-creative-link-item',
			'href'	=> esc_url_raw($settings['creative_link_url']['url']),

		]);


		if ($settings['creative_link_url']['is_external']) {
			$this->add_render_attribute('ma_el_creative_link', 'target', '_blank');
		}

		if ($settings['creative_link_url']['nofollow']) {
			$this->add_render_attribute('ma_el_creative_link', 'rel', 'nofollow');
		}
?>
		<?php if (($settings['creative_link_effect'] == "cl-effect-2") ||
			($settings['creative_link_effect'] == "cl-effect-19") ||
			($settings['creative_link_effect'] == "cl-effect-20")
		) { ?>
			<div class="csstransforms3d">
			<?php } ?>

			<div <?php echo $this->get_render_attribute_string('ma_el_creative_links_wrapper'); ?>>
				<div class="ma-el-creative-link">
					<a <?php echo $this->get_render_attribute_string('ma_el_creative_link'); ?> <?php if (($settings['creative_link_effect'] == "cl-effect-10") ||
																									($settings['creative_link_effect'] == "cl-effect-11") ||
																									($settings['creative_link_effect'] == "cl-effect-15") ||
																									($settings['creative_link_effect'] == "cl-effect-16") ||
																									($settings['creative_link_effect'] == "cl-effect-17") ||
																									($settings['creative_link_effect'] == "cl-effect-18") ||
																									($settings['creative_link_effect'] == "cl-effect-19") ||
																									($settings['creative_link_effect'] == "cl-effect-20")
																								) { ?> data-hover="<?php echo ($settings['creative_alternative_link_text']) ? $settings['creative_alternative_link_text'] : $settings['creative_link_text']; ?>" <?php } ?>>



						<?php if (($settings['creative_link_effect'] == "cl-effect-2") ||
							($settings['creative_link_effect'] == "cl-effect-5") ||
							($settings['creative_link_effect'] == "cl-effect-19") ||
							($settings['creative_link_effect'] == "cl-effect-20")
						) { ?>
							<span data-hover="<?php echo ($settings['creative_alternative_link_text']) ? $settings['creative_alternative_link_text'] : $settings['creative_link_text']; ?>">
							<?php } ?>



							<?php if (!empty($settings['ma_el_creative_link_icon']) && $settings['ma_el_creative_link_icon_alignment'] == 'left') : ?>
								<?php Master_Addons_Helper::jltma_fa_icon_picker('fas fa-external-link-alt', 'icon', $settings['ma_el_creative_link_icon'], 'ma_el_creative_link_icon', 'ma-el-creative-link-icon-left'); ?>
							<?php endif; ?>


							<?php if ($settings['creative_link_effect'] == "cl-effect-10") { ?>
								<span><?php echo  $settings['creative_link_text']; ?></span>
							<?php } else {
								echo $settings['creative_link_text'];
							} ?>


							<?php if ($settings['creative_link_effect'] == "cl-effect-9") { ?>
								<span><?php echo ($settings['creative_alternative_link_text']) ? $settings['creative_alternative_link_text'] : $settings['creative_link_text']; ?></span>
							<?php } ?>



							<?php if (!empty($settings['ma_el_creative_link_icon']) && $settings['ma_el_creative_link_icon_alignment'] == 'right') : ?>
								<?php Master_Addons_Helper::jltma_fa_icon_picker('fas fa-external-link-alt', 'icon', $settings['ma_el_creative_link_icon'], 'ma_el_creative_link_icon', 'ma-el-creative-link-icon-right'); ?>
							<?php endif; ?>

							<?php if (($settings['creative_link_effect'] == "cl-effect-2")  ||
								($settings['creative_link_effect'] == "cl-effect-5") ||
								($settings['creative_link_effect'] == "cl-effect-19") ||
								($settings['creative_link_effect'] == "cl-effect-20")
							) { ?>
							</span>
						<?php } ?>

					</a>
				</div>
			</div>

			<?php if (($settings['creative_link_effect'] == "cl-effect-2") ||
				($settings['creative_link_effect'] == "cl-effect-19") ||
				($settings['creative_link_effect'] == "cl-effect-20")
			) { ?>
			</div>
		<?php } ?>

<?php
	}
}

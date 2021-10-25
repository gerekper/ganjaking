<?php

namespace MasterAddons\Addons;

use \Elementor\Widget_Base;
use \Elementor\Icons_Manager;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Background;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 6/25/19
 */

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Call_to_Action extends Widget_Base
{

	public function get_name()
	{
		return 'ma-call-to-action';
	}

	public function get_title()
	{
		return esc_html__('Call to Action', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-call-to-action';
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
		return 'https://master-addons.com/demos/call-to-action/';
	}


	protected function _register_controls()
	{

		/**
		 * Master Call to Action: Content
		 */
		$this->start_controls_section(
			'ma_el_call_to_action_content_section',
			[
				'label' => esc_html__('Content', MELA_TD),
			]
		);


		$this->add_control(
			'ma_el_call_to_action_style_preset',
			[
				'label' 		=> esc_html__('Style Preset', MELA_TD),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'style-01',
				'separator' 	=> 'after',
				'options' 		=> [
					'style-01' => esc_html__('Default Style', MELA_TD),
					'style-02' => esc_html__('Center Style', MELA_TD),
					'style-03' => esc_html__('Quote Style', MELA_TD),
					'style-04' => esc_html__('Quote Style 2', MELA_TD),
					'style-07' => esc_html__('Left Icon', MELA_TD)
				],
			]
		);



		$this->add_control(
			'ma_el_call_to_action_title',
			[
				'label' => esc_html__('CTA Content', MELA_TD),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'default' => esc_html__('Purchase Master Addons now and unlimited Options', MELA_TD),
			]
		);


		$this->add_control(
			'ma_el_call_to_action_content_desc',
			[
				'label' => esc_html__('Description', MELA_TD),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', MELA_TD),

				// 'condition' => [
				// 	'ma_el_call_to_action_style_preset' => 'style2',
				// ],
			]
		);

		$this->add_control(
			'ma_el_call_to_action_button_text',
			[
				'label' => esc_html__('Button Text', MELA_TD),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('Purchase Now', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_call_to_action_button_link',
			[
				'label' => __('Call To Action URL', MELA_TD),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://jeweltheme.com/shop/master-addons-elementor', MELA_TD),
				'label_block' => true,
				'default' => [
					'url' => '#',
					'is_external' => true,
				],
			]
		);

		$this->add_control(
			'ma_el_call_to_action_icon',
			[
				'label'         	=> esc_html__('Icon', MELA_TD),
				'description' 		=> esc_html__('Please choose an icon from the list.', MELA_TD),
				'type'          	=> Controls_Manager::ICONS,
				'fa4compatibility' 	=> 'icon',
				'default'       	=> [
					'value'     => 'fas fa-bell',
					'library'   => 'solid',
				],
				'render_type'      => 'template',
				'condition' => [
					'ma_el_call_to_action_style_preset' => ['style-07'],
				],
			]
		);


		$this->end_controls_section();


		/**
		 * Master Addons: Call to Action Content Section
		 */
		$this->start_controls_section(
			'ma_el_call_to_action_style',
			[
				'label' => esc_html__('Presets Style ', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ma_el_call_to_action_desc_bg',
				'label' => __('CTA Background', MELA_TD),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ma-el-call-to-action'
			]
		);



		$this->add_control(
			'ma_el_call_to_action_border_color',
			[
				'label'		=> esc_html__('Border Color', MELA_TD),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#4b00e7',
				'selectors'	=> [
					'{{WRAPPER}} .style-03 .ma-el-action-content .jltma-row' => 'border-left: 10px solid {{VALUE}};',

					'{{WRAPPER}} .style-04 .ma-el-action-content .jltma-row' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .ma-el-action-content .ma-el-action-btn:hover' => 'color: {{VALUE}};',
				],

				'condition' => [
					'ma_el_call_to_action_style_preset' => ['style-03', 'style-04'],
				],
			]
		);


		$this->add_control(
			'ma_el_call_to_action_icon_color',
			[
				'label'		=> esc_html__('Icon Color', MELA_TD),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#4b00e7',
				'selectors'	=> [
					'{{WRAPPER}} .ma-el-action-content .ma-el-action-btn:hover,
						{{WRAPPER}} .style-07 .media-left i' => 'color: {{VALUE}};',
				],

				'condition' => [
					'ma_el_call_to_action_style_preset' => ['style-07'],
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'ma_el_call_to_action_title_style_section',
			[
				'label' => __('Title Style', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'ma_el_call_to_action_title_color',
			[
				'label'		=> esc_html__('Title Color', MELA_TD),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#393c3f',
				'selectors'	=> [

					'{{WRAPPER}} .style-02 .ma-el-action-title' => 'color: #fff;',
					'{{WRAPPER}} .ma-el-action-title' => 'color: {{VALUE}} !important;',
				],

			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_cta_title_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .ma-el-action-title',
			]
		);


		$this->end_controls_section();




		$this->start_controls_section(
			'ma_el_call_to_action_desc_style_section',
			[
				'label' => __('Description Style', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_el_call_to_action_description_color',
			[
				'label'		=> esc_html__('Text Color', MELA_TD),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#78909c',
				'selectors'	=> [
					'{{WRAPPER}} .ma-el-action-description' => 'color: {{VALUE}};'
				]
			]
		);




		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_call_to_action_text_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .ma-el-action-description',
			]
		);

		$this->end_controls_section();



		$this->start_controls_section(
			'ma_el_call_to_action_button_section',
			[
				'label' => __('Button Style', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->start_controls_tabs('ma_el_call_to_action_button_style_tabs');

		$this->start_controls_tab(
			'ma_el_call_to_action_button_style_tab',
			[
				'label' => esc_html__('Normal', MELA_TD)
			]
		);


		$this->add_control(
			'ma_el_call_to_action_button_bg_color',
			[
				'label'		=> esc_html__('Background Color', MELA_TD),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#4b00e7',
				'selectors'	=> [
					'{{WRAPPER}} .ma-el-action-content .ma-el-action-btn'
					=> 'background-color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'ma_el_call_to_action_button_color',
			[
				'label'		=> esc_html__('Text Color', MELA_TD),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors'	=> [
					'{{WRAPPER}} .ma-el-action-content .ma-el-action-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'          => 'ma_el_call_to_action_button_border',
				'selector'      => '{{WRAPPER}} .ma-el-action-content .ma-el-action-btn'
			]
		);

		$this->add_responsive_control(
			'ma_el_call_to_action_button_border_radius',
			array(
				'label'      => esc_html__('Border Radius', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .ma-el-action-content .ma-el-action-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab('ma_el_call_to_action_button_hover', [
			'label' => esc_html__(
				'Hover',
				MELA_TD
			)
		]);

		$this->add_control(
			'ma_el_call_to_action_button_bg_hover_color',
			[
				'label'		=> esc_html__('Background Color', MELA_TD),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#4b00e7',
				'selectors'	=> [
					'{{WRAPPER}} .ma-el-action-content .ma-el-action-btn:hover'
					=> 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ma_el_call_to_action_button_hover_color',
			[
				'label'		=> esc_html__('Text Color', MELA_TD),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors'	=> [
					'{{WRAPPER}} .ma-el-action-content .ma-el-action-btn:hover'
					=> 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'          => 'ma_el_call_to_action_border_hover',
				'selector'      => '{{WRAPPER}} .ma-el-action-content .ma-el-action-btn:hover'
			]
		);

		$this->add_responsive_control(
			'ma_el_call_to_action_button_border_hover_radius',
			array(
				'label'      => esc_html__('Border Radius', MELA_TD),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array('px', '%'),
				'selectors'  => array(
					'{{WRAPPER}} .ma-el-action-content .ma-el-action-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ma_el_call_to_action_button_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .ma-el-action-btn',
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
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/call-to-action/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/call-to-action/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=iY2q1jtSV5o" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();



		
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('ma_el_call_to_action_wrapper', [
			'class'	=> [
				'ma-el-call-to-action'
			],
			'id' => 'ma-el-action-content-' . $this->get_id()
		]);



		if (!isset($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
			$settings['icon'] = 'fas fa-bell';
		}

		$has_icon  = !empty($settings['icon']);
		if ($has_icon and 'icon' == $settings['ma_el_call_to_action_icon']) {
			$this->add_render_attribute('jltma-icon', 'class', $settings['ma_el_call_to_action_icon']);
			$this->add_render_attribute('jltma-icon', 'aria-hidden', 'true');
		}

		if (!$has_icon && !empty($settings['ma_el_call_to_action_icon']['value'])) {
			$has_icon = true;
		}

		$migrated  = isset($settings['__fa4_migrated']['ma_el_call_to_action_icon']);
		$is_new    = empty($settings['icon']) && Icons_Manager::is_migration_allowed();
?>

		<section <?php echo $this->get_render_attribute_string('ma_el_call_to_action_wrapper'); ?>>
			<div class="<?php echo esc_attr($settings['ma_el_call_to_action_style_preset']); ?>">
				<div class="ma-el-action-content">
					<div class="jltma-row">
						<div class="jltma-col-lg-9">

							<?php if ($settings['ma_el_call_to_action_style_preset'] == "style-07") { ?>
								<div class="ma-cta-icon-section media">

									<div class="ma-cta-icon media-left">
										<?php
										if ($is_new || $migrated) {
											Icons_Manager::render_icon($settings['ma_el_call_to_action_icon'], ['aria-hidden' => 'true']);
										} else {
											echo '<i ' . $this->get_render_attribute_string('jltma-icon') . '></i>';
										}
										?>
									</div>

									<div class="media-body">
										<h3 class="ma-el-action-title">
											<?php echo esc_html($settings['ma_el_call_to_action_title']); ?>
										</h3>
										<p class="ma-el-action-description">
											<?php echo esc_html($settings['ma_el_call_to_action_content_desc']); ?>
										</p>
									</div>

								</div>
							<?php } else { ?>
								<h3 class="ma-el-action-title">
									<?php echo esc_html($settings['ma_el_call_to_action_title']); ?>
								</h3>

								<p class="ma-el-action-description">
									<?php echo esc_html($settings['ma_el_call_to_action_content_desc']); ?>
								</p>
							<?php } ?>
						</div>
						<div class="jltma-col-lg-3 text-right">
							<a href="<?php echo esc_url($settings['ma_el_call_to_action_button_link']['url']); ?>" class="ma-el-action-btn">
								<?php echo esc_html($settings['ma_el_call_to_action_button_text']); ?>
							</a>
						</div>
					</div>
				</div>
			</div>

		</section>
<?php
	}
}

<?php

namespace ElementPack\Modules\CharitableCampaigns\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Charitable_Campaigns extends Module_Base {

	public function get_name() {
		return 'bdt-charitable-campaigns';
	}

	public function get_title() {
		return BDTEP . __('Charitable Campaigns', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-charitable-campaigns';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['charitable', 'charity', 'donation', 'donor', 'history', 'charitable', 'wall', 'campaigns'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-charitable-campaigns'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/ugKfZyvSbGA';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_charitable_campaigns',
			[
				'label' => __('Charitable Campaigns', 'bethemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'campaigns',
			[
				'label'       => __('Campaigns', 'bethemes-element-pack'),
				'type'        => Controls_Manager::SELECT2,
				'options'     => element_pack_charitable_forms_options(),
				'multiple'    => true,
				'label_block' => true,
			]
		);

		$this->add_control(
			'number',
			[
				'label' => esc_html__('Limit', 'bdthemes-element-pack'),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => __('Columns', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop' => 'display: grid;
					grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'items_gap',
			[
				'label' => esc_html__('Items Gap', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop' => 'grid-gap: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'button',
			[
				'label'   => __('Button Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					0         => esc_html__('None', 'bdthemes-element-pack'),
					'donate'  => esc_html__('Donate', 'bdthemes-element-pack'),
					'details' => esc_html__('Details', 'bdthemes-element-pack'),
				],
				'default' => 'donate',
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => __('Order', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'ASC'  => esc_html__('ASC', 'bdthemes-element-pack'),
					'DESC' => esc_html__('DESC', 'bdthemes-element-pack'),
				],
				'default' => 'DESC',
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __('Order By', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'post_date'  => esc_html__('Post Date', 'bdthemes-element-apck'),
					'popular'    => esc_html__('Popular', 'bdthemes-element-apck'),
					'ending' 	 => esc_html__('Ending', 'bdthemes-element-apck'),
				],
				'default' => 'post_date',
			]
		);

		$this->add_control(
			'match_height',
			[
				'label' => __('Item Match Height', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-campaigns-items-height-',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_items_style',
			[
				'label' => esc_html__('Items', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'item_background_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'item_border',
				'selector' => '{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign',
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'item_hover_background_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry:hover, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry:hover, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry:hover, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			[
				'label' => esc_html__('Image', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'selector' => '{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .wp-post-image, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .wp-post-image'
			]
		);

		$this->add_responsive_control(
			'image_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .wp-post-image, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .wp-post-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'iamge_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .wp-post-image, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .wp-post-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .wp-post-image, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .wp-post-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .wp-post-image, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .wp-post-image'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry h3, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign h3' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label' => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry h3:hover, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign h3:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry h3, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign h3' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry h3, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign h3',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_style',
			[
				'label' => esc_html__('Text', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .campaign-description, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .campaign-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .campaign-description, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .campaign-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .campaign-description, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .campaign-description',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_amount_style',
			[
				'label' => esc_html__('Amount', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'amount_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .campaign-donation-stats, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .campaign-donation-stats' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'amount_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .campaign-donation-stats, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .campaign-donation-stats' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'amount_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .campaign-donation-stats, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .campaign-donation-stats',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'progress_bar_style',
			[
				'label' => esc_html__('Progress Bar', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'progress_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .campaign-progress-bar .bar, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .campaign-progress-bar .bar' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'progress_bg_color',
			[
				'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .campaign-progress-bar, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .campaign-progress-bar' => 'background-color: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'progress_border_radius',
			[
				'label' => __('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .campaign-progress-bar, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .campaign-progress-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'progress_height',
			[
				'label' => __('Height', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .campaign-progress-bar, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .campaign-progress-bar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'progress_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry .campaign-progress-bar, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign .campaign-progress-bar' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__('Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'button' => ['donate', 'details']
				]
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry a.button, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign a.button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'selector'  => '{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry a.button, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign a.button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'selector'    => '{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry a.button, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign a.button',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry a.button, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign a.button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry a.button, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign a.button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry a.button, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign a.button',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry a.button, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign a.button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry a.button:hover, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign a.button:hover'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry a.button:hover, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign a.button:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-campaigns .campaign-loop .hentry a.button:hover, {{WRAPPER}} .bdt-charitable-campaigns .campaign-loop li.campaign a.button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		if (!$settings['campaigns']) {
			return '<div class="bdt-alert bdt-alert-warning">' . __('Please select Charitable Campaigns From Setting!', 'bdthemes-element-pack') . '</div>';
		}

		$attributes = [
			// 'id'               => implode(',', $settings['campaigns']),
			'orderby'          => $settings['orderby'],
			'order'            => $settings['order'],
			'number'           => $settings['number'],
			'button'           => $settings['button'],
		];


		if (!in_array('all', $settings['campaigns'])) {
			$attributes['id'] = implode(',', $settings['campaigns']);
		}


		$this->add_render_attribute('shortcode', $attributes);

		$shortcode   = [];
		$shortcode[] = sprintf('[campaigns %s]', $this->get_render_attribute_string('shortcode'));

		return implode("", $shortcode);
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('charitable_wrapper', 'class', 'bdt-charitable-campaigns');

		if ('yes' == $settings['match_height']) {
			$this->add_render_attribute('charitable_wrapper', 'bdt-height-match', 'target: > ol > li');
		}

?>

		<div <?php echo $this->get_render_attribute_string('charitable_wrapper'); ?>>

			<?php echo do_shortcode($this->get_shortcode()); ?>

		</div>

<?php
	}

	public function render_plain_content() {
		echo $this->get_shortcode();
	}
}

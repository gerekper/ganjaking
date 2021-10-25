<?php

namespace Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 6/27/19
 */

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.


class Master_Addons_Cards extends Widget_Base
{

	//use ElementsCommonFunctions;
	public function get_name()
	{
		return 'ma-el-card';
	}
	public function get_title()
	{
		return esc_html__('Cards', MELA_TD);
	}
	public function get_icon()
	{
		return 'ma-el-icon eicon-image-box';
	}
	public function get_categories()
	{
		return ['master-addons'];
	}
	protected function _register_controls()
	{

		/**
		 * Card Content Section
		 */
		$this->start_controls_section(
			'ma_el_card_content',
			[
				'label' => esc_html__('Content', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_card_image',
			[
				'label' => __('Image', MELA_TD),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'condition' => [
					'ma_el_card_image[url]!' => '',
				],
			]
		);

		$this->add_control(
			'ma_el_card_title',
			[
				'label' => esc_html__('Title', MELA_TD),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'separator' => 'before',
				'default' => esc_html__('Card Title', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_card_title_link',
			[
				'label' => __('Title URL', MELA_TD),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', MELA_TD),
				'label_block' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
				],
			]
		);

		$this->add_control(
			'ma_el_card_tag',
			[
				'label' => esc_html__('Tag', MELA_TD),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('Card Tag', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_card_description',
			[
				'label' => esc_html__('Description', MELA_TD),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__('Basic description about the Card', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_card_action_text',
			[
				'label' => esc_html__('Action Text', MELA_TD),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'separator' => 'before',
				'default' => esc_html__('Details', MELA_TD),
			]
		);

		$this->add_control(
			'ma_el_card_action_link',
			[
				'label' => __('Action URL', MELA_TD),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', MELA_TD),
				'label_block' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
				],
			]
		);

		$this->end_controls_section();


		/*
			* Card Styling Section
			*/
		$this->start_controls_section(
			'ma_el_section_card_styles_preset',
			[
				'label' => esc_html__('General Styles', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);
		$this->add_control(
			'ma_el_card_preset',
			[
				'label' => esc_html__('Style Preset', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'default' => 'one',
				'options' => [
					'one' => esc_html__('Variation 1', MELA_TD),
					'two' => esc_html__('Variation 2', MELA_TD),
					'three' => esc_html__('Variation 3', MELA_TD),
				],
			]
		);

		$this->add_control(
			'ma_el_card_color_scheme',
			[
				'label' => __('Color Scheme', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#5031ef',
				'selectors' => [
					'{{WRAPPER}} .ma-el-card.two .ma-el-card-action:hover, {{WRAPPER}} .ma-el-card.two .ma-el-card-title::before, {{WRAPPER}} .ma-el-card.one .ma-el-card-action:hover,
                    {{WRAPPER}} .ma-el-card.one .ma-el-card-title::before, {{WRAPPER}} .ma-el-card.three .ma-el-card-action:hover, {{WRAPPER}} .ma-el-card.three .ma-el-card-tag::before, {{WRAPPER}} .ma-el-card.three::before' => 'background-color: {{VALUE}};',

				],
			]
		);

		$this->add_control(
			'ma_el_card_background',
			[
				'label' => esc_html__('Content Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ma-el-card-body' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();


		/*
			* Card Content Styling Section
			*/
		$this->start_controls_section(
			'ma_el_section_card_styles_title',
			[
				'label' => esc_html__('Title', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'ma_el_title_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '#132c47',
				'selectors' => [
					'{{WRAPPER}} .ma-el-card-body .ma-el-card-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'card_title_typography',
				'selector' => '{{WRAPPER}} .ma-el-card-body .ma-el-card-title',
			]
		);

		$this->end_controls_section();

		// description style
		$this->start_controls_section(
			'ma_el_section_card_styles_description',
			[
				'label' => esc_html__('Description', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'ma_el_description_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ma-el-card-body .ma-el-card-description' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'card_description_typography',
				'selector' => '{{WRAPPER}} .ma-el-card-body .ma-el-card-description',
			]
		);
		$this->end_controls_section();


		$this->start_controls_section(
			'ma_el_section_card_styles_tag',
			[
				'label' => esc_html__('Tag', MELA_TD),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'ma_el_tag_color',
			[
				'label' => __('Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ma-el-card-body .ma-el-card-tag' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'card_tag_typography',
				'selector' => '{{WRAPPER}} .ma-el-card-body .ma-el-card-tag',
			]
		);

		$this->end_controls_section();
	}
	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$card_image = $this->get_settings_for_display('ma_el_card_image');
		$card_image_url_src = Group_Control_Image_Size::get_attachment_image_src($card_image['id'], 'thumbnail', $settings);
		if (empty($card_image_url_src)) {
			$card_image_url = $card_image['url'];
		} else {
			$card_image_url = $card_image_url_src;
		}

?>

		<div id="ma-el-card-<?php echo esc_attr($this->get_id()); ?>" class="ma-el-card <?php echo esc_attr($settings['ma_el_card_preset']); ?>">
			<div class="ma-el-card-thumb">
				<img src="<?php echo esc_url($card_image_url); ?>" alt="<?php echo get_post_meta($card_image['id'], '_wp_attachment_image_alt', true); ?>">
			</div>
			<div class="ma-el-card-body">
				<a href="<?php echo esc_url($settings['ma_el_card_title_link']['url']); ?>" class="ma-el-card-title"><?php echo $settings['ma_el_card_title']; ?></a>
				<p class="ma-el-card-tag"><?php echo $settings['ma_el_card_tag']; ?></p>
				<p class="ma-el-card-description">
					<?php echo $settings['ma_el_card_description']; ?>
				</p>
				<a href="<?php echo esc_url($settings['ma_el_card_action_link']['url']); ?>" class="ma-el-card-action">
					<?php if ('two' === $settings['ma_el_card_preset']) { ?>
						<i class="fa fa-arrow-right" aria-hidden="true"></i>
					<?php } else {
						echo $settings['ma_el_card_action_text'];
					}
					?>
				</a>
			</div>
		</div>

	<?php
	}

	protected function _content_template()
	{
	?>
		<div id="ma-el-card" class="ma-el-card {{ settings.ma_el_card_preset }}">
			<div class="ma-el-card-thumb">
				<img src="{{ settings.ma_el_card_image.url }}">
			</div>
			<div class="ma-el-card-body">
				<a href="{{ settings.ma_el_card_title_link.url }}" class="ma-el-card-title">{{{ settings
						.ma_el_card_title }}}</a>
				<p class="ma-el-card-tag">{{{ settings.ma_el_card_tag }}}</p>
				<p class="ma-el-card-description">{{{ settings.ma_el_card_description }}}</p>
				<a href="{{ settings.ma_el_card_action_link.url ); ?>" class="ma-el-card-action">
						<# if ( 'two' == settings.ma_el_card_preset ) {
						#><i class="fa fa-arrow-right" aria-hidden="true"></i>
						<# } else { #>
						{{{ settings.ma_el_card_action_text }}} <#
						} #>
					</a>
				</div>
			</div>
			<?php
		}
	}

	Plugin::instance()->widgets_manager->register_widget_type(new Master_Addons_Cards());

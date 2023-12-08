<?php

namespace ElementPack\Modules\BbpressStats\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Bbpress_Stats extends Module_Base {

	public function get_name() {
		return 'bdt-bbpress-stats';
	}

	public function get_title() {
		return BDTEP . esc_html__('bbPress Stats', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-bbpress-stats';
	}

	public function get_categories() {
		return ['element-pack-bbpress'];
	}

	public function get_keywords() {
		return ['bbpress', 'forum', 'community', 'discussion', 'support'];
	}

	// public function get_custom_help_url() {
	// 	return 'https://youtu.be/7vkAHZ778c4';
	// }

	protected function register_controls() {

		$this->start_controls_section(
			'section_style_bbpress_item',
			[
				'label' => esc_html__('Item', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background_color',
				'selector' => '{{WRAPPER}} .bdt-bbp-stats-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'item_border',
				'label' => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .bdt-bbp-stats-item',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-bbp-stats-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label' => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-bbp-stats-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-bbp-stats-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-bbp-stats-item',
			]
		);

		$this->add_responsive_control(
			'item_alignment',
			[
				'label'     => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
					'space-between'  => [
						'title' => esc_html__( 'Justify', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-bbp-stats-item' => 'justify-content: {{VALUE}}',
				],
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_title',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-bbp-stats-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-bbp-stats-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .bdt-bbp-stats-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_count',
			[
				'label' => esc_html__('Count', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'count_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-bbp-stats-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'count_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-bbp-stats-count' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'count_typography',
				'selector' => '{{WRAPPER}} .bdt-bbp-stats-count',
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$stats = bbp_get_statistics(); ?>
		<div class="bbpress-statistics" role="bbpress-statistics">
			<?php do_action('bbp_before_statistics'); ?>

			<div class="bdt-bbp-stats-item bdt-flex bdt-flex-middle">
				<div class="bdt-bbp-stats-title bdt-margin-right"><?php esc_html_e('Registered Users', 'bbpress'); ?></div>
				<div class="bdt-bbp-stats-count bdt-text-bold"><?php echo esc_html($stats['user_count']); ?></div>
			</div>

			<div class="bdt-bbp-stats-item bdt-flex bdt-flex-middle">
			<div class="bdt-bbp-stats-title bdt-margin-right"><?php esc_html_e('Forums', 'bbpress'); ?></div>
			<div class="bdt-bbp-stats-count bdt-text-bold"><?php echo esc_html($stats['forum_count']); ?></div>

			</div>

			<div class="bdt-bbp-stats-item bdt-flex bdt-flex-middle">
			<div class="bdt-bbp-stats-title bdt-margin-right"><?php esc_html_e('Topics', 'bbpress'); ?></div>
			<div class="bdt-bbp-stats-count bdt-text-bold"><?php echo esc_html($stats['topic_count']); ?></div>
			</div>

<div class="bdt-bbp-stats-item bdt-flex bdt-flex-middle">
			<div class="bdt-bbp-stats-title bdt-margin-right"><?php esc_html_e('Replies', 'bbpress'); ?></div>
			<div class="bdt-bbp-stats-count bdt-text-bold"><?php echo esc_html($stats['reply_count']); ?></div>
			</div>

<div class="bdt-bbp-stats-item bdt-flex bdt-flex-middle">
			<div class="bdt-bbp-stats-title bdt-margin-right"><?php esc_html_e('Topic Tags', 'bbpress'); ?></div>
			<div class="bdt-bbp-stats-count bdt-text-bold"><?php echo esc_html($stats['topic_tag_count']); ?></div>
			</div>

			<?php if (!empty($stats['empty_topic_tag_count'])) : ?>
			<div class="bdt-bbp-stats-item bdt-flex bdt-flex-middle">

				<div class="bdt-bbp-stats-title bdt-margin-right"><?php esc_html_e('Empty Topic Tags', 'bbpress'); ?></div>
				<div class="bdt-bbp-stats-count bdt-text-bold"><?php echo esc_html($stats['empty_topic_tag_count']); ?></div>

			</div>
			<?php endif; ?>

			<?php if (!empty($stats['topic_count_hidden'])) : ?>
			<div class="bdt-bbp-stats-item bdt-flex bdt-flex-middle">

				<div class="bdt-bbp-stats-title bdt-margin-right"><?php esc_html_e('Hidden Topics', 'bbpress'); ?></div>
				<div class="bdt-bbp-stats-count bdt-text-bold"><abbr title="<?php echo esc_attr($stats['hidden_topic_title']); ?>"><?php echo esc_html($stats['topic_count_hidden']); ?></abbr></div>

			</div>
			<?php endif; ?>

			<?php if (!empty($stats['reply_count_hidden'])) : ?>
			<div class="bdt-bbp-stats-item bdt-flex bdt-flex-middle">

				<div class="bdt-bbp-stats-title bdt-margin-right"><?php esc_html_e('Hidden Replies', 'bbpress'); ?></div>
				<div class="bdt-bbp-stats-count bdt-text-bold"><abbr title="<?php echo esc_attr($stats['hidden_reply_title']); ?>"><?php echo esc_html($stats['reply_count_hidden']); ?></abbr></div>

			</div>
			<?php endif; ?>

			<?php do_action('bbp_after_statistics'); ?>

			<?php wp_reset_postdata(  );?>
		</div>
<?php
	}
}

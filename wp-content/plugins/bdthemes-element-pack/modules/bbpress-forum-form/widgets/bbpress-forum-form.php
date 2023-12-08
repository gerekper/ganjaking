<?php

namespace ElementPack\Modules\BbpressForumForm\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Bbpress_Forum_Form extends Module_Base {

	public function get_name() {
		return 'bdt-bbpress-forum-form';
	}

	public function get_title() {
		return BDTEP . esc_html__('bbPress Forum Form', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-bbpress-forum-form';
	}

	public function get_categories() {
		return ['element-pack-bbpress'];
	}

	public function get_keywords() {
		return ['bbpress', 'forum', 'community', 'discussion', 'support', 'form'];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/7vkAHZ778c4';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_style_bbpress_forum_form',
			[
				'label' => esc_html__('Forum Form', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'main_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-bbp-forum-form legend' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_border',
				'label' => esc_html__('Border', 'elementor-addons'),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '1',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'unit' => 'px',
							'isLinked' => false,
						],
					],
					'color' => [
						'default' => '#c0c0c0',
					],
				],
				'selector' => '{{WRAPPER}} .bdt-bbp-forum-form .bbp-form',
			]
		);

		$this->add_responsive_control(
			'form_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-bbp-forum-form .bbp-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_padding',
			[
				'label' => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-bbp-forum-form .bbp-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-bbp-forum-form .bbp-form' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'main_title_typography',
				'label' => esc_html__( 'Title Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-bbp-forum-form legend',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_label',
			[
				'label' => esc_html__( 'Label', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-bbp-forum-form label' => 'color: {{VALUE}};',
				],
			]
		);

		// $this->add_responsive_control(
		// 	'label_margin',
		// 	[
		// 		'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
		// 		'type' => Controls_Manager::DIMENSIONS,
		// 		'size_units' => [ 'px', 'em', '%' ],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-bbp-forum-form label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 		],
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .bdt-bbp-forum-form label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__( 'Input', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'input_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-bbp-forum-form input::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-bbp-forum-form textarea::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'input_text_background',
				'selector' => '{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border',
				'label' => esc_html__('Border', 'elementor-addons'),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '1',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'unit' => 'px',
							'isLinked' => false,
						],
					],
					'color' => [
						'default' => '#c0c0c0',
					],
				],
				'selector' => '{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea',
			]
		);

		$this->add_responsive_control(
			'input_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'input_padding',
			[
				'label' => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'input_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'label' => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea',
			]
		);

		$this->add_responsive_control(
			'input_height',
			[
				'label' => esc_html__( 'Input Height', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select' => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'textarea_height',
			[
				'label' => esc_html__( 'Textarea Height', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 200,
				],
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-bbp-forum-form textarea#bbp_forum_content' => 'height: {{SIZE}}{{UNIT}}; width: 100%;',
				],
			]
		);

		$this->add_responsive_control(
			'input_textarea_width',
			[
				'label' => esc_html__( 'Input/Textarea Width(%)', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea' => 'width: {{SIZE}}%; max-width: {{SIZE}}%;',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_submit_button',
			[
				'label' => esc_html__( 'Submit Button', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_alignment',
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
					'justify'  => [
						'title' => esc_html__( 'Justify', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors_dictionary' => [
					'left' => 'text-align: left; float: inherit;',
					'right' => 'text-align: right; float: inherit;',
					'center' => 'text-align: center; float: inherit;',
					'justify' => 'text-align: justify; float: inherit; width: 100%;',
				],
				'selectors' => [
					'{{WRAPPER}} .bbp-submit-wrapper' => '{{VALUE}};',
					'{{WRAPPER}} .bbp-submit-wrapper button' => '{{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bbp-submit-wrapper button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_background_color',
				'selector' => '{{WRAPPER}} .bbp-submit-wrapper button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'label' => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .bbp-submit-wrapper button',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bbp-submit-wrapper button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bbp-submit-wrapper button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bbp-submit-wrapper button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .bbp-submit-wrapper button',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bbp-submit-wrapper button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-submit-wrapper button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_background_color_hover',
				'selector' => '{{WRAPPER}} .bbp-submit-wrapper button:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bbp-submit-wrapper button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_notice',
			[
				'label' => esc_html__( 'Notice', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'notice_text_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} div.bbp-template-notice, {{WRAPPER}} div.indicator-hint' => 'color: {{VALUE}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'notice_background_color',
				'selector' => '{{WRAPPER}} div.bbp-template-notice, {{WRAPPER}} div.indicator-hint',
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'notice_border',
				'label' => esc_html__('Border', 'elementor-addons'),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '1',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'unit' => 'px',
							'isLinked' => false,
						],
					],
					'color' => [
						'default' => '#c0c0c0',
					],
				],
				'selector' => '{{WRAPPER}} div.bbp-template-notice, {{WRAPPER}} div.indicator-hint',
			]
		);

		$this->add_responsive_control(
			'notice_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} div.bbp-template-notice, {{WRAPPER}} div.indicator-hint' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'notice_padding',
			[
				'label' => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} div.bbp-template-notice, {{WRAPPER}} div.indicator-hint' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'notice_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} div.bbp-template-notice, {{WRAPPER}} div.indicator-hint' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'notice_text_typography',
				'label' => esc_html__( 'Title Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} div.bbp-template-notice p, {{WRAPPER}} div.bbp-template-notice li',
			]
		);

		$this->end_controls_section();

	}

	public function render() {
		bbp_set_query_name('bbp_forum_form');
		$this->render_form_forum();
		wp_reset_postdata();
	}
	public function render_form_forum() {
		if (bbp_is_forum_edit()) : ?>
			<div id="bbpress-forums" class="bbpress-wrapper">
				<?php bbp_breadcrumb(); ?>
				<?php bbp_single_forum_description(array('forum_id' => bbp_get_forum_id())); ?>
			<?php endif; ?>
			<?php if (bbp_current_user_can_access_create_forum_form()) : ?>

				<div id="new-forum-<?php bbp_forum_id(); ?>" class="bbp-forum-form bdt-bbp-forum-form">

					<form id="new-post" name="new-post" method="post">

						<?php do_action('bbp_theme_before_forum_form'); ?>

						<fieldset class="bbp-form">
							<legend>

								<?php
								if (bbp_is_forum_edit()) :
									printf(esc_html__('Now Editing &ldquo;%s&rdquo;', 'bbpress'), bbp_get_forum_title());
								else :
									bbp_is_single_forum()
										? printf(esc_html__('Create New Forum in &ldquo;%s&rdquo;', 'bbpress'), bbp_get_forum_title())
										: esc_html_e('Create New Forum', 'bbpress');
								endif;
								?>

							</legend>

							<?php do_action('bbp_theme_before_forum_form_notices'); ?>

							<?php if (!bbp_is_forum_edit() && bbp_is_forum_closed()) : ?>

								<div class="bbp-template-notice">
									<ul>
										<li><?php esc_html_e('This forum is closed to new content, however your posting capabilities still allow you to post.', 'bbpress'); ?></li>
									</ul>
								</div>

							<?php endif; ?>

							<?php if (current_user_can('unfiltered_html')) : ?>

								<div class="bbp-template-notice">
									<ul>
										<li><?php esc_html_e('Your account has the ability to post unrestricted HTML content.', 'bbpress'); ?></li>
									</ul>
								</div>

							<?php endif; ?>

							<?php do_action('bbp_template_notices'); ?>

							<div>

								<?php do_action('bbp_theme_before_forum_form_title'); ?>

								<p>
									<label for="bbp_forum_title"><?php printf(esc_html__('Forum Name (Maximum Length: %d):', 'bbpress'), bbp_get_title_max_length()); ?></label><br />
									<input type="text" id="bbp_forum_title" value="<?php bbp_form_forum_title(); ?>" size="40" name="bbp_forum_title" maxlength="<?php bbp_title_max_length(); ?>" />
								</p>

								<?php do_action('bbp_theme_after_forum_form_title'); ?>

								<?php do_action('bbp_theme_before_forum_form_content'); ?>

								<?php bbp_the_content(array('context' => 'forum')); ?>

								<?php do_action('bbp_theme_after_forum_form_content'); ?>

								<?php if (!(bbp_use_wp_editor() || current_user_can('unfiltered_html'))) : ?>

									<p class="form-allowed-tags">
										<label><?php esc_html_e('You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes:', 'bbpress'); ?></label><br />
										<code><?php bbp_allowed_tags(); ?></code>
									</p>

								<?php endif; ?>

								<?php if (bbp_allow_forum_mods() && current_user_can('assign_moderators')) : ?>

									<?php do_action('bbp_theme_before_forum_form_mods'); ?>

									<p>
										<label for="bbp_moderators"><?php esc_html_e('Forum Moderators:', 'bbpress'); ?></label><br />
										<input type="text" value="<?php bbp_form_forum_moderators(); ?>" size="40" name="bbp_moderators" id="bbp_moderators" />
									</p>

									<?php do_action('bbp_theme_after_forum_form_mods'); ?>

								<?php endif; ?>

								<?php do_action('bbp_theme_before_forum_form_type'); ?>

								<p>
									<label for="bbp_forum_type"><?php esc_html_e('Forum Type:', 'bbpress'); ?></label><br />
									<?php bbp_form_forum_type_dropdown(); ?>
								</p>

								<?php do_action('bbp_theme_after_forum_form_type'); ?>

								<?php do_action('bbp_theme_before_forum_form_status'); ?>

								<p>
									<label for="bbp_forum_status"><?php esc_html_e('Status:', 'bbpress'); ?></label><br />
									<?php bbp_form_forum_status_dropdown(); ?>
								</p>

								<?php do_action('bbp_theme_after_forum_form_status'); ?>

								<?php do_action('bbp_theme_before_forum_visibility_status'); ?>

								<p>
									<label for="bbp_forum_visibility"><?php esc_html_e('Visibility:', 'bbpress'); ?></label><br />
									<?php bbp_form_forum_visibility_dropdown(); ?>
								</p>

								<?php do_action('bbp_theme_after_forum_visibility_status'); ?>

								<?php do_action('bbp_theme_before_forum_form_parent'); ?>

								<p>
									<label for="bbp_forum_parent_id"><?php esc_html_e('Parent Forum:', 'bbpress'); ?></label><br />

									<?php
									bbp_dropdown(array(
										'select_id' => 'bbp_forum_parent_id',
										'show_none' => esc_html__('&mdash; No parent &mdash;', 'bbpress'),
										'selected'  => bbp_get_form_forum_parent(),
										'exclude'   => bbp_get_forum_id()
									));
									?>
								</p>

								<?php do_action('bbp_theme_after_forum_form_parent'); ?>

								<?php do_action('bbp_theme_before_forum_form_submit_wrapper'); ?>

								<div class="bbp-submit-wrapper">

									<?php do_action('bbp_theme_before_forum_form_submit_button'); ?>

									<button type="submit" id="bbp_forum_submit" name="bbp_forum_submit" class="button submit"><?php esc_html_e('Submit', 'bbpress'); ?></button>

									<?php do_action('bbp_theme_after_forum_form_submit_button'); ?>

								</div>

								<?php do_action('bbp_theme_after_forum_form_submit_wrapper'); ?>

							</div>

							<?php bbp_forum_form_fields(); ?>

						</fieldset>

						<?php do_action('bbp_theme_after_forum_form'); ?>

					</form>
				</div>

			<?php elseif (bbp_is_forum_closed()) : ?>

				<div id="no-forum-<?php bbp_forum_id(); ?>" class="bbp-no-forum">
					<div class="bbp-template-notice">
						<ul>
							<li><?php printf(esc_html__('The forum &#8216;%s&#8217; is closed to new content.', 'bbpress'), bbp_get_forum_title()); ?></li>
						</ul>
					</div>
				</div>

			<?php else : ?>

				<div id="no-forum-<?php bbp_forum_id(); ?>" class="bbp-no-forum">
					<div class="bbp-template-notice">
						<ul>
							<li><?php is_user_logged_in()
									? esc_html_e('You cannot create new forums.',               'bbpress')
									: esc_html_e('You must be logged in to create new forums.', 'bbpress');
								?></li>
						</ul>
					</div>
				</div>

			<?php endif; ?>
			<?php if (bbp_is_forum_edit()) : ?>
			</div>
<?php endif;
		}
	}

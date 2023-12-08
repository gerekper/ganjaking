<?php

namespace ElementPack\Modules\BbpressReplyForm\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Bbpress_Reply_Form extends Module_Base {

	public function get_name() {
		return 'bdt-bbpress-reply-form';
	}

	public function get_title() {
		return BDTEP . esc_html__('bbPress Reply Form', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-bbpress-reply-form';
	}

	public function get_categories() {
		return ['element-pack-bbpress'];
	}

	public function get_keywords() {
		return ['bbpress', 'forum', 'community', 'discussion', 'support'];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/7vkAHZ778c4';
	}


	protected function register_controls() {

		//Style
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
				'label'     => esc_html__('Title Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-reply-form .bbp-form legend' => 'color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .bbp-reply-form .bbp-form',
			]
		);

		$this->add_responsive_control(
			'form_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bbp-reply-form .bbp-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bbp-reply-form .bbp-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bbp-reply-form .bbp-form' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'main_title_typography',
				'label' => esc_html__('Title Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bbp-reply-form .bbp-form legend',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_label',
			[
				'label' => esc_html__('Label', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-reply-form label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'label_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bbp-reply-form label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .bbp-reply-form label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__('Input', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'input_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-reply-form input::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bbp-reply-form textarea::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
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
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'input_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'label' => esc_html__('Typography', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bbp-reply-form fieldset.bbp-form select, {{WRAPPER}} .bbp-reply-form fieldset.bbp-form input[type="text"], {{WRAPPER}} .bbp-reply-form fieldset.bbp-form input[type="password"], {{WRAPPER}} fieldset.bbp-form select, {{WRAPPER}} fieldset.bbp-form input[type="text"], {{WRAPPER}} fieldset.bbp-form input[type="password"]' => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .bbp-reply-form textarea#bbp_reply_content, {{WRAPPER}} .bbp-reply-form textarea#bbp_topic_content, {{WRAPPER}} .bbp-reply-form textarea#bbp_forum_content' => 'height: {{SIZE}}{{UNIT}}; width: 100%;',
				],
			]
		);

		$this->add_responsive_control(
			'input_textarea_width',
			[
				'label' => esc_html__( 'Input/Textarea Width(%)', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bbp-reply-form fieldset.bbp-form select, {{WRAPPER}} .bbp-reply-form fieldset.bbp-form input[type="text"], {{WRAPPER}} .bbp-reply-form fieldset.bbp-form input[type="password"], {{WRAPPER}} fieldset.bbp-form select, {{WRAPPER}} fieldset.bbp-form input[type="text"], {{WRAPPER}} fieldset.bbp-form input[type="password"], {{WRAPPER}} .bbp-reply-form textarea#bbp_reply_content, {{WRAPPER}} .bbp-reply-form textarea#bbp_topic_content, {{WRAPPER}} .bbp-reply-form textarea#bbp_forum_content' => 'width: {{SIZE}}%; max-width: {{SIZE}}%;',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_submit_button',
			[
				'label' => esc_html__('Submit Button', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_alignment',
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
					'justify'  => [
						'title' => esc_html__('Justify', 'bdthemes-element-pack'),
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
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
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
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .bbp-submit-wrapper button',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bbp-submit-wrapper button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bbp-submit-wrapper button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
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
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
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
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
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
		bbp_set_query_name('bbp_reply_form');
		$this->render_replay_form();
		wp_reset_postdata();
	}

	public function render_replay_form() {
		$settings = $this->get_settings_for_display();

?>
		<?php if (bbp_is_reply_edit()) : ?>
			<div id="bbpress-forums" class="bbpress-wrapper">
				<?php if ($settings['show_breadcrumb']) : ?>
					<?php bbp_breadcrumb(); ?>
				<?php endif; ?>
			<?php endif; ?>
			<?php if (bbp_current_user_can_access_create_reply_form()) : ?>
				<div id="new-reply-<?php bbp_topic_id(); ?>" class="bbp-reply-form">
					<form id="new-post" name="new-post" method="post">
						<?php do_action('bbp_theme_before_reply_form'); ?>
						<fieldset class="bbp-form">
							<legend><?php printf(esc_html__('Reply To: %s', 'bbpress'), (bbp_get_form_reply_to()) ? sprintf(esc_html__('Reply #%1$s in %2$s', 'bbpress'), bbp_get_form_reply_to(), bbp_get_topic_title()) : bbp_get_topic_title()); ?></legend>
							<?php do_action('bbp_theme_before_reply_form_notices'); ?>
							<?php if (!bbp_is_topic_open() && !bbp_is_reply_edit()) : ?>
								<div class="bbp-template-notice">
									<ul>
										<li><?php esc_html_e('This topic is marked as closed to new replies, however your posting capabilities still allow you to reply.', 'bbpress'); ?></li>
									</ul>
								</div>
							<?php endif; ?>
							<?php if (!bbp_is_reply_edit() && bbp_is_forum_closed()) : ?>
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
								<?php if (bbp_current_user_can_access_anonymous_user_form()) : ?>
									<?php do_action('bbp_theme_before_anonymous_form'); ?>
									<fieldset class="bbp-form">
										<legend><?php (bbp_is_topic_edit() || bbp_is_reply_edit()) ? esc_html_e('Author Information', 'bbpress') : esc_html_e('Your information:', 'bbpress'); ?></legend>

										<?php do_action('bbp_theme_anonymous_form_extras_top'); ?>

										<p>
											<label class="bdt-inline-block" for="bbp_anonymous_author"><?php esc_html_e('Name (required):', 'bbpress'); ?></label><br />
											<input type="text" id="bbp_anonymous_author" value="<?php bbp_author_display_name(); ?>" size="40" maxlength="100" name="bbp_anonymous_name" autocomplete="off" />
										</p>

										<p>
											<label class="bdt-inline-block" for="bbp_anonymous_email"><?php esc_html_e('Mail (will not be published) (required):', 'bbpress'); ?></label><br />
											<input type="text" id="bbp_anonymous_email" value="<?php bbp_author_email(); ?>" size="40" maxlength="100" name="bbp_anonymous_email" />
										</p>

										<p>
											<label class="bdt-inline-block" for="bbp_anonymous_website"><?php esc_html_e('Website:', 'bbpress'); ?></label><br />
											<input type="text" id="bbp_anonymous_website" value="<?php bbp_author_url(); ?>" size="40" maxlength="200" name="bbp_anonymous_website" />
										</p>

										<?php do_action('bbp_theme_anonymous_form_extras_bottom'); ?>

									</fieldset>

									<?php do_action('bbp_theme_after_anonymous_form'); ?>

								<?php endif; ?>
								<?php do_action('bbp_theme_before_reply_form_content'); ?>
								<?php bbp_the_content(array('context' => 'reply')); ?>
								<?php do_action('bbp_theme_after_reply_form_content'); ?>
								<?php if (!(bbp_use_wp_editor() || current_user_can('unfiltered_html'))) : ?>
									<p class="form-allowed-tags">
										<label class="bdt-inline-block"><?php esc_html_e('You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes:', 'bbpress'); ?></label><br />
										<code><?php bbp_allowed_tags(); ?></code>
									</p>
								<?php endif; ?>
								<?php if (bbp_allow_topic_tags() && current_user_can('assign_topic_tags', bbp_get_topic_id())) : ?>
									<?php do_action('bbp_theme_before_reply_form_tags'); ?>
									<p>
										<label class="bdt-inline-block" for="bbp_topic_tags"><?php esc_html_e('Tags:', 'bbpress'); ?></label><br />
										<input type="text" value="<?php bbp_form_topic_tags(); ?>" size="40" name="bbp_topic_tags" id="bbp_topic_tags" <?php disabled(bbp_is_topic_spam()); ?> />
									</p>
									<?php do_action('bbp_theme_after_reply_form_tags'); ?>
								<?php endif; ?>
								<?php if (bbp_is_subscriptions_active() && !bbp_is_anonymous() && (!bbp_is_reply_edit() || (bbp_is_reply_edit() && !bbp_is_reply_anonymous()))) : ?>
									<?php do_action('bbp_theme_before_reply_form_subscription'); ?>
									<p>
										<input name="bbp_topic_subscription" id="bbp_topic_subscription" type="checkbox" value="bbp_subscribe" <?php bbp_form_topic_subscribed(); ?> />
										<?php if (bbp_is_reply_edit() && (bbp_get_reply_author_id() !== bbp_get_current_user_id())) : ?>
											<label class="bdt-inline-block" for="bbp_topic_subscription"><?php esc_html_e('Notify the author of follow-up replies via email', 'bbpress'); ?></label>
										<?php else : ?>
											<label class="bdt-inline-block" for="bbp_topic_subscription"><?php esc_html_e('Notify me of follow-up replies via email', 'bbpress'); ?></label>
										<?php endif; ?>
									</p>
									<?php do_action('bbp_theme_after_reply_form_subscription'); ?>
								<?php endif; ?>
								<?php if (bbp_is_reply_edit()) : ?>
									<?php if (current_user_can('moderate', bbp_get_reply_id())) : ?>
										<?php do_action('bbp_theme_before_reply_form_reply_to'); ?>
										<p class="form-reply-to">
											<label class="bdt-inline-block" for="bbp_reply_to"><?php esc_html_e('Reply To:', 'bbpress'); ?></label><br />
											<?php bbp_reply_to_dropdown(); ?>
										</p>
										<?php do_action('bbp_theme_after_reply_form_reply_to'); ?>
										<?php do_action('bbp_theme_before_reply_form_status'); ?>
										<p>
											<label class="bdt-inline-block" for="bbp_reply_status"><?php esc_html_e('Reply Status:', 'bbpress'); ?></label><br />
											<?php bbp_form_reply_status_dropdown(); ?>
										</p>
										<?php do_action('bbp_theme_after_reply_form_status'); ?>
									<?php endif; ?>
									<?php if (bbp_allow_revisions()) : ?>
										<?php do_action('bbp_theme_before_reply_form_revisions'); ?>
										<fieldset class="bbp-form">
											<legend>
												<input name="bbp_log_reply_edit" id="bbp_log_reply_edit" type="checkbox" value="1" <?php bbp_form_reply_log_edit(); ?> />
												<label class="bdt-inline-block" for="bbp_log_reply_edit"><?php esc_html_e('Keep a log of this edit:', 'bbpress'); ?></label><br />
											</legend>

											<div>
												<label class="bdt-inline-block" for="bbp_reply_edit_reason"><?php printf(esc_html__('Optional reason for editing:', 'bbpress'), bbp_get_current_user_name()); ?></label><br />
												<input type="text" value="<?php bbp_form_reply_edit_reason(); ?>" size="40" name="bbp_reply_edit_reason" id="bbp_reply_edit_reason" />
											</div>
										</fieldset>

										<?php do_action('bbp_theme_after_reply_form_revisions'); ?>

									<?php endif; ?>

								<?php endif; ?>

								<?php do_action('bbp_theme_before_reply_form_submit_wrapper'); ?>

								<div class="bbp-submit-wrapper">

									<?php do_action('bbp_theme_before_reply_form_submit_button'); ?>

									<?php bbp_cancel_reply_to_link(); ?>

									<button type="submit" id="bbp_reply_submit" name="bbp_reply_submit" class="button submit"><?php esc_html_e('Submit', 'bbpress'); ?></button>

									<?php do_action('bbp_theme_after_reply_form_submit_button'); ?>

								</div>

								<?php do_action('bbp_theme_after_reply_form_submit_wrapper'); ?>

							</div>

							<?php bbp_reply_form_fields(); ?>

						</fieldset>

						<?php do_action('bbp_theme_after_reply_form'); ?>

					</form>
				</div>

			<?php elseif (bbp_is_topic_closed()) : ?>

				<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
					<div class="bbp-template-notice">
						<ul>
							<li><?php printf(esc_html__('The topic &#8216;%s&#8217; is closed to new replies.', 'bbpress'), bbp_get_topic_title()); ?></li>
						</ul>
					</div>
				</div>

			<?php elseif (bbp_is_forum_closed(bbp_get_topic_forum_id())) : ?>

				<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
					<div class="bbp-template-notice">
						<ul>
							<li><?php printf(esc_html__('The forum &#8216;%s&#8217; is closed to new topics and replies.', 'bbpress'), bbp_get_forum_title(bbp_get_topic_forum_id())); ?></li>
						</ul>
					</div>
				</div>

			<?php else : ?>

				<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
					<div class="bbp-template-notice">
						<ul>
							<li><?php is_user_logged_in()
									? esc_html_e('You cannot reply to this topic.',               'bbpress')
									: esc_html_e('You must be logged in to reply to this topic.', 'bbpress');
								?></li>
						</ul>
					</div>

					<?php if (!is_user_logged_in()) : ?>
						<form method="post" action="<?php bbp_wp_login_action(array('context' => 'login_post')); ?>" class="bbp-login-form">
							<fieldset class="bbp-form">
								<legend><?php esc_html_e('Log In', 'bbpress'); ?></legend>
								<div class="bbp-username">
									<label class="bdt-inline-block" for="user_login"><?php esc_html_e('Username', 'bbpress'); ?>: </label>
									<input type="text" name="log" value="<?php bbp_sanitize_val('user_login', 'text'); ?>" size="20" maxlength="100" id="user_login" autocomplete="off" />
								</div>
								<div class="bbp-password">
									<label class="bdt-inline-block" for="user_pass"><?php esc_html_e('Password', 'bbpress'); ?>: </label>
									<input type="password" name="pwd" value="<?php bbp_sanitize_val('user_pass', 'password'); ?>" size="20" id="user_pass" autocomplete="off" />
								</div>
								<div class="bbp-remember-me">
									<input type="checkbox" name="rememberme" value="forever" <?php checked(bbp_get_sanitize_val('rememberme', 'checkbox')); ?> id="rememberme" />
									<label class="bdt-inline-block" for="rememberme"><?php esc_html_e('Keep me signed in', 'bbpress'); ?></label>
								</div>
								<?php do_action('login_form'); ?>
								<div class="bbp-submit-wrapper">
									<button type="submit" name="user-submit" id="user-submit" class="button submit user-submit"><?php esc_html_e('Log In', 'bbpress'); ?></button>
									<?php bbp_user_login_fields(); ?>
								</div>
							</fieldset>
						</form>
					<?php endif; ?>

				</div>

			<?php endif; ?>

			<?php if (bbp_is_reply_edit()) : ?>

			</div>

<?php endif;
		}
	}

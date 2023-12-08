<?php

namespace ElementPack\Modules\BbpressSingleReply\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;



if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Bbpress_Single_Reply extends Module_Base {

	public function get_name() {
		return 'bdt-bbpress-single-reply';
	}

	public function get_title() {
		return BDTEP . esc_html__('bbPress Single Reply', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-bbpress-single-reply';
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

	public function register_controls() {
		$this->start_controls_section(
			'section_bbpress_content',
			[
				'label' => __('Content', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'bbpress_reply_id',
			[
				'label'       => __('Single Replies', 'bdthemes-element-pack'),
				'type'        => Dynamic_Select::TYPE,
				'label_block' => true,
				'placeholder' => __('Type and select Single Reply', 'bdthemes-element-pack'),
				'query_args'  => [
					'query'        => 'bbpress_single_reply',
				],
			]
		);

		$this->add_control(
			'show_breadcrumb',
			[
				'label'     => __('Show Breadcrumb', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_bbpress_breadcrumb',
			[
				'label' => esc_html__('Breadcrumb', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_breadcrumb' => 'yes'
				]
			]
		);

		$this->add_control(
			'breadcrumb_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-breadcrumb *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'breadcrumb_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-breadcrumb a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'breadcrumb_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bbp-breadcrumb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'breadcrumb_typography',
				'selector' => '{{WRAPPER}} .bbp-breadcrumb',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_header',
			[
				'label' => esc_html__('Header', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'header_title_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.bbp-reply-header .bbp-meta *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'header_background',
				'selector' => '{{WRAPPER}} #bbpress-forums div.bbp-reply-header',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'header_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} #bbpress-forums div.bbp-reply-header',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'header_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.bbp-reply-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.bbp-reply-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.bbp-reply-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'header_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums div.bbp-reply-header .bbp-meta *',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_body',
			[
				'label' => esc_html__('Body', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'forum_body_odd_color',
			[
				'label'     => esc_html__('Odd Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.odd, {{WRAPPER}} #bbpress-forums ul.odd' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'forum_body_even_color',
			[
				'label'     => esc_html__('Even Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.even, {{WRAPPER}} #bbpress-forums ul.even' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'forum_body_list_border_color',
			[
				'label'     => esc_html__('Odd/Even Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.odd, {{WRAPPER}} #bbpress-forums ul.odd, {{WRAPPER}} #bbpress-forums div.even, {{WRAPPER}} #bbpress-forums ul.even' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'forum_body_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} #bbpress-forums div.odd, {{WRAPPER}} #bbpress-forums ul.odd, {{WRAPPER}} #bbpress-forums div.even, {{WRAPPER}} #bbpress-forums ul.even',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'forum_body_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.odd, {{WRAPPER}} #bbpress-forums ul.odd, {{WRAPPER}} #bbpress-forums div.even, {{WRAPPER}} #bbpress-forums ul.even' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'forum_body_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.odd, {{WRAPPER}} #bbpress-forums ul.odd, {{WRAPPER}} #bbpress-forums div.even, {{WRAPPER}} #bbpress-forums ul.even' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'forum_body_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.odd, {{WRAPPER}} #bbpress-forums ul.odd, {{WRAPPER}} #bbpress-forums div.even, {{WRAPPER}} #bbpress-forums ul.even' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_author',
			[
				'label' => esc_html__('Author', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'author_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-reply-author *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'author_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-reply-author a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'author_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-reply-author' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'author_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-reply-author *',
			]
		);

		$this->add_responsive_control(
			'author_avatar',
			[
				'label'     => esc_html__('Avatar Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.bbp-reply-author img.avatar' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'author_avatar_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} #bbpress-forums div.bbp-reply-author img.avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_forum_text',
			[
				'label' => esc_html__('Forum Text', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'forum_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-reply-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'forum_text_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-reply-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forum_text_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-reply-content',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_bbpress_forum_meta',
			[
				'label' => esc_html__('Forum Meta', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'forum_meta_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-meta *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'forum_meta_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-meta a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'forum_meta_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forum_meta_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-meta *',
			]
		);

		$this->end_controls_section();
	}


	protected function bbp_get_reply_post_date($reply_id = 0, $humanize = false, $gmt = false) {
		$reply_id = bbp_get_reply_id($reply_id);

		// 4 days, 4 hours ago
		if (!empty($humanize)) {
			$gmt_s  = !empty($gmt) ? 'G' : 'U';
			$date   = get_post_time($gmt_s, $gmt, $reply_id);
			$time   = false; // For filter below
			$result = bbp_get_time_since($date);

			// August 4, 2012 at 2:37 pm
		} else {
			$date   = get_post_time(get_option('date_format'), $gmt, $reply_id, true);
			$orgDate = $date;
			$newDate = date("m/d/Y", strtotime($orgDate));
			// print_r($date);
			$time   = get_post_time(get_option('time_format'), $gmt, $reply_id, true);
			$result = sprintf(_x('%1$s', 'date at time', 'bbpress'), $newDate);
		}

		// Filter & return
		return apply_filters('bbp_get_reply_post_date', $result, $reply_id, $humanize, $gmt, $newDate, $time);
	}
	public function render_loop_single_reply() {
?>
		<div id="post-<?php bbp_reply_id(); ?>" class="bbp-reply-header">
			<div class="bbp-meta">
				<span class="bbp-reply-post-date">
					<?php
					echo $this->bbp_get_reply_post_date();
					// bbp_reply_post_date();
					?>
				</span>
				<?php if (bbp_is_single_user_replies()) : ?>
					<span class="bbp-header">
						<?php esc_html_e('in reply to: ', 'bbpress'); ?>
						<a class="bbp-topic-permalink" href="<?php bbp_topic_permalink(bbp_get_reply_topic_id()); ?>"><?php bbp_topic_title(bbp_get_reply_topic_id()); ?></a>
					</span>
				<?php endif; ?>

				<a href="<?php bbp_reply_url(); ?>" class="bbp-reply-permalink">#<?php bbp_reply_id(); ?></a>

				<?php do_action('bbp_theme_before_reply_admin_links'); ?>

				<?php bbp_reply_admin_links(); ?>

				<?php do_action('bbp_theme_after_reply_admin_links'); ?>

			</div><!-- .bbp-meta -->
		</div><!-- #post-<?php bbp_reply_id(); ?> -->

		<div <?php bbp_reply_class(); ?>>
			<div class="bbp-reply-author">

				<?php do_action('bbp_theme_before_reply_author_details'); ?>

				<?php bbp_reply_author_link(array('show_role' => true)); ?>

				<?php if (current_user_can('moderate', bbp_get_reply_id())) : ?>

					<?php do_action('bbp_theme_before_reply_author_admin_details'); ?>

					<div class="bbp-reply-ip"><?php bbp_author_ip(bbp_get_reply_id()); ?></div>

					<?php do_action('bbp_theme_after_reply_author_admin_details'); ?>

				<?php endif; ?>

				<?php do_action('bbp_theme_after_reply_author_details'); ?>

			</div><!-- .bbp-reply-author -->

			<div class="bbp-reply-content">

				<?php do_action('bbp_theme_before_reply_content'); ?>

				<?php bbp_reply_content(); ?>

				<?php do_action('bbp_theme_after_reply_content'); ?>

			</div><!-- .bbp-reply-content -->
		</div><!-- .reply -->
	<?php
	}
	public function render_content_single_reply() {
		$settings = $this->get_settings_for_display();

	?>
		<div id="bbpress-forums" class="bbpress-wrapper">

			<?php if ($settings['show_breadcrumb']) : ?>
				<?php bbp_breadcrumb(); ?>
			<?php endif; ?>

			<?php do_action('bbp_template_before_single_reply'); ?>

			<?php if (post_password_required()) : ?>
				<div id="bbpress-forums" class="bbpress-wrapper">
					<fieldset class="bbp-form" id="bbp-protected">
						<Legend><?php esc_html_e('Protected', 'bbpress'); ?></legend>
						<?php echo get_the_password_form(); ?>
					</fieldset>
				</div>
			<?php else : ?>

				<?php $this->render_loop_single_reply(); ?>

			<?php endif; ?>

			<?php do_action('bbp_template_after_single_reply'); ?>

		</div>
	<?php
	}

	public function render_feedback_no_access() {
	?>
		<div id="forum-private" class="bbp-forum-content">
			<h1 class="entry-title"><?php esc_html_e('Private', 'bbpress'); ?></h1>
			<div class="entry-content">
				<div class="bbp-template-notice info">
					<ul>
						<li><?php esc_html_e('You do not have permission to view this forum.', 'bbpress'); ?></li>
					</ul>
				</div>
			</div>
		</div><!-- #forum-private -->
<?php
	}
	public function render() {
		$reply_id = bbpress()->current_reply_id = $this->get_settings_for_display('bbpress_reply_id');
		$forum_id = bbp_get_reply_forum_id($reply_id);

		// Bail if ID passed is not a reply
		if (!bbp_is_reply($reply_id)) {
			element_pack_alert('Ops, Your single replies ID is Missing, Please enter your specific replies ID');
			return;
		}

		// Reset the queries if not in theme compat
		if (!bbp_is_theme_compat_active()) {
			$bbp = bbpress();
			// Reset necessary forum_query attributes for reply loop to function
			$bbp->forum_query->query_vars['post_type'] = bbp_get_forum_post_type();
			$bbp->forum_query->in_the_loop             = true;
			$bbp->forum_query->post                    = get_post($forum_id);

			// Reset necessary reply_query attributes for reply loop to function
			$bbp->reply_query->query_vars['post_type'] = bbp_get_reply_post_type();
			$bbp->reply_query->in_the_loop             = true;
			$bbp->reply_query->post                    = get_post($reply_id);
		}

		// Start output buffer
		bbp_set_query_name('bbp_single_reply');

		// Check forum caps
		if (bbp_user_can_view_forum(array('forum_id' => $forum_id))) {
			$this->render_content_single_reply();

			// Forum is private and user does not have caps
		} elseif (bbp_is_forum_private($forum_id, false)) {
			$this->render_feedback_no_access();
		}

		// reset query
		wp_reset_postdata();
	}
}

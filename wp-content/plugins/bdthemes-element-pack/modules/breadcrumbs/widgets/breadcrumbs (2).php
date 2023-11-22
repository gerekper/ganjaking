<?php

namespace ElementPack\Modules\Breadcrumbs\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Breadcrumbs extends Module_Base {

	public function get_name() {
		return 'bdt-breadcrumbs';
	}

	public function get_title() {
		return BDTEP . esc_html__('Breadcrumbs', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-breadcrumbs';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['breadcrumbs'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-breadcrumbs'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/32yrjPHq-AA';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_breadcrumbs_content',
			[
				'label' => esc_html__('Breadcrumbs', 'bdthemes-element-pack'),
			]
		);


		$this->add_responsive_control(
			'breadcrumbs_align',
			[
				'label' => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon' => 'eicon-text-align-right',
					],
				],
				// 'prefix_class' => 'elementor%s-align-',
			]
		);

		$this->add_control(
			'breadcrumbs_separator',
			[
				'label' => esc_html__('Separator', 'bdthemes-element-pack'),
				'description' => esc_html__('Use any special character for separator, for example / -', 'bdthemes-element-pack'),
				'type' => Controls_Manager::TEXT,
				//'default' => "/",
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-breadcrumb>:nth-child(n+2):not(.bdt-first-column)::before' => 'content: "{{VALUE}}";',
				],
			]
		);

		$this->add_control(
			'home_icon',
			[
				'label' => esc_html__('Home Icon', 'bdthemes-element-pack'),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'render_type'  => 'template',
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__('Breadcrumbs', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'home_icon_color',
			[
				'label' => esc_html__('Home Icon Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-breadcrumbs-home-icon' => 'color: {{VALUE}};',
				],
				'condition'        => [
					'home_icon[value]!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'breadcrumb_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-breadcrumb>*>* ,
							 {{WRAPPER}} .bdt-ep-breadcrumb>:nth-child(n+2):not(.bdt-first-column)::before',
			]
		);


		$this->start_controls_tabs('tabs_breadcrumbs_style');

		$this->start_controls_tab(
			'tab_color_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'breadcrumb_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-breadcrumb>*>*' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'label'     => 'Background Color',
				'name'      => 'link_bg_color',
				'selector'  => '{{WRAPPER}} .bdt-ep-breadcrumb>*>*',
			]
		);

		$this->add_responsive_control(
			'link_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-breadcrumb>*>*' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'link_border',
				'label'    => esc_html__('Border', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-breadcrumb>*>*',
			]
		);

		$this->add_responsive_control(
			'link_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-breadcrumb>*>*' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_color_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-breadcrumb>*>*:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'label'     => 'Background Color',
				'name'      => 'link_hover_bg_color',
				'selector'  => '{{WRAPPER}} .bdt-ep-breadcrumb>*>*:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'link_hover_border',
				'label'    => esc_html__('Border', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-breadcrumb>*>*:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'active_item_section',
			[
				'label' => esc_html__('Active Item', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'active_item_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-breadcrumb>:last-child>span',
			]
		);

		$this->start_controls_tabs('active_item_tabs');

		$this->start_controls_tab(
			'active_item_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'active_item_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-breadcrumb>:last-child>span' => 'color: {{VALUE}}!important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'label'     => 'Background Color',
				'name'      => 'active_item_bg_color',
				'selector'  => '{{WRAPPER}} .bdt-ep-breadcrumb>:last-child>span',
			]
		);

		$this->add_responsive_control(
			'active_item_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-breadcrumb>:last-child>span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'active_item_border',
				'label'    => esc_html__('Border', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-breadcrumb>:last-child>span',
			]
		);

		$this->add_responsive_control(
			'active_item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-breadcrumb>:last-child>span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'active_item_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'active_item_hover_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-breadcrumb>:last-child>span:hover' => 'color: {{VALUE}}!important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'label'     => 'Background Color',
				'name'      => 'active_item_hover_bg_color',
				'selector'  => '{{WRAPPER}} .bdt-ep-breadcrumb>:last-child>span:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'active_item_hover_border',
				'label'    => esc_html__('Border', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-breadcrumb>:last-child>span:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();


		$this->end_controls_section();


		$this->start_controls_section(
			'separator_style',
			[
				'label' => esc_html__('Separator', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-breadcrumb>:nth-child(n+2):not(.bdt-first-column)::before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'label'     => 'Background Color',
				'name'      => 'separator_bg_color',
				'selector'  => '{{WRAPPER}} .bdt-ep-breadcrumb>:nth-child(n+2):not(.bdt-first-column)::before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'separator_size',
				// 'scheme' => Schemes\Typography::TYPOGRAPHY_2,
				'selector' => '{{WRAPPER}} .bdt-ep-breadcrumb>:nth-child(n+2):not(.bdt-first-column)::before',
			]
		);

		$this->add_responsive_control(
			'separator_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-breadcrumb>:nth-child(n+2):not(.bdt-first-column)::before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);


		$this->add_responsive_control(
			'separator_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-breadcrumb>:nth-child(n+2):not(.bdt-first-column)::before' => 'margin: 0px {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_section();
	}


	private function _breadcrumbs($align, $home_icon) {
		$home_icon = (!empty($home_icon)) ? $home_icon : '';
		$showOnHome  = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
		$delimiter   = '/'; // delimiter between crumbs
		$home        = get_bloginfo('name'); // text for the 'Home' link
		$blog        = get_theme_mod('rooten_blog_title', 'Blog');
		$shop        = get_theme_mod('rooten_woocommerce_title', 'Shop');
		$forums      = get_theme_mod('rooten_bbpress_title', 'Forum');
		$showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
		$before      = '<li><span> '; // tag before the current crumb
		$after       = '</span></li>'; // tag after the current crumb
		$output      = array();
		$class       = ['bdt-ep-breadcrumb'];
		$class[]     = ($align == 'left') ? 'breadcrumb-align-left' : null;
		$class[]     = ($align == 'center') ? 'breadcrumb-align-center' : null;
		$class[]     = ($align == 'right') ? 'breadcrumb-align-right' : null;
		$class       = implode(' ', $class);
		$homeLink    = home_url('/');

		global $post;
		global $woocommerce;

		if ($woocommerce) {
			$shopLink = get_permalink(wc_get_page_id('shop'));
		}

		$forumLink = get_post_type_archive_link('forum');

		if (is_home() || is_front_page()) {
			if ($showOnHome == 1) {
				$output[] = '<ul class="' . $class . '">
	            <li><a href="' . esc_url($homeLink) . '">' . esc_html($home) . '</a></li><li><span> ' . esc_html($blog) . ' </span></li></ul>';
			}
		} else {
			$output[] = '<ul class="' . $class . '"><li><a href="' . esc_url($homeLink) . '">' . $home_icon . esc_html($home) . '</a></li>';

			if (is_category()) {
				$thisCat = get_category(get_query_var('cat'), false);
				if ($thisCat->parent != 0) {
					$output[] = get_category_parents($thisCat->parent, TRUE, ' ') . '';
				}
				$output[] = $before . esc_html__('Category', 'bdthemes-element-pack') . ': ' . esc_html(single_cat_title('', false)) . '' . $after;
			} elseif (is_search()) {
				$output[] = $before . esc_html__('Search', 'bdthemes-element-pack') . $after;
			} elseif (is_day()) {
				$output[] = '<li><a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a></li>';
				$output[] = '<li><a href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '">' . esc_html(get_the_time('F')) . '</a></li>';
				$output[] = $before . get_the_time('d') . $after;
			} elseif (is_month()) {
				$output[] = '<li><a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a></li>';
				$output[] = $before . esc_html(get_the_time('F')) . $after;
			} elseif (is_year()) {
				$output[] = $before . esc_html(get_the_time('Y')) . $after;
			} elseif (class_exists('Woocommerce') && is_shop()) {
				$output[] = '<li><a href="' . esc_url($shopLink) . '">' . esc_html($shop) . '</a></li>';
			} elseif (class_exists('Woocommerce') && is_product()) {
				$output[] = '<li><a href="' . esc_url($shopLink) . '">' . esc_html($shop) . '</a></li> ' . $before . esc_html(get_the_title()) . $after;
			} elseif (class_exists('bbPress') && is_bbpress()) {
				$output[] = '<li><a href="' . esc_url($forumLink) . '">' . esc_html($forums) . '</a></li> ' . $before . esc_html(get_the_title()) . $after . '</a></li>';
			} elseif (is_single() && !is_attachment()) {
				if (get_post_type() != 'post') {
					if ($showCurrent == 1) {
						$output[] = ' ' . $before . get_the_title() . $after;
					}
				} else {
					$cat = get_the_category();
					$cat = $cat[0];
					$cats = get_category_parents($cat, TRUE, ' ');
					if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
					$output[] = '<li>' . $cats . '</li>'; // No need to escape here
					if ($showCurrent == 1) $output[] = $before . get_the_title() . $after;
				}
			} elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
				$post_type = get_post_type_object(get_post_type());
				$output[] = $before . wp_kses_post($post_type->labels->singular_name) . $after;
			} elseif (is_attachment()) {
				if ($showCurrent == 1) $output[] = $before . get_the_title() . $after;
			} elseif (is_page() && !$post->post_parent) {
				if ($showCurrent == 1) $output[] = $before . get_the_title() . $after;
			} elseif (is_page() && $post->post_parent) {
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_post($parent_id);
					$breadcrumbs[] = '<li><a href="' . esc_url(get_permalink($page->ID)) . '">' . get_the_title($page->ID) . '</a></li>';
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				for ($i = 0; $i < count($breadcrumbs); $i++) {
					$output[] = $breadcrumbs[$i]; // No need to escape here
					//if ($i != count($breadcrumbs)-1) $output[] = ' ' . esc_html($delimiter) . ' ';
				}
				if ($showCurrent == 1) $output[] = $before . get_the_title() . $after;
			} elseif (is_tag()) {
				$output[] = $before . esc_html__('Tag', 'bdthemes-element-pack') . ': ' . esc_html(single_tag_title('', false)) . $after;
			} elseif (is_author()) {
				global $author;
				$userdata = get_userdata($author);
				$output[] = $before . esc_html__('Articles by', 'bdthemes-element-pack') . ' ' . esc_html($userdata->display_name) . $after;
			} elseif (is_404()) {
				$output[] = $before . esc_html__('Error 404', 'bdthemes-element-pack') . $after;
			}
			if (get_query_var('paged')) {
				$output[] = $before . '(' . esc_html__('Page', 'bdthemes-element-pack') . ' ' . esc_html(get_query_var('paged')) . ')' . $after;
			}
			$output[] = '</ul>';
		}

		$output = implode("\n", $output);

		echo wp_kses_post($output);
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$align = (isset($settings['breadcrumbs_align']) && !empty($settings['breadcrumbs_align']) ? $settings['breadcrumbs_align'] : 'left');
		$home_icon = '';
		if (!empty($settings['home_icon']['value'])) {
			$home_icon = '<span class="bdt-ep-breadcrumbs-home-icon"> <i aria-hidden="true" class="' . $settings['home_icon']['value'] . '"></i> </span>';
		}

		echo "<div class='bdt-ep-breadcrumbs-wrapper'>";

		$this->_breadcrumbs($align, $home_icon);
		echo "</div>";
	}
}

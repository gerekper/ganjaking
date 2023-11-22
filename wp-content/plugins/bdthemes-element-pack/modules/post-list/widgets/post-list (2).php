<?php

namespace ElementPack\Modules\PostList\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use ElementPack\Utils;

use ElementPack\Base\Module_Base;
use ElementPack\Traits\Global_Widget_Controls;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Post_List extends Module_Base {
	use Group_Control_Query;
	use Global_Widget_Controls;

	public $_query = null;

	public function get_name() {
		return 'bdt-post-list';
	}

	public function get_title() {
		return BDTEP . esc_html__('Post List', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-post-list';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['post', 'list', 'blog', 'recent', 'news'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-post-list'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/5aQTAsLRF0o';
	}

	public function get_query() {
		return $this->_query;
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'title_tags',
			[
				'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => element_pack_title_tags(),
				'condition' => [
					'show_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_image',
			[
				'label'   => esc_html__('Image', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_horizontal',
			[
				'label' => esc_html__('Multiple Columns', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_responsive_control(
			'column',
			[
				'label'       => esc_html__('Column', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT,
				'default'        => '2',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'description' => esc_html__('For good looking set it 1 for default skin and 2 for another skin', 'bdthemes-element-pack'),
				'options'     => [
					'1' => esc_html__('One', 'bdthemes-element-pack'),
					'2' => esc_html__('Two', 'bdthemes-element-pack'),
					'3' => esc_html__('Three', 'bdthemes-element-pack'),
					'4' => esc_html__('Four', 'bdthemes-element-pack'),
				],
				'render_type' => 'template',
				'condition' => [
					'show_horizontal' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-list-grid' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'space_between',
			[
				'label'      => esc_html__('Gap', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-list .bdt-list > li:nth-child(n+2)' => 'margin-top: {{SIZE}}{{UNIT}}; padding-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.bdt-has-divider--yes .bdt-post-list .bdt-grid-2 li:nth-child(n+3) > div' => 'padding-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.bdt-has-divider--yes .bdt-post-list .bdt-grid-3 li:nth-child(n+4) > div' => 'padding-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.bdt-has-divider--yes .bdt-post-list .bdt-grid-4 li:nth-child(n+5) > div' => 'padding-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-list-grid' => 'grid-gap: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_control(
			'show_date',
			[
				'label'   => esc_html__('Date', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'human_diff_time',
			[
				'label'   => esc_html__('Human Different Time', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'human_diff_time_short',
			[
				'label'   => esc_html__('Time Short Format', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'human_diff_time' => 'yes',
					'show_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_category',
			[
				'label'   => esc_html__('Category', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_divider',
			[
				'label'   => esc_html__('Divider', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'bdt-has-divider--',
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => esc_html__('Pagination', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		//New Query Builder Settings
		$this->start_controls_section(
			'section_post_query_builder',
			[
				'label' => __('Query', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_query_builder_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => __('Image', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('image_effects');

		$this->start_controls_tab(
			'normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'list_layout_image_size',
			[
				'label' => esc_html__('Image Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 64,
						'max'  => 150,
						'step' => 10,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-thumbnail img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-thumbnail img'
			]
		);

		$this->add_responsive_control(
			'image_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-thumbnail img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'image_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-thumbnail img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'image_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-thumbnail img'
			]
		);

		$this->add_control(
			'image_radius',
			[
				'label'      => __('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'after',
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'image_shadow',
				'selector'  => '{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-thumbnail img'
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-thumbnail img',
			]
		);

		$this->add_control(
			'image_opacity',
			[
				'label' => __('Opacity', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-thumbnail img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => __('Transition Duration', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-thumbnail img' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'image_hover_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-thumbnail:hover img' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'image_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-thumbnail:hover img' => 'border-color: {{VALUE}};'
				],
				'condition' => [
					'image_border_border!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-thumbnail:hover img',
			]
		);

		$this->add_control(
			'image_opacity_hover',
			[
				'label' => __('Opacity', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-thumbnail:hover img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_list',
			[
				'label' => esc_html__('List Style', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'list_layout_title_category',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'list_layout_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-title .bdt-post-list-link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'list_layout_title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-title .bdt-post-list-link:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-title .bdt-post-list-link',
			]
		);

		$this->add_control(
			'date_heading',
			[
				'label'     => esc_html__('Date', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'show_date' => 'yes',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'date_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-meta span' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$this->add_control(
			'date_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-meta span:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'date_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-meta span',
				'condition' => [
					'show_date' => 'yes',
				],

			]
		);

		$this->add_control(
			'category_heading',
			[
				'label'     => esc_html__('Category', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'show_category' => 'yes',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'category_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-meta a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_category' => 'yes',
				],
			]
		);

		$this->add_control(
			'category_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-meta a:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_category' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'category_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .bdt-post-list .list-part .bdt-post-list-meta a',
				'condition' => [
					'show_category' => 'yes',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => esc_html__('Divider Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-list .bdt-has-divider li > div'             => 'border-top-color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-post-list .bdt-list-divider > li:nth-child(n+2)' => 'border-top-color: {{VALUE}};',
				],
				'condition' => [
					'show_divider' => 'yes',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		//Pagination
		$this->start_controls_section(
			'section_style_pagination',
			[
				'label'     => esc_html__('Pagination', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_pagination' => 'yes',
				],
			]
		);

		$this->register_pagination_controls();

		$this->end_controls_section();
	}

	public function get_taxonomies() {
		$taxonomies = get_taxonomies(['show_in_nav_menus' => true], 'objects');

		$options = ['' => ''];

		foreach ($taxonomies as $taxonomy) {
			$options[$taxonomy->name] = $taxonomy->label;
		}

		return $options;
	}

	public function get_posts_tags() {
		$taxonomy = $this->get_settings('taxonomy');

		foreach ($this->_query->posts as $post) {
			if (!$taxonomy) {
				$post->tags = [];

				continue;
			}

			$tags = wp_get_post_terms($post->ID, $taxonomy);

			$tags_slugs = [];

			foreach ($tags as $tag) {
				$tags_slugs[$tag->term_id] = $tag;
			}

			$post->tags = $tags_slugs;
		}
	}

	/**
	 * Get post query builder arguments
	 */
	public function query_posts($posts_per_page) {
		$settings = $this->get_settings_for_display();

		$args = [];
		if ($posts_per_page) {
			$args['posts_per_page'] = $posts_per_page;
			if ($settings['show_pagination']) { // fix query offset
				$args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
			}
		}

		$default = $this->getGroupControlQueryArgs();
		$args = array_merge($default, $args);

		$this->_query = new \WP_Query($args);
	}

	public function render_date() {
		$settings = $this->get_settings_for_display();

		if (!$settings['show_date']) {
			return;
		}

		echo '<span>';

		if ($settings['human_diff_time'] == 'yes') {
			echo element_pack_post_time_diff(($settings['human_diff_time_short'] == 'yes') ? 'short' : '');
		} else {
			echo get_the_date();
		}

		echo '</span>';
	}

	public function render_category() {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_category')) {
			return;
		}
?>
		<span>
			<?php
			echo element_pack_get_category_list($settings['posts_source'], ', ');
			?>
		</span>
		<?php
	}

	public function render() {

		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();


		if ($settings['show_horizontal']) {
			// $this->add_render_attribute('list-wrapper', 'class', ['bdt-list-grid'] );
			$this->add_render_attribute('list-wrapper', 'class', ['bdt-list-grid', 'bdt-grid-' . $settings['column']]);
		} else {
			$this->add_render_attribute('list-wrapper', 'class', ['bdt-list', 'bdt-list-large']);
		}

		$this->add_render_attribute('list-wrapper', 'class', ['bdt-post-list-item', 'list-part']);

		$this->add_render_attribute('bdt-post-list-title', 'class', 'bdt-post-list-title');

		if ($settings['show_divider']) {
			if ($settings['show_horizontal']) {
				$this->add_render_attribute('list-wrapper', 'class', 'bdt-has-divider');
			} else {
				$this->add_render_attribute('list-wrapper', 'class', 'bdt-list-divider');
			}
		}

		// TODO need to delete after v6.5
		if (isset($settings['posts_limit']) and $settings['posts_per_page'] == 6) {
			$limit = $settings['posts_limit'];
		} else {
			$limit = $settings['posts_per_page'];
		}

		$this->query_posts($limit);

		$wp_query = $this->get_query();

		if ($wp_query->have_posts()) :

		?>
			<div id="bdt-post-list-<?php echo esc_attr($id); ?>" class="bdt-post-list bdt-post-list-skin-base">
				<div data-bdt-scrollspy="cls: bdt-animation-fade; target: > ul > .bdt-post-list-item; delay: 350;">
					<ul <?php echo $this->get_render_attribute_string('list-wrapper'); ?>>
						<?php while ($wp_query->have_posts()) : $wp_query->the_post();

							$placeholder_image_src = Utils::get_placeholder_image_src();
							$image_src             = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'thumbnail');

						?>
							<li <?php echo $this->get_render_attribute_string('list'); ?>>
								<div class="bdt-post-list-item-inner">
									<div class="bdt-grid bdt-grid-small bdt-flex-middle" data-bdt-grid>

										<?php if ($settings['show_image']) : ?>
											<div class="bdt-post-list-thumbnail bdt-width-auto">
												<a href="<?php echo esc_url(get_permalink()); ?>" title="<?php echo esc_attr(get_the_title()); ?>">
													<?php
													if (!$image_src) {
														printf('<img src="%1$s" alt="%2$s">', $placeholder_image_src, esc_html(get_the_title()));
													} else {
														print(wp_get_attachment_image(
															get_post_thumbnail_id(),
															'thumbnail',
															false,
															[
																'alt' => esc_html(get_the_title())
															]
														));
													}
													?>
												</a>
											</div>
										<?php endif ?>

										<div class="bdt-post-list-desc bdt-width-expand">
											<?php if ($settings['show_title']) : ?>
												<<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> <?php echo $this->get_render_attribute_string('bdt-post-list-title'); ?>>
													<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-post-list-link" title="<?php echo esc_attr(get_the_title()); ?>"><?php echo esc_html(get_the_title()); ?></a>
												</<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
											<?php endif ?>

											<?php if ($settings['show_category'] or $settings['show_date']) : ?>

												<div class="bdt-post-list-meta bdt-subnav bdt-flex-middle">
													<?php $this->render_date(); ?>
													<?php if ($settings['show_category']) : ?>
														<?php $this->render_category() ?>
													<?php endif ?>

												</div>

											<?php endif ?>
										</div>
									</div>
								</div>
							</li>
						<?php endwhile; ?>
						<?php wp_reset_postdata(); ?>
					</ul>
				</div>
			</div>

			<?php
			if ($settings['show_pagination']) { ?>
				<div class="ep-pagination">
					<?php element_pack_post_pagination($wp_query); ?>
				</div>
<?php
			}
		endif;
	}
}

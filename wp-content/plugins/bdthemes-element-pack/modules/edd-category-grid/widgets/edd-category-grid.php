<?php

namespace ElementPack\Modules\EddCategoryGrid\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use ElementPack\Traits\Global_Terms_Query_Controls;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class EDD_Category_Grid extends Module_Base {
	use Global_Terms_Query_Controls;
	public function get_name() {
		return 'bdt-edd-category-grid';
	}

	public function get_title() {
		return BDTEP . esc_html__('EDD Category Grid', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-edd-category-grid bdt-new';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['easy', 'digital', 'category', 'downloads', 'eshop', 'estore', 'profile', 'editor'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-edd-category-grid'];
		}
	}
	public function get_custom_help_url() {
		return 'https://youtu.be/z6MSJtvbxPQ';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'skin_layout',
			[
				'label'      => __('Skin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'style-1',
				'options'    => [
					'style-1'  => __('Style 1', 'bdthemes-element-pack'),
					'style-2'  => __('Style 2', 'bdthemes-element-pack'),
					'style-3'  => __('Style 3', 'bdthemes-element-pack'),
					'style-4'  => __('Style 4', 'bdthemes-element-pack'),
					'style-5'  => __('Style 5', 'bdthemes-element-pack'),
				],
			]
		);
		$this->add_responsive_control(
			'columns',
			[
				'label'          => __('Columns', 'bdthemes-element-pack-pro'),
				'type'           => Controls_Manager::SELECT,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'options'        => [
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
					6 => '6',
				],
				'selectors' => [
					'{{WRAPPER}} .ep-edd-category-grid' => 'grid-template-columns:repeat({{VALUE}}, 1fr)'
				]
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => __('Item Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .ep-edd-category-grid' => 'grid-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_height',
			[
				'label'   => esc_html__('Item Height(px)', 'bdthemes-element-pack-pro'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ep-edd-category-grid .ep-edd-category-grid-image' => 'height: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'skin_layout!' => 'style-3'
				]
			]
		);
		$this->add_responsive_control(
			'item_height_skin_3',
			[
				'label'   => esc_html__('Item Height(px)', 'bdthemes-element-pack-pro'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ep-edd-category-grid .ep-edd-category-grid-image' => 'height: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'skin_layout' => 'style-3'
				]
			]
		);
		$this->add_control(
			'is_use_image',
			[
				'label'     => esc_html__('Use Static Image', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);
		$this->add_control(
			'category_image',
			[
				'label'     => __('Select Image', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url'   => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'is_use_image' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'    => 'category_thumbnail',
				'exclude' => ['custom'],
				'default' => 'medium',
				'condition' => [
					'is_use_image' => 'yes'
				]
			]
		);
		$this->add_control(
			'show_count',
			[
				'label'     => esc_html__('Show Count', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);
		$this->end_controls_section();
		$this->render_terms_query_controls('download_category');
		$this->start_controls_section(
			'section_style_item',
			[
				'label' => esc_html__('Item', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs(
			'item_tabs'
		);
		$this->start_controls_tab(
			'item_tab_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'items_background',
				'label'     => esc_html__('Backgrund', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .ep-edd-category-grid .ep-edd-category-grid-image',
			]
		);
		$this->add_control(
			'item_overlay',
			[
				'label'     => esc_html__('Overlay Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item-overlay' => 'background: {{VALUE}}',
				],
				'condition' => [
					'skin_layout!' => ['style-3']
				]
			]
		);
		$this->add_control(
			'item_overlay_blur_effect',
			[
				'label'       => esc_html__('Glassmorphism', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),
				'default'     => 'yes',
				'condition' => [
					'skin_layout' => [
						'style-5',
					]
				]
			]
		);

		$this->add_control(
			'item_overlay_blur_level',
			[
				'label'     => __('Blur Level', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default'   => [
					'size' => 10
				],
				'selectors' => [
					'{{WRAPPER}} .ep-edd-category-grid.style-5 .ep-edd-category-grid-image:before' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'item_overlay_blur_effect' => 'yes',
					'skin_layout' => [
						'style-5'
					]
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background',
				'selector' => '{{WRAPPER}} .ep-edd-category-grid.style-5 .ep-edd-category-grid-image:before',
				'condition' => [
					'skin_layout' => [
						'style-5'
					]
				]
			]
		);
		$this->add_responsive_control(
			'item_padding',
			[
				'label'                 => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'item_margin',
			[
				'label'                 => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item'    => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'item_border',
				'label'     => esc_html__('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .ep-edd-category-grid .edd-item',
			]
		);
		$this->add_responsive_control(
			'item_radius',
			[
				'label'                 => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'item_tab_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'items_hover_background',
				'label'     => esc_html__('Backgrund', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .ep-edd-category-grid .edd-item:hover .ep-edd-category-grid-image',
				'condition' => [
					'skin_layout!' => 'style-5'
				]
			]
		);
		$this->add_control(
			'item_overlay_hover',
			[
				'label'     => esc_html__('Overlay Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item:hover .edd-item-overlay' => 'background: {{VALUE}}',
				],
				'condition' => [
					'skin_layout!' => ['style-3']
				]
			]
		);
		$this->add_control(
			'item_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item:hover' => 'border-color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__('Content', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'skin_layout' => [
						'style-1',
						'style-3'
					]
				]
			]
		);
		$this->start_controls_tabs(
			'content_tabs'
		);
		$this->start_controls_tab(
			'content_tab_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'content_background',
				'selector' => '{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content',
				'condition' => [
					'skin_layout' => ['style-1', 'style-3']
				]
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'                 => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'content_margin',
			[
				'label'                 => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content'    => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'content_border',
				'label'     => esc_html__('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content',
			]
		);
		$this->add_responsive_control(
			'content_radius',
			[
				'label'                 => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_shadow',
				'selector' => '{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'content_tab_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);
		$this->add_control(
			'content_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item:hover .edd-content' => 'border-color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_hover_shadow',
				'selector' => '{{WRAPPER}} .ep-edd-category-grid .edd-item:hover .edd-content',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_category',
			[
				'label' => esc_html__('Category', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs(
			'category_tabs'
		);
		$this->start_controls_tab(
			'category_tab_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);
		$this->add_control(
			'category_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content .title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'style_5_category_bg',
				'label'     => esc_html__('Backgorund', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content .title',
				'condition' => [
					'skin_layout' => [
						'style-5'
					]
				]
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'style_6_category_bg',
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .ep-edd-category-grid.style-5 .ep-edd-category-grid-image:before',
				'condition' => [
					'skin_layout' => [
						'style-6'
					]
				]
			]
		);
		$this->add_responsive_control(
			'category_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'category_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'skin_layout' => [
						'style-4'
					]
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'exclude' => ['line_height'],
				'selector' => '{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content .title',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'category_tab_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
				'condition' => [
					'skin_layout!' => 'style-5'
				]
			]
		);
		$this->add_control(
			'hover_category_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item:hover .edd-content .title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_count',
			[
				'label' => esc_html__('Count', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs(
			'count_tabs'
		);
		$this->start_controls_tab(
			'count_tab_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
				'condition' => [
					'skin_layout!' => [
						'style-5'
					]
				]
			]
		);
		$this->add_control(
			'count_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content .edd-category-count > *' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'count_background',
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content .edd-category-count > *',
				'condition' => [
					'skin_layout' => [
						'style-2'
					]
				]
			]
		);
		$this->add_responsive_control(
			'count_number_size',
			[
				'label'         => esc_html__('Size', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px'],
				'default'       => [
					'unit'      => 'px',
					'size'      => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content .edd-category-count > *' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'skin_layout' => [
						'style-2'
					]
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'count_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .ep-edd-category-grid .edd-item .edd-content .edd-category-count > *',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'count_tab_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);
		$this->add_control(
			'count_color_hover',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ep-edd-category-grid .edd-item:hover .edd-content .edd-category-count > *' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'count_hover_background',
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .ep-edd-category-grid .edd-item:hover .edd-content .edd-category-count > *',
				'condition' => [
					'skin_layout' => [
						'style-2'
					]
				]
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}
	public function render_query() {
		$settings = $this->get_settings_for_display();
		$args = [
			'orderby'    => isset($settings['orderby']) ? $settings['orderby'] : 'name',
			'order'      => isset($settings['order']) ? $settings['order'] : 'ASC',
			'hide_empty' => isset($settings['hide_empty']) && ($settings['hide_empty'] == 'yes') ? 1 : 0,
		];


		switch ($settings['display_category']) {
			case 'all':
				if (isset($settings['cats_include_by_id']) && !empty($settings['cats_include_by_id'])) {
					$args['include'] = $settings['cats_include_by_id'];
				}
				if (isset($settings['cats_exclude_by_id']) && !empty($settings['cats_exclude_by_id'])) {
					$args['exclude'] = $settings['cats_exclude_by_id'];
				}
				break;
			case 'child':
				if ($settings['parent_cats'] != 'none' &&  !empty($settings['parent_cats'])) {
					$args['child_of'] = $settings['parent_cats'];
				}
				break;
			case 'parents':
				$args['parent'] = 0;
				break;
		}
		$categories = get_terms('download_category', $args);
		return $categories;
	}
	public function render_image() {
		$settings = $this->get_settings_for_display();
		$image_src = Utils::get_placeholder_image_src();
?>
		<div class="ep-edd-category-grid-image">
			<?php if ($settings['is_use_image']) :
				$thumb_url = Group_Control_Image_Size::get_attachment_image_src($settings['category_image']['id'], 'category_thumbnail', $settings);
				if (!empty($thumb_url)) {
					$image_src = $settings['category_image']['url'];
				}
			?>
				<img src="<?php echo esc_url($image_src); ?>" alt="">
			<?php endif; ?>
		</div><?php
			}
			public function render_loop_item() {
				$settings = $this->get_settings_for_display();
				$categories = $this->render_query();
				?>
		<?php
				if (!empty($categories)) {
					foreach ($categories as $index => $category) :
						$this->add_render_attribute('edd-category-item', 'class', ['edd-item', 'category-link'], true);
						$this->add_render_attribute('edd-category-item', 'href',  get_term_link($category->term_id, 'download_category'), true); ?>
				<a <?php $this->print_render_attribute_string('edd-category-item'); ?>>
					<?php $this->render_image(); ?>
					<div class="edd-content">
						<?php printf('<h3 class="title">%s</h3>', $category->name); ?>
						<?php if ($settings['show_count']) :
							printf('<p class="edd-category-count"><span class="edd-count-number">%s</span><span class="edd-count-text">products</span></p>', $category->count);
						endif;
						?>
					</div>
					<div class="edd-item-overlay"></div>
				</a>
		<?php
					endforeach;
				} else {
					printf('<span class="bdt-warning">%s</span>', __('Opps, Nothing found to display', 'bdthemes-element-pack'));
				}
			}

			public function render() {
				$settings = $this->get_settings_for_display();
				$this->add_render_attribute('ep-edd-category-grid', 'class', ['ep-edd-category-grid', $settings['skin_layout']]); ?>
		<div <?php $this->print_render_attribute_string('ep-edd-category-grid'); ?>>
			<?php $this->render_loop_item(); ?>
		</div>
<?php
			}
		}

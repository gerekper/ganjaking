<?php

namespace ElementPack\Modules\BrandGrid\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use ElementPack\Utils;


if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Brand_Grid extends Module_Base {

	public function get_name() {
		return 'bdt-brand-grid';
	}

	public function get_title() {
		return BDTEP . esc_html__('Brand Grid', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-brand-grid';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['brand', 'grid', 'client', 'logo', 'showcase'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-brand-grid'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/a_wJL950Kz4';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'ep_section_brand',
			[
				'label' => esc_html__('Brand Items', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__('Brand Image', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'brand_name',
			[
				'label'       => esc_html__('Brand Name', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Brand Name', 'bdthemes-element-pack'),
				'label_block' => true,
				'dynamic'     => ['active'      => true],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'         => esc_html__('Website Url', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'placeholder'   => esc_html__('https://your-link.com', 'plugin-domain'),
				'show_external' => true,
				'default'      => [
					'url'         => '#',
					'is_external' => true,
					'nofollow'    => true,
				],
				'label_block'   => true,
				'dynamic'       => ['active'      => true],
			]
		);

		$repeater->add_control(
			'website_link_text',
			[
				'label'       => esc_html__('Website Url Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('www.example.com', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Paste URL Text or Type', 'bdthemes-element-pack'),
				'label_block' => true,
				'dynamic'     => ['active'      => true],
			]
		);

		$this->add_control(
			'brand_items',
			[
				'show_label'  => false,
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => [
					['image' => ['url' => Utils::get_placeholder_image_src()]],
					['image' => ['url' => Utils::get_placeholder_image_src()]],
					['image' => ['url' => Utils::get_placeholder_image_src()]],
					['image' => ['url' => Utils::get_placeholder_image_src()]],
					['image' => ['url' => Utils::get_placeholder_image_src()]],
					['image' => ['url' => Utils::get_placeholder_image_src()]],
				]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'default'   => 'medium',
				'separator' => 'before',
				'exclude'   => ['custom']
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_settings',
			[
				'label' => esc_html__('Additional Settings', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'           => esc_html__('Columns', 'bdthemes-element-pack'),
				'type'            => Controls_Manager::SELECT,
				'desktop_default' => 3,
				'tablet_default'  => 2,
				'mobile_default'  => 1,
				'options'         => [
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
					6 => '6',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-brand-grid' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'     => esc_html__('Column Gap', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-brand-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'     => esc_html__('Row Gap', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-brand-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'show_brand_name',
			[
				'label'   => esc_html__('Show Brand Name', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'brand_html_tag',
			[
				'label'   => esc_html__('Name HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => element_pack_title_tags(),
				'condition' => [
					'show_brand_name' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_website_link',
			[
				'label'   => esc_html__('Show Link Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'brand_event',
			[
				'label'   => esc_html__('Select Event ', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover-icon',
				'options' => [
					'click'     => esc_html__('Click', 'bdthemes-element-pack'),
					'hover-icon' => esc_html__('Icon Hover', 'bdthemes-element-pack'),
					'hover-item' => esc_html__('Item Hover', 'bdthemes-element-pack'),
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label'   => esc_html__('Icon Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom-left',
				'options' => [
					'top-left'      => esc_html__('Top Left', 'bdthemes-element-pack'),
					'top-right'     => esc_html__('Top Right', 'bdthemes-element-pack'),
					'bottom-left'   => esc_html__('Bottom Left', 'bdthemes-element-pack'),
					'bottom-right'  => esc_html__('Bottom Right', 'bdthemes-element-pack'),
					'center-center' => esc_html__('Center Center', 'bdthemes-element-pack'),
				],
				'prefix_class' => 'bdt-ep-icon--',
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_items',
			[
				'label' => esc_html__('Items', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'item_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-brand-grid-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'item_border',
				'selector'  => '{{WRAPPER}} .bdt-ep-brand-grid-item',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-brand-grid-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-brand-grid-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-brand-grid-item',
			]
		);


		$this->add_control(
			'image_heading',
			[
				'label'     => esc_html__('Image', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'brand_image_size',
			[
				'label' => esc_html__('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-brand-grid-image img' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; object-fit: cover;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .bdt-ep-brand-grid-image img',
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
			'item_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-brand-grid-item:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-brand-grid-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__('Icon', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-brand-grid-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-brand-grid-checkbox, {{WRAPPER}} .bdt-ep-brand-grid-content',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'icon_border',
				'selector' => '{{WRAPPER}} .bdt-ep-brand-grid-checkbox, {{WRAPPER}} .bdt-ep-brand-grid-content'
			]
		);

		$this->add_responsive_control(
			'iamge_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-brand-grid-checkbox, {{WRAPPER}} .bdt-ep-brand-grid-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'iamge_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-brand-grid-checkbox, {{WRAPPER}} .bdt-ep-brand-grid-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-brand-grid-checkbox, {{WRAPPER}} .bdt-ep-brand-grid-content' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_font_size',
			[
				'label' => esc_html__('Font Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-brand-grid-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-brand-grid-checkbox, {{WRAPPER}} .bdt-ep-brand-grid-content'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_name',
			[
				'label' => esc_html__('Name', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_brand_name' => 'yes',
				]
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-brand-grid-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-brand-grid-name',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'name_shadow',
				'label' => esc_html__('Text Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-brand-grid-name',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_website_link',
			[
				'label' => esc_html__('Text', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_website_link' => 'yes',
				]
			]
		);

		$this->add_control(
			'website_link_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-brand-grid-link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'website_link_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-brand-grid-link:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'website_link_top_space',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-brand-grid-text' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'website_link_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-brand-grid-link',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if (empty($settings['brand_items'])) {
			return;
		}

		$this->add_render_attribute('brand-grid', 'class', 'bdt-ep-brand-grid');

		?>
		<div <?php $this->print_render_attribute_string('brand-grid'); ?>>
			<?php foreach ($settings['brand_items'] as $index => $item) :

				if ($settings['brand_event'] == 'hover-item') {
					$this->add_render_attribute('item-wrap', 'class', 'bdt-ep-brand-grid-item bdt-ep-brand-grid-item-hover', true);
				} else {
					$this->add_render_attribute('item-wrap', 'class', 'bdt-ep-brand-grid-item', true);
				}

				$this->add_render_attribute('name-wrap', 'class', 'bdt-ep-brand-grid-name', true);

				$link_key = 'link_' . $index;
				$this->add_render_attribute($link_key, 'class', 'bdt-ep-brand-grid-link', true);
				$this->add_link_attributes($link_key, $item['link']);

			?>
				<div <?php echo $this->get_render_attribute_string('item-wrap'); ?>>
					<div class="bdt-ep-brand-grid-image">
						<?php
						$thumb_url = Group_Control_Image_Size::get_attachment_image_src($item['image']['id'], 'thumbnail', $settings);
						if (!$thumb_url) {
							printf('<img src="%1$s" alt="%2$s">', $item['image']['url'], esc_html($item['brand_name']));
						} else {
							print(wp_get_attachment_image(
								$item['image']['id'],
								$settings['thumbnail_size'],
								false,
								[
									'alt' => esc_html($item['brand_name'])
								]
							));
						}
						?>
					</div>
					<?php if ($settings['brand_event'] == 'click') : ?>
						<input class="bdt-ep-brand-grid-checkbox" type="checkbox">
					<?php endif; ?>
					<div class="bdt-ep-brand-grid-content">
						<div class="bdt-ep-brand-grid-icon">
							<i class="ep-icon-plus-2" aria-hidden="true"></i>
						</div>
						<div class="bdt-ep-brand-grid-inner">
							<?php if ($item['brand_name'] && $settings['show_brand_name']) : ?>
								<<?php echo Utils::get_valid_html_tag($settings['brand_html_tag']); ?> <?php echo $this->get_render_attribute_string('name-wrap'); ?>>
									<?php echo wp_kses($item['brand_name'], element_pack_allow_tags('brand_name')); ?>
								</<?php echo Utils::get_valid_html_tag($settings['brand_html_tag']); ?>>
							<?php endif; ?>

							<?php if (!empty($item['link']['url']) && $settings['show_website_link']) : ?>
								<div class="bdt-ep-brand-grid-text">
									<a <?php echo $this->get_render_attribute_string($link_key); ?>>
										<?php echo esc_html($item['website_link_text']); ?>
									</a>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>

			<?php endforeach; ?>
		</div>
<?php
	}
}

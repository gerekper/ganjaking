<?php

namespace ElementPack\Modules\SlinkyVerticalMenu\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Repeater;

use ElementPack\Modules\SlinkyVerticalMenu\ep_slinky_vertical_menu_walker;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Slinky_Vertical_Menu extends Module_Base {

	public function get_name() {
		return 'bdt-slinky-vertical-menu';
	}

	public function get_title() {
		return BDTEP . esc_html__('Slinky Vertical Menu', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-slinky-vertical-menu';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['navbar', 'menu', 'vertical'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-slinky-vertical-menu'];
		}
	}

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['slinky', 'ep-scripts'];
        } else {
			return ['slinky', 'ep-slinky-vertical-menu'];
        }
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/5RE9w-JqKwk';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_static_menu',
			[
				'label'     => __('Layout', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'dynamic_menu',
			[
				'label'   => esc_html__('Dynamic Menu', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'navbar',
			[
				'label'   => esc_html__('Select Menu', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_get_menu(),
				'default' => 0,
				'condition' => ['dynamic_menu' => 'yes'],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'menu_title',
			[
				'label'       => __('Menu Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'label_block' => true,
				'condition' => [
					'menu_type!' => 'child_end'
				]
			]
		);

		$repeater->add_control(
			'menu_type',
			[
				'label'       => __('Select Item Type', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT,
				'dynamic'     => ['active' => true],
				'label_block' => true,
				'options' 	  => [
					'item'      => 'Item',
					'child_start' => 'Child Start',
					'child_end'   => 'Child End',
				],
				'default' => 'item',
			]
		);

		$repeater->add_control(
			'menu_link',
			[
				'label'       => __('Link', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => ['active' => true],
				'default' => [
					'url' => '#',
				],
				'label_block' => true,
				'condition' => [
					'menu_type!' => 'child_end'
				]
			]
		);

		$repeater->add_control(
			'menu_icon',
			[
				'label' => __('Icon', 'bdthemes-element-pack'),
				'type' => Controls_Manager::ICONS,
				'label_block' => true,
				'condition' => [
					'menu_type!' => 'child_end'
				]
			]
		);

		$this->add_control(
			'menus',
			[
				'label'   => __('Menu Items', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'condition' => ['dynamic_menu' => ''],
				'separator' => 'before',
				'default' => [
					[
						'menu_title'   => __('About', 'bdthemes-element-pack'),
						'menu_link'    => '#',
					],
					[
						'menu_title'   => __('Gallery', 'bdthemes-element-pack'),
						'menu_link'    => '#',
						'menu_type' => 'child_start'
					],
					[
						'menu_title'   => __('Gallery 01', 'bdthemes-element-pack'),
						'menu_link'    => '#',
					],
					[
						'menu_title'   => __('Gallery 02', 'bdthemes-element-pack'),
						'menu_link'    => '#',
						'menu_type' => 'child_start'
					],
					[
						'menu_title'   => __('Sub Gallery 01', 'bdthemes-element-pack'),
						'menu_link'    => '#',
					],
					[
						'menu_title'   => __('Sub Gallery 02', 'bdthemes-element-pack'),
						'menu_link'    => '#',
					],
					[
						'menu_title'   => __('Sub Gallery 03', 'bdthemes-element-pack'),
						'menu_link'    => '#',
					],
					[
						'menu_type' => 'child_end'
					],
					[
						'menu_title'   => __('Gallery 03', 'bdthemes-element-pack'),
						'menu_link'    => '#',
					],
					[
						'menu_type' => 'child_end'
					],
					[
						'menu_title'   => __('Contacts', 'bdthemes-element-pack'),
						'menu_link'    => '#',
					],
				],
				'title_field' => '{{{ elementor.helpers.renderIcon( this, menu_icon, {}, "i", "panel" ) || \'<i class="{{ icon }}" aria-hidden="true"></i>\' }}} <# print( (menu_type == "child_start" ) ? "<b>[ Child Start:</b> " + menu_title : menu_title ) #><# print( (menu_type == "child_end" ) ? "<b>Child End ]</b>" : "" ) #>',
			]
		);
		$this->end_controls_section();


		//Style
		$this->start_controls_section(
			'slinky_vertical_menu_additional',
			[
				'label'     => __('Additional Settings', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_sticky',
			[
				'label'   => esc_html__('Show Sticky', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_responsive_control(
			'menu_width',
			[
				'label'   => esc_html__('Menu Max Width', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slinky-vertical-menu' => 'max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_responsive_control(
			'menu_text_alignment',
			[
				'label'   => __('Text Alignemnt', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slinky-vertical-menu' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'slinky_vertical_menu_item',
			[
				'label'     => __('Menu Items', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('menu_link_styles');

		$this->start_controls_tab(
			'menu_link_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'menu_link_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slinky-vertical-menu li.bdt-menu-item > a span ' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-slinky-vertical-menu li.bdt-menu-item > a svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'menu_link_background',
				'label' => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .bdt-slinky-vertical-menu li > a',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'default' => '#e3e8eb',
					],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'menu_border',
				'selector' => '{{WRAPPER}} .bdt-slinky-vertical-menu li > a',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'menu_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slinky-vertical-menu  li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'main_menu_bg_link_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slinky-vertical-menu li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'render_type' => 'template'
			]
		);
		
		$this->add_responsive_control(
			'menu_spacing',
			[
				'label' => esc_html__('Space Between', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'tablet_default' => [
					'size' => 1,
				],
				'mobile_default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slinky-vertical-menu li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'template'
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'menu_typography',
				'selector' => '{{WRAPPER}} .bdt-slinky-vertical-menu li > a',
				'render_type' => 'template'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'menu_link_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack')
			]
		);

		$this->add_control(
			'menu_link_color_hover',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slinky-vertical-menu li.bdt-menu-item:hover > a span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-slinky-vertical-menu li.bdt-menu-item:hover > a svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'link_background_hover',
				'label' => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .bdt-slinky-vertical-menu li:hover > a',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'default' => '#d7dee3',
					],
				],
			]
		);

		$this->add_control(
			'menu_border_color_hover',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slinky-vertical-menu li:hover > a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'menu_border_border!' => '',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
			
		$this->end_controls_section();

		$this->start_controls_section(
			'slinky_vertical_menu_indicator',
			[
				'label'     => __('Indicator', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'slinky_indicator_tabs'
		);
		$this->start_controls_tab(
			'slinky_indicator_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'indicator_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slinky-vertical-menu .header a.back:before, {{WRAPPER}} .bdt-slinky-vertical-menu .next::after' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'indicator_background',
				'label' => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .bdt-slinky-vertical-menu .header a.back:before, {{WRAPPER}} .bdt-slinky-vertical-menu .next::after',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'indicator_border',
				'label'     => __('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-slinky-vertical-menu .header a.back:before, {{WRAPPER}} .bdt-slinky-vertical-menu .next::after',
				'separator' => 'before'
			]
		);
		$this->add_responsive_control(
			'indicator_radius',
			[
				'label'                 => __('Border Radius', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .bdt-slinky-vertical-menu .header a.back:before, {{WRAPPER}} .bdt-slinky-vertical-menu .next::after'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'indicator_padding',
			[
				'label'                 => __('Padding', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .bdt-slinky-vertical-menu .header a.back:before, {{WRAPPER}} .bdt-slinky-vertical-menu .next::after'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'render_type' => 'template'
			]
		);
		$this->add_responsive_control(
			'indicator_margin',
			[
				'label'                 => __('Margin', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .bdt-slinky-vertical-menu .header a.back:before, {{WRAPPER}} .bdt-slinky-vertical-menu .next::after'    => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'render_type' => 'template'
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'indicator_typography',
				'selector' => '{{WRAPPER}} .bdt-slinky-vertical-menu .header a.back:before, {{WRAPPER}} .bdt-slinky-vertical-menu .next::after',
				'render_type' => 'template'
			]
		);

		$this->end_controls_tab();
		
		$this->start_controls_tab(
			'slinky_indicator_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'indicator_hover_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slinky-vertical-menu .header:hover a.back:before, {{WRAPPER}} .bdt-slinky-vertical-menu .next:hover:after' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'indicator_hover_background',
				'label' => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .bdt-slinky-vertical-menu .header:hover a.back:before, {{WRAPPER}} .bdt-slinky-vertical-menu .next:hover:after',
			]
		);

		$this->add_control(
			'indicator_border_color_hover',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slinky-vertical-menu .header:hover a.back:before, {{WRAPPER}} .bdt-slinky-vertical-menu .next:hover:after' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'indicator_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('slinky_vertical_menu', 'class', ['bdt-slinky-vertical-menu', 'slinky-menu', 'slinky-theme-default']);
		$this->add_render_attribute('slinky_vertical_menu', 'id', 'bdt-slinky-vertical-menu-' . $this->get_id());

		if ('yes' == $settings['show_sticky']) {
			$this->add_render_attribute('slinky_vertical_menu', 'data-bdt-sticky', "bottom: #offset;");
		}
?>
		<div <?php $this->print_render_attribute_string('slinky_vertical_menu'); ?>>

			<?php if ('yes' == $settings['dynamic_menu']) : ?>
				<?php $this->dynamic_menu(); ?>
			<?php else : ?>
				<?php $this->static_menu(); ?>
			<?php endif; ?>

		</div>
	<?php
	}

	protected function static_menu() {
		$settings = $this->get_settings_for_display();
	?>
		<ul>
			<?php foreach ($settings['menus'] as $item) : ?>

				<?php
				$target = (!empty($item['menu_link']['is_external'])) ? 'target="_blank"' : '';
				$nofollow = (!empty($item['menu_link']['nofollow'])) ? ' rel="nofollow"' : '';

				if ($item['menu_type'] == 'child_start') {
					$item_class = 'has-arrow';
				} else {
					$item_class = '';
				}

				?>

				<?php if ($item['menu_type'] !== 'child_end') : ?>
					<li class="bdt-menu-item">
						<a class="<?php echo $item_class; ?>" href="<?php echo esc_url($item['menu_link']['url']); ?>" <?php echo wp_kses_post($target);
																														echo wp_kses_post($nofollow); ?>>
							<?php if (!empty($item['menu_icon']['value'])) : ?>
								<span class="bdt-menu-icon">
									<?php Icons_Manager::render_icon($item['menu_icon'], ['aria-hidden' => 'true']); ?>
								</span>
							<?php endif; ?>
							<?php echo wp_kses($item['menu_title'], element_pack_allow_tags('title')); ?>
						</a>
					<?php endif; ?>

					<?php if ($item['menu_type'] == 'child_start') : ?>
						<ul>
						<?php endif; ?>

						<?php if ($item['menu_type'] == 'child_end') : ?>
						</ul>
					</li>
				<?php endif; ?>

				<?php if ($item['menu_type'] == 'item') : ?>
					</li>
				<?php endif; ?>

			<?php endforeach; ?>
		</ul>
	<?php
	}

	protected function dynamic_menu() {
		$settings = $this->get_settings_for_display();
		$id       = 'bdt-slinky-vertical-menu-' . $this->get_id();
		if (!$settings['navbar']) {
			element_pack_alert(__('Please select a Menu From Setting!', 'bdthemes-element-pack'));
		}
		$nav_menu = !empty($settings['navbar']) ? wp_get_nav_menu_object($settings['navbar']) : false;
		if (!$nav_menu) {
			return;
		}
		$nav_menu_args = array(
			'fallback_cb'    => false,
			'container'      => false,
			'menu_id'        => $id,
			'menu_class'     => 'slinky-vertical-menu',
			'theme_location' => 'default_navmenu', // creating a fake location for better functional control
			'menu'           => $nav_menu,
			'echo'           => true,
			'depth'          => 0,
			'walker'         => new ep_slinky_vertical_menu_walker
		);

	?>
		<?php wp_nav_menu(apply_filters('widget_nav_menu_args', $nav_menu_args, $nav_menu, $settings)); ?>

<?php
	}
}

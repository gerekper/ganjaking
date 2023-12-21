<?php

/**
 * Image Swap
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Utils;

defined('ABSPATH') || die();

class Image_Swap extends Base {

	public function get_title() {
		return __('Image Swap', 'happy-addons-pro');
	}

	public function get_icon() {
		return 'hm hm-image-scroll';
	}

	public function get_keywords() {
		return ['image', 'image-swap', 'swap'];
	}

	protected function register_content_controls() {

		$this->content_controls();

	}

	protected function register_style_controls() {

		$this->style_controls();
	}

	protected function content_controls() {

		$this->start_controls_section(
			'_section_content',
			[
				'label' => __('Content', 'happy-addons-pro'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'select_effect_type',
			[
				'label'   => __('Select Effect type', 'happy-addons-pro'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'happy-addons-pro'),
					'slide'   => __('Slide', 'happy-addons-pro'),
				],
			]
		);

		$this->add_control(
			'first_image',
			[
				'type'    => Controls_Manager::MEDIA,
				'label'   => __('First Image', 'happy-addons-pro'),
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'second_image',
			[
				'type'    => Controls_Manager::MEDIA,
				'label'   => __('Second Image', 'happy-addons-pro'),
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'swip_trigger',
			[
				'type'    => Controls_Manager::CHOOSE,
				'label'   => __('Trigger', 'happy-addons-pro'),
				'options' => [
					'hover' => [
						'title' => __('Hover', 'happy-addons-pro'),
						'icon'  => 'hm hm-cursor-hover-click',
					],
					'click' => [
						'title' => __('Click', 'happy-addons-pro'),
						'icon'  => 'eicon-click',
					],
				],
				'default' => 'hover',
				'condition' => [
					'select_effect_type' => 'default',
				],
			]
		);

		$this->add_control(
			'ig_effects',
			[
				'type'      => Controls_Manager::SELECT,
				'label'     => __('Effect', 'happy-addons-pro'),
				'options'   => [
					'fade'        => __('Fade', 'happy-addons-pro'),
					'move_left'   => __('Move Left', 'happy-addons-pro'),
					'move_top'    => __('Move Top', 'happy-addons-pro'),
					'move_right'  => __('Move Right', 'happy-addons-pro'),
					'move_bottom' => __('Move Bottom', 'happy-addons-pro'),
					'zoom_in'     => __('Zoom In', 'happy-addons-pro'),
					'zoom_out'    => __('Zoom Out', 'happy-addons-pro'),
					'card_left'   => __('Card Left', 'happy-addons-pro'),
					'card_top'    => __('Card Top', 'happy-addons-pro'),
					'card_right'  => __('Card Right', 'happy-addons-pro'),
					'card_bottom' => __('Card Bottom', 'happy-addons-pro'),
				],
				'default'   => 'fade',
				'condition' => [
					'select_effect_type' => 'default',
				],
			]
		);

		$this->add_control(
			'ig_effects_slides',
			[
				'type'      => Controls_Manager::SELECT,
				'label'     => __('Effect', 'happy-addons-pro'),
				'options'   => [
					'top'  => __('Slide Top', 'happy-addons-pro'),
					'bottom'  => __('Slide Bottom', 'happy-addons-pro'),
					'right' => __('Slide Right', 'happy-addons-pro'),
					'left'  => __('Slide Left', 'happy-addons-pro'),
				],
				'default'   => 'right',
				'condition' => [
					'select_effect_type' => 'slide',
				],
			]
		);

		$this->add_control(
			'speed',
			[
				'type'        => Controls_Manager::NUMBER,
				'label'       => __('Transition Speed', 'happy-addons-pro'),
				'description' => __('Note: Here animation speed is in seconds. Default is 0.5s', 'happy-addons-pro'),
				'min'         => 0,
				'max'         => 10,
				'step'        => 0.1,
				'default'     => 0.5,
				'after'       => 's',
				'selectors'   => [
					'{{WRAPPER}} .ha-image-swap-wrapper__inside img' => '-webkit-transition: {{VALUE}}s;',
					'{{WRAPPER}} .ha-image-swap-wrapper__inside img' => 'transition: {{VALUE}}s;',
					'{{WRAPPER}} .ha-image-swap-wrapper'             => '--animation_speed: {{VALUE}}s;',
					'{{WRAPPER}}'                                    => '--animation_speed: {{VALUE}}s;',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function style_controls() {

		$this->start_controls_section(
			'_ig_style_section',
			[
				'label' => __('Image', 'happy-addons-pro'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// $this->add_responsive_control(
		// 	'container_height',
		// 	[
		// 		'label'          => __('Container Height', 'happy-addons-pro'),
		// 		'type'           => Controls_Manager::SLIDER,
		// 		'default'        => [
		// 			'unit' => 'px',
		// 			'size' => 400
		// 		],
		// 		'tablet_default' => [
		// 			'unit' => 'px',
		// 		],
		// 		'mobile_default' => [
		// 			'unit' => 'px',
		// 		],
		// 		'size_units'     => ['px', 'vh', '%'],
		// 		'range'          => [
		// 			'px' => [
		// 				'min' => 1,
		// 				'max' => 1000,
		// 			],
		// 			'%' => [
		// 				'min' => 1,
		// 				'max' => 100,
		// 			],
		// 			'vh' => [
		// 				'min' => 1,
		// 				'max' => 100,
		// 			],
		// 		],
		// 		'selectors'      => [
		// 			// '{{WRAPPER}} .ha-image-swap-wrapper img' => 'height: {{SIZE}}{{UNIT}};',
		// 			'{{WRAPPER}} .ha_img_main_wrapper_top'     => 'height: {{SIZE}}{{UNIT}};',
		// 		],
		// 		'separator' => 'after'
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'width',
		// 	[
		// 		'label'          => __('Width', 'happy-addons-pro'),
		// 		'type'           => Controls_Manager::SLIDER,
		// 		'default'        => [
		// 			'unit' => '%',
		// 		],
		// 		'tablet_default' => [
		// 			'unit' => '%',
		// 		],
		// 		'mobile_default' => [
		// 			'unit' => '%',
		// 		],
		// 		'size_units'     => ['%', 'px', 'vw'],
		// 		'range'          => [
		// 			'%'  => [
		// 				'min' => 1,
		// 				'max' => 100,
		// 			],
		// 			'px' => [
		// 				'min' => 1,
		// 				'max' => 1000,
		// 			],
		// 			'vw' => [
		// 				'min' => 1,
		// 				'max' => 100,
		// 			],
		// 		],
		// 		'selectors'      => [
		// 			// '{{WRAPPER}} .ha-image-swap-wrapper img' => 'width: {{SIZE}}{{UNIT}};',
		// 			'{{WRAPPER}} .ha-image-swap-wrapper'  => 'width: {{SIZE}}{{UNIT}};',
		// 			'{{WRAPPER}} .ha-image-swap-ctn'      => 'width: {{SIZE}}{{UNIT}};',
		// 			// '{{WRAPPER}} .ha-image-swap-item' => 'width: {{SIZE}}{{UNIT}};',
		// 		],
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'space',
		// 	[
		// 		'label'          => __('Max Width', 'happy-addons-pro'),
		// 		'type'           => Controls_Manager::SLIDER,
		// 		'default'        => [
		// 			'unit' => '%',
		// 		],
		// 		'tablet_default' => [
		// 			'unit' => '%',
		// 		],
		// 		'mobile_default' => [
		// 			'unit' => '%',
		// 		],
		// 		'size_units'     => ['%', 'px', 'vw'],
		// 		'range'          => [
		// 			'%'  => [
		// 				'min' => 1,
		// 				'max' => 100,
		// 			],
		// 			'px' => [
		// 				'min' => 1,
		// 				'max' => 1000,
		// 			],
		// 			'vw' => [
		// 				'min' => 1,
		// 				'max' => 100,
		// 			],
		// 		],
		// 		'selectors'      => [
		// 			'{{WRAPPER}} .ha-image-swap-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
		// 			'{{WRAPPER}} .ha-image-swap-ctn'     => 'max-width: {{SIZE}}{{UNIT}};',
		// 			// '{{WRAPPER}} .ha-image-swap-wrapper img' => 'max-width: {{SIZE}}{{UNIT}};',
		// 		],
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'height',
		// 	[
		// 		'label'          => __('Height', 'happy-addons-pro'),
		// 		'type'           => Controls_Manager::SLIDER,
		// 		'default'        => [
		// 			'unit' => '%',
		// 			'size' => 100
		// 		],
		// 		'tablet_default' => [
		// 			'unit' => 'px',
		// 		],
		// 		'mobile_default' => [
		// 			'unit' => 'px',
		// 		],
		// 		'size_units'     => ['%', 'px', 'vh'],
		// 		'range'          => [
		// 			'%'  => [
		// 				'min' => 1,
		// 				'max' => 100,
		// 			],
		// 			'px' => [
		// 				'min' => 1,
		// 				'max' => 1000,
		// 			],
		// 			'vh' => [
		// 				'min' => 1,
		// 				'max' => 100,
		// 			],
		// 		],
		// 		'selectors'      => [
		// 			'{{WRAPPER}} .ha-image-swap-wrapper img' => 'height: {{SIZE}}{{UNIT}};',
		// 			'{{WRAPPER}} .ha-image-swap-ctn img'     => 'height: {{SIZE}}{{UNIT}};',
		// 		],
		// 	]
		// );

		// // $this->add_responsive_control(
		// // 	'image_align',
		// // 	[
		// // 		'label'          => __( 'Position', 'happy-addons-pro' ),
		// // 		'type'           => Controls_Manager::SLIDER,
		// // 		'size_units'     => [ '%' ],
		// // 		'range'          => [
		// // 			'%' => [
		// // 				'min' => 0,
		// // 				'max' => 100,
		// // 			]
		// // 		],
		// // 		'selectors'      => [
		// // 			'{{WRAPPER}} .ha-image-swap-wrapper__inside' => 'left: {{SIZE}}{{UNIT}};',
		// // 		],
		// // 	]
		// // );

		// $this->add_responsive_control(
		// 	'image_align',
		// 	[
		// 		'label'   => __('Alignment', 'happy-addons-pro'),
		// 		'type'    => Controls_Manager::CHOOSE,
		// 		'options' => [
		// 			'align_left'   => [
		// 				'title' => __('Left', 'happy-addons-pro'),
		// 				'icon'  => 'eicon-text-align-left',
		// 			],
		// 			'align_center' => [
		// 				'title' => __('Center', 'happy-addons-pro'),
		// 				'icon'  => 'eicon-text-align-center',
		// 			],
		// 			'align_right'  => [
		// 				'title' => __('Right', 'happy-addons-pro'),
		// 				'icon'  => 'eicon-text-align-right',
		// 			],
		// 		],
		// 		'default' => 'align_center',
		// 		'toggle'  => false,
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'object-fit',
		// 	[
		// 		'label'     => __('Object Fit', 'happy-addons-pro'),
		// 		'type'      => Controls_Manager::SELECT,
		// 		'condition' => [
		// 			'height[size]!' => '',
		// 		],
		// 		'options'   => [
		// 			''        => __('Default', 'happy-addons-pro'),
		// 			'fill'    => __('Fill', 'happy-addons-pro'),
		// 			'cover'   => __('Cover', 'happy-addons-pro'),
		// 			'contain' => __('Contain', 'happy-addons-pro'),
		// 		],
		// 		'default'   => '',
		// 		'selectors' => [
		// 			'{{WRAPPER}} .ha-image-swap-wrapper img' => 'object-fit: {{VALUE}};',
		// 			'{{WRAPPER}} .ha-image-swap-ctn img'     => 'object-fit: {{VALUE}};',
		// 		],
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'selector' => '{{WRAPPER}} .ha-image-swap-wrapper img, {{WRAPPER}} .ha-image-swap-ctn img',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => __('Border Radius', 'happy-addons-pro'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-image-swap-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-image-swap-ctn img'     => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// $this->add_control(
		// 	'image_position_toggle',
		// 	[
		// 		'label'        => __('Slide Position', 'happy-addons-pro'),
		// 		'type'         => Controls_Manager::POPOVER_TOGGLE,
		// 		'label_off'    => __('None', 'happy-addons-pro'),
		// 		'label_on'     => __('Custom', 'happy-addons-pro'),
		// 		'return_value' => 'yes',
		// 		'condition'    => [
		// 			'select_effect_type' => 'slide',
		// 		],
		// 		'separator'    => 'before',
		// 	]
		// );

		// $this->start_popover();

		// $this->add_responsive_control(
		// 	'image_horizontal_position',
		// 	[
		// 		'label'      => __('Horizontal Position', 'happy-addons-pro'),
		// 		'type'       => Controls_Manager::SLIDER,
		// 		'size_units' => ['px', '%'],
		// 		'default'    => [
		// 			'unit' => '%',
		// 		],
		// 		'range'      => [
		// 			'%'  => [
		// 				'min' => -100,
		// 				'max' => 100,
		// 			],
		// 			'px' => [
		// 				'min' => -1000,
		// 				'max' => 1000,
		// 			],
		// 		],
		// 		'default'    => [
		// 			'px' => [
		// 				'min' => 0,
		// 				'max' => 0,
		// 			],
		// 		],
		// 		'condition'  => [
		// 			'image_position_toggle' => 'yes',
		// 		],
		// 		'selectors'  => [
		// 			'{{WRAPPER}} .ha-image-swap-item.active' => 'top: {{SIZE}}{{UNIT}};',
		// 			'{{WRAPPER}}'                            => '--top-position: {{SIZE}}{{UNIT}};',
		// 		],
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'image_vertical_position',
		// 	[
		// 		'label'      => __('Vertical Position', 'happy-addons-pro'),
		// 		'type'       => Controls_Manager::SLIDER,
		// 		'size_units' => ['px', '%'],
		// 		'default'    => [
		// 			'unit' => '%',
		// 		],
		// 		'range'      => [
		// 			'%'  => [
		// 				'min' => -100,
		// 				'max' => 100,
		// 			],
		// 			'px' => [
		// 				'min' => -1000,
		// 				'max' => 1000,
		// 			],
		// 		],
		// 		// 'default'    => [
		// 		// 	'unit' => 'px',
		// 		// 	'size' => 80,
		// 		// ],
		// 		'condition'  => [
		// 			'image_position_toggle' => 'yes',
		// 		],
		// 		'selectors'  => [
		// 			'{{WRAPPER}} .ha-image-swap-item.active' => 'right: {{SIZE}}{{UNIT}};',
		// 			'{{WRAPPER}}'                            => '--left-position: {{SIZE}}{{UNIT}};',
		// 		],
		// 	]
		// );

		// $this->end_popover();

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ($settings['select_effect_type'] == 'slide') {

			$this->slide_images($settings);
		} else {
			$this->default_swap($settings);
		}

	}

	protected function slide_images($settings) {

		$this->add_render_attribute(
			'wrapper',
			[
				'class'        => ['ha-image-swap-ctn', 'slide_' . $settings['ig_effects_slides']],
				'data-trigger' => 'click',
				'data-layout'  => $settings['ig_effects_slides'],
			]
		);
		// if (!empty($settings['image_align'])) {
		// 	$this->add_render_attribute(
		// 		'wrapper',
		// 		[
		// 			'class' => [$settings['image_align']],
		// 		]
		// 	);
		// }
		?>
		<div class="ha_img_main_wrapper_top">
			<div <?php $this->print_render_attribute_string('wrapper');?>>
				<div class="ha-image-swap-fakeone">
					<img src="<?php echo esc_url($settings['first_image']['url']); ?>" />
				</div>
				<div class="ha-image-swap-insider">
					<div class="ha-image-swap-item">
						<img src="<?php echo esc_url($settings['first_image']['url']); ?>" />
					</div>
					<div class="ha-image-swap-item">
						<img src="<?php echo esc_url($settings['second_image']['url']); ?>" />
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	protected function default_swap($settings) {
		$this->add_render_attribute(
			'wrapper',
			[
				'class'        => ['ha-image-swap-wrapper', $settings['ig_effects']],
				'data-trigger' => $settings['swip_trigger'],
				'id'           => 'ha-image-swap-wrapper_id',
			]
		);
		if ('click' == $settings['swip_trigger']) {
			$this->add_render_attribute(
				'wrapper',
				[
					'data-click' => 'inactive',
				]
			);
		}
		$this->add_render_attribute(
			'inside',
			[
				'class' => ['ha-image-swap-wrapper__inside'],
			]
		);
		?>

		<div <?php $this->print_render_attribute_string('wrapper');?>>

			<?php printf('<img class="fake_img" src="%s">', esc_url($settings['first_image']['url']));?>

			<div <?php $this->print_render_attribute_string('inside');?>>
				<?php printf('<img class="img_swap_first" src="%s">', esc_url($settings['first_image']['url']));?>
				<?php printf('<img class="img_swap_second" src="%s">', esc_url($settings['second_image']['url']));?>
			</div>
		</div>
		<?php
	}

}

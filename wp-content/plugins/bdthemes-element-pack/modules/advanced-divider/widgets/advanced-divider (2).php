<?php

namespace ElementPack\Modules\AdvancedDivider\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Advanced_Divider extends Module_Base {

	public function get_name() {
		return 'bdt-advanced-divider';
	}

	public function get_title() {
		return BDTEP . esc_html__('Advanced Divider', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-advanced-divider';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['svg', 'divider', 'advanced', 'icon', 'separator', 'fancy', 'svg divider'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-advanced-divider'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-scripts'];
		} else {
			return ['ep-advanced-divider'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/HbtNHQJm3m0';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_svg_divider',
			[
				'label' => __('Advanced Divider', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'advanced_divider_type',
			[
				'label'   => esc_html__('Divider Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'toggle'  => false,
				'default' => 'select',
				'options' => [
					'select' => [
						'title' => esc_html__('Select', 'bdthemes-element-pack'),
						'icon'  => 'eicon-editor-list-ul'
					],
					'choose' => [
						'title' => esc_html__('Choose', 'bdthemes-element-pack'),
						'icon'  => 'eicon-upload'
					]
				]
			]
		);

		$this->add_control(
			'advanced_divider_select',
			[
				'label'     => esc_html__('Select Divider', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'line',
				'options'   => [
					'line'        => esc_html__('Line', 'bdthemes-element-pack'),
					'line-circle' => esc_html__('Line Circle', 'bdthemes-element-pack'),
					'line-cross'  => esc_html__('Line Cross', 'bdthemes-element-pack'),
					'line-star'   => esc_html__('Line Star', 'bdthemes-element-pack'),
					'line-dashed' => esc_html__('Line Dashed', 'bdthemes-element-pack'),
					'heart'       => esc_html__('Heart', 'bdthemes-element-pack'),
					'dashed'      => esc_html__('Dashed', 'bdthemes-element-pack'),
					'floret'      => esc_html__('Floret', 'bdthemes-element-pack'),
					'rectangle'   => esc_html__('Rectangle', 'bdthemes-element-pack'),
					'leaf'        => esc_html__('Leaf', 'bdthemes-element-pack'),
					'slash'       => esc_html__('Slash', 'bdthemes-element-pack'),
					'triangle'    => esc_html__('Triangle', 'bdthemes-element-pack'),
					'wave'        => esc_html__('Wave', 'bdthemes-element-pack'),
					'kiss-curl'   => esc_html__('Kiss Curl', 'bdthemes-element-pack'),
					'jemik'       => esc_html__('Jemik', 'bdthemes-element-pack'),
					'finest'      => esc_html__('Finest', 'bdthemes-element-pack'),
					'furrow'      => esc_html__('Furrow', 'bdthemes-element-pack'),
					'peak'        => esc_html__('Peak', 'bdthemes-element-pack'),
					'melody'      => esc_html__('Melody', 'bdthemes-element-pack'),
					// 'ripple'      => esc_html__( 'Ripple', 'bdthemes-element-pack' ),
					// 'elite'        => esc_html__( 'Elite', 'bdthemes-element-pack' ),
					// 'pick'         => esc_html__( 'Pick', 'bdthemes-element-pack' ),
					// 'blossom'      => esc_html__( 'Blossom', 'bdthemes-element-pack' ),
					// 'boundary'     => esc_html__( 'Boundary', 'bdthemes-element-pack' ),
					// 'cable'        => esc_html__( 'Cable', 'bdthemes-element-pack' ),
					// 'flush'        => esc_html__( 'Flush', 'bdthemes-element-pack' ),
					// 'floweret'     => esc_html__( 'Floweret', 'bdthemes-element-pack' ),
					// 'separk'       => esc_html__( 'Separk', 'bdthemes-element-pack' ),
					// 'splitter'     => esc_html__( 'Splitter', 'bdthemes-element-pack' ),
					// 'hi'           => esc_html__( 'Hi', 'bdthemes-element-pack' ),
					// 'hello'        => esc_html__( 'Hello', 'bdthemes-element-pack' ),
					// 'boom'         => esc_html__( 'Boom', 'bdthemes-element-pack' ),
					// 'bye'          => esc_html__( 'Bye', 'bdthemes-element-pack' ),
					// 'new'          => esc_html__( 'New', 'bdthemes-element-pack' ),
					// 'omg'          => esc_html__( 'Omg', 'bdthemes-element-pack' ),
					// 'lol'          => esc_html__( 'Lol', 'bdthemes-element-pack' ),
					// 'woow'         => esc_html__( 'Woow', 'bdthemes-element-pack' ),
					// 'welcome'      => esc_html__( 'Welcome', 'bdthemes-element-pack' ),
					// 'element-pack' => esc_html__( 'Element Pack', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'advanced_divider_type' => 'select',
				],
			]
		);

		$this->add_control(
			'advanced_divider_choose',
			[
				'label'     => __('Choose Divider', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => [
					'active' => true,
				],
				'default'   => [
					'url' => BDTEP_ASSETS_URL . 'images/divider/line.svg',
				],
				'condition' => [
					'advanced_divider_type' => 'choose',
				],
			]
		);

		$this->add_control(
			'divider_align',
			[
				'label'       => __('Alignment', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'default'     => 'center',
				'options'     => [
					'left'   => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-advanced-divider' => 'text-align: {{VALUE}}; margin: 0 auto; margin-{{VALUE}}: 0;',
				],
				'condition'   => [
					'advanced_divider_select!' => [
						'line',
						'dashed',
						'line-circle',
						'line-cross',
						'line-dashed',
						'line-star',
						'slash',
						'rectangle',
						'triangle',
						'wave',
						'kiss-curl',
						'jemik',
						'finest',
						'furrow'
					]
				],
				'render_type' => 'template'
			]
		);

		$this->add_responsive_control(
			'divider_line_align',
			[
				'label'       => __('Alignment', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'default'     => 'center',
				'options'     => [
					'left'   => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-advanced-divider' => 'text-align: {{VALUE}}; margin: 0 auto; margin-{{VALUE}}: 0;',
				],
				'condition'   => [
					'advanced_divider_select' => [
						'line',
						'dashed',
						'line-circle',
						'line-cross',
						'line-dashed',
						'line-star',
						'slash',
						'rectangle',
						'triangle',
						'wave',
						'kiss-curl',
						'jemik',
						'finest',
						'furrow'
					]
				],
				'render_type' => 'template',
			]
		);

		$this->add_responsive_control(
			'max_width',
			[
				'label'     => __('Max Width', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 1200,
						'min' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-divider' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'divider_gap_top',
			[
				'label'   => __('Top Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'max' => 150,
					],
				],
				'default' => [
					'size' => 15,
				],

				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-divider' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'divider_gap_bottom',
			[
				'label'     => __('Bottom Gap', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 150,
					],
				],
				'default'   => [
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-divider' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'divider_animation',
			[
				'label'   => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'divider_loop',
			[
				'label'   => esc_html__('Loop', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'divider_animation' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_additional',
			[
				'label' => __('Additional Settings', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'divider_svg_stroke_width',
			[
				'label'     => __('Stroke Width', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 10,
						'min' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-divider svg *' => 'stroke-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'divider_crop',
			[
				'label' => __('Divider Crop', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1000,
					],
				],

				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-divider svg' => 'transform: scale({{SIZE}}) scale(0.01)',
				],
			]
		);

		$this->add_responsive_control(
			'max_height',
			[
				'label'     => __('Match Height', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-divider svg' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_svg_divider',
			[
				'label' => __('Advanced Divider', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'divider_svg_stroke_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-divider svg *' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'line_cap',
			[
				'label'   => esc_html__('Line Cap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ep_square',
				'options' => [
					'ep_square' => esc_html__('Square', 'bdthemes-element-pack'),
					'ep_round'  => esc_html__('Rounded', 'bdthemes-element-pack'),
					'ep_butt'   => esc_html__('Butt', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'divider_offset_popover',
			[
				'label'        => esc_html__('Offset', 'bdthemes-element-pack') . BDTEP_NC,
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'render_type'  => 'ui',
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'divider_horizontal_offset',
			[
				'label' => __('Horizontal', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'condition' => [
					'divider_offset_popover' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-divider-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'divider_vertical_offset',
			[
				'label' => __('Vertical', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-divider-v-offset: {{SIZE}}px;'
				],
				'condition' => [
					'divider_offset_popover' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'divider_rotate',
			[
				'label'   => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-divider-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->end_popover();

		$this->end_controls_section();
	}

	public function render_svg_image() {
		$settings = $this->get_settings_for_display();

		if ($settings['advanced_divider_choose']['id']) {
			$settings['advanced_divider_choose_size'] = 'full';
			$image_html                               = Group_Control_Image_Size::get_attachment_image_src($settings['advanced_divider_choose']['id'], 'advanced_divider_choose', $settings);
		} else {
			$image_html = BDTEP_ASSETS_URL . 'images/divider/line.svg';
		}
?>
		<img src="<?php echo esc_url($image_html); ?>" alt="<?php echo get_the_title(); ?>" <?php echo $this->get_render_attribute_string('svg_image'); ?>>

	<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$line_cap = $settings['line_cap'];

		$this->add_render_attribute('wrapper', 'class', 'bdt-ep-advanced-divider');

		$this->add_render_attribute(
			[
				'wrapper' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							'animation' => ('yes' == $settings['divider_animation']) ? true : false,
							'loop'     => ($settings['divider_loop'] == 'yes') ? true : false,
						]))
					]
				]
			]
		);

		if ('yes' == $settings['divider_animation']) {
			$this->add_render_attribute('svg_image', 'class', 'bdt-animation-stroke');
		}
		$this->add_render_attribute('svg_image', 'class', $line_cap);

		$align     = ('left' == $settings['divider_align'] or 'right' == $settings['divider_align']) ? '-' . $settings['divider_align'] : '';
		$svg_image = BDTEP_ASSETS_URL . 'images/divider/' . $settings['advanced_divider_select'] . $align . '.svg';


	?>
		<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>

			<?php if ('select' == $settings['advanced_divider_type']) : ?>
				<img src="<?php echo esc_url($svg_image); ?>" alt="advanced divider" <?php echo $this->get_render_attribute_string('svg_image'); ?>>
			<?php elseif ('choose' == $settings['advanced_divider_type']) : ?>
				<?php $this->render_svg_image(); ?>
			<?php endif; ?>

		</div>

<?php
	}
}

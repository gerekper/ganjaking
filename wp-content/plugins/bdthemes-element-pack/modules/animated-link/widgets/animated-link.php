<?php

namespace ElementPack\Modules\AnimatedLink\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class AnimatedLink extends Module_Base {

	protected $_has_template_content = false;

	public function get_name() {
		return 'bdt-animated-link';
	}

	public function get_title() {
		return BDTEP . esc_html__('Animated Link', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-animated-link';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['animated', 'link', 'headline', 'split', 'gsap', 'vivid'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-animated-link'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/qs0gEVh0x7w';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_animated_link',
			[
				'label' => esc_html__('Animated Link', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'link_style',
			[
				'label'   => esc_html__('Style', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'metis',
				'options' => [
					'carpo'   => esc_html__('Carpo', 'bdthemes-element-pack'),
					'carme'   => esc_html__('Carme', 'bdthemes-element-pack'),
					'dia'     => esc_html__('Dia', 'bdthemes-element-pack'),
					'eirene'  => esc_html__('Eirene', 'bdthemes-element-pack'),
					'elara'   => esc_html__('Elara', 'bdthemes-element-pack'),
					'ersa'    => esc_html__('Ersa', 'bdthemes-element-pack'),
					'helike'  => esc_html__('Helike', 'bdthemes-element-pack'),
					'herse'   => esc_html__('Herse', 'bdthemes-element-pack'),
					'io'      => esc_html__('Io', 'bdthemes-element-pack'),
					'iocaste' => esc_html__('Iocaste', 'bdthemes-element-pack'),
					'kale'    => esc_html__('Kale', 'bdthemes-element-pack'),
					'leda'    => esc_html__('Leda', 'bdthemes-element-pack'),
					'metis'   => esc_html__('Metis', 'bdthemes-element-pack'),
					'mneme'   => esc_html__('Mneme', 'bdthemes-element-pack'),
					'thebe'   => esc_html__('Thebe', 'bdthemes-element-pack'),
				]
			]
		);

		$this->add_control(
			'link_text',
			[
				'label'       => esc_html__('Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => esc_html__('Animated Link', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'link_url',
			[
				'label'         => esc_html__('Link', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::URL,
				'default'       => ['url' => ''],
				'show_external' => false,
				'dynamic'       => ['active' => true],
			]
		);

		$this->add_responsive_control(
			'link_alignment',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					]
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-widget-bdt-animated-link .elementor-widget-container' => 'text-align: {{VALUE}}'
				]
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_link',
			[
				'label'     => esc_html__('Animated Link', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'link_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_hover_text_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-link:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_style_color',
			[
				'label'     => esc_html__('Style Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-animated-link:before, {{WRAPPER}} .bdt-ep-animated-link:after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'link_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-animated-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'link_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-animated-link',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ($settings['link_style'] == 'leda') {
			$this->add_render_attribute('link-wrap', 'data-text',  esc_attr($settings['link_text']));
		}

		$this->add_render_attribute('link-wrap', 'class', 'bdt-ep-animated-link bdt-ep-animated-link--' . esc_attr($settings['link_style']));

		if (!empty($settings['link_url']['url'])) {
			$this->add_link_attributes( 'link-wrap', $settings['link_url'] );
		}

		?>
		<?php if ($settings['link_style'] == 'leda' or $settings['link_style'] == 'elara' or $settings['link_style'] == 'ersa' or $settings['link_style'] == 'eirene' or $settings['link_style'] == 'helike') : ?>
			<a <?php echo ($this->get_render_attribute_string('link-wrap')); ?>>
				<span><?php echo esc_html($settings['link_text']); ?></span>
			</a>
		<?php elseif ($settings['link_style'] == 'iocaste') : ?>
			<a <?php echo ($this->get_render_attribute_string('link-wrap')); ?>>
				<span><?php echo esc_html($settings['link_text']); ?></span>
				<svg class="bdt-link__graphic bdt-link__graphic--slide" width="300%" height="100%" viewBox="0 0 1200 60" preserveAspectRatio="none">
					<path d="M0,56.5c0,0,298.666,0,399.333,0C448.336,56.5,513.994,46,597,46c77.327,0,135,10.5,200.999,10.5c95.996,0,402.001,0,402.001,0"></path>
				</svg>
			</a>
		<?php elseif ($settings['link_style'] == 'herse') : ?>
			<a <?php echo ($this->get_render_attribute_string('link-wrap')); ?>>
				<span><?php echo esc_html($settings['link_text']); ?></span>
				<svg class="bdt-link__graphic bdt-link__graphic--stroke bdt-link__graphic--arc" width="100%" height="18" viewBox="0 0 59 18">
					<path d="M.945.149C12.3 16.142 43.573 22.572 58.785 10.842" pathLength="1" />
				</svg>
			</a>
		<?php elseif ($settings['link_style'] == 'carme') : ?>
			<a <?php echo ($this->get_render_attribute_string('link-wrap')); ?>>
				<span><?php echo esc_html($settings['link_text']); ?></span>
				<svg class="bdt-link__graphic bdt-link__graphic--stroke bdt-link__graphic--scribble" width="100%" height="9" viewBox="0 0 101 9">
					<path d="M.426 1.973C4.144 1.567 17.77-.514 21.443 1.48 24.296 3.026 24.844 4.627 27.5 7c3.075 2.748 6.642-4.141 10.066-4.688 7.517-1.2 13.237 5.425 17.59 2.745C58.5 3 60.464-1.786 66 2c1.996 1.365 3.174 3.737 5.286 4.41 5.423 1.727 25.34-7.981 29.14-1.294" pathLength="1" />
				</svg>
			</a>
		<?php else : ?>
			<a <?php echo ($this->get_render_attribute_string('link-wrap')); ?>>
				<?php echo esc_html($settings['link_text']); ?>
			</a>
		<?php endif; ?>

<?php
	}
}

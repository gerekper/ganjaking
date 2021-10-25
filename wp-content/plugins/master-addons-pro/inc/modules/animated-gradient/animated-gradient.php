<?php

namespace MasterAddons\Modules;

use \Elementor\Controls_Manager;
use \Elementor\Repeater;

class Extension_Animated_Gradient_Backgrounds
{

	private static $_instance = null;

	public function __construct()
	{
		add_action('elementor/element/after_section_end', [$this, 'register_controls'], 10, 3);
		add_action('elementor/element/print_template', [$this, '_print_template'], 10, 2);
		add_action('elementor/section/print_template', [$this, '_print_template'], 10, 2);
		add_action('elementor/column/print_template', [$this, '_print_template'], 10, 2);
		add_action('elementor/frontend/before_render', [$this, '_before_render']);
	}

	public function register_controls($element, $section_id, $args)
	{
		if (('section' === $element->get_name() && 'section_background' === $section_id) || ('column' === $element->get_name() && 'section_style' === $section_id)) {

			$element->start_controls_section(
				'ma_el_animated_gradient',
				[
					'tab' => Controls_Manager::TAB_STYLE,
					'label' => MA_EL_BADGE . __(' Animated Gradient Background', MELA_TD)
				]
			);
			$element->add_control(
				'ma_el_animated_gradient_enable',
				[
					'type'  => Controls_Manager::SWITCHER,
					'label' => __('Enable', MELA_TD),
					'default' => '',
					'label_on' => __('Yes', MELA_TD),
					'label_off' => __('No', MELA_TD),
					'return_value' => 'yes',
					'prefix_class'  =>  'ma-el-animated-gradient-',
					'render_type'   => 'template',
				]
			);
			$element->add_control(
				'gradient_background_angle',
				[
					'label' => __('Angle', MELA_TD),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['deg'],
					'range' => [
						'deg' => [
							'min' => -45,
							'max' => 180,
							'step' => 2,
						],
					],
					'default' => [
						'unit' => 'deg',
						'size' => -45,
					],
					'selectors' => [
						'{{WRAPPER}} .box' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'ma_el_animated_gradient_enable' => 'yes',
					]
				]
			);

			$repeater = new Repeater();

			$repeater->add_control(
				'ma_el_animated_gradient_color',
				[
					'label' => __('Add Color', MELA_TD),
					'type'  =>  Controls_Manager::COLOR,
				]
			);

			$element->add_control(
				'gradient_color_list',
				[
					'label' =>  __('Color', MELA_TD),
					'type'  => Controls_Manager::REPEATER,
					'fields' 		=> $element->get_controls(),
					'title_field'   =>  'Color {{{ma_el_animated_gradient_color}}}',
					'show_label'        =>  true,

					'default'   => [
						[
							'ma_el_animated_gradient_color'   =>  '#00a6d8'
						],
						[
							'ma_el_animated_gradient_color'   =>  '#b800c9'
						],
						[
							'ma_el_animated_gradient_color'   =>  '#e57600'
						]
					],
					'selectors' => [
						'{{WRAPPER}}' => 'background : linear-gradient({{gradient_background_angle.SIZE}}{{gradient_background_angle.UNIT}} );',
					],
					'condition' =>  [
						'ma_el_animated_gradient_enable' => 'yes',
					]
				]
			);

			$element->end_controls_section();
		}
	}

	public function _before_render($element)
	{
		if ($element->get_name() != 'section' && $element->get_name() != 'column') {
			return;
		}

		$settings = $element->get_settings();

		if ($settings['ma_el_animated_gradient_enable'] == 'yes') {

			$angle = $settings['gradient_background_angle']['size'];
			$element->add_render_attribute('_wrapper', 'data-angle', $settings['gradient_background_angle']['size'] . 'deg');
			$gradient_color_list = $settings['gradient_color_list'];
			foreach ($gradient_color_list as $gradient_color) {
				$color[] = $gradient_color['ma_el_animated_gradient_color'];
			};
			$colors = implode(',', $color);
			$element->add_render_attribute('_wrapper', 'data-color', $colors);
?>
			<!-- <style>
            .elementor-element-<?php //echo $element->get_id();
								?>.ma-el-animated-gradient-yes{
                background : <?php //echo 'linear-gradient('.$angle.'deg'.','.$colors.')';
								?>;
            }

        </style>-->
		<?php

		}
	}

	function _print_template($template, $widget)
	{
		?>
		<?php
		if ($widget->get_name() != 'section' && $widget->get_name() != 'column') {
			return $template;
		}
		$old_template = $template;
		ob_start();
		?>
		<# color_list=settings.gradient_color_list; angle=settings.gradient_background_angle.size; var color=[]; var i=0; _.each(color_list , function(color_list){ color[i]=color_list.ma_el_animated_gradient_color; i=i+1; }); view.addRenderAttribute('_wrapper', 'data-color' , color); #>
			<div class="animated-gradient" data-angle="{{{ angle }}}deg" data-color="{{{ color }}}"></div>
	<?php
		$slider_content = ob_get_contents();
		ob_end_clean();
		$template = $slider_content . $old_template;
		return $template;
	}



	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}
Extension_Animated_Gradient_Backgrounds::instance();

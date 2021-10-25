<?php

namespace MasterAddons\Modules;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Css_Filter;
use \Elementor\Group_Control_Background;

use \MasterAddons\Inc\Classes\JLTMA_Extension_Prototype;
use MasterAddons\Inc\Controls\MA_Group_Control_Transition;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
};


/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 17/09/2020
 */


/**
 * Blob - Morphing Animation
 */

class Extension_Morphing_Effects extends JLTMA_Extension_Prototype
{

	private static $instance = null;
	public $name = 'Morphing Effects';
	public $has_controls = true;
	public $common_sections_actions = array(
		array(
			'element' => 'common',
			'action' => '_section_style',
		),

		array(
			'element' => 'column',
			'action' => 'section_advanced',
		),
	);


	// public function get_script_depends() {
	//  return [
	//         'jltma-floating-effects',
	//         'master-addons-scripts'
	//     ];
	// }



	private function add_controls($element, $args)
	{

		$element_type = $element->get_type();

		$element->add_control(
			'jltma_morphing_effects_switch',
			[
				'label' 				=> __('Morphing Effects', MELA_TD),
				'type' 					=> Controls_Manager::SWITCHER,
				'default' 				=> '',
				'label_on' 				=> __('Yes', MELA_TD),
				'label_off' 			=> __('No', MELA_TD),
				'return_value' 			=> 'yes',
				'frontend_available' 	=> true,
				'prefix_class' 			=> 'jltma-morphing-fx-',
			]
		);


		$element->add_control(
			'jltma_morphing_blob_animation',
			[
				'label' 		=> esc_html__('Blob Animation', MELA_TD),
				'type' 			=> Controls_Manager::SELECT,
				'options' 		=> [
					'jltma_blob_anim_01'	=> esc_html__('Effect One', MELA_TD),
					'animation_svg_02' 		=> esc_html__('Effect Two', MELA_TD),
					'animation_svg_03' 		=> esc_html__('Effect Three', MELA_TD),
					'animation_svg_04' 		=> esc_html__('Effect Four', MELA_TD),
					'animation_svg_05' 		=> esc_html__('Effect Five', MELA_TD),
				],
				'default' 		=> 'animation_svg_02',
				'frontend_available' 	=> true,
				'prefix_class' 			=> '',
				'condition'          => [
					'jltma_morphing_effects_switch' => 'yes'
				]
			]
		);


		$element->add_control(
			'jltma_morphing_blob_type',
			[
				'label' 		=> esc_html__('Blob Type', MELA_TD),
				'type' 			=> Controls_Manager::SELECT,
				'options' 		=> [
					'color'			=> esc_html__('Color', MELA_TD),
					'gradient'			=> esc_html__('Gradient', MELA_TD),
					'lottie' 		=> esc_html__('Lottie', MELA_TD)
				],
				'default' 		=> 'gradient',
				'condition'     => [
					'jltma_morphing_effects_switch' => 'yes'
				]
			]
		);




		$element->start_controls_tabs('jltma_morphing_effects_tabs');

		$element->start_controls_tab(
			'jltma_morphing_effects_normal',
			[
				'label' => esc_html__('Normal', MELA_TD),
				'condition'          => [
					'jltma_morphing_effects_switch' => 'yes'
				]

			]
		);


		$element->add_control(
			'ma_el_dual_heading_icon_color',
			[
				'label'		=> esc_html__('Background Color', MELA_TD),
				'type'		=> Controls_Manager::COLOR,
				'default' => '#4b00e7',
				'selectors'	=> [
					'{{WRAPPER}}.jltma_blob_anim_01,
					 {{WRAPPER}}.animation_svg_02,
					 {{WRAPPER}}.animation_svg_03,
					 {{WRAPPER}}.animation_svg_04' => 'background: {{VALUE}};',
				],
				'condition' => [
					'jltma_morphing_blob_type' => 'color',
					'jltma_morphing_effects_switch' => 'yes'
				],
			]
		);


		$element->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' 		=> 'jltma_morphing_effects_background',
				'types' 	=> ['gradient'],
				'frontend_available' 	=> true,
				'selectors' => [
					'{{WRAPPER}}.jltma_blob_anim_01',
					'{{WRAPPER}}.animation_svg_02',
					'{{WRAPPER}}.animation_svg_03',
					'{{WRAPPER}}.animation_svg_04',
				],
				'condition'          => [
					'jltma_morphing_blob_type' => 'gradient',
					'jltma_morphing_effects_switch' => 'yes'
				],
			]
		);

		$element->add_control(
			'jltma_morphing_blob_blend_mode',
			[
				'label' 		=> esc_html__('Blend Mode', MELA_TD),
				'type' 			=> Controls_Manager::SELECT,
				'options' 		=> [
					'normal'			=> 'Normal',
					'color'				=> 'Color',
					'multiply'			=> 'Multiply',
					'screen'			=> 'Screen',
					'overlay'			=> 'Overlay',
					'darken'			=> 'Darken',
					'lighten'			=> 'Lighten',
					'color-dodge'		=> 'Color Dodge',
					'color-burn'		=> 'Color Burn',
					'hard-light'		=> 'Hard Light',
					'soft-light'		=> 'Soft Light',
					'difference'		=> 'Difference',
					'exclusion'			=> 'Exclusion',
					'hue'				=> 'Hue',
					'saturation'		=> 'Saturation',
					'luminosity'		=> 'Luminosity',
				],
				'default' 				=> 'multiply',
				'frontend_available' 	=> true,
				'separator'         	=> 'before',
				'selectors'				=> [
					'{{WRAPPER}} .elementor-widget-container' => 'mix-blend-mode: {{VALUE}};',
				],
				'condition'          => [
					'jltma_morphing_effects_switch' => 'yes'
				]
			]
		);


		$element->add_responsive_control(
			'jltma_morphing_effects_width',
			[
				'label' => esc_html__('Width', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				// 'default' => [
				// 	'size' => '100',
				// ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 2000,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.jltma_blob_anim_01,
					 {{WRAPPER}}.animation_svg_02,
					 {{WRAPPER}}.animation_svg_03,
					 {{WRAPPER}}.animation_svg_04' => 'width: {{SIZE}}{{UNIT}}  !important;',
				],
				'condition'          => [
					'jltma_morphing_effects_switch' => 'yes'
				]
			]
		);

		$element->add_responsive_control(
			'jltma_morphing_effects_height',
			[
				'label' => esc_html__('Height', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.jltma_blob_anim_01,
					 {{WRAPPER}}.animation_svg_02,
					 {{WRAPPER}}.animation_svg_03,
					 {{WRAPPER}}.animation_svg_04' => 'height: {{SIZE}}{{UNIT}} !important;'
				],
				'condition'          => [
					'jltma_morphing_effects_switch' => 'yes'
				]
			]
		);


		$element->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'jltma_morphing_effects_filters',
				'selector' => '{{WRAPPER}} .elementor-widget-container',
				'condition'          => [
					'jltma_morphing_effects_switch' => 'yes'
				]
			]
		);


		$element->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'jltma_morphing_effects_transition',
				'selector' 		=> '{{WRAPPER}} .elementor-widget-container',
				'condition'          => [
					'jltma_morphing_effects_switch' => 'yes'
				]
			]
		);


		// $element->add_control(
		// 	'jltma_morphing_effects_transition_duration',
		// 	[
		// 		'label' => esc_html__( 'Transition Duration', MELA_TD ),
		// 		'type' => Controls_Manager::SLIDER,
		// 		'range' => [
		// 			'px' => [
		// 				'step' => 100,
		// 				'min' => 0,
		// 				'max' => 10000,
		// 			],
		// 		],
		// 		'default' => [
		// 			'size' => '400',
		// 		],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .elementor-widget-container' => 'transition: all {{SIZE}}ms;',
		// 		],
		// 	]
		// );

		$element->end_controls_tab();



		$element->start_controls_tab(
			'jltma_morphing_effects_hover',
			[
				'label' => esc_html__('Hover', MELA_TD),
				'condition'          => [
					'jltma_morphing_effects_switch' => 'yes'
				]
			]
		);


		$element->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'jltma_morphing_effects_filters_hover',
				'selector' => '{{WRAPPER}} .elementor-widget-container:hover',
				'condition'          => [
					'jltma_morphing_effects_switch' => 'yes'
				]
			]
		);


		$element->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'jltma_morphing_effects_hover_transition',
				'selector' 		=> '{{WRAPPER}} .elementor-widget-container',
			]
		);



		// $element->add_control(
		// 	'jltma_morphing_effects_transition_hover_duration',
		// 	[
		// 		'label' => esc_html__( 'Transition Duration', MELA_TD ),
		// 		'type' => Controls_Manager::SLIDER,
		// 		'range' => [
		// 			'px' => [
		// 				'step' => 100,
		// 				'min' => 0,
		// 				'max' => 10000,
		// 			],
		// 		],
		// 		'default' => [
		// 			'size' => '400',
		// 		],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .elementor-widget-container:hover' => 'transition: all {{SIZE}}ms;',
		// 		],
		// 	]
		// );

		$element->end_controls_tab();

		$element->end_controls_tabs();
	}



	protected function add_actions()
	{

		// Activate controls for widgets
		add_action('elementor/element/common/jltma_section_morphing_effects_advanced/before_section_end', function ($element, $args) {
			$this->add_controls($element, $args);
		}, 10, 2);

		// add_filter('elementor/widget/print_template', array($this, 'jltma_morphing_print_template'), 11, 2);
		add_action('elementor/widget/render_content', array($this, 'jltma_morphing_render_template'), 11, 2);
	}


	public function jltma_morphing_print_template($content, $widget)
	{
		if (!$content)
			return '';

		$settings = $widget->get_settings_for_display();

		$id_item = $widget->get_id();

		$svg_shape = "";
		if ($settings['jltma_morphing_blob_animation'] == "animation_svg_05") {
			$svg_shape = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 747.2 726.7">
				<path d="M539.8 137.6c98.3 69 183.5 124 203 198.4 19.3 74.4-27.1 168.2-93.8 245-66.8 76.8-153.8 136.6-254.2 144.9-100.6 8.2-214.7-35.1-292.7-122.5S-18.1 384.1 7.4 259.8C33 135.6 126.3 19 228.5 2.2c102.1-16.8 213.2 66.3 311.3 135.4z"/>
			</svg>';
		}


		// $content = "<# if ( '' !== settings.jltma_morphing_effects_switch ) { #><div id=\"rellax-{{id}}\" class=\"rellax\" data-rellax-percentage=\"{{ settings.percentage_rellax.size }}\" data-rellax-zindex=\"{{ settings.zindex_rellax }}\">" . $content . "</div><# } else { #>" . $content . "<# } #>";
		$content = "<# if ( '' !== settings.jltma_morphing_effects_switch ) { #><div class='jltma-blob' style='--time: 20s; --amount: 5; --fill: #56cbb9;'>" . $svg_shape . '<div class="jltma-morphing-content"> ' . $content . "</div></div><# } else { #>" . $content . "<# } #>";
		return $content;
	}


	public function jltma_morphing_render_template($content, $widget)
	{
		$settings = $widget->get_settings_for_display();

		if (isset($settings['jltma_morphing_effects_switch']) && $settings['jltma_morphing_effects_switch'] == 'yes') {

			$this->_enqueue_alles();

			if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
			}

			$item_id = $widget->get_id();

			// $svg_shape = "";
			// $svg_shape_end = "";

			// if($settings['jltma_morphing_blob_animation'] == "animation_svg_05"){
			// 	$svg_shape .= '';
			// }

			if ($settings['jltma_morphing_blob_animation'] == "animation_svg_05") {
				// $svg_shape_end .= "</svg>";


				//         $content = '<div class="jltma-blob" style="--time: 20s; --amount: 5; --fill: #56cbb9;" data-blob-path="M539.8 137.6c98.3 69 183.5 124 203 198.4 19.3 74.4-27.1 168.2-93.8 245-66.8 76.8-153.8 136.6-254.2 144.9-100.6 8.2-214.7-35.1-292.7-122.5S-18.1 384.1 7.4 259.8C33 135.6 126.3 19 228.5 2.2c102.1-16.8 213.2 66.3 311.3 135.4z">
				//            	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 747.2 726.7">
				//            	<clipPath id="clipShape'. $id_item .'">
				//            	<path d="M539.8 137.6c98.3 69 183.5 124 203 198.4 19.3 74.4-27.1 168.2-93.8 245-66.8 76.8-153.8 136.6-254.2 144.9-100.6 8.2-214.7-35.1-292.7-122.5S-18.1 384.1 7.4 259.8C33 135.6 126.3 19 228.5 2.2c102.1-16.8 213.2 66.3 311.3 135.4z"/>
				//            	</path>
				//            	</clipPath>

				// 		<g clip-path="url(#clipShape)" >
				// 		<rect width="500" height="500"></rect>
				// <foreignObject width="500" height="500">
				// <p class="statement">'. $content .'</p>
				// </foreignObject> </g></svg></div>';


				//     	$content = '<div style="width:600px; height:500px; -webkit-clip-path: url(#maskRect' . $item_id . ');">' . $content . '</div>';
				//     	$content .= '<div class="jltma-blob"  style="--time: 2s; --amount: 5; --fill: #56cbb9;">
				//   <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 319.2 356.7">
				//   	<clipPath id="maskRect' . $item_id . '" >
				//     	<path d="M271.9 50.3c30.6 29.3 51.3 75.5 46.6 123.9-4.6 48.4-34.6 99-86.5 136.3s-125.6 61.4-168.3 35.3S9.4 243.5 3.4 177.3C-2.7 111.2-3.1 55.2 24 26.7 51.1-1.9 105.9-2.9 153.4 2.8c47.6 5.8 88 18.2 118.5 47.5z"></path>
				//     </clipPath>
				//   </svg>
				// </div>';



				//     }



				$content = '<div style="width:650px; height:500px; -webkit-clip-path: url(#maskRect' . $item_id . ');">' . $content . '</div>';
				$content .= '<div class="jltma-blob"  style="--time: 12s; --amount: 5; --fill: #56cbb9;">
			        	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 319.2 356.7">

			        	<path d="M271.9 50.3c30.6 29.3 51.3 75.5 46.6 123.9-4.6 48.4-34.6 99-86.5 136.3s-125.6 61.4-168.3 35.3S9.4 243.5 3.4 177.3C-2.7 111.2-3.1 55.2 24 26.7 51.1-1.9 105.9-2.9 153.4 2.8c47.6 5.8 88 18.2 118.5 47.5z"></path>

			        	<foreignObject style="width:350px; height:300px;">
			        	<p class="statement">' . $content . '</p>
			        	</foreignObject>


			        	</svg>
		        	</div>';
			}


			// <div class="tk-blob" style="--fill: #ff4b82;">
			//   <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 319.2 356.7">
			//     <path d="M271.9 50.3c30.6 29.3 51.3 75.5 46.6 123.9-4.6 48.4-34.6 99-86.5 136.3s-125.6 61.4-168.3 35.3S9.4 243.5 3.4 177.3C-2.7 111.2-3.1 55.2 24 26.7 51.1-1.9 105.9-2.9 153.4 2.8c47.6 5.8 88 18.2 118.5 47.5z"></path>
			//   </svg>
			// </div>


		}
		return $content;
	}

	public static function get_instance()
	{
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

Extension_Morphing_Effects::get_instance();

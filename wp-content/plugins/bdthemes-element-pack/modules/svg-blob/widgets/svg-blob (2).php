<?php

namespace ElementPack\Modules\SvgBlob\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if (!defined('ABSPATH')) {
	exit;
}

class Svg_Blob extends Module_Base {

	public function get_name() {
		return 'bdt-svg-blob';
	}

	public function get_title() {
		return BDTEP . esc_html__('SVG Blob', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-svg-blob';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['svg-blob', 'svg', 'blob', 'image', 'morphing'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-svg-blob'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['animejs', 'ep-scripts'];
		} else {
			return ['animejs', 'ep-svg-blob'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/sgyUOC7TXPA';
	}
	protected function register_controls() {
		$this->register_controls_blob_content();
		$this->register_controls_style_blob();
		$this->register_style_controls_gradient_blob();
	}

	private function register_controls_blob_content() {
		$this->start_controls_section(
			'svg_blob_section_content',
			[
				'label' => esc_html__('Blob Content', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'svg_blob_select_type',
			[
				'label' => esc_html__('Select Type', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default' => 'images',
				'options' => [
					'images' => esc_html__('Image', 'bdthemes-element-pack'),
					'color' => esc_html__('Color', 'bdthemes-element-pack'),
					'gradient' => esc_html__('Gradient Color', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'svg_blob_image',
			[
				'label' => esc_html__('Choose Image', 'bdthemes-element-pack'),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'image',
				'library_type' => 'image',
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				],
				'condition'	=> [
					'svg_blob_select_type' => ['images'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'svg_blob_image_size',
				'default' => 'large',
				'separator' => 'none',
				'condition'	=> [
					'svg_blob_select_type' => ['images'],
				],
			]
		);

		$this->add_control(
			'svg_blob_color_show_stroke',
			[
				'label' => esc_html__('Show Stroke', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Show', 'bdthemes-element-pack'),
				'label_off' => esc_html__('Hide', 'bdthemes-element-pack'),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'	=> [
					'svg_blob_select_type' => ['color'],
				],
			]
		);
		$this->add_control(
			'svg_blob_shape_style',
			[
				'label'      => esc_html__('Select Shape Style', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SELECT,
				'options'    => [
					'shape-01' => esc_html__('Shape 01', 'bdthemes-element-pack'),
					'shape-02' => esc_html__('Shape 02', 'bdthemes-element-pack'),
					'shape-03' => esc_html__('Shape 03', 'bdthemes-element-pack'),
					'shape-04' => esc_html__('Shape 04', 'bdthemes-element-pack'),
					'shape-05' => esc_html__('Shape 05', 'bdthemes-element-pack'),
					'shape-06' => esc_html__('Shape 06', 'bdthemes-element-pack'),
					'shape-07' => esc_html__('Shape 07', 'bdthemes-element-pack'),
					'shape-08' => esc_html__('Shape 08', 'bdthemes-element-pack'),
					'shape-09' => esc_html__('Shape 09', 'bdthemes-element-pack'),
					'shape-10' => esc_html__('Shape-10', 'bdthemes-element-pack'),
				],
				'default'    => 'shape-01',
				'dynamic'    => ['active' => true],
				'separator'  => 'before',
			]
		);
//		$this->add_control(
//			'svg_blob_easing_type',
//			[
//				'label' => esc_html__('Animation Type', 'bdthemes-element-pack'),
//				'type' => Controls_Manager::SELECT,
//				'default' => 'easeInSine',
//				'frontend_available' => true,
//				'options' => [
//					'linear' => esc_html__('Linear', 'bdthemes-element-pack'),
//					'easeInQuad' => esc_html__('EaseInQuad', 'bdthemes-element-pack'),
//					'easeOutQuad' => esc_html__('EaseOutQuad', 'bdthemes-element-pack'),
//					'easeInOutQuad' => esc_html__('EaseInOutQuad', 'bdthemes-element-pack'),
//					'easeOutInQuad' => esc_html__('EaseOutInQuad', 'bdthemes-element-pack'),
//					'easeInCubic' => esc_html__('EaseInCubic', 'bdthemes-element-pack'),
//					'easeOutCubic' => esc_html__('EaseOutCubic', 'bdthemes-element-pack'),
//					'easeInOutCubic' => esc_html__('EaseInOutCubic', 'bdthemes-element-pack'),
//					'easeOutInCubic' => esc_html__('EaseOutInCubic', 'bdthemes-element-pack'),
//					'easeInQuart' => esc_html__('EaseInQuart', 'bdthemes-element-pack'),
//					'easeOutQuart' => esc_html__('EaseOutQuart', 'bdthemes-element-pack'),
//					'easeInOutQuart' => esc_html__('EaseInOutQuart', 'bdthemes-element-pack'),
//					'easeOutInQuart' => esc_html__('EaseOutInQuart', 'bdthemes-element-pack'),
//					'easeInQuint' => esc_html__('EaseInQuint', 'bdthemes-element-pack'),
//					'easeOutQuint' => esc_html__('EaseOutQuint', 'bdthemes-element-pack'),
//					'easeInOutQuint' => esc_html__('EaseInOutQuint', 'bdthemes-element-pack'),
//					'easeOutInQuint' => esc_html__('EaseOutInQuint', 'bdthemes-element-pack'),
//					'easeInSine' => esc_html__('EaseInSine', 'bdthemes-element-pack'),
//					'easeOutSine' => esc_html__('EaseOutSine', 'bdthemes-element-pack'),
//					'easeInOutSine' => esc_html__('EaseInOutSine', 'bdthemes-element-pack'),
//					'easeOutInSine' => esc_html__('EaseOutInSine', 'bdthemes-element-pack'),
//					'easeInExpo' => esc_html__('EaseInExpo', 'bdthemes-element-pack'),
//					'easeOutExpo' => esc_html__('EaseOutExpo', 'bdthemes-element-pack'),
//					'easeInOutExpo' => esc_html__('EaseInOutExpo', 'bdthemes-element-pack'),
//					'easeOutInExpo' => esc_html__('EaseOutInExpo', 'bdthemes-element-pack'),
//					'easeInCirc' => esc_html__('EaseInCirc', 'bdthemes-element-pack'),
//					'easeOutCirc' => esc_html__('EaseOutCirc', 'bdthemes-element-pack'),
//					'easeInOutCirc' => esc_html__('EaseInOutCirc', 'bdthemes-element-pack'),
//					'easeOutInCirc' => esc_html__('EaseOutInCirc', 'bdthemes-element-pack'),
//					'easeInBack' => esc_html__('EaseInBack', 'bdthemes-element-pack'),
//					'easeOutBack' => esc_html__('EaseOutBack', 'bdthemes-element-pack'),
//					'easeInOutBack' => esc_html__('EaseInOutBack', 'bdthemes-element-pack'),
//					'easeOutInBack' => esc_html__('EaseOutInBack', 'bdthemes-element-pack'),
//					'easeInBounce' => esc_html__('EaseInBounce', 'bdthemes-element-pack'),
//					'easeOutBounce' => esc_html__('EaseOutBounce', 'bdthemes-element-pack'),
//					'easeInOutBounce' => esc_html__('EaseInOutBounce', 'bdthemes-element-pack'),
//					'easeOutInBounce' => esc_html__('EaseOutInBounce', 'bdthemes-element-pack'),
//				],
//			]
//		);
//
//		$this->add_control(
//			'svg_blob_direction',
//			[
//				'label' => esc_html__('Blob Direction', 'bdthemes-element-pack'),
//				'type' => Controls_Manager::SELECT,
//				'default' => 'alternate',
//				'frontend_available' => true,
//				'options' => [
//					'normal' => esc_html__('Normal', 'bdthemes-element-pack'),
//					'reverse' => esc_html__('Reverse', 'bdthemes-element-pack'),
//					'alternate' => esc_html__('Alternate', 'bdthemes-element-pack'),
//				],
//			]
//		);

		$this->add_control(
			'svg_blob_loop',
			[
				'label' => esc_html__('Loop', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,

			]
		);

		$this->add_control(
			'svg_blob_duration',
			[
				'label'         => esc_html__('Duration', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SLIDER,
				'description'   => esc_html__('Set the duration of the animation in millisecond.', 'bdthemes-element-pack'),
				'range'         => [
					'px'        => [
						'min'   => 0,
						'max'   => 10000,
						'step'  => 1,
					],
				],
				'frontend_available' => true,

			]
		);
		$this->add_control(
			'svg_blob_delay',
			[
				'label'         => esc_html__('Delay', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SLIDER,
				'description'   => esc_html__('Set the duration of the animation in millisecond.', 'bdthemes-element-pack'),
				'range'         => [
					'px'        => [
						'min'   => 0,
						'max'   => 10000,
						'step'  => 1,
					],
				],
				'frontend_available' => true,

			]
		);
		$this->add_control(
			'svg_blob_end_delay',
			[
				'label'         => esc_html__('End Delay', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SLIDER,
				'description'   => esc_html__('Set the end delay of the animation in millisecond.', 'bdthemes-element-pack'),
				'range'         => [
					'px'        => [
						'min'   => 0,
						'max'   => 10000,
						'step'  => 1,
					],
				],
				'frontend_available' => true,

			]
		);

		$this->end_controls_section();
	}

	private function register_controls_style_blob() {
		$this->start_controls_section(
			'svg_blob_section_style',
			[
				'label' => esc_html__('Blob Style', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
            'svg_blob_fill_color',
            [
                'label' => esc_html__('SVG Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-svg-blob svg path' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'svg_blob_select_type' => ['color'],
                ]
            ]
        );
        $this->add_control(
            'svg_blob_stroke_color',
            [
                'label' => esc_html__('Stroke Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '#BB004B',
                'selectors' => [
                    '{{WRAPPER}} .bdt-svg-blob svg path' => 'stroke: {{VALUE}};',
                ],
                'condition' => [
                    'svg_blob_color_show_stroke' => ['yes'],
                    'svg_blob_select_type' => ['color'],
                ]

            ]
        );

        $this->add_control(
            'svg_blob_stroke_weight',
            [
                'label' => esc_html__('Stroke Weight', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 5,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-svg-blob svg path' => 'stroke-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'svg_blob_color_show_stroke' => ['yes'],
                    'svg_blob_select_type' => ['color'],
                ]
            ]
        );


        // SVG Gradient Color style


        $this->add_control(
            'svg_blob_gradient_color_primary',
            [
                'label' => esc_html__('Color Primary', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition'	=> [
                    'svg_blob_select_type' => ['gradient'],
                ],
            ]
        );

        $this->add_control(
            'svg_blob_gradient_color_secondary',
            [
                'label' => esc_html__('Color Secondary', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition'	=> [
                    'svg_blob_select_type' => ['gradient'],
                ],
            ]
        );

		$this->add_responsive_control(
			'svg_blob_width',
			[
				'label' => esc_html__('Width', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1200,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-svg-blob svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'svg_blob_height',
			[
				'label' => esc_html__('Height', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1200,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-svg-blob svg' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'svg_blob_opacity',
			[
				'label' => esc_html__('Opacity', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .bdt-svg-blob svg > *' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'svg_blob_filters',
				'selector' => '{{WRAPPER}} .bdt-svg-blob svg > *',

			]
		);

		$this->end_controls_section();
	}


	private function register_style_controls_gradient_blob() {
		$this->start_controls_section(
			'svg_blob_gradient_section_style',
			[
				'label' => esc_html__('Advanced Blob Style', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'svg_blob_select_type' => ['gradient'],
				],
			]
		);

		$this->add_control(
			'svg_blob_offset_primary',
			[
				'label' => esc_html__('Primary Color Offset (%)', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'separator' => 'before',
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
				],
			]
		);
		$this->add_control(
			'svg_blob_offset_secondary',
			[
				'label' => esc_html__('Secondary Color Offset (%)', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
				],
			]
		);
		$this->add_control(
			'svg_blob_color_position_x1',
			[
				'label' => esc_html__('Position X1', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'separator' => 'before',
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'step' => 0.5,
				'default' => [
					'unit' => '%',
				],
			]
		);

		$this->add_control(
			'svg_blob_color_position_x2',
			[
				'label' => esc_html__('Position X2', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'step' => 0.5,
			]
		);

		$this->add_control(
			'svg_blob_color_position_y1',
			[
				'label' => esc_html__('Position Y1', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'step' => 0.5,
			]
		);

		$this->add_control(
			'svg_blob_color_position_y2',
			[
				'label' => esc_html__('Position Y2', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'step' => 0.5,
			]
		);

		$this->end_controls_section();
	}
	protected function render_shape_generator() {
		$settings = $this->get_settings_for_display();
		$shapes = [
			'shape-01' => [
				'M392,337.5Q351,425,243.5,436.5Q136,448,68.5,349Q1,250,70.5,154.5Q140,59,255.5,49.5Q371,40,402,145Q433,250,392,337.5Z',
				'M387,325.5Q337,401,254.5,392.5Q172,384,126.5,317Q81,250,107.5,149.5Q134,49,246.5,55.5Q359,62,398,156Q437,250,387,325.5Z',
			],
			'shape-02' => [
				'M423,312.5Q350,375,276,418Q202,461,142.5,395.5Q83,330,54.5,236Q26,142,111.5,81Q197,20,274,71.5Q351,123,423.5,186.5Q496,250,423,312.5Z',
				'M412,333Q382,416,293,435Q204,454,155.5,386.5Q107,319,88.5,241Q70,163,144,137Q218,111,289,111.5Q360,112,401,181Q442,250,412,333Z',
			],
			'shape-03' => [
				'M441.5,296Q458,342,401,348.5Q344,355,335.5,421Q327,487,278.5,464Q230,441,199.5,415.5Q169,390,113,390Q57,390,49,342Q41,294,82.5,258.5Q124,223,94.5,169Q65,115,120.5,119Q176,123,202,82.5Q228,42,267,60.5Q306,79,359,75Q412,71,442,111Q472,151,448.5,200.5Q425,250,441.5,296Z',
				'M441.5,293.5Q445,337,415.5,369Q386,401,348.5,420Q311,439,269,453Q227,467,192.5,438Q158,409,122.5,389Q87,369,54.5,333.5Q22,298,70.5,260Q119,222,110.5,182.5Q102,143,120,99.5Q138,56,181,31.5Q224,7,266,39.5Q308,72,354.5,77Q401,82,397.5,134Q394,186,416,218Q438,250,441.5,293.5Z',
			],
			'shape-04' => [
				'M428,282.5Q439,315,428,347.5Q417,380,386,395.5Q355,411,326.5,424.5Q298,438,266.5,436.5Q235,435,203.5,431.5Q172,428,136.5,420Q101,412,76.5,384.5Q52,357,61,318.5Q70,280,76,251Q82,222,71,184.5Q60,147,87,124.5Q114,102,150.5,103.5Q187,105,210.5,81Q234,57,267,55.5Q300,54,333.5,62Q367,70,387.5,98.5Q408,127,404.5,162.5Q401,198,409,224Q417,250,428,282.5Z',
				'M436,282Q437,314,418,340Q399,366,367.5,374Q336,382,313,395.5Q290,409,260,449.5Q230,490,195.5,472Q161,454,153.5,408.5Q146,363,131,342.5Q116,322,85.5,302.5Q55,283,47,249Q39,215,49.5,181Q60,147,98.5,137Q137,127,156.5,104Q176,81,204.5,65Q233,49,268.5,43Q304,37,345,39Q386,41,378.5,98.5Q371,156,414,167.5Q457,179,446,214.5Q435,250,436,282Z',
			],
			'shape-05' => [
				'M471,283Q453,316,421.5,334Q390,352,366,364.5Q342,377,326.5,406.5Q311,436,280.5,426Q250,416,215,441Q180,466,162,431Q144,396,119,379.5Q94,363,97,331Q100,299,67.5,274.5Q35,250,59,223Q83,196,100.5,175Q118,154,112.5,103.5Q107,53,142.5,41.5Q178,30,214,30.5Q250,31,286.5,28.5Q323,26,339,65.5Q355,105,402,105Q449,105,447,146Q445,187,467,218.5Q489,250,471,283Z',
				'M455.5,281Q440,312,441,350.5Q442,389,416.5,416.5Q391,444,352.5,445.5Q314,447,282,443Q250,439,219.5,438Q189,437,149,440.5Q109,444,102,403.5Q95,363,86,334.5Q77,306,58,278Q39,250,64,224Q89,198,94,169Q99,140,112.5,109.5Q126,79,162,84Q198,89,224,89Q250,89,274,95.5Q298,102,320.5,112Q343,122,392.5,116Q442,110,443,148.5Q444,187,457.5,218.5Q471,250,455.5,281Z',
			],
			'shape-06' => [
				'M438,272.5Q387,295,387,322.5Q387,350,383.5,389.5Q380,429,335,400.5Q290,372,270,397.5Q250,423,227.5,405.5Q205,388,157.5,415.5Q110,443,118.5,391.5Q127,340,103.5,322.5Q80,305,102,277.5Q124,250,86,217Q48,184,82,168.5Q116,153,129,127.5Q142,102,172,102Q202,102,226,70.5Q250,39,279.5,53.5Q309,68,339.5,76.5Q370,85,371,123Q372,161,429.5,167Q487,173,488,211.5Q489,250,438,272.5Z',
				'M422.5,270Q372,290,375,316.5Q378,343,358.5,358Q339,373,323.5,400.5Q308,428,279,410Q250,392,221.5,409Q193,426,184.5,389Q176,352,161.5,338.5Q147,325,120.5,313Q94,301,109.5,275.5Q125,250,113,226Q101,202,89,163Q77,124,125,134Q173,144,174,81.5Q175,19,212.5,60.5Q250,102,270.5,113Q291,124,314.5,126Q338,128,345,152Q352,176,379.5,187.5Q407,199,440,224.5Q473,250,422.5,270Z',
			],
			'shape-07' => [
				'M422.5,270Q372,290,375,316.5Q378,343,358.5,358Q339,373,323.5,400.5Q308,428,279,410Q250,392,221.5,409Q193,426,184.5,389Q176,352,161.5,338.5Q147,325,120.5,313Q94,301,109.5,275.5Q125,250,113,226Q101,202,89,163Q77,124,125,134Q173,144,174,81.5Q175,19,212.5,60.5Q250,102,270.5,113Q291,124,314.5,126Q338,128,345,152Q352,176,379.5,187.5Q407,199,440,224.5Q473,250,422.5,270Z',
				'M363.5,269.5Q371,289,400.5,334.5Q430,380,386.5,379Q343,378,315,371.5Q287,365,268.5,368Q250,371,212.5,425.5Q175,480,182,407Q189,334,162,333.5Q135,333,89.5,325Q44,317,25,283.5Q6,250,67.5,230.5Q129,211,130.5,187.5Q132,164,139.5,136Q147,108,167.5,83.5Q188,59,219,63Q250,67,286.5,46.5Q323,26,319.5,92.5Q316,159,346.5,158.5Q377,158,431.5,165.5Q486,173,421,211.5Q356,250,363.5,269.5Z',
			],
			'shape-08' => [
				'M450,279Q429,308,392.5,317.5Q356,327,352.5,356.5Q349,386,327,402.5Q305,419,277.5,456.5Q250,494,228.5,438.5Q207,383,194.5,363Q182,343,168,331.5Q154,320,128.5,309Q103,298,64.5,274Q26,250,72,228.5Q118,207,125,185.5Q132,164,152,153.5Q172,143,173,80.5Q174,18,212,69Q250,120,272,117.5Q294,115,311.5,128Q329,141,344,156Q359,171,370,189Q381,207,426,228.5Q471,250,450,279Z',
				'M446.5,282.5Q449,315,395,315.5Q341,316,334,336Q327,356,324,412Q321,468,285.5,425.5Q250,383,225,393Q200,403,152,427Q104,451,129,385.5Q154,320,96.5,319Q39,318,39,284Q39,250,65.5,224.5Q92,199,101,174Q110,149,113,107.5Q116,66,151.5,61.5Q187,57,218.5,78.5Q250,100,278,89.5Q306,79,344.5,73Q383,67,383,110Q383,153,417,169Q451,185,447.5,217.5Q444,250,446.5,282.5Z',
			],
			'shape-09' => [
				'M416,270Q374,290,372,313.5Q370,337,334,326.5Q298,316,290,332Q282,348,266,419Q250,490,231,428Q212,366,177,382.5Q142,399,98,396Q54,393,72,347.5Q90,302,97.5,276Q105,250,77,217.5Q49,185,54,148Q59,111,113.5,124.5Q168,138,196,154Q224,170,237,110Q250,50,264.5,105Q279,160,331,113.5Q383,67,364.5,124Q346,181,367.5,193Q389,205,423.5,227.5Q458,250,416,270Z',
				'M345.5,268.5Q363,287,386,326Q409,365,368.5,361Q328,357,327,420Q326,483,288,487.5Q250,492,212.5,486.5Q175,481,185,403Q195,325,127,357Q59,389,79.5,344Q100,299,117.5,274.5Q135,250,92.5,217.5Q50,185,101,182Q152,179,175.5,179Q199,179,212.5,177.5Q226,176,238,107Q250,38,282,45Q314,52,344,65.5Q374,79,382,113.5Q390,148,414.5,168.5Q439,189,383.5,219.5Q328,250,345.5,268.5Z',
			],
			'shape-10' => [
				'M430.5,283.5Q455,317,409.5,325Q364,333,356,359Q348,385,316.5,371.5Q285,358,267.5,422.5Q250,487,213,482.5Q176,478,197.5,385.5Q219,293,148,334.5Q77,376,136,322Q195,268,192.5,259Q190,250,138,223.5Q86,197,120,188.5Q154,180,133,120Q112,60,164,102.5Q216,145,233,136.5Q250,128,260.5,157.5Q271,187,324.5,130Q378,73,363,125.5Q348,178,411,177.5Q474,177,440,213.5Q406,250,430.5,283.5Z',
				'M350.5,273.5Q394,297,403,332.5Q412,368,385.5,384Q359,400,335,419Q311,438,280.5,372Q250,306,242,302.5Q234,299,173.5,368.5Q113,438,96.5,406Q80,374,128.5,324Q177,274,136,262Q95,250,123,234Q151,218,152.5,199.5Q154,181,133.5,121.5Q113,62,149,55Q185,48,217.5,95Q250,142,282.5,95.5Q315,49,332.5,80.5Q350,112,351.5,143.5Q353,175,327.5,204Q302,233,304.5,241.5Q307,250,350.5,273.5Z',
			],
		];
		return [
			'first' => array_shift($shapes[$settings['svg_blob_shape_style']]),
			'lasts' => json_encode($shapes[$settings['svg_blob_shape_style']]),
		];
	}

	public function render_image($paths) {
		$settings = $this->get_settings_for_display();
		$id = $this->get_id();
		$image = Group_Control_Image_Size::get_attachment_image_src($settings['svg_blob_image']['id'], 'svg_blob_image_size', $settings);
		$image_url = !empty($image) ? $image : $settings['svg_blob_image']['url'];
?>
		<clipPath id="blob-shape-<?php echo esc_attr($id); ?>">
			<path d="<?php echo esc_attr($paths['first']); ?>" />
		</clipPath>
		<g clip-path="url(#blob-shape-<?php echo esc_attr($id); ?>)">
			<image id="<?php echo esc_attr($settings['svg_blob_image']['id']) ?>" class="svg-blob-image" href="<?php echo esc_url($image_url); ?>" preserveAspectRatio="none"></image>
		</g>
	<?php
	}
	public function render_gradient($paths) {
		$settings = $this->get_settings_for_display();
		$id = $this->get_id();

		$color_position = [
			'position_x' => [
				(!empty($settings['svg_blob_color_position_x1']['size'])) ? $settings['svg_blob_color_position_x1']['size'] . '%' : "0%",
				(!empty($settings['svg_blob_color_position_x2']['size'])) ? $settings['svg_blob_color_position_x2']['size'] . '%' : "100%",
			],
			'position_y' => [
				(!empty($settings['svg_blob_color_position_y1']['size'])) ? $settings['svg_blob_color_position_y1']['size'] . '%' : "70.711%",
				(!empty($settings['svg_blob_color_position_y2']['size'])) ? $settings['svg_blob_color_position_y2']['size'] . '%' : "100%",
			],
		];
		$gradient_offset = [
			'offset' => [
				(!empty($settings['svg_blob_offset_primary']['size'])) ? $settings['svg_blob_offset_primary']['size'] . '%' : "0%",
				(!empty($settings['svg_blob_offset_secondary']['size'])) ? $settings['svg_blob_offset_secondary']['size'] . '%' : "100%",
			],
			'stop_color' => [
				(!empty($settings['svg_blob_gradient_color_primary'])) ? $settings['svg_blob_gradient_color_primary'] : "#5F3698",
				(!empty($settings['svg_blob_gradient_color_secondary'])) ? $settings['svg_blob_gradient_color_secondary'] : "#DC638D",
			],
		];

		$this->add_render_attribute('gradient', [
			'id' => 'shape-gradient-color-' . $id,
			'x1' => $color_position['position_x'][0],
			'x2' => $color_position['position_x'][1],
			'y1' => $color_position['position_y'][0],
			'y2' => $color_position['position_y'][1],
		]);
	?>
		<linearGradient <?php $this->print_render_attribute_string('gradient'); ?>>
			<stop offset="<?php echo esc_attr($gradient_offset['offset'][0]); ?>" stop-color="<?php echo esc_attr($gradient_offset['stop_color'][0]); ?>" stop-opacity="1"></stop>
			<stop offset="<?php echo esc_attr($gradient_offset['offset'][1]); ?>" stop-color="<?php echo esc_attr($gradient_offset['stop_color'][1]); ?>" stop-opacity="1"></stop>
		</linearGradient>
		<path fill="url(#shape-gradient-color-<?php echo esc_attr($id); ?>)" d="<?php echo esc_attr($paths['first']); ?>" />
	<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$paths =  $this->render_shape_generator();
		$this->add_render_attribute('svg-blob', [
			'class' => ['bdt-svg-blob'],
			'data-settings' => $paths['lasts'],
		]); ?>
		<div <?php $this->print_render_attribute_string('svg-blob'); ?>>
			<svg viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
				<?php if ('images' === $settings['svg_blob_select_type']) : ?>
					<?php $this->render_image($paths); ?>
				<?php elseif ('color' === $settings['svg_blob_select_type']) : ?>
					<path fill="#8C24FF" d="<?php echo esc_attr($paths['first']); ?>">
					<?php elseif ('gradient' === $settings['svg_blob_select_type']) : ?>
						<?php $this->render_gradient($paths); ?>
					<?php endif; ?>
			</svg>
		</div>
<?php
	}
}

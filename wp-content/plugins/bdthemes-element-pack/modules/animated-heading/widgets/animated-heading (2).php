<?php

namespace ElementPack\Modules\AnimatedHeading\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use ElementPack\Utils;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class AnimatedHeading extends Module_Base {

	protected $_has_template_content = false;

	public function get_name() {
		return 'bdt-animated-heading';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Animated Heading', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-animated-heading';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'animated', 'heading', 'headline', 'split', 'gsap', 'vivid' ];
	}

	public function get_style_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'ep-styles' ];
		} else {
			return [ 'ep-animated-heading' ];
		}
	}

	public function get_script_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'morphext', 'typed', 'gsap', 'split-text-js', 'ep-scripts' ];
		} else {
			return [ 'morphext', 'typed', 'gsap', 'split-text-js', 'ep-animated-heading' ];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/xypAmQodUYA';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_heading',
			[ 
				'label' => esc_html__( 'Heading', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'heading_layout',
			[ 
				'label'   => esc_html__( 'Layout', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [ 
					'animated'   => esc_html__( 'Animated', 'bdthemes-element-pack' ),
					'typed'      => esc_html__( 'Typed', 'bdthemes-element-pack' ),
					'split_text' => esc_html__( 'Split Text', 'bdthemes-element-pack' ),
				],
				'default' => 'animated',
			]
		);

		$this->add_control(
			'pre_heading',
			[ 
				'label'       => esc_html__( 'Prefix Heading', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter your prefix title', 'bdthemes-element-pack' ),
				'default'     => esc_html__( 'Hello I am', 'bdthemes-element-pack' ),
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'animated_heading',
			[ 
				'label'       => esc_html__( 'Heading', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter your title', 'bdthemes-element-pack' ),
				'description' => esc_html__( 'Write animated heading here with comma separated. Such as Animated, Morphing, Awesome', 'bdthemes-element-pack' ),
				'default'     => esc_html__( "Animated,Morphing,Awesome", 'bdthemes-element-pack' ),
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'post_heading',
			[ 
				'label'       => esc_html__( 'Post Heading', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter your suffix title', 'bdthemes-element-pack' ),
				'default'     => esc_html__( 'Heading', 'bdthemes-element-pack' ),
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'link',
			[ 
				'label'       => esc_html__( 'Link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'http://your-link.com',
			]
		);

		$this->add_control(
			'header_size',
			[ 
				'label'   => esc_html__( 'HTML Tag', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_title_tags(),
				'default' => 'h2',
			]
		);

		$this->add_responsive_control(
			'align',
			[ 
				'label'     => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [ 
					'left'   => [ 
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [ 
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [ 
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				// 'prefix_class' => 'elementor-align%s-',
				'selectors' => [ 
					'{{WRAPPER}}.elementor-widget-bdt-animated-heading' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'spilt_options',
			[ 
				'label'     => esc_html__( 'Spilt Text', 'bdthemes-element-pack' ),
				'condition' => [ 
					'heading_layout' => [ 'split_text' ],
				]
			]
		);


		$this->add_control(
			'animation_on',
			[ 
				'label'   => esc_html__( 'Animation On', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'words',
				'options' => [ 
					'chars' => 'Chars',
					'words' => 'Words',
					'lines' => 'Lines',
				],
			]
		);

		$this->add_control(
			'animation_options',
			[ 
				'label'        => esc_html__( 'Animation Options', 'bdthemes-element-pack' ),
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => esc_html__( 'Default', 'bdthemes-element-pack' ),
				'label_on'     => esc_html__( 'Custom', 'bdthemes-element-pack' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->start_popover();

		$this->add_control(
			'anim_perspective',
			[ 
				'label'       => esc_html__( 'Perspective', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SLIDER,
				'placeholder' => '400',
				'range'       => [ 
					'px' => [ 
						'min' => 50,
						'max' => 400,
					],
				],
			]
		);

		$this->add_control(
			'anim_duration',
			[ 
				'label' => esc_html__( 'Transition Duration', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [ 
					'px' => [ 
						'min'  => 0.1,
						'step' => 0.1,
						'max'  => 1,
					],
				],
			]
		);

		$this->add_control(
			'anim_scale',
			[ 
				'label' => esc_html__( 'Scale', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [ 
					'px' => [ 
						'min' => 1,
						'max' => 10,
					],
				],
			]
		);

		$this->add_control(
			'anim_rotationY',
			[ 
				'label' => esc_html__( 'rotationY', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [ 
					'px' => [ 
						'min' => -360,
						'max' => 360,
					],
				],
			]
		);

		$this->add_control(
			'anim_rotationX',
			[ 
				'label' => esc_html__( 'rotationX', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [ 
					'px' => [ 
						'min' => -360,
						'max' => 360,
					],
				],
			]
		);

		$this->add_control(
			'anim_transform_origin',
			[ 
				'label'   => esc_html__( 'Transform Origin', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '0% 50% -50',
			]
		);


		$this->end_popover();

		$this->add_control(
			'spilt_anim_repeat',
			[ 
				'label'   => esc_html__( 'Animation Repeat', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_animation',
			[ 
				'label'       => esc_html__( 'Animation', 'bdthemes-element-pack' ),
				'tab'         => Controls_Manager::TAB_STYLE,
				'condition'   => [ 
					'heading_layout!' => [ 'split_text' ],
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'heading_animation',
			[ 
				'label'       => esc_html__( 'Animation', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ANIMATION,
				'default'     => 'fadeIn',
				'label_block' => true,
				'condition'   => [ 
					'heading_animation!' => '',
					'heading_layout'     => 'animated',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'heading_animation_duration',
			[ 
				'label'     => esc_html__( 'Animation Duration', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [ 
					''     => esc_html__( 'Normal', 'bdthemes-element-pack' ),
					'slow' => esc_html__( 'Slow', 'bdthemes-element-pack' ),
					'fast' => esc_html__( 'Fast', 'bdthemes-element-pack' ),
				],
				'condition' => [ 
					'heading_animation!' => '',
					'heading_layout'     => 'animated',
				],
			]
		);

		$this->add_control(
			'heading_animation_delay',
			[ 
				'label'     => esc_html__( 'Animation Delay', 'bdthemes-element-pack' ) . ' (ms)',
				'type'      => Controls_Manager::NUMBER,
				'default'   => 2500,
				'min'       => 100,
				'max'       => 7000,
				'step'      => 100,
				'condition' => [ 
					'heading_animation!' => '',
					'heading_layout'     => 'animated',
				],
			]
		);

		$this->add_control(
			'type_speed',
			[ 
				'label'     => esc_html__( 'Type Speed', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 60,
				'min'       => 10,
				'max'       => 100,
				'step'      => 5,
				'condition' => [ 
					'heading_animation!' => '',
					'heading_layout'     => 'typed',
				],
			]
		);

		$this->add_control(
			'start_delay',
			[ 
				'label'     => esc_html__( 'Start Delay', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 100,
				'step'      => 1,
				'condition' => [ 
					'heading_animation!' => '',
					'heading_layout'     => 'typed',
				],
			]
		);

		$this->add_control(
			'back_speed',
			[ 
				'label'     => esc_html__( 'Back Speed', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 30,
				'min'       => 0,
				'max'       => 100,
				'step'      => 2,
				'condition' => [ 
					'heading_animation!' => '',
					'heading_layout'     => 'typed',
				],
			]
		);

		$this->add_control(
			'back_delay',
			[ 
				'label'     => esc_html__( 'Back Delay', 'bdthemes-element-pack' ) . ' (ms)',
				'type'      => Controls_Manager::NUMBER,
				'default'   => 500,
				'min'       => 0,
				'max'       => 3000,
				'step'      => 50,
				'condition' => [ 
					'heading_animation!' => '',
					'heading_layout'     => 'typed',
				],
			]
		);

		$this->add_control(
			'loop',
			[ 
				'label'     => esc_html__( 'Loop', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [ 
					'heading_animation!' => '',
					'heading_layout'     => 'typed',
				],
			]
		);

		$this->add_control(
			'loop_count',
			[ 
				'label'     => esc_html__( 'Loop Count', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'min'       => 0,
				'condition' => [ 
					'loop'           => 'yes',
					'heading_layout' => 'typed',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_animated_heading',
			[ 
				'label' => esc_html__( 'Heading', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// $this->add_control(
		// 	'show_text_stroke',
		// 	[
		// 		'label'   => esc_html__('Text Stroke', 'bdthemes-element-pack') . BDTEP_NC,
		// 		'type'    => Controls_Manager::SWITCHER,
		// 		'prefix_class' => 'bdt-text-stroke--',
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'text_stroke_width',
		// 	[
		// 		'label' => esc_html__('Text Stroke Width', 'bdthemes-element-pack') . BDTEP_NC,
		// 		'type'  => Controls_Manager::SLIDER,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-heading .bdt-heading-tag *' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}};',
		// 		],
		// 		'condition' => [
		// 			'show_text_stroke' => 'yes'
		// 		]
		// 	]
		// );

		$this->add_control(
			'animated_heading_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-heading .bdt-heading-tag *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'animated_heading_typography',
				'selector' => '{{WRAPPER}} .bdt-heading .bdt-heading-tag',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[ 
				'name'     => 'animated_heading_text_stroke',
				'label'    => esc_html__( 'Text Stroke', 'bdthemes-element-pack' ) . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-heading .bdt-heading-tag *',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'animated_heading_shadow',
				'selector' => '{{WRAPPER}} .bdt-heading .bdt-heading-tag',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pre_heading',
			[ 
				'label'     => esc_html__( 'Pre Heading', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'pre_heading!' => '',
				]
			]
		);

		$this->add_control(
			'pre_heading_color',
			[ 
				'label'     => esc_html__( 'Pre Heading Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-heading .bdt-pre-heading' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'pre_heading_typography',
				'selector' => '{{WRAPPER}} .bdt-heading .bdt-pre-heading',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[ 
				'name'     => 'pre_heading_text_stroke',
				'label'    => esc_html__( 'Text Stroke', 'bdthemes-element-pack' ) . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-heading .bdt-pre-heading',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'pre_heading_shadow',
				'selector' => '{{WRAPPER}} .bdt-heading .bdt-pre-heading',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_post_heading',
			[ 
				'label'     => esc_html__( 'Post Heading', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'post_heading!' => '',
				]
			]
		);

		$this->add_control(
			'post_heading_color',
			[ 
				'label'     => esc_html__( 'Post Heading Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-heading .bdt-post-heading' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'post_heading_typography',
				'selector' => '{{WRAPPER}} .bdt-heading .bdt-post-heading',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[ 
				'name'     => 'post_heading_text_stroke',
				'label'    => esc_html__( 'Text Stroke', 'bdthemes-element-pack' ) . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-heading .bdt-post-heading',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'post_heading_shadow',
				'selector' => '{{WRAPPER}} .bdt-heading .bdt-post-heading',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$id            = $this->get_id();
		$final_heading = '';
		$heading_html  = [];
		$type_heading  = explode( ",", esc_html( $settings['animated_heading'] ) );

		if ( empty( $settings['pre_heading'] ) and empty( $settings['animated_heading'] ) and empty( $settings['post_heading'] ) ) {
			return;
		}

		$this->add_render_attribute( 'heading', 'class', 'bdt-heading-tag' );
		$this->add_render_attribute( 'heading', 'style', 'opacity: 0;' );

		$this->add_render_attribute( 'animated-heading', 'id', 'bdt-ah-' . $id );
		$this->add_render_attribute( 'animated-heading', 'class', 'bdt-animated-heading' );

		if ( 'animated' == $settings['heading_layout'] ) {
			if ( $settings['heading_animation_duration'] ) {
				$this->add_render_attribute( 'animated-heading', 'class', ' bdt-animated-' . $settings['heading_animation_duration'] );
			}
			$this->add_render_attribute(
				[ 
					'animated-heading' => [ 
						'data-settings' => [ 
							wp_json_encode( array_filter( [ 
								'layout'    => $settings['heading_layout'],
								'animation' => $settings['heading_animation'],
								'speed'     => $settings['heading_animation_delay'],
							] ) )
						]
					]
				]
			);
		} elseif ( 'typed' == $settings['heading_layout'] ) {
			$this->add_render_attribute(
				[ 
					'animated-heading' => [ 
						'data-settings' => [ 
							wp_json_encode( array_filter( [ 
								'layout'     => $settings['heading_layout'],
								'strings'    => $type_heading,
								'typeSpeed'  => $settings['type_speed'],
								'startDelay' => $settings['start_delay'],
								'backSpeed'  => $settings['back_speed'],
								'backDelay'  => $settings['back_delay'],
								'loop'       => ( $settings['loop'] ) ? true : false,
								'loopCount'  => ( $settings['loop_count'] ) ? $settings['loop_count'] : '0',
							] ) )
						]
					]
				]
			);
		} elseif ( 'split_text' == $settings['heading_layout'] ) {
			$this->add_render_attribute(
				[ 
					'animated-heading' => [ 
						'data-settings' => [ 
							wp_json_encode( [ 
								'layout'                => $settings['heading_layout'],
								'animation_on'          => $settings['animation_on'],
								'anim_perspective'      => ( $settings['anim_perspective']['size'] ) ? $settings['anim_perspective']['size'] : 400,
								'anim_duration'         => ( $settings['anim_duration']['size'] ) ? $settings['anim_duration']['size'] : 0.1,
								'anim_scale'            => ( $settings['anim_scale']['size'] ) ? $settings['anim_scale']['size'] : 0,
								'anim_rotation_y'       => ( $settings['anim_rotationY']['size'] ) ? $settings['anim_rotationY']['size'] : 80,
								'anim_rotation_x'       => ( $settings['anim_rotationX']['size'] ) ? $settings['anim_rotationX']['size'] : 180,
								'anim_transform_origin' => ( $settings['anim_transform_origin'] ) ? $settings['anim_transform_origin'] : '0% 50% -50',
								'anim_repeat'           => ( ! empty( $settings['spilt_anim_repeat'] ) ) ? false : true,
							] )
						]
					]
				]
			);
		}



		if ( $settings['pre_heading'] ) {
			$final_heading .= '<span class="bdt-pre-heading">' . esc_attr( $settings['pre_heading'] ) . '</span> ';
		}

		$final_heading .= '<span ' . $this->get_render_attribute_string( 'animated-heading' ) . '>';

		if ( $settings['animated_heading'] and 'animated' == $settings['heading_layout'] ) {
			$final_heading .= rtrim( esc_attr( $settings['animated_heading'] ), ',' );
		}

		if ( $settings['animated_heading'] and $settings['heading_layout'] == 'split_text' ) {
			$final_heading .= rtrim( esc_attr( $settings['animated_heading'] ), ',' );
		}

		$final_heading .= '</span> ';

		if ( $settings['post_heading'] ) {
			$final_heading .= '<span class="bdt-post-heading">' . esc_attr( $settings['post_heading'] ) . '</span>';
		}


		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'url', 'href', $settings['link']['url'] );

			if ( $settings['link']['is_external'] ) {
				$this->add_render_attribute( 'url', 'target', '_blank' );
			}

			if ( ! empty( $settings['link']['nofollow'] ) ) {
				$this->add_render_attribute( 'url', 'rel', 'nofollow' );
			}

			$final_heading = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'url' ), $final_heading );
		}

		$heading_html[] = '<div id ="bdtah-' . $id . '" class="bdt-heading">';


		$heading_html[] = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::get_valid_html_tag( $settings['header_size'] ), $this->get_render_attribute_string( 'heading' ), $final_heading );

		$heading_html[] = '</div>';

		echo implode( "", $heading_html );
	}
}

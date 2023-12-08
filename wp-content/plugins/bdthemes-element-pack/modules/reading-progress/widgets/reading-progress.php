<?php

namespace ElementPack\Modules\ReadingProgress\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use ElementPack\Base\Module_Base;

use ElementPack\Modules\ReadingProgress\Skins;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Exit if accessed directly

class Reading_Progress extends Module_Base {

	public function get_name() {
		return 'bdt-reading-progress';
	}

	public function get_id() {
		return 'bdt-reading-progress';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Reading Progress', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-reading-progress';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'read', 'reading', 'progress', 'scroll' ];
	}

	public function get_style_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'elementor-icons-fa-solid', 'ep-styles' ];
		} else {
			return [ 'elementor-icons-fa-solid', 'ep-reading-progress' ];
		}
	}

	public function get_script_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'progressHorizontal', 'ep-scripts' ];
		} else {
			return [ 'progressHorizontal', 'ep-reading-progress' ];
		}
	}


	public function register_skins() {
		$this->add_skin( new Skins\Back_To_Top_With_Progress( $this ) );
		$this->add_skin( new Skins\Horizontal_Progress( $this ) );
		$this->add_skin( new Skins\Progress_With_Cursor( $this ) );
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/cODL1E2f9FI';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'reading_progress_layout',
			[ 
				'label' => esc_html__( 'Reading Progress', 'bdthemes-dark-mode' ),
			]
		);

		$this->add_control(
			'progress_position',
			[ 
				'label'     => __( 'Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom-right',
				'options'   => [ 
					'bottom-right' => __( 'Bottom Right', 'bdthemes-element-pack' ),
					'bottom-left'  => __( 'Bottom Left', 'bdthemes-element-pack' ),
					'top-right'    => __( 'Top Right', 'bdthemes-element-pack' ),
					'top-left'     => __( 'Top Left', 'bdthemes-element-pack' ),
				],
				'condition' => [ 
					'_skin' => [ '', 'bdt-back-to-top-with-progress' ],
				],

			]
		);

		$this->add_responsive_control(
			'reading_progress_font_area_size',
			[ 
				'label'     => __( 'Primary Circle Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 80,
				],
				'range'     => [ 
					'px' => [ 
						'min' => 40,
						'max' => 140,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-reading-progress .bdt-reading-progress-circle' => 'height: {{SIZE}}{{UNIT}} !important; width: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [ 
					'_skin' => '',
				],
			]
		);

		$this->add_responsive_control(
			'reading_progress_size',
			[ 
				'label'     => __( 'Secondary Circle Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 90,
				],
				'range'     => [ 
					'px' => [ 
						'min' => 50,
						'max' => 150,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-reading-progress '                                                                                     => 'height: {{SIZE}}{{UNIT}} !important; width: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .bdt-progress-with-top .bdt-progress-wrap, {{WRAPPER}}  .bdt-progress-with-top .bdt-progress-wrap::before ' => 'height: {{SIZE}}{{UNIT}} ; width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-progress-with-top .bdt-progress-wrap::before '                                                         => 'line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}  .bdt-progress-with-cursor .bdt-progress-wrap'                                                              => 'height: {{SIZE}}{{UNIT}} !important; width: {{SIZE}}{{UNIT}}  !important; ',
					'{{WRAPPER}}  .bdt-progress-with-cursor .bdt-cursor2, .bdt-progress-with-cursor .bdt-cursor3'                            => 'height: {{SIZE}}{{UNIT}} !important; width: {{SIZE}}{{UNIT}}  !important; ',
				],
				'condition' => [ 
					'_skin' => [ '', 'bdt-back-to-top-with-progress', 'bdt-progress-with-cursor' ],
				],
			]
		);

		$this->add_responsive_control(
			'reading_progress_horizontal_offset',
			[ 
				'label'     => __( 'Horizontal Offset', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => -150,
						'max' => 150,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-reading-progress '                    => 'transform: translateX({{SIZE}}{{UNIT}});',
					'{{WRAPPER}} .bdt-progress-with-top .bdt-progress-wrap' => 'transform: translateX({{SIZE}}{{UNIT}});',
				],
				'condition' => [ 
					'_skin' => [ '', 'bdt-back-to-top-with-progress' ],
				],
			]
		);

		$this->add_responsive_control(
			'reading_progress_vertical_offset',
			[ 
				'label'     => __( 'Vertical Offset', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => -150,
						'max' => 150,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-reading-progress'                     => 'margin: {{SIZE}}{{UNIT}} 0px;',
					'{{WRAPPER}} .bdt-progress-with-top .bdt-progress-wrap' => 'margin: {{SIZE}}{{UNIT}} 0px;',

				],
				'condition' => [ 
					'_skin' => [ '', 'bdt-back-to-top-with-progress' ],
				],
			]
		);

		$this->add_control(
			'horizontal_reading_progress_position',
			[ 
				'label'     => __( 'Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top',
				'options'   => [ 
					'top'    => __( 'Top ', 'bdthemes-element-pack' ),
					'bottom' => __( 'Bottom', 'bdthemes-element-pack' ),
				],
				'condition' => [ 
					'_skin' => 'bdt-horizontal-progress',
				],
			]
		);

		$this->add_responsive_control(
			'horizontal_reading_progress_size',
			[ 
				'label'      => __( 'Height', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 
					'px' => [ 
						'min' => 1,
						'max' => 10,
					],
				],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-horizontal-progress' => 'height: {{SIZE}}{{UNIT}}  !important;',
				],
				'condition'  => [ 
					'_skin' => 'bdt-horizontal-progress',
				],
			]
		);

		$this->end_controls_section();


		//Style
		$this->start_controls_section(
			'reading_progress_style_default',
			[ 
				'label' => __( 'Additional', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'reading_progress_value_color',
			[ 
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}}  .bdt-reading-progress .bdt-reading-progress-border .bdt-reading-progress-circle .bdt-reading-progress-text' => 'color: {{VALUE}}',
					'{{WRAPPER}}  .bdt-progress-with-top .bdt-progress-wrap::before'                                                          => 'background-color: {{VALUE}}',
				],
				'condition' => [ 
					'_skin' => [ '', 'bdt-back-to-top-with-progress' ],
				],
			]
		);



		$this->add_control(
			'reading_progress_bg',
			[ 
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#08AEEC',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-horizontal-progress' => 'background-color: {{VALUE}}  !important',
				],
				'condition' => [ 
					'_skin' => [ '', 'bdt-horizontal-progress' ],
				],
			]
		);

		$this->add_control(
			'reading_progress_font_area_bg',
			[ 
				'label'     => __( 'Secondary Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#54595F',
				'selectors' => [ 
					'{{WRAPPER}}  .bdt-reading-progress .bdt-reading-progress-border .bdt-reading-progress-circle ' => 'background-color: {{VALUE}}',
				],
				'condition' => [ 
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'reading_progress_bg_scroll',
			[ 
				'label'     => __( 'Active Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FF0000',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-horizontal-progress .inner'                                           => 'background-color: {{VALUE}}  !important',
					'{{WRAPPER}} .bdt-progress-with-top .bdt-progress-wrap svg.bdt-progress-circle path'    => 'stroke: {{VALUE}}',
					'{{WRAPPER}} .bdt-progress-with-cursor .bdt-progress-wrap svg.bdt-progress-circle path' => 'stroke: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'      => 'reading_progress_border',
				'label'     => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-reading-progress .bdt-reading-progress-circle',
				'condition' => [ 
					'_skin' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'      => 'cursor_with_progress_reading_bg',
				'selector'  => '{{WRAPPER}} .bdt-progress-with-cursor .bdt-progress-wrap,
                               {{WRAPPER}}  .bdt-progress-with-top .bdt-progress-wrap',
				'condition' => [ 
					'_skin' => [ 'bdt-back-to-top-with-progress', 'bdt-progress-with-cursor' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'      => 'reading_progress_value',
				'selector'  => '{{WRAPPER}} .bdt-reading-progress .bdt-reading-progress-border .bdt-reading-progress-circle .bdt-reading-progress-text, {{WRAPPER}} .bdt-progress-with-top .bdt-progress-wrap::before',
				'condition' => [ 
					'_skin' => [ '', 'bdt-back-to-top-with-progress' ],
				],
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		$position = $settings['progress_position'];

		$this->add_render_attribute( 'reading-progress', 'class', 'bdt-reading-progress' );
		$this->add_render_attribute( 'reading-progress', 'class', $position );

		$this->add_render_attribute(
			[ 
				'reading-progress' => [ 
					'data-settings' => [ 
						wp_json_encode( array_filter( [ 
							"progress_bg" => $settings['reading_progress_bg'],
							"scroll_bg"   => $settings['reading_progress_bg_scroll'],
						] ) ),
					],
				],
			]
		);

		?>

		<div <?php echo $this->get_render_attribute_string( 'reading-progress' ); ?>></div>

		<?php
	}
}

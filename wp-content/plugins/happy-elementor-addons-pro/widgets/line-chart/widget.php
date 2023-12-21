<?php
/**
 * Chart widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Happy_Addons_Pro\Widget\Line_Chart\Data_Map;

defined( 'ABSPATH' ) || die();

class Line_Chart extends Base {

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Line Chart', 'happy-addons-pro' );
	}

//	public function get_custom_help_url() {
//		return '';
//	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-line-graph-pointed';
	}

	public function get_keywords() {
		return [ 'chart', 'line', 'statistic' ];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__chart_content_controls();
		$this->__settings_content_controls();
	}

	protected function __chart_content_controls() {

		$this->start_controls_section(
			'_section_chart',
			[
				'label' => __( 'Line Chart', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'labels',
			[
				'label'       => __( 'Labels', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'January, February, March, April, May, June', 'happy-addons-pro' ),
				'description' => __( 'Write multiple label with comma ( , ) separator. Example: January, February, March etc', 'happy-addons-pro' ),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'line_tabs' );

		$repeater->start_controls_tab(
			'bar_tab_content',
			[
				'label' => __( 'Content', 'happy-addons-pro' ),
			]
		);

		$repeater->add_control(
			'label',
			[
				'label'   => __( 'Label', 'happy-addons-pro' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'data',
			[
				'label'       => __( 'Data', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Write data values with comma ( , ) separator. Example: 4, 2, 6', 'happy-addons-pro' ),
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'line_tab_style',
			[
				'label' => __( 'Style', 'happy-addons-pro' ),
			]
		);

		$repeater->add_responsive_control(
			'dash_border',
			[
				'label'       => __( 'Dash Border', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'For Dash Border add two number with comma separator. Example: 5, 4', 'happy-addons-pro' ),
			]
		);

		$repeater->add_control(
			'background_fill',
			[
				'label'        => __( 'Background Fill', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type'  => Controls_Manager::COLOR,
				'condition' => [
					'background_fill' => 'yes',
				]
			]
		);

		$repeater->add_control(
			'border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type'  => Controls_Manager::COLOR,
			]
		);

		$repeater->add_control(
			'point_color',
			[
				'label' => __( 'Point Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
			]
		);

		$repeater->end_controls_tab();

		$this->add_control(
			'chart_data',
			[
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => [
					[
						'label'              => __( 'Happy Addons', 'happy-addons-pro' ),
						'data'               => __( '0, 22, 4, 25, 8, 5', 'happy-addons-pro' ),
						'border_color'       => '#2E93DC',
						'point_color'       => '#2E93DC',
						'background_color'  => 'rgba(46, 147, 220, 0.12)'
					],
					[
						'label'              => __( 'Happy Addons', 'happy-addons-pro' ),
						'data'               => __( '17, 7, 58, 4, 24, 5', 'happy-addons-pro' ),
						'border_color'       => '#5A37CF',
						'point_color'       => '#5A37CF',
						'dash_border'		=> __( '5, 4', 'happy-addons-pro' ),
						'background_color'  => 'rgba(90, 55, 207, 0.1)'
					]
				]
			]
		);

		$this->end_controls_section();
	}

	protected function __settings_content_controls() {

		$this->start_controls_section(
			'settings',
			[
				'label' => __( 'Settings', 'happy-addons-pro' ),
			]
		);

		$this->add_responsive_control(
			'chart_height',
			[
				'label'       => __( 'Chart Height', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min' => 50,
						'max' => 1500,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 400,
				],
				'selectors'   => [
					'{{WRAPPER}} .ha-line-chart-container' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'xaxes_grid_display',
			[
				'label'        => __( 'X Axes Grid Lines', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'yaxes_grid_display',
			[
				'label'        => __( 'Y Axes Grid Lines', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'xaxes_labels_display',
			[
				'label'        => __( 'Show X Axes Labels', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'yaxes_labels_display',
			[
				'label'        => __( 'Show Y Axes Labels', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'tooltip_display',
			[
				'label'        => __( 'Show Tooltips', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'title_display',
			[
				'label'        => __( 'Show Title', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'chart_title',
			[
				'label'       => __( 'Title', 'happy-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Happy Addons Rocks', 'happy-addons-pro' ),
				'condition' => [
					'title_display' => 'yes'
				]
			]
		);

		$this->add_control(
			'axis_range',
			[
				'label'       => __( 'Scale Axis Range', 'happy-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 60,
				'description' => __( 'Maximum number for the scale.', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'step_size',
			[
				'label'       => __( 'Step Size', 'happy-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 20,
				'step'        => 1,
				'description' => __( 'Step size for the scale.', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'legend_heading',
			[
				'label'     => __( 'Legend', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'legend_display',
			[
				'label'        => __( 'Show Legend', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'legend_position',
			[
				'label'     => __( 'Position', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top',
				'options'   => [
					'top'    => __( 'Top', 'happy-addons-pro' ),
					'left'   => __( 'Left', 'happy-addons-pro' ),
					'bottom' => __( 'Bottom', 'happy-addons-pro' ),
					'right'  => __( 'Right', 'happy-addons-pro' ),
				],
				'condition' => [
					'legend_display' => 'yes',
				],
			]
		);

		$this->add_control(
			'legend_reverse',
			[
				'label'        => __( 'Reverse', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'return_value' => 'yes',
				'condition'    => [
					'legend_display'  => 'yes',
				],
			]
		);

		$this->add_control(
			'animation_heading',
			[
				'label'     => __( 'Animation', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'chart_animation_duration',
			[
				'label' => __( 'Duration', 'happy-addons-pro' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 10000,
				'step' => 1,
				'default' => 1000,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'animation_options',
			[
				'label'     => __( 'Easing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'linear',
				'options'   => [
					'linear'    => __( 'Linear', 'happy-addons-pro' ),
					'easeInCubic'   => __( 'Ease In Cubic', 'happy-addons-pro' ),
					'easeInCirc' => __( 'Ease In Circ', 'happy-addons-pro' ),
					'easeInBounce' => __( 'Ease In Bounce', 'happy-addons-pro' ),
				]
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__common_style_controls();
		$this->__legend_style_controls();
		$this->__labels_style_controls();
		$this->__tooltip_style_controls();
	}

	protected function __common_style_controls() {

		$this->start_controls_section(
			'_section_style_common',
			[
				'label' => __( 'Common', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'layout_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
			]
		);

		$this->add_control(
			'bar_border_width',
			[
				'label' => __( 'Border Width', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
			]
		);

		$this->add_control(
			'line_tension',
			[
				'label' => __( 'Line Tension', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
			]
		);

		$this->add_control(
			'point_border_width',
			[
				'label' => __( 'Point Border Width', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
			]
		);

		$this->add_control(
			'point_style',
			[
				'label'   => __( 'Point Style', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => [
					'circle' => __( 'Circle', 'happy-addons-pro' ),
					'react' => __( 'React', 'happy-addons-pro' ),
					'rectRot' => __( 'Rect Rot', 'happy-addons-pro' ),
					'triangle' => __( 'Triangle', 'happy-addons-pro' ),
					'cross' => __( 'Cross', 'happy-addons-pro' ),
					'dash' => __( 'Dash', 'happy-addons-pro' ),
				],
			]
		);

		$this->add_control(
			'grid_color',
			[
				'label' => __( 'Grid Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#eee',
				'condition' => [
					'grid_display' => 'yes',
				]
			]
		);

		$this->add_control(
			'title_typography_toggle',
			[
				'label' => __( 'Title Typography', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'None', 'happy-addons-pro' ),
				'label_on' => __( 'Custom', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'condition' => [
					'title_display' => 'yes'
				]
			]
		);

		$this->start_popover();

		$this->add_control(
			'title_font_size',
			[
				'label' => __( 'Font Size', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'condition' => [
					'title_display' => 'yes',
					'title_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'title_font_family',
			[
				'label' => __( 'Font Family', 'happy-addons-pro' ),
				'type' => Controls_Manager::FONT,
				'default' => '',
				'condition' => [
					'title_display' => 'yes',
					'title_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'title_font_weight',
			[
				'label'   => esc_html__( 'Font Weight', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Default', 'happy-addons-pro' ),
					'normal' => __( 'Normal', 'happy-addons-pro' ),
					'bold'   => __( 'Bold', 'happy-addons-pro' ),
					'300'    => __( '300', 'happy-addons-pro' ),
					'400'    => __( '400', 'happy-addons-pro' ),
					'600'    => __( '600', 'happy-addons-pro' ),
					'700'    => __( '700', 'happy-addons-pro' )
				],
				'condition' => [
					'title_display' => 'yes',
					'title_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'title_font_style',
			[
				'label'   => esc_html__( 'Font Style', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''        => __( 'Default', 'happy-addons-pro' ),
					'normal'  => __( 'Normal', 'happy-addons-pro' ),
					'italic'  => __( 'Italic', 'happy-addons-pro' ),
					'oblique' => __( 'Oblique', 'happy-addons-pro' ),
				],
				'condition' => [
					'title_display' => 'yes',
					'title_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'title_font_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'title_display' => 'yes',
					'title_typography_toggle' => 'yes'
				]
			]
		);

		$this->end_popover();

		$this->end_controls_section();
	}

	protected function __legend_style_controls() {

		$this->start_controls_section(
			'_section_style_legend',
			[
				'label' => __( 'Legend', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'lagend_note',
			[
				'label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'Lagend is Switched off from Content > Settings.', 'happy-addons-pro' ),
				'condition' => [
					'legend_display!' => 'yes'
				]
			]
		);

		$this->add_control(
			'legend_box_width',
			[
				'label' => __( 'Box Width', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 70,
					],
				],
				'condition' => [
					'legend_display' => 'yes'
				]
			]
		);

		$this->add_control(
            'legend_typography_toggle',
            [
                'label' => __( 'Typography', 'happy-addons-pro' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __( 'None', 'happy-addons-pro' ),
                'label_on' => __( 'Custom', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'condition' => [
					'legend_display' => 'yes'
				]
            ]
		);

		$this->start_popover();

		$this->add_control(
			'legend_font_size',
			[
				'label' => __( 'Font Size', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'condition' => [
					'legend_display' => 'yes',
					'legend_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'legend_font_family',
			[
				'label' => __( 'Font Family', 'happy-addons-pro' ),
				'type' => Controls_Manager::FONT,
				'default' => '',
				'condition' => [
					'legend_display' => 'yes',
					'legend_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'legend_font_weight',
			[
				'label'   => esc_html__( 'Font Weight', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Default', 'happy-addons-pro' ),
					'normal' => __( 'Normal', 'happy-addons-pro' ),
					'bold'   => __( 'Bold', 'happy-addons-pro' ),
					'300'    => __( '300', 'happy-addons-pro' ),
					'400'    => __( '400', 'happy-addons-pro' ),
					'600'    => __( '600', 'happy-addons-pro' ),
					'700'    => __( '700', 'happy-addons-pro' )
				],
				'condition' => [
					'legend_display' => 'yes',
					'legend_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'legend_font_style',
			array(
				'label'   => esc_html__( 'Font Style', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''        => __( 'Default', 'happy-addons-pro' ),
					'normal'  => __( 'Normal', 'happy-addons-pro' ),
					'italic'  => __( 'Italic', 'happy-addons-pro' ),
					'oblique' => __( 'Oblique', 'happy-addons-pro' ),
				),
				'condition' => [
					'legend_display' => 'yes',
					'legend_typography_toggle' => 'yes'
				]
			)
		);

		$this->add_control(
			'legend_font_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'legend_display' => 'yes',
					'legend_typography_toggle' => 'yes'
				]
			]
		);

		$this->end_popover();

		$this->end_controls_section();
	}

	protected function __labels_style_controls() {

		$this->start_controls_section(
			'_section_style_label',
			[
				'label' => __( 'Labels', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'labels_padding',
			[
				'label'       => __( 'Padding', 'happy-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
			]
		);

		$this->add_control(
			'xaxes_label_note',
			[
				'label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'X Axes Label is Switched off from Content > Settings.', 'happy-addons-pro' ),
				'condition' => [
					'xaxes_labels_display!' => 'yes'
				]
			]
		);

		$this->add_control(
            'labels_xaxes_typography_toggle',
            [
                'label' => __( 'X Axes Typography', 'happy-addons-pro' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __( 'None', 'happy-addons-pro' ),
                'label_on' => __( 'Custom', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'condition' => [
					'xaxes_labels_display' => 'yes'
				]
            ]
		);

		$this->start_popover();

		$this->add_control(
			'labels_xaxes_font_size',
			[
				'label' => __( 'Font Size', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'condition' => [
					'xaxes_labels_display' => 'yes',
					'labels_xaxes_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'labels_xaxes_font_family',
			[
				'label' => __( 'Font Family', 'happy-addons-pro' ),
				'type' => Controls_Manager::FONT,
				'default' => '',
				'condition' => [
					'xaxes_labels_display' => 'yes',
					'labels_xaxes_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'labels_xaxes_font_weight',
			[
				'label'   => esc_html__( 'Font Weight', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Default', 'happy-addons-pro' ),
					'normal' => __( 'Normal', 'happy-addons-pro' ),
					'bold'   => __( 'Bold', 'happy-addons-pro' ),
					'300'    => __( '300', 'happy-addons-pro' ),
					'400'    => __( '400', 'happy-addons-pro' ),
					'600'    => __( '600', 'happy-addons-pro' ),
					'700'    => __( '700', 'happy-addons-pro' )
				],
				'condition' => [
					'xaxes_labels_display' => 'yes',
					'labels_xaxes_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'labels_xaxes_font_style',
			array(
				'label'   => esc_html__( 'Font Style', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''        => __( 'Default', 'happy-addons-pro' ),
					'normal'  => __( 'Normal', 'happy-addons-pro' ),
					'italic'  => __( 'Italic', 'happy-addons-pro' ),
					'oblique' => __( 'Oblique', 'happy-addons-pro' ),
				),
				'condition' => [
					'xaxes_labels_display' => 'yes',
					'labels_xaxes_typography_toggle' => 'yes'
				]
			)
		);

		$this->add_control(
			'labels_xaxes_font_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'xaxes_labels_display' => 'yes',
					'labels_xaxes_typography_toggle' => 'yes'
				]
			]
		);

		$this->end_popover();

		$this->add_control(
			'yaxes_label_note',
			[
				'label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'Y Axes Label is Switched off from Content > Settings.', 'happy-addons-pro' ),
				'condition' => [
					'yaxes_labels_display!' => 'yes'
				]
			]
		);

		$this->add_control(
            'labels_yaxes_typography_toggle',
            [
                'label' => __( 'Y Axes Typography', 'happy-addons-pro' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __( 'None', 'happy-addons-pro' ),
                'label_on' => __( 'Custom', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'condition' => [
					'yaxes_labels_display' => 'yes'
				]
            ]
		);

		$this->start_popover();

		$this->add_control(
			'labels_yaxes_font_size',
			[
				'label' => __( 'Font Size', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'condition' => [
					'xaxes_labels_display' => 'yes',
					'labels_yaxes_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'labels_yaxes_font_family',
			[
				'label' => __( 'Font Family', 'happy-addons-pro' ),
				'type' => Controls_Manager::FONT,
				'default' => '',
				'condition' => [
					'xaxes_labels_display' => 'yes',
					'labels_yaxes_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'labels_yaxes_font_weight',
			[
				'label'   => esc_html__( 'Font Weight', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Default', 'happy-addons-pro' ),
					'normal' => __( 'Normal', 'happy-addons-pro' ),
					'bold'   => __( 'Bold', 'happy-addons-pro' ),
					'300'    => __( '300', 'happy-addons-pro' ),
					'400'    => __( '400', 'happy-addons-pro' ),
					'600'    => __( '600', 'happy-addons-pro' ),
					'700'    => __( '700', 'happy-addons-pro' )
				],
				'condition' => [
					'xaxes_labels_display' => 'yes',
					'labels_yaxes_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'labels_yaxes_font_style',
			array(
				'label'   => esc_html__( 'Font Style', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''        => __( 'Default', 'happy-addons-pro' ),
					'normal'  => __( 'Normal', 'happy-addons-pro' ),
					'italic'  => __( 'Italic', 'happy-addons-pro' ),
					'oblique' => __( 'Oblique', 'happy-addons-pro' ),
				),
				'condition' => [
					'xaxes_labels_display' => 'yes',
					'labels_yaxes_typography_toggle' => 'yes'
				]
			)
		);

		$this->add_control(
			'labels_yaxes_font_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'xaxes_labels_display' => 'yes',
					'labels_yaxes_typography_toggle' => 'yes'
				]
			]
		);

		$this->end_popover();

		$this->end_controls_section();
	}

	protected function __tooltip_style_controls() {

		$this->start_controls_section(
			'_section_style_tooltip',
			[
				'label' => __( 'Tooltip', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tooltip_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'condition' => [
					'tooltip_display' => 'yes',
				]
			]
		);

		$this->add_control(
			'tooltip_border_width',
			[
				'label' => __( 'Border Width', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'condition' => [
					'tooltip_display' => 'yes',
				]
			]
		);

		$this->add_control(
			'tooltip_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'condition' => [
					'tooltip_display' => 'yes',
				]
			]
		);

		$this->add_control(
			'tooltip_caret_size',
			[
				'label' => __( 'Caret Size', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'condition' => [
					'tooltip_display' => 'yes',
				]
			]
		);

		$this->add_control(
			'tooltip_mode',
			[
				'label'   => esc_html__( 'Mode', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Select Mode', 'happy-addons-pro' ),
					'nearest' => __( 'Nearest', 'happy-addons-pro' ),
					'index' => __( 'Index', 'happy-addons-pro' ),
					'x' => __( 'X', 'happy-addons-pro' ),
					'y' => __( 'Y', 'happy-addons-pro' ),
				],
				'default' => '',
				'condition' => [
					'tooltip_display' => 'yes',
				]
			]
		);

		$this->add_control(
			'tooltip_background_color',
			[
				'label' => esc_html__( 'Background Color', 'happy-addons-pro' ),
				'type'  => Controls_Manager::COLOR,
				'condition' => [
					'tooltip_display' => 'yes'
				]
			]
		);

		$this->add_control(
			'tooltip_border_color',
			[
				'label' => esc_html__( 'Border Color', 'happy-addons-pro' ),
				'type'  => Controls_Manager::COLOR,
				'condition' => [
					'tooltip_display' => 'yes'
				]
			]
		);

		$this->add_control(
			'tooltip_title_typography_toggle',
			[
				'label' => __( 'Title Typography', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'None', 'happy-addons-pro' ),
				'label_on' => __( 'Custom', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'condition' => [
					'tooltip_display' => 'yes'
				]
			]
		);

		$this->start_popover();

		$this->add_control(
			'tooltip_title_font_size',
			[
				'label' => __( 'Font Size', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'condition' => [
					'tooltip_display' => 'yes',
					'tooltip_title_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'tooltip_title_font_family',
			[
				'label' => __( 'Font Family', 'happy-addons-pro' ),
				'type' => Controls_Manager::FONT,
				'default' => '',
				'condition' => [
					'tooltip_display' => 'yes',
					'tooltip_title_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'tooltip_title_font_weight',
			[
				'label'   => esc_html__( 'Font Weight', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Default', 'happy-addons-pro' ),
					'normal' => __( 'Normal', 'happy-addons-pro' ),
					'bold'   => __( 'Bold', 'happy-addons-pro' ),
					'300'    => __( '300', 'happy-addons-pro' ),
					'400'    => __( '400', 'happy-addons-pro' ),
					'600'    => __( '600', 'happy-addons-pro' ),
					'700'    => __( '700', 'happy-addons-pro' )
				],
				'condition' => [
					'tooltip_display' => 'yes',
					'tooltip_title_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'tooltip_title_font_style',
			[
				'label'   => esc_html__( 'Font Style', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''        => __( 'Default', 'happy-addons-pro' ),
					'normal'  => __( 'Normal', 'happy-addons-pro' ),
					'italic'  => __( 'Italic', 'happy-addons-pro' ),
					'oblique' => __( 'Oblique', 'happy-addons-pro' ),
				],
				'condition' => [
					'tooltip_display' => 'yes',
					'tooltip_title_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'tooltip_title_font_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'tooltip_display' => 'yes',
					'tooltip_title_typography_toggle' => 'yes'
				]
			]
		);

		$this->end_popover();

		$this->add_control(
			'tooltip_body_typography_toggle',
			[
				'label' => __( 'Body Typography', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'None', 'happy-addons-pro' ),
				'label_on' => __( 'Custom', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'condition' => [
					'tooltip_display' => 'yes'
				]
			]
		);

		$this->start_popover();

		$this->add_control(
			'tooltip_body_font_size',
			[
				'label' => __( 'Font Size', 'happy-addons-pro' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'condition' => [
					'tooltip_display' => 'yes',
					'tooltip_body_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'tooltip_body_font_family',
			[
				'label' => __( 'Font Family', 'happy-addons-pro' ),
				'type' => Controls_Manager::FONT,
				'default' => '',
				'condition' => [
					'tooltip_display' => 'yes',
					'tooltip_body_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'tooltip_body_font_weight',
			[
				'label'   => esc_html__( 'Font Weight', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Default', 'happy-addons-pro' ),
					'normal' => __( 'Normal', 'happy-addons-pro' ),
					'bold'   => __( 'Bold', 'happy-addons-pro' ),
					'300'    => __( '300', 'happy-addons-pro' ),
					'400'    => __( '400', 'happy-addons-pro' ),
					'600'    => __( '600', 'happy-addons-pro' ),
					'700'    => __( '700', 'happy-addons-pro' )
				],
				'condition' => [
					'tooltip_display' => 'yes',
					'tooltip_body_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'tooltip_body_font_style',
			[
				'label'   => esc_html__( 'Font Style', 'happy-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''        => __( 'Default', 'happy-addons-pro' ),
					'normal'  => __( 'Normal', 'happy-addons-pro' ),
					'italic'  => __( 'Italic', 'happy-addons-pro' ),
					'oblique' => __( 'Oblique', 'happy-addons-pro' ),
				],
				'condition' => [
					'tooltip_display' => 'yes',
					'tooltip_body_typography_toggle' => 'yes'
				]
			]
		);

		$this->add_control(
			'tooltip_body_font_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'tooltip_display' => 'yes',
					'tooltip_body_typography_toggle' => 'yes'
				]
			]
		);

		$this->end_popover();

		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();
		include_once HAPPY_ADDONS_PRO_DIR_PATH . "widgets/line-chart/classes/data-map.php";

		$this->add_render_attribute( 'container',
			[
				'class'         => 'ha-line-chart-container',
				'data-settings' => Data_Map::initial($settings)
			]
		);

		$this->add_render_attribute( 'canvas',
			[
				'id' => 'ha-line-chart',
				'role'  => 'img',
			]
		);
		?>
		<div <?php echo $this->get_render_attribute_string( 'container' ); ?>>

			<canvas <?php echo $this->get_render_attribute_string( 'canvas' ); ?>></canvas>

		</div>

	<?php
	}

}

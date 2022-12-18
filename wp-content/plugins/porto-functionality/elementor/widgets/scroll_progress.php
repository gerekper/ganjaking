<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Scroll Progress widget
 *
 * @since 2.3.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Scroll_Progress_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_scroll_progress';
	}

	public function get_title() {
		return __( 'Porto Scroll Progress', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'scroll', 'progress', 'top', 'circle', 'bar', 'inner' );
	}

	public function get_icon() {
		return 'fas fa-scroll';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/scroll-progress-widget/';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			if ( ! wp_script_is( 'porto-scroll-progress', 'registered' ) ) {
				wp_register_script( 'porto-scroll-progress', PORTO_SHORTCODES_URL . 'assets/js/porto-scroll-progress.min.js', array( 'jquery-core' ), PORTO_SHORTCODES_VERSION, true );
			}
			return array( 'porto-scroll-progress' );
		} else {
			return array();
		}
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_scroll_progress',
			array(
				'label' => __( 'Scroll Progress', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'type',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Type & Position', 'porto-functionality' ),
				'description' => __( 'If you select "Around the Scroll to Top button", default scroll to top button will be hidden.', 'porto-functionality' ),
				'default'     => '',
				'options'     => array(
					''       => __( 'Horizontal progress bar', 'porto-functionality' ),
					'circle' => __( 'Around the Scroll to Top button', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'position',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Is Fixed Position?', 'porto-functionality' ),
				'default'   => '',
				'options'   => array(
					''             => __( 'No', 'porto-functionality' ),
					'top'          => __( 'Fixed on Top', 'porto-functionality' ),
					'under-header' => __( 'Under Sticky Header', 'porto-functionality' ),
					'bottom'       => __( 'Fixed on Bottom', 'porto-functionality' ),
				),
				'condition' => array(
					'type' => '',
				),
			)
		);

		$this->add_control(
			'offset_top',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Offset Height', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 200,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'condition'  => array(
					'type'     => '',
					'position' => 'top',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .porto-scroll-progress' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'offset_bottom',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Offset Height', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 200,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'condition'  => array(
					'type'     => '',
					'position' => 'bottom',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .porto-scroll-progress' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_cls',
			array(
				'type'                   => Controls_Manager::ICONS,
				'label'                  => __( 'Icon Class for scroll to top', 'porto-functionality' ),
				'fa4compatibility'       => 'icon',
				'default'                => array(
					'value'   => 'fas fa-long-arrow-alt-up',
					'library' => 'fa-solid',
				),
				'skin'                   => 'inline',
				'exclude_inline_options' => array( 'svg' ),
				'condition'              => array(
					'type' => 'circle',
				),
			)
		);

		$this->add_control(
			'circle_size',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Size', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 200,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'condition'  => array(
					'type' => 'circle',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .porto-scroll-progress-circle' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'position1',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Position', 'porto-functionality' ),
				'default'   => '',
				'options'   => array(
					''   => __( 'Bottom & Right', 'porto-functionality' ),
					'bl' => __( 'Bottom & Left', 'porto-functionality' ),
					'tl' => __( 'Top & Left', 'porto-functionality' ),
					'tr' => __( 'Top & Right', 'porto-functionality' ),
				),
				'condition' => array(
					'type' => 'circle',
				),
			)
		);

		$this->add_control(
			'offset_x1',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Offset X', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 200,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'condition'  => array(
					'type'      => 'circle',
					'position1' => array( 'tl', 'bl' ),
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .porto-scroll-progress-circle' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'offset_x2',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Offset X', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 200,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'condition'  => array(
					'type'      => 'circle',
					'position1' => array( 'tr', '' ),
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .porto-scroll-progress-circle' => 'right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'offset_y1',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Offset Y', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 200,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'condition'  => array(
					'type'      => 'circle',
					'position1' => array( 'tl', 'tr' ),
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .porto-scroll-progress-circle' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'offset_y2',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Offset Y', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 200,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'condition'  => array(
					'type'      => 'circle',
					'position1' => array( '', 'bl' ),
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .porto-scroll-progress-circle' => 'bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'thickness1',
			array(
				'type'      => Controls_Manager::NUMBER,
				'label'     => __( 'Thickness (px)', 'porto-functionality' ),
				'min'       => 1,
				'max'       => 20,
				'condition' => array(
					'type' => '',
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .porto-scroll-progress' => 'height: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'thickness2',
			array(
				'type'      => Controls_Manager::NUMBER,
				'label'     => __( 'Thickness of progress bar (px)', 'porto-functionality' ),
				'min'       => 1,
				'max'       => 10,
				'condition' => array(
					'type' => 'circle',
				),
				'selectors' => array(
					'.elementor-element-{{ID}} circle' => 'stroke-width: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Icon Size', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'condition'  => array(
					'type' => 'circle',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .porto-scroll-progress-circle' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_bgcolor',
			array(
				'type'        => Controls_Manager::COLOR,
				'label'       => __( 'Background Color', 'porto-functionality' ),
				'description' => __( 'Set the background color of icon part.', 'porto-functionality' ),
				'condition'   => array(
					'type' => 'circle',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} i' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Icon Color', 'porto-functionality' ),
				'condition' => array(
					'type' => 'circle',
				),
				'selectors' => array(
					'.elementor-element-{{ID}} i' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'br',
			array(
				'type'      => Controls_Manager::NUMBER,
				'label'     => __( 'Border Radius (px)', 'porto-functionality' ),
				'condition' => array(
					'type' => '',
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .porto-scroll-progress' => 'border-radius: {{VALUE}}px;',
					'.elementor-element-{{ID}} .porto-scroll-progress::-moz-progress-bar' => 'border-radius: {{VALUE}}px;',
					'.elementor-element-{{ID}} .porto-scroll-progress::-webkit-progress-bar' => 'border-radius: {{VALUE}}px;',
					'.elementor-element-{{ID}} .porto-scroll-progress::-webkit-progress-value' => 'border-radius: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'bgcolor',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Bar Color', 'porto-functionality' ),
				'condition' => array(
					'type' => '',
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .porto-scroll-progress' => 'background-color: {{VALUE}};',
					'.elementor-element-{{ID}} .porto-scroll-progress::-webkit-progress-bar' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'active_bgcolor',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Active Bar Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .porto-scroll-progress::-moz-progress-bar' => 'background-color: {{VALUE}};',
					'.elementor-element-{{ID}} .porto-scroll-progress::-webkit-progress-value' => 'background-color: {{VALUE}};',
					'.elementor-element-{{ID}} circle' => 'stroke: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function content_template() {
		?>
		&nbsp;
		<#
			let cls = 'porto-scroll-progress porto-scroll-progress-circle';
			if ( 'circle' == settings.type ) {
				if ( settings.position1 ) {
					cls += ' pos-' + settings.position1;
				}
				if ( settings.el_class ) {
					cls += ' ' + settings.el_class;
				}
		#>
				<a class="{{ cls }}" href="#" role="button">
					<i class="{{ settings.icon_cls && settings.icon_cls.value ? settings.icon_cls.value : 'fas fa-chevron-up' }}"></i>
					<svg  version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 70 70">
						<circle id="progress-indicator" fill="transparent" stroke="#000000" stroke-miterlimit="10" cx="35" cy="35" r="34"/>
					</svg>
				</a><style>#topcontrol{display:none}</style>
		<#
			} else {
				let cls = 'porto-scroll-progress';
				if ( settings.position ) {
					cls += ' fixed-' + settings.position;
					if ( 'under-header' == settings.position ) {
						cls += ' fixed-top';
					}
				}
				if ( settings.el_class ) {
					cls += ' ' + settings.el_class;
				}
		#>
				<progress class="{{ cls }}" max="100">
				</progress>
		<#
			}
		#>
		<?php
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( $template = porto_shortcode_template( 'porto_scroll_progress' ) ) {
			if ( isset( $atts['icon_cls'] ) && isset( $atts['icon_cls']['value'] ) ) {
				$atts['icon_cls'] = $atts['icon_cls']['value'];
			}
			include $template;
		}
	}
}

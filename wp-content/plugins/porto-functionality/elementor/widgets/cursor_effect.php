<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Cursor Effect widget
 *
 * @since 2.4.0
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;

class Porto_Elementor_Cursor_Effect_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_cursor_effect';
	}

	public function get_title() {
		return __( 'Porto Cursor Effect', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'cursor', 'effect', 'circle', 'dot', 'mouse', 'pointer', 'icon' );
	}

	public function get_icon() {
		return 'far fa-hand-pointer';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/cursor-effect-widget/';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_cursor_effect',
			array(
				'label' => __( 'Cursor Effect', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'selector',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Target', 'porto-functionality' ),
				'description' => __( 'Please input the target using a jQuery selector which this cursor effect is applied to. It you leave it empty, this cursor effect will be applied to all pages.', 'porto-functionality' ),
				'default'     => '',
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Hover Effect on Target', 'porto-functionality' ),
				'options' => array(
					'plus' => __( 'Change Mouse Cursor', 'porto-functionality' ),
					'fit'  => __( 'Outline Target', 'porto-functionality' ),
				),
				'default' => 'plus',
			)
		);

		$this->add_control(
			'inner_icon',
			array(
				'type'                   => Controls_Manager::ICONS,
				'label'                  => __( 'Icon Class for cursor inner', 'porto-functionality' ),
				'fa4compatibility'       => 'icon',
				'default'                => array(
					'value'   => 'fas fa-circle',
					'library' => 'fa-solid',
				),
				'skin'                   => 'inline',
				'exclude_inline_options' => array( 'svg' ),
				'label_block'            => false,
				'condition'              => array(
					'hover_effect' => 'plus',
				),
			)
		);

		$this->add_control(
			'icon_fs',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Icon Font Size', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 50,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 3,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 3,
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
					'inner_icon[value]!' => '',
				),
				'selectors'  => array(
					'.cursor-element-{{ID}}.cursor-inner-icon' => '--porto-cursor-inner-fs: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'inner_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Icon Color', 'porto-functionality' ),
				'condition' => array(
					'inner_icon[value]!' => '',
				),
				'selectors' => array(
					'.cursor-element-{{ID}}.cursor-inner' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tr_dr',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Transition Duration (ms)', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 2000,
					),
				),
				'default'   => array(
					'unit' => 'px',
				),
				'selectors' => array(
					'.cursor-element-{{ID}}.cursor-outer, .cursor-element-{{ID}}.cursor-inner' => 'transition-duration: {{SIZE}}ms;',
				),
			)
		);

		$this->add_control(
			'tr_dl',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Transition Delay (ms)', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 2000,
					),
				),
				'default'   => array(
					'unit' => 'px',
				),
				'selectors' => array(
					'.cursor-element-{{ID}}.cursor-outer' => 'transition-delay: {{SIZE}}ms;',
				),
			)
		);

		$this->add_control(
			'inner_tr_dl',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Cursor Inner Transition Delay (ms)', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 2000,
					),
				),
				'default'   => array(
					'unit' => 'px',
				),
				'condition' => array(
					'hover_effect' => 'plus',
				),
				'selectors' => array(
					'.cursor-element-{{ID}}.cursor-inner' => 'transition-delay: {{SIZE}}ms;',
				),
			)
		);

		$this->end_controls_section();

		// style options
		$this->start_controls_section(
			'section_cursor_options',
			array(
				'label' => __( 'Cursor', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'cursor_w',
			array(
				'type'               => Controls_Manager::SLIDER,
				'label'              => __( 'Cursor Size', 'porto-functionality' ),
				'range'              => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'default'            => array(
					'unit' => 'px',
				),
				'size_units'         => array(
					'px',
				),
				'condition'          => array(
					'hover_effect' => 'plus',
				),
				'render_type'        => 'template',
				'frontend_available' => true,
				'selectors'          => array(
					'.cursor-element-{{ID}}.cursor-outer' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'.cursor-element-{{ID}}.cursor-inner' => 'left: calc( {{SIZE}}{{UNIT}} / 2 ); top: calc( {{SIZE}}{{UNIT}} / 2 );',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'bd',
				'selector' => '.cursor-element-{{ID}}.cursor-outer',
			)
		);

		$this->add_control(
			'bg',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'condition' => array(
					'hover_effect' => 'plus',
				),
				'selectors' => array(
					'.cursor-element-{{ID}}.cursor-outer' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'br',
			array(
				'type'      => Controls_Manager::NUMBER,
				'label'     => __( 'Border Radius (px)', 'porto-functionality' ),
				'condition' => array(
					'hover_effect' => 'plus',
				),
				'selectors' => array(
					'.cursor-element-{{ID}}.cursor-outer' => 'border-radius: {{VALUE}}px;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cursor_inner_options',
			array(
				'label'     => __( 'Cursor Inner', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'hover_effect' => 'plus',
				),
			)
		);

		$this->add_control(
			'inner_cursor_w',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Inner Size', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 50,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 3,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 3,
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
				'selectors'  => array(
					'.cursor-element-{{ID}}.cursor-inner' => '--porto-cursor-inner-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'inner_bd_bs',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Border Style', 'porto-functionality' ),
				'options'   => array(
					''       => __( 'None', 'porto-functionality' ),
					'solid'  => __( 'Solid', 'porto-functionality' ),
					'double' => __( 'Double', 'porto-functionality' ),
					'dotted' => __( 'Dotted', 'porto-functionality' ),
					'dashed' => __( 'Dashed', 'porto-functionality' ),
					'groove' => __( 'Groove', 'porto-functionality' ),
				),
				'selectors' => array(
					'.cursor-element-{{ID}}.cursor-inner' => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'inner_bd_bw',
			array(
				'label'      => esc_html__( 'Width', 'porto-functionality' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
				),
				'selectors'  => array(
					'.cursor-element-{{ID}}.cursor-inner' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; margin-top: calc( var( --porto-cursor-inner-size, calc( {{TOP}}{{UNIT}} + {{BOTTOM}}{{UNIT}} ) ) / -2 ); margin-left: calc( var( --porto-cursor-inner-size, calc( {{LEFT}}{{UNIT}} + {{RIGHT}}{{UNIT}} ) ) / -2 );',
					'.cursor-element-{{ID}}.cursor-inner.cursor-inner-icon' => 'margin-top: calc( var( --porto-cursor-inner-size, calc( 1em + {{TOP}}{{UNIT}} + {{BOTTOM}}{{UNIT}} ) ) / -2 ); margin-left: calc( var( --porto-cursor-inner-size, calc( 1em + {{LEFT}}{{UNIT}} + {{RIGHT}}{{UNIT}} ) ) / -2 );',
				),
				'condition'  => array(
					'inner_bd_bs!' => '',
				),
			)
		);

		$this->add_control(
			'inner_bd_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'.cursor-element-{{ID}}.cursor-inner' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'inner_bd_bs!' => '',
				),
			)
		);

		$this->add_control(
			'inner_bg',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'selectors' => array(
					'.cursor-element-{{ID}}.cursor-inner' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'inner_br',
			array(
				'type'      => Controls_Manager::NUMBER,
				'label'     => __( 'Border Radius (px)', 'porto-functionality' ),
				'selectors' => array(
					'.cursor-element-{{ID}}.cursor-inner' => 'border-radius: {{VALUE}}px;',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function content_template() {
		?>
		<#
			const iframeWindow = elementorFrontend.elements.$window.get(0);
			if ( typeof iframeWindow.porto_cursor_effects == 'undefined' ) {
				iframeWindow.porto_cursor_effects = [];
			}

			const el_id = view.getEditModel().attributes.id;
			iframeWindow.porto_cursor_effects.forEach( function( i, index ) {
				if ( i.id && 'cursor-element-' + el_id == i.id ) {
					iframeWindow.porto_cursor_effects.splice( index, 1 );
					return false;
				}
			} );
			iframeWindow.porto_cursor_effects.push( { id: 'cursor-element-' + el_id, selector: settings.selector, hover_effect: settings.hover_effect, icon: settings.inner_icon && settings.inner_icon.value, cursor_w: settings.cursor_w && settings.cursor_w.size } );
		#>
			
		<?php
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( $template = porto_shortcode_template( 'porto_cursor_effect' ) ) {
			if ( empty( $atts ) ) {
				$atts = array();
			}
			if ( isset( $atts['inner_icon'] ) && isset( $atts['inner_icon']['value'] ) ) {
				$atts['inner_icon'] = $atts['inner_icon']['value'];
			}
			if ( isset( $atts['cursor_w'] ) && isset( $atts['cursor_w']['size'] ) ) {
				$atts['cursor_w'] = $atts['cursor_w']['size'];
			}
			$atts['el_id'] = $this->get_id();
			include $template;
		}
	}
}

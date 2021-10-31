<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Button Widget
 *
 * Porto Elementor widget to display a button.
 *
 * @since 5.2.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Button_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_button';
	}

	public function get_title() {
		return __( 'Porto Button', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'button', 'btn', 'link' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	protected function _register_controls() {

		$floating_options = porto_update_vc_options_to_elementor( porto_shortcode_floating_fields() );

		$this->start_controls_section(
			'section_button',
			array(
				'label' => __( 'Button', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'porto-functionality' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Title', 'porto-functionality' ),
				'default'     => 'Click Here',
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'link',
			array(
				'label' => __( 'Link', 'porto-functionality' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_control(
			'layout',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Layout', 'porto-functionality' ),
				'options' => array(
					''        => __( 'Default', 'porto-functionality' ),
					'modern'  => __( 'Modern', 'porto-functionality' ),
					'borders' => __( 'Outline', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'size',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Size', 'porto-functionality' ),
				'options' => array(
					'xs' => __( 'Extra Small', 'porto-functionality' ),
					'sm' => __( 'Small', 'porto-functionality' ),
					'md' => __( 'Medium', 'porto-functionality' ),
					'lg' => __( 'Large', 'porto-functionality' ),
					'xl' => __( 'Extra Large', 'porto-functionality' ),
				),
				'default' => 'md',
			)
		);

		$this->add_control(
			'is_block',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Is Full Width?', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'shape',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Shape', 'porto-functionality' ),
				'options' => array(
					''      => __( 'Default', 'porto-functionality' ),
					'round' => __( 'Round', 'porto-functionality' ),
				),
			)
		);

		if ( function_exists( 'porto_vc_commons' ) ) :
			$this->add_control(
				'skin',
				array(
					'type'    => Controls_Manager::SELECT,
					'label'   => __( 'Skin Color', 'porto-functionality' ),
					'default' => 'primary',
					'options' => array_merge(
						array_combine(
							array_values( porto_vc_commons( 'colors' ) ),
							array_keys( porto_vc_commons( 'colors' ) )
						),
						array(
							'default' => __( 'Default', 'porto-functionality' ),
						)
					),
				)
			);
		endif;

		$this->add_control(
			'icon_cls',
			array(
				'type'             => Controls_Manager::ICONS,
				'label'            => __( 'Icon', 'porto-functionality' ),
				'fa4compatibility' => 'icon',
			)
		);

		$this->add_control(
			'icon_pos',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Icon Position', 'porto-functionality' ),
				'options'   => array(
					'left'  => __( 'Left', 'porto-functionality' ),
					'right' => __( 'Right', 'porto-functionality' ),
				),
				'default'   => 'left',
				'condition' => array(
					'icon_cls[value]!' => '',
				),
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Select Hover Effect type', 'porto-functionality' ),
				'description' => __( 'Select the type of effct you want on hover', 'porto-functionality' ),
				'options'     => array(
					''                 => __( 'No Effect', 'porto-functionality' ),
					'hover-icon-zoom'  => __( 'Icon Zoom', 'porto-functionality' ),
					'hover-icon-up'    => __( 'Icon Slide Up', 'porto-functionality' ),
					'hover-icon-left'  => __( 'Icon Slide Left', 'porto-functionality' ),
					'hover-icon-right' => __( 'Icon Slide Right', 'porto-functionality' ),
				),
				'default'     => '',
				'condition'   => array(
					'icon_cls[value]!' => '',
				),
			)
		);

		$this->add_control(
			'show_arrow',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Show Animation Arrow?', 'porto-functionality' ),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'              => __( 'Alignment', 'elementor' ),
				'type'               => Controls_Manager::CHOOSE,
				'options'            => array(
					'left'    => array(
						'title' => __( 'Left', 'elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justified', 'elementor' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'            => '',
				'selectors'          => array(
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_style_options',
			array(
				'label' => __( 'Style', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'btn_typograhy',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typograhy', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .btn',
			)
		);

		$this->add_control(
			'btn_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Text Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'background_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'border_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Border Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .btn' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Hover Text Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .btn:hover, {{WRAPPER}} .btn:focus, {{WRAPPER}} .btn:active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'background_hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Hover Background Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .btn:hover, {{WRAPPER}} .btn:focus, {{WRAPPER}} .btn:active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'border_hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Hover Border Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .btn:hover, {{WRAPPER}} .btn:focus, {{WRAPPER}} .btn:active' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'padding',
			array(
				'label'      => __( 'Padding', 'porto-functionality' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => array(
					'{{WRAPPER}} .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px', 'em', 'rem' ),
			)
		);

		$left  = is_rtl() ? 'right' : 'left';
		$right = is_rtl() ? 'left' : 'right';

		$this->add_control(
			'icon_size',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Icon Size', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 72,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
				),
				'size_units' => array(
					'px',
					'rem',
					'em',
				),
				'selectors'  => array(
					'{{WRAPPER}} .btn-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'icon_cls[value]!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'spacing',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Spacing between text and icon', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 32,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 2,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 2,
					),
				),
				'size_units' => array(
					'px',
					'rem',
					'em',
				),
				'selectors'  => array(
					'{{WRAPPER}} .btn-icon-right i' => 'margin-' . $left . ': {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .btn-icon-left i' => 'margin-' . $right . ': {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'icon_cls[value]!' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_floating_options',
			array(
				'label' => __( 'Floating Animation', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		foreach ( $floating_options as $key => $opt ) {
			unset( $opt['condition']['animation_type'] );
			$this->add_control( $key, $opt );
		}

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_button' ) ) {
			$this->add_inline_editing_attributes( 'title' );
			$title_attrs_escaped = ' ' . $this->get_render_attribute_string( 'title' );
			unset( $atts['align'] );
			include $template;
		}
	}

	protected function content_template() {
		?>
		<#
			let btn_classes = ' btn-' + escape( settings.size );
			if ( 'custom' != settings.skin ) {
				btn_classes += ' btn-' + escape( settings.skin );
			}
			if ( settings.layout ) {
				btn_classes += ' btn-' + settings.layout;
			}

			if ( 'yes' == settings.is_block ) {
				btn_classes += ' btn-block';
			}

			if ( 'round' == settings.shape ) {
				btn_classes += ' btn-full-rounded';
			}

			if ( settings.icon_cls.value ) {
				btn_classes += ' btn-icon';
				if ( 'right' == settings.icon_pos ) {
					btn_classes += ' btn-icon-right';
				}
			}

			if ( settings.hover_effect ) {
				btn_classes += ' ' + settings.hover_effect;
			}

			view.addInlineEditingAttributes( 'title' );

			let extra_attr = porto_elementor_add_floating_options( settings );
		#>
		<a href="{{ settings.link.url }}" class="btn{{ btn_classes }}"{{ extra_attr }}>
		<#
			if ( settings.icon_cls.value && 'left' == settings.icon_pos ) {
		#>
			<i class="{{ settings.icon_cls.value }}"></i>
		<#
			}
		#>
			<span {{{ view.getRenderAttributeString( 'title' ) }}}>{{ settings.title }}</span>
		<#
			if ( settings.icon_cls.value && 'right' == settings.icon_pos ) {
		#>
			<i class="{{ settings.icon_cls.value }}"></i>
		<#
			}
			if ( 'yes' == settings.show_arrow ) {
		#>
			<span class="dir-arrow hlb" data-appear-animation-delay="800" data-appear-animation="rotateInUpLeft"></span>
		<#
			}
		#>
		</a>
		<?php
	}
}

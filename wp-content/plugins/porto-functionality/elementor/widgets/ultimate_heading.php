<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Ultimate Heading Widget
 *
 * Porto Elementor widget to display headings.
 *
 * @since 5.1.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Ultimate_Heading_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_ultimate_heading';
	}

	public function get_title() {
		return __( 'Porto Ultimate Heading', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'heading', 'title', 'text' );
	}

	public function get_icon() {
		return 'eicon-heading';
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_ultimate_heading',
			array(
				'label' => __( 'Ultimate Heading', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'main_heading',
			array(
				'label'       => __( 'Main Heading', 'porto-functionality' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'dynamic'     => array(
					'active' => true,
				),
				'placeholder' => __( 'Title', 'porto-functionality' ),
			)
		);
		$this->add_control(
			'enable_typewriter',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Enable typewriter effect', 'porto-functionality' ),
			)
		);
		$this->add_control(
			'typewriter_animation',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Animation Name', 'porto-functionality' ),
				'description' => __( 'e.g: typeWriter, fadeIn and so on.', 'porto-functionality' ),
				'default'     => 'fadeIn',
				'condition'   => array(
					'enable_typewriter' => 'yes',
				),
			)
		);
		$this->add_control(
			'typewriter_delay',
			array(
				'type'      => Controls_Manager::NUMBER,
				'label'     => __( 'Start Delay(ms)', 'porto-functionality' ),
				'condition' => array(
					'enable_typewriter' => 'yes',
				),
			)
		);
		$this->add_control(
			'typewriter_width',
			array(
				'type'      => Controls_Manager::NUMBER,
				'label'     => __( 'Input min width that can work. (px)', 'porto-functionality' ),
				'condition' => array(
					'enable_typewriter' => 'yes',
				),
			)
		);
		$this->add_control(
			'content',
			array(
				'type'  => Controls_Manager::WYSIWYG,
				'label' => __( 'Sub Heading (Optional)', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'heading_tag',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Tag', 'porto-functionality' ),
				'options'     => array(
					'h2'  => __( 'Default', 'porto-functionality' ),
					'h1'  => __( 'H1', 'porto-functionality' ),
					'h3'  => __( 'H3', 'porto-functionality' ),
					'h4'  => __( 'H4', 'porto-functionality' ),
					'h5'  => __( 'H5', 'porto-functionality' ),
					'h6'  => __( 'H6', 'porto-functionality' ),
					'div' => __( 'div', 'porto-functionality' ),
				),
				'default'     => 'h2',
				'description' => __( 'Default is H2', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'alignment',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Alignment', 'porto-functionality' ),
				'options' => array(
					'inherit' => __( 'Inherit', 'porto-functionality' ),
					'center'  => __( 'Center', 'porto-functionality' ),
					'left'    => __( 'Left', 'porto-functionality' ),
					'right'   => __( 'Right', 'porto-functionality' ),
				),
				'default' => 'center',
			)
		);

		$this->add_control(
			'spacer',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Separator', 'porto-functionality' ),
				'options'     => array(
					'no_spacer' => __( 'No Separator', 'porto-functionality' ),
					'line_only' => __( 'Line', 'porto-functionality' ),
				),
				'default'     => 'no_spacer',
				'description' => __( 'Horizontal line, icon or image to divide sections', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'spacer_position',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Separator Position', 'porto-functionality' ),
				'options'   => array(
					'top'    => __( 'Top', 'porto-functionality' ),
					'middle' => __( 'Between Heading & Sub-Heading', 'porto-functionality' ),
					'bottom' => __( 'Bottom', 'porto-functionality' ),
				),
				'default'   => 'top',
				'condition' => array(
					'spacer' => 'line_only',
				),
			)
		);

		$this->add_control(
			'line_width',
			array(
				'type'      => Controls_Manager::NUMBER,
				'label'     => __( 'Line Width(px) (optional)', 'porto-functionality' ),
				'condition' => array(
					'spacer' => 'line_only',
				),
			)
		);

		$this->add_control(
			'line_height',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Line Height', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1,
				),
				'condition' => array(
					'spacer' => 'line_only',
				),
			)
		);

		$this->add_control(
			'line_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Line Color', 'porto-functionality' ),
				'default'   => '#333333',
				'condition' => array(
					'spacer' => 'line_only',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_ultimate_heading_font_options',
			array(
				'label' => __( 'Style', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'main_heading_typography',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Main Heading Typograhy', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .porto-u-main-heading > *',
			)
		);

		$this->add_control(
			'main_heading_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Main Heading Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .porto-u-main-heading > *' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'main_heading_margin_bottom',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Heading Margin Bottom', 'porto-functionality' ),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'sub_heading_typography',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Sub Heading Typograhy', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .porto-u-sub-heading',
			)
		);

		$this->add_control(
			'sub_heading_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Sub Heading Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .porto-u-sub-heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sub_heading_margin_bottom',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Sub Heading Margin Bottom', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'spacer_margin_bottom',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Separator Margin Bottom', 'porto-functionality' ),
				'condition' => array(
					'spacer' => 'line_only',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_ultimate_heading_floating_fields',
			array(
				'label' => __( 'Floating Animation', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$floating_options = porto_update_vc_options_to_elementor( porto_shortcode_floating_fields() );
		foreach ( $floating_options as $key => $opt ) {
			unset( $opt['condition']['animation_type'] );
			$this->add_control( $key, $opt );
		}

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'main_heading' );
		$main_heading_attrs_escaped = ' ' . $this->get_render_attribute_string( 'main_heading' );

		if ( $template = porto_shortcode_template( 'porto_ultimate_heading' ) ) {
			include $template;
		}
	}

	protected function content_template() {
		?>
		<#
			view.addRenderAttribute( 'wrapper', 'class', 'porto-u-heading' );
			view.addRenderAttribute( 'wrapper', 'data-hspacer', settings.spacer );
			view.addRenderAttribute( 'wrapper', 'data-halign', settings.alignment );
			view.addRenderAttribute( 'wrapper', 'style', 'text-align: ' + settings.alignment );

			var spacer_style = '', line = '';
			if ( 'no_spacer' != settings.spacer && settings.spacer_margin_bottom ) {
				var unit = settings.spacer_margin_bottom.replace( /[0-9.]/, '' );
				if ( ! unit ){
					settings.spacer_margin_bottom += 'px';
				}
				spacer_style += 'margin-bottom: ' + settings.spacer_margin_bottom+ ';';
			}
			if ( 'line_only' == settings.spacer && settings.line_height && settings.line_height.size ) {
				var wrap_width = settings.line_width,
					line_style_inline  = 'border-style: solid;';
				line_style_inline += 'border-bottom-width:' + settings.line_height.size + 'px;';
				line_style_inline += 'border-color:' + settings.line_color + ';';
				line_style_inline += 'width:' + settings.line_width + ( 'auto' == settings.line_width ? ';' : 'px;' );
				if ( 'center' != settings.alignment ) {
					line_style_inline += 'margin-' + settings.alignment + ': 0';
				}
				spacer_style += 'height:' + settings.line_height.size + 'px;';
				line = '<span class="porto-u-headings-line" style="' + line_style_inline + '"></span>';
			}
			view.addRenderAttribute( 'spacer', 'class', 'porto-u-heading-spacer' );
			view.addRenderAttribute( 'spacer', 'class', settings.spacer );
			view.addRenderAttribute( 'spacer', 'style', spacer_style );

			if ( settings.main_heading_margin_bottom || '0' == settings.main_heading_margin_bottom ) {
				var unit = settings.main_heading_margin_bottom.replace( /[0-9.]/, '' );
				if ( ! unit ) {
					settings.main_heading_margin_bottom += 'px';
				}
				view.addRenderAttribute( 'main_heading', 'style', 'margin-bottom:' + settings.main_heading_margin_bottom );
			}
			if ( settings.enable_typewriter ) {
				var typewriter = {
					startDelay: 0,
					minWindowWidth: 0
				}
				if( settings.typewriter_delay ) {
					typewriter[ 'startDelay' ] = parseInt( settings.typewriter_delay, 10 );
				}
				if( settings.typewriter_width ) {
					typewriter[ 'minWindowWidth' ] = parseInt( settings.typewriter_width, 10 );
				}
				if( settings.typewriter_animation ) {
					typewriter[ 'animationName' ] = settings.typewriter_animation;
				}
				view.addRenderAttribute( 'main_heading', 'data-plugin-animated-letters', '' );
				view.addRenderAttribute( 'main_heading', 'data-plugin-options', JSON.stringify( typewriter ) );
			}
			view.addInlineEditingAttributes( 'main_heading' );
			if ( settings.sub_heading_margin_bottom || '0' == settings.sub_heading_margin_bottom ) {
				var unit = settings.sub_heading_margin_bottom.replace( /[0-9.]/, '' );
				if ( ! unit ) {
					settings.sub_heading_margin_bottom += 'px';
				}
				view.addRenderAttribute( 'sub_heading', 'style', 'margin-bottom:' + settings.sub_heading_margin_bottom );
			}

			let extra_attr = porto_elementor_add_floating_options( settings );
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}{{ extra_attr }}>
		<# if ( 'top' == settings.spacer_position ) { #>
			<div {{{ view.getRenderAttributeString( 'spacer' ) }}}>{{{ line }}}</div>
		<# } #>
		<# if ( settings.main_heading ) { #>
			<div class="porto-u-main-heading"><{{{ settings.heading_tag }}} {{{ view.getRenderAttributeString( 'main_heading' ) }}}>{{{ settings.main_heading }}}</{{{ settings.heading_tag }}}></div>
		<# } #>
		<# if ( 'middle' == settings.spacer_position ) { #>
			<div {{{ view.getRenderAttributeString( 'spacer' ) }}}>{{{ line }}}</div>
		<# } #>
		<# if ( settings.content ) { #>
			<div class="porto-u-sub-heading" {{{ view.getRenderAttributeString( 'sub_heading' ) }}}>{{{ settings.content }}}</div>
		<# } #>
		<# if ( 'bottom' == settings.spacer_position ) { #>
			<div {{{ view.getRenderAttributeString( 'spacer' ) }}}>{{{ line }}}</div>
		<# } #>
		</div>
		<?php
	}
}

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Stat Counter Widget
 *
 * Porto Elementor widget to display stat counters.
 *
 * @since 1.5.0
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;

class Porto_Elementor_Stat_Counter_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_stat_counter';
	}

	public function get_title() {
		return __( 'Porto Counter', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'icon', 'counter', 'statistics', 'up' );
	}

	public function get_icon() {
		return 'eicon-counter';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'countup', 'porto_shortcodes_countup_loader_js' );
		} else {
			return array();
		}
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_stat_counter',
			array(
				'label' => __( 'Counter', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'       => __( 'Icon to display', 'porto-functionality' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'icon'   => __( 'Font Awesome', 'porto-functionality' ),
					'custom' => __( 'Custom Image Icon', 'porto-functionality' ),
				),
				'default'     => 'icon',
				'description' => __( 'Use an existing font icon or upload a custom image.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'icon_cl',
			array(
				'type'             => Controls_Manager::ICONS,
				'label'            => __( 'Icon', 'porto-functionality' ),
				'fa4compatibility' => 'icon',
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'icon_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'icon_img',
			array(
				'type'        => Controls_Manager::MEDIA,
				'label'       => __( 'Upload Image Icon:', 'porto-functionality' ),
				'description' => __( 'Upload the custom image icon.', 'porto-functionality' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'icon_type' => array( 'custom' ),
				),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Icon Position', 'porto-functionality' ),
				'options'     => array(
					'top'   => __( 'Top', 'porto-functionality' ),
					'right' => __( 'Right', 'porto-functionality' ),
					'left'  => __( 'Left', 'porto-functionality' ),
				),
				'default'     => 'top',
				'description' => __( 'Enter Position of Icon', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'counter_title',
			array(
				'type'        => Controls_Manager::TEXTAREA,
				'label'       => __( 'Counter Title', 'porto-functionality' ),
				'description' => __( 'Enter title for stats counter block.', 'porto-functionality' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'counter_value',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Counter Value', 'porto-functionality' ),
				'default'     => '1250',
				'description' => __( 'Enter number for counter without any special character. You may enter a decimal number. Eg 12.76', 'porto-functionality' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'counter_sep',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Thousands Separator', 'porto-functionality' ),
				'default'     => ',',
				'description' => __( 'Enter character for thousanda separator. e.g. \',\' will separate 125000 into 125,000', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'counter_decimal',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Replace Decimal Point With', 'porto-functionality' ),
				'default'     => '.',
				'description' => __( "Did you enter a decimal number (Eg - 12.76) The decimal point '.' will be replaced with value that you will enter above.", 'porto-functionality' ),
			)
		);

		$this->add_control(
			'counter_prefix',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Counter Value Prefix', 'porto-functionality' ),
				'description' => __( 'Enter prefix for counter value', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'counter_suffix',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Counter Value Suffix', 'porto-functionality' ),
				'description' => __( 'Enter suffix for counter value', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'speed',
			array(
				'type'        => Controls_Manager::NUMBER,
				'label'       => __( 'Counter rolling time', 'porto-functionality' ),
				'default'     => 3,
				'min'         => 1,
				'max'         => 10,
				'description' => __( 'How many seconds the counter should roll?', 'porto-functionality' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_counter_style_icon',
			array(
				'label' => __( 'Icon', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_style',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Icon Style', 'porto-functionality' ),
				'description' => __( 'Circle Image is worked for only image icon.', 'porto-functionality' ),
				'options'     => array(
					'none'       => __( 'Simple', 'porto-functionality' ),
					'circle'     => __( 'Circle Background', 'porto-functionality' ),
					'circle_img' => __( 'Circle Image', 'porto-functionality' ),
					'square'     => __( 'Square Background', 'porto-functionality' ),
					'advanced'   => __( 'Design your own', 'porto-functionality' ),
				),
				'default'     => 'none',
			)
		);

		$this->add_control(
			'img_width',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Image Width', 'porto-functionality' ),
				'range'       => array(
					'px' => array(
						'step' => 1,
						'min'  => 16,
						'max'  => 512,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 48,
				),
				'description' => __( 'Provide image width', 'porto-functionality' ),
				'condition'   => array(
					'icon_type' => 'custom',
				),
				'selectors'   => array(
					'{{WRAPPER}} .porto-sicon-img' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Icon Size', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 12,
						'max'  => 72,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 32,
				),
				'condition' => array(
					'icon_type' => 'icon',
				),
				'selectors' => array(
					'{{WRAPPER}} .porto-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'default'   => '#333333',
				'condition' => array(
					'icon_type' => 'icon',
				),
				'selectors' => array(
					'{{WRAPPER}} .porto-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color_bg',
			array(
				'type'        => Controls_Manager::COLOR,
				'label'       => __( 'Background Color', 'porto-functionality' ),
				'default'     => '#ffffff',
				'description' => __( 'Select background color for icon.', 'porto-functionality' ),
				'condition'   => array(
					'icon_style' => array( 'circle', 'circle_img', 'square', 'advanced' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .porto-sicon-img.porto-u-circle-img:before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .porto-sicon-img' => 'background: {{VALUE}};',
					'{{WRAPPER}} .porto-icon'      => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'icon_bd',
				'selector'  => '{{WRAPPER}} .porto-sicon-img, {{WRAPPER}} .porto-icon.advanced',
				'condition' => array(
					'icon_style' => array( 'circle_img', 'advanced' ),
				),
			)
		);

		$this->add_control(
			'icon_border_radius',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Border Radius (px)', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 500,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 500,
				),
				'selectors' => array(
					'{{WRAPPER}} .porto-sicon-img'     => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .porto-icon.advanced' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_border_spacing',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Background Size', 'porto-functionality' ),
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 500,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 50,
				),
				'condition' => array(
					'icon_style' => array( 'advanced' ),
					'icon_type!' => 'custom',
				),
				'selectors' => array(
					'{{WRAPPER}} .porto-icon.advanced' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_pd',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Spacing', 'porto-functionality' ),
				'range'       => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 500,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 50,
				),
				'description' => __( 'Spacing from center of the icon till the boundary of border / background', 'porto-functionality' ),
				'condition'   => array(
					'icon_style' => array( 'circle_img', 'advanced' ),
					'icon_type'  => 'custom',
				),
				'selectors'   => array(
					'{{WRAPPER}} .porto-sicon-img.porto-u-circle-img:before' => 'border-width: calc({{SIZE}}{{UNIT}} + 1px);',
					'{{WRAPPER}} .porto-sicon-img' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_margin',
			array(
				'label'       => esc_html__( 'Icon Margin', 'porto-functionality' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => array(
					'px',
					'em',
					'rem',
				),
				'selectors'   => array(
					'{{WRAPPER}} .porto-icon, {{WRAPPER}} .porto-sicon-img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'qa_selector' => '.porto-icon, .porto-sicon-img',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_counter_style_text',
			array(
				'label' => __( 'Text', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_google_font_style',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Counter Title Typography', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .stats-text',
			)
		);

		$this->add_control(
			'counter_color_txt1',
			array(
				'type'        => Controls_Manager::COLOR,
				'label'       => __( 'Counter Title Color', 'porto-functionality' ),
				'description' => __( 'Select text color for counter title.', 'porto-functionality' ),
				'selectors'   => array(
					'{{WRAPPER}} .stats-text' => 'color: {{VALUE}};',
				),
				'qa_selector' => '.stats-text',
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_google_font_style',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Counter Value Typography', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .stats-number',
			)
		);

		$this->add_control(
			'desc_font_color1',
			array(
				'type'        => Controls_Manager::COLOR,
				'label'       => __( 'Counter Value Color', 'porto-functionality' ),
				'description' => __( 'Select text color for counter digits.', 'porto-functionality' ),
				'selectors'   => array(
					'{{WRAPPER}} .stats-number' => 'color: {{VALUE}};',
				),
				'qa_selector' => '.stats-number',
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'suf_pref_google_font_style',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Counter suffix-prefix Typography', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .counter_prefix, {{WRAPPER}} .counter_suffix',
			)
		);

		$this->add_control(
			'suf_pref_font_color1',
			array(
				'type'        => Controls_Manager::COLOR,
				'label'       => __( 'Counter suffix-prefix Color', 'porto-functionality' ),
				'description' => __( 'Select text color for counter prefix and suffix.', 'porto-functionality' ),
				'selectors'   => array(
					'{{WRAPPER}} .counter_prefix, {{WRAPPER}} .counter_suffix' => 'color: {{VALUE}};',
				),
				'qa_selector' => '.counter_prefix, .counter_suffix',
			)
		);

		$this->add_control(
			'spacing',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Spacing between Title & Value', 'porto-functionality' ),
				'range'       => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
				),
				'default'     => array(
					'unit' => 'px',
				),
				'size_units'  => array(
					'px',
					'rem',
				),
				'selectors'   => array(
					'{{WRAPPER}} .stats-text' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'qa_selector' => '.stats-desc',
			)
		);

		$this->add_control(
			'wrap_margin_bottom',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Margin Bottom', 'porto-functionality' ),
				'range'       => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
				),
				'default'     => array(
					'unit' => 'px',
				),
				'size_units'  => array(
					'px',
					'rem',
				),
				'description' => __( 'Default is 35px.', 'porto-functionality' ),
				'selectors'   => array(
					'{{WRAPPER}} .stats-block' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( isset( $atts['icon_cl'] ) && isset( $atts['icon_cl']['value'] ) ) {
			if ( isset( $atts['icon_cl']['library'] ) && isset( $atts['icon_cl']['value']['id'] ) ) {
				$atts['icon_type'] = $atts['icon_cl']['library'];
				$atts['icon']      = $atts['icon_cl']['value']['id'];
			} else {
				$atts['icon'] = $atts['icon_cl']['value'];
			}
		}

		$atts['img_width']           = '';
		$atts['icon_size']           = '';
		$atts['icon_border_size']    = '';
		$atts['icon_border_radius']  = '';
		$atts['icon_border_spacing'] = '';
		$atts['font_size_title']     = '';
		$atts['font_size_counter']   = '';
		$atts['icon_color']          = '';
		$atts['icon_color_bg']       = '';
		if ( is_array( $atts['icon_img'] ) && isset( $atts['icon_img']['id'] ) ) {
			$atts['icon_img'] = (int) $atts['icon_img']['id'];
		}

		if ( $template = porto_shortcode_template( 'porto_stat_counter' ) ) {
			$this->add_inline_editing_attributes( 'counter_title' );
			$this->add_render_attribute( 'counter_title', 'class', 'stats-text' );
			$title_attrs = $this->get_render_attribute_string( 'counter_title' );
			if ( function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) {
				$counter_init_value = $atts['counter_value'];
			}

			include $template;
		}
	}

	protected function content_template() {
		?>
		<#
			view.addRenderAttribute( 'wrapper', 'class', 'stats-block stats-' + settings.icon_position );

			view.addRenderAttribute( 'counter_title', 'class', 'stats-text' );
			view.addInlineEditingAttributes( 'counter_title' );

			var box_html = '<div class="porto-sicon-' + settings.icon_position + '"><div class="porto-just-icon-wrapper">';
			if ( 'custom' == settings.icon_type ) {
				view.addRenderAttribute( 'porto-sicon-img', 'class', 'porto-sicon-img' );
				if ( 'circle' == settings.icon_style ) {
					view.addRenderAttribute( 'porto-sicon-img', 'class', 'porto-u-circle' );
				}
				if ( 'circle_img' == settings.icon_style ) {
					view.addRenderAttribute( 'porto-sicon-img', 'class', 'porto-u-circle-img' );
				}
				if ( 'square' == settings.icon_style ) {
					view.addRenderAttribute( 'porto-sicon-img', 'class', 'porto-u-square' );
				}
				if ( settings.icon_img.url ) {
					box_html += '<div ' + view.getRenderAttributeString( 'porto-sicon-img' ) + '>';
					box_html += '<img class="img-icon" src="' + settings.icon_img.url + '" />';
					box_html += '</div>';
				}
			} else if ( settings.icon_cl.value ) {
				view.addRenderAttribute( 'porto-icon', 'class', 'porto-icon' );
				if ( settings.icon_style ) {
					view.addRenderAttribute( 'porto-icon', 'class', settings.icon_style );
				}
				box_html += '<div ' + view.getRenderAttributeString( 'porto-icon' ) + '>';
				box_html += '<i class="' + settings.icon_cl.value + '"></i>';
				box_html += '</div>';
			}
			box_html += '</div></div>';

		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<# if ( 'right' !== settings.icon_position ) { #>
				{{{ box_html }}}
			<# } #>
			<div class="stats-desc">
				<# if ( settings.counter_prefix ) { #>
					<div class="counter_prefix mycust" style="display: inline-block;">{{{ settings.counter_prefix }}}</div>
				<# } #>
				<div class="stats-number" data-speed="{{{ settings.speed }}}" data-counter-value="{{{ settings.counter_value }}}" data-separator="{{{ settings.counter_sep }}}" data-decimal="{{{ settings.counter_decimal }}}">{{{ settings.counter_value }}}</div>
				<# if ( settings.counter_suffix ) { #>
					<div class="counter_suffix mycust" style="display: inline-block;">{{{ settings.counter_suffix }}}</div>
				<# } #>
				<div {{{ view.getRenderAttributeString( 'counter_title' ) }}}>{{{ settings.counter_title }}}</div>
			</div>
			<# if ( 'right' === settings.icon_position ) { #>
				{{{ box_html }}}
			<# } #>
		</div>
		<?php
	}
}

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Info Box Widget
 *
 * Porto Elementor widget to display icon boxes.
 *
 * @since 5.1.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Info_Box_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_info_box';
	}

	public function get_title() {
		return __( 'Info Box', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'theme-elements' );
	}

	public function get_keywords() {
		return array( 'icon', 'info box', 'image box', 'icon box' );
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_info_box',
			array(
				'label' => __( 'Info Box', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'       => __( 'Icon to display', 'porto-functionality' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'icon'   => __( 'Icon Fonts', 'porto-functionality' ),
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
			'icon_img_url',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'External Image Url', 'porto-functionality' ),
				'condition' => array(
					'icon_type' => array( 'custom' ),
				),
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
					'{{WRAPPER}} .porto-icon'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .porto-icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_style',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Icon Style', 'porto-functionality' ),
				'options' => array(
					'none'       => __( 'Simple', 'porto-functionality' ),
					'circle'     => __( 'Circle Background', 'porto-functionality' ),
					'circle_img' => __( 'Circle Image', 'porto-functionality' ),
					'square'     => __( 'Square Background', 'porto-functionality' ),
					'advanced'   => __( 'Design your own', 'porto-functionality' ),
				),
				'default' => 'none',
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

		$this->add_control(
			'icon_border_style',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Icon Border Style', 'porto-functionality' ),
				'options'     => array(
					''       => __( 'None', 'porto-functionality' ),
					'solid'  => __( 'Solid', 'porto-functionality' ),
					'dashed' => __( 'Dashed', 'porto-functionality' ),
					'dotted' => __( 'Dotted', 'porto-functionality' ),
					'double' => __( 'Double', 'porto-functionality' ),
					'inset'  => __( 'Inset', 'porto-functionality' ),
					'outset' => __( 'Outset', 'porto-functionality' ),
				),
				'default'     => '',
				'description' => __( 'Select the border style for icon.', 'porto-functionality' ),
				'condition'   => array(
					'icon_style' => array( 'circle_img', 'advanced' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .porto-sicon-img'     => 'border-style: {{VALUE}};',
					'{{WRAPPER}} .porto-icon.advanced' => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color_border',
			array(
				'type'        => Controls_Manager::COLOR,
				'label'       => __( 'Border Color', 'porto-functionality' ),
				'value'       => '#333333',
				'description' => __( 'Select border color for icon.', 'porto-functionality' ),
				'condition'   => array(
					'icon_style'         => array( 'circle_img', 'advanced' ),
					'icon_border_style!' => '',
				),
				'selectors'   => array(
					'{{WRAPPER}} .porto-sicon-img'     => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .porto-icon.advanced' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_border_size',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Border Width', 'porto-functionality' ),
				'range'       => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 10,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 1,
				),
				'description' => __( 'Thickness of the border.', 'porto-functionality' ),
				'condition'   => array(
					'icon_style'         => array( 'circle_img', 'advanced' ),
					'icon_border_style!' => '',
				),
				'selectors'   => array(
					'{{WRAPPER}} .porto-sicon-img'     => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .porto-icon.advanced' => 'border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_border_radius',
			array(
				'type'      => Controls_Manager::SLIDER,
				'label'     => __( 'Border Radius', 'porto-functionality' ),
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
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Background Size', 'porto-functionality' ),
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
				),
				'selectors'   => array(
					'{{WRAPPER}} .porto-sicon-img.porto-u-circle-img:before' => 'border-width: calc({{SIZE}}{{UNIT}} + 1px);',
					'{{WRAPPER}} .porto-sicon-img'     => 'padding: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .porto-icon.advanced' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Title', 'porto-functionality' ),
				'description' => __( 'Provide the title for this icon box.', 'porto-functionality' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Sub title', 'porto-functionality' ),
				'description' => __( 'Provide the sub title for this icon box.', 'porto-functionality' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'content',
			array(
				'type'        => Controls_Manager::WYSIWYG,
				'label'       => __( 'Description', 'porto-functionality' ),
				'description' => __( 'Provide the description for this icon box.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'read_more',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Apply link to:', 'porto-functionality' ),
				'options' => array(
					'none'  => __( 'No Link', 'porto-functionality' ),
					'box'   => __( 'Complete Box', 'porto-functionality' ),
					'title' => __( 'Box Title', 'porto-functionality' ),
					'more'  => __( 'Display Read More', 'porto-functionality' ),
				),
				'default' => 'none',
			)
		);

		$this->add_control(
			'link',
			array(
				'type'        => Controls_Manager::URL,
				'label'       => __( 'Add Link', 'porto-functionality' ),
				'description' => __( 'Add a custom link or select existing page.', 'porto-functionality' ),
				'condition'   => array(
					'read_more' => array( 'box', 'title', 'more' ),
				),
			)
		);

		$this->add_control(
			'read_text',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Read More Text', 'porto-functionality' ),
				'default'     => 'Read More',
				'description' => __( 'Customize the read more text.', 'porto-functionality' ),
				'condition'   => array(
					'read_more' => 'more',
				),
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Select Hover Effect type', 'porto-functionality' ),
				'options'     => array(
					'style_1' => __( 'No Effect', 'porto-functionality' ),
					'style_2' => __( 'Icon Zoom', 'porto-functionality' ),
					'style_3' => __( 'Icon Bounce Up', 'porto-functionality' ),
				),
				'default'     => 'style_1',
				'description' => __( 'Select the type of effct you want on hover', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'pos',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Box Style', 'porto-functionality' ),
				'options'     => array(
					'default'       => __( 'Icon at Left with heading', 'porto-functionality' ),
					'heading-right' => __( 'Icon at Right with heading', 'porto-functionality' ),
					'left'          => __( 'Icon at Left', 'porto-functionality' ),
					'right'         => __( 'Icon at Right', 'porto-functionality' ),
					'top'           => __( 'Icon at Top', 'porto-functionality' ),
				),
				'default'     => 'default',
				'description' => __( 'Select icon position. Icon box style will be changed according to the icon position.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'h_align',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Horizontal Align', 'porto-functionality' ),
				'options'   => array(
					'left'   => __( 'Left', 'porto-functionality' ),
					'right'  => __( 'Right', 'porto-functionality' ),
					'center' => __( 'Center', 'porto-functionality' ),
				),
				'default'   => 'center',
				'condition' => array(
					'pos' => 'top',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_info_box_font_options',
			array(
				'label' => __( 'Style', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_tag',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Title Tag', 'porto-functionality' ),
				'options'     => array(
					'h3' => __( 'Default', 'porto-functionality' ),
					'h1' => __( 'H1', 'porto-functionality' ),
					'h2' => __( 'H2', 'porto-functionality' ),
					'h4' => __( 'H4', 'porto-functionality' ),
					'h5' => __( 'H5', 'porto-functionality' ),
					'h6' => __( 'H6', 'porto-functionality' ),
				),
				'default'     => 'h3',
				'description' => __( 'Default is H3', 'porto-functionality' ),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_google_font_style',
				'scheme'   => Elementor\Scheme_Typography::TYPOGRAPHY_1,
				'label'    => __( 'Title Typograhy', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .porto-sicon-title',
			)
		);

		$this->add_control(
			'title_font_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Title Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .porto-sicon-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'sub_title_google_font_style',
				'scheme'   => Elementor\Scheme_Typography::TYPOGRAPHY_1,
				'label'    => __( 'Sub Title Typograhy', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .porto-sicon-header p',
			)
		);

		$this->add_control(
			'subtitle_font_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Sub Title Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .porto-sicon-header p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_google_font_style',
				'scheme'   => Elementor\Scheme_Typography::TYPOGRAPHY_1,
				'label'    => __( 'Description Typograhy', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .porto-sicon-description',
			)
		);

		$this->add_control(
			'desc_font_color1',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Description Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .porto-sicon-description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_margin_bottom',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Icon Margin Bottom', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
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
					'rem',
				),
				'selectors'  => array(
					'{{WRAPPER}} .porto-just-icon-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_margin_right',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Icon Margin Right', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
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
					'rem',
				),
				'selectors'  => array(
					'{{WRAPPER}} .porto-icon, {{WRAPPER}} .porto-sicon-img' => 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'title_margin_bottom',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Title Margin Bottom', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
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
					'rem',
				),
				'selectors'  => array(
					'{{WRAPPER}} .porto-sicon-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'sub_title_margin_bottom',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Header Margin Bottom', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
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
					'rem',
				),
				'selectors'  => array(
					'{{WRAPPER}} .porto-sicon-header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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

		$atts['icon_size']           = '';
		$atts['icon_border_size']    = '';
		$atts['icon_border_radius']  = '';
		$atts['icon_border_spacing'] = '';
		if ( is_array( $atts['icon_img'] ) && ! empty( $atts['icon_img']['id'] ) ) {
			$atts['img_width'] = '';
			$atts['icon_img']  = (int) $atts['icon_img']['id'];
		} elseif ( ! empty( $atts['icon_img_url'] ) && ( empty( $atts['icon_img'] ) || empty( $atts['icon_img']['id'] ) ) ) {
			$atts['icon_img'] = $atts['icon_img_url'];
			if ( isset( $atts['img_width'] ) && is_array( $atts['img_width'] ) ) {
				$atts['img_width'] = $atts['img_width']['size'];
			}
		} else {
			$atts['img_width'] = '';
		}

		if ( $template = porto_shortcode_template( 'porto_info_box' ) ) {
			$this->add_inline_editing_attributes( 'title' );
			$this->add_render_attribute( 'title', 'class', 'porto-sicon-title' );
			$this->add_inline_editing_attributes( 'subtitle' );
			$this->add_inline_editing_attributes( 'content', 'advanced' );
			$title_attrs_escaped             = ' ' . $this->get_render_attribute_string( 'title' );
			$subtitle_attrs_escaped          = ' ' . $this->get_render_attribute_string( 'subtitle' );
			$desc_attrs_escaped              = ' ' . $this->get_render_attribute_string( 'content' );
			$atts['icon_color']              = '';
			$atts['icon_color_bg']           = '';
			$atts['icon_color_border']       = '';
			$atts['icon_border_style']       = '';
			$atts['icon_margin_bottom']      = '';
			$atts['icon_margin_right']       = '';
			$atts['title_margin_bottom']     = '';
			$atts['sub_title_margin_bottom'] = '';
			include $template;
		}
	}

	protected function _content_template() {
		?>
		<#
			if ( settings.icon_img_url && ! settings.icon_img.url ) {
				settings.icon_img.url = settings.icon_img_url;
			}
			view.addRenderAttribute( 'wrapper', 'class', 'porto-sicon-box' );
			if ( 'top' == settings.pos && 'center' != settings.h_align ) {
				view.addRenderAttribute( 'wrapper', 'class', 'text-' + settings.h_align );
			}
			if ( settings.hover_effect ) {
				view.addRenderAttribute( 'wrapper', 'class', settings.hover_effect );
			}
			if ( settings.pos ) {
				view.addRenderAttribute( 'wrapper', 'class', settings.pos + '-icon' );
				if ( 'default' == settings.pos && settings.content ) {
					view.addRenderAttribute( 'wrapper', 'class', 'flex-wrap' );
				}
				view.addRenderAttribute( 'icon-wrapper', 'class', 'porto-sicon-' + settings.pos );
			}

			var box_html = '';
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
					box_html += '<div style="display: inline-block;" ' + view.getRenderAttributeString( 'porto-sicon-img' ) + '>';
					box_html += '<img class="img-icon" src="' + settings.icon_img.url + '" />';
					box_html += '</div>';
				}
			} else if ( settings.icon_cl.value ) {
				view.addRenderAttribute( 'porto-icon', 'class', 'porto-icon' );
				if ( settings.icon_style ) {
					view.addRenderAttribute( 'porto-icon', 'class', settings.icon_style );
				}
				box_html += '<div style="display: inline-block;" ' + view.getRenderAttributeString( 'porto-icon' ) + '>';
				box_html += '<i class="' + settings.icon_cl.value + '"></i>';
				box_html += '</div>';
			}
			if ( box_html ) {
				box_html = '<div class="porto-just-icon-wrapper">' + box_html + '</div>';
			}

			if ( settings.link && settings.link.url ) {
				view.addRenderAttribute( 'link', 'href', settings.link.url );
				if ( 'more' == settings.read_more ) {
					view.addRenderAttribute( 'link', 'class', 'porto-sicon-read' );
				} else {
					view.addRenderAttribute( 'link', 'class', 'porto-sicon-box-link' );
				}
			}

			view.addRenderAttribute( 'title', 'class', 'porto-sicon-title' );
			view.addInlineEditingAttributes( 'title' );
			view.addInlineEditingAttributes( 'subtitle' );
			view.addInlineEditingAttributes( 'content', 'advanced' );
		#>
		<# if ( settings.link && 'box' == settings.read_more ) { #>
		<a {{{ view.getRenderAttributeString( 'link' ) }}}>
		<# } #>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<# if ( 'heading-right' == settings.pos || 'right' == settings.pos ) { #>
				<# if ( 'right' == settings.pos ) { #>
					<div class="porto-sicon-body">
				<# } #>
				<# if ( settings.title || settings.subtitle ) { #>
					<div class="porto-sicon-header">
						<# if ( settings.title ) { #>
							<# if ( settings.link && 'title' == settings.read_more ) { #>
								<a {{{ view.getRenderAttributeString( 'link' ) }}}>
							<# } #>
							<{{{ settings.heading_tag }}} {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</{{{ settings.heading_tag }}}>
							<# if ( settings.link && 'title' == settings.read_more ) { #>
								</a>
							<# } #>
						<# } #>
						<# if ( settings.subtitle ) { #>
							<p {{{ view.getRenderAttributeString( 'subtitle' ) }}}>{{{ settings.subtitle }}}</p>
						<# } #>
					</div>
				<# } #>
				<# if ( 'right' !== settings.pos ) { #>
					<# if ( 'none' !== settings.icon_cl.value || settings.icon_img.url ) { #>
						<div {{{ view.getRenderAttributeString( 'icon-wrapper' ) }}}>{{{ box_html }}}</div>
					<# } #>
				<# } #>
				<# if ( settings.content ) { #>
					<div class="porto-sicon-description" {{{ view.getRenderAttributeString( 'description' ) }}}>
					{{{ settings.content }}}
					<# if ( settings.link && 'more' == settings.read_more ) { #>
						<a {{{ view.getRenderAttributeString( 'link' ) }}}>
						{{{ settings.read_text }}}
						<span>&nbsp;&raquo;</span>
						</a>
					<# } #>
					</div>
				<# } #>
				<# if ( 'right' == settings.pos ) { #>
					</div>
					<# if ( 'none' !== settings.icon_cl.value || settings.icon_img.url ) { #>
						<div {{{ view.getRenderAttributeString( 'icon-wrapper' ) }}}>{{{ box_html }}}</div>
					<# } #>
				<# } #>
			<# } else { #>
				<# if ( 'none' !== settings.icon_cl.value || settings.icon_img.url ) { #>
					<div {{{ view.getRenderAttributeString( 'icon-wrapper' ) }}}>{{{ box_html }}}</div>
				<# } #>
				<# if ( 'left' == settings.pos ) { #>
					<div class="porto-sicon-body">
				<# } #>
				<# if ( settings.title || settings.subtitle ) { #>
					<div class="porto-sicon-header">
						<# if ( settings.title ) { #>
							<# if ( settings.link && 'title' == settings.read_more ) { #>
								<a {{{ view.getRenderAttributeString( 'link' ) }}}>
							<# } #>
							<{{{ settings.heading_tag }}} {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</{{{ settings.heading_tag }}}>
							<# if ( settings.link && 'title' == settings.read_more ) { #>
								</a>
							<# } #>
						<# } #>
						<# if ( settings.subtitle ) { #>
							<p {{{ view.getRenderAttributeString( 'subtitle' ) }}}>{{{ settings.subtitle }}}</p>
						<# } #>
					</div>
				<# } #>

				<# if ( settings.content || ( settings.link && 'more' == settings.read_more && settings.read_text ) ) { #>
					<div class="porto-sicon-description" {{{ view.getRenderAttributeString( 'content' ) }}}>
					{{{ settings.content }}}
					<# if ( settings.link && 'more' == settings.read_more ) { #>
						<a {{{ view.getRenderAttributeString( 'link' ) }}}>
						{{{ settings.read_text }}}
						<span>&nbsp;&raquo;</span>
						</a>
					<# } #>
					</div>
				<# } #>

				<# if ( 'left' == settings.pos ) { #>
					</div>
				<# } #>
			<# } #>
		</div>
		<# if ( settings.link && 'box' == settings.read_more ) { #>
		</a>
		<# } #>
		<?php
	}
}

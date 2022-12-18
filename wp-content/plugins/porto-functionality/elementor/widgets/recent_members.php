<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Recent Members slider Widget
 *
 * Porto Elementor widget to display recent members slider.
 *
 * @since 1.7.2
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;

class Porto_Elementor_Recent_Members_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_recent_members';
	}

	public function get_title() {
		return __( 'Porto Members Carousel', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'recent member', 'person', 'author', 'carousel', 'slider' );
	}

	public function get_icon() {
		return 'eicon-carousel';
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
			'section_members',
			array(
				'label' => __( 'Member Layout', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'porto-functionality' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Title', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'view',
			array(
				'label'   => __( 'View Type', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'classic',
				'options' => array_combine( array_values( porto_sh_commons( 'member_view' ) ), array_keys( porto_sh_commons( 'member_view' ) ) ),
			)
		);

		$this->add_control(
			'hover_image_effect',
			array(
				'label'       => __( 'Hover Image Effect', 'porto-functionality' ),
				'description' => __( 'Controls the hover effect of image.', 'porto' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array_combine( array_values( porto_sh_commons( 'custom_zoom' ) ), array_keys( porto_sh_commons( 'custom_zoom' ) ) ),
				'default'     => 'zoom',
			)
		);

		$this->add_control(
			'overview',
			array(
				'type'    => Controls_Manager::SWITCHER,
				'label'   => __( 'Show Overview', 'porto-functionality' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'socials',
			array(
				'type'    => Controls_Manager::SWITCHER,
				'label'   => __( 'Show Social Links', 'porto-functionality' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'socials_style',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Use Social Links Advance Style', 'porto-functionality' ),
				'default'   => 'yes',
				'condition' => array(
					'socials' => 'yes',
				),
			)
		);

		$this->add_control(
			'spacing',
			array(
				'type'               => Controls_Manager::NUMBER,
				'label'              => __( 'Column Spacing (px)', 'porto-functionality' ),
				'render_type'        => 'template',
				'frontend_available' => true,
				'selectors'          => array(
					'.elementor-element-{{ID}} .porto-recent-members' => '--porto-el-spacing: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'items',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items to show on Large Desktop', 'porto-functionality' ),
				'default' => '',
				'min'     => 1,
				'max'     => 10,
			)
		);

		$this->add_control(
			'items_desktop',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items to show on Desktop', 'porto-functionality' ),
				'default' => 4,
				'min'     => 1,
				'max'     => 10,
			)
		);

		$this->add_control(
			'items_tablets',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items to show on Tablets', 'porto-functionality' ),
				'default' => 3,
				'min'     => 1,
				'max'     => 6,
			)
		);

		$this->add_control(
			'items_mobile',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items to show on Mobile', 'porto-functionality' ),
				'default' => 2,
				'min'     => 1,
				'max'     => 4,
			)
		);

		$this->add_control(
			'items_row',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Items Row', 'porto-functionality' ),
				'default' => 1,
				'min'     => 1,
				'max'     => 3,
			)
		);

		$this->add_control(
			'cats',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Category IDs', 'porto-functionality' ),
				'description' => __( 'comma separated list of category ids', 'porto-functionality' ),
				'options'     => 'member_cat',
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->add_control(
			'number',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Members Count', 'porto-functionality' ),
				'default' => 8,
				'min'     => 1,
				'max'     => 100,
			)
		);

		$this->add_control(
			'ajax_load',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Enable Ajax Load', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'ajax_modal',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Ajax Load on Modal', 'porto-functionality' ),
				'condition' => array(
					'ajax_load' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_members_slider_options',
			array(
				'label' => __( 'Slider Options', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'stage_padding',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Stage Padding (px)', 'porto-functionality' ),
				'default' => '',
				'min'     => 0,
				'max'     => 100,
			)
		);

		$this->add_control(
			'slider_config',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Change Slider Options', 'porto-functionality' ),
			)
		);

		$slider_options = porto_vc_product_slider_fields();
		unset( $slider_options[2], $slider_options[6], $slider_options[8], $slider_options[9] );
		$slider_options[1]               = array(
			'type'       => 'dropdown',
			'heading'    => __( 'Nav Position', 'porto-functionality' ),
			'param_name' => 'nav_pos',
			'value'      => array(
				__( 'Middle', 'porto-functionality' ) => '',
				__( 'Middle Inside', 'porto-functionality' ) => 'nav-pos-inside',
				__( 'Middle Outside', 'porto-functionality' ) => 'nav-pos-outside',
				__( 'Top', 'porto-functionality' )    => 'show-nav-title',
				__( 'Bottom', 'porto-functionality' ) => 'nav-bottom',
			),
			'dependency' => array(
				'element'   => 'show_nav',
				'not_empty' => true,
			),
		);
		$slider_options[0]['param_name'] = 'show_nav';
		$slider_options[5]['param_name'] = 'show_dots';

		$slider_options[1]['dependency']['element'] = 'show_nav';
		$slider_options[4]['dependency']['element'] = 'show_nav';
		$slider_options[7]['dependency']['element'] = 'show_dots';

		$slider_options = porto_update_vc_options_to_elementor( $slider_options );
		unset( $slider_options['show_nav']['default'] );
		$slider_options['nav_type']['condition'] = array( 'show_nav' => 'yes' );

		foreach ( $slider_options as $key => $opt ) {
			unset( $opt['condition']['view'] );
			$opt['condition']['slider_config'] = 'yes';
			if( ! empty( $opt['responsive'] ) ) {
				$this->add_responsive_control( $key, $opt );
			} else {
				$this->add_control( $key, $opt );
			}
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => esc_html__( 'Name', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '{{WRAPPER}} .thumb-info-title, .elementor-element-{{ID}} .member-item h4',
			)
		);
		$this->add_control(
			'title_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .thumb-info-title, .elementor-element-{{ID}} .member-item h4' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'title_pd',
			array(
				'label'     => __( 'Padding', 'porto-functionality' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .thumb-info-title, .elementor-element-{{ID}} .member-item h4' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->start_controls_tabs( 'members_title_style' );
		$this->start_controls_tab(
			'members_title_normal',
			array(
				'label'     => __( 'Normal', 'porto-functionality' ),
				'condition' => array(
					'view' => array( 'classic', 'onimage' ),
				),
			)
		);
		$this->add_control(
			'title_bgc',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'selectors' => array(
					'{{WRAPPER}} .thumb-info-title' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'view' => array( 'classic', 'onimage' ),
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'members_title_hover',
			array(
				'label'     => __( 'Hover', 'porto-functionality' ),
				'condition' => array(
					'view' => array( 'classic', 'onimage' ),
				),
			)
		);
		$this->add_control(
			'title_bgc_hover',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .thumb-info:hover .thumb-info-title' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cats_style',
			array(
				'label' => esc_html__( 'Categories', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'view'  => 'outimage_cat',
				),
			)
		);
		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'cats_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .member-cats',
			)
		);
		$this->add_control(
			'cats_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .member-cats' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_meta_style',
			array(
				'label' => esc_html__( 'Role', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .thumb-info-type, .elementor-element-{{ID}} .thumb-info-caption-title span, .elementor-element-{{ID}} .member-role',
			)
		);
		$this->add_control(
			'meta_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .thumb-info-type, .elementor-element-{{ID}} .thumb-info-caption-title span, .elementor-element-{{ID}} .member-role' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'meta_pd',
			array(
				'label'     => __( 'Padding', 'porto-functionality' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'.elementor-element-{{ID}} .thumb-info-type, .elementor-element-{{ID}} .thumb-info-caption-title span, .elementor-element-{{ID}} .member-role' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'meta_bgc',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .thumb-info-type' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'view' => array( 'classic', 'onimage' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_desc_style',
			array(
				'label' => esc_html__( 'Description', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .thumb-info-caption-text p',
			)
		);
		$this->add_control(
			'desc_clr',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'porto-functionality' ),
				'selectors' => array(
					'.elementor-element-{{ID}} .thumb-info-caption-text p' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'desc_pd',
			array(
				'label'     => __( 'Padding', 'porto-functionality' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'.elementor-element-{{ID}} .thumb-info-caption-text p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_social_icons_style',
			array(
				'label'     => esc_html__( 'Social Icons', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'socials'        => 'yes',
					'socials_style!' => 'yes',
				),
			)
		);
		$this->add_control(
			'icon_fs',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Icon Font Size', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 6,
						'max'  => 50,
					),
					'em' => array(
						'step' => 0.1,
						'min'  => 0.1,
						'max'  => 5,
					),
				),
				'size_units' => array(
					'px',
					'em',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .share-links a' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'icon_width',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Icon Width and Height', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
					'em' => array(
						'step' => 0.1,
						'min'  => 0.1,
						'max'  => 10,
					),
				),
				'size_units' => array(
					'px',
					'em',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .share-links a' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_icon_style' );
			$this->start_controls_tab(
				'tab_icon_normal',
				array(
					'label' => __( 'Normal', 'porto-functionality' ),
				)
			);
				$this->add_control(
					'icon_color',
					array(
						'type'      => Controls_Manager::COLOR,
						'label'     => __( 'Color', 'porto-functionality' ),
						'selectors' => array(
							'.elementor-element-{{ID}} .share-links a:not(:hover)' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'icon_color_bg',
					array(
						'type'      => Controls_Manager::COLOR,
						'label'     => __( 'Background Color', 'porto-functionality' ),
						'selectors' => array(
							'.elementor-element-{{ID}} .share-links a:not(:hover)' => 'background-color: {{VALUE}};',
						),
					)
				);
				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'icon_box_shadow',
						'selector' => '.elementor-element-{{ID}} .share-links a',
					)
				);
			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_icon_hover',
				array(
					'label' => __( 'Hover', 'porto-functionality' ),
				)
			);
				$this->add_control(
					'icon_hover_color',
					array(
						'type'      => Controls_Manager::COLOR,
						'label'     => __( 'Hover Color', 'porto-functionality' ),
						'selectors' => array(
							'.elementor-element-{{ID}} .share-links a:hover' => 'color: {{VALUE}};',
						),
					)
				);
				$this->add_control(
					'icon_hover_color_bg',
					array(
						'type'      => Controls_Manager::COLOR,
						'label'     => __( 'Hover Background Color', 'porto-functionality' ),
						'selectors' => array(
							'.elementor-element-{{ID}} .share-links a:hover' => 'background-color: {{VALUE}};',
						),
					)
				);
				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => 'icon_box_shadow_hover',
						'selector' => '.elementor-element-{{ID}} .share-links a:hover',
					)
				);
			$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( $template = porto_shortcode_template( 'porto_recent_members' ) ) {
			if ( ! empty( $atts['cats'] ) && is_array( $atts['cats'] ) ) {
				$atts['cats'] = implode( ',', $atts['cats'] );
			}
			include $template;
		}
	}

	protected function content_template() {}
}

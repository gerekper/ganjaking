<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Faqs Widget
 *
 * Porto Elementor widget to display faqs.
 *
 * @since 1.7.4
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

class Porto_Elementor_Faqs_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_faqs';
	}

	public function get_title() {
		return __( 'Porto Faqs', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'faqs', 'posts' );
	}

	public function get_icon() {
		return 'eicon-help-o';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-jquery-infinite-scroll', 'porto-infinite-scroll', 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_faqs',
			array(
				'label' => __( 'Faqs Layout', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'type'  => Controls_Manager::TEXT,
				'label' => __( 'Title', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'cats',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Category IDs', 'porto-functionality' ),
				'description' => __( 'comma separated list of category ids', 'porto-functionality' ),
				'options'     => 'faq_cat',
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->add_control(
			'post_in',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'FAQ IDs', 'porto-functionality' ),
				'description' => __( 'comma separated list of faq ids', 'porto-functionality' ),
				'options'     => 'faq',
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->add_control(
			'number',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'FAQs Count (per page)', 'porto-functionality' ),
				'min'     => 1,
				'max'     => 99,
				'default' => 8,
			)
		);

		$this->add_control(
			'view_more',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Show Archive Link', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'view_more_class',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Extra class name for Archive Link', 'porto-functionality' ),
				'condition' => array(
					'view_more' => 'yes',
				),
			)
		);

		$this->add_control(
			'filter',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Show Category Filter', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'filter_type',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Filter Type', 'porto-functionality' ),
				'options'   => array(
					''     => __( 'Filter using Javascript/CSS', 'porto-functionality' ),
					'ajax' => __( 'Ajax Loading', 'porto-functionality' ),
				),
				'default'   => '',
				'condition' => array(
					'filter' => 'yes',
				),
			)
		);

		$this->add_control(
			'pagination',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Pagination Style', 'porto-functionality' ),
				'options' => array(
					''          => __( 'None', 'porto-functionality' ),
					'yes'       => __( 'Ajax Pagination', 'porto-functionality' ),
					'infinite'  => __( 'Infinite Scroll', 'porto-functionality' ),
					'load_more' => __( 'Load More (Button)', 'porto-functionality' ),
				),
				'default' => '',
			)
		);
		$this->end_controls_section();

		// style options
		$this->start_controls_section(
			'section_faq_options',
			array(
				'label' => __( 'Faq', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'spacing',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Spacing between items', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 50,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
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
					'.elementor-element-{{ID}} .toggle' => 'margin-bottom: {{SIZE}}{{UNIT}}; padding-bottom: 0;',
				),
			)
		);

		$this->start_controls_tabs( 'faq_style' );
		$this->start_controls_tab(
			'faq_item',
			array(
				'label' => __( 'Normal', 'porto-functionality' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'faq_bd',
				'selector' => '.elementor-element-{{ID}} .toggle',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'faq_item_active',
			array(
				'label' => __( 'Active', 'porto-functionality' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'faq_bd_active',
				'selector' => '.elementor-element-{{ID}} .toggle.active',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'faq_br',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius (px)', 'porto-functionality' ),
				'selectors'  => array(
					'.elementor-element-{{ID}} .toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_options',
			array(
				'label' => __( 'Title', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .toggle > label',
			)
		);

		$this->add_control(
			'title_pd',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'porto-functionality' ),
				'selectors'  => array(
					'.elementor-element-{{ID}} .toggle > label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px', 'em', 'rem' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'title_bd',
				'selector' => '.elementor-element-{{ID}} .toggle > label',
				'exclude'  => array(
					'color',
				),
			)
		);

		$this->start_controls_tabs( 'faqs_title_style' );
		$this->start_controls_tab(
			'faqs_title_normal',
			array(
				'label' => __( 'Normal', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'title_bgc',
			array(
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle > label' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_clr',
			array(
				'label'     => __( 'Text Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle > label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_bc',
			array(
				'label'     => __( 'Border Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle > label' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'title_bd_border!' => '',
				),
			)
		);

		$this->add_control(
			'title_br',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius (px)', 'porto-functionality' ),
				'selectors'  => array(
					'.elementor-element-{{ID}} .toggle > label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'fqs_title_hover',
			array(
				'label' => __( 'Hover', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'title_bgc_hover',
			array(
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle > label:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_clr_hover',
			array(
				'label'     => __( 'Text Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle > label:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_bc_hover',
			array(
				'label'     => __( 'Border Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle > label:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'title_bd_border!' => '',
				),
			)
		);

		$this->add_control(
			'title_br_hover',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius (px)', 'porto-functionality' ),
				'selectors'  => array(
					'.elementor-element-{{ID}} .toggle > label:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'fqs_title_active',
			array(
				'label' => __( 'Active', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'title_bgc_active',
			array(
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle.active > label' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_clr_active',
			array(
				'label'     => __( 'Text Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle.active > label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_bc_active',
			array(
				'label'     => __( 'Border Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle.active > label' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'title_bd_border!' => '',
				),
			)
		);

		$this->add_control(
			'title_br_active',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius (px)', 'porto-functionality' ),
				'selectors'  => array(
					'.elementor-element-{{ID}} .toggle.active > label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px' ),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_toggle_options',
			array(
				'label' => __( 'Toggle Icon', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'toggle_rs',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Right Spacing', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 50,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
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
					'.elementor-element-{{ID}} .toggle > label:before' => ( is_rtl() ? 'left' : 'right' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'faqs_toggle_style' );
		$this->start_controls_tab(
			'fqs_toggle_normal',
			array(
				'label' => __( 'Normal', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'toggle_icon',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Icon Type', 'porto-functionality' ),
				'options'   => array(
					''                    => __( 'Custom', 'porto-functionality' ),
					'porto'               => __( 'Porto', 'porto-functionality' ),
					'Font Awesome 5 Free' => __( 'Font Awesome 5', 'porto-functionality' ),
					'Simple-Line-Icons'   => __( 'Simple Line Icons', 'porto-functionality' ),
				),
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle > label:before' => 'font-family: "{{VALUE}}";',
				),
			)
		);

		$this->add_control(
			'toggle_content',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Icon Text', 'porto-functionality' ),
				'description' => __( 'Please input css content value which will be used as toggle icon.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .toggle > label:before' => 'content: "{{VALUE}}"; border: none; line-height: 1; top: 50%;',
				),
			)
		);

		$this->add_control(
			'toggle_fs',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Font Size', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 50,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
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
					'.elementor-element-{{ID}} .toggle > label:before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'toggle_fw',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Font Weight', 'porto-functionality' ),
				'options'   => array(
					''    => __( 'Default', 'porto-functionality' ),
					'300' => '300',
					'400' => '400',
					'500' => '500',
					'600' => '600',
					'700' => '700',
					'800' => '800',
				),
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle > label:before' => 'font-weight: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_clr',
			array(
				'label'     => __( 'Toggle Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle > label:before' => 'color: {{VALUE}};border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_rotate',
			array(
				'label'      => esc_html__( 'Rotate', 'elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default'    => array(
					'unit' => 'deg',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .toggle > label:before' => 'transform: translate3d(0, -50%, 0) rotate({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'fqs_toggle_active',
			array(
				'label' => __( 'Active', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'toggle_icon_active',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Icon Type', 'porto-functionality' ),
				'options'   => array(
					''                    => __( 'Custom', 'porto-functionality' ),
					'porto'               => __( 'Porto', 'porto-functionality' ),
					'Font Awesome 5 Free' => __( 'Font Awesome 5', 'porto-functionality' ),
					'Simple-Line-Icons'   => __( 'Simple Line Icons', 'porto-functionality' ),
				),
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle.active > label:before' => 'font-family: "{{VALUE}}";',
				),
			)
		);

		$this->add_control(
			'toggle_content_active',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Icon Text', 'porto-functionality' ),
				'description' => __( 'Please input css content value which will be used as toggle icon.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .toggle.active > label:before' => 'content: "{{VALUE}}"; border: none; line-height: 1; top: 50%;',
				),
			)
		);

		$this->add_control(
			'toggle_fs_active',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Font Size', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 50,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
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
					'.elementor-element-{{ID}} .toggle.active > label:before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'toggle_fw_active',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Font Weight', 'porto-functionality' ),
				'options'   => array(
					''    => __( 'Default', 'porto-functionality' ),
					'300' => '300',
					'400' => '400',
					'500' => '500',
					'600' => '600',
					'700' => '700',
					'800' => '800',
				),
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle.active > label:before' => 'font-weight: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_clr_active',
			array(
				'label'     => __( 'Toggle Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle.active > label:before' => 'color: {{VALUE}};border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_rotate_active',
			array(
				'label'      => esc_html__( 'Rotate', 'elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default'    => array(
					'unit' => 'deg',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .toggle.active > label:before' => 'transform: translate3d(0, -50%, 0) rotate({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_options',
			array(
				'label' => __( 'Content', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .toggle-content, .elementor-element-{{ID}} .toggle-content>p',
			)
		);

		$this->add_control(
			'content_clr',
			array(
				'label'     => __( 'Text Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle-content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'content_pd',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'porto-functionality' ),
				'selectors'  => array(
					'.elementor-element-{{ID}} .toggle-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px', 'em', 'rem' ),
			)
		);

		$this->add_control(
			'content_bgc',
			array(
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .toggle-content' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'content_bd',
				'selector' => '.elementor-element-{{ID}} .toggle-content',
			)
		);

		$this->add_control(
			'content_br',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius (px)', 'porto-functionality' ),
				'selectors'  => array(
					'.elementor-element-{{ID}} .toggle-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( $template = porto_shortcode_template( 'porto_faqs' ) ) {
			if ( ! empty( $atts['cats'] ) && is_array( $atts['cats'] ) ) {
				$atts['cats'] = implode( ',', $atts['cats'] );
			}
			if ( ! empty( $atts['post_in'] ) && is_array( $atts['post_in'] ) ) {
				$atts['post_in'] = implode( ',', $atts['post_in'] );
			}
			include $template;
		}
	}

	protected function content_template() {}
}

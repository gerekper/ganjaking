<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Shop Builder - Grid / List Toggle Widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;

class Porto_Elementor_SB_Toggle_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_sb_toggle';
	}

	public function get_title() {
		return __( 'Grid / List Toggle', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-sb' );
	}

	public function get_keywords() {
		return array( 'shop', 'woocommerce', 'toggle', 'grid', 'list', 'type' );
	}

	public function get_icon() {
		return 'Simple-Line-Icons-grid';
	}

	public function get_script_depends() {
		return array();
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/shop-builder-elements/';
	}

	protected function register_controls() {

		$right = is_rtl() ? 'left' : 'right';

		$this->start_controls_section(
			'section_toggle_layout',
			array(
				'label' => __( 'Grid / List Toggle', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'notice_skin',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'To change the Products Archive’s layout, go to Porto / Theme Options / WooCommerce / Product Archives.', 'porto-functionality' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'icon_grid',
			array(
				'label'                  => esc_html__( 'Icon', 'porto-functionality' ),
				'description'            => esc_html__( 'Set the grid toggle icon.​', 'porto-functionality' ),
				'type'                   => Controls_Manager::ICONS,
				'default'                => array(
					'value'   => '',
					'library' => '',
				),
				'skin'                   => 'inline',
				'exclude_inline_options' => array( 'svg' ),
				'label_block'            => false,
			)
		);

		$this->add_control(
			'icon_list',
			array(
				'label'                  => esc_html__( 'Icon', 'porto-functionality' ),
				'description'            => esc_html__( 'Set the list toggle icon.​', 'porto-functionality' ),
				'type'                   => Controls_Manager::ICONS,
				'default'                => array(
					'value'   => '',
					'library' => '',
				),
				'skin'                   => 'inline',
				'exclude_inline_options' => array( 'svg' ),
				'label_block'            => false,
			)
		);

		$this->add_control(
			'fs',
			array(
				'label'     => esc_html__( 'Icon Size (px)', 'porto-functionality' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 50,
					),
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .gridlist-toggle > a' => 'font-size: {{SIZE}}px',
				),
			)
		);

		$this->add_control(
			'spacing',
			array(
				'label'       => esc_html__( 'Item Spacing (px)', 'porto-functionality' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 20,
					),
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} #grid' => "margin-{$right}: {{SIZE}}px",
				),
				'description' => esc_html__( 'Adjust spacing between toggle buttons.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'clr',
			array(
				'label'       => esc_html__( 'Color', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the color of the button.', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} a:not(.active)' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'active_clr',
			array(
				'label'       => esc_html__( 'Active Color', 'porto-functionality' ),
				'description' => esc_html__( 'Controls the active color of the button.', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_toggle_style',
			array(
				'label' => __( 'Style Options', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'w',
			array(
				'label'     => esc_html__( 'Width (px)', 'porto-functionality' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .gridlist-toggle > a' => 'width: {{SIZE}}px',
				),
			)
		);

		$this->add_control(
			'h',
			array(
				'label'     => esc_html__( 'Height (px)', 'porto-functionality' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .gridlist-toggle > a' => 'height: {{SIZE}}px',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'bd',
				'selector' => '.cursor-element-{{ID}} .gridlist-toggle > a',
			)
		);

		$this->add_control(
			'active_bs',
			array(
				'label'     => esc_html__( 'Active Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.cursor-element-{{ID}} .gridlist-toggle > .active' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'bd_border!' => '',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( isset( $atts['icon_grid'] ) && isset( $atts['icon_grid']['value'] ) ) {
			if ( isset( $atts['icon_grid']['library'] ) && isset( $atts['icon_grid']['value']['id'] ) ) {
				$atts['icon_grid'] = $atts['icon_grid']['value']['id'];
			} else {
				$atts['icon_grid'] = $atts['icon_grid']['value'];
			}
		}
		if ( isset( $atts['icon_list'] ) && isset( $atts['icon_list']['value'] ) ) {
			if ( isset( $atts['icon_list']['library'] ) && isset( $atts['icon_list']['value']['id'] ) ) {
				$atts['icon_list'] = $atts['icon_list']['value']['id'];
			} else {
				$atts['icon_list'] = $atts['icon_list']['value'];
			}
		}
		include PORTO_BUILDERS_PATH . '/elements/shop/wpb/toggle.php';
	}
}

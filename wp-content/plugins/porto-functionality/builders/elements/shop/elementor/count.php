<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Shop Builder - Count Per Page Widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Typography;

class Porto_Elementor_SB_Count_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_sb_count';
	}

	public function get_title() {
		return __( 'Count Per Page', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-sb' );
	}

	public function get_keywords() {
		return array( 'page', 'shop', 'woocommerce', 'count', 'products', 'pagination', 'loop after' );
	}

	public function get_icon() {
		return 'Simple-Line-Icons-pencil';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/shop-builder-elements/';
	}

	public function get_script_depends() {
		return array();
	}

	protected function register_controls() {
		$right = is_rtl() ? 'left' : 'right';

		$this->start_controls_section(
			'section_count',
			array(
				'label' => esc_html( 'Count Per Page', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_control(
				'notice_skin',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: starting and ending bold tags */
					'raw'             => sprintf( esc_html__( 'You can set these values using %1$sWooCommerce -> Product Archives -> Products%2$s per Page in Theme Options. This displays pagination together when pagination is disabled in Type Builder Archives element.', 'porto-functionality' ), '<b>', '</b>' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_count_label',
			array(
				'label' => esc_html( 'Label', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'label_hide',
				array(
					'label'     => esc_html__( 'Label Visibility', 'porto-functionality' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => array(
						''     => __( 'Default', 'porto-functionality' ),
						'none' => __( 'Hide', 'porto-functionality' ),
					),
					'selectors' => array(
						'.elementor-element-{{ID}} label' => 'display: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'label_color',
				array(
					'label'       => esc_html__( 'Label Color', 'porto-functionality' ),
					'description' => esc_html__( 'Controls color of label.', 'porto-functionality' ),
					'type'        => Controls_Manager::COLOR,
					'selectors'   => array(
						'.elementor-element-{{ID}} label' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'label_typography',
					'label'    => esc_html__( 'Label Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} label',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_count_select',
			array(
				'label' => esc_html( 'Select', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'select_color',
				array(
					'label'       => esc_html__( 'Select box Color', 'porto-functionality' ),
					'description' => esc_html__( 'Controls color of select box.', 'porto-functionality' ),
					'type'        => Controls_Manager::COLOR,
					'selectors'   => array(
						'.elementor-element-{{ID}} select' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'select_typography',
					'label'    => esc_html__( 'Select box Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} select',
				)
			);

			$this->add_responsive_control(
				'select_padding',
				array(
					'label'       => esc_html__( 'Select box Padding', 'porto-functionality' ),
					'description' => esc_html__( 'Controls padding of select box.', 'porto-functionality' ),
					'type'        => Controls_Manager::DIMENSIONS,
					'size_units'  => array(
						'px',
						'%',
						'em',
					),
					'selectors'   => array(
						'.elementor-element-{{ID}} select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'spacing',
				array(
					'label'       => esc_html__( 'Spacing', 'porto-functionality' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 20,
						),
					),
					'selectors'   => array(
						'.elementor-element-{{ID}} label' => "margin-{$right}: {{SIZE}}px",
					),
					'description' => esc_html__( 'Controls spacing between label and select box.', 'porto-functionality' ),
				)
			);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) {
			global $porto_settings;
			$porto_settings['shop_pg_type'] = '';
			include PORTO_BUILDERS_PATH . '/elements/shop/wpb/count.php';
			unset( $porto_settings['shop_pg_type'] );
		} else {
			include PORTO_BUILDERS_PATH . '/elements/shop/wpb/count.php';
		}
	}
}

<?php
/**
 * Porto Elementor Single Post Navigation Widget
 *
 * @author     P-THEMES
 * @since      2.3.0
 */
defined( 'ABSPATH' ) || die;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Typography;

class Porto_Elementor_Single_Navigation_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_single_navigation';
	}

	public function get_title() {
		return esc_html__( 'Post Navigation', 'porto-functionality' );
	}

	public function get_icon() {
		return 'eicon-post-navigation';
	}

	public function get_categories() {
		return array( 'porto-single' );
	}

	public function get_keywords() {
		return array( 'single', 'navigation', 'next', 'preview', 'post', 'meta', 'member', 'portfolio', 'event', 'fap' );
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/post-navigation-single-builder/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_single_nav',
			array(
				'label' => esc_html__( 'Style', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'nav_align',
			array(
				'label'       => esc_html__( 'Alignment', 'porto-functionality' ),
				'description' => esc_html__( 'Controls navigations alignment. Choose from Left, Center, Right.', 'porto-functionality' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'porto-functionality' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'porto-functionality' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'porto-functionality' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .single-navigation' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'nav_space',
			array(
				'label'       => esc_html__( 'Navigation Spacing', 'porto-functionality' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array(
					'px',
					'rem',
					'em',
				),
				'description' => __( 'To control the space between previous and next navigation.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .single-navigation a + a' => 'margin-' . ( is_rtl() ? 'right' : 'left' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nav_icon_prev',
			array(
				'type'                   => Controls_Manager::ICONS,
				'label'                  => __( 'Preview Icon', 'porto-functionality' ),
				'skin'                   => 'inline',
				'default'                => array(
					'value'   => 'porto-icon-chevron-left',
					'library' => '',
				),
				'description'            => __( 'To select the previous icon', 'porto-functionality' ),
				'label_block'            => false,
				'exclude_inline_options' => array( 'svg' ),
			)
		);

		$this->add_control(
			'nav_icon_next',
			array(
				'type'                   => Controls_Manager::ICONS,
				'label'                  => __( 'Next Icon', 'porto-functionality' ),
				'default'                => array(
					'value'   => 'porto-icon-chevron-right',
					'library' => '',
				),
				'description'            => __( 'To select the next icon', 'porto-functionality' ),
				'label_block'            => false,
				'skin'                   => 'inline',
				'exclude_inline_options' => array( 'svg' ),
			)
		);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'        => 'nav_typography',
					'description' => __( 'To select the typography of icon', 'porto-functionality' ),
					'selector'    => '.elementor-element-{{ID}} .single-navigation',
				)
			);

			$this->add_control(
				'nav_color',
				array(
					'label'     => esc_html__( 'Color', 'porto-functionality' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'.elementor-element-{{ID}} .single-navigation a' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'nav_hover_color',
				array(
					'label'     => esc_html__( 'Hover Color', 'porto-functionality' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'.elementor-element-{{ID}} .single-navigation a:hover,.elementor-element-{{ID}} .single-navigation a:focus' => 'color: {{VALUE}}',
					),
				)
			);

		$this->end_controls_section();
	}

	protected function render() {
		$atts                 = $this->get_settings_for_display();
		$atts['page_builder'] = 'elementor';
		echo PortoBuildersSingle::get_instance()->shortcode_single_navigation( $atts );
	}
}

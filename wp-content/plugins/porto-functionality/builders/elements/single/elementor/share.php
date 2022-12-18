<?php
/**
 * Porto Elementor Single Post Share Widget
 *
 * @author     P-THEMES
 * @since      2.3.0
 */
defined( 'ABSPATH' ) || die;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

class Porto_Elementor_Single_Share_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_single_sharen';
	}

	public function get_title() {
		return esc_html__( 'Share', 'porto-functionality' );
	}

	public function get_icon() {
		return 'Simple-Line-Icons-share';
	}

	public function get_categories() {
		return array( 'porto-single' );
	}

	public function get_keywords() {
		return array( 'single', 'share', 'social', 'icon', 'post', 'meta' );
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/post-share-single-builder/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_single_share',
			array(
				'label' => __( 'Style', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'share_inline',
			array(
				'type'    => Controls_Manager::SWITCHER,
				'label'   => __( 'Inline Style', 'porto-functionality' ),
				'default' => '',
			)
		);

		$this->add_control(
			'hide_heading',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Hide Heading', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .post-share > span' => 'display: none;',
				),
			)
		);

		$this->add_control(
			'with_icon',
			array(
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'To hide the heading icon', 'porto-functionality' ),
				'label'       => __( 'Without Icon?', 'porto-functionality' ),
				'condition'   => array(
					'share_inline' => '',
				),
			)
		);

		$this->add_control(
			'icon_space',
			array(
				'label'       => __( 'Icon Spacing', 'porto-functionality' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array(
					'px',
					'rem',
					'em',
				),
				'description' => __( 'To control the space between the icon and heading', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .post-share > span i' => 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ': {{SIZE}}{{UNIT}};',
					'.elementor-element-{{ID}} .post-share > h3 i' => 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ': {{SIZE}}{{UNIT}};',
					'.elementor-element-{{ID}} .post-share > .share-links' => 'display: inline-block;',
				),
			)
		);

		$this->add_control(
			'share_heading_style',
			array(
				'label'     => __( 'Share Heading', 'porto-functionality' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'hide_heading' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'share_heading',
				'selector'  => '.elementor-element-{{ID}} .post-share > h3, .elementor-element-{{ID}} .post-share > span',
				'condition' => array(
					'hide_heading' => '',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'       => __( 'Color', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'description' => __( 'To control the color of the share heading.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .post-share > span' => 'color: {{VALUE}};',
					'.elementor-element-{{ID}} .post-share > h3' => 'color: {{VALUE}};',
				),
				'condition'   => array(
					'hide_heading' => '',
				),
			)
		);

		$this->add_control(
			'title_space',
			array(
				'label'       => __( 'Title Spacing', 'porto-functionality' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array(
					'px',
					'rem',
					'em',
				),
				'description' => __( 'To control the space between the icon and title', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} .post-share h3' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'.elementor-element-{{ID}} .post-share > span' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'hide_heading' => '',
				),
			)
		);

		$this->add_control(
			'share_icon_style',
			array(
				'label'     => __( 'Share Icons', 'porto-functionality' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'share_icons',
				'selector' => '.elementor-element-{{ID}} .share-links a',
			)
		);

		$this->add_control(
			'share_width',
			array(
				'label'       => __( 'Share Width', 'porto-functionality' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'To control the width and height of the share icons.', 'porto-functionality' ),
				'size_units'  => array(
					'px',
					'rem',
					'em',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .share-links a' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'share_radius',
			array(
				'label'       => __( 'Border Radius', 'porto-functionality' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'To control the border radius of the share icons.', 'porto-functionality' ),
				'size_units'  => array(
					'px',
					'rem',
					'em',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .post-share > .share-links a' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'share_space',
			array(
				'label'       => __( 'Share Spacing', 'porto-functionality' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'To control the space between the share icons.', 'porto-functionality' ),
				'size_units'  => array(
					'px',
					'rem',
					'em',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .share-links a:not(:last-child)' => 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'share_icon_box_shadow',
				'selector' => '.elementor-element-{{ID}} .post-share > .share-links a',
			)
		);

		$this->start_controls_tabs( 'tabs_share_icons' );

			$this->start_controls_tab(
				'tab_share_icon_normal',
				array(
					'label' => __( 'Normal', 'porto-functionality' ),
				)
			);

					$this->add_control(
						'share_color_normal',
						array(
							'label'       => __( 'Color', 'porto-functionality' ),
							'type'        => Controls_Manager::COLOR,
							'description' => __( 'To control the color of the share icons.', 'porto-functionality' ),
							'selectors'   => array(
								'.elementor-element-{{ID}} .post-share > .share-links a' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'share_bg_color_normal',
						array(
							'label'       => __( 'Background Color', 'porto-functionality' ),
							'type'        => Controls_Manager::COLOR,
							'description' => __( 'To control the background color of the share icons.', 'porto-functionality' ),
							'selectors'   => array(
								'.elementor-element-{{ID}} .post-share > .share-links a:not(:hover)' => 'background-color: {{VALUE}};',
							),
						)
					);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_share_icon_hover',
				array(
					'label' => __( 'Hover', 'porto-functionality' ),
				)
			);

				$this->add_control(
					'share_color_hover',
					array(
						'label'       => __( 'Color', 'porto-functionality' ),
						'type'        => Controls_Manager::COLOR,
						'description' => __( 'To control the hover color of the share icons.', 'porto-functionality' ),
						'selectors'   => array(
							'.elementor-element-{{ID}} .post-share > .share-links a:hover' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'share_bg_color_hover',
					array(
						'label'       => __( 'Background Color', 'porto-functionality' ),
						'type'        => Controls_Manager::COLOR,
						'description' => __( 'To control the background hover color of the share icons.', 'porto-functionality' ),
						'selectors'   => array(
							'.elementor-element-{{ID}} .post-share > .share-links a:hover' => 'background-color: {{VALUE}};',
						),
					)
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$atts                 = $this->get_settings_for_display();
		$atts['page_builder'] = 'elementor';
		echo PortoBuildersSingle::get_instance()->shortcode_single_share( $atts );
	}
}

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Members Widget
 *
 * Porto Elementor widget to display members.
 *
 * @since 1.7.2
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;

class Porto_Elementor_Members_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_members';
	}

	public function get_title() {
		return __( 'Porto Members', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'member', 'person', 'author' );
	}

	public function get_icon() {
		return 'eicon-person';
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
			'style',
			array(
				'label'   => __( 'Style', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					''         => __( 'Baisc', 'porto-functionality' ),
					'advanced' => __( 'Advanced', 'porto-functionality' ),
				),
				'default' => '',
			)
		);

		$this->add_control(
			'spacing',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Column Spacing (px)', 'porto-functionality' ),
				'description' => __( 'Leave blank if you use theme default value.', 'porto-functionality' ),
				'range'       => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .members-container' => '--porto-el-spacing: {{SIZE}}px;--bs-gutter-x: {{SIZE}}px;',
				),
				'condition'   => array(
					'style' => array( '' ),
				),
			)
		);

		$this->add_control(
			'columns',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Columns', 'porto-functionality' ),
				'condition' => array(
					'style' => array( '' ),
				),
				'default'   => '4',
				'options'   => porto_sh_commons( 'member_columns' ),
			)
		);

		$this->add_control(
			'view',
			array(
				'label'     => __( 'View Type', 'porto-functionality' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'classic',
				'options'   => array_combine( array_values( porto_sh_commons( 'member_view' ) ), array_keys( porto_sh_commons( 'member_view' ) ) ),
				'condition' => array(
					'style' => array( '' ),
				),
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
				'condition'   => array(
					'style' => array( '' ),
				),
			)
		);

		$this->add_control(
			'overview',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Show Overview', 'porto-functionality' ),
				'default'   => 'yes',
				'condition' => array(
					'style' => array( '' ),
				),
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
			'role',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Show Role', 'porto-functionality' ),
				'condition' => array(
					'view' => 'outimage_cat',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_members_selector',
			array(
				'label' => __( 'Members Selector', 'porto-functionality' ),
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
			'post_in',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Member IDs', 'porto-functionality' ),
				'description' => __( 'comma separated list of member ids', 'porto-functionality' ),
				'options'     => 'member',
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->add_control(
			'number',
			array(
				'type'    => Controls_Manager::NUMBER,
				'label'   => __( 'Members Count (per page)', 'porto-functionality' ),
				'default' => 8,
				'min'     => 1,
				'max'     => 100,
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

		$this->add_control(
			'filter',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Show Category Filter', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'filter_style',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Filter Style', 'porto-functionality' ),
				'options'   => array(
					''        => __( 'Style 1', 'porto-functionality' ),
					'style-2' => __( 'Style 2', 'porto-functionality' ),
					'style-3' => __( 'Style 3', 'porto-functionality' ),
				),
				'default'   => '',
				'condition' => array(
					'filter' => 'yes',
				),
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
			'ajax_load',
			array(
				'type'        => Controls_Manager::SWITCHER,
				'label'       => __( 'Enable Ajax Load', 'porto-functionality' ),
				'description' => __( 'If enabled, member content should be displayed at the top of members or on modal when you click member item in the list.', 'porto-functionality' ),
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
			'section_title_style',
			array(
				'label'       => esc_html__( 'Name', 'porto-functionality' ),
				'tab'         => Controls_Manager::TAB_STYLE,
				'qa_selector' => '.member:first-child .entry-title strong, .member:first-child .thumb-info-title, .member:first-child .member-name',
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'title_tg',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .entry-title strong, {{WRAPPER}} .thumb-info-title, .elementor-element-{{ID}} .member-name',
				)
			);
			$this->add_control(
				'title_clr',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .entry-title, {{WRAPPER}} .thumb-info-title, .elementor-element-{{ID}} .member-name' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'title_pd',
				array(
					'label'     => __( 'Padding', 'porto-functionality' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'.elementor-element-{{ID}} .entry-title, {{WRAPPER}} .thumb-info-title, .elementor-element-{{ID}} .member-name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					),
				)
			);

			$this->start_controls_tabs( 'members_title_style' );
				$this->start_controls_tab(
					'members_title_normal',
					array(
						'label'     => __( 'Normal', 'porto-functionality' ),
						'condition' => array(
							'style' => array( '' ),
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
							'style' => array( '' ),
						),
					)
				);
				$this->end_controls_tab();
				$this->start_controls_tab(
					'members_title_hover',
					array(
						'label'     => __( 'Hover', 'porto-functionality' ),
						'condition' => array(
							'style' => array( '' ),
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
						'condition' => array(
							'style' => array( '' ),
						),
					)
				);
				$this->end_controls_tab();
			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cats_style',
			array(
				'label'       => esc_html__( 'Categories', 'porto-functionality' ),
				'tab'         => Controls_Manager::TAB_STYLE,
				'condition'   => array(
					'style' => array( '' ),
					'view'  => 'outimage_cat',
				),
				'qa_selector' => '.member:first-child .member-cats',
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
				'label'       => esc_html__( 'Role', 'porto-functionality' ),
				'tab'         => Controls_Manager::TAB_STYLE,
				'qa_selector' => '.member:first-child .thumb-info-type,.member:first-child .member-role',
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'meta_tg',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .thumb-info-type, .elementor-element-{{ID}} .member-role',
				)
			);
			$this->add_control(
				'meta_clr',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .thumb-info-type, .elementor-element-{{ID}} .member-role' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'meta_pd',
				array(
					'label'     => __( 'Padding', 'porto-functionality' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'.elementor-element-{{ID}} .thumb-info-type, .elementor-element-{{ID}} .member-role' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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
						'style' => array( '' ),
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_desc_style',
			array(
				'label'       => esc_html__( 'Description', 'porto-functionality' ),
				'tab'         => Controls_Manager::TAB_STYLE,
				'qa_selector' => '.member:first-child .thumb-info-caption-text,.member:first-child .member-overview p',
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'desc_tg',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .thumb-info-caption-text, .elementor-element-{{ID}} .thumb-info-caption-text p, .elementor-element-{{ID}} .member-overview p',
				)
			);
			$this->add_control(
				'desc_clr',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Color', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .thumb-info-caption-text, .elementor-element-{{ID}} .member-overview p' => 'color: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'desc_pd',
				array(
					'label'     => __( 'Padding', 'porto-functionality' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'.elementor-element-{{ID}} .thumb-info-caption-text, .elementor-element-{{ID}} .member-overview p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_social_icons_style',
			array(
				'label'       => esc_html__( 'Social Icons', 'porto-functionality' ),
				'tab'         => Controls_Manager::TAB_STYLE,
				'qa_selector' => '.member:first-child .share-links',
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

		if ( $template = porto_shortcode_template( 'porto_members' ) ) {
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

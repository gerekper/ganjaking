<?php
namespace Happy_Addons_Pro\Widget\Skins\Post_Grid;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Skin_Base as Elementor_Skin_Base;

use Happy_Addons_Pro\Traits\Lazy_Query_Builder;
use Happy_Addons_Pro\Traits\Post_Grid_Markup;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Skin_Base extends Elementor_Skin_Base {

	use Lazy_Query_Builder;
	use Post_Grid_Markup;

	/**
	 * @var string Save current permalink to avoid conflict with plugins the filters the permalink during the post render.
	 */
	protected $current_permalink;

	protected function _register_controls_actions() {

		add_action( 'elementor/element/ha-post-grid/_section_layout/before_section_end', [ $this, 'register_controls' ] );

		add_action( 'elementor/element/ha-post-grid/_section_layout_style/after_section_end', [ $this, 'register_style_sections' ] );
	}

	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

        $this->layout_content_tab_controls();

	}

	/**
	 * Layout content controls
	 */
	protected function layout_content_tab_controls( ) {

        $this->add_control(
            'posts_per_page',
            [
                'label'   => __( 'Posts Per Page', 'happy-addons-pro' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 3,
            ]
        );

		/* $this->add_control(
			'offset',
			[
				'label' => __( 'Post Offset Item', 'happy-addons-pro' ),
				'type' => Controls_Manager::NUMBER,
			]
		); */

		$this->featured_image_controls();

		$this->badge_controls();


		$this->add_control(
			'show_title',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => __( 'Title Tag', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => [
					'h1' => __( 'H1', 'happy-addons-pro' ),
					'h2' => __( 'H2', 'happy-addons-pro' ),
					'h3' => __( 'H3', 'happy-addons-pro' ),
					'h4' => __( 'H4', 'happy-addons-pro' ),
					'h5' => __( 'H5', 'happy-addons-pro' ),
					'h6' => __( 'H6', 'happy-addons-pro' ),
					'div' => __( 'DIV', 'happy-addons-pro' ),
				],
				'condition' => [
					$this->get_control_id( 'show_title' ) => 'yes',
				],
			]
		);

		$this->meta_controls();

		$this->add_control(
			'excerpt_length',
			[
				'type'        => Controls_Manager::NUMBER,
				'label'       => __( 'Excerpt Length', 'happy-addons-pro' ),
				'description' => __( 'Leave it blank to hide it.', 'happy-addons-pro' ),
				'separator'   => 'before',
				'min'         => 0,
				'default'     => 15,
			]
		);

	}

	/**
	 * Featured Image Control
	 */
	protected function featured_image_controls() {

		$this->add_control(
			'featured_image',
			[
				'label' => __( 'Featured Image', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'featured_image',
				'default' => 'thumbnail',
				'exclude' => [
					'custom'
				],
				'default' => 'large',
				'condition' => [
					$this->get_control_id( 'featured_image' ) => 'yes',
				]
			]
		);

	}

	/**
	 * Badge Control
	 */
	protected function badge_controls() {

		$this->add_control(
			'show_badge',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'taxonomy_badge',
			[
				'label' => __( 'Badge Taxonomy', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'category',
				'options' => ha_pro_get_taxonomies(),
				'condition' => [
					$this->get_control_id( 'show_badge' ) => 'yes',
				],
			]
		);

	}

	/**
	 * Meta Control
	 */
	protected function meta_controls() {

		$this->add_control(
			'active_meta',
			[
				'type' => Controls_Manager::SELECT2,
				'label' => __( 'Active Meta', 'happy-addons-pro' ),
				'description' => __( 'Select to show and unselect to hide', 'happy-addons-pro' ),
				'label_block' => true,
				'separator' => 'before',
				'multiple' => true,
				'default' => ['author', 'date'],
				'options' => [
					'author' => __( 'Author', 'happy-addons-pro' ),
					'date' => __( 'Date', 'happy-addons-pro' ),
					'comments' => __( 'Comments', 'happy-addons-pro' ),
				]
			]
		);

		$this->add_control(
			'meta_has_icon',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Enable Icon', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					$this->get_control_id( 'active_meta!' ) => [],
				],
			]
		);

		$this->add_control(
			'meta_separator',
			[
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Separator', 'happy-addons-pro' ),
				'selectors' => [
					'{{WRAPPER}} .ha-pg-meta-wrap ul li + li:before' => 'content: "{{VALUE}}"',
				],
				'condition' => [
					$this->get_control_id( 'active_meta!' ) => []
				],
			]
		);

	}

	/**
	 * Register Style controls
	 */
	public function register_style_sections( Widget_Base $widget ) {
		$this->parent = $widget;

		//Box Style Start
		$this->box_style_tab_controls();

		//Feature Image Style Start
		$this->image_style_tab_controls();

		//Badge Taxonomy Style Start
		$this->taxonomy_badge_style_tab_controls( $this->parent  );

		//Content Style Start
		$this->content_style_tab_controls();

		//Meta Style Start
		$this->meta_style_tab_controls();
	}


	/**
	 * Box Style controls
	 */
	protected function box_style_tab_controls() {

		$this->start_controls_section(
			'_section_item_box_style',
			[
				'label' => __( 'Item Box', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_box_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'item_box_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-pg-item',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_box_box_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-pg-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'item_box_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-pg-item',
			]
		);

		$this->add_responsive_control(
			'item_box_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Image Style controls
	 */
	protected function image_style_tab_controls() {

		//Feature Post Image overlay color

		$this->start_controls_section(
			'_section_image_style',
			[
				'label' => __( 'Image', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'featured_image' ) => 'yes',
				],
			]
		);

		$this->all_style_of_feature_image();

		$this->end_controls_section();
	}

	/**
	 * All Image Style
	 */
	protected function all_style_of_feature_image() {

		$this->image_height_margin_style();

		$this->image_boxshadow_style();

		$this->image_border_styles();

		$this->image_border_radius_styles();

		$this->image_css_filter_styles();
	}

	/**
	 * Image Overlay Style
	 */
	protected function image_overlay_style() {

		//Feature Post Image overlay color
		$this->add_control(
			'feature_image_overlay_heading',
			[
				'label' => __( 'Image Overlay', 'happy-addons-pro' ),
				'description' => __( 'This overlay color only apply when post has an image.', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'feature_image_overlay',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'description' => __( 'This overlay color only apply when post has an image.', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-pg-classic .ha-pg-thumb:before',
			]
		);

		$this->add_control(
			'feature_image_heading',
			[
				'label' => __( 'Image', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

	}

	/**
	 * Image Height & margin Style
	 */
	protected function image_height_margin_style() {

		$this->add_responsive_control(
			'feature_image_height',
			[
				'label' => __( 'Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-thumb-area' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'feature_image_margin_btm',
			[
				'label' => __( 'Margin Bottom', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-thumb-area' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

	}

	/**
	 * Image boxshadow Style
	 */
	protected function image_boxshadow_style() {

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'feature_image_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-pg-thumb-area .ha-pg-thumb',
			]
		);
	}

	/**
	 * Image border Style
	 */
	protected function image_border_styles() {

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'feature_image_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-pg-thumb-area .ha-pg-thumb',
			]
		);

	}

	/**
	 * Image border radius Style
	 */
	protected function image_border_radius_styles() {

		$this->add_responsive_control(
			'feature_image_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-thumb-area .ha-pg-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

	}

	/**
	 * Image css filter Style
	 */
	protected function image_css_filter_styles() {

		$this->start_controls_tabs( 'feature_image_tabs');
		$this->start_controls_tab(
			'feature_image_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'feature_image_css_filters',
                'selector' => '{{WRAPPER}} .ha-pg-thumb-area .ha-pg-thumb img',
            ]
        );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'feature_image_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'feature_image_hover_css_filters',
                'selector' => '{{WRAPPER}} .ha-pg-thumb-area .ha-pg-thumb:hover img',
            ]
        );

		$this->end_controls_tab();
		$this->end_controls_tabs();

	}

	/**
	 * Taxonomy Badge Style controls
	 */
	protected function taxonomy_badge_style_tab_controls( $widget ) {

		$this->start_controls_section(
			'_section_taxonomy_badge_style',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'show_badge' ) => 'yes',
				],
			]
		);

		$this->taxonomy_badge_position($widget);

		$this->add_responsive_control(
			'badge_margin',
			[
				'label' => __( 'Margin', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => '_skin',
							'operator' => '!=',
							'value' => 'classic',
						],
						[
							'name' => '_skin',
							'operator' => '!=',
							'value' => 'outbox',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-badge a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'badge_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'exclude' => [
					'color'
				],
				'selector' => '{{WRAPPER}} .ha-pg-badge a',
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-badge a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-pg-badge a',
			]
		);

		$this->start_controls_tabs( 'badge_tabs');
		$this->start_controls_tab(
			'badge_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-badge a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'badge_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-pg-badge a',
			]
		);

		$this->add_control(
			'badge_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-badge a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'badge_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'badge_hover_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-badge a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'badge_hover_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-pg-badge a:hover',
			]
		);

		$this->add_control(
			'badge_hover_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-badge a:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Taxonomy badge Position
	 */
	protected function taxonomy_badge_position($widget) {

        $this->add_control(
			'badge_position_toggle',
			[
				'label' => __( 'Position', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'None', 'happy-addons-pro' ),
				'label_on' => __( 'Custom', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => '_skin',
							'operator' => '==',
							'value' => 'classic',
						],
						[
							'name' => '_skin',
							'operator' => '==',
							'value' => 'outbox',
						],
					],
				],
			]
		);

		$widget->start_popover();

		$this->add_responsive_control(
			'badge_position_x',
			[
				'label' => __( 'Position Left', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em'],
				'condition' => [
					$this->get_control_id( 'badge_position_toggle' ) => 'yes',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'em' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-thumb-area .ha-pg-badge' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_position_y',
			[
				'label' => __( 'Position Top', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition' => [
					$this->get_control_id( 'badge_position_toggle' ) => 'yes',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'em' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-thumb-area .ha-pg-badge' => 'top: {{SIZE}}{{UNIT}};bottom:auto;',
				],
			]
		);
		$widget->end_popover();

    }

	/**
	 * Content Style controls
	 */
	protected function content_style_tab_controls() {

		$this->start_controls_section(
			'_section_content_style',
			[
				'label' => __( 'Content', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		//Content area
		$this->add_responsive_control(
			'content_area_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-content-area' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		//Post Title
		$this->add_control(
			'post_title_heading',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					$this->get_control_id( 'show_title' ) => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'post_title_margin_btm',
			[
				'label' => __( 'Margin Bottom', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				// 'default' => [
				// 	'unit' => 'px',
				// 	'size' => '10',
				// ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-title' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-top: 0;',
				],
				'condition' => [
					$this->get_control_id( 'show_title' ) => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'post_title_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-pg-title a',
				'condition' => [
					$this->get_control_id( 'show_title' ) => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'post_title_tabs',
			[
				'condition' => [
					$this->get_control_id( 'show_title' ) => 'yes',
				],
			]
		);
		$this->start_controls_tab(
			'post_title_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'post_title_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-title a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'post_title_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'post_title_hover_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-title a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		//Feature Post Content
		$this->add_control(
			'post_content_heading',
			[
				'label' => __( 'Content', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					$this->get_control_id( 'excerpt_length!' ) => '',
				],
			]
		);

		$this->add_responsive_control(
			'post_content_margin_btm',
			[
				'label' => __( 'Margin Bottom', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				// 'default' => [
				// 	'unit' => 'px',
				// 	'size' => '10',
				// ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-excerpt > p' => 'margin-bottom: 0;',
				],
				'condition' => [
					$this->get_control_id( 'excerpt_length!' ) => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'post_content_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-pg-excerpt',
				'condition' => [
					$this->get_control_id( 'excerpt_length!' ) => '',
				],
			]
		);

		$this->add_control(
			'post_content_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-excerpt' => 'color: {{VALUE}}',
				],
				'condition' => [
					$this->get_control_id( 'excerpt_length!' ) => '',
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Meta Style controls
	 */
	protected function meta_style_tab_controls() {

		$this->start_controls_section(
			'_section_meta_style',
			[
				'label' => __( 'Meta', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'active_meta!' ) => []
				],
			]
		);

		//Post Meta
		$this->add_control(
			'meta_heading',
			[
				'label' => __( 'Meta', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'meta_icon_space',
			[
				'label' => __( 'Icon Space', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-meta-wrap ul li i' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-meta-wrap ul li svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_space',
			[
				'label' => __( 'Space Between', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-meta-wrap ul li' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-meta-wrap ul li:last-child' => 'margin-right: 0;',
					'{{WRAPPER}} .ha-pg-meta-wrap ul li + li:before' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_margin_btm',
			[
				'label' => __( 'Margin Bottom', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-meta-wrap ul li' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'meta_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-pg-meta-wrap',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => '_skin',
							'operator' => '==',
							'value' => 'classic',
						],
						[
							'name' => '_skin',
							'operator' => '==',
							'value' => 'outbox',
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'meta_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-pg-meta-wrap ul li a,{{WRAPPER}} .ha-pg-meta-wrap ul li + li:before',
			]
		);

		$this->start_controls_tabs( 'meta_tabs');
		$this->start_controls_tab(
			'meta_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-meta-wrap ul li a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-pg-meta-wrap ul li a i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-pg-meta-wrap ul li a path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'meta_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'meta_hover_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-meta-wrap ul li a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-pg-meta-wrap ul li a:hover i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-pg-meta-wrap ul li a:hover path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'meta_separator_color',
			[
				'label' => __( 'Separator Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ha-pg-meta-wrap ul li + li:before' => 'color: {{VALUE}}',
				],
				'condition' => [
					$this->get_control_id( 'meta_separator!' ) => '',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render content
	 */
	public function render() {

		$settings = $this->parent->get_settings();

		// return;
		$this->parent->add_render_attribute(
			'grid-wrapper',
			'class',
			[
				'ha-pg-wrapper',
				'ha-pg-default',
				'ha-pg-'. $this->get_id(),
			]
		);
		// $args = $this->get_query_args();
		$args = $this->parent->get_post_query_args();

		$args['posts_per_page'] = $this->get_instance_value( 'posts_per_page' );
		if('' !== $settings['pagination_type']){
			$args['paged'] = $this->parent->get_current_page();
		}
		$_query = new WP_Query( $args );

		// get skin settings values
		//$button_custom_attr = $this->get_instance_value( 'button_custom_attr' );
		//$button_custom_attr_value = $this->get_instance_value( 'button_custom_attr_value' );

		$query_settings = $this->query_settings( $settings, $args );

		?>
		<?php if ( $_query->have_posts() ) : ?>
				<div <?php $this->parent->print_render_attribute_string( 'grid-wrapper' ); ?>>
					<div class="ha-pg-grid-wrap">
					<?php while ( $_query->have_posts() ) : $_query->the_post(); ?>

						<?php $this->render_markup( $settings, $_query );?>

					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
					</div>
					<?php
						if( '' === $settings['pagination_type'] ){
							$this->parent->load_more_render($query_settings);
						}else{
							$this->parent->pagination_render($_query);
						}
					?>
				</div>
			<?php endif;?>
		<?php
	}

	public function render_markup( $settings, $_query ) {

		if( 'classic' == $this->get_id() ){
			return self::render_classic_markup( $settings, $_query );
		}
		elseif( 'hawai' == $this->get_id() ){
			return self::render_hawai_markup( $settings, $_query );
		}
		elseif( 'standard' == $this->get_id() ){
			return self::render_standard_markup( $settings, $_query );
		}
		elseif( 'monastic' == $this->get_id() ){
			return self::render_monastic_markup( $settings, $_query );
		}
		elseif( 'stylica' == $this->get_id() ){
			return self::render_stylica_markup( $settings, $_query );
		}
		elseif( 'outbox' == $this->get_id() ){
			return self::render_outbox_markup( $settings, $_query );
		}
		elseif( 'crossroad' == $this->get_id() ){
			return self::render_crossroad_markup( $settings, $_query );
		}

	}

	public function query_settings( $settings, $args ) {

		$query_settings = [
			'args'                						   => $args,
			'_skin'                						   => $this->get_id(),
			$this->get_control_id( 'posts_post_type' )     => $this->get_instance_value( 'posts_post_type' ),
			$this->get_control_id( 'featured_image' )      => $this->get_instance_value( 'featured_image' ),
			$this->get_control_id( 'featured_image_size' ) => $this->get_instance_value( 'featured_image_size' ),
			$this->get_control_id( 'show_badge' )          => $this->get_instance_value( 'show_badge' ),
			$this->get_control_id( 'show_title' )          => $this->get_instance_value( 'show_title' ),
			$this->get_control_id( 'title_tag' )           => $this->get_instance_value( 'title_tag' ),
			$this->get_control_id( 'active_meta' )         => $this->get_instance_value( 'active_meta' ),
			$this->get_control_id( 'excerpt_length' )      => $this->get_instance_value( 'excerpt_length' ),
		];

		if( !empty($this->get_instance_value( 'active_meta' )) ){
			$query_settings [ $this->get_control_id( 'meta_has_icon' ) ] = $this->get_instance_value( 'meta_has_icon' );
		}

		if( !empty($this->get_instance_value( 'show_badge' )) ){
			$query_settings [ $this->get_control_id( 'taxonomy_badge' ) ] = $this->get_instance_value( 'taxonomy_badge' );
		}

		if( 'classic' == $this->get_id() || 'hawai' == $this->get_id() || 'standard' == $this->get_id()){
			$query_settings [ $this->get_control_id( 'read_more' ) ] = $this->get_instance_value( 'read_more' );
			$query_settings [ $this->get_control_id( 'read_more_new_tab' ) ] = $this->get_instance_value( 'read_more_new_tab' );
		}

		if( 'standard' == $this->get_id() ){
			$query_settings [ $this->get_control_id( 'meta_position' ) ] = $this->get_instance_value( 'meta_position' );
		}

		if( 'stylica' == $this->get_id() ){
			$query_settings [ $this->get_control_id( 'devider_shape' ) ] = $this->get_instance_value( 'devider_shape' );
		}

		$query_settings = json_encode( $query_settings, true );

		return $query_settings;
	}

}

<?php
/**
 * Post Tab widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Happy_Addons\Elementor\Controls\Select2;

defined( 'ABSPATH' ) || die();

class Post_Tab extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Post Tab', 'happy-elementor-addons' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/post-tab/';
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-post-tab';
	}

	public function get_keywords() {
		return [ 'posts', 'post', 'post-tab', 'tab', 'news' ];
	}

	/**
	 * Get a list of All Post Types
	 *
	 * @return array
	 */
	public static function get_post_types() {
		$diff_key   = [
			'elementor_library' => '',
			'attachment'        => '',
			'page'              => '',
		];
		$post_types = ha_get_post_types( [], $diff_key );
		return $post_types;
	}

	/**
	 * Get a list of Taxonomy
	 *
	 * @return array
	 */
	public static function get_taxonomies( $post_type = '' ) {
		$list = [];
		if ( $post_type ) {
			$tax                = ha_get_taxonomies( [ 'public' => true, 'object_type' => [ $post_type ] ], 'object', true );
			$list[ $post_type ] = count( $tax ) !== 0 ? $tax : '';
		} else {
			$list = ha_get_taxonomies( [ 'public' => true ], 'object', true );
		}
		return $list;
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls() {
		$this->__query_content_controls();
		$this->__settings_content_controls();
	}

	protected function __query_content_controls() {

		$this->start_controls_section(
			'_section_post_tab_query',
			[
				'label' => __( 'Query', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'post_type',
			[
				'label'   => __( 'Source', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_post_types(),
				'default' => key( $this->get_post_types() ),
			]
		);

		foreach ( self::get_post_types() as $key => $value ) {
			$taxonomy = self::get_taxonomies( $key );
			if ( ! $taxonomy[ $key ] ) {
				continue;
			}
			$this->add_control(
				'tax_type_' . $key,
				[
					'label'     => __( 'Taxonomies', 'happy-elementor-addons' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => $taxonomy[ $key ],
					'default'   => key( $taxonomy[ $key ] ),
					'condition' => [
						'post_type' => $key,
					],
				]
			);

			foreach ( $taxonomy[ $key ] as $tax_key => $tax_value ) {

				$this->add_control(
					'tax_ids_' . $tax_key,
					[
						'label'          => __( 'Select ', 'happy-elementor-addons' ) . $tax_value,
						'label_block'    => true,
						'type'           => Select2::TYPE,
						'multiple'       => true,
						'sortable'       => true,
						'placeholder'    => 'Search ' . $tax_value,
						'dynamic_params' => [
							'term_taxonomy' => $tax_key,
							'object_type'   => 'term',
						],
						'condition'      => [
							'post_type'        => $key,
							'tax_type_' . $key => $tax_key,
						],
					]
				);
			}
		}

		$this->add_control(
			'item_limit',
			[
				'label'   => __( 'Item Limit', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3,
				'dynamic' => [ 'active' => true ],
			]
		);

		$this->add_control(
			'posts_orderby',
			[
				'label'   => __( 'Order By', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'author'        => __( 'Author', 'happy-elementor-addons' ),
					'comment_count' => __( 'Comment Count', 'happy-elementor-addons' ),
					'date'          => __( 'Date', 'happy-elementor-addons' ),
					'ID'            => __( 'ID', 'happy-elementor-addons' ),
					'menu_order'    => __( 'Menu Order', 'happy-elementor-addons' ),
					'rand'          => __( 'Random', 'happy-elementor-addons' ),
					'title'         => __( 'Title', 'happy-elementor-addons' ),
				],
			]
		);

		$this->add_control(
			'posts_order',
			[
				'label'   => __( 'Order', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc'  => __( 'ASC', 'happy-elementor-addons' ),
					'desc' => __( 'DESC', 'happy-elementor-addons' ),
				],
			]
		);

		$this->add_control(
			'query_id',
			[
				'label'       => __( 'Query ID', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'dynamic'     => [ 'active' => true ],
				'description' => __( 'Give your Query a custom unique id to allow server side filtering.', 'happy-elementor-addons' ),
			]
		);

		$this->end_controls_section();

	}

	protected function __settings_content_controls() {

		$this->start_controls_section(
			'_section_settings',
			[
				'label' => __( 'Settings', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'column',
			[
				'label'           => __( 'Column', 'happy-elementor-addons' ),
				'type'            => Controls_Manager::SELECT,
				'options'         => [
					'1' => __( '1 Column', 'happy-elementor-addons' ),
					'2' => __( '2 Column', 'happy-elementor-addons' ),
					'3' => __( '3 Column', 'happy-elementor-addons' ),
					'4' => __( '4 Column', 'happy-elementor-addons' ),
					'5' => __( '5 Column', 'happy-elementor-addons' ),
					'6' => __( '6 Column', 'happy-elementor-addons' ),
				],
				'desktop_default' => '4',
				'tablet_default'  => '3',
				'mobile_default'  => '1',
				'selectors'       => [
					'(desktop){{WRAPPER}} .ha-post-tab .ha-post-tab-item' => 'flex-basis: calc(100% / {{VALUE}});',
					'(tablet){{WRAPPER}} .ha-post-tab .ha-post-tab-item' => 'flex-basis: calc(100% / {{column_tablet.VALUE}});',
					'(mobile){{WRAPPER}} .ha-post-tab .ha-post-tab-item' => 'flex-basis: calc(100% / {{column_mobile.VALUE}});',
				],
				'render_type'     => 'template',
				'style_transfer'  => true,
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => __( 'Title HTML Tag', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				// 'separator' => 'before',
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->add_control(
			'show_user_meta',
			[
				'label'        => __( 'Show User Meta', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-elementor-addons' ),
				'label_off'    => __( 'Hide', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_date_meta',
			[
				'label'        => __( 'Show Date Meta', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-elementor-addons' ),
				'label_off'    => __( 'Hide', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'excerpt',
			[
				'label'        => __( 'Show Excerpt', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-elementor-addons' ),
				'label_off'    => __( 'Hide', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'filter_pos',
			[
				'label'          => __( 'Filter Position', 'happy-elementor-addons' ),
				'label_block'    => false,
				'type'           => Controls_Manager::CHOOSE,
				'default'        => 'top',
				'options'        => [
					'left'  => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'top'   => [
						'title' => __( 'Top', 'happy-elementor-addons' ),
						'icon'  => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'filter_align',
			[
				'label'          => __( 'Filter Align', 'happy-elementor-addons' ),
				'label_block'    => false,
				'type'           => Controls_Manager::CHOOSE,
				'default'        => 'left',
				'options'        => [
					'left'   => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'condition'      => [
					'filter_pos' => 'top',
				],
				'selectors'      => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-filter' => 'text-align: {{VALUE}};',
				],
				'style_transfer' => true,
			]
		);

		$this->add_responsive_control(
			'event',
			[
				'label'          => __( 'Tab action', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => [
					'click' => __( 'On Click', 'happy-elementor-addons' ),
					'hover' => __( 'On Hover', 'happy-elementor-addons' ),
				],
				'default'        => 'click',
				'render_type'    => 'template',
				'style_transfer' => true,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {
		$this->__tab_style_controls();
		$this->__tab_column_style_controls();
		$this->__content_style_controls();
	}

	protected function __tab_style_controls() {

		$this->start_controls_section(
			'_section_post_tab_filter',
			[
				'label' => __( 'Tab', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'tab_margin_btm',
			[
				'label'     => __( 'Margin Bottom', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-filter' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'filter_pos' => 'top',
				],
			]
		);

		$this->add_responsive_control(
			'tab_padding',
			[
				'label'      => __( 'Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tab_shadow',
				'label'    => __( 'Box Shadow', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-post-tab .ha-post-tab-filter',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'tab_border',
				'label'    => __( 'Border', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-post-tab .ha-post-tab-filter',
			]
		);

		$this->add_responsive_control(
			'tab_item',
			[
				'label'     => __( 'Tab Item', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'tab_item_margin',
			[
				'label'      => __( 'Margin', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-filter li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_item_padding',
			[
				'label'      => __( 'Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-filter li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tab_item_tabs' );
		$this->start_controls_tab(
			'tab_item_normal_tab',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'tab_item_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-filter li' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tab_item_background',
				'label'    => __( 'Background', 'happy-elementor-addons' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-post-tab .ha-post-tab-filter li',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover_tab',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'tab_item_hvr_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-filter li.active' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-filter li:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tab_item_hvr_background',
				'label'    => __( 'Background', 'happy-elementor-addons' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-post-tab .ha-post-tab-filter li.active,{{WRAPPER}} .ha-post-tab .ha-post-tab-filter li:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tab_item_typography',
				'label'    => __( 'Typography', 'happy-elementor-addons' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-post-tab .ha-post-tab-filter li',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'tab_item_border',
				'label'    => __( 'Border', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-post-tab .ha-post-tab-filter li',
			]
		);

		$this->add_responsive_control(
			'tab_item_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-filter li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	//Column
	protected function __tab_column_style_controls() {

		$this->start_controls_section(
			'_section_post_tab_column',
			[
				'label' => __( 'Column', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'post_item_space',
			[
				'label'     => __( 'Space Between', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-item' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_item_margin_btm',
			[
				'label'     => __( 'Margin Bottom', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_item_padding',
			[
				'label'      => __( 'Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-item-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'post_item_background',
				'label'    => __( 'Background', 'happy-elementor-addons' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-post-tab .ha-post-tab-item-inner',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'post_item_box_shadow',
				'label'    => __( 'Box Shadow', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-post-tab .ha-post-tab-item-inner',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'post_item_border',
				'label'    => __( 'Border', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-post-tab .ha-post-tab-item-inner',
			]
		);

		$this->add_responsive_control(
			'post_item_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-item-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

	}

	//Content Style
	protected function __content_style_controls() {

		$this->start_controls_section(
			'_section_post_tab_content',
			[
				'label' => __( 'Content', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'post_content_image',
			[
				'label' => __( 'Image', 'happy-elementor-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'post_item_content_img_margin_btm',
			[
				'label'     => __( 'Margin Bottom', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-item-inner .ha-post-tab-thumb' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_boder',
				'label'    => __( 'Border', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-post-tab .ha-post-tab-item-inner .ha-post-tab-thumb img',
			]
		);

		$this->add_responsive_control(
			'image_boder_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-item-inner .ha-post-tab-thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_content_title',
			[
				'label'     => __( 'Title', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'post_content_margin_btm',
			[
				'label'     => __( 'Margin Bottom', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-item-inner .ha-post-tab-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __( 'Typography', 'happy-elementor-addons' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-post-tab .ha-post-tab-item-inner .ha-post-tab-title',
			]
		);

		$this->start_controls_tabs( 'title_tabs' );
		$this->start_controls_tab(
			'title_normal_tab',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-item-inner .ha-post-tab-title a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_hover_tab',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'title_hvr_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-item-inner .ha-post-tab-title a:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'post_content_meta',
			[
				'label'     => __( 'Meta', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => __( 'Typography', 'happy-elementor-addons' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-post-tab .ha-post-tab-meta span',
			]
		);

		$this->start_controls_tabs( 'meta_tabs' );
		$this->start_controls_tab(
			'meta_normal_tab',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-meta span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-meta span a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'meta_hover_tab',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'meta_hvr_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-meta span:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-meta span:hover a' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'meta__margin',
			[
				'label'      => __( 'Margin', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-meta span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_content_excerpt',
			[
				'label'     => __( 'Excerpt', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'excerpt' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'excerpt_typography',
				'label'     => __( 'Typography', 'happy-elementor-addons' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector'  => '{{WRAPPER}} .ha-post-tab .ha-post-tab-excerpt p',
				'condition' => [
					'excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-excerpt p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'excerpt' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'excerpt_margin_top',
			[
				'label'     => __( 'Margin Top', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-post-tab .ha-post-tab-excerpt' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'excerpt' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();
		if ( ! $settings['post_type'] ) {
			return;
		}

		$taxonomy  = $settings[ 'tax_type_' . $settings['post_type'] ];
		$terms_ids = $settings[ 'tax_ids_' . $taxonomy ];

		$terms_args = [
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'include'    => $terms_ids,
			'orderby'    => 'include',
		];

		$filter_list = get_terms( $terms_args );

		$args['post_status']      = 'publish';
		$args['post_type']        = $settings['post_type'];
		$args['posts_per_page']   = $settings['item_limit'];
		$args['suppress_filters'] = false;
		$args['orderby']          = $settings['posts_orderby'] ? $settings['posts_orderby'] : 'date';
		$args['order']            = $settings['posts_order'] ? $settings['posts_order'] : 'DESC';

		$args['tax_query'] = [
			[
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				// 'terms' => $terms_ids ? $filter_list[0]->term_id : '',
				'terms'    => isset( $filter_list[0]->term_id ) ? $filter_list[0]->term_id : '',
			],
		];

		//define ha post tab custom query filter hook
		if ( ! empty( $settings['query_id'] ) ) {
			$args = apply_filters( "happyaddons/post-tab/{$settings['query_id']}", $args );
		}

		$posts = get_posts( $args );

		$query_settings = [
			'post_type'  => $settings['post_type'],
			'taxonomy'   => $taxonomy,
			'item_limit' => $settings['item_limit'],
			'orderby'    => $settings['posts_orderby'] ? $settings['posts_orderby'] : 'date',
			'order'      => $settings['posts_order'] ? $settings['posts_order'] : 'DESC',
			'show_user_meta'    => $settings['show_user_meta'] ? $settings['show_user_meta'] : 'no',
			'show_date_meta'    => $settings['show_date_meta'] ? $settings['show_date_meta'] : 'no',
			'excerpt'    => $settings['excerpt'] ? $settings['excerpt'] : 'no',
			'title_tag'  => $settings['title_tag'],
		];
		$query_settings = json_encode( $query_settings, true );

		$event = 'click';
		if ( 'hover' === $settings['event'] ) {
			$event = 'hover touchstart';
		}
		$wrapper_class = [
			'ha-post-tab',
			'ha-post-tab-' . $settings['filter_pos'],
			'ha-post-tab-grid-' . $settings['column'],
			isset( $settings['column_tablet'] ) ? 'ha-post-tab-grid-tablet-' . $settings['column_tablet'] : 'ha-post-tab-grid-tablet-3',
			isset( $settings['column_mobile'] ) ? 'ha-post-tab-grid-mobile-' . $settings['column_mobile'] : 'ha-post-tab-grid-mobile-1',
		];
		$this->add_render_attribute( 'wrapper', 'class', $wrapper_class );
		$this->add_render_attribute( 'wrapper', 'data-query-args', $query_settings );
		$this->add_render_attribute( 'wrapper', 'data-event', $event );
		$this->add_render_attribute( 'tab-filter', 'class', [ 'ha-post-tab-filter ha-text--center' ] );
		$this->add_render_attribute( 'tab-body', 'class', [ 'ha-post-tab-content' ] );
		$this->add_render_attribute( 'tab-item-wrapper', 'class', [ 'ha-post-tab-item-wrapper active' ] );
		$this->add_render_attribute( 'item', 'class', [ 'ha-post-list-item' ] );
		$i = 1;
		if ( ! empty( $terms_ids ) && count( $posts ) !== 0 ) :?>
			<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
				<ul <?php $this->print_render_attribute_string( 'tab-filter' ); ?>>
					<?php foreach ( $filter_list as $list ) : ?>
						<?php
						if ( $i === 1 ) :
							$i++;
							?>
							<li class="active"
								data-term="<?php echo esc_attr( $list->term_id ); ?>"><?php echo esc_html( $list->name ); ?></li>
						<?php else : ?>
							<li data-term="<?php echo esc_attr( $list->term_id ); ?>"><?php echo esc_html( $list->name ); ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
				<div <?php $this->print_render_attribute_string( 'tab-body' ); ?>>
					<div <?php $this->print_render_attribute_string( 'tab-item-wrapper' ); ?>
						data-term="<?php echo esc_attr( $terms_ids[0] ); ?>">
						<?php foreach ( $posts as $post ) : ?>
							<div class="ha-post-tab-item">
								<div class="ha-post-tab-item-inner">
									<?php if ( has_post_thumbnail( $post->ID ) ) : ?>
										<a href="<?php echo esc_url( get_the_permalink( $post->ID ) ); ?>"
										   class="ha-post-tab-thumb">
											<?php echo get_the_post_thumbnail( $post->ID, 'full' ); ?>
										</a>
									<?php endif; ?>
									<?php
										printf(
											'<%1$s class="ha-post-tab-title"><a href="%2$s">%3$s</a></%1$s>',
											ha_escape_tags( $settings['title_tag'], 'h2' ),
											esc_url( get_the_permalink( $post->ID ) ),
											esc_html( $post->post_title )
										);
									?>

									<?php
									if ( ( 'yes' == $settings['show_user_meta'] ) || ( 'yes' == $settings['show_date_meta'] ) ) {
										?>
											<div class="ha-post-tab-meta">
												<?php
												if ( 'yes' == $settings['show_user_meta'] ) {
													?>
														<span class="ha-post-tab-meta-author">
															<i class="fa fa-user-o"></i>
															<a href="<?php echo esc_url( get_author_posts_url( $post->post_author ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?></a>
														</span>
												<?php } ?>


												<?php
												if ( 'yes' == $settings['show_date_meta'] ) {

													$archive_year  = get_the_time( 'Y', $post->ID );
													$archive_month = get_the_time( 'm', $post->ID );
													$archive_day   = get_the_time( 'd', $post->ID );
													?>

													<span class="ha-post-tab-meta-date">
														<i class="fa fa-calendar-o"></i>
														<a href="<?php echo esc_url( get_day_link( $archive_year, $archive_month, $archive_day ) ); ?>">
														<?php echo get_the_date( get_option( 'date_format' ), $post->ID ); ?>
														</a>
													</span>

												<?php } ?>

											</div>
									<?php } ?>

									<?php if ( 'yes' === $settings['excerpt'] && ! empty( $post->post_excerpt ) ) : ?>
										<div class="ha-post-tab-excerpt">
											<p><?php echo esc_html( $post->post_excerpt ); ?></p>
										</div>
									<?php endif; ?>
								</div>
							</div>
							<?php
						endforeach;
						?>
					</div>
				</div>
			</div>
			<?php
		else :
			printf(
				'%1$s',
				__( 'No  Posts  Found', 'happy-elementor-addons' )
			);
		endif;
	}
}

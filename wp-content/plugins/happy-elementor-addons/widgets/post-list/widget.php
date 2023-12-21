<?php
/**
 * Post List widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons\Elementor\Widget;

defined( 'ABSPATH' ) || die();

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Happy_Addons\Elementor\Controls\Select2;

class Post_List extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Post List', 'happy-elementor-addons' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/post-list/';
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
		return 'hm hm-post-list';
	}

	public function get_keywords() {
		return [ 'posts', 'post', 'post-list', 'list', 'news' ];
	}

	/**
	 * Get a list of All Post Types
	 *
	 * @return array
	 */
	public function get_post_types() {
		$post_types = ha_get_post_types( [], [ 'elementor_library', 'attachment' ] );
		return $post_types;
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls() {
		$this->__post_list_content_controls();
		$this->__settings_content_controls();
	}

	protected function __post_list_content_controls() {

		$this->start_controls_section(
			'_section_post_list',
			[
				'label' => __( 'List', 'happy-elementor-addons' ),
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

		$this->add_control(
			'show_post_by',
			[
				'label'   => __( 'Show post by:', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'recent',
				'options' => [
					'recent'   => __( 'Recent Post', 'happy-elementor-addons' ),
					'selected' => __( 'Selected Post', 'happy-elementor-addons' ),
				],

			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'     => __( 'Item Limit', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3,
				'dynamic'   => [ 'active' => true ],
				'condition' => [
					'show_post_by' => [ 'recent' ],
				],
			]
		);

		$repeater = [];

		foreach ( $this->get_post_types() as $key => $value ) {

			$repeater[ $key ] = new Repeater();

			$repeater[ $key ]->add_control(
				'title',
				[
					'label'       => __( 'Title', 'happy-elementor-addons' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'placeholder' => __( 'Customize Title', 'happy-elementor-addons' ),
					'dynamic'     => [
						'active' => true,
					],
				]
			);

			$repeater[ $key ]->add_control(
				'post_id',
				[
					'label'          => __( 'Select ', 'happy-elementor-addons' ) . $value,
					'label_block'    => true,
					'type'           => Select2::TYPE,
					'multiple'       => false,
					'placeholder'    => 'Search ' . $value,
					'dynamic_params' => [
						'object_type' => 'post',
						'post_type'   => $key,
					],
				]
			);

			$this->add_control(
				'selected_list_' . $key,
				[
					'label'       => '',
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater[ $key ]->get_controls(),
					'title_field' => '{{ title }}',
					'condition'   => [
						'show_post_by' => 'selected',
						'post_type'    => $key,
					],
				]
			);
		}

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

		$this->add_control(
			'view',
			[
				'label'          => __( 'Layout', 'happy-elementor-addons' ),
				'label_block'    => false,
				'type'           => Controls_Manager::CHOOSE,
				'default'        => 'list',
				'options'        => [
					'list'   => [
						'title' => __( 'List', 'happy-elementor-addons' ),
						'icon'  => 'eicon-editor-list-ul',
					],
					'inline' => [
						'title' => __( 'Inline', 'happy-elementor-addons' ),
						'icon'  => 'eicon-ellipsis-h',
					],
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'feature_image',
			[
				'label'        => __( 'Featured Image', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-elementor-addons' ),
				'label_off'    => __( 'Hide', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'feature_image_pos',
			[
				'label'                => __( 'Image Position', 'happy-elementor-addons' ),
				'label_block'          => false,
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'left',
				'options'              => [
					'left' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon'  => 'eicon-h-align-left',
					],
					'top'  => [
						'title' => __( 'Top', 'happy-elementor-addons' ),
						'icon'  => 'eicon-v-align-top',
					],
				],
				'style_transfer'       => true,
				'condition'            => [
					'feature_image' => 'yes',
				],
				'selectors_dictionary' => [
					'left' => 'flex-direction: row',
					'top'  => 'flex-direction: column',
				],
				'selectors'            => [
					'{{WRAPPER}} .ha-post-list .ha-post-list-item a' => '{{VALUE}};',
					'{{WRAPPER}} .ha-post-list-item a img' => 'margin-right: 0px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'post_image',
				'default'   => 'thumbnail',
				'exclude'   => [
					'custom',
				],
				'condition' => [
					'feature_image' => 'yes',
				],
			]
		);

		$this->add_control(
			'list_icon',
			[
				'label'        => __( 'List Icon', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-elementor-addons' ),
				'label_off'    => __( 'Hide', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'feature_image!' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon',
			[
				'label'       => __( 'Icon', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => true,
				'default'     => [
					'value'   => 'far fa-check-circle',
					'library' => 'reguler',
				],
				'condition'   => [
					'list_icon'      => 'yes',
					'feature_image!' => 'yes',
				],
			]
		);

		$this->add_control(
			'content',
			[
				'label'        => __( 'Show Content', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-elementor-addons' ),
				'label_off'    => __( 'Hide', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'meta',
			[
				'label'        => __( 'Show Meta', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-elementor-addons' ),
				'label_off'    => __( 'Hide', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'author_meta',
			[
				'label'        => __( 'Author', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-elementor-addons' ),
				'label_off'    => __( 'Hide', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'author_icon',
			[
				'label'     => __( 'Author Icon', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'far fa-user',
					'library' => 'reguler',
				],
				'condition' => [
					'meta'        => 'yes',
					'author_meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'date_meta',
			[
				'label'        => __( 'Date', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-elementor-addons' ),
				'label_off'    => __( 'Hide', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'date_icon',
			[
				'label'     => __( 'Date Icon', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'far fa-calendar-check',
					'library' => 'reguler',
				],
				'condition' => [
					'meta'      => 'yes',
					'date_meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'category_meta',
			[
				'label'        => __( 'Category', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'happy-elementor-addons' ),
				'label_off'    => __( 'Hide', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'meta'      => 'yes',
					'post_type' => [ 'post', 'product' ],
				],
			]
		);

		$this->add_control(
			'category_icon',
			[
				'label'     => __( 'Category Icon', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'far fa-folder-open',
					'library' => 'reguler',
				],
				'condition' => [
					'meta'          => 'yes',
					'category_meta' => 'yes',
					'post_type' => [ 'post', 'product' ],
				],
			]
		);

		$this->add_control(
			'meta_position',
			[
				'label'     => __( 'Meta Position', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => [
					'top'    => __( 'Top', 'happy-elementor-addons' ),
					'bottom' => __( 'Bottom', 'happy-elementor-addons' ),
				],
				'condition' => [
					'meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => __( 'Title HTML Tag', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'h1' => [
						'title' => __( 'H1', 'happy-elementor-addons' ),
						'icon'  => 'eicon-editor-h1',
					],
					'h2' => [
						'title' => __( 'H2', 'happy-elementor-addons' ),
						'icon'  => 'eicon-editor-h2',
					],
					'h3' => [
						'title' => __( 'H3', 'happy-elementor-addons' ),
						'icon'  => 'eicon-editor-h3',
					],
					'h4' => [
						'title' => __( 'H4', 'happy-elementor-addons' ),
						'icon'  => 'eicon-editor-h4',
					],
					'h5' => [
						'title' => __( 'H5', 'happy-elementor-addons' ),
						'icon'  => 'eicon-editor-h5',
					],
					'h6' => [
						'title' => __( 'H6', 'happy-elementor-addons' ),
						'icon'  => 'eicon-editor-h6',
					],
				],
				'default' => 'h2',
				'toggle'  => false,
			]
		);

		$this->add_control(
			'item_align',
			[
				'label'                => __( 'Alignment', 'happy-elementor-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
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
				'toggle'               => true,
				'selectors_dictionary' => [
					'left'   => 'justify-content: flex-start',
					'center' => 'justify-content: center',
					'right'  => 'justify-content: flex-end',
				],
				'selectors'            => [
					'{{WRAPPER}} .ha-post-list .ha-post-list-item a' => '{{VALUE}};',
				],
				'condition'            => [
					'view'              => 'list',
					'feature_image_pos' => 'left',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {
		$this->__post_list_style_controls();
		$this->__title_style_controls();
		$this->__icon_image_style_controls();
		$this->__excerpt_style_controls();
		$this->__meta_style_controls();
	}

	protected function __post_list_style_controls() {

		$this->start_controls_section(
			'_section_post_list_style',
			[
				'label' => __( 'List', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'list_item_common',
			[
				'label' => __( 'Common', 'happy-elementor-addons' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'list_item_margin',
			[
				'label'      => __( 'Margin', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-list .ha-post-list-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'list_item_padding',
			[
				'label'      => __( 'Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-list .ha-post-list-item a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'list_item_background',
				'label'    => __( 'Background', 'happy-elementor-addons' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-post-list .ha-post-list-item',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'list_item_box_shadow',
				'label'    => __( 'Box Shadow', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-post-list .ha-post-list-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'list_item_border',
				'label'    => __( 'Border', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-post-list .ha-post-list-item',
			]
		);

		$this->add_responsive_control(
			'list_item_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-list .ha-post-list-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'advance_style',
			[
				'label'        => __( 'Advance Style', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'On', 'happy-elementor-addons' ),
				'label_off'    => __( 'Off', 'happy-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_responsive_control(
			'list_item_first',
			[
				'label'     => __( 'First Item', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'advance_style' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'list_item_first_child_margin',
			[
				'label'      => __( 'Margin', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-list .ha-post-list-item:first-child' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'advance_style' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'list_item_first_child_border',
				'label'     => __( 'Border', 'happy-elementor-addons' ),
				'selector'  => '{{WRAPPER}} .ha-post-list .ha-post-list-item:first-child',
				'condition' => [
					'advance_style' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'list_item_last',
			[
				'label'     => __( 'Last Item', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'advance_style' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'list_item_last_child_margin',
			[
				'label'      => __( 'Margin', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-list .ha-post-list-item:last-child' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'advance_style' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'list_item_last_child_border',
				'label'     => __( 'Border', 'happy-elementor-addons' ),
				'selector'  => '{{WRAPPER}} .ha-post-list .ha-post-list-item:last-child',
				'condition' => [
					'advance_style' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __title_style_controls() {

		$this->start_controls_section(
			'_section_post_list_title_style',
			[
				'label' => __( 'Title', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
				'selector' => '{{WRAPPER}} .ha-post-list-title',
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
					'{{WRAPPER}} .ha-post-list-title' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .ha-post-list .ha-post-list-item a:hover .ha-post-list-title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __icon_image_style_controls() {

		$this->start_controls_section(
			'_section_list_icon_feature_iamge_style',
			[
				'label'      => __( 'Icon & Feature Image', 'happy-elementor-addons' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'feature_image',
							'operator' => '==',
							'value'    => 'yes',
						],
						[
							'name'     => 'list_icon',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} span.ha-post-list-icon' => 'color: {{VALUE}};',
				],
				'condition' => [
					'feature_image!' => 'yes',
					'list_icon'      => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'     => __( 'Font Size', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} span.ha-post-list-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'feature_image!' => 'yes',
					'list_icon'      => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_line_height',
			[
				'label'     => __( 'Line Height', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} span.ha-post-list-icon' => 'line-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'feature_image!' => 'yes',
					'list_icon'      => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label'     => __( 'Image Width', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-post-list-item a img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'feature_image' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'image_boder',
				'label'     => __( 'Border', 'happy-elementor-addons' ),
				'selector'  => '{{WRAPPER}} .ha-post-list-item a img',
				'condition' => [
					'feature_image' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'image_boder_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-list-item a img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'feature_image' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_margin_right',
			[
				'label'     => __( 'Margin Right', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'unit' => 'px',
					'size' => '15',
				],
				'selectors' => [
					'{{WRAPPER}} span.ha-post-list-icon'   => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-post-list-item a img' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'feature_image_pos' => 'left',
				],
			]
		);

		$this->add_responsive_control(
			'feature_margin_bottom',
			[
				'label'     => __( 'Margin Bottom', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'unit' => 'px',
					'size' => '15',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-post-list-item a img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'feature_image_pos' => 'top',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __excerpt_style_controls() {

		$this->start_controls_section(
			'_section_list_excerpt_style',
			[
				'label'     => __( 'Content', 'happy-elementor-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'content' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'excerpt_typography',
				'label'    => __( 'Typography', 'happy-elementor-addons' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-post-list-excerpt p',
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ha-post-list-excerpt p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'excerpt_space',
			[
				'label'     => __( 'Space Top', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-post-list-excerpt' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __meta_style_controls() {

		$this->start_controls_section(
			'_section_list_meta_style',
			[
				'label'     => __( 'Meta', 'happy-elementor-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'meta' => 'yes',
				],
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
				'selector' => '{{WRAPPER}} .ha-post-list-meta-wrap span',
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ha-post-list-meta-wrap span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_space',
			[
				'label'     => __( 'Space Between', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-post-list-meta-wrap span' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-post-list-meta-wrap span:last-child' => 'margin-right: 0;',
				],
			]
		);

		$this->add_responsive_control(
			'meta_box_margin',
			[
				'label'      => __( 'Margin', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ha-post-list-meta-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_icon_heading',
			[
				'label'     => __( 'Meta Icon', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'meta_icon_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ha-post-list-meta-wrap span i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_icon_space',
			[
				'label'     => __( 'Space Between', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-post-list-meta-wrap span i' => 'margin-right: {{SIZE}}{{UNIT}};',
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

		$args = [
			'post_status'      => 'publish',
			'post_type'        => $settings['post_type'],
			'suppress_filters' => false,
		];

		if ( 'recent' === $settings['show_post_by'] ) {
			$args['posts_per_page'] = $settings['posts_per_page'];
		}

		$customize_title = [];
		$ids             = [];

		if ( 'selected' === $settings['show_post_by'] ) {
			$args['posts_per_page'] = -1;
			$lists                  = $settings[ 'selected_list_' . $settings['post_type'] ];

			if ( ! empty( $lists ) ) {
				foreach ( $lists as $index => $value ) {
					//trim function to remove extra space before post ID
					if ( is_array( $value['post_id'] ) ) {
						$post_id = ! empty( $value['post_id'][0] ) ? trim( $value['post_id'][0] ) : '';
					} else {
						$post_id = ! empty( $value['post_id'] ) ? trim( $value['post_id'] ) : '';
					}
					$ids[] = $post_id;
					if ( $value['title'] ) {
						$customize_title[ $post_id ] = $value['title'];
					}
				}
			}

			$args['post__in'] = (array) $ids;
			$args['orderby']  = 'post__in';
		}

		if ( 'selected' === $settings['show_post_by'] && empty( $ids ) ) {
			$posts = [];
		} else {
			$posts = get_posts( $args );
		}

		$this->add_render_attribute( 'wrapper', 'class', [ 'ha-post-list-wrapper' ] );
		$this->add_render_attribute( 'wrapper-inner', 'class', [ 'ha-post-list' ] );
		if ( 'inline' === $settings['view'] ) {
			$this->add_render_attribute( 'wrapper-inner', 'class', [ 'ha-post-list-inline' ] );
		}
		$this->add_render_attribute( 'item', 'class', [ 'ha-post-list-item' ] );

		if ( count( $posts ) !== 0 ) :?>
			<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
				<ul <?php $this->print_render_attribute_string( 'wrapper-inner' ); ?> >
					<?php foreach ( $posts as $post ) : ?>
						<li <?php $this->print_render_attribute_string( 'item' ); ?>>
							<a href="<?php echo esc_url( get_the_permalink( $post->ID ) ); ?>">
								<?php
								if ( 'yes' === $settings['feature_image'] ) :
									echo get_the_post_thumbnail( $post->ID, $settings['post_image_size'] );
								elseif ( 'yes' === $settings['list_icon'] && $settings['icon'] ) :
									echo '<span class="ha-post-list-icon">';
									Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
									echo '</span>';
								endif;
								?>
								<div class="ha-post-list-content">
									<?php
									$title = $post->post_title;
									if ( 'selected' === $settings['show_post_by'] && array_key_exists( $post->ID, $customize_title ) ) {
										$title = $customize_title[ $post->ID ];
									}
									if ( 'top' !== $settings['meta_position'] && $title ) {
										printf(
											'<%1$s %2$s>%3$s</%1$s>',
											ha_escape_tags( $settings['title_tag'], 'h2' ),
											'class="ha-post-list-title"',
											esc_html( $title )
										);
									}
									?>
									<?php if ( 'yes' === $settings['meta'] ) : ?>
										<div class="ha-post-list-meta-wrap">

											<?php
											if ( 'yes' === $settings['author_meta'] ) :
												?>
												<span class="ha-post-list-author">
												<?php
												if ( $settings['author_icon'] ) :
													Icons_Manager::render_icon( $settings['author_icon'], [ 'aria-hidden' => 'true' ] );
												endif;
												echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) );
												?>
												</span>
											<?php endif; ?>

											<?php if ( 'yes' === $settings['date_meta'] ) : ?>
												<span class="ha-post-list-date">
													<?php
													if ( $settings['date_icon'] ) :
														Icons_Manager::render_icon( $settings['date_icon'], [ 'aria-hidden' => 'true' ] );
													endif;
													echo get_the_date( get_option( 'date_format' ), $post->ID );
													?>
												</span>
											<?php endif; ?>

											<?php
											if ( ( 'post' === $settings['post_type'] || 'product' === $settings['post_type'] ) && 'yes' === $settings['category_meta'] ) :
												$taxonomy = 'category';
												if ( 'product' === $settings['post_type'] ) {
													$taxonomy = 'product_cat';
												}
												$categories = get_the_terms( $post->ID, $taxonomy );
												if ( ! $categories || is_wp_error( $categories ) ) {
													$categories = array();
												}
												?>
												<span class="ha-post-list-category">
												<?php
												if ( $settings['category_icon'] ) :
													Icons_Manager::render_icon( $settings['category_icon'], [ 'aria-hidden' => 'true' ] );
												endif;
												echo ( ! empty( $categories ) ) ? esc_html( $categories[0]->name ) : '';
												?>
												</span>
											<?php endif; ?>

										</div>
									<?php endif; ?>
									<?php
									if ( 'top' === $settings['meta_position'] && $title ) {
										printf(
											'<%1$s %2$s>%3$s</%1$s>',
											ha_escape_tags( $settings['title_tag'] ),
											'class="ha-post-list-title"',
											esc_html( $title )
										);
									}
									?>
									<?php if ( 'yes' === $settings['content'] ) : ?>
										<div class="ha-post-list-excerpt">
											<?php
											if ( has_excerpt( $post->ID ) ) {
												printf(
													'<p>%1$s</p>',
													wp_trim_words( get_the_excerpt( $post->ID ) )
												);
											} else {
												printf(
													'<p>%1$s</p>',
													wp_trim_words( get_the_content( null, false, $post->ID ), 25, '.' )
												);
											}
											?>
										</div>
									<?php endif; ?>
								</div>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php
		else :
			printf(
				'%1$s %2$s %3$s',
				__( 'No ', 'happy-elementor-addons' ),
				esc_html( $settings['post_type'] ),
				__( 'Found', 'happy-elementor-addons' )
			);
		endif;
	}
}

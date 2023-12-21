<?php
/**
 * Post Grid widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use Happy_Addons_Pro\Traits\Lazy_Query_Builder;
use Happy_Addons_Pro\Traits\Post_Grid_Markup_New;
use WP_Query;

defined( 'ABSPATH' ) || die();

class Post_Grid_New extends Base {

	use Lazy_Query_Builder;
	use Post_Grid_Markup_New;

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Post Grid', 'happy-addons-pro' );
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
		return 'hm hm-post-grid';
	}

	public function get_keywords() {
		return ['post', 'posts', 'portfolio', 'grid', 'tiles', 'query', 'blog', 'ha-skin'];
	}

	public function conditions ($key) {
		$condition = [
			'read_more_content' => [
				'relation' => 'or',
				'terms' => [
					[
						'name' =>  'skin',
						'operator' => '==',
						'value' => 'classic',
					],
					[
						'name' =>  'skin',
						'operator' => '==',
						'value' => 'hawai',
					],
					[
						'name' =>  'skin',
						'operator' => '==',
						'value' => 'standard',
					],
				],
			],
			'read_more_new_tab_content' => [
				'terms' => [
					[
						'relation' => 'or',
						'terms' => [
							[
								'name' =>  'skin',
								'operator' => '==',
								'value' => 'classic',
							],
							[
								'name' =>  'skin',
								'operator' => '==',
								'value' => 'hawai',
							],
							[
								'name' =>  'skin',
								'operator' => '==',
								'value' => 'standard',
							],
						],
					],
					[
						'terms' => [
							[
								'name' => 'read_more',
								'operator' => '!=',
								'value' => '',
							],
						],
					]
				]
			],
			'list_post_show' => [
				'relation' => 'or',
				'terms' => [
					[
						'name' => 'make_featured_post',
						'operator' => '!==',
						'value' => 'yes',
					],
					[
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'make_featured_post',
								'operator' => '===',
								'value' => 'yes',
							],
							[
								'name' => 'featured_post_column',
								'operator' => '!==',
								'value' => 'featured-col-2',
							],
							[
								'name' => 'column',
								'operator' => 'in',
								'value' => ['col-2'],
							],
						],
					],
					[
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'make_featured_post',
								'operator' => '===',
								'value' => 'yes',
							],
							[
								'name' => 'column',
								'operator' => 'in',
								'value' => ['col-3'],
							],
						],
					],
				],
			],
			'list_post_meta_old_style' => [
				'terms' => [
					[
						'name' => 'list_meta_active[0]',
						'operator' => 'in',
						'value' =>['author','date','comments'],
					],
				],
			],
			'feature_meta_old_style' => [
				'terms' => [
					[
						'name' => 'featured_meta_active[0]',
						'operator' => 'in',
						'value' => ['author','date','comments'],
					],
				],
			],
			'list_post_meta_style' => [
				'terms' => [
					[
						'name' => 'list_meta_active',
						'operator' => '!=',
						'value' => '',
					],
				],
			],
			'feature_meta_style' => [
				'terms' => [
					[
						'name' => 'featured_meta_active',
						'operator' => '!=',
						'value' => '',
					],
				],
			],
			'feature_item_height' => [
				'relation' => 'or',
				'terms' => [
					[
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'make_featured_post',
								'operator' => '===',
								'value' => 'yes',
							],
							[
								'name' => 'featured_post_style',
								'operator' => '===',
								'value' => 'inside-conent',
							],
							[
								'name' => 'column',
								'operator' => 'in',
								'value' => ['col-1'],
							],
						],
					],
					[
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'make_featured_post',
								'operator' => '===',
								'value' => 'yes',
							],
							[
								'name' => 'featured_post_column',
								'operator' => '===',
								'value' => 'featured-col-2',
							],
							[
								'name' => 'featured_post_style',
								'operator' => '===',
								'value' => 'inside-conent',
							],
							[
								'name' => 'column',
								'operator' => 'in',
								'value' => ['col-2'],
							],
						],
					],
				],
			],
		];

		return $condition[$key];
	}


	/**
	 * Register content related controls
	 */
	protected function register_content_controls() {

		//Layout
		$this->layout_content_tab_controls();

		//Query content
		$this->query_content_tab_controls();

		//Paginations content
		$this->pagination_content_tab_controls();

    }


	/**
	 * Layout content controls
	 */
	protected function layout_content_tab_controls( ) {

		$this->start_controls_section(
			'_section_layout',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
        );

		$this->add_control(
			'skin',
			[
				'label' => __( 'Skin', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'classic' => __( 'Classic', 'happy-addons-pro' ),
					'hawai' => __( 'Hawai', 'happy-addons-pro' ),
					'standard' => __( 'Standard', 'happy-addons-pro' ),
					'monastic' => __( 'Monastic', 'happy-addons-pro' ),
					'stylica' => __( 'Stylica', 'happy-addons-pro' ),
					'outbox' => __( 'Outbox', 'happy-addons-pro' ),
					'crossroad' => __( 'Crossroad', 'happy-addons-pro' ),
				],
				'default' => 'classic',
			]
		);

        $this->add_responsive_control(
			'columns',
			[
				'label' => __( 'Columns', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'prefix_class' => 'ha-pg-grid%s-',
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-grid-wrap' => 'grid-template-columns: repeat( {{VALUE}}, 1fr );',
				],
			]
		);

        $this->add_control(
            'posts_per_page',
            [
                'label'   => __( 'Posts Per Page', 'happy-addons-pro' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 3,
            ]
        );

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
					// $this->get_control_id( 'show_title' ) => 'yes',
					'show_title' => 'yes',
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

		$this->readmore_controls();

		$this->end_controls_section();
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
					// $this->get_control_id( 'featured_image' ) => 'yes',
					'featured_image' => 'yes',
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
					// $this->get_control_id( 'show_badge' ) => 'yes',
					'show_badge' => 'yes',
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
					// $this->get_control_id( 'active_meta!' ) => [],
					'active_meta!' => [],
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
					// $this->get_control_id( 'active_meta!' ) => []
					'active_meta!' => []
				],
			]
		);

		$this->add_control(
			'meta_position',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Position', 'happy-addons-pro' ),
				'label_block' => false,
				'multiple' => true,
				'default' => 'after',
				'options' => [
					'before' => __( 'Before Title', 'happy-addons-pro' ),
					'after' => __( 'After Title', 'happy-addons-pro' ),
				],
				'condition' => [
					// $this->get_control_id( 'active_meta!' ) => [],
					'skin' => 'standard',
					'active_meta!' => []
				],
			]
		);

	}



	/**
	 * Readmore Control
	 */
	protected function readmore_controls() {
		$this->add_control(
			'read_more',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __( 'Read More', 'happy-addons-pro' ),
				'placeholder' => __( 'Read More Text', 'happy-addons-pro' ),
				'description' => __( 'Leave it blank to hide it.', 'happy-addons-pro' ),
				'default' => __( 'Continue Reading »', 'happy-addons-pro' ),
				'conditions' => $this->conditions('read_more_content'),
			]
		);

		$this->add_control(
			'read_more_new_tab',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Open in new window', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'conditions' => $this->conditions('read_more_new_tab_content'),
			]
		);


	}

	/**
	 * Query content controls
	 */
	protected function query_content_tab_controls( ) {

		//Query
		$this->start_controls_section(
			'_section_query',
			[
				'label' => __( 'Query', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_query_controls();

		$this->add_control(
			'query_id',
			[
				'label'   => __( 'Query ID', 'happy-addons-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [ 'active' => true ],
				'description' => __( 'Give your Query a custom unique id to allow server side filtering.', 'happy-addons-pro' ),
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Paginations content controls
	 */
	protected function pagination_content_tab_controls( ) {

		//Pagination
		$this->start_controls_section(
			'_section_pagination',
			[
				'label' => __( 'Pagination & Load More', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'label' => __( 'Pagination', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'happy-addons-pro' ),
					'numbers' => __( 'Numbers', 'happy-addons-pro' ),
					'prev_next' => __( 'Previous/Next', 'happy-addons-pro' ),
					'numbers_and_prev_next' => __( 'Numbers', 'happy-addons-pro' ) . ' + ' . __( 'Previous/Next', 'happy-addons-pro' ),
				],
			]
		);

		$this->add_control(
			'pagination_page_limit',
			[
				'label' => __( 'Page Limit', 'happy-addons-pro' ),
				'default' => '5',
				'condition' => [
					'pagination_type!' => '',
				],
			]
		);

		$this->add_control(
			'pagination_numbers_shorten',
			[
				'label' => __( 'Shorten', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => [
					'pagination_type' => [
						'numbers',
						'numbers_and_prev_next',
					],
				],
			]
		);

		$this->add_control(
			'pagination_prev_label',
			[
				'label' => __( 'Previous Label', 'happy-addons-pro' ),
				'default' => __( '&laquo; Previous', 'happy-addons-pro' ),
				'condition' => [
					'pagination_type' => [
						'prev_next',
						'numbers_and_prev_next',
					],
				],
			]
		);

		$this->add_control(
			'pagination_next_label',
			[
				'label' => __( 'Next Label', 'happy-addons-pro' ),
				'default' => __( 'Next &raquo;', 'happy-addons-pro' ),
				'condition' => [
					'pagination_type' => [
						'prev_next',
						'numbers_and_prev_next',
					],
				],
			]
		);

		$this->add_control(
			'loadmore',
			[
				'label' => __( 'Load More Button', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'pagination_type' => '',
				],
			]
		);

		$this->add_control(
			'loadmore_text',
			[
				'label' => __( 'Load More Text', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Load More', 'happy-addons-pro' ),
				'condition' => [
					'pagination_type' => '',
					'loadmore' => 'yes',
				],
			]
		);

		$this->add_control(
			'pagination_align',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				// 'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap' => 'text-align: {{VALUE}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'pagination_type',
							'operator' => '!=',
							'value' => '',

						],
						[
							'name' => 'loadmore',
							'operator' => '===',
							'value' => 'yes',

						],
					],
				],
			]
		);

		$this->end_controls_section();

	}


	/**
	 * Register styles related controls
	 */
	protected function register_style_controls() {

		//Laout Style Start
		$this->layout_style_tab_controls();

		//Box Style Start
		$this->box_style_tab_controls();

		//Feature Image Style Start
		$this->image_style_tab_controls();

		//Devider Shape Style Start (only for stylica skin)
		$this->devider_shape_style_controls();

		//Badge Taxonomy Style Start
		$this->taxonomy_badge_style_tab_controls();

		//Content Style Start
		$this->content_style_tab_controls();

		//Meta Style Start
		$this->meta_style_tab_controls();

		//Readmore Style Start
		$this->readmore_style_tab_controls();

		//Pagination Style Start
		$this->pagination_style_tab_controls();

	}


	/**
	 * Layout Style controls
	 */
	protected function layout_style_tab_controls() {

		$this->start_controls_section(
			'_section_layout_style',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => __( 'Columns Gap', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-grid-wrap' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label' => __( 'Rows Gap', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-grid-wrap' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					],
                ],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ha-pg-item' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'skin' => 'hawai'
				]
			]
		);

		$this->end_controls_section();
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
					// $this->get_control_id( 'featured_image' ) => 'yes',
					'featured_image' => 'yes',
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

		$this->image_overlay_style();

		$this->image_height_margin_style();

		$this->image_boxshadow_style();

		$this->image_border_styles();

		$this->image_border_radius_styles();

		$this->image_css_filter_styles();

		// Add avater bg color (only for outbox skin)
		$this->add_control(
			'avatar_bg',
			[
				'label' => __( 'View', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'avater_bg',
				'selectors' => [
					'{{WRAPPER}} .ha-pg-outbox .ha-pg-item .ha-pg-avatar svg' => 'fill: {{outbox_item_box_background_color.VALUE}};',
				],
				'condition' => [
					'skin' => 'outbox',
				]
			]
		);
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
				'condition' => [
					'skin' => 'classic',
				],
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
				'condition' => [
					'skin' => 'classic',
				],
			]
		);

		$this->add_control(
			'feature_image_heading',
			[
				'label' => __( 'Image', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'skin' => 'classic',
				],
			]
		);

	}

	/**
	 * Image Height & margin Style
	 */
	protected function image_height_margin_style() {

		$this->add_responsive_control(
			'feature_image_width',
			[
				'label' => __( 'Width', 'happy-addons-pro' ),
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
					'{{WRAPPER}} .ha-pg-hawai .ha-pg-thumb-area .ha-pg-thumb' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'skin' => 'hawai'
				]
			]
		);

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
					'{{WRAPPER}} .ha-pg-hawai .ha-pg-thumb-area .ha-pg-thumb' => 'height: {{SIZE}}{{UNIT}};',
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
					// '{{WRAPPER}} .ha-pg-hawai .ha-pg-thumb-area' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'skin' => ['classic','hawai','standard','monastic','stylica','outbox'],
				]
			]
		);

		// image margin bottom (only for crossroad skin)
		$this->add_responsive_control(
			'crossroad_feature_image_margin_btm',
			[
				'label' => __( 'Margin Bottom', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => -20,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-thumb-area' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'skin' => 'crossroad',
				]
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
				'selector' => '
					{{WRAPPER}} .ha-pg-classic .ha-pg-thumb-area .ha-pg-thumb,
					{{WRAPPER}} .ha-pg-hawai .ha-pg-thumb-area .ha-pg-thumb,
					{{WRAPPER}} .ha-pg-standard .ha-pg-thumb-area,
					{{WRAPPER}} .ha-pg-monastic .ha-pg-thumb-area .ha-pg-thumb,
					{{WRAPPER}} .ha-pg-stylica .ha-pg-thumb-area .ha-pg-thumb,
					{{WRAPPER}} .ha-pg-outbox .ha-pg-thumb-area .ha-pg-thumb,
					{{WRAPPER}} .ha-pg-crossroad .ha-pg-thumb-area .ha-pg-thumb
				',
				'condition' => [
					'skin' => ['classic','hawai','standard','monastic'],
				]
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
				// 'selector' => '{{WRAPPER}} .ha-pg-thumb-area .ha-pg-thumb',
				'selector' => '
					{{WRAPPER}} .ha-pg-classic .ha-pg-thumb-area .ha-pg-thumb,
					{{WRAPPER}} .ha-pg-hawai .ha-pg-thumb-area .ha-pg-thumb,
					{{WRAPPER}} .ha-pg-standard .ha-pg-thumb-area,
					{{WRAPPER}} .ha-pg-monastic .ha-pg-thumb-area .ha-pg-thumb,
					{{WRAPPER}} .ha-pg-stylica .ha-pg-thumb-area .ha-pg-thumb,
					{{WRAPPER}} .ha-pg-outbox .ha-pg-thumb-area .ha-pg-thumb,
					{{WRAPPER}} .ha-pg-crossroad .ha-pg-thumb-area .ha-pg-thumb
				',
				'condition' => [
					'skin' => ['classic','hawai','standard','monastic'],
				]
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
					'{{WRAPPER}} .ha-pg-classic .ha-pg-thumb-area .ha-pg-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-hawai .ha-pg-thumb-area .ha-pg-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-standard .ha-pg-thumb-area' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-monastic .ha-pg-thumb-area .ha-pg-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-stylica .ha-pg-thumb-area .ha-pg-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-outbox .ha-pg-thumb-area .ha-pg-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-crossroad .ha-pg-thumb-area .ha-pg-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

	}

	/**
	 * Image css filter Style
	 */
	protected function image_css_filter_styles() {

		$this->start_controls_tabs( 'feature_image_tabs',
			[
				'condition' => [
					'skin!' => 'standard',
				],
			]
	    );
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
	protected function taxonomy_badge_style_tab_controls() {

		$this->start_controls_section(
			'_section_taxonomy_badge_style',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					// $this->get_control_id( 'show_badge' ) => 'yes',
					'show_badge' => 'yes',
				],
			]
		);

		$this->taxonomy_badge_position();

		$this->add_responsive_control(
			'badge_margin',
			[
				'label' => __( 'Margin', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-item .ha-pg-badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'skin',
							'operator' => '!=',
							'value' => 'classic',
						],
						[
							'name' => 'skin',
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
					'{{WRAPPER}} .ha-pg-item .ha-pg-badge a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .ha-pg-item .ha-pg-badge a',
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-item .ha-pg-badge a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .ha-pg-item .ha-pg-badge a',
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
					'{{WRAPPER}} .ha-pg-item .ha-pg-badge a' => 'color: {{VALUE}}',
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
				'selector' => '{{WRAPPER}} .ha-pg-item .ha-pg-badge a',
			]
		);

		$this->add_control(
			'badge_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-item .ha-pg-badge a' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} .ha-pg-item .ha-pg-badge a:hover' => 'color: {{VALUE}}',
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
				'selector' => '{{WRAPPER}} .ha-pg-item .ha-pg-badge a:hover',
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
	protected function taxonomy_badge_position() {

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
							'name' => 'skin',
							'operator' => '==',
							'value' => 'classic',
						],
						[
							'name' => 'skin',
							'operator' => '==',
							'value' => 'outbox',
						],
					],
				],
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'badge_position_x',
			[
				'label' => __( 'Position Left', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em'],
				'condition' => [
					// $this->get_control_id( 'badge_position_toggle' ) => 'yes',
					'badge_position_toggle' => 'yes',
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
					'{{WRAPPER}} .ha-pg-item .ha-pg-thumb-area .ha-pg-badge' => 'left: {{SIZE}}{{UNIT}};',
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
					// $this->get_control_id( 'badge_position_toggle' ) => 'yes',
					'badge_position_toggle' => 'yes',
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
					'{{WRAPPER}} .ha-pg-item .ha-pg-thumb-area .ha-pg-badge' => 'top: {{SIZE}}{{UNIT}};bottom:auto;',
				],
			]
		);
		$this->end_popover();

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

		$this->add_responsive_control(
			'content_area_margin',
			[
				'label' => __( 'Margin', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-content-area' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'skin' => 'crossroad',
				]
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

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'content_area_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-pg-content-area',
				'condition' => [
					'skin' => 'crossroad',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_area_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-pg-content-area',
				'condition' => [
					'skin' => 'crossroad',
				]
			]
		);

		$this->add_responsive_control(
			'content_area_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-content-area' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'skin' => 'crossroad',
				]
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
					// $this->get_control_id( 'show_title' ) => 'yes',
					'show_title' => 'yes',
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
					// $this->get_control_id( 'show_title' ) => 'yes',
					'show_title' => 'yes',
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
					// $this->get_control_id( 'show_title' ) => 'yes',
					'show_title' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'post_title_tabs',
			[
				'condition' => [
					// $this->get_control_id( 'show_title' ) => 'yes',
					'show_title' => 'yes',
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
					// $this->get_control_id( 'excerpt_length!' ) => '',
					'excerpt_length!' => '',
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
					// $this->get_control_id( 'excerpt_length!' ) => '',
					'excerpt_length!' => '',
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
					// $this->get_control_id( 'excerpt_length!' ) => '',
					'excerpt_length!' => '',
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
					// $this->get_control_id( 'excerpt_length!' ) => '',
					'excerpt_length!' => '',
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
					// $this->get_control_id( 'active_meta!' ) => []
					'active_meta!' => []
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
							'name' => 'skin',
							'operator' => '==',
							'value' => 'classic',
						],
						[
							'name' => 'skin',
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
					// $this->get_control_id( 'meta_separator!' ) => '',
					'meta_separator!' => '',
				],
			]
		);

		$this->end_controls_section();
	}


	/**
	 * Added Read More Style controls
	 */
	protected function readmore_style_tab_controls() {

		$this->start_controls_section(
			'_section_readmore_style',
			[
				'label' => __( 'Read More', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => $this->conditions('read_more_content'),
				// 'condition' => [
				// 	$this->get_control_id( 'read_more!' ) => '',
				// ],
			]
		);

		$this->add_control(
			'readmore_overlay_heading',
			[
				'label' => __( 'Overlay', 'happy-addons-pro' ),
				'description' => __( 'This overlay color only apply when post has an image.', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'skin' => 'standard',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'readmore_overlay',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'description' => __( 'This overlay color only apply when post has an image.', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-pg-standard .ha-pg-item .ha-pg-readmore::before',
				'condition' => [
					'skin' => 'standard',
				],
			]
		);

		//Read More style
		$this->add_control(
			'readmore_heading',
			[
				'label' => __( 'Read More', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'skin' => 'standard',
				],
			]
		);

		$this->add_responsive_control(
			'readmore_margin',
			[
				'label' => __( 'Margin', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-readmore' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'skin!' => 'standard',
				],
			]
		);

		$this->add_responsive_control(
			'readmore_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-readmore a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				// 'condition' => [
				// 	$this->get_control_id( 'read_more!' ) => '',
				// ],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'readmore_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'exclude' => [
					'color',
				],
				'selector' => '{{WRAPPER}} .ha-pg-readmore a',
				// 'condition' => [
				// 	$this->get_control_id( 'read_more!' ) => '',
				// ],
			]
		);

		$this->add_responsive_control(
			'readmore_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-readmore a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				// 'condition' => [
				// 	$this->get_control_id( 'read_more!' ) => '',
				// ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'readmore_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-pg-readmore a',
				// 'condition' => [
				// 	$this->get_control_id( 'read_more!' ) => '',
				// ],
			]
		);

		$this->start_controls_tabs( 'readmore_tabs');
		// 	[
		// 		'condition' => [
		// 			$this->get_control_id( 'read_more!' ) => '',
		// 		],
		// 	]
		// );
		$this->start_controls_tab(
			'readmore_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'readmore_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-readmore a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'readmore_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-pg-readmore a',
			]
		);

		$this->add_control(
			'readmore_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-readmore a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'readmore_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'readmore_hover_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-readmore a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'readmore_hover_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-pg-readmore a:hover',
			]
		);

		$this->add_control(
			'readmore_border_hover_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-readmore a:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Paginations Style controls
	 */
	protected function pagination_style_tab_controls( ) {

		$this->start_controls_section(
			'_section_pagination_style',
			[
				'label' => __( 'Pagination', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'pagination_type',
							'operator' => '!=',
							'value' => '',

						],
						[
							'name' => 'loadmore',
							'operator' => '===',
							'value' => 'yes',

						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'pagination_margin',
			[
				'label' => __( 'Margin', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_spacing',
			[
				'label' => __( 'Space Between', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				// 'separator' => 'before',
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pagination_type!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pagination_typography',
				'selector' => '{{WRAPPER}} .ha-pg-pagination-wrap, {{WRAPPER}} .ha-pg-loadmore-wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pagination_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers, {{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore',
				'exclude' => ['color'],
			]
		);

		$this->add_responsive_control(
			'pagination_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				// 'condition' => [
				// 	'navigation_show' => 'yes',
				// ]
			]
		);

		$this->start_controls_tabs( 'pagination_tabs' );

		$this->start_controls_tab(
			'pagination_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_bg_color',
			[
				'label' => __( 'Background', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_hover',
			[
				'label' => __( 'Hover & Active', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'pagination_hover_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers:not([class~=dots]):hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers.current' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_bg_hover_color',
			[
				'label' => __( 'Background', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers:not([class~=dots]):hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers.current' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_border_hover_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers:not([class~=dots]):hover' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers.current' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}


	/**
	 * Added Devider Shape Style Control
	 */
	public function devider_shape_style_controls() {

		$this->start_controls_section(
			'_section_image_devider_shape_style',
			[
				'label' => __( 'Devider Shape', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'skin' => 'stylica',
					// $this->get_control_id( 'featured_image' ) => 'yes',
					'featured_image' => 'yes',
					// $this->get_control_id( 'devider_shape!' ) => 'none',
				],
			]
		);

		$this->add_control(
			'devider_shape',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Type', 'happy-addons-pro' ),
				'label_block' => false,
				'multiple' => true,
				'default' => 'clouds',
				'options' => [
					'none'     => __( 'None', 'happy-addons-pro' ),
					'clouds'     => __( 'Clouds', 'happy-addons-pro' ),
					'corner'     => __( 'Corner', 'happy-addons-pro' ),
					'cross-line' => __( 'Cross Line', 'happy-addons-pro' ),
					'curve'      => __( 'Curve', 'happy-addons-pro' ),
					'drops'      => __( 'Drops', 'happy-addons-pro' ),
					'mountains'  => __( 'Mountains', 'happy-addons-pro' ),
					'pyramids'   => __( 'Pyramids', 'happy-addons-pro' ),
					'splash'     => __( 'Splash', 'happy-addons-pro' ),
					'split'      => __( 'Split', 'happy-addons-pro' ),
					'tilt'       => __( 'Tilt', 'happy-addons-pro' ),
					'torn-paper' => __( 'Torn Paper', 'happy-addons-pro' ),
					'triangle'   => __( 'Triangle', 'happy-addons-pro' ),
					'wave'       => __( 'Wave', 'happy-addons-pro' ),
					'zigzag'     => __( 'Zigzag', 'happy-addons-pro' ),
				],
				// 'condition' => [
				// 	$this->get_control_id( 'featured_image' ) => 'yes',
				// ],
			]
		);

		$this->add_control(
			'devider_shape_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				// 'condition' => [
				// 	$this->get_control_id( 'featured_image' ) => 'yes',
				// 	$this->get_control_id( 'devider_shape!' ) => 'none',
				// ],
				'selectors' => [
					"{{WRAPPER}} .ha-pg-stylica .ha-pg-item .ha-pg-thumb-area svg" => 'fill: {{UNIT}};',
				],
				'condition' => [
					'devider_shape!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'devider_shape_width',
			[
				'label' => __( 'Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 100,
						'max' => 500,
					],
				],
				// 'condition' => [
				// 	$this->get_control_id( 'featured_image' ) => 'yes',
				// 	$this->get_control_id( 'devider_shape!' ) => 'none',
				// ],
				'selectors' => [
					"{{WRAPPER}} .ha-pg-stylica .ha-pg-item .ha-pg-thumb-area svg" => 'width: calc({{SIZE}}{{UNIT}} + 1.3px)',
				],
				'condition' => [
					'devider_shape!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'devider_shape_height',
			[
				'label' => __( 'Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 500,
					],
				],
				'default' => [
					'size' => 90,
				],
				// 'condition' => [
				// 	$this->get_control_id( 'featured_image' ) => 'yes',
				// 	$this->get_control_id( 'devider_shape!' ) => 'none',
				// ],
				'selectors' => [
					"{{WRAPPER}} .ha-pg-stylica .ha-pg-item .ha-pg-thumb-area svg" => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'devider_shape!' => 'none',
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Get Query
	 *
	 * @param array $args
	 * @return void
	 */
	public function get_query( $args = array() ) {

		$default = $this->get_post_query_args();
		$args = array_merge( $default, $args );

		$this->query = new WP_Query( $args );
		return $this->query;
	}

	/**
	 * Get post query arguments
	 *
	 * @return function
	 */
	public function get_post_query_args() {

		return $this->get_query_args();
	}

	/**
	 * Get current page number
	 *
	 * @return init
	 */
	public function get_current_page() {
		if ( '' === $this->get_settings_for_display( 'pagination_type' ) ) {
			return 1;
		}

		return max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
	}

	/**
	 * Get page number link
	 *
	 * @param [init] $i
	 * @return string
	 */
	private function get_wp_link_page( $i ) {
		if ( ! is_singular() || is_front_page() ) {
			return get_pagenum_link( $i );
		}

		// Based on wp-includes/post-template.php:957 `_wp_link_page`.
		global $wp_rewrite;
		$post = get_post();
		$query_args = [];
		$url = get_permalink();

		if ( $i > 1 ) {
			if ( '' === get_option( 'permalink_structure' ) || in_array( $post->post_status, [ 'draft', 'pending' ] ) ) {
				$url = add_query_arg( 'page', $i, $url );
			} elseif ( get_option( 'show_on_front' ) === 'page' && (int) get_option( 'page_on_front' ) === $post->ID ) {
				$url = trailingslashit( $url ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
			} else {
				$url = trailingslashit( $url ) . user_trailingslashit( 'page'.$i, 'single_paged' ); // Change Occurs For Fixing Pagination Issue.
			}
		}

		if ( is_preview() ) {
			if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) {
				$query_args['preview_id'] = wp_unslash( $_GET['preview_id'] );
				$query_args['preview_nonce'] = wp_unslash( $_GET['preview_nonce'] );
			}

			$url = get_preview_post_link( $post, $query_args, $url );
		}

		return $url;
	}

	/**
	 * Get post navigation link
	 *
	 * @param [init] $page_limit
	 * @return string
	 */
	public function get_posts_nav_link( $page_limit = null ) {
		if ( ! $page_limit ) {
			// return;
			$page_limit = $this->query->max_num_pages; // Change Occurs For Fixing Pagination Issue.
		}

		$return = [];

		// $paged = $this->get_current_page();
		$paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );

		$link_template = '<a class="page-numbers %s" href="%s">%s</a>';
		$disabled_template = '<span class="page-numbers %s">%s</span>';

		if ( $paged > 1 ) {
			$next_page = intval( $paged ) - 1;
			if ( $next_page < 1 ) {
				$next_page = 1;
			}

			$return['prev'] = sprintf( $link_template, 'prev', $this->get_wp_link_page( $next_page ), $this->get_settings_for_display( 'pagination_prev_label' ) );
		}
		// else {
		// 	$return['prev'] = sprintf( $disabled_template, 'prev', $this->get_settings_for_display( 'pagination_prev_label' ) );
		// }

		$next_page = intval( $paged ) + 1;

		if ( $next_page <= $page_limit ) {
			$return['next'] = sprintf( $link_template, 'next', $this->get_wp_link_page( $next_page ), $this->get_settings_for_display( 'pagination_next_label' ) );
		}
		// else {
		// 	$return['next'] = sprintf( $disabled_template, 'next', $this->get_settings_for_display( 'pagination_next_label' ) );
		// }

		return $return;
	}

	/**
	 * Pagination render
	 *
	 * @param [array] $_query
	 * @return void
	 */
	public function pagination_render($_query) {

		$parent_settings = $this->get_settings_for_display();
		if ( '' === $parent_settings['pagination_type'] ) {
			return;
		}

		$page_limit = $_query->max_num_pages;
		if ( '' !== $parent_settings['pagination_page_limit'] ) {
			$page_limit = min( $parent_settings['pagination_page_limit'], $page_limit );
		}

		if ( 2 > $page_limit ) {
			return;
		}

		$has_numbers = in_array( $parent_settings['pagination_type'], [ 'numbers', 'numbers_and_prev_next' ] );
		$has_prev_next = in_array( $parent_settings['pagination_type'], [ 'prev_next', 'numbers_and_prev_next' ] );

		$links = [];

		if ( $has_numbers ) {
			$paginate_args = [
				'type' => 'array',
				'current' => $this->get_current_page(),
				'total' => $page_limit,
				'prev_next' => false,
				'show_all' => 'yes' !== $parent_settings['pagination_numbers_shorten'],
				'before_page_number' => '<span class="elementor-screen-only">' . __( 'Page', 'happy-addons-pro' ) . '</span>',
			];

			if ( is_singular() && ! is_front_page() ) {
				global $wp_rewrite;
				if ( $wp_rewrite->using_permalinks() ) {
					$paginate_args['base'] = trailingslashit( get_permalink() ) . '%_%';
					$paginate_args['format'] = user_trailingslashit( 'page%#%', 'single_paged' ); // Change Occurs For Fixing Pagination Issue.
				} else {
					$paginate_args['format'] = '?page=%#%';
				}
			}

			$links = paginate_links( $paginate_args );
		}

		if ( $has_prev_next ) {
			$prev_next = $this->get_posts_nav_link( $page_limit );
			if(isset($prev_next['prev'])) {
				array_unshift( $links, $prev_next['prev'] );
			}
			if(isset($prev_next['next'])) {
				$links[] = $prev_next['next'];
			}
		}

		?>
		<nav class="ha-pg-pagination-wrap" role="navigation" aria-label="<?php esc_attr_e( 'Pagination', 'happy-addons-pro' ); ?>">
			<?php echo implode( PHP_EOL, $links ); ?>
		</nav>
		<?php
	}

	/**
	 * Load more render
	 *
	 * @param [array] $query_settings
	 * @return void
	 */
	public function load_more_render( $query_settings ) {

		$settings = $this->get_settings_for_display();
		if ( empty($settings['loadmore']) || empty($settings['loadmore_text']) ) {
			return;
		}
		?>
		<div class="ha-pg-loadmore-wrap">
			<button class="ha-pg-loadmore" data-settings="<?php echo esc_attr($query_settings);?>">
				<?php echo esc_html($settings['loadmore_text']);?>
				<i class="eicon-loading eicon-animation-spin"></i>
			</button>
		</div>
		<?php
	}

	/**
	 * Render content
	 */
	public function render() {

		$settings = $this->get_settings_for_display();

		// return;
		$this->add_render_attribute(
			'grid-wrapper',
			'class',
			[
				'ha-pg-wrapper',
				'ha-pg-default',
				'ha-pg-'. $settings['skin'],
			]
		);
		// $args = $this->get_query_args();
		$args = $this->get_post_query_args();

		$args['posts_per_page'] = $settings['posts_per_page'];
		if('' !== $settings['pagination_type']){
			$args['paged'] = $this->get_current_page();
		}

		//define ha post grid custom query filter hook
		if ( !empty( $settings['query_id'] ) ) {
			$args = apply_filters( "happyaddons/post-grid/{$settings['query_id']}", $args );
		}

		$_query = new WP_Query( $args );

		// get skin settings values
		//$button_custom_attr = $this->get_instance_value( 'button_custom_attr' );
		//$button_custom_attr_value = $this->get_instance_value( 'button_custom_attr_value' );

		$query_settings = $this->query_settings( $settings, $args );

		?>
		<?php if ( $_query->have_posts() ) : ?>
				<div <?php $this->print_render_attribute_string( 'grid-wrapper' ); ?>>
					<div class="ha-pg-grid-wrap">
					<?php while ( $_query->have_posts() ) : $_query->the_post(); ?>

						<?php $this->render_markup( $settings, $_query );?>

					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
					</div>
					<?php
						if( '' === $settings['pagination_type'] ){
							$this->load_more_render($query_settings);
						}else{
							$this->pagination_render($_query);
						}
					?>
				</div>
			<?php endif;?>
		<?php
	}

	public function render_markup( $settings, $_query ) {

		$this->{'new_render_' . $settings['skin'] . '_markup'}( $settings, $_query );

		// if( 'classic' == $settings['skin'] ){
		// 	return self::render_classic_markup( $settings, $_query );
		// }
		// elseif( 'hawai' == $settings['skin'] ){
		// 	return self::render_hawai_markup( $settings, $_query );
		// }
		// elseif( 'standard' == $settings['skin'] ){
		// 	return self::render_standard_markup( $settings, $_query );
		// }
		// elseif( 'monastic' == $settings['skin'] ){
		// 	return self::render_monastic_markup( $settings, $_query );
		// }
		// elseif( 'stylica' == $settings['skin'] ){
		// 	return self::render_stylica_markup( $settings, $_query );
		// }
		// elseif( 'outbox' == $settings['skin'] ){
		// 	return self::render_outbox_markup( $settings, $_query );
		// }
		// elseif( 'crossroad' == $settings['skin'] ){
		// 	return self::render_crossroad_markup( $settings, $_query );
		// }

	}

	public function query_settings( $settings, $args ) {

		$query_settings = [
			'args'                => $args,
			// '_skin'            => $this->get_id(),
			'skin'               => $settings['skin'],
			'posts_post_type'     => $settings['posts_post_type'],
			'featured_image'      => $settings['featured_image'],
			'featured_image_size' => $settings['featured_image_size'],
			'show_badge'          => $settings['show_badge'],
			'show_title'          => $settings['show_title'],
			'title_tag'           => $settings['title_tag'],
			'active_meta'         => $settings['active_meta'],
			'excerpt_length'      => $settings['excerpt_length'],
		];

		if( !empty($settings['active_meta']) ){
			$query_settings ['meta_has_icon'] = $settings['meta_has_icon'];
		}

		if( !empty($settings['show_badge'] ) ){
			$query_settings ['taxonomy_badge'] = $settings['taxonomy_badge'];
		}

		if( 'classic' == $settings['skin'] || 'hawai' == $settings['skin'] || 'standard' == $settings['skin']){
			$query_settings ['read_more'] = $settings['read_more'];
			$query_settings ['read_more_new_tab'] = $settings['read_more_new_tab'];
		}

		if( 'standard' == $settings['skin'] ){
			$query_settings ['meta_position'] = $settings['meta_position'];
		}

		if( 'stylica' == $settings['skin'] ){
			$query_settings ['devider_shape'] = $settings['devider_shape'];
		}

		$query_settings = json_encode( $query_settings, true );

		return $query_settings;
	}

}

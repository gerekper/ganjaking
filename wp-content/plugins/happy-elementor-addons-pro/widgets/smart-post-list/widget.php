<?php
/**
 * Smart Post List widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Happy_Addons_Pro\Traits\Lazy_Query_Builder;
use Happy_Addons_Pro\Controls\Lazy_Select;
use Happy_Addons_Pro\Lazy_Query_Manager;
use Happy_Addons_Pro\Traits\Smart_Post_List_Markup;


defined( 'ABSPATH' ) || die();
class Smart_Post_List extends Base {

	use Lazy_Query_Builder;
	use Smart_Post_List_Markup;

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title () {
		return __( 'Smart Post List', 'happy-addons-pro' );
	}

	public function get_custom_help_url () {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/smart-post-list/';
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon () {
		return 'hm hm-post-list';
	}

	public function get_keywords () {
		return [ 'smart-post-list', 'smart', 'posts', 'post', 'post-list', 'list', 'news' ];
	}

	public function conditions ($key) {
		$condition = [
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
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__layout_content_controls();
		$this->__top_bar_content_controls();
		$this->__featured_post_content_controls();
		$this->__list_post_content_controls();
		$this->__query_content_controls();
	}

	//Layout Settings
	protected function __layout_content_controls() {

		//Layout Settings
		$this->start_controls_section(
			'_section_spl_layout',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'column',
			[
				'label' => __( 'Column', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'col-3',
				'options' => [
					'col-1' => __('Column 1', 'happy-addons-pro'),
					'col-2' => __('Column 2', 'happy-addons-pro'),
					'col-3' => __('Column 3', 'happy-addons-pro'),
				],
			]
		);

		$this->add_control(
			'top_bar_show',
			[
				'label' => __( 'Show Top Bar', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'make_featured_post',
			[
				'label' => __( 'First Post Featured', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'featured_post_column',
			[
				'label' => __( 'Featured Post Column', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'featured-col-1',
				'options' => [
					'featured-col-1' => __('Column 1', 'happy-addons-pro'),
					'featured-col-2' => __('Column 2', 'happy-addons-pro'),
				],
				'condition' => [
					'column!' => 'col-1',
					'make_featured_post' => 'yes',
				]
			]
		);

		$this->end_controls_section();
	}

	//Top Bar Settings
	protected function __top_bar_content_controls() {

		//Top Bar Settings
		$this->start_controls_section(
			'_section_spl_top_bar',
			[
				'label' => __( 'Top Bar', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'top_bar_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'widget_title',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Trending Articles',
			]
		);

		$this->add_control(
			'widget_title_tag',
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
					'widget_title!' => '',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'navigation_show',
			[
				'label' => __( 'Navigation', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'category_filter',
			[
				'label' => __( 'Show Filter', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'category_filter_style',
			[
				'label' => __( 'Filter Style', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'dropdown',
				'options' => [
					'inline' => __('Inline', 'happy-addons-pro'),
					'dropdown' => __('Dropdown', 'happy-addons-pro'),
				],
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);

		$this->add_control(
			'filter_terms_ids',
			[
				'label' => __( 'Filter Terms', 'happy-addons-pro' ),
				'description' => __( 'Terms are items in a taxonomy. The available taxonomies are: Categories, Tags, Formats and custom taxonomies.Select terms for selected filter item.by default, it will show all taxonomy under selected post type in the query settings.', 'happy-addons-pro' ),
				'type' => Lazy_Select::TYPE,
				'multiple' => true,
				'label_block' => true,
				'placeholder' => __( 'Type and select terms', 'happy-addons-pro' ),
				'lazy_args' => [
					'query' => Lazy_Query_Manager::QUERY_TERMS,
					'widget_props' => [
						'post_type' => 'posts_post_type'
					]
				],
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);

		$this->add_control(
			'filter_all_text',
			[
				'label' => __( 'All Text', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'default' =>  __( 'All', 'happy-addons-pro' ),
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);

		$this->end_controls_section();

	}

	//Featured Post Settings
	protected function __featured_post_content_controls() {

		//Featured Post Settings
		$this->start_controls_section(
			'_section_spl_featured_post',
			[
				'label' => __( 'Featured Post', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'make_featured_post' => 'yes',
				]
			]
		);

		$this->add_control(
			'featured_post_style',
			[
				'label' => __( 'Style', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'inside-conent',
				'options' => [
					'inside-conent' => __('Content Inside', 'happy-addons-pro'),
					'outside-conent' => __('Content Outside', 'happy-addons-pro'),
				],
				'condition' => [
					'make_featured_post' => 'yes',
				]
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
				'condition' => [
					'make_featured_post' => 'yes'
				]
			]
		);

		/* $this->add_control(
			'featured_post_cat',
			[
				'label' => __( 'Highlighted Category', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'make_featured_post' => 'yes',
					'posts_post_type' => 'post',
				]
			]
		); */

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
					'show_badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'featured_post_title',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'make_featured_post' => 'yes',
				]
			]
		);

		$this->add_control(
			'featured_post_title_tag',
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
					'make_featured_post' => 'yes',
					'featured_post_title' => 'yes'
				],
			]
		);

		$this->add_control(
			'featured_meta_active',
			[
				'type' => Controls_Manager::SELECT2,
				'label' => __( 'Active Meta', 'happy-addons-pro' ),
				'description' => __( 'Select to show and unselect to hide', 'happy-addons-pro' ),
				'label_block' => true,
				'multiple' => true,
				'default' => ['author', 'date', 'comments'],
				'options' => [
					'author' => __( 'Author', 'happy-addons-pro' ),
					'date' => __( 'Date', 'happy-addons-pro' ),
					'comments' => __( 'Comments', 'happy-addons-pro' ),
				]
			]
		);

		$this->add_control(
            'featured_post_author_icon',
            [
                'label' => esc_html__( 'Author Icon', 'happy-addons-pro' ),
                'type' => Controls_Manager::ICONS,
                'label_block' => false,
                'skin' => 'inline',
                'default' => [
                    'value' => 'fas fa-user',
                    'library' => 'fa-solid',
                ],
                'condition' => [
					'make_featured_post' => 'yes',
					'featured_meta_active' => 'author',
                ],
            ]
        );

		$this->add_control(
            'featured_post_date_icon',
            [
                'label' => esc_html__( 'Date Icon', 'happy-addons-pro' ),
                'type' => Controls_Manager::ICONS,
                'label_block' => false,
                'skin' => 'inline',
                'default' => [
                    'value' => 'fas fa-calendar-alt',
                    'library' => 'fa-solid',
				],
                'condition' => [
					'make_featured_post' => 'yes',
					'featured_meta_active' => 'date',
                ],
            ]
        );

		$this->add_control(
            'featured_post_comment_icon',
            [
                'label' => esc_html__( 'Comment Icon', 'happy-addons-pro' ),
                'type' => Controls_Manager::ICONS,
                'label_block' => false,
                'skin' => 'inline',
                'default' => [
                    'value' => 'fas fa-comments',
                    'library' => 'fa-solid',
				],
                'condition' => [
					'make_featured_post' => 'yes',
					'featured_meta_active' => 'comments',
                ],
            ]
		);

		$this->add_control(
			'featured_excerpt_length',
			[
				'type' => Controls_Manager::NUMBER,
				'label' => __( 'Excerpt Length', 'happy-addons-pro' ),
				'min' => 0,
				'default' => 15,
				'condition' => [
					'make_featured_post' => 'yes',
				]
			]
		);

		$this->add_control(
			'featured_post_align',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'label_block' => false,
				'type' => Controls_Manager::CHOOSE,
				'default' => 'bottom',
				'options' => [
					'top' => [
						'title' => __( 'Top', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'style_transfer' => true,
				'selectors_dictionary' => [
					'top' => '-webkit-box-align:start;-ms-flex-align:start;align-items:flex-start;',
					'middle' => '-webkit-box-align:center;-ms-flex-align:center;align-items:center;',
					'bottom' => '-webkit-box-align:end;-ms-flex-align:end;align-items:flex-end;',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-inside-conent' => '{{VALUE}};',
					'{{WRAPPER}} .ha-spl-featured-outside-conent' => '{{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}

	//List Post Settings
	protected function __list_post_content_controls() {

		//List Post Settings
		$this->start_controls_section(
			'_section_spl_list_post',
			[
				'label' => __( 'List Post', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'conditions' => $this->conditions('list_post_show'),
			]
		);

		$this->add_control(
			'list_post_title_tag',
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
			]
		);

		$this->add_control(
			'list_post_image',
			[
				'label' => __( 'Image', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
                'conditions' => [
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
                                    'name' => 'column',
                                    'operator' => 'in',
                                    'value' => ['col-1','col-2','col-3'],
                                ],
                            ],
                        ]
                    ],
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'list_post_image',
				'default' => 'thumbnail',
				'exclude' => [
					'custom'
				],
				'condition' => [
					'list_post_image' => 'yes'
				]
			]
		);

		$this->add_control(
			'list_meta_active',
			[
				'type' => Controls_Manager::SELECT2,
				'label' => __( 'Active Meta', 'happy-addons-pro' ),
				'description' => __( 'Select to show and unselect to hide', 'happy-addons-pro' ),
				'label_block' => true,
				'multiple' => true,
				'default' => ['author'],
				'options' => [
					'author' => __( 'Author', 'happy-addons-pro' ),
					'date' => __( 'Date', 'happy-addons-pro' ),
					'comments' => __( 'Comments', 'happy-addons-pro' ),
				]
			]
		);

		$this->add_control(
            'list_post_author_icon',
            [
                'label' => esc_html__( 'Author Icon', 'happy-addons-pro' ),
                'type' => Controls_Manager::ICONS,
                'label_block' => false,
                'skin' => 'inline',
                'default' => [
                    'value' => 'fas fa-user',
                    'library' => 'fa-solid',
				],
                'condition' => [
					'list_meta_active' => 'author',
                ],
            ]
        );

		$this->add_control(
            'list_post_date_icon',
            [
                'label' => esc_html__( 'Date Icon', 'happy-addons-pro' ),
                'type' => Controls_Manager::ICONS,
                'label_block' => false,
                'skin' => 'inline',
                'default' => [
                    'value' => 'fas fa-calendar-alt',
                    'library' => 'fa-solid',
                ],
                'condition' => [
					'list_meta_active' => 'date',
                ],
            ]
        );

		$this->add_control(
            'list_post_comment_icon',
            [
                'label' => esc_html__( 'Comment Icon', 'happy-addons-pro' ),
                'type' => Controls_Manager::ICONS,
                'label_block' => false,
                'skin' => 'inline',
                'default' => [
                    'value' => 'fas fa-comments',
                    'library' => 'fa-solid',
				],
                'condition' => [
					'list_meta_active' => 'comments',
                ],
            ]
		);

		$this->add_control(
			'list_post_align',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'label_block' => false,
				'type' => Controls_Manager::CHOOSE,
				'default' => 'top',
				'options' => [
					'top' => [
						'title' => __( 'Top', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'style_transfer' => true,
				'selectors_dictionary' => [
					'top' => '-webkit-box-align:start;-ms-flex-align:start;align-items:flex-start;',
					'middle' => '-webkit-box-align:center;-ms-flex-align:center;align-items:center;',
					'bottom' => '-webkit-box-align:end;-ms-flex-align:end;align-items:flex-end;',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-list' => '{{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}

	//Query Settings
	protected function __query_content_controls() {

		$this->start_controls_section(
			'_section_spl_query',
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
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__layout_style_controls();
		$this->__top_bar_style_controls();
		$this->__top_bar_title_style_controls();
		$this->__top_bar_filter_style_controls();
		$this->__top_bar_navigation_style_controls();
		$this->__featured_post_style_controls();
		$this->__featured_post_badge_style_controls();
		$this->__list_post_style_controls();
	}

	//Layout Style
	protected function __layout_style_controls(){

		$this->start_controls_section(
			'_section_spl_layout_style',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				// 'condition' => [
				// 	'make_featured_post' => 'yes',
				// 	'show_badge' => 'yes',
				// ],
			]
		);

		$this->add_responsive_control(
			'spl_grid_gap',
			[
				'label' => __( 'Column Gap', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => '30',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-grid-area' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-spl-grid-area.ha-spl-featured-post-on .ha-spl-list-wrap' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'spl_grid_row_gap',
			[
				'label' => __( 'Row Gap', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => '30',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-grid-area' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-spl-grid-area.ha-spl-featured-post-on .ha-spl-list-wrap' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'spl_feature_single_grid_height',
			[
				'label' => __( 'Feature Item Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-grid-area.ha-spl-col-1.ha-spl-featured-post-on' => 'grid-template-rows: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-spl-grid-area.ha-spl-col-2.ha-spl-featured-post-on' => 'grid-template-rows: {{SIZE}}{{UNIT}};',
				],
				'conditions' => $this->conditions('feature_item_height'),
			]
		);

		$this->add_responsive_control(
			'spl_post_list_grid_height',
			[
				'label' => __( 'Post List Item Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-grid-area.ha-spl-featured-post-off' => 'grid-auto-rows: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-spl-list-wrap' => 'grid-auto-rows: {{SIZE}}{{UNIT}};',
				],
				'conditions' => $this->conditions('list_post_show'),
			]
		);

		$this->end_controls_section(); //Layout Style End
	}

	//Top Bar Style
	protected function __top_bar_style_controls() {

		//Top Bar Style
		$this->start_controls_section(
			'_section_spl_top_bar_style',
			[
				'label' => __( 'Topbar', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'top_bar_show' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_margin',
			[
				'label' => __( 'Margin', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'spl_top_bar_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-spl-header',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'spl_top_bar_box_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-spl-header',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'spl_top_bar_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-spl-header',
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section(); //Top Bar Style End
	}

	//Top Bar Title Style
	protected function __top_bar_title_style_controls(){

		$this->start_controls_section(
			'_section_spl_top_bar_title_style',
			[
				'label' => __( 'Topbar Title', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'top_bar_show' => 'yes',
					'widget_title!' => '',
				],
			]
		);

		//Widget Title
		/* $this->add_control(
			'spl_top_bar_widget_title_heading',
			[
				'label' => __( 'Widget Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'widget_title!' => '',
				],
			]
		); */

		$this->add_responsive_control(
			'spl_top_bar_widget_title_margin_right',
			[
				'label' => __( 'Margin Right', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-widget-title' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'widget_title!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_widget_title_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-widget-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'widget_title!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'spl_top_bar_widget_title_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-spl-widget-title',
				'condition' => [
					'widget_title!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'spl_top_bar_widget_title_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-spl-widget-title',
				'condition' => [
					'widget_title!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_widget_title_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-widget-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'widget_title!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'spl_top_bar_widget_title_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-spl-widget-title',
				'condition' => [
					'widget_title!' => '',
				],
			]
		);

		$this->add_control(
			'spl_top_bar_widget_title_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-widget-title' => 'color: {{VALUE}}',
				],
				'condition' => [
					'widget_title!' => '',
				],
			]
		);

		$this->end_controls_section(); //Top Bar Title Style End
	}

	//Top Bar Filter Style
	protected function __top_bar_filter_style_controls(){

		$this->start_controls_section(
			'_section_spl_top_bar_filter_style',
			[
				'label' => __( 'Topbar Filter', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'top_bar_show' => 'yes',
					'category_filter' => 'yes',
				],
			]
		);

		//Inline Filter
		$this->add_control(
			'spl_top_bar_filter_heading',
			[
				'label' => __( 'Inline Filter', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'category_filter' => 'yes',
					'category_filter_style' => 'inline',
				]
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_filter_item_margin',
			[
				'label' => __( 'Item margin', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-filter ul.ha-spl-filter-list li span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'category_filter' => 'yes',
					'category_filter_style' => 'inline',
				]
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_filter_item_padding',
			[
				'label' => __( 'Item Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-filter ul.ha-spl-filter-list li span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'category_filter' => 'yes',
					'category_filter_style' => 'inline',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'spl_top_bar_filter_item_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-spl-filter ul.ha-spl-filter-list li span',
				'condition' => [
					'category_filter' => 'yes',
					'category_filter_style' => 'inline',
				]
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_filter_item_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-filter ul.ha-spl-filter-list li span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'category_filter' => 'yes',
					'category_filter_style' => 'inline',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'spl_top_bar_filter_item_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-spl-filter ul.ha-spl-filter-list li span',
				'condition' => [
					'category_filter' => 'yes',
					'category_filter_style' => 'inline',
				]
			]
		);

		$this->start_controls_tabs( 'spl_top_bar_filter_tabs',
			[
				'condition' => [
					'category_filter' => 'yes',
					'category_filter_style' => 'inline',
				]
			]
		);
		$this->start_controls_tab(
			'spl_top_bar_filter_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_top_bar_filter_item_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-filter ul.ha-spl-filter-list li span' => 'color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'spl_top_bar_filter_item_background_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-filter ul.ha-spl-filter-list li span' => 'background-color: {{VALUE}}',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'spl_top_bar_filter_hover_tab',
			[
				'label' => __( 'Hover/Active', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_top_bar_filter_item_hover_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-filter ul.ha-spl-filter-list li span:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-spl-filter ul.ha-spl-filter-list li span.ha-active' => 'color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'spl_top_bar_filter_item_hover_background_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-filter ul.ha-spl-filter-list li span:hover' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ha-spl-filter ul.ha-spl-filter-list li span.ha-active' => 'background-color: {{VALUE}}',
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		//Dropdown Filter
		$this->add_control(
			'spl_top_bar_select_heading',
			[
				'label' => __( 'Dropdown Filter', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_nice_select_height',
			[
				'label' => __( 'Select Box Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .nice-select.ha-spl-custom-select' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_nice_select_space',
			[
				'label' => __( 'Space Right', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .nice-select.ha-spl-custom-select' => 'margin-right: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_nice_select_padding',
			[
				'label' => __( 'Dropdown Item Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .nice-select.ha-spl-custom-select .option' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'spl_top_bar_nice_select_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .nice-select.ha-spl-custom-select,{{WRAPPER}} .nice-select.ha-spl-custom-select .list',
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_nice_select_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .nice-select.ha-spl-custom-select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .nice-select.ha-spl-custom-select .list' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'spl_top_bar_nice_select_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .nice-select.ha-spl-custom-select span.current,
				{{WRAPPER}} .nice-select.ha-spl-custom-select .option',
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);

		$this->start_controls_tabs( 'spl_top_bar_nice_select_tabs',
			[
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);
		$this->start_controls_tab(
			'spl_top_bar_nice_select_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_top_bar_nice_select_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nice-select.ha-spl-custom-select span.current' => 'color: {{VALUE}}',
					'{{WRAPPER}} .nice-select.ha-spl-custom-select:after' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .nice-select.ha-spl-custom-select .option' => 'color: {{VALUE}}',
				],
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);

		$this->add_control(
			'spl_top_bar_nice_select_background_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nice-select.ha-spl-custom-select' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .nice-select.ha-spl-custom-select .option' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .nice-select.ha-spl-custom-select .list:hover .option:not(:hover)' => 'background-color: {{VALUE}}!important',
				],
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'spl_top_bar_nice_select_hover_tab',
			[
				'label' => __( 'Hover/Active', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_top_bar_nice_select_hover_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nice-select.ha-spl-custom-select .option:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .nice-select.ha-spl-custom-select .option.focus' => 'color: {{VALUE}}',
					'{{WRAPPER}} .nice-select.ha-spl-custom-select .option.selected.focus' => 'color: {{VALUE}}',
				],
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);

		$this->add_control(
			'spl_top_bar_nice_select_hover_background_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nice-select.ha-spl-custom-select .option:hover' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .nice-select.ha-spl-custom-select .option.selected.focus' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'category_filter' => 'yes',
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();


		$this->end_controls_section(); //Top Bar Filter Style End
	}

	//Top Bar Navigation Style
	protected function __top_bar_navigation_style_controls(){

		$this->start_controls_section(
			'_section_spl_top_bar_navigation_style',
			[
				'label' => __( 'Topbar Navigation', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'top_bar_show' => 'yes',
					'navigation_show' => 'yes',
				],
			]
		);

		//Navigation
		/* $this->add_control(
			'spl_top_bar_nav_heading',
			[
				'label' => __( 'Navigation', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'navigation_show' => 'yes',
				]
			]
		); */

		$this->add_responsive_control(
			'spl_top_bar_nav_space',
			[
				'label' => __( 'Space Between', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-pagination button:first-child' => 'margin-right: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'navigation_show' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_nav_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-pagination button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation_show' => 'yes',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'spl_top_bar_nav_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-spl-pagination button',
				'exclude' => ['color'],
				'condition' => [
					'navigation_show' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_nav_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-pagination button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation_show' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'spl_top_bar_nav_font_size',
			[
				'label' => __( 'Icon Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-pagination button i' => 'font-size: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'navigation_show' => 'yes',
				]
			]
		);

		$this->start_controls_tabs( 'spl_top_bar_nav_tabs',
			[
				'condition' => [
					'navigation_show' => 'yes',
				]
			]
		);
		$this->start_controls_tab(
			'spl_top_bar_nav_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_top_bar_nav_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-pagination button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'spl_top_bar_nav_background_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-pagination button' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'spl_top_bar_nav_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-pagination button' => 'border-color: {{VALUE}}',
				],
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'spl_top_bar_nav_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_top_bar_nav_hover_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-pagination button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'spl_top_bar_nav_hover_background_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-pagination button:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'spl_top_bar_nav_border_hover_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-pagination button:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();


		$this->end_controls_section(); //Top Bar Navigation Style End
	}

	//Featured Post Style
	protected function __featured_post_style_controls(){

		//Feature Post Style
		$this->start_controls_section(
			'_section_spl_feature_post_style',
			[
				'label' => __( 'Featured Post', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'make_featured_post' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'spl_feature_post_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'spl_feature_post_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-spl-featured-post-wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'spl_feature_post_box_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-spl-featured-post-wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'spl_feature_post_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-spl-featured-post-wrap',
			]
		);

		$this->add_responsive_control(
			'spl_feature_post_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		//Feature Post Image overlay color
		$this->add_control(
			'spl_feature_post_image_overlay_heading',
			[
				'label' => __( 'Image Overlay', 'happy-addons-pro' ),
				'description' => __( 'This overlay color only apply when post has an image.', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'featured_post_style' => 'inside-conent',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'spl_feature_post_image_overlay',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'description' => __( 'This overlay color only apply when post has an image.', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-spl-featured-inside-conent .ha-spl-featured-thumb:before',
				'condition' => [
					'featured_post_style' => 'inside-conent',
				],
			]
		);

		$this->add_control(
			'spl_feature_post_image_overlay_note',
			[
				'label' => __( 'Image Overlay Note', 'happy-addons-pro' ),
				'show_label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'This overlay color only apply when post has an image.', 'happy-addons-pro' ),
				'content_classes' => 'elementor-control-field-description',
				'condition' => [
					'featured_post_style' => 'inside-conent',
				],
			]
		);

		//Feature Post Image
		$this->add_control(
			'spl_feature_post_image_heading',
			[
				'label' => __( 'Image', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'featured_post_style' => 'outside-conent',
				],
			]
		);

		$this->add_responsive_control(
			'spl_feature_post_image_width',
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
					'{{WRAPPER}} .ha-spl-featured-outside-conent .ha-spl-featured-thumb' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'featured_post_style' => 'outside-conent',
				],
			]
		);

		$this->add_responsive_control(
			'spl_feature_post_image_margin_btm',
			[
				'label' => __( 'Margin Bottom', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-outside-conent .ha-spl-featured-thumb' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'featured_post_style' => 'outside-conent',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'spl_feature_post_image_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-spl-featured-outside-conent .ha-spl-featured-thumb img',
				'condition' => [
					'featured_post_style' => 'outside-conent',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'spl_feature_post_image_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-spl-featured-outside-conent .ha-spl-featured-thumb img',
				'condition' => [
					'featured_post_style' => 'outside-conent',
				],
			]
		);

		$this->add_responsive_control(
			'spl_feature_post_image_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-outside-conent .ha-spl-featured-thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'featured_post_style' => 'outside-conent',
				],
			]
		);

		//Feature Post Title
		$this->add_control(
			'spl_feature_post_title_heading',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'featured_post_title' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'spl_feature_post_title_margin_btm',
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
				'default' => [
					'unit' => 'px',
					'size' => '10',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-title' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-top: 0;',
				],
				'condition' => [
					'featured_post_title' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'spl_feature_post_title_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-spl-featured-post .ha-spl-title a',
				'condition' => [
					'featured_post_title' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'spl_feature_post_title_tabs',
			[
				'condition' => [
					'featured_post_title' => 'yes',
				],
			]
		);
		$this->start_controls_tab(
			'spl_feature_post_title_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_feature_post_title_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-title a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'spl_feature_post_title_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_feature_post_title_hover_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-title a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		//Feature Post Meta
		$this->add_control(
			'spl_feature_post_meta_heading',
			[
				'label' => __( 'Meta', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $this->conditions('feature_meta_style'),
			]
		);

		$this->add_responsive_control(
			'spl_feature_post_meta_icon_size',
			[
				'label' => __( 'Icon Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-meta .ha-spl-meta-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'conditions' => $this->conditions('feature_meta_style'),
			]
		);

		$this->add_responsive_control(
			'spl_feature_post_meta_icon_space',
			[
				'label' => __( 'Icon Space', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-meta ul li i' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-meta ul li svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'conditions' => $this->conditions('feature_meta_style'),
			]
		);

		$this->add_responsive_control(
			'spl_feature_post_meta_space',
			[
				'label' => __( 'Space Between', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-meta ul li' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-meta ul li:last-child' => 'margin-right: 0;',
				],
				'conditions' => $this->conditions('feature_meta_style'),
			]
		);

		$this->add_responsive_control(
			'spl_feature_post_meta_margin_btm',
			[
				'label' => __( 'Margin Bottom', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-meta ul li' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'conditions' => $this->conditions('feature_meta_style'),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'spl_feature_post_meta_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-spl-featured-post .ha-spl-meta .ha-spl-meta-text',
				'conditions' => $this->conditions('feature_meta_style'),
			]
		);

		$this->start_controls_tabs( 'spl_feature_post_meta_tabs',
			[
				'conditions' => $this->conditions('feature_meta_style'),
			]
		);
		$this->start_controls_tab(
			'spl_feature_post_meta_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_feature_post_meta_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-meta .ha-spl-meta-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-meta .ha-spl-meta-icon path' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-meta .ha-spl-meta-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'spl_feature_post_meta_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_feature_post_meta_hover_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-meta a:hover .ha-spl-meta-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-meta a:hover .ha-spl-meta-icon path' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-meta a:hover .ha-spl-meta-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		//Feature Post Content
		$this->add_control(
			'spl_feature_post_content_heading',
			[
				'label' => __( 'Content', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'featured_excerpt_length!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'spl_feature_post_content_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-spl-featured-post .ha-spl-desc',
				'condition' => [
					'featured_excerpt_length!' => '',
				],
			]
		);

		$this->add_control(
			'spl_feature_post_content_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-desc' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-desc p' => 'margin-bottom: 0',
				],
				'condition' => [
					'featured_excerpt_length!' => '',
				],
			]
		);

		$this->end_controls_section(); //Feature Post Style End
	}

	//Featured Post Badge Style
	protected function __featured_post_badge_style_controls(){

		//Taxonomy Badge
		$this->start_controls_section(
			'_section_spl_featured_post_badge_style',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'make_featured_post' => 'yes',
					'show_badge' => 'yes',
				],
			]
		);

		//Taxonomy Badge
		/* $this->add_control(
			'spl_feature_badge_heading',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_badge' => 'yes',
				],
			]
		); */

		$this->add_responsive_control(
			'spl_feature_badge_margin_btm',
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
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-badge a' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_badge' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'spl_feature_badge_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-badge a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_badge' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'spl_feature_badge_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'exclude' => [
					'color'
				],
				'selector' => '{{WRAPPER}} .ha-spl-featured-post .ha-spl-badge a',
				'condition' => [
					'show_badge' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'spl_feature_badge_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-badge a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_badge' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'spl_feature_badge_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-spl-featured-post .ha-spl-badge a',
				'condition' => [
					'show_badge' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'spl_feature_badge_tabs',
			[
				'condition' => [
					'show_badge' => 'yes',
				],
			]
		);
		$this->start_controls_tab(
			'spl_feature_badge_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_feature_badge_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-badge a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'spl_feature_badge_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-spl-featured-post .ha-spl-badge a',
			]
		);

		$this->add_control(
			'spl_feature_badge_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-badge a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'spl_feature_badge_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_feature_badge_hover_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-badge a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'spl_feature_badge_hover_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-spl-featured-post .ha-spl-badge a:hover',
			]
		);

		$this->add_control(
			'spl_feature_badge_hover_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-featured-post .ha-spl-badge a:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section(); //Featured Post Badge Style End
	}

	//List Post Style
	protected function __list_post_style_controls(){

		//List Post Style
		$this->start_controls_section(
			'_section_spl_list_post_style',
			[
				'label' => __( 'List Post', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => $this->conditions('list_post_show'),
			]
		);

		$this->add_responsive_control(
			'spl_list_post_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'spl_list_post_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-spl-list',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'spl_list_post_box_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-spl-list',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'spl_list_post_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-spl-list',
			]
		);

		$this->add_responsive_control(
			'spl_list_post_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-list' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		//List Post Image
		$this->add_control(
			'spl_list_post_image_heading',
			[
				'label' => __( 'Image', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'list_post_image' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'spl_list_post_image_width',
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
					'{{WRAPPER}} .ha-spl-list .ha-spl-list-thumb' => 'max-width: {{SIZE}}{{UNIT}};-webkit-box-flex: 0;-webkit-flex: 0 0 {{SIZE}}{{UNIT}};-ms-flex: 0 0 {{SIZE}}{{UNIT}};flex: 0 0 {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'list_post_image' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'spl_list_post_image_margin_right',
			[
				'label' => __( 'Margin Right', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-list .ha-spl-list-thumb' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'list_post_image' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'spl_list_post_image_shadow',
				'label' => __( 'Box Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-spl-list .ha-spl-list-thumb img',
				'condition' => [
					'list_post_image' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'spl_list_post_image_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-spl-list .ha-spl-list-thumb img',
				'condition' => [
					'list_post_image' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'spl_list_post_image_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-list .ha-spl-list-thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'list_post_image' => 'yes',
				],
			]
		);

		//List Post Title
		$this->add_control(
			'spl_list_post_title_heading',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'spl_list_post_title_margin_btm',
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
				'selectors' => [
					'{{WRAPPER}} .ha-spl-list .ha-spl-list-title' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-top: 0;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'spl_list_post_title_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-spl-list .ha-spl-list-title a',
			]
		);

		$this->start_controls_tabs( 'spl_list_post_title_tabs');
		$this->start_controls_tab(
			'spl_list_post_title_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_list_post_title_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-list .ha-spl-list-title a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'spl_list_post_title_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_list_post_title_hover_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-list .ha-spl-list-title a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		//List Post Meta
		$this->add_control(
			'spl_list_post_meta_heading',
			[
				'label' => __( 'Meta', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $this->conditions('list_post_meta_style'),
			]
		);

		$this->add_responsive_control(
			'spl_list_post_meta_icon_size',
			[
				'label' => __( 'Icon Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-list .ha-spl-meta .ha-spl-meta-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'conditions' => $this->conditions('list_post_meta_style'),
			]
		);

		$this->add_responsive_control(
			'spl_list_post_meta_icon_space',
			[
				'label' => __( 'Icon Space', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-list .ha-spl-meta ul li i' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-spl-list .ha-spl-meta ul li svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'conditions' => $this->conditions('list_post_meta_style'),
			]
		);

		$this->add_responsive_control(
			'spl_list_post_meta_space',
			[
				'label' => __( 'Space Between', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-spl-list .ha-spl-meta ul li' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-spl-list .ha-spl-meta ul li:last-child' => 'margin-right: 0;',
				],
				'conditions' => $this->conditions('list_post_meta_style'),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'spl_list_post_meta_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-spl-list .ha-spl-meta .ha-spl-meta-text',
				'conditions' => $this->conditions('list_post_meta_style'),
			]
		);

		$this->start_controls_tabs( 'spl_list_post_meta_tabs',
			[
				'conditions' => $this->conditions('list_post_meta_style'),
			]
		);
		$this->start_controls_tab(
			'spl_list_post_meta_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_list_post_meta_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-list .ha-spl-meta .ha-spl-meta-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-spl-list .ha-spl-meta .ha-spl-meta-icon path' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .ha-spl-list .ha-spl-meta .ha-spl-meta-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'spl_list_post_meta_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'spl_list_post_meta_hover_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-spl-list .ha-spl-meta a:hover .ha-spl-meta-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-spl-list .ha-spl-meta a:hover .ha-spl-meta-icon path' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .ha-spl-list .ha-spl-meta a:hover .ha-spl-meta-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section(); //List Post Style End
	}


	/**
	 * get column class
	 */
	public function get_column_cls ($column) {

		switch( $column ){
			case "col-1":
				$column_cls = "ha-spl-col-1";
				break;
			case "col-2":
				$column_cls = "ha-spl-col-2";
				break;
			case "col-3":
				$column_cls = "ha-spl-col-3";
				break;
			default:
			  	$column_cls = "ha-spl-col-3";
		}

		return $column_cls;

	}

	/**
	 * get featured column class
	 */
	public function get_featured_column_cls ($column) {

		switch( $column ){
			case "featured-col-1":
				$column_cls = "ha-spl-featured-col-1";
				break;
			case "featured-col-2":
				$column_cls = "ha-spl-featured-col-2";
				break;
			default:
			  	$column_cls = "ha-spl-featured-col-1";
		}

		return $column_cls;

	}

	/**
	 * get item per page
	 */
	public function get_item_per_page ($item = 'per_page') {
		$settings = $this->get_settings_for_display();

		$per_page = -1;
		$list_column = '';

		if( 'col-1' === $settings['column'] ) {
			if( 'yes' === $settings['make_featured_post'] ){
				$per_page = 1;
			}else{
				$per_page = 4;
				$list_column = 'ha-spl-list-col-1';
			}
		}

		if( 'col-2' === $settings['column'] ) {
			if( 'yes' === $settings['make_featured_post'] && 'featured-col-1' === $settings['featured_post_column'] ){
				$per_page = 5;
				$list_column = 'ha-spl-list-col-1';
			}elseif( 'yes' === $settings['make_featured_post'] && 'featured-col-2' === $settings['featured_post_column'] ){
				$per_page = 1;
			}else{
				$per_page = 8;
				$list_column = 'ha-spl-list-col-2';
			}
		}

		if( 'col-3' === $settings['column'] ) {
			if( 'yes' === $settings['make_featured_post'] && 'featured-col-1' === $settings['featured_post_column'] ){
				$per_page = 9;
				$list_column = 'ha-spl-list-col-2';
			}elseif( 'yes' === $settings['make_featured_post'] && 'featured-col-2' === $settings['featured_post_column'] ){
				$per_page = 5;
				$list_column = 'ha-spl-list-col-1';
			}else{
				$per_page = 12;
				$list_column = 'ha-spl-list-col-3';
			}
		}

		if( $item === 'list_column' ){
			return $list_column;
		}else{
			return $per_page;
		}

	}

	/**
	 * render header markup
	 */
	public function render_header_markup ($header = 'yes') {
		if ( 'yes' != $header ) {
			return;
		}
		$settings = $this->get_settings_for_display();

		$post_taxonomies = get_object_taxonomies(  $settings['posts_post_type'] );
		if( !empty( $settings['filter_terms_ids'] ) ){
			$categories = get_terms( [
				'taxonomy' => $post_taxonomies,
				'term_taxonomy_id' => $settings['filter_terms_ids'],
				// 'include' => $settings['filter_terms_ids'],
				'hide_empty' => false,
				// 'orderby' => 'include',
			] );
		}else{
			$categories = get_terms( [
				'taxonomy' => $post_taxonomies,
				'hide_empty' => false,
			] );
		}

		// echo '<pre>';
		// var_dump( $post_taxonomies);
		// echo '</pre>';

		?>
			<!-- header -->
			<div class="ha-spl-header">
				<?php
				if (  $settings['widget_title'] ) {
					printf( '<%1$s %2$s>%3$s</%1$s>',
						ha_escape_tags( $settings['widget_title_tag'] ),
						'class="ha-spl-widget-title"',
						esc_html( $settings['widget_title'] )
					);
				}
				?>
				<?php if ( 'yes' === $settings['category_filter'] ): ?>
					<div class="ha-spl-filter <?php echo esc_attr($settings['category_filter_style']);?>">
						<?php if ( 'inline' === $settings['category_filter_style'] ): ?>
						<ul class="ha-spl-filter-list">
							<?php if( $settings['filter_all_text'] ):?>
								<li><span class="ha-active" data-value="<?php echo esc_html( $settings['filter_all_text'] );?>"><?php echo esc_html( $settings['filter_all_text'] );?></span></li>
							<?php endif; ?>
							<?php foreach ( $categories as $category ): ?>
								<li><span data-value="<?php echo esc_attr($category->term_taxonomy_id);?>"><?php echo esc_html( $category->name );?></span></li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>
						<select class="ha-spl-custom-select">
							<?php if( $settings['filter_all_text'] ):?>
								<option data-display="<?php echo esc_html( $settings['filter_all_text'] );?>"><?php echo esc_html( $settings['filter_all_text'] );?></option>
							<?php endif; ?>
							<?php foreach ( $categories as $category ): ?>
								<option value="<?php echo esc_attr($category->term_taxonomy_id);?>"><?php echo esc_html( $category->name );?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endif; ?>
				<?php if ( 'yes' === $settings['navigation_show'] ): ?>
					<div class="ha-spl-pagination">
						<button class="prev"><i class="fa fa-angle-left"></i></button>
						<button class="next"><i class="fa fa-angle-right"></i></button>
					</div>
				<?php endif; ?>
			</div>
			<!-- /header -->
		<?php
	}

	protected function render () {

		$settings = $this->get_settings_for_display();
		if ( ! $settings['posts_post_type'] ) {
			return;
		}

		$column = $this->get_column_cls( $settings['column'] );
		$per_page = $this->get_item_per_page('per_page');
		$list_column = $this->get_item_per_page('list_column');
		$featured_post_column = $this->get_featured_column_cls( $settings['featured_post_column'] );

		$this->add_render_attribute( 'wrapper', 'class', [ 'ha-spl-wrapper' ] );

		$this->add_render_attribute(
			'grid_wrap',
			[
				'class' => [
					'ha-spl-grid-area',
					esc_attr( $column ),
					'yes' === $settings['make_featured_post'] ? 'ha-spl-featured-post-on' : 'ha-spl-featured-post-off'
				],
			]
		);

		/* if( 'yes' === $settings['make_featured_post']){
			$this->add_render_attribute(
				'featured',
				[
					'class' => [
						'ha-spl-column',
						'ha-spl-featured-post-wrap',
						esc_attr( $featured_post_column ),
					],
				]
			);

			$this->add_render_attribute(
				'featured_inner',
				[
					'class' => [
						'ha-spl-featured-post',
						'ha-spl-featured-'.esc_attr($settings['featured_post_style']),
					],
				]
			);
		} */

		$thumb_link = 'http://localhost/happy-test/wp-content/uploads/2011/07/img_0513-1-2.jpg';

		$args = $this->get_query_args();
		// echo '<pre>';
		// var_dump($args);
		// echo '</pre>';

		// $args['posts_per_page'] = $per_page;
		$args['posts_per_page'] = -1;

		//define ha smart post list custom query filter hook
		if ( !empty( $settings['query_id'] ) ) {
			$args = apply_filters( "happyaddons/smart-post-list/{$settings['query_id']}", $args );
		}

		$posts = get_posts( $args );


		if( 'yes' === $settings['top_bar_show'] ){
			$query_settings = [
				'args' => $args,
				'posts_post_type' => $settings['posts_post_type'],
				'per_page' => $per_page,
				'column' => $settings['column'],
				'make_featured_post' => $settings['make_featured_post'],
				'featured_post_column' => $featured_post_column,
				'featured_post_style' => $settings['featured_post_style'],
				'featured_image_size' => $settings['featured_image_size'],
				// 'featured_post_cat' => $settings['featured_post_cat'],
				'show_badge' => $settings['show_badge'],
				'taxonomy_badge' => $settings['taxonomy_badge'],
				'featured_post_title' => $settings['featured_post_title'],
				'featured_post_title_tag' => $settings['featured_post_title_tag'],
				'featured_meta_active' => $settings['featured_meta_active'],

				'featured_post_author_icon' => $settings['featured_post_author_icon'],
				'featured_post_date_icon' => $settings['featured_post_date_icon'],
				'featured_post_comment_icon' => $settings['featured_post_comment_icon'],
				'featured_excerpt_length' => $settings['featured_excerpt_length'],

				'list_column' => $list_column,
				'list_post_image' => $settings['list_post_image'],
				'list_post_image_size' => $settings['list_post_image_size'],
				'list_post_title_tag' => $settings['list_post_title_tag'],
				'list_meta_active' => $settings['list_meta_active'],

				'list_post_author_icon' => $settings['list_post_author_icon'],
				'list_post_date_icon' => $settings['list_post_date_icon'],
				'list_post_comment_icon' => $settings['list_post_comment_icon'],
			];
			$query_settings = json_encode( $query_settings, true );

			$this->add_render_attribute( 'wrapper', 'data-settings', $query_settings );
			$this->add_render_attribute( 'wrapper', 'data-total-offset', '0' );
			$this->add_render_attribute( 'wrapper', 'data-offset', $per_page );
		}
		$class_array = [];
		if( 'yes' === $settings['make_featured_post']) {
			$class_array['featured'] = 'ha-spl-column ha-spl-featured-post-wrap '.esc_attr( $featured_post_column );
			$class_array['featured_inner'] = 'ha-spl-featured-post '.'ha-spl-featured-'.esc_attr($settings['featured_post_style']);
		}

		// echo '<pre>';
		// var_dump( count( $posts ));
		// echo '</pre>';
		$loop = 1;
		if ( count( $posts ) !== 0 ) :?>
			<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>

				<?php $this->render_header_markup( $settings['top_bar_show'] );?>

				<div <?php $this->print_render_attribute_string( 'grid_wrap' ); ?>>

					<?php self::render_spl_markup( $settings, $posts, $class_array, $list_column, $per_page ); ?>

				</div>
			</div>
		<?php
		else:
			printf( '%1$s %2$s %3$s',
				__( 'No ', 'happy-addons-pro' ),
				esc_html( $settings['posts_post_type'] ),
				__( 'Found', 'happy-addons-pro' )
			);
		endif;
	}

}

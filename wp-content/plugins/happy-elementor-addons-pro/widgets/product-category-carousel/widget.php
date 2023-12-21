<?php
/**
 * Product Category Carousel widget class
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;

defined( 'ABSPATH' ) || die();

class Product_Category_Carousel extends Base {

    /**
	 * By setting this to false we can remove the "Default" option from
	 * skin dropdown. And the "Default" option indicates the widget itself.
	 *
	 * @var bool
	 */
    protected $_has_template_content = false;

    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __( 'Product Category Carousel', 'happy-addons-pro' );
    }

    public function show_in_panel() {
		return false;
	}

    /**
     * Get widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'hm hm-Category-Carousel';
    }

    public function get_keywords() {
        return [ 'ecommerce', 'woocommerce', 'product', 'categroy', 'carousel', 'sale' ];
	}

	/**
	* Register & Inculde Skins
	*/
	protected function register_skins() {
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/product-category-carousel/skins/skin-base.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/product-category-carousel/skins/classic.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/product-category-carousel/skins/minimal.php' );


		$this->add_skin( new \Happy_Addons_Pro\Widget\Skins\Product_Category_Carousel\Classic( $this ) );
		$this->add_skin( new \Happy_Addons_Pro\Widget\Skins\Product_Category_Carousel\Minimal( $this ) );
	}

	/**
	 * Get parent category list
	 */
	protected function get_parent_cats() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		$parent_categories = [ 'none' => __( 'None', 'happy-addons-pro' ) ];

		$args = array(
			'parent' => 0
		);
		$parent_cats = get_terms( 'product_cat', $args );

		foreach ( $parent_cats as $parent_cat ) {
			$parent_categories[$parent_cat->term_id] = $parent_cat->name;
		}
		return $parent_categories;
	}

	/**
	 * Get all category list
	 */
	protected function get_all_cats_list() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		$cats_list = [];

		$args = [
			'orderby'    => 'name',
			'order'      => 'DESC',
		];
		$cats = get_terms( 'product_cat', $args );

        if ($cats) {
            foreach ( $cats as $cat ) {
                $cats_list[$cat->term_id] = $cat->name;
            }
        }

		return $cats_list;
	}

	/**
	 * Register content controls
	 */
    protected function register_content_controls() {

		//Layout content controls
		$this->start_controls_section(
			'_section_layout',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->end_controls_section();

		//Query content controls
		$this->query_content_tab_controls();

		//Carousel Settings controls
		$this->carousel_settings_content_tab_controls();

	}

	/**
	 * Query content controls
	 */
	protected function query_content_tab_controls() {

		$this->start_controls_section(
            '_term_query',
            [
                'label' => __( 'Query', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
		);

		$this->add_control(
            'query_type',
            [
                'label' => __( 'Type', 'happy-addons-pro' ),
                'type' => Controls_Manager::SELECT,
				'default' => 'all',
                'options' => [
                    'all' => __( 'All', 'happy-addons-pro' ),
                    'parents' => __( 'Only Parents', 'happy-addons-pro' ),
                    'child' => __( 'Only Child', 'happy-addons-pro' )
                ],
            ]
		);

		$this->start_controls_tabs( '_tabs_terms_include_exclude',
			[
				'condition' => [ 'query_type' => 'all' ]
			]
		);
		$this->start_controls_tab(
            '_tab_term_include',
            [
				'label' => __( 'Include', 'happy-addons-pro' ),
				'condition' => [ 'query_type' => 'all' ]
            ]
		);

		$this->add_control(
			'cats_include_by_id',
			[
				'label' => __( 'Categories', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => true,
				'condition' => [
					'query_type' => 'all'
				],
				'options' => $this->get_all_cats_list(),
			]
		);

		$this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_term_exclude',
            [
				'label' => __( 'Exclude', 'happy-addons-pro' ),
				'condition' => [ 'query_type' => 'all' ]
            ]
		);

		$this->add_control(
			'cats_exclude_by_id',
			[
				'label' => __( 'Categories', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => true,
				'condition' => [
					'query_type' => 'all'
				],
				'options' => $this->get_all_cats_list(),
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
            'parent_cats',
            [
                'label' => __( 'Child Categories of', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => $this->get_parent_cats(),
				'condition' => [
					'query_type' => 'child'
				]
            ]
        );

		$this->add_control(
            'order_by',
            [
                'label' => __( 'Order By', 'happy-addons-pro' ),
                'type' => Controls_Manager::SELECT,
				'default' => 'name',
                'options' => [
                    'name' => __( 'Name', 'happy-addons-pro' ),
                    'count' => __( 'Count', 'happy-addons-pro' ),
                    'slug' => __( 'Slug', 'happy-addons-pro' )
                ],
            ]
		);

		$this->add_control(
            'order',
            [
                'label' => __( 'Order', 'happy-addons-pro' ),
                'type' => Controls_Manager::SELECT,
				'default' => 'desc',
                'options' => [
                    'desc' => __( 'Descending', 'happy-addons-pro' ),
                    'asc' => __( 'Ascending', 'happy-addons-pro' ),
                ],
            ]
		);

		$this->add_control(
			'show_empty_cat',
			[
				'label' => __( 'Show Empty Categories', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
            'cat_per_page',
            [
                'label' => __( 'Number of Categories', 'happy-addons-pro' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'step' => 1,
                'max' => 1000,
            ]
		);

		$this->end_controls_section();

	}

	/**
	 * Carousel Settings controls
	 */
	protected function carousel_settings_content_tab_controls() {

		$this->start_controls_section(
            '_section_settings',
            [
                'label' => __( 'Carousel Settings', 'happy-addons-pro' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'animation_speed',
            [
                'label' => __( 'Animation Speed', 'happy-addons-pro' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 10,
                'max' => 10000,
                'default' => 800,
                'description' => __( 'Slide speed in milliseconds', 'happy-addons-pro' ),
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __( 'Autoplay?', 'happy-addons-pro' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'happy-addons-pro' ),
                'label_off' => __( 'No', 'happy-addons-pro' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => __( 'Autoplay Speed', 'happy-addons-pro' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 100,
                'step' => 100,
                'max' => 10000,
                'default' => 2000,
                'description' => __( 'Autoplay speed in milliseconds', 'happy-addons-pro' ),
                'condition' => [
                    'autoplay' => 'yes'
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'loop',
            [
                'label' => __( 'Infinite Loop?', 'happy-addons-pro' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'happy-addons-pro' ),
                'label_off' => __( 'No', 'happy-addons-pro' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'navigation',
            [
                'label' => __( 'Navigation', 'happy-addons-pro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'none' => __( 'None', 'happy-addons-pro' ),
                    'arrow' => __( 'Arrow', 'happy-addons-pro' ),
                    'dots' => __( 'Dots', 'happy-addons-pro' ),
                    'both' => __( 'Arrow & Dots', 'happy-addons-pro' ),
                ],
                'default' => 'arrow',
                'frontend_available' => true,
                'style_transfer' => true,
            ]
        );

        $this->add_responsive_control(
            'slides_to_show',
            [
                'label' => __( 'Slides To Show', 'happy-addons-pro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    1 => __( '1 Slide', 'happy-addons-pro' ),
                    2 => __( '2 Slides', 'happy-addons-pro' ),
                    3 => __( '3 Slides', 'happy-addons-pro' ),
                    4 => __( '4 Slides', 'happy-addons-pro' ),
                    5 => __( '5 Slides', 'happy-addons-pro' ),
                    6 => __( '6 Slides', 'happy-addons-pro' ),
                ],
                'desktop_default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'frontend_available' => true,
                'style_transfer' => true,
            ]
        );

        $this->end_controls_section();

	}

	/**
	 * Register style controls
	 */
    protected function register_style_controls() {

		//Nav Arrow Style Start
		$this->nav_arrow_style_tab_controls();

		//Nav Dot Style Start
		$this->nav_dot_style_tab_controls();
	}

	/**
	 * Nav Arrow style controls
	 */
	protected function nav_arrow_style_tab_controls() {

		$this->start_controls_section(
            '_section_style_arrow',
            [
                'label' => __( 'Navigation - Arrow', 'happy-addons-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'arrow_position_toggle',
            [
                'label' => __( 'Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'label_off' => __( 'None', 'happy-addons-pro' ),
                'label_on' => __( 'Custom', 'happy-addons-pro' ),
                'return_value' => 'yes',
            ]
        );

		$this->start_popover();

		$this->add_control(
			'arrow_sync_position',
			[
				'label' => __( 'Sync Position', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'yes' => [
						'title' => __( 'Yes', 'happy-addons-pro' ),
						'icon' => 'eicon-sync',
					],
					'no' => [
						'title' => __( 'No', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-stretch',
					]
				],
				'condition' => [
					'arrow_position_toggle' => 'yes'
				],
				'default' => 'no',
				'toggle' => false,
				'prefix_class' => 'ha-arrow-sync-'
			]
		);

		$this->add_control(
			'sync_position_alignment',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
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
					]
				],
				'condition' => [
					'arrow_position_toggle' => 'yes',
					'arrow_sync_position' => 'yes'
				],
				'default' => 'center',
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'left: 0',
					'center' => 'left: 50%',
					'right' => 'left: 100%',
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => '{{VALUE}}'
				]
			]
		);

		$this->add_responsive_control(
			'arrow_position_y',
			[
				'label' => __( 'Vertical', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition' => [
					'arrow_position_toggle' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'top: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'arrow_position_x',
			[
				'label' => __( 'Horizontal', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition' => [
					'arrow_position_toggle' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.ha-arrow-sync-no .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-arrow-sync-no .slick-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ha-arrow-sync-yes .slick-next, {{WRAPPER}}.ha-arrow-sync-yes .slick-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_spacing',
			[
				'label' => __( 'Space between Arrows', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition' => [
					'arrow_position_toggle' => 'yes',
					'arrow_sync_position' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
                ],
                'default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors' => [
					'{{WRAPPER}}.ha-arrow-sync-yes .slick-next' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_popover();

		$this->add_responsive_control(
			'arrow_size',
			[
				'label' => __( 'Box Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 70,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-next' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_font_size',
			[
				'label' => __( 'Icon Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-prev' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-next' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'arrow_border',
                'selector' => '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next',
            ]
        );

        $this->add_responsive_control(
            'arrow_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->start_controls_tabs( '_tabs_arrow' );

        $this->start_controls_tab(
            '_tab_arrow_normal',
            [
                'label' => __( 'Normal', 'happy-addons-pro' ),
            ]
        );

        $this->add_control(
            'arrow_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_bg_color',
            [
                'label' => __( 'Background Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-prev, {{WRAPPER}} .slick-next' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_arrow_hover',
            [
                'label' => __( 'Hover', 'happy-addons-pro' ),
            ]
        );

        $this->add_control(
            'arrow_hover_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_hover_bg_color',
            [
                'label' => __( 'Background Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_hover_border_color',
            [
                'label' => __( 'Border Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'arrow_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-prev:hover, {{WRAPPER}} .slick-next:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
	}

	/**
	 * Nav Dot style controls
	 */
	protected function nav_dot_style_tab_controls() {

		$this->start_controls_section(
            '_section_style_dots',
            [
                'label' => __( 'Navigation - Dots', 'happy-addons-pro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'dots_nav_position_y',
            [
                'label' => __( 'Vertical Position', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_nav_spacing',
            [
                'label' => __( 'Space Between', 'happy-addons-pro' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2); margin-left: calc({{SIZE}}{{UNIT}} / 2);',
                ],
            ]
        );

        $this->add_responsive_control(
            'dots_nav_align',
            [
                'label' => __( 'Alignment', 'happy-addons-pro' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'happy-addons-pro' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'happy-addons-pro' ),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'happy-addons-pro' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .slick-dots' => 'text-align: {{VALUE}}'
                ]
            ]
        );

        $this->start_controls_tabs( '_tabs_dots' );
        $this->start_controls_tab(
            '_tab_dots_normal',
            [
                'label' => __( 'Normal', 'happy-addons-pro' ),
            ]
        );

        $this->add_control(
            'dots_nav_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_dots_hover',
            [
                'label' => __( 'Hover', 'happy-addons-pro' ),
            ]
        );

        $this->add_control(
            'dots_nav_hover_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:hover:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_dots_active',
            [
                'label' => __( 'Active', 'happy-addons-pro' ),
            ]
        );

        $this->add_control(
            'dots_nav_active_color',
            [
                'label' => __( 'Color', 'happy-addons-pro' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-dots .slick-active button:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
	}

	/**
	 * Get query
	 *
	 * @return void
	 */
    public function get_query() {
		$settings = $this->get_settings_for_display();

		$args = array(
			'orderby'    => ( $settings['order_by'] ) ? $settings['order_by'] : 'name',
			'order'      => ( $settings['order'] ) ? $settings['order'] : 'ASC',
			'hide_empty' => $settings['show_empty_cat'] == 'yes' ? false : true,
		);

		if ( $settings['query_type'] == 'all' ) {
			! empty( $settings['cats_include_by_id'] ) ? $args['include'] = $settings['cats_include_by_id'] : null;
			! empty( $settings['cats_exclude_by_id'] ) ? $args['exclude'] = $settings['cats_exclude_by_id'] : null;
		} elseif ( $settings['query_type'] == 'parents' ) {
			$args['parent'] = 0;
		} elseif ( $settings['query_type'] == 'child' ) {
			if ( $settings['parent_cats'] != 'none' &&  ! empty( $settings['parent_cats'] ) ) {
				$args['child_of'] = $settings['parent_cats'];
			} elseif ( $settings['parent_cats'] == 'none' ) {
				if ( is_admin() ) {
					return printf( '<div class="ha-category-carousel-error">%s</div>', __( 'Select Parent Category from <strong>Query > Child Categories of</strong>.', 'happy-addons-pro' ) );
				}
			}
		}

		$product_cats = get_terms( 'product_cat', $args );
		if ( !empty( $settings['cat_per_page'] ) && count( $product_cats ) > $settings['cat_per_page'] ) {
			$product_cats = array_splice( $product_cats, 0, $settings['cat_per_page'] );
		}

		return $product_cats;
	}

}

<?php
/**
 * Product Category Grid widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;


defined( 'ABSPATH' ) || die();

class Product_Category_Grid extends Base {

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
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Product Category Grid', 'happy-addons-pro' );
	}

    public function show_in_panel() {
		return false;
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
		return 'hm hm-Category-Carousel';
	}

	public function get_keywords() {
		return [ 'product-category-rid', 'ecommerce', 'woocommerce', 'product', 'categroy', 'grid', 'sale' ];
	}

 	/**
	 * Register & Inculde Post Grid Skins
	 */
	protected function register_skins() {
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/product-category-grid/skins/skin-base.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/product-category-grid/skins/classic.php' );
        include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/product-category-grid/skins/minimal.php' );

		$this->add_skin( new \Happy_Addons_Pro\Widget\Skins\Product_Category_Grid\Classic( $this ) );
        $this->add_skin( new \Happy_Addons_Pro\Widget\Skins\Product_Category_Grid\Minimal( $this ) );
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

		if($cats){
			foreach ( $cats as $cat ) {
				$cats_list[$cat->term_id] = $cat->name;
			}
		}
		return $cats_list;
	}

	/**
	 * Register content related controls
	 */
	protected function register_content_controls() {

		//Layout
		$this->start_controls_section(
			'_section_layout',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
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
					'{{WRAPPER}} .ha-product-cat-grid-wrapper' => 'grid-template-columns: repeat( {{VALUE}}, 1fr );',
				],
			]
		);

		$this->end_controls_section();

		//Query content
		$this->query_content_tab_controls();

		//Load More content
		$this->load_more_content_tab_controls();

    }

	/**
	 * Query content controls
	 */
	protected function query_content_tab_controls( ) {

		$this->start_controls_section(
            '_section_term_query',
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

        $this->end_controls_section();

	}

	/**
	 * Load More content controls
	 */
	protected function load_more_content_tab_controls( ) {

		$this->start_controls_section(
			'_section_content_more',
			[
				'label' => __( 'More...', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_load_more',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Show Load More Button', 'happy-addons-pro' ),
				'default' => '',
				'return_value' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'load_more_text',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __( 'Button Text', 'happy-addons-pro' ),
				'default' => __( 'More category', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'show_load_more' => 'yes',
				]
			]
		);

		$this->add_control(
			'load_more_link',
			[
				'type' => Controls_Manager::URL,
				'label' => __( 'Button URL', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '#',
					'is_external' => true,
					'nofollow' => true,
				],
				'condition' => [
					'show_load_more' => 'yes',
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

		//Load More Style Start
		$this->load_more_style_tab_controls();

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
					'{{WRAPPER}} .ha-product-cat-grid-wrapper' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .ha-product-cat-grid-wrapper' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
    }

	/**
	 * Load More Style controls
	 */
	protected function load_more_style_tab_controls( ) {

		$this->start_controls_section(
			'_section_style_load_more_button',
			[
				'label' => __( 'Load More Button', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_load_more' => 'yes',
				],
			]
		);

		$this->add_control(
			'load_more_btn_align',
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
					'{{WRAPPER}} .ha-product-cat-grid-load-more' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_btn_margin_top',
			[
				'label' => __( 'Margin Top', 'happy-addons-pro' ),
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
					'{{WRAPPER}} .ha-product-cat-grid-load-more' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_btn_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-grid-load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'load_more_btn_typography',
				'selector' => '{{WRAPPER}} .ha-product-cat-grid-load-more-btn',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'load_more_btn_border',
				'selector' => '{{WRAPPER}} .ha-product-cat-grid-load-more-btn',
			]
		);

		$this->add_control(
			'load_more_btn_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-grid-load-more-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( '_tabs_load_more_btn_stat' );

		$this->start_controls_tab(
			'_tab_load_more_btn_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'load_more_btn_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-grid-load-more-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_btn_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-grid-load-more-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'load_more_btn_box_shadow',
				'selector' => '{{WRAPPER}} .ha-product-cat-grid-load-more-btn',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_load_more_btn_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'load_more_btn_hover_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-grid-load-more-btn:hover, {{WRAPPER}} .ha-product-cat-grid-load-more-btn:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_btn_hover_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-grid-load-more-btn:hover, {{WRAPPER}} .ha-product-cat-grid-load-more-btn:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_btn_hover_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'btn_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-grid-load-more-btn:hover, {{WRAPPER}} .ha-product-cat-grid-load-more-btn:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'load_more_btn_hove_box_shadow',
				'selector' => '{{WRAPPER}} .ha-product-cat-grid-load-more-btn:hover, {{WRAPPER}} .ha-product-cat-grid-load-more-btn:focus',
			]
		);


		$this->add_control(
			'load_more_btn_hove_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-product-cat-grid-load-more-btn:hover' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .ha-product-cat-grid-load-more-btn:focus' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get Load More button markup
	 */
	public function get_load_more_button() {
		$settings = $this->get_settings_for_display();
		if ( $settings['show_load_more'] !== 'yes' ) {
			return;
		}
		$this->add_link_attributes( 'load_more', $settings['load_more_link'] );
		$this->add_render_attribute( 'load_more', 'class', 'ha-product-cat-grid-load-more-btn' );
		?>
		<div class="ha-product-cat-grid-load-more">
			<a <?php $this->print_render_attribute_string( 'load_more' ); ?>><?php echo esc_html( $settings['load_more_text'] ); ?></a>
		</div>
		<?php
	}

	/**
	 * Get query
	 *
	 * @param [type] $cat_per_page
	 * @return void
	 */
	public function get_query( $cat_per_page ) {
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

		if ( !empty( $cat_per_page ) && count( $product_cats ) > $cat_per_page ) {
			$product_cats = array_splice( $product_cats, 0, $cat_per_page );
		}

		return $product_cats;
	}

	/**
	 * render content
	 *
	 * @return void
	 */
	protected function render() {}

}

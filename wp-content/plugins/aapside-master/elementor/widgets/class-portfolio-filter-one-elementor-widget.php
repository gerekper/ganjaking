<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Portfolio_Filter_One extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve Elementor widget name.
     *
     * @return string Widget name.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_name() {
        return 'appside-portfolio-filter-one-widget';
    }

    /**
     * Get widget title.
     *
     * Retrieve Elementor widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_title() {
        return esc_html__( 'Portfolio Filter: 01', 'aapside-master' );
    }

    /**
     * Get widget icon.
     *
     * Retrieve Elementor widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_icon() {
        return 'eicon-posts-masonry';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the Elementor widget belongs to.
     *
     * @return array Widget categories.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_categories() {
        return [ 'appside_widgets' ];
    }

    /**
     * Register Elementor widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls() {

        $this->start_controls_section(
            'settings_section',
            [
                'label' => esc_html__( 'General Settings', 'aapside-master' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control('column',[
            'label' => esc_html__('Column','aapside-master'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '2' => esc_html__('02 Column','aapside-master'),
                '3' => esc_html__('03 Column','aapside-master'),
                '4' => esc_html__('04 Column','aapside-master')
            ],
            'default' => '4'
        ]);
	    $this->add_control( 'total', [
		    'label'       => esc_html__( 'Total Posts', 'aapside-master' ),
		    'type'        => Controls_Manager::TEXT,
		    'default'     => '-1',
		    'description' => esc_html__( 'enter how many post you want in masonry , enter -1 for unlimited post.' )
	    ] );
	    $this->add_control( 'category', [
		    'label'       => esc_html__( 'Category', 'aapside-master' ),
		    'type'        => Controls_Manager::SELECT2,
		    'label_block' => true,
		    'multiple'    => true,
		    'options'     => appside_master()->get_terms_names( 'portfolio-cat', 'id' ),
		    'description' => esc_html__( 'select category as you want, leave it blank for all category', 'aapside-master' ),
	    ] );
	    $this->add_control( 'order', [
		    'label'       => esc_html__( 'Order', 'aapside-master' ),
		    'type'        => Controls_Manager::SELECT,
		    'options'     => array(
			    'ASC'  => esc_html__( 'Ascending', 'aapside-master' ),
			    'DESC' => esc_html__( 'Descending', 'aapside-master' ),
		    ),
		    'default'     => 'ASC',
		    'description' => esc_html__( 'select order', 'aapside-master' )
	    ] );
	    $this->add_control( 'orderby', [
		    'label'       => esc_html__( 'OrderBy', 'aapside-master' ),
		    'type'        => Controls_Manager::SELECT,
		    'options'     => array(
			    'ID'            => esc_html__( 'ID', 'aapside-master' ),
			    'title'         => esc_html__( 'Title', 'aapside-master' ),
			    'date'          => esc_html__( 'Date', 'aapside-master' ),
			    'rand'          => esc_html__( 'Random', 'aapside-master' ),
			    'comment_count' => esc_html__( 'Most Comments', 'aapside-master' ),
		    ),
		    'default'     => 'ID',
		    'description' => esc_html__( 'select order', 'aapside-master' )
	    ] );
	    $this->add_control( 'pagination', [
		    'label'       => esc_html__( 'Pagination', 'aapside-master' ),
		    'type'        => Controls_Manager::SWITCHER,
		    'label_on' => esc_html__('Show','aapside-master'),
		    'label_off' => esc_html__('Hide','aapside-master'),
		    'default'     => 'yes',
		    'description' => esc_html__( 'show/hide pagination', 'aapside-master' )
	    ] );
	    $this->add_control('content_divider_01',[
	       'type' => Controls_Manager::DIVIDER
        ]);
	    $this->add_control( 'all_text', [
		    'label'       => esc_html__( 'All Text', 'aapside-master' ),
		    'type'        => Controls_Manager::TEXT,
		    'default'     => esc_html__('all','aapside-master'),
	    ] );
	    $this->add_control(
		    'menu_text_align',
		    [
			    'label' => esc_html__( 'Menu Alignment', 'aapside-master' ),
			    'type' => Controls_Manager::CHOOSE,
			    'options' => [
				    'left' => [
					    'title' => esc_html__( 'Left', 'aapside-master' ),
					    'icon' => 'fa fa-align-left',
				    ],
				    'center' => [
					    'title' => esc_html__( 'Center', 'aapside-master' ),
					    'icon' => 'fa fa-align-center',
				    ],
				    'right' => [
					    'title' => esc_html__( 'Right', 'aapside-master' ),
					    'icon' => 'fa fa-align-right',
				    ],
			    ],
			    'default' => 'center',
			    'toggle' => true,
                "selectors" => [
                        "{{WRAPPER}} .portfolio-filter-nav" => "text-align: {{VALUE}};"
                ]
		    ]
	    );

        $this->end_controls_section();
	    $this->start_controls_section(
		    'menu_settings_section',
		    [
			    'label' => esc_html__( 'Menu Styling', 'aapside-master' ),
			    'tab'   => Controls_Manager::TAB_STYLE,
		    ]
	    );
	    $this->add_control(
		    'menu_bottom_gap',
		    [
			    'label' => esc_html__( 'Menu Bottom Space', 'aapside-master' ),
			    'type' => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%' ],
			    'range' => [
				    'px' => [
					    'min' => 0,
					    'max' => 1000,
					    'step' => 5,
				    ],
				    '%' => [
					    'min' => 0,
					    'max' => 100,
				    ],
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .portfolio-filter-nav' => 'margin-bottom: {{SIZE}}{{UNIT}};',
			    ],
		    ]
	    );
	    $this->add_control(
		    'menu_between_gap',
		    [
			    'label' => esc_html__( 'Menu Item Between Space', 'aapside-master' ),
			    'type' => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%' ],
			    'range' => [
				    'px' => [
					    'min' => 0,
					    'max' => 1000,
					    'step' => 5,
				    ],
				    '%' => [
					    'min' => 0,
					    'max' => 100,
				    ],
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .portfolio-filter-nav ul li + li' => 'margin-left: {{SIZE}}{{UNIT}};',
			    ],
		    ]
	    );

	    $this->add_control( 'menu_color', [
		    'label'     => esc_html__( 'Color', 'aapside-master' ),
		    'type'      => Controls_Manager::COLOR,
		    'selectors' => [
			    '{{WRAPPER}} .portfolio-filter-nav ul li' => "color:{{VALUE}}"
		    ]
	    ] );

	    $this->add_control( 'menu_active_color', [
		    'label'     => esc_html__( 'Active Color', 'aapside-master' ),
		    'type'      => Controls_Manager::COLOR,
		    'selectors' => [
			    '{{WRAPPER}} .portfolio-filter-nav ul li.active' => "color:{{VALUE}}"
		    ]
	    ] );

        $this->end_controls_section();

	    $this->start_controls_section(
		    'thumbnail_settings_section',
		    [
			    'label' => esc_html__( 'Thumbnail Styling', 'aapside-master' ),
			    'tab'   => Controls_Manager::TAB_STYLE,
		    ]
	    );
	    $this->add_control( 'thumb_border_radius', [
		    'label'      => esc_html__( 'Border Radius', 'aapside-master' ),
		    'type'       => Controls_Manager::DIMENSIONS,
		    'size_units' => [ 'px', '%', 'em' ],
		    'selectors'  => [
			    "{{WRAPPER}} .single-portfolio-style-01 .thumbnail img" => "border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};"
		    ]
	    ] );
	    $this->add_control(
		    'thumbnail_bottom_gap',
		    [
			    'label' => esc_html__( 'Thumbnail Bottom Gap', 'aapside-master' ),
			    'type' => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%' ],
			    'range' => [
				    'px' => [
					    'min' => 0,
					    'max' => 100,
					    'step' => 1,
				    ],
				    '%' => [
					    'min' => 0,
					    'max' => 100,
				    ],
			    ],
			    'selectors' => [
				    '{{WRAPPER}} .single-portfolio-style-01 .thumbnail' => 'margin-bottom: {{SIZE}}{{UNIT}};',
			    ],
		    ]
	    );
	    $this->end_controls_section();


	    /*  title styling tabs start */
	    $this->start_controls_section(
		    'title_settings_section',
		    [
			    'label' => esc_html__( 'Title Styling', 'aapside-master' ),
			    'tab'   => Controls_Manager::TAB_STYLE,
		    ]
	    );
	    $this->start_controls_tabs(
		    'style_tabs'
	    );

	    $this->start_controls_tab(
		    'style_normal_tab',
		    [
			    'label' => __( 'Normal', 'aapside-master' ),
		    ]
	    );

	    $this->add_control( 'title_color', [
		    'label'     => esc_html__( 'Color', 'aapside-master' ),
		    'type'      => Controls_Manager::COLOR,
		    'selectors' => [
			    '{{WRAPPER}} .single-portfolio-style-01 .content .title' => "color:{{VALUE}}"
		    ]
	    ] );
	    $this->end_controls_tab();

	    $this->start_controls_tab(
		    'style_hover_tab',
		    [
			    'label' => __( 'Hover', 'aapside-master' ),
		    ]
	    );
	    $this->add_control( 'title_hover_color', [
		    'label'     => esc_html__( 'Color', 'aapside-master' ),
		    'type'      => Controls_Manager::COLOR,
		    'selectors' => [
			    '{{WRAPPER}} .single-portfolio-style-01 .content .title:hover' => "color:{{VALUE}}"
		    ]
	    ] );

	    $this->end_controls_tab();

	    $this->end_controls_tabs();

	    $this->end_controls_section();

	    /*  title styling tabs end */

	    /*  readmore styling tabs start */
	    $this->start_controls_section(
		    'readmore_settings_section',
		    [
			    'label' => esc_html__( 'Category Styling', 'aapside-master' ),
			    'tab'   => Controls_Manager::TAB_STYLE,
		    ]
	    );

	    $this->start_controls_tabs(
		    'readmore_style_tabs'
	    );

	    $this->start_controls_tab(
		    'readmore_style_normal_tab',
		    [
			    'label' => __( 'Normal', 'aapside-master' ),
		    ]
	    );

	    $this->add_control( 'readmore_color', [
		    'label'     => esc_html__( 'Color', 'aapside-master' ),
		    'type'      => Controls_Manager::COLOR,
		    'selectors' => [
			    '{{WRAPPER}} .single-portfolio-style-01 .content .cats a' => "color:{{VALUE}}"
		    ]
	    ] );
	    $this->end_controls_tab();

	    $this->start_controls_tab(
		    'readmore_style_hover_tab',
		    [
			    'label' => __( 'Hover', 'aapside-master' ),
		    ]
	    );

	    $this->add_control( 'readmore_hover_color', [
		    'label'     => esc_html__( 'Color', 'aapside-master' ),
		    'type'      => Controls_Manager::COLOR,
		    'selectors' => [
			    '{{WRAPPER}} .single-portfolio-style-01 .content .cats a:hover' => "color:{{VALUE}}"
		    ]
	    ] );
	    $this->end_controls_tab();

	    $this->end_controls_tabs();

	    $this->end_controls_section();
	    /*  readmore styling tabs end */

	    /*  pagination styling tabs start */
	    $this->start_controls_section(
		    'pagination_settings_section',
		    [
			    'label' => esc_html__( 'Pagination Settings', 'aapside-master' ),
			    'tab'   => Controls_Manager::TAB_STYLE,
		    ]
	    );

	    $this->start_controls_tabs(
		    'pagination_style_tabs'
	    );

	    $this->start_controls_tab(
		    'pagination_style_normal_tab',
		    [
			    'label' => __( 'Normal', 'aapside-master' ),
		    ]
	    );

	    $this->add_control( 'pagination_color', [
		    'type'      => Controls_Manager::COLOR,
		    'label'     => esc_html__( 'Color', 'aapside-master' ),
		    'selectors' => [
			    "{{WRAPPER}} .portfolio-pagination` ul li a"   => "color: {{VALUE}}",
			    "{{WRAPPER}} .portfolio-pagination ul li span" => "color: {{VALUE}}",
		    ]
	    ] );
	    $this->add_control( 'pagination_border_color', [
		    'type'      => Controls_Manager::COLOR,
		    'label'     => esc_html__( 'Border Color', 'aapside-master' ),
		    'selectors' => [
			    "{{WRAPPER}} .portfolio-pagination ul li a"    => "border-color: {{VALUE}}",
			    "{{WRAPPER}} .portfolio-pagination ul li span" => "border-color: {{VALUE}}",
		    ]
	    ] );
	    $this->add_control( 'pagination_hr', [
		    'type' => Controls_Manager::DIVIDER
	    ] );
	    $this->add_group_control( Group_Control_Background::get_type(), [
		    'name'     => 'pagination_background',
		    'label'    => esc_html__( 'Background', 'aapside-master' ),
		    'selector' => "{{WRAPPER}} .portfolio-pagination ul li a, {{WRAPPER}} .portfolio-pagination ul li span"
	    ] );

	    $this->end_controls_tab();

	    $this->start_controls_tab(
		    'pagination_style_hover_tab',
		    [
			    'label' => __( 'Hover', 'aapside-master' ),
		    ]
	    );
	    $this->add_control( 'pagination_hover_color', [
		    'type'      => Controls_Manager::COLOR,
		    'label'     => esc_html__( 'Color', 'aapside-master' ),
		    'selectors' => [
			    "{{WRAPPER}} .portfolio-pagination ul li a:hover"      => "color: {{VALUE}}",
			    "{{WRAPPER}} .portfolio-pagination ul li span.current" => "color: {{VALUE}}",
		    ]
	    ] );
	    $this->add_control( 'pagination_hover_border_color', [
		    'type'      => Controls_Manager::COLOR,
		    'label'     => esc_html__( 'Border Color', 'aapside-master' ),
		    'selectors' => [
			    "{{WRAPPER}} .portfolio-pagination ul li a:hover"      => "border-color: {{VALUE}}",
			    "{{WRAPPER}} .portfolio-pagination ul li span.current" => "border-color: {{VALUE}}",
		    ]
	    ] );
	    $this->add_control( 'pagination_hover_hr', [
		    'type' => Controls_Manager::DIVIDER
	    ] );
	    $this->add_group_control( Group_Control_Background::get_type(), [
		    'name'     => 'pagination_hover_background',
		    'label'    => esc_html__( 'Background', 'aapside-master' ),
		    'selector' => "{{WRAPPER}} .portfolio-pagination ul li a:hover, {{WRAPPER}} .Portfolio-pagination ul li span.current"
	    ] );


	    $this->end_controls_tab();

	    $this->end_controls_tabs();

	    $this->end_controls_section();
	    /*  pagination styling tabs end */

	    /*  Typography tabs start */
	    $this->start_controls_section(
		    'typography_settings_section',
		    [
			    'label' => esc_html__( 'Typography Settings', 'aapside-master' ),
			    'tab'   => Controls_Manager::TAB_STYLE,
		    ]
	    );
	    $this->add_group_control( Group_Control_Typography::get_type(), [
		    'name'     => 'title_typography',
		    'label'    => esc_html__( 'Title Typography', 'aapside-master' ),
		    'selector' => "{{WRAPPER}} .single-portfolio-style-01 .content .title"
	    ] );
	    $this->add_group_control( Group_Control_Typography::get_type(), [
		    'name'     => 'post_meta_typography',
		    'label'    => esc_html__( 'Category Typography', 'aapside-master' ),
		    'selector' => "{{WRAPPER}} .single-portfolio-style-01 .content .cats"
	    ] );
	    $this->add_group_control( Group_Control_Typography::get_type(), [
		    'name'     => 'menu_typography',
		    'label'    => esc_html__( 'Menu Typography', 'aapside-master' ),
		    'selector' => "{{WRAPPER}} .portfolio-filter-nav ul li"
	    ] );
	    $this->end_controls_section();

	    /*  Typography tabs end */

    }

    /**
     * Render Elementor widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
	    //general options
        $settings = $this->get_settings_for_display();
	    $total_posts = $settings['total'];
	    $category    = $settings['category'];
	    $order       = $settings['order'];
	    $orderby     = $settings['orderby'];

	    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
	    $args = array(
		    'post_type' => 'portfolio',
		    'paged' => $paged,
		    'posts_per_page' => $total_posts,
		    'order' => $order,
		    'orderby' => $orderby,
		    'post_status' => 'publish',
		    'ignore_sticky_posts' => 1,
	    );

	    if (!empty($category)) {
		    $args['tax_query'] = array(
			    array(
				    'taxonomy' => 'portfolio-cat',
				    'field' => 'term_id',
				    'terms' => $category
			    )
		    );
	    }
	    $post_data = new \WP_Query($args);
	    $rand = rand(999, 99999999);
        ?>
        <div class="appside-case-study-wrapper-<?php echo esc_attr($rand);?>">
        <div class="portfolio-filter-nav appside-isotope-nav">
            <ul>
                <li class="active" data-filter="*"><?php echo esc_html($settings['all_text'])?></li>
                <?php
                if ( !empty($category) ){
	                foreach ($category as $cat_id){
		                $cat_details = get_term_by('id',$cat_id,'portfolio-cat');
		                printf(' <li data-filter=".%1$s">%2$s</li>',$cat_details->slug,$cat_details->name);
	                }
                }else{
	                $cat_details = get_terms(array('taxonomy' => 'portfolio-cat','hide_empty' => true));
	                foreach ($cat_details as $cat){
		                printf(' <li data-filter=".%1$s">%2$s</li>',$cat->slug,$cat->name);
	                }
                }
                ?>
            </ul>
        </div>
        <div class="portfolio-filter-area appside-isotope-init"
             id="appside-isotipe-init-<?php echo esc_attr($rand);?>"
        >
	    <?php
	    while ($post_data->have_posts()):$post_data->the_post();
		    $all_cat = wp_get_post_terms(get_the_ID(),'portfolio-cat');
		    $cat_markup ='';
		    $masonry_filters = '';
		    foreach ($all_cat as $cat ){
			    $masonry_filters .= ' '.$cat->slug;
			    $cat_markup .= ' <a href="'.get_term_link($cat->term_id,'portfolio-cat').'">'.$cat->name.'</a>';
		    }

		    ?>
            <div class="col-lg-<?php echo esc_attr($settings['column'])?> col-md-6 <?php echo esc_attr($masonry_filters);?> appside-masonry-item">
                <div class="single-portfolio-style-01">
		            <?php if (has_post_thumbnail()):?>
                        <div class="thumbnail">
				            <?php
				            the_post_thumbnail( 'appside_portfolio', array(
					            'alt' => the_title_attribute( array(
						            'echo' => false,
					            ) ),
				            ) );
				            ?>
                        </div>
		            <?php endif;?>
                    <div class="content">
                        <a href="<?php the_permalink();?>"> <h4 class="title"><?php the_title();?></h4></a>
                        <div class="cats">
				            <?php
				            $all_portfolio_cat = get_the_terms(get_the_ID(),'portfolio-cat');
				            foreach ( $all_portfolio_cat as $term ) {
					            printf( '<a href="%1$s">%2$s</a>', get_term_link( $term, 'portfolio-cat' ), esc_html( $term->name ) );
				            }
				            ?>
                        </div>
                    </div>
                </div>
            </div>
	    <?php
	    endwhile;
	    wp_reset_query();
	    ?>
        </div>
            <?php if ('yes' == $settings['pagination']):?>
                <div class="attorg-pagination-wrapper text-center">
		            <?php  appside_master()->post_pagination($post_data);?>
                </div>
            <?php endif;?>
        </div>
        <?php
    }
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Portfolio_Filter_One() );
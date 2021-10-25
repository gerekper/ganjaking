<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Blog_Slider_One_Widget extends Widget_Base {

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
		return 'appside-blog-slider-one-widget';
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
		return esc_html__( 'Blog Slider: 01', 'aapside-master' );
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
		return 'eicon-post-slider';
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
			'query_settings_section',
			[
				'label' => esc_html__( 'Query Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'total',
			[
				'label'       => esc_html__( 'Total Post', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'enter how many post you want to show, enter -1 for unlimited', 'aapside-master' ),
				'default'     => '-1'
			]
		);
		$this->add_control(
			'category',
			[
				'label'       => esc_html__( 'Category', 'aapside-master' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => true,
				'description' => esc_html__( 'select category, for all category leave it blank', 'aapside-master' ),
				'options' => appside_master()->get_terms_names('category','id',true)
			]
		);
		$this->add_control(
			'orderby',
			[
				'label'       => esc_html__( 'Order By', 'aapside-master' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'ID'    => esc_html__( 'ID', 'aapside-master' ),
					'title' => esc_html__( 'Title', 'aapside-master' ),
					'date'  => esc_html__( 'Date', 'aapside-master' ),
				),
				'description' => esc_html__( 'select order by', 'aapside-master' ),
				'default'     => 'ID'
			]
		);
		$this->add_control(
			'order',
			[
				'label'       => esc_html__( 'Order', 'aapside-master' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'ASC'  => esc_html__( 'Ascending', 'aapside-master' ),
					'DESC' => esc_html__( 'Descending', 'aapside-master' ),
				),
				'description' => esc_html__( 'select order.', 'aapside-master' ),
				'default'     => 'ASC'
			]
		);
        $this->end_controls_section();


		$this->start_controls_section(
			'slider_settings_section',
			[
				'label' => esc_html__( 'Slider Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'items',
			[
				'label'       => esc_html__( 'Items', 'aapside-master' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'you can set how many item show in slider', 'aapside-master' ),
				'default'     => '1'
			]
		);
		$this->add_control(
			'margin',
			[
				'label'       => esc_html__( 'Margin', 'aapside-master' ),
				'description' => esc_html__( 'you can set margin for slider', 'aapside-master' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					]
				],
				'default'     => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units'  => [ 'px' ]
			]
		);
		$this->add_control(
			'loop',
			[
				'label'       => esc_html__( 'Loop', 'aapside-master' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'you can set yes/no to enable/disable', 'aapside-master' ),
				'default'     => 'yes'
			]
		);
		$this->add_control(
			'autoplay',
			[
				'label'       => esc_html__( 'Autoplay', 'aapside-master' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'you can set yes/no to enable/disable', 'aapside-master' ),
				'default'     => 'yes'
			]
		);
		$this->add_control(
			'autoplaytimeout',
			[
				'label'      => esc_html__( 'Autoplay Timeout', 'aapside-master' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 10000,
						'step' => 2,
					]
				],
				'default'    => [
					'unit' => 'px',
					'size' => 5000,
				],
				'size_units' => [ 'px' ],
				'condition'  => array(
					'autoplay' => 'yes'
				)
			]
		);
		$this->end_controls_section();


		$this->start_controls_section(
			'post_meta_settings_section',
			[
				'label' => esc_html__( 'Thumbnail Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'plugin-domain' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .single-blog-slider-item .thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_right_gap',
			[
				'label' => esc_html__( 'Image Right Gap', 'aapside-master' ),
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
					'{{WRAPPER}} .single-blog-slider-item .thumb' => 'margin-right: {{SIZE}}{{UNIT}};',
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

		$this->add_control('title_color',[
			'label' => esc_html__('Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .single-blog-slider-item .content .title a' => "color:{{VALUE}}"
			]
		]);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => __( 'Hover', 'aapside-master' ),
			]
		);
		$this->add_control('title_hover_color',[
			'label' => esc_html__('Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .single-blog-slider-item .content .title a:hover' => "color:{{VALUE}}"
			]
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

        $this->end_controls_section();

		/*  title styling tabs end */

		/*  readmore styling tabs start */
		$this->start_controls_section(
			'readmore_settings_section',
			[
				'label' => esc_html__( 'Category Settings', 'aapside-master' ),
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

		$this->add_control('readmore_color',[
			'label' => esc_html__('Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .single-blog-slider-item .content .cats a' => "color:{{VALUE}}"
			]
		]);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'readmore_style_hover_tab',
			[
				'label' => __( 'Hover', 'aapside-master' ),
			]
		);

		$this->add_control('readmore_hover_color',[
			'label' => esc_html__('Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .single-blog-slider-item .content .cats a:hover' => "color:{{VALUE}}"
			]
		]);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
		/*  readmore styling tabs end */

		/*  Typography tabs start */
		$this->start_controls_section(
			'typography_settings_section',
			[
				'label' => esc_html__( 'Typography Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'title_typography',
			'label' => esc_html__('Title Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-blog-slider-item .content .title a"
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'category_typography',
			'label' => esc_html__('Category Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-blog-slider-item .content .cats"
		]);
		$this->end_controls_section();

		/*  Typography tabs end */
	}

	/**
	 * Render Elementor widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
        //query variable
		$settings = $this->get_settings_for_display();
		$category = $settings['category'];
		$total = $settings['total'];
		$orderby = $settings['orderby'];
		$order = $settings['order'];
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		$rand_numb             = rand( 333, 999999999 );
		//slider settings
		$loop            = $settings['loop'] ? 'true' : 'false';
		$items           = $settings['items'] ? $settings['items'] : 4;
		$autoplay        = $settings['autoplay'] ? 'true' : 'false';
		$autoplaytimeout = $settings['autoplaytimeout']['size'];
		?>
        <div class="blog-slider-three-wrapper">
            <div class="appside-blog-carousel-01 owl-carousel"
                 id="blog-one-carousel-01-<?php echo esc_attr( $rand_numb ); ?>"
                 data-loop="<?php echo esc_attr( $loop ); ?>"
                 data-margin="<?php echo esc_attr( $settings['margin']['size'] ); ?>"
                 data-items="<?php echo esc_attr( $items ); ?>"
                 data-autoplay="<?php echo esc_attr( $autoplay ); ?>"
                 data-autoplaytimeout="<?php echo esc_attr( $autoplaytimeout ); ?>"
            >
				<?php
				$args = array(
					'post_type' => 'post',
					'posts_per_page' => $total,
					'orderby' => $orderby,
					'order' => $order,
					'paged' => $paged,
					'ignore_sticky_posts' => 1
				);
				if (!empty($category)){
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'category',
							'field' => 'term_id',
							'terms' => $category
						)
					);
				}
				$result = new \WP_Query($args);
				while ($result->have_posts()):
					$result->the_post();
					$img_id = get_post_thumbnail_id();
					$img_url = wp_get_attachment_image_src($img_id,'appside_grid');
					$img_alt = get_post_meta($img_id,'_wp_attachment_image_alt',true);
					?>
                    <div class="single-blog-slider-item"><!-- single blog slider item -->
                        <div class="thumb">
                            <img src="<?php echo esc_url($img_url[0]);?>" alt="<?php echo esc_attr($img_alt);?>">
                        </div>
                        <div class="content">
                            <h4 class="title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>
                            <div class="cats">
                                <?php the_category(',');?>
                            </div>
                        </div>
                    </div>
				<?php endwhile; wp_reset_query();?>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Blog_Slider_One_Widget() );
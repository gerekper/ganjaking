<?php
/**
 * Elementor Widget
 * @package Appside
 * @since 1.0.0
 */

namespace Elementor;
class Appside_Blog_Grid_One_Widget extends Widget_Base {

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
		return 'appside-blog-grid-one-widget';
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
		return esc_html__( 'Blog Grid One', 'aapside-master' );
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
		return 'eicon-person';
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
			'slider_settings_section',
			[
				'label' => esc_html__( 'Query Settings', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'column',
			[
				'label'       => esc_html__( 'Column', 'aapside-master' ),
				'type'        => Controls_Manager::SELECT,
				'options' => array(
				    '3' => esc_html__('04 Column','aapside-master'),
				    '4' => esc_html__('03 Column','aapside-master'),
				    '2' => esc_html__('06 Column','aapside-master')
                ),
				'description' => esc_html__( 'select grid column', 'aapside-master' ),
				'default'     => '4'
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
		$this->add_control(
			'pagination',
			[
				'label'       => esc_html__( 'Pagination', 'aapside-master' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'you can set yes to show pagination.', 'aapside-master' ),
				'default'     => 'yes'
			]
		);
		$this->add_control(
			'pagination_alignment',
			[
				'label'       => esc_html__( 'Pagination Alignment', 'aapside-master' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'left'   => esc_html__( 'Left Align', 'aapside-master' ),
					'center' => esc_html__( 'Center Align', 'aapside-master' ),
					'right'  => esc_html__( 'Right Align', 'aapside-master' ),
				),
				'description' => esc_html__( 'you can set pagination alignment.', 'aapside-master' ),
				'default'     => 'left',
				'condition'   => array( 'pagination' => 'yes' )
			]
		);
        $this->end_controls_section();

        /*  post meta styling tabs start */

		$this->start_controls_section(
			'post_meta_settings_section',
			[
				'label' => esc_html__( 'Post Meta Styling', 'aapside-master' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs(
			'post_meta_tabs'
		);

		$this->start_controls_tab(
			'post_meta_normal_tab',
			[
				'label' => __( 'Normal', 'aapside-master' ),
			]
		);
		$this->add_control('post_meta_color',[
			'label' => esc_html__('Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .single-blog-grid-item .content .post-meta li a' => "color:{{VALUE}}"
			]
		]);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'post_meta_hover_tab',
			[
				'label' => __( 'Hover', 'aapside-master' ),
			]
		);
		$this->add_control('post_meta_hover_color',[
			'label' => esc_html__('Color','aapside-master'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .single-blog-grid-item .content .post-meta li a:hover' => "color:{{VALUE}}"
			]
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
		/*  post meta styling tabs end */

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
				'{{WRAPPER}} .single-blog-grid-item .content .title a' => "color:{{VALUE}}"
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
				'{{WRAPPER}} .single-blog-grid-item .content .title a:hover' => "color:{{VALUE}}"
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
				'label' => esc_html__( 'Readmore Settings', 'aapside-master' ),
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
				'{{WRAPPER}} .single-blog-grid-item .content .readmore' => "color:{{VALUE}}"
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
				'{{WRAPPER}} .single-blog-grid-item .content .readmore:hover' => "color:{{VALUE}}"
			]
		]);
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

		$this->add_control('pagination_color',[
			'type' => Controls_Manager::COLOR,
			'label' => esc_html__('Color','aapside-master'),
			'selectors' => [
				"{{WRAPPER}} .blog-pagination ul li a" => "color: {{VALUE}}",
				"{{WRAPPER}} .blog-pagination ul li span" => "color: {{VALUE}}",
			]
		]);
		$this->add_control('pagination_border_color',[
			'type' => Controls_Manager::COLOR,
			'label' => esc_html__('Border Color','aapside-master'),
			'selectors' => [
				"{{WRAPPER}} .blog-pagination ul li a" => "border-color: {{VALUE}}",
				"{{WRAPPER}} .blog-pagination ul li span" => "border-color: {{VALUE}}",
			]
		]);
		$this->add_control('pagination_hr',[
			'type' => Controls_Manager::DIVIDER
		]);
		$this->add_group_control(Group_Control_Background::get_type(),[
			'name' => 'pagination_background',
			'label' => esc_html__('Background','aapside-master'),
			'selector' => "{{WRAPPER}} .blog-pagination ul li a, {{WRAPPER}} .blog-pagination ul li span"
		]);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_style_hover_tab',
			[
				'label' => __( 'Hover', 'aapside-master' ),
			]
		);
		$this->add_control('pagination_hover_color',[
			'type' => Controls_Manager::COLOR,
			'label' => esc_html__('Color','aapside-master'),
			'selectors' => [
				"{{WRAPPER}} .blog-pagination ul li a:hover" => "color: {{VALUE}}",
				"{{WRAPPER}} .blog-pagination ul li span.current" => "color: {{VALUE}}",
			]
		]);
		$this->add_control('pagination_hover_border_color',[
			'type' => Controls_Manager::COLOR,
			'label' => esc_html__('Border Color','aapside-master'),
			'selectors' => [
				"{{WRAPPER}} .blog-pagination ul li a:hover" => "border-color: {{VALUE}}",
				"{{WRAPPER}} .blog-pagination ul li span.current" => "border-color: {{VALUE}}",
			]
		]);
		$this->add_control('pagination_hover_hr',[
			'type' => Controls_Manager::DIVIDER
		]);
		$this->add_group_control(Group_Control_Background::get_type(),[
			'name' => 'pagination_hover_background',
			'label' => esc_html__('Background','aapside-master'),
			'selector' => "{{WRAPPER}} .blog-pagination ul li a:hover, {{WRAPPER}} .blog-pagination ul li span.current"
		]);


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
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'title_typography',
			'label' => esc_html__('Title Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-blog-grid-item .content .title"
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'post_meta_typography',
			'label' => esc_html__('Post Meta Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-blog-grid-item .content .post-meta li"
		]);
		$this->add_group_control(Group_Control_Typography::get_type(),[
			'name' => 'readmore_typography',
			'label' => esc_html__('Readmore Typography','aapside-master'),
			'selector' => "{{WRAPPER}} .single-blog-grid-item .content .readmore"
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
		//other settings
		$pagination = $settings['pagination'] ? false : true;
		$pagination_alignment = $settings['pagination_alignment'];
		?>
        <div class="blog-grid-three-wrapper">
            <div class="row">
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
                <div class="col-lg-<?php echo esc_attr($settings['column']);?> col-md-6">
                    <div class="single-blog-grid-item"><!-- single blog grid item -->
                        <div class="thumb">
                            <img src="<?php echo esc_url($img_url[0]);?>" alt="<?php echo esc_attr($img_alt);?>">
                        </div>
                        <div class="content">
                            <ul class="post-meta">
                                <li><?php appside_master()->posted_on();?></li>
                                <li><?php appside_master()->posted_by();?></li>
                            </ul>
                            <h4 class="title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>
                            <a href="<?php the_permalink();?>" class="readmore"><?php echo esc_html__('Read More','aapside-master');?> <i class="fas fa-long-arrow-alt-right"></i></a>
                        </div>
                    </div>
                </div>
				<?php endwhile; wp_reset_query();?>
                <div class="col-lg-12">
                    <div class="blog-pagination text-<?php echo esc_attr($pagination_alignment)?> margin-top-20">
						<?php
						if ( !$pagination ){
							appside_master()->post_pagination($result);
						}
						?>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Appside_Blog_Grid_One_Widget() );
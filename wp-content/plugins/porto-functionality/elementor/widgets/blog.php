<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Blog Widget
 *
 * Porto Elementor widget to display posts.
 *
 * @since 5.1.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Blog_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_blog';
	}

	public function get_title() {
		return __( 'Porto Blog', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'blog', 'posts', 'article' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function _register_controls() {
		$order_by_values  = array_slice( porto_vc_order_by(), 1 );
		$order_way_values = array_slice( porto_vc_woo_order_way(), 1 );
		$slider_options   = porto_vc_product_slider_fields();
		unset( $slider_options[8] );
		unset( $slider_options[9] );
		$slider_options = porto_update_vc_options_to_elementor( $slider_options );

		$slider_options['nav_pos2']['condition']['navigation'] = 'yes';
		$slider_options['nav_type']['condition']['navigation'] = 'yes';

		$this->start_controls_section(
			'section_blog',
			array(
				'label' => __( 'Blog', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'porto-functionality' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Title', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'post_layout',
			array(
				'label'   => __( 'Blog Layout', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'timeline',
				'options' => array_combine( array_values( porto_sh_commons( 'blog_layout' ) ), array_keys( porto_sh_commons( 'blog_layout' ) ) ),
			)
		);

		$this->add_control(
			'grid_layout',
			array(
				'label'     => __( 'Grid Layout', 'porto-functionality' ),
				'type'      => 'image_choose',
				'default'   => '1',
				'options'   => array_combine( array_values( porto_sh_commons( 'masonry_layouts' ) ), array_keys( porto_sh_commons( 'masonry_layouts' ) ) ),
				'condition' => array(
					'post_layout' => 'creative',
				),
			)
		);

		$this->add_control(
			'grid_height',
			array(
				'label'     => __( 'Grid Height', 'porto-functionality' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '600px',
				'condition' => array(
					'post_layout' => 'creative',
				),
			)
		);

		$this->add_control(
			'masonry_layout',
			array(
				'label'     => __( 'Masonry Layout', 'porto-functionality' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '1',
				'options'   => array(
					'1' => '1',
				),
				'condition' => array(
					'post_layout' => 'masonry-creative',
				),
			)
		);

		$this->add_control(
			'post_style',
			array(
				'label'       => __( 'Post Style', 'porto-functionality' ),
				'description' => __( 'Only "Hover Info" and "Hover Info 2" styles are available for "Grid - Creative" Blog Layout, "Simple Grid", "Simple List" and "Widget Style" styles are available for only "Grid" and "Masonry" blog layouts, and "Modern" style is available for only "Grid" and "Slider" layout.', 'porto-functionality' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''            => __( 'Theme Options', 'porto-functionality' ),
					'default'     => __( 'Default', 'porto-functionality' ),
					'date'        => __( 'Default - Date on Image', 'porto-functionality' ),
					'author'      => __( 'Default - Author Picture', 'porto-functionality' ),
					'related'     => __( 'Post Carousel Style', 'porto-functionality' ),
					'hover_info'  => __( 'Hover Info', 'porto-functionality' ),
					'hover_info2' => __( 'Hover Info 2', 'porto-functionality' ),
					'padding'     => __( 'With Borders', 'porto-functionality' ),
					'grid'        => __( 'Simple Grid', 'porto-functionality' ),
					'list'        => __( 'Simple List', 'porto-functionality' ),
					'widget'      => __( 'Widget Style', 'porto-functionality' ),
					'modern'      => __( 'Modern', 'porto-functionality' ),
				),
				'condition'   => array(
					'post_layout' => array( 'grid', 'masonry', 'timeline', 'creative', 'masonry-creative', 'slider' ),
				),
			)
		);

		$this->add_control(
			'meta_type',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Post Meta Type', 'porto-functionality' ),
				'condition' => array(
					'post_style' => array( 'list', 'hover_info', 'hover_info2' ),
				),
				'default'   => '',
				'options'   => array(
					''     => __( 'None', 'porto-functionality' ),
					'date' => __( 'Show Date', 'porto-functionality' ),
					'cat'  => __( 'Show Categories', 'porto-functionality' ),
					'both' => __( 'Show Date & Categories', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'columns',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Columns', 'porto-functionality' ),
				'condition' => array(
					'post_layout' => array( 'grid', 'masonry', 'slider' ),
				),
				'default'   => '3',
				'options'   => porto_sh_commons( 'blog_grid_columns' ),
			)
		);

		$this->add_control(
			'no_spacing',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'No Space Between Posts?', 'porto-functionality' ),
				'condition' => array(
					'post_layout' => array( 'grid', 'masonry', 'creative', 'masonry-creative' ),
				),
			)
		);

		$this->add_control(
			'cats',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Category IDs or slugs', 'porto-functionality' ),
				'description' => __( 'comma separated list of category ids or slugs', 'porto-functionality' ),
				'options'     => 'category',
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->add_control(
			'post_in',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Post IDs', 'porto-functionality' ),
				'description' => __( 'comma separated list of post ids', 'porto-functionality' ),
				'options'     => 'post',
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->add_control(
			'number',
			array(
				'type'    => Controls_Manager::SLIDER,
				'label'   => __( 'Posts Count', 'porto-functionality' ),
				'default' => array(
					'size' => 8,
				),
				'range'   => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Order by', 'porto-functionality' ),
				'options'     => array_combine( array_values( $order_by_values ), array_keys( $order_by_values ) ),
				/* translators: %s: Wordpres codex page */
				'description' => sprintf( __( 'Select how to sort retrieved posts. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
			)
		);

		$this->add_control(
			'order',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Order way', 'porto-functionality' ),
				'options'     => array_combine( array_values( $order_way_values ), array_keys( $order_way_values ) ),
				/* translators: %s: Wordpres codex page */
				'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'type'  => Controls_Manager::NUMBER,
				'label' => __( 'Excerpt Length', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'view_more',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Pagination Style', 'porto-functionality' ),
				'options' => array(
					''     => __( 'No Pagination', 'porto-functionality' ),
					'show' => __( 'Show Pagination', 'porto-functionality' ),
					'link' => __( 'Show Blog Page Link', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'view_more_class',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Extra class name for Archive Link', 'porto-functionality' ),
				'condition' => array(
					'view_more' => 'link',
				),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Image Size', 'porto-functionality' ),
				'options'   => array_combine( array_values( porto_sh_commons( 'image_sizes' ) ), array_keys( porto_sh_commons( 'image_sizes' ) ) ),
				'default'   => '',
				'condition' => array(
					'post_layout' => array( 'grid', 'masonry', 'timeline', 'slider' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_options',
			array(
				'label'     => __( 'Slider Options', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'post_layout' => 'slider',
				),
			)
		);

		foreach ( $slider_options as $key => $opt ) {
			unset( $opt['condition']['view'] );
			$this->add_control( $key, $opt );
		}

		$this->add_control(
			'margin',
			array(
				'type'  => Controls_Manager::NUMBER,
				'label' => __( 'Spacing between items (px)', 'porto-functionality' ),
				'condition' => array(
					'post_layout' => 'slider',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( $template = porto_shortcode_template( 'porto_blog' ) ) {
			if ( ! empty( $atts['cats'] ) && is_array( $atts['cats'] ) ) {
				$atts['cats'] = implode( ',', $atts['cats'] );
			}
			if ( ! empty( $atts['post_in'] ) && is_array( $atts['post_in'] ) ) {
				$atts['post_in'] = implode( ',', $atts['post_in'] );
			}
			include $template;
		}
	}

	protected function content_template() {}
}

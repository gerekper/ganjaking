<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Portfolio Widget
 *
 * Porto Elementor widget to display portfolios.
 *
 * @since 5.1.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Portfolio_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_portfolios';
	}

	public function get_title() {
		return __( 'Porto Portfolio', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'portfolio', 'posts', 'article' );
	}

	public function get_icon() {
		return 'eicon-image-before-after';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'isotope', 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function _register_controls() {
		$order_by_values  = array_slice( porto_vc_order_by(), 1 );
		$order_way_values = array_slice( porto_vc_woo_order_way(), 1 );

		$this->start_controls_section(
			'section_portfolio',
			array(
				'label' => __( 'Portfolio Layout', 'porto-functionality' ),
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
			'portfolio_layout',
			array(
				'label'   => __( 'Portfolio Layout', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'timeline',
				'options' => array_combine( array_values( porto_sh_commons( 'portfolio_layout' ) ), array_keys( porto_sh_commons( 'portfolio_layout' ) ) ),
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
					'portfolio_layout' => 'creative',
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
					'portfolio_layout' => 'creative',
				),
			)
		);

		$this->add_control(
			'spacing',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Column Spacing (px)', 'porto-functionality' ),
				'description' => __( 'Leave blank if you use theme default value.', 'porto-functionality' ),
				'range'       => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
				),
				'condition'   => array(
					'portfolio_layout' => array( 'creative', 'masonry-creative' ),
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
					'portfolio_layout' => 'masonry-creative',
				),
			)
		);

		$this->add_control(
			'content_animation',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Content Animation', 'porto-functionality' ),
				'description' => __( 'Please check this url to see animation types.', 'porto-functionality' ) . ' <a href="https://www.portotheme.com/wordpress/porto/shortcodes/animations/" target="_blank">https://www.portotheme.com/wordpress/porto/shortcodes/animations/</a>',
				'condition'   => array(
					'portfolio_layout' => array( 'large', 'fullscreen' ),
				),
			)
		);

		$this->add_control(
			'columns',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Columns', 'porto-functionality' ),
				'condition' => array(
					'portfolio_layout' => array( 'grid', 'masonry' ),
				),
				'default'   => '3',
				'options'   => porto_sh_commons( 'portfolio_grid_columns' ),
			)
		);

		$this->add_control(
			'view',
			array(
				'label'     => __( 'View Type', 'porto-functionality' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'classic',
				'options'   => array_combine( array_values( porto_sh_commons( 'portfolio_grid_view' ) ), array_keys( porto_sh_commons( 'portfolio_grid_view' ) ) ),
				'condition' => array(
					'portfolio_layout' => array( 'grid', 'masonry', 'timeline', 'creative', 'masonry-creative' ),
				),
			)
		);

		$this->add_control(
			'info_view',
			array(
				'label'     => __( 'Info View Type', 'porto-functionality' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''                 => __( 'Standard', 'porto-functionality' ),
					'left-info'        => __( 'Left Info', 'porto-functionality' ),
					'left-info-no-bg'  => __( 'Left Info & No bg', 'porto-functionality' ),
					'centered-info'    => __( 'Centered Info', 'porto-functionality' ),
					'bottom-info'      => __( 'Bottom Info', 'porto-functionality' ),
					'bottom-info-dark' => __( 'Bottom Info Dark', 'porto-functionality' ),
					'hide-info-hover'  => __( 'Hide Info Hover', 'porto-functionality' ),
					'plus-icon'        => __( 'Plus Icon', 'porto-functionality' ),
				),
				'condition' => array(
					'portfolio_layout' => array( 'grid', 'masonry', 'timeline', 'creative', 'masonry-creative' ),
				),
			)
		);

		$this->add_control(
			'info_color_2',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Info Color', 'porto-functionality' ),
				'condition' => array(
					'portfolio_layout' => array( 'grid', 'masonry', 'timeline', 'creative', 'masonry-creative' ),
					'info_view'        => 'left-info-no-bg',
				),
				'selectors' => array(
					'{{WRAPPER}} .thumb-info .thumb-info-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'custom_portfolios',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Portfolio Indexes to use custom info color', 'porto-functionality' ),
				'description' => __( 'comma separated list of portfolio indexes', 'porto-functionality' ),
				'condition'   => array(
					'portfolio_layout' => array( 'grid', 'masonry', 'timeline', 'creative', 'masonry-creative' ),
					'info_view'        => 'left-info-no-bg',
				),
			)
		);

		$this->add_control(
			'info_color2',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Info Color for custom portfolios', 'porto-functionality' ),
				'condition' => array(
					'portfolio_layout'   => array( 'grid', 'masonry', 'timeline', 'creative', 'masonry-creative' ),
					'info_view'          => 'left-info-no-bg',
					'custom_portfolios!' => '',
				),
			)
		);

		$this->add_control(
			'info_view_type_style',
			array(
				'label'     => __( 'Info View Type Style', 'porto-functionality' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''                    => __( 'Standard', 'porto-functionality' ),
					'alternate-info'      => __( 'Alternate', 'porto-functionality' ),
					'alternate-with-plus' => __( 'Alternate with Plus', 'porto-functionality' ),
					'no-style'            => __( 'No Style', 'porto-functionality' ),
				),
				'condition' => array(
					'portfolio_layout' => array( 'grid', 'masonry', 'timeline', 'creative', 'masonry-creative' ),
				),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Image Size', 'porto-functionality' ),
				'options' => array_combine( array_values( porto_sh_commons( 'image_sizes' ) ), array_keys( porto_sh_commons( 'image_sizes' ) ) ),
			)
		);

		$this->add_control(
			'thumb_bg',
			array(
				'label'   => __( 'Image Overlay Background', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					''                => __( 'Standard', 'porto-functionality' ),
					'darken'          => __( 'Darken', 'porto-functionality' ),
					'lighten'         => __( 'Lighten', 'porto-functionality' ),
					'hide-wrapper-bg' => __( 'Transparent', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'thumb_image',
			array(
				'label'   => __( 'Hover Image Effect', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					''          => __( 'Standard', 'porto-functionality' ),
					'zoom'      => __( 'Zoom', 'porto-functionality' ),
					'slow-zoom' => __( 'Slow Zoom', 'porto-functionality' ),
					'no-zoom'   => __( 'No Zoom', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'image_counter',
			array(
				'label'     => __( 'Image Counter', 'porto-functionality' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''     => __( 'Default', 'porto-functionality' ),
					'show' => __( 'Show', 'porto-functionality' ),
					'hide' => __( 'Hide', 'porto-functionality' ),
				),
				'condition' => array(
					'portfolio_layout' => array( 'grid', 'masonry', 'timeline' ),
				),
			)
		);

		$this->add_control(
			'show_lightbox_icon',
			array(
				'label'   => __( 'Show Image Lightbox Icon', 'porto-functionality' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					''     => __( 'Default', 'porto-functionality' ),
					'show' => __( 'Show', 'porto-functionality' ),
					'hide' => __( 'Hide', 'porto-functionality' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_portfolio_selector',
			array(
				'label' => __( 'Portfolio Selector', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'cats',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Category IDs', 'porto-functionality' ),
				'description' => __( 'comma separated list of category ids', 'porto-functionality' ),
				'options'     => 'portfolio_cat',
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->add_control(
			'post_in',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Portfolio IDs', 'porto-functionality' ),
				'description' => __( 'comma separated list of portfolio ids', 'porto-functionality' ),
				'options'     => 'portfolio',
				'multiple'    => true,
				'label_block' => true,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Order by', 'porto-functionality' ),
				'options'     => array_combine( array_values( $order_by_values ), array_keys( $order_by_values ) ),
				/* translators: %s: Wordpres codex page */
				'description' => sprintf( __( 'Select how to sort retrieved portfolios. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
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
			'slider',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Slider on Portfolio', 'porto-functionality' ),
				'description' => __( 'comma separated list of portfolio ids. <br /> Will Only work with ajax on page settings', 'porto-functionality' ),
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
			'excerpt_length',
			array(
				'type'  => Controls_Manager::NUMBER,
				'label' => __( 'Excerpt Length', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'load_more_posts',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Load More Posts', 'porto-functionality' ),
				'options' => array(
					''              => __( 'Select', 'porto-functionality' ),
					'pagination'    => __( 'Pagination', 'porto-functionality' ),
					'load-more-btn' => __( 'Load More (Button)', 'porto-functionality' ),
				),
				'default' => '',
			)
		);

		$this->add_control(
			'view_more',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Show Archive Link', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'view_more_class',
			array(
				'type'      => Controls_Manager::TEXT,
				'label'     => __( 'Extra class name for Archive Link', 'porto-functionality' ),
				'condition' => array(
					'view_more' => 'yes',
				),
			)
		);

		$this->add_control(
			'filter',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Show Filter', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'filter_style',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Filter Style', 'porto-functionality' ),
				'options'   => array(
					''        => __( 'Style 1', 'porto-functionality' ),
					'style-2' => __( 'Style 2', 'porto-functionality' ),
					'style-3' => __( 'Style 3', 'porto-functionality' ),
				),
				'default'   => '',
				'condition' => array(
					'filter' => 'yes',
				),
			)
		);

		$this->add_control(
			'ajax_load',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Enable Ajax Load', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'ajax_modal',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Ajax Load on Modal', 'porto-functionality' ),
				'condition' => array(
					'ajax_load' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( $template = porto_shortcode_template( 'porto_portfolios' ) ) {
			if ( isset( $atts['spacing']['size'] ) ) {
				$atts['spacing'] = $atts['spacing']['size'];
			} else {
				unset( $atts['spacing'] );
			}
			if ( ! empty( $atts['number']['size'] ) ) {
				$atts['number'] = $atts['number']['size'];
			} else {
				unset( $atts['number'] );
			}
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

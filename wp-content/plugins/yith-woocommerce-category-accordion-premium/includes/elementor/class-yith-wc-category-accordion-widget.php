<?php
/**
 * Register elementor widget
 *
 * @package YITH\CategoryAccordion\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class YITH_WC_Category_Accordion_Widget extends \Elementor\Widget_Base {
	/**
	 * Get widget name.
	 *
	 * Retrieve YITH_WC_Category_Accordion_Widget widget name.
	 *
	 * @return string Widget name.
	 * @since  1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'yith_wc_category_accordion_menu';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve YITH_WC_Category_Accordion_Widget widget title.
	 *
	 * @return string Widget title.
	 * @since  1.0.0
	 * @access public
	 */
	public function get_title() {
		return _x( 'YITH Category Accordion', 'Elementor widget name', 'yith-woocommerce-category-accordion' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve YITH_WC_Category_Accordion_Widget widget icon.
	 *
	 * @return string Widget icon.
	 * @since  1.0.0
	 * @access public
	 */
	public function get_icon() {
		return 'eicon-menu-bar';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the YITH_WC_Category_Accordion_Widget widget belongs to.
	 *
	 * @return array Widget categories.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_categories() {
		return [ 'general', 'yith' ];
	}

	/**
	 * Register YITH_WC_Category_Accordion_Widget widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'fields_section',
			array(
				'label' => _x( 'Dashboard details', 'Elementor section title', 'yith-woocommerce-category-accordion' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'yith-woocommerce-category-accordion' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'how_show',
			array(
				'label'   => _x( 'Show in Accordion', 'Elementor control label', 'yith-woocommerce-category-accordion' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'wc'   => __( 'WooCommerce Category', 'yith-woocommerce-category-accordion' ),
					'wp'   => __( 'WordPress Category', 'yith-woocommerce-category-accordion' ),
					'tag'  => __( 'Tags', 'yith-woocommerce-category-accordion' ),
					'menu' => __( 'Menu', 'yith-woocommerce-category-accordion' ),
				),
				'default' => 'wc',
			)
		);
		$this->add_control(
			'show_wc_subcat',
			array(
				'label'        => __( 'Show WooCommerce Subcategories', 'yith-woocommerce-category-accordion' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'on',
				'default'      => 'off',
				'condition'    => array(
					'how_show' => 'wc',
				),
			)
		);
		$this->add_control(
			'show_wp_subcat',
			array(
				'label'        => __( 'Show WordPress Subcategories', 'yith-woocommerce-category-accordion' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'on',
				'default'      => 'off',
				'condition'    => array(
					'how_show' => 'wp',
				),
			)
		);
		$this->add_control(
			'show_post',
			array(
				'label'        => __( 'Show Last post', 'yith-woocommerce-category-accordion' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'on',
				'default'      => 'off',
				'condition'    => array(
					'how_show' => 'wp',
				),
			)
		);
		$this->add_control(
			'post_limit',
			array(
				'label'     => __( 'Number Post (-1 for all post )', 'yith-woocommerce-category-accordion' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => - 1,
				'min'       => - 1,
				'step'      => 1,
				'condition' => array(
					'how_show' => 'wp',
				),
			)
		);
		$this->add_control(
			'tag_wc',
			array(
				'label'        => __( 'WooCommerce tag', 'yith-woocommerce-category-accordion' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'on',
				'default'      => 'off',
				'condition'    => array(
					'how_show' => 'tag',
				),
			)
		);
		$this->add_control(
			'name_wc_tag',
			array(
				'label'     => __( 'WooCommerce Tag Label', 'yith-woocommerce-category-accordion' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => 'WooCommerce TAGS',
				'condition' => array(
					'how_show' => 'tag',
					'tag_wc'   => 'on',
				),
			)
		);

		$this->add_control(
			'tag_wp',
			array(
				'label'        => __( 'WordPress tag', 'yith-woocommerce-category-accordion' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'on',
				'default'      => 'off',
				'condition'    => array(
					'how_show' => 'tag',
				),
			)
		);
		$this->add_control(
			'name_wp_tag',
			array(
				'label'     => __( 'WordPress Tag Label', 'yith-woocommerce-category-accordion' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => 'WordPress TAGS',
				'condition' => array(
					'how_show' => 'tag',
					'tag_wp'   => 'on',
				),
			)
		);
		$this->add_control(
			'include_menu',
			array(
				'label'     => __( 'Add menu in accordion', 'yith-woocommerce-category-accordion' ),
				'type'      => \Elementor\Controls_Manager::SELECT2,
				'options'   => yith_get_navmenu(),
				'condition' => array(
					'how_show' => 'menu',
				),
				'multiple'  => true,
			)
		);

		$this->add_control(
			'exclude_wc_cat',
			array(
				'label'     => __( 'Exclude WooCommerce Categories', 'yith-woocommerce-category-accordion' ),
				'type'      => \Elementor\Controls_Manager::SELECT2,
				'options'   => $this->get_formatted_taxonomy( 'product_cat' ),
				'condition' => array(
					'how_show' => 'wc',
				),
				'multiple'  => true,
			)
		);
		$this->add_control(
			'exclude_wp_cat',
			array(
				'label'     => __( 'Exclude WordPress Categories', 'yith-woocommerce-category-accordion' ),
				'type'      => \Elementor\Controls_Manager::SELECT2,
				'options'   => $this->get_formatted_taxonomy( 'category' ),
				'condition' => array(
					'how_show' => 'wp',
				),
				'multiple'  => true,
			)
		);

		$this->add_control(
			'highlight_curr_cat',
			array(
				'label'        => __( 'Highlight the current category', 'yith-woocommerce-category-accordion' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'on',
				'default'      => 'on',
			)
		);
		$this->add_control(
			'show_count',
			array(
				'label'        => __( 'Show count', 'yith-woocommerce-category-accordion' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'on',
				'default'      => 'on',
				'conditions'   => array(
					'terms' => array(
						array(
							'name'     => 'how_show',
							'operator' => 'in',
							'value'    => array(
								'wc',
								'wp',
								'tag',
							),
						),
					),
				),
			)
		);
		$options = array();
		$args = array(
			'numberposts' => -1,
			'post_type'   => 'yith_cacc'
		);

		$category_styles_posts = get_posts( $args );

		if ( ! empty( $category_styles_posts ) ) {
			foreach ( $category_styles_posts as $style ) {
				$options[$style->ID] = __($style->post_title,  'yith-woocommerce-category-accordion');
			}
		}

		$this->add_control(
			'acc_style',
			array(
				'label'    => __( 'Style', 'yith-woocommerce-category-accordion' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'options'  => $options,
				'multiple' => false,
				'default'  => 'style_1',
			)
		);

		$this->add_control(
			'exclude_page',
			array(
				'label'    => __( 'Hide Accordion in pages', 'yith-woocommerce-category-accordion' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'options'  => $this->get_formatted_pages(),
				'multiple' => true,
			)
		);
		$this->add_control(
			'exclude_post',
			array(
				'label'    => __( 'Hide Accordion in posts', 'yith-woocommerce-category-accordion' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'options'  => $this->get_formatted_posts(),
				'multiple' => true,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => __( 'Order By', 'yith-woocommerce-category-accordion' ),
				'type'      => \Elementor\Controls_Manager::SELECT2,
				'options'   => array(
					'name'       => __( 'Name', 'yith-woocommerce-category-accordion' ),
					'count'      => __( 'Count', 'yith-woocommerce-category-accordion' ),
					'id'         => __( 'ID', 'yith-woocommerce-category-accordion' ),
					'menu_order' => __( 'WooCommerce Order', 'yith-woocommerce-category-accordion' ),
				),
				'multiple'  => false,
				'default'   => 'name',
				'condition' => array(
					'how_show!' => 'menu',
				),
			)
		);
		$this->add_control(
			'order',
			array(
				'label'      => '',
				'show_label' => false,
				'type'       => \Elementor\Controls_Manager::SELECT2,
				'options'    => array(
					'asc'  => __( 'ASC', 'yith-woocommerce-category-accordion' ),
					'desc' => __( 'DESC', 'yith-woocommerce-category-accordion' ),
				),
				'multiple'   => false,
				'default'    => 'asc',
				'condition'  => array(
					'how_show!' => 'menu',
				),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render YITH_WC_Category_Accordion_Widget widget output on the frontend.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {

		$attribute_string = '';
		$instance         = $this->get_settings_for_display();

		$how_show = $instance['how_show'];

		$string_build_shortcode = 'how_show="' . $how_show . '" ';

		$string_build_shortcode .= 'title="' . $instance['title'] . '" ';

		switch ( $how_show ) {
			case 'wc':
				$show_sub_cat = $instance['show_wc_subcat'];
				$exclude_cat  = $instance['exclude_wc_cat'];
				$show_count   = $instance['show_count'];

				if ( is_array( $exclude_cat ) ) {
					$exclude_cat = implode( ',', $exclude_cat );
				}

				$string_build_shortcode .= 'show_sub_cat="' . $show_sub_cat . '" exclude_cat="' . $exclude_cat . '" show_count="' . $show_count . '" ';
				break;
			case 'wp':
				$show_sub_cat   = $instance['show_wp_subcat'];
				$exclude_cat    = $instance['exclude_wp_cat'];
				$show_last_post = $instance['show_post'];
				$post_limit     = $instance['post_limit'];
				$show_count     = $instance['show_count'];
				if ( is_array( $exclude_cat ) ) {
					$exclude_cat = implode( ',', $exclude_cat );
				}
				$string_build_shortcode .= 'show_sub_cat="' . $show_sub_cat . '" exclude_cat="' . $exclude_cat . '" show_last_post="' . $show_last_post . '" post_limit="' . $post_limit . '" show_count="' . $show_count . '" ';
				break;
			case 'menu':
				$menu_ids = implode( ',', $instance['include_menu'] );

				$string_build_shortcode .= 'menu_ids="' . $menu_ids . '" ';

				break;

			case 'tag':
				$menu_wc_name = $instance['name_wc_tag'];
				$menu_wp_name = $instance['name_wp_tag'];

				$string_build_shortcode .= 'tag_wc="' . $instance['tag_wc'] . '" tag_wp="' . $instance['tag_wp'] . '"  name_wc_tag="' . $menu_wc_name . '" name_wp_tag="' . $menu_wp_name . '" ';
				break;

		}

		$exclude_page = $instance['exclude_page'];
		$exclude_post = $instance['exclude_post'];
		$highlight    = $instance['highlight_curr_cat'];
		$style        = $instance['acc_style'];
		$orderby      = $instance['orderby'];
		$order        = $instance['order'];

		$exclude_page = is_array( $exclude_page ) ? implode( ',', $exclude_page ) : $exclude_page;
		$exclude_post = is_array( $exclude_post ) ? implode( ',', $exclude_post ) : $exclude_post;

		$string_build_shortcode .= 'exclude_page="' . $exclude_page . '" exclude_post="' . $exclude_post . '" highlight="' . $highlight . '" orderby="' . $orderby . '" order="' . $order . '" acc_style="' . $style . '" ';
		global $YIT_Category_Accordion;

		if(strpos($instance['acc_style'], 'style') > -1){
			$style = $instance['acc_style'];
			$style_id = get_option(str_replace('_', '', $style)."_id", ' ');

		}elseif ('' === $instance['acc_style']){

			$args       = array(
				'numberposts' => 1,
				'post_type'   => 'yith_cacc',
				);
			$first_post = get_posts( $args );
			$style_id   = $first_post[0]->ID;

		} else {
			$style_id = $instance['acc_style'];
		}
		require_once YWCCA_INC . 'functions.yith-category-accordion-generate-styles.php';

		$css_inline = ywcca_generate_style_from_post( $style_id );

		wp_add_inline_style( 'ywcca_accordion_style', $css_inline );
		wp_enqueue_style( 'ywcca_accordion_style' );
		echo do_shortcode( "[yith_wcca_category_accordion {$string_build_shortcode}]" );
	}

	/**
	 * Return the array id=>name of current taxonomy
	 *
	 * @param string $taxonomy_name The taxonomy name.
	 *
	 * @return int[]|string|string[]|WP_Error|WP_Term[]
	 * @author YITH <plugins@yithemes.com>
	 * @since 1.0.46
	 */
	protected function get_formatted_taxonomy( $taxonomy_name ) {

		$all_taxonomy = get_terms( array( 'taxonomy' => $taxonomy_name, 'fields' => 'id=>name' ) );

		return $all_taxonomy;
	}

	/**
	 * Return the formatted array with id and page title
	 *
	 * @return array
	 * @since 1.04.6
	 */
	protected function get_formatted_pages() {
		$pages     = get_pages( array( 'post_status' => 'publish' ) );
		$all_pages = array();

		foreach ( $pages as $page ) {
			$all_pages[ $page->ID ] = get_the_title( $page );
		}

		return $all_pages;
	}

	/**
	 * Return the formatted array with id and post title
	 *
	 * @return array
	 * @since 1.04.6
	 */
	protected function get_formatted_posts() {
		$posts     = get_posts( array( 'numberposts' => - 1, 'fields' => 'ids', 'post_status' => 'publish' ) );
		$all_posts = array();

		foreach ( $posts as $post_id ) {
			$all_posts[ $post_id ] = get_the_title( $post_id );
		}

		return $all_posts;
	}
}

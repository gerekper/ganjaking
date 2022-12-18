<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Archive Builder
 *
 * @since 2.3.0
 */

use Elementor\Controls_Manager;

if ( ! class_exists( 'PortoBuildersArchive' ) ) :
	class PortoBuildersArchive {

		/**
		 * Display WPB Elements
		 *
		 * @access private
		 * @var boolean $display_wpb_elements
		 * @since 2.3.0
		 */
		private $display_wpb_elements = false;

		/**
		 * The Shortcodes
		 *
		 * @access private
		 * @var array $shortcodes
		 * @since 2.3.0
		 */
		private $shortcodes = array(
			'posts-grid',
		);

		/**
		 * Edit Post
		 *
		 * @access public
		 * @var object $edit_post
		 * @since 2.3.0
		 */
		public $edit_post = null;

		/**
		 * Edit Post Type
		 *
		 * @access public
		 * @var object $edit_post_type
		 * @since 2.3.0
		 */
		public $edit_post_type = null;

		/**
		 * Preview Mode
		 *
		 * @access public
		 * @var object $preview_mode
		 * @since 2.3.0
		 */
		public $preview_mode = '';

		/**
		 * Global Instance Objects
		 *
		 * @var array $instances
		 * @since 2.3.0
		 * @access private
		 */
		private static $instance = null;

		public static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->init();
		}

		/**
		 * init
		 *
		 * @since 2.3.0
		 */
		public function init() {
			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				if ( is_admin() && isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) {
					add_action(
						'elementor/elements/categories_registered',
						function( $self ) {
							$self->add_category(
								'porto-archive',
								array(
									'title'  => __( 'Porto Archive Builder', 'porto-functionality' ),
									'active' => false,
								)
							);
						}
					);
				}
				add_action( 'elementor/widgets/register', array( $this, 'elementor_custom_archive_shortcodes' ), 10, 1 );
				//apply changed post
				add_action( 'wp_ajax_porto_archive_builder_preview_apply', array( $this, 'apply_preview_el_post' ) );
				add_action( 'elementor/documents/register_controls', array( $this, 'register_elementor_preview_controls' ) );
			}
			if ( defined( 'WPB_VC_VERSION' ) ) {
				add_action( 'vc_after_init', array( $this, 'load_wpb_map_elements' ) );

				add_filter( 'vc_autocomplete_porto_ab_posts_grid_builder_id_callback', 'builder_id_callback' );
				add_filter( 'vc_autocomplete_porto_ab_posts_grid_builder_id_render', 'builder_id_render' );

				//apply changed post
				add_action( 'wp_ajax_porto_archive_builder_preview_wpb_apply', array( $this, 'apply_preview_wpb_post' ) );
				add_action(
					'template_redirect',
					function() {
						$should_add_shortcodes = false;
						if ( ( is_singular( PortoBuilders::BUILDER_SLUG ) && 'archive' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) || ! empty( $_GET['vcv-ajax'] ) || ( function_exists( 'porto_is_ajax' ) && porto_is_ajax() && ! empty( $_GET[ PortoBuilders::BUILDER_SLUG ] ) ) ) {
							$should_add_shortcodes = true;
						} elseif ( function_exists( 'porto_check_builder_condition' ) && porto_check_builder_condition( 'archive' ) ) {
							$should_add_shortcodes = true;
						}

						if ( $should_add_shortcodes ) {
							$this->add_shortcodes();
						}
					}
				);

				add_action(
					'admin_init',
					function() {
						$should_add_shortcodes = false;
						if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'vc_save' == $_REQUEST['action'] ) {
							$should_add_shortcodes = true;
						} elseif ( isset( $_POST['action'] ) && 'editpost' == $_POST['action'] && isset( $_POST['post_type'] ) && PortoBuilders::BUILDER_SLUG == $_POST['post_type'] ) {
							$should_add_shortcodes = true;
						}

						if ( $should_add_shortcodes ) {
							$this->add_shortcodes();
						}
					}
				);
			}
			$this->find_preview();
		}

		/**
		 * Register archive shortcodes
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function elementor_custom_archive_shortcodes( $self ) {
			$load_widgets = false;

			if ( is_singular( PortoBuilders::BUILDER_SLUG ) && 'archive' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
				$load_widgets = true;
			} elseif ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' == $_REQUEST['action'] && ! empty( $_POST['editor_post_id'] ) ) {
				$load_widgets = true;
			} elseif ( function_exists( 'porto_check_builder_condition' ) && porto_check_builder_condition( 'archive' ) ) {
				$load_widgets = true;
			}

			if ( $load_widgets ) {
				foreach ( $this->shortcodes as $shortcode ) {
					include_once PORTO_BUILDERS_PATH . 'elements/archive/elementor/' . $shortcode . '.php';
					$class_name = 'Porto_Elementor_Archive_' . ucfirst( str_replace( '-', '_', $shortcode ) ) . '_Widget';
					if ( class_exists( $class_name ) ) {
						$self->register( new $class_name( array(), array( 'widget_name' => $class_name ) ) );
					}
				}
			}
		}

		/**
		 * Add shortcodes for WPBakery elements
		 *
		 * @since 2.3.0
		 */
		public function add_shortcodes() {
			add_shortcode( 'porto_ab_posts_grid', array( $this, 'shortcode_archive_posts_grid' ) );
		}
		/**
		 * Add WPBakery Page Builder Archive elements
		 *
		 * @since 2.3.0
		 */
		public function load_wpb_map_elements() {
			if ( ! $this->display_wpb_elements ) {
				$this->display_wpb_elements = PortoBuilders::check_load_wpb_elements( 'archive' );
			}
			if ( ! $this->display_wpb_elements ) {
				return;
			}

			$custom_class = porto_vc_custom_class();

			$taxes = get_taxonomies( array( 'public' => true ), 'objects' );
			unset( $taxes['post_format'] );
			foreach ( $taxes as $tax_name => $tax ) {
				if ( 'product_' == substr( $tax_name, 0, 8 ) ) {
					unset( $taxes[ $tax_name ] );
				} else {
					$taxes[ $tax_name ] = esc_html( $tax->label );
				}
			}
			$taxes = apply_filters( 'porto_posts_grid_taxonomies', $taxes );
			$left  = is_rtl() ? 'right' : 'left';
			$right = is_rtl() ? 'left' : 'right';
			vc_map(
				array(
					'name'        => __( 'Archive Posts Grid', 'porto-functionality' ),
					'base'        => 'porto_ab_posts_grid',
					'icon'        => 'far fa-calendar-alt',
					'category'    => __( 'Archive Builder', 'porto-functionality' ),
					'description' => __( 'Show archive elements in the layout which built using Post Type Builder.', 'porto-functionality' ),
					'params'      => array_merge(
						array(
							array(
								'type'       => 'porto_param_heading',
								'param_name' => 'posts_layout',
								'text'       => __( 'Posts Selector', 'porto-functionality' ),
							),
							array(
								'type'        => 'autocomplete',
								'heading'     => __( 'Post Layout', 'porto-functionality' ),
								'param_name'  => 'builder_id',
								'settings'    => array(
									'multiple'      => false,
									'sortable'      => true,
									'unique_values' => true,
								),
								/* translators: starting and end A tags which redirects to edit page */
								'description' => sprintf( __( 'Please select a saved Post Layout template which was built using post type builder. Please create a new Post Layout template in %1$sPorto Templates Builder%2$s', 'porto-functionality' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=' . PortoBuilders::BUILDER_SLUG . '&' . PortoBuilders::BUILDER_TAXONOMY_SLUG . '=type' ) ) . '">', '</a>' ),
								'admin_label' => true,
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Count (per page)', 'porto-functionality' ),
								'description' => __( 'Leave blank if you use default value.', 'porto-functionality' ),
								'param_name'  => 'count',
								'admin_label' => true,
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Order by', 'porto-functionality' ),
								'param_name'  => 'orderby_term',
								'value'       => array(
									__( 'Default', 'porto-functionality' ) => '',
									__( 'Title', 'porto-functionality' ) => 'name',
									__( 'ID', 'porto-functionality' ) => 'term_id',
									__( 'Post Count', 'porto-functionality' ) => 'count',
									__( 'None', 'porto-functionality' ) => 'none',
									__( 'Parent', 'porto-functionality' ) => 'parent',
									__( 'Description', 'porto-functionality' ) => 'description',
									__( 'Term Group', 'porto-functionality' ) => 'term_group',
								),
								'std'         => '',
								/* translators: %s: Wordpres codex page */
								'description' => sprintf( __( 'Select how to sort retrieved posts. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
								'dependency'  => array(
									'element' => 'source',
									'value'   => array( 'terms' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Order way', 'porto-functionality' ),
								'param_name'  => 'order',
								'value'       => porto_vc_woo_order_way(),
								/* translators: %s: Wordpres codex page */
								'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
							),
							array(
								'type'       => 'porto_param_heading',
								'param_name' => 'posts_layout',
								'text'       => __( 'Posts Layout', 'porto-functionality' ),
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'View mode', 'porto-functionality' ),
								'param_name'  => 'view',
								'value'       => array(
									__( 'Grid', 'porto-functionality' ) => '',
									__( 'Grid - Creative', 'porto-functionality' ) => 'creative',
									__( 'Masonry', 'porto-functionality' ) => 'masonry',
									__( 'Slider', 'porto-functionality' ) => 'slider',
								),
								'admin_label' => true,
							),
							array(
								'type'       => 'porto_image_select',
								'heading'    => __( 'Grid Layout', 'porto-functionality' ),
								'param_name' => 'grid_layout',
								'dependency' => array(
									'element' => 'view',
									'value'   => array( 'creative' ),
								),
								'std'        => '1',
								'value'      => porto_sh_commons( 'masonry_layouts' ),
							),
							array(
								'type'       => 'number',
								'heading'    => __( 'Grid Height (px)', 'porto-functionality' ),
								'param_name' => 'grid_height',
								'dependency' => array(
									'element' => 'view',
									'value'   => array( 'creative' ),
								),
								'suffix'     => 'px',
								'std'        => 600,
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Column Spacing (px)', 'porto-functionality' ),
								'description' => __( 'Leave blank if you use theme default value.', 'porto-functionality' ),
								'param_name'  => 'spacing',
								'suffix'      => 'px',
								'std'         => '',
								'selectors'   => array(
									'{{WRAPPER}}' => '--porto-el-spacing: {{VALUE}}px;',
								),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Columns', 'porto-functionality' ),
								'param_name' => 'columns',
								'std'        => '4',
								'value'      => porto_sh_commons( 'products_columns' ),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Columns on tablet ( <= 991px )', 'porto-functionality' ),
								'param_name' => 'columns_tablet',
								'std'        => '',
								'value'      => array(
									__( 'Default', 'porto-functionality' ) => '',
									'1' => '1',
									'2' => '2',
									'3' => '3',
									'4' => '4',
								),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Columns on mobile ( <= 575px )', 'porto-functionality' ),
								'param_name' => 'columns_mobile',
								'std'        => '',
								'value'      => array(
									__( 'Default', 'porto-functionality' ) => '',
									'1' => '1',
									'2' => '2',
									'3' => '3',
								),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Pagination Type', 'porto-functionality' ),
								'param_name' => 'pagination_style',
								'value'      => array(
									__( 'Default', 'porto-functionality' ) => '',
									__( 'Ajax Pagination', 'porto-functionality' ) => 'ajax',
									__( 'Infinite Scroll', 'porto-functionality' ) => 'infinite',
									__( 'Load more', 'porto-functionality' )       => 'load_more',
									__( 'None', 'porto-functionality' ) => 'none',
								),
								'dependency' => array(
									'element' => 'source',
									'value'   => array( '' ),
								),
							),
							array(
								'type'        => 'checkbox',
								'heading'     => __( 'Filter By Taxonomy', 'porto-functionality' ),
								'param_name'  => 'category_filter',
								'std'         => '',
								'admin_label' => true,
								'dependency'  => array(
									'element' => 'source',
									'value'   => array( '' ),
								),
							),
							array(
								'param_name'  => 'filter_cat_tax',
								'type'        => 'dropdown',
								'heading'     => __( 'Taxonomy', 'porto-functionality' ),
								'description' => __( 'Please select a post taxonomy to be used as category filter.', 'porto-functionality' ),
								'value'       => array_merge(
									array( __( 'Default', 'porto-functionality' ) => '' ),
									array_flip( $taxes )
								),
								'label_block' => true,
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Image Size', 'porto-functionality' ),
								'param_name' => 'image_size',
								'value'      => porto_sh_commons( 'image_sizes' ),
								'std'        => '',
								'dependency' => array(
									'element'            => 'view',
									'value_not_equal_to' => 'creative',
								),
							),
							porto_vc_custom_class(),

							array(
								'type'       => 'porto_param_heading',
								'param_name' => 'p_style',
								'text'       => __( 'Pagination Style', 'porto-functionality' ),
								'dependency' => array(
									'element' => 'pagination_style',
									'value'   => array( '', 'ajax' ),
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Horizontal Align', 'porto-functionality' ),
								'param_name' => 'p_align',
								'value'      => array(
									__( 'Default', 'porto-functionality' ) => '',
									__( 'Left', 'porto-functionality' ) => 'flex-start',
									__( 'Center', 'porto-functionality' ) => 'center',
									__( 'Right', 'porto-functionality' ) => 'flex-end',
								),
								'dependency' => array(
									'element' => 'pagination_style',
									'value'   => array( '', 'ajax' ),
								),
								'selectors'  => array(
									'{{WRAPPER}} .pagination' => 'justify-content: {{VALUE}};',
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'       => 'porto_dimension',
								'heading'    => __( 'Set custom margin of pagination part.', 'porto-functionality' ),
								'param_name' => 'p_margin',
								'value'      => '',
								'dependency' => array(
									'element' => 'pagination_style',
									'value'   => array( '', 'ajax' ),
								),
								'selectors'  => array(
									'{{WRAPPER}} .pagination-wrap' => 'margin-top: {{TOP}};margin-right: {{RIGHT}};margin-bottom: {{BOTTOM}};margin-left: {{LEFT}};',
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),

							array(
								'type'       => 'porto_param_heading',
								'param_name' => 'lm_style',
								'text'       => __( 'Load More Button Style', 'porto-functionality' ),
								'dependency' => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Width', 'porto-functionality' ),
								'param_name' => 'lm_width',
								'value'      => array(
									'100%' => '',
									'auto' => 'auto',
								),
								'dependency' => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'  => array(
									'{{WRAPPER}} .load-more .next' => 'width: {{VALUE}};',
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'       => 'porto_typography',
								'heading'    => __( 'Typography', 'porto-functionality' ),
								'param_name' => 'lm_typography',
								'dependency' => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'  => array(
									'{{WRAPPER}} .load-more .next',
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'porto_dimension',
								'heading'     => __( 'Padding', 'porto-functionality' ),
								'description' => __( 'Controls padding value of button.', 'porto-functionality' ),
								'param_name'  => 'lm_padding',
								'value'       => '',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .load-more .next' => 'padding-top: {{TOP}};padding-right: {{RIGHT}};padding-bottom: {{BOTTOM}};padding-left: {{LEFT}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Spacing (px)', 'porto-functionality' ),
								'description' => __( 'Controls the spacing of load more button.', 'porto-functionality' ),
								'param_name'  => 'lm_spacing',
								'suffix'      => 'px',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .pagination-wrap' => 'margin-top: {{VALUE}}px;',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Color', 'porto-functionality' ),
								'description' => __( 'Controls the color of the button.', 'porto-functionality' ),
								'param_name'  => 'lm_color',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .load-more .next' => 'color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Hover Color', 'porto-functionality' ),
								'description' => __( 'Controls the hover color of the button.', 'porto-functionality' ),
								'param_name'  => 'lm_color_hover',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .load-more .next:hover' => 'color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Background Color', 'porto-functionality' ),
								'description' => __( 'Controls the background color of the button.', 'porto-functionality' ),
								'param_name'  => 'lm_back_color',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .load-more .next' => 'background-color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Hover Background Color', 'porto-functionality' ),
								'description' => __( 'Controls the hover background color of the button.', 'porto-functionality' ),
								'param_name'  => 'lm_back_color_hover',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .load-more .next:hover' => 'background-color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Border Color', 'porto-functionality' ),
								'description' => __( 'Controls the border color of the button.', 'porto-functionality' ),
								'param_name'  => 'lm_border_color',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .load-more .next' => 'border-color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Hover Border Color', 'porto-functionality' ),
								'description' => __( 'Controls the hover border color of the button.', 'porto-functionality' ),
								'param_name'  => 'lm_border_color_hover',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .load-more .next:hover' => 'border-color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),

							array(
								'type'       => 'porto_param_heading',
								'param_name' => 'filter_style',
								'text'       => __( 'Filters Style', 'porto-functionality' ),
								'dependency' => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Alignment', 'porto-functionality' ),
								'param_name'  => 'filter_align',
								'description' => __( 'Controls filters\'s alignment. Choose from Left, Center, Right.', 'porto-functionality' ),
								'value'       => array(
									__( 'Default', 'porto-functionality' ) => '',
									__( 'Left', 'porto-functionality' ) => 'flex-start',
									__( 'Center', 'porto-functionality' ) => 'center',
									__( 'Right', 'porto-functionality' ) => 'flex-end',
								),
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .sort-source' => 'justify-content: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Between Spacing (px)', 'porto-functionality' ),
								'description' => __( 'Controls the spacing between filters.', 'porto-functionality' ),
								'param_name'  => 'filter_between_spacing',
								'suffix'      => 'px',
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .sort-source li' => "margin-{$right}: {{VALUE}}px;",
									'{{WRAPPER}} .sort-source li' => 'margin-bottom: {{VALUE}}px;',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Bottom Spacing (px)', 'porto-functionality' ),
								'description' => __( 'Controls the spacing of the filters.', 'porto-functionality' ),
								'param_name'  => 'filter_spacing',
								'suffix'      => 'px',
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .sort-source' => 'margin-bottom: {{VALUE}}px;',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'       => 'porto_typography',
								'heading'    => __( 'Typography', 'porto-functionality' ),
								'param_name' => 'filter_typography',
								'dependency' => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'  => array(
									'{{WRAPPER}} .sort-source a',
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Item Background Color', 'porto-functionality' ),
								'description' => __( 'Controls the item\'s background color.', 'porto-functionality' ),
								'param_name'  => 'filter_normal_bgc',
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .sort-source a' => 'background-color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Item Color', 'porto-functionality' ),
								'description' => __( 'Controls the item\'s color.', 'porto-functionality' ),
								'param_name'  => 'filter_normal_color',
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .sort-source a' => 'color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Item Active Background Color', 'porto-functionality' ),
								'description' => __( 'Controls the item\'s active and hover background color.', 'porto-functionality' ),
								'param_name'  => 'filter_active_bgc',
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .sort-source li.active > a, {{WRAPPER}} .sort-source a:hover, {{WRAPPER}} .sort-source a:focus' => 'background-color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Item Active Color', 'porto-functionality' ),
								'description' => __( 'Controls the item\'s active and hover color.', 'porto-functionality' ),
								'param_name'  => 'filter_active_color',
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .sort-source li.active > a, {{WRAPPER}} .sort-source a:hover, {{WRAPPER}} .sort-source a:focus' => 'color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'textarea',
								'heading'     => __( 'Nothing Found Message', 'porto-functionality' ),
								'param_name'  => 'post_found_nothing',
								'value'       => __( 'It seems we can\'t find what you\'re looking for.', 'porto-functionality' ),
								'admin_label' => true,
								'group'       => __( 'Advanced', 'porto-functionality' ),
							),
							array(
								'type'       => 'porto_typography',
								'heading'    => __( 'Typography', 'porto-functionality' ),
								'param_name' => 'nothing_msg_typography',
								'dependency' => array(
									'element'   => 'post_found_nothing',
									'not_empty' => true,
								),
								'selectors'  => array(
									'{{WRAPPER}}.nothing-found-message',
								),
								'group'      => __( 'Advanced', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Color', 'porto-functionality' ),
								'description' => __( 'Controls the color of message.', 'porto-functionality' ),
								'param_name'  => 'nothing_msg_color',
								'dependency'  => array(
									'element'   => 'post_found_nothing',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}}.nothing-found-message' => 'color: {{VALUE}};',
								),
								'group'       => __( 'Advanced', 'porto-functionality' ),
							),
						),
						porto_vc_product_slider_fields( 'slider' )
					),
				)
			);
		}

		/**
		 * Add archive preview controls for elementor
		 */
		public function register_elementor_preview_controls( $document ) {
			if ( ! $document instanceof Elementor\Core\DocumentTypes\PageBase && ! $document instanceof Elementor\Modules\Library\Documents\Page ) {
				return;
			}

			// Add Template Builder Controls
			$id = (int) $document->get_main_id();
			if ( $id && 'archive' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {

				$_post_types = get_post_types(
					array(
						'public'            => true,
						'show_in_nav_menus' => true,
					),
					'objects'
				);
				$post_types  = array();
				foreach ( $_post_types as $post_type => $object ) {
					if ( ! in_array( $post_type, array( 'page', 'product' ) ) ) {
						$post_types[ $post_type ] = sprintf( esc_html__( '%s Archive', 'porto-functionality' ), $object->labels->singular_name );
					}
				}

				$document->start_controls_section(
					'archive_preview_settings',
					array(
						'label' => esc_html__( 'Preview Settings', 'porto-functionality' ),
						'tab'   => Controls_Manager::TAB_SETTINGS,
					)
				);

					$document->add_control(
						'archive_preview_type',
						array(
							'label'       => esc_html__( 'Preview Dynamic Content as', 'porto-functionality' ),
							'label_block' => true,
							'type'        => Controls_Manager::SELECT,
							'default'     => 'post',
							'groups'      => array(
								'archive' => array(
									'label'   => esc_html__( 'Archive', 'porto-functionality' ),
									'options' => $post_types,
								),
							),
							'export'      => false,
						)
					);

					$document->add_control(
						'archive_preview_apply',
						array(
							'type'        => Controls_Manager::BUTTON,
							'label'       => esc_html__( 'Apply & Preview', 'porto-functionality' ),
							'label_block' => true,
							'show_label'  => false,
							'text'        => esc_html__( 'Apply & Preview', 'porto-functionality' ),
							'separator'   => 'none',
						)
					);

					$document->end_controls_section();
			}
		}

		/**
		 * Find the registered post type
		 *
		 * @since 2.3.0
		 */
		public function find_preview() {
			if ( $this->preview_mode ) {
				return;
			}
			if ( ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' == $_REQUEST['action'] ) ||
			( isset( $_REQUEST['post'] ) && PortoBuilders::BUILDER_SLUG == get_post_type( (int) $_REQUEST['post'] ) ) || ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
				$post_id = 0;

				if ( ! empty( $_REQUEST['post'] ) ) {
					$post_id = (int) $_REQUEST['post'];
				}

				if ( ! empty( $_REQUEST['post_id'] ) ) {
					$post_id = (int) $_REQUEST['post_id'];
				}
				if ( ! $post_id ) {
					$post_id = get_the_ID();
				}
				if ( 'archive' != get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
					return;
				}

				$edit_post            = get_post_meta( $post_id, 'preview_id', true );
				$this->edit_post      = $edit_post ? $edit_post : 'post';
				$this->edit_post_type = $this->edit_post;
				$this->preview_mode   = true;
			}
		}

		/**
		 * Apply preview mode in ajax
		 */
		public function apply_preview_el_post() {
			check_ajax_referer( 'porto-elementor-nonce', 'nonce' );
			update_post_meta( (int) $_REQUEST['post_id'], 'preview_id', sanitize_title( $_REQUEST['mode'] ) );
			die;
		}

		/**
		 * Apply preview mode in ajax - WP Bakery
		 *
		 * @since 2.3.0
		 */
		public function apply_preview_wpb_post() {
			check_ajax_referer( 'porto-admin-nonce', 'nonce' );
			update_post_meta( (int) $_REQUEST['post_id'], 'preview_id', sanitize_title( $_REQUEST['mode'] ) );
			die;
		}

		/**
		 * Archive Posts Grid Shortcode
		 *
		 * @since 2.3.0
		 */
		public function shortcode_archive_posts_grid( $atts ) {

			if ( ! $this->preview_mode ) {
				$this->find_preview();
			}
			if ( empty( $atts ) ) {
				$atts = array();
			}
			if ( $this->preview_mode ) {
				if ( ! porto_is_archive() && ! ( is_singular( PortoBuilders::BUILDER_SLUG ) || ( isset( $_REQUEST['context'] ) && 'edit' == $_REQUEST['context'] ) || ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' == $_REQUEST['action'] ) || ( wp_doing_ajax() && empty( $_GET['vc_editable'] ) ) ) ) {
					return null;
				}
				$atts['post_type'] = $this->edit_post;
			} else {
				global $wp_query;
				$post_type = isset( $wp_query->query_vars ) && ! empty( $wp_query->query_vars['post_type'] ) ? $wp_query->query_vars['post_type'] : '';
				if ( ! $post_type ) {
					$post_types_exclude   = apply_filters( 'porto_condition_exclude_post_types', array( PortoBuilders::BUILDER_SLUG, 'attachment', 'elementor_library', 'page' ) );
					$available_post_types = get_post_types( array( 'public' => true ) );
					foreach ( $available_post_types as $p_type ) {
						if ( ! in_array( $p_type, $post_types_exclude ) && ( $wp_query->is_post_type_archive( $p_type ) || $wp_query->is_tax( get_object_taxonomies( $p_type ) ) ) ) {
							$post_type = $p_type;
							break;
						}
					}
				}
				if ( ! $post_type ) {
					$post_type = 'post';
				}
				$atts['post_type']      = $post_type;
				$atts['shortcode_type'] = 'archive';
			}

			if ( $template = porto_shortcode_template( 'porto_posts_grid' ) ) {

				$internal_css = '';
				if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
					// Shortcode class
					$shortcode_class = ' wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
						$atts,
						'porto_ab_posts_grid',
						array(
							array(
								'param_name' => 'spacing',
								'selectors'  => true,
							),
							array(
								'param_name' => 'p_align',
								'selectors'  => true,
							),
							array(
								'param_name' => 'p_margin',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_width',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_typography',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_padding',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_spacing',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_back_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_border_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_color_hover',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_back_color_hover',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_border_color_hover',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_align',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_between_spacing',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_spacing',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_typography',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_normal_bgc',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_normal_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_active_bgc',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_active_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nothing_msg_typography',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nothing_msg_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_pos_top',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_pos_bottom',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_pos_left',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_pos_right',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_br_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_abr_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_bg_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_abg_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_fs',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_width',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_height',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_br',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_h_pos',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_v_pos',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_h_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_bg_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_h_bg_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_br_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_h_br_color',
								'selectors'  => true,
							),
						)
					);
					if ( isset( $_REQUEST['vc_editable'] ) && ( true == $_REQUEST['vc_editable'] ) ) {
						$style_array = apply_filters( 'porto_shortcode_render_internal_css', 'porto_ab_posts_grid', $atts );
						if ( is_array( $style_array ) ) {
							foreach ( $style_array as $key => $value ) {
								if ( 'responsive' == $key ) {
									$internal_css .= $value;
								} else {
									$internal_css .= $key . '{' . $value . '}';
								}
							}
						}
					}
				} elseif ( defined( 'ELEMENTOR_VERSION' ) ) {
					if ( empty( $atts['spacing'] ) ) {
						$atts['spacing'] = '';
					}
					if ( ! empty( $atts['count'] ) && is_array( $atts['count'] ) ) {
						if ( isset( $atts['count']['size'] ) ) {
							$atts['count'] = $atts['count']['size'];
						}
					}
				}
				ob_start();
				include $template;
				$result = ob_get_clean();
				if ( $result && $internal_css ) {
					$first_tag_index = strpos( $result, '>' );
					if ( $first_tag_index ) {
						$internal_css = porto_filter_inline_css( $internal_css, false );
						if ( $internal_css ) {
							$result = substr( $result, 0, $first_tag_index + 1 ) . '<style>' . wp_strip_all_tags( $internal_css ) . '</style>' . substr( $result, $first_tag_index + 1 );
						}
					}
				}
				return $result;
			}
		}
	}
endif;

PortoBuildersArchive::get_instance();

<?php

/*----------------------------------------------------------------------------*\
	WC CAROUSEL PRODUCTS SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_WC_Carousel_Products' ) ) {
	class MPC_WC_Carousel_Products {
		public $shortcode  = 'mpc_wc_carousel_products';
		public $parts = array();
		private $query;
		private $posts = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( $this->shortcode, array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			/* Autocomplete */
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_ids_callback', 'MPC_Autocompleter::suggest_wc_product', 10, 1 );
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_ids_render', 'MPC_Autocompleter::render_wc_product', 10, 1 );
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_taxonomies_callback', 'MPC_Autocompleter::suggest_wc_category', 10, 1 );
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_taxonomies_render', 'MPC_Autocompleter::render_wc_category', 10, 1 );

			$this->parts = array(
				'flex_begin'    => '<div class="mpc-flex">',
				'inline_begin'  => '<div class="mpc-inline-box">',
				'block_begin'   => '<div class="mpc-block-box">',
				'buttons_begin' => '<div class="mpc-thumb__buttons mpc-transition">',
				'thumb_begin'   => '<div class="mpc-thumb__content-wrap">',

				'add_to_cart'   => '',
				'title'         => '',
				'thumbnail'     => '',
				'rating'        => '',
				'buttons'       => '',

				'section_end'   => '</div>',
			);
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( $this->shortcode . '-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/css/' . $this->shortcode .  '.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( $this->shortcode . '-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/js/' . $this->shortcode .  MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		function set_query( $query ) {
			if( !is_wp_error( $query ) ) {
				$this->query = $query;
				$this->posts = $query->posts;
			}
		}

		/* Build query */
		function build_query( $atts ) {
			$args = array(
				'post_status' => 'publish',
				'ignore_sticky_posts' => true,
				'post_type' => 'product',
				'orderby' => $atts[ 'orderby' ],
				'order' => $atts[ 'order' ],
				'posts_per_page' => -1,
			);

			if( $atts[ 'source_type' ] != 'ids' ) {
				$args[ 'posts_per_page' ] = (int) $atts[ 'items_number' ];

				if( $atts[ 'taxonomies' ] != '' ) {
					$tax_types = get_taxonomies( array( 'public' => true ) );

					$terms = get_terms( array_keys( $tax_types ), array(
						'hide_empty' => false,
						'include' => $atts[ 'taxonomies' ],
					) );

					$args[ 'tax_query' ] = array();

					$tax_queries = array();
					foreach ( $terms as $t ) {
						if ( ! isset( $tax_queries[ $t->taxonomy ] ) ) {
							$tax_queries[ $t->taxonomy ] = array(
								'taxonomy' => $t->taxonomy,
								'field' => 'id',
								'terms' => array( $t->term_id ),
								'relation' => 'IN'
							);
						} else {
							$tax_queries[ $t->taxonomy ]['terms'][] = $t->term_id;
						}
					}

					$args['tax_query'] = array_values( $tax_queries );
					$args['tax_query']['relation'] = 'OR';
				}
			}

			if( $atts[ 'source_type' ] == 'ids' ) {
				if( $atts[ 'ids' ] != '' ) {
					$args[ 'post_type' ] = array( 'product', 'product_variation' );
					$args[ 'post__in' ]  = explode( ', ', $atts[ 'ids' ] );
				}
			}

			if ( $atts['exclude_current'] === 'true' ) {
				global $product;

				if ( is_object( $product ) ) {
					$args['post__not_in'] = array( $product->get_id() );
				}
			}

			return $args;
		}

		/* Get Posts */
		function get_posts( $atts ) {
			if( $this->query === null || empty( $this->posts ) ) {
				$atts = $this->build_query( $atts );

				$this->query = new WP_Query( $atts );
				$this->posts = $this->query->get_posts();
			} else if( is_object( $this->query ) && empty( $this->posts ) && isset( $this->query->posts ) ) {
				$this->posts = $this->query->posts;
			}

			return $this->query;
		}

		/* Reset */
		function reset() {
			$this->query = null;
			$this->posts = array();
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			if( !class_exists( 'WooCommerce' ) ) {
				return '';
			}

			/* Enqueues */
			wp_enqueue_style( 'mpc-massive-slick-css', mpc_get_plugin_path( __FILE__ ) . '/assets/css/libs/slick.min.css' );
			wp_enqueue_script( 'mpc-massive-slick-js', mpc_get_plugin_path( __FILE__ ) . '/assets/js/libs/slick.min.js', array( 'jquery' ), '', true );

			global $MPC_Navigation, $MPC_WC_Product, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$slider_atts = shortcode_atts( array(
				'class'                     => '',
				'carousel_preset'           => '',
				'rows'                      => '1',
				'cols'                      => '4',
				'gap'                       => '0',
				'stretched'                 => '',
				'start_at'                  => 1,
				'single_scroll'             => '',
				'auto_slide'                => '',
				'delay'                     => '1000',
				'loop'                      => '',

				'source_type'               => 'taxonomies',
				'ids'                       => '',
				'taxonomies'                => '',
				'orderby'                   => 'date',
				'order'                     => 'ASC',
				'items_number'              => '6',
				'exclude_current'           => 'true',

				'animation_in_type'          => 'none',
				'animation_in_duration'      => '300',
				'animation_in_delay'         => '0',
				'animation_in_offset'        => '100',

				'mpc_navigation__preset'    => '',
			), $atts );

			/* Build Query */
			$this->get_posts( $slider_atts );
			if( empty( $this->posts ) ) return '<p class="mpc-noposts">' . __( 'No related products.', 'mpc' ) . '</p>';

			/* Prepare */
			$styles    = $this->shortcode_styles( $slider_atts );
			$css_id    = $styles[ 'id' ];

			$animation = MPC_Parser::animation( $slider_atts );

			/* Get Posts */
			$css_settings = array(
				'id' => $css_id,
				'selector' => '.mpc-wc-carousel-products[id="' . $css_id . '"] .mpc-wc-product'
			);

			/* Generate markup & template */
			$MPC_WC_Product->reset();
			$content = '';
			foreach( $this->posts as $single ) {
				$MPC_WC_Product->set_post( $single );
				$content .= $MPC_WC_Product->shortcode_template( $atts, null, null, $css_settings );
			}

			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $slider_atts[ 'stretched' ] != '' ? ' mpc-carousel--stretched' : '';
			$classes .= $MPC_WC_Product->classes;
			$classes .= ' ' . esc_attr( $slider_atts[ 'class' ] );

			$attributes = $animation;
			$attributes .= MPC_Parser::carousel( $slider_atts );
			$attributes .= $MPC_WC_Product->attributes;

			$return = '<div class="mpc-carousel__wrapper mpc-waypoint">';

				$carousel = '<div id="' . $css_id . '" class="mpc-wc-carousel-products' . $classes . '" ' . $attributes . '>';
					$carousel .= $content;
				$carousel  .= '</div>';

			$return .= $MPC_Navigation->shortcode_template( $slider_atts[ 'mpc_navigation__preset' ], '', $css_id, 'image', $carousel );

			$return .= '</div>';

			wp_reset_postdata();
			$MPC_WC_Product->reset();
			$this->reset();

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$frontend = $styles[ 'css' ];
				$frontend .= $MPC_WC_Product->style != '' ? $MPC_WC_Product->style : '';

				$return .= '<style>' . $frontend . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( $this->shortcode . '-' . rand( 1, 100 ) );
			$style  = '';

			// Gap
			if ( $styles[ 'gap' ] && $styles[ 'gap' ] != '0' ) {
				$style .= '.mpc-wc-carousel-products[id="' . $css_id . '"] {';
					$style .= 'margin-left: -' . floor( $styles[ 'gap' ] * 0.5 ) . 'px;';
					$style .= 'margin-right: -' . floor( $styles[ 'gap' ] * 0.5 ) . 'px;';
				$style .= '}';

				$style .= '.mpc-wc-carousel-products[id="' . $css_id . '"] .mpc-wc-product {';
					$style .= 'padding-left: ' . floor( $styles[ 'gap' ] * 0.5 ) . 'px;';
					$style .= 'padding-right: ' . floor( $styles[ 'gap' ] * 0.5 ) . 'px;';
					$style .= 'margin-bottom: ' . $styles[ 'gap' ] . 'px;';
				$style .= '}';
			}

			$mpc_massive_styles .= $style;

			return array(
				'id'  => $css_id,
				'css' => $style,
			);
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return '';
			}

			$base = array(
				array(
					'type'        => 'mpc_preset',
					'heading'     => __( 'Main Preset', 'mpc' ),
					'param_name'  => 'carousel_preset',
					'tooltip'     => MPC_Helper::style_presets_desc(),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'wide_modal'  => true,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Single Scroll', 'mpc' ),
					'param_name'       => 'single_scroll',
					'tooltip'          => __( 'Check to enable single item scroll. Navigating through carousel will jump by only one item at a time. Leave unchecked to scroll by all visible items.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Loop', 'mpc' ),
					'param_name'       => 'loop',
					'tooltip'          => __( 'Check to enable loop. Enabling loop will change the carousel to infinite scroll.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Slide Show', 'mpc' ),
					'param_name'       => 'auto_slide',
					'tooltip'          => __( 'Check to enable slide show. Carousel will auto slide once the slide show delay pass.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Slide Show Delay', 'mpc' ),
					'param_name'       => 'delay',
					'tooltip'          => __( 'Specify delay between slides.', 'mpc' ),
					'min'              => 500,
					'max'              => 15000,
					'step'             => 50,
					'value'            => 1000,
					'unit'             => 'ms',
					'dependency'       => array( 'element' => 'auto_slide', 'value' => 'true', ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			$base_ext = array(
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Gap', 'mpc' ),
					'param_name'       => 'gap',
					'tooltip'          => __( 'Choose gap between slides.', 'mpc' ),
					'min'              => 0,
					'max'              => 50,
					'step'             => 1,
					'value'            => 0,
					'unit'             => 'px',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Stretch', 'mpc' ),
					'param_name'       => 'stretched',
					'tooltip'          => __( 'Check to enable slider stretch. Enabling stretch will display parts of previous and next items on carousel sides.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Start At', 'mpc' ),
					'param_name'       => 'start_at',
					'tooltip'          => __( 'Define first displayed slide index.', 'mpc' ),
					'value'            => '',
					'std'              => 1,
					'label'            => '',
					'validate'         => true,
					'addon'            => array(
						'icon'  => 'dashicons-images-alt',
						'align' => 'prepend'
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
			);

			$source = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Source', 'mpc' ),
					'param_name' => 'source_section_divider',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Data source', 'mpc' ),
					'param_name'       => 'source_type',
					'tooltip'          => __( 'Select source type for carousel.', 'mpc' ),
					'value'            => array(
						__( 'Products Ids', 'mpc' ) => 'ids',
						__( 'Products Category', 'mpc' ) => 'taxonomies',
					),
					'std'              => 'taxonomies',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'autocomplete',
					'heading'          => __( 'Posts', 'mpc' ),
					'param_name'       => 'ids',
					'tooltip'          => __( 'Define list of products displayed by this carousel.', 'mpc' ),
					'settings'         => array(
						'multiple'      => true,
						'sortable'      => true,
						'unique_values' => true,
					),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'source_type', 'value' => array( 'ids' ), ),
				),
				array(
					'type'               => 'autocomplete',
					'heading'            => __( 'Taxonomies', 'mpc' ),
					'param_name'         => 'taxonomies',
					'tooltip'            => __( 'Define WooCommerce categories.', 'mpc' ),
					'settings'           => array(
						'multiple'       => true,
						'min_length'     => 1,
						'groups'         => true,
						'unique_values'  => true,
						'display_inline' => true,
						'delay'          => 500,
						'auto_focus'     => true,
					),
					'std'                => '',
					'param_holder_class' => 'vc_not-for-custom',
					'edit_field_class'   => 'vc_col-sm-6 vc_column',
					'dependency'         => array( 'element' => 'source_type', 'value' => array( 'taxonomies' ), ),
				),
				array(
					'type'               => 'dropdown',
					'heading'            => __( 'Sort by', 'mpc' ),
					'param_name'         => 'orderby',
					'tooltip'            => __( 'Select posts sorting parameter.', 'mpc' ),
					'value'              => array(
						__( 'Date', 'mpc' )               => 'date',
						__( 'Order by post ID', 'mpc' )   => 'ID',
						__( 'Author', 'mpc' )             => 'author',
						__( 'Title', 'mpc' )              => 'title',
						__( 'Last modified date', 'mpc' ) => 'modified',
						__( 'Number of comments', 'mpc' ) => 'comment_count',
						__( 'Random order', 'mpc' )       => 'rand',
					),
					'std'                => 'date',
					'edit_field_class'   => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'         => array(
						'element'            => 'source_type',
						'value_not_equal_to' => array( 'ids' ),
					),
				),
				array(
					'type'               => 'dropdown',
					'heading'            => __( 'Order', 'mpc' ),
					'param_name'         => 'order',
					'tooltip'            => __( 'Select products sorting order.', 'mpc' ),
//					'group'              => __( 'Source', 'mpc' ),
					'value'              => array(
						__( 'Descending', 'mpc' ) => 'DESC',
						__( 'Ascending', 'mpc' )  => 'ASC',
					),
					'std'                => 'ASC',
					'edit_field_class'   => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'         => array(
						'element'            => 'source_type',
						'value_not_equal_to' => array( 'ids' ),
					),
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Max Items Number', 'mpc' ),
					'param_name'       => 'items_number',
					'tooltip'          => __( 'Define maximum number of displayed products. If the number of products meeting the above parameters is smaller it will only show those products.', 'mpc' ),
					'value'            => '6',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-slides',
						'align' => 'prepend',
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'label'            => '',
					'validate'         => true,
					'dependency'       => array(
						'element'            => 'source_type',
						'value_not_equal_to' => array( 'ids' ),
					),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Exclude Current Post', 'mpc' ),
					'param_name'       => 'exclude_current',
					'tooltip'          => __( 'Check to exclude current post/page/product. This option comes handy while presenting related posts.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => 'true',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'       => array(
						'element'            => 'source_type',
						'value_not_equal_to' => array( 'ids' ),
					),
				),
			);

			/* General */
			$rows_cols = MPC_Snippets::vc_rows_cols( array( 'cols' => array( 'min' => 1, 'max' => 8, 'default' => 2 ) ) );
			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			/* Navigation */
			$integrate_navigation = vc_map_integrate_shortcode( 'mpc_navigation', 'mpc_navigation__', __( 'Navigation', 'mpc' ) );

			/* Integrate Item */
			$item_exclude   = array( 'exclude_regex' => '/animation_in(.*)|source_section_divider|^id$/', );
			$integrate_item = vc_map_integrate_shortcode( 'mpc_wc_product', '', '', $item_exclude );

			$params = array_merge(
				$base,
				$rows_cols,
				$base_ext,

				$source,

				$integrate_item,

				$integrate_navigation,
				$animation,
				$class
			);

			if( !class_exists( 'WooCommerce' ) ) {
				$params = array(
					array(
						'type'             => 'custom_markup',
						'param_name'       => 'woocommerce_notice',
						'value'            => '<p class="mpc-warning mpc-active"><i class="dashicons dashicons-warning"></i>' . __( 'Please install and activate <a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a> plugin in order to use this shortcode! :)', 'mpc' ) . '</p>',
						'edit_field_class' => 'vc_col-sm-12 vc_column mpc-woocommerce-field',
					),
				);
			}

			return array(
				'name'        => __( 'Carousel Products', 'mpc' ),
				'description' => __( 'Carousel with products', 'mpc' ),
				'base'        => $this->shortcode,
				'icon'        => 'mpc-shicon-wc-carousel-products',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_WC_Carousel_Products' ) ) {
	global $MPC_WC_Carousel_Products;
	$MPC_WC_Carousel_Products = new MPC_WC_Carousel_Products;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_wc_carousel_products' ) ) {
	class WPBakeryShortCode_mpc_wc_carousel_products extends MPCShortCode_Base {}
}

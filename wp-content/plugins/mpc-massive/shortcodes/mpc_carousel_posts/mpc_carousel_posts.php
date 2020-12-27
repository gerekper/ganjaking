<?php
/*----------------------------------------------------------------------------*\
	CAROUSEL POSTS SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Carousel_Posts' ) ) {
	class MPC_Carousel_Posts {
		public $shortcode  = 'mpc_carousel_posts';
		public $post_types = array();
		public $parts = array();
		private $query;
		private $posts = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_carousel_posts', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			/* Autocomplete */
			add_filter( 'vc_autocomplete_mpc_carousel_posts_ids_callback', 'vc_include_field_search', 10, 1 );
			add_filter( 'vc_autocomplete_mpc_carousel_posts_ids_render', 'vc_include_field_render', 10, 1 );
			add_filter( 'vc_autocomplete_mpc_carousel_posts_taxonomies_callback', 'vc_autocomplete_taxonomies_field_search', 10, 1 );
			add_filter( 'vc_autocomplete_mpc_carousel_posts_taxonomies_render', 'vc_autocomplete_taxonomies_field_render', 10, 1 );

			$parts = array(
				'section_begin' => '',
				'section_end'   => '',
				'overlay_begin' => '',
				'overlay_end'   => '',
				'meta'          => '',
				'readmore'      => '',
				'title'         => '',
				'date'          => '',
				'author'        => '',
				'description'   => '',
				'thumbnail'     => '',
			);

			$this->parts = $parts;
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		function set_query( $query ) {
			if( !is_wp_error( $query ) ) {
				$this->query = $query;
				$this->posts = $query->posts;
			}
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_carousel_posts-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_carousel_posts/css/mpc_carousel_posts.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_carousel_posts-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_carousel_posts/js/mpc_carousel_posts' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Retrieve posts data */
		function get_posts_details() {
			if( empty( $this->post_types ) ) {
				$this->post_types = MPC_Helper::get_posts_details();
			}
		}

		/* Build query */
		function build_query( $atts ) {
			$args = array(
				'post_status' => 'publish',
				'ignore_sticky_posts' => true,
				'post_type' => $atts[ 'post_type' ] == 'ids' ? 'any' : $atts[ 'post_type' ],
				'orderby' => $atts[ 'orderby' ],
				'order' => $atts[ 'order' ],
				'posts_per_page' => -1,
			);

			if( $atts[ 'post_type' ] != 'ids' ) {
				$args[ 'posts_per_page' ] = (int) $atts[ 'items_number' ];

				if ( $atts[ 'taxonomies' ] != '' ) {
					$tax_types = get_taxonomies( array( 'public' => true ) );

					$terms = get_terms( array_keys( $tax_types ), array(
						'hide_empty' => false,
						'include'    => $atts[ 'taxonomies' ],
					) );

					if ( ! isset( $atts[ 'include_exclude_type' ] ) ) {
						$atts[ 'include_exclude_type' ] = 'include';
					}

					$args[ 'tax_query' ] = array();
					$tax_queries         = array();

					foreach ( $terms as $t ) {
						if ( ! isset( $tax_queries[ $t->taxonomy ] ) ) {
							$tax_queries[ $t->taxonomy ] = array(
								'taxonomy'         => $t->taxonomy,
								'field'            => 'term_id',
								'terms'            => array( (int) $t->term_id ),
								'operator'         => ( $atts[ 'include_exclude_type' ] === 'exclude' ) ? 'NOT IN' : 'IN',
								/* TODO: We must add option for: include_children */
								'include_children' => false
							);
						} else {
							$tax_queries[ $t->taxonomy ][ 'terms' ][] = (int) $t->term_id;
						}
					}

					$args[ 'tax_query' ] = array_values( $tax_queries );


					if ( $atts[ 'include_exclude_type' ] === 'exclude' ) {
						$args[ 'tax_query' ][ 'relation' ] = 'AND';
					} else {
						$args[ 'tax_query' ] [ 'relation' ] = 'OR';
					}
				}
			}

			if( $atts[ 'post_type' ] == 'ids' ) {
				if( $atts[ 'ids' ] != '' ) {
					$args[ 'post__in' ] = explode( ', ', $atts[ 'ids' ] );
				}
			}

			if ( $atts['exclude_current'] === 'true' ) {
				global $post;

				if ( is_object( $post ) ) {
					$args['post__not_in'] = array( $post->ID );
				}
			}

			return $args;
		}

		/* Get Posts */
		function get_posts( $atts ) {
			if( is_object( $this->query ) && !empty( $this->posts )  ) {
				return;
			}

			if( !isset( $atts[ 'main_loop' ] ) ) {
				$atts = $this->build_query( $atts );
			}

			$this->query = new WP_Query( $atts );
			$this->posts = $this->query->get_posts();
		}

		/* Reset */
		function reset() {
			global $MPC_Single_Post;

			wp_reset_query();
			$MPC_Single_Post->reset();
			$this->query = null;
			$this->posts = array();
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			/* Enqueues */
			wp_enqueue_style( 'mpc-massive-slick-css', mpc_get_plugin_path( __FILE__ ) . '/assets/css/libs/slick.min.css' );
			wp_enqueue_script( 'mpc-massive-slick-js', mpc_get_plugin_path( __FILE__ ) . '/assets/js/libs/slick.min.js', array( 'jquery' ), '', true );

			global $MPC_Navigation, $MPC_Single_Post, $mpc_ma_options;
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

				'post_type'                 => 'post',
				'ids'                       => '',
				'taxonomies'                => '',
				'orderby'                   => 'date',
				'order'                     => 'ASC',
				'items_number'              => '6',
				'include_exclude_type'      => 'include',
				'exclude_current'           => 'true',

				'mpc_navigation__preset'    => '',

				'odd_background_type'       => 'color',
				'odd_background_color'      => '',
				'odd_background_image'      => '',
				'odd_background_image_size' => 'large',
				'odd_background_repeat'     => 'no-repeat',
				'odd_background_size'       => 'initial',
				'odd_background_position'   => 'middle-center',
				'odd_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'odd_background_posts'      => '',

				'animation_in_type'          => 'none',
				'animation_in_duration'      => '300',
				'animation_in_delay'         => '0',
				'animation_in_offset'        => '100',
			), $atts );

			/* Build Query */
			$this->get_posts( $slider_atts );
			if( empty( $this->posts ) ) return '<p class="mpc-noposts">' . __( 'No related posts.', 'mpc' ) . '</p>';

			/* Prepare */
			$styles    = $this->shortcode_styles( $slider_atts );
			$css_id    = $styles[ 'id' ];

			$animation = MPC_Parser::animation( $slider_atts );
			$carousel  = MPC_Parser::carousel( $slider_atts );

			/* Get Posts */
			$css_settings = array(
				'id' => $css_id,
				'selector' => '.mpc-carousel-posts[id="' . $css_id . '"] .mpc-post'
			);

			$MPC_Single_Post->reset();
			$content = '';
			foreach( $this->posts as $single ) {
				$MPC_Single_Post->set_post( $single );
				$content .= $MPC_Single_Post->shortcode_template( $atts, null, null, $css_settings );
			}

			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $slider_atts[ 'stretched' ] != '' ? ' mpc-carousel--stretched' : '';
			$classes .= $MPC_Single_Post->classes;
			$classes .= ' ' . esc_attr( $slider_atts[ 'class' ] );

			$return = '<div class="mpc-carousel__wrapper mpc-waypoint">';

				$carousel = '<div id="' . $css_id . '" class="mpc-carousel-posts' . $classes . '" ' . $animation . ' ' . $carousel . '>';
					$carousel .= $content;
				$carousel  .= '</div>';

			$return .= $MPC_Navigation->shortcode_template( $slider_atts[ 'mpc_navigation__preset' ], '', $css_id, 'image', $carousel );

			$return .= '</div>';

			/* Restore original Post Data */
			wp_reset_postdata();
			$MPC_Single_Post->reset();
			$this->reset();

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$frontend = $styles[ 'css' ];
				$frontend .= $MPC_Single_Post->style != '' ? $MPC_Single_Post->style : '';

				$return .= '<style>' . $frontend . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_carousel_posts-' . rand( 1, 100 ) );
			$style  = '';

			// Gap
			if ( $styles[ 'gap' ] && $styles[ 'gap' ] != '0px' ) {
				$style .= '.mpc-carousel-posts[id="' . $css_id . '"] {';
					$style .= 'margin-left: -' . floor( $styles[ 'gap' ] * 0.5 ) . 'px;';
					$style .= 'margin-right: -' . floor( $styles[ 'gap' ] * 0.5 ) . 'px;';
				$style .= '}';

				$style .= '.mpc-carousel-posts[id="' . $css_id . '"] .mpc-post {';
					$style .= 'padding-left: ' . floor( $styles[ 'gap' ] * 0.5 ) . 'px;';
					$style .= 'padding-right: ' . floor( $styles[ 'gap' ] * 0.5 ) . 'px;';
					$style .= 'margin-bottom: ' . $styles[ 'gap' ] . 'px;';
				$style .= '}';
			}

			// Regular
			if ( $temp_style = MPC_CSS::background( $styles, 'odd' ) ) {
				$style .= '.mpc-carousel-posts[id="' . $css_id . '"] .mpc-post.slick-slide:nth-child(2n+1) .mpc-post__content,';
				$style .= '.mpc-carousel-posts[id="' . $css_id . '"] .slick-slide:not(.mpc-post):nth-child(2n) > div:nth-child(2n+1) .mpc-post__content,';
				$style .= '.mpc-carousel-posts[id="' . $css_id . '"] .slick-slide:not(.mpc-post):nth-child(2n+1) > div:nth-child(2n) .mpc-post__content {';
					$style .= $temp_style;
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

			$this->get_posts_details();

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
					'group'      => __( 'Source', 'mpc' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Data source', 'mpc' ),
					'param_name'       => 'post_type',
					'tooltip'          => __( 'Select post types for carousel. <b>Custom List</b> lets you select the exact list of posts you want.', 'mpc' ),
					'value'            => $this->post_types,
					'std'              => 'post',
					'group'            => __( 'Source', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'autocomplete',
					'heading'          => __( 'Posts', 'mpc' ),
					'param_name'       => 'ids',
					'tooltip'          => __( 'Define list of posts displayed by this carousel.', 'mpc' ),
					'settings'         => array(
						'multiple'      => true,
						'sortable'      => true,
						'unique_values' => true,
					),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'post_type', 'value' => array( 'ids' ), ),
					'group'            => __( 'Source', 'mpc' ),
				),
				array(
					'type'               => 'autocomplete',
					'heading'            => __( 'Taxonomies or Tags', 'mpc' ),
					'param_name'         => 'taxonomies',
					'tooltip'            => __( 'Define posts tags, categories or custom taxonomies. It will filter the posts based also on Include \ Exclude option.', 'mpc' ),
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
					'dependency'         => array( 'element' => 'post_type', 'value_not_equal_to' => array( 'ids' ), ),
					'group'              => __( 'Source', 'mpc' ),
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
					'group'              => __( 'Source', 'mpc' ),
					'edit_field_class'   => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'         => array(
						'element'            => 'post_type',
						'value_not_equal_to' => array( 'ids' ),
					),
				),
				array(
					'type'               => 'dropdown',
					'heading'            => __( 'Order', 'mpc' ),
					'param_name'         => 'order',
					'tooltip'            => __( 'Select posts sorting order.', 'mpc' ),
					'group'              => __( 'Source', 'mpc' ),
					'value'              => array(
						__( 'Descending', 'mpc' ) => 'DESC',
						__( 'Ascending', 'mpc' )  => 'ASC',
					),
					'std'                => 'ASC',
					'edit_field_class'   => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'         => array(
						'element'            => 'post_type',
						'value_not_equal_to' => array( 'ids' ),
					),
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Max Items Number', 'mpc' ),
					'param_name'       => 'items_number',
					'tooltip'          => __( 'Define maximum number of displayed posts. If the number of posts meeting the above parameters is smaller it will only show those posts.', 'mpc' ),
					'value'            => '6',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-slides',
						'align' => 'prepend',
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'label'            => '',
					'validate'         => true,
					'group'            => __( 'Source', 'mpc' ),
					'dependency'       => array(
						'element'            => 'post_type',
						'value_not_equal_to' => array( 'ids' ),
					),
				),
				array(
					'type'               => 'dropdown',
					'heading'            => __( 'Include \ Exclude taxonomies?', 'mpc' ),
					'param_name'         => 'include_exclude_type',
					'tooltip'            => __( 'Include or Exclude selected taxonomies.', 'mpc' ),
					'group'              => __( 'Source', 'mpc' ),
					'value'              => array(
						__( 'Include', 'mpc' ) => 'include',
						__( 'Exclude', 'mpc' )  => 'exclude',
					),
					'std'                => 'include',
					'edit_field_class'   => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'         => array(
						'element'            => 'post_type',
						'value_not_equal_to' => array( 'ids' ),
					),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Exclude Current Post', 'mpc' ),
					'param_name'       => 'exclude_current',
					'tooltip'          => __( 'Check to exclude current post/page/product. This option comes handy while presenting related posts.', 'mpc' ),
					'group'              => __( 'Source', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => 'true',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'         => array(
						'element'            => 'post_type',
						'value_not_equal_to' => array( 'ids' ),
					),
				),
			);

			/* General */
			$item_odd_background  = MPC_Snippets::vc_background( array( 'prefix' => 'odd', 'subtitle' => __( 'Odd Item', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );
			$rows_cols = MPC_Snippets::vc_rows_cols( array( 'cols' => array( 'min' => 1, 'max' => 8, 'default' => 2 ) ) );
			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			/* Navigation */
			$integrate_navigation = vc_map_integrate_shortcode( 'mpc_navigation', 'mpc_navigation__', __( 'Navigation', 'mpc' ) );

			/* Integrate Item */
			$item_exclude   = array( 'exclude_regex' => '/animation_(.*)|item_id/', );
			$integrate_item = vc_map_integrate_shortcode( 'mpc_single_post', '', '', $item_exclude );

			$params = array_merge(
				$base,
				$rows_cols,
				$base_ext,

				$source,

				$integrate_item,
				$item_odd_background,

				$integrate_navigation,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Carousel Posts', 'mpc' ),
				'description' => __( 'Carousel with posts', 'mpc' ),
				'base'        => 'mpc_carousel_posts',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-carousel-posts.png',
				'icon'        => 'mpc-shicon-car-posts',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Carousel_Posts' ) ) {
	global $MPC_Carousel_Posts;
	$MPC_Carousel_Posts = new MPC_Carousel_Posts;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_carousel_posts' ) ) {
	class WPBakeryShortCode_mpc_carousel_posts extends MPCShortCode_Base {}
}

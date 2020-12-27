<?php
/*----------------------------------------------------------------------------*\
	GRID POSTS SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Grid_Posts' ) ) {
	class MPC_Grid_Posts {
		public $shortcode = 'mpc_grid_posts';
		private $default_atts = array();
		private $post_types = array();
		private $posts = array();
		private $query = null;

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( $this->shortcode, array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			/* Read More button */
			add_filter( 'excerpt_more', array( $this, 'remove_excerpt_more' ), 999 );

			/* Autocomplete */
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_ids_callback', 'vc_include_field_search', 10, 1 );
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_ids_render', 'vc_include_field_render', 10, 1 );
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_taxonomies_callback', 'vc_autocomplete_taxonomies_field_search', 10, 1 );
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_taxonomies_render', 'vc_autocomplete_taxonomies_field_render', 10, 1 );

			/* Grid Atts */
			$this->default_atts = array(
				'class'                     => '',
				'layout'                    => 'style_1',
				'cols'                      => '2',
				'gap'                       => '0',

				'post_type'                 => 'post',
				'taxonomies'                => '',
				'meta_tax_separator'        => ', ',
				'ids'                       => '',
				'order'                     => 'ASC',
				'orderby'                   => 'date',
				'items_number'              => '6',
				'paged'                     => '1',
				'include_exclude_type'      => 'include',
				'odd_background_type'       => 'color',
				'odd_background_color'      => '',
				'odd_background_image'      => '',
				'odd_background_image_size' => 'large',
				'odd_background_repeat'     => 'no-repeat',
				'odd_background_size'       => 'initial',
				'odd_background_position'   => 'middle-center',
				'odd_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'mpc_pagination__preset'    => '',
			);
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( $this->shortcode . '-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/css/' . $this->shortcode . '.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( $this->shortcode . '-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/js/' . $this->shortcode . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Remove Excerpt More */
		function remove_excerpt_more() {
			return '';
		}

		/* Set Query from outside of shortcode */
		function set_query( $query ) {
			if( !is_wp_error( $query ) ) {
				$this->query = $query;
				$this->posts = $query->posts;
			}
		}

		/* Set Query from outside of shortcode */
		function get_query() {
			return $this->query;
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
				'paged' => get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : (int) $atts[ 'paged' ],
				'post_type' => $atts[ 'post_type' ] == 'ids' ? 'any' : $atts[ 'post_type' ],
				'orderby' => $atts[ 'orderby' ],
				'order' => $atts[ 'order' ],
				'posts_per_page' => -1,
			);

			/* Other args */
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

		/* Return Pagination content */
		public function get_paginated_content( $data, $atts ) {
			$this->get_posts( $data );

			if( empty( $this->posts ) ) return '<p class="mpc-noposts">' . __( 'No related posts.', 'mpc' ) . '</p>';

			/* Prepare */
			global $MPC_Single_Post;

			$MPC_Single_Post->reset();

			$content = '<div class="mpc-pagination--settings" data-current="' . esc_attr( $data[ 'paged' ] ) . '" data-pages="' . esc_attr( $this->query->max_num_pages ) . '"></div>';
			foreach( $this->posts as $single ) {
				$MPC_Single_Post->set_post( $single );
				$content .= $MPC_Single_Post->pagination_content( $atts );
			}

			/* Clear */
			wp_reset_postdata();
			$this->reset();

			return $content;
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			/* Enqueues */
			wp_enqueue_script( 'mpc-massive-isotope-js' );

			global $MPC_Pagination, $MPC_Single_Post, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$grid_atts = shortcode_atts( $this->default_atts, $atts );

			/* Build Query */
			$this->get_posts( $grid_atts );
			if( empty( $this->posts ) ) return '<p class="mpc-noposts">' . __( 'No related posts.', 'mpc' ) . '</p>';

			/* Prepare */
			$css_id = $this->shortcode_styles( $grid_atts );
			$animation = MPC_Parser::animation( $grid_atts );

			/* Query Atts for Pagination */
			$query_atts = apply_filters( 'ma/grid_posts/query/atts', array(
				'post_type'            => $grid_atts[ 'post_type' ],
				'taxonomies'           => $grid_atts[ 'taxonomies' ],
				'ids'                  => $grid_atts[ 'ids' ],
				'order'                => $grid_atts[ 'order' ],
				'include_exclude_type' => $grid_atts[ 'include_exclude_type' ],
				'orderby'              => $grid_atts[ 'orderby' ],
				'items_number'         => $grid_atts[ 'items_number' ],
				'target'               => $css_id,
				'callback'             => 'MPC_Grid_Posts',
			) );

			/* Get Posts */
			$css_settings = array(
				'id' => $css_id,
				'selector' => '.mpc-grid-posts[id="' . $css_id . '"] .mpc-post'
			);

			/* Generate markup & template */
			$MPC_Single_Post->reset();
			$content = '';
			foreach( $this->posts as $single ) {
				$MPC_Single_Post->set_post( $single );
				$content .= $MPC_Single_Post->shortcode_template( $atts, null, null, $css_settings );
			}

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $MPC_Single_Post->classes;
			$classes .= ' ' . esc_attr( $grid_atts[ 'class' ] );
			$sh_atts = $grid_atts[ 'cols' ] != '' ? ' data-grid-cols="' . (int) esc_attr( $grid_atts[ 'cols' ] ) . '"' : '';

			/* Shortcode Output */
			$return = '<div id="' . $css_id . '" class="mpc-grid-posts' . $classes . '" ' . $animation . $sh_atts . '>';
				$return .= '<div class="mpc-grid-sizer"></div>';
				$return .= $content;
			$return .= '</div>';

			/* Prepare Pagination */
			if( !empty( $grid_atts[ 'mpc_pagination__preset' ] ) ) {
				$MPC_Pagination->query = $this->query;
				$return .= '<script type="text/javascript">var _' . $css_id . '_atts = ' . json_encode( $atts ). ', _' . $css_id . '_query = ' . json_encode( $query_atts ) . ';</script>';
				$return .= $MPC_Pagination->shortcode_template( $grid_atts[ 'mpc_pagination__preset' ], null, null, $css_id );
			}

			/* Restore original Post Data */
			$this->reset();

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_grid_posts_' . rand( 1, 100 ) );
			$style  = '';
			$styles[ 'gap' ] = $styles[ 'gap' ] != '' ? $styles[ 'gap' ] . ( is_numeric( $styles[ 'gap' ] ) ? 'px' : '' ) : '';

			// Gap
			if( $styles[ 'gap' ] != '' ) {
				$style .= '.mpc-grid-posts[id="' . $css_id . '"] {';
					$style .= 'margin-left: -' . $styles[ 'gap' ] . ';';
					$style .= 'margin-bottom: -' . $styles[ 'gap' ] . ';';
				$style .= '}';

				$style .= '.mpc-grid-posts[id="' . $css_id . '"] .mpc-post__wrapper {';
					$style .= 'margin-left: ' . $styles[ 'gap' ] . ';';
					$style .= 'margin-bottom: ' . $styles[ 'gap' ] . ';';
				$style .= '}';
			}

			// Regular
			if ( $temp_style = MPC_CSS::background( $styles, 'odd' ) ) {
				$style .= '.mpc-grid-posts[id="' . $css_id . '"] .mpc-post:nth-child(2n+1) .mpc-post__content { ';
					$style .= $temp_style;
				$style .= '}';
			}

			$mpc_massive_styles .= $style;

			return $css_id;
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
					'param_name'  => 'preset',
					'tooltip'     => MPC_Helper::style_presets_desc(),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'wide_modal'  => true,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
			);

			$base_ext = array(
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Gap', 'mpc' ),
					'param_name'       => 'gap',
					'tooltip'          => __( 'Choose gap between grid items.', 'mpc' ),
					'min'              => 0,
					'max'              => 100,
					'step'             => 1,
					'value'            => 0,
					'unit'             => 'px',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
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
					'tooltip'          => __( 'Select post types for grid. <b>Custom List</b> lets you select the exact list of posts you want.', 'mpc' ),
					'value'            => $this->post_types,
					'std'              => 'post',
					'group'            => __( 'Source', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'autocomplete',
					'heading'          => __( 'Posts', 'mpc' ),
					'param_name'       => 'ids',
					'tooltip'          => __( 'Define list of posts displayed by this grid.', 'mpc' ),
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
					'heading'          => __( 'Items Per Page', 'mpc' ),
					'param_name'       => 'items_number',
					'tooltip'          => __( 'Define maximum number of displayed posts per page. If the number of posts meeting the above parameters is smaller it will only show those posts.', 'mpc' ),
					'value'            => '6',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-slides',
						'align' => 'prepend',
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'label'            => '',
					'validate'         => true,
					'group'            => __( 'Source', 'mpc' ),
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
				)
			);


			/* General */
			$item_odd_background  = MPC_Snippets::vc_background( array( 'prefix' => 'odd', 'subtitle' => __( 'Odd', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );
			$rows_cols = MPC_Snippets::vc_rows_cols( array( 'cols' => array( 'min' => 1, 'max' => 4, 'default' => 2 ), 'rows' => false ) );
			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			/* Pagination */
			$integrate_pagination = vc_map_integrate_shortcode( 'mpc_pagination', 'mpc_pagination__', __( 'Pagination', 'mpc' ) );

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

				$integrate_pagination,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Grid Posts', 'mpc' ),
				'description' => __( 'Grid with posts', 'mpc' ),
				'base'        => $this->shortcode,
				'icon'        => 'mpc-shicon-grid-posts',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}

if ( class_exists( 'MPC_Grid_Posts' ) ) {
	global $MPC_Grid_Posts;
	$MPC_Grid_Posts = new MPC_Grid_Posts;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_grid_posts' ) ) {
	class WPBakeryShortCode_mpc_grid_posts extends MPCShortCode_Base {}
}

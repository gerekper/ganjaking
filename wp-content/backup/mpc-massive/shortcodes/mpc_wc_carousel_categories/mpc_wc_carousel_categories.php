<?php
/*----------------------------------------------------------------------------*\
	CAROUSEL PRODUCTS CATEGORIES SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_WC_Carousel_Categories' ) ) {
	class MPC_WC_Carousel_Categories {
		public $shortcode = 'mpc_wc_carousel_categories';
		public $items     = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( $this->shortcode, array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			/* Autocomplete */
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_include_callback', 'MPC_Autocompleter::suggest_wc_category', 10, 1 );
			add_filter( 'vc_autocomplete_' . $this->shortcode . '_include_render',  'MPC_Autocompleter::render_wc_category', 10, 1 );
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( $this->shortcode . '-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/css/' . $this->shortcode . '.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( $this->shortcode . '-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/js/' . $this->shortcode . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Get Categories */
		function get_items( $args ) {
			if( $args[ 'include' ] == '' ) {
				$args[ 'include' ] = array();
			}
			$this->items = get_terms( 'product_cat', $args );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			if( !class_exists( 'WooCommerce' ) ) {
				return '';
			}

			/* Enqueues */
			wp_enqueue_style( 'mpc-massive-slick-css', mpc_get_plugin_path( __FILE__ ) . '/assets/css/libs/slick.min.css' );
			wp_enqueue_script( 'mpc-massive-slick-js', mpc_get_plugin_path( __FILE__ ) . '/assets/js/libs/slick.min.js', array( 'jquery' ), '', true );

			global $MPC_Navigation, $MPC_WC_Category, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$slider_atts = shortcode_atts( array(
				'carousel_preset'           => '',
				'rows'          => '1',
				'cols'          => '4',
				'gap'           => '0',
				'stretched'     => '',
				'start_at'      => 1,
				'single_scroll' => '',
				'auto_slide'    => '',
				'delay'         => '1000',
				'loop'          => '',

				'hide_empty' => false,
				'include'    => '',
				'order'      => 'ASC',
				'orderby'    => 'title',

				'animation_in_type'          => 'none',
				'animation_in_duration'      => '300',
				'animation_in_delay'         => '0',
				'animation_in_offset'        => '100',

				'mpc_navigation__preset' => ''
			), $atts );

			/* Build Query */
			$this->get_items( $slider_atts );

			if( !$this->items ) {
				return '';
			}

			/* Prepare */
			$styles    = $this->shortcode_styles( $slider_atts );
			$css_id    = $styles[ 'id' ];

			$animation = MPC_Parser::animation( $slider_atts );
			$carousel  = MPC_Parser::carousel( $slider_atts );

			$css_settings = array(
				'id' => $css_id,
				'selector' => '.mpc-wc-carousel-categories[id="' . $css_id . '"] .mpc-wc-category'
			);

			$MPC_WC_Category->reset();
			$MPC_WC_Category->items = $this->items;

			/* Generate markup & template */
			$content = '';
			foreach( $MPC_WC_Category->items as $single ) {
				$MPC_WC_Category->set_cat( $single );
				$content .= $MPC_WC_Category->shortcode_template( $atts, null, null, $css_settings );
			}

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $slider_atts[ 'stretched' ] != '' ? ' mpc-carousel--stretched' : '';
			$classes .= $MPC_WC_Category->classes;

			$attributes = $MPC_WC_Category->attributes;

			$return = '<div class="mpc-carousel__wrapper mpc-waypoint">';
				$carousel = '<div id="' . $css_id . '" class="mpc-wc-carousel-categories' . $classes . '" ' . $attributes . ' ' . $animation . ' ' . $carousel . '>';
					$carousel .= $content;
				$carousel  .= '</div>';
				$return .= $MPC_Navigation->shortcode_template( $slider_atts[ 'mpc_navigation__preset' ], '', $css_id, 'image', $carousel );
			$return .= '</div>';

			/* Reset values */
			$MPC_WC_Category->reset();

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$frontend = $styles[ 'css' ];
				$frontend .= $MPC_WC_Category->style != '' ? $MPC_WC_Category->style : '';

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
				$style .= '.mpc-wc-carousel-categories[id="' . $css_id . '"] {';
					$style .= 'margin-left: -' . floor( $styles[ 'gap' ] * 0.5 ) . 'px;';
					$style .= 'margin-right: -' . floor( $styles[ 'gap' ] * 0.5 ) . 'px;';
				$style .= '}';

				$style .= '.mpc-wc-carousel-categories[id="' . $css_id . '"] .mpc-wc-category {';
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
					'heading'     => __( 'Carousel Preset', 'mpc' ),
					'param_name'  => 'carousel_preset',
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'description' => __( 'Specify carousel preset.', 'mpc' ),
				),
			);

			$base_ext = array(
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Gap', 'mpc' ),
					'param_name'       => 'gap',
					'description'      => __( 'Specify gap between slides.', 'mpc' ),
					'min'              => 0,
					'max'              => 50,
					'step'             => 1,
					'value'            => 0,
					'unit'             => 'px',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Stretch', 'mpc' ),
					'param_name'       => 'stretched',
					'description'      => __( 'Enable slider stretching.', 'mpc' ),
					'value'            => array( __( 'Yes', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Start At', 'mpc' ),
					'param_name'       => 'start_at',
					'description'      => __( 'Specify first slide index.', 'mpc' ),
					'value'            => '',
					'std'              => 1,
					'label'            => '',
					'validate'         => true,
					'addon'            => array(
						'icon'  => 'dashicons-images-alt',
						'align' => 'prepend'
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),

				/* Auto slide show */
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Slide Show', 'mpc' ),
					'subtitle'   => __( 'Specify slide show settings.', 'mpc' ),
					'param_name' => 'slide_show_divider',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Single Scroll?', 'mpc' ),
					'param_name'       => 'single_scroll',
					'description'      => __( 'Enable this to scroll only 1 item.', 'mpc' ),
					'value'            => array( __( 'Yes', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Auto Slide Show', 'mpc' ),
					'param_name'       => 'auto_slide',
					'description'      => __( 'Enable autoplay mode.', 'mpc' ),
					'value'            => array( __( 'Yes', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'        => 'mpc_slider',
					'heading'     => __( 'Delay', 'mpc' ),
					'param_name'  => 'delay',
					'description' => __( 'Specify delay between slides.', 'mpc' ),
					'min'         => 500,
					'max'         => 5000,
					'step'        => 50,
					'value'       => 1000,
					'unit'        => 'ms',
					'dependency'  => array(
						'element' => 'auto_slide',
						'value'   => 'true',
					),
				),

				/* Slider Loop */
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Loop', 'mpc' ),
					'subtitle'   => __( 'Specify loop mode settings.', 'mpc' ),
					'param_name' => 'loop_divider',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Enable Loop', 'mpc' ),
					'param_name'       => 'loop',
					'description'      => __( 'Enable loop mode.', 'mpc' ),
					'value'            => array( __( 'Yes', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
			);

			$source = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Source', 'mpc' ),
					'subtitle'   => __( 'Specify the source for the slider.', 'mpc' ),
					'param_name' => 'source_section_divider',
					'group'      => __( 'Source', 'mpc' ),
				),
				array(
					'type'        => 'autocomplete',
					'heading'     => __( 'Categories', 'mpc' ),
					'param_name'  => 'include',
					'settings'    => array(
						'multiple'      => true,
						'sortable'      => true,
						'unique_values' => true,
					),
					'description' => __( 'Enter List of Categories', 'mpc' ),
					'group'       => __( 'Source', 'mpc' ),
				),
				array(
					'type'               => 'dropdown',
					'heading'            => __( 'Order by', 'mpc' ),
					'param_name'         => 'orderby',
					'value'              => array(
						__( 'Title', 'mpc' )                       => 'title',
						__( 'Number of products', 'mpc' )          => 'count',
						__( 'Order by category ID', 'mpc' )        => 'ID',
						__( 'Order from Categories field', 'mpc' ) => 'include',
					),
					'std'                => 'date',
					'description'        => __( 'Select order type.', 'mpc' ),
					'group'              => __( 'Source', 'mpc' ),
					'edit_field_class'   => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'               => 'dropdown',
					'heading'            => __( 'Sorting', 'mpc' ),
					'param_name'         => 'order',
					'group'              => __( 'Source', 'mpc' ),
					'value'              => array(
						__( 'Descending', 'mpc' ) => 'DESC',
						__( 'Ascending', 'mpc' )  => 'ASC',
					),
					'std'                => 'ASC',
					'description'        => __( 'Select sorting order.', 'mpc' ),
					'edit_field_class'   => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Hide Empty', 'mpc' ),
					'param_name'       => 'hide_empty',
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to hide categories with 0 products.', 'mpc' ),
					'group'            => __( 'Source', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
			);

			/* General */
			$rows_cols = MPC_Snippets::vc_rows_cols( array( 'cols' => array( 'min' => 1, 'max' => 8, 'default' => 2 ) ) );
			$animation = MPC_Snippets::vc_animation_basic();

			/* Navigation */
			$integrate_navigation = vc_map_integrate_shortcode( 'mpc_navigation', 'mpc_navigation__', __( 'Navigation', 'mpc' ) );

			/* Integrate Item */
			$item_exclude   = array( 'exclude_regex' => '/animation_in(.*)|source_section_divider|item_id/', );
			$integrate_item = vc_map_integrate_shortcode( 'mpc_wc_category', '', '', $item_exclude );

			$params = array_merge(
				$base,
				$rows_cols,
				$base_ext,

				$source,

				$integrate_item,

				$integrate_navigation,
				$animation
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
				'name'        => __( 'Carousel Products Categories', 'mpc' ),
				'description' => __( 'Carousel with products category', 'mpc' ),
				'base'        => $this->shortcode,
				'icon'        => 'mpc-shicon-wc-carousel-categories',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}

if ( class_exists( 'MPC_WC_Carousel_Categories' ) ) {
	global $MPC_WC_Carousel_Categories;
	$MPC_WC_Carousel_Categories = new MPC_WC_Carousel_Categories;
}

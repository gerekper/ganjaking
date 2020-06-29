<?php
/*----------------------------------------------------------------------------*\
	GRID PRODUCTS_CATEGORIES SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_WC_Grid_Categories' ) ) {
	class MPC_WC_Grid_Categories {
		public $shortcode = 'mpc_wc_grid_categories';
		public $cats = array();

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
			wp_enqueue_script( $this->shortcode . '-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode .  '/js/' . $this->shortcode . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Reset */
		function reset() {
			$this->cats = array();
		}

		/* Get Categories */
		function get_items( $args ) {

			if( !empty( $this->cats ) ) {
				return;
			}

			if( $args[ 'include' ] === '' ) {
				$args[ 'include' ] = array();
			}

			$args[ 'taxonomy' ] = 'product_cat';
			$this->cats = get_terms( $args );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			if( !class_exists( 'WooCommerce' ) ) {
				return '';
			}

			/* Enqueues */
			wp_enqueue_script( 'mpc-massive-isotope-js' );
			
			global $MPC_WC_Category, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$grid_atts = shortcode_atts( array(
				'grid_preset' => '',
				'cols'        => '4',
				'gap'         => '0',

				'hide_empty' => false,
				'include'    => '',
				'order'      => 'ASC',
				'orderby'    => 'title',
			), $atts );

			/* Build Query */
			$this->get_items( $grid_atts );

			if( !$this->cats ) {
				return '';
			}

			/* Prepare */
			$styles    = $this->shortcode_styles( $grid_atts );
			$css_id    = $styles[ 'id' ];
			$animation = MPC_Parser::animation( $grid_atts );

			$css_settings = array(
				'id' => $css_id,
				'selector' => '.mpc-wc-grid-categories[id="' . $css_id . '"] .mpc-wc-category'
			);

			/* Generate markup & template */
			$MPC_WC_Category->reset();
			$content = '';
			foreach( $this->cats as $single ) {
				$MPC_WC_Category->set_cat( $single );
				$content .= $MPC_WC_Category->shortcode_template( $atts, null, null, $css_settings );
			}

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $MPC_WC_Category->classes;

			$attributes =  $grid_atts[ 'cols' ] != '' ? ' data-grid-cols="' . (int) $grid_atts[ 'cols' ] . '"' : '';
			$attributes .= $MPC_WC_Category->attributes;

			/* Shortcode Output */
			$return = '<div id="' . $css_id . '" class="mpc-wc-grid-categories' . $classes . '" ' . $animation . $attributes . '>';
				$return .= '<div class="mpc-grid-sizer"></div>';
				$return .= $content;
			$return .= '</div>';

			/* Reset values */
			$MPC_WC_Category->reset();
			$this->reset();

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
			$styles[ 'gap' ] = $styles[ 'gap' ] != '' ? $styles[ 'gap' ] . ( is_numeric( $styles[ 'gap' ] ) ? 'px' : '' ) : '';

			// Gap
			if( $styles[ 'gap' ] != '' ) {
				$style .= '.mpc-wc-grid-categories[id="' . $css_id . '"] {';
					$style .= 'margin-left: -' . $styles[ 'gap' ] . ';';
					$style .= 'margin-bottom: -' . $styles[ 'gap' ] . ';';
				$style .= '}';

				$style .= '.mpc-wc-grid-categories[id="' . $css_id . '"] .mpc-wc-category__wrap {';
					$style .= 'margin-left: ' . $styles[ 'gap' ] . ';';
					$style .= 'margin-bottom: ' . $styles[ 'gap' ] . ';';
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
					'param_name'  => 'preset',
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'description' => __( 'Specify main preset.', 'mpc' ),
				),
			);

			$base_ext = array(
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Gap', 'mpc' ),
					'param_name'       => 'gap',
					'description'      => __( 'Specify gap between items.', 'mpc' ),
					'min'              => 0,
					'max'              => 100,
					'step'             => 1,
					'value'            => 0,
					'unit'             => 'px',
				),
			);

			$source = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Source', 'mpc' ),
					'subtitle'   => __( 'Specify the source for the grid.', 'mpc' ),
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
			$rows_cols = MPC_Snippets::vc_rows_cols( array( 'cols' => array( 'min' => 1, 'max' => 5, 'default' => 3 ), 'rows' => false ) );
			$animation = MPC_Snippets::vc_animation_basic();

			/* Integrate Item */
			$item_exclude   = array( 'exclude_regex' => '/animation_in(.*)|source_section_divider|item_id/', );
			$integrate_item = vc_map_integrate_shortcode( 'mpc_wc_category', '', '', $item_exclude );

			$params = array_merge(
				$base,
				$rows_cols,
				$base_ext,
				$source,

				$integrate_item,
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
				'name'        => __( 'Grid Products Categories', 'mpc' ),
				'description' => __( 'Grid with products categories', 'mpc' ),
				'base'        => $this->shortcode,
				'icon'        => 'mpc-shicon-wc-grid-categories',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}

if ( class_exists( 'MPC_WC_Grid_Categories' ) ) {
	global $MPC_WC_Grid_Categories;
	$MPC_WC_Grid_Categories = new MPC_WC_Grid_Categories;
}

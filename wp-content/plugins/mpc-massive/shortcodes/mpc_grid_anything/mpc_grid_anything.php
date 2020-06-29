<?php
/*----------------------------------------------------------------------------*\
	GRID ANYTHING SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Grid_Anything' ) ) {
	class MPC_Grid_Anything {
		public $shortcode = 'mpc_grid_anything';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_grid_anything', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_grid_anything-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_grid_anything/css/mpc_grid_anything.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_grid_anything-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_grid_anything/js/mpc_grid_anything' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			/* Enqueues */
			wp_enqueue_script( 'mpc-massive-isotope-js' );

			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'  => '',
				'preset' => '',
				'cols'   => '4',
				'gap'    => '0',

				'item_odd_background_type'       => 'color',
				'item_odd_background_color'      => '',
				'item_odd_background_repeat'     => 'no-repeat',
				'item_odd_background_size'       => 'initial',
				'item_odd_background_position'   => 'middle-center',
				'item_odd_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'item_odd_background_image'      => '',
				'item_odd_background_image_size' => 'large',

				'item_even_background_type'       => 'color',
				'item_even_background_color'      => '',
				'item_even_background_repeat'     => 'no-repeat',
				'item_even_background_size'       => 'initial',
				'item_even_background_position'   => 'middle-center',
				'item_even_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'item_even_background_image'      => '',
				'item_even_background_image_size' => 'large',
				'item_border_css'                 => '',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',
			), $atts );

			/* Prepare */
			$styles    = $this->shortcode_styles( $atts );
			$css_id    = $styles[ 'id' ];
			$animation = MPC_Parser::animation( $atts );
			$columns   = $atts[ 'cols' ] != '' ? ' data-grid-cols="' . esc_attr( $atts[ 'cols' ] ) . '"' : '';

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			/* Shortcode Output */
			$return = '<div id="' . $css_id . '" class="mpc-grid-anything' . $classes . '" ' . $animation . $columns . '>';
				$return .= do_shortcode( $content );
			$return .= '</div>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return = '<div class="mpc-frontend-notice">';
					$return .= '<h4>' . __( 'Grid Anything', 'mpc' ) . '</h4>';
					$return .= __( 'Unfortunately this shortcode isn\'t available in <em>Frontend Editor</em> at the moment. This feature will be added in the upcoming updates. We are sorry for any inconvenience :)', 'mpc' );
				$return .= '</div>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_grid_anything-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'gap' ] = $styles[ 'gap' ] != '' ? $styles[ 'gap' ] . ( is_numeric( $styles[ 'gap' ] ) ? 'px' : '' ) : '';

			// Gap
			if( $styles[ 'gap' ] && $styles[ 'gap' ] != '0px') {
				$style .= '.mpc-grid-anything[id="' . $css_id . '"] {';
					$style .= 'margin-bottom: -' . $styles[ 'gap' ] . ';';
					$style .= 'margin-left: -' . $styles[ 'gap' ] . ';';
				$style .= '}';

				$style .= '.mpc-grid-anything[id="' . $css_id . '"] .mpc-grid__item-wrapper {';
					$style .= 'margin-bottom: ' . $styles[ 'gap' ] . ';';
					$style .= 'margin-left: ' . $styles[ 'gap' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'item_border_css' ] ) {
				$style .= '.mpc-grid-anything[id="' . $css_id . '"] .mpc-grid__item-wrapper {';
					$style .= $styles[ 'item_border_css' ];
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
					'type'             => 'custom_markup',
					'param_name'       => 'easy_notice',
					'value'            => '<p class="mpc-warning mpc-active"><i class="dashicons dashicons-warning"></i>' . __( 'Nothing to set here thanks to <em>Easy Mode</em>! :)', 'mpc' ) . '</p>',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-easy-field mpc-first-row',
				),
			);

			$base_ext = array(
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Gap', 'mpc' ),
					'param_name'       => 'gap',
					'tooltip'          => __( 'Choose gap between grid items.', 'mpc' ),
					'min'              => 0,
					'max'              => 50,
					'step'             => 1,
					'value'            => 0,
					'unit'             => 'px',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			$item_border = MPC_Snippets::vc_border( array( 'prefix' => 'item', 'subtitle' => __( 'Items', 'mpc' ) ) );

			$rows_cols = MPC_Snippets::vc_rows_cols( array( 'rows' => false, 'cols' => array( 'max' => 6 ) ) );
			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,

				$rows_cols,
				$base_ext,

				$item_border,

				$animation,
				$class
			);

			return array(
				'name'                    => __( 'Grid Anything', 'mpc' ),
				'description'             => __( 'Grid with shortcodes', 'mpc' ),
				'base'                    => 'mpc_grid_anything',
				'as_parent'               => array( 'only' => 'mpc_alert,mpc_button,mpc_cubebox,mpc_flipbox,mpc_icon,mpc_ihover,mpc_map,mpc_quote,mpc_image,vc_video,vc_column_text,mpc_callout,mpc_chart,mpc_icon_column,mpc_single_post' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
//				'icon'                    => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-grid-anything.png',
				'icon'                    => 'mpc-shicon-grid-anything',
				'category'                => __( 'Massive', 'mpc' ),
				'params'                  => $params,
				'js_view'                 => 'VcColumnView',
			);
		}
	}
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_mpc_grid_anything' ) ) {
	class WPBakeryShortCode_mpc_grid_anything extends WPBakeryShortCodesContainer {}
}

if ( class_exists( 'MPC_Grid_Anything' ) ) {
	global $MPC_Grid_Anything;
	$MPC_Grid_Anything = new MPC_Grid_Anything;
}

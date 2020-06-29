<?php
/*----------------------------------------------------------------------------*\
	CONNECTED ICONS SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Connected_Icons' ) ) {
	class MPC_Connected_Icons {
		public $shortcode = 'mpc_connected_icons';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_connected_icons', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_connected_icons-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_connected_icons/css/mpc_connected_icons.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_connected_icons-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_connected_icons/js/mpc_connected_icons' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                 => '',
				'preset'                => '',
				'content_preset'        => '',
				'layout'                => 'horizontal',
				'cols'                  => '4',
				'gap'                   => '15',

				'lines_target'          => 'icon',
				'lines_number'          => '1',
				'lines_style'           => 'solid',
				'lines_color'           => '#333',
				'lines_gap'             => '1',
				'lines_weight'          => '1',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',
			), $atts );

			/* Prepare */
			$animation = MPC_Parser::animation( $atts );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$lines   = ( int ) $atts[ 'lines_number' ] > 0 ? str_repeat( '<span></span>', ( int ) $atts[ 'lines_number' ] ) : '';
			$columns = ' data-ci-cols="' . ( $atts[ 'cols' ] != '' ? esc_attr( $atts[ 'cols' ] ) : '4' ) . '"';
			$target  = ' data-target="' . ( $atts[ 'lines_target' ]  != '' ? esc_attr( $atts[ 'lines_target' ] ) : 'icon' ) . '"';
			$layout  = ' data-layout="' . ( $atts[ 'layout' ]  != '' ? esc_attr( $atts[ 'layout' ] ) : 'vertical' ) . '"';

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init mpc-transition';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			/* Shortcode Output */
			$return = '<div id="' . $css_id . '" class="mpc-connected-icons' . $classes . '" ' . $animation . $columns . $target . $layout . '>';
				$return .= '<div class="mpc-connected-icons__line">' . $lines . '</div>';
				$return .= do_shortcode( $content );
			$return .= '</div>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_connected_icons-' . rand( 1, 100 ) );
			$style  = '';

			// Add 'px'
			$styles[ 'lines_weight' ] = $styles[ 'lines_weight' ] != '' ? $styles[ 'lines_weight' ] . ( is_numeric( $styles[ 'lines_weight' ] ) ? 'px' : '' ) : '';
			$styles[ 'lines_gap' ] = $styles[ 'lines_gap' ] != '' ? $styles[ 'lines_gap' ] . ( is_numeric( $styles[ 'lines_gap' ] ) ? 'px' : '' ) : '';
			$styles[ 'gap' ] = $styles[ 'gap' ] != '' ? $styles[ 'gap' ] . ( is_numeric( $styles[ 'gap' ] ) ? 'px' : '' ) : '';

			if( $styles[ 'layout' ] == 'horizontal' ) {
				$side = 'top';
			} else {
				$side = 'left';
			}

			$inner_styles = array();
			if ( $styles[ 'lines_style' ] ) { $inner_styles[] = 'border-' . $side . '-style:' . $styles[ 'lines_style' ] . ';'; }
			if ( $styles[ 'lines_color' ] ) { $inner_styles[] = 'border-' . $side . '-color:' . $styles[ 'lines_color' ] . ';'; }
			if ( $styles[ 'lines_weight' ] ) { $inner_styles[] = 'border-' . $side . '-width:' . $styles[ 'lines_weight' ] . ';'; }
			if ( $styles[ 'lines_gap' ] ) { $inner_styles[] = 'margin-' . $side . ':' . $styles[ 'lines_gap' ] . ';'; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-connected-icons[id="' . $css_id . '"] .mpc-connected-icons__line > span {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'gap' ] ) {
				$style .= '.mpc-connected-icons[id="' . $css_id . '"] .mpc-connected-icons__item {';
					$style .= 'padding: ' . ( $side == 'left' ? $styles[ 'gap' ] . ' 0' : '0 ' . $styles[ 'gap' ] ) . ';';
				$style .= '}';

				if( $side != 'left' ) {
					$style .= '.mpc-connected-icons[id="' . $css_id . '"] {';
						$style .= 'margin: 0 -' . $styles[ 'gap' ] . ';';
					$style .= '}';
				}
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
					'tooltip'     => MPC_Helper::style_presets_desc(),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'wide_modal'  => true,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
				array(
					'type'        => 'mpc_content',
					'heading'     => __( 'Content Preset', 'mpc' ),
					'param_name'  => 'content_preset',
					'tooltip'     => MPC_Helper::content_presets_desc(),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'extended'    => true,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Orientation', 'mpc' ),
					'param_name'       => 'layout',
					'tooltip'          => __( 'Select icons orientation.', 'mpc' ),
					'value'            => array(
						__( 'Horizontal', 'mpc' ) => 'horizontal',
						__( 'Vertical', 'mpc' )   => 'vertical',
					),
					'std'              => 'horizontal',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Columns Number', 'mpc' ),
					'tooltip'          => __( 'Select number of displayed columns.', 'mpc' ),
					'param_name'       => 'cols',
					'admin_label'      => true,
					'value'            => array(
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
					),
					'std'              => '4',
					'dependency'       => array( 'element' => 'layout', 'value' => 'horizontal' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Connection Target', 'mpc' ),
					'param_name'       => 'lines_target',
					'admin_label'      => true,
					'tooltip'          => __( 'Select connection target:<br><b>Icon</b>: lines are drawn between icons;<br><b>Box</b>: lines are drawn between whole icon columns.', 'mpc' ),
					'value'            => array(
						__( 'Icon', 'mpc' ) => 'icon',
						__( 'Box', 'mpc' )  => 'box',
					),
					'std'              => 'icon',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Side spaces', 'mpc' ),
					'param_name'       => 'gap',
					'tooltip'          => __( 'Choose side spaces for each column. Spaces are added to both sides of each column so the gaps between columns would be twice the size of specified value.', 'mpc' ),
					'max'              => 150,
					'value'            => 15,
					'unit'             => 'px',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Lines', 'mpc' ),
					'param_name'       => 'lines_divider',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Number', 'mpc' ),
					'param_name'       => 'lines_number',
					'tooltip'          => __( 'Select number of connections lines.', 'mpc' ),
					'value'            => array(
						__( 'One', 'mpc' )   => 1,
						__( 'Two', 'mpc' )   => 2,
						__( 'Three', 'mpc' ) => 3,
					),
					'std'              => 1,
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Style', 'mpc' ),
					'param_name'       => 'lines_style',
					'tooltip'          => __( 'Select lines style. Learn more about line styles <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/border-style" target="_blank">here</a>.', 'mpc' ),
					'value'            => array(
						__( 'Solid', 'mpc' )  => 'solid',
						__( 'Dotted', 'mpc' ) => 'dotted',
						__( 'Dashed', 'mpc' ) => 'dashed',
						__( 'Double', 'mpc' ) => 'double',
						__( 'Groove', 'mpc' ) => 'groove',
						__( 'Ridge', 'mpc' )  => 'ridge',
					),
					'std'              => 'solid',
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'lines_color',
					'tooltip'          => __( 'Choose lines color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-color-picker',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Gap', 'mpc' ),
					'param_name'       => 'lines_gap',
					'tooltip'          => __( 'Choose vertical gap between lines. Small lines <b>Gap</b> and large lines <b>Weight</b> may lead to lines overlap.', 'mpc' ),
					'max'              => 20,
					'value'            => 1,
					'unit'             => 'px',
					'dependency'       => array( 'element' => 'lines_number', 'value' => array( '2', '3' ) ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Weight', 'mpc' ),
					'param_name'       => 'lines_weight',
					'tooltip'          => __( 'Choose lines thickness.', 'mpc' ),
					'min'              => 1,
					'max'              => 100,
					'value'            => 1,
					'unit'             => 'px',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$animation,
				$class
			);

			return array(
				'name'                    => __( 'Info List', 'mpc' ),
				'description'             => __( 'Line connected info boxes', 'mpc' ),
				'base'                    => 'mpc_connected_icons',
				'is_container'            => true,
				'as_parent'               => array( 'only' => 'mpc_icon_column' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
//				'icon'                    => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-connected-icon-column.png',
				'icon'                    => 'mpc-shicon-connected-icons',
				'category'                => __( 'Massive', 'mpc' ),
				'params'                  => $params,
				'js_view'                 => 'VcColumnView',
			);
		}
	}
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_mpc_connected_icons' ) ) {
	class WPBakeryShortCode_mpc_connected_icons extends WPBakeryShortCodesContainer {}
}

if ( class_exists( 'MPC_Connected_Icons' ) ) {
	global $MPC_Connected_Icons;
	$MPC_Connected_Icons = new MPC_Connected_Icons;
}

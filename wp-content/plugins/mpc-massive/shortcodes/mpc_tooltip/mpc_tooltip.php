<?php
/*----------------------------------------------------------------------------*\
	TOOLTIP SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Tooltip' ) ) {
	class MPC_Tooltip {
		public $shortcode = 'mpc_tooltip';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_tooltip', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_tooltip-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_tooltip/css/mpc_tooltip.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_tooltip-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_tooltip/js/mpc_tooltip' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'          => '',
				'preset'         => '',
				'text'           => '',
				'trigger'        => 'hover',
				'position'       => 'top',
				'show_effect'    => 'fade',
				'disable_arrow'  => '',
				'disable_hover'  => '',
				'enable_wide'    => '',
				'always_visible' => '',

				'font_preset'      => '',
				'font_color'       => '',
				'font_size'        => '',
				'font_line_height' => '',
				'font_align'       => '',
				'font_transform'   => '',

				'padding_css' => '',
				'border_css'  => '',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
			), $atts );

			if ( $atts[ 'text' ] == '' ) {
				return '';
			} else {
				$atts[ 'text' ] = rawurldecode( base64_decode( strip_tags( $atts[ 'text' ] ) ) );
			}

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$classes = ' mpc-init';
			$classes .= $atts[ 'font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'font_preset' ] ) : '';
			$classes .= ' mpc-position--' . esc_attr( $atts[ 'position' ] );
			$classes .= $atts[ 'show_effect' ] != '' ? ' mpc-effect--' . esc_attr( $atts[ 'show_effect' ] ) : '';
			$classes .= $atts[ 'disable_arrow' ] != '' || ( ! preg_match( '/border-width|border-top|border-right|border-bottom|border-left/', $atts[ 'border_css' ] ) && $atts[ 'background_type' ] != 'color' ) ? ' mpc-no-arrow' : '';
			$classes .= $atts[ 'disable_hover' ] != '' ? '' : ' mpc-can-hover';
			$classes .= $atts[ 'always_visible' ] != '' ? ' mpc-triggered' : '';
			$classes .= $atts[ 'enable_wide' ] != '' ? ' mpc-wide' : '';
			$classes .= ' mpc-trigger--' . $atts[ 'trigger' ];
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$return = '<div id="' . $css_id . '" class="mpc-tooltip' . $classes . '">';
				$return .= do_shortcode( $atts[ 'text' ] );
				if ( $atts[ 'disable_arrow' ] == '' && ( $atts[ 'border_css' ] != '' || $atts[ 'background_type' ] == 'color' ) ) {
					$return .= '<div class="mpc-arrow"></div>';
				}
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
			$css_id = uniqid( 'mpc_tooltip-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'font_size' ] = $styles[ 'font_size' ] != '' ? $styles[ 'font_size' ] . ( is_numeric( $styles[ 'font_size' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-tooltip[id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Hover
			if ( $styles[ 'disable_arrow' ] == '' && strpos( $styles[ 'border_css' ], 'border-color' ) === false && $styles[ 'background_type' ] == 'color' && $styles[ 'background_color' ] != '' ) {
				$style .= '.mpc-tooltip[id="' . $css_id . '"] .mpc-arrow {';
					$style .= 'border-color: ' . $styles[ 'background_color' ];
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
					'tooltip'     => MPC_Helper::style_presets_desc(),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Trigger', 'mpc' ),
					'param_name'       => 'trigger',
					'tooltip'          => __( 'Set which event should interact with tooltip visibility.', 'mpc' ),
					'value'            => array(
						__( 'Hover', 'mpc' ) => 'hover',
						__( 'Click', 'mpc' ) => 'click',
					),
					'std'              => 'hover',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Position', 'mpc' ),
					'param_name'       => 'position',
					'tooltip'          => 'Select tooltip position.',
					'value'            => array(
						__( 'Top', 'mpc' )    => 'top',
						__( 'Right', 'mpc' )  => 'right',
						__( 'Bottom', 'mpc' ) => 'bottom',
						__( 'Left', 'mpc' )   => 'left',
					),
					'std'              => 'top',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Display Effect', 'mpc' ),
					'param_name'       => 'show_effect',
					'tooltip'          => __( 'Select tooltip display style:<br><b>Fade</b>: simple fade in effect;<br><b>Slide In</b>: slide tooltip to its target;<br><b>Slide Out</b>: slide tooltip from its target.', 'mpc' ),
					'value'            => array(
						__( 'Fade', 'mpc' )  => 'fade',
						__( 'Slide In', 'mpc' ) => 'slide',
						__( 'Slide Out', 'mpc' )  => 'push',
					),
					'std'              => 'fade',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Arrow', 'mpc' ),
					'param_name'       => 'disable_arrow',
					'tooltip'          => __( 'Check to disable arrow pointing tooltip target.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Wide', 'mpc' ),
					'param_name'       => 'enable_wide',
					'tooltip'          => __( 'Check to enable wide tooltip. Default tooltip can stretch to 300px in width.<b>Wide</b> tooltip increase the size to 500px.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Visible from start', 'mpc' ),
					'param_name'       => 'always_visible',
					'tooltip'          => __( 'Check to show tooltip on start.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-3 vc_column',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Hover', 'mpc' ),
					'param_name'       => 'disable_hover',
					'tooltip'          => __( 'Check to disable hover on tooltip. Helpful if you want to display only text without any links.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'dependency'       => array( 'element' => 'trigger', 'value' => 'hover' ),
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field',
				),
			);

			$content = array(
				array(
					'type'        => 'textarea_raw_html',
					'heading'     => __( 'Content', 'mpc' ),
					'param_name'  => 'text',
//					'holder'      => 'div',
					'tooltip'     => __( 'Define Tooltip content here, you can use HTML tags freely.', 'mpc' ),
					'value'       => '',
				),
			);

			$font       = MPC_Snippets::vc_font();
			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$class      = MPC_Snippets::vc_class();

			$params = array_merge( $base, $font, $content, $background, $border, $padding, $class );

			return array(
				'name'        => __( 'Tooltip', 'mpc' ),
				'description' => __( 'Small popup with content', 'mpc' ),
				'base'        => 'mpc_tooltip',
				'class'       => '',
				'icon'        => 'mpc-shicon-tooltip',
				'category'    => __( 'Massive', 'mpc' ),
				'as_child'    => array( 'only' => '' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Tooltip' ) ) {
	global $MPC_Tooltip;
	$MPC_Tooltip = new MPC_Tooltip;
}

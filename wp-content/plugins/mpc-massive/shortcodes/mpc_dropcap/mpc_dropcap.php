<?php
/*----------------------------------------------------------------------------*\
	DROPCAP SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Dropcap' ) ) {
	class MPC_Dropcap {
		public $shortcode = 'mpc_dropcap';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_dropcap', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_dropcap-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_dropcap/css/mpc_dropcap.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_dropcap-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_dropcap/js/mpc_dropcap' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'  => '',
				'preset' => '',

				'font_preset'      => '',
				'font_color'       => '',
				'font_size'        => '',
				'font_line_height' => '',
				'font_align'       => '',
				'font_transform'   => '',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'cover',
				'background_position'   => 'middle-center',

				'border_css'  => '',
				'padding_css' => '',
				'margin_css'  => '',

				'letter' => '',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',

				'animation_loop_type'     => 'none',
				'animation_loop_duration' => '1000',
				'animation_loop_delay'    => '1000',
				'animation_loop_hover'    => '',
			), $atts );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$animation = MPC_Parser::animation( $atts );
			$classes   = ' mpc-init';
			$classes   .= $atts[ 'font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'font_preset' ] ) : '';
			$classes   .= $animation != '' ? ' mpc-animation' : '';
			$classes   .= ' ' . esc_attr( $atts[ 'class' ] );

			$return = '<div id="' . $css_id . '" class="mpc-dropcap' . $classes . '"' . $animation . '>';
				$return .= $atts[ 'letter' ];
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
			$css_id = uniqid( 'mpc_dropcap-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'font_size' ] = $styles[ 'font_size' ] != '' ? $styles[ 'font_size' ] . ( is_numeric( $styles[ 'font_size' ] ) ? 'px' : '' ) : '';

			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-dropcap[id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
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
			);

			$letter = array(
				array(
					'type'        => 'mpc_text',
					'heading'     => __( 'Letter', 'mpc' ),
					'param_name'  => 'letter',
					'admin_label' => true,
					'tooltip'     => __( 'Specify drop cap letter.', 'mpc' ),
					'value'       => '',
					'addon'       => array(
						'icon'  => 'dashicons dashicons-editor-textcolor',
						'align' => 'prepend',
					),
					'label'       => '',
					'validate'    => false,
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			$font       = MPC_Snippets::vc_font();
			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();

			$animation = MPC_Snippets::vc_animation();
			$class     = MPC_Snippets::vc_class();

			return array(
				'name'        => __( 'Dropcap', 'mpc' ),
				'description' => __( 'Emphasized letter for text block', 'mpc' ),
				'base'        => 'mpc_dropcap',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-drop-caps.png',
				'icon'        => 'mpc-shicon-dropcap',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => array_merge( $base, $font, $letter, $background, $border, $padding, $margin, $animation, $class ),
			);
		}
	}
}
if ( class_exists( 'MPC_Dropcap' ) ) {
	global $MPC_Dropcap;
	$MPC_Dropcap = new MPC_Dropcap;
}

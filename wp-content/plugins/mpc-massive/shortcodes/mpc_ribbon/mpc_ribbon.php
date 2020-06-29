<?php
/*----------------------------------------------------------------------------*\
	RIBBON SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Ribbon' ) ) {
	class MPC_Ribbon {
		public $shortcode = 'mpc_ribbon';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_ribbon', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_ribbon-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_ribbon/css/mpc_ribbon.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_ribbon-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_ribbon/js/mpc_ribbon' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'               => '',
                'preset'              => '',
                'text'                => '',
                'style'               => 'classic',
                'alignment'           => 'top-left',
                'disable_corners'     => '',
                'corners_color'       => '#333333',
                'size'                => 'medium',

				'font_preset'      => '',
				'font_color'       => '',
				'font_size'        => '',
				'font_line_height' => '',
				'font_align'       => '',
				'font_transform'   => '',

				'icon_type'       => 'icon',
				'icon'            => '',
				'icon_character'  => '',
				'icon_image'      => '',
				'icon_image_size' => 'thumbnail',
				'icon_preset'     => '',
				'icon_size'       => '',
				'icon_color'      => '#333333',

				'margin_css'  => '',
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
				$atts[ 'text' ] = strip_tags( $atts[ 'text' ] );
			}

			/* Prepare */
			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];
			$icon   = MPC_Parser::icon( $atts );

			/* Classes */
			$classes = ' mpc-init';
			$classes .= $atts[ 'font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'font_preset' ] ) : '';
			$classes .= ' mpc-alignment--' . esc_attr( $atts[ 'alignment' ] );
			$classes .= ' mpc-style--' . esc_attr( $atts[ 'style' ] );
			$classes .= $atts[ 'style' ] == 'corner' ? ' mpc-size--' . esc_attr( $atts[ 'size' ] ) : '';
			$classes .= $atts[ 'disable_corners'] == 'true' ? ' mpc-disable-corners' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			/* Output */
			$return = '<div id="' . $css_id . '" class="mpc-ribbon' . $classes . '">';
			if ( $atts[ 'style' ] == 'corner' ) { $return .= '<div class="mpc-vertical">'; }
				$return .= '<div class="mpc-ribbon__content">';
					if( $icon[ 'class' ] != '' || $icon[ 'content' ] != '' ) {
						$return .= '<i class="mpc-ribbon__icon ' . $icon[ 'class' ] . '">' . $icon[ 'content' ] . '</i>';
					}
					$return .= $atts[ 'text' ];
				$return .= '</div>';
			if ( $atts[ 'style' ] == 'corner' ) { $return .= '</div>'; }
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
			$css_id = uniqid( 'mpc_ribbon-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'font_size' ] = $styles[ 'font_size' ] != '' ? $styles[ 'font_size' ] . ( is_numeric( $styles[ 'font_size' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-ribbon[id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-ribbon[id="' . $css_id . '"] .mpc-ribbon__content {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::icon( $styles ) ) {
				$style .= '.mpc-ribbon[id="' . $css_id . '"] .mpc-ribbon__icon {';
					$style .= $temp_style;
				$style .= '}';
			}

			if ( $styles[ 'corners_color' ] ) {
				$style .= '.mpc-ribbon[id="' . $css_id . '"]:after, .mpc-ribbon[id="' . $css_id . '"]:before {';
					$style .= 'border-color:' . $styles[ 'corners_color' ] . ';';
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
					'heading'          => __( 'Style', 'mpc' ),
					'param_name'       => 'style',
					'tooltip'          => __( 'Choose ribbon style:<br><b>Classic</b>: ribbon displays at one of its container corners;<br><b>Corner</b>: ribbon wraps one of its container corners at 45&deg; angle;<br><b>Full Width</b>: ribbon spans from left to right side of its container.', 'mpc' ),
					'value'            => array(
						__( 'Classic', 'mpc' )   => 'classic',
						__( 'Corner', 'mpc' )    => 'corner',
						__( 'Full Width', 'mpc' ) => 'fullwidth',
					),
					'std'              => 'classic',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Position', 'mpc' ),
					'param_name'       => 'alignment',
					'tooltip'          => __( 'Select ribbon position.', 'mpc' ),
					'value' => array(
						__( 'Top Left', 'mpc' )     => 'top-left',
						__( 'Top Right', 'mpc' )    => 'top-right',
						__( 'Bottom Left', 'mpc' )  => 'bottom-left',
						__( 'Bottom Right', 'mpc' ) => 'bottom-right',
					),
					'std'              => 'top-left',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Size', 'mpc' ),
					'param_name'       => 'size',
					'tooltip'          => __( 'Select ribbon size.', 'mpc' ),
					'value' => array(
						__( 'Small', 'mpc' )  => 'small',
						__( 'Medium', 'mpc' ) => 'medium',
						__( 'Large', 'mpc' )  => 'large',
					),
					'std'              => 'medium',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'style', 'value' => 'corner' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Folds', 'mpc' ),
					'param_name'       => 'disable_corners',
					'tooltip'          => __( 'Check to disable folds. Disabling them will removing ribbon 3D effect.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-clear--both mpc-advanced-field',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Folds Color', 'mpc' ),
					'param_name'       => 'corners_color',
					'tooltip'          => __( 'Choose folds color.', 'mpc' ),
					'value'            => '#333333',
					'edit_field_class' => 'vc_col-sm-8 vc_column',
					'dependency'  => array(
						'element'            => 'disable_corners',
						'value_not_equal_to' => 'true',
					),
				),
			);

			$content = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Text', 'mpc' ),
					'param_name'       => 'text',
					'admin_label'      => true,
					'tooltip'          => __( 'Define ribbon text.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			$font       = MPC_Snippets::vc_font();
			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();
			$class      = MPC_Snippets::vc_class();

			$params = array_merge( $base, $font, $content, $background, $border, $padding, $margin, $class );

			return array(
				'name'        => __( 'Ribbon', 'mpc' ),
				'description' => __( 'Fancy ribbon with text', 'mpc' ),
				'base'        => 'mpc_ribbon',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-ribbon.png',
				'icon'        => 'mpc-shicon-ribbon',
				'category'    => __( 'Massive', 'mpc' ),
				'as_child'    => array( 'only' => '' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Ribbon' ) ) {
	global $MPC_Ribbon;
	$MPC_Ribbon = new MPC_Ribbon;
}
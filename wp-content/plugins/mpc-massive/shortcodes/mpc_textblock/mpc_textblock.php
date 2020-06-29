<?php
/*----------------------------------------------------------------------------*\
	TEXTBLOCK SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Textblock' ) ) {
	class MPC_Textblock {
		public $shortcode = 'mpc_textblock';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_textblock', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_textblock-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_textblock/css/mpc_textblock.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_textblock-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_textblock/js/mpc_textblock' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $MPC_Tooltip, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'          => '',
				'inline'         => '',
				'content_width'  => '100',
				'content_shadow' => '',

				'font_preset'      => '',
				'font_color'       => '',
				'font_size'        => '',
				'font_line_height' => '',
				'font_align'       => '',
				'font_transform'   => '',

				'margin_css'  => '',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',

				'animation_loop_type'     => 'none',
				'animation_loop_duration' => '1000',
				'animation_loop_delay'    => '1000',
				'animation_loop_hover'    => '',

				/* Tooltip */
				'mpc_tooltip__disable'    => '',

				'mpc_tooltip__preset'        => '',
				'mpc_tooltip__text'          => '',
				'mpc_tooltip__trigger'       => 'hover',
				'mpc_tooltip__position'      => 'top',
				'mpc_tooltip__show_effect'   => '',
				'mpc_tooltip__disable_arrow' => '',
				'mpc_tooltip__disable_hover' => '',
				'mpc_tooltip__always_visible' => '',
				'mpc_tooltip__enable_wide'   => '',

				'mpc_tooltip__font_preset'      => '',
				'mpc_tooltip__font_color'       => '',
				'mpc_tooltip__font_size'        => '',
				'mpc_tooltip__font_line_height' => '',
				'mpc_tooltip__font_align'       => '',
				'mpc_tooltip__font_transform'   => '',

				'mpc_tooltip__padding_css' => '',
				'mpc_tooltip__border_css'  => '',

				'mpc_tooltip__background_type'       => 'color',
				'mpc_tooltip__background_color'      => '',
				'mpc_tooltip__background_image'      => '',
				'mpc_tooltip__background_image_size' => 'large',
				'mpc_tooltip__background_repeat'     => 'no-repeat',
				'mpc_tooltip__background_size'       => 'initial',
				'mpc_tooltip__background_position'   => 'middle-center',
				'mpc_tooltip__background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
			), $atts );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$atts_tooltip = MPC_Parser::shortcode( $atts, 'mpc_tooltip_' );
			$tooltip      = $atts[ 'mpc_tooltip__disable' ] == '' ? $MPC_Tooltip->shortcode_template( $atts_tooltip ) : '';

			$animation = MPC_Parser::animation( $atts );
			$classes   = ' mpc-init';
			$classes   .= $atts[ 'font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'font_preset' ] ) : '';
			$classes   .= $animation != '' ? ' mpc-animation' : '';
			$classes   .= ' ' . esc_attr( $atts[ 'class' ] );
			$classes_tooltip = $atts[ 'inline' ] != '' ? ' mpc-inline' : '';
			$classes .= $tooltip == '' ? $classes_tooltip : '';

			$return = $tooltip != '' ? '<div class="mpc-tooltip-wrap' . $classes_tooltip . '" data-id="' . $css_id . '">' : '';
				$return .= '<div id="' . $css_id . '" class="mpc-textblock' . $classes . '"' . $animation . '>';
					$return .= wpb_js_remove_wpautop( $content, true );
				$return .= '</div>';
			$return .= $tooltip;
			$return .= $tooltip != '' ? '</div>' : '';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_textblock-' . rand( 1, 100 ) );
			$style = '';
			$disabled_tooltip = $styles[ 'mpc_tooltip__disable' ] != '' || ( $styles[ 'mpc_tooltip__disable' ] == '' && $styles[ 'mpc_tooltip__text' ] == '' );

			// Add 'px'
			$styles[ 'font_size' ] = $styles[ 'font_size' ] != '' ? $styles[ 'font_size' ] . ( is_numeric( $styles[ 'font_size' ] ) ? 'px' : '' ) : '';

			// Add '%'
			$styles[ 'content_width' ] = $styles[ 'content_width' ] != '' ? $styles[ 'content_width' ] . ( is_numeric( $styles[ 'content_width' ] ) ? '%' : '' ) : '';

			// Content
			$inner_styles = array();
			if ( $styles[ 'margin_css' ] && $disabled_tooltip ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $styles[ 'content_shadow' ] ) { $inner_styles[] = 'text-shadow:' . $styles[ 'content_shadow' ] . ';'; }
			if ( $styles[ 'inline' ] === '' ) {
				$inner_styles[] = 'max-width:' . $styles[ 'content_width' ] . ';';
				$allow_center = true;
				if( false !== strpos( $styles[ 'margin_css' ], 'left' )
				    || false !== strpos( $styles[ 'margin_css' ], 'right' )
				    || false !== strpos( $styles[ 'margin_css' ], 'margin:' ) ) {
					$allow_center = false;
				}
				if ( $allow_center && intval( $styles[ 'content_width' ] ) !== 100 ) {
					if( $styles[ 'font_align' ] === 'right' ) {
						$inner_styles[] = 'margin-left: ' . ( 100 - intval( $styles[ 'content_width' ] ) ) . '%;';
					} else if( $styles[ 'font_align' ] === 'center' || $styles[ 'font_align' ] === 'justify' ) {
						$inner_styles[] = 'margin-left: ' . ( 100 - intval( $styles[ 'content_width' ] ) ) * .5 . '%;';
						$inner_styles[] = 'margin-right: ' . ( 100 - intval( $styles[ 'content_width' ] ) ) * .5 . '%;';
					}
				}
			}
			if ( $temp_style = MPC_CSS::font( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-textblock[id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'margin_css' ] && ! $disabled_tooltip ) {
				$style .= '.mpc-tooltip-wrap[data-id="' . $css_id . '"] {';
					$style .= $styles[ 'margin_css' ];
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
					'type'             => 'checkbox',
					'heading'          => __( 'Display Inline', 'mpc' ),
					'param_name'       => 'inline',
					'tooltip'          => __( 'Check to enable inline display.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Width', 'mpc' ),
					'param_name'       => 'content_width',
					'tooltip'          => __( 'Choose textblock width. If you choose width smaller then 100% it will be centered by default.', 'mpc' ),
					'value'            => 100,
					'std'              => 100,
					'min'              => 10,
					'max'              => 100,
					'unit'             => '%',
					'dependency'       => array( 'element' => 'inline', 'value_not_equal_to' => 'true' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			$content = array(
				array(
					'type'        => 'textarea_html',
					'heading'     => __( 'Content', 'mpc' ),
					'param_name'  => 'content',
					'holder'      => 'div',
					'tooltip'     => __( 'Define content. Thanks to default WordPress WYSIWYG editor you can easily format the content.', 'mpc' ),
					'value'       => '',
				),
				array(
					'type'        => 'mpc_shadow',
					'heading'     => __( 'Text Shadow', 'mpc' ),
					'param_name'  => 'content_shadow',
					'tooltip'     => __( 'Define text shadow for content.', 'mpc' ),
					'value'       => '',
				),
			);

			$integrate_tooltip = vc_map_integrate_shortcode( 'mpc_tooltip', 'mpc_tooltip__', __( 'Tooltip', 'mpc' ) );
			$disable_tooltip   = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Tooltip', 'mpc' ),
					'param_name'       => 'mpc_tooltip__disable',
					'tooltip'          => __( 'Check to disable tooltip.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Tooltip', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_tooltip = array_merge( $disable_tooltip, $integrate_tooltip );

			$font       = MPC_Snippets::vc_font();
			$margin     = MPC_Snippets::vc_margin();

			$animation = MPC_Snippets::vc_animation();
			$class     = MPC_Snippets::vc_class();

			return array(
				'name'        => __( 'Text Block', 'mpc' ),
				'description' => __( 'Display a text with font formatting', 'mpc' ),
				'base'        => 'mpc_textblock',
				'class'       => '',
				'icon'        => 'mpc-shicon-textblock',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => array_merge( $base, $font, $content, $margin, $integrate_tooltip, $animation, $class ),
			);
		}
	}
}
if ( class_exists( 'MPC_Textblock' ) ) {
	global $MPC_Textblock;
	$MPC_Textblock = new MPC_Textblock;
}

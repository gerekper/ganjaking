<?php
/*----------------------------------------------------------------------------*\
	ICON SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Icon' ) ) {
	class MPC_Icon {
		public $shortcode = 'mpc_icon';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_icon', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_icon-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_icon/css/mpc_icon.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_icon-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_icon/js/mpc_icon' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $mpc_ma_options, $MPC_Tooltip;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'      => '',
				'preset'     => '',
				'url'        => '',
				'transition' => 'none',

				'padding_css' => '',
				'margin_css'  => '',

				'border_css' => '',

				'icon_type'       => 'icon',
				'icon'            => '',
				'icon_character'  => '',
				'icon_image'      => '',
				'icon_image_size' => 'thumbnail',
				'icon_preset'     => '',
				'icon_size'       => '',
				'icon_color'      => '',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'hover_border_css' => '',

				'hover_icon_type'       => 'icon',
				'hover_icon'            => '',
				'hover_icon_character'  => '',
				'hover_icon_image'      => '',
				'hover_icon_image_size' => 'thumbnail',
				'hover_icon_preset'     => '',
				'hover_icon_color'      => '',

				'hover_background_type'       => 'color',
				'hover_background_color'      => '',
				'hover_background_image'      => '',
				'hover_background_image_size' => 'large',
				'hover_background_repeat'     => 'no-repeat',
				'hover_background_size'       => 'initial',
				'hover_background_position'   => 'middle-center',
				'hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

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

			$url_settings = MPC_Parser::url( $atts[ 'url' ] );
			$wrapper      = $url_settings != '' ? 'a' : 'div';

			// Hover icon defaults to base icon
			if ( $atts[ 'icon_type' ] == $atts[ 'hover_icon_type' ] ) {
				if ( $atts[ 'hover_icon_type' ] == 'icon' && $atts[ 'hover_icon' ] == '' ) {
					$atts[ 'hover_icon' ] = $atts[ 'icon' ];
				} elseif ( $atts[ 'hover_icon_type' ] == 'character' && $atts[ 'hover_icon_character' ] == '' ) {
					$atts[ 'hover_icon_character' ] = $atts[ 'icon_character' ];
				} elseif ( $atts[ 'hover_icon_type' ] == 'image' && $atts[ 'hover_icon_image' ] == '' ) {
					$atts[ 'hover_icon_image' ]      = $atts[ 'icon_image' ];
					$atts[ 'hover_icon_image_size' ] = $atts[ 'icon_image_size' ];
				}
			}

			$icon       = MPC_Parser::icon( $atts );
			$hover_icon = MPC_Parser::icon( $atts, 'hover' );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$atts_tooltip = MPC_Parser::shortcode( $atts, 'mpc_tooltip_' );
			$tooltip      = $atts[ 'mpc_tooltip__disable' ] == '' ? $MPC_Tooltip->shortcode_template( $atts_tooltip ) : '';

			$animation = MPC_Parser::animation( $atts );
			$classes   = ' mpc-init mpc-transition';
			$classes   .= $animation != '' ? ' mpc-animation' : '';
			$classes   .= $tooltip != '' ? ' mpc-tooltip-target' : '';
			$classes   .= ' mpc-effect-' . esc_attr( $atts[ 'transition' ] );
			$classes   .= $hover_icon[ 'class' ] != '' || $hover_icon[ 'content' ] != '' ? ' mpc-icon-hover' : '';
			$classes   .= $atts[ 'icon_type' ] == 'image' ? ' mpc-icon--image' : '';
			$classes   .= $atts[ 'icon_type' ] == 'character' ? ' mpc-icon--character' : '';
			$classes   .= ' ' . esc_attr( $atts[ 'class' ] );

			$return = $tooltip != '' ? '<div class="mpc-tooltip-wrap" data-id="' . $css_id . '">' : '';
				$return .= '<' . $wrapper . $url_settings . ' data-id="' . $css_id . '" class="mpc-icon' . $classes . '"' . $animation . '>';
					$return .= '<div class="mpc-icon-wrap">';
						$return .= '<i class="mpc-icon-part mpc-regular mpc-transition ' . $icon[ 'class' ] . '">' . $icon[ 'content' ] . '</i>';
						if ( $hover_icon[ 'class' ] != '' || $hover_icon[ 'content' ] != '' ) {
							$return .= '<i class="mpc-icon-part mpc-hover mpc-transition ' . $hover_icon[ 'class' ] . '">' . $hover_icon[ 'content' ] . '</i>';
						}
					$return .= '</div>';
				$return .= '</' . $wrapper . '>';
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
			$css_id = uniqid( 'mpc_icon-' . rand( 1, 100 ) );
			$style = '';
			$disabled_tooltip = $styles[ 'mpc_tooltip__disable' ] != '' || ( $styles[ 'mpc_tooltip__disable' ] == '' && $styles[ 'mpc_tooltip__text' ] == '' );

			// Add 'px'
			$styles[ 'icon_size' ] = $styles[ 'icon_size' ] != '' ? $styles[ 'icon_size' ] . ( is_numeric( $styles[ 'icon_size' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] && $disabled_tooltip ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-icon[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'margin_css' ] && ! $disabled_tooltip ) {
				$style .= '.mpc-tooltip-wrap[data-id="' . $css_id . '"] {';
					$style .= $styles[ 'margin_css' ];
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'icon_size' ] ) { $inner_styles[] = 'font-size:' . $styles[ 'icon_size' ] . ';'; }
			if ( $styles[ 'icon_color' ] ) { $inner_styles[] = 'color:' . $styles[ 'icon_color' ] . ';'; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-icon[data-id="' . $css_id . '"] i {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Hover
			$inner_styles = array();
			if ( $styles[ 'hover_border_css' ] ) { $inner_styles[] = $styles[ 'hover_border_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'hover' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-icon.mpc-icon-hover[data-id="' . $css_id . '"]:hover,';
				$style .= '.mpc-parent-hover:hover .mpc-icon.mpc-icon-hover[data-id="' . $css_id . '"],';
				$style .= '.mpc-active .mpc-icon.mpc-icon-hover[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'hover_icon_color' ] ) {
				$style .= '.mpc-icon.mpc-icon-hover[data-id="' . $css_id . '"]:hover i,';
				$style .= '.mpc-parent-hover:hover .mpc-icon.mpc-icon-hover[data-id="' . $css_id . '"] i,';
				$style .= '.mpc-active .mpc-icon.mpc-icon-hover[data-id="' . $css_id . '"] i {';
					$style .= 'color:' . $styles[ 'hover_icon_color' ] . ';';
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
					'heading'          => __( 'Hover Effect', 'mpc' ),
					'param_name'       => 'transition',
					'admin_label'      => true,
					'tooltip'          => __( 'Select icon hover animation style:<br><b>Fade</b>: simple fade in effect;<br><b>Push Out</b>: push out icon with hover icon from selected side.', 'mpc' ),
					'value'            => array(
						__( 'None', 'mpc' )                   => 'none',
						__( 'Fade', 'mpc' )                   => 'fade',
						__( 'Push Out - from Top', 'mpc' )    => 'slide-up',
						__( 'Push Out - from Right', 'mpc' )  => 'slide-right',
						__( 'Push Out - from Bottom', 'mpc' ) => 'slide-down',
						__( 'Push Out - from Left', 'mpc' )   => 'slide-left',
					),
					'std'              => 'none',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'vc_link',
					'heading'          => __( 'Link', 'mpc' ),
					'param_name'       => 'url',
					'admin_label'      => true,
					'tooltip'          => __( 'Choose target link for icon.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-8 vc_column mpc-advanced-field',
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

			$icon       = MPC_Snippets::vc_icon();
			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();

			$hover_atts = array( 'prefix' => 'hover', 'subtitle' => __( 'Hover', 'mpc' ) );

			$hover_icon       = MPC_Snippets::vc_icon( array( 'prefix' => 'hover', 'subtitle' => __( 'Hover', 'mpc' ), 'with_size' => false ) );
			$hover_background = MPC_Snippets::vc_background( $hover_atts );
			$hover_border     = MPC_Snippets::vc_border( $hover_atts );

			$animation = MPC_Snippets::vc_animation();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$icon,
				$background,
				$border,
				$padding,
				$margin,
				$hover_icon,
				$hover_background,
				$hover_border,
				$integrate_tooltip,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Icon', 'mpc' ),
				'description' => __( 'Customizable icon', 'mpc' ),
				'base'        => 'mpc_icon',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-icon.png',
				'icon'        => 'mpc-shicon-icon',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Icon' ) ) {
	global $MPC_Icon;
	$MPC_Icon = new MPC_Icon;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_icon' ) ) {
	class WPBakeryShortCode_mpc_icon extends MPCShortCode_Base {}
}

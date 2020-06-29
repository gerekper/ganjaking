<?php
/*----------------------------------------------------------------------------*\
	HOTSPOT SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Hotspot' ) ) {
	class MPC_Hotspot {
		public $shortcode = 'mpc_hotspot';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_hotspot', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			add_action( 'wp_ajax_mpc_hotspot_get_image', array( $this, 'get_image' ) );
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_hotspot-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_hotspot/css/mpc_hotspot.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_hotspot-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_hotspot/js/mpc_hotspot' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $MPC_Tooltip, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                       => '',
				'preset'                      => '',
				'url'                         => '',
				'position'                    => '50||50',

				'padding_css'                 => '',
				'margin_css'                  => '',
				'border_css'                  => '',

				'icon_type'                   => 'icon',
				'icon'                        => '',
				'icon_preset'                 => '',
				'icon_character'              => '',
				'icon_color'                  => '#333333',
				'icon_size'                   => '',
				'icon_image'                  => '',
				'icon_image_size'             => 'thumbnail',

				'icon_effect'                 => '',

				'background_type'             => 'color',
				'background_color'            => '',
				'background_image'            => '',
				'background_image_size'       => 'large',
				'background_repeat'           => 'no-repeat',
				'background_size'             => 'initial',
				'background_position'         => 'middle-center',
				'background_gradient'         => '#83bae3||#80e0d4||0;100||180||linear',

				'hover_border_css'            => '',

				'hover_icon_color'            => '',

				'hover_background_type'       => 'color',
				'hover_background_color'      => '',
				'hover_background_image'      => '',
				'hover_background_image_size' => 'large',
				'hover_background_repeat'     => 'no-repeat',
				'hover_background_size'       => 'initial',
				'hover_background_position'   => 'middle-center',
				'hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'animation_in_type'           => 'none',
				'animation_in_duration'       => '300',
				'animation_in_delay'          => '0',
				'animation_in_offset'         => '100',

				'animation_loop_type'         => 'none',
				'animation_loop_duration'     => '1000',
				'animation_loop_delay'        => '1000',
				'animation_loop_hover'        => '',

				/* Tooltip */
				'mpc_tooltip__disable'               => '',

				'mpc_tooltip__preset'                => '',
				'mpc_tooltip__text'                  => '',
				'mpc_tooltip__trigger'               => 'hover',
				'mpc_tooltip__position'              => 'top',
				'mpc_tooltip__show_effect'           => '',
				'mpc_tooltip__disable_arrow'         => '',
				'mpc_tooltip__disable_hover'         => '',
				'mpc_tooltip__always_visible'         => '',
				'mpc_tooltip__enable_wide'           => '',

				'mpc_tooltip__font_preset'           => '',
				'mpc_tooltip__font_color'            => '',
				'mpc_tooltip__font_size'             => '',
				'mpc_tooltip__font_line_height'      => '',
				'mpc_tooltip__font_align'            => '',
				'mpc_tooltip__font_transform'        => '',

				'mpc_tooltip__padding_css'           => '',
				'mpc_tooltip__border_css'            => '',

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
			$wrapper = $url_settings != '' ? 'a' : 'div';

			MPC_Helper::add_icon_font( $atts[ 'icon' ] );

			$icon = MPC_Parser::icon( $atts );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$atts_tooltip = MPC_Parser::shortcode( $atts, 'mpc_tooltip_' );
			$tooltip      = $atts[ 'mpc_tooltip__disable' ] == '' ? $MPC_Tooltip->shortcode_template( $atts_tooltip ) : '';

			$animation = MPC_Parser::animation( $atts );
			$classes   = ' mpc-init mpc-transition';
			$classes   .= $tooltip != '' ? ' mpc-tooltip-wrap' : '';
			$classes   .= ' ' . esc_attr( $atts[ 'class' ] );

			$icon_classes = $icon[ 'class' ];
			$icon_classes .= ' mpc-init mpc-icon-type--' . esc_attr( $atts[ 'icon_type' ] );
			$icon_classes .= $animation != '' ? ' mpc-animation' : '';
			$icon_classes .= $tooltip != '' ? ' mpc-tooltip-target' : '';

			$position = isset( $atts[ 'position' ] ) ? explode( '||', $atts[ 'position' ] ) : '';
			$position = !empty( $position ) ? " data-position='" . json_encode( $position ) . "'" : '';

			$return = '<' . $wrapper . $url_settings . ' id="' . $css_id . '" class="mpc-hotspot' . $classes . '" ' . $position . '>';
				$return .= '<i class="mpc-hotspot__icon mpc-transition ' . $icon_classes . '"' . $animation . '>' . $icon[ 'content' ] . '</i>';
				$return .= $tooltip;
			$return .= '</' . $wrapper . '>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_hotspot-' . rand( 1, 1000 ) );
			$style = '';

			$position = isset( $styles[ 'position' ] ) ? explode( '||', $styles[ 'position' ] ) : '';

			// Add 'px'
			$styles[ 'icon_size' ] = $styles[ 'icon_size' ] != '' ? $styles[ 'icon_size' ] . ( is_numeric( $styles[ 'icon_size' ] ) ? 'px' : '' ) : '';

			// Regular
			if ( count( $position ) == 2 ) {
				$style .= '.mpc-hotspot[id="' . $css_id . '"] {';
					$style .= 'left: ' . $position[ 0 ] . '%;top: ' . $position[ 1 ] . '%;';;
				$style .= '}';
			}

			// Icon
			$inner_styles = array();
			if ( $styles[ 'icon_size' ] ) { $inner_styles[] = 'font-size:' . $styles[ 'icon_size' ] . ';'; }
			if ( $styles[ 'icon_color' ] ) { $inner_styles[] = 'color:' . $styles[ 'icon_color' ] . ';'; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-hotspot[id="' . $css_id . '"] .mpc-hotspot__icon {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Hover
			$inner_styles = array();
			if ( $styles[ 'hover_border_css' ] ) { $inner_styles[] = $styles[ 'hover_border_css' ]; }
			if ( $styles[ 'hover_icon_color' ] ) { $inner_styles[] = 'color:' . $styles[ 'hover_icon_color' ] . ';'; }
			if ( $temp_style = MPC_CSS::background( $styles, 'hover' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-hotspot[id="' . $css_id . '"]:hover .mpc-hotspot__icon {';
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

			global $mpc_js_localization;
			$mpc_js_localization[ 'mpc_hotspot' ] = array(
				'set_position'  => __( 'Set Position', 'mpc' ),
				'no_background' => __( 'Please set background for Interactive Image.', 'mpc' ),
			);

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
					'type'        => 'vc_link',
					'heading'     => __( 'Link', 'mpc' ),
					'param_name'  => 'url',
					'admin_label' => true,
					'tooltip'     => __( 'Choose target link for hotspot.', 'mpc' ),
					'value'       => '',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Position', 'mpc' ),
					'param_name'       => 'position',
					'admin_label'      => true,
					'tooltip'          => __( 'Define hotspot position on background image. By clicking on <b>Set Position</b> button you can drag the hotspot point to the desired position.', 'mpc' ),
					'value'            => '50||50',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-admin-post',
						'align' => 'prepend',
					),
					'label'            => '%',
					'validate'         => false,
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-input--medium',
				),
			);

			$hover = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Hover - Icon', 'mpc' ),
					'param_name' => 'hover_divider',
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'mpc' ),
					'param_name' => 'hover_icon_color',
					'tooltip'    => __( 'If you want to change the icon color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'      => '',
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

			$hover_background = MPC_Snippets::vc_background( $hover_atts );
			$hover_border     = MPC_Snippets::vc_border( $hover_atts );

			$animation = MPC_Snippets::vc_animation();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge( $base, $icon, $background, $border, $padding, $margin, $hover, $hover_background, $hover_border, $integrate_tooltip, $animation, $class );

			return array(
				'name'            => __( 'Hotspot', 'mpc' ),
				'description'     => __( 'Icon point for interactive images', 'mpc' ),
				'base'            => 'mpc_hotspot',
				'class'           => '',
//				'icon'            => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-interactive-image.png',
				'icon'            => 'mpc-shicon-hotspot',
				'category'        => __( 'Massive', 'mpc' ),
				'as_child'        => array( 'only' => 'mpc_interactive_image' ),
				'content_element' => true,
				'params'          => $params,
			);
		}

		/* Get background image for setting coordinates */
		function get_image() {
			if ( ! isset ( $_POST[ 'image_id' ] ) ) {
				die( 'error' );
			}

			$background_image = wp_get_attachment_image_src( $_POST[ 'image_id' ], 'full' );

			if ( $background_image === false ) {
				die( 'error' );
			}

			echo '<div class="mpc-coords"><img class="mpc-coords__image" src="' . $background_image[ 0 ] . '" width="' . $background_image[ 1 ] . '" height="' . $background_image[ 2 ] . '" /><div class="mpc-coords__point"></div><div class="mpc-coords__overlay"></div></div>';
			die();
		}
	}
}
if ( class_exists( 'MPC_Hotspot' ) ) {
	global $MPC_Hotspot;
	$MPC_Hotspot = new MPC_Hotspot;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_hotspot' ) ) {
	class WPBakeryShortCode_mpc_hotspot extends MPCShortCode_Base {}
}

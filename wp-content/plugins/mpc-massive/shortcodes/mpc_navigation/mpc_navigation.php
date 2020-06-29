<?php
/*----------------------------------------------------------------------------*\
	NAVIGATION SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Navigation' ) ) {
	class MPC_Navigation {
		public $shortcode       = 'mpc_navigation';
		public $panel_section   = array();
		public $defaults        = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_navigation', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			$this->defaults = array(
				'class'                       => '',
				'preset'                      => '',
				'layout'                      => 'style_1',
				'overlay_color'               => '',
				'overlay_padding_css'         => '',
				'button_gap'                  => '',
				'button_align'                => 'left',
				'on_hover'                    => '',

				'next_icon_type'              => 'icon',
				'next_icon'                   => '',
				'next_icon_character'         => '',
				'next_icon_preset'            => '',
				'next_icon_color'             => '#333333',
				'next_icon_size'              => '',
				'next_icon_effect'            => 'stay-left',

				'previous_icon_type'          => 'icon',
				'previous_icon'               => '',
				'previous_icon_character'     => '',
				'previous_icon_preset'        => '',
				'previous_icon_color'         => '#333333',
				'previous_icon_size'          => '',
				'previous_icon_effect'        => 'stay-left',

				'padding_css'                 => '',
				'prev_margin_css'             => '',
				'next_margin_css'             => '',
				'border_css'                  => '',

				'background_type'             => 'color',
				'background_color'            => '',
				'background_repeat'           => 'no-repeat',
				'background_size'             => 'initial',
				'background_position'         => 'middle-center',
				'background_gradient'         => '#83bae3||#80e0d4||0;100||180||linear',
				'background_image'            => '',
				'background_image_size'       => 'large',

				'hover_icon_color'            => '',
				'hover_border_color'          => '',

				'hover_background_type'       => 'color',
				'hover_background_color'      => '',
				'hover_background_repeat'     => 'no-repeat',
				'hover_background_size'       => 'initial',
				'hover_background_position'   => 'middle-center',
				'hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'hover_background_image'      => '',
				'hover_background_image_size' => 'large',

				'hover_background_effect'     => 'fade-in',
				'hover_background_offset'     => '',
			);
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Build shortcode layout */
		function shortcode_layout( $style, $parts ) {
			$content = '';

			$layouts = array(
				'style_1' => array( 'carousel', 'begin', 'prev', 'next', 'end' ),
				'style_2' => array( 'begin', 'prev', 'next', 'end', 'carousel' ),
				'style_3' => array( 'carousel', 'begin', 'prev', 'end', 'begin', 'next', 'end'  ),
				'style_4' => array( 'carousel', 'begin', 'prev', 'end', 'begin', 'next', 'end'  ),
				'style_5' => array( 'carousel', 'begin', 'prev', 'end', 'begin', 'next', 'end' ),
				'style_6' => array( 'carousel', 'begin', 'prev', 'end', 'begin', 'next', 'end' ),
			);

			if( ! isset( $layouts[ $style ] ) )
				return '';

			foreach( $layouts[ $style ] as $part ) {
				$content .= $parts[ $part ];
			}

			return $content;
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_navigation-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_navigation/css/mpc_navigation.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_navigation-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_navigation/js/mpc_navigation' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $name, $content = null, $css_id = '', $type = '', $carousel = '' ) {
			global $mpc_navigation_presets, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			if ( is_array( $name ) ) {
				return '';
			}

			$save_default = false;

			if ( isset( $mpc_navigation_presets[ $name ] ) ) {
				$atts = $mpc_navigation_presets[ $name ];
				$atts[ 'preset' ] = $name;
			} else {
				$navigation_presets = get_option( 'mpc_presets_mpc_navigation' );

				$navigation_presets = json_decode( $navigation_presets, true );

				if ( isset( $navigation_presets[ $name ] ) ) {
					$atts = $navigation_presets[ $name ];
					$atts[ 'preset' ] = $name;

					$mpc_navigation_presets[ $name ] = $atts;
				} else {
					$atts = array();
					$save_default = true;
				}
			}

			$atts = shortcode_atts( $this->defaults, $atts );

			if ( $save_default && ! isset( $mpc_navigation_presets[ 'default' ] ) ) {
				$mpc_navigation_presets[ 'default' ] = $atts;
			}

			$previous_icon = MPC_Parser::icon( $atts, 'previous' );
			$next_icon     = MPC_Parser::icon( $atts, 'next' );

			$background_effect = explode( '-', $atts[ 'hover_background_effect' ] );
			if ( ! count( $background_effect ) == 2 ) {
				$background_effect = array( '', '' );
			}
			$background_effect_type = $background_effect[ 0 ] != '' ? 'mpc-effect-type--' . esc_attr( $background_effect[ 0 ] ) : '';
			$background_effect_side = $background_effect[ 1 ] != '' ? 'mpc-effect-side--' . esc_attr( $background_effect[ 1 ] ) : '';

			$classes = ' mpc-init';
			$classes .= $atts[ 'button_align' ] != '' ? ' mpc-align--' . esc_attr( $atts[ 'button_align' ] ) : '';
			$classes .= $atts[ 'layout' ] != '' ? ' mpc-navigation--' . esc_attr( $atts[ 'layout' ] ) : '';
			$classes .= $atts[ 'on_hover' ] == 'true' ? ' mpc-on-hover' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$parts            = array();
			$parts[ 'begin' ] = '<div class="mpc-navigation' . $classes . ' mpc-nav-preset--' . esc_attr( $atts[ 'preset' ] ) . '" data-mpcslider="' . $css_id . '">';
			$parts[ 'end' ]   = '</div>';
			$parts[ 'background' ] = '<div class="mpc-nav__background mpc-transition ' . $background_effect_type . ' ' . $background_effect_side . '"></div>';
			$parts[ 'prev' ]  = '<div class="mpc-nav__arrow mpcslick-prev"><div class="mpc-nav__icon mpc-transition"><i class="' . $previous_icon[ 'class' ] . '">' . $previous_icon[ 'content' ] . $parts[ 'background' ] . '</i></div></div>';
			$parts[ 'next' ]  = '<div class="mpc-nav__arrow mpcslick-next"><div class="mpc-nav__icon mpc-transition"><i class="' . $next_icon[ 'class' ] . '">' . $next_icon[ 'content' ] . $parts[ 'background' ] . '</i></div></div>';

			$parts[ 'carousel' ] = $carousel;

			$return = $this->shortcode_layout( $atts[ 'layout' ], $parts );

			return $return;
		}

		function shortcode_styles( $styles, $preset ) {
			$style = '';

			// Add 'px'
			$styles[ 'previous_icon_size' ] = $styles[ 'previous_icon_size' ] != '' ? $styles[ 'previous_icon_size' ] . ( is_numeric( $styles[ 'previous_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'next_icon_size' ] = $styles[ 'next_icon_size' ] != '' ? $styles[ 'next_icon_size' ] . ( is_numeric( $styles[ 'next_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'hover_background_offset' ] = $styles[ 'hover_background_offset' ] != '' ? $styles[ 'hover_background_offset' ] . ( is_numeric( $styles[ 'hover_background_offset' ] ) ? '%' : '' ) : '';

			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-nav-preset--' . $preset . ' i {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'overlay_padding_css' ] ) { $inner_styles[] = $styles[ 'overlay_padding_css' ]; }
			if ( $styles[ 'overlay_color' ] ) { $inner_styles[] = 'background:' . $styles[ 'overlay_color' ] . ';'; }
			if ( in_array( $styles[ 'layout' ], array( 'style_3', 'style_5', 'style_6' ) ) && count( $inner_styles ) > 0 ) {
				$style .= '.mpc-nav-preset--' . $preset . ' .mpc-nav__arrow { ';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $temp_style = $styles[ 'prev_margin_css' ] ) {
				$style .= '.mpc-nav-preset--' . $preset . ' .mpcslick-prev { ';
					$style .= $temp_style;
				$style .= '}';
			}
			if ( $temp_style = $styles[ 'next_margin_css' ] ) {
				$style .= '.mpc-nav-preset--' . $preset . ' .mpcslick-next { ';
					$style .= $temp_style;
				$style .= '}';
			}

			$inner_styles = array();
			if ( $temp_style = MPC_CSS::icon( $styles, 'previous' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-nav-preset--' . $preset . ' .mpcslick-prev i {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::icon( $styles, 'next' ) ) {
				$style .= '.mpc-nav-preset--' . $preset . ' .mpcslick-next i {';
					$style .= $temp_style;
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'hover_icon_color' ] ) { $inner_styles[] = 'color:' . $styles[ 'hover_icon_color' ] . ';'; }
			if ( $styles[ 'hover_border_color' ] ) { $inner_styles[] = 'border-color:' . $styles[ 'hover_border_color' ] . ';'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-nav-preset--' . $preset . ' i:hover {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Background effects
			if ( $temp_style = MPC_CSS::background( $styles, 'hover' ) ) {
				$style .= '.mpc-nav-preset--' . $preset . ' .mpc-nav__background {';
					$style .= $temp_style;
				$style .= '}';
			}
			if ( $styles[ 'hover_background_offset' ] ) {
				$temp_style = '';

				if ( $styles[ 'hover_background_effect' ] == 'expand-horizontal' ) {
					$temp_style = 'left:' . $styles[ 'hover_background_offset' ] . '  !important;right:' . $styles[ 'hover_background_offset' ] . '  !important;';
				} elseif ( $styles[ 'hover_background_effect' ] == 'expand-vertical' ) {
					$temp_style = 'top:' . $styles[ 'hover_background_offset' ] . '  !important;bottom:' . $styles[ 'hover_background_offset' ] . '  !important;';
				} elseif ( $styles[ 'hover_background_effect' ] == 'expand-diagonal_left' || $styles[ 'hover_background_effect' ] == 'expand-diagonal_right' ) {
					$temp_style = 'top:-' . $styles[ 'hover_background_offset' ] . '  !important;bottom:-' . $styles[ 'hover_background_offset' ] . '  !important;';
				}

				if ( $temp_style ) {
					$style .= '.mpc-nav-preset--' . $preset . ' i:hover .mpc-nav__background {';
						$style .= $temp_style;
					$style .= '}';
				}
			}

			return $style;
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return '';
			}

			$base = array(
				array(
					'type'            => 'mpc_preset',
					'heading'         => __( 'Main Preset', 'mpc' ),
					'param_name'      => 'preset',
					'tooltip'         => MPC_Helper::style_presets_desc(),
					'value'           => '',
					'shortcode'       => $this->shortcode,
					'sub_type'        => 'navigation',
				),
				array(
					'type'        => 'mpc_layout_select',
					'heading'     => __( 'Layout Select', 'mpc' ),
					'param_name'  => 'layout',
					'tooltip'     => __( 'Layout styles let you choose the target layout after you define the shortcode options.', 'mpc' ),
					'value'       => 'style_1',
					'columns'     => '3',
					'layouts'     => array(
						'style_1' => '5',
						'style_2' => '5',
						'style_3' => '4',
						'style_4' => '4',
						'style_5' => '4',
						'style_6' => '4',
					),
					'std'         => 'style_1',
					'shortcode'   => $this->shortcode,
					'description' => __( 'Choose layout style.', 'mpc' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Show on Hover', 'mpc' ),
					'param_name'       => 'on_hover',
					'tooltip'          => __( 'Enable to show navigation after user hovers over the slider.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => false,
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'dependency'       => array( 'element' => 'layout', 'value' => array( 'style_3', 'style_4', 'style_5', 'style_6' ) ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Overlay Color', 'mpc' ),
					'param_name'       => 'overlay_color',
					'tooltip'          => __( 'Choose overlay color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-8 vc_column mpc-color-picker',
					'dependency'       => array( 'element' => 'layout', 'value' => array( 'style_3', 'style_4', 'style_5', 'style_6' ) ),
				),
			);

			$buttons = array(
				array(
					'type'             => 'mpc_align',
					'heading'          => __( 'Buttons Position', 'mpc' ),
					'param_name'       => 'button_align',
					'tooltip'          => __( 'Choose buttons position.', 'mpc' ),
					'value'            => 'left',
					'grid_size'        => 'small',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'layout', 'value' => array( 'style_1', 'style_2' ) ),
				),
			);

			$hover = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Hover', 'mpc' ),
					'param_name' => 'hover_divider',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Icon Color', 'mpc' ),
					'param_name'       => 'hover_icon_color',
					'tooltip'          => __( 'If you want to change the icon color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Border Color', 'mpc' ),
					'param_name'       => 'hover_border_color',
					'tooltip'          => __( 'If you want to change the border color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
			);

			$background_effect = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Hover Effect', 'mpc' ),
					'param_name'       => 'hover_background_effect',
					'tooltip'          => __( 'Choose background hover animation style.', 'mpc' ),
					'value'            => array(
						__( 'Fade In', 'mpc' )                 => 'fade-in',
						__( 'Slide - Top', 'mpc' )             => 'slide-top',
						__( 'Slide - Right', 'mpc' )           => 'slide-right',
						__( 'Slide - Bottom', 'mpc' )          => 'slide-bottom',
						__( 'Slide - Left', 'mpc' )            => 'slide-left',
						__( 'Expand - Horizontal', 'mpc' )     => 'expand-horizontal',
						__( 'Expand - Vertical', 'mpc' )       => 'expand-vertical',
						__( 'Expand - Diagonal Left', 'mpc' )  => 'expand-diagonal_left',
						__( 'Expand - Diagonal Right', 'mpc' ) => 'expand-diagonal_right',
					),
					'std'              => 'fade-in',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Custom Offset', 'mpc' ),
					'param_name'       => 'hover_background_offset',
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => '',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array(
						'element' => 'hover_background_effect',
						'value'   => array( 'expand-horizontal', 'expand-vertical', 'expand-diagonal_left', 'expand-diagonal_right' )
					),
				),
			);

			$previous_icon = MPC_Snippets::vc_icon( array( 'prefix' => 'previous', 'subtitle' => __( 'Previous', 'mpc' ) ) );
			$next_icon     = MPC_Snippets::vc_icon( array( 'prefix' => 'next', 'subtitle' => __( 'Next', 'mpc' ) ) );
			$background    = MPC_Snippets::vc_background();
			$border        = MPC_Snippets::vc_border();
			$padding       = MPC_Snippets::vc_padding();
			$prev_margin   = MPC_Snippets::vc_margin( array( 'prefix' => 'prev', 'subtitle' => __( 'Previous', 'mpc' ) ) );
			$next_margin   = MPC_Snippets::vc_margin( array( 'prefix' => 'next', 'subtitle' => __( 'Next', 'mpc' ) ) );

			$overlay_padding = MPC_Snippets::vc_padding( array( 'prefix' => 'overlay', 'subtitle' => __( 'Overlay', 'mpc' ), 'dependency' => array( 'element' => 'layout', 'value' =>  array( 'style_3', 'style_5', 'style_6' ) ) ) );

			$hover_background = MPC_Snippets::vc_background( array( 'prefix' => 'hover', 'subtitle' => __( 'Hover', 'mpc' ) ) );

			$class = MPC_Snippets::vc_class();

			$params = array_merge( $base, $buttons, $overlay_padding, $previous_icon, $prev_margin, $next_icon, $next_margin, $background, $border, $padding, $hover, $hover_background, $background_effect, $class );

			return array(
				'name'        => __( 'Navigation', 'mpc' ),
				'description' => __( 'Navigation buttons', 'mpc' ),
				'base'        => 'mpc_navigation',
				'class'       => '',
				'icon'        => 'mpc-icon-navigation',
				'category'    => __( 'Massive', 'mpc' ),
				'as_child'    => array( 'only' => '' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Navigation' ) ) {
	global $MPC_Navigation;
	$MPC_Navigation = new MPC_Navigation;
}
if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_mpc_navigation' ) ) {
	class WPBakeryShortCode_mpc_navigation extends WPBakeryShortCode {}
}

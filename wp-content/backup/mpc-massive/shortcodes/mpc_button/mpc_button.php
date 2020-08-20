<?php
/*----------------------------------------------------------------------------*\
	BUTTON SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Button' ) ) {
	class MPC_Button {
		public $shortcode = 'mpc_button';
		public $style = '';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_button', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_button-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_button/css/mpc_button.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_button-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_button/js/mpc_button' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null, $shortcode = null, $parent_css = null ) {
			global $MPC_Tooltip, $mpc_ma_options, $mpc_can_link, $mpc_button_separator;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'  => '',
				'preset' => '',
				'title'  => '',
				'url'    => '',
				'block'  => '',

				'font_preset'      => '',
				'font_color'       => '',
				'font_size'        => '',
				'font_line_height' => '',
				'font_align'       => '',
				'font_transform'   => '',

				'padding_css' => '',
				'margin_css'  => '',
				'border_css'  => '',

				'icon_type'       => 'icon',
				'icon'            => '',
				'icon_character'  => '',
				'icon_image'      => '',
				'icon_image_size' => 'thumbnail',
				'icon_preset'     => '',
				'icon_color'      => '#333333',
				'icon_size'       => '',

				'icon_effect' => 'none-none',
				'icon_gap'    => '',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'hover_background_effect' => 'fade-in',
				'hover_background_offset' => '',

				'hover_border_css' => '',

				'hover_font_color' => '',
				'hover_icon_color' => '',

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

			$url_settings =  $mpc_can_link ? MPC_Parser::url( $atts[ 'url' ] ) : '';
			$wrapper = $url_settings != '' ? 'a' : 'div';

			$icon = MPC_Parser::icon( $atts );

			$icon_effect = explode( '-', $atts[ 'icon_effect' ] );
			if ( ! count( $icon_effect ) == 2 ) {
				$icon_effect = array( '', '' );
			}
			$icon_effect_type = $icon_effect[ 0 ] != '' ? 'mpc-effect-type--' . $icon_effect[ 0 ] : '';
			$icon_effect_side = $icon_effect[ 1 ] != '' ? 'mpc-effect-side--' . $icon_effect[ 1 ] : '';

			$background_effect = explode( '-', $atts[ 'hover_background_effect' ] );
			if ( ! count( $background_effect ) == 2 ) {
				$background_effect = array( '', '' );
			}
			$background_effect_type = $background_effect[ 0 ] != '' ? 'mpc-effect-type--' . $background_effect[ 0 ] : '';
			$background_effect_side = $background_effect[ 1 ] != '' ? 'mpc-effect-side--' . $background_effect[ 1 ] : '';

			$styles = $this->shortcode_styles( $atts, $parent_css );
			$css_id = $styles[ 'id' ];
			$css_id = ! empty( $parent_css ) ? '' : ' data-id="' . $css_id . '"';

			$atts_tooltip = MPC_Parser::shortcode( $atts, 'mpc_tooltip_' );
			$tooltip      = $atts[ 'mpc_tooltip__disable' ] == '' ? $MPC_Tooltip->shortcode_template( $atts_tooltip ) : '';

			$animation = MPC_Parser::animation( $atts );
			$classes   = ' mpc-init mpc-transition';   //
			$classes   .= $animation != '' ? ' mpc-animation' : '';
			$classes   .= $tooltip != '' ? ' mpc-tooltip-target' : '';
			$classes   .= $atts[ 'font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'font_preset' ] : '';
			$classes   .= $atts[ 'block' ] != '' && ! isset( $mpc_button_separator ) ? ' mpc-display--block' : '';
			$classes   .= ' ' . esc_attr( $atts[ 'class' ] );

			$classes_tooltip = $atts[ 'block' ] != '' && ! isset( $mpc_button_separator ) ? ' mpc-display--block' : '';
			$classes_icon = $atts[ 'icon_type' ] == 'image' ? ' mpc-icon--image' : '';

			$return = $tooltip != '' ? '<div class="mpc-tooltip-wrap' . $classes_tooltip . '"' . $css_id . '>' : '';
				$return .= '<' . $wrapper . $url_settings . $css_id . ' class="mpc-button' . $classes . '" ' . $animation . '>';
					$return .= '<div class="mpc-button__content ' . $icon_effect_type . ' ' . $icon_effect_side . '">';
						if ( $icon_effect[ 1 ] == 'left' || $icon_effect[ 1 ] == 'top' ) {
							$return .= '<i class="mpc-button__icon mpc-transition ' . $icon[ 'class' ] . $classes_icon . '">' . $icon[ 'content' ] . '</i>';
						}
						if( $atts[ 'title' ] != '' ) {
							$return .= '<span class="mpc-button__title mpc-transition">' . $atts[ 'title' ] . '</span>';
						}
						if ( $icon_effect[ 1 ] == 'right' || $icon_effect[ 1 ] == 'bottom' ) {
							$return .= '<i class="mpc-button__icon mpc-transition ' . $icon[ 'class' ] . $classes_icon . '">' . $icon[ 'content' ] . '</i>';
						}
						$return .= '</div>';
						$return .= '<div class="mpc-button__background mpc-transition ' . $background_effect_type . ' ' . $background_effect_side . '"></div>';
					$return .= '</' . $wrapper . '>';
				$return .= $tooltip;
			$return .= $tooltip != '' ? '</div>' : '';

			if ( isset( $mpc_button_separator ) ) {
				$return .= $mpc_button_separator;
			}

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles, $parent_css = '' ) {
			global $mpc_massive_styles;
			$css_id       = uniqid( 'mpc_button-' . rand( 1, 100 ) );
			$css_selector = '.mpc-button[data-id="' . $css_id . '"]';
			$style        = '';

			if ( is_array( $parent_css ) ) {
				$css_id       = $parent_css[ 'id' ];
				$css_selector = $parent_css[ 'selector' ];
			}

			if( $this->style === $css_id ) {
				return array(
					'id'  => $css_id,
					'css' => '',
				);
			}

			$disabled_tooltip = $styles[ 'mpc_tooltip__disable' ] != '' || ( $styles[ 'mpc_tooltip__disable' ] == '' && $styles[ 'mpc_tooltip__text' ] == '' );

			// Add 'px'
			$styles[ 'font_size' ] = $styles[ 'font_size' ] != '' ? $styles[ 'font_size' ] . ( is_numeric( $styles[ 'font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'icon_size' ] = $styles[ 'icon_size' ] != '' ? $styles[ 'icon_size' ] . ( is_numeric( $styles[ 'icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'icon_gap' ] = $styles[ 'icon_gap' ] != '' ? $styles[ 'icon_gap' ] . ( is_numeric( $styles[ 'icon_gap' ] ) ? 'px' : '' ) : '';

			// Add '%'
			$styles[ 'hover_background_offset' ] = $styles[ 'hover_background_offset' ] != '' ? $styles[ 'hover_background_offset' ] . ( is_numeric( $styles[ 'hover_background_offset' ] ) ? '%' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'margin_css' ] && $disabled_tooltip ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::font( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'margin_css' ] && ! $disabled_tooltip ) {
				$style .= '.mpc-tooltip-wrap[data-id="' . $css_id . '"] {';
					$style .= $styles[ 'margin_css' ];
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'icon_gap' ] && $styles[ 'icon_effect' ] == 'stay-left' ) { $inner_styles[] = 'padding-right:' . $styles[ 'icon_gap' ] . ' !important;'; }
			if ( $styles[ 'icon_gap' ] && $styles[ 'icon_effect' ] == 'stay-right' ) { $inner_styles[] = 'padding-left:' . $styles[ 'icon_gap' ] . ' !important;'; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $temp_style = MPC_CSS::icon( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-button__icon {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'padding_css' ] ) {
				$style .= $css_selector . ' .mpc-button__title {';
					$style .= $styles[ 'padding_css' ];
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::background( $styles, 'hover' ) ) {
				$style .= $css_selector . ' .mpc-button__background {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Hover
			if ( $styles[ 'hover_border_css' ] ) {
				$style .= $css_selector . ':hover,';
				$style .= '.mpc-parent-hover:hover .mpc-button[data-id="' . $css_id . '"],';
				$style .= '.mpc-active .mpc-button[data-id="' . $css_id . '"] {';
					$style .= $styles[ 'hover_border_css' ];
				$style .= '}';
			}

			if ( $styles[ 'hover_icon_color' ] ) {
				$style .= $css_selector . ':hover .mpc-button__icon,';
				$style .= '.mpc-parent-hover:hover .mpc-button[data-id="' . $css_id . '"] .mpc-button__icon,';
				$style .= '.mpc-active .mpc-button[data-id="' . $css_id . '"] .mpc-button__icon {';
					$style .= 'color:' . $styles[ 'hover_icon_color' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'hover_font_color' ] ) {
				$style .= $css_selector . ':hover .mpc-button__title,';
				$style .= '.mpc-parent-hover:hover .mpc-button[data-id="' . $css_id . '"] .mpc-button__title,';
				$style .= '.mpc-active .mpc-button[data-id="' . $css_id . '"] .mpc-button__title {';
					$style .= 'color:' . $styles[ 'hover_font_color' ] . ';';
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
					$style .= $css_selector . ':hover .mpc-button__background,';
					$style .= '.mpc-parent-hover:hover .mpc-button[data-id="' . $css_id . '"] .mpc-button__background,';
					$style .= '.mpc-active .mpc-button[data-id="' . $css_id . '"] .mpc-button__background {';
						$style .= $temp_style;
					$style .= '}';
				}
			}

			$mpc_massive_styles .= $style;
			$this->style = $css_id;

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
					'heading'          => __( 'Full Width', 'mpc' ),
					'param_name'       => 'block',
					'tooltip'          => __( 'Check to stretch button to 100% width of its container.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'vc_link',
					'heading'          => __( 'Link', 'mpc' ),
					'param_name'       => 'url',
					'admin_label'      => true,
					'tooltip'          => __( 'Choose target link for button.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-8 vc_column',
				),
			);

			$text = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Text', 'mpc' ),
					'param_name'       => 'title',
					'admin_label'      => true,
					'tooltip'          => __( 'Define button text.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
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
					'heading'          => __( 'Title Color', 'mpc' ),
					'param_name'       => 'hover_font_color',
					'tooltip'          => __( 'If you want to change the title color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Icon Color', 'mpc' ),
					'param_name'       => 'hover_icon_color',
					'tooltip'          => __( 'If you want to change the icon color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
			);

			$background_effect = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Hover Effect', 'mpc' ),
					'param_name'       => 'hover_background_effect',
					'admin_label'      => true,
					'tooltip'          => __( 'Select background hover animation style:<br><b>Fade</b>: simple fade in effect;<br><b>Slide In</b>: slide background in from selected side;<br><b>Expand</b>: expand background from selected position.', 'mpc' ),
					'value'            => array(
						__( 'Fade', 'mpc' )                    => 'fade-in',
						__( 'Slide In - from Top', 'mpc' )     => 'slide-top',
						__( 'Slide In - from Right', 'mpc' )   => 'slide-right',
						__( 'Slide In - from Bottom', 'mpc' )  => 'slide-bottom',
						__( 'Slide In - from Left', 'mpc' )    => 'slide-left',
						__( 'Expand - Horizontal', 'mpc' )     => 'expand-horizontal',
						__( 'Expand - Vertical', 'mpc' )       => 'expand-vertical',
						__( 'Expand - Diagonal Left', 'mpc' )  => 'expand-diagonal_left',
						__( 'Expand - Diagonal Right', 'mpc' ) => 'expand-diagonal_right',
					),
					'std'              => 'fade-in',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Custom Offset', 'mpc' ),
					'param_name'       => 'hover_background_offset',
					'tooltip'          => __( 'Define custom offset for expanded background size. Offset might be different dependent on the button size.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => '%',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'hover_background_effect', 'value' => array( 'expand-horizontal', 'expand-vertical', 'expand-diagonal_left', 'expand-diagonal_right' ) ),
				),
			);

			$icon_effect = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Display Effect', 'mpc' ),
					'param_name'       => 'icon_effect',
					'admin_label'      => true,
					'tooltip'          => __( 'Select icon display style:<br><b>None</b>: hide the icon;<br><b>Stay</b>: display icon on selected side;<br><b>Slide In</b>: slide icon in from selected side;<br><b>Push Out</b>: push out button text with icon from selected side.', 'mpc' ),
					'value'            => array(
						__( 'None', 'mpc' )                   => 'none-none',
						__( 'Stay - Left', 'mpc' )            => 'stay-left',
						__( 'Stay - Right', 'mpc' )           => 'stay-right',
						__( 'Slide In - from Left', 'mpc' )   => 'slide-left',
						__( 'Slide In - from Right', 'mpc' )  => 'slide-right',
						__( 'Push Out - from Top', 'mpc' )    => 'push_out-top',
						__( 'Push Out - from Right', 'mpc' )  => 'push_out-right',
						__( 'Push Out - from Bottom', 'mpc' ) => 'push_out-bottom',
						__( 'Push Out - from Left', 'mpc' )   => 'push_out-left',
					),
					'std'           => 'none-none',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Custom Gap', 'mpc' ),
					'param_name'       => 'icon_gap',
					'tooltip'          => __( 'Define gap between icon and text.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'icon_effect', 'value' => array( 'stay-left', 'stay-right' ) ),
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
			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();
			$class      = MPC_Snippets::vc_class();

			$icon = MPC_Snippets::vc_icon( array( 'tooltip' => __( 'To create a button with an icon use the section bellow to configure it.', 'mpc' ), ) );

			$hover_background = MPC_Snippets::vc_background( array( 'prefix' => 'hover', 'subtitle' => __( 'Hover', 'mpc' ) ) );
			$hover_border     = MPC_Snippets::vc_border( array( 'prefix' => 'hover', 'subtitle' => __( 'Hover', 'mpc' ) ) );

			$animation  = MPC_Snippets::vc_animation();

			$params = array_merge(
				$base,
				$font,
				$text,
				$icon,
				$icon_effect,
				$background,
				$border,
				$padding,
				$margin,
				$hover,
				$hover_background,
				$background_effect,
				$hover_border,
				$integrate_tooltip,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Button', 'mpc' ),
				'description' => __( 'Extended styles of button', 'mpc' ),
				'base'        => 'mpc_button',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-button.png',
				'icon'        => 'mpc-shicon-button',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Button' ) ) {
	global $MPC_Button;
	$MPC_Button = new MPC_Button;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_button' ) ) {
	class WPBakeryShortCode_mpc_button extends MPCShortCode_Base {}
}

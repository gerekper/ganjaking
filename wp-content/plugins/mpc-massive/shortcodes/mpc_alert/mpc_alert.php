<?php
/*----------------------------------------------------------------------------*\
	ALERT SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Alert' ) ) {
	global $MPC_Shortcode;
	$MPC_Shortcode[ 'alerts' ] = 0;

	class MPC_Alert {
		public $shortcode = 'mpc_alert';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_alert', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			/* AJAX Cals */
			add_action( 'wp_ajax_mpc_set_alert_cookie', array( $this, 'shortcode_ajax_set_alert_cookie' ) );
			add_action( 'wp_ajax_nopriv_mpc_set_alert_cookie', array( $this, 'shortcode_ajax_set_alert_cookie' ) );
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_alert-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_alert/css/mpc_alert.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_alert-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_alert/js/mpc_alert' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* AJAX Callbacks */
		function shortcode_ajax_set_alert_cookie() {
			$alert_id  = filter_var( $_POST[ 'id' ], FILTER_SANITIZE_STRING );
			$frequency = filter_var( $_POST[ 'frequency' ], FILTER_SANITIZE_STRING );

			if ( $alert_id == '' || $frequency == '' ) {
				die();
			}

			if( $frequency == 'never' ) {
				$repeat_time = YEAR_IN_SECONDS * 100;
			} else if ( $frequency == 'weekly' ) {
				$repeat_time = WEEK_IN_SECONDS;
			} elseif ( $frequency == 'monthly' ) {
				$repeat_time = MONTH_IN_SECONDS;
			} elseif ( $frequency == 'daily' ) {
				$repeat_time = DAY_IN_SECONDS;
			} else {
				$repeat_time = 0;
			}

			setcookie( $alert_id, true, time() + $repeat_time, '/' );
			die();
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $MPC_Ribbon, $MPC_Shortcode, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'        => '',
				'preset'       => '',

				'padding_css' => '',
				'margin_css'  => '',
				'border_css'  => '',

				'disable_icon'    => '',
				'icon_type'       => 'icon',
				'icon'            => '',
				'icon_character'  => '',
				'icon_image'      => '',
				'icon_image_size' => 'thumbnail',
				'icon_preset'     => '',
				'icon_size'       => '',
				'icon_color'      => '#333333',

				'icon_border_css'  => '',
				'icon_padding_css' => '',

				'icon_background_type'       => 'color',
				'icon_background_color'      => '',
				'icon_background_image'      => '',
				'icon_background_image_size' => 'large',
				'icon_background_repeat'   => 'no-repeat',
				'icon_background_size'     => 'initial',
				'icon_background_position' => 'middle-center',
				'icon_background_gradient' => '#83bae3||#80e0d4||0;100||180||linear',

				'enable_dismiss'          => '',
				'dismiss_frequency'       => 'always',
				'dismiss_position'        => 'inside',
				'dismiss_icon_type'       => 'icon',
				'dismiss_icon'            => '',
				'dismiss_icon_character'  => '',
				'dismiss_icon_image'      => '',
				'dismiss_icon_image_size' => 'thumbnail',
				'dismiss_icon_preset'     => '',
				'dismiss_icon_size'       => '',
				'dismiss_icon_color'      => '#333333',

				'dismiss_border_css'  => '',
				'dismiss_padding_css' => '',
				'dismiss_margin_css'  => '',
				'dismiss_background'  => '',

				'hover_dismiss_color'      => '',
				'hover_dismiss_border'     => '',
				'hover_dismiss_background' => '',

				'font_preset'      => '',
				'font_color'       => '',
				'font_size'        => '',
				'font_line_height' => '',
				'font_align'       => '',
				'font_transform'   => '',

				'content_border_css'  => '',
				'content_padding_css' => '',

				'content_background_type'       => 'color',
				'content_background_color'      => '',
				'content_background_image'      => '',
				'content_background_image_size' => 'large',
				'content_background_repeat'     => 'no-repeat',
				'content_background_size'       => 'initial',
				'content_background_position'   => 'middle-center',
				'content_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',

				'animation_loop_type'       => 'none',
				'animation_loop_duration'   => '1000',
				'animation_loop_delay'      => '1000',
				'animation_loop_hover'      => '',

				/* Ribbon */
				'mpc_ribbon__disable'       => '',
				'mpc_ribbon__preset'        => '',
				'mpc_ribbon__text'          => '',
				'mpc_ribbon__style'         => 'classic',
				'mpc_ribbon__alignment'     => 'top-left',
				'mpc_ribbon__corners_color' => '',
				'mpc_ribbon__size'          => 'medium',

				'mpc_ribbon__font_preset'      => '',
				'mpc_ribbon__font_color'       => '',
				'mpc_ribbon__font_size'        => '',
				'mpc_ribbon__font_line_height' => '',
				'mpc_ribbon__font_align'       => '',
				'mpc_ribbon__font_transform'   => '',

				'mpc_ribbon__icon_type'       => 'icon',
				'mpc_ribbon__icon'            => '',
				'mpc_ribbon__icon_character'  => '',
				'mpc_ribbon__icon_image'      => '',
				'mpc_ribbon__icon_image_size' => 'thumbnail',
				'mpc_ribbon__icon_preset'     => '',
				'mpc_ribbon__icon_size'       => '',
				'mpc_ribbon__icon_color'      => '#333333',

				'mpc_ribbon__margin_css'  => '',
				'mpc_ribbon__padding_css' => '',
				'mpc_ribbon__border_css'  => '',

				'mpc_ribbon__background_type'       => 'color',
				'mpc_ribbon__background_color'      => '',
				'mpc_ribbon__background_image'      => '',
				'mpc_ribbon__background_image_size' => 'large',
				'mpc_ribbon__background_repeat'     => 'no-repeat',
				'mpc_ribbon__background_size'       => 'initial',
				'mpc_ribbon__background_position'   => 'middle-center',
				'mpc_ribbon__background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
			), $atts );

			/* Prepare */
			$icon      = MPC_Parser::icon( $atts );
			$dismiss   = MPC_Parser::icon( $atts, 'dismiss' );
			$animation = MPC_Parser::animation( $atts );

			$atts_ribbon = MPC_Parser::shortcode( $atts, 'mpc_ribbon_' );
			$ribbon      = $atts[ 'mpc_ribbon__disable' ] == '' ? $MPC_Ribbon->shortcode_template( $atts_ribbon ) : '';

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];
			$alert_id = 'mpc-alert-' . get_the_ID() . '-' . $MPC_Shortcode[ 'alerts' ]++;

			if ( isset( $_COOKIE[ $alert_id ] ) ) {
				return '';
			}

			$dismiss_atts = ' data-alert="' . esc_attr( $css_id ) . '"';
			$dismiss_atts .= $atts[ 'dismiss_frequency' ] != '' ? ' data-frequency="' . esc_attr( $atts[ 'dismiss_frequency' ] ) . '"' : '';
			$dismiss_classess = $atts[ 'dismiss_position' ] == 'corner' ? ' mpc-dismiss--corner' : '';

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init mpc-transition';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'icon_type' ] == 'image' ? ' mpc-icon--image' : '';
			$classes .= $atts[ 'font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'font_preset' ] ) : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$dismiss = '<div class="mpc-alert__dismiss mpc-transition' . $dismiss_classess . '"' . $dismiss_atts . '><i class="mpc-alert__icon-content' . $dismiss[ 'class' ] . '">' . $dismiss[ 'content' ] . '</i></div>';

			$return = $atts[ 'enable_dismiss' ] != '' && $atts[ 'dismiss_position' ] == 'corner' ? '<div class="mpc-alert-wrap">' : '';
				$return .= $ribbon != '' ? '<div class="mpc-ribbon-wrap">' : '';
					$return .= '<div data-cookie="' . $alert_id . '" data-id="' . $css_id . '" class="mpc-alert' . $classes . '"' . $animation . '>';
						if( $atts[ 'disable_icon' ] == '' ) {
							$return .= '<div class="mpc-alert__icon">';
								$return .= '<i class="mpc-alert__icon-content mpc-transition' . $icon[ 'class' ] . '">' . $icon[ 'content' ] . '</i>';
							$return .= '</div>';
						}
						$return .= '<div class="mpc-alert__content">' . wpb_js_remove_wpautop( $content, true ) . '</div>';
						$return .= $atts[ 'enable_dismiss' ] != '' && $atts[ 'dismiss_position' ] == 'inside' ? $dismiss : '';
					$return .= '</div>';
					$return .= $ribbon;
				$return .= $ribbon != '' ? '</div>' : '';
			$return .= $atts[ 'enable_dismiss' ] != '' && $atts[ 'dismiss_position' ] == 'corner' ? $dismiss . '</div>' : '';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_alert-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'icon_size' ] = $styles[ 'icon_size' ] != '' ? $styles[ 'icon_size' ] . ( is_numeric( $styles[ 'icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'font_size' ] = $styles[ 'font_size' ] != '' ? $styles[ 'font_size' ] . ( is_numeric( $styles[ 'font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'dismiss_icon_size' ] = $styles[ 'dismiss_icon_size' ] != '' ? $styles[ 'dismiss_icon_size' ] . ( is_numeric( $styles[ 'dismiss_icon_size' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-alert[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Icon
			$inner_styles = array();
			if ( $styles[ 'icon_border_css' ] ) { $inner_styles[] = $styles[ 'icon_border_css' ]; }
			if ( $styles[ 'icon_padding_css' ] ) { $inner_styles[] = $styles[ 'icon_padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'icon' ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::icon( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-alert[data-id="' . $css_id . '"] .mpc-alert__icon {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Content
			$inner_styles = array();
			if ( $styles[ 'content_border_css' ] ) { $inner_styles[] = $styles[ 'content_border_css' ]; }
			if ( $styles[ 'content_padding_css' ] ) { $inner_styles[] = $styles[ 'content_padding_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-alert[data-id="' . $css_id . '"] .mpc-alert__content {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Dismiss
			$inner_styles = array();
			if ( $styles[ 'dismiss_border_css' ] ) { $inner_styles[] = $styles[ 'dismiss_border_css' ]; }
			if ( $styles[ 'dismiss_padding_css' ] ) { $inner_styles[] = $styles[ 'dismiss_padding_css' ]; }
			if ( $styles[ 'dismiss_margin_css' ] && $styles[ 'dismiss_position' ] == 'corner' ) { $inner_styles[] = $styles[ 'dismiss_margin_css' ]; }
			if ( $temp_style = MPC_CSS::icon( $styles, 'dismiss' ) ) { $inner_styles[] = $temp_style; }
			if ( $styles[ 'dismiss_background' ] ) { $inner_styles[] = 'background:' . $styles[ 'dismiss_background' ] . ';'; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-alert__dismiss[data-alert="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Dismiss Hover
			$inner_styles = array();
			if ( $styles[ 'hover_dismiss_color' ] ) { $inner_styles[] = 'color:' . $styles[ 'hover_dismiss_color' ] . ';'; }
			if ( $styles[ 'hover_dismiss_border' ] ) { $inner_styles[] = 'border-color:' . $styles[ 'hover_dismiss_border' ] . ';'; }
			if ( $styles[ 'hover_dismiss_background' ] ) { $inner_styles[] = 'background:' . $styles[ 'hover_dismiss_background' ] . ';'; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-alert__dismiss[data-alert="' . $css_id . '"]:hover {';
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
					'wide_modal'  => true,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
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
					'group'       => __( 'Content', 'mpc' ),
				),
			);

			$disable_icon = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable Icon', 'mpc' ),
					'param_name'       => 'disable_icon',
					'tooltip'          => __( 'Check to disable icon.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Icon', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
			);

			$dismiss_dependency = array( 'element' => 'enable_dismiss', 'not_empty' => true );
			$enable_dismiss = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Enable Dismiss', 'mpc' ),
					'param_name'       => 'enable_dismiss',
					'tooltip'          => __( 'Check to enable dismiss icon.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'admin_label'      => true,
					'group'            => __( 'Dismiss', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Reappear Frequency', 'mpc' ),
					'param_name'       => 'dismiss_frequency',
					'tooltip'          => __( 'Select how often the alert box should reappear for each user viewing this page after dismiss.', 'mpc' ),
					'value'            => array(
						__( 'Never', 'mpc' )   => 'never',
						__( 'Always', 'mpc' )  => 'always',
						__( 'Daily', 'mpc' )   => 'daily',
						__( 'Weekly', 'mpc' )  => 'weekly',
						__( 'Monthly', 'mpc' ) => 'monthly',
					),
					'std'              => 'always',
					'group'            => __( 'Dismiss', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => $dismiss_dependency,
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Dismiss Position', 'mpc' ),
					'param_name'       => 'dismiss_position',
					'tooltip'          => __( 'Select the position of dismiss icon.', 'mpc' ),
					'value'            => array(
						__( 'Inside', 'mpc' ) => 'inside',
						__( 'Corner', 'mpc' ) => 'corner',
					),
					'std'              => 'inside',
					'group'            => __( 'Dismiss', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => $dismiss_dependency,
				),
			);

			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();
			$class      = MPC_Snippets::vc_class();

			$icon_dependency = array( 'element' => 'disable_icon', 'value_not_equal_to' => 'true' );
			$icon            = MPC_Snippets::vc_icon( array( 'group' => __( 'Icon', 'mpc' ), 'dependency' => $icon_dependency ) );
			$icon_padding    = MPC_Snippets::vc_padding( array( 'prefix' => 'icon', 'subtitle' => __( 'Icon', 'mpc' ), 'group' => __( 'Icon', 'mpc' ), 'dependency' => $icon_dependency ) );
			$icon_border     = MPC_Snippets::vc_border( array( 'prefix' => 'icon', 'subtitle' => __( 'Icon', 'mpc' ), 'group' => __( 'Icon', 'mpc' ), 'dependency' => $icon_dependency ) );
			$icon_background = MPC_Snippets::vc_background( array( 'prefix' => 'icon', 'subtitle' => __( 'Icon', 'mpc' ), 'group' => __( 'Icon', 'mpc' ), 'dependency' => $icon_dependency ) );

			$dismiss_icon       = MPC_Snippets::vc_icon( array( 'prefix' => 'dismiss', 'subtitle' => __( 'Dismiss', 'mpc' ), 'group' => __( 'Dismiss', 'mpc' ), 'dependency' => $dismiss_dependency ) );
			$dismiss_padding    = MPC_Snippets::vc_padding( array( 'prefix' => 'dismiss', 'subtitle' => __( 'Dismiss', 'mpc' ), 'group' => __( 'Dismiss', 'mpc' ), 'dependency' => $dismiss_dependency ) );
			$dismiss_border     = MPC_Snippets::vc_border( array( 'prefix' => 'dismiss', 'subtitle' => __( 'Dismiss', 'mpc' ), 'group' => __( 'Dismiss', 'mpc' ), 'dependency' => $dismiss_dependency ) );
			$dismiss_margin     = MPC_Snippets::vc_margin( array( 'prefix' => 'dismiss', 'subtitle' => __( 'Dismiss', 'mpc' ), 'group' => __( 'Dismiss', 'mpc' ), 'dependency' => array( 'element' => 'dismiss_position', 'value' => 'corner' ) ) );
			$dismiss_background = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Background', 'mpc' ),
					'param_name'       => 'dismiss_background',
					'tooltip'          => __( 'Choose icon background.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Dismiss', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-color-picker',
					'dependency'       => $dismiss_dependency
				),
			);
			$dismiss_hover = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Hover', 'mpc' ),
					'param_name'       => 'dismiss_hover_divider',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
					'group'            => __( 'Dismiss', 'mpc' ),
					'dependency'       => $dismiss_dependency
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Icon Color', 'mpc' ),
					'param_name'       => 'hover_dismiss_color',
					'tooltip'          => __( 'If you want to change the icon color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Dismiss', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-color-picker',
					'dependency'       => $dismiss_dependency
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Background', 'mpc' ),
					'param_name'       => 'hover_dismiss_background',
					'tooltip'          => __( 'If you want to change the icon background color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Dismiss', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-color-picker',
					'dependency'       => $dismiss_dependency
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Border', 'mpc' ),
					'param_name'       => 'hover_dismiss_border',
					'tooltip'          => __( 'If you want to change the icon border color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Dismiss', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-color-picker',
					'dependency'       => $dismiss_dependency
				),
			);

			$content_atts = array( 'prefix' => 'content', 'subtitle' => __( 'Content', 'mpc' ), 'group' => __( 'Content', 'mpc' ) );

			$content_padding = MPC_Snippets::vc_padding( $content_atts );
			$content_border  = MPC_Snippets::vc_border( $content_atts );
			$content_font    = MPC_Snippets::vc_font( array( 'subtitle' => __( 'Content', 'mpc' ), 'group' => __( 'Content', 'mpc' ) ) );

			$integrate_ribbon = vc_map_integrate_shortcode( 'mpc_ribbon', 'mpc_ribbon__', __( 'Ribbon', 'mpc' ) );
			$disable_ribbon   = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Ribbon', 'mpc' ),
					'param_name'       => 'mpc_ribbon__disable',
					'tooltip'          => __( 'Check to disable ribbon.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Ribbon', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_ribbon = array_merge( $disable_ribbon, $integrate_ribbon );

			$animation = MPC_Snippets::vc_animation();

			$params = array_merge(
				$base,
				$disable_icon,
				$icon,
				$icon_background,
				$icon_border,
				$icon_padding,
				$content_font,
				$content,
				$content_border,
				$content_padding,
				$enable_dismiss,
				$dismiss_icon,
				$dismiss_background,
				$dismiss_border,
				$dismiss_padding,
				$dismiss_margin,
				$dismiss_hover,
				$background,
				$border,
				$padding,
				$margin,
				$integrate_ribbon,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Alert', 'mpc' ),
				'description' => __( 'Display important messages with style', 'mpc' ),
				'base'        => 'mpc_alert',
				'class'       => '',
				'icon'        => 'mpc-shicon-alert',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Alert' ) ) {
	global $MPC_Alert;
	$MPC_Alert = new MPC_Alert;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_alert' ) ) {
	class WPBakeryShortCode_mpc_alert extends MPCShortCode_Base {}
}

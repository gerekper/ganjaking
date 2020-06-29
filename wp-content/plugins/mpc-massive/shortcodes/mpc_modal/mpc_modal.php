<?php
/*----------------------------------------------------------------------------*\
	MODAL SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Modal' ) ) {
	global $MPC_Shortcode;
	$MPC_Shortcode[ 'modals' ] = 0;

	class MPC_Modal {
		public $shortcode = 'mpc_modal';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_modal', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			/* AJAX Cals */
			add_action( 'wp_ajax_mpc_set_modal_cookie', array( $this, 'shortcode_ajax_set_modal_cookie' ) );
			add_action( 'wp_ajax_nopriv_mpc_set_modal_cookie', array( $this, 'shortcode_ajax_set_modal_cookie' ) );
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_modal-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_modal/css/mpc_modal.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_modal-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_modal/js/mpc_modal' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* AJAX Callbacks */
		function shortcode_ajax_set_modal_cookie() {
			$modal_id  = filter_var( $_POST[ 'id' ], FILTER_SANITIZE_STRING );
			$frequency = filter_var( $_POST[ 'frequency' ], FILTER_SANITIZE_STRING );

			if ( $modal_id == '' || $frequency == '' ) {
				die();
			}

			if ( $frequency == 'weekly' ) {
				$repeat_time = WEEK_IN_SECONDS;
			} elseif ( $frequency == 'monthly' ) {
				$repeat_time = MONTH_IN_SECONDS;
			} else {
				$repeat_time = DAY_IN_SECONDS;
			}

			setcookie( $modal_id, true, time() + $repeat_time, '/' );

			die();
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $MPC_Icon, $MPC_Shortcode, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                   => '',
				'preset'                  => '',
				'content_preset'          => '',
				'modal_id'                => '',

				'frequency'               => 'always',
				'onclick_id'              => '',
				'on_page_load'            => '',
				'delay'                   => '0',
				'max_width'               => '90',
				'max_height'              => '90',
				'position'                => 'middle-center',
				'alignment'               => 'center',

				'padding_css'             => '',
				'margin_css'              => '',
				'border_css'              => '',

				'background_type'         => 'color',
				'background_color'        => '',
				'background_image'        => '',
				'background_image_size'   => 'large',
				'background_repeat'       => 'no-repeat',
				'background_size'         => 'initial',
				'background_position'     => 'middle-center',
				'background_gradient'     => '#83bae3||#80e0d4||0;100||180||linear',

				'animation_in_type'       => 'none',
				'animation_in_duration'   => '300',
				'animation_in_delay'      => '0',
				'animation_in_offset'     => '100',

				'animation_loop_type'     => 'none',
				'animation_loop_duration' => '1000',
				'animation_loop_delay'    => '1000',
				'animation_loop_hover'    => '',

				/* Overlay */
				'overlay_background_type'       => 'color',
				'overlay_background_color'      => '',
				'overlay_background_image'      => '',
				'overlay_background_image_size' => 'large',
				'overlay_background_repeat'     => 'no-repeat',
				'overlay_background_size'       => 'initial',
				'overlay_background_position'   => 'middle-center',
				'overlay_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

                /* Close Button */
				'close_position'                        => 'modal',
				'close_overlay'                         => '',

				'mpc_icon__preset'                      => '',
				'mpc_icon__transition'                  => 'none',

				'mpc_icon__icon_type'                   => 'icon',
				'mpc_icon__icon'                        => '',
				'mpc_icon__icon_character'              => '',
				'mpc_icon__icon_image'                  => '',
				'mpc_icon__icon_image_size'             => 'thumbnail',
				'mpc_icon__icon_preset'                 => '',
				'mpc_icon__icon_size'                   => '',
				'mpc_icon__icon_color'                  => '',
				'mpc_icon__icon_effect'                 => 'none-none',
				'mpc_icon__icon_gap'                    => '',

				'mpc_icon__padding_css'                 => '',
				'mpc_icon__margin_css'                  => '',
				'mpc_icon__border_css'                  => '',

				'mpc_icon__background_type'             => 'color',
				'mpc_icon__background_color'            => '',
				'mpc_icon__background_image'            => '',
				'mpc_icon__background_image_size'       => 'large',
				'mpc_icon__background_repeat'           => 'no-repeat',
				'mpc_icon__background_size'             => 'initial',
				'mpc_icon__background_position'         => 'middle-center',
				'mpc_icon__background_gradient'         => '#83bae3||#80e0d4||0;100||180||linear',

				'mpc_icon__hover_icon_type'             => 'icon',
				'mpc_icon__hover_icon'                  => '',
				'mpc_icon__hover_icon_character'        => '',
				'mpc_icon__hover_icon_image'            => '',
				'mpc_icon__hover_icon_image_size'       => 'thumbnail',
				'mpc_icon__hover_icon_preset'           => '',
				'mpc_icon__hover_icon_size'             => '',
				'mpc_icon__hover_icon_color'            => '',

				'mpc_icon__hover_color'                 => '',

				'mpc_icon__hover_background_type'       => 'color',
				'mpc_icon__hover_background_color'      => '',
				'mpc_icon__hover_background_image'      => '',
				'mpc_icon__hover_background_image_size' => 'large',
				'mpc_icon__hover_background_repeat'     => 'no-repeat',
				'mpc_icon__hover_background_size'       => 'initial',
				'mpc_icon__hover_background_position'   => 'middle-center',
				'mpc_icon__hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'mpc_icon__hover_border_css'            => '',
			), $atts );

			/* Prepare */
			$css_id = $this->shortcode_styles( $atts );
			$modal_id = 'mpc-modal-' . get_the_ID() . '-' . $MPC_Shortcode[ 'modals' ]++;

			if ( isset( $_COOKIE[ $modal_id ] ) ) {
				return '';
			}

			if ( ( $atts[ 'mpc_icon__icon_type' ] == 'icon' && $atts[ 'mpc_icon__icon' ] == '' ) ||
			     ( $atts[ 'mpc_icon__icon_type' ] == 'character' && $atts[ 'mpc_icon__icon_character' ] == '' ) ||
			     ( $atts[ 'mpc_icon__icon_type' ] == 'image' && $atts[ 'mpc_icon__icon_image' ] == '' ) ) {
				$atts[ 'close_overlay' ] = 'true';
			}

			$animation  = MPC_Parser::animation( $atts );
			$close_icon = MPC_Parser::shortcode( $atts, 'mpc_icon_' );

			/* Shortcode classes | Animation | Layout */
			$classes = $animation != '' ? ' mpc-animation' : '';
			$classes .= ' mpc-init';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$overlay_atts = ' data-id="' . $css_id . '"';
			$overlay_atts .= $atts[ 'delay' ] != '0' ? ' data-delay="' . esc_attr( $atts[ 'delay' ] ) . '"' : '';
			$overlay_atts .= ' data-position="' . esc_attr( $atts[ 'position' ] ) . '"';
			$overlay_atts .= $atts[ 'frequency' ] != 'always' ? ' data-frequency="' . esc_attr( $atts[ 'frequency' ] ) . '"' : '';
			$overlay_atts .= $atts[ 'frequency' ] == 'onclick' ? ' data-target-id="' . esc_attr( $atts[ 'onclick_id' ] ) . '"' : '';

			$overlay_classes = $atts[ 'on_page_load' ] != '' && $atts[ 'delay' ] == '0' ? ' mpc-visible' : '';
			$overlay_classes .= $atts[ 'close_overlay' ] != '' ? ' mpc-close-on-click' : '';
			$overlay_classes .= $atts[ 'close_position' ] != 'modal' ? ' mpc-close--outside' : '';

			$close_button = '<div class="mpc-modal__close">';
				$close_button .= $MPC_Icon->shortcode_template( $close_icon );
			$close_button .= '</div>';

			/* Output */
			$return = '<div id="' . $modal_id . '" class="mpc-modal-overlay' . $overlay_classes . '"' . $overlay_atts . '>';
				if ( $atts[ 'close_position' ] == 'overlay' ) {
					$return .= $close_button;
				}
				$return .= '<div class="mpc-modal' . $classes . '"' . $animation . '>';
					if ( $atts[ 'close_position' ] == 'modal' ) {
						$return .= $close_button;
					}
					$return .= '<div class="mpc-modal__content">' . do_shortcode( $content ) . '</div>';
				$return .= '</div>';
			$return .= '</div>';

			if ( $atts[ 'on_page_load' ] == '' ) {
				$return .= '<div class="mpc-modal-waypoint mpc-waypoint" data-id="' . $modal_id . '"></div>';
			}

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return = '<div class="mpc-frontend-notice">';
					$return .= '<h4>' . __( 'Modal', 'mpc' ) . '</h4>';
					$return .= __( 'Unfortunately this shortcode isn\'t available in <em>Frontend Editor</em> at the moment. This feature will be added in the upcoming updates. We are sorry for any inconvenience :)', 'mpc' );
				$return .= '</div>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_modal-' . rand( 1, 100 ) );
			$style = '';

			// Add 'vw' and 'vh'
			$styles[ 'max_height' ] = $styles[ 'max_height' ] != '' ? $styles[ 'max_height' ] . ( is_numeric( $styles[ 'max_height' ] ) ? 'vh' : '' ) : '';
			$styles[ 'max_width' ]  = $styles[ 'max_width' ] != '' ? $styles[ 'max_width' ] . ( is_numeric( $styles[ 'max_width' ] ) ? 'vw' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-modal-overlay[data-id="' . $css_id . '"] .mpc-modal {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Overlay
			if ( $temp_style = MPC_CSS::background( $styles, 'overlay' ) ) {
				$style .= '.mpc-modal-overlay[data-id="' . $css_id . '"] {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Content
			$inner_styles = array();
			if ( $styles[ 'max_height' ] ) { $inner_styles[] = 'max-height: ' . $styles[ 'max_height' ] . ';'; }
			if ( $styles[ 'max_width' ] ) { $inner_styles[] = 'max-width: ' . $styles[ 'max_width' ] . ';'; }
			if ( $styles[ 'alignment' ] ) { $inner_styles[] = 'text-align: ' . $styles[ 'alignment' ] . ';'; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-modal-overlay[data-id="' . $css_id . '"] .mpc-modal__content {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Close
			if ( $styles[ 'mpc_icon__margin_css' ] ) {
				$style .= '.mpc-modal-overlay[data-id="' . $css_id . '"] .mpc-modal__close {';
					$style .= $styles[ 'mpc_icon__margin_css' ];
				$style .= '}';
			}

			$mpc_massive_styles .= $style;

			return $css_id;
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return '';
			}

			$base = array(
				array(
					'type'             => 'hidden',
					'param_name'       => 'modal_id',
					'value'            => '',
					'std'              => '',
				),
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
					'type'             => 'mpc_divider',
					'title'            => __( 'Display', 'mpc' ),
					'param_name'       => 'appear_divider',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Appear Frequency', 'mpc' ),
					'param_name'       => 'frequency',
					'admin_label'      => true,
					'tooltip'          => __( 'Select how often the modal box should appear for each user viewing this page.', 'mpc' ),
					'value'            => array(
						__( 'On Click', 'mpc' ) => 'onclick',
						__( 'Always', 'mpc' )   => 'always',
						__( 'Hourly', 'mpc' )   => 'hourly',
						__( 'Daily', 'mpc' )    => 'daily',
						__( 'Weekly', 'mpc' )   => 'weekly',
						__( 'Monthly', 'mpc' )  => 'monthly',
					),
					'std'              => 'always',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Modal ID', 'mpc' ),
					'param_name'       => 'onclick_id',
					'admin_label'      => true,
					'tooltip'          => __( 'Paste this ID to any shortcode as link URL. This will show this modal after user clicks on the shortcode. Please remember to include the `#` before the ID.', 'mpc' ),
//					'value'            => '',
					'value'            => uniqid( 'modal_id_' ),
					'dependency'       => array( 'element' => 'frequency', 'value' => 'onclick' ),
					'edit_field_class' => 'vc_col-sm-8 vc_column',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Show On Page Load', 'mpc' ),
					'param_name'       => 'on_page_load',
					'tooltip'          => __( 'Check this to enable modal display on page load. By default modals are displayed when user scrolls to modal position on the page. Enabling this will display modal after page loads.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'dependency'       => array( 'element' => 'frequency', 'value' => array( 'always', 'hourly', 'daily', 'weekly', 'monthly' ) ),
					'edit_field_class' => 'vc_col-sm-8 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Appear Delay', 'mpc' ),
					'param_name'       => 'delay',
					'tooltip'          => __( 'Choose delay before the modal shows up.', 'mpc' ),
					'value'            => 0,
					'min'              => 0,
					'max'              => 300,
					'step'             => 1,
					'unit'             => 's',
					'dependency'       => array( 'element' => 'on_page_load', 'value' => 'true' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Maximum Width', 'mpc' ),
					'param_name'       => 'max_width',
					'tooltip'          => __( 'Choose maximum width of the modal. Actual width will depends on browser window width.', 'mpc' ),
					'value'            => 50,
					'min'              => 10,
					'max'              => 90,
					'step'             => 1,
					'unit'             => '%',
					'edit_field_class' => 'vc_col-sm-12 vc_column clear mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Maximum Height', 'mpc' ),
					'param_name'       => 'max_height',
					'tooltip'          => __( 'Choose maximum height of the modal. Actual height will depends on browser window height.', 'mpc' ),
					'value'            => 50,
					'min'              => 10,
					'max'              => 90,
					'step'             => 1,
					'unit'             => '%',
					'edit_field_class' => 'vc_col-sm-12 vc_column clear mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_align',
					'heading'          => __( 'Position', 'mpc' ),
					'param_name'       => 'position',
					'tooltip'          => __( 'Choose modal position.', 'mpc' ),
					'value'            => 'middle-center',
					'std'              => 'middle-center',
					'grid_size'        => 'large',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_align',
					'heading'          => __( 'Content Alignment', 'mpc' ),
					'param_name'       => 'alignment',
					'tooltip'          => __( 'Choose content alignment.', 'mpc' ),
					'value'            => 'center',
					'std'              => 'center',
					'grid_size'        => 'small',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
			);

			$close = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Button Position', 'mpc' ),
					'param_name'       => 'close_position',
					'tooltip'          => __( 'Select close button position.<br><b>Modal Box</b>: display button in the top-right corner of modal box;<br><b>Overlay</b>: display button in the top-right corner of overlay.', 'mpc' ),
					'value'            => array(
						__( 'Modal Box', 'mpc' ) => 'modal',
						__( 'Overlay', 'mpc' )   => 'overlay',
					),
					'std'              => 'modal',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
					'group'            => __( 'Close Button', 'mpc' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Close On Overlay', 'mpc' ),
					'param_name'       => 'close_overlay',
					'tooltip'          => __( 'Check to enable close modal on overlay click. Enabling this will let user close modal by clicking anywhere on its overlay.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-8 vc_column mpc-advanced-field mpc-first-row',
					'group'            => __( 'Close Button', 'mpc' ),
				),
			);

			/* Integrate Icon */
			$icon_exclude   = array( 'exclude_regex' => '/animation_(.*)|url|tooltip_(.*)/' );
			$integrate_icon = vc_map_integrate_shortcode( 'mpc_icon', 'mpc_icon__', __( 'Close Button', 'mpc' ), $icon_exclude );

			$overlay_background = MPC_Snippets::vc_background( array( 'prefix' => 'overlay', 'subtitle' => __( 'Overlay', 'mpc' ) ) );

			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();

			$animation = MPC_Snippets::vc_animation();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$close,
				$integrate_icon,
				$overlay_background,
				$background,
				$border,
				$padding,
				$margin,
				$animation,
				$class
			);

			$allowed_shortcodes = array( 'only' => 'vc_row,mpc_alert,mpc_dropcap,mpc_icon,mpc_icon_column,mpc_button,mpc_divider,mpc_icon,mpc_progress,vc_column_text,vc_custom_heading' );

			return array(
				'name'                    => __( 'Modal', 'mpc' ),
				'description'             => __( 'Full page popup with content', 'mpc' ),
				'base'                    => 'mpc_modal',
				'class'                   => '',
//				'icon'                    => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-modal.png',
				'icon'                    => 'mpc-shicon-modal',
				'category'                => __( 'Massive', 'mpc' ),
				'params'                  => $params,
				'is_container'            => true,
				'as_parent'               => $allowed_shortcodes,
				'content_element'         => true,
				'js_view'                 => 'VcColumnView',
				'show_settings_on_create' => true,
			);
		}
	}
}
if ( class_exists( 'MPC_Modal' ) ) {
	global $MPC_Modal;
	$MPC_Modal = new MPC_Modal;
}

if ( class_exists( 'MPCShortCodeContainer_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_modal' ) ) {
	class WPBakeryShortCode_mpc_modal extends MPCShortCodeContainer_Base {}
}

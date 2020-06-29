<?php
/*----------------------------------------------------------------------------*\
	LIGHTBOX SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Lightbox' ) ) {
	class MPC_Lightbox {
		public $shortcode = 'mpc_lightbox';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_lightbox', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_lightbox-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_lightbox/css/mpc_lightbox.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_lightbox-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_lightbox/js/mpc_lightbox' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $MPC_Button, $MPC_Tooltip, $mpc_ma_options, $mpc_button_separator;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'             => '',
				'preset'            => '',
				'lightbox_url'      => '',
				'mpc_button__title' => '',

				'mpc_button__font_preset'      => '',
				'mpc_button__font_color'       => '',
				'mpc_button__font_size'        => '',
				'mpc_button__font_line_height' => '',
				'mpc_button__font_align'       => '',
				'mpc_button__font_transform'   => '',

				'mpc_button__padding_css' => '',
				'mpc_button__margin_css'  => '',
				'mpc_button__border_css'  => '',

				'mpc_button__icon_type'       => 'icon',
				'mpc_button__icon'            => '',
				'mpc_button__icon_character'  => '',
				'mpc_button__icon_image'      => '',
				'mpc_button__icon_image_size' => 'thumbnail',
				'mpc_button__icon_preset'     => '',
				'mpc_button__icon_color'      => '#333333',
				'mpc_button__icon_size'       => '',

				'mpc_button__icon_effect' => 'none-none',
				'mpc_button__icon_gap'    => '',

				'mpc_button__background_type'       => 'color',
				'mpc_button__background_color'      => '',
				'mpc_button__background_image'      => '',
				'mpc_button__background_image_size' => 'large',
				'mpc_button__background_repeat'     => 'no-repeat',
				'mpc_button__background_size'       => 'initial',
				'mpc_button__background_position'   => 'middle-center',
				'mpc_button__background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'mpc_button__hover_background_effect' => 'fade-in',
				'mpc_button__hover_background_offset' => '',

				'mpc_button__hover_border_css' => '',

				'mpc_button__hover_font_color' => '',
				'mpc_button__hover_icon_color' => '',

				'mpc_button__hover_background_type'       => 'color',
				'mpc_button__hover_background_color'      => '',
				'mpc_button__hover_background_image'      => '',
				'mpc_button__hover_background_image_size' => 'large',
				'mpc_button__hover_background_repeat'     => 'no-repeat',
				'mpc_button__hover_background_size'       => 'initial',
				'mpc_button__hover_background_position'   => 'middle-center',
				'mpc_button__hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

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

			$animation = MPC_Parser::animation( $atts );

			$atts_tooltip = MPC_Parser::shortcode( $atts, 'mpc_tooltip_' );
			$tooltip      = $atts[ 'mpc_tooltip__disable' ] == '' ? $MPC_Tooltip->shortcode_template( $atts_tooltip ) : '';

			// ToDo: Something better..
			$temp = $mpc_button_separator;
			$mpc_button_separator ='';
			$atts_button  = MPC_Parser::shortcode( $atts, 'mpc_button_' );
			$button       = $MPC_Button->shortcode_template( $atts_button );
			$mpc_button_separator = $temp;
			unset( $temp );

			$url_settings = MPC_Parser::url( $atts[ 'lightbox_url' ] );
			$wrapper = $url_settings != '' ? 'a' : 'div';

			$classes = ' mpc-init mpc-transition';
			$classes .= MPC_Helper::lightbox_vendor();
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $tooltip != '' ? ' mpc-tooltip-target' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$return = $tooltip != '' ? '<div class="mpc-tooltip-wrap">' : '';
				$return .= '<' . $wrapper . $url_settings . ' class="mpc-lightbox' . $classes . '"' . $animation . '>';
					$return .= $button;
				$return .= '</' . $wrapper . '>';
				$return .= $tooltip;
			$return .= $tooltip != '' ? '</div>' : '';

			if ( isset( $mpc_button_separator ) ) {
				$return .= $mpc_button_separator;
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
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
					'type'        => 'vc_link',
					'heading'     => __( 'Link', 'mpc' ),
					'param_name'  => 'lightbox_url',
					'admin_label' => true,
					'tooltip'     => __( 'Choose target link for lightbox.', 'mpc' ),
					'value'       => '',
					'description' => __( 'Specify URL.', 'mpc' ),
				),
			);

			$button_exclude = array( 'exclude_regex' => '/^preset|url|block|animation_(.*)|mpc_tooltip|class|class_divider/' );
			$integrate_button = vc_map_integrate_shortcode( 'mpc_button', 'mpc_button__', '', $button_exclude );

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

			$animation = MPC_Snippets::vc_animation();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$integrate_button,
				$integrate_tooltip,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Lightbox', 'mpc' ),
				'description' => __( 'Button with image popup', 'mpc' ),
				'base'        => 'mpc_lightbox',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-lightbox.png',
				'icon'        => 'mpc-shicon-lightbox',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Lightbox' ) ) {
	global $MPC_Lightbox;
	$MPC_Lightbox = new MPC_Lightbox;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_lightbox' ) ) {
	class WPBakeryShortCode_mpc_lightbox extends MPCShortCode_Base {}
}

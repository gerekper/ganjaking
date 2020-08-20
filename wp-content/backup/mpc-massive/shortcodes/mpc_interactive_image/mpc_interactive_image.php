<?php
/*----------------------------------------------------------------------------*\
	INTERACTIVE IMAGE SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Interactive_Image' ) ) {
	class MPC_Interactive_Image {
		public $shortcode = 'mpc_interactive_image';
		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_interactive_image', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			add_action( 'wp_ajax_mpc_interactive_image_get_image', array( $this, 'get_image' ) );
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_interactive_image-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_interactive_image/css/mpc_interactive_image.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_interactive_image-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_interactive_image/js/mpc_interactive_image' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $mpc_ma_options, $MPC_Ribbon;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'              => '',
				'preset'             => '',
				'content_preset'     => '',
				'background_image'   => '',

				'slideshow_divider'  => '',
				'slideshow_enable'   => '',
				'slideshow_duration' => '250',
				'slideshow_delay'    => '1000',
				'slideshow_loop'     => '',

				/* Ribbon */
				'mpc_ribbon__disable'               => '',
				'mpc_ribbon__preset'                => '',
				'mpc_ribbon__text'                  => '',
				'mpc_ribbon__style'                 => 'classic',
				'mpc_ribbon__alignment'             => 'top-left',
				'mpc_ribbon__corners_color'         => '',
				'mpc_ribbon__size'                  => 'medium',

				'mpc_ribbon__font_preset'           => '',
				'mpc_ribbon__font_color'            => '',
				'mpc_ribbon__font_size'             => '',
				'mpc_ribbon__font_line_height'      => '',
				'mpc_ribbon__font_align'            => '',
				'mpc_ribbon__font_transform'        => '',

				'mpc_ribbon__icon_type'             => 'icon',
				'mpc_ribbon__icon'                  => '',
				'mpc_ribbon__icon_character'        => '',
				'mpc_ribbon__icon_image'            => '',
				'mpc_ribbon__icon_image_size'       => 'thumbnail',
				'mpc_ribbon__icon_preset'           => '',
				'mpc_ribbon__icon_size'             => '',
				'mpc_ribbon__icon_color'            => '#333333',

				'mpc_ribbon__margin_css'            => '',
				'mpc_ribbon__padding_css'           => '',
				'mpc_ribbon__border_css'            => '',

				'mpc_ribbon__background_type'       => 'color',
				'mpc_ribbon__background_color'      => '',
				'mpc_ribbon__background_image'      => '',
				'mpc_ribbon__background_image_size' => 'large',
				'mpc_ribbon__background_repeat'     => 'no-repeat',
				'mpc_ribbon__background_size'       => 'initial',
				'mpc_ribbon__background_position'   => 'middle-center',
				'mpc_ribbon__background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
			), $atts );

			$atts_ribbon  = MPC_Parser::shortcode( $atts, 'mpc_ribbon_' );
			$ribbon       = $atts[ 'mpc_ribbon__disable' ] == '' ? $MPC_Ribbon->shortcode_template( $atts_ribbon ) : '';

			$background_image = wp_get_attachment_image_src( $atts[ 'background_image' ], 'full' );
			$alt = ( get_post_meta( $atts[ 'background_image' ], '_wp_attachment_image_alt', true ) != '' ) ? get_post_meta( $atts[ 'background_image' ], '_wp_attachment_image_alt', true ) : __( 'Image', 'mpc' );

			if ( ! isset( $background_image[ 0 ] ) ) {
				$background_image = array(
					mpc_get_plugin_path( __FILE__ ) . '/assets/images/mpc-image-placeholder.png',
					64,
					64,
				);
			}

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$classes = ' ' . esc_attr( $atts[ 'class' ] );

			$return = $ribbon != '' ? '<div class="mpc-ribbon-wrap">' : '';
				$return .= '<div id="' . $css_id . '" class="mpc-interactive_image mpc-init' . $classes . '">';
					$return .= '<img src="' . esc_attr( $background_image[ 0 ] ) . '" width="' . esc_attr( $background_image[ 1 ] ) . '" height="' . esc_attr( $background_image[ 2 ] ) . '" alt="' . esc_attr( $alt ) . '" class="mpc-interactive_image__image" />';
					$return .= '<div class="mpc-interactive_image-wrap">';
						$return .= do_shortcode( $content );
					$return .= '</div>';
				$return .= '</div>';
				$return .= $ribbon;
			$return .= $ribbon != '' ? '</div>' : '';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_interactive_image-' . rand( 1, 100 ) );

			return array(
				'id'  => $css_id,
				'css' => '',
			);
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return '';
			}

			global $mpc_js_localization;
			$mpc_js_localization[ 'mpc_interactive_image' ] = array(
				'preview'       => __( 'Show Preview', 'mpc' ),
				'no_background' => __( 'Please set background for Interactive Image.', 'mpc' ),
				'no_hotspots'   => __( 'Please add some Hotspots.', 'mpc' ),
			);

			$base = array(
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
					'type'        => 'attach_image',
					'heading'     => __( 'Image', 'mpc' ),
					'param_name'  => 'background_image',
					'holder'      => 'img',
					'tooltip'     => __( 'Choose background image.', 'mpc' ),
					'value'       => '',
				),

				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Hotspots Preview', 'mpc' ),
					'tooltip'    => __( 'Display hotspots placement. You can preview all hotspot shortcodes placement on the specified background image.', 'mpc' ),
					'param_name' => 'preview_divider',
				),
			);

			$integrate_ribbon = vc_map_integrate_shortcode( 'mpc_ribbon', 'mpc_ribbon__', __( 'Ribbon', 'mpc' ) );
			$disable_ribbon   = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable Ribbon', 'mpc' ),
					'param_name'       => 'mpc_ribbon__disable',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to disable ribbon display.', 'mpc' ),
					'group'            => __( 'Ribbon', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_ribbon = array_merge( $disable_ribbon, $integrate_ribbon );

			$class = MPC_Snippets::vc_class();

			$params = array_merge( $base, $integrate_ribbon, $class );

			return array(
				'name'            => __( 'Interactive Image', 'mpc' ),
				'description'     => __( 'Image with interactive hot spots', 'mpc' ),
				'base'            => 'mpc_interactive_image',
				'class'           => '',
//				'icon'            => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-interactive-image.png',
				'icon'            => 'mpc-shicon-inter-image',
				'category'        => __( 'Massive', 'mpc' ),
				'as_parent'       => array( 'only' => 'mpc_hotspot' ),
				'content_element' => true,
				"js_view"         => 'VcColumnView',
				'params'          => $params,
			);
		}

		/* Get background image for hotspots preview */
		function get_image() {
			if ( ! isset ( $_POST[ 'image_id' ] ) ) {
				die( 'error' );
			}

			$background_image = wp_get_attachment_image_src( $_POST[ 'image_id' ], 'full' );

			if ( $background_image === false ) {
				die( 'error' );
			}

			echo '<img class="mpc-coords__image" src="' . $background_image[ 0 ] . '" width="' . $background_image[ 1 ] . '" height="' . $background_image[ 2 ] . '" />';
			die();
		}
	}
}
if ( class_exists( 'MPC_Interactive_Image' ) ) {
	global $MPC_Interactive_Image;
	$MPC_Interactive_Image = new MPC_Interactive_Image;
}

if ( class_exists( 'MPCShortCodeContainer_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_interactive_image' ) ) {
	class WPBakeryShortCode_mpc_interactive_image extends MPCShortCodeContainer_Base {}
}

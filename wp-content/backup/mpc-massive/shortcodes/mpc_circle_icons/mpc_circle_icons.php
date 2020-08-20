<?php
/*----------------------------------------------------------------------------*\
	CIRCLE ICONS SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Circle_Icons' ) ) {
	class MPC_Circle_Icons {
		public $shortcode = 'mpc_circle_icons';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_circle_icons', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_circle_icons-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_circle_icons/css/mpc_circle_icons.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_circle_icons-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_circle_icons/js/mpc_circle_icons' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $mpc_circle_icons_wrap, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                 => '',
				'preset'                => '',
				'content_preset'        => '',
				'item_size'             => '75',
				'active_item'           => '1',
				'active_item_effect'    => 'none',
				'enable_slideshow'      => '',
				'delay'                 => '5000',

				'border_css'            => '',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
			), $atts );

			$mpc_circle_icons_wrap = true;

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$classes = ' ' . esc_attr( $atts[ 'class' ] );

			$return = '<div id="' . $css_id . '" class="mpc-circle-icons mpc-init' . $classes . '" data-active-item="' . esc_attr( $atts[ 'active_item' ] ) . '"' . ( $atts[ 'enable_slideshow' ] == 'true' ? ' data-delay="' . esc_attr( $atts[ 'delay' ] ) . '"' : '' ) . ( $atts[ 'active_item_effect' ] != 'none' ? ' data-effect="' . esc_attr( $atts[ 'active_item_effect' ] ) . '"' : '' ) . '>';
				$return .= do_shortcode( $content );
			$return .= '</div>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			$mpc_circle_icons_wrap = false;

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_circle_icons-' . rand( 1, 100 ) );
			$style  = '';

			$item_size   = (int) $styles[ 'item_size' ] * .92;
			$item_margin = ( 100 - $item_size ) * .5;

			if ( $temp_style = MPC_CSS::background( $styles ) ) {
				$style .= '.mpc-circle-icons[id="' . $css_id . '"] {';
					$style .= $temp_style;
				$style .= '}';
			}

			if ( $styles[ 'border_css' ] ) {
				$style .= '.mpc-circle-icons[id="' . $css_id . '"]:before {';
					$style .= $styles[ 'border_css' ];
				$style .= '}';
			}

			$style .= '.mpc-circle-icons[id="' . $css_id . '"] .mpc-icon-column__content-wrap {';
				$style .= 'width:' . $item_size . '%;';
				$style .= 'height:' . $item_size . '%;';
				$style .= 'margin:' . $item_margin . '% !important;';
			$style .= '}';

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
					'type'             => 'checkbox',
					'heading'          => __( 'Slide Show', 'mpc' ),
					'param_name'       => 'enable_slideshow',
					'tooltip'          => __( 'Check to enable slide show. Icons will auto swap once the slide show delay pass.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Active Item', 'mpc' ),
					'param_name'       => 'active_item',
					'tooltip'          => __( 'Define which icon column should be active by default.', 'mpc' ),
					'value'            => '',
					'std'              => 1,
					'label'            => '',
					'validate'         => true,
					'addon'            => array(
						'icon'  => 'dashicons-images-alt',
						'align' => 'prepend'
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-first-row',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Slide Show Delay', 'mpc' ),
					'param_name'       => 'delay',
					'tooltip'          => __( 'Specify delay between slides.', 'mpc' ),
					'value'            => 5000,
					'unit'             => 'ms',
					'min'              => 1000,
					'max'              => 15000,
					'step'             => 100,
					'dependency'       => array( 'element' => 'enable_slideshow', 'value' => 'true' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Content Size', 'mpc' ),
					'param_name'       => 'item_size',
					'tooltip'          => __( 'Choose content block size. Round connected icons are taking 100% size of its container. You can choose how big would be the inner content block.', 'mpc' ),
					'value'            => 75,
					'unit'             => '%',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_animation',
					'heading'          => __( 'Active Item Effect', 'mpc' ),
					'param_name'       => 'active_item_effect',
					'tooltip'          => __( 'Choose one of the animation types. You can apply the animation to the preview box on the right with the <b>Refresh</b> button.', 'mpc' ),
					'value'            => MPC_Snippets::$animations_loop_list,
					'std'              => 'none',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border( array( 'with_radius' => false ) );
			$class      = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$background,
				$border,
				$class
			);

			return array(
				'name'                    => __( 'Info Circle', 'mpc' ),
				'description'             => __( 'Round connected info boxes', 'mpc' ),
				'base'                    => 'mpc_circle_icons',
				'is_container'            => true,
				'as_parent'               => array( 'only' => 'mpc_icon_column' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
//				'icon'                    => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-connected-icon-column.png',
				'icon'                    => 'mpc-shicon-circle-icons',
				'category'                => __( 'Massive', 'mpc' ),
				'params'                  => $params,
				'js_view'                 => 'VcColumnView',
			);
		}
	}
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_mpc_circle_icons' ) ) {
	class WPBakeryShortCode_mpc_circle_icons extends WPBakeryShortCodesContainer {}
}

if ( class_exists( 'MPC_Circle_Icons' ) ) {
	global $MPC_Circle_Icons;
	$MPC_Circle_Icons = new MPC_Circle_Icons;
}

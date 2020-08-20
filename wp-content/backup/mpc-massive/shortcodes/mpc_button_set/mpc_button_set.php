<?php
/*----------------------------------------------------------------------------*\
	BUTTON SET SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Button_Set' ) ) {
	class MPC_Button_Set {
		public $shortcode = 'mpc_button_set';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_button_set', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_button_set-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_button_set/css/mpc_button_set.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_button_set-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_button_set/js/mpc_button_set' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                   => '',
				'preset'                  => '',
				'content_preset'          => '',
				'style'                   => 'horizontal',
				'space'                   => '',
				'fullwidth'               => '',

				'padding_css'             => '',
				'margin_css'              => '',
				'border_css'              => '',

				'icon_type'               => 'icon',
				'icon'                    => '',
				'icon_character'          => '',
				'icon_image'              => '',
				'icon_image_size'         => 'thumbnail',
				'icon_preset'             => '',
				'icon_color'              => '#333333',
				'icon_size'               => '',

				'icon_margin_css'         => '',
				'icon_animation'          => 'none',

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
			), $atts );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$icon      = MPC_Parser::icon( $atts );
			$animation = MPC_Parser::animation( $atts );

			$classes = $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'fullwidth' ] != '' ? ' mpc-fullwidth' : '';
			$classes .= $atts[ 'style' ] == 'horizontal' ? ' mpc-style--horizontal' : ' mpc-style--vertical';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$separator_classes = $icon[ 'class' ] == '' && $icon[ 'content' ] == '' ? ' mpc-empty' : '';

			global $mpc_button_separator;
			$mpc_button_separator = '<span class="mpc-button-separator-wrap"><span class="mpc-button-separator-box"><span class="mpc-button-separator ' . $icon[ 'class' ] . $separator_classes . '">' . $icon[ 'content' ] . '</span></span></span>';

			$return = '<div id="' . $css_id . '" class="mpc-button-set mpc-init' . $classes . '"' . $animation . ( $atts[ 'icon_animation' ] != 'none' ? ' data-animation="' . esc_attr( $atts[ 'icon_animation' ] ) . '"' : '' ) . '>';
				$return .= do_shortcode( $content );
			$return .= '</div>';

			$mpc_button_separator = null;

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_button_set-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'icon_size' ] = $styles[ 'icon_size' ] != '' ? $styles[ 'icon_size' ] . ( is_numeric( $styles[ 'icon_size' ] ) ? 'px' : '' ) : '';

			$styles[ 'space' ] = is_numeric( $styles[ 'space' ] ) ? $styles[ 'space' ] / 2 >> 0 : '';
			$styles[ 'space' ] = $styles[ 'space' ] != '' ? $styles[ 'space' ] . 'px' : '';

			// Set
			if ( $styles[ 'margin_css' ] ) {
				$style .= '.mpc-button-set[id="' . $css_id . '"] {';
					$style .= $styles[ 'margin_css' ];
				$style .= '}';
			}

			// Separator
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::icon( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-button-set[id="' . $css_id . '"] .mpc-button-separator {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'icon_margin_css' ] ) {
				$style .= '.mpc-button-set[id="' . $css_id . '"] .mpc-button-separator-box {';
					$style .= $styles[ 'icon_margin_css' ];
				$style .= '}';
			}

			// Button
			if ( $styles[ 'space' ] ) {
				$style .= '.mpc-button-set[id="' . $css_id . '"].mpc-style--horizontal .mpc-button {';
					$style .= 'padding-left:' . $styles[ 'space' ] . ';';
					$style .= 'padding-right:' . $styles[ 'space' ] . ';';
				$style .= '}';

				$style .= '.mpc-button-set[id="' . $css_id . '"].mpc-style--vertical .mpc-button {';
					$style .= 'padding-top:' . $styles[ 'space' ] . ';';
					$style .= 'padding-bottom:' . $styles[ 'space' ] . ';';
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
					'heading'     => __( 'Style Preset', 'mpc' ),
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
					'type'             => 'dropdown',
					'heading'          => __( 'Style', 'mpc' ),
					'param_name'       => 'style',
					'admin_label'      => true,
					'tooltip'          => __( 'Select buttons set style:<br><b>Horizontal</b>: horizontal buttons side by side;<br><b>Vertical</b>: vertical buttons one under the other.', 'mpc' ),
					'value'            => array(
						__( 'Horizontal', 'mpc' ) => 'horizontal',
						__( 'Vertical', 'mpc' )   => 'vertical',
					),
					'std'              => 'horizontal',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Separator Space', 'mpc' ),
					'param_name'       => 'space',
					'tooltip'          => __( 'Define separator space between buttons. Adds additional space on sides of separator to prevent it from overlapping button content.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Full Width', 'mpc' ),
					'param_name'       => 'fullwidth',
					'tooltip'          => __( 'Check to stretch buttons to 100% width of their container.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
			);

			$icon_animation = array(
				array(
					'type'             => 'mpc_animation',
					'heading'          => __( 'Animation Effect', 'mpc' ),
					'param_name'       => 'icon_animation',
					'tooltip'          => __( 'Choose one of the animation types. You can apply the animation to the preview box on the right with the <b>Refresh</b> button.', 'mpc' ),
					'value'            => MPC_Snippets::$animations_loop_list,
					'std'              => 'none',
					'group'            => __( 'Separator', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			$margin     = MPC_Snippets::vc_margin();
			$animation  = MPC_Snippets::vc_animation();
			$class      = MPC_Snippets::vc_class();

			$background  = MPC_Snippets::vc_background( array( 'group' => __( 'Separator', 'mpc' ) ) );
			$border      = MPC_Snippets::vc_border( array( 'group' => __( 'Separator', 'mpc' ) ) );
			$padding     = MPC_Snippets::vc_padding( array( 'group' => __( 'Separator', 'mpc' ) ) );
			$icon_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'icon', 'group' => __( 'Separator', 'mpc' ) ) );
			$icon        = MPC_Snippets::vc_icon( array( 'group' => __( 'Separator', 'mpc' ) ) );

			$params = array_merge( $base, $margin, $icon, $icon_animation, $background, $border, $padding, $icon_margin, $animation, $class );

			return array(
				'name'            => __( 'Buttons Set', 'mpc' ),
				'description'     => __( 'Buttons with separator', 'mpc' ),
				'base'            => 'mpc_button_set',
				'class'           => '',
				'icon'            => 'mpc-shicon-buttons-set',
				'category'        => __( 'Massive', 'mpc' ),
				'as_parent'       => array( 'only' => 'mpc_button, mpc_lightbox' ),
				'content_element' => true,
				"js_view"         => 'VcColumnView',
				'params'          => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Button_Set' ) ) {
	global $MPC_Button_Set;
	$MPC_Button_Set = new MPC_Button_Set;
}
if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_mpc_button_set' ) ) {
	class WPBakeryShortCode_mpc_button_set extends WPBakeryShortCodesContainer {}
}

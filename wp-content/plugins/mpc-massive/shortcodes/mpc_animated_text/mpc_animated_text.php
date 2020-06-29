<?php
/*----------------------------------------------------------------------------*\
	ANIMATED TEXT SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Animated_Text' ) ) {
	class MPC_Animated_Text {
		public $shortcode = 'mpc_animated_text';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_animated_text', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_animated_text-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_animated_text/css/mpc_animated_text.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_animated_text-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_animated_text/js/mpc_animated_text' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                      => '',
				'preset'                     => '',
				'style'                      => 'rotator',
				'duration'                   => 1000,
				'delay'                      => 1000,
				'disable_loop'               => '',
				'dynamic_width'              => '',

				'disable_pointer'            => '',
				'pointer_color'              => '',

				'text'                       => '',

				'disable_sides'              => '',
				'before_text'                => '',
				'after_text'                 => '',

				'padding_css'                => '',
				'margin_css'                 => '',
				'border_css'                 => '',

				'text_font_preset'           => '',
				'text_font_color'            => '',
				'text_font_size'             => '',
				'text_font_line_height'      => '',
				'text_font_align'            => '',
				'text_font_transform'        => '',

				'text_background_type'       => 'color',
				'text_background_color'      => '',
				'text_background_image'      => '',
				'text_background_image_size' => 'large',
				'text_background_repeat'     => 'no-repeat',
				'text_background_size'       => 'initial',
				'text_background_position'   => 'middle-center',
				'text_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'text_padding_css'           => '',
				'text_border_css'            => '',

				'sides_font_preset'          => '',
				'sides_font_color'           => '',
				'sides_font_size'            => '',
				'sides_font_line_height'     => '',
				'sides_font_align'           => '',
				'sides_font_transform'       => '',

				'background_type'            => 'color',
				'background_color'           => '',
				'background_image'           => '',
				'background_image_size'      => 'large',
				'background_repeat'          => 'no-repeat',
				'background_size'            => 'initial',
				'background_position'        => 'middle-center',
				'background_gradient'        => '#83bae3||#80e0d4||0;100||180||linear',

				'animation_in_type'          => 'none',
				'animation_in_duration'      => '300',
				'animation_in_delay'         => '0',
				'animation_in_offset'        => '100',

				'animation_loop_type'        => 'none',
				'animation_loop_duration'    => '1000',
				'animation_loop_delay'       => '1000',
				'animation_loop_hover'       => '',
			), $atts );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$animation    = MPC_Parser::animation( $atts );
			$main_classes = $atts[ 'text_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'text_font_preset' ] : '';
			$main_classes .= ' mpc-init mpc-transition';
			$main_classes .= $animation != '' ? ' mpc-animation' : '';
			$main_classes .= ' mpc-style--' . $atts[ 'style' ];
			$main_classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$sides_classes = $atts[ 'sides_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'sides_font_preset' ] : '';

			$options = array(
				'style'    => $atts[ 'style' ],
				'duration' => $atts[ 'duration' ],
				'delay'    => $atts[ 'delay' ],
				'loop'     => $atts[ 'disable_loop' ] == '',
				'dynamic'  => $atts[ 'dynamic_width' ] == 'true',
			);

			$words = explode( '|||', $atts[ 'text' ] );

			$return = '<div id="' . $css_id . '" class="mpc-animated-text-wrap' . $main_classes . '" ' . $animation . ' data-options="' . htmlentities( json_encode( $options ), ENT_QUOTES, 'UTF-8' ) . '">';
				if ( $atts[ 'disable_sides' ] == '' && $atts[ 'before_text' ] != '' ) {
					$return .= '<div class="mpc-animated-text__side mpc-animated-text__before' . $sides_classes . '">' . $atts[ 'before_text' ] . '</div>';
				}

				$return .= '<div class="mpc-animated-text">';
				foreach( $words as $word ) {
					$return .= '<div class="mpc-animated-text__block"><span class="mpc-animated-text__word">' . $word . '</span></div>';
				}
				$return .= '</div>';

				if ( $atts[ 'disable_pointer' ] == '' && $atts[ 'style' ] == 'typewrite' ) {
					$return .= '<div class="mpc-animated-text__pointer">&nbsp;</div>';
				}
				if ( $atts[ 'disable_sides' ] == '' && $atts[ 'after_text' ] != '' ) {
					$return .= '<div class="mpc-animated-text__side mpc-animated-text__after' . $sides_classes . '">' . $atts[ 'after_text' ] . '</div>';
				}
			$return .= '</div>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_animated_text-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'text_font_size' ] = $styles[ 'text_font_size' ] != '' ? $styles[ 'text_font_size' ] . ( is_numeric( $styles[ 'text_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'sides_font_size' ] = $styles[ 'sides_font_size' ] != '' ? $styles[ 'sides_font_size' ] . ( is_numeric( $styles[ 'sides_font_size' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-animated-text-wrap[id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Pointer
			$inner_styles = array();
			if ( $styles[ 'pointer_color' ] ) { $inner_styles[] = 'color: ' . $styles[ 'pointer_color' ] . ';'; }
			if ( $styles[ 'text_font_size' ] ) { $inner_styles[] = 'font-size: ' . $styles[ 'text_font_size' ] . ';'; }
			if ( $styles[ 'disable_pointer' ] == '' && $styles[ 'style' ] == 'typewrite' && count( $inner_styles ) > 0 ) {
				$style .= '.mpc-animated-text-wrap[id="' . $css_id . '"] .mpc-animated-text__pointer {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Animated text
			$inner_styles = array();
			if ( $styles[ 'text_border_css' ] ) { $inner_styles[] = $styles[ 'text_border_css' ]; }
			if ( $styles[ 'text_padding_css' ] ) { $inner_styles[] = $styles[ 'text_padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'text' ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::font( $styles, 'text' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-animated-text-wrap[id="' . $css_id . '"] .mpc-animated-text {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Sides
			if ( $temp_style = MPC_CSS::font( $styles, 'sides' ) ) {
				$style .= '.mpc-animated-text-wrap[id="' . $css_id . '"] .mpc-animated-text__side {';
					$style .= $temp_style;
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
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Style', 'mpc' ),
					'param_name'       => 'style',
					'admin_label'      => true,
					'tooltip'          => __( 'Select animation style:<br><b>Rotator</b>: text blocks are sliding in and out;<br><b>Typewrite</b>: texts are written letter by letter.', 'mpc' ),
					'value'            => array(
						__( 'Rotator', 'mpc' )   => 'rotator',
						__( 'Typewrite', 'mpc' ) => 'typewrite',
					),
					'std'              => 'rotator',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Loop Texts', 'mpc' ),
					'param_name'       => 'disable_loop',
					'tooltip'          => __( 'Check to prevent the texts from looping. Disabling loop will leave the last text block visible.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Dynamic Width', 'mpc' ),
					'param_name'       => 'dynamic_width',
					'tooltip'          => __( 'Check to enable dynamic text blocks width. Enabling dynamic width will adjust the width of text blocks so the static text are always at the sides without any gaps.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'dependency'       => array( 'element' => 'style', 'value' => 'rotator' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Animation Duration', 'mpc' ),
					'param_name'       => 'duration',
					'tooltip'          => __( 'Choose how long the text block will animate.', 'mpc' ),
					'min'              => 300,
					'max'              => 5000,
					'step'             => 50,
					'value'            => 300,
					'unit'             => 'ms',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Change Delay', 'mpc' ),
					'param_name'       => 'delay',
					'tooltip'          => __( 'Choose how long the text block should be visible before changing it to the next one.', 'mpc' ),
					'min'              => 1000,
					'max'              => 10000,
					'step'             => 50,
					'value'            => 1000,
					'unit'             => 'ms',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			$pointer = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Pointer', 'mpc' ),
					'subtitle'   => __( 'Pointer settings.', 'mpc' ),
					'param_name' => 'pointer_divider',
					'dependency' => array( 'element' => 'style', 'value' => 'typewrite' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable Pointer', 'mpc' ),
					'param_name'       => 'disable_pointer',
					'value'            => array( __( 'Disable Pointer', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Check to disable pointer at the end of animating text.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'pointer_color',
					'value'            => '',
					'dependency'       => array( 'element' => 'disable_pointer', 'value_not_equal_to' => 'true' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			$text_font       = MPC_Snippets::vc_font( array( 'prefix' => 'text', 'group' => __( 'Animated Text', 'mpc' ) ) );
			$text_background = MPC_Snippets::vc_background( array( 'prefix' => 'text', 'group' => __( 'Animated Text', 'mpc' ) ) );
			$text_border     = MPC_Snippets::vc_border( array( 'prefix' => 'text', 'group' => __( 'Animated Text', 'mpc' ) ) );
			$text_padding    = MPC_Snippets::vc_padding( array( 'prefix' => 'text', 'group' => __( 'Animated Text', 'mpc' ) ) );
			$text            = array(
				array(
					'type'        => 'mpc_split',
					'heading'     => __( 'Animated Text', 'mpc' ),
					'param_name'  => 'text',
					'admin_label' => true,
					'tooltip'     => __( 'Define animation text blocks. Each new line will be a separate text block.', 'mpc' ),
					'value'       => '',
					'group'       => __( 'Animated Text', 'mpc' ),
				),
			);

			$sides  = array(
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Static Texts', 'mpc' ),
					'param_name'  => 'disable_sides',
					'tooltip'     => __( 'Check to disable static texts on the sides.', 'mpc' ),
					'value'       => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'         => '',
					'group'       => __( 'Static Text', 'mpc' ),
				),
			);
			$sides_text = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Before Animated Text', 'mpc' ),
					'param_name'       => 'before_text',
					'admin_label'      => true,
					'value'            => '',
					'tooltip'          => __( 'Define text displayed before animated text blocks.', 'mpc' ),
					'dependency'       => array( 'element' => 'disable_sides', 'value_not_equal_to' => 'true' ),
					'group'            => __( 'Static Text', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'After Animated Text', 'mpc' ),
					'param_name'       => 'after_text',
					'admin_label'      => true,
					'value'            => '',
					'tooltip'          => __( 'Define text displayed after animated text blocks.', 'mpc' ),
					'dependency'       => array( 'element' => 'disable_sides', 'value_not_equal_to' => 'true' ),
					'group'            => __( 'Static Text', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);
			$sides_font = MPC_Snippets::vc_font( array( 'prefix' => 'sides', 'dependency' => array( 'element' => 'disable_sides', 'value_not_equal_to' => 'true' ), 'group' => __( 'Static Text', 'mpc' ) ) );

			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();
			$class      = MPC_Snippets::vc_class();

			$animation  = MPC_Snippets::vc_animation();

			$params = array_merge( $base, $pointer, $text_font, $text, $text_background, $text_border, $text_padding, $sides, $sides_font, $sides_text, $background, $border, $padding, $margin, $animation, $class );

			return array(
				'name'        => __( 'Animated Text', 'mpc' ),
				'description' => __( 'Animated set of texts', 'mpc' ),
				'base'        => 'mpc_animated_text',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-animated-text.png',
				'icon'        => 'mpc-shicon-animated-text',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Animated_Text' ) ) {
	global $MPC_Animated_Text;
	$MPC_Animated_Text = new MPC_Animated_Text;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_animated_text' ) ) {
	class WPBakeryShortCode_mpc_animated_text extends MPCShortCode_Base {}
}

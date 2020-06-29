<?php
/*----------------------------------------------------------------------------*\
	DIVIDER SHORTCODE
\*----------------------------------------------------------------------------*/

if ( !class_exists( 'MPC_divider' ) ) {
	class MPC_Divider {
		public $shortcode     = 'mpc_divider';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_divider', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_divider-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_divider/css/mpc_divider.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_divider-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_divider/js/mpc_divider' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'  => '',
				'preset' => '',
				'align'  => 'center',
				'width'  => '100',

				'content_type'     => 'none',
				'content_position' => '50',

				'lines_number' => '1',
				'lines_style'  => 'solid',
				'lines_color'  => '',
				'lines_gap'    => '1',
				'lines_weight' => '1',

				'title'            => '',
				'font_preset'      => '',
				'font_color'       => '#333333',
				'font_size'        => '18',
				'font_line_height' => '',
				'font_align'       => '',
				'font_transform'   => '',

				'icon_type'       => 'icon',
				'icon'            => '',
				'icon_character'  => '',
				'icon_image'      => '',
				'icon_image_size' => 'thumbnail',
				'icon_preset'     => '',
				'icon_size'       => '',
				'icon_color'      => '#333333',

				'padding_css'         => '',
				'margin_css'          => '',
				'content_border_css'  => '',
				'content_padding_css' => '',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',

				'animation_loop_type'     => 'none',
				'animation_loop_duration' => '1000',
				'animation_loop_delay'    => '1000',
				'animation_loop_hover'    => '',
			), $atts );

			$icon = MPC_Parser::icon( $atts );

			$content       = '';
			$content_class = '';
			if ( $atts[ 'content_type' ] == 'icon' ) {
				$content       = $icon[ 'content' ];
				$content_class = $icon[ 'class' ];
			} elseif ( $atts[ 'content_type' ] == 'title' ) {
				$content       = $atts[ 'title' ];
				$content_class = $atts[ 'font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'font_preset' ] ) : '';
			} elseif ( $atts[ 'content_type' ] == 'none' ) {
				$atts[ 'content_position' ] = 0;
			}

			$lines = ( int ) $atts[ 'lines_number' ] > 0 ? str_repeat( '<span></span>', ( int ) $atts[ 'lines_number' ] ) : '';

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$animation = MPC_Parser::animation( $atts );
			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'align' ] != '' ? ' mpc-align--' . esc_attr( $atts[ 'align' ] ) : '';
			$classes .= $atts[ 'content_position' ] == '0' ? ' mpc-disable--left' : '';
			$classes .= $atts[ 'content_position' ] == '100' ? ' mpc-disable--right' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$return = '<div data-id="' . $css_id . '" class="mpc-divider-wrap">';
				$return .= '<div class="mpc-divider' . $classes . '" ' . $animation . '>';
				$return .= '<div class="mpc-divider__line mpc-side--left">' . $lines . '</div>';
				if ( $atts[ 'content_type' ] != 'none' ) {
					$return .= '<div class="mpc-divider__separator"><div class="mpc-divider__content' . $content_class . '">' . $content . '</div></div>';
				}
				$return .= '<div class="mpc-divider__line mpc-side--right">' . $lines . '</div>';
				$return .= '</div>';
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
			$css_id = uniqid( 'mpc_divider-' . rand( 1, 100 ) );
			$style = '';

			$content_position = $styles[ 'content_position' ] != '' ? ( int ) $styles[ 'content_position' ] : 50;
			$left_width       = $content_position . '%';
			$right_width      = 100 - $content_position . '%';

			// Add 'px'
			$styles[ 'font_size' ]    = $styles[ 'font_size' ] != '' ? $styles[ 'font_size' ] . ( is_numeric( $styles[ 'font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'icon_size' ]    = $styles[ 'icon_size' ] != '' ? $styles[ 'icon_size' ] . ( is_numeric( $styles[ 'icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'width' ]        = $styles[ 'width' ] != '' ? $styles[ 'width' ] . ( is_numeric( $styles[ 'width' ] ) ? '%' : '' ) : '';
			$styles[ 'lines_weight' ] = $styles[ 'lines_weight' ] != '' ? $styles[ 'lines_weight' ] . ( is_numeric( $styles[ 'lines_weight' ] ) ? 'px' : '' ) : '';
			$styles[ 'lines_gap' ]    = $styles[ 'lines_gap' ] != '' ? $styles[ 'lines_gap' ] . ( is_numeric( $styles[ 'lines_gap' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-divider-wrap[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'width' ] ) {
				$style .= '.mpc-divider-wrap[data-id="' . $css_id . '"] .mpc-divider {';
					$style .= 'width:' . $styles[ 'width' ] . ';';
				$style .= '}';
			}

			// Content
			$inner_styles = array();
			if ( $styles[ 'content_type' ] == 'icon' ) {
				if ( $styles[ 'icon_size' ] ) { $inner_styles[] = 'font-size:' . $styles[ 'icon_size' ] . ';'; }
				if ( $styles[ 'icon_color' ] ) { $inner_styles[] = 'color:' . $styles[ 'icon_color' ] . ';'; }
			} elseif ( $styles[ 'content_type' ] == 'title' ) {
				if ( $temp_style = MPC_CSS::font( $styles ) ) { $inner_styles[] = $temp_style; }
			}
			if ( $styles[ 'content_padding_css' ] ) { $inner_styles[] = $styles[ 'content_padding_css' ]; }
			if ( $styles[ 'content_border_css' ] ) { $inner_styles[] = $styles[ 'content_border_css' ]; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-divider-wrap[data-id="' . $css_id . '"] .mpc-divider__content {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Lines
			$inner_styles = array();
			if ( $styles[ 'lines_style' ] ) { $inner_styles[] = 'border-top-style:' . $styles[ 'lines_style' ] . ';'; }
			if ( $styles[ 'lines_color' ] ) { $inner_styles[] = 'border-top-color:' . $styles[ 'lines_color' ] . ';'; }
			if ( $styles[ 'lines_weight' ] ) { $inner_styles[] = 'border-top-width:' . $styles[ 'lines_weight' ] . ';'; }
			if ( $styles[ 'lines_gap' ] ) { $inner_styles[] = 'margin-top:' . $styles[ 'lines_gap' ] . ';'; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-divider-wrap[data-id="' . $css_id . '"] .mpc-divider__line > span {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Lines
			$style .= '.mpc-divider-wrap[data-id="' . $css_id . '"] .mpc-divider__line.mpc-side--left {';
				$style .= 'width:' . $left_width . ';';
			$style .= '}';

			$style .= '.mpc-divider-wrap[data-id="' . $css_id . '"] .mpc-divider__line.mpc-side--right {';
				$style .= 'width:' . $right_width . ';';
			$style .= '}';

			$mpc_massive_styles .= $style;

			return array(
				'id'  => $css_id,
				'css' => $style,
			);
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( !function_exists( 'vc_map' ) ) {
				return;
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
					'type'             => 'mpc_slider',
					'heading'          => __( 'Width', 'mpc' ),
					'param_name'       => 'width',
					'tooltip'          => __( 'Choose divider width. It will take the specified value of its container width.', 'mpc' ),
					'value'            => '100',
					'unit'             => '%',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_align',
					'heading'          => __( 'Alignment', 'mpc' ),
					'param_name'       => 'align',
					'tooltip'          => __( 'Choose divider alignment.', 'mpc' ),
					'value'            => '',
					'std'              => 'center',
					'grid_size'        => 'small',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Content Type', 'mpc' ),
					'param_name'       => 'content_type',
					'admin_label'      => true,
					'tooltip'          => __( 'Choose divider content type.', 'mpc' ),
					'value'            => array(
						__( 'None', 'mpc' ) => 'none',
						__( 'Text', 'mpc' ) => 'title',
						__( 'Icon', 'mpc' ) => 'icon',
					),
					'std'              => 'none',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Content Position', 'mpc' ),
					'param_name'       => 'content_position',
					'tooltip'          => __( 'Choose horizontal divider content position.', 'mpc' ),
					'value'            => '50',
					'unit'             => '%',
					'dependency'       => array( 'element' => 'content_type', 'value' => array( 'title', 'icon' ) ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			$title = array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Text', 'mpc' ),
					'param_name'  => 'title',
					'admin_label' => true,
					'tooltip'     => __( 'Define content text.', 'mpc' ),
					'value'       => '',
				),
			);

			$lines = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Lines', 'mpc' ),
					'param_name' => 'lines_divider',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Number', 'mpc' ),
					'param_name'       => 'lines_number',
					'tooltip'          => __( 'Select number of divider lines.', 'mpc' ),
					'value'            => array(
						__( 'One', 'mpc' )   => 1,
						__( 'Two', 'mpc' )   => 2,
						__( 'Three', 'mpc' ) => 3,
					),
					'std'              => '1',
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Style', 'mpc' ),
					'param_name'       => 'lines_style',
					'tooltip'          => __( 'Select lines style. Learn more about line styles <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/border-style" target="_blank">here</a>.', 'mpc' ),
					'value'            => array(
						__( 'Solid', 'mpc' )  => 'solid',
						__( 'Dotted', 'mpc' ) => 'dotted',
						__( 'Dashed', 'mpc' ) => 'dashed',
						__( 'Double', 'mpc' ) => 'double',
						__( 'Groove', 'mpc' ) => 'groove',
						__( 'Ridge', 'mpc' )  => 'ridge',
					),
					'std'              => 'solid',
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'lines_color',
					'tooltip'          => __( 'Choose lines color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-color-picker',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Gap', 'mpc' ),
					'param_name'       => 'lines_gap',
					'tooltip'          => __( 'Choose vertical gap between lines. Small lines <b>Gap</b> and large lines <b>Weight</b> may lead to lines overlap.', 'mpc' ),
					'max'              => 20,
					'value'            => '1',
					'unit'             => 'px',
					'dependency'       => array( 'element' => 'lines_number', 'value' => array( '2', '3' ) ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'         => 'mpc_slider',
					'heading'      => __( 'Weight', 'mpc' ),
					'param_name'   => 'lines_weight',
					'tooltip'          => __( 'Choose lines thickness.', 'mpc' ),
					'min'          => 1,
					'max'          => 20,
					'value'        => '1',
					'unit'         => 'px',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			$title_font = MPC_Snippets::vc_font( array( 'subtitle' => __( 'Title', 'mpc' ), 'dependency' => array( 'element' => 'content_type', 'value' => 'title' ) ) );
			$icon       = MPC_Snippets::vc_icon( array( 'dependency' => array( 'element' => 'content_type', 'value' => 'icon' ) ) );

			$content_padding = MPC_Snippets::vc_padding( array( 'prefix' => 'content', 'subtitle' => __( 'Content', 'mpc' ) ) );
			$content_border  = MPC_Snippets::vc_border( array( 'prefix' => 'content', 'subtitle' => __( 'Content', 'mpc' ) ) );
			$padding         = MPC_Snippets::vc_padding();
			$margin          = MPC_Snippets::vc_margin();

			$animation = MPC_Snippets::vc_animation();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$content_border,
				$content_padding,
				$title_font,
				$title,
				$icon,
				$lines,
				$padding,
				$margin,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Divider', 'mpc' ),
				'description' => __( 'Stylish horizontal line with icon', 'mpc' ),
				'base'        => 'mpc_divider',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-divider.png',
				'icon'        => 'mpc-shicon-divider',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Divider' ) ) {
	global $MPC_Divider;
	$MPC_Divider = new MPC_Divider;
}

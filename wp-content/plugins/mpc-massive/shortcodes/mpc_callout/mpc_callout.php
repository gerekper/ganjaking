<?php
/*----------------------------------------------------------------------------*\
	CALLOUT SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Callout' ) ) {
	class MPC_Callout {
		public $shortcode = 'mpc_callout';
		private $parts = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_callout', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			$parts = array(
				'section_begin' => '',
				'section_end'   => '',
				'icon'          => '',
				'divider'       => '',
				'button'        => '',
				'title'         => '',
				'description'   => '',
			);

			$this->parts = $parts;
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_callout-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_callout/css/mpc_callout.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_callout-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_callout/js/mpc_callout' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Build shortcode layout */
		function shortcode_layout( $style, $parts ) {
			$content = '';

			$layouts = array(
				'style_1' => array( 'icon', 'section_begin', 'title', 'divider', 'description', 'section_end', 'button' ),
				'style_2' => array( 'icon', 'section_begin', 'title', 'divider', 'description', 'section_end', 'button' ),
				'style_3' => array( 'icon', 'section_begin', 'divider', 'title', 'description', 'section_end', 'button' ),
				'style_4' => array( 'button', 'section_begin', 'title', 'divider', 'description', 'section_end', 'icon' ),
				'style_5' => array( 'section_begin', 'title', 'divider', 'description', 'section_end', 'icon', 'button' ),
				'style_6' => array( 'icon', 'section_begin', 'title', 'description', 'divider', 'section_end', 'button' ),
				'style_7' => array( 'button', 'section_begin', 'divider', 'title', 'description', 'section_end', 'icon' ),
				'style_8' => array( 'icon', 'section_begin', 'title', 'divider', 'description', 'button', 'section_end' ),
				'style_9' => array( 'icon', 'section_begin', 'title', 'divider', 'description', 'button', 'section_end' ),
			);

			if( ! isset( $layouts[ $style ] ) )
				return '';

			foreach( $layouts[ $style ] as $part ) {
				$content .= $parts[ $part ];
			}

			return $content;
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $description = null ) {
			global $MPC_Button, $MPC_Divider, $MPC_Ribbon, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'  => '',
				'preset' => '',
				'layout' => 'style_1',

				'title_font_preset'      => '',
				'title_font_color'       => '',
				'title_font_size'        => '',
				'title_font_line_height' => '',
				'title_font_align'       => '',
				'title_font_transform'   => '',
				'title'                  => '',
				'title_margin_css'       => '',

				'content_font_preset'      => '',
				'content_font_color'       => '',
				'content_font_size'        => '',
				'content_font_line_height' => '',
				'content_font_align'       => '',
				'content_font_transform'   => '',
				'content_width'            => '100',
				'content_margin_css'       => '',

				'icon_disable'    => '',
				'icon_type'       => 'icon',
				'icon'            => '',
				'icon_character'  => '',
				'icon_preset'     => '',
				'icon_color'      => '#333333',
				'icon_image'      => '',
				'icon_image_size' => 'thumbnail',
				'icon_size'       => '',
				'icon_align'      => '',

				'icon_padding_css'           => '',
				'icon_margin_css'            => '',
				'icon_border_css'            => '',
				'icon_background_type'       => 'color',
				'icon_background_color'      => '',
				'icon_background_image'      => '',
				'icon_background_image_size' => 'large',
				'icon_background_repeat'     => 'no-repeat',
				'icon_background_size'       => 'initial',
				'icon_background_position'   => 'middle-center',
				'icon_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'icon_background_effect'     => 'fade-in',

				'link_color'       => '',
				'link_hover_color' => '',

				'padding_css' => '',
				'margin_css'  => '',
				'border_css'  => '',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'background_effect'     => 'fade-in',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',

				'animation_loop_type'     => 'none',
				'animation_loop_duration' => '1000',
				'animation_loop_delay'    => '1000',
				'animation_loop_hover'    => '',

				/* Divider */
				'mpc_divider__disable'    => '',
				'mpc_divider__align'      => 'center',
				'mpc_divider__width'      => '100',

				'mpc_divider__content_type'     => 'none',
				'mpc_divider__content_position' => '50',

				'mpc_divider__lines_number' => '1',
				'mpc_divider__lines_style'  => 'solid',
				'mpc_divider__lines_color'  => '',
				'mpc_divider__lines_gap'    => '1',
				'mpc_divider__lines_weight' => '1',

				'mpc_divider__title'            => '',
				'mpc_divider__font_preset'      => '',
				'mpc_divider__font_color'       => '#333333',
				'mpc_divider__font_size'        => '18',
				'mpc_divider__font_line_height' => '',
				'mpc_divider__font_align'       => '',
				'mpc_divider__font_transform'   => '',

				'mpc_divider__icon_type'       => 'icon',
				'mpc_divider__icon'            => '',
				'mpc_divider__icon_character'  => '',
				'mpc_divider__icon_image'      => '',
				'mpc_divider__icon_image_size' => 'thumbnail',
				'mpc_divider__icon_preset'     => '',
				'mpc_divider__icon_size'       => '',
				'mpc_divider__icon_color'      => '#333333',

				'mpc_divider__padding_css' => '',
				'mpc_divider__margin_css'  => '',

				/* Button */
				'mpc_button__class'        => '',
				'mpc_button__disable'      => '',
				'mpc_button__align'        => '',
				'mpc_button__title'        => '',
				'mpc_button__url'          => '',
				'mpc_button__block'        => '',

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
				'mpc_button__icon_effect'     => 'none-none',
				'mpc_button__icon_gap'        => '',

				'mpc_button__background_type'       => 'color',
				'mpc_button__background_color'      => '',
				'mpc_button__background_image'      => '',
				'mpc_button__background_image_size' => 'large',
				'mpc_button__background_repeat'     => 'no-repeat',
				'mpc_button__background_size'       => 'initial',
				'mpc_button__background_position'   => 'middle-center',
				'mpc_button__background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

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

				'mpc_button__hover_background_effect' => 'fade-in',
				'mpc_button__hover_background_offset' => '',

				'mpc_button__animation_loop_type'     => 'none',
				'mpc_button__animation_loop_duration' => '1000',
				'mpc_button__animation_loop_delay'    => '1000',
				'mpc_button__animation_loop_hover'    => '',

				/* Ribbon */
				'mpc_ribbon__disable'                 => '',
				'mpc_ribbon__preset'                  => '',
				'mpc_ribbon__text'                    => '',
				'mpc_ribbon__style'                   => 'classic',
				'mpc_ribbon__alignment'               => 'top-left',
				'mpc_ribbon__corners_color'           => '',
				'mpc_ribbon__size'                    => 'medium',

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
			$animation    = MPC_Parser::animation( $atts );
			$atts_button  = MPC_Parser::shortcode( $atts, 'mpc_button_' );
			$atts_divider = MPC_Parser::shortcode( $atts, 'mpc_divider_' );
			$atts_icon    = MPC_Parser::icon( $atts );
			$atts_ribbon  = MPC_Parser::shortcode( $atts, 'mpc_ribbon_' );
			$ribbon       = $atts[ 'mpc_ribbon__disable' ] == '' ? $MPC_Ribbon->shortcode_template( $atts_ribbon ) : '';

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'layout' ] != '' ? ' mpc-callout--' . $atts[ 'layout' ] : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );
			$classes_title   = $atts[ 'title_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'title_font_preset' ] : '';
			$classes_content = $atts[ 'content_font_preset' ] != '' ? ' mpc-typography--' . $atts[ 'content_font_preset' ] : '';
			$classes_button  = $atts[ 'mpc_button__align' ] != '' ? ' mpc-align--' . $atts[ 'mpc_button__align' ] : '';
			$classes_icon    = $atts[ 'icon_align' ] != '' ? ' mpc-align--' . $atts[ 'icon_align' ] : '';
			$classes_icon   .= $atts[ 'icon_type' ] == 'image' ? ' mpc-icon--image' : '';

			/* Layout parts */
			$this->parts[ 'section_begin' ] = '<div class="mpc-callout__content">';
			$this->parts[ 'section_end' ]   = '</div>';
			$this->parts[ 'button' ]        = $atts[ 'mpc_button__disable' ] == '' ? '<div class="mpc-callout__button' . $classes_button . '">' . $MPC_Button->shortcode_template( $atts_button ) . '</div>' : '';
			$this->parts[ 'divider' ]       = $atts[ 'mpc_divider__disable' ] == '' ? $MPC_Divider->shortcode_template( $atts_divider ) : '';
			$this->parts[ 'icon' ]          = $atts[ 'icon_disable' ] == '' ?'<div class="mpc-callout__icon-wrap' . $classes_icon . '"><div class="mpc-callout__icon"><i class="mpc-transition ' . $atts_icon[ 'class' ] . '">' . $atts_icon[ 'content' ] . '</i></div></div>' : '';
			$this->parts[ 'title' ]         = $atts[ 'title' ] != '' ? '<h3 class="mpc-callout__heading' . $classes_title . '">' . $atts[ 'title' ] . '</h3>' : '';
			$this->parts[ 'description' ]   = $description != '' ? '<div class="mpc-callout__description' . $classes_content . '">' . wpb_js_remove_wpautop( $description, true ) . '</div>' : '';

			/* Shortcode Output */
			$return = $ribbon != '' ? '<div class="mpc-ribbon-wrap">' : '';
				$return .= '<div data-id="' . $css_id . '" class="mpc-callout' . $classes . '" ' . $animation . '>';
					$return .= $this->shortcode_layout( $atts[ 'layout' ], $this->parts );
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
			$css_id = uniqid( 'mpc_callout-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'content_font_size' ] = $styles[ 'content_font_size' ] != '' ? $styles[ 'content_font_size' ] . ( is_numeric( $styles[ 'content_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'title_font_size' ]   = $styles[ 'title_font_size' ] != '' ? $styles[ 'title_font_size' ] . ( is_numeric( $styles[ 'title_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'icon_size' ]         = $styles[ 'icon_size' ] != '' ? $styles[ 'icon_size' ] . ( is_numeric( $styles[ 'icon_size' ] ) ? 'px' : '' ) : '';

			// Add '%'
			$styles[ 'content_width' ] = $styles[ 'content_width' ] != '' ? $styles[ 'content_width' ] . ( is_numeric( $styles[ 'content_width' ] ) ? '%' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-callout[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Heading
			$inner_styles = array();
			// Check if center of heading is allowed
			$allow_center = true;
			if( false !== strpos( $styles[ 'title_margin_css' ], 'left' )
			    || false !== strpos( $styles[ 'title_margin_css' ], 'right' )
			    || false !== strpos( $styles[ 'title_margin_css' ], 'margin:' ) ) {
				$allow_center = false;
			}
			if ( $styles[ 'content_width' ] ) { $inner_styles[] = 'width: ' . $styles[ 'content_width' ] . ';'; }
			if ( $styles[ 'title_margin_css' ] ) { $inner_styles[] = $styles[ 'title_margin_css' ]; }
			if ( $allow_center && intval( $styles[ 'content_width'] ) !== 100 ) {
				if( $styles[ 'title_font_align' ] === 'right' ) {
					$inner_styles[] = 'margin-left: ' . ( 100 - intval( $styles[ 'content_width' ] ) ) . '%;';
				} else if( $styles[ 'title_font_align' ] === 'center' || $styles[ 'title_font_align' ] === 'justify' ) {
					$inner_styles[] = 'margin-left: ' . ( 100 - intval( $styles[ 'content_width' ] ) ) * .5 . '%;';
					$inner_styles[] = 'margin-right: ' . ( 100 - intval( $styles[ 'content_width' ] ) ) * .5 . '%;';
				}
			}
			if ( $temp_style = MPC_CSS::font( $styles, 'title' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-callout[data-id="' . $css_id . '"] .mpc-callout__heading {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Description
			$inner_styles = array();
			// Check if center of content is allowed
			$allow_center = true;
			if( false !== strpos( $styles[ 'content_margin_css' ], 'left' )
			    || false !== strpos( $styles[ 'content_margin_css' ], 'right' )
			    || false !== strpos( $styles[ 'content_margin_css' ], 'margin:' ) ) {
				$allow_center = false;
			}
			if ( $styles[ 'content_width' ] ) { $inner_styles[] = 'width: ' . $styles[ 'content_width' ] . ';'; }
			if ( $styles[ 'content_margin_css' ] ) { $inner_styles[] = $styles[ 'content_margin_css' ]; }
			if ( $allow_center && intval( $styles[ 'content_width'] ) !== 100 ) {
				if( $styles[ 'content_font_align' ] === 'right' ) {
					$inner_styles[] = 'margin-left: ' . ( 100 - intval( $styles[ 'content_width' ] ) ) . '%;';
				} else if( $styles[ 'content_font_align' ] === 'center' || $styles[ 'content_font_align' ] === 'justify' ) {
					$inner_styles[] = 'margin-left: ' . ( 100 - intval( $styles[ 'content_width' ] ) ) * .5 . '%;';
					$inner_styles[] = 'margin-right: ' . ( 100 - intval( $styles[ 'content_width' ] ) ) * .5 . '%;';
				}
			}
			if ( $temp_style = MPC_CSS::font( $styles, 'content' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-callout[data-id="' . $css_id . '"] .mpc-callout__description {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'link_color' ] ) {
				$style .= '.mpc-callout[data-id="' . $css_id . '"] .mpc-callout__description a {';
					$style .= 'color: ' . $styles[ 'link_color' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'link_hover_color' ] ) {
				$style .= '.mpc-callout[data-id="' . $css_id . '"] .mpc-callout__description a:hover {';
					$style .= 'color: ' . $styles[ 'link_hover_color' ] . ';';
				$style .= '}';
			}

			// Icon
			$inner_styles = array();
			if ( $styles[ 'icon_border_css' ] ) { $inner_styles[] = $styles[ 'icon_border_css' ]; }
			if ( $styles[ 'icon_padding_css' ] ) { $inner_styles[] = $styles[ 'icon_padding_css' ]; }
			if ( $styles[ 'icon_margin_css' ] ) { $inner_styles[] = $styles[ 'icon_margin_css' ]; }
			if ( $temp_style = MPC_CSS::icon( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::background( $styles, 'icon' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-callout[data-id="' . $css_id . '"] .mpc-callout__icon {';
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
				array(
					'type'             => 'mpc_layout_select',
					'heading'          => __( 'Layout Select', 'mpc' ),
					'param_name'       => 'layout',
					'tooltip'          => __( 'Layout styles let you choose the target layout after you define the shortcode options.', 'mpc' ),
					'value'            => '',
					'columns'          => '3',
					'layouts'          => array(
						'style_1' => '1',
						'style_2' => '5',
						'style_3' => '5',
						'style_4' => '1',
						'style_5' => '1',
						'style_6' => '5',
						'style_7' => '5',
						'style_8' => '2',
						'style_9' => '2',
					),
					'std'              => 'style_1',
					'shortcode'        => $this->shortcode,
					'description'      => __( 'Choose layout style.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			/* SECTION TITLE */
			$title = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Title', 'mpc' ),
					'param_name'       => 'title',
					'admin_label'      => true,
					'tooltip'          => __( 'Define title.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Content', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			/* SECTION DESCRIPTION */
			$description_text = array(
				array(
					'type'        => 'textarea_html',
					'holder'      => 'div',
					'heading'     => __( 'Description', 'mpc' ),
					'tooltip'     => __( 'Define content. Thanks to default WordPress WYSIWYG editor you can easily format the content.', 'mpc' ),
					'param_name'  => 'content',
					'value'       => '',
					'group'       => __( 'Content', 'mpc' ),
				),
			);

			$description = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Description', 'mpc' ),
					'param_name'       => 'description_divider',
					'std'              => '',
					'group'            => __( 'Content', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Width', 'mpc' ),
					'param_name'       => 'content_width',
					'tooltip'          => __( 'Choose description width. If you choose width smaller then 100% it will be centered by default.', 'mpc' ),
					'value'            => 100,
					'std'              => 100,
					'min'              => 10,
					'max'              => 100,
					'unit'             => '%',
					'group'            => __( 'Content', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Link Color', 'mpc' ),
					'param_name'       => 'link_color',
					'tooltip'          => __( 'Choose description links color.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Content', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Link Hover Color', 'mpc' ),
					'param_name'       => 'link_hover_color',
					'tooltip'          => __( 'If you want to change the link color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Content', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
			);

			/* Icon */
			$disable_icon = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Icon', 'mpc' ),
					'param_name'       => 'icon_disable',
					'tooltip'          => __( 'Check to disable icon.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Icon', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'mpc_align',
					'heading'          => __( 'Icon Alignment', 'mpc' ),
					'param_name'       => 'icon_align',
					'tooltip'          => __( 'Choose icon alignment.', 'mpc' ),
					'grid_size'        => 'small',
					'value'            => 'center',
					'group'            => __( 'Icon', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-first-row',
					'dependency'       => array( 'element' => 'icon_disable', 'value_not_equal_to' => 'true' ),
				),
			);

			/* Integrate Button */
			$button_exclude    = array( 'exclude_regex' => '/tooltip(.*)|animation_in(.*)/', );
			$integrate_button  = vc_map_integrate_shortcode( 'mpc_button', 'mpc_button__', __( 'Button', 'mpc' ), $button_exclude );
			$disable_button = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Button', 'mpc' ),
					'param_name'       => 'mpc_button__disable',
					'tooltip'          => __( 'Check to disable button.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Button', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-section-disabler',
				),
				array(
					'type'             => 'mpc_align',
					'heading'          => __( 'Button Alignment', 'mpc' ),
					'param_name'       => 'mpc_button__align',
					'tooltip'          => __( 'Choose button alignment.', 'mpc' ),
					'grid_size'        => 'small',
					'value'            => 'center',
					'group'            => __( 'Button', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-first-row',
				),
			);
			$integrate_button = array_merge( $disable_button, $integrate_button );

			/* Integrate Divider */
			$divider_exclude   = array( 'exclude_regex' => '/animation_(.*)/', );
			$integrate_divider = vc_map_integrate_shortcode( 'mpc_divider', 'mpc_divider__', __( 'Divider', 'mpc' ), $divider_exclude );
			$disable_divider   = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Divider', 'mpc' ),
					'param_name'       => 'mpc_divider__disable',
					'tooltip'          => __( 'Check to disable divider.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Divider', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_divider = array_merge( $disable_divider, $integrate_divider );

			$icon            = MPC_Snippets::vc_icon( array( 'group' => __( 'Icon', 'mpc' ), 'dependency' => array( 'element' => 'icon_disable', 'value_not_equal_to' => 'true' ), ) );
			$icon_border     = MPC_Snippets::vc_border( array( 'prefix' => 'icon', 'subtitle' => __( 'Icon', 'mpc' ), 'group' => __( 'Icon', 'mpc' ), 'dependency' => array( 'element' => 'icon_disable', 'value_not_equal_to' => 'true' ), ) );
			$icon_padding    = MPC_Snippets::vc_padding( array( 'prefix' => 'icon', 'subtitle' => __( 'Icon', 'mpc' ), 'group' => __( 'Icon', 'mpc' ), 'dependency' => array( 'element' => 'icon_disable', 'value_not_equal_to' => 'true' ), ) );
			$icon_margin     = MPC_Snippets::vc_margin( array( 'prefix' => 'icon', 'subtitle' => __( 'Icon', 'mpc' ), 'group' => __( 'Icon', 'mpc' ), 'dependency' => array( 'element' => 'icon_disable', 'value_not_equal_to' => 'true' ), ) );
			$icon_background = MPC_Snippets::vc_background( array( 'prefix' => 'icon', 'subtitle' => __( 'Icon', 'mpc' ), 'group' => __( 'Icon', 'mpc' ), 'dependency' => array( 'element' => 'icon_disable', 'value_not_equal_to' => 'true' ), ) );

			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();

			$title_font         = MPC_Snippets::vc_font( array( 'prefix' => 'title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Content', 'mpc' ) ) );
			$title_margin       = MPC_Snippets::vc_margin( array( 'prefix' => 'title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Content', 'mpc' ) ) );
			$description_font   = MPC_Snippets::vc_font( array( 'prefix' => 'content', 'subtitle' => __( 'Description', 'mpc' ), 'group' => __( 'Content', 'mpc' ) ) );
			$description_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'content', 'subtitle' => __( 'Description', 'mpc' ), 'group' => __( 'Content', 'mpc' ) ) );

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

			$animation  = MPC_Snippets::vc_animation();
			$class      = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$title_font,
				$title,
				$title_margin,
				$description,
				$description_font,
				$description_text,
				$description_margin,
				$disable_icon,
				$icon,
				$icon_background,
				$icon_border,
				$icon_padding,
				$icon_margin,
				$background,
				$border,
				$padding,
				$margin,
				$integrate_button,
				$integrate_divider,
				$integrate_ribbon,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Callout', 'mpc' ),
				'description' => __( 'Build eye catching callout box', 'mpc' ),
				'base'        => 'mpc_callout',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-call-out.png',
				'icon'        => 'mpc-shicon-callout',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Callout' ) ) {
	global $MPC_Callout;
	$MPC_Callout = new MPC_Callout;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_callout' ) ) {
	class WPBakeryShortCode_mpc_callout extends MPCShortCode_Base {}
}

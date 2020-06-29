<?php
/*----------------------------------------------------------------------------*\
	QUOTE SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Quote' ) ) {
	class MPC_Quote {
		public $shortcode = 'mpc_quote';
		private $parts = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_quote', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			$this->parts = array(
				'section_begin' => '',
				'section_end'   => '',
				'icon'          => '',
				'signature'     => '',
				'description'   => '',
			);
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_quote-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_quote/css/mpc_quote.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_quote-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_quote/js/mpc_quote' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Build shortcode layout */
		function shortcode_layout( $style, $parts ) {
			$content = '';

			$layouts = array(
				'style_1' => array( 'icon', 'section_begin', 'description', 'signature', 'section_end' ),
				'style_2' => array( 'icon', 'section_begin', 'signature', 'description', 'section_end' ),
				'style_3' => array( 'icon', 'section_begin', 'description', 'signature', 'section_end' ),
				'style_4' => array( 'icon', 'section_begin', 'signature', 'description', 'section_end' ),
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
			global $MPC_Ribbon, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                   => '',
                'layout'                  => 'style_1',
                'alignment'               => 'left',

                'quote_font_preset'       => '',
                'quote_font_color'        => '',
                'quote_font_size'         => '',
                'quote_font_line_height'  => '',
                'quote_font_align'        => '',
                'quote_font_transform'    => '',

                'author_font_preset'      => '',
                'author_font_color'       => '',
                'author_font_size'        => '',
                'author_font_line_height' => '',
                'author_font_align'       => '',
                'author_font_transform'   => '',
                'author'                  => '',

                'background_type'         => 'color',
                'background_color'        => '',
                'background_repeat'       => 'no-repeat',
                'background_size'         => 'initial',
                'background_gradient'     => '#83bae3||#80e0d4||0;100||180||linear',
                'background_image'        => '',
                'background_position'     => 'middle-center',
                'background_image_size'   => 'large',

                'icon_type'                  => 'icon',
                'icon'                       => '',
                'icon_character'             => '',
                'icon_image'                 => '',
                'icon_image_size'            => 'thumbnail',
                'icon_preset'                => '',
                'icon_size'                  => '#333333',
                'icon_color'                 => '',
                'icon_opacity'               => 50,

                'icon_background_type'       => 'color',
                'icon_background_color'      => '',
                'icon_background_repeat'     => 'no-repeat',
                'icon_background_size'       => 'initial',
                'icon_background_position'   => 'middle-center',
                'icon_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
                'icon_background_image'      => '',
                'icon_background_image_size' => 'large',

                'icon_border_css'            => '',
                'icon_padding_css'           => '',
                'icon_margin_css'            => '',

                'padding_css'                => '',
                'margin_css'                 => '',
                'border_css'                 => '',

                'animation_in_type'          => 'none',
                'animation_in_duration'      => '300',
                'animation_in_delay'         => '0',
                'animation_in_offset'        => '100',

                'animation_loop_type'        => 'none',
                'animation_loop_duration'    => '1000',
                'animation_loop_delay'       => '1000',
                'animation_loop_hover'       => '',

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

			/* Prepare */
			$animation = MPC_Parser::animation( $atts );
			$icon      = MPC_Parser::icon( $atts );

			$atts_ribbon = MPC_Parser::shortcode( $atts, 'mpc_ribbon_' );
			$ribbon      = $atts[ 'mpc_ribbon__disable' ] == '' ? $MPC_Ribbon->shortcode_template( $atts_ribbon ) : '';

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init mpc-transition';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'layout' ] != '' ? ' mpc-quote--' . esc_attr( $atts[ 'layout' ] ) : '';
			$classes .= $atts[ 'alignment' ] != '' ? ' mpc-icon--' . esc_attr( $atts[ 'alignment' ] ) : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$quote_classes  = $atts[ 'quote_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'quote_font_preset' ] ) : '';
			$author_classes = $atts[ 'author_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'author_font_preset' ] ) : '';

			/* Layout parts */
			$this->parts[ 'section_begin' ] = '<div class="mpc-quote__content' . $quote_classes . '">';
			$this->parts[ 'section_end' ]   = '</div>';
			$this->parts[ 'description' ]   = '<div class="mpc-quote__description">' . wpb_js_remove_wpautop( $description, true ) . '</div>';
			$this->parts[ 'signature' ]     = '<div class="mpc-quote__signature' . $author_classes . '"><span>' . $atts[ 'author' ] . '</span></div>';
			$this->parts[ 'icon' ]          = !empty( $icon ) ? '<div class="mpc-quote__icon-wrapper"><i class="mpc-quote__icon mpc-transition ' . $icon[ 'class' ] . '">' . $icon[ 'content' ] . '</i></div>' : '';

			/* Shortcode Output */
			$return = $ribbon != '' ? '<div class="mpc-ribbon-wrap">' : '';
				$return .= '<div data-id="' . $css_id . '" class="mpc-quote' . $classes . '" ' . $animation . '>';
					$return .= '<div class="mpc-quote__wrapper">';
						$return .= $this->shortcode_layout( $atts[ 'layout' ], $this->parts );
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
			$css_id = uniqid( 'mpc_quote-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'author_font_size' ] = $styles[ 'author_font_size' ] != '' ? $styles[ 'author_font_size' ] . ( is_numeric( $styles[ 'author_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'quote_font_size' ]  = $styles[ 'quote_font_size' ] != '' ? $styles[ 'quote_font_size' ] . ( is_numeric( $styles[ 'quote_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'icon_size' ]   = $styles[ 'icon_size' ] != '' ? $styles[ 'icon_size' ] . ( is_numeric( $styles[ 'icon_size' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-quote[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Icon
			$inner_styles = array();
			if ( $styles[ 'icon_opacity' ] && ( $styles [ 'layout' ] == 'style_3' || $styles [ 'layout' ] == 'style_4' ) ) { $inner_styles[] = 'opacity: ' . ( (int) $styles[ 'icon_opacity' ] / 100 ) . '; filter: alpha( opacity=' . $styles[ 'icon_opacity' ] . ');'; }
			if ( $styles[ 'icon_border_css' ] ) { $inner_styles[] = $styles[ 'icon_border_css' ]; }
			if ( $styles[ 'icon_padding_css' ] ) { $inner_styles[] = $styles[ 'icon_padding_css' ]; }
			if ( $temp_style = MPC_CSS::icon( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::background( $styles, 'icon' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-quote[data-id="' . $css_id . '"] .mpc-quote__icon {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $temp_style = $styles[ 'icon_margin_css' ] ) {
				$style .= '.mpc-quote[data-id="' . $css_id . '"] .mpc-quote__icon-wrapper {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Content
			if ( $temp_style = MPC_CSS::font( $styles, 'quote' ) ) {
				$style .= '.mpc-quote[data-id="' . $css_id . '"] .mpc-quote__description {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Author
			if ( $temp_style = MPC_CSS::font( $styles, 'author' ) ) {
				$style .= '.mpc-quote[data-id="' . $css_id . '"] .mpc-quote__signature {';
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
					'type'             => 'mpc_layout_select',
					'heading'          => __( 'Layout Select', 'mpc' ),
					'param_name'       => 'layout',
					'tooltip'          => __( 'Layout styles let you choose the target layout after you define the shortcode options.', 'mpc' ),
					'value'            => '',
					'columns'          => '5',
					'layouts'          => array(
						'style_1' => '2',
						'style_2' => '2',
						'style_3' => '2',
						'style_4' => '2',
					),
					'std'              => 'style_1',
					'shortcode'        => $this->shortcode,
					'description'      => __( 'Choose layout style.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			/* SECTION AUTHOR */
			$author = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Author', 'mpc' ),
					'param_name'       => 'author',
					'admin_label'      => true,
					'tooltip'          => __( 'Define author name.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			/* SECTION QUOTE */
			$quote = array(
				array(
					'type'        => 'textarea_html',
					'heading'     => __( 'Content', 'mpc' ),
					'param_name'  => 'content',
					'holder'      => 'div',
					'tooltip'     => __( 'Define content. Thanks to default WordPress WYSIWYG editor you can easily format the content.', 'mpc' ),
					'value'       => '',
				),
			);

			/* SECTION ICON */
			$icon_opacity = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Position', 'mpc' ),
					'param_name'       => 'alignment',
					'tooltip'          => __( 'Select icon position.', 'mpc' ),
					'value'            => array(
						__( 'Left', 'mpc' )    => 'left',
						__( 'Right', 'mpc' )   => 'right',
					),
					'std'              => 'left',
					'group'            => __( 'Icon', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'        => 'mpc_slider',
					'heading'     => __( 'Opacity', 'mpc' ),
					'param_name'  => 'icon_opacity',
					'tooltip'     => __( 'Choose icon opacity.', 'mpc' ),
					'value'       => 100,
					'min'         => 0,
					'max'         => 100,
					'step'        => 1,
					'unit'        => '%',
					'group'       => __( 'Icon', 'mpc' ),
					'dependency'  => array(
						'element'   => 'layout',
						'value' => array( 'style_3', 'style_4' ),
					),
				),
			);

			$integrate_ribbon = vc_map_integrate_shortcode( 'mpc_ribbon', 'mpc_ribbon__', __( 'Ribbon', 'mpc' ) );
			$disable_ribbon   = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Ribbon', 'mpc' ),
					'param_name'       => 'mpc_ribbon__disable',
					'tooltip'          => __( 'Check disable ribbon.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Ribbon', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_ribbon = array_merge( $disable_ribbon, $integrate_ribbon );

			$icon_atts = array( 'prefix' => 'icon', 'subtitle' => __( 'Icon', 'mpc' ), 'group' => __( 'Icon', 'mpc' ) );

			$icon            = MPC_Snippets::vc_icon( array( 'group' => __( 'Icon', 'mpc' ) ) );
			$icon_background = MPC_Snippets::vc_background( $icon_atts );
			$icon_border     = MPC_Snippets::vc_border( $icon_atts );
			$icon_padding    = MPC_Snippets::vc_padding( $icon_atts );
			$icon_margin     = MPC_Snippets::vc_margin( $icon_atts );

			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();

			$author_font = MPC_Snippets::vc_font( array( 'prefix' => 'author', 'subtitle' => __( 'Author', 'mpc' ) ) );
			$quote_font  = MPC_Snippets::vc_font( array( 'prefix' => 'quote', 'subtitle' => __( 'Quote', 'mpc' ) ) );

			$animation = MPC_Snippets::vc_animation();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$background,
				$author_font,
				$author,
				$quote_font,
				$quote,
				$icon,
				$icon_opacity,
				$icon_border,
				$icon_background,
				$icon_padding,
				$icon_margin,
				$border,
				$padding,
				$margin,
				$integrate_ribbon,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Quote', 'mpc' ),
				'description' => __( 'Quote text block', 'mpc' ),
				'base'        => 'mpc_quote',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-quote.png',
				'icon'        => 'mpc-shicon-quote',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Quote' ) ) {
	global $MPC_Quote;
	$MPC_Quote = new MPC_Quote;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_quote' ) ) {
	class WPBakeryShortCode_mpc_quote extends MPCShortCode_Base {}
}

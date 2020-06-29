<?php
/*----------------------------------------------------------------------------*\
	PRICING BOX SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Pricing_Box' ) ) {
	class MPC_Pricing_Box {
		public $shortcode = 'mpc_pricing_box';
		public $parts = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_pricing_box', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_pricing_box-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_pricing_box/css/mpc_pricing_box.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_pricing_box-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_pricing_box/js/mpc_pricing_box' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			/* Enqueues */
			wp_enqueue_style( 'mpc-massive-slick-css', mpc_get_plugin_path( __FILE__ ) . '/assets/css/libs/slick.min.css' );
			wp_enqueue_script( 'mpc-massive-slick-js', mpc_get_plugin_path( __FILE__ ) . '/assets/js/libs/slick.min.js', array( 'jquery' ), '', true );

			global $MPC_Shortcode, $MPC_Navigation, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                  => '',
				'preset'                 => '',
				'content_preset'         => '',
				'cols'                   => '4',

				/* Slider */
				'layout'                 => 'classic',
				'slider_disable'         => '',
				'single_scroll'          => '',
				'rows'                   => '1',
				'gap'                    => '0',
				'auto_slide'             => '',
				'delay'                  => '1000',

				/* Title */
				'title_font_preset'      => '',
				'title_font_color'       => '',
				'title_font_size'        => '',
				'title_font_line_height' => '',
				'title_font_align'       => '',
				'title_font_transform'   => '',
				'title_disable'          => '',
				'title_padding_css'      => '',

				/* Price */
				'price_font_preset'      => '',
				'price_font_color'       => '',
				'price_font_size'        => '',
				'price_font_line_height' => '',
				'price_font_align'       => '',
				'price_font_transform'   => '',
				'price_disable'          => '',
				'price_padding_css'      => '',

				/* Properties */
				'prop_font_preset'       => '',
				'prop_font_color'        => '',
				'prop_font_size'         => '',
				'prop_font_line_height'  => '',
				'prop_font_align'        => '',
				'prop_font_transform'    => '',
				'prop_padding_css'       => '',

				/* Button */
				'mpc_button__disable'                     => '',

				'mpc_button__font_preset'                 => '',
				'mpc_button__font_color'                  => '',
				'mpc_button__font_size'                   => '',
				'mpc_button__font_line_height'            => '',
				'mpc_button__font_align'                  => '',
				'mpc_button__font_transform'              => '',

				'mpc_button__padding_css'                 => '',
				'mpc_button__margin_css'                  => '',
				'mpc_button__border_css'                  => '',

				'mpc_button__icon_type'                   => 'icon',
				'mpc_button__icon'                        => '',
				'mpc_button__icon_character'              => '',
				'mpc_button__icon_image'                  => '',
				'mpc_button__icon_image_size'             => 'thumbnail',
				'mpc_button__icon_preset'                 => '',
				'mpc_button__icon_color'                  => '#333333',
				'mpc_button__icon_size'                   => '',
				'mpc_button__icon_effect'                 => 'none-none',
				'mpc_button__icon_gap'                    => '',

				'mpc_button__background_type'             => 'color',
				'mpc_button__background_color'            => '',
				'mpc_button__background_image'            => '',
				'mpc_button__background_image_size'       => 'large',
				'mpc_button__background_repeat'           => 'no-repeat',
				'mpc_button__background_size'             => 'initial',
				'mpc_button__background_position'         => 'middle-center',
				'mpc_button__background_gradient'         => '#83bae3||#80e0d4||0;100||180||linear',

				'mpc_button__hover_background_effect'     => 'fade-in',

				'mpc_button__hover_border_css'            => '',

				'mpc_button__hover_font_color'            => '',
				'mpc_button__hover_icon_color'            => '',

				'mpc_button__hover_background_type'       => 'color',
				'mpc_button__hover_background_color'      => '',
				'mpc_button__hover_background_image'      => '',
				'mpc_button__hover_background_image_size' => 'large',
				'mpc_button__hover_background_repeat'     => 'no-repeat',
				'mpc_button__hover_background_size'       => 'initial',
				'mpc_button__hover_background_position'   => 'middle-center',
				'mpc_button__hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				/* General */
				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'margin_css'            => '',
				'border_css'            => '',

				/* Animation */
				'animation_in_type'      => 'none',
				'animation_in_duration'  => '300',
				'animation_in_delay'     => '0',
				'animation_in_offset'    => '100',

				'mpc_navigation__preset' => '',
			), $atts );

			/* Setup Globals */
			$MPC_Shortcode[ 'pricing' ] = array(
				'presets' => array(
					'title' => esc_attr( $atts[ 'title_font_preset' ] ),
					'price' => esc_attr( $atts[ 'price_font_preset' ] ),
					'prop'  => esc_attr( $atts[ 'prop_font_preset' ] ),
				),
				'disable' => array(
					'title'  => $atts[ 'title_disable' ],
					'price'  => $atts[ 'price_disable' ],
					'button' => $atts[ 'mpc_button__disable' ],
				),
				'parts' => array(
					'legend'  => '',
					'columns' => '',
					'button'  => MPC_Parser::shortcode( $atts, 'mpc_button_' ),
				),
				'style' => '',
			);

			/* Parse content */
			do_shortcode( $content );

			/* Prepare */
			$styles   = $this->shortcode_styles( $atts, $MPC_Shortcode[ 'pricing' ][ 'style' ] );
			$css_id   = $styles[ 'id' ];
			$animation = MPC_Parser::animation( $atts );

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init'; // mpc-transition
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'slider_disable' ] != 'true' ? ' mpc-init--slick' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$columns = $atts[ 'cols' ] != '' ? ' data-pb-cols="' . esc_attr( $atts[ 'cols' ] ) . '"' : '';

			$atts[ 'cols' ] = $MPC_Shortcode[ 'pricing' ][ 'parts' ][ 'legend' ] != '' ? (int) $atts[ 'cols' ] - 1 : $atts[ 'cols']; // reduce number of columns for slick if Legend is set
			$carousel_atts  = $atts[ 'slider_disable' ] != 'true' ? MPC_Parser::carousel( $atts, '', array( 'edgeFriction' => 0, 'loop' => 'false' ) ) : '';

			/* Shortcode Output */
			$return = '<div class="mpc-carousel__wrapper mpc-carousel-pricing mpc-waypoint">';
				$carousel = '<div id="' . $css_id . '" class="mpc-pricing-box' . $classes . '" ' . $columns . $animation . '>';
					if( $MPC_Shortcode[ 'pricing' ][ 'parts' ][ 'legend' ] != '' ) {
						$carousel .= $MPC_Shortcode[ 'pricing' ][ 'parts' ][ 'legend' ];
					}

					if( $atts[ 'slider_disable' ] != 'true' ) {
						$carousel .= '<div class="mpc-pricing-box__wrapper" ' . $carousel_atts . '>' . $MPC_Shortcode[ 'pricing' ][ 'parts' ][ 'columns' ] . '</div>';
					} else {
						$carousel .= $MPC_Shortcode[ 'pricing' ][ 'parts' ][ 'columns' ];
					}
				$carousel .= '</div>';

			if( $atts[ 'slider_disable' ] != 'true' ) {
				$return .= $MPC_Navigation->shortcode_template( $atts[ 'mpc_navigation__preset' ], '', $css_id, 'image', $carousel );
			} else {
				$return .= $carousel;
			}

			$return .= '</div>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return = '<div class="mpc-frontend-notice">';
					$return .= '<h4>' . __( 'Pricing Box', 'mpc' ) . '</h4>';
					$return .= __( 'Unfortunately this shortcode isn\'t available in <em>Frontend Editor</em> at the moment. This feature will be added in the upcoming updates. We are sorry for any inconvenience :)', 'mpc' );
				$return .= '</div>';
			}

			unset( $MPC_Shortcode[ 'pricing' ] ); // clean up

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles, $child_styles = '' ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc-pricing-box-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'title_font_size' ] = $styles[ 'title_font_size' ] != '' ? $styles[ 'title_font_size' ] . ( is_numeric( $styles[ 'title_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'price_font_size' ] = $styles[ 'price_font_size' ] != '' ? $styles[ 'price_font_size' ] . ( is_numeric( $styles[ 'price_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'prop_font_size' ]  = $styles[ 'prop_font_size' ] != '' ? $styles[ 'prop_font_size' ] . ( is_numeric( $styles[ 'prop_font_size' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-pricing-box[id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::background( $styles ) ) {
				$style .= '.mpc-pricing-box[id="' . $css_id . '"] .mpc-pricing-legend {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Title
			$inner_styles = array();
			if ( $styles[ 'title_padding_css' ] ) { $inner_styles[] = $styles[ 'title_padding_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'title' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-pricing-box[id="' . $css_id . '"] .mpc-pricing__title {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Price
			$inner_styles = array();
			if ( $styles[ 'price_padding_css' ] ) { $inner_styles[] = $styles[ 'price_padding_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'price' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-pricing-box[id="' . $css_id . '"] .mpc-pricing__price {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Properties
			if ( $temp_style = MPC_CSS::font( $styles, 'prop' ) ) {
				$style .= '.mpc-pricing-box[id="' . $css_id . '"] .mpc-pricing__properties {';
					$style .= $temp_style;
				$style .= '}';
			}

			if ( $styles[ 'prop_padding_css' ] ) {
				$style .= '.mpc-pricing-box[id="' . $css_id . '"] .mpc-pricing__property {';
					$style .= $styles[ 'prop_padding_css' ];
				$style .= '}';
			}

			$style .= $child_styles;
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
					'heading'          => __( 'Columns Number', 'mpc' ),
					'param_name'       => 'cols',
					'admin_label'      => true,
					'tooltip'          => __( 'Select number of displayed columns.', 'mpc' ),
					'value'            => array(
						'1' => 1,
						'2' => 2,
						'3' => 3,
						'4' => 4,
						'5' => 5,
						'6' => 6,
					),
					'std'              => '4',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				)
			);

			/* Section Title */
			$title_enable = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Titles', 'mpc' ),
					'param_name'       => 'title_disable',
					'tooltip'          => __( 'Check to disable titles in pricing columns.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Elements', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			/* Section Price */
			$price_enable = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Prices', 'mpc' ),
					'param_name'       => 'price_disable',
					'tooltip'          => __( 'Check to disable prices in pricing columns.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Elements', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field mpc-no-wrap',
				),
			);

			/* Section Slider */
			$slider = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Slider', 'mpc' ),
					'param_name'       => 'slider_disable',
					'tooltip'          => __( 'Check to disable slider.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Slider', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Single Scroll', 'mpc' ),
					'param_name'       => 'single_scroll',
					'tooltip'          => __( 'Check to enable single item scroll. Navigating through pricing box will jump by only one item at a time. Leave unchecked to scroll by all visible items.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Slider', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-first-row',
					'dependency'       => array( 'element' => 'slider_disable', 'value_not_equal_to' => 'true' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Slide Show', 'mpc' ),
					'param_name'       => 'auto_slide',
					'tooltip'          => __( 'Check to enable slide show. Pricing box will auto slide once the slide show delay pass.', 'mpc' ),
					'value'            => array( __( 'Yes', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-first-row',
					'group'            => __( 'Slider', 'mpc' ),
					'dependency'       => array( 'element' => 'slider_disable', 'value_not_equal_to' => 'true' ),
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Slide Show Delay', 'mpc' ),
					'param_name'       => 'delay',
					'tooltip'          => __( 'Specify delay between slides.', 'mpc' ),
					'min'              => 500,
					'max'              => 15000,
					'step'             => 50,
					'value'            => 1000,
					'unit'             => 'ms',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'auto_slide', 'value' => 'true', ),
					'group'            => __( 'Slider', 'mpc' ),
				),
			);

			/* Integrate Elements */
			$title_atts    = array( 'prefix' => 'title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Elements', 'mpc' ), 'dependency' => array( 'element' => 'title_disable', 'value_not_equal_to' => 'true' ) );
			$title_font    = MPC_Snippets::vc_font( $title_atts );
			$title_padding = MPC_Snippets::vc_padding( $title_atts );

			$price_atts    = array( 'prefix' => 'price', 'subtitle' => __( 'Price', 'mpc' ), 'group' => __( 'Elements', 'mpc' ), 'dependency' => array( 'element' => 'price_disable', 'value_not_equal_to' => 'true' ) );
			$price_font    = MPC_Snippets::vc_font( $price_atts );
			$price_padding = MPC_Snippets::vc_padding( $price_atts );

			$props_atts    = array( 'prefix' => 'prop', 'subtitle' => __( 'Properties', 'mpc' ), 'group' => __( 'Elements', 'mpc' ) );
			$props_font    = MPC_Snippets::vc_font( $props_atts );
			$props_padding = MPC_Snippets::vc_padding( $props_atts );

			/* Button */
			$button_exclude   = array( 'exclude_regex' => '/animation_(.*)|mpc_tooltip_(.*)|block|url|title/' );
			$integrate_button = vc_map_integrate_shortcode( 'mpc_button', 'mpc_button__', __( 'Button', 'mpc' ), $button_exclude );
			$disable_button = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Button', 'mpc' ),
					'param_name'       => 'mpc_button__disable',
					'tooltip'          => __( 'Check to disable buttons in pricing columns.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Button', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_button = array_merge( $disable_button, $integrate_button );

			/* END Integrate Elements */

			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$margin     = MPC_Snippets::vc_margin();
			$animation  = MPC_Snippets::vc_animation_basic();
			$class      = MPC_Snippets::vc_class();

			$integrate_navigation = vc_map_integrate_shortcode( 'mpc_navigation', 'mpc_navigation__', __( 'Navigation', 'mpc' ) );

			$params = array_merge(
				$base,

				$title_enable,
				$title_font,
				$title_padding,
				$price_enable,
				$price_font,
				$price_padding,
				$props_font,
				$props_padding,

				$integrate_button,

				$slider,

				$border,
				$background,
				$margin,
				$animation,

				$integrate_navigation,
				$class
			);

			return array(
				'name'                    => __( 'Pricing Box', 'mpc' ),
				'description'             => __( 'Pricing tables with legend', 'mpc' ),
				'base'                    => 'mpc_pricing_box',
				'is_container'            => true,
				'as_parent'               => array( 'only' => 'mpc_pricing_column,mpc_pricing_legend' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
//				'icon'                    => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-pricing-box.png',
				'icon'                    => 'mpc-shicon-pricing-box',
				'category'                => __( 'Massive', 'mpc' ),
				'params'                  => $params,
				'js_view'                 => 'VcColumnView',
			);
		}
	}
}

if ( class_exists( 'MPCShortCodeContainer_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_pricing_box' ) ) {
	class WPBakeryShortCode_mpc_pricing_box extends MPCShortCodeContainer_Base {}
}

if ( class_exists( 'MPC_Pricing_Box' ) ) {
	global $MPC_Pricing_Box;
	$MPC_Pricing_Box = new MPC_Pricing_Box;
}

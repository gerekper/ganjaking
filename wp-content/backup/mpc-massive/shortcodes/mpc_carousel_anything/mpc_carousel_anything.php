<?php
/*----------------------------------------------------------------------------*\
	CAROUSEL ANYTHING SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Carousel_Anything' ) ) {
	class MPC_Carousel_Anything {
		public $shortcode = 'mpc_carousel_anything';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_carousel_anything', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_carousel_anything-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_carousel_anything/css/mpc_carousel_anything.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_carousel_anything-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_carousel_anything/js/mpc_carousel_anything' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			/* Enqueues */
			wp_enqueue_style( 'mpc-massive-slick-css', mpc_get_plugin_path( __FILE__ ) . '/assets/css/libs/slick.min.css' );
			wp_enqueue_script( 'mpc-massive-slick-js', mpc_get_plugin_path( __FILE__ ) . '/assets/js/libs/slick.min.js', array( 'jquery' ), '', true );

			global $MPC_Navigation, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                           => '',
				'preset'                          => '',
				'single_scroll'                   => '',
				'rows'                            => '1',
				'cols'                            => '2',
				'gap'                             => '0',
				'loop'                            => '',
				'auto_slide'                      => '',
				'delay'                           => '1000',
				'stretched'                       => '',
				'start_at'                        => 1,

				'item_odd_background_type'        => 'color',
				'item_odd_background_color'       => '',
				'item_odd_background_repeat'      => 'no-repeat',
				'item_odd_background_size'        => 'initial',
				'item_odd_background_position'    => 'middle-center',
				'item_odd_background_gradient'    => '#83bae3||#80e0d4||0;100||180||linear',
				'item_odd_background_image'       => '',
				'item_odd_background_image_size'  => 'large',

				'item_even_background_type'       => 'color',
				'item_even_background_color'      => '',
				'item_even_background_repeat'     => 'no-repeat',
				'item_even_background_size'       => 'initial',
				'item_even_background_position'   => 'middle-center',
				'item_even_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'item_even_background_image'      => '',
				'item_even_background_image_size' => 'large',

				'item_border_css'                 => '',

				'animation_in_type'               => 'none',
				'animation_in_duration'           => '300',
				'animation_in_delay'              => '0',
				'animation_in_offset'             => '100',

				'mpc_navigation__preset'          => '',
			), $atts );

			/* Prepare */
			$styles    = $this->shortcode_styles( $atts );
			$css_id    = $styles[ 'id' ];
			$carousel  = MPC_Parser::carousel( $atts );
			$animation = MPC_Parser::animation( $atts );

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'stretched' ] != '' ? ' mpc-carousel--stretched' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			/* Shortcode Output */
			$return = '<div class="mpc-carousel__wrapper mpc-waypoint">';
				$carousel = '<div id="' . $css_id . '" class="mpc-carousel-anything' . $classes . '" ' . $animation . $carousel . '>';
					$carousel .= do_shortcode( $content );
				$carousel .= '</div>';
			$return .= $MPC_Navigation->shortcode_template( $atts[ 'mpc_navigation__preset' ], '', $css_id, 'image', $carousel );
			$return .= '</div>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return = '<div class="mpc-frontend-notice">';
					$return .= '<h4>' . __( 'Carousel Anything', 'mpc' ) . '</h4>';
					$return .= __( 'Unfortunately this shortcode isn\'t available in <em>Frontend Editor</em> at the moment. This feature will be added in the upcoming updates. We are sorry for any inconvenience :)', 'mpc' );
				$return .= '</div>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_carousel_anything-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'gap' ] = $styles[ 'gap' ] != '' ? $styles[ 'gap' ] . ( is_numeric( $styles[ 'gap' ] ) ? 'px' : '' ) : '';

			// Gap
			if ( $styles[ 'gap' ] && $styles[ 'gap' ] != '0px' ) {
				$style .= '.mpc-carousel-anything[id="' . $css_id . '"] .slick-track {';
					$style .=  'margin-left: -' . $styles[ 'gap' ] . ';';
				$style .= '}';

				$style .= '.mpc-carousel-anything[id="' . $css_id . '"] .mpc-carousel__item-wrapper {';
					$style .=  'padding-left: ' . $styles[ 'gap' ] . ';';     // horizontal
					$style .=  'margin-bottom: ' . $styles[ 'gap' ] . ';';     // vertical
				$style .= '}';
			}

			// Item
			if ( $temp_style = MPC_CSS::background( $styles, 'item_odd' ) ) {
				$style .= '.mpc-carousel-anything[id="' . $css_id . '"] .mpc-carousel__item-wrapper.slick-slide:nth-child(2n+1) {';
					$style .= $temp_style;
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::background( $styles, 'item_even' ) ) {
				$style .= '.mpc-carousel-anything[id="' . $css_id . '"] .mpc-carousel__item-wrapper.slick-slide:nth-child(2n) {';
					$style .= $temp_style;
				$style .= '}';
			}

			if ( $styles[ 'item_border_css' ] ) {
				$style .= '.mpc-carousel-anything[id="' . $css_id . '"] .mpc-carousel__item-wrapper {';
					$style .= $styles[ 'item_border_css' ];
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
					'type'             => 'checkbox',
					'heading'          => __( 'Single Scroll', 'mpc' ),
					'param_name'       => 'single_scroll',
					'tooltip'          => __( 'Check to enable single item scroll. Navigating through carousel will jump by only one item at a time. Leave unchecked to scroll by all visible items.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Loop', 'mpc' ),
					'param_name'       => 'loop',
					'tooltip'          => __( 'Check to enable loop. Enabling loop will change the carousel to infinite scroll.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-first-row',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Slide Show', 'mpc' ),
					'param_name'       => 'auto_slide',
					'tooltip'          => __( 'Check to enable slide show. Carousel will auto slide once the slide show delay pass.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-first-row',
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
					'dependency'       => array( 'element' => 'auto_slide', 'value' => 'true', ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
			);

			$base_ext = array(
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Gap', 'mpc' ),
					'param_name'       => 'gap',
					'tooltip'          => __( 'Choose gap between slides.', 'mpc' ),
					'min'              => 0,
					'max'              => 50,
					'step'             => 1,
					'value'            => 0,
					'unit'             => 'px',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Stretch', 'mpc' ),
					'param_name'       => 'stretched',
					'tooltip'          => __( 'Check to enable slider stretch. Enabling stretch will display parts of previous and next items on carousel sides.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Start At', 'mpc' ),
					'param_name'       => 'start_at',
					'tooltip'          => __( 'Define first displayed slide index.', 'mpc' ),
					'value'            => '',
					'std'              => 1,
					'label'            => '',
					'validate'         => true,
					'addon'            => array(
						'icon'  => 'dashicons-images-alt',
						'align' => 'prepend'
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
			);

			$item_border          = MPC_Snippets::vc_border( array( 'prefix' => 'item', 'subtitle' => __( 'Item', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );
			$item_even_background = MPC_Snippets::vc_background( array( 'prefix' => 'item_even', 'subtitle' => __( 'Even Item', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );
			$item_odd_background  = MPC_Snippets::vc_background( array( 'prefix' => 'item_odd', 'subtitle' => __( 'Odd Item', 'mpc' ), 'group' => __( 'Item', 'mpc' ) ) );

			$rows_cols = MPC_Snippets::vc_rows_cols( array( 'cols' => array( 'max' => 16 ) ) );
			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			$integrate_navigation = vc_map_integrate_shortcode( 'mpc_navigation', 'mpc_navigation__', __( 'Navigation', 'mpc' ) );

			$params = array_merge(
				$base,

				$rows_cols,
				$base_ext,

				$item_border,
				$item_odd_background,
				$item_even_background,

				$animation,

				$integrate_navigation,

				$class
			);

			return array(
				'name'                    => __( 'Carousel Anything', 'mpc' ),
				'description'             => __( 'Carousel with shortcodes', 'mpc' ),
				'base'                    => 'mpc_carousel_anything',
				'is_container'            => true,
				'as_parent'               => array( 'only' => 'mpc_image,mpc_alert,mpc_button,mpc_cubebox,mpc_flipbox,mpc_icon,mpc_ihover,mpc_map,mpc_quote,vc_single_image,vc_video,vc_column_text,mpc_callout,mpc_chart,mpc_counter,mpc_icon_column' ),
				'content_element'         => true,
				'show_settings_on_create' => true,
//				'icon'                    => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-carousel-anything.png',
				'icon'                    => 'mpc-shicon-car-anything',
				'category'                => __( 'Massive', 'mpc' ),
				'params'                  => $params,
				'js_view'                 => 'VcColumnView',
			);
		}
	}
}

if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_mpc_carousel_anything' ) ) {
	class WPBakeryShortCode_mpc_carousel_anything extends WPBakeryShortCodesContainer {}
}

if ( class_exists( 'MPC_Carousel_Anything' ) ) {
	global $MPC_Carousel_Anything;
	$MPC_Carousel_Anything = new MPC_Carousel_Anything;
}

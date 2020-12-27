<?php
/*----------------------------------------------------------------------------*\
	CAROUSEL IMAGE SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Carousel_Image' ) ) {
	class MPC_Carousel_Image {
		public $shortcode = 'mpc_carousel_image';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_carousel_image', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_carousel_image-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_carousel_image/css/mpc_carousel_image.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_carousel_image-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_carousel_image/js/mpc_carousel_image' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
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
				'class'       => '',
				'preset'      => '',
				'padding_css' => '',
				'margin_css'  => '',
				'border_css'  => '',

				'layout'              => 'classic',
				'stretched'           => '',
				'start_at'            => 1,
				'effect'              => 'none',
				'effect_reverse'      => '',
				'single_scroll'       => '',
				'rows'                => '2',
				'cols'                => '4',
				'gap'                 => '0',
				'loop'                => '',
				'auto_slide'          => '',
				'delay'               => '1000',
				'speed'               => '500',
				'slider_effect'       => 'slide',
				'images'              => '',
				'images_links'        => '',
				'images_links_target' => '',

				'image_odd_background_type'       => 'color',
				'image_odd_background_color'      => '',
				'image_odd_background_repeat'     => 'no-repeat',
				'image_odd_background_size'       => 'initial',
				'image_odd_background_position'   => 'middle-center',
				'image_odd_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'image_odd_background_image'      => '',
				'image_odd_background_image_size' => 'large',

				'image_even_background_type'       => 'color',
				'image_even_background_color'      => '',
				'image_even_background_repeat'     => 'no-repeat',
				'image_even_background_size'       => 'initial',
				'image_even_background_position'   => 'middle-center',
				'image_even_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'image_even_background_image'      => '',
				'image_even_background_image_size' => 'large',

				'image_hover_background_type'       => 'color',
				'image_hover_background_color'      => '',
				'image_hover_background_repeat'     => 'no-repeat',
				'image_hover_background_size'       => 'initial',
				'image_hover_background_position'   => 'middle-center',
				'image_hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'image_hover_background_image'      => '',
				'image_hover_background_image_size' => 'large',

				'image_size'          => 'medium',
				'enable_force_height' => '',
				'force_height'        => '300',
				'image_opacity'       => '100',
				'image_hover_opacity' => '100',

				'image_border_css'             => '',
				'image_hover_border_css'       => '',
				'image_inner_border_css'       => '',
				'image_hover_inner_border_css' => '',

				'overlay_enable_lightbox' => '',
				'overlay_enable_url'      => '',
				'overlay_padding_css'     => '',
				'overlay_overlay_effect'  => 'fade',
				'overlay_background'      => '',
				'overlay_icon_align'      => 'middle-center',

				'overlay_icon_background'  => '',
				'overlay_icon_margin_css'  => '',
				'overlay_icon_padding_css' => '',
				'overlay_icon_border_css'  => '',

				'overlay_icon_type'       => 'icon',
				'overlay_icon'            => '',
				'overlay_icon_preset'     => '',
				'overlay_icon_character'  => '',
				'overlay_icon_color'      => '#333333',
				'overlay_icon_size'       => '',
				'overlay_icon_image'      => '',
				'overlay_icon_image_size' => 'thumbnail',
				'overlay_icon_mirror'     => '',

				'overlay_url_icon_type'       => 'icon',
				'overlay_url_icon'            => '',
				'overlay_url_icon_preset'     => '',
				'overlay_url_icon_character'  => '',
				'overlay_url_icon_color'      => '#333333',
				'overlay_url_icon_size'       => '',
				'overlay_url_icon_image'      => '',
				'overlay_url_icon_image_size' => 'thumbnail',
				'overlay_url_icon_mirror'     => '',

				'overlay_hover_color'           => '',
				'overlay_hover_border'          => '',
				'overlay_hover_icon_background' => '',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',

				'mpc_navigation__preset' => '',
			), $atts );

			/* Validate data */
			if( empty( $atts[ 'images' ] ) )
				return '';

			/* Prepare */
			$styles    = $this->shortcode_styles( $atts );
			$css_id    = $styles[ 'id' ];
			$animation = MPC_Parser::animation( $atts );
			$carousel  = MPC_Parser::carousel( $atts );

			$images       = explode( ',', $atts[ 'images' ] );
			$images_links = $atts[ 'images_links' ] != '' ? explode( ',', $atts[ 'images_links' ] ) : '';
			$image_size   = $atts[ 'image_size' ] != '' ? $atts[ 'image_size' ] : 'medium';
			$image_target = $atts[ 'images_links_target'] != '' ? '_blank' : '';
			$index        = 0;

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'layout' ] != '' ? ' mpc-layout--' . $atts[ 'layout' ] : '';
			$classes .= $atts[ 'effect' ] != '' ? ' mpc-effect--' . $atts[ 'effect' ] : '';
			$classes .= $atts[ 'effect_reverse' ] != '' ? ' mpc-effect--reverse' : '';
			$classes .= $atts[ 'stretched' ] != '' ? ' mpc-carousel--stretched' : '';
			$classes .= $atts[ 'enable_force_height' ] != '' ? ' mpc-force-height' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$data_atts = $animation . $carousel;
			$classes_item = 'mpc-carousel__item-wrapper';

			/* Lightbox & Overlay */
			$overlay_begin = $overlay_end = '';
			if ( $atts[ 'overlay_enable_lightbox' ] != '' || $atts[ 'overlay_enable_url' ] != '' ? true : false ) {
				$overlay_begin = '<div class="mpc-item-overlay mpc-transition"><div class="mpc-overlay--vertical-wrap"><div class="mpc-overlay--vertical">';
			    $overlay_end   = '</div></div></div>';

				$data_atts .= $atts[ 'overlay_icon_align' ] != '' ? ' data-align="' . $atts[ 'overlay_icon_align' ] . '"' : '';
				$classes .= $atts[ 'overlay_overlay_effect' ] != '' ? ' mpc-overlay--' . $atts[ 'overlay_overlay_effect' ] : ' mpc-overlay--fade';
			}

			$overlay_icon = $overlay_atts = $lightbox = '';
			if ( $atts[ 'overlay_enable_lightbox' ] != '' ) {
				$lightbox  = MPC_Helper::lightbox_vendor();

				$overlay_atts = ' rel="mpc[' . $css_id . ']"';
				$classes_icon = 'mpc-item-overlay__icon mpc-type--lightbox';
				$classes_icon .= $atts[ 'overlay_icon_mirror' ] != '' ? ' mpc-icon--mirror' : '';

				$overlay_icon = MPC_Parser::icon( $atts, 'overlay' );
				$overlay_icon[ 'class' ] .= $atts[ 'overlay_icon_type' ] != '' ? ' mpc-icon--' . $atts[ 'overlay_icon_type' ] : '';

				$overlay_icon = '<i class="' . $classes_icon . $overlay_icon[ 'class' ] . '">' . $overlay_icon[ 'content' ] . '</i>';
			}

			$url_icon = '';
			if ( $atts[ 'overlay_enable_url' ] != '' ) {
				$classes_icon = 'mpc-item-overlay__icon mpc-type--external';
				$classes_icon .= $atts[ 'overlay_url_icon_mirror' ] != '' ? ' mpc-icon--mirror' : '';

				$url_icon = MPC_Parser::icon( $atts, 'overlay_url' );
				$url_icon[ 'class' ] .= $atts[ 'overlay_url_icon_type' ] != '' ? ' mpc-icon--' . $atts[ 'overlay_url_icon_type' ] : '';

				$url_icon = '<i class="' . $classes_icon  . $url_icon[ 'class' ] . '">' . $url_icon[ 'content' ] . '</i>';
			}

			/* Shortcode Output */
			$return = '<div class="mpc-carousel__wrapper mpc-waypoint">';
				$carousel = '<div id="' . $css_id . '" class="mpc-carousel-image' . $classes . '" ' . $data_atts . '>';

				foreach( $images as $image_id ) {
					$image = wpb_getImageBySize( array(
							'attach_id'  => $image_id,
							'thumb_size' => $image_size,
							'class'      => 'mpc-transition',
					) );

					if( !$image ) {
						$index++;
						continue;
					}

					$overlay = '';
					if( $atts[ 'overlay_enable_url' ] != '' ) {
						$url_link = isset( $images_links[ $index ] ) && $images_links[ $index ] != '' ? esc_url( $images_links[ $index ] ) : '';
						$overlay .= '<a href="' . $url_link . '" class="mpc-icon-anchor">' . $url_icon . '</a>';
					}

					if( $atts[ 'overlay_enable_lightbox' ] != '' ) {
						$full_image = wp_get_attachment_image_src( $image_id, 'full' );
						$image_link = is_array( $full_image ) ? $full_image[ 0 ] : '';
						$overlay    .= '<a href="' . $image_link . '"' . $overlay_atts . ' class="mpc-icon-anchor' . $lightbox . '">' . $overlay_icon . '</a>';
					}

					$overlay = $overlay != '' ? $overlay_begin . $overlay . $overlay_end : '';
					$wrapper_link = $overlay == '' && isset( $images_links[ $index ] ) && $images_links[ $index ] != '' ? true : false;

					$wrapper     = $wrapper_link != '' ? 'a href="' . esc_url( $images_links[ $index ] ) . '"' : 'div';
					$wrapper_end = $wrapper_link != '' ? 'a' : 'div';
					$border 	 = '<div class="mpc-border"></div>';

					$item = '<' . $wrapper  . ' target="' . $image_target . '"class="' . $classes_item . '" data-height="' . esc_attr( $image[ 'p_img_large' ][ 2 ] ) . '">';
						$item_content = '<div onclick="" class="mpc-item mpc-transition">' . $border . $image[ 'thumbnail' ]  . $overlay . '</div>';
						$item .= apply_filters( 'ma/carousel_image/item', $item_content, $image_id, $index, count( $images ) );
					$item .= '</' . $wrapper_end . '>';

					$carousel .= $item;
					$index++;
				}

				$carousel .= '</div>';
			$return .= $MPC_Navigation->shortcode_template( $atts[ 'mpc_navigation__preset' ], '', $css_id, 'image', $carousel );
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
			$css_id = uniqid( 'mpc_carousel_image-' . rand( 1, 100 ) );
			$style  = '';

			// Add 'px'
			$styles[ 'gap' ] = $styles[ 'gap' ] != '' ? $styles[ 'gap' ] . ( is_numeric( $styles[ 'gap' ] ) ? 'px' : '' ) : '';
			$styles[ 'force_height' ] = $styles[ 'force_height' ] != '' ? $styles[ 'force_height' ] . ( is_numeric( $styles[ 'force_height' ] ) ? 'px' : '' ) : '';
			$styles[ 'overlay_icon_size' ] = $styles[ 'overlay_icon_size' ] != '' ? $styles[ 'overlay_icon_size' ] . ( is_numeric( $styles[ 'overlay_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'overlay_url_icon_size' ] = $styles[ 'overlay_url_icon_size' ] != '' ? $styles[ 'overlay_url_icon_size' ] . ( is_numeric( $styles[ 'overlay_url_icon_size' ] ) ? 'px' : '' ) : '';

			// Gap
			if ( $styles[ 'gap' ] && $styles[ 'gap' ] != '0px' ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .slick-track {';
					$style .=  'margin-left: -' . $styles[ 'gap' ] . ';';
				$style .= '}';

				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-carousel__item-wrapper {';
					$style .=  'padding-left: ' . $styles[ 'gap' ] . ';';
					$style .=  'margin-bottom: ' . $styles[ 'gap' ] . ';';
				$style .= '}';

				$style .= '.mpc-carousel-image[id="' . $css_id . '"]:not(.slick-slider) .mpc-carousel__item-wrapper {';
					$style .=  'padding: 0 ' . (int) $styles[ 'gap' ] * 0.5 . 'px;';
				$style .= '}';
			}

			// Force Height
			if ( $styles[ 'enable_force_height' ] ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] {';
					$style .= 'height: ' . $styles[ 'force_height' ] . ';';
				$style .= '}';
			}

			// Regular
			if ( $temp_style = MPC_CSS::background( $styles, 'image_odd' ) ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-carousel__item-wrapper.slick-slide:nth-child(2n+1) .mpc-item,';
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .slick-slide:not(.mpc-carousel__item-wrapper):nth-child(2n) > div:nth-child(2n+1) .mpc-item,';
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .slick-slide:not(.mpc-carousel__item-wrapper):nth-child(2n+1) > div:nth-child(2n) .mpc-item {';
					$style .= $temp_style;
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::background( $styles, 'image_even' ) ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-carousel__item-wrapper.slick-slide:nth-child(2n) .mpc-item,';
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .slick-slide:not(.mpc-carousel__item-wrapper):nth-child(2n+1) > div:nth-child(2n+1) .mpc-item,';
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .slick-slide:not(.mpc-carousel__item-wrapper):nth-child(2n) > div:nth-child(2n) .mpc-item {';
					$style .= $temp_style;
				$style .= '}';
			}

			if ( $styles[ 'image_opacity' ] ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-item {';
					$style .= 'opacity: ' . ( $styles[ 'image_opacity' ] / 100 ) . ';filter: alpha( opacity = ' . $styles[ 'image_opacity' ] . ' );';
				$style .= '}';
			}

			if ( $styles[ 'image_border_css' ] ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-border {';
					$style .= $styles[ 'image_border_css' ];
				$style .= '}';
			}

			if ( $styles[ 'image_inner_border_css' ] ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-border:before {';
					$style .= $styles[ 'image_inner_border_css' ];
				$style .= '}';
			}

			// Hover
			if ( $temp_style = MPC_CSS::background( $styles, 'image_hover' ) ) {
				$style .= '#' . $css_id . ' .mpc-item:hover {';
					$style .= $temp_style;
				$style .= '}';
			}

			if ( $styles[ 'image_hover_opacity' ] ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-item:hover {';
					$style .= 'opacity: ' . ( $styles[ 'image_hover_opacity' ] / 100 ) . ';filter: alpha( opacity = ' . $styles[ 'image_hover_opacity' ] . ' );';
				$style .= '}';
			}

			if ( $styles[ 'image_hover_border_css' ] ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-item:hover .mpc-border {';
					$style .= $styles[ 'image_hover_border_css' ];
				$style .= '}';
			}

			if ( $styles[ 'image_hover_inner_border_css' ] ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-item:hover .mpc-border:before {';
					$style .= $styles[ 'image_hover_inner_border_css' ];
				$style .= '}';
			}

			// Overlay & Lightbox
			if ( $styles[ 'overlay_background' ] ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-item-overlay {';
					$style .= 'background: ' . $styles[ 'overlay_background' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'overlay_padding_css' ] ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-overlay--vertical {';
					$style .= $styles[ 'overlay_padding_css' ];
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'overlay_icon_border_css' ] ) { $inner_styles[] = $styles[ 'overlay_icon_border_css' ]; }
			if ( $styles[ 'overlay_icon_padding_css' ] ) { $inner_styles[] = $styles[ 'overlay_icon_padding_css' ]; }
			if ( $styles[ 'overlay_icon_margin_css' ] ) { $inner_styles[] = $styles[ 'overlay_icon_margin_css' ]; }
			if ( $styles[ 'overlay_icon_background' ] ) { $inner_styles[] = 'background: ' . $styles[ 'overlay_icon_background' ] . ';'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-icon-anchor {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::icon( $styles, 'overlay_url' ) ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-type--external {';
					$style .= $temp_style;
				$style .= '}';
			}
			if ( $temp_style = MPC_CSS::icon( $styles, 'overlay' ) ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-type--lightbox {';
					$style .= $temp_style;
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'overlay_hover_border' ] ) { $inner_styles[] = 'border-color:' . $styles[ 'overlay_hover_border' ] . ';'; }
			if ( $styles[ 'overlay_hover_icon_background' ] ) { $inner_styles[] = 'background:' . $styles[ 'overlay_hover_icon_background' ]. ';'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-icon-anchor:hover {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}
			if ( $styles[ 'overlay_hover_color' ] ) {
				$style .= '.mpc-carousel-image[id="' . $css_id . '"] .mpc-icon-anchor:hover i {';
					$style .= 'color:' . $styles[ 'overlay_hover_color' ] . ';';
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
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Slide Show', 'mpc' ),
					'param_name'       => 'auto_slide',
					'tooltip'          => __( 'Check to enable slide show. Carousel will auto slide once the slide show delay pass.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
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
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Slider Speed', 'mpc' ),
					'param_name'       => 'speed',
					'tooltip'          => __( 'Specify slider speed.', 'mpc' ),
					'min'              => 0,
					'max'              => 5000,
					'step'             => 50,
					'value'            => 500,
					'unit'             => 'ms',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Image Layout', 'mpc' ),
					'param_name'       => 'layout',
					'tooltip'          => __( 'Select image layout:<br><b>Classic</b>: images are displayed in equal columns, they might have different heights;<br><b>Fluid</b>: images are displayed in line with equal heights, part of next/previous image might be visible on the sides.', 'mpc' ),
					'value'            => array(
						__( 'Classic', 'mpc' ) => 'classic',
						__( 'Fluid', 'mpc' )   => 'fluid',
					),
					'std'              => 'classic',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-clear--both mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Slider Effect', 'mpc' ),
					'param_name'       => 'slider_effect',
					'tooltip'          => __( 'Select the effect for slider transitions.', 'mpc' ),
					'value'            => array(
						__( 'Slide', 'mpc' ) => 'slide',
						__( 'Fade', 'mpc' )  => 'fade',
					),
					'std'              => 'slide',
					'dependency'       => array( 'element' => 'layout', 'value' => 'classic' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-clear--both mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Force Carousel Height', 'mpc' ),
					'param_name'       => 'enable_force_height',
					'tooltip'          => __( 'Check this to force exact height of this carousel.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'layout', 'value' => 'fluid' ),
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Forced Height', 'mpc' ),
					'param_name'       => 'force_height',
					'tooltip'          => __( 'Choose exact height for the carousel.', 'mpc' ),
					'min'              => 50,
					'max'              => 1000,
					'step'             => 10,
					'value'            => 300,
					'unit'             => 'px',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'dependency' => array( 'element' => 'enable_force_height', 'value' => 'true' ),
				),
				array(
					'type'        => 'attach_images',
					'heading'     => __( 'Images', 'mpc' ),
					'param_name'  => 'images',
					'tooltip'     => __( 'Choose images for this carousel.', 'mpc' ),
					'value'       => '',
					'admin_label' => true,
				),
				array(
					'type'        => 'exploded_textarea',
					'heading'     => __( 'Images Links', 'mpc' ),
					'param_name'  => 'images_links',
					'tooltip'     => __( 'Define custom links for carousel images. Each new line will be a separate link. Leave empty line to skip an image.', 'mpc' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Images Links Target ', 'mpc' ),
					'param_name'       => 'images_links_target',
					'tooltip'          => __( 'Choose whether the links should open in a new tab.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
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

			$image = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Images', 'mpc' ),
					'param_name'       => 'image_section_divider',
					'group'            => __( 'Images', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Size', 'mpc' ),
					'param_name'       => 'image_size',
					'tooltip'          => __( 'Define images size. You can use default WordPress sizes (<em>thumbnail</em>, <em>medium</em>, <em>large</em>, <em>full</em>) or pass exact size by width and height in this format: 100x200.', 'mpc' ),
					'value'            => 'medium',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-editor-expand',
						'align' => 'prepend',
					),
					'label'            => '',
					'validate'         => false,
					'group'            => __( 'Images', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field mpc-input--large',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Opacity', 'mpc' ),
					'param_name'       => 'image_opacity',
					'tooltip'          => __( 'Choose opacity for images.', 'mpc' ),
					'min'              => 0,
					'max'              => 100,
					'step'             => 1,
					'value'            => 100,
					'unit'             => '%',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'group'            => __( 'Images', 'mpc' ),
				),
			);

			$image_hover = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Image Hover', 'mpc' ),
					'param_name'       => 'image_hover_section_divider',
					'group'            => __( 'Images', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Effect', 'mpc' ),
					'param_name'       => 'effect',
					'tooltip'          => __( 'Select hover effect for carousel images.<br><br><b>Please notice that some of them will work only in modern browsers</b>.', 'mpc' ),
					'value'            => array(
						__( 'None', 'mpc' )       => 'none',
						__( 'Brightness', 'mpc' ) => 'brightness',
						__( 'Contrast', 'mpc' )   => 'contrast',
						__( 'Grey Scale', 'mpc' ) => 'grey-scale',
						__( 'Hue', 'mpc' )        => 'hue',
						__( 'Invert', 'mpc' )     => 'invert',
						__( 'Saturate', 'mpc' )   => 'saturate',
						__( 'Sepia', 'mpc' )      => 'sepia',
					),
					'std'              => 'none',
					'group'            => __( 'Images', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Reverse', 'mpc' ),
					'param_name'       => 'effect_reverse',
					'tooltip'          => __( 'Check to reverse the effect.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Images', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Opacity', 'mpc' ),
					'param_name'       => 'image_hover_opacity',
					'tooltip'          => __( 'If you want to change the image opacity after hover choose a different value from the slider below.', 'mpc' ),
					'min'              => 0,
					'max'              => 100,
					'step'             => 1,
					'value'            => 100,
					'unit'             => '%',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
					'group'            => __( 'Images', 'mpc' ),
				),
			);

			$overlay  = MPC_Snippets::vc_overlay();
			$rows_cols = MPC_Snippets::vc_rows_cols( array( 'cols' => array( 'min' => 1, 'max' => 12, 'default' => 4, 'dependency' => array( 'element' => 'layout', 'value' => 'classic' ) ), 'rows' =>  array( 'max' => 4, 'dependency' => array( 'element' => 'layout', 'value' => 'classic' ) ) ) );

			$image_atts = array( 'prefix' => 'image', 'subtitle' => __( 'Image', 'mpc' ), 'group' => __( 'Images', 'mpc' ));
			$image_hover_atts = array( 'prefix' => 'image_hover', 'subtitle' => __( 'Image Hover', 'mpc' ), 'group' => __( 'Images', 'mpc' ));

			$image_border          = MPC_Snippets::vc_border( $image_atts );
			$image_inner_border    = MPC_Snippets::vc_inner_border( $image_atts );
			$image_even_background = MPC_Snippets::vc_background( array( 'prefix' => 'image_even', 'subtitle' => __( 'Even Image', 'mpc' ), 'group' => __( 'Images', 'mpc' ) ) );
			$image_odd_background  = MPC_Snippets::vc_background( array( 'prefix' => 'image_odd', 'subtitle' => __( 'Odd Image', 'mpc' ), 'group' => __( 'Images', 'mpc' ) ) );

			$image_hover_border       = MPC_Snippets::vc_border( $image_hover_atts );
			$image_hover_inner_border = MPC_Snippets::vc_inner_border( $image_hover_atts );
			$image_hover_background   = MPC_Snippets::vc_background( $image_hover_atts );

			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			$integrate_navigation = vc_map_integrate_shortcode( 'mpc_navigation', 'mpc_navigation__', __( 'Navigation', 'mpc' ) );

			$params = array_merge(
				$base,
				$rows_cols,
				$base_ext,
				$image,
				$image_border,
				$image_inner_border,
				$image_even_background,
				$image_odd_background,
				$image_hover,
				$image_hover_border,
				$image_hover_inner_border,
				$image_hover_background,

				$overlay,

				$animation,
				$integrate_navigation,
				$class
			);

			return array(
				'name'        => __( 'Carousel Images', 'mpc' ),
				'description' => __( 'Carousel with images', 'mpc' ),
				'base'        => 'mpc_carousel_image',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-carousel-image.png',
				'icon'        => 'mpc-shicon-car-images',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}

if ( class_exists( 'MPC_Carousel_Image' ) ) {
	global $MPC_Carousel_Image;
	$MPC_Carousel_Image = new MPC_Carousel_Image;
}

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_mpc_carousel_image' ) ) {
	class WPBakeryShortCode_mpc_carousel_image extends WPBakeryShortCode {
		function __construct( $settings ) {
			parent::__construct( $settings );

			$this->shortcodeScripts();
		}

		public function shortcodeScripts() {
			wp_enqueue_script( 'vc_grid-js-imagesloaded', vc_asset_url( 'lib/bower/imagesloaded/imagesloaded.pkgd.min.js' ) );
		}

		public function singleParamHtmlHolder( $param, $value ) {
			$output = '';

			$param_name = isset( $param[ 'param_name' ] ) ? $param[ 'param_name' ] : '';
			$group      = isset( $param[ 'group' ] ) ? '[' . $param[ 'group' ] . '] ' : '';
			$heading    = isset( $param[ 'heading' ] ) ? $param[ 'heading' ] : '';
			$type       = isset( $param[ 'type' ] ) ? $param[ 'type' ] : '';
			$class      = isset( $param[ 'class' ] ) ? $param[ 'class' ] : '';

			if ( $param_name == 'images' ) {
				$images_ids = empty( $value ) ? array() : explode( ',', trim( $value ) );
				$output .= '<ul class="attachment-thumbnails' . ( empty( $images_ids ) ? ' image-exists' : '' ) . '" data-name="' . $param_name . '">';
				foreach ( $images_ids as $image ) {
					$img = wpb_getImageBySize( array( 'attach_id' => (int) $image, 'thumb_size' => 'thumbnail' ) );
					$output .= ( $img ? '<li>' . $img['thumbnail'] . '</li>' : '<li><img width="150" height="150" test="' . $image . '" src="' . vc_asset_url( 'vc/blank.gif' ) . '" class="attachment-thumbnail" alt="" title="" /></li>' );
				}
				$output .= '</ul>';
				$output .= '<a href="#" class="column_edit_trigger' . ( ! empty( $images_ids ) ? ' image-exists' : '' ) . '">' . __( 'Add images', 'js_composer' ) . '</a>';
				$output .= '<br>';
			} else if ( isset( $param['holder'] ) && $param['holder'] !== 'hidden' ) {
				$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
			} else if ( isset( $param['admin_label'] ) && $param['admin_label'] === true ) {
				$output .= '<span class="vc_admin_label admin_label_' . $param_name . ( empty( $value ) ? ' hidden-label' : '' ) . '"><label>' . $group . $heading . '</label>: ' . $value . '</span>';
			}

			return $output;
		}
	}
}

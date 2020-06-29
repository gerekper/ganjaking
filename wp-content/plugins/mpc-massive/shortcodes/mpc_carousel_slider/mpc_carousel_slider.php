<?php
/*----------------------------------------------------------------------------*\
	CAROUSEL IMAGE SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Carousel_Slider' ) ) {
	class MPC_Carousel_Slider {
		public $shortcode = 'mpc_carousel_slider';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_carousel_slider', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_carousel_slider-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_carousel_slider/css/mpc_carousel_slider.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_carousel_slider-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_carousel_slider/js/mpc_carousel_slider' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
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
				'class'                         => '',
				'preset'                        => '',
				'padding_css'                   => '',
				'margin_css'                    => '',
				'border_css'                    => '',

				'layout'                        => 'fluid',
				'stretched'                     => '',
				'start_at'                      => 1,
				'effect'                        => 'none',
				'effect_reverse'                => '',
				'gap'                           => '0',
				'height'                        => '400',
				'loop'                          => '',
				'auto_slide'                    => '',
				'delay'                         => '1000',
				'images'                        => '',
				'images_links'                  => '',
				'enable_lightbox'               => '',
				'lightbox_source'               => '',

				'counter_padding_css'           => '',
				'counter_margin_css'            => '',
				'counter_border_css'            => '',
				'counter_position'              => 'bottom-right',
				'font_preset'                   => '',
				'font_color'                    => '',
				'font_size'                     => '',
				'font_line_height'              => '',
				'font_align'                    => '',
				'font_transform'                => '',
				'second_font_size'              => '',
				'second_font_color'             => '',

				'background_type'               => 'color',
				'background_color'              => '',
				'background_image'              => '',
				'background_image_size'         => 'large',
				'background_repeat'             => 'no-repeat',
				'background_size'               => 'initial',
				'background_position'           => 'middle-center',
				'background_gradient'           => '#83bae3||#80e0d4||0;100||180||linear',

				'image_size'                    => 'medium',
				'image_opacity'                 => '100',
				'image_hover_opacity'           => '100',

				'image_border_css'              => '',
				'image_hover_border_css'        => '',
				'image_inner_border_css'        => '',
				'image_hover_inner_border_css'  => '',

				'overlay_enable_lightbox'       => '',
				'overlay_enable_url'            => '',
				'overlay_padding_css'           => '',
				'overlay_overlay_effect'        => 'fade',
				'overlay_background'            => '',
				'overlay_icon_align'            => 'middle-center',

				'overlay_icon_background'       => '',
				'overlay_icon_margin_css'       => '',
				'overlay_icon_padding_css'      => '',
				'overlay_icon_border_css'       => '',

				'overlay_icon_type'             => 'icon',
				'overlay_icon'                  => '',
				'overlay_icon_preset'           => '',
				'overlay_icon_character'        => '',
				'overlay_icon_color'            => '#333333',
				'overlay_icon_size'             => '',
				'overlay_icon_image'            => '',
				'overlay_icon_image_size'       => 'thumbnail',
				'overlay_icon_mirror'           => '',

				'overlay_url_icon_type'         => 'icon',
				'overlay_url_icon'              => '',
				'overlay_url_icon_preset'       => '',
				'overlay_url_icon_character'    => '',
				'overlay_url_icon_color'        => '#333333',
				'overlay_url_icon_size'         => '',
				'overlay_url_icon_image'        => '',
				'overlay_url_icon_image_size'   => 'thumbnail',
				'overlay_url_icon_mirror'       => '',

				'overlay_hover_color'           => '',
				'overlay_hover_border'          => '',
				'overlay_hover_icon_background' => '',

				'animation_in_type'             => 'none',
				'animation_in_duration'         => '300',
				'animation_in_delay'            => '0',
				'animation_in_offset'           => '100',

				'mpc_navigation__preset'        => '',
			), $atts );

			/* Validate data */
			if( empty( $atts[ 'images' ] ) ) {
				return '';
			}

			/* Prepare */
			$styles    = $this->shortcode_styles( $atts );
			$css_id    = $styles[ 'id' ];
			$carousel  = MPC_Parser::carousel( $atts );
			$animation = MPC_Parser::animation( $atts );

			$images       = explode( ',', $atts[ 'images' ] );
			$images_links = $atts[ 'images_links' ] != '' ? explode( ',', $atts[ 'images_links' ] ) : '';
			$image_size   = $atts[ 'image_size' ] != '' ? $atts[ 'image_size' ] : 'medium';
			$index        = 0;

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'effect' ] != '' ? ' mpc-effect--' . $atts[ 'effect' ] : '';
			$classes .= $atts[ 'effect_reverse' ] != '' ? ' mpc-effect--reverse' : '';
			$classes .= $atts[ 'stretched' ] != '' ? ' mpc-carousel--stretched' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$classes_counter = $atts[ 'font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'font_preset' ] ) : '';
			$classes_counter .= $atts[ 'counter_position' ] != '' ? ' mpc-align--' . esc_attr( $atts[ 'counter_position' ] ) : '';

			$data_atts = $animation . $carousel;
			$classes_item = 'mpc-carousel__item-wrapper';

			/* Lightbox & Overlay */
			$overlay_begin = $overlay_end = $lightbox = '';
			if( $atts[ 'overlay_enable_lightbox' ] != '' || $atts[ 'overlay_enable_url' ] != '' ? true : false ) {
				$overlay_begin = '<div class="mpc-item-overlay mpc-transition"><div class="mpc-overlay--vertical-wrap"><div class="mpc-overlay--vertical">';
				$overlay_end   = '</div></div></div>';

				$data_atts .= $atts[ 'overlay_icon_align' ] != '' ? ' data-align="' . esc_attr( $atts[ 'overlay_icon_align' ] ) . '"' : '';
				$classes .= $atts[ 'overlay_overlay_effect' ] != '' ? ' mpc-overlay--' . esc_attr( $atts[ 'overlay_overlay_effect' ] ) : ' mpc-overlay--fade';
			}

			$overlay_atts = $overlay_icon = '';
			if( $atts[ 'overlay_enable_lightbox' ] != '' ) {
				$lightbox = MPC_Helper::lightbox_vendor();

				$overlay_atts = ' rel="mpc[' . $css_id . ']"';
				$classes_icon = 'mpc-item-overlay__icon mpc-type--lightbox';
				$classes_icon .= $atts[ 'overlay_icon_mirror' ] != '' ? ' mpc-icon--mirror' : '';

				$overlay_icon = MPC_Parser::icon( $atts, 'overlay' );
				$overlay_icon[ 'class' ] .= $atts[ 'overlay_icon_type' ] != '' ? ' mpc-icon--' . esc_attr( $atts[ 'overlay_icon_type' ] ) : '';

				$overlay_icon = '<i class="' . $classes_icon . esc_attr( $overlay_icon[ 'class' ] ) . '">' . $overlay_icon[ 'content' ] . '</i>';
			}

			$url_icon = '';
			if( $atts[ 'overlay_enable_url' ] != '' ) {
				$classes_icon = 'mpc-item-overlay__icon mpc-type--external';
				$classes_icon .= $atts[ 'overlay_url_icon_mirror' ] != '' ? ' mpc-icon--mirror' : '';

				$url_icon = MPC_Parser::icon( $atts, 'overlay_url' );
				$url_icon[ 'class' ] .= $atts[ 'overlay_url_icon_type' ] != '' ? ' mpc-icon--' . esc_attr( $atts[ 'overlay_url_icon_type' ] ) : '';

				$url_icon = '<i class="' . $classes_icon  . $url_icon[ 'class' ] . '">' . $url_icon[ 'content' ] . '</i>';
			}

			/* Shortcode Output */
			$return = '<div class="mpc-carousel__wrapper mpc-waypoint">';
				$carousel = '<div id="' . $css_id . '" class="mpc-carousel-slider' . $classes . '" ' . $data_atts . '>';

				foreach( $images as $image_id ) {
					$image = wpb_getImageBySize( array(
						'attach_id'  => $image_id,
						'thumb_size' => $image_size,
						'class'      => 'mpc-transition',
					) );

					if( !$image )
						continue;

					$overlay = '';
					if( $atts[ 'overlay_enable_url' ] != '' ) {
						$url_link = isset( $images_links[ $index ] ) && $images_links[ $index ] != '' ? esc_url( $images_links[ $index ] ) : '';
						$overlay .= '<a href="' . $url_link . '" class="mpc-icon-anchor">' . $url_icon . '</a>';
					}

					if( $atts[ 'overlay_enable_lightbox' ] != '' ) {
						$image_link = isset( $image[ 'p_img_large' ][ 0 ] ) ? esc_url( $image[ 'p_img_large' ][ 0 ] ) : '';
						$overlay .= '<a href="' . $image_link . '"' . $overlay_atts . ' class="mpc-icon-anchor' . $lightbox . '">' . $overlay_icon . '</a>';
					}

					$overlay = $overlay != '' ? $overlay_begin . $overlay . $overlay_end : '';
					$wrapper_link = $overlay == '' && isset( $images_links[ $index ] ) && $images_links[ $index ] != '' ? true : false;

					$wrapper     = $wrapper_link != '' ? 'a href="' . esc_url( $images_links[ $index ] ) . '"' : 'div onclick=""'; // Testing iOS
					$wrapper_end = $wrapper_link != '' ? 'a' : 'div';
					$border		 = '<div class="mpc-border"></div>';

					$item = '<' . $wrapper . ' class="' . $classes_item . '">';
						$item .= '<div class="mpc-item mpc-transition">' . $border . $image[ 'thumbnail' ] . $overlay . '</div>';
					$item .= '</' . $wrapper_end . '>';

					$carousel .= $item;
					$index++;
				}

				$carousel .= '<span class="mpc-carousel__count' . $classes_counter . '" data-current-slide="' . (int) esc_attr( $atts[ 'start_at' ] ) .  '" data-slides-amount="' . count( $images ) . '">/</span>';
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
			$css_id = uniqid( 'mpc_carousel_slider-' . rand( 1, 100 ) );
			$style  = '';

			// Add 'px'
			$styles[ 'font_size' ] = $styles[ 'font_size' ] != '' ? $styles[ 'font_size' ] . ( is_numeric( $styles[ 'font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'second_font_size' ] = $styles[ 'second_font_size' ] != '' ? $styles[ 'second_font_size' ] . ( is_numeric( $styles[ 'second_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'gap' ] = $styles[ 'gap' ] != '' ? $styles[ 'gap' ] . ( is_numeric( $styles[ 'gap' ] ) ? 'px' : '' ) : '';
			$styles[ 'height' ] = $styles[ 'height' ] != '' ? $styles[ 'height' ] . ( is_numeric( $styles[ 'height' ] ) ? 'px' : '' ) : '';
			$styles[ 'overlay_icon_size' ] = $styles[ 'overlay_icon_size' ] != '' ? $styles[ 'overlay_icon_size' ] . ( is_numeric( $styles[ 'overlay_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'overlay_url_icon_size' ] = $styles[ 'overlay_url_icon_size' ] != '' ? $styles[ 'overlay_url_icon_size' ] . ( is_numeric( $styles[ 'overlay_url_icon_size' ] ) ? 'px' : '' ) : '';

			// Gap
			if( $styles[ 'gap' ] && $styles[ 'gap' ] != '0px' ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-carousel__item-wrapper {';
					$style .= 'margin-left: ' . $styles[ 'gap' ] . ';';
					$style .= 'margin-right: ' . $styles[ 'gap' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'height' ] ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] {';
					$style .=  'height: ' . $styles[ 'height' ] . ';';
				$style .= '}';
			}

			// Counter
			$inner_styles = array();
			if ( $styles[ 'counter_padding_css' ] ) { $inner_styles[] = $styles[ 'counter_padding_css' ]; }
			if ( $styles[ 'counter_margin_css' ] ) { $inner_styles[] = $styles[ 'counter_margin_css' ]; }
			if ( $styles[ 'counter_border_css' ] ) { $inner_styles[] = $styles[ 'counter_border_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::font( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-carousel__count {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'second_font_size' ] ) { $inner_styles[] = 'font-size:' . $styles[ 'second_font_size' ] . ';'; }
			if ( $styles[ 'second_font_color' ] ) { $inner_styles[] = 'color:' . $styles[ 'second_font_color' ] . ';'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-carousel__count::before {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Regular
			if ( $styles[ 'image_opacity' ] ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-item {';
					$style .= 'opacity: ' . ( $styles[ 'image_opacity' ] / 100 ) . ';filter: alpha( opacity = ' . $styles[ 'image_opacity' ] . ' );';
				$style .= '}';
			}

			if ( $styles[ 'image_border_css' ] ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-border {';
					$style .= $styles[ 'image_border_css' ];
				$style .= '}';
			}

			if ( $styles[ 'image_inner_border_css' ] ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-border::before {';
					$style .= $styles[ 'image_inner_border_css' ];
				$style .= '}';
			}

			// Hover
			if ( $styles[ 'image_hover_opacity' ] ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-item:hover {';
					$style .= $styles[ 'image_hover_opacity' ];
				$style .= '}';
			}

			if ( $styles[ 'image_hover_border_css' ] ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-item:hover .mpc-border {';
					$style .= $styles[ 'image_hover_border_css' ];
				$style .= '}';
			}

			if ( $styles[ 'image_hover_inner_border_css' ] ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-item:hover .mpc-border::before {';
					$style .= $styles[ 'image_hover_inner_border_css' ];
				$style .= '}';
			}

			// Overlay & Lightbox
			if ( $styles[ 'overlay_background' ] ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-item-overlay {';
					$style .= 'background: ' . $styles[ 'overlay_background' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'overlay_padding_css' ] ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-overlay--vertical {';
					$style .= $styles[ 'overlay_padding_css' ];
				$style .= '}';
			}


			$inner_styles = array();
			if ( $styles[ 'overlay_icon_border_css' ] ) { $inner_styles[] = $styles[ 'overlay_icon_border_css' ]; }
			if ( $styles[ 'overlay_icon_padding_css' ] ) { $inner_styles[] = $styles[ 'overlay_icon_padding_css' ]; }
			if ( $styles[ 'overlay_icon_margin_css' ] ) { $inner_styles[] = $styles[ 'overlay_icon_margin_css' ]; }
			if ( $styles[ 'overlay_icon_background' ] ) { $inner_styles[] = 'background: ' . $styles[ 'overlay_icon_background' ] . ';'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-icon-anchor {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::icon( $styles, 'overlay_url' ) ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-type--external {';
					$style .= $temp_style;
				$style .= '}';
			}
			if ( $temp_style = MPC_CSS::icon( $styles, 'overlay' ) ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-type--lightbox {';
					$style .= $temp_style;
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'overlay_hover_border' ] ) { $inner_styles[] = 'border-color:' . $styles[ 'overlay_hover_border' ] . ';'; }
			if ( $styles[ 'overlay_hover_icon_background' ] ) { $inner_styles[] = 'background:' . $styles[ 'overlay_hover_icon_background' ]. ';'; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-icon-anchor:hover {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}
			if ( $styles[ 'overlay_hover_color' ] ) {
				$style .= '.mpc-carousel-slider[id="' . $css_id . '"] .mpc-icon-anchor:hover i {';
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
			);

			$base_ext = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Display', 'mpc' ),
					'param_name'       => 'display_divider',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_slider',
					'heading'          => __( 'Slider Height', 'mpc' ),
					'param_name'       => 'height',
					'tooltip'          => __( 'Choose desired slider height.', 'mpc' ),
					'min'              => 50,
					'max'              => 1000,
					'step'             => 25,
					'value'            => 400,
					'unit'             => 'px',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
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
					'value'            => '1',
					'label'            => '',
					'validate'         => true,
					'addon'            => array(
						'icon'  => 'dashicons-images-alt',
						'align' => 'prepend'
					),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
			);

			$counter = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Counter Position', 'mpc' ),
					'param_name'       => 'counter_position',
					'tooltip'          => __( 'Select slides counter position.', 'mpc' ),
					'value'            => array(
						__( 'Top Left', 'mpc' )     => 'top-left',
						__( 'Top Right', 'mpc' )    => 'top-right',
						__( 'Bottom Left', 'mpc' )  => 'bottom-left',
						__( 'Bottom Right', 'mpc' ) => 'bottom-right',
					),
					'std'              => 'bottom-right',
					'group'            => __( 'Counter', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
			);

			$second_font = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Current Slide - Typography', 'mpc' ),
					'param_name' => 'loop_divider',
					'group'      => __( 'Counter', 'mpc' ),
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Size', 'mpc' ),
					'param_name'       => 'second_font_size',
					'tooltip'          => __( 'Define font size for current image number in counter.', 'mpc' ),
					'value'            => '',
					'label'            => 'px',
					'validate'         => true,
					'addon'            => array(
						'icon'  => 'dashicons-editor-textcolor',
						'align' => 'prepend'
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'group'            => __( 'Counter', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'second_font_color',
					'tooltip'          => __( 'Choose font color for current image number in counter.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-color-picker',
					'group'            => __( 'Counter', 'mpc' ),
				),
			);

			$image_opacity = array(
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

			$image_hover_opacity = array(
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
					'value'            => array( __( 'Yes', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Images', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
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
			$image_atts = array( 'prefix' => 'image', 'subtitle' => __( 'Image', 'mpc' ), 'group' => __( 'Images', 'mpc' ));
			$image_hover_atts = array( 'prefix' => 'image_hover', 'subtitle' => __( 'Image Hover', 'mpc' ), 'group' => __( 'Images', 'mpc' ));

			$image_border          = MPC_Snippets::vc_border( $image_atts );
			$image_inner_border    = MPC_Snippets::vc_inner_border( $image_atts );

			$image_hover_border       = MPC_Snippets::vc_border( $image_hover_atts );
			$image_hover_inner_border = MPC_Snippets::vc_inner_border( $image_hover_atts );

			$counter_font       = MPC_Snippets::vc_font( array( 'subtitle' => __( 'Counter', 'mpc' ), 'group' => __( 'Counter', 'mpc' ) ) );
			$counter_padding    = MPC_Snippets::vc_padding( array( 'prefix' => 'counter', 'subtitle' => __( 'Counter', 'mpc' ), 'group' => __( 'Counter', 'mpc' ) ) );
			$counter_margin     = MPC_Snippets::vc_margin( array( 'prefix' => 'counter', 'subtitle' => __( 'Counter', 'mpc' ), 'group' => __( 'Counter', 'mpc' ) ) );
			$counter_border     = MPC_Snippets::vc_border( array( 'prefix' => 'counter', 'subtitle' => __( 'Counter', 'mpc' ), 'group' => __( 'Counter', 'mpc' ) ) );
			$counter_background = MPC_Snippets::vc_background( array( 'subtitle' => __( 'Counter', 'mpc' ), 'group' => __( 'Counter', 'mpc' ) ) );

			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			$integrate_navigation = vc_map_integrate_shortcode( 'mpc_navigation', 'mpc_navigation__', __( 'Navigation', 'mpc' ) );

			$params = array_merge(
				$base,
				$base_ext,

				$counter,
				$counter_font,
				$second_font,
				$counter_background,
				$counter_border,
				$counter_padding,
				$counter_margin,

				$image_opacity,
				$image_border,
				$image_inner_border,

				$image_hover_opacity,
				$image_hover_border,
				$image_hover_inner_border,

				$overlay,

				$animation,
				$integrate_navigation,
				$class
			);

			return array(
				'name'        => __( 'Carousel Slider', 'mpc' ),
				'description' => __( 'Carousel slider with images', 'mpc' ),
				'base'        => 'mpc_carousel_slider',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-carousel-slider.png',
				'icon'        => 'mpc-shicon-car-slider',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}

if ( class_exists( 'MPC_Carousel_Slider' ) ) {
	global $MPC_Carousel_Slider;
	$MPC_Carousel_Slider = new MPC_Carousel_Slider;
}

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_mpc_carousel_slider' ) ) {
	class WPBakeryShortCode_mpc_carousel_slider extends WPBakeryShortCode {
		function __construct( $settings ) {
			parent::__construct( $settings );

			$this->shortcodeScripts();
		}

		public function shortcodeScripts() {
			wp_enqueue_script( 'vc_grid-js-imagesloaded', vc_asset_url( 'lib/bower/imagesloaded/imagesloaded.pkgd.min.js' ) );
		}

		public function singleParamHtmlHolder( $param, $value ) {
			$output = '';

			$param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
			$type = isset( $param['type'] ) ? $param['type'] : '';
			$class = isset( $param['class'] ) ? $param['class'] : '';

			if ( isset( $param['holder'] ) == true && $param['holder'] !== 'hidden' ) {
				$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
			}
			if ( $param_name == 'images' ) {
				$images_ids = empty( $value ) ? array() : explode( ',', trim( $value ) );
				$output .= '<ul class="attachment-thumbnails' . ( empty( $images_ids ) ? ' image-exists' : '' ) . '" data-name="' . $param_name . '">';
				foreach ( $images_ids as $image ) {
					$img = wpb_getImageBySize( array( 'attach_id' => (int) $image, 'thumb_size' => 'thumbnail' ) );
					$output .= ( $img ? '<li>' . $img['thumbnail'] . '</li>' : '<li><img width="150" height="150" test="' . $image . '" src="' . vc_asset_url( 'vc/blank.gif' ) . '" class="attachment-thumbnail" alt="" title="" /></li>' );
				}
				$output .= '</ul>';
				$output .= '<a href="#" class="column_edit_trigger' . ( ! empty( $images_ids ) ? ' image-exists' : '' ) . '">' . __( 'Add images', 'js_composer' ) . '</a>';

			}

			return $output;
		}
	}
}

<?php
/**
 * Add-on Name: Image Separator
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package Image Separator
 */

if ( ! class_exists( 'Ultimate_Image_Separator' ) ) {
	/**
	 * Function that initializes Image Separator Module
	 *
	 * @class Ultimate_Image_Separator
	 */
	class Ultimate_Image_Separator {
		/**
		 * Constructor function that constructs default values for the Image Separator module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'ultimate_img_separator_init' ) );
			}
			add_shortcode( 'ultimate_img_separator', array( $this, 'ultimate_img_separator_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_easy_separator_assets' ), 1 );
		}
		/**
		 * Function that register styles and scripts for Image Separator Module.
		 *
		 * @method register_easy_separator_assets
		 */
		public function register_easy_separator_assets() {
			Ultimate_VC_Addons::ultimate_register_style( 'ult-easy-separator-style', 'image-separator' );

			Ultimate_VC_Addons::ultimate_register_script( 'ult-easy-separator-script', 'image-separator', false, array( 'jquery' ), ULTIMATE_VERSION, false );
		}
		/**
		 * Function that initializes settings of Image Separator Module.
		 *
		 * @method ultimate_img_separator_init
		 */
		public function ultimate_img_separator_init() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'        => __( 'Image Separator', 'ultimate_vc' ),
						'base'        => 'ultimate_img_separator',
						'class'       => 'vc_img_separator_icon',
						'icon'        => 'vc_icon_img_separator',
						'category'    => 'Ultimate VC Addons',
						'description' => __( 'Add image as row seperator', 'ultimate_vc' ),
						'params'      => array(
							array(
								'type'       => 'ult_img_single',
								'heading'    => __( 'Image', 'ultimate_vc' ),
								'param_name' => 'img_separator',
							),
							array(
								'type'       => 'animator',
								'class'      => '',
								'heading'    => __( 'Animation', 'ultimate_vc' ),
								'param_name' => 'animation',
								'value'      => '',
								'group'      => 'Animation',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Animation Duration', 'ultimate_vc' ),
								'param_name'  => 'animation_duration',
								'value'       => 3,
								'min'         => 1,
								'max'         => 100,
								'suffix'      => 's',
								'description' => __( 'How long the animation effect should last. Decides the speed of effect.', 'ultimate_vc' ),
								'group'       => 'Animation',

							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Animation Delay', 'ultimate_vc' ),
								'param_name'  => 'animation_delay',
								'value'       => 0,
								'min'         => 1,
								'max'         => 100,
								'suffix'      => 's',
								'description' => __( 'Delays the animation effect for seconds you enter above.', 'ultimate_vc' ),
								'group'       => 'Animation',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Animation Repeat Count', 'ultimate_vc' ),
								'param_name'  => 'animation_iteration_count',
								'value'       => 1,
								'min'         => 0,
								'max'         => 100,
								'suffix'      => '',
								'description' => __( 'The animation effect will repeat to the count you enter above. Enter 0 if you want to repeat it infinitely.', 'ultimate_vc' ),
								'group'       => 'Animation',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Viewport Position', 'ultimate_vc' ),
								'param_name'  => 'opacity_start_effect',
								'suffix'      => '%',
								'value'       => '90',
								'description' => __( 'The area of screen from top where animation effects will start working.', 'ultimate_vc' ),
								'group'       => 'Animation',
							),

							array(
								'type'       => 'ultimate_responsive',
								'heading'    => __( 'Image Size (px)', 'ultimate_vc' ),
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',                  // Here '28' is default value set for 'Desktop'.
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'param_name' => 'img_separator_width',
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Image Position', 'ultimate_vc' ),
								'param_name' => 'img_separator_position',
								'value'      => array(
									__( 'Top', 'ultimate_vc' ) => 'ult-top-easy-separator',
									__( 'Bottom', 'ultimate_vc' ) => 'ult-bottom-easy-separator',
								),
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Gutter', 'ultimate_vc' ),
								'param_name'  => 'img_separator_gutter',
								'suffix'      => '%',
								'description' => __( '50% is default. Increase to push the image outside or decrease to pull the image inside.', 'ultimate_vc' ),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Image Alignment', 'ultimate_vc' ),
								'param_name' => 'img_separator_alignment',
								'value'      => array(
									__( 'Center', 'ultimate_vc' ) => 'ult-center-img',
									__( 'Left', 'ultimate_vc' ) => 'ult-left-img',
									__( 'Right', 'ultimate_vc' ) => 'ult-right-img',
								),
							),
							array(
								'type'       => 'number',
								'heading'    => __( 'Image Position from Left', 'ultimate_vc' ),
								'param_name' => 'img_position_left',
								'suffix'     => '%',
								'dependency' => array(
									'element' => 'img_separator_alignment',
									'value'   => array( 'ult-left-img' ),
								),
							),
							array(
								'type'       => 'number',
								'heading'    => __( 'Image Position from right', 'ultimate_vc' ),
								'param_name' => 'img_position_right',
								'suffix'     => '%',
								'dependency' => array(
									'element' => 'img_separator_alignment',
									'value'   => array( 'ult-right-img' ),
								),
							),
							array(
								'type'        => 'vc_link',
								'heading'     => __( 'Link ', 'ultimate_vc' ),
								'param_name'  => 'sep_link',
								'value'       => '',
								'description' => __( 'Add a custom link or select existing page. You can remove existing link as well.', 'ultimate_vc' ),
							),
						),
					)
				);
			}
		}
		/**
		 * Render function for Image Separator Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function ultimate_img_separator_shortcode( $atts, $content ) {
			$output                    = '';
			$wrapper_class             = '';
			$custom_position           = '';
			$opacity_start_effect_data = '';
			$animation_style           = '';
			$animation_el_class        = '';
			$animation_data            = '';
			$href                      = '';
			$url                       = '';
			$link_title                = '';
			$target                    = '';
			$target                    = '';
			$link_title                = '';
			$rel                       = '';
			$is_animation              = false;
				$ult_imgs_settings     = shortcode_atts(
					array(
						'img_separator'             => '',
						'animation'                 => '',
						'img_separator_width'       => '',
						'img_separator_position'    => 'ult-top-easy-separator',
						'img_separator_alignment'   => 'ult-center-img',
						'img_separator_gutter'      => '',
						'img_position_left'         => '',
						'img_position_right'        => '',
						'opacity'                   => 'set',
						'opacity_start_effect'      => '',
						'animation_duration'        => '',
						'animation_delay'           => '',
						'animation_iteration_count' => '',
						'sep_link'                  => '',
					),
					$atts
				);

			$ultimate_custom_vc_row = get_option( 'ultimate_custom_vc_row' );
			if ( '' == $ultimate_custom_vc_row ) {
				$ultimate_custom_vc_row = 'wpb_row';
			}

			$img = apply_filters( 'ult_get_img_single', $ult_imgs_settings['img_separator'], 'url' );
			$alt = apply_filters( 'ult_get_img_single', $ult_imgs_settings['img_separator'], 'alt' );

			$id = 'ult-easy-separator-' . uniqid( wp_rand() );

			if ( '' != $ult_imgs_settings['sep_link'] ) {
				$href = vc_build_link( $ult_imgs_settings['sep_link'] );

				$url        = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
				$target     = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
				$link_title = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
				$rel        = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
			}

			$args      = array(
				'target'      => '#' . $id,  // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'width' => $ult_imgs_settings['img_separator_width'],
				),
			);
			$data_list = get_ultimate_vc_responsive_media_css( $args );

			$trans = '-50%';
			if ( is_rtl() ) {
				$trans = '50%';
			}
			if ( 'ult-left-img' == $ult_imgs_settings['img_separator_alignment'] || 'ult-right-img' == $ult_imgs_settings['img_separator_alignment'] ) {
				$trans = '0';
			}

			if ( '' != $ult_imgs_settings['img_separator_gutter'] ) {
				$wrapper_class = 'ult-easy-separator-no-default';
				if ( 'ult-top-easy-separator' == $ult_imgs_settings['img_separator_position'] ) {
					$ult_imgs_settings['img_separator_gutter'] = '-' . $ult_imgs_settings['img_separator_gutter'];
					$custom_position                          .= 'transform: translate(' . $trans . ',' . $ult_imgs_settings['img_separator_gutter'] . '%)!important;';
					$custom_position                          .= '-ms-transform: translate(' . $trans . ',' . $ult_imgs_settings['img_separator_gutter'] . '%)!important;';
					$custom_position                          .= '-webkit-transform: translate(' . $trans . ',' . $ult_imgs_settings['img_separator_gutter'] . '%)!important;';

				} elseif ( 'ult-bottom-easy-separator' == $ult_imgs_settings['img_separator_position'] ) {
					$custom_position .= 'transform: translate(' . $trans . ',' . $ult_imgs_settings['img_separator_gutter'] . '%)!important;';
					$custom_position .= '-ms-transform: translate(' . $trans . ',' . $ult_imgs_settings['img_separator_gutter'] . '%)!important;';
					$custom_position .= '-webkit-transform: translate(' . $trans . ',' . $ult_imgs_settings['img_separator_gutter'] . '%)!important;';
				}
			}

			$img_alignment = '';
			if ( 'ult-left-img' == $ult_imgs_settings['img_separator_alignment'] && ! wp_is_mobile() ) {
				$img_alignment = 'ult-left-img';
			} elseif ( 'ult-right-img' == $ult_imgs_settings['img_separator_alignment'] && ! wp_is_mobile() ) {
				$img_alignment = 'ult-right-img';
			} else {
				$img_alignment = '';
			}

			$img_alignment_position     = '';
			$img_separator_gutter_value = '';
			if ( '' != $ult_imgs_settings['img_separator_gutter'] ) {
				$img_separator_gutter_value = esc_attr( $ult_imgs_settings['img_separator_gutter'] );
			} else {
				$img_separator_gutter_value = '50';
			}

			if ( 'ult-left-img' == $ult_imgs_settings['img_separator_alignment'] && '' != $ult_imgs_settings['img_position_left'] && ! wp_is_mobile() ) {
				$img_alignment_position  = 'left:' . $ult_imgs_settings['img_position_left'] . '%;';
				$img_alignment_position .= 'transform: translate(-' . $ult_imgs_settings['img_position_left'] . '%,' . $img_separator_gutter_value . '%);';
				$img_alignment_position .= '-ms-transform: translate(-' . $ult_imgs_settings['img_position_left'] . '%,' . $img_separator_gutter_value . '%);';
				$img_alignment_position .= '-webkit-transform: translate(-' . $ult_imgs_settings['img_position_left'] . '%,' . $img_separator_gutter_value . '%);';
			} elseif ( 'ult-right-img' == $ult_imgs_settings['img_separator_alignment'] && '' != $ult_imgs_settings['img_position_right'] && ! wp_is_mobile() ) {
				$img_alignment_position  = 'right:' . $ult_imgs_settings['img_position_right'] . '%;';
				$img_alignment_position .= 'transform: translate(' . $ult_imgs_settings['img_position_right'] . '%,' . $img_separator_gutter_value . '%);';
				$img_alignment_position .= '-ms-transform: translate(' . $ult_imgs_settings['img_position_right'] . '%,' . $img_separator_gutter_value . '%);';
				$img_alignment_position .= '-webkit-transform: translate(' . $ult_imgs_settings['img_position_right'] . '%,' . $img_separator_gutter_value . '%);';
			} else {
				$img_alignment_position = '';
			}

			$animation_style .= 'opacity:0;';
			if ( strtolower( $ult_imgs_settings['animation'] ) !== strtolower( 'No Animation' ) ) {
				$is_animation  = true;
				$inifinite_arr = array( 'InfiniteRotate', 'InfiniteDangle', 'InfiniteSwing', 'InfinitePulse', 'InfiniteHorizontalShake', 'InfiniteBounce', 'InfiniteFlash', 'InfiniteTADA' );
				if ( 0 == $ult_imgs_settings['animation_iteration_count'] || in_array( $ult_imgs_settings['animation'], $inifinite_arr, true ) ) {
					$ult_imgs_settings['animation_iteration_count'] = 'infinite';
					$ult_imgs_settings['animation']                 = 'infinite ' . $ult_imgs_settings['animation'];
				}
				if ( 'set' == $ult_imgs_settings['opacity'] ) {
					$animation_el_class       .= ' ult-animation ult-animate-viewport ';
					$opacity_start_effect_data = 'data-opacity_start_effect="' . esc_attr( $ult_imgs_settings['opacity_start_effect'] ) . '"';
				}
				$animation_data .= ' data-animate="' . esc_attr( $ult_imgs_settings['animation'] ) . '" ';
				$animation_data .= ' data-animation-delay="' . esc_attr( $ult_imgs_settings['animation_delay'] ) . '" ';
				$animation_data .= ' data-animation-duration="' . esc_attr( $ult_imgs_settings['animation_duration'] ) . '" ';
				$animation_data .= ' data-animation-iteration="' . esc_attr( $ult_imgs_settings['animation_iteration_count'] ) . '" ';
			} else {
				$animation_el_class .= 'ult-no-animation';
			}

			$output              = '<div id="' . esc_attr( $id ) . '" class="ult-easy-separator-wrapper ult-responsive ' . esc_attr( $ult_imgs_settings['img_separator_position'] ) . ' ' . esc_attr( $wrapper_class ) . ' ' . esc_attr( $img_alignment ) . '" style="' . esc_attr( $custom_position ) . '' . esc_attr( $img_alignment_position ) . '" data-vc-row="' . esc_attr( $ultimate_custom_vc_row ) . '" ' . $data_list . '>';
				$output         .= '<div class="ult-easy-separator-inner-wrapper">';
					$output     .= '<div class="' . esc_attr( $animation_el_class ) . '" style="' . esc_attr( $animation_style ) . '"  ' . $animation_data . ' ' . $opacity_start_effect_data . '>';
						$output .= '<img class="ult-easy-separator-img" alt="' . esc_attr( $alt ) . '" src="' . esc_url( apply_filters( 'ultimate_images', $img ) ) . '" />';
			if ( '' != $url ) {
				$output .= '<a ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . '></a>';
			}
					$output .= '</div>';
				$output     .= '</div>';
			$output         .= '</div>';

			return $output;
		}
	}
}
if ( class_exists( 'Ultimate_Image_Separator' ) ) {
	/**
	* Function that checks if the class is exists or not.
	*/
	$ultimate_image_separator = new Ultimate_Image_Separator();
}



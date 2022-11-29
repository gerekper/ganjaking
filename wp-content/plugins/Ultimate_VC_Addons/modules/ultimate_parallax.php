<?php
/**
 * Add-on Name: Ultimate Parallax Background for WPBakery Page Builder
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package UAVC Ultimate Parallax
 */

$ultimate_row = get_option( 'ultimate_row' );
if ( 'enable' != $ultimate_row ) {
	return false;
}
if ( ! class_exists( 'Ultimate_VC_Addons_Parallax' ) ) {
	/**
	 * Function that initializes  Ultimate Parallax.
	 *
	 * @class Ultimate_VC_Addons_Parallax
	 */
	class Ultimate_VC_Addons_Parallax {
		/**
		 * Constructor function that constructs default values for the Ultimate Parallax.
		 *
		 * @method __construct
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'admin_init', array( $this, 'parallax_init' ) );
			$ultimate_row = get_option( 'ultimate_row' );
			if ( 'enable' == $ultimate_row ) {
				if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, '4.4', '>=' ) ) {
					add_filter( 'vc_shortcode_output', array( $this, 'execute_ultimate_vc_shortcode' ), 10, 3 );
				}
			}
		}//end __construct()

		/**
		 * Execute shortcode.
		 *
		 * @since ----
		 * @param mixed $output output.
		 * @param mixed $obj object.
		 * @param mixed $attr attributes.
		 * @access public
		 */
		public function execute_ultimate_vc_shortcode( $output, $obj, $attr ) {
			if ( $obj->settings( 'base' ) == 'vc_row' ) {
				$output .= $this->parallax_shortcode( $attr, '' );
			}
			return $output;
		}
		/**
		 * Execute Parallax shortcode.
		 *
		 * @since ----
		 * @param mixed $atts atts variable.
		 * @param mixed $content contents.
		 * @access public
		 */
		public static function parallax_shortcode( $atts, $content ) {
			$bsf_img_repeat = '';
			$overlay        = '';

			$seperator_html        = '';
			$seperator_bottom_html = '';
			$seperator_top_html    = '';
			$seperator_css         = '';

			$ult_parallax_settings = shortcode_atts(
				array(
					'bg_type'                        => '',
					'bg_image'                       => '',
					'bg_image_new'                   => '',
					'bg_image_repeat'                => '',
					'bg_image_size'                  => '',
					'parallax_style'                 => '',
					'parallax_sense'                 => '30',
					'video_opts'                     => '',
					'bg_image_posiiton'              => '',
					'video_url'                      => '',
					'video_url_2'                    => '',
					'video_poster'                   => '',
					'u_video_url'                    => '',
					'bg_cstm_size'                   => '',
					'bg_override'                    => '0',
					'bg_img_attach'                  => '',
					'u_start_time'                   => '',
					'u_stop_time'                    => '',
					'layer_image'                    => '',
					'bg_grad'                        => '',
					'bg_color_value'                 => '',
					'bg_fade'                        => '',
					'css'                            => '',
					'viewport_vdo'                   => '',
					'enable_controls'                => '',
					'controls_color'                 => '',
					'animation_direction'            => 'left-animation',
					'animation_type'                 => 'false',
					'horizontal_animation'           => '',
					'vertical_animation'             => '',
					'animation_speed'                => '',
					'animation_repeat'               => 'repeat',
					'animated_bg_color'              => '',
					'fadeout_row'                    => '',
					'fadeout_start_effect'           => '30',
					'parallax_content'               => '',
					'parallax_content_sense'         => '30',
					'disable_on_mobile'              => '',
					'disable_on_mobile_img_parallax' => 'off',
					'enable_overlay'                 => '',
					'overlay_color'                  => '',
					'overlay_pattern'                => '',
					'overlay_pattern_opacity'        => '80',
					'overlay_pattern_size'           => '',
					'overlay_pattern_attachment'     => 'scroll',
					'multi_color_overlay'            => '',
					'multi_color_overlay_opacity'    => '60',
					'seperator_enable'               => '',
					'seperator_type'                 => 'none_seperator',
					'seperator_position'             => 'top_seperator',
					'seperator_shape_size'           => '40',
					'seperator_shape_background'     => '#fff',
					'seperator_shape_border'         => 'none',
					'seperator_shape_border_color'   => '',
					'seperator_shape_border_width'   => '1',
					'seperator_svg_height'           => '60',
					'icon_type'                      => 'no_icon',
					'icon'                           => '',
					'icon_color'                     => '',
					'icon_style'                     => 'none',
					'icon_color_bg'                  => '',
					'icon_border_style'              => '',
					'icon_color_border'              => '#333333',
					'icon_border_size'               => '1',
					'icon_border_radius'             => '500',
					'icon_border_spacing'            => '50',
					'icon_img'                       => '',
					'img_width'                      => '48',
					'icon_size'                      => '32',
					'ult_hide_row'                   => '',
					'ult_hide_row_large_screen'      => '',
					'ult_hide_row_desktop'           => '',
					'ult_hide_row_tablet'            => '',
					'ult_hide_row_tablet_small'      => '',
					'ult_hide_row_mobile'            => '',
					'ult_hide_row_mobile_large'      => '',
					'video_fixer'                    => 'true',
				),
				$atts
			);

			if ( '' === $ult_parallax_settings['bg_type'] ) {
				$ult_parallax_settings['bg_type'] = 'no_bg';
			}
			if ( '' === $ult_parallax_settings['parallax_style'] ) {
				$ult_parallax_settings['parallax_style'] = 'vcpb-default';
			}
			if ( '' === $ult_parallax_settings['bg_image_repeat'] ) {
				$ult_parallax_settings['bg_image_repeat'] = 'repeat';
			}
			if ( '' === $ult_parallax_settings['bg_image_size'] ) {
				$ult_parallax_settings['bg_image_size'] = 'cover';
			}
			if ( '' === $ult_parallax_settings['bg_img_attach'] ) {
				$ult_parallax_settings['bg_img_attach'] = 'scroll';
			}

			$bsf_dev_mode = bsf_get_option( 'dev_mode' );
			if ( 'enable' === $bsf_dev_mode ) {
				$js_path  = UAVC_URL . 'assets/js/';
				$css_path = UAVC_URL . 'assets/css/';
				$ext      = '';
			} else {
				$js_path  = UAVC_URL . 'assets/min-js/';
				$css_path = UAVC_URL . 'assets/min-css/';
				$ext      = '.min';
			}

			/* enqueue scripts */
			if ( ( 'enable' == get_option( 'ultimate_row' ) && '' !== $ult_parallax_settings['bg_type'] ) || '' != $ult_parallax_settings['parallax_content'] || '' != $ult_parallax_settings['fadeout_row'] ) {

				$ultimate_js = get_option( 'ultimate_js' );

				if ( 'enable' != $ultimate_js ) :
					if ( 'no_bg' == $ult_parallax_settings['bg_type'] && ( '' != $ult_parallax_settings['parallax_content'] || '' != $ult_parallax_settings['fadeout_row'] ) ) { // @codingStandardsIgnoreLine.
					} elseif ( 'no_bg' != $ult_parallax_settings['bg_type'] && ( '' != $ult_parallax_settings['parallax_content'] || '' != $ult_parallax_settings['fadeout_row'] ) ) {
						wp_enqueue_script( 'ultimate-vc-addons-appear' );
						wp_enqueue_script( 'ultimate-vc-addons-row-bg', $js_path . 'ultimate_bg' . $ext . '.js', null, ULTIMATE_VERSION, false );
						wp_enqueue_script( 'ultimate-vc-addons-custom' );
					} elseif ( 'no_bg' != $ult_parallax_settings['bg_type'] && ( '' == $ult_parallax_settings['parallax_content'] || '' == $ult_parallax_settings['fadeout_row'] ) ) {
						wp_enqueue_script( 'ultimate-vc-addons-appear' );
						wp_enqueue_script( 'ultimate-vc-addons-row-bg', $js_path . 'ultimate_bg' . $ext . '.js', null, ULTIMATE_VERSION, false );
						wp_enqueue_script( 'ultimate-vc-addons-custom' );
					}
				endif;

				$html                   = '';
				$autoplay               = '';
				$muted                  = '';
				$loop                   = '';
				$pos_suffix             = '';
				$bg_img                 = '';
				$bg_img_id              = '';
				$icon_inline            = '';
				$commom_data_attributes = '';
				$vc_version             = '';

				$ultimate_custom_vc_row = get_option( 'ultimate_custom_vc_row' );
				$ultimate_theme_support = get_option( 'ultimate_theme_support' );

				if ( defined( 'WPB_VC_VERSION' ) ) {
					$vc_version = WPB_VC_VERSION;
				}

				$is_vc_4_4 = ( version_compare( $vc_version, '4.4', '<' ) ) ? true : false;

				$commom_data_attributes                        .= ' data-custom-vc-row="' . esc_attr( $ultimate_custom_vc_row ) . '" ';
				$commom_data_attributes                        .= ' data-vc="' . esc_attr( $vc_version ) . '" ';
				$commom_data_attributes                        .= ' data-is_old_vc="' . esc_attr( $is_vc_4_4 ) . '" ';
				$commom_data_attributes                        .= ' data-theme-support="' . esc_attr( $ultimate_theme_support ) . '" ';
					$ult_parallax_settings['disable_on_mobile'] = 'true';

				if ( 'off' == $ult_parallax_settings['disable_on_mobile_img_parallax'] || '' == $ult_parallax_settings['disable_on_mobile_img_parallax'] ) {
					$ult_parallax_settings['disable_on_mobile_img_parallax'] = 'true';
				} else {
					$ult_parallax_settings['disable_on_mobile_img_parallax'] = 'false';
				}
				// for overlay.
				if ( 'enable_overlay_value' == $ult_parallax_settings['enable_overlay'] ) {
					if ( 'transperant' != $ult_parallax_settings['overlay_pattern'] && '' != $ult_parallax_settings['overlay_pattern'] ) {
						$pattern_url = UAVC_URL . 'assets/images/patterns/' . $ult_parallax_settings['overlay_pattern'];
					} else {
						$pattern_url = '';
					}
					if ( preg_match( '/^#[a-f0-9]{6}$/i', $ult_parallax_settings['overlay_color'] ) ) { // hex color is valid.
						$ult_parallax_settings['overlay_color'] = hex2rgbUltParallax( $ult_parallax_settings['overlay_color'], $opacity = 0.2 );
					}

					if ( strpos( $ult_parallax_settings['overlay_pattern_opacity'], '.' ) === false ) {
						$ult_parallax_settings['overlay_pattern_opacity'] = $ult_parallax_settings['overlay_pattern_opacity'] / 100;
					}

					$overlay = ' data-overlay="true" data-overlay-color="' . esc_attr( $ult_parallax_settings['overlay_color'] ) . '" data-overlay-pattern="' . esc_attr( $pattern_url ) . '" data-overlay-pattern-opacity="' . esc_attr( $ult_parallax_settings['overlay_pattern_opacity'] ) . '" data-overlay-pattern-size="' . esc_attr( $ult_parallax_settings['overlay_pattern_size'] ) . '" data-overlay-pattern-attachment="' . esc_attr( $ult_parallax_settings['overlay_pattern_attachment'] ) . '" ';

					if ( 'uvc-multi-color-bg' == $ult_parallax_settings['multi_color_overlay'] ) {
						$ult_parallax_settings['multi_color_overlay_opacity'] = $ult_parallax_settings['multi_color_overlay_opacity'] / 100;
						$overlay .= ' data-multi-color-overlay="' . esc_attr( $ult_parallax_settings['multi_color_overlay'] ) . '" data-multi-color-overlay-opacity="' . esc_attr( $ult_parallax_settings['multi_color_overlay_opacity'] ) . '" ';
					}
				} else {
					$overlay = ' data-overlay="false" data-overlay-color="" data-overlay-pattern="" data-overlay-pattern-opacity="" data-overlay-pattern-size="" ';
				}

				// for seperator.
				if ( 'seperator_enable_value' == $ult_parallax_settings['seperator_enable'] ) {
					$seperator_bottom_html  = ' data-seperator="true" ';
					$seperator_bottom_html .= ' data-seperator-type="' . esc_attr( $ult_parallax_settings['seperator_type'] ) . '" ';
					$seperator_bottom_html .= ' data-seperator-shape-size="' . esc_attr( $ult_parallax_settings['seperator_shape_size'] ) . '" ';
					$seperator_bottom_html .= ' data-seperator-svg-height="' . esc_attr( $ult_parallax_settings['seperator_svg_height'] ) . '" ';
					$seperator_bottom_html .= ' data-seperator-full-width="true"';
					$seperator_bottom_html .= ' data-seperator-position="' . esc_attr( $ult_parallax_settings['seperator_position'] ) . '" ';

					if ( '' != $ult_parallax_settings['seperator_shape_background'] ) {
						if ( 'multi_triangle_seperator' == $ult_parallax_settings['seperator_type'] ) {
							preg_match( '/\(([^)]+)\)/', $ult_parallax_settings['seperator_shape_background'], $output_temp );
							if ( isset( $output_temp[1] ) ) {
								$rgba = explode( ',', $output_temp[1] );
								$ult_parallax_settings['seperator_shape_background'] = rgbaToHexUltimate( $rgba[0], $rgba[1], $rgba[2] );
							}
						}
						$seperator_bottom_html .= ' data-seperator-background-color="' . esc_attr( $ult_parallax_settings['seperator_shape_background'] ) . '" ';
					}
					if ( 'none' != $ult_parallax_settings['seperator_shape_border'] ) {
						$seperator_bottom_html .= ' data-seperator-border="' . esc_attr( $ult_parallax_settings['seperator_shape_border'] ) . '" ';
						$bwidth                 = ( '' == $ult_parallax_settings['seperator_shape_border_width'] ) ? '1' : $ult_parallax_settings['seperator_shape_border_width'];
						$seperator_bottom_html .= ' data-seperator-border-width="' . esc_attr( $bwidth ) . '" ';
						$seperator_bottom_html .= ' data-seperator-border-color="' . esc_attr( $ult_parallax_settings['seperator_shape_border_color'] ) . '" ';
					}

					if ( 'no_icon' != $ult_parallax_settings['icon_type'] ) {
						$icon_animation = '';
						$alignment      = 'center';
						$icon_inline    = do_shortcode( '[just_icon icon_align="' . esc_attr( $alignment ) . '" icon_type="' . esc_attr( $ult_parallax_settings['icon_type'] ) . '" icon="' . esc_attr( $ult_parallax_settings['icon'] ) . '" icon_img="' . esc_attr( $ult_parallax_settings['icon_img'] ) . '" img_width="' . esc_attr( $ult_parallax_settings['img_width'] ) . '" icon_size="' . esc_attr( $ult_parallax_settings['icon_size'] ) . '" icon_color="' . esc_attr( $ult_parallax_settings['icon_color'] ) . '" icon_style="' . esc_attr( $ult_parallax_settings['icon_style'] ) . '" icon_color_bg="' . esc_attr( $ult_parallax_settings['icon_color_bg'] ) . '" icon_color_border="' . esc_attr( $ult_parallax_settings['icon_color_border'] ) . '"  icon_border_style="' . esc_attr( $ult_parallax_settings['icon_border_style'] ) . '" icon_border_size="' . esc_attr( $ult_parallax_settings['icon_border_size'] ) . '" icon_border_radius="' . esc_attr( $ult_parallax_settings['icon_border_radius'] ) . '" icon_border_spacing="' . esc_attr( $ult_parallax_settings['icon_border_spacing'] ) . '" icon_animation="' . esc_attr( $icon_animation ) . '"]' );
					}
					$seperator_bottom_html .= ' data-icon="' . htmlentities( $icon_inline ) . '" '; // @codingStandardsIgnoreLine.
				}

				$seperator_html = $seperator_top_html . ' ' . $seperator_bottom_html;

				// for hide row.
				$device_message    = '';
				$ult_hide_row_data = '';
				if ( 'ult_hide_row_value' == $ult_parallax_settings['ult_hide_row'] ) {
					if ( 'large_screen' == $ult_parallax_settings['ult_hide_row_large_screen'] ) {
						$ult_hide_row_data .= ' uvc_hidden-lg ';
					}
					if ( 'desktop' == $ult_parallax_settings['ult_hide_row_desktop'] ) {
						$ult_hide_row_data .= ' uvc_hidden-ml ';
					}
					if ( 'tablet' == $ult_parallax_settings['ult_hide_row_tablet'] ) {
						$ult_hide_row_data .= ' uvc_hidden-md ';
					}
					if ( 'xs_tablet' == $ult_parallax_settings['ult_hide_row_tablet_small'] ) {
						$ult_hide_row_data .= ' uvc_hidden-sm ';
					}
					if ( 'mobile' == $ult_parallax_settings['ult_hide_row_mobile'] ) {
						$ult_hide_row_data .= ' uvc_hidden-xs ';
					}
					if ( 'xl_mobile' == $ult_parallax_settings['ult_hide_row_mobile_large'] ) {
						$ult_hide_row_data .= ' uvc_hidden-xsl ';
					}

					if ( '' != $ult_hide_row_data ) {
						$ult_hide_row_data = ' data-hide-row="' . esc_attr( $ult_hide_row_data ) . '" ';
					}
				}

				// RTL.
				$rtl = 'false';
				if ( is_rtl() ) {
					$rtl = 'true';
				}
				if ( 'false' === $rtl || false === $rtl ) {
					$ultimate_rtl_support = get_option( 'ultimate_rtl_support' );
					if ( 'enable' == $ultimate_rtl_support ) {
						$rtl = 'true';
					}
				}

				$output = '<!-- Row Backgrounds -->';
				if ( '' != $ult_parallax_settings['bg_image_new'] ) {
					$bg_img_id = $ult_parallax_settings['bg_image_new'];
				} elseif ( '' != $ult_parallax_settings['bg_image'] ) {
					$bg_img_id = $ult_parallax_settings['bg_image'];
				} else {
					if ( '' !== $ult_parallax_settings['css'] ) {
						$arr = explode( '?id=', $ult_parallax_settings['css'] );
						if ( isset( $arr[1] ) ) {
							$arr       = explode( ')', $arr[1] );
							$bg_img_id = $arr[0];
						}
					}
				}
				if ( '' != $ult_parallax_settings['bg_image_posiiton'] ) {
					if ( strpos( $ult_parallax_settings['bg_image_posiiton'], 'px' ) ) {
						$pos_suffix = 'px';
					} elseif ( strpos( $ult_parallax_settings['bg_image_posiiton'], 'em' ) ) {
						$pos_suffix = 'em';
					} else {
						$pos_suffix = '%';
					}
				}
				if ( 'no_bg' == $ult_parallax_settings['bg_type'] ) { // @codingStandardsIgnoreLine.
				} elseif ( 'image' == $ult_parallax_settings['bg_type'] ) {
					if ( 'cstm' == $ult_parallax_settings['bg_image_size'] ) {
						if ( '' != $ult_parallax_settings['bg_cstm_size'] ) {
							$ult_parallax_settings['bg_image_size'] = $ult_parallax_settings['bg_cstm_size'];
						}
					}
					if ( 'vcpb-fs-jquery' == $ult_parallax_settings['parallax_style'] || 'vcpb-mlvp-jquery' == $ult_parallax_settings['parallax_style'] ) {
						if ( 'vcpb-fs-jquery' == $ult_parallax_settings['parallax_style'] ) {
							wp_enqueue_script( 'ultimate-vc-addons-jquery.shake', $js_path . 'jparallax' . $ext . '.js', null, ULTIMATE_VERSION, false );
						}

						if ( 'vcpb-mlvp-jquery' == $ult_parallax_settings['parallax_style'] ) {
							wp_enqueue_script( 'ultimate-vc-addons-jquery.vhparallax', $js_path . 'vhparallax' . $ext . '.js', null, ULTIMATE_VERSION, false );
						}
						$imgs                                 = explode( ',', $ult_parallax_settings['layer_image'] );
						$ult_parallax_settings['layer_image'] = array();
						foreach ( $imgs as $value ) {
							$ult_parallax_settings['layer_image'][] = wp_get_attachment_image_src( $value, 'full' );
						}
						foreach ( $ult_parallax_settings['layer_image'] as $key => $value ) {
							$bg_imgs[] = $ult_parallax_settings['layer_image'][ $key ][0];
						}
						$html .= '<div class="upb_bg_img" data-ultimate-bg="' . esc_attr( implode( ',', $bg_imgs ) ) . '" data-ultimate-bg-style="' . esc_attr( $ult_parallax_settings['parallax_style'] ) . '" data-bg-img-repeat="' . esc_attr( $ult_parallax_settings['bg_image_repeat'] ) . '" data-bg-img-size="' . esc_attr( $ult_parallax_settings['bg_image_size'] ) . '" data-bg-img-position="' . esc_attr( $ult_parallax_settings['bg_image_posiiton'] ) . '" data-parallx_sense="' . esc_attr( $ult_parallax_settings['parallax_sense'] ) . '" data-bg-override="' . esc_attr( $ult_parallax_settings['bg_override'] ) . '" data-bg_img_attach="' . esc_attr( $ult_parallax_settings['bg_img_attach'] ) . '" data-upb-overlay-color="' . esc_attr( $ult_parallax_settings['overlay_color'] ) . '" data-upb-bg-animation="' . esc_attr( $ult_parallax_settings['bg_fade'] ) . '" data-fadeout="' . esc_attr( $ult_parallax_settings['fadeout_row'] ) . '" data-fadeout-percentage="' . esc_attr( $ult_parallax_settings['fadeout_start_effect'] ) . '" data-parallax-content="' . esc_attr( $ult_parallax_settings['parallax_content'] ) . '" data-parallax-content-sense="' . esc_attr( $ult_parallax_settings['parallax_content_sense'] ) . '" data-row-effect-mobile-disable="' . esc_attr( $ult_parallax_settings['disable_on_mobile'] ) . '" data-img-parallax-mobile-disable="' . esc_attr( $ult_parallax_settings['disable_on_mobile_img_parallax'] ) . '" data-rtl="' . esc_attr( $rtl ) . '" ' . $commom_data_attributes . ' ' . $overlay . ' ' . $seperator_html . ' ' . $ult_hide_row_data . '></div>';
					} else {
						if ( 'vcpb-vz-jquery' == $ult_parallax_settings['parallax_style'] || 'vcpb-hz-jquery' == $ult_parallax_settings['parallax_style'] ) {
							wp_enqueue_script( 'ultimate-vc-addons-jquery.vhparallax', $js_path . 'vhparallax' . $ext . '.js', null, ULTIMATE_VERSION, false );
						}

						if ( $bg_img_id ) {
							if ( '' == $ult_parallax_settings['animation_direction'] && 'false' != $ult_parallax_settings['animation_type'] ) {
								if ( 'h' == $ult_parallax_settings['animation_type'] ) {
									$animation = $ult_parallax_settings['horizontal_animation'];
								} else {
									$animation = $ult_parallax_settings['vertical_animation'];
								}
							} else {
								if ( 'top-animation' == $ult_parallax_settings['animation_direction'] || 'bottom-animation' == $ult_parallax_settings['animation_direction'] ) {
									$ult_parallax_settings['animation_type'] = 'v';
								} else {
									$ult_parallax_settings['animation_type'] = 'h';
								}
									$animation = $ult_parallax_settings['animation_direction'];
								if ( '' == $animation ) {
									$animation = 'left-animation';
								}
							}

							$bg_img = apply_filters( 'ult_get_img_single', $bg_img_id, 'url' );
							$html  .= '<div class="upb_bg_img" data-ultimate-bg="url(' . esc_url( $bg_img ) . ')" data-image-id="' . esc_attr( $bg_img_id ) . '" data-ultimate-bg-style="' . esc_attr( $ult_parallax_settings['parallax_style'] ) . '" data-bg-img-repeat="' . esc_attr( $ult_parallax_settings['bg_image_repeat'] ) . '" data-bg-img-size="' . esc_attr( $ult_parallax_settings['bg_image_size'] ) . '" data-bg-img-position="' . esc_attr( $ult_parallax_settings['bg_image_posiiton'] ) . '" data-parallx_sense="' . esc_attr( $ult_parallax_settings['parallax_sense'] ) . '" data-bg-override="' . esc_attr( $ult_parallax_settings['bg_override'] ) . '" data-bg_img_attach="' . esc_attr( $ult_parallax_settings['bg_img_attach'] ) . '" data-upb-overlay-color="' . esc_attr( $ult_parallax_settings['overlay_color'] ) . '" data-upb-bg-animation="' . esc_attr( $ult_parallax_settings['bg_fade'] ) . '" data-fadeout="' . esc_attr( $ult_parallax_settings['fadeout_row'] ) . '" data-bg-animation="' . esc_attr( $animation ) . '" data-bg-animation-type="' . esc_attr( $ult_parallax_settings['animation_type'] ) . '" data-animation-repeat="' . esc_attr( $ult_parallax_settings['animation_repeat'] ) . '" data-fadeout-percentage="' . esc_attr( $ult_parallax_settings['fadeout_start_effect'] ) . '" data-parallax-content="' . esc_attr( $ult_parallax_settings['parallax_content'] ) . '" data-parallax-content-sense="' . esc_attr( $ult_parallax_settings['parallax_content_sense'] ) . '" data-row-effect-mobile-disable="' . esc_attr( $ult_parallax_settings['disable_on_mobile'] ) . '" data-img-parallax-mobile-disable="' . esc_attr( $ult_parallax_settings['disable_on_mobile_img_parallax'] ) . '" data-rtl="' . esc_attr( $rtl ) . '" ' . $commom_data_attributes . ' ' . $overlay . ' ' . $seperator_html . ' ' . $ult_hide_row_data . '></div>';
						}
					}
				} elseif ( 'video' == $ult_parallax_settings['bg_type'] ) {
					$v_opts = explode( ',', $ult_parallax_settings['video_opts'] );
					if ( is_array( $v_opts ) ) {
						foreach ( $v_opts as $opt ) {
							if ( 'muted' == $opt ) {
								$muted .= $opt;
							}
							if ( 'autoplay' == $opt ) {
								$autoplay .= $opt;
							}
							if ( 'loop' == $opt ) {
								$loop .= $opt;
							}
						}
					}
					if ( 'viewport_play' == $ult_parallax_settings['viewport_vdo'] ) {
						$enable_viewport_vdo = 'true';
					} else {
						$enable_viewport_vdo = 'false';
					}

					$video_fixer_option = get_option( 'ultimate_video_fixer' );
					if ( $video_fixer_option ) {
						if ( 'enable' == $video_fixer_option ) {
							$ult_parallax_settings['video_fixer'] = 'false';
						}
					}

					$ult_parallax_settings['u_stop_time']  = ( '' != $ult_parallax_settings['u_stop_time'] ) ? $ult_parallax_settings['u_stop_time'] : 0;
					$ult_parallax_settings['u_start_time'] = ( '' != $ult_parallax_settings['u_stop_time'] ) ? $ult_parallax_settings['u_start_time'] : 0;
					$v_img                                 = apply_filters( 'ult_get_img_single', $ult_parallax_settings['video_poster'], 'url' );
					$html                                 .= '<div class="upb_content_video" data-controls-color="' . esc_attr( $ult_parallax_settings['controls_color'] ) . '" data-controls="' . esc_attr( $ult_parallax_settings['enable_controls'] ) . '" data-viewport-video="' . esc_attr( $enable_viewport_vdo ) . '" data-ultimate-video="' . esc_attr( $ult_parallax_settings['video_url'] ) . '" data-ultimate-video2="' . esc_attr( $ult_parallax_settings['video_url_2'] ) . '" data-ultimate-video-muted="' . esc_attr( $muted ) . '" data-ultimate-video-loop="' . esc_attr( $loop ) . '" data-ultimate-video-poster="' . esc_attr( $v_img ) . '" data-ultimate-video-autoplay="autoplay" data-bg-override="' . esc_attr( $ult_parallax_settings['bg_override'] ) . '" data-upb-overlay-color="' . esc_attr( $ult_parallax_settings['overlay_color'] ) . '" data-upb-bg-animation="' . esc_attr( $ult_parallax_settings['bg_fade'] ) . '" data-fadeout="' . esc_attr( $ult_parallax_settings['fadeout_row'] ) . '" data-fadeout-percentage="' . esc_attr( $ult_parallax_settings['fadeout_start_effect'] ) . '" data-parallax-content="' . esc_attr( $ult_parallax_settings['parallax_content'] ) . '" data-parallax-content-sense="' . esc_attr( $ult_parallax_settings['parallax_content_sense'] ) . '" data-row-effect-mobile-disable="' . esc_attr( $ult_parallax_settings['disable_on_mobile'] ) . '" data-rtl="' . esc_attr( $rtl ) . '" data-img-parallax-mobile-disable="' . esc_attr( $ult_parallax_settings['disable_on_mobile_img_parallax'] ) . '" ' . $commom_data_attributes . ' ' . $overlay . ' ' . $seperator_html . ' ' . $ult_hide_row_data . ' data-video_fixer="' . esc_attr( $ult_parallax_settings['video_fixer'] ) . '"></div>';

					if ( 'display_control' == $ult_parallax_settings['enable_controls'] ) {
						wp_enqueue_style( 'ultimate-vc-addons-vidcons', UAVC_URL . 'assets/fonts/vidcons.css', null, ULTIMATE_VERSION );
					}
				} elseif ( 'u_iframe' == $ult_parallax_settings['bg_type'] ) {
					wp_enqueue_script( 'ultimate-vc-addons-jquery.ytplayer', $js_path . 'mb-YTPlayer' . $ext . '.js', null, ULTIMATE_VERSION, false );
					$v_opts = explode( ',', $ult_parallax_settings['video_opts'] );
					$v_img  = apply_filters( 'ult_get_img_single', $ult_parallax_settings['video_poster'], 'url' );
					if ( is_array( $v_opts ) ) {
						foreach ( $v_opts as $opt ) {
							if ( 'muted' == $opt ) {
								$muted .= $opt;
							}
							if ( 'autoplay' == $opt ) {
								$autoplay .= $opt;
							}
							if ( 'loop' == $opt ) {
								$loop .= $opt;
							}
						}
					}
					if ( 'viewport_play' === $ult_parallax_settings['viewport_vdo'] ) {
						$enable_viewport_vdo = 'true';
					} else {
						$enable_viewport_vdo = 'false';
					}

					$video_fixer_option = get_option( 'ultimate_video_fixer' );
					if ( $video_fixer_option ) {
						if ( 'enable' == $video_fixer_option ) {
							$ult_parallax_settings['video_fixer'] = 'false';
						}
					}

					$html .= '<div class="upb_content_iframe" data-controls="' . esc_attr( $ult_parallax_settings['enable_controls'] ) . '" data-viewport-video="' . esc_attr( $enable_viewport_vdo ) . '" data-ultimate-video="' . esc_attr( $ult_parallax_settings['u_video_url'] ) . '" data-bg-override="' . esc_attr( $ult_parallax_settings['bg_override'] ) . '" data-start-time="' . esc_attr( $ult_parallax_settings['u_start_time'] ) . '" data-stop-time="' . esc_attr( $ult_parallax_settings['u_stop_time'] ) . '" data-ultimate-video-muted="' . esc_attr( $muted ) . '" data-ultimate-video-loop="' . esc_attr( $loop ) . '" data-ultimate-video-poster="' . esc_attr( $v_img ) . '" data-upb-overlay-color="' . esc_attr( $ult_parallax_settings['overlay_color'] ) . '" data-upb-bg-animation="' . esc_attr( $ult_parallax_settings['bg_fade'] ) . '" data-fadeout="' . esc_attr( $ult_parallax_settings['fadeout_row'] ) . '" data-fadeout-percentage="' . esc_attr( $ult_parallax_settings['fadeout_start_effect'] ) . '"  data-parallax-content="' . esc_attr( $ult_parallax_settings['parallax_content'] ) . '" data-parallax-content-sense="' . esc_attr( $ult_parallax_settings['parallax_content_sense'] ) . '" data-row-effect-mobile-disable="' . esc_attr( $ult_parallax_settings['disable_on_mobile'] ) . '" data-img-parallax-mobile-disable="' . esc_attr( $ult_parallax_settings['disable_on_mobile_img_parallax'] ) . '" data-rtl="' . esc_attr( $rtl ) . '" ' . $commom_data_attributes . ' ' . $overlay . ' ' . $seperator_html . ' ' . $ult_hide_row_data . ' data-video_fixer="' . esc_attr( $ult_parallax_settings['video_fixer'] ) . '"></div>';
				} elseif ( 'grad' == $ult_parallax_settings['bg_type'] ) {
					$html .= '<div class="upb_grad" data-grad="' . esc_attr( $ult_parallax_settings['bg_grad'] ) . '" data-bg-override="' . esc_attr( $ult_parallax_settings['bg_override'] ) . '" data-upb-overlay-color="' . esc_attr( $ult_parallax_settings['overlay_color'] ) . '" data-upb-bg-animation="' . esc_attr( $ult_parallax_settings['bg_fade'] ) . '" data-fadeout="' . esc_attr( $ult_parallax_settings['fadeout_row'] ) . '" data-fadeout-percentage="' . esc_attr( $ult_parallax_settings['fadeout_start_effect'] ) . '" data-parallax-content="' . esc_attr( $ult_parallax_settings['parallax_content'] ) . '" data-parallax-content-sense="' . esc_attr( $ult_parallax_settings['parallax_content_sense'] ) . '" data-row-effect-mobile-disable="' . esc_attr( $ult_parallax_settings['disable_on_mobile'] ) . '" data-img-parallax-mobile-disable="' . esc_attr( $ult_parallax_settings['disable_on_mobile_img_parallax'] ) . '" data-rtl="' . esc_attr( $rtl ) . '" ' . $commom_data_attributes . ' ' . $overlay . ' ' . $seperator_html . ' ' . $ult_hide_row_data . '></div>';
				} elseif ( 'bg_color' == $ult_parallax_settings['bg_type'] ) {
					$html .= '<div class="upb_color" data-bg-override="' . esc_attr( $ult_parallax_settings['bg_override'] ) . '" data-bg-color="' . esc_attr( $ult_parallax_settings['bg_color_value'] ) . '" data-fadeout="' . esc_attr( $ult_parallax_settings['fadeout_row'] ) . '" data-fadeout-percentage="' . esc_attr( $ult_parallax_settings['fadeout_start_effect'] ) . '" data-parallax-content="' . esc_attr( $ult_parallax_settings['parallax_content'] ) . '" data-parallax-content-sense="' . esc_attr( $ult_parallax_settings['parallax_content_sense'] ) . '" data-row-effect-mobile-disable="' . esc_attr( $ult_parallax_settings['disable_on_mobile'] ) . '" data-img-parallax-mobile-disable="' . esc_attr( $ult_parallax_settings['disable_on_mobile_img_parallax'] ) . '" data-rtl="' . esc_attr( $rtl ) . '" ' . $commom_data_attributes . ' ' . $overlay . ' ' . $seperator_html . ' ' . $ult_hide_row_data . '></div>';
				}
				$output .= $html;
				if ( 'no_bg' == $ult_parallax_settings['bg_type'] ) {
					return '';
				} else {
					self::front_scripts();
					return $output;
				}
			}
		} /* end parallax_shortcode */
		/**
		 * Initialize the parallax.
		 *
		 * @since ----
		 * @access public
		 */
		public function parallax_init() {
			$group_name    = 'Background';
			$group_effects = 'Effect';
			if ( function_exists( 'vc_remove_param' ) ) {
				vc_remove_param( 'vc_row', 'bg_image_repeat' );
			}

			$pluginname = dirname( dirname( plugin_basename( __FILE__ ) ) );

			$patterns_path = realpath( dirname( plugin_dir_path( __FILE__ ) ) . '/assets/images/patterns' );
			$patterns_list = glob( $patterns_path . '/*.*' );
			$patterns      = array();

			foreach ( $patterns_list as $pattern ) {
				$patterns[ basename( $pattern ) ] = UAVC_URL . '/assets/images/patterns/' . basename( $pattern );
			}
			if ( function_exists( 'vc_add_param' ) ) {
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'dropdown',
						'class'       => '',
						'admin_label' => true,
						'heading'     => __( 'Background Style', 'ultimate_vc' ),
						'param_name'  => 'bg_type',
						'value'       => array(
							__( 'Default', 'ultimate_vc' ) => '',
							__( 'Single Color', 'ultimate_vc' ) => 'bg_color',
							__( 'Gradient Color', 'ultimate_vc' ) => 'grad',
							__( 'Image / Parallax', 'ultimate_vc' ) => 'image',
							__( 'YouTube Video', 'ultimate_vc' ) => 'u_iframe',
							__( 'Hosted Video', 'ultimate_vc' ) => 'video',
						),
						'description' => __( 'Select the kind of background would you like to set for this row.', 'ultimate_vc' ) . ' ' . __( 'Not sure?', 'ultimate_vc' ) . ' ' . __( 'See Narrated', 'ultimate_vc' ) . " <a href='https://www.youtube.com/watch?v=Qxs8R-uaMWk&list=PL1kzJGWGPrW981u5caHy6Kc9I1bG1POOx' target='_blank' rel='noopener'>" . __( 'Video Tutorials', 'ultimate_vc' ) . '</a>',
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'gradient',
						'class'       => '',
						'heading'     => __( 'Gradient Type', 'ultimate_vc' ),
						'param_name'  => 'bg_grad',
						'description' => __( 'At least two color points should be selected.', 'ultimate_vc' ) . ' <a href="https://www.youtube.com/watch?v=yE1M4AKwS44" target="_blank" rel="noopener">' . __( 'Video Tutorial', 'ultimate_vc' ) . '</a>',
						'dependency'  => array(
							'element' => 'bg_type',
							'value'   => array( 'grad' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'       => 'colorpicker',
						'class'      => '',
						'heading'    => __( 'Background Color', 'ultimate_vc' ),
						'param_name' => 'bg_color_value',
						'dependency' => array(
							'element' => 'bg_type',
							'value'   => array( 'bg_color' ),
						),
						'group'      => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'dropdown',
						'class'       => '',
						'heading'     => __( 'Parallax Style', 'ultimate_vc' ),
						'param_name'  => 'parallax_style',
						'value'       => array(
							__( 'No Parallax', 'ultimate_vc' ) => '',
							__( 'Simple Background Image', 'ultimate_vc' ) => 'vcpb-default',
							__( 'Auto Moving Background', 'ultimate_vc' ) => 'vcpb-animated',
							__( 'Vertical Parallax On Scroll', 'ultimate_vc' ) => 'vcpb-vz-jquery',
							__( 'Horizontal Parallax On Scroll', 'ultimate_vc' ) => 'vcpb-hz-jquery',
							__( 'Interactive Parallax On Mouse Hover', 'ultimate_vc' ) => 'vcpb-fs-jquery',
							__( 'Multilayer Vertical Parallax', 'ultimate_vc' ) => 'vcpb-mlvp-jquery',
						),
						'description' => __( 'Select the kind of style you like for the background.', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'bg_type',
							'value'   => array( 'image' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'ult_img_single',
						'class'       => '',
						'heading'     => __( 'Background Image', 'ultimate_vc' ),
						'param_name'  => 'bg_image_new',
						'value'       => '',
						'description' => __( 'Upload or select background image from media gallery.', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'parallax_style',
							'value'   => array( 'vcpb-default', 'vcpb-animated', 'vcpb-vz-jquery', 'vcpb-hz-jquery' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'attach_images',
						'class'       => '',
						'heading'     => __( 'Layer Images', 'ultimate_vc' ),
						'param_name'  => 'layer_image',
						'value'       => '',
						'description' => __( 'Upload or select background images from media gallery.', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'parallax_style',
							'value'   => array( 'vcpb-fs-jquery', 'vcpb-mlvp-jquery' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'dropdown',
						'class'       => '',
						'heading'     => __( 'Background Image Repeat', 'ultimate_vc' ),
						'param_name'  => 'bg_image_repeat',
						'value'       => array(
							__( 'Repeat', 'ultimate_vc' ) => '',
							__( 'Repeat X', 'ultimate_vc' ) => 'repeat-x',
							__( 'Repeat Y', 'ultimate_vc' ) => 'repeat-y',
							__( 'No Repeat', 'ultimate_vc' ) => 'no-repeat',
						),
						'description' => __( 'Options to control repeatation of the background image.', 'ultimate_vc' ) . ' ' . __( 'Learn on', 'ultimate_vc' ) . " <a href='http://www.w3schools.com/cssref/playit.asp?filename=playcss_background-repeat' target='_blank' rel='noopener'>" . __( 'W3School', 'ultimate_vc' ) . '</a>',
						'dependency'  => array(
							'element' => 'parallax_style',
							'value'   => array( 'vcpb-default', 'vcpb-fix', 'vcpb-vz-jquery', 'vcpb-hz-jquery' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'dropdown',
						'class'       => '',
						'heading'     => __( 'Background Image Size', 'ultimate_vc' ),
						'param_name'  => 'bg_image_size',
						'value'       => array(
							__( 'Cover - Image to be as large as possible', 'ultimate_vc' ) => '',
							__( 'Contain - Image will try to fit inside the container area', 'ultimate_vc' ) => 'contain',
							__( 'Initial', 'ultimate_vc' ) => 'initial',
						),
						'description' => __( 'Options to control repeatation of the background image.', 'ultimate_vc' ) . ' ' . __( 'Learn on', 'ultimate_vc' ) . " <a href='http://www.w3schools.com/cssref/playit.asp?filename=playcss_background-size&preval=50%25' target='_blank' rel='noopener'>" . __( 'W3School', 'ultimate_vc' ) . '</a>',
						'dependency'  => array(
							'element' => 'parallax_style',
							'value'   => array( 'vcpb-default', 'vcpb-animated', 'vcpb-fix', 'vcpb-vz-jquery', 'vcpb-hz-jquery' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'textfield',
						'class'       => '',
						'heading'     => __( 'Custom Background Image Size', 'ultimate_vc' ),
						'param_name'  => 'bg_cstm_size',
						'value'       => '',
						'description' => __( 'You can use initial, inherit or any number with px, em, %, etc. Example- 100px 100px', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'bg_image_size',
							'value'   => array( 'cstm' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'dropdown',
						'class'       => '',
						'heading'     => __( 'Scroll Effect', 'ultimate_vc' ),
						'param_name'  => 'bg_img_attach',
						'value'       => array(
							__( 'Move with the content', 'ultimate_vc' ) => '',
							__( 'Fixed at its position', 'ultimate_vc' ) => 'fixed',
						),
						'description' => __( 'Options to set whether a background image is fixed or scroll with the rest of the page.', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'parallax_style',
							'value'   => array( 'vcpb-default', 'vcpb-animated', 'vcpb-hz-jquery', 'vcpb-vz-jquery' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'number',
						'class'       => '',
						'heading'     => __( 'Parallax Speed', 'ultimate_vc' ),
						'param_name'  => 'parallax_sense',
						'value'       => '',
						'max'         => '100',
						'description' => __( 'Control speed of parallax. Enter value between 1 to 100 (Default 30)', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'parallax_style',
							'value'   => array( 'vcpb-vz-jquery', 'vcpb-animated', 'vcpb-hz-jquery', 'vcpb-vs-jquery', 'vcpb-hs-jquery', 'vcpb-fs-jquery', 'vcpb-mlvp-jquery' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'textfield',
						'class'       => '',
						'heading'     => __( 'Background Image Position', 'ultimate_vc' ),
						'param_name'  => 'bg_image_posiiton',
						'value'       => '',
						'description' => __( 'You can use any number with px, em, %, etc. Example- 100px 100px.', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'parallax_style',
							'value'   => array( 'vcpb-default', 'vcpb-fix' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'       => 'dropdown',
						'class'      => '',
						'heading'    => __( 'Animation Direction', 'ultimate_vc' ),
						'param_name' => 'animation_direction',
						'value'      => array(
							__( 'Left to Right', 'ultimate_vc' ) => '',
							__( 'Right to Left', 'ultimate_vc' ) => 'right-animation',
							__( 'Top to Bottom', 'ultimate_vc' ) => 'top-animation',
							__( 'Bottom to Top', 'ultimate_vc' ) => 'bottom-animation',

						),
						'dependency' => array(
							'element' => 'parallax_style',
							'value'   => array( 'vcpb-animated' ),
						),
						'group'      => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'       => 'dropdown',
						'class'      => '',
						'heading'    => __( 'Background Repeat', 'ultimate_vc' ),
						'param_name' => 'animation_repeat',
						'value'      => array(
							__( 'Repeat', 'ultimate_vc' ) => '',
							__( 'Repeat X', 'ultimate_vc' ) => 'repeat-x',
							__( 'Repeat Y', 'ultimate_vc' ) => 'repeat-y',
						),
						'dependency' => array(
							'element' => 'parallax_style',
							'value'   => array( 'vcpb-animated' ),
						),
						'group'      => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'       => 'textfield',
						'class'      => '',
						'heading'    => __( 'Link to the video in MP4 Format', 'ultimate_vc' ),
						'param_name' => 'video_url',
						'value'      => '',
						'dependency' => array(
							'element' => 'bg_type',
							'value'   => array( 'video' ),
						),
						'group'      => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'textfield',
						'class'       => '',
						'heading'     => __( 'Link to the video in WebM / Ogg Format', 'ultimate_vc' ),
						'param_name'  => 'video_url_2',
						'value'       => '',
						'description' => __( 'IE, Chrome & Safari', 'ultimate_vc' ) . " <a href='http://www.w3schools.com/html/html5_video.asp' target='_blank' rel='noopener'>" . __( 'support', 'ultimate_vc' ) . '</a> ' . __( 'MP4 format, while Firefox & Opera prefer WebM / Ogg formats.', 'ultimate_vc' ) . ' ' . __( 'You can upload the video through', 'ultimate_vc' ) . " <a href='" . home_url() . "/wp-admin/media-new.php' target='_blank' rel='noopener'>" . __( 'WordPress Media Library', 'ultimate_vc' ) . '</a>.',
						'dependency'  => array(
							'element' => 'bg_type',
							'value'   => array( 'video' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'textfield',
						'class'       => '',
						'heading'     => __( 'Enter YouTube URL of the Video', 'ultimate_vc' ),
						'param_name'  => 'u_video_url',
						'value'       => '',
						'description' => __( 'Enter YouTube url. Example - YouTube (https://www.youtube.com/watch?v=tSqJIIcxKZM) ', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'bg_type',
							'value'   => array( 'u_iframe' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'checkbox',
						'class'       => '',
						'heading'     => __( 'Extra Options', 'ultimate_vc' ),
						'param_name'  => 'video_opts',
						'value'       => array(
							__( 'Loop', 'ultimate_vc' )  => 'loop',
							__( 'Muted', 'ultimate_vc' ) => 'muted',
						),
						'description' => __( 'Muted autoplay is always allowed in chrome.', 'upb_parallax' ) . " <a href='https://developers.google.com/web/updates/2017/09/autoplay-policy-changes' target='_blank' rel='noopener'>" . __( 'Refer this link', 'upb_parallax' ) . '</a>.',
						'dependency'  => array(
							'element' => 'bg_type',
							'value'   => array( 'video', 'u_iframe' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'ult_img_single',
						'class'       => '',
						'heading'     => __( 'Placeholder Image', 'ultimate_vc' ),
						'param_name'  => 'video_poster',
						'value'       => '',
						'description' => __( 'Placeholder image is displayed in case background videos are restricted (Ex - on iOS devices).', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'bg_type',
							'value'   => array( 'video', 'u_iframe' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'       => 'number',
						'class'      => '',
						'heading'    => __( 'Start Time', 'ultimate_vc' ),
						'param_name' => 'u_start_time',
						'value'      => '',
						'suffix'     => 'seconds',
						'dependency' => array(
							'element' => 'bg_type',
							'value'   => array( 'u_iframe' ),
						),
						'group'      => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'number',
						'class'       => '',
						'heading'     => __( 'Stop Time', 'ultimate_vc' ),
						'param_name'  => 'u_stop_time',
						'value'       => '',
						'suffix'      => 'seconds',
						'description' => __( 'You may start / stop the video at any point you would like.', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'bg_type',
							'value'   => array( 'u_iframe' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'ult_switch',
						'class'       => '',
						'heading'     => __( 'Play video only when in viewport', 'ultimate_vc' ),
						'param_name'  => 'viewport_vdo',
						'value'       => '',
						'options'     => array(
							'viewport_play' => array(
								'label' => '',
								'on'    => __( 'Yes', 'ultimate_vc' ),
								'off'   => __( 'No', 'ultimate_vc' ),
							),
						),
						'description' => __( 'Video will be played only when user is on the particular screen position. Once user scroll away, the video will pause.', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'bg_type',
							'value'   => array( 'video', 'u_iframe' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'ult_switch',
						'class'       => '',
						'heading'     => __( 'Display Controls', 'ultimate_vc' ),
						'param_name'  => 'enable_controls',
						'value'       => '',
						'options'     => array(
							'display_control' => array(
								'label' => '',
								'on'    => __( 'Yes', 'ultimate_vc' ),
								'off'   => __( 'No', 'ultimate_vc' ),
							),
						),
						'description' => __( 'Display play / pause controls for the video on bottom right position.', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'bg_type',
							'value'   => array( 'video' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'colorpicker',
						'class'       => '',
						'heading'     => __( 'Color of Controls Icon', 'ultimate_vc' ),
						'param_name'  => 'controls_color',
						'description' => __( 'Display play / pause controls for the video on bottom right position.', 'upb_parallax' ),
						'dependency'  => array(
							'element' => 'enable_controls',
							'value'   => array( 'display_control' ),
						),
						'group'       => $group_name,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'dropdown',
						'class'       => '',
						'heading'     => __( 'Background Override (Read Description)', 'ultimate_vc' ),
						'param_name'  => 'bg_override',
						'value'       => array(
							__( 'Default Width', 'ultimate_vc' ) => '',
							__( "Apply 1st parent element's width", 'ultimate_vc' ) => '1',
							__( "Apply 2nd parent element's width", 'ultimate_vc' ) => '2',
							__( "Apply 3rd parent element's width", 'ultimate_vc' ) => '3',
							__( "Apply 4th parent element's width", 'ultimate_vc' ) => '4',
							__( "Apply 5th parent element's width", 'ultimate_vc' ) => '5',
							__( "Apply 6th parent element's width", 'ultimate_vc' ) => '6',
							__( "Apply 7th parent element's width", 'ultimate_vc' ) => '7',
							__( "Apply 8th parent element's width", 'ultimate_vc' ) => '8',
							__( "Apply 9th parent element's width", 'ultimate_vc' ) => '9',
							__( 'Full Width', 'ultimate_vc' ) => 'full',
							__( 'Maximum Full Width', 'ultimate_vc' ) => 'ex-full',
							__( 'Browser Full Dimension', 'ultimate_vc' ) => 'browser_size',
						),
						'description' => __( "By default, the background will be given to the WPBakery Page Builder row. However, in some cases depending on your theme's CSS - it may not fit well to the container you are wishing it would. In that case you will have to select the appropriate value here that gets you desired output..", 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'bg_type',
							'value'   => array( 'u_iframe', 'image', 'video', 'grad', 'bg_color', 'animated' ),
						),
						'group'       => $group_name,
					)
				);

				vc_add_param(
					'vc_row',
					array(
						'type'       => 'ult_switch',
						'class'      => '',
						'heading'    => __( 'Activate on Mobile', 'ultimate_vc' ),
						'param_name' => 'disable_on_mobile_img_parallax',
						'value'      => '',
						'options'    => array(
							'disable_on_mobile_img_parallax_value' => array(
								'label' => '',
								'on'    => __( 'Yes', 'ultimate_vc' ),
								'off'   => __( 'No', 'ultimate_vc' ),
							),
						),
						'group'      => $group_name,
						'dependency' => array(
							'element' => 'parallax_style',
							'value'   => array( 'vcpb-animated', 'vcpb-vz-jquery', 'vcpb-hz-jquery', 'vcpb-fs-jquery', 'vcpb-mlvp-jquery' ),
						),
					)
				);

				vc_add_param(
					'vc_row',
					array(
						'type'             => 'ult_switch',
						'class'            => '',
						'heading'          => __( 'Easy Parallax', 'ultimate_vc' ),
						'param_name'       => 'parallax_content',
						'value'            => '',
						'options'          => array(
							'parallax_content_value' => array(
								'label' => '',
								'on'    => __( 'Yes', 'ultimate_vc' ),
								'off'   => __( 'No', 'ultimate_vc' ),
							),
						),
						'group'            => $group_effects,
						'edit_field_class' => 'uvc-divider last-uvc-divider vc_column vc_col-sm-12',
						'description'      => __( 'If enabled, the elements inside row - will move slowly as user scrolls.', 'ultimate_vc' ),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'textfield',
						'class'       => '',
						'heading'     => __( 'Parallax Speed', 'ultimate_vc' ),
						'param_name'  => 'parallax_content_sense',
						'value'       => '',
						'group'       => $group_effects,
						'description' => __( 'Enter value between 0 to 100 (Default 30)', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'parallax_content',
							'value'   => array( 'parallax_content_value' ),
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'             => 'ult_switch',
						'class'            => '',
						'heading'          => __( 'Fade Effect on Scroll', 'ultimate_vc' ),
						'param_name'       => 'fadeout_row',
						'value'            => '',
						'options'          => array(
							'fadeout_row_value' => array(
								'label' => '',
								'on'    => __( 'Yes', 'ultimate_vc' ),
								'off'   => __( 'No', 'ultimate_vc' ),
							),
						),
						'group'            => $group_effects,
						'edit_field_class' => 'uvc-divider last-uvc-divider vc_column vc_col-sm-12',
						'description'      => __( 'If enabled, the the content inside row will fade out slowly as user scrolls down.', 'ultimate_vc' ),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'number',
						'class'       => '',
						'heading'     => __( 'Viewport Position', 'ultimate_vc' ),
						'param_name'  => 'fadeout_start_effect',
						'suffix'      => '%',
						'value'       => '',
						'group'       => $group_effects,
						'description' => __( 'The area of screen from top where fade out effect will take effect once the row is completely inside that area. (Default 30)', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'fadeout_row',
							'value'   => array( 'fadeout_row_value' ),
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'             => 'ult_switch',
						'heading'          => __( 'Enable Overlay', 'ultimate_vc' ),
						'param_name'       => 'enable_overlay',
						'value'            => '',
						'options'          => array(
							'enable_overlay_value' => array(
								'label' => '',
								'on'    => __( 'Yes', 'ultimate_vc' ),
								'off'   => __( 'No', 'ultimate_vc' ),
							),
						),
						'edit_field_class' => 'uvc-divider last-uvc-divider vc_column vc_col-sm-12',
						'group'            => $group_effects,
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'colorpicker',
						'heading'     => __( 'Color', 'ultimate_vc' ),
						'param_name'  => 'overlay_color',
						'value'       => '',
						'group'       => $group_effects,
						'dependency'  => array(
							'element' => 'enable_overlay',
							'value'   => array( 'enable_overlay_value' ),
						),
						'description' => __( 'Select RGBA values or opacity will be set to 20% by default.', 'ultimate_vc' ),
					)
				);

				vc_add_param(
					'vc_row',
					array(
						'type'       => 'radio_image_box',
						'heading'    => __( 'Pattern', 'ultimate_vc' ),
						'param_name' => 'overlay_pattern',
						'value'      => '',
						'options'    => $patterns,
						'css'        => array(
							'width'             => '40px',
							'height'            => '35px',
							'background-repeat' => 'repeat',
							'background-size'   => 'cover',
						),
						'group'      => $group_effects,
						'dependency' => array(
							'element' => 'enable_overlay',
							'value'   => array( 'enable_overlay_value' ),
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'             => 'number',
						'heading'          => __( 'Pattern Opacity', 'ultimate_vc' ),
						'param_name'       => 'overlay_pattern_opacity',
						'value'            => '',
						'max'              => '100',
						'suffix'           => '%',
						'group'            => $group_effects,
						'dependency'       => array(
							'element' => 'enable_overlay',
							'value'   => array( 'enable_overlay_value' ),
						),
						'description'      => __( 'Enter value between 0 to 100 (0 is maximum transparency, while 100 is minimum & default is 80)', 'ultimate_vc' ),
						'edit_field_class' => 'vc_column vc_col-sm-4',
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'             => 'number',
						'heading'          => __( 'Pattern Size', 'ultimate_vc' ),
						'param_name'       => 'overlay_pattern_size',
						'value'            => '',
						'suffix'           => 'px',
						'group'            => $group_effects,
						'dependency'       => array(
							'element' => 'enable_overlay',
							'value'   => array( 'enable_overlay_value' ),
						),
						'description'      => __( 'This is optional; sets the size of the pattern image manually.', 'ultimate_vc' ),
						'edit_field_class' => 'vc_column vc_col-sm-4',
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'             => 'dropdown',
						'heading'          => __( 'Pattern Scroll Effect', 'ultimate_vc' ),
						'param_name'       => 'overlay_pattern_attachment',
						'value'            => array(
							__( 'Move with the Content', 'ultimate_vc' ) => '',
							__( 'Fixed at its position', 'ultimate_vc' ) => 'fixed',
						),
						'group'            => $group_effects,
						'dependency'       => array(
							'element' => 'enable_overlay',
							'value'   => array( 'enable_overlay_value' ),
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
					)
				);

				vc_add_param(
					'vc_row',
					array(
						'type'             => 'checkbox',
						'heading'          => __( 'Fancy Multi Color Overlay', 'ultimate_vc' ),
						'param_name'       => 'multi_color_overlay',
						'value'            => array(
							__( 'Enable', 'ultimate_vc' ) => 'uvc-multi-color-bg',
						),
						'group'            => $group_effects,
						'dependency'       => array(
							'element' => 'enable_overlay',
							'value'   => array( 'enable_overlay_value' ),
						),
						'edit_field_class' => 'vc_column vc_col-sm-4 clear',
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'             => 'number',
						'heading'          => __( 'Multi Color Overlay Opacity', 'ultimate_vc' ),
						'param_name'       => 'multi_color_overlay_opacity',
						'value'            => '',
						'suffix'           => '%',
						'group'            => $group_effects,
						'dependency'       => array(
							'element' => 'multi_color_overlay',
							'value'   => array( 'uvc-multi-color-bg' ),
						),
						'edit_field_class' => 'vc_column vc_col-sm-8',
						'description'      => __( 'Default 60', 'ultimate_vc' ),
					)
				);

				vc_add_param(
					'vc_row',
					array(
						'type'             => 'ult_switch',
						'heading'          => __( 'Separator ', 'ultimate_vc' ),
						'param_name'       => 'seperator_enable',
						'value'            => '',
						'options'          => array(
							'seperator_enable_value' => array(
								'on'  => __( 'Yes', 'ultimate_vc' ),
								'off' => __( 'No', 'ultimate_vc' ),
							),
						),
						'edit_field_class' => 'uvc-divider last-uvc-divider vc_column vc_col-sm-12',
						'group'            => $group_effects,
					)
				);

				vc_add_param(
					'vc_row',
					array(
						'type'             => 'dropdown',
						'heading'          => __( 'Type', 'ultimate_vc' ),
						'param_name'       => 'seperator_type',
						'value'            => array(
							__( 'None', 'ultimate_vc' )   => '',
							__( 'Triangle', 'ultimate_vc' ) => 'triangle_svg_seperator',
							__( 'Big Triangle', 'ultimate_vc' ) => 'xlarge_triangle_seperator',
							__( 'Big Triangle Left', 'ultimate_vc' ) => 'xlarge_triangle_left_seperator',
							__( 'Big Triangle Right', 'ultimate_vc' ) => 'xlarge_triangle_right_seperator',
							__( 'Half Circle', 'ultimate_vc' ) => 'circle_svg_seperator',
							__( 'Curve Center', 'ultimate_vc' ) => 'xlarge_circle_seperator',
							__( 'Curve Left', 'ultimate_vc' ) => 'curve_up_seperator',
							__( 'Curve Right', 'ultimate_vc' ) => 'curve_down_seperator',
							__( 'Tilt Left', 'ultimate_vc' ) => 'tilt_left_seperator',
							__( 'Tilt Right', 'ultimate_vc' ) => 'tilt_right_seperator',
							__( 'Round Split', 'ultimate_vc' ) => 'round_split_seperator',
							__( 'Waves', 'ultimate_vc' )  => 'waves_seperator',
							__( 'Clouds', 'ultimate_vc' ) => 'clouds_seperator',
							__( 'Multi Triangle', 'ultimate_vc' ) => 'multi_triangle_seperator',
						),
						'group'            => $group_effects,
						'dependency'       => array(
							'element' => 'seperator_enable',
							'value'   => array( 'seperator_enable_value' ),
						),
						'edit_field_class' => 'uvc-divider-content-first vc_column vc_col-sm-12',
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'             => 'dropdown',
						'heading'          => __( 'Position', 'ultimate_vc' ),
						'param_name'       => 'seperator_position',
						'value'            => array(
							__( 'Top', 'ultimate_vc' )    => '',
							__( 'Bottom', 'ultimate_vc' ) => 'bottom_seperator',
							__( 'Top & Bottom', 'ultimate_vc' ) => 'top_bottom_seperator',
						),
						'group'            => $group_effects,
						'dependency'       => array(
							'element' => 'seperator_enable',
							'value'   => array( 'seperator_enable_value' ),
						),
						'edit_field_class' => 'uvc-divider-content-first vc_column vc_col-sm-12',
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'number',
						'heading'     => __( 'Size', 'ultimate_vc' ),
						'param_name'  => 'seperator_shape_size',
						'value'       => '',
						'suffix'      => 'px',
						'group'       => $group_effects,
						'dependency'  => array(
							'element' => 'seperator_type',
							'value'   => array( 'triangle_seperator', 'circle_seperator', 'round_split_seperator' ),
						),
						'description' => __( 'Default 40', 'ultimate_vc' ),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'number',
						'heading'     => __( 'Height', 'ultimate_vc' ),
						'param_name'  => 'seperator_svg_height',
						'value'       => '',
						'suffix'      => 'px',
						'group'       => $group_effects,
						'dependency'  => array(
							'element' => 'seperator_type',
							'value'   => array( 'xlarge_triangle_seperator', 'curve_up_seperator', 'curve_down_seperator', 'waves_seperator', 'clouds_seperator', 'xlarge_circle_seperator', 'triangle_svg_seperator', 'circle_svg_seperator', 'xlarge_triangle_left_seperator', 'xlarge_triangle_right_seperator', 'tilt_left_seperator', 'tilt_right_seperator', 'multi_triangle_seperator' ),
						),
						'description' => __( 'Default 60', 'ultimate_vc' ),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'colorpicker',
						'heading'     => __( 'Background', 'ultimate_vc' ),
						'param_name'  => 'seperator_shape_background',
						'value'       => '',
						'group'       => $group_effects,
						'dependency'  => array(
							'element' => 'seperator_type',
							'value'   => array( 'xlarge_triangle_seperator', 'triangle_seperator', 'circle_seperator', 'curve_up_seperator', 'curve_down_seperator', 'round_split_seperator', 'waves_seperator', 'clouds_seperator', 'xlarge_circle_seperator', 'triangle_svg_seperator', 'circle_svg_seperator', 'xlarge_triangle_left_seperator', 'xlarge_triangle_right_seperator', 'tilt_left_seperator', 'tilt_right_seperator', 'multi_triangle_seperator' ),
						),
						'description' => __( 'Mostly, this should be background color of your adjacent row section. (Default - White)', 'ultimate_vc' ),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'       => 'dropdown',
						'heading'    => __( 'Border', 'ultimate_vc' ),
						'param_name' => 'seperator_shape_border',
						'value'      => array(
							__( 'None', 'ultimate_vc' )   => '',
							__( 'Solid', 'ultimate_vc' )  => 'solid',
							__( 'Dotted', 'ultimate_vc' ) => 'dotted',
							__( 'Dashed', 'ultimate_vc' ) => 'dashed',
						),
						'group'      => $group_effects,
						'dependency' => array(
							'element' => 'seperator_type',
							'value'   => array( 'triangle_seperator', 'circle_seperator', 'round_split_seperator' ),
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'       => 'colorpicker',
						'heading'    => __( 'Border Color', 'ultimate_vc' ),
						'param_name' => 'seperator_shape_border_color',
						'value'      => '',
						'group'      => $group_effects,
						'dependency' => array(
							'element' => 'seperator_type',
							'value'   => array( 'triangle_seperator', 'circle_seperator', 'round_split_seperator' ),
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'             => 'number',
						'heading'          => __( 'Border Width', 'ultimate_vc' ),
						'param_name'       => 'seperator_shape_border_width',
						'value'            => '',
						'suffix'           => 'px',
						'group'            => $group_effects,
						'dependency'       => array(
							'element' => 'seperator_type',
							'value'   => array( 'triangle_seperator', 'circle_seperator', 'round_split_seperator' ),
						),
						'edit_field_class' => 'uvc-divider-content-last vc_column vc_col-sm-12',
						'description'      => __( 'Default 1px', 'ultimate_vc' ),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'dropdown',
						'class'       => '',
						'heading'     => __( 'Icon to display:', 'ultimate_vc' ),
						'param_name'  => 'icon_type',
						'value'       => array(
							__( 'None', 'ultimate_vc' ) => '',
							__( 'Font Icon Manager', 'ultimate_vc' ) => 'selector',
							__( 'Custom Image Icon', 'ultimate_vc' ) => 'custom',
						),
						'group'       => $group_effects,
						'description' => __( 'Use an existing font icon or upload a custom image.', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'seperator_enable',
							'value'   => array( 'seperator_enable_value' ),
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'icon_manager',
						'class'       => '',
						'heading'     => __( 'Select Icon ', 'ultimate_vc' ),
						'param_name'  => 'icon',
						'value'       => '',
						'group'       => $group_effects,
						'description' => __( "Click and select icon of your choice. If you can't find the one that suits for your purpose", 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-font-icon-manager' target='_blank' rel='noopener'>" . __( 'add new here', 'ultimate_vc' ) . '</a>.',
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => array( 'selector' ),
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'number',
						'class'       => '',
						'heading'     => __( 'Size of Icon', 'ultimate_vc' ),
						'param_name'  => 'icon_size',
						'value'       => '',
						'max'         => 72,
						'suffix'      => 'px',
						'group'       => $group_effects,
						'description' => __( 'How big would you like it? (Default 32)', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => array( 'selector' ),
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'colorpicker',
						'class'       => '',
						'heading'     => __( 'Color', 'ultimate_vc' ),
						'param_name'  => 'icon_color',
						'value'       => '',
						'group'       => $group_effects,
						'description' => __( 'Give it a nice paint!', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => array( 'selector' ),
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'dropdown',
						'class'       => '',
						'heading'     => __( 'Icon Style', 'ultimate_vc' ),
						'param_name'  => 'icon_style',
						'value'       => array(
							__( 'Simple', 'ultimate_vc' ) => '',
							__( 'Circle Background', 'ultimate_vc' ) => 'circle',
							__( 'Square Background', 'ultimate_vc' ) => 'square',
							__( 'Design your own', 'ultimate_vc' ) => 'advanced',
						),
						'group'       => $group_effects,
						'description' => __( 'We have given three quick preset if you are in a hurry. Otherwise, create your own with various options.', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => array( 'selector' ),
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'colorpicker',
						'class'       => '',
						'heading'     => __( 'Background Color', 'ultimate_vc' ),
						'param_name'  => 'icon_color_bg',
						'value'       => '',
						'group'       => $group_effects,
						'description' => __( 'Select background color for icon.', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'icon_style',
							'value'   => array( 'circle', 'square', 'advanced' ),
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'dropdown',
						'class'       => '',
						'heading'     => __( 'Icon Border Style', 'ultimate_vc' ),
						'param_name'  => 'icon_border_style',
						'value'       => array(
							__( 'None', 'ultimate_vc' )   => '',
							__( 'Solid', 'ultimate_vc' )  => 'solid',
							__( 'Dashed', 'ultimate_vc' ) => 'dashed',
							__( 'Dotted', 'ultimate_vc' ) => 'dotted',
							__( 'Double', 'ultimate_vc' ) => 'double',
							__( 'Inset', 'ultimate_vc' )  => 'inset',
							__( 'Outset', 'ultimate_vc' ) => 'outset',
						),
						'group'       => $group_effects,
						'description' => __( 'Select the border style for icon.', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'icon_style',
							'value'   => array( 'advanced' ),
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'colorpicker',
						'class'       => '',
						'heading'     => __( 'Border Color', 'ultimate_vc' ),
						'param_name'  => 'icon_color_border',
						'value'       => '',
						'group'       => $group_effects,
						'description' => __( 'Select border color for icon. (Default - #333333)', 'ultimate_vc' ),
						'dependency'  => array(
							'element'   => 'icon_border_style',
							'not_empty' => true,
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'number',
						'class'       => '',
						'heading'     => __( 'Border Width', 'ultimate_vc' ),
						'param_name'  => 'icon_border_size',
						'value'       => '',
						'max'         => 10,
						'suffix'      => 'px',
						'group'       => $group_effects,
						'description' => __( 'Thickness of the border. (Default - 1px)', 'ultimate_vc' ),
						'dependency'  => array(
							'element'   => 'icon_border_style',
							'not_empty' => true,
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'number',
						'class'       => '',
						'heading'     => __( 'Border Radius', 'ultimate_vc' ),
						'param_name'  => 'icon_border_radius',
						'value'       => '',
						'max'         => 500,
						'suffix'      => 'px',
						'group'       => $group_effects,
						'description' => __( '0 pixel value will create a square border. As you increase the value, the shape convert in circle slowly. (Default 500).', 'ultimate_vc' ),
						'dependency'  => array(
							'element'   => 'icon_border_style',
							'not_empty' => true,
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'number',
						'class'       => '',
						'heading'     => __( 'Background Size', 'ultimate_vc' ),
						'param_name'  => 'icon_border_spacing',
						'value'       => '',
						'max'         => 500,
						'suffix'      => 'px',
						'group'       => $group_effects,
						'description' => __( 'Spacing from center of the icon till the boundary of border / background (Default - 50)', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'icon_style',
							'value'   => array( 'advanced' ),
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'ult_img_single',
						'class'       => '',
						'heading'     => __( 'Upload Image Icon:', 'ultimate_vc' ),
						'param_name'  => 'icon_img',
						'value'       => '',
						'group'       => $group_effects,
						'description' => __( 'Upload the custom image icon.', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => array( 'custom' ),
						),
					)
				);
				vc_add_param(
					'vc_row',
					array(
						'type'        => 'number',
						'class'       => '',
						'heading'     => __( 'Image Width', 'ultimate_vc' ),
						'param_name'  => 'img_width',
						'value'       => '',
						'max'         => 512,
						'suffix'      => 'px',
						'group'       => $group_effects,
						'description' => __( 'Provide image width (Default - 48)', 'ultimate_vc' ),
						'dependency'  => array(
							'element' => 'icon_type',
							'value'   => array( 'custom' ),
						),
					)
				);

				vc_add_param(
					'vc_row',
					array(
						'type'             => 'ult_switch',
						'heading'          => __( 'Hide Row', 'ultimate_vc' ),
						'param_name'       => 'ult_hide_row',
						'value'            => '',
						'options'          => array(
							'ult_hide_row_value' => array(
								'on'  => __( 'Yes', 'ultimate_vc' ),
								'off' => __( 'No', 'ultimate_vc' ),
							),
						),
						'edit_field_class' => 'uvc-divider last-uvc-divider vc_column vc_col-sm-12',
						'group'            => $group_effects,
					)
				);

				vc_add_param(
					'vc_row',
					array(
						'type'             => 'ult_switch',
						'heading'          => '<i class="dashicons dashicons-welcome-view-site"></i> ' . __( 'Large Screen', 'ultimate_vc' ),
						'param_name'       => 'ult_hide_row_large_screen',
						'value'            => '',
						'options'          => array(
							'large_screen' => array(
								'on'  => __( 'Yes', 'ultimate_vc' ),
								'off' => __( 'No', 'ultimate_vc' ),
							),
						),
						'group'            => $group_effects,
						'dependency'       => array(
							'element' => 'ult_hide_row',
							'value'   => array( 'ult_hide_row_value' ),
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
					)
				);

				vc_add_param(
					'vc_row',
					array(
						'type'             => 'ult_switch',
						'heading'          => '<i class="dashicons dashicons-desktop"></i> ' . __( 'Desktop', 'ultimate_vc' ),
						'param_name'       => 'ult_hide_row_desktop',
						'value'            => '',
						'options'          => array(
							'desktop' => array(
								'on'  => __( 'Yes', 'ultimate_vc' ),
								'off' => __( 'No', 'ultimate_vc' ),
							),
						),
						'group'            => $group_effects,
						'dependency'       => array(
							'element' => 'ult_hide_row',
							'value'   => array( 'ult_hide_row_value' ),
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
					)
				);

				vc_add_param(
					'vc_row',
					array(
						'type'             => 'ult_switch',
						'heading'          => '<i class="dashicons dashicons-tablet" style="transform: rotate(90deg);"></i> ' . __( 'Tablet', 'ultimate_vc' ),
						'param_name'       => 'ult_hide_row_tablet',
						'value'            => '',
						'options'          => array(
							'tablet' => array(
								'on'  => __( 'Yes', 'ultimate_vc' ),
								'off' => __( 'No', 'ultimate_vc' ),
							),
						),
						'group'            => $group_effects,
						'dependency'       => array(
							'element' => 'ult_hide_row',
							'value'   => array( 'ult_hide_row_value' ),
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
					)
				);

				vc_add_param(
					'vc_row',
					array(
						'type'             => 'ult_switch',
						'heading'          => '<i class="dashicons dashicons-tablet"></i> ' . __( 'Tablet Portrait', 'ultimate_vc' ),
						'param_name'       => 'ult_hide_row_tablet_small',
						'value'            => '',
						'options'          => array(
							'xs_tablet' => array(
								'on'  => __( 'Yes', 'ultimate_vc' ),
								'off' => __( 'No', 'ultimate_vc' ),
							),
						),
						'group'            => $group_effects,
						'dependency'       => array(
							'element' => 'ult_hide_row',
							'value'   => array( 'ult_hide_row_value' ),
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
					)
				);

				vc_add_param(
					'vc_row',
					array(
						'type'             => 'ult_switch',
						'heading'          => '<i class="dashicons dashicons-smartphone"></i> ' . __( 'Mobile', 'ultimate_vc' ),
						'param_name'       => 'ult_hide_row_mobile',
						'value'            => '',
						'options'          => array(
							'mobile' => array(
								'on'  => __( 'Yes', 'ultimate_vc' ),
								'off' => __( 'No', 'ultimate_vc' ),
							),
						),
						'group'            => $group_effects,
						'dependency'       => array(
							'element' => 'ult_hide_row',
							'value'   => array( 'ult_hide_row_value' ),
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
					)
				);

				vc_add_param(
					'vc_row',
					array(
						'type'             => 'ult_switch',
						'heading'          => '<i class="dashicons dashicons-smartphone" style="transform: rotate(90deg);"></i> ' . __( 'Mobile Landscape', 'ultimate_vc' ),
						'param_name'       => 'ult_hide_row_mobile_large',
						'value'            => '',
						'options'          => array(
							'xl_mobile' => array(
								'on'  => __( 'Yes', 'ultimate_vc' ),
								'off' => __( 'No', 'ultimate_vc' ),
							),
						),
						'group'            => $group_effects,
						'dependency'       => array(
							'element' => 'ult_hide_row',
							'value'   => array( 'ult_hide_row_value' ),
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
					)
				);

				vc_add_param(
					'vc_row',
					array(
						'type'             => 'ult_param_heading',
						'text'             => __( 'In order for Effects below to work, you must select something except "default" in background tab.', 'ultimate_vc' ) . ' ' . __( 'May be single color.' ) . ' <br> ' . __( 'Screenshot', 'ultimate_vc' ) . ' - <a href="https://cloudup.com/cc1J8ZlcdZW" target="_blank" rel="noopener">https://cloudup.com/cc1J8ZlcdZW</a>',
						'param_name'       => 'notification',
						'edit_field_class' => 'ult-param-important-wrapper ult-dashicon vc_column vc_col-sm-12',
						'group'            => $group_effects,
					)
				);
			}
		} /* parallax_init*/
		/**
		 * Admin Scripts.
		 *
		 * @since ----
		 * @param mixed $hook hooks.
		 * @access public
		 */
		public function admin_scripts( $hook ) {
			if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
				wp_register_script( 'ultimate-vc-addons-colorpicker', UAVC_URL . 'admin/js/jquery-colorpicker.js ', array( 'jquery' ), ULTIMATE_VERSION, false );
				wp_register_script( 'ultimate-vc-addons-classygradient', UAVC_URL . 'admin/js/jquery-classygradient-min.js', array( 'jquery' ), ULTIMATE_VERSION, false );

				Ultimate_VC_Addons::ultimate_register_style( 'ultimate-vc-addons-classycolorpicker-style', UAVC_URL . 'admin/css/jquery-colorpicker.css', true );

				Ultimate_VC_Addons::ultimate_register_style( 'ultimate-vc-addons-classygradient-style', UAVC_URL . 'admin/css/jquery-classygradient-min.css', true );

				$bsf_dev_mode = bsf_get_option( 'dev_mode' );
				if ( 'enable' === $bsf_dev_mode ) {
					wp_enqueue_script( 'ultimate-vc-addons-colorpicker' );
					wp_enqueue_script( 'ultimate-vc-addons-classygradient' );

					wp_enqueue_style( 'ultimate-vc-addons-classycolorpicker-style' );
					wp_enqueue_style( 'ultimate-vc-addons-classygradient-style' );
				}
			}
		}//end admin_scripts()
		/**
		 * Initialize Font scrips.
		 *
		 * @since ----
		 * @access public
		 */
		public static function front_scripts() {
			$bsf_dev_mode = bsf_get_option( 'dev_mode' );
			if ( 'enable' === $bsf_dev_mode ) {
				$js_path  = '../assets/js/';
				$css_path = '../assets/css/';
				$ext      = '';
			} else {
				$js_path  = '../assets/min-js/';
				$css_path = '../assets/min-css/';
				$ext      = '.min';
			}
			$ultimate_css = get_option( 'ultimate_css' );
			if ( 'enable' != $ultimate_css || 'enable' === $bsf_dev_mode ) {
				Ultimate_VC_Addons::ultimate_register_style( 'ultimate-vc-addons-background-style', 'background-style' );
				wp_enqueue_style( 'ultimate-vc-addons-background-style' );
			}
		} /* end front_scripts */
	}
	new Ultimate_VC_Addons_Parallax();
}
$ultimate_row = get_option( 'ultimate_row' );
if ( ( defined( 'WPB_VC_VERSION' ) && ( ! version_compare( WPB_VC_VERSION, '4.4', '>=' ) ) ) && 'enable' == $ultimate_row ) {
	if ( ! function_exists( 'vc_theme_after_vc_row' ) ) {
		/**
		 * For the animation in the module
		 *
		 * @since ----
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		function vc_theme_after_vc_row( $atts, $content = null ) {
			return Ultimate_VC_Addons_Parallax::parallax_shortcode( $atts, $content );
		}
	}
}
if ( ! function_exists( 'hex2rgbUltParallax' ) ) {
	/**
	 * Hex to Rgb
	 *
	 * @since ----
	 * @param mixed $hex hex number.
	 * @param mixed $opacity color opacity.
	 * @access public
	 */
	function hex2rgbUltParallax( $hex, $opacity ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
		$hex = str_replace( '#', '', $hex );
		if ( preg_match( '/^([a-f0-9]{3}|[a-f0-9]{6})$/i', $hex ) ) :      // check if input string is a valid hex colour code.
			if ( strlen( $hex ) == 3 ) { // three letters code.
				$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
				$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
				$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
			} else { // six letters coode.
				$r = hexdec( substr( $hex, 0, 2 ) );
				$g = hexdec( substr( $hex, 2, 2 ) );
				$b = hexdec( substr( $hex, 4, 2 ) );
			}
			return 'rgba(' . implode( ',', array( $r, $g, $b ) ) . ',' . $opacity . ')';// returns the rgb values separated by commas, ready for usage in a rgba( rr,gg,bb,aa ) CSS rule.
		else :
			return '';  // input string is not a valid hex color code - return a blank value; this can be changed to return a default colour code for example.
		endif;
	}
}
if ( ! function_exists( 'rgbaToHexUltimate' ) ) {
	/**
	 * Rgba to Hex
	 *
	 * @since ----
	 * @param mixed $r red.
	 * @param mixed $g green.
	 * @param mixed $b blue.
	 * @access public
	 */
	function rgbaToHexUltimate( $r, $g, $b ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
		$hex  = '#';
		$hex .= str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT );
		$hex .= str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT );
		$hex .= str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );
		return $hex;
	}
}

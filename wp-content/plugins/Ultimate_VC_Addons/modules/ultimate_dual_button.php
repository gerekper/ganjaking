<?php
/**
 *  UAVC Ultimate Dual Button module file
 *
 *  @package Ultimate Dual Button
 */

if ( ! class_exists( 'AIO_Dual_Button' ) ) {
	/**
	 * Function that initializes Ultimate Dual Button Module
	 *
	 * @class AIO_Dual_Button
	 */
	class AIO_Dual_Button {
		/**
		 * Constructor function that constructs default values for the Ultimate Animation module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'ultimate_dual_button' ) );
			}
			add_shortcode( 'ult_dualbutton', array( $this, 'ultimate_dualbtn_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'dualbutton_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'dualbutton_backend_scripts' ) );
		}
		/**
		 * Function for button admin script
		 *
		 * @since ----
		 * @param mixed $hook for the script.
		 * @access public
		 */
		public function dualbutton_backend_scripts( $hook ) {
			if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
				$bsf_dev_mode = bsf_get_option( 'dev_mode' );
				if ( 'enable' === $bsf_dev_mode ) {
					wp_register_script( 'jquery_dualbtn_new', UAVC_URL . 'admin/js/dualbtnbackend.js', array( 'jquery' ), ULTIMATE_VERSION, false );
					wp_enqueue_script( 'jquery_dualbtn_new' );
				}
			}
		}
		/**
		 * Function for enque script.
		 *
		 * @since ----
		 * @access public
		 */
		public function dualbutton_scripts() {

			Ultimate_VC_Addons::ultimate_register_style( 'ult-dualbutton', 'dual-button' );

			Ultimate_VC_Addons::ultimate_register_script( 'jquery.dualbtn', 'dual-button', false, array( 'jquery' ), ULTIMATE_VERSION, false );

			if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
				$params = wp_parse_url( $_SERVER['HTTP_REFERER'] );

				$vc_is_inline = false;
				if ( isset( $params['query'] ) ) {
					parse_str( $params['query'], $params );
					$vc_is_inline = isset( $params['vc_action'] ) ? true : false;
				}

				if ( $vc_is_inline ) {
					Ultimate_VC_Addons::ultimate_register_style( 'ult-dualbutton', 'dual-button' );
					wp_enqueue_style( 'ult-dualbutton' );
					Ultimate_VC_Addons::ultimate_register_script( 'jquery.dualbtn', 'dual-button', false, array( 'jquery' ), ULTIMATE_VERSION, false );
					wp_enqueue_script( 'jquery.dualbtn' );
				}
			}
		}

		/**
		 * Function for button module shortcode
		 *
		 * @since ----
		 * @param array $atts represts module attribuits.
		 * @access public
		 */
		public function ultimate_dualbtn_shortcode( $atts ) {

			$target1     = '';
			$link_title1 = '';
			$rel1        = '';
			$target2     = '';
			$link_title2 = '';
			$rel2        = '';

			$ult_db_settings = shortcode_atts(
				array(

					/*--------btn1-----------*/
						'button1_text'           => '',
					'icon_type'                  => 'selector',
					'icon'                       => '',
					'icon_img'                   => '',
					'img_width'                  => '',
					'icon_size'                  => '32',
					'icon_color'                 => '#333333',
					'icon_hover_color'           => '#333333',
					'icon_style'                 => 'none',
					'icon_color_bg'              => '#ffffff',
					'icon_border_style'          => 'solid',
					'icon_color_border'          => '#333333',
					'icon_border_size'           => '1',
					'icon_border_radius'         => '0',
					'icon_border_spacing'        => '30',
					'icon_link'                  => '',
					'icon_align'                 => 'left',
					'btn1_background_color'      => '#ffffff',
					'btn1_bghovercolor'          => '#bcbcbc',
					'btn1_font_family'           => '',
					'btn1_heading_style'         => '',
					'btn1_text_color'            => '#333333',
					'btn1_text_hovercolor'       => '#333333',
					'icon_color_hoverbg'         => '#ecf0f1',
					'icon_color_hoverborder'     => '#333333',
					'btn1_padding'               => '',

					/*--------btn2-----------*/
					'button2_text'               => '',
					'btn_icon_type'              => 'selector',
					'btn_icon'                   => '',
					'btn_icon_img'               => '',
					'btn_img_width'              => '48',
					'btn_icon_size'              => '32',
					'btn_icon_color'             => '#333333',
					'btn_iconhover_color'        => '#333333',
					'btn_icon_style'             => 'none',
					'btn_icon_color_bg'          => '#ffffff',
					'icon_color_bg'              => '#ffffff',
					'btn_icon_border_style'      => '',
					'btn_icon_color_border'      => '#333333',
					'btn_icon_border_size'       => '1',
					'btn_icon_border_radius'     => '1',
					'btn_icon_border_spacing'    => '30',
					'btn_icon_link'              => '',
					'btn2_icon_align'            => 'right',
					'btn2_background_color'      => '#ffffff',
					'btn2_bghovercolor'          => '#bcbcbc',
					'btn2_font_family'           => '',
					'btn2_heading_style'         => '',
					'btn2_text_color'            => '#333333',
					'btn2_text_hovercolor'       => '#333333',
					'btn_icon_color_hoverbg'     => '#ffffff',
					'btn_icon_color_hoverborder' => '#333333',
					'btn2_padding'               => '',

					/*--------divider-----------*/

					'divider_style'              => 'text',
					'divider_text'               => 'or',
					'divider_text_color'         => '#ffffff',
					'divider_bg_color'           => '#333333',
					'divider_icon'               => '',
					'divider_icon_img'           => '',
					'divider_border_radius'      => '',
					'divider_border_size'        => '1',
					'divider_color_border'       => '#e7e7e7',
					'divider_border_style'       => '',

					/*--------general-----------*/

					'btn_border_style'           => '',
					'btn_color_border'           => '#333333',
					'btn_border_size'            => '1',
					'btn_border_radius'          => '',
					'btn_hover_style'            => 'Style 1',
					'title_font_size'            => '15',
					'title_line_ht'              => '15',
					'el_class'                   => '',
					'btn_alignment'              => 'center',
					'btn_width'                  => '',
					'dual_resp'                  => 'on',
					'css_dualbtn_design'         => '',

				),
				$atts
			);

			$vc_version    = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
			$is_vc_49_plus = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'ult-adjust-bottom-margin' : '';

			$extraclass            = $ult_db_settings['el_class'];
			$el_class1             = '';
			$css_trans             = '';
			$button2_bstyle        = '';
			$button1_bstyle        = '';
			$btn_color_hoverborder = '';
			$iconoutput            = '';
			$style                 = '';
			$link_sufix            = '';
			$link_prefix           = '';
			$target                = '';
			$href                  = '';
			$icon_align_style      = '';
			$secicon               = '';
			$style1                = '';
			$dual_design_style_css = '';
			$url1                  = '';
			$target1               = '';
			$link_title1           = '';
			$rel1                  = '';
			$url2                  = '';
			$link_title2           = '';
			$rel2                  = '';
			$target2               = '';

			$dual_design_style_css = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_db_settings['css_dualbtn_design'], ' ' ), 'ult_dualbutton', $atts );
			$dual_design_style_css = esc_attr( $dual_design_style_css );

			if ( '' !== $ult_db_settings['icon_link'] ) {
				$href2 = vc_build_link( $ult_db_settings['icon_link'] );

				$url1        = ( isset( $href2['url'] ) && '' !== $href2['url'] ) ? $href2['url'] : '';
				$target1     = ( isset( $href2['target'] ) && '' !== $href2['target'] ) ? esc_attr( trim( $href2['target'] ) ) : '';
				$link_title1 = ( isset( $href2['title'] ) && '' !== $href2['title'] ) ? esc_attr( $href2['title'] ) : '';
				$rel1        = ( isset( $href2['rel'] ) && '' !== $href2['rel'] ) ? esc_attr( $href2['rel'] ) : '';

				if ( '' == $url1 ) {
					$url1 = 'javascript:void(0);';
				}
			} else {
				$url1 = 'javascript:void(0);';
			}

			if ( 'custom' == $ult_db_settings['icon_type'] ) {
				if ( '' !== $ult_db_settings['icon_img'] ) {
					$img = apply_filters( 'ult_get_img_single', $ult_db_settings['icon_img'], 'url' );
					$alt = apply_filters( 'ult_get_img_single', $ult_db_settings['icon_img'], 'alt' );
					if ( 'none' !== $ult_db_settings['icon_style'] ) {
						if ( '' !== $ult_db_settings['icon_color_bg'] ) {
							$style .= 'background:' . $ult_db_settings['icon_color_bg'] . ';';
						}
					}
					if ( 'circle' == $ult_db_settings['icon_style'] ) {
						$ult_db_settings['el_class'] .= ' uavc-circle ';
					}
					if ( 'square' == $ult_db_settings['icon_style'] ) {
						$ult_db_settings['el_class'] .= ' uavc-square ';
					}
					if ( 'advanced' == $ult_db_settings['icon_style'] && '' !== $ult_db_settings['icon_border_style'] ) {
						$style .= 'border-style:' . $ult_db_settings['icon_border_style'] . ';';
						$style .= 'border-color:' . $ult_db_settings['icon_color_border'] . ';';
						$style .= 'border-width:' . $ult_db_settings['icon_border_size'] . 'px;';
						$style .= 'padding:' . $ult_db_settings['icon_border_spacing'] . 'px;';
						$style .= 'border-radius:' . $ult_db_settings['icon_border_radius'] . 'px;';
					}
					if ( ! empty( $img ) ) {
						$iconoutput .= "\n" . '<span class="aio-icon-img ' . esc_attr( $ult_db_settings['el_class'] ) . ' ' . 'btn1icon " style="font-size:' . esc_attr( $ult_db_settings['img_width'] ) . 'px;' . esc_attr( $style ) . '" ' . $css_trans . '>'; // phpcs:ignore Generic.Strings.UnnecessaryStringConcat.Found
						$iconoutput .= "\n\t" . '<img class="img-icon dual_img" alt="' . esc_attr( $alt ) . '" src="' . esc_url( apply_filters( 'ultimate_images', $img ) ) . '" />';
						$iconoutput .= "\n" . '</span>';
					}
					if ( ! empty( $img ) ) {
						$iconoutput = $iconoutput;
					} else {
						$iconoutput = '';
					}
				}
			} else {
				if ( '' !== $ult_db_settings['icon'] ) {
					if ( '' !== $ult_db_settings['icon_color'] ) {
						$style .= 'color:' . $ult_db_settings['icon_color'] . ';';
					}
					if ( 'none' !== $ult_db_settings['icon_style'] ) {
						if ( '' !== $ult_db_settings['icon_color_bg'] ) {
							$style .= 'background:' . $ult_db_settings['icon_color_bg'] . ';';
						}
					}
					if ( 'advanced' == $ult_db_settings['icon_style'] ) {
						$style .= 'border-style:' . $ult_db_settings['icon_border_style'] . ';';
						$style .= 'border-color:' . $ult_db_settings['icon_color_border'] . ';';
						$style .= 'border-width:' . $ult_db_settings['icon_border_size'] . 'px;';
						$style .= 'width:' . $ult_db_settings['icon_border_spacing'] . 'px;';
						$style .= 'height:' . $ult_db_settings['icon_border_spacing'] . 'px;';
						$style .= 'line-height:' . $ult_db_settings['icon_border_spacing'] . 'px;';
						$style .= 'border-radius:' . $ult_db_settings['icon_border_radius'] . 'px;';
					}
					if ( '' !== $ult_db_settings['icon_size'] ) {
						$style .= 'font-size:' . $ult_db_settings['icon_size'] . 'px;';
					}
					if ( 'left' !== $ult_db_settings['icon_align'] ) {
						$style .= 'display:inline-block;';
					}
					if ( '' !== $ult_db_settings['icon'] ) {
						$iconoutput .= "\n" . '<span class="aio-icon btn1icon ' . esc_attr( $ult_db_settings['icon_style'] ) . ' ' . esc_attr( $ult_db_settings['el_class'] ) . '" ' . $css_trans . ' style="' . esc_attr( $style ) . '">';
						$iconoutput .= "\n\t" . '<i class="' . esc_attr( $ult_db_settings['icon'] ) . '" ></i>';
						$iconoutput .= "\n" . '</span>';
					}
					if ( '' !== $ult_db_settings['icon'] && 'none' !== $ult_db_settings['icon'] ) {
						$iconoutput = $iconoutput;
					} else {
						$iconoutput = '';
					}
				}
			}

			$style2      = '';
			$href1       = '';
			$target2     = '';
			$img2        = '';
			$alt1        = '';
			$iconoutput2 = '';
			$url2        = '';
			/*---- for icon 2--------------*/
			if ( '' !== $ult_db_settings['btn_icon_link'] ) {
				$href1 = vc_build_link( $ult_db_settings['btn_icon_link'] );

				$url2        = ( isset( $href1['url'] ) && '' !== $href1['url'] ) ? $href1['url'] : '';
				$target2     = ( isset( $href1['target'] ) && '' !== $href1['target'] ) ? esc_attr( trim( $href1['target'] ) ) : '';
				$link_title2 = ( isset( $href1['title'] ) && '' !== $href1['title'] ) ? esc_attr( $href1['title'] ) : '';
				$rel2        = ( isset( $href1['rel'] ) && '' !== $href1['rel'] ) ? esc_attr( $href1['rel'] ) : '';

				if ( '' == $url2 ) {
					$url2 = 'javascript:void(0);';
				}
			} else {
				$url2 = 'javascript:void(0);';
			}

			if ( 'custom' == $ult_db_settings['btn_icon_type'] ) {
				$img2 = apply_filters( 'ult_get_img_single', $ult_db_settings['btn_icon_img'], 'url' );
				$alt2 = apply_filters( 'ult_get_img_single', $ult_db_settings['btn_icon_img'], 'alt' );
				if ( 'none' !== $ult_db_settings['btn_icon_style'] ) {
					if ( '' !== $ult_db_settings['btn_icon_color_bg'] ) {
						$style2 .= 'background:' . $ult_db_settings['btn_icon_color_bg'] . ';';
					}
				}

				if ( 'square' == $ult_db_settings['btn_icon_style'] ) {
					$el_class1 .= ' uavc-square ';
				}
				if ( 'circle' == $ult_db_settings['btn_icon_style'] ) {
					$el_class1 .= ' uavc-circle ';
				}
				if ( 'advanced' == $ult_db_settings['btn_icon_style'] && '' !== $ult_db_settings['btn_icon_border_style'] ) {
					$style2 .= 'border-style:' . $ult_db_settings['btn_icon_border_style'] . ';';
					$style2 .= 'border-color:' . $ult_db_settings['btn_icon_color_border'] . ';';
					$style2 .= 'border-width:' . $ult_db_settings['btn_icon_border_size'] . 'px;';
					$style2 .= 'padding:' . $ult_db_settings['btn_icon_border_spacing'] . 'px;';
					$style2 .= 'border-radius:' . $ult_db_settings['btn_icon_border_radius'] . 'px;';
				}
				if ( ! empty( $img2 ) ) {
					$iconoutput2 .= "\n" . '<span class="aio-icon-img ' . esc_attr( $el_class1 ) . ' btn1icon" style="font-size:' . esc_attr( $ult_db_settings['btn_img_width'] ) . 'px;' . esc_attr( $style2 ) . '" ' . $css_trans . '>';
					$iconoutput2 .= "\n\t" . '<img class="img-icon dual_img" alt="' . esc_attr( $alt2 ) . '" src="' . esc_url( apply_filters( 'ultimate_images', $img2 ) ) . '" />';
					$iconoutput2 .= "\n" . '</span>';
				}
				if ( ! empty( $img2 ) ) {
					$iconoutput2 = $iconoutput2;
				} else {
					$iconoutput2 = '';
				}
			} else {
				if ( '' !== $ult_db_settings['btn_icon_color'] ) {
					$style2 .= 'color:' . $ult_db_settings['btn_icon_color'] . ';';
				}
				if ( 'none' !== $ult_db_settings['btn_icon_style'] ) {
					if ( '' !== $ult_db_settings['btn_icon_color_bg'] ) {
						$style2 .= 'background:' . $ult_db_settings['btn_icon_color_bg'] . ';';
					}
				}
				if ( 'advanced' == $ult_db_settings['btn_icon_style'] ) {
					$style2 .= 'border-style:' . $ult_db_settings['btn_icon_border_style'] . ';';
					$style2 .= 'border-color:' . $ult_db_settings['btn_icon_color_border'] . ';';
					$style2 .= 'border-width:' . $ult_db_settings['btn_icon_border_size'] . 'px;';
					$style2 .= 'width:' . $ult_db_settings['btn_icon_border_spacing'] . 'px;';
					$style2 .= 'height:' . $ult_db_settings['btn_icon_border_spacing'] . 'px;';
					$style2 .= 'line-height:' . $ult_db_settings['btn_icon_border_spacing'] . 'px;';
					$style2 .= 'border-radius:' . $ult_db_settings['btn_icon_border_radius'] . 'px;';
				}
				if ( '' !== $ult_db_settings['btn_icon_size'] ) {
					$style2 .= 'font-size:' . $ult_db_settings['btn_icon_size'] . 'px;';
				}

				if ( 'left' !== $ult_db_settings['btn2_icon_align'] ) {
					$style2 .= 'display:inline-block;';
				}
				if ( '' !== $ult_db_settings['btn_icon'] ) {
					$iconoutput2 .= "\n" . '<span class="aio-icon btn1icon ' . esc_attr( $ult_db_settings['btn_icon_style'] ) . ' ' . esc_attr( $el_class1 ) . '" ' . $css_trans . ' style="' . esc_attr( $style2 ) . '">';
					$iconoutput2 .= "\n\t" . '<i class="' . esc_attr( $ult_db_settings['btn_icon'] ) . '" ></i>';
					$iconoutput2 .= "\n" . '</span>';
				}
				if ( '' !== $ult_db_settings['btn_icon'] && 'none' !== $ult_db_settings['btn_icon'] ) {
					$iconoutput2 = $iconoutput2;
				} else {

					$iconoutput2 = '';
				}
			}

			$hstyle     = '';
			$hoverstyle = '';
			$ult_db_settings['btn_hover_style'];
			if ( 'Style 1' == $ult_db_settings['btn_hover_style'] ) {
				$hoverstyle = 'ult-dual-btn';
			}
			if ( '' == $ult_db_settings['btn_hover_style'] ) {
				$hoverstyle = 'ult-dual-btn';

			}
			if ( 'Style 2' == $ult_db_settings['btn_hover_style'] ) {
				$hoverstyle = 'ult-dual-btn3';

			}
			if ( 'Style 3' == $ult_db_settings['btn_hover_style'] ) {
				$hoverstyle = 'ult-dual-btn4';

			}

			/*--------css for title1------------*/
			$ult_db_settings['btn1_padding'];
			$dual_btn_id  = 'dualbtn-' . wp_rand( 1000, 9999 );
			$title1_style = '';
			if ( function_exists( 'get_ultimate_font_family' ) ) {
					$mhfont_family = get_ultimate_font_family( $ult_db_settings['btn1_font_family'] );
				if ( '' !== $mhfont_family ) {
					$title1_style .= 'font-family:' . $mhfont_family . ';';
				}
			}
			if ( function_exists( 'get_ultimate_font_style' ) ) {
				$title1_style .= get_ultimate_font_style( $ult_db_settings['btn1_heading_style'] );
			}
			if ( is_numeric( $ult_db_settings['title_font_size'] ) ) {
				$ult_db_settings['title_font_size'] = 'desktop:' . $ult_db_settings['title_font_size'] . 'px;';
			}
			if ( is_numeric( $ult_db_settings['title_line_ht'] ) ) {
				$ult_db_settings['title_line_ht'] = 'desktop:' . $ult_db_settings['title_line_ht'] . 'px;';
			}
			$title1_style .= 'color:' . $ult_db_settings['btn1_text_color'] . ';';// color.
			$dualbtn_args  = array(
				'target'      => '#' . $dual_btn_id . ' .ult-dual-button-title', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_db_settings['title_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_db_settings['title_line_ht'],
				),
			);
			$data_list1    = get_ultimate_vc_responsive_media_css( $dualbtn_args );
			/*--------css for title2------------*/

			$title2_style = '';
			if ( function_exists( 'get_ultimate_font_family' ) ) {
					$mhfont_family1 = get_ultimate_font_family( $ult_db_settings['btn2_font_family'] );
				if ( '' !== $mhfont_family1 ) {
					$title2_style .= 'font-family:' . $mhfont_family1 . ';';
				}
			}
			if ( function_exists( 'get_ultimate_font_style' ) ) {
				$title2_style .= get_ultimate_font_style( $ult_db_settings['btn2_heading_style'] );
			}
			$title2_style .= 'color:' . $ult_db_settings['btn2_text_color'] . ';';// color.
			/*--------css for button1------------*/

			$btncolor_style  = '';
			$btncolor_style .= 'background-color:' . $ult_db_settings['btn1_background_color'] . ' !important;';

			/*--------css for button2------------*/

			$btncolor1_style  = '';
			$btncolor1_style .= 'background-color:' . $ult_db_settings['btn2_background_color'] . ' !important;';

			/*--------css for button------------*/

			$btnmain_style  = '';
			$btnmain_style .= 'border-color:' . $ult_db_settings['btn_color_border'] . ';';

			$btnmain_style .= 'border-style:' . $ult_db_settings['btn_border_style'] . ';';
			if ( '' != $ult_db_settings['btn_border_style'] ) {
				$btnmain_style .= 'border-width:' . $ult_db_settings['btn_border_size'] . 'px;';
			} else {
				$btnmain_style .= 'border-width:0px;';
			}
			$btnmain_style .= 'border-radius:' . $ult_db_settings['btn_border_radius'] . 'px;';
			if ( '' != $ult_db_settings['btn_width'] ) {
				$btnmain_style .= 'width:' . $ult_db_settings['btn_width'] . 'px;';
			}

			/*--------for divider------------*/
			$text_style  = '';
			$text_style .= 'line-height: 1.8em;';
			$text_style .= 'color:' . $ult_db_settings['divider_text_color'] . ';';
			$text_style .= 'background-color:' . $ult_db_settings['divider_bg_color'] . ';';

			if ( '' == $ult_db_settings['divider_border_style'] ) {
				$text_style .= 'border-width:0px;';
			} else {
				$text_style .= 'border-color:' . $ult_db_settings['divider_color_border'] . ';';
				$text_style .= 'border-width:' . $ult_db_settings['divider_border_size'] . 'px;';
				$text_style .= 'border-style:' . $ult_db_settings['divider_border_style'] . ';';
				$text_style .= 'border-radius:' . $ult_db_settings['divider_border_radius'] . 'px;';
			}

			if ( 'text' == $ult_db_settings['divider_style'] ) {
				$text = $ult_db_settings['divider_text'];
			} elseif ( 'icon' == $ult_db_settings['divider_style'] ) {
				$text = '<i class="' . $ult_db_settings['divider_icon'] . '"></i>';

			} elseif ( 'image' == $ult_db_settings['divider_style'] ) {
				$text_style  = '';
				$text_style .= 'width: 25px;
				height: 25px;
				border-radius: 50%;
				background-color:' . $ult_db_settings['divider_bg_color'] . ';';

				$img3 = apply_filters( 'ult_get_img_single', $ult_db_settings['divider_icon_img'], 'url' );
				$alt3 = apply_filters( 'ult_get_img_single', $ult_db_settings['divider_icon_img'], 'alt' );
				$text = '<img class="img-icon" alt="' . esc_attr( $alt3 ) . '" src="' . esc_url( apply_filters( 'ultimate_images', $img3 ) ) . '" style="' . esc_attr( $text_style ) . '" />';

			}
			/*--- generate random no------------*/
			$ult_db_settings['dual_resp'];
			$resp_data = 'data-response="' . esc_attr( $ult_db_settings['dual_resp'] ) . '"';
			$id        = '';
			$id        = 'ult_btn_' . wp_rand();

			/*----------for btn1 hover------------*/
			$btn_hover  = '';
			$btn_hover .= 'data-bgcolor="' . esc_attr( $ult_db_settings['btn1_background_color'] ) . '" ';
			$btn_hover .= 'data-bghovercolor="' . esc_attr( $ult_db_settings['btn1_bghovercolor'] ) . '" ';
			$btn_hover .= 'data-icon_color="' . esc_attr( $ult_db_settings['icon_color'] ) . '" ';
			$btn_hover .= 'data-icon_hover_color="' . esc_attr( $ult_db_settings['icon_hover_color'] ) . '" ';
			$btn_hover .= 'data-textcolor="' . esc_attr( $ult_db_settings['btn1_text_color'] ) . '" ';
			$btn_hover .= 'data-texthovercolor="' . esc_attr( $ult_db_settings['btn1_text_hovercolor'] ) . '" ';
			if ( 'none' == $ult_db_settings['icon_style'] ) {
				$btn_hover .= 'data-iconbgcolor="transperent" ';
				$btn_hover .= 'data-iconbghovercolor="transperent" ';
				$btn_hover .= 'data-iconborder="transperent" ';
				$btn_hover .= 'data-iconhoverborder="transperent" ';
			} else {

				$btn_hover .= 'data-iconbgcolor="' . esc_attr( $ult_db_settings['icon_color_bg'] ) . '" ';
				$btn_hover .= 'data-iconbghovercolor="' . esc_attr( $ult_db_settings['icon_color_hoverbg'] ) . '" ';
				$btn_hover .= 'data-iconborder="' . esc_attr( $ult_db_settings['icon_color_border'] ) . '" ';
				$btn_hover .= 'data-iconhoverborder="' . esc_attr( $ult_db_settings['icon_color_hoverborder'] ) . '" ';
			}

			/*----------for btn2 hover------------*/
			$btn2_hover  = '';
			$btn2_hover .= 'data-bgcolor="' . esc_attr( $ult_db_settings['btn2_background_color'] ) . '" ';
			$btn2_hover .= 'data-bghovercolor="' . esc_attr( $ult_db_settings['btn2_bghovercolor'] ) . '" ';
			$btn2_hover .= 'data-icon_color="' . esc_attr( $ult_db_settings['btn_icon_color'] ) . '" ';
			$btn2_hover .= 'data-icon_hover_color="' . esc_attr( $ult_db_settings['btn_iconhover_color'] ) . '" ';
			$btn2_hover .= 'data-textcolor="' . esc_attr( $ult_db_settings['btn2_text_color'] ) . '" ';
			$btn2_hover .= 'data-texthovercolor="' . esc_attr( $ult_db_settings['btn2_text_hovercolor'] ) . '" ';
			if ( 'none' == $ult_db_settings['btn_icon_style'] ) {
				$btn2_hover .= 'data-iconbgcolor="transperent" ';
				$btn2_hover .= 'data-iconbghovercolor="transperent" ';
				$btn2_hover .= 'data-iconborder="transperent" ';
				$btn2_hover .= 'data-iconhoverborder="transperent" ';
			} else {
				$btn2_hover .= 'data-iconbgcolor="' . esc_attr( $ult_db_settings['btn_icon_color_bg'] ) . '" ';
				$btn2_hover .= 'data-iconbghovercolor="' . esc_attr( $ult_db_settings['btn_icon_color_hoverbg'] ) . '" ';
				$btn2_hover .= 'data-iconborder="' . esc_attr( $ult_db_settings['btn_icon_color_border'] ) . '" ';
				$btn2_hover .= 'data-iconhoverborder="' . esc_attr( $ult_db_settings['btn_icon_color_hoverborder'] ) . '" ';
			}

			/*--- main button border-----*/
			$mainbtn = '';
			if ( '' == $ult_db_settings['btn_hover_style'] ) {
				$mainbtn .= 'data-bcolor="' . esc_attr( $ult_db_settings['btn_color_border'] ) . '"';
				$mainbtn .= 'data-bhcolor="' . esc_attr( $ult_db_settings['btn_color_border'] ) . '"';
			} else {
				$mainbtn .= 'data-bcolor="' . esc_attr( $ult_db_settings['btn_color_border'] ) . '"';
				$mainbtn .= 'data-bhcolor="' . esc_attr( $btn_color_hoverborder ) . '"';
			}

			$ult_db_settings['icon_align'];

			/*---- for icon line-height----*/
			$size          = '';
			$icon1_lineht  = '';
			$icon2_lineht  = '';
			$iconht1       = '';
			$iconht        = '';
			$icon2_lineht2 = '';
			$iconht2       = '';
			$icon1_lineht2 = '';
			$icnsize       = '';
			$icnsize1      = '';
			$icnsize2      = '';
			$emptyicon     = '';
			$emptyicon1    = '';
			if ( '' == $iconoutput ) {
				$emptyicon                     = 'padding-left:0px;';
				$ult_db_settings['icon_align'] = 'left';
			}
			if ( '' == $iconoutput2 ) {
				$emptyicon1                         = 'padding-left:0px;';
				$ult_db_settings['btn2_icon_align'] = 'right';
			}
			$subop            = '';
			$subop           .= '
			<div class="ult_dual_button ' . esc_attr( $dual_design_style_css ) . ' ' . esc_attr( $is_vc_49_plus ) . ' to-' . esc_attr( $ult_db_settings['btn_alignment'] ) . '  ' . esc_attr( $extraclass ) . '"  ' . $resp_data . ' id="' . esc_attr( $id ) . '">

			<div id="' . esc_attr( $dual_btn_id ) . '" class="ulitmate_dual_buttons ' . esc_attr( $hoverstyle ) . ' ult_main_dualbtn " ' . $mainbtn . '>

			<div class="ult_dualbutton-wrapper btn-inline place-template bt1 ">';
			$is_no_icon_first = ( trim( $iconoutput ) === '' ) ? 'ult-dual-btn-no-icon' : '';
			if ( 'right' == $ult_db_settings['icon_align'] ) {
				$subop .= '<a ' . Ultimate_VC_Addons::uavc_link_init( $url1, $target1, $link_title1, $rel1 ) . ' class="ult_ivan_button round-square with-icon icon-after with-text place-template ult_dual1" style="' . esc_attr( $icon1_lineht2 ) . ';margin-right:px;' . esc_attr( $size ) . ';' . esc_attr( $btncolor_style ) . esc_attr( $button1_bstyle ) . '; ' . esc_attr( $btnmain_style ) . ';">
			<span class="ult-dual-btn-1 ' . esc_attr( $ult_db_settings['btn_hover_style'] ) . '" style=""  ' . $btn_hover . '>

			<span class="text-btn ult-dual-button-title title_left ult-responsive " ' . $data_list1 . '  style="' . esc_attr( $title1_style ) . '">' . esc_html( $ult_db_settings['button1_text'] ) . '</span>
			<span class="icon-simple icon-right1 ult_btn1span ' . esc_attr( $is_no_icon_first ) . '"  style="' . esc_attr( $icnsize1 ) . ';' . esc_attr( $emptyicon ) . ' ">' . $iconoutput . '</span
			</span>
			</a>';
			} else {

				$subop .= '<a ' . Ultimate_VC_Addons::uavc_link_init( $url1, $target1, $link_title1, $rel1 ) . ' class="ult_ivan_button round-square with-icon icon-before with-text place-template ult_dual1" style="' . esc_attr( $icon1_lineht2 ) . ';margin-right:px;' . esc_attr( $size ) . ';' . esc_attr( $btncolor_style ) . esc_attr( $button1_bstyle ) . '; ' . esc_attr( $btnmain_style ) . ';">
			<span class="ult-dual-btn-1 ' . esc_attr( $ult_db_settings['btn_hover_style'] ) . '" style=""  ' . $btn_hover . '>
			<span class="icon-simple icon-left1 ult_btn1span ' . esc_attr( $is_no_icon_first ) . '"  style="' . esc_attr( $icnsize1 ) . ';' . esc_attr( $emptyicon ) . ' ">' . $iconoutput . '</span>
			<span class="text-btn ult-dual-button-title ult-responsive" ' . $data_list1 . ' style="' . esc_attr( $title1_style ) . '">' . esc_html( $ult_db_settings['button1_text'] ) . '</span>

			</span>
			</a>';
			}

			$subop     .= '<span class="middle-text" style="' . esc_attr( $text_style ) . '">
			<span class="middle-inner"  >' . $text . '</span>
			</span>

			</div>

			<div class="ult_dualbutton-wrapper btn-inline place-template btn2 ">';
			$is_no_icon = ( trim( $iconoutput2 ) === '' ) ? 'ult-dual-btn-no-icon' : '';
			if ( 'right' == $ult_db_settings['btn2_icon_align'] ) {
				$subop .= '<a ' . Ultimate_VC_Addons::uavc_link_init( $url2, $target2, $link_title2, $rel2 ) . ' class="ult_ivan_button round-square with-icon icon-after with-text place-template ult_dual2"  style="' . esc_attr( $icon2_lineht2 ) . ';' . esc_attr( $btncolor1_style ) . esc_attr( $button2_bstyle ) . ';margin-left:px;' . esc_attr( $size ) . ';' . esc_attr( $btnmain_style ) . '">
			<span class="ult-dual-btn-2 ' . esc_attr( $ult_db_settings['btn_hover_style'] ) . '"  ' . $btn2_hover . '>
			<span class="text-btn ult-dual-button-title" style="' . esc_attr( $title2_style ) . '">' . esc_html( $ult_db_settings['button2_text'] ) . '</span>

			<span class="icon-simple icon-right2 ult_btn1span ' . esc_attr( $is_no_icon ) . '"  style="' . esc_attr( $icnsize2 ) . ';' . esc_attr( $emptyicon1 ) . ' ">' . $iconoutput2 . '</span>
			</span>
			</a>';
			} else {

				$subop .= '<a ' . Ultimate_VC_Addons::uavc_link_init( $url2, $target2, $link_title2, $rel2 ) . ' class="ult_ivan_button   round-square  with-icon icon-before with-text place-template ult_dual2"  style="' . esc_attr( $icon2_lineht2 ) . ';' . esc_attr( $btncolor1_style ) . esc_attr( $button2_bstyle ) . ';margin-left:-0px;' . esc_attr( $size ) . '; ' . esc_attr( $btnmain_style ) . '">
			<span class="ult-dual-btn-2 ' . esc_attr( $ult_db_settings['btn_hover_style'] ) . '"  ' . $btn2_hover . '>

			<span class="icon-simple icon-left2 ult_btn1span ' . esc_attr( $is_no_icon ) . '"  style="' . esc_attr( $icnsize2 ) . ';' . esc_attr( $emptyicon1 ) . ' ">' . $iconoutput2 . '</span>
			<span class="text-btn ult-dual-button-title title_right" style="' . esc_attr( $title2_style ) . '">' . esc_html( $ult_db_settings['button2_text'] ) . '</span>
			</span>
			</a>';

			}
			$subop .= '</div>
			</div>
			</div>';

			$is_preset = false; // Retrieve preset Code.
			if ( isset( $_GET['preset'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$is_preset = true;
			}
			if ( $is_preset ) {
				$text = 'array ( ';
				foreach ( $atts as $key => $att ) {
					$text .= '<br/>	\'' . $key . '\' => \'' . $att . '\',';
				}
				if ( '' != $content ) {
					$text .= '<br/>	\'content\' => \'' . $content . '\',';
				}
				$text  .= '<br/>)';
				$subop .= '<pre>';
				$subop .= $text;
				$subop .= '</pre>'; // remove backslash once copied.
			}

			return $subop;

		}
		/**
		 * Function to intialize the button module
		 *
		 * @since ----
		 * @access public
		 */
		public function ultimate_dual_button() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'        => __( 'Dual Button' ),
						'base'        => 'ult_dualbutton',
						'icon'        => 'uvc_dual_button',
						'class'       => 'uvc_dual_button',
						'category'    => __( 'Ultimate VC Addons', 'ultimate_vc' ),
						'description' => __( 'Add a dual button and give some custom style.', 'ultimate_vc' ),
						'params'      => array(
							// Play with icon selector.
							/*-----------general------------*/
								array(
									'type'        => 'dropdown',
									'class'       => '',
									'heading'     => __( 'Button Style', 'ultimate_vc' ),
									'param_name'  => 'btn_hover_style',
									'value'       => array(
										'Style 1' => 'Style 1',
										'Style 2' => 'Style 2',
										'None'    => ' ',

									),
									'description' => __( 'Select the Hover style for Button.', 'ultimate_vc' ),

								),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Text Font size', 'ultimate_vc' ),
								'param_name' => 'title_font_size',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Text Line Height', 'ultimate_vc' ),
								'param_name' => 'title_line_ht',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
							),
							array(
								'type'             => 'number',
								'class'            => '',
								'heading'          => __( 'Border Radius', 'ultimate_vc' ),
								'param_name'       => 'btn_border_radius',

								'min'              => 1,
								'max'              => 50,
								'suffix'           => 'px',
								'edit_field_class' => 'vc_column vc_col-sm-4',

							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Border Style', 'ultimate_vc' ),
								'param_name'  => 'btn_border_style',
								'value'       => array(
									'None'   => '',
									'Solid'  => 'solid',
									'Dashed' => 'dashed',
									'Dotted' => 'dotted',
									'Double' => 'double',
									'Inset'  => 'inset',
									'Outset' => 'outset',
								),
								'description' => __( 'Select the border style for Button.', 'ultimate_vc' ),

							),
							array(
								'type'             => 'colorpicker',
								'class'            => '',
								'heading'          => __( 'Border Color', 'ultimate_vc' ),
								'param_name'       => 'btn_color_border',
								'value'            => '',
								'description'      => __( 'Select border color for button.', 'ultimate_vc' ),
								'dependency'       => array(
									'element'   => 'btn_border_style',
									'not_empty' => true,
								),
								'edit_field_class' => 'vc_column vc_col-sm-6',
							),

							array(
								'type'             => 'number',
								'class'            => '',
								'heading'          => __( 'Border Width', 'ultimate_vc' ),
								'param_name'       => 'btn_border_size',
								'value'            => '',
								'min'              => 1,
								'max'              => 10,
								'suffix'           => 'px',
								'description'      => __( 'Thickness of the border.', 'ultimate_vc' ),
								'dependency'       => array(
									'element'   => 'btn_border_style',
									'not_empty' => true,
								),
								'edit_field_class' => 'vc_column vc_col-sm-6',
							),
							array(
								'type'             => 'number',
								'class'            => '',
								'heading'          => __( 'Button Width', 'ultimate_vc' ),
								'param_name'       => 'btn_width',
								'min'              => 1,
								'max'              => 50,
								'suffix'           => 'px',
								'edit_field_class' => 'vc_column vc_col-sm-6',

							),
							array(
								'type'             => 'dropdown',
								'class'            => '',
								'heading'          => __( 'Button Alignment', 'ultimate_vc' ),
								'param_name'       => 'btn_alignment',
								'value'            => array(
									'center' => '',
									'left'   => 'left',
									'right'  => 'right',

								),
								'edit_field_class' => 'vc_column vc_col-sm-6',

							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'param_name'  => 'dual_resp',
								'value'       => 'on',
								'default_set' => true,
								'options'     => array(
									'on' => array(
										'label' => __( 'Enable Responsive Mode?', 'ultimate_vc' ),
										'on'    => __( 'Yes', 'ultimate_vc' ),
										'off'   => __( 'No', 'ultimate_vc' ),
									),
								),
								'description' => __( 'Enable Responsive Mod or not', 'ultimate_vc' ),
							),

							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Custom CSS Class', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'value'       => '',
								'description' => __( 'Ran out of options? Need more styles? Write your own CSS and mention the class name here.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( ' Button Text', 'ultimate_vc' ),
								'param_name'  => 'button1_text',
								'value'       => '',
								'admin_label' => true,
								'description' => __( 'Enter your text here.', 'ultimate_vc' ),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'vc_link',
								'class'       => '',
								'heading'     => __( 'Link ', 'ultimate_vc' ),
								'param_name'  => 'icon_link',
								'value'       => '',
								'description' => __( 'Add a custom link or select existing page. You can remove existing link as well.', 'ultimate_vc' ),
								'group'       => 'Button1',

							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Background Color', 'ultimate_vc' ),
								'param_name'  => 'btn1_background_color',
								'value'       => '',
								'description' => __( 'Select Background Color for Button.', 'ultimate_vc' ),
								'group'       => 'Button1',

							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Background Hover Color', 'ultimate_vc' ),
								'param_name'  => 'btn1_bghovercolor',
								'value'       => '',
								'description' => __( 'Select background hover color for Button.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'btn_hover_style',
									'value'   => array( 'Style 1', 'Style 2', 'Style 3' ),
								),
								'group'       => 'Button1',

							),

							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'btn1_icon_setting',
								'text'             => __( 'Icon/Image ', 'ultimate_vc' ),
								'value'            => '',
								'class'            => '',
								'group'            => __( 'Button1', 'ultimate_vc' ),
								'edit_field_class' => 'ult-param-heading-wrapper  vc_column vc_col-sm-12',
							),

							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon to display', 'ultimate_vc' ),
								'param_name'  => 'icon_type',
								'value'       => array(
									'Font Icon Manager' => 'selector',
									'Custom Image Icon' => 'custom',
								),
								'description' => __( 'Use existing font icon or upload a custom image.', 'ultimate_vc' ),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'icon_manager',
								'class'       => '',
								'heading'     => __( 'Select Icon ', 'ultimate_vc' ),
								'param_name'  => 'icon',
								'value'       => '',
								'description' => __( "Click and select icon of your choice. If you can't find the one that suits for your purpose", 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-font-icon-manager' target='_blank' rel='noopener'>" . __( 'add new here', 'ultimate_vc' ) . '</a>.',
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'ult_img_single',
								'class'       => '',
								'heading'     => __( 'Upload Image Icon:', 'ultimate_vc' ),
								'param_name'  => 'icon_img',
								'value'       => '',
								'description' => __( 'Upload the custom image icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'custom' ),
								),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Image Width', 'ultimate_vc' ),
								'param_name'  => 'img_width',
								'value'       => '',
								'min'         => 16,
								'max'         => 512,
								'suffix'      => 'px',
								'description' => __( 'Provide image width', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'custom' ),
								),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Size of Icon', 'ultimate_vc' ),
								'param_name'  => 'icon_size',
								'value'       => '',
								'min'         => 12,
								'max'         => 72,
								'suffix'      => 'px',
								'description' => __( 'How big would you like it?', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color',
								'value'       => '',
								'description' => __( 'Icon Color!', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon Hover Color', 'ultimate_vc' ),
								'param_name'  => 'icon_hover_color',
								'value'       => '',
								'description' => __( 'Icon hover color !', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon or Image Style', 'ultimate_vc' ),
								'param_name'  => 'icon_style',
								'value'       => array(
									'Simple'            => 'none',
									'Circle Background' => 'circle',
									'Square Background' => 'square',
									'Design your own'   => 'advanced',
								),
								'description' => __( 'We have given three quick preset if you are in a hurry. Otherwise, create your own with various options.', 'ultimate_vc' ),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon or Image Background Color ', 'ultimate_vc' ),
								'param_name'  => 'icon_color_bg',
								'value'       => '',
								'description' => __( 'Select background color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_style',
									'value'   => array( 'circle', 'square', 'advanced' ),
								),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon or Image Background Hover Color ', 'ultimate_vc' ),
								'param_name'  => 'icon_color_hoverbg',
								'value'       => '',
								'description' => __( 'Select background hover color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_style',
									'value'   => array( 'circle', 'square', 'advanced' ),
								),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon or Image Border Style', 'ultimate_vc' ),
								'param_name'  => 'icon_border_style',
								'value'       => array(
									'Solid'  => 'solid',
									'None'   => '',
									'Dashed' => 'dashed',
									'Dotted' => 'dotted',
									'Double' => 'double',
									'Inset'  => 'inset',
									'Outset' => 'outset',
								),
								'description' => __( 'Select the border style for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_style',
									'value'   => array( 'advanced' ),
								),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon or Image Border Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color_border',
								'value'       => '',
								'description' => __( 'Select border color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon or Image Border Hover Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color_hoverborder',
								'value'       => '',
								'description' => __( 'Select border hover color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Icon or Image Border Width', 'ultimate_vc' ),
								'param_name'  => 'icon_border_size',
								'value'       => '',
								'min'         => 1,
								'max'         => 10,
								'suffix'      => 'px',
								'description' => __( 'Thickness of the border.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Icon or Image Border Radius', 'ultimate_vc' ),
								'param_name'  => 'icon_border_radius',
								'value'       => '',
								'min'         => 1,
								'max'         => 100,
								'suffix'      => 'px',
								'description' => __( '0 pixel value will create a square border. As you increase the value, the shape convert in circle slowly. (e.g 500 pixels).', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
								'group'       => 'Button1',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Background Size', 'ultimate_vc' ),
								'param_name'  => 'icon_border_spacing',
								'value'       => '',
								'min'         => 2,
								'max'         => 100,
								'suffix'      => 'px',
								'description' => __( 'Spacing from center of the icon till the boundary of border / background', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
								'group'       => 'Button1',

							),

							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Alignment', 'ultimate_vc' ),
								'param_name' => 'icon_align',
								'value'      => array(
									'Left'  => '',
									'Right' => 'right',
								),
								'group'      => 'Button1',
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( ' Button Text', 'ultimate_vc' ),
								'param_name'  => 'button2_text',
								'value'       => '',
								'admin_label' => true,
								'description' => __( 'Enter your Button2 text here.', 'ultimate_vc' ),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'vc_link',
								'class'       => '',
								'heading'     => __( 'Link ', 'ultimate_vc' ),
								'param_name'  => 'btn_icon_link',
								'value'       => '',
								'description' => __( 'Add a custom link or select existing page. You can remove existing link as well.', 'ultimate_vc' ),
								'group'       => 'Button2',

							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Background Color', 'ultimate_vc' ),
								'param_name'  => 'btn2_background_color',
								'value'       => '',
								'description' => __( 'Select Background Color for Button.', 'ultimate_vc' ),
								'group'       => 'Button2',

							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Background Hover Color', 'ultimate_vc' ),
								'param_name'  => 'btn2_bghovercolor',
								'value'       => '',
								'description' => __( 'Select background hover color for Button.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'btn_hover_style',
									'value'   => array( 'Style 1', 'Style 2', 'Style 3' ),
								),
								'group'       => 'Button2',

							),

							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'btn1_icon_setting',
								'text'             => __( 'Icon/Image ', 'ultimate' ),
								'value'            => '',
								'class'            => '',
								'group'            => __( 'Button2', 'ultimate_vc' ),
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon to display:', 'ultimate_vc' ),
								'param_name'  => 'btn_icon_type',
								'value'       => array(
									'Font Icon Manager' => 'selector',
									'Custom Image Icon' => 'custom',
								),
								'description' => __( 'Use existing font icon or upload a custom image.', 'ultimate_vc' ),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'icon_manager',
								'class'       => '',
								'heading'     => __( 'Select Icon ', 'ultimate_vc' ),
								'param_name'  => 'btn_icon',
								'value'       => '',
								'description' => __( "Click and select icon of your choice. If you can't find the one that suits for your purpose", 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-font-icon-manager' target='_blank' rel='noopener'>" . __( 'add new here', 'ultimate_vc' ) . '</a>.',
								'dependency'  => array(
									'element' => 'btn_icon_type',
									'value'   => array( 'selector' ),
								),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'ult_img_single',
								'class'       => '',
								'heading'     => __( 'Upload Image Icon:', 'ultimate_vc' ),
								'param_name'  => 'btn_icon_img',
								'value'       => '',
								'description' => __( 'Upload the custom image icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'btn_icon_type',
									'value'   => array( 'custom' ),
								),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Image Width', 'ultimate_vc' ),
								'param_name'  => 'btn_img_width',
								'value'       => '',
								'min'         => 16,
								'max'         => 512,
								'suffix'      => 'px',
								'description' => __( 'Provide image width', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'btn_icon_type',
									'value'   => array( 'custom' ),
								),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Size of Icon', 'ultimate_vc' ),
								'param_name'  => 'btn_icon_size',
								'value'       => '',
								'min'         => 12,
								'max'         => 72,
								'suffix'      => 'px',
								'description' => __( 'How big would you like it?', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'btn_icon_type',
									'value'   => array( 'selector' ),
								),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon Color', 'ultimate_vc' ),
								'param_name'  => 'btn_icon_color',
								'value'       => '',
								'description' => __( 'Icon Color!', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'btn_icon_type',
									'value'   => array( 'selector' ),
								),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon Hover Color', 'ultimate_vc' ),
								'param_name'  => 'btn_iconhover_color',
								'value'       => '',
								'description' => __( 'Icon hover color!', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'btn_icon_type',
									'value'   => array( 'selector' ),
								),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon or Image Style', 'ultimate_vc' ),
								'param_name'  => 'btn_icon_style',
								'value'       => array(
									'Simple'            => 'none',
									'Circle Background' => 'circle',
									'Square Background' => 'square',
									'Design your own'   => 'advanced',
								),
								'description' => __( 'We have given three quick preset if you are in a hurry. Otherwise, create your own with various options.', 'ultimate_vc' ),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon or Image Background Color', 'ultimate_vc' ),
								'param_name'  => 'btn_icon_color_bg',
								'value'       => '',
								'description' => __( 'Select background color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'btn_icon_style',
									'value'   => array( 'circle', 'square', 'advanced' ),
								),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon or Image Background hover Color', 'ultimate_vc' ),
								'param_name'  => 'btn_icon_color_hoverbg',
								'value'       => '',
								'description' => __( 'Select background hover color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'btn_icon_style',
									'value'   => array( 'circle', 'square', 'advanced' ),
								),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon or Image Border Style', 'ultimate_vc' ),
								'param_name'  => 'btn_icon_border_style',
								'value'       => array(
									'None'   => '',
									'Solid'  => 'solid',
									'Dashed' => 'dashed',
									'Dotted' => 'dotted',
									'Double' => 'double',
									'Inset'  => 'inset',
									'Outset' => 'outset',
								),
								'description' => __( 'Select the border style for Button.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'btn_icon_style',
									'value'   => array( 'advanced' ),
								),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon or Image Border Color', 'ultimate_vc' ),
								'param_name'  => 'btn_icon_color_border',
								'value'       => '',
								'description' => __( 'Select border color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'btn_icon_border_style',
									'not_empty' => true,
								),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon or Image Border Hover Color', 'ultimate_vc' ),
								'param_name'  => 'btn_icon_color_hoverborder',
								'value'       => '',
								'description' => __( 'Select border color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'btn_icon_border_style',
									'not_empty' => true,
								),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Icon or Image Border Width', 'ultimate_vc' ),
								'param_name'  => 'btn_icon_border_size',
								'value'       => '',
								'min'         => 1,
								'max'         => 10,
								'suffix'      => 'px',
								'description' => __( 'Thickness of the border.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'btn_icon_border_style',
									'not_empty' => true,
								),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Icon or Image Border Radius', 'ultimate_vc' ),
								'param_name'  => 'btn_icon_border_radius',
								'value'       => '',
								'min'         => 1,
								'max'         => 100,
								'suffix'      => 'px',
								'description' => __( '0 pixel value will create a square border. As you increase the value, the shape convert in circle slowly. (e.g 500 pixels).', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'btn_icon_border_style',
									'not_empty' => true,
								),
								'group'       => 'Button2',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Icon or Image Background Size', 'ultimate_vc' ),
								'param_name'  => 'btn_icon_border_spacing',
								'value'       => '',
								'min'         => 30,
								'max'         => 500,
								'suffix'      => 'px',
								'description' => __( 'Spacing from center of the icon till the boundary of border / background', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'btn_icon_border_style',
									'not_empty' => true,
								),
								'group'       => 'Button2',

							),

							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Alignment', 'ultimate_vc' ),
								'param_name' => 'btn2_icon_align',
								'value'      => array(
									'Right' => '',
									'Left'  => 'left',

								),
								'group'      => 'Button2',
							),

							/*--------divider---------------*/
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Select Divider options', 'ultimate_vc' ),
								'param_name' => 'divider_style',
								'value'      => array(
									'Text'  => 'text',
									'Icon'  => 'icon',
									'Image' => 'image',
								),
								'group'      => 'Divider',
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( ' Text', 'ultimate_vc' ),
								'param_name'  => 'divider_text',
								'value'       => '',
								'description' => __( 'Enter your Divider text here.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'divider_style',
									'value'   => array( 'text' ),
								),
								'group'       => 'Divider',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Text/Icon Color', 'ultimate_vc' ),
								'param_name'  => 'divider_text_color',
								'value'       => '',
								'description' => __( 'Select  color for divider text/icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'divider_style',
									'value'   => array( 'text', 'icon' ),
								),
								'group'       => 'Divider',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Background Color', 'ultimate_vc' ),
								'param_name'  => 'divider_bg_color',
								'value'       => '',
								'description' => __( 'Select border color for Icon/Text/Image.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'divider_style',
									'not_empty' => true,
								),
								'group'       => 'Divider',
							),

							array(
								'type'        => 'icon_manager',
								'class'       => '',
								'heading'     => __( 'Select Icon ', 'ultimate_vc' ),
								'param_name'  => 'divider_icon',
								'value'       => '',
								'description' => __( "Click and select icon of your choice. If you can't find the one that suits for your purpose", 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-font-icon-manager' target='_blank' rel='noopener'>" . __( 'add new here', 'ultimate_vc' ) . '</a>.',
								'dependency'  => array(
									'element' => 'divider_style',
									'value'   => array( 'icon' ),
								),
								'group'       => 'Divider',
							),
							array(
								'type'        => 'ult_img_single',
								'class'       => '',
								'heading'     => __( 'Upload Image Icon:', 'ultimate_vc' ),
								'param_name'  => 'divider_icon_img',
								'value'       => '',
								'description' => __( 'Upload the custom image icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'divider_style',
									'value'   => array( 'image' ),
								),
								'group'       => 'Divider',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Border Style', 'ultimate_vc' ),
								'param_name'  => 'divider_border_style',
								'value'       => array(
									'None'   => '',
									'Solid'  => 'solid',
									'Dashed' => 'dashed',
									'Dotted' => 'dotted',
									'Double' => 'double',
									'Inset'  => 'inset',
									'Outset' => 'outset',
								),
								'description' => __( 'Select the border style for Button.', 'ultimate_vc' ),
								'group'       => 'Divider',

							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Border Color', 'ultimate_vc' ),
								'param_name'  => 'divider_color_border',
								'value'       => '',
								'description' => __( 'Select border color for divider.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'divider_border_style',
									'not_empty' => true,
								),
								'group'       => 'Divider',
							),

							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Border Width', 'ultimate_vc' ),
								'param_name'  => 'divider_border_size',
								'value'       => '',
								'min'         => 1,
								'max'         => 10,
								'suffix'      => 'px',
								'description' => __( 'Thickness of the border.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'divider_border_style',
									'not_empty' => true,
								),
								'group'       => 'Divider',
							),

							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Border Radius', 'ultimate_vc' ),
								'param_name' => 'divider_border_radius',

								'min'        => 1,
								'max'        => 50,
								'suffix'     => 'px',
								'dependency' => array(
									'element'   => 'divider_border_style',
									'not_empty' => true,
								),
								'group'      => 'Divider',

							),
							/*--- typgraphy--*/

									array(
										'type'             => 'ult_param_heading',
										'param_name'       => 'bt1typo-setting',
										'text'             => __( 'Button 1 ', 'ultimate' ),
										'value'            => '',
										'class'            => '',
										'group'            => __( 'Typography', 'ultimate_vc' ),
										'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
									),

							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Title Font Family', 'ultimate_vc' ),
								'param_name'  => 'btn1_font_family',
								'description' => __( 'Select the font of your choice. ', 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-google-font-manager' target='_blank' rel='noopener'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography',
							),

							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'btn1_heading_style',

								'group'      => 'Typography',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Text Color', 'ultimate_vc' ),
								'param_name'  => 'btn1_text_color',
								'value'       => '',
								'description' => __( 'Select text color for icon.', 'ultimate_vc' ),
								'group'       => 'Typography',

							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Text Hover Color', 'ultimate_vc' ),
								'param_name'  => 'btn1_text_hovercolor',
								'value'       => '',
								'description' => __( 'Select text hover color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'btn_hover_style',
									'value'   => array( 'Style 1', 'Style 2', 'Style 3' ),
								),
								'group'       => 'Typography',

							),

							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'btn2_bg_setting',
								'text'             => __( 'Button 2 ', 'ultimate' ),
								'value'            => '',
								'class'            => '',
								'group'            => __( 'Typography', 'ultimate_vc' ),
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),

							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Title Font Family', 'ultimate_vc' ),
								'param_name'  => 'btn2_font_family',
								'description' => __( 'Select the font of your choice. ', 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-google-font-manager' target='_blank' rel='noopener'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography',
							),

							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'btn2_heading_style',

								'group'      => 'Typography',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Text Color', 'ultimate_vc' ),
								'param_name'  => 'btn2_text_color',
								'value'       => '',
								'description' => __( 'Select text color for icon.', 'ultimate_vc' ),
								'group'       => 'Typography',

							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Text Hover Color', 'ultimate_vc' ),
								'param_name'  => 'btn2_text_hovercolor',
								'value'       => '',
								'description' => __( 'Select text hover color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'btn_hover_style',
									'value'   => array( 'Style 1', 'Style 2', 'Style 3' ),
								),
								'group'       => 'Typography',

							),
							array(
								'type'             => 'css_editor',
								'heading'          => __( 'Css', 'ultimate_vc' ),
								'param_name'       => 'css_dualbtn_design',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
							),
						),
					)
				);
			}
		}

	}
}
if ( class_exists( 'AIO_Dual_Button' ) ) {
	$AIO_Dual_Button = new AIO_Dual_Button(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase


}
if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Ult_Dualbutton' ) ) {
	/**
	 * Function that initializes Ultimate Dual Button Module
	 *
	 * @class WPBakeryShortCode_ult_dualbutton
	 */
	class WPBakeryShortCode_Ult_Dualbutton extends WPBakeryShortCode {
	}
}

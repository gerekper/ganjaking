<?php
/**
 *  Expandable Section for WPBakery Page Builder
 *
 *  @package Ultimate Animation
 */

if ( ! class_exists( 'AIO_Ultimate_Exp_Section' ) ) {
	/**
	 * Function that initializes Expandable Section Module
	 *
	 * @AIO_Ultimate_Exp_Section
	 */
	class AIO_Ultimate_Exp_Section {
		/**
		 * Constructor function that constructs default values for the Expandable Section module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'ultimate_ultimate_exp_section' ) );
			}
			add_shortcode( 'ultimate_exp_section', array( $this, 'ultimate_exp_section_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'ultimate_exp_scripts' ), 1 );

		}
		/**
		 * Function for expandable admin script and styles
		 *
		 * @since ----
		 * @access public
		 */
		public function ultimate_exp_scripts() {

			Ultimate_VC_Addons::ultimate_register_style( 'style_ultimate_expsection', 'expandable-section' );

			Ultimate_VC_Addons::ultimate_register_script( 'jquery_ultimate_expsection', 'expandable-section', false, array( 'jquery', 'jquery_ui' ), ULTIMATE_VERSION, false );

			Ultimate_VC_Addons::ultimate_register_script( 'jquery_ui', 'jquery-ui', false, array( 'jquery' ), ULTIMATE_VERSION, false );
		}

		/**
		 * Shortcode handler function for stats Icon
		 *
		 * @since ----
		 * @param array  $atts represts module attribuits.
		 * @param string $content module content.
		 * @access public
		 */
		public function ultimate_exp_section_shortcode( $atts, $content ) {
			$el_class   = '';
			$css_editor = '';

				$utl_ues_settings = shortcode_atts(
					array(

						'title'                   => ' ',
						'heading_style'           => ' ',
						'font_family'             => ' ',
						'title_font_size'         => '20',
						'title_line_ht'           => '20',
						'text_color'              => '#333333',
						'text_hovercolor'         => '#333333',
						'icon_type'               => 'selector',
						'icon'                    => '',
						'icon_img'                => '',
						'img_width'               => '48',
						'icon_size'               => '32',
						'icon_color'              => '#333333',
						'icon_hover_color'        => '#333333',
						'icon_style'              => 'none',
						'icon_color_bg'           => '#ffffff',
						'icon_color_hoverbg'      => '#ecf0f1',
						'icon_border_style'       => 'solid',
						'icon_color_border'       => '#333333',
						'icon_color_hoverborder'  => '#333333',
						'icon_border_size'        => '1',
						'icon_border_radius'      => '0',
						'icon_border_spacing'     => '30',
						'icon_align'              => 'center',
						'extra_class'             => ' ',
						'css'                     => ' ',
						'background_color'        => '#dbdbdb',
						'bghovercolor'            => '#e5e5e5',
						'cnt_bg_color'            => '#dbdbdb',
						'cnt_hover_bg_color'      => ' ',
						'exp_icon'                => ' ',
						'exp_effect'              => 'slideToggle',
						'cont_css'                => ' ',
						'section_width'           => ' ',
						'map_override'            => '0',
						'new_title'               => ' ',
						'new_icon'                => ' ',
						'new_icon_img'            => ' ',
						'title_active'            => '#333333',
						'title_active_bg'         => '#dbdbdb',
						'icon_active_color'       => '#333333',
						'icon_active_color_bg'    => '#ffffff',
						'title_margin'            => ' ',
						'title_alignment'         => 'center',
						'iconmargin_css'          => ' ',
						'icon_color_activeborder' => '#333333',
						'title_margin'            => ' ',
						'title_padding'           => ' ',
						'desc_padding'            => ' ',
						'desc_margin'             => ' ',
						'icon_margin'             => ' ',
						'section_height'          => '0',

					),
					$atts
				);

				$vc_version    = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
				$is_vc_49_plus = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'ult-adjust-bottom-margin' : '';

				/*
				---------- data attribute-----------------------------
				*/

				$data  = '';
				$data .= 'data-textcolor="' . esc_attr( $utl_ues_settings['text_color'] ) . '"';
			if ( ' ' == $utl_ues_settings['text_hovercolor'] ) {
				$utl_ues_settings['text_hovercolor'] = $utl_ues_settings['text_color'];
			}

			if ( '' == $utl_ues_settings['title_alignment'] ) {
				$utl_ues_settings['title_alignment'] = 'center';
			}

				$data .= 'data-texthover="' . esc_attr( $utl_ues_settings['text_hovercolor'] ) . '"';
				$data .= 'data-icncolor="' . esc_attr( $utl_ues_settings['icon_color'] ) . '"';
				$data .= 'data-ihover="' . esc_attr( $utl_ues_settings['icon_hover_color'] ) . '"';
				$data .= 'data-height="' . esc_attr( $utl_ues_settings['section_height'] ) . '"';

				$data .= 'data-cntbg="' . esc_attr( $utl_ues_settings['background_color'] ) . '"';
				$data .= 'data-cnthvrbg="' . esc_attr( $utl_ues_settings['bghovercolor'] ) . '"';
				$data .= 'data-headerbg="' . esc_attr( $utl_ues_settings['background_color'] ) . '"';
			if ( ' ' == $utl_ues_settings['bghovercolor'] ) {
				$utl_ues_settings['bghovercolor'] = $utl_ues_settings['background_color'];
			}
				$data .= 'data-headerhover="' . esc_attr( $utl_ues_settings['bghovercolor'] ) . '"';
				$data .= 'data-title="' . esc_attr( $utl_ues_settings['title'] ) . '"';
			if ( ' ' == $utl_ues_settings['new_title'] ) {
				$utl_ues_settings['new_title'] = $utl_ues_settings['title'];
			}

				$data .= 'data-newtitle="' . esc_attr( $utl_ues_settings['new_title'] ) . '"';

				$data .= 'data-icon="' . esc_attr( $utl_ues_settings['icon'] ) . '"';

			if ( ' ' == $utl_ues_settings['new_icon'] ) {
				$utl_ues_settings['new_icon'] = $utl_ues_settings['icon'];
			}
			if ( 'none' == $utl_ues_settings['new_icon'] ) {
				$utl_ues_settings['new_icon'] = $utl_ues_settings['icon'];
			}
				$data .= 'data-newicon="' . esc_attr( $utl_ues_settings['new_icon'] ) . '"';
				/*----active icon --------*/

			if ( '' == $utl_ues_settings['icon_active_color'] ) {
				$utl_ues_settings['icon_active_color'] = $utl_ues_settings['icon_hover_color'];
			}
				$data .= 'data-activeicon="' . esc_attr( $utl_ues_settings['icon_active_color'] ) . '"';

			if ( 'none' != $utl_ues_settings['icon_style'] ) {
				$data .= 'data-icnbg="' . esc_attr( $utl_ues_settings['icon_color_bg'] ) . '"';
				$data .= 'data-icnhvrbg="' . esc_attr( $utl_ues_settings['icon_color_hoverbg'] ) . '"';
				if ( ' ' == $utl_ues_settings['icon_active_color_bg'] ) {
					$utl_ues_settings['icon_active_color_bg'] = $utl_ues_settings['icon_color_hoverbg'];
				}
				$data .= 'data-activeiconbg="' . esc_attr( $utl_ues_settings['icon_active_color_bg'] ) . '"';

			}
			if ( 'advanced' == $utl_ues_settings['icon_style'] ) {
				$data .= 'data-icnbg="' . esc_attr( $utl_ues_settings['icon_color_bg'] ) . '"';
				$data .= 'data-icnhvrbg="' . esc_attr( $utl_ues_settings['icon_color_hoverbg'] ) . '"';
				$data .= 'data-icnborder="' . esc_attr( $utl_ues_settings['icon_color_border'] ) . '"';
				if ( ' ' == $utl_ues_settings['icon_color_hoverborder'] ) {
					$utl_ues_settings['icon_color_hoverborder'] = $utl_ues_settings['icon_color_border'];
				}
				$data .= 'data-icnhvrborder="' . esc_attr( $utl_ues_settings['icon_color_hoverborder'] ) . '"';
				if ( ' ' == $utl_ues_settings['icon_active_color_bg'] ) {
					$utl_ues_settings['icon_active_color_bg'] = $utl_ues_settings['bghovercolor'];
				}
				$data .= 'data-activeiconbg="' . esc_attr( $utl_ues_settings['icon_active_color_bg'] ) . '"';

				if ( ' ' == $utl_ues_settings['icon_color_activeborder'] ) {
					$utl_ues_settings['icon_color_activeborder'] = $icnhvrborder;
				}
				$data .= 'data-activeborder="' . esc_attr( $utl_ues_settings['icon_color_activeborder'] ) . '"';

			}
				$data .= 'data-effect="' . esc_attr( $utl_ues_settings['exp_effect'] ) . '"';
				$data .= 'data-override="' . esc_attr( $utl_ues_settings['map_override'] ) . '"';

				/*---active color ----------*/
			if ( '' == $utl_ues_settings['title_active'] ) {
				$utl_ues_settings['title_active'] = $utl_ues_settings['text_hovercolor'];
			}
				$data .= 'data-activetitle="' . esc_attr( $utl_ues_settings['title_active'] ) . '"';

			if ( ' ' == $utl_ues_settings['title_active_bg'] ) {
				$utl_ues_settings['title_active_bg'] = $utl_ues_settings['bghovercolor'];
			}
				$data .= 'data-activebg="' . esc_attr( $utl_ues_settings['title_active_bg'] ) . '"';

				/*----active icon --------*/

				/*------------icon style---------*/
				$iconoutput  = '';
				$newsrc      = '';
				$src1        = '';
				$img_ext     = '';
				$style       = '';
				$css_trans   = '';
				$iconbgstyle = '';
			if ( 'custom' == $utl_ues_settings['icon_type'] ) {

				if ( '' !== $utl_ues_settings['icon_img'] ) {

					$img = apply_filters( 'ult_get_img_single', $utl_ues_settings['icon_img'], 'url', 'large' );

					$newimg = apply_filters( 'ult_get_img_single', $utl_ues_settings['new_icon_img'], 'url', 'large' );

					$newsrc = $newimg;
					$src1   = $img;
					$alt    = apply_filters( 'ult_get_img_single', $utl_ues_settings['icon_img'], 'alt' );

					if ( 'none' !== $utl_ues_settings['icon_style'] ) {
						if ( '' !== $utl_ues_settings['icon_color_bg'] ) {
							$style .= 'background:' . $utl_ues_settings['icon_color_bg'] . ';';
						}
					}
					if ( 'circle' == $utl_ues_settings['icon_style'] ) {
						$el_class .= ' uavc-circle ';
						$img_ext  .= 'ult_circle ';
					}
					if ( 'square' == $utl_ues_settings['icon_style'] ) {
						$el_class .= ' uavc-square ';
						$img_ext  .= 'ult_square ';
					}
					if ( 'advanced' == $utl_ues_settings['icon_style'] && '' !== $utl_ues_settings['icon_border_style'] ) {
						$style .= 'border-style:' . $utl_ues_settings['icon_border_style'] . ';';
						$style .= 'border-color:' . $utl_ues_settings['icon_color_border'] . ';';
						$style .= 'border-width:' . $utl_ues_settings['icon_border_size'] . 'px;';
						$style .= 'padding:' . $utl_ues_settings['icon_border_spacing'] . 'px;';
						$style .= 'border-radius:' . $utl_ues_settings['icon_border_radius'] . 'px;';
					}
					if ( ! empty( $img ) ) {

						if ( 'center' == $utl_ues_settings['icon_align'] ) {
							$style .= 'display:inline-block;';
						}
						$iconoutput .= "\n" . '<br><span class="aio-icon-img ' . esc_attr( $el_class ) . ' ' . 'ult_expsection_icon " style="font-size:' . esc_attr( $utl_ues_settings['img_width'] ) . 'px;' . esc_attr( $style ) . '" ' . $css_trans . '>'; // phpcs:ignore Generic.Strings.UnnecessaryStringConcat.Found
						$iconoutput .= "\n\t" . '<img class="img-icon ult_exp_img ' . esc_attr( $img_ext ) . '" alt="' . esc_attr( $alt ) . '" src="' . esc_url( apply_filters( 'ultimate_images', $img ) ) . '" />';
						$iconoutput .= "\n" . '</span>';
					}
					if ( ! empty( $img ) ) {

						$iconoutput = $iconoutput;
					} else {
						$iconoutput = '';
					}
				}
			} else {
				if ( '' !== $utl_ues_settings['icon'] ) {
					if ( '' !== $utl_ues_settings['icon_color'] ) {
						$style .= 'color:' . $utl_ues_settings['icon_color'] . ';';
					}
					if ( 'none' !== $utl_ues_settings['icon_style'] ) {
						if ( '' !== $utl_ues_settings['icon_color_bg'] ) {
							$style .= 'background:' . $utl_ues_settings['icon_color_bg'] . ';';
						}
					}
					if ( 'advanced' == $utl_ues_settings['icon_style'] ) {
						$style .= 'border-style:' . $utl_ues_settings['icon_border_style'] . ';';
						$style .= 'border-color:' . $utl_ues_settings['icon_color_border'] . ';';
						$style .= 'border-width:' . $utl_ues_settings['icon_border_size'] . 'px;';
						$style .= 'width:' . $utl_ues_settings['icon_border_spacing'] . 'px;';
						$style .= 'height:' . $utl_ues_settings['icon_border_spacing'] . 'px;';
						$style .= 'line-height:' . $utl_ues_settings['icon_border_spacing'] . 'px;';
						$style .= 'border-radius:' . $utl_ues_settings['icon_border_radius'] . 'px;';
					}
					if ( '' !== $utl_ues_settings['icon_size'] ) {
						$style .= 'font-size:' . $utl_ues_settings['icon_size'] . 'px;';
					}
					if ( 'left' !== $utl_ues_settings['icon_align'] ) {
						$style .= 'display:inline-block;';
					}
					if ( '' !== $utl_ues_settings['icon'] ) {
						$iconoutput .= "\n" . '<span class="aio-icon  ' . esc_attr( $utl_ues_settings['icon_style'] ) . ' ' . esc_attr( $el_class ) . ' ult_expsection_icon " ' . $css_trans . ' style="' . esc_attr( $style ) . '">';
						$iconoutput .= "\n\t" . '<i class="' . esc_attr( $utl_ues_settings['icon'] ) . ' ult_ex_icon"  ></i>';
						$iconoutput .= "\n" . '</span>';
					}
					if ( '' !== $utl_ues_settings['icon'] && 'none' !== $utl_ues_settings['icon'] ) {
						$iconoutput = $iconoutput;
					} else {
						$iconoutput = '';
					}
				}
			}

			/*----------- image replace ----------------*/

			$data .= 'data-img="' . esc_url( $src1 ) . '"';
			if ( '' == $newsrc ) {
				$newsrc = $src1;
			}
			$data .= 'data-newimg="' . esc_url( $newsrc ) . '"';

			/*------------header bg style---------*/

			$headerstyle = '';
			if ( '' != $utl_ues_settings['text_color'] ) {
				$headerstyle .= 'color:' . $utl_ues_settings['text_color'] . ';';
			}
			if ( '' != $utl_ues_settings['background_color'] ) {
				$headerstyle .= 'background-color:' . $utl_ues_settings['background_color'] . ';';
			}

			if ( function_exists( 'get_ultimate_font_family' ) ) {
					$mhfont_family = get_ultimate_font_family( $utl_ues_settings['font_family'] );
				if ( '' != $mhfont_family ) {
					$headerstyle .= 'font-family:' . $mhfont_family . ';';
				}
			}
			if ( function_exists( 'get_ultimate_font_style' ) ) {
				$headerstyle .= get_ultimate_font_style( $utl_ues_settings['heading_style'] );
			}

			if ( is_numeric( $utl_ues_settings['title_font_size'] ) ) {
				$utl_ues_settings['title_font_size'] = 'desktop:' . $utl_ues_settings['title_font_size'] . 'px;';
			}
			if ( is_numeric( $utl_ues_settings['title_line_ht'] ) ) {
				$utl_ues_settings['title_line_ht'] = 'desktop:' . $utl_ues_settings['title_line_ht'] . 'px;';
			}
			$ult_expandable_id   = 'uvc-exp-wrap-' . wp_rand( 1000, 9999 );
			$ult_expandable_args = array(
				'target'      => '#' . $ult_expandable_id . '', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $utl_ues_settings['title_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $utl_ues_settings['title']_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $utl_ues_settings['title_line_ht'],
				),
			);
			$data_list           = get_ultimate_vc_responsive_media_css( $ult_expandable_args );
			$headerstyle        .= $utl_ues_settings['title_margin'];
			$headerstyle        .= $utl_ues_settings['title_padding'];

			/*---------------title padding---------------------*/
			$css_class = '';
			$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $utl_ues_settings['css'], ' ' ), 'ultimate_exp_section', $atts );
			$css_class = esc_attr( $css_class );

			/*---------------desc padding---------------------*/
			$desc_css_class = '';
			$desc_css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $utl_ues_settings['cont_css'], ' ' ), 'ultimate_exp_section', $atts );
			$desc_css_class = esc_attr( $desc_css_class );

			/*---------------desc padding---------------------*/
			$icon_css_class = '';
			$icon_css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $utl_ues_settings['iconmargin_css'], ' ' ), 'ultimate_exp_section', $atts );
			$icon_css_class = esc_attr( $icon_css_class );

			/*
			--------------------- full width row settings---------------------
			*/

			/*------------content style--------------------------*/
			$cnt_style = '';

			if ( '' != $utl_ues_settings['cnt_bg_color'] ) {
				$cnt_style .= 'background-color:' . $utl_ues_settings['cnt_bg_color'] . ';';
			}

			$cnt_style .= $utl_ues_settings['desc_padding'];
			$cnt_style .= $utl_ues_settings['desc_margin'];

			$position = '';
			if ( 'left' == $utl_ues_settings['icon_align'] ) {
				$position = 'ult_expleft_icon';
			}
			if ( 'right' == $utl_ues_settings['icon_align'] ) {
				$position = 'ult_expright_icon';
			}
			$top         = '';
			$output      = '';
			$icon_output = '';
			$text_align  = '';
			if ( 'top' == $utl_ues_settings['icon_align'] ) {
				if ( 'custom' == $utl_ues_settings['icon_type'] ) {
					$text_align .= 'text-align:center;';
				} else {
					$text_align .= 'text-align:center;';
				}
			}
			$text_align .= $utl_ues_settings['icon_margin'];

			$headerstyle .= 'text-align:' . $utl_ues_settings['title_alignment'] . ';';

			if ( '' != $iconoutput ) {
				$icon_output = '	<div class="ult-just-icon-wrapper ult_exp_icon">
					<div class="align-icon ' . esc_attr( $icon_css_class ) . '">
						' . $iconoutput . '
					</div>
				</div>';
			}

			if ( empty( $iconoutput ) || ' ' == $iconoutput ) {

					$icon_output = '';
			}
			$section_style = ' ';
			if ( ' ' !== $utl_ues_settings['section_width'] ) {
				$section_style = 'max-width:' . $utl_ues_settings['section_width'] . 'px;';
			}

			$output .= '<div class="ult_exp_section_layer ' . esc_attr( $is_vc_49_plus ) . ' ' . esc_attr( $utl_ues_settings['extra_class'] ) . '" >
	<div id="' . esc_attr( $ult_expandable_id ) . '"  ' . $data_list . ' class="ult_exp_section  ult-responsive ' . esc_attr( $css_class ) . '" style="' . esc_attr( $headerstyle ) . '" ' . $data . '>';

			if ( 'left' == $utl_ues_settings['icon_align'] ) {
				$output .= '<div class="ult_exp_section-main ' . esc_attr( $position ) . '">' . $icon_output . '
				<div class="ult_expheader" >' . $utl_ues_settings['title'] . '
				</div>
			</div>
		</div>';
			} elseif ( 'top' == $utl_ues_settings['icon_align'] ) {
				$output .= '<div class="ult_exp_section-main ' . esc_attr( $position ) . '">
						' . $icon_output . '
						<div class="ult_expheader" >' . $utl_ues_settings['title'] . '
						 </div></div>
				</div>';

			} else {

				$output .= '<div  class="ult_exp_section-main ' . esc_attr( $position ) . '">
					<div class="ult_expheader" >' . $utl_ues_settings['title'] . '
					 </div>' . $icon_output . '</div>
				</div>';
			}
			if ( '' != $content ) {
				$output .= '<div class="ult_exp_content ' . esc_attr( $desc_css_class ) . '" style="' . esc_attr( $cnt_style ) . '">';

				$output .= '<div class="ult_ecpsub_cont" style="' . esc_attr( $section_style ) . '" >';
				$output .= do_shortcode( $content );
				$output .= '</div>';
			}
			// <!--end of ult_ecpsub_cont-->
			$output .= '</div>

			</div>';
			// <!--end of exp_content-->

			if ( ' ' != $utl_ues_settings['title'] || ' ' != $utl_ues_settings['new_title'] ) {
				return $output;
			}

		}

		/**
		 * For vc map check
		 *
		 * @since ----
		 * @access public
		 */
		public function ultimate_ultimate_exp_section() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'                    => __( 'Expandable Section' ),
						'base'                    => 'ultimate_exp_section',
						'icon'                    => 'uvc_expandable',
						'class'                   => 'uvc_expandable',
						'as_parent'               => array( 'except' => 'ultimate_exp_section' ),
						'category'                => __( 'Ultimate VC Addons', 'ultimate_vc' ),
						'description'             => __( 'Add a Expandable Section.', 'ultimate_vc' ),
						'content_element'         => true,
						'front_enqueue_css'       => UAVC_URL . 'assets/css/expandable-section.css',
						'front_enqueue_js'        => UAVC_URL . 'assets/js/expandable-section.js',
						'controls'                => 'full',
						'show_settings_on_create' => true,

						'params'                  => array(
							// Play with icon selector.
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Title ', 'ultimate_vc' ),
								'param_name' => 'title',
								'value'      => '',

							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Title After Click ', 'ultimate_vc' ),
								'param_name'  => 'new_title',
								'value'       => '',
								'description' => __( 'Keep empty if you want to dispaly same title as previous.', 'ultimate_vc' ),

							),

							/*-----------general------------*/

							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Title Color', 'ultimate_vc' ),
								'param_name' => 'text_color',
								'value'      => '',

								'group'      => 'Color',
							),
							array(
								'type'             => 'colorpicker',
								'class'            => '',
								'heading'          => __( 'Title Background Color', 'ultimate_vc' ),
								'param_name'       => 'background_color',
								'value'            => '',
								'group'            => 'Color',
								'edit_field_class' => 'vc_col-sm-12 vc_column ult_space_border',

							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Title Hover Color', 'ultimate_vc' ),
								'param_name' => 'text_hovercolor',
								'value'      => '',
								'group'      => 'Color',
							),

							array(
								'type'             => 'colorpicker',
								'class'            => '',
								'heading'          => __( 'Title Hover Background Color', 'ultimate_vc' ),
								'param_name'       => 'bghovercolor',
								'value'            => '',
								'group'            => 'Color',
								'edit_field_class' => 'vc_col-sm-12 vc_column ult_space_border',

							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Title Active Color', 'ultimate_vc' ),
								'param_name' => 'title_active',
								'value'      => '',
								'group'      => 'Color',
							),
							array(
								'type'             => 'colorpicker',
								'class'            => '',
								'heading'          => __( 'Title Active Background Color', 'ultimate_vc' ),
								'param_name'       => 'title_active_bg',
								'value'            => '',
								'group'            => 'Color',
								'edit_field_class' => 'vc_col-sm-12 vc_column ult_space_border',

							),
							/*--container bg color---*/
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Content Background Color', 'ultimate_vc' ),
								'param_name' => 'cnt_bg_color',
								'value'      => '',
								'group'      => 'Color',

							),

							/*---icon---*/
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'btn1_icon_setting',
								'text'             => __( 'Icon / Image ', 'ultimate_vc' ),
								'value'            => '',
								'class'            => '',
								'group'            => __( 'Icon', 'ultimate_vc' ),
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
								'group'       => __( 'Icon', 'ultimate_vc' ),
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
								'group'       => __( 'Icon', 'ultimate_vc' ),
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
								'group'       => __( 'Icon', 'ultimate_vc' ),
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
								'group'       => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'btn1_icon_setting',
								'text'             => '',
								'value'            => '',
								'class'            => '',
								'group'            => __( 'Icon', 'ultimate_vc' ),
								'edit_field_class' => 'ult-param-heading-wrapper  vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'icon_manager',
								'class'       => '',
								'heading'     => __( 'Select Icon For On Click ', 'ultimate_vc' ),
								'param_name'  => 'new_icon',
								'value'       => '',
								'description' => __( "Click and select icon of your choice. If you can't find the one that suits for your purpose", 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-font-icon-manager' target='_blank' rel='noopener'>" . __( 'add new here', 'ultimate_vc' ) . '</a>.',
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
								'group'       => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'        => 'ult_img_single',
								'class'       => '',
								'heading'     => __( 'Upload Image On Click:', 'ultimate_vc' ),
								'param_name'  => 'new_icon_img',

								'value'       => '',
								'description' => __( 'Upload the custom image icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'custom' ),
								),
								'group'       => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Icon / Image Position', 'ultimate_vc' ),
								'param_name' => 'icon_align',
								'value'      => array(
									'Bottom' => '',
									'Top'    => 'top',
									'Left'   => 'left',
									'Right'  => 'right',
								),
								'group'      => __( 'Icon', 'ultimate_vc' ),
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
								'group'       => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Icon Color', 'ultimate_vc' ),
								'param_name' => 'icon_color',
								'value'      => '',
								'dependency' => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
								'group'      => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Icon Hover Color', 'ultimate_vc' ),
								'param_name' => 'icon_hover_color',
								'value'      => '',
								'dependency' => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
								'group'      => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Icon Active Color', 'ultimate_vc' ),
								'param_name' => 'icon_active_color',
								'value'      => '',
								'dependency' => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
								'group'      => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon / Image Style', 'ultimate_vc' ),
								'param_name'  => 'icon_style',
								'value'       => array(
									'Simple'            => 'none',
									'Circle Background' => 'circle',
									'Square Background' => 'square',
									'Design your own'   => 'advanced',
								),
								'description' => __( 'We have given three quick preset if you are in a hurry. Otherwise, create your own with various options.', 'ultimate_vc' ),
								'group'       => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Icon / Image Background Color ', 'ultimate_vc' ),
								'param_name' => 'icon_color_bg',
								'value'      => '',
								'dependency' => array(
									'element' => 'icon_style',
									'value'   => array( 'circle', 'square', 'advanced' ),
								),
								'group'      => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Icon / Image Hover Background Color ', 'ultimate_vc' ),
								'param_name' => 'icon_color_hoverbg',
								'value'      => '',
								'dependency' => array(
									'element' => 'icon_style',
									'value'   => array( 'circle', 'square', 'advanced' ),
								),
								'group'      => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Icon / Image Active Background Color ', 'ultimate_vc' ),
								'param_name' => 'icon_active_color_bg',
								'value'      => '',
								'dependency' => array(
									'element' => 'icon_style',
									'value'   => array( 'circle', 'square', 'advanced' ),
								),
								'group'      => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon / Image Border Style', 'ultimate_vc' ),
								'param_name'  => 'icon_border_style',
								'value'       => array(
									'Solid'  => '',

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
								'group'       => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Icon / Image Border Color', 'ultimate_vc' ),
								'param_name' => 'icon_color_border',
								'value'      => '',
								'dependency' => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
								'group'      => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Icon / Image Hover Border Color', 'ultimate_vc' ),
								'param_name' => 'icon_color_hoverborder',
								'value'      => '',
								'dependency' => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
								'group'      => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Icon / Image Active Border Color', 'ultimate_vc' ),
								'param_name' => 'icon_color_activeborder',
								'value'      => '',
								'dependency' => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
								'group'      => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Icon / Image Border Width', 'ultimate_vc' ),
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
								'group'       => __( 'Icon', 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Icon / Image Border Radius', 'ultimate_vc' ),
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
								'group'       => __( 'Icon', 'ultimate_vc' ),
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
								'group'       => __( 'Icon', 'ultimate_vc' ),

							),

							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Effect ', 'ultimate_vc' ),
								'param_name' => 'exp_effect',
								'value'      => array(
									'Slide' => '',
									'Fade'  => 'fadeToggle',

								),

							),

							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Custom CSS Class', 'ultimate_vc' ),
								'param_name'  => 'extra_class',
								'value'       => '',
								'description' => __( 'Ran out of options? Need more styles? Write your own CSS and mention the class name here.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Expandable Section Width Override', 'ultimate_vc' ),
								'param_name'  => 'map_override',
								'value'       => array(
									'Default Width'      => '0',
									"Apply 1st parent element's width" => '1',
									"Apply 2nd parent element's width" => '2',
									"Apply 3rd parent element's width" => '3',
									"Apply 4th parent element's width" => '4',
									"Apply 5th parent element's width" => '5',
									"Apply 6th parent element's width" => '6',
									"Apply 7th parent element's width" => '7',
									"Apply 8th parent element's width" => '8',
									"Apply 9th parent element's width" => '9',
									'Full Width '        => 'full',
									'Maximum Full Width' => 'ex-full',
								),
								'description' => __( "By default, the section will be given to the WPBakery Page Builder row. However, in some cases depending on your theme's CSS - it may not fit well to the container you are wishing it would. In that case you will have to select the appropriate value here that gets you desired output..", 'ultimate_vc' ),
								'group'       => __( 'Design ', 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Content Width', 'ultimate_vc' ),
								'param_name'  => 'section_width',
								'value'       => '',
								'min'         => 200,
								'max'         => 1200,
								'suffix'      => 'px',
								'description' => __( 'Adjust width of your content. Keep empty for full width.', 'ultimate_vc' ),
								'group'       => __( 'Design ', 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Top Gutter Position', 'ultimate_vc' ),
								'param_name'  => 'section_height',
								'value'       => '',
								'min'         => 0,
								'max'         => 1200,
								'suffix'      => 'px',
								'description' => __( 'After click distance between viewport top & expandable section.', 'ultimate_vc' ),
								'group'       => __( 'Design ', 'ultimate_vc' ),
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'title-setting',
								'text'             => __( 'Title ', 'ultimate' ),
								'value'            => '',
								'class'            => '',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',

							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Title Text Alignment', 'ultimate_vc' ),
								'param_name'  => 'title_alignment',
								'value'       => array(
									'Center' => 'center',
									'Left'   => 'left',
									'Right'  => 'right',
								),
								'description' => __( 'Select the title and icon alignment.', 'ultimate_vc' ),
								'group'       => __( 'Design ', 'ultimate_vc' ),
							),
							array(
								'type'        => 'ultimate_spacing',
								'heading'     => ' Title Margin ',
								'param_name'  => 'title_margin',
								'mode'        => 'margin',                    // margin/padding.
								'unit'        => 'px',                        // [required] px,em,%,all     Default all
								'positions'   => array(                   // Also set 'defaults'.
									'Top'    => '',
									'Right'  => '',
									'Bottom' => '',
									'Left'   => '',
								),
								'group'       => __( 'Design ', 'ultimate_vc' ),
								'description' => __( 'Add spacing from outside to titlebar.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'ultimate_spacing',
								'heading'     => ' Title Padding ',
								'param_name'  => 'title_padding',
								'mode'        => 'padding',                    // margin/padding.
								'unit'        => 'px',                        // [required] px,em,%,all     Default all
								'positions'   => array(                   // Also set 'defaults'.
									'Top'    => '',
									'Right'  => '',
									'Bottom' => '',
									'Left'   => '',
								),
								'group'       => __( 'Design ', 'ultimate_vc' ),
								'description' => __( 'Add spacing from inside to titlebar.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'title-setting',
								'text'             => __( 'Content ', 'ultimate' ),
								'value'            => '',
								'class'            => '',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',

							),

							array(
								'type'        => 'ultimate_spacing',
								'heading'     => ' Content Margin ',
								'param_name'  => 'desc_margin',
								'mode'        => 'margin',                    // margin/padding.
								'unit'        => 'px',                        // [required] px,em,%,all     Default all
								'positions'   => array(                   // Also set 'defaults'.
									'Top'    => '',
									'Right'  => '',
									'Bottom' => '',
									'Left'   => '',
								),
								'group'       => __( 'Design ', 'ultimate_vc' ),
								'description' => __( 'Add spacing from outside to content.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'ultimate_spacing',
								'heading'     => ' Content Padding ',
								'param_name'  => 'desc_padding',
								'mode'        => 'padding',                    // margin/padding.
								'unit'        => 'px',                        // [required] px,em,%,all     Default all
								'positions'   => array(                   // Also set 'defaults'.
									'Top'    => '',
									'Right'  => '',
									'Bottom' => '',
									'Left'   => '',
								),
								'group'       => __( 'Design ', 'ultimate_vc' ),
								'description' => __( 'Add spacing from inside to content.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'icn-setting',
								'text'             => __( 'Icon ', 'ultimate' ),
								'value'            => '',
								'class'            => '',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',

							),

							array(
								'type'        => 'ultimate_spacing',
								'heading'     => ' Icon Margin ',
								'param_name'  => 'icon_margin',
								'mode'        => 'margin',                    // margin/padding.
								'unit'        => 'px',                        // [required] px,em,%,all     Default all
								'positions'   => array(                   // Also set 'defaults'.
									'Top'    => '',
									'Right'  => '',
									'Bottom' => '',
									'Left'   => '',
								),
								'group'       => __( 'Design ', 'ultimate_vc' ),
								'description' => __( 'Add spacing to icon.', 'ultimate_vc' ),
							),

							/*---typography-------*/

							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Title Font Family', 'ultimate_vc' ),
								'param_name'  => 'font_family',
								'description' => __( 'Select the font of your choice. ', 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-google-font-manager' target='_blank' rel='noopener'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography ',
							),

							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'heading_style',

								'group'      => 'Typography ',
							),

							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'title_font_size',
								'unit'       => 'px',
								'media'      => array(

									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography ',
							),

							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'title_line_ht',
								'unit'       => 'px',
								'media'      => array(

									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography ',
							),
						),
						'js_view'                 => 'VcColumnView',
					)
				);
			}
		}

	}
}
if ( class_exists( 'AIO_Ultimate_Exp_Section' ) ) {

	$AIO_Ultimate_Exp_Section = new AIO_Ultimate_Exp_Section(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

}

if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Ultimate_Exp_Section' ) ) {
	/**
	 * Function that initializes Ultimate Expandable section Module
	 *
	 * @class WPBakeryShortCode_Ultimate_Exp_Section
	 */
	class WPBakeryShortCode_Ultimate_Exp_Section extends WPBakeryShortCodesContainer {
	}
}

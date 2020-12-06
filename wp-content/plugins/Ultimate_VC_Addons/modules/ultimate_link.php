<?php
/**
 * Add-on Name: Ultimate Creative link
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package UAVC Ultimate Creative link
 */

if ( ! class_exists( 'AIO_creative_link' ) ) {
	/**
	 * Function that initializes Ultimate Creative link Module
	 *
	 * @class AIO_creative_link
	 */
	class AIO_creative_link { // @codingStandardsIgnoreLine.
		/**
		 * Constructor function that constructs default values for the Ultimate Creative link module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'ultimate_createlink' ) );
			}
			add_shortcode( 'ult_createlink', array( $this, 'ult_createlink_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'creative_link_scripts' ), 1 );
		}

		/**
		 * Function to regester assets
		 *
		 * @since ----
		 * @access public
		 */
		public function creative_link_scripts() {

			Ultimate_VC_Addons::ultimate_register_style( 'ult_cllink', 'creative-link' );

			Ultimate_VC_Addons::ultimate_register_script( 'jquery.ult_cllink', 'creative-link', false, array( 'jquery' ), ULTIMATE_VERSION, false );
		}

		/**
		 * Shortcode handler function for stats Icon
		 *
		 * @since ----
		 * @param array $atts represts module attribuits.
		 * @access public
		 */
		public function ult_createlink_shortcode( $atts ) {

			$ult_links_settings = shortcode_atts(
				array(

					'btn_link'          => '',
					'text_color'        => '#333333',
					'text_hovercolor'   => '#333333',
					'background_color'  => '#ffffff',
					'bghovercolor'      => '',
					'font_family'       => '',
					'heading_style'     => '',
					'title_font_size'   => '',
					'title_line_ht'     => '',
					'link_hover_style'  => '',
					'border_style'      => 'solid',
					'border_color'      => '#333333',
					'border_hovercolor' => '#333333',
					'border_size'       => '1',
					'el_class'          => '',
					'dot_color'         => '#333333',
					'css'               => '',
					'title'             => '',
					'text_style'        => '',

				),
				$atts
			);

			$vc_version    = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
			$is_vc_49_plus = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'ult-adjust-bottom-margin' : '';

			$href     = '';
			$target   = '';
			$text     = '';
			$url      = '';
			$alt_text = '';
			$rel      = '';
			if ( '' !== $ult_links_settings['btn_link'] ) {
				$href = vc_build_link( $ult_links_settings['btn_link'] );

				$url      = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
				$target   = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
				$alt_text = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
				$rel      = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';

				if ( '' == $url ) {
					$url = 'javascript:void(0);';
				}
			} else {
				$url = 'javascript:void(0);';
			}

			/*--- design option---*/
			if ( '' !== $ult_links_settings['title'] ) {
				$text = $ult_links_settings['title'];
			} else {
				$text = $alt_text;
			}

			$css_class         = '';
			$title_style       = '';
			$secondtitle_style = '';
			$span_style        = '';
			$css_class         = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_links_settings['css'], ' ' ), 'ult_createlink', $atts );
			$css_class         = esc_attr( $css_class );

			if ( 'Style_2' == $ult_links_settings['link_hover_style'] ) {
				$span_style = 'background:' . $ult_links_settings['background_color'] . ';';     // background-color.
			}

			/*--- hover effect for link-----*/

			$data_link = '';
			if ( '' == $ult_links_settings['link_hover_style'] ) {
				$data_link .= 'data-textcolor="' . esc_attr( $ult_links_settings['text_color'] ) . '" ';
				$data_link .= 'data-texthover="' . esc_attr( $ult_links_settings['text_hovercolor'] ) . '"';
			} else {
				$data_link .= 'data-textcolor="' . esc_attr( $ult_links_settings['text_color'] ) . '" ';
				$data_link .= 'data-texthover="' . esc_attr( $ult_links_settings['text_hovercolor'] ) . '"';
			}

			if ( 'Style_2' == $ult_links_settings['link_hover_style'] ) {
				if ( '' == $ult_links_settings['text_hovercolor'] ) {
					$ult_links_settings['text_hovercolor'] = $ult_links_settings['text_color'];
				}
				if ( '' == $ult_links_settings['bghovercolor'] ) {
					$ult_links_settings['bghovercolor'] = $ult_links_settings['background_color'];
				}
				if ( '' == $ult_links_settings['text_hovercolor'] && '' == $ult_links_settings['bghovercolor'] ) {

					$data_link .= 'data-bgcolor="' . esc_attr( $ult_links_settings['background_color'] ) . '"';
					$data_link .= 'data-bghover="' . esc_attr( $ult_links_settings['background_color'] ) . '"';
				} else {

					$data_link .= 'data-bgcolor="' . esc_attr( $ult_links_settings['background_color'] ) . '"';
					$data_link .= 'data-bghover="' . esc_attr( $ult_links_settings['bghovercolor'] ) . '"';
				}
			}
			$data_link .= 'data-style="' . esc_attr( $ult_links_settings['link_hover_style'] ) . '"';

			/*--- border style---*/

			$data_border = '';
			if ( '' != $ult_links_settings['border_style'] ) {
				$data_border .= 'border-color:' . $ult_links_settings['border_color'] . ';';
				$data_border .= 'border-width:' . $ult_links_settings['border_size'] . 'px;';
				$data_border .= 'border-style:' . $ult_links_settings['border_style'] . ';';
			}

			$main_span         = '';
			$before            = '';
			$borderhover       = '';
			$ult_style2css     = '';
			$ult_style11css    = '';
			$after             = '';
			$style             = '';
			$class             = '';
			$id                = '';
			$colorstyle        = '';
			$borderstyle       = '';
			$style11_css_class = '';

			/*---- text typography----*/

			if ( '' != $ult_links_settings['text_style'] ) {
				$colorstyle .= 'float:' . $ult_links_settings['text_style'] . ';';
			}

			if ( function_exists( 'get_ultimate_font_family' ) ) {
					$mhfont_family = get_ultimate_font_family( $ult_links_settings['font_family'] );        // for font family.
				if ( '' != $mhfont_family ) {
					$colorstyle .= 'font-family:' . $mhfont_family . ';';
				}
			}
			if ( function_exists( 'get_ultimate_font_style' ) ) {
					// for font style.
				$colorstyle .= get_ultimate_font_style( $ult_links_settings['heading_style'] );
			}

			// Responsive param.

			if ( is_numeric( $ult_links_settings['title_font_size'] ) ) {
				$ult_links_settings['title_font_size'] = 'desktop:' . $ult_links_settings['title_font_size'] . 'px;';
			}
			if ( is_numeric( $ult_links_settings['title_line_ht'] ) ) {
				$ult_links_settings['title_line_ht'] = 'desktop:' . $ult_links_settings['title_line_ht'] . 'px;';
			}
			$creative_link_id        = 'creative-link-wrap-' . wp_rand( 1000, 9999 );
			$creative_link_args      = array(
				'target'      => '#' . $creative_link_id . ' .ult_colorlink', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_links_settings['title_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_links_settings['title_line_ht'],
				),
			);
			$creative_link_data_list = get_ultimate_vc_responsive_media_css( $creative_link_args );

			// font-size.
			$title_style .= 'color:' . $ult_links_settings['text_color'] . ';';// color.

			/*-- hover style---*/

			$id = '';
			if ( 'Style_1' == $ult_links_settings['link_hover_style'] ) {               // style1.
				$class      .= 'ult_cl_link_1';
				$colorstyle .= 'color:' . $ult_links_settings['text_color'] . ';'; // text color for bracket.
			} elseif ( 'Style_2' == $ult_links_settings['link_hover_style'] ) {              // style2.
				$class .= 'ult_cl_link_2';

			} elseif ( 'Style_3' == $ult_links_settings['link_hover_style'] ) {               // style3.
				$class       .= 'ult_cl_link_3';
				$data_border  = '';
				$data_border .= 'border-color:' . $ult_links_settings['border_color'] . ';';
				$data_border .= 'border-bottom-width:' . $ult_links_settings['border_size'] . 'px;';
				$data_border .= 'border-style:' . $ult_links_settings['border_style'] . ';';
				$borderstyle .= $data_border; // text color for btm border.
				$after       .= '<span class="ult_link_btm3 " style="' . esc_attr( $borderstyle ) . '"></span>';

			} elseif ( 'Style_4' == $ult_links_settings['link_hover_style'] ) {               // style4.
				$class       .= 'ult_cl_link_4';
				$data_border  = '';
				$data_border .= 'border-color:' . $ult_links_settings['border_color'] . ';';
				$data_border .= 'border-bottom-width:' . $ult_links_settings['border_size'] . 'px;';
				$data_border .= 'border-style:' . $ult_links_settings['border_style'] . ';';
				$borderstyle .= $data_border; // text color for btm border.
				$after       .= '<span class="ult_link_btm4 " style="' . esc_attr( $borderstyle ) . '"></span>';
			} elseif ( 'Style_6' == $ult_links_settings['link_hover_style'] ) {               // style6.
				$class      .= 'ult_cl_link_6';
				$colorstyle .= 'color:' . $ult_links_settings['text_hovercolor'] . ';';
				$after      .= '<span class="ult_btn6_link_top " data-color="' . esc_attr( $ult_links_settings['dot_color'] ) . '">•</span>';
			} elseif ( 'Style_5' == $ult_links_settings['link_hover_style'] ) {               // style5.
				$class       .= 'ult_cl_link_5';
				$data_border  = '';
				$data_border .= 'border-color:' . $ult_links_settings['border_color'] . ';';
				$data_border .= 'border-bottom-width:' . $ult_links_settings['border_size'] . 'px;';
				$data_border .= 'border-style:' . $ult_links_settings['border_style'] . ';';
				$borderstyle .= $data_border; // text color for btm border.
				$before       = '<span class="ult_link_top" style="' . esc_attr( $borderstyle ) . '"></span>';
				$after       .= '<span class="ult_link_btm  " style="' . esc_attr( $borderstyle ) . '"></span>';
			} elseif ( 'Style_7' == $ult_links_settings['link_hover_style'] ) {               // style7.
				$class       .= 'ult_cl_link_7';
				$borderstyle .= 'background:' . $ult_links_settings['border_color'] . ';';
				$borderstyle .= 'height:' . $ult_links_settings['border_size'] . 'px;';

				$before = '<span class="ult_link_top btn7_link_top " style="' . esc_attr( $borderstyle ) . '"></span>';
				$after .= '<span class="ult_link_btm  btn7_link_btm" style="' . esc_attr( $borderstyle ) . '"></span>';
			} elseif ( 'Style_8' == $ult_links_settings['link_hover_style'] ) {               // style8.
				$class       .= 'ult_cl_link_8';
				$borderstyle .= 'outline-color:' . $ult_links_settings['border_color'] . ';';
				$borderstyle .= 'outline-width:' . $ult_links_settings['border_size'] . 'px;';
				$borderstyle .= 'outline-style:' . $ult_links_settings['border_style'] . ';'; // text color for btm border.

				$borderhover .= 'outline-color:' . $ult_links_settings['border_hovercolor'] . ';';
				$borderhover .= 'outline-width:' . $ult_links_settings['border_size'] . 'px;';
				$borderhover .= 'outline-style:' . $ult_links_settings['border_style'] . ';'; // text color for btm border.

				$before = '<span class="ult_link_top ult_btn8_link_top " style="' . esc_attr( $borderstyle ) . '"></span>';
				$after .= '<span class="ult_link_btm  ulmt_btn8_link_btm" style="' . esc_attr( $borderhover ) . '"></span>';
			} elseif ( 'Style_9' == $ult_links_settings['link_hover_style'] ) {               // style9.
				$class       .= 'ult_cl_link_9';
				$borderstyle .= 'border-top-width:' . $ult_links_settings['border_size'] . 'px;';
				$borderstyle .= 'border-top-style:' . $ult_links_settings['border_style'] . ';';
				$borderstyle .= 'border-top-color:' . $ult_links_settings['border_color'] . ';';

				$before = '<span class="ult_link_top ult_btn9_link_top " style="' . esc_attr( $borderstyle ) . '"></span>';
				$after .= '<span class="ult_link_btm  ult_btn9_link_btm" style="' . esc_attr( $borderstyle ) . '"></span>';

			} elseif ( 'Style_10' == $ult_links_settings['link_hover_style'] ) {               // style10.
				$class       .= 'ult_cl_link_10';
				$borderstyle .= 'background:' . $ult_links_settings['border_color'] . ';';
				$borderstyle .= 'height:' . $ult_links_settings['border_size'] . 'px;';
				$span_style  .= 'background:' . $ult_links_settings['background_color'] . ';';
				if ( '' != $ult_links_settings['border_style'] ) {
					$span_style .= 'border-top:' . $ult_links_settings['border_size'] . 'px ' . $ult_links_settings['border_style'] . ' ' . $ult_links_settings['border_color'] . ';';
				}

				$span_style1  = '';
				$span_style1 .= 'background:' . $ult_links_settings['bghovercolor'] . ';';
			} elseif ( 'Style_11' == $ult_links_settings['link_hover_style'] ) {
				// style11.
				$style11_css_class = '';
				$style11_css_class = $css_class;
				$css_class         = '';
				$class            .= 'ult_cl_link_11';
				$span_style       .= 'background:' . $ult_links_settings['background_color'] . ';';
				$span_style1       = '';
				$span_style1      .= 'background:' . $ult_links_settings['bghovercolor'] . ';';
				$span_style1      .= 'color:' . $ult_links_settings['text_hovercolor'] . ';';

				// padding.
				$ult_style2css  = $css_class;
				$css_class      = '';
				$domain         = strstr( $ult_links_settings['css'], 'padding' );
				$domain         = ( explode( '}', $domain ) );
				$ult_style11css = $domain[0];

				$before = '<span class="ult_link_top ult_btn11_link_top " style="' . esc_attr( $span_style1 ) . ';' . esc_attr( $ult_style11css ) . '">' . $text . '</span>';

			}
			$text = $text;
			if ( 'Style_2' == $ult_links_settings['link_hover_style'] ) {
				$ult_style2css = $css_class;
				$css_class     = '';

			}
			$output = '';

			if ( 'Style_10' != $ult_links_settings['link_hover_style'] ) {

					$output .= '<span id="' . esc_attr( $creative_link_id ) . '" class="ult_main_cl ' . esc_attr( $is_vc_49_plus ) . ' ' . esc_attr( $ult_links_settings['el_class'] ) . ' ' . esc_attr( $style11_css_class ) . '" >
	 			<span class="' . esc_attr( $class ) . '  ult_crlink" >
					<a ' . $creative_link_data_list . ' ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $alt_text, $rel ) . ' class="ult_colorlink ult-responsive ' . esc_attr( $css_class ) . '" style="' . esc_attr( $colorstyle ) . ' "  ' . $data_link . '>
						' . $before . '
						<span data-hover="' . esc_attr( $text ) . '" style="' . esc_attr( $title_style ) . ';' . esc_attr( $span_style ) . ';' . esc_attr( $ult_style11css ) . '" class="ult_btn10_span  ' . esc_attr( $ult_style2css ) . ' ">' . $text . '</span>
						' . $after . '
					</a>
				</span>
			</span>';

			} elseif ( 'Style_10' == $ult_links_settings['link_hover_style'] ) {

				$output .= '<span id="' . esc_attr( $creative_link_id ) . '" class=" ult_main_cl  ' . esc_attr( $ult_links_settings['el_class'] ) . '" >
	 			<span  class="' . esc_attr( $class ) . '  ult_crlink" id="' . esc_attr( $id ) . '">
					<a ' . $creative_link_data_list . ' ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $alt_text, $rel ) . ' class="ult_colorlink  ult-responsive "  style="' . esc_attr( $colorstyle ) . ' "  ' . $data_link . '>
						<span   class="ult_btn10_span  ' . esc_attr( $css_class ) . '" style="' . esc_attr( $span_style ) . '" data-color="' . esc_attr( $ult_links_settings['border_color'] ) . '"  data-bhover="' . esc_attr( $ult_links_settings['bghovercolor'] ) . '" data-bstyle="' . esc_attr( $ult_links_settings['border_style'] ) . '">
							<span class="ult_link_btm  ult_btn10_link_top" style="' . esc_attr( $span_style1 ) . '">
								<span style="' . esc_attr( $title_style ) . ';color:' . esc_attr( $ult_links_settings['text_hovercolor'] ) . '" class="style10-span">' . $text . '</span>
							</span>
							<span style="' . esc_attr( $title_style ) . ';">' . $text . '</span>
						</span>

					</a>
				</span>
			</span>';
			}
			if ( '' != $text ) {
				$is_preset = false; // Preset setting array display.
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
					$text   .= '<br/>)';
					$output .= '<pre>';
					$output .= $text;
					$output .= '</pre>';
				}
				return $output;
			}
		}

		/**
		 * For vc map check
		 *
		 * @since ----
		 * @access public
		 */
		public function ultimate_createlink() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'        => __( 'Creative Link' ),
						'base'        => 'ult_createlink',
						'icon'        => 'uvc_creative_link',
						'category'    => __( 'Ultimate VC Addons', 'ultimate_vc' ),
						'description' => __( 'Add a custom link.', 'ultimate_vc' ),
						'params'      => array(
							// Play with icon selector.
							array(
								'type'        => 'textfield',
								'class'       => '',
								'admin_label' => true,
								'heading'     => __( 'Title', 'ultimate_vc' ),
								'param_name'  => 'title',
								'value'       => '',
							),
							array(
								'type'        => 'vc_link',
								'class'       => '',
								'heading'     => __( 'Link ', 'ultimate_vc' ),
								'param_name'  => 'btn_link',
								'value'       => '',
								'description' => __( 'Add a custom link or select existing page. You can remove existing link as well.', 'ultimate_vc' ),

							),

							/*---typography-------*/

							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'bt1typo-setting',
								'text'             => __( 'Typography', 'ultimate' ),
								'value'            => '',
								'class'            => '',
								'group'            => 'Typography ',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',

							),

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
								'class'      => 'font-size',
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
							/*-----------general------------*/
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'admin_label' => true,
								'heading'     => __( 'Link Style', 'ultimate_vc' ),
								'param_name'  => 'link_hover_style',
								'value'       => array(
									'None'     => '',
									'Style 1'  => 'Style_1',
									'Style 2'  => 'Style_2',
									'Style 3'  => 'Style_3',
									'Style 4'  => 'Style_4',
									'Style 5'  => 'Style_5',
									'Style 6'  => 'Style_6',
									'Style 7'  => 'Style_8',
									'Style 8'  => 'Style_9',
									'Style 9'  => 'Style_10',
									'Style 10' => 'Style_11',
								),
								'description' => __( 'Select the Hover style for Link.', 'ultimate_vc' ),

							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'button1bg_settng',
								'text'             => __( 'Color Settings', 'ultimate_vc' ),
								'value'            => '',
								'class'            => '',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Link Color', 'ultimate_vc' ),
								'param_name'  => 'text_color',
								'value'       => '#333333',
								'description' => __( 'Select text color for Link.', 'ultimate_vc' ),

							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Link Hover Color', 'ultimate_vc' ),
								'param_name'  => 'text_hovercolor',
								'value'       => '#333333',
								'description' => __( 'Select text hover color for Link.', 'ultimate_vc' ),

							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Link Background Color', 'ultimate_vc' ),
								'param_name'  => 'background_color',
								'value'       => '#ffffff',
								'description' => __( 'Select Background Color for link.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'link_hover_style',
									'value'   => array( 'Style_2', 'Style_10', 'Style_11' ),
								),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Link Background Hover Color', 'ultimate_vc' ),
								'param_name'  => 'bghovercolor',
								'value'       => '',
								'description' => __( 'Select background hover color for link.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'link_hover_style',
									'value'   => array( 'Style_2', 'Style_10', 'Style_11' ),
								),

							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Border Style', 'ultimate_vc' ),
								'param_name'  => 'border_style',
								'value'       => array(
									'Solid'  => 'solid',
									'Dashed' => 'dashed',
									'Dotted' => 'dotted',
									'Double' => 'double',
									'Inset'  => 'inset',
									'Outset' => 'outset',

								),
								'description' => __( 'Select the border style for link.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'link_hover_style',
									'value'   => array( 'Style_3', 'Style_4', 'Style_5', 'Style_7', 'Style_8', 'Style_9', 'Style_10' ),
								),

							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Link Border Color', 'ultimate_vc' ),
								'param_name'  => 'border_color',
								'value'       => '#333333',
								'description' => __( 'Select border color for link.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'border_style',
									'value'   => array( 'solid', 'dashed', 'dotted', 'double', 'inset', 'outset' ),
								),

							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Link Border HoverColor', 'ultimate_vc' ),
								'param_name'  => 'border_hovercolor',
								'value'       => '#333333',
								'description' => __( 'Select border hover color for link.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'link_hover_style',
									'value'   => array( 'Style_8' ),
								),

							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Link Border Width', 'ultimate_vc' ),
								'param_name'  => 'border_size',
								'value'       => 1,
								'min'         => 1,
								'max'         => 10,
								'suffix'      => 'px',
								'description' => __( 'Thickness of the border.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'border_style',
									'value'   => array( 'solid', 'dashed', 'dotted', 'double', 'inset', 'outset' ),
								),

							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Link Dot Color', 'ultimate_vc' ),
								'param_name'  => 'dot_color',
								'value'       => '#333333',
								'description' => __( 'Select color for dots.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'link_hover_style',
									'value'   => array( 'Style_6' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Link Alignment', 'ultimate_vc' ),
								'param_name'  => 'text_style',
								'value'       => array(
									'Center' => ' ',
									'Left'   => 'left',
									'Right'  => 'right',

								),
								'description' => __( 'Select the text align for link.', 'ultimate_vc' ),
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
								'type'             => 'css_editor',
								'heading'          => __( 'Css', 'ultimate_vc' ),
								'param_name'       => 'css',
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
if ( class_exists( 'AIO_creative_link' ) ) {

	$AIO_creative_link = new AIO_creative_link();// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

}
if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_ult_createlink' ) ) {
	/**
	 * Function that initializes Ultimate Creative link Module
	 *
	 * @class WPBakeryShortCode_ult_createlink
	 */
	class WPBakeryShortCode_ult_createlink extends WPBakeryShortCode { // @codingStandardsIgnoreLine.
	}
}

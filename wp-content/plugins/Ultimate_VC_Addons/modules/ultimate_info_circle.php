<?php
/**
 * Add-on Name: Info Circle for WPBakery Page Builder
 * Add-on URI: http://dev.brainstormforce.com
 *
 * @package Ultimate Addons for WPBakery Page Builder
 */

if ( ! class_exists( 'Ultimate_Info_Circle' ) ) {
	/**
	 * Ultimate_Info_Circle initial setup
	 */
	class Ultimate_Info_Circle {
		/**
		 * Constructor function.
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'add_info_circle' ) );
			}
			add_action( 'wp_enqueue_scripts', array( $this, 'register_info_circle_assets' ), 1 );
			add_shortcode( 'info_circle', array( $this, 'info_circle' ) );
			add_shortcode( 'info_circle_item', array( $this, 'info_circle_item' ) );
		}
		/**
		 * Register info circle assets.
		 */
		public function register_info_circle_assets() {
			Ultimate_VC_Addons::ultimate_register_script( 'info-circle', 'info-circle', false, array( 'jquery' ), ULTIMATE_VERSION, false );

			Ultimate_VC_Addons::ultimate_register_script( 'info-circle-ui-effect', 'jquery-ui-effect', false, array( 'jquery' ), ULTIMATE_VERSION, false );

			Ultimate_VC_Addons::ultimate_register_style( 'info-circle', 'info-circle' );
		}
		/**
		 * Info circle content.
		 *
		 * @param array  $atts .
		 * @param string $content  .
		 */
		public function info_circle( $atts, $content = null ) {

			$clipped_circle      = '';
				$ult_info_circle = shortcode_atts(
					array(
						'edge_radius'           => '80',
						'visible_circle'        => '70',
						'start_degree'          => '90',
						'circle_type'           => '',
						'icon_position'         => 'full',
						'focus_on'              => 'hover',
						'eg_br_width'           => '1',
						'eg_br_style'           => 'none',
						'eg_border_color'       => '',
						'cn_br_style'           => 'none',
						'cn_br_width'           => '1',
						'cn_border_color'       => '',
						'highlight_style'       => 'info-circle-highlight-style',
						'icon_size'             => '32',
						'img_icon_size'         => '32',
						'eg_padding'            => '50',
						'icon_diversion'        => '',
						'icon_show'             => 'show',
						'content_icon_size'     => '32',
						'content_color'         => '',
						'content_bg'            => '',
						'responsive'            => 'on',
						'responsive_breakpoint' => '800',
						'auto_slide'            => 'off',
						'auto_slide_duration'   => '3',
						'icon_launch'           => '',
						'icon_launch_duration'  => '1',
						'icon_launch_delay'     => '0.2',
						'el_class'              => '',
						'title_font'            => '',
						'title_font_style'      => '',
						'title_font_size'       => '',
						'title_line_height'     => '',
						'desc_font'             => '',
						'desc_font_style'       => '',
						'desc_font_size'        => '',
						'desc_line_height'      => '',
					),
					$atts
				);

			$vc_version    = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
			$is_vc_49_plus = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'ult-adjust-bottom-margin' : '';

			$uniq         = uniqid();
			$browser_info = ult_getBrowser();

			global $title_style_inline, $desc_style_inline, $info_circle_id, $info_circle_data_list;

			/* ---- main title styles ---- */
			if ( '' != $ult_info_circle['title_font'] ) {
				$title_font_family = get_ultimate_font_family( $ult_info_circle['title_font'] );
				if ( '' != $title_font_family ) {
					$title_style_inline = 'font-family:\'' . $title_font_family . '\';';
				}
			}
			// main heading font style.
			$title_style_inline .= get_ultimate_font_style( $ult_info_circle['title_font_style'] );
			// attach font size if set.

			// responsive param for title.

			if ( is_numeric( $ult_info_circle['title_font_size'] ) ) {
				$ult_info_circle['title_font_size'] = 'desktop:' . $ult_info_circle['title_font_size'] . 'px;';
			}

			if ( is_numeric( $ult_info_circle['title_line_height'] ) ) {
				$ult_info_circle['title_line_height'] = 'desktop:' . $ult_info_circle['title_line_height'] . 'px;';
			}

			$info_circle_id   = 'info-cirlce-wrap-' . wp_rand( 1000, 9999 );
			$info_circle_args = array(
				'target'      => '#' . $info_circle_id . ' .responsive-font-class h3.new-cust-responsive-class', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_info_circle['title_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_info_circle['title_line_height'],
				),
			);

			$info_circle_data_list = get_ultimate_vc_responsive_media_css( $info_circle_args );

			/* ---- description styles ---- */
			if ( '' != $ult_info_circle['desc_font'] ) {
				$desc_font_family = get_ultimate_font_family( $ult_info_circle['desc_font'] );
				if ( '' != $desc_font_family ) {
					$desc_style_inline = 'font-family:\'' . $desc_font_family . '\';';
				}
			}
			// main heading font style.
			$desc_style_inline .= get_ultimate_font_style( $ult_info_circle['desc_font_style'] );
			// attach font size if set.

			// Responsive param for Description.

			if ( is_numeric( $ult_info_circle['desc_font_size'] ) ) {
				$ult_info_circle['desc_font_size'] = 'desktop:' . $ult_info_circle['desc_font_size'] . 'px;';
			}

			if ( is_numeric( $ult_info_circle['desc_line_height'] ) ) {
				$ult_info_circle['desc_line_height'] = 'desktop:' . $ult_info_circle['desc_line_height'] . 'px;';
			}

			$info_circle_desc_args = array(
				'target'      => '#' . $info_circle_id . ' .responsive-font-class *', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_info_circle['desc_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_info_circle['desc_line_height'],
				),
			);

			$info_circle_desc_data_list = get_ultimate_vc_responsive_media_css( $info_circle_desc_args );

			$style    = '';
			$style1   = '';
			$style3   = '';
			$ex_class = '';

			if ( stripos( $browser_info['name'], 'safari' ) == true && stripos( $browser_info['version'], '11.' ) == true ) {
				if ( 'none' != $ult_info_circle['eg_br_style'] && '' != $ult_info_circle['eg_br_width'] && '' != $ult_info_circle['eg_border_color'] ) {
					$style .= 'box-shadow: 0 0 0 ' . $ult_info_circle['eg_br_width'] . 'px ' . $ult_info_circle['eg_border_color'] . ';';
				}
			} else {
				if ( 'none' != $ult_info_circle['eg_br_style'] && '' != $ult_info_circle['eg_br_width'] && '' != $ult_info_circle['eg_border_color'] ) {
					$style .= 'border:' . $ult_info_circle['eg_br_width'] . 'px ' . $ult_info_circle['eg_br_style'] . ' ' . $ult_info_circle['eg_border_color'] . ';';
				}
			}
			if ( stripos( $browser_info['name'], 'safari' ) == true && stripos( $browser_info['version'], '11.' ) == true ) {
				if ( 'none' != $ult_info_circle['cn_br_style'] && '' != $ult_info_circle['cn_br_width'] && '' != $ult_info_circle['cn_border_color'] ) {
					$style1 .= 'box-shadow: 0 0 0 ' . $ult_info_circle['cn_br_width'] . 'px ' . $ult_info_circle['cn_border_color'] . ';';
				}
			} else {
				if ( 'none' != $ult_info_circle['cn_br_style'] && '' != $ult_info_circle['cn_br_width'] && '' != $ult_info_circle['cn_border_color'] ) {
					$style1 .= 'border:' . $ult_info_circle['cn_br_width'] . 'px ' . $ult_info_circle['cn_br_style'] . ' ' . $ult_info_circle['cn_border_color'] . ';';
				}
			}
			$style1 .= 'background-color:' . $ult_info_circle['content_bg'] . ';color:' . $ult_info_circle['content_color'] . ';';
			$style1 .= 'width:' . $ult_info_circle['eg_padding'] . '%;height:' . $ult_info_circle['eg_padding'] . '%;margin:' . ( ( 100 - $ult_info_circle['eg_padding'] ) / 2 ) . '%;';
			if ( '' != $ult_info_circle['el_class'] ) {
				$ex_class = $ult_info_circle['el_class'];
			}
			if ( 'on' == $ult_info_circle['responsive'] ) {
				$ex_class .= ' info-circle-responsive';
			}
			if ( 'show' == $ult_info_circle['icon_show'] ) {
				$ult_info_circle['content_icon_size'] = $ult_info_circle['content_icon_size'];
			} else {
				$ult_info_circle['content_icon_size'] = '';
			}
			if ( '' != $ult_info_circle['edge_radius'] ) {
				$style .= 'width:' . $ult_info_circle['edge_radius'] . '%;';
			}
			$style .= 'opacity:0;';
			if ( '' == $ult_info_circle['circle_type'] ) {
				$ult_info_circle['circle_type'] = 'info-c-full-br';
			}

			if ( 'full' == $ult_info_circle['icon_position'] ) {
				$circle_type_extended = 'full-circle';
			} else {
				if ( 90 == $ult_info_circle['icon_position'] ) {
					$circle_type_extended = 'left-circle';
				} elseif ( 270 == $ult_info_circle['icon_position'] ) {
					$circle_type_extended = 'right-circle';
				} elseif ( 180 == $ult_info_circle['icon_position'] ) {
					$circle_type_extended = 'top-circle';
				} elseif ( 0 == $ult_info_circle['icon_position'] ) {
					$circle_type_extended = 'bottom-circle';
				} else {
					$circle_type_extended = 'full-circle';
				}
			}

			$connector_position = '';
			if ( is_rtl() ) {
				$connector_position = 'right:' . esc_attr( $ult_info_circle['img_icon_size'] / 2 ) . 'px;';
			} else {
				$connector_position = 'left:' . esc_attr( $ult_info_circle['img_icon_size'] / 2 ) . 'px;';
			}

			if ( '' != $ult_info_circle['visible_circle'] && 100 != $ult_info_circle['visible_circle'] && 'full-circle' != $circle_type_extended ) {
				$clipped_circle = 'clipped-info-circle';
			}

			$output  = '<div class="info-wrapper ' . esc_attr( $is_vc_49_plus ) . '"><div id="info-circle-wrapper-' . esc_attr( $uniq ) . '" data-uniqid="' . esc_attr( $uniq ) . '" class="info-circle-wrapper ' . esc_attr( $ex_class ) . ' ' . esc_attr( $clipped_circle ) . '" data-half-percentage="' . esc_attr( $ult_info_circle['visible_circle'] ) . '" data-circle-type="' . esc_attr( $circle_type_extended ) . '">';
			$output .= '<div class="' . esc_attr( $ult_info_circle['circle_type'] ) . '" style=\'' . esc_attr( $style ) . '\' data-start-degree="' . esc_attr( $ult_info_circle['start_degree'] ) . '" data-divert="' . esc_attr( $ult_info_circle['icon_diversion'] ) . '" data-info-circle-angle="' . esc_attr( $ult_info_circle['icon_position'] ) . '" data-responsive-circle="' . esc_attr( $ult_info_circle['responsive'] ) . '" data-responsive-breakpoint="' . esc_attr( $ult_info_circle['responsive_breakpoint'] ) . '" data-launch="' . esc_attr( $ult_info_circle['icon_launch'] ) . '" data-launch-duration="' . esc_attr( $ult_info_circle['icon_launch_duration'] ) . '" data-launch-delay="' . esc_attr( $ult_info_circle['icon_launch_delay'] ) . '" data-slide-true="' . esc_attr( $ult_info_circle['auto_slide'] ) . '" data-slide-duration="' . esc_attr( $ult_info_circle['auto_slide_duration'] ) . '" data-icon-size="' . esc_attr( $ult_info_circle['icon_size'] ) . '" data-icon-show="' . esc_attr( $ult_info_circle['icon_show'] ) . '" data-icon-show-size="' . esc_attr( $ult_info_circle['content_icon_size'] ) . '" data-highlight-style="' . esc_attr( $ult_info_circle['highlight_style'] ) . '" data-focus-on="' . esc_attr( $ult_info_circle['focus_on'] ) . '">';

			$output .= '<div  class="icon-circle-list">';

			$output .= do_shortcode( $content );
			if ( 'full' != $ult_info_circle['icon_position'] ) {
				$output .= '<div class="info-circle-icons suffix-remove"></div>';
			}
			$output .= '</div>';
			$output .= '<div id="' . esc_attr( $info_circle_id ) . '" class="info-c-full" style="' . esc_attr( $style1 ) . '"><div class="info-c-full-wrap"></div>';
			$output .= '</div>';
			$output .= '</div>';
			if ( 'on' == $ult_info_circle['responsive'] ) {
				$output .= '<div class="smile_icon_list_wrap " data-content_bg="' . esc_attr( $ult_info_circle['content_bg'] ) . '" data-content_color="' . esc_attr( $ult_info_circle['content_color'] ) . '">
							<ul id="' . esc_attr( $info_circle_id ) . '" class="smile_icon_list left circle with_bg"><li class="icon_list_item" style="font-size:' . ( esc_attr( $ult_info_circle['img_icon_size'] ) * 3 ) . 'px;">';
				if ( $ult_info_circle['img_icon_size'] <= 120 ) {
					$output .= '
									<div class="icon_list_icon" style="font-size:' . esc_attr( $ult_info_circle['img_icon_size'] ) . 'px;width:' . esc_attr( $ult_info_circle['img_icon_size'] ) . 'px;height:' . esc_attr( $ult_info_circle['img_icon_size'] ) . 'px;line-height:1;">
										<i class="smt-pencil"></i>
									</div>
									<div  class="icon_description" style="font-size:' . esc_attr( $ult_info_circle['img_icon_size'] ) . 'px;">
										<div class="responsive-font-class ult-responsive" ' . $info_circle_desc_data_list . ' style="' . esc_attr( $desc_style_inline ) . '">
											<h3 ' . $info_circle_data_list . ' class="ult-responsive new-cust-responsive-class" style="' . esc_attr( $title_style_inline ) . '"></h3>
											<p></p>
										</div>
									</div>
									<div class="icon_list_connector" style=" border-style:' . esc_attr( $ult_info_circle['eg_br_style'] ) . ';border-color:' . esc_attr( $ult_info_circle['eg_border_color'] ) . '; ' . esc_attr( $connector_position ) . ' top:' . esc_attr( $ult_info_circle['img_icon_size'] ) . 'px;">
									</div>';
				} else {
					$output .= '
									<div class="icon_list_icon" style="font-size:' . esc_attr( $ult_info_circle['img_icon_size'] ) . 'px;width:' . esc_attr( $ult_info_circle['img_icon_size'] ) . 'px;height:' . esc_attr( $ult_info_circle['img_icon_size'] ) . 'px;float: none;margin: 30px 0;line-height:1;left: 50%;transform: translateX(-50%);">
										<i class="smt-pencil"></i>
									</div>
									<div  class="icon_description" style="font-size:' . esc_attr( $ult_info_circle['img_icon_size'] ) . 'px; text-align:center;">
										<div class="responsive-font-class ult-responsive" ' . $info_circle_desc_data_list . ' style="' . esc_attr( $desc_style_inline ) . '">
											<h3 ' . $info_circle_data_list . ' class="ult-responsive new-cust-responsive-class" style="' . esc_attr( $title_style_inline ) . '"></h3>
											<p></p>
										</div>
									</div>';
				}
							$output .= '</li></ul>
						</div>';
			}
			$output .= '</div></div>';
			return $output;
		}
		/**
		 * Redirect to menu position.
		 *
		 * @param string $atts .
		 * @param string $content .
		 * @return string $output .
		 */
		public function info_circle_item( $atts, $content = null ) {
			global $title_style_inline, $desc_style_inline, $info_circle_id, $info_circle_data_list, $info_circle_desc_data_list;
			// Do nothing.

			$contents                 = '';
			$radius                   = '';
			$icon_size                = '';
			$icon_html                = '';
			$style                    = '';
			$output                   = '';
			$style                    = '';
			$target                   = '';
			$link_title               = '';
			$rel                      = '';
			$padding_style            = '';
			$ult_info_circle_settings = shortcode_atts(
				array(
					'info_title'        => '',
					'info_icon'         => '',
					'icon_color'        => '',
					'icon_bg_color'     => '',
					'info_img'          => '',
					'icon_type'         => 'selector',
					'icon_br_style'     => 'none',
					'icon_br_width'     => '1',
					'icon_br_padding'   => '',
					'icon_border_color' => '',
					'contents'          => '',
					'el_class'          => '',
					'ilink'             => '',
				),
				$atts
			);
			$icon_html                = '';
			$output                   = '';
			$icon_type_class          = '';
			if ( 'selector' == $ult_info_circle_settings['icon_type'] ) {
				$icon_html      .= '<i class="' . esc_attr( $ult_info_circle_settings['info_icon'] ) . ' info-circle-icon" ></i>';
				$icon_type_class = 'ult-info-circle-icon';
			} else {
				$img = apply_filters( 'ult_get_img_single', $ult_info_circle_settings['info_img'], 'url' );
				$alt = apply_filters( 'ult_get_img_single', $ult_info_circle_settings['info_img'], 'alt' );
				if ( '' == $alt ) {
					$alt = 'icon';
				}
				$icon_html      .= '<img class="info-circle-img-icon" alt="' . esc_attr( $alt ) . '" src="' . esc_url( apply_filters( 'ultimate_images', $img ) ) . '"/>';
				$icon_type_class = 'ult-info-circle-img';
			}
			if ( '' != $ult_info_circle_settings['icon_bg_color'] ) {
				$style .= 'background:' . $ult_info_circle_settings['icon_bg_color'] . ';';
			} else {
				$ult_info_circle_settings['el_class'] .= ' info-circle-icon-without-background ';
			}
			if ( '' != $ult_info_circle_settings['icon_color'] ) {
				$style .= 'color:' . $ult_info_circle_settings['icon_color'] . ';';
			}
			if ( '' != $ult_info_circle_settings['icon_br_padding'] ) {
				$padding_style = 'data-padding-style=' . $ult_info_circle_settings['icon_br_padding'];
				$style        .= 'padding:' . $ult_info_circle_settings['icon_br_padding'] . 'px;';

			}
			if ( 'none' != $ult_info_circle_settings['icon_br_style'] && '' != $ult_info_circle_settings['icon_br_width'] && '' != $ult_info_circle_settings['icon_border_color'] ) {
				$style .= 'border-style:' . $ult_info_circle_settings['icon_br_style'] . ';';
				$style .= 'border-width:' . $ult_info_circle_settings['icon_br_width'] . 'px;';
				$style .= 'border-color:' . $ult_info_circle_settings['icon_border_color'] . ';';
			}
			$href = vc_build_link( $ult_info_circle_settings['ilink'] );
			if ( ! empty( $href['url'] ) ) {

				$url        = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
				$target     = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
				$link_title = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
				$rel        = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
				$output    .= '<div class="info-circle-icons ' . esc_attr( $ult_info_circle_settings['el_class'] ) . '" style="' . esc_attr( $style ) . '" ' . $padding_style . '><div class="info-circle-link">
								<a class="info-circle-href" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . '></a>';
				$output    .= $icon_html;
				$output    .= '</div></div>';
			} else {
				$output .= '<div class="info-circle-icons ' . $ult_info_circle_settings['el_class'] . '" style="' . esc_attr( $style ) . '" ' . $padding_style . '>';
				$output .= $icon_html;
				$output .= '</div>';
			}
			$output .= '<div class="info-details" data-icon-class="' . esc_attr( $icon_type_class ) . '">';
			if ( ! empty( $href['url'] ) ) {

				$url        = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
				$target     = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
				$link_title = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
				$rel        = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
				$output    .= '<div class="info-circle-def"><div  class="info-circle-sub-def">
							<a class="info-circle-href" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . ' style="color:inherit;">' . $icon_html . '</a>
								<div class="responsive-font-class ult-responsive" ' . $info_circle_desc_data_list . '><h3 ' . $info_circle_data_list . ' class="info-circle-heading ult-responsive new-cust-responsive-class" style="' . esc_attr( $title_style_inline ) . '">' . $ult_info_circle_settings['info_title'] . '</h3>
								<div ' . $info_circle_desc_data_list . ' class="info-circle-text " style="' . esc_attr( $desc_style_inline ) . '">' . do_shortcode( $content ) . '</div>
							</div></div></div></div>';
			} else {
				$output .= '<div class="info-circle-def"><div  class="info-circle-sub-def">' . $icon_html . '<div class="responsive-font-class ult-responsive" ' . $info_circle_desc_data_list . '><h3 ' . $info_circle_data_list . ' class="info-circle-heading ult-responsive new-cust-responsive-class" style="' . esc_attr( $title_style_inline ) . '">' . $ult_info_circle_settings['info_title'] . '</h3><div ' . $info_circle_desc_data_list . ' class="info-circle-text " style="' . esc_attr( $desc_style_inline ) . '">' . do_shortcode( $content ) . '</div></div></div></div></div>';
			}
			return $output;
		}
		/**
		 * Add info circle.
		 */
		public function add_info_circle() {
			if ( function_exists( 'vc_map' ) ) {
				$thumbnail_tab   = 'Thumbnail';
				$information_tab = 'Information Area';
				$connector_tab   = 'Connector';
				$reponsive_tab   = 'Responsive';

				vc_map(
					array(
						'name'                    => __( 'Info Circle', 'ultimate_vc' ),
						'base'                    => 'info_circle',
						'class'                   => 'vc_info_circle',
						'icon'                    => 'vc_info_circle',
						'category'                => 'Ultimate VC Addons',
						'as_parent'               => array( 'only' => 'info_circle_item' ),
						'description'             => __( 'Information Circle', 'ultimate_vc' ),
						'content_element'         => true,
						'show_settings_on_create' => true,
						'params'                  => array(
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Select area to display thumbnail icons', 'ultimate_vc' ),
								'param_name' => 'icon_position',
								'value'      => array(
									__( 'Complete', 'ultimate_vc' ) => 'full',
									__( 'Top', 'ultimate_vc' )  => '180',
									__( 'Bottom', 'ultimate_vc' ) => '0',
									__( 'Left', 'ultimate_vc' ) => '90',
									__( 'Right', 'ultimate_vc' ) => '270',
								),
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Clipped Circle', 'ultimate_vc' ),
								'param_name' => 'visible_circle',
								'value'      => '70',
								'suffix'     => '%',
								'dependency' => array(
									'element' => 'icon_position',
									'value'   => array( '180', '270', '90', '0' ),
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Size of Information Circle', 'ultimate_vc' ),
								'param_name'  => 'edge_radius',
								'value'       => 80,
								'suffix'      => '%',
								'description' => __( 'Size of circle relative to container width.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Position of First Thumbnail', 'ultimate_vc' ),
								'param_name'  => 'start_degree',
								'value'       => 90,
								'max'         => 360,
								'suffix'      => '&deg; degree',
								'description' => __( 'The degree from where Info Circle will be displayed.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_position',
									'value'   => array( 'full' ),
								),
								'group'       => $thumbnail_tab,
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Distance Between Thumbnails & Information Circle', 'ultimate_vc' ),
								'param_name' => 'eg_padding',
								'value'      => array(
									__( 'Extra large', 'ultimate_vc' ) => '50',
									__( 'Large', 'ultimate_vc' ) => '60',
									__( 'Medium', 'ultimate_vc' ) => '70',
									__( 'Small', 'ultimate_vc' ) => '80',
								),
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Thumbnail Icon Size', 'ultimate_vc' ),
								'param_name' => 'icon_size',
								'value'      => 32,
								'suffix'     => 'px',
								'group'      => $thumbnail_tab,
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Responsive Icon/image Size', 'ultimate_vc' ),
								'param_name'  => 'img_icon_size',
								'value'       => 32,
								'suffix'      => 'px',
								'dependency'  => array(
									'element' => 'responsive',
									'value'   => array( 'on' ),
								),
								'group'       => $thumbnail_tab,
								'description' => __( 'This size of the thumbnails on breakpoint.', 'smile' ),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Display Icon', 'ultimate_vc' ),
								'param_name'  => 'icon_show',
								'value'       => array(
									__( 'Yes', 'ultimate_vc' ) => 'show',
									__( 'No', 'ultimate_vc' )  => 'not-show',
								),
								'description' => __( 'Select whether you want to show icon in information circle.', 'ultimate_vc' ),
								'group'       => $information_tab,
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Icon Size', 'ultimate_vc' ),
								'param_name' => 'content_icon_size',
								'value'      => 32,
								'suffix'     => 'px',
								'dependency' => array(
									'element' => 'icon_show',
									'value'   => array( 'show' ),
								),
								'group'      => $information_tab,
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Background Color', 'ultimate_vc' ),
								'param_name' => 'content_bg',
								'value'      => '',
								'group'      => $information_tab,
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Text Color', 'ultimate_vc' ),
								'param_name' => 'content_color',
								'value'      => '',
								'group'      => $information_tab,
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Line Style', 'ultimate_vc' ),
								'param_name' => 'eg_br_style',
								'value'      => array(
									__( 'None', 'ultimate_vc' ) => 'none',
									__( 'Solid', 'ultimate_vc' ) => 'solid',
									__( 'Dashed', 'ultimate_vc' ) => 'dashed',
									__( 'Dotted', 'ultimate_vc' ) => 'dotted',
								),
								'group'      => $connector_tab,
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Line Width', 'ultimate_vc' ),
								'param_name' => 'eg_br_width',
								'value'      => 1,
								'min'        => 0,
								'max'        => 10,
								'suffix'     => 'px',
								'dependency' => array(
									'element' => 'eg_br_style',
									'value'   => array( 'solid', 'dashed', 'dotted' ),
								),
								'group'      => $connector_tab,
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Line Color', 'ultimate_vc' ),
								'param_name' => 'eg_border_color',
								'value'      => '',
								'dependency' => array(
									'element' => 'eg_br_style',
									'value'   => array( 'solid', 'dashed', 'dotted' ),
								),
								'group'      => $connector_tab,
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Border Style', 'ultimate_vc' ),
								'param_name' => 'cn_br_style',
								'value'      => array(
									__( 'None', 'ultimate_vc' ) => 'none',
									__( 'Solid', 'ultimate_vc' ) => 'solid',
									__( 'Dashed', 'ultimate_vc' ) => 'dashed',
									__( 'Dotted', 'ultimate_vc' ) => 'dotted',
									__( 'Double', 'ultimate_vc' ) => 'double',
									__( 'Inset', 'ultimate_vc' ) => 'inset',
									__( 'Outset', 'ultimate_vc' ) => 'outset',
								),
								'group'      => $information_tab,
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Border Width', 'ultimate_vc' ),
								'param_name' => 'cn_br_width',
								'value'      => 1,
								'min'        => 0,
								'max'        => 10,
								'suffix'     => 'px',

								'dependency' => array(
									'element' => 'cn_br_style',
									'value'   => array( 'solid', 'dashed', 'dotted', 'double', 'inset', 'outset' ),
								),
								'group'      => $information_tab,
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Border color', 'ultimate_vc' ),
								'param_name' => 'cn_border_color',
								'value'      => '',
								'dependency' => array(
									'element' => 'cn_br_style',
									'value'   => array( 'solid', 'dashed', 'dotted', 'double', 'inset', 'outset' ),
								),
								'group'      => $information_tab,
							),

							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Appear Information Circle on', 'ultimate_vc' ),
								'param_name'  => 'focus_on',
								'value'       => array(
									__( 'Hover', 'ultimate_vc' ) => 'hover',
									__( 'Click', 'ultimate_vc' ) => 'click',
								),
								'description' => __( 'Select on which event information should appear in information circle.', 'ultimate_vc' ),
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Autoplay', 'ultimate_vc' ),
								'param_name' => 'auto_slide',
								'value'      => array(
									__( 'No', 'ultimate_vc' )  => 'off',
									__( 'Yes', 'ultimate_vc' ) => 'on',
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Autoplay Time', 'ultimate_vc' ),
								'param_name'  => 'auto_slide_duration',
								'value'       => 3,
								'suffix'      => 'seconds',
								'description' => __( 'Duration before info circle should display next information on thumbnails.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'auto_slide',
									'value'   => array( 'on' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Animation of Active Thumbnail', 'ultimate_vc' ),
								'param_name'  => 'highlight_style',
								'value'       => array(
									__( 'None', 'ultimate_vc' ) => 'info-circle-highlight-style',
									__( 'Zoom InOut', 'ultimate_vc' ) => 'info-circle-pulse',
									__( 'Zoom Out', 'ultimate_vc' ) => 'info-circle-push',
									__( 'Zoom In', 'ultimate_vc' ) => 'info-circle-pop',
								),
								'description' => __( 'Select animation style for active thumbnails.', 'ultimate_vc' ),
								'group'       => $thumbnail_tab,
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Animation of Thumbnails when Page Loads', 'ultimate_vc' ),
								'param_name'  => 'icon_launch',
								'value'       => array(
									__( 'None', 'ultimate_vc' ) => '',
									__( 'Linear', 'ultimate_vc' ) => 'linear',
									__( 'Elastic', 'ultimate_vc' ) => 'easeOutElastic',
									__( 'Bounce', 'ultimate_vc' ) => 'easeOutBounce',
								),
								'description' => __( 'Select Animation Style.', 'ultimate_vc' ),
								'group'       => $thumbnail_tab,
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Animation Duration', 'ultimate_vc' ),
								'param_name'  => 'icon_launch_duration',
								'value'       => 1,
								'suffix'      => 'seconds',
								'description' => __( 'Specify animation duration.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'icon_launch',
									'not_empty' => true,
								),
								'group'       => $thumbnail_tab,
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Animation Delay', 'ultimate_vc' ),
								'param_name'  => 'icon_launch_delay',
								'value'       => 0.2,
								'suffix'      => 'seconds',
								'description' => __( 'Delay of animatin start in-between thumbnails.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'icon_launch',
									'not_empty' => true,
								),
								'group'       => $thumbnail_tab,
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Responsive Nature', 'ultimate_vc' ),
								'param_name'  => 'responsive',
								'value'       => array(
									__( 'True', 'ultimate_vc' ) => 'on',
									__( 'False', 'ultimate_vc' ) => 'off',
								),
								'description' => __( 'Select true to change its display style on low resolution.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Breakpoint', 'ultimate_vc' ),
								'param_name'  => 'responsive_breakpoint',
								'value'       => 800,
								'suffix'      => 'px',
								'description' => __( 'Break point is the point of screen resolution from where you can set your info-circle style into list style to the minimum screen resolution.', 'smile' ),
								'dependency'  => array(
									'element' => 'responsive',
									'value'   => array( 'on' ),
								),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Extra Class', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'value'       => '',
								'description' => __( 'Custom class.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Title Settings', 'ultimate_vc' ),
								'param_name'       => 'title_typography',
								'group'            => 'Typography',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
							),
							array(
								'type'       => 'ultimate_google_fonts',
								'heading'    => __( 'Font Family', 'ultimate_vc' ),
								'param_name' => 'title_font',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'title_font_style',
								'group'      => 'Typography',
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
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'title_line_height',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Description Settings', 'ultimate_vc' ),
								'param_name'       => 'desc_typography',
								'group'            => 'Typography',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'       => 'ultimate_google_fonts',
								'heading'    => __( 'Font Family', 'ultimate_vc' ),
								'param_name' => 'desc_font',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'desc_font_style',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => 'font-size',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'desc_font_size',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'desc_line_height',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => "<span style='display: block;'><a href='http://bsf.io/z-dpz' target='_blank' rel='noopener'>" . __( 'Watch Video Tutorial', 'ultimate_vc' ) . " &nbsp; <span class='dashicons dashicons-video-alt3' style='font-size:30px;vertical-align: middle;color: #e52d27;'></span></a></span>",
								'param_name'       => 'notification',
								'edit_field_class' => 'ult-param-important-wrapper ult-dashicon ult-align-right ult-bold-font ult-blue-font vc_column vc_col-sm-12',
							),
						),
						'js_view'                 => 'VcColumnView',
					)
				);
				vc_map(
					array(
						'name'            => __( 'Info Circle Item', 'ultimate_vc' ),
						'base'            => 'info_circle_item',
						'class'           => 'vc_info_circle_item',
						'icon'            => 'vc_info_circle_item',
						'category'        => 'Ultimate VC Addons',
						'content_element' => true,
						'as_child'        => array( 'only' => 'info_circle' ),
						'is_container'    => false,
						'params'          => array(
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Title', 'ultimate_vc' ),
								'param_name'  => 'info_title',
								'value'       => '',
								'admin_label' => true,
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon to display', 'ultimate_vc' ),
								'param_name'  => 'icon_type',
								'value'       => array(
									__( 'Font Icon Manager', 'ultimate_vc' ) => 'selector',
									__( 'Custom Image Icon', 'ultimate_vc' ) => 'custom',
								),
								'description' => __( 'Use existing font icon or upload a custom image.', 'ultimate_vc' ),
								'group'       => __( 'Design' ),
							),
							array(
								'type'        => 'icon_manager',
								'class'       => '',
								'heading'     => __( 'Select Icon For Information Circle & Thumbnail ', 'ultimate_vc' ),
								'param_name'  => 'info_icon',
								'value'       => '',
								'description' => __( "Click and select icon of your choice. If you can't find the one that suits for your purpose", 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-font-icon-manager' target='_blank' rel='noopener'>" . __( 'add new here', 'ultimate_vc' ) . '</a>.',
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
								'group'       => __( 'Design' ),
							),
							array(
								'type'        => 'ult_img_single',
								'class'       => '',
								'heading'     => __( 'Upload Image Icon', 'ultimate_vc' ),
								'param_name'  => 'info_img',
								'admin_label' => true,
								'value'       => '',
								'description' => __( 'Upload the custom image icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'custom' ),
								),
								'group'       => __( 'Design' ),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon Background Color', 'ultimate_vc' ),
								'param_name'  => 'icon_bg_color',
								'value'       => '',
								'description' => __( 'Select the color for icon background.', 'ultimate_vc' ),
								'group'       => __( 'Design' ),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color',
								'value'       => '',
								'description' => __( 'Select the color for icon.', 'ultimate_vc' ),
								'group'       => __( 'Design' ),
							),
							array(
								'type'       => 'textarea_html',
								'class'      => '',
								'heading'    => __( 'Description', 'ultimate_vc' ),
								'param_name' => 'content',
								'value'      => '',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Border Style', 'ultimate_vc' ),
								'param_name' => 'icon_br_style',
								'value'      => array(
									__( 'None', 'ultimate_vc' )   => 'none',
									__( 'Solid', 'ultimate_vc' )   => 'solid',
									__( 'Dashed', 'ultimate_vc' ) => 'dashed',
									__( 'Dotted', 'ultimate_vc' ) => 'dotted',
									__( 'Double', 'ultimate_vc' ) => 'double',
									__( 'Inset', 'ultimate_vc' )  => 'inset',
									__( 'Outset', 'ultimate_vc' ) => 'outset',
								),
								'group'      => __( 'Design' ),
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Border Thickness', 'ultimate_vc' ),
								'param_name' => 'icon_br_width',
								'value'      => 1,
								'min'        => 0,
								'max'        => 10,
								'suffix'     => 'px',
								'dependency' => array(
									'element' => 'icon_br_style',
									'value'   => array( 'solid', 'dashed', 'dotted', 'double', 'inset', 'outset' ),
								),
								'group'      => __( 'Design' ),
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Padding', 'ultimate_vc' ),
								'param_name' => 'icon_br_padding',
								'value'      => '',
								'min'        => 0,
								'max'        => '',
								'suffix'     => 'px',
								'dependency' => array(
									'element' => 'icon_br_style',
									'value'   => array( 'solid', 'dashed', 'dotted', 'double', 'inset', 'outset' ),
								),
								'group'      => __( 'Design' ),
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Border Color', 'ultimate_vc' ),
								'param_name' => 'icon_border_color',
								'value'      => '',
								'dependency' => array(
									'element' => 'icon_br_style',
									'value'   => array( 'solid', 'dashed', 'dotted', 'double', 'inset', 'outset' ),
								),
								'group'      => __( 'Design' ),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Extra Class', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'value'       => '',
								'description' => __( 'Custom class.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'vc_link',
								'class'       => '',
								'heading'     => __( 'Link', 'ultimate_vc' ),
								'param_name'  => 'ilink',
								'value'       => '',
								'description' => __( 'Add link to Icon/image on Info Circle', 'ultimate_vc' ),
							),
						),
					)
				);
			}
		}
	}
}
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	if ( ! class_exists( 'WPBakeryShortCode_info_circle' ) ) {
		/**
		 * WPBakeryShortCode_info_circle class
		 */
		class WPBakeryShortCode_Info_Circle extends WPBakeryShortCodesContainer {
		}
	}

	if ( ! class_exists( 'WPBakeryShortCode_Info_Circle_Item' ) ) {
		/**
		 * WPBakeryShortCode_Info_Circle_Item class
		 */
		class WPBakeryShortCode_Info_Circle_Item extends WPBakeryShortCode {
		}
	}
}
if ( class_exists( 'Ultimate_Info_Circle' ) ) {
	$ultimate_info_circle = new Ultimate_Info_Circle();
}

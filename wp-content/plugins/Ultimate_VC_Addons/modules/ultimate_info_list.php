<?php
/**
 * Add-on Name: Info List for WPBakery Page Builder
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package Ultimate Headings
 */

if ( ! class_exists( 'AIO_Info_List' ) ) {
	/**
	 * Function that initializes Ultimate Info List Module
	 *
	 * @class AIO_Info_list
	 */
	class AIO_Info_List {
		/**
		 * Info List data
		 *
		 * @var $connector_animate
		 */

		public $connector_animate;
		/**
		 * Info List data
		 *
		 * @var $connect_color_style
		 */
		public $connect_color_style;
		/**
		 * Info List data
		 *
		 * @var $icon_font
		 */
		public $icon_font;
		/**
		 * Info List data
		 *
		 * @var $border_col
		 */
		public $border_col;
		/**
		 * Info List data
		 *
		 * @var $icon_style
		 */
		public $icon_style;

		/**
		 * Info List data
		 *
		 * @var $icon_size
		 */
		public $icon_size;
		/**
		 * Constructor function that constructs default values for the Ultimate Info List module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			$this->connector_animate   = '';
			$this->connect_color_style = '';
			$this->icon_style          = '';
			$this->icon_style          = '';
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'add_info_list' ) );
			}
			if ( function_exists( 'vc_is_inline' ) ) {
				if ( ! vc_is_inline() ) {
					add_shortcode( 'info_list', array( $this, 'info_list' ) );
					add_shortcode( 'info_list_item', array( $this, 'info_list_item' ) );
				}
			} else {
				add_shortcode( 'info_list', array( $this, 'info_list' ) );
				add_shortcode( 'info_list_item', array( $this, 'info_list_item' ) );
			}
		}
		/**
		 * Render function for Ultimate Info List Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function info_list( $atts, $content = null ) {
			$this->icon_style                        = '';
			$this->connector_animate                 = '';
			$this->icon_font                         = '';
			$this->border_col                        = '';
			$info_list_link_html                     = '';
			$ult_info_list_settings                  = shortcode_atts(
				array(
					'position'            => 'left',
					'style'               => 'square with_bg',
					'connector_animation' => '',
					'icon_color'          => '#333333',
					'icon_bg_color'       => '#ffffff',
					'eg_br_style'         => 'dashed',
					'eg_br_width'         => '1',
					'connector_color'     => '#333333',
					'border_color'        => '#333333',
					'font_size_icon'      => '24',
					'icon_border_style'   => 'none',
					'icon_border_size'    => '1',
					'el_class'            => '',
					'css_info_list'       => '',
				),
				$atts
			);
			$this->connect_color_style               = '';
			$ult_info_list_settings['css_info_list'] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_info_list_settings['css_info_list'], ' ' ), 'info_list', $atts );
			$ult_info_list_settings['css_info_list'] = esc_attr( $ult_info_list_settings['css_info_list'] );

			$vc_version       = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
			$is_vc_49_plus    = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'ult-adjust-bottom-margin' : '';
			$eg_br_width_plus = '';
			if ( 'left' == $ult_info_list_settings['position'] && 'none' != $ult_info_list_settings['eg_br_style'] && '' != $ult_info_list_settings['eg_br_width'] && '' != $ult_info_list_settings['connector_color'] ) {
				if ( is_rtl() ) {
					$this->connect_color_style  = 'border-left-width: ' . $ult_info_list_settings['eg_br_width'] . 'px;';
					$this->connect_color_style .= 'border-left-style: ' . $ult_info_list_settings['eg_br_style'] . ';';
				} else {
					$this->connect_color_style  = 'border-right-width: ' . $ult_info_list_settings['eg_br_width'] . 'px;';
					$this->connect_color_style .= 'border-right-style: ' . $ult_info_list_settings['eg_br_style'] . ';';
				}
				$this->connect_color_style .= 'border-color: ' . $ult_info_list_settings['connector_color'] . ';';
				if ( ( 'square with_bg' == $ult_info_list_settings['style'] || 'circle with_bg' == $ult_info_list_settings['style'] || 'hexagon' == $ult_info_list_settings['style'] ) && (int) $ult_info_list_settings['eg_br_width'] > 1 ) {
					$eg_br_width_plus           = ( ( $ult_info_list_settings['font_size_icon'] * 3 ) + ( (int) $ult_info_list_settings['icon_border_size'] * 2 ) - ( (int) $ult_info_list_settings['eg_br_width'] ) ) / 2;
					$this->connect_color_style .= 'left: ' . $eg_br_width_plus . 'px;';
				}
			}
			if ( 'right' == $ult_info_list_settings['position'] && 'none' != $ult_info_list_settings['eg_br_style'] && '' != $ult_info_list_settings['eg_br_width'] && '' != $ult_info_list_settings['connector_color'] ) {
				if ( is_rtl() ) {
					$this->connect_color_style  = 'border-right-width: ' . $ult_info_list_settings['eg_br_width'] . 'px;';
					$this->connect_color_style .= 'border-right-style: ' . $ult_info_list_settings['eg_br_style'] . ';';
				} else {
					$this->connect_color_style  = 'border-left-width: ' . $ult_info_list_settings['eg_br_width'] . 'px;';
					$this->connect_color_style .= 'border-left-style: ' . $ult_info_list_settings['eg_br_style'] . ';';
				}
				$this->connect_color_style .= 'border-color: ' . $ult_info_list_settings['connector_color'] . ';';
				if ( ( 'square with_bg' == $ult_info_list_settings['style'] || 'circle with_bg' == $ult_info_list_settings['style'] || 'hexagon' == $ult_info_list_settings['style'] ) && (int) $ult_info_list_settings['eg_br_width'] > 1 ) {
					$eg_br_width_plus           = ( ( $ult_info_list_settings['font_size_icon'] * 3 ) + ( (int) $ult_info_list_settings['icon_border_size'] * 2 ) - ( (int) $ult_info_list_settings['eg_br_width'] ) ) / 2;
					$this->connect_color_style .= 'right: ' . $eg_br_width_plus . 'px;';
				}
			}
			if ( 'top' == $ult_info_list_settings['position'] && 'none' != $ult_info_list_settings['eg_br_style'] && '' != $ult_info_list_settings['eg_br_width'] && '' != $ult_info_list_settings['connector_color'] ) {
				$this->connect_color_style  = 'border-top-width: ' . $ult_info_list_settings['eg_br_width'] . 'px;';
				$this->connect_color_style .= 'border-top-style: ' . $ult_info_list_settings['eg_br_style'] . ';';
				$this->connect_color_style .= 'border-color: ' . $ult_info_list_settings['connector_color'] . ';';
				if ( ( 'square with_bg' == $ult_info_list_settings['style'] || 'circle with_bg' == $ult_info_list_settings['style'] ) && (int) $ult_info_list_settings['eg_br_width'] > 1 ) {
					$eg_br_width_plus           = ( ( $ult_info_list_settings['font_size_icon'] * 3 ) + ( (int) $ult_info_list_settings['icon_border_size'] * 2 ) - ( (int) $ult_info_list_settings['eg_br_width'] ) ) / 2;
					$this->connect_color_style .= 'top: ' . $eg_br_width_plus . 'px;';
				}
			}
			if ( 'none' == $ult_info_list_settings['eg_br_style'] && ( 'left' == $ult_info_list_settings['position'] || 'right' == $ult_info_list_settings['position'] || 'top' == $ult_info_list_settings['position'] ) ) {
				$this->connect_color_style = 'border: none !important;';
			}

			$this->border_col = $ult_info_list_settings['border_color'];
			if ( 'square with_bg' == $ult_info_list_settings['style'] || 'circle with_bg' == $ult_info_list_settings['style'] || 'hexagon' == $ult_info_list_settings['style'] ) {
				$this->icon_font = 'font-size:' . ( $ult_info_list_settings['font_size_icon'] * 3 ) . 'px;';
				if ( '' !== $ult_info_list_settings['icon_border_size'] ) {
					$this->icon_style .= 'font-size:' . $ult_info_list_settings['font_size_icon'] . 'px;';
					if ( 'hexagon' !== $ult_info_list_settings['style'] ) {
						$this->icon_style .= 'border-width:' . $ult_info_list_settings['icon_border_size'] . 'px;';
						$this->icon_style .= 'border-style:' . $ult_info_list_settings['icon_border_style'] . ';';
					}
					$this->icon_style .= 'background:' . $ult_info_list_settings['icon_bg_color'] . ';';
					$this->icon_style .= 'color:' . $ult_info_list_settings['icon_color'] . ';';
					if ( 'hexagon' == $ult_info_list_settings['style'] ) {
						$this->icon_style .= 'border-color:' . $ult_info_list_settings['icon_bg_color'] . ';';
					} else {
						$this->icon_style .= 'border-color:' . $ult_info_list_settings['border_color'] . ';';
					}
				}
			} else {
				$big_size = ( $ult_info_list_settings['font_size_icon'] * 3 ) + ( $ult_info_list_settings['icon_border_size'] * 2 );
				if ( '' !== $ult_info_list_settings['icon_border_size'] ) {
					$this->icon_font   = 'font-size:' . $big_size . 'px;';
					$this->icon_style .= 'font-size:' . $ult_info_list_settings['font_size_icon'] . 'px;';
					$this->icon_style .= 'border-width:' . $ult_info_list_settings['icon_border_size'] . 'px;';
					$this->icon_style .= 'border-style:' . $ult_info_list_settings['icon_border_style'] . ';';
					$this->icon_style .= 'color:' . $ult_info_list_settings['icon_color'] . ';';
					$this->icon_style .= 'border-color:' . $ult_info_list_settings['border_color'] . ';';
				}
			}

			$this->icon_size = $ult_info_list_settings['font_size_icon'];

			if ( 'top' == $ult_info_list_settings['position'] ) {
				$this->connector_animate = 'fadeInLeft';
			} else {
				$this->connector_animate = $ult_info_list_settings['connector_animation'];
			}
			$output  = '<div class="smile_icon_list_wrap ult_info_list_container ' . esc_attr( $is_vc_49_plus ) . ' ' . esc_attr( $ult_info_list_settings['el_class'] ) . ' ' . esc_attr( $ult_info_list_settings['css_info_list'] ) . '">';
			$output .= '<ul class="smile_icon_list ' . esc_attr( $ult_info_list_settings['position'] ) . ' ' . esc_attr( $ult_info_list_settings['style'] ) . '">';
			$output .= do_shortcode( $content );
			$output .= '</ul>';
			$output .= '</div>';
			return $output;
		}
		/**
		 * Render function for Ultimate Info List Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function info_list_item( $atts, $content = null ) {
			// Do nothing.
			$icon_color    = '';
			$icon_bg_color = '';

			$ult_info_list_item = shortcode_atts(
				array(
					'list_title'             => '',
					'heading_tag'            => 'h3',
					'animation'              => '',
					'list_icon'              => '',
					'icon_img'               => '',
					'icon_type'              => '',
					'title_font'             => '',
					'title_font_style'       => '',
					'title_font_size'        => '16',
					'title_font_line_height' => '24',
					'title_font_color'       => '',
					'desc_font'              => '',
					'desc_font_style'        => '',
					'desc_font_size'         => '13',
					'desc_font_color'        => '',
					'desc_font_line_height'  => '18',
					'info_list_link'         => '',
					'info_list_link_apply'   => '',
				),
				$atts
			);

			$css_trans           = '';
			$style               = '';
			$ico_col             = '';
			$connector_trans     = '';
			$icon_html           = '';
			$title_style         = '';
			$desc_style          = '';
			$info_list_link_html = '';
			$target              = '';
			$link_title          = '';
			$rel                 = '';

			$is_link = false;

			if ( '' != $ult_info_list_item['info_list_link'] ) {
				$href = vc_build_link( $ult_info_list_item['info_list_link'] );

				$url        = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
				$target     = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
				$link_title = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
				$rel        = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';

				if ( '' != $url ) {
					$info_list_link_html = '<a class="ulimate-info-list-link" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . '></a>';
				}
				$is_link = true;
			}

						/* title */
			if ( '' != $ult_info_list_item['title_font'] ) {
				$font_family  = get_ultimate_font_family( $ult_info_list_item['title_font'] );
				$title_style .= 'font-family:\'' . $font_family . '\';';
			}
			if ( '' != $ult_info_list_item['title_font_style'] ) {
				$title_style .= get_ultimate_font_style( $ult_info_list_item['title_font_style'] );
			}
			if ( is_numeric( $ult_info_list_item['title_font_size'] ) ) {
				$ult_info_list_item['title_font_size'] = 'desktop:' . $ult_info_list_item['title_font_size'] . 'px;';
			}
			if ( is_numeric( $ult_info_list_item['title_font_line_height'] ) ) {
				$ult_info_list_item['title_font_line_height'] = 'desktop:' . $ult_info_list_item['title_font_line_height'] . 'px;';
			}
			$info_list_id        = 'Info-list-wrap-' . wp_rand( 1000, 9999 );
			$info_list_args      = array(
				'target'      => '#' . $info_list_id . ' ' . $ult_info_list_item['heading_tag'], // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_info_list_item['title_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_info_list_item['title_font_line_height'],
				),
			);
			$info_list_data_list = get_ultimate_vc_responsive_media_css( $info_list_args );

			if ( '' != $ult_info_list_item['title_font_color'] ) {
				$title_style .= 'color:' . $ult_info_list_item['title_font_color'] . ';';
			}

			/* description */
			if ( '' != $ult_info_list_item['desc_font'] ) {
				$font_family = get_ultimate_font_family( $ult_info_list_item['desc_font'] );
				$desc_style .= 'font-family:\'' . $font_family . '\';';
			}
			if ( '' != $ult_info_list_item['desc_font_style'] ) {
				$desc_style .= get_ultimate_font_style( $ult_info_list_item['desc_font_style'] );
			}
			if ( is_numeric( $ult_info_list_item['desc_font_size'] ) ) {
				$ult_info_list_item['desc_font_size'] = 'desktop:' . $ult_info_list_item['desc_font_size'] . 'px;';
			}
			if ( is_numeric( $ult_info_list_item['desc_font_line_height'] ) ) {
				$ult_info_list_item['desc_font_line_height'] = 'desktop:' . $ult_info_list_item['desc_font_line_height'] . 'px;';
			}
			$info_list_desc_args      = array(
				'target'      => '#' . $info_list_id . ' .icon_description_text', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_info_list_item['desc_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_info_list_item['desc_font_line_height'],
				),
			);
			$info_list_desc_data_list = get_ultimate_vc_responsive_media_css( $info_list_desc_args );

			if ( '' != $ult_info_list_item['desc_font_color'] ) {
				$desc_style .= 'color:' . $ult_info_list_item['desc_font_color'] . ';';
			}
			if ( 'none' !== $ult_info_list_item['animation'] ) {
				$css_trans = 'data-animation="' . $ult_info_list_item['animation'] . '" data-animation-delay="03"';
			}
			if ( $this->connector_animate ) {
				$connector_trans = 'data-animation="' . $this->connector_animate . '" data-animation-delay="03"';
			}
			if ( '' != $icon_color ) {
				$ico_col = 'style="color:' . $icon_color . '";';
			}
			if ( '' != $icon_bg_color ) {
				$style .= 'background:' . $icon_bg_color . ';  color:' . $icon_bg_color . ';';
			}
			if ( '' != $icon_bg_color ) {
				$style .= 'border-color:' . $this->border_col . ';';
			}

			if ( 'custom' == $ult_info_list_item['icon_type'] ) {
				$img = apply_filters( 'ult_get_img_single', $ult_info_list_item['icon_img'], 'url', 'large' );
				$alt = apply_filters( 'ult_get_img_single', $ult_info_list_item['icon_img'], 'alt' );
				if ( '' == $alt ) {
					$alt = 'icon';
				}

				$icon_html .= '<div class="icon_list_icon" ' . $css_trans . ' style="' . esc_attr( $this->icon_style ) . '">';
				$icon_html .= '<img class="list-img-icon" alt="' . esc_attr( $alt ) . '" src="' . esc_url( apply_filters( 'ultimate_images', $img ) ) . '"/>';
				if ( $is_link && 'icon' == $ult_info_list_item['info_list_link_apply'] ) {
					$icon_html .= $info_list_link_html;
				}
				$icon_html .= '</div>';
				// }
			} else {
				$icon_html .= '<div class="icon_list_icon" ' . $css_trans . ' style="' . esc_attr( $this->icon_style ) . '">';
				$icon_html .= '<i class="' . esc_attr( $ult_info_list_item['list_icon'] ) . '" ' . $ico_col . '></i>';
				if ( $is_link && 'icon' == $ult_info_list_item['info_list_link_apply'] ) {
					$icon_html .= $info_list_link_html;
				}
				$icon_html .= '</div>';
			}
			$output  = '<li class="icon_list_item" style=" ' . esc_attr( $this->icon_font ) . '">';
			$output .= $icon_html;
			$output .= '<div class="icon_description" id="' . esc_attr( $info_list_id ) . '" style="font-size:' . esc_attr( $this->icon_size ) . 'px;">';
			if ( '' != $ult_info_list_item['list_title'] ) {
				$output .= '<' . $ult_info_list_item['heading_tag'] . ' class="ult-responsive info-list-heading" ' . $info_list_data_list . ' style="' . esc_attr( $title_style ) . '">';
				if ( $is_link && 'title' == $ult_info_list_item['info_list_link_apply'] ) {
					$output .= '<a ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . '>' . $ult_info_list_item['list_title'] . '</a>';
				} else {
					$output .= $ult_info_list_item['list_title'];
				}
				$output .= '</' . $ult_info_list_item['heading_tag'] . '>';
			}
			$output .= '<div class="icon_description_text ult-responsive" ' . $info_list_desc_data_list . ' style="' . esc_attr( $desc_style ) . '">' . wpb_js_remove_wpautop( $content, true ) . '</div>';
			$output .= '</div>';
			$output .= '<div class="icon_list_connector" ' . $connector_trans . ' style="' . esc_attr( $this->connect_color_style ) . '"></div>';
			if ( $is_link && 'container' == $ult_info_list_item['info_list_link_apply'] ) {
				$output .= $info_list_link_html;
			}
			$output .= '</li>';
			return $output;
		}
		/**
		 * Shortcode Functions for frontend editor..
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function front_info_list( $atts, $content = null ) {
			// Do nothing.
			$ult_info_list_front = shortcode_atts(
				array(
					'position'            => 'left',
					'style'               => 'square with_bg',
					'connector_animation' => '',
					'icon_color'          => '#333333',
					'icon_bg_color'       => '#ffffff',
					'eg_br_style'         => 'dashed',
					'eg_br_width'         => '1',
					'connector_color'     => '#333333',
					'border_color'        => '#333333',
					'font_size_icon'      => '24',
					'icon_border_style'   => 'none',
					'icon_border_size'    => '1',
					'el_class'            => '',
				),
				$atts
			);
			$eg_br_width_plus    = '';
			if ( 'left' == $ult_info_list_front['position'] && 'none' != $ult_info_list_front['eg_br_style'] && '' != $ult_info_list_front['eg_br_width'] && '' != $ult_info_list_front['connector_color'] ) {
				$this->connect_color_style  = 'border-right-width: ' . $ult_info_list_front['eg_br_width'] . 'px;';
				$this->connect_color_style .= 'border-right-style: ' . $ult_info_list_front['eg_br_style'] . ';';
				$this->connect_color_style .= 'border-color: ' . $ult_info_list_front['connector_color'] . ';';
				if ( ( 'square with_bg' == $ult_info_list_front['style'] || 'circle with_bg' == $ult_info_list_front['style'] || 'hexagon' == $ult_info_list_front['style'] ) && (int) $ult_info_list_front['eg_br_width'] > 1 ) {
					$eg_br_width_plus           = ( ( $ult_info_list_front['font_size_icon'] * 3 ) + ( (int) $ult_info_list_front['icon_border_size'] * 2 ) - ( (int) $ult_info_list_front['eg_br_width'] ) ) / 2;
					$this->connect_color_style .= 'left: ' . $eg_br_width_plus . 'px;';
				}
			}
			if ( 'right' == $ult_info_list_front['position'] && 'none' != $ult_info_list_front['eg_br_style'] && '' != $ult_info_list_front['eg_br_width'] && '' != $ult_info_list_front['connector_color'] ) {
				$this->connect_color_style  = 'border-left-width: ' . $ult_info_list_front['eg_br_width'] . 'px;';
				$this->connect_color_style .= 'border-left-style: ' . $ult_info_list_front['eg_br_style'] . ';';
				$this->connect_color_style .= 'border-color: ' . $ult_info_list_front['connector_color'] . ';';
				if ( ( 'square with_bg' == $ult_info_list_front['style'] || 'circle with_bg' == $ult_info_list_front['style'] || 'hexagon' == $ult_info_list_front['style'] ) && (int) $ult_info_list_front['eg_br_width'] > 1 ) {
					$eg_br_width_plus           = ( ( $ult_info_list_front['font_size_icon'] * 3 ) + ( (int) $ult_info_list_front['icon_border_size'] * 2 ) - ( (int) $ult_info_list_front['eg_br_width'] ) ) / 2;
					$this->connect_color_style .= 'right: ' . $eg_br_width_plus . 'px;';
				}
			}
			if ( 'top' == $ult_info_list_front['position'] && 'none' != $ult_info_list_front['eg_br_style'] && '' != $ult_info_list_front['eg_br_width'] && '' != $ult_info_list_front['connector_color'] ) {
				$this->connect_color_style  = 'border-top-width: ' . $ult_info_list_front['eg_br_width'] . 'px;';
				$this->connect_color_style .= 'border-top-style: ' . $ult_info_list_front['eg_br_style'] . ';';
				$this->connect_color_style .= 'border-color: ' . $ult_info_list_front['connector_color'] . ';';
				if ( ( 'square with_bg' == $ult_info_list_front['style'] || 'circle with_bg' == $ult_info_list_front['style'] ) && (int) $ult_info_list_front['eg_br_width'] > 1 ) {
					$eg_br_width_plus           = ( ( $ult_info_list_front['font_size_icon'] * 3 ) + ( (int) $ult_info_list_front['icon_border_size'] * 2 ) - ( (int) $ult_info_list_front['eg_br_width'] ) ) / 2;
					$this->connect_color_style .= 'top: ' . $eg_br_width_plus . 'px;';
				}
			}

			if ( 'none' == $ult_info_list_front['eg_br_style'] && ( 'left' == $ult_info_list_front['position'] || 'right' == $ult_info_list_front['position'] || 'top' == $ult_info_list_front['position'] ) ) {
				$this->connect_color_style = 'border: none !important;';
			}

			$this->border_col = $ult_info_list_front['border_color'];
			if ( 'square with_bg' == $ult_info_list_front['style'] || 'circle with_bg' == $ult_info_list_front['style'] || 'hexagon' == $ult_info_list_front['style'] ) {
				$this->icon_font = 'font-size:' . ( $ult_info_list_front['font_size_icon'] * 3 ) . 'px;';
				if ( '' !== $ult_info_list_front['icon_border_size'] ) {
					$this->icon_style  = 'font-size:' . $ult_info_list_front['font_size_icon'] . 'px;';
					$this->icon_style .= 'border-width:0px;';
					$this->icon_style .= 'border-style:none;';
					$this->icon_style .= 'background:' . $ult_info_list_front['icon_bg_color'] . ';';
					$this->icon_style .= 'color:' . $ult_info_list_front['icon_color'] . ';';
					if ( 'hexagon' == $ult_info_list_front['style'] ) {
						$this->icon_style .= 'border-color:' . $ult_info_list_front['icon_bg_color'] . ';';
					} else {
						$this->icon_style .= 'border-color:' . $ult_info_list_front['border_color'] . ';';
					}
				}
			} else {
				$big_size = ( $ult_info_list_front['font_size_icon'] * 3 ) + ( $ult_info_list_front['icon_border_size'] * 2 );
				if ( '' !== $ult_info_list_front['icon_border_size'] ) {
					$this->icon_font   = 'font-size:' . $big_size . 'px;';
					$this->icon_style  = 'font-size:' . $ult_info_list_front['font_size_icon'] . 'px;';
					$this->icon_style .= 'border-width:' . $ult_info_list_front['icon_border_size'] . 'px;';
					$this->icon_style .= 'border-style:' . $ult_info_list_front['icon_border_style'] . ';';
					$this->icon_style .= 'color:' . $ult_info_list_front['icon_color'] . ';';
					$this->icon_style .= 'border-color:' . $ult_info_list_front['border_color'] . ';';
				}
			}
			if ( 'top' == $ult_info_list_front['position'] ) {
				$this->connector_animate = 'fadeInLeft';
			} else {
				$this->connector_animate = $ult_info_list_front['connector_animation'];
			}
			$output  = '<div class="smile_icon_list_wrap ' . esc_attr( $ult_info_list_front['el_class'] ) . '' . esc_attr( $css_info_list ) . '">';
			$output .= '<ul class="smile_icon_list ' . esc_attr( $ult_info_list_front['position'] ) . ' ' . esc_attr( $ult_info_list_front['style'] ) . '" data-style="' . esc_attr( $this->icon_style ) . '" data-fonts="' . esc_attr( $this->icon_font ) . '" data-connector="' . esc_attr( $ult_info_list_front['connector_color'] ) . '">';
			$output .= do_shortcode( $content );
			$output .= '</ul>';
			$output .= '</div>';
			return $output;
		}
		/**
		 * Frontend List for Ultimate Info List Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function front_info_list_item( $atts, $content = null ) {
			// Do nothing.

			$icon_color               = '';
			$icon_bg_color            = '';
			$icon_type                = '';
			$ult_info_list_front_item = shortcode_atts(
				array(
					'list_title' => '',
					'animation'  => '',
					'list_icon'  => '',
					'icon_img'   => '',
					'icon_type'  => '',
				),
				$atts
			);
			$css_trans                = '';
			$style                    = '';
			$ico_col                  = '';
			$connector_trans          = '';
			$icon_html                = '';
			if ( 'none' !== $ult_info_list_front_item['animation'] ) {
				$css_trans = 'data-animation="' . esc_attr( $ult_info_list_front_item['animation'] ) . '" data-animation-delay="03"';
			}
			if ( $this->connector_animate ) {
				$connector_trans = 'data-animation="' . esc_attr( $this->connector_animate ) . '" data-animation-delay="02"';
			}
			if ( '' != $icon_color ) {
				$ico_col = 'style="color:' . $icon_color . '";';
			}
			if ( '' != $icon_bg_color ) {
				$style .= 'background:' . $icon_bg_color . ';  color:' . $icon_bg_color . ';';
			}
			if ( '' != $icon_bg_color ) {
				$style .= 'border-color:' . $this->border_col . ';';
			}
			if ( 'selector' == $ult_info_list_front_item['icon_type'] ) {
				$icon_html .= '<div class="icon_list_icon" ' . $css_trans . '>';
				$icon_html .= '<i class="' . esc_attr( $ult_info_list_front_item['list_icon'] ) . '" ' . $ico_col . '></i>';
				$icon_html .= '</div>';
			} else {
				$img = apply_filters( 'ult_get_img_single', $ult_info_list_front_item['icon_img'], 'url', 'large' );

				$icon_html .= '<div class="icon_list_icon" ' . $css_trans . '>';
				$icon_html .= '<img class="list-img-icon " alt="icon" src="' . esc_url( apply_filters( 'ultimate_images', $img ) ) . '"/>';
				$img        = apply_filters( 'ult_get_img_single', $ult_info_list_front_item['icon_img'], 'url' );
				$icon_html .= '<div class="icon_list_icon" ' . $css_trans . '>';
				$icon_html .= '<img class="list-img-icon" alt="icon" src="' . esc_url( apply_filters( 'ultimate_images', $img ) ) . '"/>';
				$icon_html .= '</div>';
			}

			$output  = '<li class="icon_list_item">';
			$output .= $icon_html;
			$output .= '<div class="icon_description">';
			$output .= '<' . $heading_tag . '>' . $ult_info_list_front_item['list_title'] . '</' . $heading_tag . '>';
			$output .= wpb_js_remove_wpautop( $content, true );
			$output .= '</div>';
			$output .= '<div class="icon_list_connector" ' . $connector_trans . ' style="' . esc_attr( $this->connect_color_style ) . ';"></div>';
			$output .= '</li>';
			return $output;
		}
		/**
		 * Render function for Ultimate Heading Module.
		 *
		 * @access public
		 */
		public function add_info_list() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'                    => __( 'Info List', 'ultimate_vc' ),
						'base'                    => 'info_list',
						'class'                   => 'vc_info_list',
						'icon'                    => 'vc_icon_list',
						'category'                => 'Ultimate VC Addons',
						'as_parent'               => array( 'only' => 'info_list_item' ),
						'description'             => __( 'Text blocks connected together in one list.', 'ultimate_vc' ),
						'content_element'         => true,
						'show_settings_on_create' => true,
						'params'                  => array(
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon or Image Position', 'ultimate_vc' ),
								'param_name'  => 'position',
								'value'       => array(
									__( 'Icon to the Left', 'ultimate_vc' ) => 'left',
									__( 'Icon to the Right', 'ultimate_vc' ) => 'right',
									__( 'Icon at Top', 'ultimate_vc' ) => 'top',
								),
								'description' => __( 'Select the icon position for icon list.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Style of Image or Icon + Color', 'ultimate_vc' ),
								'param_name'  => 'style',
								'value'       => array(
									__( 'Square With Background', 'ultimate_vc' ) => 'square with_bg',
									__( 'Circle With Background', 'ultimate_vc' ) => 'circle with_bg',
									__( 'Hexagon With Background', 'ultimate_vc' ) => 'hexagon',
								),
								'description' => __( 'Select the icon style for icon list.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon Background Color:', 'ultimate_vc' ),
								'param_name'  => 'icon_bg_color',
								'value'       => '#ffffff',
								'description' => __( 'Select the color for icon background.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'style',
									'value'   => array( 'square with_bg', 'circle with_bg', 'hexagon' ),
								),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Icon Color:', 'ultimate_vc' ),
								'param_name'  => 'icon_color',
								'value'       => '#333333',
								'description' => __( 'Select the color for icon.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Icon Font Size', 'ultimate_vc' ),
								'param_name'  => 'font_size_icon',
								'value'       => 24,
								'min'         => 12,
								'max'         => 72,
								'suffix'      => 'px',
								'description' => __( 'Enter value in pixels.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Border Style', 'ultimate_vc' ),
								'param_name'  => 'icon_border_style',
								'value'       => array(
									__( 'None', 'ultimate_vc' ) => 'none',
									__( 'Solid', 'ultimate_vc' ) => 'solid',
									__( 'Dashed', 'ultimate_vc' ) => 'dashed',
									__( 'Dotted', 'ultimate_vc' ) => 'dotted',
									__( 'Double', 'ultimate_vc' ) => 'double',
									__( 'Inset', 'ultimate_vc' ) => 'inset',
									__( 'Outset', 'ultimate_vc' ) => 'outset',
								),
								'description' => __( 'Select the border style for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'style',
									'value'   => array( 'square with_bg', 'circle with_bg' ),
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Border Width', 'ultimate_vc' ),
								'param_name'  => 'icon_border_size',
								'value'       => 1,
								'min'         => 0,
								'max'         => 10,
								'suffix'      => 'px',
								'description' => __( 'Thickness of the border.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_border_style',
									'value'   => array( 'solid', 'dashed', 'dotted', 'double', 'inset', 'outset' ),
								),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Border Color:', 'ultimate_vc' ),
								'param_name'  => 'border_color',
								'value'       => '#333333',
								'description' => __( 'Select the color border.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_border_style',
									'value'   => array( 'solid', 'dashed', 'dotted', 'double', 'inset', 'outset' ),
								),
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Line Style', 'ultimate_vc' ),
								'param_name' => 'eg_br_style',
								'value'      => array(
									__( 'Dashed', 'ultimate_vc' ) => 'dashed',
									__( 'Solid', 'ultimate_vc' ) => 'solid',
									__( 'Dotted', 'ultimate_vc' ) => 'dotted',
									__( 'None', 'ultimate_vc' ) => 'none',
								),
								'group'      => 'Connector',
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
								'group'      => 'Connector',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Line Color:', 'ultimate_vc' ),
								'param_name'  => 'connector_color',
								'value'       => '#333333',
								'dependency'  => array(
									'element' => 'eg_br_style',
									'value'   => array( 'solid', 'dashed', 'dotted' ),
								),
								'description' => __( 'Select the color for connector line.', 'ultimate_vc' ),
								'group'       => 'Connector',
							),
							array(
								'type'        => 'checkbox',
								'class'       => '',
								'heading'     => __( 'Line Animation: ', 'ultimate_vc' ),
								'param_name'  => 'connector_animation',
								'value'       => array(
									__( 'Enabled', 'ultimate_vc' ) => 'fadeInUp',
								),
								'dependency'  => array(
									'element' => 'eg_br_style',
									'value'   => array( 'solid', 'dashed', 'dotted' ),
								),
								'description' => __( 'Select wheather to animate connector or not', 'ultimate_vc' ),
								'group'       => 'Connector',
							),

							// Customize everything.
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Extra Class', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'value'       => '',
								'description' => __( 'Add extra class name that will be applied to the info list, and you can use this class for your customizations.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => "<span style='display: block;'><a href='http://bsf.io/v9k0x' target='_blank' rel='noopener'>" . __( 'Watch Video Tutorial', 'ultimate_vc' ) . " &nbsp; <span class='dashicons dashicons-video-alt3' style='font-size:30px;vertical-align: middle;color: #e52d27;'></span></a></span>",
								'param_name'       => 'notification',
								'edit_field_class' => 'ult-param-important-wrapper ult-dashicon ult-align-right ult-bold-font ult-blue-font vc_column vc_col-sm-12',
							),
							array(
								'type'             => 'css_editor',
								'heading'          => __( 'Css', 'ultimate_vc' ),
								'param_name'       => 'css_info_list',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
							),
						),
						'js_view'                 => 'VcColumnView',
					)
				);
				// Add list item.
				vc_map(
					array(
						'name'            => __( 'Info List Item', 'ultimate_vc' ),
						'base'            => 'info_list_item',
						'class'           => 'vc_info_list',
						'icon'            => 'vc_icon_list',
						'category'        => 'Ultimate VC Addons',
						'content_element' => true,
						'as_child'        => array( 'only' => 'info_list' ),
						'is_container'    => false,
						'params'          => array(
							array(
								'type'             => 'textfield',
								'class'            => '',
								'heading'          => __( 'Title', 'ultimate_vc' ),
								'admin_label'      => true,
								'param_name'       => 'list_title',
								'value'            => '',
								'description'      => __( 'Provide a title for this icon list item.', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-8',
							),
							array(
								'type'             => 'dropdown',
								'heading'          => __( 'Tag', 'ultimate_vc' ),
								'param_name'       => 'heading_tag',
								'value'            => array(
									__( 'Default', 'ultimate_vc' ) => 'h3',
									__( 'H1', 'ultimate_vc' ) => 'h1',
									__( 'H2', 'ultimate_vc' ) => 'h2',
									__( 'H4', 'ultimate_vc' ) => 'h4',
									__( 'H5', 'ultimate_vc' ) => 'h5',
									__( 'H6', 'ultimate_vc' ) => 'h6',
									__( 'Div', 'ultimate_vc' ) => 'div',
									__( 'p', 'ultimate_vc' )  => 'p',
									__( 'span', 'ultimate_vc' ) => 'span',
								),
								'description'      => __( 'Default is H3', 'ultimate_vc' ),
								'edit_field_class' => 'ult-param-padding-remove vc_col-sm-4',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon to display:', 'ultimate_vc' ),
								'param_name'  => 'icon_type',
								'value'       => array(
									__( 'Font Icon Manager', 'ultimate_vc' ) => 'selector',
									__( 'Custom Image Icon', 'ultimate_vc' ) => 'custom',
								),
								'description' => __( 'Use existing font icon or upload a custom image.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'icon_manager',
								'class'       => '',
								'heading'     => __( 'Select List Icon ', 'ultimate_vc' ),
								'param_name'  => 'list_icon',
								'value'       => '',
								'description' => __( "Click and select icon of your choice. If you can't find the one that suits for your purpose", 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-font-icon-manager' target='_blank' rel='noopener'>" . __( 'add new here', 'ultimate_vc' ) . '</a>.',
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
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
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon Animation', 'ultimate_vc' ),
								'param_name'  => 'animation',
								'value'       => array(
									__( 'No Animation', 'ultimate_vc' ) => '',
									__( 'Swing', 'ultimate_vc' ) => 'swing',
									__( 'Pulse', 'ultimate_vc' ) => 'pulse',
									__( 'Fade In', 'ultimate_vc' ) => 'fadeIn',
									__( 'Fade In Up', 'ultimate_vc' ) => 'fadeInUp',
									__( 'Fade In Down', 'ultimate_vc' ) => 'fadeInDown',
									__( 'Fade In Left', 'ultimate_vc' ) => 'fadeInLeft',
									__( 'Fade In Right', 'ultimate_vc' ) => 'fadeInRight',
									__( 'Fade In Up Long', 'ultimate_vc' ) => 'fadeInUpBig',
									__( 'Fade In Down Long', 'ultimate_vc' ) => 'fadeInDownBig',
									__( 'Fade In Left Long', 'ultimate_vc' ) => 'fadeInLeftBig',
									__( 'Fade In Right Long', 'ultimate_vc' ) => 'fadeInRightBig',
									__( 'Slide In Down', 'ultimate_vc' ) => 'slideInDown',
									__( 'Slide In Left', 'ultimate_vc' ) => 'slideInLeft',
									__( 'Slide In Left', 'ultimate_vc' ) => 'slideInLeft',
									__( 'Bounce In', 'ultimate_vc' ) => 'bounceIn',
									__( 'Bounce In Up', 'ultimate_vc' ) => 'bounceInUp',
									__( 'Bounce In Down', 'ultimate_vc' ) => 'bounceInDown',
									__( 'Bounce In Left', 'ultimate_vc' ) => 'bounceInLeft',
									__( 'Bounce In Right', 'ultimate_vc' ) => 'bounceInRight',
									__( 'Rotate In', 'ultimate_vc' ) => 'rotateIn',
									__( 'Light Speed In', 'ultimate_vc' ) => 'lightSpeedIn',
									__( 'Roll In', 'ultimate_vc' ) => 'rollIn',
								),
								'description' => __( 'Select the animation style for icon.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'textarea_html',
								'class'       => '',
								'heading'     => __( 'Description', 'ultimate_vc' ),
								'param_name'  => 'content',
								'value'       => '',
								'description' => __( 'Description about this list item', 'ultimate_vc' ),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Apply link To', 'ultimate_vc' ),
								'param_name' => 'info_list_link_apply',
								'value'      => array(
									__( 'No Link', 'ultimate_vc' ) => 'no-link',
									__( 'Complete Container', 'ultimate_vc' ) => 'container',
									__( 'List Title', 'ultimate_vc' ) => 'title',
									__( 'Icon', 'ultimate_vc' ) => 'icon',
								),
							),
							array(
								'type'       => 'vc_link',
								'heading'    => __( 'Link', 'ultimate_vc' ),
								'param_name' => 'info_list_link',
								'dependency' => array(
									'element' => 'info_list_link_apply',
									'value'   => array( 'container', 'title', 'icon' ),
								),
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'title_text_typography',
								'text'             => __( 'Title Settings', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Typography',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
							),
							array(
								'type'       => 'ultimate_google_fonts',
								'heading'    => __( 'Font Family', 'ultimate_vc' ),
								'param_name' => 'title_font',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'title_font_style',
								'value'      => '',
								'group'      => 'Typography',
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
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Font Line Height', 'ultimate_vc' ),
								'param_name' => 'title_font_line_height',
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
								'type'       => 'colorpicker',
								'param_name' => 'title_font_color',
								'heading'    => __( 'Color', 'ultimate_vc' ),
								'group'      => 'Typography',
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'desc_text_typography',
								'text'             => __( 'Description Settings', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Typography',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'       => 'ultimate_google_fonts',
								'heading'    => __( 'Font Family', 'ultimate_vc' ),
								'param_name' => 'desc_font',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'desc_font_style',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
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
								'heading'    => __( 'Font Line Height', 'ultimate_vc' ),
								'param_name' => 'desc_font_line_height',
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
								'type'       => 'colorpicker',
								'param_name' => 'desc_font_color',
								'heading'    => __( 'Color', 'ultimate_vc' ),
								'group'      => 'Typography',
							),
						),
					)
				);
			}//endif
		}
	}
}
global $a_i_o_info_list; // WPB: Beter to create singleton in AIO_Info_list I think, but it also work.
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	if ( ! class_exists( 'WPBakeryShortCode_Info_List' ) ) {
		/**
		 * WPBakeryShortCode_info_list initial setup
		 */
		class WPBakeryShortCode_Info_List extends WPBakeryShortCodesContainer {
			/**
			 * Content.
			 *
			 * @param array  $atts represts module attribuits.
			 * @param string $content value has been set to null.
			 * @access public
			 */
			public function content( $atts, $content = null ) {
				global $a_i_o_info_list;
				return $a_i_o_info_list->front_info_list( $atts, $content );
			}
		}
	}
	if ( ! class_exists( 'WPBakeryShortCode_Info_List_Item' ) ) {
		/**
		 * WPBakeryShortCode_info_list_item initial setup
		 */
		class WPBakeryShortCode_Info_List_Item extends WPBakeryShortCode {
			/**
			 * Content.
			 *
			 * @param array  $atts represts module attribuits.
			 * @param string $content value has been set to null.
			 * @access public
			 */
			public function content( $atts, $content = null ) {
				global $a_i_o_info_list;
				return $a_i_o_info_list->front_info_list_item( $atts, $content );
			}
		}
	}
}
if ( class_exists( 'AIO_Info_List' ) ) {
	$a_i_o_info_list = new AIO_Info_List();
}

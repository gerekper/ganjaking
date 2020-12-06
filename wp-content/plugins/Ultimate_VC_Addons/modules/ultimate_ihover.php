<?php
/**
 * Add-on Name: Ultimate iHover
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package Ultimate iHover
 */

if ( ! class_exists( 'ULT_IHover' ) ) {
	/**
	 * Function that initializes Ultimate iHover Module
	 *
	 * @class ULT_IHover
	 */
	class ULT_IHover {
		/**
		 * Constructor function that constructs default values for the Ultimate iHover module.
		 *
		 * @method __construct
		 */
		public function __construct() {

			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'ult_ihover_init' ) );
			}
			add_shortcode( 'ult_ihover', array( $this, 'ult_ihover_callback' ) );
			add_shortcode( 'ult_ihover_item', array( $this, 'ult_ihover_item_callback' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'ult_ihover_scripts' ), 1 );
		}
		/**
		 * Render function for Ultimate iHover Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function ult_ihover_callback( $atts, $content = null ) {
			/*	global variables */
			global $glob_gutter_width, $glob_thumb_height_width, $glob_ihover_shape;

			$glob_gutter_width       = '';
			$glob_thumb_height_width = '';
			$glob_ihover_shape       = '';
			$output                  = '';
				$ult_ihover_setting  = shortcode_atts(
					array(
						'thumb_shape'            => 'circle',
						'el_class'               => '',
						'thumb_height_width'     => '250',
						'res_thumb_height_width' => '',
						'responsive_size'        => 'off',
						'align'                  => 'center',
						'gutter_width'           => '30',
					),
					$atts
				);
			$vc_version              = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
			$is_vc_49_plus           = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'ult-adjust-bottom-margin' : '';

			// Shape.
			$shape = '';
			if ( '' != $ult_ihover_setting['thumb_shape'] ) :
				$glob_ihover_shape = $ult_ihover_setting['thumb_shape'];
				$shape             = ' data-shape="' . $ult_ihover_setting['thumb_shape'] . '" ';
			endif;

			// Height/Width.
			$width  = '';
			$height = '';
			if ( '' != $ult_ihover_setting['thumb_height_width'] ) :
				$glob_thumb_height_width = $ult_ihover_setting['thumb_height_width'];
				$width                   = ' data-width="' . esc_attr( $ult_ihover_setting['thumb_height_width'] ) . '" ';
				$height                  = ' data-height="' . esc_attr( $ult_ihover_setting['thumb_height_width'] ) . '" ';
			endif;

			// Responsive Height/Width.
			$res_width  = '';
			$res_height = '';
			if ( 'on' == $ult_ihover_setting['responsive_size'] && '' != $ult_ihover_setting['res_thumb_height_width'] ) {
				$res_width  = ' data-res_width="' . esc_attr( $ult_ihover_setting['res_thumb_height_width'] ) . '" ';
				$res_height = ' data-res_height="' . esc_attr( $ult_ihover_setting['res_thumb_height_width'] ) . '" ';
			}

			// Gutter Width.
			if ( '' != $ult_ihover_setting['gutter_width'] ) :
				$glob_gutter_width = $ult_ihover_setting['gutter_width'];
			endif;

			// Extra Class.
			$ex_class = '';
			if ( '' != $ult_ihover_setting['el_class'] ) :
				$ex_class = $ult_ihover_setting['el_class'];
			endif;

			$container_style = '';
			if ( '' != $ult_ihover_setting['align'] ) {
				$container_style = 'text-align:' . $ult_ihover_setting['align'] . '; ';}

			$output .= '<div class="ult-ih-container ' . esc_attr( $is_vc_49_plus ) . ' ' . esc_attr( $ex_class ) . ' " >';
			$output .= '	<ul class="ult-ih-list " ' . $shape . '' . $width . '' . $height . '' . $res_width . '' . $res_height . ' style="' . esc_attr( $container_style ) . '">';
			$output .= do_shortcode( $content );
			$output .= '	</ul>';
			$output .= '</div>';

			return $output;
		}
		/**
		 * Render function for Ultimate iHover item Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function ult_ihover_item_callback( $atts, $content = null ) {
			global $glob_gutter_width, $glob_thumb_height_width, $glob_ihover_shape;
			global $glob_gutter_width;
			global $glob_thumb_height_width;
			global $glob_ihover_effectdirection;

			// Item.
			$title_responsive_font_line_height = '';
			$desc_font_size                    = '';
			$desc_font_line_height             = '';
			$item_output                       = '';
			$item_output                       = '';
			$target                            = '';
			$link_title                        = '';
			$rel                               = '';
			$url                               = '';
				$ult_ihover_settings           = shortcode_atts(
					array(
						'thumb_img'                     => '',
						'title'                         => '',
						'heading_tag'                   => '',
						'title_text_typography'         => '',
						'title_font'                    => '',
						'title_font_style'              => '',
						'title_responsive_font_size'    => 'desktop:22px;',
						'title_responsive_line_height'  => 'desktop:28px;',
						'title_font_color'              => '#ffffff',
						'desc_text_typography'          => '',
						'desc_font'                     => '',
						'desc_font_style'               => '',
						'desc_responsive_font_size'     => 'desktop:12px;',
						'desc_responsive_line_height'   => 'desktop:18px;',
						'desc_font_color'               => '#bbbbbb',
						'info_color_bg'                 => 'rgba(0,0,0,0.75)',
						'hover_effect'                  => 'effect1',
						'effect_direction'              => 'right_to_left',
						'spacer_border'                 => 'solid',
						'spacer_border_color'           => 'rgba(255,255,255,0.75)',
						'spacer_width'                  => '100',
						'spacer_border_width'           => '1',
						'block_click'                   => '',
						'block_link'                    => '',
						'thumbnail_border_styling'      => 'solid',
						'block_border_color'            => 'rgba(255,255,255,0.2)',
						'spinner_top_left_border_color' => '#ecab18',
						'spinner_bottom_right_border_color' => '#1ad280',
						'block_border_size'             => '20',
						'effect_scale'                  => 'scale_up',
						'effect_top_bottom'             => 'top_to_bottom',
						'effect_left_right'             => 'left_to_right',
						'title_margin'                  => '',
						'divider_margin'                => '',
						'description_margin'            => '',
					),
					$atts
				);

			$content = wpb_js_remove_wpautop( $content, true ); // fix unclosed/unwanted paragraph tags in $content.

			$info_style             = '';
			$title_style            = '';
			$desc_style             = '';
			$thumbnail_border_style = '';

			if ( '' != $ult_ihover_settings['info_color_bg'] ) :
				$info_style .= 'background-color: ' . $ult_ihover_settings['info_color_bg'] . '; ';
endif;

			if ( '' != $ult_ihover_settings['title_font'] ) {
				$font_family  = get_ultimate_font_family( $ult_ihover_settings['title_font'] );
				$title_style .= 'font-family:\'' . $font_family . '\';';
			}
			if ( '' != $ult_ihover_settings['title_font_style'] ) {
				$title_style .= get_ultimate_font_style( $ult_ihover_settings['title_font_style'] ); }
			if ( '' != $ult_ihover_settings['title_font_color'] ) {
				$title_style .= 'color:' . $ult_ihover_settings['title_font_color'] . ';'; }

			if ( '' != $ult_ihover_settings['desc_font'] ) {
				$font_family = get_ultimate_font_family( $ult_ihover_settings['desc_font'] );
				$desc_style .= 'font-family:\'' . $font_family . '\';';
			}
			if ( '' != $ult_ihover_settings['desc_font_style'] ) {
				$desc_style .= get_ultimate_font_style( $ult_ihover_settings['desc_font_style'] ); }
			if ( '' != $ult_ihover_settings['desc_font_color'] ) {
				$desc_style .= 'color:' . $ult_ihover_settings['desc_font_color'] . ';'; }

			$spacer_line_style = '';
			$spacer_style      = '';
			if ( '' != $ult_ihover_settings['spacer_border'] ) {
				$spacer_line_style .= 'border-style:' . $ult_ihover_settings['spacer_border'] . ';';
				if ( '' != $ult_ihover_settings['spacer_border_color'] ) {
					$spacer_line_style .= 'border-color:' . $ult_ihover_settings['spacer_border_color'] . ';';
				}
				if ( '' != $ult_ihover_settings['spacer_width'] ) {
					$spacer_line_style .= 'width:' . $ult_ihover_settings['spacer_width'] . 'px;';
				}
				if ( '' != $ult_ihover_settings['spacer_border_width'] ) {
					$spacer_line_style .= 'border-width:' . $ult_ihover_settings['spacer_border_width'] . 'px;';
					/* spacer height */
					$spacer_style .= 'height:' . $ult_ihover_settings['spacer_border_width'] . 'px;';
				}
			}

			$thumb_url = '';
			$thumb_alt = '';
			if ( '' != $ult_ihover_settings['thumb_img'] ) {

				$img       = apply_filters( 'ult_get_img_single', $ult_ihover_settings['thumb_img'], 'url' );
				$thumb_alt = apply_filters( 'ult_get_img_single', $ult_ihover_settings['thumb_img'], 'alt' );
				if ( '' == $thumb_alt ) {
					$thumb_alt = 'image';
				}
				$thumb_url = $img;
			}

			if ( '' != $ult_ihover_settings['thumbnail_border_styling'] && 'none' != $ult_ihover_settings['thumbnail_border_styling'] ) {
				$thumbnail_border_style .= 'border-style: ' . $ult_ihover_settings['thumbnail_border_styling'] . '; ';
				if ( '' != $ult_ihover_settings['block_border_color'] ) :
					$thumbnail_border_style .= 'border-color: ' . $ult_ihover_settings['block_border_color'] . '; ';
endif;
				if ( '' != $ult_ihover_settings['block_border_size'] ) :
					$thumbnail_border_style .= 'border-width: ' . $ult_ihover_settings['block_border_size'] . 'px;';
endif;
			}

			$height_width = '';
			$img_width    = '';
			$img_width    = '';
			if ( '' != $glob_thumb_height_width ) {
				$height_width .= 'height: ' . $glob_thumb_height_width . 'px; ';
				$height_width .= 'width: ' . $glob_thumb_height_width . 'px; ';
			}

			$effect = '';
			if ( '' != $ult_ihover_settings['hover_effect'] ) :
				$effect = $ult_ihover_settings['hover_effect'];
			endif;
			$spinner_border_color = null;
			if ( 'effect20' == $effect ) {
				if ( '' != $ult_ihover_settings['spinner_top_left_border_color'] ) :
					$spinner_border_color .= 'border-top-color: ' . $ult_ihover_settings['spinner_top_left_border_color'] . '; border-left-color : ' . $ult_ihover_settings['spinner_top_left_border_color'] . '; ';
				endif;
				if ( '' != $ult_ihover_settings['spinner_bottom_right_border_color'] ) :
					$spinner_border_color .= 'border-bottom-color: ' . $ult_ihover_settings['spinner_bottom_right_border_color'] . '; border-right-color : ' . $ult_ihover_settings['spinner_bottom_right_border_color'] . '; ';
				endif;
			}
			$scale = '';
			switch ( $effect ) {
				case 'effect6':
					if ( '' != $ult_ihover_settings['effect_scale'] ) :
						$scale = 'ult-ih-' . $ult_ihover_settings['effect_scale'];
endif;
					break;
			}

			// Directions: [left, right, top, bottom].
			$direction = '';
			switch ( $effect ) {
				case 'effect2':
				case 'effect3':
				case 'effect4':
				case 'effect7':
				case 'effect8':
				case 'effect9':
				case 'effect11':
				case 'effect12':
				case 'effect13':
				case 'effect14':
				case 'effect18':
					if ( '' != $ult_ihover_settings['effect_direction'] ) :
						$direction = 'ult-ih-' . $ult_ihover_settings['effect_direction'];
endif;
					break;
			}

			$top_bottom = '';
			switch ( $effect ) {
				case 'effect10':
				case 'effect1':
				case 'effect20':
					if ( '' != $ult_ihover_settings['effect_top_bottom'] ) :
						$top_bottom = 'ult-ih-' . $ult_ihover_settings['effect_top_bottom'];
endif;
					break;
			}

			$left_right = '';
			switch ( $effect ) {
				case 'effect16':
					if ( '' != $ult_ihover_settings['effect_left_right'] ) :
						$left_right = 'ult-ih-' . $ult_ihover_settings['effect_left_right'];
endif;
					break;
			}

			$gutter_margin = '';
			if ( '' != $glob_gutter_width ) {
				$gutter_margin = 'margin: ' . ( $glob_gutter_width / 2 ) . 'px';
			}

			$heading_block     = '';
			$description_block = '';
			if ( '' != $ult_ihover_settings['title_margin'] ) {
				$heading_block .= $ult_ihover_settings['title_margin'];   }
			if ( '' != $ult_ihover_settings['description_margin'] ) {
				$description_block .= $ult_ihover_settings['description_margin']; }
			if ( '' != $ult_ihover_settings['divider_margin'] ) {
				$spacer_style .= $ult_ihover_settings['divider_margin']; }

			if ( '' != $ult_ihover_settings['block_link'] ) {
				$href = vc_build_link( $ult_ihover_settings['block_link'] );

				$url        = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
				$target     = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
				$link_title = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
				$rel        = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
			}

			$item_id = 'ult-ih-list-item-' . wp_rand( 1000, 9999 );

			// responsive font size and line height for title.
			$args             = array(
				'target'      => '#' . $item_id . ' .ult-ih-heading',
				'media_sizes' => array(
					'font-size'   => $ult_ihover_settings['title_responsive_font_size'],
					'line-height' => $ult_ihover_settings['title_responsive_line_height'],
				),
			);
			$title_responsive = get_ultimate_vc_responsive_media_css( $args );

			// Assigning tag to title.
			$ult_ihover_settings['heading_tag'] = ( isset( $ult_ihover_settings['heading_tag'] ) && trim( $ult_ihover_settings['heading_tag'] ) != '' ) ? $ult_ihover_settings['heading_tag'] : 'h3';

			// resposnive font size and line height for description.
			$args            = array(
				'target'      => '#' . $item_id . ' .ult-ih-description, #' . $item_id . ' .ult-ih-description p',
				'media_sizes' => array(
					'font-size'   => $ult_ihover_settings['desc_responsive_font_size'],
					'line-height' => $ult_ihover_settings['desc_responsive_line_height'],
				),
			);
			$desc_responsive = get_ultimate_vc_responsive_media_css( $args );

			$item_output .= '<li id="' . esc_attr( $item_id ) . '" class="ult-ih-list-item" style="' . esc_attr( $height_width ) . ' ' . esc_attr( $gutter_margin ) . '">';
			if ( '' != $ult_ihover_settings['block_click'] ) {
				$item_output .= '<a class="ult-ih-link" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . ' ><div style="' . esc_attr( $height_width ) . '" class="ult-ih-item ult-ih-' . esc_attr( $effect ) . ' ' . esc_attr( $left_right ) . ' ' . esc_attr( $direction ) . ' ' . esc_attr( $scale ) . ' ' . esc_attr( $top_bottom ) . '">';
			} else {
				$item_output .= '<div style="' . esc_attr( $height_width ) . '" class="ult-ih-item ult-ih-' . esc_attr( $effect ) . ' ' . esc_attr( $left_right ) . ' ' . esc_attr( $direction ) . ' ' . esc_attr( $scale ) . ' ' . esc_attr( $top_bottom ) . ' ">';
			}

			$height_widthe20 = '';
			$img_width       = '';
			$img_width       = '';
			if ( '' != $glob_thumb_height_width && 'effect20' == $effect ) {
				$glob_thumb_height_width = $glob_thumb_height_width;
				$height_widthe20        .= 'height: ' . $glob_thumb_height_width . 'px; ';
				$height_widthe20        .= 'width: ' . $glob_thumb_height_width . 'px; ';
			}

			switch ( $effect ) {

				case 'effect8':
								$item_output .= '<div class="ult-ih-image-block-container">';
								$item_output .= '	<div class="ult-ih-image-block" style="' . esc_attr( $height_width ) . '">';
								$item_output .= '		<div class="ult-ih-wrapper" style="' . esc_attr( $thumbnail_border_style ) . '"></div>';
								$item_output .= '		<img class="ult-ih-image" src="' . esc_url( apply_filters( 'ultimate_images', $thumb_url ) ) . '" alt="' . esc_attr( $thumb_alt ) . '">';
								$item_output .= '	</div> ';
								$item_output .= '</div>';

								$item_output .= '<div class="info-container">';
								$item_output .= '	<div class="ult-ih-info" style="' . esc_attr( $info_style ) . '">';
								$item_output .= $this->common_structure( $desc_responsive, $title_responsive, $heading_block, $title_style, $ult_ihover_settings, $spacer_style, $spacer_line_style, $description_block, $desc_style, $content );
								$item_output .= '	</div>';
								$item_output .= '</div>';

					break;

				case 'effect1':
				case 'effect5':
				case 'effect18':
					$item_output .= '<div class="ult-ih-image-block" style="' . esc_attr( $height_width ) . '">';
					$item_output .= '	<div class="ult-ih-wrapper" style="' . esc_attr( $thumbnail_border_style ) . '"></div>';
					$item_output .= '	<img class="ult-ih-image" src="' . esc_attr( apply_filters( 'ultimate_images', $thumb_url ) ) . '" alt="' . esc_attr( $thumb_alt ) . '">';
					$item_output .= '</div>';

					$item_output .= '<div class="ult-ih-info" >';
					$item_output .= '	<div class="ult-ih-info-back" style="' . esc_attr( $info_style ) . '">';

					$item_output .= $this->common_structure( $desc_responsive, $title_responsive, $heading_block, $title_style, $ult_ihover_settings, $spacer_style, $spacer_line_style, $description_block, $desc_style, $content );

					$item_output .= '	</div>';
					$item_output .= '</div>';
					break;

				case 'effect20':
					$item_output .= '<div class="spinner" style="' . esc_attr( $height_widthe20 ) . ' ' . esc_attr( $spinner_border_color ) . ' "></div>';
					$item_output .= '<div class="ult-ih-image-block">';

					$item_output .= '	<img class="ult-ih-image" src="' . esc_attr( apply_filters( 'ultimate_images', $thumb_url ) ) . '" alt="' . esc_attr( $thumb_alt ) . '">';
					$item_output .= '</div>';

					$item_output .= '<div class="ult-ih-info" >';
					$item_output .= '	<div class="ult-ih-info-back" style="' . esc_attr( $info_style ) . '">';

					$item_output .= $this->common_structure( $desc_responsive, $title_responsive, $heading_block, $title_style, $ult_ihover_settings, $spacer_style, $spacer_line_style, $description_block, $desc_style, $content );

					$item_output .= '	</div>';
					$item_output .= '</div>';
					break;

				default:
					$item_output .= '<div class="ult-ih-image-block" style="' . esc_attr( $height_width ) . '">';
					$item_output .= '	<div class="ult-ih-wrapper" style="' . esc_attr( $thumbnail_border_style ) . '"></div>';
					$item_output .= '	<img class="ult-ih-image" src="' . esc_url( apply_filters( 'ultimate_images', $thumb_url ) ) . '" alt="' . esc_attr( $thumb_alt ) . '">';
					$item_output .= '</div>';

					$item_output .= '<div class="ult-ih-info" style="' . esc_attr( $info_style ) . '">';
					$item_output .= '	<div class="ult-ih-info-back">';

					$item_output .= $this->common_structure( $desc_responsive, $title_responsive, $heading_block, $title_style, $ult_ihover_settings, $spacer_style, $spacer_line_style, $description_block, $desc_style, $content );
					$item_output .= '	</div>';
					$item_output .= '</div>';
					break;
			}

			// Check anchor.
			if ( '' != $ult_ihover_settings['block_click'] ) {
				$item_output .= '</div></a>';
			} else {
				$item_output .= '</div>';
			}
			$item_output .= '</li>';

			return $item_output;
		}
		/**
		 * Render function for Ultimate iHover item Module.
		 *
		 * @param string $desc_responsive have a value string.
		 * @param string $title_responsive have a value string.
		 * @param string $heading_block have a value string.
		 * @param string $title_style have a value string.
		 * @param array  $ult_ihover_settings have a value string.
		 * @param string $spacer_style have a value string.
		 * @param string $spacer_line_style have a value string.
		 * @param string $description_block have a value string.
		 * @param string $desc_style have a value string.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function common_structure( $desc_responsive, $title_responsive, $heading_block, $title_style, $ult_ihover_settings, $spacer_style, $spacer_line_style, $description_block, $desc_style, $content ) {
			$item_output = '';

			$item_output .= '	<div class="ult-ih-content">';

			$item_output .= '			<div class="ult-ih-heading-block" style="' . esc_attr( $heading_block ) . '">';
			$item_output .= '				<' . $ult_ihover_settings['heading_tag'] . ' class="ult-ih-heading ult-responsive" style="' . esc_attr( $title_style ) . '" ' . $title_responsive . '>' . $ult_ihover_settings['title'] . '</' . $ult_ihover_settings['heading_tag'] . '>';
			$item_output .= '			</div>';

			$item_output .= '			<div class="ult-ih-divider-block" style="' . esc_attr( $spacer_style ) . '">';
			$item_output .= '				<span class="ult-ih-line" style="' . esc_attr( $spacer_line_style ) . '"></span>';
			$item_output .= '			</div>';

			$item_output .= '			<div class="ult-ih-description-block" style="' . esc_attr( $description_block ) . '">';
			$item_output .= '				<div class="ult-ih-description ult-responsive" style="' . esc_attr( $desc_style ) . '" ' . $desc_responsive . '>';
			if ( '' != $content ) {
				$item_output .= $content;
			}
			$item_output .= '				</div>';
			$item_output .= '			</div>';
			$item_output .= '	</div>';

			return $item_output;
		}
		/**
		 * Function that initializes settings of Ultimate iHover Module.
		 *
		 * @method ult_ihover_init
		 */
		public function ult_ihover_init() {
			// Register "container" content element. It will hold all your inner (child) content elements.
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'                    => __( 'iHover', 'ultimate_vc' ),
						'base'                    => 'ult_ihover',
						'as_parent'               => array( 'only' => 'ult_ihover_item' ), // Use only|except attributes to limit child shortcodes (separate multiple values with comma).
						'content_element'         => true,
						'show_settings_on_create' => true,
						'category'                => 'Ultimate VC Addons',
						'icon'                    => 'ult_ihover',
						'class'                   => 'ult_ihover',
						'description'             => __( 'Image hover effects with information.', 'ultimate_vc' ),
						'params'                  => array(
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Thumbnail Shape', 'ultimate_vc' ),
								'param_name'  => 'thumb_shape',
								'value'       => array(
									'Circle' => 'circle',
									'Square' => 'square',
								),
								'admin_label' => true,
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Thumbnail Height & Width', 'ultimate_vc' ),
								'param_name'  => 'thumb_height_width',
								'admin_label' => true,
								'suffix'      => 'px',
								'value'       => '',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Responsive Size', 'ultimate_vc' ),
								'param_name'  => 'responsive_size',
								'default_set' => true,
								'value'       => '',
								'options'     => array(
									'on' => array(
										'label' => __( 'Add responsive size below 768px.', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
							),
							array(
								'type'       => 'number',
								'heading'    => __( 'Responsive Thumbnail Height & Width', 'ultimate_vc' ),
								'param_name' => 'res_thumb_height_width',
								'suffix'     => 'px',
								'value'      => '',
								'dependency' => array(
									'element' => 'responsive_size',
									'value'   => 'on',
								),
							),
							array(
								'type'       => 'number',
								'heading'    => __( 'Spacing Between Two Thumbnails', 'ultimate_vc' ),
								'param_name' => 'gutter_width',
								'suffix'     => 'px',
								'value'      => '',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'iHover Alignment', 'ultimate_vc' ),
								'param_name' => 'align',
								'value'      => array(
									__( 'Center', 'ultimate_vc' )    => 'center',
									__( 'Left', 'ultimate_vc' )  => 'left',
									__( 'Right', 'ultimate_vc' )     => 'right',
								),
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Extra class name', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'ultimate_vc' ),
							),
						),
						'js_view'                 => 'VcColumnView',
					)
				);

				vc_map(
					array(
						'name'            => __( 'iHover Item', 'ultimate_vc' ),
						'base'            => 'ult_ihover_item',
						'content_element' => true,
						'icon'            => 'ult_ihover',
						'class'           => 'ult_ihover',
						'as_child'        => array( 'only' => 'ult_ihover' ), // Use only|except attributes to limit parent (separate multiple values with comma).
						'is_container'    => false,
						'params'          => array(
							// General.
							array(
								'type'             => 'textfield',
								'class'            => '',
								'heading'          => __( 'Title', 'ultimate_vc' ),
								'param_name'       => 'title',
								'admin_label'      => true,
								'value'            => '',
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
									__( 'p', 'ultimate_vc' ) => 'p',
									__( 'span', 'ultimate_vc' ) => 'span',
								),
								'description'      => __( 'Default is H3', 'ultimate_vc' ),
								'edit_field_class' => 'ult-param-padding-remove vc_col-sm-4',
							),
							array(
								'type'        => 'ult_img_single',
								'class'       => '',
								'heading'     => __( 'Upload Image', 'ultimate_vc' ),
								'param_name'  => 'thumb_img',
								'value'       => '',
								'description' => __( 'Upload image.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'textarea_html',
								'holder'           => '',
								'class'            => '',
								'heading'          => __( 'Description', 'ultimate_vc' ),
								'param_name'       => 'content',
								'value'            => '',
								'description'      => __( 'Provide the description for the iHover.', 'ultimate_vc' ),
								'edit_field_class' => 'ult_hide_editor_fullscreen vc_col-xs-12 vc_column wpb_el_type_textarea_html vc_wrapper-param-type-textarea_html vc_shortcode-param',
							),

							// Effects.
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Hover Effect', 'ultimate_vc' ),
								'param_name'  => 'hover_effect',
								'admin_label' => true,
								'value'       => array(
									__( 'Effect 1', 'ultimate_vc' ) => 'effect1',
									__( 'Effect 2', 'ultimate_vc' ) => 'effect2',
									__( 'Effect 3', 'ultimate_vc' ) => 'effect3',
									__( 'Effect 4', 'ultimate_vc' ) => 'effect4',
									__( 'Effect 5', 'ultimate_vc' ) => 'effect5',
									__( 'Effect 6', 'ultimate_vc' ) => 'effect6',
									__( 'Effect 7', 'ultimate_vc' ) => 'effect7',
									__( 'Effect 8', 'ultimate_vc' ) => 'effect8',
									__( 'Effect 9', 'ultimate_vc' ) => 'effect9',
									__( 'Effect 10', 'ultimate_vc' ) => 'effect10',
									__( 'Effect 11', 'ultimate_vc' ) => 'effect11',
									__( 'Effect 12', 'ultimate_vc' ) => 'effect12',
									__( 'Effect 13', 'ultimate_vc' ) => 'effect13',
									__( 'Effect 14', 'ultimate_vc' ) => 'effect14',
									__( 'Effect 15', 'ultimate_vc' ) => 'effect15',
									__( 'Effect 16', 'ultimate_vc' ) => 'effect16',
									__( 'Effect 17', 'ultimate_vc' ) => 'effect17',
									__( 'Effect 18', 'ultimate_vc' ) => 'effect18',
									__( 'Effect 19', 'ultimate_vc' ) => 'effect19',
									__( 'Effect 20', 'ultimate_vc' ) => 'effect20',
								),
								'description' => __( 'Select the Hover Effect for iHover.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Hover Effect Direction', 'ultimate_vc' ),
								'param_name'  => 'effect_direction',
								'value'       => array(
									__( 'Towards Left', 'ultimate_vc' ) => 'right_to_left',
									__( 'Towards Right', 'ultimate_vc' ) => 'left_to_right',
									__( 'Towards Top', 'ultimate_vc' ) => 'bottom_to_top',
									__( 'Towards Bottom', 'ultimate_vc' ) => 'top_to_bottom',
								),
								'description' => __( 'Select the Hover Effect Direction for iHover.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'hover_effect',
									'value'   => array( 'effect2', 'effect3', 'effect4', 'effect7', 'effect8', 'effect9', 'effect11', 'effect12', 'effect13', 'effect14', 'effect18' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Hover Effect Scale', 'ultimate_vc' ),
								'param_name'  => 'effect_scale',
								'value'       => array(
									__( 'Scale Up', 'ultimate_vc' ) => 'scale_up',
									__( 'Scale Down', 'ultimate_vc' ) => 'scale_down',
									__( 'Scale Down Up', 'ultimate_vc' ) => 'scale_down_up',
								),
								'description' => __( 'Select the Hover Effect Scale for iHover.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'hover_effect',
									'value'   => 'effect6',
								),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Hover Effect Direction', 'ultimate_vc' ),
								'param_name'  => 'effect_top_bottom',
								'value'       => array(
									__( 'Top to Bottom', 'ultimate_vc' ) => 'top_to_bottom',
									__( 'Bottom to Top', 'ultimate_vc' ) => 'bottom_to_top',
								),
								'description' => __( 'Select the Hover Effect Direction for iHover.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'hover_effect',
									'value'   => array( 'effect10' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Hover Effect Direction', 'ultimate_vc' ),
								'param_name'  => 'effect_left_right',
								'value'       => array(
									__( 'Left to Right', 'ultimate_vc' ) => 'left_to_right',
									__( 'Right to Left', 'ultimate_vc' ) => 'right_to_left',
								),
								'description' => __( 'Select the Hover Effect Direction for iHover.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'hover_effect',
									'value'   => 'effect16',
								),
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'On Click', 'ultimate_vc' ),
								'param_name' => 'block_click',
								'value'      => array(
									__( 'Do Nothing', 'ultimate_vc' ) => '',
									__( 'Link', 'ultimate_vc' ) => 'link',
								),
							),
							array(
								'type'        => 'vc_link',
								'class'       => '',
								'heading'     => __( 'Apply Link to:', 'ultimate_vc' ),
								'param_name'  => 'block_link',
								'value'       => '',
								'description' => __( 'Provide the link for iHover.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'block_click',
									'value'   => 'link',
								),
							),
							array(
								'type'       => 'colorpicker',
								'param_name' => 'title_font_color',
								'heading'    => __( 'Title Color', 'ultimate_vc' ),
								'group'      => 'Design',
								'value'      => '#ffffff',
							),
							array(
								'type'       => 'colorpicker',
								'param_name' => 'desc_font_color',
								'heading'    => __( 'Description Color', 'ultimate_vc' ),
								'group'      => 'Design',
								'value'      => '',
							),
							array(
								'type'       => 'colorpicker',
								'param_name' => 'info_color_bg',
								'heading'    => __( 'iHover Background Color', 'ultimate_vc' ),
								'group'      => 'Design',
								'value'      => '',
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'thumbnail_border_styling_text',
								'text'             => __( 'Thumbnail Border Styling', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Design',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
								'dependency'       => array(
									'element' => 'hover_effect',
									'value'   => array( 'effect1', 'effect2', 'effect3', 'effect4', 'effect5', 'effect6', 'effect7', 'effect8', 'effect9', 'effect10', 'effect11', 'effect12', 'effect13', 'effect14', 'effect15', 'effect16', 'effect17', 'effect18', 'effect19' ),
								),
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'spinner_border_styling_text',
								'text'             => __( 'Spinner Border Styling', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Design',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
								'dependency'       => array(
									'element' => 'hover_effect',
									'value'   => array( 'effect20' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Border Style', 'ultimate_vc' ),
								'param_name'  => 'thumbnail_border_styling',
								'value'       => array(
									__( 'Solid', 'ultimate_vc' ) => 'solid',
									__( 'None', 'ultimate_vc' ) => 'none',
								),
								'description' => __( 'Select Thumbnail Border Style for iHover.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'hover_effect',
									'value'   => array( 'effect1', 'effect2', 'effect3', 'effect4', 'effect5', 'effect6', 'effect7', 'effect8', 'effect9', 'effect10', 'effect11', 'effect12', 'effect13', 'effect14', 'effect15', 'effect16', 'effect17', 'effect18', 'effect19' ),
								),
								'group'       => 'Design',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Border Color', 'ultimate_vc' ),
								'param_name' => 'block_border_color',
								'value'      => '',
								'dependency' => array(
									'element' => 'thumbnail_border_styling',
									'value'   => 'solid',
								),
								'group'      => 'Design',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Spinner - Top-Left', 'ultimate_vc' ),
								'param_name'  => 'spinner_top_left_border_color',
								'value'       => '#ecab18',
								'description' => __( 'Select Spinner - Top-Left Border Color.', 'ultimate' ),
								'dependency'  => array(
									'element' => 'hover_effect',
									'value'   => array( 'effect20' ),
								),
								'group'       => 'Design',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Spinner - Bottom-Right', 'ultimate_vc' ),
								'param_name'  => 'spinner_bottom_right_border_color',
								'value'       => '#1ad280',
								'description' => __( 'Select Spinner - Bottom Right Border Color.', 'ultimate' ),
								'dependency'  => array(
									'element' => 'hover_effect',
									'value'   => array( 'effect20' ),
								),
								'group'       => 'Design',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Border Thickness', 'ultimate_vc' ),
								'param_name' => 'block_border_size',
								'value'      => '',
								'suffix'     => 'px',
								'dependency' => array(
									'element' => 'thumbnail_border_styling',
									'value'   => 'solid',
								),
								'group'      => 'Design',
							),

							// Divider.
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'thumbnail_divider_styling_text',
								'text'             => __( 'Heading & Description Divider', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Design',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Divider - Style', 'ultimate_vc' ),
								'param_name'  => 'spacer_border',
								'value'       => array(
									__( 'Solid', 'ultimate_vc' )  => 'solid',
									__( 'Dashed', 'ultimate_vc' ) => 'dashed',
									__( 'Dotted', 'ultimate_vc' ) => 'dotted',
									__( 'Double', 'ultimate_vc' ) => 'double',
									__( 'Inset', 'ultimate_vc' ) => 'inset',
									__( 'Outset', 'ultimate_vc' ) => 'outset',
									__( 'None', 'ultimate_vc' )  => 'none',
								),
								'description' => __( "Select Heading & Description's Divider Border Style.", 'ultimate_vc' ),
								'group'       => 'Design',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Divider - Border Color', 'ultimate_vc' ),
								'param_name'  => 'spacer_border_color',
								'value'       => 'rgba(255,255,255,0.75)',
								'description' => __( 'Select Divider Border Color.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'spacer_border',
									'value'   => array( 'solid', 'dashed', 'dotted', 'double', 'inset', 'outset' ),
								),
								'group'       => 'Design',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Divider - Line Width (optional)', 'ultimate_vc' ),
								'param_name'  => 'spacer_width',
								'value'       => '',
								'suffix'      => 'px',
								'description' => __( 'Width of Divider Border. Default: 100%;', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'spacer_border',
									'value'   => array( 'solid', 'dashed', 'dotted', 'double', 'inset', 'outset' ),
								),
								'group'       => 'Design',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Divider - Border Thickness', 'ultimate_vc' ),
								'param_name'  => 'spacer_border_width',
								'value'       => '',
								'suffix'      => 'px',
								'description' => __( 'Height of Divider Border.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'spacer_border',
									'value'   => array( 'solid', 'dashed', 'dotted', 'double', 'inset', 'outset' ),
								),
								'group'       => 'Design',
							),

							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'thumbnail_spacing_styling_text',
								'text'             => __( 'Spacing', 'ultimate_vc' ),
								'value'            => '',
								'description'      => __( 'Add Space Between Title, Divider and Description. Just put only numbers in textbox. All values will consider in px.', 'ultimate_vc' ),
								'group'            => 'Design',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'       => 'ultimate_margins',
								'heading'    => __( 'Title Margins', 'ultimate_vc' ),
								'param_name' => 'title_margin',
								'positions'  => array(
									__( 'Top', 'ultimate_vc' ) => 'top',
									__( 'Bottom', 'ultimate_vc' ) => 'bottom',
									__( 'Left', 'ultimate_vc' ) => 'left',
									__( 'Right', 'ultimate_vc' ) => 'right',
								),
								'group'      => 'Design',
							),
							array(
								'type'       => 'ultimate_margins',
								'heading'    => __( 'Divider Margins', 'ultimate_vc' ),
								'param_name' => 'divider_margin',
								'positions'  => array(
									__( 'Top', 'ultimate_vc' ) => 'top',
									__( 'Bottom', 'ultimate_vc' ) => 'bottom',
									__( 'Left', 'ultimate_vc' ) => 'left',
									__( 'Right', 'ultimate_vc' ) => 'right',
								),
								'group'      => 'Design',
							),
							array(
								'type'       => 'ultimate_margins',
								'heading'    => __( 'Description Margins', 'ultimate_vc' ),
								'param_name' => 'description_margin',
								'positions'  => array(
									__( 'Top', 'ultimate_vc' ) => 'top',
									__( 'Bottom', 'ultimate_vc' ) => 'bottom',
									__( 'Left', 'ultimate_vc' ) => 'left',
									__( 'Right', 'ultimate_vc' ) => 'right',
								),
								'group'      => 'Design',
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'title_text_typography',
								'text'             => __( 'Title settings', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Typography',
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
								'param_name' => 'title_responsive_font_size',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '22',
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
								'param_name' => 'title_responsive_line_height',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '28',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'desc_text_typography',
								'text'             => __( 'Description settings', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Typography',
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
								'param_name' => 'desc_responsive_font_size',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '12',
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
								'param_name' => 'desc_responsive_line_height',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '18',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
						),
					)
				);
			}
		}
		/**
		 * Load plugin css and javascript files which you may need on front end of your site.
		 *
		 * @method ult_ihover_scripts
		 */
		public function ult_ihover_scripts() {

			Ultimate_VC_Addons::ultimate_register_style( 'ult_ihover_css', 'ihover' );

			Ultimate_VC_Addons::ultimate_register_script( 'ult_ihover_js', 'ihover', false, array( 'jquery' ), ULTIMATE_VERSION, true );
		}
	}
	// Finally initialize code.
	new ULT_IHover();

	if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Ult_Ihover' ) ) {
		/**
		 * Function that checks if the class is exists or not.
		 */
		class WPBakeryShortCode_Ult_Ihover extends WPBakeryShortCodesContainer {
		}
	}
	if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Ult_Ihover_Item' ) ) {
		/**
		 * Function that checks if the class is exists or not.
		 */
		class WPBakeryShortCode_Ult_Ihover_Item extends WPBakeryShortCode {
		}
	}
}

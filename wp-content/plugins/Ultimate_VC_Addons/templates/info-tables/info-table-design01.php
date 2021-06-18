<?php
/**
 * Add-on Name: Info Tables for WPBakery Page Builder
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package UAVC Design layout 01
 */

if ( ! function_exists( 'ult_info_table_generate_design01' ) ) {
	/**
	 * For the design shortcode.
	 *
	 * @since ----
	 * @param array  $atts represts module attribuits.
	 * @param string $content value has been set to null.
	 * @access public
	 */
	function ult_info_table_generate_design01( $atts, $content = null ) {
		$package_price                        = '';
		$package_unit                         = '';
		$target                               = '';
		$link_title                           = '';
		$rel                                  = '';
		$ult_info_design01                    = shortcode_atts(
			array(
				'color_scheme'           => 'black',
				'package_heading'        => '',
				'heading_tag'            => 'h3',
				'package_sub_heading'    => '',
				'sub_heading_tag'        => 'h5',
				'icon_type'              => 'none',
				'icon'                   => '',
				'icon_img'               => '',
				'img_width'              => '48',
				'icon_size'              => '32',
				'icon_color'             => '#333333',
				'icon_style'             => 'none',
				'icon_color_bg'          => '#ffffff',
				'icon_color_border'      => '#333333',
				'icon_border_style'      => '',
				'icon_border_size'       => '1',
				'icon_border_radius'     => '500',
				'icon_border_spacing'    => '50',
				'use_cta_btn'            => '',
				'package_btn_text'       => '',
				'package_link'           => '',
				'package_featured'       => '',
				'color_bg_main'          => '',
				'color_txt_main'         => '',
				'color_bg_highlight'     => '',
				'color_txt_highlight'    => '',
				'heading_font_family'    => '',
				'heading_font_style'     => '',
				'heading_font_size'      => '',
				'heading_font_color'     => '',
				'heading_line_height'    => '',
				'subheading_font_family' => '',
				'subheading_font_style'  => '',
				'subheading_font_size'   => '',
				'subheading_font_color'  => '',
				'subheading_line_height' => '',
				'features_font_family'   => '',
				'features_font_style'    => '',
				'features_font_size'     => '',
				'features_font_color'    => '',
				'features_line_height'   => '',
				'button_font_family'     => '',
				'button_font_style'      => '',
				'button_font_size'       => '',
				'button_font_color'      => '',
				'button_line_height'     => '',
				'el_class'               => '',
				'features_min_ht'        => '',
				'css_info_tables'        => '',
			),
			$atts
		);
		$ult_info_design01['css_info_tables'] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_info_design01['css_info_tables'], ' ' ), 'ultimate_info_table', $atts );
		$ult_info_design01['css_info_tables'] = esc_attr( $ult_info_design01['css_info_tables'] );

		$output         = '';
		$link           = '';
		$target         = '';
		$featured       = '';
		$featured_style = '';
		$normal_style   = '';
		$dynamic_style  = '';
		$box_icon       = '';
		if ( 'none' !== $ult_info_design01['icon_type'] ) {
			$box_icon = do_shortcode( '[just_icon icon_type="' . esc_attr( $ult_info_design01['icon_type'] ) . '" icon="' . esc_attr( $ult_info_design01['icon'] ) . '" icon_img="' . esc_attr( $ult_info_design01['icon'] ) . '" img_width="' . esc_attr( $ult_info_design01['img_width'] ) . '" icon_size="' . esc_attr( $ult_info_design01['icon_size'] ) . '" icon_color="' . esc_attr( $ult_info_design01['icon_color'] ) . '" icon_style="' . esc_attr( $ult_info_design01['icon_style'] ) . '" icon_color_bg="' . esc_attr( $ult_info_design01['icon_color_bg'] ) . '" icon_color_border="' . esc_attr( $ult_info_design01['icon_color_border'] ) . '"  icon_border_style="' . esc_attr( $ult_info_design01['icon_border_style'] ) . '" icon_border_size="' . esc_attr( $ult_info_design01['icon_border_size'] ) . '" icon_border_radius="' . esc_attr( $ult_info_design01['icon_border_radius'] ) . '" icon_border_spacing="' . esc_attr( $ult_info_design01['icon_border_spacing'] ) . '"]' );
		}
		if ( 'custom' == $ult_info_design01['color_scheme'] ) {
			if ( '' !== $ult_info_design01['color_bg_main'] ) {
				$normal_style .= 'background:' . $ult_info_design01['color_bg_main'] . ';';
			}
			if ( '' !== $ult_info_design01['color_txt_main'] ) {
				$normal_style .= 'color:' . $ult_info_design01['color_txt_main'] . ';';
			}
			if ( '' !== $ult_info_design01['color_bg_highlight'] ) {
				$featured_style .= 'background:' . $ult_info_design01['color_bg_highlight'] . ';';
			}
			if ( '' !== $ult_info_design01['color_txt_highlight'] ) {
				$featured_style .= 'color:' . $ult_info_design01['color_txt_highlight'] . ';';
			}
		}
		if ( '' !== $ult_info_design01['package_link'] ) {
			$href = vc_build_link( $ult_info_design01['package_link'] );

			$link       = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
			$target     = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
			$link_title = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
			$rel        = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
		} else {
			$link = '#';
		}
		if ( '' !== $ult_info_design01['package_featured'] ) {
			$featured      = 'ult_featured';
			$dynamic_style = $featured_style;
		} else {
			$dynamic_style = $normal_style;
		}
		if ( 'box' == $ult_info_design01['use_cta_btn'] ) {
			$output .= '<a ' . Ultimate_VC_Addons::uavc_link_init( $link, $target, $link_title, $rel ) . ' class="ult_price_action_button">' . $ult_info_design01['package_btn_text'];
		}

		/*---min ht style---*/
		$info_tab_ht       = '';
		$info_tab_ht_style = '';
		if ( '' !== $ult_info_design01['features_min_ht'] ) {
				$info_tab_ht        = 'info_min_ht';
				$info_tab_ht_style .= 'min-height:' . $ult_info_design01['features_min_ht'] . 'px;';
		}

		/* typography */

		$heading_style_inline = '';
		$sub_heading_inline   = '';
		$features_inline      = '';
		$button_inline        = '';

		// heading.
		if ( '' != $ult_info_design01['heading_font_family'] ) {
			$hdfont_family = get_ultimate_font_family( $ult_info_design01['heading_font_family'] );
			if ( '' !== $hdfont_family ) {
				$heading_style_inline .= 'font-family:\'' . $hdfont_family . '\';';
			}
		}

		if ( 'span' == $ult_info_design01['heading_tag'] ) {
			$heading_style_inline .= 'display:block;';
		}
		$heading_style_inline .= get_ultimate_font_style( $ult_info_design01['heading_font_style'] );

		if ( '' != $ult_info_design01['heading_font_color'] ) {
			$heading_style_inline .= 'color:' . $ult_info_design01['heading_font_color'] . ';';
		}

		if ( is_numeric( $ult_info_design01['heading_font_size'] ) ) {
				$ult_info_design01['heading_font_size'] = 'desktop:' . $ult_info_design01['heading_font_size'] . 'px;';
		}

		if ( is_numeric( $ult_info_design01['heading_line_height'] ) ) {
				$ult_info_design01['heading_line_height'] = 'desktop:' . $ult_info_design01['heading_line_height'] . 'px;';
		}

			$info_table_id        = 'Info-table-wrap-' . wp_rand( 1000, 9999 );
			$info_table_args      = array(
				'target'      => '#' . $info_table_id . ' ' . $ult_info_design01['heading_tag'], // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_info_design01['heading_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_info_design01['heading_line_height'],
				),
			);
			$info_table_data_list = get_ultimate_vc_responsive_media_css( $info_table_args );
			// sub heading.
			if ( '' != $ult_info_design01['subheading_font_family'] ) {
				$shfont_family = get_ultimate_font_family( $ult_info_design01['subheading_font_family'] );
				if ( '' !== $shfont_family ) {
					$sub_heading_inline .= 'font-family:\'' . $shfont_family . '\';';
				}
			}

			if ( 'span' == $ult_info_design01['sub_heading_tag'] ) {
				$sub_heading_inline .= 'display:block;';
			}

			$sub_heading_inline .= get_ultimate_font_style( $ult_info_design01['subheading_font_style'] );

			if ( '' != $ult_info_design01['subheading_font_color'] ) {
				$sub_heading_inline .= 'color:' . $ult_info_design01['subheading_font_color'] . ';';
			}

			if ( is_numeric( $ult_info_design01['subheading_font_size'] ) ) {
				$ult_info_design01['subheading_font_size'] = 'desktop:' . $ult_info_design01['subheading_font_size'] . 'px;';
			}

			if ( is_numeric( $ult_info_design01['subheading_line_height'] ) ) {
				$ult_info_design01['subheading_line_height'] = 'desktop:' . $ult_info_design01['subheading_line_height'] . 'px;';
			}

			$info_table_sub_head_args      = array(
				'target'      => '#' . $info_table_id . ' ' . $ult_info_design01['sub_heading_tag'], // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_info_design01['subheading_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_info_design01['subheading_line_height'],
				),
			);
			$info_table_sub_head_data_list = get_ultimate_vc_responsive_media_css( $info_table_sub_head_args );

			// features.
			if ( '' != $ult_info_design01['features_font_family'] ) {
				$featuresfont_family = get_ultimate_font_family( $ult_info_design01['features_font_family'] );
				if ( '' !== $featuresfont_family ) {
					$features_inline .= 'font-family:\'' . $featuresfont_family . '\';';
				}
			}

			$features_inline .= get_ultimate_font_style( $ult_info_design01['features_font_style'] );

			if ( '' != $ult_info_design01['features_font_color'] ) {
				$features_inline .= 'color:' . $ult_info_design01['features_font_color'] . ';';
			}

			if ( is_numeric( $ult_info_design01['features_font_size'] ) ) {
				$ult_info_design01['features_font_size'] = 'desktop:' . $ult_info_design01['features_font_size'] . 'px;';
			}

			if ( is_numeric( $ult_info_design01['features_line_height'] ) ) {
				$ult_info_design01['features_line_height'] = 'desktop:' . $ult_info_design01['features_line_height'] . 'px;';
			}

			$info_table_features_id = 'info_table_features_wrap-' . wp_rand( 1000, 9999 );

			$info_table_features_args      = array(
				'target'      => '#' . $info_table_features_id . '.ult_price_features', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_info_design01['features_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_info_design01['features_line_height'],
				),
			);
			$info_table_features_data_list = get_ultimate_vc_responsive_media_css( $info_table_features_args );

			// button.
			if ( '' != $ult_info_design01['button_font_family'] ) {
				$buttonfont_family = get_ultimate_font_family( $ult_info_design01['button_font_family'] );
				if ( '' !== $buttonfont_family ) {
					$button_inline .= 'font-family:\'' . $buttonfont_family . '\';';
				}
			}

			$button_inline .= get_ultimate_font_style( $ult_info_design01['button_font_style'] );

			if ( '' != $ult_info_design01['button_font_color'] ) {
				$button_inline .= 'color:' . $ult_info_design01['button_font_color'] . ';';
			}

			if ( is_numeric( $ult_info_design01['button_font_size'] ) ) {
				$ult_info_design01['button_font_size'] = 'desktop:' . $ult_info_design01['button_font_size'] . 'px;';
			}

			if ( is_numeric( $ult_info_design01['button_line_height'] ) ) {
				$ult_info_design01['button_line_height'] = 'desktop:' . $ult_info_design01['button_line_height'] . 'px;';
			}

			$info_table_btn_id = 'info_table_btn_wrap-' . wp_rand( 1000, 9999 );

			$info_table_btn_args      = array(
				'target'      => '#' . $info_table_btn_id . ' .ult_price_action_button', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_info_design01['button_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_info_design01['button_line_height'],
				),
			);
			$info_table_btn_data_list = get_ultimate_vc_responsive_media_css( $info_table_btn_args );

			$output .= '<div class="ult_pricing_table_wrap ult_info_table ult_design_1 ' . esc_attr( $featured ) . ' ult-cs-' . esc_attr( $ult_info_design01['color_scheme'] ) . ' ' . esc_attr( $ult_info_design01['el_class'] ) . '' . esc_attr( $ult_info_design01['css_info_tables'] ) . '">
					<div class="ult_pricing_table ' . esc_attr( $info_tab_ht ) . '" style="' . esc_attr( $featured_style ) . ' ' . esc_attr( $info_tab_ht_style ) . '">';
			$output .= '<div class="ult_pricing_heading" id="' . esc_attr( $info_table_id ) . '">
							<' . esc_attr( $ult_info_design01['heading_tag'] ) . ' class="ult-responsive" ' . $info_table_data_list . ' style="' . esc_attr( $heading_style_inline ) . '">' . $ult_info_design01['package_heading'] . '</' . esc_attr( $ult_info_design01['heading_tag'] ) . '>';
			if ( '' !== $ult_info_design01['package_sub_heading'] ) {
				$output .= '<' . esc_attr( $ult_info_design01['sub_heading_tag'] ) . ' class="ult-responsive" ' . $info_table_sub_head_data_list . 'style="' . esc_attr( $sub_heading_inline ) . '">' . $ult_info_design01['package_sub_heading'] . '</' . esc_attr( $ult_info_design01['sub_heading_tag'] ) . '>';
			}
			$output .= '</div><!--ult_pricing_heading-->';
			$output .= '<div class="ult_price_body_block">
							<div class="ult_price_body">
								<div class="ult_price">
								' . $box_icon . '
								</div>
							</div>
						</div><!--ult_price_body_block-->';
			$output .= '<div id="' . esc_attr( $info_table_features_id ) . '" ' . $info_table_features_data_list . ' class="ult-responsive ult_price_features" style="' . esc_attr( $features_inline ) . '">
							' . wpb_js_remove_wpautop( do_shortcode( $content ), true ) . '
						</div><!--ult_price_features-->';
			if ( 'true' == $ult_info_design01['use_cta_btn'] ) {
				$output .= '<div id="' . esc_attr( $info_table_btn_id ) . '" class="ult_price_link" style="' . esc_attr( $normal_style ) . '">
							<a ' . Ultimate_VC_Addons::uavc_link_init( $link, $target, $link_title, $rel ) . ' ' . $info_table_btn_data_list . ' class="ult-responsive ult_price_action_button" style="' . esc_attr( $featured_style ) . ' ' . esc_attr( $button_inline ) . '">' . $ult_info_design01['package_btn_text'] . '</a>
						</div><!--ult_price_link-->';
			}
			$output .= '<div class="ult_clr"></div>
			</div><!--pricing_table-->
		</div><!--pricing_table_wrap-->';
			if ( 'box' == $ult_info_design01['use_cta_btn'] ) {
				$output .= '</a>';
			}
			return $output;
	}
}

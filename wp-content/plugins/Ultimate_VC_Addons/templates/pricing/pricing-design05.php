<?php
/**
 * Add-on Name: Stats Counter for WPBakery Page Builder
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package UAVC Design layout 03
 */

if ( ! function_exists( 'ult_price_generate_design05' ) ) {
	/**
	 * For the design shortcode.
	 *
	 * @since ----
	 * @param array  $atts represts module attribuits.
	 * @param string $content value has been set to null.
	 * @access public
	 */
	function ult_price_generate_design05( $atts, $content = null ) {
		$target                   = '';
		$link_title               = '';
		$rel                      = '';
			$ult_pricing_design05 = shortcode_atts(
				array(
					'color_scheme'             => 'black',
					'package_heading'          => '',
					'heading_tag'              => 'h3',
					'package_sub_heading'      => '',
					'sub_heading_tag'          => 'h5',
					'package_price'            => '',
					'package_unit'             => '',
					'package_btn_text'         => '',
					'package_link'             => '',
					'package_featured'         => '',
					'color_bg_main'            => '',
					'color_txt_main'           => '',
					'color_bg_highlight'       => '',
					'color_txt_highlight'      => '',
					'package_name_font_family' => '',
					'package_name_font_style'  => '',
					'package_name_font_size'   => '',
					'package_name_font_color'  => '',
					'package_name_line_height' => '',
					'subheading_font_family'   => '',
					'subheading_font_style'    => '',
					'subheading_font_size'     => '',
					'subheading_font_color'    => '',
					'subheading_line_height'   => '',
					'price_font_family'        => '',
					'price_font_style'         => '',
					'price_font_size'          => '',
					'price_font_color'         => '',
					'price_line_height'        => '',
					'price_unit_font_family'   => '',
					'price_unit_font_style'    => '',
					'price_unit_font_size'     => '',
					'price_unit_font_color'    => '',
					'price_unit_line_height'   => '',
					'features_font_family'     => '',
					'features_font_style'      => '',
					'features_font_size'       => '',
					'features_font_color'      => '',
					'features_line_height'     => '',
					'button_font_family'       => '',
					'button_font_style'        => '',
					'button_font_size'         => '',
					'button_font_color'        => '',
					'button_line_height'       => '',
					'el_class'                 => '',
					'min_ht'                   => '',
					'css_price_box'            => '',

				),
				$atts
			);
		$ult_pricing_design05['css_price_box'] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_pricing_design05['css_price_box'], ' ' ), 'ultimate_pricing', $atts );
		$ult_pricing_design05['css_price_box'] = esc_attr( $ult_pricing_design05['css_price_box'] );
		$output                                = '';
		$link                                  = '';
		$target                                = '';
		$featured                              = '';
		$featured_style                        = '';
		$normal_style                          = '';
		$dynamic_style                         = '';
		if ( 'custom' == $ult_pricing_design05['color_scheme'] ) {
			if ( '' !== $ult_pricing_design05['color_bg_main'] ) {
				$normal_style .= 'background:' . $ult_pricing_design05['color_bg_main'] . ';';
			}
			if ( '' !== $ult_pricing_design05['color_txt_main'] ) {
				$normal_style .= 'color:' . $ult_pricing_design05['color_txt_main'] . ';';
			}
			if ( '' !== $ult_pricing_design05['color_bg_highlight'] ) {
				$featured_style .= 'background:' . $ult_pricing_design05['color_bg_highlight'] . ';';
			}
			if ( '' !== $ult_pricing_design05['color_txt_highlight'] ) {
				$featured_style .= 'color:' . $ult_pricing_design05['color_txt_highlight'] . ';';
			}
		}
		if ( '' !== $ult_pricing_design05['package_link'] ) {
			$href = vc_build_link( $ult_pricing_design05['package_link'] );

			$link       = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
			$target     = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
			$link_title = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
			$rel        = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
		} else {
			$link = '#';
		}
		if ( '' !== $ult_pricing_design05['package_featured'] ) {
			$featured = 'ult_featured';
		}

		/* Typography */

		$package_name_inline = '';
		$sub_heading_inline  = '';
		$price_inline        = '';
		$price_unit_inline   = '';
		$features_inline     = '';
		$button_inline       = '';

		// package name/title.
		if ( '' != $ult_pricing_design05['package_name_font_family'] ) {
			$pkgfont_family = get_ultimate_font_family( $ult_pricing_design05['package_name_font_family'] );
			if ( '' !== $pkgfont_family ) {
				$package_name_inline .= 'font-family:\'' . $pkgfont_family . '\';';
			}
		}

		$package_name_inline .= get_ultimate_font_style( $ult_pricing_design05['package_name_font_style'] );

		if ( '' != $ult_pricing_design05['package_name_font_color'] ) {
			$package_name_inline .= 'color:' . $ult_pricing_design05['package_name_font_color'] . ';';
		}

		if ( 'span' == $ult_pricing_design05['sub_heading_tag'] ) {
			$sub_heading_inline .= 'display:block;';
		}

		if ( is_numeric( $ult_pricing_design05['package_name_font_size'] ) ) {
			$ult_pricing_design05['package_name_font_size'] = 'desktop:' . $ult_pricing_design05['package_name_font_size'] . 'px;';
		}

		if ( is_numeric( $ult_pricing_design05['package_name_line_height'] ) ) {
			$ult_pricing_design05['package_name_line_height'] = 'desktop:' . $ult_pricing_design05['package_name_line_height'] . 'px;';
		}

		$price_table_id = 'price-table-wrap-' . wp_rand( 1000, 9999 );

		$price_table_args = array(
			'target'      => '#' . $price_table_id . ' .cust-headformat', // set targeted element e.g. unique class/id etc.
			'media_sizes' => array(
				'font-size'   => $ult_pricing_design05['package_name_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
				'line-height' => $ult_pricing_design05['package_name_line_height'],
			),
		);

		$price_table_data_list = get_ultimate_vc_responsive_media_css( $price_table_args );

		// sub heading.
		if ( '' != $ult_pricing_design05['subheading_font_family'] ) {
			$shfont_family = get_ultimate_font_family( $ult_pricing_design05['subheading_font_family'] );
			if ( '' !== $shfont_family ) {
				$sub_heading_inline .= 'font-family:\'' . $shfont_family . '\';';
			}
		}

		$sub_heading_inline .= get_ultimate_font_style( $ult_pricing_design05['subheading_font_style'] );

		if ( '' != $ult_pricing_design05['subheading_font_color'] ) {
			$sub_heading_inline .= 'color:' . $ult_pricing_design05['subheading_font_color'] . ';';
		}

		if ( is_numeric( $ult_pricing_design05['subheading_font_size'] ) ) {
			$ult_pricing_design05['subheading_font_size'] = 'desktop:' . $ult_pricing_design05['subheading_font_size'] . 'px;';
		}

		if ( is_numeric( $ult_pricing_design05['subheading_line_height'] ) ) {
			$ult_pricing_design05['subheading_line_height'] = 'desktop:' . $ult_pricing_design05['subheading_line_height'] . 'px;';
		}

		$price_table_subhead_args = array(
			'target'      => '#' . $price_table_id . ' .cust-subhead', // set targeted element e.g. unique class/id etc.
			'media_sizes' => array(
				'font-size'   => $ult_pricing_design05['subheading_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
				'line-height' => $ult_pricing_design05['subheading_line_height'],
			),
		);

		$price_table_subhead_data_list = get_ultimate_vc_responsive_media_css( $price_table_subhead_args );

		// price.
		if ( '' != $ult_pricing_design05['price_font_family'] ) {
			$pricefont_family = get_ultimate_font_family( $ult_pricing_design05['price_font_family'] );
			if ( '' !== $pricefont_family ) {
				$price_inline .= 'font-family:\'' . $pricefont_family . '\';';
			}
		}

		$price_inline .= get_ultimate_font_style( $ult_pricing_design05['price_font_style'] );

		if ( '' != $ult_pricing_design05['price_font_color'] ) {
			$price_inline .= 'color:' . $ult_pricing_design05['price_font_color'] . ';';
		}

		// responsive param.

		if ( is_numeric( $ult_pricing_design05['price_font_size'] ) ) {
			$ult_pricing_design05['price_font_size'] = 'desktop:' . $ult_pricing_design05['price_font_size'] . 'px;';
		}

		if ( is_numeric( $ult_pricing_design05['price_line_height'] ) ) {
			$ult_pricing_design05['price_line_height'] = 'desktop:' . $ult_pricing_design05['price_line_height'] . 'px;';
		}

		$price_table_price_id = 'price-table-wrap-' . wp_rand( 1000, 9999 );

		$price_table_price_args = array(
			'target'      => '#' . $price_table_price_id . ' .ult_price_figure', // set targeted element e.g. unique class/id etc.
			'media_sizes' => array(
				'font-size'   => $ult_pricing_design05['price_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
				'line-height' => $ult_pricing_design05['price_line_height'],
			),
		);

		$price_table_price_data_list = get_ultimate_vc_responsive_media_css( $price_table_price_args );

		// price unit.
		if ( '' != $ult_pricing_design05['price_unit_font_family'] ) {
			$price_unitfont_family = get_ultimate_font_family( $ult_pricing_design05['price_unit_font_family'] );
			if ( '' !== $price_unitfont_family ) {
				$price_unit_inline .= 'font-family:\'' . $price_unitfont_family . '\';';
			}
		}

		$price_unit_inline .= get_ultimate_font_style( $ult_pricing_design05['price_unit_font_style'] );

		if ( '' != $ult_pricing_design05['price_unit_font_color'] ) {
			$price_unit_inline .= 'color:' . $ult_pricing_design05['price_unit_font_color'] . ';';
		}

		// responsive param.

		if ( is_numeric( $ult_pricing_design05['price_unit_font_size'] ) ) {
			$ult_pricing_design05['price_unit_font_size'] = 'desktop:' . $ult_pricing_design05['price_unit_font_size'] . 'px;';
		}

		if ( is_numeric( $ult_pricing_design05['price_unit_line_height'] ) ) {
			$ult_pricing_design05['price_unit_line_height'] = 'desktop:' . $ult_pricing_design05['price_unit_line_height'] . 'px;';
		}

		$price_table_price_unit_args = array(
			'target'      => '#' . $price_table_price_id . ' .ult_price_term', // set targeted element e.g. unique class/id etc.
			'media_sizes' => array(
				'font-size'   => $ult_pricing_design05['price_unit_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
				'line-height' => $ult_pricing_design05['price_unit_line_height'],
			),
		);

		$price_table_price_unit_data_list = get_ultimate_vc_responsive_media_css( $price_table_price_unit_args );

		// features.
		if ( '' != $ult_pricing_design05['features_font_family'] ) {
			$featuresfont_family = get_ultimate_font_family( $ult_pricing_design05['features_font_family'] );
			if ( '' !== $featuresfont_family ) {
				$features_inline .= 'font-family:\'' . $featuresfont_family . '\';';
			}
		}

		$features_inline .= get_ultimate_font_style( $ult_pricing_design05['features_font_style'] );

		if ( '' != $ult_pricing_design05['features_font_color'] ) {
			$features_inline .= 'color:' . $ult_pricing_design05['features_font_color'] . ';';
		}

		// responsive param.

		if ( is_numeric( $ult_pricing_design05['features_font_size'] ) ) {
			$ult_pricing_design05['features_font_size'] = 'desktop:' . $ult_pricing_design05['features_font_size'] . 'px;';
		}

		if ( is_numeric( $ult_pricing_design05['features_line_height'] ) ) {
			$ult_pricing_design05['features_line_height'] = 'desktop:' . $ult_pricing_design05['features_line_height'] . 'px;';
		}
		$price_table_features_id   = 'price-table-features-wrap-' . wp_rand( 1000, 9999 );
		$price_table_features_args = array(
			'target'      => '#' . $price_table_features_id . '', // set targeted element e.g. unique class/id etc.
			'media_sizes' => array(
				'font-size'   => $ult_pricing_design05['features_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
				'line-height' => $ult_pricing_design05['features_line_height'],
			),
		);

		$price_table_features_data_list = get_ultimate_vc_responsive_media_css( $price_table_features_args );

		/*-- min height-------*/
		$ult_price_table_ht = '';
		if ( '' != $ult_pricing_design05['min_ht'] ) {
			$ult_price_table_ht .= 'ult_price_table_ht';
			$normal_style       .= 'min-height:' . $ult_pricing_design05['min_ht'] . 'px;';
		}

		// button.
		if ( '' != $ult_pricing_design05['button_font_family'] ) {
			$buttonfont_family = get_ultimate_font_family( $ult_pricing_design05['button_font_family'] );
			if ( '' !== $buttonfont_family ) {
				$button_inline .= 'font-family:\'' . $buttonfont_family . '\';';
			}
		}

		$button_inline .= get_ultimate_font_style( $ult_pricing_design05['button_font_style'] );

		if ( '' != $ult_pricing_design05['button_font_color'] ) {
			$button_inline .= 'color:' . $ult_pricing_design05['button_font_color'] . ';';
		}

		// responsive param.

		if ( is_numeric( $ult_pricing_design05['button_font_size'] ) ) {
			$ult_pricing_design05['button_font_size'] = 'desktop:' . $ult_pricing_design05['button_font_size'] . 'px;';
		}

		if ( is_numeric( $ult_pricing_design05['button_line_height'] ) ) {
			$ult_pricing_design05['button_line_height'] = 'desktop:' . $ult_pricing_design05['button_line_height'] . 'px;';
		}
		$price_table_button_id   = 'price-table-button-wrap-' . wp_rand( 1000, 9999 );
		$price_table_button_args = array(
			'target'      => '#' . $price_table_button_id . ' .ult_price_action_button', // set targeted element e.g. unique class/id etc.
			'media_sizes' => array(
				'font-size'   => $ult_pricing_design05['button_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
				'line-height' => $ult_pricing_design05['button_line_height'],
			),
		);

		$price_table_button_data_list = get_ultimate_vc_responsive_media_css( $price_table_button_args );

		/* End Typography */

		$output     .= '<div class="ult_pricing_table_wrap ult_design_5 ' . esc_attr( $featured ) . ' ult-cs-' . esc_attr( $ult_pricing_design05['color_scheme'] ) . ' ' . esc_attr( $ult_pricing_design05['el_class'] ) . ' ' . esc_attr( $ult_pricing_design05['css_price_box'] ) . '">
					<div class="ult_pricing_table ' . esc_attr( $ult_price_table_ht ) . '" style="' . esc_attr( $normal_style ) . '">';
			$output .= '<div id="' . esc_attr( $price_table_id ) . '" class="ult_pricing_heading" style="' . esc_attr( $featured_style ) . '">
							<' . $ult_pricing_design05['heading_tag'] . ' class="price-heading ult-responsive cust-headformat" ' . $price_table_data_list . ' style="' . esc_attr( $package_name_inline ) . '">' . $ult_pricing_design05['package_heading'] . '</' . $ult_pricing_design05['heading_tag'] . '>';
		if ( '' !== $ult_pricing_design05['package_sub_heading'] ) {
			$output .= '<' . $ult_pricing_design05['sub_heading_tag'] . ' ' . $price_table_subhead_data_list . ' class="price-subheading ult-responsive cust-subhead" style="' . esc_attr( $sub_heading_inline ) . '">' . $ult_pricing_design05['package_sub_heading'] . '</' . $ult_pricing_design05['sub_heading_tag'] . '>';
		}
			$output .= '</div><!--ult_pricing_heading-->';
			$output .= '<div class="ult_price_body_block" style="' . $featured_style . '">
							<div class="ult_price_body">
								<div id="' . esc_attr( $price_table_price_id ) . '"  class="ult_price">
									<span ' . $price_table_price_data_list . ' class="ult_price_figure ult-responsive" style="' . esc_attr( $price_inline ) . '">' . esc_html( $ult_pricing_design05['package_price'] ) . '</span>
									<span ' . $price_table_price_unit_data_list . ' class="ult_price_term ult-responsive" style="' . esc_attr( $price_unit_inline ) . '">' . esc_html( $ult_pricing_design05['package_unit'] ) . '</span>
								</div>
							</div>
						</div><!--ult_price_body_block-->';
			$output .= '<div id="' . esc_attr( $price_table_features_id ) . '" class="ult_price_features ult-responsive" ' . $price_table_features_data_list . ' style="' . esc_attr( $features_inline ) . '">
							' . wpb_js_remove_wpautop( do_shortcode( $content ), true ) . '
						</div><!--ult_price_features-->';
		if ( '' !== $ult_pricing_design05['package_btn_text'] ) {
			$output .= '<div id="' . esc_attr( $price_table_button_id ) . '" class="ult_price_link">
							<a ' . $price_table_button_data_list . ' ' . Ultimate_VC_Addons::uavc_link_init( $link, $target, $link_title, $rel ) . ' class="ult_price_action_button ult-responsive" style="' . esc_attr( $featured_style ) . ' ' . esc_attr( $button_inline ) . '">' . $ult_pricing_design05['package_btn_text'] . '</a>
						</div><!--ult_price_link-->';
		}
			$output .= '<div class="ult_clr"></div>
			</div><!--pricing_table-->
		</div><!--pricing_table_wrap-->';
		return $output;
	}
}

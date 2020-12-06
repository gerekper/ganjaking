<?php
/**
 * Add-on Name: Team Module
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package Team Module
 */

if ( ! class_exists( 'Ultimate_Team' ) ) {
	/**
	 * Function that initializes Team Module Module
	 *
	 * @class Ultimate_Team
	 */
	class Ultimate_Team {
		/**
		 * Constructor function that constructs default values for the Team Module module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'init_team' ) );
			}
			add_shortcode( 'ult_team', array( $this, 'ult_team_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_team_assets' ), 1 );
		}
		/**
		 * Function that register styles and scripts for Team Module Module.
		 *
		 * @method register_team_assets
		 */
		public function register_team_assets() {
			Ultimate_VC_Addons::ultimate_register_script( 'ultimate-team', 'teams', false, array( 'jquery' ), ULTIMATE_VERSION, false );

			Ultimate_VC_Addons::ultimate_register_style( 'ultimate-team', 'teams' );
		}
		/**
		 * Function that register styles and scripts for Team Module Module.
		 *
		 * @param array $hook has the array of files.
		 * @method admin_scripts
		 */
		public function admin_scripts( $hook ) {

			if ( 'post.php' == $hook || 'post-new.php' == $hook || 'visual-composer_page_vc-roles' == $hook ) {
				$bsf_dev_mode = bsf_get_option( 'dev_mode' );
				if ( 'enable' === $bsf_dev_mode ) {
					wp_enqueue_script( 'ult-team-admin', UAVC_URL . 'admin/js/team-admin.js', array( 'jquery' ), ULTIMATE_VERSION, true );
				}
			}
		}
		/**
		 * Render function for Team Module Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been not set to null.
		 * @access public
		 */
		public function ult_team_shortcode( $atts, $content ) {

			/* Declaration for style-1 for team */
			$selected_team_icon      = '';
			$social_icon_url         = '';
			$social_link_title       = '';
			$social_icon_color       = '';
			$social_icon_hover_color = '';
			$team_member_bg_color    = '';

			$title_box_margin = '';
			$target           = '';
			$link_title       = '';
			$rel              = '';

			/* Declaration closed for style-1 for team */
				$ult_team_settings = shortcode_atts(
					array(
						'image'                            => '',
						'name'                             => '',
						'pos_in_org'                       => '',
						'text_align'                       => '',
						'team_member_name_tag'             => '',
						'team_member_name_font'            => '',
						'team_member_name_font_style'      => '',
						'team_member_name_font_size'       => '',
						'team_member_name_line_height'     => '',
						'team_member_position_font'        => '',
						'team_member_position_font_style'  => '',
						'team_member_position_font_size'   => '',
						'team_member_position_line_height' => '',
						'team_member_description_font'     => '',
						'team_member_description_font_style' => '',
						'team_member_description_font_size' => '',
						'team_member_description_line_height' => '',
						'team_member_name_color'           => '',
						'team_member_org_color'            => '',
						'team_member_desc_color'           => '',
						'img_hover_eft'                    => '',
						'img_hover_color'                  => '',
						'img_border_style'                 => '',
						'img_border_width'                 => '',
						'img_border_radius'                => '',
						'img_border_color'                 => '',
						'staff_link'                       => '',
						'link_switch'                      => '',

						// New attributes for style 2.
						'team_member_style'                => '',
						'divider_effect'                   => '',
						'team_member_align_style'          => '',
						'team_member_divider_color'        => '',
						'team_member_divider_width'        => '',
						'team_member_divider_height'       => '',
						'social_icon_effect'               => '',
						'social_links'                     => '',
						'social_icon_size'                 => '',
						'social_icon_space'                => '',

						'title_box_padding'                => '',
						'custom_team_class'                => '',

						'team_css'                         => '',
						'team_member_responsive_enable'    => '',
						'team_responsive_width'            => '',

						'team_img_opacity'                 => '',
						'team_img_hover_opacity'           => '',
						'team_img_hover_opacity_style3'    => '',
						'team_img_bg_color'                => '',

						'team_img_grayscale'               => 'on',

					),
					$atts
				);

			// Grayscale Image.
			$team_img_grayscale_cls = ( 'off' != $ult_team_settings['team_img_grayscale'] ) ? 'ult-team-grayscale' : '';

			// Style-2 Image Opacity.
			$ult_team_settings['team_img_opacity']              = ( isset( $ult_team_settings['team_img_opacity'] ) && trim( $ult_team_settings['team_img_opacity'] ) !== '' ) ? $ult_team_settings['team_img_opacity'] : '1';
			$ult_team_settings['team_img_hover_opacity']        = ( isset( $ult_team_settings['team_img_hover_opacity'] ) && trim( $ult_team_settings['team_img_hover_opacity'] ) !== '' ) ? $ult_team_settings['team_img_hover_opacity'] : '0.65';
			$ult_team_settings['team_img_hover_opacity_style3'] = ( isset( $ult_team_settings['team_img_hover_opacity_style3'] ) && trim( $ult_team_settings['team_img_hover_opacity_style3'] ) !== '' ) ? $ult_team_settings['team_img_hover_opacity_style3'] : '0.1';

			$ult_team_settings['team_img_bg_color'] = ( isset( $ult_team_settings['team_img_bg_color'] ) && trim( $ult_team_settings['team_img_bg_color'] ) !== '' ) ? $ult_team_settings['team_img_bg_color'] : 'inherit';

			$ult_team_settings['team_member_style'] = ( isset( $ult_team_settings['team_member_style'] ) && trim( $ult_team_settings['team_member_style'] ) !== '' ) ? $ult_team_settings['team_member_style'] : 'style-1';
			$ult_team_settings['custom_team_class'] = ( isset( $ult_team_settings['custom_team_class'] ) && trim( $ult_team_settings['custom_team_class'] ) !== '' ) ? $ult_team_settings['custom_team_class'] : '';

			// Set responsive width.
			$ult_team_settings['team_responsive_width'] = ( isset( $ult_team_settings['team_responsive_width'] ) && trim( $ult_team_settings['team_responsive_width'] ) !== '' ) ? $ult_team_settings['team_responsive_width'] : '';
			$ult_team_settings['team_responsive_width'] = ( isset( $ult_team_settings['team_member_responsive_enable'] ) && trim( $ult_team_settings['team_member_responsive_enable'] ) == 'on' ) ? $ult_team_settings['team_responsive_width'] : '';

			// Set typography colors.
			$ult_team_settings['team_member_name_color'] = ( isset( $ult_team_settings['team_member_name_color'] ) && trim( $ult_team_settings['team_member_name_color'] ) !== '' ) ? $ult_team_settings['team_member_name_color'] : 'inherit';
			$ult_team_settings['team_member_org_color']  = ( isset( $ult_team_settings['team_member_org_color'] ) && trim( $ult_team_settings['team_member_org_color'] ) !== '' ) ? $ult_team_settings['team_member_org_color'] : 'inherit';
			$ult_team_settings['team_member_desc_color'] = ( isset( $ult_team_settings['team_member_desc_color'] ) && trim( $ult_team_settings['team_member_desc_color'] ) !== '' ) ? $ult_team_settings['team_member_desc_color'] : 'inherit';

			// Set team Member name's tag element.
			$ult_team_settings['team_member_name_tag'] = ( isset( $ult_team_settings['team_member_name_tag'] ) && trim( $ult_team_settings['team_member_name_tag'] ) !== '' ) ? $ult_team_settings['team_member_name_tag'] : 'h2';

			$ult_team_settings['team_css'] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_team_settings['team_css'], ' ' ), 'ult_team', $atts );

			// title box style.
			$title_box_style  = '';
			$title_box_style .= trim( $ult_team_settings['title_box_padding'] ) != '' ? $ult_team_settings['title_box_padding'] : '';

			$href = vc_build_link( $ult_team_settings['staff_link'] );

			$url        = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
			$target     = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
			$link_title = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
			$rel        = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';

			$font_args                     = array();
			$team_member_name_font_styling = '';
			if ( '' == ! $ult_team_settings['team_member_name_font'] ) {
				$team_member_font_family        = function_exists( 'get_ultimate_font_family' ) ? get_ultimate_font_family( $ult_team_settings['team_member_name_font'] ) : '';
				$team_member_font_family        = ( '' != $team_member_font_family ) ? $team_member_font_family : 'inherit';
				$team_member_name_font_styling .= 'font-family:' . $team_member_font_family . ';';
				array_push( $font_args, $ult_team_settings['team_member_name_font'] );
			}

			if ( function_exists( 'get_ultimate_font_style' ) ) {
				if ( isset( $ult_team_settings['team_member_name_font_style'] ) && trim( $ult_team_settings['team_member_name_font_style'] ) != '' ) {
					$team_member_name_font_styling .= get_ultimate_font_style( $ult_team_settings['team_member_name_font_style'] );
				}
			}

			if ( ! ( '' == $ult_team_settings['team_member_name_color'] ) && ! ( 'inherit' == $ult_team_settings['team_member_name_color'] ) ) {

				$team_member_name_font_styling .= 'color:' . $ult_team_settings['team_member_name_color'] . ';';
			}
			$team_member_position_font_styling = '';
			if ( '' == ! $ult_team_settings['team_member_position_font'] ) {
				$team_member_font_family            = function_exists( 'get_ultimate_font_family' ) ? get_ultimate_font_family( $ult_team_settings['team_member_position_font'] ) : '';
				$team_member_font_family            = ( '' != $team_member_font_family ) ? $team_member_font_family : 'inherit';
				$team_member_position_font_styling .= 'font-family:' . $team_member_font_family . ';';
				array_push( $font_args, $ult_team_settings['team_member_position_font'] );
			}
			if ( '' == ! $ult_team_settings['team_member_position_font_style'] ) {
				$team_member_position_font_styling .= $ult_team_settings['team_member_position_font_style'] . ';';
			}

			if ( ! ( '' == $ult_team_settings['team_member_org_color'] ) && ! ( 'inherit' == $ult_team_settings['team_member_org_color'] ) ) {
				$team_member_position_font_styling .= 'color:' . $ult_team_settings['team_member_org_color'] . ';';
			}
			$team_member_description_font_styling = '';
			if ( '' == ! $ult_team_settings['team_member_description_font'] ) {
				$team_member_font_family               = function_exists( 'get_ultimate_font_family' ) ? get_ultimate_font_family( $ult_team_settings['team_member_description_font'] ) : '';
				$team_member_font_family               = ( '' != $team_member_font_family ) ? $team_member_font_family : 'inherit';
				$team_member_description_font_styling .= 'font-family:' . $team_member_font_family . ';';
				array_push( $font_args, $ult_team_settings['team_member_description_font'] );
			}
			if ( '' == ! $ult_team_settings['team_member_description_font_style'] ) {
				$team_member_description_font_styling .= $ult_team_settings['team_member_description_font_style'] . ';';
			}

			if ( ! ( '' == $ult_team_settings['team_member_desc_color'] ) && ! ( 'inherit' == $ult_team_settings['team_member_desc_color'] ) ) {

				$team_member_description_font_styling .= 'color:' . $ult_team_settings['team_member_desc_color'] . ';';
			}
			$img_hver_class = '';
			$img_hver_data  = '';
			if ( 'on' == $ult_team_settings['img_hover_eft'] ) {
				$img_hver_class                       = 'ult-team_img_hover';
				$ult_team_settings['img_hover_color'] = ( isset( $ult_team_settings['img_hover_color'] ) && trim( $ult_team_settings['img_hover_color'] ) != '' ) ? $ult_team_settings['img_hover_color'] : 'rgba(100,100,100,0.6)';
				$img_hver_data                        = 'data-background_clr = "' . esc_attr( $ult_team_settings['img_hover_color'] ) . '"';
			} elseif ( 'off' == $ult_team_settings['img_hover_eft'] ) {
				$img_hver_class = '';
				$img_hver_data  = '';
			}
			$team_image_style = '';
			if ( '' == ! $ult_team_settings['img_border_style'] ) {
				$team_image_style .= 'border-style:' . $ult_team_settings['img_border_style'] . ';';
			}
			if ( '' == ! $ult_team_settings['img_border_width'] ) {
				$team_image_style .= 'border-width:' . $ult_team_settings['img_border_width'] . 'px;';
			}
			if ( '' == ! $ult_team_settings['img_border_radius'] ) {
				$team_image_style .= 'border-radius:' . $ult_team_settings['img_border_radius'] . 'px;';
			}
			if ( '' == ! $ult_team_settings['img_border_color'] ) {
				$team_image_style .= 'border-color:' . $ult_team_settings['img_border_color'] . ';';
			}

			$img = apply_filters( 'ult_get_img_single', $ult_team_settings['image'], 'url' );
			$alt = apply_filters( 'ult_get_img_single', $ult_team_settings['image'], 'alt' );

			// Code for Responsive font-size [Open].
			$id = uniqid( 'ultimate-heading' );
			// FIX: set old font size before implementing responsive param.
			if ( is_numeric( $ult_team_settings['team_member_name_font_size'] ) ) {
				$ult_team_settings['team_member_name_font_size'] = 'desktop:' . $ult_team_settings['team_member_name_font_size'] . 'px;';     }
			if ( is_numeric( $ult_team_settings['team_member_name_line_height'] ) ) {
				$ult_team_settings['team_member_name_line_height'] = 'desktop:' . $ult_team_settings['team_member_name_line_height'] . 'px;';     }
			$team_name_args              = array(
				'target'      => '.ult-team-member-bio-wrap.' . $id . ' .ult-team-member-name',
				'media_sizes' => array(
					'font-size'   => $ult_team_settings['team_member_name_font_size'],
					'line-height' => $ult_team_settings['team_member_name_line_height'],
				),
			);
			$team_member_name_responsive = get_ultimate_vc_responsive_media_css( $team_name_args );

			if ( is_numeric( $ult_team_settings['team_member_position_font_size'] ) ) {
				$ult_team_settings['team_member_position_font_size'] = 'desktop:' . $ult_team_settings['team_member_position_font_size'] . 'px;';     }
			if ( is_numeric( $ult_team_settings['team_member_position_line_height'] ) ) {
				$ult_team_settings['team_member_position_line_height'] = 'desktop:' . $ult_team_settings['team_member_position_line_height'] . 'px;';     }
			$team_position_args              = array(
				'target'      => '.ult-team-member-bio-wrap.' . $id . ' .ult-team-member-position',
				'media_sizes' => array(
					'font-size'   => $ult_team_settings['team_member_position_font_size'],
					'line-height' => $ult_team_settings['team_member_position_line_height'],
				),
			);
			$team_member_position_responsive = get_ultimate_vc_responsive_media_css( $team_position_args );

			if ( is_numeric( $ult_team_settings['team_member_description_font_size'] ) ) {
				$ult_team_settings['team_member_description_font_size'] = 'desktop:' . $ult_team_settings['team_member_description_font_size'] . 'px;';     }
			if ( is_numeric( $ult_team_settings['team_member_description_line_height'] ) ) {
				$ult_team_settings['team_member_description_line_height'] = 'desktop:' . $ult_team_settings['team_member_description_line_height'] . 'px;';     }
			$team_desc_args              = array(
				'target'      => '.ult-team-member-bio-wrap.' . $id . ' .ult-team-member-description',
				'media_sizes' => array(
					'font-size'   => $ult_team_settings['team_member_description_font_size'],
					'line-height' => $ult_team_settings['team_member_description_line_height'],
				),
			);
			$team_member_desc_responsive = get_ultimate_vc_responsive_media_css( $team_desc_args );

			$ult_team_settings['team_member_divider_color'] = ( isset( $ult_team_settings['team_member_divider_color'] ) && trim( $ult_team_settings['team_member_divider_color'] ) !== '' ) ? $ult_team_settings['team_member_divider_color'] : '';

			$ult_team_settings['team_member_align_style'] = ( isset( $ult_team_settings['team_member_align_style'] ) && trim( $ult_team_settings['team_member_align_style'] ) !== '' ) ? $ult_team_settings['team_member_align_style'] : 'center';

			$ult_team_settings['social_icon_size']  = ( isset( $ult_team_settings['social_icon_size'] ) && trim( $ult_team_settings['social_icon_size'] ) !== '' ) ? $ult_team_settings['social_icon_size'] . 'px' : '16px';
			$ult_team_settings['social_icon_space'] = ( isset( $ult_team_settings['social_icon_space'] ) && trim( $ult_team_settings['social_icon_space'] ) !== '' ) ? ( $ult_team_settings['social_icon_space'] / 2 ) . 'px' : '5px';

			$ult_team_settings['team_member_divider_width']  = ( isset( $ult_team_settings['team_member_divider_width'] ) && trim( $ult_team_settings['team_member_divider_width'] ) !== '' ) ? $ult_team_settings['team_member_divider_width'] : '80';
			$ult_team_settings['team_member_divider_width']  = ( $ult_team_settings['team_member_divider_width'] <= 100 ) ? $ult_team_settings['team_member_divider_width'] : '100';
			$ult_team_settings['team_member_divider_height'] = ( isset( $ult_team_settings['team_member_divider_height'] ) && trim( $ult_team_settings['team_member_divider_height'] ) !== '' ) ? $ult_team_settings['team_member_divider_height'] : '1';
			// Code for Responsive font-size [Closed].

			ob_start();
			if ( 'style-3' == $ult_team_settings['team_member_style'] ) {

				$team_desc_args              = array(
					'target'      => '.ult-team-member-image.' . $id . ' .ult-team-member-description',
					'media_sizes' => array(
						'font-size'   => $ult_team_settings['team_member_description_font_size'],
						'line-height' => $ult_team_settings['team_member_description_line_height'],
					),
				);
				$team_member_desc_responsive = get_ultimate_vc_responsive_media_css( $team_desc_args );

				echo '<div class="ult-team-member-wrap ult-style-3 ' . esc_attr( $ult_team_settings['custom_team_class'] ) . ' ' . esc_attr( $ult_team_settings['team_css'] ) . '">';
				if ( 'on' == $ult_team_settings['link_switch'] && '' != $ult_team_settings['staff_link'] ) {
					$href = vc_build_link( $ult_team_settings['staff_link'] );

					$url    = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
					$target = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
					$title  = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
					$rel    = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';

					echo '<a ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $title, $rel ) . '>'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
				}
					echo '<div class="ult-team-member-image ' . esc_attr( $id ) . '" style="' . esc_attr( $team_image_style ) . '; background-color:' . esc_attr( $ult_team_settings['team_img_bg_color'] ) . '" data-hover_opacity="' . esc_attr( $ult_team_settings['team_img_hover_opacity_style3'] ) . '" > <img class="' . esc_attr( $team_img_grayscale_cls ) . '" src="' . esc_url( apply_filters( 'ultimate_images', $img ) ) . '" alt="' . esc_attr( $alt ) . '" >';
					echo '<span class="ult-team-member-image-overlay ' . esc_attr( $img_hver_class ) . '" ' . esc_attr( $img_hver_data ) . ' ></span>';
				if ( $content ) {
					echo '<div class="ult-team-member-description ult-responsive" ' . $team_member_desc_responsive . ' style="' . esc_attr( $team_member_description_font_styling ) . '; text-align:' . esc_attr( $ult_team_settings['team_member_align_style'] ) . ';' . esc_attr( $title_box_style ) . '; "><p>' . do_shortcode( $content ) . '</p></div>'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				echo '</div>';// ult-team-member-image.

				if ( 'on' == $ult_team_settings['link_switch'] && '' != $ult_team_settings['staff_link'] ) {
					echo '</a>';
				}

				echo '<div class="ult-team-member-bio-wrap ' . esc_attr( $ult_team_settings['team_member_style'] ) . ' ' . esc_attr( $id ) . '" style="text-align:' . esc_attr( $ult_team_settings['team_member_align_style'] ) . ';' . esc_attr( $title_box_style ) . '; ">';

				echo '<div class="ult-team-member-name-wrap">';
				if ( 'on' == $ult_team_settings['link_switch'] && '' != $ult_team_settings['staff_link'] ) {
					$href = vc_build_link( $ult_team_settings['staff_link'] );

					$url    = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
					$target = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
					$title  = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
					$rel    = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';

					echo '<a ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $title, $rel ) . ' style="text-decoration: none;" >'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				echo '<' . esc_attr( $ult_team_settings['team_member_name_tag'] ) . ' class="ult-team-member-name ult-responsive" ' . $team_member_name_responsive . ' style="' . esc_attr( $team_member_name_font_styling ) . '">' . esc_attr( $ult_team_settings['name'] ) . '</' . esc_attr( $ult_team_settings['team_member_name_tag'] ) . '>'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
				if ( 'on' == $ult_team_settings['link_switch'] && '' != $ult_team_settings['staff_link'] ) {
					echo '</a>';
				}
				if ( $ult_team_settings['pos_in_org'] ) {
					echo '<div class="ult-team-member-position ult-responsive" ' . $team_member_position_responsive . ' style="' . esc_attr( $team_member_position_font_styling ) . '">' . esc_attr( $ult_team_settings['pos_in_org'] ) . '</div>'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				echo '<div style="margin-bottom:15px">';

				if ( 'on' == $ult_team_settings['divider_effect'] ) {

					$divider_margin = '';
					if ( 'center' != $ult_team_settings['team_member_align_style'] ) {
						$divider_margin = 'margin-' . $ult_team_settings['team_member_align_style'] . ':0px';
					}

					echo '<hr align="' . esc_attr( $ult_team_settings['team_member_align_style'] ) . '" class="ult-team-divider" style="padding-top: ' . esc_attr( $ult_team_settings['team_member_divider_height'] ) . 'px; width: ' . esc_attr( $ult_team_settings['team_member_divider_width'] ) . '%; background-color: ' . esc_attr( $ult_team_settings['team_member_divider_color'] ) . '; ' . esc_attr( $divider_margin ) . '" />';
				}
				echo '</div>';

				$social_icons = json_decode( urldecode( $ult_team_settings['social_links'] ) );

				if ( $social_icons && count( $social_icons ) > 0 && 'on' == $ult_team_settings['social_icon_effect'] ) {

					$icon_styling = 'font-size:' . esc_attr( $ult_team_settings['social_icon_size'] ) . ' ; margin-left:' . esc_attr( $ult_team_settings['social_icon_space'] ) . ';margin-right:' . esc_attr( $ult_team_settings['social_icon_space'] ) . ';';
					echo "<div class='ult-social-buttons'>";

					foreach ( $social_icons as $social_link ) {

						if ( isset( $social_link->selected_team_icon ) && '' !== $social_link->selected_team_icon ) {

							$social_icon_url         = ( isset( $social_link->social_icon_url ) && '' !== $social_link->social_icon_url ) ? $social_link->social_icon_url : '#';
							$social_link_title       = ( isset( $social_link->social_link_title ) && '' !== $social_link->social_link_title ) ? $social_link->social_link_title : '';
							$social_icon_color       = ( isset( $social_link->social_icon_color ) && '' !== $social_link->social_icon_color ) ? $social_link->social_icon_color : 'inherit';
							$default_icon_color      = ( 'inherit' != $social_icon_color ) ? 'color:' . $social_icon_color . ';' : '';
							$social_icon_hover_color = ( isset( $social_link->social_icon_hover_color ) && '' !== $social_link->social_icon_hover_color ) ? $social_link->social_icon_hover_color : 'inherit';
							echo "<a href='" . esc_url( $social_icon_url ) . "' target='_blank' rel='noopener' title='" . esc_attr( $social_link_title ) . "' class='ult-team ult-social-icon' style='" . esc_attr( $icon_styling ) . ';' . esc_attr( $default_icon_color ) . "'  data-iconcolor='" . esc_attr( $social_icon_color ) . "' data-iconhover='" . esc_attr( $social_icon_hover_color ) . "' ><i class='" . esc_attr( $social_link->selected_team_icon ) . "'></i></a>";
						}
					}

					echo '</div>';
				}
				echo '</div>'; // ult-team-member-name-wrap.

				echo '</div>'; // ult-team-member-bio-wrap.
				echo '</div>'; // ult-team-member-wrap.
			} elseif ( 'style-2' == $ult_team_settings['team_member_style'] ) {
				echo '<div class="ult-team-member-wrap ult-style-2 ' . esc_attr( $ult_team_settings['custom_team_class'] ) . ' ' . esc_attr( $ult_team_settings['team_css'] ) . '" data-responsive_width="' . $ult_team_settings['team_responsive_width'] . '" style="background-color:' . esc_attr( $ult_team_settings['team_img_bg_color'] ) . '">'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
				if ( 'on' == $ult_team_settings['link_switch'] && '' != $ult_team_settings['staff_link'] ) {
					$href = vc_build_link( $ult_team_settings['staff_link'] );

					$url    = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
					$target = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
					$title  = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
					$rel    = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';

					echo '<a ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $title, $rel ) . ' style="text-decoration: none;" >'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				echo '<div class="ult-team-member-image" style="' . esc_attr( $team_image_style ) . '" data-opacity="' . esc_attr( $ult_team_settings['team_img_opacity'] ) . '" data-hover_opacity="' . esc_attr( $ult_team_settings['team_img_hover_opacity'] ) . '" > <img src="' . esc_url( apply_filters( 'ultimate_images', $img ) ) . '" alt="' . esc_attr( $alt ) . '"  style="opacity:' . esc_attr( $ult_team_settings['team_img_opacity'] ) . '">';
				echo '</div>';// ult-team-member-image.

				echo '<div class="ult-team-member-bio-wrap ' . esc_attr( $id ) . '">';
				echo '<div class="ult-team-member-name-wrap"  style="text-align:' . esc_attr( $ult_team_settings['team_member_align_style'] ) . ';' . esc_attr( $title_box_style ) . ' ">';

				echo '<' . esc_attr( $ult_team_settings['team_member_name_tag'] ) . ' class="ult-team-member-name ult-responsive" ' . $team_member_name_responsive . ' style="' . esc_attr( $team_member_name_font_styling ) . '">' . esc_attr( $ult_team_settings['name'] ) . '</' . esc_attr( $ult_team_settings['team_member_name_tag'] ) . '>'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped

				if ( $ult_team_settings['pos_in_org'] ) {
					echo '<div class="ult-team-member-position ult-responsive" ' . $team_member_position_responsive . ' style="' . esc_attr( $team_member_position_font_styling ) . '">' . esc_attr( $ult_team_settings['pos_in_org'] ) . '</div>'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				echo '</div>'; // ult-team-member-name-wrap.

				echo '<div class="ult-team_description_slide"  style="text-align:' . esc_attr( $ult_team_settings['team_member_align_style'] ) . ';' . esc_attr( $title_box_style ) . ' ">';
				if ( $content ) {
					echo '<div class="ult-team-member-description ult-responsive" ' . $team_member_desc_responsive . ' style="' . esc_attr( $team_member_description_font_styling ) . '"><p>' . do_shortcode( $content ) . '</p></div>'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				echo '<div style="margin-bottom:15px">';
				if ( 'on' == $ult_team_settings['divider_effect'] ) {

					$divider_margin = '';
					if ( 'center' != $ult_team_settings['team_member_align_style'] ) {
						$divider_margin = 'margin-' . $ult_team_settings['team_member_align_style'] . ':0px';
					}
					echo '<hr align="' . esc_attr( $ult_team_settings['team_member_align_style'] ) . '" class="ult-team-divider" style="padding-top: ' . esc_attr( $ult_team_settings['team_member_divider_height'] ) . 'px; width: ' . esc_attr( $ult_team_settings['team_member_divider_width'] ) . '%; background-color: ' . esc_attr( $ult_team_settings['team_member_divider_color'] ) . '; ' . esc_attr( $divider_margin ) . '" />';
				}
				echo '</div>';

				$social_icons = json_decode( urldecode( $ult_team_settings['social_links'] ) );
				if ( $social_icons && count( $social_icons ) > 0 && 'on' == $ult_team_settings['social_icon_effect'] ) {

					$icon_styling = ' font-size:' . $ult_team_settings['social_icon_size'] . ' ; margin-left:' . $ult_team_settings['social_icon_space'] . ';margin-right:' . $ult_team_settings['social_icon_space'] . ';';
					echo "<div class='ult-social-buttons'>";

					foreach ( $social_icons as $social_link ) {

						if ( isset( $social_link->selected_team_icon ) && '' !== $social_link->selected_team_icon ) {

							$social_icon_url         = ( isset( $social_link->social_icon_url ) && '' !== $social_link->social_icon_url ) ? $social_link->social_icon_url : '#';
							$social_link_title       = ( isset( $social_link->social_link_title ) && '' !== $social_link->social_link_title ) ? $social_link->social_link_title : '';
							$social_icon_color       = ( isset( $social_link->social_icon_color ) && '' !== $social_link->social_icon_color ) ? $social_link->social_icon_color : 'inherit';
							$default_icon_color      = ( 'inherit' != $social_icon_color ) ? 'color:' . $social_icon_color . ';' : '';
							$social_icon_hover_color = ( isset( $social_link->social_icon_hover_color ) && '' !== $social_link->social_icon_hover_color ) ? $social_link->social_icon_hover_color : 'inherit';
							echo "<a href='" . esc_url( $social_icon_url ) . "' target='_blank' rel='noopener' title='" . esc_attr( $social_link_title ) . "' class='ult-team ult-social-icon' style='" . esc_attr( $icon_styling ) . '; ' . esc_attr( $default_icon_color ) . "'  data-iconcolor='" . esc_attr( $social_icon_color ) . "' data-iconhover='" . esc_attr( $social_icon_hover_color ) . "' ><i class='" . esc_attr( $social_link->selected_team_icon ) . "'></i></a>";
						}
					}

					echo '</div>';
				}

				echo '</div>'; // Description Slide.

				echo '</div>'; // ult-team-member-bio-wrap.
				if ( 'on' == $ult_team_settings['link_switch'] && '' != $ult_team_settings['staff_link'] ) {
					echo '</a>';
				}
				echo '</div>'; // ult-team-member-wrap.
			} elseif ( 'style-1' == $ult_team_settings['team_member_style'] ) {

				echo '<div class="ult-team-member-wrap ult-style-1 ' . esc_attr( $ult_team_settings['custom_team_class'] ) . ' ' . esc_attr( $ult_team_settings['team_css'] ) . '">';
				if ( 'on' == $ult_team_settings['link_switch'] && '' != $ult_team_settings['staff_link'] ) {
					$href = vc_build_link( $ult_team_settings['staff_link'] );

					$url    = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
					$target = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
					$title  = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
					$rel    = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';

					echo '<a ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $title, $rel ) . '>'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
				}
					echo '<div class="ult-team-member-image" style="' . esc_attr( $team_image_style ) . '"> <img class="' . esc_attr( $team_img_grayscale_cls ) . '" src="' . esc_url( apply_filters( 'ultimate_images', $img ) ) . '" alt="' . esc_attr( $alt ) . '"  style="">';
					echo '<span class="ult-team-member-image-overlay ' . esc_attr( $img_hver_class ) . '" ' . $img_hver_data . ' ></span>'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '</div>';// ult-team-member-image.
				if ( 'on' == $ult_team_settings['link_switch'] && '' != $ult_team_settings['staff_link'] ) {
					echo '</a>';
				}

				echo '<div class="ult-team-member-bio-wrap ' . esc_attr( $ult_team_settings['team_member_style'] ) . ' ' . esc_attr( $id ) . '" style="text-align:' . esc_attr( $ult_team_settings['team_member_align_style'] ) . ';' . esc_attr( $title_box_style ) . '; ">';

				echo '<div class="ult-team-member-name-wrap">';
				if ( 'on' == $ult_team_settings['link_switch'] && '' != $ult_team_settings['staff_link'] ) {
					$href = vc_build_link( $ult_team_settings['staff_link'] );

					$url    = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
					$target = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
					$title  = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
					$rel    = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';

					echo '<a ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $title, $rel ) . ' style="text-decoration: none;">'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped

				}
					echo '<' . esc_attr( $ult_team_settings['team_member_name_tag'] ) . ' class="ult-team-member-name ult-responsive" ' . $team_member_name_responsive . ' style="' . esc_attr( $team_member_name_font_styling ) . '">' . esc_attr( $ult_team_settings['name'] ) . '</' . esc_attr( $ult_team_settings['team_member_name_tag'] ) . '>'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
				if ( 'on' == $ult_team_settings['link_switch'] && '' != $ult_team_settings['staff_link'] ) {
					echo '</a>';
				}
				if ( $ult_team_settings['pos_in_org'] ) {
					echo '<div class="ult-team-member-position ult-responsive" ' . $team_member_position_responsive . ' style="' . esc_attr( $team_member_position_font_styling ) . '">' . esc_attr( $ult_team_settings['pos_in_org'] ) . '</div>'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				if ( $content ) {
					echo '<div class="ult-team-member-description ult-responsive" ' . $team_member_desc_responsive . ' style="' . esc_attr( $team_member_description_font_styling ) . '"><p>' . do_shortcode( $content ) . '</p></div>'; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				echo '<div style="margin-bottom:15px">';

				if ( 'on' == $ult_team_settings['divider_effect'] ) {

					$divider_margin = '';
					if ( 'center' != $ult_team_settings['team_member_align_style'] ) {
						$divider_margin = 'margin-' . $ult_team_settings['team_member_align_style'] . ':0px';
					}

					echo '<hr align="' . esc_attr( $ult_team_settings['team_member_align_style'] ) . '" class="ult-team-divider" style="padding-top: ' . esc_attr( $ult_team_settings['team_member_divider_height'] ) . 'px; width: ' . esc_attr( $ult_team_settings['team_member_divider_width'] ) . '%; background-color: ' . esc_attr( $ult_team_settings['team_member_divider_color'] ) . '; ' . esc_attr( $divider_margin ) . '" />';
				}
				echo '</div>';

				$social_icons = json_decode( urldecode( $ult_team_settings['social_links'] ) );
				if ( $social_icons && count( $social_icons ) > 0 && 'on' == $ult_team_settings['social_icon_effect'] ) {

					$icon_styling = 'font-size:' . $ult_team_settings['social_icon_size'] . ' ; margin-left:' . $ult_team_settings['social_icon_space'] . ';margin-right:' . esc_attr( $ult_team_settings['social_icon_space'] ) . ';';
					echo "<div class='ult-social-buttons'>";

					foreach ( $social_icons as $social_link ) {

						if ( isset( $social_link->selected_team_icon ) && '' !== $social_link->selected_team_icon ) {

							$social_icon_url         = ( isset( $social_link->social_icon_url ) && '' !== $social_link->social_icon_url ) ? $social_link->social_icon_url : '#';
							$social_link_title       = ( isset( $social_link->social_link_title ) && '' !== $social_link->social_link_title ) ? $social_link->social_link_title : '';
							$social_icon_color       = ( isset( $social_link->social_icon_color ) && '' !== $social_link->social_icon_color ) ? $social_link->social_icon_color : 'inherit';
							$default_icon_color      = ( 'inherit' != $social_icon_color ) ? 'color:' . $social_icon_color . ';' : '';
							$social_icon_hover_color = ( isset( $social_link->social_icon_hover_color ) && '' !== $social_link->social_icon_hover_color ) ? $social_link->social_icon_hover_color : 'inherit';
							echo "<a href='" . esc_url( $social_icon_url ) . "' target='_blank' rel='noopener' title='" . esc_attr( $social_link_title ) . "' class='ult-team ult-social-icon' style='" . esc_attr( $icon_styling ) . ';' . esc_attr( $default_icon_color ) . "'  data-iconcolor='" . esc_attr( $social_icon_color ) . "' data-iconhover='" . esc_attr( $social_icon_hover_color ) . "' ><i class='" . esc_attr( $social_link->selected_team_icon ) . "'></i></a>";
						}
					}

					echo '</div>';
				}
				echo '</div>'; // ult-team-member-name-wrap.

				echo '</div>'; // ult-team-member-bio-wrap.
				echo '</div>'; // ult-team-member-wrap.
			}
			$is_preset = false; // Display settings for Preset.
			$output    = '';
			if ( isset( $_GET['preset'] ) ) { // PHPCS:ignore:WordPress.Security.NonceVerification.Recommended
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
				$output  = '<pre>';
				$output .= $text;
				$output .= '</pre>';
			}
			echo $output; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
			return ob_get_clean();
		}
		/**
		 * Function that initializes settings of Team Module Module.
		 *
		 * @method init_team
		 */
		public function init_team() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'             => __( 'Team', 'ultimate_vc' ),
						'base'             => 'ult_team',
						'icon'             => 'vc_icon_team',
						'class'            => '',
						'content_element'  => true,
						'controls'         => 'full',
						'category'         => 'Ultimate VC Addons',
						'description'      => __( 'Show your awesome team.', 'ultimate_vc' ),
						'admin_enqueue_js' => preg_replace( '/\s/', '%20', UAVC_URL . 'admin/js/team-admin.js' ),
						'params'           => array(
							// Custom Coding for new team styles.
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Team Style', 'ultimate_vc' ),
								'param_name'  => 'team_member_style',
								'value'       => array(
									__( 'Style 1', 'ultimate_vc' )  => 'style-1',
									__( 'Style 2', 'ultimate_vc' )  => 'style-2',
									__( 'Style 3', 'ultimate_vc' )  => 'style-3',
								),
								'description' => '',
								'group'       => 'Image',
							),
							array(
								'type'        => 'ult_img_single',
								'heading'     => __( 'Select Image', 'ultimate_vc' ),
								'param_name'  => 'image',
								'description' => '',
								'group'       => 'Image',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Grayscale Image', 'ultimate_vc' ),
								'param_name'  => 'team_img_grayscale',
								'value'       => 'on',
								'options'     => array(
									'on' => array(
										'label' => '',
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'default_set' => true,
								'description' => '',
								'dependency'  => array(
									'element' => 'team_member_style',
									'value'   => array( 'style-1', 'style-3' ),
								),
								'group'       => 'Image',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Image Border Style', 'ultimate_vc' ),
								'param_name'  => 'img_border_style',
								'value'       => array(
									__( 'None', 'ultimate_vc' )   => '',
									__( 'Solid', 'ultimate_vc' )  => 'solid',
									__( 'Dashed', 'ultimate_vc' ) => 'dashed',
									__( 'Dotted', 'ultimate_vc' ) => 'dotted',
									__( 'Double', 'ultimate_vc' ) => 'double',
									__( 'Inset', 'ultimate_vc' )  => 'inset',
									__( 'Outset', 'ultimate_vc' ) => 'outset',
								),
								'description' => '',
								'group'       => 'Image',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Image Border Width', 'ultimate_vc' ),
								'param_name'  => 'img_border_width',
								'value'       => '',
								'suffix'      => '',
								'dependency'  => array(
									'element'   => 'img_border_style',
									'not_empty' => true,
								),
								'description' => '',
								'group'       => 'Image',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Image Border Color', 'ultimate_vc' ),
								'param_name'  => 'img_border_color',
								'value'       => '',
								'dependency'  => array(
									'element'   => 'img_border_style',
									'not_empty' => true,
								),
								'description' => '',
								'group'       => 'Image',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Image border radius', 'ultimate_vc' ),
								'param_name'  => 'img_border_radius',
								'value'       => '0',
								'min'         => '0',
								'max'         => '500',
								'step'        => '1',
								'suffix'      => 'px',
								'description' => '',
								'dependency'  => array(
									'element'   => 'img_border_style',
									'not_empty' => true,
								),
								'group'       => 'Image',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Image Hover Effect', 'ultimate_vc' ),
								'param_name'  => 'img_hover_eft',
								'value'       => 'off',
								'options'     => array(
									'on' => array(
										'label' => __( 'Hover effect for the team member image', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'description' => '',
								'dependency'  => array(
									'element' => 'team_member_style',
									'value'   => 'style-1',
								),
								'group'       => 'Image',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Image Hover Color', 'ultimate_vc' ),
								'param_name'  => 'img_hover_color',
								'value'       => '',
								'description' => '',
								'group'       => 'Image',
								'dependency'  => array(
									'element' => 'img_hover_eft',
									'value'   => 'on',
								),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Background Color', 'ultimate_vc' ),
								'param_name'  => 'team_img_bg_color',
								'value'       => '',
								'description' => '',
								'group'       => 'Image',
								'dependency'  => array(
									'element' => 'team_member_style',
									'value'   => array( 'style-2', 'style-3' ),
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Image Opacity', 'ultimate_vc' ),
								'param_name'  => 'team_img_opacity',
								'value'       => 1,
								'min'         => 0,
								'max'         => 1,
								'description' => __( 'Enter value between 0.0 to 1 (0 is maximum transparency, while 1 is lowest)', 'ultimate_vc' ),
								'group'       => 'Image',
								'dependency'  => array(
									'element' => 'team_member_style',
									'value'   => array( 'style-2' ),
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Image Opacity on Hover', 'ultimate_vc' ),
								'param_name'  => 'team_img_hover_opacity',
								'value'       => '0.65',
								'min'         => 0,
								'max'         => 1,
								'description' => __( 'Enter value between 0.0 to 1 (0 is maximum transparency, while 1 is lowest)', 'ultimate_vc' ),
								'group'       => 'Image',
								'dependency'  => array(
									'element' => 'team_member_style',
									'value'   => array( 'style-2' ),
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Image Opacity on Hover', 'ultimate_vc' ),
								'param_name'  => 'team_img_hover_opacity_style3',
								'value'       => 0.1,
								'min'         => 0,
								'max'         => 1,
								'description' => __( 'Enter value between 0.0 to 1 (0 is maximum transparency, while 1 is lowest)', 'ultimate_vc' ),
								'group'       => 'Image',
								'dependency'  => array(
									'element' => 'team_member_style',
									'value'   => array( 'style-3' ),
								),
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Custom Class', 'ultimate_vc' ),
								'param_name'  => 'custom_team_class',
								'description' => '',
								'group'       => 'Image',
							),
							array(
								'type'             => 'textfield',
								'heading'          => __( 'Name', 'ultimate_vc' ),
								'param_name'       => 'name',
								'admin_label'      => true,
								'description'      => '',
								'group'            => 'Text',
								'edit_field_class' => 'vc_col-sm-8',
							),

							array(
								'type'             => 'dropdown',
								'class'            => '',
								'heading'          => __( 'Tag', 'ultimate_vc' ),
								'param_name'       => 'team_member_name_tag',
								'value'            => array(
									__( 'Default', 'ultimate_vc' )  => 'h2',
									__( 'H1', 'ultimate_vc' )  => 'h1',
									__( 'H3', 'ultimate_vc' )  => 'h3',
									__( 'H4', 'ultimate_vc' )  => 'h4',
									__( 'H5', 'ultimate_vc' )  => 'h5',
									__( 'H6', 'ultimate_vc' )  => 'h6',
									__( 'Div', 'ultimate_vc' )  => 'div',
									__( 'p', 'ultimate_vc' )  => 'p',
									__( 'span', 'ultimate_vc' )  => 'span',
								),
								'description'      => __( 'Default is H2', 'ultimate_vc' ),
								'group'            => 'Text',
								'edit_field_class' => 'ult-param-padding-remove vc_col-sm-4',
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Designation', 'ultimate_vc' ),
								'param_name'  => 'pos_in_org',
								'description' => '',
								'group'       => 'Text',
							),
							array(
								'type'        => 'textarea_html',
								'heading'     => __( 'Description', 'ultimate_vc' ),
								'param_name'  => 'content',
								'description' => '',
								'group'       => 'Text',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Text Alignment', 'ultimate_vc' ),
								'param_name'  => 'team_member_align_style',
								'value'       => array(
									__( 'Center', 'ultimate_vc' )   => 'center',
									__( 'Left', 'ultimate_vc' )  => 'left',
									__( 'Right', 'ultimate_vc' )  => 'right',
								),
								'description' => '',
								'group'       => 'Text',
							),
							array(
								'type'       => 'ultimate_spacing',
								'heading'    => __( 'Space around text', 'ultimate_vc' ),
								'param_name' => 'title_box_padding',
								'mode'       => 'padding',
								'unit'       => 'px',
								'positions'  => array(
									__( 'Top', 'ultimate_vc' )  => '',
									__( 'Right', 'ultimate_vc' ) => '',
									__( 'Bottom', 'ultimate_vc' ) => '',
									__( 'Left', 'ultimate_vc' ) => '',
								),
								'group'      => __( 'Text', 'ultimate_vc' ),
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Enable Social Icons', 'ultimate_vc' ),
								'param_name'  => 'social_icon_effect',
								'value'       => 'off',
								'options'     => array(
									'on' => array(
										'label' => __( 'Add Social Icon links to connect on social network', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'description' => '',
								'group'       => 'Social Links',
							),
							array(
								'type'       => 'param_group',
								'heading'    => __( 'Add Social Links', 'ultimate_vc' ),
								'param_name' => 'social_links',
								'group'      => __( 'Social Links', 'ultimate_vc' ),
								'value'      => rawurlencode(
									wp_json_encode(
										array(
											array(
												'selected_team_icon' => '',
												'social_title' => '',
												'social_icon_url' => '',
												'social_icon_color' => '',
												'social_icon_hover_color' => '',
											),
										)
									)
								),
								'params'     => array(
									array(
										'type'        => 'textfield',
										'heading'     => __( 'Title', 'my-text-domain' ),
										'param_name'  => 'social_link_title',
										'value'       => '',
										'description' => '',
										'admin_label' => true,
									),
									array(
										'type'        => 'textfield',
										'heading'     => __( 'Link', 'ultimate_vc' ),
										'param_name'  => 'social_icon_url',
										'description' => '',
										'value'       => '#',
									),
									array(
										'type'        => 'icon_manager',
										'heading'     => __( 'Select Icon', 'js_composer' ),
										'param_name'  => 'selected_team_icon',
										'value'       => '',
										'description' => __( 'Select icon from library.', 'ultimate_vc' ),
									),
									array(
										'type'       => 'colorpicker',
										'class'      => '',
										'heading'    => __( 'Icon Color', 'ultimate_vc' ),
										'param_name' => 'social_icon_color',
										'value'      => '',
									),
									array(
										'type'       => 'colorpicker',
										'class'      => '',
										'heading'    => __( 'Icon hover Color', 'ultimate_vc' ),
										'param_name' => 'social_icon_hover_color',
										'value'      => '',
									),
								),
								'dependency' => array(
									'element' => 'social_icon_effect',
									'value'   => 'on',
								),
								'callbacks'  => array(
									'after_add' => 'vcChartParamAfterAddCallback',
								),
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Social Icon Size', 'ultimate_vc' ),
								'param_name'  => 'social_icon_size',
								'value'       => '16',
								'suffix'      => 'px',
								'dependency'  => array(
									'element'   => 'img_border_style',
									'not_empty' => true,
								),
								'description' => '',
								'dependency'  => array(
									'element' => 'social_icon_effect',
									'value'   => 'on',
								),
								'group'       => __( 'Social Links', 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Spacing Between Social Icons', 'ultimate_vc' ),
								'param_name'  => 'social_icon_space',
								'value'       => '10',
								'suffix'      => 'px',
								'dependency'  => array(
									'element'   => 'img_border_style',
									'not_empty' => true,
								),
								'description' => '',
								'dependency'  => array(
									'element' => 'social_icon_effect',
									'value'   => 'on',
								),
								'group'       => __( 'Social Links', 'ultimate_vc' ),
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Separator', 'ultimate_vc' ),
								'param_name'  => 'divider_effect',
								'value'       => 'off',
								'options'     => array(
									'on' => array(
										'label' => __( 'Separator between description & social icons', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'description' => '',
								'group'       => 'Social Links',
								'dependency'  => array(
									'element' => 'social_icon_effect',
									'value'   => 'on',
								),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Separator Color', 'ultimate_vc' ),
								'param_name'  => 'team_member_divider_color',
								'value'       => '',
								'description' => '',
								'group'       => 'Social Links',
								'dependency'  => array(
									'element' => 'divider_effect',
									'value'   => 'on',
								),
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Separator Height', 'ultimate_vc' ),
								'param_name' => 'team_member_divider_height',
								'value'      => 1,
								'min'        => 1,
								'max'        => 500,
								'suffix'     => 'px',
								'group'      => 'Social Links',
								'dependency' => array(
									'element' => 'divider_effect',
									'value'   => 'on',
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Separator Width', 'ultimate_vc' ),
								'param_name'  => 'team_member_divider_width',
								'value'       => '80',
								'suffix'      => '%',
								'description' => '',
								'group'       => 'Social Links',
								'dependency'  => array(
									'element' => 'divider_effect',
									'value'   => 'on',
								),
							),
							array(
								'type'             => 'text',
								'param_name'       => 'title_text_typography',
								'heading'          => __( '<h4>Name Typography</h4>', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Typography',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'       => 'ultimate_google_fonts',
								'heading'    => __( 'Font Family', 'ultimate_vc' ),
								'param_name' => 'team_member_name_font',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'team_member_name_font_style',
								'value'      => '',
								'group'      => 'Typography',
							),
							// Responsive Param.
							array(
								'type'       => 'ultimate_responsive',
								'class'      => 'font-size',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'team_member_name_font_size',
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
								'class'      => 'font-size',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'team_member_name_line_height',
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
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Text Color', 'ultimate_vc' ),
								'param_name'  => 'team_member_name_color',
								'value'       => '',
								'description' => '',
								'group'       => 'Typography',
							),
							array(
								'type'             => 'text',
								'param_name'       => 'title_text_typography',
								'heading'          => __( '<h4>Designation Typography</h4>', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Typography',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'       => 'ultimate_google_fonts',
								'heading'    => __( 'Font Family', 'ultimate_vc' ),
								'param_name' => 'team_member_position_font',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'team_member_position_font_style',
								'value'      => '',
								'group'      => 'Typography',
							),
							// Responsive Param.
							array(
								'type'       => 'ultimate_responsive',
								'class'      => 'font-size',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'team_member_position_font_size',
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
								'class'      => 'font-size',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'team_member_position_line_height',
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
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Text Color', 'ultimate_vc' ),
								'param_name'  => 'team_member_org_color',
								'value'       => '',
								'description' => '',
								'group'       => 'Typography',
							),
							array(
								'type'             => 'text',
								'param_name'       => 'title_text_typography',
								'heading'          => __( '<h4>Description Typography</h4>', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Typography',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'       => 'ultimate_google_fonts',
								'heading'    => __( 'Font Family', 'ultimate_vc' ),
								'param_name' => 'team_member_description_font',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'team_member_description_font_style',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => 'font-size',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'team_member_description_font_size',
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
								'class'      => 'font-size',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'team_member_description_line_height',
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
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Text Color', 'ultimate_vc' ),
								'param_name'  => 'team_member_desc_color',
								'value'       => '',
								'description' => '',
								'group'       => 'Typography',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Responsive', 'ultimate_vc' ),
								'param_name'  => 'team_member_responsive_enable',
								'value'       => 'off',
								'options'     => array(
									'on' => array(
										'label' => __( 'Apply Breakpoint to container?', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'description' => '',
								'dependency'  => array(
									'element' => 'team_member_style',
									'value'   => 'style-2',
								),
								'group'       => 'Advanced',
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Breakpoint', 'ultimate_vc' ),
								'param_name'  => 'team_responsive_width',
								'suffix'      => 'px',
								'description' => 'Breakpoint is the point of screen resolution from where you can set your Style-2 into Style-1 to the minimum screen resolution.',
								'dependency'  => array(
									'element' => 'team_member_responsive_enable',
									'value'   => 'on',
								),
								'group'       => 'Advanced',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Custom link to staff page', 'ultimate_vc' ),
								'param_name'  => 'link_switch',
								'value'       => '',
								'options'     => array(
									'on' => array(
										'label' => __( 'Add custom link to employee page', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'description' => '',
								'dependency'  => '',
								'group'       => 'Advanced',
							),
							array(
								'type'       => 'vc_link',
								'class'      => '',
								'heading'    => __( 'Custom Link', 'ultimate_vc' ),
								'param_name' => 'staff_link',
								'value'      => '',
								'group'      => __( 'Advanced', 'ultimate_vc' ),
								'dependency' => array(
									'element' => 'link_switch',
									'value'   => 'on',
								),
							),
							array(
								'type'       => 'css_editor',
								'heading'    => __( 'CSS box', 'my-text-domain' ),
								'param_name' => 'team_css',
								'group'      => __( 'Design Options', 'my-text-domain' ),
							),
						), // params array.
					)
				);
			}
		}
	}
	new Ultimate_Team();

	if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Ult_Team' ) ) {
		/**
		 * Function that checks if the class is exists or not.
		 */
		class WPBakeryShortCode_Ult_Team extends WPBakeryShortCode {
		}
	}
}

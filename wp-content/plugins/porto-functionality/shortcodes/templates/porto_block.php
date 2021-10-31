<?php
$load_posts_only = function_exists( 'porto_is_ajax' ) && porto_is_ajax() && isset( $_GET['load_posts_only'] );
if ( $load_posts_only ) {
	return false;
}

$output = $id = $name = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'id'                 => '',
			'name'               => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'post_type'          => '',
			'not_render_home'    => '',
			'el_class'           => '',
		),
		$atts
	)
);

if ( empty( $post_type ) ) {
	$post_type = 'porto_builder';
}

if ( ( $not_render_home && is_front_page() ) || ! post_type_exists( $post_type ) ) {
	return;
}

if ( $id || $name ) {
	global $wpdb;
	$el_class = porto_shortcode_extract_class( $el_class );

	if ( $id ) {
		$where = is_numeric( $id ) ? 'ID' : 'post_name';
	} else {
		$where = is_numeric( $name ) ? 'ID' : 'post_name';
	}

	$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s AND $where = %s", $post_type, $id ? absint( $id ) : sanitize_text_field( $name ) ) );

	if ( $post_id ) {
		// Polylang
		if ( function_exists( 'pll_get_post' ) && pll_get_post( $post_id ) ) {
			$lang_id = pll_get_post( $post_id );
			if ( $lang_id ) {
				$post_id = $lang_id;
			}
		}

		// WPML
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			if ( function_exists( 'icl_object_id' ) ) {
				$lang_id = icl_object_id( $post_id, $post_type, false, ICL_LANGUAGE_CODE );
			} else {
				$lang_id = apply_filters( 'wpml_object_id', $post_id, $post_type, false, ICL_LANGUAGE_CODE );
			}
			if ( $lang_id ) {
				$post_id = $lang_id;
			}
		}
	}

	if ( $post_id ) {
		$post_id     = (int) $post_id;
		$before_html = '';
		// Add edit link for admins.
		if ( current_user_can( 'edit_pages' ) && ! is_customize_preview() && (
				( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) &&
				( ! function_exists( 'porto_is_elementor_preview' ) || ! porto_is_elementor_preview() ) &&
				( ! function_exists( 'porto_is_vc_preview' ) || ! porto_is_vc_preview() )
				) ) {
			if ( defined( 'VCV_VERSION' ) && 'fe' == get_post_meta( $post_id, 'vcv-be-editor', true ) ) {
				$edit_link = admin_url( 'post.php?post=' . $post_id . '&action=edit&vcv-action=frontend&vcv-source-id=' . $post_id );
			} elseif ( defined( 'ELEMENTOR_VERSION' ) && get_post_meta( $post_id, '_elementor_edit_mode', true ) ) {
				$edit_link = admin_url( 'post.php?post=' . $post_id . '&action=elementor' );
			} else {
				$edit_link = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
			}
			$builder_type = get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
			if ( ! $builder_type ) {
				$builder_type = __( 'Template', 'porto' );
			}
			/* translators: template name */
			$before_html = '<div class="pb-edit-link" data-title="' . sprintf( esc_html__( 'Edit %s: %s', 'porto' ), esc_attr( $builder_type ), esc_attr( get_the_title( $post_id ) ) ) . '" data-link="' . esc_url( $edit_link ) . '"></div>';
		}

		$the_post = get_post( $post_id, null, 'display' );

		$shortcodes_custom_css = '';
		$elements_data         = false;

		if ( defined( 'ELEMENTOR_VERSION' ) && get_post_meta( $post_id, '_elementor_edit_mode', true ) ) {
			$elements_data = get_post_meta( $post_id, '_elementor_data', true );

			if ( $elements_data ) {
				$elements_data = json_decode( $elements_data, true );
			}
		}

		$inner_container = get_post_meta( $post_id, 'container', true );

		if ( ! empty( $elements_data ) ) {
			if ( ! wp_style_is( 'elementor-frontend', 'enqueued' ) ) {
				wp_enqueue_style( 'elementor-icons' );
				wp_enqueue_style( 'elementor-animations' );
				wp_enqueue_style( 'elementor-frontend' );
				do_action( 'elementor/frontend/after_enqueue_styles' );
			}
			$css_file               = new Elementor\Core\Files\CSS\Post( $post_id );
			$shortcodes_custom_css .= $css_file->get_content();

			$post_content  = $before_html;
			$post_content .= '<div class="porto-block' . ( function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() && is_single( $post_id ) ? '" data-el_cls="elementor elementor-' . intval( $post_id ) : ' elementor elementor-' . intval( $post_id ) ) . '">';
			if ( 'fluid' == $inner_container ) {
				$post_content .= '<div class="container-fluid">';
			}
			ob_start();
			foreach ( $elements_data as $element_data ) {

				$element = Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );

				if ( ! $element ) {
					continue;
				}

				$element->print_element();
			}
			$post_content .= ob_get_clean();
			if ( 'fluid' == $inner_container ) {
				$post_content .= '</div>';
			}
			$post_content .= '</div>';
		} else {
			$post_content = $the_post->post_content;
			if ( defined( 'VCV_VERSION' ) ) {
				$post_content = vcfilter( 'vcv:frontend:content', $post_content );

				$bundle_url = get_post_meta( $post_id, 'vcvSourceCssFileUrl', true );
				if ( $bundle_url ) {
					if ( 0 !== strpos( $bundle_url, 'http' ) ) {
						if ( false === strpos( $bundle_url, 'assets-bundles' ) ) {
							$bundle_url = '/assets-bundles/' . $bundle_url;
						}
					}
					$handle = 'vcv:assets:source:main:styles:' . vchelper( 'Str' )->slugify( $bundle_url );
					if ( ! wp_style_is( $handle, 'enqueued' ) ) {
						$path                   = vchelper( 'Assets' )->getFilePath( str_replace( '/assets-bundles', '', $bundle_url ) );
						$shortcodes_custom_css .= vchelper( 'File' )->getContents( $path );
					}
				}
			}

			$use_google_map = get_post_meta( $post_id, 'porto_page_use_google_map_api', true );

			if ( '1' === $use_google_map || stripos( $post_content, '[porto_google_map' ) ) {
				wp_enqueue_script( 'googleapis' );
			}
			if ( stripos( $post_content, '[porto_concept ' ) ) {
				wp_enqueue_script( 'modernizr' );
				wp_enqueue_style( 'jquery-flipshow' );
			}


			if ( class_exists( 'Ultimate_VC_Addons' ) ) {
				$isajax              = false;
				$ultimate_ajax_theme = get_option( 'ultimate_ajax_theme' );
				if ( 'enable' == $ultimate_ajax_theme ) {
					$isajax = true;
				}
				$dependancy = array( 'jquery' );

				$bsf_options             = get_option( 'bsf_options' );
				$ultimate_global_scripts = ( isset( $bsf_options['ultimate_global_scripts'] ) ) ? $bsf_options['ultimate_global_scripts'] : false;

				if ( 'enable' !== $ultimate_global_scripts ) {
					if ( stripos( $post_content, 'font_call:' ) ) {
						preg_match_all( '/font_call:(.*?)"/', $post_content, $display );
						enquque_ultimate_google_fonts_optimzed( $display[1] );
					}

					$ultimate_js  = get_option( 'ultimate_js', 'disable' );
					$bsf_dev_mode = ( isset( $bsf_options['dev_mode'] ) ) ? $bsf_options['dev_mode'] : false;

					if ( ( 'enable' == $ultimate_js || $isajax ) && ( 'enable' != $bsf_dev_mode ) ) {
						if (
							stripos( $post_content, '[swatch_container' )
							|| stripos( $post_content, '[ultimate_modal' )
						) {
							wp_enqueue_script( 'ultimate-modernizr' );
						}

						if ( stripos( $post_content, '[ultimate_exp_section' ) ||
							stripos( $post_content, '[info_circle' ) ) {
							wp_enqueue_script( 'jquery_ui' );
						}

						if ( stripos( $post_content, '[icon_timeline' ) ) {
							wp_enqueue_script( 'masonry' );
						}

						if ( $isajax ) { // if ajax site load all js
							wp_enqueue_script( 'masonry' );
						}

						if ( stripos( $post_content, '[ultimate_google_map' ) ) {
							if ( defined( 'DISABLE_ULTIMATE_GOOGLE_MAP_API' ) && ( DISABLE_ULTIMATE_GOOGLE_MAP_API == true || DISABLE_ULTIMATE_GOOGLE_MAP_API == 'true' ) ) {
								$load_map_api = false;
							} else {
								$load_map_api = true;
							}
							if ( $load_map_api ) {
								wp_enqueue_script( 'googleapis' );
							}
						}

						if ( stripos( $post_content, '[ultimate_modal' ) ) {
							//$modal_fixer = get_option('ultimate_modal_fixer');
							//if($modal_fixer === 'enable')
							//wp_enqueue_script('ultimate-modal-all-switched');
							//else
							wp_enqueue_script( 'ultimate-modal-all' );
						}
					} elseif ( 'disable' == $ultimate_js ) {
						wp_enqueue_script( 'ultimate-vc-params' );

						if (
							stripos( $post_content, '[ultimate_spacer' )
							|| stripos( $post_content, '[ult_buttons' )
							|| stripos( $post_content, '[ult_team' )
							|| stripos( $post_content, '[ultimate_icon_list' )
						) {
							wp_enqueue_script( 'ultimate-custom' );
						}
						if (
							stripos( $post_content, '[just_icon' )
							|| stripos( $post_content, '[ult_animation_block' )
							|| stripos( $post_content, '[icon_counter' )
							|| stripos( $post_content, '[ultimate_google_map' )
							|| stripos( $post_content, '[icon_timeline' )
							|| stripos( $post_content, '[bsf-info-box' )
							|| stripos( $post_content, '[info_list' )
							|| stripos( $post_content, '[ultimate_info_table' )
							|| stripos( $post_content, '[interactive_banner_2' )
							|| stripos( $post_content, '[interactive_banner' )
							|| stripos( $post_content, '[ultimate_pricing' )
							|| stripos( $post_content, '[ultimate_icons' )
						) {
							wp_enqueue_script( 'ultimate-appear' );
							wp_enqueue_script( 'ultimate-custom' );
						}
						if ( stripos( $post_content, '[ultimate_heading' ) ) {
							wp_enqueue_script( 'ultimate-headings-script' );
						}
						if ( stripos( $post_content, '[ultimate_carousel' ) ) {
							wp_enqueue_script( 'ult-slick' );
							wp_enqueue_script( 'ultimate-appear' );
							wp_enqueue_script( 'ult-slick-custom' );
						}
						if ( stripos( $post_content, '[ult_countdown' ) ) {
							wp_enqueue_script( 'jquery.timecircle' );
							wp_enqueue_script( 'jquery.countdown' );
						}
						if ( stripos( $post_content, '[icon_timeline' ) ) {
							wp_enqueue_script( 'masonry' );
						}
						if ( stripos( $post_content, '[ultimate_info_banner' ) ) {
							wp_enqueue_script( 'ultimate-appear' );
							wp_enqueue_script( 'utl-info-banner-script' );
						}
						if ( stripos( $post_content, '[ultimate_google_map' ) ) {
							if ( defined( 'DISABLE_ULTIMATE_GOOGLE_MAP_API' ) && ( DISABLE_ULTIMATE_GOOGLE_MAP_API == true || DISABLE_ULTIMATE_GOOGLE_MAP_API == 'true' ) ) {
								$load_map_api = false;
							} else {
								$load_map_api = true;
							}
							if ( $load_map_api ) {
								wp_enqueue_script( 'googleapis' );
							}
						}
						if ( stripos( $post_content, '[swatch_container' ) ) {
							wp_enqueue_script( 'ultimate-modernizr' );
							wp_enqueue_script( 'swatchbook-js' );
						}
						if ( stripos( $post_content, '[ult_ihover' ) ) {
							wp_enqueue_script( 'ult_ihover_js' );
						}
						if ( stripos( $post_content, '[ult_hotspot' ) ) {
							wp_enqueue_script( 'ult_hotspot_tooltipster_js' );
							wp_enqueue_script( 'ult_hotspot_js' );
						}
						if ( stripos( $post_content, '[ult_content_box' ) ) {
							wp_enqueue_script( 'ult_content_box_js' );
						}
						if ( stripos( $post_content, '[bsf-info-box' ) ) {
							wp_enqueue_script( 'info_box_js' );
						}
						if ( stripos( $post_content, '[icon_counter' ) ) {
							wp_enqueue_script( 'flip_box_js' );
						}
						if ( stripos( $post_content, '[ultimate_ctation' ) ) {
							wp_enqueue_script( 'utl-ctaction-script' );
						}
						if ( stripos( $post_content, '[stat_counter' ) ) {
							wp_enqueue_script( 'ultimate-appear' );
							wp_enqueue_script( 'ult-stats-counter-js' );
							//wp_enqueue_script('ult-slick-custom');
							wp_enqueue_script( 'ultimate-custom' );
							array_push( $dependancy, 'stats-counter-js' );
						}
						if ( stripos( $post_content, '[ultimate_video_banner' ) ) {
							wp_enqueue_script( 'ultimate-video-banner-script' );
						}
						if ( stripos( $post_content, '[ult_dualbutton' ) ) {
							wp_enqueue_script( 'jquery.dualbtn' );

						}
						if ( stripos( $post_content, '[ult_createlink' ) ) {
							wp_enqueue_script( 'jquery.ult_cllink' );
						}
						if ( stripos( $post_content, '[ultimate_img_separator' ) ) {
							wp_enqueue_script( 'ultimate-appear' );
							wp_enqueue_script( 'ult-easy-separator-script' );
							wp_enqueue_script( 'ultimate-custom' );
						}

						if ( stripos( $post_content, '[ult_tab_element' ) ) {
							wp_enqueue_script( 'ultimate-appear' );
							wp_enqueue_script( 'ult_tabs_rotate' );
							wp_enqueue_script( 'ult_tabs_acordian_js' );
						}
						if ( stripos( $post_content, '[ultimate_exp_section' ) ) {
							wp_enqueue_script( 'jquery_ui' );
							wp_enqueue_script( 'jquery_ultimate_expsection' );
						}

						if ( stripos( $post_content, '[info_circle' ) ) {
							wp_enqueue_script( 'jquery_ui' );
							wp_enqueue_script( 'ultimate-appear' );
							wp_enqueue_script( 'info-circle' );
							//wp_enqueue_script('info-circle-ui-effect');
						}

						if ( stripos( $post_content, '[ultimate_modal' ) ) {
							wp_enqueue_script( 'ultimate-modernizr' );
							//$modal_fixer = get_option('ultimate_modal_fixer');
							//if($modal_fixer === 'enable')
							//wp_enqueue_script('ultimate-modal-all-switched');
							//else
							wp_enqueue_script( 'ultimate-modal-all' );
						}

						if ( stripos( $post_content, '[ult_sticky_section' ) ) {
							wp_enqueue_script( 'ult_sticky_js' );
							wp_enqueue_script( 'ult_sticky_section_js' );
						}

						if ( stripos( $post_content, '[ult_team' ) ) {
							wp_enqueue_script( 'ultimate-team' );
						}
					}

					$ultimate_css = get_option( 'ultimate_css' );

					if ( 'enable' == $ultimate_css ) {
						if ( stripos( $post_content, '[ultimate_carousel' ) ) {
							wp_enqueue_style( 'ult-icons' );
						}
					} else {

						$ib_2_found = false;
						$ib_found   = false;

						if ( stripos( $post_content, '[ult_animation_block' ) ) {
							wp_enqueue_style( 'ultimate-animate' );
						}
						if ( stripos( $post_content, '[icon_counter' ) ) {
							wp_enqueue_style( 'ultimate-animate' );
							wp_enqueue_style( 'ult-flip-style' );
						}
						if ( stripos( $post_content, '[ult_countdown' ) ) {
							wp_enqueue_style( 'ult-countdown' );
						}
						if ( stripos( $post_content, '[ultimate_icon_list' ) ) {
							wp_enqueue_style( 'ultimate-animate' );
							wp_enqueue_style( 'ultimate-tooltip' );
						}
						if ( stripos( $post_content, '[ultimate_carousel' ) ) {
							wp_enqueue_style( 'ult-slick' );
							wp_enqueue_style( 'ult-icons' );
							wp_enqueue_style( 'ultimate-animate' );
						}
						if ( stripos( $post_content, '[ultimate_fancytext' ) ) {
							wp_enqueue_style( 'ultimate-fancytext-style' );
						}
						if ( stripos( $post_content, '[ultimate_ctation' ) ) {
							wp_enqueue_style( 'utl-ctaction-style' );
						}
						if ( stripos( $post_content, '[ult_buttons' ) ) {
							wp_enqueue_style( 'ult-btn' );
						}
						if ( stripos( $post_content, '[ultimate_heading' ) ) {
							wp_enqueue_style( 'ultimate-headings-style' );
						}
						if ( stripos( $post_content, '[ultimate_icons' ) || stripos( $post_content, '[single_icon' ) ) {
							wp_enqueue_style( 'ultimate-animate' );
							wp_enqueue_style( 'ultimate-tooltip' );
						}
						if ( stripos( $post_content, '[ult_ihover' ) ) {
							wp_enqueue_style( 'ult_ihover_css' );
						}
						if ( stripos( $post_content, '[ult_hotspot' ) ) {
							wp_enqueue_style( 'ult_hotspot_css' );
							wp_enqueue_style( 'ult_hotspot_tooltipster_css' );
						}
						if ( stripos( $post_content, '[ult_content_box' ) ) {
							wp_enqueue_style( 'ult_content_box_css' );
						}
						if ( stripos( $post_content, '[bsf-info-box' ) ) {
							wp_enqueue_style( 'ultimate-animate' );
							wp_enqueue_style( 'info-box-style' );
						}
						if ( stripos( $post_content, '[info_circle' ) ) {
							wp_enqueue_style( 'ultimate-animate' );
							wp_enqueue_style( 'info-circle' );
						}
						if ( stripos( $post_content, '[ultimate_info_banner' ) ) {
							wp_enqueue_style( 'utl-info-banner-style' );
							wp_enqueue_style( 'ultimate-animate' );
						}
						if ( stripos( $post_content, '[icon_timeline' ) ) {
							wp_enqueue_style( 'ultimate-animate' );
							wp_enqueue_style( 'ultimate-timeline-style' );
						}
						if ( stripos( $post_content, '[just_icon' ) ) {
							wp_enqueue_style( 'ultimate-animate' );
							wp_enqueue_style( 'ultimate-tooltip' );
						}
						if ( stripos( $post_content, '[interactive_banner_2' ) ) {
							$ib_2_found = true;
						}
						if ( stripos( $post_content, '[interactive_banner' ) && ! stripos( $post_content, '[interactive_banner_2' ) ) {
							$ib_found = true;
						}
						if ( stripos( $post_content, '[interactive_banner ' ) && stripos( $post_content, '[interactive_banner_2' ) ) {
							$ib_found   = true;
							$ib_2_found = true;
						}

						if ( $ib_found && ! $ib_2_found ) {
							wp_enqueue_style( 'ultimate-animate' );
							wp_enqueue_style( 'ult-interactive-banner' );
						} elseif ( ! $ib_found && $ib_2_found ) {
							wp_enqueue_style( 'ult-ib2-style' );
						} elseif ( $ib_found && $ib_2_found ) {
							wp_enqueue_style( 'ultimate-animate' );
							wp_enqueue_style( 'ult-interactive-banner' );
							wp_enqueue_style( 'ult-ib2-style' );
						}
						if ( stripos( $post_content, '[info_list' ) ) {
							wp_enqueue_style( 'ultimate-animate' );
						}
						if ( stripos( $post_content, '[ultimate_modal' ) ) {
							wp_enqueue_style( 'ultimate-animate' );
							wp_enqueue_style( 'ultimate-modal' );
						}
						if ( stripos( $post_content, '[ultimate_info_table' ) ) {
							wp_enqueue_style( 'ultimate-animate' );
							wp_enqueue_style( 'ultimate-pricing' );
						}
						if ( stripos( $post_content, '[ultimate_pricing' ) ) {
							wp_enqueue_style( 'ultimate-animate' );
							wp_enqueue_style( 'ultimate-pricing' );
						}
						if ( stripos( $post_content, '[swatch_container' ) ) {
							wp_enqueue_style( 'swatchbook-css' );
						}
						if ( stripos( $post_content, '[stat_counter' ) ) {
							wp_enqueue_style( 'ultimate-animate' );
							wp_enqueue_style( 'ult-stats-counter-style' );
						}
						if ( stripos( $post_content, '[ultimate_video_banner' ) ) {
							wp_enqueue_style( 'ultimate-video-banner-style' );
						}
						if ( stripos( $post_content, '[ult_dualbutton' ) ) {
							wp_enqueue_style( 'ult-dualbutton' );
						}
						if ( stripos( $post_content, '[ult_createlink' ) ) {
							wp_enqueue_style( 'ult_cllink' );
						}
						if ( stripos( $post_content, '[ultimate_img_separator' ) ) {
							wp_enqueue_style( 'ultimate-animate' );
							wp_enqueue_style( 'ult-easy-separator-style' );
						}
						if ( stripos( $post_content, '[ult_tab_element' ) ) {
							wp_enqueue_style( 'ult_tabs' );
							wp_enqueue_style( 'ult_tabs_acordian' );
						}
						if ( stripos( $post_content, '[ultimate_exp_section' ) ) {
							wp_enqueue_style( 'style_ultimate_expsection' );
						}
						if ( stripos( $post_content, '[ult_sticky_section' ) ) {
							wp_enqueue_style( 'ult_sticky_section_css' );
						}
						if ( stripos( $post_content, '[ult_team' ) ) {
							wp_enqueue_style( 'ultimate-team' );
						}
					}

					if ( stripos( $post_content, '[ultimate_google_map' ) ) {
						if ( ! wp_script_is( 'googleapis', 'done' ) ) {
							global $porto_settings;
							$api     = 'https://maps.googleapis.com/maps/api/js';
							$map_key = ! empty( $porto_settings['gmap_api'] ) ? $porto_settings['gmap_api'] : '';
							if ( $map_key ) {
								$arr_params = array(
									'key' => $map_key,
								);
								$api        = esc_url( add_query_arg( $arr_params, $api ) );
							}
							echo "<script src='" . $api . "'></script>";
							wp_dequeue_script( 'googleapis' );
						}
					}
				}
			}

			$shortcodes_custom_css .= get_post_meta( $post_id, '_wpb_shortcodes_custom_css', true );
			if ( $shortcodes_custom_css && defined( 'WPB_VC_VERSION' ) ) {
				global $porto_settings_optimize;
				if ( isset( $porto_settings_optimize['lazyload'] ) && $porto_settings_optimize['lazyload'] && ( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) ) {
					preg_match_all( '/\.vc_custom_([^{]*)[^}]*((background-image):[^}]*|(background):[^}]*url\([^}]*)}/', $shortcodes_custom_css, $matches );
					if ( isset( $matches[0] ) && ! empty( $matches[0] ) ) {
						foreach ( $matches[0] as $key => $value ) {
							if ( ! isset( $matches[1][ $key ] ) || empty( $matches[1][ $key ] ) ) {
								continue;
							}
							if ( preg_match( '/\[(porto_interactive_banner|vc_row|vc_column|vc_row_inner|vc_column_inner)\s[^]]*.vc_custom_' . trim( $matches[1][ $key ] ) . '[^]]*\]/', $post_content ) ) {
								if ( ! empty( $matches[3][ $key ] ) ) {
									$shortcodes_custom_css = preg_replace( '/\.vc_custom_' . $matches[1][ $key ] . '([^}]*)(background-image:[^;]*;)/', '.vc_custom_' . $matches[1][ $key ] . '$1', $shortcodes_custom_css );
								} else {
									$shortcodes_custom_css = preg_replace( '/\.vc_custom_' . $matches[1][ $key ] . '([^}]*)(background)(:\s#[A-Fa-f0-9]{3,6}\s)(url\([^)]*\))\s(!important;)/', '.vc_custom_' . $matches[1][ $key ] . '$1background-color$3$5', $shortcodes_custom_css );
								}
							}
						}
					}
				}
			}

			if ( function_exists( 'porto_the_content' ) ) {
				$post_content = porto_the_content( $post_content, false );
			} else {
				$post_content = do_shortcode( $post_content );
			}

			$output .= $before_html;
			$output .= '<div class="porto-block' . ( $el_class ? esc_attr( $el_class ) : '' ) . '"';
			if ( $animation_type ) {
				$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
				if ( $animation_delay ) {
					$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
				}
				if ( $animation_duration && 1000 != $animation_duration ) {
					$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
				}
			}
			$output .= '>';

			if ( 'fluid' == $inner_container ) {
				$output .= '<div class="container-fluid">';
			}
		}

		if ( defined( 'WPB_VC_VERSION' ) ) {
			$shortcodes_custom_css .= get_post_meta( $post_id, '_wpb_post_custom_css', true );
		}
		$shortcodes_custom_css .= get_post_meta( $post_id, 'custom_css', true );
		if ( $shortcodes_custom_css ) {
			$output .= '<style>';
			$output .= wp_strip_all_tags( preg_replace( '#<style[^>]*>(.*)</style>#is', '$1', $shortcodes_custom_css ) );
			$output .= '</style>';
		}

		if ( 'yes' == $inner_container ) {
			$output .= '<div class="container">';
		}

		$output .= apply_filters( 'porto_lazy_load_images', $post_content );

		if ( 'yes' == $inner_container || ( empty( $elements_data ) && 'fluid' == $inner_container ) ) {
			$output .= '</div>';
		}

		if ( empty( $elements_data ) ) {
			$output .= '</div>';
		}

		$shortcodes_custom_js = get_post_meta( $post_id, 'custom_js_body', true );
		if ( $shortcodes_custom_js ) {
			$output .= '<script>';
			$output .= trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', $shortcodes_custom_js ) );
			$output .= '</script>';
		}

		echo porto_filter_output( $output );
	}
}

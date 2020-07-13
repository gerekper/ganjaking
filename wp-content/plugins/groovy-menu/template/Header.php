<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Displays a navigation menu.
 *
 * @param array             $args           {
 *                                          Optional. Array of nav menu arguments.
 *
 * @type int|string|WP_Term $menu           Desired menu. Accepts (matching in order) id, slug, name, menu object. Default empty.
 * @type string             $gm_preset_id   Groovy menu preset id.
 * @type bool               $gm_echo        Whether to echo the menu or return it. Default true.
 * @type int                $depth          How many levels of the hierarchy are to be included. 0 means all. Default 0.
 * @type string             $theme_location Theme location to be used. Must be registered with register_nav_menu()
 *                                          in order to be selectable by the user.
 * @type bool               $is_disable     If true - menu do not show
 *
 * @return string|null  if  $gm_echo is true then return void (by default)
 */
function groovyMenu( $args = array() ) {

	// Main var with GM block HTML.
	$output_html = '';

	$show_mobile_menu = true;

	global $groovyMenuSettings, $groovyMenuPreview;

	$post_type = GroovyMenuUtils::get_current_page_type();

	if ( ! empty( $post_type ) && $post_type ) {
		$def_val = GroovyMenuUtils::getTaxonomiesPresetByPostType( $post_type );
	}

	if ( ! isset( $args['gm_preset_id'] ) ) {
		if ( ! empty( $def_val['preset'] ) ) {
			$args['gm_preset_id'] = $def_val['preset'];
		}
		$current_preset_id = GroovyMenuSingleMetaPreset::get_preset_id_from_meta();
		if ( $current_preset_id ) {
			$args['gm_preset_id'] = $current_preset_id;
		}
	}

	if ( ! isset( $args['menu'] ) ) {
		if ( ! empty( $def_val['menu'] ) ) {
			$args['menu'] = $def_val['menu'];
		}
		$current_menu_id = GroovyMenuSingleMetaPreset::get_menu_id_from_meta();
		if ( $current_menu_id ) {
			$args['menu'] = $current_menu_id;
		}
	}

	if ( isset( $args['gm_preset_id'] ) && 'none' === $args['gm_preset_id'] ) {
		return null;
	}

	$defaults_args = array(
		'menu'           => GroovyMenuUtils::getMasterNavmenu(),
		'gm_preset_id'   => GroovyMenuUtils::getMasterPreset(),
		'theme_location' => GroovyMenuUtils::getMasterLocation(),
		'echo'           => false,
		'gm_echo'        => true,
		'gm_pre_storage' => false,
		'depth'          => 0, // limit the depth of the nav.
		'is_disable'     => false,
	);

	$args['menu'] =
		( empty( $args['menu'] ) || 'default' === $args['menu'] )
			?
			GroovyMenuUtils::getMasterNavmenu()
			:
			$args['menu'];

	$args['gm_preset_id'] =
		( empty( $args['gm_preset_id'] ) || 'default' === $args['gm_preset_id'] )
			?
			GroovyMenuUtils::getMasterPreset()
			:
			$args['gm_preset_id'];


	// Merge incoming params with defaults.
	$args = wp_parse_args( $args, $defaults_args );

	// We must rewrite some params for excluding issues in design and styles.
	$args['menu_class']           = 'gm-navbar-nav'; // adding custom nav class.
	$args['before']               = ''; // before the menu.
	$args['after']                = ''; // after the menu.
	$args['link_before']          = ''; // before each link.
	$args['link_after']           = ''; // after each link.
	$args['fallback_cb']          = ''; // fallback function (if there is one).
	$args['items_wrap']           = '<ul id="%1$s" class="%2$s">%3$s</ul>';
	$args['walker']               = new \GroovyMenu\FrontendWalker();
	$args['container']            = false;
	$args['groovy_menu']          = true;
	$args['gm_navigation_mobile'] = false;
	$args['echo']                 = false;

	$nav_menu_obj = ! empty( $args['menu'] ) ? wp_get_nav_menu_object( $args['menu'] ) : null;

	if ( $args['menu'] && ! $nav_menu_obj ) {
		$args['menu'] = '';
	} elseif ( $args['menu'] && ! empty( $nav_menu_obj->term_id ) ) {
		$args['menu'] = $nav_menu_obj->term_id;
	}

	$category_options = gm_get_current_category_options();

	if ( $category_options && isset( $category_options['custom_options'] ) && '1' === $category_options['custom_options'] ) {
		$cat_preset   = GroovyMenuCategoryPreset::getCurrentPreset();
		$cat_nav_menu = GroovyMenuCategoryPreset::getCurrentNavMenu();
		if ( $cat_preset ) {
			$args['gm_preset_id'] = $cat_preset;
		}
		if ( $cat_nav_menu ) {
			$nav_menu_obj = ! empty( $cat_nav_menu ) ? wp_get_nav_menu_object( $cat_nav_menu ) : null;
			if ( ! empty( $nav_menu_obj ) && ! empty( $nav_menu_obj->term_id ) ) {
				$args['menu'] = $nav_menu_obj->term_id;
			}
		}
	}

	// Check if GM stored before, if so - return html.
	$current_gm_id = \GroovyMenu\PreStorage::get_instance()->get_id( $args );
	$stored_pre_gm = \GroovyMenu\PreStorage::get_instance()->get_stored_gm_list();
	if ( in_array( $current_gm_id, $stored_pre_gm, true ) ) {
		$stored_gm_data = \GroovyMenu\PreStorage::get_instance()->get_gm( $current_gm_id );
		if ( $stored_gm_data ) {
			if ( $args['gm_echo'] ) {
				echo ( ! empty( $stored_gm_data['gm_html'] ) ) ? $stored_gm_data['gm_html'] : '';

				return null;

			} else {

				return $stored_gm_data['gm_html'];
			}
		}
	}

	$locations     = get_theme_mod( 'nav_menu_locations' );
	$is_menu_empty = false;

	if ( ! $args['menu'] ) {
		if ( empty( $locations[ $args['theme_location'] ] ) ) {
			$is_menu_empty = true;
		} else {
			$nav_menu_obj = wp_get_nav_menu_object( $locations[ $args['theme_location'] ] );
			if ( ! $nav_menu_obj ) {
				$is_menu_empty = true;
			}
		}
	}

	if ( 'default' === $args['gm_preset_id'] ) {
		$args['gm_preset_id'] = null;
	}


	$styles = new GroovyMenuStyle( $args['gm_preset_id'] );


	// Check conditions if need prevent output Groovy Menu.
	$display_gm_when_menu_block_edit = $styles->getGlobal( 'tools', 'display_gm_when_menu_block_edit' );
	if ( ! $display_gm_when_menu_block_edit ) {

		$prevent_gm_output = apply_filters( 'groovy_menu_prevent_output_html', false );

		// Prevent output if self gm_menu_block post_type preview.
		if ( $prevent_gm_output || 'gm_menu_block' === get_post_type() ) {
			return null;
		}
	}


	if ( empty( $groovyMenuSettings ) ) {

		$serialized_styles = $styles->serialize();

		$groovyMenuSettings                         = $serialized_styles;
		$groovyMenuSettings['preset']               = array(
			'id'   => $styles->getPreset()->getId(),
			'name' => $styles->getPreset()->getName(),
		);
		$groovyMenuSettings['nav_menu_data']        = array(
			'id' => $args['menu'],
		);
		$groovyMenuSettings['extra_navbar_classes'] = $styles->getHtmlClasses();
	}

	$preset_id = isset( $groovyMenuSettings['preset']['id'] ) ? $groovyMenuSettings['preset']['id'] : 'all';

	$compiled_css = $styles->get( 'general', 'compiled_css' . ( is_rtl() ? '_rtl' : '' ) );

	$additional_html_class = '';
	if ( ! empty( $groovyMenuSettings['extra_navbar_classes'] ) ) {
		$additional_html_class = ' ' . implode( ' ', $groovyMenuSettings['extra_navbar_classes'] );
	}


	/**
	 * Google Font link building
	 */

	if ( ! empty( $groovyMenuSettings['googleFont'] ) && $groovyMenuSettings['googleFont'] !== 'none' ) {

		$common_font_family   = rawurlencode( $groovyMenuSettings['googleFont'] );
		$common_font_variants = [];
		$common_font_subsets  = [];

		if ( ! empty( $groovyMenuSettings['itemTextWeight'] ) && $groovyMenuSettings['itemTextWeight'] !== 'none' ) {
			array_push( $common_font_variants, $groovyMenuSettings['itemTextWeight'] );
		}

		if ( ! empty( $groovyMenuSettings['mobileItemTextWeight'] ) && $groovyMenuSettings['mobileItemTextWeight'] !== 'none' ) {
			array_push( $common_font_variants, $groovyMenuSettings['mobileItemTextWeight'] );
		}

		if ( ! empty( $groovyMenuSettings['mobileSubitemTextWeight'] ) && $groovyMenuSettings['mobileSubitemTextWeight'] !== 'none' ) {
			array_push( $common_font_variants, $groovyMenuSettings['mobileSubitemTextWeight'] );
		}

		if ( ! empty( $groovyMenuSettings['subLevelItemTextWeight'] ) && $groovyMenuSettings['subLevelItemTextWeight'] !== 'none' ) {
			array_push( $common_font_variants, $groovyMenuSettings['subLevelItemTextWeight'] );
		}

		if ( ! empty( $groovyMenuSettings['megamenuTitleTextWeight'] ) && $groovyMenuSettings['megamenuTitleTextWeight'] !== 'none' ) {
			array_push( $common_font_variants, $groovyMenuSettings['megamenuTitleTextWeight'] );
		}

		if ( ! empty( $common_font_variants ) ) {
			$uniq_common_fonts_variants = array_unique( $common_font_variants );
			$common_font_family         = $common_font_family . ':' . implode( ',', $uniq_common_fonts_variants );
		}

		if ( ! empty( $groovyMenuSettings['itemTextSubset'] ) && $groovyMenuSettings['itemTextSubset'] !== 'none' ) {
			array_push( $common_font_subsets, $groovyMenuSettings['itemTextSubset'] );
		}

		if ( ! empty( $groovyMenuSettings['subLevelItemTextSubset'] ) && $groovyMenuSettings['subLevelItemTextSubset'] !== 'none' ) {
			array_push( $common_font_subsets, $groovyMenuSettings['subLevelItemTextSubset'] );
		}

		if ( ! empty( $groovyMenuSettings['megamenuTitleTextSubset'] ) && $groovyMenuSettings['megamenuTitleTextSubset'] !== 'none' ) {
			array_push( $common_font_subsets, $groovyMenuSettings['megamenuTitleTextSubset'] );
		}

		if ( ! empty( $common_font_variants ) && ! empty( $common_font_subsets ) ) {
			$uniq_common_fonts_subsets = array_unique( $common_font_subsets );
			$common_font_family        = $common_font_family . '&subset=' . implode( ',', $uniq_common_fonts_subsets );
		}

		$output_html .= groovy_menu_add_gfonts_fontface( $preset_id, 'google_font', $common_font_family, ( ! $args['gm_echo'] ) );
	}

	if ( ! empty( $groovyMenuSettings['logoTxtFont'] ) && $groovyMenuSettings['logoTxtFont'] !== 'none' ) {

		$logo_font_family   = rawurlencode( $groovyMenuSettings['logoTxtFont'] );
		$logo_font_variants = [];
		$logo_font_subsets  = [];

		if ( ! empty( $groovyMenuSettings['logoTxtWeight'] ) && $groovyMenuSettings['logoTxtWeight'] !== 'none' ) {
			array_push( $logo_font_variants, $groovyMenuSettings['logoTxtWeight'] );
		}

		if ( ! empty( $groovyMenuSettings['stickyLogoTxtWeight'] ) && $groovyMenuSettings['stickyLogoTxtWeight'] !== 'none' ) {
			array_push( $logo_font_variants, $groovyMenuSettings['stickyLogoTxtWeight'] );
		}

		if ( ! empty( $logo_font_variants ) ) {
			$uniq_logo_fonts_variants = array_unique( $logo_font_variants );
			$logo_font_family         = $logo_font_family . ':' . implode( ',', $uniq_logo_fonts_variants );
		}

		if ( ! empty( $groovyMenuSettings['logoTxtSubset'] ) && $groovyMenuSettings['logoTxtSubset'] !== 'none' ) {
			array_push( $logo_font_subsets, $groovyMenuSettings['logoTxtSubset'] );
		}

		if ( ! empty( $groovyMenuSettings['stickyLogoTxtSubset'] ) && $groovyMenuSettings['stickyLogoTxtSubset'] !== 'none' ) {
			array_push( $logo_font_subsets, $groovyMenuSettings['stickyLogoTxtSubset'] );
		}

		if ( ! empty( $logo_font_variants ) && ! empty( $logo_font_subsets ) ) {
			$uniq_logo_fonts_subsets = array_unique( $logo_font_subsets );
			$logo_font_family        = $logo_font_family . '&subset=' . implode( ',', $uniq_logo_fonts_subsets );
		}

		$output_html .= groovy_menu_add_gfonts_fontface( $preset_id, 'logo_txt_font', $logo_font_family, ( ! $args['gm_echo'] ) );
	}

	$uniqid = 'gm-' . uniqid();

	if ( $args['gm_echo'] ) {
		$output_html .= groovy_menu_js_request( $uniqid, true );
	} else {
		groovy_menu_js_request( $uniqid );
	}

	if ( ! $groovyMenuPreview ) {
		$css_file_params = array(
			'upload_dir'   => GroovyMenuUtils::getUploadDir(),
			'upload_uri'   => GroovyMenuUtils::getUploadUri(),
			'css_filename' => 'preset_' . $preset_id . ( is_rtl() ? '_rtl' : '' ) . '.css',
			'preset_id'    => strval( $preset_id ),
			'preset_key'   => empty( $groovyMenuSettings['presetKey'] ) ? GROOVY_MENU_VERSION : $groovyMenuSettings['presetKey'],
		);

		$groovyMenuSettings['css_file_params'] = $css_file_params;

		$output_html .= groovy_menu_add_preset_style( $preset_id, $compiled_css, $args['gm_echo'] );

		// Custom CSS & JS.
		$custom_css = trim( stripslashes( $styles->get( 'general', 'css' ) ) );
		$custom_js  = trim( stripslashes( $styles->get( 'general', 'js' ) ) );

		if ( $custom_css ) {
			$tag_name     = 'style';
			$output_html .= "\n" . '<' . esc_attr( $tag_name ) . '>' . $custom_css . '</' . esc_attr( $tag_name ) . '>';
		}
		if ( $custom_js ) {
			$tag_name     = 'script';
			$output_html .= "\n" . '<' . esc_attr( $tag_name ) . '>' . $custom_js . '</' . esc_attr( $tag_name ) . '>';
		}
	}

	$wrapper_tag = 'header';
	if ( $groovyMenuSettings['wrapperTag'] !== $wrapper_tag ) {
		$wrapper_tag = esc_attr( $groovyMenuSettings['wrapperTag'] );
	}

	if ( isset( $groovyMenuSettings['mobileNavMenu'] ) && 'none' === $groovyMenuSettings['mobileNavMenu'] ) {
		$show_mobile_menu = false;
	}

	// Clean output, first level.
	ob_start();


	ob_start();
	/**
	 * Fires before the groovy menu output.
	 *
	 * @since 1.2.20
	 */
	do_action( 'gm_before_main_header' );
	$output_html .= ob_get_clean();

	$output_html .= '
	<' . esc_html( $wrapper_tag ) . ' class="gm-navbar gm-preset-id-' . esc_attr( $preset_id ) . esc_attr( $additional_html_class ) . '"
	        id="' . esc_attr( $uniqid ) . '" data-version="' . esc_attr( GROOVY_MENU_VERSION ) . '">
		<div class="gm-wrapper">';

	if ( 'true' === $groovyMenuSettings['header']['toolbar'] ) {

		$socials = array(
			'twitter',
			'facebook',
			'google',
			'vimeo',
			'dribbble',
			'pinterest',
			'youtube',
			'linkedin',
			'instagram',
			'flickr',
			'vk',
		);

		$toolbar_email = '';
		if ( ! empty( $styles->getGlobal( 'toolbar', 'toolbar_email' ) ) ) {
			$toolbar_email = $styles->getGlobal( 'toolbar', 'toolbar_email' );
			$toolbar_email = apply_filters( 'wpml_translate_single_string', $toolbar_email, 'groovy-menu', 'Global settings - toolbar email text' );
		}

		$toolbar_phone = '';
		if ( ! empty( $styles->getGlobal( 'toolbar', 'toolbar_phone' ) ) ) {
			$toolbar_phone = $styles->getGlobal( 'toolbar', 'toolbar_phone' );
			$toolbar_phone = apply_filters( 'wpml_translate_single_string', $toolbar_phone, 'groovy-menu', 'Global settings - toolbar phone text' );
		}

		$output_html .= '
				<div class="gm-toolbar">
					<div class="gm-toolbar-bg"></div>
					<div class="gm-container">
						<div class="gm-toolbar-left">';


		ob_start();
		/**
		 * Fires at the toolbar left as first element output.
		 *
		 * @since 1.8.18
		 */
		do_action( 'gm_toolbar_left_first' );
		$output_html .= ob_get_clean();


		$output_html .= '<div class="gm-toolbar-contacts">';
		if ( ! empty( $toolbar_email ) ) {
			$output_html .= '<span class="gm-toolbar-email">';
			if ( $styles->getGlobal( 'toolbar', 'toolbar_email_icon' ) ) {
				$output_html .= '<span class="' . esc_attr( $styles->getGlobal( 'toolbar', 'toolbar_email_icon' ) ) . '"></span>';
			}
			$output_html .= '<span class="gm-toolbar-contacts__txt">';
			if ( $styles->getGlobal( 'toolbar', 'toolbar_email_as_link' ) ) {
				$output_html .= '<a href="mailto:' . esc_attr( $styles->getGlobal( 'toolbar', 'toolbar_email' ) ) . '">' . esc_attr( $styles->getGlobal( 'toolbar', 'toolbar_email' ) ) . '</a>';
			} else {
				$output_html .= esc_attr( $toolbar_email );
			}
			$output_html .= '</span>';
			$output_html .= '</span>';
		}
		if ( ! empty( $toolbar_phone ) ) {
			$output_html .= '<span class="gm-toolbar-phone">';
			if ( $styles->getGlobal( 'toolbar', 'toolbar_phone_icon' ) ) {
				$output_html .= '<span class="' . esc_attr( $styles->getGlobal( 'toolbar', 'toolbar_phone_icon' ) ) . '"></span>';
			}
			$output_html .= '<span class="gm-toolbar-contacts__txt">';
			if ( $styles->getGlobal( 'toolbar', 'toolbar_phone_as_link' ) ) {
				$output_html .= '<a href="tel:' . esc_attr( $toolbar_phone ) . '">' . esc_attr( $toolbar_phone ) . '</a>';
			} else {
				$output_html .= esc_attr( $toolbar_phone );
			}
			$output_html .= '</span>';
			$output_html .= '</span>';
		}
		$output_html .= '</div>';


		ob_start();
		/**
		 * Fires at the toolbar left as last element output.
		 *
		 * @since 1.8.18
		 */
		do_action( 'gm_toolbar_left_last' );
		$output_html .= ob_get_clean();


		$output_html .= '</div>';
		$output_html .= '<div class="gm-toolbar-right">';


		ob_start();
		/**
		 * Fires at the toolbar right as first element output.
		 *
		 * @since 1.8.18
		 */
		do_action( 'gm_toolbar_right_first' );
		$output_html .= ob_get_clean();


		$output_html .= '<ul class="gm-toolbar-socials-list">';

		$link_attr = '';
		if ( ! empty( $styles->getGlobal( 'social', 'social_set_nofollow' ) ) ) {
			$link_attr .= 'rel="nofollow noopener" ';
		}
		if ( ! empty( $styles->getGlobal( 'social', 'social_set_blank' ) ) ) {
			$link_attr .= 'target="_blank" ';
		}

		foreach ( $socials as $social ) {
			if ( $styles->getGlobal( 'social', 'social_' . $social ) ) {

				$output_html .= '<li class="gm-toolbar-socials-list__item"><a href="' .
					esc_url( $styles->getGlobal( 'social', 'social_' . $social . '_link' ) ) .
					'" class="gm-toolbar-social-link" ' .
					$link_attr .
					'>';

				$icon = $styles->getGlobal( 'social', 'social_' . $social . '_icon' );
				if ( $icon ) {

					$output_html .= '<i class="' . esc_attr( $icon ) . '"></i>';

				} else {

					$output_html .= '<i class="fa fa-' . esc_attr( $social ) . '"></i>';

				}

				$link_text = $styles->getGlobal( 'social', 'social_' . $social . '_text' );
				$link_text = empty( $link_text ) ? '' : trim( $link_text );
				if ( ! empty( $link_text ) ) {
					$output_html .= '<span>' . $link_text . '</span>';
				}

				$output_html .= '</a>';
				$output_html .= '</li>';

			}
		}

		$output_html .= '</ul>';
		if ( $groovyMenuSettings['showWpml'] ) {
			ob_start();
			do_action( 'wpml_add_language_selector' );
			$output_html .= ob_get_clean();
		}


		ob_start();
		/**
		 * Fires at the toolbar right as last element output.
		 *
		 * @since 1.8.18
		 */
		do_action( 'gm_toolbar_right_last' );
		$output_html .= ob_get_clean();


		$output_html .= '</div>';
		$output_html .= '</div>';
		$output_html .= '</div>';
	}
	$output_html .= '<div class="gm-inner">
				<div class="gm-inner-bg"></div>
				<div class="gm-container">';

	$header_style = intval( $groovyMenuSettings['header']['style'] );
	if ( 5 === $header_style ) {
		$output_html .= '<span class="gm-menu-btn--expanded">
						<span class="gm-menu-btn__inner">';

		$menu_icon = 'fa fa-bars';
		if ( ! empty( $styles->getGlobal( 'misc_icons', 'menu_icon' ) ) ) {
			$menu_icon = $styles->getGlobal( 'misc_icons', 'menu_icon' );
		}

		$output_html .= '	<i class="' . esc_attr( $menu_icon ) . '"></i>
					</span>
					</span>';
	}


	$output_html .= '<div class="gm-logo">';


	ob_start();
	/**
	 * Fires before the groovy menu Logo output.
	 *
	 * @since 1.2.20
	 */
	do_action( 'gm_before_logo' );
	$output_html .= ob_get_clean();


	$logo_url = trailingslashit( network_site_url() );
	if ( ! empty( $styles->getGlobal( 'logo', 'logo_url' ) ) ) {
		$logo_url = $styles->getGlobal( 'logo', 'logo_url' );
	} elseif ( defined( 'WPML_PLUGIN_FOLDER' ) && WPML_PLUGIN_FOLDER ) {
		$logo_url = apply_filters( 'wpml_home_url', $logo_url );
	}

	$logo_url_open_type = '';
	if ( ! empty( $styles->getGlobal( 'logo', 'logo_url_open_type' ) ) ) {
		$logo_url_open_type = $styles->getGlobal( 'logo', 'logo_url_open_type' );
		$logo_url_open_type = ( 'same' === $logo_url_open_type ) ? '' : ' target="_blank"';
	}

	if ( 'img' === $groovyMenuSettings['logoType'] ) {

		$logo_arr  = array();
		$logo_html = '';

		$header_style = intval( $groovyMenuSettings['header']['style'] );

		if ( in_array( $header_style, array( 4, 5 ), true ) ) {
			$logo_arr['default'] = $styles->getGlobal( 'logo', 'logo_style_4' );
		} else {
			$logo_arr['default'] = $styles->getGlobal( 'logo', 'logo_default' );
		}

		$logo_arr['alt']               = $styles->getGlobal( 'logo', 'logo_alt' ) ? : $logo_arr['default'];
		$logo_arr['sticky']            = $styles->getGlobal( 'logo', 'logo_sticky' ) ? : $logo_arr['default'];
		$logo_arr['sticky-alt']        = $styles->getGlobal( 'logo', 'logo_sticky_alt' ) ? : $logo_arr['alt'];
		$logo_arr['mobile']            = $styles->getGlobal( 'logo', 'logo_mobile' ) ? : $logo_arr['default'];
		$logo_arr['mobile-alt']        = $styles->getGlobal( 'logo', 'logo_mobile_alt' ) ? : $logo_arr['mobile'];
		$logo_arr['sticky-mobile']     = $styles->getGlobal( 'logo', 'logo_sticky_mobile' ) ? : $logo_arr['mobile'];
		$logo_arr['sticky-alt-mobile'] = $styles->getGlobal( 'logo', 'logo_sticky_alt_mobile' ) ? : $logo_arr['sticky-mobile'];

		if ( $groovyMenuSettings['useAltLogoAtTop'] ) {
			unset( $logo_arr['default'] );
		} else {
			unset( $logo_arr['alt'] );
		}

		if ( $groovyMenuSettings['useAltLogoAtSticky'] ) {
			unset( $logo_arr['sticky'] );
		} else {
			unset( $logo_arr['sticky-alt'] );
		}

		if ( $groovyMenuSettings['useAltLogoAtMobile'] ) {
			unset( $logo_arr['mobile'] );
		} else {
			unset( $logo_arr['mobile-alt'] );
		}

		if ( $groovyMenuSettings['useAltLogoAtStickyMobile'] ) {
			unset( $logo_arr['sticky-mobile'] );
		} else {
			unset( $logo_arr['sticky-alt-mobile'] );
		}

		if ( 'disable-sticky-header' === $groovyMenuSettings['stickyHeader'] ) {
			unset( $logo_arr['sticky'] );
			unset( $logo_arr['sticky-alt'] );
		}
		if ( 'disable-sticky-header' === $groovyMenuSettings['stickyHeaderMobile'] ) {
			unset( $logo_arr['sticky-mobile'] );
			unset( $logo_arr['sticky-alt-mobile'] );
		}

		foreach ( $logo_arr as $key => $attach_id ) {
			if ( ! $attach_id ) {
				continue;
			}

			$img = wp_get_attachment_url( $attach_id );

			if ( ! $img ) {
				continue;
			}

			$img_src    = $img;
			$img_width  = '';
			$img_height = '';

			$filetype = wp_check_filetype( $img );

			if ( ! empty( $filetype['type'] ) ) {
				$img = wp_get_attachment_image_src( $attach_id, 'full' );
				if ( ! empty( $img[0] ) ) {
					$img_src = $img[0];
				}
				if ( ! empty( $img[1] ) ) {
					$img_width = ' width="' . $img[1] . '"';
				}
				if ( ! empty( $img[2] ) ) {
					$img_height = ' height="' . $img[2] . '"';
				}
			}

			$img_src_wpml = esc_url( apply_filters( 'wpml_translate_single_string', $img_src, 'groovy-menu', 'Global settings - Logo image file URL (id:' . $attach_id . ')' ) );
			if ( ! empty( $img_src_wpml ) ) {
				$img_src = $img_src_wpml;
			}

			switch ( $key ) {
				case 'default':
					$additional_class = ( in_array( $header_style, array( 4, 5 ), true ) ) ? 'header-sidebar' : 'default';

					$logo_html .= '<img src="' . $img_src . '"' . $img_width . $img_height . ' class="gm-logo__img gm-logo__img-' . $additional_class . '" alt="" />';
					break;

				default:
					$logo_html .= '<img src="' . $img_src . '"' . $img_width . $img_height . ' class="gm-logo__img gm-logo__img-' . $key . '" alt="" />';
					break;
			}
		}

		if ( $logo_html ) {
			$output_html .= '<a href="' . esc_url( $logo_url ) . '" ' . $logo_url_open_type . '>' . $logo_html . '</a>';
		} else {
			$output_html .= '<span class="gm-logo__no-logo">' . esc_html__( 'Please add image or text logo', 'groovy-menu' ) . '</span>';
		}

	} elseif ( 'text' === $groovyMenuSettings['logoType'] ) {

		$logo_text = '';
		if ( ! empty( $styles->getGlobal( 'logo', 'logo_text' ) ) ) {
			$logo_text = $styles->getGlobal( 'logo', 'logo_text' );
			$logo_text = apply_filters( 'wpml_translate_single_string', $logo_text, 'groovy-menu', 'Global settings - logo text' );
		}

		// Add text logotype.
		$output_html .=
			'<a href="' . esc_url( $logo_url ) . '" ' .
			( ( $logo_url_open_type ) ? $logo_url_open_type : '' ) .
			'><span class="gm-logo__txt">' . esc_html( $logo_text ) . '</span></a>';

	}


	ob_start();
	/**
	 * Fires after the groovy menu Logo output.
	 *
	 * @since 1.2.20
	 */
	do_action( 'gm_after_logo' );
	$output_html .= ob_get_clean();


	$output_html .= '</div>';
	$output_html .= '<span class="gm-menu-btn">
						<span class="gm-menu-btn__inner">';

	$menu_icon = 'fa fa-bars';
	if ( ! empty( $styles->getGlobal( 'misc_icons', 'menu_icon' ) ) ) {
		$menu_icon = $styles->getGlobal( 'misc_icons', 'menu_icon' );
	}

	$output_html .= '	<i class="' . esc_attr( $menu_icon ) . '"></i>
					</span>
					</span>';


	ob_start();
	/**
	 * Fires before the groovy main menu wrapper.
	 *
	 * @since 2.2.0
	 */
	do_action( 'gm_before_main_menu_nav' );
	$output_html .= ob_get_clean();


	$output_html .= '<div class="gm-main-menu-wrapper">
						<nav id="gm-main-menu">';


	ob_start();
	/**
	 * Fires at the main menu nav.
	 *
	 * @since 1.9.5
	 */
	do_action( 'gm_main_menu_nav_first' );
	$output_html .= ob_get_clean();


	$output_html .= wp_nav_menu( $args );


	if ( $is_menu_empty ) {
		$output_html .= '<div class="gm-menu-empty">' . esc_html__( 'Please assign a menu to the primary menu location under Menus.', 'groovy-menu' ) . '</div>';
	}

	ob_start();
	/**
	 * Fires at the main menu nav.
	 *
	 * @since 1.9.5
	 */
	do_action( 'gm_main_menu_nav_last' );
	$output_html .= ob_get_clean();


	$output_html .= '</nav>';


	ob_start();
	/**
	 * Fires after main menu nav.
	 *
	 * @since 1.9.5
	 */
	do_action( 'gm_after_main_menu_nav' );
	$output_html .= ob_get_clean();


	$show_gm_action = false;

	$searchForm = $groovyMenuSettings['searchForm'];
	if ( 'disable' !== $searchForm ) {
		$show_gm_action = true;
	}

	if ( ! gm_get_shop_is_catalog() && $groovyMenuSettings['woocommerceCart'] && class_exists( 'WooCommerce' ) ) {
		$show_gm_action = true;
	}

	if ( $show_gm_action ) {

		$output_html .= '<div class="gm-actions">';


		ob_start();
		/**
		 * Fires as first groovy menu action buttons.
		 *
		 * @since 2.2.0
		 */
		do_action( 'gm_main_menu_actions_button_first' );
		$output_html .= ob_get_clean();


		if ( $styles->get( 'general', 'show_divider' ) ) {
			$header_style = $styles->get( 'general', 'header' );
			if ( isset( $header_style['style'] ) && 1 === $header_style['style'] ) {
				$output_html .= '<span class="gm-nav-inline-divider"></span>';
			}
		}

		if ( 'disable' !== $searchForm ) {

			$searchIcon = 'gmi gmi-zoom-search';
			if ( $styles->getGlobal( 'misc_icons', 'search_icon' ) ) {
				$searchIcon = $styles->getGlobal( 'misc_icons', 'search_icon' );
			}

			if ( method_exists( 'GroovyMenuUtils', 'getSearchBlock' ) ) {
				$output_html .= GroovyMenuUtils::getSearchBlock( $searchIcon );
			}


		}

		if ( ! gm_get_shop_is_catalog() && $groovyMenuSettings['woocommerceCart'] && class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_id' ) ) {
			global $woocommerce;

			$qty = 0;
			if ( $woocommerce && isset( $woocommerce->cart ) ) {
				$qty = $woocommerce->cart->get_cart_contents_count();
			}
			$cartIcon = 'gmi gmi-bag';
			if ( $styles->getGlobal( 'misc_icons', 'cart_icon' ) ) {
				$cartIcon = $styles->getGlobal( 'misc_icons', 'cart_icon' );
			}

			$output_html .= '<div class="gm-minicart gm-dropdown">';

			$output_html .= '<a href="' . get_permalink( wc_get_page_id( 'cart' ) ) . '"
										   class="gm-minicart-link">
											<div class="gm-minicart-icon-wrapper">
												<i class="' . esc_attr( $cartIcon ) . '"></i>
												<span class="gm-minicart__txt">'
			                . esc_html__( 'My cart', 'groovy-menu' ) .
			                '</span>'
			                . groovy_menu_woocommerce_mini_cart_counter( $qty ) .
			                '</div>
										</a>';

			$output_html .= '<div class="gm-dropdown-menu gm-minicart-dropdown">
											<div class="widget_shopping_cart_content">';

			if ( $woocommerce && isset( $woocommerce->cart ) ) {
				ob_start();

				$template_mini_cart_path = get_stylesheet_directory() . '/woocommerce/cart/mini-cart.php';
				if ( file_exists( $template_mini_cart_path ) && is_file( $template_mini_cart_path ) ) {
					include $template_mini_cart_path;
				} elseif ( defined( 'WC_PLUGIN_FILE' ) ) {
					$original_mini_cart_path = dirname( WC_PLUGIN_FILE ) . '/templates/cart/mini-cart.php';
					if ( file_exists( $original_mini_cart_path ) && is_file( $original_mini_cart_path ) ) {
						$args['list_class'] = '';
						include $original_mini_cart_path;
					}
				}

				$output_html .= ob_get_clean();
			}


			$output_html .= '
											</div>
										</div>
									</div>
									';
		}


		ob_start();
		/**
		 * Fires as last groovy menu action buttons.
		 *
		 * @since 2.2.0
		 */
		do_action( 'gm_main_menu_actions_button_last' );
		$output_html .= ob_get_clean();


		$output_html .= '</div>';
	}
	$output_html .= '</div>
				</div>
			</div>
		</div>
		<div class="gm-padding"></div>
	</' . esc_html( $wrapper_tag ) . '>';

	// ------------------------------------------------------------------------------------------- mobile menu --------.
	if ( $show_mobile_menu ) {

		$custom_css_class = $styles->getCustomHtmlClass();

		$output_html .= '<aside class="gm-navigation-drawer gm-navigation-drawer--mobile gm-hidden';
		if ( $custom_css_class ) {
			$output_html .= ' ' . esc_attr( $custom_css_class );
		}
		$output_html .= '">';
		$output_html .= '<div class="gm-grid-container d-flex flex-column h-100">
			<div>';

		$args['gm_navigation_mobile'] = true;

		if ( isset( $groovyMenuSettings['mobileNavMenu'] ) && is_numeric( $groovyMenuSettings['mobileNavMenu'] ) ) {
			// Re-assign nav_menu for the mobile view.
			$args['menu'] = intval( $groovyMenuSettings['mobileNavMenu'] );
		}


		ob_start();
		/**
		 * Fires at the mobile main menu nav.
		 *
		 * @since 1.9.5
		 */
		do_action( 'gm_mobile_main_menu_nav_first' );
		$output_html .= ob_get_clean();


		$output_html .= wp_nav_menu( $args );


		ob_start();
		/**
		 * Fires at the mobile main menu nav.
		 *
		 * @since 1.9.5
		 */
		do_action( 'gm_mobile_main_menu_nav_last' );
		$output_html .= ob_get_clean();


		$output_html .= '</div>';
		$output_html .= '<div class="flex-grow-1"></div>';


		ob_start();
		/**
		 * Fires after main menu nav for mobile.
		 *
		 * @since 1.9.5
		 */
		do_action( 'gm_mobile_after_main_menu_nav' );
		$output_html .= ob_get_clean();


		$output_html .= '<div class="d-flex justify-content-center align-items-center text-center mb-4 mt-5">';

		$searchForm = $groovyMenuSettings['searchForm'];
		$searchIcon = 'gmi gmi-zoom-search';
		if ( $styles->getGlobal( 'misc_icons', 'search_icon' ) ) {
			$searchIcon = $styles->getGlobal( 'misc_icons', 'search_icon' );
		}

		if ( 'disable' !== $searchForm ) {

			$isFullScreen   = false;
			$isSearchCustom = false;

			if ( 'custom' === $searchForm ) {
				$isSearchCustom = true;
			}

			$searchFormCustomWrapper = isset( $groovyMenuSettings['searchFormCustomWrapper'] ) ? $groovyMenuSettings['searchFormCustomWrapper'] : 'fullscreen';
			if ( 'fullscreen' === $searchForm || ( $isSearchCustom && 'dropdown' !== $searchFormCustomWrapper ) ) {
				$isFullScreen = 'fullscreen';
			}


			ob_start();
			/**
			 * Fires before groovy menu mobile search icon.
			 *
			 * @since 2.2.0
			 */
			do_action( 'gm_mobile_before_search_icon' );
			$output_html .= ob_get_clean();


			$output_html .= '<div class="gm-search ' . ( $isFullScreen ? 'fullscreen' : 'gm-dropdown' ) . '">
						<i class="gm-icon ' . esc_attr( $searchIcon ) . '"></i>
						<span class="gm-search__txt">'
			                . esc_html__( 'Search', 'groovy-menu' ) .
			                '</span>
					</div>';

		}
		$output_html .= '<div class="gm-divider--vertical mx-4"></div>';
		if ( ! gm_get_shop_is_catalog() && $groovyMenuSettings['woocommerceCart'] && class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_id' ) ) {
			global $woocommerce;

			$qty = 0;
			if ( $woocommerce && isset( $woocommerce->cart ) ) {
				$qty = $woocommerce->cart->get_cart_contents_count();
			}
			$cartIcon = 'gmi gmi-bag';
			if ( $styles->getGlobal( 'misc_icons', 'cart_icon' ) ) {
				$cartIcon = $styles->getGlobal( 'misc_icons', 'cart_icon' );
			}


			ob_start();
			/**
			 * Fires before groovy menu mobile minicart.
			 *
			 * @since 2.2.0
			 */
			do_action( 'gm_mobile_before_minicart' );
			$output_html .= ob_get_clean();


			$output_html .= '
					<div class="gm-minicart">
						<a href="' . get_permalink( wc_get_page_id( 'cart' ) ) . '" class="gm-minicart-link">
							<div class="gm-badge">' . groovy_menu_woocommerce_mini_cart_counter( $qty ) . '</div>
							<i class="gm-icon ' . esc_attr( $cartIcon ) . '"></i>
							<span class="gm-minicart__txt">'
			                . esc_html__( 'My cart', 'groovy-menu' ) .
			                '</span>
						</a>
					</div>
					';
		}


		ob_start();
		/**
		 * Fires at the groovy menu mobile toolbar end.
		 *
		 * @since 2.2.0
		 */
		do_action( 'gm_mobile_toolbar_end' );
		$output_html .= ob_get_clean();


		$output_html .= '</div>';
		$output_html .= '</div>';
		$output_html .= '</aside>';
	} // end of if $show_mobile_menu.

	ob_start();
	/**
	 * Fires after the groovy menu output.
	 *
	 * @since 1.2.20
	 */
	do_action( 'gm_after_main_header' );
	$output_html .= ob_get_clean();


	// Required if the action returns unexpected content.
	// Clean output.
	$final = ob_get_clean();


	if ( $args['gm_echo'] ) {
		echo ( ! empty( $output_html ) ) ? $output_html : '';
	} else {
		return $output_html;
	}

	return null;

}

/**
 * Alias for function groovyMenu(). Displays a navigation menu.
 *
 * @param array             $args           {
 *                                          Optional. Array of nav menu arguments.
 *
 * @type int|string|WP_Term $menu           Desired menu. Accepts (matching in order) id, slug, name, menu object. Default empty.
 * @type string             $menu_class     CSS class to use for the ul element which forms the menu.Default is 'gm-navbar-nav'
 * @type string             $gm_preset_id   Groovy menu preset id.
 * @type bool               $echo           Whether to echo the menu or return it. Default true.
 * @type int                $depth          How many levels of the hierarchy are to be included. 0 means all. Default 0.
 * @type string             $theme_location Theme location to be used. Must be registered with register_nav_menu()
 *                                          in order to be selectable by the user.
 *
 * @return string|void  if  $echo is true then return void (by default)
 *
 */
function gm_wp_nav_menu( $args = array() ) {
	return groovyMenu( $args );
}


/**
 * Alias for function groovyMenu(). Displays a navigation menu.
 *
 * @param array             $args           {
 *                                          Optional. Array of nav menu arguments.
 *
 * @type int|string|WP_Term $menu           Desired menu. Accepts (matching in order) id, slug, name, menu object. Default empty.
 * @type string             $menu_class     CSS class to use for the ul element which forms the menu.Default is 'gm-navbar-nav'
 * @type string             $gm_preset_id   Groovy menu preset id.
 * @type bool               $echo           Whether to echo the menu or return it. Default true.
 * @type int                $depth          How many levels of the hierarchy are to be included. 0 means all. Default 0.
 * @type string             $theme_location Theme location to be used. Must be registered with register_nav_menu()
 *                                          in order to be selectable by the user.
 *
 * @return string|void  if  $echo is true then return void (by default)
 *
 */
function groovy_menu( $args = array() ) {
	return groovyMenu( $args );
}

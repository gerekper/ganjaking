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
 *                                          }
 *
 * @return string  if $gm_echo is true then return empty string (by default)
 */
function groovyMenu( $args = array() ) {

	if ( ! is_array( $args ) ) {
		$args = array();
	}

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
		return '';
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

				return '';

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

	if ( 'none' === $args['gm_preset_id'] ) {
		return '';
	}


	$styles = new GroovyMenuStyle( $args['gm_preset_id'] );


	// Check conditions if need prevent output Groovy Menu.
	$display_gm_when_menu_block_edit = $styles->getGlobal( 'tools', 'display_gm_when_menu_block_edit' );
	if ( ! $display_gm_when_menu_block_edit ) {

		$prevent_gm_output = apply_filters( 'groovy_menu_prevent_output_html', false );

		// Prevent output if self gm_menu_block post_type preview.
		if ( $prevent_gm_output || 'gm_menu_block' === get_post_type() ) {
			return '';
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
	if ( ! empty( $groovyMenuSettings['mobileDisableDesktop'] ) && $groovyMenuSettings['mobileDisableDesktop'] ) {
		$additional_html_class .= ' gm-disable-desktop-view';
	}
	if ( ! empty( $groovyMenuSettings['extra_navbar_classes'] ) ) {
		$additional_html_class .= ' ' . implode( ' ', $groovyMenuSettings['extra_navbar_classes'] );
	}

	$header_style = intval( $groovyMenuSettings['header']['style'] );

	if ( class_exists( 'GroovyMenuActions' ) ) {
		// Do custom shortcodes from preset.
		GroovyMenuActions::do_preset_shortcodes( $styles );

		if ( $groovyMenuSettings['toolbarMenuEnable'] ) {
			// Do custom shortcodes from preset.
			GroovyMenuActions::check_toolbar_menu( $styles );
		}

		if ( in_array( $header_style, [ 1, 2 ], true ) ) {
			// Do custom shortcodes from preset.
			GroovyMenuActions::check_menu_block_for_actions( $styles );
		}
	}


	if ( method_exists( 'GroovyMenuUtils', 'enquare_styles_recompile' ) ) {
		GroovyMenuUtils::enquare_styles_recompile( $compiled_css, $groovyMenuSettings['version'] );
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

	$uniqid = empty( $groovyMenuSettings['gm-uniqid'][ $args['gm_preset_id'] ] ) ? 'gm-' . uniqid() : $groovyMenuSettings['gm-uniqid'][ $args['gm_preset_id'] ];

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
			$tag_name    = 'style';
			$output_html .= "\n" . '<' . esc_attr( $tag_name ) . '>' . $custom_css . '</' . esc_attr( $tag_name ) . '>';
		}
		if ( $custom_js ) {
			$tag_name    = 'script';
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

	$searchForm = $groovyMenuSettings['searchForm'];

	$menu_button_text = $styles->getGlobal( 'misc_icons', 'menu_button_text' );
	$menu_button_text = apply_filters( 'wpml_translate_single_string', $menu_button_text, 'groovy-menu', 'Global settings - Menu button text' );


	// prepare for Second sidebar hamburger html.
	$second_sidebar_burger = array(
		'main_bar_left'                 => '',
		'main_bar_right'                => '',
		'main_bar_before_logo'          => '',
		'main_bar_after_logo'           => '',
		'main_bar_before_main_menu'     => '',
		'main_bar_after_main_menu'      => '',
		'main_bar_before_action_button' => '',
	);

	if (
		1 === $header_style &&
		$groovyMenuSettings['secondSidebarMenuEnable'] &&
		! empty( $groovyMenuSettings['secondSidebarMenuSideIconPosition'] ) &&
		isset( $second_sidebar_burger[ $groovyMenuSettings['secondSidebarMenuSideIconPosition'] ] )
	) {
		$menu_second_button_text_full = '';
		if ( $groovyMenuSettings['secondSidebarMenuButtonShowText'] ) {
			$menu_second_button_text_full = '<span class="gm-menu-btn--text" >' . $menu_button_text . '</span >';
		}
		$second_sidebar_burger_html = '<div class="gm-menu-btn-second gm-burger hamburger">' . $menu_second_button_text_full . '<div class="hamburger-box"><div class="hamburger-inner"></div></div></div>';

		$second_sidebar_burger[ $groovyMenuSettings['secondSidebarMenuSideIconPosition'] ] = $second_sidebar_burger_html;
	}


	// Clean output, first level --------------------------------------------------------------------------------------.
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

		$toolbar_type      = isset( $groovyMenuSettings['toolbarType'] ) ? $groovyMenuSettings['toolbarType'] : 'default';
		$toolbar_custom_id = isset( $groovyMenuSettings['toolbarCustomId'] ) ? intval( $groovyMenuSettings['toolbarCustomId'] ) : 0;

		$output_html .= '
				<div class="gm-toolbar" id="gm-toolbar">
					<div class="gm-toolbar-bg"></div>';

		if ( 'menublock' === $toolbar_type && ! empty( $toolbar_custom_id ) ) {
			$menu_block_helper  = new \GroovyMenu\WalkerNavMenu();
			$menu_block_content = $menu_block_helper->getMenuBlockPostContent( $toolbar_custom_id );
			if ( function_exists( 'groovy_menu_add_custom_styles' ) ) {
				groovy_menu_add_custom_styles( $toolbar_custom_id );
			}
			if ( function_exists( 'groovy_menu_add_custom_styles_support' ) ) {
				groovy_menu_add_custom_styles_support( $toolbar_custom_id );
			}

			$output_html .= '<div class="gm-container gm-block-container">';

			$output_html .= $menu_block_content;
		} else {

			$output_html .= '<div class="gm-container">';
			$output_html .= '<div class="gm-toolbar-left">';

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


			$output_html .= '</div>'; // .gm-toolbar-left
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
					                ' aria-label="'. $social . '"'.
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


			$output_html .= '</div>'; // .gm-toolbar-right

		}

		$output_html .= '</div>'; // .gm-container
		$output_html .= '</div>'; // #gm-toolbar.gm-toolbar
	}
	$output_html .= '<div class="gm-inner">
				<div class="gm-inner-bg"></div>
				<div class="gm-container">';

	if ( 5 === $header_style ) {
		$output_html .= '<div class="gm-menu-btn--expanded hamburger"><div class="hamburger-box"><div class="hamburger-inner"></div></div></div>';
	}


	$output_html .= GroovyMenuUtils::clean_output( $second_sidebar_burger['main_bar_left'] );


	$output_html .= '<div class="gm-logo">';


	ob_start();
	/**
	 * Fires before the groovy menu Logo output.
	 *
	 * @since 1.2.20
	 */
	do_action( 'gm_before_logo' );
	$output_html .= ob_get_clean();


	$output_html .= GroovyMenuUtils::clean_output( $second_sidebar_burger['main_bar_before_logo'] );


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

		if ( 5 === $header_style && $groovyMenuSettings['sidebarExpandingMenuSecondLogoEnable'] ) {
			if ( ! empty( $groovyMenuSettings['sidebarExpandingMenuSecondLogo'] ) ) {
				$logo_arr['expanded'] = $styles->getGlobal( 'logo', $groovyMenuSettings['sidebarExpandingMenuSecondLogo'] ) ? : $logo_arr['default'];
			}
		}

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
			$img_alt    = '';

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

			// Image Alt attribute.
			if ( $groovyMenuSettings['logoShowAlt'] ) {
				$img_alt = $groovyMenuSettings['logoShowTitleAsAlt'] ? get_the_title( $attach_id ) : get_post_meta( $attach_id, '_wp_attachment_image_alt', true );
				$img_alt = esc_attr( $img_alt );
			}

			// Filter for WPML logo image SRC changes.
			$img_src_wpml = esc_url( apply_filters( 'wpml_translate_single_string', $img_src, 'groovy-menu', 'Global settings - Logo image file URL (id:' . $attach_id . ')' ) );
			if ( ! empty( $img_src_wpml ) ) {
				$img_src = $img_src_wpml;
			}


			/**
			 * Can change logo image src by key.
			 *
			 * @param string $img_src   Full source URL for logo image.
			 * @param string $key       Logo image key. Possible keys:
			 *                          'default', 'alt', 'sticky', 'sticky-alt', 'mobile', 'mobile-alt', 'sticky-mobile', 'sticky-alt-mobile'.
			 * @param string $attach_id id by WP Media Library.
			 *
			 * @since 2.4.4
			 */
			$img_src = apply_filters( 'gm_logo_change_src_by_key', $img_src, $key, $attach_id );

			/**
			 * Can change logo image alt by key.
			 *
			 * @param string $img_src   Full source URL for logo image.
			 * @param string $key       Logo image key. Possible keys:
			 *                          'default', 'alt', 'sticky', 'sticky-alt', 'mobile', 'mobile-alt', 'sticky-mobile', 'sticky-alt-mobile'.
			 * @param string $attach_id id by WP Media Library.
			 *
			 * @since 2.4.4
			 */
			$img_alt = apply_filters( 'gm_logo_change_alt_by_key', $img_alt, $key, $attach_id );


			switch ( $key ) {
				case 'default':
					$additional_class = ( in_array( $header_style, array( 4, 5 ), true ) ) ? 'header-sidebar' : 'default';

					$logo_html .= '<img src="' . $img_src . '"' . $img_width . $img_height . ' class="gm-logo__img gm-logo__img-' . $additional_class . '" alt="' . $img_alt . '" />';
					break;

				default:
					$logo_html .= '<img src="' . $img_src . '"' . $img_width . $img_height . ' class="gm-logo__img gm-logo__img-' . $key . '" alt="' . $img_alt . '" />';
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


	$output_html .= GroovyMenuUtils::clean_output( $second_sidebar_burger['main_bar_after_logo'] );


	ob_start();
	/**
	 * Fires after the groovy menu Logo output.
	 *
	 * @since 1.2.20
	 */
	do_action( 'gm_after_logo' );
	$output_html .= ob_get_clean();


	$output_html .= '</div>';


	$mobile_woo_icon_html    = '';
	$mobile_search_icon_html = '';

	// Woocomerce minicart for mobile top bar & minimalistic.
	if (
		! gm_get_shop_is_catalog() &&
		$groovyMenuSettings['woocommerceCart'] &&
		class_exists( 'WooCommerce' ) &&
		function_exists( 'wc_get_page_id' ) &&
		(
			(
				! empty( $groovyMenuSettings['woocommerceIconPositionMobile'] ) &&
				in_array( $groovyMenuSettings['woocommerceIconPositionMobile'], array(
					'topbar',
					'topbar_slideBottom',
				), true )
			) || (
				2 === $header_style &&
				! empty( $groovyMenuSettings['minimalisticMenuWooIconPosition'] ) &&
				in_array( $groovyMenuSettings['minimalisticMenuWooIconPosition'], array(
					'topbar',
					'topbar_slideBottom',
				), true )
			)
		)
	) {
		global $woocommerce;

		$qty = 0;
		if ( $woocommerce && isset( $woocommerce->cart ) ) {
			$qty = $woocommerce->cart->get_cart_contents_count();
		}
		$cartIcon = 'gmi gmi-bag';
		if ( $styles->getGlobal( 'misc_icons', 'cart_icon' ) ) {
			$cartIcon = $styles->getGlobal( 'misc_icons', 'cart_icon' );
		}

		$mobile_woo_icon_html .= '
					<div class="gm-menu-action-btn gm-minicart">
						<a href="' . get_permalink( wc_get_page_id( 'cart' ) ) . '" class="gm-minicart-link">
							<div class="gm-badge">' . groovy_menu_woocommerce_mini_cart_counter( $qty ) . '</div>
							<i class="gm-icon ' . esc_attr( $cartIcon ) . '"></i>
						</a>
					</div>';
	}

	// Search icon for mobile top bar & minimalistic.
	if (
		'disable' !== $searchForm &&
		(
			(
				! empty( $groovyMenuSettings['searchFormIconPositionMobile'] ) &&
				in_array( $groovyMenuSettings['searchFormIconPositionMobile'], array(
					'topbar',
					'topbar_slideBottom',
				), true )
			) || (
				2 === $header_style &&
				! empty( $groovyMenuSettings['minimalisticMenuSearchIconPosition'] ) &&
				in_array( $groovyMenuSettings['minimalisticMenuSearchIconPosition'], array(
					'topbar',
					'topbar_slideBottom',
				), true )
			)
		)
	) {
		$searchIcon = 'gmi gmi-zoom-search';
		if ( $styles->getGlobal( 'misc_icons', 'search_icon' ) ) {
			$searchIcon = $styles->getGlobal( 'misc_icons', 'search_icon' );
		}

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
		$mobile_search_icon_html .= ob_get_clean();


		$mobile_search_icon_html .= '<div class="gm-search ' . ( $isFullScreen ? 'fullscreen' : 'gm-dropdown' ) . '">
						<i class="gm-icon ' . esc_attr( $searchIcon ) . '"></i>
						<span class="gm-search__txt">'
		                            . esc_html__( 'Search', 'groovy-menu' ) .
		                            '</span>
					</div>';
	}

	ob_start();
	/**
	 * Fires as first groovy menu action buttons.
	 *
	 * @since 2.2.0
	 */
	do_action( 'gm_main_menu_actions_button_first' );
	$gm_main_menu_actions_button_first = ob_get_clean();


	ob_start();
	/**
	 * Fires as last groovy menu action buttons.
	 *
	 * @since 2.2.0
	 */
	do_action( 'gm_main_menu_actions_button_last' );
	$gm_main_menu_actions_button_last = ob_get_clean();


	if ( ! empty( $mobile_woo_icon_html ) || ! empty( $mobile_search_icon_html ) || ! empty( $gm_main_menu_actions_button_first ) || ! empty( $gm_main_menu_actions_button_last ) ) {
		$output_html .= '<div class="gm-menu-actions-wrapper">' . $gm_main_menu_actions_button_first . $mobile_search_icon_html . $mobile_woo_icon_html . $gm_main_menu_actions_button_last . '</div>';
	}


	if ( $groovyMenuSettings['mobileCustomHamburger'] ) {


		if ( $groovyMenuSettings['mobileCustomHamburger'] ) {
			ob_start();
			/**
			 * Fires for custom mobile hamburger.
			 *
			 * @since 2.4.11
			 */
			do_action( 'gm_custom_mobile_hamburger' );
			$output_html .= ob_get_clean();
		}


	} else {


		ob_start();
		/**
		 * Fires before mobile hamburger.
		 *
		 * @since 2.4.11
		 */
		do_action( 'gm_before_mobile_hamburger' );
		$output_html .= ob_get_clean();

		$menu_button_text_full        = '';
		$menu_second_button_text_full = '';
		if ( $groovyMenuSettings['mobileMenuButtonShowText'] || 2 === $header_style ) {
			$menu_button_text_full = '<span class="gm-menu-btn--text" >' . $menu_button_text . '</span >';
		}

		if ( 2 === $header_style && $groovyMenuSettings['minimalisticCssHamburger'] ) {

			$output_html .= '<div class="gm-menu-btn gm-burger hamburger">' . $menu_button_text_full . '<div class="hamburger-box"><div class="hamburger-inner"></div></div></div>';

		} elseif ( 2 !== $header_style && $groovyMenuSettings['mobileIndependentCssHamburger'] ) {

			$output_html .= '<div class="gm-menu-btn gm-burger hamburger">' . $menu_button_text_full . '<div class="hamburger-box"><div class="hamburger-inner"></div></div></div>';

		} else {
			$output_html .= '<span class="gm-menu-btn">';
			$output_html .= $menu_button_text_full;
			$output_html .= '	<span class="gm-menu-btn__inner">';

			$menu_icon = 'fa fa-bars';
			if ( ! empty( $styles->getGlobal( 'misc_icons', 'menu_icon' ) ) ) {
				$menu_icon = $styles->getGlobal( 'misc_icons', 'menu_icon' );
			}
			$output_html .= '	<i class="' . esc_attr( $menu_icon ) . '"></i>';
			$output_html .= '	</span>';
			$output_html .= '</span>';
		}

		ob_start();
		/**
		 * Fires after mobile hamburger.
		 *
		 * @since 2.4.11
		 */
		do_action( 'gm_after_mobile_hamburger' );
		$output_html .= ob_get_clean();


	}


	ob_start();
	/**
	 * Fires before the groovy main menu wrapper.
	 *
	 * @since 2.2.0
	 */
	do_action( 'gm_before_main_menu_nav' );
	$output_html .= ob_get_clean();


	$output_html .= '<div class="gm-main-menu-wrapper">';

	if ( 2 === $header_style && $groovyMenuSettings['minimalisticMenuFullscreen'] && ! $groovyMenuSettings['minimalisticMenuShowCloseButton'] ) {
		$output_html .= '<span class="gm-fullscreen-close" aria-label="close"><svg height="32" width="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
    <path fill-rule="evenodd" d="M 16 32 C 7.16 32 0 24.84 0 16 C 0 7.16 7.16 0 16 0 C 24.84 0 32 7.16 32 16 C 32 24.84 24.84 32 16 32 Z M 16 2 C 8.27 2 2 8.27 2 16 C 2 23.73 8.27 30 16 30 C 23.73 30 30 23.73 30 16 C 30 8.27 23.73 2 16 2 Z M 17.35 16 C 17.35 16 20.71 19.37 20.71 19.37 C 21.09 19.74 21.09 20.34 20.71 20.71 C 20.34 21.09 19.74 21.09 19.37 20.71 C 19.37 20.71 16 17.35 16 17.35 C 16 17.35 12.63 20.71 12.63 20.71 C 12.26 21.09 11.66 21.09 11.29 20.71 C 10.91 20.34 10.91 19.74 11.29 19.37 C 11.29 19.37 14.65 16 14.65 16 C 14.65 16 11.29 12.63 11.29 12.63 C 10.91 12.26 10.91 11.66 11.29 11.29 C 11.66 10.91 12.26 10.91 12.63 11.29 C 12.63 11.29 16 14.65 16 14.65 C 16 14.65 19.37 11.29 19.37 11.29 C 19.74 10.91 20.34 10.91 20.71 11.29 C 21.09 11.66 21.09 12.26 20.71 12.63 C 20.71 12.63 17.35 16 17.35 16 Z" />
</svg></span>';
	}

	if ( 2 === $header_style && $groovyMenuSettings['minimalisticMenuShowCloseButton'] ) {
		$output_html .= '<div class="gm-menu-btn-close-drawer">';
		if ( $groovyMenuSettings['minimalisticCssHamburger'] ) {

			$output_html .= '<div class="hamburger is-active ' . $groovyMenuSettings['minimalisticCssHamburgerType'] . '"><div class="hamburger-box"><div class="hamburger-inner"></div></div></div>';

		} else {
			$output_html .= '<span class="gm-menu-btn">';
			$output_html .= '	<span class="gm-menu-btn__inner">';

			$menu_icon = 'fa fa-bars';
			if ( ! empty( $styles->getGlobal( 'misc_icons', 'close_icon' ) ) ) {
				$menu_icon = $styles->getGlobal( 'misc_icons', 'close_icon' );
			}
			$output_html .= '	<i class="' . esc_attr( $menu_icon ) . '"></i>';
			$output_html .= '	</span>';
			$output_html .= '</span>';
		}
		$output_html .= '</div>';
	}

	$output_html .= '<nav id="gm-main-menu">';


	ob_start();
	/**
	 * Fires at the main menu nav.
	 *
	 * @since 1.9.5
	 */
	do_action( 'gm_main_menu_nav_first' );
	$output_html .= ob_get_clean();


	$output_html .= GroovyMenuUtils::clean_output( $second_sidebar_burger['main_bar_before_main_menu'] );


	$output_html .= wp_nav_menu( $args );


	if ( $is_menu_empty ) {
		$output_html .= '<div class="gm-menu-empty">' . esc_html__( 'Please assign a menu to the primary menu location under Menus.', 'groovy-menu' ) . '</div>';
	}


	$output_html .= GroovyMenuUtils::clean_output( $second_sidebar_burger['main_bar_after_main_menu'] );


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
	$expand_space   = false;

	if ( 'disable' !== $searchForm ) {
		$show_gm_action = true;
	}

	if ( ! gm_get_shop_is_catalog() && $groovyMenuSettings['woocommerceCart'] && class_exists( 'WooCommerce' ) ) {
		$show_gm_action = true;
	}

	if ( in_array( $header_style, [ 3, 5 ], true ) && ! $show_gm_action ) {
		$show_gm_action = true;
		$expand_space   = true;
	}

	if ( $show_gm_action ) {

		$output_html .= '<div class="gm-actions">';

		$output_html .= $gm_main_menu_actions_button_first;

		$output_html .= GroovyMenuUtils::clean_output( $second_sidebar_burger['main_bar_before_action_button'] );


		if ( $styles->get( 'general', 'show_divider' ) ) {
			if ( 1 === $header_style ) {
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

			$woo_cart_dropdown = $groovyMenuSettings['wooCartDisableDropdown'] ? '' : ' gm-dropdown';

			$output_html .= '<div class="gm-minicart' . $woo_cart_dropdown . '">';

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

				$template_mini_cart_path = str_replace( array(
					'\\',
					'/'
				), DIRECTORY_SEPARATOR, get_stylesheet_directory() . '/woocommerce/cart/mini-cart.php' );

				if ( file_exists( $template_mini_cart_path ) && is_file( $template_mini_cart_path ) ) {
					include $template_mini_cart_path;
				} elseif ( defined( 'WC_PLUGIN_FILE' ) ) {
					$original_mini_cart_path = str_replace( array(
						'\\',
						'/'
					), DIRECTORY_SEPARATOR, dirname( WC_PLUGIN_FILE ) . '/templates/cart/mini-cart.php' );

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


		$output_html .= $gm_main_menu_actions_button_last;


		if ( $expand_space ) {
			$output_html .= '<div class="gm-expand-space"></div>';
		}

		$output_html .= '</div>';
	}

	$output_html .= GroovyMenuUtils::clean_output( $second_sidebar_burger['main_bar_right'] );

	$output_html .= '</div>
				</div>
			</div>
		</div>
		<div class="gm-padding"></div>
	</' . esc_html( $wrapper_tag ) . '>';


	// ---------------------------------------------------------------------------- second_sidebar_menu_enable --------.
	if ( 1 === $header_style && $groovyMenuSettings['secondSidebarMenuEnable'] ) {
		$second_css_classes = $styles->getHtmlClassesSecondSidebarMenu();

		$output_html .= '<div id="gm-second-nav-drawer" class="gm-second-nav-drawer gm-hidden';
		if ( ! empty( $second_css_classes ) ) {
			$output_html .= ' ' . implode( ' ', $second_css_classes );
		}
		$output_html .= '">';


		ob_start();
		/**
		 * Fires at the Top of Second Sidebar Menu.
		 *
		 * @since 2.5.0
		 */
		do_action( 'gm_second_sidebar_menu_top' );
		$output_html .= ob_get_clean();


		if ( isset( $groovyMenuSettings['secondSidebarMenuId'] ) && is_numeric( $groovyMenuSettings['secondSidebarMenuId'] ) ) {
			// Re-assign nav_menu for the Second Sidebar Menu.
			$args['menu'] = intval( $groovyMenuSettings['secondSidebarMenuId'] );

			// Second Sidebar Menu wrapper.
			$output_html .= '<div class="gm-second-nav-container">';


			ob_start();
			/**
			 * Fires at the Second Sidebar Menu nav.
			 *
			 * @since 2.5.0
			 */
			do_action( 'gm_second_sidebar_menu_nav_first' );
			$output_html .= ob_get_clean();


			$output_html .= wp_nav_menu( $args );


			ob_start();
			/**
			 * Fires at the Second Sidebar Menu nav.
			 *
			 * @since 2.5.0
			 */
			do_action( 'gm_second_sidebar_menu_nav_last' );
			$output_html .= ob_get_clean();


			$output_html .= '</div>'; // .gm-mobile-menu-container


			$output_html .= '<div class="flex-grow-1"></div>';

		}


		ob_start();
		/**
		 * Fires at the Bottom of Second Sidebar Menu.
		 *
		 * @since 2.5.0
		 */
		do_action( 'gm_second_sidebar_menu_bottom' );
		$output_html .= ob_get_clean();


		$output_html .= '</div>';
	}



	// ------------------------------------------------------------------------------------------- mobile menu --------.
	if ( $show_mobile_menu ) {

		$custom_css_class = $styles->getCustomHtmlClass();

		$output_html .= '<aside class="gm-navigation-drawer gm-navigation-drawer--mobile gm-hidden';
		if ( $custom_css_class ) {
			$output_html .= ' ' . esc_attr( $custom_css_class );
		}
		if ( 'slider' === $styles->get( 'mobile', 'mobile_submenu_style' ) ) {
			$output_html .= ' gm-mobile-submenu-style-slider';
		}
		$output_html .= '">';

		$output_html .= '<div class="gm-grid-container d-flex flex-column h-100">';

		if ( $groovyMenuSettings['mobileMenuShowCloseButton'] ) {
			$output_html .= '<div class="gm-menu-btn-close-mobile-drawer gm-hamburger-close" aria-label="close">';

			if ( ( 2 === $header_style && $groovyMenuSettings['minimalisticCssHamburger'] ) || ( $groovyMenuSettings['mobileIndependentCssHamburger'] && 2 !== $header_style ) ){

				$hamburgerType = (2 === $header_style) ? $groovyMenuSettings['minimalisticCssHamburgerType'] : $groovyMenuSettings['mobileIndependentCssHamburgerType'];

				$output_html .= '<div class="hamburger is-active ' . esc_attr( $hamburgerType ) . '"><div class="hamburger-box"><div class="hamburger-inner"></div></div></div>';

			} else {
				$output_html .= '<span class="gm-menu-btn">';
				$output_html .= '	<span class="gm-menu-btn__inner">';

				$menu_icon = 'fa fa-bars';
				if ( ! empty( $styles->getGlobal( 'misc_icons', 'close_icon' ) ) ) {
					$menu_icon = $styles->getGlobal( 'misc_icons', 'close_icon' );
				}
				$output_html .= '	<i class="' . esc_attr( $menu_icon ) . '"></i>';
				$output_html .= '	</span>';
				$output_html .= '</span>';
			}

			$output_html .= '</div>';
		}

		ob_start();
		/**
		 * Fires at the mobile main menu top.
		 *
		 * @since 2.3.2
		 */
		do_action( 'gm_mobile_main_menu_top' );
		$output_html .= ob_get_clean();


		// Mobile Menu wrapper.
		$output_html .= '<div class="gm-mobile-menu-container">';

		$args['gm_navigation_mobile'] = true;

		if ( isset( $groovyMenuSettings['mobileNavMenu'] ) && is_numeric( $groovyMenuSettings['mobileNavMenu'] ) ) {
			// Re-assign nav_menu for the mobile view.
			$args['menu'] = intval( $groovyMenuSettings['mobileNavMenu'] );
		} elseif ( isset( $groovyMenuSettings['mobileNavMenu'] ) && 'default' === $groovyMenuSettings['mobileNavMenu'] ) {
			if ( ! empty( $groovyMenuSettings['nav_menu_data']['id'] ) ) {
				// Re-assign nav_menu for the mobile view.
				$args['menu'] = $groovyMenuSettings['nav_menu_data']['id'];
			}
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


		$output_html .= '</div>'; // .gm-mobile-menu-container
		$output_html .= '<div class="flex-grow-1"></div>';


		ob_start();
		/**
		 * Fires after main menu nav for mobile.
		 *
		 * @since 1.9.5
		 */
		do_action( 'gm_mobile_after_main_menu_nav' );
		$output_html .= ob_get_clean();


		$output_html .= '<div class="gm-mobile-action-area-wrapper d-flex justify-content-center align-items-center text-center mb-4 mt-5">';

		$searchIcon = 'gmi gmi-zoom-search';
		if ( $styles->getGlobal( 'misc_icons', 'search_icon' ) ) {
			$searchIcon = $styles->getGlobal( 'misc_icons', 'search_icon' );
		}

		if (
			'disable' !== $searchForm &&
			! empty( $groovyMenuSettings['searchFormIconPositionMobile'] ) &&
			in_array( $groovyMenuSettings['searchFormIconPositionMobile'], array(
				'slideBottom',
				'topbar_slideBottom',
			), true )
		) {

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

		// Check if need output icon divider.
		if (
			(
				'disable' !== $searchForm && ! empty( $groovyMenuSettings['searchFormIconPositionMobile'] ) && in_array( $groovyMenuSettings['searchFormIconPositionMobile'], array(
					'slideBottom',
					'topbar_slideBottom'
				), true ) )
			&&
			(
				! gm_get_shop_is_catalog() && $groovyMenuSettings['woocommerceCart'] && class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_id' )
			)
		) {
			$output_html .= '<div class="gm-divider--vertical mx-4"></div>';
		}

		// Woo minicart mobile (slide container bottom).
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


		$output_html .= '</div>'; // .gm-mobile-action-area-wrapper
		$output_html .= '</div>'; // .gm-grid-container
		$output_html .= '<div class="gm-mobile-postwrap"></div>';
		$output_html .= '</aside>';
	} // end of if $show_mobile_menu.

	if ( $groovyMenuSettings['dropdownOverlay'] ) {
		$output_html .= '<div class="gm-dropdown-overlay"></div>';
	}

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

	return '';

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
 * @return string  if  $echo is true then return empty string (by default)
 *
 */
function gm_wp_nav_menu( $args = array() ) {
	global $groovyMenuSettings;

	if ( ! empty( $args['menu'] ) || ! empty( $args['gm_preset_id'] ) ) {
		\GroovyMenu\PreStorage::get_instance()->remove_all_gm();
	}

	$defaults_args = array(
		'menu'           => 'default',
		'gm_preset_id'   => 'default',
		'echo'           => false,
		'gm_echo'        => true,
		'gm_pre_storage' => false,
		'depth'          => 0, // limit the depth of the nav.
		'is_disable'     => false,
	);

	$args = shortcode_atts( $defaults_args, $args, '' );

	if ( ! empty( $args['gm_preset_id'] ) ) {
		$groovyMenuSettings['preset']['id'] = $args['gm_preset_id'];
	}

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
 * @return string  if  $echo is true then return empty string (by default)
 *
 */
function groovy_menu( $args = array() ) {
	return groovyMenu( $args );
}


add_shortcode( 'groovy_menu', 'groovyMenu' );

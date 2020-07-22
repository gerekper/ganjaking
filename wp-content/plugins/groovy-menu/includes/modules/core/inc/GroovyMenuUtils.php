<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuUtils
 */
class GroovyMenuUtils {

	/**
	 * Register groovy_menu_preset post type.
	 */
	public static function add_groovy_menu_preset_post_type() {

		$capabilities = array(
			'edit_post'           => 'groovy_menu_edit_preset',
			'read_post'           => 'groovy_menu_read_preset',
			'delete_post'         => 'groovy_menu_delete_preset',
			'delete_posts'        => 'groovy_menu_delete_preset',
			'edit_posts'          => 'groovy_menu_edit_preset',
			'edit_others_posts'   => 'groovy_menu_edit_others_presets',
			'publish_posts'       => 'groovy_menu_publish_presets',
			'read_private_posts'  => 'groovy_menu_read_private_presets',
			'create_posts'        => 'groovy_menu_create_preset',
			'delete_others_posts' => 'groovy_menu_delete_others_presets',
		);

		register_post_type( 'groovy_menu_preset',
			array(
				'labels'            => array(
					'name'          => esc_html__( 'Groovy Menu Preset', 'groovy-menu' ),
					'singular_name' => esc_html__( 'Groovy Menu Preset', 'groovy-menu' ),
					'add_new_item'  => esc_html__( 'Add New Groovy Menu Preset', 'groovy-menu' ),
					'edit_item'     => esc_html__( 'Edit Groovy Menu Preset', 'groovy-menu' ),
					'view_item'     => esc_html__( 'View Groovy Menu Preset', 'groovy-menu' ),
				),
				'public'            => false,
				'has_archive'       => false,
				'show_in_nav_menus' => false,
				'show_in_menu'      => false,
				'taxonomies'        => array(),
				'supports'          => array(
					'title',
					'editor',
					'thumbnail',
					'author',
					'custom-fields',
					'revisions',
				),
				'rewrite'           => array(
					'slug'       => 'groovy_menu_preset',
					'with_front' => false,
				),
				'capability_type'   => 'groovy_menu_preset',
				'capabilities'      => $capabilities,
			)
		);

	}


	/**
	 * @return array
	 */
	public static function get_old_preset_ids() {
		$preset_change_id = array();
		$all_presets      = GroovyMenuPreset::getAll();
		foreach ( $all_presets as $preset ) {
			$old_id = get_post_meta( $preset->id, 'gm_old_id', true );
			if ( ! empty( $old_id ) && intval( $old_id ) ) {
				$preset_change_id[ intval( $old_id ) ] = $preset->id;
			}
		}

		return $preset_change_id;
	}


	/**
	 * @return array
	 */
	public static function get_preset_used_in() {
		$preset_used_in = array();

		$preset_used_in = get_option( 'groovy_menu_preset_used_in_storage' );

		if ( ! is_array( $preset_used_in ) ) {
			return array();
		}

		return $preset_used_in;
	}


	/**
	 * @param      $preset_id
	 * @param bool $return_count
	 *
	 * @return array|int
	 */
	public static function get_preset_used_in_by_id( $preset_id, $return_count = false ) {

		$preset_id = empty( $preset_id ) ? null : intval( $preset_id );

		if ( empty( $preset_id ) ) {
			return array();
		}

		$used_in        = array();
		$preset_used_in = self::get_preset_used_in();
		$counter        = 0;

		if ( ! is_array( $preset_used_in ) ) {
			$preset_used_in = array();
		}

		foreach ( $preset_used_in as $place => $data ) {

			switch ( $place ) {
				case 'default':
					if ( intval( $data ) === $preset_id ) {
						$used_in['default'] = true;
						$counter ++;
					}
					break;
				case 'global':
					foreach ( $data as $post_type => $preset ) {
						if ( intval( $preset ) === $preset_id ) {
							$used_in['global'][ $post_type ] = true;
							$counter ++;
						}
					}
					break;
				case 'taxonomy':
					foreach ( $data as $taxonomy_id => $preset ) {
						if ( intval( $preset ) === $preset_id ) {
							$used_in['taxonomy'][ $taxonomy_id ] = true;
							$counter ++;
						}
					}
					break;
				case 'post':
					foreach ( $data as $post_type => $post_data ) {
						foreach ( $post_data as $post_id => $preset ) {
							if ( intval( $preset ) === $preset_id ) {
								$used_in['post'][ $post_type ][ $post_id ] = true;
								$counter ++;
							}
						}
					}
					break;
			}
		}

		if ( $return_count ) {
			return $counter;
		} else {
			return $used_in;
		}
	}


	/**
	 * @return string
	 */
	public static function getUploadDir() {
		$uploadDir = wp_upload_dir();

		return $uploadDir['basedir'] . '/groovy/';
	}


	/**
	 * @return string
	 */
	public static function getFontsDir() {
		$fonts = self::getUploadDir() . 'fonts/';

		return $fonts;
	}


	/**
	 * @return string
	 */
	public static function getUploadUri() {
		$uploadDir = wp_upload_dir();
		$scheme    = is_ssl() ? 'https' : 'http';

		return set_url_scheme( $uploadDir['baseurl'] . '/groovy/', $scheme );
	}


	public static function addPresetCssFile() {

		global $groovyMenuSettings;
		$css_file_params = isset( $groovyMenuSettings['css_file_params'] ) ? $groovyMenuSettings['css_file_params'] : array();

		if ( ! empty( $css_file_params ) && is_file( $css_file_params['upload_dir'] . $css_file_params['css_filename'] ) ) {

			wp_enqueue_style(
				'groovy-menu-preset-style-' . $css_file_params['preset_id'],
				$css_file_params['upload_uri'] . $css_file_params['css_filename'],
				[ 'groovy-menu-style' ],
				$css_file_params['preset_key'] . ( is_rtl() ? '_rtl' : '' )
			);

		}
	}


	/**
	 * @param bool $name_as_key
	 *
	 * @return array
	 */
	public static function getPostTypes( $name_as_key = true ) {

		$post_types = array();

		// get the registered data about each post type with get_post_type_object.
		$post_types_query = get_post_types(
			array(
				'public'            => true,
				'show_in_nav_menus' => true,
			)
		);

		if ( empty( $post_types_query ) ) {
			return $post_types;
		}

		foreach ( $post_types_query as $type ) {
			$type_obj = get_post_type_object( $type );

			$name  = isset( $type_obj->name ) ? $type_obj->name : null;
			$label = isset( $type_obj->label ) ? $type_obj->label : '';

			if ( empty( $name ) || empty( $label ) ) {
				continue;
			}

			if ( $name_as_key ) {
				$post_types[ $name ] = $label;
			} else {
				$post_types[ $label ] = $name;
			}
		}

		return $post_types;

	}

	/**
	 * @param bool $name_as_key
	 *
	 * @param bool $get_custom_types
	 *
	 * @return array
	 */
	public static function getPostTypesExtended( $name_as_key = true, $get_custom_types = true ) {

		$post_types     = self::getPostTypes( true );
		$post_types_ext = array();

		if ( ! is_array( $post_types ) ) {
			return $post_types_ext;
		}

		foreach ( $post_types as $type => $name ) {

			if ( 'gm_menu_block' === $type && $get_custom_types ) {
				continue;
			}

			$post_types_ext[ $type ] = $name;

			if ( $get_custom_types ) {
				switch ( $type ) {
					case 'post':
						$post_types_ext['post--single'] = $name . ' (' . esc_html__( 'single pages', 'groovy-menu' ) . ')';
						break;
					case 'page':
						$post_types_ext['page--is_search'] = esc_html__( 'Search page', 'groovy-menu' );
						$post_types_ext['page--is_404']    = esc_html__( '404 Not Found Page', 'groovy-menu' );
						break;
					default:
						$type_obj = get_post_type_object( $type );
						// Post type can has archive and single pages.
						if ( is_object( $type_obj ) && ! empty( $type_obj->has_archive ) && $type_obj->has_archive ) {
							$post_types_ext[ $type . '--single' ] = $name . ' (' . esc_html__( 'single pages', 'groovy-menu' ) . ')';
						}
						break;
				}
			}
		}

		if ( ! $name_as_key ) {
			$_post_types_ext = array();
			foreach ( $post_types_ext as $type => $name ) {
				$_post_types_ext[ $name ] = $type;
			}
			$post_types_ext = $_post_types_ext;
		}

		return $post_types_ext;

	}

	/**
	 * @return array
	 */
	public static function getPostTypesForSearch() {

		$post_types     = self::getPostTypes( true );
		$post_types_ext = array();

		if ( ! is_array( $post_types ) ) {
			return $post_types_ext;
		}

		foreach ( $post_types as $type => $name ) {

			if ( 'gm_menu_block' === $type ) {
				continue;
			}

			$type_obj = get_post_type_object( $type );

			if ( is_object( $type_obj ) ) {
				$post_types_ext[ $type ] = array(
					'title'     => esc_html__( 'Search in:', 'groovy-menu' ) . ' ' . $name . ' (' . $type . ')',
					'condition' => array( 'search_form', 'in', array( 'fullscreen', 'dropdown-without-ajax' ) ),
				);
			}
		}

		// support product woo, while not detect in some cases.
		if ( empty( $post_types['product'] ) && class_exists( 'WooCommerce' ) ) {
			$post_types_ext['product'] = array(
				'title'     => esc_html__( 'Search in:', 'groovy-menu' ) . ' Products (product)',
				'condition' => array( 'search_form', 'in', array( 'fullscreen', 'dropdown-without-ajax' ) ),
			);
		}

		return $post_types_ext;

	}


	/**
	 * Get all posts by gm_menu_block post type.
	 *
	 * @return array
	 */
	public static function getMenuBlockPostsList() {

		static $cached_posts = array();

		if ( ! empty( $cached_posts ) ) {
			return $cached_posts;
		}

		$params = array(
			'post_type'   => 'gm_menu_block',
			'post_status' => 'publish',
			'numberposts' => - 1,
		);

		$posts = get_posts( $params );

		foreach ( $posts as $post ) {
			if ( ! empty( $post->ID ) ) {
				$title                     = empty( $post->post_title ) ? '' : $post->post_title;
				$title                     = $title . ' (id:' . $post->ID . ')';
				$cached_posts[ $post->ID ] = $title;
			}
		}

		return $cached_posts;

	}


	/**
	 * Get all posts by gm_menu_block post type.
	 *
	 * @param string $searchIcon
	 *
	 * @return string
	 */
	public static function getSearchBlock( $searchIcon ) {

		global $groovyMenuSettings;

		$html             = '';
		$searchFilter     = '';
		$menuBlockContent = '';
		$isFullScreen     = false;
		$isShowDefault    = true;
		$isSearchCustom   = false;

		$searchForm                  = isset( $groovyMenuSettings['searchForm'] ) ? $groovyMenuSettings['searchForm'] : 'fullscreen';
		$searchFormFrom              = isset( $groovyMenuSettings['searchFormFrom'] ) ? $groovyMenuSettings['searchFormFrom'] : 'all';
		$searchFormCustomWrapper     = isset( $groovyMenuSettings['searchFormCustomWrapper'] ) ? $groovyMenuSettings['searchFormCustomWrapper'] : 'fullscreen';
		$searchFormCustomShowDefault = isset( $groovyMenuSettings['searchFormCustomShowDefault'] ) ? $groovyMenuSettings['searchFormCustomShowDefault'] : true;
		$searchFormCustomId          = isset( $groovyMenuSettings['searchFormCustomId'] ) ? intval( $groovyMenuSettings['searchFormCustomId'] ) : 0;

		if ( 'custom' === $searchForm ) {
			$isSearchCustom = true;
		}

		if ( 'fullscreen' === $searchForm || ( $isSearchCustom && 'dropdown' !== $searchFormCustomWrapper ) ) {
			$isFullScreen = 'fullscreen';
		}

		if ( $isSearchCustom && ! $searchFormCustomShowDefault ) {
			$isShowDefault = false;
		}

		if ( $isSearchCustom && ! empty( $searchFormCustomId ) ) {
			$menuBlockHelper  = new \GroovyMenu\WalkerNavMenu();
			$menuBlockContent = $menuBlockHelper->getMenuBlockPostContent( $searchFormCustomId );
		}

		if ( ! empty( $searchForm ) && 'all' !== $searchFormFrom ) {
			$searchFilter .= '<input type="hidden" name="post_type" value="' . $searchFormFrom . '">';
		}

		$current_lang = apply_filters( 'wpml_current_language', null );
		if ( $current_lang ) {
			$searchFilter .= '<input type="hidden" name="lang" value="' . $current_lang . '">';
		}

		$searchFilter = apply_filters( 'gm_search_filter_hidden_input', $searchFilter );

		$home_url = trailingslashit( network_site_url() );
		if ( defined( 'WPML_PLUGIN_FOLDER' ) && WPML_PLUGIN_FOLDER ) {
			$home_url = apply_filters( 'wpml_home_url', $home_url );
		}

		$html .= '<div class="gm-search ' . ( $isFullScreen ? 'fullscreen' : 'gm-dropdown' ) . '">
										<i class="' . esc_attr( $searchIcon ) . '"></i>
										<span class="gm-search__txt">'
		         . esc_html__( 'Search', 'groovy-menu' ) .
		         '</span>';

		if ( $searchForm === 'dropdown-without-ajax' || ( 'custom' === $searchForm && 'dropdown' === $searchFormCustomWrapper ) ) {
			$html .= '
										<div class="gm-search-wrapper">';
			if ( $isSearchCustom && 'dropdown' === $searchFormCustomWrapper ) {
				$html .= $menuBlockContent;
			}

			if ( $isShowDefault ) {
				$html .= '					<form action="' . $home_url . '"
											      method="get"
											      class="gm-search-wrapper-form">
												<div class="gm-form-group">
													<input placeholder="' . esc_html__( 'Search...', 'groovy-menu' ) . '"
													       type="text"
													       name="s"
													       class="gm-search__input">
													' . $searchFilter . '
													<button type="submit" class="gm-search-btn">
														<i class="fa fa-search"></i>
													</button>
												</div>
											</form>';
			}
			$html .= '					</div>';
		}

		$html .= '<div class="gm-search__fullscreen-container gm-hidden">
										<span class="gm-search__close"></span>

										<div class="gm-search__inner">';
		if ( $isShowDefault ) {
			$html .= '<span class="gm-search__alpha">'
			         . esc_html__( 'START TYPING AND PRESS ENTER TO SEARCH', 'groovy-menu' ) .
			         '</span>';
		}
		$html .= '							<div class="gm-search-wrapper">';

		if ( $isSearchCustom ) {
			$html .= $menuBlockContent;
		}

		if ( $isShowDefault ) {
			$html .= '							<form action="' . $home_url . '"
												      method="get"
												      class="gm-search-wrapper-form">
													<div class="gm-form-group">
														<input type="text" name="s" class="gm-search__input">
														' . $searchFilter . '
														<button type="submit" class="gm-search-btn">
															<i class="fa fa-search"></i>
														</button>
													</div>
												</form>';
		}
		$html .= '							</div>
										</div>
									</div>';
		$html .= '				</div>';


		return $html;

	}

	/**
	 * Uses for many custom options
	 *
	 * @return string
	 */
	public static function get_current_page_type() {

		$type = get_post_type() ? : 'page';

		global $wp_query;
		if ( empty( $wp_query ) || ! is_object( $wp_query ) ) {
			return $type;
		}


		try {

			if ( self::is_shop_search() ) {

				$type = 'product';

			} elseif ( is_search() ) {

				$type = 'page--is_search';

			} elseif ( is_404() ) {

				$type = 'page--is_404';

			} elseif ( is_attachment() ) {

				$type = 'attachment';

			} elseif ( self::is_product_woocommerce_page() ) {

				$type = 'product--single';

			} elseif ( self::is_shop_and_category_woocommerce_page() || self::is_additional_woocommerce_page() || self::is_product_woocommerce_page() ) {

				$type = 'product';

			} elseif ( is_page_template( 'template-blog.php' ) || is_home() ) {

				$type = 'post';

			} elseif ( is_page_template( 'template-portfolio.php' ) ) {

				$type = 'crane_portfolio';

			} elseif ( ( is_single() && 'crane_portfolio' === get_post_type() ) || ( is_archive() && 'crane_portfolio' === get_post_type() ) || 'crane_portfolio_cats' === get_query_var( 'taxonomy' ) || 'crane_portfolio_tags' === get_query_var( 'taxonomy' ) ) {

				$type = is_single() ? 'crane_portfolio--single' : 'crane_portfolio';

			} elseif ( ( is_single() && 'post' === get_post_type() ) || ( is_archive() && 'post' === get_post_type() ) || is_archive() ) {

				$type = is_single() ? 'post--single' : 'post';

			} elseif ( is_page() && 'page' === get_post_type() ) {

				$type = 'page';

			} elseif ( 'posts' === get_option( 'show_on_front' ) ) {
				// Check if the blog page is the front page.
				$type = 'post';

			} elseif ( is_single() ) {

				$type = $type . '--single';

			}


		} catch ( Exception $e ) {
			$type = 'page';
		}


		return $type;
	}

	/**
	 * Detect current page
	 *
	 * @return bool
	 */
	public static function is_product_woocommerce_page() {

		if ( function_exists( 'is_product' ) ) {
			if ( is_product() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Return true if current search page is post_type === 'product'
	 *
	 * @return bool
	 */
	public static function is_shop_search() {
		if ( get_search_query() && get_query_var( 'post_type' ) && 'product' === get_query_var( 'post_type' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Detect current page
	 *
	 * @return bool
	 */
	public static function is_shop_and_category_woocommerce_page() {

		if (
			function_exists( 'is_woocommerce' ) &&
			function_exists( 'is_product' ) &&
			function_exists( 'is_shop' ) &&
			function_exists( 'is_product_tag' )
		) {
			if ( ! is_product() && ( is_woocommerce() || is_shop() || is_product_tag() ) ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * It determines whether the current page belongs to woocommerce
	 * (cart and checkout are standard pages with shortcodes and which are also included)
	 *
	 * @return bool
	 */
	public static function is_additional_woocommerce_page() {

		if ( function_exists( 'is_cart' ) && is_cart() ) {
			return true;
		}
		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			return true;
		}
		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			return true;
		}
		if ( function_exists( 'is_checkout_pay_page' ) && is_checkout_pay_page() ) {
			return true;
		}
		if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url() ) {
			return true;
		}

		return false;
	}


	/**
	 * @return array
	 */
	public static function getNavMenus() {

		static $cached_nav_menu = array();

		$nav_menus = array();

		if ( ! empty( $cached_nav_menu ) ) {
			$menus = $cached_nav_menu;
		} else {
			$menus = wp_get_nav_menus();
		}

		if ( ! empty( $menus ) ) {
			$cached_nav_menu = $menus;
			foreach ( $menus as $menu ) {
				if ( isset( $menu->term_id ) && isset( $menu->name ) ) {
					$nav_menus[ $menu->term_id ] = $menu->name;
				}
			}
		}

		return $nav_menus;

	}


	/**
	 * Get default menu
	 *
	 * @return string
	 */
	public static function getDefaultMenu() {

		$menu_location = self::getNavMenuLocations( true );
		$locations     = get_nav_menu_locations();
		if ( ! empty( $menu_location ) && ! empty( $locations ) ) {
			$menu_id = $locations[ $menu_location ];
			if ( ! empty( $menu_id ) ) {
				$menu_obj = wp_get_nav_menu_object( $menu_id );
			}
		}

		if ( ! empty( $menu_obj ) && isset( $menu_obj->term_id ) ) {
			$menu = $menu_obj->term_id;
		} else {
			$menu = '';
		}

		return strval( $menu );

	}


	/**
	 * Return registered nav_menu locations.
	 *
	 * @param string $name check exists location name.
	 *
	 * @return array|string
	 */
	public static function getRegisteredLocations( $name = '' ) {

		global $_wp_registered_nav_menus;

		$return_value = empty( $_wp_registered_nav_menus ) ? array() : $_wp_registered_nav_menus;

		if ( ! empty( $name ) ) {
			$return_value = empty( $return_value[ $name ] ) ? '' : $return_value[ $name ];
		}

		return $return_value;

	}


	/**
	 * Retrieves all registered navigation menu locations and the menus assigned to them.
	 *
	 * @param bool $return_first   return only first of them, or 'gm_primary' if exists.
	 *
	 * @param bool $loc_name_first return list with location name first as value.
	 *
	 * @return array|int|null|string
	 */
	public static function getNavMenuLocations( $return_first = false, $loc_name_first = false ) {

		$locations      = array();
		$menu_locations = get_nav_menu_locations();

		foreach ( $menu_locations as $location => $location_id ) {
			if ( $loc_name_first ) {
				$value = $location . ' (' . wp_get_nav_menu_name( $location ) . ')';
			} else {
				$value = wp_get_nav_menu_name( $location ) . ' (' . $location . ')';
			}

			$locations[ $location ] = $value;
		}

		if ( $return_first ) {
			if ( isset( $locations['gm_primary'] ) ) {
				return 'gm_primary';
			}

			if ( empty( $locations ) ) {
				$locations = '';
			} else {
				reset( $locations );
				$locations = key( $locations );
			}
		}

		return $locations;

	}


	/**
	 * @return null|string
	 */
	public static function getMasterPreset() {
		$styles = new GroovyMenuStyle();

		return $styles->getGlobal( 'taxonomies', 'default_master_preset' );
	}


	/**
	 * @return int|string
	 */
	public static function getMasterNavmenu() {

		$styles = new GroovyMenuStyle();

		$master_navmenu = $styles->getGlobal( 'taxonomies', 'default_master_menu' );
		if ( ! empty( $master_navmenu ) ) {
			return $master_navmenu;
		}

		$master_location = self::getMasterLocation();

		if ( empty( $master_location ) ) {
			$locations = self::getNavMenuLocations();
			if ( ! empty( $locations ) && is_array( $locations ) ) {
				foreach ( $locations as $key => $val ) {
					$master_location = $key;
					break;
				}
			}
		}

		$master_navmenu  = '';
		$theme_locations = get_nav_menu_locations();

		if ( ! empty( $master_location ) && ! empty( $theme_locations ) && ! empty( $theme_locations[ $master_location ] ) ) {
			$menu_obj = get_term( $theme_locations[ $master_location ], 'nav_menu' );
		}
		if ( ! empty( $menu_obj ) && isset( $menu_obj->term_id ) ) {
			$master_navmenu = $menu_obj->term_id;
		}

		if ( empty( $master_navmenu ) ) {
			$master_navmenu = self::getDefaultMenu();
		}

		return $master_navmenu;

	}


	/**
	 * @return null|string
	 */
	public static function getMasterLocation() {
		$master_location = 'gm_primary';
		$all_locations   = self::getNavMenuLocations();

		if ( empty( $all_locations[ $master_location ] ) ) {
			$master_location = self::getNavMenuLocations( true );
		}

		return $master_location;
	}


	public static function check_apr() {
		$apr        = '';
		$name       = '_' . '_l' . 'ic';
		$name_cache = $name . '_cache2';
		$apr_opt    = get_option( GROOVY_MENU_DB_VER_OPTION . $name );
		$cache      = get_transient( GROOVY_MENU_DB_VER_OPTION . $name_cache );
		if ( $apr_opt && ! $cache ) {
			$get_apr   = 'ge' . 't_pa';
			$get_apr  .= 'raml' . 'ic';
			$name_apr  = 'che' . 'ck_l';
			$name_apr .= 'ic';
			if ( method_exists( 'GroovyMenuUtils', $get_apr ) ) {
				$apr = self::$get_apr( 'ap' . 'pr' . 'ove' );
			}
			if ( $apr && method_exists( 'GroovyMenuUtils', $name_apr ) ) {
				$res = self::$name_apr( true );
				if ( $res ) {
					set_transient( GROOVY_MENU_DB_VER_OPTION . $name_cache, true, 52 * HOUR_IN_SECONDS );
				} else {
					update_option( GROOVY_MENU_DB_VER_OPTION . $name, false );
				}
			} elseif( ! $apr ) {
				update_option( GROOVY_MENU_DB_VER_OPTION . $name, false );
			}
		}

		return true;
	}


	/**
	 * Explode values for taxonomy specific field
	 *
	 * @param string $raw_value takes a value for processing as an argument.
	 *
	 * @param bool   $fill_empty_post_types
	 *
	 * @return array
	 */
	public static function getTaxonomiesPresets( $raw_value = '', $fill_empty_post_types = true ) {

		if ( empty( $raw_value ) ) {
			$styles    = new GroovyMenuStyle();
			$raw_value = $styles->getGlobal( 'taxonomies', 'taxonomies_preset' );
		}

		if ( empty( $raw_value ) ) {
			return array();
		}

		if ( is_array( $raw_value ) ) {
			return $raw_value;
		}

		if ( is_string( $raw_value ) ) {
			$saved_value = explode( ',', $raw_value );
		}

		$saved_tax      = array();
		$default_values = array(
			'preset' => 'default',
			'menu'   => 'default',
		);

		if ( ! empty( $raw_value ) && is_array( $saved_value ) ) {
			foreach ( $saved_value as $tax_opt ) {
				$key_value = explode( ':::', $tax_opt );
				if ( is_array( $key_value ) && isset( $key_value[0] ) && isset( $key_value[1] ) ) {
					$tax    = $key_value[0];
					$params = explode( '@', $key_value[1] );
					if ( is_array( $params ) && isset( $params[0] ) && isset( $params[1] ) ) {
						$saved_tax[ $tax ] = array(
							'preset' => $params[0],
							'menu'   => $params[1],
						);
					} else {
						$saved_tax[ $tax ] = $default_values;
					}
				}
			}
		}

		if ( $fill_empty_post_types ) {

			$post_types = self::getPostTypesExtended();
			foreach ( $post_types as $type_name => $type_label ) {
				if ( empty( $saved_tax[ $type_name ] ) ) {
					$saved_tax[ $type_name ] = $default_values;
				}
			}

		}


		return $saved_tax;

	}

	/**
	 * Implode values for taxonomy specific field
	 *
	 * @param array $taxonomies takes a value for processing as an argument.
	 *
	 * @return string
	 */
	public static function setTaxonomiesPresets( $taxonomies = array() ) {

		if ( empty( $taxonomies ) || ! is_array( $taxonomies ) ) {
			return '';
		}

		$saved_value    = '';
		$saved_tax      = array();
		$default_values = array(
			'preset' => 'default',
			'menu'   => 'default',
		);

		foreach ( self::getTaxonomiesPresets() as $post_type => $settings ) {
			if ( empty( $taxonomies[ $post_type ] ) ) {
				$taxonomies[ $post_type ] = $settings;
			}
		}

		foreach ( $taxonomies as $post_type => $settings ) {
			$value_preset = empty( $settings['preset'] ) ? $default_values['preset'] : $settings['preset'];
			$value_menu   = empty( $settings['menu'] ) ? $default_values['menu'] : $settings['menu'];

			$saved_tax[] = $post_type . ':::' . $value_preset . '@' . $value_menu;
		}

		if ( ! empty( $saved_tax ) ) {
			$saved_value = implode( ',', $saved_tax );
		}


		return $saved_value;

	}


	/**
	 * @param $post_type
	 *
	 * @return array|mixed
	 */
	public static function getTaxonomiesPresetByPostType( $post_type ) {

		$styles           = new GroovyMenuStyle();
		$override_for_tax = $styles->getGlobal( 'taxonomies', 'override_for_tax' );
		$return_values    = array(
			'preset' => 'default',
			'menu'   => 'default',
		);

		if ( ! $override_for_tax ) {
			return $return_values;
		}

		$saved_tax = self::getTaxonomiesPresets();

		if ( ! empty( $saved_tax[ $post_type ] ) ) {
			$return_values = $saved_tax[ $post_type ];
		}

		return $return_values;

	}

	/**
	 * Get all the registered image sizes along with their dimensions
	 *
	 * @global array $_wp_additional_image_sizes
	 *
	 * @link http://core.trac.wordpress.org/ticket/18947 Reference ticket
	 *
	 * @return array $image_sizes The image sizes
	 */
	public static function get_all_image_sizes() {
		global $_wp_additional_image_sizes;

		$default_image_sizes = get_intermediate_image_sizes();

		$image_sizes = array( 'full' => array() );

		foreach ( $default_image_sizes as $size ) {
			$image_sizes[ $size ]['width']  = intval( get_option( "{$size}_size_w" ) );
			$image_sizes[ $size ]['height'] = intval( get_option( "{$size}_size_h" ) );
			$image_sizes[ $size ]['crop']   = get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
		}

		if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
			$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
		}

		return $image_sizes;
	}

	/**
	 * Get all the registered image sizes for key value
	 *
	 * @return array $image_sizes_array The image sizes.
	 */
	public static function get_all_image_sizes_for_select() {
		$image_sizes_array = array( 'full' => esc_html__( 'Original full size', 'groovy-menu' ) );
		$image_sizes       = self::get_all_image_sizes();

		foreach ( $image_sizes as $size => $size_data ) {
			$image_sizes_array[ $size ] = $size;
		}

		return $image_sizes_array;
	}

	public static function groovy_wpml_register_single_string( GroovyMenuStyle $styles ) {

		$logo_text     = $styles->getGlobal( 'logo', 'logo_text' );
		$toolbar_email = $styles->getGlobal( 'toolbar', 'toolbar_email' );
		$toolbar_phone = $styles->getGlobal( 'toolbar', 'toolbar_phone' );

		//WPML
		/**
		 * register strings for translation.
		 */
		do_action( 'wpml_register_single_string', 'groovy-menu', 'Global settings - logo text', $logo_text );
		do_action( 'wpml_register_single_string', 'groovy-menu', 'Global settings - toolbar email text', $toolbar_email );
		do_action( 'wpml_register_single_string', 'groovy-menu', 'Global settings - toolbar phone text', $toolbar_phone );
		//WPML

	}

	public static function getAutoIntegrationOptionName() {
		return 'gm_auto_integrate_locations_';
	}

	public static function getIntegrationConfigOptionName() {
		return 'gm_integrate_config_';
	}

	/**
	 * Check if in auto-integration mode
	 *
	 * @return bool
	 */
	public static function getAutoIntegration() {
		global $gm_supported_module;

		if ( isset( $gm_supported_module['GroovyMenuShowIntegration'] ) && ! $gm_supported_module['GroovyMenuShowIntegration'] ) {
			return false;
		}

		if ( isset( $_REQUEST['mailpoet_router'] ) ) { // @codingStandardsIgnoreLine
			return false;
		}

		$theme_name     = empty( $gm_supported_module['theme'] ) ? wp_get_theme()->get_template() : $gm_supported_module['theme'];
		$integrate_data = get_option( self::getAutoIntegrationOptionName() . $theme_name );
		$return_value   = ( ! empty( $integrate_data ) && $integrate_data ) ? true : false;

		return $return_value;
	}

	/**
	 * Check if in integration location selected
	 *
	 * @return bool
	 */
	public static function getSingleLocationIntegration() {
		global $gm_supported_module;

		$theme_name       = empty( $gm_supported_module['theme'] ) ? wp_get_theme()->get_template() : $gm_supported_module['theme'];
		$integrate_config = get_option( self::getIntegrationConfigOptionName() . $theme_name );

		$return_value = '';

		if ( ! empty( $integrate_config ) && ! empty( $integrate_config['single_location'] ) ) {
			$return_value = esc_attr( $integrate_config['single_location'] );
		}

		return $return_value;
	}


	public static function get_posts_fields( $args = array() ) {
		$valid_fields = array(
			'ID'             => '%d',
			'post_author'    => '%d',
			'post_type'      => '%s',
			'post_mime_type' => '%s',
			'post_title'     => false,
			'post_name'      => '%s',
			'post_date'      => '%s',
			'post_modified'  => '%s',
			'menu_order'     => '%d',
			'post_parent'    => '%d',
			'post_excerpt'   => false,
			'post_content'   => false,
			'post_status'    => '%s',
			'comment_status' => false,
			'ping_status'    => false,
			'to_ping'        => false,
			'pinged'         => false,
			'comment_count'  => '%d'
		);
		$defaults     = array(
			'post_type'      => 'groovy_menu_preset',
			'post_status'    => 'publish',
			'orderby'        => 'post_date',
			'order'          => 'DESC',
			'posts_per_page' => - 1,
		);

		$order = $orderby = $posts_per_page = '';

		global $wpdb;
		$args  = wp_parse_args( $args, $defaults );
		$where = "";
		foreach ( $valid_fields as $field => $can_query ) {
			if ( isset( $args[ $field ] ) && $can_query ) {
				if ( '' !== $where ) {
					$where .= ' AND ';
				}
				$where .= $wpdb->prepare( $field . " = " . $can_query, $args[ $field ] );
			}
		}
		if ( isset( $args['search'] ) && is_string( $args['search'] ) ) {
			if ( '' !== $where ) {
				$where .= ' AND ';
			}
			$where .= $wpdb->prepare( "post_title LIKE %s", "%" . $args['search'] . "%" );
		}
		if ( isset( $args['include'] ) ) {
			if ( is_string( $args['include'] ) ) {
				$args['include'] = explode( ',', $args['include'] );
			}
			if ( is_array( $args['include'] ) ) {
				$args['include'] = array_map( 'intval', $args['include'] );
				if ( '' !== $where ) {
					$where .= ' OR ';
				}
				$where .= "ID IN (" . implode( ',', $args['include'] ) . ")";
			}
		}
		if ( isset( $args['exclude'] ) ) {
			if ( is_string( $args['exclude'] ) ) {
				$args['exclude'] = explode( ',', $args['exclude'] );
			}
			if ( is_array( $args['exclude'] ) ) {
				$args['exclude'] = array_map( 'intval', $args['exclude'] );
				if ( '' !== $where ) {
					$where .= ' AND ';
				}
				$where .= "ID NOT IN (" . implode( ',', $args['exclude'] ) . ")";
			}
		}
		extract( $args );
		$iscol = false;
		if ( isset( $fields ) ) {
			if ( is_string( $fields ) ) {
				$fields = explode( ',', $fields );
			}
			if ( is_array( $fields ) ) {
				$fields = array_intersect( $fields, array_keys( $valid_fields ) );
				if ( count( $fields ) === 1 ) {
					$iscol = true;
				}
				$fields = implode( ',', $fields );
			}
		}
		if ( empty( $fields ) ) {
			$fields = '*';
		}
		if ( ! in_array( $orderby, $valid_fields ) ) {
			$orderby = 'post_date';
		}
		if ( ! in_array( strtoupper( $order ), array( 'ASC', 'DESC' ) ) ) {
			$order = 'DESC';
		}
		if ( ! intval( $posts_per_page ) && $posts_per_page != - 1 ) {
			$posts_per_page = $defaults['posts_per_page'];
		}
		if ( '' === $where ) {
			$where = '1';
		}
		$q = "SELECT $fields FROM $wpdb->posts WHERE " . $where;
		$q .= " ORDER BY $orderby $order";
		if ( $posts_per_page != - 1 ) {
			$q .= " LIMIT $posts_per_page";
		}

		return $iscol ? $wpdb->get_col( $q ) : $wpdb->get_results( $q );
	}

	/**
	 * Adds meta links to the plugin in the WP Admin > Plugins screen
	 *
	 * @param array  $links
	 * @param string $file
	 *
	 * @return array
	 */
	public static function gm_plugin_meta_links( $links, $file ) {
		if ( 'groovy-menu/groovy-menu.php' !== $file ) {
			return $links;
		}

		$lver = false;
		if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
			$lver = true;
		}

		if ( $lver) {
			$links[] = '<a href="https://grooni.com/docs/groovy-menu/" target="_blank">' . esc_html__( 'Docs', 'groovy-menu' ) . '</a>';
			$links[] = '<a href="https://wordpress.org/support/plugin/groovy-menu/" target="_blank"">' . esc_html__( 'Free support', 'groovy-menu' ) . '</a>';
		} else {
			$links[] = '<a href="https://grooni.com/docs/groovy-menu/" target="_blank">' . esc_html__( 'Docs', 'groovy-menu' ) . '</a>';
			$links[] = '<a href="https://grooni.ticksy.com/" target="_blank"">' . esc_html__( 'Get Support', 'groovy-menu' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Adds plugin action links to the plugin in the WP Admin > Plugins screen
	 *
	 * @param array $actions
	 * @param string $plugin_file
	 *
	 * @return array
	 */
	public static function gm_plugin_page_links( $actions, $plugin_file ) {

		$basename = defined( 'GROOVY_MENU_BASENAME' ) ? GROOVY_MENU_BASENAME : 'groovy-menu.php';

		if ( false === strpos( $plugin_file, $basename ) ) {
			return $actions;
		}

		$settings_link = '<a href="' . admin_url( 'admin.php?page=groovy_menu_settings' ) . '" target="_blank">' . esc_html__( 'Settings', 'groovy-menu' ) . '</a>';
		array_unshift( $actions, $settings_link );

		$lver = false;
		if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
			$lver = true;
		}

		if ( $lver && 'groovy-menu-free/groovy-menu.php' === $plugin_file ) {
			$addcss_arr = array(
				'color'       => '#3e9e16',
				'font-weight' => 'bold',
			);
			$addcss     = '';
			foreach ( $addcss_arr as $index => $item ) {
				$addcss .= $index . ': ' . $item . ';';
			}
			$addcss = 'style' . '="' . $addcss . '"';
			$upgrade_link = '<a href="https://groovymenu.grooni.com/upgrade/" target="_blank"><span ' . $addcss . '>' . esc_html__( 'Upgrade to Pro', 'groovy-menu' ) . '</span></a>';
			array_unshift( $actions, $upgrade_link );
		}

		return $actions;
	}


	/**
	 * Install default fonts.
	 *
	 * @param bool $self_install
	 *
	 * @return null|void
	 */
	public static function install_default_icon_packs( $self_install = false ) {

		$message        = 'done';
		$uploaded_fonts = \GroovyMenu\FieldIcons::getFonts();
		$default_packs  = GroovyMenuUtils::get_default_icon_packs_list();

		if ( is_array( $uploaded_fonts ) && ! empty( $uploaded_fonts ) ) {
			foreach ( $uploaded_fonts as $index => $_font ) {
				if ( isset( $_font['name'] ) && isset( $default_packs[ $_font['name'] ] ) ) {
					unset( $default_packs[ $_font['name'] ] );
				}
			}
		}

		if ( ! empty( $default_packs ) ) {

			if ( class_exists( 'ZipArchive' ) ) {

				if ( ! defined( 'FS_METHOD' ) ) {
					define( 'FS_METHOD', 'direct' );
				}

				global $wp_filesystem;
				if ( empty( $wp_filesystem ) ) {
					if ( file_exists( ABSPATH . '/wp-admin/includes/file.php' ) ) {
						require_once ABSPATH . '/wp-admin/includes/file.php';
						WP_Filesystem();
					}
				}
				if ( empty( $wp_filesystem ) ) {
					@ob_clean();

					if ( ! $self_install ) {
						wp_send_json( array(
							'status'  => 'critical_error',
							'message' => esc_html__( 'WP_Filesystem() load library error', 'groovy-menu' )
						), 500 );
					} else {
						return null;
					}
				}


				foreach ( $default_packs as $index => $default_pack ) {

					$url = $default_pack['url'];

					// create temp folder
					$_tmp = wp_tempnam( $url );
					@unlink( $_tmp );

					$package = download_url( $url, 360 );

					if ( is_wp_error( $package ) ) {
						continue;
					}

					$zip = new \ZipArchive();
					if ( $zip->open( $package ) ) {
						$fonts = \GroovyMenu\FieldIcons::getFonts();

						$selection     = $zip->getFromName( 'selection.json' );
						$selectionData = json_decode( $selection, true );
						$name          = $default_pack['internal_name'];

						$fontFiles['woff'] = $zip->getFromName( 'fonts/' . $selectionData['metadata']['name'] . '.woff' );
						$fontFiles['ttf']  = $zip->getFromName( 'fonts/' . $selectionData['metadata']['name'] . '.ttf' );
						$fontFiles['svg']  = $zip->getFromName( 'fonts/' . $selectionData['metadata']['name'] . '.svg' );
						$fontFiles['eot']  = $zip->getFromName( 'fonts/' . $selectionData['metadata']['name'] . '.eot' );

						$dir = GroovyMenuUtils::getFontsDir();
						wp_mkdir_p( $dir );

						file_put_contents( $dir . $name . '.woff', $fontFiles['woff'] );
						file_put_contents( $dir . $name . '.ttf', $fontFiles['ttf'] );
						file_put_contents( $dir . $name . '.svg', $fontFiles['svg'] );
						file_put_contents( $dir . $name . '.eot', $fontFiles['eot'] );
						file_put_contents( $dir . $name . '.css',
							self::generate_fonts_css( $name, $selectionData ) );

						$icons = array();
						foreach ( $selectionData['icons'] as $icon ) {
							$icons[] = array(
								'name' => $icon['icon']['tags'][0],
								'code' => $icon['properties']['code']
							);
						}
						$fonts[ $name ] = array( 'icons' => $icons, 'name' => $selectionData['metadata']['name'] );
						\GroovyMenu\FieldIcons::setFonts( $fonts );
					}
				}
			} else {
				$message = esc_html__( "Wasn't able to work with Zip Archive. Missing php-zip extension.", 'groovy-menu' );
			}
		}

		if ( ! $self_install ) {
			$output = array( 'message' => $message );
			wp_die( wp_json_encode( $output ) );
		}
	}

	/**
	 * @param $name
	 * @param $selectionData
	 *
	 * @return string
	 */
	public static function generate_fonts_css( $name, $selectionData ) {
		$css = '
@font-face {
	font-family: \'' . $name . '\';
	src:url(\'' . $name . '.eot?jk3qnc\');
	src:url(\'' . $name . '.eot?jk3qnc#iefix\') format(\'embedded-opentype\'),
		url(\'' . $name . '.ttf?jk3qnc\') format(\'truetype\'),
		url(\'' . $name . '.woff?jk3qnc\') format(\'woff\'),
		url(\'' . $name . '.svg?jk3qnc#icomoon1\') format(\'svg\');
	font-weight: normal;
	font-style: normal;
}

[class^="' . $name . '"],
[class*=" ' . $name . '"] {
	font-family: \'' . $name . '\';
	speak: none;
	font-style: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
	line-height: 1;

	/* Enable Ligatures ================ */
	letter-spacing: 0;
	-webkit-font-feature-settings: "liga";
	-moz-font-feature-settings: "liga=1";
	-moz-font-feature-settings: "liga";
	-ms-font-feature-settings: "liga" 1;
	-o-font-feature-settings: "liga";
	font-feature-settings: "liga";

	/* Better Font Rendering =========== */
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;
}
';

		foreach ( $selectionData['icons'] as $icon ) {
			$iconName = $icon['icon']['tags'][0];
			$code     = dechex( $icon['properties']['code'] );
			$css      .= '.' . $name . '-' . $iconName . ':before { content: \'\\' . $code . '\'; }';

		}

		return $css;
	}

	/**
	 * Get default icon packs list
	 *
	 * @return array
	 */
	public static function get_default_icon_packs_list() {
		$packs = array(
			'wp-Ingenicons'     => array(
				'name'          => 'wp-Ingenicons',
				'url'           => 'https://license.grooni.com/wp-update-server/icon_packs/wp-Ingenicons.zip',
				'internal_name' => 'groovy-28328'
			),
			'Simple-Line-Icons' => array(
				'name'          => 'Simple-Line-Icons',
				'url'           => 'https://license.grooni.com/wp-update-server/icon_packs/Simple-Line-Icons.zip',
				'internal_name' => 'groovy-69018'
			),
			'socicon'           => array(
				'name'          => 'socicon',
				'url'           => 'https://license.grooni.com/wp-update-server/icon_packs/socicon.zip',
				'internal_name' => 'groovy-socicon'
			),
		);

		return $packs;
	}


	/**
	 * Short-circuit the wp_nav_menu() output if we have cached output ready.
	 *
	 * Returning a non-null value to the filter will short-circuit
	 * wp_nav_menu(), echoing that value if $args->echo is true,
	 * returning that value otherwise.
	 *
	 * @see wp_nav_menu()
	 *
	 * @param string|null $output Nav menu output to short-circuit with. Default null.
	 * @param stdClass    $args   An object containing wp_nav_menu() arguments.
	 *
	 * @return string|null Nav menu output to short-circuit with. Passthrough (default null) if we don’t have a cached version.
	 */
	public static function cache_pre_wp_nav_menu( $output, $args ) {

		static $gm_nav_menu_items = array();
		static $menu_id_slugs = array();

		if ( empty( $args->gm_preset_id ) || ! isset( $args->gm_echo ) ) {
			return $output;
		}

		/* This section is from wp_nav_menu(). It is here to find a menu when none is provided. */
		// @codingStandardsIgnoreStart

		// Get the nav menu based on the requested menu
		$menu = wp_get_nav_menu_object( $args->menu );

		// Get the nav menu based on the theme_location
		if ( ! $menu && $args->theme_location && ( $locations = get_nav_menu_locations() ) && isset( $locations[ $args->theme_location ] ) ) {
			$menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );
		}

		// get the first menu that has items if we still can't find a menu
		if ( ! $menu && ! $args->theme_location ) {
			$menus = wp_get_nav_menus();
			foreach ( $menus as $menu_maybe ) {
				if ( $menu_items = wp_get_nav_menu_items( $menu_maybe->term_id, array( 'update_post_term_cache' => false ) ) ) {
					$menu = $menu_maybe;
					break;
				}
			}
		}

		if ( empty( $args->menu ) ) {
			$args->menu = $menu;
		}

		// If the menu exists, get its items.
		if ( $menu && ! is_wp_error( $menu ) && ! isset( $menu_items ) ) {
			// DiS. GM cache condition.
			if ( empty( $gm_nav_menu_items[ $menu->term_id ] ) ) {
				$menu_items                          = wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );
				$gm_nav_menu_items[ $menu->term_id ] = $menu_items;
				global $groovyMenuSettings;
				$groovyMenuSettings['nav_menu_data']['data'][ $menu->term_id ] = $menu_items;
			} else {
				$menu_items = $gm_nav_menu_items[ $menu->term_id ];
			}
		}

		/*
		 * If no menu was found:
		 *  - Fall back (if one was specified), or bail.
		 *
		 * If no menu items were found:
		 *  - Fall back, but only if no theme location was specified.
		 *  - Otherwise, bail.
		 */
		if ( ( ! $menu || is_wp_error( $menu ) || ( isset( $menu_items ) && empty( $menu_items ) && ! $args->theme_location ) )
		     && isset( $args->fallback_cb ) && $args->fallback_cb && is_callable( $args->fallback_cb ) ) {
			return call_user_func( $args->fallback_cb, (array) $args );
		}

		if ( ! $menu || is_wp_error( $menu ) ) {
			return false;
		}

		$nav_menu = $items = '';

		$show_container = false;
		if ( $args->container ) {
			/**
			 * Filters the list of HTML tags that are valid for use as menu containers.
			 *
			 * @since 3.0.0
			 *
			 * @param array $tags The acceptable HTML tags for use as menu containers.
			 *                    Default is array containing 'div' and 'nav'.
			 */
			$allowed_tags = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'nav' ) );
			if ( is_string( $args->container ) && in_array( $args->container, $allowed_tags ) ) {
				$show_container = true;
				$class          = $args->container_class ? ' class="' . esc_attr( $args->container_class ) . '"' : ' class="menu-' . $menu->slug . '-container"';
				$id             = $args->container_id ? ' id="' . esc_attr( $args->container_id ) . '"' : '';
				$nav_menu       .= '<' . $args->container . $id . $class . '>';
			}
		}

		// Set up the $menu_item variables
		_wp_menu_item_classes_by_context( $menu_items );

		$sorted_menu_items = $menu_items_with_children = array();
		foreach ( (array) $menu_items as $menu_item ) {
			$sorted_menu_items[ $menu_item->menu_order ] = $menu_item;
			if ( $menu_item->menu_item_parent ) {
				$menu_items_with_children[ $menu_item->menu_item_parent ] = true;
			}
		}

		// Add the menu-item-has-children class where applicable
		if ( $menu_items_with_children ) {
			foreach ( $sorted_menu_items as &$menu_item ) {
				if ( isset( $menu_items_with_children[ $menu_item->ID ] ) ) {
					$menu_item->classes[] = 'menu-item-has-children';
				}
			}
		}

		unset( $menu_items, $menu_item );

		/**
		 * Filters the sorted list of menu item objects before generating the menu's HTML.
		 *
		 * @since 3.1.0
		 *
		 * @param array    $sorted_menu_items The menu items, sorted by each menu item's menu order.
		 * @param stdClass $args              An object containing wp_nav_menu() arguments.
		 */
		$sorted_menu_items = apply_filters( 'wp_nav_menu_objects', $sorted_menu_items, $args );

		$items .= walk_nav_menu_tree( $sorted_menu_items, $args->depth, $args );
		unset( $sorted_menu_items );

		// Attributes
		if ( ! empty( $args->menu_id ) ) {
			$wrap_id = $args->menu_id;
		} else {
			$wrap_id = 'menu-' . $menu->slug;
			while ( in_array( $wrap_id, $menu_id_slugs ) ) {
				if ( preg_match( '#-(\d+)$#', $wrap_id, $matches ) ) {
					$wrap_id = preg_replace( '#-(\d+)$#', '-' . ++ $matches[1], $wrap_id );
				} else {
					$wrap_id = $wrap_id . '-1';
				}
			}
		}
		$menu_id_slugs[] = $wrap_id;

		$wrap_class = $args->menu_class ? $args->menu_class : '';

		/**
		 * Filters the HTML list content for navigation menus.
		 *
		 * @since 3.0.0
		 *
		 * @see   wp_nav_menu()
		 *
		 * @param string   $items The HTML list content for the menu items.
		 * @param stdClass $args  An object containing wp_nav_menu() arguments.
		 */
		$items = apply_filters( 'wp_nav_menu_items', $items, $args );
		/**
		 * Filters the HTML list content for a specific navigation menu.
		 *
		 * @since 3.0.0
		 *
		 * @see   wp_nav_menu()
		 *
		 * @param string   $items The HTML list content for the menu items.
		 * @param stdClass $args  An object containing wp_nav_menu() arguments.
		 */
		$items = apply_filters( "wp_nav_menu_{$menu->slug}_items", $items, $args );

		// Don't print any markup if there are no items at this point.
		if ( empty( $items ) ) {
			return false;
		}

		$nav_menu .= sprintf( $args->items_wrap, esc_attr( $wrap_id ), esc_attr( $wrap_class ), $items );
		unset( $items );

		if ( $show_container ) {
			$nav_menu .= '</' . $args->container . '>';
		}

		/**
		 * Filters the HTML content for navigation menus.
		 *
		 * @since 3.0.0
		 *
		 * @see   wp_nav_menu()
		 *
		 * @param string   $nav_menu The HTML content for the navigation menu.
		 * @param stdClass $args     An object containing wp_nav_menu() arguments.
		 */
		$nav_menu = apply_filters( 'wp_nav_menu', $nav_menu, $args );

		if ( $args->echo ) {
			echo $nav_menu;
		} else {
			return $nav_menu;
		}

		// @codingStandardsIgnoreEnd
		/* End of the section from wp_nav_menu(). It was a pleasure, ladies and gents. */
	}


	public static function show_gm_php_version() {
		?>

		<div id="gm-upgrade-notice" class="notice-error settings-error notice is-dismissible">
			<p class="gm-install-addons-text-block">
				<?php
				esc_html_e( 'Plugin Groovy Menu  is deactivated. Minimum required version for PHP 7.', 'groovy-menu' );
				echo ' <a href="' . get_admin_url( null, 'plugins.php', 'relative' ) . '">';
				esc_html_e( 'Plugins page', 'groovy-menu' );
				echo '</a> ';
				?>
				<br>
			</p>
		</div>
		<?php
	}


	/**
	 * Short-circuit the wp_nav_menu() output if we have cached Groovy Menu markup ready.
	 *
	 * Returning a non-null value to the filter will short-circuit
	 * wp_nav_menu(), echoing that value if $args->echo is true,
	 * returning that value otherwise.
	 *
	 * @see wp_nav_menu()
	 *
	 * @param string|null $output Nav menu output to short-circuit with. Default null.
	 * @param stdClass    $args   An object containing wp_nav_menu() arguments.
	 *
	 * @return string|null Nav menu output to short-circuit with. Passthrough (default null) if we don’t have a cached version.
	 */
	public static function add_groovy_menu_as_wp_nav_menu( $output, $args ) {

		// Prevent recursion by call wp_nav_menu()
		if ( ( isset( $args->groovy_menu ) && $args->groovy_menu ) || isset( $args->gm_pre_storage ) && $args->gm_pre_storage ) {
			return $output;
		}

		$gm_html = '';

		$saved_integration_location = self::getSingleLocationIntegration();

		if ( ! $output && ! empty( $saved_integration_location ) && isset( $args->theme_location ) && esc_attr( $saved_integration_location ) === $args->theme_location ) {

			$gm_ids = \GroovyMenu\PreStorage::get_instance()->search_ids_by_location( array( 'theme_location' => 'gm_primary' ) );

			if ( ! empty( $gm_ids ) ) {
				foreach ( $gm_ids as $gm_id ) {
					$gm_data = \GroovyMenu\PreStorage::get_instance()->get_gm( $gm_id );
					$gm_html .= $gm_data['gm_html'];

				}
			} else {
				$gm_html .= groovy_menu( [
					'gm_echo'        => false,
					'theme_location' => 'gm_primary',
				] );
			}

		}

		if ( ! empty( $gm_html ) ) {
			$output = $gm_html;
		}

		return $output;

	}


	public static function update_config_text_domain() {
		$config_global   = include GROOVY_MENU_DIR . 'includes/config/ConfigGlobal.php';
		$settings_global = \GroovyMenu\StyleStorage::getInstance()->get_global_settings();

		$updated = false;

		if ( ! empty( $settings_global ) && is_array( $settings_global ) && is_array( $config_global ) ) {

			foreach ( $config_global as $category_name => $category ) {

				if ( isset( $category['title'] ) && isset( $settings_global[ $category_name ]['title'] ) ) {
					if ( $settings_global[ $category_name ]['title'] !== $category['title'] ) {
						$settings_global[ $category_name ]['title'] = $category['title'];

						$updated = true;
					}
				}

				if ( isset( $category['fields'] ) ) {
					foreach ( $category['fields'] as $field => $config_value ) {
						if ( isset( $settings_global[ $category_name ]['fields'][ $field ] ) ) {

							$settings_global_value = $settings_global[ $category_name ]['fields'][ $field ];

							if ( isset( $config_value['title'] ) && isset( $settings_global_value['title'] ) ) {
								if ( $settings_global_value['title'] !== $config_value['title'] ) {
									$settings_global[ $category_name ]['fields'][ $field ]['title'] = $config_value['title'];

									$updated = true;
								}
							}
							if ( isset( $config_value['description'] ) && isset( $settings_global_value['description'] ) ) {
								if ( $settings_global_value['description'] !== $config_value['description'] ) {
									$settings_global[ $category_name ]['fields'][ $field ]['description'] = $config_value['description'];

									$updated = true;
								}
							}
							if ( isset( $config_value['options'] ) && isset( $settings_global_value['options'] ) && is_array( $config_value['options'] ) ) {
								foreach ( $config_value['options'] as $index => $option ) {

									if ( empty( $settings_global_value['options'][ $index ] ) ) {
										continue;
									}

									if ( $settings_global_value['options'][ $index ] !== $config_value['options'][ $index ] ) {
										$settings_global[ $category_name ]['fields'][ $field ]['options'][ $index ] = $config_value['options'][ $index ];

										$updated = true;
									}
								}
							}
						}
					}
				}

			}

			if ( $updated ) {
				\GroovyMenu\StyleStorage::getInstance()->set_global_settings( $settings_global );
			}

		}

	}

	
	public static function l10n( $for_admin = true ) {
		$groovyMenuL10n = array();

		if ( $for_admin ) {
			$groovyMenuL10n['save_alert'] = esc_html__( 'The changes you made will be lost if you navigate away from this page.', 'groovy-menu' );
		} else {
			// yet empty ...
		}

		return $groovyMenuL10n;
	}
	

	public static function clean_output( $text ) {
		$text = trim( $text );

		return $text;
	}


	/**
	 * Lic checker.
	 *
	 * @param bool $immediately Check without long timer
	 *
	 * @return bool|string
	 */
	public static function check_lic( $immediately = false ) {
		if ( ! $immediately && get_transient( GROOVY_MENU_DB_VER_OPTION . '__lic_cache' ) ) {
			$lic_opt = get_option( GROOVY_MENU_DB_VER_OPTION . '__lic' );
			if ( empty( $lic_opt ) || ! $lic_opt ) {
				return false;
			} else {
				return $lic_opt;
			}
		}

		$transient_timer = 2 * MINUTE_IN_SECONDS; // by default

		global $gm_supported_module;

		$check_url = 'https://license.grooni.com/user-dashboard/?glm_action=check&glm_page=product';

		$check_url .= '&glm_product=groovy-menu';
		$check_url .= '&glm_theme=' . $gm_supported_module['theme'];
		$check_url .= '&glm_rs=' . rawurlencode( get_site_url() );

		$body = wp_remote_get( $check_url );

		if ( is_wp_error( $body ) ) {
			$error_msg = $body->get_error_code() . ' * ' . $body->get_error_message() . ' * ' . $body->get_error_data();
			$body      = '{}';

			$gm_supported_module['lic_check_error'] = $error_msg;

			add_action( 'gm_before_welcome_output', function () {
				global $gm_supported_module;
				if ( ! empty( $gm_supported_module['lic_check_error'] ) ) {
					echo '<div class="gm-lic-check-error">' . $gm_supported_module['lic_check_error'] . '</div>';
				}
			} );

			$transient_timer = 5 * MINUTE_IN_SECONDS;

		} elseif ( isset( $body['body'] ) ) {
			$body = $body['body'];
		} else {
			$body = '{}';
		}

		$body    = json_decode( $body, true );
		$lic_opt = false; // by default.

		if ( is_array( $body ) && isset( $body['approve'] ) ) {
			if ( $body['approve'] === true ) {
				update_option( GROOVY_MENU_DB_VER_OPTION . '__lic', GROOVY_MENU_VERSION );
				$lic_opt = true;
				$transient_timer = 4 * HOUR_IN_SECONDS;
			} elseif ( $body['approve'] === false ) {
				update_option( GROOVY_MENU_DB_VER_OPTION . '__lic', false );
				$lic_opt = false;
				$transient_timer = 3 * MINUTE_IN_SECONDS;
			}

			$body['gm_version'] = GROOVY_MENU_VERSION;

			update_option( GROOVY_MENU_DB_VER_OPTION . '__lic_data', $body );
		} else {
			update_option( GROOVY_MENU_DB_VER_OPTION . '__lic_data', array( 'gm_version' => GROOVY_MENU_VERSION ) );
		}

		set_transient( GROOVY_MENU_DB_VER_OPTION . '__lic_cache', true, $transient_timer );

		return $lic_opt;
	}


	public static function get_paramlic( $field ) {
		$answer = '';

		$data = get_option( GROOVY_MENU_DB_VER_OPTION . '__lic_data' );
		if ( ! empty( $field ) && is_array( $data ) && isset( $data[ $field ] ) ) {
			$answer = $data[ $field ];
		}

		if ( 'type' === $field && empty( $answer ) ) {
			$answer = 'regular';
		}

		return $answer;
	}


	/**
	 * Check license supported until param.
	 *
	 * @return bool|int
	 */
	public static function check_lic_supported_until() {
		$answer = false; // by default

		$supported_until = self::get_paramlic( 'supported_until' );

		if ( ! empty( $supported_until ) ) {

			$until_date   = strtotime( date( "c", strtotime( $supported_until ) ) );
			$current_date = strtotime( date( "c" ) );

			if ( $until_date >= $current_date ) {
				$answer = $until_date;
			}

		}

		return $answer;
	}


	/**
	 * Notation to numbers.
	 *
	 * This function transforms the php.ini notation for numbers (like '2M') to an integer.
	 *
	 * @param  string $size Size value.
	 *
	 * @return int
	 */
	public static function let_to_num( $size ) {
		$l   = substr( $size, - 1 );
		$ret = (int) substr( $size, 0, - 1 );
		switch ( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			// No break.
			case 'T':
				$ret *= 1024;
			// No break.
			case 'G':
				$ret *= 1024;
			// No break.
			case 'M':
				$ret *= 1024;
			// No break.
			case 'K':
				$ret *= 1024;
			// No break.
		}

		return $ret;
	}


	/**
	 * Numbers to Notation.
	 *
	 * @param     $bytes
	 * @param int $precision
	 *
	 * @return int
	 */
	public static function num_to_let( $bytes, $precision = 2 ) {
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );

		$bytes = max( $bytes, 0 );
		$pow   = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow   = min( $pow, count( $units ) - 1 );

		$bytes /= ( 1 << ( 10 * $pow ) );

		return round( $bytes, $precision ) . $units[ $pow ];
	}


	/**
	 * Get system info
	 *
	 * @param string $return_type wait 'array' or 'html'.
	 * @param array  $params      list of params for output (filter), or leave empty for all.
	 *
	 * @return array|string
	 */
	public static function get_environment_info( $return_type = 'array', $params = array() ) {
		$info = array();

		// PHP version
		$php_version = phpversion();
		$str_pos     = strpos( $php_version, '-' );
		if ( $str_pos ) {
			$php_version = substr( $php_version, 0, $str_pos );
		}

		// PHP version.
		$info['php_version'] = array(
			'title' => __( 'PHP 7.0 or greater', 'groovy-menu' ),
			'value' => $php_version,
			'pass'  => ( version_compare( $php_version, '7.0', '>' ) ) ? true : false // more then 7.0
		);

		// ZipArchive php module.
		$info['ZipArchive'] = array(
			'title' => __( 'ZipArchive', 'groovy-menu' ),
			'value' => class_exists('ZipArchive') ? __( 'Installed', 'groovy-menu' ) : __( 'Fail', 'groovy-menu' ),
			'pass'  => class_exists( 'ZipArchive' ) ? true : false,
		);

		// Memory limit.
		$wp_memory_limit = self::let_to_num( WP_MEMORY_LIMIT );
		if ( function_exists( 'memory_get_usage' ) ) {
			$wp_memory_limit = max( $wp_memory_limit, self::let_to_num( @ini_get( 'memory_limit' ) ) );
		}

		$info['memory_limit'] = array(
			'title' => 'PHP Memory limit',
			'value' => self::num_to_let( $wp_memory_limit ),
			'pass'  => ( $wp_memory_limit > 67107864 ) ? true : false, // more then 64M
		);

		// php_max_input_vars
		$php_max_input_vars         = intval( @ini_get( 'max_input_vars' ) );
		$info['php_max_input_vars'] = array(
			'title'          => __( 'PHP Max input vars', 'groovy-menu' ),
			'value'          => $php_max_input_vars,
			'desc'           => __( 'Matters to the Appearance - Menus editor', 'groovy-menu' ),
			'recommend_desc' => sprintf( __( 'Current Max input vars is OK, however %s is recommended for the correct operation of all the function.', 'groovy-menu' ), '1000' ),
			'recommend'      => ( $php_max_input_vars >= 200 ) ? true : false, // minimum 200
			'pass'           => ( $php_max_input_vars >= 1000 ) ? true : false, // minimum 1000
		);

		// php_max_input_vars
		$php_max_execution_time         = intval( @ini_get( 'max_execution_time' ) );
		$info['php_max_execution_time'] = array(
			'title'          => __( 'PHP Max execution time', 'groovy-menu' ),
			'value'          => $php_max_execution_time,
			'desc'           => __( 'php.ini values show above. Real values may vary.', 'groovy-menu' ),
			'recommend_desc' => __( 'Current max execution time is OK, however 180 is recommended.', 'groovy-menu' ),
			'recommend'      => ( $php_max_execution_time >= 30 ) ? true : false, // minimum 60
			'pass'           => ( $php_max_execution_time >= 180 ) ? true : false, // minimum 180
		);

		// Filter.
		if ( ! empty( $params ) ) {
			$_info = array();
			foreach ( $params as $param ) {
				if ( isset( $info[ $param ] ) ) {
					$_info[ $param ] = $info[ $param ];
				}
			}
			$info = $_info;
		}

		// Sent HTML if so
		if ( 'html' === $return_type ) {
			$evenodd = 1;
			$html    = '<div class="gm-sysinfo--wrapper">';

			foreach ( $info as $index => $item ) {
				// Description.
				$desc = '';
				if ( ! $item['pass'] && ! empty( $item['recommend'] ) && $item['recommend'] && ! empty( $item['recommend_desc'] ) ) {
					$desc .= '<p class="gm-sysinfo--desc">' . $item['recommend_desc'] . '</p>';
				}
				if ( ! empty( $item['desc'] ) ) {
					$desc .= '<p class="gm-sysinfo--desc">' . $item['desc'] . '</p>';
				}

				// Check true or false.
				$pass = '<span class="gm-sysinfo--pass-ok dashicons dashicons-yes"></span>';
				if ( ! $item['pass'] ) {
					$pass = '<span class="gm-sysinfo--pass-fail dashicons dashicons-dismiss"></span>';
				}
				if ( ! $item['pass'] && ! empty( $item['recommend'] ) && $item['recommend'] ) {
					$pass = '<span class="gm-sysinfo--pass-warn dashicons dashicons-warning"></span>';
				}

				$html .= '<div class="gm-sysinfo--row' . ( ( $evenodd % 2 === 0 ) ? ' gm-sysinfo--row-even' : '' ) . '">';
				$html .= '	<div class="gm-sysinfo--title"><span>' . $item['title'] . '</span>' . $desc . '</div>';
				$html .= '	<div class="gm-sysinfo--pass">' . $pass . '</div>';
				$html .= '	<div class="gm-sysinfo--value">' . $item['value'] . '</div>';
				$html .= '</div>';

				$evenodd ++;
			}

			$html .= '</div>';

			return $html;
		}


		// return array by default
		return $info;
	}


	/**
	 * Redirect page.
	 *
	 * @param string $url
	 *
	 */
	public static function safe_redirect( $url ) {
		$url = empty( $url ) ? '' : $url;

		if ( empty( $url ) ) {
			wp_die();
		}

		$tagn = 'script type="text/javascript"';
		$tagt = 'type="text/javascript"';
		$text = 'window.location.replace("' . $url . '");';

		echo '<' . $tagn . ' ' . $tagt . '>' . $text . '</' . $tagn . '>';

	}


	/**
	 * Detect different WP content builders.
	 *
	 * @return false|string
	 */
	public static function check_wp_builders() {
		$detected = false;

		if ( isset( $_GET['fl_builder'] ) ) { // @codingStandardsIgnoreLine
			$detected = 'fl_builder';
		}

		// Elementor builder.
		if ( isset( $_GET['elementor-preview'] ) ) { // @codingStandardsIgnoreLine
			$detected = 'elementor';
		}

		// Divi builder.
		$rest_post_types = array(
			'et_header_layout',
			'et_body_layout',
			'et_footer_layout',
		);
		if (
			in_array( get_post_type(), $rest_post_types, true ) ||
			! empty( $_GET['et_fb'] ) ||
			! empty( $_GET['et_pb_preview'] )
		) {
			$detected = 'divi_builder';
		}

		// Avada theme / Fusion builder.
		if ( defined( 'AVADA_VERSION' ) && isset( $_GET['builder'] ) && 'true' === $_GET['builder'] ) { // @codingStandardsIgnoreLine
			$detected = 'fusion_builder';
		}
		if ( defined( 'AVADA_VERSION' ) && ! empty( $_GET['fb-edit'] ) ) { // @codingStandardsIgnoreLine
			$detected = 'fusion_builder';
		}

		return $detected;
	}

}

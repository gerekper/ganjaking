<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuMenuBlockPostType
 */
class GroovyMenuMenuBlockPostType {

	public function __construct() {
		add_action( 'init', array( $this, 'add_menu_block_post_type' ) );
		add_action( 'template_redirect', array( $this, 'pages_redirect' ), 1 );
		add_filter( 'post_updated_messages', array( $this, 'remove_view_link' ) );
		add_filter( 'post_row_actions', array( $this, 'remove_post_row_view_link' ) );
		add_filter( 'custom_menu_order', '__return_true' );
		add_filter( 'gutenberg_can_edit_post_type', array( $this, 'gutenberg_can_edit_post_type' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'change_admin_menu_order' ), 999 );
	}

	/**
	 * Register gm_menu_block post type
	 */
	public function add_menu_block_post_type() {
		$show_in_menu = false;
		if ( GroovyMenuRoleCapabilities::blockRead( true ) ) {
			$show_in_menu = 'groovy_menu_welcome';
		}

		$lic_opt = get_option( GROOVY_MENU_DB_VER_OPTION . '__lic' );
		if ( ! $lic_opt ) {
			$show_in_menu = false;
		}

		$capabilities = array(
			'edit_post'           => 'groovy_menu_edit_block',
			'read_post'           => 'groovy_menu_read_block',
			'delete_post'         => 'groovy_menu_delete_block',
			'delete_posts'        => 'groovy_menu_delete_blocks',
			'delete_others_posts' => 'groovy_menu_delete_others_blocks',
			'edit_posts'          => 'groovy_menu_edit_blocks',
			'edit_others_posts'   => 'groovy_menu_edit_others_blocks',
			'publish_posts'       => 'groovy_menu_publish_blocks',
			'read_private_posts'  => 'groovy_menu_read_private_blocks',
			'create_posts'        => 'groovy_menu_create_block',
		);

		$args = array(
			'labels'              => array(
				'name'          => __( 'Menu blocks', 'groovy-menu' ),
				'singular_name' => __( 'Menu block', 'groovy-menu' ),
				'add_new'       => __( 'Add New Menu block', 'groovy-menu' ),
				'add_new_item'  => __( 'Add New Menu block', 'groovy-menu' ),
				'edit_item'     => __( 'Edit Menu block', 'groovy-menu' ),
			),
			'public'              => true,
			'show_in_menu'        => $show_in_menu,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => true,
			'supports'            => array(
				'title',
				'editor',
				'revisions',
			),
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'rest_base'           => null,
			'menu_position'       => 100,
			'menu_icon'           => null,
			'hierarchical'        => false,
			'taxonomies'          => array(),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => true,
			'capability_type'     => 'gm_menu_block',
			'capabilities'        => $capabilities,
		);

		register_post_type( 'gm_menu_block', $args );

	}

	/**
	 * Enable the Gutenberg editor for gm_menu_block post type.
	 *
	 * @param bool   $can_edit  Whether to use the Gutenberg editor.
	 * @param string $post_type Name of WordPress post type.
	 *
	 * @return bool  $can_edit
	 */
	public function gutenberg_can_edit_post_type( $can_edit, $post_type ) {
		return 'gm_menu_block' === $post_type ? true : $can_edit;
	}


	public function pages_redirect() {
		$exclusion = false;

		if ( isset( $_GET['fl_builder'] ) ) { // @codingStandardsIgnoreLine
			$exclusion = true;
		}

		if ( isset( $_GET['elementor-preview'] ) ) { // @codingStandardsIgnoreLine
			$exclusion = true;
		}

		if ( ! is_preview() && 'gm_menu_block' === get_post_type() && ! $exclusion ) {
			wp_safe_redirect( esc_url_raw( home_url() ), 301 );
			exit();
		}
	}

	public function remove_view_link( $messages ) {

		if ( 'gm_menu_block' === get_post_type() && is_array( $messages ) ) {

			foreach ( $messages as $post_type => $post_data ) {

				foreach ( $post_data as $key => $data ) {
					preg_match( '# ?<a(.+)crane_footer=(.+)<\/a>#im', $data, $matches );
					if ( ! empty( $matches[0] ) ) {
						$messages[ $post_type ][ $key ] = str_replace( $matches[0], '', $messages[ $post_type ][ $key ] );
					}
				}

			}

		}

		return $messages;

	}

	public function remove_post_row_view_link( $actions, $post = 0 ) {
		if ( 'gm_menu_block' === get_post_type() ) {
			if ( isset( $actions['view'] ) ) {
				unset( $actions['view'] );
			}
		}

		return $actions;
	}

	/**
	 * Custom order for sub items of groovy_menu_welcome
	 *
	 */
	public function change_admin_menu_order() {
		global $menu;
		global $submenu;

		// check empty.
		if ( empty( $submenu['groovy_menu_welcome'] ) || ! is_array( $submenu['groovy_menu_welcome'] ) ) {
			return;
		}

		$gm_submenus = $submenu['groovy_menu_welcome'];

		$correct_order = array(
			'groovy_menu_welcome'              => array(),
			'groovy_menu_settings'             => array(),
			'edit.php?post_type=gm_menu_block' => array(),
			'groovy_menu_menus'                => array(),
		);

		// do new oreder.
		foreach ( $correct_order as $item_slug => $item_order ) {
			foreach ( $gm_submenus as $index => $gm_submenu ) {
				if ( ! empty( $correct_order[ $item_slug ] ) ) {
					continue;
				}
				if ( ! empty( $gm_submenu[2] ) && $item_slug === $gm_submenu[2] ) {
					$correct_order[ $item_slug ] = $gm_submenu;
				}
			}
		}

		// add missing items.
		foreach ( $gm_submenus as $index => $gm_submenu ) {
			if ( ! empty( $gm_submenu[2] ) && empty( $correct_order[ $gm_submenu[2] ] ) ) {
				$correct_order[ $gm_submenu[2] ] = $gm_submenu;
			}
		}

		// clear empty items.
		foreach ( $correct_order as $index => $item ) {
			if ( empty( $item ) ) {
				unset( $correct_order[ $index ] );
			}
		}


		$submenu['groovy_menu_welcome'] = $correct_order;
	}

}


// TODO separate to self file
if ( class_exists( 'Ultimate_VC_Addons' ) ) {

	class Grooni_Ultimate_VC_Addons extends Ultimate_VC_Addons {
		public function __construct() {
			if ( ! defined( 'UAVC_DIR' ) ) {
				define( 'UAVC_DIR', plugin_dir_path( trailingslashit( WP_PLUGIN_DIR ) . 'Ultimate_VC_Addons/Ultimate_VC_Addons.php' ) );
			}
			if ( ! defined( 'UAVC_URL' ) ) {
				define( 'UAVC_URL', plugins_url( '/', trailingslashit( WP_PLUGIN_DIR ) . 'Ultimate_VC_Addons/Ultimate_VC_Addons.php' ) );
			}
			$this->vc_template_dir = UAVC_DIR . 'vc_templates/';
			$this->vc_dest_dir     = get_template_directory() . '/vc_templates/';
			$this->module_dir      = UAVC_DIR . 'modules/';
			$this->params_dir      = UAVC_DIR . 'params/';
			$this->assets_js       = UAVC_URL . 'assets/js/';
			$this->assets_css      = UAVC_URL . 'assets/css/';
			$this->admin_js        = UAVC_URL . 'admin/js/';
			$this->admin_css       = UAVC_URL . 'admin/css/';

			$this->paths          = wp_upload_dir();
			$this->paths['fonts'] = 'smile_fonts';

			$scheme = is_ssl() ? 'https' : 'http';

			$this->paths['fonturl'] = set_url_scheme( $this->paths['baseurl'] . '/' . $this->paths['fonts'], $scheme );
		}
	}

}

// Start pre storage (compile groovy menu preset and nav_menu) before template.
if ( ! is_admin() && ! gm_is_wplogin() ) {
	add_action( 'wp_enqueue_scripts', 'groovy_menu_add_custom_styles', 10100 );
	add_action( 'wp_enqueue_scripts', 'groovy_menu_add_custom_styles_support', 10095 );
	add_action( 'wp_footer', 'groovy_menu_add_custom_styles' );
}

if ( ! function_exists( 'groovy_menu_add_custom_styles' ) ) {
	/**
	 * Output custom styles for vc.
	 *
	 * @param null|int $new_id for collect post ids.
	 */
	function groovy_menu_add_custom_styles( $new_id = null ) {
		static $post_ids      = array();
		static $already_added = false;

		if ( ! empty( $new_id ) ) {
			$post_ids[ $new_id ] = $new_id;

			return;
		}

		if ( empty( $post_ids ) || $already_added ) {
			return;
		}

		global $post;
		global $wp_query;

		foreach ( $post_ids as $post_id ) {
			$post_id = intval( $post_id );

			$wpml_post_id = apply_filters( 'wpml_object_id', $post_id, 'gm_menu_block', true );
			$post_id      = $wpml_post_id;

			if ( class_exists( 'Ultimate_VC_Addons' ) ) {

				// Copy global $post exemplar
				$_post    = $post;
				$sec_post = get_post( $post_id );
				$post     = $sec_post;

				$page_is = null;

				// Copy $wp_query
				$_wp_query = $wp_query;
				if ( is_404() ) {
					$wp_query->is_404 = false;
					$page_is          = 'is_404';
				}
				if ( is_search() ) {
					$wp_query->is_search = false;
					$page_is             = 'is_search';
				}


				if ( class_exists( 'Grooni_Ultimate_VC_Addons' ) ) {
					$instance = new Grooni_Ultimate_VC_Addons;
					$instance->aio_front_scripts();
				}

				if ( function_exists( 'enquque_ultimate_google_fonts_optimzed' ) ) {
					$post_content = apply_filters( 'ultimate_front_scripts_post_content', $post->post_content, $post );

					if ( stripos( $post_content, 'font_call:' ) ) {
						preg_match_all( '/font_call:(.*?)"/', $post_content, $display );

						gm_enqueue_ultimate_google_fonts_optimzed( $display[1] );
					}
				}

				if ( 'is_404' === $page_is ) {
					$wp_query->is_404 = true;
				}
				if ( 'is_search' === $page_is ) {
					$wp_query->is_search = true;
				}

				// Revert $wp_query
				$wp_query = $_wp_query;
				// Recovery global $post exemplar
				$post = $_post;

			}

			$post_custom_css = get_post_meta( $post_id, '_wpb_post_custom_css', true );
			if ( ! empty( $post_custom_css ) ) {
				$post_custom_css = strip_tags( $post_custom_css );
				echo '<style type="text/css" data-type="vc_custom-css">';
				echo $post_custom_css;
				echo '</style>';
			}

			$shortcodes_custom_css = get_post_meta( $post_id, '_wpb_shortcodes_custom_css', true );
			if ( ! empty( $shortcodes_custom_css ) ) {
				$shortcodes_custom_css = strip_tags( $shortcodes_custom_css );
				echo '<style type="text/css" data-type="vc_shortcodes-custom-css">';
				echo $shortcodes_custom_css;
				echo '</style>';
			}
		}

		$already_added = true;

	}
}


if ( ! function_exists( 'groovy_menu_add_custom_styles_support' ) ) {
	/**
	 * Output custom styles for different plugins.
	 *
	 * @param null|int $new_id for collect post ids.
	 */
	function groovy_menu_add_custom_styles_support( $new_id = null ) {
		static $post_ids = array();

		if ( ! empty( $new_id ) ) {
			$post_ids[ $new_id ] = $new_id;

			return;
		}

		if ( empty( $post_ids ) ) {
			return;
		}

		foreach ( $post_ids as $post_id ) {
			$post_id = intval( $post_id );

			$wpml_post_id = apply_filters( 'wpml_object_id', $post_id, 'gm_menu_block', true );
			$post_id      = $wpml_post_id;

			if ( class_exists( 'NectarElAssets' ) && function_exists( 'nectar_main_styles' ) && function_exists( 'nectar_page_sepcific_styles' ) ) {

				$content_copy = NectarElAssets::$post_content;
				$sec_post     = get_post( $post_id );

				NectarElAssets::$post_content = ( isset( $sec_post->post_content ) ) ? $sec_post->post_content : '';

				nectar_main_styles();
				nectar_page_sepcific_styles();

				NectarElAssets::$post_content = $content_copy;

			}
		}

	}
}


if ( ! function_exists( 'gm_enqueue_ultimate_google_fonts_optimzed' ) ) {

	/**
	 * Modified copy of function 'enquque_ultimate_google_fonts_optimzed'. Ultimate addons plugin for WPBackery.
	 *
	 * @param $enqueue_fonts
	 *
	 * @return string
	 */
	function gm_enqueue_ultimate_google_fonts_optimzed( $enqueue_fonts ) {

		static $font_stack = array();

		$selected_fonts    = apply_filters(
			'enquque_selected_ultimate_google_fonts',
			get_option( 'ultimate_selected_google_fonts' )
		);
		$skip_font_enqueue = apply_filters(
			'enquque_ultimate_google_fonts_skip',
			false
		);

		if ( true === boolval( $skip_font_enqueue ) ) {
			return '';
		}

		$main              = array();
		$subset_main_array = array();
		$fonts             = array();
		$subset_call       = '';

		if ( ! empty( $enqueue_fonts ) ) {
			$font_count = 0;
			foreach ( $enqueue_fonts as $key => $efont ) {
				$font_name = $font_call = $font_variant = '';
				$font_arr  = $font_call_arr = $font_weight_arr = array();
				$font_arr  = explode( '|', $efont );

				$font_name = trim( $font_arr[0] );

				if ( ! isset( $main[ $font_name ] ) ) {
					$main[ $font_name ] = array();
				}

				if ( ! empty( $font_name ) ):

					$font_count ++;
					if ( isset( $font_arr[1] ) ) {
						$font_call_arr = explode( ':', $font_arr[1] );

						if ( isset( $font_arr[2] ) ) {
							$font_weight_arr = explode( ':', $font_arr[2] );
						}

						if ( isset( $font_call_arr[1] ) && '' !== $font_call_arr[1] ) {
							$font_variant  = $font_call_arr[1];
							$pre_font_call = $font_name;

							if ( '' !== $font_variant && 'regular' !== $font_variant ) {
								$main[ $font_name ]['varients'][] = $font_variant;
								array_push( $main[ $font_name ]['varients'], $font_variant );
								if ( ! empty( $main[ $font_name ]['varients'] ) ) {
									$main[ $font_name ]['varients'] = array_values( array_unique( $main[ $font_name ]['varients'] ) );
								}
							}
						}
					}

					foreach ( $selected_fonts as $sfont ) {
						if ( $sfont['font_family'] == $font_name ) {
							if ( ! empty( $sfont['subsets'] ) ) {
								$subset_array = array();
								foreach ( $sfont['subsets'] as $tsubset ) {
									if ( $tsubset['subset_selected'] == 'true' ) {
										array_push( $subset_array, $tsubset['subset_value'] );
									}
								}
								if ( ! empty( $subset_array ) ) :
									$subset_call = '';
									$j           = count( $subset_array );
									foreach ( $subset_array as $subkey => $subset ) {
										$subset_call .= $subset;
										if ( ( $j - 1 ) != $subkey ) {
											$subset_call .= ',';
										}
									}
									array_push( $subset_main_array, $subset_call );
								endif;
							}
						}
					}
				endif;
			}

			$link          = 'https://fonts.googleapis.com/css?family=';
			$main_count    = count( $main );
			$mcount        = 0;
			$subset_string = '';

			foreach ( $main as $font => $font_data ) {
				if ( '' !== $font ) {
					$link .= $font;
					if ( 'Open+Sans+Condensed' === $font && empty( $font_data['varients'] ) ) {
						$link .= ':300';
					}
					if ( ! empty( $font_data['varients'] ) ) {
						$link         .= ':regular,';
						$varient_count = count( $font_data['varients'] );
						foreach ( $font_data['varients'] as $vkey => $varient ) {
							$link .= $varient;
							if ( ( $varient_count - 1 ) != $vkey ) {
								$link .= ',';
							}
						}
					}

					if ( ! empty( $font_data['subset'] ) ) {
						$subset_string .= '&subset=' . $font_data['subset'];
					}

					if ( $mcount != ( $main_count - 1 ) ) {
						$link .= '|';
					}
					$mcount ++;
				}
			}

			if ( ! empty( $subset_array ) ) {
				$subset_main_array = array_unique( $subset_main_array );

				$subset_string     = '&subset=';
				$subset_count      = count( $subset_main_array );
				$subset_main_array = array_values( $subset_main_array );

				foreach ( $subset_main_array as $skey => $subset ) {
					if ( $subset !== '' ) {
						$subset_string .= $subset;
						if ( ( $subset_count - 1 ) != $skey ) {
							$subset_string .= ',';
						}
					}
				}
			}

			$font_api_call = $link . $subset_string;
			$stack_key     = md5( $font_api_call );

			if ( $font_count > 0 && empty( $font_stack[ $stack_key ] ) ) {

				$font_stack[ $stack_key ] = $font_api_call;

				wp_enqueue_style( 'ultimate-google-fonts-' . $stack_key, $font_api_call, array(), null );
			}
		}
	}
}

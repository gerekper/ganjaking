<?php
/**
 * Plugin Name: Brainstorm Core
 * Plugin URI: https://brainstormforce.com
 * Author: Brainstorm Force
 * Author URI: https://brainstormforce.com
 * Version: 1.0
 * Description: Brainstorm Core
 *
 * @package BSF_Core
 * Text Domain: bsf
 */

/*
	Instrunctions - Product Registration & Updater
	# Copy "auto-upadater" folder to admin folder
	# Change "include_once" and "require_once" directory path as per your "auto-updater" path (Line no. 72, 78, 79)

*/

defined( 'ABSPATH' ) || exit;

// abspath of groupi.
if ( ! defined( 'BSF_UPDATER_PATH' ) ) {
	define( 'BSF_UPDATER_PATH', dirname( __FILE__ ) );
}

if ( ! defined( 'BSF_UPDATER_FILE' ) ) {
	define( 'BSF_UPDATER_FILE', __FILE__ );
}

if ( ! defined( 'BSF_UPDATER_FULLNAME' ) ) {
	define( 'BSF_UPDATER_FULLNAME', apply_filters( 'agency_updater_fullname', 'Brainstorm Force' ) );
}

if ( ! defined( 'BSF_UPDATER_SHORTNAME' ) ) {
	define( 'BSF_UPDATER_SHORTNAME', apply_filters( 'agency_updater_shortname', 'Brainstorm' ) );
}

if ( ! defined( 'BSF_UPDATER_VERSION' ) ) {
	global $bsf_core_version;
	define( 'BSF_UPDATER_VERSION', sanitize_text_field( $bsf_core_version ) );
}

// Do not initialize graupi in the customizer.

if ( is_customize_preview() ) {
	return;
}

/* product registration */
require_once 'includes/helpers.php';
require_once 'includes/system-info.php';
require_once 'auto-update/admin-functions.php';
require_once 'auto-update/updater.php';
require_once 'class-bsf-update-manager.php';
require_once 'class-bsf-rollback-version-manager.php';
require_once 'class-bsf-license-manager.php';
require_once 'classes/class-bsf-extension-installer.php';

require_once 'classes/class-bsf-core-update.php';
require_once 'classes/class-bsf-core-rest.php';
require_once 'classes/class-bsf-rollback-version.php';

if ( defined( 'WP_CLI' ) ) {
	require 'class-bsf-wp-cli-command.php';
}

add_action( 'admin_init', 'set_bsf_core_constant', 1 );
if ( ! function_exists( 'set_bsf_core_constant' ) ) {
	/**
	 *  Set BSF Core constant.
	 */
	function set_bsf_core_constant() {
		if ( ! defined( 'BSF_CORE' ) ) {
			define( 'BSF_CORE', true );
		}
	}
}

/*
	Instrunctions - Plugin Installer
	# Copy "plugin-installer" folder to theme's admin folder
	# Change "include_once" and "require_once" directory path as per your "plugin-installer" path (Line no. 101, 113)
*/

if ( ! function_exists( 'init_bsf_plugin_installer' ) ) {
	/**
	 * BSF Plugin installer.
	 */
	function init_bsf_plugin_installer() {
		require_once 'plugin-installer/admin-functions.php';

		/**
		 * Action will run after plugin installer is loaded
		 */
		do_action( 'bsf_after_plugin_installer' );
	}
}

add_action( 'admin_init', 'init_bsf_plugin_installer', 0 );
add_action( 'network_admin_init', 'init_bsf_plugin_installer', 0 );

if ( ! is_multisite() ) {
	add_action( 'admin_menu', 'register_bsf_extension_page', 999 );
} else {
	add_action( 'network_admin_menu', 'register_bsf_extension_page_network', 999 );
}

if ( ! function_exists( 'register_bsf_extension_page' ) ) {
	/**
	 * Register BSF extension Page.
	 */
	function register_bsf_extension_page() {
		add_submenu_page(
			'imedica_options',
			__( 'Extensions', 'bsf' ),
			__( 'Extensions', 'bsf' ),
			'manage_options',
			'bsf-extensions-10395942',
			'bsf_extensions_callback'
		);
	}
}
if ( ! function_exists( '_bsf_maybe_add_dashboard_menu' ) ) {

	/**
	 * Check if the dashboard menu for installer should be added.
	 * Checks if theme or plugin is active, if it is not active, the menu for installer should not be registered.
	 *
	 * @param int $product_id Product if of brainstorm product.
	 *
	 * @return boolean true - If menu is to be shown | false - if menu is not to be displayed.
	 */
	function _bsf_maybe_add_dashboard_menu( $product_id ) {
		$brainstrom_products = ( get_option( 'brainstrom_products' ) ) ? get_option( 'brainstrom_products' ) : array();
		$template_plugin     = '';
		$template_theme      = '';
		$is_theme            = false;

		if ( is_multisite() ) {
			// Do not register menu if we are on multisite, multisite menu will be registered below the brainstorm menu.
			return false;
		}

		if ( ! empty( $brainstrom_products ) ) {

			if ( isset( $brainstrom_products['plugins'] ) && isset( $brainstrom_products['plugins'][ $product_id ] ) ) {
				$template_plugin = $brainstrom_products['plugins'][ $product_id ]['template'];
			}

			if ( isset( $brainstrom_products['themes'] ) && isset( $brainstrom_products['themes'][ $product_id ] ) ) {
				$template_theme = $brainstrom_products['themes'][ $product_id ]['product_name'];
				$is_theme       = true;
			}

			if ( true === $is_theme && '' !== $template_theme ) {

				$themes     = wp_get_theme();
				$theme_name = '';

				$parent = $themes->parent();

				if ( empty( $parent ) ) {
					$theme_name = $themes->get( 'Name' );
				} else {
					$theme_name = $themes->parent()->get( 'Name' );
				}

				if ( $theme_name === $template_theme ) {
					// Theme / Parent theme is active, hence display menu.
					return true;
				}

				// don't display menu if theme/parent theme does not need extension installer.
				return false;

			} elseif ( false === $is_theme && '' !== $template_plugin ) {

				include_once ABSPATH . 'wp-admin/includes/plugin.php';

				if ( is_plugin_active( $template_plugin ) || is_plugin_active_for_network( $template_plugin ) ) {
					// Plugin is active, hence display menu.
					return true;
				}

				// don't display menu if plugin does not need extension installer.
				return false;

			}
		}

		// do not register menu if all conditions fail.
		return false;

	}
}


if ( ! function_exists( 'register_bsf_extension_page_network' ) ) {
	/**
	 *  Register BSF extension page Network
	 */
	function register_bsf_extension_page_network() {

		$themes = wp_get_themes( array( 'allowed' => 'network' ) );

		$parent_slug = 'bsf-registration';

		if ( defined( 'BSF_REG_MENU_TO_SETTINGS' ) && ( BSF_REG_MENU_TO_SETTINGS === true || BSF_REG_MENU_TO_SETTINGS === 'true' ) ) {
			$parent_slug = 'settings.php';
		}

		foreach ( $themes as $theme ) {
			if ( 'iMedica' === $theme->Name ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				add_submenu_page( $parent_slug, __( 'iMedica Extensions', 'bsf' ), __( 'iMedica Extensions', 'bsf' ), 'manage_options', 'bsf-extensions-10395942', 'bsf_extensions_callback' );
				break;
			}
		}
	}
}
if ( ! function_exists( 'bsf_extensions_callback' ) ) {
	/**
	 * BSF extensions callback
	 */
	function bsf_extensions_callback() {
		include_once 'plugin-installer/index.php';
	}
}

if ( ! function_exists( 'bsf_extract_product_id' ) ) {
	/**
	 * BSF extract Product ID.
	 *
	 * @param string $path Path.
	 */
	function bsf_extract_product_id( $path ) {
		$id            = false;
		$file          = rtrim( $path, '/' ) . '/admin/bsf.yml';
		$file_fallback = rtrim( $path, '/' ) . '/bsf.yml';

		if ( is_file( $file ) ) {
			$file = $file;
		} elseif ( is_file( $file_fallback ) ) {
			$file = $file_fallback;
		} else {
			return apply_filters( 'bsf_extract_product_id', $id, $path );
		}

		// Use of file_get_contents() - https://github.com/WordPress/WordPress-Coding-Standards/pull/1374/files#diff-400e43bc09c24262b43f26fce487fdabR43-R52.
		$filelines = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading local file is OK.
		if ( stripos( $filelines, 'ID:[' ) !== false ) {
			preg_match_all( '/ID:\[(.*?)\]/', $filelines, $matches );
			if ( isset( $matches[1] ) ) {
				$id = ( isset( $matches[1][0] ) ) ? $matches[1][0] : '';
			}
		}

		return apply_filters( 'bsf_extract_product_id', $id, $path );
	}
}

if ( ! function_exists( 'init_bsf_core' ) ) {
	/**
	 *  Init BSF Core
	 */
	function init_bsf_core() {

		$plugins = get_plugins();
		$themes = wp_get_themes();
		$theme_directories = search_theme_directories();
		$bsf_products = array();

		$bsf_authors = apply_filters(
			'bsf_authors_list',
			array(
				'Brainstorm Force',
			)
		);

		foreach ( $plugins as $plugin => $plugin_data ) {
			if ( in_array( trim( $plugin_data['Author'] ), $bsf_authors, true ) ) {
				$plugin_data['type']     = 'plugin';
				$plugin_data['template'] = $plugin;
				$plugin_data['path']     = dirname( realpath( WP_PLUGIN_DIR . '/' . $plugin ) );
				$id                      = bsf_extract_product_id( $plugin_data['path'] );
				if ( false !== $id ) {
					$plugin_data['id'] = $id;
				} // without readme.txt filename
				array_push( $bsf_products, $plugin_data );
			}
		}

		foreach ( $themes as $theme => $theme_data ) {
			$temp         = array();
			$theme_author = trim( $theme_data->display( 'Author', false ) );
			if ( 'Brainstorm Force' === $theme_author ) {
				$temp['Name']        = $theme_data->get( 'Name' );
				$temp['ThemeURI']    = $theme_data->get( 'ThemeURI' );
				$temp['Description'] = $theme_data->get( 'Description' );
				$temp['Author']      = $theme_data->get( 'Author' );
				$temp['AuthorURI']   = $theme_data->get( 'AuthorURI' );
				$temp['Version']     = $theme_data->get( 'Version' );
				$temp['type']        = 'theme';
				$temp['template']    = $theme;
				$temp['path']        = realpath( get_theme_root() . '/' . $theme );
				$id                  = bsf_extract_product_id( $temp['path'] );
				if ( false !== $id ) {
					$temp['id'] = $id;
				} // without readme.txt filename
				array_push( $bsf_products, $temp );
			}
		}

		$brainstrom_products = get_option( 'brainstrom_products', array() );
		$bundled_products = get_option( 'brainstrom_bundled_products', array() );

		if ( ! empty( $brainstrom_products ) ) {
			if ( isset( $brainstrom_products['plugins'] ) ) {
				foreach ( $brainstrom_products['plugins'] as $key => $value ) {
					if ( ! array_key_exists( $value['template'], $plugins ) ) {
						unset( $brainstrom_products['plugins'][ $key ] );
					}
				}
			}

			if ( isset( $brainstrom_products['themes'] ) ) {
				foreach ( $brainstrom_products['themes'] as $key => $value ) {
					if ( ! array_key_exists( $value['template'], $theme_directories ) ) {
						unset( $brainstrom_products['themes'][ $key ] );
					}
				}
			}
		}

		// Update newly added brainstorm_products.
		if ( ! empty( $bsf_products ) ) {
			foreach ( $bsf_products as $key => $product ) {
				if ( ! ( isset( $product['id'] ) ) || '' === $product['id'] ) {
					continue;
				}
				if ( isset( $brainstrom_products[ $product['type'] . 's' ][ $product['id'] ] ) ) {
					$bsf_product_info = $brainstrom_products[ $product['type'] . 's' ][ $product['id'] ];
				} else {
					$bsf_product_info = array();
					do_action( 'brainstorm_updater_new_product_added' );
				}
				$bsf_product_info['template'] = $product['template'];
				$bsf_product_info['type']     = $product['type'];
				$bsf_product_info['id']       = $product['id'];
				$brainstrom_products[ $product['type'] . 's' ][ $product['id'] ] = $bsf_product_info;
			}
		}

		// Update bundled products.
		foreach ( $bundled_products as $key => $product ) {
			$bsf_product = get_brainstorm_product( $key );

			if ( empty( $bsf_product ) ) {
				unset( $bundled_products[ $key ] );
			}
		}

		update_option( 'brainstrom_products', $brainstrom_products );
		update_option( 'brainstrom_bundled_products', $bundled_products );
	}
}

add_action( 'admin_init', 'init_bsf_core' );

// assets.
add_action( 'admin_enqueue_scripts', 'register_bsf_core_admin_styles', 1 );
if ( ! function_exists( 'register_bsf_core_admin_styles' ) ) {
	/**
	 * Register BSF Core Admin Styles
	 *
	 * @param string $hook Hook.
	 */
	function register_bsf_core_admin_styles( $hook ) {
		// bsf core style.
		$hook_array = array(
			'toplevel_page_bsf-registration',
			'update-core.php',
			'dashboard_page_bsf-registration',
			'index_page_bsf-registration',
			'admin_page_bsf-extensions',
			'settings_page_bsf-registration',
			'admin_page_bsf-registration',
			'plugins.php',
			'imedica_page_product-license',
		);
		$hook_array = apply_filters( 'bsf_core_style_screens', $hook_array );

		if ( in_array( $hook, $hook_array, true ) || strpos( $hook, 'bsf-extensions' ) !== false ) {

			$envato_activation_nonce = wp_create_nonce( 'envato_activation_nonce' );

			wp_register_style( 'bsf-core-admin', bsf_core_url( '/assets/css/style.css' ), array(), BSF_UPDATER_VERSION );
			wp_enqueue_style( 'bsf-core-admin' );

			wp_register_style( 'brainstorm-switch', bsf_core_url( '/assets/css/switch.css' ), array(), BSF_UPDATER_VERSION );
			wp_enqueue_style( 'brainstorm-switch' );

			wp_register_script( 'brainstorm-switch', bsf_core_url( '/assets/js/switch.js' ), array( 'jquery' ), BSF_UPDATER_VERSION, true );
			wp_enqueue_script( 'brainstorm-switch' );

			wp_register_script( 'bsf-core', bsf_core_url( '/assets/js/bsf-core.js' ), array( 'jquery' ), BSF_UPDATER_VERSION, true );

			wp_localize_script( 'bsf-core', 'bsf_core', array( 'envato_activation_nonce' => $envato_activation_nonce ) );

			wp_enqueue_script( 'bsf-core' );
		}

		// frosty script.
		$hook_frosty_array = array();
		$hook_frosty_array = apply_filters( 'bsf_core_frosty_screens', $hook_frosty_array );
		if ( in_array( $hook, $hook_frosty_array, true ) ) {
			wp_register_script( 'bsf-core-frosty', bsf_core_url( '/assets/js/frosty.js' ), array(), BSF_UPDATER_VERSION, false );
			wp_enqueue_script( 'bsf-core-frosty' );

			wp_register_style( 'bsf-core-frosty-style', bsf_core_url( '/assets/css/frosty.css' ), array(), BSF_UPDATER_VERSION );
			wp_enqueue_style( 'bsf-core-frosty-style' );
		}
	}
}
if ( is_multisite() ) {
	add_action( 'admin_print_scripts', 'print_bsf_styles' );
	if ( ! function_exists( 'print_bsf_styles' ) ) {
		/**
		 * Print BSF styles.
		 */
		function print_bsf_styles() {
			$path = bsf_core_url( '/assets/fonts' );

			echo "<style>
				@font-face {
					font-family: 'brainstorm';
					src:url('" . esc_url( $path ) . "/brainstorm.eot');
					src:url('" . esc_url( $path ) . "/brainstorm.eot') format('embedded-opentype'),
						url('" . esc_url( $path ) . "/brainstorm.woff') format('woff'),
						url('" . esc_url( $path ) . "/brainstorm.ttf') format('truetype'),
						url('" . esc_url( $path ) . "/brainstorm.svg') format('svg');
					font-weight: normal;
					font-style: normal;
				}
				.toplevel_page_bsf-registration > div.wp-menu-image:before {
					content: \"\\e603\" !important;
					font-family: 'brainstorm' !important;
					speak: none;
					font-style: normal;
					font-weight: normal;
					font-variant: normal;
					text-transform: none;
					line-height: 1;
					-webkit-font-smoothing: antialiased;
					-moz-osx-font-smoothing: grayscale;
				}
			</style>";
		}
	}
}

if ( ! function_exists( 'bsf_flush_bundled_products' ) ) {
	/**
	 * BSF flush Bundled products.
	 */
	function bsf_flush_bundled_products() {
		$bsf_force_check_extensions = (bool) get_site_option( 'bsf_force_check_extensions', false );

		if ( $bsf_force_check_extensions ) {
			delete_site_option( 'brainstrom_bundled_products' );

			global $ultimate_referer;
			if ( empty( $ultimate_referer ) ) {
				$ultimate_referer = 'on-flush-bundled-products';
			}
			get_bundled_plugins();

			update_site_option( 'bsf_force_check_extensions', false );
		}
	}
}

add_action( 'bsf_after_plugin_installer', 'bsf_flush_bundled_products' );
add_action( 'deleted_plugin', 'bsf_flush_bundled_products' );

/**
 * Return array of bundled plugins for a specific
 *
 * @since Graupi 1.9
 */
if ( ! function_exists( 'bsf_bundled_plugins' ) ) {
	/**
	 * BSF bundled plugins.
	 *
	 * @param int $product_id Product ID.
	 */
	function bsf_bundled_plugins( $product_id = '' ) {
		$products = array();

		$brainstrom_bundled_products = get_option( 'brainstrom_bundled_products', '' );

		if ( '' !== $brainstrom_bundled_products ) {
			if ( array_key_exists( $product_id, $brainstrom_bundled_products ) ) {
				$products = $brainstrom_bundled_products[ $product_id ];
			}
		}

		return $products;
	}
}

/**
 * Get product name from product ID
 *
 * @since Graupi 1.9
 */
if ( ! function_exists( 'brainstrom_product_name' ) ) {
	/**
	 * Brainstorm product name.
	 *
	 * @param int $product_id Product ID.
	 */
	function brainstrom_product_name( $product_id = '' ) {
		$product_name        = '';
		$brainstrom_products = get_option( 'brainstrom_products', array() );

		foreach ( $brainstrom_products as $key => $value ) {
			foreach ( $value as $key => $product ) {
				if ( $product_id === $key ) {
					$product_name = isset( $product['product_name'] ) ? $product['product_name'] : '';
				}
			}
		}

		return $product_name;
	}
}

/**
 * Get product id from product name
 *
 * @since Graupi 1.19
 */
if ( ! function_exists( 'brainstrom_product_id_by_name' ) ) {
	/**
	 * Brainstorm product ID by name.
	 *
	 * @param int $product_name Product name.
	 */
	function brainstrom_product_id_by_name( $product_name ) {
		$product_id          = '';
		$brainstrom_products = get_option( 'brainstrom_products', array() );

		foreach ( $brainstrom_products as $key => $value ) {
			foreach ( $value as $key => $product ) {
				if ( isset( $product['product_name'] ) && strcasecmp( $product['product_name'], $product_name ) === 0 ) {
					$product_id = isset( $product['id'] ) ? $product['id'] : '';
				}
			}
		}

		return $product_id;
	}
}

if ( ! function_exists( 'brainstrom_product_id_by_init' ) ) {
	/**
	 * BrainstormProduct Id by init.
	 *
	 * @param string $plugin_init Plugin init.
	 */
	function brainstrom_product_id_by_init( $plugin_init ) {

		$brainstrom_products = get_option( 'brainstrom_products', array() );
		$brainstorm_plugins  = isset( $brainstrom_products['plugins'] ) ? $brainstrom_products['plugins'] : array();
		$brainstorm_themes   = isset( $brainstrom_products['themes'] ) ? $brainstrom_products['themes'] : array();

		$all_products = $brainstorm_plugins + $brainstorm_themes;

		foreach ( $all_products as $key => $product ) {

			$template = isset( $product['template'] ) ? $product['template'] : '';
			if ( $plugin_init === $template ) {

				return isset( $product['id'] ) ? $product['id'] : false;
			}
		}
	}
}

/**
 * Dismiss Extension installer nag
 *
 * @since Graupi 1.9
 */
if ( ! function_exists( 'bsf_dismiss_extension_nag' ) ) {
	/**
	 * BSF dismiss extension nag.
	 */
	function bsf_dismiss_extension_nag() {
		if ( isset( $_REQUEST['bsf-extension-nag-nonce'] ) && wp_verify_nonce( $_REQUEST['bsf-extension-nag-nonce'], 'bsf-extension-nag' ) && isset( $_GET['bsf-dismiss-notice'] ) ) {
			$product_id = sanitize_text_field( $_GET['bsf-dismiss-notice'] );
			update_user_meta( get_current_user_id(), $product_id . '-bsf_nag_dismiss', true );
		}
	}
}

add_action( 'admin_head', 'bsf_dismiss_extension_nag' );

// For debugging uncomment line below and remove query var &bsf-dismiss-notice from url and nag will be restored.
// delete_user_meta( get_current_user_id(), 'bsf-next-bsf_nag_dismiss');.

/*
 * Load BSF core frosty scripts on front end
*/
add_action( 'wp_enqueue_scripts', 'register_bsf_core_styles', 1 );
/**
 * Register BSF Core styles
 *
 * @param string $hook Hook.
 */
function register_bsf_core_styles( $hook ) {
	// Register Frosty script and style.
	wp_register_script( 'bsf-core-frosty', bsf_core_url( '/assets/js/frosty.js' ), array(), BSF_UPDATER_VERSION, false );
	wp_register_style( 'bsf-core-frosty-style', bsf_core_url( '/assets/css/frosty.css' ), array(), BSF_UPDATER_VERSION );
}

/**
 * Add link to debug settings for braisntorm updater on license registration page
 */
if ( ! function_exists( 'bsf_core_debug_link' ) ) {
	/**
	 * Register BSF Core styles
	 *
	 * @param string $text Text.
	 */
	function bsf_core_debug_link( $text ) {
		$screen = get_current_screen();

		$screens = array(
			'dashboard_page_bsf-registration',
			'toplevel_page_bsf-registration-network',
			'settings_page_bsf-registration',
			'settings_page_bsf-registration-network',
		);

		$screens = apply_filters( 'bsf_core_debug_link_screens', $screens );

		if ( ! in_array( $screen->id, $screens, true ) ) {
			return $text;
		}

		$url  = bsf_registration_page_url( '&author' );
		$link = '<a href="' . $url . '">' . BSF_UPDATER_SHORTNAME . ' Updater debug settings</a>';
		$text = $link . ' | ' . $text;

		return $text;
	}
}

add_filter( 'update_footer', 'bsf_core_debug_link', 999 );

/**
 * Return brainstorm registration page URL
 *
 * @param $append (string) - Append at string at the end of the url
 */
if ( ! function_exists( 'bsf_registration_page_url' ) ) {
	/**
	 * BSF Registration Page URL
	 *
	 * @param string $append Append.
	 * @param int    $product_id Product ID.
	 */
	function bsf_registration_page_url( $append = '', $product_id = '' ) {

		$bsf_updater_options       = get_option( 'bsf_updater_options', array() );
		$option                    = false;
		$constant                  = false;
		$skip_brainstorm_menu      = get_site_option( 'bsf_skip_braisntorm_menu', false );
		$product_registration_link = apply_filters( "bsf_registration_page_url_{$product_id}", '' );

		// If Brainstorm meu is not registered.
		if ( ( defined( 'BSF_UNREG_MENU' ) && ( BSF_UNREG_MENU === true || BSF_UNREG_MENU === 'true' ) ) ||
			true === $skip_brainstorm_menu
		) {

			if ( '&author' === $append ) {

				return admin_url( 'options.php?page=bsf-registration' . $append );
			}
		}

		if ( isset( $bsf_updater_options['brainstorm_menu'] ) && '1' === $bsf_updater_options['brainstorm_menu'] ) {
			$option = true;
		}

		if ( defined( 'BSF_REG_MENU_TO_SETTINGS' ) && 'BSF_REG_MENU_TO_SETTINGS' === true || 'BSF_REG_MENU_TO_SETTINGS' === 'true' ) {
			$constant = true;
		}

		if ( '' !== $product_registration_link ) {

			return $product_registration_link . '' . $append;
		} else {

			if ( true === $option || true === $constant ) {
				// bsf menu in settings.
				if ( is_multisite() ) {
					return network_admin_url( 'settings.php?page=bsf-registration' . $append );
				} else {
					return admin_url( 'options-general.php?page=bsf-registration' . $append );
				}
			} else {
				if ( is_multisite() ) {
					return network_admin_url( 'admin.php?page=bsf-registration' . $append );
				} else {
					return admin_url( 'index.php?page=bsf-registration' . $append );
				}
			}
		}

	}
}

/**
 * Return extension installer page URL
 */
if ( ! function_exists( 'bsf_exension_installer_url' ) ) {
	/**
	 * BSF extension installer URL
	 *
	 * @param int $priduct_id Product ID.
	 * @return string URL.
	 */
	function bsf_exension_installer_url( $priduct_id ) {

		if ( is_multisite() ) {

			if ( defined( 'BSF_REG_MENU_TO_SETTINGS' ) && ( BSF_REG_MENU_TO_SETTINGS === true || BSF_REG_MENU_TO_SETTINGS === 'true' ) ) {
				return network_admin_url( 'settings.php?page=bsf-extensions-' . $priduct_id );
			} else {
				return network_admin_url( 'admin.php?page=bsf-extensions-' . $priduct_id );
			}
		} else {
			return admin_url( 'admin.php?page=bsf-extensions-' . $priduct_id );
		}
	}
}

if ( ! function_exists( 'bsf_set_options' ) ) {

	/**
	 * Set options based on reading $_GET parameters and $_POST parameters
	 *
	 * 1. force Check updates
	 * 2. Skip Brainstorm Account Registration
	 * 3. Reset Brainstorm Registration data
	 */
	function bsf_set_options() {
		// Force check updates.
		if ( isset( $_GET['force-check-update'] ) || isset( $_GET['force-check'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			global $pagenow;
			global $ultimate_referer;

			if ( 'update-core.php' === $pagenow && '1' === $_GET['force-check'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$ultimate_referer = 'on-force-check-update-update-core';
			} else {
				$ultimate_referer = 'on-force-check-update';
			}

			bsf_check_product_update();
			update_option( 'bsf_last_update_check', current_time( 'timestamp' ) );
		}

		// Skip Author registration.
		$skip_author_products = apply_filters( 'bsf_skip_author_registration', $products = array() );
		$ids                  = array();
		$skip_author_option   = get_site_option( 'bsf_skip_author', false );
		$brainstorm_products  = bsf_get_brainstorm_products( true );
		foreach ( $brainstorm_products as $key => $product ) {

			if ( isset( $product['id'] ) && ! in_array( $product['id'], $skip_author_products, true ) ) {
				$ids[] = $product['id'];
			}
		}

		if ( isset( $_REQUEST['bsf-skip-author-nonce'] ) && wp_verify_nonce( $_REQUEST['bsf-skip-author-nonce'], 'bsf-skip-author' ) && isset( $_GET['bsf-skip-author'] ) || empty( $ids ) && '' === $skip_author_option ) {
			update_site_option( 'bsf_skip_author', true );
		} elseif ( ! empty( $ids ) && '1' === $skip_author_option ) {
			update_site_option( 'bsf_skip_author', false );
		}

		// Skip Brainstorm Menu.
		$default_skip_brainstorm_menu = array(
			'uabb',
			'convertpro',
			'astra-addon',
			'astra-pro-sites',
			'wp-schema-pro',
			'6892199', // UAVC.
			'10395942', // iMedica.
			'14058953', // Convert Plus.
			'5159016', // Baslider.
			'imedica-mu-companion',
			'astra-sites-showcase',
			'uael',
			'brainstorm-updater',
			'astra-portfolio',
			'7155037', // VC Modal Popups.
			'astra',
		);

		$skip_brainstorm_menu_products = apply_filters( 'bsf_skip_braisntorm_menu', $default_skip_brainstorm_menu );
		$ids                           = array();
		$skip_brainstorm_menu          = get_site_option( 'bsf_skip_braisntorm_menu', false );
		foreach ( $brainstorm_products as $key => $product ) {

			if ( isset( $product['id'] ) && ! in_array( $product['id'], $skip_brainstorm_menu_products, true ) ) {
				$ids[] = $product['id'];
			}
		}

		if ( empty( $ids ) && '' === $skip_brainstorm_menu ) {
			update_site_option( 'bsf_skip_braisntorm_menu', true );
		} elseif ( ! empty( $ids ) && '1' === $skip_brainstorm_menu ) {
			update_site_option( 'bsf_skip_braisntorm_menu', false );
		}

		// Reset Brainstorm Registration.
		if ( isset( $_GET['reset-bsf-users'] ) ) {
			delete_option( 'brainstrom_users' );
			delete_option( 'brainstrom_products' );
			delete_option( 'brainstrom_bundled_products' );
			delete_site_option( 'bsf_skip_author' );
		}

		// Reset Bundled products.
		if ( isset( $_GET['remove-bundled-products'] ) ) {

			global $ultimate_referer;
			$ultimate_referer = 'on-refresh-bundled-products';
			delete_option( 'brainstrom_bundled_products' );
			get_bundled_plugins();

			$redirect = isset( $_GET['redirect'] ) ? esc_url_raw( urldecode( esc_attr( $_GET['redirect'] ) ) ) : '';

			if ( '' !== $redirect && filter_var( $redirect, FILTER_VALIDATE_URL ) ) {
				$redirect = add_query_arg( 'bsf-reload-page', '', $redirect );

				wp_safe_redirect( $redirect );
				exit;
			}
		}

	}
}

add_action( 'admin_init', 'bsf_set_options', 0 );
add_action( 'network_admin_init', 'bsf_set_options', 0 );

/**
 * Flush skip registration option when any new brainstorm product is installed on the site.
 */
function bsf_flush_skip_registration() {
	delete_site_option( 'bsf_skip_author' );
}

add_action( 'brainstorm_updater_new_product_added', 'bsf_flush_skip_registration' );

/**
 * Return site option brainstorm_products
 *
 * Brainstorm_options option saves the data related to all the brainstorm products required for license management and updates.
 *
 * @param (boolean) $mix true: the output will be combined array of themes and plugins.
 * @return (array) $brainstorm_products
 */
if ( ! function_exists( 'bsf_get_brainstorm_products' ) ) {
	/**
	 *  BSF Get Brainstorm products.
	 *
	 *  @param bool $mix Mix.
	 *  @return array $brainstorm_products.
	 */
	function bsf_get_brainstorm_products( $mix = false ) {
		$brainstorm_products = get_option( 'brainstrom_products', array() );

		if ( true === $mix ) {
			$plugins = ( isset( $brainstorm_products['plugins'] ) ) ? $brainstorm_products['plugins'] : array();
			$themes  = ( isset( $brainstorm_products['themes'] ) ) ? $brainstorm_products['themes'] : array();

			$brainstorm_products = array_merge( $plugins, $themes );
		}

		return $brainstorm_products;
	}
}
/**
 *  BSF envatoredirect URL callback.
 */
function bsf_envato_redirect_url_callback() {

	check_ajax_referer( 'envato_activation_nonce', 'envato_activation_nonce' );
	// bail if current user cannot manage_options.
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	$envato_activate = new BSF_Envato_Activate();

	$form_data = array();

	$form_data['product_id']               = isset( $_GET['product_id'] ) ? esc_attr( $_GET['product_id'] ) : '';
	$form_data['url']                      = isset( $_GET['url'] ) ? esc_url_raw( $_GET['url'] ) : '';
	$form_data['redirect']                 = isset( $_GET['redirect'] ) ? rawurlencode( $_GET['redirect'] ) : '';
	$form_data['privacy_consent']          = ( isset( $_GET['privacy_consent'] ) && 'true' === $_GET['privacy_consent'] ) ? true : false;
	$form_data['terms_conditions_consent'] = ( isset( $_GET['terms_conditions_consent'] ) && 'true' === $_GET['terms_conditions_consent'] ) ? true : false;

	$url = $envato_activate->envato_activation_url( $form_data );

	$data = array(
		'url' => esc_url_raw( $url ),
	);

	return wp_send_json_success( $data );
}

add_action( 'wp_ajax_bsf_envato_redirect_url', 'bsf_envato_redirect_url_callback' );

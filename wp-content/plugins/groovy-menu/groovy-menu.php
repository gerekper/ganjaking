<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );
/*
Plugin Name: Groovy Menu
Version: 2.6.0
Description: Groovy menu is a modern adjustable and flexible menu designed for creating mobile-friendly menus with a lot of options.
Plugin URI: https://groovymenu.grooni.com/
Author: Grooni
Author URI: https://grooni.com
Text Domain: groovy-menu
Domain Path: /languages/
*/


define( 'GROOVY_MENU_VERSION', '2.6.0' );
define( 'GROOVY_MENU_DB_VER_OPTION', 'groovy_menu_db_version' );
define( 'GROOVY_MENU_PREFIX_WIM', 'groovy-menu-wim' );
define( 'GROOVY_MENU_SITE_URI', site_url() );
define( 'GROOVY_MENU_DIR', plugin_dir_path( __FILE__ ) );
define( 'GROOVY_MENU_URL', plugin_dir_url( __FILE__ ) );
define( 'GROOVY_MENU_BASENAME', plugin_basename( trailingslashit( dirname( dirname( __FILE__ ) ) ) . 'groovy-menu.php' ) );

if ( ! defined( 'AUTH_COOKIE' ) && function_exists( 'is_multisite' ) && is_multisite() ) {
	if ( function_exists( 'wp_cookie_constants' ) ) {
		wp_cookie_constants();
	}
}

$db_version = get_option( GROOVY_MENU_DB_VER_OPTION );
if ( ! $db_version || version_compare( '2.0.0', $db_version, '>=' ) ) {
	update_option( GROOVY_MENU_DB_VER_OPTION, GROOVY_MENU_VERSION );
	$db_version = GROOVY_MENU_VERSION;
}
if ( ! defined( 'GROOVY_MENU_LVER' ) ) {
	define( 'GROOVY_MENU_LVER', '1' );
}

global $gm_supported_module;
$gm_supported_module = array(
	'theme'      => wp_get_theme()->get_template(),
	'post_types' => empty( $gm_supported_module['post_types'] ) ? array() : $gm_supported_module['post_types'],
	'categories' => empty( $gm_supported_module['categories'] ) ? array() : $gm_supported_module['categories'],
	'activate'   => array(),
	'deactivate' => array(),
	'db_version' => $db_version,
);

if ( ! function_exists( 'gm_is_wplogin' ) ) {
	function gm_is_wplogin() {
		$path = str_replace( array( '\\', '/' ), DIRECTORY_SEPARATOR, ABSPATH );

		return ( ( in_array( $path . 'wp-login.php', get_included_files(), true ) || in_array( $path . 'wp-register.php', get_included_files(), true ) ) || ( isset( $_GLOBALS['pagenow'] ) && 'wp-login.php' === $GLOBALS['pagenow'] ) || '/wp-login.php' === $_SERVER['PHP_SELF'] );
	}
}

// Autoload modules and classes by composer.
require_once GROOVY_MENU_DIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

if ( version_compare( PHP_VERSION, '7.0.0', '<' ) && class_exists( 'GroovyMenuUtils' ) && method_exists( 'GroovyMenuUtils', 'show_gm_php_version' ) ) {
	add_action( 'admin_notices', array( 'GroovyMenuUtils', 'show_gm_php_version' ), 7 );

	return;
}

register_activation_hook( __FILE__, 'groovy_menu_activation' );
register_deactivation_hook( __FILE__, 'groovy_menu_deactivation' );

add_action( 'init', array( 'GroovyMenuUtils', 'add_groovy_menu_preset_post_type' ), 3 );
add_filter( 'plugin_row_meta', array( 'GroovyMenuUtils', 'gm_plugin_meta_links' ), 10, 2 );
add_filter( 'plugin_action_links', array( 'GroovyMenuUtils', 'gm_plugin_page_links' ), 10, 2 );
add_filter( 'wp_update_nav_menu', array( 'GroovyMenuUtils', 'saveNavMenuLocation' ), 10 );
add_action( 'admin_enqueue_scripts', array( 'GroovyMenuUtils', 'checkNavMenuLocationPage' ), 10 );

add_action( 'init', 'groovy_menu_init_classes', 2 );

if ( ! function_exists( 'groovy_menu_init_classes' ) ) {
	// Initialize Groovy Menu.
	function groovy_menu_init_classes() {
		if ( class_exists( 'GroovyMenuPreset' ) ) {
			new GroovyMenuPreset( null, true );
		}

		if ( class_exists( 'GroovyMenuSettings' ) ) {
			new GroovyMenuSettings();
		}

		if ( class_exists( 'GroovyMenuCategoryPreset' ) ) {
			new GroovyMenuCategoryPreset();
		}

		if ( class_exists( 'GroovyMenuSingleMetaPreset' ) ) {
			new GroovyMenuSingleMetaPreset();
		}

		if ( class_exists( '\GroovyMenu\AdminWalker' ) ) {
			\GroovyMenu\AdminWalker::registerWalker();
		}

		$db_version = get_option( GROOVY_MENU_DB_VER_OPTION );
		if ( $db_version && version_compare( '2.4.10', $db_version, '>' ) && version_compare( '1.9.9', $db_version, '<' ) ) {
			$migration_report = get_option( GROOVY_MENU_DB_VER_OPTION . '__report' );
			if ( empty( $migration_report ) || ! is_array( $migration_report ) ) {
				$migration_report = array();
			}
			$migration_report['cron_job']       = false;
			$migration_report['dismissed_info'] = true;
			update_option( GROOVY_MENU_DB_VER_OPTION . '__report', $migration_report );
			update_option( GROOVY_MENU_DB_VER_OPTION, '2.4.10' );
		}
	}
}

if ( method_exists( 'GroovyMenuUtils', 'cache_pre_wp_nav_menu' ) ) {
	add_filter( 'pre_wp_nav_menu', array( 'GroovyMenuUtils', 'cache_pre_wp_nav_menu' ), 10, 2 );
}

if ( method_exists( 'GroovyMenuUtils', 'add_groovy_menu_as_wp_nav_menu' ) ) {
	add_filter( 'pre_wp_nav_menu', array( 'GroovyMenuUtils', 'add_groovy_menu_as_wp_nav_menu' ), 30, 2 );
}

if ( method_exists( 'GroovyMenuUtils', 'install_default_icon_packs' ) ) {
	add_action( 'wp_ajax_gm_install_default_icon_packs', array( 'GroovyMenuUtils', 'install_default_icon_packs' ) );
}

if ( method_exists( 'GroovyMenuUtils', 'update_config_text_domain' ) && is_admin() ) {
	add_action( 'wp_loaded', array( 'GroovyMenuUtils', 'update_config_text_domain' ), 1000 );
}

if ( method_exists( 'GroovyMenuUtils', 'output_uniqid_gm_js' ) ) {
	add_action( 'gm_enqueue_script_actions', array( 'GroovyMenuUtils', 'output_uniqid_gm_js' ), 999 );
	add_action( 'gm_after_main_header', array( 'GroovyMenuUtils', 'output_uniqid_gm_js' ), 999 );
}

if ( method_exists( 'GroovyMenuUtils', 'add_critical_css' ) ) {
	add_action( 'gm_before_main_header', array( 'GroovyMenuUtils', 'add_critical_css' ), 1 );
}

if ( method_exists( 'GroovyMenuUtils', 'load_font_internal' ) ) {
	GroovyMenuUtils::load_font_internal();
}

function groovy_menu_activation() {
	// Check Free version work & disable it.
	if ( function_exists( 'groovy_menu_activation_free' ) || is_plugin_active( 'groovy-menu-free/groovy-menu.php' ) ) {
		deactivate_plugins( 'groovy-menu-free/groovy-menu.php' );
	}

	global $gm_supported_module;

	foreach ( $gm_supported_module['activate'] as $launch_function ) {
		$launch_function();
	}

	if ( class_exists( 'GroovyMenuRoleCapabilities' ) ) {
		GroovyMenuRoleCapabilities::add_capabilities();
	}

	$default_icon_packs = get_option( 'gm_default_icon_packs_installed' );
	if ( empty( $default_icon_packs ) && method_exists( 'GroovyMenuUtils', 'install_default_icon_packs' ) ) {
		GroovyMenuUtils::install_default_icon_packs( true );
		update_option( 'gm_default_icon_packs_installed', true, false );
	}

	$lic_gm_version = GroovyMenuUtils::get_paramlic( 'gm_version' );
	if ( empty( $lic_gm_version ) || GROOVY_MENU_VERSION !== $lic_gm_version ) {
		GroovyMenuUtils::check_lic();
	}

	update_option( 'groovy_menu_do_activation_redirect', true );
}

function groovy_menu_deactivation() {
	global $gm_supported_module;

	foreach ( $gm_supported_module['deactivate'] as $launch_function ) {
		$launch_function();
	}
}

if ( ! function_exists( 'groovy_menu_welcome' ) ) {
	function groovy_menu_welcome() {
		if ( get_option( 'groovy_menu_do_activation_redirect', false ) ) {
			delete_option( 'groovy_menu_do_activation_redirect' );

			$welcome_url = add_query_arg(
				array( 'page' => 'groovy_menu_welcome' ),
				admin_url( 'admin.php' )
			);
			wp_safe_redirect( esc_url( $welcome_url ) );
		}
	}
}

add_action( 'admin_init', 'groovy_menu_welcome' );


if ( ! function_exists( 'groovy_menu_scripts' ) ) {
	function groovy_menu_scripts() {

		define( 'GROOVY_MENU_SCRIPTS_INIT', true );

		wp_enqueue_style( 'groovy-menu-style', GROOVY_MENU_URL . 'assets/style/frontend.css', [], GROOVY_MENU_VERSION );
		wp_style_add_data( 'groovy-menu-style', 'rtl', 'replace' );
		wp_enqueue_script( 'groovy-menu-js', GROOVY_MENU_URL . 'assets/js/frontend.js', [], GROOVY_MENU_VERSION, true );
		wp_localize_script( 'groovy-menu-js', 'groovyMenuHelper', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( 'groovy-menu-js', 'groovyMenuNonce', array( 'style' => esc_attr( wp_create_nonce( 'gm_nonce_preset_save' ) ) ) );

		foreach ( \GroovyMenu\FieldIcons::getFonts() as $name => $icon ) {
			wp_enqueue_style( 'groovy-menu-style-fonts-' . $name, esc_url( GroovyMenuUtils::getUploadUri() . 'fonts/' . $name . '.css' ), [], GROOVY_MENU_VERSION );
		}

		/**
		 * Fires when enqueue_script for Groovy Menu
		 *
		 * @since 1.2.20
		 */
		do_action( 'gm_enqueue_script_actions' );
	}
}

if ( ! function_exists( 'groovy_menu_toolbar' ) ) {
	function groovy_menu_toolbar() {
		if ( function_exists( 'is_user_logged_in' ) && is_user_logged_in() && current_user_can( 'edit_theme_options' ) ) {
			wp_enqueue_style( 'groovy-menu-style-toolbar', GROOVY_MENU_URL . 'assets/style/toolbar.css', [], GROOVY_MENU_VERSION );
			wp_style_add_data( 'groovy-menu-style-toolbar', 'rtl', 'replace' );
		}
	}
}

if ( ! function_exists( 'groovy_menu_load_textdomain' ) ) {
	function groovy_menu_load_textdomain() {
		load_plugin_textdomain( 'groovy-menu', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

add_action( 'plugins_loaded', 'groovy_menu_load_textdomain' );

add_action( 'wp_enqueue_scripts', 'groovy_menu_toolbar' );
add_action( 'admin_enqueue_scripts', 'groovy_menu_toolbar' );
add_action( 'wp_enqueue_scripts', 'groovy_menu_scripts' );
add_action( 'in_admin_footer', function () {
	global $pagenow;
	if ( 'nav-menus.php' === $pagenow ) {
		echo GroovyMenuRenderIconsModal();
	}
} );

$lic_gm_version = GroovyMenuUtils::get_paramlic( 'gm_version' );
if ( empty( $lic_gm_version ) || GROOVY_MENU_VERSION !== $lic_gm_version ) {
	GroovyMenuUtils::check_lic();
}
$lic_type = GroovyMenuUtils::get_paramlic( 'type' );
$gm_supported_module['check_update'] = "";
if ( 'extended' !== $lic_type || ! empty( $gm_supported_module['check_update'] ) ) {
	if ( class_exists( '\Puc_v4_Factory' ) ) {
		$update_checker = \Puc_v4_Factory::buildUpdateChecker(
			'https://license.grooni.com/wp-update-server/?action=get_metadata&slug=groovy-menu&glm_product=groovy-menu&glm_theme=' . $gm_supported_module['theme'] . '&glm_rs=' . rawurlencode( get_site_url() ),
			__FILE__,
			'groovy-menu'
		);
	}
}


add_filter( 'body_class', 'groovy_menu_add_version_class_2_html' );

if ( ! function_exists( 'groovy_menu_add_version_class_2_html' ) ) {
	/**
	 * @param $classes
	 *
	 * @return array
	 */
	function groovy_menu_add_version_class_2_html( $classes ) {
		$classes[] = 'groovy_menu_' . str_replace( '.', '-', GROOVY_MENU_VERSION );

		return $classes;
	}
}


add_filter( 'admin_body_class', 'groovy_menu_add_admin_body_class' );

if ( ! function_exists( 'groovy_menu_add_admin_body_class' ) ) {
	/**
	 * Adds html classes to the body tag in the dashboard.
	 *
	 * @param  String $classes Current body classes.
	 *
	 * @return String          Altered body classes.
	 */
	function groovy_menu_add_admin_body_class( $classes ) {
		global $gm_supported_module;

		if ( 'crane' === $gm_supported_module['theme'] && defined( 'CRANE_THEME_DB_VER_OPTION' ) ) {
			$crane_db_version = get_option( CRANE_THEME_DB_VER_OPTION );
			$gta_version      = defined( 'GROONI_THEME_ADDONS_VERSION' ) ? GROONI_THEME_ADDONS_VERSION : '1';
			$gta_need_version = version_compare( $gta_version, '1.3.10', '<' );
			if ( ( ! empty( $crane_db_version ) && version_compare( $crane_db_version, '1.3.9.1563', '<' ) ) || $gta_need_version ) {
				$classes = $classes . ' crane-needs-to-update-first';
			}
		}

		return $classes;
	}
}


// Start pre storage (compile groovy menu preset and nav_menu) before template.
if ( ! is_admin() && ! gm_is_wplogin() ) {
	add_action( 'wp_enqueue_scripts', 'groovy_menu_start_pre_storage', 50 );
}


if ( ! function_exists( 'groovy_menu_start_pre_storage' ) ) {
	function groovy_menu_start_pre_storage() {
		if ( isset( $_GET['gm_action_preview'] ) && $_GET['gm_action_preview'] ) { // @codingStandardsIgnoreLine
			return;
		}

		if ( class_exists( '\GroovyMenu\PreStorage' ) ) {
			\GroovyMenu\PreStorage::get_instance()->start_pre_storage();
		}
	}
}


if ( ! is_admin() && ! gm_is_wplogin() && GroovyMenuUtils::getAutoIntegration() ) {
	add_action( 'init', 'groovy_menu_start_buffer', 0, 0 );
	add_action( 'shutdown', 'groovy_menu_pre_shutdown', 0 );
	add_filter( 'groovy_menu_final_output', 'groovy_menu_add_after_body' );
	add_filter( 'groovy_menu_after_body_insert', 'groovy_menu_add_markup' );
}

if ( ! function_exists( 'groovy_menu_start_buffer' ) ) {
	/**
	 * Start buffering on the front-end.
	 *
	 * @since 1.3.1
	 */
	function groovy_menu_start_buffer() {
		if ( is_admin() || gm_is_wplogin() ) {
			return;
		}
		ob_start();
	}
}

if ( ! function_exists( 'groovy_menu_pre_shutdown' ) ) {
	/**
	 * Before final action.
	 *
	 * @since 1.3.1
	 */
	function groovy_menu_pre_shutdown() {
		if ( is_admin() || gm_is_wplogin() || ! defined( 'GROOVY_MENU_SCRIPTS_INIT' ) ) {
			return;
		}

		$final = ob_get_clean();

		echo apply_filters( 'groovy_menu_final_output', $final );
	}
}


if ( ! function_exists( 'groovy_menu_add_after_body' ) ) {
	/**
	 * Parse body tag and add additional output after.
	 *
	 * @param string $output additional output text for adding.
	 *
	 * @since 1.3.1
	 *
	 * @return null|string
	 */
	function groovy_menu_add_after_body( $output ) {
		if ( is_admin() || gm_is_wplogin() || ! defined( 'GROOVY_MENU_SCRIPTS_INIT' ) ) {
			return $output;
		}

		if ( isset( $_GET['gm_action_preview'] ) ) { // @codingStandardsIgnoreLine
			return $output;
		}

		$after_body = apply_filters( 'groovy_menu_after_body_insert', '' );
		$output     = apply_filters( 'groovy_menu_after_body_insert_output', $output );
		$limit      = apply_filters( 'groovy_menu_after_body_insert_limit', 1 );

		$output = preg_replace( '#(\<body.*\>)#i', '$1' . $after_body, $output, $limit );

		return $output;
	}
}


if ( ! function_exists( 'groovy_menu_add_markup' ) ) {
	/**
	 * Add markup
	 *
	 * @param string $after_body consist html code for insert after body.
	 *
	 * @since 1.3.1
	 *
	 * @return string
	 */
	function groovy_menu_add_markup( $after_body ) {

		$saved_auto_integration = GroovyMenuUtils::getAutoIntegration();

		if ( $saved_auto_integration ) {

			$gm_ids = \GroovyMenu\PreStorage::get_instance()->search_ids_by_location( array( 'theme_location' => 'gm_primary' ) );

			if ( ! empty( $gm_ids ) ) {

				foreach ( $gm_ids as $gm_id ) {
					$gm_data = \GroovyMenu\PreStorage::get_instance()->get_gm( $gm_id );

					$after_body .= $gm_data['gm_html'];
				}

			} else {
				$after_body .= groovy_menu( [
					'gm_echo'        => false,
					'theme_location' => 'gm_primary',
				] );
			}
		}

		return $after_body;
	}
}

// This theme uses wp_nav_menu() in one location.
register_nav_menus( array(
	'gm_primary' => esc_html__( 'Groovy menu Primary', 'groovy-menu' ),
) );


if ( ! function_exists( 'groovy_menu_get_post_types' ) ) {
	/**
	 * Return public post types.
	 *
	 * @return array
	 */
	function groovy_menu_get_post_types() {
		$post_types = array();

		// get the registered data about each post type with get_post_type_object.
		foreach ( get_post_types() as $type ) {
			$type_obj = get_post_type_object( $type );

			if ( isset( $type_obj->public ) && $type_obj->public ) {
				if ( 'attachment' !== $type_obj->name ) {
					$post_types[ $type_obj->name ] = $type_obj->label;
				}
			}
		}

		return $post_types;
	}
}


if ( ! function_exists( 'groovy_menu_js_request' ) ) {
	/**
	 * Return script with preset customs js
	 *
	 * @param string $uniqid        unique string id.
	 * @param bool   $return_string if true: return string wrap in html tag: script. If false return empty string and add script to wp_add_inline_script() function.
	 *
	 * @return string
	 */
	function groovy_menu_js_request( $uniqid, $return_string = false ) {
		global $groovyMenuPreview, $groovyMenuSettings;

		if ( $groovyMenuPreview ) {
			$groovyMenuPreview = $uniqid;
		}

		$groovyMenuSettings_json = $groovyMenuSettings;
		if ( isset( $groovyMenuSettings_json['nav_menu_data'] ) ) {
			unset( $groovyMenuSettings_json['nav_menu_data'] );
		}

		$preset_id = isset( $groovyMenuSettings['preset']['id'] ) ? $groovyMenuSettings['preset']['id'] : 'default';

		if ( ! empty( $groovyMenuSettings['gm-uniqid'][ $preset_id ] ) && $groovyMenuSettings['gm-uniqid'][ $preset_id ] === $uniqid ) {
			return '';
		}

		$groovyMenuSettings['gm-uniqid'][ $preset_id ] = $uniqid;

		$additional_js      = '';
		$additional_js_var  = 'var groovyMenuSettings = ' . wp_json_encode( $groovyMenuSettings_json ) . ';';
		$additional_js_init = "
";

		if ( ! $groovyMenuSettings['frontendInitImmediately'] ) {
			$additional_js_init .= ' document.addEventListener("DOMContentLoaded", function () { ';
		}

		if ( $groovyMenuSettings['frontendInitAlt'] ) {
			$additional_js_init .= ' let groovyMenuWrapperNode = document.querySelector(\'.gm-navbar\'); ';
		} else {
			$additional_js_init .= ' let groovyMenuWrapperNode = document.querySelector(\'.gm-preset-id-' . $preset_id . '\'); ';
		}

		$additional_js_init .= '
	if (groovyMenuWrapperNode) {
		if ( ! groovyMenuWrapperNode.classList.contains(\'gm-init-done\')) {
			var gm = new GroovyMenu(groovyMenuWrapperNode ,groovyMenuSettings); gm.init();
		}
	}
';

		if ( ! $groovyMenuSettings['frontendInitImmediately'] ) {
			$additional_js_init .= ' }); ';
		}

		$additional_js .= apply_filters( 'groovy_menu_additional_js_front__var', $additional_js_var, $groovyMenuSettings_json );
		$additional_js .= apply_filters( 'groovy_menu_additional_js_front__init', $additional_js_init, $preset_id );

		if ( $return_string ) {
			$tag_name = 'script';

			return "\n" . '<' . esc_attr( $tag_name ) . '>' . $additional_js . '</' . esc_attr( $tag_name ) . '>';
		} else {

			// Then work with GroovyMenuUtils::output_uniqid_gm_js .
			$groovyMenuSettings['gm-uniqid-js'][ $preset_id ] = $additional_js;

		}

		return '';
	}
}


if ( ! function_exists( 'groovy_menu_add_preset_style' ) ) {
	/**
	 * Return style with preset customs css
	 *
	 * @param string|integer $preset_id
	 * @param string         $compiled_css
	 * @param bool           $return_string
	 *
	 * @return string
	 */
	function groovy_menu_add_preset_style( $preset_id, $compiled_css, $return_string = false ) {

		global $groovyMenuSettings;
		$css_file_params = isset( $groovyMenuSettings['css_file_params'] ) ? $groovyMenuSettings['css_file_params'] : array();

		// If we have CSS preset style file - then enqueue it and return.
		if ( ! empty( $css_file_params ) && is_file( $css_file_params['upload_dir'] . $css_file_params['css_filename'] ) ) {
			if ( method_exists( 'GroovyMenuUtils', 'addPresetCssFile' ) ) {

				GroovyMenuUtils::addPresetCssFile();

				return '';
			}
		}

		if ( empty( $compiled_css ) ) {
			$styles       = new GroovyMenuStyle( $preset_id );
			$compiled_css = $styles->get( 'general', 'compiled_css' . ( is_rtl() ? '_rtl' : '' ) );
		}

		if ( $return_string ) {
			$handled_compiled_css = trim( stripcslashes( $compiled_css ) );
			$tag_name             = 'style';

			return "\n" . '<' . $tag_name . ' id="gm-style-preset--' . $preset_id . '" class="gm-compiled-css">' . $handled_compiled_css . '</' . $tag_name . '>';
		} else {
			if ( function_exists( 'wp_add_inline_style' ) ) {
				wp_add_inline_style( 'groovy-menu-style', $compiled_css );
			}
		}

		return '';
	}
}


add_action( 'admin_enqueue_scripts', 'groovy_menu_scripts_admin', 10, 1 );
if ( ! function_exists( 'groovy_menu_scripts_admin' ) ) {
	/**
	 * Enqueue scripts and styles for admin pages.
	 *
	 * @param string $hook_suffix suffix of the current page.
	 */
	function groovy_menu_scripts_admin( $hook_suffix ) {

		$screen            = get_current_screen();
		$menu_block_editor = false;
		if ( ! empty( $screen->id ) && 'edit-gm_menu_block' === $screen->id ) {
			$menu_block_editor = true;
		}

		// For any admin page.
		wp_enqueue_style( 'groovy-css-admin-menu', GROOVY_MENU_URL . 'assets/style/admin-common.css', [], GROOVY_MENU_VERSION );
		wp_enqueue_script( 'groovy-js-admin', GROOVY_MENU_URL . 'assets/js/admin.js', [], GROOVY_MENU_VERSION, true );

		// Only Welcome page.
		if ( in_array( $hook_suffix, array(
			'toplevel_page_groovy_menu_settings',
			'groovy-menu_page_groovy_menu_welcome',
			'toplevel_page_groovy_menu_welcome',
		), true ) ) {
			wp_enqueue_style( 'groovy-menu-style-welcome', GROOVY_MENU_URL . 'assets/style/welcome.css', array(), GROOVY_MENU_VERSION );
			wp_style_add_data( 'groovy-menu-style-welcome', 'rtl', 'replace' );
			wp_enqueue_script( 'groovy-js-welcome', GROOVY_MENU_URL . 'assets/js/welcome.js', [], GROOVY_MENU_VERSION, true );
		}

		// Only integration.
		if ( in_array( $hook_suffix, array(
				'groovy-menu_page_groovy_menu_integration',
				'toplevel_page_groovy_menu_integration',
			), true ) && ! isset( $_GET['action'] ) ) { // @codingStandardsIgnoreLine
			wp_enqueue_script( 'groovy-menu-js-dashboard', GROOVY_MENU_URL . 'assets/js/dashboard.js', array(), GROOVY_MENU_VERSION, true );
			wp_enqueue_script( 'groovy-menu-js-integration', GROOVY_MENU_URL . 'assets/js/integration.js', array(), GROOVY_MENU_VERSION, true );
			wp_enqueue_style( 'groovy-menu-style-font-roboto', 'https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap', array(), GROOVY_MENU_VERSION );
			wp_enqueue_style( 'groovy-menu-style-welcome', GROOVY_MENU_URL . 'assets/style/welcome.css', array(), GROOVY_MENU_VERSION );
			wp_style_add_data( 'groovy-menu-style-welcome', 'rtl', 'replace' );

		}

		// Only dashboard.
		if ( in_array( $hook_suffix, array(
				'groovy-menu_page_groovy_menu_settings',
				'toplevel_page_groovy_menu_settings',
			), true ) && ! isset( $_GET['action'] ) ) { // @codingStandardsIgnoreLine
			wp_enqueue_script( 'groovy-menu-js-dashboard', GROOVY_MENU_URL . 'assets/js/dashboard.js', array(), GROOVY_MENU_VERSION, true );
			wp_enqueue_style( 'groovy-menu-style-font-roboto', 'https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap', array(), GROOVY_MENU_VERSION );
		}

		// Only preset editor page.
		if ( in_array( $hook_suffix, array(
				'groovy-menu_page_groovy_menu_settings',
				'toplevel_page_groovy_menu_settings',
			), true ) && isset( $_GET['id'] ) && isset( $_GET['action'] ) && 'edit' === $_GET['action'] ) { // @codingStandardsIgnoreLine
			wp_enqueue_script( 'groovy-menu-js-preset', GROOVY_MENU_URL . 'assets/js/preset.js', [], GROOVY_MENU_VERSION, true );
			wp_localize_script( 'groovy-menu-js-preset', 'groovyMenuNonce', array( 'style' => esc_attr( wp_create_nonce( 'gm_nonce_preset_save' ) ) ) );
		}

		// Only Appearance > Menus page.
		if ( 'nav-menus.php' === $hook_suffix ) {
			wp_enqueue_media();
			wp_enqueue_script( 'groovy-menu-js-appearance', GROOVY_MENU_URL . 'assets/js/appearance.js', [], GROOVY_MENU_VERSION, true );
		}

		// Only Debug page.
		if ( 'tools_page_groovy_menu_debug_page' === $hook_suffix ) {
			wp_enqueue_script( 'groovy-menu-js-appearance', GROOVY_MENU_URL . 'assets/js/debug.js', [], GROOVY_MENU_VERSION, true );
		}

		$allow_pages = array(
			'toplevel_page_groovy_menu_settings',
			'groovy_menu_integration',
			'groovy_menu_welcome',
			'groovy_menu_license',
			'groovy-menu_page_groovy_menu_settings',
			'groovy-menu_page_groovy_menu_integration',
			'groovy-menu_page_groovy_menu_welcome',
			'toplevel_page_groovy_menu_welcome',
			'groovy-menu_page_groovy_menu_license',
			'tools_page_groovy_menu_debug_page',
			'nav-menus.php',
		);

		// Only Allowed pages.
		if ( in_array( $hook_suffix, $allow_pages, true ) || $menu_block_editor ) {

			wp_add_inline_script( 'groovy-js-admin', 'var groovyMenuL10n = ' . wp_json_encode( GroovyMenuUtils::l10n( true ) ) . ';' );

			$groovy_menu_localize = array(
				'GroovyMenuAdminUrl' => get_admin_url( null, 'admin.php?page=groovy_menu_settings', 'relative' ),
				'GroovyMenuSiteUrl'  => get_site_url(),
			);
			wp_localize_script( 'groovy-js-admin', 'groovyMenuLocalize', $groovy_menu_localize );

			wp_enqueue_style( 'groovy-css-admin', GROOVY_MENU_URL . 'assets/style/admin.css', [], GROOVY_MENU_VERSION );
			wp_style_add_data( 'groovy-css-admin', 'rtl', 'replace' );

			foreach ( \GroovyMenu\FieldIcons::getFonts() as $name => $icon ) {
				wp_enqueue_style( 'groovy-menu-style-fonts-' . $name, GroovyMenuUtils::getUploadUri() . 'fonts/' . $name . '.css', [], GROOVY_MENU_VERSION );
			}

			/**
			 * Fires when enqueue_script admin for Groovy Menu
			 *
			 * @since 2.2.13
			 */
			do_action( 'gm_enqueue_script_admin_actions' );
		}

	}
}


add_action( 'admin_enqueue_scripts', 'gm_include_code_editor', 10, 1 );
if ( ! function_exists( 'gm_include_code_editor' ) ) {
	/**
	 * Enqueue scripts and styles of codemirror for textarea.
	 *
	 * @param string $hook_suffix suffix of the current page.
	 */
	function gm_include_code_editor( $hook_suffix ) {

		if ( 'toplevel_page_groovy_menu_settings' !== $hook_suffix && 'groovy-menu_page_groovy_menu_settings' !== $hook_suffix ) {
			return;
		}

		$output = '';

		foreach ( array( 'css', 'javascript' ) as $type ) {

			$codemirror_params = array( 'autoRefresh' => true );

			if ( 'javascript' === $type ) {
				$codemirror_params['closeBrackets'] = true;
			}

			$settings = false;
			// function wp_enqueue_code_editor() since WP 4.9 .
			if ( function_exists( 'wp_enqueue_code_editor' ) ) {
				$settings = wp_enqueue_code_editor( array(
					'type'       => 'text/' . $type,
					'codemirror' => $codemirror_params,
				) );
			}

			if ( false !== $settings ) {

				$output .= sprintf( '
					var groovyMenuCodeMirror%3$sAreas = $(".gmCodemirrorInit[data-lang_type=\'%2$s\']");
					if (groovyMenuCodeMirror%3$sAreas.length > 0) {
						$.each(groovyMenuCodeMirror%3$sAreas, function(key, element) {
							var codeEditorObj = wp.codeEditor.initialize( element, %1$s );
							codeEditorObj.codemirror.on("change", function( cm ) {
								cm.save();
							});
						});
					}',
					wp_json_encode( $settings ),
					$type,
					strtoupper( $type )
				);
			}
		}


		// Add inline js.
		if ( $output ) {
			wp_add_inline_script(
				'code-editor',
				'(function ($) { $(function () {
				' . $output . '
				});})(jQuery)'
			);
		}

	}
}


add_filter( 'woocommerce_add_to_cart_fragments', 'groovy_menu_woocommerce_add_to_cart_fragments', 50 );

if ( ! function_exists( 'groovy_menu_woocommerce_add_to_cart_fragments' ) ) {
	/**
	 * Mini cart fix
	 *
	 * @param array $fragments elements of cart.
	 *
	 * @return mixed
	 */
	function groovy_menu_woocommerce_add_to_cart_fragments( $fragments ) {
		global $woocommerce;
		$count = $woocommerce->cart->cart_contents_count;

		$fragments['.gm-cart-counter'] = groovy_menu_woocommerce_mini_cart_counter( $count );

		return $fragments;
	}
}


if ( ! function_exists( 'groovy_menu_woocommerce_mini_cart_counter' ) ) {
	/**
	 * Mini cart counter
	 *
	 * @param string $count count of elements.
	 *
	 * @return string
	 */
	function groovy_menu_woocommerce_mini_cart_counter( $count = '' ) {
		if ( empty( $count ) ) {
			$count = '';
		}

		$count_text = ' <span class="gm-cart-counter">' . esc_html( $count ) . '</span> ';

		return $count_text;
	}
}


if ( ! function_exists( 'groovy_menu_add_gfonts_fontface' ) ) {
	/**
	 * @param $preset_id
	 * @param $font_option
	 * @param $common_font_family
	 *
	 * @return string
	 */
	function groovy_menu_add_gfonts_fontface( $preset_id, $font_option, $common_font_family, $add_inline = false ) {
		$output = '';
		if ( class_exists( 'GroovyMenuGFonts' ) ) {
			$google_fonts = new GroovyMenuGFonts();

			$output = $google_fonts->add_gfont_face( $preset_id, $font_option, $common_font_family, $add_inline );
		}

		return $output;
	}
}


add_action( 'wp_head', 'groovy_menu_add_gfonts_from_pre_storage' );

if ( ! function_exists( 'groovy_menu_add_gfonts_from_pre_storage' ) ) {
	/**
	 * Add link tag with google fonts.
	 */
	function groovy_menu_add_gfonts_from_pre_storage() {
		$font_data = \GroovyMenu\PreStorage::get_instance()->get_preset_data_by_key( 'font_family' );

		if ( ! empty( $font_data ) ) {
			$font_family_exist = array();
			foreach ( $font_data as $_preset_id => $font_family_array ) {
				foreach ( $font_family_array as $index => $font_family ) {

					// Prevent duplicate.
					if ( in_array( $font_family, $font_family_exist, true ) ) {
						continue;
					}

					// Store for duplicate check.
					$font_family_exist[] = $font_family;

					echo '
<link rel="stylesheet" id="gm-google-fonts-' . esc_attr( $index ) . '" href="https://fonts.googleapis.com/css?family=' . esc_attr( $font_family ) . '" type="text/css" media="all">
';
				}
			}
		}
	}
}


if ( ! function_exists( 'groovy_menu_check_gfonts_params' ) ) {
	/**
	 * Enable or Disable google fonts loading from local directory
	 */
	function groovy_menu_check_gfonts_params() {

		$google_fonts_local = false;
		$styles_class       = new GroovyMenuStyle( null );

		if ( $styles_class->getGlobal( 'tools', 'google_fonts_local' ) ) {
			$google_fonts_local = true;
		}


		if ( class_exists( 'GroovyMenuGFonts' ) ) {
			$google_fonts = new GroovyMenuGFonts();

			if ( $google_fonts_local ) {

				$need_fonts = $google_fonts->get_specific_fonts();

				foreach ( $need_fonts as $_font ) {
					if ( ! empty( $_font['zip_url'] ) ) {
						$google_fonts->download_font( $_font['zip_url'] );
					}
				}
			} else {
				delete_transient( $google_fonts->get_opt_name() );
				delete_transient( $google_fonts->get_opt_name() . '__current' );
				delete_option( $google_fonts->get_opt_name() . '__downloaded' );
			}
		}

	}
}

<?php
/**
 * WordPress Admin
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WordPress\Plugin_Admin;

defined( 'ABSPATH' ) or exit;

/**
 * The menus handler class.
 *
 * @since 1.0.0
 */
class Menus {

	/** @var string the minimum capability to load the menu(s) */
	const CAPABILITY = 'manage_options';

	/** @var string slug for the top-level menu item */
	const MENU_SLUG = 'skyverge';


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->add_hooks();
	}


	/**
	 * Adds the necessary action and filter hooks.
	 *
	 * @since 1.0.0
	 */
	private function add_hooks() {

		// enqueue the assets
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		// add main menu item
		add_action( 'admin_menu', [ $this, 'add_menu_item' ] );
	}


	/**
	 * Enqueues the assets.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {

		// the menu styles are loaded on every page
		wp_enqueue_style( 'sv-wordpress-plugin-admin-menus', Package::get_package_url() . '/assets/css/menus.css', [], Package::VERSION );

		// enqueue an admin menu script
		$script_url = 'https://dashboard-assets.skyverge.com/scripts/admin.js';

		/**
		 * Filters the admin menu script URL.
		 *
		 * @since 1.0.1
		 *
		 * @param string $script_url default URL
		 */
		$script_url = (string) apply_filters( 'sv_wordpress_plugin_admin_client_admin_script_url', $script_url );

		wp_enqueue_script( 'sv-wordpress-plugin-admin-client-admin', $script_url, [], Package::VERSION, true );

		/* @see https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/ */
		wp_localize_script( 'sv-wordpress-plugin-admin-client-admin', 'SVWPPluginAdminAPIParams', [
			'root'  => esc_url_raw( rest_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' )
		] );
	}


	/**
	 * Registers the top-level menu page.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_menu_item() {

		$menu_title = __( 'SkyVerge', 'sv-wordpress-plugin-admin' );
		$page_title = __( 'SkyVerge Dashboard', 'sv-wordpress-plugin-admin' );
		$menu_html  = '<div id="skyverge-dashboard-react-main-menu-item"></div>';

		add_menu_page( $page_title, $menu_title . $menu_html, self::CAPABILITY, self::MENU_SLUG, [ Package::instance()->get_dashboard_handler(), 'render_page' ], null, '58.5' );
	}


}

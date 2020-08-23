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
 * The Page handler class, extended by specific pages.
 *
 * @since 1.0.0
 */
abstract class Page {

	/** @var string the minimum capability to load the menu(s) */
	const CAPABILITY = 'manage_options';

	/** @var string $screen_id screen ID  */
	protected $screen_id;

	/** @var string $page_title page title  */
	protected $page_title;


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

		add_action( 'admin_menu', [ $this, 'add_menu_item' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		/**
		 * Hides third party admin notices on the SkyVerge pages.
		 * @see \Automattic\WooCommerce\Admin\Loader::inject_before_notices()
		 */
		add_action( 'admin_notices', [ $this, 'inject_before_notices' ], -9999 );
		add_action( 'admin_notices', [ $this, 'inject_after_notices' ], PHP_INT_MAX );
	}


	/**
	 * Registers the menu page.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_menu_item() {

		add_submenu_page( Menus::MENU_SLUG, $this->page_title, $this->page_title, self::CAPABILITY, $this->screen_id, [ $this, 'render_page' ] );
	}


	/**
	 * Enqueues the necessary assets.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {

		if ( ! self::is_skyverge_page() ) {
			return;
		}

		// Typekit fonts (Sofia Pro etc.)
		wp_enqueue_style( 'sv-wordpress-plugin-admin-fonts', 'https://use.typekit.net/fsd0oby.css', [], Package::VERSION );

		// Dashboard React FE
		$script_url = 'https://dashboard-assets.skyverge.com/scripts/index.js';

		/**
		 * Filters the client React script URL.
		 *
		 * @since 1.0.1
		 *
		 * @param string $script_url default URL
		 */
		$script_url = (string) apply_filters( 'sv_wordpress_plugin_admin_client_script_url', $script_url );

		wp_enqueue_script( 'sv-wordpress-plugin-admin-client', $script_url, [], Package::VERSION, true );

		/* @see https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/ */
		wp_localize_script( 'sv-wordpress-plugin-admin-client', 'SVWPPluginAdminAPIParams', [
			'root'  => esc_url_raw( rest_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' )
		] );
	}


	/**
	 * Renders the page markup.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function render_page() {

		?>
		<div id="skyverge-dashboard-react-root" style="margin-left: -20px;"></div>
		<?php
	}


	/**
	 * Hides admin notices.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function inject_before_notices() {

		if ( ! self::is_skyverge_page() ) {
			return;
		}

		?>
		<style type="text/css">
			.skyverge-dashboard-hidden {
				display: none !important;
			}
		</style>
		<div class="skyverge-dashboard-hidden">
			<div class="wp-header-end"></div>
		<?php
	}


	/**
	 * Hides admin notices.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function inject_after_notices() {

		if ( ! self::is_skyverge_page() ) {
			return;
		}

		?>
		</div>
		<?php
	}


	/**
	 * Checks if the current page is the SkyVerge Dashboard page.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_skyverge_page() {
		global $current_screen;

		$screen_ids = [
			'toplevel_page_' . Dashboard::SCREEN_ID,
			'skyverge_page_' . Dashboard::SCREEN_ID,
			'skyverge_page_' . Support::SCREEN_ID,
		];

		return ! empty( $current_screen ) && in_array( $current_screen->id, $screen_ids, true );
	}


}

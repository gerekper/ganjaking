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

use SkyVerge\WordPress\Plugin_Admin\REST_API\Controllers\Shop;
use SkyVerge\WordPress\Plugin_Admin\REST_API\Controllers\Support_Requests;
use SkyVerge\WordPress\Plugin_Admin\REST_API\Controllers\Messages;

defined( 'ABSPATH' ) or exit;

/**
 * The REST API handler class.
 *
 * @since 1.0.0
 */
class REST_API {


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

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}


	/**
	 * Registers all of the API routes via the controllers.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {

		require_once( Package::get_package_path() . '/REST_API/Controllers/Shop.php' );
		$shop_controller = new Shop();
		$shop_controller->register_routes();

		require_once( Package::get_package_path() . '/REST_API/Controllers/Support_Requests.php' );
		$support_requests_controller = new Support_Requests();
		$support_requests_controller->register_routes();

		require_once( Package::get_package_path() . '/REST_API/Controllers/Messages.php' );
		$support_requests_controller = new Messages();
		$support_requests_controller->register_routes();
	}


}

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
 * The Dashboard page handler class.
 *
 * @since 1.0.0
 */
class Dashboard extends Page {

	/**
	 * @var string ID of the screen
	 *
	 * Uses the same slug as the parent menu item, to avoid an empty "SkyVerge" submenu item.
	 */
	const SCREEN_ID = 'skyverge';


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->screen_id  = self::SCREEN_ID;
		$this->page_title = __( 'Dashboard', 'sv-wordpress-plugin-admin' );

		parent::__construct();
	}


	/**
	 * Registers the menu page.
	 *
	 * Overrides parent method to insert some HTML placeholder in the submenu item.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_menu_item() {

		$submenu_item_html = '<div id="skyverge-dashboard-react-dashboard-menu-item"></div>';

		add_submenu_page( Menus::MENU_SLUG, $this->page_title, $this->page_title . $submenu_item_html, self::CAPABILITY, $this->screen_id, [ $this, 'render_page' ] );
	}


}

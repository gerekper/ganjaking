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
 * The Support (Get Help) page handler class.
 *
 * @since 1.0.0
 */
class Support extends Page {

	/** @var string ID of the screen */
	const SCREEN_ID = 'skyverge-support';


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->screen_id  = self::SCREEN_ID;
		$this->page_title = __( 'Get Help', 'sv-wordpress-plugin-admin' );

		parent::__construct();
	}


}

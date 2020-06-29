<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\API\v2;

use SkyVerge\WooCommerce\Memberships\API\Controller\Membership_Plans as Membership_Plans_Controller;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Membership Plans REST API V2 handler.
 *
 * @since 1.11.0
 */
class Membership_Plans extends Membership_Plans_Controller {


	/**
	 * Membership Plans REST API V2 constructor.
	 *
	 * @since 1.11.0
	 */
	public function __construct() {

		parent::__construct();

		$this->version   = 'v2';
		$this->namespace = 'wc/v2/memberships';
	}


}

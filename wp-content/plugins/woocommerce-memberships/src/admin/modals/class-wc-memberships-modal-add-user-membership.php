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
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Add a user to a membership plan.
 *
 * This modal is used when a user when we need to search users to assign memberships to.
 *
 * @since 1.9.0
 */
class WC_Memberships_Modal_Add_User_Membership extends \WC_Memberships_Member_Modal {


	/**
	 * Constructs the modal.
	 *
	 * @since 1.9.0
	 */
	public function __construct() {

		parent::__construct();

		$this->id                  = 'wc-memberships-modal-add-user-membership';
		$this->title               = __( 'Add Member', 'woocommerce-memberships' );
		$this->action_button_label = __( 'Add Member', 'woocommerce-memberships' );
	}


	/**
	 * Returns the modal description.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	protected function get_description() {
		return __( 'Search for an existing user or create a new one to add as a new member.', 'woocommerce-memberships' );
	}


}

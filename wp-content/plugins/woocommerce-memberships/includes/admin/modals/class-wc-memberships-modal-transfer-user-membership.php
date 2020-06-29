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

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Transfer membership from a user to another.
 *
 * This modal is used when a user to transfer a membership to needs to be selected.
 * It is very similar to the modal to add a new user, so we can extend that modal for simplicity.
 *
 * @see \WC_Memberships_Modal_Add_User_Membership
 *
 * @since 1.9.0
 */
class WC_Memberships_Modal_Transfer_User_Membership extends \WC_Memberships_Member_Modal {


	/**
	 * Modal constructor.
	 *
	 * @since 1.9.0
	 */
	public function __construct() {

		parent::__construct();

		$this->id                  = 'wc-memberships-modal-transfer-user-membership';
		$this->title               = __( 'Transfer Membership', 'woocommerce-memberships' );
		$this->action_button_label = __( 'Transfer Membership', 'woocommerce-memberships' );
	}


	/**
	 * Returns the modal main description.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	protected function get_description() {
		return __( 'Search for an existing user or create a new user to transfer this membership to.', 'woocommerce-memberships' );
	}


	/**
	 * Returns the options for adding a member (new or existing).
	 *
	 * @since 1.9.0
	 *
	 * @return array associative array of option values and labels
	 */
	protected function get_user_source_options() {
		return array(
			'existing' => __( 'Transfer this membership to an existing user', 'woocommerce-memberships' ),
			'new'      => __( 'Create a new user to transfer this membership to', 'woocommerce-memberships' ),
		);
	}

}

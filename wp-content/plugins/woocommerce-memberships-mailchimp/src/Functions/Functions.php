<?php
/**
 * MailChimp for WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade MailChimp for WooCommerce Memberships to newer
 * versions in the future. If you wish to customize MailChimp for WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships-mailchimp/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2017-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;


/**
 * Gets the One True Instance of Memberships MailChimp.
 *
 * @since 1.0.0
 *
 * @return \SkyVerge\WooCommerce\Memberships\MailChimp\Plugin
 */
function wc_memberships_mailchimp() {

	return \SkyVerge\WooCommerce\Memberships\MailChimp\Plugin::instance();
}


/**
 * Returns a MailChimp audience object.
 *
 * @since 1.0.0
 *
 * @param string|null $list_id optional: will default to current audience
 * @return null|\SkyVerge\WooCommerce\Memberships\MailChimp\MailChimp_List
 */
function wc_memberships_mailchimp_get_list( $list_id = null ) {

	return \SkyVerge\WooCommerce\Memberships\MailChimp\MailChimp_Lists::get_list( $list_id );
}

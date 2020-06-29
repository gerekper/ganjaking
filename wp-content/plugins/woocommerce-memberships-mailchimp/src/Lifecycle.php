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

namespace SkyVerge\WooCommerce\Memberships\MailChimp;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Lifecycle events handler static class.
 *
 * @since 1.0.0
 *
 * @method Plugin get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 1.0.9
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'1.0.1',
			'1.0.8',
		];
	}


	/**
	 * Checks whether the current is a brand new install of MailChimp.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_new_install() {

		return null === get_option( 'wc_memberships_mailchimp_version', null );
	}


	/**
	 * Performs tasks upon plugin first install.
	 *
	 * @since 1.0.6
	 */
	protected function install() {

		$this->get_plugin()->clear_transients();
	}


	/**
	 * Updates to 1.0.1
	 *
	 * @since 1.0.9
	 */
	protected function upgrade_to_1_0_1() {

		// save new settings defaults
		update_option( 'wc_memberships_mailchimp_sync_members_opt_in', 'automatic' );
		update_option( 'wc_memberships_mailchimp_sync_members_opt_in_prompt_text', __( 'You are not subscribed to the members email list. Would you like to subscribe now?', 'woocommerce-memberships-mailchimp' ) );
		update_option( 'wc_memberships_mailchimp_sync_members_opt_in_button_text', __( 'Add me!', 'woocommerce-memberships-mailchimp' ) );

		// if upgrading and there's a connection, set an option so we can remind admins to sync members
		if ( wc_memberships_mailchimp()->is_connected() ) {
			update_option( '_wc_memberships_mailchimp_sync_needs_members_sync', true );
		}
	}


	/**
	 * Updates to version 1.0.8
	 *
	 * Assumes that plans have had already their default merge fields generated and sets a post meta to avoid forcing these to MailChimp again in case they've been deleted.
	 *
	 * @since 1.0.9
	 */
	protected function upgrade_to_1_0_8() {

		$plans = wc_memberships_get_membership_plans();

		foreach ( $plans as $plan ) {

			$default_merge_tag = MailChimp_Lists::parse_merge_tag( $plan->get_slug() );

			update_post_meta( $plan->get_id(), '_mailchimp_sync_default_merge_tag', $default_merge_tag );
		}
	}


}

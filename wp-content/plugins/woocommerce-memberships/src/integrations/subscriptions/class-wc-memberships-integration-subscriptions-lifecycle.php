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
 * Integration class for WooCommerce Subscriptions lifecycle.
 *
 * @since 1.6.0
 */
class WC_Memberships_Integration_Subscriptions_Lifecycle {


	/**
	 * Adds Memberships/Subscriptions lifecycle hooks.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		// Upon Memberships or Subscription activation.
		add_action( 'wc_memberships_activated',              array( $this, 'handle_activation' ), 1 );
		add_action( 'woocommerce_subscriptions_activated',   array( $this, 'handle_activation' ), 1 );
		// Upon Subscriptions deactivation.
		add_action( 'woocommerce_subscriptions_deactivated', array( $this, 'handle_deactivation' ) );
	}


	/**
	 * Handle Subscriptions plugin activation.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 */
	public function handle_activation() {

		$this->update_subscription_memberships();
	}


	/**
	 * Handles Subscriptions plugin deactivation.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 */
	public function handle_deactivation() {

		$this->pause_free_trial_subscription_memberships();
	}


	/**
	 * Pauses subscription-based memberships.
	 *
	 * Find any memberships that are on free trial and pause them.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 */
	public function pause_free_trial_subscription_memberships() {

		// get user memberships on free trial status
		$posts = get_posts( array(
			'post_type'   => 'wc_user_membership',
			'post_status' => 'wcm-free_trial',
			'nopaging'    => true,
		) );

		// bail out if there are no memberships on free trial
		if ( empty( $posts ) ) {
			return;
		}

		// pause the memberships found
		foreach ( $posts as $post ) {

			$user_membership = wc_memberships_get_user_membership( $post );
			$user_membership->pause_membership( __( 'Membership paused because WooCommerce Subscriptions was deactivated.', 'woocommerce-memberships' ) );
		}
	}


	/**
	 * Re-activates subscription-based memberships.
	 *
	 * Find any memberships tied to a subscription that are paused, which may need to be re-activated or put back on trial.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 */
	public function update_subscription_memberships() {

		if ( $integration = wc_memberships()->get_integrations_instance()->get_subscriptions_instance() ) {

			$background_handler  = $integration->get_utilities_instance()->get_activation_background_instance();
			$user_membership_ids = get_posts( array(
				'post_type'    => 'wc_user_membership',
				'fields'       => 'ids',
				'nopaging'     => true,
				'post_status'  => 'any',
				'meta_key'     => '_subscription_id',
				'meta_value'   => '0',
				'meta_compare' => '>',
			) );

			// bother only if we have memberships in the first place...
			if ( ! empty( $user_membership_ids ) ) {

				$single_run = true;

				// will process this in background only if environment allows it...
				if ( $background_handler && $background_handler->test_connection() ) {

					$single_run    = false;
					$existing_jobs = $background_handler->get_jobs();

					// delete past existing jobs
					if ( ! empty( $existing_jobs ) )  {

						foreach ( $existing_jobs as $past_job ) {

							$background_handler->delete_job( $past_job );
						}
					}

					$job = $background_handler->create_job( array(
						'user_membership_ids' => $user_membership_ids,
					) );

					if ( $job ) {
						$background_handler->dispatch();
					} else {
						$single_run = true;
					}
				}

				// ...otherwise attempt to process all memberships in one go (original approach)
				if ( $single_run ) {

					foreach ( $user_membership_ids as $user_membership_id ) {

						$background_handler->process_item( $user_membership_id );
					}
				}
			}
		}
	}


}

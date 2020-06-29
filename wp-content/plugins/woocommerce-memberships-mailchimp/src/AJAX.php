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
 * needs please refer to https://docs.woocommerce.com/document/mailchimp-for-woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2017-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\MailChimp;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Main AJAX handler.
 *
 * @since 1.0.0
 */
class AJAX {


	/**
	 * Hooks into AJAX.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// checks whether the entered MailChimp API key is valid
		add_action( 'wp_ajax_wc_memberships_mailchimp_sync_is_api_key_valid', array( $this, 'is_api_key_valid' ) );

		// updates the settings page when selecting another audience
		add_action( 'wp_ajax_wc_memberships_mailchimp_sync_update_list_settings', array( $this, 'update_list_settings_html' ) );

		// syncs a single member to MailChimp
		add_action( 'wp_ajax_wc_memberships_mailchimp_sync_update_member', array( $this, 'sync_member' ) );

		// syncs all members with MailChimp
		add_action( 'wp_ajax_wc_memberships_mailchimp_sync_start_members_sync', array( $this, 'start_members_sync' ) );

		// gets the status of a members sync batch job
		add_action( 'wp_ajax_wc_memberships_mailchimp_sync_get_members_sync_status', array( $this, 'get_members_sync_status' ) );

		// flag a user to opt in for subscribing to the members audience
		add_action( 'wp_ajax_wc_memberships_mailchimp_sync_member_opt_in',        array( $this, 'sync_member_opt_in' ) );
		add_action( 'wp_ajax_nopriv_wc_memberships_mailchimp_sync_member_opt_in', array( $this, 'sync_member_opt_in' ) );
	}


	/**
	 * Checks whether the MailChimp API key is valid.
	 *
	 * This is normally used as callback to check if an API key is valid while the user is typing it in the settings.
	 *
	 * However, this will be also save the API key without the user needing to update the plugin settings.
	 * There's a visual feedback in the settings page in the form of a green check mark or red mark, which should give a cue whether the key is valid or not.
	 *
	 * One reason to store the API key on the fly is to ensure that on the next page load this is correctly picked up by the plugin.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function is_api_key_valid() {

		check_ajax_referer( 'mailchimp-sync-is-valid-api-key', 'security' );

		$plugin      = wc_memberships_mailchimp();
		$old_api_key = $plugin->get_api_key();
		$new_api_key = isset( $_POST['api_key'] ) && is_string( $_POST['api_key'] ) ? $_POST['api_key'] : '';
		$success     = '' !== $new_api_key && $plugin->get_api_instance()->is_api_key_valid( $_POST['api_key'] );

		$plugin->set_api_key( $new_api_key );

		if ( $success ) {

			if ( $old_api_key !== $new_api_key && ( $list = MailChimp_Lists::get_list() ) ) {

				$plugin->get_membership_plans_instance()->create_plans_merge_field_tags( $list );

				if ( ! $list->get_active_status_merge_field() ){

					$list->set_default_active_status_merge_field( true );
				}
			}

			wp_send_json_success();

		} else {

			wp_send_json_error();
		}
	}


	/**
	 * Updates the HTML in the settings page when a new audience is selected.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function update_list_settings_html() {

		check_ajax_referer( 'mailchimp-sync-update-list-settings', 'security' );

		if ( ! empty( $_POST['list_id'] ) ) {

			$html  = null;
			$admin = wc_memberships_mailchimp()->get_admin_instance();
			$error = 'Cannot load admin instance';

			if ( $admin ) {

				$settings = $admin->get_settings_instance();
				$error    = 'Cannot load settings instance';

				if ( $settings ) {

					$error = sprintf( 'Cannot return HTML for audience ID %s', $_POST['list_id'] );

					ob_start();

					$settings->get_list_settings_html( $_POST['list_id'] );

					$html = ob_get_clean();
				}
			}

			if ( ! empty( $html ) ) {
				wp_send_json_success( $html );
			} else {
				wp_send_json_error( $error );
			}
		}

		die;
	}


	/**
	 * Updates a user membership with a MailChimp audience subscription.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function sync_member() {

		check_ajax_referer( 'mailchimp-sync-update-member', 'security' );

		$user_membership  = ! empty( $_POST['user_membership_id'] ) ? wc_memberships_get_user_membership( $_POST['user_membership_id'] ) : null;
		$user_memberships = wc_memberships_mailchimp()->get_user_memberships_instance();

		if ( $user_membership && $user_memberships && $user_memberships->sync_member( $user_membership ) ) {
			wp_send_json_success();
		}

		wp_send_json_error( __( 'Could not sync this member.', 'woocommerce-memberships-mailchimp' ) );
	}


	/**
	 * Starts a member sync job.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function start_members_sync() {

		check_ajax_referer( 'mailchimp-sync-start-members-sync', 'security' );

		// reset our upgrade option to remove any notices
		update_option( '_wc_memberships_mailchimp_sync_needs_members_sync', false );

		// get the user IDs who have a membership or have previously been synced with MailChimp
		$user_ids = wc_memberships_mailchimp()->get_user_memberships_instance()->get_member_ids_for_sync();

		if ( ! empty( $user_ids ) ) {

			$job = wc_memberships_mailchimp()->get_background_sync_instance()->create_job( array(
				'user_ids' => $user_ids,
				'batches'  => array(),
			) );

			// dispatch the background processor
			wc_memberships_mailchimp()->get_background_sync_instance()->dispatch();

			wp_send_json_success( $job );

		} else {

			wp_send_json_error( array(
				'message' => __( 'No members to sync.', 'woocommerce-memberships-mailchimp' ),
			) );
		}
	}


	/**
	 * Gets a sync job's status.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @throws \Exception
	 */
	public function get_members_sync_status() {

		check_ajax_referer( 'mailchimp-sync-get-members-sync-status', 'security' );

		try {

			$job_id = Framework\SV_WC_Helper::get_posted_value( 'job_id' );

			if ( empty( $job_id ) ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Job ID missing.' );
			}

			$job = wc_memberships_mailchimp()->get_background_sync_instance()->get_job( $job_id );

			if ( empty( $job ) ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Job could not be found.' );
			}

			// if loopback connections aren't supported, manually process the job
			if ( ! wc_memberships_mailchimp()->get_background_sync_instance()->test_connection() && 'completed' !== $job->status ) {
				$job = wc_memberships_mailchimp()->get_background_sync_instance()->process_job( $job );
			}

			$status   = $job->status;
			$finished = 0;

			// if background processing is complete, check each MailChimp batch operation's status before considering the sync finished
			if ( 'completed' === $status ) {

				foreach ( $job->batch_ids as $batch_id ) {

					if ( $batch = wc_memberships_mailchimp()->get_api_instance()->get_batch_operation( $batch_id ) ) {

						$finished += (int) $batch->finished_operations;

						// if one batch isn't finished, consider the background job still processing
						if ( 'finished' !== $batch->status ) {
							$status = 'processing';
						}

					} else {

						$finished = $job->progress;
					}
				}
			}

			// if everything is finally is finished, delete the job
			if ( 'completed' === $status ) {
				wc_memberships_mailchimp()->get_background_sync_instance()->delete_job( $job );
			}

			$job_data = array(
				'id'       => $job->id,
				'status'   => $status,
				'total'    => count( $job->user_ids ),
				'finished' => $finished,
			);

			wp_send_json_success( $job_data );

		} catch ( Framework\SV_WC_Plugin_Exception $e ) {

			wp_send_json_error( $e->getMessage() );
		}
	}


	/**
	 * Flags a user for audience opt in.
	 *
	 * @internal
	 *
	 * @since 1.0.1
	 */
	public function sync_member_opt_in() {

		check_ajax_referer( 'wc_memberships_mailchimp_sync_member_opt_in_nonce', 'security' );

		if ( ! empty( $_POST['user_id'] ) && is_numeric( $_POST['user_id'] ) ) {

			$user = get_user_by( 'id', (int) $_POST['user_id'] );

			if ( $user instanceof \WP_User ) {

				$opt_in = (bool) update_user_meta( $user->ID, '_wc_memberships_mailchimp_sync_opt_in', 'yes' );

				// sync this member already since they opted in
				if ( $opt_in ) {
					wc_memberships_mailchimp()->get_user_memberships_instance()->sync_member( $user );
				}

				wp_send_json_success( $opt_in );
			}
		}

		wp_send_json_error();
	}


}

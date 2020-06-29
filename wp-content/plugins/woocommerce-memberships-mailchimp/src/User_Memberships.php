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
 * User Memberships events handler.
 *
 * @since 1.0.0
 */
class User_Memberships {


	/**
	 * Hooks in user memberships and member user events.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// changes related to a user membership
		add_action( 'wc_memberships_user_membership_created',        array( $this, 'sync_new_user_membership' ), 10, 2 );
		add_action( 'wc_memberships_user_membership_saved',          array( $this, 'sync_new_user_membership' ), 10, 2 );
		add_action( 'wc_memberships_user_membership_status_changed', array( $this, 'sync_user_membership_status_change' ), 10, 3 );
		add_action( 'wc_memberships_user_membership_transferred',    array( $this, 'sync_user_membership_transfer' ), 10, 3 );
		add_action( 'before_delete_post',                            array( $this, 'sync_user_membership_deletion' ) );

		// changes related to the member user profile
		add_action( 'profile_update',                        array( $this, 'sync_updated_member' ), 10, 2 );
		add_action( 'woocommerce_checkout_update_user_meta', array( $this, 'sync_member' ) );
	}


	/**
	 * Handles events upon user membership creation (via purchase, sign up, or admin action).
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Memberships_Membership_Plan $membership_plan Related membership plan object
	 * @param array $args associative array of arguments
	 */
	public function sync_new_user_membership( $membership_plan, array $args ) {

		$user_membership_id = isset( $args['user_membership_id'] ) ? (int) $args['user_membership_id'] : null;

		if ( $user_membership_id > 0  ) {

			$user_membership = wc_memberships_get_user_membership( $user_membership_id );

			// before syncing: make sure the membership exists and is not an auto save (when created manually)
			if ( $user_membership && $user_membership->post && in_array( $user_membership->post->post_status, wc_memberships()->get_user_memberships_instance()->get_user_membership_statuses( false ), true ) ) {

				$this->sync_member( $user_membership->get_user() );
			}
		}
	}


	/**
	 * Handles events upon user membership status change.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership the user membership
	 * @param string $new_status new status updated to
	 * @param string $old_status old status before change
	 */
	public function sync_user_membership_status_change( $user_membership, $new_status, $old_status ) {

		if ( $old_status !== $new_status && ! ( 'active' === $new_status && 'expired' === $old_status ) ) {

			$list = MailChimp_Lists::get_list();
			$api  = wc_memberships_mailchimp()->get_api_instance();

			if ( $list && $api ) {

				$api->update_list_member_membership_status( $list, $user_membership );
			}
		}
	}


	/**
	 * Handles events upon user membership deletion.
	 *
	 * Note: this runs right before a post is deleted in WordPress, so it still exists in database.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The post ID that may belong to a user membership post
	 */
	public function sync_user_membership_deletion( $post_id ) {

		$list = MailChimp_Lists::get_list();

		// the action runs before the post is effectively deleted, so we can still grab the membership
		if (    $list
		     && 'wc_user_membership' === get_post_type( $post_id )
		     && ( $user_membership = wc_memberships_get_user_membership( $post_id ) ) ) {

			$api  = wc_memberships_mailchimp()->get_api_instance();
			$user = $user_membership->get_user();

			// don't bother if user is not on the audience to begin with
			if ( $api && $user && $api->is_list_member( $user ) ) {

				$memberships = wc_memberships_get_user_memberships( $user->ID );
				$plan_count  = count( $memberships );

				// there is either more than one membership or the handling is to just unsubscribe the member from the audience
				if ( $plan_count > 1 || $list->is_deleted_memberships_handling( array( 'keep', 'unsubscribe' ) ) ) {

					// force move the membership to a non active status so it may trigger the non-active flag in MailChimp if the other plans aren't active either
					if ( ! $user_membership->has_status( array( 'expired', 'cancelled' ) ) ) {
						$user_membership->cancel_membership();
					}

					// syncs the membership: if there are no other active plans for this user (see above), it will mark the member as inactive
					$this->sync_member( $user );

					$member_data = array();
					$merge_field = $list->get_plan_merge_field( $user_membership->get_plan_id() );

					// this will remove the information for the plan merge field for the membership that is being deleted
					if ( ! empty( $merge_field ) && is_array( $merge_field ) ) {

						$member_data['merge_fields'] = array(
							current( $merge_field ) => ''
						);
					}

					// if there is only one membership and is the one being deleted, we may proceed to unsubscribe the user too
					if ( 1 === $plan_count && $list->is_deleted_memberships_handling( 'unsubscribe' ) ) {
						$member_data['status'] = 'unsubscribed';
					}

					if ( ! empty( $member_data ) ) {
						$api->update_list_member( $list->get_id(), $user, $member_data );
					}

				// user has only one membership and is the one being deleted
				} elseif ( $list->is_deleted_memberships_handling( 'remove' ) ) {

					$api->delete_list_member( $list->get_id(), $user );
				}
			}
		}
	}


	/**
	 * Handles events upon user membership transfer.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership the user membership that was transferred
	 * @param \WP_User $new_user the membership new owner
	 * @param \WP_User $old_user the membership old owner
	 */
	public function sync_user_membership_transfer( $user_membership, $new_user, $old_user ) {

		if ( $new_user && $old_user && $new_user->ID !== $old_user->ID ) {

			$this->sync_member( $old_user );
			$this->sync_member( $new_user );

			$old_user_memberships = wc_memberships_get_user_memberships( $old_user->ID );

			$list = MailChimp_Lists::get_list();

			// if the old user is left with no more memberships, and the settings dictate complete removal, delete the member in MC
			// TODO: consider handling this in User_Memberships::sync_member() {CW 2018-03-22}
			if ( count( $old_user_memberships ) < 1 && $list && $list->is_deleted_memberships_handling( 'remove' ) ) {
				wc_memberships_mailchimp()->get_api_instance()->delete_list_member( $list->get_id(), $old_user );
			}
		}
	}


	/**
	 * Gets a user object from a variety of sources (helper method).
	 *
	 * @since 1.0.8
	 *
	 * @param int|string|\WP_User|\WC_Memberships_User_Membership $user_id a user identifier
	 * @return \WP_User|null
	 */
	private function get_member_user( $user_id ) {

		$user = null;

		if ( $user_id instanceof \WP_User ) {
			$user = $user_id;
		} elseif ( $user_id instanceof \WC_Memberships_User_Membership ) {
			$user = $user_id->get_user();
		} elseif ( is_numeric( $user_id ) ) {
			$user = get_user_by( 'id', $user_id );
		} elseif ( is_string( $user_id ) ) {
			if ( is_email( $user_id ) ) {
				$user = get_user_by( 'email', $user_id );
			} else {
				$user = get_user_by( 'login', $user_id );
			}
		}

		return $user instanceof \WP_User ? $user : null;
	}


	/**
	 * Syncs a user/member with MailChimp upon WordPress profile update.
	 *
	 * Before syncing a member that has just updated their profile, we need to check if the changed their email address.
	 *
	 * @internal
	 *
	 * @since 1.0.8
	 *
	 * @param int $user_id the user ID
	 * @param \WP_User $old_user_data object containing the user data as it was before the current update
	 */
	public function sync_updated_member( $user_id, $old_user_data ) {

		$user      = $this->get_member_user( $user_id );
		$new_email = $user ? $user->user_email : '';
		$old_email = $old_user_data->user_email;

		// if the emails no longer match, we can issue a request to MailChimp to change their email as audience subscribers as well
		if ( $old_email && $new_email !== $old_email && ( $list = MailChimp_Lists::get_list() ) ) {

			/** @see User_Memberships::set_member_status() we don't need to update the member status meta since an email change wouldn't affect status */
			wc_memberships_mailchimp()->get_api_instance()->update_list_member( $list->get_id(), $old_email, [
				'email_address' => $new_email
			] );
		}

		$this->sync_member( $user );
	}


	/**
	 * Syncs a user/member with MailChimp.
	 *
	 * This will either create a new Member or update an existing, and then set merge tag values for each membership plan that's been configured in settings.
	 * The method is also used as an action callback to update a Member when they update their profile.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|\WP_User|\WC_Memberships_User_Membership|null|false $user_member WordPress user or Memberships User Membership object
	 * @param string $list_id specific audience ID to sync with
	 * @return bool
	 */
	public function sync_member( $user_member, $list_id = null ) {

		$success = false;

		if ( $user_member = $this->get_member_user( $user_member ) ) {

			// bail if updating the user meta at checkout before a membership is set for the current user
			if ( in_array( current_action(), array( 'profile_update', 'woocommerce_checkout_update_user_meta' ), true ) ) {

				$user_memberships = wc_memberships_get_user_memberships( $user_member );

				if ( empty( $user_memberships ) ) {
					return false;
				}
			}

			$list = MailChimp_Lists::get_list( $list_id );
			$api  = wc_memberships_mailchimp()->get_api_instance();

			if ( $list && ( $member = $api->sync_list_member( $list, $user_member ) ) ) {

				$this->set_member_status( $user_member->ID, array(
					'list_id' => $list->get_id(),
					'status'  => $member->status,
				) );

				$success = true;
			}
		}

		return $success;
	}


	/**
	 * Returns member IDs for audience syncing.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_User[]|int[] $users optional array of users or user IDs, or empty array (will fetch users)
	 * @return int[] array of member (user) IDs
	 */
	public function get_member_ids_for_sync( array $users = array() ) {
		global $wpdb;

		$user_ids = array();

		if ( empty( $users ) ) {

			$user_ids = get_users( array(
				'number' => PHP_INT_MAX,
				'fields' => 'ID',
			) );

		} else {

			foreach ( $users as $user ) {

				if ( $user instanceof \WP_User && ! empty( $user->ID ) ) {
					$user_ids[] = $user->ID;
				} elseif ( is_numeric( $user ) ) {
					$user_ids[] = (int) $user;
				}
			}
		}

		foreach ( $user_ids as $key => $user_id ) {

			$memberships = (int) $wpdb->get_var( $wpdb->prepare( "
				SELECT COUNT(*)
				FROM   $wpdb->posts
				WHERE  post_type = 'wc_user_membership'
				AND    post_author = %d
			", $user_id ) );

			$sync_status = $this->get_member_status( $user_id );

			// skip the user if they have no memberships and were never previously synced
			if ( $memberships <= 0 && empty( $sync_status ) ) {
				unset( $user_ids[ $key ] );
			}
		}

		return $user_ids;
	}


	/**
	 * Gets the user meta key to set the MailChimp member subscription status information.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_member_status_meta_key() {

		return '_mailchimp_sync_status';
	}


	/**
	 * Returns the audience subscription status information from a user membership.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id the member ID
	 * @return array
	 */
	public function get_member_status( $user_id ) {

		$member_status = get_user_meta( $user_id, $this->get_member_status_meta_key(), true );

		return is_array( $member_status ) ? $member_status : array();
	}


	/**
	 * Updates a user membership's audience subscription status.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id the member ID
	 * @param array $member_data associative array of member data
	 * @return bool
	 */
	public function set_member_status( $user_id, array $member_data ) {

		$data = wp_parse_args( $member_data, array(
			'list_id'   => MailChimp_Lists::get_current_list_id(),
			'status'    => 'subscribed',
			'timestamp' => current_time( 'timestamp', true ),
		) );

		return update_user_meta( $user_id, $this->get_member_status_meta_key(), $data );
	}


	/**
	 * Removes the MailChimp audience subscription status information from a user membership
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id the member ID
	 * @return bool
	 */
	public function delete_member_status( $user_id ) {

		return delete_user_meta( $user_id, $this->get_member_status_meta_key() );
	}


}

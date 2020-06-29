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

use SkyVerge\WooCommerce\PluginFramework\v5_7_1;

defined( 'ABSPATH' ) or exit;

/**
 * Integration class for User Switching plugin.
 *
 * @since 1.0.0
 */
class WC_Memberships_Integration_User_Switching {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_filter( 'post_row_actions', array( $this, 'customize_membership_plan_row_actions' ), 11, 2 );

		// custom admin actions
		add_action( 'admin_action_view_as_member', array( $this, 'view_as_member' ) );

		add_action( 'switch_back_user',  array( $this, 'remove_temp_user' ) );
		add_action( 'clear_auth_cookie', array( $this, 'remove_temp_user' ) );

		if ( get_user_meta( get_current_user_id(), '_wc_memberships_temp_user', true ) ) {
			add_filter( 'show_admin_bar', '__return_true', 9999 );
		}

	}


	/**
	 * Customizes membership plan row actions.
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions array of post actions
	 * @param \WP_Post $post post object
	 * @return array
	 */
	public function customize_membership_plan_row_actions( $actions, \WP_Post $post ) {
		global $typenow;

		if ( 'wc_membership_plan' !== $typenow ) {
			return $actions;
		}

		// add view as member action
		$actions['view_as_member'] = '<a href="' . wp_nonce_url( admin_url( 'edit.php?post_type=wc_membership_plan&action=view_as_member&amp;post=' . $post->ID ), 'wc-memberships-view-as-member-of_' . $post->ID ) . '" title="' . __( 'View site as a member of this plan', 'woocommerce-memberships' ) . '" rel="permalink">' .
		                             __( 'View site as member', 'woocommerce-memberships' ) .
		                             '</a>';

		return $actions;
	}


	/**
	 * Handles viewing the site as a member of a particular plan.
	 *
	 * @since 1.0.0
	 */
	public function view_as_member() {

		if ( empty( $_REQUEST['post'] ) ) {
			return;
		}

		// get the plan post ID
		$id = isset( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : '';

		check_admin_referer( 'wc-memberships-view-as-member-of_' . $id );

		$plan = wc_memberships_get_membership_plan( $id );

		// bail out if plan could not be determined
		if ( ! $id || ! $plan ) {
			return;
		}

		// create a temporary user
		$username = uniqid( 'wcm_' );

		/**
		 * Filter temporary user data.
		 *
		 * Allows adjusting the data for a temporary user,
		 * which is created to allow viewing the site as a member of a particular membership plan
		 *
		 * @since 1.0.0
		 *
		 * @param array $data
		 * @param \WC_Memberships_Membership_Plan $plan
		 */
		$temp_user_data = apply_filters( 'wc_memberships_temporary_user_data', array(
			'user_login'  => $username,
			'user_pass'   => uniqid( 'wcmp_' ),
			'user_email'  => $username . '@.example.com',
			/* translators: %s - Membership Plan name */
			'first_name'  => sprintf( __( '%s Plan', 'woocommerce-memberships' ), $plan->get_name() ),
			'last_name'   => __( 'Test User', 'woocommerce-memberships' ),
			/* translators: %s - Membership Plan name */
			'description' => sprintf( __( "A temporary user created for testing the %s membership plan. If you don't use it, feel free to delete this user.", 'woocommerce-memberships' ), $plan->get_name() ),
			'role'        => 'customer',
		), $plan );

		$user_id = wp_insert_user( $temp_user_data );

		if ( is_wp_error( $user_id ) ) {
			return;
		}

		// set a temporary value in DB indicating which temporary user has been created for the current user
		set_transient( 'wc_memberships_user_' . get_current_user_id() . '_viewing_as', $user_id, YEAR_IN_SECONDS );
		update_user_meta( $user_id, '_wc_memberships_temp_user', 1 );

		// create user membership
		$membership_id = wp_insert_post( array(
			'post_type'   => 'wc_user_membership',
			'post_parent' => $plan->get_id(),
			'post_author' => $user_id,
			'post_status' => 'wcm-active'
		) );

		// set membership start date to now
		update_post_meta( $membership_id, '_start_date', current_time( 'mysql', true ) );

		$user = get_user_by( 'id', $user_id );

		// now switch to that user
		$link = add_query_arg( array(
			'action'   => 'switch_to_user',
			'user_id'  => $user->ID,
			'_wpnonce' => wp_create_nonce( "switch_to_user_{$user->ID}" ),
		), wp_login_url() );

		wp_redirect( $link );
		exit;
	}


	/**
	 * Removes the temporary user.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id
	 */
	public function remove_temp_user( $user_id = null ) {

		$user_id = $user_id ? $user_id : get_current_user_id();

		if ( ! $user_id || ( isset( $_REQUEST['action'] ) && 'switch_to_user' === $_REQUEST['action'] ) ) {
			return;
		}

		$temp_user_id = get_transient( 'wc_memberships_user_' . $user_id . '_viewing_as' );

		// remove old, temporary user
		if ( $temp_user_id ) {

			if ( ! function_exists( 'wp_delete_user' ) ) {
				include ABSPATH . 'wp-admin/includes/user.php';
			}

			wp_delete_user( $temp_user_id );
		}
	}


}

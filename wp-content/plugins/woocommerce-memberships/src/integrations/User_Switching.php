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

namespace SkyVerge\WooCommerce\Memberships\Integrations;

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Integration class for User Switching plugin.
 *
 * @since 1.0.0
 */
class User_Switching {


	/** @var string the "view as member" action name */
	private $view_as_member_action_name = 'view_as_member';

	/** @var string action name used to trigger viewing as a member of a given %s plan ID */
	private $view_as_member_of_action_placeholder = 'wc-memberships-view-as-member-of_%s';

	/** @var string transient key used to store the temporary user ID, the %s placeholder is replaced with the current user switching from */
	private $transient_placeholder = 'wc_memberships_user_%s_viewing_as';

	/** @var string user meta key to flag a temporary user */
	private $temporary_user_meta_key = '_wc_memberships_temp_user';


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// add a row action link below a membership plan in the plans admin list screen
		add_filter( 'post_row_actions', [ $this, 'customize_membership_plan_row_actions' ], 11, 2 );
		// processes the custom action link
		add_action( "admin_action_{$this->view_as_member_action_name}", [ $this, 'view_as_member' ] );

		// when the admin user logs in again, or switches back from the temporary user, delete the temporary user
		add_action( 'switch_back_user',  [ $this, 'remove_temp_user' ] );
		add_action( 'clear_auth_cookie', [ $this, 'remove_temp_user' ] );

		// show the WordPress admin bar for the user switched to in front end
		if ( get_user_meta( get_current_user_id(), $this->temporary_user_meta_key, true ) ) {
			add_filter( 'show_admin_bar', '__return_true', 9999 );
		}
	}


	/**
	 * Gets the transient key to store the user ID switching to.
	 *
	 * @since 1.19.2
	 *
	 * @param null|int $user_id the ID of the user switching from (defaults to the current logged in user)
	 * @return string transient key
	 */
	private function get_transient_key( $user_id = null ) {

		$user_id = $user_id ?: get_current_user_id();

		return sprintf( $this->transient_placeholder, (string) $user_id );
	}


	/**
	 * Gets the "view as member" action name for a given plan.
	 *
	 * @since 1.19.2
	 *
	 * @param int $plan_id the ID of the plan to generate the action for
	 * @return string
	 */
	private function get_view_as_member_of_plan_action_name( $plan_id ) {

		return sprintf( $this->view_as_member_of_action_placeholder, (string) $plan_id );
	}


	/**
	 * Gets or generates a temporary plan member.
	 *
	 * @since 1.19.2
	 *
	 * @param \WC_Memberships_Membership_Plan $membership_plan the plan to generate a member for
	 * @return \WP_User user object
	 * @throws Framework\SV_WC_Plugin_Exception upon user creation or assignment error
	 */
	private function get_temporary_plan_member( \WC_Memberships_Membership_Plan $membership_plan ) {

		/**
		 * Allows third parties to set a specific user to use as temporary member to switch to.
		 *
		 * If a user is defined through this filter, it will not be deleted after the admin has switched back or logged in again.
		 * Please note that a membership will be created for this user, and if it exist already will be set to active to the current date.
		 * The membership will not be deleted when the main user switches back and it will be responsibility of third party code to handle it, if necessary.
		 *
		 * @since 1.19.2
		 *
		 * @param null|\WP_User default null, the user will be generated; if a user is specified, it will be used as the tempoaray user to switch to instead
		 * @param \WC_Memberships_Membership_Plan $membership_plan the membership plan the user will be assigned to
		 */
		$user = apply_filters( 'wc_memberships_user_switching_temporary_user', null, $membership_plan );

		if ( null === $user ) {

			// create a temporary user
			$username = uniqid( 'wcm_', false );

			/**
			 * Filter temporary user data.
			 *
			 * Allows adjusting the data for a temporary user,
			 * which is created to allow viewing the site as a member of a particular membership plan
			 *
			 * @since 1.0.0
			 *
			 * @param array $data
			 * @param \WC_Memberships_Membership_Plan $membership_plan
			 */
			$temp_user_data = (array) apply_filters( 'wc_memberships_temporary_user_data', [
				'user_login'  => $username,
				'user_pass'   => uniqid( 'wcm_', false ),
				'user_email'  => $username . '@example.com',
				/* translators: Placeholder: %s - Membership Plan name */
				'first_name'  => sprintf( __( '"%s" Plan', 'woocommerce-memberships' ), $membership_plan->get_name() ),
				'last_name'   => __( 'Test User', 'woocommerce-memberships' ),
				/* translators: Placeholder: %s - Membership Plan name */
				'description' => sprintf( __( 'A temporary user created for testing the "%s" membership plan. If you don\'t use it, feel free to delete this user.', 'woocommerce-memberships' ), $membership_plan->get_name() ),
				'role'        => 'customer',
			], $membership_plan );

			$user_id = wp_insert_user( $temp_user_data );

			if ( $user_id instanceof \WP_Error ) {
				throw new Framework\SV_WC_Plugin_Exception( $user_id->get_error_message() );
			}

			$current_user_id = get_current_user_id();

			/**
			 * Filters the transient length set when switching users.
			 *
			 * @since 1.19.2
			 *
			 * @param int $transient_length timestamp, default one week in seconds
			 * @param int $current_user_id current user ID switching from
			 * @param int $user_id user ID switching to
			 */
			$transient_length = (int) apply_filters( 'wc_memberships_user_switching_transient_length', WEEK_IN_SECONDS, $current_user_id, $user_id );

			// set a temporary value in DB indicating which temporary user has been created for the current user
			set_transient( $this->get_transient_key( $current_user_id ), $user_id, $transient_length );

			$user = get_user_by( 'id', $user_id );
		}

		if ( ! $user instanceof \WP_User ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'A temporary plan member could not be determined.', 'woocommerce-memberships' ) );
		}

		// adds a flag so that the WordPress bar will be displayed for the user in front end
		update_user_meta( $user->ID, $this->temporary_user_meta_key, 1 );

		return $user;
	}


	/**
	 * Customizes membership plan row actions.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions array of post actions
	 * @param \WP_Post $post post object
	 * @return array
	 */
	public function customize_membership_plan_row_actions( $actions, \WP_Post $post ) {
		global $typenow;

		// add view as member action
		if ( 'wc_membership_plan' === $typenow ) {

			$action_url = wp_nonce_url(
				admin_url( "edit.php?post_type=wc_membership_plan&action={$this->view_as_member_action_name}&post={$post->ID}" ),
				$this->get_view_as_member_of_plan_action_name( $post->ID )
			);

			ob_start();

			?>
			<a href="<?php echo esc_url( $action_url ); ?>" title="<?php esc_attr_e( 'View site as a member of this plan', 'woocommerce-memberships' ); ?>" rel="permalink">
				<?php esc_html_e( 'View site as member', 'woocommerce-memberships' ); ?>
			</a>
			<?php

			$actions[ $this->view_as_member_action_name ] = ob_get_clean();
		}

		return $actions;
	}


	/**
	 * Handles viewing the site as a member of a particular plan.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function view_as_member() {

		// get the plan post ID
		$membership_plan_id = absint( Framework\SV_WC_Helper::get_requested_value( 'post', 0 ) );

		check_admin_referer( $this->get_view_as_member_of_plan_action_name( $membership_plan_id ) );

		$membership_plan = $membership_plan_id > 0 ? wc_memberships_get_membership_plan( $membership_plan_id ) : null;

		try {

			if ( ! $membership_plan ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'The plan to view as a member of could not be determined.', 'woocommerce-memberships' ) );
			}

			$user            = $this->get_temporary_plan_member( $membership_plan );
			$user_membership = wc_memberships_get_user_membership( $user, $membership_plan );

			if ( ! $user_membership ) {

				$user_membership_id = wp_insert_post( [
					'post_type'   => 'wc_user_membership',
					'post_parent' => $membership_plan->get_id(),
					'post_author' => $user->ID,
					'post_status' => 'wcm-active'
				] );

				$user_membership = wc_memberships_get_user_membership( $user_membership_id );
			}

			// ensure the membership is active and started
			$user_membership->set_start_date( current_time( 'mysql', true ) );

			if ( 'active' !== $user_membership->get_status() ) {
				$user_membership->activate_membership();
			}

			// finally switch to that user and redirect to front end
			wp_redirect( add_query_arg( [
				'action'   => 'switch_to_user',
				'user_id'  => $user->ID,
				'_wpnonce' => wp_create_nonce( "switch_to_user_{$user->ID}" ),
			], wp_login_url() ) );
			exit;

		} catch ( Framework\SV_WC_Plugin_Exception $e ) {

			wp_die( $e->getMessage() );
		}
	}


	/**
	 * Removes the temporary user.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param null|int $user_id the user ID (defaults to the current user ID, when logged in)
	 */
	public function remove_temp_user( $user_id = null ) {

		$user_id = $user_id ?: get_current_user_id();

		if ( ! $user_id || ( isset( $_REQUEST['action'] ) && 'switch_to_user' === $_REQUEST['action'] ) ) {
			return;
		}

		$transient_key = $this->get_transient_key( $user_id );
		$temp_user_id  = get_transient( $transient_key );
		$temp_user     = $temp_user_id > 0 ? get_user_by( 'id', $temp_user_id ) : null;

		// remove old, temporary user (sanity check: verifies that the user email is a dummy one, ensuring that the user hasn't been reassigned to an actual one)
		if ( $temp_user && Framework\SV_WC_Helper::str_ends_with( (string) $temp_user->user_email, '@example.com' ) ) {

			if ( ! function_exists( 'wp_delete_user' ) ) {
				include ABSPATH . 'wp-admin/includes/user.php';
			}

			wp_delete_user( $temp_user_id );

			delete_transient( $transient_key );
		}
	}


}

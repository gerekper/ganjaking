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

use SkyVerge\WooCommerce\Memberships\Helpers\Strings_Helper;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Users admin handler.
 *
 * This class handles all the admin-related functionality in users edit screens.
 * Note: it's not necessary to check for the post type, or `$typenow` in this class, as this is already handled in `WC_Memberships_Admin::init()`.
 *
 * @since 1.7.4
 */
class WC_Memberships_Admin_Users {


	/**
	 * Handler constructor.
	 *
	 * @since 1.7.4
	 */
	public function __construct() {

		// show user memberships in Users list screen
		add_filter( 'manage_users_columns',       array( $this, 'add_user_columns' ), 11 );
		add_filter( 'manage_users_custom_column', array( $this, 'user_column_values' ), 11, 3 );

		// list user memberships in individual WordPress User Profile page
		add_action( 'show_user_profile', array( $this, 'show_user_memberships' ) );
		add_action( 'edit_user_profile', array( $this, 'show_user_memberships' ) );
	}


	/**
	 * Adds the "Memberships" column to the User's admin table.
	 *
	 * @internal
	 *
	 * @since 1.7.4
	 *
	 * @param array $columns the array of Users columns
	 * @return array $columns the updated column layout
	 */
	public function add_user_columns( $columns ) {

		if ( current_user_can( 'manage_woocommerce' ) ) {

			// move Memberships before Orders for aesthetics
			$last_column = array_slice( $columns, -1, 1, true );

			array_pop( $columns );

			$columns['wc_memberships_user_memberships'] = __( 'Active Memberships', 'woocommerce-memberships' );

			$columns += $last_column;
		}

		return $columns;
	}


	/**
	 * Displays membership plan name(s) if a given user has a membership for the plan.
	 *
	 * @internal
	 *
	 * @since 1.7.4
	 *
	 * @param string $output the string to output in the column specified with $column_name
	 * @param string $column_name the string key for the current column in an admin table
	 * @param int $user_id the ID of the user to which this row relates
	 * @return string $output links to active user memberships
	 */
	public function user_column_values( $output, $column_name, $user_id ) {

		if ( 'wc_memberships_user_memberships' === $column_name ) {

			// get all active memberships
			$memberships = wc_memberships()->get_user_memberships_instance()->get_user_memberships( $user_id );

			if ( ! empty( $memberships ) ) {

				$membership_links = array();

				foreach ( $memberships as $membership ) {

					$plan = $membership->get_plan();

					// get a link to the membership only if currently active
					if ( $plan && wc_memberships_is_user_active_member( $user_id, $plan ) ) {
						$membership_links[] = '<a href="' . esc_url( get_edit_post_link( $membership->get_id() ) ) . '">' . esc_html( $plan->name ) . '</a>';
					}
				}
			}

			$output = ! empty( $membership_links ) ? implode( '<br />', $membership_links ) : '&mdash;';
		}

		return $output;
	}


	/**
	 * Shows user memberships on user profile page.
	 *
	 * @internal
	 *
	 * @since 1.7.4
	 *
	 * @param \WP_User $user the user object
	 */
	public function show_user_memberships( \WP_User $user ) {

		$user_memberships          = wc_memberships()->get_user_memberships_instance()->get_user_memberships( $user->ID );
		$can_edit_user_memberships = current_user_can( 'manage_woocommerce' );

		?>
		<div class="wc-memberships user-memberships" style="padding-bottom: 15px;">
			<h3><?php esc_html_e( 'Memberships', 'woocommerce-memberships' ); ?></h3>
			<p>
				<?php if ( ! empty( $user_memberships ) ) : $plan_links = array(); ?>

					<?php foreach ( $user_memberships as $user_membership ) : ?>

						<?php

						if ( $user_membership->get_plan() ) :

							$statuses = wc_memberships_get_user_membership_statuses();
							$status   = 'wcm-' . $user_membership->get_status();
							$status   = isset( $statuses[ $status ]['label'] ) ? '(' . esc_html( $statuses[ $status ]['label'] ) . ')' : '';

							$plan_label   = is_rtl() ? wp_kses_post( $status . ' <strong>' . $user_membership->get_plan()->get_name() . '</strong>' ) : '<strong>' . $user_membership->get_plan()->get_name() . '</strong> ' . $status;
							$plan_links[] = true === $can_edit_user_memberships ? '<a href="' . esc_url( get_edit_post_link( $user_membership->get_id() ) ) . '">' . $plan_label . '</a>' : $plan_label;

						endif;

						?>

					<?php endforeach; ?>

					<?php if ( ! empty( $plan_links ) ) : ?>

						<?php printf(
							/* translators: Placeholder: %s - Membership Plan(s) */
							__( 'This user is a member of %s.', 'woocommerce-memberships' ),
							Strings_Helper::get_human_readable_items_list( $plan_links, 'and' )
						); ?>
						<?php if ( $can_edit_user_memberships ) : ?>
							<br><br><a class="button" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=wc_user_membership&user=' . $user->ID ) ); ?>"><?php esc_html_e( 'Add another membership.', 'woocommerce-memberships' ); ?></a>
						<?php endif; ?>

					<?php else : ?>

						<?php esc_html_e( 'This user is already a member of every plan.', 'woocommerce-memberships' ); ?>

					<?php endif; ?>

				<?php else : ?>

					<?php esc_html_e( 'This user has no memberships yet.', 'woocommerce-memberships' ); ?>
					<?php if ( $can_edit_user_memberships ) : ?>
						<br><br><a class="button" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=wc_user_membership&user=' . $user->ID ) ); ?>"><?php esc_html_e( 'Add a membership manually.', 'woocommerce-memberships' ); ?></a>
					<?php endif; ?>

				<?php endif; ?>

			</p>
		</div>
		<?php
	}


}

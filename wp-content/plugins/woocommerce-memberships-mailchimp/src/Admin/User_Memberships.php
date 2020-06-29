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

namespace SkyVerge\WooCommerce\Memberships\MailChimp\Admin;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Memberships\MailChimp\MailChimp_Lists;

defined( 'ABSPATH' ) or exit;

/**
 * User Memberships admin handler class.
 *
 * @since 1.0.0
 */
class User_Memberships {


	/**
	 * Hook into User Memberships edit screens.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// append some buttons to the member details box
		add_action( 'wc_memberships_after_user_membership_member_details', array( $this, 'add_view_member_in_mailchimp_button' ), 10, 2 );

		// add user memberships edit screen rows & bulk sync actions
		add_action( 'post_row_actions',      array( $this, 'add_edit_screen_rows_sync_action' ), 1, 2 );
		add_action( 'admin_footer-edit.php', array( $this, 'add_edit_screen_bulk_sync_action' ) );
		// process bulk sync action
		add_action( 'load-edit.php',         array( $this, 'process_bulk_sync_action' ) );
	}


	/**
	 * Prepares a URL for bulk processing multiple membership to MailChimp.
	 *
	 * @since 1.0.0
	 *
	 * @param int[] $user_membership_ids array of user membership IDs
	 * @return string URL
	 */
	private function get_bulk_sync_url( array $user_membership_ids ) {

		$query_arg = array( 'mailchimp-sync' => array_filter( array_map( 'absint', $user_membership_ids ) ) );

		return add_query_arg( $query_arg, admin_url( 'edit.php?post_type=wc_user_membership' ) );
	}


	/**
	 * Adds a row action on the user memberships edit screen to sync a membership to MailChimp.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions associative array of action
	 * @param \WP_Post $post the corresponding user membership post object
	 * @return array
	 */
	public function add_edit_screen_rows_sync_action( $actions, \WP_Post $post ) {

		if (    current_user_can( 'manage_woocommerce_user_memberships' )
		     && wc_memberships_mailchimp()->can_sync_user( $post->post_author ) ) {

			$actions['mailchimp-sync'] = '<a href="' . esc_url( $this->get_bulk_sync_url( array( $post->ID ) ) ) . '" class="mailchimp-sync" data-user-membership-id="' . esc_attr( $post->ID ) . '">' . esc_html__( 'Sync to MailChimp', 'woocommerce-memberships-mailchimp' ) . '</a>';
		}

		return $actions;
	}


	/**
	 * Adds bulk actions on the user memberships edit screen to sync multiple memberships to MailChimp.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_edit_screen_bulk_sync_action() {
		global $post_type;

		if ( $post_type === 'wc_user_membership' && current_user_can( 'manage_woocommerce_user_memberships' ) ) :

			?>
			<script type="text/javascript">
				jQuery( document ).ready( function() {

					jQuery( '<option>' ).val( 'mailchimp-sync' ).text( '<?php esc_html_e( 'Sync to MailChimp', 'woocommerce-memberships-mailchimp' ); ?>').appendTo( "select[name='action']"  );
					jQuery( '<option>' ).val( 'mailchimp-sync' ).text( '<?php esc_html_e( 'Sync to MailChimp', 'woocommerce-memberships-mailchimp' ); ?>').appendTo( "select[name='action2']" );

					jQuery( 'input.action' ).on( 'click', function( e ) {

						var optVal = jQuery( this ).parent().find( 'select' ).val();
						var ids    = [];

						if ( 'mailchimp-sync' === optVal ) {

							jQuery( 'th.check-column' ).each( function () {

								var $cb = jQuery( this ).find( 'input' );

								if ( $cb && $cb.is( ':checked' ) ) {
									ids.push( $cb.val() )
								}
							} );

							if ( 0 <= ids.length ) {
								window.location.href = window.location.href.split( '?' )[0] + '?post_type=wc_user_membership&mailchimp-sync=' + ids.join();
                            }
						}
					} );
				} );
			</script>
			<?php

		endif;
	}


	/**
	 * Processes bulk user memberships sync to MailChimp.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function process_bulk_sync_action() {

		$screen = get_current_screen();

		if ( $screen && 'edit-wc_user_membership' === $screen->id && isset( $_REQUEST['mailchimp-sync'] ) ) {

			$sync_request = $_REQUEST['mailchimp-sync'];

			if ( ! empty( $sync_request ) ) {

				$success             = 0;
				$failure             = 0;
				$failed              = array();
				$not_found           = array();
				$handler             = wc_memberships_mailchimp()->get_user_memberships_instance();
				$user_membership_ids = array_filter( array_map( 'absint', is_array( $sync_request ) ? $sync_request : explode( ',', (string) $sync_request ) ) );

				if ( $handler && ! empty( $user_membership_ids ) ) {

					foreach ( $user_membership_ids as $user_membership_id ) {

						if ( $user_membership = wc_memberships_get_user_membership( $user_membership_id ) ) {

							$user = $user_membership->get_user();

							if ( $handler->sync_member( $user ) ) {
								$success++;
							} else {
								$failed[ $user_membership_id ] = $user;
								$failure++;
							}

						} else {

							$not_found[] = $user_membership_id;
							$failure++;
						}
					}
				}

				$processed = $failure + $success;
				$message   = sprintf( _n( 'Processed %d user membership to sync to MailChimp.', 'Processed %d user memberships to sync to MailChimp.', $processed, 'woocommerce-memberships-mailchimp' ), $processed );

				if ( $success > 0 ) {
					$message .= '<br /> - ' . sprintf( _n( '%d member successfully synced.', '%d members successfully synced.', $success, 'woocommerce-memberships-mailchimp' ), $success );
				}

				if ( $failure > 0 ) {

					$conjunction   = __( 'and', 'woocommerce-memberships-mailchimp' );
					$default_error = __( 'an error occurred', 'woocommerce-memberships-mailchimp' );

					if ( ! empty( $failed ) ) {

						$links = array();

						foreach ( $failed as $user_membership_id => $user ) {

							if ( $user instanceof \WP_User ) {

								$links[] = '<a href="' . esc_url( get_edit_post_link( $user_membership_id ) ) . '">' . esc_html( $user->display_name ) . '</a>';
							}
						}

						$links = ! empty( $links ) ? wc_memberships_list_items( $links, $conjunction ) : $default_error;

					} elseif ( ! empty( $not_found ) ) {

						$count = count( $not_found );
						$links = sprintf( _n( 'membership %s not found', 'memberships %s not found', $count, 'woocommerce-memberships-mailchimp' ), wc_memberships_list_items( $not_found, $conjunction ) );

					} else {

						$links = $default_error;
					}

					$message .= '<br /> - ' . sprintf( _n( 'Could not sync %d member: %s.', 'Could not sync %d members: %s.', $failure, 'woocommerce-memberships-mailchimp' ), $failure, $links );
				}

				wc_memberships_mailchimp()->get_message_handler()->add_message( $message );
			}
		}
	}


	/**
	 * Adds a button to view the current member in MailChimp.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id the member user ID
	 * @param int $user_membership_id the membership (post) ID
	 */
	public function add_view_member_in_mailchimp_button( $user_id, $user_membership_id ) {

		// the action hook wouldn't return the correct user membership ID before Memberships v1.9.4
		if ( ! wc_memberships_mailchimp()->is_memberships_version_gte( '1.9.4' ) ) {
			return;
		}

		// do not display if not connect to MailChimp yet
		if ( ! wc_memberships_mailchimp()->is_connected() ) {
			return;
		}

		$user_membership = wc_memberships_get_user_membership( $user_membership_id );

		// do not offer to sync members created in admin which haven't been saved yet
		if ( ! $user_membership || ! $user_membership->post || ! in_array( $user_membership->post->post_status, wc_memberships()->get_user_memberships_instance()->get_user_membership_statuses( false ), true ) ) {
			return;
		}

		if ( $admin = wc_memberships_mailchimp()->get_admin_instance() ) :

			?>
			<ul id="wc-memberships-mailchimp-user-membership-details">

				<?php if ( wc_memberships_mailchimp()->can_sync_user( $user_id ) ) : ?>

					<li>
						<button class="button button-primary button-small tips sync-member" data-tip="<?php esc_attr_e( 'Synchronizes this member with the matched MailChimp list', 'woocommerce-memberships-mailchimp' ); ?>" data-user-membership-id="<?php echo esc_attr( $user_membership_id ); ?>"><?php esc_html_e( 'Sync to MailChimp', 'woocommerce-memberships-mailchimp' ); ?></button>
					</li
					<li style="display: none;">
						<div class="syncing"    style="display:none; color:<?php echo esc_attr( $admin->get_status_color_code( 'default' ) ); ?>;"><?php esc_html_e( 'Syncing...', 'woocommerce-memberships-mailchimp' ); ?></div>
						<div class="sync-done"  style="display:none; color:<?php echo esc_attr( $admin->get_status_color_code( 'success' ) ); ?>;"><?php esc_html_e( 'Done!', 'woocommerce-memberships-mailchimp' ); ?></div>
						<div class="sync-error" style="              color:<?php echo esc_attr( $admin->get_status_color_code( 'failure' ) ); ?>;"></div>
					</li>
					<?php if ( $this->is_user_membership_in_sync( $user_id ) ) : ?>
						<?php $date = $this->get_user_membership_list_subscription_status_local_date( $user_id ); ?>
						<li>
							<div>
								<small><?php
									/* translators: Placeholder: %s - date or string */
									printf( __( 'Last Synced: %s', 'woocommerce-memberships-mailchimp' ), ! empty( $date ) ? $date : '<span style="color: ' . esc_attr( $admin->get_status_color_code( 'failure' ) ) . ';">' . __( 'Not synced yet', 'woocommerce-memberships-mailchimp' ) . '</span>' );
									?></small>
							</div>
						</li>
					<?php endif; ?>

				<?php elseif ( ! wc_memberships_mailchimp()->is_members_opt_in_mode( 'automatic' ) ) : ?>

					<li>
						<div>
							<small>
								<?php

								$opt_in = get_user_meta( $user_id, '_wc_memberships_mailchimp_sync_opt_in', true );

								if ( 'no' === $opt_in ) :
									esc_html_e( 'MailChimp: This member has not given consent to subscribe to the MailChimp audience.', 'woocommerce-memberships-mailchimp' );
								elseif ( empty( $opt_in ) ) :
									esc_html_e( 'MailChimp: This member has not given consent yet to subscribe to the MailChimp audience.', 'woocommerce-memberships-mailchimp' );
								endif;

								?>
							</small>
						</div>
					</li>

				<?php endif; ?>

			</ul>
			<?php

		endif;
	}


	/**
	 * Checks whether an user membership is synced with the current MailChimp audience in use.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id the user ID
	 * @return bool
	 */
	private function is_user_membership_in_sync( $user_id ) {

		$synced = false;

		$data = wc_memberships_mailchimp()->get_user_memberships_instance()->get_member_status( $user_id );

		if ( is_array( $data ) && isset( $data['list_id'] ) ) {
			$synced = $data['list_id'] === MailChimp_Lists::get_current_list_id();
		}

		return $synced;
	}


	/**
	 * Returns the localized date for the last sync timestamp.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id the member ID
	 * @return string
	 */
	private function get_user_membership_list_subscription_status_local_date( $user_id ) {

		$date = '';
		$data = wc_memberships_mailchimp()->get_user_memberships_instance()->get_member_status( $user_id );

		if ( is_array( $data ) && isset( $data['timestamp'] ) &&  $data['timestamp'] > 0 ) {
			$date = get_date_from_gmt( date( 'Y-m-d H:i:s', (int) $data['timestamp'] ), wc_date_format() . ' @ ' . wc_time_format() );
		}

		return $date;
	}


}

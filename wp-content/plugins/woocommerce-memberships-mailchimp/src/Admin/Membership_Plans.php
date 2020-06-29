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
 * Membership Plans admin handler class.
 *
 * @since 1.0.0
 */
class Membership_Plans {


	/**
	 * Hook into Membership Plans edit screens.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// add an input to display or edit the current plan's merge field tag
		add_action( 'wc_membership_plan_options_membership_plan_data_general', array( $this, 'add_merge_field_tag_input' ) );

		// show the merge tag in the plans edit screen
		add_filter( 'manage_edit-wc_membership_plan_columns',        array( $this, 'customize_columns' ), 20 );
		add_action( 'manage_wc_membership_plan_posts_custom_column', array( $this, 'custom_column_content' ), 20 );
	}


	/**
	 * Outputs a merge tag input for the current plan.
	 *
	 * This is the merge tag used for the corresponding merge field ID.
	 * If a merge field ID does not exist yet for this plan, it will be created.
	 *
	 * Note: merge field tags in MailChimp are normally 10 alphanumeric characters maximum.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_merge_field_tag_input() {
		global $post;

		if ( $plan = wc_memberships_get_membership_plan( $post ) ) :

			$plugin = wc_memberships_mailchimp();
			$api    = $plugin->get_api_instance();
			$plans  = $plugin->get_membership_plans_instance();

			if ( $plans && $api && $plugin->is_connected() ) :

				$merge_tag     = '';
				$error_message = '';
				$has_merge_id  = $plans->plan_has_merge_field_id( $plan );

				if ( $has_merge_id ) {

					$merge_tag = $plans->get_plan_merge_field_tag( $plan->get_id() );

					if ( null === $merge_tag ) {
						/* translators: Placeholders: %1$s - opening <a> link tag, %2$s - closing </a> link tag */
						$error_message = sprintf( __( 'A merge field appears to be set for this plan in MailChimp Sync, but the corresponding merge tag could not be found. Perhaps it has been deleted? Please check %1$sMailChimp for WooCommerce Memberships settings%2$s and assign a new merge field to this plan.', 'woocommerce-memberships-mailchimp' ), '<a href="' . esc_url( $plugin->get_settings_url() ) . '">', '</a>' );
					}

				} else {

					$list = MailChimp_Lists::get_list();

					// normally the default tag is obtained from plan slug, but we return an empty one if this somehow exists already in MailChimp
					$merge_tag = empty( $merge_tag ) ? MailChimp_Lists::parse_merge_tag( $plan->get_slug() ) : $merge_tag;
					$merge_tag = $list && $api->merge_field_tag_exists( $list->get_id(), $merge_tag ) ? '' : $merge_tag;
				}

				?>
				<p class="form-field mailchimp-sync-merge-field-tag">
					<label for="_mailchimp_sync_merge_tag"><?php esc_html_e( 'MailChimp Merge Field', 'woocommerce-memberships-mailchimp' ); ?></label>

					<input
							id="_mailchimp_sync_merge_tag"
							name="_mailchimp_sync_merge_tag"
							type="text"
							maxlength="10"
							placeholder="<?php echo $has_merge_id && null === $merge_tag ? __( 'Not found!', 'woocommerce-memberships-mailchimp' ) : ''; ?>"
							value="<?php echo esc_attr( $merge_tag ); ?>"
						<?php disabled( $has_merge_id, true, true ); ?>
					/>

					<?php echo wc_help_tip( __( 'The merge field tag can be used to segment members of this plan in MailChimp.', 'woocommerce-memberships-mailchimp' ) ); ?>

					<?php if ( ! empty( $error_message ) && ( $admin = wc_memberships_mailchimp()->get_admin_instance() ) ) : ?>
						<em style="color:<?php echo esc_attr( $admin->get_status_color_code( 'failure' ) ); ?>;"><?php echo $error_message; ?></em>
					<?php elseif ( ! $has_merge_id ) : ?>
						<em><?php esc_html_e( 'Merge field tags in MailChimp Sync must be unique alphanumeric words of maximum 10 characters, in uppercase and without spaces.', 'woocommerce-memberships-mailchimp' ); ?></em>
					<?php endif; ?>
				</p>
			<?php

			endif;

		endif;
	}


	/**
	 * Adds a new column in the Membership Plans edit screen table.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns
	 * @return array
	 */
	public function customize_columns( $columns ) {

		$mailchimp_sync = array( 'mailchimp_sync' => __( 'Merge Tag', 'woocommerce-memberships-mailchimp' ) );

		// try to insert after slug or after title if unavailable, or just append
		if ( isset( $columns['slug'] ) ) {
			$columns = Framework\SV_WC_Helper::array_insert_after( $columns, 'slug', $mailchimp_sync );
		} elseif ( isset( $columns['title'] ) ) {
			$columns = Framework\SV_WC_Helper::array_insert_after( $columns, 'title', $mailchimp_sync );
		} else {
			$columns = array_merge( $columns, $mailchimp_sync );
		}

		return $columns;
	}


	/**
	 * Outputs the chosen merge tag for Membership Plans in the plans edit screen rows.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $column the current column being displayed
	 */
	public function custom_column_content( $column ) {
		global $post;

		if ( $post && 'mailchimp_sync' === $column ) {

			$membership_plan = wc_memberships_get_membership_plan( $post );

			if ( $membership_plan ) {

				$merge_tag = wc_memberships_mailchimp()->get_membership_plans_instance()->get_plan_merge_field_tag( $membership_plan );

				echo empty( $merge_tag ) || ! is_string( $merge_tag ) ? '&mdash;' : '<code>' . $merge_tag . '</code>';
			}
		}
	}


}

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

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Integration class for Groups plugin.
 *
 * @since 1.0.0
 */
class WC_Memberships_Integration_Groups {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_filter( 'wc_memberships_general_settings', array( $this, 'groups_integration_settings' ) );
		add_action( 'woocommerce_admin_field_wc_memberships_groups_import_button', array( $this, 'admin_wc_memberships_groups_button' ) );

		// register the Import page
		add_action( 'admin_menu', array( $this, 'add_menu_items' ) );

		// customize admin title for the import page
		add_filter( 'admin_title', array( $this, 'admin_title' ) );

		// handle groups import
		add_action( 'init', array( $this, 'import_groups' ), 11 ); // Lower priority, so that CPTs are registered!

		// remove over-zealous groups restriction select box
		add_action( 'admin_init', array( $this, 'remove_groups_access_restriction' ), 11 );
	}


	/**
	 * Adds Groups integration settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings
	 * @return array
	 */
	public function groups_integration_settings( $settings ) {

		$settings[] = array(
			'name' => __( 'Groups Integration', 'woocommerce-memberships' ),
			'type' => 'title',
			'id'   => 'memberships_groups_integration',
		);

		$settings[] = array(
			'id'                => 'import_groups',
			'custom_attributes' => array(
				'href'   => admin_url( 'admin.php?page=wc-memberships-import-groups' ),
			),
			'type'              => 'wc_memberships_groups_import_button',
			'title'             => __( 'Groups Import', 'woocommerce-memberships' ),
			'class'             => 'button import-groups',
			'desc_tip'          => __( 'Click this button to import memberships from WordPress Groups plugin.', 'woocommerce-memberships' ),
			'default'           => __( 'Import Members from Groups', 'woocommerce-memberships' ),
		);

		$settings[] = array(
			'type' => 'sectionend',
			'id'   => 'memberships_groups_integration'
		);

		return $settings;
	}


	/**
	 * Adds Groups Import settings page.
	 *
	 * @since 1.0.0
	 */
	public function add_menu_items() {
		global $_registered_pages;

		// register Import Groups page
		if ( current_user_can( 'manage_woocommerce' ) ) {

			// modifies the `$_registered_pages` global directly
			$hookname = get_plugin_page_hookname( 'wc-memberships-import-groups', null );

			add_action( $hookname, array( $this, 'render_import_page' ) );
			$_registered_pages[ $hookname ] = true;
		}
	}


	/**
	 * Adds Memberships admin menu items
	 *
	 * @since 1.0.0
	 *
	 * @param string $title Admin title
	 * @return string Modified title
	 */
	public function admin_title( $title ) {

		if ( isset( $_GET['page'] ) && 'wc-memberships-import-groups' === $_GET['page'] ) {
			$title = _x( 'Import members from Groups', 'Page title', 'woocommerce-memberships' ) . $title;
		}

		return $title;
	}


	/**
	 * Renders groups import page.
	 *
	 * @since 1.0.0
	 */
	public function render_import_page() {

		// access_types check
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( ! count( $this->get_groups() ) ) {

			esc_html_e( 'There are no groups to import.', 'woocommerce-memberships' );
			return;
		}

		$membership_plans = wc_memberships_get_membership_plans();

		?>
		<form method="post" id="import_groups">
			<?php wp_nonce_field(); ?>
			<div class="wrap">

				<h2><?php esc_html_e( 'Import Members from Groups', 'woocommerce-memberships' ); ?></h2>
				<p><?php esc_html_e( 'For each group, specify which plan should we import members to.', 'woocommerce-memberships' ); ?></p>

				<?php foreach ( $this->get_groups() as $group ) : ?>

					<fieldset>
						<h4><?php printf( /* translators: %s - Group name, %d - Group ID */
								__( 'Group: %s (ID #%d) ', 'woocommerce-memberships' ), $group->name, $group->group_id ); ?></h4>

						<p>
							<legend><?php esc_html_e( 'What should be done with members of this group?', 'woocommerce-memberships' ) ?></legend>
						</p>

						<ul style="list-style:none;">
							<li>
								<label>
									<input
										type="radio"
									    name="import_groups[<?php echo esc_attr( $group->group_id ); ?>][action]"
									    id="import_groups_action_<?php echo esc_attr( $group->group_id ); ?>_0"
									    value="skip"
									    class="js-import-action">
									<?php esc_html_e( 'Skip', 'woocommerce-memberships' ); ?>
								</label>
							</li>

							<li>
								<label>
									<input
										type="radio"
									    name="import_groups[<?php echo esc_attr( $group->group_id ); ?>][action]"
									    id="import_groups_action_<?php echo esc_attr( $group->group_id ); ?>_1"
									    value="import"
									    class="js-import-action"
									/>
									<?php esc_html_e( 'Import all members to:', 'woocommerce-memberships' ); ?>
								</label>

								<select
									name="import_groups[<?php echo esc_attr( $group->group_id ); ?>][plan]"
									id="import_groups_plan_<?php echo esc_attr( $group->group_id ); ?>"
									class="">
									<?php foreach ( $membership_plans as $plan ) : ?>
										<option value="<?php echo esc_attr( $plan->get_id() ); ?>">
											<?php echo esc_html( $plan->get_name() ); ?>
										</option>
									<?php endforeach; ?>
								</select>

							</li>
						</ul>
					</fieldset>

				<?php endforeach; ?>

				<input
					type="hidden"
					name="action"
					value="wc_memberships_import_groups"
				/>

				<p class="submit">

					<input
						type="submit"
						name="submit"
						id="submit"
						class="button"
						value="<?php esc_attr_e( 'Confirm Import', 'woocommerce-memberships' ); ?>"
						disabled=""
					/>

				</p>

			</div>
		</form>
		<?php

		wc_enqueue_js( "
			var groups_count = " . count( $this->get_groups() ) . ";
			jQuery('#import_groups').on( 'change', 'input.js-import-action', function() {
				var checked_count = jQuery( '#import_groups' ).find( 'input.js-import-action:checked' ).length;

				if ( checked_count >= groups_count ) {
					jQuery( '#submit' ).removeAttr( 'disabled' );
				}
			} );
		" );
	}


	/**
	 * Removes groups access restriction field from Memberships screens.
	 *
	 * @since 1.0.0
	 */
	public function remove_groups_access_restriction() {
		global $typenow;

		if ( in_array( $typenow, array( 'wc_user_membership', 'wc_membership_plan' ), true ) ) {
			remove_action( 'restrict_manage_posts', array( 'Groups_Admin_Posts', 'restrict_manage_posts' ) );
		}
	}



	/**
	 * Imports members from groups.
	 *
	 * @since 1.0.0
	 */
	public function import_groups() {

		// bail out if not importing
		if ( ! isset( $_POST['action'] ) || $_POST['action'] !== 'wc_memberships_import_groups' ) {
			return;
		}

		$redirect_to = admin_url( 'edit.php?post_type=wc_user_membership' );

		// bail out if nothing to import
		if ( ! isset( $_POST['import_groups'] ) || empty( $_POST['import_groups'] ) ) {

			wp_redirect( $redirect_to );
			exit;
		}

		$import_count = 0;

		// loop over groups and import members
		foreach ( $_POST['import_groups'] as $group_id => $map ) {

			// skip skipped groups
			if ( 'skip' === $map['action'] ) {
				continue;
			}

			$members = $this->get_group_members( $group_id );

			// skip if no members to import
			if ( empty( $members ) ) {
				continue;
			}

			// loop over each member and import
			foreach ( $members as $user_id ) {
				$result = $this->import_user_membership( $user_id, $group_id, $map['plan'] );

				if ( $result ) {
					$import_count++;
				}
			}
		}

		// add admin message
		if ( $import_count ) {
			$message = sprintf( _n( '%d member imported from Groups.', '%d members imported from Groups', $import_count, 'woocommerce-memberships' ), $import_count );
		} else {
			$message = __( 'No members were imported from Groups.', 'woocommerce-memberships' );
		}

		wc_memberships()->get_admin_instance()->get_message_handler()->add_message( $message );

		// redirect to members screen
		wp_redirect( $redirect_to );
		exit;
	}


	/**
	 * Imports user membership from group membership.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id
	 * @param int $group_id
	 * @param int $plan_id
	 * @return int|bool Imported membership ID or false on skip/failure
	 */
	private function import_user_membership( $user_id, $group_id, $plan_id ) {

		// user is already a member, skip
		if ( wc_memberships_is_user_member( $user_id, $plan_id ) ) {
			return false;
		}

		/**
		 * Filter new membership data, used when importing membership from Groups.
		 *
		 * @since 1.0.0
		 *
		 * @param array $data
		 * @param array $args
		 */
		$data = apply_filters( 'wc_memberships_groups_import_membership_data', array(
			'post_parent'    => $plan_id,
			'post_author'    => $user_id,
			'post_type'      => 'wc_user_membership',
			'post_status'    => 'wcm-active',
			'comment_status' => 'open',
		), array(
			'user_id'  => $user_id,
			'group_id' => $group_id,
		) );

		// create a new membership
		$user_membership_id = wp_insert_post( $data, true );

		// bail out on failure
		if ( is_wp_error( $user_membership_id ) ) {
			return false;
		}

		// save group ID that granted access
		update_post_meta( $user_membership_id, '_group_id', $group_id );

		// save the membership start date
		update_post_meta( $user_membership_id, '_start_date', current_time( 'mysql', true ) );

		// calculate membership end date based on membership length
		$plan     = wc_memberships_get_membership_plan( $plan_id );
		$end_date = '';

		if ( $plan->get_access_length_amount() ) {

			$now = current_time( 'timestamp', true );

			if ( strpos( $plan->get_access_length_period(), 'month' ) !== false ) {
				$end = wc_memberships_add_months_to_timestamp( $now, $plan->get_access_length_amount() );
			} else {
				$end = strtotime( '+ ' . $plan->get_access_length(), $now );
			}

			$end_date = date( 'Y-m-d H:i:s', $end );
		}

		// save/update end date
		$user_membership = wc_memberships_get_user_membership( $user_membership_id );
		$user_membership->set_end_date( $end_date );

		// add membership note
		$group = \Groups_Group::read( $group_id );
		/* translators: %s - Group name, %d Group ID */
		$user_membership->add_note( sprintf( __( 'Membership imported from Group "%s" (ID #%d)' ), $group->name, $group_id ) );

		return $user_membership_id;
	}


	/**
	 * Outputs button type settings field
	 *
	 * @since 1.0.0
	 *
	 * @param array $value
	 */
	public function admin_wc_memberships_groups_button( $value ) {

		// custom attribute handling
		$custom_attributes = array();

		if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
			foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		// description handling
		$field_description = \WC_Admin_Settings::get_field_description( $value );

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
				<?php echo $field_description['tooltip_html']; ?>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
				<a id="<?php echo esc_attr( $value['id'] ); ?>"
				   type="<?php echo esc_attr( $value['type'] ); ?>"
				   style="<?php echo esc_attr( $value['css'] ); ?>"
				   value="<?php echo esc_attr( $value['default'] ); ?>"
				   class="<?php echo esc_attr( $value['class'] ); ?>"
					<?php echo implode( ' ', $custom_attributes ); ?>><?php echo esc_html( $value['default'] ); ?></a>
				<?php echo $field_description['description']; ?>
			</td>
		</tr>
		<?php
	}


	/**
	 * Returns all the groups.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_groups() {
		global $wpdb;

		$group_table = _groups_get_tablename( "group" );
		return (array) $wpdb->get_results( "SELECT group_id, name FROM $group_table ORDER BY group_id" );
	}


	/**
	 * Returns all groups for a specific user.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id
	 * @return array
	 */
	private function get_user_groups( $user_id ) {

		$user = new Groups_User( $user_id );
		return (array) $user->groups;
	}


	/**
	 * Returns all users of a group.
	 *
	 * @since 1.0.0
	 *
	 * @param int $group_id
	 * @return array
	 */
	private function get_group_members( $group_id ) {
		global $wpdb;

		$table = _groups_get_tablename( "user_group" );
		return (array) $wpdb->get_col( $wpdb->prepare( "SELECT user_id FROM $table WHERE group_id=%d", $group_id ) );
	}


}

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
 * Admin settings handler.
 *
 * @since 1.0.0
 */
class Settings {


	/** @var string the Memberships settings tab section ID */
	private $id;

	/** @var string the key of the setting holding audience preferences */
	private $lists_settings_key;

	/** @var array associative array of MailChimp audience IDs and names */
	private $lists = [];

	/** @var array associative array with interest categories preferences per audience ID */
	private $interest_categories = [];

	/** @var array associative array with merge fields preferences per audience ID */
	private $merge_fields = [];


	/**
	 * Extends Memberships Settings for handling MailChimp Sync options.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->id                 = 'mailchimp-sync';
		$this->lists_settings_key = MailChimp_Lists::get_list_settings_option();

		// add a section for MailChimp Sync to Memberships Settings tab and add settings to it
		add_filter( 'woocommerce_get_sections_memberships', array( $this, 'add_mailchimp_sync_section' ), 40 );
		add_filter( 'woocommerce_get_settings_memberships', array( $this, 'add_mailchimp_sync_settings' ), 10, 2 );

		// add a container for placing fields dynamically created from the current MailChimp list audience
		add_action( 'woocommerce_settings_tabs_memberships', array( $this, 'get_list_settings_html' ) );

		// save settings
		add_action( 'woocommerce_settings_save_memberships', array( $this, 'save_list_settings' ) );
	}


	/**
	 * Performs some tasks upon settings initialization.
	 *
	 * @see Settings::add_mailchimp_sync_settings()
	 *
	 * @since 1.0.0
	 */
	private function init() {

		$plugin = wc_memberships_mailchimp();
		$api    = $plugin->get_api_instance();

		if ( $api && $api->is_api_key_valid() ) {

			$list = MailChimp_Lists::get_list();

			if ( $list ) {

				if ( ! $list->has_active_status_merge_field() ) {
					// tries to create an "is active" merge field if none is set
					$list->set_default_active_status_merge_field( true );
				}

				// creates merge fields and matching tags for membership plans who have none
				wc_memberships_mailchimp()->get_membership_plans_instance()->create_plans_merge_field_tags( $list );
			}
		}

		$plugin->clear_transients();
	}


	/**
	 * Adds a MailChimp Sync section to Memberships Settings tab.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections Associative array
	 * @return array
	 */
	public function add_mailchimp_sync_section( array $sections ) {

		$sections['mailchimp-sync'] = __( 'MailChimp Sync', 'woocommerce-memberships-mailchimp' );

		return $sections;
	}


	/**
	 * Adds MailChimp Sync Settings when the corresponding section is selected.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Associative array
	 * @param string $section The current settings section
	 * @return array
	 */
	public function add_mailchimp_sync_settings( array $settings, $section ) {

		if ( 'mailchimp-sync' === $section ) {

			$this->init();

			$mailchimp_sync = wc_memberships_mailchimp();
			$admin          = $mailchimp_sync->get_admin_instance();
			$api_key_class  = '';
			$api_key_info   = '&nbsp;';
			$display_lists  = false;

			if ( $admin && $mailchimp_sync->has_api_key() ) {
				if ( $mailchimp_sync->get_api_instance()->is_api_key_valid() ) {
					$display_lists = true;
					$api_key_class = 'valid-key';
					$api_key_info  = '<span class="' . $api_key_class. '" style="color:' . esc_attr( $admin->get_status_color_code( 'success' ) ) . ';">&#10004;</span>';
				} else {
					$api_key_class = 'invalid-key';
					$api_key_info  = '<span class="' . $api_key_class. '" style="color:' . esc_attr( $admin->get_status_color_code( 'failure' ) ) . ';">&#10005;</span>';
				}
			}

			$settings = array(

				'header'    => array(
					'title'    => __( 'MailChimp Sync', 'woocommerce-memberships-mailchimp' ),
					'type'     => 'title',
				),

				'api_key'   => array(
					'id'       => 'wc_memberships_mailchimp_sync_mailchimp_api_key',
					'title'    => __( 'MailChimp API Key', 'woocommerce-memberships-mailchimp' ),
					'desc_tip' => __( 'Enter your MailChimp API Key.', 'woocommerce-memberships-mailchimp' ),
					'type'     => 'text',
					'class'    => $api_key_class,
					'desc'     => $api_key_info,
				),

				'debug_mode' => array(
					'id'       => 'wc_memberships_mailchimp_sync_enable_debug_mode',
					'title'    => __( 'Enable Debug Mode', 'woocommerce-memberships-mailchimp' ),
					'type'     => 'checkbox',
					'default'  => 'no',
					'desc'     => __( 'Enable this to log communication with MailChimp. As a best practice, only enable this if prompted by support.', 'woocommerce-memberships-mailchimp' ),
				),

				'footer'     => array(
					'type'     => 'sectionend',
				),

			);

			if ( $display_lists ) {

				$lists = $this->get_lists();

				if ( ! empty( $lists ) ) {

					$members_opt_in_settings = wc_memberships_mailchimp()->is_memberships_version_gte( '1.10.3' ) ? array(

						array(
							'id'       => 'wc_memberships_mailchimp_sync_members_opt_in',
							'title'    => __( 'Members Sign Up', 'woocommerce-memberships-mailchimp' ),
							'desc'     => sprintf(
										/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
								'<em>' . esc_html__( '%1$sSee the plugin documentation%2$s for more details on whether opt in is required.', 'woocommerce-memberships-mailchimp' ) . '</em><br>',
								'<a href="' . wc_memberships_mailchimp()->get_documentation_url() . '#optin" target="_blank">', '</a>'
							 ),
							'desc_tip' => __( 'Choose whether to sign members up automatically or have them opt in manually.', 'woocommerce-memberships-mailchimp' ),
							'type'     => 'radio',
							'options'  => array(
								'automatic' => __( 'Subscribe members automatically', 'woocommerce-memberships-mailchimp' ),
								'manual'    => __( 'Members must subscribe at checkout or from the Members Area', 'woocommerce-memberships-mailchimp' ),
							),
							'default'  => 'automatic',
						),

						array(
							'id'       => 'wc_memberships_mailchimp_sync_members_opt_in_prompt_text',
							'title'    => __( 'Members Sign Up Prompt Text', 'woocommerce-memberships-mailchimp' ),
							'desc'     => __( 'Text to be displayed to a member when subscribing to the audience. HTML and shortcodes are allowed.', 'woocommerce-memberships-mailchimp' ),
							'desc_tip' => true,
							'type'     => 'textarea',
							'default'  => __( 'You are not subscribed to the members email list. Would you like to subscribe now?', 'woocommerce-memberships-mailchimp' ),
						),

						array(
							'id'       => 'wc_memberships_mailchimp_sync_members_opt_in_button_text',
							'title'    => __( 'Members Sign Up Button Text', 'woocommerce-memberships-mailchimp' ),
							'desc'     => __( 'Text used for the button to subscribe the member to the audience.', 'woocommerce-memberships-mailchimp' ),
							'desc_tip' => true,
							'type'     => 'text',
							'default'  => __( 'Add me!', 'woocommerce-memberships-mailchimp' ),
						),

					) : array();

					$additional_settings = array_merge( $members_opt_in_settings, array(

						array(
							'id'       => 'wc_memberships_mailchimp_sync_default_list',
							'title'    => __( 'Select audience', 'woocommerce-memberships-mailchimp' ),
							'desc'     => __( 'Select the MailChimp audience to use.', 'woocommerce-memberships-mailchimp' ),
							'type'     => 'select',
							'class'    => 'wc-enhanced-select',
							'css'      => 'min-width:300px;',
							'desc_tip' => true,
							'options'  => $lists,
						),

					) );

					$settings = Framework\SV_WC_Helper::array_insert_after( $settings, 'debug_mode', $additional_settings );
				}
			}

			/**
			 * Filters the MailChimp Sync settings.
			 *
			 * @since 1.0.0
			 *
			 * @param array $settings
			 */
			$settings = (array) apply_filters( 'wc_memberships_mailchimp_settings', $settings );
		}

		return $settings;
	}


	/**
	 * Returns a list of options in array form to populate the MailChimp audience lists field.
	 *
	 * @since 1.0.0
	 *
	 * @return array Associative array
	 */
	private function get_lists() {

		if ( wc_memberships_mailchimp()->is_connected( false ) ) {

			$options = array();
			$lists   = MailChimp_Lists::get_lists();

			if ( ! empty( $lists ) ) {
				foreach ( $lists as $list ) {
					$options[ $list->get_id() ] = $list->get_name();
				}
			}

			$this->lists = $options;

		} else {

			$this->lists = array();
		}

		return $this->lists;
	}


	/**
	 * Returns settings for the audience interest groups.
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id the audience ID
	 * @return array
	 */
	private function get_interests( $list_id ) {

		if ( empty( $this->interest_categories[ $list_id ] ) ) {

			$list = MailChimp_Lists::get_list( $list_id );

			$this->interest_categories[ $list_id ] = $list ? $list->get_interests() : array();
		}

		return isset( $this->interest_categories[ $list_id ] ) ? $this->interest_categories[ $list_id ] : array();
    }


	/**
	 * Returns options for the audience merge fields.
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id the audience ID
	 * @return array
	 */
	private function get_merge_fields( $list_id ) {

		if ( empty( $this->merge_fields[ $list_id ] ) ) {

			$list = MailChimp_Lists::get_list( $list_id );

			$this->merge_fields[ $list_id ] = $list ? $list->get_merge_fields() : array();
		}

		return isset( $this->merge_fields[ $list_id ] ) ? $this->merge_fields[ $list_id ] : array();
	}


	/**
	 * Adds settings for the selected audience.
	 *
	 * @since 1.0.0
	 *
	 * @param null|string $list_id the ID of the audience to parse settings for, defaults to current
	 */
	public function get_list_settings_html( $list_id = null ) {

		if ( ! is_ajax() && ! wc_memberships_mailchimp()->is_plugin_settings() ) {
			return;
		}

		$lists = $this->get_lists();

		if ( ! empty( $lists ) ) :

			if ( null === $list_id || 'woocommerce_settings_tabs_memberships' === current_action() ) {
				$list_id = MailChimp_Lists::get_current_list_id();
			}

			?>
			<table
					id="wc-memberships-mailchimp-sync-list-settings"
					class="form-table list-id<?php echo esc_attr( $list_id ); ?>">

				<tbody>

					<?php $interests = $this->get_interests( $list_id ); ?>

					<?php if ( ! empty( $interests ) ) : ?>

						<tr valign="top">
							<th scope="row" class="titledesc"><?php esc_html_e( 'Interest Categories', 'woocommerce-memberships-mailchimp' ); ?></th>
							<td class="forminp">

								<p><em><?php esc_html_e( 'Add all members to the following interest groups:', 'woocommerce-memberships-mailchimp' ); ?></em></p>

								<br>

								<div class="list-field">
									<?php foreach ( $interests as $interest_category ) : ?>

										<fieldset class="interests">
											<legend><strong><?php echo esc_html( $interest_category['name'] ); ?></strong></legend>
											<ul>
												<?php foreach ( (array) $interest_category['interests'] as $interest ) : ?>

													<li>
														<label>
															<input
																type="checkbox"
																name="<?php echo esc_attr( $this->lists_settings_key ); ?>[<?php echo esc_attr( $list_id ); ?>][interests][<?php echo esc_attr( $interest_category['id'] ); ?>][]"
																value="<?php echo esc_attr( $interest['id'] ); ?>"
																<?php checked( $interest['chosen'], true, true ); ?>
															/>
															<?php echo esc_html( $interest['name'] ); ?>
														</label>
													</li>

												<?php endforeach; ?>
											</ul>
										</fieldset>

									<?php endforeach; ?>
								</div>
							</td>
						</tr>

					<?php endif; ?>

					<tr valign="top">
						<th scope="row" class="titledesc"><?php esc_html_e( 'Merge Fields', 'woocommerce-memberships-mailchimp' ); ?></th>
						<td class="forminp">

							<p><em><?php esc_html_e( 'Merge tags will help you segment your members across your audience.', 'woocommerce-memberships-mailchimp' ); ?></em></p>

							<table class="list-field merge-fields">

								<thead>
									<tr>
										<td>
											<strong><?php esc_html_e( 'Plan or Status', 'woocommerce-memberships-mailchimp' ); ?></strong>
											<?php echo wc_help_tip( __( 'The membership entity to associate a merge field to', 'woocommerce-memberships-mailchimp' ) ); ?>
										</td>
										<td>
											<strong><?php esc_html_e( 'Merge Tag', 'woocommerce-memberships-mailchimp' ); ?></strong>
											<?php echo wc_help_tip( __( 'Associate a merge field from MailChimp to the corresponding plan', 'woocommerce-memberships-mailchimp' ) ); ?>
										</td>
									</tr>
								</thead>

								<tbody>
									<?php foreach ( $this->get_merge_fields( $list_id ) as $group_id => $group ) : ?>
										<?php foreach ( (array) $group as $item ) : ?>

											<tr>
												<td>
													<?php if ( 'plans' === $group_id && is_numeric( $item['id'] ) ) : ?>
														<a href="<?php echo esc_url( get_edit_post_link( $item['id'] ) ); ?>"><?php echo esc_html( $item['name'] ); ?></a>
													<?php else : ?>
														<?php echo esc_html( $item['name'] ); ?>
													<?php endif; ?>
												</td>
												<td>
													<select
															name="<?php echo esc_attr( $this->lists_settings_key ); ?>[<?php echo esc_attr( $list_id ); ?>][merge_fields][<?php echo esc_attr( $group_id ); ?>][<?php echo esc_attr( $item['id'] ); ?>]"
															class="wc-enhanced-select">
														<option value="">&nbsp;</option>
														<?php foreach ( (array) $item['merge_fields'] as $merge_field ) : ?>
															<option value="<?php echo esc_attr( $merge_field['id'] ); ?>" <?php selected( $merge_field['chosen'], true, true ); ?>><?php echo esc_html( $merge_field['tag'] ); ?></option>
														<?php endforeach; ?>
													</select>
												</td>
											</tr>

										<?php endforeach; ?>
									<?php endforeach; ?>
								</tbody>

								<tfoot>
									<tr>
										<td colspan="2">
											<em><?php
												/* translators: Placeholders: %1$s - opening <a> link tag, %2$s - closing </a> link tag */
												printf( __( 'Once merge fields have been assigned to membership plans and members started syncing, changing or deleting fields may result in unintended consequences. %1$sRead the documentation%2$s for more information.', 'woocommerce-memberships-mailchimp' ), '<a href="' . esc_url( wc_memberships_mailchimp()->get_documentation_url() ). '">', '</a>' ); ?></em>
										</td>
									</tr>
								</tfoot>

							</table>
						</td>
					</tr>

					<?php if ( $list_id && ( $list = wc_memberships_mailchimp_get_list( $list_id ) ) ) : ?>

						<tr valign="top">
							<th scope="row" class="titledesc"><?php esc_html_e( 'Deleted Memberships', 'woocommerce-memberships-mailchimp' ); ?></th>
							<td class="forminp">
								<fieldset>
									<legend style="margin-bottom: 10px; padding-top: 6px;">
										<em><?php esc_html_e( 'When a User Membership is deleted, you can choose to keep, unsubscribe or remove the corresponding member from the list.', 'woocommerce-memberships-mailchimp' ); ?></em>
										<?php echo wc_help_tip( __( 'If a user is still member of other plans, they will not be deleted and their status will depend on the status of the remaining user memberships. If you choose to keep a member, their merge fields will be cleared.', 'woocommerce-memberships-mailchimp' ) ); ?>
									</legend>
									<label>
										<input
											type="radio"
											name="<?php echo esc_attr( $this->lists_settings_key ); ?>[<?php echo esc_attr( $list_id ); ?>][deleted_memberships]"
											value="unsubscribe"
											<?php checked( $list->is_deleted_memberships_handling( 'unsubscribe' ), true, true ); ?>
										/>
										<?php esc_html_e( 'Unsubscribe', 'woocommerce-memberships-mailchimp' ); ?>
									</label>
									&nbsp;&nbsp;
									<label>
										<input
											type="radio"
											name="<?php echo esc_attr( $this->lists_settings_key ); ?>[<?php echo esc_attr( $list_id ); ?>][deleted_memberships]"
											value="remove"
											<?php checked( $list->is_deleted_memberships_handling( 'remove' ), true, true ); ?>
										/>
										<?php esc_html_e( 'Remove', 'woocommerce-memberships-mailchimp' ); ?>
									</label>
									&nbsp;&nbsp;
									<label>
										<input
											type="radio"
											name="<?php echo esc_attr( $this->lists_settings_key ); ?>[<?php echo esc_attr( $list_id ); ?>][deleted_memberships]"
											value="keep"
											<?php checked( $list->is_deleted_memberships_handling( 'keep' ), true, true ); ?>
										/>
										<?php esc_html_e( 'Keep', 'woocommerce-memberships-mailchimp' ); ?>
									</label>
								</fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row" class="titledesc"><?php esc_html_e( 'Deleted Plans Merge Fields', 'woocommerce-memberships-mailchimp' ); ?></th>
							<td class="forminp">
								<fieldset>
									<legend style="margin-bottom: 10px; padding-top: 6px;">
										<em><?php esc_html_e( 'When a Membership Plan is deleted, you can choose to delete its matched merge field from MailChimp or keep it.', 'woocommerce-memberships-mailchimp' ); ?></em>
										<?php echo wc_help_tip( __( 'Note that this may delete also a merge field created in MailChimp or used elsewhere, if currently linked to that plan.', 'woocommerce-memberships-mailchimp' ) ); ?>
									</legend>
									<label>
										<input
											type="radio"
											name="<?php echo esc_attr( $this->lists_settings_key ); ?>[<?php echo esc_attr( $list_id ); ?>][deleted_plan_merge_field]"
											value="keep"
											<?php checked( $list->is_deleted_plan_merge_field_handling( 'keep' ), true, true ); ?>
										/>
										<?php esc_html_e( 'Keep', 'woocommerce-memberships-mailchimp' ); ?>
									</label>
									&nbsp;&nbsp;
									<label>
										<input
											type="radio"
											name="<?php echo esc_attr( $this->lists_settings_key ); ?>[<?php echo esc_attr( $list_id ); ?>][deleted_plan_merge_field]"
											value="delete"
											<?php checked( $list->is_deleted_plan_merge_field_handling( 'delete' ), true, true ); ?>
										/>
										<?php esc_html_e( 'Delete', 'woocommerce-memberships-mailchimp' ); ?>
									</label>
								</fieldset>
							</td>
						</tr>

						<?php $sync_in_progress = (bool) wc_memberships_mailchimp()->get_background_sync_instance()->get_job(); ?>

						<tr valign="top">
							<th scope="row" class="titledesc"><?php esc_html_e( 'Sync Members', 'woocommerce-memberships-mailchimp' ); ?></th>
							<td class="forminp">
								<button
									id="wc-memberships-mailchimp-sync-sync-members"
									class="button button-primary"
									<?php disabled( $sync_in_progress, true, true ); ?>>
									<?php esc_html_e( 'Sync Now', 'woocommerce-memberships-mailchimp' ); ?>
								</button>
								<p style="margin-top: 10px;">
									<?php if ( $sync_in_progress ) : ?>
										<strong><?php echo esc_html__( 'Syncing...', 'woocommerce-memberships-mailchimp' ); ?></strong>
									<?php else : ?>
										<em><?php esc_html_e( "This will sync existing members with your MailChimp list as configured above. This is most useful with initial set up, or if you've updated your settings above to re-sync your list.", 'woocommerce-memberships-mailchimp' ); ?></em>
									<?php endif; ?>
								</p>
							</td>
						</tr>

					<?php endif; ?>

				</tbody>

			</table>
			<?php

		endif;
	}


	/**
	 * Saves the current list preferences.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function save_list_settings() {
		global $current_section;

		if ( $this->id === $current_section ) {

			if ( ! empty( $_POST[ $this->lists_settings_key ] ) && is_array( $_POST[ $this->lists_settings_key ] ) ) {

				$list_id = key( $_POST[ $this->lists_settings_key ] );

				MailChimp_Lists::update_list_settings( $list_id, $_POST[ $this->lists_settings_key ][ $list_id ] );
			}

			wc_memberships_mailchimp()->clear_transients();
		}
    }


}

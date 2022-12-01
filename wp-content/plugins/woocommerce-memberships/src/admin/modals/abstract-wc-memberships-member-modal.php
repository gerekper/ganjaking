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
 * Abstract member modal class.
 *
 * This class acts as a base, abstracted modal view for member actions.
 * Core use examples: adding or transferring a user membership between users.
 *
 * @since 1.9.0
 */
abstract class WC_Memberships_Member_Modal extends \WC_Memberships_Modal {


	/**
	 * Returns the modal main description.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	abstract protected function get_description();


	/**
	 * Returns the modal body template.
	 *
	 * @since 1.9.0
	 *
	 * @return string HTML
	 */
	protected function get_template_body() {

		ob_start();
		?>
		<article>
			<form id="<?php echo esc_attr( $this->get_id() ); ?>">

				<div class="wc-memberships-member-modal-description">

					<?php echo esc_html( $this->get_description() ); ?>

					<select id="wc-memberships-member-modal-user-source" style="width: 100%;">
						<?php foreach ( $this->get_user_source_options() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>

				</div>

				<div id="wc-memberships-modal-select-existing-user">

					<h3 class="wc-memberships-select-user"><?php esc_html_e( 'Select User', 'woocommerce-memberships' ); ?></h3>

					<?php echo $this->get_user_search_field_html(); ?>

				</div>

				<div id="wc-memberships-modal-create-new-user" style="display: none;">

					<h3 class="wc-memberships-select-user"><?php esc_html_e( 'Create User', 'woocommerce-memberships' ); ?></h3>

					<?php echo $this->get_new_user_fields_html(); ?>
				</div>

				<div id="wc-memberships-member-modal-response"></div>

			</form>

		</article>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the options for selecting a user to perform the member action on.
	 *
	 * @since 1.9.0
	 *
	 * @return array associative array of option values and labels
	 */
	protected function get_user_source_options() {

		return array(
			'existing' => __( 'Add an existing user as a member', 'woocommerce-memberships' ),
			'new'      => __( 'Create a new user to add as a member', 'woocommerce-memberships' ),
		);
	}


	/**
	 * Returns the HTML for the user search field.
	 *
	 * @since 1.0.0
	 *
	 * @return string HTML
	 */
	protected function get_user_search_field_html() {

		ob_start();

		?>
		<select
			class="wc-customer-search"
			id="search_member_id"
			placeholder="<?php esc_html_e( 'Search for user', 'woocommerce-memberships' ); ?>"
			data-placeholder="<?php esc_html_e( 'Search for user', 'woocommerce-memberships' ); ?>"
			data-allow_clear="true"
			style="width:100%;">
		</select>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the HTML for fields for adding a new user.
	 *
	 * @since 1.0.0
	 *
	 * @return string HTML
	 */
	protected function get_new_user_fields_html() {

		ob_start();

		?>
		<table style="width: 100%;">
			<tbody>
			<?php \WC_Admin_Settings::output_fields( $this->get_new_user_fields() ); ?>
			</tbody>
		</table>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the form field definitions for new user form in add member modal.
	 *
	 * @since 1.9.0
	 *
	 * @return array[] associative array of field definitions
	 */
	protected function get_new_user_fields() {

		// Note that the fields do not have an id or name attribute and are identified by class names instead.
		// This is on purpose, so that the fields are not posted to the server
		$user_fields = array(
			'wc-memberships-member-modal-user-login' => array(
				'id'                => 'wc-memberships-member-modal-user-login',
				'type'              => 'text',
				'title'             => __( 'Username', 'woocommerce-memberships' ),
				// mimics input field attributes for the user login input found in the WordPress Add User form
				'custom_attributes' => array(
					'autocapitalize' => 'none',
					'autocorrect'    => 'off',
					'maxlength'      => 60,
				),
			),
			'wc-memberships-member-modal-user-email'  => array(
				'id'    => 'wc-memberships-member-modal-user-email',
				'type'  => 'email',
				/* translators: Placeholder: %s - "required" in parenthesis */
				'title' => sprintf( __( 'Email %s', 'woocommerce-memberships' ), __( '(Required)', 'woocommerce-memberships' ) ),
			),
			'wc-memberships-member-modal-password' => array(
				'id'    => 'wc-memberships-member-modal-password',
				'type'  => 'password',
				'title' => __( 'Password', 'woocommerce-memberships' ),
			),
			'wc-memberships-member-modal-user-first-name' => array(
				'id'    => 'wc-memberships-member-modal-user-first-name',
				'type'  => 'text',
				'title' => __( 'First Name', 'woocommerce-memberships' ),
			),
			'wc-memberships-member-modal-user-last-name' => array(
				'id'    => 'wc-memberships-member-modal-user-last-name',
				'type'  => 'text',
				'title' => __( 'Last Name', 'woocommerce-memberships' ),
			),
		);

		// remove password field when password will be automatically generated
		if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) ) {
			unset( $user_fields['wc-memberships-member-modal-password'] );
		}

		// remove username field when it will be automatically generated
		if ( 'yes' === get_option( 'woocommerce_registration_generate_username' ) ) {
			unset( $user_fields['wc-memberships-member-modal-user-login'] );
		}

		/**
		 * Filters the fields for the new user form in member modal.
		 *
		 * @since 1.9.0
		 *
		 * @param array[] $user_fields associative array of field definitions
		 * @param string $id the modal id
		 */
		return apply_filters( 'wc_memberships_member_modal_new_user_fields', $user_fields, $this->get_id() );
	}


}

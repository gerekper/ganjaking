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

namespace SkyVerge\WooCommerce\Memberships\Admin\Views\Meta_Boxes\User_Membership;

use SkyVerge\WooCommerce\Memberships\Profile_Fields as Profile_Fields_Handler;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Meta box for listing and editing profile fields associated to a user membership.
 *
 * @since 1.19.0
 */
class Profile_Fields extends \WC_Memberships_Meta_Box {


	/**
	 * The user membership's profile fields meta box constructor.
	 *
	 * @since 1.19.0
	 */
	public function __construct() {

		$this->id       = 'wc-memberships-user-membership-profile-fields';
		$this->context  = 'side';
		$this->screens  = [ 'wc_user_membership' ];

		parent::__construct();

		// ensure that the WP Media Manager is available on the user membership edit screen
		add_action( 'admin_enqueue_scripts', static function() {
			if ( ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
		} );
	}


	/**
	 * Gets the meta box title.
	 *
	 * @since 1.19.0
	 *
	 * @return string
	 */
	public function get_title() {

		return __( 'Profile Fields', 'woocommerce-memberships' );
	}


	/**
	 * Gets profile fields for the current membership.
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership membership to retrieve fields that apply to
	 * @return Profile_Field[]
	 */
	private function get_profile_fields_for_member( \WC_Memberships_User_Membership $user_membership ) {

		$profile_field_definitions = Profile_Fields_Handler::get_profile_field_definitions( [ 'membership_plan_ids' => [ $user_membership->get_plan_id() ] ] );
		$profile_fields            = [];

		foreach ( $profile_field_definitions as $profile_field_definition ) {

			$profile_field_slug = $profile_field_definition->get_slug();

			if ( $found_profile_field = $user_membership->get_profile_field( $profile_field_slug ) ) {

				$profile_fields[ $profile_field_slug ] = $found_profile_field;

			} else {

				try {

					$profile_field = new Profile_Field();

					$profile_field->set_user_id( $user_membership->get_user_id() );
					$profile_field->set_slug( $profile_field_slug );

					$profile_fields[ $profile_field_slug ] = $profile_field;

				} catch ( \Exception $e ) { continue; }
			}
		}

		return $profile_fields;
	}


	/**
	 * Renders the meta box HTML.
	 *
	 * @since 1.19.0
	 *
	 * @param \WP_Post $post
	 */
	public function output( \WP_Post $post ) {

		if ( null === $this->user_membership && 'wc_user_membership' === get_post_type( $post ) ) {
			$this->user_membership = wc_memberships_get_user_membership( $post );
		}

		$profile_fields = $this->user_membership ? $this->get_profile_fields_for_member( $this->user_membership ) : [];

		/**
		 * Fires before rendering the profile fields meta box on a user membership edit screen.
		 *
		 * @since 1.19.0
		 *
		 * @param \WC_Memberships_User_Membership $user_membership the related user membership object
		 * @param Profile_Field[] $profile_fields array of profile fields associated with the membership
		 * @param Profile_Fields $profile_fields_meta_box the meta box instance
		 */
		do_action( 'wc_memberships_before_user_membership_profile_fields', $this->user_membership, $profile_fields, $this );

		if ( empty( $profile_fields ) ) :

			printf(
				/* translators: Placeholders: %1$s - opening <a> HTML link tag, %2$s - closing </a> HTML link tag */
				'<p>' . esc_html__( 'Collect or store more information about your members with %1$sProfile Fields%2$s.', 'woocommerce-memberships' ) . '</p>',
				'<a href="' . esc_url( admin_url( 'admin.php?page=wc_memberships_profile_fields' ) ) . '">', '</a>'
			);

		else :

			?>
			<a id="edit-profile-fields" href="#">
				<span class="dashicons dashicons-edit"></span> <small><?php esc_html_e( 'Edit Profile Fields', 'woocommerce-memberships' ); ?></small>
			</a>
			<dl>
				<?php

				foreach ( $profile_fields as $profile_field ) :

					$profile_field_definition = $profile_field->get_definition();

					if ( ! $profile_field_definition ) :
						continue;
					endif;

					switch ( $profile_field_definition->get_type() ) :

						case Profile_Fields_Handler::TYPE_CHECKBOX :
						case Profile_Fields_Handler::TYPE_MULTICHECKBOX :
							$this->output_checkboxes_profile_field( $profile_field, $profile_field_definition );
						break;

						case Profile_Fields_Handler::TYPE_RADIO :
							$this->output_radio_profile_field( $profile_field, $profile_field_definition );
						break;

						case Profile_Fields_Handler::TYPE_SELECT :
						case Profile_Fields_Handler::TYPE_MULTISELECT :
							$this->output_select_profile_field( $profile_field, $profile_field_definition );
						break;

						case Profile_Fields_Handler::TYPE_FILE :
							$this->output_file_profile_field( $profile_field, $profile_field_definition );
						break;

						case Profile_Fields_Handler::TYPE_TEXT :
							$this->output_text_profile_field( $profile_field, $profile_field_definition );
						break;

						case Profile_Fields_Handler::TYPE_TEXTAREA :
							$this->output_textarea_profile_field( $profile_field, $profile_field_definition );
						break;

					endswitch;

				endforeach;

				?>
			</dl>
			<button
				id="save-profile-fields"
				class="button button-primary wc-memberships-profile-field-input"
				style="display: none;"><?php esc_html_e( 'Save', 'woocommerce-memberships' ); ?></button>
			<?php

		endif;

		/**
		 * Fires after rendering the profile fields meta box on a user membership edit screen.
		 *
		 * @since 1.19.0
		 *
		 * @param \WC_Memberships_User_Membership $user_membership the related user membership object
		 * @param Profile_Field[] $profile_fields array of profile fields associated with the membership
		 * @param Profile_Fields $profile_fields_meta_box the meta box instance
		 */
		do_action( 'wc_memberships_after_user_membership_profile_fields', $this->user_membership, $profile_fields, $this );
	}


	/**
	 * Outputs single or multiple checkboxes profile field data.
	 *
	 * @since 1.19.0
	 *
	 * @param \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field $profile_field
	 * @param \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition $profile_field_definition
	 */
	private function output_checkboxes_profile_field( Profile_Field $profile_field, Profile_Field_Definition $profile_field_definition ) {

		$type = $profile_field_definition->get_type();

		?>
		<dt><label <?php echo ( Profile_Fields_Handler::TYPE_CHECKBOX === $type ) ? 'for="wc-memberships-profile-field-' . $profile_field_definition->get_slug() . '"' : '' ?>><?php echo esc_html( stripslashes( $profile_field_definition->get_name() ) ); ?></label></dt>
		<dd>
			<span class="wc-memberships-profile-field-value" data-profile-field="<?php echo esc_attr( $profile_field->get_slug() ); ?>">
				<?php echo esc_html( $profile_field->get_formatted_value() ?: '&mdash;' ); ?>
			</span>

			<?php if ( Profile_Fields_Handler::TYPE_CHECKBOX === $type ) : ?>

				<fieldset class="wc-memberships-profile-field-input wc-memberships-profile-field-input-checkbox" style="display: none;">
					<input
						type="hidden"
						name="_profile_fields[<?php echo esc_attr( $profile_field->get_slug() ); ?>]"
						value="0"
						data-slug="<?php echo esc_attr( $profile_field->get_slug() ); ?>"
					/>
					<input
						type="checkbox"
						name="_profile_fields[<?php echo esc_attr( $profile_field->get_slug() ); ?>]"
						id="wc-memberships-profile-field-<?php echo esc_attr( $profile_field->get_slug() ); ?>"
						value="yes"
						data-slug="<?php echo esc_attr( $profile_field->get_slug() ); ?>"
						data-editable-by="<?php echo esc_attr( $profile_field_definition->get_editable_by() ); ?>"
						<?php checked( $profile_field->get_value() )?>
					/>
				</fieldset>

			<?php elseif ( Profile_Fields_Handler::TYPE_MULTICHECKBOX === $type ) : ?>

				<fieldset class="wc-memberships-profile-field-input wc-memberships-profile-field-input-multicheckbox" style="display: none;">
					<input
						type="hidden"
						name="_profile_fields[<?php echo esc_attr( $profile_field->get_slug() ); ?>][]"
						value=""
						data-slug="<?php echo esc_attr( $profile_field->get_slug() ); ?>"
					/>
					<ul>
						<?php foreach ( $profile_field_definition->get_options() as $option ) : ?>

							<?php $option = stripslashes( $option ); ?>
							<li>
								<label>
									<input
										type="checkbox"
										name="_profile_fields[<?php echo $profile_field->get_slug(); ?>][]"
										value="<?php echo esc_attr( $option ); ?>"
										data-slug="<?php echo esc_attr( $profile_field->get_slug() ); ?>"
										data-editable-by="<?php echo esc_attr( $profile_field_definition->get_editable_by() ); ?>"
										<?php checked( in_array( $option, (array) $profile_field->get_value(), false ) ); ?>
									/> <?php echo esc_html( $option ); ?>
								</label>
							</li>

						<?php endforeach; ?>
					</ul>
				</fieldset>

			<?php endif; ?>

		</dd>
		<?php
	}


	/**
	 * Outputs radio profile field data.
	 *
	 * @since 1.19.0
	 *
	 * @param \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field $profile_field
	 * @param \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition $profile_field_definition
	 */
	private function output_radio_profile_field( Profile_Field $profile_field, Profile_Field_Definition $profile_field_definition ) {

		?>
		<dt><label><?php echo esc_html( stripslashes( $profile_field_definition->get_name() ) ); ?></label></dt>
		<dd>
			<span class="wc-memberships-profile-field-value" data-profile-field="<?php echo esc_attr( $profile_field->get_slug() ); ?>">
				<?php echo esc_html( $profile_field->get_formatted_value() ?: '&mdash;' ); ?>
			</span>
			<fieldset class="wc-memberships-profile-field-input wc-memberships-profile-field-input-radio" style="display: none;">
				<ul>
					<?php foreach ( $profile_field_definition->get_options() as $option ) : ?>

						<?php $option = stripslashes( $option ); ?>
						<li>
							<label>
								<input
									type="radio"
									name="_profile_fields[<?php echo $profile_field->get_slug(); ?>]"
									value="<?php echo esc_attr( $option ); ?>"
									data-slug="<?php echo esc_attr( $profile_field->get_slug() ); ?>"
									data-editable-by="<?php echo esc_attr( $profile_field_definition->get_editable_by() ); ?>"
									<?php checked( $option, $profile_field->get_value() ); ?>
								/> <?php echo esc_html( $option ); ?>
							</label>
						</li>

					<?php endforeach; ?>
				</ul>
			</fieldset>
		</dd>
		<?php
	}


	/**
	 * Outputs simple or multiple dropdown profile field data.
	 *
	 * @since 1.19.0
	 *
	 * @param \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field $profile_field
	 * @param \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition $profile_field_definition
	 */
	private function output_select_profile_field( Profile_Field $profile_field, Profile_Field_Definition $profile_field_definition ) {

		$type = $profile_field_definition->get_type();

		?>
		<dt><label for="wc-memberships-profile-field-<?php echo $profile_field_definition->get_slug(); ?>"><?php echo esc_html( stripslashes( $profile_field_definition->get_name() ) ); ?></label></dt>
		<dd>
			<span class="wc-memberships-profile-field-value" data-profile-field="<?php echo esc_attr( $profile_field->get_slug() ); ?>">
				<?php echo esc_html( $profile_field->get_formatted_value() ?: '&mdash;' ); ?>
			</span>
			<div class="wc-memberships-profile-field-input wc-memberships-profile-field-input-<?php echo $type; ?>" style="display: none;">
				<select
					name="_profile_fields[<?php echo esc_attr( $profile_field->get_slug() ); ?>]<?php echo Profile_Fields_Handler::TYPE_MULTISELECT === $type ? '[]' : ''; ?>"
					id="wc-memberships-profile-field-<?php echo esc_attr( $profile_field->get_slug() ); ?>"
					class="wc-enhanced-select"
					style="width: 100%;"
					data-slug="<?php echo esc_attr( $profile_field->get_slug() ); ?>"
					data-editable-by="<?php echo esc_attr( $profile_field_definition->get_editable_by() ); ?>"
					<?php echo Profile_Fields_Handler::TYPE_MULTISELECT === $type ? 'multiple="multiple"' : ''; ?>>
					<?php foreach ( $profile_field_definition->get_options() as $option ) : ?>
						<?php $option = stripslashes( $option ); ?>
						<option value="<?php echo esc_attr( $option ); ?>" <?php selected( in_array( $option, (array) $profile_field->get_value(), false ) ); ?>><?php echo esc_html( $option ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</dd>
		<?php
	}


	/**
	 * Outputs file upload profile field data.
	 *
	 * @since 1.19.0
	 *
	 * @param \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field $profile_field
	 * @param \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition $profile_field_definition
	 */
	private function output_file_profile_field( Profile_Field $profile_field, Profile_Field_Definition $profile_field_definition ) {

		$attachment_id     = $profile_field->get_value();
		$media_library_url = $attachment_id ? get_edit_post_link( $attachment_id ) : '';
		$formatted_value   = '' !== $media_library_url ? sprintf( '<a href="%s">%s</a>', esc_url( $media_library_url ), basename( get_attached_file( $attachment_id ) ) ) : '&mdash;';

		?>
		<dt><label for="wc-memberships-profile-field-<?php echo $profile_field_definition->get_slug(); ?>"><?php echo esc_html( stripslashes( $profile_field_definition->get_name() ) ); ?></label></dt>
		<dd>
			<span class="wc-memberships-profile-field-value" data-profile-field="<?php echo esc_attr( $profile_field->get_slug() ); ?>">
				<?php echo $formatted_value; // expects formatted HTML link to media library item ?>
			</span>
			<div class="wc-memberships-profile-field-input wc-memberships-profile-field-input-file" style="display: none;">
				<input
					type="hidden"
					name="_profile_fields[<?php echo $profile_field->get_slug(); ?>]"
					value="<?php echo esc_attr( $attachment_id ); ?>"
					data-slug="<?php echo esc_attr( $profile_field->get_slug() ); ?>"
					data-field-type="<?php echo esc_attr( Profile_Fields_Handler::TYPE_FILE ); ?>"
					data-editable-by="<?php echo esc_attr( $profile_field_definition->get_editable_by() ); ?>"
				/>
				<input
					type="text"
					id="wc-memberships-profile-field-<?php echo $profile_field->get_slug(); ?>"
					readonly="readonly"
					value="<?php echo esc_attr( $profile_field->get_formatted_value() ); ?>"
				/>
				<a href="#"
				   id="wc-memberships-profile-field-input-file-upload-<?php echo $profile_field->get_slug(); ?>"
				   class="button button-small button-primary wc-memberships-profile-field-input-file-upload"><?php esc_html_e( 'Edit', 'woocommerce-memberships' ); ?></a>
				<a href="#"
				   id="wc-memberships-profile-field-input-file-remove-<?php echo $profile_field->get_slug(); ?>"
				   class="button button-small wc-memberships-profile-field-input-file-remove" <?php echo ! $profile_field->get_value() ? 'style="display: none;"' : ''; ?>><?php echo esc_html_e( 'Remove', 'woocommerce-memberships' ); ?></a>
			</div>
		</dd>
		<?php

		wc_enqueue_js( "

			$( '#wc-memberships-profile-field-input-file-upload-" . esc_js( $profile_field->get_slug() ) . "' ).on( 'click', function( e ){
				e.preventDefault();

				var button   = $( this );
				var uploader = wp.media( { multiple: false } ).on('select', function() {
					var attachment = uploader.state().get( 'selection' ).first().toJSON();
					button.parent( 'div' ).find( 'input[type=text]' ).val( attachment.url );
					button.parent( 'div' ).find( 'input[type=hidden]' ).val( attachment.id );
					button.next().show();
				} ).open();
			} );

			$( '#wc-memberships-profile-field-input-file-remove-" . esc_js( $profile_field->get_slug() ) . "' ).on( 'click', function( e ) {
				e.preventDefault();
				$( this ).parent( 'div' ).find( 'input[type=text]' ).val( '' );
				$( this ).parent( 'div' ).find( 'input[type=hidden]' ).val( '' );
				$( this ).hide();
			} );

		" );
	}


	/**
	 * Outputs text profile field data.
	 *
	 * @since 1.19.0
	 *
	 * @param \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field $profile_field
	 * @param \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition $profile_field_definition
	 */
	private function output_text_profile_field( Profile_Field $profile_field, Profile_Field_Definition $profile_field_definition ) {

		?>
		<dt><label for="wc-memberships-profile-field-<?php echo $profile_field_definition->get_slug(); ?>"><?php echo esc_html( stripslashes( $profile_field_definition->get_name() ) ); ?></label></dt>
		<dd>
			<span class="wc-memberships-profile-field-value" data-profile-field="<?php echo esc_attr( $profile_field->get_slug() ); ?>">
				<?php echo esc_html( $profile_field->get_formatted_value() ?: '&mdash;' ); ?>
			</span>
			<input
				type="text"
				name="_profile_fields[<?php echo $profile_field->get_slug(); ?>]"
				id="wc-memberships-profile-field-<?php echo $profile_field->get_slug(); ?>"
				class="wc-memberships-profile-field-input wc-memberships-profile-field-input-text"
				style="display: none;"
				value="<?php echo esc_attr( $profile_field->get_value() ); ?>"
				data-slug="<?php echo esc_attr( $profile_field->get_slug() ); ?>"
				data-editable-by="<?php echo esc_attr( $profile_field_definition->get_editable_by() ); ?>"
			/>
		</dd>
		<?php
	}


	/**
	 * Outputs text area profile field data.
	 *
	 * @since 1.19.0
	 *
	 * @param \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field $profile_field
	 * @param \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition $profile_field_definition
	 */
	private function output_textarea_profile_field( Profile_Field $profile_field, Profile_Field_Definition $profile_field_definition ) {

		?>
		<dt><label for="wc-memberships-profile-field-<?php echo esc_attr( $profile_field_definition->get_slug() ); ?>"><?php echo esc_html( stripslashes( $profile_field_definition->get_name() ) ); ?></label></dt>
		<dd>
			<span class="wc-memberships-profile-field-value" data-profile-field="<?php echo esc_attr( $profile_field->get_slug() ); ?>">
				<?php echo esc_html( $profile_field->get_formatted_value() ?: '&mdash;' ); ?>
			</span>
			<textarea
				name="_profile_fields[<?php echo esc_attr( $profile_field->get_slug() ); ?>]"
				id="wc-memberships-profile-field-<?php echo esc_attr( $profile_field->get_slug() ); ?>"
				class="wc-memberships-profile-field-input wc-memberships-profile-field-input-textarea"
				style="display: none;"
				data-slug="<?php echo esc_attr( $profile_field->get_slug() ); ?>"
				data-editable-by="<?php echo esc_attr( $profile_field_definition->get_editable_by() ); ?>"
			><?php echo esc_html( $profile_field->get_value() ); ?></textarea>
		</dd>
		<?php
	}


	/**
	 * Saves profile fields for the user membership.
	 *
	 * @since 1.19.0
	 *
	 * @param int $post_id user membership ID
	 * @param \WP_Post $post post object
	 */
	public function update_data( $post_id, \WP_Post $post ) {

		$user_membership = wc_memberships_get_user_membership( $post );

		if ( ! $user_membership ) {
			return;
		}

		$profile_fields = Framework\SV_WC_Helper::get_posted_value( '_profile_fields', [] );

		foreach ( Profile_Fields_Handler::get_profile_field_definitions( [ 'membership_plan_ids' => $user_membership->get_plan_id() ] ) as $profile_field_definition ) {

			$profile_field_slug = $profile_field_definition->get_slug( 'edit' );

			if ( isset( $profile_fields[ $profile_field_slug ] ) ) {

				if ( $profile_field_definition->is_type( Profile_Fields_Handler::TYPE_CHECKBOX ) ) {
					$value = wc_string_to_bool( $profile_fields[ $profile_field_slug ] );
				} else {
					$value = $profile_fields[ $profile_field_slug ];
				}

				try {

					$profile_field = $user_membership->get_profile_field( $profile_field_slug ) ?: new Profile_Field();

					$profile_field->set_user_id( $user_membership->get_user_id() );
					$profile_field->set_slug( $profile_field_slug );
					$profile_field->set_value( $value );
					$profile_field->save();

				} catch ( \Exception $e ) {

					wc_memberships()->get_admin_instance()->get_message_handler()->add_error( $e->getMessage() );
				}

			} else {

				$user_membership->delete_profile_field( $profile_field_slug );
			}
		}
	}


}

<?php

/**
 * Add extra profile fields for users in admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'FUE_Admin_Profile' ) ) :

class FUE_Admin_Profile {

	/**
	 * Class constructor. Hook in the methods.
	 */
	public function __construct() {
		add_action( 'show_user_profile', array( $this, 'add_api_key_field' ), 20 );
		add_action( 'edit_user_profile', array( $this, 'add_api_key_field' ), 20 );

		add_action( 'personal_options_update', array( $this, 'generate_api_key' ) );
		add_action( 'edit_user_profile_update', array( $this, 'generate_api_key' ) );
	}

	/**
	 * Display the API keys.
	 *
	 * @param WP_User $user
	 * @since 4.1
	 */
	public function add_api_key_field( $user ) {
		if ( ! current_user_can( 'manage_follow_up_emails' ) )
			return;

		$permissions = array(
			'read'       => __( 'Read', 'follow_up_emails' ),
			'write'      => __( 'Write', 'follow_up_emails' ),
			'read_write' => __( 'Read/Write', 'follow_up_emails' ),
		);

		if ( current_user_can( 'edit_user', $user->ID ) ) {
			?>
			<table class="form-table">
				<tbody>
				<tr>
					<th><label for="fue_generate_api_key"><?php esc_html_e( 'Follow-Up Emails API Keys', 'follow_up_emails' ); ?></label></th>
					<td>
						<?php if ( empty( $user->fue_api_consumer_key ) ) : ?>
							<input name="fue_generate_api_key" type="checkbox" id="fue_generate_api_key" value="0" />
							<span class="description"><?php esc_html_e( 'Generate API Key', 'follow_up_emails' ); ?></span>
						<?php else : ?>
							<strong><?php esc_html_e( 'Consumer Key:', 'follow_up_emails' ); ?>&nbsp;</strong><code id="fue_api_consumer_key"><?php echo esc_html( $user->fue_api_consumer_key ) ?></code><br/>
							<strong><?php esc_html_e( 'Consumer Secret:', 'follow_up_emails' ); ?>&nbsp;</strong><code id="fue_api_consumer_secret"><?php echo esc_html( $user->fue_api_consumer_secret ); ?></code><br/>
							<strong><?php esc_html_e( 'Permissions:', 'follow_up_emails' ); ?>&nbsp;</strong><span id="fue_api_key_permissions"><select name="fue_api_key_permissions" id="fue_api_key_permissions">
							<?php
							foreach ( $permissions as $permission_key => $permission_name ) {
								echo '<option value="' . esc_attr( $permission_key ) . '" ' . selected( $permission_key, $user->fue_api_key_permissions, false ) . '>' . esc_html( $permission_name ) . '</option>';
							}
							?>
							</select></span><br/>
							<input name="fue_generate_api_key" type="checkbox" id="fue_generate_api_key" value="0" />
							<span class="description"><?php esc_html_e( 'Revoke API Key', 'follow_up_emails' ); ?></span>
						<?php endif; ?>
					</td>
				</tr>
				</tbody>
			</table>
		<?php
		}
	}

	/**
	 * Generate and save API keys for the user.
	 *
	 * @param int $user_id
	 * @since 4.1
	 */
	public function generate_api_key( $user_id ) {

		if ( current_user_can( 'edit_user', $user_id ) ) {

			$user = get_userdata( $user_id );

			// Creating/deleting key.
			if ( isset( $_POST['fue_generate_api_key'] ) ) {

				// Consumer key.
				if ( empty( $user->fue_api_consumer_key ) ) {
					$consumer_key = 'ck_' . hash( 'md5', $user->user_login . date( 'U' ) . mt_rand() );
					update_user_meta( $user_id, 'fue_api_consumer_key', $consumer_key );
				} else {
					delete_user_meta( $user_id, 'fue_api_consumer_key' );
				}

				// Consumer secret.
				if ( empty( $user->fue_api_consumer_secret ) ) {
					$consumer_secret = 'cs_' . hash( 'md5', $user->ID . date( 'U' ) . mt_rand() );
					update_user_meta( $user_id, 'fue_api_consumer_secret', $consumer_secret );
				} else {
					delete_user_meta( $user_id, 'fue_api_consumer_secret' );
				}

				// Permissions.
				if ( empty( $user->fue_api_key_permissions ) ) {
					if ( isset( $_POST['fue_api_key_permissions'] ) ) {
						$permissions = ( in_array( $_POST['fue_api_key_permissions'], array( 'read', 'write', 'read_write' ) ) ) ? $_POST['fue_api_key_permissions'] : 'read';
					} else {
						$permissions = 'read';
					}

					update_user_meta( $user_id, 'fue_api_key_permissions', $permissions );
				} else {
					delete_user_meta( $user_id, 'fue_api_key_permissions' );
				}
			} else {
				// Updating permissions for key.
				if ( ! empty( $_POST['fue_api_key_permissions'] ) && $user->fue_api_key_permissions !== $_POST['fue_api_key_permissions'] ) {

					$permissions = ( ! in_array( $_POST['fue_api_key_permissions'], array( 'read', 'write', 'read_write' ) ) ) ? 'read' : $_POST['fue_api_key_permissions'];

					update_user_meta( $user_id, 'fue_api_key_permissions', $permissions );
				}
			}
		}
	}
}

endif;

return new FUE_Admin_Profile();

<?php
/**
 * Dashboard template: Support Functions
 *
 * Manage support tickets, grant support-staff access and view System
 * configuration.
 *
 * Following variables are passed into the template:
 * - $member          = WPMUDEV_Dashboard::$api->get_profile();
 * - $urls            = $this->page_urls;
 * - $allowed_users   = WPMUDEV_Dashboard::$site->get_allowed_users();
 * - $auto_update     = WPMUDEV_Dashboard::$site->get_option( 'autoupdate_dashboard' );
 * - $membership_type = WPMUDEV_Dashboard::$api->get_membership_type( $single_id );
 * - $single_id (int. ID of the single-license project)
 *
 * @since   4.0.0
 * @package WPMUDEV_Dashboard
 */

// Render the page header section.
$page_title = __( 'Settings', 'wpmudev' );
$page_slug  = 'settings';
$this->render_sui_header( $page_title, $page_slug );

/** @var WPMUDEV_Dashboard_Sui $this */
/** @var WPMUDEV_Dashboard_Sui_Page_Urls $urls */
/** @var array $member */
/** @var array $allowed_users */
/** @var bool $auto_update */
/** @var string $membership_type */
/** @var int $single_id */

$can_manage_users = true;
$profile          = $member['profile'];

// Adding users is only possible when the admin did not define a hardcoded
// user-list in wp-config.
if ( WPMUDEV_LIMIT_TO_USER ) {
	$can_manage_users = false;
}

?>

<?php if ( isset( $_GET['success-action'] ) ) : // wpcs csrf ok. ?>

	<?php switch ( $_GET['success-action'] ) { // wpcs csrf ok.

		case 'autoupdate-dashboard' : ?>

			<div class="sui-notice-top sui-notice-success sui-can-dismiss">

				<div class="sui-notice-content">
					<p><?php esc_html_e( 'General settings updated.', 'wpmudev' ); ?></p>
				</div>

				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>

			</div>

			<?php break;

		case 'admin-add' : ?>

			<div class="sui-notice-top sui-notice-success sui-can-dismiss">

				<div class="sui-notice-content">
					<p><?php esc_html_e( 'User added.', 'wpmudev' ); ?></p>
				</div>

				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>

			</div>

			<?php break;

		case 'admin-remove' : ?>

			<div class="sui-notice-top sui-notice-success sui-can-dismiss">

				<div class="sui-notice-content">
					<p><?php esc_html_e( 'User removed.', 'wpmudev' ); ?></p>
				</div>

				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>

			</div>

			<?php break;
		default:
			break;
	} ?>

<?php endif; ?>

<?php if ( isset( $_GET['failed-action'] ) ) : // wpcs csrf ok. ?>

	<?php switch ( $_GET['failed-action'] ) { // wpcs csrf ok.

		case 'remote-grant' : ?>

			<div class="sui-notice-top sui-notice-error sui-can-dismiss">

				<div class="sui-notice-content">
					<p><?php esc_html_e( 'Failed to grant support access.', 'wpmudev' ); ?></p>
				</div>

				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>

			</div>

			<?php break;
		default:
			break;
	} ?>

<?php endif; ?>

<div class="sui-row-with-sidenav">

	<div class="sui-sidenav">

		<ul class="sui-vertical-tabs sui-sidenav-hide-md">

			<li class="sui-vertical-tab">
				<a href="#general">
					<?php esc_html_e( 'General', 'wpmudev' ); ?>
				</a>
			</li>

			<li class="sui-vertical-tab">
				<a href="#permissions">
					<?php esc_html_e( 'Permissions', 'wpmudev' ); ?>
				</a>
			</li>

			<li class="sui-vertical-tab">
				<a href="#apikey"><?php esc_html_e( 'API Key', 'wpmudev' ); ?></a>
			</li>

		</ul>

		<div class="sui-sidenav-hide-lg">

			<select class="sui-mobile-nav">
				<option value="#general" selected="selected"><?php esc_html_e( 'General', 'wpmudev' ); ?></option>
				<option value="#permissions"><?php esc_html_e( 'Permissions', 'wpmudev' ); ?></option>
				<option value="#apikey"><?php esc_html_e( 'API Key', 'wpmudev' ); ?></option>
			</select>

		</div>

	</div>

	<div class="sui-box js-sidenav-content" id="general" style="display: none;">

		<form method="POST" action="<?php echo esc_url( $urls->settings_url ) . '#general'; ?>">

			<input
				type="hidden"
				name="action"
				value="autoupdate-dashboard"
			/>

			<?php wp_nonce_field( 'autoupdate-dashboard', 'hash' ); ?>

			<div class="sui-box-header">
				<h2 class="sui-box-title"><?php esc_html_e( 'General', 'wpmudev' ); ?></h2>
			</div>

			<div class="sui-box-body">

				<div class="sui-box-settings-row">

					<div class="sui-box-settings-col-1">

						<span class="sui-settings-label"><?php esc_html_e( 'Automatic Updates', 'wpmudev' ); ?></span>

						<span class="sui-description"><?php esc_html_e( 'Enable automatic updates of the WPMU DEV Dashboard plugin to ensure API connectivity is always up to date.', 'wpmudev' ); ?></span>

					</div>

					<div class="sui-box-settings-col-2">

						<label class="sui-toggle">
							<input
								type="checkbox"
								name="autoupdate_dashboard"
								value="1"
								id="autoupdate_dashboard"
								<?php checked( $auto_update ); ?>
							/>
							<span class="sui-toggle-slider"></span>
						</label>

						<label for="autoupdate_dashboard"><?php esc_html_e( 'Automatically update the WPMU DEV Dashboard plugin', 'wpmudev' ); ?></label>

					</div>

				</div>

				<div class="sui-box-settings-row">

					<div class="sui-box-settings-col-1">

						<span class="sui-settings-label"><?php esc_html_e( 'Single Sign-on', 'wpmudev' ); ?></span>

						<span class="sui-description"><?php esc_html_e( 'Tired of logging in to your WP Admin area? Enable this setting to be automatically logged in when you visit this site from The Hub.', 'wpmudev' ); ?></span>

					</div>

					<div class="sui-box-settings-col-2">

						<label class="sui-toggle">
							<input
								type="checkbox"
								name="enable_sso"
								value="1"
								id="enable_sso"
								<?php checked( $enable_sso ); ?>
							/>
							<span class="sui-toggle-slider"></span>
						</label>
						<div class="enable_sso_label">
							<label for="enable_sso"><?php esc_html_e( 'Enable Single Sign-on for this website', 'wpmudev' ); ?></label>
							<div class="sui-notice"><p><?php printf( esc_html__( 'Note: You need to stay logged into %1$1s The Hub%2$2s to use this feature.', 'wpmudev' ), '<a href="https://premium.wpmudev.org/hub/my-websites/">', '</a>' ); ?></p></div>
						</div>
					</div>
				</div>

			</div>

			<div class="sui-box-footer">

				<div class="sui-actions-right">

					<button
						type="submit"
						name="status"
						value="settings"
						class="sui-button sui-button-blue"
					>

						<span class="sui-loading-text">
							<i class="sui-icon-save" aria-hidden="true"></i>
							<?php esc_html_e( 'Save Changes', 'wpmudev' ); ?>
						</span>

						<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>

					</button>

				</div>

			</div>

		</form>

	</div>

	<div class="sui-box js-sidenav-content" id="permissions" style="display: none;">

		<form
			method="POST"
			action="<?php echo esc_url( $urls->settings_url ) . '#permissions'; ?>"
		>

			<input
				type="hidden"
				name="action"
				value="permissions-setup"
			/>

			<?php wp_nonce_field( 'permissions-setup', 'hash' ); ?>

			<div class="sui-box-header">
				<h2 class="sui-box-title"><?php esc_html_e( 'Permissions', 'wpmudev' ); ?></h2>
			</div>

			<div class="sui-box-body">

				<div class="sui-box-settings-row">

					<div class="sui-box-settings-col-1">

						<span class="sui-settings-label"><?php esc_html_e( 'Visibility', 'wpmudev' ); ?></span>

						<span class="sui-description"><?php esc_html_e( 'By default, only the user who authenticated the WPMU DEV Dashboard can see it in the sidebar. Enable other admins to view it by adding them here.' ); ?></span>

					</div>

					<div class="sui-box-settings-col-2">

						<div class="dashui-list-users">

							<?php foreach ( $allowed_users as $user ) :

								$disabled   = '';
								$remove_url = '';

								if ( $can_manage_users ) {

									$remove_url = wp_nonce_url(
										add_query_arg(
											array(
												'user'   => $user['id'],
												'action' => 'admin-remove',
											),
											$urls->settings_url . '#permissions'
										),
										'admin-remove',
										'hash'
									);

								} else {
									$disabled = ' disabled';
								} ?>

								<div class="dashui-user">

									<div class="dashui-user-name">
										<i class="sui-icon-profile-male" aria-hidden="true"></i>
										<span><?php echo esc_html( ucwords( $user['name'] ) ); ?></span>
										<?php if ( $user['is_me'] ) : ?>
											<span class="sui-tag"><?php esc_html_e( 'You', 'wpmudev' ); ?></span>
										<?php endif; ?>
									</div>

									<div class="dashui-user-email">
										<span><?php echo esc_html( $user['email'] ); ?></span>
										<?php if ( $user['is_me'] ) : ?>
											<span class="sui-tag"><?php esc_html_e( 'You', 'wpmudev' ); ?></span>
										<?php endif; ?>
									</div>

									<div class="dashui-user-action">

										<?php if ( $user['is_me'] ) { ?>

											<div
												class="sui-button-icon sui-tooltip disabled"
												data-tooltip="<?php esc_html_e( 'You cannot remove yourself', 'wpmudev' ); ?>"
											>
												<i class="sui-icon-trash" aria-hidden="true"></i>
											</div>

										<?php } else { ?>

											<a
												href="<?php echo esc_url( $remove_url ); ?>"
												class="sui-button-icon js-remove-user-permisssions<?php echo esc_attr( $disabled ); ?>"
											>
												<span class="sui-loading-text">
													<i class="sui-icon-trash" aria-hidden="true"></i>
												</span>
												<i class="sui-icon-loader sui-loading"></i>
											</a>

										<?php } ?>

									</div>

								</div>

							<?php endforeach; ?>

						</div>

						<button class="sui-button sui-button-ghost js-show-admin-add-modal" <?php echo( ! $can_manage_users ? 'disabled="disabled"' : '' ); ?>>
							<i class="sui-icon-plus" aria-hidden="true"></i>
							<?php esc_html_e( 'ADD USER', 'wpmudev' ); ?>
						</button>

						<?php if ( ! $can_manage_users ) : ?>

							<div class="sui-notice">
								<p><?php esc_html_e( 'To manage user permissions here you need to remove the constant WPMUDEV_LIMIT_TO_USER from your wp-config file.', 'wpmudev' ); ?></p>
							</div>

						<?php endif; ?>

					</div>

				</div>

			</div>

		</form>

	</div>

	<div class="sui-box js-sidenav-content" id="apikey" style="display: none;">

		<div class="sui-box-header">

			<h2 class="sui-box-title"><?php esc_html_e( 'API Key', 'wpmudev' ); ?></h2>

			<div class="sui-actions-right">

				<a
					href="<?php echo esc_url( $urls->hub_url . '/account' ); ?>"
					target="_blank"
					class="sui-button sui-button-ghost"
				>
					<i class="sui-icon-key" aria-hidden="true"></i>
					<?php esc_html_e( 'Manage API Key', 'wpmudev' ); ?>
				</a>

			</div>

		</div>

		<div class="sui-box-body">

			<p><?php esc_html_e( 'Your API Key is unique to your WPM DEV account and is the connection between you and our servers, and your access to all our Pro plugins syncing with The Hub.', 'wpmudev' ); ?></p>

			<div class="sui-form-field ">

				<label class="sui-label" for="api_key"><?php esc_html_e( 'Active Key', 'wpmudev' ); ?></label>

				<div class="sui-control-with-icon">
					<input
						value="<?php echo esc_attr( strtolower( WPMUDEV_Dashboard::$api->get_key() ) ); ?>"
						class="sui-form-control"
						id="api_key"
						readonly="readonly"
					>
					<i class="sui-icon-key" aria-hidden="true"></i>
				</div>

			</div>

			<div class="sui-notice">

				<p><?php esc_html_e( 'Note: If you are experiencing issues connecting to WPMU DEV, resetting this key can sometimes fix issues. You can do this via the Manage API Key button above.', 'wpmudev' ); ?></p>

			</div>

		</div>

	</div>

</div>

<?php
$this->load_sui_template( 'element-last-refresh', array(), true );

$this->load_sui_template( 'footer', array(), true ); ?>

<div class="sui-dialog sui-dialog-alt sui-dialog-sm" aria-hidden="true" tabindex="-1" id="admin-add">

	<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>

	<div class="sui-dialog-content" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">

		<div class="sui-box" role="document">

			<form id="form-admin-add" action="<?php echo esc_url( $urls->settings_url ) . '#permissions'; ?>" method="POST">

				<input type="hidden" name="action" value="admin-add" />

				<?php wp_nonce_field( 'admin-add', 'hash' ); ?>

				<div class="sui-box-header sui-block-content-center">

					<h3 class="sui-box-title" id="dialogTitle"><?php esc_html_e( 'Add User', 'wpmudev' ); ?></h3>

					<a class="sui-dialog-close" data-a11y-dialog-hide>
						<span class="sui-screen-reader-text"><?php esc_html_e( 'Close this dialog window', 'wpmudev' ); ?></span>
					</a>

				</div>

				<div class="sui-box-body sui-box-body-slim sui-block-content-center">

					<p id="dialogDescription" class="sui-description"><?php esc_html_e( 'Add as many administrators as you like. Only these specific users will see the WPMU DEV menu.', 'wpmudev' ); ?></p>

					<div class="sui-form-field">
						<label class="sui-label" for="searchuser"><?php echo esc_html__('Search users', 'wpmudev'); ?></label>
						<div class="sui-control-with-icon">
							<select class="sui-select sui-form-control"
							        id="searchuser"
							        name="user"
							        data-placeholder="<?php esc_html_e( 'Type Username', 'wpmudev' ); ?>"
							        data-minimum-input-length="3"
							        data-hash="<?php echo esc_attr( wp_create_nonce( 'usersearch' ) ); ?>"
							        data-language-searching="<?php esc_attr_e( 'Searching...', 'wpmudev' ); ?>"
							        data-language-noresults="<?php esc_attr_e( 'No results found', 'wpmudev' ); ?>"
							        data-language-error-load="<?php esc_attr_e( 'Searching...', 'wpmudev' ); ?>"
							        data-language-input-tooshort="<?php esc_attr_e( 'Type minimum 3 characters to start searching', 'wpmudev' ); ?>"
							>
							</select>
							<i class="sui-icon-profile-male" aria-hidden="true"></i>
						</div>
					</div>
				</div>

				<div class="sui-box-footer" style="padding-top: 0;">

					<a class="sui-button sui-button-ghost" data-a11y-dialog-hide="admin-add"><?php esc_html_e( 'Cancel', 'wpmudev' ); ?></a>

					<button type="submit" class="sui-button">
						<span class="sui-loading-text"><?php esc_html_e( 'Add', 'wpmudev' ); ?></span>
						<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
					</button>

				</div>

			</form>

		</div>

	</div>
</div>

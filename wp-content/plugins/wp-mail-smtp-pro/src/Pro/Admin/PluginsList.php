<?php

namespace WPMailSMTP\Pro\Admin;

use WPMailSMTP\Options;
use WPMailSMTP\Pro\License\Updater;
use WPMailSMTP\Pro\Pro;

/**
 * Class PluginsList.
 *
 * Handles the notice displayed in the Plugins page for Pro users.
 *
 * @since 3.8.0
 */
class PluginsList {

	/**
	 * License Status: Empty.
	 *
	 * @since 3.8.0
	 *
	 * @var int
	 */
	const LICENSE_STATUS_EMPTY = 1;

	/**
	 * License Status: Expired.
	 *
	 * @since 3.8.0
	 *
	 * @var int
	 */
	const LICENSE_STATUS_EXPIRED = 2;

	/**
	 * License Status: Valid.
	 *
	 * @since 3.8.0
	 *
	 * @var int
	 */
	const LICENSE_STATUS_VALID = 3;

	/**
	 * The license status.
	 *
	 * @since 3.8.0
	 *
	 * @var null|int
	 */
	private $license_status = null;

	/**
	 * Latest version fetched from remote source.
	 *
	 * @since 3.8.0
	 *
	 * @var null|string
	 */
	private $remote_latest_version = null;

	/**
	 * Whether or not this site is using the latest version of WP Mail SMTP Pro.
	 *
	 * @since 3.8.0
	 *
	 * @var bool
	 */
	private $is_using_latest_version = null;

	/**
	 * Register hooks.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function hooks() {

		add_action( 'admin_head', [ $this, 'add_style' ] );
		add_filter( 'site_transient_update_plugins', [ $this, 'site_transient_update_plugins' ] );
		add_action( 'after_plugin_row', [ $this, 'show_plugin_notice' ] );
	}

	/**
	 * Remove the border between the WP Mail SMTP Pro and custom notice.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function add_style() {

		$current_screen = get_current_screen();

		if ( is_null( $current_screen ) || $current_screen->id !== 'plugins' || $this->get_license_status() === self::LICENSE_STATUS_VALID ) {
			return;
		}

		?>
		<style>
			.plugins tr[data-slug="wp-mail-smtp-pro"] th,
			.plugins tr[data-slug="wp-mail-smtp-pro"] td {
				box-shadow: none;
			}

			.plugins tr[data-slug="wp-mail-smtp-pro"] .second,
			.plugins tr[data-slug="wp-mail-smtp-pro"] .row-actions {
				padding-bottom: 0;
			}

			@media screen and (max-width: 782px) {
				.plugins tr[data-slug="wp-mail-smtp-pro"].plugin-update-tr.active:before {
					background-color: #f0f6fc;
					border-left: 4px solid #72aee6;
				}
			}
		</style>
		<?php
	}

	/**
	 * Filters `update_plugins` transient in Admin Plugins page.
	 *
	 * This method removes the "update" of the Pro plugin IF the license key is not valid.
	 * Doing this fixes the edge case where both the default WP plugin update notice and our custom
	 * notice are displayed at the same time.
	 *
	 * @since 3.8.0
	 *
	 * @param mixed $value Value of site transient.
	 *
	 * @return object $value Amended WordPress update object.
	 */
	public function site_transient_update_plugins( $value ) {

		global $current_screen;

		// We only want this filter in the Dashboard -> Plugins page.
		if ( is_null( $current_screen ) || $current_screen->id !== 'plugins' ) {
			return $value;
		}

		$plugin_path = $this->get_pro_plugin_file_path();

		if ( empty( $value ) || ! property_exists( $value, 'response' ) || empty( $value->response[ $plugin_path ] ) ) {
			return $value;
		}

		if ( $this->get_license_status() === self::LICENSE_STATUS_VALID ) {
			return $value;
		}

		unset( $value->response[ $plugin_path ] );

		$value->no_update[ $plugin_path ] = Updater::get_no_update_object(
			[
				'plugin_path' => $plugin_path,
				'plugin_slug' => Pro::SLUG,
				'version'     => WPMS_PLUGIN_VER,
			]
		);

		return $value;
	}

	/**
	 * Returns the WP Mail SMTP Pro plugin file path relative to the plugins directory.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	private function get_pro_plugin_file_path() {

		return Pro::SLUG . '/wp_mail_smtp.php';
	}

	/**
	 * Get the license status.
	 *
	 * @since 3.8.0
	 *
	 * @return int
	 */
	private function get_license_status() {

		if ( ! is_null( $this->license_status ) ) {
			return $this->license_status;
		}

		$this->license_status = self::LICENSE_STATUS_EMPTY;

		$license_option = Options::init()->get_group( 'license' );

		// If there's a license, check if its expired.
		if ( ! empty( $license_option['is_expired'] ) && $license_option['is_expired'] === true ) {
			$this->license_status = self::LICENSE_STATUS_EXPIRED;

			return $this->license_status;
		}

		if ( wp_mail_smtp()->get_pro()->get_license()->is_valid() ) {
			$this->license_status = self::LICENSE_STATUS_VALID;
		}

		return $this->license_status;
	}

	/**
	 * Adds custom plugin notice for Pro users without a valid license.
	 *
	 * @since 3.8.0
	 *
	 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
	 *
	 * @return void
	 */
	public function show_plugin_notice( $plugin_file ) {

		if (
			$plugin_file !== $this->get_pro_plugin_file_path() ||
			$this->get_license_status() === self::LICENSE_STATUS_VALID
		) {
			return;
		}

		global $wp_list_table;

		$columns_count = 4;

		if ( ! empty( $wp_list_table ) ) {
			$columns_count = $wp_list_table->get_column_count();
		}
		?>
		<tr id="<?php echo esc_attr( Pro::SLUG ); ?>-update" class='plugin-update-tr active'
			data-slug="<?php echo esc_attr( Pro::SLUG ); ?>"
			data-plugin='<?php echo esc_attr( $plugin_file ); ?>'
		>
			<td colspan='<?php echo esc_attr( $columns_count ); ?>' class='plugin-update'>
				<div class='update-message notice inline notice-warning notice-alt'>
					<?php
						echo '<p>' . wp_kses(
							$this->get_update_notice(),
							[
								'a'      => [
									'href'   => [],
									'target' => [],
								],
								'br'     => [],
								'strong' => [
									'style' => [],
								],
							]
						) . '</p>';
					?>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Get the update notice.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	private function get_update_notice() {

		switch ( $this->get_license_status() ) {
			case self::LICENSE_STATUS_EMPTY:
				$message = $this->get_no_license_notice();
				break;

			case self::LICENSE_STATUS_EXPIRED:
				$message = $this->get_expired_license_notice();
				break;

			default:
				$message = '';
				break;
		}

		return $message;
	}

	/**
	 * Get the notice for users without license key.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	private function get_no_license_notice() {

		$activate_url = wp_mail_smtp()->get_admin()->get_admin_page_url();
		$purchase_url = wp_mail_smtp()->get_upgrade_link(
			[
				'medium'  => 'all-plugins-license',
				'content' => 'Purchase one now',
			]
		);

		if ( $this->is_using_latest_version() ) {
			return sprintf( /* translators: %1$s - WP Mail SMTP Pro URL; %2$s - WP Mail SMTP Pro purchase link. */
				__(
					'<a href="%1$s">Activate WP Mail SMTP Pro</a> to receive features, updates, and support. Don\'t have a license? <a target="_blank" href="%2$s" rel="noopener noreferrer">Purchase one now</a>.',
					'wp-mail-smtp-pro'
				),
				esc_url( $activate_url ),
				esc_url( $purchase_url )
			);
		}

		return $this->get_new_version_available_notice()
		. '<br />'
		. sprintf( /* translators: %1$s - WP Mail SMTP Pro URL; %2$s - WP Mail SMTP Pro purchase link. */
			__(
				'<a href="%1$s">Activate</a> your license to access this update, new features, and support. Don\'t have a license? <a target="_blank" href="%2$s" rel="noopener noreferrer">Purchase one now</a>.',
				'wp-mail-smtp-pro'
			),
			esc_url( $activate_url ),
			esc_url( $purchase_url )
		);
	}

	/**
	 * Get the notice for users with expired license key.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	private function get_expired_license_notice() {

		$message = $this->is_using_latest_version() ? '' : $this->get_new_version_available_notice() . '<br />';

		return $message . sprintf( /* translators: %s - WP Mail SMTP Pro renew link. */
			__(
				'<strong style="color: #e72f0a">Your WP Mail SMTP Pro license is expired.</strong> <a target="_blank" href="%s" rel="noopener noreferrer">Renew now</a> to receive new features, updates, and support.',
				'wp-mail-smtp-pro'
			),
			esc_url(
				wp_mail_smtp()->get_pro()->get_license()->get_renewal_link(
					[
						'medium'  => 'all-plugins-license',
						'content' => 'Renew now',
					]
				)
			)
		);
	}

	/**
	 * Get the notice to show when a new version of WP Mail SMTP Pro is available.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	private function get_new_version_available_notice() {

		return sprintf( /* translators: %1$s - WP Mail SMTP Pro Changelog link; %2$s - WP Mail SMTP Pro latest version. */
			__(
				'There is a new version of WP Mail SMTP Pro available. <a target="_blank" href="%1$s" rel="noopener noreferrer">View version %2$s details</a>.',
				'wp-mail-smtp-pro'
			),
			esc_url(
				wp_mail_smtp()->get_utm_url(
					'https://wpmailsmtp.com/docs/how-to-view-recent-changes-to-the-wp-mail-smtp-plugin-changelog/',
					[
						'medium'  => 'all-plugins-license',
						'content' => 'View version details',
					]
				)
			),
			esc_html( $this->get_remote_latest_version() )
		);
	}

	/**
	 * Whether or not this site is using the latest version of WP Mail SMTP Pro.
	 *
	 * @since 3.8.0
	 *
	 * @return bool
	 */
	private function is_using_latest_version() {

		if ( ! is_null( $this->is_using_latest_version ) ) {
			return $this->is_using_latest_version;
		}

		$this->is_using_latest_version = ! version_compare( WPMS_PLUGIN_VER, $this->get_remote_latest_version(), '<' );

		return $this->is_using_latest_version;
	}

	/**
	 * Get the remote latest version.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	private function get_remote_latest_version() {

		if ( ! is_null( $this->remote_latest_version ) ) {
			return $this->remote_latest_version;
		}

		$this->remote_latest_version = wp_mail_smtp()->get_pro()->get_license()->fetch_latest_plugin_version();

		return $this->remote_latest_version;
	}
}

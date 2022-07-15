<?php

namespace WPMailSMTP\Pro\Alerts\Admin;

use WPMailSMTP\Admin\Pages\AlertsTab;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\Alerts\AbstractOptions;
use WPMailSMTP\Pro\Alerts\Loader;
use WPMailSMTP\Pro\Emails\Logs\Admin\PageAbstract;
use WPMailSMTP\WP;

/**
 * Class SettingsTab.
 *
 * @since 3.5.0
 */
class SettingsTab extends AlertsTab {

	/**
	 * Providers loader.
	 *
	 * @since 3.5.0
	 *
	 * @var Loader
	 */
	private $loader;

	/**
	 * Constructor.
	 *
	 * @since 3.5.0
	 *
	 * @param PageAbstract $parent_page Parent page object.
	 */
	public function __construct( $parent_page = null ) {

		parent::__construct( $parent_page );

		if ( wp_mail_smtp()->get_admin()->get_current_tab() === $this->slug ) {
			$this->hooks();
		}

		$this->loader = new Loader();
	}

	/**
	 * Register hooks.
	 *
	 * @since 3.5.0
	 */
	public function hooks() {

		add_action( 'wp_mail_smtp_admin_area_enqueue_assets', [ $this, 'enqueue_assets' ] );

		add_filter( 'wp_mail_smtp_options_postprocess_key_defaults', [ $this, 'options_defaults' ], 10, 3 );
	}

	/**
	 * Enqueue required JS and CSS.
	 *
	 * @since 3.5.0
	 */
	public function enqueue_assets() {

		$min = WP::asset_min();

		wp_enqueue_script(
			'wp-mail-smtp-alerts',
			wp_mail_smtp()->plugin_url . "/assets/pro/js/smtp-pro-alerts{$min}.js",
			[ 'jquery', 'wp-mail-smtp-admin' ],
			WPMS_PLUGIN_VER,
			true
		);

		wp_localize_script(
			'wp-mail-smtp-alerts',
			'wp_mail_smtp_alerts',
			$this->get_localized_data()
		);
	}

	/**
	 * Get localized data.
	 *
	 * @since 3.5.0
	 *
	 * @return array
	 */
	private function get_localized_data() {

		return [
			'providers' => array_map(
				function ( AbstractOptions $option ) {
					return [
						'connection_options_tmpl' => $option->get_connection_options( [], '%%index%%' ),
						'max_connections_count'   => $option->get_max_connections_count(),
					];
				},
				$this->loader->get_options_all()
			),
		];
	}

	/**
	 * Set options defaults.
	 *
	 * @since 3.5.0
	 *
	 * @param mixed  $value Option value.
	 * @param string $group Group key.
	 * @param string $key   Option key.
	 *
	 * @return mixed
	 */
	public function options_defaults( $value, $group, $key ) {

		// Set admin email as default for Email channel.
		if ( $group === 'alert_email' && $key === 'connections' ) {
			$value = [
				[
					'send_to' => get_option( 'admin_email' ),
				],
			];
		} elseif (
			$key === 'connections' &&
			in_array( $group, [ 'alert_slack_webhook', 'alert_twilio_sms', 'alert_custom_webhook' ], true )
		) {
			$value = [];
		}

		return $value;
	}

	/**
	 * Output HTML of the email controls settings.
	 *
	 * @since 3.5.0
	 */
	public function display() {

		$options = Options::init();
		?>

		<form method="POST" action="">
			<?php $this->wp_nonce_field(); ?>

			<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-content section-heading">
				<div class="wp-mail-smtp-setting-field">
					<h2><?php esc_html_e( 'Alerts', 'wp-mail-smtp-pro' ); ?></h2>
					<p class="desc">
						<?php esc_html_e( 'Configure at least one of these integrations to receive notifications when email fails to send from your site. Alert notifications will contain the following important data: email subject, email Send To address, the error message, and helpful links to help you fix the issue.', 'wp-mail-smtp-pro' ); ?>
					</p>
				</div>
			</div>

			<?php foreach ( $this->loader->get_options_all() as $option ) : ?>
				<?php
				$is_enabled  = (bool) $options->get( $option->get_group(), 'enabled' );
				$connections = (array) $options->get( $option->get_group(), 'connections' );
				?>

				<div id="wp-mail-smtp-setting-row-alerts-<?php echo esc_attr( $option->get_slug() ); ?>" class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-alert">
					<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-content section-heading">
						<div class="wp-mail-smtp-setting-field">
							<h3><?php echo esc_html( $option->get_title() ); ?></h3>
							<p class="desc"><?php echo wp_kses_post( $option->get_description() ); ?></p>
						</div>
					</div>

					<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-checkbox-toggle">
						<div class="wp-mail-smtp-setting-label">
							<label for="wp-mail-smtp-setting-alert-<?php echo esc_attr( $option->get_slug() ); ?>-enabled">
								<?php
								/* translators: %s - Alert title. */
								echo sprintf( esc_html__( '%s Alerts', 'wp-mail-smtp-pro' ), esc_html( $option->get_title() ) );
								?>
							</label>
						</div>
						<div class="wp-mail-smtp-setting-field">
							<label for="wp-mail-smtp-setting-alert-<?php echo esc_attr( $option->get_slug() ); ?>-enabled">
								<input type="checkbox" id="wp-mail-smtp-setting-alert-<?php echo esc_attr( $option->get_slug() ); ?>-enabled" class="js-wp-mail-smtp-setting-alert-enabled" name="wp-mail-smtp[alert_<?php echo esc_attr( $option->get_slug() ); ?>][enabled]" value="yes" <?php checked( $is_enabled ); ?>/>
								<span class="wp-mail-smtp-setting-toggle-switch"></span>
								<span class="wp-mail-smtp-setting-toggle-checked-label"><?php esc_html_e( 'On', 'wp-mail-smtp-pro' ); ?></span>
								<span class="wp-mail-smtp-setting-toggle-unchecked-label"><?php esc_html_e( 'Off', 'wp-mail-smtp-pro' ); ?></span>
							</label>
						</div>
					</div>

					<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-alert-options"<?php echo ! $is_enabled ? ' style="display:none;"' : ''; ?>>

						<?php $option->display_options(); ?>

						<div class="wp-mail-smtp-setting-row">
							<div class="wp-mail-smtp-setting-field">
								<button class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-blueish js-wp-mail-smtp-setting-alert-add-connection" data-provider="<?php echo esc_attr( $option->get_slug() ); ?>" <?php disabled( $option->get_max_connections_count() > 0 && count( $connections ) >= $option->get_max_connections_count() ); ?>>
									<?php echo esc_html( $option->get_add_connection_text() ); ?>
								</button>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>

			<?php $this->display_save_btn(); ?>
		</form>
		<?php
	}

	/**
	 * Process tab form submission ($_POST).
	 *
	 * @since 3.5.0
	 *
	 * @param array $data Post data specific for the plugin.
	 */
	public function process_post( $data ) {

		$this->check_admin_referer();

		$options = Options::init();

		foreach ( $this->loader->get_options_all() as $option ) {
			$group                     = $option->get_group();
			$data[ $group ]['enabled'] = ! empty( $data[ $group ]['enabled'] );

			if ( ! empty( $data[ $group ]['connections'] ) ) {
				$data[ $group ]['connections'] = array_values( array_unique( $data[ $group ]['connections'], SORT_REGULAR ) );
			}
		}

		$all = $options->get_all();

		// Prevent connections array recursive merge.
		$all = array_merge( $all, $data );

		// All the sanitization is done there.
		$options->set( $all, false, true );

		WP::add_admin_notice(
			esc_html__( 'Settings were successfully saved.', 'wp-mail-smtp-pro' ),
			WP::ADMIN_NOTICE_SUCCESS
		);
	}
}

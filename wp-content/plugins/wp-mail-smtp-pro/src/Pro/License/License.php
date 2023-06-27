<?php

namespace WPMailSMTP\Pro\License;

use WPMailSMTP\Options;
use WPMailSMTP\Pro\Pro;
use WPMailSMTP\WP;

/**
 * License key fun.
 *
 * @since 1.5.0
 */
class License {

	/**
	 * Interval time, in days, to remote fetch the latest version.
	 *
	 * @since 3.8.0
	 *
	 * @var int
	 */
	const REMOTE_FETCH_LATEST_VERSION_INTERVAL_IN_DAYS = 7;

	/**
	 * Cache key for remote latest version.
	 *
	 * @since 3.8.0
	 *
	 * @var string
	 */
	const CACHE_REMOTE_LATEST_VERSION_KEY = 'wp_mail_smtp_latest_remote_version';

	/**
	 * Holds any license error messages.
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	public $errors = array();

	/**
	 * Holds any license success messages.
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	public $success = array();

	/**
	 * Remote URL for getting license information.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	public $remote_url = 'https://wpmailsmtp.com/license-api';

	/**
	 * Remote URL for getting the latest version information.
	 *
	 * @since 3.8.0
	 *
	 * @var string
	 */
	private $latest_version_remote_url = 'https://wpmailsmtp.com/wp-content/core.json';

	/**
	 * Primary class constructor.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		$this->register_updater();

		// Register licensing ajax action (with custom tasks).
		add_action( 'wp_ajax_wp_mail_smtp_pro_license_ajax', array( $this, 'process_ajax' ) );

		// Filter admin area options save process.
		add_filter( 'wp_mail_smtp_options_set', array( $this, 'filter_options_set' ) );

		// Redefine licensing field content.
		add_filter( 'wp_mail_smtp_admin_get_pages', function ( $pages ) {

			remove_action( 'wp_mail_smtp_admin_pages_settings_license_key', array(
				\WPMailSMTP\Admin\Pages\SettingsTab::class,
				'display_license_key_field_content',
			) );

			add_action( 'wp_mail_smtp_admin_pages_settings_license_key', array( $this, 'display_settings_license_key_field_content' ) );

			return $pages;
		} );

		// Admin notices.
		add_action( 'admin_notices', array( $this, 'notices' ) );

		if ( WP::use_global_plugin_settings() ) {
			add_action( 'network_admin_notices', array( $this, 'notices' ) );
		}

		// Periodic background license check.
		if ( wp_mail_smtp()->get_license_key() ) {
			$this->maybe_validate_key();
		}
	}

	/**
	 * Load plugin updater.
	 *
	 * @since 1.5.0
	 */
	protected function register_updater() {

		// Only in admin area.
		if ( ! is_admin() ) {
			return;
		}

		$key = wp_mail_smtp()->get_license_key();

		// Only if we have the key.
		if ( empty( $key ) ) {
			return;
		}

		// Initialize the updater.
		new Updater(
			array(
				'plugin_name' => 'WP Mail SMTP Pro',
				'plugin_slug' => Pro::SLUG,
				'plugin_path' => Pro::SLUG . '/wp_mail_smtp.php',
				'plugin_url'  => trailingslashit( wp_mail_smtp()->plugin_url ),
				'version'     => WPMS_PLUGIN_VER,
				'key'         => $key,
			)
		);
	}

	/**
	 * Process AJAX requests fired by a pro version of a plugin and related to a license management.
	 *
	 * @since 1.5.0
	 */
	public function process_ajax() {

		$generic_error = esc_html__( 'Something went wrong. Please try again later.', 'wp-mail-smtp-pro' );

		// Verify nonce.
		if (
			! isset( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'wp_mail_smtp_pro_license_nonce' )
		) {
			wp_send_json_error( $generic_error );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( $generic_error );
		}

		$task = isset( $_POST['task'] ) ? sanitize_key( $_POST['task'] ) : '';

		switch ( $task ) {
			case 'license_verify':
				$license = isset( $_POST['license'] ) ? sanitize_key( $_POST['license'] ) : '';

				if ( empty( $license ) ) {
					wp_send_json_error( $generic_error );
				}

				$this->verify_key( $license, true );
				break;

			case 'license_deactivate':
				$this->deactivate_key( true );
				break;

			case 'license_refresh':
				$this->validate_key( wp_mail_smtp()->get_license_key(), true, true );
				break;
		}

		// Process unknown tasks or other edge cases.
		wp_send_json_error( $generic_error );
	}

	/**
	 * Redefine admin area Settings page License Key field content.
	 *
	 * @since 1.5.0
	 *
	 * @param Options $options The plugin options.
	 * @param bool    $echo    Whether to output HTML.
	 */
	public function display_settings_license_key_field_content( $options, $echo = true ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded

		$key  = 'B5E0B5F8DD8689E6ACA49DD6E6E1A930';
		$type = 'Pro';
		$license     = $options->get_group( 'license' );
		$is_expired  = isset( $license['is_expired'] ) && $license['is_expired'] === true;
		$is_disabled = isset( $license['is_disabled'] ) && $license['is_disabled'] === true;
		$is_invalid  = isset( $license['is_invalid'] ) && $license['is_invalid'] === true;
		$is_valid    = ! empty( $key ) && ! $is_expired && ! $is_disabled && ! $is_invalid;

		$input_class = '';

		if ( $is_valid ) {
			$input_class = 'wp-mail-smtp-setting-license-key--valid';
		} elseif ( ! empty( $key ) ) {
			$input_class = 'wp-mail-smtp-setting-license-key--invalid';
		}

		ob_start();
		?>
		<div id="wp-mail-smtp-setting-field-license">
			<?php wp_nonce_field( 'wp_mail_smtp_pro_license_nonce', 'wp-mail-smtp-setting-license-nonce' ); ?>

			<div class="wp-mail-smtp-setting-field-row">
				<input type="password" id="wp-mail-smtp-setting-license-key"
							 value="<?php echo esc_attr( $key ); ?>" name="wp-mail-smtp[license][key]"
							 class="wp-mail-smtp-setting-license-key<?php echo ! empty( $input_class ) ? ' ' . esc_attr( $input_class ) : ''; ?>"
							 <?php echo ( $options->is_const_defined( 'license', 'key' ) || $is_valid ) ? 'disabled' : ''; ?>
				/>

				<?php if ( $is_expired ) : ?>
					<a href="<?php echo esc_url( $this->get_renewal_link( [ 'content' => 'Renew License Button' ] ) ); ?>" target="_blank" rel="noopener noreferrer" id="wp-mail-smtp-setting-license-key-renew" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-red">
						<?php esc_html_e( 'Renew License', 'wp-mail-smtp-pro' ); ?>
					</a>
				<?php endif; ?>

				<?php if ( empty( $key ) ) : ?>
					
				<?php else : ?>
					<button type="button" id="wp-mail-smtp-setting-license-key-deactivate" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-grey">
						<?php esc_html_e( 'Remove Key', 'wp-mail-smtp-pro' ); ?>
					</button>
				<?php endif; ?>

			</div>

			<?php
			$type_message = '';
			$desc_message = '';

			if ( empty( $key ) ) {
				$desc_message = wp_kses(
					sprintf( /* translators: %1$s - WP Mail SMTP account dashboard url; %2$s - pricing page url. */
						__( 'Your license key can be found in your <a href="%1$s" target="_blank" rel="noopener noreferrer">WP Mail SMTP Account Dashboard</a>. Don\'t have a license?  <a href="%2$s" target="_blank" rel="noopener noreferrer">Sign up today!</a>', 'wp-mail-smtp-pro' ),
						// phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
						esc_url( wp_mail_smtp()->get_utm_url( 'https://wpmailsmtp.com/account/', [ 'content' => 'License Key Account Dashboard Link' ] ) ),
						esc_url( wp_mail_smtp()->get_upgrade_link( [ 'content' => 'License Sign Up Today' ] ) )
					),
					[
						'a' => [
							'href'   => [],
							'target' => [],
							'rel'    => [],
						],
					]
				);
			} elseif ( $is_valid ) {
				$type_message = sprintf( /* translators: $s - license type. */
					esc_html__( 'Your license key level is %s.', 'wp-mail-smtp-pro' ),
					'<strong>' . esc_html( $type ) . '</strong>'
				);

				$desc_message = wp_kses(
					__( 'If your license has been upgraded or is incorrect, then please <a href="#" id="wp-mail-smtp-setting-license-key-refresh">force a refresh</a>.', 'wp-mail-smtp-pro' ),
					[
						'a' => [
							'href' => [],
							'id'   => [],
						],
					]
				);
			} elseif ( $is_expired ) {
				$type_message = wp_kses(
					__( '<b>Your license has expired.</b> An active license is needed to access some of the Pro features, plugin updates (including security improvements), and our world class support!', 'wp-mail-smtp-pro' ),
					[
						'b' => [],
					]
				);

				$desc_message = wp_kses(
					__( 'If your license has been upgraded or is incorrect, then please <a href="#" id="wp-mail-smtp-setting-license-key-refresh">force a refresh</a>.', 'wp-mail-smtp-pro' ),
					[
						'a' => [
							'href' => [],
							'id'   => [],
						],
					]
				);
			} elseif ( $is_disabled ) {
				$type_message = wp_kses(
					__( '<b>Your license key has been disabled.</b> Please use a different key to continue receiving automatic updates.', 'wp-mail-smtp-pro' ),
					[
						'b' => [],
					]
				);
			} elseif ( $is_invalid ) {
				$type_message = wp_kses(
					__( '<b>Your license key is invalid.</b> The key no longer exists or the user associated with the key has been deleted. Please use a different key to continue receiving automatic updates.', 'wp-mail-smtp-pro' ),
					[
						'b' => [],
					]
				);
			}

			if ( ! empty( $type_message ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				printf( '<p class="type">%s</p>', $type_message );
			}

			if ( ! empty( $desc_message ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				printf( '<p class="desc">%s</p>', $desc_message );
			}

			?>
		</div>
		<?php

		$result = ob_get_clean();

		if ( $echo ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $result;
		}

		return $result;
	}

	/**
	 * Sanitize admin area options.
	 *
	 * @since 1.5.0
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function filter_options_set( $options ) {

		if ( isset( $options['license'] ) ) {
			$options['license']['key']  = sanitize_key( (string) $options['license']['key'] );
			$options['license']['type'] = array_key_exists( 'type', $options['license'] ) ? sanitize_key( (string) $options['license']['type'] ) : '';

			if ( array_key_exists( 'is_expired', $options['license'] ) ) {
				$options['license']['is_expired'] = (bool) $options['license']['is_expired'];
			}
			if ( array_key_exists( 'is_disabled', $options['license'] ) ) {
				$options['license']['is_disabled'] = (bool) $options['license']['is_disabled'];
			}
			if ( array_key_exists( 'is_invalid', $options['license'] ) ) {
				$options['license']['is_invalid'] = (bool) $options['license']['is_invalid'];
			}
		} else {
			// Lite values by default.
			$options['license'] = array(
				'key'         => '',
				'type'        => 'lite',
				'is_expired'  => false,
				'is_disabled' => false,
				'is_invalid'  => false,
			);
		}

		return $options;
	}

	/**
	 * Verify a license key entered by the user.
	 *
	 * @since 1.5.0
	 *
	 * @param string $key
	 * @param bool   $ajax
	 *
	 * @return bool
	 */
	public function verify_key( $key = '', $ajax = false ) {

		if ( empty( $key ) ) {
			return false;
		}

		$options = Options::init();
		$all_opt = $options->get_all();

		// Perform a request to verify the key.
		$verify = $this->perform_remote_request( 'verify-key', array( 'tgm-updater-key' => $key ) );

		// If it returns false, send back a generic error message and return.
		if ( ! $verify ) {
			$msg = esc_html__( 'There was an error connecting to the remote key API. Please try again later.', 'wp-mail-smtp-pro' );
			if ( $ajax ) {
				wp_send_json_error( $msg );
			} else {
				$this->errors[] = $msg;

				return false;
			}
		}

		// If an error is returned, set the error and return.
		if ( ! empty( $verify->error ) ) {
			if ( $ajax ) {
				wp_send_json_error( $verify->error );
			} else {
				$this->errors[] = $verify->error;

				return false;
			}
		}

		$success = isset( $verify->success ) ? $verify->success : esc_html__( 'Congratulations! This site is now receiving automatic updates.', 'wp-mail-smtp-pro' );

		$this->success[] = $success;

		$license_type = isset( $verify->type ) ? $verify->type : $all_opt['license']['type'];

		// Otherwise, our request has been done successfully. Update the option and set the success message.
		$data = [
			'license' => [
				'key'         => $key,
				'type'        => $license_type,
				'is_expired'  => false,
				'is_disabled' => false,
				'is_invalid'  => false,
			],
		];

		$options->set( $data, false, false );

		wp_clean_plugins_cache( true );

		if ( $ajax ) {
			wp_send_json_success(
				[
					'type'          => $license_type,
					'message'       => $success,
					'settings_html' => $this->display_settings_license_key_field_content( $options, false ),
				]
			);
		}

		return true;
	}

	/**
	 * Maybe validate a license key entered by the user.
	 *
	 * @since 1.5.0
	 */
	public function maybe_validate_key() {

		$options = Options::init();
		$all_opt = $options->get_all();

		if ( empty( $all_opt['license']['key'] ) ) {
			return;
		}

		if ( empty( $all_opt['license']['updates'] ) ) {
			$data = [
				'license' => [
					'updates' => strtotime( '+24 hours' ),
				],
			];

			$options->set( $data, false, false );

			// Perform a request to validate the key.
			$this->validate_key( $all_opt['license']['key'] );
		} else {
			$current_timestamp = time();
			if ( $current_timestamp < $all_opt['license']['updates'] ) {
				return;
			} else {
				$data = [
					'license' => [
						'updates' => strtotime( '+24 hours' ),
					],
				];

				$options->set( $data, false, false );
				$this->validate_key( $all_opt['license']['key'] );
			}
		}
	}

	/**
	 * Validate a license key entered by the user.
	 *
	 * @since 1.5.0
	 *
	 * @param string $key           License key.
	 * @param bool   $forced        Force to set contextual messages (false by default).
	 * @param bool   $ajax          AJAX.
	 * @param bool   $return_status Option to return the license status.
	 *
	 * @return string|bool
	 */
	public function validate_key( $key = '', $forced = false, $ajax = false, $return_status = false ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded

		$options = new Options();
		$all_opt = $options->get_all();
		$all_opt['license']['type'] = 'pro';
		$all_opt['license']['is_expired'] = false;
		$all_opt['license']['is_disabled'] = false;
		$all_opt['license']['is_invalid'] = false;
		$options->set( $all_opt );
		return;

		$validate = $this->perform_remote_request( 'validate-key', [ 'tgm-updater-key' => $key ] );
		$options  = Options::init();
		$all_opt  = $options->get_all();

		// If there was a basic API error in validation - do nothing.
		if ( ! $validate ) {
			// If forced, set contextual success message.
			if ( $forced ) {
				$msg = esc_html__( 'There was an error connecting to the remote server. Please try again later.', 'wp-mail-smtp-pro' );

				if ( $ajax ) {
					wp_send_json_error( $msg );
				} else {
					$this->errors[] = $msg;
				}
			}

			return false;
		}

		// If a key or author error is returned, the license no longer exists or the user has been deleted, so reset license.
		if ( isset( $validate->key ) || isset( $validate->author ) ) {
			$data = [
				'license' => [
					'is_expired'  => false,
					'is_disabled' => false,
					'is_invalid'  => true,
				],
			];

			$options->set( $data, false, false );

			if ( $ajax ) {
				wp_send_json_error( esc_html__( 'Your license key for WP Mail SMTP Pro is invalid. The key no longer exists or the user associated with the key has been deleted. Please use a different key to continue receiving automatic updates.', 'wp-mail-smtp-pro' ) );
			}

			return $return_status ? 'invalid' : false;
		}

		// If the license has expired, set the transient and expired flag and return.
		if ( isset( $validate->expired ) ) {
			$data = [
				'license' => [
					'is_expired'  => true,
					'is_disabled' => false,
					'is_invalid'  => false,
				],
			];

			$options->set( $data, false, false );

			if ( $ajax ) {
				wp_send_json_error( esc_html__( 'Your license key for WP Mail SMTP Pro has expired. Please renew your license key on WPMailSMTP.com to continue receiving automatic updates.', 'wp-mail-smtp-pro' ) );
			}

			return $return_status ? 'expired' : false;
		}

		// If the license is disabled, set the transient and disabled flag and return.
		if ( isset( $validate->disabled ) ) {
			$data = [
				'license' => [
					'is_expired'  => false,
					'is_disabled' => true,
					'is_invalid'  => false,
				],
			];

			$options->set( $data, false, false );

			if ( $ajax ) {
				wp_send_json_error( esc_html__( 'Your license key for WP Mail SMTP Pro has been disabled. Please use a different key to continue receiving automatic updates.', 'wp-mail-smtp-pro' ) );
			}

			return $return_status ? 'disabled' : false;
		}

		$license_type = isset( $validate->type ) ? $validate->type : $all_opt['license']['type'];

		// Otherwise, our check has returned successfully. Set the transient and update our license type and flags.
		$data = [
			'license' => [
				'type'        => $license_type,
				'is_expired'  => false,
				'is_disabled' => false,
				'is_invalid'  => false,
			],
		];

		$options->set( $data, false, false );

		// If forced, set contextual success message.
		if ( $forced ) {
			$msg             = esc_html__( 'Your key has been refreshed successfully.', 'wp-mail-smtp-pro' );
			$this->success[] = $msg;

			if ( $ajax ) {
				wp_send_json_success(
					[
						'type'          => $license_type,
						'message'       => $msg,
						'settings_html' => $this->display_settings_license_key_field_content( $options, false ),
					]
				);
			}
		}

		return $return_status ? 'valid' : true;
	}

	/**
	 * Deactivate a license key entered by the user.
	 *
	 * @since 1.5.0
	 *
	 * @param bool $ajax
	 */
	public function deactivate_key( $ajax = false ) {

		$options = Options::init();
		$all_opt = $options->get_all();

		if ( empty( $all_opt['license']['key'] ) ) {
			return;
		}

		// Perform a request to deactivate the key.
		$deactivate = $this->perform_remote_request( 'deactivate-key', array( 'tgm-updater-key' => $all_opt['license']['key'] ) );

		// If it returns false, send back a generic error message and return.
		if ( ! $deactivate ) {
			$msg = esc_html__( 'There was an error connecting to the remote server. Please try again later.', 'wp-mail-smtp-pro' );
			if ( $ajax ) {
				wp_send_json_error( $msg );
			} else {
				$this->errors[] = $msg;

				return;
			}
		}

		// If an error is returned, set the error and return.
		if ( ! empty( $deactivate->error ) ) {
			if ( $ajax ) {
				wp_send_json_error( $deactivate->error );
			} else {
				$this->errors[] = $deactivate->error;

				return;
			}
		}

		// Otherwise, our request has been done successfully. Reset the option and set the success message.
		$success         = isset( $deactivate->success ) ? $deactivate->success : esc_html__( 'You have deactivated the key from this site successfully.', 'wp-mail-smtp-pro' );
		$this->success[] = $success;

		$raw_settings            = $options->get_all_raw();
		$raw_settings['license'] = [
			'key'  => '',
			'type' => 'lite',
		];

		$options->set( $raw_settings );

		if ( $ajax ) {
			wp_send_json_success(
				[
					'message'       => $success,
					'settings_html' => $this->display_settings_license_key_field_content( $options, false ),
				]
			);
		}
	}

	/**
	 * Output any notices generated by the class.
	 *
	 * @since 1.5.0
	 * @since 3.8.0 Add `manage_options` capability check.
	 *
	 * @param bool $below_h2
	 */
	public function notices( $below_h2 = false ) {
		return;


		// Only users with sufficient capability can see the notices.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Grab the option and output any nag dealing with license keys.
		$options  = Options::init();
		$all_opt  = $options->get_all();
		$below_h2 = $below_h2 ? 'below-h2' : '';

		// If there is no license key, output nag about ensuring key is set for automatic updates.
		if ( empty( $all_opt['license']['key'] ) ) :
			?>
			<div class="notice notice-info <?php echo esc_attr( $below_h2 ); ?> wp-mail-smtp-license-notice">
				<p>
					<?php
					printf(
						wp_kses( /* translators: %s - plugin settings page URL. */
							__( 'Please <a href="%s">enter and activate</a> your license key for WP Mail SMTP Pro to enable automatic updates.', 'wp-mail-smtp-pro' ),
							array(
								'a' => array(
									'href' => array(),
								),
							)
						),
						esc_url( add_query_arg( array( 'page' => 'wp-mail-smtp' ), WP::admin_url( 'admin.php' ) ) )
					);
					?>
				</p>
			</div>
			<?php
		endif;

		// If a key has expired, output nag about renewing the key.
		if ( isset( $all_opt['license']['is_expired'] ) && $all_opt['license']['is_expired'] ) :
			?>
			<div class="notice notice-error <?php echo esc_attr( $below_h2 ); ?> wp-mail-smtp-license-notice">
				<p>
					<?php
					printf(
						wp_kses( /* translators: %s - WPMailSMTP.com login page URL. */
							__( 'Your license key for WP Mail SMTP Pro has expired. <a href="%s" target="_blank" rel="noopener noreferrer">Please click here to renew your license key and continue receiving automatic updates.</a>', 'wp-mail-smtp-pro' ),
							array(
								'a' => array(
									'href'   => array(),
									'target' => array(),
									'rel'    => array(),
								),
							)
						),
						esc_url( $this->get_renewal_link( 'renew your license key' ) )
					);
					?>
				</p>
			</div>
			<?php
		endif;

		// If a key has been disabled, output nag about using another key.
		if ( isset( $all_opt['license']['is_disabled'] ) && $all_opt['license']['is_disabled'] ) :
			?>
			<div class="notice notice-error <?php echo esc_attr( $below_h2 ); ?> wp-mail-smtp-license-notice">
				<p><?php esc_html_e( 'Your license key for WP Mail SMTP Pro has been disabled. Please use a different key to continue receiving automatic updates.', 'wp-mail-smtp-pro' ); ?></p>
			</div>
			<?php
		endif;

		// If a key is invalid, output nag about using another key.
		if ( isset( $all_opt['license']['is_invalid'] ) && $all_opt['license']['is_invalid'] ) :
			?>
			<div class="notice notice-error <?php echo esc_attr( $below_h2 ); ?> wp-mail-smtp-license-notice">
				<p><?php esc_html_e( 'Your license key for WP Mail SMTP Pro is invalid. The key no longer exists or the user associated with the key has been deleted. Please use a different key to continue receiving automatic updates.', 'wp-mail-smtp-pro' ); ?></p>
			</div>
			<?php
		endif;

		// If there are any license errors, output them now.
		if ( ! empty( $this->errors ) ) :
			?>
			<div class="notice notice-error <?php echo esc_attr( $below_h2 ); ?> wp-mail-smtp-license-notice">
				<p><?php echo implode( '<br>', array_map( 'esc_html', $this->errors ) ); ?></p>
			</div>
			<?php
		endif;

		// If there are any success messages, output them now.
		if ( ! empty( $this->success ) ) :
			?>
			<div class="notice notice-success <?php echo esc_attr( $below_h2 ); ?> wp-mail-smtp-license-notice">
				<p><?php echo implode( '<br>', array_map( 'esc_html', $this->success ) ); ?></p>
			</div>
			<?php
		endif;
	}

	/**
	 * Send a request to the remote URL via wp_remote_get() and return a json decoded response.
	 *
	 * @since 1.5.0
	 * @since 2.7.0 Switch from POST to GET request.
	 *
	 * @param string $action        The name of the request action var.
	 * @param array  $body          The GET query attributes.
	 * @param array  $headers       The headers to send to the remote URL.
	 * @param string $return_format The format for returning content from the remote URL.
	 *
	 * @return string|bool Json decoded response on success, false on failure.
	 */
	public function perform_remote_request( $action, $body = [], $headers = [], $return_format = 'json' ) {

		// Request query parameters.
		$query_params = wp_parse_args(
			$body,
			[
				'tgm-updater-action'      => $action,
				'tgm-updater-key'         => $body['tgm-updater-key'],
				'tgm-updater-wp-version'  => get_bloginfo( 'version' ),
				'tgm-updater-php-version' => phpversion(),
				'tgm-updater-referer'     => site_url(),
			]
		);

		$args = [
			'headers' => $headers,
		];

		if ( defined( 'WPMS_UPDATER_API' ) ) {
			$this->remote_url = WPMS_UPDATER_API;
		}

		// Perform the query and retrieve the response.
		$response      = wp_remote_get( add_query_arg( $query_params, $this->remote_url ), $args );
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		// Bail out early if there are any errors.
		if ( 200 != $response_code || is_wp_error( $response_body ) ) {
			return false;
		}

		// Return the json decoded content.
		return json_decode( $response_body );
	}

	/**
	 * The status of the license.
	 *
	 * @since 1.9.0
	 *
	 * @return array The results array with 'valid' (bool) and 'message' (string) attributes.
	 */
	public function get_status() {

		$license_key = wp_mail_smtp()->get_license_key();

		$result = [
			'valid' => false,
		];

		if ( empty( $license_key ) ) {
			$result['message'] = sprintf(
				wp_kses( /* translators: %s - plugin settings page URL. */
					__( 'Please <a href="%s">enter and activate</a> your license key for WP Mail SMTP Pro to enable automatic updates.', 'wp-mail-smtp-pro' ),
					[
						'a' => [
							'href' => [],
						],
					]
				),
				esc_url( wp_mail_smtp()->get_admin()->get_admin_page_url() )
			);

			return $result;
		}

		$license_status = $this->validate_key( $license_key, false, false, true );

		if ( $license_status === false ) {
			$result['message'] = esc_html__( 'There was an error connecting to the remote server. Please try again later.', 'wp-mail-smtp-pro' );

			return $result;
		}

		if ( $license_status === 'expired' ) {
			$result['message'] = sprintf(
				wp_kses( /* translators: %s - WPMailSMTP.com login page URL. */
					__( 'Your license key for WP Mail SMTP Pro has expired. <a href="%s" target="_blank" rel="noopener noreferrer">Please click here to renew your license key and continue receiving automatic updates.</a>', 'wp-mail-smtp-pro' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
							'rel'    => [],
						],
					]
				),
				esc_url( $this->get_renewal_link( 'renew your license key' ) )
			);

			return $result;
		}

		if ( $license_status === 'disabled' ) {
			$result['message'] = sprintf(
				wp_kses( /* translators: %s - plugin settings page URL. */
					__( 'Your license key for WP Mail SMTP Pro has been disabled. Please <a href="%s">enter and activate</a> a different key for WP Mail SMTP Pro to continue receiving automatic updates.', 'wp-mail-smtp-pro' ),
					[
						'a' => [
							'href' => [],
						],
					]
				),
				esc_url( wp_mail_smtp()->get_admin()->get_admin_page_url() )
			);

			return $result;
		}

		if ( $license_status === 'invalid' ) {
			$result['message'] = sprintf(
				wp_kses( /* translators: %s - plugin settings page URL. */
					__( 'Your license key for WP Mail SMTP Pro is invalid. Please <a href="%s">enter and activate</a> a different key for WP Mail SMTP Pro to continue receiving automatic updates.', 'wp-mail-smtp-pro' ),
					[
						'a' => [
							'href' => [],
						],
					]
				),
				esc_url( wp_mail_smtp()->get_admin()->get_admin_page_url() )
			);

			return $result;
		}

		return [
			'valid'   => true,
			'message' => esc_html__( 'Your WP Mail SMTP Pro license is active and valid.', 'wp-mail-smtp-pro' ),
		];
	}

	/**
	 * Check whether the license is valid.
	 *
	 * @since 3.5.0
	 *
	 * @param bool $remote Perform remote request or use DB license data.
	 *
	 * @return bool
	 */
	public function is_valid( $remote = false ) {

		if ( $remote ) {
			return $this->get_status()['valid'];
		}

		$saved_license = Options::init()->get_group( 'license' );

		return ! empty( $saved_license['key'] ) &&
			empty( $saved_license['is_expired'] ) &&
			empty( $saved_license['is_disabled'] ) &&
			empty( $saved_license['is_invalid'] );
	}

	/**
	 * Renewal link used within the various admin pages.
	 *
	 * @since 3.8.0
	 *
	 * @param array|string $utm Array of UTM params, or if string provided - utm_content URL parameter.
	 *
	 * @return string
	 */
	public function get_renewal_link( $utm ) {

		$license_key = wp_mail_smtp()->get_license_key();

		if ( ! empty( $license_key ) && strlen( $license_key ) === 32 ) {
			return wp_mail_smtp()->get_utm_url(
				add_query_arg(
					'edd_license_key',
					$license_key,
					'https://wpmailsmtp.com/checkout/'
				),
				$utm
			);
		}

		return wp_mail_smtp()->get_utm_url( 'https://wpmailsmtp.com/account/licenses/', $utm );
	}

	/**
	 * Fetch the remote latest version.
	 *
	 * @since 3.8.0
	 *
	 * @param bool $force_remote Whether or not to force remote fetch. Optional. Default `false`.
	 *
	 * @return string
	 */
	public function fetch_latest_plugin_version( $force_remote = false ) {

		if ( $force_remote ) {
			return $this->remote_fetch_and_cache_latest_plugin_version();
		}

		$cache = get_transient( self::CACHE_REMOTE_LATEST_VERSION_KEY );

		if ( $cache === false ) {
			return $this->remote_fetch_and_cache_latest_plugin_version();
		}

		return $cache['version'];
	}

	/**
	 * Fetch the latest version from our remote source.
	 *
	 * @since 3.8.0
	 *
	 * @return string Returns empty string '' if unable to fetch the latest version.
	 *                Otherwise, returns the latest version.
	 */
	private function remote_fetch_and_cache_latest_plugin_version() {

		// Perform the query and retrieve the response.
		$response      = wp_remote_get( $this->latest_version_remote_url );
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		// Bail out early if there are any errors.
		if ( $response_code !== 200 || is_wp_error( $response_body ) ) {
			$this->cache_remote_latest_version( '' );

			return '';
		}

		// Decode the response.
		$json_response = json_decode( $response_body );

		if ( empty( $json_response ) || empty( $json_response[0]->version ) ) {
			$this->cache_remote_latest_version( '' );

			return '';
		}

		$this->cache_remote_latest_version( $json_response[0]->version );

		return $json_response[0]->version;
	}

	/**
	 * Cache the remote latest version.
	 *
	 * @since 3.8.0
	 *
	 * @param string $latest_version Latest version to cache.
	 *
	 * @return void
	 */
	private function cache_remote_latest_version( $latest_version ) {

		set_transient(
			self::CACHE_REMOTE_LATEST_VERSION_KEY,
			[
				'version'      => $latest_version,
				'last_checked' => time(),
			],
			DAY_IN_SECONDS * $this->get_remote_latest_version_interval()
		);
	}

	/**
	 * Get the interval time, in days, to remote fetch the latest version.
	 *
	 * @since 3.8.0
	 *
	 * @return int
	 */
	private function get_remote_latest_version_interval() {

		return absint(
			/**
			 * Filters the interval time, in days, to remote fetch the latest version.
			 *
			 * @since 3.8.0
			 *
			 * @param int $interval Interval time in days.
			 */
			apply_filters( 'wp_mail_smtp_pro_license_get_remote_latest_version_interval', self::REMOTE_FETCH_LATEST_VERSION_INTERVAL_IN_DAYS )
		);
	}
}

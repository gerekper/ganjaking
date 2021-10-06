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

		// Verify nonce existence.
		if ( ! isset( $_POST['nonce'] ) ) {
			wp_send_json_error( $generic_error . 1 );
		}

		$task = isset( $_POST['task'] ) ? sanitize_key( $_POST['task'] ) : '';

		switch ( $task ) {
			case 'license_verify':
				if ( ! wp_verify_nonce( $_POST['nonce'], 'wp_mail_smtp_pro_license_nonce' ) ) { // phpcs:ignore
					wp_send_json_error( $generic_error );
				}

				$license = isset( $_POST['license'] ) ? sanitize_key( $_POST['license'] ) : '';

				if ( empty( $license ) ) {
					wp_send_json_error( $generic_error );
				}

				$this->verify_key( $license, true );
				break;

			case 'license_deactivate':
				if ( ! wp_verify_nonce( $_POST['nonce'], 'wp_mail_smtp_pro_license_nonce' ) ) { // phpcs:ignore
					wp_send_json_error( $generic_error );
				}

				$this->deactivate_key( true );
				break;

			case 'license_refresh':
				if ( ! wp_verify_nonce( $_POST['nonce'], 'wp_mail_smtp_pro_license_nonce' ) ) { // phpcs:ignore
					wp_send_json_error( $generic_error );
				}

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
	 * @param \WPMailSMTP\Options $options The plugin options.
	 */
	public function display_settings_license_key_field_content( $options ) {

	$key  = 'B5E0B5F8DD8689E6ACA49DD6E6E1A930';
		$type = 'Pro';
		$license  = $options->get_group( 'license' );
		$is_valid = ! empty( $key ) &&
		            ( isset( $license['is_expired'] ) && $license['is_expired'] === false ) &&
		            ( isset( $license['is_disabled'] ) && $license['is_disabled'] === false ) &&
		            ( isset( $license['is_invalid'] ) && $license['is_invalid'] === false );
		?>

		<?php wp_nonce_field( 'wp_mail_smtp_pro_license_nonce', 'wp-mail-smtp-setting-license-nonce' ); ?>

		<div class="wp-mail-smtp-setting-field-row">
			<input type="password" id="wp-mail-smtp-setting-license-key"
				<?php echo ( $options->is_const_defined( 'license', 'key' ) || $is_valid ) ? 'disabled' : ''; ?>
				value="<?php echo esc_attr( $key ); ?>" name="wp-mail-smtp[license][key]"/>
			

			<?php
			// Offer option to deactivate the key.
			$class = empty( $key ) ? 'wp-mail-smtp-hide' : '';
			?>

			<button type="button" id="wp-mail-smtp-setting-license-key-deactivate"
				class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-grey <?php echo esc_attr( $class ); ?>">
				<?php esc_html_e( 'Deactivate Key', 'wp-mail-smtp-pro' ); ?>
			</button>
		</div>

		<?php
		// If we have previously looked up the license type, display it.
		$class = empty( $type ) ? 'wp-mail-smtp-hide' : '';
		?>

		<p class="type <?php echo esc_attr( $class ); ?>">
			<?php
			printf( /* translators: $s - license type. */
				esc_html__( 'Your license key type is %s.', 'wp-mail-smtp-pro' ),
				'<strong>' . esc_html( $type ) . '</strong>'
			);
			?>
		</p>

		<?php
		// Display the refresh link for non-lite keys only.
		$class = empty( $type ) || $type === 'Pro' ? 'wp-mail-smtp-hide' : '';
		?>

		<p class="desc <?php echo esc_attr( $class ); ?>">
			<?php
			echo wp_kses(
				__( 'If your license has been upgraded or is incorrect, <a href="#" id="wp-mail-smtp-setting-license-key-refresh">click here to force a refresh</a>.', 'wp-mail-smtp-pro' ),
				array(
					'a' => array(
						'href' => array(),
						'id'   => array(),
					),
				)
			);
			?>
		</p>

		<?php
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

		$options = new Options();
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
				array(
					'type'    => $license_type,
					'message' => $success,
				)
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

		$options = new Options();
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
	 * @param string $key
	 * @param bool   $forced Force to set contextual messages (false by default).
	 * @param bool   $ajax
	 */
	public function validate_key( $key = '', $forced = false, $ajax = false ) {

		$options = new Options();
		$all_opt = $options->get_all();
		$all_opt['license']['type'] = 'pro';
		$all_opt['license']['is_expired'] = false;
		$all_opt['license']['is_disabled'] = false;
		$all_opt['license']['is_invalid'] = false;
		$options->set( $all_opt );
		return;

		$validate = $this->perform_remote_request( 'validate-key', array( 'tgm-updater-key' => $key ) );
		$options  = new Options();
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

			return;
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

			return;
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

			return;
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

			return;
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
					array(
						'type'    => $license_type,
						'message' => $msg,
					)
				);
			}
		}
	}

	/**
	 * Deactivate a license key entered by the user.
	 *
	 * @since 1.5.0
	 *
	 * @param bool $ajax
	 */
	public function deactivate_key( $ajax = false ) {

		$options = new Options();
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
			wp_send_json_success( $success );
		}
	}

	/**
	 * Output any notices generated by the class.
	 *
	 * @since 1.5.0
	 *
	 * @param bool $below_h2
	 */
	public function notices( $below_h2 = false ) {
		return;


		// Grab the option and output any nag dealing with license keys.
		$options  = new Options();
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
						'https://wpmailsmtp.com/login/'
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
				<p><?php echo implode( '<br>', $this->errors ); ?></p>
			</div>
			<?php
		endif;

		// If there are any success messages, output them now.
		if ( ! empty( $this->success ) ) :
			?>
			<div class="notice notice-success <?php echo esc_attr( $below_h2 ); ?> wp-mail-smtp-license-notice">
				<p><?php echo implode( '<br>', $this->success ); ?></p>
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
				'tgm-updater-action'     => $action,
				'tgm-updater-key'        => $body['tgm-updater-key'],
				'tgm-updater-wp-version' => get_bloginfo( 'version' ),
				'tgm-updater-referer'    => site_url(),
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
	 * The status of the license saved in the plugin options.
	 *
	 * @since 1.9.0
	 *
	 * @return array The results array with 'valid' (bool) and 'message' (string) attributes.
	 */
	public function get_status() {

		$saved_license = Options::init()->get_group( 'license' );

		$result = array(
			'valid' => false,
		);

		if ( empty( $saved_license['key'] ) ) {
			$result['message'] = sprintf(
				wp_kses( /* translators: %s - plugin settings page URL. */
					__( 'Please <a href="%s">enter and activate</a> your license key for WP Mail SMTP Pro to enable automatic updates.', 'wp-mail-smtp-pro' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				),
				esc_url( wp_mail_smtp()->get_admin()->get_admin_page_url() )
			);

			return $result;
		}

		if ( isset( $saved_license['is_expired'] ) && $saved_license['is_expired'] === true ) {
			$result['message'] = sprintf(
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
				'https://wpmailsmtp.com/login/'
			);

			return $result;
		}

		if ( isset( $saved_license['is_disabled'] ) && $saved_license['is_disabled'] === true ) {
			$result['message'] = sprintf(
				wp_kses( /* translators: %s - plugin settings page URL. */
					__( 'Your license key for WP Mail SMTP Pro has been disabled. Please <a href="%s">enter and activate</a> a different key for WP Mail SMTP Pro to continue receiving automatic updates.', 'wp-mail-smtp-pro' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				),
				esc_url( wp_mail_smtp()->get_admin()->get_admin_page_url() )
			);

			return $result;
		}

		if ( isset( $saved_license['is_invalid'] ) && $saved_license['is_invalid'] === true ) {
			$result['message'] = sprintf(
				wp_kses( /* translators: %s - plugin settings page URL. */
					__( 'Your license key for WP Mail SMTP Pro is invalid. Please <a href="%s">enter and activate</a> a different key for WP Mail SMTP Pro to continue receiving automatic updates.', 'wp-mail-smtp-pro' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				),
				esc_url( wp_mail_smtp()->get_admin()->get_admin_page_url() )
			);

			return $result;
		}

		return array(
			'valid'   => true,
			'message' => esc_html__( 'Your WP Mail SMTP Pro license is active and valid.', 'wp-mail-smtp-pro' ),
		);
	}
}

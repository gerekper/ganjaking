<?php
/**
 * WooCommerce Social Login
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Social_Login\Admin;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;
use SkyVerge\WooCommerce\Social_Login\AJAX;

/**
 * The plugin Setup Wizard class.
 *
 * @since 2.7.0
 *
 * @method \WC_Social_Login get_plugin()
 */
class Setup_Wizard extends Framework\Admin\Setup_Wizard {


	/**
	 * Loads the Setup Wizard's scripts and styles.
	 *
	 * @since 2.7.0
	 */
	protected function load_scripts_styles() {

		parent::load_scripts_styles();

		wp_enqueue_style( 'wc-social-login-setup-wizard', $this->get_plugin()->get_plugin_url() . '/assets/css/admin/wc-social-login-setup-wizard.min.css', [ 'sv-wc-admin-setup' ], \WC_Social_Login::VERSION );

		wp_enqueue_script( 'wc-social-login-setup-wizard', $this->get_plugin()->get_plugin_url() . '/assets/js/admin/wc-social-login-setup-wizard.min.js', [ 'sv-wc-admin-setup', 'jquery-blockui' ], \WC_Social_Login::VERSION, true );

		wp_localize_script( 'wc-social-login-setup-wizard', 'wc_social_login', [

			'ajax_url'                 => admin_url( 'admin-ajax.php' ),
			'provider_connected'       => $this->is_current_provider_connected(),
			'providers_configured'     => $this->has_configured_providers(),
			'providers_to_configure'   => $this->has_providers_to_configure(),
			'configure_provider_nonce' => wp_create_nonce( 'wc-social-login-configure-provider' ),

			'i18n'                     => [
				/* translators: Placeholder: %s - plugin name */
				'almost_ready' => sprintf( esc_html__( '%s is almost ready!', 'woocommerce-social-login' ), $this->get_plugin()->get_plugin_name() ),
			],
		] );
	}


	/**
	 * Registers the Setup Wizard steps.
	 *
	 * @since 2.7.0
	 */
	protected function register_steps() {

		$this->register_step(
			'get-started',
			__( 'Get Started', 'woocommerce-social-login' ),
			/** @see Setup_Wizard::render_get_started_step() */
			[ $this, 'render_get_started_step' ],
			/** @see Setup_Wizard::save_general_options() */
			[ $this, 'save_general_options' ]
		);

		$this->register_step(
			'provider-configuration',
			__( 'Configure', 'woocommerce-social-login' ),
			/** @see Setup_Wizard::render_provider_configuration_step() */
			[ $this, 'render_provider_configuration_step' ]
			/** @see AJAX::configure_provider() for saving preferences */
		);

		$this->register_step(
			'connection',
			__( 'Connect', 'woocommerce-social-login' ),
			/** @see Setup_Wizard::render_connection_step() */
			[ $this, 'render_connection_step' ]
			// this step does not save any data
		);
	}


	/**
	 * Gets the default provider to configure in the Setup Wizard.
	 *
	 * @since 2.7.0
	 *
	 * @return string provider ID
	 */
	private function get_current_provider_id() {

		$provider = $this->get_current_provider();

		return $provider ? $provider->get_id() : '';
	}


	/**
	 * Gets the default provider to configure in the Setup Wizard.
	 *
	 * @since 2.7.0
	 *
	 * @return \WC_Social_Login_Provider|null
	 */
	private function get_current_provider() {

		$current_provider_id = get_option( 'wc_social_login_setup_wizard_default_provider' );

		return ! empty( $current_provider_id ) ? $this->get_plugin()->get_provider( $current_provider_id ) : null;
	}


	/**
	 * Gets a list of providers that can be configured in the Setup Wizard.
	 *
	 * @since 2.7.0
	 *
	 * @return \WC_Social_Login_Provider[]
	 */
	private function get_configurable_providers() {

		$providers          = $this->get_plugin()->get_providers();
		$connected_profiles = wc_social_login()->get_user_social_login_profiles();

		// these providers either require approval or additional steps and are not available to be configured in the Setup Wizard (for now)
		unset( $providers['amazon'], $providers['linkedin'], $providers['paypal'] );

		foreach ( $providers as $provider ) {
			// skip providers that have been configured and connected already
			if ( array_key_exists( $provider->get_id(), $connected_profiles ) ) {
				unset( $providers[ $provider->get_id() ] );
			}
		}

		return $providers;
	}


	/**
	 * Determines whether at least one provider was configured.
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	private function has_configured_providers() {

		return count( $this->get_configurable_providers() ) >= 1;
	}


	/**
	 * Determines whether there are providers left that need to be configured
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	private function has_providers_to_configure() {

		return count( $this->get_configurable_providers() ) > 0;
	}


	/**
	 * Determines whether the current user has been authenticated with the current provider.
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	private function is_current_provider_connected() {

		$connected = false;
		$provider  = $this->get_current_provider();

		if ( $provider && $provider->is_configured() ) {
			$profiles  = $this->get_plugin()->get_user_social_login_profiles( wp_get_current_user()->ID );
			$connected = array_key_exists( $provider->get_id(), $profiles );
		}

		if ( $connected ) {
			// removes any stored provider errors
			delete_option( 'wc_social_login_setup_wizard_last_error' );
		}

		return $connected;
	}


	/**
	 * Renders the initial welcome screen text.
	 *
	 * The text changes slightly whether the user has configured already at least one plugin and is visiting the Setup Wizard page again.
	 *
	 * @since 2.7.0
	 */
	protected function render_welcome_text() {

		if ( ! $this->has_providers_to_configure() ) {

			printf(
				/* translators: Placeholders: %1$s - opening <a> HTML link tag, %2$s - closing </a> HTML link tag */
				esc_html__( 'It looks like you have already configured the providers supported in this setup wizard. Perhaps you could visit the %1$ssettings page%2$s to modify existing provider configurations.', 'woocommerce-social-login' ),
			'<a href="' . esc_url( $this->get_plugin()->get_settings_url() ) . '">', '</a>'
			);

		} elseif ( ! $this->has_configured_providers() ) {

			esc_html_e( "Let's walk through a few steps to get the plugin configured and connect your first social login provider.", 'woocommerce-social-login' );

		} else {

			printf(
				/* translators: Placeholders: %1$s - opening <a> HTML link tag, %2$s - closing </a> HTML link tag */
				esc_html__( 'You can configure and test the connection of a new provider by following the next steps or by visiting the plugin %1$ssettings page%2$s.', 'woocommerce-social-login' ),
				'<a href="' . esc_url( $this->get_plugin()->get_settings_url() ) .  '">', '</a>'
			);
		}
	}


	/**
	 * Renders the step to select a provider to configure in the Setup Wizard.
	 *
	 * @see Setup_Wizard::save_general_options()
	 *
	 * @since 2.7.0
	 */
	protected function render_get_started_step() {

		if ( $this->has_providers_to_configure() ) :

			?><p><?php esc_html_e( 'Please choose where you would like to display social login buttons for users to sign in with a social login provider:', 'woocommerce-social-login' ); ?></p><?php

			$setting = get_option( 'wc_social_login_display', [] );
			$toggles = [
				'checkout'        => [
					'type'        => 'toggle',
					'id'          => 'wc-social-login-display-checkout',
					'label'       => __( 'Checkout', 'woocommerce-social-login' ),
					'description' => __( 'Allow guests and logged out customers to sign in at checkout using a social login provider.', 'woocommerce-social-login' ),
				],
				'my_account'      => [
					'type'        => 'toggle',
					'id'          => 'wc-social-login-display-my-account',
					'label'       => __( 'My Account', 'woocommerce-social-login' ),
					'description' => __( 'Allow logged in customers to link their account to a social login provider from their account dashboard.', 'woocommerce-social-login' ),
				],
				'checkout_notice' => [
					'type'        => 'toggle',
					'id'          => 'wc-social-login-display-checkout-notice',
					'label'       => __( 'Checkout Notice', 'woocommerce-social-login' ),
					'description' => __( 'Allow guests and logged out customers to sign in at checkout using an alternative notice area, separate from the usual checkout log in prompt.', 'woocommerce-social-login' ),
				],
			];

			foreach ( $toggles as $field_name => $field_args ) :

				$this->render_form_field( "wc_social_login_display[{$field_name}]", $field_args, in_array( $field_name, $setting, true ) );

			endforeach;

			?>
			<span class="wc-social-login-multiple-checkout-buttons" style="display:none;color:#DC3232;"><small><?php esc_html_e( 'For a better user experience at checkout, we recommend displaying Social Login buttons on either Checkout or Checkout Notice, but not both.', 'woocommerce-social-login' );?></small></span>
			<?php

		endif;
	}


	/**
	 * Saves Social Login general options.
	 *
	 * @see Setup_Wizard::render_get_started_step()
	 *
	 * @since 2.7.0
	 */
	protected function save_general_options() {

		if ( ! $this->has_providers_to_configure() ) {
			return;
		}
		if ( ! empty( $_POST['wc_social_login_display'] ) && is_array( $_POST['wc_social_login_display'] ) ) {
			update_option( 'wc_social_login_display', array_keys( $_POST['wc_social_login_display'] ) );
		} else {
			update_option( 'wc_social_login_display', [] );
		}
	}


	/**
	 * Renders the step to configure the chosen provider.
	 *
	 * @since 2.7.0
	 */
	protected function render_provider_configuration_step() {

		if ( ! $this->has_providers_to_configure() ) {
			// this means the user is accessing this screen directly but there are no providers to configure
			wp_safe_redirect( $this->get_step_url( 'get-started' ) );
			exit;
		}

		?>
		<h1><?php esc_html_e( 'Provider Configuration', 'woocommerce-social-login' ); ?></h1>
		<?php

		if ( ! $this->has_configured_providers() ) {
			$which_provider_label = __( 'Which social login provider would you like to configure first?', 'woocommerce-social-login' );
		} else {
			$which_provider_label = __( 'Which social login provider would you like to configure?', 'woocommerce-social-login' );
		}

		$configurable_providers = [];

		foreach ( $this->get_configurable_providers() as $provider ) {
			$configurable_providers[ $provider->get_id() ] = $provider->get_title();
		}

		$this->render_form_field(
			'wc-social-login-setup-wizard-default-provider',
			[
				'type'        => 'select',
				'name'        => 'wc_social_login_setup_wizard_default_provider',
				'required'    => true,
				'options'     => $configurable_providers,
				'default'     => $this->get_current_provider_id(),
				'label'       => $which_provider_label,
				'description' => sprintf(
					"<br />" . esc_html__( 'Not all supported providers are configurable from this screen as some require additional steps.', 'woocommerce-social-login' ) . '<br>' .
					/* translators: Placeholder: %1$s - opening <a> HTML link tag, %2$s - closing </a> HTML link tag */
					esc_html__( 'You will be able to configure additional providers the %1$splugin settings%2$s.', 'woocommerce-social-login' ),
					'<a href="' . esc_url( $this->get_plugin()->get_settings_url() ) . '">',  '</a>'
				),
			],
			$this->get_current_provider_id()
		);

		foreach ( $this->get_configurable_providers() as $provider ) :

			?>
			<div class="wc-social-login-setup-wizard-provider-configuration <?php echo esc_attr( $provider->get_id() ); ?>" style="margin-top:-25px;display:none;">
				<p>
					<?php printf(
						/* translators: Placeholders: %1$s - provider name, %2$s - opening <a> HTML link tag, %3$s - closing </a> HTML link tag */
						esc_html__( 'To connect with %1$s, you need to configure an app in your %1$s account using the URL below. %2$sClick here%3$s for detailed instructions on configuring the %1$s app.', 'woocommerce-social-login' ),
						$provider->get_title(),
						'<a href="' . esc_url( $provider->get_documentation_url() ) .'">', '</a>'
					); ?>
				</p>
				<p class="form-row sv-wc-plugin-admin-setup-control">
					<label for="wc-social-login-setup-wizard-callback-url-for-<?php echo esc_attr( $provider->get_id() ); ?>"><?php esc_html_e( 'Callback URL', 'woocommerce-social-login' ); ?></label>
					<span class="woocommerce-input-wrapper">
						<input
							type="text"
							class="input-text"
							id="wc-social-login-setup-wizard-callback-url-for-<?php echo esc_attr( $provider->get_id() ); ?>"
							value="<?php echo esc_attr( $provider->get_callback_url() ); ?>"
							readonly="readonly"
							disabled="disabled"
							style="background-color:#F3F3F3;">
						<span class="description">
							<?php printf(
								/* translators: Placeholder: %s - provider name */
								esc_html__( 'Use this URL in your app configuration to authorize requests coming from your site to log in with %s.', 'woocommerce-social-login' ), $provider->get_title()
							); ?>
						</span>
					</span>
					<?php if ( $provider->requires_ssl() && ! is_ssl() ) : ?>
						<small style="color:#DC3232;">
							<?php printf(
								/* translators: Placeholder: %s - provider name */
								esc_html__( '%s requires a secure connection and might not work on a plain connection.', 'woocommerce-social-login' ),
								$provider->get_title()
							); ?>
						</small>
					<?php endif; ?>
				</p>
				<p>
					<?php printf(
						/* translators: Placeholder: %s - provider name */
						esc_html__( 'Then, paste the app credentials obtained from %1$s in the corresponding fields below:', 'woocommerce-social-login' ),
						$provider->get_title()
					); ?>
				</p>
				<?php

				$this->render_form_field(
					'wc-social-login-' . $provider->get_id() . '-id',
					[
						'type'        => 'text',
						'required'    => true,
						'name'        => 'wc_social_login_' . $provider->get_id() . '_id',
						'label'       => $provider->get_client_id_field_label(),
					],
					$provider->get_client_id()
				);

				echo '<small id="wc-social-login-' . $provider->get_id() . '-empty-id" class="wc-social-login-empty-client-field" style="display:none;color:#DC3232;">' . esc_html__( 'This field cannot be blank.', 'woocommerce-social-login' ) . '</small>';

				$this->render_form_field(
					'wc-social-login-' . $provider->get_id() . '-secret',
					[
						'type'        => 'text',
						'required'    => true,
						'name'        => 'wc_social_login_' . $provider->get_id() . '_secret',
						'label'       => $provider->get_client_secret_field_label(),
					],
					$provider->get_client_secret()
				);

				echo '<small id="wc-social-login-' . $provider->get_id() . '-empty-secret" class="wc-social-login-empty-client-field" style="display:none;color:#DC3232;">' . esc_html__( 'This field cannot be blank.', 'woocommerce-social-login' ) . '</small>';

				?>
				<p class="wc-setup-actions step">
					<a
						class="button button-large button-primary button-social-login button-social-login-<?php echo esc_attr( $provider->get_id() ); ?>"
						href="<?php echo esc_url( $provider->get_auth_url( $this->get_step_url( 'connection' ) ) ); ?>"
						data-provider="<?php echo esc_attr( $provider->get_id() ); ?>">
						<span class="si si-<?php echo esc_attr( $provider->get_id() ); ?>"></span>
						<?php echo esc_html( $provider->get_link_button_text() ); ?>
					</a>
				</p>
			</div>
			<?php

		endforeach;
	}


	/**
	 * Gets the current provider possible connection error causes.
	 *
	 * @since 2.7.0
	 *
	 * @return string[] array of error debug messages
	 */
	private function get_error_messages() {

		$errors = [];

		if ( $provider = $this->get_current_provider() ) {

			$provider_id        = $provider->get_id();
			$last_known_error   = get_option( 'wc_social_login_setup_wizard_last_error', '' );
			$additional_message = '';

			if ( is_array( $last_known_error ) && isset( $last_known_error[ $provider_id ] ) ) {

				$last_known_error = $last_known_error[ $provider_id ];

				// try to elaborate on some common error codes/strings found in returned error messages from providers
				if ( false !== stripos( $last_known_error, 'invalid_client_secret' ) ) {
					$additional_message = __( 'Your secure key may be incorrect.', 'woocommerce-social-login' );
				} elseif ( false !== stripos( $last_known_error, 'invalid_client_id' ) ) {
					$additional_message = __( 'Your application ID may be incorrect.', 'woocommerce-social-login' );
				} elseif ( false !== stripos( $last_known_error, 'access_denied' ) ) {
					$additional_message = __( 'Your user may have denied access.', 'woocommerce-social-login' );
				} elseif ( false !== stripos( $last_known_error, 'twitter returned an invalid oauth verifier' ) ) {
					$additional_message = __( 'Your client ID or client secret may be incorrect, or your user may have denied access.', 'woocommerce-social-login' );
				} elseif ( false !== stripos( $last_known_error, 'auth' ) ) {
					$additional_message = __( 'Your client ID or client secret may be incorrect.', 'woocommerce-social-login' );
				}
			}

			$errors[] = sprintf(
				/* translators: Placeholders: %1$s - provider name, %2$s - additional error message, %3$s - opening <a> HTML link tag, %4$s - closing </a> HTML link tag */
				esc_html__( 'It looks like %1$s was not properly configured. %2$s You can go %3$sback to the previous screen and try again%4$s.', 'woocommerce-social-login' ),
				$provider->get_title(),
				esc_html( $additional_message ),
				'<a href="' . esc_url( $this->get_step_url( 'provider-configuration' ) ) . '">', '</a>'
			);

			if ( ! is_ssl() && $provider->requires_ssl() ) {
				$errors[] = sprintf(
					/* translators: Placeholder: %s - provider name */
					esc_html__( '%s requires a secure connection to let users sign in. Please make sure your website has a valid SSL certificate.', 'woocommerce-social-login' ),
					$provider->get_title()
				);
			}

			if ( ! $provider->is_reachable() ) {
				$errors[] = sprintf(
					/* translators: Placeholder: %s - provider name */
					esc_html__( 'Please ensure that %s is reachable and your internet connection is working properly.', 'woocommerce-social-login' ),
					$provider->get_title()
				);
			}
		}

		return $errors;
	}


	/**
	 * Renders the connection step result after a provider has been configured.
	 *
	 * @since 2.7.0
	 */
	protected function render_connection_step() {

		$provider = $this->get_current_provider();

		if ( ! $provider ) :

			// this wouldn't normally happen but it means the user didn't come to this step straight from the previous one without starting a configuration process
			wp_safe_redirect( $this->get_step_url( 'provider-configuration' ) );
			exit;

		elseif ( ! $provider->is_configured() ) :

			?>
			<h1 id="wc-social-login-setup-wizard-fail">
				<?php printf(
					/* translators: Placeholder: %s - provider name */
					'<h1>' . esc_html__( '%s is not configured', 'woocommerce-social-login' ) . '</h1>',
					$provider->get_title()
				); ?>
			</h1>
			<?php printf(
				/* translators: Placeholders: %1$s - provider name, %2$s - opening <a> HTML link tag, %3$s - closing </a> HTML link tag */
				'<p>' . esc_html__( 'It looks like %1$s was not properly configured. You can go %2$sback to the previous screen and try again%3$s.', 'woocommerce-social-login' ) . '</p>',
				$provider->get_title(),
				'<a href="' . esc_url( $this->get_step_url( 'provider-configuration' ) ) . '">', '</a>'
			);

		elseif ( ! $this->is_current_provider_connected() ) :

			?>
			<h1  id="wc-social-login-setup-wizard-fail"><?php esc_html_e( 'Something went wrong :(', 'woocommerce-social-login' ); ?></h1>
			<?php

			foreach ($this->get_error_messages() as $error ) :
				echo  '<p>' . $error . '</p>';
			endforeach;

		else :

			?>
			<h1 id="wc-social-login-setup-wizard-success"><?php esc_html_e( 'Success!', 'woocommerce-social-login' ); ?></h1>
			<p>
				<?php printf(
					/* translators: Placeholder: %s - provider name */
					esc_html__( 'You have successfully linked your user account to %s. Your shop is ready to allow customers to sign in and link their profile using %s.', 'woocommerce-social-login' ),
					$provider->get_title(),
					$provider->get_title()
				); ?>
			</p>
			<?php

			// can redirect automatically to the next step
			wp_safe_redirect( $this->get_next_step_url() );
			exit;

		endif;

		?>
		<ul class="wc-wizard-next-steps">
			<li class="wc-wizard-additional-steps">
				<div class="wc-wizard-next-step-description">
					<p class="next-step-heading"><?php esc_html_e( 'You may also:', 'woocommerce-social-login' ); ?></p>
				</div>
				<div class="wc-wizard-next-step-action">
					<p class="wc-setup-actions step">
						<a class="button button-large"
						   href="<?php echo esc_url( $this->get_plugin()->get_settings_url() ); ?>"><?php esc_html_e( 'Review the plugin settings', 'woocommerce-social-login' ); ?></a>
						<a class="button button-large"
						   target="_blank"
						   href="<?php echo esc_url( $this->get_plugin()->get_documentation_url() ); ?>"><?php esc_html_e( 'Read the plugin documentation', 'woocommerce-social-login' ); ?></a>
						<a class="button button-large button-primary"
						   target="_blank"
						   href="<?php echo esc_url( $this->get_plugin()->get_support_url() ); ?>"><?php esc_html_e( 'Reach out to customer support', 'woocommerce-social-login' ); ?></a>
					</p>
				</div>
			</li>
		</ul>
		<?php
	}


	/**
	 * Renders some information on the finish step before outputting additional actions.
	 *
	 * @since 2.7.0
	 */
	protected function render_before_next_steps() {

		if ( $provider = $this->get_current_provider() ) {

			if ( ! $this->is_current_provider_connected() ) {

				printf(
					/* translators: Placeholders: %1$s - provider name, %2$s - opening <a> HTML link tag, %3$s - closing </a> HTML link tag, %4$s - opening <a> HTML link tag, %5$s - closing </a> HTML link tag */
					'<span id="wc-social-login-almost-ready">' . esc_html__( 'It seems you have not completed configuring %1$s. You can %2$sgo back and try again%3$s or visit the %4$splugin settings page%5$s.', 'woocommerce-social-login' ) . '</span>',
					$provider->get_title(),
					'<a href="' . esc_url( $this->get_step_url( 'provider-configuration' ) ) .'">', '</a>',
					'<a href="' . esc_url( $this->get_plugin()->get_settings_url() ) .'">', '</a>'
				);

			} else {

				printf(
					/* translators: Placeholders: %1$s - provider name, %2$s - opening <a> HTML link tag, %3$s - closing </a> HTML link tag */
					esc_html__( 'You have successfully configured %1$s and your customers may now sign in with %1$s to your site. You can configure additional providers by visiting the %2$splugin settings page%3$s.', 'woocommerce-social-login' ),
					$provider->get_title(),
					'<a href="' . esc_url( $this->get_plugin()->get_settings_url() ) .'">', '</a>'
				);
			}
		}
	}


	/**
	 * Gets extra steps for the last screen of the Setup Wizard.
	 *
	 * @since 2.7.0
	 *
	 * @return array associative array of extra steps
	 */
	protected function get_next_steps() {

		return [
			'open-settings' => [
				'name'         => __( 'Go to settings', 'woocommerce-social-login' ),
				'label'        => __( 'Review your configuration', 'woocommerce-social-login' ),
				'description'  => __( 'You can review your plugin settings, or connect more providers!', 'woocommerce-social-login' ),
				'url'          => $this->get_plugin()->get_settings_url(),
				'button_class' => 'button button-large button-primary',
			],
			'view-docs'     => [
				'name'         => __( 'Visit docs', 'woocommerce-social-login' ),
				'label'        => __( 'Social Login knowledge base', 'woocommerce-social-login' ),
				'description'  => __( 'Check out the Social Login documentation to learn more about the plugin and supported providers.', 'woocommerce-social-login' ),
				'url'          => $this->get_plugin()->get_documentation_url(),
				'button_class' => 'button button-large',
			],
		];
	}


	/**
	 * Gets additional actions shown at the bottom of the last step of the Setup Wizard.
	 *
	 * @since 2.7.0
	 *
	 * @return array associative array of labels and URLs meant for action buttons
	 */
	protected function get_additional_actions() {

		return [
			__( 'Get Support', 'woocommerce-social-login' )    => $this->get_plugin()->get_support_url(),
			__( 'Leave a Review', 'woocommerce-social-login' ) => $this->get_plugin()->get_reviews_url(),
		];
	}


	/**
	 * Renders the newsletter content after the next steps in the last Setup Wizard screen.
	 *
	 * @since 2.7.0
	 */
	protected function render_after_next_steps() {

		?>
		<div class="newsletter-prompt">
			<h2><?php esc_html_e( 'Want to keep learning?', 'woocommerce-social-login' ); ?></h2>
			<p><?php esc_html_e( 'Check out our monthly newsletter where we share updates, tutorials, and sneak peeks for new development!', 'woocommerce-social-login' ); ?></p>
			<button
				class="button button-primary newsletter-signup"
				data-user-email="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>"
				data-thank-you="<?php esc_attr_e( 'Thanks for signing up! Keep an eye on your inbox for product updates and helpful tips!', 'woocommerce-social-login' ); ?>"
			><?php echo esc_html_x( 'Sign up', 'Newsletter sign up', 'woocommerce-social-login' ); ?></button>
			<span class="spinner" style="display:inline-block; position: absolute;"></span>
		</div>
		<?php
	}


}

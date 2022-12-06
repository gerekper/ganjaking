<?php
/**
 * WooCommerce Intuit Payments
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Intuit Payments to newer
 * versions in the future. If you wish to customize WooCommerce Intuit Payments for your
 * needs please refer to https://docs.woocommerce.com/document/intuit-qbms/
 *
 * @package   WC-Intuit-Payments/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Intuit\Admin;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * The plugin Setup Wizard class.
 *
 * @since 2.5.0
 */
class Setup_Wizard extends Framework\Payment_Gateway\Admin\Setup_Wizard {


	/**
	 * Returns an instance of the Intuit Payments Credit Card gateway.
	 *
	 * @since 2.5.0
	 *
	 * @return \WC_Gateway_Inuit_Payments_Credit_Card
	 */
	protected function get_gateway() {

		return $this->get_plugin()->get_gateway( \WC_Intuit_Payments::CREDIT_CARD_ID );
	}


	/**
	 * Loads the Setup Wizard's scripts and styles.
	 *
	 * @since 2.5.0
	 */
	protected function load_scripts_styles() {

		parent::load_scripts_styles();

		wp_enqueue_style(
			'wc-intuit-payments-setup-wizard',
			$this->get_plugin()->get_plugin_url() . '/assets/css/admin/wc-intuit-payments-setup-wizard.min.css',
			[ 'sv-wc-admin-setup' ],
			$this->get_plugin()->get_version()
		);

		wp_enqueue_script(
			'wc-intuit-payments-setup-wizard',
			$this->get_plugin()->get_plugin_url() . '/assets/js/admin/wc-intuit-payments-setup-wizard.min.js',
			[ 'wc-intuit-payments-connect' ],
			$this->get_plugin()->get_version(),
			true
		);

		wp_localize_script( 'wc-intuit-payments-setup-wizard', 'wc_intuit_payments', [
			'ajaxurl'                          => admin_url( 'admin-ajax.php' ),
			'gateway_id'                       => $this->get_gateway()->get_id(),
			'update_connection_settings_nonce' => wp_create_nonce( 'wc-intuit-payments-update-connection-settings' ),
			'connect_url'                      => $this->get_gateway()->get_connection_handler()->get_connect_url(),
		] );
	}


	/**
	 * Registers the Setup Wizard steps.
	 *
	 * @since 2.5.0
	 */
	protected function register_steps() {

		$this->register_step(
			'welcome',
			__( 'Welcome', 'woocommerce-gateway-intuit-payments' ),
			/** @see Setup_Wizard::render_welcome_step() */
			[ $this, 'render_welcome_step' ]
		);

		// overwrite the text for the Continue button with help from render_step() overwritten below
		$this->steps['welcome']['button_label'] = __( "Let's Go!", 'woocommerce-gateway-intuit-payments' );

		$this->register_step(
			'create-app',
			__( 'Create app', 'woocommerce-gateway-intuit-payments' ),
			/** @see Setup_Wizard::render_create_app_step() */
			[ $this, 'render_create_app_step' ]
		);

		$this->register_step(
			'app-details',
			__( 'App details', 'woocommerce-gateway-intuit-payments' ),
			/** @see Setup_Wizard::render_app_details_step() */
			[ $this, 'render_app_details_step' ]
		);

		$this->register_step(
			'connect',
			__( 'Connect', 'woocommerce-gateway-intuit-payments' ),
			/** @see Setup_Wizard::render_connect_step() */
			[ $this, 'render_connect_step' ]
			// Client ID and Client Secret are saved through AJAX
		);

		// overwrite the text for the Continue button with help from render_step() overwritten below
		$this->steps['connect']['button_label'] = __( 'Connect to QuickBooks', 'woocommerce-gateway-intuit-payments' );
	}


	/**
	 * Adds hooks to check whether we need to redirect to the Setup Wizard and calls
	 * the parent method to add default actions and hooks.
	 *
	 * @since 2.5.0
	 */
	protected function add_hooks() {

		// maybe redirect to the setup wizard when the plugin is activated
		if ( ! $this->is_complete() ) {
			add_action( 'wc_' . $this->get_plugin()->get_id() . '_installed', [ $this, 'maybe_redirect' ] );
		}

		parent::add_hooks();
	}


	/**
	 * Redirects to the Setup Wizard admin page if the plugin was just installed.
	 *
	 * @since 2.5.0
	 */
	public function maybe_redirect() {

		if ( get_transient( 'wc_intuit_payments_setup_wizard_redirect' ) ) {

			$do_redirect = true;

			// postpone the redirect on the network admin screen or while doing ajax
			if ( wp_doing_ajax() || is_network_admin() ) {
				$do_redirect = false;
			}

			// disable the redirect if multiple plugins were activated together
			if ( isset( $_GET['activate-multi'] ) ) {

				delete_transient( 'wc_intuit_payments_setup_wizard_redirect' );

				$do_redirect = false;
			}

			if ( $do_redirect ) {

				delete_transient( 'wc_intuit_payments_setup_wizard_redirect' );

				wp_safe_redirect( $this->get_setup_url() );
				exit;
			}
		}
	}


	/**
	 * Renders the default welcome note heading.
	 *
	 * @since 2.5.0
	 */
	protected function render_welcome_heading() {

		esc_html_e( 'Welcome to WooCommerce Intuit Payments!', 'woocommerce-gateway-intuit-payments' );
	}


	/**
	 * Renders the welcome note text.
	 *
	 * @since 2.5.0
	 *
	 * @see Framework\Admin\Setup_Wizard::render_welcome_text()
	 */
	protected function render_welcome_text() {

		esc_html_e( "Let's create your Intuit app and connect it to the plugin so you can process credit card payments on your store. Please keep this wizard open in one tab for reference while working through the following steps.", 'woocommerce-gateway-intuit-payments' );
	}


	/**
	 * Renders the main content for the welcome step.
	 *
	 * @since 2.5.0
	 */
	protected function render_welcome_step() {

		printf(
			/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag, %3$s - opening <a> HTML tag, %4$s - closing </a> HTML tag, %5$s - opening <a> HTML tag, %6$s - closing </a> HTML tag */
			'<p>' . esc_html__( '%1$sNote:%2$s You must have a %3$sQuickBooks Online account%4$s with %5$sIntuit Payments%6$s enabled to complete setup.', 'woocommerce-gateway-intuit-payments' ) . '</p>',
			'<strong>',
			'</strong>',
			'<a href="https://quickbooks.intuit.com/pricing/" target="_blank">',
			'</a>',
			'<a href="https://c5.qbo.intuit.com/app/paymentsactivation" target="_blank">',
			'</a>'
		);

		echo '<p>' . esc_html__( 'Not ready to setup yet? You can access the setup wizard later from the plugin settings.', 'woocommerce-gateway-intuit-payments' ) . '</p>';
	}


	/**
	 * Renders content for the Create app step.
	 *
	 * @since 2.5.0
	 */
	protected function render_create_app_step() {
		?>
		<h1><?php esc_html_e( 'Create the app', 'woocommerce-gateway-intuit-payments' ); ?></h1>

		<p><?php esc_html_e( "First, let's create the app:", 'woocommerce-gateway-intuit-payments' ); ?></p>

		<ol>
			<?php
			printf(
				/* translators: Placeholders: %1$s - opening <a> HTML tag, %2$s - closing </a> HTML tag */
				'<li>' . esc_html__( 'Go to the %1$sIntuit Developer site%2$s and login with your QuickBooks Online credentials.', 'woocommerce-gateway-intuit-payments' ) . '</li>',
				'<a href="https://developer.intuit.com/" target="_blank">',
				'</a>'
			);

			printf(
				/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag */
				'<li>' . esc_html__( 'Click %1$sMy Apps > Create an app > QuickBooks Online and Payments%2$s.', 'woocommerce-gateway-intuit-payments' ) . '</li>',
				'<strong>',
				'</strong>'
			);

			printf(
				/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag */
				'<li>' . esc_html__( 'Enter a name and select "Payments (US only)", then click %1$sCreate app%2$s.', 'woocommerce-gateway-intuit-payments' ) . '</li>',
				'<strong>',
				'</strong>'
			);
			?>
		</ol>
		<?php
	}


	/**
	 * Renders content for the App details step.
	 *
	 * @since 2.5.0
	 */
	protected function render_app_details_step() {

		$advanced_settings_url = add_query_arg( [
			'page' => 'wc-settings',
			'tab'  => 'advanced',
		], admin_url( 'admin.php' ) );

		$redirect_url = add_query_arg( [
			'wc-api' => $this->get_gateway()->get_connection_handler()->get_authorize_action_name(),
		], home_url() );

		?>
		<h1><?php esc_html_e( 'Update app details', 'woocommerce-gateway-intuit-payments' ); ?></h1>

		<ol>
			<li>
				<?php printf(
					/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag */
					esc_html__( 'Go to the %1$sProduction tab%2$s.', 'woocommerce-gateway-intuit-payments' ),
					'<strong>',
					'</strong>'
				); ?>
			</li>

			<li>
				<?php printf(
					/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag */
					esc_html__( 'Update the %1$sTerms of Service Links%2$s section.', 'woocommerce-gateway-intuit-payments' ),
					'<strong>',
					'</strong>'
				); ?>

				<ul>
					<?php
					printf(
						/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag, %3$s - opening <a> HTML tag, %4$s - closing </a> HTML tag */
						'<li>' . esc_html__( 'You can find your current %1$sTerms and Conditions%2$s page in your %3$sWooCommerce Settings%4$s.', 'woocommerce-gateway-intuit-payments' ) . '</li>',
						'<strong>',
						'</strong>',
						sprintf( '<a href="%s" target="_blank">', esc_attr( $advanced_settings_url ) ),
						'</a>'
					);

					printf(
						/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag */
						'<li>' . esc_html__( 'You can use the same URL in the %1$sEULA%2$s and %1$sPrivacy Policy%2$s fields.', 'woocommerce-gateway-intuit-payments' ) . '</li>',
						'<strong>',
						'</strong>'
					);

					printf(
						/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag */
						'<li>' . esc_html__( 'Click %1$sSave%2$s.', 'woocommerce-gateway-intuit-payments' ) . '</li>',
						'<strong>', '</strong>'
					);
					?>
				</ul>
			</li>

			<li>
				<?php printf(
					/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag */
					esc_html__( 'Go to the %1$sKeys & OAuth%2$s tab.', 'woocommerce-gateway-intuit-payments' ),
					'<strong>',
					'</strong>'
				); ?>
			</li>

			<li>
				<?php printf(
					/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag */
					esc_html__( 'Update the %1$sRedirect URIs%2$s field with the following URL:', 'woocommerce-gateway-intuit-payments' ),
					'<strong>',
					'</strong>'
				); ?>

				<pre><code><?php echo esc_url( $redirect_url ); ?></code></pre>
			</li>

			<li>
				<?php printf(
					/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag */
					esc_html__( 'Update the %1$sApp URLs%2$s fields.', 'woocommerce-gateway-intuit-payments' ),
					'<strong>',
					'</strong>'
				); ?>

				<ul>
					<li><?php esc_html_e( "You can use your homepage or shop domain &ndash; these fields don't require a special URL.", 'woocommerce-gateway-intuit-payments' ); ?></li>
					<li>
						<?php printf(
							/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag */
							esc_html__( 'Click %1$sSave%2$s.', 'woocommerce-gateway-intuit-payments' ),
							'<strong>',
							'</strong>'
						); ?>
					</li>
				</ul>
			</li>
		</ol>
		<?php
	}


	/**
	 * Renders the content for the Connect step.
	 *
	 * @since 2.5.0
	 */
	protected function render_connect_step() {

		?><h1><?php esc_html_e( 'Connect to QuickBooks', 'woocommerce-gateway-intuit-payments' ); ?></h1>

		<div class="wc-intuit-payments-setup-connection-settings">

			<?php

			printf(
				/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag */
				'<p>' . esc_html__( 'Now we\'re ready to connect! Enter your %1$sClient ID%2$s and %1$sClient Secret%2$s in the fields below:', 'woocommerce-gateway-intuit-payments' ) . '</p>',
				'<strong>',
				'</strong>'
			);

			$gateway = $this->get_gateway();

			$this->render_form_field(
				'wc_gateway_' . $gateway->get_id() . '_client_id',
				[
					'label'     => __( 'Client ID', 'woocommerce-gateway-intuit-payments' ),
					'type'      => 'text',
					'required'  => true,
					'autofocus' => true,
				],
				// allow both production and sandbox credentials to be shown to facilitate testing
				$gateway->get_environment() === 'production' ? $gateway->get_option( 'client_id' ) : $gateway->get_option( 'sandbox_client_id' )
			);

			?>

			<label for="<?php echo esc_attr( 'wc_gateway_' . $gateway->get_id() . '_client_id' ); ?>" class="wc-intuit-payments-error-message wc-intuit-payments-error-message-empty"><?php esc_html_e( 'Please fill out this field.', 'woocommerce-gateway-intuit-payments' ); ?></label>
			<label for="<?php echo esc_attr( 'wc_gateway_' . $gateway->get_id() . '_client_id' ); ?>" class="wc-intuit-payments-error-message wc-intuit-payments-error-message-invalid"><?php esc_html_e( 'The Client ID appears to be invalid. Please make sure the value is at least ten characters long and includes alphanumeric characters only.', 'woocommerce-gateway-intuit-payments' ); ?></label>

			<?php $this->render_form_field(
				'wc_gateway_' . $gateway->get_id() . '_client_secret',
				[
					'label'    => __( 'Client Secret', 'woocommerce-gateway-intuit-payments' ),
					'type'     => 'text',
					'required' => true,
				],
				// allow both production and sandbox credentials to be shown to facilitate testing
				$gateway->get_environment() === 'production' ? $gateway->get_option( 'client_secret' ) : $gateway->get_option( 'sandbox_client_secret' )
			); ?>

			<label for="<?php echo esc_attr( 'wc_gateway_' . $gateway->get_id() . '_client_secret' ); ?>" class="wc-intuit-payments-error-message wc-intuit-payments-error-message-empty"><?php esc_html_e( 'Please fill out this field.', 'woocommerce-gateway-intuit-payments' ); ?></label>
			<label for="<?php echo esc_attr( 'wc_gateway_' . $gateway->get_id() . '_client_secret' ); ?>" class="wc-intuit-payments-error-message wc-intuit-payments-error-message-invalid"><?php esc_html_e( 'The Client Secret appears to be invalid. Please make sure the value is at least ten characters long and includes alphanumeric characters only.', 'woocommerce-gateway-intuit-payments' ); ?></label>

		</div>
		<div class="wc-intuit-payments-setup-connection-suggestions">

			<p class="wc-intuit-payments-connection-error">
				<?php printf(
					/* translators: Placeholders: %1$s - opening <a> HTML tag, %2$s - closing </a> HTML tag */
					esc_html__( 'Connection failed. Please go back and check the following settings, then %1$stry again%2$s:', 'woocommerce-gateway-intuit-payments' ),
					sprintf( '<a href="%s">', esc_url( $this->get_step_url( 'connect' ) ) ),
					'</a>'
				); ?>
			</p>

			<p class="wc-intuit-payments-connection-error-with-message">
				<?php printf(
					/* translators: Placeholders: %1$s - <span></span> opening and closing HTML tags, %2$s - opening <a> HTML tag, %3$s - closing </a> HTML tag */
					esc_html__( 'Connection failed with error: %1$s. Please go back and check the following settings, then %2$stry again%3$s:', 'woocommerce-gateway-intuit-payments' ),
					'<span></span>',
					sprintf( '<a href="%s">', esc_url( $this->get_step_url( 'connect' ) ) ),
					'</a>'
				); ?>
			</p>

			<ul>
				<?php
				printf(
					/* translators: Placeholders: %1$s - opening <a> HTML tag, %2$s - closing </a> HTML tag */
					'<li>' . esc_html__( '%1$sConfirm that the Intuit app was set up correctly%2$s.', 'woocommerce-gateway-intuit-payments' ) . '</li>',
					sprintf( '<a href="%s">', esc_url( $this->get_step_url( 'create-app' ) ) ),
					'</a>'
				);

				printf(
					/* translators: Placeholders: %1$s - opening <a> HTML tag, %2$s - closing </a> HTML tag */
					'<li>' . esc_html__( '%1$sDouble-check the Redirect URI in the Intuit app%2$s.', 'woocommerce-gateway-intuit-payments' ) . '</li>',
					sprintf( '<a href="%s">', esc_url( $this->get_step_url( 'app-details' ) ) ),
					'</a>'
				);

				printf(
					/* translators: Placeholders: %1$s - opening <a> HTML tag, %2$s - closing </a> HTML tag */
					'<li>' . esc_html__( '%1$sDouble-check the Client ID and Client Secret fields%2$s.', 'woocommerce-gateway-intuit-payments' ) . '</li>',
					sprintf( '<a href="%s">', esc_url( $this->get_step_url( 'connect' ) ) ),
					'</a>'
				);
				?>
			</ul>

			<?php printf(
				/* translators: Placeholders: %1$s - opening <a> HTML tag, %2$s - closing </a> HTML tag */
				'<p>'. esc_html__( 'Still not connecting correctly? Please %1$scontact support%2$s.', 'woocommerce-gateway-intuit-payments' ) . '</p>',
				sprintf( '<a href="%s" target="_blank">', esc_url( $this->get_plugin()->get_support_url() ) ),
				'</a>'
			); ?>

		</div>
		<?php
	}


	/**
	 * Renders the finished screen markup.
	 *
	 * This is what gets displayed after all of the steps have been completed or skipped.
	 *
	 * Copied from the \Framework\Admin\Setup_Wizard to allow the main title to be changed.
	 *
	 * @since 2.5.0
	 */
	protected function render_finished() {

		$this->render_finished_step_title();
		$this->render_before_next_steps();
		$this->render_next_steps();
		$this->render_after_next_steps();
	}


	/**
	 * @since 2.5.0
	 */
	protected function render_finished_step_title() {

		?><h1><?php esc_html_e( 'Finished!', 'woocommerce-gateway-intuit-payments' ); ?></h1><?php
	}


	/**
	 * @since 2.5.0
	 */
	protected function render_before_next_steps() {

		?><p><?php esc_html_e( "That's it! You're ready to enable the plugin and accept payments.", 'woocommerce-gateway-intuit-payments' ); ?></p><?php
	}


	/**
	 * @since 2.5.0
	 */
	protected function get_next_steps() {

		return [
			'enable-intuit-payments' => [
				'label'        => __( 'Enable Intuit Payments', 'woocommerce-gateway-intuit-payments' ),
				'description'  => __( 'Enable the gateway to start accepting payments now or update other plugin settings.', 'woocommerce-gateway-intuit-payments' ),
				'name'         => __( 'Go to Settings', 'woocommerce-gateway-intuit-payments' ),
				'url'          => $this->get_plugin()->get_settings_url(),
			],
			'view-docs'              => [
				'label'        => __( 'Intuit Payments documentation', 'woocommerce-gateway-intuit-payments' ),
				'description'  => __( 'Read about the gateway features and settings.', 'woocommerce-gateway-intuit-payments' ),
				'name'         => __( 'Visit docs', 'woocommerce-gateway-intuit-payments' ),
				'url'          => $this->get_plugin()->get_documentation_url(),
				'button_class' => 'button button-large',
			],
			'get-support'            => [
				'label'        => __( 'Get support', 'woocommerce-gateway-intuit-payments' ),
				'description'  => __( 'Need help? Get in touch with our support team!', 'woocommerce-gateway-intuit-payments' ),
				'name'         => __( 'Contact support', 'woocommerce-gateway-intuit-payments' ),
				'url'          => $this->get_plugin()->get_support_url(),
				'button_class' => 'button button-large',
			]
		];
	}


	/**
	 * @since 2.5.0
	 */
	protected function get_additional_actions() {

		return [];
	}


	/**
	 * Renders a given step's markup.
	 *
	 * This will display a title, whatever get's rendered by the step's view
	 * callback, then the navigation buttons.
	 *
	 * Copied from the \Framework\Admin\Setup_Wizard to allow the button title to be modified.
	 *
	 * @since 2.5.0
	 *
	 * @param string $step_id step ID to render
	 */
	protected function render_step( $step_id ) {

		call_user_func( $this->steps[ $step_id ]['view'], $this );

		if ( isset( $this->steps[ $step_id ]['button_label'] ) ) {
			$label = $this->steps[ $step_id ]['button_label'];
		} else {
			$label = __( 'Continue', 'woocommerce-gateway-intuit-payments' );
		}

		?>
		<p class="wc-setup-actions step">

			<?php if ( is_callable( $this->steps[ $step_id ]['save'] ) ) : ?>

				<button
					type="submit"
					name="save_step"
					class="button-primary button button-large button-next"
					value="<?php echo esc_attr( $label ); ?>">
					<?php echo esc_html( $label ); ?>
				</button>

			<?php else : ?>

				<a class="button-primary button button-large button-next" href="<?php echo esc_url( $this->get_next_step_url( $step_id ) ); ?>"><?php echo esc_html( $label ); ?></a>

			<?php endif; ?>
		</p>
		<?php
	}


	/**
	 * Renders the newsletter content after the next steps in the last Setup Wizard screen.
	 *
	 * @since 2.5.0
	 */
	protected function render_after_next_steps() {

		?>
		<ul class="wc-wizard-next-steps wc-intuit-payments-newsletter-prompt">
			<li class="wc-wizard-next-step-item">
				<div class="wc-wizard-next-step-description">
					<h3><?php esc_html_e( 'Want to keep learning?', 'woocommerce-gateway-intuit-payments' ); ?></h3>
					<p class="wc-intuit-payments-newsletter-prompt-description">
						<?php esc_html_e( 'Check out our monthly newsletter where we share updates, tutorials, and sneak peeks for new development!', 'woocommerce-memberships' ); ?>
					</p>
					<button
						class="button button-primary newsletter-signup"
						data-user-email="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>"
						data-thank-you="<?php esc_attr_e( 'Thanks for signing up! Keep an eye on your inbox for product updates and helpful tips!', 'woocommerce-gateway-intuit-payments' ); ?>"
					><?php echo esc_html_x( 'Sign up', 'Newsletter sign up', 'woocommerce-gateway-intuit-payments' ); ?></button>
					<span class="spinner" style="display:inline-block; position: absolute;"></span>
				</div>
			</li>
		</ul>
		<?php
	}


}

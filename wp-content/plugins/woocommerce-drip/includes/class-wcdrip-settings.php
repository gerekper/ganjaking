<?php
/**
 * WooCommerce Drip Settings
 *
 * @package   WooCommerce Drip
 * @author    Bryce <bryce@bryce.se>
 * @license   GPL-2.0+
 * @link      http://bryce.se
 * @copyright 2014 Bryce Adams
 * @since     1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooCommerce Drip Settings Class
 *
 * @package  WooCommerce Drip
 * @author   Bryce <bryce@bryce.se>
 * @since    1.1.1
 */

if ( ! class_exists( 'WC_Drip_Settings' ) && class_exists( 'WC_Integration' ) ) {

	class WC_Drip_Settings extends WC_Integration {

		/**
		 * Init and hook in the integration.
		 */
		public function __construct() {

			$this->id                 = 'wcdrip';
			$this->method_title       = __( 'Drip', 'woocommerce-drip' );
			$this->method_description = __( 'Configure the settings below to connect your WooCommerce store to your Drip account and start recording events.', 'woocommerce-drip' ) . '<br /><br />' . __( 'After authenticating and selecting an account, the Drip embed code will automatically be added to your site.', 'woocommerce-drip' );

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			/**
			 * Define user set variables.
			 **/

			// General
			$this->api_key            = $this->get_option( 'api_key' );
			$this->account            = $this->get_option( 'account' );

			// Subscription Checkbox
			$this->subscribe_enable   = $this->get_option( 'subscribe-enable' );
			$this->subscribe_campaign = $this->get_option( 'subscribe-campaign' );
			$this->subscribe_text     = $this->get_option( 'subscribe-text' );

			// Event: Sale 'Name'
			$this->event_sale_name    = $this->get_option( 'event-sale-name' );

			$this->logging_enabled    = $this->get_option( 'logging_enabled', 'no' );

			// Actions.
			add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'init', array( $this, 'init' ), 10 );
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

			// Filters.
			add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitize_settings' ) );

		}


		/**
		 * Wrapper containing all settings for easy access
		 *
		 * @package WooCommerce Drip
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 * @return  array
		 */

		public function wrapper() {

			$wrapper = array(
				// General
				'api_key'                => $this->get_option( 'api_key' ),
				'account'                => $this->get_option( 'account' ),

				// Subscription Checkbox
				'subscribe_enable'       => $this->get_option( 'subscribe-enable' ),
				'subscribe_campaign'     => $this->get_option( 'subscribe-campaign' ),
				'subscribe_text'         => $this->get_option( 'subscribe-text' ),

				// Event: Sale 'Name'
				'event_sale_name'        => $this->get_option( 'event-sale-name' ),

				'logging_enabled'        => $this->get_option( 'logging_enabled' ),

			);

			return $wrapper;

		}


		/**
		 * Initialize integration settings form fields.
		 *
		 * @since  1.1.0
		 * @return void
		 */
		public function init_form_fields() {

			$this->form_fields = array(
				'api-title' => array(
					'title'       => __( 'Authentication & Account', 'woocommerce-drip' ),
					'type'        => 'title',
					'description' => __( 'Please authenticate your Drip account here and choose the account to connect it with.', 'woocommerce-drip' ),
					'class'       => 'wcdrip-section-title-main',
				),
				'api_key' => array(
					'title'       => __( 'API Token', 'woocommerce-drip' ),
					'type'        => 'text',
					'description' => sprintf( __( 'Go to your %s and copy the API Token provided. %s', 'woocommerce-drip' ), '<a href="https://www.getdrip.com/user/edit" target="_blank"><strong>' . __( 'Drip User Account Settings', 'woocommerce-drip' ) . '</strong></a>', '<a href="' . get_admin_url() . 'admin.php?page=wc-settings&tab=integration&section=wcdrip&wcdrip_clear_api_key=1">' . __( 'Clear API Token?', 'woocommerce-drip' ) . '</a>' ),
					'default'     => '',
				),
				'account' => array(
					'title'       => __( 'Account', 'woocommerce-drip' ),
					'type'        => 'select',
					'description' => __( 'Which account do you want to link this site up with?', 'woocommerce-drip' ),
					'options'     => $this->accounts(),
				),

				'subscribe-title' => array(
					'title'       => __( 'Subscribe Checkbox', 'woocommerce-drip' ),
					'type'        => 'title',
					'description' => __( 'Add a subscription box to the checkout page and WooCommerce registration form.', 'woocommerce-drip' ),
					'class'       => 'wcdrip-section-title',
				),

				// Subscribe Option Enable
				'subscribe-enable' => array(
					'title'       => __( 'Subscribe Checkbox', 'woocommerce-drip' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable Subscription Checkbox', 'woocommerce-drip' ),
					'default'     => 'no',
					'desc_tip'    => true,
					'description' => __( 'This will add a subscription box on checkout', 'woocommerce-drip' ),
				),

				'subscribe-campaign' => array(
					'title'       => __( 'Campaign', 'woocommerce-drip' ),
					'type'        => 'select',
					'description' => __( 'Which campaign do you want the subscribe box to sign a user up for?', 'woocommerce-drip' ) . '<br />' . '<strong>' . __( 'Note:', 'woocommerce-drip' ) . '</strong> ' . __( 'After saving a new account above, please use the Reload Data option below.', 'woocommerce-drip' ),
					'options'     => $this->campaigns(),
					'class'       => 'wcdrip-subscribe-field',
				),

				'subscribe-text' => array(
					'title'       => __( 'Subscribe Text', 'woocommerce-drip' ),
					'type'        => 'text',
					'placeholder' => 'Subscribe to {campaign_name}',
					'desc_tip'    => false,
					'description' => sprintf( __( '%s Subscribe to {campaign_name}%sText for the subscribe checkbox - valid HTML allowed. Use the template tag %s to display the campaign name (optional).', 'woocommerce-drip' ), '<strong>' . __( 'Default:', 'woocommerce-drip' ) . '</strong>', '<br />', '<span class="wcdrip-tag"><strong>{campaign_name}</strong></span>' ),
					'class'       => 'wcdrip-subscribe-field',
				),

				'notif-title' => array(
					'title' => __( 'Other', 'woocommerce-drip' ),
					'type'  => 'title',
					'class' => 'wcdrip-section-title',
				),

				'event-sale-name' => array(
					'title'       => __( 'Event Sale Name', 'woocommerce-drip' ),
					'type'        => 'text',
					'placeholder' => 'Purchase',
					'desc_tip'    => false,
					'description' => sprintf( __( '%s Purchase%s Name the event that is added to a subscriber after a purchase. It should (optionally) match your Conversion Goal name exactly.', 'woocommerce-drip' ), '<strong>' . __( 'Default:', 'woocommerce-drip' ) . '</strong>', '<br />' ),
				),

				// Test Notification Button
				'reload_accounts' => array(
					'type'        => 'reload_accounts',
				),

				'logging_enabled' => array(
					'title'       => __( 'Enable Logging' ),
					'type'        => 'checkbox',
					'desc_tip'    => __( 'Note: this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.', 'woocommerce-drip' ),
					'description' => __( 'Log Drip debug events, such as API requests, to WooCommerce System Status log.', 'woocommerce-drip' ),
				)
			);

		}


		/**
		 * Scripts / CSS Needed
		 *
		 * @package WooCommerce Drip
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Let's get some jQuery going
			wp_enqueue_script( 'jquery' );

			// Register Scripts / Styles
			wp_register_script( 'wcdrip-admin-js', plugins_url( 'assets/js/wcdrip-admin' . $suffix . '.js', dirname( __FILE__ ) ), array( 'jquery') );

			// Enqueue Scripts / Styles
			wp_enqueue_script( 'wcdrip-admin-js' );

		}


		/**
		 * Clear API key or refresh accounts/campaigns list
		 *
		 * @package WooCommerce Drip
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.1.0
		 */

		public function init() {

			/**
			 * Clear API key if desired:
			 * (this will also delete all Drip settings as they are API-key related)
			 * */
			if ( isset( $_GET['wcdrip_clear_api_key'] ) && ( $_GET['wcdrip_clear_api_key'] == 1 ) ) {
				delete_option( 'woocommerce_wcdrip_settings' );
				wp_safe_redirect( get_admin_url() . 'admin.php?page=wc-settings&tab=integration&section=wcdrip' );
			}

			/**
			 * Reload accounts by clearing old transients and redirecting back to settings:
			 * (this will trigger an accounts/campaign refresh)
			 * */
			if ( isset( $_GET['wcdrip_reload_accounts'] ) && ( $_GET['wcdrip_reload_accounts'] == 1 ) ) {
				delete_transient( 'wcdrip_accounts' );
				delete_transient( 'wcdrip_campaigns' );
				wp_safe_redirect( get_admin_url() . 'admin.php?page=wc-settings&tab=integration&section=wcdrip' );
			}

		}


		/**
		 * Reload Accounts Button HTML
		 *
		 * @package WooCommerce Drip
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function generate_reload_accounts_html() {
			ob_start();
			?>
			<tr valign="top" id="service_options">
				<th scope="row" class="titledesc"><?php _e( 'Reload Drip Data', 'woocommerce-drip' ); ?></th>
				<td>
					<p><a href="<?php echo get_admin_url(); ?>admin.php?page=wc-settings&tab=integration&section=wcdrip&wcdrip_reload_accounts=1" class="button" id="wcdrip-reload-data-button"><?php _e('Reload Accounts / Campaigns', 'woocommerce-drip'); ?></a></p>
					<p><em><?php _e( 'If you have added a new account and/or campaign and it\'s not showing, this will refresh the list.', 'woocommerce-drip' ); ?></em></p>
				</td>
			</tr>
			<?php
			return ob_get_clean();
		}


		/**
		 * Show available accounts
		 *
		 * @package WooCommerce Drip
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.1.1
		 */

		public function accounts() {

			$wrapper = $this->wrapper();
			$api_key = $wrapper['api_key'];

			if ( false === ( $all_accounts = get_transient( 'wcdrip_accounts' ) ) ) {
				$all_accounts = $this->store_accounts( $api_key );
			}

			if ( $all_accounts == false ) {
				return 'No accounts exist!';
			}

			return $all_accounts;

		}


		/**
		 * Show available campaigns
		 *
		 * @package WooCommerce Drip
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function campaigns() {

			$wrapper = $this->wrapper();
			$api_key = $wrapper['api_key'];
			$account_id = $wrapper['account'];

			if ( $api_key && $account_id ) {

				if ( false === ( $all_campaigns = get_transient( 'wcdrip_campaigns' ) ) ) {

					$wcdrip_api = new Drip_API( $api_key );

					$params = array(
						'account_id' 	=> $account_id,
						'status'		=> 'all',
					);


					wcdrip_log( sprintf( '%s: Get campaigns from API with params: %s', __METHOD__, print_r( $params, true ) ) );
					$campaigns = $wcdrip_api->get_campaigns( $params );
					wcdrip_log( sprintf( '%s: Got campaigns from API: %s', __METHOD__, print_r( $campaigns, true ) ) );

					$all_campaigns = array();

					foreach ( $campaigns as $campaign ) {

						$all_campaigns[$campaign['id']] = $campaign['name'];

					}

					// Cache the data for now
					set_transient( 'wcdrip_campaigns', $all_campaigns, 86400 ); // 60 * 60 * 24 = 1 DAY

				}

				return $all_campaigns;

			} else {

				return array( 'Reload Page to See Campaigns' );

			}

		}


		/**
		 * Sanitize our settings
		 * @see process_admin_options()
		 */
		public function sanitize_settings( $settings ) {

			if ( isset( $settings ) && isset( $settings['api_key'] ) ) {
				esc_html( $settings['api_key'] );
			}
			return $settings;

		}


		/**
		 * Validate the API key
		 * @see validate_settings_fields()
		 * @since 1.1.0
		 */
		public function validate_api_key_field( $key ) {

			// Get the posted value
			$value = $_POST[ $this->plugin_id . $this->id . '_' . $key ];

			// Get Accounts
			$accounts = $this->store_accounts( $value );

			// If no accounts returned, label API key as invalid
			if ( isset( $value ) && ( $accounts == false ) ) {
				$this->errors[] = $key;
			}

			return $value;

		}


		/**
		 * Display errors by overriding the display_errors() method
		 * @see display_errors()
		 */
		public function display_errors( ) {

			// loop through each error and display it
			foreach ( $this->errors as $key => $value ) { ?>

				<div class="error">
					<p><?php _e( 'The API Key is invalid. Please double check it and try again!', 'woocommerce-drip' ); ?></p>
				</div>

			<?php }

		}

		/**
		 * Get and store accounts in transient as this is needed a couple times.
		 * @param  $api_key
		 * @return bool
		 * @since  1.1.1
		 */
		public function store_accounts( $api_key ) {

			$wcdrip_api = new Drip_API( $api_key );


			wcdrip_log( sprintf( '%s: Get accounts from API', __METHOD__ ) );
			$accounts = $wcdrip_api->get_accounts();
			wcdrip_log( sprintf( '%s: Got accounts from API: %s', __METHOD__, print_r( $accounts, true ) ) );

			if ( $accounts == false ) {
				return false;
			}

			$all_accounts = array();

			foreach ( $accounts as $account ) {
				$all_accounts[$account['id']] = $account['name'];
			}

			set_transient( 'wcdrip_accounts', $all_accounts, 86400 ); // 60 * 60 * 24 = 1 DAY
			return $all_accounts;

		}

	}

}

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Waitlist_Settings' ) ) {
	/**
	 * Waitlist Settings
	 *
	 * Displays settings for the waitlist
	 *
	 * @class Pie_WCWL_Waitlist_Settings
	 */
	class Pie_WCWL_Waitlist_Settings {

		/**
		 * Initialise settings
		 *
		 * @access public
		 */
		public function init() {
			// Required for our settings tab in woocommerce versions < 2.3.
			add_action( 'woocommerce_settings_waitlist', array( $this, 'render_settings' ) );
			add_action( 'woocommerce_update_options_waitlist', array( $this, 'save_settings' ) );
			// Required for our settings section on product tab in woocommerce version >= 2.3.
			add_filter( 'woocommerce_get_sections_products', array( $this, 'add_waitlist_settings' ), 10 );
			add_filter( 'woocommerce_get_settings_products', array( $this, 'waitlist_all_settings' ), 10, 2 );
			// Required to filter the email description text on the settings page for "new accounts".
			add_action( 'woocommerce_settings_start', array( $this, 'add_filter_for_new_account_email_description' ), 10 );
			add_action( 'woocommerce_settings_start', array( $this, 'add_filter_for_new_account_old_email_description' ), 10 );
			add_action( 'woocommerce_email_settings_before', array( $this, 'remove_filter_for_email_description_text' ), 10 );
			// Hook up JS to pull in the button to update waitlist counts.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_settings_scripts' ) );
			// Add account endpoint.
			add_filter( 'woocommerce_settings_pages', array( $this, 'add_waitlist_account_endpoint_setting' ) );
		}

		/**
		 * Enqueue required styles and scripts
		 *
		 * @param $hook
		 */
		public function enqueue_settings_scripts( $hook ) {
			if ( 'woocommerce_page_wc-settings' == $hook && $this->is_waitlist_settings_page() ) {
				wp_enqueue_script( 'wcwl_admin_settings', WCWL_ENQUEUE_PATH . '/includes/js/src/wcwl_admin_settings.min.js', array(), WCWL_VERSION );
				wp_localize_script( 'wcwl_admin_settings', 'wcwl_settings', $this->get_script_data() );
			}
		}

		/**
		 * Check we are on the waitlist settings page
		 *
		 * @return bool
		 */
		protected function is_waitlist_settings_page() {
			// Check for woocommerce versions >= 2.3
			if ( isset( $_GET['section'] ) && 'waitlist' == $_GET['section'] ) {
				return true;
			}
			// Check for woocommerce versions < 2.3
			if ( isset( $_GET['tab'] ) && 'waitlist' == $_GET['tab'] ) {
				return true;
			}

			return false;
		}

		/**
		 * Generate data required for settings scripts
		 *
		 * @return array
		 */
		protected function get_script_data() {
			return array(
				'get_products_nonce'             => wp_create_nonce( 'wcwl-ajax-get-products-nonce' ),
				'update_counts_nonce'            => wp_create_nonce( 'wcwl-ajax-update-counts-nonce' ),
				'update_meta_nonce'              => wp_create_nonce( 'wcwl-ajax-update-meta-nonce' ),
				'export_nonce'                   => wp_create_nonce( 'wcwl-ajax-export-nonce' ),
				'confirmation_message'           => __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?' ),
				'update_counts_desc'             => __( 'Update Waitlist Counts', 'woocommerce-waitlist' ),
				'update_meta_desc'               => __( 'Update Waitlist Metadata', 'woocommerce-waitlist' ),
				'export_desc'                    => __( 'Export Waitlist Data', 'woocommerce-waitlist' ),
				'update_counts_button_text'      => __( 'Update Counts', 'woocommerce-waitlist' ),
				'update_meta_button_text'        => __( 'Update Metadata', 'woocommerce-waitlist' ),
				'export_button_text'             => __( 'Export', 'woocommerce-waitlist' ),
				'no_products_message'            => sprintf( __( 'No products were found. If you believe this is an error please submit a ticket for the plugin through Woo Support %s', 'woocommerce-waitlist' ), '(https://woocommerce.com/my-account/create-a-ticket/)' ),
				'update_warning'                 => sprintf( __( '%1$sWarning:%2$s This could take a long while depending on how many products there are. Do not navigate away from this page until the update is complete.', 'woocommerce-waitlist' ), '<b>', '</b>' ),
				'export_text'                    => __( 'Download all waitlist and archive data for all products to a CSV file', 'woocommerce-waitlist' ),
				'update_counts_message'          => sprintf( __( 'Currently updating waitlist counts for product %s', 'woocommerce-waitlist' ), '<span class="wcwl_current_update"></span>/<span class="wcwl_total_updates"></span>' ),
				'update_counts_message_complete' => sprintf( __( 'Successfully updated waitlist counts for %s products', 'woocommerce-waitlist' ), '<span class="wcwl_total_updates"></span>' ),
				'update_meta_message'            => sprintf( __( 'Currently updating waitlist metadata for product %s', 'woocommerce-waitlist' ), '<span class="wcwl_current_update"></span>/<span class="wcwl_total_updates"></span>' ),
				'update_meta_message_complete'   => sprintf( __( 'Successfully updated waitlist metadata for %s products', 'woocommerce-waitlist' ), '<span class="wcwl_total_updates"></span>' ),
			);
		}

		/**
		 * Save waitlist settings
		 *
		 * @access     public
		 * @return     void
		 * @since      1.3
		 */
		public function save_settings() {
			woocommerce_update_options( $this->get_settings() );
		}

		/**
		 * Render waitlist settings page
		 *
		 * @access     public
		 * @return     void
		 * @since      1.3
		 */
		public function render_settings() {
			woocommerce_admin_fields( $this->get_settings() );
		}

		/**
		 * Return options to be displayed on waitlist settings page
		 *
		 * @access public
		 * @return array $settings options to be rendered
		 * @since  1.3
		 */
		public function get_settings() {
			$settings = array(
				array(
					'title' => 'Waitlist Display Options',
					'type'  => 'title',
					'id'    => 'waitlist_display',
				),
				array(
					'title'   => __( 'Waitlists require registration', 'woocommerce-waitlist' ),
					'desc'    => __( 'A user must be logged in to the site to be able to join a waitlist', 'woocommerce-waitlist' ),
					'id'      => WCWL_SLUG . '_registration_needed',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => __( 'Force account creation', 'woocommerce-waitlist' ),
					'desc'    => __( 'Automatically create an account for users that join a waitlist', 'woocommerce-waitlist' ),
					'id'      => WCWL_SLUG . '_create_account',
					'default' => 'yes',
					'type'    => 'checkbox',
				),
				array(
					'title'   => __( 'Auto-Login users', 'woocommerce-waitlist' ),
					'desc'    => __( 'Automatically login users when an account is created for them', 'woocommerce-waitlist' ),
					'id'      => WCWL_SLUG . '_auto_login',
					'default' => 'no',
					'type'    => 'checkbox',
					'class'   => 'wcwl_hidden',
				),
				array(
					'title'   => __( 'Display opt-in for new users', 'woocommerce-waitlist' ),
					'desc'    => __( 'Display a checkbox to logged out users to get consent for creating an account and using their email address', 'woocommerce-waitlist' ),
					'id'      => 'woocommerce_waitlist_new_user_opt-in',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => __( 'Display opt-in for registered users', 'woocommerce-waitlist' ),
					'desc'    => __( 'Display a checkbox to logged in users to get consent for using their email address', 'woocommerce-waitlist' ),
					'id'      => 'woocommerce_waitlist_registered_user_opt-in',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'   => __( 'Show Waitlist elements on shop page', 'woocommerce-waitlist' ),
					'desc'    => __( 'Allow the user to join/leave waitlists for simple products directly from the main shop page', 'woocommerce-waitlist' ),
					'id'      => 'woocommerce_waitlist_show_on_shop',
					'default' => 'no',
					'type'    => 'checkbox',
				),
			);
			if ( function_exists( 'tribe_is_event' ) ) {
				$settings[] = array(
					'title'   => __( 'Show Waitlist elements for event tickets', 'woocommerce-waitlist' ),
					'desc'    => __( 'Allow waitlists for event tickets (this only supports WooCommerce Tickets)', 'woocommerce-waitlist' ),
					'id'      => 'woocommerce_waitlist_events',
					'default' => 'no',
					'type'    => 'checkbox',
				);
			}
			$settings = array_merge(
				$settings,
				array(
					array(
						'type' => 'sectionend',
						'id'   => 'waitlist_updates',
					),
					array(
						'title' => 'Waitlist Admin Options',
						'type'  => 'title',
						'id'    => 'waitlist_admin',
					),
					array(
						'title'   => __( 'Archive Waitlists', 'woocommerce-waitlist' ),
						'desc'    => __( 'Keep a record of customers that have been emailed an in stock notification. This enables you to easily re-send emails, send custom emails or add the user back onto the waitlist', 'woocommerce-waitlist' ),
						'id'      => WCWL_SLUG . '_archive_on',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
					array(
						'title'   => __( 'Minimum stock', 'woocommerce-waitlist' ),
						'desc'    => __( 'Minimum stock amount before users are notified that a product is back in stock. This can be overwritten per product on the product edit screen', 'woocommerce-waitlist' ),
						'id'      => WCWL_SLUG . '_minimum_stock',
						'default' => 1,
						'type'    => 'number',
					),
					array(
						'title'   => __( 'Remove data on uninstall', 'woocommerce-waitlist' ),
						'desc'    => __( 'When uninstalling the plugin from WordPress, check this option to remove all waitlist data from your database', 'woocommerce-waitlist' ),
						'id'      => WCWL_SLUG . '_remove_all_data',
						'type'    => 'checkbox',
						'default' => 'no',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'waitlist_admin',
					),
					array(
						'title' => __( 'Waitlist Update Options', 'woocommerce-waitlist' ),
						'type'  => 'title',
						'id'    => 'waitlist_updates',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'waitlist_updates',
					),
				)
			);

			return $settings;
		}

		/**
		 * Add waitlist options section to the top of the product settings page
		 *
		 * @param array $sections current woocommerce product sections
		 *
		 * @access public
		 * @return array $sections updated woocommerce product sections
		 * @since  1.8.0
		 */
		public function add_waitlist_settings( $sections ) {
			$sections['waitlist'] = __( 'Waitlist', 'woocommerce-waitlist' );

			return $sections;
		}

		/**
		 * Output the settings for the waitlist section under the products tab
		 *
		 * A new filter was added in woocommerce version 2.3 that we need to use
		 *
		 * @param  array  $settings        current settings for this tab
		 * @param  string $current_section the settings section being accessed
		 *
		 * @access public
		 * @return array  $settings        required waitlist settings
		 */
		public function waitlist_all_settings( $settings, $current_section ) {
			if ( 'waitlist' == $current_section ) {
				$settings = $this->get_settings();
			}

			return $settings;
		}

		/**
		 * Add filter for the email description text within email settings on the "new account" tab
		 *
		 * Tab and section names changed from WC 2.1
		 */
		public function add_filter_for_new_account_email_description() {
			if ( ! isset( $_REQUEST['page'] ) || 'wc-settings' != $_REQUEST['page'] || ! isset( $_REQUEST['tab'] ) || 'email' != $_REQUEST['tab'] || ! isset( $_REQUEST['section'] ) || 'wc_email_customer_new_account' != $_REQUEST['section'] ) {
				return;
			} else {
				add_filter( 'gettext', array( $this, 'filter_new_account_email_description' ), 20, 3 );
			}
		}

		/**
		 * Add filter for the email description text within email settings on the "new account" tab
		 *
		 * Required for WC 2.0
		 */
		public function add_filter_for_new_account_old_email_description() {
			if ( ! isset( $_REQUEST['page'] ) || 'woocommerce_settings' != $_REQUEST['page'] || ! isset( $_REQUEST['tab'] ) || 'email' != $_REQUEST['tab'] || ! isset( $_REQUEST['section'] ) || 'WC_Email_Customer_New_Account' != $_REQUEST['section'] ) {
				return;
			} else {
				add_filter( 'gettext', array( $this, 'filter_new_account_email_description' ), 20, 3 );
			}
		}

		/**
		 * Modify the description text within email settings as required
		 *
		 * @param  string $translated_text translated text
		 *
		 * @return string                  modified text string
		 */
		public function filter_new_account_email_description( $translated_text ) {
			if ( 'Customer "new account" emails are sent to the customer when a customer signs up via checkout or account pages.' == $translated_text || 'Customer new account emails are sent when a customer signs up via the checkout or My Account page.' == $translated_text ) {
				$translated_text = __( 'Customer "new account" emails are sent to the customer when a customer signs up via checkout page, My Account page or when adding their email to a waitlist.', 'woocommerce-waitlist' );
			}

			return $translated_text;
		}

		/**
		 * Remove our filter from the gettext hook as early as possible
		 */
		public function remove_filter_for_email_description_text() {
			remove_filter( 'gettext', array( $this, 'filter_new_account_email_description' ) );
		}

		/**
		 * Add an endpoint for the waitlist account page
		 *
		 * @param array $settings current settings.
		 * @return array $settings
		 */
		public function add_waitlist_account_endpoint_setting( $settings ) {
			$logout_setting_key = false;
			foreach ( $settings as $key => $setting ) {
				if ( 'woocommerce_logout_endpoint' === $setting['id'] ) {
					$logout_setting_key = $key;
				}
			}
			if ( $logout_setting_key ) {
				array_splice( $settings, $logout_setting_key + 2, 0, $this->waitlist_endpoint_setting() );
			}
			return $settings;
		}

		/**
		 * Return an array of required settings for the waitlist account endpoint
		 *
		 * @return array
		 */
		public function waitlist_endpoint_setting() {
			return array(
				array(
					'title'    => __( 'Waitlist', 'woocommerce-waitlist' ),
					'desc'     => __( 'Endpoint for the customers waitlist.', 'woocommerce-waitlist' ),
					'id'       => 'woocommerce_myaccount_waitlist_endpoint',
					'type'     => 'text',
					'default'  => 'woocommerce-waitlist',
					'desc_tip' => true,
				),
			);
		}
	}
}

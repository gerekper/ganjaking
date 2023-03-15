<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

use Automattic\WooCommerce\Admin\Features\Navigation\Menu;
use Automattic\WooCommerce\Admin\Features\Navigation\Screen;

class WC_XR_Settings {

	const OPTION_PREFIX = 'wc_xero_';

	/**
	 * Option key name postfix for upload info.
	 *
	 * @since 1.7.13
	 */
	const UPLOAD_INFO_POSTFIX = '_upload_info';

	// Settings defaults
	private $settings = array();
	private $override = array();

	private $key_file_delete_result = array();

	public $valid_filetypes = array(
		'txt' => 'text/plain',
		'pem' => 'text/plain',
		'cer' => 'text/plain',
		'pub' => 'text/plain',
		'ppk' => 'text/plain',
	);

	/**
	 * Allow txt/pem/cer/pub/ppk to be uploaded.
	 *
	 * @param $t array Array of mime types keyed by the file extension regex corresponding to those types.
	 *
	 * @return array
	 */
	public function override_upload_mimes( $t ) {
		return array_merge( $t, $this->valid_filetypes );
	}

	public function __construct( $override = null ) {

		add_filter( 'option_page_capability_woocommerce_xero', array( $this, 'add_save_capability' ) );

		if ( $override !== null ) {
			$this->override = $override;
		}

		// Set the settings
		$this->settings = array(
			// OAuth data.
			'client_id'     => array(
				'title'       => __( 'Client ID', 'woocommerce-xero' ),
				'default'     => '',
				'type'        => 'text_oauth',
				'description' => __( 'OAuth Credential retrieved from <a href="https://developer.xero.com/myapps/" target="_blank">Xero Developer My Apps Centre</a>.', 'woocommerce-xero' ),
			),
			'client_secret' => array(
				'title'       => __( 'Client Secret', 'woocommerce-xero' ),
				'default'     => '',
				'type'        => 'text',
				'description' => __( 'OAuth Credential retrieved from <a href="https://developer.xero.com/myapps/" target="_blank">Xero Developer My Apps Centre</a>.', 'woocommerce-xero' ),
			),
			// Connect to Xero button.
			'oauth_20'      => array(
				'title'       => __( 'Authenticate', 'woocommerce-xero' ),
				'default'     => '',
				'type'        => 'oauth',
				'description' => __( 'Use this button to authenticate your Xero integration', 'woocommerce-xero' ),
			),
		);

		if ( ! WC_XR_OAuth20::can_use_oauth20() && $this->oauth10_setup_params_exist() ) {
			$this->settings = array_merge(
				$this->settings,
				array(
					// API keys.
					'consumer_key'        => array(
						'title'       => __( 'Consumer Key', 'woocommerce-xero' ),
						'default'     => '',
						'type'        => 'text',
						'description' => __( 'OAuth Credential retrieved from <a href="http://api.xero.com" target="_blank">Xero Developer Centre</a>.', 'woocommerce-xero' ),
					),
					'consumer_secret'     => array(
						'title'       => __( 'Consumer Secret', 'woocommerce-xero' ),
						'default'     => '',
						'type'        => 'text',
						'description' => __( 'OAuth Credential retrieved from <a href="http://api.xero.com" target="_blank">Xero Developer Centre</a>.', 'woocommerce-xero' ),
					),
					// SSH key files.
					'public_key_content'  => array(
						'title'       => __( 'Public Key', 'woocommerce-xero' ),
						'default'     => '',
						'type'        => 'key_file',
						'key_type'    => 'public',
						'file_ext'    => '.cer',
						'description' => __( 'Public key file created to authenticate this site with Xero.', 'woocommerce-xero' ),
					),
					'private_key_content' => array(
						'title'       => __( 'Private Key', 'woocommerce-xero' ),
						'default'     => '',
						'type'        => 'key_file',
						'key_type'    => 'private',
						'file_ext'    => '.pem',
						'description' => __( 'Private key file created to authenticate this site with Xero.', 'woocommerce-xero' ),
					),
				)
			);
		}

		// Prepare branding theme list items.
		$branding_themes_list     = array();
		$branding_themes_list[''] = __( 'Select', 'woocommerce-xero' );
		$branding_themes          = get_option( 'xero_branding_themes' );
		if ( $branding_themes ) {
			$branding_themes_list = array_merge( $branding_themes_list, $branding_themes );
		}

		$this->settings = array_merge(
			$this->settings,
			array(
				// Invoice Prefix.
				'invoice_prefix'           => array(
					'title'       => __( 'Invoice Prefix', 'woocommerce-xero' ),
					'default'     => '',
					'type'        => 'text',
					'description' => __( 'Allow you to prefix all your invoices.', 'woocommerce-xero' ),
				),
				// Accounts.
				'sales_account'            => array(
					'title'       => __( 'Sales Account', 'woocommerce-xero' ),
					'default'     => '',
					'type'        => 'text',
					'description' => __( 'Code for Xero account to track sales.', 'woocommerce-xero' ),
					'mandatory'   => true,
				),
				'shipping_account'         => array(
					'title'       => __( 'Shipping Account', 'woocommerce-xero' ),
					'default'     => '',
					'type'        => 'text',
					'description' => __( 'Code for Xero account to track shipping charges.', 'woocommerce-xero' ),
					'mandatory'   => true,
				),
				'fees_account'             => array(
					'title'       => __( 'Fees Account', 'woocommerce-xero' ),
					'default'     => '',
					'type'        => 'text',
					/* translators: Placeholders %1$s - opening HTML <a> link tag, closing HTML </a> link tag */
					'description' => sprintf( __( 'Code for Xero account to allow fees. This account represents the fees created by the %1$sWooCommerce Fees API%2$s.', 'woocommerce-xero' ), '<a href="https://docs.woocommerce.com/document/add-a-surcharge-to-cart-and-checkout-uses-fees-api/" target="_blank">', '</a>' ),
					'mandatory'   => true,
				),
				'payment_account'          => array(
					'title'       => __( 'Payment Account', 'woocommerce-xero' ),
					'default'     => '',
					'type'        => 'text',
					'description' => __( 'Code for Xero account to track payments received.', 'woocommerce-xero' ),
					'mandatory'   => true,
				),
				'rounding_account'         => array(
					'title'       => __( 'Rounding Account', 'woocommerce-xero' ),
					'default'     => '',
					'type'        => 'text',
					'description' => __( 'Code for Xero account to allow an adjustment entry for rounding.', 'woocommerce-xero' ),
					'mandatory'   => true,
				),
				// Misc settings
				'send_invoices'            => array(
					'title'       => __( 'Send Invoices', 'woocommerce-xero' ),
					'default'     => 'manual',
					'type'        => 'select',
					'description' => __( 'Send Invoices manually (from the order\'s action menu), on creation (when the order is created), or on completion (when order status is changed to completed).', 'woocommerce-xero' ),
					'options'     => array(
						'manual'             => __( 'Manually', 'woocommerce-xero' ),
						'creation'           => __( 'On Order Creation', 'woocommerce-xero' ),
						'payment_completion' => __( 'On Payment Completion', 'woocommerce-xero' ),
						'on'                 => __( 'On Order Completion', 'woocommerce-xero' ),
					),
				),
				'send_payments'            => array(
					'title'       => __( 'Send Payments', 'woocommerce-xero' ),
					'default'     => 'off',
					'type'        => 'select',
					'description' => __( 'Send Payments manually or automatically when order is completed. This may need to be turned off if you sync via a separate integration such as PayPal.', 'woocommerce-xero' ),
					'options'     => array(
						'manual'             => __( 'Manually', 'woocommerce-xero' ),
						'payment_completion' => __( 'On Payment Completion', 'woocommerce-xero' ),
						'on'                 => __( 'On Order Completion', 'woocommerce-xero' ),
					),
				),
				'treat_shipping_as'        => array(
					'title'       => __( 'Treat Shipping As', 'woocommerce-xero' ),
					'default'     => 'expense',
					'type'        => 'select',
					'description' => __( 'Set this to correspond to your Xero shipping account\'s type.', 'woocommerce-xero' ),
					'options'     => array(
						'income'  => __( 'Income / Revenue / Sales', 'woocommerce-xero' ),
						'expense' => __( 'Expense', 'woocommerce-xero' ),
					),
				),
				'treat_fees_as'            => array(
					'title'       => __( 'Treat Fees As', 'woocommerce-xero' ),
					'default'     => 'expense',
					'type'        => 'select',
					'description' => __( 'Set this to correspond to your Xero fees account\'s type.', 'woocommerce-xero' ),
					'options'     => array(
						'income'  => __( 'Income / Revenue / Sales', 'woocommerce-xero' ),
						'expense' => __( 'Expense', 'woocommerce-xero' ),
					),
				),
				'branding_theme'           => array(
					'title'       => __( 'Xero Branding theme', 'woocommerce-xero' ),
					'default'     => '',
					'type'        => 'select',
					'description' => __( 'Set a default branding theme for Xero invoices. Refresh the page to see updated list.', 'woocommerce-xero' ),
					'options'     => $branding_themes_list,
				),
				'match_zero_vat_tax_rates' => array(
					'title'       => __( 'Match zero value tax rates', 'woocommerce-xero' ),
					'default'     => 'off',
					'type'        => 'checkbox',
					'description' => __( 'If the integration is having trouble matching up your tax exempt line items with a tax exempt Xero tax rate, enable this and follow the <a href="https://docs.woocommerce.com/document/xero/#line-items-without-vat-applied-appear-as-zero-rated-ec-services-in-xero-invoices" target="_blank">instructions to force match the WooCommerce tax exempt rates to Xero tax exempt rates.</a>', 'woocommerce-xero' ),
				),
				'four_decimals'            => array(
					'title'       => __( 'Four Decimal Places', 'woocommerce-xero' ),
					'default'     => 'off',
					'type'        => 'checkbox',
					'description' => __( 'Use four decimal places for unit prices instead of two.', 'woocommerce-xero' ),
				),
				'export_zero_amount'       => array(
					'title'       => __( 'Orders with zero total', 'woocommerce-xero' ),
					'default'     => 'off',
					'type'        => 'checkbox',
					'description' => __( 'Export orders with zero total.', 'woocommerce-xero' ),
				),
				'send_inventory'           => array(
					'title'       => __( 'Send Inventory Items', 'woocommerce-xero' ),
					'default'     => 'off',
					'type'        => 'checkbox',
					'description' => __( 'Send Item Code field with invoices. If this is enabled then each product must have a SKU defined and be setup as an <a href="https://central.xero.com/s/article/Add-an-inventory-item" target="_blank">inventory item</a> in Xero.', 'woocommerce-xero' ),
				),
				'append_email_to_contact'  => array(
					'title'       => esc_html__( 'Use customer email in contact name', 'woocommerce-xero' ),
					'default'     => 'on',
					'type'        => 'checkbox',
					'description' => esc_html__( 'Append customer email to contact name to ensure uniqueness and prevent duplicates. Email will be appended only if a contact with the same name exists.', 'woocommerce-xero' ),
				),
				'debug'                    => array(
					'title'       => __( 'Debug', 'woocommerce-xero' ),
					'default'     => 'off',
					'type'        => 'checkbox',
					'description' => __( 'Enable logging. Log file is located at: /wc-logs/. <br> <strong>Note: this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.</strong>', 'woocommerce-xero' ),
				),
			)
		);

		$this->migrate_existing_keys();
		$this->maybe_disconnect_from_xero();
		$this->key_file_delete_result = $this->delete_old_key_file();
		if ( WC_XR_OAuth20::can_use_oauth20() ) {
			$this->cleanup_old_options();
		}
	}

	/**
	 * Delete old options, we do this after migration ( even if it is in theory not needed at this stage ).
	 * This way we need to deal only with one options scheme.
	 *
	 * @since 1.7.24
	 */
	private function cleanup_old_options() {
		foreach ( array( 'consumer_key', 'consumer_secret', 'public_key_content', 'private_key_content' ) as $option ) {
			delete_option( self::OPTION_PREFIX . $option );
		}
	}

	/**
	 * Checks if we have any OAuth1.0 options set.
	 *
	 * @since 1.7.24
	 */
	private function oauth10_setup_params_exist() {
		foreach ( array( 'consumer_key', 'consumer_secret', 'public_key_content', 'private_key_content' ) as $option ) {
			$option_value = get_option( self::OPTION_PREFIX . $option, false );
			if ( false !== $option_value ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Migrate key file(s) contents to option database.
	 *
	 * Copies key content into DB from the files pointed to by
	 * 'private_key' and 'public_key'. Key content is placed in
	 * new option entries: 'private_key_content' and 'public_key_content'.
	 * 'private_key' and 'public_key' keys will be deleted only
	 * if they do not point to a valid key file. Key files
	 * are deleted in {@see delete_old_key_files()}.
	 *
	 * @since 1.7.13
	 *
	 * @return void
	 */
	public function migrate_existing_keys() {
		foreach ( array( 'private_key', 'public_key' ) as $key_postfix ) {
			$old_key_name     = self::OPTION_PREFIX . $key_postfix;
			$content_key_name = $old_key_name . '_content';
			$new_key_content  = get_option( $content_key_name );
			$key_file_path    = get_option( $old_key_name );

			if ( false !== $key_file_path ) {
				if ( file_exists( $key_file_path ) ) {
					// If new {@since 1.7.13} key has been set yet.
					if ( false === $new_key_content ) {
						$key_file_content = $this->read_key_file( $key_file_path );
						if ( ! empty( $key_file_content ) && empty( $new_key_content ) ) {
							// Save key content from file to db.
							update_option( $content_key_name, $key_file_content );
							$this->set_upload_info( $content_key_name, basename( $key_file_path ) );
						}
					}
				} else {
					// Just delete the option, since the file it's pointing to does not exist.
					delete_option( $old_key_name );
				}
			}
		}
	}

	/**
	 * Saves upload info to options db.
	 *
	 * Saves key file name and upload timestamp to options db so it can be reported back to user.
	 *
	 * @since 1.7.13
	 *
	 * @param string $content_key_name key name where content of key is saved (e.g. 'wc_private_key_content').
	 * @param string $filename         filename of uploaded file.
	 *
	 * @return void
	 */
	public function set_upload_info( $content_key_name, $filename ) {
		$upload_info_key = $content_key_name . self::UPLOAD_INFO_POSTFIX;

		update_option(
			$upload_info_key,
			array(
				'upload_timestamp' => current_time( 'timestamp' ),
				'upload_filename'  => $filename,
			)
		);
	}

	/**
	 * Get formatted string for upload info.
	 *
	 * Returns a human-readable string for what filename was uploaded and when.
	 *
	 * @since 1.7.13
	 *
	 * @param string $content_key_name key name where content of key is saved (e.g. 'wc_private_key_content').
	 *
	 * @return string Filename and upload datetime.
	 */
	public function get_upload_info_string( $content_key_name ) {
		$upload_info_key = $content_key_name . self::UPLOAD_INFO_POSTFIX;
		$upload_info     = get_option( $upload_info_key );

		if ( ! empty( $upload_info ) ) {
			$format           = get_option( 'time_format' ) . ', ' . get_option( 'date_format' );
			$upload_date_time = date_i18n( $format, $upload_info['upload_timestamp'] );
			return sprintf( __( 'Using %1$s uploaded at %2$s', 'woocommerce-xero' ), $upload_info['upload_filename'], $upload_date_time );
		}
		return '';
	}

	/**
	 * Handle delete requests from Delete <filename> button push.
	 *
	 * Deletes key file. Once key file is deleted, the option
	 * field will be cleaned up in {@see migrate_existing_keys}.
	 *
	 * @see key_migration_notice()
	 * @since 1.7.13
	 *
	 * @return array {
	 *     Key file deletion result.
	 *
	 *     @type string $result   Result of delete ('error' / 'success').
	 *     @type string $key_file Full file path of key file to be deleted.
	 * }
	 */
	public function delete_old_key_file() {
		$key_file_path = '';
		$result        = '';
		if ( isset( $_POST['delete_key_file'] ) && current_user_can( 'manage_options' ) ) {
			$key_name      = sanitize_text_field( $_POST['delete_key_file'] );
			$key_file_path = get_option( $key_name );
			if ( ! empty( $key_file_path ) ) {
				$delete_result = unlink( $key_file_path );
				if ( false === $delete_result ) {
					$result = 'error';
				} else {
					$result = 'success';
				}
			}
		}
		return array(
			'result'   => $result,
			'key_file' => $key_file_path,
		);
	}

	/**
	 * Disconnect from Xero.
	 *
	 * @since 1.7.14
	 */
	public function maybe_disconnect_from_xero() {
		if ( isset( $_POST['disconnect_from_xero'] ) && current_user_can( 'manage_options' ) ) {
			if (
				! isset( $_POST['wc_xero_disconnect_nonce'] )
				|| ! wp_verify_nonce( $_POST['wc_xero_disconnect_nonce'], 'wc_xero_disconnect' )
			) {
				echo '<div>' . esc_html__( 'Nonce verification failed!', 'woocommerce-xero' ) . '</div>';
				exit;
			} else {
				WC_XR_OAuth20::get_instance()->clear_connection_status();
			}
		}
	}

	/**
	 * Reads key file contents.
	 *
	 * @since 1.7.13
	 *
	 * @param string Path to key file.
	 * @return string Key content.
	 */
	public function read_key_file( $file_path ) {
		$fp            = fopen( $file_path, 'r' );
		$file_contents = fread( $fp, 8192 );
		fclose( $fp );
		return $file_contents;
	}

	/**
	 * Adds manage_woocommerce capability to settings so that
	 * any roles with this capabilitity will be able to save the settings
	 */
	public function add_save_capability( $capability ) {
		return 'manage_woocommerce';
	}

	/**
	 * Setup the required settings hooks
	 */
	public function setup_hooks() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_item_oauth' ) );
		add_action( 'admin_notices', array( $this, 'key_migration_notice' ) );
		add_action( 'admin_notices', array( $this, 'oauth20_migration_notice' ) );

		// Register menu items in the new WooCommerce navigation.
		add_action( 'admin_menu', array( $this, 'register_navigation_items' ) );

		// If secret or key were changed we don't want to internally disconnect.
		add_filter( 'update_option_' . self::OPTION_PREFIX . 'client_id', array( $this, 'maybe_clear_oauth20_settings' ), 10, 2 );
		add_filter( 'update_option_' . self::OPTION_PREFIX . 'client_secret', array( $this, 'maybe_clear_oauth20_settings' ), 10, 2 );
	}

	/**
	 * Get an option
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function get_option( $key ) {

		if ( isset( $this->override[ $key ] ) ) {
			return $this->override[ $key ];
		}

		return get_option( self::OPTION_PREFIX . $key, $this->settings[ $key ]['default'] );
	}

	/**
	 * settings_init()
	 *
	 * @access public
	 * @return void
	 */
	public function register_settings() {

		// Add section
		add_settings_section(
			'wc_xero_settings',
			__( 'Xero Settings', 'woocommerce-xero' ),
			array(
				$this,
				'settings_intro',
			),
			'woocommerce_xero'
		);

		// Add setting fields
		foreach ( $this->settings as $key => $option ) {

			// Add setting fields
			add_settings_field(
				self::OPTION_PREFIX . $key,
				$option['title'],
				array(
					$this,
					'input_' . $option['type'],
				),
				'woocommerce_xero',
				'wc_xero_settings',
				array(
					'key'    => $key,
					'option' => $option,
				)
			);

			if ( 'key_file' === $option['type'] ) {
				add_filter( 'pre_update_option_' . self::OPTION_PREFIX . $key, array( $this, 'handle_key_file_upload' ), 10, 3 );
			}

			// Register setting
			register_setting( 'woocommerce_xero', self::OPTION_PREFIX . $key );

		}

		// Check if API is ready.
		$client_id     = get_option( 'wc_xero_client_id', '' );
		$client_secret = get_option( 'wc_xero_client_secret', '' );
		$xero_oauth    = WC_XR_OAuth20::get_instance( $client_id, $client_secret );

		// Update Branding Themes, only on the Xero settings page.
		if ( $xero_oauth->is_api_ready() && 'Xero' === get_admin_page_title() ) {
			$this->update_branding_themes();
		}
	}

	/**
	 * Get Xero branding themes from API and store in the database.
	 */
	private function update_branding_themes() {
		try {
			// Make a call.
			$org_request = new WC_XR_Request_Branding_Themes( new static() );
			$org_request->do_request();
			$xml_response = $org_request->get_response_body_xml();

			$branding_themes = array();
			if ( 'OK' === (string) $xml_response->Status ) {
				if ( isset( $xml_response->BrandingThemes->BrandingTheme ) ) {
					foreach ( $xml_response->BrandingThemes->BrandingTheme as $branding_theme ) {
						$branding_themes[ (string) esc_html( $branding_theme->BrandingThemeID ) ] = (string) esc_html( $branding_theme->Name );
					}
				}
			}

			update_option( 'xero_branding_themes', $branding_themes );
		} catch ( \Exception $e ) {
			add_action( 'admin_notices', array( $this, 'branding_themes_connection_notice' ) );
		}
	}

	/**
	 * Add menu item
	 *
	 * @return void
	 */
	public function add_menu_item() {
		$sub_menu_page = add_submenu_page(
			'woocommerce',
			__( 'Xero', 'woocommerce-xero' ),
			__( 'Xero', 'woocommerce-xero' ),
			'manage_woocommerce',
			'woocommerce_xero',
			array(
				$this,
				'options_page',
			)
		);

		add_action( 'load-' . $sub_menu_page, array( $this, 'enqueue_style' ) );
		add_action( 'load-' . $sub_menu_page, array( $this, 'enqueue_xero_style_style' ) );
	}

	public function enqueue_style() {
		global $woocommerce;
		wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
	}

	/**
	 * Register the navigation items in the WooCommerce navigation.
	 */
	public function register_navigation_items() {
		if (
			! method_exists( Menu::class, 'add_setting_item' )
		) {
			return;
		}

		Menu::add_setting_item(
			array(
				'id'         => 'woocommerce_xero',
				'title'      => __( 'Xero', 'woocommerce-xero' ),
				'capability' => 'manage_woocommerce',
				'url'        => 'woocommerce_xero',
			)
		);
	}

	/**
	 * Enqueue styles used by Xero.
	 */
	public function enqueue_xero_style_style() {
		wp_enqueue_style( 'woocomemrce_xero_admin_styles', plugin_dir_url( WC_Xero::get_plugin_file() ) . 'assets/css/admin.css' );
	}

	/**
	 * Add menu item.
	 *
	 * @return void
	 */
	public function add_menu_item_oauth() {
		$sub_menu_page = add_submenu_page(
			null,
			__( 'Xero OAuth', 'woocommerce-xero' ),
			__( 'Xero OAuth', 'woocommerce-xero' ),
			'manage_woocommerce',
			'woocommerce_xero_oauth',
			array(
				$this,
				'oauth_redirect',
			)
		);

		// Use this if we would want redirect styling.
		add_action( 'load-' . $sub_menu_page, array( $this, 'enqueue_style' ) );
		add_action( 'load-' . $sub_menu_page, array( $this, 'enqueue_xero_style_style' ) );
	}

	/**
	 * Implement redirect page.
	 */
	public function oauth_redirect() {
		require_once 'class-wc-xr-oauth20.php';

		$client_id       = $this->get_option( 'client_id' );
		$client_secret   = $this->get_option( 'client_secret' );
		$xero_oauth      = WC_XR_OAuth20::get_instance( $client_id, $client_secret );
		$state_transient = get_transient( 'wc_xero_oauth2state' );

		// If we don't have an authorization code then get one.
		if ( ! isset( $_GET['code'] ) ) {
			?>
			<div class="wrap woocommerce">
			<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br/></div>
					<h2><?php _e( 'Xero OAuth', 'woocommerce-xero' ); ?></h2>
					Something went wrong - token not received!
			</div>
			<?php
			// Check given state against previously stored one to mitigate CSRF attack.
		} elseif ( empty( $_GET['state'] ) || ( $_GET['state'] !== $state_transient ) ) {
			?>
			<div class="wrap woocommerce">
			<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br/></div>
					<h2><?php _e( 'Xero OAuth', 'woocommerce-xero' ); ?></h2>
					Something went wrong - previous state is different. CSRF prevention.
			</div>
			<?php
		} else {
			try {
				$authorization_code = $_GET['code'];
				$xero_oauth->get_access_token_using_authorization_code( $authorization_code );
				$this->print_xero_connection_status( $xero_oauth->get_connection_status() );
				// From now on OAuth20 will be used exclusively.
				WC_XR_OAuth20::mark_successful_connection();

			} catch ( \League\OAuth2\Client\Provider\Exception\IdentityProviderException $e ) {
				if ( 'invalid_grant' === $e->getResponseBody()['error'] ) {
					$this->print_xero_connection_status( array( 'errorMessage' => 'invalid_grant' ) );
				} else {
					?>
					<div class="wrap woocommerce">
					<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br/></div>
							<h2><?php _e( 'Xero OAuth', 'woocommerce-xero' ); ?></h2>
							Callback failed:<br/>
							<?php var_dump( $e ); ?>
					</div>
					<?php
				}
			}
		}
		// Go back link.
		echo '</br><a href="' . admin_url( 'admin.php?page=woocommerce_xero' ) . '">Go back to Xero settings page.</a>';
	}

	/**
	 * The options page
	 */
	public function options_page() {
		?>
		<div class="wrap woocommerce">
			<form method="post" id="mainform" enctype="multipart/form-data" action="options.php">
				<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br/></div>
				<h2><?php _e( 'Xero for WooCommerce', 'woocommerce-xero' ); ?></h2>

				<?php
				if ( isset( $_GET['settings-updated'] ) && ( $_GET['settings-updated'] == 'true' ) ) {
					echo '<div id="message" class="updated fade"><p><strong>' . __( 'Your settings have been saved.', 'woocommerce-xero' ) . '</strong></p></div>';

					// Show errors if missing any mandatory fields.
					$mandatory_fields = array();
					foreach ( $this->settings as $key => $field ) {
						if ( isset( $field['mandatory'] ) && $field['mandatory'] && ! get_option( 'wc_xero_' . $key ) ) {
							$mandatory_fields[ $key ] = $field['title'];
						}
					}

					if ( count( $mandatory_fields ) ) {
						echo '<div id="message" class="error fade">
							<p>
								<strong>' . esc_html__( 'Missing required fields', 'woocommerce-xero' ) . ': </strong>
								<i>' . implode( '</i>, <i>', array_map( 'esc_html', $mandatory_fields ) ) . '</i>
							</p>
						</div>';
					}
				} elseif ( isset( $_GET['settings-updated'] ) && ( $_GET['settings-updated'] == 'false' ) ) {
					echo '<div id="message" class="error fade"><p><strong>' . __( 'There was an error saving your settings.', 'woocommerce-xero' ) . '</strong></p></div>';
				}
				?>

				<?php settings_fields( 'woocommerce_xero' ); ?>
				<?php do_settings_sections( 'woocommerce_xero' ); ?>
				<p class="submit"><input type="submit" class="button-primary" value="Save"/></p>
			</form>
		</div>
		<?php
	}

	/**
	 * Settings intro
	 */
	public function settings_intro() {
		echo '<p>';
		esc_html_e( 'Settings for your Xero account including security keys and default account numbers.', 'woocommerce-xero' );
		echo '<br/>';
		/* translators: %1$s: opening anchor tag; %2$s: closing anchor tag */
		printf( esc_html__( 'Please ensure you\'re following all %1$srequirements%2$s prior to setup.', 'woocommerce-xero' ), '<a href="https://woocommerce.com/document/xero/#requirements" target="_blank">', '</a>' );
		echo '<br/>';
		/* translators: %1$s: opening strong tag; %2$s: closing strong tag */
		printf( esc_html__( '%1$sAll%2$s text fields are required for the integration to work properly.', 'woocommerce-xero' ), '<strong>', '</strong>' );
		echo '</p>';
	}

	/**
	 * Key file input field.
	 *
	 * Generates file input for key file upload.
	 *
	 * @param $args
	 */
	public function input_key_file( $args ) {
		$input_name = self::OPTION_PREFIX . $args['key'];
		$file_ext   = $args['option']['file_ext'];

		// File input field.
		echo '<input type="file" name="' . esc_attr( $input_name ) . '" id="' . esc_attr( $input_name ) . '" accept="' . esc_attr( $file_ext ) . '"/>';

		echo '<p class="description">' . esc_html( $args['option']['description'] ) . '</p>';

		$key_content = $this->get_option( $args['key'] );
		if ( ! empty( $key_content ) ) {
			echo '<p style="margin-top:15px;"><span style="padding: .5em; background-color: #4AB915; color: #fff; font-weight: bold;">' . esc_html( $this->get_upload_info_string( $input_name ) ) . '</span></p>';
		} else {
			echo '<p style="margin-top:15px;"><span style="padding: .5em; background-color: #bc0b0b; color: #fff; font-weight: bold;">' . __( 'Key not set', 'woocommerce-xero' ) . '</span></p>';
		}
	}

	/**
	 * Key file input field.
	 *
	 * Generates file input for key file upload.
	 *
	 * @param $args
	 */
	public function input_oauth( $args ) {
		require_once __DIR__ . '/../lib/packages/autoload.php';
		require_once 'class-wc-xr-oauth20.php';

		$client_id     = $this->get_option( 'client_id' );
		$client_secret = $this->get_option( 'client_secret' );
		$xero_oauth    = WC_XR_OAuth20::get_instance( $client_id, $client_secret );
		$data_complete = (bool) $client_id && (bool) $client_secret;

		$options = array(
			'scope' => array( 'openid email profile offline_access accounting.settings accounting.transactions accounting.contacts accounting.journals.read accounting.reports.read accounting.attachments' ),
		);

		// Fetch the authorization URL from the provider; this returns the urlAuthorize option and generates and applies any necessary parameters (e.g. state).
		$authorization_url = $data_complete ? WC_XR_OAuth20::get_authorization_url( $options ) : '#';

		// Get the state generated for you and store it in the transient.
		set_transient( 'wc_xero_oauth2state', $xero_oauth->get_state(), 10 * MINUTE_IN_SECONDS );

		$connection_status = $data_complete ? WC_XR_OAuth20::get_connection_status() : false;

		if ( $data_complete && array_key_exists( 'correctRequest', $connection_status ) && $connection_status['correctRequest'] ) {
			?>
			<div class="wc-xero-oauth-data-complete">
				<form method="post">
					<button class="wc-xero-oauth-disconnect-button" type="submit" name="disconnect_from_xero" value="xero-disconnect">
					<?php wp_nonce_field( 'wc_xero_disconnect', 'wc_xero_disconnect_nonce' ); ?>
					<?php echo esc_html__( 'Disconnect from Xero', 'woocommerce-xero' ); ?>
					</button>
				</form>
			</div>
			<?php
		} elseif ( $data_complete ) {
			echo '<div class="wc-xero-oauth-data-complete">';
			// Redirect the user to the authorization URL.
			echo '<span data-xero-sso data-href="' . $authorization_url . '" data-label="' . esc_html__( 'Sign in with Xero', 'woocommerce-xero' ) . '"></span>';
			echo '<script src="https://edge.xero.com/platform/sso/xero-sso.js" async defer></script>';
			echo '</div>';
		} else {
			/* translators: %1$s: line break tag; %2$s: opening anchor tag; %3$s: closing anchor tag; */
			echo '<span><b>' . sprintf( esc_html( __( 'Please fill in the Client ID and the Client Secret fields first and save before continuing.%1$s Also, ensure you\'re following all %2$srequirements%3$s prior to setup.', 'woocommerce-xero' ) ), '<br>', '<a href="https://woocommerce.com/document/xero/#requirements" target="_blank">', '</a>' ) . '</b></span><br/>';
		}

		if ( $data_complete ) {
			$this->print_xero_connection_status_in_settings( $connection_status );
		}
	}

	/**
	 * Text setting field
	 *
	 * @param array $args
	 */
	public function input_text( $args ) {
		echo '<input type="text" name="' . self::OPTION_PREFIX . $args['key'] . '" id="' . self::OPTION_PREFIX . $args['key'] . '" value="' . $this->get_option( $args['key'] ) . '" />';
		echo '<p class="description">' . $args['option']['description'] . '</p>';
	}

	/**
	 * Text setting field
	 *
	 * @param array $args Field arguments.
	 */
	public function input_text_oauth( $args ) {
		require_once 'class-wc-xr-oauth20.php';
		$this->input_text( $args );
		echo '<p>' . _e( 'Please use the following url as your redirect url when creating a Xero application:', 'woocommerce-xero' ) . '</p>';
		echo WC_XR_OAuth20::build_redirect_uri();
		echo '<br/></br>';
	}


	/**
	 * Checkbox setting field
	 *
	 * @param array $args
	 */
	public function input_checkbox( $args ) {
		echo '<input type="checkbox" name="' . self::OPTION_PREFIX . $args['key'] . '" id="' . self::OPTION_PREFIX . $args['key'] . '" ' . checked( 'on', $this->get_option( $args['key'] ), false ) . ' /> ';
		echo '<p class="description">' . $args['option']['description'] . '</p>';
	}

	/**
	 * Drop down setting field
	 *
	 * @param array $args
	 */
	public function input_select( $args ) {
		$option = $this->get_option( $args['key'] );

		$name = esc_attr( self::OPTION_PREFIX . $args['key'] );
		$id   = esc_attr( self::OPTION_PREFIX . $args['key'] );
		echo "<select name='$name' id='$id'>";

		foreach ( $args['option']['options'] as $key => $value ) {
			$selected = selected( $option, $key, false );
			$text     = esc_html( $value );
			$val      = esc_attr( $key );
			echo "<option value='$val' $selected>$text</option>";
		}

		echo '</select>';
		echo '<p class="description">' . esc_html( $args['option']['description'] ) . '</p>';
	}

	/**
	 * Helper function to print connection status.
	 *
	 * @param object $status
	 */
	public function print_xero_connection_status( $status ) {
		echo '<div class="wc-xero-oauth-redirect-page">WooCommerce Xero authorization redirect page.</div>';
		echo '<div class="wc-xero-status">';
		echo '<img class="wc-xero-logo" src= ' . WC_XERO_ABSURL . 'assets/xero_logo_blue.png>';
		echo '</br><span><b>Connection status:</b>';
		if ( array_key_exists( 'correctRequest', $status ) ) {
			echo '<span class="wc-xero-oauth-connection-ok"><b> [OK]</b></span></span>';
			echo '<div>You are connected to <b>' . $status['connectedCompany'] . '</b> organisation.</div>';
		} else {
			if ( $status['errorMessage'] === 'invalid_grant' ) {
				echo '<span class="wc-xero-oauth-connection-error"><b> [ERROR]</b></span></span>';
				echo '<div>Cannot request the access token, please connect your application again!</div>';
				echo '<div>For more information check the documentation page : <a href="https://docs.woocommerce.com/document/xero/#section-3">WooCommerce and Xero setup</a></div>';
			} elseif ( $status['errorMessage'] === 'no_connection' ) {
				echo '<div>Application not authorized with Xero! Pleas click Sign in with Xero button.</div>';
			} else {
				echo $status['errorMessage'] . '</span>';
			}
		}
		echo '</div">';
	}

	/**
	 * Helper function to print connection status on settings page.
	 *
	 * @param object $status
	 */
	public function print_xero_connection_status_in_settings( $status ) {
		echo '</br><span><b>Connection status:</b>';
		if ( array_key_exists( 'correctRequest', $status ) && $status['correctRequest'] ) {
			echo '<span class="wc-xero-oauth-connection-ok"><b> [OK]</b></span></span>';
			echo '<div>You are connected to <b>' . $status['connectedCompany'] . '</b> organisation.</div>';
		} else {
			if ( $status['errorMessage'] === 'invalid_grant' ) {
				echo '<span class="wc-xero-oauth-connection-error"><b> [ERROR]</b></span></span>';
				echo '<div>Cannot request the access token, please connect your application again!</div>';
				echo '<div>For more information check the documentation page : <a href="https://docs.woocommerce.com/document/xero/#section-3">WooCommerce and Xero setup</a></div>';
			} elseif ( $status['errorMessage'] === 'no_connection' ) {
				echo '<div>Application not authorized with Xero! Please click Sign in with Xero button.</div>';
			} else {
				echo $status['errorMessage'] . '</span>';
			}
		}
	}

	/**
	 * Check if options have been changed.
	 *
	 * @param string $new_value New option.
	 * @param string $old_value Old option.
	 */
	public function maybe_clear_oauth20_settings( $new_value, $old_value ) {
		if ( $new_value != $old_value ) { // phpcs:ignore
			WC_XR_OAuth20::get_instance()->clear_connection_status();
		}
	}

	/**
	 * Handle key file upload.
	 *
	 * Retrieves key content from user uploaded as string
	 * and returns it so that's stored as the option value
	 * instead of just the file name. This should be hooked
	 * into the 'pre_update_option_' hook for 'key_file'
	 * inputs so key data can be retrieved.
	 *
	 * @since 1.7.13
	 *
	 * @param string $new_value filename of uploaded file
	 * @param string $old_value original value of key.
	 * @param string $option_name full name of option.
	 *
	 * @return string key content to directly store in option db.
	 */
	public function handle_key_file_upload( $new_value, $old_value, $option_name ) {
		if ( ! isset( $_FILES[ $option_name ] ) ) {
			return $old_value;
		}
		add_filter( 'upload_mimes', array( $this, 'override_upload_mimes' ), 10, 1 );

		$overrides = array(
			'test_form' => false,
			'mimes'     => $this->valid_filetypes,
		);
		$import    = $_FILES[ $option_name ];
		$upload    = wp_handle_upload( $import, $overrides );

		if ( isset( $upload['error'] ) ) {
			return $old_value;
		}

		$key_content = $this->read_key_file( $upload['file'] );
		if ( empty( $key_content ) ) {
			return $old_value;
		}

		$this->set_upload_info( $option_name, $import['name'] );
		return $key_content;
	}

	/**
	 * Notify user is key file(s) still exists.
	 *
	 * @since 1.7.13
	 *
	 * @return void
	 */
	public function key_migration_notice() {
		if ( current_user_can( 'manage_options' ) ) {
			foreach ( array( 'wc_xero_public_key', 'wc_xero_private_key' ) as $key_name ) {
				$key_file_path = esc_html( get_option( $key_name ) );
				if ( false !== $key_file_path && file_exists( $key_file_path ) ) {
					$filename = basename( $key_file_path );
					?>
					<div class="notice notice-warning">
						<p><?php echo esc_html( sprintf( __( 'Xero has securely saved the contents of key file %s to the database and no longer requires it.', 'woocommerce-xero' ), $key_file_path ) ); ?></p>
						<form method="post">
							<button type="submit" name="delete_key_file" value="<?php echo esc_attr( $key_name ); ?>">
								<?php echo esc_html( sprintf( __( 'Delete %s', 'woocommerce-xero' ), $filename ) ); ?>
							</button>
						</form>
					</div>
					<?php
				}
			}

			// Show file deletion result if file was deleted.
			if ( ! empty( $this->key_file_delete_result['result'] ) ) {
				$key_file_path = $this->key_file_delete_result['key_file'];
				if ( 'error' === $this->key_file_delete_result['result'] ) {
					?>
					<div class="error">
						<p>
							<?php echo esc_html( sprintf( __( 'Xero could not delete %s. Check permissions and try again.', 'woocommerce-xero' ), $key_file_path ) ); ?>
						</p>
					</div>
					<?php
				} elseif ( 'success' === $this->key_file_delete_result['result'] ) {
					?>
					<div class="updated">
						<p>
							<?php echo esc_html( sprintf( __( 'Xero successfully deleted %s.', 'woocommerce-xero' ), $key_file_path ) ); ?>
						</p>
					</div>
					<?php
				}
			}
		}
	}

	/**
	 * Notify users that connection using keys will not be possible.
	 *
	 * @since 1.7.13
	 *
	 * @return void
	 */
	public function oauth20_migration_notice() {
		if ( current_user_can( 'manage_options' ) && ! WC_XR_OAuth20::can_use_oauth20() && $this->oauth10_setup_params_exist() ) {
			?>
			<div class="notice notice-warning">
				<p><?php echo esc_html( __( 'Xero authentication using keys is in the process of being deprecated and new private apps can no longer be created.', 'woocommerce-xero' ) ); ?></br>
				<?php echo esc_html( __( 'Please use new flow and the button available in Xero settings to authorize your application.', 'woocommerce-xero' ) ); ?></p>
				<?php echo '<a href="' . admin_url( 'admin.php?page=woocommerce_xero' ) . '">Go to Xero settings page.</a>'; ?></br>
			</div>
			<?php
		}
	}

	/**
	 * Notify users that the branding themes connection failed.
	 *
	 * @since 1.7.47
	 *
	 * @return void
	 */
	public function branding_themes_connection_notice() {
		?>
		<div class="notice notice-error">
			<p><?php echo esc_html( __( 'Unable to fetch the Branding Theme details. Please ensure your Xero connection is properly authenticated.', 'woocommerce-xero' ) ); ?></p>
		</div>
		<?php
	}

	/**
	 * Need to send tax inclusive prices to Xero?.
	 *
	 * @since 1.7.42
	 *
	 * @return boolean
	 */
	public function send_tax_inclusive_prices() {
		/**
		 * Filters the behavior of send tax inclusive prices to Xero.
		 */
		return apply_filters( 'woocommerce_xero_send_tax_inclusive_prices', wc_prices_include_tax() );
	}
}

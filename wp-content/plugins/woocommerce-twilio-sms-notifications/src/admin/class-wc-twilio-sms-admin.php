<?php
/**
 * WooCommerce Twilio SMS Notifications
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Twilio SMS Notifications to newer
 * versions in the future. If you wish to customize WooCommerce Twilio SMS Notifications for your
 * needs please refer to http://docs.woocommerce.com/document/twilio-sms-notifications/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Twilio SMS Admin class
 *
 * Loads admin settings page and adds related hooks / filters
 *
 * @since 1.0
 */
class WC_Twilio_SMS_Admin {


	/** @var string id of tab on WooCommerce Settings page */
	public static $tab_id = 'twilio_sms';


	/**
	 * Setup admin class
	 *
	 * @since  1.0
	 */
	public function __construct() {

		/** General Admin Hooks */

		// Add SMS tab
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab'  ), 100 );

		// Show SMS settings page
		add_action( 'woocommerce_settings_twilio_sms', array( $this, 'display_settings' ) );

		// Add admin notices
		add_action( 'admin_notices', array( $this, 'display_notices' ) );

		// Load the scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );

		// save SMS settings
		add_action( 'woocommerce_update_options_' . self::$tab_id, array( $this, 'process_settings' ) );

		// Add custom 'wc_twilio_sms_link' form field type
		add_action( 'woocommerce_admin_field_wc_twilio_sms_link', array( $this, 'add_link_field' ) );

		// add 'Twilio SMS Notifications' item to admin bar menu
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu_item' ), 100 );

		/** Order Admin Hooks */

		// Add 'Send an SMS' meta-box on Order page to send SMS to customer
		add_action( 'add_meta_boxes', array( $this, 'add_order_meta_box' ) );
	}


	/**
	 * Add SMS tab to WooCommerce Settings after 'Email' tab
	 *
	 * @since 1.0
	 * @param array $settings_tabs tabs array sans 'SMS' tab
	 * @return array $settings_tabs now with 100% more 'SMS' tab!
	 */
	public function add_settings_tab( $settings_tabs ) {

		$new_settings_tabs = array();

		foreach ( $settings_tabs as $tab_id => $tab_title ) {

			$new_settings_tabs[ $tab_id ] = $tab_title;

			// Add our tab after 'Email' tab
			if ( 'email' === $tab_id ) {
				$new_settings_tabs[ self::$tab_id ] = __( 'SMS', 'woocommerce-twilio-sms-notifications' );
			}
		}

		return $new_settings_tabs;
	}


	/**
	 * Outputs sections for the Twilio SMS settings tab.
	 *
	 * @since 1.12.0
	 */
	public function display_sections() {
		global $current_section;

		$sections = $this->get_sections();

		if ( empty( $sections ) || 1 === sizeof( $sections ) ) {

			return;
		}

		echo '<ul class="subsubsub">';

		$section_ids = array_keys( $sections );

		foreach ( $sections as $id => $label ) {

			echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=twilio_sms&section=' . sanitize_title( $id ) ) ) . '" class="' . ( $current_section === $id ? 'current' : '' ) . '">' . esc_html( $label ) . '</a> ' . ( end( $section_ids ) === $id ? '' : '|' ) . ' </li>';
		}

		echo '</ul><br class="clear" />';
	}


	/**
	 * Show SMS settings page
	 *
	 * @see woocommerce_admin_fields()
	 * @uses WC_Twilio_SMS_Admin::get_settings() to get settings array
	 * @uses WC_Twilio_SMS_Admin::display_send_test_sms_form() to output 'send test SMS' form
	 * @since 1.0
	 */
	public function display_settings() {
		global $current_section;

		// default to orders section
		if ( ! $current_section ) {
			$current_section = 'general';
		}

		$this->display_sections();

		// display general settings only on General tab
		$settings = ( 'general' === $current_section ) ? self::get_settings() : array();

		/**
		 * Allow actors to change the settings to be displayed.
		 *
		 * @since 1.12.0
		 *
		 * @param array $sections
		 */
		$settings = apply_filters( 'wc_twilio_sms_settings', $settings );

		// output settings
		if ( ! empty ( $settings ) ) {

			woocommerce_admin_fields( $settings );
		}
	}


	/**
	 * Display admin notices.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 */
	public function display_notices() {

		if (    \WC_Twilio_SMS_URL_Shortener::using_shortened_urls()
		     && \WC_Twilio_SMS_URL_Shortener::is_shortener_service( 'google-url-shortener' )
		     &&  wc_twilio_sms()->is_plugin_settings() ) {

			wc_twilio_sms()->get_message_handler()->add_error(
				sprintf(
					/* translators: Placeholders: %1$s, %3$s, %5$s - Opening <a> HTML link tag; %2$s, %4$s, %6$s closing </a> HTML link tag */
					esc_html__( 'Heads up! You are currently using %1$sGoogle\'s URL shortener%2$s for Twilio SMS Notifications, but their service is deprecated, and %3$sFirebase Dynamic Links%4$s is the recommended replacement. %5$sRead more here%6$s about how to migrate to the new service if you wish to continue sending short URLs.', 'woocommerce-twilio-sms-notifications' ),
					'<a href="https://goo.gl/">', '</a>',
					'<a href="https://firebase.google.com/products/dynamic-links/">', '</a>',
					'<a href="https://developers.googleblog.com/2018/03/transitioning-google-url-shortener.html">', '</a>'
				)
			);
		}

		wc_twilio_sms()->get_message_handler()->show_messages();
	}


	/**
	 * Load the scripts and styles.
	 *
	 * TODO: Look for a replacement for the screen ID check here post WC 3.1+. {BR 2017-02-22}
	 *
	 * @since 1.6.0
	 */
	public function enqueue_scripts_and_styles() {

		$screen = get_current_screen();

		// Only enqueue the scripts and styles on the settings page, order edit screen, and product edit screen
		if ( $screen && 'shop_order' !== $screen->id && 'product' !== $screen->id && ! wc_twilio_sms()->is_plugin_settings() ) {
			return;
		}

		wp_enqueue_script( 'wc-twilio-sms-admin', wc_twilio_sms()->get_plugin_url() . '/assets/js/admin/wc-twilio-sms-admin.min.js', array(), WC_Twilio_SMS::VERSION, true );

		wp_localize_script( 'wc-twilio-sms-admin', 'wc_twilio_sms_admin', array(

			// Settings screen
			'test_sms_error_message' => __( 'Please make sure you have entered a mobile phone number and test message.', 'woocommerce-twilio-sms-notifications' ),
			'test_sms_nonce'         => wp_create_nonce( 'wc_twilio_sms_send_test_sms' ),

			// Edit order screen
			'edit_order_id'              => get_the_ID(),
			'toggle_order_updates_nonce' => wp_create_nonce( 'wc_twilio_sms_toggle_order_updates' ),
			'send_order_sms_nonce'       => wp_create_nonce( 'wc_twilio_sms_send_order_sms' ),

			// General
			'assets_url' => esc_url( wc_twilio_sms()->get_framework_assets_url() . '/images/ajax-loader.gif' ),
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
		) );

		wp_enqueue_style( 'wc-twilio-sms-notifications-admin', wc_twilio_sms()->get_plugin_url() . '/assets/css/admin/wc-twilio-sms-notifications.min.css', '', WC_Twilio_SMS::VERSION );
	}


	/**
	 * Add 'Send an SMS' meta-box to Orders page
	 *
	 * @since 1.0
	 */
	public function add_order_meta_box() {

		add_meta_box(
			'wc_twilio_sms_order_meta_box',
			__( 'SMS Messages', 'woocommerce-twilio-sms-notifications' ),
		 	array( $this, 'display_order_meta_box' ),
			'shop_order',
			'side',
			'default'
		);
	}


	/**
	 * Display the 'Send an SMS' meta-box on the Orders page
	 *
	 * TODO Instantiate an order here instead to update meta value post WC 3.1+ {BR 2017-02-22}
	 *
	 * @since 1.0
	 */
	public function display_order_meta_box( $post ) {

		$optin = get_post_meta( $post->ID, '_wc_twilio_sms_optin', true ); ?>

		<p style="margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid #eee;">
			<input id="wc_twilio_sms_toggle_order_updates" type="checkbox" <?php checked( 1, $optin ); ?> />
			<label for="wc_twilio_sms_toggle_order_updates"><?php _e( 'Send automated order updates.', 'woocommerce-twilio-sms-notifications' ); ?></label>
		</p>

		<?php $default_message = apply_filters( 'wc_twilio_sms_notifications_default_admin_sms_message', '' ); ?>

		<p><?php _e( 'Send SMS Message:', 'woocommerce-twilio-sms-notifications' ); ?></p>
		<p><textarea type="text" name="wc_twilio_sms_order_message" id="wc_twilio_sms_order_message" class="input-text" style="width: 100%;" rows="4" value="<?php echo esc_attr( $default_message ); ?>"></textarea></p>
		<p><a class="button tips" id="wc_twilio_sms_order_send_message" data-tip="<?php _e( 'Send an SMS to the billing phone number for this order.', 'woocommerce-twilio-sms-notifications' ); ?>"><?php _e( 'Send SMS', 'woocommerce-twilio-sms-notifications' ); ?></a>
		<span id="wc_twilio_sms_order_message_char_count" style="color: green; float: right; font-size: 16px;">0</span></p>

		<?php
	}


	/**
	 * Update options on SMS settings page
	 *
	 * @see woocommerce_update_options()
	 * @uses WC_Twilio_SMS_Admin::get_settings() to get settings array
	 * @since 1.0
	 */
	public function process_settings() {

		if ( isset( $_GET['section'] ) && 'general' !== $_GET['section'] ) {
			return;
		}

		woocommerce_update_options( self::get_settings() );
	}


	/**
	 * Get sections
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	public function get_sections() {

		$sections = [
			'general' => __( 'General', 'woocommerce-twilio-sms-notifications' ),
		];

		/**
		 * Allow actors to change the sections for the Twilio SMS settings tab.
		 *
		 * @since 1.12.0
		 *
		 * @param array $sections
		 */
		return apply_filters( 'wc_twilio_sms_sections', $sections );
	}


	/**
	 * Build array of plugin settings in format needed to use WC admin settings API
	 *
	 * @see woocommerce_admin_fields()
	 * @see woocommerce_update_options()
	 *
	 * @since 1.0
	 *
	 * @return array settings
	 */
	public static function get_settings() {

		$settings = [

			[
				'name' => __( 'General Settings', 'woocommerce-twilio-sms-notifications' ),
				'type' => 'title'
			],

			[
				'id'       => 'wc_twilio_sms_checkout_optin_checkbox_label',
				'name'     => __( 'Opt-in Checkbox Label', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'Label for the Opt-in checkbox on the Checkout page. Leave blank to disable the opt-in and force ALL customers to receive SMS updates.', 'woocommerce-twilio-sms-notifications' ),
				'css'      => 'min-width: 275px;',
				'default'  => __( 'Please send me order updates via text message', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'text'
			],

			[
				'id'       => 'wc_twilio_sms_checkout_optin_checkbox_default',
				'name'     => __( 'Opt-in Checkbox Default', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'Default status for the Opt-in checkbox on the Checkout page.', 'woocommerce-twilio-sms-notifications' ),
				'std'      => 'unchecked',
				'default'  => 'unchecked',
				'type'     => 'select',
				'options'  => [
					'unchecked' => __( 'Unchecked', 'woocommerce-twilio-sms-notifications' ),
					'checked'   => __( 'Checked', 'woocommerce-twilio-sms-notifications' )
				],
			],

			[
				'id'       => 'wc_twilio_sms_allow_concatenate_messages',
				'name'     => __( 'Concatenate Messages', 'woocommerce-twilio-sms-notifications' ),
				'desc'     => __( 'Enable to let the carrier split a long message into concatenated multiple SMS if the total characters count is beyond a single SMS characters limit.', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'checkbox',
				'default'  => 'no',
			],

			[
				'id'      => 'wc_twilio_sms_shorten_urls',
				'name'    => __( 'Shorten URLs', 'woocommerce-twilio-sms-notifications' ),
				'desc'    => __( 'Enable to automatically shorten links in SMS notifications via a URL shortener service.', 'woocommerce-twilio-sms-notifications' ),
				'default' => 'no',
				'type'    => 'checkbox',
			],

			[
				'id'       => 'wc_twilio_sms_url_shortener_service',
				'name'     => __( 'URL Shortener Service', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'Choose a remote URL Shortener Service to use.', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'select',
				'default'  => \WC_Twilio_SMS_URL_Shortener::get_default_url_shortener_service(),
				'options'  => \WC_Twilio_SMS_URL_Shortener::get_url_shortener_services( true ),
			],

			[
				'id'       => 'wc_twilio_sms_firebase_dynamic_links_api_key',
				'name'     => __( 'Firebase Web API Key', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'Enter your Firebase project API Key.', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'text',
				'default'  => '',
			],

			[
				'id'       => 'wc_twilio_sms_firebase_dynamic_links_domain',
				'name'     => __( 'Firebase Dynamic Links Domain', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'Enter the custom Firebase subdomain to use without prepending "http://" or "https://" to it.', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'text',
				'default'  => '',
			],

			[
				'id'       => 'wc_twilio_sms_google_url_shortener_api_key',
				'name'     => __( 'Google URL Shortener API Key', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'Enter your Google Shortener project API Key.', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'text',
				'default'  => '',
			],

			[ 'type' => 'sectionend' ],

			[
				'name' => __( 'Admin Notifications', 'woocommerce-twilio-sms-notifications' ),
				'type' => 'title'
			],

			[
				'id'      => 'wc_twilio_sms_enable_admin_sms',
				'name'    => __( 'Enable new order SMS admin notifications.', 'woocommerce-twilio-sms-notifications' ),
				'default' => 'no',
				'type'    => 'checkbox'
			],

			[
				'id'          => 'wc_twilio_sms_admin_sms_recipients',
				'name'        => __( 'Admin Mobile Number', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip'    => __( 'Enter the mobile number (starting with the country code) where the New Order SMS should be sent. Send to multiple recipients by separating numbers with commas.', 'woocommerce-twilio-sms-notifications' ),
				'placeholder' => '1-555-867-5309',
				'type'        => 'text'
			],

			[
				'id'       => 'wc_twilio_sms_admin_sms_template',
				'name'     => __( 'Admin SMS Message', 'woocommerce-twilio-sms-notifications' ),
				/* translators: Placeholders: %1$s is <code>, %2$s is </code> */
				'desc'     => sprintf( __( 'Use these tags to customize your message: %1$s%%shop_name%%%2$s, %1$s%%order_id%%%2$s, %1$s%%order_count%%%2$s, %1$s%%order_amount%%%2$s, %1$s%%order_status%%%2$s, %1$s%%billing_name%%%2$s, %1$s%%shipping_name%%%2$s, and %1$s%%shipping_method%%%2$s. Remember that SMS messages may be limited to 160 characters or less.', 'woocommerce-twilio-sms-notifications' ), '<code>', '</code>' ),
				'css'      => 'min-width:500px;',
				'default'  => __( '%shop_name% : You have a new order (%order_id%) for %order_amount%!', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'textarea'
			],

			[ 'type' => 'sectionend' ],

			[
				'name' => __( 'Customer Notifications', 'woocommerce-twilio-sms-notifications' ),
				'type' => 'title'
			],
		];

		$order_statuses = wc_get_order_statuses();

		$settings[] = [
			'id'                => 'wc_twilio_sms_send_sms_order_statuses',
			'name'              => __( 'Order statuses to send SMS notifications for', 'woocommerce-twilio-sms-notifications' ),
			'desc_tip'          => __( 'Orders with these statuses will have SMS notifications sent.', 'woocommerce-twilio-sms-notifications' ),
			'type'              => 'multiselect',
			'options'           => $order_statuses,
			'default'           => array_keys( $order_statuses ),
			'class'             => 'wc-enhanced-select',
			'css'               => 'min-width: 250px',
			'custom_attributes' => [
				'data-placeholder' => __( 'Select statuses to automatically send notifications', 'woocommerce-twilio-sms-notifications' ),
			],
		];

		$settings[] = [
			'id'       => 'wc_twilio_sms_default_sms_template',
			'name'     => __( 'Default Customer SMS Message', 'woocommerce-twilio-sms-notifications' ),
			/* translators: Placeholders: %1$s is <code>, %2$s is </code> */
			'desc'     => sprintf( __( 'Use these tags to customize your message: %1$s%%shop_name%%%2$s, %1$s%%order_id%%%2$s, %1$s%%order_count%%%2$s, %1$s%%order_amount%%%2$s, %1$s%%order_status%%%2$s, %1$s%%billing_name%%%2$s, %1$s%%billing_first%%%2$s, %1$s%%billing_last%%%2$s, %1$s%%shipping_name%%%2$s, and %1$s%%shipping_method%%%2$s. Remember that SMS messages may be limited to 160 characters or less.', 'woocommerce-twilio-sms-notifications' ), '<code>', '</code>' ),
			'css'      => 'min-width:500px;',
			'default'  => __( '%shop_name% : Your order (%order_id%) is now %order_status%.', 'woocommerce-twilio-sms-notifications' ),
			'type'     => 'textarea'
		];

		// Display a textarea setting for each available order status
		foreach( $order_statuses as $slug => $label ) {

			$slug = 'wc-' === substr( $slug, 0, 3 ) ? substr( $slug, 3 ) : $slug;

			$settings[] = [
				'id'       => 'wc_twilio_sms_' . $slug . '_sms_template',
				/* translators: Placeholder: %s - order status label */
				'name'     => sprintf( __( '%s SMS Message', 'woocommerce-twilio-sms-notifications' ), $label ),
				/* translators: Placeholder: %s - order status slug */
				'desc_tip' => sprintf( __( 'Add a custom SMS message for %s orders or leave blank to use the default message above.', 'woocommerce-twilio-sms-notifications' ), $slug ),
				'css'      => 'min-width:500px;',
				'type'     => 'textarea'
			];
		}

		// Continue adding settings as usual
		$settings = array_merge( $settings, [

			[ 'type' => 'sectionend' ],

			[
				'name' => __( 'Return SMS Message', 'woocommerce-twilio-sms-notifications' ),
				'desc' => sprintf(
							/* translators: %1$s - opening <a> tag, %2$s - closing </a> tag, %3$s - request URL */
							__( 'When a customer replies to a SMS, Twilio will by default respond with a generic message. In order to send back a custom return message you can modify this section. You will then need to log into your Twilio account and navigate to the %1$sPhone Numbers page%2$s. Select the phone number where you want to receive SMS. Paste this URL into the Messaging > Request URL field: %3$s', 'woocommerce-twilio-sms-notifications' ),
							'<a href="https://www.twilio.com/user/account/phone-numbers/incoming" target="_blank">',
							'</a>','<code>' . home_url() . '?wc_twilio_sms_response' . '</code>'
						),
				'type' => 'title',
			],

			[
				'id'      => 'wc_twilio_sms_enable_return_message',
				'name'    => __( 'Enable return SMS message.', 'woocommerce-twilio-sms-notifications' ),
				'default' => 'no',
				'type'    => 'checkbox',
			],

			[
				'id'       => 'wc_twilio_sms_return_message',
				'name'     => __( 'Response Message', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'Enter the response message to be sent. Remember that SMS messages may be limited to 160 characters or less. Leave blank to store the inbound SMS but disable replies.', 'woocommerce-twilio-sms-notifications' ),
				'default'  => __( '%shop_name% : Unfortunately we do not provide support via SMS.  Please visit us at %site_url% for further assistance.', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'textarea',
				'css'      => 'min-width: 500px;',
			],

			[ 'type' => 'sectionend' ],

			[
				'name' => __( 'Sender ID', 'woocommerce-twilio-sms-notifications' ),
				'desc' => sprintf(
							/* translators: %1$s - opening <a> tag, %2$s - closing </a> tag */
							__( 'Alphanumeric Sender ID allows you to set your own business brand as the Sender ID when sending one-way messages. This is only used when sending messages to %1$ssupported countries%2$s and messages sent using the Sender ID cannot accept customer replies. Spoofing of brands or companies with Sender ID is not allowed.', 'woocommerce-twilio-sms-notifications' ),
							'<a href="https://www.twilio.com/help/faq/sms/what-countries-does-twilio-support-alphanumeric-sender-id" target="_blank">',
							'</a>'
						),
				'type' => 'title',
			],

			[
				'id'      => 'wc_twilio_sms_enable_asid',
				'name'    => __( 'Enable Sender ID', 'woocommerce-twilio-sms-notifications' ),
				'default' => 'no',
				'type'    => 'checkbox',
			],

			[
				'id'       => 'wc_twilio_sms_asid',
				'name'     => __( 'Sender ID', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'Enter the Alphanumeric Sender ID to send SMS messages from.', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'text',
			],

			[ 'type' => 'sectionend' ],

			[
				'name' => __( 'Connection Settings', 'woocommerce-twilio-sms-notifications' ),
				'type' => 'title',
			],

			[
				'id'       => 'wc_twilio_sms_account_sid',
				'name'     => __( 'Account SID', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'Log into your Twilio Account to find your Account SID.', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'text',
			],

			[
				'id'       => 'wc_twilio_sms_auth_token',
				'name'     => __( 'Auth Token', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'Log into your Twilio Account to find your Auth Token.', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'text',
			],

			[
				'id'       => 'wc_twilio_sms_from_number',
				'name'     => __( 'From Number', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'Enter the number to send SMS messages from. This must be a purchased number from Twilio.', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'text',
			],

			[
				'id'       => 'wc_twilio_sms_log_errors',
				'name'     => __( 'Log Errors', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'Enable this to log Twilio API errors to the WooCommerce log. Use this if you are having issues sending SMS.', 'woocommerce-twilio-sms-notifications' ),
				'default'  => 'no',
				'type'     => 'checkbox',
			],

			[ 'type' => 'sectionend' ],

			[
				'name' => __( 'Send Test SMS', 'woocommerce-twilio-sms-notifications' ),
				'type' => 'title',
			],

			[
				'id'       => 'wc_twilio_sms_test_mobile_number',
				'name'     => __( 'Mobile Number', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'Enter the mobile number (starting with the country code) where the test SMS should be send. Note that if you are using a trial Twilio account, this number must be verified first.', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'text',
			],

			[
				'id'       => 'wc_twilio_sms_test_message',
				'name'     => __( 'Message', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'Enter the test message to be sent. Remember that SMS messages may be limited to 160 characters or less.', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'textarea',
				'css'      => 'min-width: 500px;',
			],

			[
				'name'  => __( 'Send', 'woocommerce-twilio-sms-notifications' ),
				'href'  => '#',
				'class' => 'wc_twilio_sms_test_sms_button' . ' button',
				'type'  => 'wc_twilio_sms_link',
			],

			[
				'type' => 'sectionend',
				'id'   => 'wc_twilio_sms_send_test_section'
			],

		] );

		return $settings;
	}


	/**
	 * Add custom woocommerce admin form field via woocommerce_admin_field_* action
	 *
	 * @since 1.0
	 * @param array $field associative array of field parameters
	 */
	public function add_link_field( $field ) {

		if ( isset( $field['name'] ) && isset( $field['class'] ) && isset( $field['href'] ) ) :

		?>
			<tr valign="top">
				<th scope="row" class="titledesc"></th>
				<td class="forminp">
					<a href="<?php echo esc_url( $field['href'] ); ?>" class="<?php echo esc_attr( $field['class'] ); ?>"><?php echo wp_filter_kses( $field['name'] ); ?></a>
				</td>
			</tr>
		<?php

		endif;
	}


	/**
	 * Add the 'Twilio SMS Notifications' admin menu bar item
	 *
	 * @since 1.1
	 */
	public function add_admin_bar_menu_item() {
		global $wp_admin_bar;

		// security check
		if ( ! is_admin_bar_showing() || ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// add top-level menu
		$menu_args = array(
			'id'    => 'wc_twilio_sms_admin_bar_menu',
			'title' => __( 'Twilio SMS Notifications', 'woocommerce-twilio-sms-notifications' ),
			'href'  => false
		);

		// get SMS usage
		$sms_usage = $this->get_sms_usage();

		// set message
		if ( 0 === $sms_usage['count'] ) {
			$message = __( 'Your store has not sent any SMS messages today.', 'woocommerce-twilio-sms-notifications' );
		} else {
								/* translators: %1$d - the number of SMS messages sent, %2$s - the total cost of the SMS messages sent */
			$message = sprintf( _n( 'Your store has sent %1$d SMS message today at a cost of $%2$s', 'Your store has sent %1$d SMS messages today at a cost of $%2$s', $sms_usage['count'], 'woocommerce-twilio-sms-notifications' ),
								$sms_usage['count'], $sms_usage['cost'] );
		}

		// setup 'usage' item
		$sms_usage_item_args = array(
			'id' => 'wc_twilio_sms_sms_usage_item',
			'title' => $message,
			'href' => false,
			'parent' => 'wc_twilio_sms_admin_bar_menu'
		);

		// setup 'add funds' link
		$add_funds_item_args = array(
			'id'     => 'wc_twilio_sms_add_funds_item',
			'title'  => __( 'Add Funds to Your Twilio Account', 'woocommerce-twilio-sms-notifications' ),
			'href'   => 'https://www.twilio.com/user/billing',
			'meta'   => array( 'target' => '_blank' ),
			'parent' => 'wc_twilio_sms_admin_bar_menu'
		);

		// add menu + items
		$wp_admin_bar->add_menu( $menu_args );
		$wp_admin_bar->add_menu( $sms_usage_item_args );
		$wp_admin_bar->add_menu( $add_funds_item_args );
	}


	/**
	 * Get SMS usage for today via Twilio API and set as 15 minute transient
	 *
	 * @since 1.1
	 */
	private function get_sms_usage() {

		// get transient
		if ( false === ( $usage = get_transient( 'wc_twilio_sms_sms_usage' ) ) ) {

			// transient doesn't exist, fetch via Twilio API
			try {

				// get SMS usage
				$response = wc_twilio_sms()->get_api()->get_sms_usage();

				$usage = array(
					'count' => ( isset( $response['usage_records'][0]['count'] ) ) ? $response['usage_records'][0]['count'] : 0,
					'cost'  => ( isset( $response['usage_records'][0]['price'] ) ) ? $response['usage_records'][0]['price'] : 0
				);

				// set 15 minute transient
				set_transient( 'wc_twilio_sms_sms_usage', $usage, 60*15 );

				return $usage;

			} catch ( Exception $e ) {

				wc_twilio_sms()->log( $e->getMessage() );

				return array( 'count' => 0, 'cost' => '0.00' );
			}

		} else {

			return $usage;
		}
	}


}

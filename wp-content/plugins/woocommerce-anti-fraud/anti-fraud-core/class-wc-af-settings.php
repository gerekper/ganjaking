<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_AF_Settings' ) ) :

	function wc_af_add_settings() {
		/**
		 * Settings class
		 *
		 * @since 1.0.0
		 */
		class WC_AF_Settings extends WC_Settings_Page {
		
			/**
			 * The request response
		 *
			 * @var array
			*/
			private $response = null;

			/**
			 * The error message
		 *
			 * @var string
			*/
			private $error_message = '';

			/**
			 * Setup settings class
			 *
			 * @since  1.0
			 */

			const SETTINGS_NAMESPACE = 'anti_fraud';

			public function __construct() {
				$this->id    = 'wc_af';
				$this->label = __( 'Anti Fraud', 'wc_af' );
			
				add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
				add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
				add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
				add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
				add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'Authorized_Minfraud' ) );
				add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'Authorized_Quickemailverification' ) );

				/* initiation of logging instance */
				$this->log = new WC_Logger();
			}
		
			/**
			 * Get sections
			 *
			 * @return array
			 */
			public function get_sections() {
		
				$sections = array(
				''         => __( 'General Settings', 'wc_af' ),
				'rules' => __( 'Rules', 'wc_af' ),
				'black_list' => __( 'Blacklist Settings', 'wc_af' ),
				'paypal_settings' => __( 'Paypal Settings', 'wc_af' ),
				'minfraud_settings' => __( 'MinFraud Settings', 'wc_af' ),
				);
			
				return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
			}
		
			/**
			 * Get settings array
			 *
			 * @since 1.0.0
			 * @param string $current_section Optional. Defaults to empty string.
			 * @return array Array of settings
			 */
			public function get_settings( $current_section = '' ) {
			
				$score_options = array();
				for ( $i = 100; $i > - 1; $i -- ) {
					if ( ( $i % 5 ) == 0 ) {
						$score_options[$i] = $i;
					}
				}

				$rule_weight = array();
				for ($i = 20; $i > -1; $i -- ) {
					$rule_weight[$i] = $i;  
				} 

				if ( 'minfraud_settings' == $current_section ) {

					/**
					 * WCAF Filter Plugin  MinFraud Settings
					 *
					 * @since 1.0.0
					 * @param array $settings Array of the plugin settings
					*/
				 
					$settings = apply_filters( 'myplugin_minfraud_settings', array(
					array(
						'name'     => __( 'MaxMind minFraud Settings', 'woocommerce-anti-fraud' ),
						'type'     => 'title',
						'desc'     => 'MaxMind minFraud is a paid, external service that uses machine learning to detect potential fraud transactions.  By using minFraud you can potentially detect more fraudulent transactions.<hr/>',
						'id'       => 'wc_af_minfraud_settings', 
					),

					array(
						'title'    => __( 'Enable/Disable', 'woocommerce-anti-fraud' ),
						'type'     => 'checkbox',
						'label'    => '',
						'desc'    =>  __( 'Enable MaxMind minFraud Integration', 'woocommerce-anti-fraud' ),
						'default'  => 'no',
						'id'       => 'wc_af_maxmind_type'
					),

					array(
						'title'    => __( 'Device Tracking', 'woocommerce-anti-fraud' ),
						'type'     => 'checkbox',
						'label'    => 'Device Tracking Settings',
						'desc'    =>  __( 'Detect if a person uses the same device but changes proxies while they are placing multiple orders from your website', 'woocommerce-anti-fraud' ),
						'default'  => 'no',
						'id'       => 'wc_af_maxmind_device_tracking'
					),
					
					array(
						'name'     => __( 'MaxMind Account ID', 'woocommerce-anti-fraud' ),
						'type'     => 'text',
						'desc'     => __( 'Enter your MaxMind account ID here.  If you don&apos;t have an account, please sign up at <a href="https://maxmind.com">https://maxmind.com</a>.', 'woocommerce-anti-fraud' ),
						'id'       => 'wc_af_maxmind_user',
						'css'      => 'width: 10em;',
					),
					array(
						'name'     => __( 'MaxMind License Key', 'woocommerce-anti-fraud' ),
						'type'     => 'password',
						'desc'     => __( 'Enter the license key provided by MaxMind here.', 'woocommerce-anti-fraud' ),
						'id'       => 'wc_af_maxmind_license_key',
						'css'      => 'width: 15em;',
					),

					 array(
						'type' => 'sectionend',
						'id'   => 'wc_af_minfraud_settings'
					),

					array(
						'name' => __( 'Threshold Settings' ),
						'type' => 'title',
						'desc' => '<hr/>',
						'id'   => 'wc_af_minfraud_rule_settings' 
					),

					array(
						'name'     => __( 'Threshold minFraud Risk Score', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'desc'     => __( 'If the risk score returned by the minFraud system exceeds this number, the transaction will be considered potentially fraudulent and will contribute to the overall fraud score calculation.  If the risk score from minFraud is lower than this threshold, it will not contribute to the overall fraud score.' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_minfraud_risk_score',
						'css'      => 'display: block; width: 5em;',
						'default'  => '5',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),

					),

					array(
						'name'     => __( 'minFraud Rule Weight', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'options'  => $rule_weight,
						'desc'     => __( 'If the minFraud risk score exceeds the threshold set above, this weight will be applied to the overall risk score.  By adjusting this, you can balance the minFraud value against the other rules-based calculations in the plugin.' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_minfraud_order_weight',
						'css'         => 'display: block; width: 5em;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					),
					array(
						'type' => 'sectionend',
						'id'   => 'wc_af_minfraud_rule_settings'
					),

					) );

				} else if ( 'black_list' == $current_section ) {
			
					/**
					 * WCAF Filter Plugin  Blacklist Settings
					 *
					 * @since 1.0.0
					 * @param array $settings Array of the plugin settings
					*/
					$settings = apply_filters( 'myplugin_company_file_settings', array(
					array(
						'name' => __( 'Blacklist' ),
						'type' => 'title',
						'desc' => __( 'WooCommerce Anti-Fraud allows you to create a list of email addresses as well as a list of IP addresses that will always be marked as potential fraud transactions.' ),
						'id'   => 'wc_af_blacklist_settings', 
					),
					//Enable email blacklist
					array(
						'title'       => __( 'Email Blacklist', 'woocommerce-anti-fraud' ),
						'type'        => 'checkbox',
						'label'       => 'wc_af_email_blacklist',
						'default'     => 'no',
						'desc' => __( 'Enable the email blacklist function' ),
						'id'   => 'wc_settings_' . self::SETTINGS_NAMESPACE . 'enable_automatic_email_blacklist', 
					),  
					//Enable automatic blacklisting
					array(
						'title'       => __( 'Automatic Blacklisting', 'woocommerce-anti-fraud' ),
						'type'        => 'checkbox',
						'label'       => 'wc_af_email_blacklist',
						'default'     => 'no',
						'desc' => __( 'Add email addresses of orders reported with a high risk of fraud to blacklist automatically' ),
						'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . 'enable_automatic_blacklist', 
					),
					//Block these email addresses
					array(
						'name'        => __( 'Blocked Email Addresses', 'woocommerce-anti-fraud' ),
						'type'        => 'textarea',
						'desc'        => __( 'The email addresses listed in the text area below will be considered unsafe:', 'woocommerce-anti-fraud '),
						'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . 'blacklist_emails',
						'css'         => 'width:100%; height: 100px;',
						'default'     => '',
						'class'       => 'wc_af_tags_input',
					), 
					
					//Block these email addresses
					array(
						'name'        => __( 'Blocked IP Adresses', 'woocommerce-anti-fraud' ),
						'type'        => 'textarea',
						'desc'        => __( 'The IP addresses listed in the text area below will be considered unsafe:', 'woocommerce-anti-fraud '),
						'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . 'blacklist_ipaddress',
						'css'         => 'width:100%; height: 100px;',
						'default'     => '',
						'class'       => 'wc_af_tags_input',
					), 
					
					array(
						'type' => 'sectionend',
						'id'   => 'wc_af_blacklist_settings'
					),

					) );
				
				} else if ('paypal_settings' == $current_section) {
					/**
					 * WCAF Filter Plugin Paypal Settings
					 *
					 * @since 1.0.0
					 * @param array $settings Array of the plugin settings
					*/
					$settings = apply_filters( 'wc_af_paypal_settings', array(
				
					array(
						'name' => __( 'Paypal Settings' ),
						'type' => 'title',
						'desc' => __( 'These settings are specific to Paypal Payment Gateway. This means, when the customer use their Paypal account for making payment for their orders, then these rules come into action.<hr/>' ),
						'id'   => 'wc_af_paypal_settings', 
					),
					array(
						'title'       => __( 'Enable/Disable', 'woocommerce-anti-fraud' ),
						'type'        => 'checkbox',
						'label'       => 'wc_af_paypal_verification',
						'default'     => 'no',
						'desc' => __( 'Select to enable Paypal verification' ),
						'id'   => 'wc_af_paypal_verification', 
					),  
					//Prevent downloads if verification failed or still processing
					array(
						'title'       => __( 'Block Downloads', 'woocommerce-anti-fraud' ),
						'type'        => 'checkbox',
						'label'       => 'wc_af_paypal_verification',
						'default'     => 'no',
						'desc' => __( 'Prevent digital downloads if verification failed or still processing' ),
						'id'   => 'wc_af_paypal_prevent_downloads', 
					),
					//Time span before further attempts 
					array(
						'name'     => __( 'Verification Retry' ),
						'type'     => 'number',
						'desc'     => __( 'Number of days that have to pass before sending another email if the order is still waiting for verification' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_time_paypal_attempts',
						'css'         => 'display: block; width: 5em;',
						'default' => '2',
						'custom_attributes' => array(
							'min'  => 1,
							'step' => 1,
						),
					),
					//Time span before the orders are cancelled 
					array(
						'name'     => __( 'Auto Cancellation Days' ),
						'type'     => 'number',
						'desc'     => __( 'Orders that are not verified within the given number of days will be automatically cancelled' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_day_deleting_paypal_order',
						'default' => '2',
						'css'       => 'display: block; width: 5em;',
						'custom_attributes' => array(
							'min'  => 1,
							'step' => 1,
						),
					),  
					//PayPal verified addresses
					array(
						'name'        => __( 'Paypal verified addresses', 'woocommerce-anti-fraud' ),
						'type'        => 'textarea',
						'desc'        => __( 'Verified email addresses'),
						'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_paypal_verified_address',
						'class'         => 'wc_af_tags_input',
						'default'     => '',
					), 
					 array(
						'type' => 'sectionend',
						'id'   => 'wc_af_paypal_settings'
					),
					array(
						'name' => __( 'Email Template' ),
						'type' => 'title',
						'desc' => 'Configure the email template below.   The following tags can be embedded in the template fields:<br/><br/><b>{site_title}</b> - replaced with the Wordpress Site Title<br/><b>{site_email}</b> - replaced with the Wordpress site admin email address',
						'id'   => 'wc_af_paypal_email_settings' 
					),

					//Email type
					array(
						'name'     => __( 'Email Type', 'woocommerce-anti-fraud' ),
						'type'     => 'select',
						'options'  => array(
							'html'        => __( 'HTML', 'woocommerce_antifraud' ),
							'text'       => __( 'Text-Only', 'woocommerce_antifraud' ),
						),
						'desc'     => __( 'Emails can be sent either as plain text or HTML' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_paypal_email_format',
						'default' => 'html',
						'css'       => 'display: block; width: 5em;',
					), 
					//Email subject
					array(
						'name'     => __( 'Email Subject', 'woocommerce' ),
						'desc'     => __( 'The subject of the email that will be sent to the purchaser' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_paypal_email_subject',
						'type'     => 'text',
						'placeholder' => '[{site_title}] Confirm your PayPal email address'
					),
					//Email body
					array(
						'name'        => __( 'Email body', 'woocommerce-anti-fraud' ),
						'type'        => 'textarea',
						'desc'        => __( 'Enter the body of the email to be sent to the purchaser in the text area below:', 'woocommerce-anti-fraud '),
						'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_email_body',
						'css'         => 'width:100%; height: 100px;',
						'default'     => 'Hi!We have received your order on {site_title}, but to complete we have to verify your PayPal email address.If you havent made or authorized any purchase, please, contact PayPal support service immediately,and email us to {site_email} for having your money back.',
					), 

					array( 
						'type' => 'sectionend', 
						'id' => 'wc_af_paypal_email_settings' 
					),
					
					) );
				} else if ( '' == $current_section ) {
				
					/**
					 * WCAF Filter Plugin General Settings
					 *
					 * @since 1.0.0
					 * @param array $settings Array of the plugin settings
					 */
					$settings = apply_filters( 'wc_af_general_settings', array(
				
						array(
						'name' => __( 'General Settings' ),
						'type' => 'title',
						'desc' => 'General behaviours of the Anti Fraud plugin are configured on this page.<hr/>',
						'id'   => 'wc_af_general_settings' 
					),
					 array(
						'title'       => __( 'Pre-Payment Checking', 'woocommerce-anti-fraud' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'no',
						'desc' => __( 'Perform the fraud check before payment<br/><br/><i>If this is enabled, the fraud check will be done on the order checkout page i.e. immediately before the actual payment. If the calculated fraud score violates any of the other rules enabled in the Anti Fraud settings, the customer will not be allowed to place this order.</i>' ),
						'id'    => 'wc_af_fraud_check_before_payment',
					),
					array(
						'title'       => __( 'Enable Payment Method Whitelisting', 'woocommerce-anti-fraud' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'no',
						'desc' => __( '' ),
						'id'    => 'wc_af_enable_whitelist_payment_method'
					),
					 array(
						'name'        => __( 'Whitelisted Payment Methods', 'woocommerce-anti-fraud', 'woocommerce-anti-fraud' ),
						'type'        => 'textarea',
						'desc'        => __( 'List of payment methods those will be whitelisted i.e. will not be considered for Cancel Order Score and On-Hold Order Score evaluation for each order.', 'woocommerce-anti-fraud '),
						'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_whitelist_payment_method',
						'css'         => 'width:100%; height: 100px;',
						'default'     => $this->whitelist_payment_method(),
						'class'       => 'wc_af_tags_input' 
					),                    
					array(
						'title'       => __( 'Send Admin Email', 'woocommerce-anti-fraud' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'yes',
						'desc' => __( 'Send a notification mail to the site admin showing the outcome of anti-fraud checks.' ),
						'id'    => 'wc_af_email_notification'
					),    
					array(
						'name'     => __( 'Cancel Score', 'woocommerce-anti-fraud' ),
						'type'     => 'select',
						'options'  => $score_options,
						'desc'     => __( 'Orders with a score equal to or greater than this number will be automatically cancelled. Select 0 to disable.', 'woocommerce-anti-fraud' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_cancel_score',
						'css'         => 'display: block; width: 5em;',
						'default' => '90',
					), 
					array(
						'name'     => __( 'On-hold Score', 'woocommerce-anti-fraud' ),
						'type'     => 'select',
						'options'  => $score_options,
						'desc'     => __( 'Orders with a score equal to or greater than this number will be automatically set on hold. Select 0 to disable.', 'woocommerce-anti-fraud' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_hold_score',
						'css'         => 'display: block; width: 5em;',
						'default' => '70',
					),
					array(
						'name'     => __( 'Email Notification Score', 'woocommerce-anti-fraud' ),
						'type'     => 'select',
						'options'  => $score_options,
						'desc'     => __( 'An admin email notification will be sent if an orders scores equal to or greater than this number. Select 0 to disable.', 'woocommerce-anti-fraud' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_email_score',
						'css'         => 'display: block; width: 5em;',
						'default' => '50',
					),
					//custom code for send email to other users
					 array(
						'name'     => __( 'Additional Recipients', 'woocommerce-anti-fraud' ),
						'type'     => 'text',
						'options'  => $score_options,
						'desc'     => __( 'To send email notifications to additional addresses, enter them, separated by commas, above.', 'woocommerce-anti-fraud' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_custom_email',
						'css'         => 'display: block; width: 70%;'
					),
					array(
						'name'        => __( 'Email Whitelist', 'woocommerce-anti-fraud' ),
						'type'        => 'textarea',
						'desc'        => __( 'Email addresses listed below will not be subject to fraud checks. Enter one email address per line.', 'woocommerce-anti-fraud '),
						'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_whitelist',
						'css'         => 'width:100%; height: 100px;',
						'default'     => '',
					),   
					array( 
						'type' => 'sectionend', 
						'id' => 'wc_af_general_settings' 
					),
					//thresholds settings
					array(
						'name' => __( 'Settings for risk thresholds' ),
						'type' => 'title',
						'desc' => 'There are three risk thresholds.  Low, Medium and High.   In this section you can define where the boundary point between those thresholds.   The maximum risk score is 100, the minimum is 0.<br/>',
						'id'   => 'wc_af_thresholds_settings',
						'css'   => 'display: block;' 
					),
					array(
						'name'     => __( 'Medium Risk threshold', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'desc'     => __( '' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_low_risk_threshold',
						'css'         => 'display: block; width: 5em;',
						'default' => '25',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					),
					array(
						'name'     => __( 'High Risk threshold', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'desc'     => __( '' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_higher_risk_threshold',
						'css'         => 'display: block; width: 5em;',
						'default' => '75',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					),
					array( 
						'type' => 'sectionend', 
						'id' => 'wc_af_thresholds_settings' 
					),
					) );
				} else if ( 'rules' == $current_section ) {
					$settings = apply_filters( 'wc_af_rule_settings', array(
					
					array(
						'name' => __( 'General Rules' ),
						'type' => 'title',
						'desc' => __('Each rule that is matched will add the configured &quot;Rule Weight&quot; value to the overall fraud score.  In this section you can configure general fraud detection rules.<hr/>'),
						'id'   => 'wc_af_rule_settings' 
					),
					array(
						'title'       => __( "Is Customer's First Order?", 'woocommerce-anti-fraud' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'yes',
						'desc' => __( 'Check if it is the customer&apos;s first purchase on your site' ),
						'id'    => 'wc_af_first_order'
					),
					array(
						'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'options'  => $rule_weight,
						'desc'     => __( '<br/>' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_first_order_weight',
						'css'         => 'display: block; width: 5em;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					),  
					//custom rule for processing order
					array(
						'title'       => __( 'Re-Check First Orders in Processing State?', 'woocommerce-anti-fraud' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'no',
						'desc' => __( 'Perform first order check again once order is in Processing state' ),
						'id'    => 'wc_af_first_order_custom'
					),
					array(
						'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'options'  => $rule_weight,
						'desc'     => __( '<br/>' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_first_order_custom_weight',
						'css'         => 'display: block; width: 5em;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					), 
					array(
						'title'       => __( 'Does IP Address Match Location?', 'woocommerce-anti-fraud' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'yes',
						'desc' => __( 'Confirm that the customer&apos;s location matches the location given by their IP address' ),
						'id'    => 'wc_af_ip_geolocation_order'
					),
					array(
						'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'options'  => $rule_weight,
						'desc'     => __( '<br/>' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_ip_geolocation_order_weight',
						'css'         => 'display: block; width: 5em;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					),
					array(
						'title'       => __( 'Are Billing and Shipping Addresses Same?', 'woocommerce-anti-fraud' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'yes',
						'desc' => __( 'Confirm billing and shipping addresses are the same.' ),
						'id'    => 'wc_af_bca_order'
					),
					array(
						'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'options'  => $rule_weight,
						'desc'     => __( '<br/>' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_bca_order_weight',
						'css'         => 'display: block; width: 5em;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					),
					array(
						'title'       => __( 'Enable phone number and billing country check', 'woocommerce-anti-fraud' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'no',
						'desc' => __( 'If you enable this rule, then it is highly recommended that you use a separate Phone Number Validation plugin to make sure your customers specify a correct international phone number format on the checkout page. Else, this rule will treat an invalid phone number as a risk.' ),
						'id'    => 'wc_af_billing_phone_number_order'

					),

					array(
						'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'options'  => $rule_weight,
						'desc'     => __( 'Weight of the single rule in the total calculation of risk.' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_billing_phone_number_order_weight',
						'css'         => 'display: block;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					),
					array(
						'title'       => __( 'Customer behind Proxy or VPN?', 'woocommerce-anti-fraud' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'yes',
						'desc' => __( 'Check if the customer is behind either a proxy or a VPN' ),
						'id'    => 'wc_af_proxy_order'
					),
					array(
						'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'options'  => $rule_weight,
						'desc'     => __( '<br/>' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_proxy_order_weight',
						'css'         => 'display: block; width: 5em;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					),
					array(
						'title'       => __( 'Same IP but Different Customer Addresses?' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'yes',
						'desc' => __( 'Check if multiple orders with different billing or shipping addresses have originated from the same IP address' ),
						'id'    => 'wc_af_ip_multiple_check'
					),
					array(
						'name'     => __( 'Time span (days) to check' ),
						'type'     => 'number',
						'desc'     => __( 'The number of days in the past to check IP addresses and physical addresses' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_ip_multiple_time_span',
						'css'         => 'display: block; width: 5em;',
						'default' => '2',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
						),
					),
					array(
						'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'options'  => $rule_weight,
						'desc'     => __( '<br/><hr/>' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_ip_multiple_weight',
						'css'         => 'display: block; width: 5em;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					),
					array( 
						'type' => 'sectionend', 
						'id' => 'wc_af_rule_settings' 
					),    

					 array(
						'name' => __( 'Origin Domains and Countries' ),
						'type' => 'title',
						'desc' => 'International orders tend to have a higher fraud-risk than order that originate in your home country.  Merchants often find that certain origin countries or email domains have a higher potential for fraud.  Settings to help manage these risks are in this section.',
						'id'   => 'wc_af_domains_countries',
						'css'   => 'display: block;' 
					),
					array(
						'title'       => __( 'Is International Order?', 'woocommerce-anti-fraud' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'yes',
						'desc' => __( 'Check if the order originates from outside of your store&apos;s home country.' ),
						'id'    => 'wc_af_international_order'
					),
					array(
						'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'options'  => $rule_weight,
						'desc'     => __( '<br/>' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_international_order_weight',
						'css'         => 'display: block; width: 5em;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					), 
					array(
						'title'       => __( 'Is suspicious email domain?' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'yes',
						'desc' => __( 'Check if customer&apos;s email address originates from any high-risk domain listed below' ),
						'id'    => 'wc_af_suspecius_email'
					),
					array(
						'name'        => __( 'High-risk domains', 'woocommerce-anti-fraud', 'woocommerce-anti-fraud' ),
						'type'        => 'textarea',
						'desc'        => __( 'Enter any email origin domains you consider to be high-risk below:', 'woocommerce-anti-fraud '),
						'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_suspecious_email_domains',
						'css'         => 'width:100%; height: 100px;',
						'default'     => $this->suspicious_domains(),
						'class'       => 'wc_af_tags_input' 
					),
					 array(
						'title'       => __( 'API Key for quickemailverification.com' ),
						'type'        => 'password',
						'desc' => __( 'You can use quickemailverification.com to get more accurate results for false email domain related checks' ),
						'id'    => 'check_email_domain_api_key'
					),
					array(
						'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'options'  => $rule_weight,
						'desc'     => __( '<br/>' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_suspecious_email_weight',
						'css'         => 'display: block; width: 5em;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					),

					array(
						'title'       => __( 'Is order from high-risk country?' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'yes',
						'desc' => __( 'Check if order originates from any country in the high-risk countries list below:' ),
						'id'    => 'wc_af_unsafe_countries'
					),
					array(
						'name'        => __( 'Define unsafe countries', 'woocommerce-anti-fraud', 'woocommerce-anti-fraud' ),
						'type'        => 'multiselect',
						'desc'        => __( '' ),
						'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_define_unsafe_countries_list',
						'class'        => 'chzn-drop',
						'options'      => $this->get_countries() 
					), 
					array(
						'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'options'  => $rule_weight,
						'desc'     => __( '<br/><hr/>' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_unsafe_countries_weight',
						'css'         => 'display: block; width: 5em;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					),
					array( 
						'type' => 'sectionend', 
						'id' => 'wc_af_domains_countries' 
					),
					array(
						'name' => __( 'Order Amounts and Attempts' ),
						'type' => 'title',
						'desc' => 'The rules in this section are triggered based on defined order amounts and by counting attempted orders.   Orders with unusually high values, or customers who make an excessive number of transactions in a short period are more likely to be fraudulent.<hr/>',
						'id'   => 'wc_af_amounts' 
					),
					array(
						'title'       => __( 'Order amount above average?' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'yes',
						'desc' => __( 'Check if order significantly exceeds the average order amount for your site' ),
						'id'    => 'wc_af_order_avg_amount_check'
					),
					array(
						'name'     => __( 'Average multiplier', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'desc'     => __( 'The amount over the average transaction value that will trigger the rule (expressed as a multiplier).' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_avg_amount_multiplier',
						'css'         => 'display: block; width: 5em;',
						'default' => '2',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
						),
					),
					array(
						'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'options'  => $rule_weight,
						'desc'     => __( '<br/>' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_order_avg_amount_weight',
						'css'         => 'display: block; width: 5em;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					),
					array(
						'title'       => __( 'Order exceeds maximum?' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'yes',
						'desc' => __( 'Confirm the total amount of the order does not exceed the maxmimum configured below' ),
						'id'    => 'wc_af_order_amount_check'
					),
					 array(
						'name'     => __( 'Amount limit ($)', 'woocommerce-anti-fraud' ),
						'type'     => 'text',
						'desc'     => __( 'Total maximum order amount accepted.' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_amount_limit',
						'css'         => 'display: block; width: 5em;',
						'default' => '0',
					),
					array(
						'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'options'  => $rule_weight,
						'desc'     => __( '<br/>' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_order_amount_weight',
						'css'         => 'display: block; width: 5em;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					),
					array(
						'title'       => __( 'Too many order attempts?' ),
						'type'        => 'checkbox',
						'label'       => '',
						'default'     => 'yes',
						'desc' => __( 'Check if customer attempts to make a purchase too many times within the time period configured below' ),
						'id'    => 'wc_af_attempt_count_check'
					),

					array(
						'name'     => __( 'Time span to check' ),
						'type'     => 'number',
						'desc'     => __( 'Time span (hours) to check' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_attempt_time_span',
						'css'         => 'display: block; width: 5em;',
						'default' => '1',
					),
					array(
						'name'     => __( 'Maximum number of orders per time span' ),
						'type'     => 'number',
						'desc'     => __( 'Maximum number of orders that a user can make in the specified time span' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_max_order_attempt_time_span',
						'css'         => 'display: block; width: 5em;',
						'default' => '2',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
						),
					),
					array(
						'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
						'type'     => 'number',
						'options'  => $rule_weight,
						'desc'     => __( '</br>' ),
						'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_order_attempt_weight',
						'css'         => 'display: block; width: 5em;',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 100
						),
					),
					array( 
						'type' => 'sectionend', 
						'id' => 'wc_af_amounts' 
					),
					) );
				
				}
			
				/**
				 * Filter WCAF Settings
				 *
				 * @since 1.0.0
				 * @param array $settings Array of the plugin settings
				 */
				return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
			
			}
		
		
			/**
			 * Output the settings
			 *
			 * @since 1.0
			 */
			public function output() {
		
				global $current_section;
			
				$settings = $this->get_settings( $current_section );
				WC_Admin_Settings::output_fields( $settings );
			}
		
		
			/**
			 * Save settings
			 *
			 * @since 1.0
			 */
			public function save() {
		
				global $current_section;
			
				$settings = $this->get_settings( $current_section );
				WC_Admin_Settings::save_fields( $settings );
			}

		
			/**
			 * Authorized_Minfraud
			 *
			 * @since 1.0
			 */
			public function Authorized_Minfraud() {
		
				global $current_section;
				$get_settings = $this->get_settings( $current_section );

				if (isset( $get_settings ) ) {

					$this->log->add( 'MinFraud', '====== Authentication function has been accessed' );

					$curr_settings =  $get_settings['0']['id'];
					$setting_type = get_option( 'wc_af_maxmind_type' );

					$this->log->add( 'MinFraud', print_r( array( 'current settings tab' => $curr_settings, 'setting enable' => $setting_type ), true ) );

					if ($setting_type == 'yes' &&  $curr_settings == 'wc_af_minfraud_settings') {

						$maxmind_user = get_option( 'wc_af_maxmind_user' );
						$maxmind_license_key = get_option( 'wc_af_maxmind_license_key' );
						$authkey = 'Basic ' . base64_encode( $maxmind_user . ':' . $maxmind_license_key );

						$this->log->add( 'MinFraud', print_r( array( 'Authorization' => $authkey), true ) );

						$curl = curl_init();

						curl_setopt_array($curl, array(
						CURLOPT_URL => 'https://minfraud.maxmind.com/minfraud/v2.0/score',
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_USERAGENT => 'AnTiFrAuDOPMC',
						CURLOPT_ENCODING => '',
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 30,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => 'POST',
						CURLOPT_POSTFIELDS => '',
						CURLOPT_HTTPHEADER => array(
						'authorization:' . $authkey,
						'cache-control: no-cache',
						'content-type: application/json',
						),
						));

						$response = curl_exec($curl);
						curl_close($curl);
						$result = json_decode( $response, true );
						$error = $result['code'];

						if ($error == 'AUTHORIZATION_INVALID') {

							$this->log->add( 'MinFraud', '====== Authentication failed' );
							$this->log->add( 'MinFraud', print_r( array( 'MaxMind Account Id' => $maxmind_user, 'MaxMind license key' => $maxmind_license_key ), true ) );
							add_action('admin_notices', array( $this, 'auth_error_admin_notice'));

						} else {

							$this->log->add( 'MinFraud', '====== Authentication succeed ' );
							add_action('admin_notices', array( $this, 'auth_success_admin_notice'));
						
						}
					}
				}            
			}

			/**
			 * Auth_error_admin_notice
			 *
			 * @since 1.0
			 */
			public function auth_error_admin_notice() {
		
				?>
			<div class="error is-dismissible">
				<p><strong>Your Account Id or License Key could not be authenticated!!</strong></p>
			</div>

				<?php
			}

			/**
			 * Auth_success_admin_notice
			 *
			 * @since 1.0
			 */
			public function auth_success_admin_notice() {
		
				?>
			<div class="notice notice-success is-dismissible">
				<p><strong>Great, authenticated successfully!!</strong></p>
			</div>

				<?php
			}

			public function suspicious_domains() {
				$email_domains = array('hotmail',
				'live',
				'gmail',
				'yahoo',
				'mail',
				'123vn',
				'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijk',
				'aaemail.com',
				'webmail.aol',
				'postmaster.info.aol',
				'personal',
				'atgratis',
				'aventuremail',
				'byke',
				'lycos',
				'computermail',
				'dodgeit',
				'thedoghousemail',
				'doramail',
				'e-mailanywhere',
				'eo.yifan',
				'earthlink',
				'emailaccount',
				'zzn',
				'everymail',
				'excite',
				'expatmail',
				'fastmail',
				'flashmail',
				'fuzzmail',
				'galacmail',
				'godmail',
				'gurlmail',
				'howlermonkey',
				'hushmail',
				'icqmail',
				'indiatimes',
				'juno',
				'katchup',
				'kukamail',
				'mail',
				'mail2web',
				'mail2world',
				'mailandnews',
				'mailinator',
				'mauimail',
				'meowmail',
				'merawalaemail',
				'muchomail',
				'MyPersonalEmail',
				'myrealbox',
				'nameplanet',
				'netaddress',
				'nz11',
				'orgoo',
				'phat.co',
				'probemail',
				'prontomail',
				'rediff',
				'returnreceipt',
				'synacor',
				'walkerware',
				'walla',
				'wongfaye',
				'xasamail',
				'zapak',
				'zappo');
				return implode(',', $email_domains);
			}
		
			public function get_countries() {
				$countries_obj   = new WC_Countries();
				$countries       = $countries_obj->__get('countries');
				return $countries;

			}

			/**
			 * Authorized_Quickemailverification
			 *
			 * @since 1.0
			 */
			public function Authorized_Quickemailverification() {
		
				global $current_section;
				$get_settings = $this->get_settings( $current_section );

				if (isset( $get_settings ) ) {

					$curr_settings =  $get_settings['0']['id'];

					$setting_type = get_option( 'wc_af_suspecius_email' );

					if ($setting_type == 'yes' &&  $curr_settings == 'wc_af_general_settings') {

						$email_api_key = get_option( 'check_email_domain_api_key' );
						$admin_email = get_option( 'admin_email' );

						$contents = @file_get_contents("https://api.quickemailverification.com/v1/verify?email=$admin_email&apikey=$email_api_key");

						if ( $contents !== false ) {

							$res = @json_decode($contents);
						
							if (json_last_error() === JSON_ERROR_NONE) {
							
								$data = @$res->message;

								if ( 'Invalid api key' !== $data  ) {

									add_action('admin_notices', array( $this, 'auth_quickemailverification_success_admin_notice'));
								} else {
									 add_action('admin_notices', array( $this, 'auth_quickemailverification_success_low_creadit_admin_notice'));
								}  
							} 

						} else {

							 add_action('admin_notices', array( $this, 'auth_quickemailverification_error_admin_notice'));
						}
					}
				}            
			}

			/**
			 * Auth_error_admin_notice
			 *
			 * @since 1.0
			 */
			public function auth_quickemailverification_error_admin_notice() {
		
				?>
			<div class="error is-dismissible">
				<p><strong>Your Quickemailverification API Key could not be authenticated!!</strong></p>
			</div>

				<?php
			}

			/**
			 * Auth_success_admin_notice
			 *
			 * @since 1.0
			 */
			public function auth_quickemailverification_success_admin_notice() {
		
				?>
			<div class="notice notice-success is-dismissible">
				<p><strong>Great, Quickemailverification authenticated successfully!!</strong></p>
			</div>

				<?php
			}

			/**
			 * Auth_success_admin_notice With low creadit
			 *
			 * @since 1.0
			 */
			public function auth_quickemailverification_success_low_creadit_admin_notice() {
		
				?>
			<div class="notice notice-info is-dismissible">
				<p><strong>Great, Quickemailverification authenticated successfully but you don't have enough credit to use this service.</strong></p>
			</div>

				<?php
			}

			public function whitelist_payment_method() {

				$whitelist_payment_method = array('paysera',
				'skrill_flexible',
				'skrill_wlt',
				'skrill_acc',
				'skrill_vsa',
				'skrill_msc',
				'skrill_ntl'
				);
				return implode(',', $whitelist_payment_method);
			}
		

		}
		$settings[] = new WC_AF_Settings();
		 return $settings;
		/*$a =  new WC_AF_Settings();*/
		//$res = $a->get_settings();
	
	}
	add_filter( 'woocommerce_get_settings_pages', 'wc_af_add_settings', 15 );
endif;

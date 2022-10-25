<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_AF_Settings' ) ) :

	function wc_af_add_settings( $settings) {
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
//
			const SETTINGS_NAMESPACE = 'anti_fraud';

			public function __construct() {
				$this->id    = 'wc_af';
				$this->label = __( 'Anti Fraud', 'woocommerce-anti-fraud' );

				add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
				add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
				add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
				add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
				add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'Authorized_Minfraud' ) );
				add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'Authorized_Quickemailverification' ) );

				add_action('woocommerce_admin_field_section', array($this, 'opmc_add_admin_field_section') );
				add_action('woocommerce_admin_field_button', array($this, 'opmc_add_admin_field_button') );
				add_action('woocommerce_admin_field_timepicker', array($this, 'opmc_add_admin_field_timepicker') );

				/* initiation of logging instance */
				$this->log = new WC_Logger();
			}


			public function generate_custom_settings_html( $form_fields, $echo = true) {
				global $current_section;
				// pr($current_section);
				// pr($form_fields);exit;

				if ( empty( $form_fields ) ) {
					$form_fields = $this->get_form_fields();
				}
				include_once WOOCOMMERCE_ANTI_FRAUD_PLUGIN_DIR . 'anti-fraud-core/tamplate-admin-settings-page.php';

			}

			// public function admin_options() {
			// 	$this->generate_custom_settings_html( $this->get_form_fields(), false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			// }
			// public function process_admin_options() {
	
			// 	$saved = parent::process_admin_options();
			// 	$this->init_form_fields();
			// 	return $saved;
			// }

			/**
			 * Get sections
			 *
			 * @return array
			 */
			public function get_sections() {

				$sections = array(
				''         => __( 'General Settings', 'woocommerce-anti-fraud' ),
				'rules' => __( 'Rules', 'woocommerce-anti-fraud' ),
				'black_list' => __( 'Blacklist Settings', 'woocommerce-anti-fraud' ),
				'email_alert' => __('Email Alerts', 'woocommerce-anti-fraud'),
				'paypal_settings' => __( 'Paypal Settings', 'woocommerce-anti-fraud' ),
				'minfraud_settings' => __( 'MinFraud Settings', 'woocommerce-anti-fraud' ),
				'minfraud_insights_settings' => __( 'MinFraud Insights Settings', 'woocommerce-anti-fraud' ),
				'minfraud_factors_settings' => __( 'MinFraud Factors Settings', 'woocommerce-anti-fraud' ),
				'minfraud_recaptcha_settings' => __( 'Re-Captcha', 'woocommerce-anti-fraud' ),
				'need_support' => __('Need Support?', 'woocommerce-anti-fraud'),
				);

				/**
				 * Wc_sections for admin settings
				 * 
				 * @since 1.0.0
				 */
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
				
				$user_roles = [];
				$wc_af_whitelist_user_roles = get_option('wc_af_whitelist_user_roles');
				if ( empty($wc_af_whitelist_user_roles) ) {
					$wc_af_whitelist_user_roles = array();
				}
				//print_r($wc_af_whitelist_user_roles);
				$wc_af_whitelist_payment_methods = get_option('wc_settings_anti_fraud_whitelist_payment_method');
				if ( empty($wc_af_whitelist_payment_methods) ) {
					$wc_af_whitelist_payment_methods = array();
				} 
				//print_r($wc_af_whitelist_payment_methods);

				$wc_af_unsafe_countries_list = get_option('wc_settings_anti_fraud_define_unsafe_countries_list');
				if ( empty($wc_af_unsafe_countries_list) ) {
					$wc_af_unsafe_countries_list = array();
				}
	
				global $wp_roles;

				$all_roles = $wp_roles->roles;

				/**
				 * Editable roles 
				 *
				 * @since 1.0.0
				 */
				$editable_roles = apply_filters('editable_roles', $all_roles);
				foreach ($editable_roles as $role => $details) {
					$role = esc_attr($role);
					$name = translate_user_role($details['name']);
					$user_roles[$role] = $name;
				}
				
				$installed_payment_methods = WC()->payment_gateways->payment_gateways();
				$availableMethods = [];
				foreach ( $installed_payment_methods as $methods=>$arr ) {
					$availableMethods[$methods] = $arr->method_title;
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
							'desc'     => __( '<a href="https://maxmind.pxf.io/qnvxGg" target="_blank" >MaxMind minFraud</a> is a paid, external service that uses machine learning to detect potential fraud transactions. By using <a href="https://maxmind.pxf.io/qnvxGg" target="_blank" >minFraud</a> you can potentially detect more fraudulent transactions.<br/>To sign up to MaxMind minFraud, click this link: <a href="https://maxmind.pxf.io/qnvxGg" target="_blank">https://maxmind.com</a><hr/>', 'woocommerce-anti-fraud' ),
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
							'desc'     => __( 'Enter your MaxMind account ID here.  If you don&apos;t have an account, please sign up at <a href="https://maxmind.pxf.io/qnvxGg" target="_blank">https://maxmind.com</a>.', 'woocommerce-anti-fraud' ),
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
							'name' => __( 'Threshold Settings', 'woocommerce-anti-fraud'  ),
							'type' => 'title',
							'desc' => '<hr/>',
							'id'   => 'wc_af_minfraud_rule_settings'
						),
						
						array(
							'name'     => __( 'Threshold minFraud Risk Score', 'woocommerce-anti-fraud' ),
							'type'     => 'number',
							'desc'     => __( 'If the risk score returned by the minFraud system exceeds this number, the transaction will be considered potentially fraudulent and will contribute to the overall fraud score calculation.  If the risk score from minFraud is lower than this threshold, it will not contribute to the overall fraud score.', 'woocommerce-anti-fraud' ),
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
							'desc'     => __( 'If the minFraud risk score exceeds the threshold set above, this weight will be applied to the overall risk score.  By adjusting this, you can balance the minFraud value against the other rules-based calculations in the plugin.', 'woocommerce-anti-fraud' ),
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
						
						) 
					);
					
				} else if ( 'minfraud_insights_settings' == $current_section ) {
					
					/**
					* WCAF Filter Plugin  MinFraud Settings
					*
					* @since 1.0.0
					* @param array $settings Array of the plugin settings
					*/
					$settings = apply_filters( 'myplugin_minfraud_insights_settings', array(
						array(
							'name'     => __( 'MaxMind minFraud Settings', 'woocommerce-anti-fraud' ),
							'type'     => 'title',
							'desc'     => __( '<a href="https://maxmind.pxf.io/qnvxGg" target="_blank" >MaxMind minFraud</a> is a paid, external service that uses machine learning to detect potential fraud transactions. By using <a href="https://maxmind.pxf.io/qnvxGg" target="_blank" >minFraud</a> you can potentially detect more fraudulent transactions.<br/>To sign up to MaxMind minFraud, click this link: <a href="https://maxmind.pxf.io/qnvxGg" target="_blank">https://maxmind.com</a><hr/>', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_af_minfraud_settings',
						),
						array(
							'title'    => __( 'Enable/Disable', 'woocommerce-anti-fraud' ),
							'type'     => 'checkbox',
							'label'    => '',
							'desc'    =>  __( 'Enable MaxMind minFraud Insights Integration', 'woocommerce-anti-fraud' ),
							'default'  => 'no',
							'id'       => 'wc_af_maxmind_insights'
						),
						array(
							'type' => 'sectionend',
							'id'   => 'wc_af_minfraud_settings'
						),
						
						array(
							'name' => __( 'Threshold Settings', 'woocommerce-anti-fraud'  ),
							'type' => 'title',
							'desc' => '<hr/>',
							'id'   => 'wc_af_minfraud_rule_settings'
						),
						
						array(
							'name'     => __( 'Threshold minFraud Insights Risk Score', 'woocommerce-anti-fraud' ),
							'type'     => 'number',
							'desc'     => __( 'If the risk score returned by the minFraud system exceeds this number, the transaction will be considered potentially fraudulent and will contribute to the overall fraud score calculation.  If the risk score from minFraud is lower than this threshold, it will not contribute to the overall fraud score.', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_minfraud_insights_risk_score',
							'css'      => 'display: block; width: 5em;',
							'default'  => '5',
							'custom_attributes' => array(
								'min'  => 0,
								'step' => 1,
								'max'  => 100
							),
							
						),
						
						array(
							'name'     => __( 'minFraud Insights Rule Weight', 'woocommerce-anti-fraud' ),
							'type'     => 'number',
							'options'  => $rule_weight,
							'desc'     => __( 'If the minFraud risk score exceeds the threshold set above, this weight will be applied to the overall risk score.  By adjusting this, you can balance the minFraud value against the other rules-based calculations in the plugin.', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_minfraud_insights_order_weight',
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
						
						) 
					);
					
				} else if ( 'minfraud_factors_settings' == $current_section ) {
					
					/**
					* WCAF Filter Plugin  MinFraud Settings
					*
					* @since 1.0.0
					* @param array $settings Array of the plugin settings
					*/
					$settings = apply_filters( 'myplugin_minfraud_factors_settings', array(
						array(
							'name'     => __( 'MaxMind minFraud Settings', 'woocommerce-anti-fraud' ),
							'type'     => 'title',
							'desc'     => __( '<a href="https://maxmind.pxf.io/qnvxGg" target="_blank" >MaxMind minFraud</a> is a paid, external service that uses machine learning to detect potential fraud transactions. By using <a href="https://maxmind.pxf.io/qnvxGg" target="_blank" >minFraud</a> you can potentially detect more fraudulent transactions.<br/>To sign up to MaxMind minFraud, click this link: <a href="https://maxmind.pxf.io/qnvxGg" target="_blank">https://maxmind.com</a><hr/>', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_af_minfraud_settings',
						),
						array(
							'title'    => __( 'Enable/Disable', 'woocommerce-anti-fraud' ),
							'type'     => 'checkbox',
							'label'    => '',
							'desc'    =>  __( 'Enable MaxMind minFraud Factors Integration', 'woocommerce-anti-fraud' ),
							'default'  => 'no',
							'id'       => 'wc_af_maxmind_factors'
						),
						array(
							'type' => 'sectionend',
							'id'   => 'wc_af_minfraud_settings'
						),
						
						array(
							'name' => __( 'Threshold Settings', 'woocommerce-anti-fraud'  ),
							'type' => 'title',
							'desc' => '<hr/>',
							'id'   => 'wc_af_minfraud_rule_settings'
						),
						
						array(
							'name'     => __( 'Threshold minFraud Factors Risk Score', 'woocommerce-anti-fraud' ),
							'type'     => 'number',
							'desc'     => __( 'If the risk score returned by the minFraud system exceeds this number, the transaction will be considered potentially fraudulent and will contribute to the overall fraud score calculation.  If the risk score from minFraud is lower than this threshold, it will not contribute to the overall fraud score.', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_minfraud_factors_risk_score',
							'css'      => 'display: block; width: 5em;',
							'default'  => '5',
							'custom_attributes' => array(
								'min'  => 0,
								'step' => 1,
								'max'  => 100
							),
							
						),
						
						array(
							'name'     => __( 'minFraud Factors Rule Weight', 'woocommerce-anti-fraud' ),
							'type'     => 'number',
							'options'  => $rule_weight,
							'desc'     => __( 'If the minFraud risk score exceeds the threshold set above, this weight will be applied to the overall risk score.  By adjusting this, you can balance the minFraud value against the other rules-based calculations in the plugin.', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_minfraud_factors_order_weight',
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
						
						) 
					);
					
				} else if ('need_support' == $current_section) {
					
					/**
					* WCAF Filter Plugin  MinFraud Settings
					*
					* @since 1.0.0
					* @param array $settings Array of the plugin settings
					*/
					$settings = apply_filters( 'need_support', array(
						array(
							'name'     => __('Support for the Anti-Fraud Plugin', 'woocommerce-anti-fraud'),
							'type'     => 'title',
							'desc'     => '<hr/>',
							'id'       => 'wc_af_need_support',
						),
						array(
							'title'    => __('Facing an Issue with the plugin?', 'woocommerce-anti-fraud'),
							'name' => __('Contact Our Support', 'woocommerce-anti-fraud'),
							'type' => 'button',
							'desc' => __('If you have any issues or feedback about Anti-Fraud plugin, we would loveo here from you!', 'woocommerce-anti-fraud'),
							'class' => 'button-secondary',
							'href'  => 'https://woocommerce.com/my-account/create-a-ticket/',
							'id'	=> 'wc_af_contact_support',
						),
						array(
							'title'    => __('Love our Plugin?', 'woocommerce-anti-fraud'),
							'title_icon' => WOOCOMMERCE_ANTI_FRAUD_PLUGIN_URL . 'templates/icons/stars.png',
							'name' => __('Leave Us a Review!', 'woocommerce-anti-fraud'),
							'type' => 'button',
							'desc' => __('Your positive reviews are always encouraging!', 'woocommerce-anti-fraud'),
							'class' => 'button-secondary',
							'href'  => 'https://woocommerce.com/products/woocommerce-anti-fraud/#reviews-start',
							'id'	=> 'wc_af_contact_support',
						),
						array(
							'type' => 'sectionend',
							'id'   => 'wc_af_need_support'
						),
						
						)
					);
				} else if ( 'minfraud_recaptcha_settings' == $current_section ) {
					
					/**
					* WCAF Filter Plugin  MinFraud Settings
					*
					* @since 1.0.0
					* @param array $settings Array of the plugin settings
					*/
					$settings = apply_filters( 'myplugin_recaptcha_settings', array(
						array(
							'name'     => __( 'Google Re-Captcha Settings', 'woocommerce-anti-fraud' ),
							'type'     => 'title',
							'desc'     => __( 'Click <a href="https://www.google.com/recaptcha/admin" target="_blank">here</a> to know more.<hr/>', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_af_recaptch_settings',
						),
						array(
							'title'    => __( 'Site Key', 'woocommerce-anti-fraud' ),
							'type'     => 'text',
							'label'    => '',
							'desc'    =>  __( 'Enter Site Key', 'woocommerce-anti-fraud' ),
							'default'  => '',
							'id'       => 'wc_af_recaptcha_site_key'
						),
						array(
							'title'    => __( 'Secret Key', 'woocommerce-anti-fraud' ),
							'type'     => 'text',
							'label'    => '',
							'desc'    =>  __( 'Enter Secret Key', 'woocommerce-anti-fraud' ),
							'default'  => '',
							'id'       => 'wc_af_recaptcha_secret_key'
						),
						array(
							'title'       => __( 'Enable Re-Captcha', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'no',
							'desc'        => __( 'Enable Re-Captcha on checkout page<br/>', 'woocommerce-anti-fraud' ),
							'id'          => 'wc_af_enable_recaptcha_checkout',
						),
						array(
							'type' => 'sectionend',
							'id'   => 'wc_af_recaptch_settings'
						),
						
						) 
					);
					
				} else if ( 'email_alert' == $current_section ) {
					
					/**
					* WCAF Filter Plugin  MinFraud Settings
					*
					* @since 1.0.0
					* @param array $settings Array of the plugin settings
					*/
					$settings = apply_filters( 'email_alert', array(
						array(
							'name'     => __( 'Alerts', 'woocommerce-anti-fraud' ),
							'type'     => 'title',
							'desc'     => __( 'Get alerts about suspeted fradulent activites <hr/>', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_af_email_alert_settings',
						),
						array(
							'title'       => __( 'Activate EmailAlerts for Admin', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'no',
							'desc'        => '',
							'desc_tip'    => __( 'Enable to receive fraud alerts on admin email address when risk-score exceeds value set in Email Notification Score.', 'woocommerce-anti-fraud' ),
							'id'          => 'wc_af_email_notification'
						),
						array(
							'name'     => __( 'Additional Address(es) to Notify', 'woocommerce-anti-fraud' ),
							'type'     => 'textarea',
							'desc'     => '',
							'desc_tip'   => __( 'Add additional email address(es) you want to be notified upon exceeding Risk-score. Press “Comma”, TAB or ENTER button for the next entry.', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_custom_email',
							'css'         => 'width:100%; height: 100px;',
							'default'     => '',
							'class'       => 'wc_af_tags_input',
						),
						array(
							'name'     => __( 'Email Notification Score', 'woocommerce-anti-fraud' ),
							'type'     => 'select',
							'options'  => $score_options,
							'desc'     => '',
							'desc_tip'     => __( 'Risk scores that meet or exceed this value will trigger email alerts to your specified email addresses.', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_email_score',
							'css'         => 'display: block; width: 5em;',
							'default' => '50',
						),
						array(
							'type' => 'sectionend',
							'id'   => 'wc_af_email_alert_settings'
						),  
						) 
					);
					
				} else if ( 'black_list' == $current_section ) {
					
					/**
					* WCAF Filter Plugin  Blacklist Settings
					*
					* @since 1.0.0
					* @param array $settings Array of the plugin settings
					*/
					$settings = apply_filters( 'myplugin_company_file_settings', array(

						array(
							'name' => __( 'Blacklist', 'woocommerce-anti-fraud' ),
							'type' => 'title',
							'desc' => __( 'WooCommerce Anti-Fraud allows you to create a list of email addresses as well as a list of IP addresses that will always be marked as potential fraud transactions.', 'woocommerce-anti-fraud' ),
							'id'   => 'wc_af_blacklist_settings',
						),

						array(
							'name' => __( 'Email Blacklisting', 'woocommerce-anti-fraud' ),
							'type' => 'section',
							'desc' => '',
							'desc_tip' => '',
							'id'   => 'wc_af_sub_blacklist_settings',
							'class' => 'wc_af_sub-section',
							'css'   => 'display: block;'
						),
						//Enable email blacklist
						array(
							'title'       => __( 'Email Blacklist', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => 'wc_af_email_blacklist',
							'default'     => 'no',
							'desc' => '',
							'desc_tip'    => __( 'Enable the email blacklist function to block emails captured by WooCommerce Anti-fraud rules.', 'woocommerce-anti-fraud' ),
							'id'   =>'wc_settings_anti_fraudenable_automatic_email_blacklist',
						),
						//Enable automatic blacklisting
						array(
							'title'       => __( 'Automatic Blacklisting', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => 'wc_af_automatic_blacklist',
							'default'     => 'no',
							'desc' => '',
							'desc_tip'    => __( 'Add email addresses of orders reported with a high risk of fraud to blacklist automatically.', 'woocommerce-anti-fraud' ),
							'id'          => 'wc_settings_anti_fraudenable_automatic_blacklist',
						),
						//Block these email addresses
						array(
							'name'        => __( 'Blocked Email Addresses', 'woocommerce-anti-fraud' ),
							'type'        => 'textarea',
							'desc'        =>'',
							'desc_tip'   => __( 'The email addresses listed in the text area will be considered unsafe. You can also add or remove emails manually here. Type “,” or press TAB/ENTER button for the next entry.', 'woocommerce-anti-fraud' ),
							'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . 'blacklist_emails',
							'css'         => 'width:100%; height: 100px;',
							'default'     => '',
							'class'       => 'wc_af_tags_input',
						),

						array(
							'name' => __( 'IP Blacklisting', 'woocommerce-anti-fraud' ),
							'type' => 'section',
							'desc' => '',
							'desc_tip' => '',
							'id'   => 'wc_af_sub_ip_blacklist_settings',
							'class' => 'wc_af_sub-section',
							'css'   => 'display: block;'
						),
						//Enable IP blacklist
						array(
							'title'       => __( 'IP Blacklist', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => 'wc_af_ip_blacklist',
							'default'     => 'no',
							'desc' => __( 'Enable the IP blacklist function', 'woocommerce-anti-fraud' ),
							'desc_tip'    => __( 'Enable the IP blacklist function to block IP addresses with high risk of frauds.', 'woocommerce-anti-fraud' ),
							'id'   => 'wc_settings_anti_fraudenable_automatic_ip_blacklist',
						),
						//Block these email addresses
						array(
							'name'        => __( 'Blocked IP Adresses', 'woocommerce-anti-fraud' ),
							'type'        => 'textarea',
							'desc'        => '',
							'desc_tip'    => __( 'The IP addresses listed in the text area will be considered unsafe.  You can also add or remove IP addresses manually here. Type “,” or press TAB/ENTER button for the next entry.', 'woocommerce-anti-fraud' ),
							'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . 'blacklist_ipaddress',
							'css'         => 'width:100%; height: 100px;',
							'default'     => '',
							'class'       => 'wc_af_tags_input',
						),
						
						array(
							'type' => 'sectionend',
							'id'   => 'wc_af_blacklist_settings'
						),
						) 
					);
					
				} else if ('paypal_settings' == $current_section) {
					/**
					* WCAF Filter Plugin Paypal Settings
					*
					* @since 1.0.0
					* @param array $settings Array of the plugin settings
					*/
					$settings = apply_filters( 'wc_af_paypal_settings', array(
						
						array(
							'name' => __( 'Paypal Settings', 'woocommerce-anti-fraud' ),
							'type' => 'title',
							'desc' => __( 'These settings are specific to Paypal Payment Gateway. This means, when the customer use their Paypal account for making payment for their orders, then these rules come into action.<hr/>', 'woocommerce-anti-fraud' ),
							'id'   => 'wc_af_paypal_settings',
						),
						array(
							'title'       => __( 'Enable/Disable', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => 'wc_af_paypal_verification',
							'default'     => 'no',
							'desc' => __( 'Select to enable Paypal verification', 'woocommerce-anti-fraud' ),
							'id'   => 'wc_af_paypal_verification',
						),
						//Prevent downloads if verification failed or still processing
						array(
							'title'       => __( 'Block Downloads', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => 'wc_af_paypal_verification',
							'default'     => 'no',
							'desc' => __( 'Prevent digital downloads if verification failed or still processing', 'woocommerce-anti-fraud' ),
							'id'   => 'wc_af_paypal_prevent_downloads',
						),
						//Time span before further attempts
						array(
							'name'     => __( 'Verification Retry', 'woocommerce-anti-fraud' ),
							'type'     => 'number',
							'desc'     => __( 'Number of days that have to pass before sending another email if the order is still waiting for verification', 'woocommerce-anti-fraud' ),
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
							'name'     => __( 'Auto Cancellation Days', 'woocommerce-anti-fraud' ),
							'type'     => 'number',
							'desc'     => __( 'Orders that are not verified within the given number of days will be automatically cancelled', 'woocommerce-anti-fraud' ),
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
							'desc'        => __( 'Verified email addresses', 'woocommerce-anti-fraud' ),
							'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_paypal_verified_address',
							'class'         => 'wc_af_tags_input',
							'default'     => '',
						),
						array(
							'type' => 'sectionend',
							'id'   => 'wc_af_paypal_settings'
						),
						array(
							'name' => __( 'Email Template', 'woocommerce-anti-fraud' ),
							'type' => 'title',
							'desc' => __( 'Configure the email template below. The following tags can be embedded in the template fields:<br/><br/><b>{site_title}</b> - replaced with the Wordpress Site Title<br/><b>{site_email}</b> - replaced with the Wordpress site admin email address', 'woocommerce-anti-fraud' ),
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
							'desc'     => __( 'Emails can be sent either as plain text or HTML', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_paypal_email_format',
							'default' => 'html',
							'css'       => 'display: block; width: 5em;',
						),
						//Email subject
						array(
							'name'     => __( 'Email Subject', 'woocommerce' ),
							'desc'     => __( 'The subject of the email that will be sent to the purchaser', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_paypal_email_subject',
							'type'     => 'text',
							'placeholder' => __( '[{site_title}] Confirm your PayPal email address', 'woocommerce-anti-fraud' )
						),
						//Email body
						array(
							'name'        => __( 'Email body', 'woocommerce-anti-fraud' ),
							'type'        => 'textarea',
							'desc'        => __( 'Enter the body of the email to be sent to the purchaser in the text area below:', 'woocommerce-anti-fraud '),
							'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_email_body',
							'css'         => 'width:100%; height: 100px;',
							'default'     => __( 'Hi!We have received your order on {site_title}, but to complete we have to verify your PayPal email address.If you havent made or authorized any purchase, please, contact PayPal support service immediately,and email us to {site_email} for having your money back.', 'woocommerce-anti-fraud' ),
						),
						
						array(
							'type' => 'sectionend',
							'id' => 'wc_af_paypal_email_settings'
						),
						
						) 
					);
				} else if ( '' == $current_section ) {
					
					$generalSettingsArray = array(
						
						array(
							'name' => __( 'General Settings', 'woocommerce-anti-fraud' ),
							'type' => 'title',
							'desc' => __( 'General behaviours of the Anti Fraud plugin are configured on this page.<hr/>', 'woocommerce-anti-fraud' ),
							'id'   => 'wc_af_general_settings'
						),
						
						//thresholds settings
						array(
							'name' => __( 'Set Risk Thresholds', 'woocommerce-anti-fraud' ),
							'type' => 'section',
							'desc' => __( 'There are three risk thresholds.  Low, Medium and High.   In this section you can define where the boundary point between those thresholds.   The maximum risk score is 100, the minimum is 0.<br/>', 'woocommerce-anti-fraud' ),
							'desc_tip' => __( 'There are three risk thresholds: Low, Medium and High. In this section, you can define where the boundary point between those thresholds. The maximum risk score is 100, the minimum is 0. Orders are considered Low, Medium, and High Risk Orders based on these values.', 'woocommerce-anti-fraud' ),
							'id'   => 'wc_af_thresholds_settings',
							'class' => 'wc_af_sub-section',
							'css'   => 'display: block;'
						),
						array(
							'name'     => __( 'Medium Risk threshold', 'woocommerce-anti-fraud' ),
							'type'     => 'number',
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_low_risk_threshold',
							'css'         => 'display: block; width: 5em;',
							'desc'  => '',
							'desc_tip'  => 'The threshold between Low and Medium Risk Score. Move the left handle on slider or update the value in text field to set the boundary between Low and Medium-Risk orders. Risk Score less than this value is Low-Risk order and a greater value is the starting point of Medium Risk Orders.',
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
							'desc'  => '',
							'desc_tip'  => 'This is a threshold between Medium and High-Risk Score. Slide the right handle on slider or update the value in text field to set the boundary between Medium and High-Risk orders. Risk Score less than this value is the end point of Medium-Risk order and a greater value is High-Risk order.',
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
							'title'       => __( 'Enable debug logging', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'no',
							'id'    => 'wc_af_enable_debug_logging'
						),
						array(
							'type' => 'sectionend',
							'id' => 'wc_af_thresholds_settings'
						),
						//thresholds settings
						array(
							'name' => __( 'Pre-Purchase Assesment', 'woocommerce-anti-fraud' ),
							'type' => 'section',
							'desc'  => '',
							'desc_tip' => __( 'Evaluate customers before making the actual payment.  If the fraud score reaches a high-risk order range before making payment, the customer order will be denied in advance, and a custom message appears on the checkout page explaining buyer the reason for denial.' ),
							'id'   => 'wc_af_pre_purchase_settings',
							'class' => 'wc_af_sub-section',
							'css'   => 'display: block;'
						),
						array(
							'title'       => __( 'Pre-Payment Checking', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'no',
							'desc'        => '',
							'desc_tip'    => 'Enable pre-payment checking to assess fraud before making payment and block buyers with high fraud risk from placing any order and notify them via alert message.',
							'id'    => 'wc_af_fraud_check_before_payment',
						),
						//Pre payment custom text
						array(
							'title'       => __( 'Notification message for Blocked users', 'woocommerce-anti-fraud' ),
							'type'        => 'textarea',
							'label'       => '',
							'desc'        => '',
							'default'     => __( 'Website Administrator does not allow you to place this order. Please contact our support team. Sorry for any inconvenience.', 'woocommerce-anti-fraud' ),
							'css'         => 'width:100%; height: 100px;',
							'desc_tip' => __( 'Adds a custom alert message for blocked users on Pre-Payment Check. This message will be shown to the user on the checkout page while they attempt to pay.', 'woocommerce-anti-fraud' ),
							'id'    => 'wc_af_pre_payment_message',
						),
						
						array(
							'type' => 'sectionend',
							'id' => 'wc_af_pre_purchase_settings'
						),
						
						array(
							'name' => __( 'Change Order Status based on Risk Score', 'woocommerce-anti-fraud' ),
							'type' => 'section',
							'desc'  => '',
							'desc_tip' => __( 'Updates order status to cancel and/or on-hold when order passes a certain Risk Score threshold.' ),
							'id'   => 'wc_af_order_status_settings',
							'class' => 'wc_af_sub-section',
							'css'   => 'display: block;'
						),
						array(
							'title'       => __( 'Update Order Status based on Fraud Score', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'yes',
							'desc'       => '',
							'desc_tip' => __( 'Enable to cancel and/or put orders on hold based on Risk Score.', 'woocommerce-anti-fraud' ),
							'id'    => 'wc_af_fraud_update_state',
						),
						array(
							'name'     => __( 'Weighting to Cancel Order', 'woocommerce-anti-fraud' ),
							'type'     => 'select',
							'options'  => $score_options,
							'option'   => '',
							'desc'     => '',
							'desc_tip'     => __( 'Orders with a score equal to or greater than this value will be automatically cancelled. <br/> To disable it, slide the slider to the extreme left or Select 0.', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_cancel_score',
							'css'         => 'display: block; width: 5em;',
							'default' => '90',
						),
						array(
							'title'     => __( 'Weighting to put Order On-hold', 'woocommerce-anti-fraud' ),
							'type'     => 'select',
							'options'  => $score_options,
							'desc'     => '',
							'desc_tip'     => __( 'Orders with a score equal to or greater than this number will be automatically set on hold. <br/> To disable it, slide the slider to the extreme left or Select 0.', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_hold_score',
							'css'         => 'display: block; width: 5em;',
							'default' => '70',
						),
						array(
							'type' => 'sectionend',
							'id' => 'wc_af_order_status_settings'
						),
						
						array(
							'name' => __( 'Whitelist Payment Methods', 'woocommerce-anti-fraud' ),
							'type' => 'section',
							'desc'   => '',
							'desc_tip' => __( 'Payments made via the whitelisted payment methods will bypass anti-fraud’s rule to change order status based on risk score.' ),
							'id'   => 'wc_af_whitelist_payment_settings',
							'class' => 'wc_af_sub-section',
							'css'   => 'display: block;'
						),
						array(
							'title'       => __( 'Enable Whitelisting of Payment Methods', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'desc'       => '',
							'desc_tip'    => __('Enable to add payment methods to the whitelist.'),
							'default'     => 'no',
							'id'    => 'wc_af_enable_whitelist_payment_method'
						),
						array(
							'title'        => __( 'Select Payment Methods to be Whitelisted', 'woocommerce-anti-fraud' ),
							'type'        => 'multiselect',
							'options'  => $availableMethods,
							'desc'     => '',
							'desc_tip'        => __( 'Press “Ctrl” or “Command” key and click multiple payment methods to whitelist and scroll down to select more payment methods', 'woocommerce-anti-fraud '),
							'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_whitelist_payment_method',
							'default'     => $wc_af_whitelist_payment_methods
						),
						array(
							'type' => 'sectionend',
							'id' => 'wc_af_whitelist_payment_settings'
						),
						
						array(
							'name' => __( 'User Roles Whitelisting', 'woocommerce-anti-fraud' ),
							'type' => 'section',
							'desc'  => '',
							'desc_tip' => __( 'Filter accounts with different user roles to bypass Anti-fraud scoring system.' ),
							'id'   => 'wc_af_user_role_settings',
							'class' => 'wc_af_sub-section',
							'css'   => 'display: block;'
						),
						array(
							'title'       => __( 'Enable Whitelisting of User Roles', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'desc'        => '',
							'desc_tip'    => __('Enable it to activate whitelisting of User Roles.'),
							'default'     => 'no',
							'id'    => 'wc_af_enable_whitelist_user_roles'
						),
						array(
							'title'       => __( 'Select User Roles to be Whitelisted', 'woocommerce-anti-fraud' ),
							'type'        => 'multiselect',
							'options'     => $user_roles,
							'desc'      => '',
							'desc_tip'  => __('Press “Ctrl” or “Command” key and click one or multiple user roles to whitelist. Scroll up/down to select and see more user roles if any.'),
							'id'    => 'wc_af_whitelist_user_roles',
							'placeholder' => __( 'Select roles', 'woocommerce-anti-fraud' ),
							'default'     => $wc_af_whitelist_user_roles,
						),
						array(
							'type' => 'sectionend',
							'id' => 'wc_af_user_role_settings'
						),


						array(
							'name' => __( 'Whitelisted Emails', 'woocommerce-anti-fraud' ),
							'type' => 'section',
							'desc'  => '',
							'desc_tip' => __( 'List of Emails whitelitsted. Add emails manullay to make whitlist. Unblocked emails also added to this list.' ),
							'id'   => 'wc_af_email_whitelist_settings',
							'class' => 'wc_af_sub-section',
							'css'   => 'display: block;'
						),
						array(
							'name'        => __( 'Email Whitelist', 'woocommerce-anti-fraud' ),
							'type'        => 'textarea',
							'desc'        => '',
							'desc_tip'        => __( 'Enter any email you want to be whitelisted. Press “Tab” or “Comma” after entering any new domain.', 'woocommerce-anti-fraud '),
							'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_whitelist',
							'css'         => 'width:100%; height: 100px;',
							'class'       => 'wc_af_tags_input',
							'default'     => '',
						),
						
						array(
							'type' => 'sectionend',
							'id' => 'wc_af_email_whitelist_settings'
						),
						
						
						array(
							'type' => 'sectionend',
							'id' => 'wc_af_general_settings'
						),
						
					);
					
					/**
					* WCAF Filter Plugin General Settings
					*
					* @since 1.0.0
					* @param array $settings Array of the plugin settings
					*/ 
					$settings = apply_filters( 'wc_af_general_settings', $generalSettingsArray );
					
				} else if ( 'rules' == $current_section ) {
					
					/**
					* WCAF Filter Plugin General Settings
					*
					* @since 1.0.0
					* @param array $settings Array of the plugin settings
					*/
					$settings = apply_filters( 'wc_af_rule_settings', array(
						
						array(
							'name' => __( 'General Rules', 'woocommerce-anti-fraud'  ),
							'type' => 'title',
							'desc' => __('Each rule that is matched will add the configured &quot;Rule Weight&quot; value to the overall fraud score.  In this section you can configure general fraud detection rules.<hr/>', 'woocommerce-anti-fraud' ),
							'id'   => 'wc_af_rule_settings'
						),
				
						array(
							'name' => __( 'First Time Purchase Rules', 'woocommerce-anti-fraud' ),
							'type' => 'section',
							'desc' => __( 'Rule set for Buyers who are purchasing from your website for the first time.', 'woocommerce-anti-fraud' ),
							'desc_tip' => __( 'Rule set for Buyers who are purchasing from your website for the first time.', 'woocommerce-anti-fraud' ),
							'id'   => 'wc_af_first_time_purchase_settings',
							'class' => 'wc_af_sub-section',
							'css'   => 'display: block;'
						),
				
						array(
							'title'       => __( 'First Purchase on Website', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'yes',
							'desc'        => '',
							'desc_tip' => __( 'Enable it to identify customers purchasing from your website for the first time.', 'woocommerce-anti-fraud' ),
							'id'    => 'wc_af_first_order'
						),
						array(
							'name'     => __( 'First Purchase Rule Weight', 'woocommerce-anti-fraud' ),
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
						array(
							'title'       => __( 'Re-Check First Orders in Processing State?', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'no',
							'desc' => '',
							'desc_tip' => __( 'Enable it to perform first-order re-check on orders in processing state.', 'woocommerce-anti-fraud' ),
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
							'type' => 'sectionend',
							'id' => 'wc_af_first_time_purchase_settings'
						),
				
				
						array(
							'name' => __( 'IP, Billing and Shipping Address-based Rules', 'woocommerce-anti-fraud' ),
							'type' => 'section',
							'desc' => '',
							'desc_tip' => __( 'Rules set for identifying fraudulent orders triggering risks based on IP, billing and shipping addresses.', 'woocommerce-anti-fraud' ),
							'id'   => 'wc_af_address_based_rules_settings',
							'class' => 'wc_af_sub-section',
							'css'   => 'display: block;'
						),
						array(
							'title'       => __( 'Does IP Address Match Location?', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'yes',
							'desc' => '',
							'desc_tip' => __( 'Enable it to identify that customer\'s physical location matches the location provided by IP address.', 'woocommerce-anti-fraud' ),
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

						/* Geo Location */
						array(
							'title'       => __( 'Does Geo Location Match?', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'yes',
							'desc' => '',
							'desc_tip' => __( 'Enable it to identify that customer\'s shipping/billing state matches the state provided by Geo Location.', 'woocommerce-anti-fraud' ),
							'id'    => 'wc_af_geolocation_order'
						),
						array(
							'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
							'type'     => 'number',
							'options'  => $rule_weight,
							'desc'     => __( '<br/>' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_geolocation_order_weight',
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
							'desc' => '',
							'desc_tip' => __( 'Enable to identify difference between billing and shipping addresses.', 'woocommerce-anti-fraud' ),
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
							'title'       => __( 'Enable Phone Number and Billing Country Check', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'no',
							'desc'        => '',
							'desc_tip'    => __( 'If you enable this rule, then it is highly recommended that you use a separate Phone Number Validation plugin to make sure your customers specify a correct international phone number format on the checkout page. Else, this rule will treat an invalid phone number as a risk.', 'woocommerce-anti-fraud' ),
							'id'          => 'wc_af_billing_phone_number_order'
						
						),
						
						array(
							'name'     => __( 'Rule Weight', 'woocommerce-anti-fraud' ),
							'type'     => 'number',
							'options'  => $rule_weight,
							'desc'     => __( 'Weight of the single rule in the total calculation of risk.', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_billing_phone_number_order_weight',
							'css'         => 'display: block;',
							'custom_attributes' => array(
								'min'  => 0,
								'step' => 1,
								'max'  => 100
							),
						),
						array(
							'title'       => __( 'Is Customer behind Proxy or VPN?', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'yes',
							'desc' => '',
							'desc_tip' => __( 'Check if the customer is behind either a proxy or a VPN', 'woocommerce-anti-fraud'  ),
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
							'type' => 'sectionend',
							'id' => 'wc_af_address_based_rules_settings'
						),
				
						array(
							'name' => __( 'Multiple Orders Attempts using Different Addresses from Same IP', 'woocommerce-anti-fraud' ),
							'type' => 'section',
							'desc' => '',
							'desc_tip' => __( 'Rule to check buyer is ordering products using different addresses from the same IP over a certain period from your store.', 'woocommerce-anti-fraud' ),
							'id'   => 'wc_af_multi_order_attempts_rules_settings',
							'class' => 'wc_af_sub-section',
							'css'   => 'display: block;'
						),
						array(
							'title'       => __( 'Purchased from same IP but Different Customer Addresses?
							', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'yes',
							'desc' => '',
							'desc_tip' => __( 'Enable this rule to monitor if multiple orders with different billing or shipping addresses have originated from the same IP address. Make sure to set time span of checking.', 'woocommerce-anti-fraud' ),
							'id'    => 'wc_af_ip_multiple_check'
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
							'name'     => __( 'Past Number of Days to Check ', 'woocommerce-anti-fraud' ),
							'type'     => 'number',
							'desc'     => '',
							'desc_tip'     => __( 'The number of days in the past to check IP addresses and different physical addresses.', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_ip_multiple_time_span',
							'css'         => 'display: block; width: 5em;',
							'default' => '2',
							'custom_attributes' => array(
								'min'  => 0,
								'step' => 1,
							),
						),
				
						array(
							'type' => 'sectionend',
							'id' => 'wc_af_multi_order_attempts_rules_settings'
						),
				
						array(
							'name' => __( 'Origin Countries', 'woocommerce-anti-fraud' ),
							'type' => 'section',
							'desc' => '',
							'desc_tip' => __( 'International orders tend to have a higher fraud risk than orders that originate in your home country. Merchants often find that certain origin countries have a higher potential for fraud. These Rule sets help manage such risks.', 'woocommerce-anti-fraud' ),
							'id'   => 'wc_af_origin_countries_rules_settings',
							'class' => 'wc_af_sub-section',
							'css'   => 'display: block;'
						),
						array(
							'title'       => __( 'Is it International Order?', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'yes',
							'desc' => '',
							'desc_tip' => __( 'Enable to check if the order originates from outside of your store\'s home country.', 'woocommerce-anti-fraud' ),
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
							'title'       => __( 'Is Order from High-Risk Country?', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'yes',
							'desc' => '',
							'desc_tip' => __( 'Enable it to check if orders originate from unsafe countries. Select unsafe countries from the list below.', 'woocommerce-anti-fraud' ),
							'id'    => 'wc_af_unsafe_countries'
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
							'name'        => __( 'Mark Unsafe Countries', 'woocommerce-anti-fraud' ),
							'type'        => 'multiselect',
							'desc'        => '',
							'desc_tip'    => 'Press Ctrl or ⌘ key and select multiple countries that you think are unsafe for your area of service.',
							'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_define_unsafe_countries_list',
							'class'        => 'chzn-drop',
							'options'      => $this->get_countries(),
							'default'      => $wc_af_unsafe_countries_list
						),
				
				
						array(
							'type' => 'sectionend',
							'id' => 'wc_af_origin_countries_rules_settings'
						),
				
						array(
							'name' => __( 'High-Risk Email Domains', 'woocommerce-anti-fraud' ),
							'type' => 'section',
							'desc' => '',
							'desc_tip' => __( 'Merchants often find that certain email domains have a higher potential for fraud. These Rule sets will help you manage High-Risk Email Domains.<br/>Identify High-Risk Domains by manually adding such email domains to High-Risk Domain section and/or using QuickEmailVerification.com API key to make this operation automatic.', 'woocommerce-anti-fraud' ),
							'id'   => 'wc_af_high_risk_domain_rules_settings',
							'class' => 'wc_af_sub-section',
							'css'   => 'display: block;'
						),
						array(
							'title'       => __( 'Is Suspicious / High-Risk Email Domain?', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'yes',
							'desc' => '',
							'desc_tip' => __( 'Enable it to check if customer\'s email address originates from any high-risk domain listed below and/or add API for “quickemailverification. com” for automatic checking', 'woocommerce-anti-fraud' ),
							'id'    => 'wc_af_suspecius_email'
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
							'name'        => __( 'High-Risk Domains', 'woocommerce-anti-fraud' ),
							'type'        => 'textarea',
							'desc'        => '',
							'desc_tip'    => __( 'Enter any email origin domains you consider to be high-risk. Press “Tab” or “Comma” after entering any new domain.', 'woocommerce-anti-fraud '),
							'id'          => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_suspecious_email_domains',
							'css'         => 'width:100%; height: 100px;',
							'default'     => $this->suspicious_domains(),
							'class'       => 'wc_af_tags_input'
						),
						array(
							'title'       => __( 'API Key for quickemailverification.com', 'woocommerce-anti-fraud' ),
							'type'        => 'password',
							'desc' => __( 'Don\'t have quickemailverification account? <a href="">Signup here</a><br/>Verify 50 emails for free. Upgrade if you want more', 'woocommerce-anti-fraud' ),
							'desc_tip' => __( 'You can use quickemailverification.com to get more accurate results for false email domain-related checks. ', 'woocommerce-anti-fraud' ),
							'id'    => 'check_email_domain_api_key'
						),
				
						array(
							'type' => 'sectionend',
							'id' => 'wc_af_high_risk_domain_rules_settings'
						),
				
						array(
							'name' => __( 'Order Amounts and Attempts', 'woocommerce-anti-fraud' ),
							'type' => 'section',
							'desc' => '',
							'desc_tip' => __( 'The rules in this section are triggered based on defined order amounts and by counting attempted orders. You can also limit orders within a certain time frame. Orders with unusually high values, or customers who make an excessive number of transactions in a short period are more likely to be fraudulent.', 'woocommerce-anti-fraud' ),
							'id'   => 'wc_af_order_amount_attempts_rules_settings',
							'class' => 'wc_af_sub-section',
							'css'   => 'display: block;'
						),
						array(
							'title'       => __( 'Order Amount is Above Average?', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'yes',
							'desc' => '',
							'desc_tip' => __( 'Check if order significantly exceeds the average order amount for your site. Set multiplier value to trigger this rule when order place is value times greater than an average order.', 'woocommerce-anti-fraud' ),
							'id'    => 'wc_af_order_avg_amount_check'
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
							'name'     => __( 'Average Multiplier', 'woocommerce-anti-fraud' ),
							'type'     => 'number',
							'desc'     => '',
							'desc_tip'     => __( 'Enter the multiplier value to check amount over average transaction value that will trigger the rule (expressed as a multiplier). For example, if you want to limit orders that are 2 times greater than the average value of orders, set the multiplier value to 2.', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_avg_amount_multiplier',
							'css'         => 'display: block; width: 5em;',
							'default' => '2',
							'custom_attributes' => array(
								'min'  => 0,
								'step' => 1,
							),
						),
						array(
							'title'       => __( 'Order Exceeds Maximum Amount Limit?', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'yes',
							'desc' => '',
							'desc_tip' => __( 'Enable this rule to confirm that the total amount of the order does not exceed maximum configured amount below.', 'woocommerce-anti-fraud' ),
							'id'    => 'wc_af_order_amount_check'
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
							'name'     => __( 'Amount Limit ($)', 'woocommerce-anti-fraud' ),
							'type'     => 'text',
							'desc'     => '',
							'desc_tip' => __( 'Enter the maximum amount of purchase accepted in your store.', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_amount_limit',
							'css'         => 'display: block; width: 5em;',
							'default' => '0',
						),
						
						array(
							'title'       => __( 'Too many order attempts?', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'yes',
							'desc' => '',
							'desc_tip' => __( 'Enabling this rule allows you to limit number of orders per user within the time span configured below.', 'woocommerce-anti-fraud' ),
							'id'    => 'wc_af_attempt_count_check'
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
							'name'     => __( 'Time Span to Check (hours)', 'woocommerce-anti-fraud' ),
							'type'     => 'number',
							'desc'     => '',
							'desc_tip'     => __( 'Enter number of hours you want to check ', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_attempt_time_span',
							'css'         => 'display: block; width: 5em;',
							'default' => '24',
						),
						array(
							'name'     => __( 'Maximum Allowed Number of Orders in Time Span?', 'woocommerce-anti-fraud' ),
							'type'     => 'number',
							'desc'     => '',
							'desc_tip'     => __( 'Enter a total number of orders that a buyer can make in the specified time span.', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_settings_' . self::SETTINGS_NAMESPACE . '_max_order_attempt_time_span',
							'css'         => 'display: block; width: 5em;',
							'default' => '5',
							'custom_attributes' => array(
								'min'  => 0,
								'step' => 1,
							),
						),
				
						array(
							'title'       => __( 'Limit Number of Orders between Time', 'woocommerce-anti-fraud' ),
							'type'        => 'checkbox',
							'label'       => '',
							'default'     => 'no',
							'desc' => '',
							'desc_tip' => __( 'Enabling this rule limits the number of orders to be placed within a certain period of the day.', 'woocommerce-anti-fraud' ),
							'id'    => 'wc_af_limit_order_count'
						),
						array(
							'name'     => __( 'Start Time ', 'woocommerce-anti-fraud' ),
							'type'     => 'time',
							'desc'     => '',
							'desc_tip'     => __( 'Enter the start time for the rule to limit orders ', 'woocommerce-anti-fraud' ),
							'id'       => 'wc_af_limit_time_start',
							'css'         => 'display: block; width: 8.5em;',
							'default' => '',
						),
						array(
							'name'     => __('End Time ', 'woocommerce-anti-fraud'),
							'type'     => 'time',
							'desc'     => '',
							'desc_tip' => __('Enter the end time for the rule to limit orders', 'woocommerce-anti-fraud'),
							'id'       => 'wc_af_limit_time_end',
							'css'         => 'display: block; width: 8.5em;',
							'default' => '',
						),
						array(
							'name'     => __('Maximum Allowed Number of Orders Between Time', 'woocommerce-anti-fraud'),
							'type'     => 'number',
							'desc'     => '',
							'desc_tip'     => __('Enter maximum number of orders that can be placed during specified time span.', 'woocommerce-anti-fraud'),
							'id'       => 'wc_af_allowed_order_limit',
							'css'         => 'display: block; width: 5em;',
							'default' => '',
						),
				
				
						array(
							'type' => 'sectionend',
							'id' => 'wc_af_order_amount_attempts_rules_settings'
						),
				
						array(
							'type' => 'sectionend',
							'id' => 'wc_af_rule_settings'
						),
						
						) 
					);
					
				}

				/**
				 * Filter WCAF Settings
				 *
				 * @since 1.0.0
				 * @param array $settings Array of the plugin settings
				 */
				return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );

			}


			public function opmc_add_admin_field_button( $value ) {
				$option_value = (array) WC_Admin_Settings::get_option( $value['id'] );
				$description = WC_Admin_Settings::get_field_description( $value );
				
				?>
			   
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="<?php echo esc_attr( $value['id'] ); ?>">
							<?php echo esc_html( $value['title'] ); ?>
						</label>
						<?php if (isset($value['title_icon']) && '' != $value['title_icon']) : ?>
							<img src="<?php echo esc_attr($value['title_icon']); ?>" alt="<?php echo esc_html( $value['title'] ); ?>" style="width:100px;">
						<?php endif; ?>
					</th>
					
					<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
						<a 
							target="_blank"
							href="<?php echo esc_attr( $value['href'] ); ?>" 
							class="<?php echo esc_attr($value['class']); ?>" 
							id="<?php echo esc_attr($value['id']); ?>"							
							><?php echo esc_attr( $value['name'] ); ?></a> 
						<?php echo wp_kses_post($description['description']); ?>
					   
					</td>
				</tr>

				<?php       
			}

			public function opmc_add_admin_field_section( $value ) {
				$option_value = (array) WC_Admin_Settings::get_option( $value['id'] );
				$description = WC_Admin_Settings::get_field_description( $value );
				
				?>
				</table>
				   <div class="wc_af_sub-section-title">
					   <h3 class="<?php echo esc_attr( $value['class'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>"><?php echo wp_kses_post( $value['name'] ); ?> <?php echo  wp_kses_post($description['tooltip_html']); ?></h3>
					   <?php if ( ! empty( $value['description'] ) ) : ?>
						   <p><?php echo wp_kses_post( $value['description'] ); ?></p>
					   <?php endif; ?>
				   </div>
				<table class="form-table opmc_wc_af_table">
				<?php       
			}

			public function opmc_score_slider( $score = 0, $order_risk = false, $thresholds = false ) {


				$medium_risk_score = get_option('wc_settings_anti_fraud_low_risk_threshold');
				$high_risk_score = get_option('wc_settings_anti_fraud_higher_risk_threshold');

				$gradient = 'linear-gradient(90deg, rgba(90,198,125,1) ' . ( $medium_risk_score - 25 ) . '%, rgba(205,119,57,1) ' . ( $high_risk_score ) . '%, rgba(185,74,72,1) 100%)';
				$score_bar_bg = '#777777';

				if ('' == $score) {
					$score = 0;
				}

				if (0 == $score && !$thresholds) {
					$score_bar_bg = '#777777';
				} else {
					$score_bar_bg = $gradient;
				}

				if (0 < $score && $medium_risk_score >= $score) {
					$score_value_border = 'rgba(90,198,125,1)';
				} elseif ($medium_risk_score <= $score && $high_risk_score >= $score) {
					$score_value_border = 'rgba(205,119,57,1)';
				} elseif ($high_risk_score < $score) {
					$score_value_border = 'rgba(185,74,72,1)';
				} else {
					$score_value_border = '#777777';
				}

				if ($order_risk) {
					if (0 < $score && $medium_risk_score >= $score) {
						$order_risk_status = 'Low Risk Order';
					} elseif ($medium_risk_score <= $score && $high_risk_score >= $score) {
						$order_risk_status = 'Medium Risk Order';
					} elseif ($high_risk_score < $score) {
						$order_risk_status = 'High Risk Order';
					} else {
						$order_risk_status = 'Disabled';
					}	
				}


				?>
				<div class="score-slider <?php echo ( $thresholds ) ? 'multi-handle' : ''; ?>">
					<div class="score-bar" style="background:<?php echo esc_attr($score_bar_bg); ?>;">

						<?php if ($thresholds) : ?>
							<div class="score-value min-score" style="left:<?php echo esc_attr(get_option('wc_settings_anti_fraud_low_risk_threshold')); ?>%; border-color:rgba(90,198,125,1);">
								<span class="score-text min-score">
									<?php echo esc_html(get_option('wc_settings_anti_fraud_low_risk_threshold')); ?>
								</span>
							</div>
							<div class="score-value max-score" style="left:<?php echo esc_attr(get_option('wc_settings_anti_fraud_higher_risk_threshold')); ?>%; border-color:rgba(205,119,57,1)">
								<span class="score-text max-score">
									<?php echo esc_html(get_option('wc_settings_anti_fraud_higher_risk_threshold')); ?>
								</span>
							</div>
							<?php else : ?>
								<div class="score-value" style="left:<?php echo esc_attr($score); ?>%; border-color:<?php echo esc_attr($score_value_border); ?>" data-min-score="<?php echo esc_attr($medium_risk_score); ?>" data-max-score="<?php echo esc_attr($high_risk_score); ?>">
									<span class="score-text">
										<?php echo wp_kses_post( $score ); ?>
									</span>
								</div>
							<?php endif; ?>

						</div>
						<?php if ($thresholds) : ?>
							<div class="score-bar-label">
								<span>Low Risk</span>
								<span>Medium Risk</span>
								<span>High Risk</span>
							</div>
							<?php elseif ($order_risk) : ?>
								<div class="score-bar-label">
									<span><?php echo esc_html($order_risk_status); ?></span>
								</div>
						<?php else : ?>
							<div class="score-bar-label">
								<span>No Importance</span>
								<span>Moderate</span>
								<span>High Importance</span>
							</div>
						<?php endif; ?>
				</div>
				<?php
			}


			/**
			 * Output the settings
			 *
			 * @since 1.0
			 */
			public function output() {

				global $current_section;

				$settings = $this->get_settings( $current_section );
				// $form_fields = $this->get_form_fields($settings);
				include_once WOOCOMMERCE_ANTI_FRAUD_PLUGIN_DIR . 'anti-fraud-core/tamplate-admin-settings-page.php';
				// WC_Admin_Settings::output_fields( $settings );
				// $this->generate_custom_settings_html( $settings, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}


			/**
			 * Save settings
			 *
			 * @since 1.0
			 */
			public function save() {

				global $current_section;

				$settings = $this->get_settings( $current_section );
				if (wp_verify_nonce('test', 'wc_none')) {
					return true;
				}
				if (isset($_POST['wc_settings_anti_fraud_whitelist'])) {
					$_POST['wc_settings_anti_fraud_whitelist'] = str_replace(',' , "\n" , sanitize_text_field( $_POST['wc_settings_anti_fraud_whitelist'] )); 
				}
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

					if ('yes' == $setting_type &&  'wc_af_minfraud_settings' == $curr_settings) {

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

						if ('AUTHORIZATION_INVALID' === $error) {

							$this->log->add( 'MinFraud', '====== Authentication failed' );
							$this->log->add( 'MinFraud', print_r( array( 'MaxMind Account Id' => $maxmind_user, 'MaxMind license key' => $maxmind_license_key ), true ) );
							update_option('wc_af_maxmind_authentication', false);
							add_action('admin_notices', array( $this, 'auth_error_admin_notice'));

						} else {

							$this->log->add( 'MinFraud', '====== Authentication succeed ' );
							update_option('wc_af_maxmind_authentication', true);
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
				<p><strong><?php echo esc_html_e( 'Your Account Id or License Key could not be authenticated!!', 'woocommerce-anti-fraud' ); ?></strong></p>
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
				<p><strong><?php echo esc_html_e( 'Great, authenticated successfully!!', 'woocommerce-anti-fraud'); ?></strong></p>
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

					if ('yes' == $setting_type &&  'wc_af_general_settings' == $curr_settings) {

						$email_api_key = get_option( 'check_email_domain_api_key' );
						$admin_email = get_option( 'admin_email' );

						$contents = @file_get_contents("https://api.quickemailverification.com/v1/verify?email=$admin_email&apikey=$email_api_key");

						if ( false !== $contents ) {

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
							$email_api_key = get_option( 'check_email_domain_api_key' );
							if ( !empty($email_api_key) ) {
															add_action('admin_notices', array( $this, 'auth_quickemailverification_error_admin_notice'));
							}
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
				<p><strong><?php echo esc_html_e( 'Your Quickemailverification API Key could not be authenticated!!', 'woocommerce-anti-fraud' ); ?></strong></p>
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
				<p><strong><?php echo esc_html_e( 'Great, Quickemailverification authenticated successfully!!', 'woocommerce-anti-fraud' ); ?></strong></p>
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
				<p><strong><?php echo esc_html_e( 'Great, Quickemailverification authenticated successfully but you don\'t have enough credit to use this service.', 'woocommerce-anti-fraud' ); ?></strong></p>
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
			
			public function whitelist_user_roles() {
				
			}


		}
		$settings[] = new WC_AF_Settings();
		 return $settings;
		/*$a =  new WC_AF_Settings();*/
		//$res = $a->get_settings();

	}
	add_filter( 'woocommerce_get_settings_pages', 'wc_af_add_settings', 15 );
endif;

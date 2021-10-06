<?php
/*
 * Support Tab Setting
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSCashbackModule' ) ) {

	class RSCashbackModule {

		public static function init() {

			add_action( 'rs_default_settings_fpcashback' , array( __CLASS__ , 'set_default_value' ) ) ;

			add_action( 'woocommerce_rs_settings_tabs_fpcashback' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

			add_action( 'woocommerce_update_options_fprsmodules_fpcashback' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

			add_action( 'woocommerce_admin_field_rs_select_inc_user_search_label' , array( __CLASS__ , 'rs_select_inc_user_search_label' ) ) ;

			add_action( 'woocommerce_admin_field_rs_select_exc_user_search_label' , array( __CLASS__ , 'rs_select_exc_user_search_label' ) ) ;

			add_action( 'woocommerce_admin_field_rs_encash_applications_list' , array( __CLASS__ , 'encash_list_overall_applications' ) ) ;

			add_action( 'woocommerce_admin_field_rs_encash_applications_edit_lists' , array( __CLASS__ , 'encash_applications_list_table' ) ) ;

			add_action( 'woocommerce_admin_field_redeeming_conversion_for_cash_back' , array( __CLASS__ , 'reward_system_redeeming_points_conversion_for_cash_back' ) ) ;

			add_action( 'woocommerce_admin_field_rs_enable_disable_cashback_module' , array( __CLASS__ , 'enable_module' ) ) ;
			
			add_action('woocommerce_admin_field_rs_minimum_points_based_on_user_role_for_cashback', array( __CLASS__ , 'minimum_points_based_on_user_role_for_cashback' ) );

			add_filter( 'woocommerce_fpcashback' , array( __CLASS__ , 'cashback_redeeming_percentage_based_on_userrole' ) ) ;

			add_action( 'fp_action_to_reset_module_settings_fpcashback' , array( __CLASS__ , 'reset_cashback_module' ) ) ;

			add_action( 'rs_display_save_button_fpcashback' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

			add_action( 'rs_display_reset_button_fpcashback' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;

			if ( check_whether_hoicker_is_active() ) {
				add_filter( 'woocommerce_fpcashback' , array( __CLASS__ , 'rs_function_to_add_label_for_wallet' ) ) ;
			}
		}

		/*
		 * Cashback redeeming percentage based on userrole.
		 * 
		 * @return string.
		 */

		public static function cashback_redeeming_percentage_based_on_userrole( $settings ) {

			global $wp_roles ;

			$updated_settings = array() ;

			foreach ( $settings as $section ) {

				if ( isset( $section[ 'id' ] ) && 'rs_user_role_reward_points_for_redeem_cashback' == $section[ 'id' ] &&
						isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {

					foreach ( $wp_roles->role_names as $role_name_key => $role_name_value ) {

						$updated_settings[] = array(
							'name'     => esc_html__( 'Reward Points Cashback Percentage for ' . sanitize_text_field($role_name_value) . ' User Role' , 'rewardsystem' ) ,
							'desc'     => esc_html__( 'Please enter the percentage value' , 'rewardsystem' ) ,
							'class'    => 'rs_cashback_' . sanitize_text_field($role_name_key) . '_for_redeem_percentage rewardpoints_userrole_for_redeem_cashback' ,
							'id'       => 'rs_cashback_' . sanitize_text_field($role_name_key) . '_for_redeem_percentage' ,
							'css'      => 'min-width:150px;' ,
							'std'      => '100' ,
							'default'  => '100' ,
							'type'     => 'text' ,
							'newids'   => 'rs_cashback_' . sanitize_text_field($role_name_key) . '_for_redeem_percentage' ,
							'desc_tip' => true ,
								) ;
					}
					$updated_settings[] = array(
						'type' => 'sectionend' ,
						'id'   => 'rs_user_role_reward_points_for_redeem_cashback' ,
							) ;
				}

				$updated_settings[] = $section ;
			}

			return $updated_settings ;
		}
		
		/*
		 * Minimum points based on user role for cashback.
		 */
		public static function minimum_points_based_on_user_role_for_cashback() {
			
			global $wp_roles ;
			if (!is_object($wp_roles)) {
				return;
			}
			
			foreach ($wp_roles->role_names as $role_name_key => $role_name_value) :
				?>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label><?php esc_html_e('Minimum Points required to display the Cashback Form for ' . $role_name_value); ?></label>
					</th>
					<td class="forminp forminp-number">
						<input name="rs_minimum_points_based_on_<?php echo esc_attr($role_name_key); ?>_for_cashback" 
							   class="rs_minimum_points_based_on_<?php echo esc_attr($role_name_key); ?>_for_cashback rs_minimum_points_based_on_user_role_for_cashback" 
							   min="1"
							   step ="any"
							   type="number"
							   value="<?php echo esc_attr(get_option('rs_minimum_points_based_on_' . $role_name_key . '_for_cashback')); ?>"> 							
					</td>
				</tr>
				
				<?php
			endforeach;
		}

		/*
		 * Function label settings to Member Level Tab
		 */

		public static function reward_system_admin_fields() {
			global $woocommerce ;
			$walletia_label     = get_option( 'rs_encashing_wallet_menu_label' ) ? get_option( 'rs_encashing_wallet_menu_label' ) : 'Hoicker Wallet' ;
			$list_of_user_roles = fp_user_roles() ;
			if ( check_whether_hoicker_is_active() ) {
				$payment_method = array(
					'1' => __( 'PayPal' , 'rewardsystem' ) ,
					'2' => __( 'Custom Payment' , 'rewardsystem' ) ,
					'4' => __( $walletia_label , 'rewardsystem' ) ,
					'3' => __( 'All' , 'rewardsystem' ) ,
						) ;
			} else {
				$payment_method = array(
					'1' => __( 'PayPal' , 'rewardsystem' ) ,
					'2' => __( 'Custom Payment' , 'rewardsystem' ) ,
					'3' => __( 'All' , 'rewardsystem' ) ,
						) ;
				if ( '4' == get_option( 'rs_select_payment_method' )  ) {
					update_option( 'rs_select_payment_method' , 3 ) ;
				}
			}
			return apply_filters( 'woocommerce_fpcashback' , array(
				array(
					'type' => 'rs_modulecheck_start' ,
				) ,
				array(
					'name' => __( 'Cashback Module' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_activate_cashback_module'
				) ,
				array(
					'type' => 'rs_enable_disable_cashback_module' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_activate_cashback_module' ) ,
				array(
					'type' => 'rs_modulecheck_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Cashback Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_reward_point_encashing_settings'
				) ,
				array(
					'name'     => __( 'Enable Cashback for Reward Points' , 'rewardsystem' ) ,
					'desc'     => __( 'Enable this option to provide the feature to Cashback the Reward Points earned by the Users' , 'rewardsystem' ) ,
					'id'       => 'rs_enable_disable_encashing' ,
					'std'      => '2' ,
					'default'  => '2' ,
					'type'     => 'select' ,
					'newids'   => 'rs_enable_disable_encashing' ,
					'options'  => array(
						'1' => __( 'Enable' , 'rewardsystem' ) ,
						'2' => __( 'Disable' , 'rewardsystem' ) ,
					) ,
					'desc_tip' => true ,
				) ,
				array(
					'name'    => __( 'Cashback Form accessible selection' , 'rewardsystem' ) ,
					'id'      => 'rs_user_selection_type_for_cashback' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'select' ,
					'newids'  => 'rs_user_selection_type_for_cashback' ,
					'options' => array(
						'1' => __( 'All User(s)' , 'rewardsystem' ) ,
						'2' => __( 'Include User(s)' , 'rewardsystem' ) ,
						'3' => __( 'Exclude User(s)' , 'rewardsystem' ) ,
						'4' => __( 'All User Role(s)' , 'rewardsystem' ) ,
						'5' => __( 'Include User role(s)' , 'rewardsystem' ) ,
						'6' => __( 'Exclude User role(s)' , 'rewardsystem' ) ,
					)
				) ,
				array(
					'type' => 'rs_select_inc_user_search_label' ,
				) ,
				array(
					'type' => 'rs_select_exc_user_search_label' ,
				) ,
				array(
					'name'        => __( 'Include User Role(s)' , 'rewardsystem' ) ,
					'id'          => 'rs_select_inc_userrole' ,
					'css'         => 'min-width:343px;' ,
					'std'         => '' ,
					'default'     => '' ,
					'placeholder' => 'Search for a User Role' ,
					'type'        => 'multiselect' ,
					'options'     => $list_of_user_roles ,
					'newids'      => 'rs_select_inc_userrole' ,
					'desc_tip'    => false ,
				) ,
				array(
					'name'        => __( 'Exclude User Role(s)' , 'rewardsystem' ) ,
					'id'          => 'rs_select_exc_userrole' ,
					'css'         => 'min-width:343px;' ,
					'std'         => '' ,
					'default'     => '' ,
					'placeholder' => 'Search for a User Role' ,
					'type'        => 'multiselect' ,
					'options'     => $list_of_user_roles ,
					'newids'      => 'rs_select_exc_userrole' ,
					'desc_tip'    => false ,
				) ,
				array(
					'name'     => __( 'Allow User to Request Cashback' , 'rewardsystem' ) ,
					'id'       => 'rs_allow_user_to_request_cashback' ,
					'std'      => '1' ,
					'default'  => '1' ,
					'type'     => 'select' ,
					'newids'   => 'rs_allow_user_to_request_cashback' ,
					'options'  => array(
						'1' => __( 'Editable' , 'rewardsystem' ) ,
						'2' => __( 'Non-Editable' , 'rewardsystem' ) ,
					) ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Current Reward Points Label' , 'rewardsystem' ) ,
					'id'       => 'rs_total_points_for_cashback_request' ,
					'std'      => 'Current Reward Points' ,
					'default'  => 'Current Reward Points' ,
					'type'     => 'text' ,
					'newids'   => 'rs_total_points_for_cashback_request' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Points for Cashback Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Points the Label for Cashback' , 'rewardsystem' ) ,
					'id'       => 'rs_encashing_points_label' ,
					'std'      => 'Points for Cashback' ,
					'default'  => 'Points for Cashback' ,
					'type'     => 'text' ,
					'newids'   => 'rs_encashing_points_label' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Currency Value Label' , 'rewardsystem' ) ,
					'id'       => 'rs_encashing_currency_label' ,
					'std'      => 'Currency Value' ,
					'default'  => 'Currency Value' ,
					'type'     => 'text' ,
					'newids'   => 'rs_encashing_currency_label' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Reason for Cashback Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter label for Reason Cashback' , 'rewardsystem' ) ,
					'id'       => 'rs_encashing_reason_label' ,
					'std'      => 'Reason for Cashback' ,
					'default'  => 'Reason for Cashback' ,
					'type'     => 'text' ,
					'newids'   => 'rs_encashing_reason_label' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'    => __( 'Reason Field' , 'rewardsystem' ) ,
					'desc'    => __( 'By enabling this checkbox, reason field will be displayed as Mandatory in the Cashback Form' , 'rewardsystem' ) ,
					'id'      => 'rs_reason_mandatory_for_cashback_form' ,
					'type'    => 'checkbox' ,
					'std'     => 'yes' ,
					'default' => 'yes' ,
					'newids'  => 'rs_reason_mandatory_for_cashback_form' ,
				) ,
				array(
					'name'     => __( 'Payment Method Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Payment Method Label for Cashback' , 'rewardsystem' ) ,
					'id'       => 'rs_encashing_payment_method_label' ,
					'std'      => 'Payment Method' ,
					'default'  => 'Payment Method' ,
					'type'     => 'text' ,
					'newids'   => 'rs_encashing_payment_method_label' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'    => __( 'Display Payment Method' , 'rewardsystem' ) ,
					'id'      => 'rs_select_payment_method' ,
					'std'     => '3' ,
					'default' => '3' ,
					'type'    => 'select' ,
					'newids'  => 'rs_select_payment_method' ,
					'options' => $payment_method ,
				) ,
				array(
					'name'    => __( 'Save Payment Details' , 'rewardsystem' ) ,
					'desc'    => __( 'By enabling this option you can save your customer[s] payment details which they used in the form' , 'rewardsystem' ) ,
					'id'      => 'rs_allow_admin_to_save_previous_payment_method' ,
					'type'    => 'checkbox' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'newids'  => 'rs_allow_admin_to_save_previous_payment_method' ,
				) ,
				array(
					'name'     => __( 'PayPal Email Address Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter PayPal Email Address Label for Cashback' , 'rewardsystem' ) ,
					'id'       => 'rs_encashing_payment_paypal_label' ,
					'std'      => 'PayPal Email Address' ,
					'default'  => 'PayPal Email Address' ,
					'type'     => 'text' ,
					'newids'   => 'rs_encashing_payment_paypal_label' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Custom Payment Details Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Custom Payment Details Label for Cashback' , 'rewardsystem' ) ,
					'id'       => 'rs_encashing_payment_custom_label' ,
					'std'      => 'Custom Payment Details' ,
					'default'  => 'Custom Payment Details' ,
					'type'     => 'text' ,
					'newids'   => 'rs_encashing_payment_custom_label' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Cashback Form Submit Button Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Cashback Form Submit Button Label ' , 'rewardsystem' ) ,
					'id'       => 'rs_encashing_submit_button_label' ,
					'std'      => 'Submit' ,
					'default'  => 'Submit' ,
					'type'     => 'text' ,
					'newids'   => 'rs_encashing_submit_button_label' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'    => __( 'After submitting the Cashback form' , 'rewardsystem' ) ,
					'id'      => 'rs_select_type_to_redirect' ,
					'std'     => 'Submit' ,
					'default' => 'Submit' ,
					'type'    => 'select' ,
					'newids'  => 'rs_select_type_to_redirect' ,
					'options' => array(
						'1' => __( 'Same Page' , 'rewardsystem' ) ,
						'2' => __( 'Redirect to the Custom Page' , 'rewardsystem' ) ,
					)
				) ,
				array(
					'name'     => __( 'Custom Page URL' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Custom Page URL' , 'rewardsystem' ) ,
					'id'       => 'rs_custom_page_url_after_submit' ,
					'std'      => '' ,
					'default'  => '' ,
					'type'     => 'text' ,
					'newids'   => 'rs_custom_page_url_after_submit' ,
					'desc_tip' => true ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_reward_point_encashing_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Cashback Restriction Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => 'rs_cashback_restriction_settings'
				) ,
				array(
					'name'    => __( 'Cashback restriction based on' , 'rewardsystem' ) ,
					'id'      => 'rs_select_type_for_min_max_cashback' ,
					'std'     => '1' ,
					'default' => '1' ,
					'type'    => 'select' ,
					'newids'  => 'rs_select_type_for_min_max_cashback' ,
					'options' => array(
						'1' => __( 'Minimum/Maximum Points' , 'rewardsystem' ) ,
						'2' => __( 'User Role' , 'rewardsystem' ) ,
					)
				) ,
				array(
					'name'              => __( 'Minimum Points for Cashback of Reward Points' , 'rewardsystem' ) ,
					'desc'              => __( 'Enter the Minimum points that the user should have in order to Submit the Cashback Request' , 'rewardsystem' ) ,
					'id'                => 'rs_minimum_points_encashing_request' ,
					'std'               => '' ,
					'default'           => '' ,
					'type'              => 'number' ,
					'newids'            => 'rs_minimum_points_encashing_request' ,
					'custom_attributes' => array(
						'min' => 0
					) ,
					'desc_tip'          => true ,
				) ,
				array(
					'name'              => __( 'Maximum Points for Cashback of Reward Points' , 'rewardsystem' ) ,
					'desc'              => __( 'Enter the Maximum points that the user should enter order to Submit the Cashback Request' , 'rewardsystem' ) ,
					'id'                => 'rs_maximum_points_encashing_request' ,
					'std'               => '' ,
					'default'           => '' ,
					'type'              => 'number' ,
					'newids'            => 'rs_maximum_points_encashing_request' ,
					'custom_attributes' => array(
						'min' => 0
					) ,
					'desc_tip'          => true ,
				) ,
				array(
					'type'    => 'rs_minimum_points_based_on_user_role_for_cashback'
				),
				array( 'type' => 'sectionend' , 'id' => 'rs_cashback_restriction_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Google reCAPTCHA Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_google_recaptcha_settings'
				) ,
				array(
					'name'    => __( 'Enable Google reCAPTCHA for Cash Back Form' , 'rewardsystem' ) ,
					'id'      => 'rs_enable_recaptcha_to_display' ,
					'class'   => 'rs_enable_recaptcha_to_display' ,
					'type'    => 'checkbox' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'newids'  => 'rs_enable_recaptcha_to_display' ,
				) ,
				array(
					'name'     => __( 'Google reCaptcha Label' , 'rewardsystem' ) ,
					'id'       => 'rs_google_recaptcha_label' ,
					'std'      => 'Google ReCaptcha' ,
					'default'  => 'Google reCaptcha' ,
					'type'     => 'text' ,
					'newids'   => 'rs_google_recaptcha_label' ,
				) ,
				array(
					'name'     => __( 'Site Key ' , 'rewardsystem' ) ,
					'id'       => 'rs_google_recaptcha_site_key' ,
					'std'      => '' ,
					'default'  => '' ,
					'type'     => 'text' ,
					'newids'   => 'rs_google_recaptcha_site_key' ,
					'desc_tip' => true ,
										/* translators: %s: - Site key */
					'desc'     => sprintf( __( 'You can find the Site key %s' , 'rewardsystem' ) , '<a target="_blank" href="https://www.google.com/recaptcha/admin#list">' . __( 'here' , 'rewardsystem' ) . '</a>' ) ,
				) ,
				array(
					'name'     => __( 'Secret Key ' , 'rewardsystem' ) ,
					'id'       => 'rs_google_recaptcha_secret_key' ,
					'std'      => '' ,
					'default'  => '' ,
					'type'     => 'text' ,
					'newids'   => 'rs_google_recaptcha_secret_key' ,
					'desc_tip' => true ,
										/* translators: %s: - Secret key */
					'desc'     => sprintf( __( 'You can find the Secret key %s' , 'rewardsystem' ) , '<a target="_blank" href="https://www.google.com/recaptcha/admin#list">' . __( 'here' , 'rewardsystem' ) . '</a>' ) ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_google_recaptcha_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Cashback Percentage based on User Role' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => 'rs_user_role_reward_points_for_redeem_cashback' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => 'rs_user_role_reward_points_for_redeem_cashback' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Redeeming Points Conversion Settings for Cashback' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_redeem_point_conversion_for_cash_back'
				) ,
				array(
					'type' => 'redeeming_conversion_for_cash_back' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_redeem_point_conversion_cash_back' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Cashback Request List' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_request_for_cash_back_setting'
				) ,
				array(
					'type' => 'rs_encash_applications_list' ,
				) ,
				array(
					'type' => 'rs_encash_applications_edit_lists' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_request_for_cash_back_setting' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Email Notification for Cashback' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => 'rs_email_notfication_for_cashback'
				) ,
				array(
					'name'    => __( 'Enable Email Notification for Admin' , 'rewardsystem' ) ,
					'desc'    => __( 'By enabling this option, admin to receives the email notification when the users request for cashback' , 'rewardsystem' ) ,
					'id'      => 'rs_email_notification_for_Admin_cashback' ,
					'class'   => 'rs_email_notification_for_Admin_cashback' ,
					'type'    => 'checkbox' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'newids'  => 'rs_email_notification_for_Admin_cashback' ,
				) ,
				array(
					'name'     => __( 'Email Sender Option' , 'rewardsystem' ) ,
					'id'       => 'rs_mail_sender_for_admin_for_cashback' ,
					'class'    => 'rs_mail_sender_for_admin_for_cashback' ,
					'std'      => 'woocommerce' ,
					'default'  => 'woocommerce' ,
					'type'     => 'radio' ,
					'options'  => array(
						'woocommerce' => __( 'Woocommerce' , 'rewardsystem' ) ,
						'local'       => __( 'Local' , 'rewardsystem' ) ,
					) ,
					'newids'   => 'rs_mail_sender_for_admin_for_cashback' ,
					'desc_tip' => true ,
					'desc'     => __( 'Woocommerce - Default Email from name and from address <br> Local - Manually Adding name for from name and from address' , 'rewardsystem' ) ,
				) ,
				array(
					'name'    => __( '"From" Name' , 'rewardsystem' ) ,
					'id'      => 'rs_from_name_for_admin_cashback' ,
					'class'   => 'rs_from_name_for_admin_cashback' ,
					'std'     => '' ,
					'default' => '' ,
					'type'    => 'text' ,
					'newids'  => 'rs_from_name_for_admin_cashback' ,
				) ,
				array(
					'name'    => __( '"From" Email' , 'rewardsystem' ) ,
					'id'      => 'rs_from_email_for_admin_cashback' ,
					'class'   => 'rs_from_email_for_admin_cashback' ,
					'std'     => '' ,
					'default' => '' ,
					'type'    => 'email' ,
					'newids'  => 'rs_from_email_for_admin_cashback' ,
				) ,
				array(
					'name'    => __( 'Email Subject' , 'rewardsystem' ) ,
					'type'    => 'textarea' ,
					'id'      => 'rs_email_subject_message_for_cashback' ,
					'newids'  => 'rs_email_subject_message_for_cashback' ,
					'class'   => 'rs_email_subject_message_for_cashback' ,
					'std'     => 'Cashback Request – Notification' ,
					'default' => 'Cashback Request – Notification' ,
				) ,
				array(
					'name'    => __( 'Email Message' , 'rewardsystem' ) ,
					'type'    => 'textarea' ,
					'id'      => 'rs_email_message_for_cashback' ,
					'newids'  => 'rs_email_message_for_cashback' ,
					'class'   => 'rs_email_message_for_cashback' ,
					'std'     => 'Hi,<br><br>The Cashback Request is given by [username] with [_rs_point_for_cashback] points. <br><br> Selected Payment Method : [rs_payment_gateway]<br><br>Thanks<br><br>' ,
					'default' => 'Hi,<br><br>The Cashback Request is given by [username] with [_rs_point_for_cashback] points. <br><br>Payment Method [rs_payment_gateway]<br><br>Thanks<br><br>' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => 'rs_email_notification_for_cashback' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'My Cashback Table Label Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_my_cashback_label_settings'
				) ,
				array(
					'name'     => __( 'My Cashback Table' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_table' ,
					'std'      => '1' ,
					'desc_tip' => true ,
					'default'  => '1' ,
					'newids'   => 'rs_my_cashback_table' ,
					'type'     => 'select' ,
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'     => __( 'My Cashback Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the My Cashback Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_title' ,
					'std'      => 'My Cashback' ,
					'default'  => 'My Cashback' ,
					'type'     => 'text' ,
					'newids'   => 'rs_my_cashback_title' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'S.No Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Serial Number Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_sno_label' ,
					'std'      => 'S.No' ,
					'default'  => 'S.No' ,
					'type'     => 'text' ,
					'newids'   => 'rs_my_cashback_sno_label' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Username Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Username Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_userid_label' ,
					'std'      => 'Username' ,
					'default'  => 'Username' ,
					'type'     => 'text' ,
					'newids'   => 'rs_my_cashback_userid_label' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Requested for Cashback Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Requested for Cashback Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_requested_label' ,
					'std'      => 'Requested for Cashback' ,
					'default'  => 'Requested for Cashback' ,
					'type'     => 'text' ,
					'newids'   => 'rs_my_cashback_requested_label' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Status Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Status On Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_status_label' ,
					'std'      => 'Status' ,
					'default'  => 'Status' ,
					'type'     => 'text' ,
					'newids'   => 'rs_my_cashback_status_label' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Action Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Action On Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_action_label' ,
					'std'      => 'Action' ,
					'default'  => 'Action' ,
					'type'     => 'rs_action_for_cash_back' ,
					'newids'   => 'rs_my_cashback_action_label' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'My Cashback Table - Shortcode' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_table_shortcode' ,
					'std'      => '1' ,
					'desc_tip' => true ,
					'default'  => '1' ,
					'newids'   => 'rs_my_cashback_table_shortcode' ,
					'type'     => 'select' ,
					'options'  => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'     => __( 'My Cashback Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the My Cashback Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_title_shortcode' ,
					'std'      => 'My Cashback' ,
					'default'  => 'My Cashback' ,
					'type'     => 'text' ,
					'newids'   => 'rs_my_cashback_title_shortcode' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'S.No Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Serial Number Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_sno_label_shortcode' ,
					'std'      => 'S.No' ,
					'default'  => 'S.No' ,
					'type'     => 'text' ,
					'newids'   => 'rs_my_cashback_sno_label_shortcode' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Username Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Username Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_userid_label_shortcode' ,
					'std'      => 'Username' ,
					'default'  => 'Username' ,
					'type'     => 'text' ,
					'newids'   => 'rs_my_cashback_userid_label_shortcode' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Requested for Cashback Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Requested for Cashback Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_requested_label_shortcode' ,
					'std'      => 'Requested for Cashback' ,
					'default'  => 'Requested for Cashback' ,
					'type'     => 'text' ,
					'newids'   => 'rs_my_cashback_requested_label_shortcode' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Status Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Status On Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_status_label_shortcode' ,
					'std'      => 'Status' ,
					'default'  => 'Status' ,
					'type'     => 'text' ,
					'newids'   => 'rs_my_cashback_status_label_shortcode' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Action Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Action On Label' , 'rewardsystem' ) ,
					'id'       => 'rs_my_cashback_action_label_shortcode' ,
					'std'      => 'Action' ,
					'default'  => 'Action' ,
					'type'     => 'rs_action_for_cash_back' ,
					'newids'   => 'rs_my_cashback_action_label_shortcode' ,
					'desc_tip' => true ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_my_cashback_label_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Message Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_message_settings_encashing'
				) ,
				array(
					'name'     => __( 'Message displayed for Guest' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Message displayed for Guest' , 'rewardsystem' ) ,
					'id'       => 'rs_message_for_guest_encashing' ,
					'std'      => 'Please [rssitelogin] to Cashback your Reward Points.' ,
					'default'  => 'Please [rssitelogin] to Cashback your Reward Points.' ,
					'type'     => 'textarea' ,
					'newids'   => 'rs_message_for_guest_encashing' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Login Link for Guest Label' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Login link for Guest Label' , 'rewardsystem' ) ,
					'id'       => 'rs_encashing_login_link_label' ,
					'std'      => 'Login' ,
					'default'  => 'Login' ,
					'type'     => 'text' ,
					'newids'   => 'rs_encashing_login_link_label' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Message displayed for Banned Users' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Message Displayed for Banned Users' , 'rewardsystem' ) ,
					'id'       => 'rs_message_for_banned_users_encashing' ,
					'std'      => 'You cannot Cashback Your points' ,
					'default'  => 'You cannot Cashback Your points' ,
					'type'     => 'textarea' ,
					'newids'   => 'rs_message_for_banned_users_encashing' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Message displayed when Users don\'t have Reward Points' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Message to be Displayed when Users dont have Reward Points' , 'rewardsystem' ) ,
					'id'       => 'rs_message_users_nopoints_encashing' ,
					'std'      => 'You Don\'t have points for Cashback' ,
					'default'  => 'You Don\'t have points for Cashback' ,
					'type'     => 'textarea' ,
					'newids'   => 'rs_message_users_nopoints_encashing' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Message displayed when Cashback Request is Submitted' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Message to be Displayed when Cashback Request is Submitted' , 'rewardsystem' ) ,
					'id'       => 'rs_message_encashing_request_submitted' ,
					'std'      => 'Cashback Request Submitted' ,
					'default'  => 'Cashback Request Submitted' ,
					'type'     => 'textarea' ,
					'newids'   => 'rs_message_encashing_request_submitted' ,
					'desc_tip' => true ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_message_settings_encashing' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'CSV Settings (Export CSV for Paypal Mass Payment)' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_csv_message_settings_encashing'
				) ,
				array(
					'name'     => __( 'Custom Note for Paypal' , 'rewardsystem' ) ,
					'desc'     => __( 'A Custom Note for Paypal' , 'rewardsystem' ) ,
					'id'       => 'rs_encashing_paypal_custom_notes' ,
					'std'      => 'Thanks for your Business' ,
					'default'  => 'Thanks for your Business' ,
					'type'     => 'textarea' ,
					'newids'   => 'rs_encashing_paypal_custom_notes' ,
					'desc_tip' => true ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_csv_message_settings_encashing' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Error Message Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_error_settings_encashing'
				) ,
				array(
					'name'     => __( 'Error Message displayed when Points for Cashback Field is left Empty' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Message to be Displayed when Points for Cashback Field is Empty' , 'rewardsystem' ) ,
					'id'       => 'rs_error_message_points_empty_encash' ,
					'std'      => 'Points for Cashback Field cannot be empty' ,
					'default'  => 'Points for Cashback Field cannot be empty' ,
					'type'     => 'text' ,
					'newids'   => 'rs_error_message_points_empty_encash' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Error Message displayed when Points to Cashback Value is not a Number' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Message to be Displayed when Points To Cashback Field value is not a number' , 'rewardsystem' ) ,
					'id'       => 'rs_error_message_points_number_val_encash' ,
					'std'      => 'Please Enter only Numbers' ,
					'default'  => 'Please Enter only Numbers' ,
					'type'     => 'text' ,
					'newids'   => 'rs_error_message_points_number_val_encash' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Error Message displayed when Points entered for Cashback is more than the Points Earned' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Message to be Displayed when Points entered for Cashback is more than the Points Earned' , 'rewardsystem' ) ,
					'id'       => 'rs_error_message_points_greater_than_earnpoints' ,
					'std'      => 'Points Entered for Cashback is more than the Earned Points' ,
					'default'  => 'Points Entered for Cashback is more than the Earned Points' ,
					'type'     => 'text' ,
					'newids'   => 'rs_error_message_points_greater_than_earnpoints' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Error Message displayed when Current User Points is less than the Minimum Points for Cashback' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Message to be Displayed when Points entered for Cashback is more than the Maximum Points for Cashback' , 'rewardsystem' ) ,
					'id'       => 'rs_error_message_currentpoints_less_than_minimum_points' ,
					'std'      => 'You need a Minimum of [minimum_encash_points] points in order for Cashback' ,
					'default'  => 'You need a Minimum of [minimum_encash_points] points in order for Cashback' ,
					'type'     => 'textarea' ,
					'newids'   => 'rs_error_message_currentpoints_less_than_minimum_points' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Error Message displayed when Points entered to Cashback is less than the Minimum Points and more than Maximum Points' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Message to be Displayed when Points entered to Cashback is less than the Minimum Points and more than Maximum Points' , 'rewardsystem' ) ,
					'id'       => 'rs_error_message_points_lesser_than_minimum_points' ,
					'std'      => 'Please Enter Between [minimum_encash_points] and [maximum_encash_points] ' ,
					'default'  => 'Please Enter Between [minimum_encash_points] and [maximum_encash_points]' ,
					'type'     => 'textarea' ,
					'newids'   => 'rs_error_message_points_lesser_than_minimum_points' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Error Message displayed when Reason to Cashback Field is Empty' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Message to be Displayed when Reason To Cashback Field is Empty' , 'rewardsystem' ) ,
					'id'       => 'rs_error_message_reason_encash_empty' ,
					'std'      => 'Reason to Encash Field cannot be empty' ,
					'default'  => 'Reason to Encash Field cannot be empty' ,
					'type'     => 'text' ,
					'newids'   => 'rs_error_message_reason_encash_empty' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Error Message displayed when PayPal Email Address is Empty' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Message to be Displayed when PayPal Email Address is Empty' , 'rewardsystem' ) ,
					'id'       => 'rs_error_message_paypal_email_empty' ,
					'std'      => 'Paypal Email Field cannot be empty' ,
					'default'  => 'Paypal Email Field cannot be empty' ,
					'type'     => 'text' ,
					'newids'   => 'rs_error_message_paypal_email_empty' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Error Message displayed when PayPal Email Address Format is wrong' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Message to be Displayed when PayPal Email Address format is wrong' , 'rewardsystem' ) ,
					'id'       => 'rs_error_message_paypal_email_wrong' ,
					'std'      => 'Enter a Correct Email Address' ,
					'default'  => 'Enter a Correct Email Address' ,
					'type'     => 'text' ,
					'newids'   => 'rs_error_message_paypal_email_wrong' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Error Message displayed when Custom Payment Details field is left Empty' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Message to be Displayed when Custom Payment Details field is Empty' , 'rewardsystem' ) ,
					'id'       => 'rs_error_custom_payment_field_empty' ,
					'std'      => 'Custom Payment Details Field cannot be empty' ,
					'default'  => 'Custom Payment Details Field cannot be empty' ,
					'type'     => 'text' ,
					'newids'   => 'rs_error_custom_payment_field_empty' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Error Message displayed when reCAPTCHA field is Empty' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Message to be Displayed when reCAPTCHA field is Empty' , 'rewardsystem' ) ,
					'id'       => 'rs_error_recaptcha_field_empty' ,
					'std'      => 'reCAPTCHA is mandatory' ,
					'default'  => 'reCAPTCHA is mandatory' ,
					'type'     => 'text' ,
					'newids'   => 'rs_error_recaptcha_field_empty' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Error Message' , 'rewardsystem' ) ,
					'id'       => 'rs_minimum_points_based_on_userrole_error_msg' ,
					'std'      => 'You are eligible to submit the cashback form only when you have <b>[points_value]</b> points in your account.' ,
					'default'  => 'You are eligible to submit the cashback form only when you have <b>[points_value]</b> points in your account.' ,
					'type'     => 'textarea' ,
					'newids'   => 'rs_minimum_points_based_on_userrole_error_msg' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_error_settings_encashing' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Shortcode used in Form for Cashback' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => 'rs_shortcode_for_cashback'
				) ,
				array(
					'type' => 'title' ,
					'desc' => __('<b>[minimum_encash_points]</b> - To display minimum points required to get cashback<br><br>'
					. '<b>[maximum_encash_points]</b> - To display maximum points required to get cashback<br><br>'
					. '<b>[rssitelogin]</b> - To display login link for guests' , 'rewardsystem' ) ,
				) ,
				array( 'type' => 'sectionend' , 'id' => 'rs_shortcode_for_cashback' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
					) ) ;
		}

		/**
		 * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
		 */
		public static function reward_system_register_admin_settings() {

			woocommerce_admin_fields( self::reward_system_admin_fields() ) ;
		}

		/**
		 * Update the Settings on Save Changes may happen in SUMO Reward Points
		 */
		public static function reward_system_update_settings() {
			woocommerce_update_options( self::reward_system_admin_fields() ) ;
			if ( isset( $_REQUEST[ 'rs_select_inc_user_search' ] ) ) {
				update_option( 'rs_select_inc_user_search' , wc_clean(wp_unslash($_REQUEST[ 'rs_select_inc_user_search' ])) ) ;
			} else {
				update_option( 'rs_select_inc_user_search' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_select_exc_user_search' ] ) ) {
				update_option( 'rs_select_exc_user_search' , wc_clean(wp_unslash(_REQUEST[ 'rs_select_exc_user_search' ] ))) ;
			} else {
				update_option( 'rs_select_exc_user_search' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_redeem_point_for_cash_back' ] ) ) {
				update_option( 'rs_redeem_point_for_cash_back' , wc_clean(wp_unslash($_REQUEST[ 'rs_redeem_point_for_cash_back' ] ))) ;
			} else {
				update_option( 'rs_redeem_point_for_cash_back' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_redeem_point_value_for_cash_back' ] ) ) {
				update_option( 'rs_redeem_point_value_for_cash_back' , wc_clean(wp_unslash($_REQUEST[ 'rs_redeem_point_value_for_cash_back' ] ) ));
			} else {
				update_option( 'rs_redeem_point_value_for_cash_back' , '' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_cashback_module_checkbox' ] ) ) {
				update_option( 'rs_cashback_activated' , wc_clean(wp_unslash($_REQUEST[ 'rs_cashback_module_checkbox' ] ))) ;
			} else {
				update_option( 'rs_cashback_activated' , 'no' ) ;
			}
			
			global $wp_roles;
			if (is_object($wp_roles)) {
				foreach ($wp_roles->role_names as $role_name => $role_value) {
					$minimum_points_based_on_role = isset($_REQUEST['rs_minimum_points_based_on_' . $role_name . '_for_cashback']) ? wc_clean(wp_unslash($_REQUEST['rs_minimum_points_based_on_' . $role_name . '_for_cashback'])):0;
					update_option( 'rs_minimum_points_based_on_' . $role_name . '_for_cashback' , $minimum_points_based_on_role ) ;
				}
			}
		}

		/**
		 * Select the User(s) function 	     
		 */
		public static function rs_select_inc_user_search_label() {
			$field_id    = 'rs_select_inc_user_search' ;
			$field_label = __('Include User(s)' , 'rewardsystem');
			$getuser     = get_option( 'rs_select_inc_user_search' ) ;
			echo wp_kses_post(user_selection_field( $field_id , $field_label , $getuser ) );
		}

		public static function rs_select_exc_user_search_label() {
			$field_id    = 'rs_select_exc_user_search' ;
			$field_label = __('Exclude User(s)', 'rewardsystem') ;
			$getuser     = get_option( 'rs_select_exc_user_search' ) ;
			echo wp_kses_post(user_selection_field( $field_id , $field_label , $getuser )) ;
		}

		/**
		 * Initialize the Default Settings by looping this function
		 */
		public static function set_default_value() {
			foreach ( self::reward_system_admin_fields() as $setting ) {
				if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
					add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
				}
			}
		}

		public static function reset_cashback_module() {
			$settings = self::reward_system_admin_fields() ;
			RSTabManagement::reset_settings( $settings ) ;
			update_option( 'rs_redeem_point_for_cash_back' , '1' ) ;
			update_option( 'rs_redeem_point_value_for_cash_back' , '1' ) ;
		}

		public static function rs_function_to_add_label_for_wallet( $settings ) {
			$updated_settings = array() ;
			foreach ( $settings as $section ) {
				if ( isset( $section[ 'id' ] ) && '_rs_reward_point_encashing_settings' == $section[ 'id' ] &&
						isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
					$updated_settings[] = array(
						'name'     => __( 'Hoicker Wallet Label' , 'rewardsystem' ) ,
						'desc'     => __( 'Please Enter Wallet Label for Cashback' , 'rewardsystem' ) ,
						'id'       => 'rs_encashing_wallet_label' ,
						'std'      => 'Cashback will be added to your Hoicker Wallet' ,
						'default'  => 'Cashback will be added to your Hoicker Wallet' ,
						'type'     => 'text' ,
						'newids'   => 'rs_encashing_wallet_label' ,
						'desc_tip' => true ,
							) ;
					$updated_settings[] = array(
						'name'     => __( 'Hoicker Wallet Menu Label' , 'rewardsystem' ) ,
						'desc'     => __( 'Please Enter Wallet Menu Label for Cashback' , 'rewardsystem' ) ,
						'id'       => 'rs_encashing_wallet_menu_label' ,
						'std'      => 'Hoicker Wallet' ,
						'default'  => 'Hoicker Wallet' ,
						'type'     => 'text' ,
						'newids'   => 'rs_encashing_wallet_menu_label' ,
						'desc_tip' => true ,
							) ;
				}
				$updated_settings[] = $section ;
			}
			return $updated_settings ;
		}

		public static function encash_list_overall_applications() {
			global $wpdb ;
			global $current_section ;
			global $current_tab ;

			$testListTable = new FPRewardSystemEncashTabList() ;
			$testListTable->prepare_items() ;
			if ( ! isset( $_REQUEST[ 'encash_application_id' ] ) ) {
				$array_list = array() ;
				$message    = '' ;
				if ( 'encash_application_delete' === $testListTable->current_action() && isset( $_REQUEST[ 'id' ])) {
										/* translators: %s: Deleted count */
					$message = '<div class="updated below-h2" id="message"><p>' . sprintf( __( 'Items deleted: %d' ) , count( $_REQUEST[ 'id' ] ) ) . '</p></div>' ;
				}
				echo wp_kses_post($message) ;
				$testListTable->display() ;
			}
		}

		public static function encash_applications_list_table( $item ) {
			global $wpdb ;
			$message    = '' ;
			$notice     = '' ;
			$default    = array(
				'id'                    => 0 ,
				'userid'                => '' ,
				'pointstoencash'        => '' ,
				'encashercurrentpoints' => '' ,
				'reasonforencash'       => '' ,
				'encashpaymentmethod'   => '' ,
				'paypalemailid'         => '' ,
				'otherpaymentdetails'   => '' ,
				'status'                => '' ,
					) ;

			if ( isset( $_REQUEST[ 'nonce' ] ) ) {
				if ( wp_verify_nonce( wc_clean(wp_unslash($_REQUEST[ 'nonce' ])) , basename( __FILE__ ) ) ) {
					$item       = shortcode_atts( $default , wc_clean(wp_unslash($_REQUEST)) ) ;
					$item_valid = self::encash_validation( $item ) ;
					if ( true == $item_valid  ) {
						if ( 0 == $item[ 'id' ]  ) {
												$result       = $wpdb->insert( "{$wpdb->prefix}sumo_reward_encashing_submitted_data" , $item ) ;
							$item[ 'id' ] = $wpdb->insert_id ;
							if ( $result ) {
								$message = __( 'Item was successfully saved' ) ;
							} else {
								$notice = __( 'There was an error while saving item' ) ;
							}
						} else {
							$result = $wpdb->update( "{$wpdb->prefix}sumo_reward_encashing_submitted_data" , $item , array( 'id' => $item[ 'id' ] ) ) ;



							if ( $result ) {
								$message = __( 'Item was successfully updated' ) ;
							} else {
								$notice = __( 'There was an error while updating item' ) ;
							}
						}
					} else {
						// if $item_valid not true it contains error message(s)
						$notice = $item_valid ;
					}
				}
			} else {
				// if this is not post back we load item to edit or give new one to create
				$item = $default ;

				if ( isset( $_REQUEST[ 'encash_application_id' ] ) ) {
										$item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sumo_reward_encashing_submitted_data WHERE id = %d" , wc_clean(wp_unslash($_REQUEST[ 'encash_application_id' ])) ) , ARRAY_A ) ;

					if ( ! $item ) {
						$item   = $default ;
						$notice = __( 'Item not found' ) ;
					}
				}
			}
			?>
			<?php
			if ( isset( $_REQUEST[ 'encash_application_id' ] ) ) {
				$timeformat   = get_option( 'time_format' ) ;
				$dateformat   = get_option( 'date_format' ) . ' ' . $timeformat ;
				$expired_date = date_i18n( $dateformat ) ;
				?>
				<div class="wrap">
					<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
					<h3><?php esc_html_e( 'Edit Cashback Status' , 'rewardsystem' ) ; ?>
											<a class="add-new-h2" href="<?php echo esc_url(get_admin_url( get_current_blog_id() , 'admin.php?page=rewardsystem_callback&tab=encash_applications' ) ); ?>"><?php esc_html_e( 'Back to list', 'rewardsystem' ); ?></a>
					</h3>
					<?php if ( ! empty( $notice ) ) : ?>
						<div id="notice" class="error"><p><?php echo wp_kses_post($notice); ?></p></div>
					<?php endif ; ?>
					<?php if ( ! empty( $message ) ) : ?>
						<div id="message" class="updated"><p><?php echo wp_kses_post($message); ?></p></div>
					<?php endif ; ?>
					<form id="form" method="POST">
						<input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce( basename( __FILE__ ) )); ?>"/>
						<input type="hidden" name="id" value="<?php echo esc_attr($item[ 'id' ]); ?>"/>
						<input type="hidden" name="userid" value="<?php echo esc_attr($item[ 'userid' ]) ; ?>"/>
						<input type="hidden" value="<?php echo esc_attr($item[ 'setvendoradmins' ]) ; ?>" name="setvendoradmins"/>
						<input type="hidden" value="<?php echo esc_attr($item[ 'setusernickname' ] ); ?>" name="setusernickname"/>
						<input type="hidden" value="<?php echo esc_attr($expired_date) ; ?>" name="date"/>
						<div class="metabox-holder" id="poststuff">
							<div id="post-body">
								<div id="post-body-content">
									<table class="form-table">
										<tbody>                                        
											<tr>
												<th scope="row"><?php esc_html_e( 'Points for Cashback' , 'rewardsystem' ) ; ?></th>
												<td>
													<input type="text" name="pointstoencash" id="setvendorname" value="<?php echo esc_attr($item[ 'pointstoencash' ] ); ?>"/>
												</td>
											</tr>
											<tr>
												<th scope="row"><?php esc_html_e( 'Reason for Cashback' , 'rewardsystem' ) ; ?></th>
												<td>
													<textarea name="reasonforencash" rows="3" cols="30"><?php echo wp_kses_post($item[ 'reasonforencash' ]) ; ?></textarea>
												</td>
											</tr>
											<tr>
												<th scope="row"><?php esc_html_e( 'Application Status' , 'rewardsystem' ) ; ?></th>
												<td>
													<?php
													$selected_approved         = 'Paid' == $item[ 'status' ] ? 'selected=selected' : '' ;
													$selected_rejected         = 'Due' == $item[ 'status' ] ? 'selected=selected' : '' ;
													?>
													<select name = "status">                                                    
														<option value = "Paid" <?php echo esc_attr($selected_approved) ; ?>><?php esc_html_e( 'Paid' , 'rewardsystem' ) ; ?></option>
														<option value = "Due" <?php echo esc_attr($selected_rejected) ; ?>><?php esc_html_e( 'Due' , 'rewardsystem' ) ; ?></option>
													</select>
												</td>
											</tr>                                                                                
											<tr>
												<th scope="row"><?php esc_html_e( 'Cashback Payment Option' , 'rewardsystem' ) ; ?></th>
												<td>                                             
													<?php
													$selectedpaymentoption     = 'encash_through_paypal_method'  == $item[ 'encashpaymentmethod' ]? 'selected=selected' : '' ;
													$mainselectedpaymentoption = 'encash_through_custom_payment' == $item[ 'encashpaymentmethod' ]  ? 'selected=selected' : '' ;
													?>
													<select id="encashpaymentmethod" name="encashpaymentmethod">
														<option value="1" <?php echo esc_attr($selectedpaymentoption) ; ?>><?php esc_html_e( 'Paypal Address' , 'rewardsystem' ) ; ?></option>
														<option value="2" <?php echo esc_attr($mainselectedpaymentoption) ; ?>><?php esc_html_e( 'Custom Payment' , 'rewardsystem' ) ; ?></option>
													</select>
												</td>
											</tr>
											<tr>
												<th scope="row"><?php esc_html_e( 'User Paypal Email' , 'rewardsystem' ) ; ?></th>
												<td>
													<input type="text" name="paypalemailid" class="paypalemailid" value="<?php echo esc_attr($item[ 'paypalemailid' ] ); ?>"/>
												</td>
											</tr>
											<tr>
												<th scope="row"><?php esc_html_e( 'User Custom Payment Details' , 'rewardsystem' ) ; ?></th>
												<td>
													<textarea name='otherpaymentdetails' rows='3' cols='30' id='otherpaymentdetails' class='otherpaymentdetails'><?php echo wp_kses_post($item[ 'otherpaymentdetails' ] ); ?></textarea>
												</td>
											</tr>
										</tbody>
									</table>
									<input type="submit" value="<?php esc_html_e( 'Save Changes' , 'rewardsystem' ); ?>" id="submit" class="button-primary" name="submit">
								</div>
							</div>
						</div>                    
					</form>

				</div>
			<?php } ?>

			<?php
		}

		public static function encash_validation( $item ) {
			$messages = array() ;
			if ( empty( $messages ) ) {
				return true ;
			}
			return implode( '<br />' , $messages ) ;
		}

		public static function reward_system_redeeming_points_conversion_for_cash_back() {
			?>
			<tr valign="top">
				<td class="forminp forminp-text">
					<input type="number" step="any" min="0" value="<?php echo esc_attr(get_option( 'rs_redeem_point_for_cash_back' )) ; ?>" id="rs_redeem_point_for_cash_back" name="rs_redeem_point_for_cash_back"> <?php esc_html_e( 'Redeeming Point(s)' , 'rewardsystem' ) ; ?>
					&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
					<?php echo wp_kses_post(get_woocommerce_currency_symbol()) ; ?> 	<input type="number" step="any" min="0" value="<?php echo wp_kses_post(get_option( 'rs_redeem_point_value_for_cash_back' )) ; ?>" id="rs_redeem_point_value_for_cash_back" name="rs_redeem_point_value_for_cash_back"></td>
			</td>
			</tr>
			<?php
		}

		public static function enable_module() {
			RSModulesTab::checkbox_for_module( get_option( 'rs_cashback_activated' ) , 'rs_cashback_module_checkbox' , 'rs_cashback_activated' ) ;
		}

	}

	RSCashbackModule::init() ;
}

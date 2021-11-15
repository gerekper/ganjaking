<?php
/*
 * Gift Voucher - Module
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSGiftVoucher' ) ) {

	class RSGiftVoucher {

		public static function init() {

			add_action( 'woocommerce_rs_settings_tabs_fpgiftvoucher' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

			add_action( 'woocommerce_update_options_fprsmodules_fpgiftvoucher' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

			add_action( 'woocommerce_admin_field_rs_generate_voucher_code_settings' , array( __CLASS__ , 'settings_for_voucher_code_generation' ) ) ;

			add_action( 'woocommerce_admin_field_rs_offline_online_rewards_display_table_settings' , array( __CLASS__ , 'table_to_display_created_voucher_codes' ) ) ;

			add_action( 'fp_action_to_reset_module_settings_fpgiftvoucher' , array( __CLASS__ , 'reset_gift_voucher_tab' ) ) ;

			add_action( 'woocommerce_admin_field_rs_enable_gift_voucher_module' , array( __CLASS__ , 'enable_module' ) ) ;

			add_action( 'rs_default_settings_fpgiftvoucher' , array( __CLASS__ , 'set_default_value' ) ) ;

			add_action( 'rs_display_save_button_fpgiftvoucher' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

			add_action( 'rs_display_reset_button_fpgiftvoucher' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
		}

		public static function reward_system_admin_fields() {

			return apply_filters( 'woocommerce_fpgiftvoucher_settings' , array(
				array(
					'type' => 'rs_modulecheck_start' ,
				) ,
				array(
					'name' => __( 'Gift Voucher Module' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_activate_gift_voucher_module'
				) ,
				array(
					'type' => 'rs_enable_gift_voucher_module' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_activate_gift_voucher_module' ) ,
				array(
					'type' => 'rs_modulecheck_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Voucher Code Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_voucher_code_settings'
				) ,
				array(
					'name'    => __( 'Enable Voucher Code Settings' , 'rewardsystem' ) ,
					'id'      => 'rs_enable_voucher_code' ,
					'std'     => 'Enable' ,
					'default' => 'Enable' ,
					'newids'  => 'rs_enable_voucher_code' ,
					'type'    => 'select' ,
					'options' => array(
						'Enable'  => __( 'Enable' , 'rewardsystem' ) ,
						'Disable' => __( 'Disable' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'type' => 'rs_generate_voucher_code_settings' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_voucher_code_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Gift Voucher Redeem Field Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_my_account_voucher_table_settings'
				) ,
				array(
					'name'    => __( 'Gift Voucher Field' , 'rewardsystem' ) ,
					'id'      => 'rs_show_hide_redeem_voucher' ,
					'std'     => '1' ,
					'default' => '1' ,
					'newids'  => 'rs_show_hide_redeem_voucher' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Show' , 'rewardsystem' ) ,
						'2' => __( 'Hide' , 'rewardsystem' ) ,
					) ,
				) ,
				array(
					'name'    => __( 'Redeem your Gift Voucher Label' , 'rewardsystem' ) ,
					'id'      => 'rs_redeem_your_gift_voucher_label' ,
					'std'     => 'Redeem your Gift Voucher' ,
					'default' => 'Redeem your Gift Voucher' ,
					'newids'  => 'rs_redeem_your_gift_voucher_label' ,
					'type'    => 'text' ,
				) ,
				array(
					'name'    => __( 'Redeem your Gift Voucher Field Placeholder' , 'rewardsystem' ) ,
					'id'      => 'rs_redeem_your_gift_voucher_placeholder' ,
					'std'     => 'Please enter your Reward Code' ,
					'default' => 'Please enter your Reward Code' ,
					'newids'  => 'rs_redeem_your_gift_voucher_placeholder' ,
					'type'    => 'text' ,
				) ,
				array(
					'name'    => __( 'Redeem Gift Voucher Button Label' , 'rewardsystem' ) ,
					'id'      => 'rs_redeem_gift_voucher_button_label' ,
					'std'     => 'Redeem Gift Voucher' ,
					'default' => 'Redeem Gift Voucher' ,
					'newids'  => 'rs_redeem_gift_voucher_button_label' ,
					'type'    => 'text' ,
				) ,
				array(
					'name'    => __( 'Voucher Field Position' , 'rewardsystem' ) ,
					'id'      => 'rs_redeem_voucher_position' ,
					'std'     => '1' ,
					'default' => '1' ,
					'newids'  => 'rs_redeem_voucher_position' ,
					'type'    => 'select' ,
					'options' => array(
						'1' => __( 'Before My Account' , 'rewardsystem' ) ,
						'2' => __( 'After My Account' , 'rewardsystem' ) ,
					) ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_my_account_voucher_table_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Gift Voucher List' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_table'
				) ,
				array(
					'type' => 'rs_offline_online_rewards_display_table_settings' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_table' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Email Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_voucher_email_settings'
				) ,
				array(
					'name'    => __( 'Enable To Send Mail For Gift Voucher Reward Points' , 'rewardsystem' ) ,
					'desc'    => __( 'Enabling this option will send Gift Voucher Points through Mail' , 'rewardsystem' ) ,
					'id'      => 'rs_send_mail_gift_voucher' ,
					'type'    => 'checkbox' ,
					'std'     => 'no' ,
					'default' => 'no' ,
					'newids'  => 'rs_send_mail_gift_voucher' ,
				) ,
				array(
					'name'    => __( 'Email Subject For Gift Voucher Points' , 'rewardsystem' ) ,
					'id'      => 'rs_email_subject_gift_voucher' ,
					'std'     => 'Gift Voucher - Notification' ,
					'default' => 'Gift Voucher - Notification' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_email_subject_gift_voucher' ,
				) ,
				array(
					'name'    => __( 'Email Message For Gift Voucher Points' , 'rewardsystem' ) ,
					'id'      => 'rs_email_message_gift_voucher' ,
					'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
					'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account' ,
					'type'    => 'textarea' ,
					'newids'  => 'rs_email_message_gift_voucher' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_voucher_email_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Gift Voucher Message Settings' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_gift_voucher_message_settings' ,
				) ,
				array(
					'name'     => __( 'Error Message displayed when a Gift Voucher field is left empty' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Message which will be displayed when Redeem Voucher Button is clicked without entering the voucher code ' , 'rewardsystem' ) ,
					'id'       => 'rs_voucher_redeem_empty_error' ,
					'std'      => 'Please Enter your Voucher Code' ,
					'default'  => 'Please Enter your Voucher Code' ,
					'type'     => 'text' ,
					'newids'   => 'rs_voucher_redeem_empty_error' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Success Message displayed when a Gift Voucher is Redeemed' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Message which will be displayed when the Gift Voucher has been Successfully Redeemed' , 'rewardsystem' ) ,
					'id'       => 'rs_voucher_redeem_success_message' ,
					'std'      => '[giftvoucherpoints] Reward points has been added to your Account' ,
					'default'  => '[giftvoucherpoints] Reward points has been added to your Account' ,
					'type'     => 'text' ,
					'newids'   => 'rs_voucher_redeem_success_message' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Error Message displayed when a User tries to Redeem Expired Voucher Code' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Message which will be displayed when the Gift Voucher has been Successfully Redeemed' , 'rewardsystem' ) ,
					'id'       => 'rs_voucher_code_expired_error_message' ,
					'std'      => 'Voucher has been Expired' ,
					'default'  => 'Voucher has been Expired' ,
					'type'     => 'text' ,
					'newids'   => 'rs_voucher_code_expired_error_message' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Error Message displayed when a User tries to Redeem an Invalid Voucher Code' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Message which will be displayed when a Invalid Voucher is used for Redeeming' , 'rewardsystem' ) ,
					'id'       => 'rs_invalid_voucher_code_error_message' ,
					'std'      => 'Sorry, Voucher not found in list' ,
					'default'  => 'Sorry, Voucher not found in list' ,
					'type'     => 'text' ,
					'newids'   => 'rs_invalid_voucher_code_error_message' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Login Link Label for Guests' , 'rewardsystem' ) ,
					'desc'     => __( 'Please Enter Login link for Guest Label' , 'rewardsystem' ) ,
					'id'       => 'rs_redeem_voucher_login_link_label' ,
					'css'      => 'min-width:200px;' ,
					'std'      => 'Login' ,
					'default'  => 'Login' ,
					'type'     => 'text' ,
					'newids'   => 'rs_redeem_voucher_login_link_label' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Message displayed for Guests' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Message which will be displayed for Guest when Gift Voucher Shortcode is used' , 'rewardsystem' ) ,
					'id'       => 'rs_voucher_redeem_guest_error_message' ,
					'std'      => 'Please [rs_login_link] to View this Page' ,
					'default'  => 'Please [rs_login_link] to View this Page' ,
					'type'     => 'text' ,
					'newids'   => 'rs_voucher_redeem_guest_error_message' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Error Message displayed when a User tries to Redeem a used Voucher Code' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Message that will be displayed when User tries to Redeem a Voucher code that has already been Used' , 'rewardsystem' ) ,
					'id'       => 'rs_voucher_code_used_error_message' ,
					'css'      => 'min-width:200px;' ,
					'std'      => 'Voucher has been used' ,
					'default'  => 'Voucher has been used' ,
					'type'     => 'text' ,
					'newids'   => 'rs_voucher_code_used_error_message' ,
					'desc_tip' => true ,
				) ,
				array(
					'name'     => __( 'Message displayed for Banned Users' , 'rewardsystem' ) ,
					'desc'     => __( 'Enter the Message that will be displayed when a Banned User tries to Redeem the Gift Voucher' , 'rewardsystem' ) ,
					'id'       => 'rs_banned_user_redeem_voucher_error' ,
					'std'      => 'You have Earned 0 Points' ,
					'default'  => 'You have Earned 0 Points' ,
					'type'     => 'textarea' ,
					'newids'   => 'rs_banned_user_redeem_voucher_error' ,
					'desc_tip' => true ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_gift_voucher_message_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Voucher Code Form Customization' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_offline_to_online_form_customize_settings'
				) ,
				array(
					'name'    => __( 'Voucher Code field Label' , 'rewardsystem' ) ,
					'id'      => 'rs_reward_code_field_caption' ,
					'std'     => 'Enter your Voucher Code below to Claim' ,
					'default' => 'Enter your Voucher Code below to Claim' ,
					'type'    => 'text' ,
					'newids'  => 'rs_reward_code_field_caption' ,
				) ,
				array(
					'name'    => __( 'Placeholder for Voucher Code Field' , 'rewardsystem' ) ,
					'id'      => 'rs_reward_code_field_placeholder' ,
					'std'     => 'Voucher Code' ,
					'default' => 'Voucher Code' ,
					'type'    => 'text' ,
					'newids'  => 'rs_reward_code_field_placeholder' ,
				) ,
				array(
					'name'   => __( 'Submit Button Field Caption' , 'rewardsystem' ) ,
					'id'     => 'rs_reward_code_submit_field_caption' ,
					'std'    => 'Submit' ,
					'type'   => 'text' ,
					'newids' => 'rs_reward_code_submit_field_caption' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_offline_to_online_form_customize_settings' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Current Balance Message Customization' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_current_balance_shortcode_customization'
				) ,
				array(
					'name'    => __( 'Current Available Points Label' , 'rewardsystem' ) ,
					'id'      => 'rs_current_available_balance_caption' ,
					'std'     => 'Current Balance:' ,
					'default' => 'Current Balance:' ,
					'type'    => 'text' ,
					'newids'  => 'rs_current_available_balance_caption' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_current_balance_shortcode_customization' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Extra Class Name for Button' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_myaccount_custom_class_name' ,
				) ,
				array(
					'name'     => __( 'Extra Class Name for Redeem Gift Voucher Button' , 'rewardsystem' ) ,
					'desc'     => __( 'Add Extra Class Name to the My Account Redeem Gift Voucher Button, Don\'t Enter dot(.) before Class Name' , 'rewardsystem' ) ,
					'id'       => 'rs_extra_class_name_redeem_gift_voucher_button' ,
					'std'      => '' ,
					'default'  => '' ,
					'type'     => 'text' ,
					'newids'   => 'rs_extra_class_name_redeem_gift_voucher_button' ,
					'desc_tip' => true ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_myaccount_custom_class_name' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
				array(
					'type' => 'rs_wrapper_start' ,
				) ,
				array(
					'name' => __( 'Shortcode used in Gift Vocuher' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => 'rs_shortcode_for_gift_voucher'
				) ,
				array(
					'type' => 'title' ,
					'desc' => __('<b>[giftvoucherpoints]</b> - To display points earned for using voucher code<br><br>'
					. '<b>[rs_login_link]</b> - To display login link for guests' , 'rewardsystem'),
				) ,
				array( 'type' => 'sectionend' , 'id' => 'rs_shortcode_for_gift_voucher' ) ,
				array(
					'type' => 'rs_wrapper_end' ,
				) ,
					) ) ;
		}

		public static function reward_system_register_admin_settings() {

			woocommerce_admin_fields( self::reward_system_admin_fields() ) ;
		}

		public static function reward_system_update_settings() {
			woocommerce_update_options( self::reward_system_admin_fields() ) ;
			if ( isset( $_REQUEST[ 'rs_gift_voucher_module_checkbox' ] ) ) {
				update_option( 'rs_gift_voucher_activated' , wc_clean(wp_unslash($_REQUEST[ 'rs_gift_voucher_module_checkbox' ] ))) ;
			} else {
				update_option( 'rs_gift_voucher_activated' , 'no' ) ;
			}
			if ( isset( $_REQUEST[ 'rs_reward_code_type' ] ) ) {
				update_option( 'rs_reward_code_type' , wc_clean(wp_unslash($_REQUEST[ 'rs_reward_code_type' ] ))) ;
			} else {
				update_option( 'rs_reward_code_type' , '' ) ;
			}

			if ( isset( $_REQUEST[ 'rs_enable_voucher_code' ] ) ) {
				update_option( 'rs_enable_voucher_code' , wc_clean(wp_unslash($_REQUEST[ 'rs_enable_voucher_code' ] ) ));
			} else {
				update_option( 'rs_enable_voucher_code' , '' ) ;
			}
		}

		public static function set_default_value() {
			foreach ( self::reward_system_admin_fields() as $setting ) {
				if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
					add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
				}
			}
		}

		public static function enable_module() {
			RSModulesTab::checkbox_for_module( get_option( 'rs_gift_voucher_activated' ) , 'rs_gift_voucher_module_checkbox' , 'rs_gift_voucher_activated' ) ;
		}

		public static function settings_for_voucher_code_generation() {
			?>
			<table class="form-table">
				<tr valign="top">
					<th class="titledesc" scope="row">
						<label><?php esc_html_e( 'Prefix/Suffix' , 'rewardsystem' ) ; ?></label>
					</th>
					<td class="forminp forminp-select">
						<input type="checkbox" id="rs_enable_prefix" name="rs_enable_prefix_offline_online_rewards" class="rs_enable_prefix_offline_online_rewards"><span><?php esc_html_e( 'Prefix' , 'rewardsystem' ) ; ?></span>
						<input type="text" name="rs_voucher_prefix_offline_online" class="rs_voucher_prefix_offline_online" />
						<span class="rs_prefix_error"></span><br><br>
						<input type="checkbox" name="rs_enable_suffix_offline_online_rewards" class="rs_enable_suffix_offline_online_rewards"><span><?php esc_html_e( 'Suffix' , 'rewardsystem' ) ; ?></span>
						<input type="text" name="rs_voucher_suffix_offline_online" class="rs_voucher_suffix_offline_online" />
						<span class="rs_suffix_error"></span>
					</td>
				</tr>
				<tr valign="top">
					<th class="titledesc" scope="row">
						<label><?php esc_html_e( 'Voucher Code Type' , 'rewardsystem' ) ; ?></label><br/><br/>
					</th>
					<td class="forminp forminp-select">
						<select name="rs_reward_code_type" id="rs_reward_code_type">
							<option value="numeric"><?php esc_html_e( 'Numeric' , 'rewardsystem' ) ; ?></option>
							<option value="alphanumeric"><?php esc_html_e( 'Alphanumeric' , 'rewardsystem' ) ; ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th class="titledesc" scope="row">
						<label class="rs_exclude_characters_code_generation_label"><?php esc_html_e( 'Excluded Alphabets from Voucher Code creation' , 'rewardsystem' ) ; ?></label>                    
					</th>
					<td class="forminp forminp-select">
						<input type="text" id="rs_alphabets_from_voucher_code_creation" name="rs_exclude_characters_code_generation" class="rs_exclude_characters_code_generation" />
						<label class="exclude_caption"><?php esc_html_e( 'Alphabets are comma separated(For eg: i,l,o)' , 'rewardsystem' ) ; ?></label>
					</td>
				</tr>            
				<tr valign="top">
					<th class="titledesc" scope="row">
						<label><?php esc_html_e( 'Voucher Code Length' , 'rewardsystem' ) ; ?></label>                    
					</th>
					<td class="forminp forminp-select">
						<input type="number" id="rs_voucher_code_length" step="1" min="0" name="rs_voucher_code_length_offline_online" class="rs_voucher_code_length_offline_online" />
						<span class="rs_character_error"></span>
					</td>
				</tr>            
				<tr valign="top">
					<th class="titledesc" scope="row">
						<label><?php esc_html_e( 'Reward Points per Voucher Code' , 'rewardsystem' ) ; ?></label>                    
					</th>
					<td class="forminp forminp-select">
						<input type="number" id="rs_per_voucher_code" step="any" min="0" name="rs_voucher_code_points_value_offline_online" class="rs_voucher_code_points_value_offline_online" />
						<span class="rs_points_error"></span>
					</td>
				</tr>            
				<tr valign="top">
					<th class="titledesc" scope="row">
						<label><?php esc_html_e( 'Number of Voucher Codes to generate' , 'rewardsystem' ) ; ?></label>                    
					</th>
					<td class="forminp forminp-select">
						<input type="number" id="rs_voucher_code_to_generate"step="any" min="0" name="rs_voucher_code_count_offline_online" class="rs_voucher_code_count_offline_online" />
						<span class="rs_noofcode_error"></span>
					</td>
				</tr>
				<tr valign="top">
					<th class="titledesc" scope="row">
						<label><?php esc_html_e( 'Voucher Code Used by' , 'rewardsystem' ) ; ?></label><br/><br/>
					</th>
					<td class="forminp forminp-select">
						<select name="rs_voucher_code_user_for" id="rs_voucher_code_user_for">
							<option value="1"><?php esc_html_e( 'Single User' , 'rewardsystem' ) ; ?></option>
							<option value="2"><?php esc_html_e( 'Multiple Users' , 'rewardsystem' ) ; ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th class="titledesc" scope="row">
						<label><?php esc_html_e( 'Voucher Code Usage Limit' , 'rewardsystem' ) ; ?></label><br/><br/>
					</th>
					<td class="forminp forminp-select">
						<select name="rs_voucher_code_usage_limit" id="rs_voucher_code_usage_limit">
							<option value="1"><?php esc_html_e( 'Limited' , 'rewardsystem' ) ; ?></option>
							<option value="2"><?php esc_html_e( 'Unlimited' , 'rewardsystem' ) ; ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th class="titledesc" scope="row">
						<label><?php esc_html_e( 'Number of Users to restrict the usage of Voucher Code' , 'rewardsystem' ) ; ?></label>                    
					</th>
					<td class="forminp forminp-select">
						<input type="number" step="1" min="0" name="rs_voucher_code_usage_limit_value" id="rs_voucher_code_usage_limit_value" class="rs_voucher_code_usage_limit_value" />
					</td>
				</tr>
				<tr valign="top">
					<th class="titledesc" scope="row">
						<label><?php esc_html_e( 'Expiry Date of Voucher Code(s)' , 'rewardsystem' ) ; ?></label>                    
					</th>
					<td class="forminp forminp-select">
						<input type="text" class="rs_gift_voucher_expiry" value="" name="rs_gift_voucher_expiry" id="rs_gift_voucher_expiry" />
						<span class="rs_expdate_error"></span>
					</td>
				</tr>
				<tr valign="top">
					<td class="forminp forminp-select">
						<div id="dialog1" hidden="hidden" ></div>
						<button id="rs_create_Voucher_Codes"class="button-primary rs_create_voucher_codes_offline_online rs_voucher_codes_btn" ><?php esc_html_e( 'Create Voucher Codes' , 'rewardsystem' ) ; ?></button>            
					</td>
				</tr>
			</table>
			<?php
		}

		public static function table_to_display_created_voucher_codes() {
			?>
			<table class="form-table">
				<tr valign="top">
					<th class="titledesc" scope="row">
						<label for="rs_import_gift_voucher_csv"><?php esc_html_e( 'Export Voucher Codes as CSV' , 'rewardsystem' ) ; ?></label>
					</th>
					<td class="forminp forminp-select">
						<input type="submit" id="rs_export_reward_codes_csv" class="rs_export_button" name="rs_export_reward_codes_csv" value="Export Voucher Codes"/>
					</td>
				</tr>
				<tr valign="top">
					<th class="titledesc" scope="row">
						<label for="rs_import_gift_voucher_csv"><?php esc_html_e( 'Import Gift Voucher to CSV' , 'rewardsystem' ) ; ?></label>
					</th>
					<td class="forminp forminp-select">
						<input type="file" id="rs_import_gift_voucher_csv" name="file" />
					</td>
				</tr>
				<tr valign="top">
					<th class="titledesc" scope="row">
						<label for="rs_voucher_code_import_type"><?php esc_html_e( 'If Voucher Code already exists' , 'rewardsystem' ) ; ?></label>
					</th>
					<td class="forminp forminp-select">                
						<select id ="rs_voucher_code_import_type" class="rs_voucher_code_import_type" name="rs_voucher_code_import_type">
							<option value="1"><?php esc_html_e( 'Ignore Voucher Code' , 'rewardsystem' ) ; ?>  </option>
							<option value="2"><?php esc_html_e( 'Replace Voucher Code' , 'rewardsystem' ) ; ?>  </option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<td class="forminp forminp-select">
						<input type="submit" id="rs_import_reward_codes_csv_from_old" class="rs_export_button" name="rs_import_reward_codes_csv_from_old" value="Import Voucher Codes as CSV "/>
					</td>

				</tr>            
			</table>
			<?php
			$voucher_info_array = array() ;
			if ( isset( $_REQUEST[ 'rs_export_reward_codes_csv' ] ) ) {
				global $wpdb ;
				$voucher_datas = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rsgiftvoucher" , ARRAY_A ) ;
				foreach ( $voucher_datas as $voucher_data ) {
					if ( '' != $voucher_data[ 'memberused' ] ) {
						$memberused = unserialize( $voucher_data[ 'memberused' ] ) ;
						$usernames  = array() ;
						foreach ( $memberused as $userid ) {
							$usernames[] = get_userdata( $userid )->user_login ;
						}
						$voucher_used_by = implode( ',' , $usernames ) ;
					} else {
						$voucher_used_by = '' ;
					}
					$voucher_expired_date = ( '' !=  $voucher_data[ 'voucherexpiry' ] ) ? $voucher_data[ 'voucherexpiry' ] : 'Never' ;
					$voucher_info[]       = array(
						$voucher_data[ 'vouchercode' ] ,
						$voucher_data[ 'points' ] ,
						$voucher_data[ 'vouchercreated' ] ,
						$voucher_expired_date ,
						$voucher_used_by
							) ;
				}
				ob_end_clean() ;
				header( 'Content-type: text/csv' ) ;
				header( 'Content-Disposition: attachment; filename=srp_voucher_codes' . gmdate( 'Y-m-d' ) . '.csv' ) ;
				header( 'Pragma: no-cache' ) ;
				header( 'Expires: 0' ) ;
				self::outputCSV( $voucher_info ) ;
				exit() ;
			}
			if ( isset( $_REQUEST[ 'rs_import_reward_codes_csv_from_old' ] ) && isset( $_REQUEST[ 'rs_voucher_code_import_type' ] ) ) {
				if ( isset($_FILES[ 'file' ][ 'error' ]) && wc_clean(wp_unslash($_FILES[ 'file' ][ 'error' ])) > 0 ) {
					echo wp_kses_post('Error: ' . wc_clean(wp_unslash($_FILES[ 'file' ][ 'error' ])) . '<br>' );
				} else {
					$mimes = array( 'text/csv' ,
						'text/plain' ,
						'application/csv' ,
						'text/comma-separated-values' ,
						'application/excel' ,
						'application/vnd.ms-excel' ,
						'application/vnd.msexcel' ,
						'text/anytext' ,
						'application/octet-stream' ,
						'application/txt' ) ;
					if ( isset($_FILES[ 'file' ][ 'type' ], $_FILES[ 'file' ][ 'tmp_name' ]) && in_array( wc_clean(wp_unslash($_FILES[ 'file' ][ 'type' ])) , $mimes ) ) {
						self::inputCSV( wc_clean(wp_unslash($_FILES[ 'file' ][ 'tmp_name' ])) , wc_clean(wp_unslash($_REQUEST[ 'rs_voucher_code_import_type' ] ))) ;
					} else {
											$contents = 'div.error {
								display:block;
							}';
																						
						wp_register_style( 'fp-srp-giftvoucher-style' , false , array() , SRP_VERSION ) ; // phpcs:ignore
						wp_enqueue_style( 'fp-srp-giftvoucher-style' ) ;
						wp_add_inline_style( 'fp-srp-giftvoucher-style' , $contents ) ;                                            
					}
				}
			}
			if ( isset( $_GET[ 'vouchercode' ] ) ) {
				$newwp_list_table_for_users = new SRP_View_Gift_Voucher() ;
				$newwp_list_table_for_users->prepare_items() ;
				$newwp_list_table_for_users->search_box( 'Search' , 'search_id' ) ;
				$newwp_list_table_for_users->display() ;
			} else {
				$newwp_list_table_for_users = new SRP_NewGiftVoucher_List_Table() ;
				$newwp_list_table_for_users->prepare_items() ;
				$newwp_list_table_for_users->search_box( 'Search' , 'search_id' ) ;
				$newwp_list_table_for_users->display() ;
			}
		}

		public static function inputCSV( $data_path, $importtype ) {
			global $wpdb ;
			$row    = 1 ;
						$handle = fopen( $data_path , 'r' );
			if ( false !== ( $handle ) ) {
				while ( false !== ( $data = fgetcsv( $handle , 1000 , ',' ) ) ) {
					$row ++ ;
					$date         = isset( $data[ 2 ] ) ? strtotime( $data[ 2 ] ) : 999999999999 ;
					$expirydate   = ( 'Never' != $data[ 3 ] && '' != $data[ 3 ] ) ? strtotime( $data[ 3 ] ) : '' ;
					$usedby       = isset( $data[ 4 ] ) ? $data[ 4 ] : '' ;
					$collection[] = array( $data[ 0 ] , $data[ 1 ] , $date , $expirydate , $usedby ) ;
				}
				$voucher_codes = $wpdb->get_col( "SELECT vouchercode FROM {$wpdb->prefix}rsgiftvoucher" ) ;
				foreach ( $collection as $collecteddata ) {
					$VoucherCode = $collecteddata[ 0 ] ;
					if ( empty( $VoucherCode ) ) {
						continue ;
					}

					$expired_date = ( isset( $collecteddata[ 3 ] ) && '' != $collecteddata[ 3 ] ) ? date_i18n( 'Y-m-d' , $collecteddata[ 3 ] ) : '' ;
					if ( 'Notyet' != $collecteddata[ 4 ] && '' != $collecteddata[ 4 ] ) {
						$usernames  = array() ;
						$memberused = explode( ',' , $collecteddata[ 4 ] ) ;
						foreach ( $memberused as $userid ) {
							$usernames[] = get_user_by( 'login' , $userid )->ID ;
						}
						$user = serialize( $usernames ) ;
					} else {
						$user = '' ;
					}
					if ( ! in_array( $VoucherCode , $voucher_codes ) ) {
						$wpdb->insert(
								"{$wpdb->prefix}rsgiftvoucher" , array(
							'points'         => $collecteddata[ 1 ] ,
							'vouchercode'    => $VoucherCode ,
							'vouchercreated' => date_i18n( 'Y-m-d' , $collecteddata[ 2 ] ) ,
							'voucherexpiry'  => $expired_date ,
							'memberused'     => $user )
						) ;
					} else {
						if ( '2' ==  $importtype ) {
														$query = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}rsgiftvoucher WHERE vouchercode = %s", $VoucherCode) , ARRAY_A ) ;
							$wpdb->update(
									"{$wpdb->prefix}rsgiftvoucher" , array(
								'points'         => $collecteddata[ 1 ] ,
								'vouchercreated' => date_i18n( 'Y-m-d' , $collecteddata[ 2 ] ) ,
								'voucherexpiry'  => $expired_date ,
								'memberused'     => $user ) , array( 'id' => $query[ 'id' ] )
							) ;
						}
					}
				}
			}
		}

		public static function outputCSV( $data ) {
			$output = fopen( 'php://output' , 'w' ) ;
			if ( is_array( $data ) && ! empty( $data ) ) {
				foreach ( $data as $row ) {
					if (false !=  $row ) {
						fputcsv( $output , $row ) ; // here you can change delimiter/enclosure
					}
				}
			}
			fclose( $output ) ;
		}

		public static function reset_gift_voucher_tab() {
			$settings = self::reward_system_admin_fields() ;
			RSTabManagement::reset_settings( $settings ) ;
		}

	}

	RSGiftVoucher::init() ;
}

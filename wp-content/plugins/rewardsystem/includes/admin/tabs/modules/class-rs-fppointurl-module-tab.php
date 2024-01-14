<?php
/*
 * Point URL Setting Tsb
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSPointURL' ) ) {

	class RSPointURL {

		public static function init() {

			add_action( 'woocommerce_rs_settings_tabs_fppointurl' , array( __CLASS__, 'register_admin_options' ) ) ;

			add_action( 'rs_default_settings_fppointurl' , array( __CLASS__, 'set_default_value' ) ) ;

			add_action( 'woocommerce_update_options_fprsmodules_fppointurl' , array( __CLASS__, 'update_settings' ) ) ;

			add_action( 'woocommerce_admin_field_rs_enable_disable_point_url_module' , array( __CLASS__, 'enable_module' ) ) ;

			add_action( 'woocommerce_admin_field_generate_point_url' , array( __CLASS__, 'settings_to_generate_point_url' ) ) ;

			add_action( 'fp_action_to_reset_module_settings_fppointurl' , array( __CLASS__, 'reset_points_url_module' ) ) ;

			add_action( 'rs_display_save_button_fppointurl' , array( 'RSTabManagement', 'rs_display_save_button' ) ) ;

			add_action( 'rs_display_reset_button_fppointurl' , array( 'RSTabManagement', 'rs_display_reset_button' ) ) ;
		}

		public static function settings_option() {
						/**
						 * Hook:woocommerce_fppointurl_settings.
						 * 
						 * @since 1.0
						 */
			return apply_filters( 'woocommerce_fppointurl_settings' , array(
				array(
					'type' => 'rs_modulecheck_start',
				),
				array(
					'name' => __( 'Point URL Module' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_activate_point_url_module',
				),
				array(
					'type' => 'rs_enable_disable_point_url_module',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_activate_point_url_module' ),
				array(
					'type' => 'rs_modulecheck_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Point URL Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_pointurl_setting',
				),
				array(
					'name'              => __( 'Name' , 'rewardsystem' ),
					'desc_tip'          => false,
					'id'                => 'rs_label_for_site_url',
					'newids'            => 'rs_label_for_site_url',
					'type'              => 'text',
					'std'               => '',
					'default'           => '',
					'custom_attributes' => array(
						'required' => 'required',
					),
				),
				array(
					'name'     => __( 'Site URL' , 'rewardsystem' ),
					'desc'     => __( '(If it is empty,then we consider Site URL as Base URL)' , 'rewardsystem' ),
					'desc_tip' => true,
					'id'       => 'rs_site_url',
					'newids'   => 'rs_site_url',
					'type'     => 'text',
					'std'      => site_url(),
					'default'  => site_url(),
				),
				array(
					'name'              => __( 'Points' , 'rewardsystem' ),
					'id'                => 'rs_point_for_url',
					'newids'            => 'rs_point_for_url',
					'type'              => 'text',
					'std'               => '',
					'default'           => '',
					'custom_attributes' => array(
						'required' => 'required',
					),
				),
				array(
					'name'    => __( 'Validity' , 'rewardsystem' ),
					'id'      => 'rs_time_limit_for_pointurl',
					'newids'  => 'rs_time_limit_for_pointurl',
					'type'    => 'select',
					'std'     => '1',
					'default' => '1',
					'options' => array(
						'1' => __( 'Unlimited' , 'rewardsystem' ),
						'2' => __( 'Limited' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Expiry Time' , 'rewardsystem' ),
					'id'      => 'rs_expiry_time_for_pointurl',
					'newids'  => 'rs_expiry_time_for_pointurl',
					'type'    => 'text',
					'std'     => '',
					'default' => '',
				),
				array(
					'name'    => __( 'Usage Count' , 'rewardsystem' ),
					'id'      => 'rs_count_limit_for_pointurl',
					'newids'  => 'rs_count_limit_for_pointurl',
					'type'    => 'select',
					'std'     => '1',
					'default' => '1',
					'options' => array(
						'1' => __( 'Unlimited' , 'rewardsystem' ),
						'2' => __( 'Limited' , 'rewardsystem' ),
					),
				),
				array(
					'name'    => __( 'Count' , 'rewardsystem' ),
					'id'      => 'rs_count_for_pointurl',
					'newids'  => 'rs_count_for_pointurl',
					'type'    => 'text',
					'std'     => '',
					'default' => '',
				),
				array(
					'type' => 'generate_point_url',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_pointurl_setting' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Email Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_point_url_email_settings',
				),
				array(
					'name'    => __( 'Enable To Send Mail For Point URL Reward Points' , 'rewardsystem' ),
					'desc'    => __( 'Enabling this option will send Point URL Points through Mail' , 'rewardsystem' ),
					'id'      => 'rs_send_mail_point_url',
					'type'    => 'checkbox',
					'std'     => 'no',
					'default' => 'no',
					'newids'  => 'rs_send_mail_point_url',
				),
				array(
					'name'    => __( 'Email Subject For Point URL Points' , 'rewardsystem' ),
					'id'      => 'rs_email_subject_point_url',
					'std'     => 'Point URL - Notification',
					'default' => 'Point URL - Notification',
					'type'    => 'textarea',
					'newids'  => 'rs_email_subject_point_url',
				),
				array(
					'name'    => __( 'Email Message For Point URL Points' , 'rewardsystem' ),
					'id'      => 'rs_email_message_point_url',
					'std'     => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account',
					'default' => 'You have earned [rs_earned_points] points and currently you have [rs_available_points] in your account',
					'type'    => 'textarea',
					'newids'  => 'rs_email_message_point_url',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_point_url_email_settings' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Success Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_pointurl_message_setting',
				),
				array(
					'name'    => __( 'Success Message to display when Points associated URL is accessed' , 'rewardsystem' ),
					'id'      => 'rs_success_message_for_pointurl',
					'newids'  => 'rs_success_message_for_pointurl',
					'type'    => 'text',
					'std'     => '[points] Points added for [offer_name]',
					'default' => '[points] Points added for [offer_name]',
				),
				array(
					'name'    => __( 'Log to be displayed in My Account and Master Log' , 'rewardsystem' ),
					'id'      => 'rs_message_for_pointurl',
					'newids'  => 'rs_message_for_pointurl',
					'type'    => 'text',
					'std'     => '[points] Points added, from Visited Point URL',
					'default' => '[points] Points added, from Visited Point URL',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_pointurl_message_setting' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Error Message Settings' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_sk_message_setting1',
				),
				array(
					'name'    => __( 'Error Message displayed when the Points associated URL was already accessed' , 'rewardsystem' ),
					'id'      => 'failure_msg_for_accessed_url',
					'newids'  => 'failure_msg_for_accessed_url',
					'type'    => 'textarea',
					'std'     => 'You cannot get Points for this link because you have already claimed',
					'default' => 'You cannot get coupon for this link because you have already claimed',
				),
				array(
					'name'    => __( 'Error Message displayed when Points associated URL is accessed after Expiry' , 'rewardsystem' ),
					'id'      => 'failure_msg_for_expired_url',
					'newids'  => 'failure_msg_for_expired_url',
					'type'    => 'text',
					'std'     => '[offer_name] has been Expired',
					'default' => '[offer_name] has been Expired',
				),
				array(
					'name'    => __( 'Error Message displayed when Usage Count has been exceeded' , 'rewardsystem' ),
					'id'      => 'failure_msg_for_count_exceed',
					'newids'  => 'failure_msg_for_count_exceed',
					'type'    => 'text',
					'std'     => 'Usage of Link Limitation reached',
					'default' => 'Usage of Link Limitation reached',
				),
				array( 'type' => 'sectionend', 'id' => '_sk_message_setting1' ),
				array(
					'type' => 'rs_wrapper_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Shortcode used in Point URL' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => 'rs_shortcode_for_points_url',
				),
				array(
					'type' => 'title',
					'desc' => __('<b>[points]</b> - To display points earned for using url<br><br>'
					. '<b>[offer_name]</b> - To display url has been expired' , 'rewardsystem'),
				),
								array(
					'type' => 'title',
					'desc' => __('<b>Note:</b> <br/>We recommend don’t use the above shortcodes anywhere on your site. It will give the value only on the place where we have predefined.<br/> Please check by using the shortcodes available in the <b>Shortcodes </b> tab which will give the value globally.', 'rewardsystem'),
					'id'   => 'rs_shortcode_note_points_url',
				),
				array( 'type' => 'sectionend', 'id' => 'rs_shortcode_for_points_url' ),
				array(
					'type' => 'rs_wrapper_end',
				),
					) ) ;
		}

		public static function register_admin_options() {
			woocommerce_admin_fields( self::settings_option() ) ;
		}

		public static function update_settings() {
			woocommerce_update_options( self::settings_option() ) ;
			if ( isset( $_REQUEST[ 'rs_point_url_module_checkbox' ] ) ) {
				update_option( 'rs_point_url_activated' , wc_clean(wp_unslash($_REQUEST[ 'rs_point_url_module_checkbox' ] ))) ;
			} else {
				update_option( 'rs_point_url_activated' , 'no' ) ;
			}
		}

		public static function set_default_value() {
			foreach ( self::settings_option() as $setting ) {
				if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
					add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
				}
			}
		}

		public static function enable_module() {
			RSModulesTab::checkbox_for_module( get_option( 'rs_point_url_activated' ) , 'rs_point_url_module_checkbox' , 'rs_point_url_activated' ) ;
		}

		public static function settings_to_generate_point_url() {
			?>
			<tr>
				<td></td>
				<td>
					<input type="submit" id="rs_button_for_point_url" class="rs_export_button" value="<?php esc_html_e( 'Generate Point URL' , 'rewardsystem' ) ; ?>"/>
				</td>
			</tr>
			<table>        
				<tr valign="top">
					<td>
						<p>
							<label><?php esc_html_e( 'Search:' , 'rewardsystem' ) ; ?></label>
							<input id="filterings_pointurl" type="text"/>
							<label><?php esc_html_e( 'Page Size:' , 'rewardsystem' ) ; ?></label>
							<select id="changepagesizers_for_url">
								<option value="5"><?php echo esc_html( '5'); ?></option>
								<option value="10"><?php echo esc_html( '10'); ?></option>
								<option value="50"><?php echo esc_html( '50'); ?></option>
								<option value="100"><?php echo esc_html( '100'); ?></option>
							</select>
						</p>
					</td>
				</tr>
			</table>    
			<table id="rs_table_for_point_url" class="wp-list-table widefat fixed posts  rs_table_for_point_url" data-filter = "#filterings_pointurl" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next">
				<thead>
					<tr>
						<th><?php esc_html_e( 'S.No' , 'rewardsystem' ) ; ?></th>
						<th><?php esc_html_e( 'Name for Point URL' , 'rewardsystem' ) ; ?></th>
						<th><?php esc_html_e( 'URL' , 'rewardsystem' ) ; ?></th>                    
						<th><?php esc_html_e( 'Point(s)' , 'rewardsystem' ) ; ?></th>
						<th><?php esc_html_e( 'Date' , 'rewardsystem' ) ; ?></th>
						<th><?php esc_html_e( 'Time Limit' , 'rewardsystem' ) ; ?></th>
						<th><?php esc_html_e( 'Count Limit' , 'rewardsystem' ) ; ?></th>
						<th><?php esc_html_e( 'Current Usage Count' , 'rewardsystem' ) ; ?></th>                    
						<th><?php esc_html_e( 'Delete' , 'rewardsystem' ) ; ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i            = 1 ;
					$PointURLData = get_option( 'points_for_url_click' ) ;
					if ( srp_check_is_array( $PointURLData ) ) {
						foreach ( $PointURLData as $key => $value ) {
							$add_query = add_query_arg( 'rsid' , $key , $value[ 'url' ] ) ;
							?>
							<tr>
								<td><?php echo esc_html($i) ; ?></td>
								<td><?php echo esc_html($value[ 'name' ]) ; ?></td>
								<td><?php echo esc_url($add_query) ; ?></td>
								<td><?php echo esc_html($value[ 'points' ]) ; ?></td>
								<td><?php echo esc_html($value[ 'date' ]) ; ?></td>
								<td><?php echo esc_html('1' == $value[ 'time_limit' ] ? __( 'Unlimited' , 'rewardsystem' ) : __( 'Limited' , 'rewardsystem' ) ); ?></td>                            
								<td><?php echo esc_html('1' == $value[ 'count_limit' ] ? __( 'Unlimited' , 'rewardsystem' ) : __( 'Limited' , 'rewardsystem' ) ); ?></td>
								<td><?php echo esc_html($value[ 'current_usage_count' ] ); ?></td>
								<td><div data-uniqid="<?php echo esc_attr($key) ; ?>" class="rs_remove_point_url">x</div></td>
							</tr>    
							<?php
							$i++ ;
						}
					}
					?>
				</tbody>
			</table>
			<div class="rs_pagination">
				<div class="pagination pagination-centered"></div>
			</div>
			<?php
		}

		public static function reset_points_url_module() {
			$settings = self::settings_option() ;
			RSTabManagement::reset_settings( $settings ) ;
		}
	}

	RSPointURL::init() ;
}

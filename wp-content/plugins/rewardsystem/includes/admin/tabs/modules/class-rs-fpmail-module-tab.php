<?php
/*
 * Support Tab Setting
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSEmailModule' ) ) {

	class RSEmailModule {

		public static function init() {

			add_action( 'rs_default_settings_fpmail' , array( __CLASS__, 'set_default_value' ) ) ;

			add_action( 'woocommerce_admin_field_rs_enable_disable_email_module' , array( __CLASS__, 'enable_module' ) ) ;

			add_action( 'woocommerce_rs_settings_tabs_fpmail' , array( __CLASS__, 'register_settings' ) ) ;

			add_action( 'woocommerce_update_options_fprsmodules_fpmail' , array( __CLASS__, 'update_settings' ) ) ;

			

			add_action( 'woocommerce_admin_field_email_templates_table' , array( __CLASS__, 'email_templates_table' ) ) ;

			add_action( 'fp_action_to_reset_module_settings_fpmail' , array( __CLASS__, 'reset_email_module' ) ) ;

			add_action( 'rs_display_save_button_fpmail' , array( 'RSTabManagement', 'rs_display_save_button' ) ) ;

			add_action( 'rs_display_reset_button_fpmail' , array( 'RSTabManagement', 'rs_display_reset_button' ) ) ;
		}

		public static function settings_fields() {
			$options = ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'wpmandrill/wpmandrill.php' ) ) ? array( '1' => 'mail()', '2' => 'wp_mail()', '3' => 'wpmandrill' ) : array( '1' => 'mail()', '2' => 'wp_mail()' ) ;
			/**
						 * Hook:woocommerce_fpmail.
						 * 
						 * @since 1.0
						 */
						return apply_filters( 'woocommerce_fpmail' , array(
				array(
					'type' => 'rs_modulecheck_start',
						),
						array(
						'name' => __( 'Email Module' , 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_activate_email_module',
						),
						array(
						'type' => 'rs_enable_disable_email_module',
						),
						array( 'type' => 'sectionend', 'id' => '_rs_activate_email_module' ),
						array(
						'type' => 'rs_modulecheck_end',
						),
						array(
						'type' => 'rs_wrapper_start',
						),
						array(
						'name' => __( 'Email Settings' , 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_mail_setting',
						),
						array(
						'name'     => __( 'Select Email Function' , 'rewardsystem' ),
						'id'       => 'rs_select_mail_function',
						'std'      => '2',
						'default'  => '2',
						'newids'   => 'rs_select_mail_function',
						'type'     => 'select',
						'options'  => $options,
						'desc_tip' => true,
						),
						array(
						'name'    => __( 'Enable Minimum Threshold to send Email(Admin)' , 'rewardsystem' ),
						'desc'    => __( 'Enable this option to send email notification to admin' , 'rewardsystem' ),
						'id'      => 'rs_mail_enable_threshold_points',
						'type'    => 'checkbox',
						'std'     => 'no',
						'default' => 'no',
						'newids'  => 'rs_mail_enable_threshold_points',
						),
						array(
						'name'     => __( 'Minimum Threshold to send Email' , 'rewardsystem' ),
						'desc'     => __( 'Please Enter Minimum Threshold points to send Email to Admin when the User Points is less than the threshold' , 'rewardsystem' ),
						'id'       => 'rs_mail_threshold_points',
						'newids'   => 'rs_mail_threshold_points',
						'type'     => 'text',
						'desc_tip' => true,
						'std'      => '',
						'default'  => '',
						),
						array(
						'name'    => __( 'Email Subject' , 'rewardsystem' ),
						'id'      => 'rs_email_subject_threshold_points',
						'std'     => 'Threshold Points - Notification',
						'default' => 'Threshold Points - Notification',
						'type'    => 'textarea',
						'newids'  => 'rs_email_subject_threshold_points',
						),
						array(
						'name'    => __( 'Message Notification' , 'rewardsystem' ),
						'id'      => 'rs_email_message_threshold_points',
						'std'     => 'The User[Username] has reached their Minimum Threshold and Current Points is:[TotalPoint]',
						'default' => 'The User [Username] has reached their Minimum Threshold and Current Points is:[TotalPoint]',
						'type'    => 'textarea',
						'newids'  => 'rs_email_message_threshold_points',
						),
						array( 'type' => 'sectionend', 'id' => '_rs_reward_mail_settings' ),
						array(
						'type' => 'rs_wrapper_end',
						),
						array(
						'type' => 'rs_wrapper_start',
						),
						array(
						'name' => __( 'Email Cron Settings' , 'rewardsystem' ),
						'type' => 'title',
						'id'   => 'rs_cron_settings',
						),
						array(
						'name'     => __( 'Email Cron Time Type' , 'rewardsystem' ),
						'id'       => 'rs_mail_cron_type',
						'type'     => 'select',
						'newids'   => 'rs_mail_cron_type',
						'desc_tip' => true,
						'options'  => array( 'minutes' => 'Minutes', 'hours' => 'Hours', 'days' => 'Days' ),
						'std'      => 'days',
						'default'  => 'days',
						),
						array(
						'name'     => __( 'Email Cron Time' , 'rewardsystem' ),
						'desc'     => __( 'Please Enter time after which Email cron job should run' , 'rewardsystem' ),
						'id'       => 'rs_mail_cron_time',
						'newids'   => 'rs_mail_cron_time',
						'type'     => 'text',
						'desc_tip' => true,
						'std'      => '3',
						'default'  => '3',
						),
						array( 'type' => 'sectionend', 'id' => 'rs_cron_settings' ),
						array( 'type' => 'sectionend', 'id' => '_rs_mail_setting' ),
						array(
						'type' => 'rs_wrapper_end',
						),
						array(
						'type' => 'rs_modulecheck_start',
						),
						array(
						'name' => __( 'Email Templates Settings' , 'rewardsystem' ),
						'type' => 'title',
						'id'   => '_rs_email_template_setting',
						),
						array(
						'type' => 'email_templates_table',
						),
						array( 'type' => 'sectionend', 'id' => '_rs_email_template_setting' ),
						array(
						'type' => 'rs_modulecheck_end',
						),
						) ) ;
		}

		public static function register_settings() {
			woocommerce_admin_fields( self::settings_fields() ) ;
		}

		public static function update_settings() {
			//Setting a cron values
			if ( isset($_REQUEST[ 'rs_mail_cron_type' ], $_REQUEST[ 'rs_mail_cron_time' ]) && wc_clean(wp_unslash($_REQUEST[ 'rs_mail_cron_type' ])) != get_option( 'rs_mail_cron_type' ) || wc_clean(wp_unslash($_REQUEST[ 'rs_mail_cron_time' ])) != get_option( 'rs_mail_cron_time' ) ) {
				wp_clear_scheduled_hook( 'rscronjob' ) ;
				if (class_exists('SRP_Cron_Handler')) {
					 SRP_Cron_Handler::maybe_set_wp_schedule_event() ;
				}
			}
			woocommerce_update_options( self::settings_fields() ) ;
			if ( isset( $_REQUEST[ 'rs_email_module_checkbox' ] ) ) {
				update_option( 'rs_email_activated' , wc_clean(wp_unslash($_REQUEST[ 'rs_email_module_checkbox' ] ))) ;
			} else {
				update_option( 'rs_email_activated' , 'no' ) ;
			}
			wp_safe_redirect( esc_url_raw( add_query_arg( 'rs_saved' , '1' , isset($_SERVER[ 'REQUEST_URI' ]) ? wc_clean(wp_unslash($_SERVER[ 'REQUEST_URI' ] )) : '') ) ) ;
			exit() ;
		}

		public static function set_default_value() {
			foreach ( self::settings_fields() as $setting ) {
				if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
					add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
				}
			}
		}

		public static function reset_email_module() {
			$settings = self::settings_fields() ;
			RSTabManagement::reset_settings( $settings , 'rsemailmodule' ) ;
		}

		public static function enable_module() {
			RSModulesTab::checkbox_for_module( get_option( 'rs_email_activated' ) , 'rs_email_module_checkbox' , 'rs_email_activated' ) ;
		}

		public static function email_templates_table() {
			?>
			<p><?php esc_html_e( 'Email Template Settings' , 'rewardsystem' ) ; ?></p>
			<?php
			global $wpdb ;
			if ( ( isset( $_GET[ 'rs_new_email' ] ) ) && ( isset( $_GET[ 'rs_saved' ] ) ) ) {
				$TemplateData = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rs_templates_email WHERE id = %d" , get_option( 'rs_new_template_id' ) ) , OBJECT ) ;
				$TemplateData = $TemplateData[ 0 ] ;
				echo wp_kses_post(self::table_for_template( $TemplateData , true ) );
			} else if ( ( isset( $_GET[ 'rs_new_email' ] ) ) && ( ! isset( $_GET[ 'rs_saved' ] ) ) ) {
				echo wp_kses_post(self::table_for_template( array() , false ) );
			} else if ( isset( $_GET[ 'rs_edit_email' ] ) ) {
								$TemplateData = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rs_templates_email WHERE id=%d", wc_clean(wp_unslash($_GET[ 'rs_edit_email' ])) ) , OBJECT ) ;
				$TemplateData = $TemplateData[ 0 ] ;
				echo wp_kses_post(self::table_for_template( $TemplateData , true ) );
			} else {
								$SavedTemplates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rs_templates_email", OBJECT ) ;
				$NewTemplateURL = add_query_arg( array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmodules', 'section' => 'fpmail', 'rs_new_email' => 'template' ) , SRP_ADMIN_URL ) ;
				?>
				<a href='<?php echo esc_url($NewTemplateURL); ?>'>
					<input type="button" name="rs_new_email_template" id="rs_new_email_template" class="button rs_email_button" value="<?php esc_html_e( 'New Template' , 'rewardsystem' ) ; ?>">
				</a>
				<p>
					<?php esc_html_e( 'Search:' , 'rewardsystem' ) ; ?><input id="rs_email_templates" type="text"/>
					<?php esc_html_e( 'Page Size:' , 'rewardsystem' ) ; ?>
					<select id="changepagesizertemplates">
						<option value="1">1</option>
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="50">50</option>
						<option value="100">100</option>
					</select>
				</p>
				<table class="wp-list-table widefat fixed posts" data-filter = "#rs_email_templates" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next" id="rs_email_templates_table" cellspacing="0">
					<thead>
						<tr>
							<th scope='col' data-toggle="true" class='manage-column column-serial_number'><?php esc_html_e( 'S.No' , 'rewardsystem' ) ; ?></th>
							<th scope='col' id='rs_user_names' class='manage-column column-rs_user_name'><?php esc_html_e( 'Template Name' , 'rewardsystem' ) ; ?></th>
							<th scope='col' id='rs_from_name' class='manage-column column-rs_from_name'><?php esc_html_e( 'From Name' , 'rewardsystem' ) ; ?></th>
							<th scope='col' id='rs_from_email' class='manage-column column-rs_from_email'><?php esc_html_e( 'From Email' , 'rewardsystem' ) ; ?></th>
							<th scope="col" id="rs_subject" class='manage-column column-rs_subject'><?php esc_html_e( 'Email Subject' , 'rewardsystem' ) ; ?></th>
							<th scope='col' id='rs_message' class='manage-column column-rs_message'><?php esc_html_e( 'Email Message' , 'rewardsystem' ) ; ?></th>
							<th scope="col" id="rs_minimum_userpoints" class="manage-column column-rs_minimum_userpoints"><?php esc_html_e( 'Minimum User Points' , 'rewardsystem' ) ; ?></th>
							<th scope="col" id="rs_email_status" class='manage-column column-rs_email_status'><?php esc_html_e( 'Status' , 'rewardsystem' ) ; ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ( srp_check_is_array( $SavedTemplates ) ) {
							$i = 1 ;
							foreach ( $SavedTemplates as $each_template ) {
								$EditTemplateURL = add_query_arg( array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmodules', 'section' => 'fpmail', 'rs_edit_email' => $each_template->id ) , SRP_ADMIN_URL ) ;
								$FromName        = 'local' == $each_template->sender_opt ? $each_template->from_name : get_option( 'woocommerce_email_from_name' ) ;
								$FromEmail       = 'local' == $each_template->sender_opt ? $each_template->from_email : get_option( 'woocommerce_email_from_address' ) ;
								$Message         = strip_tags( $each_template->message ) ;
								$Message         = ( strlen( $Message ) > 80 ) ? substr( $Message , 0 , 80 ) . '...' : $Message ;
								$Status          = $each_template->rs_status ;
								$ButtonText      = ( 'ACTIVE' == $Status ) ? __( 'Deactivate' , 'rewardsystem' ) : __( 'Activate' , 'rewardsystem' ) ;
								?>
								<tr>
									<td>
										<?php echo esc_attr($i) ; ?>&nbsp;&nbsp;
										<span>
											<a href="<?php echo esc_url($EditTemplateURL) ; ?>"><?php esc_html_e( 'Edit' , 'rewardsystem' ) ; ?></a>&nbsp;&nbsp;
										</span>
										<span>
											<a href="" class="rs_delete" data-id="<?php echo esc_attr($each_template->id) ; ?>" ><?php esc_html_e( 'Delete' , 'rewardsystem' ) ; ?></a>
										</span>
									</td>
									<td>
										<?php echo esc_html($each_template->template_name) ; ?>
									</td>
									<td>
										<?php echo esc_html($FromName ); ?>
									</td>
									<td>
										<?php echo esc_html($FromEmail ); ?>
									</td>
									<td>
										<?php echo esc_html($each_template->subject) ; ?>
									</td>
									<td>
										<?php echo esc_html($Message) ; ?>
									</td>
									<td>
										<?php echo esc_html($each_template->minimum_userpoints) ; ?>
									</td>
									<td>
										<a href="#" class="button rs_mail_active" data-rsmailid="<?php echo esc_attr($each_template->id ); ?>" data-currentstate="<?php echo esc_html($Status) ; ?>"><?php echo esc_html($ButtonText) ; ?></a>
									</td>
								</tr>
								<?php
								$i++ ;
							}
						}
						?>
					</tbody>
				</table>
				<div>
					<div class="pagination pagination-centered"></div>
				</div>
				<?php
			}
		}

		public static function table_for_template( $Template, $Bool, $edit = '' ) {
			$EditorId            = empty( $edit ) ? 'rs_email_template_new' : 'rs_email_template_edit' ;
			$Textarea            = array( 'textarea_name' => $EditorId ) ;
			$Content             = $Bool ? $Template->message : __( 'Hi {rsfirstname} {rslastname}, <br><br> You have Earned Reward Points: {rspoints} on {rssitelink}  <br><br> You can use this Reward Points to make discounted purchases on {rssitelink} <br><br> Thanks' , 'rewardsystem' ) ;
			$TemplateName        = $Bool ? $Template->template_name : __( 'Default' , 'rewardsystem' ) ;
			$NonActiveStatus     = $Bool ? selected( $Template->rs_status , 'NOTACTIVE' , false ) : '' ;
			$ActiveStatus        = $Bool ? selected( $Template->rs_status , 'ACTIVE' , false ) : '' ;
			$Woo                 = $Bool ? checked( $Template->sender_opt , 'woo' , false ) : checked( 'woo' , 'woo' , false ) ;
			$Local               = $Bool ? checked( $Template->sender_opt , 'local' , false ) : '' ;
			$FromName            = $Bool ? $Template->from_name : __( 'Admin' , 'rewardsystem' ) ;
			$FromMail            = $Bool ? $Template->from_email : '' ;
			$Subject             = $Bool ? $Template->subject : '' ;
			$UserList            = array( 0 ) ;
			$SendMailforAll      = $Bool ? checked( $Template->sendmail_options , '1' , false ) : checked( '1' , '1' , false ) ;
			$SendMailforSelected = $Bool ? checked( $Template->sendmail_options , '2' , false ) : '' ;
			$OnlyOnce            = $Bool ? checked( $Template->mailsendingoptions , '1' , false ) : '' ;
			$Always              = $Bool ? checked( $Template->mailsendingoptions , '2' , false ) : checked( '2' , '2' , false ) ;
			$MailforEarning      = $Bool ? checked( $Template->rsmailsendingoptions , '1' , false ) : '' ;
			$MailforRedeeming    = $Bool ? checked( $Template->rsmailsendingoptions , '2' , false ) : '' ;
			$MailforCron         = $Bool ? checked( $Template->rsmailsendingoptions , '3' , false ) : checked( '3' , '3' , false ) ;
			$MinEarningPoint     = $Bool ? $Template->earningpoints : '' ;
			$MinRedeemingPoint   = $Bool ? $Template->redeemingpoints : '' ;
			$MinUserPoints       = $Bool ? $Template->minimum_userpoints : '' ;
			$SelectedUser        = $Bool ? unserialize( $Template->sendmail_to ) : '' ;
			$ReturnURL           = add_query_arg( array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmodules', 'section' => 'fpmail' ) , SRP_ADMIN_URL ) ;
			$shortcode_note      = __('<b>Note:</b> <br/>We recommend don’t use the above shortcodes anywhere on your site. It will give the value only on the place where we have predefined.<br/> Please check by using the shortcodes available in the <b>Shortcodes </b> tab which will give the value globally.', 'rewardsystem');
			?>
			<table class="widefat">
								
				<tr><td><span><strong>{rssitelink}</strong> - <?php esc_html_e( 'Use this Shortcode to insert the Cart Link in the mail' , 'rewardsystem' ) ; ?></span></td></tr>
				<tr><td><span><strong>{rsfirstname}</strong> - <?php esc_html_e( 'Use this Shortcode to insert Receiver First Name in the mail' , 'rewardsystem' ) ; ?></span></td></tr>
				<tr><td><span><strong>{rslastname}</strong> - <?php esc_html_e( 'Use this Shortcode to insert Receiver Last Name in the mail' , 'rewardsystem' ) ; ?></span></td></tr>
				<tr><td><span><strong>{rspoints}</strong> - <?php esc_html_e( 'Use this Shortcode to insert User Points in the Mail' , 'rewardsystem' ) ; ?></span></td></tr>
				<tr><td><span><strong>{rs_points_in_currency}</strong> - <?php esc_html_e( 'Use this Shortcode for displaying the Currency Value of Available Reward Points' , 'rewardsystem' ) ; ?></span></td></tr>
				<tr><td><span><strong>{rs_earned_points}</strong> - <?php esc_html_e( 'Use this Shortcode to display Earned Points for the products in the Mail' , 'rewardsystem' ) ; ?></span></td></tr>
				<tr><td><span><strong>{rs_redeemed_points}</strong> -  <?php esc_html_e( 'Use this Shortcode to display Redeemed Points for the products in the Mail' , 'rewardsystem' ) ; ?></span></td></tr>
				<tr><td><span><strong>{site_referral_url}</strong> - <?php esc_html_e( 'Use this Shortcode for displaying the Referral Link' , 'rewardsystem' ) ; ?></span></td></tr> 
				<tr><td><span><strong>{order_id}</strong> - <?php esc_html_e( 'Use this shortcode to display the Order ID in an email when points are earned & redeemed in the order' , 'rewardsystem' ) ; ?></span></td></tr>
				<tr><td><span><?php echo wp_kses_post($shortcode_note); ?><span></td></tr>
				<tr>
					<td><?php esc_html_e( 'Template Name' , 'rewardsystem' ) ; ?>:</td>
					<td>
						<input type="text" name="rs_template_name" value="<?php echo esc_html($TemplateName) ; ?>"id="rs_template_name">
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Template Status' , 'rewardsystem' ) ; ?>:</td>
					<td>
						<select name="rs_template_status" id="rs_template_status"> 
							<option value="NOTACTIVE" <?php echo esc_attr($NonActiveStatus) ; ?>><?php esc_html_e( 'Deactivated' , 'rewardsystem' ) ; ?></option>
							<option value="ACTIVE" <?php echo esc_attr($ActiveStatus) ; ?>><?php esc_html_e( 'Activated' , 'rewardsystem' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Send Email' , 'rewardsystem' ) ; ?>:</td>
					<td>
						<input type="radio" name="mailsendingoptions" id="mailsendingoptions" value="1" <?php echo esc_attr($OnlyOnce) ; ?>/><?php esc_html_e( 'Only Once' , 'rewardsystem' ) ; ?><br>
						<input type="radio" name="mailsendingoptions" id="mailsendingoptions" value="2" <?php echo esc_attr($Always) ; ?>/><?php esc_html_e( 'Always' , 'rewardsystem' ) ; ?><br>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Email Sending is based on' , 'rewardsystem' ) ; ?>:</td>
					<td>
						<input type="radio" name="rsmailsendingoptions" id="rsmailsendingoptions" class="rsmailsendingoptions" value="1" <?php echo esc_attr($MailforEarning) ; ?>><?php esc_html_e( 'Earning Point' , 'rewardsystem' ) ; ?><br>
						<input type="radio" name="rsmailsendingoptions" id="rsmailsendingoptions" class="rsmailsendingoptions" value="2" <?php echo esc_attr($MailforRedeeming) ; ?>><?php esc_html_e( 'Redeeming Point' , 'rewardsystem' ) ; ?><br>
						<input type="radio" name="rsmailsendingoptions" id="rsmailsendingoptions" class="rsmailsendingoptions" value="3" <?php echo esc_attr($MailforCron) ; ?>><?php esc_html_e( 'Cron Job' , 'rewardsystem' ) ; ?><br>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Minimum Points to be earned in an order to send Email' , 'rewardsystem' ) ; ?></td>
					<td>
						<input type="text" name="earningpoints" class="earningpoints" id="earningpoints" value="<?php echo esc_attr($MinEarningPoint) ; ?>"/>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Minimum Points to be redeemed in an order to send Email' , 'rewardsystem' ) ; ?></td>
					<td>
						<input type="text" name="redeemingpoints" class="redeemingpoints" id="redeemingpoints" value="<?php echo esc_attr($MinRedeemingPoint) ; ?>"/>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Email Sender Option' , 'rewardsystem' ) ; ?>: </td>
					<td>
						<input type="radio" name="rs_sender_opt" id="rs_sender_woo" value="woo" <?php echo esc_attr($Woo) ; ?> class="rs_sender_opt"><?php esc_html_e( 'Woocommerce' , 'rewardsystem' ) ; ?>
						<input type="radio" name="rs_sender_opt" id="rs_sender_local" value="local" <?php echo esc_attr($Local) ; ?> class="rs_sender_opt"><?php esc_html_e( 'Local' , 'rewardsystem' ) ; ?>
					</td>
				</tr>
				<tr class="rs_local_senders">
					<td><?php esc_html_e( 'From Name' , 'rewardsystem' ) ; ?>:</td>
					<td>
						<input type="text" name="rs_from_name" id="rs_from_name" value="<?php echo esc_attr($FromName) ; ?>"/>
					</td>
				</tr>
				<tr class="rs_local_senders">
					<td><?php esc_html_e( 'From Email' , 'rewardsystem' ) ; ?>:</td>
					<td>
						<input type="text" name="rs_from_email" id="rs_from_email" value="<?php echo esc_attr($FromMail) ; ?>"/>
					</td>
				</tr>
				<tr class="rs_minimum_userpoints_field">
					<td><?php esc_html_e( 'Minimum Balance Points to send Email' , 'rewardsystem' ) ; ?>:</td>
					<td>
						<input type="text" class="rs_minimum_userpoints" name="rs_minimum_userpoints" id="rs_minimum_userpoints" value="<?php echo esc_attr($MinUserPoints) ; ?>"/>
					</td>
				</tr>
				<tr class="rs_sendmail_options">
					<td><?php esc_html_e( 'Send Email To' , 'rewardsystem' ) ; ?>:</td>
					<td>
						<input type="radio" id = "rs_sendmail_options_all" name="rs_sendmail_options" value="1" <?php echo esc_attr($SendMailforAll) ; ?> class="rs_sendmail_options"><?php esc_html_e( 'All Users' , 'rewardsystem' ) ; ?>
						<input type="radio" name="rs_sendmail_options" id="rs_sendmail_options_selected" value="2" <?php echo esc_attr($SendMailforSelected) ; ?> class="rs_sendmail_options"/><?php esc_html_e( 'Selected Users' , 'rewardsystem' ) ; ?>
					</td>
				</tr>
				<tr valign="top">
					<td class="titledesc" scope="row">
						<label for="rs_multiselect_mail_send"><?php esc_html_e( 'Send Email to Selected User(s)' , 'rewardsystem' ) ; ?></label>
					</td>
					<td>
						<?php if ( WC_VERSION <= ( float ) ( '2.2.0' ) ) { ?>
							<select name="rs_multiselect_mail_send" id="rs_multiselect_mail_send" multiple="multiple" class="short rs_multiselect_mail_send">
								<?php
								if ( ! empty( $SelectedUser ) ) {
									$UserIds = srp_check_is_array( $SelectedUser ) ? $SelectedUser : array_filter( array_map( 'absint' , ( array ) explode( ',' , $SelectedUser ) ) ) ;
									foreach ( $UserIds as $UserId ) {
										$UserInfo = get_user_by( 'id' , $UserId ) ;
										if ( ! is_object( $UserInfo ) ) {
											continue ;
										}
										?>
										<option value="<?php echo esc_attr($UserId) ; ?>" selected="selected"><?php echo esc_html( $UserInfo->display_name ) . ' (#' . absint( $UserInfo->ID ) . ' &ndash; ' . esc_html( $UserInfo->user_email ) ; ?></option>
										<?php
									}
								}
								?>
							</select>
						<?php } elseif ( WC_VERSION >= ( float ) ( '3.0.0' ) ) { ?>
							<select class="wc-customer-search" name="rs_multiselect_mail_send" id="rs_multiselect_mail_send" multiple="multiple" data-placeholder="<?php esc_html_e( 'Search for a customer' , 'rewardsystem' ) ; ?>">
								<?php
								if ( ! empty( $SelectedUser ) ) {
									$UserIds = srp_check_is_array( $SelectedUser ) ? $SelectedUser : array_filter( array_map( 'absint' , ( array ) explode( ',' , $SelectedUser ) ) ) ;
									foreach ( $UserIds as $UserId ) {
										$UserInfo = get_user_by( 'id' , $UserId ) ;
										if ( ! is_object( $UserInfo ) ) {
											continue ;
										}
										?>
										<option value="<?php echo esc_attr($UserId ); ?>" selected="selected"><?php echo esc_html( $UserInfo->display_name ) . ' (#' . absint( $UserInfo->ID ) . ' &ndash; ' . esc_html( $UserInfo->user_email ) ; ?></option>
										<?php
									}
								}
								?>
							</select>
						<?php } else { ?>
							<input type="hidden" class="wc-customer-search" name="rs_multiselect_mail_send" id="rs_multiselect_mail_send" data-multiple="true" data-placeholder="<?php esc_html_e( 'Search for a customer' , 'rewardsystem' ) ; ?>" data-selected="
								<?php
								$JsonIds = array() ;
								if ( ! empty( $SelectedUser ) ) {
									$UserIds = srp_check_is_array( $SelectedUser ) ? $SelectedUser : array_filter( array_map( 'absint' , ( array ) explode( ',' , $SelectedUser ) ) ) ;
									foreach ( $UserIds as $UserId ) {
										$UserInfo = get_user_by( 'id' , $UserId ) ;
										if ( ! is_object( $UserInfo ) ) {
											continue ;
										}

										$JsonIds[ $UserId ] = esc_html( $UserInfo->display_name ) . ' (#' . absint( $UserInfo->ID ) . ' &ndash; ' . esc_html( $UserInfo->user_email ) ;
									}
									echo esc_attr( json_encode( $JsonIds ) ) ;
								}
								?>
							" value="<?php echo esc_attr(implode( ',' , array_keys( $JsonIds ) )) ; ?>" data-allow_clear="true" />
							   <?php } ?>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Email Subject' , 'rewardsystem' ) ; ?>:</td>
					<td>
						<input type="text" name="rs_subject" id="rs_subject" value="<?php echo esc_html($Subject) ; ?>">
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Email Message' , 'rewardsystem' ) ; ?>:</td>
					<td>
						<?php
						wp_editor( $Content , $EditorId , $Textarea ) ;
						?>
					</td>
				</tr>
								<?php if (isset($Template->id)) : ?>
								<tr>
									<td></td>
									<td>
										<a class="button" href="<?php echo esc_url(wp_nonce_url( add_query_arg(array( 'rs_email_template_id' => $Template->id ), admin_url( '?rs_preview_email_template=true' )), 'rs-preview-mail' )); ?>" target="_blank"><?php echo esc_attr('Preview Email', 'rewardsystem'); ?></a>
									</td>
								</tr>
								<?php endif; ?>
				<tr>
					<td>
						<input type="button" name="rs_save_new_template" class="button button-primary button-large" id="rs_save_new_template" value="<?php esc_html_e( 'Save' , 'rewardsystem' ) ; ?>">&nbsp;
						<a href="<?php echo esc_url($ReturnURL) ; ?>"><input type="button" class="button rs_email_button" name="returntolist" value="<?php esc_html_e( 'Return to Mail Templates' , 'rewardsystem' ) ; ?>"></a>&nbsp;
					</td>
				</tr>
			</table>
			<?php
		}
	}

	RSEmailModule::init() ;
}

<?php
/*
 * Email Template Tab
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSEmailExpiredPointsTemplate' ) ) {

	class RSEmailExpiredPointsTemplate {

		public static function init() {

			add_action( 'woocommerce_rs_settings_tabs_fpemailexpiredpoints' , array( __CLASS__ , 'register_settings' ) ) ;

			add_action( 'woocommerce_update_options_fprsmodules_fpemailexpiredpoints' , array( __CLASS__ , 'update_settings' ) ) ;

			add_action( 'woocommerce_admin_field_emailexpiry_templates_table' , array( __CLASS__ , 'emailexpiry_templates_table' ) ) ;

			add_action( 'woocommerce_admin_field_rs_enable_emailexpiry_module' , array( __CLASS__ , 'enable_module' ) ) ;

			add_action( 'rs_display_save_button_fpemailexpiredpoints' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

			add_action( 'rs_display_reset_button_fpemailexpiredpoints' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;

			add_action( 'fp_action_to_reset_settings_fpemailexpiredpoints' , array( __CLASS__ , 'reset_emailexpiry_module' ) ) ;
		}

		public static function enable_module() {
			RSModulesTab::checkbox_for_module( get_option( 'rs_email_template_expire_activated' ) , 'rs_email_template_expire_checkbox' , 'rs_email_template_expire_activated' ) ;
		}

		public static function settings_fields() {
			global $wpdb ;
						$Templates     = $wpdb->get_results( "SELECT template_name FROM {$wpdb->prefix}rs_expiredpoints_email WHERE rs_status='ACTIVE'", ARRAY_A ) ;
			$TemplateNames = array() ;
			if ( srp_check_is_array( $Templates ) ) {
				foreach ( $Templates as $Template ) {
					$TemplateNames[ $Template[ 'template_name' ] ] = $Template[ 'template_name' ] ;
				}
			}
			return apply_filters( 'woocommerce_fpemailexpiredpoints_settings' , array(
				array(
					'type' => 'rs_modulecheck_start' ,
				) ,
				array(
					'name' => __( 'Email Template for Expire' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_activate_email_template_expire_module' ,
				) ,
				array(
					'type' => 'rs_enable_emailexpiry_module' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_activate_email_template_expire_module' ) ,
				array(
					'type' => 'rs_modulecheck_end' ,
				) ,
				array(
					'type' => 'rs_modulecheck_start' ,
				) ,
				array(
					'name' => __( 'Email Templates Settings for Expire' , 'rewardsystem' ) ,
					'type' => 'title' ,
					'id'   => '_rs_email_expired_point_template_setting'
				) ,
				array(
					'name'    => __( 'Select Template ' , 'rewardsystem' ) ,
					'id'      => 'rs_select_template' ,
					'class'   => 'rs_select_template' ,
					'std'     => '' ,
					'default' => '' ,
					'type'    => 'select' ,
					'newids'  => 'rs_select_template' ,
					'options' => $TemplateNames ,
				) ,
				array( 'type' => 'sectionend' , 'id' => 'rs_general_tab_' ) ,
				array(
					'type' => 'emailexpiry_templates_table' ,
				) ,
				array( 'type' => 'sectionend' , 'id' => '_rs_email_expired_point_template_setting' ) ,
				array(
					'type' => 'rs_modulecheck_end' ,
				) ,
					) ) ;
		}

		public static function register_settings() {
			woocommerce_admin_fields( self::settings_fields() ) ;
		}

		public static function update_settings() {
			woocommerce_update_options( self::settings_fields() ) ;
			if ( isset( $_REQUEST[ 'rs_email_template_expire_checkbox' ] ) ) {
				update_option( 'rs_email_template_expire_activated' , wc_clean(wp_unslash($_REQUEST[ 'rs_email_template_expire_checkbox' ] ))) ;
			} else {
				update_option( 'rs_email_template_expire_activated' , 'no' ) ;
			}
			wp_safe_redirect( esc_url_raw( add_query_arg( 'rs_saved' , '1' , isset($_SERVER[ 'REQUEST_URI' ]) ? wc_clean(wp_unslash($_SERVER[ 'REQUEST_URI' ] ) ):'') )) ;
			exit() ;
		}

		public static function emailexpiry_templates_table() {
			global $wpdb ;

			if ( isset( $_GET[ 'rs_new_email_expired' ] ) && ( isset( $_GET[ 'rs_saved' ] ) ) ) {
								$TemplateData = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rs_expiredpoints_email WHERE id = %d", get_option( 'rs_new_template_id_for_expiry' ) ) , OBJECT ) ;
				$Template     = $TemplateData[ 0 ] ;
				echo wp_kses_post(self::table_for_template( $Template , true ) );
			} else if ( isset( $_GET[ 'rs_new_email_expired' ] ) && ( ! isset( $_GET[ 'rs_saved' ] ) ) ) {
				echo wp_kses_post(self::table_for_template( array() , false ) );
			} else if ( isset( $_GET[ 'rs_edit_email_expired' ] ) ) {
								$TemplateData = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rs_expiredpoints_email WHERE id = %d", wc_clean(wp_unslash($_GET[ 'rs_edit_email_expired' ] ))) , OBJECT ) ;
				$Template     = $TemplateData[ 0 ] ;
				echo wp_kses_post(self::table_for_template( $Template , true , 'edit' ) );
			} else {
								$SavedTemplates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rs_expiredpoints_email" , OBJECT ) ;
				$NewTemplateURL = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpemailexpiredpoints' , 'rs_new_email_expired' => 'template' ) , SRP_ADMIN_URL ) ;
				?>
				<a href='<?php echo esc_url($NewTemplateURL); ?>'>
					<input type="button" name="rs_new_email_expired_template" id="rs_new_email_expired_template" class="button rs_email_button" value="<?php esc_html_e('New Template', 'rewardsystem'); ?>">
				</a>
				<p>
					<?php esc_html_e( 'Search:' , 'rewardsystem' ) ; ?><input id="rs_email_expired_templates" type="text"/>
					<?php esc_html_e( 'Page Size:' , 'rewardsystem' ) ; ?>
					<select id="changepagesizertemplates">
						<option value="1">1</option>
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="50">50</option>
						<option value="100">100</option>
					</select>
				</p>
				<table class="wp-list-table widefat fixed posts" data-filter = "#rs_email_templates_expired" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next" id="rs_email_templates_table_expired" cellspacing="0">
					<thead>
						<tr>
							<th scope='col' data-toggle="true" class='manage-column column-serial_number'><?php esc_html_e( 'S.No' , 'rewardsystem' ) ; ?></th>
							<th scope='col' id='rs_user_names' class='manage-column column-rs_user_name'><?php esc_html_e( 'Template Name' , 'rewardsystem' ) ; ?></th>
							<th scope='col' id='rs_expired_from_name' class='manage-column column-rs_expired_from_name'><?php esc_html_e( 'From Name' , 'rewardsystem' ) ; ?></th>
							<th scope='col' id='rs_expired_from_email' class='manage-column column-rs_expired_from_email'><?php esc_html_e( 'From Email' , 'rewardsystem' ) ; ?></th>
							<th scope="col" id='rs_subject_expired' class='manage-column column-rs_subject_expired'><?php esc_html_e( 'Email Subject' , 'rewardsystem' ) ; ?></th>
							<th scope='col' id='rs_message_expired' class='manage-column column-rs_message_expired'><?php esc_html_e( 'Email Message' , 'rewardsystem' ) ; ?></th>
							<th scope='col' id='rs_no_of_days' class='manage-column column-rs_no_of_days'><?php esc_html_e( 'No of days' , 'rewardsystem' ) ; ?></th>
							<th scope="col" id="rs_email_status" class="manage-column column-rs_email_status"><?php esc_html_e( 'Status' , 'rewardsystem' ) ; ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ( srp_check_is_array( $SavedTemplates ) ) {
							$i = 1 ;
							foreach ( $SavedTemplates as $each_template ) {
								$EditTemplateURL = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpemailexpiredpoints' , 'rs_edit_email_expired' => $each_template->id ) , SRP_ADMIN_URL ) ;
								$FromName        = 'local'  == $each_template->sender_opt ? $each_template->from_name : get_option( 'woocommerce_email_from_name' ) ;
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
											<a href="" class="rs_delete_expired" data-id="<?php echo esc_attr($each_template->id) ; ?>" ><?php esc_html_e( 'Delete' , 'rewardsystem' ) ; ?></a>
										</span>
									</td>
									<td>
										<?php echo wp_kses_post($each_template->template_name) ; ?>
									</td>
									<td>
										<?php echo wp_kses_post($FromName) ; ?>
									</td>
									<td>
										<?php echo wp_kses_post($FromEmail) ; ?>
									</td>
									<td>
										<?php echo wp_kses_post($each_template->subject) ; ?>
									</td>
									<td>
										<?php echo wp_kses_post($Message) ; ?>
									</td>
									<td>
										<?php echo esc_attr($each_template->noofdays) ; ?>
									</td>
									<td>
										<a href="#" class="button rs_expired_mail_active" data-rsmailid="<?php echo esc_attr($each_template->id ); ?>" data-currentstate="<?php echo wp_kses_post($Status) ; ?>"><?php echo wp_kses_post($ButtonText) ; ?></a>
									</td>
								</tr>
								<?php
								$i ++ ;
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
			$EditorId        = empty( $edit ) ? 'rs_email_new_expired' : 'rs_email_expired_template_edit' ;
			$Textarea        = array( 'textarea_name' => $EditorId ) ;
			$Content         = $Bool ? $Template->message : __( 'Hi {rsfirstname} {rslastname}, <br><br>Please check the below table which shows about your earned points with an expiry date. You can make use of those points to get discount on future purchases in {rssitelink} <br><br> {rs_points_expire} <br><br> Thanks' , 'rewardsystem' ) ;
			$TemplateName    = $Bool ? $Template->template_name : __( 'Default' , 'rewardsystem' ) ;
			$NonActiveStatus = $Bool ? selected( $Template->rs_status , 'NOTACTIVE' , false ) : '' ;
			$ActiveStatus    = $Bool ? selected( $Template->rs_status , 'ACTIVE' , false ) : '' ;
			$Woo             = $Bool ? checked( $Template->sender_opt , 'woo' , false ) : checked( 'woo' , 'woo' , false ) ;
			$Local           = $Bool ? checked( $Template->sender_opt , 'local' , false ) : '' ;
			$FromName        = $Bool ? $Template->from_name : __( 'Admin' , 'rewardsystem' ) ;
			$FromMail        = $Bool ? $Template->from_email : '' ;
			$Subject         = $Bool ? $Template->subject : '' ;
			$NoofDays        = $Bool ? $Template->noofdays : '' ;
			$ReturnURL       = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpemailexpiredpoints' ) , SRP_ADMIN_URL ) ;
			?>
			<table class="widefat">
				<tr><td><span><strong><?php esc_html_e('{rssitelink}', 'rewardsystem'); ?></strong> - <?php esc_html_e( 'Use this Shortcode to insert the Cart Link in the mail' , 'rewardsystem' ) ; ?></span></td></tr>
				<tr><td><span><strong><?php esc_html_e('{rsfirstname}', 'rewardsystem'); ?></strong> - <?php esc_html_e( 'Use this Shortcode to insert Receiver First Name in the mail' , 'rewardsystem' ) ; ?></span></td></tr>
				<tr><td><span><strong><?php esc_html_e('{rslastname}', 'rewardsystem'); ?></strong> - <?php esc_html_e( 'Use this Shortcode to insert Receiver Last Name in the mail' , 'rewardsystem' ) ; ?></span></td></tr>
				<tr><td><span><strong><?php esc_html_e('{rs_points_expire}', 'rewardsystem'); ?></strong> - <?php esc_html_e( 'Use this shortcode to display the earned points with an expiry date in the Table' , 'rewardsystem' ) ; ?></span></td></tr>
				<tr><td><span><strong><?php esc_html_e('{site_referral_url}', 'rewardsystem'); ?></strong> - <?php esc_html_e( 'Use this Shortcode for displaying the Referral Link' , 'rewardsystem' ) ; ?></span></td></tr>                 
				<tr>
					<td><?php esc_html_e( 'Template Name' , 'rewardsystem' ); ?>:</td>
					<td>
						<input type="text" name="rs_email_expired_name" id="rs_email_expired_name" value="<?php echo wp_kses_post($TemplateName) ; ?>"/>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Template Status' , 'rewardsystem' ); ?>:</td>
					<td>
						<select name="rs_expired_template_status" id="rs_expired_template_status">                       
							<option value="NOTACTIVE" <?php echo esc_attr($NonActiveStatus) ; ?> ><?php esc_html_e( 'Deactivated' , 'rewardsystem' ) ; ?></option>
							<option value="ACTIVE" <?php echo esc_attr($ActiveStatus) ; ?> ><?php esc_html_e( 'Activated' , 'rewardsystem' ) ; ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Email Sender Option' , 'rewardsystem' ); ?>:</td>
					<td>
						<input type="radio" name="rs_sender_opt_expired" id="rs_sender_woo_expired" value="woo" <?php echo esc_attr($Woo) ; ?> class="rs_sender_opt_expired"/><?php esc_html_e( 'Woocommerce' , 'rewardsystem' ) ; ?>
						<input type="radio" name="rs_sender_opt_expired" id="rs_sender_local" value="local" <?php echo esc_attr($Local) ; ?> class="rs_sender_opt_expired"><?php esc_html_e( 'Local' , 'rewardsystem' ) ; ?>
					</td>
				</tr>
				<tr class="rs_local_senders_expired">
					<td><?php esc_html_e( 'From Name' , 'rewardsystem' ); ?>:</td>
					<td>
						<input type="text" name="rs_expired_from_name" id="rs_expired_from_name" value="<?php echo esc_attr($FromName) ; ?>"/>
					</td>
				</tr>
				<tr class="rs_local_senders_expired">
					<td><?php esc_html_e( 'From Email' , 'rewardsystem' ); ?>:</td>
					<td>
						<input type="text" name="rs_expired_from_email" id="rs_expired_from_email" value="<?php echo esc_attr($FromMail) ; ?>"/>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Email Subject' , 'rewardsystem' ); ?>:</td>
					<td>
						<input type="text" name="rs_subject_expired" id="rs_subject_expired" value="<?php echo wp_kses_post($Subject) ; ?>"/>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'No of Days' , 'rewardsystem' ); ?>:</td>
					<td>
						<input type="text" name="rs_no_of_days" id="rs_no_of_days" value="<?php echo esc_attr($NoofDays) ; ?>"/>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Email Message' , 'rewardsystem' ); ?>:</td>
					<td>
						<?php
						wp_editor( $Content , $EditorId , $Textarea ) ;
						?>
					</td>
				</tr>
				<tr>
					<td>
						<input type="button" name="rs_save_new_expired_template" class="button button-primary button-large" id="rs_save_new_expired_template" value="<?php esc_html_e( 'Save' , 'rewardsystem' ) ; ?>">
						<a href="<?php echo esc_url($ReturnURL); ?>"><input type="button" class="button" name="returntolist" value=<?php esc_html_e( 'Return to Mail Templates' , 'rewardsystem' ) ; ?>></a>
					</td>
				</tr>
			</table>
			<?php
		}

		public static function reset_emailexpiry_module() {
			$Settings = RSEmailTemplate::settings_fields() ;
			RSTabManagement::reset_settings( $Settings ) ;
		}

	}

	RSEmailExpiredPointsTemplate::init() ;
}

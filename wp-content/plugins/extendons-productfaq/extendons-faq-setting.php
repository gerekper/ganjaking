<?php  if ( ! defined( 'ABSPATH' ) ) exit;  

//faq setting class
class EXTENDONS_FAQ_SETTING_CLASS extends EXTENDONS_FAQ_MAIN_CLASS {
	
	public function __construct() {
		
		add_action( 'admin_menu', array($this,'extendonss_admin_settings_option'));
	
	}

	//adding sub menu for question post type	
	function extendonss_admin_settings_option() {
		
		add_submenu_page('edit.php?post_type=product_review_post',
				        'Setting Option',
				        __( 'Settings', 'extendons_faq_domain' ),
				        'manage_options',
				        'extendon-setting-option',
				        array($this, 'wpd_custom_submenu_page_callback' ));

		
	}

// function wan_load_textdomain() {
// 	load_plugin_textdomain( 'motivation', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
// }
	
	function wpd_custom_submenu_page_callback () { ?>

		<div id="extedndons-tabs">
		
			<?php 
		
			$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {
				$active_plugins = array_merge( $active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
			}

			foreach ( $active_plugins as $plugin ) {

					$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );

				if ( in_array('Extendons', $plugin_data)) { 

					$outpluing = $plugin_data;
				}
			}

			?>

			<div class="extendons-tabs-ulli">
				
				<div class="extendon-logo-ui">
					<img src="<?php echo product_question_url.'img/Extendons-logo.png'; ?>">
					<h2><?php _e(''.$outpluing['Name'].' ('.$outpluing['Version'].')', 'extendons_faq_domain'); ?></h2>
				</div>

				<ul>
			 		<li><a href="#tabs-1"><span class="dashicons dashicons-sos"></span><?php _e('General Settings', 'extendons_faq_domain'); ?></a></li>
			 		<li><a href="#tabs-2"><span class="dashicons dashicons-email"></span><?php _e('Email Settings', 'extendons_faq_domain'); ?></a></li>
			 		<li><a href="#tabs-3"><span class="dashicons dashicons-editor-table"></span><?php _e('Google Captcha Settings', 'extendons_faq_domain'); ?></a></li>
				</ul>
				
				<ul class="collapsed-extendon">
					<li id="coll"><a href="#"><span class="dashicons dashicons-arrow-left"></span><?php _e('Collapse Menu', 'extendons_faq_domain'); ?></a></li>
				</ul>
			
			</div>
			 
			<div class="extendons-tabs-content">
				
				<!-- form starts from here -->
				<form id="extendfaq_setting_optionform" action="" method="">

				<div class="extendon-top-content">
					<h1>
						<?php _e('Extension configuration settings', 'extendons_faq_domain'); ?></h1>
					<p>
						<?php _e('Configure basic settings to personalize the extension to your website specific requirements. With an enticing user interface, you can easily enable or disable an option or functionality. Try customization the extension and explore the useful features of this extension.', 'extendons_faq_domain'); ?></p>

					<div id="option-success"><p><?php _e('Settings Saved!', 'extendons_faq_domain'); ?></p></div>
					
					<div class="extendon-support-actions">
						<div class="actions extendon-support-links">
							<a href="#" target="_blank"><span class="dashicons dashicons-thumbs-up"></span><?php _e('Support Center', 'extendons_faq_domain'); ?></a>
						</div>
						<div class="actions extendon-submit">
							<span id="ajax-extend"></span>
							<input onclick="extendsettopt()" class="button button-primary" type="button" name="" value="<?php _e('Save Changes', 'extendons_faq_domain'); ?>">
							<?php wp_nonce_field(); ?>
						</div>
					</div>

				</div>	

				<div class="extendon-singletab" id="tabs-1">
					
					<h2><?php _e('General Settings', 'extendons_faq_domain'); ?></h2>
					
					<table class="extendon-table-optoin">
						
						<tbody>

							<!-- block title -->
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Block Title', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Block Title for Single Page Section', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<input value="<?php echo get_option('page_title_setting'); ?>" id="ext_blocktitle" class="extendon-input-field" type="text">
								</td>
							</tr>

							<!-- single page tab title -->
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Tab Title Product Single Page', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Single Product Tab Title', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<input id="single_tab_title" value="<?php echo get_option('extendons_singlepage_tab'); ?>" class="extendon-input-field" type="text">
								</td>
							</tr>

							<!-- Show total question per product -->
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Total Question Per Page', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Total Question dispaly on Single page. Default 10', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<input id="single_faq_limit" value="<?php echo get_option('post_perpage_setting'); ?>" min="0" class="extendon-input-field" type="number">
								</td>
							</tr>

							<!-- Question user pending publish -->
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Question Publish/Pending', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Status of Question Publish or Pending. Default is Publish', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<div id="radios">
									  <input checked class="userfaq" value="publish" id="rad1" type="radio" name="radioBtn" <?php echo checked( get_option('qustiondisabele_enabled'), 'publish') ?>>
									  <label class="labels" for="rad1"><?php _e('Publish', 'extendons_faq_domain'); ?></label>
									  <input class="userfaq" value="pending" id="rad2" type="radio" name="radioBtn" <?php echo checked( get_option('qustiondisabele_enabled'), 'pending') ?>>
									  <label class="labels" for="rad2"><?php _e('Pending', 'extendons_faq_domain'); ?></label>
									  <div id="bckgrnd"></div>
									</div>
								</td>
							</tr>

							<!-- Asked question yes no -->
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Frontend Question Open', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Frontend Question Open/Close to Submit', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<div id="rad">
									  <input checked class="questionpermission" id="rad11" value="enabled" type="radio" name="radiosBtn" <?php echo checked( get_option('ask_queston'), 'enabled') ?>>
									  <label class="radlab" for="rad11"><?php _e('Enabled', 'extendons_faq_domain'); ?></label>
									  <input class="questionpermission" id="rad22" value="disabled" type="radio" name="radiosBtn" <?php echo checked( get_option('ask_queston'), 'disabled') ?>>
									  <label class="radlab" for="rad22"><?php _e('Disabled', 'extendons_faq_domain'); ?></label>
									  <div id="radback"></div>
									</div>
								</td>
							</tr>
							
							<!-- Likes permission -->
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Likes/Dislikes', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Default is Enable', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<div id="like_dislike">
									  <input checked value="enabled" class="likespermission" id="extld0" type="radio" name="dislike" <?php echo checked( get_option('likes_queston'), 'enabled') ?>>
									  <label class="extndc" for="extld0"><?php _e('Enabled', 'extendons_faq_domain'); ?></label>
									  <input value="disabled" class="likespermission" id="extld1" type="radio" name="dislike" <?php echo checked( get_option('likes_queston'), 'disabled') ?>>
									  <label class="extndc" for="extld1"><?php _e('Disabled', 'extendons_faq_domain'); ?></label>
									  <div id="like_dislikeb"></div>
									</div>
								</td>
							</tr>

							<!-- Question my account page -->
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('My Account Question Section Setting', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('How many Question you want to display in My Account. Default listing is 10' ,'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<input id="question_myaccount" value="<?php echo get_option('myaccount_perpage_setting'); ?>" min="0" class="extendon-input-field" type="number">
								</td>
							</tr>

							<!-- answer my account page -->
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('My Account Answer Section Setting', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('How many Answer you want to display in My Account. Default listing is 10', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<input id="answer_myaccount" value="<?php echo get_option('myaccountans_perpage_setting'); ?>" min="0" class="extendon-input-field" type="number">
								</td>
							</tr>

							<!-- comment apporoved -->
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Comments Approved/Unapproved', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Comments Approved/Unapproved Default is Apprved', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<div id="ext_comment">
									  <input checked value="1" class="commentapp" id="extc0" type="radio" name="comments" <?php echo checked( get_option('comment_approv'), '1') ?>>
									  <label class="extcc" for="extc0"><?php _e('Approved', 'extendons_faq_domain'); ?></label>
									  <input value="0" class="commentapp" id="extc1" type="radio" name="comments" <?php echo checked( get_option('comment_approv'), '0') ?>>
									  <label class="extcc" for="extc1"><?php _e('Unapproved', 'extendons_faq_domain'); ?></label>
									  <div id="ext_commentb"></div>
									</div>
								</td>
							</tr>

							<!--my account title  -->
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('My Account Title', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Title Display on My Account Page', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<input id="myaccountbtitle" value="<?php echo get_option('extendons_myaccount_blocktitle') ?>" class="extendon-input-field" type="text">
								</td>
							</tr>

							<!-- myaccount tab title -->
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('My Account Tab Tilte', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('My Account Tab Title', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<input id="myaccounttabtitle" value="<?php echo get_option('extendons_myaccount_blocktab') ?>" class="extendon-input-field" type="text">
								</td>
							</tr>

							<!-- comment permission -->
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Comment Open/Closed', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Comments on Faq Open/Closed', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<div id="commentmainid">
									 
									 <input checked value="open" class="comentper" id="commo" type="radio" name="comemntoc" <?php echo checked( get_option('commentcloseopen'), 'open') ?>>

									  <label class="commanelc" for="commo"><?php _e('Open', 'extendons_faq_domain'); ?></label>

									  <input value="close" class="comentper" id="commc" type="radio" name="comemntoc" <?php echo checked( get_option('commentcloseopen'), 'close') ?>>

									  <label class="commanelc" for="commc"><?php _e('Closed', 'extendons_faq_domain'); ?></label>
									
									  <div id="commentb"></div>
									
									</div>
								</td>
							</tr>

							<!-- have a question text -->
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Button Text', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Text for button under product price for adding new question. Default Have A Question', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<input value="<?php echo get_option('_have_a_question'); ?>" id="ext_haveaquest" class="extendon-input-field" type="text">
								</td>
							</tr>


							<!-- have a question enable disable -->
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Have A Question', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Have A Question show/hide', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<div id="haveafaq">
									 
									 <input checked value="show" class="haveafaqinc" id="hfaq1" type="radio" name="havefaqname" <?php echo checked( get_option('havfaqcloseopen'), 'show') ?>>

									  <label class="havefaql" for="hfaq1"><?php _e('Show', 'extendons_faq_domain'); ?></label>

									  <input value="hide" class="haveafaqinc" id="hfaq2" type="radio" name="havefaqname" <?php echo checked( get_option('havfaqcloseopen'), 'hide') ?>>

									  <label class="havefaql" for="hfaq2"><?php _e('Hide', 'extendons_faq_domain'); ?></label>
									
									  <div id="haveafaqb"></div>
									
									</div>
								</td>
							</tr>


							<tr class="submit-extendon extendon-option-field">
								<th></th>
								<td>
									<div class="actions extendon-submit">
										<input onclick="extendsettopt()" class="button button-primary" type="button" name="" value="<?php _e('Save Changes', 'extendons_faq_domain'); ?>">
									</div>
								</td>
							</tr>

						</tbody>

					</table>
				
				</div>

				<div class="extendon-singletab" id="tabs-2">
					
					<h2><?php _e('Email Settings', 'extendons_faq_domain'); ?></h2>
					
					<table class="extendon-table-optoin">
						
						<tbody>
							
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Sender Email', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Set Email Address for user notification. If not set admin email will be used', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<input value="<?php echo get_option('email_sender_setting'); ?>" id="email_sender_setting" class="extendon-input-field" type="email">
								</td>
							</tr>

							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Notification for Client', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('For Client Notificaiton about Question. Default is Enabled', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<div id="ext_email_noti">
									  <input checked class="ext_notify_email" value="yes" id="extn1" type="radio" name="emailnoti" <?php echo checked( get_option('notificationclient_email'), 'yes') ?>>
									  <label class="emailc" for="extn1"><?php _e('ON', 'extendons_faq_domain'); ?></label>
									  <input class="ext_notify_email" value="no" id="extn2" type="radio" name="emailnoti" <?php echo checked( get_option('notificationclient_email'), 'no') ?>>
									  <label class="emailc" for="extn2"><?php _e('OFF', 'extendons_faq_domain');?></label>
									  <div id="emailb"></div>
									</div>
								</td>
							</tr>

							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Comment Submit notification', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('For Client Notificaiton on comment submission. Default is Enabled', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<div id="ext_comme_noti">
									  <input checked class="ext_notify_comme" value="yes" id="extcn1" type="radio" name="commenoti" <?php echo checked( get_option('notificationclient_comme'), 'yes') ?>>
									  <label class="commec" for="extcn1"><?php _e('ON', 'extendons_faq_domain'); ?></label>
									  <input class="ext_notify_comme" value="no" id="extcn2" type="radio" name="commenoti" <?php echo checked( get_option('notificationclient_comme'), 'no') ?>>
									  <label class="commec" for="extcn2"><?php _e('OFF', 'extendons_faq_domain');?></label>
									  <div id="commeb"></div>
									</div>
								</td>
							</tr>

							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Client Email Subject', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Notification Email Subject', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<input value="<?php echo get_option('emailclient_subject'); ?>" id="emailclient_subject" class="extendon-input-field" type="text">
								</td>
							</tr>

							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Email Content', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Content For Email When a Question is Submitted', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<textarea id="email_content_publish" rows="5" class="extendon-input-field"><?php echo get_option('email_content_publish'); ?></textarea>
								</td>
							</tr>

							<tr class="submit-extendon extendon-option-field">
								<th></th>
								<td>
									<div class="actions extendon-submit">
										<input onclick="extendsettopt()" class="button button-primary" type="button" name="" value="<?php _e('Save Changes', ''); ?>">
									</div>
								</td>
							</tr>

						</tbody>

					</table>

				</div>

				<div class="extendon-singletab" id="tabs-3">
					<h2><?php _e('Google Captcha Settings', 'extendons_faq_domain'); ?></h2>
					
					<table class="extendon-table-optoin">
						
						<tbody>
							
							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Your recaptcha Secret Key', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Go to Google recaptcha site <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">Google Recaptcha</a> then click on top right reCaptcha button and follow the Instructions. Register you site by giving url path and get the secret key.', 'extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<input value="<?php echo get_option('your_secret_key'); ?>" id="your_secret_key" class="extendon-input-field" type="text">
								</td>
							</tr>

							<tr class="extendon-option-field">
								<th>
									<div class="option-head">
										<h3><?php _e('Your recaptcha Site Key', 'extendons_faq_domain'); ?></h3>
									</div>
									<span class="description">
										<p><?php _e('Go to Google recaptcha site <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">Google Recaptcha</a> then click on top right reCaptcha button and follow the Instructions. Register you site by giving url path and get the site key.','extendons_faq_domain'); ?></p>
									</span>
								</th>
								<td>
									<input value="<?php echo get_option('your_site_key'); ?>" id="your_site_key" class="extendon-input-field" type="text">
								</td>
							</tr>

							<tr class="submit-extendon extendon-option-field">
								<th></th>
								<td>
									<div class="actions extendon-submit">
										<input onclick="extendsettopt()" class="button button-primary" type="button" name="" value="<?php _e('Save Changes', 'extendons_faq_domain'); ?>">
									</div>
								</td>
							</tr>

						</tbody>

					</table>

				</div>
			
				</form>

			</div>
		
		</div>
		 
		<script>
			
			jQuery( function() {
				jQuery( "#extedndons-tabs" ).tabs().addClass('ui-tabs-vertical ui-helper-clearfix');
		  	});

		  	// ajax function for submitting setting option
		  	function extendsettopt() {
		  		
				var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
				
				var condition = 'setting_extend';

				var extend_blocktitle = jQuery('#ext_blocktitle').val();
				var single_tab_title = jQuery('#single_tab_title').val();					
				var single_q_limit = jQuery('#single_faq_limit').val();
				var faqpublishpending = jQuery('.userfaq:checked').val();	
				var faq_permission = jQuery('.questionpermission:checked').val();
				var likes_enable = jQuery('.likespermission:checked').val();
				var questionmyaccount = jQuery('#question_myaccount').val();
				var answermyaccont = jQuery('#answer_myaccount').val();
				var commentapproval = jQuery('.commentapp:checked').val();
				var commentcloseopen = jQuery('.comentper:checked').val();
				var ext_haveaquest = jQuery('#ext_haveaquest').val();
				var ext_haveafaqdisable = jQuery('.haveafaqinc:checked').val();

				var myaccountbtitle = jQuery('#myaccountbtitle').val();
				var myaccounttabtitle = jQuery('#myaccounttabtitle').val();

				var sender_email = jQuery('#email_sender_setting').val();
				var email_notification = jQuery('.ext_notify_email:checked').val();	
				var cemail_notification = jQuery('.ext_notify_comme:checked').val();	
				var email_subject = jQuery('#emailclient_subject').val();
				var email_content = jQuery('#email_content_publish').val();

				var google_secret_key = jQuery('#your_secret_key').val();
				var google_site_key = jQuery('#your_site_key').val();
				jQuery('#ajax-extend').show();
					jQuery.ajax({
						url : ajaxurl,
						type : 'post',
						data : {
							action : 'extendon_settingopt',
							
							condition : condition,

							extend_blocktitle : extend_blocktitle,
							single_tab_title : single_tab_title,
							single_q_limit :single_q_limit,
							faqpublishpending : faqpublishpending,
							faq_permission : faq_permission,
							likes_enable : likes_enable,
							questionmyaccount : questionmyaccount,
							answermyaccont : answermyaccont,
							commentapproval : commentapproval,
							commentcloseopen : commentcloseopen,
							ext_haveaquest : ext_haveaquest,
							ext_haveafaqdisable : ext_haveafaqdisable,

							myaccountbtitle : myaccountbtitle,
							myaccounttabtitle : myaccounttabtitle,

							sender_email : sender_email,
							email_notification : email_notification,
							email_subject : email_subject,
							cemail_notification : cemail_notification,
							email_content : email_content,

							google_secret_key : google_secret_key,
							google_site_key : google_site_key,
						},
						success : function(response) {
							jQuery("#option-success").show().delay(3000).fadeOut("slow");
						},
						complete: function(){
						    jQuery('#ajax-extend').hide();
						}
					});
		  	}

		  	jQuery(document).ready(function(){
			    
			    jQuery("#coll").click(function() {
			        
			        jQuery('.extendons-tabs-ulli').toggleClass('red');
			        jQuery(".extendon-logo-ui h2").toggleClass('reddisnon');
			        jQuery('.extendons-tabs-content').toggleClass('green');
			      	jQuery('#coll span.dashicons').toggleClass('dashicons-arrow-left dashicons-arrow-right');
			        
			        if (jQuery('.extendons-tabs-ulli').hasClass('red')){
			        	
			        	jQuery('#ui-id-1').get(0).lastChild.nodeValue = "";
			        	jQuery('#ui-id-2').get(0).lastChild.nodeValue = "";
			        	jQuery('#ui-id-3').get(0).lastChild.nodeValue = "";
			        	jQuery('#coll a').get(0).lastChild.nodeValue = "";
			       	
			       	} else {
			       		
			       		jQuery('.extendons-tabs-ulli').addClass('redd');
			       		jQuery('#ui-id-1').get(0).lastChild.nodeValue = "General Setting";
			       		jQuery('#ui-id-2').get(0).lastChild.nodeValue = "Email Settings";
			        	jQuery('#ui-id-3').get(0).lastChild.nodeValue = "Google Captcha Settings";
			        	jQuery('#coll a').get(0).lastChild.nodeValue = "Collapse Menu";
			       	}
			    });
			});

		</script>

	<?php }

}
new EXTENDONS_FAQ_SETTING_CLASS();
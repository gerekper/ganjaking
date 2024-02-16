<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Code is poetry.';
    exit;
}

function wpuf_admin_settings_page() {
	//Check
	
	//Google Captcha Sitekey
	if ( empty(get_option("wpuf_recaptcha_sitekey"))) { 
		update_option("wpuf_recaptcha_sitekey", "000000000000000000000000000000"); 
	} else { 
		get_option("wpuf_recaptcha_sitekey"); 
	}
	
	//Google Captcha Secretkey
	if ( empty(get_option("wpuf_recaptcha_secretkey"))) { 
		update_option("wpuf_recaptcha_secretkey", "000000000000000000000000000000"); 
	} else { 
		get_option("wpuf_recaptcha_secretkey"); 
	}
	
	//UptimeRobot API Key
	if ( empty(get_option("wpuf_uptimerobot_api"))) { 
		update_option("wpuf_uptimerobot_api", "000000000000000000000000000000");
	} else { 
		get_option("wpuf_uptimerobot_api"); 
	}	
	
	//Notify Mail
	if ( empty(get_option("wpuf_mail_notify"))) { 
		$get_admin_Email = get_option('admin_email');
		update_option("wpuf_mail_notify", $get_admin_Email);
	} else { 
		get_option("wpuf_mail_notify"); 
	}	
	
	//Default Google reCAPTCHA API
	$checkReCaptchaSiteKeyforSS = get_option("wpuf_recaptcha_sitekey") == '000000000000000000000000000000';
	$checkReCaptchaSecretKeyforSS = get_option("wpuf_recaptcha_secretkey") == '000000000000000000000000000000';
	
	//Google Captcha Sitekey
	if ($checkReCaptchaSiteKeyforSS || $checkReCaptchaSecretKeyforSS AND get_option("wpuf_select_set") == 4 || get_option("wpuf_select_set") == 5 || get_option("wpuf_security_ban") == 2 ) {
		update_option("wpuf_select_set", 1);
		update_option("wpuf_security_ban", 1);
		$recType = 'updated';
		$recMessage = __( 'Please enter your reCAPTCHA API key!', 'ua-protection-lang' );
		$cRCECount = 1;
		
	add_settings_error(
		'wp_error_reCAPTCHA',
		esc_attr( 'settings_updated' ),
		$recMessage,
		$recType
	);	
	} else { $cRCECount = 0; }
	
    ?>
    <div class="wrap projectStyle">
	<div id="whiteboxH" class="postbox">
	
	<div class="topHead">
		<h2><?php echo __("Admin Settings","ua-protection-lang") ?></h2>
		<?php if ($cRCECount == 1) { settings_errors('wp_error_reCAPTCHA'); } else{ settings_errors();} ?>
	</div>
	
	<div class="inside">
        <form action="options.php" method="post">
        <?php settings_fields("wpuf_admin_settings") ?>
		<h3><?php echo __("Security Settings","ua-protection-lang") ?></h3>
            <table class="form-table">	
			
			<tr valign="top">
				<th scope="row"><label for="wpuf_select_set"><?php echo __("Security Level","ua-protection-lang") ?></label></th>
				<td>
				<label>
				<select style="width:40%;margin-top:5px;" name="wpuf_select_set">
					<option value="1" <?php selected( get_option("wpuf_select_set"), 1); ?>><?php echo __("Custom Settings","ua-protection-lang") ?></option>
					<option value="2" <?php selected( get_option("wpuf_select_set"), 2); ?>><?php echo __("Low","ua-protection-lang") ?></option>
					<option value="3" <?php selected( get_option("wpuf_select_set"), 3); ?>><?php echo __("Medium","ua-protection-lang") ?></option>
					<option value="4" <?php selected( get_option("wpuf_select_set"), 4); ?>><?php echo __("High","ua-protection-lang") ?></option>
					<option value="5" <?php selected( get_option("wpuf_select_set"), 5); ?>><?php echo __("I'm Under Attack!","ua-protection-lang") ?></option>
				</select>
				</label>
					<p><?php echo __("Arrange your security level.","ua-protection-lang") ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="wpuf_security_ban"><?php echo __("Prevention Method","ua-protection-lang") ?></label></th>
				<td>
				<label>
				<select style="width:40%;margin-top:5px;" name="wpuf_security_ban">
					<option value="1" <?php selected( get_option("wpuf_security_ban"), 1); ?>><?php echo __("Do not allow access.","ua-protection-lang") ?></option>
					<option value="2" <?php selected( get_option("wpuf_security_ban"), 2); ?>><?php echo __("Protect with reCAPTCHA.","ua-protection-lang") ?></option>
				</select>
				</label>
					<p><?php echo __("The prevention method is designs as two options. You can block the attacks directly if you want, or you can check it through the reCAPTCHA control. We recommend that you activate the reCAPTCHA control option.","ua-protection-lang") ?></p>
				</td>
			</tr> 	

	</table>
		<h3><?php echo __("API Settings","ua-protection-lang") ?></h3>
            <table class="form-table">

				<tr valign="top">
					<th scope="row"><label for="wpuf_recaptcha_sitekey"><?php echo __("reCAPTCHA API keys","wp-useful-features") ?></label></th>
					<td>
						<input name="wpuf_recaptcha_sitekey" id="wpuf_recaptcha_sitekey" type="text" value="<?php echo get_option("wpuf_recaptcha_sitekey"); ?>" class="regular-text" />
						 <p style="color:#7a7a7a;" ><?php echo __("reCAPTCHA Site Key","wp-useful-features") ?></p>
					</td>
				</tr>			
				
				<tr valign="top">
					<th scope="row"></th>
					<td>
						<input name="wpuf_recaptcha_secretkey" id="wpuf_recaptcha_secretkey" type="text" value="<?php echo get_option("wpuf_recaptcha_secretkey"); ?>" class="regular-text" />
						 <p style="color:#7a7a7a;" ><?php echo __("reCAPTCHA Secret Key","wp-useful-features") ?></p>
					</td>
				</tr>
						
			<tr valign="top">
					<th scope="row"><label for="wpuf_uptimerobot_api"><?php echo __("Uptime Robot API","wp-useful-features") ?></label></th>
					<td>
					<input name="wpuf_uptimerobot_api" id="wpuf_uptimerobot_api" type="text" value="<?php echo get_option("wpuf_uptimerobot_api"); ?>" class="regular-text" />
						 <p style="color:#7a7a7a;" ><?php echo __("Uptime Robot Monitor-Specific API Key","wp-useful-features") ?></p>
					</td>
				</tr>

	</table>
		<h3><?php echo __("Notification Settings","ua-protection-lang") ?></h3>
            <table class="form-table">

                <tr valign="top">
                    <th scope="row"><label for="wpuf_mail_alarm"><?php echo __("Mail Notifications","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_mail_alarm") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_mail_alarm" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("By activating email notification tool, you can get emails of attacks that were performed to your website. If your server that you are using is not compatible with email, you can make required settings by SMTP plugins.","ua-protection-lang") ?></p>
                    </td>
                </tr> 	
				
			<?php if( get_option("wpuf_mail_alarm") == 1 ) : ?>	
				
				<tr valign="top">
					<th scope="row"><label for="wpuf_mail_notify"><?php echo __("Notification Address","wp-useful-features") ?></label></th>
					<td>
						<input name="wpuf_mail_notify" id="wpuf_mail_notify" type="text" value="<?php echo get_option("wpuf_mail_notify"); ?>" class="regular-text" />
						 <p style="color:#7a7a7a;" ><?php echo __("Enter your email address for notifications","wp-useful-features") ?></p>
					</td>
				</tr>
				
				<tr valign="top" >
					<th scope="row"><label for="wpuf_mail_alarm_admin"><?php echo __("Notification Types","ua-protection-lang") ?></label></th>
					<td style="padding-top: 15px; padding-bottom:0;">
					<label>
					  <input <?php if( get_option("wpuf_mail_alarm_admin") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_mail_alarm_admin" type="checkbox"/>
					  <?php echo __("Admin Login Logs","ua-protection-lang") ?>
					</label>
					</td>
				</tr>
			
				<tr valign="top">
					<th scope="row"><label for="wpuf_mail_alarm_hacker"></label></th>
						
					<td style="padding-top: 5px; padding-bottom:5px;">
						<label>
						  <input <?php if( get_option("wpuf_mail_alarm_hacker") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_mail_alarm_hacker" type="checkbox"/>
						  <?php echo __("Hacker Attacks","ua-protection-lang") ?>
						</label>
					</td>
				</tr>

				<tr valign="top" >
					<th scope="row"><label for="wpuf_mail_alarm_bruteforce"></label></th>
					<td style="padding-top: 5px; padding-bottom:5px;">
					<label>
					  <input <?php if( get_option("wpuf_mail_alarm_bruteforce") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_mail_alarm_bruteforce" type="checkbox"/>
					  <?php echo __("Brute-Force Attacks","ua-protection-lang") ?>
					</label>
					</td>
				</tr>	

				<tr valign="top">
					<th scope="row"><label for="wpuf_mail_alarm_proxy"></label></th>
					<td style="padding-top: 5px; padding-bottom:5px;">
					<label>
					  <input <?php if( get_option("wpuf_mail_alarm_proxy") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_mail_alarm_proxy" type="checkbox"/>
					  <?php echo __("Proxy Attacks","ua-protection-lang") ?>
					</label>
					</td>
				</tr>				
				
				<tr valign="top">
					<th scope="row"><label for="wpuf_mail_alarm_spam"></label></th>
					<td style="padding-top: 5px; padding-bottom:5px;">
					<label>
					  <input <?php if( get_option("wpuf_mail_alarm_spam") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_mail_alarm_spam" type="checkbox"/>
					  <?php echo __("Spam Attacks","ua-protection-lang") ?>
					</label>
					</td>				
				</tr >	
				
				<tr valign="top">
					<th scope="row"><label for="wpuf_mail_alarm_fc"></label></th>
					<td style="padding-top: 5px; padding-bottom:5px;">
					<label>
					  <input <?php if( get_option("wpuf_mail_alarm_fc") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_mail_alarm_fc" type="checkbox"/>
					  <?php echo __("Spam Comment Attacks","ua-protection-lang") ?>
					</label>
					</td>
				</tr>								
			
			<?php endif; ?> 
	
	</table>
	

          
      </div>
	  
	</div>
</div>

            <div class="wrap projectStyle" id="whiteboxH">
				<div class="postbox">
				<div class="inside">
				<div style="display:inline-block">
			  
					<div class="contentDoYouLike">
					  <p><?php echo __("How would you rate <strong>WP Ultimate Firewall</strong>?", "ua-protection-lang") ?></p>
					</div>

					<div class="wrapperDoYouLike">
					  <input type="checkbox" id="st1" value="1" />
					  <label for="st1"></label>
					  <input type="checkbox" id="st2" value="2" />
					  <label for="st2"></label>
					  <input type="checkbox" id="st3" value="3" />
					  <label for="st3"></label>
					  <input type="checkbox" id="st4" value="4" />
					  <label for="st4"></label>
					  <input type="checkbox" id="st5" value="5" />
					  <label for="st5"></label>
					</div>					
					
					<a target="_blank" href="https://codecanyon.net/item/wp-ultimate-firewall-website-security-optimization/reviews/20695212" class="sabutton button button-primary" style="margin: -5px 0 0 50px;"><?php echo __("Rate this plugin!", "ua-protection-lang") ?></a>
				</div>
					<?php submit_button(); ?>
				</div>
				</div>
			</div>
</form>
    <?php
}

add_action("admin_init","wpuf_admin_register");
function wpuf_admin_register() {
	
	//Security Level
	register_setting("wpuf_admin_settings","wpuf_select_set");
	//Mail
	register_setting("wpuf_admin_settings","wpuf_mail_alarm");
	
	if( get_option("wpuf_mail_alarm") == 1 ) {
		register_setting("wpuf_admin_settings","wpuf_mail_alarm_admin");
		register_setting("wpuf_admin_settings","wpuf_mail_notify");
		register_setting("wpuf_admin_settings","wpuf_mail_alarm_spam");
		register_setting("wpuf_admin_settings","wpuf_mail_alarm_hacker");
		register_setting("wpuf_admin_settings","wpuf_mail_alarm_fc");
		register_setting("wpuf_admin_settings","wpuf_mail_alarm_bruteforce");
		register_setting("wpuf_admin_settings","wpuf_mail_alarm_proxy");
	}
	//Captcha API
	register_setting("wpuf_admin_settings","wpuf_recaptcha_sitekey");
	register_setting("wpuf_admin_settings","wpuf_recaptcha_secretkey");
	register_setting("wpuf_admin_settings","wpuf_uptimerobot_api");
	register_setting("wpuf_admin_settings","wpuf_security_ban");
	
	
}
<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Code is poetry.';
    exit;
}

function wpuf_firewall_settings_page() {
	
	//Default Google reCAPTCHA API
	$checkReCaptchaSiteKeyforSS = get_option("wpuf_recaptcha_sitekey") == '000000000000000000000000000000';
	$checkReCaptchaSecretKeyforSS = get_option("wpuf_recaptcha_secretkey") == '000000000000000000000000000000';
	
	//Google Captcha Sitekey
	if ( $checkReCaptchaSiteKeyforSS || $checkReCaptchaSecretKeyforSS AND get_option("wpuf_access_security") == 1 || get_option("wpuf_comment_sec_wc") == 2 || get_option("wpuf_recaptcha_protection_lrl") == 1 || get_option("wpuf_spam_attacks_psp") == 1 ) {
		update_option("wpuf_access_security", 0);
		update_option("wpuf_comment_sec_wc", 0);
		update_option("wpuf_recaptcha_protection_lrl", 0);
		update_option("wpuf_spam_attacks_psp", 0);
		$recType = 'updated';
		$recMessage = __( 'Please enter your reCAPTCHA API key!', 'ua-protection-lang' );
		$cRCECount = 1;
		
	add_settings_error(
		'wp_error_reCAPTCHA_Fw',
		esc_attr( 'settings_updated' ),
		$recMessage,
		$recType
	);	
	} else { $cRCECount = 0; }
	
if (get_option("wpuf_select_set") == 1) {
    ?>
    <div class="wrap projectStyle">
	<div id="whiteboxH" class="postbox">
	
	<div class="topHead">
		<h2><?php echo __("Security &amp; Firewall Settings","ua-protection-lang") ?></h2>
		<?php if ($cRCECount == 1) { settings_errors('wp_error_reCAPTCHA_Fw'); } else{ settings_errors();} ?>
	</div>
	
	<div class="inside">
        <form action="options.php" method="post">
        <?php settings_fields("wpuf_firewall_settings") ?>
            <table class="form-table">                
			<h3><?php echo __("Security Settings","ua-protection-lang") ?></h3>
				<tr valign="top">
                    <th scope="row"><label for="wpuf_header_sec"><?php echo __("HTTP Security Headers","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_header_sec") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_header_sec" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Secure your website's HTTP with X-Content-Type-Options, X-XSS-Protection and X-Frame-Options settings.","ua-protection-lang") ?></p>
                    </td>
                </tr>				
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_xr_security"><?php echo __("XML-RPC and REST-API Security","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_xr_security") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_xr_security" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Prevent XML-RPC and REST-API requests.","ua-protection-lang") ?></p>
                    </td>
                </tr>				
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_disable_fileedit"><?php echo __("File Editor","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_disable_fileedit") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_disable_fileedit" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Disable file editing in the Wordpress Admin panel.","ua-protection-lang") ?></p>
                    </td>
                </tr> 				
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_recaptcha_protection_lrl"><?php echo __("Captcha Protection","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_recaptcha_protection_lrl") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_recaptcha_protection_lrl" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Secure your User Registration, Login and Lost Password forms with reCAPTCHA.","ua-protection-lang") ?></p>
                    </td>
                </tr> 				 	
				<?php if( get_option("wpuf_recaptcha_protection_lrl") == 1 ) : ?>	
				<tr valign="top">
					<th scope="row"><label for="wpuf_recaptcha_protection_lrl_login"><?php echo __("Captcha Protection Settings","ua-protection-lang") ?></label></th>
						
					<td style="padding-top:15px;padding-bottom:0;">
						<label>
						  <input <?php if( get_option("wpuf_recaptcha_protection_lrl_login") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_recaptcha_protection_lrl_login" type="checkbox"/>
						  <?php echo __("Login Form","ua-protection-lang") ?>
						</label>
					</td>
				</tr>				
				
				<tr valign="top">
					<th scope="row"></th>
						
					<td style="padding-top: 5px; padding-bottom:5px;">
						<label>
						  <input <?php if( get_option("wpuf_recaptcha_protection_lrl_registration") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_recaptcha_protection_lrl_registration" type="checkbox"/>
						  <?php echo __("User Registration Form","ua-protection-lang") ?>
						</label>
					</td>
				</tr>				
				
				<tr valign="top">
					<th scope="row"></th>
						
					<td style="padding-top: 5px; padding-bottom:5px;">
						<label>
						  <input <?php if( get_option("wpuf_recaptcha_protection_lrl_lpf") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_recaptcha_protection_lrl_lpf" type="checkbox"/>
						  <?php echo __("Lost Password Form","ua-protection-lang") ?>
						</label>
					</td>
				</tr>
				<?php endif; ?>
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_comment_sec_wc"><?php echo __("Comment Security","ua-protection-lang") ?></label></th>
                    <td>
					<label>
						<select style="width:40%;margin-top:5px;" name="wpuf_comment_sec_wc">
							<option value="0" <?php selected( get_option("wpuf_comment_sec_wc"), 0); ?>><?php echo __("Disable","ua-protection-lang") ?></option>
							<option value="1" <?php selected( get_option("wpuf_comment_sec_wc"), 1); ?>><?php echo __("Protect with Honeypot","ua-protection-lang") ?></option>
							<option value="2" <?php selected( get_option("wpuf_comment_sec_wc"), 2); ?>><?php echo __("Protect with reCAPTCHA","ua-protection-lang") ?></option>
						</select>
					</label>
                        <p><?php echo __("Edit comment form protection settings.","ua-protection-lang") ?></p>
                    </td>
                </tr> 				
				
				<?php if( get_option("wpuf_comment_sec_wc") == 2) : ?>
				<tr valign="top">
                    <th scope="row"></th>
                    <td>
					<label class="button-toggle-wrap goTop">
					  <input <?php if( get_option("wpuf_disable_rcp_lgus") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_disable_rcp_lgus" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p><?php echo __("Disable reCAPTCHA for logged-in users.","ua-protection-lang") ?></p>
                    </td>
                </tr> 		
				<?php endif; ?>
				
				<tr valign="top">
                    <th scope="row"></th>
                    <td>
					<label class="button-toggle-wrap goTop">
					  <input <?php if( get_option("wpuf_pingback_disable") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_pingback_disable" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p><?php echo __("To prevent pingback spam attacks, disable comment pingbacks.","ua-protection-lang") ?></p>
                    </td>
                </tr> 
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_content_security"><?php echo __("Content Protection","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_content_security") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_content_security" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("With Content Protection, by turning off functions like right-click, view page source, and etc, you can prevent others from stealing your content.","ua-protection-lang") ?></p>
                    </td>
                </tr>

	</table>
		<h3><?php echo __("Firewall Settings","ua-protection-lang") ?></h3>
            <table class="form-table">	
			
				<tr valign="top">
                    <th scope="row"><label for="wpuf_spam_attacks"><?php echo __("Real-Time Firewall Protection","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_spam_attacks") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_spam_attacks" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Block Spam Attacks and Requests with Real-Time Firewall.","ua-protection-lang") ?></p>
                    </td>
                </tr> 	
			 	
				<?php if( get_option("wpuf_spam_attacks") == 1 ) : ?>	
				<tr valign="top">
					<th scope="row"><label for="wpuf_spam_attacks_general"><?php echo __("Firewall Settings","ua-protection-lang") ?></label></th>
						
					<td style="padding-top:15px;padding-bottom:0;">
						<label>
						  <input <?php if( get_option("wpuf_spam_attacks_general") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_spam_attacks_general" type="checkbox"/>
						  <?php echo __("Block Spam &amp; Mass Requests","ua-protection-lang") ?>
						</label>
					</td>
				</tr>				
				
				<tr valign="top">
					<th scope="row"></th>
						
					<td style="padding-top: 5px; padding-bottom:5px;">
						<label>
						  <input <?php if( get_option("wpuf_spam_attacks_psp") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_spam_attacks_psp" type="checkbox"/>
						  <?php echo __("Block Potential Spam Users","ua-protection-lang") ?>
						</label>
					</td>
				</tr>				
				
				<tr valign="top">
					<th scope="row"></th>
						
					<td style="padding-top: 5px; padding-bottom:5px;">
						<label>
						  <input <?php if( get_option("wpuf_spam_attacks_bf") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_spam_attacks_bf" type="checkbox"/>
						  <?php echo __("Block Brute-Force attacks from Wordpress Panel","ua-protection-lang") ?>
						</label>
					</td>
				</tr>
				<?php endif; ?>			
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_wpscan_protection"><?php echo __("WPScan Protection","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_wpscan_protection") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_wpscan_protection" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Block WPScan requests.","ua-protection-lang") ?></p>
                    </td>
                </tr> 				
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_tor_protection"><?php echo __("Tor Detection","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_tor_protection") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_tor_protection" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Block access to Tor Users and Servers.","ua-protection-lang") ?></p>
                    </td>
                </tr>
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_proxy_protection"><?php echo __("Proxy Protection","ua-protection-lang") ?></label></th>
                    <td>
					<label>
						<select style="width:40%;margin-top:5px;" name="wpuf_proxy_protection">
							<option value="0" <?php selected( get_option("wpuf_proxy_protection"), 0); ?>><?php echo __("Disable","ua-protection-lang") ?></option>
							<option value="1" <?php selected( get_option("wpuf_proxy_protection"), 1); ?>><?php echo __("Low","ua-protection-lang") ?></option>
							<option value="2" <?php selected( get_option("wpuf_proxy_protection"), 2); ?>><?php echo __("High","ua-protection-lang") ?></option>
						</select>
					</label>
                        <p><?php echo __("Real-time Proxy Protection prevents people using the Proxy from log in to your site.","ua-protection-lang") ?></p>
                    </td>
                </tr>	
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_access_security"><?php echo __("Access Security","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_access_security") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_access_security" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Take reCAPTCHA control of the people entering your site.","ua-protection-lang") ?></p>
                    </td>
                </tr>				
				
				<tr valign="top">
                    <th scope="row"><label for="wpuf_sql_protection"><?php echo __("Hacker Protection","ua-protection-lang") ?></label></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_sql_protection") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_sql_protection" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Prevent SQL Injection attacks.","ua-protection-lang") ?></p>
                    </td>
                </tr>				
				
				<tr valign="top">
                    <th scope="row"></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_badbot_protection") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_badbot_protection" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Protect yourself from malicious bots.","ua-protection-lang") ?></p>
                    </td>
                </tr>					
				
				<tr valign="top">
                    <th scope="row"></th>
                    <td>
					<label class="button-toggle-wrap">
					  <input <?php if( get_option("wpuf_fakebot_protection") == 1) {echo 'checked="checked"';} ?> value="1" name="wpuf_fakebot_protection" class="toggler" type="checkbox" data-toggle="button-toggle"/>
					  <div class="button-toggle">
						<div class="handle">
						  <div class="bars"></div>
						</div>
					  </div>
					</label>
                        <p class="description" ><?php echo __("Do not allow access to fake search engine bots.","ua-protection-lang") ?></p>
                    </td>
                </tr>				
				
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
} else { ?>

<div class="wrap projectStyle">
	<div id="whiteboxH" class="postbox">
		<div class="topHead">
			<h2><?php echo __("Firewall Settings","ua-protection-lang") ?></h2>
		</div>
	
		<div class="inside">
			<p class="alert alert-error"><?php echo __("Security mode is enabled. If you want to, you can change the security level to '<strong>Custom Settings</strong>'.","ua-protection-lang") ?></p>
		</div>
	</div>
</div>

<?php
	}
}

add_action("admin_init","wpuf_firewall_register");
function wpuf_firewall_register() {
	register_setting("wpuf_firewall_settings","wpuf_header_sec");
	register_setting("wpuf_firewall_settings","wpuf_wpscan_protection");
	register_setting("wpuf_firewall_settings","wpuf_xr_security");
	register_setting("wpuf_firewall_settings","wpuf_pingback_disable");
	register_setting("wpuf_firewall_settings","wpuf_proxy_protection");
	register_setting("wpuf_firewall_settings","wpuf_access_security");
	register_setting("wpuf_firewall_settings","wpuf_comment_sec_wc");
	register_setting("wpuf_firewall_settings","wpuf_sql_protection");
	register_setting("wpuf_firewall_settings","wpuf_badbot_protection");
	register_setting("wpuf_firewall_settings","wpuf_fakebot_protection");
	register_setting("wpuf_firewall_settings","wpuf_content_security");
	register_setting("wpuf_firewall_settings","wpuf_disable_rcp_lgus");
	register_setting("wpuf_firewall_settings","wpuf_tor_protection");
	register_setting("wpuf_firewall_settings","wpuf_disable_fileedit");
	
	register_setting("wpuf_firewall_settings","wpuf_spam_attacks");
	if( get_option("wpuf_spam_attacks") == 1 ) {
		register_setting("wpuf_firewall_settings","wpuf_spam_attacks_general");
		register_setting("wpuf_firewall_settings","wpuf_spam_attacks_bf");
		register_setting("wpuf_firewall_settings","wpuf_spam_attacks_psp");
	}	
	
	register_setting("wpuf_firewall_settings","wpuf_recaptcha_protection_lrl");
	if( get_option("wpuf_recaptcha_protection_lrl") == 1 ) {
		register_setting("wpuf_firewall_settings","wpuf_recaptcha_protection_lrl_login");
		register_setting("wpuf_firewall_settings","wpuf_recaptcha_protection_lrl_registration");
		register_setting("wpuf_firewall_settings","wpuf_recaptcha_protection_lrl_lpf");
	}
}
<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Code is poetry.';
    exit;
}

//Access Settings Page
function wpuf_access_settings_page() {

    $uptMessage = null;
    $uptType = null;
	
    // Save Options
    if (isset( $_POST['wpuf_access_sec'] ) && wp_verify_nonce( $_POST['wpuf_access_sec'], 'access_set' ) ) {
        $wpuf_firewall_iplist = wp_kses($_POST['wpuf_firewall_iplist'], array());
        $wpuf_firewall_useragents = wp_kses($_POST['wpuf_firewall_useragents'], array());
        $white_ip_list = wp_kses($_POST['white_ip_list'], array());
        $blocked_country_list = wp_kses($_POST['blocked_country_list'], array());
        update_option('get_ip_list', $wpuf_firewall_iplist);
        update_option('get_ua_list', $wpuf_firewall_useragents);
        update_option('get_white_list', $white_ip_list);
        update_option('get_blocked_country_list', $blocked_country_list);
		
		//Updated
		$uptType = 'updated';
		$uptMessage = __( 'Settings saved.', 'ua-protection-lang' );
		add_settings_error(
			'wp_updated_display',
			esc_attr( 'settings_updated' ),
			$uptMessage,
			$uptType
		);
	}

    // Set Options
    $wpuf_firewall_iplist = get_option('get_ip_list');
    $wpuf_firewall_useragents = get_option('get_ua_list');
    $white_ip_list = get_option('get_white_list');
    $blocked_country_list = get_option('get_blocked_country_list');
?>

<div class="wrap projectStyle">
	<div id="whiteboxH" class="postbox">
		<div class="topHead">
			<h2><?php echo __("Access Settings","ua-protection-lang") ?></h2>
			<?php if (isset( $_POST['wpuf_access_sec'] ) && wp_verify_nonce( $_POST['wpuf_access_sec'], 'access_set' ) ) { settings_errors(); } ?>
		</div>
		
		<div class="inside">
			<p class="alert alert-error"><?php echo __("Please write the information into cells by top to down and by giving a space break. For instance, if you are entering an IP address, give a space break from below to write a second IP address.","ua-protection-lang") ?></p>

			<form action="" method="post">

				<label for='firewall-ip-list'><h2 style="margin-top:40px;"><?php echo __("Block IP Addresses","ua-protection-lang") ?></h2></label>
				<p><?php echo __("<strong>IP addresses</strong> enter one by one or like <strong>127.0.0.1 - 127.0.0.55</strong> state the range.","ua-protection-lang") ?></p>
				<textarea name='wpuf_firewall_iplist' id='firewall-ip-list' class="large-text code" rows="6"><?php echo $wpuf_firewall_iplist ?></textarea>

				<label for='blocked-country-list'><h2 style="margin-top:40px;"><?php echo __("Block Countries","ua-protection-lang") ?></h2></label>
				<p><?php echo __("Enter the ISO code of the country you want to block. <strong>Example:</strong> <strong>TR</strong> for Turkey, <strong>US</strong> for United States.","ua-protection-lang") ?></p>
				<textarea name='blocked_country_list' id='blocked-country-list' class="large-text code" rows="6"><?php echo $blocked_country_list ?></textarea>		

				<label for='firewall-user-agents'><h2 style="margin-top:40px;"><?php echo __("Block User Agents","ua-protection-lang") ?></h2></label>
				<p><?php echo __("<strong>Example User Agent:</strong> Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0","ua-protection-lang") ?></p>
				<textarea name='wpuf_firewall_useragents' id='firewall-user-agents' class="large-text code" rows="6"><?php echo $wpuf_firewall_useragents ?></textarea>
				
				<label for='white-ip-list'><h2 style="margin-top:40px;"><?php echo __("Whitelist","ua-protection-lang") ?></h2></label>
				<p><?php echo __("The IP addresses you added to Whitelist are excluded from your active security settings. We recommend you to use this setting if you are using a Static IP address. <strong>Whitelist IP addresses</strong> enter one by one or like <strong>127.0.0.1 - 127.0.0.55</strong> state the range.","ua-protection-lang") ?></p>
				<textarea name='white_ip_list' id='white-ip-list' class="large-text code" rows="6"><?php echo $white_ip_list ?></textarea>

				<?php wp_nonce_field('access_set', 'wpuf_access_sec') ?>

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
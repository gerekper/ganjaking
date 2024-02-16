<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Code is poetry.';
    exit;
}

function wpuf_plugin_dashboard_page() {

//Register Scripts for Dashboard
wp_register_script( 'wp_admin_custom_scripts', WPUF_URL.'admin/assets/js/wpuf-admin-scripts.js' );
wp_enqueue_script( 'wp_admin_custom_scripts' );

//Default Uptime Robot API
$checkUptimeRobotApiWeb = get_option("wpuf_uptimerobot_api") == '000000000000000000000000000000';

// Uptime Robot api =/ 0x13
if ( !$checkUptimeRobotApiWeb ) {
	$curl = curl_init();

	//Get Status from UptimeRobot
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://api.uptimerobot.com/v2/getMonitors",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "gzip, deflate",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 10,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => "api_key=". get_option("wpuf_uptimerobot_api") . "&format=json&all_time_uptime_ratio=1&custom_uptime_ratios=1-10-20&reason=1&response_times=1&response_times_average=30&alert_contacts=1&logs=1&logs_limit=5",
	  CURLOPT_HTTPHEADER => array(
		"cache-control: no-cache",
		"content-type: application/x-www-form-urlencoded"
	  ),
	));
	 
	$response = curl_exec($curl);
	$uptimeParamet = json_decode($response, true);

	//Check
	$CheckErrorCurl = curl_error($curl);
	$getMonitorStatus = 0;

	//Get Monitor Status
	if ( !$CheckErrorCurl ) {
		$CheckError = $uptimeParamet['stat'];
		if ( $CheckError == 'ok' ) { 
			$getMonitorStatus = $uptimeParamet['monitors'][0]['status'];
		}
		$curlErrorDisplay = 0;
	} else {
		$CheckError = 'down';
		$curlErrorDisplay = 1;
	}

	if ( $CheckError == 'ok' && $getMonitorStatus == 2 && !$CheckErrorCurl)  {
		//Call
		//Get Monitor Uptime Ratio
		$getMonitorUptime = floor($uptimeParamet['monitors'][0]['all_time_uptime_ratio']);

		//Get Custom Uptime Ratios

		//24 hours
		$datatoArray = $uptimeParamet['monitors'][0]['custom_uptime_ratio'];
		$getStatusArray = explode("-", $datatoArray);
		$getStatusArray24Hours = $getStatusArray[0];
		$statusDay = round($getStatusArray24Hours, 2);

		//7 days
		$getStatusArray7Days = $getStatusArray[1];
		$statusWeek = round($getStatusArray7Days, 2);

		//30 days
		$getStatusArray30Days = $getStatusArray[2];
		$statusMonth = round($getStatusArray30Days, 2);

		//Response Status
		$statusResponse = $uptimeParamet['monitors'][0]['status'];
		// endGet Custom Uptime Ratios

		// Get Response Times

		// Get 1 Hour
		$get1Hour = $uptimeParamet['monitors'][0]['response_times'][0]['value'];

		// Get 12 Hours 
		$get1Day = floor($uptimeParamet['monitors'][0]['average_response_time']);

		//end Response Status

		$countCurlArray = count($uptimeParamet['monitors'][0]['logs']);

		//Up or Down
		$getLogUporDown = $uptimeParamet['monitors'][0]['logs'][0]['type'];
		$getLogDate = $uptimeParamet['monitors'][0]['logs'][1]['datetime'];
		$getLogEchoTime = gmdate("Y-m-d", $getLogDate);

		//end Call

	}

	curl_close($curl);

}

//Percents
$percentGetandCheckSec = 0;
$percentGetandCheckOpt = 0;

//Percent Checks - Security
if( get_option("wpuf_header_sec")  == 1 ) {
	$percentGetandCheckSec = $percentGetandCheckSec + 5;
} if( get_option("wpuf_xr_security")  == 1 ) {
	$percentGetandCheckSec = $percentGetandCheckSec + 10;
} if( get_option("wpuf_wpscan_protection")  == 1 ) {
	$percentGetandCheckSec = $percentGetandCheckSec + 5;
} if( get_option("wpuf_spam_attacks")  == 1 ) {
	$percentGetandCheckSec = $percentGetandCheckSec + 10;
} if( get_option("wpuf_content_security")  == 1 ) {
	$percentGetandCheckSec = $percentGetandCheckSec + 15;
} if( get_option("wpuf_proxy_protection")  == 1 || get_option("wpuf_proxy_protection")  == 2 ) {
	$percentGetandCheckSec = $percentGetandCheckSec + 5;
} if( get_option("wpuf_badbot_protection")  == 1 ) {
	$percentGetandCheckSec = $percentGetandCheckSec + 10;
} if( get_option("wpuf_sql_protection")  == 1 ) {
	$percentGetandCheckSec = $percentGetandCheckSec + 10;
} if( get_option("wpuf_fakebot_protection")  == 1 ) {
	$percentGetandCheckSec = $percentGetandCheckSec + 10;
} if( get_option("wpuf_comment_sec_wc")  == 1 || get_option("wpuf_comment_sec_wc")  == 2 ) {
	$percentGetandCheckSec = $percentGetandCheckSec + 10;
} if( get_option("wpuf_recaptcha_protection_lrl")  == 1 ) {
	$percentGetandCheckSec = $percentGetandCheckSec + 10;
}

//Percent Checks - Optimization
if( get_option("wpuf_gzip_comp")  == 1 ) {
	$percentGetandCheckOpt = $percentGetandCheckOpt + 10;
} if( get_option("wpuf_page_minifier")  == 1 ) {
	$percentGetandCheckOpt = $percentGetandCheckOpt + 10;
} if( get_option("wpuf_lazy_load")  == 1 ) {
	$percentGetandCheckOpt = $percentGetandCheckOpt + 10;
} if( get_option("wpuf_disable_emojis")  == 1 ) {
	$percentGetandCheckOpt = $percentGetandCheckOpt + 5;
} if( get_option("wpuf_remove_jquery_migrate")  == 1 ) {
	$percentGetandCheckOpt = $percentGetandCheckOpt + 5;
} if( get_option("wpuf_browser_caching")  == 1 ) {
	$percentGetandCheckOpt = $percentGetandCheckOpt + 10;
} if( get_option("wpuf_headtofooter_opt")  == 1 ) {
	$percentGetandCheckOpt = $percentGetandCheckOpt + 5;
} if( get_option("wpuf_remove_feeds")  == 1 ) {
	$percentGetandCheckOpt = $percentGetandCheckOpt + 5;
} if( get_option("wpuf_remove_query_strings")  == 1 ) {
	$percentGetandCheckOpt = $percentGetandCheckOpt + 10;
} if( get_option("wpuf_author_redirect")  == 1 ) {
	$percentGetandCheckOpt = $percentGetandCheckOpt + 10;
} if( get_option("wpuf_headtofooter_opt")  == 1 ) {
	$percentGetandCheckOpt = $percentGetandCheckOpt + 20;
}

//Status Percent
switch (get_option("wpuf_select_set")) {
	
	case 1:
		$percentGetandCheckCS = 'NaN';
		break;
	case 2:
		$percentGetandCheckCS = 25;
		break;
	case 3:
		$percentGetandCheckCS = 50;
		break;
	case 4:
		$percentGetandCheckCS = 75;
		break;
	case 5:
		$percentGetandCheckCS = 100;
		break;
	default:
		$percentGetandCheckCS = 'NaN';
}

?>
<div class="wrap projectStyle">
		<div id="whiteboxH" class="postbox dWid" style="margin-top:60px;margin-left:56px;">
		
		<div class="topHead">
			<h3><?php echo __("Firewall Dashboard","ua-protection-lang") ?><a style="float:right;margin:0 auto;" class="button activate" target="_blank" href="<?php echo WPUF_URL.'Documentation/index.html'; ?>"><?php echo __("Documentation","ua-protection-lang") ?></a></h3>
		</div>
		
			<div class="inside" style="margin:auto;padding:30px 40px 20px;margin-bottom:-100px">
			<div style="margin: 0 auto;display:table;">
			<div style="display:table; position:relative;left: -15px;top: 72px;">
			<?php if ($percentGetandCheckSec >= 5) { ?>
				<span style="display:inline-block;padding: 10px;" class="statusCheck alert2 statBef"><?php echo __("Status:","ua-protection-lang") ?> </span><span style="display:inline-block;padding: 10px;" class="statusCheckAfter alert2 alert-success"><?php echo __("Active","ua-protection-lang") ?></span>
				<p class="alert2 alert-success"><?php echo __("Website protection is enabled.","ua-protection-lang") ?></p>
			<?php } else { ?>
				<span style="display:inline-block;padding: 10px;" class="statusCheck alert2 statBefAlert"><?php echo __("Status:","ua-protection-lang") ?> </span><span style="display:inline-block;padding: 10px;" class="statusCheckAfter alert2 alert-error"><?php echo __("Inactive","ua-protection-lang") ?></span>
				<p class="alert2 alert-error"><?php echo __("Website protection is disabled.","ua-protection-lang") ?></p>
			<?php } ?>
			</div>
			
			<div style="margin:auto; display:table;position: relative;left: 110px;top: -100px;margin-right:55px;margin-left:90px">
				
				<div class="progress-pie-chart wpufcs" data-cspercent="<?php echo $percentGetandCheckCS; ?>">
				  <!--Pie Chart -->
				  <div class="ppc-progress">
					<div class="ppc-progress-fill wpufcs-fill"></div>
				  </div>
				  <div class="ppc-percents wpufcs-text">
					<div class="pcc-percents-wrapper">
					  <span>%</span>
					</div>
				  </div>					  
				  
				  <div class="ppc-percents2">
				  <?php if(get_option('wpuf_select_set') == 1) { ?><div class="pcc-percents-wrapper2">
					  <span><?php echo __("Custom Settings","ua-protection-lang") ?></span>
					</div><?php
					} elseif (get_option('wpuf_select_set') == 2) { ?><div style="left:10px;" class="pcc-percents-wrapper2">
					  <span><?php echo __("Low Security","ua-protection-lang") ?></span>
					</div><?php
					} elseif (get_option('wpuf_select_set') == 3) { ?><div class="pcc-percents-wrapper2">
					  <span><?php echo __("Medium Security","ua-protection-lang") ?></span>
					</div><?php
					} elseif (get_option('wpuf_select_set') == 4) { ?><div style="left:10px;" class="pcc-percents-wrapper2">
					  <span><?php echo __("High Security","ua-protection-lang") ?></span>
					</div><?php
					} elseif (get_option('wpuf_select_set') == 5) { ?><div style="left:-10px;" class="pcc-percents-wrapper2">
					  <span><?php echo __("I'm Under Attack!","ua-protection-lang") ?></span>
					</div><?php } ?>
				  </div>
				</div>
					
				<div class="progress-pie-chart wpufsec" data-secpercent="<?php echo $percentGetandCheckSec; ?>">
					  <!--Pie Chart -->
					  <div class="ppc-progress">
						<div class="ppc-progress-fill wpufsec-fill"></div>
					  </div>
					  <div class="ppc-percents wpufsec-text">
						<div class="pcc-percents-wrapper">
						  <span>%</span>
						</div>
					  </div>
					<div class="ppc-percents2">
						<div class="pcc-percents-wrapper2" style="left:-4px;">
						  <span><?php echo __("Firewall &amp; Security","ua-protection-lang") ?></span>
						</div>
					  </div>
				</div>			
					
					<div class="progress-pie-chart wpufopt" data-optpercent="<?php echo $percentGetandCheckOpt; ?>">
					  <!--Pie Chart -->
					  <div class="ppc-progress">
						<div class="ppc-progress-fill wpufopt-fill"></div>
					  </div>
					  <div class="ppc-percents wpufopt-text">
						<div class="pcc-percents-wrapper">
						  <span>%</span>
						</div>
					  </div>
					 <div class="ppc-percents2">
						<div class="pcc-percents-wrapper2" style="left:-10px;">
						  <span><?php echo __("Speed Optimization","ua-protection-lang") ?></span>
						</div>
					  </div>
					</div>

				</div>
			</div>
			</div>
		</div>	

	<div id="whiteboxH" class="postbox dWid">
		<div class="topHead"><h3 style="font-size:20px;font-weight:500;"><?php echo __("Uptime Status","ua-protection-lang") ?>
		<?php if ( !$checkUptimeRobotApiWeb) {
				if ( $CheckError == 'ok' ) {
					if ($getMonitorStatus == 2) { ?>
						: <div class="alert2 alert-success" style="display: inline;"><?php echo __("Up","ua-protection-lang") ?></div><?php 
						} else { ?>
						: <div class="alert2 alert-error" style="display: inline;"><?php echo __("Down / Paused","ua-protection-lang") ?></div><?php 
					}
				}
			} ?>
		
			<a style="float:right;margin:0 auto;" class="button activate" href="<?php echo admin_url('admin.php?page=wpuf_admin_settings_page#wpuf_uptimerobot_api'); ?>"><?php echo __("Edit API","ua-protection-lang") ?></a></h3></div>
	<?php if ( !$checkUptimeRobotApiWeb) {
		if ( $CheckError == 'ok' && $getMonitorStatus == 2 && !$CheckErrorCurl)  { ?>		
			<div class="inside" style="margin:auto;padding:30px 40px 20px;margin-bottom:-120px">
			<div style="margin: 0 auto;display:table;">
			<div class="statChartHolder" style="display:table; position:relative;left: -30px;">
				<div class="progress-pie-chart wpufur" data-urpercent="<?php echo $getMonitorUptime; ?>">
				  <!--Pie Chart -->
				  <div class="ppc-progress">
					<div class="ppc-progress-fill wpufur-fill"></div>
				  </div>
				  <div class="ppc-percents">
					<div class="pcc-percents-wrapper wpufur-text">
					  <span>%</span>
					</div>
				  </div>
				</div>
				<!--End Chart -->
				<p class="alltimeAlert"><?php echo __("Total Uptime Ratio","ua-protection-lang") ?></p>
			</div>
				
			<div style="margin:auto;display:table;position: relative;left: 76px;top: -100px;margin-right:80px;margin-left: 170px;margin-bottom: -120px;margin-top: -50px;">
				<div class="statChartHolder statLeft">
					<h3 style="font-weight:400;"><?php echo __("Uptime Status","ua-protection-lang") ?></h3>
					<p class="setMargin"><span class="alert2 statBef" style="display:inline-block"><?php echo __("Last 24 Hours:","ua-protection-lang") ?></span><span class="alert2 alert-success" style="display:inline-block"><?php echo $statusDay; ?>%</span></p>
					<p class="setMargin"><span class="alert2 statBef" style="display:inline-block"><?php echo __("Last 7 Days:","ua-protection-lang") ?></span><span class="alert2 alert-success" style="display:inline-block"><?php echo $statusWeek; ?>%</span></p>
					<p class="setMargin"><span class="alert2 statBef" style="display:inline-block"><?php echo __("Last 30 Days:","ua-protection-lang") ?></span><span class="alert2 alert-success" style="display:inline-block"><?php echo $statusMonth; ?>%</span></p>
				</div>				
				
				<div class="statChartHolder statLeft" style="margin-left:25px;">
					<h3 style="font-weight:400;"><?php echo __("Response Times","ua-protection-lang") ?></h3>
					<p class="setMargin"><span class="alert2 statBef" style="display:inline-block"><?php echo __("Last 1 Hour:","ua-protection-lang") ?></span><span class="alert2 alert-success" style="display:inline-block"><?php echo $get1Hour; ?>ms</span></p>
					<p class="setMargin"><span class="alert2 statBef" style="display:inline-block"><?php echo __("Last 24 Hours:","ua-protection-lang") ?></span><span class="alert2 alert-success" style="display:inline-block"><?php echo $get1Day; ?>ms</span></p>
					<p class="setMargin"><span class="alert2 statBef" style="display:inline-block"><?php echo __("Response Status:","ua-protection-lang") ?></span><span class="alert2 alert-success" style="display:inline-block">
					<?php if ($statusResponse == 0) { echo 'Paused -  0'; 
						} elseif ($statusResponse == 1) { echo 'Not Checked Yet - 1';
						} elseif ($statusResponse == 2) { echo 'Up - 2';
						} elseif ($statusResponse == 8) { echo 'Seems Down - 8';
						} elseif ($statusResponse == 9) { echo 'Down - 9'; } ?></span></p>
				</div>
				
				<div class="statChartHolder statLeft" style="margin-left:25px;">
				<h3 style="font-weight:400;margin-bottom:32px"><?php echo __("Latest Events","ua-protection-lang") ?></h3>
				
				<?php for($countCurlPoint = 0; $countCurlPoint < $countCurlArray; $countCurlPoint++) {
						   $wpufUporDown = $uptimeParamet['monitors'][0]['logs'][$countCurlPoint]['type'];
						   $wpufDateTimeTimeStamp = $uptimeParamet['monitors'][0]['logs'][$countCurlPoint]['datetime'];
						   $wpufDurationTimeStamp = $uptimeParamet['monitors'][0]['logs'][$countCurlPoint]['duration'];

							$wpufDurathours = floor($wpufDurationTimeStamp / 3600);
							$wpufDuratminutes = floor(($wpufDurationTimeStamp / 60) % 60);
							
						   ?>
						<p style="margin:0"><?php if ($wpufUporDown == 1) { ?><span class="alert2 statBefAlert" style="display:inline-block;width:55px;"><?php echo __("DOWN","ua-protection-lang") ?></span><?php 
						} elseif ($wpufUporDown == 2) {?><span class="alert2 statBefAlert" style="display:inline-block;width:55px;"><?php echo __("UP","ua-protection-lang") ?></span><?php
						} elseif ($wpufUporDown == 99) {?><span class="alert2 statBefAlert" style="display:inline-block;width:55px;"><?php echo __("PAUSED","ua-protection-lang") ?></span><?php
						} elseif ($wpufUporDown == 98) {?><span class="alert2 statBefAlert" style="display:inline-block;width:55px;"><?php echo __("STARTED","ua-protection-lang") ?></span><?php } ?><span class="alert2 statBefAlertAfter" style="display:inline-block"><?php echo gmdate("Y-m-d H:i", $wpufDateTimeTimeStamp); ?></span><span title="Hour : Minute" class="alert2 statBefAlert" style="display:inline-block;width:55px;padding:5px;"><?php printf("%02d:%02d", $wpufDurathours, $wpufDuratminutes); ?></span></p>
					<?php
						}
					?>
				</div>
			</div>
			</div>
			</div>
	<?php } elseif ($curlErrorDisplay == 1) { ?>
		<div class="inside"><p class="alert alert-error"><?php echo __("Connection Timeout","ua-protection-lang") ?></p></div>
	<?php }
		}	?>
	</div>	
		
		<div class="dWid">
			<div id="whiteboxH" class="postbox mWid1">
			<div class="topHead" style="padding:-5px 40px;"><h3 style="font-size:20px;font-weight:500;"><?php echo __("Admin Security","ua-protection-lang") ?> <a style="float:right;margin:0 auto;" class="button activate" href="<?php echo admin_url("profile.php"); ?>"><?php echo __("Edit","ua-protection-lang") ?></a></h3></div>
			<div class="inside">
			<?php $current_user = wp_get_current_user(); ?>
			<div style="margin-bottom:15px;"><strong><?php echo __("Current Login:","ua-protection-lang") ?></strong> <?php echo get_current_login(); ?></div>
			<div style="margin-bottom:15px;"><strong><?php echo __("Current Login IP Address:","ua-protection-lang") ?></strong> <?php echo get_current_admin_ip(); ?></div>
			<div style="margin-bottom:15px;"><strong><?php echo __("Last Login:","ua-protection-lang") ?></strong> <?php echo get_last_login(); ?></div>
			<div style="margin-bottom:15px;"><strong><?php echo __("Last Login IP Address:","ua-protection-lang") ?></strong> <?php echo get_last_admin_ip(); ?></div>
			</div>
			</div>		
		
			<div id="whiteboxH" class="postbox mWid2">
			<div class="topHead" style="padding:-5px 40px;"><h3 style="font-size:20px;font-weight:500;"><?php echo __("E-Mail Summary","ua-protection-lang") ?> <a style="float:right;margin:0 auto;" class="button activate" href="<?php echo admin_url("admin.php?page=wpuf_admin_settings_page#wpuf_mail_alarm"); ?>"><?php echo __("Edit","ua-protection-lang") ?></a></h3></div>
			<div class="inside">
			<div style="margin-bottom:15px;"><strong><?php echo __("Username:","ua-protection-lang") ?></strong> <?php echo $current_user->user_login; ?></div>
			<div style="margin-bottom:15px;"><strong><?php echo __("Mail Notifications:","ua-protection-lang") ?></strong> <?php if(get_option('wpuf_mail_alarm') == 1) { ?><?php echo __("Enabled","ua-protection-lang") ?><?php } else { ?><?php echo __("Disabled","ua-protection-lang") ?><?php } ?></div>
			<div style="margin-bottom:15px;"><strong><?php echo __("Current Address:","ua-protection-lang") ?></strong> <?php echo $current_user->user_email; ?></div>
			<?php if(get_option('wpuf_mail_alarm') == 1) { ?><div style="margin-bottom:15px;"><strong><?php echo __("Notification Address:","ua-protection-lang") ?></strong> <?php echo get_option('wpuf_mail_notify'); ?></div><?php } ?>
			</div>
			</div>
		</div>
		
		<div id="whiteboxH" class="postbox dWid">
		<div class="topHead" style="padding:-5px 40px;"><h3 style="font-size:20px;font-weight:500;"><?php echo __("Settings Summary","ua-protection-lang") ?> <a style="float:right;margin:0 auto;" class="button activate" href="<?php echo admin_url("admin.php?page=wpuf_firewall_settings_page"); ?>"><?php echo __("Edit","ua-protection-lang") ?></a></h3></div>
			<div class="inside panelStatic">
			<div style="width:33%">
				<p><strong><?php echo __("Security Level:","ua-protection-lang") ?></strong> <?php if(get_option('wpuf_select_set') == 1) { ?> <?php echo __("Custom Settings","ua-protection-lang") ?> <?php
				} elseif (get_option('wpuf_select_set') == 2) { ?><?php echo __("Low","ua-protection-lang") ?><?php
				} elseif (get_option('wpuf_select_set') == 3) { ?><?php echo __("Medium","ua-protection-lang") ?><?php
				} elseif (get_option('wpuf_select_set') == 4) { ?><?php echo __("High","ua-protection-lang") ?><?php
				} elseif (get_option('wpuf_select_set') == 5) { ?><?php echo __("I'm Under Attack!","ua-protection-lang") ?><?php } ?></p>
				<p><strong><?php echo __("Prevention Method:","ua-protection-lang") ?></strong> <?php if(get_option('wpuf_security_ban') == 1) { ?><?php echo __("Block Access","ua-protection-lang") ?><?php
				} elseif (get_option('wpuf_security_ban') == 2) { ?><?php echo __("reCAPTCHA","ua-protection-lang") ?><?php } ?></p>
			</div>
			<div style="width:33%">
				<p><strong><?php echo __("Access Security:","ua-protection-lang") ?></strong> <?php if(get_option('wpuf_access_security') == 1) { ?><?php echo __("Enabled","ua-protection-lang") ?><?php
				} else { ?><?php echo __("Disabled","ua-protection-lang") ?><?php } ?></p>
				<p><strong><?php echo __("Spam Protection:","ua-protection-lang") ?></strong> <?php if(get_option('wpuf_spam_attacks') == 1) { ?><?php echo __("Enabled","ua-protection-lang") ?><?php
				} else { ?><?php echo __("Disabled","ua-protection-lang") ?><?php } ?></p>
			</div>			
			<div style="width:33%">
				<p><strong><?php echo __("Script Optimization:","ua-protection-lang") ?></strong> <?php if(get_option('wpuf_headtofooter_opt') == 1) { ?><?php echo __("Disabled","ua-protection-lang") ?><?php
				} else { ?><?php echo __("Enabled","ua-protection-lang") ?><?php } ?></p>
				<p><strong><?php echo __("Browser Caching:","ua-protection-lang") ?></strong> <?php if(get_option('wpuf_browser_caching') == 1) { ?><?php echo __("Enabled","ua-protection-lang") ?><?php
				} else { ?><?php echo __("Disabled","ua-protection-lang") ?><?php } ?></p>
			</div>
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
		</div>
		</div>
	</div>

    <?php
}
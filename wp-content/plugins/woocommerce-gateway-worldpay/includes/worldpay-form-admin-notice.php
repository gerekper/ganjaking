<?php
	global $current_user ;
	$user_id = $current_user->ID;
?>
	<div id="worldpay-online">
		<h3><?php _e('Problems with orders not updating or cancelling? Customers not being returned to your site after payment?', 'woocommerce_worlday'); ?></h3>
		<ul>
		<li><a href="https://docs.woocommerce.com/document/worldpay/#section-2" target="_blank"><?php _e("Check your configuration against the setup guide, with screenshots", 'woocommerce_worlday'); ?></a></li>
		<li><a href="https://docs.woocommerce.com/document/worldpay/#section-8" target="_blank"><?php _e("Check the troubleshooting section of the documentation.", 'woocommerce_worlday'); ?></a></li>
		<li><a href="https://docs.woocommerce.com/document/worldpay/#section-12" target="_blank"><?php _e("Create a resultY file to act as a fallback if the standard callback fails.", 'woocommerce_worlday'); ?></a></li>

		<li><strong><a href="https://docs.woocommerce.com/document/worldpay/#section-11" target="_blank"><?php _e("Are you using CloudFlare?", 'woocommerce_worlday'); ?></a></strong></li>
		<li><strong><a href="https://docs.woocommerce.com/document/worldpay/#section-11" target="_blank"><?php _e("I'm getting an 'invalid HTTP status line: >null<' message from Worldpay.", 'woocommerce_worlday'); ?></a></strong></li>

		</ul>
		<p><h4><a href="https://docs.woocommerce.com/document/worldpay/" target="_blank"><?php _e("Read the full Worldpay documentation here", 'woocommerce_worlday'); ?></a></h4></p>
	</div>
<?php
	/* If user clicks to ignore the notice, add that to their user meta */
	if ( isset($_GET['worldpay_settings_ignore']) && '0' == $_GET['worldpay_settings_ignore'] ) {
		update_user_meta( $user_id, 'worldpay_settings_ignore_notice_34', 'true' );
	}

	/* If user clicks to show the notice, add that to their user meta */
	if ( isset($_GET['worldpay_settings_ignore']) && '1' == $_GET['worldpay_settings_ignore'] ) {
		update_user_meta( $user_id, 'worldpay_settings_ignore_notice_34', 'false' );
	}

	?>
	<h3><?php _e('WorldPay Form', 'woocommerce_worlday'); ?></h3>
	<p><?php _e('The WorldPay Form gateway works by sending the user to <a href="http://www.worldpay.com">WorldPay</a> to enter their payment information.', 'woocommerce_worlday'); ?></p>

	<?php if ( (!$this->instId || $this->instId== '' || !$this->callbackPW || $this->callbackPW == '' ) && current_user_can( 'manage_options' ) ) { ?>
		
		<div id="wc_gateway_worldpay_form">
			<p>
			<?php _e('Please complete the settings below, as minimum you need to enter the Installation ID, given to you by WorldPay, and create a Callback Password.', 'woocommerce_worlday'); ?>
			<strong><?php _e('Using an MD5 password is also recommended', 'woocommerce_worlday'); ?></strong>
			</p>

			<p><?php _e('Once you have saved the settings you will be shown a list of values that need to be transferred to your WorldPay account at <a href="http://worldpay.com/uk" target="_blank">http://worldpay.com/uk</a>', 'woocommerce_worlday'); ?></p>

			<p><?php _e('Please refer to the <a href="http://docs.woothemes.com/document/worldpay/" target="_blank">WooCommerce WorldPay docs</a> for more information and a short video', 'woocommerce_worlday'); ?></p>
		</div>

	<?php } ?>
	
	<?php if ( $this->callbackPW && get_user_meta($user_id, 'worldpay_settings_ignore_notice_34', TRUE) != 'true' && current_user_can( 'manage_options' ) ) { ?>
		
		<div id="wc_gateway_worldpay_form">

			<p class="alignright"><a class="submitdelete button-primary" href="<?php echo admin_url('admin.php?page=wc-settings&tab=checkout&section=wc_gateway_worldpay_form&worldpay_settings_ignore=0'); ?>">Hide WorldPay Settings</a></p>
	   	
			<p><?php _e('<strong>These are the settings that should be entered into your WorldPay Installation, <a href="https://secure.worldpay.com/sso/public/auth/login.html?serviceIdentifier=merchantadmin&maiversion=version2" target="_blank">login to WorldPay here.</a></strong>', 'woocommerce_worlday'); ?></p>
			
			<p><?php echo '<strong>Installation ID :</strong>  ' . $this->instId; ?><br />

			<?php if( $this->dynamiccallback ) {
				$url = htmlspecialchars('<wpdisplay item=MC_callback>');
			} else {

				$url = WC()->api_request_url( 'WC_Gateway_Worldpay_Form' );

				if( isset( $this->addgautm ) && $this->addgautm == 'yes' ) {

					// We don't know if the URL already has the parameter so we should remove it just in case
					$url = remove_query_arg( 'utm_nooverride', $url );

					// Now add the utm_nooverride query arg to the URL
					$url = add_query_arg( 'utm_nooverride', '1', $url );

				}
				
			} 

			echo '<strong>Payment Response URL :</strong>  ' . $url;

			?>

	   		<br />
	   		<strong>Payment Response password :</strong> <?php echo $this->callbackPW;?>
	   		<br />
	   		<strong>MD5 secret for transactions :</strong>  <?php echo $this->worldpaymd5;?>
	   		<?php if ( '' != $this->worldpaymd5 ) {
	   			echo '<br /><strong>Signature Fields :</strong> ' . apply_filters( 'woocommerce_worldpay_signature_fields', $this->signaturefields ); 
	   			echo '<br /><i>Please enter this list exactly as it appears here making sure there are no leading or trailing spaces</i>'; 
	   		} ?>
	   		</p>

	   		<p>Once you have finished testing make sure you copy your Test Installation to your Production Installation in your WorldPay Admin 
	   		and change the Status setting to Live in these settings. 
	   		Please refer to the <a href="http://docs.woothemes.com/document/worldpay/" target="_blank">WooCommerce WorldPay docs</a> 
	   		for more information and a short video</p>

	   		</div>

	<?php } elseif ( $this->callbackPW && get_user_meta($user_id, 'worldpay_settings_ignore_notice_34', TRUE) == 'true' && current_user_can( 'manage_options' ) ) { ?>
		
		<div id="wc_gateway_worldpay_form">
			<p><a class="button-primary" href="<?php echo admin_url('admin.php?page=wc-settings&tab=checkout&section=wc_gateway_worldpay_form&worldpay_settings_ignore=1'); ?>">Show WorldPay Settings</a></p>
	   	</div>

	<?php } ?>
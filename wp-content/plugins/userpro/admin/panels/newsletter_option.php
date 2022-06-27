<form method="post" action="">

<?php 
$newsletter_html= '
<h3><i class="userpro-icon-envelope-alt"></i>'.__('MailChimp Settings','userpro').'</h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="mailchimp_api">'.__('MailChimp API Key','userpro').'</label></th>
		<td>
			<input type="text" name="mailchimp_api" id="mailchimp_api" value="'. __(userpro_get_option('mailchimp_api')).'" class="regular-text" />
			<span class="up-description">'.__('Enter your MailChimp API key here to allow integration with MailChimp subscription.','userpro').'</span>
		</td>
	</tr>

</table>
<h3><i class="userpro-icon-envelope-alt"></i>'.__('AWeber Settings','userpro').'</h3>
<table class="form-table">
<tr valign="top">
		<th scope="row"><label for="Aweber_api">'. __('AWeber Authorization Code','userpro').'</label></th>
		<td>
			<input type="text" name="aweber_api" id="authorization_code" value="'.__(userpro_get_option('aweber_api')).'" class="regular-text" />
			<span class="up-description">'. __('Enter your Aweber Authorization Code here to allow integration with Aweber subscription.','userpro').' <a target="_blank" href="https://auth.aweber.com/1.0/oauth/authorize_app/f49b1bcf">Click Here</a> to get.</span>
		  			
		</td>
		</tr>
		<tr valign="top">
		<th scope="row"><label for="Aweber_listid">'. __('AWeber List Id','userpro').'</label></th>
		<td>
			<input type="text" name="aweber_listname" id="aweber_listname" value="'. __(userpro_get_option('aweber_listname')).'" class="regular-text" />
		</td>
	</tr>

</table>
<h3><i class="userpro-icon-envelope-alt"></i>'. __('Campaign Monitor Settings','userpro').'</h3>
<table class="form-table">

	<tr valign="top">
	<th scope="row"><label for="Campaignmonitor_api">'.__('CampaignMonitor Authorization Code','userpro').'</label></th>
		<td>
		<input type="text" name="Campaignmonitor_api" id="Campaignmonitor_code" value="'. __(userpro_get_option('Campaignmonitor_api')).'" class="regular-text" />
		<span class="up-description">'.__('Enter your CampaignMonitor Authorization Code here to allow integration with Campaignmonitor subscription.','userpro').'</span>
	</td>
	</tr>
<tr valign="top">
		<th scope="row"><label for="Campaignmonitor_listname">'.__('CampaignMonitor List Name','userpro').'</label></th>
		<td>
			<input type="text" name="Campaignmonitor_listname" id="Campaignmonitor_listname" value="'.__(userpro_get_option('Campaignmonitor_listname')).'" class="regular-text" />
		</td>
	</tr>
</table>
';
/* Filter for Newsletter Add-ons , Added by Madhulika */
$html='';
$html_add=apply_filters('mailing_list_new_add',$html);
echo $newsletter_html.$html_add;

?>
<!--Global hook for adding extra setting fields   Added by Rahul-->
<?php do_action("userpro_add_setting_fields");?>
<p class="submit">
	<input type="submit" name="submit" id="submit" class="up-admin-btn up-admin-btn--dark-blue small" value="<?php _e('Save Changes','userpro'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="up-admin-btn remove small" value="<?php _e('Reset Options','userpro'); ?>"  />
</p>

</form>

<?php 
define( 'WPL_PLUGIN_PATH', plugin_dir_url('../',__FILE__));?>
<script type="text/javascript">
<?php global $userpro; ?>
	jQuery(document).ready(function(){
           		
		  var html='<form id="wplLoginUserFrm" action="<?php echo $userpro->permalink() ?>" method="get">';
			html+='<input type="hidden" id="wplUsername" name="wplUsername" value=""/>';
			html+='<input type="hidden" id="wplDisplayName" name="wplDisplayName" value=""/>'
			html+='<input type="hidden" id="wplProfilePic" name="wplProfilePic" value=""/>',	
			html+='<input type="hidden" id="wplEmail" name="wplEmail" value=""/>',
			html+='<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('linkedin_auth');?>">'
		  html+='</form>';

	      jQuery('body').append(html);
		jQuery('.wplLiLoginBtn').click(function(){
			wplLoginLinkedIn();
		});
	});
	var wpl_lUserName='';
	var wpl_lUserId='';
	var wpl_lUserEmail='';
	 var wpl_ProfilePic='';
	var wpl_linkedin_auth_window;
	function wplLoginLinkedIn(){
		wpl_linkedin_auth_window=window.open('<?php echo WPL_PLUGIN_PATH;?>userpro/lib/linkedin-auth/linkedinAuth.php?plugin_url=<?php echo WPL_PLUGIN_PATH;?>&k=<?php echo userpro_get_option('linkedin_app_key');?>&s=<?php echo userpro_get_option('linkedin_Secret_Key');?>','name','width=600,height=500');
	}
	function wpl_set_linkedin_data(){
       jQuery('#wplUsername').val(wpl_lUserId);
		jQuery('#wplDisplayName').val(wpl_lUserName);
		jQuery('#wplEmail').val(wpl_lUserEmail);
		jQuery('#wplProfilePic').val(wpl_ProfilePic);
	    jQuery('#wplLoginUserFrm').submit();
	}
</script>

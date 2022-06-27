<?php 
define( 'WPI_PLUGIN_PATH', plugin_dir_url('../',__FILE__));?>
<script type="text/javascript">
<?php global $userpro; ?>
	jQuery(document).ready(function(){
           		
		  var html='<form id="wpInLoginUserFrm" action="<?php echo $userpro->permalink() ?>" method="get">';
			html+='<input type="hidden" id="wpInUsername" name="wpInUsername" value=""/>';
			html+='<input type="hidden" id="wpInDisplayName" name="wpInDisplayName" value=""/>'
				
			html+='<input type="hidden" id="wpInProfilePic" name="wpInProfilePic" value=""/>',
			html+='<input type="hidden" id="wpInBio" name="wpInBio">',
			html+='<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('instagram_auth');?>">'
		  html+='</form>';

	      jQuery('body').append(html);
		jQuery('.wpInLoginBtn').click(function(){
			wpinLoginInstagram();
		});
	});
	var wpin_UserName='';
	var wpin_UserId='';
	var wpin_DisplayName ='';
	var wpin_ProfilePic ='';
	
	var wpin_instagram_auth_window;
	function wpinLoginInstagram(){
		wpin_instagram_auth_window=window.open('<?php echo WPI_PLUGIN_PATH;?>userpro/lib/instagram-auth/instagramAuth.php?plugin_url=<?php echo WPI_PLUGIN_PATH;?>&k=<?php echo userpro_get_option('instagram_app_key');?>&s=<?php echo userpro_get_option('instagram_Secret_Key');?>','name','width=600,height=500');
	}
	function wpin_set_instagram_data(){
        jQuery('#wpInUsername').val(wpin_UserName);
		jQuery('#wpInDisplayName').val(wpin_DisplayName);
		jQuery('#wpInProfilePic').val(wpin_ProfilePic);
	    jQuery('#wpInLoginUserFrm').submit();
	}
</script>

<?php global $userpro;
	 $Personal_user_id=$args['user_id'];

	$i = (isset($i)) ? $i : '';
	$layout = (isset($layout)) ? $layout : '';
        $url=home_url(add_query_arg( NULL, NULL ) );
?>
<div class="title" id="walltitle"><?php echo userpro_userwall_get_option( 'personalwall_title' );; ?></div>
<?php if (empty($userrole) || in_array($current_user_role,$allow_userrole) ) {?>

<div class="large-12 block columns">
<input type="hidden" name="sw_personalwall" id="sw_personalwall" value="sw_personalwall">
<p style="margin-bottom:20px !important;">Use the form below to leave your favorite memory.</p>
<?php if(!is_user_logged_in()){?>
<div class=''>
	<input type="text" placeholder="Enter Full Name" id="user_full_name" name="user_full_name">
</div>
<?php }?>
	<div class="textarea">
	  <div contentEditable="true" data-text="<?php _e('Leave your memory.','userpro-userwall');?>" id="userpost" class="userpost" style="border: 1px solid #ccc;border-radius: 0px;width: 100%; height: 72px;overflow: hidden;"></div>
	    <div class="smilies" id="smilies">
		<a href="#" title="" data-smiley="smiley1"><img alt="" border="0" src="<?php echo UPS_PLUGIN_URL.'images/smiley/smiley1.png'?>" /></a>
		<a href="#" title="" data-smiley="smiley2"><img alt="" border="0" src="<?php echo UPS_PLUGIN_URL.'images/smiley/smiley2.png'?>" /></a>
		<a href="#" title="" data-smiley="smiley3"><img alt="" border="0" src="<?php echo UPS_PLUGIN_URL.'images/smiley/smiley3.png'?>" /></a>
		<a href="#" title="" data-smiley="smiley4"><img alt="" border="0" src="<?php echo UPS_PLUGIN_URL.'images/smiley/smiley4.png'?>" /></a>
		<a href="#" title="" data-smiley="smiley5"><img alt="" border="0" src="<?php echo UPS_PLUGIN_URL.'images/smiley/smiley5.png'?>" /></a>
		<a href="#" title="" data-smiley="smiley6"><img alt="" border="0" src="<?php echo UPS_PLUGIN_URL.'images/smiley/smiley6.png'?>" /></a>
	</div>

	</div>

<?php if(!is_user_logged_in()){
		$rand1 = rand(1, 10);
		$rand2 = rand(1, 10);
		$res .= "<div style='font-size: 14px; margin-bottom: 6px;'>".sprintf(__('How much is %s + %s ?','userpro'), $rand1, $rand2)."</div>";
		$res .= "<input type='text' name='antispam-answer' id='antispam-answer' placeholder='Answer'/>";
		$res .= "<input type='hidden' name='antispam-total' id='antispam-total' value='".($rand1 + $rand2)."' />";
		echo $res;
?>


<?php }?>
	<div class="buttonpost"><button type="submit"  name="Post_Now" value="Post Now" title="<?php _e('Add to Wall','userpro-userwall'); ?>" onclick="user_post_data('userpost',<?php echo get_current_user_id();?>,<?php echo $Personal_user_id;?>,'personal');"><i class="fa fa-send fa-fw"></i><b><?php _e('Add to Wall','userpro-userwall');?></b></button></div>
	<?php if(userpro_userwall_get_option('allow_mediabutton')=='1') { ?>
	<div class="upload" style="margin-top:0"><button id='frontend-button' class="userwall_upload"  data-filetype = 'photo' type="submit"  name="upload_image" value="upload" data-allowed_extensions=jpg,png,jpeg,gif,mp4,mkv,avi title="<?php _e('Upload','userpro-userwall'); ?>"><i class="fa fa-image fa-fw"></i> <b><?php _e('Add Media','userpro-userwall');?></b></button>
	
<!--        <input class='button userwall_upload' name="upload_image" data-posttype="<?php echo $posttype;?>" data-filetype = 'photo' data-allowed_extensions=jpg,png,jpeg,gif,mp4,mkv,avi data-url="<?php echo $url;?>" id="frontend-button" type="button" value="<?php _e('Upload','userpro-userwall'); ?>" >-->
        </div><?php } ?>
	<br>

</div>

<?php }?>


<div id="userwalldata">
<?php
$args = array(
	'posts_per_page'   => 100,
	'order'            => 'DESC',
	'include'          => '',
	'exclude'          => '',
	'meta_key'         => '',
	'meta_value'       => '',
	'post_type'        => 'userpro_userwall',
	'post_mime_type'   => '',
	'post_parent'      => '',
	'post_status'      => 'publish',
	'suppress_filters' => true );

$postslist = get_posts( $args );
foreach($postslist as $post)
{

	global $userpro;

$postids=get_user_meta($Personal_user_id,'userids',true );
if(!is_array($postids)) $postids = array();

if(in_array($post->ID,$postids))
{
		global $userpro;
		$user_id = get_post_meta( $post->ID,'user_id');

		include UPS_PLUGIN_DIR.'templates/personalwall-single-post.php';
?>



<?php }} ?>

  </div>
<div class="clear"></div>

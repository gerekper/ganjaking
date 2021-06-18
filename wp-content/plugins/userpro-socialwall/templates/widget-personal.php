<div class="updb-widget-style">

	<div class="updb-basic-info"><?php _e( 'Personal Wall', 'userpro-socialwall' );?></div>
<div class="personalwall-main updb-view-profile-details"><br>
		
<!-- Start Personal Wall Template -->
<?php global $userpro;
 $Personal_user_id=$user_id;
 if (get_current_user_id()) {?>

<div class="large-12 block columns">

	<div class="textarea">
	  <div contentEditable="true" data-text="<?php _e('Leave your memory.','userpro-userwall');?>" id="userpost" class="userpost" style="border: 1px solid #ccc;width:auto; height: 72px;overflow: hidden;"></div>
	    <div class="smilies" id="smilies">
		<a href="#" title="" data-smiley="smiley1"><img alt="" border="0" src="<?php echo UPS_PLUGIN_URL.'images/smiley/smiley1.png'?>" /></a>
		<a href="#" title="" data-smiley="smiley2"><img alt="" border="0" src="<?php echo UPS_PLUGIN_URL.'images/smiley/smiley2.png'?>" /></a>
		<a href="#" title="" data-smiley="smiley3"><img alt="" border="0" src="<?php echo UPS_PLUGIN_URL.'images/smiley/smiley3.png'?>" /></a>
		<a href="#" title="" data-smiley="smiley4"><img alt="" border="0" src="<?php echo UPS_PLUGIN_URL.'images/smiley/smiley4.png'?>" /></a>
		<a href="#" title="" data-smiley="smiley5"><img alt="" border="0" src="<?php echo UPS_PLUGIN_URL.'images/smiley/smiley5.png'?>" /></a>
		<a href="#" title="" data-smiley="smiley6"><img alt="" border="0" src="<?php echo UPS_PLUGIN_URL.'images/smiley/smiley6.png'?>" /></a>
	    <div class="share_button">	<input type="button" class="userpro-button"  name="Post_Now" value="<?php _e('Share','userpro-userwall');?>" title="<?php _e('Add to Wall','userpro-userwall'); ?>" onclick="user_post_data('userpost',<?php echo get_current_user_id();?>,<?php echo $Personal_user_id;?> , 'personal' );">
	
	</div>
	
	</div>

	<br></div>

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
?>



	<div class="" id=<?php echo $post->ID?>>


	<?php if ( userpro_can_edit_user( $user_id ) ) {?>
<div class="userwall_delete_post">
<?php

	$report_userid=get_post_meta($post->ID,'socialwall_report',true );
    if(!is_array($report_userid)) $report_userid = array();
	if(!in_array(get_current_user_id(), $report_userid)) {?>
<i onclick="userwall_report_post(<?php echo $post->ID;?> ,<?php echo get_current_user_id();?>);" class="reportpost fa fa-exclamation-circle"></i>
<?php } else { ?>
	<i style="color:black;opacity: 0.5;cursor:default;" class="fa fa-exclamation-circle"></i>
<?php } ?>

	<i onclick="userwall_delete_post(<?php echo $post->ID;?> , this);" class="fa fa-trash fa-fw"></i>


</div>
<?php } ?>



<div  class="userwall-post-content-img"><a  href="<?php echo $userpro->permalink($user_id[0]); ?>" title="<?php _e('View Profile','userpro'); ?>" ><?php echo get_avatar( $user_id[0], "60" );  ?> </a>
</div>
<div class="userwall-post-content" id=userwall-post-content<?php echo $post->ID?>>

<!-- Display name and Post Date -->

<div class="displayname"><a href="<?php echo $userpro->permalink($user_id[0]); ?>"><?php echo userpro_profile_data('display_name', $user_id[0]); ?></a>
  <div class="clear"></div>
				<?php $timestamp = strtotime($post->post_date);
$new_date = date('F dS Y', $timestamp);?>
  <div class="post-date"><?php _e("","userpro-userwall");echo $new_date;?></div>
</div>


<div class="clear"></div>

<?php
echo html_entity_decode($post->post_content);?>
</div>


<div class="userwall-comment-data-<?php echo $post->ID;?>" id="userwall-comment" >
<?php
 $like_post=array();
 $dislike_post=array();

$like_post=get_post_meta($post->ID,'socialwall_likes',true);
$dislike_post=get_post_meta($post->ID,'socialwall_dislikes',true);
if(!is_array($like_post)) $like_post = array();
if(!is_array($dislike_post)) $dislike_post = array();
if (!in_array(get_current_user_id(), $like_post) &&  !in_array(get_current_user_id(), $dislike_post) && is_user_logged_in())
{ ?>

	<div class="userwall_postlikecount_post" id=userwall_postlikecount_post<?php echo $post->ID?>>
	<i onclick="userwall_postlikecount_post(<?php echo $post->ID;?>,<?php echo get_current_user_id()?>,<?php if(empty($like_post)) echo 0; else echo  count($like_post);?>,<?php if (empty($dislike_post)) echo 0; else echo count($dislike_post);?>);" class="userpro-icon-thumbsup socialwall_postlikecount_post btn-like-dislike"></i> <i class="socialwall_postlikecount_post "><?php if(empty($like_post)) echo 0; else echo  count($like_post);?></i>
	<i onclick="userwall_postdislikecount_post(<?php echo $post->ID;?>,<?php echo get_current_user_id()?>,<?php if (empty($dislike_post)) echo 0; else echo count($dislike_post);?>,<?php if(empty($like_post)) echo 0; else echo  count($like_post);?>);" class="userpro-icon-thumbsdown socialwall_postlikecount_post btn-like-dislike"></i><i class="socialwall_postlikecount_post "><?php if (empty($dislike_post)) echo 0; else echo count($dislike_post);?></i>
	</div>

<?php }
else
{
?>
<?php
echo '<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsup"></i>'; ?>
   <i class="socialwall_postlikecount_post "><?php if(empty($like_post)) echo 0; else echo  count($like_post);?></i>
<?php
echo '<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsdown"></i>';
?>
  <i class="socialwall_postlikecount_post "><?php if (empty($dislike_post)) echo 0; else echo count($dislike_post);?></i>
<?php
}

?>
</div>


<?php
global $wpdb;
$results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s", $post->ID, 'user_comment') );

$i=0;
foreach($results as $result)
{
	$comment = unserialize($result->meta_value);
?>


<div class="personalwall_comment" id="<?php echo ++$i ?>">

<?php  if($comment['comment_userid']==get_current_user_id() ||  is_super_admin(get_current_user_id())) { ?>

        <div class=userwall_delete_comment>
		<i id="delete_comment_<?php echo $post->ID; ?>" onclick="userwall_delete_comment('<?php echo $post->ID;?>','<?php echo $comment['comment_content'];?>',this);" class="fa fa-trash fa-fw-3"></i></div>
<?php }?>
        
        <div class="userwall-comment-content-img" data-key="profilepicture"><a  href="<?php echo $userpro->permalink($comment['comment_userid']); ?>" title="<?php _e('View Profile','userpro'); ?>" ><?php echo get_avatar( $comment['comment_userid'], "40" );  ?> </a></div>
        <div class="userwall-post-content"><div class="displayname"><a href="<?php echo $userpro->permalink($comment['comment_userid']); ?>"><?php echo userpro_profile_data('display_name', $comment['comment_userid']); ?></a></div>
<?php
	$flag = 0;
	if( isset($comment['actioned_by'] ))
	{
		$comment_actioned_by = explode(',', $comment['actioned_by']);
		$flag = in_array(get_current_user_id(), $comment_actioned_by);
	}
	$commenttimestamp = strtotime($comment['comment_date']);
	$commenttime = date('d-M-Y', $commenttimestamp);?>
	<div class="post-date "><?php echo "Commented On ".$commenttime;?></div><?php
	echo "<br><div style='clear:both;'><p>".$comment['comment_content']."</p></div>";
?>

<div class="userwall_commentlikecount_comment" id=userwall_commentlikecount_comment<?php echo $result->meta_id?>>
<?php if( $flag ){ 
echo '<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsup"></i>'; ?>
    <i class="socialwall_postlikecount_post "><?php if(!isset($comment['like'])) echo 0; else echo  $comment['like'];?></i>
<?php
echo '<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsdown"></i>';
?>
    <i class="socialwall_postlikecount_post "><?php if (!isset($comment['dislike'])) echo 0; else echo $comment['dislike'];?></i>
<?php
} else{ ?>
    <i onclick="userwall_commentlikedislikecount_comment(<?php echo $result->meta_id;?>,<?php echo get_current_user_id()?>,<?php  if(!isset($comment['like'])) echo 0; else echo  $comment['like']?>,<?php if (!isset($comment['dislike'])) echo 0; else echo $comment['dislike'];?>,'like');"  class="socialwall_postlikecount_post userpro-icon-thumbsup btn-like-dislike"></i><i class="socialwall_postlikecount_post " id="comment_like_count"><?php if(!isset($comment['like'])) echo 0; else echo  $comment['like'];?></i>
 
<?php if( $flag ){
 	echo '<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsdown"></i>';
  }
 ?>
    <i onclick="userwall_commentlikedislikecount_comment(<?php echo $result->meta_id;?>,<?php echo get_current_user_id()?>,<?php  if (!isset($comment['like'])) echo 0; else echo $comment['like'];?>,<?php if(!isset($comment['dislike'])) echo 0; else echo  $comment['dislike'];?>,'dislike');"  class="socialwall_postlikecount_post userpro-icon-thumbsdown btn-like-dislike"></i><i class="socialwall_postlikecount_post" id="comment_dislike_count"><?php if (!isset($comment['dislike'])) echo 0; else echo $comment['dislike'];?></i>
<?php }?>

</div>
</div>
</div>



<?php  } ?>

<div id="personalwall-comment">
    <?php if ( is_user_logged_in() ) {?>
  <textarea id=userwall-comment-<?php echo $post->ID;?> placeholder="<?php _e('Enter comment and Hit ENTER to submit...','userpro-userwall');?>"  onkeypress="user_post_comment(this,'userwall-comment',<?php echo get_current_user_id();?>,<?php  echo $post->ID;?>,event);" name="userwall-comment" cols="30" rows="1" style="width:95%;height:63px;" ></textarea>
    <?php }?>
   </div>

</div> 
<?php }} ?>
  
</div>



</div></div>
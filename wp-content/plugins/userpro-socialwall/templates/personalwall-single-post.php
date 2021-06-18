<?php
	$following=0;
	$array = get_user_meta($post->post_author,'_userpro_followers_ids');
	if(isset($array['0']))
	{
		foreach($array['0'] as  $key => $val)
		{
			if(get_current_user_id()==$key )
			{
				$following=1;
			}
		}
	}
	if(get_current_user_id()==$post->post_author || is_super_admin(get_current_user_id()) || userpro_userwall_get_option('followerspost')=='0')
	 $following=1;
	$user_info=array();
	$user_info = get_userdata($post->post_author);


if(($following==1 && empty($args['role'])) || $args['role']==implode(', ', $user_info->roles))
{
	global $userpro;
	$user_id = get_post_meta( $post->ID,'user_id');

?>
<div class="userwall" id=<?php echo $post->ID?>>

<div class="userwall_delete_post">

<?php if($user_id[0]==get_current_user_id() ||  is_super_admin(get_current_user_id())) {?>
<i onclick="userwall_delete_post(<?php echo $post->ID;?> , this);" class="fa fa-trash fa-fw"></i>
<?php }?></div>
<div class="userwall-post-content-img" data-key="profilepicture"><a  href="<?php echo $userpro->permalink($user_id[0]); ?>" title="<?php _e('View Profile','userpro'); ?>" ><?php echo get_avatar( $user_id[0], "60" );  ?> </a></div>

<div class="userwall-post-content" id=userwall-post-content<?php echo $post->ID?>>
				<div class="displayname"><a href="<?php echo $userpro->permalink($user_id[0]); ?>"><?php echo userpro_profile_data('display_name', $user_id[0]); ?></a>
				</div>
				<br><div class="personalwall-content-text content-text">
<?php

if($post->post_title=="imgpost"){
	$photo_desc = get_post_meta($post->ID , 'userwall_photo_desc' , true);
	echo '<div class="post-img"><img src="'.$post->post_content.'" style="-moz-user-select: none;
    border-radius: 10px;max-width:99%; width: 100% !important;">
 	<div>'.$photo_desc.'</div>
 	</div>';
	echo userpro_socialwall_sharebutton($url.'#'.$post->ID);
}
else if($post->post_title=="vidpost"){
	$photo_desc = get_post_meta($post->ID , 'userwall_photo_desc' , true);
	echo '<div class="post-video">
		<video style="-moz-user-select: none;
	    border-radius: 10px;max-width:89%; width: 90% !important;"controls>
  		<source src="'.$post->post_content.'" >
		</video>
 	<div>'.$photo_desc.'</div>
 	</div>';
	echo userpro_socialwall_sharebutton($url.'#'.$post->ID);
}
else
{
	echo html_entity_decode(make_clickable($post->post_content));
	echo userpro_socialwall_sharebutton($url.'#'.$post->ID);
}
?>
</div>
</div>
<div class="personalwall-userwall-comment userwall-comment-data-<?php echo $post->ID;?>" id="userwall-comment" >
<?php
 $like_post=array();
 $dislike_post=array();

$like_post=get_post_meta($post->ID,'socialwall_likes',true);
$dislike_post=get_post_meta($post->ID,'socialwall_dislikes',true);
if(!is_array($like_post)) $like_post = array();
if(!is_array($dislike_post)) $dislike_post = array();
$timestamp = strtotime($post->post_date);
$new_date = date('d-M-Y h:i', $timestamp);
$ups_date = date('d-M-Y', $timestamp);
if (!in_array(get_current_user_id(), $like_post) &&  !in_array(get_current_user_id(), $dislike_post) && is_user_logged_in())
{
?>
    <?php $report_userid=get_post_meta($post->ID,'socialwall_report',true );
if(!is_array($report_userid)) $report_userid = array();

?>
<div class="userwall_postlikecount_post" id=userwall_postlikecount_post<?php echo $post->ID?>>
<i onclick="userwall_postlikecount_post(<?php echo $post->ID;?>,<?php echo get_current_user_id()?>,<?php if(empty($like_post)) echo 0; else echo  count($like_post);?>,<?php if (empty($dislike_post)) echo 0; else echo count($dislike_post);?>,<?php if(empty($timestamp)) echo ''; else echo  $timestamp;?>);" class="userpro-icon-thumbsup socialwall_postlikecount_post btn-like-dislike"></i> <i class="socialwall_postlikecount_post "><?php if(empty($like_post)) echo 0; else echo  count($like_post);?></i>
<i onclick="userwall_postdislikecount_post(<?php echo $post->ID;?>,<?php echo get_current_user_id()?>,<?php if (empty($dislike_post)) echo 0; else echo count($dislike_post);?>,<?php if(empty($like_post)) echo 0; else echo  count($like_post);?>,<?php if(empty($timestamp)) echo ''; else echo  $timestamp;?>);" class="userpro-icon-thumbsdown socialwall_postlikecount_post btn-like-dislike"></i><i class="socialwall_postlikecount_post "><?php if (empty($dislike_post)) echo 0; else echo count($dislike_post);?></i>
<?php if(!in_array(get_current_user_id(), $report_userid)) {?>
<i onclick="userwall_report_post(<?php echo $post->ID;?> ,<?php echo get_current_user_id();?>);" class="reportpost fa fa-exclamation-circle"></i>
<?php } else{?>
<i style="color:black;opacity: 0.5;cursor:default;margin-left:12px;" class="fa fa-exclamation-circle"></i>
<?php } ?>
<span class="post-date"><?php echo $new_date;?></span>
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

<?php $report_userid=get_post_meta($post->ID,'socialwall_report',true );
if(!is_array($report_userid)) $report_userid = array();
	if(!in_array(get_current_user_id(), $report_userid)) {?>
<i onclick="userwall_report_post(<?php echo $post->ID;?> ,<?php echo get_current_user_id();?>);" class="reportpost fa fa-exclamation-circle"></i>
<?php } else{?>
<i style="color:black;opacity: 0.5;cursor:default;margin-left:12px;" class="fa fa-exclamation-circle"></i>
<?php }?>
<span class="commented-date-main-post"><?php echo $new_date;?></span>
<?php
}
?>
</div>
<div class="personalwall-comment-container userwall-comment-container">
<?php
global $wpdb;
$results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s", $post->ID, 'user_comment') );
$comment_count=count_comment($post->ID);
$i=0;
foreach($results as $result)
{
	$meta_id = $result->meta_id;
	$comment = unserialize( $result->meta_value);
	include UPS_PLUGIN_DIR.'templates/single-comment.php';
}
?>
<div class="commenttext">
<div class="clear"></div>
<?php
		$limit_comment=userpro_userwall_get_option('limit_number_of_comment');

		if(isset($args['role']))
			$args['role']=$args['role'];
		else
		$args['role']='';
	if ( (is_user_logged_in() && $comment_count<$limit_comment && empty($args['role'] ))  || $args['role']==$current_user_role) {?>
<textarea id=userwall-comment-<?php echo $post->ID;?> placeholder="<?php _e('Write a comment...','userpro-userwall');?>"  onkeypress="user_post_comment(this,'userwall-comment',<?php echo get_current_user_id();?>,<?php  echo $post->ID;?>,event,<?php echo count_comment($post->ID);?>,<?php  echo $limit_comment;?>);" name="userwall-comment"cols="40" rows="1" class="comment_textarea"></textarea>
<?php }?>
</div>
</div>
<?php
}?>
</div>

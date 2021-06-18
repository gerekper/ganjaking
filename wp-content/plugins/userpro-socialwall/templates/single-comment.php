<div class="userwall_comment_data" id="<?php echo ++$i ?>"><?php

if($comment['comment_userid']==get_current_user_id() ||  is_super_admin(get_current_user_id())) {
		$sw_onclick = "userwall_delete_comment('".$post->ID."',".$meta_id.",this);";
		?><div class=userwall_delete_comment>
		<i id="delete_comment_<?php echo $post->ID; ?>" onclick="<?php echo $sw_onclick; ?>" class="fa fa-trash fa-fw-3"></i></div>
	<?php
	}?><div class="userwall-comment-content-img" data-key="profilepicture"><a  href="<?php echo $userpro->permalink($comment['comment_userid']); ?>" title="<?php _e('View Profile','userpro'); ?>" ><?php echo get_avatar( $comment['comment_userid'], "40" );  ?> </a></div> 
	<div class="userpro-comment-post-content userwall-post-content"><div class="displayname"><a href="<?php echo $userpro->permalink($comment['comment_userid']); ?>"><?php echo userpro_profile_data('display_name', $comment['comment_userid']); ?></a></div>
	<?php 
	   $flag = 0;
	if( isset($comment['actioned_by'] ))
	{
		$comment_actioned_by = explode(',', $comment['actioned_by']);
		$flag = in_array(get_current_user_id(), $comment_actioned_by);
	}	
	$commenttimestamp = strtotime($comment['comment_date']);
	$commenttime = date('d-M-Y H:i', $commenttimestamp);?>
	<?php
	echo "<br><p>".stripslashes(make_clickable($comment['comment_content']))."</p>";
	?>
<div class="userpro-comment-meta userwall_commentlikecount_comment" id=userwall_commentlikecount_comment<?php echo $meta_id?>>
<?php 
  if( $flag ){
    ?>
<?php
echo '<i style="color:black;opacity: 0.5;" class="upc-thumb socialwall_postlikecount_post userpro-icon-thumbsup"></i>'; ?>
<i class="socialwall_postlikecount_post "><?php if(!isset($comment['like'])) echo 0; else echo  $comment['like'];?></i>
<?php 
echo '<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsdown"></i>';
?>
<i class="socialwall_postlikecount_post "><?php if (!isset($comment['dislike'])) echo 0; else echo $comment['dislike'];?></i>

<?php 
  }
  else{
?>
<i onclick="userwall_commentlikedislikecount_comment(<?php echo $meta_id;?>,<?php echo get_current_user_id()?>,<?php  if(!isset($comment['like'])) echo 0; else echo  $comment['like']?>,<?php if (!isset($comment['dislike'])) echo 0; else echo $comment['dislike'];?>,'like',<?php echo $post->ID;?>,<?php if (!isset($commenttimestamp)) echo 0; else echo $commenttimestamp;?>);"  class="socialwall_postlikecount_post userpro-icon-thumbsup btn-like-dislike"></i><i class="socialwall_postlikecount_post " id="comment_like_count"><?php if(!isset($comment['like'])) echo 0; else echo  $comment['like'];?></i>
 <?php if( $flag ){
 	echo '<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsdown"></i>';
  }
 ?>
<i onclick="userwall_commentlikedislikecount_comment(<?php echo $meta_id;?>,<?php echo get_current_user_id()?>,<?php  if (!isset($comment['like'])) echo 0; else echo $comment['like'];?>,<?php if(!isset($comment['dislike'])) echo 0; else echo  $comment['dislike'];?>,'dislike',<?php echo $post->ID;?>,<?php if (!isset($commenttimestamp)) echo 0; else echo $commenttimestamp;?>);"  class="socialwall_postlikecount_post userpro-icon-thumbsdown btn-like-dislike"></i><i class="socialwall_postlikecount_post" id="comment_dislike_count"><?php if (!isset($comment['dislike'])) echo 0; else echo $comment['dislike'];?></i>
<?php }?>
<span class="commented-date"><?php echo $commenttime;?></span></div>
	</div>
</div>
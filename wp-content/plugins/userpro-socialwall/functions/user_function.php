<?php

function userpro_socialwall_sharebutton($url)
{

	if(userpro_userwall_get_option('display_socialbutton')=='1')
	{
		$html='';

		$html.='<br><div class="a2a_kit a2a_default_style" data-a2a-url="'.$url.'">';
		$html.='<a class="a2a_button_facebook"></a>';
		$html.='<a class="a2a_button_twitter"></a>';
		$html.='<a class="a2a_button_google_plus"></a>';
		$html.='<a class="a2a_button_linkedin"></a>';

		$html.="</div>";


		return $html;
	}
}


function userspost($userid)
{
	global $wpdb;


	$post_count = $wpdb->get_results("SELECT id FROM $wpdb->posts WHERE post_type = 'userpro_userwall' AND post_status = 'publish'  AND post_author=$userid");

	$result=count($post_count);
	return $result;
}


function count_comment($id)
{
	global $wpdb;
	$comment_count = $wpdb->get_results("
                SELECT post_id FROM $wpdb->postmeta
                WHERE post_id = $id AND meta_key ='user_comment'");

	return count($comment_count);

}
add_action('wp_ajax_nopriv_countpost', 'countpost');
add_action('wp_ajax_countpost', 'countpost');
function countpost()
{
	global $wpdb;
	$formdate=$_POST['formdate'];
	$todate=$_POST['todate'];

	$post_count = $wpdb->get_results("SELECT id FROM $wpdb->posts WHERE post_type = 'userpro_userwall' AND post_status = 'publish'  AND post_date BETWEEN '$formdate '
AND '$todate'");

	echo count($post_count);
	die();
}


add_action('wp_ajax_socialwall_delete_post_by_date', 'socialwall_delete_post_by_date');
function socialwall_delete_post_by_date()
{
	global $wpdb;

	$formdate=$_POST['formdate'];
	$todate=$_POST['todate'];

	$post_count = $wpdb->get_results("SELECT id FROM $wpdb->posts WHERE post_type = 'userpro_userwall' AND post_status = 'publish'  AND post_date BETWEEN '$formdate'
AND '$todate'");

	if(!empty($post_count))
	{

		foreach ($post_count as $count)
		{
			$list = (array)get_option('sw_reportpostid');
			$key = array_search($count->id, $list);
			unset($list[$key]);
			update_option('sw_reportpostid',$list);
			wp_delete_post($count->id);
		}
		echo count($post_count);
		die();

	}
	else
	{
		echo "notfound";
		die();
	}

}

add_action('wp_ajax_nopriv_socialwall_ignore_post', 'socialwall_ignore_post');
add_action('wp_ajax_socialwall_ignore_post', 'socialwall_ignore_post');
function socialwall_ignore_post()
{
	$list = (array)get_option('sw_reportpostid');
	$key = array_search($_POST['post_id'], $list);
	unset($list[$key]);
	update_option('sw_reportpostid',$list);

}


add_action('wp_ajax_nopriv_socialwall_report_post', 'socialwall_report_post');
add_action('wp_ajax_socialwall_report_post', 'socialwall_report_post');
function socialwall_report_post()
{

	$report_post=get_post_meta($_POST['post_id'],'socialwall_report',true );


	$report_post_id=get_option('sw_reportpostid');
	if(is_array($report_post_id))
	{
		array_push($report_post_id,$_POST['post_id']);
	}
	else
	{
		$report_post_id=array($_POST['post_id']);
	}

	$post_id=get_option('sw_reportpostid');
	if(!in_array($_POST['post_id'], (array)$post_id))
	update_option("sw_reportpostid",$report_post_id);

	if(is_array($report_post))
	{
		array_push($report_post,$_POST['userid']);
	}
	else
	{
		$report_post=array($_POST['userid']);
	}
	$report_userid=get_post_meta($_POST['post_id'],'socialwall_report',true );
	if(!is_array($report_userid)) $report_userid = array();
	if(!in_array($_POST['userid'], $report_userid))
	update_post_meta($_POST['post_id'],"socialwall_report", $report_post);

}


add_action('wp_ajax_nopriv_socialwall_dislikecount_post', 'socialwall_dislikecount_post');
add_action('wp_ajax_socialwall_dislikecount_post', 'socialwall_dislikecount_post');

function socialwall_dislikecount_post()
{
	$dislike_post=get_post_meta($_POST['post_id'],'socialwall_dislikes',true );


	if(is_array($dislike_post))
	{
		array_push($dislike_post,$_POST['userid']);
	}
	else
	{
		$dislike_post=array($_POST['userid']);
	}
	update_post_meta($_POST['post_id'],"socialwall_dislikes", $dislike_post);



	$postdata = get_post( $_POST['post_id']);
	$author=$postdata->post_author;
	if(userpro_userwall_get_option('send_email_on_post_likedis')=="1" && $author!=$_POST['userid'])
	mail_user($author,$_POST['userid'],"postdislike");
        $report_userid=get_post_meta($_POST['post_id'],'socialwall_report',true );
        if(!is_array($report_userid)) $report_userid = array();
        if(!in_array(get_current_user_id(), $report_userid)) {
            echo 'yes' ;
        } else{
            echo 'No';
        }
        die();
}


add_action('wp_ajax_nopriv_socialwall_count_posts_like', 'socialwall_count_posts_like');
add_action('wp_ajax_socialwall_count_posts_like', 'socialwall_count_posts_like');
function socialwall_count_posts_like()
{

	$like_post=get_post_meta($_POST['post_id'],'socialwall_likes',true );

	if(is_array($like_post))
	{
		array_push($like_post,$_POST['userid']);
	}
	else
	{
		$like_post=array($_POST['userid']);
	}


	update_post_meta($_POST['post_id'],"socialwall_likes", $like_post);
	$postdata = get_post( $_POST['post_id']);
	$author=$postdata->post_author;
	if(userpro_userwall_get_option('send_email_on_post_likedis')=="1" && $author!=$_POST['userid'])
	mail_user($author,$_POST['userid'],"postlike");
        $report_userid=get_post_meta($_POST['post_id'],'socialwall_report',true );
        if(!is_array($report_userid)) $report_userid = array();
        if(!in_array(get_current_user_id(), $report_userid)) {
            echo 'yes' ;
        } else{
            echo 'No';
        }
        die();
}


add_action( 'wp_ajax_socialwall_comment_like_dislike' , 'socialwall_comment_like_dislike' );
add_action( 'wp_ajax_nopriv_socialwall_comment_like_dislike' , 'socialwall_comment_like_dislike' );

function socialwall_comment_like_dislike()
{


	global $wpdb;
	$user_id = esc_attr( $_POST['userid'] );
	$postid = esc_attr( $_POST['postid'] );
	$action = esc_attr( $_POST['request'] );
	$meta_id = esc_attr( $_POST['meta_id'] );
	$action = esc_attr( $_POST['request'] );
	$results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE meta_id = %d ", $meta_id) );
	$postdata = get_post( $postid);
	$author=$postdata->post_author;
	$comment = unserialize($results[0]->meta_value);
	if( $action=='like' )
	{
		if(userpro_userwall_get_option('send_email_on_comment_likedis')=="1" && $author!=$user_id)
		mail_user($author,$user_id,"commentlike");
		$count = isset( $comment['like'] )?$comment['like']+1:1;

	}
	else{
		if(userpro_userwall_get_option('send_email_on_comment_likedis')=="1" && $author!=$user_id)
		mail_user($author,$user_id,"commentdislike");
		$count = isset( $comment['dislike'] )?$comment['dislike']+1:1;
	}

	$comment[$action] = $count;
	$comment['actioned_by'] = isset( $comment['actioned_by'] )?$comment['actioned_by'].",".$user_id:$user_id.",";
	$like = $comment['like'];
	$dislike = $comment['dislike'];
	$comment = serialize( $comment );
	$wpdb->update(
	$wpdb->postmeta,
	array( 'meta_value'=>$comment ),
	array( 'meta_id'=>$meta_id )
	);



	echo json_encode(array('likes'=>$like,'dislikes'=>$dislike));
	die();
}


add_action('wp_ajax_nopriv_socialwall_load_posts', 'socialwall_load_posts');
add_action('wp_ajax_socialwall_load_posts', 'socialwall_load_posts');
function socialwall_load_posts(){
	global $userpro;
	$url = $_SERVER['HTTP_REFERER'];
	$posttype=$_POST['posttype'];
	global $wp_query,$wpdb;
	$curauth = $wp_query->get_queried_object();

	$postargs = array(
	'posts_per_page'   => '-1',
	'order'            => 'DESC',
	'include'          => '',
	'exclude'          => '',
	'meta_key'         => $posttype,
	'meta_value'       => '',
	'post_type'        => 'userpro_userwall',
	'post_mime_type'   => '',
	'post_parent'      => '',
	'post_status'      => 'publish',
	'suppress_filters' => true );

	$postslist = get_posts( $postargs );



	$following=0;
	$cnt=0;


	foreach($postslist as $post)
	{
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

		if($following==1 || get_current_user_id()==$post->post_author )
		$cnt=$cnt+1;

	}
	/*if(userpro_userwall_get_option('followerspost')=='1')
	 {
	 $post_count=$cnt;

	 }*/
	/*else
	 {*/
	$post_count =count($postslist);
	//}

	$postperpage=$_POST['count'];
	$social_userid=$_POST['social_userid'];
	$userrole=$_POST['role'];
	$userids_by_role='';
	if(isset($_POST['role']))
	{
		$userids=get_users("role=$userrole");
		foreach ( $userids as $user ) {
			if(is_array($userids_by_role))
			{
				array_push($userids_by_role,$user->ID);
			}
			else
			{
				$userids_by_role=array($user->ID);
			}
		}
	}
	else
	{
		$userids_by_role='';
	}

	$array = get_user_meta($post->post_author,'_userpro_followers_ids');
	if(isset($array['0']))
	{
		foreach($array['0'] as  $key => $val)
		{
			$user_ids[] = $key;
		}
	}

	if(empty($user_ids))
	$userids_by_role=get_current_user_id();

	if(userpro_userwall_get_option('followerspost')=='0')
	$userids_by_role='';

	//$post_count=$post_count-userpro_userwall_get_option( 'totalpost' );
	if($post_count >=$postperpage)
	{
		echo '<div id="userwalldata">';
		$args = array(
	'posts_per_page'   => userpro_userwall_get_option( 'totalpost' ),
	'offset'           => $postperpage,
	'category'         => '',
	'category_name'    => '',
	'order'            => 'DESC',
	'include'          => '',
	'exclude'          => '',
	'meta_key'         => $posttype,
	'meta_value'       => '',
	'post_type'        => 'userpro_userwall',
	'post_mime_type'   => '',
	'author__in' 	   => $userids_by_role,
	'post_parent'      => '',
	'post_status'      => 'publish',
	'suppress_filters' => true );

		$postslist = get_posts( $args );


		foreach($postslist as $post)
		{

			if(get_current_user_id()==$post->post_author || is_super_admin(get_current_user_id()) || userpro_userwall_get_option('followerspost')=='0')
			$following=1;


			if($following==1)
			{
				$user_id = get_post_meta( $post->ID,'user_id');

				?>
<div class="userwall" id=<?php echo $post->ID?>>

	<div class="userwall_delete_post">

	<?php if($user_id[0]==get_current_user_id() ||  is_super_admin(get_current_user_id())) {?>


		<i onclick="userwall_delete_post(<?php echo $post->ID;?> , this);"
			class="fa fa-trash fa-fw"></i>


			<?php }?>
	</div>
	<div class="userwall-post-content-img" data-key="profilepicture">
		<a href="<?php echo $userpro->permalink($user_id[0]); ?>"
			title="<?php _e('View Profile','userpro'); ?>"><?php echo get_avatar( $user_id[0], "60" );  ?>
		</a>
	</div>

	<div class="userwall-post-content" id=userwall-post-content
	<?php echo $post->ID?>>
		<div class="displayname">
			<a href="<?php echo $userpro->permalink($user_id[0]); ?>"><?php echo userpro_profile_data('display_name', $user_id[0]); ?>
			</a>
		</div>
		<br>
		<div class="content-text">
		<?php

		if($post->post_title=="imgpost"){

			$photo_desc = get_post_meta($post->ID , 'userwall_photo_desc' , true);
			echo '<div class="post-img"><img src="'.$post->post_content.'" style="-moz-user-select: none;
			    border-radius: 10px;max-width:99%; width: 100% !important;">
			 	<div>'.$photo_desc.'</div>
			 	</div>';

			echo userpro_socialwall_sharebutton($url.'?postid='.$post->ID.'#'.$post->ID);

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
	<div class="userwall-comment-data-<?php echo $post->ID;?>"
		id="userwall-comment">
		<?php
		$like_post=array();
		$dislike_post=array();

		$timestamp = strtotime($post->post_date);
		$new_date = date('d-M-Y h:i', $timestamp);

		$like_post=get_post_meta($post->ID,'socialwall_likes',true);
		$dislike_post=get_post_meta($post->ID,'socialwall_dislikes',true);
		if(!is_array($like_post)) $like_post = array();
		if(!is_array($dislike_post)) $dislike_post = array();


		if (!in_array(get_current_user_id(), $like_post) && !in_array(get_current_user_id(), $dislike_post) && is_user_logged_in())
		{
		?>

		<div class="userwall_postlikecount_post" id="userwall_postlikecount_post<?php echo $post->ID?>">
			<i onclick="userwall_postlikecount_post(<?php echo $post->ID;?>,<?php echo get_current_user_id()?>,<?php if(empty($like_post)) echo 0; else echo  count($like_post);?>,<?php if (empty($dislike_post)) echo 0; else echo count($dislike_post);?>,<?php echo $timestamp;?>);"
				class="userpro-icon-thumbsup socialwall_postlikecount_post btn-like-dislike"></i>
			<i class="socialwall_postlikecount_post "><?php if(empty($like_post)) echo 0; else echo  count($like_post);?>
			</i> <i
				onclick="userwall_postdislikecount_post(<?php echo $post->ID;?>,<?php echo get_current_user_id()?>,<?php if (empty($dislike_post)) echo 0; else echo count($dislike_post);?>,<?php if(empty($like_post)) echo 0; else echo  count($like_post);?>,<?php echo $timestamp;?>);"
				class="userpro-icon-thumbsdown socialwall_postlikecount_post btn-like-dislike"></i><i
				class="socialwall_postlikecount_post "><?php if (empty($dislike_post)) echo 0; else echo count($dislike_post);?>
			</i>
			<?php $report_userid=get_post_meta($post->ID,'socialwall_report',true );
			if(!is_array($report_userid)) $report_userid = array();
			if(!in_array(get_current_user_id(), $report_userid)) {?>
			<i
				onclick="userwall_report_post(<?php echo $post->ID;?> ,<?php echo get_current_user_id();?>);"
				class="reportpost fa fa-exclamation-circle"></i>
				<?php } else{?>
			<i style="color: black; opacity: 0.5; cursor: default;"
				class="fa fa-exclamation-circle"></i>
				<?php }?>

			<span class="post-date">
			<?php echo $new_date;?>
			</span>
		</div>
		<!-- <div class="countlike" id="countlike<?php echo $post->ID ?>">

</div> -->

		<?php }
		else
		{?>

		<div class="userwall_postlikecount_post" id="userwall_postlikecount_post<?php echo $post->ID?>">

		<?php
			echo '<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsup"></i>'; ?>
		<i class="socialwall_postlikecount_post "><?php if(empty($like_post)) echo 0; else echo  count($like_post);?>
		</i>
		<?php
		echo '<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsdown"></i>';
		?>
		<i class="socialwall_postlikecount_post "><?php if (empty($dislike_post)) echo 0; else echo count($dislike_post);?>
		</i>
		<?php $report_userid=get_post_meta($post->ID,'socialwall_report',true );
			if(!is_array($report_userid)) $report_userid = array();
			if(!in_array(get_current_user_id(), $report_userid)) {?>
			<i
				onclick="userwall_report_post(<?php echo $post->ID;?> ,<?php echo get_current_user_id();?>);"
				class="reportpost fa fa-exclamation-circle"></i>
				<?php } else{?>
			<i style="color: black; opacity: 0.5; cursor: default;"
				class="fa fa-exclamation-circle"></i>
				<?php }?>

			<span class="post-date">
			<?php echo $new_date;?>
			</span>

			</div>
		<?php
		}




		?>
	</div>
	<div class="userwall-comment-container">
	<?php
	global $wpdb;
	$results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s", $post->ID, 'user_comment') );
	$comment_count=count_comment($post->ID);
	//$comments = get_post_meta($post->ID,'user_comment');
	$i=0;
	foreach($results as $result)
	{
		$comment = unserialize($result->meta_value);
		?>
		<div class="userwall_comment_data" id="<?php echo ++$i ?>">
		<?php
		if($comment['comment_userid']==get_current_user_id() ||  is_super_admin(get_current_user_id())) {
			?>
			<div class=userwall_delete_comment>
				<i id="delete_comment_<?php echo $post->ID; ?>"
					onclick="userwall_delete_comment('<?php echo $post->ID;?>','<?php echo $comment['comment_content'];?>',this);"
					class="fa fa-trash fa-fw-3"></i>
			</div>
			<?php
		}?>
			<div class="userwall-comment-content-img" data-key="profilepicture">
				<a
					href="<?php echo $userpro->permalink($comment['comment_userid']); ?>"
					title="<?php _e('View Profile','userpro'); ?>"><?php echo get_avatar( $comment['comment_userid'], "40" );  ?>
				</a>
			</div>


			<div class="userpro-comment-post-content userwall-post-content">
				<div class="displayname">
					<a
						href="<?php echo $userpro->permalink($comment['comment_userid']); ?>"><?php echo userpro_profile_data('display_name', $comment['comment_userid']); ?>
					</a>
				</div>

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
				echo "<br><p>".make_clickable($comment['comment_content'])."</p>";
				?>
				<div class="userwall_commentlikecount_comment"
					id=userwall_commentlikecount_comment<?php echo $result->meta_id?>>
					<?php
					if( $flag ){
						?>
						<?php
						echo '<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsup"></i>'; ?>
					<i class="socialwall_postlikecount_post ">
						<?php if(!isset($comment['like'])) echo 0; else echo $comment['like'];?>
					</i>
					<?php
					echo '<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsdown"></i>';
					?>
					<i class="socialwall_postlikecount_post ">
						<?php if (!isset($comment['dislike'])) echo 0; else echo $comment['dislike'];?>
					</i>
					<?php
					}
					else{
						?>
					<i
						onclick="userwall_commentlikedislikecount_comment(<?php echo $result->meta_id;?>,<?php echo get_current_user_id()?>,<?php  if(!isset($comment['like'])) echo 0; else echo  $comment['like']?>,<?php if (!isset($comment['dislike'])) echo 0; else echo $comment['dislike'];?>,'like',<?php echo $post->ID;?>,<?php if (!isset($commenttimestamp)) echo 0; else echo $commenttimestamp;?>);"
						class="socialwall_postlikecount_post userpro-icon-thumbsup btn-like-dislike"></i><i
						class="socialwall_postlikecount_post " id="comment_like_count"><?php if(!isset($comment['like'])) echo 0; else echo  $comment['like'];?>
					</i>
					<?php if( $flag ){
						echo '<i style="color:black;opacity: 0.5;" class="socialwall_postlikecount_post userpro-icon-thumbsdown"></i>';
					}
					?>
					<i
						onclick="userwall_commentlikedislikecount_comment(<?php echo $result->meta_id;?>,<?php echo get_current_user_id()?>,<?php  if (!isset($comment['like'])) echo 0; else echo $comment['like'];?>,<?php if(!isset($comment['dislike'])) echo 0; else echo  $comment['dislike'];?>,'dislike',<?php echo $post->ID;?>,<?php if (!isset($commenttimestamp)) echo 0; else echo $commenttimestamp;?>);"
						class="socialwall_postlikecount_post userpro-icon-thumbsdown btn-like-dislike"></i><i
						class="socialwall_postlikecount_post" id="comment_dislike_count"><?php if (!isset($comment['dislike'])) echo 0; else echo $comment['dislike'];?>
					</i>
					<?php }?>
					<span class="commented-date">
					<?php echo $commenttime;?>
					</span>
				</div>
				<?php
				echo "</div></div>";

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
					<textarea id=userwall-comment-<?php echo $post->ID;?>
						placeholder="<?php _e('Enter comment and Hit ENTER to submit...','userpro-userwall');?>"
						onkeypress="user_post_comment(this,'userwall-comment',<?php echo get_current_user_id();?>,<?php  echo $post->ID;?>,event,<?php echo count_comment($post->ID);?>,<?php  echo $limit_comment;?>);"
						name="userwall-comment" cols="40" rows="1" style=""
						class="comment_textarea"></textarea>
					<?php }?>
				</div>
			</div>



			<?php
			}?>
		</div>

		<?php }

		?>

	</div>
</div>
</div>
		<?php
		die();


	}
	else
	{
		echo "hide";
		die();
	}


}
add_action('wp_ajax_nopriv_limit_post', 'limit_post');
add_action('wp_ajax_limit_post', 'limit_post');
function limit_post()
{

	$postcount=userspost(get_current_user_id());
	$limit_post=userpro_userwall_get_option('limit_number_of_post');

	if($postcount<$limit_post || $limit_post=='-1' )
	{
		echo 'show';
	}
	else
	{
		echo 'hide';
	}
	die();

}


add_action('wp_ajax_nopriv_post_userdata', 'post_userdata');
add_action('wp_ajax_post_userdata', 'post_userdata');

function post_userdata(){
	global $userpro;
	if(isset($_POST['posttype']))
	$posttype=$_POST['posttype'];
	$url = $_SERVER['HTTP_REFERER'];
	$wall_data = str_replace("\n","<br/>",$_POST['file_name']);
	$pos = strpos($wall_data, '<img') !== false || strpos($wall_data,'<script>') !==false?1:0;
	if($pos)
	{
		$wall_data = str_replace("<script>","",str_replace("onerror", "", $wall_data));
	}
	$my_post = array(
		  'post_title'    => 'My post',
		  'post_content'  => str_replace("\n","<br/>",$wall_data),
		  'post_status'   => 'publish',
		  'post_type'   => 'userpro_userwall',

	);
	remove_filter('content_save_pre', 'wp_filter_post_kses');
	remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');

	$post_data=wp_insert_post($my_post);

	add_filter('content_save_pre', 'wp_filter_post_kses');
	add_filter('content_filtered_save_pre', 'wp_filter_post_kses');

	if(isset($_POST['posttype']))
	add_post_meta($post_data,$posttype,'1');
	add_post_meta($post_data, 'user_id',$_POST['user_id'] );

	$userids=get_user_meta($_POST['puser_id'],'userids',true );

	if(is_array($userids))
	{
		array_push($userids,$post_data);
	}
	else
	{
		$userids=array($post_data);
	}
	update_user_meta($_POST['puser_id'],"userids",$userids);

	if(isset($_POST['visibility'])){
		update_post_meta($post_data,'ups_visibility',$_POST['visibility']);
	}

	if( isset($_POST['user_full_name']) ){

		$user_name = $_POST['user_full_name'];
		update_post_meta($post_data,"user_full_name",$_POST['user_full_name']);
	}
	else{
		$user_name = userpro_profile_data('display_name', get_current_user_id());
	}

	$post = get_post($post_data);
	$dislike_post=0;
	$like_post=0;
	if(isset($_POST['visibility']) && $_POST['visibility'] == 'personal' ){
		ob_start();
		include UPS_PLUGIN_DIR.'templates/personalwall-single-post.php';
		$output = ob_get_contents();
		ob_end_clean();
		$data['user_profile'] = $output;
		$post_all_data=json_encode($data);
	}else{
		ob_start();
		include UPS_PLUGIN_DIR.'templates/single-post.php';
		$output = ob_get_contents();
		ob_end_clean();
		$data['user_profile'] = $output;
		$post_all_data=json_encode($data);
	}

	if(is_array($post_all_data)){ print_r($post_all_data); }else{ echo $post_all_data; } die;
}


add_action('wp_ajax_nopriv_post_usercomment', 'post_usercomment');
add_action('wp_ajax_post_usercomment', 'post_usercomment');

function post_usercomment(){
	global $userpro;
	$comment_data = str_replace("\n","<br/>",$_POST['file_name']);

	$pos = strpos($comment_data, '<img') !== false || strpos($comment_data,'<script>') !==false?1:0;
	if($pos)
	{
		$comment_data = str_replace("<script>","",str_replace("onerror", "", $comment_data));
	}
	$comment=array(
		'comment_content'  => $comment_data,
		'comment_userid'   => $_POST['user_id'],
		'comment_date'   => date('d-m-Y H:i'),
        'like' => 0,
        'dislike' => 0
	);

	$i=1;
	$meta_id = add_post_meta($_POST['post_id'],'user_comment',$comment);
	$post = get_post($_POST['post_id']);
	ob_start();
	include UPS_PLUGIN_DIR.'templates/single-comment.php';
	$output = ob_get_contents();
	ob_end_clean();
	$comment_data = array( 'user_comment'=> $output );
	if(userpro_userwall_get_option('send_email_on_comment')){
		$current_post = get_post($_POST['post_id']);
		$post_posted_by = $current_post->post_author;
		if($post_posted_by != $_POST['user_id']){
			prepare_email_on_comment($post_posted_by, $_POST['user_id']);
		}
	}

	$post = get_post($_POST['post_id']);
	$post_author_id = $post->post_author;

	$comment_user_id = get_current_user_id();

	$perma = get_permalink($_POST['pageid']);
	$link = $perma.'#'.$_POST['post_id'];

	$recent_comment = array('post_link' =>$link ,'comment_user' =>$comment_user_id);
	$recent_commenters = get_user_meta($post_author_id,'recent_comments',true);

	if(is_array($recent_commenters)){
		array_push($recent_commenters, $recent_comment);
	}
	else{
		$recent_commenters = array($recent_comment);
	}

	update_user_meta($post_author_id,'recent_comments',$recent_commenters);

	$comment_all_data=json_encode($comment_data);

	if(is_array($comment_all_data)){ print_r($comment_all_data); }else{ echo $comment_all_data; } die;
}


add_action('wp_ajax_nopriv_userwall_upload_img', 'userwall_upload_img');
add_action('wp_ajax_userwall_upload_img', 'userwall_upload_img');
function userwall_upload_img()
{
	$sw_personalwall = $_POST['sw_personalwall'];
	global $userpro;
	$url=home_url(add_query_arg( NULL, NULL ) );

	$file_extension = strtolower(strrchr($_POST['src'], "."));
	if(in_array($file_extension, array( '.mp4' , '.mkv', '.avi' ) ))
	$post_title = 'vidpost';
	else
	$post_title = 'imgpost';

	$my_post = array(
		  'post_title'    => $post_title,
		  'post_content'  => $_POST['src'],
		  'post_status'   => 'publish',
		  'post_type'   => 'userpro_userwall',
		  'meta_key'    => $_POST['posttype'],
		  'meta_value'  => '1'
	);

	$post_data=wp_insert_post($my_post);
	add_post_meta($post_data, 'userwall_photo_desc', $_POST['photo_desc']);
	add_post_meta($post_data, 'user_id',get_current_user_id() );
    $query_id = userpro_get_view_user( get_query_var('up_username') );
    if(empty($query_id)){
        $query_id = get_current_user_id();
    }
    $userids=get_user_meta($query_id,'userids',true );

    if(is_array($userids))
	{
		array_push($userids,$post_data);
	}
	else
	{
		$userids=array($post_data);
	}
	if( $sw_personalwall == 'sw_personalwall'){
		update_user_meta($query_id,"userids",$userids);
	}

	$post = get_post($post_data);
	$comment_count=count_comment($post_data);
	$limit_comment=userpro_userwall_get_option('limit_number_of_comment');
	$dislike_post=0;
	$like_post=0;
        if(isset($sw_personalwall) && $sw_personalwall == 'sw_personalwall'){
		update_post_meta($post_data,'ups_visibility','personal');
	}
	ob_start();
	if( $sw_personalwall == 'sw_personalwall'){
		include UPS_PLUGIN_DIR.'templates/personalwall-single-post.php';
	}else{
		include UPS_PLUGIN_DIR.'templates/single-post.php';
	}

	$output = ob_get_contents();
	ob_end_clean();
	$data = array('user_profile'=>$output);
	$post_all_data=json_encode($data);

	if(is_array($post_all_data)){ print_r($post_all_data); }else{ echo $post_all_data; } die;
}

add_action('wp_ajax_nopriv_userwall_delete_userpost', 'userwall_delete_userpost');
add_action('wp_ajax_userwall_delete_userpost', 'userwall_delete_userpost');

function userwall_delete_userpost()
{

	$list = (array)get_option('sw_reportpostid');
	$key = array_search($_POST['postid'], $list);
	unset($list[$key]);
	update_option('sw_reportpostid',$list);

	$my_post = get_post( $_POST['postid'] ); // $id - Post ID
	$author_id= $my_post->post_author;
	if(get_current_user_id()==$author_id || is_super_admin(get_current_user_id()))
	wp_delete_post($_POST['postid']);
	else
	echo "You do not have permission to delete this post";
	die();

}
add_action('wp_ajax_nopriv_delete_comment', 'delete_comment');
add_action('wp_ajax_delete_comment', 'delete_comment');
function delete_comment()
{

	global $wpdb;
	$result=$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = 'user_comment' and post_id=".$_POST['post_id']." LIMIT 1");

}


function mail_user($to, $from,$template="") {
	global $userpro;


	$user = get_userdata($to);
	$display_name = userpro_profile_data('display_name', $from);

	$headers = 'From: '.userpro_get_option('mail_from_name').' <'.userpro_get_option('mail_from').'>' . "\r\n";
	$headers .= "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";


	if($template=="commentlike" || $template=="commentdislike")
	{


		if($template=="commentlike")
		$action="like";
		else
		$action="dislike";

		$val = socialwall_replace_placeholders($from,$to,$action);
		$search = $val['search'];
		$replace = $val['replace'];
		$subject = userpro_userwall_get_option("mail_user_on_likedis_comment_s");
		$subject = str_replace( $search, $replace, $subject );
		$body = nl2br(userpro_userwall_get_option("mail_user_on_likedis_comment"));
		$body = str_replace( $search, $replace, $body );

	}
	elseif($template=="postlike" || $template=="postdislike")
	{
		if($template=="postlike")
		$action="like";
		else
		$action="dislike";

		$val = socialwall_replace_placeholders($from,$to,$action);
		$search = $val['search'];
		$replace = $val['replace'];
		$subject = userpro_userwall_get_option("mail_user_on_likedis_post_s");
		$subject = str_replace( $search, $replace, $subject );
		$body = nl2br(userpro_userwall_get_option("mail_user_on_likedis_post"));
		$body = str_replace( $search, $replace, $body );

	}
	else
	{

		$val = socialwall_replace_placeholders($from,$to);
		$search = $val['search'];
		$replace = $val['replace'];
		$subject = userpro_userwall_get_option("mail_user_on_comment_s");
		$subject = str_replace( $search, $replace, $subject );
		$body = nl2br(userpro_userwall_get_option("mail_user_on_comment"));
		$body = str_replace( $search, $replace, $body );

	}

	wp_mail( $user->user_email , $subject, $body, $headers );


}

function socialwall_replace_placeholders($from_id=null,$to_id=null,$action=null){
	global $userpro;
	$builtin = array(
				'{USERPRO_ADMIN_EMAIL}' => userpro_get_option('mail_from'),
				'{USERPRO_BLOGNAME}' => userpro_get_option('mail_from_name'),
				'{USERPRO_BLOG_URL}' => home_url(),
				'{USERPRO_BLOG_ADMIN}' => admin_url(),
				'{action}' => $action,

	);

	if(isset($from_id)){
		$from_user = get_userdata($from_id);
		$builtin['{USERPRO_FROM_USERNAME}'] = $from_user->user_login;
		$builtin['{USERPRO_FROM_FIRST_NAME}'] = userpro_profile_data('first_name', $from_user->ID );
		$builtin['{USERPRO_FROM_LAST_NAME}'] = userpro_profile_data('last_name', $from_user->ID );
		$builtin['{USERPRO_FROM_NAME}'] = userpro_profile_data('display_name', $from_user->ID );
		$builtin['{USERPRO_FROM_EMAIL}'] = $from_user->user_email;
		$builtin['{USERPRO_FROM_PROFILE_LINK}'] = $userpro->permalink( $from_user->ID );
	}

	if(isset($to_id)){
		$to_user = get_userdata($to_id);
		$builtin['{USERPRO_TO_USERNAME}'] = $to_user->user_login;
		$builtin['{USERPRO_TO_FIRST_NAME}'] = userpro_profile_data('first_name', $to_user->ID );
		$builtin['{USERPRO_TO_LAST_NAME}'] = userpro_profile_data('last_name', $to_user->ID );
		$builtin['{USERPRO_TO_NAME}'] = userpro_profile_data('display_name', $to_user->ID );
		$builtin['{USERPRO_TO_EMAIL}'] = $to_user->user_email;
		$builtin['{USERPRO_TO_PROFILE_LINK}'] = $userpro->permalink( $to_user->ID );
	}
	$search = array_keys($builtin);
	$replace = array_values($builtin);
	return array( 'search'=>$search, 'replace'=>$replace );

}
/* Prepare email for sending when someone comments on a post */

function prepare_email_on_comment($to =null, $from=null){

	mail_user($to,$from);

}

/* Function for sending emails */



add_action('init','check_notification');
function check_notification(){

	$user_id = get_current_user_id();
	$check = get_user_meta($user_id,'recent_comments',true);

	if(!empty($check) && userpro_userwall_get_option('sw_comment_notification')==1){
		add_filter('wp_footer', 'display_socialwall_notification');
	}
}

function display_socialwall_notification($user_id){

	echo '<div id="socialnotify"></div>';

	?>
<script>
			jQuery(function(){
				jQuery.ajax({
					url:userpro_ajax_url,
					data: "action=userpro_social_chk_notification",
					type: 'POST',
					success:function(data){
						jQuery('#socialnotify').html(data);
					},
				});
			});
			</script>
	<?php
}

add_action('wp_ajax_nopriv_userpro_social_chk_notification', 'userpro_social_chk_notification');
add_action('wp_ajax_userpro_social_chk_notification', 'userpro_social_chk_notification');

function userpro_social_chk_notification() {
	global $userpro;
	if (userpro_is_logged_in()){
		$user_id = get_current_user_id();
		//if ($userpro_msg->has_new_chats($user_id)) {
		require_once UPS_PLUGIN_DIR . 'templates/notification.php';
		//	}
	}
	die();
}

function comment_notifiction_count($user_id){

	$comments = get_user_meta($user_id,'recent_comments',true);
	if(is_array($comments)){
		$count = sizeof($comments);
	}
	else{
		$count = 0;
	}

	return sprintf('%s New Comments', $count);
}

function display_notification($user_id){
	global $userpro;
	$output = '';
	$results = get_user_meta($user_id,'recent_comments',true);

	if(is_array($results)){
		foreach ($results as $result){
			$link = $result['post_link'];
			$user = $result['comment_user'];
			$userdata = get_userdata( $user );
			$output .= '<div class="socail_notify_comment"><a href="'.$userpro->permalink($userdata->ID).'">'.ucfirst($userdata->display_name).'</a> has commented on your <a href="'.$link.'" target="_blank">post</a> <br></div><hr>';
		}
	}

	delete_user_meta( $user_id, 'recent_comments');

	return $output;

}

add_action('wp_ajax_nopriv_socialwall_display_notify', 'socialwall_display_notify');
add_action('wp_ajax_socialwall_display_notify', 'socialwall_display_notify');
function socialwall_display_notify(){
	global $userpro;
	$output = array();

	$user_id = $_POST['user_id'];
	if ( !userpro_is_logged_in() || $user_id != get_current_user_id() ) die();

	ob_start();

	require_once UPS_PLUGIN_DIR . 'templates/notify.php';

	$output['html'] = ob_get_contents();

	ob_end_clean();

	$output=json_encode($output);
	if(is_array($output)){ print_r($output); }else{ echo $output; } die;
}

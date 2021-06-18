<?php global $wpdb;
$query="SELECT post_author FROM ".$wpdb->prefix."posts WHERE post_type IN ('post')";
$results = $wpdb->get_results($query , ARRAY_A);
if(is_array($results)) {
    $list_users = array();
foreach ($results as $result){
	$list_users[] = $result['post_author'];
}
if(isset($list_users))
$list_users = array_unique($list_users);
}else {
	$list_users = array();
}

if(!isset($args['user']))
$args['user']='';
?>
<div class="post_by_users">
			
	<?php if(userpro_get_option('show_filter')=='1'){?>
		<div class="select_user">
			<select id="user_list" class="chosen-container chosen-container-single"   data-placeholder="<?php _e('Filter Results by','gridfx'); ?>"  style="width: 200px;">
			<?php if(is_user_logged_in()) {?>			
			<option value="all">My Posts</option>
			<?php } else
			{?>
				<option value="all">All Users</option>
							
 
			<?php  }
				if(isset($list_users))
				foreach ($list_users as $author) {
					$user_info = get_userdata($author);
					echo '<option value='.$author.' '.  selected($args['user'],$user_info->user_login ).' >'.$user_info->user_login.'</option>';
				}
			?>
			
		</select>
		</div>
		
</br></br>
<?php }?>

<div class="userpro-post-wrap">

	<?php if ($post_query->have_posts() ) { ?>
	
	<div class="userpro-posts">
	
	<?php while ($post_query->have_posts()) { $post_query->the_post(); ?>
	
		<?php if ($postsbyuser_mode == 'compact' ) { ?>
		
		<div class="userpro-post userpro-post-compact">

			<?php if ($postsbyuser_showthumb == 1) {?>
			<div class="userpro-post-img">
				<a href="<?php the_permalink(); ?>"><?php echo $userpro->post_thumb( $post->ID, $postsbyuser_thumb ); ?><span class="shadowed"></span><span class="iconed"></span></a>
			</div>
			<?php } ?>
				
			<div class="userpro-post-title">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</div>
			
			<div class="userpro-post-stat">
				<a href="<?php the_permalink(); ?>#comments"><i class="userpro-icon-comment"></i> <?php echo get_comments_number(); ?></a>
			</div>
			
			<div class="userpro-clear"></div>
		
		</div><div class="userpro-clear"></div>
		
		<?php } else { ?>
		
		<div class="userpro-post">

			<div class="userpro-post-img">
				<a href="<?php the_permalink(); ?>"><?php echo $userpro->post_thumb( $post->ID ); ?><span class="shadowed"></span><span class="iconed"></span></a>
			</div>
			
			<div class="userpro-post-title">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</div>
			
			<div class="userpro-post-stat">
				<?php  
				
				$my_post = get_post($post->ID); // $id - Post ID
       		 		 $author_id= $my_post->post_author;
				if($args['usercanedit']==1 &&(get_current_user_id()==$author_id || is_super_admin(get_current_user_id()))) {?>
				<input type="button" value="Delete" id='postsbyuserddelete' class="userpro-button" onclick="userpro_delete_userpost(<?php echo $post->ID;?>,this);" />
	                         <?php $link=get_publish_page_link();
			
				
					$link.=	"?post_id=".$post->ID;			
					?>
				
				<a href="<?php echo $link?>"><i class="userpro-icon-edit"></i> Edit </a>&nbsp;&nbsp;
				<?php }?>
				<a href="<?php the_permalink(); ?>#comments"><i class="userpro-icon-comment"></i> <?php echo get_comments_number(); ?></a>
			</div>
		
		</div>
		
		<?php } ?>
	
	<?php } ?>
	
	</div>
		
	<?php } else { // no results ?>
		<div class="userpro-search-noresults"><?php _e('This user has not published any posts','userpro'); ?></div>
	<?php } ?>
	
</div><div class="userpro-clear"></div>
<?php if($is_paginate) { ?>
<div class="userpro-paginate bottom"><?php echo $paginate; ?></div></div>	
<?php  } ?>

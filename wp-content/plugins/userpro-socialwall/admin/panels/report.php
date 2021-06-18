<form method="post" action="">

<h3><?php _e('Reported Posts','userpro-userwall'); ?></h3>

<table class="form-table">
<style>
tr.socialwalltd td div {
margin-left: 10px;
}

</style>

<tr >
<th colspan=3>
<b><?php _e('Listed below are posts on the social wall which users have reported as objectionable or offensive','userpro-userwall');?></b>
</th>
</tr>
<tr valign="top">

		<th scope="row"><label><?php _e('#','userpro-userwall'); ?></label></th>
		<th scope="row"><label ><?php _e('Post','userpro-userwall'); ?></label></th>
		<th scope="row"><label><?php _e('Users','userpro-userwall'); ?></label></th>
		<th scope="row"><label><?php _e('Action','userpro-userwall'); ?></label></th>
		
</tr>

	<?php 

		
		$i=1;
		$reporteposts=get_option('sw_reportpostid');
		foreach($reporteposts as $postid)
		{
			
			$userids=get_post_meta($postid,'socialwall_report',true);
			
			
		

		?>


<tr valign="top" id="<?php echo $postid;?>" class="socialwalltd">

	
	<td ><div>
			<?php echo $i;?>
		</div></td>
		<td class="socialwalltd">
			<div>



<?php 
$ID = $postid;
$args = array('p' => $ID, 'post_type' => 'userpro_userwall');
$loop = new WP_Query($args);
?>
<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
    <?php global $post; ?>
    <?php
    $src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), array( 5600,1000 ),  false, '' ); ?>  
    <div class="section" style="background: url(<?php echo $src[0]; ?>) no-repeat center     center fixed; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover; z-index:-1;">


<?php $posttype= get_the_title(); 

		if($posttype=="imgpost")
		{$content = get_the_content(); 

?>
		<img src="<?php echo wp_filter_nohtml_kses( $content )?> " height="auto" width="100" >
		<?php
		}
		else
		{
			the_content () ;
		}			
		

?>   



<?php endwhile; ?>
</div>
</td>
		<td >
<div>
			
			<?php 
				
				foreach($userids as $userid)	
				{
					
					 $user_info = get_userdata($userid);
      					$username = $user_info->user_login;
					
				echo $username.'<br>';
				}
			?>
		</div>	
		</td>
		<td>
		<div>
			<a href=#><i onclick="userwall_delete_post(<?php echo $postid;?>,this);" >Delete/</i></a>
			<a href=#><i onclick="userwall_ignore_post(<?php echo $postid;?>);" >Ignore</i></a>
		</div>

		</td>	

	</tr>

	<?php 
$i++;
}?>
	
</table>



<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro-userwall'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro-userwall'); ?>"  />
</p>

</form>


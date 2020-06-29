<?php 
	global $post;
	$postid = get_the_ID();
	$featured_image=get_the_post_thumbnail($postid, 'full', array('title' => get_the_title()));
	$clients_url= get_post_meta( $postid, 'theplus_clients_url', true );
	$clients_subtitle= get_post_meta( $postid, 'theplus_clients_subtitle', true );
?>
<div class="pt-plus-client-list-style-content client-content-2  <?php echo esc_attr($uid_loop) ?>"> 
	<div class="post-content">
		<div class="blog-media clent-im image-loaded ">
			<span class="thumb-wrap">
			<a href="<?php echo esc_url($clients_url); ?>" rel=""><?php echo $featured_image; ?></a>
			</span>
		</div>
		<div class="client-content">
			<div class="blur"> </div>
			<div class="vertical-center">
			<?php	include THEPLUS_PLUGIN_PATH. 'vc_elements/clients/client-title.php';   ?>
			<?php	include THEPLUS_PLUGIN_PATH. 'vc_elements/clients/client-subtitle.php';  ?>
			<?php	include THEPLUS_PLUGIN_PATH. 'vc_elements/clients/client-desc.php';  ?>
			</div>
		</div>
	</div>	
</div>

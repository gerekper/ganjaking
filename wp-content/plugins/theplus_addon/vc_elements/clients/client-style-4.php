<?php 
	global $post;
	$postid = get_the_ID();
	$featured_image=get_the_post_thumbnail($postid, 'full', array('title' => get_the_title()));
	$clients_url= get_post_meta( $postid, 'theplus_clients_url', true );
	$clients_subtitle= get_post_meta( $postid, 'theplus_clients_subtitle', true );
	$client_img = wp_get_attachment_image( get_post_meta( get_the_ID(), 'theplus_clients_hover_img_id', 1 ), 'full' );
?>
<div class="pt-plus-client-list-style-content client-content-4 text-center <?php echo esc_attr($uid_loop) ?>" >
	<div class="post-content">
		<div class="blog-media clent-im image-loaded anmation-style-1">
			<?php if ($client_img!= '') { ?>
				<span class="thumb-wrap hover-front">
					<a href="<?php echo esc_url($clients_url); ?>" rel=""><?php echo $featured_image; ?></a>
				</span>
				<span class="thumb-wrap hover-back">
					<a href="<?php echo esc_url($client_img); ?>" rel=""><?php echo $client_img; ?></a>
				</span>
			<?php }else{?>
					<a href="<?php echo esc_url($clients_url); ?>" rel=""><?php echo $featured_image; ?></a>
			<?php }?>
		</div>
	</div>	
</div>
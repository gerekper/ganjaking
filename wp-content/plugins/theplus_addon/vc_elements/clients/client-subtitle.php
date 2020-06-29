<?php 
	global $post;
	$postid = get_the_ID();
	$clients_url= get_post_meta( $postid, 'theplus_clients_url', true );
	$clients_subtitle= get_post_meta( $postid, 'theplus_clients_subtitle', true );
	 ?>
	<div class="client-subtitle">
				<a href="<?php echo esc_url($clients_url); ?>"><?php echo esc_html($clients_subtitle); ?></a>
	</div>

<?php global $post;
	$postid = get_the_ID();
	$clients_url= get_post_meta( $postid, 'theplus_clients_url', true );
	?>
<div class="client-title">
	<a href="<?php echo esc_url($clients_url); ?>"><?php echo esc_html(get_the_title()); ?></a>
</div>
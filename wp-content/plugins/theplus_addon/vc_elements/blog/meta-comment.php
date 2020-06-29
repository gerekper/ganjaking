<?php
	$comments_count = wp_count_comments();
?>
<span class="meta-comments">
	<a href="<?php the_permalink(); ?>#comments" rel="author" class="fn">
		<span><?php comments_number( '0', '1', '%' ); ?></span><span class="comments-title">&nbsp;<?php _e('Comments', 'pt_theplus'); ?></span>
	</a>
</span>
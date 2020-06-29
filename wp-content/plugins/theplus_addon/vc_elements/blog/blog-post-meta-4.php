<div class="post-meta-info">
	<span class="post-author"><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a> </span>
	<span class="meta-date">
		<a href="<?php echo esc_url(get_the_permalink()); ?> "><?php echo __('On ', 'pt_theplus'); ?><span class="entry-date"><?php echo get_the_date('F d,Y'); ?></span></a>
	</span>
</div>


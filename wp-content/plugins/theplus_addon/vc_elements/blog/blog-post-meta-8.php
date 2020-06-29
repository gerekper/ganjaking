<div class="post-meta-info">
	<span class="post-author"><i class="fa fa-user" aria-hidden="true"></i><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a> </span>
	<span class="meta-date">
		<i class="fa fa-table" aria-hidden="true"></i><a href="<?php echo esc_url(get_the_permalink()); ?> "><span class="entry-date"><?php echo get_the_date('F d,Y'); ?></span></a>
	</span>
    <?php if ( ! is_single() ) {
        echo '<span class="meta-comments"><i class="fa fa-comment" aria-hidden="true"></i>';
        comments_popup_link(__('No Comments', 'pt_theplus'), __('1 Comment', 'pt_theplus'), __('% Comments', 'pt_theplus'));
		echo '</span>';
    } ?>
</div>


<div class="post-meta-info">
	<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/meta-date.php'; ?>
	<span class="post-author"><?php echo __('By : ', 'pt_theplus'); ?> <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a> </span>
    <?php if ( ! is_single() ) {
	echo '<span class="meta-comments">';
        comments_popup_link(__('No Comments', 'pt_theplus'), __('1 Comment', 'pt_theplus'), __('% Comments', 'pt_theplus'));
	echo '</span>';
    } ?>
</div>


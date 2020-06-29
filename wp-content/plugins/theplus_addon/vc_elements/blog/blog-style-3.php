<?php 
$postid=get_the_ID();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="blog-list-style-content text-center">
	<div class="post-content ">
		<?php
		
				if ( has_post_thumbnail() ) { ?>
				<a href="<?php echo esc_url(get_the_permalink()); ?>">
					<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/format-image.php'; ?>
				</a>
				<?php }else{ ?>
				<div class="blog-featured-image">
					<a href="<?php echo esc_url(get_the_permalink()); ?>">
						<?php echo pt_plus_loading_image_grid($postid); ?>
					</a>
				</div>
				<?php }
		?>
		<div class="column-date">
			<a href="<?php echo esc_url(get_the_permalink()); ?> "><div class="entry-date"><?php echo get_the_date('d'); ?></div><div class="entry-month"><?php echo get_the_date('M'); ?></div></a>
		</div>
	</div>
	<div class="post-title-content">
	<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/post-meta-title.php'; ?>
	<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/blog-post-meta-2.php'; ?>
	</div>
	<div class="entry-content">
		<p><?php echo pt_plus_excerpt(30); ?></p>
	</div>
	<div class="pt-theplus-post-read-more-button">
		<a href="<?php echo esc_url(get_the_permalink()); ?>" class="read-more-btn"><?php echo __('Read More ', 'pt_theplus'); ?>  <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
	</div>
</div>
</article>
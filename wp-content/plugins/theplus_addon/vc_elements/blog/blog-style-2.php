<?php 
$postid=get_the_ID();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="blog-list-style-content">
	<div class="post-content">
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
	</div>
	<div class="post-content-data">
		<div class="content-column column-date">
			<a href="<?php echo esc_url(get_the_permalink()); ?> "><div class="entry-date"><?php echo get_the_date('F d'); ?></div><div class="entry-year"><?php echo get_the_date('Y'); ?></div></a>
		</div>
		<div class="content-column">
			<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/post-meta-title.php'; ?>
			<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/blog-post-meta-2.php'; ?>
		</div>
	</div>
</div>
</article>
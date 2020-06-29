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
				<?php } ?>
		</div>
		<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/post-meta-title.php'; ?>
		<div class="post-meta-info">
			<hr class="border-bottom" />
			<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/blog-post-meta.php'; ?>
			<hr class="border-bottom" />
		</div>
		<?php if(get_the_excerpt()){ ?>
			<div class="entry-content">
				<p><?php echo pt_plus_excerpt(30); ?></p>
			</div>
		<?php } ?>
		<div class="pt-theplus-post-read-more-button pt_plus_button  button-style-7 ">
			<a class="button-link-wrap" href="<?php echo esc_url(get_the_permalink()); ?>" title="<?php echo __('Read More', 'pt_theplus'); ?>" target=""><?php echo __('Read More', 'pt_theplus'); ?><span class="btn-arrow"></span></a>
		</div>
	</div>
</article>

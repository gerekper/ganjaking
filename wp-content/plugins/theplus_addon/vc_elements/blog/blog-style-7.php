<?php 
$postid=get_the_ID();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="blog-list-style-content  text-center">
	<div class="post-content">
		<div class="border-top"><span class="border-dark"></span></div>
		<div class="post-meta-info">
			<span class="meta-date"><a href="<?php echo esc_url(get_the_permalink()); ?>" class="meta-post-date"><span class="entry-date"><i class="fa fa-clock-o" aria-hidden="true"></i><?php echo get_the_date('d F'); ?></span></a></span>
		</div>
		<?php
			if($layout=='grid' || $layout=='carousel'){
				if ( has_post_thumbnail() ) { ?>
				<a href="<?php echo esc_url(get_the_permalink()); ?>">
					<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/format-image.php'; ?>
				</a>
				<?php }else{ ?>
				<div class="blog-media image-loaded">
					<a href="<?php echo esc_url(get_the_permalink()); ?>">
						<?php echo pt_plus_loading_image_grid($postid); ?>
					</a>
					
				</div>
				<?php }
			}else{
				if(!get_post_format() || has_post_format('image')) {
					if ( has_post_thumbnail() ) { ?>
					<a href="<?php echo esc_url(get_the_permalink()); ?>">
						<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/format-image.php'; ?>
					</a>
					<?php }else{ ?>
					<div class="blog-media image-loaded">
						<a href="<?php echo esc_url(get_the_permalink()); ?>">
							<?php echo pt_plus_loading_image_grid($postid); ?>
						</a>
						
					</div>
					<?php }
				}
			}
		?>
	</div>
	<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/post-meta-title.php'; ?>
	<?php if(get_the_excerpt()){ ?>
		<div class="entry-content ">
			<p><?php echo pt_plus_excerpt(30); ?></p>
		</div>
	<?php } ?>
	<div class="pt-theplus-post-read-more-button">
		<a href="<?php echo esc_url(get_the_permalink()); ?>" class="read-more-btn" title="<?php echo __('Read More', 'pt_theplus'); ?>" ><?php echo esc_html__('Read More', 'pt_theplus'); ?></a>
	</div>
</div>
</article>
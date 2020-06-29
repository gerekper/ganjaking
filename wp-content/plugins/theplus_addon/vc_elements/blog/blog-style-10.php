<?php 
$postid=get_the_ID();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="blog-list-style-content text-left">
		<?php if ( has_post_thumbnail() ) { 
					include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/format-image.php';
			 }else{ ?>
					<div class="blog-media image-loaded">
						<?php echo pt_plus_loading_image_grid($postid); ?>
					
					</div>
		<?php }	?>
	<div class="post-title-content">
		<div class="post-inner-block">
			<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/post-meta-title.php'; ?>
			<div class="entry-content">
				<p><?php echo pt_plus_excerpt(10); ?></p>
			</div>
			<div class="post-meta-info">
				<span class="meta-date"><a href="<?php echo esc_url(get_the_permalink()); ?> "><span class="entry-date"><?php echo get_the_date('F d Y'); ?></span></a></span> <span class="post-author"> | <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a> </span> 
				 <div class="pt_plus_button button-style-3">
					<a class="button-link-wrap" href="<?php echo esc_url(get_the_permalink()); ?>" title="" target=""><svg class="arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" width="48" height="9" viewBox="0 0 48 9"><path d="M48.000,4.243 L43.757,8.485 L43.757,5.000 L0.000,5.000 L0.000,4.000 L43.757,4.000 L43.757,0.000 L48.000,4.243 Z" class="cls-1"></path></svg><svg class="arrow-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" width="48" height="9" viewBox="0 0 48 9"><path d="M48.000,4.243 L43.757,8.485 L43.757,5.000 L0.000,5.000 L0.000,4.000 L43.757,4.000 L43.757,0.000 L48.000,4.243 Z" class="cls-1"></path></svg></a>
				 </div>
			</div>
		</div>
	</div>
</div>
</article>

<?php $postid=get_the_ID(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="blog-list-style-content ">
		<?php 
		if ( has_post_thumbnail() ) {
		$data_attr=pt_plus_loading_bg_image($postid);
		}else{
		 $data_attr = pt_plus_loading_image_grid($postid,'background');
		}
		?>
	
	<div class="post-title-content">
	<div class="post-category-list">
		<?php	echo get_the_category_list(', '); ?>
	</div>
	<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/post-meta-title.php'; ?>
	</div>
	<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/blog-post-meta-4.php'; ?>
	<div class="pt-theplus-post-read-more-button pt_plus_button  button-style-1">
		<a class="button-link-wrap" href="<?php echo esc_url(get_the_permalink()); ?>" title="<?php echo __('Read More', 'pt_theplus'); ?>"><?php echo __('Read More', 'pt_theplus'); ?><div class="button_line"></div></a>
	</div>
	<div class="post-featured-image " <?php echo $data_attr; ?>>
	</div>
</div>
</article>
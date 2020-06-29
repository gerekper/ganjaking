<?php 
$postid=get_the_ID();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="blog-list-style-content ">
		<?php if ( has_post_thumbnail() ) {
		$data_attr=pt_plus_loading_bg_image($postid);
		}else{
		 $data_attr = pt_plus_loading_image_grid($postid,'background');
		} ?>
	<div class="post-featured-image" <?php echo $data_attr; ?>>
		<div class="post-block-inner">
			<div class="post-category-list">
				<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/meta-date.php'; ?><?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/meta-comment.php'; ?>
			</div>
			<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/post-meta-title.php'; ?>
			
			<div class="entry-content">
				<p><?php echo pt_plus_excerpt(30); ?></p>
			</div>
			<a href="<?php echo esc_url(get_the_permalink()); ?>" class="hover-button"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
		</div>
	</div>
	
</div>
</article>
<?php 
$postid=get_the_ID();
	if($layout=='metro' || $layout=='carousel'){
		if ( has_post_thumbnail() ) {
			$data_attr=pt_plus_loading_bg_image($postid);
		}else{
			$data_attr = pt_plus_loading_image_grid($postid,'background');
		}
	}else{
		$data_attr='';
	}
	?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="blog-list-style-content lazyload" <?php echo $data_attr; ?>>
	<div class="post-content-data">
		<div class="content-column column-date">
			<a href="<?php echo esc_url(get_the_permalink()); ?> "><div class="entry-date"><?php echo get_the_date('d'); ?></div><div><?php echo get_the_date('M'); ?></div></a>
		</div>
		<div class="content-column">
			<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/post-meta-title.php'; ?>
			<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/blog-post-meta-9.php'; ?>
		</div>
	</div>
</div>
</article>

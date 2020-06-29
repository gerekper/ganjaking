<?php 
	$postid=get_the_ID();
	if($layout=='metro'){
		if ( has_post_thumbnail() ) {
			$data_attr=pt_plus_loading_bg_image($postid);
		}else{
			$data_attr = pt_plus_loading_image_grid($postid,'background');
		}
	}else{
		$data_attr='';
	}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('hover-tilt'); ?>>
<div class="blog-list-style-content text-left"<?php echo $data_attr; ?>>
	<?php if($layout!='metro'){
		 if ( has_post_thumbnail() ) { ?>
				<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/format-image.php'; ?>
		<?php }else{ ?>
				<div class="blog-featured-image">
					<?php echo pt_plus_loading_image_grid($postid); ?>
				</div>
		<?php }
		} ?>
	<div class="post-title-content">
		<div class="grid_overlay"></div>
		<div class="post-inner-block">
			<?php include THEPLUS_PLUGIN_PATH. 'vc_elements/blog/post-meta-title.php'; ?>
			<div class="column-date">
				<span class="meta-category"><?php	echo get_the_category_list(' | '); ?></span><span class="meta-date"><a href="<?php echo esc_url(get_the_permalink()); ?> "><?php echo get_the_date('F d Y'); ?></a></span>
			</div>
		</div>
	</div>
</div>
</article>
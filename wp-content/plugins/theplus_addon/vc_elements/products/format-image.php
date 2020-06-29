<?php 
	global $post;
	$postid = get_the_ID();

	if(isset($layout) && $layout=='grid'){
		
		$featured_image=get_the_post_thumbnail_url(get_the_ID(),'tp-image-grid');
		$featured_image='<img src="'.esc_url($featured_image).'" alt="'.esc_attr(get_the_title()).'">';
		
	}else if(isset($layout) && $layout=='masonry'){
		$featured_image=get_the_post_thumbnail_url(get_the_ID(),'full');
		$featured_image='<img src="'.esc_url($featured_image).'" alt="'.esc_attr(get_the_title()).'">';
	}else{
		$featured_image=get_the_post_thumbnail_url(get_the_ID(),'full');
		$featured_image='<img src="'.esc_url($featured_image).'" alt="'.esc_attr(get_the_title()).'">';
	}
?>
	<div class="product-featured-image">
	<span class="thumb-wrap">
		<?php echo $featured_image; ?>
	</span>
	</div>
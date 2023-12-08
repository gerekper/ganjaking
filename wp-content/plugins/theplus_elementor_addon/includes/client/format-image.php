<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if($clientContentFrom == 'clrepeater'){
	$featured_image_url = $clientImage;
}else{
	global $post;
	$postid = get_the_ID();
	$featured_image_url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
}

$tsize='';
if( !empty($featured_image_url) ){
	if($display_thumbnail == 'yes' && !empty($thumbnail)){
		$tsize = $thumbnail;
	}else{
		$tsize = 'full';
	}

	if($clientContentFrom == 'clrepeater'){
		$feat_id = $clientImageId;
		if( !empty($feat_id) ){
			$featured_image = tp_get_image_rander($feat_id, $tsize);
		}
	}else{
		$featured_image = tp_get_image_rander(get_the_ID(), $tsize, [], 'post');
	}
}else{
	$featured_image = theplus_get_thumb_url();
	if($clientContentFrom == 'clrepeater'){
		$featured_image = '<img src="'.esc_url($featured_image).'" alt="'.esc_attr($clientLinkMaskLabel).'">';
	}else{
		$featured_image = '<img src="'.esc_url($featured_image).'" alt="'.esc_attr(get_the_title()).'">';
	}
}
?>

<div class="client-featured-logo">
	<span class="thumb-wrap">
		<?php echo $featured_image; ?>
	</span>
</div>
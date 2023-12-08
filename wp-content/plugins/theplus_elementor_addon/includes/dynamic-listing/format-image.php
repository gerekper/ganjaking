<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	global $post;
	$postid = get_the_ID();
	$featured_image_url = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );
	$tsize='';
	if(! empty( $featured_image_url )){
		if(!empty($layout) && $layout=='grid'){
			if((!empty($display_thumbnail) && $display_thumbnail=='yes') && !empty($thumbnail)){
				$tsize = $thumbnail;
			}else{
				$tsize = 'tp-image-grid';				
			}
			$featured_image= tp_get_image_rander( get_the_ID(), $tsize,[], 'post' );
			
		}else if(!empty($layout) && $layout=='masonry'){
			if((!empty($display_thumbnail) && $display_thumbnail=='yes') && !empty($thumbnail)){		
				$tsize = $thumbnail;
			}else{
				$tsize = 'full';
			}
			$featured_image= tp_get_image_rander( get_the_ID(), $tsize,[], 'post' );
			
		}else if(!empty($layout) && $layout=='carousel'){
			
			if(empty($featured_image_type)){
				$featured_image_type='full';				
			}else{
				if($featured_image_type=='grid'){
				 $featured_image_type='tp-image-grid';
				}else if($featured_image_type=='custom'){
					 $featured_image_type=$thumbnail_car;
				}
			}
			$featured_image= tp_get_image_rander( get_the_ID(), $featured_image_type,[], 'post' );
		}else{
			$featured_image= tp_get_image_rander( get_the_ID(), 'full',[], 'post' );
		}
	}else{
		$featured_image=theplus_get_thumb_url();
		$featured_image=$featured_image='<img src="'.esc_url($featured_image).'" alt="'.esc_attr(get_the_title()).'">';
	}
	$fis_class='';
	if(!empty($full_image_size) && $full_image_size=='yes'){
		$fis_class = ' tp-cst-img-full img';
	}
?>
	<div class="blog-featured-image <?php echo $fis_class; ?>">
	<span class="thumb-wrap">
		<?php echo $featured_image; ?>
	</span>
	</div>
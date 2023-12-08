<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	if($attachment){
		$featured_image_id = $attachment->ID;
	}else{
		$featured_image_id = $image_id;
	}
	$tsize='';
	if(!empty($featured_image_id) && !empty($display_thumbnail) && $display_thumbnail=='yes' && !empty($thumbnail)){
		if(!empty($layout) && ($layout=='grid' || $layout=='masonry')){
			$tsize = $thumbnail;
		}
		$featured_image= tp_get_image_rander( $featured_image_id, $tsize);
	}else if(! empty( $featured_image_id )){
		if(!empty($layout) && $layout=='grid'){			
			$tsize = 'tp-image-grid';
		}else if(!empty($layout) && $layout=='masonry'){		
			$tsize = 'full';
		}else if(!empty($layout) && $layout=='carousel'){		
			//custom size carousel image
			if(!empty($featured_image_type) && $featured_image_type=='custom' && !empty($thumbnail_carousel)){
				$tsize = $thumbnail_carousel;
			}else if(empty($featured_image_type) || $featured_image_type=='full'){				
				$tsize = 'full';
			}else{
				if($featured_image_type=='grid'){				 
					$tsize = 'tp-image-grid';
				}
			}
		}else{			
			$tsize = 'full';
		}
		
		$featured_image= tp_get_image_rander( $featured_image_id, $tsize);
	}else{
		$featured_image=theplus_get_thumb_url();
		$featured_image=$featured_image='<img src="'.esc_url($featured_image).'" alt="'.esc_attr($image_alt).'">';
	}
	
?>
	<div class="gallery-image">
	<span class="thumb-wrap">
		<?php echo $featured_image; ?>
	</span>
	</div>
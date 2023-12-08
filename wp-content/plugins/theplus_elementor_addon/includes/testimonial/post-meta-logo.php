<?php 
// Exit if accessed directly
if (!defined('ABSPATH')) exit; 

if($tlContentFrom == 'tlrepeater'){
	$logo_id = $testiLogoId;	
	if($display_thumbnail == 'yes' && !empty($thumbnail)){
		if(!empty($logo_id)){					
			$testimonial_logo = tp_get_image_rander($logo_id, $thumbnail);
		}
	}

	$testimonial_logo = $testiLogo;
}else{
	if($display_thumbnail == 'yes' && !empty($thumbnail)){
		$testimonial_logo= tp_get_image_rander(get_the_ID(), $thumbnail,[], 'post');		
	}else{
		$testimonial_logo = get_post_meta(get_the_id(), 'theplus_testimonial_logo', true); 		
	}
}
	
if( !empty($testimonial_logo) ){ ?>
	<div class="testimonial-author-logo"><img src="<?php echo esc_url($testimonial_logo); ?>" /></div>
<?php } ?>
<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if(!isset($post_title_tag) && empty($post_title_tag)){
	$post_title_tag='h3';
}

$title_text_main=esc_html(get_the_title());
$title_text=''; 
if((!empty($display_title_limit) && $display_title_limit=='yes') && !empty($display_title_input)){
		if(!empty($display_title_by)){				
			if($display_title_by=='char'){												
				$title_text = substr($title_text_main,0,$display_title_input);								
			}else if($display_title_by=='word'){
				$title_text = limit_words($title_text_main,$display_title_input);					
			}
		}				
		if($display_title_by=='char'){
			if(strlen($title_text_main) > $display_title_input){
				if(!empty($display_title_3_dots) && $display_title_3_dots=='yes'){
					$title_text .='...';
				}
			}
		}else if($display_title_by=='word'){
			if(str_word_count($title_text_main) > $display_title_input){
				if(!empty($display_title_3_dots) && $display_title_3_dots=='yes'){
					$title_text .='...';
				}
			}
		}
}else{
	$title_text=esc_html(get_the_title());
}
			?>
<<?php echo theplus_validate_html_tag($post_title_tag); ?> class="post-title">
	<a href="<?php echo esc_url(get_the_permalink()); ?>" class="<?php echo $title_desc_word_break; ?>"><?php echo $title_text; ?></a>
</<?php echo theplus_validate_html_tag($post_title_tag); ?>>
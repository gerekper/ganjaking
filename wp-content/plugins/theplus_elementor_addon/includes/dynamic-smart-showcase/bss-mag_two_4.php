<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$postid=get_the_ID();
$bg_attr=$lazyclass='';
	if((!empty($display_thumbnail) && $display_thumbnail=='yes') && !empty($thumbnail)){
		$featured_image=get_the_post_thumbnail_url($postid,$thumbnail);
		if ( !empty($featured_image) ) {
			$bg_attr='style="background:url('.$featured_image.') #f7f7f7;"';			
		}else{
			$bg_attr = theplus_loading_image_grid($postid,'background');			
		}
	}else{
		$featured_image=get_the_post_thumbnail_url($postid,'full');
		if ( !empty($featured_image) ) {
			$bg_attr=theplus_loading_bg_image($postid);			
		}else{
			$bg_attr = theplus_loading_image_grid($postid,'background');			
		}
	}
	
	if(tp_has_lazyload()){
		$lazyclass=' lazy-background';
	}
?>

<div class="bss-wrapper">
	<div class="bss-wrap <?php echo esc_attr($lazyclass); ?>" <?php echo $bg_attr; ?> >	
		
		<?php if($display_post_category=='yes'){ ?>
				<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/blog-category-'.$post_category_style.'.php'; ?>
		<?php } ?>		
		<div class="bss-content">
			<div class="bss-content-inner">
				<div class="bss-meta-content"><a href="<?php echo esc_url(get_the_permalink()); ?>" class="bss-meta-content-link">
					<?php if(!empty($display_post_meta) && $display_post_meta=='yes'){ ?>
							<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/blog-post-meta-'.$post_meta_tag_style.'.php'; ?>
					<?php } ?></a>
				</div>
				<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/post-meta-title.php'; ?>												
				<?php if(!empty($display_excerpt) && $display_excerpt=='yes' && get_the_excerpt()){ 
						include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/get-excerpt.php';
					} 
				?>				
			</div>
		</div>
		<a href="<?php echo esc_url(get_the_permalink()); ?>" class="bss-title-link" tabindex="0"></a>
	</div>	
</div>

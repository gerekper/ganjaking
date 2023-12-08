<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$postid=get_the_ID();
?>
<div class="bss-wrapper">
			<div class="post-content-image-tag">
				<div class="tagimg">
					<a href="<?php echo esc_url(get_the_permalink()); ?>" class="bss-img-st2">
						<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/format-image.php'; ?>
						<?php if($display_post_category=='yes'){ ?>
							<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/blog-category-'.$post_category_style.'.php'; ?>
						<?php } ?>	
					</a>
				</div>
				<div class="post-content-wrapper">					
						
						<div class="bss-content">
								<div class="bss-meta-content"><a href="<?php echo esc_url(get_the_permalink()); ?>" class="bss-meta-content-link">
									<?php if(!empty($display_post_meta) && $display_post_meta=='yes'){ ?>
											<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/blog-post-meta-new-'.$post_meta_tag_style.'.php'; ?>
									<?php } ?></a>																	
								</div>		
								<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/post-meta-title.php';
								if(!empty($display_excerpt) && $display_excerpt=='yes' && get_the_excerpt()){ ?>
									<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/get-excerpt.php'; ?>									
								<?php } ?>								
					</div>
				</div>
			</div>
			<div class="post-content-remain-list">	
				<a href="<?php echo esc_url(get_the_permalink()); ?>" class="bss-remain-img">
					<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/format-image.php'; ?>
				</a>
				<div class="bss-content">				
						<div class="bss-meta-content"><a href="<?php echo esc_url(get_the_permalink()); ?>" class="bss-meta-content-link">
							<?php if(!empty($display_post_meta) && $display_post_meta=='yes'){ ?>
									<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/blog-post-meta-'.$post_meta_tag_style.'.php'; ?>
							<?php } ?></a>
						</div>
						<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/post-meta-title.php'; 
						if(!empty($display_excerpt) && $display_excerpt=='yes' && get_the_excerpt()){ ?>
									<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/get-excerpt.php'; ?>									
								<?php } ?>	
				</div>
			</div>		
</div>
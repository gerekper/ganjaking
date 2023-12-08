<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$postid=get_the_ID();
$bg_attr=$lazyclass='';
if($layout=='metro'){	
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
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="blog-list-content">
		<?php if($layout!='metro'){ ?>
		<div class="post-content-image">
			<a href="<?php echo esc_url(get_the_permalink()); ?>">
				<?php include THEPLUS_INCLUDES_URL. 'dynamic-listing/format-image.php'; ?>
			</a>			
		</div>
		<?php } ?>
		<div class="post-content-bottom">
			<?php if(!empty($display_post_meta) && $display_post_meta=='yes'){ ?>
				<?php include THEPLUS_INCLUDES_URL. 'dynamic-listing/dl-post-meta-'.$post_meta_tag_style.'.php'; ?>
			<?php } ?>
			<?php include THEPLUS_INCLUDES_URL. 'dynamic-listing/post-meta-title.php'; ?>
			<div class="post-hover-content">
				<?php if(!empty($display_excerpt) && $display_excerpt=='yes' && get_the_excerpt()){ 
					include THEPLUS_INCLUDES_URL. 'dynamic-listing/get-excerpt.php';
				} 
				/*tp quick view start*/
				if(!empty($display_theplus_quickview) && $display_theplus_quickview=='yes'){
				?>								
					<div class="tp-dl-quickview" style="opacity: 1;">
						<a href="#" class="tp-quick-view-wrap-dl" data-product-id="<?php the_ID(); ?>"><i aria-hidden="true" class="fas fa-eye"></i></a>
					</div> <?php
				}
				/*tp quick view end*/
				?>								
			</div>
		</div>
		<?php if($layout=='metro'){ ?>
			<a href="<?php echo esc_url(get_the_permalink()); ?>"><div class="blog-bg-image-metro <?php echo esc_attr($lazyclass); ?>" <?php echo $bg_attr; ?> ></div></a>
		<?php }
			include THEPLUS_INCLUDES_URL. 'dynamic-listing/blog-skeleton.php';
		?>
	</div>
</article>

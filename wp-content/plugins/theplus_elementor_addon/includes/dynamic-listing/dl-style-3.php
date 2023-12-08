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
$column_class_1=$column_class_2='';
if(!empty($style_layout) && $style_layout=='layout-style-1'){
	$column_class_1='tp-col-12 tp-col-md-4 tp-col-sm-4 tp-col-xs-12';
	$column_class_2='tp-col-12 tp-col-md-8 tp-col-sm-8 tp-col-xs-12';
}else if(!empty($style_layout) && $style_layout=='layout-style-2'){
	$column_class_1='tp-col-12 tp-col-md-6 tp-col-sm-6 tp-col-xs-12';
	$column_class_2='tp-col-12 tp-col-md-6 tp-col-sm-6 tp-col-xs-12';
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="blog-list-content d-flex flex-row flex-wrap tp-align-items-center">
		<?php if($layout!='metro'){ ?>
		<div class="post-content-image <?php echo esc_attr($column_class_1); ?> flex-column flex-wrap">
			<a href="<?php echo esc_url(get_the_permalink()); ?>">
				<?php include THEPLUS_INCLUDES_URL. 'dynamic-listing/format-image.php'; ?>
			</a>
		</div>
		<?php } ?>
		<div class="post-content-bottom <?php echo esc_attr($column_class_2); ?> flex-column flex-wrap">
			<?php if(!empty($style_layout)){
				include THEPLUS_INCLUDES_URL. 'dynamic-listing/dl-style-3-'.$style_layout.'.php';
			}else{
				include THEPLUS_INCLUDES_URL. 'dynamic-listing/dl-style-3-layout-style-1.php';
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
		<?php if($layout=='metro'){ ?>
		<a href="<?php echo esc_url(get_the_permalink()); ?>"><div class="blog-bg-image-metro <?php echo esc_attr($lazyclass); ?>" <?php echo $bg_attr; ?>></div></a>
		<?php }
				include THEPLUS_INCLUDES_URL. 'dynamic-listing/blog-skeleton.php';
		?>
	</div>
</article>

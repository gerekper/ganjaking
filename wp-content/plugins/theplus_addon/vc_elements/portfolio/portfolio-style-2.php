<?php 
	global $post;
	$postid=get_the_ID();
	$data_attr='';
	if($layout=='metro'){
		if ( has_post_thumbnail() ) {
			$data_attr=pt_plus_loading_bg_image($postid);
		}else{
			$data_attr = pt_plus_loading_image_grid($postid,'background');
		}
	}
	$page_url='';
	if(!empty(Pt_plus_MetaBox::get("theplus_portfolio_page_url"))){
		$page_url=Pt_plus_MetaBox::get("theplus_portfolio_page_url");
	}else{
		$page_url=get_permalink();
	}
?>
<a href="<?php echo esc_url( $page_url ); ?>" class="portfolio-item " <?php echo $data_attr; ?>>
	<?php 
	if($layout!='metro'){
		if ( has_post_thumbnail() ) {
			require THEPLUS_PLUGIN_PATH .'vc_elements/portfolio/featured-image.php';
		}else{ ?>
			<div class="tp-portfolio-image">
				<?php echo pt_plus_loading_image_grid($postid); ?>
			</div>
	<?php }
	}
	?>
	<div class="portfolio-item-hover" style="background-color: <?php echo Pt_plus_MetaBox::get("theplus_portfolio_primary_color");?>">
		<?php if(Pt_plus_MetaBox::get("theplus_portfolio_logo_img")!=''){ ?>
			<img src="<?php echo esc_url(Pt_plus_MetaBox::get("theplus_portfolio_logo_img")); ?>" class="portfolio-logo-image" alt="<?php echo esc_attr(get_the_title()); ?>" />
		<?php } ?>
		<div class="portfolio-categories-info">
			<?php 
			$taxonomy=pt_plus_portfolio_post_category();
			$terms = get_the_terms( get_the_ID(),$taxonomy);
				if ( $terms != null ){
					foreach( $terms as $term ) {
						echo '<span>'.$term->name.'</span>';
					unset($term);
					}
				}
			?>
		</div>				
	</div>
	<div class="portfolio-list-title">
		<?php require THEPLUS_PLUGIN_PATH .'vc_elements/portfolio/post-meta-title.php'; ?>
		<h3 class="portfolio-subtitle"><?php echo Pt_plus_MetaBox::get("theplus_portfolio_subtitle"); ?></h3>
	</div>
</a>
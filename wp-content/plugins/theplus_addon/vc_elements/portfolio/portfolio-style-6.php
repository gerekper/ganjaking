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
<a href="<?php echo esc_url( $page_url ); ?>" class="portfolio-item loading-image-bg" <?php echo $data_attr; ?>>
	<div class="portfolio-item-content">
		<div class="separators">
			<span class="separator-1" style="background:<?php echo Pt_plus_MetaBox::get("theplus_portfolio_primary_color");?>;"></span>
			<span class="separator-2" style="background:<?php echo Pt_plus_MetaBox::get("theplus_portfolio_primary_color");?>;"></span>
		</div>
		<div class="portfolio-item-img">
			<div class="post_overlay">
			<?php 
				if(Pt_plus_MetaBox::get("theplus_portfolio_logo_img")!=''){
					echo '<img src="'.esc_url(Pt_plus_MetaBox::get("theplus_portfolio_logo_img")).'" alt="" class="portfolio-logo-image" />';
				}
			?>
			</div>
			<div class="portfolio-item-hover" >
				<div class="portfolio-list-title">
					<?php require THEPLUS_PLUGIN_PATH .'vc_elements/portfolio/post-meta-title.php'; ?>
					<h3 class="portfolio-subtitle"><?php echo Pt_plus_MetaBox::get("theplus_portfolio_subtitle");?></h3>
				</div>
				<div class="post_overlay"></div>
			</div>
			<?php 
				if($layout!='metro'){
					if ( has_post_thumbnail() ) {
						require THEPLUS_PLUGIN_PATH .'vc_elements/portfolio/featured-image.php';
					}else{ ?>
					<div class="tp-portfolio-image">
						<?php echo pt_plus_loading_image_grid($postid); ?>
					</div>
				<?php }
			} ?>
		</div>
	</div>
</a>
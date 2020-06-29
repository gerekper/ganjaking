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
<a href="<?php echo esc_url( $page_url ); ?>" class="portfolio-item">
	<div class="portfolio-item-content">
		<div class="portfolio-item-img">
			<div class="portfolio-item-hover" style="background-color: <?php echo Pt_plus_MetaBox::get("theplus_portfolio_primary_color"); ?>">
				<div class="portfolio-list-title">
					<?php require THEPLUS_PLUGIN_PATH .'vc_elements/portfolio/post-meta-title.php'; ?>
				</div>
				<?php echo $portfolio_category;
					$port_date= Pt_plus_MetaBox::get("theplus_portfolio_date_custom");
					$portdate=date("d M", strtotime($port_date));
					if($port_date!=''){ ?>
						<div class="portfolio-year"><?php echo esc_html($portdate); ?></div>
				<?php } ?>
			</div>
			<?php 
				if($layout!='metro'){
					if ( has_post_thumbnail() ) {
						require THEPLUS_PLUGIN_PATH .'vc_elements/portfolio/featured-image.php';
					}else{ ?>
					<div class="image-loaded">
							<?php echo pt_plus_loading_image_grid($postid); ?>
					</div>
				<?php }
				}?>
		</div>
	</div>
	<?php if($layout=='metro'){ ?>
	<div class="portfolio-parallax-bg-image" <?php echo $data_attr; ?>></div>
	<?php } ?>
</a>
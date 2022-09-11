<?php	
/*
 *	The template for displaying event categoroes - event location 
 * 	In order to customize this archive page template
 *	Override this template by coping it to ../yourtheme/eventon/ folder
 
 *	@Author: AJDE
 *	@EventON
 *	@version: 2.6.14
 */	
	

	evo_get_page_header();


	$tax = get_query_var( 'taxonomy' );
	$term = get_query_var( 'term' );

	$term = get_term_by( 'slug', $term, $tax );

	do_action('eventon_before_main_content');

	$term_meta = evo_get_term_meta( 'event_location',$term->term_id );
	//$term_meta = get_option( "taxonomy_".$term->term_id );

	// location image
		$img_url = false;
		if(!empty($term_meta['evo_loc_img'])){
			$img_url = wp_get_attachment_image_src($term_meta['evo_loc_img'],'full');
			if($img_url) $img_url = $img_url[0];
		}

	//location address
		$location_address = $location_latlan = false;
		$location_type = 'add';

			$location_latlan = (!empty($term_meta['location_lat']) && $term_meta['location_lon'])?
				$term_meta['location_lat'].','.$term_meta['location_lon']:false;

		if(empty($term_meta['location_address'])){
			if($location_latlan){
				$location_type ='latlng';
				$location_address = true;
			}
		}else{
			if($location_latlan) $location_type = 'latlng';
			$location_address = stripslashes($term_meta['location_address']);
		}
		
	// location link
		$location_link_target = (!empty($term_meta['evcal_location_link_target']) && $term_meta['evcal_location_link_target'] == 'yes')? '_blank':'';
		$location_link_a = (!empty($term_meta['evcal_location_link']))? 
			'<a target="'.$location_link_target.'" href="'. evo_format_link($term_meta['evcal_location_link']) .'">': false;
		$location_link_b = $location_link_a? '</a>':false;

?>

<div class='wrap evotax_term_card evo_location_card'>	
	
	<div id='primary' class="content-area">

		<div class='eventon site-main'>

			<header class='page-header'>
				<h1 class="page-title"><?php evo_lang_e('Events at this location');?></h1>
			</header>

			<div class='entry-content'>
				<div class="evo_location_tax evotax_term_details" style='background-image:url(<?php echo $img_url;?>)'>
					
					<?php if($img_url):?>
						<div class="location_circle term_image_circle" style='background-image:url(<?php echo $img_url;?>)'></div>
					<?php endif;?>
					
					<h2 class="location_name tax_term_name"><span><?php echo $location_link_a . $term->name . $location_link_b;?></span></h2>
					<?php if($location_type=='add'):?><p class="location_address"><span><i class='fa fa-map-marker'></i> <?php echo $location_address;?></span></p><?php endif;?>
					<div class='location_description tax_term_description'><?php echo category_description();?></div>
				</div>
				
				<?php if($location_address):
					EVO()->cal->set_cur('evcal_1');
					$zoomlevel = EVO()->cal->get_prop('evcal_gmap_zoomlevel');
						if(!$zoomlevel) $zoomlevel = 16;

					$map_type = EVO()->cal->get_prop('evcal_gmap_format');
						if(!$map_type) $map_type = 'roadmap';

					$eventtop_style = EVO()->cal->get_prop('evosm_eventtop_style','evcal_1') == 'white'? '0':'2';
				?>
					<div id='evo_locationcard_gmap' class="evo_location_map" data-address='<?php echo $location_address;?>' data-latlng='<?php echo $location_latlan;?>' data-location_type='<?php echo $location_type;?>'data-zoom='<?php echo $zoomlevel;?>' data-scroll='<?php echo EVO()->cal->check_yn('evcal_gmap_scroll')? 'no':'yes';?>' data-mty='<?php echo $map_type;?>'></div>
				<?php endif;?>

				<h3 class="location_subtitle evotax_term_subtitle"><?php evo_lang_e('Events at this location');?></h3>
				
				<?php 
					$shortcode = apply_filters('evo_tax_archieve_page_shortcode', 
						'[add_eventon_list number_of_months="5" '.$tax.'='.$term->term_id.' hide_mult_occur="no" hide_empty_months="yes" eventtop_style="'. $eventtop_style.'"]', 
						$tax,
						$term->term_id
					);
					echo do_shortcode($shortcode);
				?>

			</div>
		</div>
	</div>

	<?php evo_get_page_sidebar(); ?>

</div>

<?php	do_action('eventon_after_main_content'); ?>

<?php 

	evo_get_page_footer();


?>
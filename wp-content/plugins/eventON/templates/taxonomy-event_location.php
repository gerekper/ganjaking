<?php	
/*
 *	The template for displaying event categoroes - event location 
 * 	In order to customize this archive page template
 *	Override this template by coping it to ../yourtheme/eventon/ folder
 
 *	@Author: AJDE
 *	@EventON
 *	@version: 4.4.4
 */	
	

	evo_get_page_header();

	$help = new evo_helper();

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

		$location_term_link = !empty($term_meta['evcal_location_link']) ? evo_format_link($term_meta['evcal_location_link']) : false;

		$location_term_name = $location_term_link ? 
			'<a target="'.$location_link_target.'" href="'. $location_term_link .'">' .  $term->name . '</a>':
			 $term->name;


?>

<div class='wrap evotax_term_card evo_location_card alignwide'>	
	<div class='evo_card_wrapper'>	

		<div id='' class="content-area">

			<div class='eventon site-main'>

				<header class='page-header'>
					<h1 class="page-title"><?php evo_lang_e('Events at this location');?></h1>
				</header>

				<div class='entry-content'>
					
					<div class='evo_term_top_section dfx'>

						<?php if($img_url):?>
							<div class="evotax_term_img" style='background-image:url(<?php echo $img_url;?>)'>
							</div>	
						<?php endif;?>

						<div class='evo_tax_details'>
							<h2 class="location_name tax_term_name evo_h2 ttu"><span><?php echo $location_term_name;?></span></h2>
							
							<?php if($location_address):?>
								<p class="location_address mar0 pad0"><i class='fa fa-map-marker marr10'></i> <?php echo $location_address;?></p>
							<?php endif;?>
							
							<div class='location_description tax_term_description'>
								<?php echo category_description();?>								
							</div>

							<?php if( $location_term_link):?>
								<p class='mar0 pad0'><a class='evo_btn evcal_btn' href='<?php echo $location_term_link;?>' target='<?php echo $location_link_target;?>'><?php evo_lang_e('Learn More');?></a></p>
							<?php endif;?>
						</div>

					</div>
					
					<?php 

					// location google map
					if($location_address):
						EVO()->cal->set_cur('evcal_1');
						$zoomlevel = EVO()->cal->get_prop('evcal_gmap_zoomlevel');
							if(!$zoomlevel) $zoomlevel = 16;

						$map_type = EVO()->cal->get_prop('evcal_gmap_format');
							if(!$map_type) $map_type = 'roadmap';

						$eventtop_style = EVO()->cal->get_prop('evosm_eventtop_style','evcal_1') == 'white'? '0':'2';

						$map_data = array(
							'address'=> $location_address,
							'latlng'=> $location_latlan,
							'location_type'=> $location_type,
							'zoom'=> $zoomlevel,
							'scroll'=> EVO()->cal->check_yn('evcal_gmap_scroll')? 'no':'yes',
							'mty'=>$map_type,
							'delay'=>400
						);
					?>
						<div id='evo_event_location_term_<?php echo $term->term_id;?>' class="evo_trigger_map evo_location_map" <?php echo $help->array_to_html_data($map_data);?>></div>
					<?php endif;?>


					<div class='evo_term_events'>
						<h3 class="location_subtitle evotax_term_subtitle border marb30"><?php evo_lang_e('Events at this location');?></h3>
						
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
		</div>

		<?php evo_get_page_sidebar(); ?>
	</div>
</div>

<?php	do_action('eventon_after_main_content'); ?>

<?php 	evo_get_page_footer(); ?>
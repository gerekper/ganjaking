<?php	
/*
 *	The template for displaying event categoroes - event organizer 
 *
 *	Override this template by coping it to ../yourtheme/eventon/ folder
 
 *	@Author: AJDE
 *	@EventON
 *	@version: 4.2
 */	
	

	evo_get_page_header();

	$help = new evo_helper();


	$tax = get_query_var( 'taxonomy' );
	$term = get_query_var( 'term' );

	$term = get_term_by( 'slug', $term, $tax );

	do_action('eventon_before_main_content');

	//$term_meta = get_option( "taxonomy_".$term->term_id );
	$term_meta = evo_get_term_meta( 'event_organizer',$term->term_id );

	// organizer image
		$img_url = false;
		if(!empty($term_meta['evo_org_img'])){
			$img_url = wp_get_attachment_image_src($term_meta['evo_org_img'],'full');
			$img_url = $img_url[0];
		}

	// organizer link
		$organizer_link_target = (!empty($term_meta['_evocal_org_exlink_target']) && $term_meta['_evocal_org_exlink_target'] == 'yes')? '_blank':'';

		$organizer_term_link = !empty($term_meta['evcal_org_exlink']) ? evo_format_link($term_meta['evcal_org_exlink']): false;

		$organizer_term_name = $organizer_term_link ? '<a target="'.$organizer_link_target.'" href="'. $organizer_term_link .'">' . $term->name . '</a>' : $term->name; 

?>

<div class='wrap evotax_term_card evo_organizer_card alignwide'>	

	<div id='' class="content-area">

		<div class='eventon site-main'>
			<header class='page-header'>
				<h1 class="page-title"><?php evo_lang_e('Events by this organizer');?></h1>
			</header>

			<div class='entry-content'>

				<div class='evo_term_top_section dfx'>
					
					<?php if($img_url):?>
						<div class="evotax_term_img" style='background-image:url(<?php echo $img_url;?>)'>
						</div>	
					<?php endif;?>						
					

					<div class='evo_tax_details'>
						<h2 class="tax_term_name organizer_name evo_h2 ttu"><span><?php echo $organizer_term_name;?></span></h2>		
						<?php 

						echo category_description();
						
						if(!empty($term_meta['evcal_org_contact'])){						
							echo "<p class='contactinfo border marb10'>". $term_meta['evcal_org_contact'] ."</p>";
						}
						if(!empty($term_meta['evcal_org_contact_e'])){						
							echo "<p class='contactinfo email border marb10'>". $term_meta['evcal_org_contact_e'] ."</p>";
						}


						echo (!empty($term_meta['evcal_org_address']))? '<p class="org_address border marb10">'.$term_meta['evcal_org_address'].'</p>':null; 


						// social media links
						$social_html = '';
						foreach(apply_filters('evo_organizer_archive_page_social', array(
							'twitter'=>'evcal_org_tw',
							'facebook'=>'evcal_org_fb',
							'linkedin'=>'evcal_org_ln',
							'youtube'=>'evcal_org_yt'
						)) as $f=>$k){
							if(!isset($term_meta[$k])) continue;

							$social_html .= "<a href='". $term_meta[$k]. "'><i class='fa fa-{$f}'></i></a>";
						}

						if(!empty($social_html)){
							echo "<div class='evo_tax_social_media marb10'>{$social_html}</div>";
						}
						?>			

						<?php if( $organizer_term_link):?>
							<p class='mar0 pad0'><a class='evo_btn evcal_btn' href='<?php echo $organizer_term_link;?>' target='<?php echo $organizer_link_target;?>'><?php evo_lang_e('Learn More');?></a></p>
						<?php endif;?>

					</div>
				</div>



				<?php 
				// location map
				if( !empty($term_meta['evcal_org_address']) ):
					EVO()->cal->set_cur('evcal_1');
					$zoomlevel = EVO()->cal->get_prop('evcal_gmap_zoomlevel');
						if(!$zoomlevel) $zoomlevel = 16;

					$map_type = EVO()->cal->get_prop('evcal_gmap_format');
						if(!$map_type) $map_type = 'roadmap';

					$location_address = stripslashes($term_meta['evcal_org_address']);

					$map_data = array(
						'address'=> $location_address,
						'latlng'=> '',
						'location_type'=> 'add',
						'zoom'=> $zoomlevel,
						'scroll'=> EVO()->cal->check_yn('evcal_gmap_scroll')? 'no':'yes',
						'mty'=>$map_type,
						'delay'=>400
					);
				?>

					<div id='evo_event_organizer_term_<?php echo $term->term_id;?>' class="evo_trigger_map evo_location_map term_location_map" <?php echo $help->array_to_html_data($map_data);?>></div>
				
				<?php endif;?>		
				

				<div class='evo_term_events'>
					<h3 class="evotax_term_subtitle organizer_subtitle border marb30"><?php evo_lang_e('Events by this organizer');?></h3>
				
					<?php 

						$eventtop_style = EVO()->cal->get_prop('evosm_eventtop_style','evcal_1') == 'white'? '0':'2';

						echo EVO()->shortcodes->events_list( apply_filters('evo_tax_archieve_page_shortcode' ,array(
							'number_of_months'=>5,
							'event_organizer'=>$term->term_id,
							'hide_mult_occur'=>'no',
							'hide_empty_months'=>'yes',
							'eventtop_style'=> $eventtop_style
						) , $tax,$term->term_id) );

					?>
				</div>
			</div>
		</div>
	</div>

	<?php evo_get_page_sidebar(); ?>

</div>

<?php	do_action('eventon_after_main_content'); ?>

<?php 	evo_get_page_footer(); ?>
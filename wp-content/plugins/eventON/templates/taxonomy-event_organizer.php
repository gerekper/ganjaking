<?php	
/*
 *	The template for displaying event categoroes - event organizer 
 *
 *	Override this template by coping it to ../yourtheme/eventon/ folder
 
 *	@Author: AJDE
 *	@EventON
 *	@version: 3.0.3
 */	
	
	global $eventon;

	get_header();

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
		$organizer_link_a = (!empty($term_meta['evcal_org_exlink']))? 
			'<a target="'.$organizer_link_target.'" href="'. evo_format_link($term_meta['evcal_org_exlink']) .'">': false;
		$organizer_link_b = ($organizer_link_a)? '</a>':false;
?>

<div class='wrap evotax_term_card evo_organizer_card'>	

	<div id='primary' class="content-area">

		<div class='eventon site-main'>
			<header class='page-header'>
				<h1 class="page-title"><?php evo_lang_e('Events by this organizer');?></h1>
			</header>

			<div class='entry-content'>

				<div class="evo_location_tax evotax_term_details" style='background-image:url(<?php echo $img_url;?>)'>
					
					<?php if($img_url):?>
						<div class="location_circle term_image_circle" style='background-image:url(<?php echo $img_url;?>)'></div>
					<?php endif;?>

					<h2 class="organizer_name tax_term_name"><span><?php echo $organizer_link_a.$term->name.$organizer_link_b;?></span></h2>			
				</div>	

				<div class='evo_tax_details'>
					<?php 

					echo category_description();
					
					if(!empty($term_meta['evcal_org_contact'])){						
						echo "<p class='contactinfo'>". $term_meta['evcal_org_contact'] ."</p>";
					}
					if(!empty($term_meta['evcal_org_contact_e'])){						
						echo "<p class='contactinfo email'>". $term_meta['evcal_org_contact_e'] ."</p>";
					}


					echo (!empty($term_meta['evcal_org_address']))? '<p>'.$term_meta['evcal_org_address'].'</p>':null; 


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
						echo "<div class='evo_tax_social_media'>{$social_html}</div>";
					}
					?>					
				</div>




				<?php if( !empty($term_meta['evcal_org_address']) ):?>
					<div id='evo_locationcard_gmap' class="evo_location_map term_location_map" data-address='<?php echo stripslashes($term_meta['evcal_org_address']);?>' data-latlng='' data-location_type='add'data-zoom='16'></div>
				<?php endif;?>		
				
				<h3 class="evotax_term_subtitle organizer_subtitle"><?php evo_lang_e('Events by this organizer');?></h3>
			
				<?php 

					$eventtop_style = EVO()->cal->get_prop('evosm_eventtop_style','evcal_1') == 'white'? '0':'2';

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

	<?php get_sidebar(); ?>

</div>

<?php	do_action('eventon_after_main_content'); ?>


<?php get_footer(); ?>
<?php
/**
 * Event Taxonomy Class 
 * @version 4.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; 

class EVO_Event_Tax {
	
	function get_organizer_lightbox_content($post){

		$helper = new evo_helper();
		$EVENT = new EVO_Event($post['eventid'], '', isset($post['ri']) ? $post['ri']:'0' );

		$org_data = $EVENT->get_taxonomy_data( 'event_organizer', true, $post['term_id'] );


		if( !$org_data) return $arr;

		$org_data_this = $org_data['event_organizer'][$post['term_id']];

		// organizer link
		$organizer_link_target = (!empty($org_data_this->organizer_link_target) && $org_data_this->organizer_link_target == 'yes')? '_blank':'';

		$organizer_term_link = !empty($org_data_this->organizer_link) ? evo_format_link($org_data_this->organizer_link): false;

		$organizer_term_name = $organizer_term_link ? '<a target="'.$organizer_link_target.'" href="'. $organizer_term_link .'">' . $org_data_this->name . '</a>' : $org_data_this->name; 

		$org_img = false;
		if( !empty($org_data_this->img_id) ){
			$img_url = wp_get_attachment_image_src( $org_data_this->img_id ,'full');
			$org_img = isset( $img_url[0] ) ? $img_url[0] : false ;
		}

		ob_start();
		?>
		<div class='evo_event_moreinfo_org pad40'>
			<div class='evo_tax_intro_details'>
				
				<?php 
					// image
					if( $org_img ): 
						?>	
							<div class='evo_tax_img padb20'>
								<img class='borderr15' style='width:100%' src='<?php echo $org_img;?>'/>
							</div>
						<?php 
					endif;

				?>
				
				<div class='evo_tax_details'>
					<h3 class='evo_h3 padt20 padb20 fw900i' style="font-size:36px;"><?php echo $org_data_this->name;?></h3>

					<div class='padb10 evo_org_desc'><?php echo stripslashes( $org_data_this->description );?></div>
					
					<?php
					if(!empty($org_data_this->organizer_contact)){						
						echo "<p class='padb10 marb10i evo_borderb' >". $org_data_this->organizer_contact ."</p>";
					}
					if(!empty($org_data_this->contact_email)){						
						echo "<p class='padb10 marb10i evo_borderb' >". $org_data_this->contact_email ."</p>";
					}

					// physical address
					if(!empty($org_data_this->organizer_address)){						
						echo "<p class='padb10 marb10i evo_borderb' >". $org_data_this->organizer_address ."</p>";
					}
					?>

					<?php 
					// social media links
						$social_html = '';
						foreach( $EVENT->get_organizer_social_meta_array() as $f=>$k){
							if( empty($org_data_this->$f)) continue;

							$social_html .= "<a class='pad10' target='_blank' href='". $org_data_this->$f. "'><i class='fa fa-{$f}'></i></a>";
						}

						if(!empty($social_html)){
							echo "<div class='evo_tax_social_media padt10 padb10'>{$social_html}</div>";
						}
					?>

					<?php if( $organizer_term_link):?>
						<p class='mar0 pad0'><a class='evo_btn evcal_btn' href='<?php echo $organizer_term_link;?>' target='<?php echo $organizer_link_target;?>'><?php evo_lang_e('Learn More');?></a></p>
					<?php endif;?>

				</div>						
			</div>
			
			<?php 
			// organizer location map
				if( !empty($org_data_this->organizer_address) ):
				
				EVO()->cal->set_cur('evcal_1');
				$zoomlevel = EVO()->cal->get_prop('evcal_gmap_zoomlevel');
					if(!$zoomlevel) $zoomlevel = 16;

				$map_type = EVO()->cal->get_prop('evcal_gmap_format');
					if(!$map_type) $map_type = 'roadmap';

				$location_address = stripslashes( $org_data_this->organizer_address );

				$map_data = array(
					'address'=> $location_address,
					'latlng'=>'',
					'location_type'=> 'add',
					'zoom'=> $zoomlevel,
					'scroll'=> EVO()->cal->check_yn('evcal_gmap_scroll')? 'no':'yes',
					'mty'=>$map_type,
					'delay'=>400
				);

			?>
			
				<div id='evo_org_<?php echo $org_data_this->term_id;?>' class="evo_trigger_map borderr15 mart15" style='height:250px;' <?php echo $helper->array_to_html_data($map_data);?>></div>
		
			<?php endif;?>	
			

			<?php do_action('evo_eventcard_organizer_info_before_events', $org_data_this, $EVENT);?>
			
			<div class='mart20'>
				<h3 class="evo_h3" ><?php evo_lang_e('Events by');?> <?php echo $org_data_this->name;?></h3>
			</div>

			<div class='evo_databox borderr15 pad30 mart15'>					
				
				<?php 

				$eventtop_style = EVO()->cal->get_prop('evosm_eventtop_style','evcal_1') == 'white'? '0':'2';

				// event type color override @since 4.5.2
				$etc_override = EVO()->cal->check_yn('evosm_etc_override','evcal_1') ? 'yes':'no';

				
				echo EVO()->shortcodes->events_list( array(
					'number_of_months'=>5,
					'event_organizer'=>$post['term_id'],
					'hide_mult_occur'=>'no',
					'hide_empty_months'=>'yes',
					'eventtop_style'=> $eventtop_style,
					'ux_val'=>3,
					'etc_override'=> $etc_override,
				));

				?>
			</div>
		</div>

		<?php

		return ob_get_clean();
	}

	function get_organizer_data(){
		$O_term = apply_filters('evodata_organizer_term', $this->get_organizer_term_id('all'), $this);
		if($O_term && !is_wp_error( $O_term)){
			$R = array();

			$org_term_meta = evo_get_term_meta( 'event_organizer', (int)$O_term->term_id);
			
			$R['organizer'] = $O_term;
			$R['organizer_term'] = $O_term;
			$R['organizer_term_id'] = (int)$O_term->term_id;
			$R['organizer_name'] = $O_term->name;
			$R['organizer_description'] = $O_term->description;

			$organizer_meta = $this->get_organizer_social_meta_array();
			$organizer_meta['organizer_img_id'] = 'evo_org_img';
			$organizer_meta['organizer_contact'] = 'evcal_org_contact';
			$organizer_meta['organizer_address'] = 'evcal_org_address';
			$organizer_meta['organizer_link'] = 'evcal_org_exlink';
			$organizer_meta['organizer_link_target'] = '_evocal_org_exlink_target';

			// meta values
			foreach($organizer_meta as $I=>$key){	
				$K = is_integer($I)? $key: $I;				
				$R[$K] = (empty($org_term_meta[$key]))? '': $org_term_meta[$key];
			}				

			return $R;
		}else{
			return false;
		}
	}
	function get_organizer_social_meta_array(){
		return eventon_get_organizer_social_meta_array();
	}

	// event taxonomy data / @4.2
		function get_taxonomy_data($tax, $load_meta_data = true, $term_id = false){
			
			// get terms
			$terms = apply_filters('evodata_taxonomy_terms', wp_get_post_terms($this->ID, $tax), $tax, $term_id, $this );

			
			if ( $terms && ! is_wp_error( $terms ) ){
				$R = array();

				if( $load_meta_data){
					$meta_key_array = $this->get_taxonomy_meta_array( $tax );
				}

				foreach($terms as $term){

					if( $term_id && $term->term_id != $term_id ) continue; 

					$R[ $tax ][ $term->term_id ] = $term;

					// if meta data key exists
					if( $meta_key_array && count($meta_key_array)>0){
						$term_meta = evo_get_term_meta( $tax, (int)($term->term_id) );

						foreach( $meta_key_array as $I=>$key){
							$K = is_integer($I)? $key: $I;				
							$R[ $tax ][ $term->term_id ]->$K = (empty($term_meta[$key]))? '': $term_meta[$key];
						}
					}

					// append secondary description to main description
					if( !empty( $R[ $tax ][ $term->term_id ]->description2 )){
						$R[ $tax ][ $term->term_id ]->description .= '<div class="evo_sd">'. $R[ $tax ][ $term->term_id ]->description2 .'</div>';
					}

					// pass link 
					$R[ $tax ][ $term->term_id ]->link = get_term_link( $term , $tax);
				}				
				return $R;			

			}else{
				return false;
			}
		}

		function get_taxonomy_meta_array($tax){
			$meta_data = array();

			$meta_data['event_organizer'] = $this->get_organizer_social_meta_array();
			
			$meta_data['event_organizer']['img_id'] = 'evo_org_img';
			$meta_data['event_organizer']['organizer_img_id'] = 'evo_org_img';
			$meta_data['event_organizer']['organizer_contact'] = 'evcal_org_contact';
			$meta_data['event_organizer']['contact_email'] = 'evcal_org_contact_e';
			$meta_data['event_organizer']['organizer_address'] = 'evcal_org_address';
			$meta_data['event_organizer']['organizer_link'] = 'evcal_org_exlink';
			$meta_data['event_organizer']['organizer_link_target'] = '_evocal_org_exlink_target';
			$meta_data['event_organizer']['description2'] = 'description2';

			$meta_data = apply_filters( 'evo_single_event_taxonomy_meta_array', $meta_data, $tax, $this);

			return isset($meta_data[ $tax ]) ? $meta_data[ $tax ]: false;
		}

		// get any taxonomy term data including evo saved term meta from options
		// @since 4.3
		function get_term_data($tax, $term_id){
			$term = get_term_by('term_id', $term_id, $tax);

			if ( !$term && is_wp_error( $term ) ) return false;

			// load term meta
			$termmeta = evo_get_term_meta($tax,$term_id);
			if($termmeta && is_array($termmeta)) $term->termmeta = $termmeta;

			return $term;
		}

}
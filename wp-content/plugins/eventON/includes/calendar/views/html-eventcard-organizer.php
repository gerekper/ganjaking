<?php 
/**
 * EventCard Organizer html content
 * @4.5.9
 */


						
$OT = "<div class='evo_metarow_organizer evorow evcal_evdata_row evcal_evrow_sm ".$end_row_class."'>
		<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon('evcal__fai_004', 'fa-microphone',$evOPT )."'></i></span>
		<div class='evcal_evdata_cell'>							
			<h3 class='evo_h3'>". evo_lang_get('evcal_evcard_org', 'Organizer')."</h3>";
			
		$OT.= "<div class='evo_evdata_cell_content'>";

// foreach organizer
foreach( $event_organizer as $EOID=>$EO){


	// image
	$img_src = (!empty($EO->organizer_img_id)? 
		wp_get_attachment_image_src($EO->organizer_img_id,'medium'): null);

	$newdinwow = (!empty($EO->organizer_link_target) && $EO->organizer_link_target=='yes')? 'target="_blank"':'';

	// Organizer link
		$org_link = '';
		if(!empty($EO->organizer_link) || !empty($EO->link) ){	

			if( !empty($EO->link) ) $org_link = $EO->link;
			if( !empty($EO->organizer_link) ) $org_link = $EO->organizer_link;

			$orgNAME = "<span class='evo_card_organizer_name_t marb5'><a ".( $newdinwow )." href='" . 
				evo_format_link( $org_link ) . "'>".$EO->name."</a></span>";
		}else{
			$orgNAME = "<span class='evo_card_organizer_name_t marb5'>". $EO->name."</span>";
		}	

		//$orgNAME = "<span class='evo_card_organizer_name_t marb5'>". $EO->name."</span>";


	$OT.= "<div class='evo_card_organizer'>";

	// image
		$OT.= (!empty($img_src)? 
				"<p class='evo_data_val evo_card_organizer_image'><img src='{$img_src[0]}'/></p>":null);

	

	$org_data = '';
	$org_data .= "<h4 class='evo_h4 marb5'>" . $orgNAME . "</h4>" ;
	
	/* // hide this in 4.5.5 
	$org_data .= "<div class='evo_data_val'>".
		
		( $description? "<div class='evo_card_organizer_description marb5 db'>".$description."</div>":'')

		.(!empty($EO->organizer_contact)? 
		"<span class='evo_card_organizer_contact marb5'>". stripslashes($EO->organizer_contact). "</span>":null)."
		".(!empty($EO->organizer_address)? 
		"<span class='evo_card_organizer_address marb5'>". stripslashes($EO->organizer_address). "</span>":null)."
		</div>";



	// organizer social share
		$org_social = '';
		foreach($EVENT->get_organizer_social_meta_array() as $key=>$val){

			if( empty($EO->$key )) continue;

			if( $key == 'twitter') $key = 'x-'. $key;
				
			$org_social .= "<a target='_blank' href='". urldecode( $EO->$key ) . "'><i class='fa fa-{$key}'></i></a>";
		}
		if( !empty($org_social)) 
			$org_data .= "<p class='evo_card_organizer_social'>" .$org_social ."</p>";

	*/

	// learn more button
		$btn_data = array(
			'lbvals'=> array(
				'lbc'=>'evo_organizer_lb',
				't'=>	$EO->name,
				'ajax'=>'yes',
				'ajax_type'=>'endpoint',
				'ajax_action'=>'eventon_get_tax_card_content',
				'end'=>'client',
				'd'=> array(					
					'eventid'=> $EVENT->ID,
					'ri'=> $EVENT->ri,
					'term_id'=> $EO->term_id,
					'tax'=>'event_organizer',
					'load_lbcontent'=>true
				)
			)
		);
		$org_data .= "<p class='evo_card_organizer_more'><a class='evolb_trigger evcal_btn mart10' ".$this->helper->array_to_html_data($btn_data).">". evo_lang('Learn More') . "</a></p>";

	$OT .= apply_filters('evo_organizer_event_card', $org_data, $ED, $EO->term_id);

	$OT .= "</div>";

}
		$OT.= "</div>";															
		$OT .= "</div>	</div>";

echo $OT;

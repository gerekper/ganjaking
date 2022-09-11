<?php
/**
* Calendar single event's html structure 
* @version 4.1.3
*/

class EVO_Cal_Event_Structure{
	private $EVENT;
	private $timezone = '';
	private $timezone_data = array();

	private $OO = array();
	private $OO2 = array();
	
	public function __construct($EVENT=''){

		$this->timezone_data = array(
			'__f'=>'YYYY-MM-DD h:mm:a',
			'__t'=> evo_lang('View in my time')
		);

		if(!empty($EVENT)) $this->EVENT = $EVENT;

		$this->timezone = get_option('gmt_offset', 0);
	}


// HTML EventTop
	function get_eventtop_item_html($field, $object, $eventtop_fields = ''){
		extract($object);
		$object = (object) $object;
		$SC = EVO()->calendar->shortcode_args;
		$EVENT = $this->EVENT;

		$eventtop_used_fields = $eventtop_fields['used'];

		
		$OT = '';
		switch($field){
			case 'ft_img':
				if( empty($object->img_url_med)) return $OT; 

				$url = apply_filters('eventon_eventtop_image_url', $object->img_url_med);

				$time_vals = ( $object->show_time) ? '<span class="evo_img_time"></span>':'';

				$OT.= "<span class='ev_ftImg' data-img='".(!empty($object->img_src)? $object->img_src: '')."' data-thumb='".$url."' style='background-image:url(\"".$url."\")' >{$time_vals}</span>";
			break;

			case 'day_block':

				if(!empty($eventtop_day_block) && !$eventtop_day_block) break;
				if(!is_array( $object->start_date_data )) break;

				// if event hide_et_dn
				if( isset($SC['hide_et_dn']) && $SC['hide_et_dn'] == 'yes') break;

				// day block data adjustments
				$day_block_data = isset($eventtop_fields['day_block']) && is_array($eventtop_fields['day_block']) ? $eventtop_fields['day_block']: array();

				$show_start_year = ( in_array('eventyear',$day_block_data) || 
					(isset($SC['eventtop_style']) && $SC['eventtop_style'] == 3 && $object->year_long) 
					?'yes':'no');
				$show_end_year = in_array('eventendyear',$day_block_data) ?'yes':'no';

				// bubbles event title passing
				$data_add = ( $SC['eventtop_style'] == '3') ? apply_filters('eventon_eventtop_maintitle',$this->EVENT->get_title() ) : '';
				
				$OT.="<span class='evoet_dayblock evcal_cblock ".( $object->year_long?'yrl ':null).( $object->month_long?'mnl ':null)."' data-bgcolor='".$color."' data-smon='".$object->start_date_data['F']."' data-syr='".$object->start_date_data['Y']."' data-bub='{$data_add}'>";
				
				// include dayname if allowed via settings
				$daynameS = $daynameE = '';
				if( is_array($day_block_data) && in_array('dayname', $day_block_data)){
					$daynameS = (!empty($event_date_html['start']['day'])? $event_date_html['start']['day']:'');
					$daynameE = (!empty($event_date_html['end']['day'])? $event_date_html['end']['day']:'');
				}

				$time_data = apply_filters('evo_eventtop_dates', array(
					'start'=>array(
						'year'=> 	($show_start_year=='yes'? $event_date_html['start']['year']:''),	
						'day'=>		$daynameS,
						'date'=> 	(!empty($event_date_html['start']['date'])?$event_date_html['start']['date']:''),
						'month'=>  	(!empty($event_date_html['start']['month'])?$event_date_html['start']['month']:''),
						'time'=>  	(!empty($event_date_html['start']['time'])?$event_date_html['start']['time']:''),
					),
					'end'=>array(
						'year'=> 	(($show_end_year=='yes' && !empty($event_date_html['end']['year']) )? $event_date_html['end']['year']:''),	
						'day'=>		$daynameE,
						'date'=> 	(!empty($event_date_html['end']['date'])?$event_date_html['end']['date']:''),
						'month'=> 	(!empty($event_date_html['end']['month'])? $event_date_html['end']['month']:''),
						'time'=> 	(!empty($event_date_html['end']['time'])? $event_date_html['end']['time']:''),
					),
				), $show_start_year, $object );


				$class_add = '';
				foreach($time_data as $type=>$data){					
					$end_content = '';
					foreach($data as $field=>$value){
						if(empty($value)) continue;
						$end_content .= "<em class='{$field}'>{$value}</em>";
					}

					if($type == 'end' && empty($data['year']) && empty($data['month']) && empty($data['date']) && !empty($data['time'])){
						$class_add = 'only_time';
					}
					if(empty($end_content)) continue;
					$OT .= "<span class='evo_{$type} {$class_add}'>";
					$OT .= $end_content;
					$OT .= "</span>";
				}

				// crystal clear style
				if( $SC['eventtop_style'] == '5'){
					
					$OT .= "<span class='evo_ett_break'></span><span class='evo_eventcolor_circle'><i style='background-color:{$object->color}'></i></span>";
				}
							
				$OT .= "</span>";

			break;

			case 'tags':
				// above title inserts
				if( isset($SC['hide_et_tags']) && $SC['hide_et_tags'] == 'yes') return $OT;

				$OT.= "<span class='evoet_tags evo_above_title'>";
					
					// live now virtual event
					if($EVENT && $EVENT->is_virtual() && !$EVENT->is_cancelled() && $EVENT->is_event_live_now() && !EVO()->cal->check_yn('evo_hide_live') ){
						$OT.= "<span class='evo_live_now' title='".( evo_lang('Live Now')  )."'>". EVO()->elements->get_icon('live') ."</span>";
					}


					// get set to hide event top tags
					$eventtop_hidden_tags = isset($evOPT['evo_etop_tags']) ? $evOPT['evo_etop_tags'] : array();

					$OT .= apply_filters("eventon_eventtop_abovetitle", '', $object, $EVENT, $eventtop_hidden_tags);

					$eventtop_tags = apply_filters('eventon_eventtop_abovetitle_tags', array());

					// status
					if( $_status && $_status != 'scheduled'){
						$eventtop_tags['status'] = array(
							$EVENT->get_event_status_lang(),
							$_status
						);
					}

					// featured
					if(!empty($featured) && $featured){
						$eventtop_tags['featured'] = array(evo_lang('Featured')	);
					}

					// virtual
					if( $EVENT && $EVENT->get_attendance_mode() != 'offline' ){
						if( $EVENT->is_mixed_attendance()){
							$eventtop_tags['virtual_physical'] = 
								array(evo_lang('Virtual/ Physical Event'), 'vir'	);
						}else{
							$eventtop_tags['virtual'] = array(evo_lang('Virtual Event'), 'vir'	);
						}							
					}

					
					// print the event top tags
					foreach($eventtop_tags as $ff=>$vv){
						if(in_array($ff, $eventtop_hidden_tags)) continue;

						$v1 = isset($vv[1]) ? ' '.$vv[1]:'';
						$OT.= "<span class='evo_event_headers {$ff}{$v1}'>". $vv[0] . "</span>";
					}



				$OT.="</span>";
			break;

			case 'title':
				// event edit button
					$editBTN = '';
					// settings enabled - 4.0
					if( EVO()->cal->check_yn('evo_showeditevent','evcal_1')){
						$__go = false;
						// if user is admin of the site
						if( current_user_can('manage_options') ) $__go = true;

						if( $__go){
							$editBTN = apply_filters('eventon_event_title_editbtn',
								"<i href='".get_edit_post_link($this->EVENT->ID)."' class='editEventBtnET fa fa-pencil'></i>", $this->EVENT);
						}
					}
				$OT.= "<span class='evoet_title evcal_desc2 evcal_event_title' itemprop='name'>". apply_filters('eventon_eventtop_maintitle',$this->EVENT->get_title() ) . $editBTN."</span>";

				// location attributes
					$event_location_variables = '';
					if(!empty($location_name) && (!empty($location_address) || !empty($location_latlng))){
						$LL = !empty($location_latlng)? $location_latlng:false;

						if(!empty($location_address)) $event_location_variables .= ' data-location_address="'.$location_address.'" ';
						$event_location_variables .= ($LL)? 'data-location_type="lonlat"': 'data-location_type="address"';
						$event_location_variables .= ' data-location_name="'.$location_name.'"';
						if(isset($location_url))	$event_location_variables .= ' data-location_url="'.$location_url.'"';
						$event_location_variables .= ' data-location_status="true"';

						if( $LL){
							$event_location_variables .= ' data-latlng="'.$LL.'"';
						}

						$OT.= "<span class='event_location_attrs' {$event_location_variables}></span>";
					}

			break;
			case 'subtitle':				
				// below title inserts
				$OT.= "<span class='evoet_subtitle evo_below_title'>";
					if($ST = $this->EVENT->get_subtitle()){
						$OT.= "<span class='evcal_event_subtitle' >" . apply_filters('eventon_eventtop_subtitle' , $ST) ."</span>";
					}

					// event status reason 
					if( $reason = $this->EVENT->get_status_reason()){
						$OT.= '<span class="status_reason">'. $reason .'</span>';
					}

				$OT.="</span>";
			break;
			case 'time':

				if( isset($SC['hide_et_tl']) && $SC['hide_et_tl'] == 'yes') return $OT;

				$OT.= "<span class='evoet_time_expand level_3'>";
				
				// time
				$timezone_text = (!empty($object->timezone)? ' <em class="evo_etop_timezone">'.$object->timezone. '</em>':null);

				$tzo = $tzo_box = '';
				$event_timezone_key = $EVENT->get_timezone_key();

				// show local time
				if( $event_timezone_key && EVO()->cal->check_yn('evo_show_localtime','evcal_1') ){

					extract( $this->timezone_data);
					$help = new evo_helper();
					
					
					if( !EVO()->cal->check_yn('evo_gmt_hide','evcal_1')){

						$GMT_text = $help->get_timezone_gmt( $event_timezone_key, $EVENT->start_unix);

						$timezone_text .= "<span class='evo_tz'>(". $GMT_text .")</span>";
					}

					// event utc offset
					$tzo = $help->get_timezone_offset( $event_timezone_key, $EVENT->start_unix);

					$tzo_box = "<em class='evcal_tz_time evo_mytime tzo_trig' title='". evo_lang('My Time') ."' data-tzo='{$tzo}' data-tform='{$__f}' data-times='{$event_start_unix}-{$event_end_unix}' ><i class='fa fa-globe-americas'></i> <b>{$__t}</b></em>";							
				}

				$OT.= "<em class='evcal_time evo_tz_time'>". apply_filters('evoeventtop_belowtitle_datetime', $object->event_date_html['html_fromto'], $object->event_date_html, $object) . $timezone_text ."</em> ";

				// local time
				if( $event_timezone_key) $OT.= $tzo_box;

				// manual timezone text
				if( !$event_timezone_key) $OT.= "<em class='evcal_local_time' data-s='{$event_start_unix}' data-e='{$event_end_unix}' data-tz='". $EVENT->get_prop('_evo_tz') ."'></em>";
			
				$OT.= "</span>";

			break;

			case 'location':

				// hide via shortcode
				if( isset($SC['hide_et_tl']) && $SC['hide_et_tl'] == 'yes') return $OT;

				$eventtop_location_data = $eventtop_fields['location'];
				

				// location name				
				$LOCname = ( ('locationame' == $eventtop_location_data || $eventtop_location_data == 'both') && !empty($location_name) )? $location_name: false;

				// location address
				$LOCadd = ( ( 'location' == $eventtop_location_data || $eventtop_location_data =='both') && !empty($location_address))? stripslashes($location_address): false;

				if($LOCname || $LOCadd){
					$OT.= "<span class='evoet_location level_3'>";
					$OT.= '<em class="evcal_location" '.( !empty($location_latlng)? ' data-latlng="'.$location_latlng.'"':null ).' data-add_str="'.$LOCadd.'">'.($LOCname? '<em class="event_location_name">'.$LOCname.'</em>':'').
						( ($LOCname && $LOCadd)?', ':'').
						$LOCadd.'</em>';
					$OT.= "</span>";
				}				

			break;
			case 'organizer':
				if( in_array('organizer',$eventtop_used_fields) && !empty($organizer) && isset($organizer->name)){
					
					$OT.="<span class='evcal_oganizer level_4'>
						<em><i>".( eventon_get_custom_language( '','evcal_evcard_org', 'Event Organized By')  ).':</i></em>
						<em>'.$organizer->name."</em>
						</span>";
				}

			break;
			case 'eventtags':
				// event tags
				
					$event_tags = wp_get_post_tags($this->EVENT->ID);
					if(!$event_tags) return $OT;

					$OT.="<span class='evo_event_tags level_4'>
						<em><i>".eventon_get_custom_language( '','evo_lang_eventtags', 'Event Tags')."</i></em>";

					$count = count($event_tags);
					$i = 1;
					foreach($event_tags as $tag){
						$OT.="<em data-tagid='{$tag->term_id}'>{$tag->name}".( ($count==$i)?'':',')."</em>";
						$i++;
					}
					$OT.="</span>";
			break;
			
			case 'progress_bar':
				
				// event progress bar
				if( !EVO()->cal->check_yn('evo_eventtop_progress_hide','evcal_1')  && $EVENT->is_event_live_now() && !$EVENT->is_cancelled()
					&& !$EVENT->echeck_yn('hide_progress')
					&& $EVENT->get_event_status() != 'postponed'
				){
					

					$livenow_bar_sc = isset($SC['livenow_bar']) ? $SC['livenow_bar'] : 'yes';
					
					// check if shortcode livenow_bar is set to hide live bar
					if($livenow_bar_sc != 'yes') return $OT;

					$OT.= "<span class='evoet_progress_bar evo_event_progress ' >";

					//$OT.= "<span class='evo_ep_pre'>". evo_lang('Live Now') ."</span>";

					$now =  EVO()->calendar->utc_time;
					$duration = $EVENT->duration;
					$end_utc = $EVENT->get_end_time( true);
					$gap = $end_utc - $now; // how far event has progressed

					$perc = $duration == 0? 0: ($duration - $gap) / $duration;
					$perc = (int)( $perc*100);
					if( $perc > 100) $perc = 100;

					// action on expire							
					$exp_act = $nonce = '';
					if( isset($SC['cal_now']) && $SC['cal_now'] == 'yes'){
						$exp_act = 'runajax_refresh_now_cal';
						$nonce = wp_create_nonce('evo_calendar_now');
					}

					
					$OT.= "<span class='evo_epbar_o'><span class='evo_ep_bar'><b style='width:{$perc}%'></b></span></span>";
					$OT.= "<span class='evo_ep_time evo_countdowner' data-gap='{$gap}' data-dur='{$duration}' data-exp_act='". $exp_act ."' data-n='{$nonce}' data-ds='".evo_lang('Days')."' data-d='".evo_lang('Day')."' data-t='". evo_lang('Time Left')."'></span>";

					$OT.= "</span>";

				}
			
			case has_filter("eventon_eventtop_{$field}"):
				//echo $field;
				$helpers = array(
					'evOPT'=>	EVO()->calendar->evopt1,
					'evoOPT2'=> EVO()->calendar->evopt2,
				);

				$OT.= apply_filters("eventon_eventtop_{$field}", '', $object, $EVENT, $helpers);	
			break;
		}


		// custom meta fields
		if( strpos($field, 'cmd') !== false){
			if(!empty($object->cmf_data) && is_array($object->cmf_data) && count($object->cmf_data)>0){

				if( !isset($object->cmf_data[ $field ])) return $OT;
				$OT = $this->get_eventtop_cmf_html( $object->cmf_data[ $field ]);

			}
		}

		// event type taxonomy
		if(strpos($field, 'eventtype') !== false){
			$OT .= $this->get_eventtop_types($field, $object);
		}

		return $OT;
	}

	// HTML for various event top blocks
		function get_eventtop_types($tax_field, $object){
			$OT = '';

			if( empty($object->$tax_field)) return $OT;

			$tax_data = $object->$tax_field;
			if( !isset( $tax_data['terms'] )) return $OT;

			$OT .="<span class='evoet_eventtypes level_4 evcal_event_types ett{$tax_data['tax_index']}'><em><i>{$tax_data['tax_name']}:</i></em>";

			foreach($tax_data['terms'] as $term_id=>$TD){
				$OT .="<em data-filter='{$TD['s']}'>".$TD['i']. $TD['tn'] . $TD['add'] ."</em>";
			}
			$OT .="</span>";

			
			return $OT;
		}

		function get_eventtop_cmf_html($v){
			$OT = $icon_string = '';
			if( empty($v['value'])) return $OT;

			// user loggedin visibility restriction
			if( !empty($v['login_needed_message']) ) return $OT;

			// user role restriction validation
			if( ($v['visibility_type'] =='admin' && !current_user_can( 'manage_options' ) ) ||
				($v['visibility_type'] =='loggedin' && !is_user_logged_in() && empty($v['login_needed_message']))
			) return $OT;
			
			// custom icon
			if( !empty($v['imgurl']) && EVO()->cal->check_yn('evo_eventtop_customfield_icons','evcal_1') ){
				$icon_string ='<i class="fa '. $v['imgurl'] .'"></i>'; 
			}

			if( $v['type'] == 'button'){									
				$OT.= "<span class='evoet_cmf'><em class='evcal_cmd evocmd_button' data-href='". ($v['valueL'] ). "' data-target='". ($v['_target']). "'>" . $icon_string .$v['value']."</em></span>";
			
			}elseif( $v['type'] == 'textarea'){

			}else{	
				$OT.= "<span class='evoet_cmf'><em class='evcal_cmd'>". $icon_string . "<i>".  $v['field_name'].':</i></em><em>'. $v['value'] ."</em>
					</span>";									
			}

			return $OT;
		}

	function get_event_top($EventData, $eventtop_fields){
			
		$EVENT = $this->EVENT;
		$SC = EVO()->calendar->shortcode_args;
		$OT = '';		
		
		$evOPT = EVO()->calendar->evopt1;
		$evOPT2 = EVO()->calendar->evopt2;

		extract($EventData);

		EVO()->cal->set_cur('evcal_1');

		// open for pluggability 
			$eventtop_fields = apply_filters('evoet_data_structure', $eventtop_fields, $EventData, $EVENT);

			$layout = $eventtop_fields['layout'];
			//print_r($layout);

		// for each column in eventtop
			for($x = 0; $x<5; $x++){
				if(!isset( $layout['c'.$x] )) continue;

				$additional_class = '';
				if( $x == '3'){
					$show_widget_eventtops = (!empty($evOPT['evo_widget_eventtop']) && $evOPT['evo_widget_eventtop']=='yes')? '':' hide_eventtopdata ';
					$additional_class .= 'evcal_desc'. $show_widget_eventtops;
				} 

				$inner_content = '';
				
				if( is_array( $layout['c'.$x] ) && isset( $layout['c'.$x]) ){

					foreach( $layout['c'.$x] as $field){
						// eventtop bubbles
						//if( $SC['eventtop_style'] == '3' && $field['f'] != 'day_block') continue;

						$inner_content .= $this->get_eventtop_item_html( $field['f'], $EventData, $eventtop_fields);
					}
				}

				if( empty($inner_content)) continue;

				$OT.= "<span class='evoet_c{$x} evoet_cx {$additional_class}'>";
				$OT .= $inner_content;

				$OT .= "</span>";
			}		

		return $OT;
	}

// EvnetCard HTML
	function get_event_card($array, $EventData, $evOPT, $evoOPT2, $ep_fields = ''){
		// INIT
			$EVENT = $this->EVENT;
			$ED = $EventData;
			$evoOPT2 = (!empty($evoOPT2))? $evoOPT2: '';
			$this->OO = $evOPT;
			$this->OO2 = $evoOPT2;
			
			$OT ='';
			$count = 1;
			$items = count($array);	

			extract($EventData);

			$ep_fields = !empty($ep_fields)? explode(',', $ep_fields): false;
			
			// close button
			$close = "<div class='evcal_evdata_row evcal_close' title='".eventon_get_custom_language($evoOPT2, 'evcal_lang_close','Close')."'></div>";

			// additional fields array 
			$array = apply_filters('evo_eventcard_adds' , $array);

			//print_r($array);
			//print_r($EventData);

		ob_start();


		// get event card designer fields
		$eventcard_fields = EVO()->calendar->helper->get_eventcard_fields_array();

		//print_r($eventcard_fields);
		
		$rows = count($eventcard_fields);
		$i = 1;
		foreach( $eventcard_fields as $R=>$boxes){
			
			$CC = '';
			$box_count = 0;
			
			$opened = false;

			foreach( $boxes as $B=>$box){

				if( !isset($box['n'])) continue;
				$NN = $box['n'];
				if( isset($box['h']) && $box['h'] =='y' ) continue;

				// get box data
					if( !array_key_exists($NN, $array)) continue;
					
					$BD = $array[ $NN ];
					// convert to an object
					$BDO = new stdClass();
					foreach ($BD as $key => $value){
						$BDO->$key = $value;
					}

				// if only specific fields set
					if( $ep_fields && !in_array($NN, $ep_fields) ) continue;

				// box content
				$BCC = $this->get_eventcard_box_content( $NN, $BDO, $EVENT , $EventData);

				if( empty($BCC)) continue;

				$color = isset($box['c']) ? $box['c']:'';

				if( $B == 'L1' || $B == 'R1'){
					$CC .= "<div class='evocard_box_h'>";
					$opened = true;
				}

				$CC .= "<div class='evocard_box {$NN}' data-c='". $color ."' 
					style='". (!empty($color) ? "background-color:#{$color}":'') ."'>". $BCC . "</div>";

				// stacked boxes close container
				if( $opened){
					if( (count($boxes) == 3 && ($B == 'L2' || $B == 'R2') ) ||
						( count($boxes) == 4 && ($B == 'L3' || $B == 'R3') ) ){
						$CC .= "</div>"; $opened = false;
					}
				}
				$box_count++;
			}

			if( $opened ) $CC .= "</div>";
			if( empty($CC)) continue;

			$row_class = array('evocard_row');
			if($box_count>1) $row_class[] ='bx'.$box_count;
			if($box_count>1) $row_class[] ='bx';
			if( array_key_exists('L1', $boxes)) $row_class[] = 'L';
			if($i == $rows)  $row_class[] = 'lastrow';

			echo "<div class='". implode(' ', $row_class) ."'>";
			echo $CC;
			echo "</div>";
			$i++;
		}

		echo "<div class='evo_card_row_end evcal_close' title='".eventon_get_custom_language($evoOPT2, 'evcal_lang_close','Close')."'></div>";

		return ob_get_clean();		
	}	

// return box HTML content using box field name
	function get_eventcard_box_content($box_name, $box_data, $EVENT, $EventData){

		$OT = '';
		$evOPT = $this->OO;
		$evoOPT2 = $this->OO2;
		$object = $box_data;
		$end_row_class = $end = '';
		$ED = $EventData;

		//print_r($EventData);

		extract($EventData);


		// each eventcard type
			switch($box_name){

				// addition
					case has_filter("eventon_eventCard_{$box_name}"):
					
						$helpers = array(
							'evOPT'=> $evOPT,
							'evoOPT2'=>$evoOPT2,
							'end_row_class'=> '','end'=>'',
						);

						$OT.= apply_filters("eventon_eventCard_{$box_name}", $object, $helpers, $EVENT);
						
					break;
					
				// Event Details
					case 'eventdetails':	
						
						$more_code=''; $evo_more_active_class = '';

						// check if character length of description is longer than X size
						if( !empty($evOPT['evo_morelass']) && $evOPT['evo_morelass']!='yes' && (strlen($object->fulltext) )>600 ){
							$more_code = 
								"<p class='eventon_shad_p' style='padding:5px 0 0; margin:0'>
									<span class='evcal_btn evo_btn_secondary evobtn_details_show_more' content='less'>
										<span class='ev_more_text' data-txt='".evo_lang_get('evcal_lang_less','less')."'>".evo_lang_get('evcal_lang_more','more')."</span><span class='ev_more_arrow ard'></span>
									</span>
								</p>";
							$evo_more_active_class = 'shorter_desc';
						}

						$iconHTML = "<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon('evcal__fai_001', 'fa-align-justify',$evOPT )."'></i></span>";

						$_full_event_details = stripslashes( $object->fulltext );

						
						$OT.="<div class='evo_metarow_details evorow evcal_evdata_row evcal_event_details".$end_row_class."'>
								".$object->excerpt.$iconHTML."
								
								<div class='evcal_evdata_cell ".$evo_more_active_class."'>
									<div class='eventon_full_description'>
										<h3 class='padb5 evo_h3'>".$iconHTML . evo_lang_get('evcal_evcard_details','Event Details')."</h3>
										<div class='eventon_desc_in' itemprop='description'>
										". 

										apply_filters('evo_eventcard_details',EVO()->frontend->filter_evo_content( $_full_event_details )) 

										."</div>";
										
										// pluggable inside event details
										do_action('eventon_eventcard_event_details');

										$OT .= $more_code;

										$OT.="<div class='clear'></div>
									</div>
								</div>
							</div>";
									
					break;

				// TIME
					case 'time':
						$iconTime = "<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon('evcal__fai_002', 'fa-clock-o',$evOPT )."'></i></span>";
						// time for event card
						$timezone = (!empty($object->timezone)? ' <em class="evo_eventcard_tiemzone">'. $object->timezone.'</em>':null);
						
						$evc_time_text = "<span class='evo_eventcard_time_t'>". apply_filters('evo_eventcard_time', $object->timetext. $timezone, $object) . "</span>";

						// if timezone selected
						if( !empty($object->_evo_tz) && EVO()->cal->check_yn('evo_show_localtime','evcal_1')){

							extract( $this->timezone_data);
							$help = new evo_helper();

							

							if( !EVO()->cal->check_yn('evo_gmt_hide','evcal_1')){

								$GMT_text = $help->get_timezone_gmt( $object->_evo_tz , $EVENT->start_unix);

								$evc_time_text .= "<span class='evo_tz'>(". $GMT_text .")</span>";
							}

							// event utc offset
							$tzo = $help->get_timezone_offset( $object->_evo_tz,  $EVENT->start_unix);

							$evc_time_text .= "<span class='evo_mytime tzo_trig' title='". evo_lang('My Time') ."' data-tzo='{$tzo}' data-tform='{$__f}' data-times='{$object->event_times}'><i class='fa fa-globe-americas'></i> <b>{$__t}</b></span>";
						}
						

						$OT.="<div class='evo_metarow_time evorow evcal_evdata_row evcal_evrow_sm ".$end_row_class."'>
								{$iconTime}
								<div class='evcal_evdata_cell'>							
									<h3 class='evo_h3'>".$iconTime . evo_lang_get('evcal_lang_time','Time')."</h3><p>".$evc_time_text."</p>
								</div>
							</div>";
					break;

				// location
					case 'location':
						$iconLoc = "<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon('evcal__fai_003', 'fa-map-marker',$evOPT )."'></i></span>";
						
						if(!empty($EventData['location_name']) || !empty($EventData['location_address'])){
							
							$locationLink = (!empty($location_link))? '<a target="'. ($location_link_target=='yes'? '_blank':'') .'" href="'. evo_format_link($location_link).'">':false;
							
							$OT.= 
							"<div class='evcal_evdata_row evo_metarow_time_location evorow '>
								
									{$iconLoc}
									<div class='evcal_evdata_cell' data-loc_tax_id='{$EventData['location_term_id']}'>";

									if( $location_hide){
										$OT.= "<h3 class='evo_h3'>".$iconLoc. evo_lang_get('evcal_lang_location','Location'). "</h3>";
										$OT .= "<p class='evo_location_name'>". EVO()->calendar->helper->get_field_login_message() . "</p>";
									}else{
										
										$OT.= "<h3 class='evo_h3'>".$iconLoc.($locationLink? $locationLink:''). evo_lang_get('evcal_lang_location','Location').($locationLink?'</a>':'')."</h3>";

										if( !empty($location_name) && !$EVENT->check_yn('evcal_hide_locname') )
											$OT.= "<p class='evo_location_name'>". $locationLink. $location_name . ($locationLink? '</a>':'') ."</p>";

										// for virtual location
										if( $location_type == 'virtual'){
											if( $locationLink) 
												$OT.= "<p class='evo_virtual_location_url'>" . evo_lang('URL:'). $locationLink . ' '. $location_link."</a></p>";
										}else{

											if(!empty($location_address)){
												$OT .= "<p class='evo_location_address'>". $locationLink . stripslashes($location_address) . ($locationLink? '</a>':'') ."</p>";
											}
											
										}											
									}
									$OT.= "</div>
								
							</div>";
						}
					break;

				

				// REPEAT SERIES
					case 'repeats':
						$OT.="<div class='evo_metarow_repeats evorow evcal_evdata_row evcal_evrow_sm ".$end_row_class."'>
								<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon('evcal__fai_repeats', 'fa-repeat',$evOPT )."'></i></span>
								<div class='evcal_evdata_cell'>							
									<h3 class='evo_h3'>".eventon_get_custom_language($evoOPT2, 'evcal_lang_repeats','Future Event Times in this Repeating Event Series')."</h3>
									<p class='evo_repeat_series_dates ".($object->clickable?'clickable':'')."'' data-click='".$object->clickable."' data-event_url='".$object->event_permalink."'>";

							$datetime = new evo_datetime();

							// allow for custom date time format passing to repeat event times
							$repeat_start_time_format = apply_filters('evo_eventcard_repeatseries_start_dtformat','');
							$repeat_end_time_format = apply_filters('evo_eventcard_repeatseries_end_dtformat','');


							
							foreach($object->future_intervals as $key=>$interval){
								$OT .= "<span data-repeat='{$key}' data-l='". $EVENT->get_permalink($key,$EVENT->l) ."' class='evo_repeat_series_date'>"; 
								

								if($EVENT->is_year_long()){
									$OT .= date('Y', $interval[0]);
								}elseif( $EVENT->is_month_long()){

									$OT .= $EVENT->get_readable_formatted_date( $interval[0], 'F, Y');

								}elseif( $EVENT->is_all_day()){
									$OT .= $EVENT->get_readable_formatted_date( $interval[0], EVO()->calendar->date_format);

									// show end time
									if( $object->showendtime && !empty($interval[1])){

										$OT .= ' - '.$EVENT->get_readable_formatted_date( $interval[1], EVO()->calendar->date_format);
									}

								}else{

									$OT .= $EVENT->get_readable_formatted_date( $interval[0], $repeat_start_time_format);

									if( $object->showendtime && !empty($interval[1])){

										$OT .= ' - '.$EVENT->get_readable_formatted_date( $interval[1], $repeat_end_time_format);
									}

								}
								
								
								$OT.= "</span>";
							}

						$OT.="</p></div></div>";
					break;

				// Location Image
					case 'locImg':

						if(empty($location_img_id)) break;
						$img_src = wp_get_attachment_image_src($location_img_id,'full');
						if(empty($img_src)) break;

						$fullheight = (int)EVO()->calendar->get_opt1_prop('evo_locimgheight',400);

						if(!empty($img_src)){
							
							// text over location image
							$inside = $inner = '';
							if(!empty($location_name) && $EVENT->check_yn('evcal_name_over_img') ){

								if(!empty($location_address))	$inner .= '<span style="padding-bottom:10px">'. stripslashes($location_address) .'</span>';
								if(!empty($location_description)) $inner .= '<span class="location_description">'.$location_description.'</span>';
								$inside = "<p class='evoLOCtxt'><span class='evo_loc_text_title'>{$location_name}</span>{$inner}</p>";
							}
							$OT.="<div class='evcal_evdata_row evo_metarow_locImg evorow ".( !empty($inside)?'tvi':null)."' style='height:{$fullheight}px; background-image:url(".$img_src[0].")' id='".$location_img_id."_locimg' >{$inside}</div>";
						}
					break;

				// GOOGLE map
					case 'gmap':	

						if( $EventData['location_type'] != 'virtual' || !isset($EventData['location_type'])){
							$OT.="<div class='evcal_evdata_row evo_metarow_gmap evorow evcal_gmaps ".$object->id."_gmap' id='".$object->id."_gmap' style='max-width:none'></div>";
						}
					break;
				
				// Featured image
					case 'ftimage':
						
						$__hoverclass = (!empty($object->hovereffect) && $object->hovereffect!='yes')? ' evo_imghover':null;
						$__noclickclass = (!empty($object->clickeffect) && $object->clickeffect=='yes')? ' evo_noclick':null;
						$__zoom_cursor = (!empty($evOPT['evo_ftim_mag']) && $evOPT['evo_ftim_mag']=='yes')? ' evo_imgCursor':null;

						ob_start();

						
						// if set to direct image
						if(!empty($evOPT['evo_ftimg_height_sty']) && $evOPT['evo_ftimg_height_sty']=='direct'){
							// ALT Text for the image
								$alt = !empty($object->img_id)? get_post_meta($object->img_id,'_wp_attachment_image_alt', true):false;
								$alt = !empty($alt)? 'alt="'.$alt.'"': '';
							echo "<div class='evo_metarow_directimg evcal_evdata_row'><img class='evo_event_main_img' src='{$object->img}' {$alt}/></div>";
						}else{

							// make sure image array object passed
							if( $object->main_image && is_array($object->main_image)){

								$main_image = $object->main_image;

								$height = !empty($object->img[2])? $object->img[2]:'';
								$width = !empty($object->img[1])? $object->img[1]:'';

								echo "<div class='evo_metarow_fimg evorow evcal_evdata_img evcal_evdata_row ".$end_row_class.$__hoverclass.$__zoom_cursor.$__noclickclass."' data-imgheight='". $main_image['full_h'] ."' data-imgwidth='". $main_image['full_w'] ."'  style='background-image: url(\"".$object->img."\")' data-imgstyle='".$object->ftimg_sty."' data-minheight='".$object->min_height."' data-status=''></div>";
							}
						}

						// additional images
							$adds = $EVENT->get_prop('_evo_images');

							if( apply_filters('evo_eventcard_additional_images', $adds, $object, $EVENT) ){

								//print_r($object);
								echo "<div class='evcal_evdata_row evo_event_images'>";

								echo "<span class='evo_event_more_img select'><img title='' src='{$object->img}' data-f='{$object->img}'/></span>";
								

								$imgs = explode(',', $adds);
								$imgs = array_filter($imgs);

								$x = 1;
								foreach($imgs as $img){

									$caption = get_post_field('post_excerpt',$img);
									$thumb = wp_get_attachment_image_src($img);
									$full = wp_get_attachment_image_src($img,'full');

									echo "<span class='evo_event_more_img '><img title='{$caption}' src='{$thumb[0]}' data-f='{$full[0]}' data-h='{$full[2]}' data-w='{$full[1]}'/></span>";
									$x++;
								}
								echo "</div>";
							}

							do_action('evo_eventcard_ftimg_end', $object, $EVENT);

						$OT .= ob_get_clean();
						
					break;
				
				// event organizer
					case 'organizer':					
						$evcal_evcard_org = eventon_get_custom_language($evoOPT2, 'evcal_evcard_org','Organizer');

						if(empty($ED['organizer_term'])) break;
						
						$img_src = (!empty($organizer_img_id)? 
							wp_get_attachment_image_src($organizer_img_id,'medium'): null);

						$newdinwow = (!empty($organizer_link_target) && $organizer_link_target=='yes')? 'target="_blank"':'';

						// organizer name text openinnewwindow
							if(!empty($organizer_link)){							
								$orgNAME = "<span class='evo_card_organizer_name_t'><a ".( $newdinwow )." href='" . 
									evo_format_link($organizer_link) . "'>".$organizer_name."</a></span>";
							}else{
								$orgNAME = "<span class='evo_card_organizer_name_t'>".$organizer_name."</span>";
							}	

						
						$OT.= "<div class='evo_metarow_organizer evorow evcal_evdata_row evcal_evrow_sm ".$end_row_class."'>
								<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon('evcal__fai_004', 'fa-headphones',$evOPT )."'></i></span>
								<div class='evcal_evdata_cell'>							
									<h3 class='evo_h3'>".$evcal_evcard_org."</h3>
									".(!empty($img_src)? 
										"<p class='evo_data_val evo_card_organizer_image'><img src='{$img_src[0]}'/></p>":null)."
									<div class='evo_card_organizer'>";

									$description = !empty($organizer_description) ? stripslashes($organizer_description): false;

									$org_data = "<p class='evo_data_val evo_card_organizer_name'>
										".$orgNAME.
										( $description? "<span class='evo_card_organizer_description'>".$description."</span>":'').
										(!empty($organizer_contact)? 
										"<span class='evo_card_organizer_contact'>". stripslashes($organizer_contact). "</span>":null)."
										".(!empty($organizer_address)? 
										"<span class='evo_card_organizer_address'>". stripslashes($organizer_address). "</span>":null)."
										</p>";

									// organizer social share
										$org_social = '';
										foreach($EVENT->get_organizer_social_meta_array() as $key=>$val){
											if( !empty($EventData[$key]))
												$org_social .= "<a href='". urldecode( $EventData[$key] ) . "'><i class='fa fa-{$key}'></i></a>";
										}
										if( !empty($org_social)) 
											$org_data .= "<p class='evo_card_organizer_social'>" .$org_social ."</p>";

									$OT .= apply_filters('evo_organizer_event_card', $org_data, $ED, $organizer_term_id);

									$OT .= "</div><div class='clear'></div>							
								</div>
							</div>";
						
					break;
				
				// get directions
					case 'getdirection':
						
						$_lang_1 = evo_lang_get('evcalL_getdir_placeholder','Type your address to get directions');
						$_lang_2 = evo_lang_get('evcalL_getdir_title','Click here to get directions');

						$_from_address = false;
						if(!empty($location_address)) $_from_address = $location_address;
						if(!empty($location_getdir_latlng) && $location_getdir_latlng =='yes' && !empty($location_latlng)){
							$_from_address = $location_latlng;
						}

						if(!$_from_address) break;
						
						$OT.="<div class='evo_metarow_getDr evorow evcal_evdata_row evcal_evrow_sm getdirections'>
							<form action='https://maps.google.com/maps' method='get' target='_blank'>
							<input type='hidden' name='daddr' value=\"{$_from_address}\"/> 
							<p><input class='evoInput' type='text' name='saddr' placeholder='{$_lang_1}' value=''/>
							<button type='submit' class='evcal_evdata_icons evcalicon_9' title='{$_lang_2}'><i class='fa ".get_eventON_icon('evcal__fai_008a', 'fa-road',$evOPT )."'></i></button>
							</p></form>
						</div>";
						
					break;

				// learn more link
					case "learnmore":
						// learn more link with pluggability
						$learnmore_link = !empty($EVENT->get_prop('evcal_lmlink'))? apply_filters('evo_learnmore_link', $EVENT->get_prop('evcal_lmlink'), $object): false;
						$learnmore_target = ($EVENT->get_prop('evcal_lmlink_target')  && $EVENT->get_prop('evcal_lmlink_target')=='yes')? 'target="_blank"':null;

						if(!$learnmore_link) break;
						
						$OT.= "<div class='evo_metarow_learnM evo_metarow_learnmore evorow'>
							<a class='evcal_evdata_row evo_clik_row ' href='".$learnmore_link."' ".$learnmore_target.">
								<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon('evcal__fai_006', 'fa-link',$evOPT )."'></i></span>
								<h3 class='evo_h3'>".eventon_get_custom_language($evoOPT2, 'evcal_evcard_learnmore2','Learn More')."</h3>
							</a>
							</div>";
					break;

					case "addtocal":
						$__ics_url =admin_url('admin-ajax.php').'?action=eventon_ics_download&amp;event_id='.$EVENT->ID.'&amp;ri='.$EVENT->ri;

							$O = (object)array(
								'location_name'=> !empty($location_name)?$location_name:'',
								'location_address'=> !empty($location_address)?$location_address:'',
								'etitle'=> $event_title,
								'excerpt'=> $event_excerpt_txt
							);

							
							$__googlecal_link = $EVENT->get_addto_googlecal_link(
								$O->location_name,
								$O->location_address
							);


						// which options to show for add to calendar
							$addCaloptions = !empty($evOPT['evo_addtocal'])? $evOPT['evo_addtocal']: 'all';
							$addCalContent = '';

						// add to cal section
							switch($addCaloptions){
								case 'ics':
									$addCalContent = "<a href='{$__ics_url}' class='evo_ics_nCal' title='".eventon_get_custom_language($evoOPT2, 'evcal_evcard_addics','Add to your calendar')."'>".eventon_get_custom_language($evoOPT2, 'evcal_evcard_calncal','Calendar')."</a>";
								break;
								case 'gcal':
									$addCalContent = "<a href='". $__googlecal_link. "' target='_blank' class='evo_ics_gCal' title='".eventon_get_custom_language($evoOPT2, 'evcal_evcard_addgcal','Add to google calendar')."'>".eventon_get_custom_language($evoOPT2, 'evcal_evcard_calgcal','GoogleCal')."</a>";
								break;
								case 'all':
									$addCalContent = "<a href='{$__ics_url}' class='evo_ics_nCal' title='".eventon_get_custom_language($evoOPT2, 'evcal_evcard_addics','Add to your calendar')."'>".eventon_get_custom_language($evoOPT2, 'evcal_evcard_calncal','Calendar')."</a>".
										"<a href='{$__googlecal_link}' target='_blank' class='evo_ics_gCal' title='".eventon_get_custom_language($evoOPT2, 'evcal_evcard_addgcal','Add to google calendar')."'>".eventon_get_custom_language($evoOPT2, 'evcal_evcard_calgcal','GoogleCal')."</a>";
								break;
							}

						if( $addCaloptions != 'none'){
							$OT .= "<div class='evo_metarow_ICS evorow evcal_evdata_row'>
									<span class='evcal_evdata_icons'><i class='fa fa-calendar'></i></span>
									<div class='evcal_evdata_cell'>
										<p>{$addCalContent}</p>	
									</div>
								</div>";
						}
					break;
						
			
				// Related Events @+2.8
					case 'relatedEvents':
						$events = $EVENT->get_prop('ev_releated');
						$events = !empty($events)? json_decode($events, true): false;

						if($events && is_array($events)){
							
							ob_start();
							?>
								<div class='evo_metarow_rel_events evorow evcal_evdata_row'>
									<span class='evcal_evdata_icons'><i class='fa <?php echo get_eventON_icon('evcal__fai_relev', 'fa-calendar-plus',$evOPT );?>'></i></span>
									<div class='evcal_evdata_cell'>
										<h3 class='evo_h3'><?php echo evo_lang('Related Events');?></h3>
										<div class='evcal_cell_rel_events'>
										<?php

										$rel_events = array();

										foreach($events as $I=>$N){
											$id = explode('-', $I);
											$EE = new EVO_Event($id[0]);
											$x = isset($id[1])? $id[1]:'0';
											$time = $EE->get_formatted_smart_time($x);

											$imgs = $EE->get_image_urls();
											$img_html = '<span class="noimg" style="background-color:#'. $EE->get_hex() .'"></span>';
											if($imgs){
												$img_html = '<span class="" style="background-image: url('. $imgs['medium'].')"></span>';
											}

											
											$rel_events[ $I .'.'. $EE->get_start_time() ] =  "<a href='". $EE->get_permalink($x). "'>
												".$img_html."
												<b>{$N}</b><em>{$time}</em>
												</a>";
										}

										//krsort($rel_events);
										foreach($rel_events as $html){
											echo $html;
										}
										?>
										</div>
									</div>
								</div>
							<?php
							$OT.= ob_get_clean();

						}
					break;
				
				// Virtual Event
					case 'virtual':
						if($EVENT->is_virtual() && !$EVENT->is_cancelled()):
							ob_start();

							$vir = new EVO_Event_Virtual($EVENT);
							echo $vir->get_eventcard_cell_html();
							
							$OT.= ob_get_clean();
						endif;
					break;

				// health guidance
					case 'health':


						if( !$EVENT->check_yn('_health')) break;

						$EVENT->localize_edata('_edata');

						ob_start();
						?>
						<div class='evo_metarow_health evorow evcal_evdata_row'>
							<span class='evcal_evdata_icons'><i class='fa <?php echo get_eventON_icon('evcal__fai_health', 'fa-heartbeat',$evOPT );?>'></i></span>
							<div class='evcal_evdata_cell'>
								<h3 class='evo_h3'><?php echo evo_lang('Health Guidelines for this Event');?></h3>
								<div class='evcal_cell'>
									<div class='evo_card_health_boxes'>
									<?php

									foreach(apply_filters('evo_healthcaredata_frontend', array(
										'_health_mask'=> array('svg','mask', evo_lang('Masks Required')),
										'_health_temp'=> array('i','thermometer-half', evo_lang('Temperature Checked At Entrance')),
										'_health_pdis'=> array('svg','distance', evo_lang('Physical Distance Maintained')),
										'_health_san'=> array('i','clinic-medical', evo_lang('Event Area Sanitized')),
										'_health_out'=> array('i','tree', evo_lang('Outdoor Event')),
										'_health_vac'=> array('i','syringe', evo_lang('Vaccination Required')),
									), $EVENT) as $k=>$v){

										if(!$EVENT->echeck_yn( $k )) continue;
										
										echo "<div class='evo_health_b_o'><div class='evo_health_b'>";
									 	if($v[0]=='svg')
											echo "<svg class='evo_svg_icon' enable-background='new 0 0 20 20' height='20' viewBox='0 0 512 512' width='20' xmlns='http://www.w3.org/2000/svg'>". EVO()->elements->svg->get_icon_path( $v[1]) ."</svg>";
										if($v[0]=='i') echo "<i class='fa fa-{$v[1]}'></i>";
										echo "<span>". $v[2] ."</span>
										</div></div>";
									}

									?>

									</div>

									<?php if($EVENT->get_eprop('_health_other')):?>
									<div class='evo_health_b ehb_other'>
										<span class='evo_health_bo_title'>
											<i class='fa fa-laptop-medical'></i><span><?php evo_lang_e('Other Health Guidelines');?></span>
										</span>
										<span class='evo_health_bo_data'><?php echo $EVENT->get_eprop('_health_other');?></span>
									</div>
									<?php endif;?>
										

								</div>
							</div>
						</div>
						<?php 	$OT.= ob_get_clean();


					break;

				// paypal link
						case 'paypal':
							$ev_txt = $EVENT->get_prop('evcal_paypal_text');
							$text = ($ev_txt)? $ev_txt: evo_lang_get('evcal_evcard_tix1','Buy ticket via Paypal');

							$currency = !empty($evOPT['evcal_pp_cur'])? $evOPT['evcal_pp_cur']: false;
							$email = ($EVENT->get_prop('evcal_paypal_email')? $EVENT->get_prop('evcal_paypal_email'): $evOPT['evcal_pp_email']);

							if($currency && $email):
								$_event_time = $EVENT->get_formatted_smart_time();							
								
								ob_start();
							?>
							

							<div class='evo_metarow_paypal evorow evcal_evdata_row evo_paypal'>
								<span class='evcal_evdata_icons'><i class='fa <?php echo get_eventON_icon('evcal__fai_007', 'fa-ticket',$evOPT );?>'></i></span>
								<div class='evcal_evdata_cell'>
									<p class='evcal_evdata_cell_title' style='padding-bottom:5px;'><?php echo $text;?></p>
									<form target="_blank" name="_xclick" action="https://www.paypal.com/us/cgi-bin/webscr" method="post">
										<input type="hidden" name="cmd" value="_xclick">
										<input type="hidden" name="business" value="<?php echo $email;?>">
										<input type="hidden" name="currency_code" value="<?php echo $currency;?>">
										<input type="hidden" name="item_name" value="<?php echo $EVENT->post_title.' '.$_event_time;?>">
										<input type="hidden" name="amount" value="<?php echo $EVENT->get_prop('evcal_paypal_item_price');?>">
										<input type='submit' class='evcal_btn' value='<?php echo evo_lang_get('evcal_evcard_btn1','Buy Now');?>'/>
									</form>										
								</div></div>							
							<?php $OT.= ob_get_clean();
							endif;

						break;

				// social share
					case 'evosocial':
						$__calendar_type = EVO()->calendar->__calendar_type;
						$evo_opt = $evOPT;

						$event_id = $EVENT->ID;
						$repeat_interval = $EVENT->ri;
						$event_post_meta = get_post_custom($event_id);

						// Event Times
							$dateTime = new evo_datetime();
							$unixes = $dateTime->get_correct_event_repeat_time($event_post_meta, $repeat_interval);
							$datetime_string = $dateTime->get_formatted_smart_time($unixes['start'], $unixes['end'],$event_post_meta);

						
						// check if social media to show or not
						if( (!empty($evo_opt['evosm_som']) && $evo_opt['evosm_som']=='yes' && $__calendar_type=='single') 
							|| ( empty($evo_opt['evosm_som']) ) || ( !empty($evo_opt['evosm_som']) && $evo_opt['evosm_som']=='no' ) ){
							
							$post_title = get_the_title($event_id);
							$event_permalink = get_permalink($event_id);

							// Link to event
								$permalink = $EVENT->get_permalink();
								$encodeURL = EVO()->cal->check_yn('evosm_diencode','evcal_1') ? $permalink:  urlencode($permalink);

							// thumbnail
								$img_id = get_post_thumbnail_id($event_id);
								$img_src = ($img_id)? wp_get_attachment_image_src($img_id,'thumbnail'): false;

							// event details
								$summary = EVO()->frontend->filter_evo_content(get_post_field('post_content',$event_id));

							$title 		= str_replace('+','%20',urlencode($post_title));
							$titleCOUNT = $post_title;
							$summary = (!empty($summary)? urlencode(eventon_get_normal_excerpt($summary, 16)): '--');
							$imgurl = $img_src? urlencode($img_src[0]):'';
							
							
							$output_sm = EVO()->calendar->helper->get_social_share_htmls(array(
								'post_title'=> $post_title,
								'summary'=> $summary,
								'imgurl'=> $imgurl,
								'permalink'=> $permalink,
								'encodeURL'=> $encodeURL,
								'datetime_string'=> $datetime_string
							));

							if(!empty($output_sm)){
								$O= "<div class='evo_metarow_socialmedia evcal_evdata_row '>".$output_sm."</div>";
								return $O;
							}
						}
					
						EVO()->calendar->__calendar_type ='default';

					break;
				
			}// end switch

			// for custom meta data fields
				if(!empty($object->x) && $box_name == 'customfield'.$object->x){
					
					$i18n_name = eventon_get_custom_language($evoOPT2,'evcal_cmd_'.$object->x , $evOPT['evcal_ec_f'.$object->x.'a1']);

					// user role restriction access validation
					if( 
						($object->visibility_type=='admin' && !current_user_can( 'manage_options' ) ) ||
						($object->visibility_type=='loggedin' && !is_user_logged_in() && empty($object->login_needed_message))
					){}else{

						// value processing with passed on {}
						$VV = $object->value;

						$VV = $EVENT->process_dynamic_tags( $VV );					


						$OT .="<div class='evo_metarow_cusF{$object->x} evorow evcal_evdata_row evcal_evrow_sm '>
								<span class='evcal_evdata_icons'><i class='fa ".$object->imgurl."'></i></span>
								<div class='evcal_evdata_cell'>							
									<h3 class='evo_h3'>".$i18n_name."</h3>";

							// if visible only to loggedin users and user is not logged in
							if( !empty($object->login_needed_message)){
								$OT .="<div class='evo_custom_content evo_data_val'>". $object->login_needed_message . "</div>";
							}else{
								if($object->type=='button'){

									$link = $EVENT->process_dynamic_tags( $object->valueL );			

									$_target = (!empty($object->_target) && $object->_target=='yes')? 'target="_blank"':null;
									$OT .="<a href='". $link ."' {$_target} class='evcal_btn evo_cusmeta_btn'>". $VV ."</a>";
								}else{
									$OT .="<div class='evo_custom_content evo_data_val'>". 
									(  EVO()->frontend->filter_evo_content( $VV ) )."</div>";
								}
							}
						
						$OT .="</div></div>";
					}
				}

		return $OT;
	}


// SEO Schema data
	function get_schema($EventData, $_eventcard){
		extract($EventData);
		$EVENT = $this->EVENT;

		//print_r($EventData);

		$__scheme_data = '<div class="evo_event_schema" style="display:none" >';

		$tz = strpos($this->timezone, '-') === false? '+'. $this->timezone : $this->timezone;


		// Start time 
			$_schema_starttime = $_schema_endtime = '';
			if(is_array($start_date_data))
				$_schema_starttime = $start_date_data['Y'].'-'.$start_date_data['n'].'-'.$start_date_data['j'].( !$EVENT->is_all_day()? 'T'.$start_date_data['H'].':'.$start_date_data['i']. $tz. ':00' :'');
			if(is_array($end_date_data))
				$_schema_endtime = $end_date_data['Y'].'-'.$end_date_data['n'].'-'.$end_date_data['j']. ( !$EVENT->is_all_day()? 'T'.$end_date_data['H'].':'.$end_date_data['i'].$tz. ':00':'');

		// Event Status
			$ES = array(
				'cancelled'=>'https://schema.org/EventCancelled',
				'movedonline'=>'https://schema.org/EventMovedOnline',
				'postponed'=>'https://schema.org/EventPostponed',
				'rescheduled'=>'https://schema.org/EventRescheduled',
			);

			$_ES = isset($ES[$_status])? $ES[$_status]: 'https://schema.org/EventScheduled';

		
		// Event details				
			$__schema_desc = !empty($event_excerpt_txt)? $event_excerpt_txt : (isset($EVENT->post_title)? '"'.$EVENT->post_title.'"':'');
			
			if(!empty($event_details)) $__schema_desc = $event_details;
			
			$__schema_desc = str_replace("'","'", $__schema_desc);
			$__schema_desc = str_replace('"',"'", $__schema_desc);
			$__schema_desc = preg_replace( "/\r|\n/", " ", $__schema_desc );

		// attendence mode
			$AM = ucfirst( $EVENT->get_attendance_mode() );
			$_AM = 'https://schema.org/'. $AM .'EventAttendanceMode';
		
		if(!empty($schema) && $schema){	
			// for each schema custom values
			foreach(apply_filters('evo_event_schema',array(
				'url'=>array(
					'type'=>'a',
					'attr'=>'href',
					'attrcontent'=> $EVENT->get_permalink()
				),					
				'image'=>array(
					'type'=>'meta',
					'content'=> (!empty($img_src) &&!empty($img_src)? $img_src:'')
				),					
				'startDate'=>array(
					'type'=>'meta',
					'content'=> $_schema_starttime
				),
				'endDate'=>array(
					'type'=>'meta',
					'content'=> $_schema_endtime
				),
				'eventStatus'=>array(
					'type'=>'meta',
					'content'=>  $_ES
				),
			),$EVENT, $EVENT->ID) as $key=>$value){
				$__scheme_data .= "<".(!empty($value['type'])?$value['type']:'meta') ." itemprop='{$key}' ".(!empty($value['content'])? 'content="'.$value['content'].'"':'') ." ". ( !empty($value['attr'])? $value['attr']."='". $value['attrcontent']."'":'');

				if(!empty($value['itemtype'])) $__scheme_data .= ' itemscope itemtype="'.$value['itemtype'].'"';
				
				$__scheme_data .= ($value['type'] =='meta')? "/>": ">";
				$__scheme_data .= (!empty($value['html'])?$value['html']:'');
				$__scheme_data .= (isset($value['type']) && $value['type'] == 'meta')? '': 
					( isset($value['type'])? "</".$value['type'] .">" :'' ); 
			}
			
			// location data
				if( !empty($location_type) && $location_type =='virtual'){
					$__scheme_data .= '<item style="display:none" itemprop="location" itemscope itemtype="http://schema.org/VirtualLocation">';
					if(!empty($location_link)) $__scheme_data .= '<span itemprop="url">'.$location_link.'</span>';
					$__scheme_data .= "</item>";

					//$_AM = 'https://schema.org/OnlineEventAttendanceMode';
					
				}

				if(!empty($location_address)){

					$__scheme_data .= '<item style="display:none" itemprop="location" itemscope itemtype="http://schema.org/Place">'. ( !empty($location_name)? '<span itemprop="name">'.$location_name.'</span>':'').'<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><item itemprop="streetAddress">'. stripslashes($location_address) .'</item></span></item>';					}

				$__scheme_data .= '<item style="display:none" itemprop="eventAttendanceMode" itemscope itemtype="'.$_AM.'"></item>';

			// offer data
				if( $EVENT->get_prop('_seo_offer_price') && $EVENT->get_prop('_seo_offer_currency')){
					$__scheme_data .= '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				        <div class="event-price" itemprop="price" content="'.$EVENT->get_prop('_seo_offer_price').'">'.$EVENT->get_prop('_seo_offer_price').'</div>
				        <meta itemprop="priceCurrency" content="'.$EVENT->get_prop('_seo_offer_currency').'">
				        <meta itemprop="url" content="'.$EVENT->get_permalink().'">
				        <meta itemprop="availability" content="http://schema.org/InStock">
				        <meta itemprop="validFrom" content="'.$_schema_starttime.'">
				    </div>';
				}

		    // organizer data
			    if(!empty($organizer) && isset($organizer->name)){
				    $__scheme_data .= '<div itemprop="organizer" itemscope="" itemtype="http://schema.org/Organization">
				    	<meta itemprop="name" content="'.$organizer->name.'">
				    	'. (!empty($organizer_link)? '<meta itemprop="url" content="'.$organizer_link.'">':'').
				    '</div>';
				}

			// performer data using organizer data
				if( $EVENT->get_prop('evo_event_org_as_perf') && !empty($organizer) && isset($organizer->name)){
					 $__scheme_data .= '<div itemprop="performer" itemscope="" itemtype="http://schema.org/Person">
				    	<meta itemprop="name" content="'.$organizer->name.'">
				    </div>';
				}
		}else{
			$__scheme_data .= '<a href="'.$event_permalink.'"></a>';
		}

		// JSON LD
		if(!empty($schema_jsonld) && $schema_jsonld){
			$__scheme_data .= '<script type="application/ld+json">';				

			// event status
			$_schema_eventstatus = ',"eventStatus":"'. $_ES .'"';

			// location
				$_schema_location = ''; 
				
				if(!empty($location_type) && $location_type == 'virtual' || !empty($location_address)){
					$_schema_location .= ',"location":';
				}

				if(!empty($location_type) && $location_type == 'virtual' || !empty($location_address))
					$_schema_location .= '[';

				if(!empty($location_type) && $location_type == 'virtual'){
					$_schema_location .= '{"@type":"VirtualLocation"';
					if(!empty($location_link)) $_schema_location .= ',"url":"'.$location_link.'"';
					$_schema_location .= '}';
				}
				if(!empty($location_address)){

					if(!empty($location_type) && $location_type == 'virtual')
						$_schema_location .= ',';

					$_name = !empty($location_name)? '"name":"'.$location_name.'",':'';
					$location_name = str_replace('"', "", $location_name);
					$_schema_location .= '{"@type":"Place",'.$_name.'"address":{"@type": "PostalAddress","streetAddress":"'. str_replace("\,",",", stripslashes($location_address) ).'"}}';
				}
				if(!empty($location_type) && $location_type == 'virtual' || !empty($location_address)){
					$_schema_location .= ']';
				}

			// organizer 
				$_schema_performer = $_schema_organizer = '';
				if(!empty($organizer) && isset($organizer->name)){
					$_schema_organizer = ',"organizer":{"@type":"Organization","name":"'.$organizer->name.'"'. 
						( !empty($organizer_link)? ',"url":"'.$organizer_link.'"':'').
						'}';				
				}

			// perfomer data using organizer
				if( $EVENT->get_prop('evo_event_org_as_perf') && !empty($organizer) && isset($organizer->name) ){
					$_schema_performer = ',"performer":{"@type":"Person","name":"'.$organizer->name.'"}';
				}

			// offers field
				$_schema_offers = '';
				if( $EVENT->get_prop('_seo_offer_price') && $EVENT->get_prop('_seo_offer_currency')){
					$_schema_offers = ',"offers":{"@type":"Offer","price":"'. $EVENT->get_prop('_seo_offer_price') .'","priceCurrency":"'.$EVENT->get_prop('_seo_offer_currency').'","availability":"http://schema.org/InStock","validFrom":"'.$_schema_starttime.'","url":"'.$EVENT->get_permalink().'"}';
				}

			$__scheme_data .= 
				'{"@context": "http://schema.org","@type": "Event",
				"@id": "event_'. $EVENT->get_event_uniqid().'",
				"eventAttendanceMode":"'. $_AM .'",
				"name": '.(isset($EVENT->post_title)? '"'.htmlspecialchars( $EVENT->post_title, ENT_QUOTES ) .'"' :'').',
				"url": "'. $EVENT->get_permalink() .'",
				"startDate": "'.$_schema_starttime.'",
				"endDate": "'.$_schema_endtime.'",
				"image":'.(!empty($img_src) &&!empty($img_src)? '"'.$img_src.'"':'""').', 
				"description":"'.$__schema_desc.'"'.
			  	$_schema_location.
			  	$_schema_organizer.
			  	$_schema_performer.
			  	$_schema_offers.
			  	$_schema_eventstatus.
			  	apply_filters('eventon_event_json_schema_adds', '', $EVENT, $EVENT->ID).
			'}';
			$__scheme_data .= "</script>";
		}
		$__scheme_data .= "</div>";

		return $__scheme_data;
	}
}

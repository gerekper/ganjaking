<?php
/**
 *  Frontend supporting functions
 *  @version  1.0
 */
class evoss_functions{
	
	function speaker_fields(){
		// field ID
		// name
		// placehodler
		return $fields = apply_filters('evoss_speaker_fields', array(
			'evo_speaker_name'=>array('evoss_name', __('Event Speaker Name','eventon'), __('eg. Jamison Anderson','eventon') ),
			'evo_speaker_title'=>array('evoss_title', __('Event Speaker Title','eventon'), __('eg. CEO','eventon') ),
			'evo_speaker_desc'=>array('evoss_desc', __('Event Speaker Description','eventon'), __('eg. Speaker is an awesome guy','eventon') ),
			'evoss_fb'=>array('evoss_fb', __('Facebook','eventon'), __('eg. http://www.facebook.com','eventon') ),
			'evoss_tw'=>array('evoss_tw', __('Twitter','eventon'), __('eg. http://www.twitter.com','eventon') ),
			'evoss_ig'=>array('evoss_ig', __('Instagram','eventon'), __('eg. http://www.instagram.com','eventon') ),
			'evoss_ln'=>array('evoss_ln', __('Linkedin','eventon'), __('eg. http://www.linkedin.com','eventon') ),
			'evoss_url'=>array('evoss_url', __('URL','eventon'), __('eg. http://www.speaker.com','eventon') ),
			'evo_spk_img'=>array('evo_spk_img'),
		));
	}

	function speaker_fields_processed($event_tax_term =''){
		return array(
			'term_name'=>array(
				'type'=>'text',
				'name'=> __('Event Speaker Name','evoss'),
				'placeholder'=>__('eg. Jamison Anderson','evoss'),
				'value'=> ($event_tax_term? $event_tax_term->name:''),
				'var'=>	'term_name',
				'legend'=> __('NOTE: If you change the speaker name, it will create a new speaker.','evoss')
			),
			'description'=>array(
				'type'=>'wysiwyg',
				'name'=>__('Speaker Description','evoss'),
				'var'=>'description',
				'value'=> ($event_tax_term? $event_tax_term->description:''),				
			),
			'evo_speaker_title'=>array(
				'type'=>'text','name'=>__('Event Speaker Title','evoss'),'var'=>'evo_speaker_title'				
			),
			'evo_speaker_company'=>array(
				'type'=>'text','name'=>__('Company Name','evoss'),'var'=>'evo_speaker_company'				
			),
			'evoss_fb'=>array('type'=>'text','name'=>__('Facebook','evoss'),'placehodler'=>__('eg. http://www.facebook.com','evoss'),'var'=>'evoss_fb'),
			'evoss_tw'=>array('type'=>'text','name'=>__('Twitter','evoss'),'placehodler'=>__('eg. http://www.twitter.com','evoss'),'var'=>'evoss_tw'),
			'evoss_ig'=>array('type'=>'text','name'=>__('Instagram','evoss'),'placehodler'=>__('eg. http://www.instagram.com','evoss'),'var'=>'evoss_ig'),
			'evoss_ln'=>array('type'=>'text','name'=>__('Linkedin','evoss'),'placehodler'=>__('eg. http://www.linkedin.com','evoss'),'var'=>'evoss_ln'),
			'evoss_url'=>array('type'=>'text','name'=>__('URL','evoss'),'placehodler'=>__('eg. http://www.speaker.com','evoss'),'var'=>'evoss_url'),
			'evo_spk_img'=>array('type'=>'image','name'=>__('Image','evoss'),'var'=>'evo_spk_img'),
			//'submit'=>array('type'=>'button',)
		);
	}
	function schedule_fields(){
		// field ID
		// name
		// placehodler
		return $fields = apply_filters('evoss_schedule_fields', array(
			'evo_sch_title'=>array('evossh_title', 
				__('Schedule Block Title','eventon'),
				__('eg. Registration & Greeting','eventon'), 
				'required' ),
			'evo_sch_date'=>array('evossh_date', __('Block Date','eventon') ),
			'evo_sch_stime'=>array('evossh_stime', __('Block Start Time','eventon'), 
				__('eg. 8:00am','eventon') ,''),
			'evo_sch_etime'=>array('evossh_etime', __('Block End Time','eventon'), __('eg. 8:00am','eventon'),'' ),
			'evo_sch_desc'=>array('evossh_desc', __('Description','eventon') ,'',),
			'evo_sch_spk'=>array('evossh_spk', 
				__('Speakers','eventon') ),
		));
	}

	function get_selected_item_html($speakerTerm, $termMeta){

		$termID =  $speakerTerm->term_id;
		$existing_tax_ids[] = $termID;

		$termmeta = evo_get_term_meta('event_speaker',$termID, $termMeta);

		ob_start();
		// image
			$img_url = false;
			if(!empty($termmeta['evo_spk_img'])){
				$img_url = wp_get_attachment_image_src($termmeta['evo_spk_img'],'thumbnail');
			}

		echo "<p class='evo_tax_term' data-termid='{$termID}' title='".__('Click to edit','eventon')."'>";
		echo ($img_url)? "<b><img src='{$img_url[0]}'/></b>":'';
		echo "<span>". $speakerTerm->name. (!empty($termmeta['evo_speaker_title'])? '<em>'. stripslashes( $termmeta['evo_speaker_title'] ) .'</em>':'')."</span>";
		echo "<i TITLE='".__('Click to Remove','eventon')."'>X</i>";
		echo "</p>";

		return ob_get_clean();
	}

	function get_tax_select_list($term, $checked){
		ob_start();
		echo "<span><b data-value='{$term->term_id}' class='fa fa-{$checked}'></b>
			<em>".$term->name."</em></span>";
		return ob_get_clean();
	}
	function get_speakers_array(){
		$speakers = get_terms('event_speaker', array('hide_empty'=>false) );
		$spk = array();
		if(count($speakers)>0){
			foreach($speakers as $speaker){
				$spk[ $speaker->term_id] = $speaker->name;
			}
		}
		return $spk;
	}
// Schedule
	function save_schedule($EVENT, $data, $day, $key){

		$blocks = $EVENT->get_prop('_sch_blocks')? $EVENT->get_prop('_sch_blocks'): array();

		$blocks[$day][0] = $data['evo_sch_date']; // save the readable date
		$blocks[$day][$key] = $data;

		// remove existing duplications
		$blocks = $this->remove_duplicated_blocks($blocks, $key , $day);

		// remove empty blocks 
		$blocks = $this->remove_empty_block_days( $blocks);
			
		//print_r($blocks);
		
		$EVENT->set_prop( '_sch_blocks', $blocks);

		return $blocks;
	}

	function remove_duplicated_blocks($blocks, $key, $day ){
		$count = 0;
		foreach($blocks as $d=>$dd){
			foreach($dd as $k=>$v){
				if( $k == 0 ) continue;
				if( $k == $key && $d == $day){ 
					$count++;
				}elseif($k == $key){
					unset($blocks[$d][$k]);
				}
			}
		}
		return $blocks;
	}
	// remove block days without any schedule from the array
	function remove_empty_block_days($blocks){
		foreach($blocks as $d=>$dd){
			if( count($dd) >1) continue;
			unset($blocks[$d]);
		}
		return $blocks;
	}
	function delete_schedule($EVENT, $day, $key){
		
		$blocks = $EVENT->get_prop('_sch_blocks')? $EVENT->get_prop('_sch_blocks'): array();

		if(count($blocks)==0) return true;

		unset($blocks[$day][$key]);
		// remove empty blocks 
		$blocks = $this->remove_empty_block_days( $blocks);

		$EVENT->set_prop( '_sch_blocks', $blocks);

		return $blocks;
	}

	// HTML for backend schedule section
	function get_schedule_html($blocks, $EVENT){
		$nav = $content = '';

		$speakers = $this->get_speakers_array();
		$help = new evo_helper();

		//ksort($blocks);

		$block_count = 1;
		for($v=1; $v<=100; $v++){

			$day_ = 'd'.$v;
			if( empty($blocks[$day_])) continue;

			$block = $blocks[$day_];

			
			// if the block only have date values left which is 1 item
			if(count($block)==1) continue;

			$day = substr($day_, 1);

			// nav
				$nav .= "<li class='".( $block_count==1?'show':'')."' data-day='{$day}' title='".$block[0]."'>Day ".$day."</li>";
						
			$content.= "<ul class='evosch_oneday_schedule ".($block_count==1?'show':'')." evosch_date_{$day}'>";

			foreach($block as $key=>$data){
				if($key==0) continue;

				// button data
				$edit_btn_data = array(
					'lbvals'=> array(
						'lbc'=>'evo_config_schedule',
						't'=>__('Edit Schedule Block','evoss'),
						'ajax'=>'yes',
						'd'=> array(
							'uid'=>'evoss_edit_schedule',
							'type'=>'edit',
							'key'=> $key,
							'day'=> $day_,
							'eventid'=> $EVENT->ID,
							'load_new_content'=> true,
							'action'=> 'evoss_form_schedule'
						)
					)
				);

				$delete_btn_data = array(
					'd'=> array(
						'ajaxdata'=> array(
							'key'=> $key,
							'day'=> $day_,
							'eventid'=> $EVENT->ID,
							'action'=> 'evoss_delete_schedule',
						),
						'uid'=> 'evoss_del_schedule',
					)
				);

				$content.= "<li id='{$key}' class='evosch_block' data-day='{$day_}'>";

				$time = '-';
				if( isset($data['evo_sch_stime'])) $time = $data['evo_sch_stime'];
				if( isset($data['evo_sch_etime'])) $time .= '- '.$data['evo_sch_etime'];

				$content.= "<p><em>(".$time.') '.$data['evo_sch_title']."</em>";
				$content.= "<i ". $help->array_to_html_data($edit_btn_data) ." class='fa fa-pencil evolb_trigger'></i>";
				$content.= "<i ". $help->array_to_html_data($delete_btn_data) ." class='fa fa-times evo_trigger_ajax_run'></i>";

				$content.= "<i class='fa fa-bars'></i>";
				$content.= "</p>";
			
				$content.= "</li>";
			}	
			$content.= "</ul>";
			$block_count++;	
		}

		return (!empty($nav)? "<ul class='evosch_nav'>".$nav."</ul>":''). $content;					
	}

	function get_schedule_form_html($event_id, $key='', $day=''){
		
		$EVENT = new EVO_Event($event_id);

		$blocks = $EVENT->get_prop('_sch_blocks')? $EVENT->get_prop('_sch_blocks'): array();

		// get current block information if exist
		$thisblock = false;
		if(!empty($key) && !empty($day) ){
			if(!empty($blocks[$day][$key]))
				$thisblock = $blocks[$day][$key];
		}
		ob_start();
		?>
		<form>
			<?php 
			echo EVO()->elements->process_multiple_elements(
				array(
					array('type'=>'hidden','name'=>'action','value'=>'evoss_save_schedule'),
					array('type'=>'hidden','name'=>'eventid','value'=>$event_id),
					array('type'=>'hidden','name'=>'key','value'=>$key),
					array('type'=>'hidden','name'=>'day','value'=>$day),
				)
			);
			?>
		<div id='evosch_new_block_form' >
			<div class='evo_tax_entry evoselectfield_saved_data sections' >
			<?php if(empty($day) && empty($key)){
				echo "<h3 style='padding-bottom:15px'>".__('New Schedule Block','eventon'). "</h3>";
			}
			?>
		<?php
			foreach($this->schedule_fields() as $field=>$var){
				if( in_array($field, array('evo_sch_spknm')) ) continue;

				$value = !empty($thisblock[$field]) ? $thisblock[$field]: false;
				$required = !empty($var[3]) && $var[3] == 'required' ? ' *':'';
				$label = $var[1].$required;

				if($field=='evo_sch_date'):

					$inbetween_days = $this->eventdates($EVENT );

					?>
					<p class='schedule_date' style='display:flex'>
						<span class='_sch_date' style='padding-right: 5px;'>
							<label for='<?php echo $field;?>'><?php echo $label;?></label>
							<select class='evoss_field <?php echo $var[0];?>' name="<?php echo $field;?>"><?php
								$count = 1;

								foreach( $inbetween_days as $date){
									$selected = (!empty($day) && 'd'.$count==$day)? 'selected="selected"':'';
									echo "<option data-date='d{$count}' value='{$date}' {$selected}>Day ".$count.': '.$date."</option>";
									$count++;
								}
								?>							
							</select>							
						</span>
						<span class="_sch_day">

							<label ><?php _e('Alternative Day Number','evoss')?></label>
							<select class='evoss_field' name='evo_sch_alt_day'>
								<option value='na'>--</option>
								<?php 
								$c = 1;
								foreach( $inbetween_days as $date){
									$selected = (!empty($day) && 'd'.$c==$day)? 'selected="selected"':'';
									echo "<option value='d{$c}' {$selected}>Day ".$c."</option>";
									$c++;
								}
								?>
							</select>
							
						</span>

						
					</p>
					<?php
				elseif($field=='evo_sch_desc'):
					echo EVO()->elements->get_element(
						array('type'=>'wysiwyg','id'=>$field,
							'value'=>$value? $value:'',
							'name'=>$label
						)
					);

				elseif($field=='evo_sch_spk'):
					$speakers = get_terms('event_speaker', array('hide_empty'=>false) );
					if(! empty( $speakers ) && ! is_wp_error( $speakers )):
					?>
					<p class='evo_sch_spk'>
						<label for='<?php echo $field;?>'><?php echo $label;?></label>
						<?php										
						foreach($speakers as $spk){
							$termid = $spk->term_id;
							$checked = ($value && in_array($spk->name, $value))?'checked':'';
							echo "<span style='margin-right:10px'><input class='evoss_field {$var[0]}' type='checkbox' name='{$field}[{$termid}]' value='".$spk->name."' {$checked}/>". $spk->name .'</span>';
						}
						?>
					</p>
					<?php
					endif;
				else:
					?>
					<p>
						<label for='<?php echo $field;?>'><?php echo $label;?></label>
						<input type='text' class='evoss_field <?php echo $var[0];?>' name='<?php echo $field;?>' value="<?php echo $value? $value:'';?>" style='width:100%' placeholder='<?php echo !empty($var[2])? $var[2]:'';?>'/></p>
					<?php
				endif;
			}
		?>		
			<p><span class="evo_btn evoss_add_new_schedule" ><?php _e('Save Schedule Block','eventon');?></span>
			</p>
			</div>					
		</div>
	</form>
		<?php

		return ob_get_clean();
	}

	// Get event dates for creating schedule
	function eventdates($EVENT){
		$start = explode('-', date('z-Y-M-j',$EVENT->get_prop('evcal_srow') ));
		$end = explode('-', date('z-Y-M-j',$EVENT->get_prop('evcal_erow') ));

		// if it is a repeating event
			$repeat = false;
			if( $EVENT->is_repeating_event() ){
				$repeat = true;
				$repeat_times = $EVENT->get_repeats();
			}

		// make the date format sync with wordpress date format value
		$date_format = get_option('date_format');

		$ouput = array();
			if($repeat){
				$string = '';
				foreach($repeat_times as $data){
					$start = (int)date( 'z',$data[0] );
					$end = (int)date( 'z',$data[1] );

					if($start == $end){
						$output[] = date($date_format, $data[0]);
					}else{
						$count = 0;
						for($x=$start; $x<=$end; $x++){
							$date = strtotime("+".$count." day", $data[0] );
							$output[] = date($date_format, $date);
							$count++;
						}
					}
				}
			}else{
				if($start[0] == $end[0]){
					$output[] = date($date_format, $EVENT->get_prop('evcal_srow') );
				}else{					
					$count = 0;
					$x = $EVENT->get_prop('evcal_srow');
					while( $x <= $EVENT->get_prop('evcal_erow') ){

						$output[] = date($date_format, $x);
						$x += 86400;
						$count ++;
					}
				}
			}

		return $output;		
	}
}
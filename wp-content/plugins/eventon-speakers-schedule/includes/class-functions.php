<?php
/**
 *  Frontend supporting functions
 *  @version  0.6
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
	function schedule_fields(){
		// field ID
		// name
		// placehodler
		return $fields = apply_filters('evoss_schedule_fields', array(
			'evo_sch_title'=>array('evossh_title', 
				__('Schedule Block Title','eventon'),
				__('eg. Registration & Greeting','eventon'), 
				'required' ),
			'evo_sch_date'=>array('evossh_date', 
				__('Block Date','eventon') ),
			'evo_sch_stime'=>array('evossh_stime', 
				__('Block Start Time','eventon'), 
				__('eg. 8:00am','eventon') ,
				'required'),
			'evo_sch_etime'=>array('evossh_etime', 
				__('Block End Time','eventon'), 
				__('eg. 8:00am','eventon'),'required' ),
			'evo_sch_desc'=>array(
				'evossh_desc', 
				__('Description','eventon') ,
				'',
				'required'),
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
		echo "<span>". $speakerTerm->name. (!empty($termmeta['evo_speaker_title'])? '<em>'.$termmeta['evo_speaker_title'].'</em>':'')."</span>";
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
	function save_schedule($eventid, $data, $day, $key, $epmv=''){
		$epmv = (!empty($epmv))? $epmv: get_post_custom($eventid);

		$blocks = !empty($epmv['_sch_blocks'])? unserialize($epmv['_sch_blocks'][0]): array();

		$blocks[$day][0]=$data['evo_sch_date'];
		$blocks[$day][$key]=$data;
		
		update_post_meta($eventid, '_sch_blocks', $blocks);
		return $blocks;
	}
	function delete_schedule($eventid, $day, $key, $epmv=''){
		$epmv = (!empty($epmv))? $epmv: get_post_custom($eventid);
		$blocks = !empty($epmv['_sch_blocks'])? unserialize($epmv['_sch_blocks'][0]): array();

		if(count($blocks)==0) return true;

		unset($blocks[$day][$key]);
		update_post_meta($eventid, '_sch_blocks', $blocks);
		return $blocks;
	}

	// HTML for backend schedule section
	function get_schedule_html($blocks){
		$nav = $content = '';

		$speakers = $this->get_speakers_array();

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
				$content.= "<li id='{$key}' class='evosch_block' data-day='{$day_}'>";
				$content.= "<p><b>(".$data['evo_sch_stime'].'-'.$data['evo_sch_etime'].') '.$data['evo_sch_title']."</b> <i>".__('Edit','eventon')."</i><em>".__('Delete','eventon')."</em></p>";
				$content.= "<p class='evosch_desc'>".$data['evo_sch_desc']."</p>";

				if( !empty($data['evo_sch_spk'])){
					$spkContent = implode(', ', $data['evo_sch_spk']);
					$content.= "<p class='evosch_spks'>".__('Speakers:','eventon').' '.$spkContent."</p>";
				}

				$content.= "</li>";
			}	
			$content.= "</ul>";
			$block_count++;	
		}

		return (!empty($nav)? "<ul class='evosch_nav'>".$nav."</ul>":''). $content;					
	}

	function get_schedule_form_html($eventid, $key='', $day=''){
		global $evo_speak;

			$epmv = (!empty($epmv))? $epmv: get_post_custom($eventid);
			$blocks = !empty($epmv['_sch_blocks'])? unserialize($epmv['_sch_blocks'][0]): array();

			// get current block information if exist
			$thisblock = false;
			if(!empty($key) && !empty($day) ){
				if(!empty($blocks[$day][$key]))
					$thisblock = $blocks[$day][$key];
			}
			ob_start();
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
					?>
					<p>
						<select class='evoss_field <?php echo $var[0];?>' name="<?php echo $field;?>"><?php
						$count = 1;

						foreach($this->eventdates($epmv) as $date){
							$selected = (!empty($day) && 'd'.$count==$day)? 'selected="selected"':'';
							echo "<option data-date='d{$count}' value='{$date}' {$selected}>Day ".$count.': '.$date."</option>";
							$count++;
						}
						?></select>
						<label for='<?php echo $field;?>'><?php echo $label;?></label>
					</p>
					<?php
				elseif($field=='evo_sch_desc'):
					?>
					<p><textarea class='evoss_field <?php echo $var[0];?>' name="<?php echo $field;?>" rows="4" style='width:100%'><?php echo $value? $value:'';?></textarea><label for='<?php echo $field;?>'><?php echo $label;?></label></p>
					<?php
				elseif($field=='evo_sch_spk'):
					$speakers = get_terms('event_speaker', array('hide_empty'=>false) );
					if(! empty( $speakers ) && ! is_wp_error( $speakers )):
					?>
					<p class='evo_sch_spk'>
					<?php										
						foreach($speakers as $spk){
							$termid = $spk->term_id;
							$checked = ($value && in_array($spk->name, $value))?'checked':'';
							echo "<span><input class='evoss_field {$var[0]}' type='checkbox' name='{$field}[{$termid}]' value='".$spk->name."' {$checked}/>". $spk->name .'</span>';
						}
					?><label for='<?php echo $field;?>'><?php echo $label;?></label>
					</p>
					<?php
					endif;
				else:
					?>
					<p><input type='text' class='evoss_field <?php echo $var[0];?>' name='<?php echo $field;?>' value="<?php echo $value? $value:'';?>" style='width:100%' placeholder='<?php echo !empty($var[2])? $var[2]:'';?>'/><label for='<?php echo $field;?>'><?php echo $label;?></label></p>
					<?php
				endif;
			}
		?>		
			<p><span class="evo_btn evoss_add_new_schedule" data-eventid='<?php echo $eventid;?>'  data-key='<?php echo !empty($key)? $key:'';?>'><?php _e('Save Schedule Block','eventon');?></span>
			</p>
			</div>					
		</div><!-- form-->
		<?php

		return ob_get_clean();
	}

	// Get event dates for creating schedule
	function eventdates($epmv){
		$start = explode('-', date('z-Y-M-j',$epmv['evcal_srow'][0]));
		$end = explode('-', date('z-Y-M-j',$epmv['evcal_erow'][0]));

		// if it is a repeating event
			$repeat = false;
			if( !empty($epmv['evcal_repeat']) && $epmv['evcal_repeat'][0] == 'yes' && !empty($epmv['repeat_intervals'])){
				$repeat = true;
				$repeat_times = (unserialize($epmv['repeat_intervals'][0]));
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
					$output[] = date($date_format, $epmv['evcal_srow'][0]);
				}else{
					$count = 0;
					for($x=$start[0]; $x<=$end[0]; $x++){
						$date = strtotime("+".$count." day", $epmv['evcal_srow'][0] );
						$output[] = date($date_format, $date);
						$count++;
					}
				}
			}

		return $output;		
	}
}
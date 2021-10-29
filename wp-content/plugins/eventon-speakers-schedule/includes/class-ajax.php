<?php
/** 
 * EVOSS - ajax
 * @version 0.1
 */
class EVOSS_ajax{
	public function __construct(){
		$ajax_events = array(
				'evoss_new_speaker'=>'evoss_new_speaker',
				'evoss_change_speaker'=>'evoss_change_speaker',
				'evoss_get_speaker_values'=>'evoss_get_speaker_values',
				'evoss_get_schedule'=>'evoss_get_schedule',
				'evoss_save_schedule'=>'evoss_save_schedule',
				'evoss_delete_schedule'=>'evoss_delete_schedule',
				'evoss_form_schedule'=>'evoss_form_schedule',
				'evoss_save_schedule_order'=>'evoss_save_schedule_order',
			);
			foreach ( $ajax_events as $ajax_event => $class ) {				
				add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
				add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
			}
	}

// Speakers
	// add new speaker via backend
		function evoss_new_speaker(){
			global $evo_speak;

			$term_name = esc_attr(stripslashes($_POST['evo_speaker_name']));
			$term = term_exists( $term_name, 'event_speaker');
			$post_id = $_POST['eventid'];

			$termid = false;

			// Term Exist
			if($term !== 0 && $term !== null){
				wp_set_object_terms( $post_id, $term_name, 'event_speaker', true);
				$termid = isset($_POST['termid'])?$_POST['termid']: $term->term_id;
				if(!empty($_POST['evo_speaker_desc'])){
					wp_update_term($termid, 'event_speaker', array(
						'description'=> stripslashes($_POST['evo_speaker_desc'])
					));
				}				
			}else{
				// create slug from name
					$trans = array(" "=>'-', ","=>'');
					$term_slug= strtr($term_name, $trans);

				// create wp term
				$new_term_ = wp_insert_term( $term_name, 'event_speaker', array(
					'slug'=>$term_slug,
					'description'=> (!empty($_POST['evo_speaker_desc'])? $_POST['evo_speaker_desc']: '')
				) );

				// if term created correctly
				if(!is_wp_error($new_term_)){
					$termid = (int)$new_term_['term_id'];
					wp_set_object_terms( $post_id, array($termid), 'event_speaker', true);
				}	
			}

			// if term good, save term meta values
			if($termid){
				foreach($evo_speak->functions->speaker_fields() as $field=>$var){
					if($field=='evo_speaker_desc') continue;
					if(!empty($_POST[$field])){
						$newtermmeta[$field]= $_POST[$field];
					}
				}
				evo_save_term_metas('event_speaker',$termid, $newtermmeta);	

				// get new content
					$speaker_terms = wp_get_post_terms($post_id, 'event_speaker');
					$content = ''; $list = ''; $existing_tax_ids = array();

					// if terms exists
					if ( $speaker_terms && ! is_wp_error( $speaker_terms ) ){
						$termMeta = get_option( "evo_tax_meta");
						foreach($speaker_terms as $speakerTerm){
							$existing_tax_ids[] = $speakerTerm->term_id;
							$content .= $evo_speak->functions->get_selected_item_html($speakerTerm, $termMeta);
						}
						$content .= "<div class='clear'></div>";
					}

					// get updated terms list
						$allTerms = get_terms('event_speaker', array('hide_empty'=>false) );
						foreach ( $allTerms as $term ) {
							$checked = (count($existing_tax_ids)>0 && in_array($term->term_id, $existing_tax_ids))?
								'dot-circle-o': 'circle-o';
							$list.= $evo_speak->functions->get_tax_select_list($term, $checked);
							if($checked=='dot-circle-o') $selectedSpeakers[] = $term->term_id;
						}

						$list .= "<input type='hidden' class='evo_tax_selected_list_values' name='event_speakers' value='".implode(',',$selectedSpeakers)."'/>";

				echo json_encode(array(
					'content'=>$content, 
					'list'=>$list,
					'status'=>'good',
					'msg'=>__('Speaker Updated Successfully!','eventon')
				));
				exit;
			}else{
				echo json_encode(array(
					'status'=>__('Could not create term','eventon')
				));
				exit;
			}	
		}

	// change speakers
		function evoss_change_speaker(){
			global $evo_speak;

			$post_id = $_POST['eventid'];

			// convert to array of speaker selection
				if(isset($_POST['values'])){
					$term_ids = explode(',', $_POST['values']);
					$term_ids = array_filter($term_ids);
					$term_ids = array_map('intval', $term_ids);
					$term_ids = array_unique($term_ids);
				}else{ $values = '';}

			// save new speaker selection values
				wp_set_object_terms( $post_id, $term_ids, 'event_speaker', false);

			// get new set values section HTML
				$speaker_terms = wp_get_post_terms($post_id, 'event_speaker');
				$content = '';

				// if terms exists
				if ( $speaker_terms && ! is_wp_error( $speaker_terms ) ){
					$termMeta = get_option( "evo_tax_meta");
					foreach($speaker_terms as $speakerTerm){
						$content .= $evo_speak->functions->get_selected_item_html($speakerTerm, $termMeta);
					}
					$content .= "<div class='clear'></div>";
				}

			echo json_encode(array(
				'content'=>$content, 'status'=>'good'
			));
			exit;
		}	

	// get speaker values
		function evoss_get_speaker_values(){
			$termid = (int)$_POST['termid'];

			$term = get_term($termid, 'event_speaker');
			$termMeta = get_option( "evo_tax_meta");

			$termmeta = evo_get_term_meta('event_speaker',$termid, $termMeta);

			// imamge
				$img_url = '';
				if(!empty($termmeta['evo_spk_img'])){
					$img_url = wp_get_attachment_image_src($termmeta['evo_spk_img'],'medium');
					$img_url = $img_url[0];
				}
			$termmeta['evo_speaker_name'] = $term->name;
			$termmeta['evo_speaker_desc'] = $term->description;

			echo json_encode(array(
				'status'=>'good',
				'meta'=>$termmeta,
				'imgsrc'=>$img_url
			));
			exit;
		}

// Schedule
	// order
		function evoss_save_schedule_order(){
			$EID = (int)$_POST['eventid'];
			$blocks = get_post_meta($EID,'_sch_blocks',true);
			$order = $_POST['order'];

			$NO = array();
			foreach($order as $day=>$DD){
				$NO[$day] = array();
				$NO[$day][0] = $blocks[$day][0];
				foreach($DD as $block){
					if(!isset($blocks[$day][$block])) continue;
					$NO[$day][$block] = $blocks[$day][$block];
				}
			}

			update_post_meta($EID,'_sch_blocks', $NO);
			echo json_encode(array(	'status'=>'good','r'=>$NO, 'b'=>$blocks, 'o'=>$order	));exit;
		}
	// get
		function evoss_get_schedule(){
			$eventid = $_POST['eventid'];
			$key = $_POST['key'];
			$day = $_POST['day'];

			$epmv = (!empty($epmv))? $epmv: get_post_custom($eventid);
			$blocks = !empty($epmv['_sch_blocks'])? unserialize($epmv['_sch_blocks'][0]): array();

			$content = (!empty($blocks[$day][$key]))? $blocks[$day][$key]: false;
			echo json_encode(array(
				'status'=>'good',
				'content'=>$content
			));
			exit;
		}
	// save schedule block
		function evoss_save_schedule(){
			global $evo_speak;

			$eventid = $_POST['eventid'];

			$key = empty($_POST['key'])? uniqid(): $_POST['key'];
			$day = $_POST['day'];
			$alt_day = (isset($_POST['evo_sch_alt_day']) && $_POST['evo_sch_alt_day'] != 'na') ? $_POST['evo_sch_alt_day']: false;

			// override day number with alternative day name
			if($alt_day) $day = $alt_day;

			$data = $pass = array();
			foreach($evo_speak->functions->schedule_fields() as $field=>$val){
				if(!empty($_POST[ $field ]))
					$data[$field] = $_POST[ $field ];
			}

			//$pass[$day][$id] = $data;

			$newblocks = $evo_speak->functions->save_schedule($eventid, $data, $day, $key); 

			$content = $evo_speak->functions->get_schedule_html($newblocks);

			echo json_encode(array(
				'status'=>'good',
				'content'=>$content
			));
			exit;
		}
	// delete
		function evoss_delete_schedule(){
			global $evo_speak;

			$eventid = $_POST['eventid'];
			$key = $_POST['key'];
			$day = $_POST['day'];

			$newblocks = $evo_speak->functions->delete_schedule($eventid, $day, $key); 
			$content = $evo_speak->functions->get_schedule_html($newblocks);

			echo json_encode(array(
				'status'=>'good',
				'content'=>$content
			));
			exit;
		}
	// get form
		function evoss_form_schedule(){
			global $evo_speak;
			$eventid = $_POST['eventid'];
			$key = isset($_POST['key'])? $_POST['key']:'';
			$day = isset($_POST['day'])? $_POST['day']:'';

			echo json_encode(array(
				'status'=>'good',
				'content'=>$evo_speak->functions->get_schedule_form_html($eventid, $key, $day)
			));
			exit;
		}
	
}
new EVOSS_ajax();
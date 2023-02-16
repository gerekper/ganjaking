<?php
/** 
 * EVOSS - ajax
 * @version 2.0
 */
class EVOSS_ajax{
	public function __construct(){
		$ajax_events = array(
			'evoss_save_schedule'=>'evoss_save_schedule',
			'evoss_delete_schedule'=>'evoss_delete_schedule',
			'evoss_form_schedule'=>'evoss_form_schedule',
			'evoss_save_schedule_order'=>'evoss_save_schedule_order',
			'evoss_get_speaker_details'=>'get_speaker_details',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}

		$this->help = new evo_helper();
		$this->postdata = $this->help->sanitize_array($_POST);
	}

// speaker
	// get speaker details for the lightbox
	function get_speaker_details(){

		$EVENT = new EVO_Event($this->postdata['eventid']);
		$speaker_id = $this->postdata['speaker_id'];

		$speaker = $EVENT->get_term_data('event_speaker', $speaker_id);

		ob_start();
		$img_url = EVOSS()->assets_path.'speaker.jpg';
		$termmeta = $speaker->termmeta;
		if(!empty($speaker->termmeta['evo_spk_img'])){
			$img_url = wp_get_attachment_image_src($speaker->termmeta['evo_spk_img'],'medium');
			$img_url = isset($img_url[0])? $img_url[0]: '';
		}
		?>
		<div class='evoss_speaker_lb'>
			<div class="evospk_img">
				<span style='background-image: url(<?php echo $img_url;?>)'></span></div>
			<div class="evospk_info">
				<h2><?php echo $speaker->name;?></h2>
				<?php 
					// title
					if(!empty($termmeta['evo_speaker_title']))
						echo "<p class='evo_speaker_title'>".  stripslashes( $termmeta['evo_speaker_title'] ) .'</p>';

					// description
					if(!empty($speaker->description)){
						echo "<div class='evo_speaker_desc'>". apply_filters('the_content',$speaker->description) .'</div>';
					}

					$social = $other = false;
					foreach(EVOSS()->functions->speaker_fields() as $key=>$val){
						//print_r($key);
						if(in_array($key, array('evo_spk_img','evo_speaker_name','evo_speaker_title','evo_speaker_desc'))) continue;

						if(empty($termmeta[$key])) continue;

						// social media link
						if(in_array($key, array('evoss_fb','evoss_tw','evoss_ln','evoss_ig'))){
							$social.="<a target='_blank' href='".$termmeta[$key]."' class='fa fa-".strtolower($val[1])."'></a>";
						
						// all other extra fields
						}else{

							$field_val = ($key=='evoss_url')? "<a target='_blank' href='". $termmeta[$key] ."'>".$termmeta[$key]."</a>": $termmeta[$key];
							$other.= "<p class='{$key} extra'><em>". $val[1]  .'</em> '.$field_val.'</p>';
						}												
					}

					// social media
						if($social){
							echo "<p class='evo_speaker_social'>".$social.'</p>';
						}
						echo $other;
				?>
			</div>
		</div>

		<?php 

		$content = ob_get_clean();
		echo json_encode(array(	'status'=>'good',
			'content'=>$content
		));exit;


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
	// get form
		function evoss_form_schedule(){
			
			$postdata = $this->postdata;

			$event_id = $postdata['eventid'];
			$key = isset($postdata['key'])? $postdata['key']:'';
			$day = isset($postdata['day'])? $postdata['day']:'';

			echo json_encode(array(
				'status'=>'good',
				'content'=>EVOSS()->functions->get_schedule_form_html($event_id, $key, $day)
			));
			exit;
		}
	// save schedule block
		function evoss_save_schedule(){
			
			$postdata = $this->postdata;

			$eventid = $postdata['eventid'];
			$EVENT = new EVO_Event( $eventid );

			$key = empty($postdata['key'])? uniqid(): $postdata['key'];
			$day = $postdata['day'];
			$alt_day = (isset($postdata['evo_sch_alt_day']) && $postdata['evo_sch_alt_day'] != 'na') ? $postdata['evo_sch_alt_day']: false;

			// override day number with alternative day name
			if($alt_day) $day = $alt_day;

			$data = $pass = array();
			foreach(EVOSS()->functions->schedule_fields() as $field=>$val){
				if(!empty($postdata[ $field ]))
					$data[$field] = $postdata[ $field ];
			}

			// description html processing
			if( isset($postdata['evo_sch_desc'])){
				$data['evo_sch_desc'] = $_POST['evo_sch_desc'];
			}


			$newblocks = EVOSS()->functions->save_schedule($EVENT, $data, $day, $key); 

			$content = EVOSS()->functions->get_schedule_html($newblocks, $EVENT);

			echo json_encode(array(
				'status'=>'good',
				'msg'=> __('New Schedule Created Successfully!'),
				'content'=>$content,
			));
			exit;
		}
	// delete
		function evoss_delete_schedule(){
			
			$eventid = $_POST['eventid'];
			$EVENT = new EVO_Event( $eventid );

			$key = $_POST['key'];
			$day = $_POST['day'];

			$newblocks = EVOSS()->functions->delete_schedule($EVENT, $day, $key); 
			$content = EVOSS()->functions->get_schedule_html($newblocks, $EVENT);

			echo json_encode(array(
				'status'=>'good',
				'msg'=> __('Schedule Successfully Deleted!'),
				'content'=>$content
			));
			exit;
		}
	
	
}
new EVOSS_ajax();
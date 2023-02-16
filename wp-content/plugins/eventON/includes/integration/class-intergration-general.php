<?php
/**
 * General integration with other 3rd party providers
 * @version 3.0.8
 */

class EVO_Int_General{
	public function __construct(){
		if(is_admin()){		
			add_action( 'wp_ajax_evo_jitsi_settings', array( $this, 'evo_jitsi_settings' ) );
			add_action( 'wp_ajax_evo_jitsi_save', array( $this, 'jitsi_save' ) );
		}
	}


// jitsi
	function evo_jitsi_settings(){

		global $ajde;

		$eid = (int)$_POST['eid'];
				
		$E = new EVO_Event( $eid);
		$E->get_event_post();
		$help = new evo_helper();

		$E->localize_edata('_evojitsi'); // localize event data array
		
		// meeting name using event name
			$mtg_id = $E->get_eprop('_mtg_id');
			$mtg_id = $mtg_id? $mtg_id: md5($E->post_title);

		extract($E->get_start_end_times());
		
		ob_start();

		$ajde->wp_admin->_print_date_picker_values();

		?>
		<div style='padding:20px;'>
		<form class='evoz_form'>
			<?php 
			echo EVO()->elements->process_multiple_elements(
				array(					
					array('type'=> 'hidden','id'=>'action', 'value'=>'evo_jitsi_save'),
					array('type'=> 'hidden','id'=>'event_id', 'value'=> $eid),
					array('type'=> 'hidden','id'=>'mtg_id', 'value'=> $mtg_id),
				)
			);
			?>

			<p class='' style='display: <?php echo ($mtg_id)? 'block':'none';?>'><?php _e('Jitsi Meeting ID','eventon');?>: <b><?php echo $mtg_id;?></b></p>
										

			<p>
				<label><?php _e('Password','eventon');?></label>
				<input type="text" class='R' name="_vir_pass" value='<?php echo $E->get_virtual_pass();?>'/>
				<em style='opacity: 0.5'><?php _e('Leave empty to not have access password','eventon');?></em>
			</p>
			<?php 	
		

			echo EVO()->elements->process_multiple_elements(
				array(
					array(
						'type'=>'yesno_btn','id'=>'_raise_hand',
						'value'=>	$E->get_eprop('_raise_hand'),
						'label'=> __('Allow viewers to raise hand','eventon'),
					),array(
						'type'=>'yesno_btn',
						'id'=>'_invite',
						'value'=>	$E->get_eprop('_invite'),
						'label'=> __('Enable Invite others','eventon'),
					),array(
						'type'=>'yesno_btn',
						'id'=>'_fullscreen',
						'value'=>	$E->get_eprop('_fullscreen'),
						'label'=> __('Enable viewer to switch to fullscreen','eventon'),
					),array(
						'type'=>'yesno_btn',
						'id'=>'_desktop',
						'value'=>	$E->get_eprop('_desktop'),
						'label'=> __('Enable viewer share desktop','eventon'),
					),array(
						'type'=>'yesno_btn',
						'id'=>'_feedback',
						'value'=>	$E->get_eprop('_feedback'),
						'label'=> __('Enable Viewer to leave feedback','eventon'),
					),array(
						'type'=>'yesno_btn',
						'id'=>'_stats',
						'value'=>	$E->get_eprop('_stats'),
						'label'=> __('Enable Speaker Stats','eventon'),
					),array(
						'type'=>'yesno_btn',
						'id'=>'_shortcuts',
						'value'=>	$E->get_eprop('_shortcuts'),
						'label'=> __('Enable viewer to see keyboard shortcuts','eventon'),
					),array(
						'type'=>'yesno_btn',
						'id'=>'_mute-everyone',
						'value'=>	$E->get_eprop('_mute-everyone'),
						'label'=> __('Enable mute everyone','eventon'),
					),array(
						'type'=>'yesno_btn',
						'id'=>'_recording',
						'value'=>	$E->get_eprop('_recording'),
						'label'=> __('Enable viewer to record video ','eventon'),
						'tooltip'=> __('This will allow viewer to record event video and upload to dropbox.','eventon'),
						'tooltip_position'=>'L'
					),array(
						'type'=>'yesno_btn',
						'id'=>'_sharedvideo',
						'value'=>	$E->get_eprop('_sharedvideo'),						
						'label'=> __('Enable viewer to share a youtube video','eventon'),
						'tooltip'=> __('Viewers can share a youtube video with this option.','eventon'),
						'tooltip_position'=>'L'
					),array(
						'type'=>'yesno_btn',
						'id'=>'startWithAudioMuted',
						'value'=>	$E->get_eprop('startWithAudioMuted'),						
						'label'=> __('Start with audio muted','eventon'),
						'tooltip'=> __('Both mod and guest will start with audio muted from mic.','eventon'),
					),array(
						'type'=>'yesno_btn',
						'id'=>'startWithVideoMuted',
						'value'=>	$E->get_eprop('startWithVideoMuted'),						
						'label'=> __('Start with video muted','eventon'),
						'tooltip'=> __('Both mod and guest will start with video muted from camera.','eventon'),
					),
									
					
				)
			);			
			?>

			<div class='actions'>
				<p><span class='evo_btn evo_jitsi_save'><?php _e('Update Jitsi Options','eventon');?></span></p>
			</div>

			
			<p><i style='opacity: 0.7'><?php _e('IMPORTANT: Jitsi video will appear only on single event page, every where else will link to single event page.','eventon');?></i></p>
			<p><i style='opacity: 0.7'><?php _e('For moderator: You can visit event page at any time and join the event before everyone. Until the moderator joins, event access will be hidden to others.','eventon');?></i></p>
			<p><i style='opacity: 0.7'><?php _e('Other Information: Until the moderator is joined, the viewers will see a message. Once the moderator is signed in, the viewers page will refresh at above mentioned refresh rate and will reload page wth event access open for them.','eventon');?></i></p>
		</form>
		</div>
		<?php
		echo json_encode(array(
			'status'=>'good','content'=> ob_get_clean()
		));exit;

	}

	public function jitsi_save(){

		$help = new evo_helper();
		$PP = $help->sanitize_array( $_POST);

		$E = new EVO_Event( $PP['event_id']);
		$E->localize_edata('_evojitsi');

		foreach($PP as $k=>$v){
			if( in_array($k, array( '_vir_pass','mtg_id','event_id','action' ))) continue;
			if(empty($v)) continue;

			$E->set_eprop($k, $v , false);
		}

		$E->save_eprops('_evojitsi');

		$E->set_prop('_vir_pass', (isset($PP['_vir_pass'])? $PP['_vir_pass']:'') , false);
		$E->set_prop('_vir_url', $PP['mtg_id'] , false);

		echo json_encode(array(
			'status'=>'good',
			'msg'=> __('Jitsi event information saved successfully.','eventon'),
			'pass'=>	$PP['_vir_pass'],
			'join_url'=> $PP['mtg_id']
		)); exit;		
	}

	
}

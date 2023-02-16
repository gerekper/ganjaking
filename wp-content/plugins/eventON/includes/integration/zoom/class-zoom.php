<?php
/**
Zoom integration with eventon
@version 2.8.10
*/

class EVO_Zoom_Int{

	public function __construct(){
		if(is_admin()){
			add_filter('eventon_settings_3rdparty', array($this, 'settings'),10,1);

			add_action( 'wp_ajax_evo_zoom_settings', array( $this, 'ajax_zoom_settings' ) );
			add_action( 'wp_ajax_evo_zoom_connect', array( $this, 'ajax_zoom_connect' ) );
		}
	}

	function ajax_zoom_settings(){

		global $ajde;

		$eid = (int)$_POST['eid'];

		EVO()->cal->set_cur('evcal_1');

		$api_key = EVO()->cal->get_prop('_evo_zoom_key');
		$api_secret = EVO()->cal->get_prop('_evo_zoom_secret');

		// validate key and secret
		if( !$api_key || !$api_secret){
			echo json_encode(array(
				'status'=>'bad','content'=> "<div style='padding:20px;'>".__('Zoom API Access information must be saved under EventON Settings > Third Party APIs','eventon') ."!</div>"
			));exit;
		}

		$E = new EVO_Event( $eid);
		$E->get_event_post();
		$help = new evo_helper();

		include_once('connect.php');
		$ZM = new EVO_Zoom_Run();	

		$E->localize_edata('_evoz'); // localize event data array
		
		// meeting name using event name
			$mt_name = $E->get_eprop('_evoz_n');
			$mt_name = $mt_name? $mt_name: $E->post_title;

		extract($E->get_start_end_times());

		
		ob_start();

		$ajde->wp_admin->_print_date_picker_values();

		$mtg_id = false;
		// format zoom meeting id
		if($mtg_id = $E->get_eprop('_evoz_mtg_id')){
			$mtg_id = $this->_get_zoom_mtg_id( $mtg_id );
		}

		?>
		<div style='padding:20px;'>
		<form class='evoz_form'>
			<input type="hidden" name="type" value='<?php echo $mtg_id? 'edit':'new';?>' class='form_type'>
			<input type="hidden" name="action" value='evo_zoom_connect'>
			<input type="hidden" name="event_id" value='<?php echo $eid;?>'>
			<input type="hidden" name="_evoz_mtg_id" value='<?php echo $E->get_eprop('_evoz_mtg_id');?>'>
			
			<p class='evoz_mtg_id' style='display: <?php echo ($mtg_id)? 'block':'none';?>'><?php _e('Zoom Meeting','eventon');?> <a href='https://zoom.us/meeting/<?php echo $E->get_eprop('_evoz_mtg_id');?>' class='evo_admin_btn btn_secondary ' target='_blank'><?php echo $mtg_id;?></a></p>
			
			<p>
				<label><?php _e('Meeting Name','eventon');?></label>
				<input type="text" name="_evoz_n" value='<?php echo $mt_name;?>'/>
			</p>
			<p>
				<label><?php _e('When','eventon');?></label>
				<span class='evo_date_time_picker_box'>
					<?php				
					EVO()->elements->print_date_time_selector(
						array(
							'date_format' => (empty(EVO()->calendar->date_format)? get_option('date_format'): EVO()->calendar->date_format ),
							'date_format_hidden'=>'Y/m/d',
							'unix'=> ( ( $zu = $E->get_eprop('_evoz_unix'))? $zu: $start)
						)
					);	?>
				</span>
			</p>
			<p>
				<label><?php _e('Duration','eventon');?></label>
				<span class='evo_date_time_picker_box'>
					<?php
					$ajde->wp_admin->print_time_selector(
						array( 'minutes'=> $E->get_eprop('_evoz_d'), 'var'=>'_evoz_d')
					);
					?>
				</span>
				<em style='opacity: 0.5'><?php _e('Based on your zoom plan, this meeting duration might be restricted','eventon');?></em>
			</p>
			<p>
				<label><?php _e('Time Zone','eventon');?></label>
				<select name='_evoz_tz'><?php 

				$_evoz_tz = $E->get_eprop('_evoz_tz');
				foreach($help->get_timezone_array(false, true) as $f=>$v){
					echo "<option value='{$f}' ". ( $_evoz_tz && $_evoz_tz == $f? 'selected="selected"':'') .">{$v}</option>";
				}

				?></select>
			</p>
			

			<p>
				<label><?php _e('Password','eventon');?></label>
				<input type="text" class='R' name="_evoz_pw" value='<?php echo $E->get_eprop('_vir_pass');?>'/>
				<em style='opacity: 0.5'><?php _e('Leave empty to auto generate pass','eventon');?></em>
			</p>
			<?php 

			echo EVO()->elements->process_multiple_elements(
				array(
					array(
						'type'=>'yesno_btn',
						'id'=>'_evoz_mtg_auth',
						'var'=>	$E->get_eprop('_evoz_mtg_auth'),
						'label'=> __('Allow only loggedin user via zoom app to join','eventon'),
						'tooltip'=> __('Users logged in via zoom app can only join the event.','eventon'),
						'tooltip_position'=>'L'
					),array(
						'type'=>'yesno_btn',
						'id'=>'_evoz_jbh',
						'var'=>	$E->get_eprop('_evoz_jbh'),
						'label'=> __('Join event before host start the meeting','eventon'),
						'tooltip'=> __('Allow participants to join the meeting before the host starts the meeting. This is only for scheduled or recurring meetings','eventon'),
						'tooltip_position'=>'L'
					),array(
						'type'=>'yesno_btn',
						'id'=>'_evoz_hv',
						'var'=>	$E->get_eprop('_evoz_hv'),
						'label'=> __('Turn on host video on when joining meeting','eventon'),
					),array(
						'type'=>'yesno_btn',
						'id'=>'_evoz_pv',
						'var'=>	$E->get_eprop('_evoz_pv'),
						'label'=> __('Turn on participant video on when joining meeting','eventon'),
					),array(
						'type'=>'yesno_btn',
						'id'=>'_evoz_mpoj',
						'var'=>	$E->get_eprop('_evoz_mpoj'),
						'label'=> __('Mute participant on joining the meeting','eventon'),
					),array(
						'type'=>'yesno_btn',
						'id'=>'_evoz_ewr',
						'var'=>	$E->get_eprop('_evoz_ewr'),
						'label'=> __('Enable waiting room','eventon'),
					),
				)
			);


			
			?>
			<p>
				<label><?php _e('Automatic Recording','eventon');?></label>
				<select name='_evoz_arec'><?php 

				$_evoz_arec = $E->get_eprop('_evoz_arec');
				foreach(array(
					'none'=>'Disabled',
					'local'=>'Record on local',
					'cloud'=> 'Record on clound'
				) as $f=>$v){
					echo "<option value='{$f}' ". ( $_evoz_arec && $_evoz_arec == $f? 'selected="selected"':'') .">{$v}</option>";
				}
				?></select>
			</p>

			<div class='actions'>
			<?php if($mtg_id):?>
				<p>
					<span class='evo_btn evoz_connect'><?php _e('Update Meeting with Zoom','eventon');?></span>
					<span class='evo_btn evoz_connect del'><?php _e('Delete Meeting','eventon');?></span>
				</p>
			<?php else:?>
				<p><span class='evo_btn evoz_connect'><?php _e('Create Meeting','eventon');?></span></p>
			<?php endif;?>
			</div>

			<p><i style='opacity: 0.5'><?php _e('NOTE: If you switched to a different virtual event broadcasting method, already created zoom meetings will be deleted automatically.','eventon');?></i></p>
		</form>
		</div>
		<?php
		echo json_encode(array(
			'status'=>'good','content'=> ob_get_clean()
		));exit;

	}

	// connecting to zoom API for creating or updating
	function ajax_zoom_connect(){
		
		$P = $_POST;
		$E = new EVO_Event( (int)$P['event_id'] );
		$DD = EVO()->calendar->DD;

		include_once('connect.php');
		$ZM = new EVO_Zoom_Run();	

		$date = explode('/', $P['event_start_date_x']);
		$DD->setDate($date[0], $date[1], $date[2]);
		$DD->setTime($P['_start_hour'], $P['_start_minute']);

		// localize event data array
		$E->localize_edata('_evoz');


		$data = array(
			'meetingTopic'=> $P['_evoz_n'],
			'userId'=>'me',
			'timezone'=> (isset($P['_evoz_tz']) ? $P['_evoz_tz'] : 'UTC'),
			'start_unix'=> $DD->format('U'),
			'password'=> $P['_evoz_pw'],
			'duration'=> (int)$P['_evoz_d']
		);	

		foreach( array(
			'_evoz_mtg_auth','_evoz_jbh', '_evoz_hv', '_evoz_pv','_evoz_mpoj','_evoz_ewr','_evoz_arec'
		) as $f){
			if(isset($P[$f])) $data[$f] = $P[ $f];
		}

		// creating a new meeting
		if($P['type'] == 'new'){		

			$R = $ZM->create_meeting( $data);

			// if a meeting was created correctly
			if($R && !empty($R->id) ){
				
				$this->update_event_meta( $R, $P, $E, $DD->format('U'));
		
				echo json_encode(array(
					'status'=>'good',
					'id'=> $R->id,
					'join_url'=> $R->join_url,
					'pass'=> $R->password,
					'msg'=> __('Successfully Created Meeting.','eventon'),
					'action_html'=> "<p>
						<span class='evo_btn evoz_connect'>".__('Update Meeting with Zoom','eventon')."</span>
						<span class='evo_btn evoz_connect del'>".__('Delete Meeting','eventon')."</span>
					</p>",
					'r'=> $R,
				));
			}else{
				echo json_encode(array(
					'status'=>'bad','msg'=> __('Could not connect with zoom API, try again later or create meeting manual at zoom.com','eventon'),
					'r'=> $R,
				));
			}

		// editing the meeting
		}elseif($P['type'] == 'edit'){

			$data['meeting_id'] = (int)$P['_evoz_mtg_id'];
			$R = $ZM->update_meeting( $data );

			if($R){
				// meeting does not exists in zoom
				if( !empty($R->code) && $R->code == 3001){
					
					$this->delete_event_meeting_meta( $E );
					
					echo json_encode(array(
						'status'=>'bad',
						'code'=> 3001,
						'msg'=> __('Meeting does not exist in zoom, you may create a new meeting','eventon'),
						'action_html'=> "<p><span class='evo_btn evoz_connect'>".__('Create Meeting','eventon')."</span></p>"
					));
				// meeting was updated
				}elseif( !empty($R->id)){

					$this->update_event_meta( $R, $P, $E, $DD->format('U'));
					
					echo json_encode(array(
						'status'=>'good',
						'id'=> $R->id,
						'join_url'=> $R->join_url,
						'pass'=> $R->password,
						'msg'=> __('Successfully Updated Meeting.','eventon')
					));
				}else{
					echo json_encode(array(
						'status'=>'bad',
						'msg'=> __('Could not connect with zoom API, try again later or create meeting manual at zoom.com','eventon'),
						'r'=> $R
					));
				}

			}else{
				echo json_encode(array(
					'status'=>'bad',
					'msg'=> __('Could not update with zoom. Make sure your event is schedule in future.','eventon'),
					'error'=>'no_return_from_zoom',
					'd'=> $data
				));
			}


		// delete
		}elseif($P['type'] == 'delete'){

			if(!isset($P['_evoz_mtg_id'])){
				echo json_encode(array(
					'status'=>'bad',
					'msg'=> __('Meeting ID is missing.','eventon'),
					'type'=>'delete',
				));
			}

			$R = $ZM->delete_meeting( (int)$P['_evoz_mtg_id'] );

			// return back with a response code
			if($R && !empty($R->code)){
				$this->delete_event_meeting_meta( $E );
				echo json_encode(array(
					'status'=>'good',
					'code'=> $R->code,
					'msg'=> ( !empty($R->message)? $R->message.' ':''). __('Local meeting data has been deleted','eventon'),
					'type'=>'delete',
					'action_html'=> "<p><span class='evo_btn evoz_connect'>".__('Create Meeting','eventon')."</span></p>",
					'r'=> $R
				));
				
			}else{
				$this->delete_event_meeting_meta( $E );
				echo json_encode(array(
					'status'=>'good',
					'msg'=> __('Meeting deleted Successfully.','eventon'),
					'action_html'=> "<p><span class='evo_btn evoz_connect'>".__('Create Meeting','eventon')."</span></p>",
					'type'=>'delete',
				));
			}
		}
		exit;
	}

	// SUPPORTIVES
	function update_event_meta($R, $P, $E, $unix){
		if(!empty($R->id)) $E->set_eprop('_evoz_mtg_id', $R->id, false);
		if(!empty($R->start_url)) $E->set_eprop('_evoz_start_url', $R->start_url, false);
		if(!empty($R->duration)) $E->set_eprop('_evoz_d', $R->duration, false);
		$E->set_eprop('_evoz_unix', $unix, false);

		foreach( $this->_extra_zoom_meeting_meta_fields() as $f){
			if(!isset($P[$f])) continue;
			$E->set_eprop($f, $P[$f] , false);
		}

		$E->save_eprops('_evoz');

		$E->set_prop('_vir_url', $R->join_url, false);
		$E->set_prop('_vir_pass', $R->password , false);
	}
	function delete_event_meeting_meta($E){
		$E->del_mul_prop( array('_vir_url','_vir_pass'));
		$E->del_mul_eprop( array('_evoz_mtg_id','_evoz_start_url','_evoz_d','_evoz_unix') );
		$E->del_mul_eprop( $this->_extra_zoom_meeting_meta_fields() );
	}
	function _extra_zoom_meeting_meta_fields(){
		return array('_evoz_jbh','_evoz_tz','_evoz_hv', '_evoz_pv','_evoz_ewr','_evoz_mpoj','_evoz_rmopc');
	}

	function _get_zoom_mtg_id($id){
		$a = substr($id, 0, 3);
		$b = substr($id, 3, 4);
		$c = substr($id, 7);
		return $a.' '. $b.' '. $c;

	}

	// EventON Settings
	function settings($A){
		$B = array(
			array('type'=>'sub_section_open','name'=>__('Zoom','eventon')),
			array('id'=>'_evo_zoom','type'=>'yesno','name'=>__('Enable Zoom API','eventon'),'afterstatement'=>'_evo_zoom', 'legend'=>'This will allow you to integrate zoom direct into each event using the API information from zoom.'),
			array('id'=>'_evo_zoom','type'=>'begin_afterstatement'),

			array('id'=>'_evo_zoom_key','type'=>'text',
				'name'=>__('API Key','eventon'),'hideable'=>true
			),	
			array('id'=>'_evo_zoom_secret','type'=>'text',
				'name'=>__('API Secret Key','eventon'), 'hideable'=>true
			),	
			array('id'=>'_evo_zoom_note','type'=>'note',
				'name'=> sprintf('<a href="%s" target="_blank">%s</a>',
					'http://docs.myeventon.com/documentations/how-to-find-zoom-api-keys/',
					__('Learn how to find Zoom API key and other information.','eventon') 
				)
			),			
						
			array('id'=>'_evo_zoom','type'=>'end_afterstatement'),
			array('type'=>'sub_section_close'),
		);

		return array_merge($A, $B);
	}
}

new EVO_Zoom_Int();
<?php
/** 
 * Eventcard virtual event html content
 * @version 4.0.6
 */

	
class EVO_Event_Virtual{

	public $current_user;
	public $is_live_now = false;
	public $is_past = false;
	public $vir_type;
	public $_is_user_moderator;

	public $single_override = false;
	public function __construct($EVENT, $ri=''){

		if( is_numeric($EVENT)) $EVENT = new EVO_Event( $EVENT, '', $ri);

		$this->EVENT = $this->event = $EVENT;
		$this->current_user = wp_get_current_user();

		$this->is_past = $this->EVENT->is_vir_event_ended();
		$this->is_live_now = $this->EVENT->is_event_live_now();
		$this->vir_type = $this->EVENT->virtual_type();

		$moderator = $this->EVENT->get_prop('_mod');
		if($moderator){
			if( $this->current_user && $this->current_user->ID == $moderator ) 
				$this->_is_user_moderator = true;
		}else{
			if( current_user_can('administrator')) $this->_is_user_moderator = true;
		}
		
		do_action('evo_vir_initial_setup', $this);

	}

	public function get_eventcard_cell_html(){

		$EV = $this->event;


		// debug end

		ob_start();
		?>
		<div class='evo_metarow_virtual evorow evcal_evdata_row'>
			<span class='evcal_evdata_icons'><i class='fa <?php echo get_eventON_icon('evcal__fai_vir', 'fa-globe' );?>'></i></span>
			<div class='evcal_evdata_cell'>
				<h3 class='evo_h3'><?php echo evo_lang('Virtual Event Details');?></h3>
				<p id='evo_vir_debug' style='display:none'>RUN</p>
				
				<div class='evo_vir_pre_content' style='padding-bottom: 10px;'>
					<?php echo $this->get_pre_content();?>						
				</div>			
				
				<?php	do_action('evo_vir_before_main_content', $this );	?>				
				
				<div class='evo_vir_main_content evo_vir_box'>
					<?php	echo $this->get_main_content();	?>
				</div>		
				
				<div class='evo_vir_post_content'><?php echo $this->get_post_content();?>	</div>
				
				<?php

					$el_data = array(
						'key'=>'evo_vir_data',
						'refresh'=> apply_filters('evo_virtual_refreshable', false), // if heartbeat refreshing to be used
						'single'=> is_single()? 'y':'n',
						'stage'=> $this->get_current_stage(),						
						'refresh_main' => $this->_is_refresh_main_content(),
						'vir_type'=> $this->vir_type,

						'mod_joined' => $this->_get_current_mod_status(),
						'ismod'=> ( $this->_is_user_moderator ? 'y':'n'), // for moderator leave for jitsi
						'check_awaitmod' => true
					);

					$HELP = new evo_helper();

				?>
				<div class='evo_vir_data evo_refresh_on_heartbeat' <?php echo $HELP->array_to_html_data( $el_data);?>></div>

				<?php 	echo $this->get_moderator_access();	?>
				
			</div>
		</div>

		<?php return ob_get_clean();
	}


	// check whether main content need refreshed
		public function _is_refresh_main_content($old_stage = ''){
			if(!$this->vir_type == 'jitsi') return '';
			if($this->_is_user_moderator) return '';
			if( !$this->EVENT->get_prop('_mod_joined') ) return 'y';
		}
		public function _get_current_mod_status(){
			return $this->EVENT->get_prop('_mod_joined') =='in'? 'in':'left';
		}

	// moderator access section
		public function get_moderator_access(){
			$EVENT = $this->EVENT;	
			
			if(!$this->_is_user_moderator) return false;

			echo "<div class='evo_vir_mod_box' style='margin-top:10px'>";

			if( $EVENT->virtual_type() == 'jitsi'){
				if( is_single() || $this->single_override){
					
					$mtg_id = $EVENT->get_virtual_url();

					echo "<p style='padding:10px 0'>". evo_lang('You are the moderator of this event. Please sign-in to allow viewers to join to this virtual event') ."</p>";

					echo "<div class='evo-jitsi-wrapper mod' data-n='{$mtg_id}' data-p='__' data-d='". $EVENT->get_jitsi_json('mod') ."'data-width='100%' data-height='600' data-mute='false' data-videomute='". ( $EVENT->get_eprop('startWithVideoMuted')? 'true':'false') ."' data-audiomute='". ( $EVENT->get_eprop('startWithAudioMuted')? 'true':'false') ."' data-screen='false'></div>";

					echo "<div class='evo_vir_mod_left' style='display:none'>" .evo_lang('You have left the jitsi meet. Refresh the page to access jitsi meet again.') ."</div>";
				}
			}else{
				echo "<div class='evo_vir_access'>
					<p class='evo_vir_access_title'><span style='display:block'>".  evo_lang('You are the moderator of this event. Access the live stream') ."</span></p>					
					<p class='evo_vir_access_actions'><span class='evo_vir_access_actions_in'>";
					if( $EVENT->virtual_url())
						echo "<a target='_blank' href='". $EVENT->virtual_url() ."' class='evcal_btn'>". evo_lang('Join the Event Now') ."</a>";					
				
				if($virtual_pass = $EVENT->get_virtual_pass() )
					echo "<span class='evo_vir_pass'>". evo_lang('Password'). ' <b>' . $virtual_pass ."</b></span>";
				
				echo "</span></p></div>";
			}


			do_action('evo_eventcard_vir_modbox_end',$this);

			echo "</div>";
		}

	// Get stages
		public function get_current_stage(){
			$stage = 'pre';
			if($this->is_live_now) $stage = 'live';
			if($this->is_past) $stage = 'post';
			return $stage;
		}
	
	// PRE
		public function get_pre_content(){
			echo apply_filters('evo_eventcard_vir_pre_content', '' ,$this);
		}

	// check if main content can be shown
		public function can_show_main_content(){
			$show_details = false;

			$vir_show = $this->EVENT->get_prop('_vir_show');

			if(!$vir_show || $vir_show == 'always'){
				$show_details = true;
			}else{
				$time_to_event = $this->EVENT->seconds_to_start_event();
				if($time_to_event && $time_to_event <= (int)$vir_show) $show_details = true;
			}

			// if live
			if($this->is_live_now){
				if( !$this->EVENT->check_yn('_vir_hide') ) $show_details = true;
			}

			// show details for moderator of jitsi
			if( $this->vir_type == 'jitsi' && $this->_is_user_moderator) $show_details = true;

			// hide information for past events
			if($this->is_past) $show_details = false;

			return apply_filters('evo_eventcard_vir_details_bool',$show_details, $this);
		}

	// MAIN event content
	public function get_main_content($user = ''){

		ob_start();

		$show_details = $mod_joined = false;	
		$EVENT = $this->EVENT;									
				
		// Event is Live
			if($this->is_live_now){
				
				echo "<div class='evocell_virtual_livenow'>";
				echo apply_filters('evo_eventcard_virtual_livenow_html', "<span class='evo_live_now evo_live_now_tag'>" . EVO()->elements->get_icon('live') . evo_lang('Live Now') ."</span>" , 
					$EVENT);			
				echo "</div>";
			}	

		// jitsi data localization
			$EVENT->localize_edata('_evojitsi');	


		// Virtual event access details
		if( $this->can_show_main_content() ){

			ob_start();

			$virtual_pass = $EVENT->get_virtual_pass();								

			// jitsi type
			if($this->vir_type == 'jitsi'){	

				// make sure this is a single event page
				if( is_single() || $this->single_override){

					$mtg_id = $EVENT->get_virtual_url();
				
					// non moderator login
					if( !$this->_is_user_moderator ){
						
						if( $EVENT->get_prop('_mod_joined') =='in' ){
							$mod_joined = true;
							echo "<div class='evo-jitsi-wrapper' data-n='{$mtg_id}' data-p='_". $virtual_pass."_' data-d='". $EVENT->get_jitsi_json() ."' data-width='100%' data-height='600' data-mute='false' data-videomute='". ( $EVENT->get_eprop('startWithVideoMuted')? 'true':'false') ."' data-audiomute='". ( $EVENT->get_eprop('startWithAudioMuted')? 'true':'false') ."' data-screen='false'></div>";
						}else{
							
							echo "<div class='evo_vir_access evo_vir_jitsi_waitmod'><p class='evo_vir_access_title waiting_mod'><span>". evo_lang('Waiting for the moderator to join..'). "</span></p></div>";
						}
					}

					// moderator a separate will show

				// jitsi on non single event page
				}else{
					echo "<p style='padding:10px 0'><a href='". $EVENT->get_permalink()."' class='evcal_btn'>".  evo_lang('Join the live video now') ."</a></p>";
				}						

			// non jitsi
			}else{

				if( !$this->_is_user_moderator){
					echo "<div class='evo_vir_access'>
						<p class='evo_vir_access_title'><span>". evo_lang('Join the live stream') ."</span></p>					
						<p class='evo_vir_access_actions'>
						<span class='evo_vir_access_actions_in'>";
						if($EVENT->virtual_url()) echo "<a target='_blank' href='". $EVENT->virtual_url() ."' class='evcal_btn'>". evo_lang('Join the Event Now') ."</a>";					
					
					if($virtual_pass)
						echo "<span class='evo_vir_pass'>". evo_lang('Password'). ' <b>' . $virtual_pass ."</b></span>";
					
					echo "</span></p></div>";
				}
			}
						

			// EMBED CODE
				if($embed = $EVENT->get_prop('_vir_embed')){
					echo "<div class='evo_vir_embed' style='margin-top:5px;'>";
					echo $embed;
					echo "</div>";
				}

			// other event access details
				if($v_other = $EVENT->get_prop('_vir_other')){
					echo "<h4 class='evo_h4' style='margin-top:10px;'>". evo_lang('Other Access Information') ."</h4>";
					echo "<p class='evo_vir_other'>". $v_other ."</p>";
				}

			echo apply_filters('evo_eventcard_vir_main_content', ob_get_clean(), $EVENT, $this->current_user);

		// if main content is not showing
		}else{			
			
			// if the event is past
			if( $this->is_past ){
				echo "<p class='event_is_past'></p>";
			}else{
				if( $this->is_live_now){

					if( $EVENT->check_yn('_vir_hide') ){
						echo "<p>". evo_lang('Event has already started and the access to the event is closed') . "!</p>";
					}					

				// not past not live
				}else{

					if( $EVENT->is_event_starting_soon()){
						echo "<div class='evo_vir_access startingsoon'><p class='evo_vir_access_title '><span>". evo_lang('Event starting shortly..'). "</span></p></div>";
					}else{
						echo "<p>". apply_filters('evo_eventcard_vir_txt_cur', evo_lang('Event access information coming soon, Please check back again closer to event start time.'), $EVENT, $this->_is_user_moderator) . "</p>";
					}
					
				}				
			}
		}
		
		// PLUG
		do_action('evo_eventcard_vir_after_details', $this );

		return ob_get_clean();
		
	}

	// POST event content
	public function get_post_content(){

		
		if( !$this->EVENT->is_vir_event_ended()) return;	

		ob_start();											
		
		if($after_content = $this->EVENT->is_vir_after_content() ){
			echo apply_filters('evo_eventcard_virtual_after_content', $after_content, $this->EVENT);
		}else{
			echo "<p class='evo_vir_past_content'>". evo_lang('Event has already taken place') . "!</p>";
		}
		return ob_get_clean();
	}

}

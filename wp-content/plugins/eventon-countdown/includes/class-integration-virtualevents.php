<?php 
/* Virtual Events Integration */

class EVOCD_Virtual_Events{

	public function __construct(){
		add_action('evo_editevent_vir_before_after_event', array($this, 'event_edit_options'),10,1);

		add_action('evo_eventcard_vir_pre_content', array($this, 'vir_notshowing_details'), 10, 2);
	}

	// ADMIN event edit virtual event options
		public function event_edit_options($EVENT){
			?>
			<p class='yesno_row evo '>
				<?php 	
				echo EVO()->elements->get_element(
					array(
					'type'=>'yesno_btn',
					'id'=>		'_vir_hide_countd', 
					'var'=>		$EVENT->get_prop('_vir_hide_countd'),
					'input'=>	true,
					'label'=> 	__('Hide before event start countdown timer on eventcard', 'evocd'),
					'tooltip'=> __('This will hide before event go live countdown timer on eventcard.','evocd')
				));
				?>			
			</p>

			<?php
		}

	// virtual event integration
		public function vir_notshowing_details($html, $EV){


			// if event has started not show this
			if( $EV->event->is_event_started()) return $html;

			if($EV->EVENT->check_yn('_vir_hide_countd') ) return $html;

			// pass time data values to calendar
				$time_json = json_encode(array(
					'yr'=> EVOCD()->lang( 'evocd_001','Yr'),
					'o'=> EVOCD()->lang( 'evocd_002','Mo'),
					'w'=> EVOCD()->lang( 'evocd_003','Wk'),
					'd'=> EVOCD()->lang( 'evocd_004','Dy'),
					'h'=> EVOCD()->lang( 'evocd_005','Hr'),
					'm'=> EVOCD()->lang( 'evocd_006','Mn'),
					's'=> EVOCD()->lang( 'evocd_007','Sc'),
				));

			$event_start = $EV->EVENT->start_unix;
			$time_now = EVO()->calendar->get_current_time();

			$time_to = $event_start - $time_now ;

			echo "<div style='display:flex;padding:20px; margin-bottom:10px;' class='evocd_ondemand_timer_holder'>". evo_lang('Event is going live in') ." <span class='evocd_ondemand_timer' data-trig='evo_refresh_designated_elm' data-refresher='evo_vir_data' data-et='{$time_to}' data-timetx='".$time_json."'>. . . . . .</span></div>";

		}
}

new EVOCD_Virtual_Events();
<?php
/* Schedule view calendar */

class Evo_Cal_Schedule{
	public $sv_onpage = false;
	public function __construct(){
		add_action('eventon_below_sorts', array($this, 'schedule_preload'), 10, 2);
		add_filter('evo_global_data', array($this, 'global_data'), 10, 1);
		add_filter('evo_init_ajax_data', array($this, 'init_ajax_data'), 10, 2);
		add_action('evo_view_switcher_items', array($this, 'view_switcher'),10,2);
	}

	public function run($A){
		if( $A['calendar_type'] != 'schedule') return;

		$this->sv_onpage = true;
		add_filter('eventon_cal_class', array($this, 'eventon_cal_class'), 10, 1);

		$O = EVO()->calendar->_get_initial_calendar($A );

		// close out calendar
		$this->remove_sv_only_actions();

		return $O;	
		
	}
	public function remove_sv_only_actions(){
		remove_filter('eventon_cal_class', array($this, 'eventon_cal_class'));	
	}
	function eventon_cal_class($name){
		$name[]='evoSV';
		return $name;
	}
	public function schedule_preload($A, $args){
		if($args['calendar_type'] != 'schedule') return;

		?>
		<div class='evo_ajax_load_events evodv_pre_loader'>
			<span ></span>
		</div>
		<?php
	}

	// Template
	function global_data($A){
		// tell the page sv is on page to load sv specific codes
		if( $this->sv_onpage ) $A['calendars'][] = 'EVOSV';
		return $A;
	}
	function init_ajax_data($A, $global){
		if(isset($global['calendars']) && in_array('EVOSV', $global['calendars'])){
			
			ob_start();
			?><div class='evodv_CD evoADDS evodv_current_day dv_vs_{{dv_view_style}}' style='display:none'>
				<p class='evodv_dayname'>{{fixed_day_name}}</p>
				<p class='evodv_daynum'><span class='evodv_daynum_switch prev' data-dir='prev'><i class='fa fa-angle-left'></i></span><b class='evodv_current_fixed_day'>{{fixed_day}}</b><span class='evodv_daynum_switch next' data-dir='next'><i class='fa fa-angle-right'></i></span></p>
				<p class='evodv_events' style='display:none'></p>
			</div><?php
			$A['temp']['evosv_grid'] = ob_get_clean();

			// text string
			$A['txt']['until'] = evo_lang('Until' );
			$A['txt']['from'] = evo_lang('From' );
			$A['txt']['all_day'] = evo_lang_get('evcal_lang_allday','All Day');
		}
		return $A;
	}

	// Other Additions
		function view_switcher($A, $args){
			if($args['view_switcher'] == 'yes'){
				$DATA = array();

				$DATA['c'] = 'evoSV';
				$DATA['el_visibility'] = 'hide_list';

				$this->sv_onpage = true;
				$A['evosv'] = array($DATA, 'schedule', evo_lang('Schedule'));
			}

			return $A;
		}
}
<?php
/** 
 * Template functions
 * @version 
 */

class EVOAU_Templates{

	public $event_count = 5;
	public function __construct(){
		add_action('evoau_manager_print_styles', array($this,'print_styles'));
		add_action('evoau_manager_print_events', array($this,'print_events'),10,2);
		add_action('evoau_manager_before_events', array($this,'before_events'),10,2);
		add_action('evoau_manager_after_events', array($this,'after_events'),10,1);
	}

	function print_events($events, $atts){
		
		if(!$atts || empty($atts)) $atts = array();
		
		$fnc = new evoau_functions();

		if(sizeof($events) == 0) { 
			echo '<p class="manager_has_no_events">'.evo_lang('You do not have any events') . '</p>'; 
			return false;
		}

		
		$atts['page'] = 1;
		
		$epp = (isset($atts['events_per_page']))? $atts['events_per_page'] : $this->event_count;
		$atts['events_per_page'] = $epp;
		$atts['direction'] = 'none';
		$max_pages = ceil(count($events)/$epp);
		$atts['max_pages'] = $max_pages;

		echo $fnc->get_paged_events($events, $atts);
				
	}

	function before_events($events, $atts){

		$current_user = get_user_by( 'id', get_current_user_id() );
		$pag = isset($atts['pagination']) && $atts['pagination'] == 'yes' ? 'yes':'no';
		
		$epp = (isset($atts['events_per_page']))? $atts['events_per_page'] : $this->event_count;
		$max_pages = ceil(count($events)/$epp);

		?><div class='evoau_manager_event_rows' data-pag='<?php echo $pag;?>' data-page='1' data-epp='<?php echo $epp;?>' data-uid="<?php echo $current_user->ID;?>" data-events='<?php echo count($events);?>' data-pages='<?php echo $max_pages;?>'><?php
	}
	function after_events($atts){
		echo "</div>";

		if(isset($atts['pagination']) && $atts['pagination'] == 'yes'){
			?>
			<div class='evoau_manager_pagination'>
				<span class='evo_btn evoau_paginations prev'><i class='fa fa-angle-left'></i> <?php evo_lang_e('Previous Events');?></span>
				<span class='evo_btn evoau_paginations next'><?php evo_lang_e('Next Events');?> <i class='fa fa-angle-right'></i></span>
			</div>	
			<?php			
		}
	}
	function print_styles(){
		EVOAU()->manager->print_em_styles();
	}
}

new EVOAU_Templates();
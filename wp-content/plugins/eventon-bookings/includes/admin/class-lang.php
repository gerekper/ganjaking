<?php
/**
 * Event Booking Lang
 * @version 0.1
 */

class EVOBO_Lang{
	public function __construct(){
		add_action('admin_init', array($this, 'admin_init'));
	}

	function admin_init(){
		add_filter('eventon_settings_lang_tab_content', array($this, 'lang_additions'), 10, 1);
	}

	// language settings additinos
	function lang_additions($_existen){
		$new_ar = array(
			array('type'=>'togheader','name'=>'ADDON: Event Booking'),
					array('label'=>'Block Time','var'=>1),
					array('label'=>'Booking Slot Time','var'=>1),
					array('label'=>'Select a date','var'=>1),
					array('label'=>'Your Total','var'=>1),
					array('label'=>'Select an available time slot','var'=>1),
					array('label'=>'No available slots, please try another date!','var'=>1),
					array('label'=>'There are no available time slots at the moment!','var'=>1),
					array('label'=>'Your selected time','var'=>1),
					array('label'=>'Today','var'=>1),
					array('label'=>'Out of stock','var'=>1),
					array('label'=>'Can not add more! You have already added all the available spaces to your cart!','var'=>1),
					array('label'=>'Time Block successfully added to cart','var'=>1),
			array('type'=>'togend'),
		);
		return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
	}
}

new EVOBO_Lang();
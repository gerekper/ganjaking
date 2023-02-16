<?php
/**
 * All the language string additions
 */

class evovo_lang{
	function __construct(){
		add_filter('eventon_settings_lang_tab_content', array($this,'language_additions'), 13, 1);
	}
	function language_additions($_existen){
		$new_ar = array(
			array('type'=>'togheader','name'=>'ADDON: Event Ticket Options'),
				array('label'=>'Fees', 'var'=>1),				
				array('label'=>'Fee', 'var'=>1),				
				array('label'=>'Sold Out', 'var'=>1),				
				array('label'=>'Your Total', 'var'=>1),				
				array('label'=>'Optional Ticket Additions', 'var'=>1),				
				array('label'=>'Base Price', 'var'=>1),				
				array('label'=>'Variations for ticket', 'var'=>1),				
				array('label'=>'Optional Additions', 'var'=>1),				
				array('label'=>'Add', 'var'=>1),				
				array('label'=>'Added', 'var'=>1),				
				array('label'=>'remove', 'var'=>1),				
				array('label'=>'Out of Stock', 'var'=>1),				
				array('label'=>'Selected options not available for sale', 'var'=>1),				
			array('type'=>'togend'),
		);
		return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
	}
}
new evovo_lang();
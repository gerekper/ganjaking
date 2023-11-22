<?php
/**
 * eventON Functions
 *
 * Hooked-in functions for eventON related events on the front-end.
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	eventON/Functions
 * @version     2.9.1
 */ 
 
// PHP tag driven event calendar 
if( !function_exists ('ajde_evcal_calendar')){
	function ajde_evcal_calendar($args=''){	
		$content =EVO()->evo_generator->_get_initial_calendar( $args );
		echo $content;
	} 
}

function add_eventon($args=''){	
	$content = EVO()->evo_generator->_get_initial_calendar( $args );	
	echo $content;
} 
?>
<?php
/**
 *	EventON Template Control hooks and functions
 *	@version 4.1.2
 */

class EVO_Temp_Control{

	public function __construct(){
		add_filter( 'init', array( $this, 'register_block_patterns' ) );
	}

	function register_block_patterns(){
		

		
	}

	function render_frontend(){
		return 'test';
	}
}

new EVO_Temp_Control();